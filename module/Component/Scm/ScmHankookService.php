<?php
namespace Component\Scm;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Component\Goods\SmsStock;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Encryptor;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use Framework\Utility\KafkaUtils;
use SlComponent\Util\SlSmsUtil;

/**
 * 한국타이어 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmHankookService {

    /**
     * @var array 주문 상태 출력에서 제외할 주문 코드들
     */
    public $statusExcludeCd = [];
    /**
     * @var array 사용자 클레임 승인 코드 (승인/대기/거부)
     */
    public $statusClaimHandleCode = [];
    /**
     * @var array 사용자 클레임 신청 코드 (환불/반품/교환)
     */
    public $statusUserClaimRequestCode = [];
    /**
     * @var array 클래임 상태에서 변경 가능 상태 기준표 (주문상태내 클래임접수)
     */
    public $statusClaimCode = [];
    /**
     * @var array 영수증 발급 가능한 주문 코드들
     */
    public $statusReceiptApprovalPossible = [];


    public function runMethod($methodName, $param){
        $memberService = SlLoader::cLoad('godo','memberService','sl');
        $isHankookManager = $memberService->isHankookManager(\Session::get('member.memId'));
        $isHyundaeManager = $memberService->isHyundaeManager(\Session::get('member.memId'));
        if( $isHankookManager ) {
            return self::$methodName($param);
        }else if( $isHyundaeManager ){
            return $this->setHyundaeOrderControlData($param);
        }else{
            return null;
        }
    }

    public function setOrderControlData($param){
        $param['controller']->getView()->setPageName('order/order_hankook');
        $param['controller']->setData('hankookType' , SlCodeMap::HANKOOK_TYPE);
        return true;
    }

    public function setHyundaeOrderControlData($param){
        $param['controller']->getView()->setPageName('order/order_hyundae');
        //$param['controller']->setData('hankookType' , SlCodeMap::HANKOOK_TYPE);
        return true;
    }

    /**
     * 한국타이어 마스터 아이디 전용 리스트.
     * @param int $pageNum
     * @param null $dates
     * @param null $statusMode
     * @return mixed
     */
    public function getHankookMasterOrderList($pageNum = 10, $dates = null, $statusMode = null){

        $order = SlLoader::cLoad('order','order');
        $db = \App::getInstance('DB');

        // 배열 선언
        $arrBind = $arrWhere = [];

        // 상품혜택관리 치환코드 생성
        if(!is_object($goodsBenefit)){
            $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
        }

        // 회원 or 비회원 패스워드 체크 ( 무조건 added join )
        if (MemberUtil::checkLogin() == 'member') {
            $arrJoin[] = ' JOIN sl_orderAddedData so on o.orderNo = so.orderNo ';
        } else {
            throw new AlertRedirectException(__('로그인 정보가 존재하지 않습니다.'), null, null, '../member/login.php');
        }

        // 기간 설정
        if (null !== $dates && is_array($dates) && $dates[0] != '' && $dates[1] != '') {
            $arrWhere[] = 'o.regDt BETWEEN ? AND ?'; // 한달 이내쿼리로 조작';
            $db->bind_param_push($arrBind, 's', $dates[0] . ' 00:00:00');
            $db->bind_param_push($arrBind, 's', $dates[1] . ' 23:59:59');
        }
        else {  //빈값으로 넘어오면 1년범위 검색
            $dates[0] = date('Y-m-d', strtotime('-365 days'));
            $dates[1] = date('Y-m-d');
            $arrWhere[] = 'o.regDt BETWEEN ? AND ?'; // 한달 이내쿼리로 조작';
            $db->bind_param_push($arrBind, 's', $dates[0] . ' 00:00:00');
            $db->bind_param_push($arrBind, 's', $dates[1] . ' 23:59:59');
        }

        // 주문 테이블 필드
        $arrInclude = [
            'orderNo',
            'orderChannelFl',
            'settlePrice',
            'settleKind',
            'orderGoodsCnt',
            'orderTypeFl',
            'orderGoodsCnt',
            'multiShippingFl'
        ];
        if (Globals::get('gGlobal.isFront')) {
            array_push($arrInclude,'currencyPolicy','exchangeRatePolicy');
        }

        $tmpField[] = DBTableField::setTableField('tableOrder', $arrInclude, null, 'o');

        // 조인
        $arrJoin[] = ' LEFT JOIN ' . DB_ORDER_GOODS . ' og ON o.orderNo = og.orderNo ';

        // 결제실패를 제외하고 전부 출력
        $arrWhere[] = 'og.orderStatus != ?';
        $db->bind_param_push($arrBind, 's', 'f1');

        // 주문 or 취소 리스트 조건
        switch ($statusMode) {
            // 주문관련 리스트만
            case 'order':
                $tmpField[] = DBTableField::setTableField('tableOrderUserHandle', ['userHandleFl'], null, 'ouh');
                $arrJoin[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON og.userHandleSno = ouh.sno AND og.orderNo = ouh.orderNo ';
                $arrWhere[] = 'og.handleSno<=0 AND LEFT(og.orderStatus, 1) NOT IN (\'' . implode('\',\'', $this->statusExcludeCd) . '\')';
                break;

            // 기본 프로퍼티에서 반품(r) 제거
            case 'cancel':
                $statusExcludeCd = [];
                foreach ($this->statusExcludeCd as $key => $val) {
                    if ($val != 'r') {
                        $statusExcludeCd[$key] = $val;
                    }
                }
                $tmpField[] = DBTableField::setTableField('tableOrderGoods', ['handleSno'], null, 'og');
                $arrWhere[] = '((og.handleSno > 0) OR (LEFT(og.orderStatus, 1) IN (\'' . implode('\',\'', $statusExcludeCd) . '\')))';
                break;

            // 교환, 반품 신청 및 거절 상태
            case 'cancelRequest':
                // 사용자 클레임 승인 코드에서 승인(y) 제거
                $statusClaimHandleCode = [];
                foreach ($this->statusClaimHandleCode as $key => $val) {
                    if ($val != 'y') {
                        $statusClaimHandleCode[$key] = $val;
                    }
                }

                // 사용자 클레임 신청 코드에서 환불(r) 제거
                $statusUserClaimRequestCode = [];
                foreach ($this->statusUserClaimRequestCode as $key => $val) {
                    if ($val != 'r') {
                        $statusUserClaimRequestCode[$key] = $val;
                    }
                }

                $tmpField[] = DBTableField::setTableField('tableOrderUserHandle', ['userHandleFl'], null, 'ouh');
                $arrJoin[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON og.orderNo = ouh.orderNo AND (og.userHandleSno = ouh.sno || (og.sno = ouh.userHandleGoodsNo && left(og.orderStatus, 1) NOT IN (\'' . implode('\',\'', $statusUserClaimRequestCode) . '\')))';
                $arrWhere[] = 'ouh.userHandleFl IN (\'' . implode('\',\'', $statusClaimHandleCode) . '\') AND ouh.userHandleMode IN (\'' . implode('\',\'', $statusUserClaimRequestCode) . '\') AND LEFT(og.orderStatus, 1) IN (\'' . implode('\',\'', $this->statusClaimCode['b']) . '\')';
                break;

            // 반품만
            case 'refund':
                $arrJoin[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON og.userHandleSno = ouh.sno AND og.orderNo = ouh.orderNo AND ouh.userHandleFl = \'y\'';
                $arrWhere[] = 'LEFT(og.orderStatus, 1) IN (\'r\')';
                break;

            // 환불 신청 및 거절 상태
            case 'refundRequest':
                // 사용자 클레임 승인 코드에서 승인(y) 제거
                $statusClaimHandleCode = [];
                foreach ($this->statusClaimHandleCode as $key => $val) {
                    if ($val != 'y') {
                        $statusClaimHandleCode[$key] = $val;
                    }
                }

                // 사용자 클레임 신청 코드에서 반품, 교환(b, e) 제거
                $statusUserClaimRequestCode = [];
                foreach ($this->statusUserClaimRequestCode as $key => $val) {
                    if ($val != 'b' && $val != 'e') {
                        $statusUserClaimRequestCode[$key] = $val;
                    }
                }

                $tmpField[] = DBTableField::setTableField('tableOrderUserHandle', ['userHandleFl'], null, 'ouh');
                $arrJoin[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON og.orderNo = ouh.orderNo AND (og.userHandleSno = ouh.sno || (og.sno = ouh.userHandleGoodsNo && left(og.orderStatus, 1) NOT IN (\'' . implode('\',\'', $statusUserClaimRequestCode) . '\')))';
                $arrWhere[] = 'ouh.userHandleFl IN (\'' . implode('\',\'', $statusClaimHandleCode) . '\') AND ouh.userHandleMode IN (\'' . implode('\',\'', $statusUserClaimRequestCode) . '\') AND LEFT(og.orderStatus, 1) IN (\'' . implode('\',\'', $this->statusReceiptApprovalPossible) . '\')';
                break;

            // 모바일 프론트에서 사용
            case 'mobile':
                break;
        }

        // 필드 정리
        $tmpKey = array_keys($tmpField);
        $arrField = [];
        foreach ($tmpKey as $key) {
            $arrField = array_merge($arrField, $tmpField[$key]);
        }
        unset($tmpField, $tmpKey);

        // 페이지 기본설정
        $pageNo = Request::get()->get('page', 1);
        $page = \App::load('\\Component\\Page\\Page', $pageNo);
        $page->page['list'] = $pageNum; // 페이지당 리스트 수
        $page->block['cnt'] = 5;
        $page->setPage();
        $page->setUrl(Request::getQueryString());

        // 현 페이지 결과
        $db->strJoin = implode('', $arrJoin);
        $db->strField = implode(', ', $arrField) . ', o.regDt';
        $db->strWhere = implode(' AND ', gd_isset($arrWhere));
        $db->strOrder = 'og.orderNo desc';
        $db->strLimit = $page->recode['start'] . ',' . $pageNum;
        $db->strGroup = 'og.orderNo';

        if (empty($arrBind)) {
            $arrBind = null;
        }
        $query = $db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER . ' o ' . implode(' ', $query);
        $data = $db->query_fetch($strSQL, $arrBind);

        // 현 페이지 검색 결과
        unset($query['group'], $query['order'], $query['limit']);
        $strCntSQL = 'SELECT COUNT(1) as cnt, SUM(settlePrice) as totalSettlePrice FROM (' . 'SELECT o.orderNo, o.settlePrice FROM ' . DB_ORDER . ' AS o ' . implode(' ', $query) . ' group by o.orderNo, o.settlePrice ) a';

        $fetchData = $db->query_fetch($strCntSQL, $arrBind, false);
        $total = $fetchData['cnt'];
        $totalSettlePrice = $fetchData['totalSettlePrice'];

        // 검색 레코드 수
        $page->recode['total'] = $total;
        $page->recode['totalSettlePrice'] = $totalSettlePrice;
        $page->setPage();

        // 결제방법 과 처리 상태 설정
        if (gd_isset($data)) {
            foreach ($data as $key => $val) {
                $useMultiShippingKey = false;
                if (\Component\Order\OrderMultiShipping::isUseMultiShipping() == true) {
                    $useMultiShippingKey = true;
                }

                $val['goods'] = $order->getOrderGoodsData($val['orderNo'], null, null, null, null, false, false, null, null, false, $useMultiShippingKey);
                $val['orderInfo'] = $order->getOrderInfo($val['orderNo'], false);
                $val['orderGoodsCnt'] = gd_isset(count($val['goods']), 0);
                $val['settleName'] = $order->getSettleKind($val['settleKind']);

                // 멀티상점 환율 기본 정보
                if (Globals::get('gGlobal.isFront')) {
                    $val['currencyPolicy'] = json_decode($val['currencyPolicy'], true);
                    $val['exchangeRatePolicy'] = json_decode($val['exchangeRatePolicy'], true);
                    $val['currencyIsoCode'] = $val['currencyPolicy']['isoCode'];
                    $val['exchangeRate'] = $val['exchangeRatePolicy']['exchangeRate' . $val['currencyPolicy']['isoCode']];
                }

                // 주문상품 loop
                if (isset($val['goods']) && empty($val['goods']) === false) {
                    foreach ($val['goods'] as $aKey => $aVal) {
                        $addGoodsCnt = gd_isset($aVal['addGoodsCnt'], 0);
                        //상품혜택 사용시 해당 변수 재설정
                        $val['goods'][$aKey] = $goodsBenefit->goodsDataFrontReplaceCode($aVal, 'mypage');
                        // 리스트에서 각 모드별로 불필요한 row를 제거
                        switch ($statusMode) {
                            case 'order':
                                if (in_array(substr($aVal['orderStatus'], 0, 1), $this->statusExcludeCd)) {
                                    $val['orderGoodsCnt'] -= 1;
                                    unset($val['goods'][$aKey]);
                                } else {
                                    $val['orderAddGoodsCnt'] += $addGoodsCnt;
                                }
                                break;

                            case 'cancel':
                                $includeOrderGoods = false;
                                if (in_array(substr($aVal['orderStatus'], 0, 1), $statusExcludeCd)) {
                                    $includeOrderGoods = true;
                                }
                                if ((int)$aVal['handleSno'] > 0) {
                                    $includeOrderGoods = true;
                                }

                                if($includeOrderGoods === true){
                                    $val['orderAddGoodsCnt'] += $addGoodsCnt;
                                    $val['goods'][$aKey]['orderInfoRow'] = 1;
                                }
                                else {
                                    $val['orderGoodsCnt'] -= 1;
                                    unset($val['goods'][$aKey]);
                                }
                                break;

                            case 'cancelRequest':
                                if ($aVal['userHandleSno'] <= 0 || in_array(substr($aVal['orderStatus'], 0, 1), $this->statusClaimCode['r']) || in_array(substr($aVal['orderStatus'], 0 , 1), $statusUserClaimRequestCode)) {
                                    $val['orderGoodsCnt'] -= 1;
                                    unset($val['goods'][$aKey]);
                                } else {
                                    $val['orderAddGoodsCnt'] += $addGoodsCnt;
                                    $val['goods'][$aKey]['orderInfoRow'] = 1;
                                }
                                break;

                            case 'refund':
                                if (!in_array(substr($aVal['orderStatus'], 0, 1), ['r'])) {
                                    $val['orderGoodsCnt'] -= 1;
                                    unset($val['goods'][$aKey]);
                                } else {
                                    $val['orderAddGoodsCnt'] += $addGoodsCnt;
                                    $val['goods'][$aKey]['orderInfoRow'] = 1;
                                }
                                break;

                            case 'refundRequest':
                                if ($aVal['userHandleSno'] <= 0 || in_array(substr($aVal['orderStatus'], 0, 1), ['r'])) {
                                    $val['orderGoodsCnt'] -= 1;
                                    unset($val['goods'][$aKey]);
                                } else {
                                    $val['orderAddGoodsCnt'] += $addGoodsCnt;
                                    $val['goods'][$aKey]['orderInfoRow'] = 1;
                                }
                                break;
                        }
                    }
                }

                // 데이터 재가공
                $data[$key] = $val;
            }
        }

        // 배열 인덱스 정리
        foreach ($data as $key => $val) {
            $data[$key]['goods'] = array_values($data[$key]['goods']);
        }

        //gd_debug( $data );

        return gd_htmlspecialchars_stripslashes($data);
    }


    /**
     * 한국타이어 마스터 아이디 전용 주문 뷰
     * @param $orderNo
     * @return mixed
     * @throws Exception
     */    
    public function getHankookMasterOrderDataInfo($orderNo)
    {
        $order = SlLoader::cLoad('order','order');

        // 주문 기본 정보
        $arrExclude = [
            'orderIp',
            'orderPGLog',
            'orderDeliveryLog',
            'orderAdminLog',
        ];

        // getOrderData에서 arrWhere와 arrBind를 사용하니 주의해서 확인 필요
        $getData = $order->getOrderData($orderNo, $arrExclude);

        // 주문 정보가 없는 경우
        if (empty($getData)) {
            throw new Exception(__('주문정보가 없습니다.'));
        }
        // 회원 or 비회원 패스워드 체크
        $isNotAccess = false;
        switch (MemberUtil::checkLogin()) {
            // 로그인을 한 경우
            case 'member':
                /*if ($getData['memNo'] !== Session::get('member.memNo')) {
                    $isNotAccess = true;
                }*/
                break;

            // 로그인을 하지 않은 경우
            case 'guest':
                // 비회원으로 들어왔을때 네이버 페이인경우에는 검색된 주문번호로 게스트세션 주문번호를 재셋팅처리
                if ($getData['orderChannelFl'] == 'naverpay') {
                    //Session::set('guest.orderNo', $getData['orderNo']);
                }

                // 비회원 패스워드 체크
                if (!Session::has('guest.orderNo') || !Session::has('guest.orderNm')) {
                    // 비회원 로그아웃
                    // MemberUtil::logoutGuest();

                    // 로그인 페이지로 이동
                    header('location:' . URI_HOME . 'member/login.php?returnUrl=' . urlencode(Request::getReferer()));
                    exit();
                }

                // 탈퇴여부 확인 후 비회원이 맞는지 체크
                if ($order->isHackOut($orderNo) === false) {
                    if (intval($getData['memNo']) !== 0) {
                        $isNotAccess = true;
                    }
                }

                // 세션에 저장된 주문자 이름 비교
                if ($getData['orderName'] !== Session::get('guest.orderNm')) {
                    $isNotAccess = true;
                }

                // 세션에 저장된 주문번호 비교
                if ($getData['orderNo'] !== Session::get('guest.orderNo')) {
                    $isNotAccess = true;
                }
                break;

            // 어떤 회원관련 정보도 없는 경우
            default:
                throw new Exception(__('회원정보가 존재하지 않습니다.'));
                break;
        }
        // 접근권한이 없는 경우
        if ($isNotAccess !== false) {
            throw new Exception(__('접근 권한이 없습니다.'));
        }

        // 주문 추가 필드 정보
        $getData['addField'] = $order->getOrderAddFieldView($getData['addField']);

        // 남기실 내용
        $getData['orderMemo'] = nl2br($getData['orderMemo']);

        // 무통장 입금 은행 정보
        $getData['bankAccount'] = explode(STR_DIVISION, $getData['bankAccount']);

        // PG 결과 처리
        $getData['pgSettleNm'] = explode(STR_DIVISION, $getData['pgSettleNm']);
        $getData['pgSettleCd'] = explode(STR_DIVISION, $getData['pgSettleCd']);

        // 주문 상태 처리
        $getData['orderStatus'] = substr($getData['orderStatus'], 0, 1);

        // 결제 방법
        $getData['settleName'] = $order->getSettleKind($getData['settleKind']);
        $getData['settleGateway'] = substr($getData['settleKind'], 0, 1);
        $getData['settleMethod'] = substr($getData['settleKind'], 1, 1);

        // 에스크로여부
        if ($getData['settleGateway'] === 'e') {
            $getData['settleName'] = __('에스크로 ') . $getData['settleName'];
        }

        // 멀티상점 환율 기본 정보
        $getData['currencyPolicy'] = json_decode($getData['currencyPolicy'], true);
        $getData['exchangeRatePolicy'] = json_decode($getData['exchangeRatePolicy'], true);
        $getData['currencyIsoCode'] = $getData['currencyPolicy']['isoCode'];
        $getData['exchangeRate'] = $getData['exchangeRatePolicy']['exchangeRate' . $getData['currencyPolicy']['isoCode']];

        // 영수증 출력 정보 세팅 (PG 거래 영수증 - 현금영수증 제외)
        if ($getData['settleMethod'] == 'c') {
            $getData['settleReceipt'] = 'card';
        } elseif ($getData['settleMethod'] == 'b') {
            $getData['settleReceipt'] = 'bank';
        } elseif ($getData['settleMethod'] == 'v') {
            $getData['settleReceipt'] = 'vbank';
        } elseif ($getData['settleMethod'] == 'h') {
            $getData['settleReceipt'] = 'hphone';
        } else {
            $getData['settleReceipt'] = '';
        }
        $pgCodeConfig = App::getConfig('payment.pg');
        if (empty($getData['settleReceipt']) === false && isset($pgCodeConfig->getPgReceiptUrl()[$getData['pgName']][$getData['settleReceipt']]) === false) {
            $getData['settleReceipt'] = '';
        }

        $getData['absTotalEnuriDcPrice'] = abs($getData['totalEnuriDcPrice']);

        $useMultiShippingKey = false;
        if ($getData['multiShippingFl'] == 'y') {
            $useMultiShippingKey = true;
        }

        // 주문 상품 정보
        $getData['goods'] = $order->getOrderGoodsData($orderNo, null, null, null, 'user', false, false, null, null, false, $useMultiShippingKey);

        return $getData;
    }

    /**
     * 주문번호와 회원 정보로 추가 정보 셋팅
     * @param $orderNo
     * @param $memNo
     * @return mixed
     */
    public function setOrderAddedData($orderNo, $memNo){
        $addedData = DBUtil2::getOne('sl_orderAddedData','orderNo', $orderNo);
        $memberInfo = DBUtil2::getOne(DB_MEMBER, 'memNo', $memNo);
        $addedData['orderMemId'] = $memberInfo['memId'];
        $addedData['orderMemNm'] = $memberInfo['memNm'];
        return $addedData;
    }

    /**
     * 한국타이어 마스터 아이디 주문
     * @param $orderNo
     * @throws Exception
     */
    public function setHankookMasterOrder($orderNo){
        $postValue = Request::post()->toArray();
        $orderData = DBUtil2::getOne(DB_ORDER,'orderNo',$orderNo);
        $settlePrice = $orderData['totalGoodsPrice'];
        //SitelabLogger::logger('한국타이어 마스터 주문');
        //SitelabLogger::logger($postValue);
        //오픈패키지일 경우 아이디 생성

        if( 0 == $postValue['memberType'] ){
            //회원 생성
            $param['agreementInfoFl'] = 'y';
            $param['privateApprovalFl'] = 'y';

            //ID 생성-------------------------------------------------------
            $createId = 'hk'.substr($postValue['receiverCellPhone'],-4);
            //중복ID체크
            $idSearchVo = new SearchVo('memId=?', $createId);
            $member = DBUtil2::getOneBySearchVo(DB_MEMBER, $idSearchVo);
            if( !empty($member) ){
                $count = DBUtil2::getCount(DB_MEMBER, new SearchVo("memId LIKE concat(?,'%')", $createId));
                $createId .= $count;
            }
            $param['memId'] = $createId;
            //ID 생성 끝 ---------------------------------------------------

            $param['memPw'] = 'hankook'.substr($postValue['receiverCellPhone'],-4);
            $param['cellPhone'] = $postValue['receiverCellPhone'];
            $param['memNm'] = $postValue['receiverName'];
            $param['ex1'] = '한국타이어';
            $param['hankookType'] = $postValue['hankookType'];
            $memberService = SlLoader::cLoad('member','member');
            $rt = $memberService->join($param);
            $memNo = $rt->getMemNo();

            $contentParam['hkUrl'] = 'http://www.hankookb2b.co.kr';
            $contentParam['orderName'] = $postValue['receiverName'];
            $contentParam['memId'] = $param['memId'];
            $contentParam['btnUrl'] = "http://m.hankookb2b.co.kr/mypage/order_list.php?orderType=all";
            SlKakaoUtil::send(13 , $param['cellPhone'] ,  $contentParam);
            /*$content = SlSmsUtil::getSmsMsg(2,$contentParam);
            $memberList[] = [
                'memNo' => 0,
                'memName' => $postValue['receiverName'],
                'smsFl' => 'y',
                'cellPhone' => $param['cellPhone'],
            ];
            SlSmsUtil::sendSms($content, $memberList, 'lms');*/

        }else{
            $memNo = $postValue['recontactMemNo']; //재계약 회원
        }
        //주문을 변경한다. (결제 완료 처리 및 생성 또는 재계약 회원으로 변경)
        $updateData['orderStatus'] = 'p1';
        $updateData['paymentDt'] = 'now()';
        DBUtil2::update(DB_ORDER_GOODS, $updateData, new SearchVo('orderNo=?', $orderNo));
        $updateData['memNo'] = $memNo;
        DBUtil2::update(DB_ORDER, $updateData, new SearchVo('orderNo=?', $orderNo));

        //예치금 적립
        $giftAmount = preg_replace("/[^0-9]*/s", "", $postValue['giftAmount']);

        $depositAmount = $giftAmount - $settlePrice;

        if( $depositAmount > 0 ){
            $depositService = SlLoader::cLoad('deposit','deposit');
            $rslt = $depositService->setMemberDeposit($memNo, $depositAmount, Deposit::REASON_CODE_GROUP . Deposit::REASON_CODE_ETC, 'o', $orderNo,  null, '본사 선물금 잔액 예치금 적립 ('. $orderNo .')');
            if(empty($rslt)){
                SitelabLogger::error('예치금 적립 오류');
                SitelabLogger::error($postValue);
            }
        }else{
            $depositAmount = 0;
        }

        //마스터 주문 기록.
        $saveOrderAddedData['memNo'] = $memNo;
        $saveOrderAddedData['orderNo'] = $orderNo;
        $saveOrderAddedData['giftAmount'] = $giftAmount;
        $saveOrderAddedData['settlePrice'] = $settlePrice;
        $saveOrderAddedData['addDeposit'] = $depositAmount;
        $saveOrderAddedData['reqDeliveryDt'] = $postValue['reqDeliveryDt'];
        $saveOrderAddedData['memberType'] = $postValue['memberType'];
        $saveOrderAddedData['storeType'] = $postValue['hankookType'];
        DBUtil2::insert('sl_orderAddedData', $saveOrderAddedData);
    }

    /**
     * 한국타이어 마스터 주문 ( 정책 변경으로 배송 예정일만 저장 )
     * @param $orderNo
     */
    public function setHankookMasterOrderV2($orderNo){
        $postValue = Request::post()->toArray();
        $memNo = \Session::get('member')['memNo'];
        $orderData = DBUtil2::getOne(DB_ORDER,'orderNo',$orderNo);
        //마스터 주문 기록.
        $saveOrderAddedData['memNo'] = $memNo;
        $saveOrderAddedData['orderNo'] = $orderNo;
        $saveOrderAddedData['settlePrice'] = $orderData['totalGoodsPrice'];
        $saveOrderAddedData['reqDeliveryDt'] = $postValue['reqDeliveryDt'];
        DBUtil2::insert('sl_orderAddedData', $saveOrderAddedData);
    }

    /**
     * 리서치 알림톡 발송
     * @param $receiverPhoneNumber
     * @param $memName
     * @param $orderNo
     * @return bool
     */
    public function sendHkResearch($receiverPhoneNumber, $memName, $orderNo){
        //SMS로 보내면 False
        $isContinue = true;

        //춘추
        $goodsSpringList = [
            1000000233,
            1000000232,
            1000000231,
            1000000229,
        ];
        //동계
        $goodsWinterList = [
            1000000288,
            1000000287,
            1000000286,
            1000000285,
            1000000284,
            1000000283,
            1000000282,
            1000000281,
        ];

        $searchVoSpring = new SearchVo("orderNo=?",$orderNo);
        $searchVoSpring->setWhere(DBUtil::bind('goodsNo', DBUtil::IN, count($goodsSpringList) ));
        $searchVoSpring->setWhereValueArray( $goodsSpringList  );

        $searchVoWinter = new SearchVo("orderNo=?",$orderNo);
        $searchVoWinter->setWhere(DBUtil::bind('goodsNo', DBUtil::IN, count($goodsWinterList) ));
        $searchVoWinter->setWhereValueArray( $goodsWinterList  );

        $springGoodsCount = DBUtil2::getCount(DB_ORDER_GOODS, $searchVoSpring);
        $winterGoodsCount = DBUtil2::getCount(DB_ORDER_GOODS, $searchVoWinter);

        $receiverData[0]['memNo'] = '0';
        $receiverData[0]['smsFl'] = 'y';
        $receiverData[0]['cellPhone'] = $receiverPhoneNumber;
        $receiverData[0]['memNm'] = $memName;

        //춘계 설문 보내기.
        if( $springGoodsCount > 0 ){
            $title = '22년 춘추 리오더상품 만족도조사';
            $surveyUrl = 'https://forms.gle/4NRacCAY3xLHuirL9'; //춘추
            $this->sendToSurveyContents($title, $memName, $surveyUrl);
            $isContinue = false;
        }
        //동계 설문 보내기.
        if( $winterGoodsCount > 0 ){
            $title = '22년 동계 리오더상품 만족도조사';
            $surveyUrl = 'https://forms.gle/Y5tQzbyMEw4fgnmi8'; //동계
            $this->sendToSurveyContents($title, $memName, $surveyUrl);
            $isContinue = false;
        }

        return $isContinue;

    }

    /**
     * 특정 상품 특정 리서치 URL SMS 전달.
     * @param $title
     * @param $memName
     * @param $surveyUrl
     */
    public function sendToSurveyContents($title, $memName, $surveyUrl){
        $content = "
{$title}
안녕하세요 [{$memName}] 님
★★ 경품 받으러 가기★★

간단한 설문조사하고
우리 매장 회식비 지원 받기!!

경품 받기 : {$surveyUrl}

여러분의 목소리를 들려주세요!
더욱 만족스러운 서비스를 위해 '구매 만족도 평가'를 실시하고 있습니다.
향후 서비스 개선을 위해 활용될 예정이니 많은 참여 부탁드립니다 :)

▷ [한국타이어B2B] 바로가기
[hankookb2b.co.kr]
고객센터
(070-4239-4380)";

        $result = SlSmsUtil::sendSms($content, $receiverData, 'lms', 'send', date('Y-m-d').' '.'18:30:00');
        SitelabLogger::logger($content);
        SitelabLogger::logger($result);
    }

}

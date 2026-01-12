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
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\PhpExcelUtil;
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
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;


/**
 * 한전 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmKepidService {

    const NAME_PLATE_GOODS_LIST = [
        1000000339,
        1000000380,
    ];


    /**
     * @var \Framework\Database\DBTool null|object 데이터베이스 인스턴스(싱글턴)
     */
    protected $db;
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

    const TEE = 0;
    const PANTS = 1;

    public function __construct(){
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }

    public static function getPreOrderGoods($type = null){
        if( SlCommonUtil::isDev() ){
            $goods = [
                1000002052, //티 (파트너)
                1000002051, //바지 (파트너)
            ];
        }else{
            $goods = [
                //1000000334, //춘추점퍼
                //1000000335, //경동 동계점퍼
                //한타시작
                /*1000000283
                ,	1000000284
                ,	1000000285,*/
                //한타종료

                //미쓰비시 설치 동계
                //1000000346, //하의
                //1000000345, //상의

                //오티스
                //1000000215, //춘추
                
                //1000000343, //동계바지(파트너)
                //1000000342, //동계점퍼(파트너)
                //1000000341, //동계바지(정직원)
                //1000000340, //동계점퍼(정직원)

                //1000000339, //한전
                //1000000338, //한전
                //1000000337, //한전
            ];
        }
        if( isset($type) ){
            return $goods[$type];
        }else{
            return $goods;
        }

        /*$goods = [
            1000000329, //티 (파트너)
            1000000331, //바지 (파트너)
            1000000328, //티
            1000000330, //바지
        ];*/

    }

    public function setTkeOrder($orderNo){

    }

    public function setTkePreOrderStatus(){
        // PreOrder일 경우 상태변경 , 출고전 주기적 실행.
        //1. 주문 상품에 PreOrder상품이 있을 경우 상태 변경 p3 or p2)
        $current = date('Ymd');
        $standard = '20230820';
        if( $standard > $current ){
            $changeOrderStatus = 'p3';
        }else{
            $changeOrderStatus = 'p2'; //생산처 직접 출고 이후건.
        }

        //결제완료 주문 중 PreOrder상품이 있다면 상태 변경.
        $orderGoodsList = DBUtil2::getList(DB_ORDER_GOODS, new SearchVo('(scmNo=8 OR scmNo=17) AND orderStatus=?', 'p1'));
        $orderNo = '';
        foreach($orderGoodsList as $orderGoodsData){
            //Preorder상품이라면.
            if( in_array($orderGoodsData['goodsNo'] , ScmTkeService::getPreOrderGoods()) ){
                $orderNo = $orderGoodsData['orderNo'];
                //SitelabLogger::logger($orderGoodsData['sno']);
                DBUtil2::update(DB_ORDER_GOODS, ["orderStatus = '{$changeOrderStatus}' "], new SearchVo('sno=?', $orderGoodsData['sno']));
            }
        }

        if(!empty($orderNo)){
            $searchVo = new SearchVo('orderNo=?', $orderNo);
            DBUtil2::update(DB_ORDER, ["orderStatus = '{$changeOrderStatus}' "], $searchVo);
        }
        //TODO TEST 주문 이후 테스트.
    }


    /**
     * TKE 회원 추가
     * @param $files
     * @throws \Exception
     */
    public function addMemberTke($files){

        $files = \Request::files()->toArray();
        $params['instance'] = $this;

        $params['fnc'] = 'saveMemberTkeRaw';
        DBUtil2::runSql("TRUNCATE TABLE sl_tkeMember"); //Raw넣을때만.
        //$params['fnc'] = 'saveMemberTke';

        $params['mixData'] = [
            'excelField' => [
                'memId'=> 1,
                'memNm'=> 2,
                'nickNm'=> 3,
                'cellPhone'=> 4,
                'zipcode'=> 5,
                'address'=> 6,
                'email'=> 7,
                'groupName'=> 8,
                'teamName'=> 9,
                'teamRep'=> 10,
                'buyLimitCount'=> 11,
            ]
        ];
        PhpExcelUtil::runExcelReadAndProcess($files, $params, 1);
    }

    public function saveMemberTke1($each, $key, &$mixData){
        SitelabLogger::logger($key);
        SitelabLogger::logger($each);
    }

    /**
     * TKE 회원 추가 (개별저장)
     * @param $each
     * @param $key
     * @param $mixData
     * @return bool
     * @throws \Exception
     */
    public function saveMemberTkeRaw($each, $key, &$mixData){
        $paramData = PhpExcelUtil::getExcelDataList($each, $mixData['excelField']);
        DBUtil2::insert('sl_tkeMember', $paramData);
    }

    public function saveMemberTke(){
        $list = DBUtil2::getList('sl_tkeMember','1','1');
        SlCommonUtil::setEachData($list, $this, 'saveMemberTkeEach');

        $sql = "update es_member set regDt = now() where ex1 = 'TKE(티센크루프)' ";
        DBUtil2::runSql($sql);
        //gd_debug('Complete....');
    }

    public function saveMemberTkeEach($paramData, $key, &$mixData){

        $beforeMemberTempTable = "stmp_member_230913";

        $groupInfo = [
            'NI' => 5,
            'SVC' => 6,
            'MFG' => 7,
        ];

        if(empty($paramData['memId'])) {
            gd_debug($key.'아이디 없음.');
            return false;
        }

        $saveData = SlCommonUtil::getAvailData($paramData,[
            'memId',
            'memNm',
            'nickNm',
            'cellPhone',
            'zipcode',
            'address',
            'email',
        ]);
        $saveData['zonecode'] = $paramData['zipcode'];

        //그룹명
        $saveData['groupSno'] = 1;
        //a + 사번 or a + 휴대폰(no dash)
        //정직원 그룹
        $tkeMemberGroup = [
            '정직원',
            '파견직',
            '컨설턴트',
        ];

        if( in_array($paramData['groupName'], $tkeMemberGroup) ){
            if( '컨설턴트' === $paramData['groupName'] ){
                $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['cellPhone']); //파트너
            }else{
                $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['memId']); //정직원
            }
            $memberType = 1;
        }else{
            $saveData['groupSno'] = $groupInfo[strtoupper($paramData['groupName'])];
            $pwdStr = 'a' . preg_replace("/[^0-9]*/s", "", $paramData['cellPhone']); //파트너
            $memberType = 2;
        }

        //$saveData['memPw'] = Digester::digest($pwdStr);
        $saveData['memPw'] = $paramData['memPw'];

        //SitelabLogger::logger($paramData['memId'] . ' :: ' . $paramData['groupName'] . ' : '. $pwdStr);
        //SitelabLogger::logger($paramData['memId'] . ' :: ' . $paramData['groupName'] . ' : '. $saveData['memPw']);
        $saveData['adminMemo'] = '시스템일괄등록 : ' . date('Y-m-d');

        $saveData['ex1'] = 'TKE(티센크루프)';
        $saveData['ex2'] = 'TKE(티센크루프)';
        $saveData['ex3'] = $paramData['teamName'];

        if( -1 == $paramData['buyLimitCount'] ){
            $saveData['appFl'] = 'n';
        }else{
            $saveData['appFl'] = 'y';
            $saveData['approvalDt'] = date('Y-m-d H:i:00');
        }
        $saveData['sleepFl'] = 'n';
        $saveData['entryDt'] = date('Y-m-d H:i:00');
        $saveData['changePasswordDt'] = date('Y-m-d H:i:00');

        //저장 데이터
        //SitelabLogger::logger('===> 저장 데이터 체크 ');
        //SitelabLogger::logger($saveData);

        //1. 기존 회원 찾기
        $findSql = "select * from {$beforeMemberTempTable} where memId='{$saveData['memId']}'";
        $beforeMemData = DBUtil2::runSelect($findSql);
        if(!empty($beforeMemData)){
            //2-1. 있으면 회원 정보 가져와서 그대로 삽입 후 업데이트
            $memNo = $beforeMemData[0]['memNo'];
            //DBUtil2::delete(DB_MEMBER, new SearchVo('memNo=?', $memNo));
            $insertSql = "insert into es_member {$findSql}";
            DBUtil2::runSql($insertSql);
            DBUtil2::update(DB_MEMBER, $saveData, new SearchVo('memNo=?', $memNo));
            DBUtil2::runSql("delete from es_memberSleep where memId = '{$saveData['memId']}'");
            //gd_debug("있어서 업데이트 {$saveData['memId']} : " . $upRslt);
            //SitelabLogger::logger("기존회원 정보 업데이트 : {$memNo}");
        }else{
            //2-2. 없으면 정보 삽입
            //DBUtil2::delete(DB_MEMBER, new SearchVo('memId=?', $paramData['memId']));
            $memNo = DBUtil2::insert(DB_MEMBER,$saveData);
            //gd_debug("없어서 인서트 {$saveData['memId']} : " . $memNo);
            //SitelabLogger::logger("신규회원 등록 : {$memNo}");
        }

        //3.구매제한 수량 및 배송지 정보 추가 (memNo delete insert)
        DBUtil2::delete('sl_setMemberConfig', new SearchVo('memNo=?', $memNo));
        $rslt = DBUtil2::insert('sl_setMemberConfig',[
            'memNo' => $memNo,
            'memberType' => $memberType,
            'buyLimitCount' => $paramData['buyLimitCount'],
            'teamName' => $paramData['teamName'],
            'repFl' => !empty($paramData['teamRep'])?'y':'n' ,
        ]);
        //gd_debug("구매 {$saveData['memId']} : " . $memNo);
        //SitelabLogger::logger('구매수량 업데이트 번호 : ' . $rslt);

        return true;
    }

    public function getTkeOrderList($pageNum = 10, $dates = null, $statusMode = null, $memberConfig = null)
    {
        //튜닝
        $orderComponent = SlLoader::cLoad('order','order');

        // 배열 선언
        $arrBind = $arrWhere = [];

        // 상품혜택관리 치환코드 생성
/*        if(!is_object($goodsBenefit)){
            $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
        }*/

        // 회원 or 비회원 패스워드 체크
        if (MemberUtil::checkLogin() == 'member') {
            $memNoList = [];
            $teamMemberList = DBUtil2::getList('sl_setMemberConfig','teamName',$memberConfig['teamName']);
            foreach($teamMemberList as $teamMember){
                $memNoList[] = $teamMember['memNo'];
            }
            $teamStr = implode(',',$memNoList);
            $arrWhere[] = "o.memNo IN ( {$teamStr} )";
        } else {
            throw new AlertRedirectException(__('로그인 정보가 존재하지 않습니다.'), null, null, '../member/login.php');
        }

        // 기간 설정
        if (null !== $dates && is_array($dates) && $dates[0] != '' && $dates[1] != '') {
            $arrWhere[] = 'o.regDt BETWEEN ? AND ?'; // 한달 이내쿼리로 조작';
            $this->db->bind_param_push($arrBind, 's', $dates[0] . ' 00:00:00');
            $this->db->bind_param_push($arrBind, 's', $dates[1] . ' 23:59:59');
        }
        else {  //빈값으로 넘어오면 1년범위 검색
            $dates[0] = date('Y-m-d', strtotime('-365 days'));
            $dates[1] = date('Y-m-d');
            $arrWhere[] = 'o.regDt BETWEEN ? AND ?'; // 한달 이내쿼리로 조작';
            $this->db->bind_param_push($arrBind, 's', $dates[0] . ' 00:00:00');
            $this->db->bind_param_push($arrBind, 's', $dates[1] . ' 23:59:59');
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
        $this->db->bind_param_push($arrBind, 's', 'f1');

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
        $this->db->strJoin = implode('', $arrJoin);
        $this->db->strField = implode(', ', $arrField) . ', o.regDt';
        $this->db->strWhere = implode(' AND ', gd_isset($arrWhere));
        $this->db->strOrder = 'og.orderNo desc';
        $this->db->strLimit = $page->recode['start'] . ',' . $pageNum;
        $this->db->strGroup = 'og.orderNo';

        if (empty($arrBind)) {
            $arrBind = null;
        }
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER . ' o ' . implode(' ', $query);
        $data = $this->db->slave()->query_fetch($strSQL, $arrBind);

        // 현 페이지 검색 결과
        unset($query['group'], $query['order'], $query['limit']);
        $strCntSQL = 'SELECT COUNT(DISTINCT og.orderNo) AS cnt FROM ' . DB_ORDER . ' AS o ' . implode(' ', $query);
        $total = $this->db->slave()->query_fetch($strCntSQL, $arrBind, false)['cnt'];

        // 검색 레코드 수
        $page->recode['total'] = $total;
        $page->setPage();

        // 결제방법 과 처리 상태 설정
        if (gd_isset($data)) {
            foreach ($data as $key => $val) {
                $useMultiShippingKey = false;
                if (\Component\Order\OrderMultiShipping::isUseMultiShipping() == true) {
                    $useMultiShippingKey = true;
                }

                $val['goods'] = $orderComponent->getOrderGoodsData($val['orderNo'], null, null, null, null, false, false, null, null, false, $useMultiShippingKey);
                $val['orderInfo'] = $orderComponent->getOrderInfo($val['orderNo'], false);
                $val['orderGoodsCnt'] = gd_isset(count($val['goods']), 0);
                $val['settleName'] = $orderComponent->getSettleKind($val['settleKind']);

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
                        //$val['goods'][$aKey] = $goodsBenefit->goodsDataFrontReplaceCode($aVal, 'mypage');

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

        return gd_htmlspecialchars_stripslashes($data);
    }

    public function setFindAttributeTke(){

        $goodsList = DBUtil2::runSelect( "select * from sl_3plProduct where scmNo = 8 order by productName" );

        $contentsList = [
            'goodsPart' => [
                'TKEK' => 'TKEK',
                '파트너사' => '파트너사',
                '파트너' => '파트너사',
            ],
            'produceYear' => [
                '21' => '2021',
                '22' => '2022',
                '23' => '2023',
            ],
            'season' => [
                '춘추' => '춘추',
                '하계' => '하계',
                '동계' => '동계',
            ],
            'goodsType' => [
                '카라티'=>'상의',
                '바지'=>'하의',
                '점퍼'=>'상의',
            ],
        ];

        foreach($goodsList as $goods){
            foreach($contentsList as $compareKey => $contents){
                foreach($contents as $compareData => $value){

                    if (strpos($goods['productName'] , $compareData) !== false) {
                        $rslt = DBUtil2::update('sl_3plProduct',[$compareKey=>$value], new SearchVo('sno=?', $goods['sno']));
                        gd_debug($goods['sno'] . ' : ' . $compareKey .' => ' . $value . ' ==> ' . $rslt);
                        break;
                    }
                }
            }
        }

    }

    public function setFindAttribute(){
        $goodsList = DBUtil2::getListBySearchVo(DB_GOODS, new SearchVo(" NOT(goodsNm like '%기성%' ) AND NOT(goodsNm like '%트레이닝%' ) AND stockFl = 'y' AND delFl='n' AND scmNo=?",8));
        $contentsList = [
            'goodsPart' => [
                'TKEK' => 'TKEK',
                '파트너사' => '파트너사',
                '파트너' => '파트너사',
            ],
            'produceYear' => [
                '21' => '2021',
                '22' => '2022',
                '23' => '2023',
            ],
            'season' => [
                '춘추' => '춘추',
                '하계' => '하계',
                '동계' => '동계',
            ],
            'goodsType' => [
                '카라티'=>'상의',
                '바지'=>'하의',
                '점퍼'=>'상의',
            ],
        ];

        $updateDataList = [];
        foreach($goodsList as $goods){

            $updateData = [];
            foreach($contentsList as $compareKey => $contents){

                foreach($contents as $compareData => $value){
                    if (strpos($goods['goodsNm'] , $compareData) !== false) {
                        $updateData[$compareKey] = $value;
                        break;
                    }
                }
            }

            $updateData['produceYear'] = gd_date_format('Y',$goods['regDt']);
            $updateData['goodsNo'] = $goods['goodsNo'];

            $updateDataList[$goods['goodsNo']] = [
                'goodsNm' => $goods['goodsNm'],
                'updateData' => $updateData
            ];

        }

        //DBUtil2::runSql('truncate table sl_goodsFindAttribute');
        foreach($updateDataList as $updateData){
            DBUtil2::insert('sl_goodsFindAttribute',$updateData['updateData']);
        }

        gd_debug($updateDataList);
    }


    /**
     * 당진사업처의 경우 출고대기 상태로 변경.
     * 폐쇄몰 담당자가 수기로 결제완료 상태로 바꾼 후
     * @param $orderNo
     * @throws Exception
     */
    public function setDangJinVest($orderNo){
        $searchVo = new SearchVo('orderNo=?', $orderNo);
        $order = DBUtil2::getOneBySearchVo(DB_ORDER, $searchVo);
        $orderGoodsList = DBUtil2::getListBySearchVo(DB_ORDER_GOODS, $searchVo);
        $isOk = false;
        foreach( $orderGoodsList as $orderGoods ){
            if( '1000000375' == $orderGoods['goodsNo'] && 14998 == $order['memNo']){ //당진 담당자 + 한전 하계 조끼.
                $isOk = true;
                //출고대기 처리. (해당 상품만)
                DBUtil2::update(DB_ORDER_GOODS,['orderStatus'=>'p2'],new SearchVo('sno=?', $orderGoods['sno']));
            }
        }

        if( $isOk ){
            //메일 발송
            $todayKr = date('m월d일');
            $subject = "(엠에스이노버) {$todayKr} 당진 조끼 주문 알림";
            $contents = "<br>금일 당진 조끼 주문이 있습니다.<br>내용 확인 후 신용나염에 발주 바랍니다. <br>";
            if( SlCommonUtil::isDev() ){
                $to = 'jhsong@msinnover.com';
            }else{
                $to = implode(',',SlCodeMap::ORDER_MAIL_LIST);
            }
            SiteLabMailUtil::sendSimpleMail($subject, $contents, $to);
        }

    }

    /**
     * 당진 후처리.
     * @param $val
     *
     */
    public function setDangJinVestAfter(&$val){
        if( '1000000375' == $val['goodsNo'] && 14998 == $val['memNo'] ){ //당진 담당자 + 한전 하계 조끼.
            $val['receiverZoneCode'] = '04561';
            $val['receiverName'] = '신용나염';
            $val['address'] = '서울시 중구 광희동1가 191번지 4층';
            $val['receiverPhone'] = '02-2277-7378';
            $val['receiverCellPhone'] = '02-2277-7378';
        }
    }


}

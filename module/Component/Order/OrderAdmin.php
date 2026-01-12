<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */
namespace Component\Order;

use App;
use Component\Mail\MailAutoObserver;
use Component\Godo\NaverPayAPI;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Database\DBTableField;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Manager;
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
use Exception;
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
use LogHandler;
use Request;
use Session;
use Framework\Utility\DateTimeUtils;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBConst;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Framework\Debug\Exception\AlertBackException;

/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class OrderAdmin extends \Bundle\Component\Order\OrderAdmin{

    use OrderAdminListTrait;

    private $orderService;
    private $goodsStockService;

    public function __construct(){
        $this->orderService = SlLoader::cLoad('Order','OrderService');
        $this->goodsStockService = SlLoader::cLoad('Goods','GoodsStock');
        parent::__construct();
    }

    /**
     * 환불 완료 처리
     * @param $getData
     * @param $autoProcess
     */
    public function setRefundComplete($getData, $autoProcess){
        parent::setRefundComplete($getData, $autoProcess);
        //주문 처리
        //SitelabLogger::logger('환불완료 처리===============');
        //SitelabLogger::logger($getData);
        $this->orderService->refineApplyPolicyOrderGoods($getData, 2);
    }

    /**
     * 관리자 주문 상세정보
     *
     * @param string $orderNo 주문 번호
     * @param null $orderGoodsNo
     * @param integer $handleSno 반품/교환/환불 테이블 번호
     *
     * @param string $statusMode
     * @param array $excludeStatus 제외할 주문상태 값
     * @param string $orderStatusMode 주문상세페이지 로드시 내역 종류
     *
     * @return array|bool 주문 상세정보
     * @throws Exception
     */
    public function getOrderView($orderNo, $orderGoodsNo = null, $handleSno = null, $statusMode = null, $excludeStatus = null, $orderStatusMode = null){
        $orderGoodsData = parent::getOrderView($orderNo, $orderGoodsNo, $handleSno, $statusMode, $excludeStatus, $orderStatusMode);
        $orderService = SlLoader::cLoad('Order','OrderService');

        $orderAcctData = $this->addOrderAcctData($orderNo);
        $orderGoodsData['orderAcctClass']  = $orderAcctData['orderAcctClass'];
        $orderGoodsData['orderAcctStr']  = $orderAcctData['orderAcctStr'];

        $orderScmData = DBUtil2::getOne('sl_orderScm', 'orderNo', $orderNo);
        $branchDept = DBUtil2::getOne('sl_branchDept', 'sno', $orderScmData['branchDept']);
        $orderGoodsData['branchDept'] = $branchDept['branch'] . ' - ' . $branchDept['dept'];

        foreach( $orderGoodsData['goods'] as $orderGoodsDataKey => $scmData){
            foreach($scmData as $scmKey => $deliveryData){
                foreach($deliveryData as $deliveryKey => $eachOrderGoods){
                    $applyPolicy = $orderService->getOrderGoodsPolicyApplyInfo($orderNo,$eachOrderGoods['sno']);
                    //무상 제공 수량
                    $applyPolicy['policyInfo'] = json_decode(gd_htmlspecialchars_stripslashes($applyPolicy['policyInfo']),true);
                    //무상 금액 , 본사지불금액 , 구매자 결제금액 , 정책 정보 파싱
                    $orderGoodsData['goods'][$orderGoodsDataKey][$scmKey][$deliveryKey]['applyPolicy'] = $applyPolicy;

                    //아시아나 추가 정보 보기
                    $asiaOrder = DBUtil2::getOne('sl_asianaOrderHistory', 'orderGoodsSno', $eachOrderGoods['sno']);
                    $orderGoodsData['goods'][$orderGoodsDataKey][$scmKey][$deliveryKey]['asianaOrderInfo'] = $asiaOrder;

                }
            }
        }

        return $orderGoodsData;
    }

    /**
     * 관리자 주문 리스트
     * 반품/교환/환불 정보까지 한번에 가져올 수 있게 되어있다.
     *
     * @param string  $searchData   검색 데이타
     * @param string  $searchPeriod 기본 조회 기간
     * @param boolean $isUserHandle
     *
     * @return array 주문 리스트 정보
     */
    public function getOrderListForAdmin($searchData, $searchPeriod, $isUserHandle = false){

        //튜닝 : 초기값 지정
        if(empty($searchData['sort'])){
            $searchData['sort'] = 'sm.companyNm desc';
        }
        if(empty($searchData['pageNum'])){

            $searchData['pageNum'] = 20;
            $this->search['treatDate'][0] = date('Y-m-d');
            $this->search['treatDate'][1] = date('Y-m-d');
            
            /*$baseName = Request::getInfoUri()['basename'];
            if( 'order_list_settle.php' === $baseName || 'order_list_all.php' === $baseName){
                $searchData['pageNum'] = 20;
                $this->search['treatDate'][0] = date('Y-m-d');
                $this->search['treatDate'][1] = date('Y-m-d');
            }else{
                $searchData['pageNum'] = 500;
            }*/
        }

        if(trim($searchData['orderAdminGridMode']) !== ''){
            //주문리스트 그리드 설정
            $orderAdminGrid = \App::load('\\Component\\Order\\OrderAdminGrid');
            $this->orderGridConfigList = $orderAdminGrid->getSelectOrderGridConfigList($searchData['orderAdminGridMode']);
        }

        if( 7 == $searchData['scmNo'][0] ) {
            $this->search['treatDate'][0] = date('Y-m-d');
            $this->search['treatDate'][1] = date('Y-m-d');
        }
        // --- 검색 설정
        $this->_setSearch($searchData, $searchPeriod, $isUserHandle);
        if( 7 == $searchData['scmNo'][0] ) {
            $this->search['treatDate'][0] = '2015-01-01';
            $this->search['treatDate'][1] = date('Y-m-d');
        }

        // 주문번호별로 보기
        $isDisplayOrderGoods = ($this->search['view'] !== 'order');// view모드가 orderGoods & orderGoodsSimple이 아닌 경우 true
        $this->search['searchPeriod'] = gd_isset($searchData['searchPeriod']);

        // --- 페이지 기본설정
        gd_isset($searchData['page'], 1);
        gd_isset($searchData['pageNum'], 20);
        $page = \App::load('\\Component\\Page\\Page', $searchData['page'],0,0,$searchData['pageNum']);
        $page->setCache(true)->setUrl(\Request::getQueryString()); // 페이지당 리스트 수

        // 주문상태 정렬 예외 케이스 처리
        if ($searchData['sort'] == 'og.orderStatus asc') {
            $searchData['sort'] = 'case LEFT(og.orderStatus, 1) when \'o\' then \'01\' when \'p\' then \'02\' when \'g\' then \'03\' when \'d\' then \'04\' when \'s\' then \'05\' when \'e\' then \'06\' when \'b\' then \'07\' when \'r\' then \'08\' when \'c\' then \'09\' when \'f\' then \'10\' else \'11\' end';
        } elseif ($searchData['sort'] == 'og.orderStatus desc') {
            $searchData['sort'] = 'case LEFT(og.orderStatus, 1) when \'f\' then \'01\' when \'c\' then \'02\' when \'r\' then \'03\' when \'b\' then \'04\' when \'e\' then \'05\' when \'s\' then \'06\' when \'d\' then \'07\' when \'g\' then \'08\' when \'p\' then \'09\' when \'o\' then \'10\' else \'11\' end';
        }

        if($isDisplayOrderGoods){
            if(trim($searchData['sort']) !== ''){
                $orderSort = $searchData['sort'] . ', og.orderDeliverySno asc';
            }
            else {
                if($this->isUseMultiShipping === true){
                    $orderSort = $this->orderGoodsMultiShippingOrderBy;
                }
                else {
                    $orderSort = $this->orderGoodsOrderBy;
                }
            }
        }
        else {
            $orderSort = gd_isset($searchData['sort'], 'og.orderNo desc');
            if( 'og.orderNo desc' !== $orderSort ){
                //gd_debug($orderSort);
            }
        }
        //튜닝 추가
        if(preg_match("/companyNm/", $orderSort)){
            if(preg_match("/desc/", $orderSort)){
                $orderSort = "sm.companyNm desc, oi.orderName desc, o.regDt desc";
            }else {
                $orderSort = "sm.companyNm asc, oi.orderName asc, o.regDt desc";
            }
        }

        //상품준비중 리스트에서 묶음배송 정렬 기준
        if(preg_match("/packetCode/", $orderSort)){
            if(preg_match("/desc/", $orderSort)){
                $orderSort = "oi.packetCode desc, og.orderNo desc";
            }
            else {
                $orderSort = "oi.packetCode desc, og.orderNo asc";
            }
        }
        //복수배송지 사용시 배송지별 묶음
        if($this->isUseMultiShipping === true){
            if(!preg_match("/orderInfoCd/", $orderSort)){
                $orderSort = $orderSort . ", oi.orderInfoCd asc";
            }
        }

        $arrIncludeOh = [
            'handleMode',
            'beforeStatus',
            'refundMethod',
            'handleReason',
            'handleDetailReason',
            'regDt AS handleRegDt',
            'handleDt',
        ];
        $arrIncludeOi = [
            'orderName',
            'receiverName',
            'orderMemo',
            'orderCellPhone',
            'packetCode',
            'smsFl',
        ];


        $tmpField[] = ['oh.regDt AS handleRegDt'];
        $tmpField[] = DBTableField::setTableField('tableOrderHandle', $arrIncludeOh, null, 'oh');
        $tmpField[] = DBTableField::setTableField('tableOrderInfo', $arrIncludeOi, null, 'oi');
        $tmpField[] = ['oi.sno AS orderInfoSno'];

        // join 문
        $join[] = ' LEFT JOIN ' . DB_ORDER . ' o ON o.orderNo = og.orderNo ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_HANDLE . ' oh ON og.handleSno = oh.sno AND og.orderNo = oh.orderNo ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_DELIVERY . ' od ON og.orderDeliverySno = od.sno ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_INFO . ' oi ON (og.orderNo = oi.orderNo)   
                    AND (CASE WHEN od.orderInfoSno > 0 THEN od.orderInfoSno = oi.sno ELSE oi.orderInfoCd = 1 END)';

        if(($this->search['key'] =='all' && empty($this->search['keyword']) === false)  || $this->search['key'] =='sm.companyNm' || strpos($orderSort, "sm.companyNm ") !== false || $this->multiSearchScmJoinFl) {
            $join[] = ' LEFT JOIN ' . DB_SCM_MANAGE . ' sm ON og.scmNo = sm.scmNo ';
        }
        if((($this->search['key'] =='all' && empty($this->search['keyword']) === false)  || $this->search['key'] =='pu.purchaseNm') && gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true && gd_is_provider() === false || $this->multiSearchPurchaseJoinFl) {
            $join[] = ' LEFT JOIN ' . DB_PURCHASE . ' pu ON og.purchaseNo = pu.purchaseNo ';
        }
        if(($this->search['key'] =='all' && empty($this->search['keyword']) === false) || $this->search['key'] =='m.nickNm' || $this->search['key'] =='m.memId' || ($this->search['memFl'] =='y' && empty($this->search['memberGroupNo']) === false ) || $this->multiSearchMemberJoinFl) {
            $join[] = ' LEFT JOIN ' . DB_MEMBER . ' m ON o.memNo = m.memNo AND m.memNo > 0 ';
        }
        //상품 브랜드 코드 검색
        if(empty($this->search['brandCd']) === false || empty($this->search['brandNoneFl'])=== false) {
            $join[] = ' LEFT JOIN ' . DB_GOODS . ' as g ON og.goodsNo = g.goodsNo ';
        }
        //택배 예약 상태에 따른 검색
        if ($this->search['invoiceReserveFl']) {
            $join[] = ' LEFT JOIN ' . DB_ORDER_GODO_POST . ' ogp ON ogp.invoiceNo = og.invoiceNo ';
        }

        // 쿠폰검색시만 join
        if ($this->search['couponNo'] > 0) {
            $join[] = ' LEFT JOIN ' . DB_ORDER_COUPON . ' oc ON o.orderNo = oc.orderNo ';
            $join[] = ' LEFT JOIN ' . DB_MEMBER_COUPON . ' mc ON mc.memberCouponNo = oc.memberCouponNo ';
        }

        // 반품/교환/환불신청 사용에 따른 리스트 별도 처리 (조건은 검색 메서드 참고)
        if ($isUserHandle) {

            $arrIncludeOuh = [
                'sno',
                'userHandleMode',
                'userHandleFl',
                'userHandleGoodsNo',
                'userHandleGoodsCnt',
                'userHandleReason',
                'userHandleDetailReason',
                'adminHandleReason',
            ];
            $tmpField[] = ['ouh.regDt AS userHandleRegDt','ouh.sno AS userHandleNo'];
            $tmpField[] = DBTableField::setTableField('tableOrderUserHandle', $arrIncludeOuh, null, 'ouh');
            $joinOrderStatusArray = $this->getExcludeOrderStatus($this->orderStatus, $this->statusUserClaimRequestCode);
            $join[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON (og.userHandleSno = ouh.sno || (og.sno = ouh.userHandleGoodsNo && og.orderStatus IN (\'' . implode('\',\'', array_keys($joinOrderStatusArray)) . '\')))';
        }
        // @kookoo135 고객 클레임 신청 주문 제외
        if ($this->search['userHandleViewFl'] == 'y') {
            if (!$isDisplayOrderGoods) {
                $this->arrWhere[] = ' NOT EXISTS (SELECT 1 FROM ' . DB_ORDER_USER_HANDLE . ' WHERE o.orderNo = orderNo AND userHandleFl = \'r\')';
            } else {
                $this->arrWhere[] = ' NOT EXISTS (SELECT 1 FROM ' . DB_ORDER_USER_HANDLE . ' WHERE (og.userHandleSno = sno OR og.sno = userHandleGoodsNo) AND userHandleFl = \'r\')';
            }
        }

        // 상품º주문번호별 메모 검색시
        if($this->search['withAdminMemoFl'] == 'y'){
            $join[] = ' LEFT JOIN ' . DB_ADMIN_ORDER_GOODS_MEMO . ' aogm ON o.orderNo = aogm.orderNo ';
        }

        //튜닝
        $this->setListTuneBegin($join, $tmpField, $searchData);

        // 쿼리용 필드 합침
        $tmpKey = array_keys($tmpField);
        $arrField = [];
        foreach ($tmpKey as $key) {
            $arrField = array_merge($arrField, $tmpField[$key]);
        }
        unset($tmpField, $tmpKey);
        // 현 페이지 결과
        $this->db->strField = 'og.sno,og.orderNo,og.goodsNo,og.scmNo ,og.mallSno ,og.purchaseNo ,o.memNo, o.trackingKey, o.orderTypeFl, o.appOs, o.pushCode ,' . implode(', ', $arrField) . ',og.orderDeliverySno';

        // addGoods 필드 변경 처리 (goods와 동일해서)
        $this->db->strJoin = implode('', $join);
        $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));
        $this->db->strOrder = $orderSort;
        if (!$isDisplayOrderGoods) {
            if($searchData['statusMode'] === 'o'){
                // 입금대기리스트 > 주문번호별 에서 '주문상품명' 을 입금대기 상태의 주문상품명만으로 노출시키기 위해 개수를 구함
                $this->db->strField .= ', SUM(IF(LEFT(og.orderStatus, 1)=\'o\', 1, 0)) AS noPay';
            }
            $this->db->strField .= ', o.regDt, SUM(IF(LEFT(og.orderStatus, 1)=\'o\' OR LEFT(og.orderStatus, 1)=\'p\' OR LEFT(og.orderStatus,1)=\'g\', 1, 0)) AS noDelivery';
            $this->db->strField .= ', SUM(IF(LEFT(og.orderStatus, 1)=\'d\' AND og.orderStatus != \'d2\', 1, 0)) AS deliverying';
            $this->db->strField .= ', SUM(IF(og.orderStatus=\'d2\' OR LEFT(og.orderStatus, 1)=\'s\', 1, 0)) AS deliveryed';
            $this->db->strField .= ', SUM(IF(LEFT(og.orderStatus, 1)=\'c\', 1, 0)) AS cancel';
            $this->db->strField .= ', SUM(IF(LEFT(og.orderStatus, 1)=\'e\', 1, 0)) AS exchange';
            $this->db->strField .= ', SUM(IF(LEFT(og.orderStatus, 1)=\'b\', 1, 0)) AS back';
            $this->db->strField .= ', SUM(IF(LEFT(og.orderStatus, 1)=\'r\', 1, 0)) AS refund';

            $this->db->strGroup = 'og.orderNo';
        }

        gd_isset($searchData['useStrLimit'], true);
        if ($searchData['useStrLimit']) {
            $this->db->strLimit = $page->recode['start'] . ',' . $searchData['pageNum'];
        }

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', $query);
        $getData = $this->db->query_fetch($strSQL, $this->arrBind);
        //gd_debug($strSQL);

        // 검색 레코드 수
        $query['group'] = 'GROUP BY og.orderNo';
        unset($query['order']);
        if($page->hasRecodeCache('total') === false) {
            if (Manager::isProvider()) {
                // 검색된 주문의 개수
                $total = $this->db->query_fetch('SELECT COUNT(og.sno) AS cnt FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', str_ireplace('limit ' . $page->recode['start'] . ',' . $searchData['pageNum'], '', $query)), $this->arrBind, true);

                // 검색된 주문 총 배송비 금액
                $priceDeliveryQuery = $query;
                if(trim($query['group']) !== ''){
                    $priceDeliveryQuery['group'] = $query['group'] . ', og.orderDeliverySno';
                }
                else {
                    $priceDeliveryQuery['group'] = 'GROUP BY og.orderNo, og.orderDeliverySno';
                }

                $providerPriceQueryArr = [];
                $providerPriceQueryArr[] = 'SELECT';
                $providerPriceQueryArr[] = '(od.realTaxSupplyDeliveryCharge + od.realTaxVatDeliveryCharge + od.realTaxFreeDeliveryCharge + od.divisionDeliveryUseDeposit + od.divisionDeliveryUseMileage) AS deliveryPrice';
                $providerPriceQueryArr[] = 'FROM ' . DB_ORDER_GOODS . ' og';
                $providerPriceQueryArr[] = implode(' ', str_ireplace('limit ' . $page->recode['start'] . ',' . $searchData['pageNum'], '', $priceDeliveryQuery));
                $providerPriceQuery = implode(' ', $providerPriceQueryArr);
                $providerTotalDeliveryPrice = $this->db->query_fetch($providerPriceQuery, $this->arrBind, true);
                if(count($total) > 0){
                    $total[0]['price'] += array_sum(array_column($providerTotalDeliveryPrice, 'deliveryPrice'));
                }

                // 검색된 주문 총 상품 금액
                $priceQuery = $query;
                if(trim($query['where']) !== ''){
                    $priceQuery['where'] = str_replace("WHERE ", "WHERE og.orderStatus != 'r3' AND ", $query['where']);
                }
                else {
                    $priceQuery['where'] = "WHERE og.orderStatus != 'r3'";
                }

                $providerPriceQueryArr = [];
                $providerPriceQueryArr[] = 'SELECT';
                $providerPriceQueryArr[] = 'SUM((og.goodsPrice + og.optionPrice + og.optionTextPrice) * og.goodsCnt) AS price';
                $providerPriceQueryArr[] = 'FROM ' . DB_ORDER_GOODS . ' og';
                $providerPriceQueryArr[] = implode(' ', str_ireplace('limit ' . $page->recode['start'] . ',' . $searchData['pageNum'], '', $priceQuery));
                $providerPriceQuery = implode(' ', $providerPriceQueryArr);
                $providerTotalPrice = $this->db->query_fetch($providerPriceQuery, $this->arrBind, true);

                if(count($total) > 0){
                    $total[0]['price'] += array_sum(array_column($providerTotalPrice, 'price'));
                }
            }
            else {
                $total = $this->db->query_fetch('SELECT sum(if(\'goods\' = og.goodsType , og.goodsCnt, 0)) as orderGoodsCnt, (o.realTaxSupplyPrice + o.realTaxFreePrice + o.realTaxVatPrice) AS price, COUNT(og.sno) AS cnt FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', str_ireplace('limit ' . $page->recode['start'] . ',' . $searchData['pageNum'], '', $query)), $this->arrBind, true);
            }

            $page->recode['totalPrice'] = array_sum(array_column($total, 'price'));
            $page->recode['orderGoodsCnt'] = array_sum(array_column($total, 'orderGoodsCnt')); //튜닝 추가.
        }

        if ($isDisplayOrderGoods) {
            $ogSno = 'og.sno';
            $groupby = '';
            $page->recode['total'] = array_sum(array_column($total, 'cnt'));
            $this->search['deliveryFl'] = true;
        } else {
            $ogSno = 'og.orderNo';
            $groupby = ' GROUP BY og.orderNo';
            $page->recode['total'] = count($total);
        }

        // 주문상태에 따른 전체 갯수
        if($page->hasRecodeCache('amount') === false) {
            if (Manager::isProvider()) {
                if ($this->search['statusMode'] !== null) {
                    $total = $this->db->query_fetch('SELECT COUNT(' . $ogSno . ') as total FROM ' . DB_ORDER_GOODS . ' og WHERE og.scmNo=' . Session::get('manager.scmNo') . ' AND (og.orderStatus LIKE concat(\'' . $this->search['statusMode'] . '\',\'%\'))' . $groupby, null, true);
                } else {
                    $total = $this->db->query_fetch('SELECT COUNT(' . $ogSno . ') as total FROM ' . DB_ORDER_GOODS . ' og WHERE og.scmNo=' . Session::get('manager.scmNo') . ' AND LEFT(og.orderStatus, 1) NOT IN (\'o\', \'c\') AND og.orderStatus != \'' . $this->arrBind[1] . '\'' . $groupby, null, true);
                }
            } else {
                if ($this->search['statusMode'] !== null) {
                    $total = $this->db->query_fetch('SELECT COUNT(' . $ogSno . ') as total FROM ' . DB_ORDER_GOODS . ' og WHERE (og.orderStatus LIKE concat(\'' . $this->search['statusMode'] . '\',\'%\'))' . $groupby, null, true);
                } else if ($searchData['navTabs'] && $searchData['memNo']) { // CRM 주문관리 회원일련번호기준 갯수
                    $total = $this->db->query_fetch('SELECT COUNT(' . $ogSno . ') as total FROM ' . DB_ORDER_GOODS . ' og LEFT JOIN ' . DB_ORDER . ' o ON o.orderNo = og.orderNo WHERE o.memNo = ' . $searchData['memNo'] . ' AND (og.orderStatus != \'' . $this->arrBind[1] . '\') AND og.orderStatus != \'f1\'' . $groupby, null, true);
                } else {
                    $total = $this->db->query_fetch('SELECT COUNT(' . $ogSno . ') as total FROM ' . DB_ORDER_GOODS . ' og WHERE (og.orderStatus != \'' . $this->arrBind[1] . '\') AND og.orderStatus != \'f1\'' . $groupby, null, true);
                }
            }
        }

        // 주문상태/상품주분번호별 쿼리에 따른 전체갯수 처리
        if ($isDisplayOrderGoods) {
            $total = array_shift($total);
        } else {
            $total['total'] = count($total);
        }

        $page->recode['amount'] = $total['total'];

        $page->setPage(null,['totalPrice']);

        $orderAdminList =  $this->setOrderListForAdmin($getData, $isUserHandle, $isDisplayOrderGoods, true, $searchData['statusMode']);

        //튜닝
        $orderService = SlLoader::cLoad('Order','OrderService');

        foreach($orderAdminList['data'] as $orderNo => $orderAdminData){
            $scmNo = $orderService->getOrderScm($orderNo)['scmNo'];
            //승인처리하는 공급사만 승인 데이터 추가
            //if(  !empty( SlCodeMap::SCM_USE_ORDER_ACCEPT_[$scmNo])  ){
            if(  SlCommonUtil::getIsOrderAccept($scmNo)  ){
                $acceptData = $orderService->getOrderAcceptData($orderNo);
                $orderAcctStatus = $acceptData['orderAcctStatus'];
                $orderAcctClass = SlCodeMap::ORDER_ACCT_STATUS_LABEL_COLOR[$orderAcctStatus];
                $orderAcctStr = SlCodeMap::ORDER_ACCT_STATUS[$orderAcctStatus];
                $orderAdminData['orderAcctClass'] = $orderAcctClass;
                $orderAdminData['orderAcctStr'] = $orderAcctStr;
                $orderAdminData['orderAcctRegDt'] = $acceptData['regDt'];
                $orderAdminData['orderAcctModDt'] = $acceptData['modDt'];
                foreach($orderAdminData['goods'] as $goodsKey1 => $goodsData1  ){
                    foreach($goodsData1 as $goodsKey2 => $goodsData2  ){
                        foreach($goodsData2 as $goodsKey3 => $goodsData3  ){
                            $goodsData3['orderAcctClass'] = $orderAcctClass;
                            $goodsData3['orderAcctStr'] = $orderAcctStr;
                            $goodsData3['orderAcctRegDt'] = $acceptData['regDt'];
                            $goodsData3['orderAcctModDt'] = $acceptData['modDt'];
                            $orderAdminData['goods'][$goodsKey1][$goodsKey2][$goodsKey3] = $goodsData3;
                        }
                    }
                }
                $orderAdminList['data'][$orderNo] = $orderAdminData;
            }
        }

        $orderAdminList['search']['branchList'] = SlCommonUtil::getBranchList();
        //$orderAdminList['search'] = SlCommonUtil::getBranchList();

        return $orderAdminList;
    }

    /**
     * 사용자 교환/반품/환불신청을 승인 or 거절을 처리
     * 승인시 자동으로 교환/반품/환불 접수 상태로 주문상태를 변경하며,
     * 거절시 사유를 사용자가 볼 수 있도록 해당 테이블을 업데이트 한다.
     *
     * @param array $arrData 리퀘스트 데이터
     * @param boolean $userHandleFl 사용자 반품/교환/환불 요청
     *
     * @return boolean 오류시 false
     * @throws Exception
     * @author Jong-tae Ahn <qnibus@godo.co.kr>
     */
    public function approveUserHandle($arrData, $userHandleFl, $autoProceess = false){
        $returnSno = parent::approveUserHandle($arrData, $userHandleFl, $autoProceess);
        foreach($arrData['statusCheck'] as $key => $value){
            $orderNo = explode('||',$value)[0];
            $param['orderNo'] = $orderNo;
            $this->orderService->refineApplyPolicyOrderGoods($param,5);
        }
        return $returnSno;
    }

    /**
     * 주문 상태 수정 전 처리 및 서로 변경할 수 없는 조건에서의 처리
     * 상태변경이 안되는 경우 $this->statusStandardCode 멤버변수 확인 할 것
     *
     * !중요! 해당로직안에는 Exception 절대로 넣지 마시오!
     *
     * @param string $orderNo 주문 번호
     * @param array $goodsData 주문 상품 정보
     * @param string $statusMode 현재 주문 상태코드 (한자리)
     * @param string $changeStatus 변경할 주문 상태 코드
     * @param bool|string $reason 변경사유 ( 기본은 false 이며, 주문 리스트에서 처리시)
     * @param boolean $bundleFl 특정 처리에서의 처리 모드
     * @param string $mode 처리모드(입금대기리스트 구분 필요 시)
     * @param string $useVisit 방문수령여부
     * @param boolean $isFront 프론트의pg에서 넘어오는지의 여부
     *
     * @return boolean
     * @throws Exception
     */
    public function updateStatusUnconditionalPreprocess($orderNo, $goodsData, $statusMode, $changeStatus, $reason = false, $bundleFl = false, $mode = null, $useVisit = null, $autoProcess = false, $isFront = false){
        //SitelabLogger::logger('## updateStatusUnconditionalPreprocess BEGIN');
        $parentResult = parent::updateStatusUnconditionalPreprocess($orderNo, $goodsData, $statusMode, $changeStatus, $reason, $bundleFl, $mode, $useVisit, $autoProcess, $isFront);
        /*if('s1' ==$changeStatus && true == $parentResult ){
            $sendCnt = DBUtil2::getCount('sl_orderMsgHistory',new SearchVo(['orderNo=?','templateId=?'], [$orderNo,  '6' ] ));
            if( 0 == $sendCnt ){
                $orderService = SlLoader::cLoad('Order','OrderService');
                $orderService->sendOrderMsg(6, $orderNo);
                SitelabLogger::logger('## 구매확정 자동 변경 및 메세지 발송 :  ' . $orderNo);
            }
        }*/
        //SitelabLogger::logger('## updateStatusUnconditionalPreprocess END');
        return $parentResult;
    }


    /**
     * 관리자 주문 리스트 엑셀
     * 반품/교환/환불 정보까지 한번에 가져올 수 있게 되어있다.
     *
     * @param string $searchData 검색 데이타
     * @param string $searchPeriod 기본 조회 기간
     *
     * @return array 주문 리스트 정보
     */
    public function getOrderListForAdminExcel($searchData, $searchPeriod, $isUserHandle = false, $orderType='goods',$excelField,$page,$pageLimit)
    {

        unset($this->arrWhere);
        unset($this->arrBind);
        //$excelField  / $page / $pageLimit 해당 정보가 없을경우 튜닝한 업체이므로 기존형태로 반환해줘야함
        // --- 검색 설정
        $this->_setSearch($searchData, $searchPeriod, $isUserHandle);

        if ($searchData['statusCheck'] && is_array($searchData['statusCheck'])) {
            foreach ($searchData['statusCheck'] as $key => $val) {
                foreach ($val as $k => $v) {
                    $_tmp = explode(INT_DIVISION, $v);
                    if($orderType =='goods' && $searchData['view'] =='order') unset($_tmp[1]);
                    if($_tmp[1]) {
                        $tmpWhere[] = "(og.orderNo = ? AND og.sno = ?)";
                        $this->db->bind_param_push($this->arrBind, 's', $_tmp[0]);
                        $this->db->bind_param_push($this->arrBind, 's', $_tmp[1]);
                    } else {
                        $tmpWhere[] = "(og.orderNo = ?)";
                        $this->db->bind_param_push($this->arrBind, 's', $_tmp[0]);
                    }
                }
            }

            $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            unset($tmpWhere);
        }

        // 주문상태 정렬 예외 케이스 처리
        if ($searchData['sort'] == 'og.orderStatus asc') {
            $searchData['sort'] = 'case LEFT(og.orderStatus, 1) when \'o\' then \'1\' when \'p\' then \'2\' when \'g\' then \'3\' when \'d\' then \'4\' when \'s\' then \'5\' when \'e\' then \'6\' when \'b\' then \'7\' when \'r\' then \'8\' when \'c\' then \'9\' when \'f\' then \'10\' else \'11\' end';
        } elseif ($searchData['sort'] == 'og.orderStatus desc') {
            $searchData['sort'] = 'case LEFT(og.orderStatus, 1) when \'f\' then \'1\' when \'c\' then \'2\' when \'r\' then \'3\' when \'b\' then \'4\' when \'e\' then \'5\' when \'s\' then \'6\' when \'d\' then \'7\' when \'g\' then \'8\' when \'p\' then \'9\' when \'o\' then \'10\' else \'11\' end';
        }

        // 정렬 설정
        if($orderType === 'goods'){
            $orderSort = gd_isset($searchData['sort'], $this->orderGoodsMultiShippingOrderBy);
        }
        else {
            $orderSort = gd_isset($searchData['sort'], $this->orderGoodsOrderBy);
        }
        if($orderType === 'goods'){
            if(!preg_match("/orderInfoCd/", $orderSort)){
                $orderSort = $orderSort . ", oi.orderInfoCd asc";
            }
        }

        // 사용 필드
        $arrInclude = [
            'o.orderNo',
            'o.orderChannelFl',
            'o.apiOrderNo',
            'o.memNo',
            'o.orderChannelFl',
            'o.orderGoodsNm',
            'o.orderGoodsCnt',
            'o.settlePrice as totalSettlePrice',
            'o.totalDeliveryCharge',
            'o.useDeposit as totalUseDeposit',
            'o.useMileage as totalUseMileage',
            '(o.totalMemberDcPrice + o.totalMemberDeliveryDcPrice) AS totalMemberDcPrice',
            'o.totalGoodsDcPrice',
            '(o.totalCouponGoodsDcPrice + o.totalCouponOrderDcPrice + o.totalCouponDeliveryDcPrice)as totalCouponDcPrice',
            'totalCouponOrderDcPrice',
            'totalCouponDeliveryDcPrice',
            'o.totalMileage',
            'o.totalGoodsMileage',
            'o.totalMemberMileage',
            '(o.totalCouponGoodsMileage+o.totalCouponOrderMileage) as totalCouponMileage',
            'o.settleKind',
            'o.bankAccount',
            'o.bankSender',
            'o.receiptFl',
            'o.pgResultCode',
            'o.pgTid',
            'o.pgAppNo',
            'o.paymentDt',
            'o.addField',
            'o.mallSno',
            'o.orderGoodsNmStandard',
            'o.overseasSettlePrice',
            'o.currencyPolicy',
            'o.exchangeRatePolicy',
            'o.totalEnuriDcPrice',
            '(o.realTaxSupplyPrice + o.realTaxVatPrice + o.realTaxFreePrice) AS totalRealSettlePrice',
            'o.checkoutData',
            'o.trackingKey',
            'o.fintechData',
            'o.checkoutData',
            'o.orderTypeFl',
            'o.appOs',
            'o.pushCode',
            'o.memberPolicy',
            'o.totalMyappDcPrice',
            'oi.regDt as orderDt',
            'oi.orderName',
            'oi.orderEmail',
            'oi.orderPhone',
            'oi.orderCellPhone',
            'oi.receiverName',
            'oi.receiverPhone',
            'oi.receiverCellPhone',
            'oi.receiverUseSafeNumberFl',
            'oi.receiverSafeNumber',
            'oi.receiverSafeNumberDt',
            'oi.receiverZonecode',
            'oi.receiverZipcode',
            'oi.receiverAddress',
            'oi.receiverAddressSub',
            'oi.receiverCity',
            'oi.receiverState',
            'oi.receiverCountryCode',
            'oi.orderMemo',
            'oi.packetCode',
            'oi.orderInfoCd',
            'oi.visitName',
            'oi.visitPhone',
            'oi.visitMemo',
            '(og.orderDeliverySno) AS orderDeliverySno ',
            '(og.scmNo) AS scmNo ',
            '(og.apiOrderGoodsNo) AS apiOrderGoodsNo ',
            '(og.sno) AS orderGoodsSno ',
            '(og.orderCd) AS orderCd ',
            '(og.orderStatus) AS orderStatus ',
            '(og.goodsNo) AS goodsNo ',
            '(og.goodsCd) AS goodsCd ',
            '(og.goodsModelNo) AS goodsModelNo ',
            '(og.goodsNm) AS goodsNm ',
            '(og.optionInfo) AS optionInfo ',
            '(og.goodsCnt) AS goodsCnt ',
            '(og.goodsWeight) AS goodsWeight ',
            '(og.goodsVolume) AS goodsVolume ',
            '(og.cateCd) AS cateCd ',
            '(og.goodsCnt) AS goodsCnt ',
            '(og.brandCd) AS brandCd ',
            '(og.makerNm) AS makerNm ',
            '(og.originNm) AS originNm ',
            '(og.addGoodsCnt) AS addGoodsCnt ',
            '(og.optionTextInfo) AS optionTextInfo ',
            '(og.goodsTaxInfo) AS goodsTaxInfo ',
            '(og.goodsPrice) AS goodsPrice ',
            '(og.fixedPrice) AS fixedPrice ',
            '(og.costPrice) AS costPrice ',
            '(og.commission) AS commission ',
            '(og.optionPrice) AS optionPrice ',
            '(og.optionCostPrice) AS optionCostPrice ',
            '(og.optionTextPrice) AS optionTextPrice ',
            '(og.invoiceCompanySno) AS invoiceCompanySno ',
            '(og.invoiceNo) AS invoiceNo ',
            '(og.deliveryCompleteDt) AS deliveryCompleteDt ',
            '(og.visitAddress) AS visitAddress ',
            'og.goodsDeliveryCollectFl',
            'og.deliveryMethodFl',
            'og.goodsNmStandard',
            'og.goodsMileage',
            'og.memberMileage',
            'og.couponGoodsMileage',
            'og.divisionUseDeposit',
            'og.divisionUseMileage',
            'og.divisionCouponOrderDcPrice',
            'og.goodsDcPrice',
            '(og.memberDcPrice+og.memberOverlapDcPrice+od.divisionMemberDeliveryDcPrice) as memberDcPrice',
            'og.memberDcPrice as orgMemberDcPrice',
            'og.memberOverlapDcPrice as orgMemberOverlapDcPrice',
            'og.goodsDiscountInfo',
            'og.myappDcPrice',
            '(og.couponGoodsDcPrice + og.divisionCouponOrderDcPrice) as couponGoodsDcPrice',
            '(og.goodsTaxInfo) AS addGoodsTaxInfo ',
            '(og.commission) AS addGoodsCommission ',
            '(og.goodsPrice) AS addGoodsPrice ',
            'og.timeSalePrice',
            'og.finishDt',
            'og.deliveryDt',
            'og.deliveryCompleteDt',
            'og.goodsType',
            'og.hscode',
            'og.checkoutData AS og_checkoutData',
            'og.enuri',
            'oh.handleReason',
            'oh.handleDetailReason',
            'oh.refundMethod',
            'oh.refundBankName',
            'oh.refundAccountNumber',
            'oh.refundDepositor',
            'oh.refundPrice',
            'oh.refundDeliveryCharge',
            'oh.refundDeliveryInsuranceFee',
            'oh.refundUseDeposit',
            'oh.refundUseMileage',
            'oh.refundUseDepositCommission',
            'oh.refundUseMileageCommission',
            'oh.completeCashPrice',
            'oh.completePgPrice',
            'oh.completeCashPrice',
            'oh.completeDepositPrice',
            'oh.completeMileagePrice',
            'oh.refundCharge',
            'oh.refundUseDeposit',
            'oh.refundUseMileage',
            'oh.regDt as handleRegDt',
            'oh.handleDt',
            'od.deliveryCharge',
            'od.orderInfoSno',
            'od.deliveryPolicyCharge',
            'od.deliveryAreaCharge',
            'od.realTaxSupplyDeliveryCharge',
            'od.realTaxVatDeliveryCharge',
            'od.realTaxFreeDeliveryCharge',
            'od.divisionDeliveryUseMileage',
            'od.divisionDeliveryUseDeposit',
        ];
        if($searchData['statusMode'] === 'o'){
            // 입금대기리스트에서 '주문상품명' 을 입금대기 상태의 주문상품명만으로 노출시키기 위해 개수를 구함
            $arrInclude[] = 'SUM(IF(LEFT(og.orderStatus, 1)=\'o\', 1, 0)) AS noPay';
        }

        // join 문
        $join[] = ' LEFT JOIN ' . DB_ORDER . ' o ON o.orderNo = og.orderNo ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_HANDLE . ' oh ON og.handleSno = oh.sno AND og.orderNo = oh.orderNo ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_DELIVERY . ' od ON og.orderDeliverySno = od.sno ';
        $join[] = ' LEFT JOIN ' . DB_ORDER_INFO . ' oi ON (og.orderNo = oi.orderNo) 
                    AND (CASE WHEN od.orderInfoSno > 0 THEN od.orderInfoSno = oi.sno ELSE oi.orderInfoCd = 1 END)';

        //매입처
        if((($this->search['key'] =='all' && empty($this->search['keyword']) === false)  || $this->search['key'] =='pu.purchaseNm' || empty($excelField) === true || in_array("purchaseNm",array_values($excelField))) && gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true && gd_is_provider() === false) {
            $arrIncludePurchase =[
                'pu.purchaseNm'
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludePurchase);
            $join[] = ' LEFT JOIN ' . DB_PURCHASE . ' pu ON og.purchaseNo = pu.purchaseNo ';
            unset($arrIncludePurchase);
        }

        //공급사
        if(in_array("scmNm",array_values($excelField)) || in_array("scmNo",array_values($excelField)) || empty($excelField) === true || empty($searchData['scmFl']) === false || ($searchData['key'] =='all' && $searchData['keyword'])) {
            $arrIncludeScm =[
                'sm.companyNm as scmNm'
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludeScm);
            $join[] = ' LEFT JOIN ' . DB_SCM_MANAGE . ' sm ON og.scmNo = sm.scmNo ';
            unset($arrIncludeScm);
        }

        //회원
        //if(in_array("memNo",array_values($excelField)) || in_array("memNm",array_values($excelField)) ||  in_array("groupNm",array_values($excelField)) || empty($excelField) === true || $searchData['memFl'] || ($searchData['key'] =='all' && $searchData['keyword'])) {
            $arrIncludeMember =[
                'IF(m.memNo > 0, m.memNm, oi.orderName) AS memNm',
                'm.memId',
                'mg.groupNm',
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludeMember);
            $join[] = ' LEFT JOIN ' . DB_MEMBER . ' m ON o.memNo = m.memNo ';
            $join[] = ' LEFT OUTER JOIN ' . DB_MEMBER_GROUP . ' mg ON m.groupSno = mg.sno ';
            unset($arrIncludeMember);
        //}

        //사은품
        if(in_array("oi.presentSno",array_values($excelField)) || empty($excelField) === true || in_array("ogi.giftNo",array_values($excelField))) {
            $arrIncludeGift =[
                'GROUP_CONCAT(ogi.presentSno SEPARATOR "/") AS presentSno ',
                'GROUP_CONCAT(ogi.giftNo SEPARATOR "/") AS giftNo '
            ];

            $arrInclude = array_merge($arrInclude, $arrIncludeGift);

            $join[] = ' LEFT JOIN ' . DB_ORDER_GIFT . ' ogi ON ogi.orderNo = o.orderNo ';
            unset($arrIncludeGift);
        }

        //상품 브랜드 코드 검색
        if(empty($this->search['brandCd']) === false || empty($excelField) === true || empty($this->search['brandNoneFl'])=== false) {
            $join[] = ' LEFT JOIN ' . DB_GOODS . ' as g ON og.goodsNo = g.goodsNo ';
        }

        //택배 예약 상태에 따른 검색
        if ($this->search['invoiceReserveFl']) {
            $join[] = ' LEFT JOIN ' . DB_ORDER_GODO_POST . ' ogp ON ogp.invoiceNo = og.invoiceNo ';
        }

        // 쿠폰검색시만 join
        if ($this->search['couponNo'] > 0) {
            $join[] = ' LEFT JOIN ' . DB_ORDER_COUPON . ' oc ON o.orderNo = oc.orderNo ';
            $join[] = ' LEFT JOIN ' . DB_MEMBER_COUPON . ' mc ON mc.memberCouponNo = oc.memberCouponNo ';
        }

        // 반품/교환/환불신청 사용에 따른 리스트 별도 처리 (조건은 검색 메서드 참고)
        if ($isUserHandle) {
            $arrIncludeOuh = [
                'count(ouh.sno) as totalClaimCnt',
                'userHandleReason',
                'userHandleDetailReason',
                'userRefundAccountNumber',
                'adminHandleReason',
                'ouh.regDt AS userHandleRegDt'
            ];
            $join[] = ' LEFT JOIN ' . DB_ORDER_USER_HANDLE . ' ouh ON og.userHandleSno = ouh.sno ';

            $arrInclude = array_merge($arrInclude, $arrIncludeOuh);
            unset($arrIncludeOuh);
        }
        // @kookoo135 고객 클레임 신청 주문 제외
        if ($this->search['userHandleViewFl'] == 'y') {
            $this->arrWhere[] = ' NOT EXISTS (SELECT 1 FROM ' . DB_ORDER_USER_HANDLE . ' WHERE (og.userHandleSno = sno OR og.sno = userHandleGoodsNo) AND userHandleFl = \'r\')';
        }

        // 현 페이지 결과
        if($page =='0') {
            $this->db->strField = 'og.orderNo';
            $this->db->strJoin = implode('', $join);
            $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));
            if($orderType =='goods') $this->db->strGroup = "CONCAT(og.orderNo,og.orderCd,og.goodsNo)";
            else  $this->db->strGroup = "CONCAT(og.orderNo)";

            //총갯수관련
            $query = $this->db->query_complete();
            $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', $query);
            $result['totalCount'] = $this->db->query_fetch($strSQL, $this->arrBind);
        }

        $tmpField = [];
        $tmpField[] = ['oac.orderAcctStatus AS orderAcctStatusCode'];
        $tmpField[] = ["(case when '1' = orderAcctStatus then '승인대기'  when '2' = orderAcctStatus then '승인완료'  when '3' = orderAcctStatus then '출고불가'  ELSE '' END) AS orderAcctStatus"];

        $this->setListTuneBegin($join, $tmpField, $searchData);

        // 쿼리용 필드 합침
        $tmpKey = array_keys($tmpField);
        $arrField = [];
        foreach ($tmpKey as $key) {
            $arrField = array_merge($arrField, $tmpField[$key]);
        }

        unset($tmpField, $tmpKey);
        $this->db->strField = implode(', ', $arrInclude).",totalGoodsPrice, ".implode(',',$arrField) ; //튜닝
        $this->db->strJoin = implode('', $join);
        $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));

        if($orderType =='goods') $this->db->strGroup = "CONCAT(og.orderNo,og.orderCd,og.goodsNo)";
        else  $this->db->strGroup = "CONCAT(og.orderNo)";


        //튜닝
        //SitelabLogger::logger( '====> 튜닝 부분 확인' );
        $isGroupCountList = $this->isGroupCountList($excelField);
        //SitelabLogger::logger( $excelField );
        if( $isGroupCountList ){
            $this->setGroupCountList($join);
        }
        //SitelabLogger::logger( $this->db->strField );


        $this->db->strOrder = $orderSort;
        if($pageLimit) $this->db->strLimit = (($page * $pageLimit)) . "," . $pageLimit;

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_ORDER_GOODS . ' og ' . implode(' ', $query);

        //SitelabLogger::logger('===> TEST QUERY SQL');
        //SitelabLogger::logger($strSQL);

        if(empty($excelField) === false) {
            if (Manager::isProvider()) {
                $result['orderList'] = $this->db->query_fetch($strSQL, $this->arrBind);
            }
            else {
                $result['orderList'] = $this->db->query_fetch_generator($strSQL, $this->arrBind);
            }
        }
        else {
            $result = $this->db->query_fetch($strSQL, $this->arrBind);
        }

        if (Manager::isProvider()) {
            $result = $this->getProviderTotalPriceExcelList($result, $orderType);
        }

        return $result;
    }

    protected function _setSearch($searchData, $searchPeriod = 7, $isUserHandle = false){

        //튜닝추가
        $searchData['view'] = gd_isset(\Request::get()->get('view'),$searchData['view']);

        if( !empty(\Session::get('manager')) && !empty(\Request::get()->get('writerMemNo'))  ){
            $searchData['memNo'] = \Request::get()->get('writerMemNo');
        }

        $isMultiSearch = gd_isset(\Session::get('manager.isOrderSearchMultiGrid'), 'n');
        // 통합 검색
        $this->search['combineSearch'] = [
            'o.orderNo' => __('주문번호'),
            'og.invoiceNo' => __('송장번호'),
            'og.goodsNm' => __('상품명'),
            'og.goodsNo' => __('상품코드'),
            'og.goodsCd' => __('자체 상품코드'),
            'og.goodsModelNo' => __('상품모델명'),
            'og.makerNm' => __('제조사'),
            '__disable1' =>'==========',
            'oi.orderName' => __('주문자명'),
            'oi.orderPhone' => __('주문자 전화번호'),
            'oi.orderCellPhone' => __('주문자 휴대폰번호'),
            'oi.orderEmail' => __('주문자 이메일'),
            'oi.receiverName' => __('수령자명'),
            'oi.receiverPhone' => __('수령자 전화번호'),
            'oi.receiverCellPhone' => __('수령자 휴대폰번호'),
            'o.bankSender' => __('입금자명'),
            '__disable2' =>'==========',
            'm.memId' => __('아이디'),
            'm.nickNm' => __('닉네임'),
            'oi.orderName' => __('주문자명'),
        ];
        if($isMultiSearch == 'y') {
            $this->search['combineSearch'] = [
                'o.orderNo' => __('주문번호'),
                'og.invoiceNo' => __('송장번호'),
                'o.bankSender' => __('입금자명'),
                'm.memId' => __('아이디'),
                'm.nickNm' => __('닉네임'),
                '__disable1' => '==========',
                'oi.orderName' => __('주문자명'),
                'oi.orderPhone' => __('주문자 전화번호'),
                'oi.orderCellPhone' => __('주문자 휴대폰번호'),
                'oi.orderEmail' => __('주문자 이메일'),
                'oi.receiverName' => __('수령자명'),
                'oi.receiverPhone' => __('수령자 전화번호'),
                'oi.receiverCellPhone' => __('수령자 휴대폰번호'),
            ];
        }

        // Like Search & Equal Search
        $this->search['searchKindArray'] = [
            'equalSearch' => __('검색어 전체일치'),
            'fullLikeSearch' => __('검색어 부분포함'),
        ];

        if(gd_is_provider() === false) {
            $this->search['combineSearch']['__disable3'] = "==========";
            $this->search['combineSearch']['sm.companyNm'] = __('고객사명');
            if (gd_is_plus_shop(PLUSSHOP_CODE_PURCHASE) === true) {
                $this->search['combineSearch']['pu.purchaseNm'] = __('매입처명');
            }
        }

        // !중요! 순서 변경시 하단의 노출항목 조절 필요
        $this->search['combineTreatDate'] = [
            'og.regDt' => __('주문일'),
            'og.paymentDt' => __('결제확인일'),
            'og.invoiceDt' => __('송장입력일'),
            'og.deliveryDt' => __('배송일'),
            'og.deliveryCompleteDt' => __('배송완료일'),
            'og.finishDt' => __('구매확정일'),
            'og.cancelDt' => __('취소완료일'),
            'oh.regDt.b' => __('반품접수일'),
            'oh.handleDt.b' => __('반품완료일'),
            'oh.regDt.e' => __('교환접수일'),
            'oh.handleDt.e' => __('교환완료일'),
            'oh.regDt.r' => __('환불접수일'),
            'oh.handleDt.r' => __('환불완료일'),
            'oi.packetCode' => __('묶음배송'),
        ];

        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }

        // --- 정렬
        $this->search['sortList'] = [
            'og.orderNo desc' => sprintf('%s↓', __('주문일')),
            'og.orderNo asc' => sprintf('%s↑', __('주문일')),
            'og.orderNo desc' => sprintf('%s↓', __('주문번호')),
            'og.orderNo asc' => sprintf('%s↑', __('주문번호')),
            'o.orderGoodsNm desc' =>sprintf('%s↓',  __('상품명')),
            'o.orderGoodsNm asc' => sprintf('%s↑', __('상품명')),
            'oi.orderName desc' => sprintf('%s↓', __('주문자')),
            'oi.orderName asc' => sprintf('%s↑', __('주문자')),
            'o.settlePrice desc' => sprintf('%s↓', __('총 결제금액')),
            'o.settlePrice asc' => sprintf('%s↑', __('총 결제금액')),
            'oi.receiverName desc' => sprintf('%s↓', __('수령자')),
            'oi.receiverName asc' => sprintf('%s↑', __('수령자')),
            'sm.companyNm desc' => sprintf('%s↓', __('공급사')),
            'sm.companyNm asc' => sprintf('%s↑', __('공급사')),
            'og.orderStatus desc' => sprintf('%s↓', __('처리상태')),
            'og.orderStatus asc' => sprintf('%s↑', __('처리상태')),
        ];

        // 상품주문번호별 탭을 제외하고는 처리상태 정렬 제거
        if ($isUserHandle === false) {
            unset($this->search['sortList']['og.orderStatus desc'], $this->search['sortList']['og.orderStatus asc']);
        }

        // 상품주문번호별 탭을 제외하고는 처리상태 정렬 제거
        if ($isUserHandle === false) {
            unset($this->search['sortList']['og.orderStatus desc'], $this->search['sortList']['og.orderStatus asc']);
        }

        // statusMode에 따른 combineTreatDate 노출항목 설정 ($this->search['combineTreatDate'] 데이터 변경되면 반드시 바껴야 함)
        if (isset($searchData['statusMode'])) {
            switch ($searchData['statusMode']) {
                case 'o':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.regDt');
                    $this->search['combineTreatDate'] = array_slice($this->search['combineTreatDate'], 0, 1);
                    break;

                case 'p':
                case 'g':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.paymentDt');
                    $this->search['combineTreatDate'] = array_slice($this->search['combineTreatDate'], 0, 2);

                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'oi.packetCode');

                    self::setAddSearchSortList(array('paymentDt', 'packetCode'));
                    break;

                case 'd1':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.deliveryDt');
                    $this->search['combineTreatDate'] = array_slice($this->search['combineTreatDate'], 0, 4);

                    self::setAddSearchSortList(array('paymentDt'));
                    break;

                case 'd2':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.deliveryCompleteDt');
                    $this->search['combineTreatDate'] = array_slice($this->search['combineTreatDate'], 0, 5);

                    self::setAddSearchSortList(array('paymentDt'));
                    break;

                case 's':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.finishDt');
                    $this->search['combineTreatDate'] = array_slice($this->search['combineTreatDate'], 0, 6);

                    self::setAddSearchSortList(array('paymentDt'));
                    break;

                case 'f':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.regDt');
                    $this->search['combineTreatDate'] = array_slice($this->search['combineTreatDate'], 0, 1);
                    break;

                case 'c':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.cancelDt');
                    $this->search['combineTreatDate'] = array_merge(array_slice($this->search['combineTreatDate'], 0, 1), array_slice($this->search['combineTreatDate'], 6, 1));
                    break;

                case 'e':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'oh.regDt.e');
                    $this->search['combineTreatDate'] = array_merge(array_slice($this->search['combineTreatDate'], 0, 6), array_slice($this->search['combineTreatDate'], 9, 2));
                    break;

                case 'b':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'oh.regDt.b');
                    $this->search['combineTreatDate'] = array_merge(array_slice($this->search['combineTreatDate'], 0, 6), array_slice($this->search['combineTreatDate'], 7, 2));
                    break;

                case 'r':
                    $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'oh.regDt.r');
                    $this->search['combineTreatDate'] = array_merge(array_slice($this->search['combineTreatDate'], 0, 6), array_slice($this->search['combineTreatDate'], 11, 2));
                    break;
            }
        }

        // 검색을 위한 bind 정보
        $fieldTypeGoods = DBTableField::getFieldTypes('tableGoods');

        // 검색기간 설정
        $data = gd_policy('order.defaultSearch');
        // CRM관리에서 주문요약 내역 90일 처리
        $thisCallController = \App::getInstance('ControllerNameResolver')->getControllerName();
        if($thisCallController == 'Controller\Admin\Share\MemberCrmController') {
            $searchPeriod = 90;
        } else {
            $searchPeriod = gd_isset($data['searchPeriod'], 6);
        }

        // --- 검색 설정
        $this->search['mallFl'] = gd_isset($searchData['mallFl'], 'all');
        $this->search['exceptOrderStatus'] = gd_isset($searchData['exceptOrderStatus']);    //예외처리할 주문상태
        $this->search['detailSearch'] = gd_isset($searchData['detailSearch']);
        $this->search['statusMode'] = gd_isset($searchData['statusMode']);
        $this->search['key'] = gd_isset($searchData['key']);
        $this->search['keyword'] = gd_isset($searchData['keyword']);
        $this->search['sort'] = gd_isset($searchData['sort']);
        $this->search['orderStatus'] = gd_isset($searchData['orderStatus']);
        $this->search['pgChargeBack'] = gd_isset($searchData['pgChargeBack']);
        $this->search['processStatus'] = gd_isset($searchData['processStatus']);
        $this->search['userHandleMode'] = gd_isset($searchData['userHandleMode']);
        $this->search['userHandleFl'] = gd_isset($searchData['userHandleFl']);
        $this->search['treatDateFl'] = gd_isset($searchData['treatDateFl'], 'og.regDt');
        $this->search['treatDate'][] = gd_isset($searchData['treatDate'][0], date('Y-m-d', strtotime('-' . $searchPeriod . ' day')));
        if($searchPeriod == '1') $this->search['treatDate'][] = gd_isset($searchData['treatDate'][1], date('Y-m-d', strtotime('-' . $searchPeriod . ' day')));
        else $this->search['treatDate'][] = gd_isset($searchData['treatDate'][1], date('Y-m-d'));
        if($searchData['treatTimeFl'] != 'y') unset($searchData['treatTime']); // 시간설정 사용 시
        $this->search['treatTime'][] = gd_isset($searchData['treatTime'][0], '00:00:00');
        $this->search['treatTime'][] = gd_isset($searchData['treatTime'][1], '23:59:59');
        $this->search['treatTimeFl'] = gd_isset($searchData['treatTimeFl'], 'n');
        $this->search['settleKind'] = gd_isset($searchData['settleKind']);
        $this->search['settlePrice'][] = gd_isset($searchData['settlePrice'][0]);
        $this->search['settlePrice'][] = gd_isset($searchData['settlePrice'][1]);
        $this->search['memFl'] = gd_isset($searchData['memFl']);
        $this->search['memberGroupNo'] = gd_isset($searchData['memberGroupNo']);
        $this->search['memberGroupNoNm'] = gd_isset($searchData['memberGroupNoNm']);
        $this->search['receiptFl'] = gd_isset($searchData['receiptFl']);
        $this->search['userHandleViewFl'] = gd_isset($searchData['userHandleViewFl']);
        $this->search['orderTypeFl'] = gd_isset($searchData['orderTypeFl']);
        $this->search['orderChannelFl'] = gd_isset($searchData['orderChannelFl']);
        $this->search['scmFl'] = gd_isset($searchData['scmFl'], 'all');
        // 공급사 선택 후 공급사가 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }
        $this->search['scmNo'] = gd_isset($searchData['scmNo']);
        $this->search['scmNoNm'] = gd_isset($searchData['scmNoNm']);
        $this->search['scmAdjustNo'] = gd_isset($searchData['scmAdjustNo']);
        $this->search['scmAdjustType'] = gd_isset($searchData['scmAdjustType']);
        $this->search['manualPayment'] = gd_isset($searchData['manualPayment'], '');
        $this->search['invoiceFl'] = gd_isset($searchData['invoiceFl'], '');
        $this->search['firstSaleFl'] = gd_isset($searchData['firstSaleFl'], 'n');
        $this->search['withGiftFl'] = gd_isset($searchData['withGiftFl'], 'n');
        $this->search['withMemoFl'] = gd_isset($searchData['withMemoFl'], 'n');
        $this->search['withAdminMemoFl'] = gd_isset($searchData['withAdminMemoFl'], 'n');
        $this->search['withPacket'] = gd_isset($searchData['withPacket'], 'n');
        $this->search['overDepositDay'] = gd_isset($searchData['overDepositDay']);
        $this->search['invoiceCompanySno'] = gd_isset($searchData['invoiceCompanySno']);
        $this->search['invoiceNoFl'] = gd_isset($searchData['invoiceNoFl']);
        $this->search['underDeliveryDay'] = gd_isset($searchData['underDeliveryDay']);
        $this->search['underDeliveryOrder'] = gd_isset($searchData['underDeliveryOrder'], 'n');
        $this->search['couponNo'] = gd_isset($searchData['couponNo']);
        $this->search['couponNoNm'] = gd_isset($searchData['couponNoNm']);
        $this->search['couponAllFl'] = gd_isset($searchData['couponAllFl']);
        $this->search['eventNo'] = gd_isset($searchData['eventNo']);
        $this->search['eventNoNm'] = gd_isset($searchData['eventNoNm']);
        $this->search['dateSearchFl'] = gd_isset($searchData['dateSearchFl'],'y');

        $this->search['purchaseNo'] = gd_isset($searchData['purchaseNo']);
        $this->search['purchaseNoNm'] = gd_isset($searchData['purchaseNoNm']);
        $this->search['purchaseNoneFl'] = gd_isset($searchData['purchaseNoneFl']);

        $this->search['brandNoneFl'] = gd_isset($searchData['brandNoneFl']);
        $this->search['brand'] =ArrayUtils::last(gd_isset($searchData['brand']));
        $this->search['brandCd'] = gd_isset($searchData['brandCd']);
        $this->search['brandCdNm'] = gd_isset($searchData['brandCdNm']);
        $this->search['orderNo'] = gd_isset($searchData['orderNo']);
        $this->search['orderMemoCd'] = gd_isset($searchData['orderMemoCd']);

        $this->search['goodsNo'] = gd_isset($searchData['goodsNo']);
        $this->search['goodsText'] = gd_isset($searchData['goodsText']);
        $this->search['goodsKey'] = gd_isset($searchData['goodsKey']);

        // --- 검색 종류 설정 (Like Or Equal)
        $this->search['searchKind'] = gd_isset($searchData['searchKind']);

        $orderBasic = gd_policy('order.basic');
        if (($orderBasic['userHandleAdmFl'] == 'y' && $orderBasic['userHandleScmFl'] == 'y') === false) {
            unset($orderBasic['userHandleScmFl']);
        }
        $userHandleUsePage = ['order_list_all.php', 'order_list_pay.php', 'order_list_goods.php', 'order_list_delivery.php', 'order_list_delivery_ok.php'];
        if ($orderBasic['userHandleFl'] == 'y' && in_array(Request::getFileUri(), $userHandleUsePage) === true && (!Manager::isProvider() && $orderBasic['userHandleAdmFl'] == 'y') || (Manager::isProvider() && $orderBasic['userHandleScmFl'] == 'y')) {
            $this->search['userHandleAdmFl'] = 'y';
        }

        
        //튜닝 추가
        if(empty($searchData['memNo'])){
            if($isMultiSearch == 'y') {
                if (DateTimeUtils::intervalDay($this->search['treatDate'][0], $this->search['treatDate'][1]) > 180) {
                    throw new AlertBackException(__('6개월이상 기간으로 검색하실 수 없습니다.'));
                }
            } else {
                if (DateTimeUtils::intervalDay($this->search['treatDate'][0], $this->search['treatDate'][1]) > 730 ) {
                    throw new AlertBackException(__('2년이상 기간으로 검색하실 수 없습니다.'));
                }
            }
        }


        // 주문/주문상품 탭 설정
        if (in_array($searchData['statusMode'], ['','o'])) {
            $this->search['view'] = gd_isset($searchData['view'], 'order');
        } elseif (in_array(substr($searchData['statusMode'], 0, 1), ['p','g','d','s'])) {
            $this->search['view'] = gd_isset($searchData['view'], 'orderGoodsSimple');
        } else {
            $this->search['view'] = gd_isset($searchData['view'], 'orderGoods');
        }

        // CRM
        $this->search['memNo'] = gd_isset($searchData['memNo'], null);

        // --- 검색 설정
        $this->checked['treatTimeFl'][$this->search['treatTimeFl']]  =
        $this->checked['purchaseNoneFl'][$this->search['purchaseNoneFl']]  =
        $this->checked['mallFl'][$this->search['mallFl']] =
        $this->checked['scmFl'][$this->search['scmFl']] =
        $this->checked['memFl'][$this->search['memFl']] =
        $this->checked['manualPayment'][$this->search['manualPayment']] =
        $this->checked['firstSaleFl'][$this->search['firstSaleFl']] =
        $this->checked['withGiftFl'][$this->search['withGiftFl']] =
        $this->checked['withMemoFl'][$this->search['withMemoFl']] =
        $this->checked['withAdminMemoFl'][$this->search['withAdminMemoFl']] =
        $this->checked['withPacket'][$this->search['withPacket']] =
        $this->checked['underDeliveryOrder'][$this->search['underDeliveryOrder']] =
        $this->checked['invoiceNoFl'][$this->search['invoiceNoFl']] =
        $this->checked['brandNoneFl'][$this->search['brandNoneFl']] =
        $this->checked['couponAllFl'][$this->search['couponAllFl']] =
        $this->checked['receiptFl'][$this->search['receiptFl']] =
        $this->checked['memoType'][$this->search['memoType']] =
        $this->checked['userHandleViewFl'][$this->search['userHandleViewFl']] = 'checked="checked"';
        $this->checked['periodFl'][$searchPeriod] = 'active';

        // --- 검색 종류 설정 (Like Or Equal)
        if ($this->search['searchKind'] && in_array($this->search['key'], $this->changeSearchKind)) {
            $this->setKeySearchType($this->search['key'], $this->search['searchKind']);
        }

        if ($this->search['orderNo'] !== null) {
            $this->arrWhere[] = 'o.orderNo = ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['orderNo']);
        }

        // 회원 주문인 경우 (CRM 주문조회)
        if ($this->search['memNo'] !== null) {
            $this->arrWhere[] = 'o.memNo = ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['memNo']);
        }

        // 주문 상태 모드가 있는 경우
        if ($this->search['statusMode'] !== null) {
            $tmp = explode(',', $this->search['statusMode']);
            foreach ($tmp as $val) {
                $sameOrderStatus = $this->getOrderStatusList($val, null, null, 'orderList');
                $sameOrderStatus = array_keys($sameOrderStatus);
                $sameOrderStatusCount = count($sameOrderStatus);
                if ($sameOrderStatusCount > 1) {
                    $tmpbind = array_fill(0, $sameOrderStatusCount, '?');
                    $tmpWhere[] = 'og.orderStatus IN (' . implode(',', $tmpbind). ')';
                    foreach ($sameOrderStatus as $valStatus) {
                        $this->db->bind_param_push($this->arrBind, 's', $valStatus);
                    }
                } else {
                    $tmpWhere[] = 'og.orderStatus = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $sameOrderStatus[0]);
                }
            }
            $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            unset($tmpWhere);
        } else {
            // 결제시도 무조건 제거
            $this->arrWhere[] = 'og.orderStatus != ?';
            $this->db->bind_param_push($this->arrBind, 's', 'f1');
        }

        if ($this->search['exceptOrderStatus']) { //예외처리할 주문상태 쿼리
            $exceptStatusQuery = implode("','", $this->search['exceptOrderStatus']);
            $this->arrWhere[] = "og.orderStatus NOT IN ('" . $exceptStatusQuery . "')";
        }

        // 수동입금확인 체크
        if ($this->search['manualPayment'] == 'y') {
            $this->arrWhere[] = 'o.settleKind = ? AND og.paymentDt != \'0000-00-00 00:00:00\' AND (o.orderAdminLog NOT LIKE concat(\'%\',?,\'%\'))';
            $this->db->bind_param_push($this->arrBind, 's', 'gb');
            $this->db->bind_param_push($this->arrBind, 's', BankdaOrder::BANK_AUTO_DEPOSIT);
        }

        // 멀티상점 선택
        if ($this->search['mallFl'] !== 'all') {
            $this->arrWhere[] = 'o.mallSno = ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['mallFl']);
        }

        // 공급사 선택
        if (Manager::isProvider()) {
            // 공급사로 로그인한 경우 기존 scm에 값 설정
            $this->arrWhere[] = 'og.scmNo = ' . Session::get('manager.scmNo');
            // 공급사에서는 입금대기 상태와 취소상태가 보여지면 안된다.
            $excludeStatusCode = ['o', 'c', 'f'];
            $arrWhereOrderStatusArray = $this->getExcludeOrderStatus($this->orderStatus, $excludeStatusCode);
            $this->arrWhere[] = 'og.orderStatus IN (\'' . implode('\',\'', array_keys($arrWhereOrderStatusArray)) . '\')';
            unset($arrWhereOrderStatusArray);
        } else {
            if ($this->search['scmFl'] == '1') {
                if (is_array($this->search['scmNo'])) {
                    foreach ($this->search['scmNo'] as $val) {
                        $tmpWhere[] = 'og.scmNo = ?';
                        $this->db->bind_param_push($this->arrBind, 's', $val);
                    }
                    $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
                    unset($tmpWhere);
                } else if ($this->search['scmNo'] > 1) {
                    $this->arrWhere[] = 'og.scmNo = ?';
                    $this->db->bind_param_push($this->arrBind, 'i', $this->search['scmNo']);
                }
            } elseif ($this->search['scmFl'] == '0') {
                $this->arrWhere[] = 'og.scmNo = 1';
            }
        }

        // 상품 검색
        if ($this->search['goodsNo']) {
            $this->arrWhere[] = 'og.goodsNo = ?';
            $this->db->bind_param_push($this->arrBind, 'i', $this->search['goodsNo']);
        } else if($this->search['goodsText']) {
            $goodsKey = $this->search['goodsKey'];
            if($goodsKey == 'og.goodsNm') {
                $this->arrWhere[] = 'og.goodsNm LIKE concat(\'%\',?,\'%\')';
            } else {
                $this->arrWhere[] = $goodsKey . ' = ?';
            }
            $this->db->bind_param_push($this->arrBind, 's', $this->search['goodsText']);
        }

        // 키워드 검색
        if ($this->search['key'] && $this->search['keyword']) {
            $keyword = $this->search['keyword'];
            if($isMultiSearch == 'y') {
                $useNaverPay = $this->getNaverPayConfig('useYn') == 'y';
                foreach($keyword as $keywordKey => $keywordVal) {
                    if($keywordVal) {
                        if(in_array($this->search['key'][$keywordKey], ['m.memId', 'm.nickNm'])) $this->multiSearchMemberJoinFl = true;
                        if(in_array($this->search['key'][$keywordKey], ['pu.purchaseNm'])) $this->multiSearchPurchaseJoinFl = true;
                        if(in_array($this->search['key'][$keywordKey], ['sm.companyNm'])) $this->multiSearchScmJoinFl = true;
                        $keywordVal = explode(',', preg_replace('{(?:\r\n|\r|\n)}', ",", $keywordVal));
                        $_keyword = $_naverPayKeyword = $_naverPayVal = [];
                        foreach($keywordVal as $keywordVal2) {
                            $keywordVal2 = trim($keywordVal2);
                            if(count($_keyword) >= 10 || empty($keywordVal2)) continue;
                            if (strpos($this->search['key'][$keywordKey], 'Phone') !== false) {
                                $keywordVal2 = StringUtils::numberToPhone(str_replace('-', '', $keywordVal2), true);
                            }
                            $_keyword[] = '?';
                            $this->db->bind_param_push($this->arrBind, 's', $keywordVal2);
                            if ($this->search['key'][$keywordKey] == 'o.orderNo' && $useNaverPay) { //네이버페이 사용할경우 네이버페이 주문번호도 추가 검색
                                $_naverPayKeyword[] = '?';
                                $_naverPayVal[] = $keywordVal2;
                            }
                        }
                        if($_keyword) $keywordWhere[] = $this->search['key'][$keywordKey]." in (" . implode(",", $_keyword) . ")";
                        if($useNaverPay && count($_naverPayVal) > 0) {
                            $keywordWhere[] = "o.apiOrderNo in (" . implode(",", $_naverPayKeyword) . ")";
                            foreach($_naverPayVal as $_naverPayVal2) {
                                $this->db->bind_param_push($this->arrBind, 's', $_naverPayVal2);
                            }
                        }
                        unset($_keyword, $_naverPayKeyword, $_naverPayVal);
                    }
                }
                if($keywordWhere) $this->arrWhere[] = '(' . implode(' OR ', $keywordWhere) . ')';
            } else {
                if(is_array($this->search['key'])) $this->search['key'] = $this->search['key'][0];
                if(is_array($this->search['keyword'])) $this->search['keyword'] = $this->search['keyword'][0];
                if ($this->search['key'] == 'all') {
                    $tmpWhere = array_keys($this->search['combineSearch']);
                    if ($this->getNaverPayConfig('useYn') == 'y') {    //네이버페이 사용할경우 네이버페이 주문번호도 추가 검색
                        $tmpWhere[] = 'o.apiOrderNo';
                    }
                    array_shift($tmpWhere);
                    $arrWhereAll = [];
                    foreach ($tmpWhere as $keyNm) {
                        // 전화번호인 경우 -(하이픈)이 없어도 검색되도록 처리
                        if (strpos($keyNm, 'Phone') !== false) {
                            $keyword = StringUtils::numberToPhone($keyword, true);
                        } else {
                            $keyword = $this->search['keyword'];
                        }
                        $searchType = $this->getKeySearchType($keyNm);
                        if ($searchType == 'fullLikeSearch') {
                            $arrWhereAll[] = '(' . $keyNm . ' LIKE concat(\'%\',?,\'%\'))';
                        } else if ($searchType == 'equalSearch') {
                            $arrWhereAll[] = '(' . $keyNm . ' = ? )';
                        } else if ($searchType == 'endLikeSearch') {
                            $arrWhereAll[] = '(' . $keyNm . ' LIKE concat(?,\'%\'))';
                        } else {
                            $arrWhereAll[] = '(' . $keyNm . ' LIKE concat(\'%\',?,\'%\'))';
                        }
                        $this->db->bind_param_push($this->arrBind, 's', $keyword);
                    }
                    $this->arrWhere[] = '(' . implode(' OR ', $arrWhereAll) . ')';
                    unset($tmpWhere);
                } else {
                    if ($this->search['key'] == 'o.orderNo') {    //네이버페이 사용중이고 주문번호 단일 검색일 경우 주문번호는 equal 검색
                        if ($this->getNaverPayConfig('useYn') == 'y') {
                            $this->arrWhere[] = '(' . $this->search['key'] . ' = ? OR apiOrderNo = ? )';
                            $this->db->bind_param_push($this->arrBind, 's', $keyword);
                        } else {
                            $this->arrWhere[] = $this->search['key'] . ' = ?';
                        }
                    } else {
                        $searchType = $this->getKeySearchType($this->search['key']);
                        if ($searchType == 'fullLikeSearch') {
                            $this->arrWhere[] = '(' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\'))';
                        } else if ($searchType == 'equalSearch') {
                            $this->arrWhere[] = '(' . $this->search['key'] . ' = ?)';
                        } else if ($searchType == 'endLikeSearch') {
                            $this->arrWhere[] = '(' . $this->search['key'] . ' LIKE concat(?,\'%\'))';
                        } else {
                            $this->arrWhere[] = '(' . $this->search['key'] . ' LIKE concat(\'%\',?,\'%\'))';
                        }
                    }

                    // 전화번호인 경우 -(하이픈)이 없어도 검색되도록 처리
                    if (strpos($this->search['key'], 'Phone') !== false) {
                        $keyword = StringUtils::numberToPhone($keyword, true);
                    } else {
                        $keyword = $this->search['keyword'];
                    }
                    $this->db->bind_param_push($this->arrBind, 's', $keyword);
                }
            }
        }

        // 주문유형
        if ($this->search['orderTypeFl'][0]) {
            $orderTypeMobileAll = false; // 모바일 전체 검색 여부(WEB / APP)
            foreach ($this->search['orderTypeFl'] as $val) {
                $this->checked['orderTypeFl'][$val] = 'checked="checked"';

                // 모바일 (WEB / APP) 주문유형 검색 추가
                if (in_array('mobile', $this->search['orderTypeFl'])) {
                    $orderTypeMobileAll = true;
                }

                if ($orderTypeMobileAll === false) {
                    switch ($val) {
                        case 'mobile-web':
                            $val = 'mobile';
                            $this->arrWhere[] = 'o.appOs  = ""';
                            break;
                        case 'mobile-app':
                            $val = 'mobile';
                            $this->arrWhere[] = '(o.appOs  != "" OR o.pushCode != "")';
                            break;
                    }
                }
                $tmpWhere[] = 'o.orderTypeFl = ?';
                $this->db->bind_param_push($this->arrBind, 's', $val);
            }
            $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            unset($tmpWhere);
        } else {
            $this->checked['orderTypeFl'][''] = 'checked="checked"';
        }

        // 주문채널
        if ($this->search['orderChannelFl'][0]) {
            foreach ($this->search['orderChannelFl'] as $val) {
                if ($val == 'paycoShopping') {
                    $tmpWhere[] = "o.trackingKey <> ''";
                } else {
                    $tmpWhere[] = 'o.orderChannelFl = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $val);
                }
                $this->checked['orderChannelFl'][$val] = 'checked="checked"';
            }
            $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            unset($tmpWhere);
        } else {
            $this->checked['orderChannelFl'][''] = 'checked="checked"';
        }

        // 반품/교환/환불신청 처리상태
        if ($isUserHandle) {
            $orderUserHandleSort = [];
            $orderUserHandleSort['ouh.regDt desc'] = sprintf('%s↓', __('신청일'));
            $orderUserHandleSort['ouh.regDt asc'] = sprintf('%s↑', __('신청일'));
            $this->search['sortList'] = ArrayUtils::insertArrayByPosition($this->search['sortList'], $orderUserHandleSort, 2, true);
            $orderUserHandleCombineTreatDate = [];
            $orderUserHandleCombineTreatDate['ouh.regDt'] = __('신청일');
            $this->search['combineTreatDate'] = ArrayUtils::insertArrayByPosition(   $this->search['combineTreatDate'], $orderUserHandleCombineTreatDate, 1, true);

            // 필수 조건으로 반품/교환/환불신청 건만 출력하도록 설정
            $this->arrWhere[] = 'og.userHandleSno > 0';

            // 반품/교환/환불신청 모드가 있는 경우만 출력
            if ($this->search['userHandleMode'] != null) {
                $this->arrWhere[] = 'ouh.userHandleMode = ?';
                $this->db->bind_param_push($this->arrBind, 's', $this->search['userHandleMode']);
                unset($tmpWhere);
            }

            // 검색 조건에 따른 출력
            if ($this->search['userHandleFl'][0]) {
                foreach ($this->search['userHandleFl'] as $val) {
                    $tmpWhere[] = 'ouh.userHandleFl = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $val);
                    $this->checked['userHandleFl'][$val] = 'checked="checked"';
                }
                $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
                unset($tmpWhere);
            } else {
                $this->checked['userHandleFl'][''] = 'checked="checked"';
            }
        }

        // 주문상태
        if ($this->search['orderStatus'][0]) {
            foreach ($this->search['orderStatus'] as $val) {
                // 주문번호별/상품주문번호별 검색조건중 주문상태의 여부에 따라 검색설정 저장이 오작동하는 이슈가 있어 프론트에는 노출되지 않지만 hidden필드로 처리해서 임의로 작동되게 처리 함
                if ($this->search['view'] === 'orderGoods' || $this->search['statusMode'] !== null) {
                    $tmpWhere[] = 'og.orderStatus = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $val);
                }
                $this->checked['orderStatus'][$val] = 'checked="checked"';
            }
            if ($this->search['view'] === 'orderGoods' || $this->search['statusMode'] !== null) {
                $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            }
            unset($tmpWhere);
        } else {
            $this->checked['orderStatus'][''] = 'checked="checked"';
        }

        // 차지백 서비스건만 검색
        if ($this->search['pgChargeBack']) {
            $this->arrWhere[] = ' o.pgChargeBack=? ';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['pgChargeBack']);
            $this->checked['pgChargeBack'][$this->search['pgChargeBack']] = 'checked="checked"';
        }

        // 처리일자 검색
        if ($this->search['dateSearchFl'] =='y' && $this->search['treatDateFl'] && isset($searchPeriod) && $searchPeriod != -1 && $this->search['treatDate'][0] && $this->search['treatDate'][1]) {
            switch (substr($this->search['treatDateFl'], -2)) {
                case '.b':
                case '.e':
                case '.r':
                    $this->arrWhere[] = ' oh.handleMode=? ';
                    $this->db->bind_param_push($this->arrBind, 's', substr($this->search['treatDateFl'], -1));
                    break;
            }
            $dateField = str_replace(['Dt.r', 'Dt.b', 'Dt.e'], 'Dt', $this->search['treatDateFl']);

            $this->arrWhere[] = $dateField . ' BETWEEN ? AND ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['treatDate'][0] . ' ' .$this->search['treatTime'][0]);
            $this->db->bind_param_push($this->arrBind, 's', $this->search['treatDate'][1] . ' ' .$this->search['treatTime'][1]);
        }

        // 결제 방법
        if ($this->search['settleKind'][0]) {
            foreach ($this->search['settleKind'] as $val) {
                if ($val == self::SETTLE_KIND_DEPOSIT) {
                    $tmpWhere[] = 'o.useDeposit > 0';
                } elseif ($val == self::SETTLE_KIND_MILEAGE) {
                    $tmpWhere[] = 'o.useMileage > 0';
                } else {
                    $tmpWhere[] = 'o.settleKind = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $val);
                }
                $this->checked['settleKind'][$val] = 'checked="checked"';
            }
            $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
            unset($tmpWhere);
        } else {
            $this->checked['settleKind'][''] = 'checked="checked"';
        }

        // 결제금액 검색
        if ($this->search['settlePrice'][1]) {
            //            $this->arrWhere[] = '(((og.goodsPrice + og.optionPrice + og.optionTextPrice ) * og.goodsCnt) + og.addGoodsPrice - og.memberDcPrice - og.memberOverlapDcPrice - og.couponGoodsDcPrice - og.divisionUseDeposit - og.divisionUseMileage - og.divisionGoodsDeliveryUseDeposit - og.divisionGoodsDeliveryUseMileage + od.deliveryCharge) BETWEEN ? AND ?';
            $this->arrWhere[] = '(o.settlePrice BETWEEN ? AND ?)';
            $this->db->bind_param_push($this->arrBind, 'i', $this->search['settlePrice'][0]);
            $this->db->bind_param_push($this->arrBind, 'i', $this->search['settlePrice'][1]);
        }

        // 회원여부 및 그룹별 검색
        if ($this->search['memFl']) {
            if ($this->search['memFl'] == 'y') {
                // 회원그룹선택
                if (is_array($this->search['memberGroupNo'])) {
                    foreach ($this->search['memberGroupNo'] as $val) {
                        $tmpWhere[] = 'm.groupSno = ?';
                        $this->db->bind_param_push($this->arrBind, 's', $val);
                    }
                    $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
                    unset($tmpWhere);
                } else if ($this->search['memberGroupNo'] > 1) {
                    $this->arrWhere[] = 'm.groupSno = ?';
                    $this->db->bind_param_push($this->arrBind, 'i', $this->search['memberGroupNo']);
                }

                // 회원만
                $this->arrWhere[] = 'o.memNo > 0';
            } elseif ($this->search['memFl'] == 'n') {
                $this->arrWhere[] = 'o.memNo = 0';
            }
        }

        // 첫주문 검색
        if ($this->search['firstSaleFl'] == 'y') {
            $this->arrWhere[] = 'o.firstSaleFl = ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['firstSaleFl']);
        }

        // 영수증 검색
        if ($this->search['receiptFl']) {
            $this->arrWhere[] = 'o.receiptFl = ?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['receiptFl']);
        }

        // 배송정보 검색 (사은품 포함)
        if ($this->search['withGiftFl'] == 'y') {
            $this->arrWhere[] = '(SELECT COUNT(sno) FROM ' . DB_ORDER_GIFT . ' WHERE orderNo = og.orderNo) > 0';
        }

        // 배송정보 검색 (배송메시지 입력)
        if ($this->search['withMemoFl'] == 'y') {
            $this->arrWhere[] = 'oi.orderMemo != \'\'';
        }

        // 상품º주문번호별 메모 (관리자 메모 입력)
        if ($this->search['withAdminMemoFl'] == 'y') {
            if($this->search['orderMemoCd']){
                $this->arrWhere[] = 'aogm.memoCd=? AND aogm.delFl = \'n\'';
                $this->db->bind_param_push($this->arrBind, 's', $this->search['orderMemoCd']);
            }else{
                $this->arrWhere[] = 'aogm.orderNo != \'\' AND aogm.delFl = \'n\'';
            }
            //$this->arrWhere[] = 'o.adminMemo != \'\'';
        }

        // 배송정보 검색 (묶음배송)
        if ($this->search['withPacket'] == 'y') {
            $this->arrWhere[] = 'oi.packetCode != \'\'';
        }

        // 입금경과일
        if ($this->search['overDepositDay'] > 0) {
            $this->arrWhere[] = 'og.orderStatus = \'o1\' AND og.regDt < ?';
            $this->db->bind_param_push($this->arrBind, 's', date('Y-m-d', strtotime('-' . $this->search['overDepositDay'] . ' day')) . ' 00:00:00');
        }

        // 배송지연일
        if ($this->search['underDeliveryDay'] > 0) {
            $includeStatusCode = ['p', 'g'];
            $arrWhereOrderStatusArray = $this->getIncludeOrderStatus($this->orderStatus, $includeStatusCode);
            $this->arrWhere[] = 'og.orderStatus IN (\'' . implode('\',\'', array_keys($arrWhereOrderStatusArray)) . '\') AND og.paymentDt < ?';
            $this->db->bind_param_push($this->arrBind, 's', date('Y-m-d', strtotime('-' . $this->search['underDeliveryDay'] . ' day')) . ' 00:00:00');
            unset($arrWhereOrderStatusArray);

            // 주문상태 체크하기
            unset($this->checked['orderStatus']);
            $this->checked['orderStatus']['p1'] =
            $this->checked['orderStatus']['g1'] =
            $this->checked['orderStatus']['g2'] =
            $this->checked['orderStatus']['g3'] =
            $this->checked['orderStatus']['g4'] =
                'checked="checked"';

            //TODO 추후 주문단위 리스트 생기면 작업?
            if ($this->search['underDeliveryOrder'] == 'y') {}
        }

        // 송장번호 검색
        if ($this->search['invoiceCompanySno'] > 0) {
            $this->arrWhere[] = 'og.invoiceCompanySno=?';
            $this->db->bind_param_push($this->arrBind, 's', $this->search['invoiceCompanySno']);
        }

        // 송장번호 유무 체크
        if ($this->search['invoiceNoFl'] === 'y') {
            $this->arrWhere[] = 'og.invoiceNo<>\'\'';
        } elseif ($this->search['invoiceNoFl'] === 'n') {
            $this->arrWhere[] = 'og.invoiceNo=\'\'';
        }

        if($this->search['couponAllFl'] === 'y'){
            //쿠폰사용 주문 전체 검색
            $this->arrWhere[] = '(o.totalCouponGoodsDcPrice > 0 OR o.totalCouponOrderDcPrice > 0 OR o.totalCouponDeliveryDcPrice > 0)';
        }
        else {
            // 쿠폰 검색
            if ($this->search['couponNo'] > 0) {
                $this->arrWhere[] = 'mc.couponNo=?';
                $this->db->bind_param_push($this->arrBind, 's', $this->search['couponNo']);
            }
        }

        // 공급사 정산 검색
        if ($this->search['scmAdjustNo']) {
            if ($this->search['scmAdjustType'] == 'oa') {
                $this->arrWhere[] = 'og.scmAdjustAfterNo = ?';
                $this->db->bind_param_push($this->arrBind, 'i', $this->search['scmAdjustNo']);
            } else if ($this->search['scmAdjustType'] == 'o') {
                $this->arrWhere[] = 'og.scmAdjustNo = ?';
                $this->db->bind_param_push($this->arrBind, 'i', $this->search['scmAdjustNo']);
            } else if ($this->search['scmAdjustType'] == 'da') {
                $this->arrWhere[] = 'od.scmAdjustAfterNo = ?';
                $this->db->bind_param_push($this->arrBind, 'i', $this->search['scmAdjustNo']);
            } else if ($this->search['scmAdjustType'] == 'd') {
                $this->arrWhere[] = 'od.scmAdjustNo = ?';
                $this->db->bind_param_push($this->arrBind, 'i', $this->search['scmAdjustNo']);
            }
        }

        // 배송 검색
        if ($this->search['invoiceFl']) {
            if ($this->search['invoiceFl'] == 'y') $this->arrWhere[] = 'og.invoiceNo !=""';
            else if ($this->search['invoiceFl'] == 'n') $this->arrWhere[] = 'og.invoiceNo =""';
            else $this->arrWhere[] = 'TRIM(oi.receiverCellPhone) NOT REGEXP \'^([0-9]{3,4})-?([0-9]{3,4})-?([0-9]{4})$\'';

            $this->checked['invoiceFl'][$this->search['invoiceFl']] = 'checked="checked"';
        } else {
            $this->checked['invoiceFl'][''] = 'checked="checked"';
        }

        // 매입처 검색
        if (($this->search['purchaseNo'] && $this->search['purchaseNoNm'])) {
            if (is_array($this->search['purchaseNo'])) {
                foreach ($this->search['purchaseNo'] as $val) {
                    $tmpWhere[] = 'og.purchaseNo = ?';
                    $this->db->bind_param_push($this->arrBind, 's', $val);
                }
                $this->arrWhere[] = '(' . implode(' OR ', $tmpWhere) . ')';
                unset($tmpWhere);
            }
        }

        //매입처 미지정
        if ($this->search['purchaseNoneFl']) {
            $this->arrWhere[] = '(og.purchaseNo IS NULL OR og.purchaseNo  = "" OR og.purchaseNo  <= 0)';
        }

        // 브랜드 검색
        if (($this->search['brandCd'] && $this->search['brandCdNm']) || $this->search['brand']) {
            if (!$this->search['brandCd'] && $this->search['brand'])
                $this->search['brandCd'] = $this->search['brand'];
            $this->arrWhere[] = 'g.brandCd = ?';
            $this->db->bind_param_push($this->arrBind, $fieldTypeGoods['brandCd'], $this->search['brandCd']);
        }
        else {
            $this->search['brandCd'] = '';
        }

        //브랜드 미지정
        if ($this->search['brandNoneFl']) {
            $this->arrWhere[] = 'g.brandCd  = ""';
        }

        if (empty($this->arrBind)) {
            $this->arrBind = null;
        }

        $this->search['combineTreatDate']['sorder.reqDeliveryDt'] = '배송요청일';
        $this->search['combineSearch']['oi.receiverAddress'] = '주소';
        $this->search['combineSearch']['oi.receiverAddressSub'] = '주소상세';
        //$this->search['orderAcctStatus'] = gd_isset($searchData['orderAcctStatus']);
        //gd_debug( $this->search );
        $this->search['orderAcctStatus'] = gd_isset($searchData['orderAcctStatus'], ''); //this설정
        $this->checked['orderAcctStatus'][$this->search['orderAcctStatus']] = 'checked="checked"';
    }

}
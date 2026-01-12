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
use Component\Database\DBTableField;
use Component\Delivery\OverseasDelivery;
use Component\Deposit\Deposit;
use Component\ExchangeRate\ExchangeRate;
use Component\Godo\NaverPayAPI;
use Component\Goods\KakaoAlimStock;
use Component\Goods\MailStock;
use Component\Goods\SmsStock;
use Component\Mail\MailAutoObserver;
use Component\Mail\MailMimeAuto;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Member\Util\MemberUtil;
use Component\Mileage\Mileage;
use Component\Naver\NaverPay;
use Component\Policy\Policy;
use Component\Scm\ScmAsianaCodeMap;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Sms\SmsAutoObserver;
use Component\Validator\Validator;
use Encryptor;
use Exception;
use Framework\Application\Bootstrap\Log;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Helper\MallHelper;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\UrlUtils;
use Globals;
use Logger;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Framework\Utility\KafkaUtils;

/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class Order extends \Bundle\Component\Order\Order{

    private $orderService;
    private $goodsStockService;
    private $deliveryCompanyMap;

    public function __construct(){
        $this->orderService = SlLoader::cLoad('Order','OrderService');
        $this->goodsStockService = SlLoader::cLoad('Goods','GoodsStock');
        $this->deliveryCompanyMap = SlCommonUtil::getDeliveryCompanyMap();
        parent::__construct();
    }

    /**
     * 상품정보 저장
     * @param $cartInfo
     * @param $orderInfo
     * @param $orderPrice
     * @param bool $checkSumData
     * @return
     * @throws Exception
     */
    public function saveOrderInfo($cartInfo, $orderInfo, $orderPrice, $checkSumData = true){

        //주문 저장처리
        $saveOrderInfoData = parent::saveOrderInfo($cartInfo, $orderInfo, $orderPrice, $checkSumData);

        if( !empty(\Session::get('member.memId')) ){

            //SitelabLogger::logger("===========> 할인정책 저장 (상품 추가시 여기 타는지 여부 확인) <=============");
            // POST 데이터 수신 (할인정책 적용)
            $orderNo = $this->orderNo;
            $postValue = Request::post()->toArray();

            foreach($postValue['customDcInfo'] as $goodsNo => $optionInfo){
                foreach($optionInfo as $optionSno => $dcInfo){
                    $saveData['goodsNo']   = $goodsNo;
                    $saveData['optionNo'] = $this->orderService->getOptionNoByOptionSno($optionSno);;
                    $saveData['memNo']     = Session::get('member.memNo'); // ?
                    $saveData['orderNo']   = $orderNo;
                    $saveData['orderGoodsSno']   = $this->orderService->getOrderGoodsSno($orderNo,$goodsNo,$optionSno);
                    $saveData['freeDcCount']     = $dcInfo['freeDcCount'];
                    $saveData['freeDcAmount']    = $dcInfo['freeDcAmount'];
                    $saveData['companyPayment']  = $dcInfo['companyPayment'];
                    $saveData['buyerPayment']    = $dcInfo['buyerPayment'];
                    $saveData['policyInfo']      = $dcInfo['policyInfo'];
                    $saveData['cancelReason'] = 1;
                    //SitelabLogger::logger($saveData);
                    $this->orderService->saveOrderGoodsPolicy($saveData);
                }
            }

            $this->orderService->saveOrderScm($orderNo, $postValue); //주문별 공급사 저장
            $this->orderService->saveOrderAcct($orderNo, $postValue); //주문별 승인상태 정보 저장
            $this->orderService->saveOrderAttachedFile($orderNo); //첨부파일 저장

            //오픈패키지 회원이 구매하면 일반 회원 등급으로 변경
            $memberService = SlLoader::cLoad('godo','memberService','sl');
            $memberData = DBUtil2::getOne(DB_MEMBER, 'memNo', \Session::get('member.memNo'));
            if( SlCodeMap::OPEN_PACKAGE_GRADE == $memberData['groupSno'] && !$memberService->isHankookManager(\Session::get('member.memId')) ){ //한국타이어 마스터 아이디는 오픈 패키지로 변경하지 않는다.
                DBUtil2::update(DB_MEMBER, ['groupSno' => SlCodeMap::GENERAL_GRADE ], new SearchVo('memNo=?', \Session::get('member.memNo') ) ) ;
            }

            //주문 후 안전재고 체크
            $this->checkOrderGoodsSafeCnt($orderNo);

            $this->orderService->setOrderTax($orderNo); //Tax 이상점 변경.

            $kepidService = SlLoader::cLoad('scm','scmKepidService');
            $kepidService->setDangJinVest($orderNo); //당진 주문 처리.

        }

        return $saveOrderInfoData;
    }

    /**
     * 재고차감
     * @param $orderNo
     * @param $arrGoodsSno
     * @return array
     * @throws Exception
     */
    public function setGoodsStockCutback($orderNo, $arrGoodsSno){
        $setInfo['addReason'] = '0';
        $setInfo['minusReason'] = '6';
        $setInfo['orderNo'] = $orderNo;
        $setInfo['arrGoodsSno'] = $arrGoodsSno;

        //특정상품(아시아나, 무재한이지만 재고는 있는 것은 차감하도록 업데이트처리후 완료되면 다시 업데이트)
        $beforeOrderGoodsList = $this->orderService->getOrderGoodsAndGoodsOption($orderNo);
        foreach($beforeOrderGoodsList as $key => $orderGoods){
            if( in_array($orderGoods['goodsNo'] , ScmAsianaCodeMap::UNLIMIT_GOODS) ){
                DBUtil2::runSql("update es_goods set stockFl = 'y' where goodsNo = {$orderGoods['goodsNo']} ");
                //SitelabLogger::logger2(__METHOD__, "아시아나 무한정 재고처리 시작 : {$orderGoods['goodsNo']}");
            }
        }
        //부모 함수 수행
        $stockSetResult = parent::setGoodsStockCutback($setInfo['orderNo'], $setInfo['arrGoodsSno']);
        foreach($beforeOrderGoodsList as $key => $orderGoods){
            if( in_array($orderGoods['goodsNo'] , ScmAsianaCodeMap::UNLIMIT_GOODS) ){
                DBUtil2::runSql("update es_goods set stockFl = 'n' where goodsNo = {$orderGoods['goodsNo']} ");
                //SitelabLogger::logger2(__METHOD__, "아시아나 무한정 재고처리 완료 : {$orderGoods['goodsNo']}");
            }
        }

        return $stockSetResult;
    }

    /**
     * 재고복원
     * @param $orderNo
     * @param $arrGoodsSno
     * @return bool
     */
    public function setGoodsStockRestore($orderNo, $arrGoodsSno){
        //SitelabLogger::logger("=============== REQUEST CHECK ===============");
        //SitelabLogger::logger(Request::request());
        //$request = Request::request();
        $setInfo['addReason'] = '0';
        $setInfo['minusReason'] = '0';
        $setInfo['orderNo'] = $orderNo;
        $setInfo['arrGoodsSno'] = $arrGoodsSno;
        //재고 복원

        //특정상품(아시아나, 무재한이지만 재고는 있는 것은 차감하도록 업데이트처리후 완료되면 다시 업데이트)
        $beforeOrderGoodsList = $this->orderService->getOrderGoodsAndGoodsOption($orderNo);
        foreach($beforeOrderGoodsList as $key => $orderGoods){
            if( in_array($orderGoods['goodsNo'] , ScmAsianaCodeMap::UNLIMIT_GOODS) ){
                DBUtil2::runSql("update es_goods set stockFl = 'y' where goodsNo = {$orderGoods['goodsNo']} ");
                //SitelabLogger::logger2(__METHOD__, "아시아나 무한정 재고처리 시작 : {$orderGoods['goodsNo']}");
            }
        }
        //부모 함수 수행
        $stockSetResult = parent::setGoodsStockRestore($setInfo['orderNo'], $setInfo['arrGoodsSno']);
        foreach($beforeOrderGoodsList as $key => $orderGoods){
            if( in_array($orderGoods['goodsNo'] , ScmAsianaCodeMap::UNLIMIT_GOODS) ){
                DBUtil2::runSql("update es_goods set stockFl = 'n' where goodsNo = {$orderGoods['goodsNo']} ");
                //SitelabLogger::logger2(__METHOD__, "아시아나 무한정 재고처리 완료 : {$orderGoods['goodsNo']}");
            }
        }
        return $stockSetResult;
    }

    /**
     * 출고불가로 인한 재고 복원
     * @param $orderNo
     * @param $arrGoodsSno
     * @return mixed
     * @throws Exception
     */
    public function setGoodsStockRestoreByOrderDenied($orderNo, $arrGoodsSno){
        //SitelabLogger::logger("=============== REQUEST CHECK ===============");
        //SitelabLogger::logger(Request::request());
        //$request = Request::request();
        $setInfo['addReason'] = '12';
        $setInfo['minusReason'] = '0';
        $setInfo['orderNo'] = $orderNo;
        $setInfo['arrGoodsSno'] = $arrGoodsSno;
        //재고 복원
        $result = $this->setGoodsStockProc($setInfo,'setGoodsStockRestore');
        return $result;
    }

    /**
     * 재고 변경 사항 처리
     * @param $setInfo
     * @param $funcName
     * @return mixed
     * @throws Exception
     */
    public function setGoodsStockProc($setInfo,$funcName){
        $orderNo = $setInfo['orderNo'];

        //특정상품(아시아나, 무재한이지만 재고는 있는 것은 차감하도록 업데이트처리후 완료되면 다시 업데이트)
        $beforeOrderGoodsList = $this->orderService->getOrderGoodsAndGoodsOption($orderNo);
        foreach($beforeOrderGoodsList as $key => $orderGoods){
            if( in_array($orderGoods['goodsNo'] , ScmAsianaCodeMap::UNLIMIT_GOODS) ){
                DBUtil2::runSql("update es_goods set stockFl = 'y' where goodsNo = {$orderGoods['goodsNo']} ");
                //SitelabLogger::logger2(__METHOD__, "아시아나 무한정 재고처리 시작 : {$orderGoods['goodsNo']}");
            }
        }
        //부모 함수 수행
        $stockSetResult = parent::$funcName($setInfo['orderNo'], $setInfo['arrGoodsSno']);
        foreach($beforeOrderGoodsList as $key => $orderGoods){
            if( in_array($orderGoods['goodsNo'] , ScmAsianaCodeMap::UNLIMIT_GOODS) ){
                DBUtil2::runSql("update es_goods set stockFl = 'n' where goodsNo = {$orderGoods['goodsNo']} ");
                //SitelabLogger::logger2(__METHOD__, "아시아나 무한정 재고처리 완료 : {$orderGoods['goodsNo']}");
            }
        }
        return $stockSetResult;
    }

    /**
     * 주문 후 상품 안전재고 체크
     * @param $orderNo
     */
    public function checkOrderGoodsSafeCnt($orderNo){
        $afterOrderGoodsList = $this->orderService->getOrderGoodsAndGoodsOption($orderNo);
        //안전재고 체크 대상 저장
        $goodsInfo = array();
        $checkSafeGoodsCntTargetList = array();
        $lastOrderGoodsRegDt = '';
        $orderGoodsNm = '';
        $safeCntCheckGoodsIdx = 0;
        $scmNo = 0;
        foreach($afterOrderGoodsList as $key => $orderGoods){
            $goodsNo = $orderGoods['goodsNo'];
            $optionNo = $orderGoods['optionNo'];
            //안전재고 체크 대상
            $stockCheckGoodsOption[$orderGoods['goodsNo']][] = $orderGoods['optionNo'];
            //안전재고 체크를 위함
            if( empty($goodsInfo[$goodsNo]) ){
                $goodsInfo[$goodsNo] = DBUtil::getOne(DB_GOODS,'goodsNo',$goodsNo);
            }
            if( 'y' ===  $goodsInfo[$goodsNo]['stockFl']  ){
                //상품+옵션 번호로 안전재고 및 재고 가져오기
                $stockCnt = DBUtil::getOne(DB_GOODS_OPTION,['goodsNo','optionNo'],[$goodsNo,$optionNo])['stockCnt'];
                $safeCnt = DBUtil::getOne('sl_goodsSafeStock',['goodsNo','optionNo'],[$goodsNo,$optionNo])['safeCnt'];
                if(  empty($checkSafeGoodsCntTargetList[$goodsNo]) &&  $safeCnt > $stockCnt && !empty($safeCnt)  ){
                    $scmNo = $orderGoods['scmNo'];
                    $checkSafeGoodsCntTargetList[$goodsNo] = $goodsNo;
                    $lastOrderGoodsRegDt = $orderGoods['regDt'];
                    if( empty($orderGoodsNm) ){
                        $orderGoodsNm = $goodsInfo[$goodsNo]['goodsNm'];
                    }else{
                        $safeCntCheckGoodsIdx++;
                    }
                }
            }
        }
        $mailData['orderDt'] = $lastOrderGoodsRegDt;
        if( $safeCntCheckGoodsIdx > 0 ){
            $mailData['orderGoodsNm'] = $orderGoodsNm  .' 외 ' . $safeCntCheckGoodsIdx . '건';
        }else{
            $mailData['orderGoodsNm'] = $orderGoodsNm;
        }
        $mailData['scmNo'] = $scmNo;
        if( count( $checkSafeGoodsCntTargetList ) > 0 ){
            $this->goodsStockService->checkAndMailSendGoodsStock($checkSafeGoodsCntTargetList, $goodsInfo, $mailData);
        }
    }

    /**
     * 배송 중, 배송 완료 상태인 상품를 카운트해서 2개 이상이면 true 아니면 false 을 넣어서 반환, 교환,환불,반품 신청 할 경우 제외
     * @param array $orderData 주문 정보
     * @return array $orderData
     */
    public function getOrderSettleButton($orderData){
        $result = parent::getOrderSettleButton($orderData);

        $canList[] = 'canRefund';
        $canList[] = 'canExchange';
        $canList[] = 'canBack';
        //gd_debug($result);

        foreach($result as $key => $value1){
            $orderCanButton = array();
            foreach($value1['goods'] as $orderGoodsKey => $orderGoodsData){
                foreach( $canList as $canValue  ){
                    if(!empty($orderGoodsData[$canValue])){
                        $orderCanButton[$canValue] = 1;
                    }
                }
            }
            //gd_debug($value1['orderNo']);
            //gd_debug($orderCanButton);
            $result[$key]['orderCanButton'] = $orderCanButton;
        }


        return $result;
    }

    /**
     * 마이페이지 > 주문리스트에서 사용하며, 회원/비회원 데이터를 모두 출력해준다.
     * @param int     $pageNum    페이지당 출력할 갯수
     * @param null    $dates      시작날짜와 끝날짜의 배열
     * @param boolean $statusMode 취소상태 표기 여부
     *
     * @return string
     * @throws AlertRedirectException
     */
    public function getOrderList($pageNum = 10, $dates = null, $statusMode = 'order'){
        $scmHankookService = SlLoader::cLoad('scm','scmHankookService');
        $memberService = SlLoader::cLoad('godo','memberService','sl');
        $isHankookManager = $memberService->isHankookManager(\Session::get('member.memId'));

        if( $isHankookManager ){
            $orgOrderList = $scmHankookService->getHankookMasterOrderList($pageNum, $dates, $statusMode);
        }else{

            $memberConfig = DBUtil2::getOne('sl_setMemberConfig', 'memNo', \Session::get('member.memNo'));

            if(!empty($memberConfig['teamName']) && 'y' === $memberConfig['repFl'] ){
                $tkeService = SlLoader::cLoad('scm','scmTkeService');
                $orgOrderList = $tkeService->getTkeOrderList($pageNum, $dates, $statusMode, $memberConfig);
            }else{
                $orgOrderList = parent::getOrderList($pageNum, $dates, $statusMode);
            }
        }

        //gd_debug($orgOrderList);
        foreach($orgOrderList as $key => $orderData){
            $orderNo = $orderData['orderNo'];
            $orderAcctData = $this->addOrderAcctData($orderNo);
            $orgOrderList[$key]['orderAcctClass']  = $orderAcctData['orderAcctClass'];
            $orgOrderList[$key]['orderAcctStr']  = $orderAcctData['orderAcctStr'];
            $orgOrderList[$key]['acctDt']  = substr($orderAcctData['acctDt'],0,10);
            $orgOrderList[$key]['orderAcct']  = $orderAcctData['orderAcct'];

            if( $isHankookManager ){ //$orderNo ,  $memNo
                $addedData = $scmHankookService->setOrderAddedData($orderNo, $orderData['goods'][0]['memNo']);
                $orgOrderList[$key]['orderAddedData']  = $addedData;
            }

            $orgOrderList[$key]['goods'] = SlCommonUtil::setEachData($orgOrderList[$key]['goods'], $this, 'setOrderGoodsEachAddData');

            $orgOrderList[$key]['asianaInfo'] = $this->addAsianaOrderData($orgOrderList[$key]['goods']);
            //gd_debug( $orgOrderList[$key]['asianaInfo'] );
        }

        foreach($orgOrderList as $orderKey => $orderEach){
            $orderEach['myOrderStatus'] = DBUtil2::getOne(DB_ORDER,'orderNo',$orderEach['orderNo'])['orderStatus'];
            $orderEach['myOrderStatusKr'] = SlCommonUtil::getOrderStatusName2($orderEach['myOrderStatus']);
            $orgOrderList[$orderKey]= $orderEach;
        }

        return $orgOrderList;
    }

    /**
     * 주문 상세정보
     *
     * @param string $orderNo 주문 번호
     *
     * @return array 주문 상세정보
     * @throws Exception
     */
    public function getOrderView($orderNo){
        $orderViewData = parent::getOrderView($orderNo);
        $orderAcctData = $this->addOrderAcctData($orderNo);
        $orderViewData['orderAcctClass']  = $orderAcctData['orderAcctClass'];
        $orderViewData['orderAcctStr']  = $orderAcctData['orderAcctStr'];
        $orderViewData['acctDt']  = substr($orderAcctData['acctDt'],0,10);
        $orderViewData['orderAcct']  = $orderAcctData['orderAcct'];

        $orderService = SlLoader::cLoad('order','orderService');
        $paymentsHistory = $orderService->getPaymentsHistory($orderNo, true);
        $orderViewData['paymentsHistory'] = $paymentsHistory;

        $orderViewData['goods'] = SlCommonUtil::setEachData($orderViewData['goods'], $this, 'setOrderGoodsEachAddData');
        $orderViewData['asianaInfo'] = $this->addAsianaOrderData($orderViewData['goods']);

        return $orderViewData;
    }

    public function addAsianaOrderData($goodsList){
        $asianaOrderMap = [];
        $availStatus = ['s','d','p','g','o'];
        foreach($goodsList as $goods){
            $statusPrefix = substr($goods['orderStatus'],0,1);
            if(in_array($statusPrefix, $availStatus)){
                $goodsData = $goods['asiaInfo'];
                //gd_debug($goods['asiaInfo']);
                $key = $goodsData['companyId'].' '.$goodsData['name'];
                $key2 = $goodsData['prdName'].' '.$goodsData['prdOption'];
                $asianaOrderMap[$key][$key2] += $goodsData['orderCnt'];
            }
        }
        return $asianaOrderMap;
    }

    /**
     * 주문 상품 개별 설정
     * @param $each
     * @param $key
     * @return mixed
     */
    public function setOrderGoodsEachAddData($each, $key){
        $each['invoiceCompanyName'] = $this->deliveryCompanyMap[$each['invoiceCompanySno']];
        if(34 == $each['scmNo']){
            $asiaInfo = DBUtil2::getOne('sl_asianaOrderHistory', 'orderGoodsSno', $each['sno']);
            $each['asiaInfo'] = $asiaInfo;
        }
        return $each;
    }

    /**
     * 주문 정보 확인
     *
     * @param integer $orderNo 주문 번호
     *
     * @return array
     * @throws Exception
     */
    public function getOrderDataInfo($orderNo){
        $memberService = SlLoader::cLoad('godo','memberService','sl');
        $isHankookManager = $memberService->isHankookManager(\Session::get('member.memId'));
        $isHyundaeManager = $memberService->isHyundaeManager(\Session::get('member.memId'));

        if( $isHankookManager || $isHyundaeManager ){
            $scmHankookService = SlLoader::cLoad('scm','scmHankookService');
            $orderViewData = $scmHankookService->getHankookMasterOrderDataInfo($orderNo);
            $orderViewData['orderAddedData']  = $scmHankookService->setOrderAddedData($orderNo, $orderViewData['goods'][0]['memNo']);
        }else{
            $orderViewData = parent::getOrderDataInfo($orderNo);
        }
        return $orderViewData;
    }

    public function addOrderAcctData($orderNo){
        $orderAcctData = array();
        $scmNo = $this->orderService->getOrderScm($orderNo)['scmNo'];
        //if(  !empty( SlCodeMap::SCM_USE_ORDER_ACCEPT_[$scmNo])  ){
        if(  SlCommonUtil::getIsOrderAccept($scmNo) ){
            $acceptData = $this->orderService->getOrderAcceptData($orderNo);
            $orderAcctStatus = $acceptData['orderAcctStatus'];
            $orderAcctData['orderAcctClass'] = SlCodeMap::ORDER_ACCT_STATUS_LABEL_COLOR[$orderAcctStatus];
            $orderAcctData['orderAcctStr'] = SlCodeMap::ORDER_ACCT_STATUS[$orderAcctStatus];
            $orderAcctData['orderAcct'] = $orderAcctStatus;
            $orderAcctData['acctDt'] = $acceptData['acctDt'];
        }
        return $orderAcctData;
    }

    /**
     * 사용자 클레임 신청(환불/교환/반품)이 있을경우 주문리스트를 가공하여 리턴
     *
     * @param array   $arrData   주문리스트
     * @param string  $mode      모드
     *
     * @return mixed
     */
    public function getOrderClaimList($arrData, $mode = null){
        if(strpos(Request::getFullFileUri(), 'mypage') !== false) {
            //mypage에서만 실행
            $orderData = $this->getOrderClaimListForMypage($arrData, $mode);
        }else{
            $orderData = parent::getOrderClaimList($arrData, $mode);
        }

        return $orderData;
    }

    public function getOrderClaimListForMypage($arrData, $mode = null){
        $attachedOrderGoods = array();
        $goodsSnoList = array();
        $includeOrderStatus = 'p,g,d,s'; //입금, 결제, 준비, 배송, 확정
        if( empty($mode) ){
            $includeOrderStatus.=',o';
        }

        //교환추가 상품
        foreach( $arrData['goods'] as $key => $value){
            if( 'z' ===  $value['handleMode']  && (strpos($includeOrderStatus, substr($value['orderStatus'],0,1)) !== false)  ){
                //각 상태(결제, 배송, 구매확정) 에 맞게 처리하기
                $value['canBack'] = true;
                $value['canRefund'] = true;
                $value['canExchange'] = true;
                $value['handleSno'] = 0;
                $attachedOrderGoods[$value['sno']] = $value;
                //gd_debug($value['sno']);
            }
        }

        $orderData = parent::getOrderClaimList($arrData, $mode);

        $newOrderGoodsList = array();
        $i=1;
        foreach($orderData[0]['goods'] as $goodsDataKey => $goodsDataValue){
            if(strpos($includeOrderStatus, substr($goodsDataValue['orderStatus'],0,1)) !== false) {
                //주문상태에 따라 표시할 상품만 나오게 한다.
                $newOrderGoodsList[$i++] = $goodsDataValue;
                $goodsSnoList[] = $goodsDataValue['sno'];
            }
        }

        //제거되었던 상품 다시 붙이기
        //gd_debug($attachedOrderGoods);
        foreach($attachedOrderGoods as $aOrderGoodsKey => $aOrderGoodsValue){
            $goodsSnoListStr = implode(',',$goodsSnoList);
            if( strpos($goodsSnoListStr, $aOrderGoodsKey.'') === false ) {
                $newOrderGoodsList[] = $aOrderGoodsValue;
            }
        }

        //새로운 상품 리스트로 교체
        $orderData[0]['goods'] = $newOrderGoodsList;

        foreach($orderData as $orderKey => $orderEach){
            $orderEach['myOrderStatus'] = DBUtil2::getOne(DB_ORDER,'orderNo',$orderEach['orderNo'])['orderStatus'];
            $orderEach['myOrderStatusKr'] = SlCommonUtil::getOrderStatusName2($orderEach['myOrderStatus']);
            $orderData[$orderKey]= $orderEach;
        }

        return $orderData;
    }

    /**
     * 상태 수정 - 배송
     * 배송 상태의 처리사항 (마일리지/쿠폰 적립 , 재고차감, 에스크로 배송등록, 현금영수증 자동발행)
     *
     * @param string $orderNo 주문 번호
     * @param array  $arrData 상태 정보
     * @param boolean $statusChange 상태 수정여부
     * @param string $useVisit 방문수령여부
     */
    public function statusChangeCodeD($orderNo, $arrData, $statusChange = true, $useVisit = null)
    {
        parent::statusChangeCodeD($orderNo, $arrData, $statusChange, $useVisit);

        if ($arrData['changeStatus'] === 'd2') {   // SMS 전송 (배송 완료시)
            //배송완료는 한국타이어만 발송(중단:이유미22/11/01) , TKE-배송완료시 자동 발송(22/11/01추가)

            //$memberOrderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
            //$memInfo = DBUtil::getOne('es_member','memNo',$memberOrderData['memNo']);
            //$scmInfo = DBUtil::getOne('es_scmManage','companyNm',$memInfo['ex1']);

            /*
            if( '6' == $scmInfo['scmNo'] ){ //SCM이 한국타이어 일때만.
                $orderService = SlLoader::cLoad('Order','OrderService');
                $orderService->sendOrderMsg(6, $orderNo);
                //SitelabLogger::logger('## 배송완료시 리서치 문자 발송 :  ' . $orderNo);
            }
            */
            if( '8' == $scmInfo['scmNo'] ) { //배송완료시 SCM이 TKE 일때만 나가게 처리.
                //$orderService = SlLoader::cLoad('Order','OrderService');
                //$orderService->sendOrderMsg(6, $orderNo);
                //SitelabLogger::logger('## 배송완료시 리서치 문자 발송 :  ' . $orderNo);
            }
        }
    }

    /**
     *
     * 주문서 저장시 처리 .
     *
     * @param mixed $cartSno 장바구니 SNO
     * @param mixed $orderNo 업데이트 할 주문번호
     *
     * @throws Exception
     * @author Jong-tae Ahn <qnibus@godo.co.kr>
     */
    public function updateCartWithOrderNo($cartSno, $orderNo){
        $requestParam = \Request::request()->toArray();

        //아시아나일 경우 처리 .
        $asianaService = SlLoader::cLoad('scm','ScmAsianaService');

        $scmNo = MemberUtil::getMemberScmNo();
        if(34 == $scmNo){
            if( empty($requestParam['companyId']) && empty($requestParam['empName']) ){
                throw new Exception('사번이나 이름은 필수로 입력 바랍니다.');
            }else{
                //아시아나 주문 기록  (연결성 확보)
                $sql = "
                    select a.*, b.goodsNm, c.optionValue1 
                    from es_orderGoods a 
                    join es_goods b on a.goodsNo=b.goodsNo
                    join es_goodsOption c on a.optionSno=c.sno
                    where orderNo={$orderNo}";
                $orderGoodsList = DBUtil2::runSelect($sql);
                foreach($orderGoodsList as $orderGoods){
                    $asianaOrder = [];
                    $asianaOrder['memNo'] = \Session::get('member.memNo');
                    $asianaOrder['companyId'] = $requestParam['companyId'];
                    $asianaOrder['requestDt'] = date('Ymd');
                    $asianaOrder['prdName'] = $orderGoods['goodsNm'];
                    $asianaOrder['prdOption'] = $orderGoods['optionValue1'];
                    $asianaOrder['orderGoodsSno'] = $orderGoods['sno'];
                    $asianaOrder['optionInfo'] = $orderGoods['optionInfo'];
                    $asianaOrder['optionSno'] = $orderGoods['optionSno'];
                    $asianaOrder['orderCnt'] = $orderGoods['goodsCnt'];
                    //이름 정의 ( 사번이 없으면 리퀘스트로 , 사번이 있으면 조회 해서 등록 )
                    if(empty($requestParam['companyId'])){
                        $asianaOrder['name'] = $requestParam['empName'];
                    }else{
                        $empInfo = DBUtil2::getOne('sl_asianaEmployee', 'companyId', $requestParam['companyId']);
                        $asianaOrder['name'] = $empInfo['empName'];
                        $asianaOrder['empTeam'] = $empInfo['empTeam'];
                        $asianaOrder['empPart1'] = $empInfo['empPart1'];
                        $asianaOrder['empPart2'] = $empInfo['empPart2'];
                    }
                    DBUtil2::insert('sl_asianaOrderHistory', $asianaOrder);
                    $asianaService->saveEmpAllHistory($requestParam['companyId']);
                }
            }
        }

        parent::updateCartWithOrderNo($cartSno, $orderNo);

        //비회원은 PASS
        if( !empty(\Session::get('member.memId')) ){

            $memberService = SlLoader::cLoad('godo','memberService','sl');
            $isHankookManager = $memberService->isHankookManager(\Session::get('member.memId'));

            if($isHankookManager){
                $scmHankookService = SlLoader::cLoad('scm','scmHankookService');
                $scmHankookService->setHankookMasterOrderV2($orderNo);
            }

            $isHyundaeManager = $memberService->isHyundaeManager(\Session::get('member.memId'));
            if($isHyundaeManager){
                $scmHyndaeService = SlLoader::cLoad('scm','scmHyundaeService');
                $scmHyndaeService->setHyundaeMasterOrder($orderNo);
            }

            //TKE 주문시 필요 정보 추가 등록.
            //$scmHankookService = SlLoader::cLoad('scm','scmHankookService');
            //$scmHankookService->setTkeOrder($orderNo);

            //결제 주문서 처리.
            $orderGoods = DBUtil2::getOne(DB_ORDER_GOODS, 'orderNo', $orderNo);
            $goodsInfo = DBUtil2::getOne(DB_GOODS,'goodsNo', $orderGoods['goodsNo']);
            if( SlCommonUtil::isDev() ){
                $cateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY_DEV;
            }else{
                $cateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY;
            }
            if( $cateCd === $goodsInfo['cateCd'] ){
                //SitelabLogger::logger('RUN... proc Payment Save Order After... : ' . $orderNo . ' // ' . $goodsInfo['goodsNo'] );
                $this->orderService->procPaymentSaveOrderAfter($orderNo, $goodsInfo['goodsNo']);
            }


            if( !empty($requestParam['deliveryList']) ){
                $data = DBUtil2::getOne('sl_orderSelectedDeliveryName','orderNo',$orderNo);
                if(empty($data)){
                    DBUtil2::insert('sl_orderSelectedDeliveryName', [
                        'orderNo' => $orderNo,
                        'orderDeliveryName' => $requestParam['deliveryList'],
                    ]);
                }
            }

        }


    }


}

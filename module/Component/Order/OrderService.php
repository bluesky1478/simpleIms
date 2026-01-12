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
use Component\Scm\ScmKepidService;
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
use Component\Storage\Storage;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;
use SlComponent\Util\SlSmsUtil;


/**
 * 주문 서비스
 */
class OrderService{

    private $sql;
    private $goodsPolicyService;

    public function __construct(){
        $this->sql = \App::load(\Component\Order\Sql\OrderServiceSql::class);
        $this->goodsPolicyService = \App::load(\Component\Goods\GoodsPolicy::class);
        //$this->order = \App::load('\\Component\\Order\\Order');
    }

    /**
     * 주문상품 일련번호 가져오기
     * @param $orderNo
     * @param $goodsNo
     * @param $optionSno
     * @return mixed
     */
    public function getOrderGoodsSno($orderNo,$goodsNo,$optionSno){
        return $this->sql->getOrderGoodsSno($orderNo,$goodsNo,$optionSno);
    }

    /**
     * 상품옵션시퀀스로 상품 번호 (인덱스?) 가져오기
     * @param $optionSno
     * @return mixed
     */
    public function getOptionNoByOptionSno($optionSno){
        return DBUtil::getOne(DB_GOODS_OPTION,'sno',$optionSno)['optionNo'];
    }

    /**
     * orderNo로 memNo 반환
     * @param $orderNo
     * @return mixed
     */
    public function getMemNoByOrderNo($orderNo){
        return DBUtil::getOne(DB_ORDER,'orderNo',$orderNo)['memNo'];
    }

    /**
     * 주문에 적용된 정책 가져오기
     * @param $orderNo
     * @param $orderGoodsSno
     * @return mixed
     */
    public function getOrderGoodsPolicyApplyInfo($orderNo, $orderGoodsSno){
        $result = $this->sql->getOrderGoodsPolicyApplyInfo($orderNo, $orderGoodsSno);
        $isEmpty = true;
        foreach($result as $val){
            if(!empty($val)) $isEmpty = false;
        }
        return  $isEmpty ? null :  $result;
    }

    /**
     * 주문 상품 정책 (history)
     * @param $saveData
     */
    public function saveOrderGoodsPolicy($saveData){
        //SitelabLogger::logger('== 주문 적용 정책 저장 ==');
        //SitelabLogger::logger($saveData);
        //$totalValue = $saveData['freeDcCount'] + $saveData['freeDcAmount'] + $saveData['companyPayment'] + $saveData['buyerPayment'];
        DBUtil::insert('sl_orderGoodsPolicy',$saveData);
    }

    public function saveOrderScm($orderNo, $postValue){
        //SitelabLogger::logger('== 주문 SCM (통계를 위함) ==');
        //SitelabLogger::logger($saveData);

        $saveOrderSCMData['orderNo'] =  $orderNo;
        $saveOrderSCMData['scmNo'] =  MemberUtil::getMemberScmNo(); //세션 없는 주문이 필요할때는 ... ?
        $saveOrderSCMData['branchDept'] =  $postValue['branchDept'];

        $orderInfo = DBUtil2::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
        $selectAddress = DBUtil2::getOne('sl_setScmDeliveryList', 'receiverAddress', $orderInfo['receiverAddress']);
        if(!empty($selectAddress)){
            $saveOrderSCMData['scmDeliverySno'] =  $selectAddress['sno'];
        }

        DBUtil::insert('sl_orderScm',$saveOrderSCMData);
    }

    /**
     * 주문상품과 주문옵션 가져오기
     * @param $orderNo
     * @return mixed
     */
    public function getOrderGoodsAndGoodsOption($orderNo){
        return $this->sql->getOrderGoodsAndGoodsOption($orderNo);
    }

    /**
     * 정책 적용된 상품 정제시키기 (취소/교환/환불등으로)
     * @param $dataParam
     * @param int $cancelReason
     */
    public function refineApplyPolicyOrderGoods($dataParam, $cancelReason = 4){

        $orderNo = $dataParam['orderNo'];

        //클레임으로 발생한 정책변동이라면 업데이트
        if( !empty($dataParam['claimSno']) ){
            $handleGroupCd = DBUtil::getMax('es_orderHandle', 'handleGroupCd', 'orderNo', $orderNo);
            //최종적으로 orderGoodsSno별로 루프 돌면서 가져온 handGroupCd 업데이트 ( to claimHistory )
            if(  !empty($handleGroupCd)  ){
                $updateParam['handleGroupCd'] = $handleGroupCd;
                $updateData = DBUtil::makeUpdateData($updateParam,'handleGroupCd');
                try {
                    DBUtil::update('sl_claimHistory', $updateData, new SearchVo('sno=?', $dataParam['claimSno']));
                } catch (Exception $e) {
                    SitelabLogger::loggerAutoDebug("sl_claimHistory 업데이트 중 오류 발생");
                    SitelabLogger::loggerAutoDebug($dataParam);
                    SitelabLogger::loggerAutoDebug($updateParam);
                }
            }
        }

        //SitelabLogger::logger('[ refineApplyPolicyOrderGoods ] -------------------------');

        $orderInfo = $this->sql->getOrderInfo($orderNo);
        $memNo = $orderInfo['memNo'];

        $orderGoodsList = $this->getOrderGoodsAndGoodsOption($orderNo);
        foreach($orderGoodsList as $key => $orderGoods){

            //$orderGoodsPayment = $orderGoods['taxSupplyGoodsPrice'] + $orderGoods['taxVatGoodsPrice'] - $orderGoods['taxFreeGoodsPrice']; // ?
            $orderGoodsPayment = $orderGoods['taxSupplyGoodsPrice'] + $orderGoods['taxVatGoodsPrice'];

            //공통 저장 정보
            $saveData['goodsNo']   = $orderGoods['goodsNo'];
            $saveData['optionNo'] = $this->getOptionNoByOptionSno($orderGoods['optionSno']);
            //$saveData['orderGoodsSno']  = $this->getOrderGoodsSno($orderNo,$orderGoods['goodsNo'],$orderGoods['optionSno']);
            $saveData['orderGoodsSno']  = $orderGoods['sno'];
            $saveData['cancelReason'] = $cancelReason;
            $saveData['orderNo']   = $orderNo;
            $saveData['memNo']     = $orderInfo['memNo'];

            //1) 주문 상품 개별 적용된 정책이 있는지 확인
            $applyPolicyInfo = $this->getOrderGoodsPolicyApplyInfo($orderNo, $orderGoods['sno']);

            /*SitelabLogger::logger('------------ OrderGoods Info ---------------');
            SitelabLogger::logger('0. claimSno : ' . $dataParam['claimSno']);
            SitelabLogger::logger('1. goodsNo : ' . $orderGoods['goodsNo']);
            SitelabLogger::logger('2. orderGoodsSno : ' . $orderGoods['orderGoodsSno']);*/

            if( !empty($applyPolicyInfo) ){

                $saveData['policyInfo']       = $applyPolicyInfo['policyInfo'];

                if(  SlCommonUtil::isCancel( $orderGoods['orderStatus']) ){
                    //정책이 있고 - 전체 환불/취소
                    $saveData['freeDcCount'] =  $applyPolicyInfo['freeDcCount'] * -1;
                    $saveData['freeDcAmount']  =  $applyPolicyInfo['freeDcAmount'] * -1;
                    $saveData['companyPayment'] =  $applyPolicyInfo['companyPayment'] * -1;
                    $saveData['buyerPayment'] =  $applyPolicyInfo['buyerPayment'] * -1;
                    $this->saveOrderGoodsPolicy($saveData);
                }else{

                    //부분취소
                    $beforePayment = $applyPolicyInfo[ 'companyPayment' ] + $applyPolicyInfo['buyerPayment'] + $applyPolicyInfo['freeDcAmount'] ;
                    $afterPayment = $orderGoods['goodsDcPrice'] + $orderGoodsPayment;

                    if( (int)$afterPayment !== (int)$beforePayment ){

                        //SitelabLogger::logger($saveData['orderGoodsSno'].'는 정책이 있지만 재계산 .');
                        //SitelabLogger::logger('Before : '.number_format($beforePayment));
                        //SitelabLogger::logger('After : '.number_format($afterPayment));

                        //변경된 policy 이력 추가
                        $saveData['freeDcCount']     = $applyPolicyInfo['freeDcCount'] * -1; //무상수량 마이너스 처리
                        $saveData['freeDcAmount']    = $applyPolicyInfo['freeDcAmount'] * -1; //무상금액 마이너스 처리 (무상은 전체 취소 전제)
                        $saveData['companyPayment']  = $orderGoods['goodsDcPrice'] - ($applyPolicyInfo['companyPayment'] + $applyPolicyInfo['freeDcAmount']);
                        $saveData['buyerPayment'] = $orderGoodsPayment - $applyPolicyInfo['buyerPayment'];
                        $this->saveOrderGoodsPolicy($saveData);
                    }
                }
            }else{

                //SitelabLogger::logger($saveData['orderGoodsSno'].'는 정책이 들어가지 않은 상품.');

                //취소상태 제외하고 정책이 없으면 추가 상품으로 할인정책 추가해준다.
                //단 회원-상품에 정책이 있을경우.
                //( f, r, c, b, e - 실패, 환불, 취소, 반품, 교환이 아니면 정책 추가 )
                if(empty(SlCodeMap::ORDER_CANCEL_REASON[substr($orderGoods['orderStatus'],0,1)])){
                    //상품 정책 가져오기
                    $unitOrderGoodsApplyPolicyInfo = $this->goodsPolicyService->calculationGoodsPolicy($orderGoods, $memNo);
                    if(!empty($unitOrderGoodsApplyPolicyInfo)){
                        $saveData['policyInfo'] = $unitOrderGoodsApplyPolicyInfo['unitPolicyInfo'];
                        $saveData['freeDcCount'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['freeDcCount'];
                        $saveData['freeDcAmount'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['freeDcAmount'];
                        $saveData['companyPayment'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['companyPayment'];
                        $saveData['buyerPayment'] = $unitOrderGoodsApplyPolicyInfo['unitCustomDcInfo']['buyerPayment'];
                        $this->saveOrderGoodsPolicy($saveData);
                    }
                }
            }
        }

        //추가 : 취소상품 정제.
        $this->syncOrderStatus($orderNo);

    }

    /**
     * 주문상품 적용 정책 한개 가져오기
     * @param $orderGoodsSno
     * @return mixed
     */
    public function getOneOrderGoodsPolicy($orderGoodsSno){
        return $this->sql->getOneOrderGoodsPolicy($orderGoodsSno);
    }

    /**
     * 회원 주문리스트 정제
     */
    public function refineOrderList($orderData){
        //order data refine
        foreach($orderData as $orderDataKey => $orderDataValue){
            $orderData[$orderDataKey]['orderGoodsCnt'] = count($orderDataValue['goods']);
         }
        //gd_debug($orderData['listMode']);
        //gd_debug($orderData[0]['orderGoodsCnt']);
        return $orderData;
    }


    /**
     * 승인대기/승인 공통 처리
     * @param $orderNo
     * @param $accpetCode
     * @return bool
     * @throws Exception
     */
    public function orderWaitAcceptCommon($orderNo, $accpetCode){
        $order = SlLoader::cLoad('order','order');
        $orderGoodsList = $this->getOrderGoodsAndGoodsOption($orderNo);
        $snoList = array();
        $notRevokeGoods = array();
        $isNotRevoke = false;

        foreach( $orderGoodsList as $key => $val  ) {
            //복원된것이 있다
            $excludeStatus = ['r','e','f']; //이 상태에 대해서는 복원 무시.
            if( 'y' == $val['minusRestoreStockFl'] && !in_array(substr($val['orderStatus'],0,1), $excludeStatus)  ){
                $optionInfo = DBUtil::getOne(DB_GOODS_OPTION, 'sno', $val['optionSno']);
                if( $optionInfo['stockCnt'] >= $val['goodsCnt'] ){
                    $snoList[] = $val['sno'];
                }else{
                    $isNotRevoke = true;
                    $notRevokeGoods[] = $val['goodsNm'] . ' ( ' . SlCommonUtil::getRefineOrderGoodsOption($val['optionInfo'])  .')';
                }
            }
        }

        if( $isNotRevoke ){
            //복원 불가상품이 있음
            $returnFlag = false;
        }else{
            $this->updateOrderAcceptStatus($orderNo, $accpetCode); //승인완료
            //승인처리하여 다시 재고 차감 (대상만)
            $order->setGoodsStockCutback($orderNo, $snoList);
            $returnFlag = true;
        }
        return $returnFlag;
    }

    /**
     * 승인대기
     * @param $param
     * @return string
     * @throws Exception
     */
    public function orderWait($param){
        $returnMsg = "주문 대기 처리 완료.";
        $isExsistsNotRevokeGoods = false;
        foreach( $param['orderNo'] as $key => $orderNo ){
            $isComplete = $this->orderWaitAcceptCommon($orderNo, '1'); //승인대기
            if( $isComplete ){
                $isExsistsNotRevokeGoods = true;
            }
        }
        if( $isExsistsNotRevokeGoods ){
            $returnMsg .= '재고가 없어 복원 불가능한 상품이 있습니다..';
        }
        return $returnMsg;
    }

    /**
     * 주문승인
     * @param $param
     * @return string
     * @throws Exception
     */
    public function orderAccept($param){
        $code = 200;
        $returnMsg = "주문 승인 처리 완료.";
        $isExsistsNotRevokeGoods = false;
        foreach( $param['orderNo'] as $key => $orderNo ){
            $isComplete = $this->orderWaitAcceptCommon($orderNo, '2'); //승인완료
            if( $isComplete ){
                //SMS 발송
                //$this->sendOrderProcSms($orderNo,0);
                //KAKAO 발송
                $orderInfo = DBUtil::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
                $param['orderName'] = $orderInfo['orderName'];
                SlKakaoUtil::send(1 , $orderInfo['orderCellPhone'] ,  $param);
            }else{
                $code = 500;
                $isExsistsNotRevokeGoods = true;
            }
        }
        if( $isExsistsNotRevokeGoods ){
            $returnMsg = '재고가 없어 승인처리 불가합니다.';
        }

        $returnValue = [
          'message' => $returnMsg,
          'code' => $code
        ];

        return $returnValue;
    }

    /**
     * 승인불가
     * @param $param
     * @return string
     * @throws Exception
     */
    public function orderDenied($param){
        $asiaService = SlLoader::cLoad('scm','ScmAsianaService');
        $order = SlLoader::cLoad('order','order');
        $cancelOrderList = []; //출고 불가는 주문 취소 처리
        foreach($param['orderNo'] as $key => $orderNo){
            $cancelOrderList[] = $orderNo;
            $this->updateOrderAcceptStatus($orderNo, '3', $param['reason']); //승인불가
            //승인 처리 후 재고 복원
            $orderGoodsList = $this->getOrderGoodsAndGoodsOption($orderNo);
            $snoList = array();

            $scmNo = 0;

            foreach( $orderGoodsList as $key => $val ) {
                $snoList[] = $val['sno'];
                $scmNo = $val['scmNo'];
            }
            //재고 복원
            $order->setGoodsStockRestoreByOrderDenied($orderNo, $snoList);

            //SMS 발송
            //$this->sendOrderProcSms($orderNo,1);
            //KAKAO 발송
            $orderInfo = DBUtil::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
            $param['orderName'] = $orderInfo['orderName'];
            if( empty($param['reason']) ){
                SlKakaoUtil::send(2 , $orderInfo['orderCellPhone'] ,  $param);
            }else{
                SlKakaoUtil::send(17 , $orderInfo['orderCellPhone'] ,  $param);
            }

            //아시아나일 경우 이력 재처리.
            if( 34 == $scmNo ){
                $companyIdList = [];
                //출고불가 이력 취소로
                foreach( $orderGoodsList as $asiaKey => $val ) {
                    $asiaHis = DBUtil2::getOne('sl_asianaOrderHistory', 'orderGoodsSno', $val['sno']);
                    $companyIdList[$asiaHis['companyId']]=true;
                    $updateRslt = DBUtil2::update('sl_asianaOrderHistory', [
                        'delFl' => 'y'
                    ], new SearchVo('sno=?',$asiaHis['sno']));
                }
                foreach($companyIdList as $companyId => $blank){
                    $asiaService->saveEmpAllHistory($companyId); //취소된 사용자 이력 갱신
                }
            }
        }

        //주문 취소 처리
        foreach($cancelOrderList as $orderNo){
            DBUtil2::update(DB_ORDER, ['orderStatus'=>'c3'], new SearchVo('orderNo=?',$orderNo));
            DBUtil2::update(DB_ORDER_GOODS, ['orderStatus'=>'c3','cancelDt'=>'now()'], new SearchVo('orderNo=?',$orderNo));
        }

        return "주문 출고 불가 처리에 성공하였습니다.";
    }

    /**
     * 주문 출고 처리에 대한 SMS 발송
     * @param $orderNo
     * @param $msgCode
     * @return mixed
     */
    public function sendOrderProcSms($orderNo, $msgCode){
        $orderInfo = DBUtil::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
        $receiverData[0]['memNo'] = '0';
        $receiverData[0]['memNm'] = $orderInfo['orderName'];
        $receiverData[0]['smsFl'] = 'y';
        $receiverData[0]['cellPhone'] = $orderInfo['orderCellPhone'];
        $content = SlSmsUtil::getSmsMsg($msgCode, $orderInfo);;
        return SlSmsUtil::sendSms($content, $receiverData, 'lms');
    }

    /**
     * 승인처리 업데이트
     * @param $orderNo
     * @param $orderAcctStatus
     * @param string $reason
     * @throws Exception
     */
    public function updateOrderAcceptStatus($orderNo, $orderAcctStatus , $reason = ''){
        $updateData['orderAcctStatus'] = $orderAcctStatus; //승인처리
        $updateData['reason'] = $reason; //승인처리

        if( '2' == $orderAcctStatus ){
            $updateData['managerSno'] = \Session::get('manager.sno'); //승인자
            $updateData['acctDt'] = SlCommonUtil::getNow(); //승인시간
        }else{
            $updateData['acctDt'] = ''; //승인시간
        }

        DBUtil::update('sl_orderAccept', $updateData, new SearchVo('orderNo=?', $orderNo));
    }

    /**
     * 주문 승인 데이터 가져오기
     * @param $orderNo
     * @return mixed
     */
    public function getOrderAcceptData($orderNo){
        return DBUtil::getOne('sl_orderAccept', 'orderNo', $orderNo);
    }

    /**
     * 주문의 공급사 정보 가져오기
     * @param $orderNo
     * @return mixed
     */
    public function getOrderScm($orderNo){
        return DBUtil::getOne('sl_orderScm','orderNo',$orderNo);
    }

    /**
     * 승인 상태 신규 저장
     * @param $orderNo
     * @param $postValue
     * @return mixed
     */
    public function saveOrderAcct($orderNo, $postValue){

        $saveOrderSCMData['orderNo'] =  $orderNo;
        $saveOrderSCMData['scmNo'] =  MemberUtil::getMemberScmNo(); //세션 없는 주문이 필요할때는 ... ?
        $saveOrderSCMData['branchDept'] =  $postValue['branchDept'];

        //if( !empty(SlCodeMap::SCM_USE_ORDER_ACCEPT_[$arrData['scmNo']]) ){
        if( SlCommonUtil::getIsOrderAccept($saveOrderSCMData['scmNo']) && !in_array(\Session::get('member.memNo'),SlCodeMap::SCM_ORDER_EXCLUDE_MEM_NO) ){
            $saveOrderSCMData['orderAcctStatus'] = '1'; //승인대기
        }else{
            $saveOrderSCMData['orderAcctStatus'] = '2'; //사용하지 않으면 바로 승인완료
        }
        return DBUtil::insert('sl_orderAccept',$saveOrderSCMData);
    }


    /**
     * 주문시 첨부 저장
     * @throws Exception
     */
    public function saveOrderAttachedFile($orderNo){

        //SitelabLogger::logger('ORDER DEBUG');
        //SitelabLogger::logger(Request::post()->toArray());

        //첨부저장
        $maxuploadSize = 5;
        $storage = Storage::disk(Storage::PATH_CODE_COMMON, 'local');    //파일저장소세팅
        $uploadPath = 'upload/order/';
        $file_array = ArrayUtils::rearrangeFileArray(Request::files()->get('upfiles'));

        if (empty($file_array) === false) {

            $fileCnt = count($file_array);
            if ($fileCnt > 10) {
                throw new \Exception(sprintf(__('업로드는 최대 %1$s 개만 지원합니다'), 10));
            }

            for ($i = 0; $i < $fileCnt; $i++) {
                if (!$file_array[$i]['name']) {
                    continue;
                }
                if ($errorCode = $file_array[$i]['error'] != UPLOAD_ERR_OK) {
                    switch ($errorCode) {
                        case UPLOAD_ERR_INI_SIZE :
                            throw new \Exception(sprintf(__('업로드 용량이 %1$s MByte(s) 를 초과했습니다.'), $maxuploadSize));
                            break;
                        default :
                            throw new \Exception(__('알수 없는 오류입니다.') . '( UPLOAD ERROR CODE : ' . $errorCode . ')');
                    }
                }
                if (is_uploaded_file($file_array[$i]['tmp_name'])) {
                    if ($maxuploadSize && $file_array[$i]['size'] > ($maxuploadSize * 1024 * 1024)) {
                        throw new \Exception(sprintf(__('업로드 용량이 %1$s MByte(s) 를 초과했습니다.'), $maxuploadSize));
                    }
                    $uploadFileNm[$i] = $file_array[$i]['name'];
                    $saveFileNm[$i] = substr(md5(microtime()), 0, 16);
                    $uploadResult = $storage->upload($file_array[$i]['tmp_name'], $uploadPath . $saveFileNm[$i]);

                    $saveData['fileName'] = $file_array[$i]['name'];
                    $saveData['fileDirPath'] = $uploadResult;
                    $saveData['orderNo'] = $orderNo;
                    DBUtil::insert('sl_orderAttFile',$saveData);
                    //SitelabLogger::logger(' Upload Result ......... ');
                    //SitelabLogger::logger($uploadResult);
                } else {
                    throw new \Exception(sprintf(__('업로드 용량이 %1$s MByte(s) 를 초과했습니다.'), ini_get('upload_max_filesize')));
                }
            }
        }

    }

    /**
     * 주문메세지 보내기
     * @param $templateId
     * @param $orderNo
     * @param bool $isOnce
     * @throws Exception
     */
    public function sendOrderMsg($templateId, $orderNo, $isOnce = true)
    {
        //이미 전송되었다면 다시 전송하지 않는다.

        $historyCount = DBUtil2::getCount('sl_orderMsgHistory', new SearchVo(['orderNo=?', 'templateId=?'], [$orderNo, $templateId]));
        $changeTemplateId = $templateId;
        $isContinue = true;

        if (0 >= $historyCount || false === $isOnce) {
            $orderData = DBUtil2::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
            $receiverPhoneNumber = str_replace('-', '', $orderData['orderCellPhone']);

            $param['orderName'] = $orderData['orderName'];
            //설문일 경우
            if(  6 == $templateId ){
                //만족도 조사 URL
                //업체별 분기로 변경
                $memberOrderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
                $memInfo = DBUtil::getOne('es_member','memNo',$memberOrderData['memNo']);
                $scmInfo = DBUtil::getOne('es_scmManage','companyNm',$memInfo['ex1']);

                $changeTemplateId = 7;
                $param['surveyUrl'] = 'https://forms.gle/NmzsDDyU8xduYzwk9';
                $param['btnUrl']  = 'https://forms.gle/NmzsDDyU8xduYzwk9';

                if( '6' == $scmInfo['scmNo'] ){
                    //한국 타이어
                    //$param['surveyUrl'] = 'https://forms.gle/swyXx8U4Z6sMLLjm7';
                    //$param['btnUrl']  = 'https://forms.gle/swyXx8U4Z6sMLLjm7';
                    //$param['surveyUrl'] = 'https://forms.gle/vS2CDNL3BssPyPby5'; //춘추
                    $param['surveyUrl'] = 'https://forms.gle/J7kwcEwpkNossWtb7'; //동계
                    $changeTemplateId = 10;

                    //특정상품의 경우 SMS로 설문 발송
                    //$scmHankookService = SlLoader::cLoad('scm','scmHankookService');
                    //$isContinue = $scmHankookService->sendHkResearch($receiverPhoneNumber, $param['orderName'], $orderNo);
                    $isContinue = false;//문자 보낼 경우 이 코드 삭제해야함. (이유미 요청으로 종료)

                }else if( '8' == $scmInfo['scmNo'] ){
                    //TKE 설문의 경우 한번만 보내게 한다.
                    $cellPhoneCount = DBUtil2::getCount('sl_researchMsgHistory', new SearchVo(['cellPhone=?'], [$receiverPhoneNumber]));
                    if( $cellPhoneCount > 0 ){
                        $isContinue = false;
                    }
                    //TKE
                    //$param['surveyUrl'] = 'https://forms.gle/Fxb4wBURrcCea2eF9';
                    //$param['btnUrl']  = 'https://forms.gle/Fxb4wBURrcCea2eF9';
                    //$param['surveyUrl'] = 'https://forms.gle/vfzgxtJJB1hswhP7A';
                    //$param['btnUrl']  = 'https://forms.gle/vfzgxtJJB1hswhP7A';
                    $param['surveyUrl'] = 'https://forms.gle/ufpsvK2WVxYrjxLH6';
                    $param['btnUrl']  = 'https://forms.gle/ufpsvK2WVxYrjxLH6';

                    //$templateId = 8;
                    $changeTemplateId = 12;
                    //춘추 동계 ? -> https://forms.gle/vfzgxtJJB1hswhP7A
                    //https://forms.gle/vfzgxtJJB1hswhP7A
                    $isContinue = false;
                }else if( '11' == $scmInfo['scmNo'] ){
                    //OTIS
                    $param['surveyUrl'] = 'https://forms.gle/9F3tuRYkzKF4JDpu6';
                    $changeTemplateId = 11;
                    //하계 : https://forms.gle/mfp3jKbh7P7xtnoY7
                    //춘추 : https://forms.gle/Wz16csHPgc5BFLDU6
                    //동계 : https://forms.gle/9F3tuRYkzKF4JDpu6
                    $isContinue = false;
                }
                $param['reserveTime'] = date('Y-m-d').' '.'18:30:00';
                $isContinue = false; //만족도 체크 X
            }

            if( $isContinue ){
                SitelabLogger::logger('## 카카오톡 알림 발송 ('. $changeTemplateId .') :  ' . $orderNo);
                SlKakaoUtil::send($changeTemplateId , $receiverPhoneNumber ,  $param);
                //주문메세지 기록
                $historyData['orderNo'] = $orderNo;
                $historyData['templateId'] = $templateId;
                DBUtil2::insert('sl_orderMsgHistory', $historyData);
                DBUtil2::insert('sl_researchMsgHistory', ['cellPhone'=>$receiverPhoneNumber]);
            }
        }
    }

    /**
     * 공유재고 데이터 전달
     * @param $openGoodsNo
     * @param $optionNo
     * @return mixed
     */
    public function getShareStockCnt($openGoodsNo, $optionNo) {
        $tableList= [
            'a' => //메인
                [
                    'data' => [ DB_GOODS_OPTION ]
                    , 'field' => ['*', 'stockCnt - b.shareNotCnt as shareTotalCnt']
                ]
            , 'b' => //작성자
                [
                    'data' => [ 'sl_goodsSafeStock', 'LEFT OUTER JOIN', 'a.goodsNo = b.goodsNo AND a.optionNo = b.optionNo' ]
                    , 'field' => ['shareNotCnt' ]
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);
        $searchVo = new SearchVo('a.goodsNo=?' , $openGoodsNo);
        if( !empty($optionNo) ){
            $searchVo->setWhere('a.optionNo=?');
            $searchVo->setWhereValue($optionNo);
        }
        $searchVo->setOrder('a.optionNo ASC, a.sno ASC');
        return DBUtil2::getComplexList($table, $searchVo);
    }


    /**
     * 주문정보 전달
     * @param $orderNo
     * @return mixed
     */
    public function getOrder($orderNo)
    {
        $orderData = DBUtil::getOne(DB_ORDER, 'orderNo', $orderNo);
        $orderData['orderStatusStr'] = SlCommonUtil::getOrderStatusName2($orderData['orderStatus']);
        $orderData['orderGoodsData'] = $this->sql->getOrderGoodsOptionList($orderNo);
        $orderInfoData = DBUtil::getOne(DB_ORDER_INFO, 'orderNo', $orderNo);
        return array_merge($orderData, $orderInfoData);
    }

    /**
     * 결제 추가
     * @param $param
     * @return mixed
     */
    public function addPayments($param)
    {
        $saveData = $param;
        //상품을 만든다.
        $goodsAdmin = SlLoader::cLoad('goods', 'goodsAdmin');
        $newGoodsNo = $goodsAdmin->createPaymentsGoods($saveData['paymentSubject'], $saveData['reqPrice']);
        $saveData['goodsNo'] = $newGoodsNo;

        $result = DBUtil::insert('sl_collectOrder', $saveData);

        if(!empty($result)){
            $orderData = DBUtil2::getOne(DB_ORDER_INFO, 'orderNo', $saveData['orderNo']);

            $contentParam['orderName'] = $orderData['orderName'];
            $contentParam['subject'] = $saveData['paymentSubject'];
            $contentParam['amount'] = number_format($saveData['reqPrice']).'원';
            $contentParam['btnUrl'] = \Request::getScheme()."://m.".\Request::getDefaultHost().'/mypage/order_list.php';
            /*$receiverData = [];
            $receiverData[0]['memNo'] = '0';
            $receiverData[0]['smsFl'] = 'y';
            $receiverData[0]['cellPhone'] = gd_isset($orderData['orderCellPhone'],$orderData['receiverCellPhone']);
            $receiverData[0]['memNm'] = $orderData['orderName'];*/
            //$content = SlSmsUtil::getSmsMsg(3, $contentParam);
            //SlSmsUtil::sendSms($content, $receiverData, 'lms');
            $cellPhone = gd_isset($orderData['orderCellPhone'],$orderData['receiverCellPhone']);
            SlKakaoUtil::send(14, $cellPhone ,  $contentParam);
        }

        return $result;
    }

    /**
     * 결제이력 가져오기
     * @param $orderNo
     * @param bool $isWithoutCancel
     * @return mixed
     */
    public function getPaymentsHistory($orderNo, $isWithoutCancel = false)
    {

        $paymentsHistory = $this->sql->getPaymentsHistory($orderNo, $isWithoutCancel);

        foreach ($paymentsHistory as $key => $value) {
            //주문상태
            if (empty($value['collectOrderNo'])) {
                $value['orderStatusStr'] = '결제주문서없음';
                $value['receiptKindStr'] = '-';
                $value['settleKindStr'] = '-';
            } else {
                $value['orderStatusStr'] = SlCommonUtil::getOrderStatusName($value['collectOrderStatus']);
                $value['receiptKindStr'] = SlCodeMap::RECEIPT_KIND[$value['receiptKind']];
                $value['settleKindStr'] = SlCommonUtil::getSettleKindName($value['settleKind']);
                $value['bankAccountInfo'] = explode('^|^', $value['bankAccount']  );
            }
            $paymentsHistory[$key] = $value;
        }
        return $paymentsHistory;
    }

    /**
     * 결제상품을 카트에 넣는다.
     * @param $goodsNo
     * @param $goodsPrice
     * @throws Exception
     */
    public function paymentGoodsToCart($goodsNo, $goodsPrice)
    {
        $cart = \App::load('\\Component\\Cart\\Cart');
        $cart->truncateDirectCart();
        DBUtil2::delete(DB_CART, new SearchVo(['goodsNo=?', 'memNo=?'], [$goodsNo, \Session::get('member.memNo')]));
        $saveData = array();
        $saveData['mallSno'] = Mall::getSession('mallSno');
        $saveData['goodsNo'] = $goodsNo;
        $saveData['optionSno'] = DBUtil2::getOne(DB_GOODS_OPTION, 'goodsNo', $goodsNo)['sno'];
        $saveData['goodsCnt'] = 1;
        $saveData['deliveryCollectFl'] = 'pre';
        $saveData['memberCouponNo'] = '';
        $saveData['tmpOrderNo'] = '';
        $saveData['printInfo'] = '';
        $saveData['scmNo'] = '1';
        $saveData['cartMode'] = 'd';
        $saveData['directOrderFl'] = 'y';
        $saveData['goodsPrice'] = $goodsPrice;
        $cart->saveGoodsToCart($saveData);
    }


    /**
     * 추가 결제 카테고리 여부 반환
     * @param $cateCd
     * @return bool
     */
    public function isPaymentCategory($cateCd){
        if( SlCommonUtil::isDev() ){
            $paymentOnlyCateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY_DEV;
        }else{
            $paymentOnlyCateCd = SlCodeMap::PRIVATE_PAYMENT_CATEGORY;
        }
        return $paymentOnlyCateCd === $cateCd;
    }

    /**
     * 주문 컨트롤러 셋팅
     * @param $controller
     */
    public function setOrderController($controller){

        $memberConfig = MemberUtil::getMemberConfig(\Session::get('member.memNo'));
        if( in_array(\Session::get('member.memId'), SlCodeMap::TKE_MANAGER_ID) || 2 == $memberConfig['memberType'] ){ //휴대전화가 안된다. (파트너)
            unset($memberConfig['teamName']);
        }else{
            $teamRepMemberConfig = DBUtil2::getOneBySearchVo('sl_setMemberConfig', new SearchVo("repFl='y' and teamName=?", $memberConfig['teamName']));
            $teamManager = DBUtil2::getOne(DB_MEMBER, 'memNo', $teamRepMemberConfig['memNo']);
            $controller->setData('teamManager', $teamManager); //memNm, cellPhone
        }

        $controller->setData('memberConfig', $memberConfig);

        $controller->setData('otherSkin', SlSkinUtil::getOtherSkinName());
        $scmService=SlLoader::cLoad('godo','scmService','sl');
        $scmService->setDeliverySelectFl(MemberUtil::getMemberScmNo(), $controller);
        $controller->setData( 'branchMap', SlCommonUtil::getBranchList());
        $controller->setData( 'hasScmGoods', false);
        $scmHankookService = SlLoader::cLoad('scm','scmHankookService');
        $scmHankookService->runMethod('setOrderControlData',['controller'=>$controller]);
        $cartInfo = $controller->getData('cartInfo');

        $isKepidGoods = false;
        $isContinue = true;
        foreach( $cartInfo as $each1 ){
            foreach( $each1 as $each2 ){
                foreach( $each2 as $each3 ){
                    //한전산업 명찰
                    if( in_array($each3['goodsNo'], ScmKepidService::NAME_PLATE_GOODS_LIST )){
                        $isKepidGoods=true;
                    }

                    if( $this->isPaymentCategory($each3['cateCd']) ){
                        $isContinue = false;
                        $controller->setData('isPaymentOnly','1');
                        $controller->getView()->setPageName('order/order_payment');

                        //최근 주문하나 불러오기
                        $memNo = \Session::get('member.memNo');
                        $orderInfo = DBUtil2::runSelect("select * from es_order a join es_orderInfo b on a.orderNo = b.orderNo where a.memNo = {$memNo} order by a.regDt desc")[0];
                        $orderData = SlCommonUtil::getAvailData($orderInfo,[
                            'receiverName',
                            'receiverZonecode',
                            'receiverZipcode',
                            'receiverAddress',
                            'receiverAddressSub',
                        ]);
                        $orderData['cellphone'] = $orderInfo['receiverCellPhone'];
                        $orderData['receiverZonecode'] = gd_isset($orderInfo['receiverZonecode'],'00000');
                        $orderData['email'] = gd_isset($orderInfo['orderEmail'],'innover_dev@msinnover.com');
                        $controller->setData('orderData',$orderData);
                        break;
                    }
                }
                if(!$isContinue) break;
            }
            if(!$isContinue) break;
        }
        $controller->setData( 'hasKepidGoods', $isKepidGoods);

        if( 21 == MemberUtil::getMemberScmNo(\Session::get('member.memNo')) ){
            $controller->setData( 'isNotFile', true);
            $controller->setData( 'isNotDc', true);
        }

        $controller->setData('imsAjaxUrl' , SlCommonUtil::getHost().'/ics/ics_ps.php');

        //오렌지티 현금영수증 강제 설정
        if( SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME] ){
            $receipt = $controller->getData('receipt');
            $receipt['aboveFl'] = 'r';
            $controller->setData('receipt', $receipt);

            if( empty(\Session::get('member.memNo')) ){
                MemberUtil::guest();
            }
            $controller->setData( 'memberScm', SlCodeMap::NO_LOGIN_VIEW_SITE[URI_HOME]['scmNo'] );
        }
        

    }

    /**
     * 고도몰 주문 저장 이후 결제관련 주문 처리
     * @param $orderNo
     * @param $paymentGoodsNo
     * @throws Exception
     */
    public function procPaymentSaveOrderAfter($orderNo, $paymentGoodsNo)
    {
        //SitelabLogger::logger(' >>>>>>>>>>>>>>>>> procPaymentSaveOrderAfter');
        //결제 주문건 정보 업데이트
        $orderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
        $collectOrder = $this->getOrderByPaymentsGoodsNo($paymentGoodsNo);
        $updateData['collectOrderNo'] = $orderNo;
        $updateData['receiptKind'] = $orderData['receiptFl'];
        //SitelabLogger::logger($updateData);
        DBUtil2::update('sl_collectOrder', $updateData, new SearchVo(['sno=?'], [$collectOrder['paymentsInfo']['sno']]));

        //결제주문건 타입 변경
        //SitelabLogger::logger(' >>> 결제 진행상태 변경');
        $orderUpdateData['userConsultMemo'] = '1';
        DBUtil2::update(DB_ORDER, $orderUpdateData, new SearchVo('orderNo=?', $orderNo)); //상품들 업데이트

        //입금대기 상태로 변경
        if ('o1' == $orderData['orderStatus']) {
            //SitelabLogger::logger(' >>> 상품 진행상태 변경');
            $orderGoodsUpdateData['orderStatus'] = 'o1';
            $orderUpdateData['orderStatus'] = 'o1';
            DBUtil2::update(DB_ORDER_GOODS, $orderGoodsUpdateData, new SearchVo('orderNo=?', $orderNo)); //상품들 업데이트
        }else if ('p1' == $orderData['orderStatus'] && 0 == $orderData['settlePrice'] ) {
            //결제 완료 처리 (전액결제)
            $this->checkPgCompleteAndSetStatus($orderNo);
        }

        DBUtil2::update(DB_ORDER, $orderUpdateData, new SearchVo('orderNo=?', $orderNo));
    }

    /**
     * 원 주문정보를 결제상품 번호로 가져오기
     * @param $goodsNo
     * @return mixed
     */
    public function getOrderByPaymentsGoodsNo($goodsNo)
    {
        $collectOrder = DBUtil2::getOne('sl_collectOrder', 'goodsNo', $goodsNo);
        $orderInfo = $this->getOrder($collectOrder['orderNo']);
        $orderInfo['paymentsInfo'] = $collectOrder;
        return $orderInfo;
    }

    /**
     * PG 결제 완료 되었을 때 처리
     * @param $collectOrderNo
     * @throws Exception
     */
    public function checkPgCompleteAndSetStatus($collectOrderNo)
    {
        //SitelabLogger::logger('checkPgCompleteAndSetStatus');
        //결제 주문서 확인후 재연결
        $paymentGoods = DBUtil2::getOne(DB_ORDER_GOODS, 'orderNo', $collectOrderNo);
        if( !empty($paymentGoods) ){
            //SitelabLogger::logger('#### checkPgCompleteAndSetStatus CHECK : ' . $collectOrderNo . ' / ' . $paymentGoods['orderStatus']);
            if ('p' == substr($paymentGoods['orderStatus'],0,1)) {
                //변경되었으면 Update , 아니면 말고.
                DBUtil2::update('sl_collectOrder', ['collectOrderNo'=>$collectOrderNo], new SearchVo(['goodsNo=?', 'collectOrderNo <> ?'], [$paymentGoods['goodsNo'], $collectOrderNo]));
            }
        }
    }

    /**
     * 박스타입 설정
     * @param $orderNo
     * @param $boxType
     * @throws Exception
     */
    public function setDeliveryBoxType($orderNo, $boxType){
        $searchVo = new SearchVo('orderNo=?', $orderNo);
        $data = DBUtil::getOneBySearchVo('sl_orderExtend', $searchVo);
        if(!empty($data)){
            DBUtil::update('sl_orderExtend', ['deliveryBoxType'=>$boxType], $searchVo );
        }else{
            DBUtil::insert('sl_orderExtend', [
                'deliveryBoxType'=>$boxType,
                'orderNo'=>$orderNo
            ] );
        }

    }


    public function getOrderDownloadData($orderNo){

        $orderData = $this->getOrder($orderNo);

        $orderPrintData['viewData'] = [];
        $totalPrice = 0;
        foreach( $orderData['orderGoodsData'] as $each ){
            //$goodsInfo = DBUtil::getOne(DB_GOODS, 'goodsNo', $each['goodsNo'] );
            $optionInfoList = json_decode($each['optionInfo'],true);

            $goodsPrice = $each['goodsPrice'] + $each['optionPrice'];
            $goodsTotalPrice = $goodsPrice * $each['goodsCnt'];

            $printData['goodsNm'] = $each['goodsNm'];
            $printData['goodsCnt'] = $each['goodsCnt'];

            $optionStrList = [];
            foreach($optionInfoList as $option){
                $optionStrList[] = $option[1];
            }
            $printData['optionNm'] = implode('/', $optionStrList);

            $printData['goodsPrice'] = number_format($goodsPrice);
            $taxPrice = NumberUtils::taxAll( $goodsTotalPrice, 10, 't');
            $printData['supplyPrice'] = number_format($taxPrice['supply']);
            $printData['vatPrice'] = number_format($taxPrice['tax']);
            $printData['totalPrice'] = number_format($goodsTotalPrice);

            $orderPrintData['viewData'][] = $printData;
            $totalPrice += $goodsTotalPrice;
        }

        //배송비
        if( $orderData['totalDeliveryCharge'] > 0 ){
            $taxPrice = NumberUtils::taxAll( $orderData['totalDeliveryCharge'], 10, 't');
            $deliveryData['supplyPrice'] = number_format($taxPrice['supply']);
            $deliveryData['vatPrice'] = number_format($taxPrice['tax']);

            $orderPrintData['viewData'][] = [
                'goodsNm' => '배송비'
                , 'optionNm' => '-'
                , 'sizeNm' => '-'
                , 'goodsCnt' => '1'
                , 'goodsPrice' => number_format($orderData['totalDeliveryCharge'])
                , 'supplyPrice' => number_format($taxPrice['supply'])
                , 'vatPrice' => number_format($taxPrice['tax'])
                , 'totalPrice' => number_format($orderData['totalDeliveryCharge'])
            ];
            $totalPrice += $orderData['totalDeliveryCharge'];
        }

        $taxPrice = NumberUtils::taxAll( $totalPrice, 10, 't');
        $orderPrintData['priceTotal'] = number_format($totalPrice);
        $orderPrintData['supplyTotal'] = number_format($taxPrice['supply']);
        $orderPrintData['vatTotal'] = number_format($taxPrice['tax']);

        $orderPrintData['tradingDate'] = gd_date_format('Y-m-d', $each['regDt']);
        //$orderPrintData['tradingDate'] = gd_date_format('Y-m-d', );

        $orderPrintData['writerNm'] = $orderData['orderName'];
        //$orderPrintData['tradingDate'] = date('Y-m-d');

        return $orderPrintData;
    }

    /**
     * 주문 상품 TAX 체크
     * @param $orderNo
     * @throws Exception
     */
    public function setOrderTax($orderNo){

        $orderData = DBUtil2::getOne(DB_ORDER, 'orderNo',$orderNo);
        $orderGoodsData = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo',$orderNo);
        //SitelabLogger::logger($orderData);
        //SitelabLogger::logger($orderGoodsData);
        $taxPrice = $orderData['taxSupplyPrice'] + $orderData['taxVatPrice'];

        if( $orderData['settlePrice'] != $taxPrice ){

            //$trunc = Globals::get('gTrunc.goods');
            $goods = SlLoader::cLoad('goods','goods');

            $refineTaxPrice = NumberUtils::taxAll($orderData['settlePrice'], 10, 't');
            DBUtil2::update(DB_ORDER, [
                'taxSupplyPrice' => $refineTaxPrice['supply'],
                'taxVatPrice' => $refineTaxPrice['tax'],
                'realTaxSupplyPrice' => $refineTaxPrice['supply'],
                'realTaxVatPrice' => $refineTaxPrice['tax'],
            ],new SearchVo('orderNo=?', $orderNo));

            //$debugOrderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
            //gd_debug($debugOrderData['taxSupplyPrice']);
            //gd_debug($debugOrderData['taxVatPrice']);

            //상품 할인 재계산.
            foreach($orderGoodsData as $orderGoods){
                $goodsNo = $orderGoods['goodsNo'];
                $getData = $goods->getGoodsInfo($goodsNo);
                //상품별할인
                if ($getData['goodsDiscountFl'] == 'y') {
                    if ($getData['goodsDiscountUnit'] == 'price') $getData['goodsDiscountPrice'] = $getData['goodsPrice'] - $getData['goodsDiscount'];
                    else $getData['goodsDiscountPrice'] = $getData['goodsPrice'] - (($getData['goodsDiscount'] / 100) * $getData['goodsPrice']);
                }
                //할인가 기본 세팅
                $memInfo = DBUtil2::getOne(DB_MEMBER,'memNo',$orderData['memNo']);

                //$getData['goodsDcPrice'] = $this->getOrderGoodsDcPrice($getData, $memInfo['groupSno'], $orderData['memNo']);
                $goodsDcPrice = $this->getOrderGoodsDcPrice($getData, $memInfo['groupSno'], $orderData['memNo']);
                $goodsDcPrice = $goodsDcPrice * $orderGoods['goodsCnt'];
                //gd_debug('상품할인 가격');
                //gd_debug($goodsDcPrice);

                $orderGoodsPrice = ($orderGoods['goodsCnt'] * $orderGoods['goodsPrice']) + ($orderGoods['goodsCnt'] * $orderGoods['optionPrice']);
                $orderGoodsPriceWithDc = $orderGoodsPrice - $goodsDcPrice;

                $refineGoodsTaxPrice = NumberUtils::taxAll($orderGoodsPriceWithDc, 10, 't');
                $updateData = [
                    'taxSupplyGoodsPrice' => $refineGoodsTaxPrice['supply'],
                    'taxVatGoodsPrice' => $refineGoodsTaxPrice['tax'],
                    'realTaxSupplyGoodsPrice' => $refineGoodsTaxPrice['supply'],
                    'realTaxVatGoodsPrice' => $refineGoodsTaxPrice['tax'],
                    'goodsDcPrice' => $goodsDcPrice,
                ];
                //gd_debug($updateData);
                DBUtil2::update(DB_ORDER_GOODS, $updateData, new SearchVo('sno=?', $orderGoods['sno']));
            }
        }
    }


    /**
     * 상품의 상품할인가 반환
     *
     * @param array $aGoodsInfo 상품정보
     * @return int 상품할인가반환
     */
    public function getOrderGoodsDcPrice($aGoodsInfo, $groupSno, $memNo)
    {
        // 상품 할인 금액
        $goodsDcPrice = 0;

        // 상품 할인을 사용하는 경우 상품 할인 계산
        if ($aGoodsInfo['goodsDiscountFl'] === 'y') {
            // 상품 할인 기준 금액 처리
            $tmp['discountByPrice'] = $aGoodsInfo['goodsPrice'];

            // 절사 내용
            $tmp['trunc'] = Globals::get('gTrunc.goods');

            switch ($aGoodsInfo['goodsDiscountGroup']) {
                case 'group':
                    $goodsDiscountGroupMemberInfoData = json_decode($aGoodsInfo['goodsDiscountGroupMemberInfo'], true);
                    $discountKey = array_flip($goodsDiscountGroupMemberInfoData['groupSno'])[$groupSno];

                    if ($discountKey >= 0) {
                        if ($goodsDiscountGroupMemberInfoData['goodsDiscountUnit'][$discountKey] === 'percent') {
                            $discountPercent = $goodsDiscountGroupMemberInfoData['goodsDiscount'][$discountKey] / 100;

                            // 상품할인금액
                            $goodsDcPrice = gd_number_figure($tmp['discountByPrice'] * $discountPercent, $tmp['trunc']['unitPrecision'], $tmp['trunc']['unitRound']);
                        } else {
                            // 상품할인금액 (정액인 경우 해당 설정된 금액으로)
                            $goodsDcPrice = $goodsDiscountGroupMemberInfoData['goodsDiscount'][$discountKey];
                        }
                    }
                    break;
                case 'member':
                default:
                    if ($aGoodsInfo['goodsDiscountUnit'] === 'percent') {
                        // 상품할인금액
                        $discountPercent = $aGoodsInfo['goodsDiscount'] / 100;
                        $goodsDcPrice = gd_number_figure($tmp['discountByPrice'] * $discountPercent, $tmp['trunc']['unitPrecision'], $tmp['trunc']['unitRound']);
                    } else {
                        // 상품할인금액 (정액인 경우 해당 설정된 금액으로)
                        $goodsDcPrice = $aGoodsInfo['goodsDiscount'];
                    }
                    if ($aGoodsInfo['goodsDiscountGroup'] == 'member' && $memNo === false) {
                        $goodsDcPrice = 0;
                    }
                    break;
            }
        }

        return $goodsDcPrice;
    }


    /**
     * 상품 수량 일괄 수정
     * @param $param
     * @param string $modifyComment
     * @return bool
     * @throws Exception
     */
    public function modifyGoodsCnt($param, $modifyComment = '수량변경'): bool
    {
        $isChange = false;
        $orderNo = $param['orderNo'];
        $orderHistorySno = $this->saveOrderChangeHistory($modifyComment, $orderNo);
        foreach ($param['param'] as $data) {
            $updateData['goodsCnt'] = (int)$data['goodsCnt'];
            $updateResult = DBUtil2::update(DB_ORDER_GOODS, $updateData, new SearchVo(['sno=?', 'goodsCnt<>?'], [$data['sno'], $data['goodsCnt']]));
            if( !empty($updateResult) ){
                $isChange = true;
            }
        }

        if( $isChange ){
            $this->reCalcOrderData($orderNo);
            $this->saveOrderChangeHistory($modifyComment, $orderNo, $orderHistorySno);
        }else{
            //수량 변경 안되었으면 변경 이력 삭제
            DBUtil2::delete('sl_orderChangeHistory', new SearchVo('sno=?', $orderHistorySno));
        }

        return true;
    }

    /**
     * 주문변경 로그 기록
     * @param $comment
     * @param $orderNo
     * @param null $updateSno
     * @return mixed|null
     * @throws Exception
     */
    public function saveOrderChangeHistory($comment, $orderNo, $updateSno = null){
        $orderData = DBUtil2::getOne(DB_ORDER, 'orderNo', $orderNo);
        $saveData['orderNo'] = $orderNo;
        $saveData['managerSno'] = Session::get('manager.sno');

        $saveField = [
            'orderStatus'
            , 'orderGoodsNm'
            , 'orderGoodsCnt'
            , 'settlePrice'
            , 'taxSupplyPrice'
            , 'taxVatPrice'
            , 'taxFreePrice'
            , 'realTaxSupplyPrice'
            , 'realTaxVatPrice'
            , 'realTaxFreePrice'
            , 'totalGoodsPrice'
            , 'totalDeliveryCharge'
            , 'paymentDt'
        ];

        $availOrderData = SlCommonUtil::getAvailData($orderData, $saveField);
        $refineOrderData = json_encode( $availOrderData );

        $orderGoodsDataList = DBUtil2::getList(DB_ORDER_GOODS, 'orderNo', $orderNo);
        $saveGoodsField = [
            'orderCd'
            , 'orderStatus'
            , 'goodsNo'
            , 'goodsCd'
            , 'goodsNm'
            , 'goodsCnt'
            , 'goodsPrice'
            , 'optionSno'
            , 'optionInfo'
        ];

        $availOrderGoodsData = [];
        foreach($orderGoodsDataList as $orderGoodsData){
            $availOrderGoodsData[] = SlCommonUtil::getAvailData($orderGoodsData, $saveGoodsField);
        }

        $refineOrderGoodsData = json_encode( $availOrderGoodsData );

        if( empty($updateSno) ){
            //이전
            $saveData['logComment'] = $comment;
            $saveData['beforeOrder'] = $refineOrderData;
            $saveData['beforeGoods'] = $refineOrderGoodsData;
            $updateSno = DBUtil2::insert('sl_orderChangeHistory' , $saveData);
        }else{
            //이후
            $saveData['logComment'] = $comment;
            $saveData['afterOrder'] = $refineOrderData;
            $saveData['afterGoods'] = $refineOrderGoodsData;
            DBUtil2::update('sl_orderChangeHistory', $saveData, DBUtil2::createSearchVo($updateSno) );
        }

        return $updateSno;
    }

    /**
     * 배송비 무료 처리
     * @param $orderNo
     * @throws Exception
     */
    public function setFreeDelivery($orderNo) {
        $searchVo = new SearchVo('orderNo=?', $orderNo);
        $orderUpdateData['totalDeliveryCharge'] = 0;
        $orderUpdateData['totalMemberDeliveryDcPrice'] = 0;
        DBUtil2::update(DB_ORDER, $orderUpdateData, $searchVo);
        $orderDeliveryUpdateData['deliveryCharge'] = 0;
        DBUtil2::update(DB_ORDER_DELIVERY, $orderDeliveryUpdateData, $searchVo);
    }

    /**
     * 주문 재 계산
     * @param $orderNo
     * @throws Exception
     */
    public function reCalcOrderData($orderNo)
    {
        //$this->debugMsg(' 주문재계산 <<=========');

        $orderInfo = $this->getOrder($orderNo);

        //배송비 무료 정책 적용
        $freeDeilivery = DBUtil2::runSelect("select b.deliveryFree from es_member a join es_memberGroup b on a.groupSno = b.sno where a.memNo = {$orderInfo['memNo']}")[0]['deliveryFree'];
        if( 'y' === $freeDeilivery ){
            $this->setFreeDelivery($orderNo);
        }

        $orderGoodsList = $orderInfo['orderGoodsData'];

        $refineOrderGoodsList = array();
        $etcGoodsKey = 0;
        foreach ($orderGoodsList as $orderGoodsData) {
            if ('goods' === $orderGoodsData['goodsType']) {
                //일반 상품 (sort..)
                $refineOrderGoodsList[$orderGoodsData['orderGoodsOptionKey'].$orderGoodsData['sno']] = $orderGoodsData;
            } else {
                //추가 상품
                if (empty($orderGoodsData['goodsCd'])) {
                    //기타상품
                    $addGoodsKey = '999999999999' . str_pad($etcGoodsKey++, 4, '0', STR_PAD_LEFT);
                } else {
                    //인쇄 상품
                    $addGoodsKey = '8' . $orderGoodsData['goodsNo'] . '99999';
                }
                $refineOrderGoodsList[$addGoodsKey] = $orderGoodsData;
            }
        }

        ksort($refineOrderGoodsList); //옵션 순서대로 정렬한다.

        $totalOrderGoodsPrice = 0;
        $deliverySnoArray = array();
        $orderCd = 1;
        $totalGoodsNm = '';
        $goodsCnt = 0;
        $firstSno = 99999999;
        $totalOrderGoodsCnt = 0;

        $dcPrice = 0;

        foreach ($refineOrderGoodsList as $refineOrderGoodsKey => $refineOrderGoodsData) {
            //SitelabLogger::logger(' * KEY => ' . $refineOrderGoodsKey);
            if ($orderCd > 1 && 'goods' == $refineOrderGoodsData['goodsType']) {
                $goodsCnt++;
            }
            if ($firstSno > $refineOrderGoodsData['sno'] && 'goods' == $refineOrderGoodsData['goodsType'] ) {
                $firstSno = $refineOrderGoodsData['sno'];
                $totalGoodsNm = $refineOrderGoodsData['goodsNm'];
            }
            //주문상품 가격
            $orderGoodsPrice = ($refineOrderGoodsData['goodsPrice'] + $refineOrderGoodsData['optionPrice'] + $refineOrderGoodsData['optionTextPrice']) * $refineOrderGoodsData['goodsCnt'];

            /*if( SlCommonUtil::isDevId() ){
                SitelabLogger::logger($refineOrderGoodsData['sno']. ' : '. $orderGoodsPrice);
            }*/

            //총 주문상품가격 계산
            $totalOrderGoodsPrice += $orderGoodsPrice;
            //배송비 계산 준비여신 변경 확인
            $deliverySnoArray[$refineOrderGoodsData['orderDeliverySno']] = $refineOrderGoodsData['orderDeliverySno'];

            $dcPrice += $refineOrderGoodsData['goodsDcPrice'];

            $orderGoodsSortData['orderCd'] = $orderCd++;

            //TAX 재계산
            $taxOrderGoodsSupplyPrice = NumberUtils::taxAll($orderGoodsPrice, 10, 't');
            $orderGoodsSortData['taxSupplyGoodsPrice'] = $taxOrderGoodsSupplyPrice['supply'];
            $orderGoodsSortData['taxVatGoodsPrice'] = $taxOrderGoodsSupplyPrice['tax'];
            $orderGoodsSortData['realTaxSupplyGoodsPrice'] = $taxOrderGoodsSupplyPrice['supply'];
            $orderGoodsSortData['realTaxVatGoodsPrice'] = $taxOrderGoodsSupplyPrice['tax'];

            DBUtil2::update(DB_ORDER_GOODS, $orderGoodsSortData, new SearchVo('sno=?', $refineOrderGoodsData['sno']));
            $totalOrderGoodsCnt += $refineOrderGoodsData['goodsCnt'];

        }

        $totalDeliveryCharge = 0;

        foreach ($deliverySnoArray as $deliverySno) {
            $deliveryInfo = DBUtil2::getOne(DB_ORDER_DELIVERY, 'sno', $deliverySno);
            $totalDeliveryCharge += $deliveryInfo['deliveryCharge'];
        }

        $totalPrice = $totalOrderGoodsPrice + $totalDeliveryCharge - $dcPrice;
        /*gd_debug($totalOrderGoodsPrice);
        gd_debug($totalDeliveryCharge);
        gd_debug($orderInfo['totalGoodsDcPrice']);
        gd_debug($totalPrice);*/

        //상품명 변경
        $totalGoodsNm = ($goodsCnt > 0) ? $totalGoodsNm . ' 외 ' . $goodsCnt . ' 건' : $totalGoodsNm;

        //상품금액, 배송비, 총금액 재계산
        $orderUpdateData['totalDeliveryCharge'] = $totalDeliveryCharge;
        $orderUpdateData['totalGoodsPrice'] = $totalOrderGoodsPrice;
        $orderUpdateData['totalGoodsDcPrice'] = $dcPrice;
        $orderUpdateData['orderGoodsCnt'] = $totalOrderGoodsCnt;
        $orderUpdateData['settlePrice'] = $totalPrice;

        //세금 재계산
        $taxSupplyPrice = NumberUtils::taxAll($totalPrice, 10, 't');
        $orderUpdateData['taxSupplyPrice'] = $taxSupplyPrice['supply'];
        $orderUpdateData['taxVatPrice'] = $taxSupplyPrice['tax'];
        $orderUpdateData['realTaxSupplyPrice'] = $taxSupplyPrice['supply'];
        $orderUpdateData['realTaxVatPrice'] = $taxSupplyPrice['tax'];

        $orderUpdateData['orderGoodsNm'] = $totalGoodsNm;
        $orderUpdateData['orderGoodsNmStandard'] = $totalGoodsNm;

        DBUtil2::update(DB_ORDER, $orderUpdateData, new SearchVo('orderNo=?', $orderNo));
        //Order와 OrderGoods 등록일자 싱크
        $this->sql->updateSyncRegDt($orderNo);

        //마일리지 체크
        //$this->refineOrderMileage($orderNo);
        //$this->debugMsg(' 주문재계산 완료 <<=========');
    }

    public function syncOrderStatus($orderNo){
        $checkData = DBUtil2::runSql("select sum(1) as cnt , sum(if('c' = left(orderStatus,1),1,0)) as cancelCnt from es_orderGoods where goodsType='goods' and orderNo={$orderNo}");
        if($checkData['cnt'] === $checkData['cancelCnt']){
            DBUtil2::update(DB_ORDER, ['orderStatus'=>'c3'], new SearchVo('orderNo=?', $orderNo));
        }
    }

    /**
     * 마지막 상품으로 주문상태 연동
     * @param $orderNo
     * @throws Exception
     */
    public function latestSyncOrderStatus($orderNo){
        $latestOrderGoods = DBUtil2::getOneSortData(DB_ORDER_GOODS,'orderNo=?', $orderNo,'regDt desc');
        DBUtil2::update(DB_ORDER,['orderStatus'=>$latestOrderGoods['orderStatus']], new SearchVo('orderNo=?', $orderNo));
    }


}
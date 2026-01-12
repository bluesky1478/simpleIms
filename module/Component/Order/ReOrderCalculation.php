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
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;


/**
 * 주문 class
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class ReOrderCalculation extends \Bundle\Component\Order\ReOrderCalculation{

    private $orderService;
    private $goodsStockService;

    public function __construct(){
        $this->orderService = SlLoader::cLoad('Order','OrderService');
        $this->goodsStockService = SlLoader::cLoad('Goods','GoodsStock');
        parent::__construct();
    }

    /**
     * 환불
     * @param $getData
     * @param $autoProcess
     */
    public function setRefundCompleteOrderGoods($getData, $autoProcess){
        SitelabLogger::logger('Legacy때문에 남겨둔것 같음 . 실제로 여기 타지 않는것 같다.');
        parent::setRefundCompleteOrderGoods($getData, $autoProcess);
        $this->orderService->refineApplyPolicyOrderGoods($getData, 2);
    }
    public function setRefundCompleteOrderGoodsNew($getData, $autoProcess){
        parent::setRefundCompleteOrderGoodsNew($getData, $autoProcess);
        $this->orderService->refineApplyPolicyOrderGoods($getData, 2);
    }

    /**
     * 주문취소
     * @param array $cancel 취소 key (orderNo, orderGoods)
     * @param array $cancelMsg 취소 메세지 (orderStatus, handleReason, handleDetailReason)
     * @param array $cancelPrice 취소 금액
     * @param array $cancelReturn 주문 시 처리된 것의 복원 (stock, coupon, gift)
     * @param boolean $restoreCouponFl 쿠폰 복원 여부
     * @return mixed
     */
    public function setCancelOrderGoods($cancel, $cancelMsg, $cancelPrice, $cancelReturn, $restoreCouponFl = true){
        $result = parent::setCancelOrderGoods($cancel, $cancelMsg, $cancelPrice, $cancelReturn, $restoreCouponFl);
        $this->orderService->refineApplyPolicyOrderGoods($cancel,4);
        return $result;
    }

    /**
     * 맞교환 (동일상품교환)
     * @param $postData
     * @return mixed
     * @throws Exception
     */
    public function setSameExchangeOrderGoods($postData){
        $result = parent::setSameExchangeOrderGoods($postData);
        $this->orderService->refineApplyPolicyOrderGoods($postData,11);
        //무조건 착불 변경 ?
        //SitelabLogger::logger('맞 교환으로 인한 재계산');
        //SitelabLogger::logger($postData);
        //SitelabLogger::logger($result);

        $this->orderService->latestSyncOrderStatus($postData['orderNo']);

        return $result;
    }

    /**
     * 타상품 교환
     * @param $postData
     * @return mixed
     */
    public function setAnotherExchangeOrderGoods($postData){
        $result = parent::setAnotherExchangeOrderGoods($postData);
        $this->orderService->refineApplyPolicyOrderGoods($postData,10);
        /*SitelabLogger::logger('교환으로 인한 재계산');
        SitelabLogger::logger($postData);*/

        $this->orderService->latestSyncOrderStatus($postData['orderNo']);

        return $result;
    }

    /**
     * 교환 철회
     * @param $postData
     * @return mixed
     */
    public function restoreExchangeCancel($postData){
        $result = parent::restoreExchangeCancel($postData);
        $this->orderService->refineApplyPolicyOrderGoods($postData,9);
        /*SitelabLogger::logger('교환철회로 인한 재계산');
        SitelabLogger::logger($postData['orderNo']);
        SitelabLogger::logger($result);*/

        $this->orderService->latestSyncOrderStatus($postData['orderNo']);

        return $result;
    }

    /**
     * 상품 추가
     * @param $orderNo
     * @param $addData
     * @param false $orderGoodsChange
     * @return mixed
     */
    public function setAddOrderGoods($orderNo, $addData, $orderGoodsChange = false){
        $result = parent::setAddOrderGoods($orderNo, $addData, $orderGoodsChange);
        $param['orderNo'] = $orderNo;
        $this->orderService->refineApplyPolicyOrderGoods($param,8);
        /*
        SitelabLogger::logger('상품추가');
        SitelabLogger::logger($orderNo);
        SitelabLogger::logger($addData);
        SitelabLogger::logger($orderGoodsChange);
        SitelabLogger::logger($result);
        */
        return $result;
    }

    /**
     * 취소 복원
     * @param $orderNo
     * @param $claimStatus
     * @return mixed
     */
    public function setOrderRestore($orderNo, $claimStatus){
        $result = parent::setOrderRestore($orderNo, $claimStatus);
        $param['orderNo'] = $orderNo;
        $this->orderService->refineApplyPolicyOrderGoods($param,7);
        /*
        SitelabLogger::logger('상품추가');
        SitelabLogger::logger($orderNo);
        SitelabLogger::logger($claimStatus);
        SitelabLogger::logger($result);
        */
        return $result;
    }

    /**
     * 환불 접수
     * @param $arrData
     * @param $changeStatusName
     * @param false $isChargeBack
     * @return mixed
     */
    public function setBackRefundOrderGoods($arrData, $changeStatusName, $isChargeBack = false){
        $result = parent::setBackRefundOrderGoods($arrData, $changeStatusName, $isChargeBack);
        $this->orderService->refineApplyPolicyOrderGoods($arrData,2);
        /*
        SitelabLogger::logger('환불접수');
        SitelabLogger::logger($arrData);
        SitelabLogger::logger($changeStatusName);
        SitelabLogger::logger($isChargeBack);
        SitelabLogger::logger($result);
        */
        return $result;
    }

    /**
     * 사용자 교환 승인처리
     * @param $postData
     * @return mixed
     */
    public function setUserSameExchangeOrderGoods($postData){
        //SitelabLogger::logger("여기 오는지 체크하기 - 사용자 교환처리");
        //SitelabLogger::logger($postData);
        $returnOrderGoodsSno = parent::setUserSameExchangeOrderGoods($postData);
        //ArrayList
        foreach($postData['statusCheck'] as $key => $value){
            $orderNo = explode('||',$value)[0];
            $param['orderNo'] = $orderNo;
            $this->orderService->refineApplyPolicyOrderGoods($param,3);
        }
        //SitelabLogger::logger($returnOrderGoodsSno);
        return $returnOrderGoodsSno;
    }

    /**
     * 주문상품 신규생성
     *
     * @param string $orderNo 주문번호
     * @param integer $orderGoodsSno 주문상품번호
     * @param string $orderStatus 상태값
     * @param integer $handleSno handle sno
     * @param integer $goodsCnt 재고
     * @param integer $userHandleSno user handle sno
     * @param integer $orderCd orderCd
     * @param array $updateOrderGoodsData
     *
     * @return void
     * @throws Exception
     */
    public function copyOrderGoodsData($orderNo, $orderGoodsSno, $orderStatus, $handleSno = null, $goodsCnt = null, $userHandleSno = null, $orderCd=null, $updateOrderGoodsData=[])
    {
        $insertSno =  parent::copyOrderGoodsData($orderNo, $orderGoodsSno, $orderStatus, $handleSno, $goodsCnt, $userHandleSno, $orderCd, $updateOrderGoodsData);
        $insertGoods = DBUtil2::getOne(DB_ORDER_GOODS, 'sno',$insertSno);

        //SitelabLogger::logger('교환 상품 생성 정보 체크 ');
        //SitelabLogger::logger($insertGoods);

        $handleData = DBUtil2::getOne(DBUtil2::getOne(DB_ORDER_HANDLE, 'sno', $insertGoods['handleSno']));
        if( 'z' === $handleData['handleMode']  ){
            //교환 추가 상품은 착불 처리.
            DBUtil2::update(DB_ORDER_GOODS, ['goodsDeliveryCollectFl'=>'later'], new SearchVo('sno=?', $insertSno));
        }
        return $insertSno;
    }

}
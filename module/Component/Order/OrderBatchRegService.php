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
use Component\Storage\Storage;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;


/**
 * 주문 서비스
 */
class OrderBatchRegService{

    public function batchOrder($param, $isMultiOrder = true){

        $defaultValidation = true;
        $defaultErrMsg = [];

        $fieldMap = [
            'memId' => '회원아이디',
            'goodsNo' => '상품번호',
            //'receiverName' => '수령자명',
            //'receiverCellPhone' => '수령자 핸드폰',
            'optionName' => '옵션명',
            'deliveryName' => '배송지점',
            'stockCnt' => '주문수량',
        ];

        //상품+옵션 = 수량 , 주문자별 묶음
        $orderBundle = [];
        $goodsOptionBundle = [];
        foreach($param as $key => $value){
            $goodsNo = $value['goodsNo'];
            $optionName = $value['optionName'];
            $receiverKey = $value['memId'].'#'.$value['deliveryName'];

            if( !$isMultiOrder && !empty($orderBundle[$receiverKey]) ){
                $defaultValidation = false;
                $defaultErrMsg[] = "{$receiverKey} 님은 중복 주문 입니다.(확인요망)";
            }

            if( empty($goodsOptionBundle[$goodsNo]) ){
                $goodsInfo = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
                $goodsInfo['orderOption'][$optionName]['reqStockCnt'] = $value['stockCnt'];
                $goodsOptionBundle[$goodsNo] = $goodsInfo;
            }else{
                $goodsOptionBundle[$goodsNo]['orderOption'][$optionName]['reqStockCnt'] += $value['stockCnt'];
            }
            if( !isset($goodsOptionBundle[$goodsNo]['orderOption'][$optionName]['stockCnt']) ){
                //gd_debug( $optionName );
                $optionInfo = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo(['goodsNo=?','optionValue1=?'],[$goodsNo,$optionName]));
                //gd_debug( $optionInfo );
                if(!empty($optionInfo)){
                    $goodsOptionBundle[$goodsNo]['orderOption'][$optionName]['optionInfo'] = $optionInfo;
                    $goodsOptionBundle[$goodsNo]['orderOption'][$optionName]['stockCnt'] = $optionInfo['stockCnt'];
                }
            }

            foreach($fieldMap as $fieldKey => $fieldName){
                $row = $key+1;
                $this->setValidation($value[$fieldKey], $defaultValidation, $defaultErrMsg, "{$row}번 행 {$fieldName} 값이 비어 있습니다.(필수입력)");
            }

            $orderBundle[$receiverKey]['orderInfo'] = $value;
            unset($orderBundle[$receiverKey]['orderInfo']['stockCnt']);
            unset($orderBundle[$receiverKey]['orderInfo']['optionName']);
            unset($orderBundle[$receiverKey]['orderInfo']['goodsNo']);

            $orderBundle[$receiverKey]['orderGoods'][$goodsNo]['myOrderOption'][$optionName] += $value['stockCnt'];

        }

        $errMsg = null;
        if( $defaultValidation ){
            $result = $this->setAndCheckManualOrderValue($orderBundle, $goodsOptionBundle);
            if( !$result['isValid'] ){
                //gd_debug( $result['errMsg'] );
                $errMsg = $result['errMsg'];
            }else{
                $this->saveManualOrder($result['orderBundle'], $goodsOptionBundle);
            }
        }else{
            //gd_debug( $defaultErrMsg );
            $errMsg = $defaultErrMsg;
        }

        return $errMsg;

    }

    public function setValidation($info, &$isValid, &$errMsgList, $errMsg){
        if( empty($info) ){
            $isValid = false;
            $errMsgList[] = $errMsg;
        }
    }

    public function setAndCheckManualOrderValue($orderBundle, $goodsOptionBundle){
        $isValid = true;
        $errMsg = [];

        //상품별 재고 체크
        foreach($goodsOptionBundle as $key => $value){
            $this->setValidation($value['goodsNo'], $isValid, $errMsg, "상품번호 : {$key} 은(는) 찾을 수 없습니다.");
            if( !empty($value['goodsNo']) ){
                foreach($value['orderOption'] as $orderOptionName => $orderOption){
                    $this->setValidation($orderOption['optionInfo'], $isValid, $errMsg, "상품 : {$value['goodsNm']}({$key}) / {$orderOptionName} 은(는) 옵션정보를 찾을 수 없습니다.");
                    if( 'y' === $value['stockFl'] && !empty($orderOption['optionInfo']) && $orderOption['reqStockCnt'] > $orderOption['stockCnt']){
                        $isValid = false;
                        $errMsg[] = "상품 : {$value['goodsNm']}({$key}) / {$orderOptionName} 은(는) 재고가 부족합니다. ( 요청:{$orderOption['reqStockCnt']}/현재:{$orderOption['stockCnt']} )";
                    }
                }
            }
        }

        foreach($orderBundle as $key => $value){
            //지점 체크
            if( !empty($value['orderInfo']['deliveryName']) ){
                $deliveryInfo = DBUtil2::getOne('sl_setScmDeliveryList','subject',$value['orderInfo']['deliveryName']);
                $orderBundle[$key]['orderInfo']['deliveryInfo'] = $deliveryInfo;
                $this->setValidation($deliveryInfo, $isValid, $errMsg, "배송지점 [{$value['orderInfo']['deliveryName']}]은(는) 찾을 수 없습니다.");
            }
            //회원 체크
            $memberInfo = DBUtil2::getOne(DB_MEMBER,'memId',$value['orderInfo']['memId']);
            $orderBundle[$key]['orderInfo']['memberInfo'] = $memberInfo;
            $this->setValidation($memberInfo, $isValid, $errMsg,"회원아이디 [{$value['orderInfo']['memId']}]은(는) 찾을 수 없습니다.");
        }

        return [
            'isValid'=>$isValid,
            'orderBundle'=>$orderBundle,
            'errMsg'=>$errMsg
        ];
    }

    public function saveManualOrder($orderBundle, $goodsOptionBundle){
        $writeSw = true;

        //각 SCM및 조건에 맞게 셋팅
        $order = \App::load(\Component\Order\Order::class);
        $orderStatus = 'p3';
        $scmNo = 16;
        $settleKind = 'gz';
        $paymentDt = date('Y-m-d H:i:s');
        if( SlCommonUtil::isDev() ){
            $deliverySno = 2050; //TEST 서버
        }else{
            $deliverySno = 68;
        }

        $orderGoodsNm = '';

        foreach( $orderBundle as $orderData ){

            $orderGoodsListParam = [];

            //상품/옵션 데이터
            foreach($orderData['orderGoods'] as $buyGoodsNo => $buyGoods){
                foreach( $buyGoods['myOrderOption'] as $myOptionName => $myOrderProductCnt ){
                    //gd_debug( $myOptionName . ' : ' . $myOrderProductCnt  );
                    $optionInfo = $goodsOptionBundle[$buyGoodsNo]['orderOption'][$myOptionName]['optionInfo'];
                    $buyGoodsData = SlCommonUtil::getAvailData($goodsOptionBundle[$buyGoodsNo],[
                        'goodsCd','goodsNm','cateCd','brandCd'
                    ]);
                    $buyGoodsData['goodsNmStandard'] = $buyGoodsData['goodsNm'];
                    $buyGoodsData['optionSno'] = $optionInfo['sno'];
                    if( empty($orderGoodsNm) ) $orderGoodsNm = $buyGoodsData['goodsNm'];

                    $tmp[] = [
                        $goodsOptionBundle[$buyGoodsNo]['optionName'],
                        $myOptionName,
                        $optionInfo['optionCode'],
                        $optionInfo['optionPrice'],
                        null,
                    ];
                    $buyGoodsData['optionInfo'] = json_encode($tmp, JSON_UNESCAPED_UNICODE);
                    $buyGoodsData['goodsNo'] = $buyGoodsNo;
                    $buyGoodsData['goodsCnt'] = $myOrderProductCnt;
                    unset($tmp);
                    $orderGoodsListParam[$buyGoodsNo][$myOptionName] = $buyGoodsData;
                }
            }

            $param = SlCommonUtil::getAvailData($orderData['orderInfo'],[
                'memId', 'receiverName', 'receiverCellPhone'
            ]);

            //gd_debug($orderData);
            $deliveryInfo = $orderData['orderInfo']['deliveryInfo'];
            $memberInfo = $orderData['orderInfo']['memberInfo'];

            //자동 완성
            $param['orderNo'] = $order->generateOrderNo();
            $param['orderStatus'] = $orderStatus;
            $param['orderTypeFl'] = 'write';
            $param['scmNo'] = $scmNo;
            $param['settleKind'] = $settleKind;
            $param['paymentDt'] = $paymentDt;
            $param['deliverySno'] = $deliverySno;

            //Validation Check 한 정보로 완성
            $param['orderName'] = $memberInfo['memNm'];
            $param = array_merge($param,SlCommonUtil::getAvailData($deliveryInfo,[
                'receiverZipcode','receiverZonecode','receiverAddress','receiverAddressSub', 'receiverName', 'receiverCellPhone'
            ]));
            $orderGoodsCnt = count($orderData['orderGoods']);
            //$param['orderGoodsCnt'] = $orderGoodsCnt;
            $orderGoodsCnt -= 1;
            $goodsNmSuffix = $orderGoodsCnt > 0 ? " 외 {$orderGoodsCnt}건":"";
            $param['orderGoodsCnt'] += $orderGoodsCnt;
            $param['orderGoodsNm'] = $orderGoodsNm.$goodsNmSuffix;
            $param['orderGoodsNmStandard'] = $orderGoodsNm.$goodsNmSuffix;
            $param['memNo'] = $memberInfo['memNo'];
            $param['memNm'] = '조경식';
            //$param['orderCellPhone'] = $memberInfo['cellPhone'];
            //$param['receiverCellPhone'] = $memberInfo['cellPhone'];

            if( $writeSw ){
                //주문 저장
                DBUtil2::insert(DB_ORDER, $param);
                DBUtil2::insert(DB_ORDER_INFO, $param);
                $orderDeliverySno = DBUtil2::insert(DB_ORDER_DELIVERY, $param);
                $param['orderDeliverySno'] = $orderDeliverySno;
                foreach($orderGoodsListParam as $orderGoodsList){
                    $orderGoodsSnoList = [];
                    foreach($orderGoodsList as $orderGoodsData){
                        $saveGoodsParam = array_merge($param, $orderGoodsData);
                        $orderGoodsSnoList[] = DBUtil2::insert(DB_ORDER_GOODS, $saveGoodsParam);
                    }
                    //재고차감
                    $order->setGoodsStockCutback($param['orderNo'], $orderGoodsSnoList);
                }
                //승인여부
                $orderService = SlLoader::cLoad('Order','OrderService');
                $orderService->saveOrderAcct($param);
            }
        }
    }


}
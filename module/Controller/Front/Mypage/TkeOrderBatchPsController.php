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

namespace Controller\Front\Mypage;

use App;
use Component\Order\OrderAdmin;
use Component\Member\Manager;
use Component\Sms\Code;
use Component\Sms\SmsAutoOrder;
use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Exception;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertReloadException;
use Framework\Debug\Exception\AlertRedirectException;
use Message;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 * @author  Shin Donggyu <artherot@godo.co.kr>
 */
class TkeOrderBatchPsController extends \Bundle\Controller\Front\Mypage\OrderPsController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {

        gd_debug('주문전환 시작');
        $tkeOrderList = DBUtil2::runSelect("select * from sl_tkeOrder");
        /*gd_debug($tkeOrderList);
        gd_debug($tkeOrderGoodsList);
        gd_debug(count($tkeOrderList));
        gd_debug(count($tkeOrderGoodsList));*/
        $order = \App::load(\Component\Order\Order::class);
        $memNo = SlCommonUtil::isDev() ? 1 : 5639;
        $desliverySno = SlCommonUtil::isDev() ? 2050 : 32;
        $orderStatus = 'p3';
        $settleKind = 'gz';
        $scmNo = 8;
        $now = date('Y-m-d H:i:s');

        if( SlCommonUtil::isDev() ){
            $goodsMap = [
                '1000002052' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000002052'),
                '1000002051' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000002051'),
            ];
        }else{
            $goodsMap = [
                '1000000331' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000331'),
                '1000000329' => DBUtil2::getOne(DB_GOODS, 'goodsNo', '1000000329'),
            ];
        }

        $deliverySearch = new SearchVo('deliverySno=?',$desliverySno);
        $deliverySearch->setLimit(1);
        $deliverySearch->setOrder("regDt desc");
        $deliveryInfo = DBUtil2::getOneBySearchVo(DB_ORDER_DELIVERY, $deliverySearch);
        unset($deliveryInfo['sno']);
        unset($deliveryInfo['orderNo']);

        foreach($tkeOrderList as $tkeOrderData){
            $orderNo = $order->generateOrderNo();
            $param['orderNo'] = $orderNo;
            $param['memNo'] = $memNo;
            $param['orderStatus'] = $orderStatus;
            $param['settleKind'] = $settleKind;
            $param['paymentDt'] = $now;
            $param['regDt'] = $now;

            $orderParam = [
                'orderNo' => $orderNo,
                'orderStatus' => $orderStatus,
                'scmNo' => $scmNo,
                'goodsMap' => $goodsMap,
                'now' => $now,
                'orderService' => $order
            ];

            $saveGoodsInfo = $this->saveOrderGoods($orderParam, $tkeOrderData['sno'], $deliveryInfo); //외 1건.
            //gd_debug('주문 상품 저장 체크');
            //gd_debug($saveGoodsInfo);

            $param['orderGoodsNm'] = $saveGoodsInfo['goodsNm'];
            $param['orderGoodsNmStandard'] = $saveGoodsInfo['goodsNm'];
            $param['orderGoodsCnt'] = $saveGoodsInfo['goodsCnt'];
            DBUtil2::insert(DB_ORDER, $param);

            //gd_debug('주문 저장 체크');
            //gd_debug($param);

            //OrderInfo
            $info['orderNo'] = $orderNo;
            $info['orderName'] = '파트너관리';
            $info['receiverName'] = $tkeOrderData['receiverName'];
            $info['receiverPhone'] = $tkeOrderData['receiverPhone'];
            $info['receiverCellPhone'] = $tkeOrderData['receiverCellPhone'];
            $info['receiverAddress'] = $tkeOrderData['receiverAddress'];
            $info['receiverAddressSub'] = $tkeOrderData['memId'];
            $info['regDt'] = $now;

            //gd_debug('상품INFO 저장 체크');
            //gd_debug($info);
            DBUtil2::insert(DB_ORDER_INFO, $info);
            DBUtil2::runSql("update sl_tkeOrder set receiverZipcode='{$orderNo}' where sno={$tkeOrderData['sno']}");
        }

        gd_debug('주문전환 종료');

        exit();
    }

    public function saveOrderGoods($orderParam, $tkeOrderSno, $deliveryInfo){
        $map = $orderParam['goodsMap'];
        $deliveryInfo['orderNo']=$orderParam['orderNo'];
        $deliveryInfo['regDt']=$orderParam['now'];
        $newOrderDeliverySno = DBUtil2::insert(DB_ORDER_DELIVERY, $deliveryInfo);

        $tkeOrderGoodsList = DBUtil2::runSelect("select * from sl_tkeOrderGoods where tkeOrderSno={$tkeOrderSno} ");
        $prdCnt = count($tkeOrderGoodsList);
        $firstGoodsNm = '';
        $goodsCnt = 0;

        //gd_debug('상품저장 체크');
        $optionSnoList = [];
        foreach($tkeOrderGoodsList as $idx => $tkeOrderGoods){
            $goodsNo=$tkeOrderGoods['goodsNo'];
            $saveGoods['mallSno']=1;
            $saveGoods['orderNo']=$orderParam['orderNo'];
            $saveGoods['orderCd']=($idx+1);
            $saveGoods['orderStatus']=$orderParam['orderStatus'];
            $saveGoods['orderDeliverySno']=$newOrderDeliverySno;
            $saveGoods['scmNo']=$orderParam['scmNo'];
            $saveGoods['goodsNo']=$goodsNo;
            $saveGoods['goodsNm']=$map[$goodsNo]['goodsNm'];
            if( $idx == 0 ){
                $firstGoodsNm = $saveGoods['goodsNm'];
            }
            $saveGoods['goodsNmStandard']=$map[$goodsNo]['goodsNm'];
            $saveGoods['goodsCnt']=$tkeOrderGoods['goodsCnt'];

            $goodsCnt += $saveGoods['goodsCnt'];

            $size = trim($tkeOrderGoods['optionName']);
            $optionData = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo([
                'goodsNo=?',
                'optionValue1=?',
            ],[
                $goodsNo,
                $size,
            ]));
            $saveGoods['optionSno']=$optionData['sno'];
            $saveGoods['optionInfo']='[["사이즈","'.$size.'","",0,null]]';
            $saveGoods['paymentDt']=$orderParam['now'];
            $saveGoods['regDt']=$orderParam['now'];

            $optionSnoList[] = DBUtil2::insert(DB_ORDER_GOODS, $saveGoods);
            //gd_debug($saveGoods);
        }

        //재고 차감.
        $orderParam['orderService']->setGoodsStockCutback($orderParam['orderNo'], $optionSnoList);

        $dpPrdCnt = $prdCnt-1;
        return [
            'goodsNm' => $firstGoodsNm . ($prdCnt > 1?" 외 {$dpPrdCnt}건":''),
            'goodsCnt' => $goodsCnt,
        ];
    }

}

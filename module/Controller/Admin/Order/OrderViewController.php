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
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Order;

use App;
use Component\Sitelab\SiteLabDownloadUtil;
use Exception;
use Request;
use Framework\Debug\Exception\LayerNotReloadException;
use Framework\Debug\Exception\LayerException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

/**
 * 주문 첨부파일 다운로드
 *
 * @package Bundle\Controller\Admin\Order
 * @author  su
 */
class OrderViewController extends \Bundle\Controller\Admin\Order\OrderViewController{
    public function index(){
        parent::index();

        //$list = DBUtil2::getList('sl_3plProduct', 'scmNo', 6);
        //$hkCodeMap = SlCommonUtil::arrayAppKeyValue($list,'thirdPartyProductCode','productName');
        //$this->setData('hkCodeMap', $hkCodeMap);

        $data = $this->getData('data');

        $totalGoodsCnt = 0;
        $totalGoodsPrice = 0;
        $totalGoodsAmount = 0;
        foreach($data['goods'] as $each1){
            foreach($each1 as $each2){
                foreach($each2 as $each3){
                    $totalGoodsCnt += $each3['goodsCnt'];
                    $totalGoodsAmount += $each3['settlePrice'];
                    $totalGoodsPrice += ($each3['goodsPrice']*$each3['goodsCnt']);
                }
            }
        }

        $this->setData('totalGoodsCnt',$totalGoodsCnt);
        $this->setData('totalGoodsAmount',$totalGoodsAmount);
        $this->setData('totalGoodsPrice',$totalGoodsPrice);

        //파일정보 가져오기
        $orderFileList = DBUtil::getList('sl_orderAttFile','orderNo',$data['orderNo']);
        $this->setData('orderFileList',$orderFileList);
        //gd_debug($orderFileList);

        $orderAddedData = DBUtil2::getOne('sl_orderAddedData', 'orderNo', $data['orderNo']);
        $this->setData('orderAddedData',$orderAddedData);

        $scmService=SlLoader::cLoad('godo','scmService','sl');
        $tkeDeliveryList = $scmService->getScmAddressList(8);
        $this->setData('tkeDeliveryList', $tkeDeliveryList);

        //결제히스토리
        $orderService = SlLoader::cLoad('order','orderService');
        $paymentsHistory = $orderService->getPaymentsHistory($data['orderNo']);
        $this->setData('paymentsHistory',$paymentsHistory);



    }
}

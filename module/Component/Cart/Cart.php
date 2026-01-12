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
namespace Component\Cart;

use Component\Goods\Goods;
use Component\Member\Member;
use Component\Naver\NaverPay;
use Component\Payment\Payco\Payco;
use Component\GoodsStatistics\GoodsStatistics;
use Component\Policy\Policy;
use Component\Database\DBTableField;
use Component\Delivery\EmsRate;
use Component\Delivery\OverseasDelivery;
use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Component\Member\Util\MemberUtil;
use Component\Member\Group\Util;
use Component\Scm\ScmTkeService;
use Component\Validator\Validator;
use Cookie;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertRedirectCloseException;
use Framework\Debug\Exception\Except;
use Framework\Utility\ArrayUtils;
use Framework\Utility\NumberUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Globals;
use Request;
use Session;
use Component\Godo\GodoCenterServerApi;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ScmCodeMap;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * 장바구니 class
 *
 * 상품과 추가상품을 분리하는 작업에서 추가상품을 기존과 동일하게 상품에 종속시켜놓은 이유는
 * 상품과 같이 배송비 및 다양한 조건들을 아직은 추가상품에 설정할 수 없어서
 * 해당 상품으로 부터 할인/적립등의 조건을 상속받아서 사용하기 때문이다.
 * 따라서 추후 추가상품쪽에 상품과 동일한 혜택과 기능이 추가되면
 * 장바구니 테이블에서 상품이 별도로 담길 수 있도록 개발되어져야 한다.
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class Cart extends \Bundle\Component\Cart\Cart{

    protected function getCartDataInfo($getData, $postValue = []){
        /*foreach( $getData as $getKey => $eachData  ){
            //$eachData['deliverySno'] = 2054;
            $getData[$getKey] = $eachData;
        }*/
        //gd_debug( $getData );

        //SitelabLogger::logger('====>  getCartDataInfo  <========');
        $requestPostValue = Request::post()->toArray();
        $orderNo = empty($postValue['orderNo']) ? $requestPostValue['orderNo'] : $postValue['orderNo'];
        if( !empty($orderNo) ){
            $orderSerivce = SlLoader::cLoad('order','orderService');
            $cartMemNo = $orderSerivce->getMemNoByOrderNo($orderNo);
        }else{
            $cartMemNo = empty(Session::get('member.memNo'))?0:Session::get('member.memNo');
        }
        //SitelabLogger::logger($cartMemNo);
        //SitelabLogger::logger('=========================');

        $goodsPolicy = \App::load(\Component\Goods\GoodsPolicy::class);
        $cartData = parent::getCartDataInfo($getData, $postValue);

        //scm별
        foreach($cartData as $scmKey => $deliveryData){
            //delivery별
            foreach($deliveryData as $goodsDataListIndex => $goodsDataList){
                //goods별
                foreach($goodsDataList as $goodsIndex => $goodsData){
                    $unitGoodsPolicyData = $goodsPolicy->calculationGoodsPolicy($goodsData, $cartMemNo);
                    $this->totalGoodsDcPrice += $unitGoodsPolicyData['unitDcPrice'];
                    $this->totalScmGoodsDcPrice[$scmKey] += $unitGoodsPolicyData['unitDcPrice'];
                    $goodsData['price']['goodsDcPrice'] = $unitGoodsPolicyData['unitDcPrice'];
                    $goodsData['price']['goodsPriceSubtotal'] -= $unitGoodsPolicyData['unitDcPrice'];
                    $goodsData['customDcInfo'] = $unitGoodsPolicyData['unitCustomDcInfo'];
                    $goodsData['policyInfo'] = $unitGoodsPolicyData['unitPolicyInfo'];
                    $goodsDataList[$goodsIndex] = $goodsData;
                }
                $deliveryData[$goodsDataListIndex] = $goodsDataList;
            }
            $cartData[$scmKey] = $deliveryData;
        }
        //SitelabLogger::logger('-----------------------------------------------------------------------------------------');
        //gd_debug($cartData);
        return $cartData;
    }



    public function checkOrderPossible($data, $whsiFl = false){
        $shreStock = $this->getShareStock($data['goodsNo'], $data['optionSno']);
        $data['stockCnt'] += $shreStock;
        $data['totalStock'] += $shreStock;
        /*if( 1 ==  \Session::get('manager.sno') ){
            gd_debug($data['stockCnt']);
            gd_debug($data['totalStock']);
        }*/
        return parent::checkOrderPossible($data, $whsiFl);;
    }


    public function getShareStock($goodsNo, $optionSno){
        $stockCnt = 0;
        $orderService=SlLoader::cLoad('order','orderService','');
        $goodsData = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
        if( '1' == $goodsData['isOpenFl'] && !empty($goodsData['openGoodsNo']) ){
            $searchVo = new SearchVo(['goodsNo=?', 'sno=?' ] , [$goodsNo, $optionSno]);
            $goodsOption = DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, $searchVo);
            $shareData = $orderService->getShareStockCnt($goodsData['openGoodsNo'], $goodsOption['optionNo']);
            if( !empty($shareData[0]['shareTotalCnt']) &&  $shareData[0]['shareTotalCnt'] > 0 ){
                $stockCnt = $shareData[0]['shareTotalCnt'];
            }
        }
        return $stockCnt;
    }


    /**
     * 카트 비우기
     */
    public function truncateDirectCart(){
        parent::setDeleteDirectCart();
    }


}

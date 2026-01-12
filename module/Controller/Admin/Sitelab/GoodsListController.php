<?php

namespace Controller\Admin\Sitelab;

use App;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SlComponent\Util\SlCodeMap;

class GoodsListController extends \Bundle\Controller\Admin\Goods\GoodsListController{
    public function index(){
        parent::index();

        $goodsPolicyComponent = \App::load(\Component\Goods\GoodsPolicy::class);

        /*<script src="https://cdn.jsdelivr.net/npm/vue"></script>*/
        /*$this->setData(
            'headerScript', [
                 'https://cdn.jsdelivr.net/npm/vue',
            ]
        );*/
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
            /*'../../script/vue.js',*/
        ]);

        //-- Title 설정
        $goodsGridConfigList['check'] = '선택';
        $goodsGridConfigList['check'] = '번호';
        $goodsGridConfigList['goodsNo'] = '상품코드';
        $goodsGridConfigList['goodsImage'] = '이미지';
        $goodsGridConfigList['goodsNm'] = '상품명';
        //$goodsGridConfigList['option'] = '옵션';
        $goodsGridConfigList['goodsPrice'] = '판매가';
        //$goodsGridConfigList['scmNo'] = '공급사';
        $goodsGridConfigList['sale1'] = '공동구매';
        //$goodsGridConfigList['stockFl'] = '재고';
        $goodsGridConfigList['policy'] = '적용정책';
        $goodsGridConfigList['memberType'] = '특이사항';
        $goodsGridConfigList['addReason'] = 'A/S추가사유';
        $goodsGridConfigList['soldOutMemo'] = '사이즈표/품절메세지';

        //-- 데이터 가공
        $goodsData = $this->getData('data');
        //gd_debug($goodsData);

        $additionalInfoGoodsData = $goodsPolicyComponent->getAdditionalInfoGoodsData($goodsData);

        $this->setData('data', $additionalInfoGoodsData);

        $this->setData('goodsGridConfigList', $goodsGridConfigList); // 상품 그리드 항목
        $this->callMenu('sitelab', 'goods', 'policy_list');
        $this->getView()->setPageName('sitelab/goods_policy_list.php');

        $this->setData('memberTypeMap', SlCodeMap::MEMBER_TYPE);
        $this->setData('hankookTypeMap', SlCodeMap::HANKOOK_TYPE);


        $addReasonMap = [];
        foreach ( SlCodeMap::ADMIN_CLAIM_REASON as $eachKey => $eachValue){
            if( $eachKey > 10 ){
                $addReasonMap[$eachKey] = $eachValue;
            }
        }
        $this->setData('adminClaimReasonMap', $addReasonMap);

        $goodsConfig = (gd_policy('goods.display')); // 상품 설정 config 불러오기
        $goodsConfig['goodsModDtTypeList'] = gd_isset($goodsConfig['goodsModDtTypeList'], 'y');
        $goodsConfig['goodsModDtFl'] = gd_isset($goodsConfig['goodsModDtFl'], 'n');
        $this->setData('goodsConfig', $goodsConfig);

        /*타이틀 설정*/
        $titles = [];
        if (count($goodsGridConfigList) > 0) {
            foreach($goodsGridConfigList as $gridKey => $gridName){
                $addClass = '';
                if($gridKey === 'display') continue;
                if($gridKey === 'goodsNm') {
                    $addClass = " style='min-width: 300px !important;' ";
                }
                if( $gridKey === 'goodsDisplayFl' || $gridKey ==='goodsSellFl') {
                    $addClass = " style='min-width: 120px !important;' ";
                }
                if($gridKey === 'check') {
                    $titles[] =  "<th><input type='checkbox' value='y' class='js-checkall' data-target-name='goodsNo'/></th>";
                }
                else {
                    $titles[] =  "<th ".$addClass.">".$gridName."</th>";
                }
            }
        }
        $this->setData('titles',$titles);

    }

}
<?php

namespace Controller\Admin\Test;

use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * TEST 페이지
 */
class TestInitController extends \Controller\Admin\Controller{

    private $orderService;

    public function index(){
        gd_debug("== 테스트 정보 초기화 ==");

        //삭제할 테이블
        $deleteTargetTable = [
            'es_order'
            ,'es_orderInfo'
            ,'es_orderGoods'
            ,'es_adminOrderGoodsMemo'
            ,'sl_orderGoodsPolicy'
            ,'sl_orderGoodsFreeBuyHistory'
            ,'es_logOrder'
            ,'es_memberOrderGoodsCountLog'
            ,'es_orderDelivery'
            ,'es_orderDeliveryOriginal'
            ,'es_orderGoodsOriginal'
            ,'es_orderHandle'
            ,'es_orderInvoice'
            ,'es_orderOriginal'
            ,'es_orderPayHistory'
            ,'es_orderPayHistory2'
            ,'es_orderSalesStatistics'
            ,'es_orderSalesStatisticsOriginal'
            ,'es_orderUserHandle'
            ,'sl_claimHistory'
            ,'sl_claimHistoryGoods'
            ,'sl_orderScm'
        ];

        //삭제 주문번호
        $list = DBUtil::getList('es_order','memNo','1');
        foreach($list as $key => $value){
            foreach( $deleteTargetTable as $targetKey => $targetTable){
                $result = DBUtil::delete($targetTable,new SearchVo('orderNo=?',$value['orderNo']));
                gd_debug($result . '삭제완료 : ' , $result  );
            }
        }
        // TODO :: sl_goodsStock 추후 초기화.
        gd_debug("완료");
        exit();
    }

}
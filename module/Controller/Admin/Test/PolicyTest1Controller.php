<?php

namespace Controller\Admin\Test;

use Component\Database\DBTableField;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;

/**
 * TEST 페이지
 */
class PolicyTest1Controller extends \Controller\Admin\Controller{

    public function index(){
        gd_debug("== ORDER POLICY TEST ==");

        $orderNo = '2009051854434507';

        gd_debug( 'orderGoods' );
        $orderGoodsInfo = DBUtil::getList('sl_orderGoodsPolicy', 'orderNo' , $orderNo);

        gd_debug($orderGoodsInfo);

        gd_debug("== 완료 ==");
        exit();
    }

}
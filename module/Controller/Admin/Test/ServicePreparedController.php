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
 * 서비스 준비 페이지
 */
class ServicePreparedController extends \Controller\Admin\Controller{

    private $orderService;

    public function index(){
        gd_debug("== 서비스 준비 하기 ==");

        //Order.php or OrderService에 추가
        $order = \App::load(\Component\Order\Order::class);
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        $orderData = $order->getOrderList(3, [$startDate, $endDate], 'order');

        gd_debug($this->refineOrderList($orderData));

        gd_debug("완료");
        exit();
    }

    /**
     * 주문리스트 정제
     */
    public function refineOrderList($orderData){

        return $orderData;
    }

}
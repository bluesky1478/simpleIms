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

use Globals;
use Request;

/**
 * 주문 리스트 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class OrderListAllController extends \Bundle\Controller\Admin\Order\OrderListAllController{

    public function index(){
        \Request::get()->set('exceptOrderStatus', ['c1','c2','c3','c4','c5','f1','f2','f3','f4'] );
        parent::index();
        //gd_debug($this->getData('data'));
    }
}

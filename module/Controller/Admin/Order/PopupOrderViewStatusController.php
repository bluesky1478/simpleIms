<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2017, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Order;

use App;
use Exception;
use Component\Naver\NaverPay;
use Framework\Debug\Exception\AlertCloseException;
use Session;
use Request;

/**
 * Class PopupOrderViewStatusController
 *
 * @package Bundle\Controller\Admin\Order
 * @author <bumyul2000@godo.co.kr>
 */
class PopupOrderViewStatusController extends \Bundle\Controller\Admin\Order\PopupOrderViewStatusController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        try {
            parent::index();
            $getValue = Request::get()->toArray();
            $this->setData('orderGoodsSnoList',$getValue['orderGoodsSnoList']);
            $this->setData('reqCnt',$getValue['reqCnt']);
            $this->setData('claimSno',$getValue['claimSno']);

            if( !empty($getValue['claimSno']) ){
                $this->setData('defaultReason','기타');
                $this->setData('defaultReasonView','none');
            }else{
                $this->setData('defaultReason','');
                $this->setData('defaultReasonView','block');
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
}

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
namespace Controller\Mobile\Order;

use Bundle\Component\Mall\MallDAO;
use Component\CartRemind\CartRemind;
use Framework\Debug\Exception\AlertRedirectException;
use Component\Mall\Mall;
use Message;
use Globals;
use Session;
use Request;
use SlComponent\Util\SlSkinUtil;

/**
 * 주문 완료 페이지
 *
 * @package Bundle\Controller\Mobile\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class CartController extends \Bundle\Controller\Mobile\Order\CartController
{
    /**
     * index
     *
     */
    public function index()
    {
        parent::index();
        $this->setData('otherSkin', SlSkinUtil::getOtherSkinName());
        /*$this->addScript([
            'gd_board_write.js',
        ]);*/

    }
}


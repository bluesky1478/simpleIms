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

namespace Controller\Front\Mypage;

use Component\Database\DBTableField;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Cookie;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Request;
use Session;
use SlComponent\Util\SlLoader;

/**
 * 환불 처리
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class LayerOrderRefundRegistController extends \Bundle\Controller\Front\Mypage\LayerOrderRefundRegistController
{
    /**
     * @inheritdoc
     */
    public function index()
    {
        try {
            parent::index();
            $orderService = SlLoader::cLoad('Order','OrderService');
            $orderData = $orderService->refineOrderList( $this->getData('orderData') );
            $this->setData('orderData',$orderData);
        } catch (Exception $e) {
            throw new AlertBackException($e->getMessage());
        }
    }
}

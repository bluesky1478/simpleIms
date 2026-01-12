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
namespace Controller\Admin\Ims;

use Exception;
use Request;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

/**
 * Class LayerAdminOrderGoodsMemoController
 * 상품º주문번호별 메모
 *
 * @package Bundle\Controller\Admin\Order
 * @author  choisueun <cseun555@godo.co.kr>
 */
class LayerOrderAddInfoController extends CallViewController
{
    /**
     * @inheritdoc
     */
    public function index()
    {
        parent::index();
        $this->getView()->setDefine('layout', 'layout_layer.php');
        $this->getView()->setPageName('ims/layer_order_add_info.php');
    }
}

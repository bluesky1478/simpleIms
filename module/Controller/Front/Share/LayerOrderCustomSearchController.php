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
namespace Controller\Front\Share;


use Component\Board\Board;
use Component\Member\Util\MemberUtil;
use Component\Goods\AddGoodsAdmin;
use Component\Goods\Goods;
use Component\Order\OrderAdmin;
use Framework\Utility\SkinUtils;

class LayerOrderCustomSearchController extends \Bundle\Controller\Front\Share\LayerOrderSearchController
{
    public function index()
    {
        parent::index();
    }
}

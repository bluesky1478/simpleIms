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
namespace Controller\Front\Goods;

use Component\Member\Util\MemberUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSkinUtil;

/**
 * 품절 재입고 알림 요청
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */
class PopupGoodsRestockController extends \Bundle\Controller\Front\Goods\PopupGoodsRestockController
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        parent::index();
        $controllerService = SlLoader::cLoad('godo','controllerService','sl');
        $controllerService->setRestockData($this);
    }
}

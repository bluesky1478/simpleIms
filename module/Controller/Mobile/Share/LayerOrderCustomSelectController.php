<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Enamoo S5 to newer
 * versions in the future.
 *
 * @copyright Copyright (c) 2015 NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Controller\Mobile\Share;


use Component\Board\BoardConfig;

class LayerOrderCustomSelectController extends \Bundle\Controller\Mobile\Share\LayerOrderSelectController
{
    /**
     * index
     * 레이어-주문선택
     */
    public function index()
    {
        parent::index();
        $this->setData('memNo', \Request::request()->toArray()['memNo']);
    }
}

<?php
namespace Controller\Admin\Ims\Popup;

use Request;
use Controller\Admin\Ims\ImsControllerTrait;

class ImsPopUpdateHistoryListController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    public function index() {
        $this->setDefault();

        $request = \Request::request()->toArray();
        $iType = (int)$request['type'];
        switch($iType) {
            case 1: $sMenuName = '프로젝트/스타일 이슈'; break;

            default: $sMenuName = '프로젝트/스타일 이슈'; break;
        }
        $this->setData('sMenuName', $sMenuName);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
<?php
namespace Controller\Admin\Ims\Popup;

use Controller\Admin\Ims\ImsControllerTrait;

class ImsPopMaterialTypeDetailController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    public function index() {
        $this->setDefault();

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
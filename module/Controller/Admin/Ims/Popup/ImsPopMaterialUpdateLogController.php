<?php
namespace Controller\Admin\Ims\Popup;

use Controller\Admin\Ims\ImsControllerTrait;
use Request;

class ImsPopMaterialUpdateLogController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    public function index() {
        $this->setDefault();

        $iSno = (int)Request::get()->get('sno');

        $this->setData('iSno', $iSno);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
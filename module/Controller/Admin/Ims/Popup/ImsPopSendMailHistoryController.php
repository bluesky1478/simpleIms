<?php
namespace Controller\Admin\Ims\Popup;

use Controller\Admin\Ims\ImsControllerTrait;
use Request;

class ImsPopSendMailHistoryController extends \Controller\Admin\Controller {
    use ImsControllerTrait;

    public function index(){
        $this->setDefault();
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
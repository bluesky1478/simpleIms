<?php
namespace Controller\Admin\Ims\Popup;

use Request;
use Controller\Admin\Ims\ImsControllerTrait;

class ImsPopUpsertStylePlanRefController  extends \Controller\Admin\Controller {
    use ImsControllerTrait;

    public function index() {
        $this->setDefault();

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Request;

use Controller\Admin\Ims\ImsControllerTrait;
use Controller\Admin\Ims\ImsPsNkTrait;

class ImsPopSalesCustomerStatsController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    use ImsPsNkTrait;

    public function index() {
        $this->setDefault();

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
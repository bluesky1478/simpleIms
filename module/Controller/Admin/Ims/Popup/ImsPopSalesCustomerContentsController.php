<?php
namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Request;

use Controller\Admin\Ims\ImsControllerTrait;
use Controller\Admin\Ims\ImsPsNkTrait;

class ImsPopSalesCustomerContentsController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    use ImsPsNkTrait;

    public function index() {
        $this->setDefault();

        $this->setData('iSno', (int)Request::get()->get('sno'));
        $this->setData('aDefaultJson', $this->getJsonDefaultForm(ImsDBName::SALES_CUSTOMER));
        $this->setData('sPageMode', (string)Request::get()->get('mode'));

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
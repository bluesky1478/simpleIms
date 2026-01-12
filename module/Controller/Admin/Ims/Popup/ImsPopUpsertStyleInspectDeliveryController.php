<?php
namespace Controller\Admin\Ims\Popup;

use Request;
use Controller\Admin\Ims\ImsControllerTrait;
use SlComponent\Util\SlLoader;

class ImsPopUpsertStyleInspectDeliveryController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    public function index() {
        $this->setDefault();

        //리비전
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $this->setData('revReasonList', $imsService->getCode('workRev','작지변경사유'));
        $this->setData('revTypeList', $imsService->getCode('workRevType','작지변경구분'));

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
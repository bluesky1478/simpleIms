<?php
namespace Controller\Admin\Ims\Popup;

use Request;
use Controller\Admin\Ims\ImsControllerTrait;
use Component\Ims\ImsJsonSchema;
use SiteLabUtil\SlCommonUtil;

//수정은 이 페이지에서 안함. 등록만
class ImsPopUpsertEstimateController extends \Controller\Admin\Controller {
    use ImsControllerTrait;

    public function index() {
        $this->setDefault();

        $aSchema = ImsJsonSchema::ESTIMATE;
        $this->setData('aContentsSchema', $aSchema);
        $this->setData('sCurrDollerRatio', SlCommonUtil::getCurrentDollar());
        $this->setData('sCurrDollerRatioDt', date('Y-m-d'));

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
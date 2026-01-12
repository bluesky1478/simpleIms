<?php
namespace Controller\Admin\Ims\Popup;

use Controller\Admin\Ims\ImsControllerTrait;
use Request;
use SlComponent\Util\SlLoader;

class ImsPopStoredOutputRegController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2','ImsNkService');
    }

    public function index() {
        $this->setDefault();

        $iInputSno = (int)Request::get()->get('sno');
        if ($iInputSno === 0) {
            echo "접근오류";
            exit;
        }

        $aFabricInputInfo = $this->imsNkService->getStoredFabricInputInfo($iInputSno);
        if (!isset($aFabricInputInfo['sno'])) {
            echo "접근오류";
            exit;
        }

        $this->setData('chooseInput', $aFabricInputInfo);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
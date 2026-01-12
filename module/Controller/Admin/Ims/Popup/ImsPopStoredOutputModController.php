<?php

namespace Controller\Admin\Ims\Popup;

use Component\Ims\ImsDBName;
use Request;
use Controller\Admin\Ims\ImsControllerTrait;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class ImsPopStoredOutputModController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    private $imsNkService;

    public function __construct() {
        parent::__construct();
        $this->imsNkService = SlLoader::cLoad('imsv2','ImsNkService');
    }

    public function index() {
        $this->setDefault();
        $iOutputSno = (int)Request::get()->get('sno');
        $iInputSno = (int)Request::get()->get('inputSno');

        //출고수량, 출고사유 가져오기
        $searchVo = new SearchVo(['delFl=?', 'sno=?'], ['n',$iOutputSno]);
        $aInfo = DBUtil2::getOneBySearchVo(ImsDBName::STORED_FABRIC_OUT, $searchVo);
        if (!isset($aInfo['sno'])) {
            echo "접근오류";
            exit;
        }

        //출고수정폼에 보여질 데이터 가져오기(특히 현재수량)
        $aFabricInputInfo = $this->imsNkService->getStoredFabricInputInfo($iInputSno);
        if (!isset($aFabricInputInfo['sno'])) {
            echo "접근오류";
            exit;
        }
        $aFabricInputInfo['sno'] = $iOutputSno;
        unset($aFabricInputInfo['customerUsageSno'], $aFabricInputInfo['fabricSno'], $aFabricInputInfo['customerSno'], $aFabricInputInfo['unitPrice'], $aFabricInputInfo['inputQty']
            , $aFabricInputInfo['inputUnit'], $aFabricInputInfo['inputReason'], $aFabricInputInfo['inputLocation'], $aFabricInputInfo['inputMemo']
            , $aFabricInputInfo['inputDt'], $aFabricInputInfo['expireDt'], $aFabricInputInfo['outQty'], $aFabricInputInfo['customerName']
        );
        $aFabricInputInfo['outQty'] = $aInfo['outQty'];
        $aFabricInputInfo['outReason'] = $aInfo['outReason'];

        $this->setData('fabricOutputInfo', $aFabricInputInfo);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
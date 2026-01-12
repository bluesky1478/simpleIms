<?php
namespace Controller\Admin\Ims\Popup;

use Component\Database\DBNkIms;
use Controller\Admin\Ims\ImsControllerTrait;
use Request;
use SlComponent\Util\SlLoader;

class ImsPopStoredInputRegController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;

    private $imsNkService;
    private $imsService;

    public function __construct() {
        parent::__construct();
        $this->imsService = SlLoader::cLoad('ims','imsService');
        $this->imsNkService = SlLoader::cLoad('imsv2','ImsNkService');
    }

    public function index() {
        $this->setDefault();

        //원단리스트 가져오기, 고객사 리스트 가져오기
        $aFabric = $this->imsNkService->getStoredFabricList();
        $aFabricMap = [];
        foreach ($aFabric as $val) $aFabricMap[$val['sno']] = $val['fabricName'].' / '.$val['fabricMix'].' / '.$val['color'];
        $this->setData('fabricList', $aFabricMap);
        $this->setData('existFabricList', $aFabric);
        $this->setData('customerListMap', $this->imsService->getCustomerListMap());
        $this->setData('chooseFabricSno', (int)Request::get()->get('sno'));

        //DB 스키마 가져오기
        $this->setData('schemaTable', ['StoredFabric' => DBNkIms::tableImsStoredFabric(), 'StoredFabricInput' => DBNkIms::tableImsStoredFabricInput()]);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }

}
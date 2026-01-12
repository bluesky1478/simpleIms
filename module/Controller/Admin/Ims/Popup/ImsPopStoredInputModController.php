<?php
namespace Controller\Admin\Ims\Popup;

use Component\Database\DBNkIms;
use Controller\Admin\Ims\ImsControllerTrait;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class ImsPopStoredInputModController extends \Controller\Admin\Controller
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
        //입고정보 가져오기
        $iInputSno = (int)Request::get()->get('sno');
        $searchVo = new SearchVo(['a.delFl=?', 'b.delFl=?', 'b.sno=?'], ['n', 'n', $iInputSno]);
        $searchVo->setGroup('b.sno');
        $tableList = SlLoader::sqlLoad('Component\Ims\ImsStoredService', false)->getStoredInputTable();
        $aData = DBUtil2::getComplexListWithQuery($tableList, $searchVo, false, false, false)['list'];
        if (!isset($aData[0]['sno'])) {
            echo "접근오류";
            exit;
        }
        $aFabricSchema = DBNkIms::tableImsStoredFabric();
        $aFabricInputSchema = DBNkIms::tableImsStoredFabricInput();
        //필요없는 필드 빼기
        $aOutFld = ['regManagerSno', 'regDt', 'modDt', 'fabricSno', 'delFl'];
        foreach ($aFabricSchema as $key => $val) {
            if (in_array($val['val'], $aOutFld)) unset($aFabricSchema[$key]);
        }
        foreach ($aFabricInputSchema as $key => $val) {
            if (in_array($val['val'], $aOutFld)) unset($aFabricInputSchema[$key]);
        }
        //값 넣기
        foreach ($aData[0] as $key => $val) { //fld for
            if ($key == 'sno') {
                foreach ($aFabricInputSchema as $key2 => $val2) { //schema fld for
                    if ($val2['val'] == 'sno') $aFabricInputSchema[$key2]['def'] = $val;
                }
            } elseif ($key == 'fabricSno') {
                foreach ($aFabricSchema as $key2 => $val2) { //schema fld for
                    if ($val2['val'] == 'sno') $aFabricSchema[$key2]['def'] = $val;
                }
            } else {
                foreach ($aFabricSchema as $key2 => $val2) {
                    if ($val2['val'] == $key) {
                        $aFabricSchema[$key2]['def'] = $val == '0000-00-00' ? '' : $val;
                    }
                }
                foreach ($aFabricInputSchema as $key2 => $val2) {
                    if ($val2['val'] == $key) $aFabricInputSchema[$key2]['def'] = $val == '0000-00-00' ? '' : $val;
                }
            }
        }

        //DB 스키마 가져오기
        $this->setData('schemaTable', ['StoredFabric' => $aFabricSchema, 'StoredFabricInput' => $aFabricInputSchema]);

        //gnb, lnb 없애는 view단 구성
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }

}
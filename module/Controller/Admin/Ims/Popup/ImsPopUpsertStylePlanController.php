<?php
namespace Controller\Admin\Ims\Popup;

use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Component\Ims\NkCodeMap;
use Request;
use Controller\Admin\Ims\ImsPsNkTrait;
use Controller\Admin\Ims\ImsControllerTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;

class ImsPopUpsertStylePlanController extends \Controller\Admin\Controller
{
    use ImsControllerTrait;
    use ImsPsNkTrait;

    //스타일기획 등록/수정시 호출. 등록시에는 default form을 $this->setData(), 수정시에는 select해와서 기획정보form을 $this->setData()
    //이 방법 안좋음. DB로부터 뽑아낸 데이터를 vue.js 변수에 넣어줘야 하는데 줄바꿈이나 특수문자(')를 제대로 넣어주지 못함
    public function index() {
        $this->setDefault();

        $iProjectSno = (int)Request::get()->get('projectSno');
        $iStyleSno = (int)Request::get()->get('styleSno');
        if ($iStyleSno === 0 || $iProjectSno === 0) {
            echo "접근오류";
            exit;
        }

        //스타일정보(스타일명,코드), 프로젝트기획일정 가져오기
        $searchVo = new SearchVo('a.sno=?', $iStyleSno);
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PRODUCT ], 'field' => ["a.sno as styleSno, productName, styleCode, prdStyle, prdSeason, targetPrice, targetPrdCost, prdExQty"]],
            'b' => ['data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'a.projectSno = b.projectSno' ], 'field' => ["b.planScheMemo"]],
            'd' => ['data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = d.sno' ], 'field' => ["d.customerSno"]],
        ];
        $allData = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);
        $aStyleInfo = $aPlanList = [];
        $sPrdStyle = $sPrdSeason = '';
        foreach ($allData as $val) {
            if (count($aStyleInfo) === 0) {
                $aStyleInfo = SlCommonUtil::getAvailData($val, [
                    'customerSno',
                    'styleSno',
                    'productName',
                    'styleCode',
                    'planScheMemo',
                    'targetPrice',
                    'targetPrdCost',
                    'prdExQty',
                ]);
                $sPrdStyle = $val['prdStyle'];
                $sPrdSeason = $val['prdSeason'];
            }
            if ($val['sno'] != null) $aPlanList[$val['scheType']][$val['scheStep']] = $val['scheDt'];
        }
        $this->setData('aStyleInfo', $aStyleInfo);
        $this->setData('aPlanList', $aPlanList);
        $this->setData('sPrdStyle', $sPrdStyle);
        $this->setData('sPrdSeason', $sPrdSeason);
        $this->setData('iProjectSno', $iProjectSno);
        $this->setData('iStyleSno', $iStyleSno);

        //이미 등록된 스타일기획있으면 정보 가져옴 -> vue.js 변수에 반영할 배열에다가 값 넣기
        $iStylePlanSno = (int)Request::get()->get('sno');
        $aExistPlanInfo = ['refName'=>''];
        $bFlagShowCancelBtn = 'false'; //기획등록버튼 클릭으로 팝업창 진입시 수정취소버튼 안보이게 함
        if ($iStylePlanSno !== 0) {
            $bFlagShowCancelBtn = 'true';
            $aTableInfo=[
                'a' => ['data' => [ ImsDBName::PRODUCT_PLAN ], 'field' => ["a.*"]],
                'b' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN, 'LEFT OUTER JOIN', 'a.refStylePlanSno = b.sno' ], 'field' => ["if(b.sno is null, '미지정', b.refName) as refName"]],
            ];
            $aTmpExistPlanInfo = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), new SearchVo('a.sno=?', $iStylePlanSno), false, false, true);
            if (isset($aTmpExistPlanInfo[0]['sno'])) {
                $aExistPlanInfo = $aTmpExistPlanInfo[0];
                $aExistPlanInfo['changeQtyHan'] = NkCodeMap::PRODUCT_PLAN_CHANGE_QTY[$aExistPlanInfo['changeQty']];
                $aExistPlanInfo['prdGenderHan'] = NkCodeMap::PRODUCT_PLAN_GENDER[$aExistPlanInfo['prdGender']];
            }
        }
        $this->setData('bFlagShowCancelBtn', $bFlagShowCancelBtn);
        //스타일기획TABLE 스키마대로 배열정리(->vue.js 변수에 반영)
        $aTmpTableFldList = DBTableField::callTableFunction(ImsDBName::PRODUCT_PLAN);
        $aTmpTableFldList[] = ['val' => 'changeQtyHan', 'typ' => 'i', 'def' => null, 'name' => ''];
        $aTmpTableFldList[] = ['val' => 'prdGenderHan', 'typ' => 'i', 'def' => null, 'name' => ''];
        $aTableFldList = ['refName'=>$aExistPlanInfo['refName']];
        $aNotUpsertFlds = ['styleSno','regManagerSno','regDt','modDt'];

        //json으로 저장되는 컬럼의 default form 가져오기
        $aDefaultJson = $this->getJsonDefaultForm(ImsDBName::PRODUCT_PLAN);

        //환율 가져오기(현재환율, 등록시 저장된 환율)
        $fDollerRatio = SlCommonUtil::getCurrentDollar();
        $sCurrDollerRatio = $sSaveDollerRatio = $fDollerRatio;
        $sCurrDollerRatioDt = $sSaveDollerRatioDt = date('Y-m-d');
        if (count($aExistPlanInfo) > 0) {
            $sSaveDollerRatio = $aExistPlanInfo['dollerRatio'];
            $sSaveDollerRatioDt = $aExistPlanInfo['dollerRatioDt'];
        }
        $this->setData('sCurrDollerRatio', $sCurrDollerRatio);
        $this->setData('sCurrDollerRatioDt', $sCurrDollerRatioDt);
        $this->setData('sSaveDollerRatio', $sSaveDollerRatio);
        $this->setData('sSaveDollerRatioDt', $sSaveDollerRatioDt);

        foreach ($aTmpTableFldList as $val) {
            if (!in_array($val['val'], $aNotUpsertFlds)) {
                if (count($aExistPlanInfo) > 0) {
                    if (in_array($val['val'], array_keys($aDefaultJson))) {
                        if ($aExistPlanInfo[$val['val']] == null || $aExistPlanInfo[$val['val']] == '') $aTableFldList[$val['val']] = [];
                        else $aTableFldList[$val['val']] = json_decode(str_replace('\'','',$aExistPlanInfo[$val['val']]), true);
                    } else $aTableFldList[$val['val']] = addslashes(str_replace(["\n","\r","\r\n"],'<br/>',$aExistPlanInfo[$val['val']]));
                } else {
                    if (in_array($val['val'], ['sno','prdCustomerSno'])) $aTableFldList[$val['val']] = 0;
                    else if ($val['val'] == 'changeQty') $aTableFldList[$val['val']] = '1';
                    else if ($val['val'] == 'dollerRatio') $aTableFldList[$val['val']] = $fDollerRatio; //insert일때만 환율컬럼에 값넣기
                    else if ($val['val'] == 'dollerRatioDt') $aTableFldList[$val['val']] = date('Y-m-d'); //insert일때만 환율컬럼에 값넣기
                    else if (in_array($val['val'], array_keys($aDefaultJson))) $aTableFldList[$val['val']] = [];
                    else $aTableFldList[$val['val']] = '';
                }
            }
        }
        //fileList.stylePlanFile1, fileList.stylePlanFile2 배열정리(참고파일)
        $aTableFldList['fileList']['stylePlanFile1'] = ['title' => '등록된 파일이 없습니다.', 'memo' => '', 'files' => [], 'noRev' => null];
        $aTableFldList['fileList']['stylePlanFile2'] = ['title' => '등록된 파일이 없습니다.', 'memo' => '', 'files' => [], 'noRev' => null];
        if ($iStylePlanSno !== 0) {
            $searchVo = new SearchVo(['eachSno=?'], [$iStylePlanSno]);
            $searchVo->setWhere("fileDiv like 'stylePlanFile%'");
            $searchVo->setOrder('eachSno asc, fileDiv asc, rev desc');
            $fileTableInfo=[
                'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
            ];
            $aTmpFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $searchVo, false, false, true);
            if (count($aTmpFileList) > 0) {
                $aFileList = [];
                foreach ($aTmpFileList as $val) {
                    if (!isset($aFileList[$val['eachSno']][$val['fileDiv']])) $aFileList[$val['eachSno']][$val['fileDiv']] = $val;
                }
                if (isset($aFileList[$iStylePlanSno]['stylePlanFile1'])) {
                    $aFileInfo = $aFileList[$iStylePlanSno]['stylePlanFile1'];
                    $aTableFldList['fileList']['stylePlanFile1'] = [
                        'title' => 'Rev'.$aFileInfo['rev'].' '.$aFileInfo['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $aFileInfo['regDt']).')',
                        'memo' => str_replace("'",'',$aFileInfo['memo']),
                        'files' => json_decode(str_replace("'",'',$aFileInfo['fileList']), true),
                        'sno' => $aFileInfo['sno']
                    ];
                }
                if (isset($aFileList[$iStylePlanSno]['stylePlanFile2'])) {
                    $aFileInfo = $aFileList[$iStylePlanSno]['stylePlanFile2'];
                    $aTableFldList['fileList']['stylePlanFile2'] = [
                        'title' => 'Rev'.$aFileInfo['rev'].' '.$aFileInfo['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $aFileInfo['regDt']).')',
                        'memo' => str_replace("'",'',$aFileInfo['memo']),
                        'files' => json_decode(str_replace("'",'',$aFileInfo['fileList']), true),
                        'sno' => $aFileInfo['sno']
                    ];
                }
            }
        }
        //참고파일 이력에 쓰이는 파라메터
        $aTableFldList['customerSno'] = $aStyleInfo['customerSno'];
        $aTableFldList['projectSno'] = $iProjectSno; //기획이미지파일 업로드에도 쓰임
        $aTableFldList['styleSno'] = $iStyleSno;
        $aTableFldList['eachSno'] = $aTableFldList['sno'];

        //현재 시험성적서 갯수 구해오기
        $aFabricMateSnos = [];
        if (isset($aTableFldList['fabric']) && count($aTableFldList['fabric']) > 0) {
            foreach ($aTableFldList['fabric'] as $key => $val) {
                if ((int)$val['materialSno'] > 0) $aFabricMateSnos[] = (int)$val['materialSno'];
                $aTableFldList['fabric'][$key]['cntTestReportByCustomerSno'] = [];
            }
        }
        if (count($aFabricMateSnos) > 0) {
            $oSVTest = new SearchVo();
            $oSVTest->setWhere("materialSno in (".implode(",",$aFabricMateSnos).")");
            $oSVTest->setGroup('materialSno, testType, customerSno');
            $aTestTableInfo=[
                'a' => ['data' => [ ImsDBName::TEST_REPORT_FILL ], 'field' => ["a.materialSno, a.testType, a.customerSno, count(a.sno) as cntTest"]],
            ];
            $aTestList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTestTableInfo,false), $oSVTest);
            if (count($aTestList) > 0) {
                $aTestCntListByMaterialSno = [];
                foreach ($aTestList as $val) {
                    if ($val['testType'] == 1) $aTestCntListByMaterialSno[$val['materialSno']][$val['customerSno']] = (int)$val['cntTest'];
                }
                foreach ($aTableFldList['fabric'] as $key => $val) {
                    if (isset($aTestCntListByMaterialSno[$val['materialSno']])) {
                        $aTableFldList['fabric'][$key]['cntTestReportByCustomerSno'] = $aTestCntListByMaterialSno[$val['materialSno']];
                    }
                }
            }
        }

        $this->setData('aTableFldList', $aTableFldList);

        //스타일기획별로 저장되어 있는 고객제공샘플 가져오기 -> $aCustomerSample[측정항목][사이즈] = ['optionSize'=>'','optionName'=>'','optionValue'=>'','optionUnit'=>''];
        $aCustomerSample = [];
        $sCustomerSampleYn = 'n';
        if ($iStylePlanSno !== 0) {
            $aTmpCSSearchVo = new SearchVo('productPlanSno=?', $iStylePlanSno);
            $aTmpCSSearchVo->setOrder('sortNum asc');
            $aTmpCSList = DBUtil2::getListBySearchVo(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, $aTmpCSSearchVo);
            if (count($aTmpCSList) > 0) {
                $sCustomerSampleYn = 'y';
                $sPrevName = '';
                $iKey = 0;
                foreach ($aTmpCSList as $val) {
                    if ($sPrevName != '' && $sPrevName != $val['optionName']) $iKey++;
                    $sPrevName = $val['optionName'];
                    $aCustomerSample[$iKey][] = ['optionSize'=>$val['optionSize'],'optionName'=>$val['optionName'],'optionValue'=>$val['optionValue'],'optionUnit'=>$val['optionUnit']];
                }
            } else {
                //저장된 고객제공샘플이 없다면 확정스펙 항목대로 구성시킴
                if (count($aTableFldList['jsonFitSpec']) > 0) {
                    foreach($aTableFldList['jsonFitSpec'] as $val) {
                        //고객 제공 사이즈 추가시 defualt값 공백.
                        $aCustomerSample[] = [['optionSize'=>$aTableFldList['fitSize'],'optionName'=>$val['optionName'],'optionValue'=>'','optionUnit'=>$val['optionUnit']]];
                    }
                }
            }
        }
        $this->setData('aCustomerSample', $aCustomerSample);
        $this->setData('sCustomerSampleYn', $sCustomerSampleYn);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
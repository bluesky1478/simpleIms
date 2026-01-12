<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

class ImsProductPlanService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;

    public function __construct(){
        $this->dpData = [
            ['type' => 'c', 'col' => 15, 'class' => '', 'name' => 'productName', 'title' => '스타일', ],
            ['type' => 'c', 'col' => 20, 'class' => '', 'name' => 'planConcept', 'title' => '디자인 컨셉', ],
            ['type' => 'i', 'col' => 0, 'class' => '', 'name' => 'planQty', 'title' => '견적 수량', ],
            ['type' => 'i', 'col' => 0, 'class' => '', 'name' => 'planPrdCost', 'title' => '기획 생산가', ],
            ['type' => 'i', 'col' => 0, 'class' => '', 'name' => 'targetPrice', 'title' => '타겟 판매가', ],
            ['type' => 'c', 'col' => 15, 'class' => '', 'name' => 'mainFabric_fabricName', 'title' => '메인원단', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'mainFabric_onHandYn', 'title' => '생지유무', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'mainFabric_makePeriod', 'title' => '생산기간(생지有)', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'mainFabric_makePeriodNoOnHand', 'title' => '생산기간(생지無)', ],
        ];
    }

    public function getDisplay(){
        return $this->dpData;
    }

    public function getListStylePlan($params) {
        $iProductPlanSno = (int)$params['productPlanSno'];
        $searchVo = new SearchVo();
        if ($iProductPlanSno !== 0) { //상세 가져오기
            $searchVo->setWhere('a.sno = '.$iProductPlanSno);
        } else { //리스트 가져오기
            $iProjectSno = (int)$params['projectSno'];
            $iStyleSno = (int)$params['sno'];
            $searchData['condition'] = $params;
            $this->refineCommonCondition($searchData['condition'], $searchVo);
            if ($iProjectSno !== 0) $searchVo->setWhere('b.projectSno = '.$iProjectSno);
            if ($iStyleSno !== 0) $searchVo->setWhere('styleSno = '.$iStyleSno);
            $searchVo->setWhere('a.planConcept <> ""');
            $searchVo->setOrder('a.styleSno asc, a.sortNum asc');
        }
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PRODUCT_PLAN ], 'field' => ["a.*"]],
            'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
            'b' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = b.sno' ], 'field' => ["productName"]],
        ];
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        $aList = $allData['listData'];

        //스타일별로 rowspan
        $aCntStylePlanByStyleSno = [];
        foreach ($aList as $key => $val) {
            if (!isset($aCntStylePlanByStyleSno[$val['styleSno']])) $aCntStylePlanByStyleSno[$val['styleSno']] = 0;
            $aCntStylePlanByStyleSno[$val['styleSno']]++;
        }
        //json컬럼 가져오기
        $aTmpFlds = DBTableField::callTableFunction(ImsDBName::PRODUCT_PLAN);
        $aJsonFlds = [];
        foreach ($aTmpFlds as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
        }
        //리스트 정제
        foreach ($aList as $key => $val) {
            //json컬럼 변환
            foreach ($aJsonFlds as $val2) {
                if (isset($aList[$key][$val2]) && $aList[$key][$val2] != null && $aList[$key][$val2] != '') $aList[$key][$val2] = json_decode($val[$val2], true);
                else $aList[$key][$val2] = [];
            }
            if (isset($aCntStylePlanByStyleSno[$val['styleSno']])) {
                $aList[$key]['cntStylePlan'] = $aCntStylePlanByStyleSno[$val['styleSno']];
                unset($aCntStylePlanByStyleSno[$val['styleSno']]);
            }
            $aList[$key]['changeQtyHan'] = NkCodeMap::PRODUCT_PLAN_CHANGE_QTY[$val['changeQty']];
            //메인원단 정보
            $aDefaultFabric = ['no'=>'', 'attached'=>'', 'fabricName'=>'', 'fabricMix'=>'', 'color'=>'', 'spec'=>'', 'meas'=>'', 'unitPriceDoller'=>'', 'unitPrice'=>'', 'makeNational'=>'', 'moq'=>'', 'onHandYn'=>'', 'btYn'=>'', 'makePeriod'=>'', 'makePeriodNoOnHand'=>'', 'fabricCompany'=>'', ];
            foreach ($aDefaultFabric as $key3 => $val3) {
                $aList[$key]['mainFabric_'.$key3] = '';
            }
            foreach ($aList[$key]['fabric'] as $val2) {
                if($val2['no'] == 'G') {
                    foreach ($aDefaultFabric as $key3 => $val3) {
                        $aList[$key]['mainFabric_'.$key3] = $val2[$key3];
                    }
                    break;
                }
            }
            //이전소스. 리스트 return할때는 아래와 같은 식으로 값 안넣어도됨
//            $aList[$key]['mainFabric'] = ['materialSno'=>'0', 'code'=>'', 'no'=>'', 'attached'=>'', 'fabricName'=>'', 'fabricMix'=>'', 'color'=>'', 'spec'=>'', 'meas'=>'', 'unitPriceDoller'=>'', 'unitPrice'=>'', 'makeNational'=>'', 'moq'=>'', 'onHandYn'=>'', 'btYn'=>'', 'makePeriod'=>'', 'makePeriodNoOnHand'=>'', 'fabricCompany'=>'', 'memo'=>''];
//            foreach ($aList[$key]['fabric'] as $val2) {
//                if($val2['no'] == 'G') {
//                    $aList[$key]['mainFabric'] = $val2;
//                }
//            }
        }

        //현재 시험성적서 갯수 구해오기
        $aFabricMateSnos = [];
        foreach ($aList as $key => $val) {
            foreach ($val['fabric'] as $key2 => $val2) {
                $aFabricMateSnos[] = (int)$val2['materialSno'];
                $aList[$key]['fabric'][$key2]['cntTestReportByCustomerSno'] = [];
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

                foreach ($aList as $key => $val) {
                    foreach ($val['fabric'] as $key2 => $val2) {
                        if (isset($aTestCntListByMaterialSno[$val2['materialSno']])) {
                            $aList[$key]['fabric'][$key2]['cntTestReportByCustomerSno'] = $aTestCntListByMaterialSno[$val2['materialSno']];
                        }
                    }
                }
            }
        }

        $aFldList = $this->getDisplay();

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $aList,
            'fieldData' => $aFldList
        ];
    }

    public function getListStylePlanCustomerFit($params) {
        $iPlanSno = (int)$params['productPlanSno'];
        if ($iPlanSno > 0) {
            $oSV = new SearchVo('productPlanSno=?',$iPlanSno);
            $oSV->setOrder('sortNum asc');
            $aList = DBUtil2::getListBySearchVo(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, $oSV);
            $aReturn = [];
            if (count($aList) > 0) {
                foreach ($aList as $val) {
                    $aReturn[$val['optionName']][$val['optionSize']] = $val['optionValue'];
                }
            }
            return $aReturn;
        }
    }
}
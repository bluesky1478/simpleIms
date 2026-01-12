<?php
namespace Component\Ims;

use App;

use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;
use Component\Ims\ImsServiceSampleTrait;

class ImsProductSampleService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;
    use ImsServiceSampleTrait; //스타일(product)샘플의 첨부파일Div값을 가져오기 위함

    private $dpData;

    public function __construct(){
        $this->dpData = [
            ['type' => 'pop_detail_customer', 'col' => 7, 'class' => '', 'name' => 'customerName', 'title' => '고객사명', ],
            ['type' => 'pop_detail_project', 'col' => 3, 'class' => '', 'name' => 'projectSno', 'title' => '프로젝트번호', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'sampleTypeHan', 'title' => '샘플구분', ],
            ['type' => 'c', 'col' => 8, 'class' => '', 'name' => 'planConcept', 'title' => '스타일기획', ],
            ['type' => 'pop_detail_sample', 'col' => 13, 'class' => '', 'name' => 'productName', 'title' => '스타일/샘플명', ],
            //['type' => 'c', 'col' => 7, 'class' => 'ta-l pdl5', 'name' => 'styleCode', 'title' => '스타일코드', ],
            //['type' => 'pop_detail', 'col' => 12, 'class' => 'ta-l pdl5', 'name' => 'sampleName', 'title' => '샘플명', ],
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'factoryName', 'title' => '샘플실', ],
            ['type' => 'i', 'col' => 4, 'class' => '', 'name' => 'sampleCount', 'title' => '수량', ],
            ['type' => 'i', 'col' => 4, 'class' => '', 'name' => 'sampleCost', 'title' => '제작비용', ],
            ['type' => 'file', 'col' => 30, 'class' => 'pd0', 'name' => 'sampleFile1', 'title' => '파일', ], //type이 file일때 name값은 컬럼명이 아니라 fileDiv
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'sampleFactoryBeginDt', 'title' => '샘플투입일', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'sampleFactoryEndDt', 'title' => '샘플실마감일', ],
            ['type' => 'c', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'sampleMemo', 'title' => '비고', ],
        ];
    }
    public function getDisplay(){ return $this->dpData; }

    public function getListProductSample($params) {
        //~*~*~*~*~*~*~메소드 1개 안에서 getComplexListWithPaging() 함수 2개 이상 쓰면 안됨. 전역변수or포인터변수를 덮어쓰고 있는 것으로 추정(paging 배열)
        //첨부파일. 7,8,9,10(썸네일,샘플도안,마크도안,마카) 추가
        //첨부파일(리뷰서) 11,12,13(샘플사진앞면,샘플사진뒷면,샘플사진디테일)
        $aSampleFileDivs = $this->getSampleFileFieldList();
        $aSampleFileDivs = array_merge($aSampleFileDivs, ['sampleFile7','sampleFile8','sampleFile9','sampleFile10','sampleFile11','sampleFile12','sampleFile13']);

        //DB 테이블스키마 가져오기. json컬럼 가져오기
        $aTmpFldList = DBTableField::callTableFunction(ImsDBName::SAMPLE);
        $aJsonFldNms = [];
        foreach ($aTmpFldList as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFldNms[] = $val['val'];
        }

        //where 설정
        $searchData['condition'] = $params;
        $searchVo = new SearchVo('b.delFl=?','n');
        if (isset($params['customerSno']) && (int)$params['customerSno'] > 0) $searchVo->setWhere('a.customerSno = '.(int)$params['customerSno']);
        if (isset($params['upsertSnoGet'])) {
            if ((int)$params['upsertSnoGet'] > 0) $searchVo->setWhere('a.sno = '.(int)$params['upsertSnoGet']);
            else { //insert form return
                $aInsertForm = [];
                $aSkipUpsertFlds = ['customerSno', 'projectSno', 'recentLocation', 'jsonLocation', 'sampleConfirm', 'sampleConfirmManager', 'sampleConfirmDt', 'sampleFile1Approval', 'regDt', 'modDt'];
                foreach ($aTmpFldList as $val) {
                    if (!in_array($val['val'], $aSkipUpsertFlds)) {
                        $aInsertForm[$val['val']] = $val['def'];
                    }
                }
                $aInsertForm['sno'] = 0;
                //json컬럼
                foreach ($aInsertForm as $key => $val) {
                    if (in_array($key, $aJsonFldNms)) {
                        $aInsertForm[$key] = [];
                    }
                }
                //첨부파일 껍데기배열 만들기(스타일상세팝업(이전에는 modal) 기준)
                $aInsertForm['fileList'] = [];
                foreach ($aSampleFileDivs as $val) $aInsertForm['fileList'][$val] = ['title' => '등록된 파일이 없습니다.', 'memo' => '', 'files' => [], 'noRev' => null];

                return [
                    'list' => [$aInsertForm],
                ];
            }
        }

        $this->refineCommonCondition($searchData['condition'], $searchVo);
        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);

        //select 할 테이블정보(join 포함, 가져올 컬럼들 포함)
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SAMPLE ], 'field' => ["a.*, a.sno as eachSno"]],
            'cust' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ], 'field' => ["if(cust.sno is null, '미선택', cust.customerName) as customerName"]],
            'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.sampleManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as sampleManagerNm, if(manager.sno is null, '미선택', manager.cellPhone) as sampleManagerCellPhone"]],
            'b' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = b.sno' ], 'field' => ["b.productName, b.styleCode, substring(b.prdYear, 3, 2) as prdYear, b.prdStyle, b.prdSeason, b.prdGender"]],
            'c' => ['data' => [ ImsDBName::SAMPLE_FACTORY, 'LEFT OUTER JOIN', 'a.sampleFactorySno = c.sno' ], 'field' => ["if(c.sno is null, '미선택', c.factoryName) as factoryName, if(c.sno is null, '미선택', c.factoryPhone) as factoryPhone"]],
            'd' => ['data' => [ ImsDBName::SAMPLE_FACTORY, 'LEFT OUTER JOIN', 'a.patternFactorySno = d.sno' ], 'field' => ["if(d.sno is null, '미선택', d.factoryName) as patternFactoryName, if(d.sno is null, '미선택', d.factoryPhone) as patternFactoryPhone"]],
            'e' => ['data' => [ ImsDBName::PRODUCT_PLAN, 'LEFT OUTER JOIN', 'a.productPlanSno = e.sno' ], 'field' => ["if(e.sno is null, '미선택', e.planConcept) as planConcept, if(e.sno is null, 0, e.planPrdCost) as planPrdCost, if(e.sno is null, 0, e.targetPrdCost) as targetPrdCost, e.produceNational"]],
        ];
        //샘플 리스트 가져오기
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        $aSnos = [];
        foreach ($allData['listData'] as $key => $val) {
            $allData['listData'][$key] = SlCommonUtil::setDateBlank($val); //0000-00-00값이면 공백값으로 변경
            $aSnos[] = (int)$val['sno'];
            foreach ($val as $key2 => $val2) {
                if (in_array($key2, $aJsonFldNms)) {
                    if ($val2 != null && $val2 != '') $allData['listData'][$key][$key2] = json_decode($val2, true);
                    else $allData['listData'][$key][$key2] = [];
                }
            }

            $allData['listData'][$key]['sampleTypeHan'] = $val['sampleType'] == 9 ? '구버전' : NkCodeMap::SAMPLE_TYPE[$val['sampleType']];
            //성별 한글로 변환
            switch ($val['prdGender']) {
                case 'M' : $allData['listData'][$key]['prdGenderHan'] = '남자'; break;
                case 'F' : $allData['listData'][$key]['prdGenderHan'] = '여자'; break;
                default : $allData['listData'][$key]['prdGenderHan'] = '공용'; break;
            }

            //첨부파일 껍데기배열 만들기(스타일상세팝업(이전에는 modal) 기준)
            $allData['listData'][$key]['fileList'] = [];
            foreach ($aSampleFileDivs as $val2) $allData['listData'][$key]['fileList'][$val2] = ['title' => '등록된 파일이 없습니다.', 'memo' => '', 'files' => [], 'noRev' => null];
        }
        //샘플의 첨부파일(Div당 1개 파일정보만 담는다) 가져오기
        if (count($aSnos) > 0) {
            $searchVo = new SearchVo();
            $searchVo->setWhere("eachSno in (" . implode(',', $aSnos) . ")");
            $searchVo->setWhere("fileDiv in ('" . implode("','", array_values($aSampleFileDivs)) . "')");
            $searchVo->setOrder('eachSno asc, fileDiv asc, rev desc');
            $fileTableInfo=[
                'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
            ];
            $aTmpFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $searchVo, false, false, true);
            $aFileList = [];
            foreach ($aTmpFileList as $val) {
                if (!isset($aFileList[$val['eachSno']][$val['fileDiv']])) $aFileList[$val['eachSno']][$val['fileDiv']] = $val;
                //rev가 제일 높은거 하나만 가져와서 아래 라인 주석처리했음
                //$aFileList[$val['eachSno']][$val['fileDiv']][] = $val;
            }
            foreach ($allData['listData'] as $key => $val) {
                if (isset($aFileList[$val['sno']])) {
                    foreach ($aFileList[$val['sno']] as $key2 => $val2) { //fileDiv 반복. $key2 === fileDiv
                        $allData['listData'][$key]['fileList'][$key2] = [];
                        $allData['listData'][$key]['fileList'][$key2]['title'] = 'Rev' . $val2['rev'] . ' ' . $val2['regManagerName'] . '등록' . '(' . gd_date_format('y/m/d', $val2['regDt']) . ')';
                        $allData['listData'][$key]['fileList'][$key2]['files'] = json_decode($val2['fileList'], true);
                        $allData['listData'][$key]['fileList'][$key2]['memo'] = $val2['memo'];
                        $allData['listData'][$key]['fileList'][$key2]['sno'] = $val2['sno'];
                    }
                }
            }
        }

        //현재 시험성적서 갯수 구해오기
        $aFabricMateSnos = [];
        foreach ($allData['listData'] as $key => $val) {
            foreach ($val['fabric'] as $key2 => $val2) {
                $aFabricMateSnos[] = (int)$val2['materialSno'];
                $allData['listData'][$key]['fabric'][$key2]['cntTestReportByCustomerSno'] = [];
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

                foreach ($allData['listData'] as $key => $val) {
                    foreach ($val['fabric'] as $key2 => $val2) {
                        if (isset($aTestCntListByMaterialSno[$val2['materialSno']])) {
                            $allData['listData'][$key]['fabric'][$key2]['cntTestReportByCustomerSno'] = $aTestCntListByMaterialSno[$val2['materialSno']];
                        }
                    }
                }
            }
        }

        //필드정보 가져오기
        $aFldList = $this->getDisplay();

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => $aFldList
        ];
    }

    public function modifySampleRatioMulti($params) {
        if (isset($params['data']) && is_array($params['data']) && count($params['data']) > 0) {
            $aSampleSnos = [];
            foreach ($params['data'] as $key => $val) $aSampleSnos[] = (int)$key;
            $searchVo = new SearchVo();
            $searchVo->setWhere("sno in (" . implode(',', $aSampleSnos) . ")");
            $aList = DBUtil2::getListBySearchVo(ImsDBName::SAMPLE, $searchVo);
            $aListBySno = [];
            foreach ($aList as $val) {
                if ($val['dollerRatio'] != $params['data'][$val['sno']]) {
                    $aListBySno[$val['sno']] = $val;
                }
            }
            if (count($aListBySno) > 0) {
                foreach ($aListBySno as $key => $val) {
                    $fChgRatio = (float)$params['data'][$key];
                    if ($fChgRatio > 0) {
                        $aUpdate = ['fabric'=>'[]', 'subFabric'=>'[]', 'jsonUtil'=>'[]', 'jsonMark'=>'[]'];
                        foreach ($aUpdate as $key2 => $val2) {
                            if ($val[$key2] != null && $val[$key2] != '') {
                                $aJson = json_decode($val[$key2], true);
                                foreach ($aJson as $key3 => $val3) {
                                    if (isset($val3['unitPriceDoller']) && (float)$val3['unitPriceDoller'] > 0) {
                                        $aJson[$key3]['unitPrice'] = round((float)$val3['unitPriceDoller'] * $fChgRatio);
                                    }
                                }
                                $aUpdate[$key2] = json_encode($aJson);
                            }
                        }
                        $aUpdate['dollerRatio'] = $fChgRatio;
                        $aUpdate['dollerRatioDt'] = '0000-00-00';

                        DBUtil2::update(ImsDBName::SAMPLE, $aUpdate, new SearchVo('sno=?', $key));
                    }
                }
            }
        }
    }

    public function getListProductSampleGuide($params) {
        $oSV = new SearchVo();
        $oSV->setWhere("jsonConfirmGuide is not null");
        $oSV->setWhere("jsonConfirmGuide <> ''");
        $oSV->setWhere("jsonConfirmGuide <> '[]'");
        $aSampleList = DBUtil2::getListBySearchVo(ImsDBName::SAMPLE, $oSV);

        $aTmpList = $aList = $aTypeList = [];
        if (count($aSampleList) > 0) {
            foreach ($aSampleList as $val) {
                $aGuide = json_decode($val['jsonConfirmGuide'], true);
                foreach ($aGuide as $val2) {
                    if (!in_array($val2['guideType'], $aTypeList)) $aTypeList[] = $val2['guideType'];
                    if (!in_array($val2['guideType'].'__namku__'.$val2['guideContent'], $aTmpList)) $aTmpList[] = $val2['guideType'].'__namku__'.$val2['guideContent'];
                }
            }
        }
        sort($aTmpList);
        foreach ($aTmpList as $val) {
            $aTmp = explode('__namku__', $val);
            $aTmp2 = ['guideType'=>$aTmp[0], 'guideContent'=>$aTmp[1]];
            $aList[] = $aTmp2;
        }
        sort($aTypeList);

        return [
            'list' => $aList,
            'type_list' => $aTypeList,
        ];
    }



}
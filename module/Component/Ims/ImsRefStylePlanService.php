<?php
namespace Component\Ims;

use App;

use Component\Ims\ImsCodeMap;
use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

class ImsRefStylePlanService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;
    public function __construct() {
        $this->dpData = [
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'customerTypeHan', 'title' => '고객구분', ],
        ];
    }
    public function getDisplay(){ return $this->dpData; }

    //공용함수 : 스타일기획레퍼런스에 연결된 원부자재리스트 가져오기
    //사용처 : 스타일기획레퍼런스 upsert페이지, 스타일기획 upsert페이지(레퍼런스정보 가져와서 뿌려줄때)
    public function getJsonMateListFromRefMateList($mParam) {
        if (is_array($mParam)) $iRefSno = (int)$mParam['data'];
        else $iRefSno = (int)$mParam;

        $aReturn = [];
        $oSVMaterial = new SearchVo('refStylePlanSno=?', $iRefSno);
        $oSVMaterial->setOrder('sortNum');
        $aMaterialList = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_MATERIAL, $oSVMaterial);
        if (count($aMaterialList) > 0) {
            $aMatchFldNm = [
                'fabric' => ['eachSno'=>'materialSno', 'materialCode'=>'code', 'materialNo'=>'no', 'materialAttached'=>'attached', 'materialName'=>'fabricName', 'fabricMix'=>'fabricMix',
                    'materialColor'=>'color', 'materialSpec'=>'spec', 'materialQty'=>'meas', 'currencyUnit'=>'currencyUnit', 'unitPriceDoller'=>'unitPriceDoller', 'unitPrice'=>'unitPrice', 'makeNational'=>'makeNational', 'materialMoq'=>'moq', 'onHandYn'=>'onHandYn',
                    'btYn'=>'btYn', 'makePeriod'=>'makePeriod', 'makePeriodNoOnHand'=>'makePeriodNoOnHand', 'produceManagerSno'=>'produceManagerSno', 'fabricCompany'=>'fabricCompany', 'materialMemo'=>'memo', 'grpMaterialNames'=>'grpMaterialNames',
                ],
                'subFabric' => ['eachSno'=>'materialSno', 'materialCode'=>'code', 'materialNo'=>'no', 'materialName'=>'subFabricName',
                    'materialColor'=>'color', 'materialSpec'=>'spec', 'materialQty'=>'meas', 'currencyUnit'=>'currencyUnit', 'unitPriceDoller'=>'unitPriceDoller', 'unitPrice'=>'unitPrice', 'makeNational'=>'makeNational', 'materialMoq'=>'moq',
                    'produceManagerSno'=>'produceManagerSno', 'fabricCompany'=>'company', 'materialMemo'=>'memo', 'grpMaterialNames'=>'grpMaterialNames',
                ],
                'jsonMark' => ['eachSno'=>'materialSno', 'materialCode'=>'code', 'materialNo'=>'no', 'materialName'=>'subFabricName',
                    'materialColor'=>'color', 'materialSpec'=>'spec', 'materialQty'=>'meas', 'currencyUnit'=>'currencyUnit', 'unitPriceDoller'=>'unitPriceDoller', 'unitPrice'=>'unitPrice', 'makeNational'=>'makeNational', 'materialMoq'=>'moq',
                    'produceManagerSno'=>'produceManagerSno', 'fabricCompany'=>'company', 'materialMemo'=>'memo', 'grpMaterialNames'=>'grpMaterialNames',
                ],
                'jsonUtil' => ['eachSno'=>'materialSno', 'materialCode'=>'code', 'materialNo'=>'no', 'materialName'=>'utilName', 'materialQty'=>'utilQty', 'currencyUnit'=>'currencyUnit', 'unitPriceDoller'=>'unitPriceDoller', 'unitPrice'=>'unitPrice', 'produceManagerSno'=>'produceManagerSno', 'fabricCompany'=>'company', 'materialMemo'=>'memo', 'grpMaterialNames'=>'grpMaterialNames'],
                'jsonLaborCost' => ['eachSno'=>'etcCostSno', 'materialCode'=>'code', 'materialName'=>'name', 'materialQty'=>'costQty', 'unitPrice'=>'unitPrice', 'produceManagerSno'=>'produceManagerSno', 'fabricCompany'=>'company', 'materialMemo'=>'memo'],
                'jsonEtc' => ['eachSno'=>'etcCostSno', 'materialCode'=>'code', 'materialName'=>'name', 'materialQty'=>'costQty', 'unitPrice'=>'unitPrice', 'produceManagerSno'=>'produceManagerSno', 'fabricCompany'=>'company', 'materialMemo'=>'memo'],
            ];
            $aFabricMateSnos = [];
            foreach ($aMaterialList as $val) {
                $aTmp = ['sno'=>$val['sno']];
                foreach ($val as $key2 => $val2) {
                    if  (isset($aMatchFldNm[$val['materialType']][$key2])) $aTmp[$aMatchFldNm[$val['materialType']][$key2]] = $val2;
                }
                //원단이라면 현재 시험성적서 갯수 가져와야함
                if ($val['materialType'] == 'fabric') {
                    $aFabricMateSnos[] = (int)$val['eachSno'];
                    $aTmp['cntTestReportByCustomerSno'] = [];
                }

                $aReturn[$val['materialType']][] = $aTmp;
            }

            //원단은 ImsDBName::TEST_REPORT_FILL select from -> 현재 시험성적서갯수 표시해줌(cntTestReportByCustomerSno = [customerSno => 갯수, customerSno => 갯수, .....])
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
                foreach ($aReturn['fabric'] as $key => $val) {
                    if (isset($aTestCntListByMaterialSno[$val['materialSno']])) {
                        $aReturn['fabric'][$key]['cntTestReportByCustomerSno'] = $aTestCntListByMaterialSno[$val['materialSno']];
                    }
                }
            }


            //namkuuuuuuuuuu 원부자재리스트 띄울때마다(상세페이지 : ~했음~레퍼런스, ~했음~스타일기획, ~했음~샘플, 가견적(등록페이지ok. 수정페이지 확인필요), 작지-원부자재) 무조건 현재 시험성적서갯수를 가져올것(cntTestReportByCustomerSno는 검색모듈 알고리즘때문에 DB에 저장은 하지만 열람은 안함(upsert당시 시험성적서갯수))

        }
        if (is_array($mParam)) return ['data'=>$aReturn];
        else return $aReturn;
    }

    public function getListRefStylePlanAppendInfo($params) {
        $oSV = new SearchVo();
        $this->refineCommonCondition($params, $oSV);
        $this->setListSortNk($params['sort'], $oSV);

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND ], 'field' => ["a.*"]],
        ];
        if (!isset($params['page']) || (int)$params['page'] == 0) $params['page'] = gd_isset($params['condition']['page'], 1);
        if (!isset($params['pageNum']) || (int)$params['pageNum'] == 0) $params['pageNum'] = gd_isset($params['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $oSV, $params, false, true);

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => []
        ];
    }
    public function getListRefStylePlanAppendInfoSimple($params=[]) {
        $aReturn = [];
        $oSVAppend = new SearchVo();
        $oSVAppend->setOrder('sortNum');
        $aTmpAppendList = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_APPEND, $oSVAppend);
        foreach ($aTmpAppendList as $val) {
            if (!isset($aReturn[$val['infoType']])) $aReturn[$val['infoType']] = [];
            $aReturn[$val['infoType']][] = ['sno' => $val['sno'], 'infoName' => $val['infoName']];
        }

        return $aReturn;
    }

    public function setRefStylePlanAppendInfo($params) {
        $iResgisterSno = \Session::get('manager.sno');
        $sCurrDt = date('Y-m-d H:i:s');

        foreach ($params['list'] as $key => $val) {
            $iSno = (int)$val['sno'];
            unset($val['sno']);
            if ($iSno === 0) {
                $val['infoType'] = $params['infoType'];
                $val['regManagerSno'] = $iResgisterSno;
                if ($params['bFlagRunSearch'] != 'true') $val['sortNum'] = $key + 1;
                $val['regDt'] = $sCurrDt;

                DBUtil2::insert(ImsDBName::REF_PRODUCT_PLAN_APPEND, $val);
            } else {
                if ($params['bFlagRunSearch'] != 'true') $val['sortNum'] = $key + 1;
                $val['modDt'] = $sCurrDt;

                DBUtil2::update(ImsDBName::REF_PRODUCT_PLAN_APPEND, $val, new SearchVo('sno=?', $iSno));
            }
        }
    }

    public function removeRefStylePlanAppendInfo($params) {
        $iSno = isset($params['sno']) ? (int)$params['sno'] : 0;
        if ($iSno === 0) return ['data'=> '접근 오류'];

        $aExistRelationList = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, new SearchVo('infoSno=?', $iSno));
        if (count($aExistRelationList) > 0) return ['data'=> '스타일기획 레퍼런스와 연결되어 있으므로 삭제가 불가능합니다.'];

        DBUtil2::delete(ImsDBName::REF_PRODUCT_PLAN_APPEND, new SearchVo('sno=?', $iSno));
        return ['data'=> ''];
    }

    public function getListStylePlanRef($params) {
        $sTableNm = ImsDBName::REF_PRODUCT_PLAN;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
            'b1' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, 'LEFT OUTER JOIN', 'a.sno = b1.refStylePlanSno and b1.infoType = 1' ], 'field' => ["b1.sno as rel_sno1"]],
            'b2' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, 'LEFT OUTER JOIN', 'a.sno = b2.refStylePlanSno and b2.infoType = 2' ], 'field' => ["b2.sno as rel_sno2"]],
            'b3' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, 'LEFT OUTER JOIN', 'a.sno = b3.refStylePlanSno and b3.infoType = 3' ], 'field' => ["b3.sno as rel_sno3"]],
            'b4' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, 'LEFT OUTER JOIN', 'a.sno = b4.refStylePlanSno and b4.infoType = 4' ], 'field' => ["b4.sno as rel_sno4"]],
            'addinfo1' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND, 'LEFT OUTER JOIN', 'b1.infoSno = addinfo1.sno' ], 'field' => ["addinfo1.infoName as infoName1"]],
            'addinfo2' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND, 'LEFT OUTER JOIN', 'b2.infoSno = addinfo2.sno' ], 'field' => ["addinfo2.infoName as infoName2"]],
            'addinfo3' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND, 'LEFT OUTER JOIN', 'b3.infoSno = addinfo3.sno' ], 'field' => ["addinfo3.infoName as infoName3"]],
            'addinfo4' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND, 'LEFT OUTER JOIN', 'b4.infoSno = addinfo4.sno' ], 'field' => ["addinfo4.infoName as infoName4"]],
            'mate' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_MATERIAL, 'LEFT OUTER JOIN', 'a.sno = mate.refStylePlanSno' ], 'field' => ["mate.materialName"]],
            'c' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION, 'LEFT OUTER JOIN', 'a.sno = c.refStylePlanSno' ], 'field' => ["c.sno as customerRelationSno"]],
            'custinfo' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'c.customerSno = custinfo.sno' ], 'field' => ["custinfo.sno as custSno"]],
            'cate2' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'custinfo.busiCateSno = cate2.sno' ], 'field' => ["if(cate2.sno is null, '미선택', cate2.cateName) as cateName, if(cate2.sno is null, 0, cate2.parentBusiCateSno) as parentBusiCateSno"]],
            'cate1' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'cate2.parentBusiCateSno = cate1.sno' ], 'field' => ["if(cate1.sno is null, '미선택', cate1.cateName) as parentCateName"]],
            'mateDetail' => ['data' => [ ImsDBName::MATERIAL, 'LEFT OUTER JOIN', "mate.eachSno = mateDetail.sno and mate.materialType in ('fabric', 'subFabric', 'jsonUtil', 'jsonMark')" ], 'field' => ["mateDetail.sno as mateDetailSno"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리 -> 스타일기획 레퍼런스는 박스형이라 이거 별로 필요없음
            $aFldList = []; //$this->getDisplay();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList, ['a.sno']);

        $imsService = SlLoader::cLoad('ims','imsService');
        $aSeasonCodeList = SlCommonUtil::arrayAppKeyValue(DBUtil2::getList(ImsDBName::CODE, 'codeType', '시즌'),'codeValueEn','codeValueKr');
        $aStyleCodeList = $imsService->getCode('style','스타일');
        if (isset($params['upsertSnoGet'])) { //등록 or 수정(==상세)
            //$aReturn = ['info'=>~~~]
            //부가정보 리스트 가져오기
            $aReturn['info_append'] = $this->getListRefStylePlanAppendInfoSimple();

            $aReturn['info']['fabric'] = $aReturn['info']['subFabric'] = $aReturn['info']['jsonUtil'] = $aReturn['info']['jsonMark'] = $aReturn['info']['jsonLaborCost'] = $aReturn['info']['jsonEtc'] = [];
            $aReturn['info']['chkAppendInfo'][1] = $aReturn['info']['chkAppendInfo'][2] = $aReturn['info']['chkAppendInfo'][3] = $aReturn['info']['chkAppendInfo'][4] = [];
            if ($params['upsertSnoGet'] > 0) {
                $iRefSno = (int)$params['upsertSnoGet'];

                $aReturn['info']['refSeasonHan'] = $aSeasonCodeList[$aReturn['info']['refSeason']];
                $aReturn['info']['refStyleHan'] = $aStyleCodeList[$aReturn['info']['refStyle']];
                $aReturn['info']['refGenderHan'] = isset(NkCodeMap::PRODUCT_PLAN_GENDER[$aReturn['info']['refGender']]) ? NkCodeMap::PRODUCT_PLAN_GENDER[$aReturn['info']['refGender']] : '공용';
                //타입 선택옵션value's and 선택옵션text's 가져오기
                $aReturn['info']['refTypeHan'] = $this->convertCheckboxSumToArr(NkCodeMap::REF_PRODUCT_PLAN_TYPE, $aReturn['info']['refType'], 'text');
                $aReturn['info']['refTypeHan'] = implode(', ', $aReturn['info']['refTypeHan']);
                $aReturn['info']['refType'] = $this->convertCheckboxSumToArr(NkCodeMap::REF_PRODUCT_PLAN_TYPE, $aReturn['info']['refType']);
                //연결된 브랜드, 컨셉, 디자인, 부가기능 select from -> 선택sno's and 선택한 text's 담기
                $aReturn['info']['chkAppendInfoHan'][1] = $aReturn['info']['chkAppendInfoHan'][2] = $aReturn['info']['chkAppendInfoHan'][3] = $aReturn['info']['chkAppendInfoHan'][4] = [];
                $oSVAppendInfo = new SearchVo('refStylePlanSno=?', $iRefSno);
                $oSVAppendInfo->setOrder('b.infoType asc, b.sortNum asc');
                $aTableInfo=[
                    'a' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION ], 'field' => ["a.sno, a.infoSno"]],
                    'b' => ['data' => [ ImsDBName::REF_PRODUCT_PLAN_APPEND, 'LEFT OUTER JOIN', 'a.infoSno = b.sno' ], 'field' => ["b.infoType, b.infoName"]],
                ];
                $aAppendInfoList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), $oSVAppendInfo, false, false, true);
                if (count($aAppendInfoList) > 0) {
                    foreach ($aAppendInfoList as $val) {
                        $aReturn['info']['chkAppendInfo'][$val['infoType']][] = $val['infoSno'];
                        $aReturn['info']['chkAppendInfoHan'][$val['infoType']][] = $val['infoName'];
                    }
                }
                foreach ($aReturn['info']['chkAppendInfoHan'] as $key => $val) {
                    $aReturn['info']['chkAppendInfoHan'][$key] = implode(', ', $val);
                }

                //연결된 원부자재 리스트 select from -> 스타일기획Table스키마에 맞게 배열에 담기(이 부분 스타일기획 upsert페이지에서 레퍼런스정보 가져올때 쓰일예정)
                $aMateList = $this->getJsonMateListFromRefMateList($iRefSno);
                foreach ($aMateList as $key => $val) $aReturn['info'][$key] = $val;

                //연결된 고객사 리스트 select from
                $aCustomerParam = ['sort'=>'D,asc,a.sno,asc', 'sRadioSchRefStylePlanSno'=>$iRefSno];
                $sTableNm = ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION;
                $tableInfo=[
                    'a' => ['data' => [$sTableNm], 'field' => ["a.refStylePlanSno"]],
                    'b' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = b.sno' ], 'field' => ["b.sno as customerSno, customerName, contactName, if(use3pl='y', '예', '아니오') as use3pl, if(useMall='y', '예', '아니오') as useMall, customerCost, customerPrice"]],
                    'sales' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.salesManagerSno = sales.sno' ], 'field' => ["if(sales.sno is null, '미선택', sales.managerNm) as salesManagerNm"]],
                    'cate' => ['data' => [ ImsDBName::BUSI_CATE, 'LEFT OUTER JOIN', 'b.busiCateSno = cate.sno' ], 'field' => ["if(cate.sno is null, '미지정', cate.cateName) as cateName"]],
                ];
                $aTmp = $this->fnRefineListUpsertForm($aCustomerParam, $sTableNm, $tableInfo, []);
                $aReturn['info_customer'] = [];
                foreach ($aTmp['list'] as $val) {
                    unset($val['refStylePlanSno']);
                    $aReturn['info_customer'][] = $val;
                }

                //현재환율 가져오기
                $aReturn['info_curr_doller']['dollerRatio'] = SlCommonUtil::getCurrentDollar();
                $aReturn['info_curr_doller']['dollerRatioDt'] = date('Y-m-d');
            } else {
                //등록인 경우 기본값(환율정보 가져오기, null값 공백값으로 대체)
                $aReturn['info']['dollerRatio'] = SlCommonUtil::getCurrentDollar();
                $aReturn['info']['dollerRatioDt'] = date('Y-m-d');
                $aReturn['info_curr_doller']['dollerRatio'] = $aReturn['info']['dollerRatio'];
                $aReturn['info_curr_doller']['dollerRatioDt'] = $aReturn['info']['dollerRatioDt'];
                $aReturn['info']['refSeason'] = $aReturn['info']['refStyle'] = $aReturn['info']['refGender'] = '';
                $aReturn['info']['refType'] = $aReturn['info_customer'] = [];
            }
        } else {
            //$aReturn = ['pageEx' => ~~~, 'page' => ~~~, 'list' => [~~~], 'fieldData' => [~~~]]


        }

        return $aReturn;
    }

    public function setStylePlanRef($params) {
        $iRefSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        $sCurrDt = date('Y-m-d H:i:s');
        $iRegManagerSno = \Session::get('manager.sno');
        //메인원단 정보(단가,생지유무) 넣기
        $params['data']['mainFabricUnitPrice'] = 0;
        $params['data']['mainFabricOnHandYn'] = '';
        if (count($params['data']['fabric']) > 0) {
            foreach ($params['data']['fabric'] as $val) {
                if ($val['no'] == 'G') {
                    $params['data']['mainFabricUnitPrice'] = $val['unitPrice'];
                    $params['data']['mainFabricOnHandYn'] = $val['onHandYn'];
                    break;
                }
            }
        }

        if ($iRefSno === 0) {
            $params['data']['regManagerSno'] = $iRegManagerSno;
            $params['data']['regDt'] = $sCurrDt;
            $iRefSno = DBUtil2::insert(ImsDBName::REF_PRODUCT_PLAN, $params['data']);
        } else {
            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::REF_PRODUCT_PLAN, $params['data'], new SearchVo('sno=?', $iRefSno));
        }

        //부가정보 릴레이션table insert or delete
        $aChangeInfoSnos = $aExistInfoSnos = $aInsertInfoSnos = $aDeleteInfoSnos = [];
        foreach ($params['data']['chkAppendInfo'] as $val) {
            foreach ($val as $val2) $aChangeInfoSnos[] = (int)$val2;
        }
        $aTmp = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, new SearchVo('refStylePlanSno=?', $iRefSno));
        foreach ($aTmp as $val) $aExistInfoSnos[] = (int)$val['infoSno'];
        $aInsertInfoSnos = array_diff($aChangeInfoSnos, $aExistInfoSnos);
        $aDeleteInfoSnos = array_diff($aExistInfoSnos, $aChangeInfoSnos);
        if (count($aInsertInfoSnos) > 0) {
            //부가정보sno별 부가정보유형(infoType) 가져오기
            $aInfoTypeByInfoSno = [];
            $oSVAppendInfo = new SearchVo();
            $oSVAppendInfo->setWhere("sno in (".implode(",",$aInsertInfoSnos).")");
            $aTmpInfoTypeByInfoSno = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_APPEND, $oSVAppendInfo);
            foreach ($aTmpInfoTypeByInfoSno as $val) $aInfoTypeByInfoSno[$val['sno']] = $val['infoType'];

            foreach ($aInsertInfoSnos as $val) {
                DBUtil2::insert(ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, ['refStylePlanSno'=>$iRefSno, 'infoSno'=>$val, 'infoType'=>$aInfoTypeByInfoSno[$val]]);
            }
        }
        if (count($aDeleteInfoSnos) > 0) {
            $oDeleteSV = new SearchVo('refStylePlanSno=?', $iRefSno);
            $oDeleteSV->setWhere("infoSno in (".implode(",",$aDeleteInfoSnos).")");
            DBUtil2::delete(ImsDBName::REF_PRODUCT_PLAN_APPEND_RELATION, $oDeleteSV);
        }

        //고객사 릴레이션table insert or delete
        $aChangeCustomerSnos = $aExistCustomerSnos = $aInsertCustomerSnos = $aDeleteCustomerSnos = [];
        $aChgCustomerList = (array)$params['data_cust'];
        foreach ($aChgCustomerList as $val) {
            if ((int)$val['customerSno'] > 0) $aChangeCustomerSnos[] = (int)$val['customerSno'];
        }
        $aTmp = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION, new SearchVo('refStylePlanSno=?', $iRefSno));
        foreach ($aTmp as $val) $aExistCustomerSnos[] = (int)$val['customerSno'];
        $aInsertCustomerSnos = array_diff($aChangeCustomerSnos, $aExistCustomerSnos);
        $aDeleteCustomerSnos = array_diff($aExistCustomerSnos, $aChangeCustomerSnos);
        if (count($aInsertCustomerSnos) > 0) {
            foreach ($aInsertCustomerSnos as $val) {
                DBUtil2::insert(ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION, ['refStylePlanSno'=>$iRefSno, 'customerSno'=>$val]);
            }
        }
        if (count($aDeleteCustomerSnos) > 0) {
            $oDeleteSV = new SearchVo('refStylePlanSno=?', $iRefSno);
            $oDeleteSV->setWhere("customerSno in (".implode(",",$aDeleteCustomerSnos).")");
            DBUtil2::delete(ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION, $oDeleteSV);
        }

        //원부자재 릴레이션table insert or update or delete
        $aFabricKeyNms = array_keys(NkCodeMap::REF_PRODUCT_PLAN_MATERIAL_TYPE);
        $aChangeMaterialList = $aExistMateRelationSnos = [];
        foreach ($params['data'] as $key => $val) {
            if (in_array($key, $aFabricKeyNms)) {
                $aChangeMaterialList[$key] = $val;
            }
        }
        $aTmp = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_MATERIAL, new SearchVo('refStylePlanSno=?', $iRefSno));
        foreach ($aTmp as $val) $aExistMateRelationSnos[] = (int)$val['sno'];
        if (count($aChangeMaterialList) > 0) {
            //$aMatchColNm[DB컬럼명] = [매칭되는 json키Name...];
            $aMatchColNm = [
                'eachSno'=>['materialSno','etcCostSno'], 'materialCode'=>['code'], 'materialNo'=>['no'],
                'materialAttached'=>['attached'], 'materialName'=>['fabricName','subFabricName','utilName','name'],
                'fabricMix'=>['fabricMix'], 'materialColor'=>['color'], 'materialSpec'=>['spec'],
                'materialQty'=>['meas','utilQty','costQty'], 'currencyUnit'=>['currencyUnit'], 'unitPriceDoller'=>['unitPriceDoller'], 'unitPrice'=>['unitPrice'],
                'makeNational'=>['makeNational'], 'materialMoq'=>['moq'], 'onHandYn'=>['onHandYn'], 'btYn'=>['btYn'],
                'makePeriod'=>['makePeriod'], 'makePeriodNoOnHand'=>['makePeriodNoOnHand'],
                'produceManagerSno'=>['produceManagerSno'], 'fabricCompany'=>['fabricCompany','company'], 'materialMemo'=>['memo'], 'grpMaterialNames'=>['grpMaterialNames'],
            ];
            foreach ($aChangeMaterialList as $key => $val) { //$key === materialType, $val === 원부자재rows(Array)
                foreach ($val as $key2 => $val2) { //$key2 === 0부터정수, $val2 === 원부자재row(Array)
                    $aTmp = [];
                    foreach ($val2 as $key3 => $val3) { //$key3 === json키Name, $val3 === 값
                        foreach ($aMatchColNm as $key4 => $val4) { //$key4 === DB컬럼명, $val4 === 매치되는 json키Name들(Array)
                            if (in_array($key3, $val4)) {
                                if (is_array($val3)) $aTmp[$key4] = json_decode($val3);
                                else $aTmp[$key4] = $val3;
                                break;
                            }
                        }
                    }
                    $aTmp['sortNum'] = $key2 + 1;
                    if (!isset($val2['sno']) || (int)$val2['sno'] == 0) {
                        //insert
                        $aTmp['refStylePlanSno'] = $iRefSno;
                        $aTmp['materialType'] = $key;
                        $aTmp['regDt'] = $sCurrDt;
                        DBUtil2::insert(ImsDBName::REF_PRODUCT_PLAN_MATERIAL, $aTmp);
                    } else {
                        //update
                        $iRelationSno = (int)$val2['sno'];
                        $aTmp['modDt'] = $sCurrDt;
                        DBUtil2::update(ImsDBName::REF_PRODUCT_PLAN_MATERIAL, $aTmp, new SearchVo('sno=?', $iRelationSno));

                        $aExistMateRelationSnos = array_diff($aExistMateRelationSnos, [$iRelationSno]);
                    }
                }
            }
        }
        //delete
        if (count($aExistMateRelationSnos) > 0) {
            $oDeleteSV2 = new SearchVo('refStylePlanSno=?', $iRefSno);
            $oDeleteSV2->setWhere("sno in (".implode(",",$aExistMateRelationSnos).")");
            DBUtil2::delete(ImsDBName::REF_PRODUCT_PLAN_MATERIAL, $oDeleteSV2);
        }

    }

}
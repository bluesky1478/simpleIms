<?php

namespace Component\Imsv2;


use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Component\Ims\ImsService;
use Component\Ims\ImsServiceTrait;
use Component\Ims\ImsServiceSortNkTrait;
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
use Controller\Admin\Ims\ImsPsNkTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SlLoader;

class ImsNkService
{
    use ImsServiceTrait;
    use ImsServiceSortNkTrait;
    use ImsPsNkTrait;

    //원단 리스트 가져오기(입고등록/수정시 호출)
    public function getStoredFabricList()
    {
        $searchVo = new SearchVo('1=?', '1');
        return DBUtil2::getListBySearchVo(ImsDBName::STORED_FABRIC, $searchVo);
    }

    //원단 입고정보 가져오기(+출고수량 -합산)
    public function getStoredFabricInputInfo($iInputSno)
    {
        $searchVo = new SearchVo(['a.delFl=?', 'b.delFl=?', 'b.sno=?'], ['n', 'n', $iInputSno]);
        $searchVo->setGroup('b.sno');

        $tableList = SlLoader::sqlLoad('Component\Ims\ImsStoredService', false)->getStoredInputTable();
        $aData = DBUtil2::getComplexListWithQuery($tableList, $searchVo, false, false, true)['list'];

        if (!isset($aData[0]['sno'])) {
            return [];
        } else {
            $sCNm = '';
            if ($aData[0]['inputOwn'] == 3 && $aData[0]['customerName'] != null) $sCNm = ' (' . $aData[0]['customerName'] . ')';
            $aData[0]['inputOwn'] = \Component\Ims\NkCodeMap::STORED_INPUT_OWN[$aData[0]['inputOwn']] . $sCNm;
            $aData[0]['remainQty'] = $aData[0]['inputQty'] - (int)$aData[0]['outQty'];
            return $aData[0];
        }
    }

    //프로젝트별 부가판매/구매 테이블구조 가져오기
    public function getTableSchemeAddedBuySale() {
        $aTmpReturn = DBTableField::callTableFunction(ImsDBName::ADDED_B_S);
        $aReturn = [];
        foreach ($aTmpReturn as $val) {
            $aReturn[$val['val']] = $val['typ'] == 'i' ? (int)$val['def'] : $val['def'];
        }
        $iResgisterSno = \Session::get('manager.sno');
        $aReturn['regManagerSno'] = $iResgisterSno;
        $aReturn['buyManagerSnoHan'] = '없음';
        unset($aReturn['modDt'], $aReturn['regDt']);

        return $aReturn;
    }

    //프로젝트 간략리스트 가져오기
    public function getListProjectSimple($params) {
        $iCustomerSno = (int)$params['customerSno'];
        $searchVo = new SearchVo();
        if ($iCustomerSno !== 0) $searchVo->setWhere("a.customerSno = $iCustomerSno ");
        if (isset($params['projectSnos']) && is_array($params['projectSnos']) && count($params['projectSnos']) > 0) $searchVo->setWhere("a.sno in (" . implode(',', $params['projectSnos']) . ") ");
        $searchVo->setGroup('a.sno');
        $searchVo->setOrder('a.regDt desc');
        $fileTableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT ], 'field' => ["a.sno"]],
            'b' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', "a.sno = b.projectSno and b.delFl = 'n'" ], 'field' => ["count(b.sno) as cntStyle, productName"]],
        ];
        $aTmpList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $searchVo, false, false, true);
        $aList = [];
        foreach ($aTmpList as $val) {
            $aTmp = ['sno'=>$val['sno'], 'projectName'=>$val['productName']];
            $aTmp['projectName'] .= $val['cntStyle'] == 1 ? ' 1건' : ' 외 '.($val['cntStyle']-1).'건';
            $aList[] = $aTmp;
        }

        return $aList;
    }
    //스타일 간략리스트 가져오기
    public function getListStyleSimple($params) {
        $iProjectSno = (int)$params['projectSno'];
        $searchVo = new SearchVo();
        if ($iProjectSno !== 0) $searchVo->setWhere("a.projectSno = $iProjectSno ");
        if (isset($params['styleSnos']) && is_array($params['styleSnos']) && count($params['styleSnos']) > 0) $searchVo->setWhere("a.sno in (" . implode(',', $params['styleSnos']) . ") ");
        $searchVo->setOrder('a.regDt desc');

        $iSchLevel = (int)$params['sch_level'];
        $aList = [];
        if ($iSchLevel == 0) { //기본
            $fileTableInfo=[
                'a' => ['data' => [ ImsDBName::PRODUCT ], 'field' => ["a.sno, productName, styleCode"]],
            ];
            $aTmpList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $searchVo, false, false, true);
            foreach ($aTmpList as $val) {
                $aTmp = ['sno'=>$val['sno'], 'productName'=>$val['productName'].' ('.$val['styleCode'].')'];
                $aList[] = $aTmp;
            }
        } else if ($iSchLevel == 1 || $iSchLevel == 2) { //생산스케쥴관리 -> 생산정보 -> QC/인라인 검수관리 클릭으로 호출했을때 or 납품보고서 작성폼인쇄
            $fileTableInfo=[
                'a' => ['data' => [ ImsDBName::PRODUCT ], 'field' => ["productName, sizeSpec, produceCompanySno, prdExQty, projectSno, customerSno, a.styleCode"]],
                'cust' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ], 'field' => ["if(cust.sno is null, '미선택', cust.customerName) as customerName"]],
                'b' => ['data' => [ ImsDBName::PRODUCT_INSPECT, 'LEFT OUTER JOIN', 'a.sno = b.styleSno' ], 'field' => ["if(b.sno is null, 0, b.sno) as sno, jsonInspectList, jsonInspectSizeSpec, jsonInspectCheck, jsonInspectComment1, jsonInspectComment2"]],
                'pc' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.produceCompanySno = pc.sno' ], 'field' => ["if(pc.sno is null, '미지정', pc.managerNm) as produceCompanyName"]],
            ];
            $aTmpList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $searchVo, false, false, false);
            $aList = $aTmpList[0];
            $aList['sizeSpec'] = json_decode($aList['sizeSpec'], true);
            $aList['jsonInspectList'] = $aList['sno'] == 0 ? [] : json_decode(gd_htmlspecialchars_stripslashes($aList['jsonInspectList']), true);
            $aList['jsonInspectSizeSpec'] = $aList['sno'] == 0 ? [] : json_decode(gd_htmlspecialchars_stripslashes($aList['jsonInspectSizeSpec']), true);
            $aList['jsonInspectCheck'] = $aList['sno'] == 0 ? [] : json_decode(gd_htmlspecialchars_stripslashes($aList['jsonInspectCheck']), true);
            $aList['jsonInspectComment1'] = $aList['sno'] == 0 ? [] : json_decode(gd_htmlspecialchars_stripslashes($aList['jsonInspectComment1']), true);
            $aList['jsonInspectComment2'] = $aList['sno'] == 0 ? [] : json_decode(gd_htmlspecialchars_stripslashes($aList['jsonInspectComment2']), true);
            $aList['chkDefaultForm'] = [
                ['cntType'=>0, 'chkType'=>'완성', 'chkTitle'=>'작업지시서 준수', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'완성', 'chkTitle'=>'SPEC 준수', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'완성', 'chkTitle'=>'원 부자재 사양 준수', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'완성', 'chkTitle'=>'로고 위치 준수', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'봉제', 'chkTitle'=>'디자인 사양', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'봉제', 'chkTitle'=>'봉제 퀄리티', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'봉제', 'chkTitle'=>'좌우 비대칭', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'핏', 'chkTitle'=>'주머니 깊이', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'핏', 'chkTitle'=>'사이즈 점검', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'핏', 'chkTitle'=>'단차 및 로고 위치', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
                ['cntType'=>0, 'chkType'=>'핏', 'chkTitle'=>'기타 수정사항', 'qcPoint'=>'', 'qcDesc'=>'', 'inlinePoint'=>'', 'inlineDesc'=>''],
            ];
            //첨부파일 select
            $aList['fileList'] = [];
            if ($aList['sno'] > 0) {
                //차수별로 최근 것만 담기
                $oSVFile = new SearchVo('eachSno=?', $aList['sno']);
                $oSVFile->setWhere("fileDiv like 'styleInspect%'");
                $oSVFile->setOrder('sno desc');
                $fileTableInfo=[
                    'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                    'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
                ];
                $aFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $oSVFile, false, false, true);
                if (count($aFileList) > 0) {
                    foreach ($aFileList as $val) {
                        if (!isset($aList['fileList'][$val['fileDiv']])) {
                            $aList['fileList'][$val['fileDiv']] = [
                                'title' => 'Rev'.$val['rev'].' '.$val['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $val['regDt']).')',
                                'memo' => str_replace("'",'',$val['memo']),
                                'files' => json_decode(str_replace("'",'',$val['fileList']), true),
                                'sno' => $val['sno']
                            ];
                        }
                    }
                }
            }
            //납품보고서 - 샘플도안 이미지 가져오기(작업지시서로부터)
            if ($iSchLevel == 2 && $aList['sno'] > 0 && isset($params['styleSnos']) && is_array($params['styleSnos']) && count($params['styleSnos']) > 0) {
                //차수별로 최근 것만 담기
                $oSVFile = new SearchVo('styleSno=?', $params['styleSnos'][0]);
                $oSVFile->setWhere("fileDiv = 'fileSpec'");
                $oSVFile->setOrder('sno desc');
                $fileTableInfo=[
                    'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                    'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
                ];
                $aFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($fileTableInfo,false), $oSVFile, false, false, true);
                if (count($aFileList) > 0) {
                    foreach ($aFileList as $val) {
                        if (!isset($aList['fileList'][$val['fileDiv']])) {
                            $aList['fileList'][$val['fileDiv']] = [
                                'title' => 'Rev'.$val['rev'].' '.$val['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $val['regDt']).')',
                                'memo' => str_replace("'",'',$val['memo']),
                                'files' => json_decode(str_replace("'",'',$val['fileList']), true),
                                'sno' => $val['sno']
                            ];
                        }
                    }
                }
            }
        }

        return $aList;
    }

    //게시물 수정이력 가져오기
    public function getListUpdateHistory($params) {
        $iTableType = (int)$params['type'];
        $iEachSno = (int)$params['sno'];
        if ($iTableType === 0 || $iEachSno === 0) {
            //이 함수에서 code, message 값을 return해도 frontend에서는 무조건 code:200 으로 받아짐
//            $aReturn['message'] = '접근오류';
            return [];
        }

        $searchVo = new SearchVo(['a.tableType=?','a.eachSno=?'], [$iTableType, $iEachSno]);
        $searchVo->setOrder('a.regDt desc, a.sno asc');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::UPDATE_HISTORY_NK ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["reg.managerNm"]],
        ];
        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);
        //프로젝트명, 스타일명 뽑아오기
        $aPrjSnos = $aStyleSnos = $aPrjNmByPrjSno = $aStyleNmByStyleSno = [];
        foreach ($aList as $val) {
            if ($val['fldName'] == 'projectSno') {
                $aPrjSnos[] = (int)$val['beforeValue'];
                $aPrjSnos[] = (int)$val['afterValue'];
            } else if ($val['fldName'] == 'styleSno') {
                $aStyleSnos[] = (int)$val['beforeValue'];
                $aStyleSnos[] = (int)$val['afterValue'];
            }
        }
        if (count($aPrjSnos) > 0) {
            $aTmp = $this->getListProjectSimple(['projectSnos'=>$aPrjSnos]);
            foreach ($aTmp as $val) $aPrjNmByPrjSno[$val['sno']] = $val['projectName'];
        }
        if (count($aStyleSnos) > 0) {
            $aTmp = $this->getListStyleSimple(['styleSnos'=>$aStyleSnos]);
            foreach ($aTmp as $val) $aStyleNmByStyleSno[$val['sno']] = $val['productName'];
        }
        //데이터 정제
        foreach ($aList as $key => $val) {
            //projectSno || styleSno 인 경우 sno가 아니라 text 보여줌
            if ($val['fldName'] == 'projectSno') {
                $aList[$key]['fldNameHan'] = str_replace(' 일련번호','', $val['fldNameHan']);
                $aList[$key]['beforeValue'] = isset($aPrjNmByPrjSno[$val['beforeValue']]) ? $aPrjNmByPrjSno[$val['beforeValue']] : $val['beforeValue'];
                $aList[$key]['afterValue'] = isset($aPrjNmByPrjSno[$val['afterValue']]) ? $aPrjNmByPrjSno[$val['afterValue']] : $val['afterValue'];
            } else if ($val['fldName'] == 'styleSno') {
                $aList[$key]['fldNameHan'] = str_replace(' 일련번호','', $val['fldNameHan']);
                $aList[$key]['beforeValue'] = isset($aStyleNmByStyleSno[$val['beforeValue']]) ? $aStyleNmByStyleSno[$val['beforeValue']] : $val['beforeValue'];
                $aList[$key]['afterValue'] = isset($aStyleNmByStyleSno[$val['afterValue']]) ? $aStyleNmByStyleSno[$val['afterValue']] : $val['afterValue'];
            }
            if ($iTableType === 1) { //프로젝트/스타일 이슈 수정이력
                if ($val['fldName'] == 'issueType') unset($aList[$key]);
                else if ($val['fldName'] == 'issueSt') {
                    $aList[$key]['beforeValue'] = NkCodeMap::PROJECT_ISSUE_ST[$val['beforeValue']];
                    $aList[$key]['afterValue'] = NkCodeMap::PROJECT_ISSUE_ST[$val['afterValue']];
                }
            }


        }

        return $aList;
    }

    //입고리스트(고객상세창에서 호출)
    public function getListStoredOfCustom($params)
    {
        $iCustomSno = (int)$params['customerSno'];
        if ($iCustomSno === 0) {
            return ['pageEx' => [], 'page' => [], 'list' => []];
        }
        $searchVo = new SearchVo(['a.delFl=?', 'b.delFl=?', 'a.customerUsageSno=?'], ['n', 'n', $iCustomSno]);
        $searchVo->setGroup('b.sno');
        $this->setListSortNk($params['sort'], $searchVo);
        $searchData = [
            'page' => gd_isset($params['page'], 1),
            'pageNum' => gd_isset($params['pageNum'], 999),
        ];

        $storedService = SlLoader::sqlLoad('Component\Ims\ImsStoredService', false);
        $allData = DBUtil2::getComplexListWithPaging($storedService->getStoredInputTable(), $searchVo, $searchData, false, true);
        foreach ($allData['listData'] as $key => $val) {
            $allData['listData'][$key]['inputOwn'] = NkCodeMap::STORED_INPUT_OWN[$val['inputOwn']];
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData']
        ];
    }

    //댓글리스트 가져오기
    public function getListReply($params)
    {
        $aParams = $params;
        $searchVo = new SearchVo(['commentDiv=?', 'eachSno=?'], [$aParams['commentDiv'], $aParams['eachSno']]);
        $searchVo->setOrder('regDt desc');

        $list = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_COMMENT, $searchVo);
        //DB_MANAGER 가져오기
        $aCustSnos = [];
        foreach ($list as $key => $each) {
            $aCustSnos[] = $each['regManagerSno'];
        }
        $searchVo = new SearchVo();
        $searchVo->setWhere("sno in (" . implode(',', $aCustSnos) . ")");
        $aCustList = DBUtil2::getListBySearchVo(DB_MANAGER, $searchVo);
        $aCustListBySno = [];
        foreach ($aCustList as $val) $aCustListBySno[$val['sno']] = $val;
        //response
        foreach ($list as $key => $each) {
            $each['isModify'] = 'n';
            $each['commentBr'] = nl2br($each['comment']);
            $each['regManagerName'] = isset($aCustListBySno[$each['regManagerSno']]) ? $aCustListBySno[$each['regManagerSno']]['managerNm'] : '';
            $each['regManagerId'] = isset($aCustListBySno[$each['regManagerSno']]) ? $aCustListBySno[$each['regManagerSno']]['managerId'] : '';
            $list[$key] = $each;
        }

        return $list;
    }

    //자재 리스트 가져오기
    public function getListMaterial($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsMaterialService');
        return $imsMaterialService->getListMaterial($params);
    }
    //자재분류 리스트 가져오기
    public function getListMaterialTypeDetail($params=[])
    {
        $aList = DBUtil2::getListBySearchVo(ImsDBName::MATERIAL_TYPE_DETAIL, new SearchVo());

        if (count($aList) > 0) {
            foreach ($aList as $key => $val) $aList[$key]['materialTypeHan'] = NkCodeMap::MATERIAL_TYPE[$val['materialTypeByDetail']];
        }

        return [
            'pageEx' => [],
            'page' => [],
            'list' => $aList,
            'fieldData' => []
        ];
    }

    //수정이력 가져오기
    public function getListMaterialUpdateLog($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsMaterialService');
        return $imsMaterialService->getListMaterialUpdateLog($params);
    }
    //유사퀄리티(그룹) 리스트 가져오기
    public function getListMaterialGrp($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsMaterialService');
        return $imsMaterialService->getListMaterialGrp($params);
    }

    //프로젝트별 부가판매/구매 리스트 가져오기
    public function getListAddedBS($param) {
        $iPjtSno = (int)$param['project_sno'];
        if ($iPjtSno === 0) return [];

        $searchVo = new SearchVo('a.projectSno=?', $iPjtSno);
        $searchVo->setOrder('a.regDt asc');

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::ADDED_B_S ], 'field' => ["a.*"]],
        ];
        $settingTableInfo = DBUtil2::setTableInfo($tableInfo,false);

        //리스트 가져오기
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging($settingTableInfo, $searchVo, $searchData, false, true);
        $aReturn = [];
        foreach ($allData['listData'] as $val) {
            unset($val['regDt'], $val['modDt']);
            $aReturn[] = $val;
        }
        return $aReturn;
    }

    //정산리스트 가져오기(프로젝트별)
    public function getListAccount($params) {
        //where 설정
        $searchData['condition'] = $params;
        $searchVo = new SearchVo();
        $this->refineCommonCondition($searchData['condition'], $searchVo);
        $searchVo->setWhere("(b.sno is not null or c.sno is null)");
        $searchVo->setGroup('a.sno');

        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);

        //select 할 테이블정보(join 포함, 가져올 컬럼들 포함)
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT ], 'field' => ["a.sno, a.isBookRegistered, a.isBookRegisteredDt, a.prdPriceApproval, a.projectType"]],
            'cust' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ], 'field' => ["if(cust.sno is null, '미선택', cust.customerName) as customerName"]],
            'b' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.sno = b.projectSno and b.delFl = "n"' ], 'field' => ["b.projectSno"]],
            'c' => ['data' => [ ImsDBName::ADDED_B_S, 'LEFT OUTER JOIN', 'a.sno = c.projectSno' ], 'field' => ["c.addedType"]],
            'd' => ['data' => [ ImsDBName::PROJECT_EXT, 'LEFT OUTER JOIN', 'a.sno = d.projectSno' ], 'field' => ["d.accountingMessage"]],
        ];

        //프로젝트 리스트 가져오기 - 스타일, 부가구매/판매 정보가 있는 프로젝트들만 가져왔음
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);

        $aPjtSnos = [];
        foreach ($allData['listData'] as $key => $val) $aPjtSnos[] = (int)$val['sno'];
        //프로젝트에 속한 스타일,부가구매/판매 리스트 가져오기
        $searchVo = new SearchVo('delFl=?','n'); //스타일 가져올때 쓰는 검색
        $searchVo2 = new SearchVo(); //부가판매/구매 가져올때 쓰는 검색
        $searchVo->setWhere("projectSno in (" . implode(',', $aPjtSnos) . ")");
        $searchVo2->setWhere("projectSno in (" . implode(',', $aPjtSnos) . ")");
        //product table의 컬럼으로 검색했을때 start
        $aSchFldNmProject = ['cust.customerName', 'a.sno'];
        $aSchNotProjectFld = [];
        foreach ($searchData['condition']['multiKey'] as $val) {
            if (!in_array($val['key'], $aSchFldNmProject) && $val['keyword'] != '') {
                $aSchNotProjectFld[] = $val;
            }
        }
        if (count($aSchNotProjectFld) > 0) {
            $aSqlSchNotProjectFld = [];
            foreach ($aSchNotProjectFld as $val) {
                $aSqlSchNotProjectFld[] = explode('.', $val['key'])[1].' like "%'.$val['keyword'].'%"';
            }
            $searchVo->setWhere('('.implode(' '.$searchData['condition']['multiCondition'].' ', $aSqlSchNotProjectFld).')');
        }
        //product table의 컬럼으로 검색했을때 end
        $searchVo->setOrder('sno asc');
        $aTmpPrdList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT, $searchVo);
        $aPrdList = [];
        foreach ($aTmpPrdList as $val) {
            if (!isset($aPrdList[$val['projectSno']])) $aPrdList[$val['projectSno']] = [];
            $aPrdList[$val['projectSno']][] = $val;
        }
        $searchVo2->setOrder('addedType asc, sno asc');
        $aTmpAddedList = DBUtil2::getListBySearchVo(ImsDBName::ADDED_B_S, $searchVo2);
        $aAddedList = [];
        foreach ($aTmpAddedList as $val) {
            if (!isset($aAddedList[$val['projectSno']])) $aAddedList[$val['projectSno']] = [];
            $aAddedList[$val['projectSno']][] = $val;
        }
        //스타일,부가구매/판매 리스트 프로젝트에 넣기
        foreach ($allData['listData'] as $key => $val) {
            unset($allData['listData'][$key]['projectSno'], $allData['listData'][$key]['addedType']);
            $allData['listData'][$key]['styleSno'] = $allData['listData'][$key]['prdName'] = $allData['listData'][$key]['prdQty'] = $allData['listData'][$key]['prdMsQty'] =
            $allData['listData'][$key]['prdOriginAmt'] = $allData['listData'][$key]['prdAmt'] = $allData['listData'][$key]['prdMargin'] = [];
            $iTotalAmt = $iTotalOriginAmt = 0;

            if (isset($aPrdList[$val['sno']]) && count($aPrdList[$val['sno']]) > 0) {
                foreach ($aPrdList[$val['sno']] as $val2) {
                    $allData['listData'][$key]['styleSno'][] = (int)$val2['sno']; //스타일(product) 일련번호
                    $allData['listData'][$key]['prdName'][] = substr($val2['prdYear'],2,2).' '.$val2['prdSeason'].' '.$val2['productName'];
                    $allData['listData'][$key]['prdQty'][] = (int)$val2['prdExQty']; //수량
                    $allData['listData'][$key]['prdMsQty'][] = (int)$val2['msQty']; //미청구수량
                    if ($val['projectType'] == 4 || (int)$val2['prdCostConfirmSno'] > 0) $iOriginAmt = (int)$val2['prdCost'];
                    else if ((int)$val2['estimateConfirmSno'] > 0) $iOriginAmt = (int)$val2['estimateCost'];
                    else $iOriginAmt = (int)$val2['targetPrdCost'];
                    $allData['listData'][$key]['prdOriginAmt'][] = $iOriginAmt; //생산(매입)가(개당가격)
                    if ($val['prdPriceApproval'] == 'p' || (int)$val2['salePrice'] > 0) $iSaleAmt = (int)$val2['salePrice'];
                    else $iSaleAmt = (int)$val2['targetPrice'];
                    $allData['listData'][$key]['prdAmt'][] = $iSaleAmt; //판매가(개당가격)
                    $allData['listData'][$key]['prdOriginAmtMultiplyQty'][] = $iOriginAmt * (int)$val2['prdExQty']; //원가
                    $allData['listData'][$key]['prdAmtMultiplyQty'][] = $iSaleAmt * ((int)$val2['prdExQty'] - (int)$val2['msQty']); //판매가

//                    $allData['listData'][$key]['prdMargin'][] = (int)(($iSaleAmt-$iOriginAmt) / $iSaleAmt * 100).'%'; //마진 -> 제외

                    $iTotalOriginAmt += $iOriginAmt * (int)$val2['prdExQty']; //총생산(매입)가 합산하기
                    $iTotalAmt += $iSaleAmt * ((int)$val2['prdExQty'] - (int)$val2['msQty']); //총판매가 합산하기
                }
            }
            if (isset($aAddedList[$val['sno']]) && count($aAddedList[$val['sno']]) > 0) {
                foreach ($aAddedList[$val['sno']] as $val2) {
                    $allData['listData'][$key]['styleSno'][] = 0; //부가판매/구매는 스타일상세팝업이 아니라 프로젝트상세팝업
                    $sCssNm = $val2['addedType'] == 1 ? 'text-danger' : 'sl-blue';
                    $allData['listData'][$key]['prdName'][] = '<span class="'.$sCssNm.'">('.NkCodeMap::ADDED_BS_TYPE[$val2['addedType']].')</span> '.$val2['addedName'];
                    $allData['listData'][$key]['prdQty'][] = (int)$val2['addedQty']; //수량
                    $allData['listData'][$key]['prdMsQty'][] = 0; //미청구수량
                    $allData['listData'][$key]['prdOriginAmt'][] = (int)$val2['addedBuyAmount']; //매입단가 == 원가?
                    $allData['listData'][$key]['prdAmt'][] = (int)$val2['addedSaleAmount']; //판매단가
                    $allData['listData'][$key]['prdOriginAmtMultiplyQty'][] = (int)$val2['addedBuyAmount'] * (int)$val2['addedQty']; //판매금액
                    $allData['listData'][$key]['prdAmtMultiplyQty'][] = (int)$val2['addedSaleAmount'] * (int)$val2['addedQty']; //매입금액

//                    $allData['listData'][$key]['prdMargin'][] = (int)(((int)$val2['addedBuyAmount']-(int)$val2['addedSaleAmount']) / (int)$val2['addedAmount'] * 100).'%'; //마진 -> 제외

                    $iTotalOriginAmt += (int)$val2['addedBuyAmount'] * (int)$val2['addedQty']; //총생산(매입)가 합산하기
                    $iTotalAmt += (int)$val2['addedSaleAmount'] * (int)$val2['addedQty']; //총판매가 합산하기
                }
            }
            $allData['listData'][$key]['totalOriginAmt'] = $iTotalOriginAmt;
            $allData['listData'][$key]['totalAmt'] = $iTotalAmt;
            $allData['listData'][$key]['totalMargin'] = $iTotalAmt==0 ? '판매가없음' : round(($iTotalAmt-$iTotalOriginAmt)/$iTotalAmt*100, 2) .'%';
            $allData['listData'][$key]['accountingMessage'] = nl2br($val['accountingMessage']);
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => []
        ];
    }

    //고객 리스트 (레퍼런스-리스트검색모듈에 쓰임)
    public function getListCustomerNk($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->getListCustomerNk($params);
    }
    
    //고객견적 리스트
    public function getListCustomerEstimateNk($params) {
        //where 설정
        $searchData['condition'] = $params;
        $searchVo = new SearchVo();
        if (isset($params['customerSno']) && (int)$params['customerSno'] > 0) $searchVo->setWhere('a.customerSno = '.(int)$params['customerSno']);
        $this->refineCommonCondition($searchData['condition'], $searchVo);
        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);

        //select 할 테이블정보(join 포함, 가져올 컬럼들 포함)
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::CUSTOMER_ESTIMATE ], 'field' => ["a.*"]],
            'cust' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ], 'field' => ["if(cust.sno is null, '미선택', cust.customerName) as customerName"]],
        ];

        //고객견적 리스트 가져오기
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        foreach ($allData['listData'] as $key => $val) {
            $allData['listData'][$key]['estimateTypeHan'] = ImsCodeMap::CUST_ESTIMATE_TYPE[$val['estimateType']];
            $allData['listData'][$key]['sum_amount'] = (int)$val['supply'] + (int)$val['tax'];
            $allData['listData'][$key]['estimateMemo'] = nl2br($val['estimateMemo']);
            $allData['listData'][$key]['innoverMemo'] = nl2br($val['innoverMemo']);
            $allData['listData'][$key]['key'] = SlCommonUtil::aesEncrypt($val['sno']);
        }
        //필드정보 가져오기
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerEstimateNkService');
        $aFldList = $imsService->getDisplay();

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => $aFldList
        ];
    }

    //샘플 리스트(스타일(product) 1 : N 샘플)
    public function getListProductSample($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProductSampleService');
        return $imsService->getListProductSample($params);
    }
    //샘플확정서 - 안내사항 리스트, 안내사항구분 리스트 return
    public function getListProductSampleGuide($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProductSampleService');
        return $imsService->getListProductSampleGuide($params);
    }


    //프로젝트 -> 스타일(기성복) -> 생산가관리(근거) 리스트 가져오기
    public function getListProductPrdCost($params) {
        $iStyleSno = (int)$params['styleSno'];
        $aReturn = ['pageEx' => [], 'page' => [], 'list' => [], 'fieldData' => []];

        $SearchVo = new SearchVo('styleSno=?', $iStyleSno);
        $SearchVo->setOrder('sortNum asc');
        $aList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT_PRD_COST, $SearchVo);

        $aTmpFldList = DBTableField::callTableFunction(ImsDBName::PRODUCT_PRD_COST);
        $aFldList = [];
        $aSkipUpsertFlds = ['regDt'];
        foreach ($aTmpFldList as $val) {
            if (!in_array($val['val'], $aSkipUpsertFlds)) {
                $aTmp = ['type' => 'c', 'col' => 4, 'class' => 'ta-l pdl5', 'name' => '', 'title' => '' ];
                if ($val['typ'] == 'i') {
                    $aTmp['type'] = 'i';
                    $aTmp['class'] = 'ta-r';
                }
                if (in_array($val['val'], ['sno', 'styleSno'])) $aTmp['skip'] = true;
                $aTmp['name'] = $val['val'];
                $aTmp['title'] = $val['name'];
                $aFldList[] = $aTmp;
            }
        }

        $aReturn['list'] = $aList;
        $aReturn['fieldData'] = $aFldList;

        return $aReturn;
    }
    //최초기획일정 가져오기
    public function getListProjectPlanSche($param) {
        $iPjtSno = (int)$param['project_sno'];
        if ($iPjtSno === 0) return [];
        //리스트 가져오기
        $searchVo = new SearchVo('a.projectSno=?', $iPjtSno);
        $searchVo->setOrder('a.regDt asc');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT_PLAN_SCHE ], 'field' => ["a.*"]],
        ];
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        //리스트 정제
        $aReturn = [];
        foreach (NkCodeMap::PROJECT_PLAN_SCHE_TYPE as $key => $val) {
            foreach (NkCodeMap::PROJECT_PLAN_SCHE_STEP as $key2 => $val2) {
                $aReturn[$key][$key2] = '';
            }
        }
        foreach ($allData['listData'] as $val) {
            $aReturn[$val['scheType']][$val['scheStep']] = $val['scheDt'] == '0000-00-00' ? '' : $val['scheDt'];
        }
        return $aReturn;
    }
    //스타일기획리스트
    public function getListStylePlan($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProductPlanService');
        return $imsService->getListStylePlan($params);
    }
    //고객제공사이즈 리스트 가져오기
    public function getListStylePlanCustomerFit($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProductPlanService');
        return $imsService->getListStylePlanCustomerFit($params);
    }
    //고객사 담당자 리스트 가져오기 + 테이블스키마 기반으로 필드목록도 가져오기
    public function getListCustomerContact($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->getListCustomerContact($params);
    }

    //메일발송 리스트 가져오기
    public function getListSendHistory($params) {
        //이 함수에서 code,msg,message return해봤자 소용없음
        $aReturn = ['data' => [], 'code'=>500, 'msg' => ''];
        if ((int)$params['sno'] === 0 || $params['historyDiv'] == '') {
            $aReturn['msg'] = '접근오류';
            return $aReturn;
        }

        $searchVo = new SearchVo(['a.projectSno=?','a.sendType=?'], [(int)$params['sno'],$params['historyDiv']]);
        $searchVo->setOrder('a.regDt desc');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SEND_HISTORY ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.sendManagerSno = reg.sno' ], 'field' => ["reg.managerNm"]],
        ];
        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);

        $aReturn['data'] = $aList;
        $aReturn['code'] = 200;
        return $aReturn;
    }

    //프로젝트/스타일 이슈리스트 가져오기 + 등록/수정시 정보 가져오기
    public function getListProjectIssue($params) {
        $imsIssueService = SlLoader::cLoad('ims', 'ImsProjectIssueService');
        return $imsIssueService->getListProjectIssue($params);
    }
    //프로젝트/스타일 이슈 조치사항리스트 가져오기 + 등록/수정시 정보 가져오기
    public function getListProjectIssueAction($params) {
        $imsIssueService = SlLoader::cLoad('ims', 'ImsProjectIssueService');
        return $imsIssueService->getListProjectIssueAction($params);
    }

    //기초정보-사이즈스펙 양식리스트
    public function getListFitSpec($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsFitSpecService');
        return $imsService->getListFitSpec($params);
    }

    //기초정보-공임비용/기타비용(스타일기획, 샘플 등록/수정시 사용) 리스트/등록수정폼 가져오기
    public function getListSampleEtcCost($params) {
        $sTableNm = ImsDBName::BASIC_ETC_COST;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트에서 뿌려줄 필드리스트 정리
            $imsService = SlLoader::cLoad('ims', 'ImsFitSpecService');
            $aFldList = $imsService->getDisplayEtcCost();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            //리스트 정제
            if (count($aReturn['list'] > 0)) {
                foreach ($aReturn['list'] as $key => $val) {
                    $aReturn['list'][$key]['costTypeHan'] = NkCodeMap::SAMPLE_ETC_COST_TYPE[$val['costType']];
                }
            }
        } else if ($params['upsertSnoGet'] !== 0) { //수정폼을 return받으려는 경우
            //데이터 정제
            $aReturn['info']['costTypeHan'] = NkCodeMap::SAMPLE_ETC_COST_TYPE[$aReturn['info']['costType']];
        }

        return $aReturn;
    }

    //기초정보-샘플실/패턴실(샘플 등록/수정시 사용) 리스트/등록수정폼 가져오기
    public function getListSampleRoom($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsFitSpecService');
        return $imsService->getListSampleRoom($params);
    }

    //기초정보-피팅체크 양식리스트
    public function getListFittingCheck($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsFitSpecService');
        return $imsService->getListFittingCheck($params);
    }

    //차량정보 가져오기. 수정(==상세)일때 주행거리, 정비건수 등 자세한 정보 가져옴
    public function getListEtcCar($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsEtcCarService');
        return $imsService->getListEtcCar($params);
    }
    //정비일지 CRU. 등록일때 현재km 가져옴
    public function getListEtcCarMaintain($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsEtcCarService');
        return $imsService->getListEtcCarMaintain($params);
    }
    //주소지 CRU. 현재는 리스트일때만 호출
    public function getListEtcCarAddr($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsEtcCarService');
        return $imsService->getListEtcCarAddr($params);
    }
    //운행일지 CRU. 등록일때 현재km 가져옴
    public function getListEtcCarDrive($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsEtcCarService');
        return $imsService->getListEtcCarDrive($params);
    }

    //발굴고객리스트 가져오기
    public function getListFindCustomer($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->getListFindCustomer($params);
    }



    //프로젝트의 세부스케쥴 가져오기
    public function getListProjectScheDetail($params) {
        $iProjectSno = (int)$params['sno'];
        $aTableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT_SCHE_DETAIL ], 'field' => ["a.*"]],
            'owner' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.ownerManagerSno = owner.sno' ], 'field' => ["if(owner.sno is null, '미선택', owner.managerNm) as ownerManagerName"]],
        ];
        $oSearchVo = new SearchVo('projectSno=?', $iProjectSno);
        $oSearchVo->setOrder('a.sortSche asc');
        $aProjectScheDetail = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTableInfo,false), $oSearchVo, false, false, true);
        $aScheDetail = $this->getListScheDetail();

        if (count($aProjectScheDetail) > 0) {
            foreach ($aProjectScheDetail as $key => $val) {
                if (isset($aScheDetail[$val['scheDetailSno']])) {
                    $aProjectScheDetail[$key] = array_merge($aProjectScheDetail[$key], $aScheDetail[$val['scheDetailSno']]);
                }
            }
        }

        return $aProjectScheDetail;
    }

    //업종리스트 가져오기
    public function getListBusiCate($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsBusiCateService');
        return $imsService->getListBusiCate($params);
    }
    //발굴고객별 영업활동이력 + 발굴고객정보 가져오기
    public function getListSalesCustomerContents($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->getListSalesCustomerContents($params);
    }
    //통계 가져오기
    public function getListSalesCustomerStats($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->getListSalesCustomerStats($params);
    }

    //영업기획서 작성페이지 진입시
    public function getListBasicFormToSalesPlanPage($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProjectNkService');
        return $imsService->getListBasicFormToSalesPlanPage($params);
    }

    //기초정보관리-제안서가이드 양식관리
    public function getListBasicFormProposalGuide($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProjectNkService');
        return $imsService->getListBasicFormProposalGuide($params);
    }

    //자재상세 - 시험성적서 리스트 가져오기
    public function getListTestReport($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsMaterialService');
        return $imsMaterialService->getListTestReport($params);
    }
    //자재상세 - 시험성적서 등록시 작성폼 가져오기
    public function getListTestReportForm($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsMaterialService');
        return $imsMaterialService->getListTestReportForm($params);
    }
    
    //파우치-수령담당자 리스트 가져오기
    public function getListCustomerReceiver($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->getListCustomerReceiver($params);
    }
    //파우치-발주건정보(사이즈별 제작수량) 가져오기
    public function getListPackingInfo($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->getListPackingInfo($params);
    }
    //파우치-설정된 분류패킹 리스트 가져오기
    public function getListCustomerReceiverDelivery($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->getListCustomerReceiverDelivery($params);
    }
    //납품===발주===분류패킹 리스트 가져오기
    public function getListCustomerPacking($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->getListCustomerPacking($params);
    }

    //납품검수(납품보고서) 가져오기
    public function getListStyleInspectDelivery($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProjectNkService');
        return $imsService->getListStyleInspectDelivery($params);
    }

    //스타일기획 레퍼런스 - 부가정보리스트 가져오기
    public function getListRefStylePlanAppendInfo($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->getListRefStylePlanAppendInfo($params);
    }
    public function getListRefStylePlanAppendInfoSimple($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->getListRefStylePlanAppendInfoSimple($params);
    }

    //스타일기획 레퍼런스 - 레퍼런스 리스트/정보 가져오기
    public function getListStylePlanRef($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->getListStylePlanRef($params);
    }


}
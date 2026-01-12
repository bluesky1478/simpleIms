<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

/**
 * IMS 자재 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsMaterialService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpData;
    private $dpDataGrp;

    public function __construct(){
        $this->dpData = [
            ['title' => '일련번호', 'type' => 'c', 'name' => 'sno', 'col' => 4, 'class' => '', 'skip'=>true],
            ['title' => '유사퀄리티', 'type' => 'group', 'name' => 'grpName', 'col' => 4, 'class' => ''],
            ['title' => '품목코드', 'type' => 'c', 'name' => 'code', 'col' => 4, 'class' => ''],
            ['title' => '매입처', 'type' => 'c', 'name' => 'customerName', 'col' => 4, 'class' => ''],
            ['title' => '타입', 'type' => 'c', 'name' => 'materialTypeHan', 'col' => 3, 'class' => ''],
            ['title' => '품목구분', 'type' => 'c', 'name' => 'materialTypeText', 'col' => 3, 'class' => ''],
            ['title' => '자재명', 'type' => 'pop_modify', 'name' => 'name', 'col' => 14, 'class' => '','titleClass' => 'ta-l pdl5' ],
            ['title' => '혼용률', 'type' => 'c', 'name' => 'mixRatio', 'col' => 6, 'class' => '','titleClass' => 'ta-l pdl5'],
            ['title' => '컬러', 'type' => 's', 'name' => 'materialColor', 'col' => 4, 'class' => '','titleClass' => 'ta-l pdl5'],
            ['title' => '폭/규격', 'type' => 'c', 'name' => 'spec', 'col' => 5, 'class' => ''],
            ['title' => '매입단가', 'type' => 'c', 'name' => 'unitPrice', 'col' => 5, 'class' => ''],
            ['title' => '생산국', 'type' => 'c', 'name' => 'makeNational', 'col' => 4, 'class' => ''],
            ['title' => 'MOQ', 'type' => 'c', 'name' => 'moq', 'col' => 4, 'class' => ''], //moq == 미니멈
            ['title' => 'BT준비', 'type' => 'c', 'name' => 'btYn', 'col' => 4, 'class' => ''], //기간 -> 유무
            ['title' => '생지보유', 'type' => 'c', 'name' => 'onHandYn', 'col' => 4, 'class' => ''],
            ['title' => 'BT기간', 'type' => 's', 'name' => 'btPeriod', 'col' => 4, 'class' => ''],
            ['title' => '생산기간', 'type' => 'c', 'name' => 'makePeriod', 'col' => 4, 'class' => ''], //기간 -> 유무
            ['title' => '상태', 'type' => 'c', 'name' => 'materialStHan', 'col' => 3, 'class' => ''],
            ['title' => '시험성적서', 'type' => 'c', 'name' => 'cntTestReport', 'col' => 3, 'class' => ''],
            ['title' => '자체테스트', 'type' => 'c', 'name' => 'cntTestSelf', 'col' => 3, 'class' => ''],
            ['title' => '등록일', 'type' => 'c', 'name' => 'regDt', 'col' => 4, 'class' => ''],
            ['title' => '수정일', 'type' => 'c', 'name' => 'modDt', 'col' => 4, 'class' => ''],
            ['title' => '수정/이력', 'type' => 'pop_modify_log', 'name' => 'modDt', 'col' => 5, 'class' => ''],
            //['title' => '후가공', 'type' => 'c', 'name' => 'afterMake', 'col' => 6, 'class' => ''],
        ];

        $this->dpDataGrp = [
            ['type' => 'c', 'col' => 0, 'class' => 'ta-l', 'name' => 'grpName', 'title' => '그룹명', ],
            ['type' => 'cnt_material', 'col' => 14, 'class' => 'ta-l', 'name' => 'cntMaterial', 'title' => '소속된 자재갯수', ],
            ['type' => 'c', 'col' => 18, 'class' => '', 'name' => 'regDt', 'title' => '등록일', ],
            ['type' => 'c', 'col' => 18, 'class' => '', 'name' => 'modDt', 'title' => '수정일', ],
        ];
    }

    public function getDisplay() { return $this->dpData; }
    public function getDisplayGrp() { return $this->dpDataGrp; }

    public function getListMaterial($params) {
        //where 설정
        $searchData['condition'] = $params;
        $searchVo = new SearchVo();
        $this->refineCommonCondition($searchData['condition'], $searchVo);

        //where 컬럼명 in (~~~) 반영
        if (isset($params['sWhereSnos']) && $params['sWhereSnos'] != '') {
            $searchVo->setWhere("a.sno in (".$params['sWhereSnos'].")");
        }

        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);
        $searchVo->setGroup('a.sno');
        //select 할 테이블정보(join 포함, 가져올 컬럼들 포함)
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::MATERIAL ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::MATERIAL_TYPE_DETAIL, 'LEFT OUTER JOIN', 'a.typeDetailSno = b.sno' ], 'field' => ["if(b.sno is null, '미선택', b.materialTypeText) as materialTypeText"]],
            //cust === 매입처
            'cust' => ['data' => [ ImsDBName::SAMPLE_FACTORY, 'LEFT OUTER JOIN', 'a.buyerSno = cust.sno' ], 'field' => ["if(cust.sno is null, '없음', factoryName) as customerName, if(cust.sno is null, '없음', factoryPhone) as customerPhone, if(cust.sno is null, '없음', factoryAddress) as customerAddr"]],
            'grp' => ['data' => [ ImsDBName::MATERIAL_GROUP, 'LEFT OUTER JOIN', 'a.groupSno = grp.sno' ], 'field' => ["if(grp.sno is null, '없음', grpName) as grpName"]],
            'test' => ['data' => [ ImsDBName::TEST_REPORT_FILL, 'LEFT OUTER JOIN', 'a.sno = test.materialSno and test.testType = 1' ], 'field' => ["count(test.sno) as cntTestReport"]], //갯수 제대로 못가져옴. 등록여부 검색용으로 join
            'test2' => ['data' => [ ImsDBName::TEST_REPORT_FILL, 'LEFT OUTER JOIN', 'a.sno = test2.materialSno and test2.testType = 2' ], 'field' => ["count(test2.sno) as cntTestSelf"]], //갯수 제대로 못가져옴. 등록여부 검색용으로 join
        ];
        $settingTableInfo = DBUtil2::setTableInfo($tableInfo,false);

        //리스트 가져오기
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging($settingTableInfo, $searchVo, $searchData, false, true);

        //시험성적서 갯수와 자체테스트 갯수는 서로 join이 중복되어 정확한 갯수를 가져오지 못하므로 따로 select 때려야함(시험성적서갯수, 자체테스트갯수, 고객사별 시험성적서 갯수 구하기)
        if (count($allData['listData']) > 0) {
            $aMeterialSnos = [];
            foreach ($allData['listData'] as $key => $val) {
                $aMeterialSnos[] = (int)$val['sno'];
                $allData['listData'][$key]['cntTestReportByCustomerSno'] = [];
            }
            $oSVTest = new SearchVo();
            $oSVTest->setWhere("materialSno in (".implode(",",$aMeterialSnos).")");
            $oSVTest->setGroup('materialSno, testType, customerSno');
            $aTestTableInfo=[
                'a' => ['data' => [ ImsDBName::TEST_REPORT_FILL ], 'field' => ["a.materialSno, a.testType, a.customerSno, count(a.sno) as cntTest"]],
            ];

            //원부자재리스트에서 당시 시험성적서,자체테스트 작성갯수 가져오기 and 원부자재리스트 모듈에서 원단선택시 당시 시험성적서 작성갯수(고객사별) 가져오기
            $aTestList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aTestTableInfo,false), $oSVTest);
            if (count($aTestList) > 0) {
                $aTestCntList = $aTestCntListByCustomerSno = [];
                foreach ($aTestList as $val) {
                    if (!isset($aTestCntList[$val['materialSno']][$val['testType']])) $aTestCntList[$val['materialSno']][$val['testType']] = 0;
                    $aTestCntList[$val['materialSno']][$val['testType']] += (int)$val['cntTest'];

                    if ($val['testType'] == 1) $aTestCntListByCustomerSno[$val['materialSno']][1][$val['customerSno']] = (int)$val['cntTest'];
                }
                foreach ($allData['listData'] as $key => $val) {
                    //원부자재리스트에서 당시 시험성적서,자체테스트 작성갯수 가져오기
                    if (isset($aTestCntList[$val['sno']][1])) $allData['listData'][$key]['cntTestReport'] = $aTestCntList[$val['sno']][1];
                    if (isset($aTestCntList[$val['sno']][2])) $allData['listData'][$key]['cntTestSelf'] = $aTestCntList[$val['sno']][2];

                    //원부자재리스트 모듈에서 원단선택시 당시 시험성적서 작성갯수(고객사별) 가져오기
                    if (isset($aTestCntListByCustomerSno[$val['sno']][1])) $allData['listData'][$key]['cntTestReportByCustomerSno'] = $aTestCntListByCustomerSno[$val['sno']][1];
                }
            }
        }

        $aGroupSnos = [];
        foreach ($allData['listData'] as $key => $val) {
            if ($val['currencyUnit'] == 1) $allData['listData'][$key]['unitPrice'] = number_format($val['unitPrice'])."\\";
            else $allData['listData'][$key]['unitPrice'] = number_format($val['unitPrice'], 2, '.', ',')."$";
            $allData['listData'][$key]['moq'] = number_format($val['moq']);
            if ($val['makePeriodNoOnHand'] != 0) $allData['listData'][$key]['makePeriod'] = $val['makePeriod'].' / '.$val['makePeriodNoOnHand'];
            $allData['listData'][$key]['materialTypeHan'] = NkCodeMap::MATERIAL_TYPE[$val['materialType']];
            $allData['listData'][$key]['makeNationalCode'] = $val['makeNational'];
            $allData['listData'][$key]['makeNational'] = ImsCodeMap::PRD_NATIONAL_CODE[$val['makeNational']];
            $allData['listData'][$key]['btYn'] = NkCodeMap::MATERIAL_BT_YN[$val['btYn']];
            $allData['listData'][$key]['onHandYn'] = NkCodeMap::MATERIAL_ON_HAND[$val['onHandYn']];
            $allData['listData'][$key]['regDt'] = substr($val['regDt'],0, 10);
            $allData['listData'][$key]['modDt'] = substr($val['modDt'],0, 10);
            $allData['listData'][$key]['materialStHan'] = NkCodeMap::MATERIAL_ST[$val['materialSt']];
            //유사퀄리티(그룹)에 소속된 자재를 가져오기 위한 준비
            $allData['listData'][$key]['grpMaterialNames'] = '유사퀄리티 없음';
            if ($val['groupSno'] != 0 && !in_array($val['groupSno'], $aGroupSnos)) array_push($aGroupSnos, $val['groupSno']);
        }
        if (count($aGroupSnos) > 0) {
            $aCurrGrpList = $this->getListMaterialGrpAllpage(['aChkboxScha.sno'=>$aGroupSnos]);
            if (count($aCurrGrpList) > 0) {
                $aGrpMatesByGrpSno = [];
                foreach ($aCurrGrpList as $val) {
                    $aGrpMatesByGrpSno[$val['sno']] = str_replace("||","\r\n",$val['materialNames']);
                }
                foreach ($allData['listData'] as $key => $val) {
                    if ($val['groupSno'] != 0) {
                        $allData['listData'][$key]['grpMaterialNames'] = $aGrpMatesByGrpSno[$val['groupSno']];
                    }
                }
            }
        }

        //필드정보 가져오기
        $aFldList = $this->getDisplay();

        //frontend로부터 전달받은 컬럼명arr이 있다면 이것들만 fieldData에다가 구성시킴. 현재는 library_nk_sch_modal.php 에서만 쓰임
        if (isset($params['require_fld_list']) && is_array($params['require_fld_list']) && count($params['require_fld_list']) > 0) {
            $aTmpFldList = $aFldList;
            $aFldList = [];
            foreach ($aTmpFldList as $val) {
                if (in_array($val['name'], $params['require_fld_list'])) $aFldList[] = $val;
            }

            //스타일상세 -> 원단or부자재 : 환율보여주기 and 환율계산한 값 단가에 뿌려주기 start
            foreach ($aFldList as $key => $val) {
                if ($val['name'] == 'unitPrice') {
                    $aFldList[$key]['name'] = 'unit_price_origin';
                    break;
                }
            }
            $aFldList[] = ['title' => '생산기간(생지없음)', 'type' => 'c', 'name' => 'makePeriodNoOnHand', 'col' => 4, 'class' => ''];
            $aFldList[] = ['title' => '환율', 'type' => 'c', 'name' => 'doller_ratio', 'col' => 5, 'class' => ''];
            $aFldList[] = ['title' => '원화', 'type' => 'c', 'name' => 'unitPrice', 'col' => 5, 'class' => ''];
            $aFldList[] = ['title' => '상태', 'type' => 'c', 'name' => 'materialStHan', 'col' => 3, 'class' => '']; //namku(chk) 이런식으로 필드 추가하면 require_fld_list에 없는 필드이기 때문에 선택시 복사 안된다(리스트에서만 보여주기 용도)
            $fDollerRatio = SlCommonUtil::getCurrentDollar();
            foreach ($allData['listData'] as $key => $val) {
                $allData['listData'][$key]['unit_price_origin'] = $val['unitPrice']; //검색한 리스트에서 보여줌
                $allData['listData'][$key]['makePeriod'] = explode(' / ', $val['makePeriod'])[0];
                $allData['listData'][$key]['btYn'] = $val['btYn'] == '완료' ? 'O' : 'X';
                if ($val['onHandYn'] == '생지있음') $allData['listData'][$key]['onHandYn'] = 'O';
                else if ($val['onHandYn'] == '생지없음') $allData['listData'][$key]['onHandYn'] = 'X';
                else $allData['listData'][$key]['onHandYn'] = '미확인';
                if ($val['currencyUnit'] == 1) { //화폐단위가 원화인 경우
                    $allData['listData'][$key]['doller_ratio'] = $allData['listData'][$key]['unitPriceDoller'] = 0; //textbox에 입력시키는 값
                } else {
                    $allData['listData'][$key]['doller_ratio'] = $fDollerRatio;
                    $allData['listData'][$key]['unitPriceDoller'] = (float)str_replace(['$',','],'',$val['unitPrice']);
                    $allData['listData'][$key]['unitPrice'] = number_format(ceil((float)str_replace(['$',','],'',$val['unitPrice']) * $fDollerRatio)).'\\';
                }
            }
            //스타일상세 -> 원단or부자재 : 환율보여주기 and 환율계산한 값 단가에 뿌려주기 end
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => $aFldList
        ];
    }

    public function getListMaterialUpdateLog($params) {
        //where 설정
        $searchData['condition'] = $params;
        if ((int)$searchData['condition']['materialSno'] === 0) {
            echo "err";
            exit;
        }

        $searchVo = new SearchVo('a.materialSno=?', (int)$searchData['condition']['materialSno']);

        //table order by 설정
        $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
        unset($searchData['condition']['sort']);
        $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);

        //select 할 테이블정보(join 포함, 가져올 컬럼들 포함)
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::MATERIAL_UPDATE_LOG ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["reg.managerNm"]],
        ];
        $settingTableInfo = DBUtil2::setTableInfo($tableInfo,false);

        //리스트 가져오기
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging($settingTableInfo, $searchVo, $searchData, false, true);
        if (count($allData['listData']) > 0) {
            //컬럼명(한글) 가져오기
            $aTmpTableInfoList = DBTableField::callTableFunction(ImsDBName::MATERIAL);
            $aTableInfoList = [];
            foreach ($aTmpTableInfoList as $key => $val) {
                $aTableInfoList[$val['val']] = $val['name'];
            }
            //구분(한글), 매입처(한글) 가져오기
            $aMaterialDetailList = $aMaterialBuyerList = [];
            $aTmpList = DBUtil2::getListBySearchVo(ImsDBName::MATERIAL_TYPE_DETAIL, new SearchVo());
            foreach ($aTmpList as $val) $aMaterialDetailList[$val['sno']] = $val['materialTypeText'];
            $aTmpList = DBUtil2::getListBySearchVo(ImsDBName::SAMPLE_FACTORY, new SearchVo());
            foreach ($aTmpList as $val) $aMaterialBuyerList[$val['sno']] = $val['factoryName'];
            //수정이력리스트 정제
            foreach ($allData['listData'] as $key => $val) {
                $aoTmp = (array)json_decode(str_replace(["\n","\r","\r\n"],'\n',$val['updateDesc']));
                $aTmpLog = [];
                foreach ($aoTmp as $key2 => $val2) {
                    switch ($key2) {
                        case 'buyerSno':
                            $sBefore = isset($aMaterialBuyerList[$val2->before]) ? $aMaterialBuyerList[$val2->before] : '없음';
                            $sAfter = isset($aMaterialBuyerList[$val2->after]) ? $aMaterialBuyerList[$val2->after] : '없음';
                            break;
                        case 'typeDetailSno':
                            $sBefore = isset($aMaterialDetailList[$val2->before]) ? $aMaterialDetailList[$val2->before] : '미선택';
                            $sAfter = isset($aMaterialDetailList[$val2->after]) ? $aMaterialDetailList[$val2->after] : '미선택';
                            break;
                        case 'makeNational':
                            $sBefore = ImsCodeMap::PRD_NATIONAL_CODE[$val2->before];
                            $sAfter = ImsCodeMap::PRD_NATIONAL_CODE[$val2->after];
                            break;
                        case 'btYn':
                            $sBefore = NkCodeMap::MATERIAL_BT_YN[$val2->before];
                            $sAfter = NkCodeMap::MATERIAL_BT_YN[$val2->after];
                            break;
                        case 'onHandYn':
                            $sBefore = NkCodeMap::MATERIAL_ON_HAND[$val2->before];
                            $sAfter = NkCodeMap::MATERIAL_ON_HAND[$val2->after];
                            break;
                        case 'usedStyle':
                            $sBefore = implode(',',$this->convertCheckboxSumToArr(NkCodeMap::MATERIAL_USED_STYLE, (int)$val2->before, 'str'));
                            $sAfter = implode(',',$this->convertCheckboxSumToArr(NkCodeMap::MATERIAL_USED_STYLE, (int)$val2->after, 'str'));
                            break;
                        case 'materialSt':
                            $sBefore = NkCodeMap::MATERIAL_ST[$val2->before];
                            $sAfter = NkCodeMap::MATERIAL_ST[$val2->after];
                            break;
                        case 'currencyUnit':
                            $sBefore = NkCodeMap::CURRENCY_UNIT[$val2->before];
                            $sAfter = NkCodeMap::CURRENCY_UNIT[$val2->after];
                            break;
                        default:
                            $sBefore = $val2->before;
                            $sAfter = $val2->after;
                            break;
                    }
                    $sFldNmHan = isset($aTableInfoList[$key2]) ? $aTableInfoList[$key2] : '';
                    $aTmpLog[] = ['fld_name'=>$sFldNmHan, 'before'=>$sBefore, 'after'=>$sAfter];
                }
                $allData['listData'][$key]['updateDesc'] = $aTmpLog;
            }
        }

        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => $allData['listData'],
            'fieldData' => []
        ];
    }

    public function getListMaterialGrp($params) {
        $sTableNm = ImsDBName::MATERIAL_GROUP;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::MATERIAL, 'LEFT OUTER JOIN', 'a.sno = b.groupSno' ], 'field' => ["count(b.sno) as cntMaterial, GROUP_CONCAT(b.sno order by b.name) AS materialSnos, GROUP_CONCAT(b.name order by b.name SEPARATOR '||') AS materialNames"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            if (!isset($params['sort'])) $params['sort'] = 'sno,asc';
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayGrp();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList, ['a.sno']);

        if (!isset($params['upsertSnoGet'])) {
            if (count($aReturn['list']) > 0) {
                foreach ($aReturn['list'] as $key => $val) {
                    $aReturn['list'][$key]['materialNames'] = str_replace('||', "\r\n", $val['materialNames']);
                    $aReturn['list'][$key]['regDt'] = explode(' ', $val['regDt'])[0];
                    $aReturn['list'][$key]['modDt'] = explode(' ', $val['modDt'])[0];
                }
            }
        } else {
            $aReturn['list_items'] = [];
            if ($params['upsertSnoGet'] > 0) {
                //수정(==상세)시 그룹에 소속된 자재리스트 가져오기
                $aReturn['list_items'] = $this->getListMaterial(['sWhereSnos'=>$aReturn['info']['materialSnos']])['list'];
            }
        }

        return $aReturn;
    }
    //paging정보(TOTAL count) 바꾸지 않도록 메소드 따로 만듬
    private function getListMaterialGrpAllpage($params) {
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::MATERIAL_GROUP ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::MATERIAL, 'LEFT OUTER JOIN', 'a.sno = b.groupSno' ], 'field' => ["count(b.sno) as cntMaterial, GROUP_CONCAT(b.sno order by b.name) AS materialSnos, GROUP_CONCAT(b.name order by b.name SEPARATOR '||') AS materialNames"]],
        ];
        $searchVo = new SearchVo();
        $searchData['condition'] = $params;
        $this->refineCommonCondition($searchData['condition'], $searchVo);
        $searchVo->setGroup('a.sno');
        $aReturn = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo);
        if (count($aReturn) > 0) {
            foreach ($aReturn as $key => $val) {
                $aReturn[$key]['materialNames'] = str_replace('||', ", ", $val['materialNames']);
            }
        }
        return $aReturn;
    }

    public function setMaterialGrp($params) {
        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        $sCurrDt = date('Y-m-d H:i:s');
        $iRegManagerSno = \Session::get('manager.sno');

        if ($iSno === 0) {
            $params['data']['regManagerSno'] = $iRegManagerSno;
            $params['data']['regDt'] = $sCurrDt;
            $iSno = DBUtil2::insert(ImsDBName::MATERIAL_GROUP, $params['data']);
        } else {
            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::MATERIAL_GROUP, $params['data'], new SearchVo('sno=?', $iSno));

            DBUtil2::update(ImsDBName::MATERIAL, ['groupSno'=>0], new SearchVo('groupSno=?', $iSno));
        }
        if (count($params['itemSnos']) > 0) {
            foreach ($params['itemSnos'] as $val) DBUtil2::update(ImsDBName::MATERIAL, ['groupSno'=>$iSno], new SearchVo('sno=?', $val));
        }

        //소속자재가 없는 그룹은 delete from
        $aCurrGrpList = $this->getListMaterialGrpAllpage([]);
        foreach ($aCurrGrpList as $val) {
            if ($val['cntMaterial'] == 0) {
                DBUtil2::delete(ImsDBName::MATERIAL_GROUP, new SearchVo('sno=?', $val['sno']));
            }
        }
    }

    public function getListTestReport($params) {
        $oSV = new SearchVo();
        $oSV->setOrder('materialSno asc');
        $oSV->setOrder('customerName asc');
        $oSV->setOrder('materialColor asc');
        $iSchMaterialSno = (int)$params['materialSno'];
        if ($iSchMaterialSno > 0) $oSV->setWhere('materialSno = '.$iSchMaterialSno);
        $iSchCustomerSno = (int)$params['customerSno'];
        if ($iSchCustomerSno > 0) $oSV->setWhere('customerSno = '.$iSchCustomerSno);
        $iTestType = (int)$params['testType'];
        if ($iTestType > 0) $oSV->setWhere('a.testType = '.$iTestType);
        $sSchMaterialColor = $params['materialColor'];
        if ($sSchMaterialColor != '') $oSV->setWhere("materialColor = '".$sSchMaterialColor."'");
        $aSchMaterialInfo = $params['aoSchMaterial'];
        if (is_array($aSchMaterialInfo) && count($aSchMaterialInfo) > 0) {
            $aTmpSql = [];
            foreach ($aSchMaterialInfo as $val) {
                $aTmpSql[] = "(materialSno = ".$val['materialSno']." and materialColor = '".$val['materialColor']."')";
            }
            $oSV->setWhere("(".implode(' or ', $aTmpSql).")");
        }

        $oSV->setGroup('a.sno');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::TEST_REPORT_FILL ], 'field' => ["a.*"]],
            'cust' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'a.customerSno = cust.sno' ], 'field' => ["cust.customerName"]],
            'file' => ['data' => [ ImsDBName::PROJECT_FILE, 'LEFT OUTER JOIN', "a.sno = file.eachSno and file.fileDiv = 'materialTestSelf'" ], 'field' => ["if(file.sno is null , '[]', file.fileList) as fileList, file.rev, file.regDt as fileRegDt"]],
            'file_reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'file.regManagerSno = file_reg.sno' ], 'field' => ["if(file_reg.sno is null, '미선택', file_reg.managerNm) as fileRegManagerName"]],
        ];
        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oSV, false, false, true);

        //고객별/색상별 스타일명/부착위치 text 가져오기
        $oSV = new SearchVo();
        if ($iSchMaterialSno > 0) $oSV->setWhere('materialSno = '.$iSchMaterialSno);
        if ($iSchCustomerSno > 0) $oSV->setWhere('customerSno = '.$iSchCustomerSno);
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PRD_MATERIAL ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = b.sno' ], 'field' => ["b.productName, b.customerSno"]],
        ];
        $aPrdMaterialList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oSV, false, false, true);
        $aStyleInfoList = [];
        foreach ($aPrdMaterialList as $val) {
            if (!isset($aStyleInfoList[$val['customerSno'].'___'.$val['color']])) $aStyleInfoList[$val['customerSno'].'___'.$val['color']] = [];
            if (!in_array($val['productName'].'/'.$val['position'], $aStyleInfoList[$val['customerSno'].'___'.$val['color']])) {
                $aStyleInfoList[$val['customerSno'].'___'.$val['color']][] = $val['productName'].'/'.$val['position'];
            }
        }

        foreach ($aList as $key => $val) {
            $aList[$key]['jsonFillContents'] = json_decode($val['jsonFillContents'], true);
            if (isset($aStyleInfoList[$val['customerSno'].'___'.$val['materialColor']])) {
                $aList[$key]['styleInfo'] = implode('<br/>',$aStyleInfoList[$val['customerSno'].'___'.$val['materialColor']]);
            }
            //자체테스트 첨부파일 정리
            $aFileList = json_decode($val['fileList'], true);
            if (count($aFileList) > 0) {
                $aList[$key]['fileList'] = [
                    'title' => 'Rev'.$aList[$key]['rev'].' '.$aList[$key]['fileRegManagerName'].'등록'.'('.gd_date_format('y/m/d', $aList[$key]['fileRegDt']).')',
                    'memo' => str_replace("'",'',$aList[$key]['memo']),
                    'files' => $aFileList,
                    'sno' => $aList[$key]['sno']
                ];
            } else {
                $aList[$key]['fileList'] = ['title' => '첨부한 파일이 없습니다.', 'memo' => '', 'files' => [], 'noRev' => null];
            }
        }

        return $aList;
    }

    public function getListTestReportForm($params) {
        //namkuuu 후순위작업. 시험성적서 작성폼 여러개 필요하다면 양식table 만들고 기초정보관리에서 메뉴 만들기(자재상세팝업의 시험성적서 수정소스 활용)

        if ($params['testType'] == 1) {
            $aFillForm = [
                ['grpName'=>'혼용율', 'sumAvgYn'=>'n', 'childGrp'=>
                    [
                        ['grpName'=>'혼용율', 'standardVal'=>'', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'폴리에스터', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'폴리우레탄', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                    ]
                ],
                ['grpName'=>'염색 견뢰도', 'sumAvgYn'=>'y', 'childGrp'=>
                    [
                        ['grpName'=>'세탁 견뢰도', 'standardVal'=>'4', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'변퇴색', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(폴리에스터)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(면)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'손세탁 견뢰도', 'standardVal'=>'4', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'변퇴색', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(폴리에스터)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(면)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(아세테이트)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(폴리아마이드)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(아크릴)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(모)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'일광 및 땀 견뢰도', 'standardVal'=>'4', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'산성', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'알칼리성', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'물 견뢰도', 'standardVal'=>'4', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'변퇴색', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(폴리에스터)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'오염(면)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                    ]
                ],
                ['grpName'=>'치수 변화율', 'sumAvgYn'=>'n', 'childGrp'=>
                    [
                        ['grpName'=>'세탁', 'standardVal'=>'-1.9', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'웨일 방향 (장)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'코스 방향 (폭)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'손세탁', 'standardVal'=>'-1.9', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'웨일 방향 (장)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'코스 방향 (폭)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'드라이클리닝', 'standardVal'=>'-1.9', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'웨일 방향 (장)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'코스 방향 (폭)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'다리미', 'standardVal'=>'-1.9', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'웨일 방향 (장)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'코스 방향 (폭)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'스팀프레스', 'standardVal'=>'-1.9', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'웨일 방향 (장)', 'rowVal'=>'', 'rowValNumber'=>''],
                                ['rowName'=>'코스 방향 (폭)', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                    ]
                ],
                ['grpName'=>'물성 시험', 'sumAvgYn'=>'n', 'childGrp'=>
                    [
                        ['grpName'=>'필링', 'standardVal'=>'', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'필링', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'인장 강도', 'standardVal'=>'', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'인장 강도', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                        ['grpName'=>'마모 강도', 'standardVal'=>'', 'avgVal'=>'0', 'childRow'=>
                            [
                                ['rowName'=>'마모 강도', 'rowVal'=>'', 'rowValNumber'=>''],
                            ]
                        ],
                    ]
                ],
            ];
        } elseif ($params['testType'] == 2) {
            $aFillForm = [
                ['testPlace'=>'','testDt'=>'','handWashMethod'=>'','testComment'=>[]]
            ];
        }

        return $aFillForm;
    }
}


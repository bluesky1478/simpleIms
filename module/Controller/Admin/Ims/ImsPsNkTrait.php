<?php


namespace Controller\Admin\Ims;

use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Component\Ims\NkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

trait ImsPsNkTrait
{
    //세부스케쥴 리스트 가져오기 - 프로토타입(안쓰임)
    public function getListScheDetail() {
        //namku(chk) 이 배열에는 sno키값이 없음. 중요($aReturn배열의 key값이 대체)
        $aReturn = [
            1  => ['scheDetailName' => '정보 수집 / 샘플 확보', 'sortScheDetail' => 1, 'grpSche' => 1, 'grpSmallSche' => 1, 'extectedCompleteDay' => 155 ],
            2  => ['scheDetailName' => '미팅 준비', 'sortScheDetail' => 2, 'grpSche' => 1, 'grpSmallSche' => 1, 'extectedCompleteDay' => 154 ],
            3  => ['scheDetailName' => '사전 미팅', 'sortScheDetail' => 3, 'grpSche' => 1, 'grpSmallSche' => 1, 'extectedCompleteDay' => 153 ],
            4  => ['scheDetailName' => '근무 환경 조사', 'sortScheDetail' => 4, 'grpSche' => 1, 'grpSmallSche' => 1, 'extectedCompleteDay' => 152 ],
            5  => ['scheDetailName' => '디자인 요청', 'sortScheDetail' => 5, 'grpSche' => 1, 'grpSmallSche' => 2, 'extectedCompleteDay' => 151 ],
            6  => ['scheDetailName' => '현장 리서치(디자인)', 'sortScheDetail' => 6, 'grpSche' => 1, 'grpSmallSche' => 2, 'extectedCompleteDay' => 150 ],
            7  => ['scheDetailName' => '샘플 요청', 'sortScheDetail' => 7, 'grpSche' => 1, 'grpSmallSche' => 2, 'extectedCompleteDay' => 149 ],
            8  => ['scheDetailName' => '현장 리서치(소재/기능)', 'sortScheDetail' => 8, 'grpSche' => 1, 'grpSmallSche' => 2, 'extectedCompleteDay' => 148 ],
            9  => ['scheDetailName' => '제안서 제작 요청', 'sortScheDetail' => 9, 'grpSche' => 1, 'grpSmallSche' => 2, 'extectedCompleteDay' => 147 ],
            10 => ['scheDetailName' => '제안 미팅', 'sortScheDetail' => 10, 'grpSche' => 1, 'grpSmallSche' => 3, 'extectedCompleteDay' => 146 ],
            11 => ['scheDetailName' => '디자인 기획 요청', 'sortScheDetail' => 11, 'grpSche' => 1, 'grpSmallSche' => 3, 'extectedCompleteDay' => 145 ],
            12 => ['scheDetailName' => '사전기획', 'sortScheDetail' => 12, 'grpSche' => 2, 'grpSmallSche' => 3, 'extectedCompleteDay' => 144 ],
            13 => ['scheDetailName' => '기획', 'sortScheDetail' => 13, 'grpSche' => 2, 'grpSmallSche' => 3, 'extectedCompleteDay' => 143 ],
            14 => ['scheDetailName' => '제안', 'sortScheDetail' => 14, 'grpSche' => 2, 'grpSmallSche' => 3, 'extectedCompleteDay' => 142 ],
            15 => ['scheDetailName' => '샘플지시서', 'sortScheDetail' => 15, 'grpSche' => 2, 'grpSmallSche' => 4, 'extectedCompleteDay' => 141 ],
            16 => ['scheDetailName' => '샘플완료', 'sortScheDetail' => 16, 'grpSche' => 2, 'grpSmallSche' => 4, 'extectedCompleteDay' => 140 ],
            17 => ['scheDetailName' => '샘플확정', 'sortScheDetail' => 17, 'grpSche' => 2, 'grpSmallSche' => 4, 'extectedCompleteDay' => 139 ],
            18 => ['scheDetailName' => '작지/사양서', 'sortScheDetail' => 18, 'grpSche' => 2, 'grpSmallSche' => 4, 'extectedCompleteDay' => 138 ],
            19 => ['scheDetailName' => '샘플확정', 'sortScheDetail' => 19, 'grpSche' => 3, 'grpSmallSche' => 4, 'extectedCompleteDay' => 136 ],
            20 => ['scheDetailName' => '샘플회수', 'sortScheDetail' => 20, 'grpSche' => 3, 'grpSmallSche' => 5, 'extectedCompleteDay' => 133 ],
            21 => ['scheDetailName' => '아소트', 'sortScheDetail' => 21, 'grpSche' => 3, 'grpSmallSche' => 5, 'extectedCompleteDay' => 130 ],
            22 => ['scheDetailName' => '사양서확정', 'sortScheDetail' => 22, 'grpSche' => 3, 'grpSmallSche' => 5, 'extectedCompleteDay' => 129 ],
            23 => ['scheDetailName' => '작업지시서', 'sortScheDetail' => 23, 'grpSche' => 3, 'grpSmallSche' => 5, 'extectedCompleteDay' => 126 ],
            24 => ['scheDetailName' => '발주', 'sortScheDetail' => 24, 'grpSche' => 3, 'grpSmallSche' => 5, 'extectedCompleteDay' => 124 ],
            25 => ['scheDetailName' => '납품', 'sortScheDetail' => 25, 'grpSche' => 3, 'grpSmallSche' => 5, 'extectedCompleteDay' => 122 ],
        ];

        return $aReturn;
    }

    //공용함수(여기저기서 활용할 가능성이 높은 기능들) start

    //공용함수 : frontend에서 호출 - 리스트 가져오기($params['target']필요. 파라메터는 $params['condition']로 전달)(ex>/ims/admin/ims/popup/ims_pop_customer_issue.php)
    public function getListNk($params){
        $aReturn = ['data'=>[],'message'=>'조회 완료'];
        $imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
        $fncName = 'getList'.ucfirst($params['target']);
        unset($params['condition']['mode']);
        $mData = $imsNkService->$fncName($params['condition']);
        $aReturn['data'] = $mData;
        if (isset($mData['code']) && $mData['code'] != 200) {
            //이렇게 해놔도 response code값은 무조건 200. getListNk함수를 호출하는 부분에서 이렇게 처리함(SlControllerTrait.php의 ajax()함수)
            $aReturn['code'] = $mData['code'];
            $aReturn['message'] = $mData['message'];
        }
        return $aReturn;
    }
    //공용함수 : 레코드 delete from
    public function hardDeleteNk($params) {
        $mSno = isset($params['sno']) ? $params['sno'] : 0;
        if ($mSno === 0) return ['data'=> [],'msg'=>'삭제 실패'];

        $aTargetTable = [
            'aaaaa' => ImsDBName::ADDED_B_S, //부가판매/구매 record delete
            'bbbbb' => ImsDBName::PRODUCT_PLAN, //스타일기획 record delete
            'ccccc' => ImsDBName::CUSTOMER_CONTACT, //고객사 담당자
            'ddddd' => ImsDBName::PROJECT_ISSUE_ACTION, //프로젝트/스타일 이슈 조치내역
            'eeeee' => ImsDBName::ETC_CAR_DRIVE, //차량관리 - 운행
            'fffff' => ImsDBName::ETC_CAR_MAINTAIN, //차량관리 - 정비
            'ggggg' => ImsDBName::BASIC_FITTING_CHECK, //기초정보-피팅체크양식
            'hhhhh' => ImsDBName::BASIC_SIZE_SPEC, //기초정보-사이즈스펙양식
            'iiiii' => ImsDBName::BASIC_PROPOSAL_GUIDE, //기초정보-제안서가이드양식
            'jjjjj' => ImsDBName::CUSTOMER_RECEIVER, //파우치-분류패킹 고객담당자
        ];
        if (isset($aTargetTable[$params['target']])) {
            if (is_array($mSno)) {
                if (count($mSno) === 0) return ['data'=> [],'msg'=>'삭제 실패'];
                $oSearchVo = new SearchVo();
                $oSearchVo->setWhere("sno in (".implode(",",$mSno).")");
                //스타일기획 삭제인 경우 참고파일table도 delete(fileDiv where절에 넣는거 절대 잊지 말것)
                if ($params['target'] === 'bbbbb') {
                    $oFileSearchVo = new SearchVo();
                    $oFileSearchVo->setWhere("fileDiv like 'stylePlanFile%'");
                    $oFileSearchVo->setWhere("eachSno in (" . implode(',', $mSno) . ")");
                    DBUtil2::delete(ImsDBName::PROJECT_FILE, $oFileSearchVo);
                }
            } else $oSearchVo = new SearchVo('sno=?', $mSno);

            DBUtil2::delete($aTargetTable[$params['target']], $oSearchVo);
        }
        return ['data'=> $params,'msg'=>'삭제 완료'];
    }
    //공용함수 : 간단 upsert. 메소드 호출시 table_number 새로 넣으면 switch문에서 새로 정의. 테이블에 regManagerSno컬럼 없어도 제대로 동작함. return 없음
    //내부호출일때만 insert_id return. frontend에서 호출시 insert_id return 안함
    public function setSimpleDbTable($params) {
        $iTableAlias = (int)$params['table_number'];
        if ($iTableAlias === 0) return false;
        switch($iTableAlias) {
            case 2: $sTableNm = ImsDBName::BASIC_ETC_COST; break; //샘플 공임비용/기타비용 항목 upsert
            case 3: $sTableNm = ImsDBName::SAMPLE_FACTORY; break; //샘플실/패턴실 upsert
            case 4: $sTableNm = ImsDBName::ETC_CAR; break; //차량 upsert
            case 5: $sTableNm = ImsDBName::ETC_CAR_MAINTAIN; break; //차량정비 upsert
            case 6: $sTableNm = ImsDBName::ETC_CAR_ADDR; break; //차량 주소지 upsert
            case 7:
                $params['data']['driveStartTime'] = $params['data']['driveStartTimeHour'].':'.$params['data']['driveStartTimeMin'].':00';
                $params['data']['driveEndTime'] = $params['data']['driveEndTimeHour'].':'.$params['data']['driveEndTimeMin'].':59';
                $sTableNm = ImsDBName::ETC_CAR_DRIVE;
                break; //차량 운행기록 upsert
            case 8: $sTableNm = ImsDBName::BUSI_CATE; break; //업종 upsert
            case 9: $sTableNm = ImsDBName::SALES_CUSTOMER_CONTENTS; break; //고객발굴 -> 영업이력 upsert
            case 10: $sTableNm = ImsDBName::BASIC_FITTING_CHECK; break; //기초정보-피팅체크양식 upsert
            case 11: $sTableNm = ImsDBName::BASIC_SIZE_SPEC; break; //기초정보-사이즈스펙양식 upsert
            case 12: $sTableNm = ImsDBName::TEST_REPORT_FILL; break; //자재상세-시험성적서탭-성적서 작성내용 upsert
            case 13: $sTableNm = ImsDBName::PRODUCT_INSPECT; break; //스타일 QC/인라인 검수
            case 14: $sTableNm = ImsDBName::PRODUCT_INSPECT_DELIVERY; break; //스타일 납품 검수

            default: $sTableNm = ''; break;
        }
        if ($sTableNm !== '') {
            //테이블정보 가져와서 json컬럼 고르기 -> json컬럼은 json_encode 해주거나 [] 값으로 넣어주기 + 체크한 값들 합산이 DB에 들어가지는 경우
            $aTmpFlds = DBTableField::callTableFunction($sTableNm);
            $aJsonFlds = [];
            $aChkSumFlds = [];
            foreach ($aTmpFlds as $val) {
                if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
                if (isset($val['checkbox_sum']) && $val['checkbox_sum'] === true) $aChkSumFlds[] = $val['val'];
            }
            if (count($aJsonFlds) > 0) {
                foreach ($aJsonFlds as $val) {
                    if (isset($params['data'][$val]) && is_array($params['data'][$val]) && count($params['data'][$val]) > 0) $params['data'][$val] = json_encode($params['data'][$val]);
                    else $params['data'][$val] = '[]';
                }
            }
            //checkbox sum인 경우(array로 받음 -> 각 값들 합산한 숫자를 DB에 upsert)
            if (count($aChkSumFlds) > 0) {
                foreach ($aChkSumFlds as $val) {
                    if (isset($params['data'][$val]) && is_array($params['data'][$val]) && count($params['data'][$val]) > 0) {
                        $aTmpVal = $params['data'][$val];
                        $params['data'][$val] = 0;
                        foreach ($aTmpVal as $val2) $params['data'][$val] += (int)$val2;
                    } else $params['data'][$val] = 0;
                }
            }

            $iSno = (int)$params['data']['sno'];
            unset($params['data']['sno']);
            $sCurrDt = date('Y-m-d H:i:s');
            $iRegManagerSno = \Session::get('manager.sno');

            //예외사항 처리 : 차량 운행upsert시 도착지 직접입력이면 insert -> sno 넣기
            if ($iTableAlias == 7) {
                if ($params['data']['startAddrSno'] == -1) {
                    $params['registAddrInfoStart']['regManagerSno'] = $iRegManagerSno;
                    $params['registAddrInfoStart']['addrType'] = '거래처';
                    $params['data']['startAddrSno'] = DBUtil2::insert(ImsDBName::ETC_CAR_ADDR, $params['registAddrInfoStart']);
                }
                if ($params['data']['arriveAddrSno'] == -1) {
                    $params['registAddrInfoArrive']['regManagerSno'] = $iRegManagerSno;
                    $params['registAddrInfoArrive']['addrType'] = '거래처';
                    $params['data']['arriveAddrSno'] = DBUtil2::insert(ImsDBName::ETC_CAR_ADDR, $params['registAddrInfoArrive']);
                }
            }

            if ($iSno === 0) {
                $params['data']['regManagerSno'] = $iRegManagerSno;
                $params['data']['regDt'] = $sCurrDt;
                $iSno = DBUtil2::insert($sTableNm, $params['data']);
            } else {
                $params['data']['modDt'] = $sCurrDt;
                DBUtil2::update($sTableNm, $params['data'], new SearchVo('sno=?', $iSno));
            }

            return $iSno;
        } else return false;
    }
    //공용함수 : 컬럼 1개만 update
    public function modifySimpleDbCol($params) {
        $iTableAlias = (int)$params['table_number'];
        if ($iTableAlias === 0) return false;
        switch($iTableAlias) {
            case 1: $sTableNm = ImsDBName::PROJECT_FILE; break; //샘플등록/수정페이지 : 샘플도안 순서바꿨을때
            case 2: $sTableNm = ImsDBName::SAMPLE; break; //프로젝트상세->샘플리스트 : 샘플위치 등록/수정시 실행
            case 3: $sTableNm = ImsDBName::MATERIAL; break; //자재(원단)상세->이미지url update
            case 4: $sTableNm = ImsDBName::CUSTOMER_PACKING; break; //납품(발주)->창고수량 update
            case 5: $sTableNm = ImsDBName::EWORK; break; //납품검수->작지의 revision json update

            default: $sTableNm = ''; break;
        }
        if ($sTableNm !== '') {
            $sVal = is_array($params['data']) ? json_encode($params['data']) : $params['data'];
            $oSearchVo = new SearchVo();
            foreach ($params['where'] as $key => $val) {
                $oSearchVo->setWhere($key." = '".$val."'");
            }
            DBUtil2::update($sTableNm, [$params['colNm'] => $sVal], $oSearchVo);
        }
    }
    //공용함수 : 레코드 간단복사
    public function copyRecordSimple($params) {
        $iSno = (int)$params["sno"];
        if ($iSno > 0) {
            $aList = DBUtil2::getListBySearchVo($params["table_name"], new SearchVo('sno=?', $iSno));
            if (count($aList) === 1) {
                $aInsert = [];
                foreach ($aList[0] as $key=>$val) {
                    if ($key != 'sno') {
                        $aInsert[$key] = $val;
                    }
                }
                $aInsert['regManagerSno'] = \Session::get('manager.sno');

                if (isset($params["chg_val"]) && is_array($params["chg_val"]) && count($params["chg_val"]) > 0) {
                    foreach ($params["chg_val"] as $key=>$val) {
                        $aInsert[$key] = $val;
                    }
                }

//                $aInsert['fittingCheckName'] = $params["name"];
                $aInsert['regDt'] = date('Y-m-d H:i:s');

                DBUtil2::insert($params["table_name"], $aInsert);
            }
        }
    }

    //공용함수 : backend에서만 호출 - 리스트/등록/수정(==상세) 기본폼 return
    public function fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList=[], $aGroupBy=[], $sWhereFldName='sno') {
        $aSkipUpsertFlds = ['regManagerSno', 'regDt', 'modDt'];
        $aTmpFldList = DBTableField::callTableFunction($sTableNm);
        $aJsonFldNames = [];
        foreach ($aTmpFldList as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFldNames[] = $val['val'];
        }
        if (isset($params['upsertSnoGet'])) { //등록/수정(==상세)인 경우 기본 frame 구성. 등록인 경우 select안하고 기본 frame만 response
            //upsert기본frame구성. DBNkIms.php 파일에 def 값 제대로 넣어야함(특히 sno, 상태값)
            $aUpsertForm = [];
            foreach ($aTmpFldList as $val) {
                if (!in_array($val['val'], $aSkipUpsertFlds)) {
                    if (in_array($val['val'], $aJsonFldNames)) $aUpsertForm[$val['val']] = [];
                    else $aUpsertForm[$val['val']] = $val['def'];
                }
            }
            $aUpsertForm['regManagerName'] = ''; //table에는 없지만 select쿼리문으로 가져오는 필드값이라 여기에서 빈값으로나마 넣어둔다

            if ((int)$params['upsertSnoGet'] === 0) { //등록인 경우는 여기에서 return
                $aReturn['info'] = $aUpsertForm;
                return $aReturn;
            }
        }

        //리스트 가져오기
        $iUpsertSnoGet = (int)$params['upsertSnoGet'];
        $searchVo = new SearchVo();
        if ($iUpsertSnoGet !== 0) $searchVo->setWhere('a.'.$sWhereFldName.' = '.$iUpsertSnoGet); //수정(==상세)인 경우(1개 record만 가져옴)
        else { //리스트 가져오는 경우
            $searchData['condition'] = $params;
            $this->refineCommonCondition($searchData['condition'], $searchVo);

            //table order by 설정
            if (isset($searchData['condition']['sort'])) {
                $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
                unset($searchData['condition']['sort']);
                $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo, $params);
            } else {
                $searchVo->setOrder('a.regDt desc');
            }
        }
        //group by 설정
        if (count($aGroupBy) > 0) {
            foreach ($aGroupBy as $val) $searchVo->setGroup($val);
        }

        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        foreach ($allData['listData'] as $key => $val) {
            $allData['listData'][$key] = SlCommonUtil::setDateBlank($val); //0000-00-00값이면 공백값으로 변경
            foreach ($val as $key2 => $val2) {
                if (in_array($key2, $aJsonFldNames)) $allData['listData'][$key][$key2] = json_decode($val2, true);
            }
        }

        if ($iUpsertSnoGet !== 0) { //수정(==상세)인 경우
            //upsert할때만 쓰이는 필드 정리
            foreach ($allData['listData'][0] as $key => $val) {
                if (!in_array($key, $aSkipUpsertFlds)) {
                    $aUpsertForm[$key] = $val;
                }
            }
            $aReturn['info'] = $aUpsertForm;
            return $aReturn;
        } else { //리스트 가져오는 경우
            return [
                'pageEx' => $allData['pageData']->getPage('#'),
                'page' => $allData['pageData'],
                'list' => $allData['listData'],
                'fieldData' => $aFldList
            ];
        }
    }
    //공용함수 : backend(리스트 가져오는 부분)에서 호출 - 공통 검색필터 정리

    private function refineCommonCondition($condition, &$searchVo) {
        //frontend(검색필터)에서 안넣더라도 backend에서 fnRefineListUpsertForm($param) 호출전에 $param에 값 넣어도 쿼리 적용됨(key이름 규칙 지켜야함)

        //multiKey : 검색어
        //searchDateType : 일자범위(예전 범위검색. 지금은 sTextboxRangeStartSch컬럼명,sTextboxRangeEndSch컬럼명 으로 대체). startDt, endDt 파라메터도 필요
        //sTextboxSch컬럼명 : like 검색
        //sTextboxRangeStartSch컬럼명 : 범위(~이상)(컬럼명 >= 값).
        //sTextboxRangeEndSch컬럼명 : 범위(~이하)(컬럼명 <= 값)
        //aChkboxSch컬럼명 : 체크박스(다중선택). 전달받은 값이 배열임
        //aChkboxSumSch컬럼명 : 체크박스(다중선택)(DB에 저장된 값이 선택한 value들의 합산인 경우)(ex>자재 > 사용스타일)
        //sRadioSch컬럼명 : 라디오버튼. 일치값 where(***all값이면 검색에서 제외***) (컬럼명 = 값)
        //sEqualOrEmptySch컬럼명 : 값이 일치하거나 값이 없는 경우(ex>샘플리뷰서upsert > 스타일의 시즌/스타일코드에 맞는 피팅체크양식리스트 가져올때)
        //sExistOrNotSch컬럼명 : 있거나(컬럼명 is not null and 컬럼명 > 0) 없는거(컬럼명 is null or 컬럼명 = 0)(ex>고객발굴리스트 > IMS등록여부)

        foreach ($condition as $key => $val) {
            if ($key === 'multiKey' && !empty($condition[$key])) {
                //멀티 검색
                $whereConditionList = [];
                foreach ($condition[$key] as $keyIndex => $keyCondition) {
                    $key = "REPLACE(" . $keyCondition['key'] . ",' ','')";
                    $keyword = str_replace(' ', '', $keyCondition['keyword']);
                    if (!empty($keyword)) {
                        if ('OR' != $condition['multiCondition']) {
                            $searchVo->setWhere(DBUtil2::bind($key, DBUtil2::BOTH_LIKE));
                            $searchVo->setWhereValue($keyword);
                        } else {
                            $whereConditionList[] = " ( {$key} like '%{$keyword}%' ) ";
                        }
                    }
                }
                if ('OR' == $condition['multiCondition']) {
                    if (count($whereConditionList) > 0) {
                        $searchVo->setWhere(implode(' OR ', $whereConditionList));
                    }
                }
            } elseif ($key === 'searchDateType' && !empty($condition[$key])) {
                //기간검색
                if( !empty($condition['startDt']) ){
                    $searchVo->setWhere($condition[$key].' >= ?');
                    $searchVo->setWhereValue( $condition['startDt'] );
                }
                if( !empty($condition['endDt']) ){
                    $searchVo->setWhere($condition[$key].' <= ?');
                    $searchVo->setWhereValue( $condition['endDt'].' 23:59:59' );
                }
            } elseif (substr($key,0,11) === 'sTextboxSch' && isset($condition[$key]) && $condition[$key] != '') {
                $searchVo->setWhere(lcfirst(str_replace('sTextboxSch','',$key)).' like "%'.$condition[$key].'%"');
            } elseif (substr($key,0,21) === 'sTextboxRangeStartSch' && isset($condition[$key]) && $condition[$key] != '') {
                //범위(~이상)
                $sColNm = lcfirst(str_replace('sTextboxRangeStartSch','',$key));
                $sSchVal = $condition[$key];
                if ($sColNm == 'regDt') {
                    $sColNm = 'a.regDt';
                    $sSchVal .= ' 00:00:00';
                }
                $searchVo->setWhere($sColNm.' >= ?');
                $searchVo->setWhereValue($sSchVal);
            } elseif (substr($key,0,19) === 'sTextboxRangeEndSch' && isset($condition[$key]) && $condition[$key] != '') {
                //범위(~이하)
                $sColNm = lcfirst(str_replace('sTextboxRangeEndSch','',$key));
                $sSchVal = $condition[$key];
                if ($sColNm == 'regDt') {
                    $sColNm = 'a.regDt';
                    $sSchVal .= ' 23:59:59';
                }
                $searchVo->setWhere($sColNm.' <= ?');
                $searchVo->setWhereValue($sSchVal);
            } elseif (substr($key,0,10) === 'aChkboxSch' && isset($condition[$key]) && is_array($condition[$key]) && count($condition[$key]) > 0) {
                //일반적인 checkbox(체크한 value들 합산하는 방식 제외)
                $searchVo->setWhere(lcfirst(str_replace('aChkboxSch','',$key))." in ('".implode("','",$condition[$key])."')");
            } elseif (substr($key,0,13) === 'aChkboxSumSch' && isset($condition[$key]) && is_array($condition[$key]) && count($condition[$key]) > 0) {
                //namku(chk) 이 케이스가 추가되면 아래 switch 문에 case 추가해줘야함
                $iMinKeyVal = 99999;
                foreach ($condition[$key] as $val) {
                    if ($iMinKeyVal > (int)$val) $iMinKeyVal = (int)$val;
                }

                $sColNm = lcfirst(str_replace('aChkboxSumSch','',$key));
                $aWhereInSno = [];
                //컬럼에 따라 다르게 처리해야 하는 부분(table select, NkCodeMap::배열)
                switch ($sColNm) {
                    case 'factoryType': //샘플실/패턴실/매입처-타입
                        $sTargetTableName = ImsDBName::SAMPLE_FACTORY;
                        $aTargetArr = NkCodeMap::FACTORY_TYPE;
                        break;
                    case 'refType': //샘플실/패턴실/매입처-타입
                        $sTargetTableName = ImsDBName::REF_PRODUCT_PLAN;
                        $aTargetArr = NkCodeMap::REF_PRODUCT_PLAN_TYPE;
                        break;
                    default: //usedStyle : 자재-사용스타일
                        $sTargetTableName = ImsDBName::MATERIAL;
                        $aTargetArr = NkCodeMap::MATERIAL_USED_STYLE;
                        break;
                }
                $aTmpTableList = DBUtil2::getComplexList([DBUtil2::getTableInfo($sTargetTableName, 'sno, '.$sColNm)], new SearchVo($sColNm.'>=?', $iMinKeyVal), false, false, true);
                foreach ($aTmpTableList as $val2) {
                    if (count(array_intersect($condition[$key], $this->convertCheckboxSumToArr($aTargetArr, (int)$val2[$sColNm]))) > 0) $aWhereInSno[] = (int)$val2['sno'];
                }

                if (count($aWhereInSno) > 0) $searchVo->setWhere("a.sno in (".implode(",",$aWhereInSno).")");
                else $searchVo->setWhere("1 = 0");
            } elseif (substr($key,0,9) === 'sRadioSch' && isset($condition[$key]) && $condition[$key] != 'all' && $condition[$key] != '') {
                $searchVo->setWhere(lcfirst(str_replace('sRadioSch','',$key)).' = ?');
                $searchVo->setWhereValue($condition[$key]);
            } elseif (substr($key,0,16) === 'sEqualOrEmptySch' && isset($condition[$key]) && $condition[$key] != '') {
                $sFldName = lcfirst(str_replace('sEqualOrEmptySch','',$key));
                $searchVo->setWhere("(".$sFldName." = '' or ".$sFldName." is null or ".$sFldName." = ?)");
                $searchVo->setWhereValue($condition[$key]);
            } elseif (substr($key,0,14) === 'sExistOrNotSch' && isset($condition[$key]) && $condition[$key] != 'all' && $condition[$key] != '') {
                $sFldName = lcfirst(str_replace('sExistOrNotSch','',$key));
                if ($condition[$key] == 'exist') {
                    $searchVo->setWhere('('.$sFldName.' is not null and '.$sFldName.' > 0)');
                } else if ($condition[$key] == 'not') {
                    $searchVo->setWhere('('.$sFldName.' is null or '.$sFldName.' = 0)');
                }
            }
        }
    }

    //공용함수 : json default form 불러오기. Contoller 파일에서 호출함
    public function getJsonDefaultForm($mTableNm) {
        if (is_array($mTableNm)) $sTableNm = $mTableNm['data']; //frontend에서 $.imsPost({~~})로 호출한 경우
        else $sTableNm = $mTableNm; //backend(Controller파일)에서 $this->~~ 로 호출한 경우

        //namku(chk) <script> 안에서 <?php~ 로 값 뿌려주는 경우 : 값이나 name에 ' 문자가 들어가면 안됨
        //no == 부위명, meas/utilQty/costQty == 수량(==가요척), produceManagerSno == 생산처sno, fabricCompany/company == 생산처명, grpMaterialNames == 유사퀄리티 자재명들, cntTestReportByCustomerSno == 시험성적서 갯수(고객사별 array)(저장만 하고 열람은 안함(열람은 최근 갯수 select from 해서 표시))
        $fabric =   ['materialSno'=>'0', 'code'=>'', 'no'=>'', 'attached'=>'', 'fabricName'=>'', 'fabricMix'=>'', 'color'=>'', 'spec'=>'', 'meas'=>'',      'currencyUnit'=>1, 'unitPriceDoller'=>'', 'unitPrice'=>'', 'makeNational'=>'한국', 'moq'=>'', 'onHandYn'=>'', 'btYn'=>'', 'makePeriod'=>'', 'makePeriodNoOnHand'=>'', 'produceManagerSno'=>'', 'fabricCompany'=>'', 'memo'=>'', 'grpMaterialNames'=>'', 'cntTestReportByCustomerSno'=>'']; //디자인기획 : 원단 항목
        $subFabric =['materialSno'=>'0', 'code'=>'', 'no'=>'',                 'subFabricName'=>'',               'color'=>'', 'spec'=>'', 'meas'=>'',      'currencyUnit'=>1, 'unitPriceDoller'=>'', 'unitPrice'=>'', 'makeNational'=>'한국', 'moq'=>'',                                                                         'produceManagerSno'=>'', 'company'=>'',       'memo'=>'', 'grpMaterialNames'=>'']; //디자인기획 : 부자재 항목
        $jsonMark = ['materialSno'=>'0', 'code'=>'', 'no'=>'',                 'subFabricName'=>'',               'color'=>'', 'spec'=>'', 'meas'=>'',      'currencyUnit'=>1, 'unitPriceDoller'=>'', 'unitPrice'=>'', 'makeNational'=>'한국', 'moq'=>'',                                                                         'produceManagerSno'=>'', 'company'=>'',       'memo'=>'', 'grpMaterialNames'=>'']; //디자인기획 : 마크 항목
        $jsonUtil = ['materialSno'=>'0', 'code'=>'', 'no'=>'',                 'utilName'=>'',                                             'utilQty'=>'',   'currencyUnit'=>1, 'unitPriceDoller'=>'', 'unitPrice'=>'',                                                                                                            'produceManagerSno'=>'', 'company'=>'',       'memo'=>'', 'grpMaterialNames'=>'']; //디자인기획 : 기능 항목
        $jsonLaborCost = ['etcCostSno'=>'0', 'code'=>'',                       'name'=>'',                                                 'costQty'=>'',                     'unitPrice'=>'',                                                                                                            'produceManagerSno'=>'', 'company'=>'',       'memo'=>'']; //디자인기획 : 공임비용 항목
        $jsonEtc =       ['etcCostSno'=>'0', 'code'=>'',                       'name'=>'',                                                 'costQty'=>'',                     'unitPrice'=>'',                                                                                                            'produceManagerSno'=>'', 'company'=>'',       'memo'=>'']; //디자인기획 : 기타비용 항목

        $jsonFixedSpec = ['optionName'=>'', 'optionValue'=>'']; //디자인기획 : 확정스펙(사이즈스펙의 기획시 확정값) 항목
        $jsonReviewSpec = ['optionName'=>'', 'madeValue'=>'', 'checkValue'=>'', 'specDesc'=>'']; //샘플(리뷰서) : 사이즈스펙의 리뷰시 제작샘플치수값,피팅검토치수값,체크의견 항목
        $jsonReviewCheck = ['cntType'=>0, 'checkType'=>'', 'checkName'=>'', 'checkYn'=>'', 'customerCheckYn'=>'', 'checkDesc'=>'']; //샘플(리뷰서) : 피팅체크 체크항목
        $jsonReviewComment = ['commentCompany'=>'', 'commentType'=>'', 'commentDiff'=>'', 'commentMethod'=>'']; //샘플(리뷰서) : 샘플실의견&생산유의사항 항목
        $jsonConfirmSpec = ['optionName'=>'', 'optionValue'=>'']; //샘플(확정서) : 확정사이즈(사이즈스펙의 샘플확정시 확정값) 항목
        $jsonConfirmSuggest = ['suggestContent'=>'', 'suggestCheckYn'=>'']; //샘플(확정서) : 이노버 추가 제안 내용 (사이즈스펙 외) 항목
        $jsonConfirmRequest = ['requestContent'=>'', 'requestDesc'=>'']; //샘플(확정서) : 고객사 요청 사항 항목
        $jsonConfirmGuide = ['guideType'=>'', 'guideContent'=>'']; //샘플(확정서) : 안내사항 항목

        $jsonExpectSales = ['productName'=>'', 'prdSeason'=>'ALL', 'prdStyle'=>'JK', 'saleQty'=>'', 'unitPrice'=>'']; //고객발굴 : 예상매출 항목
        $jsonProposalGuide = ['guideName'=>'', 'guideDesc'=>'', 'guideFileUrl'=>'']; //프로젝트상세 -> 영업기획서 작성시 : 제안서가이드 기본폼

        $jsonFitSpec = ['optionName'=>'', 'optionRange'=>'', 'optionValue'=>'', 'optionUnit'=>'']; //기초관리-사이즈스펙양식-측정항목
        $jsonBasicFittingCheck = ['checkType'=>'', 'checkName'=>'']; //기초관리-피팅체크양식-체크항목

        switch ($sTableNm) {
            case ImsDBName::PRODUCT_PLAN: //디자인기획페이지
            case ImsDBName::SAMPLE: //샘플지시서페이지
                $aReturn = ['jsonFitSpec' => $jsonFitSpec, 'jsonFixedSpec' => $jsonFixedSpec, 'jsonUtil' => $jsonUtil, 'fabric' => $fabric, 'subFabric' => $subFabric, 'jsonMark' => $jsonMark, 'jsonLaborCost' => $jsonLaborCost, 'jsonEtc' => $jsonEtc, ];
                break;
            case ImsDBName::SAMPLE.'_review': //샘플리뷰서페이지
                $aReturn = ['jsonFitSpec' => $jsonFitSpec, 'jsonFixedSpec' => $jsonFixedSpec, 'jsonReviewSpec' => $jsonReviewSpec, 'jsonReviewCheck' => $jsonReviewCheck, 'jsonReviewComment' => $jsonReviewComment ];
                break;
            case ImsDBName::SAMPLE.'_confirm': //샘플확정서페이지
                $aReturn = ['jsonFitSpec' => $jsonFitSpec, 'jsonFixedSpec' => $jsonFixedSpec, 'jsonReviewSpec' => $jsonReviewSpec, 'jsonConfirmSpec' => $jsonConfirmSpec, 'jsonConfirmSuggest' => $jsonConfirmSuggest, 'jsonConfirmRequest' => $jsonConfirmRequest, 'jsonReviewCheck' => $jsonReviewCheck, 'jsonConfirmGuide' => $jsonConfirmGuide ];
                break;
            case ImsDBName::SALES_CUSTOMER: //고객발굴페이지
                $aReturn = ['jsonExpectSales' => $jsonExpectSales, ];
                break;
            case ImsDBName::PROJECT_SALES_PLAN_FILL: //영업기획서작성 페이지
                $aReturn = ['jsonProposalGuide' => $jsonProposalGuide, ];
                break;
            case ImsDBName::BASIC_SIZE_SPEC: //기초정보-사이즈스펙양식페이지
                $aReturn = ['jsonFitSpec' => $jsonFitSpec, ];
                break;
            case ImsDBName::BASIC_FITTING_CHECK: //기초정보-피팅체크양식페이지
                $aReturn = ['jsonBasicFittingCheck' => $jsonBasicFittingCheck, ];
                break;
            default:
                $aReturn = [];
                break;
        }

        if (is_array($mTableNm)) return ['data'=>$aReturn];
        else return $aReturn;
    }
    //공용함수 : backend에서 호출 - 숫자(checkbox 체크한 value들을 합산한 값) -> 쪼개기
    private function convertCheckboxSumToArr($aTarget, $iSumVal, $sReturnType='num') {
        krsort($aTarget);
        $aReturn = [];
        foreach ($aTarget as $key2 => $val2) {
            if ($iSumVal === 0) break;
            if ($iSumVal >= $key2) {
                if ($sReturnType == 'num') $aReturn[] = $key2;
                else $aReturn[] = $val2;
                $iSumVal -= $key2;
            }
        }
        krsort($aReturn);
        return array_values($aReturn);
    }
    //공용함수 : 업종리스트를 depth에 맞게 가져오기
    public function getBusiCateListByDepth() {
        $aData['parent_cate_list'] = [];
        $aData['cate_list'] = [0=>[0=>'선택']];
        $aCateList = DBUtil2::getListBySearchVo(ImsDBName::BUSI_CATE, new SearchVo());
        if (count($aCateList) > 0) {
            foreach ($aCateList as $val) {
                if ($val['parentBusiCateSno'] == 0) $aData['parent_cate_list'][$val['sno']] = $val['cateName'];
                else $aData['cate_list'][$val['parentBusiCateSno']][$val['sno']] = $val['cateName'];
            }
        }
        $aData['parent_cate_list'] = (object)$aData['parent_cate_list'];
        foreach ($aData['cate_list'] as $key => $val) $aData['cate_list'][$key][0] = '선택';
        foreach ($aData['cate_list'] as $key => $val) {
            $aData['cate_list'][$key] = (object)$val;
        }
        $aData['cate_list'] = (object)$aData['cate_list'];

        $aReturn['data'] = $aData;
        return $aReturn;
    }

    //공용함수(여기저기서 활용할 가능성이 높은 기능들) end


    /**
     * 원단 입고 등록
     * @param $params
     * @return array
     */
    public function saveStoredFabricInput($params){
        $sCurrDt = date('Y-m-d H:i:s');
        $iResgisterSno = \Session::get('manager.sno');
        if ((int)$params['StoredFabric']['sno'] != 0) {
            $params['StoredFabricInput']['fabricSno'] = (int)$params['StoredFabric']['sno'];
            unset($params['StoredFabric']);
        } else {
            $params['StoredFabric']['regManagerSno'] = $iResgisterSno;
            $params['StoredFabric']['regDt'] = $sCurrDt;
        }
        $params['StoredFabricInput']['regManagerSno'] = $iResgisterSno;
        $params['StoredFabricInput']['regDt'] = $sCurrDt;
        $params['StoredFabricInput']['unitPrice'] = (int)str_replace(',','', $params['StoredFabricInput']['unitPrice']);
        $params['StoredFabricInput']['inputQty'] = (int)str_replace(',','', $params['StoredFabricInput']['inputQty']);
        unset($params['mode']);

        $imsStoredService = SlLoader::cLoad('ims', 'ImsStoredService');
        $imsStoredService->saveStoredFabricInput($params);
        return ['data'=> 0,'msg'=>'저장 완료'];
    }
    //원단 수정
    public function modifyStoredFabric($params) {
        $sCurrDt = date('Y-m-d H:i:s');
        $params['StoredFabric']['regDt'] = $sCurrDt;
        unset($params['mode']);

        $imsStoredService = SlLoader::cLoad('ims', 'ImsStoredService');
        return $imsStoredService->modifyStoredFabric($params);
    }
    //원단 삭제
    public function deleteStoredFabric($params) {
        $searchVo = new SearchVo('sno=?', (int)$params['storedSno']);
        DBUtil2::update(ImsDBName::STORED_FABRIC, ['delFl'=>'y'], $searchVo);
        return [];
    }

    //원단 입고정보 수정
    public function modifyStoredFabricInput($params)
    {
        $sCurrDt = date('Y-m-d H:i:s');
        if ((int)$params['StoredFabric']['sno'] != 0) {
            $params['StoredFabricInput']['fabricSno'] = (int)$params['StoredFabric']['sno'];
            unset($params['StoredFabric']);
        } else {
            $params['StoredFabric']['regDt'] = $sCurrDt;
        }
        $params['StoredFabricInput']['modDt'] = $sCurrDt;
        $params['StoredFabricInput']['unitPrice'] = (int)str_replace(',', '', $params['StoredFabricInput']['unitPrice']);
        $params['StoredFabricInput']['inputQty'] = (int)str_replace(',', '', $params['StoredFabricInput']['inputQty']);
        unset($params['mode']);

        $imsStoredService = SlLoader::cLoad('ims', 'ImsStoredService');
        $imsStoredService->modifyStoredFabricInput($params);
        return ['data' => 0, 'msg' => '저장 완료'];
    }
    //원단 입고건 삭제
    public function deleteStoredFabricInput($params) {
        $searchVo = new SearchVo('sno=?', (int)$params['inputSno']);
        DBUtil2::update(ImsDBName::STORED_FABRIC_INPUT, ['delFl'=>'y'], $searchVo);
        return [];
    }
    //원단 출고 등록
    public function saveStoredFabricOutput($params){
        $sCurrDt = date('Y-m-d H:i:s');
        $aInsertOutput = [
            'inputSno' => $params['sno'],
            'regManagerSno' => \Session::get('manager.sno'),
            'outQty' => (int)str_replace(',','', $params['outQty']),
            'outReason' => $params['outReason'],
            'regDt' => $sCurrDt,
        ];

        $imsStoredService = SlLoader::cLoad('ims', 'ImsStoredService');
        $imsStoredService->saveStoredFabricOutput($aInsertOutput);
        return ['data'=> 0,'msg'=>'저장 완료'];
    }
    //원단 출고리스트 가져오기
    public function getListStoredOutput($params){
        return ['data'=>$this->imsStoredService->getListStoredOutput($params),'msg'=>'조회 완료'];
    }
    //원단 출고건 수정
    public function modifyStoredFabricOutput($params) {
        $aUpdateOutput = [
            'outQty' => (int)str_replace(',','',$params['outQty']),
            'outReason' => $params['outReason'],
            'modDt' => date('Y-m-d H:i:s'),
        ];
        $outputSno = (int)$params['sno'];

        $searchVo = new SearchVo('sno=?', $outputSno);
        DBUtil2::update(ImsDBName::STORED_FABRIC_OUT, $aUpdateOutput, $searchVo);

        return ['data'=> 0,'msg'=>'저장 완료'];
    }
    //원단 출고건 삭제
    public function deleteStoredFabricOutput($params) {
        $searchVo = new SearchVo('sno=?', (int)$params['outputSno']);
        DBUtil2::update(ImsDBName::STORED_FABRIC_OUT, ['delFl'=>'y'], $searchVo);
        return [];
    }

    //기획/제작 일괄수정, 리오더/기성복 일괄수정, 프로젝트관리->전체 일괄수정
    public function updateDesignMulti($params){
        $imsNkService = SlLoader::cLoad('imsv2','ImsNkMultiUpdateService');
        $imsNkService->updateDesignMulti($params);
        return ['data'=> 0,'msg'=>'저장 완료'];
    }
    //발주 일괄수정
    public function updateQcMulti($params){
        $imsNkService = SlLoader::cLoad('imsv2','ImsNkMultiUpdateService');
        $imsNkService->updateQcMulti($params);
        return ['data'=> 0,'msg'=>'저장 완료'];
    }

    //자재분류 등록/수정
    public function setMaterialTypeDetail($params){
        $sCurrDt = date('Y-m-d H:i:s');
        $iResgisterSno = \Session::get('manager.sno');
        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        if ($iSno === 0) {
            $params['data']['regManagerSno'] = $iResgisterSno;
            $params['data']['regDt'] = $sCurrDt;

            DBUtil2::insert(ImsDBName::MATERIAL_TYPE_DETAIL, $params['data']);
        } else {
            $params['data']['modDt'] = $sCurrDt;

            DBUtil2::update(ImsDBName::MATERIAL_TYPE_DETAIL, $params['data'], new SearchVo('sno=?', $iSno));
        }

        return ['data'=> 0,'msg'=>'저장 완료'];
    }
    //자재 등록/수정
    public function setMaterialNk($params) {
        $sCurrDt = date('Y-m-d H:i:s');
        $iResgisterSno = \Session::get('manager.sno');
        $iSno = (int)$params['data']['sno'];
        //매입처 신규등록일 때
        if ($params['data']['buyerSno'] == -1) {
            $params['data']['buyerSno'] = DBUtil2::insert(ImsDBName::SAMPLE_FACTORY, ['factoryName'=>$params['data']['factoryName'], 'factoryPhone'=>$params['data']['factoryPhone'], 'factoryAddress'=>$params['data']['factoryAddress'], 'factoryType'=>4, 'regDt'=>$sCurrDt]);
        }
        //품목구분 신규등록일 때
        if ($params['data']['typeDetailSno'] == -1) {
            $params['data']['typeDetailSno'] = DBUtil2::insert(ImsDBName::MATERIAL_TYPE_DETAIL, ['regManagerSno'=>$iResgisterSno, 'materialTypeByDetail'=>$params['data']['materialType'], 'materialTypeText'=>$params['data']['materialTypeText'], 'regDt'=>$sCurrDt]);
        }
        unset($params['data']['sno'], $params['data']['factoryName'], $params['data']['materialTypeText']);
        //다중체크하는 항목(ex>사용스타일)은 배열(체크한 checkbox들 value)로 받으므로 값 더하기
        foreach ($params['data'] as $key => $val) {
            if (is_array($val)) {
                if (count($val) > 0) $params['data'][$key] = array_sum($val);
                else $params['data'][$key] = 0;
            }
        }
        //upsert 처리
        if ($iSno === 0) {
            $params['data']['regManagerSno'] = $iResgisterSno;
            $params['data']['regDt'] = $sCurrDt;
            DBUtil2::insert(ImsDBName::MATERIAL, $params['data']);
        } else {
            //수정이력 insert
            $aCurrInfo = DBUtil2::getOne(ImsDBName::MATERIAL, 'sno', $iSno);
            $aUpdateLogDesc = [];
            foreach ($params['data'] as $key => $val) {
                if (isset($aCurrInfo[$key]) && $val != $aCurrInfo[$key]) {
                    $aUpdateLogDesc[$key] = ['before'=>addslashes(str_replace("'","\"",$aCurrInfo[$key])), 'after'=>addslashes(str_replace("'","\"",$val))];
                }
            }
            if (count($aUpdateLogDesc) > 0) {
                DBUtil2::insert(ImsDBName::MATERIAL_UPDATE_LOG, ['materialSno'=>$iSno, 'regManagerSno'=>$iResgisterSno, 'updateDesc'=>json_encode($aUpdateLogDesc, JSON_UNESCAPED_UNICODE ), 'regDt'=>$sCurrDt]);
            }

            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::MATERIAL, $params['data'], new SearchVo('sno=?', $iSno));
        }

        return ['data'=> 0,'msg'=>'저장 완료'];
    }

    //프로젝트별 부가판매/구매 테이블구조 가져오기
    public function getTableSchemeAddedBuySale() {
        $imsNkService = SlLoader::cLoad('imsv2','ImsNkService');
        return ['data'=> $imsNkService->getTableSchemeAddedBuySale(),'msg'=>'저장 완료'];
    }
    //부가판매/구매 등록/수정
    public function setAddedBS($param) {
        $sCurrDt = date('Y-m-d H:i:s');
        $aInsert = $aUpdate = [];
        foreach ($param['data'] as $val) {
            if ((int)$val['sno'] === 0) {
                unset($val['sno']);
                $val['regDt'] = $sCurrDt;
                $aInsert[] = $val;
            } else {
                unset($val['projectSno'], $val['regManagerSno']);
                $val['modDt'] = $sCurrDt;
                $aUpdate[] = $val;
            }
        }

        if (count($aInsert) > 0) {
            foreach ($aInsert as $val) {
                DBUtil2::insert(ImsDBName::ADDED_B_S, $val);
            }
        }
        if (count($aUpdate) > 0) {
            //수정한 내용이 없는 레코드는 update에서 제외시킴
            $aUpdateSno = [];
            foreach ($aUpdate as $val) $aUpdateSno[] = (int)$val['sno'];
            $searchVo = new SearchVo('projectSno', $aUpdate[0]['projectSno']);
            $searchVo->setWhere("sno in (" . implode(',', $aUpdateSno) . ")");
            $aTmpOriginList = DBUtil2::getListBySearchVo(ImsDBName::ADDED_B_S, $searchVo);
            $aOriginList = [];
            foreach ($aTmpOriginList as $val) $aOriginList[$val['sno']] = $val;
            $aUpdateRows = [];
            foreach ($aUpdate as $val) {
                foreach ($val as $key2 => $val2) {
                    if ($key2 != 'modDt' && $val2 != $aOriginList[$val['sno']][$key2]) {
                        $aUpdateRows[] = $val;
                        break;
                    }
                }
            }

            if (count($aUpdateRows) > 0) {
                foreach ($aUpdateRows as $val) {
                    $iTmp = (int)$val['sno'];
                    unset($val['sno']);
                    DBUtil2::update(ImsDBName::ADDED_B_S, $val, new SearchVo('sno=?', $iTmp));
                }
            }
        }
    }

    //프로젝트 -> 스타일(기성복) -> 생산가관리(근거) 저장
    public function setProductPrdCost($params) {
        if (isset($params['data']) && is_array($params['data']) && count($params['data']) > 0) {
            $iStyleSno = isset($params['data'][0]['styleSno']) ? (int)$params['data'][0]['styleSno'] : 0;
            if ($iStyleSno === 0) exit;

            $sCurrDt = date('Y-m-d H:i:s');
            $aSnos = $aExistSnos = $aInsert = $aUpdate = [];
            foreach ($params['data'] as $val) {
                $iSno = (int)$val['sno'];
                if ($iSno === 0) { //insert 대상
                    unset($val['sno']);
                    $val['regDt'] = $sCurrDt;
                    $aInsert[] = $val;
                } else { //update 대상
                    $aSnos[] = $iSno;
                    unset($val['regDt'], $val['styleSno']);
                    $val['modDt'] = $sCurrDt;
                    $aUpdate[] = $val;
                }
            }
            //delete
            $aExistList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCT_PRD_COST, new SearchVo('styleSno=?', $iStyleSno));
            if (count($aExistList) > 0) {
                foreach ($aExistList as $val) $aExistSnos[] = $val['sno'];
                $aDiffSnos = array_diff($aExistSnos, $aSnos);
                if (count($aDiffSnos) > 0) {
                    $searchVo = new SearchVo();
                    $searchVo->setWhere("sno in (" . implode(',', $aDiffSnos) . ")");
                    DBUtil2::delete(ImsDBName::PRODUCT_PRD_COST, $searchVo);
                }
            }
            $iTotalAmt = 0;
            //insert
            if (count($aInsert) > 0) {
                foreach ($aInsert as $val) {
                    $iTotalAmt += (int)$val['prdCostAmount'];
                    DBUtil2::insert(ImsDBName::PRODUCT_PRD_COST, $val);
                }
            }
            //update
            if (count($aUpdate) > 0) {
                foreach ($aUpdate as $val) {
                    $iTotalAmt += (int)$val['prdCostAmount'];
                    $iPrdCostSno = (int)$val['sno'];
                    unset($val['sno']);
                    DBUtil2::update(ImsDBName::PRODUCT_PRD_COST, $val, new SearchVo('sno=?', $iPrdCostSno));
                }
            }
            DBUtil2::update(ImsDBName::PRODUCT, ['prdCost'=>$iTotalAmt, 'estimateCost'=>$iTotalAmt], new SearchVo('sno=?', $iStyleSno));
        }
    }
    //프로젝트 기획일정 upsert
    public function setProjectPlanSche($params) {
        $iProjectSno = (int)$params['project_sno'];
        if ($iProjectSno === 0) exit;

        $sCurrDt = date('Y-m-d H:i:s');
        $iResgisterSno = \Session::get('manager.sno');
        foreach ($params['data'] as $key => $val) {
            foreach ($val as $key2 => $val2) {
                $aUpsert = [
                    'projectSno'=>$iProjectSno, 'regManagerSno'=>$iResgisterSno, 'scheType'=>$key, 'scheStep'=>$key2, 'scheDt'=>$val2, 'regDt'=>$sCurrDt
                ];
                $sSql = 'insert into '.ImsDBName::PROJECT_PLAN_SCHE.' ('.implode(',',array_keys($aUpsert)).') values ("'.implode('","',array_values($aUpsert)).'" ) ON DUPLICATE KEY UPDATE scheDt = "'.$val2.'", modDt ="'.$sCurrDt.'"';
                DBUtil2::runSql($sSql);
            }
        }
        DBUtil2::update(ImsDBName::PROJECT_EXT, ['planScheMemo'=>$params['memo']], new SearchVo('projectSno=?', $iProjectSno));
    }

    //파일 업로드하는 시점에 파일내용을 DB에 저장하고 싶을때 호출하는 함수.(등록시 : 파일업로드 -> insert -> vue.js변수 sno=0을 insert_id로 변경 -> 저장시 sno!=0이므로 등록임에도 update. 이러면 등록일때 modDt에도 값 들어가진다)
    //스타일 기획 업로드한 이미지url 저장(upsert)
    public function setStylePlanFileInfo($params) {
        $iStyleSno = (int)$params['styleSno'];
        if ($iStyleSno === 0) exit;
        $iSno = (int)$params['targetSno'];
        if (isset($params['filePlan'])) $aUpsert = ['filePlan'=>$params['filePlan']];
        else return ['data'=>$iSno,'msg'=>'타입오류'];
        if ($iSno === 0) { //insert -> response insert_id
            //최근등록 레코드가 순서 상위
            DBUtil2::runSql('update '.ImsDBName::PRODUCT_PLAN.' set sortNum = sortNum + 1 where styleSno = '.$iStyleSno);
            //insert
            $aUpsert['styleSno'] = $iStyleSno;
            $aUpsert['regManagerSno'] = \Session::get('manager.sno');
            $aUpsert['regDt'] = date('Y-m-d H:i:s');
            $iSno = DBUtil2::insert(ImsDBName::PRODUCT_PLAN, $aUpsert);

            return ['data'=>$iSno,'msg'=>'등록 완료'];
        } else { //update
            DBUtil2::update(ImsDBName::PRODUCT_PLAN, $aUpsert, new SearchVo('sno=?', $iSno));
            return ['data'=>$iSno,'msg'=>'수정 완료'];
        }
    }
    //스타일 기획 upsert
    public function setStylePlan($params) {
        $iStyleSno = (int)$params['styleSno'];
        if ($iStyleSno === 0) exit;

        //테이블정보 가져와서 json컬럼 고르기 -> json컬럼은 json_encode 해주거나 [] 값으로 넣어주기
        $aTmpFlds = DBTableField::callTableFunction(ImsDBName::PRODUCT_PLAN);
        $aJsonFlds = [];
        foreach ($aTmpFlds as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
        }
        if (count($aJsonFlds) > 0) {
            foreach ($aJsonFlds as $val) {
                if (isset($params['data'][$val]) && is_array($params['data'][$val]) && count($params['data'][$val]) > 0) $params['data'][$val] = json_encode($params['data'][$val]);
                else $params['data'][$val] = '[]';
            }
        }

        $sCurrDt = date('Y-m-d H:i:s');
        $iPlanSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        if ($iPlanSno === 0) {
            //최근등록 레코드가 순서 상위
            DBUtil2::runSql('update '.ImsDBName::PRODUCT_PLAN.' set sortNum = sortNum + 1 where styleSno = '.$iStyleSno);
            //insert
            $params['data']['styleSno'] = $iStyleSno;
            $params['data']['regManagerSno'] = \Session::get('manager.sno');
            $params['data']['regDt'] = $sCurrDt;
            $iPlanSno = DBUtil2::insert(ImsDBName::PRODUCT_PLAN, $params['data']);

            if (isset($params['data']['fileList']['stylePlanFile1']['files']) && count($params['data']['fileList']['stylePlanFile1']['files']) > 0) {
                $oImsService = SlLoader::cLoad('ims', 'imsService');
                $aSaveData = [
                    'customerSno'=>$params['data']['customerSno'], 'projectSno'=>$params['data']['projectSno'], 'styleSno'=>$params['data']['styleSno'], 'eachSno'=>$iPlanSno,
                    'fileDiv'=>'stylePlanFile1', 'fileList'=>$params['data']['fileList']['stylePlanFile1']['files'], 'memo'=>$params['data']['fileList']['stylePlanFile1']['memo'],
                ];
                $oImsService->saveProjectFiles($aSaveData);
            }
            if (isset($params['data']['fileList']['stylePlanFile2']['files']) && count($params['data']['fileList']['stylePlanFile2']['files']) > 0) {
                $oImsService = SlLoader::cLoad('ims', 'imsService');
                $aSaveData = [
                    'customerSno'=>$params['data']['customerSno'], 'projectSno'=>$params['data']['projectSno'], 'styleSno'=>$params['data']['styleSno'], 'eachSno'=>$iPlanSno,
                    'fileDiv'=>'stylePlanFile2', 'fileList'=>$params['data']['fileList']['stylePlanFile2']['files'], 'memo'=>$params['data']['fileList']['stylePlanFile2']['memo'],
                ];
                $oImsService->saveProjectFiles($aSaveData);
            }
        } else {
            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::PRODUCT_PLAN, $params['data'], new SearchVo('sno=?', $iPlanSno));
        }
        //고객제공샘플(사이즈스펙 관련) insert
        if (isset($params['customerSample']) && is_array($params['customerSample'])) {
            if (count($params['customerSample']) === 0) {
                DBUtil2::delete(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, new SearchVo('productPlanSno=?', $iPlanSno));
            } else {
                //고객제공샘플 바뀌었는지 확인하기
                $bFlagUpdate = false;
                $iCntUpdateOption = 0;
                foreach ($params['customerSample'] as $val) {
                    foreach ($val as $val2) {
                        $iCntUpdateOption++;
                    }
                }
                $oOptionSearchVo = new SearchVo('productPlanSno=?', $iPlanSno);
                $oOptionSearchVo->setOrder('sortNum asc');
                $aExistOptionList = DBUtil2::getListBySearchVo(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, $oOptionSearchVo);
                if (count($aExistOptionList) != $iCntUpdateOption) {
                    $bFlagUpdate = true;
                } else {
                    $sPrevName = '';
                    $iKey = 0;
                    $aExistOption2DepthList = [];
                    foreach ($aExistOptionList as $val) {
                        if ($sPrevName != '' && $sPrevName != $val['optionName']) $iKey++;
                        $sPrevName = $val['optionName'];
                        $aExistOption2DepthList[$iKey][] = ['optionSize'=>$val['optionSize'],'optionName'=>$val['optionName'],'optionValue'=>$val['optionValue'],'optionUnit'=>$val['optionUnit']];
                    }
                    foreach ($params['customerSample'] as $key => $val) {
                        foreach ($val as $key2 => $val2) {
                            if (!isset($aExistOption2DepthList[$key][$key2])) {
                                $bFlagUpdate = true;
                                break;
                            }
                            foreach ($val2 as $key3 => $val3) {
                                if (!isset($aExistOption2DepthList[$key][$key2][$key3]) || $val3 != $aExistOption2DepthList[$key][$key2][$key3]) {
                                    $bFlagUpdate = true;
                                    break;
                                }
                            }
                            if ($bFlagUpdate === true) break;
                        }
                        if ($bFlagUpdate === true) break;
                    }
                }

                if ($bFlagUpdate === true) {
                    DBUtil2::delete(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, new SearchVo('productPlanSno=?', $iPlanSno));
                    $iSort = 0;
                    foreach ($params['customerSample'] as $val) { //항목 반복
                        foreach ($val as $val2) { //사이즈 반복
                            $iSort++;
                            $val2['productPlanSno'] = $iPlanSno;
                            $val2['sortNum'] = $iSort;
                            $val2['regDt'] = $sCurrDt;
                            DBUtil2::insert(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, $val2);
                        }
                    }
                }
            }
        } else DBUtil2::delete(ImsDBName::CUSTOMER_FIT_SPEC_OPTION, new SearchVo('productPlanSno=?', $iPlanSno));

        //스타일기획 레퍼런스를 연결한 경우 레퍼런스에 (해당 스타일의)고객사가 연결되어 있는지 확인
        $iRefSno = (int)$params['data']['refStylePlanSno'];
        $iCustSno = (int)$params['data']['customerSno'];
        if ($iRefSno > 0 && $iCustSno > 0) {
            $oSVRelCust = new SearchVo('refStylePlanSno=?', $iRefSno);
            $oSVRelCust->setWhere('customerSno = '.$iCustSno);
            $aRelCustList = DBUtil2::getListBySearchVo(ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION, $oSVRelCust);
            if (count($aRelCustList) == 0) {
                DBUtil2::insert(ImsDBName::REF_PRODUCT_PLAN_CUSTOMER_RELATION, ['refStylePlanSno'=>$iRefSno, 'customerSno'=>$iCustSno]);
            }
        }

        return ['data'=>$iPlanSno,'message'=>'저장 완료'];
    }
    //스타일 기획리스트 순서 바꾸기
    public function updateSortStylePlan($params) {
        $aSortSnos = (array)$params['data'];
        if (count($aSortSnos) > 0) {
            foreach ($aSortSnos as $key => $val) {
                $iSort = $key + 1;
                DBUtil2::update(ImsDBName::PRODUCT_PLAN, ['sortNum'=>$iSort], new SearchVo('sno=?', $val));
            }
        }
    }
    //스타일기획 복사
    public function registCopyMultiStylePlan($params) {
        $aSnos = (array)$params['data'];
        if (count($aSnos) === 0) return false;

        $searchVo = new SearchVo();
        $searchVo->setWhere("sno in (" . implode(',', $aSnos) . ")");
        $searchVo->setOrder('a.styleSno asc, a.sortNum desc');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PRODUCT_PLAN ], 'field' => ["a.*"]],
        ];
        $aList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $searchVo, false, false, true);

        $sCurrDt = date('Y-m-d H:i:s');
        $iResgisterSno = \Session::get('manager.sno');
        $aInsertSnoByOriginSno = [];
        foreach ($aList as $val) {
            $iOriginSno = $val['sno'];
            unset($val['sno'],$val['modDt']);
            $val['sortNum'] = 1;
            $val['regManagerSno'] = $iResgisterSno;
            $val['planConcept'] = $val['planConcept'].' (복사)';
            $val['regDt'] = $sCurrDt;
            //최근등록 레코드가 순서 상위
            DBUtil2::runSql('update '.ImsDBName::PRODUCT_PLAN.' set sortNum = sortNum + 1 where styleSno = '.$val['styleSno']);
            $aInsertSnoByOriginSno[$iOriginSno] = DBUtil2::insert(ImsDBName::PRODUCT_PLAN, $val);
        }

        //참고파일(file테이블) 복사
        $searchVo = new SearchVo();
        $searchVo->setWhere("fileDiv like 'stylePlanFile%'");
        $searchVo->setWhere("eachSno in (" . implode(',', $aSnos) . ")");
        $aFileTableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
        ];
        $aFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($aFileTableInfo,false), $searchVo, false, false, true);
        if (count($aFileList) > 0) {
            foreach ($aFileList as $val) {
                if (isset($aInsertSnoByOriginSno[$val['eachSno']])) {
                    $val['eachSno'] = $aInsertSnoByOriginSno[$val['eachSno']];
                    unset($val['sno'],$val['modDt']);
                    $val['regManagerSno'] = $iResgisterSno;
                    $val['regDt'] = $sCurrDt;

                    DBUtil2::insert(ImsDBName::PROJECT_FILE, $val);
                }
            }
        }
    }

    //고객담당자 upsert
    public function setCustomerContact($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->setCustomerContact($params);
    }
    //고객담당자 메인담당자로 지정
    public function setCustomerMainContact($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->setCustomerMainContact($params);
    }

    //프로젝트/스타일 이슈 upsert
    public function setProjectIssue($params) {
        $imsIssueService = SlLoader::cLoad('ims', 'ImsProjectIssueService');
        return $imsIssueService->setProjectIssue($params);
    }
    //프로젝트/스타일 이슈 조치사항 upsert
    public function setProjectIssueAction($params) {
        $imsIssueService = SlLoader::cLoad('ims', 'ImsProjectIssueService');
        return $imsIssueService->setProjectIssueAction($params);
    }

    //샘플정보 저장(NEW)
    public function saveSampleNk($params) {
        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        $sCurrDt = date('Y-m-d H:i:s');
        
        //테이블정보 가져와서 json컬럼 고르기 -> json컬럼은 json_encode 해주거나 [] 값으로 넣어주기
        $aTmpFlds = DBTableField::callTableFunction(ImsDBName::SAMPLE);
        $aJsonFlds = [];
        foreach ($aTmpFlds as $val) {
            if (isset($val['json']) && $val['json'] === true) $aJsonFlds[] = $val['val'];
        }
        if (count($aJsonFlds) > 0) {
            foreach ($aJsonFlds as $val) {
                if (isset($params['data'][$val]) && is_array($params['data'][$val]) && count($params['data'][$val]) > 0) $params['data'][$val] = json_encode($params['data'][$val]);
                else $params['data'][$val] = '[]';
            }
        }

        if ($iSno === 0) {
            //customerSno projectSno 구하기
            if (!isset($params['data']['customerSno']) || $params['data']['customerSno'] == 0 || !isset($params['data']['projectSno']) || $params['data']['projectSno'] == 0) {
                $aStyleInfo = DBUtil2::getOne(ImsDBName::PRODUCT,'sno',$params['data']['styleSno']);
                $params['data']['customerSno'] = $aStyleInfo['customerSno'];
                $params['data']['projectSno'] = $aStyleInfo['projectSno'];
            }

            $params['data']['regDt'] = $sCurrDt;
            $iSno = DBUtil2::insert(ImsDBName::SAMPLE, $params['data']);
        } else {
            //다른 메뉴에서 update하는 컬럼(위치정보json,최근위치)는 update에서 제외
            unset($params['data']['recentLocation'], $params['data']['jsonLocation']);

            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::SAMPLE, $params['data'], new SearchVo('sno=?', $iSno));
        }

        return ['data'=>$iSno,'message'=>'저장 완료'];
    }

    public function setMaterialGrp($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsMaterialService');
        return $imsMaterialService->setMaterialGrp($params);
    }

    //샘플리스트 : 환율 일괄수정
    public function modifySampleRatioMulti($params) {
        $imsMaterialService = SlLoader::cLoad('ims', 'ImsProductSampleService');
        return $imsMaterialService->modifySampleRatioMulti($params);
    }


    //프로젝트의 세부스케쥴 정보수정
    public function modifyProjectScheDetail($params) {
        if (count($params['data']) > 0) {
            $iProjectSno = (int)$params['data'][0]['projectSno'];
            DBUtil2::update(ImsDBName::PROJECT, ['customerDeliveryDt'=>$params['customerDeliveryDt']], new SearchVo('sno=?', $iProjectSno));

            $sCurrDt = date('Y-m-d H:i:s');
            foreach ($params['data'] as $val) {
                $iSno = (int)$val['sno'];
                unset($val['sno']);
                $val['modDt'] = $sCurrDt;
                DBUtil2::update(ImsDBName::PROJECT_SCHE_DETAIL, $val, new SearchVo('sno=?', $iSno));
            }
        }
    }

    //고객 발글 등록(등록은 간소하게. 영업이력(TM, 그외 모두) 등록시 발굴고객의 상세한 정보 update하게 됨)
    public function setFindCustomer($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->setFindCustomer($params);
    }
    //고객 발굴 영업이력 등록. 고객발굴update(Ext테이블 없으면 insert) + 영업이력 insert
    public function setSalesCustomerContents($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->setSalesCustomerContents($params);
    }
    //발굴고객메뉴에서 프로젝트 등록하기(복수고객 등록 가능하게). 고객insert + 프로젝트insert + 프로젝트상세insert + 스타일insert
    public function registProjectBySaleCustomer($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerService');
        return $imsService->registProjectBySaleCustomer($params);
    }

    //영업기획서작성 upsert
    public function setProjectSalesPlanFill($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsProjectNkService');
        return $imsService->setProjectSalesPlanFill($params);
    }

    //제안서가이드 양식 upsert
    public function setBasicProposalGuide($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsFitSpecService');
        return $imsService->setBasicProposalGuide($params);
    }

    //파우치-수령담당자 upsert
    public function setCustomerReceiver($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->setCustomerReceiver($params);
    }
    //파우치-수령자별 분류패킹 등록
    public function registCustomerReceiverDelivery($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->registCustomerReceiverDelivery($params);
    }

    //파우치-수령자별 분류패킹 정보수정(고객관리자)
    public function modifyCustomerReceiverDelivery($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->modifyCustomerReceiverDelivery($params);
    }

    //파우치-고객관리자가 담당자에게 입력요청하기
    public function requestWriteReceiverDelivery($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->requestWriteReceiverDelivery($params);
    }

    //파우치-담당자가 입력수량 변경
    public function modifyCRD($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->modifyCRD($params);
    }

    //파우치-분류패킹 확정
    public function confirmReceiverDelivery($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->confirmReceiverDelivery($params);
    }

    //송장번호, 배송회사 update
    public function modifyDeliveryInfo($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsCustomerReceiverService');
        return $imsService->modifyDeliveryInfo($params);
    }

    //스타일기획 레퍼런스 - 부가정보 upsert
    public function setRefStylePlanAppendInfo($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->setRefStylePlanAppendInfo($params);
    }
    //스타일기획 레퍼런스 - 부가정보 삭제
    public function removeRefStylePlanAppendInfo($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->removeRefStylePlanAppendInfo($params);
    }
    //스타일기획 레퍼런스 - 레퍼런스 upsert
    public function setStylePlanRef($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->setStylePlanRef($params);
    }
    //레퍼런스의 원부자재정보 가져오기(스타일기획upsert에서 레퍼런스 선택시 실행)
    public function getJsonMateListFromRefMateList($params) {
        $imsService = SlLoader::cLoad('ims', 'ImsRefStylePlanService');
        return $imsService->getJsonMateListFromRefMateList($params);
    }


}
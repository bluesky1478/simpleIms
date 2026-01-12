<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Controller\Admin\Ims\ImsPsNkTrait;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

class ImsProjectIssueService {
    use ImsServiceSortNkTrait;
    use ImsPsNkTrait;

    private $imsNkService;
    private $dpData;
    private $dpDataAction;
    public function __construct(){
        $this->imsNkService = SlLoader::cLoad('imsv2', 'ImsNkService');

        $this->dpData = [
            ['type' => 'c', 'col' => 10, 'class' => 'ta-l pdl5', 'name' => 'projectTitle', 'title' => '고객사/프로젝트', ],
            ['type' => 'c', 'col' => 10, 'class' => 'ta-l pdl5', 'name' => 'styleTitle', 'title' => '스타일', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'issueTypeText', 'title' => '유형', ],
            ['type' => 'title', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'issueSubject', 'title' => '이슈 제목', ],
            ['type' => 'c', 'col' => 4, 'class' => '', 'name' => 'issueStHan', 'title' => '상태', ],
//            ['type' => 'c', 'col' => 4, 'class' => '', 'name' => 'isRepeat', 'title' => '반복유무', ],
//            ['type' => 'c', 'col' => 4, 'class' => 'pd0', 'name' => 'isLongUnprocess', 'title' => '장기미처리', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'salesManagerName', 'title' => '영업담당자', ],
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'modDt', 'title' => '마지막 업데이트', ],
        ];

        $this->dpDataAction = [
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'regDt', 'title' => '등록일', ],
            ['type' => 'c', 'col' => 7, 'class' => '', 'name' => 'regManagerName', 'title' => '처리자', ],
            ['type' => 'html', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'actionContents', 'title' => '처리사항', ],
        ];
    }

    public function getDisplay(){
        return $this->dpData;
    }
    public function getDisplayIssueAction(){
        return $this->dpDataAction;
    }

    //프로젝트/스타일 이슈리스트 가져오기 + 등록/수정시 정보 가져오기
    public function getListProjectIssue($params) {
        //첨부파일 관련 - fileDiv값 정의
        $sAppendFileDiv = 'projectIssueFile1';
        if (isset($params['issueSnoGet'])) { //등록/수정인 경우 기본 frame 구성. 등록인 경우 select안하고 기본 frame만 response
            $aoProjectSelects = $aoStyleSelects = [(object)['key'=>0,'text'=>'선택']];
            //upsert기본frame구성. DBNkIms.php 파일에 def 값 제대로 넣어야함(특히 sno, 상태값)
            $aTmpFldList = DBTableField::callTableFunction(ImsDBName::PROJECT_ISSUE);
            $aIssueInfo = [];
            $aSkipUpsertFlds = ['regManagerSno', 'isRepeat', 'isLongUnprocess', 'regDt', 'modDt'];
            foreach ($aTmpFldList as $val) {
                if (!in_array($val['val'], $aSkipUpsertFlds)) {
                    $aIssueInfo[$val['val']] = $val['def'];
                }
            }
            $aIssueInfo['customerSno'] = 0;
            $aIssueInfo['customerName'] = '';
            if ((int)$params['issueSnoGet'] === 0) { //등록인 경우는 여기에서 return
                $aReturn['info'] = $aIssueInfo;
                //첨부파일 관련 - 등록시 초기값 세팅
                $aReturn['info']['fileList'][$sAppendFileDiv] = ['title' => '파일을 첨부해 주세요.', 'memo' => '', 'files' => [], 'noRev' => null];
                $aReturn['info']['bFlagAppendFile'] = false;
                //프로젝트상세에서 이슈등록시 or 스타일리스트에서 이슈등록시 start
                //프로젝트리스트, 스타일리스트 가져와서 response -> vue init 하기전에 obj에 넣기
                if ($params['customerSnoGet'] != 0) {
                    $aTmpProjectSelects = $this->imsNkService->getListProjectSimple(['customerSno'=>$params['customerSnoGet']]);
                    foreach ($aTmpProjectSelects as $val) {
                        array_push($aoProjectSelects, (object)['key'=>$val['sno'],'text'=>$val['projectName']]); //selectbox에 뿌려줄 option
                    }
                }
                if ($params['projectSnoGet'] != 0) {
                    $aTmpStyleSelects = $this->imsNkService->getListStyleSimple(['projectSno'=>$params['projectSnoGet']]);
                    foreach ($aTmpStyleSelects as $val) {
                        array_push($aoStyleSelects, (object)['key'=>$val['sno'],'text'=>$val['productName']]); //selectbox에 뿌려줄 option
                    }
                }
                $aReturn['list_project_selects'] = $aoProjectSelects;
                $aReturn['list_style_selects'] = $aoStyleSelects;
                //프로젝트상세에서 이슈등록시 or 스타일리스트에서 이슈등록시 end

                return $aReturn;
            }
        }

        //리스트 가져오기
        $iIssueSno = (int)$params['issueSnoGet'];
        $searchVo = new SearchVo();
        $iCustomerSno = (int)$params['customerSno'];
        if ($iCustomerSno !== 0) $searchVo->setWhere('b.customerSno = '.$iCustomerSno);
        if ($iIssueSno !== 0) $searchVo->setWhere('a.sno = '.$iIssueSno); //수정인 경우(1개 record만 가져옴)
        else { //리스트 가져오는 경우
            //프로젝트상세페이지에서 open한 경우 하단에 해당 프로젝트/스타일의 이슈리스트 출력 start
            $iListProjectSno = (int)$params['listProjectSno'];
            $iListStyleSno = (int)$params['listStyleSno'];
            if ($iListProjectSno !== 0) {
                $searchVo->setWhere('a.projectSno = '.$iListProjectSno);
                $searchVo->setWhere('a.styleSno = 0');
            }
            if ($iListStyleSno !== 0) $searchVo->setWhere('a.styleSno = '.$iListStyleSno);
            //프로젝트상세페이지에서 open한 경우 하단에 해당 프로젝트/스타일의 이슈리스트 출력 end

            $searchData['condition'] = $params;
            $this->refineCommonCondition($searchData['condition'], $searchVo);
            //table order by 설정
            $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
            unset($searchData['condition']['sort']);
            $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);
        }

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT_ISSUE ], 'field' => ["a.*"]],
            'b' => ['data' => [ ImsDBName::PROJECT, 'LEFT OUTER JOIN', 'a.projectSno = b.sno' ], 'field' => ["projectName, b.customerSno"]],
            'c' => ['data' => [ ImsDBName::PRODUCT, 'LEFT OUTER JOIN', 'a.styleSno = c.sno' ], 'field' => ["productName, substring(prdYear,3,2) as prdYear, prdSeason"]],
            'cust' => ['data' => [ ImsDBName::CUSTOMER, 'LEFT OUTER JOIN', 'b.customerSno = cust.sno' ], 'field' => ["customerName"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
            'sales' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'b.salesManagerSno = sales.sno' ], 'field' => ["if(sales.sno is null, '미선택', sales.managerNm) as salesManagerName"]],
        ];
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        if ($iIssueSno !== 0) { //수정인 경우
            //upsert할때만 쓰이는 필드 정리
            foreach ($aIssueInfo as $key => $val) {
                if (isset($allData['listData'][0][$key])) $aIssueInfo[$key] = $allData['listData'][0][$key];
            }
            //프로젝트리스트, 스타일리스트 가져와서 response -> vue init 하기전에 obj에 넣기
            $aTmpProjectSelects = $this->imsNkService->getListProjectSimple(['customerSno'=>$aIssueInfo['customerSno']]);
            foreach ($aTmpProjectSelects as $val) {
                if ($aIssueInfo['projectSno'] == $val['sno']) $aIssueInfo['projectName'] = $val['projectName']; //상세보기에서 선택한 프로젝트text 뿌려줌
                array_push($aoProjectSelects, (object)['key'=>$val['sno'],'text'=>$val['projectName']]); //selectbox에 뿌려줄 option
            }
            $aTmpStyleSelects = $this->imsNkService->getListStyleSimple(['projectSno'=>$aIssueInfo['projectSno']]);
            foreach ($aTmpStyleSelects as $val) {
                if ($aIssueInfo['styleSno'] == $val['sno']) $aIssueInfo['styleName'] = $val['productName']; //상세보기에서 선택한 스타일text 뿌려줌
                array_push($aoStyleSelects, (object)['key'=>$val['sno'],'text'=>$val['productName']]); //selectbox에 뿌려줄 option
            }
            $aReturn['list_project_selects'] = $aoProjectSelects;
            $aReturn['list_style_selects'] = $aoStyleSelects;

            $aIssueInfo['issueStHan'] = NkCodeMap::PROJECT_ISSUE_ST[$aIssueInfo['issueSt']];
            $aReturn['info'] = $aIssueInfo;
            //첨부파일 관련 - 상세보기/수정시 첨부파일 가져오기
            $aReturn['info']['fileList'][$sAppendFileDiv] = ['title' => '첨부한 파일이 없습니다.', 'memo' => '', 'files' => [], 'noRev' => null];
            $aReturn['info']['bFlagAppendFile'] = false;
            $oFileSearchVo = new SearchVo(['fileDiv=?', 'eachSno=?'], [$sAppendFileDiv, $iIssueSno]);
            $oFileSearchVo->setOrder('a.rev desc');
            $tableInfo=[
                'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["reg.managerNm as regManagerName"]],
            ];
            $aFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oFileSearchVo, false, false, true);
            if (isset($aFileList[0]['sno'])) {
                $aReturn['info']['fileList'][$sAppendFileDiv] = [
                    'title' => 'Rev'.$aFileList[0]['rev'].' '.$aFileList[0]['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $aFileList[0]['regDt']).')',
                    'memo' => str_replace("'",'',$aFileList[0]['memo']),
                    'files' => json_decode(str_replace("'",'',$aFileList[0]['fileList']), true),
                    'sno' => $aFileList[0]['sno']
                ];
                $aReturn['info']['eachSno'] = $iIssueSno;
            }

            return $aReturn;
        } else { //리스트 가져오는 경우
            if (count($allData['listData'] > 0)) {
                //댓글관련 - 이슈글마다 댓글숫자 표기하기 위해 리스트 가져옴
                $aIssueSnos = [];
                foreach ($allData['listData'] as $key => $val) $aIssueSnos[] = (int)$val['sno'];
                $oReplySearchVo = new SearchVo('commentDiv=?', 'projectIssue');
                $oReplySearchVo->setWhere("eachSno in (" . implode(',', $aIssueSnos) . ")");
                $aReplyList = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_COMMENT, $oReplySearchVo);
                $aCntReplyByIssueSno = [];
                foreach ($aReplyList as $val) {
                    if (!isset($aCntReplyByIssueSno[$val['eachSno']])) $aCntReplyByIssueSno[$val['eachSno']] = 0;
                    $aCntReplyByIssueSno[$val['eachSno']]++;
                }
                //리스트 정제
                foreach ($allData['listData'] as $key => $val) {
                    $allData['listData'][$key]['projectTitle'] = $val['projectSno'].' '.$val['customerName'];
                    $allData['listData'][$key]['styleTitle'] = $val['styleSno'] == 0 ? '(프로젝트 이슈)' : $val['prdYear'].' '.$val['prdSeason'].' '.$val['productName'];
                    $allData['listData'][$key]['issueStHan'] = NkCodeMap::PROJECT_ISSUE_ST[$val['issueSt']];
                    $allData['listData'][$key]['cnt_reply'] = $aCntReplyByIssueSno[$val['sno']];
                }
            }
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplay();

            return [
                'pageEx' => $allData['pageData']->getPage('#'),
                'page' => $allData['pageData'],
                'list' => $allData['listData'],
                'fieldData' => $aFldList
            ];
        }
    }

    //프로젝트/스타일 이슈 조치사항리스트 가져오기 + 등록/수정시 정보 가져오기
    public function getListProjectIssueAction($params) {
        //첨부파일 관련 - fileDiv값 정의
        $sAppendFileDiv = 'projectIssueActionFile1';
        if (isset($params['issueActionSnoGet'])) { //등록/수정인 경우 기본 frame 구성. 등록인 경우 select안하고 기본 frame만 response
            //upsert기본frame구성. DBNkIms.php 파일에 def 값 제대로 넣어야함(특히 sno, 상태값)
            $aTmpFldList = DBTableField::callTableFunction(ImsDBName::PROJECT_ISSUE_ACTION);
            $aIssueActionInfo = [];
            $aSkipUpsertFlds = ['regManagerSno', 'regDt', 'modDt'];
            foreach ($aTmpFldList as $val) {
                if (!in_array($val['val'], $aSkipUpsertFlds)) {
                    $aIssueActionInfo[$val['val']] = $val['def'];
                }
            }
            $aIssueActionInfo['regManagerName'] = ''; //table에는 없지만 select쿼리문으로 가져오는 필드값이라 여기에서 빈값으로나마 넣어둔다
            //첨부파일 관련 - 등록/수정시 초기값 세팅
            $aIssueActionInfo['fileList'][$sAppendFileDiv] = ['title' => '파일을 첨부해 주세요.', 'memo' => '', 'files' => [], 'noRev' => null];
            $aIssueActionInfo['bFlagAppendFile'] = false;

            if ((int)$params['issueActionSnoGet'] === 0) { //등록인 경우는 여기에서 return
                $aReturn['info'] = $aIssueActionInfo;

                return $aReturn;
            }
        }

        //리스트 가져오기
        $iIssueActionSno = (int)$params['issueActionSnoGet'];
        $searchVo = new SearchVo();
        $iIssueSno = (int)$params['issueSno'];
        if ($iIssueSno !== 0) $searchVo->setWhere('a.issueSno = '.$iIssueSno);
        if ($iIssueActionSno !== 0) $searchVo->setWhere('a.sno = '.$iIssueActionSno); //수정인 경우(1개 record만 가져옴)
        else { //리스트 가져오는 경우
            $searchData['condition'] = $params;
            $this->refineCommonCondition($searchData['condition'], $searchVo);
            //table order by 설정
            if (isset($searchData['condition']['sort'])) {
                $searchData['condition']['sort_nk'] = $searchData['condition']['sort'];
                unset($searchData['condition']['sort']);
                $this->setListSortNk($searchData['condition']['sort_nk'], $searchVo);
            } else {
                $searchVo->setOrder('a.regDt desc');
            }
        }

        $tableInfo=[
            'a' => ['data' => [ ImsDBName::PROJECT_ISSUE_ACTION ], 'field' => ["a.*"]],
            'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["if(reg.sno is null, '미선택', reg.managerNm) as regManagerName"]],
        ];
        if (!isset($searchData['page']) || (int)$searchData['page'] == 0) $searchData['page'] = gd_isset($searchData['condition']['page'], 1);
        if (!isset($searchData['pageNum']) || (int)$searchData['pageNum'] == 0) $searchData['pageNum'] = gd_isset($searchData['condition']['pageNum'], 15000);
        $allData = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($tableInfo,false), $searchVo, $searchData, false, true);
        if ($iIssueActionSno !== 0) { //수정인 경우
            //upsert할때만 쓰이는 필드 정리
            foreach ($aIssueActionInfo as $key => $val) {
                if (isset($allData['listData'][0][$key])) $aIssueActionInfo[$key] = $allData['listData'][0][$key];
            }
            $aReturn['info'] = $aIssueActionInfo;
            //첨부파일 관련 - 상세보기/수정시 첨부파일 가져오기
            $oFileSearchVo = new SearchVo(['fileDiv=?', 'eachSno=?'], [$sAppendFileDiv, $iIssueActionSno]);
            $oFileSearchVo->setOrder('a.rev desc');
            $tableInfo=[
                'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["reg.managerNm as regManagerName"]],
            ];
            $aFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oFileSearchVo, false, false, true);
            if (isset($aFileList[0]['sno'])) {
                $aReturn['info']['fileList'][$sAppendFileDiv] = [
                    'title' => 'Rev'.$aFileList[0]['rev'].' '.$aFileList[0]['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $aFileList[0]['regDt']).')',
                    'memo' => str_replace("'",'',$aFileList[0]['memo']),
                    'files' => json_decode(str_replace("'",'',$aFileList[0]['fileList']), true),
                    'sno' => $aFileList[0]['sno']
                ];
                $aReturn['info']['eachSno'] = $iIssueActionSno;
            }

            return $aReturn;
        } else { //리스트 가져오는 경우
            if (count($allData['listData'] > 0)) {
                //첨부파일 관련 - 리스트에서 첨부파일정보를 넣어주기 위해 select -> 게시물마다 첨부파일정보 넣어줌 -> frontend에서 이미지파일을 리스트마다 뿌려줌
                $aIssueActionSnos = $aFileInfoByIssueActionSno = [];
                foreach ($allData['listData'] as $key => $val) $aIssueActionSnos[] = (int)$val['sno'];
                $oFileSearchVo = new SearchVo(['fileDiv=?'], [$sAppendFileDiv]);
                $oFileSearchVo->setWhere("a.eachSno in (" . implode(',', $aIssueActionSnos) . ")");
                $oFileSearchVo->setOrder('a.rev desc');
                $tableInfo=[
                    'a' => ['data' => [ ImsDBName::PROJECT_FILE ], 'field' => ["a.*"]],
                    'reg' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = reg.sno' ], 'field' => ["reg.managerNm as regManagerName"]],
                ];
                $aFileList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oFileSearchVo, false, false, true);
                foreach ($aFileList as $val) $aFileInfoByIssueActionSno[$val['eachSno']] = $val;

                //리스트 정제
                foreach ($allData['listData'] as $key => $val) {
                    //첨부파일 관련 - 게시물마다 첨부파일정보 넣어줌 -> frontend에서 이미지파일을 리스트마다 뿌려줌
                    $allData['listData'][$key]['fileList'][$sAppendFileDiv] = ['title' => '파일을 첨부해 주세요.', 'memo' => '', 'files' => [], 'noRev' => null];
                    if (isset($aFileInfoByIssueActionSno[$val['sno']])) {
                        $allData['listData'][$key]['fileList'][$sAppendFileDiv] = [
                            'title' => 'Rev'.$aFileInfoByIssueActionSno[$val['sno']]['rev'].' '.$aFileInfoByIssueActionSno[$val['sno']]['regManagerName'].'등록'.'('.gd_date_format('y/m/d', $aFileInfoByIssueActionSno[$val['sno']]['regDt']).')',
                            'memo' => str_replace("'",'',$aFileInfoByIssueActionSno[$val['sno']]['memo']),
                            'files' => json_decode(str_replace("'",'',$aFileInfoByIssueActionSno[$val['sno']]['fileList']), true),
                            'sno' => $aFileInfoByIssueActionSno[$val['sno']]['sno']
                        ];
                    }

                }
            }
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayIssueAction();

            return [
                'pageEx' => $allData['pageData']->getPage('#'),
                'page' => $allData['pageData'],
                'list' => $allData['listData'],
                'fieldData' => $aFldList
            ];
        }
    }

    //프로젝트/스타일 이슈 upsert
    public function setProjectIssue($params) {
        //첨부파일 관련 - fileDiv값 정의
        $sAppendFileDiv = 'projectIssueFile1';
        //setProjectIssue() 함수를 실행하는 부분에서 response를 무조건 code:200 으로 보내줘서 이 함수안에서 code, message 변경하고 return해봤자 소용없음
        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno']);
        $sCurrDt = date('Y-m-d H:i:s');
        $iRegManagerSno = \Session::get('manager.sno');
        if ($iSno === 0) {
            $params['data']['regManagerSno'] = $iRegManagerSno;
            $params['data']['regDt'] = $sCurrDt;
            $iSno = DBUtil2::insert(ImsDBName::PROJECT_ISSUE, $params['data']);
        } else {
            //컬럼 한글명 가져오기 위해 불러옴
            $aTmpFldList = DBTableField::callTableFunction(ImsDBName::PROJECT_ISSUE);
            $aFldNameHan = [];
            foreach ($aTmpFldList as $val) $aFldNameHan[$val['val']] = $val['name'];
            //수정이력 insert
            $aInsertUpdateLog = [];
            $aExistInfo = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_ISSUE, new SearchVo('sno=?', $iSno))[0];
            foreach ($aExistInfo as $key => $val) {
                if (isset($params['data'][$key]) && $val != $params['data'][$key]) {
                    $aInsertUpdateLog[] = [
                        'regManagerSno'=>$iRegManagerSno, 'tableType'=>1, 'eachSno'=>$iSno, 'fldName'=>$key,
                        'fldNameHan'=>$aFldNameHan[$key], 'beforeValue'=>$val, 'afterValue'=>$params['data'][$key], 'regDt'=>$sCurrDt
                    ];
                }
            }
            //첨부파일 변경했을 때 수정이력 남기기
            if ($params['data']['bFlagAppendFile'] == 'true' && isset($params['data']['fileList'][$sAppendFileDiv]['files']) && count($params['data']['fileList'][$sAppendFileDiv]['files']) > 0) {
                $aInsertUpdateLog[] = [
                    'regManagerSno'=>$iRegManagerSno, 'tableType'=>1, 'eachSno'=>$iSno, 'fldName'=>'change_append_file',
                    'fldNameHan'=>'내용의 첨부파일 변경', 'beforeValue'=>'첨부파일 이력 참고', 'afterValue'=>'첨부파일 이력 참고', 'regDt'=>$sCurrDt
                ];
            }
            if (count($aInsertUpdateLog) > 0) {
                foreach ($aInsertUpdateLog as $val) {
                    DBUtil2::insert(ImsDBName::UPDATE_HISTORY_NK, $val);
                }

                $params['data']['modDt'] = $sCurrDt;
                DBUtil2::update(ImsDBName::PROJECT_ISSUE, $params['data'], new SearchVo('sno=?', $iSno));
            }
        }

        //첨부파일 관련 - 등록/수정시 첨부파일 insert (bFlagAppendFile로 첨부파일 올렸는지 확인한다)
        if ($params['data']['bFlagAppendFile'] == 'true' && isset($params['data']['fileList'][$sAppendFileDiv]['files']) && count($params['data']['fileList'][$sAppendFileDiv]['files']) > 0) {
            $oImsService = SlLoader::cLoad('ims', 'imsService');
            $aSaveData = [
                'customerSno'=>$params['data']['customerSno'], 'projectSno'=>$params['data']['projectSno'], 'styleSno'=>$params['data']['styleSno'], 'eachSno'=>$iSno,
                'fileDiv'=>$sAppendFileDiv, 'fileList'=>$params['data']['fileList'][$sAppendFileDiv]['files'], 'memo'=>$params['data']['fileList'][$sAppendFileDiv]['memo'],
            ];
            $oImsService->saveProjectFiles($aSaveData);
        }

        return ['data'=>$iSno,'message'=>'저장 완료'];
    }

    //프로젝트/스타일 이슈 조치사항 upsert
    public function setProjectIssueAction($params) {
        $aReturn = ['data'=>0,'message'=>'조회 완료'];

        $iSno = (int)$params['data']['sno'];
        unset($params['data']['sno'], $params['data']['regDt']);
        $sCurrDt = date('Y-m-d H:i:s');
        $iRegManagerSno = \Session::get('manager.sno');
        if ($iSno === 0) {
            $params['data']['regManagerSno'] = $iRegManagerSno;
            $params['data']['regDt'] = $sCurrDt;
            $iSno = DBUtil2::insert(ImsDBName::PROJECT_ISSUE_ACTION, $params['data']);

            //조치내용 등록시 이슈상태 자동변경
            $oIssueSearchVo = new SearchVo('sno=?', $params['data']['issueSno']);
            $aIssueInfo = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_ISSUE, $oIssueSearchVo)[0];
            $iPrevIssueSt = (int)$aIssueInfo['issueSt'];
            $iChgIssueSt = $params['data']['chkDirectComplete'] == true ? 3 : 2;
            if ($iPrevIssueSt != $iChgIssueSt) {
                DBUtil2::update(ImsDBName::PROJECT_ISSUE, ['issueSt'=>$iChgIssueSt], $oIssueSearchVo);
                $aInsertUpdateLog = [
                    'regManagerSno'=>$iRegManagerSno, 'tableType'=>1, 'eachSno'=>$params['data']['issueSno'], 'fldName'=>'issueSt',
                    'fldNameHan'=>'상태(조치등록)', 'beforeValue'=>$iPrevIssueSt, 'afterValue'=>$iChgIssueSt, 'regDt'=>$sCurrDt
                ];
                DBUtil2::insert(ImsDBName::UPDATE_HISTORY_NK, $aInsertUpdateLog);
                $aReturn['data'] = $iChgIssueSt;
            }
        } else {
            $params['data']['modDt'] = $sCurrDt;
            DBUtil2::update(ImsDBName::PROJECT_ISSUE_ACTION, $params['data'], new SearchVo('sno=?', $iSno));
        }

        //첨부파일 관련 - fileDiv값 정의
        $sAppendFileDiv = 'projectIssueActionFile1';

        //첨부파일 관련 - 등록/수정시 첨부파일 insert (bFlagAppendFile로 첨부파일 올렸는지 확인한다)
        if ($params['data']['bFlagAppendFile'] == 'true' && isset($params['data']['fileList'][$sAppendFileDiv]['files']) && count($params['data']['fileList'][$sAppendFileDiv]['files']) > 0) {
            $oImsService = SlLoader::cLoad('ims', 'imsService');
            $aSaveData = [
                'customerSno'=>0, 'projectSno'=>0, 'styleSno'=>0, 'eachSno'=>$iSno,
                'fileDiv'=>$sAppendFileDiv, 'fileList'=>$params['data']['fileList'][$sAppendFileDiv]['files'], 'memo'=>$params['data']['fileList'][$sAppendFileDiv]['memo'],
            ];
            $oImsService->saveProjectFiles($aSaveData);
        }

        return $aReturn;
    }



}
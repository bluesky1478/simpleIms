<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS TO-DO 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceTodoTrait {

    public function getApprovalData($params){
        //'approvalType', projectSno
        $approvalData = [];

        $searchCondition = $params;
        unset($searchCondition['mode']);
        unset($searchCondition['target']);
        $searchVo = new SearchVo();
        $searchVo->setOrder('regDt desc');
        $searchVo->setWhere( "delFl='n'" );

        foreach($searchCondition as $key => $each){
            if(!empty($each)){
                $searchVo->setWhere( "{$key}=?" );
                $searchVo->setWhereValue($each);
            }
        }

        $approvalRequestData = DBUtil2::getOneBySearchVo(ImsDBName::TODO_REQUEST, $searchVo);
        if( !empty($approvalRequestData) ){
            $approvalData = $this->getTodoRequest([
                'sno' => $approvalRequestData['sno']
            ]);
        }

        return $approvalData;
    }

    /**
     * 나의 요청 상세
     * @param $params
     * @return array|mixed
     */
    public function getTodoRequest($params){
        if(empty($params['sno'])){
            $data = DBTableField::getTableBlankData('tableImsTodoRequest'); //초기 데이터.
            //초기 결재 타입
            if( !empty($params['approvalType']) ){
                $data['approvalType'] = $params['approvalType'];
                $data['regManagerSno'] = SlCommonUtil::getManagerSno();
                $data['regManagerNm'] = SlCommonUtil::getManagerInfo(SlCommonUtil::getManagerSno())['managerNm'];
                //$data['subject'] = ImsApprovalService::APPROVAL_TYPE[$params['approvalType']]['name'].' 결재 요청';
            }

            $data['projectSno'] = '';
            $data['projectNo'] = '';
            $data['projectYear'] = '';
            $data['projectSeason'] = '';
            $data['customerName'] = '';
        }else{
            //SitelabLogger::logger2(__METHOD__, 'getTodoRequest 가져오기 인자값 체크');
            //SitelabLogger::logger2(__METHOD__, $params);
            $condition = [
                'sno' => $params['sno'],
            ];

            $dataList = $this->getListTodoRequest([
                'condition' => $condition
            ])['list']; //TargetList와 Join된 데이터.. ( response와 조인됨 )

            //SitelabLogger::logger2(__METHOD__,'컨디션체크');
            //SitelabLogger::logger2(__METHOD__,$condition);
            //SitelabLogger::logger2(__METHOD__,$dataList);

            //요청 번호가 없다면 첫번째 데이터로 셋팅
            if( empty($params['resSno']) ){
                $data=$dataList[0];
                $data['targetManagerSno'] = 0;
            }

            //받은사람 정보 하나로 만들기.
            $data['myAccept'] = false; //결재자할 사람이 나와 동일한 경우 체크 (승인 버튼 활성화)

            foreach( $dataList as $dataEachKey => $dataEach ){

                if( 'target' !== $dataEach['targetType'] ) continue;

                if( $dataEach['resSno'] == $params['resSno'] ){
                    $data=$dataEach;
                }

                if( ( 'accept' === $dataEach['status'] || 'reject' === $dataEach['status']) && $dataEach['targetManagerSno'] === SlCommonUtil::getManagerSno() ){
                    $data['myCancel'] = true; //결재 취소 가능 여부
                    $data['myAcceptInfo'] = [
                        'responseSno' => $dataEach['resSno'],
                        'managerSno' => $dataEach['targetManagerSno'],
                        'isLast' => $dataEachKey == count($dataList)-1 ,
                    ];
                    $data['myApprovalStatus'] = $dataEach['status'];
                }

                if( 'accept' !== $dataEach['status'] && $dataEach['targetManagerSno'] === SlCommonUtil::getManagerSno() ){
                    $data['myAccept'] = true;
                    $data['myAcceptInfo'] = [
                        'responseSno' => $dataEach['resSno'],
                        'managerSno' => $dataEach['targetManagerSno'],
                        'isLast' => $dataEachKey == count($dataList)-1 ,
                    ];
                    $data['myApprovalStatus'] = $dataEach['status'];
                }

                $appTitle = '';
                $managerInfo = SlCommonUtil::getManagerInfo($dataEach['targetManagerSno']);

                foreach(ImsApprovalService::APP_LINE_TITLE_CHK as $appLineTitleKey => $appLineTitleList){
                    foreach($appLineTitleList as $appLineCode){
                        if( $appLineCode == $managerInfo[$appLineTitleKey] ){
                            $appTitle = gd_code(substr($appLineCode,0,5))[$appLineCode];
                            break;
                        }
                    }
                    if(!empty($appTitle)) break;
                }

                if(empty($appTitle)) $appTitle = gd_code('02001')[$managerInfo['departmentCd']];


                $statusKr='';
                if('todo' === $dataEach['todoType']){
                    if('ready' == $dataEach['status']) $statusKr = '요청';
                    if('complete' == $dataEach['status']) $statusKr = '완료';
                }
                if(empty($statusKr)){
                    $statusKr = ImsApprovalService::APPROVAL_STATUS[$dataEach['status']];
                }

                $data['targetManagerList'][] = [
                    'sno' => $dataEach['targetManagerSno'],
                    'name' => $dataEach['dpTargetName'],
                    'status' => $dataEach['status'],
                    'statusKr' => $statusKr,
                    'expectedDt' => $dataEach['expectedDt'],
                    'completeDt' => $dataEach['completeDt'],
                    'myAccept' => $dataEach['targetManagerSno'] === SlCommonUtil::getManagerSno(),
                    'appTitle' => $appTitle,
                    'resSno' => $dataEach['resSno'],
                    'reason' => $dataEach['reason'],
                ];
            }

            $data['contentsNl2br'] = nl2br($data['contents']);

        }
        return $data;
    }

    /**
     * 조건 추가
     * @param $condition
     * @param $searchVo
     */
    public function setTodoCondition($condition, $searchVo){

        //결재상태
        if( 'all' !== $condition['approvalStatus'] && !empty($condition['approvalStatus']) ) {
            $searchVo->setWhere('a.approvalStatus=?');
            $searchVo->setWhereValue($condition['approvalStatus']);
        }

        //결재자
        if( !empty($condition['approvalManagerSno']) ) {
            //
            $searchVo->setWhere('b.targetType=\'target\'');
            $searchVo->setWhere(
                "( a.regManagerSno=? OR ( a.approvalStatus <> 'ready' and b.managerSno=? ) )"
            );
            $searchVo->setWhereValue($condition['approvalManagerSno']);
            $searchVo->setWhereValue($condition['approvalManagerSno']);
        }

        //요청자
        if( !empty($condition['managerSno']) ) {
            $searchVo->setWhere('b.managerSno=?');
            $searchVo->setWhereValue($condition['managerSno']);
        }

        //팀, 받는사람.
        if( !empty($condition['teamSno']) || !empty($condition['respManagerSno']) ) {
            $managerSearch=[];
            if( !empty($condition['teamSno']) ) {
                $managerSearch[] = 'code.itemCd=?';
                $searchVo->setWhereValue($condition['teamSno']);
            }
            if( !empty($condition['respManagerSno']) ) {
                $managerSearch[] = 'b.managerSno=?';
                $searchVo->setWhereValue($condition['respManagerSno']);
            }
            $searchVo->setWhere('('.implode(' OR ', $managerSearch).')');
        }

        //요청자
        if( !empty($condition['reqManagerSno']) ) {
            $searchVo->setWhere('a.regManagerSno=?');
            $searchVo->setWhereValue($condition['reqManagerSno']);
        }
        //상태
        if( !empty($condition['status']) ) {
            $searchVo->setWhere('b.status=?');
            $searchVo->setWhereValue($condition['status']);
        }
        //결재선 타입별 조회
       if( !empty($condition['approvalType']) ) {
            $searchVo->setWhere("(a.approvalType='all' OR a.approvalType=?)");
            $searchVo->setWhereValue($condition['approvalType']);
        }
       //todoType
       if( !empty($condition['todoType']) ) {
            $searchVo->setWhere('a.todoType=?');
            $searchVo->setWhereValue($condition['todoType']);
        }
        //결재라인 등록자
        if( !empty($condition['approvalLineReqManagerSno']) ) {
            $searchVo->setWhere('(a.regManagerSno=0 or a.regManagerSno=?)');
            $searchVo->setWhereValue($condition['approvalLineReqManagerSno']);
        }

        //프로젝트 번호
        if( !empty($condition['projectSno']) ) {
            $searchVo->setWhere('prj.sno=?');
            $searchVo->setWhereValue($condition['projectSno']);
        }

    }

    /**
     * 나의 요청 리스트 
     * @param $params
     * @return array
     */
    public function getListTodoRequest($params){
        $searchVo = new SearchVo();
        $totalSearchVo = new SearchVo ();

        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setCommonCondition($params['condition'], $totalSearchVo);

        $this->setTodoCondition($params['condition'], $searchVo);
        $this->setTodoCondition($params['condition'], $totalSearchVo);

        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];
        $allData = DBUtil2::getComplexListWithPaging($this->sql->getTodoRequestListTable(), $searchVo, $searchData);
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationTodoRequest');

        //Rowspan 설정
        SlCommonUtil::setListRowSpan($list, [
            'reqSno'  => ['valueKey' => 'sno'],
        ], $params);

        $pageEx = $allData['pageData']->getPage('#');

        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list
        ];
    }

    /**
     * 받은요청 리스트
     * @param $params
     * @return array
     */
    public function getListTodoResponse($params){
        return $this->getListTodoRequest($params);
    }

    /**
     * 결재 리스트
     * @param $params
     * @return array
     */
    public function getListTodoApproval($params){
        $searchVo = new SearchVo();
        $totalSearchVo = new SearchVo();

        //검색 기본 삭제 제거.
        if( empty($params['condition']['delFl']) ){
            $searchVo->setWhere("a.delFl='n'");
            $totalSearchVo->setWhere("a.delFl='n'");
        }else{
            $searchVo->setWhere('a.delFl=?');
            $searchVo->setWhereValue($params['condition']['delFl']);
            $totalSearchVo->setWhere('a.delFl=?');
            $totalSearchVo->setWhereValue($params['condition']['delFl']);
        }

        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setCommonCondition($params['condition'], $totalSearchVo);
        $this->setTodoCondition($params['condition'], $searchVo);
        $this->setTodoCondition($params['condition'], $totalSearchVo);

        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];

        $searchVo->setDistinct();

        if(empty($searchVo->getOrder())){
            $searchVo->setOrder('a.regDt desc');
        }

        $allData = DBUtil2::getComplexListWithPaging($this->sql->getApprovalListTable(), $searchVo, $searchData);
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationTodoApprovalList');

        //SitelabLogger::logger2(__METHOD__, $list);

        SlCommonUtil::setListRowSpan($list, [
            'projectSno'  => ['valueKey' => 'projectSno'],
        ], $params);

        $pageEx = $allData['pageData']->getPage('#');

        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list
        ];
    }

    /**
     * 결재 리스트 추가 정보
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return mixed
     * @throws \Exception
     */
    public function decorationTodoApprovalList($each, $key=null, $mixData=null){
        $each = DBTableField::parseJsonField(ImsDBName::TODO_REQUEST, $each);
        $each = DBTableField::fieldStrip(ImsDBName::TODO_REQUEST, $each);

        //결재상태
        $each['approvalStatusKr'] = ImsApprovalService::APPROVAL_STATUS[$each['approvalStatus']];
        $each['approvalTypeKr'] = ImsApprovalService::APPROVAL_TYPE[$each['approvalType']]['name'];

        $appManagerNameArray = [];
        foreach($each['appManagers'] as $appManager){
            $appManagerNameArray[] = $appManager['name'];
        }

        $each['appManagersStr'] = implode(' > ', $appManagerNameArray);

        $reqSno = !empty($each['reqSno'])?$each['reqSno'] : $each['sno'];
        $each['commentCnt'] = DBUtil2::getCount(ImsDBName::TODO_COMMENT, new SearchVo('todoSno=?', $reqSno));

        return SlCommonUtil::setDateBlank($this->decorationTodoCommon($each,$key,$mixData));
    }


    /**
     * 결재라인 리스트
     * @param $params
     * @return array
     */
    public function getListApprovalLine($params){

        $params['condition']['approvalLineReqManagerSno'] = SlCommonUtil::getManagerSno();

        $searchVo = new SearchVo();
        $totalSearchVo = new SearchVo ();
        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setCommonCondition($params['condition'], $totalSearchVo);
        $this->setTodoCondition($params['condition'], $searchVo);
        $this->setTodoCondition($params['condition'], $totalSearchVo);

        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];

        $allData = DBUtil2::getComplexListWithPaging($this->sql->getApprovalLineListTable(), $searchVo, $searchData);
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationApprovalList');

        $pageEx = $allData['pageData']->getPage('#');

        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list
        ];
    }


    /**
     * To-do List 공통꾸미기
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return mixed
     */
    public function decorationTodoCommon($each, $key=null, $mixData=null){
        //상태
        $each['statusKr'] = TODO_STATUS::getName($each['status']);
        //프로젝트가 연결되었을 경우 추가 작업
        if(!empty($each['projectSno'])){
            $this->setProjectIcon($each, $each['projectSno']);
            $each['projectTypeEn'] = ImsCodeMap::PROJECT_TYPE_EN[$each['projectType']];
            $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];
            $searchData = $each;
            $searchData['sno'] = $each['projectSno'];
            $projectData = $this->decorationProject($searchData);
            $each['styleName'] = $projectData['styleName'];
        }

        $each['regManagerNm'] = -1 == $each['regManagerSno'] ? '고객요청':empty($each['regManagerNm'])?'시스템':$each['regManagerNm'];

        //참조자 (현재는 함께 요청 받은 자) , + 팀
        $each['refManagerList'] = DBUtil2::runSelect("select a.managerSno, b.managerNm, a.status from sl_imsTodoResponse a left outer join es_manager b on a.managerSno = b.sno where a.reqSno = {$each['sno']} and a.managerSno <> {$each['targetManagerSno']}  ");
        foreach( $each['refManagerList'] as  $refManagerKey => $refManager ){
            $refManager['rf'] = $refManager['managerSno'];
            $refManager['statusKr'] = TODO_STATUS::getName($refManager['status']);
            $teamName = ImsCodeMap::CODE_DEPT_INFO['0'.$refManager['managerSno']]['name'];
            if( !empty($teamName) ){
                $refManager['managerNm'] = $teamName;
            }
            $each['refManagerList'][$refManagerKey] = $refManager;
        }

        return $each;
    }

    /**
     * 나의 요청 리스트 꾸미기
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return array|mixed
     * @throws \Exception
     */
    public function decorationTodoRequest($each, $key=null, $mixData=null){
        $each = DBTableField::parseJsonField(ImsDBName::TODO_REQUEST, $each);
        $each = DBTableField::fieldStrip(ImsDBName::TODO_REQUEST, $each);

        $reqSno = !empty($each['reqSno'])?$each['reqSno'] : $each['sno'];
        $each['commentCnt'] = DBUtil2::getCount(ImsDBName::TODO_COMMENT, new SearchVo('todoSno=?', $reqSno));

        if(empty($each['targetManagerNm'])){
            //팀별 요청.
            $each['dpTargetName'] = $each['teamNm'];
            $each['dpTargetTeamSno'] = $each['targetManagerSno'];
        }else{
            //개별 요청.
            $each['dpTargetName'] = $each['targetManagerNm'];
            $each['dpTargetTeamSno'] = $each['targetTeamSno'];
        }

        //TODO_STATUS::
        return SlCommonUtil::setDateBlank($this->decorationTodoCommon($each,$key,$mixData));
    }

    /**
     * 결재선 지정
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return array|mixed
     * @throws \Exception
     */
    public function decorationApprovalList($each, $key=null, $mixData=null){
        $each = DBTableField::parseJsonField(ImsDBName::APPROVAL_LINE, $each);
        $each['appManagers'] = SlCommonUtil::setEachData($each['appManagers'], $this, 'decorationApprovalManagers');
        $each['refManagers'] = SlCommonUtil::setEachData($each['refManagers'], $this, 'decorationApprovalManagers');
        return SlCommonUtil::setDateBlank($this->decorationTodoCommon($each,$key,$mixData));
    }
    public function decorationApprovalManagers($each, $key=null, $mixData=null){
        $approvalManagerData = ImsJsonSchema::APPROVAL_MANAGER_DATA;
        $approvalManagerData['sno'] = $each['sno'];
        $approvalManagerData['name'] = $each['name'];
        return $approvalManagerData;
    }

    /**
     * 받은 요청 리스트 꾸미기
     * @param $each
     * @param null $key
     * @param null $mixData
     * @return array|mixed
     * @throws \Exception
     */
    public function decorationTodoResponse($each, $key=null, $mixData=null){
        $each = DBTableField::parseJsonField(ImsDBName::TODO_REQUEST, $each);
        $each = DBTableField::fieldStrip(ImsDBName::TODO_REQUEST, $each);
        //TODO_STATUS::
        return SlCommonUtil::setDateBlank($this->decorationTodoCommon($each,$key,$mixData));
    }


    /**
     * 결재 등록
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveApproval($params){
        $sno = $params['document']['sno'];

        //파일 처리. (인코드 해서 저장)
        $params['document']['todoFile1'] = json_encode($params['document']['todoFile1']);

        if( empty($sno) ) {
            //필수 체크.
            DBTableField::checkRequired(ImsDBName::TODO_REQUEST, $params['document']);
            
            //결재자 체크.
            if( empty($params['document']['appManagers']) ){
                throw new \Exception('결재자가 없습니다!');
            }

            $appManagers = $params['document']['appManagers'];
            $refManagers = $params['document']['refManagers'];

            $params['document']['appManagers'] = json_encode($params['document']['appManagers']);
            $params['document']['refManagers'] = json_encode($params['document']['refManagers']);

            //신규 결재 등록
            $params['document']['approvalStatus'] = 'proc';
            $sno = $this->save(ImsDBName::TODO_REQUEST, $params['document']);

            //결재자들에게 TO-DO 등록
            foreach( $appManagers as $key => $appManager ){
                $respSaveData = [
                    'reqSno' => $sno,
                    'managerSno' => $appManager['sno'],
                    'targetType' => 'target',
                    'status' => 'proc',
                ];
                //SitelabLogger::logger2(__METHOD__, '결재자들에게 TO-DO 등록');
                //SitelabLogger::logger2(__METHOD__, $respSaveData);
                $this->save(ImsDBName::TODO_RESPONSE, $respSaveData);
            }

            //참조자 TO_DO 참조 타입 등록
            foreach( $refManagers as $refManager ){
                $this->save(ImsDBName::TODO_RESPONSE, [
                    'reqSno' => $sno,
                    'managerSno' => $refManager['sno'],
                    'targetType' => 'ref',
                ]);
            }
            $this->sendApprovalMsg($sno, 'proc',' 결재 바랍니다.');

            $approvalData = $this->getTodoRequest(['sno'=>$sno]);

            //$imsApprovalService = SlLoader::cLoad('ims', 'imsApprovalService');
            //$fncName = 'proc'.ucfirst($params['document']['approvalType']);
            //$imsApprovalService->$fncName($approvalData);
            $this->runApprovalMethod('proc', $params['document']['approvalType'], $approvalData);

        }else{
            //수정 ( 결재요청 전일 때 가능)
            //unset($params['document']['appManager']);
            //unset($params['document']['refManager']);
            $sno = $this->save(ImsDBName::TODO_REQUEST, $params['document']);
        }

        if(!empty($params['document']['projectSno'])){
            ImsScheduleUtil::setProjectScheduleStatus($params['document']['projectSno']);
        }

        return $sno;
    }


    /**
     * 자체 결재
     * @param $params
     * @throws \Exception
     */
    public function saveApprovalSelf($params){
        $managerInfo = SlCommonUtil::getManagerInfo();
        $params['document']['appManagers'] = [
            [
                'sno' => $managerInfo['sno'],
                'name' => $managerInfo['managerNm'],
            ]
        ];
        $reqSno = $this->saveApproval($params); //우선 저장 시킨다.
        $this->setApprovalStatus([
            'sno' => $reqSno,
            'approvalStatus' => 'accept'
        ]);
    }

    /**
     * 저장.
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveTodo($params){
        $sno = $params['document']['sno'];

        //파일 처리. (인코드 해서 저장)
        $params['document']['todoFile1'] = json_encode($params['document']['todoFile1']);

        if( empty($sno) ) {
            //필수 체크.
            DBTableField::checkRequired(ImsDBName::TODO_REQUEST, $params['document']);
            //요청대상자 체크.
            if( empty($params['reqManagers']) ){
                throw new \Exception('요청 대상자가 없습니다!');
            }

            $projectData = null;
            if( !empty($params['document']['projectSno']) ){
                $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['document']['projectSno']);
                $params['document']['customerSno'] = $projectData['customerSno'];
            }

            //신규 등록
            $sno = $this->save(ImsDBName::TODO_REQUEST, $params['document']);

            //요청 대상자에 등록
            foreach( $params['reqManagers'] as $reqManager ){
                $saveData = [
                    'reqSno' => $sno,
                    'managerSno' => (int)$reqManager,
                    'targetType' => 'target',
                ];
                $saveData['expectedDt'] = $params['document']['hopeDt'];//희망일 = 예정일 (기본 등록)
                $resRslt = $this->save(ImsDBName::TODO_RESPONSE, $saveData);
            }

            $title = "[{$params['document']['subject']}] 요청이 등록 됨";

            $contents = $params['document']['projectYear'].$params['document']['projectSeason'].' '.$params['document']['customerName'].'<br>';
            $contents .= nl2br($params['document']['contents']);

            $this->sendTodoMailAndKakao($sno, $title, $contents,50198); //메일/카카오 발송

        }else{
            //수정
            $sno = $this->save(ImsDBName::TODO_REQUEST, $params['document']);
        }
        return $sno;
    }


    /**
     * 완료 예정일 저장
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveTodoExpectedDt($params){
        /*if( empty($params['expectedDt']) ){
            throw new \Exception('예정일은 필수입니다!');
        }else{*/
            if( !empty($params['sno']) ){
                $params['snoList'][] = $params['sno'];
            }
            foreach($params['snoList'] as $resSno){
                $this->save(ImsDBName::TODO_RESPONSE, [
                    'sno' => $resSno,
                    'expectedDt' => $params['expectedDt'],
                ]);
            }
        //}
    }

    /**
     * To-do 상태변경
     * @param $params
     */
    public function setTodoStatus($params){
        foreach($params['snoList'] as $resSno){

            if( 'complete' === $params['status'] ){
                $completeDt = 'now()';

                $resData = DBUtil2::getOne(ImsDBName::TODO_RESPONSE,'sno', $resSno); //받은 정보
                $reqData = DBUtil2::getOne(ImsDBName::TODO_REQUEST,'sno', $resData['reqSno']); //요청 정보

                $regManagerInfo = SlCommonUtil::getManagerInfo($reqData['regManagerSno']);
                $managerInfo = SlCommonUtil::getManagerInfo(\Session::get('manager.sno'));

                $title = "{$managerInfo['managerNm']}님이 [ {$reqData['subject']} ] 건을 완료했습니다.";
                $email = $regManagerInfo['email'];

                $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $reqData['projectSno']);
                if(!empty($projectData)){
                    $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER,'sno',$projectData['customerSno']);
                    $contents = $projectData['projectYear'].$projectData['projectSeason'].' '.$customerData['customerName'] .' 관련 <br>';
                    $contents .=  date('Y-m-d H:i:s') . '에 완료';
                }else{
                    $contents =  date('Y-m-d H:i:s') . ' 완료';
                }

                SiteLabMailUtil::sendSystemMail($title, $contents, $email);

                /*SitelabLogger::logger2(__METHOD__, $params);
                SitelabLogger::logger2(__METHOD__, $todoData);
                SitelabLogger::logger2(__METHOD__, $regManagerInfo);*/

                /*SitelabLogger::logger2(__METHOD__, '=======================');
                SitelabLogger::logger2(__METHOD__, $title);
                SitelabLogger::logger2(__METHOD__, $email);
                SitelabLogger::logger2(__METHOD__, $contents);*/

                $completeManagerSno = \Session::get('manager.sno');

            }else{
                $completeDt = '';
                $completeManagerSno = '';
            }

            $this->save(ImsDBName::TODO_RESPONSE, [
                'sno' => $resSno,
                'status' => $params['status'],
                'completeDt' => $completeDt,
                'completeManagerSno' => $completeManagerSno,
            ]);
        }
    }

    /**
     * 카카오 및 메일 발송
     * @param $todoSno
     * @param $title
     * @param $content
     * @throws \Exception
     */
    public function sendTodoMailAndKakao($todoSno, $title, $content, $templateId=''){

        $requestInfo = DBUtil2::getList(ImsDBName::TODO_REQUEST,'sno',$todoSno);

        //if( !SlCommonUtil::isDev() ){
        $subject = "(IMS) ". $title;
        $contents = $title."<br><br>{$content}<br>";

        //작성자 정보
        $managerInfo = SlCommonUtil::getManagerInfo(SlCommonUtil::getManagerSno());
        $contents .= "<br> - {$managerInfo['managerNm']} 작성<br>";

        //보내기
        $targetList = DBUtil2::getList(ImsDBName::TODO_RESPONSE,'reqSno',$todoSno);
        $targetEmail = [];
        $targetPhone = [];
        foreach($targetList as $target){
            if(SlCommonUtil::getManagerSno() != $target['managerSno']){
                //200만 이상은 부서별
                if( 2000000 > (int)$target['managerSno'] ){
                    $targetManagerInfo = DBUtil2::getOne(DB_MANAGER, 'sno' , $target['managerSno']);
                    //이메일 리스트
                    if( !empty($targetManagerInfo['email']) ){
                        $targetEmail[] = $targetManagerInfo['email'];
                    }
                    //휴대폰 리스트
                    if( !empty($targetManagerInfo['cellPhone']) ){
                        $targetPhone[] = [
                            'managerInfo' => $targetManagerInfo,
                            'phone' => $targetManagerInfo['cellPhone'],
                        ];
                    }
                }else{
                    //팀별 요청.
                    $teamInfo = ImsCodeMap::CODE_DEPT_INFO[$target['managerSno']];
                    $teamEmail = SlCommonUtil::isDev() ? 'innover_dev@msinnover.com' : $teamInfo['email'];
                    $teamPhone = $teamInfo['phone'];
                    if( !empty($teamEmail) ){
                        $targetEmail[] = $teamEmail;
                    }
                    if( !empty($teamPhone) ){
                        $targetPhone[] = [
                            'managerInfo' => ['managerNm'=>$teamInfo['managerNm']],
                            'phone' => $teamPhone,
                        ];
                    }
                }
            }
        }

        //이메일 발송
        $to = implode(',',$targetEmail);
        if(!empty($to)){
            SiteLabMailUtil::sendSystemMail($subject, $contents, $to);
        }
        
        //카카오 발송
        /*foreach($targetPhone as $target){
            $param['phone'] = $target['phone'];
            $param['requesterName'] = $managerInfo['managerNm'];
            $param['managerName'] = $target['managerNm'];
            $param['subject'] = $subject;
            $param['shopUrl'] = 'http://gdadmin.innoverb2b.com/';
            $param['deadLine'] = $requestInfo['hopeDt'];
            SlKakaoUtil::send(50193 , $target['phone'] ,  $param);
        }*/

    }

    /**
     * 코멘트 저장
     * @param $params
     * @return mixed|string[]
     * @throws \Exception
     */
    public function writeComment($params){
        $sno = $params['sno'];
        if( empty($sno) ){
            //신규 등록
            //필수 체크.
            DBTableField::checkRequired(ImsDBName::TODO_COMMENT, $params);
            $params['regManagerSno'] = SlCommonUtil::getManagerSno();
            $todoData = DBUtil2::getOne(ImsDBName::TODO_REQUEST,'sno', $params['todoSno']); //요청 정보

            //$todoData
            $contents = nl2br($params['comment']);
            $sql = "select  b.customerName, a.projectYear, a.projectSeason, a.projectNo  from sl_imsProject a join sl_imsCustomer b on a.customerSno = b.sno where a.sno = {$todoData['projectSno']}";
            $projectData = DBUtil2::runSelect($sql)[0];
            $contents .= '<br><br>'.$projectData['projectNo'].' '.$projectData['projectYear'].$projectData['projectSeason'].' '.$projectData['customerName'];

            $title = "[ {$todoData['subject']} ] 건에 댓글이 추가 되었습니다.";
            $this->sendTodoMailAndKakao($params['todoSno'], $title, $contents);
        }else{
            //수정
            unset($params['todoSno']);
        }

        $sno = $this->save(ImsDBName::TODO_COMMENT, $params); //comment만 변경.

        return $sno;
    }

    /**
     * 나의 요청 리스트
     * @param $params
     * @return array
     */
    public function getListTodoComment($params){
        $searchVo = new SearchVo('todoSno=?',$params['condition']['todoSno']);
        $searchVo->setOrder('a.regDt desc'); //가장 최근 순
        //$searchVo->setWhere();
        //$searchVo->setWhereValue();

        $list = DBUtil2::getComplexList($this->sql->getTodoCommentListTable(),$searchVo);
        foreach($list as $key => $each){
            $list[$key]['commentBr'] = nl2br($each['comment']);
        }

        return $list;
    }


    /**
     * To-Do 코멘트 제거
     * @param $params
     * @throws \Exception
     */
    public function deleteTodoComment($params)
    {
        DBUtil2::delete(ImsDBName::TODO_COMMENT, new SearchVo('sno=?', $params['sno'])); //굳이 백업X
    }

    /**
     * TO-DO 지우기
     * @param $params
     * @throws \Exception
     */
    public function deleteTodoRequest($params)
    {
        //DBUtil2::delete(ImsDBName::TODO_RESPONSE, new SearchVo('sno=?', $params['sno'])); //굳이 백업X
        $this->delete(ImsDBName::TODO_RESPONSE, $params['sno']);
    }


    /**
     * 처리할 작업 카운팅(메인 메뉴 용)
     * @return array
     */
    public function getTodoRequestCount(){
        $mSno = \Session::get('manager.sno');
        $managerInfo = SlCommonUtil::getManagerInfo($mSno);
        $teamSno = $managerInfo['departmentCd'];

        //나의 요청
        $sql = "select 'request' as estimateType, count(1) as cnt from sl_imsTodoRequest a join sl_imsTodoResponse b on a.sno = b.reqSno where  b.status = 'ready' and a.todoType='todo' and a.regManagerSno={$mSno} and a.delFl = 'n'";
        $result = SlCommonUtil::arrayAppKeyValue(DBUtil2::runSelect($sql), 'estimateType', 'cnt');

        $sql = "select count(1) as cnt from sl_imsTodoRequest a join sl_imsTodoResponse b on a.sno = b.reqSno where b.status = 'ready' and a.todoType='todo' and ( b.managerSno={$mSno} OR b.managerSno={$teamSno} ) and a.delFl = 'n'";
        $result['inbox'] = DBUtil2::runSelect($sql)[0]['cnt'];

        //$sql = "select count(1) as cnt from sl_imsTodoRequest a join sl_imsTodoResponse b on a.sno = b.reqSno where b.status = 'proc' and approvalStatus='proc' and a.todoType='approval' and ( b.managerSno={$mSno} or a.regManagerSno={$mSno}) and a.delFl = 'n'";

        $filePath = './module/Component/Ims/Sql/';
        $approvalTotalSql = $filePath.'approval_total.sql';
        $sql = SlCommonUtil::getFileData($approvalTotalSql);
        $sql = str_replace('{mSno}', $mSno, $sql);
        //gd_debug($sql);
        $result['approval'] = DBUtil2::runSelect($sql)[0]['cnt'];
        //gd_debug($sql);

        return $result;
    }

    /**
     * 긴급 요청건 가져오기 ( 화면에 뿌린다. )
     * @return array
     */
    public function getEmergencyTodoRequest(){
        $mSno = \Session::get('manager.sno');
        return DBUtil2::runSelect("
        select a.sno as reqSno, b.sno as resSno, a.subject, a.customerSno, c.customerName, a.projectSno, d.managerNm as regManagerName  
          from sl_imsTodoRequest a 
          join sl_imsTodoResponse b on a.sno = b.reqSno
          join sl_imsCustomer c on a.customerSno = c.sno   
          join es_manager d on a.regManagerSno = d.sno
         where a.emergency = 'y' 
           and a.delFl = 'n'
           and b.managerSno = {$mSno}
           and ( b.emergencyConfirmDt = '0000-00-00' OR  b.emergencyConfirmDt is NULL)
        ");
    }


    /**
     * 결재라인 정보
     * @param $params
     * @return array|mixed
     */
    public function getApprovalLine($params){
        if(empty($params['sno'])){
            $data = DBTableField::getTableBlankData('tableImsApprovalLine'); //초기 데이터.
        }else{
            $condition = [
                'sno' => $params['sno'],
            ];
            $dataList = $this->getListApprovalLine([
                'condition' => $condition
            ])['list'];
            $data = $dataList[0];
        }
        return $data;
    }


    /**
     * 승인 상태 변경
     * @param $params (sno, approvalStatus)
     * @return array|mixed
     * @throws \Exception
     */
    public function setApprovalStatus($params){

        if(empty($params['sno'])) throw new \Exception('결재 번호가 없습니다!(개발팀 문의)');

        $rsltMsg = '처리 되었습니다.';

        //어떤 결재자인지 확인
        $approvalData = $this->getTodoRequest($params);

        if( empty(SlCommonUtil::isDevId()) ){
            if(!$approvalData['myAccept'] && !$approvalData['myCancel'] && 'remove' !== $params['approvalStatus'] ) throw new \Exception(SlCommonUtil::getMyInfo()['managerNm'] . '님은 결재자가 아닙니다!');
        }

        if( 'allAccept' === $params['approvalStatus'] ) {
            $completeDt = 'now()';
            $rsltMsg = '승인(전결) 처리 되었습니다.';

            $subject='';
            $msg='';
            foreach($approvalData['targetManagerList'] as $approval){
                $saveEach = [
                    'sno'=>$approval['resSno'],
                    'status'=>'complete',
                    'completeDt'=>$completeDt
                ];
                $this->save(ImsDBName::TODO_RESPONSE, $saveEach);

                $managerInfo = SlCommonUtil::getManagerInfo($approval['sno']);
                $subject = "IMS [{$approvalData['subject']}] 전결 승인 처리되었습니다.";
                $msg = "고객명 : {$approvalData['customerName']}<br>프로젝트번호 : {$approvalData['projectNo']}<br>요청자 : {$approvalData['regManagerNm']}";
                $msg .= "<br><br>전결 사유 : {$params['reason']}";
                $msg .= "<br>전결 처리자 : ". \Session::get('manager.managerNm');
                SiteLabMailUtil::sendSystemMail($subject, $subject.'<br><br>'.$msg, $managerInfo['email']);
            }
            //개발자 테스트 용
            SiteLabMailUtil::sendSystemMail($subject, $subject.'<br><br>'.$msg, 'jhsong@msinnover.com');
            $params['approvalStatus'] = 'accept';
        }else if( 'accept' === $params['approvalStatus'] ) {
            $completeDt = 'now()';
            $rsltMsg = '승인 처리 되었습니다.';
            //승인 메세지 전달
            $this->sendApprovalMsg($approvalData['sno'], '이(가) 승인되었습니다.','req');
            //다음 결재자에 결재 요청 정보 발송
            $this->sendApprovalMsg($approvalData['sno'], ' 결재 바랍니다.');
        }else if( 'reject' === $params['approvalStatus'] ){
            $rsltMsg = '반려 처리 되었습니다.';
            $completeDt='now()';
            $this->sendApprovalMsg($approvalData['sno'], '이(가) 반려되었습니다.','req');
            //요청자에게 알려준다.
        }else if( 'remove' === $params['approvalStatus'] ){
            $rsltMsg = '결재요청이 철회 되었습니다.';
            $completeDt='';
            $this->sendApprovalMsg($approvalData['sno'], ' 결재 요청이 철회 되었습니다.');
            $this->save(ImsDBName::TODO_REQUEST, [
                'sno'=>$approvalData['sno']
                ,'delFl'=>'y'
            ]);//업데이트
        }else{
            $completeDt='';
        }

        if('cancel' === $params['approvalStatus']){
            $params['approvalStatus'] = 'proc';
        }
        //SitelabLogger::log('철회조건 체크');
        //SitelabLogger::log($params);

        $saveResData = [
            'sno'=>$approvalData['myAcceptInfo']['responseSno'],
            'status'=>$params['approvalStatus'],
            'completeDt'=>$completeDt,
            'reason' => $params['reason']
        ];
        $this->save(ImsDBName::TODO_RESPONSE, $saveResData);
        $this->checkAndSetApprovalStatus($params['sno']); //TODO : 결재 철회시 r 체크하기

        if(!empty($approvalData['projectNo'])){
            ImsScheduleUtil::setProjectScheduleStatus($approvalData['projectNo']);
        }

        return [
            'msg' => $rsltMsg,
        ];
    }

    /**
     * 결재 상태 체크
     * @param $approvalSno
     * @throws \Exception
     */
    public function checkAndSetApprovalStatus($approvalSno){

        $approvalData = $this->getTodoRequest(['sno'=>$approvalSno]);

        if('proc' === $approvalData['approvalStatus']){
            //최종 승인자가 결재를 완료 했는가 ? => 전체 승인한것으로 간주. (반련 제외)
            $finalManager = $approvalData['targetManagerList'][count($approvalData['targetManagerList'])-1];

            if( 'accept' === $finalManager['status'] ){
                foreach($approvalData['targetManagerList'] as $targetKey => $target){
                    if( 'proc' === $target['status'] ){
                        $saveResData = [
                            'sno'=>$target['resSno'],
                            'status'=>'complete',
                            'completeDt'=>'now()'
                        ];
                        $this->save(ImsDBName::TODO_RESPONSE, $saveResData);
                        $approvalData['targetManagerList'][$targetKey]['status'] = 'complete';
                    }
                }
            }

            //전체 승인인지, 반려가 있는지 체크
            $isComplete = true;
            $isReject = false;
            foreach($approvalData['targetManagerList'] as $target){
                if( !('accept' === $target['status'] || 'complete' === $target['status']) )  $isComplete &= false; //승인체크
                if( 'reject' === $target['status'] )  $isReject   |= true;  //반려체크
            }
            //승인 처리 됨.
            if($isComplete){
                $this->runApprovalMethod('accept', $approvalData['approvalType'], $approvalData);
            }
            //반려 처리 됨.
            if($isReject){
                $this->runApprovalMethod('reject', $approvalData['approvalType'], $approvalData);
            }
        }else if('accept' === $approvalData['approvalStatus']){
            //전체 승인인지
            $isComplete = true;
            foreach($approvalData['targetManagerList'] as $target){
                if( !('accept' !== $target['status'] || 'complete' !== $target['status'])  )  $isComplete &= false; //승인체크
            }
            if($isComplete){
                //$fncName = 'proc'.ucfirst($approvalData['approvalType']);
                //$imsApprovalService->$fncName($approvalData);
                $this->runApprovalMethod('proc', $approvalData['approvalType'], $approvalData);
            }
        }else if('reject' === $approvalData['approvalStatus']){
            //한개라도 반려가 있는지
            $isReject = false;
            foreach($approvalData['targetManagerList'] as $target){
                if( 'reject' === $target['status'] )    $isReject   |= true;  //반려체크
            }
            if($isReject){
                //$fncName = 'proc'.ucfirst($approvalData['approvalType']);
                //$imsApprovalService->$fncName($approvalData);
                $this->runApprovalMethod('proc', $approvalData['approvalType'], $approvalData);
            }
        }
    }

    /**
     * 상태값 결정
     * @param array $bStates
     * @return string
     */
    public function determineAStatus(array $bStates): string {
        // 상태값들이 모두 'n'인지 확인
        if (count(array_unique($bStates)) === 1 && $bStates[0] === 'n') {
            return 'n';
        }
        // 상태값들이 모두 'p'인지 확인
        if ( count(array_unique($bStates)) === 1 && ($bStates[0] === 'p' || $bStates[0] === 'x') ) {
            return 'p';
        }
        // 그 외의 경우는 'r' 반환
        return 'r';
    }

    /**
     * 결재선 저장
     * @param $params
     */
    public function saveApprovalLine($params){
        $params['appManagers'] = json_encode($params['appManagers']);
        $params['refManagers'] = json_encode($params['refManagers']);
        $this->save(ImsDBName::APPROVAL_LINE, $params);
    }

    /**
     * 결재 메소드 실행
     * @param $status
     * @param $type
     * @param $approvalData
     */
    public function runApprovalMethod($status,$type,$approvalData){
        $methodClass = new \ReflectionClass('\Component\Ims\ImsApprovalService');
        $methods = $methodClass->getMethods();
        $methodMap = [];
        foreach( $methods as $method ){
            $methodMap[$method->name]=1;
        }
        $imsApprovalService = SlLoader::cLoad('ims', 'imsApprovalService');
        $fncName = $status.ucfirst($type);

        if( !empty($methodMap[$fncName]) ){
            $imsApprovalService->$fncName($approvalData);
        }else{
            $statusDbValueMap = [
                'accept' => 'p','reject' => 'f','proc' => 'r'
            ];
            //없는건 이거롤 한다.
            $imsApprovalService->statusChangeCommon($approvalData, $status, $statusDbValueMap[$status]);
        }
    }

    /**
     * TO-DO 3PL 입고 알림 요청
     */
    public function setTodo3plStoreAlarm(){
        $deliveryExpectedDt = SlCommonUtil::getDateCalc(date('Y-m-d'),30); //선적예정일
        $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'),5); //처리 완료일

        $params['condition']['searchDateType']='a.shipExpectedDt';
        $params['condition']['startDt']=$deliveryExpectedDt;
        $params['condition']['endDt']=$deliveryExpectedDt;
        $params['condition']['productionStatus']=4; //4=30 생산 진행 중.

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $list = $imsService->getListProduction($params);

        $targetList = [];
        foreach($list['list'] as $key => $each){
            if('y' === $each['use3pl']){
                $targetList[$each['customerSno']]['customerName']=$each['customerName'];
                $targetList[$each['customerSno']]['productList'][]= '<span class="text-danger">'.$each['projectNo'] . '</span> ' . $each['productName'].'('.number_format($each['totalQty']).'개)';
            }
        }

        $subject = '3PL업체 입고알림 처리 ('. $deliveryExpectedDt .'일 선적 예정 건)';
        $contents = [];
        $contents[] = "<b>{$subject}</b>";

        $isContinue = false;

        foreach($targetList as $customerSno => $target){
            $contents[] = "<br><b>{$target['customerName']}</b>";
            foreach($target['productList'] as $prd){
                $contents[] = " * {$prd}";
                $isContinue = true;
            }
        }

        $document = [
            'todoType' => 'todo',
            'subject' => $subject,
            'contents' => implode('<br>', $contents),
            'hopeDt' => $hopeDt,
        ];
        $reqManagers = ImsCodeMap::PRIVATE_MALL_MANAGER_SIMPLE; //폐쇄몰 담당자
        /*$reqManagers = [
            15 //개발자.
        ];*/
        $saveTodoData = [
            'document' => $document,
            'reqManagers'  => $reqManagers
        ];

        if( $isContinue ){
            $imsService->saveTodo($saveTodoData);
        }
        //SitelabLogger::logger2(__METHOD__, '작업이 잘 실행되었습니다. '. $deliveryExpectedDt);
    }

    /**
     * TO-DO Simple하게 System 등록
     * @param $subject
     * @param $hopeDt
     * @param $contents
     * @param array $reqManagers
     * @throws \Exception
     */
    public function addSimpleTodoData($subject, $hopeDt, $contents, array $reqManagers){
        $document = [
            'todoType' => 'todo',
            'subject' => $subject,
            'contents' => $contents,
            'hopeDt' => $hopeDt,
        ];
        $saveTodoData = [
            'document' => $document,
            'reqManagers'  => $reqManagers
        ];
        $this->saveTodo($saveTodoData);
    }


    /**
     * 원부자재 선적일 기준 패킹 리스트 요청 TO-DO LIST 등록
     */
    public function autoPackingTodo(){
        //오늘 원부자재 선적 예정일
        $today = date('Y-m-d');
        $expectedDt = SlCommonUtil::getDateCalc($today,10);

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $condition['isExcludeRtw'] = 'true';
        $condition['searchDateType'] = 'a.fabricShipExpectedDt';
        $condition['startDt'] = $today;
        $condition['endDt'] = $today;
        $condition['productionStatus'] = '4';
        $condition['packingYn'] = 'y';

        $list = $imsService->getListProduction(['condition'=>$condition]);
        $alarmList = [];
        foreach($list['list'] as $each){
            $alarmList[$each['customerSno']] = $each;
        }
        $customerNameList = [];
        foreach($alarmList as $target){
            $customerNameList[] = $target['customerName'];
        }

        if( !empty($customerNameList) ){
            $subject = '분류패킹 파일 가져오기';
            $contents = implode(',',$customerNameList);
            $contents .= "<br> 위 고객사 원부자재 선적 예정일입니다.";
            $contents .= "<br> 분류패킹 파일을 10일 내로 ({$expectedDt}) 확보 바랍니다.";
            $imsService->addSimpleTodoData($subject, $expectedDt, $contents, [
                '02001001', //QC
                '02001003', //생산
            ]);
        }
    }


    /**
     * 간단 TO-DO 등록
     * @param $subject
     * @param $contents
     * @param $hopeDt
     * @param $projectSno
     * @param $reqManagers
     * @param string $emergency
     * @throws \Exception
     */
    public function addTodoData($subject, $contents, $hopeDt, $projectSno, $reqManagers, $emergency='n'){
        $saveTodoData = [
            'document' => [
                'todoType' => 'todo',
                'subject' => $subject,
                'contents' => $contents,
                'hopeDt' => $hopeDt,
                'projectSno' => $projectSno,
                'emergency' => $emergency,
            ],
            'reqManagers'  => $reqManagers,
        ];
        $this->saveTodo($saveTodoData);
    }

}
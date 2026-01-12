<?php

namespace Component\Imsv2;

use App;
use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceSortTrait;
use Component\Ims\ImsServiceTrait;
use Component\Imsv2\Util\ImsProjectListServiceUtil;
use Component\Member\Manager;
use Component\Sms\Code;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\ImsStatusUtil;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * IMSv2 프로젝트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsProjectService
{
    use ImsServiceTrait;
    use ImsStepTrait;

    private $sql;

    public function __construct(){
        $this->sql = \App::load('\\Component\\Imsv2\\Sql\\ImsProjectListServiceSql');
    }

    /**
     * Ims Project 관련 데이터 저장
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveImsProject($params){
        $projectData = $params['project'];
        $projectExtData = $params['projectExt'];

        //프로젝트 저장
        $sno = $this->imsSave('project', $projectData);

        //프로젝트 확장 정보 저장
        $projectExtData['projectSno'] = $sno;
        $this->imsSave('projectExt', $projectExtData);

        //스케쥴 정보 저장
        $this->saveAddInfo(['sno'=>$sno]);

        //프로젝트별 세부스케쥴 insert
        /*if( empty($projectData['sno']) || 0 >= $projectData['sno'] ){
            $imsNkService = SlLoader::cLoad('imsv2', 'imsNkService');
            $aScheDetail = $imsNkService->getListScheDetail();
            $iSalesManagerSno = (int)$params['project']['salesManagerSno'];
            $iDesignManagerSno = (int)$params['project']['designManagerSno'];
            $sCurrDt = date('Y-m-d H:i:s');
            $iSort = 0;
            foreach ($aScheDetail as $key => $val) {
                $iOwner = 0;
                $sDepart = '';
                if ($val['grpSche'] == 1) $iOwner = $iSalesManagerSno;
                else if ($val['grpSche'] == 2) $iOwner = $iDesignManagerSno;
                else if ($val['grpSche'] == 3) $sDepart = '생산';

                $iSort++;
                $aInsertProjectScheDetail = [
                    'projectSno'=>$sno,
                    'scheDetailSno'=>$key,
                    'sortSche'=>$iSort,
                    'ownerManagerSno'=>$iOwner,
                    'departName'=>$sDepart,
                    'deadlineDt'=>$sCurrDt,
                    'expectedDt'=>$sCurrDt,
                    'scheSt'=>1,
                    'regDt'=>$sCurrDt,
                ];

                DBUtil2::insert(ImsDBName::PROJECT_SCHE_DETAIL, $aInsertProjectScheDetail);
            }
        }*/

        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * Ims Project 데이터 수정
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function updateProject($params){

        $projectData = $params['project'];
        unset($projectData['projectStatus']); //상태(단계)는 저장하지 않는다.

        //프로젝트 저장
        $projectSno = $this->imsSave('project', $projectData);



        $projectExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectSno);

        $projectExtData = $projectData;
        $projectExtData['sno'] = $projectExt['sno'];

        //연계 정보 업데이트 (고객)
        DBUtil2::update(ImsDBName::CUSTOMER, [
            'salesManagerSno' => $projectData['salesManagerSno'],
            'designManagerSno' => $projectData['designManagerSno'],
        ], new SearchVo('sno=?', $projectData['customerSno']));

        //확장 정보 저장
        //$projectExtData['extDesigner'] = json_encode($projectExtData['extDesigner']);
        $this->imsSave('projectExt', $projectExtData);

        //영업 상태 변경시 처리
        $this->checkSalesStatusRelatedProc($projectSno, $projectData['salesStatus']);

        //상품별 연동 업데이트 처리.
        $loadProject = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);

        $prdList = DBUtil2::getList(ImsDBName::PRODUCT,"delFl='n' and projectSno",$projectSno);
        foreach($prdList as $prd){
            $updateData = [];
            
            //납기 연동인경우
            if('y' === $loadProject['syncProduct']){
                $updateData['customerDeliveryDt'] = $loadProject['customerDeliveryDt'];
                $updateData['msDeliveryDt'] = $loadProject['msDeliveryDt'];
            }

            //생산처 연동 (값이 없을때만)
            if(!empty($loadProject['produceCompanySno']) && empty($prd['produceCompanySno']) ){
                $updateData['produceCompanySno'] = $loadProject['produceCompanySno'];
            }
            //생산국가 연동 (값이 없을때만)
            if(!empty($loadProject['produceType']) && empty($prd['produceType']) ){
                $updateData['produceType'] = $loadProject['produceType'];
            }
            //생산국가 연동 (값이 없을때만)
            if(!empty($loadProject['produceNational']) && empty($prd['produceNational']) ){
                $updateData['produceNational'] = $loadProject['produceNational'];
            }

            if(!empty($updateData) && count($updateData)>0){
                //SitelabLogger::logger2(__METHOD__, '업데이트 합니다.');
                //SitelabLogger::logger2(__METHOD__, $updateData);
                $updateData['sno']=$prd['sno'];
                $this->imsSave('projectProduct',$updateData);
            }
        }

        //추가 참여자 인원 갱신
        ImsScheduleUtil::setAddManager($projectSno, $params);

        //상태 체크
        //SitelabLogger::logger2(__METHOD__, '==> CHECK');
        //SitelabLogger::logger2(__METHOD__, $projectSno);
        ImsScheduleUtil::setProjectScheduleStatus($projectSno);


        //프로젝트 정보 갱신
        ImsUtil::refreshProject($projectSno);
        
        return ['data'=> $projectSno,'msg'=>'저장 완료'];
    }

    /**
     * 영업 상태 변경
     * @param $projectSno
     * @param $salesStatus
     * @throws \Exception
     */
    public function setSalesStatus($projectSno, $salesStatus){
        $projectExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectSno);
        if($salesStatus !== $projectExt['salesStatus']){
            $this->imsSave('projectExt', [
                'sno' => $projectExt['sno'],
                'salesStatus' => $salesStatus,
            ]);
            $this->checkSalesStatusRelatedProc($projectSno,$salesStatus);
        }
    }

    /**
     * 영업상태 체크후 연관 처리
     * @param $projectSno
     * @param $salesStatus
     * @throws \Exception
     */
    public function checkSalesStatusRelatedProc($projectSno, $salesStatus){
        //'hold' => '영업보류', //'fail' => '유찰',
        if( 'fail' === $salesStatus || 'hold' === $salesStatus){
            $this->imsSave('project', [
                'sno' => $projectSno,
                'projectStatus' => 11,
            ]);
        }
    }

    /**
     * 간단 프로젝트 반환
     * @param $sno
     * @return mixed
     * @throws \Exception
     */
    public function getSimpleProject($sno){
        $projectData = DBUtil2::getComplexList($this->sql->getProjectListSql(),new SearchVo('prj.sno=?',$sno), false, false, false)[0];
        $projectData = SlCommonUtil::refineDbData($projectData, ImsDBName::PROJECT);
        $projectData = SlCommonUtil::refineDbData($projectData, ImsDBName::PROJECT_EXT);

        $projectData['accountingMessage'] = gd_htmlspecialchars_stripslashes($projectData['accountingMessage']);
        $projectData['accountingMessageBr'] = nl2br(gd_htmlspecialchars_stripslashes($projectData['accountingMessage']));

        //DECORATION (소스 양 보고 따로 뺄지는 추 후 결정)
        //영업 담당자 설정
        if( empty($projectData['salesManagerSno']) ){
            $sql = "select a.*, b.managerNm from sl_imsCustomer a left outer join es_manager b on a.salesManagerSno = b.sno where a.sno = {$projectData['customerSno']} ";
            $customerData = DBUtil2::runSelect($sql)[0];
            $projectData['salesManagerSno'] = $customerData['salesManagerSno'];
            $projectData['salesManagerNm'] = $customerData['managerNm'];
        }

        $this->decorationSimpleProject($projectData);

        return $projectData;
    }

    /**
     * 프로젝트 데이터 추가 정보 삽입 (AddInfo DB조회 있음)
     * @param $projectData
     */
    public function decorationSimpleProject(&$projectData){
        //$projectData = $this->setScheduleStatus($projectData);
        $projectData['fabricStatusKr'] = ImsCodeMap::IMS_FABRIC_STATUS[$projectData['fabricStatus']]['name'];
        $projectData['btStatusKr'] = ImsCodeMap::IMS_BT_STATUS[$projectData['btStatus']]['name'];
        $projectData['workStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['workStatus']]['name'];
        $projectData['workStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['workStatus']]['icon'];

        $projectData['priceStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['priceStatus']]['name'];
        $projectData['priceStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['priceStatus']]['icon'];

        $projectData['costStatusKr'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['costStatus']]['name'];
        $projectData['costStatusIcon'] = ImsCodeMap::IMS_PROC_STATUS[$projectData['costStatus']]['icon'];

        $projectData['deliveryMethod'] = gd_htmlspecialchars_stripslashes($projectData['deliveryMethod']);

        $projectData['isReorderType'] = !empty(ImsCodeMap::PROJECT_TYPE_R[$projectData['projectType']])?'y':'n';

        //제안형태 처리
        $recommend = $projectData['recommend'];
        $projectData['recommendList'] = [];
        foreach (ImsCodeMap::RECOMMEND_TYPE as $recommendKey => $recommendValue) {
            if (($recommendKey & $recommend) > 0) {
                $projectData['recommendList'][] = $recommendKey . '';
            }
        }

        //추가 담당자 가져오기
        $addManagerMap = ImsProjectService::getProjectAddManagerList($projectData['sno']);

        //스케쥴 처리 
        $scheduleMap = ImsScheduleUtil::getScheduleMap();
        //$scheduleList = array_merge(ImsCodeMap::PROJECT_SCHEDULE_LIST, ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST);
        foreach($scheduleMap as $scheduleKey => $scheduleName){
            $projectData[$scheduleKey.'CommentCnt'] = DBUtil2::runSelect("select count(1) as cnt from sl_imsComment where commentDiv='{$scheduleKey}' and projectSno={$projectData['sno']} ")[0]['cnt'];
            $projectData[$scheduleKey.'Status'] = ImsScheduleConfig::PROJECT_SCHEDULE_STATUS[$projectData['st'.ucfirst($scheduleKey)]]['name'];
            $projectData[$scheduleKey.'Icon'] = ImsScheduleConfig::PROJECT_SCHEDULE_STATUS[$projectData['st'.ucfirst($scheduleKey)]]['icon'];
            $projectData[$scheduleKey.'Color'] = ImsScheduleConfig::PROJECT_SCHEDULE_STATUS[$projectData['st'.ucfirst($scheduleKey)]]['color'];

            $projectData[$scheduleKey.'AddManager'] = $addManagerMap[$scheduleKey]; //      ImsCodeMap::PROJECT_SCHEDULE_STATUS[$projectData['st'.ucfirst($scheduleKey)]]['color'];
        }
        
        ImsProjectService::decorationProjectCommon($projectData);

        //예정 디자이너 (신규버전 미사용)
        if(empty($projectData['extDesigner'])){
            $projectData['extDesigner'] = [];
        }
    }


    /**
     * 예정 스케쥴 반환
     * @param $projectSno
     * @param null $prjExt
     * @return array
     */
    public static function getExpectedSchedule($projectSno, $prjExt=null){
        if(empty($prjExt)){
            $prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectSno);
        }
        $scheduleKeyList = array_keys(ImsScheduleConfig::SCHEDULE_LIST);
        $scheduleTargetKeyList = [];
        foreach($scheduleKeyList as $key => $each){
            if( !empty($prjExt['ex'.ucfirst($each)])
                && '0000-00-00' !== $prjExt['ex'.ucfirst($each)]
                && ( empty($prjExt['cp'.ucfirst($each)]) || '0000-00-00' == $prjExt['cp'.ucfirst($each)] )
                && ( empty($prjExt['tx'.ucfirst($each)]) )
            ){
                $scheduleTargetKeyList[] = $each;
            }
        }
        return $scheduleTargetKeyList;
    }


    /**
     * 프로젝트 추가 담당자 스케쥴별 리스트
     * @param $projectSno
     * @param bool $expectedOnly
     * @return array
     */
    public static function getProjectAddManagerList($projectSno, $expectedOnly=false){
        $searchVo = new SearchVo('projectSno=?', $projectSno);
        if($expectedOnly){
            $searchVo->setWhere('scheduleStatus=?');
            $searchVo->setWhereValue('1');
        }
        //참여 내용만 가져오기
        $addManagerList=DBUtil2::getJoinList(ImsDBName::PROJECT_MANAGER, [
            'b' => [DB_MANAGER, 'a.managerSno=b.sno', 'b.managerNm']
        ],$searchVo);
        $addManagerMap = [];
        foreach($addManagerList as $each){
            unset($each['regDt']);
            unset($each['modDt']);
            $addManagerMap[$each['scheduleDiv']][] = $each;
        }
        return $addManagerMap;
    }

    /**
     * 프로젝트 추가 담당자 저장
     * @param $params
     */
    public function addProjectMember($params){
        foreach($params['schedule'] as $schedule){
            foreach($params['managers'] as $managerSno){
                if($managerSno > 0){
                    $before = DBUtil2::getOne(ImsDBName::PROJECT_MANAGER, "scheduleDiv='{$schedule}' AND managerSno", $managerSno);
                    if(empty($before)){
                        DBUtil2::insert(ImsDBName::PROJECT_MANAGER, [
                            'projectSno' => $params['projectSno'],
                            'scheduleDiv' => $schedule,
                            'managerSno' => $managerSno,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 프로젝트 공통 데코레이션
     * @param $each
     * @return mixed
     */
    public static function decorationProjectCommon(&$each){
        $now = date('Y-m-d');
        $map = ImsScheduleUtil::getScheduleMap();
        foreach($map as $scKey => $scName){
            $compareKey = ucfirst($scKey);
            $each['delay'.$compareKey] = 'n';
            if(!empty($each['ex'.$compareKey]) && empty($each['cp'.$compareKey]) && empty($each['tx'.$compareKey]) ){
                if( $now > $each['ex'.$compareKey] ){
                    $each['delay'.$compareKey] = 'y';
                }
            }
        }
        $each['isReorder'] = in_array($each['projectType'], array_flip(ImsCodeMap::PROJECT_TYPE_R));
    }


    /**
     * 스케쥴 상태 설정
     * @param $projectData
     * @return mixed
     */
    public function setScheduleStatus($projectData){
        //프로젝트 일정 가져오기.
        /*$scheduleList =  DBUtil2::getList(ImsDBName::PROJECT_ADD_INFO, 'projectSno', $projectData['sno']);
        foreach($scheduleList as $key => $each){
            $scheduleList[$key] = SlCommonUtil::setDateBlank($each);
        }
        $scheduleMap = SlCommonUtil::arrayAppKey( $scheduleList,'fieldDiv');

        //제안 단계 ( 기획 > 제안 > 제안서발송 > 제안서확정대기 )
        $mapProposal = [ //키값은 완료된 스텝이다.
            '' => '기획서 준비중',
            'plan' => '제안서 준비중',
            'proposal' => '제안서 발송 대기',
            'custInform' => '제안서 확정 대기',
            'custProposalConfirm' => '제안 단계 완료',
        ];
        $stepProposal = ['plan', 'proposal', 'custInform', 'custProposalConfirm'];

        //샘플 단계 ( 샘플지시서 > 샘플완료일 > 샘플리뷰서 > 샘플발송 > 샘플 확정 요청 )
        $mapSample = [
            '' => '대기 중',
            'sampleOrder'       => '샘플 작업 진행 중',
            'sampleOut'         => '리뷰 대기 중',
            'sampleReview'      => '샘플 발송 대기 중',
            'custSampleInform'  => '샘플 확정 대기 중',
            'custSampleConfirm' => '샘플 단계 완료',
        ];
        $stepSample = ['sampleOrder', 'sampleOut', 'sampleReview', 'custSampleInform', 'custSampleConfirm'];

        //발주 단계 ( 고객발주 >  작지/사양서 > 사양서 발송 > 사양서 확정 ) , 샘플회수는 별도로 본다 (단계로 보지 않음).
        $mapOrder = [
            '' => '대기 중',
            'custOrder' => '작지/사양서 작업 중',
            'order' => '사양서 발송 대기',
            'custSpec' => '사양서 확정 대기',
            'custSpecConfirm' => '발주 준비 완료',
        ];
        $stepOrder = ['custOrder' ,'order', 'custSpec', 'custSpecConfirm'];

        $projectData['scheduleStatus'] = [
            'stepProposal' => $this->setScheduleUnitStatus($stepProposal, $scheduleMap, $mapProposal),
            'stepSample'   => $this->setScheduleUnitStatus($stepSample, $scheduleMap, $mapSample),
            'stepOrder'    => $this->setScheduleUnitStatus($stepOrder, $scheduleMap, $mapOrder),
        ];
        $projectData['schedule'] = $scheduleMap;*/
        return $projectData;
    }

    /**
     * 스케쥴 상태 반환
     * @param $stepData
     * @param $checkMap
     * @param $statusMap
     * @return array
     */
    public function setScheduleUnitStatus($stepData, $checkMap, $statusMap){
        $checkCnt = 0;
        $completeCnt = 0;
        $lastStep = '';
        $lastStatusCode = 0;

        foreach($stepData as $step){
            $checkValue = $checkMap[$step];
            //완료상태 체크
            if(
                (!empty($checkValue['completeDt']) && '0000-00-00' != $checkValue['completeDt']) //완료일 등록
                || !empty($checkValue['alterText']) //대체 텍스트 등록
            ){
                $lastStep = $step;
                $completeCnt++;
            }
            $checkCnt++;
        }

        if(!empty($lastStep)){
            $lastStatusCode = 1;
            if( $checkCnt == $completeCnt ) $lastStatusCode = 2;
        }

        return [
            'checkCnt' => $checkCnt, //체크 대상 수
            'completeCnt' => $completeCnt, //완료 대상 수
            'lastStatus'  => $statusMap[$lastStep], //마지막 완료된 단계
            'lastStatusCode' => $lastStatusCode, //마지막 완료된 단계
        ];
    }

    /**
     * 프로젝트 결재 상태 패스
     * @param $params
     * @throws \Exception
     */
    public function setApproval($params){
        //SitelabLogger::logger2(__METHOD__, '===> CHECK');
        //SitelabLogger::logger2(__METHOD__, $params);
        DBUtil2::update(ImsDBName::PROJECT, [
            $params['approvalType'] => $params['status'],
            $params['approvalMemo'] => $params['memo'],
        ], new SearchVo('sno=?', $params['projectSno']));

        //기획 승인 상태 처리
        if( 'planConfirm' === $params['approvalType'] ){
            if( 'p' === $params['status'] ){
                if(!empty($params['memo'])){
                    $status = 9;
                    ImsStatusUtil::setStatus(['projectSno'=>$params['projectSno'],'projectStatus'=>30,'reason'=>'기획 PASS로 단계변경']); //제안단계로 변경
                }else{
                    $status = 10;
                    ImsStatusUtil::setStatus(['projectSno'=>$params['projectSno'],'projectStatus'=>30,'reason'=>'기획 승인 완료로 단계변경']); //제안단계로 변경
                }
                DBUtil2::update(ImsDBName::PROJECT_EXT, ['stPlan'=>$status,'cpPlan'=>'now()'], new SearchVo('projectSno=?',$params['projectSno'])); //완료일 등록
            }
            //PASS 원복
            if( 'n' === $params['status'] ){
                DBUtil2::update(ImsDBName::PROJECT_EXT, ['stPlan'=>0,'cpPlan'=>'0000-00-00'], new SearchVo('projectSno=?',$params['projectSno']));
            }
        }

        //제안 승인 상태 처리
        if( 'proposalConfirm' === $params['approvalType'] ){
            if( 'p' === $params['status'] ){
                if(!empty($params['memo'])){
                    $status = 9;
                    ImsStatusUtil::setStatus(['projectSno'=>$params['projectSno'],'projectStatus'=>40,'reason'=>'제안서 PASS로 단계변경']); //샘플 단계로 변경
                    DBUtil2::update(ImsDBName::PROJECT_EXT, ['stMeetingProposal'=>9,'cpMeetingProposal'=>'now()'], new SearchVo('projectSno=?',$params['projectSno'])); //연관상태도 PASS (제안서 전달)
                }else{
                    $status = 10;
                    ImsStatusUtil::setStatus(['projectSno'=>$params['projectSno'],'projectStatus'=>40,'reason'=>'제안서 승인되어 단계변경']); //샘플 단계로 변경
                }
                //완료일 등록
                DBUtil2::update(ImsDBName::PROJECT_EXT, ['stProposal'=>$status,'cpProposal'=>'now()'], new SearchVo('projectSno=?',$params['projectSno']));
            }
            //PASS 원복
            if( 'n' === $params['status'] ){
                DBUtil2::update(ImsDBName::PROJECT_EXT, ['stProposal'=>0,'cpProposal'=>'0000-00-00'], new SearchVo('projectSno=?',$params['projectSno']));
                DBUtil2::update(ImsDBName::PROJECT_EXT, ['stMeetingProposal'=>0,'cpMeetingProposal'=>'0000-00-00'], new SearchVo('projectSno=?',$params['projectSno'])); //연관 상태 원복
            }
        }

        //상태 체크
        ImsScheduleUtil::setProjectScheduleStatus($params['projectSno']);
    }


    /**
     * 리오더
     * @param $projectSno
     * @param $initStatus
     * @param $year
     * @param $orderDt
     * @param $deliveryDt
     * @return mixed
     * @throws \Exception
     */
    public function reOrder($projectSno, $initStatus, $year, $orderDt=null, $deliveryDt=null){

        if( empty($projectSno) ){
            throw new \Exception('복사할 프로젝트 번호가 없습니다!');
        }
        $orgProjectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $orgProjectData['customerSno']);

        if(!empty($year)){
            $projectYear = $year;
        }else{
            $projectYear = ((int)$orgProjectData['projectYear'])+1;
        }
        $saveProjectData = SlCommonUtil::getAvailData($orgProjectData, [
            'customerSno',
            'projectSeason',
            'use3pl',
            'useMall',
            'salesManagerSno',
            'produceNational',
        ]);
        $projectMemo = '  ' . $orgProjectData['sno'] . '로 부터 복사함 ' . \Session::get('manager.managerNm') . ' ' . date('y/m/d');
        $saveProjectData['projectMemo'] = $projectMemo;
        $saveProjectData['srcProjectSno'] = $orgProjectData['sno'];
        $saveProjectData['projectStatus'] = $initStatus;
        $saveProjectData['projectYear'] = $projectYear; //TODO - 추가 , 수정AS는 +1 하지 않는다.  //CustomerSno 만 ?
        $saveProjectData['isBookRegistered'] = 'n'; //회계반영은 안함.
        $saveProjectData['customerDeliveryDtConfirmed'] = 'y';
        $saveProjectData['customerDeliveryDtStatus2'] = 'n';
        $saveProjectData['customerDeliveryDtStatus'] = 0;
        $saveProjectData['projectType'] = 1; //타입 리오더
        $saveProjectData['bizPlanYn'] = 'y'; //사업계획
        $saveProjectData['bidType2'] = 'single'; //단독
        //납기일,발주DL 설정.
        $customerDeliveryDt = '';

        $autoDate = true;
        $orderDeadLine = null;
        if(!empty($orderDt)){
            $autoDate = false;
            $saveProjectData['customerOrderDeadLine'] = $orderDt;
            $saveExtData['exProductionOrder'] =$orderDt;
        }
        if(!empty($deliveryDt)){
            $autoDate = false;
            $saveProjectData['customerDeliveryDt'] = $deliveryDt;
        }

        if( $autoDate && !empty($orgProjectData['customerDeliveryDt']) && '0000-00-00' != $orgProjectData['customerDeliveryDt'] ){
            $customerDeliveryDt = SlCommonUtil::getDateCalc($orgProjectData['customerDeliveryDt'],'+365','day');
            $saveProjectData['customerDeliveryDt'] = $customerDeliveryDt;
            $orderDeadLine = SlCommonUtil::getDateCalc($saveProjectData['customerDeliveryDt'],'-150','day');
            $saveExtData['exProductionOrder'] = $orderDeadLine;
            $saveProjectData['customerOrderDeadLine'] = $orderDeadLine;
        }

        $saveExtData['salesStatus'] = 'complete';

        //1. 프로젝트 등록
        $newProjectSno = $this->saveImsProject([
            'project' => $saveProjectData,
            'projectExt' => $saveExtData,
        ])['data'];

        //상품 복사
        $prdList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl = 'n' and projectSno", $projectSno, 'sort',false);

        $this->setReorderSchedule($newProjectSno);

        foreach($prdList as $prd){
            $this->copyStyle($prd['sno'], $newProjectSno, $projectYear, $customerDeliveryDt);
        }
        //gd_debug( $orgProjectData['sno'] . ' = ' . $newProjectSno );

        return $newProjectSno;
    }

    /**
     * 스타일 복사
     * @param $prdSno
     * @param $newProjectSno
     * @param $projectYear
     * @param $customerDeliveryDt
     */
    public function copyStyle($prdSno, $newProjectSno, $projectYear=null, $customerDeliveryDt=null){
        $prd = DBUtil2::getOne(ImsDBName::PRODUCT,'sno',$prdSno);
        $targetProject = DBUtil2::getOne(ImsDBName::PROJECT,'sno',$newProjectSno);
        $customerData = DBUtil2::getOne(ImsDBName::CUSTOMER,'sno',$targetProject['customerSno']);

        $styleCode = empty($projectYear) ? $targetProject['projectYear'] : $projectYear;

        $styleCodeMap = [];
        $styleCodeMap[]=$prd['prdSeason'];
        $styleCodeMap[]=$prd['prdGender'];
        $styleCodeMap[]=$customerData['styleCode'];
        $styleCodeMap[]=$prd['prdStyle'];
        $styleCodeMap[]=$prd['prdColor'];
        $styleCodeMap[]=$prd['addStyleCode'];
        foreach($styleCodeMap as $styleCodeEach){
            if(!empty($styleCodeEach) && '구분없음' != $styleCodeEach ){
                $styleCode .= ' '.$styleCodeEach;
            }
        }
        $newPrd = SlCommonUtil::getAvailData($prd,[
            'prdSeason',
            'prdGender',
            'prdStyle',
            'prdColor',
            'addStyleCode',
            'productName',
            'produceCompanySno',
            'produceNational',
            'prdExQty',
            'salePrice',
            'fileThumbnail',
            'fileThumbnailReal',
            'sampleConfirmSno',
            'sort',
            'sizeSpec',
        ]);

        $newPrd['prdYear'] = '20'.'';
        $newPrd['customerSno'] = $targetProject['customerSno'];
        $newPrd['parentSno'] = $prd['sno'];

        $newPrd['customerDeliveryDt'] = empty($customerDeliveryDt)?$targetProject['customerDeliveryDt']:$customerDeliveryDt; //고객 납기일
        $newPrd['msDeliveryDt'] = empty($customerDeliveryDt)?$targetProject['msDeliveryDt']:''; //이노버 납기일

        $newPrd['styleCode'] = $styleCode; //스타일코드
        $newPrd['projectSno'] = $newProjectSno; //스타일코드

        $newPrdSno = DBUtil2::insert(ImsDBName::PRODUCT, $newPrd); //상품(스타일) 복사

        //Ework 복사
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $imsStyleService->copyStyleBasicInfo([
            'srcSno' => $prd['sno'],
            'targetSno' => $newPrdSno,
        ]);
    }

    public function setReOrderSchedule($projectSno){

        $map = [
            'custSample' => '고객샘플확보',
            'inspection' => '현장조사',
            'meeting' => '미팅',
            'plan' => '기획',
            'proposal' => '제안서',
            'sampleOrder' => '샘플지시서',
            'sampleComplete' => '샘플완료',
            'sampleReview' => '샘플리뷰',
            'sampleInform' => '샘플발송',
            'sampleConfirm' => '샘플확정',
        ];
        $map2 = [
            'ex','cp','tx'
        ];

        $projectExt = SlCommonUtil::setDateBlank(DBUtil2::getOne(ImsDBName::PROJECT_EXT, "projectSno", $projectSno));
        foreach($map as $mapKey => $mapValue){
            $isEmpty = true;
            foreach ($map2 as $prefix){
                if(!empty($projectExt[$prefix.ucfirst($mapKey)])){
                    $isEmpty = false;
                }
            }
            if($isEmpty){
                DBUtil2::update(ImsDBName::PROJECT_EXT, ['tx'.ucfirst($mapKey) => '해당없음' ], new SearchVo('sno=?', $projectExt['sno']));
            }
        }
    }

    public function getCopyEworkField() {
        return "styleSno,
             warnMain,
             prdFabricInfo,
             filePrd,
             fileMain,
             fileBatek,
             fileAi,
             fileMarkAi,
             fileCareAi,
             fileMark1,
             fileMark2,
             fileMark3,
             fileMark4,
             fileMark5,
             fileMark6,
             fileMark7,
             fileMark8,
             fileMark9,
             fileMark10,
             fileMarkPosition7,
             fileMarkPosition8,
             fileMarkPosition9,
             fileMarkPosition10,
             fileMarkPosition1,
             fileMarkPosition2,
             fileMarkPosition3,
             fileMarkPosition4,
             fileMarkPosition5,
             fileMarkPosition6,
             filePosition,
             fileCare,
             fileSpec,
             filePacking1,
             filePacking2,
             filePacking3,
             markInfo1,
             markInfo2,
             markInfo3,
             markInfo4,
             markInfo5,
             markInfo6,
             markInfo7,
             markInfo8,
             markInfo9,
             markInfo10,
             warnMaterial,
             warnBatek,
             warnMark,
             warnPosition,
             warnSpec,
             warnPacking,
             specData,
             beforeSpecData,
             usePacking,
             useMark,
             useBatek,
             produceWarning";
    }

    /**
     * 프로젝트 분기
     * @param $projectSno
     * @param array $prdSnoList
     * @throws \Exception
     */
    public function divideProject($projectSno, array $prdSnoList){
        $projectSchemaList = DBTableField::tableImsProject();
        $field = [];
        $exclude = [
            'sno', 'regDt', 'modDt', 'projectSno' , 'projectNo'
        ];
        foreach($projectSchemaList as $projectSchema){
            if( !in_array($projectSchema['val'], $exclude) ){
                $field[] = $projectSchema['val'];
            }
        }
        $projectField = implode(',',$field);
        $sql = "insert into sl_imsProject( {$projectField}) select {$projectField} from sl_imsProject where sno = {$projectSno} "; //Insert Sno 를 모른다. Last 조회 ?
        DBUtil2::runSql($sql);
        $newProjectSno = DBUtil2::runSelect('SELECT LAST_INSERT_ID() as sno')[0]['sno'];
        $this->movePrd($newProjectSno, $prdSnoList);
    }

    /**
     * 상품 이동
     * @param $newProjectSno
     * @param $prdSnoList
     * @throws \Exception
     */
    public function movePrd($newProjectSno, $prdSnoList){
        $relationTableList = [
            'sl_imsProjectProduct',
            'sl_imsFabric',
            'sl_imsFile',
            'sl_imsStatusHistory',
            'sl_imsSample',
            'sl_imsFabricRequest',
            'sl_imsFabricReqHistory',
            'sl_imsEstimate',
            'sl_imsProduction',
            'sl_imsTodoRequest',
            'sl_imsAddInfo'
        ];

        foreach($prdSnoList as $prdSno){
            foreach($relationTableList as $tableName){
                DBUtil2::update($tableName, [
                    'projectSno' => $newProjectSno
                ],new SearchVo('sno=?',$prdSno));
            }
        }
    }

}



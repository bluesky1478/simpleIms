<?php

namespace Component\Imsv2;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Member\Manager;
use Component\Sms\Code;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Component\Scm\ScmAsianaTrait;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Mail\MailService;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 프로젝트 스케쥴 유틸
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsScheduleUtil
{
    /**
     * 일자 지나면 자동 완료 처리
     */
    public static function setCpMeeting(){
        $sql = "
        UPDATE sl_imsProjectExt
        SET cpMeeting = DATE(STR_TO_DATE(TRIM(exMeeting), '%Y-%m-%d'))
        WHERE NULLIF(TRIM(exMeeting), '') IS NOT NULL
          AND TRIM(exMeeting) <> '0000-00-00'
          AND STR_TO_DATE(TRIM(exMeeting), '%Y-%m-%d') IS NOT NULL
          AND DATE(STR_TO_DATE(TRIM(exMeeting), '%Y-%m-%d')) < CURDATE()
          AND (cpMeeting IS NULL OR cpMeeting = '0000-00-00')
        ";
        DBUtil2::runSql($sql);
    }

    /**
     * 완료일 자동 등록 스케쥴
     * @return array
     */
    public static function getAutoScheduleKey(){
        foreach(ImsScheduleConfig::SCHEDULE_LIST as $key => $value){
            if('y' === $value['auto']){
                $schedule[] = $key;
            }
        }
        return $schedule;
    }

    /**
     * 스케쥴 키맵 반환
     * @return array
     */
    public static function getScheduleMap(){
        foreach(ImsScheduleConfig::SCHEDULE_LIST as $key => $value){
            $schedule[$key] = $value['name'];
        }
        return $schedule;
    }

    /**
     * 데드라인 등 설정 (나중에 DB보고 모두 설정 가능하게?)
     * @return array
     */
    public static function getScheduleConfig(){
        return SlCommonUtil::arrayAppKey(DBUtil2::getList('sl_imsScheduleConfig', '1', '1'),'scheduleType');
    }

    /**
     * 스케쥴 반환
     */
    public static function getScheduleList(){
        $rslt = [];
        foreach(ImsScheduleConfig::SCHEDULE_LIST as $key => $value){
            if('y'===$value['prep']){
                //사전 스케쥴
                foreach($value['dept'] as $dept){
                    $prepKey = 'prep_'.$dept;
                    $rslt[$prepKey][$key] = $value['name'];
                }
            }else if('y'===$value['main']){
                //메인 스케쥴
                $rslt['summary'][$key] = $value['name'];
            }else{
                //각 부서 스케쥴
                foreach($value['dept'] as $dept){
                    $rslt[$dept][$key] = $value['name'];
                }
            }
        }
        return $rslt;
    }

    /**
     * 영업 사전 스케쥴 반환
     */
    public static function getSalesPrepSchedule(){
        $rslt = [];
        foreach(ImsScheduleConfig::SCHEDULE_LIST as $key => $value){
            if('y'===$value['prep'] && 's' === $value['dept']){
                $rslt[$key] = $value['name'];
            }
        }
        return $rslt;
    }


    /**
     * 프로젝트 스케쥴 상태 최신화 (완료일이 있으면 완료이다.)
     * @param $projectSno
     * @throws \Exception
     */
    public static function setProjectScheduleStatus($projectSno){
        //스케쥴 데이터 가져오기
        $scheduleData = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectSno);
        $scheduleList = ImsScheduleUtil::getScheduleMap();
        foreach($scheduleList as $scheduleName => $scheduleNameKr){
            $method = SlCommonUtil::getMethodMap(__CLASS__);
            $methodName = 'setCheck'.ucfirst($scheduleName); //ex. setCheckPlan, setCheckProposal ...
            if( !empty($method[$methodName]) ){
                ImsScheduleUtil::$methodName($scheduleData,$scheduleName);
            }else{
                ImsScheduleUtil::setCheckBasic($scheduleData,$scheduleName);
            }
        }
    }

    /**
     * 사양서 확정 처리
     */
    public static function setCheckOrderConfirm(){
        //사양서 확정에 대해서는 아무처리 안함
    }

    /**
     *
     */
    public static function setCheckAssortConfirm(){
        //발송이력이 있으면 4 : 여기선 아무작업도 하지 않는다.
    }

    /**
     * 추가 담당자 설정
     * @param $projectSno
     * @param $params
     * @throws \Exception
     */
    public static function setAddManager($projectSno, $params){
        $scheduleList = ImsScheduleUtil::getScheduleMap();
        //추가 대상 저장
        foreach($scheduleList as $scheduleKey => $schedule){
            foreach($params['project'][$scheduleKey.'AddManager'] as $addManager){
                if( empty($addManager['sno']) ){
                    $insertSearch =  new SearchVo('projectSno=?', $projectSno);
                    $insertSearch->setWhere('scheduleDiv=?');
                    $insertSearch->setWhereValue($scheduleKey);
                    $insertSearch->setWhere('managerSno=?');
                    $insertSearch->setWhereValue($addManager['managerSno']);
                    if( 0 >= DBUtil2::getCount(ImsDBName::PROJECT_MANAGER , $insertSearch)){
                        DBUtil2::insert(ImsDBName::PROJECT_MANAGER, [
                            'projectSno'  => $projectSno,
                            'scheduleDiv' => $scheduleKey,
                            'managerSno'  => $addManager['managerSno'],
                        ]);
                    }
                }
            }
        }
        $projectManagerList = DBUtil2::getList(ImsDBName::PROJECT_MANAGER, 'projectSno', $projectSno);
        $beforeAddManagerList = [];
        foreach($projectManagerList as $manager){
            $beforeAddManagerList[$manager['scheduleDiv']][$manager['managerSno']] = 'y';
        }
        foreach($scheduleList as $scheduleKey => $schedule){
            foreach($params['project'][$scheduleKey.'AddManager'] as $addManager){
                unset($beforeAddManagerList[$scheduleKey][$addManager['managerSno']]);
            }
        }
        foreach( $beforeAddManagerList as $scheduleKey => $deleteManager ){
            $managerSnoList = array_keys($deleteManager);
            if(count($managerSnoList)>0){
                $managerSnoListStr = implode(',',$managerSnoList);
                $searchVo = new SearchVo(" managerSno IN ({$managerSnoListStr}) AND projectSno={$projectSno} AND scheduleDiv=?",$scheduleKey);
                DBUtil2::delete(ImsDBName::PROJECT_MANAGER, $searchVo);
            }
        }
    }


    //-- 조건에 따른 자동 상태 설정 ------

    /**
     * 기획서 스케쥴 상태 체크
     * @param $scheduleData
     * @param $scheduleName
     * @throws \Exception
     */
    public static function setCheckPlan($scheduleData, $scheduleName){
        $file = DBUtil2::getCount(ImsDBName::PROJECT_FILE, new SearchVo("fileDiv='filePlan' and projectSno=?",$scheduleData['projectSno']));
        if( $file > 0 ){
            $imsService = SlLoader::cLoad('ims', 'imsService');
            $params['projectSno'] = $scheduleData['projectSno'];
            $params['approvalType'] = 'plan';
            $approvalData = $imsService->getApprovalData($params);

            if(!empty($approvalData)){
                if( 'accept' === $approvalData['approvalStatus']){
                    //파일 있음 , 결재 완료
                    $updateData['stPlan'] = 10;
                }else{
                    //파일 있음 , 결재 있음 - 미완료
                    $updateData['stPlan'] = 2;
                    $updateData['cpPlan'] = '0000-00-00';
                }
            }else{
                //파일 있음 , 결재 없음
                $updateData['stPlan'] = 1;
                $updateData['cpPlan'] = '0000-00-00';
            }
        }else{
            if(!empty($scheduleData['cpPlan']) && '0000-00-00' !== $scheduleData['cpPlan']){
                //PASS
                $updateData['stPlan'] = 9; //파일은 없는데 완료일이 있음
            }else{
                //파일 없음
                $updateData['stPlan'] = 0;
                $updateData['cpPlan'] = '0000-00-00';
            }
        }
        DBUtil2::update(ImsDBName::PROJECT_EXT, $updateData, new SearchVo('projectSno=?', $scheduleData['projectSno']));
    }

    /**
     * 제안서 처리
     * @param $scheduleData
     * @param $scheduleName
     * @throws \Exception
     */
    public static function setCheckProposal($scheduleData, $scheduleName){
        $file = DBUtil2::getCount(ImsDBName::PROJECT_FILE, new SearchVo("fileDiv='fileProposal' and projectSno=?",$scheduleData['projectSno']));
        if( $file > 0 ){
            $imsService = SlLoader::cLoad('ims', 'imsService');
            $params['projectSno'] = $scheduleData['projectSno'];
            $params['approvalType'] = 'proposal';
            $approvalData = $imsService->getApprovalData($params);

            if(!empty($approvalData)){
                if( 'accept' === $approvalData['approvalStatus']){
                    //파일 있음 , 결재 완료
                    $updateData['stProposal'] = 10;
                }else{
                    //파일 있음 - 결재 있음 - 미완료
                    $updateData['stProposal'] = 2;
                    $updateData['cpProposal'] = '0000-00-00';
                }
            }else{
                //파일 있음 , 결재 없음
                $updateData['stProposal'] = 1;
                $updateData['cpProposal'] = '0000-00-00';
            }
        }else{
            if(!empty($scheduleData['cpProposal']) && '0000-00-00' !== $scheduleData['cpProposal']){
                //PASS
                $updateData['stProposal'] = 9; //파일은 없는데 완료일이 있음
            }else{
                //파일 없음
                $updateData['stProposal'] = 0;
                $updateData['cpProposal'] = '0000-00-00';
            }
        }
        DBUtil2::update(ImsDBName::PROJECT_EXT, $updateData, new SearchVo('projectSno=?', $scheduleData['projectSno']));
    }

    /**
     * 작지 상태 처리
     * @param $scheduleData
     * @param $scheduleName
     * @throws \Exception
     */



    /**
     * 완료일 있으면 단순 완료 처리
     * @param $scheduleData
     * @param $scheduleName
     * @throws \Exception
     */
    public static function setCheckBasic($scheduleData, $scheduleName){
        $scheduleSuffix = ucfirst($scheduleName);
        $updateData = [];
        $isUpdate = false;
        if( '0000-00-00' != $scheduleData['cp'.$scheduleSuffix] && !empty($scheduleData['cp'.$scheduleSuffix]) ){
            if(9 > $scheduleData['st'.$scheduleSuffix]){ //이미 PASS나 완료 면 업데이트 하지 않는다.
                $updateData['st'.$scheduleSuffix] = 10;
                $isUpdate = true;
            }
        }else{
            //완료 날짜 없음 + 이미 상태가 PASS 또는 완료
            if($scheduleData['st'.$scheduleSuffix] >= 9) { //이미 PASS나 완료 면 업데이트 하지 않는다.
                $updateData['st'.$scheduleSuffix] = 0;
                $isUpdate = true;
            }
        }
        if( $isUpdate && $scheduleData['st'.$scheduleSuffix] != $updateData['st'.$scheduleSuffix] ){
            DBUtil2::update(ImsDBName::PROJECT_EXT, $updateData, new SearchVo('projectSno=?', $scheduleData['projectSno']));
        }
    }

    /**
     * 스케쥴 상태 변경
     * @param $projectSno
     * @param $type
     * @param string $completeDt
     * @throws \Exception
     */
    public static function setScheduleCompleteDt($projectSno, $type, $completeDt='0000-00-00'){
        /*SitelabLogger::log('스케쥴 상태 변경');
        SitelabLogger::log($projectSno);
        SitelabLogger::log('cp'.ucfirst($type));
        SitelabLogger::log($completeDt);
        SitelabLogger::log($type);*/
        $rslt = DBUtil2::update(ImsDBName::PROJECT_EXT, ['cp'.ucfirst($type)=>$completeDt], new SearchVo('projectSno=?', $projectSno));
        //SitelabLogger::log('업데이트 결과 : '. $rslt);
    }

}
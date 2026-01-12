<?php
namespace SiteLabUtil;

use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\StatusValidService;
use Exception;
use SlComponent\Database\DBUtil2;
use SlComponent\Mail\SiteLabMailMessage;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * IMS 상태 관리 유틸 (바로 체크할 수 있게 하기 위해 이동)
 * Class ImsStatusUtil
 * @package SiteLabUtil
 */
class ImsStatusUtil {

    /**
     * IMS 프로젝트 상태 변경 및 변경 제약 처리
     * @param $params
     * @param false $checkCurrentStatus
     * @return false|mixed
     * @throws \Exception
     */
    public static function setStatus($params, $checkCurrentStatus=false)
    {
        //$beforeData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['projectSno']);
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $beforeData = $imsProjectService->getSimpleProject($params['projectSno']);

        //현재 상태가 더 크면 업데이트 하지 않는다. (체크를 요청했을 때만, 기본은 체크하지 않는다.)
        if($checkCurrentStatus && $beforeData['projectStatus'] >= $params['projectStatus']){ // 31 - 40
            return false;
        }

        //발주완료 처리시 체크 (생산 스케쥴 자동 등록)
        if( 90 != $beforeData['projectStatus'] && 90 == $params['projectStatus'] ){
            $errMsg = StatusValidService::checkOrderComplete( $beforeData , [] , [] );
            if( !empty($errMsg) ){
                throw new \Exception($errMsg); //강제값 오류 처리
            }
            $params['reason'] = \Session::get('manager.managerNm');
        }else if( 31 > $beforeData['projectStatus'] && $params['projectStatus'] >= 31 //기획서 이상으로 변경
            && empty(ImsCodeMap::PROJECT_TYPE_R[$beforeData['projectType']])){ //변경전 상태가 더 작아야 한다.

            if( 'p' !== $beforeData['proposalConfirm']  ){
                throw new Exception('제안서가 승인 또는 PASS 되지 않았습니다.');
            }
        }else if( 30 > $beforeData['projectStatus'] && $params['projectStatus'] >= 30 //기획서 체크 ( 제안으로 변경할 때,  기존 상태가 더 크면 체크하지 않는다 )
            && empty(ImsCodeMap::PROJECT_TYPE_R[$beforeData['projectType']]) //리오더는 해당 없음
        ){ //변경전 상태가 더 작아야 한다.
            if( 'p' !== $beforeData['planConfirm']  ){
                throw new Exception('기획서가 승인 또는 PASS 되지 않았습니다.');
            }
        }else if( 20 > $beforeData['projectStatus'] && $params['projectStatus'] >= 20 //영업 기획서 체크 ( 기획으로 변경할 때,  기존 상태가 더 크면 체크하지 않는다 )
            && empty(ImsCodeMap::PROJECT_TYPE_R[$beforeData['projectType']]) //리오더는 해당 없음
        ){ //변경전 상태가 더 작아야 한다.
            if( 'p' !== $beforeData['salesPlanApproval']  ){
                throw new Exception('영업 기획서가 승인되지 않았습니다.');
            }
        }

        //상태변경 히스토리 저장
        ImsStatusUtil::saveStatusHistory([
            'projectSno' => $params['projectSno'],
            'beforeStatus' => $beforeData['projectStatus'],
            'afterStatus' => $params['projectStatus'],
            'reason' => $params['reason'],
            'regManagerSno' => \Session::get('manager.sno'),
        ]);
        
        //후처리 
        //사전 영업으로 변경시 후처리 ( 영업 + 디자인 공유 )
        if( 15 > $beforeData['projectStatus'] && $params['projectStatus'] >= 15 //기획서 이상으로 변경
            && empty(ImsCodeMap::PROJECT_TYPE_R[$beforeData['projectType']]) //리오더는 해당 없음
        ){
            //디자인실 참여
            $designJoin = [];
            foreach(\Component\Ims\ImsCodeMap::DESIGN_JOIN_TYPE as $k => $v){
                if( $beforeData['designTeamInfo'] & $k ){
                    $designJoin[] = $v;
                }
            }
            //생산팀 + 디자인팀+ 대표님에 발송
            $managerList1 = SiteLabMailUtil::getTeamMail( ImsCodeMap::TEAM_SALES );
            $managerList2 = SiteLabMailUtil::getTeamMail( ImsCodeMap::TEAM_DESIGN );
            SiteLabMailUtil::sendSystemKeyMail(SiteLabMailMessage::STATUS15, [
                'to' => implode(',',array_merge($managerList1, $managerList2, ['jhseo@msinnover.com'])), //대표님까지.
                'projectSno' => $params['projectSno'],
                'customerName' => $beforeData['customerName'],
                'industry' => $beforeData['bizCate1'].' '.$beforeData['bizCate2'],
                'contact' => $beforeData['contactName'].' '.$beforeData['contactMobile'].' '.$beforeData['contactEmail'],
                'designJoin' => implode(', ', $designJoin),
                'exMeeting' => $beforeData['exMeeting'],
                'salesMemo' => $beforeData['salesMemo'],
                'salesManagerNm' => $beforeData['salesManagerNm'],
            ]);
        }

        //상태변경하기
        return DBUtil2::updateBySno(ImsDBName::PROJECT, ['projectStatus' => $params['projectStatus']], $params['projectSno']);
    }


    /**
     * 상태 이력 저장
     * @param $saveParam
     */
    public static function saveStatusHistory($saveParam){
        if( !empty($saveParam['projectSno']) && empty($saveParam['customerSno']) ){
            $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $saveParam['projectSno']);
            $saveParam['customerSno'] = $projectData['customerSno'];
        }
        DBUtil2::insert(ImsDBName::STATUS_HISTORY, $saveParam);
    }
    
}

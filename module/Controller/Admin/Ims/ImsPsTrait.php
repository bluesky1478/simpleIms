<?php

namespace Controller\Admin\Ims;

use App;
use Component\Database\DBTableField;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\MemberSnsService;
use Component\Member\MemberValidation;
use Component\Member\Util\MemberUtil;
use Component\Policy\SnsLoginPolicy;
use Component\SiteLink\SiteLink;
use Component\Storage\Storage;
use Controller\Front\Controller;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertReloadException;
use Logger;
use Request;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * TO_DO List 관련 Trait
 */
trait ImsPsTrait {

    public function getMyService(){
        return $this;
    }

    public function delayTest($params){
        sleep(5);
        return ['msg'=>'조회 완료'];
    }

    /**
     * 기본 구조 가져오기
     * @param $params
     * @return array
     */
    public function getSchema($params){
        $fncName = 'getSchema'.ucfirst($params['target']);
        return ['data'=>$this->imsService->$fncName($params),'msg'=>'조회 완료'];
    }

    /**
     * target 리스트 가져오기
     * @param $params
     * @return array
     */
    public function getList($params){
        //SitelabLogger::logger2(__METHOD__, 'targetList 가져오기');
        //SitelabLogger::logger2(__METHOD__, $params);
        $fncName = 'getList'.ucfirst($params['target']);
        return ['data'=>$this->imsService->$fncName($params),'msg'=>'조회 완료'];
    }

    /**
     * 프로젝트 상품 리스트
     * @param $params
     * @return array
     */
    public function getProjectProduct($params){
        return ['data'=>$this->imsService->getProductList($params),'msg'=>'조회 완료'];
    }

    /**
     * target 데이터 가져오기
     * @param $params
     * @return array
     */
    public function getProductData($params){
        return ['data'=>$this->imsService->getProductData($params),'msg'=>'조회 완료'];
    }

    /**
     * target 데이터 삭제
     * @param $params
     * @return array
     */
    public function deleteData($params){
        $fncName = 'delete'.ucfirst($params['target']);
        return ['data'=>$this->imsService->$fncName($params),'msg'=>'삭제 완료'];
    }

    /**
     * 실시간 저장 (sl_ims 한정)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveRealTime($params){
        $dbName = 'sl_ims'.ucfirst($params['target']);
        if(!empty($params['target']) && !empty($params['updateField']) && !empty($params['key']) && !empty($params['keyValue']) ){
            $updateData = [$params['updateField'] => $params['updateData']];
            $updateData = DBTableField::setJsonField($dbName, $updateData, 'encode');

            if( 'y' === $params['dataMerge'] ){
                DBUtil2::merge($dbName, $updateData ,new SearchVo($params['key'].'=?', $params['keyValue']));
            }else{
                DBUtil2::update($dbName, $updateData ,new SearchVo($params['key'].'=?', $params['keyValue']));
            }
        }else{
            throw new Exception('update 필요 인자 부족');
        }

        //Assort 변경시 처리
        if('projectProduct' === $params['target'] && 'assort' === $params['updateField']){
            $this->imsStyleService->updateTotalQty(['sno'=>$params['keyValue']]); //TODO ProjectSno 가 필요하다.
        }

        return ['data'=>$params,'msg'=>'저장 완료'];
    }
    
    /**
     * 프로젝트 등록 ( 구 버전, 너무 많은 커플링 )
     * @param $params
     * @return array
     */
    public function saveProject($params){
        $sno = $this->imsService->saveProject($params['saveCustomer'], $params['saveProject'], $params['saveMeeting']);
        return ['data'=>[
            'inputData' => $params,
            'sno' => $sno,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 상품 등록
     * @param $params
     * @return array
     */
    public function saveProduct($params){
        $sno = $this->imsService->saveProduct($params['saveData']);
        return ['data'=>[
            'inputData' => $params,
            'sno' => $sno,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 고객사 등록
     * @param $params
     * @return array
     */
    public function saveCustomer($params){
        $sno = $this->imsService->saveCustomer($params['saveData']);
        return ['data'=>[
            'inputData' => $params,
            'sno' => $sno,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 생산선행 요청 등록
     * @param $params
     * @return array
     */
    public function savePreparedReq($params){
        $sno = $this->imsService->savePreparedReq($params['saveData']);
        return ['data'=>[
            'inputData' => $params,
            'sno' => $sno,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 파일 등록
     * @param $params
     * @return array
     */
    public function saveProjectFiles($params){
        $returnData = $this->imsService->saveProjectFiles($params['saveData']);
        return ['data'=>$returnData,'msg'=>'저장 완료'];
    }

    /**
     * 상태변경
     * @param $params
     * @return array
     */
    public function setStatus($params){
        $this->imsService->setStatus($params);
        return ['data'=> $params,'msg'=>'변경 완료'];
    }

    /**
     * 생산등록 (임시적 혹은 테스트)
     * 실제 생산관리 단계로 넘기거나 , 가발주 처리완료 되어야 등록된다. (같은 프로젝트 다시 등록하려면 등록 불가 1:1 )
     * @param $params
     * @return array
     */
    public function addProduce($params){
        $this->imsProduceService->addProduce($params);
        return ['data'=> $params,'msg'=>'추가 완료'];
    }

    public function saveProduce($params){
        $this->imsProduceService->saveProduce($params);
        return ['data'=> $params,'msg'=>'저장 완료'];
    }

    /**
     * 생산 Step 변경.
     * @param $params
     * @return array
     */
    public function setBatchProduceChangeStep($params){
        $this->imsProduceService->setBatchProduceChangeStep($params);
        return ['data'=> $params,'msg'=>'요청 완료'];
    }

    /**
     * 생산 단계별 컨펌
     * @param $params
     * @return array
     */
    public function setPrdStepConfirm($params){
        $stepNo = SlCommonUtil::getOnlyNumber($params['prdStep']);
        if( strpos(ImsCodeMap::PRODUCE_STEP_MAP[$stepNo],'ⓒ')!==false  ){
            $data = $this->imsProduceService->setPrdStepConfirm($params);
        }else{
            $data = false;
        }
        return ['data'=> $data,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 승인 처리
     * @param $params
     * @return array
     */
    public function setAccept($params){
        $data = $this->imsService->setAccept($params);
        return ['data'=> $data,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 및 기타 승인 처리
     * @param $params
     * @return array
     */
    public function setNewAccept($params){
        $this->imsService->setNewAccept($params);
        return ['data'=> $params,'msg'=>'저장 완료'];
    }

    /**
     * 원단 기본 스킴
     * @param $params
     * @return array
     */
    public function getFabricSchema($params){
        $schema = ImsJsonSchema::FABRIC_INFO;
        try{
            if(empty($params['no'])){
                $schema['no'] = 'G';
            }else if( 0 >= $params['index'] ){
                $schema['no'] = 'A';
            }else{
                /*$ord = ord($params['no']);
                if( ($ord >= 65 && 90 > $ord) || ($ord >= 97 && 122 > $ord) ){
                    $schema['no'] = chr(ord($ord)+1);
                }*/
            }
        }catch(Exception $e){}
        return ['data'=> $schema,'msg'=>'조회 완료'];
    }

    /**
     * 부자재 기본 스킴
     * @param $params
     * @return array
     */
    public function getSubFabricSchema($params){
        $schema = ImsJsonSchema::FABRIC_INFO;
        if(empty($params['no'])){
            $schema['no'] = 'A';
        }else{
            /*$ord = ord($params['no']);
            if( ($ord >= 65 && 90 > $ord) || ($ord >= 97 && 122 > $ord) ){
                $schema['no'] = chr($ord+1);
            }*/
            //gd_debug(chr(ord('A')+1));
        }

        return ['data'=> $schema,'msg'=>'조회 완료'];
    }

    /**
     * 디자인 정보 저장
     * @param $params
     * @return array
     */
    public function saveDesignData($params){
        $rslt = $this->imsService->saveDesignData($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 개별데이터 저장
     * @param $params
     * @return array
     */
    public function saveProjectEachData($params){
        $this->imsService->saveProjectEachData($params);
        return ['data'=> $params,'msg'=>'조회 완료'];
    }

    /**
     * 파일정보 가져오기
     * @param $params
     * @return array
     */
    public function loadFile($params){
        return ['data'=> $this->imsService->loadFile($params),'msg'=>'조회 완료'];
    }

    /**
     * 샘플 수량 카운트 +1
     * @param $params
     * @return array
     */
    public function addSampleCount($params){
        //$saveData['sno'] = $params['sno'];
        //$saveData['sampleCount'] = $params['count']+1;
        //$this->imsService->save(ImsDBName::PROJECT, $saveData);
        return ['data'=> $saveData,'msg'=>'처리 완료'];
    }

    /**
     * 파일 업로드 후 완료일자 업데이트
     * @param $params
     * @return array
     */
    public function setCompleteDt($params){
        $this->imsService->setCompleteDt($params);
        return ['data'=> $this->imsService->getProject(['sno'=>$params['sno']]),'msg'=>'처리 완료'];
    }


    /**
     * 가견적 정보 업데이트
     * @param $params
     * @return array
     */
    public function setEstimate($params){
        $this->imsService->setEstimate($params);
        return ['data'=> $this->imsService->getProject(['sno'=>$params['projectSno']]),'msg'=>'처리 완료'];
    }


    /**
     * 고객 정보 일괄 업로드
     * @param $params
     * @return array
     */
    public function saveBatchCustomer($params){
        $imsBatchService = SlLoader::cLoad('ims', 'imsBatchService');
        $filesValue = Request::files()->toArray();
        $imsBatchService->saveBatchCustomer($params, $filesValue);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 프로젝트 정보 일괄 업로드
     * @param $params
     * @return array
     */
    public function saveBatchProject($params){
        $imsBatchService = SlLoader::cLoad('ims', 'imsBatchService');
        $filesValue = Request::files()->toArray();
        $imsBatchService->saveBatchProject($params, $filesValue);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 생산 정보 일괄 업로드
     * @param $params
     * @return array
     */
    public function saveBatchProduce($params){
        $imsBatchService = SlLoader::cLoad('ims', 'imsBatchService');
        $filesValue = Request::files()->toArray();
        $imsBatchService->saveBatchProduce($params, $filesValue);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 프로젝트 복사
     * @param $params
     * @return array
     */
    public function copyProject($params){
        return ['data'=> $this->imsService->copyProject($params),'msg'=>'처리 완료'];
    }

    /**
     * 스타일 삭제
     * @param $params
     * @return array
     */
    public function goTrashProduct($params){
        $this->imsService->goTrashProduct($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }


    /**
     * 스타일 일괄 견적 요청
     * @param $params
     * @return array
     */
    public function goBatchEstimate($params){
        $this->imsService->goBatchEstimate($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스타일 복원
     * @param $params
     * @return array
     */
    public function recoveryProduct($params){
        $this->imsService->recoveryProduct($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스타일 영구 삭제
     * @param $params
     * @return array
     */
    public function deleteProduct($params){
        $this->imsService->deleteProduct($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스타일 복사
     * @param $params
     * @return array
     */
    public function copyProduct($params){
        $this->imsService->copyProduct($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스타일 다른 프로젝트에 복사
     * @param $params
     * @return array
     * @throws Exception
     */
    public function copyProductToTargetProject($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['projectSno']);
        if(empty($projectData)){
            throw new Exception('대상 프로젝트를 찾을 수 없습니다.');
        }else{
            foreach($params['prdSnoList'] as $prdSno){
                $imsProjectService->copyStyle($prdSno, $params['projectSno']);
            }
        }
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    public function eachUpdate($params){
        //SitelabLogger::logger($params);
        $tableName = $params['tableName'];
        unset($params);
        //$this->imsService->save($tableName,$params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스케쥴 업데이트
     * @param $params
     * @return array
     * @throws Exception
     */
    public function scheduleUpdate($params){
        //SitelabLogger::logger('스케쥴 업데이트 체크');
        //SitelabLogger::logger($params);
        $imsProduceService = SlLoader::cLoad('ims', 'imsProduceService');
        $data = $imsProduceService->getProduceData($params['sno']);

        $updatePrdStep = [];
        foreach(ImsCodeMap::PRODUCE_STEP_MAP as $stepKey => $stepName){
            $prdStepKey = 'prdStep'.$stepKey;
            $prdStep = $data[$prdStepKey];
            $prdStep['expectedDt'] = $params[$prdStepKey]['expectedDt'];
            $prdStep['completeDt'] = $params[$prdStepKey]['completeDt'];
            $prdStep['memo'] = $params[$prdStepKey]['memo'];
            $updatePrdStep[$prdStepKey] = $prdStep;
        }
        $saveData['sno'] = $data['sno'];
        $saveData['prdStep'] = json_encode($updatePrdStep);

        if(!empty($params['produceMemo'])) {
            $this->saveComment([
                'commentDiv' => 'produce',
                'comment' => $params['produceMemo'],
                'projectSno' => $params['sno'],
                'isShare' => 'n',
            ]);
        }

        $imsProduceService->save(ImsDBName::PRODUCE,$saveData);

        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 코멘트 저장 (어드민)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveComment($params){
        $saveData = $params;
        unset($saveData['mode']);

        if( !empty($saveData['sno']) ){
            DBUtil2::update(ImsDBName::PROJECT_COMMENT, [
                'comment' => $saveData['comment']
            ], new SearchVo('sno=?', $saveData['sno']));
        }else{
            //CommentDiv가 없으면 자동으로 채운다.
            $saveData['regManagerSno'] = \Session::get('manager.sno');
            $saveData['commentCnt'] = 1;
            if(empty($params['commentDiv'])){
                $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['projectSno']);
                $saveData['commentDiv'] = ImsCodeMap::PROJECT_STEP_COMMENT_DIV[$projectData['projectStatus']];
            }
            DBUtil2::insert(ImsDBName::PROJECT_COMMENT, $saveData);
        }

        if( 'produce' === $saveData['commentDiv'] ){
            $commentList = $this->imsService->getProjectComment($params['projectSno'], 'produce');
        }else{
            $commentList = $this->imsService->getProjectComment($params['projectSno']);
        }

        if( !empty($params['projectSno']) && !empty($params['commentDiv']) && array_key_exists($params['commentDiv'],ImsCodeMap::PROJECT_ADD_INFO) ){
            $commentCount = DBUtil2::getCount(ImsDBName::PROJECT_COMMENT, new SearchVo(" projectSno = {$params['projectSno']} and commentDiv=?", $params['commentDiv']));
            DBUtil2::update(ImsDBName::PROJECT_ADD_INFO,['commentCnt'=>$commentCount], new SearchVo(" projectSno = {$params['projectSno']} and fieldDiv=?", $params['commentDiv']));
        }

        //프로젝트 카운트 저장
        if(!empty($params['projectSno'])){
            $countData = ImsUtil::getProjectCommentCount($params['projectSno']);
            $rslt =  DBUtil2::update(ImsDBName::PROJECT_EXT, [
                'commentCount'  => json_encode($countData),
            ], new SearchVo('projectSno=?', $params['projectSno']));
        }

        return ['data'=> $commentList,'msg'=>'저장 완료'];
    }

    /**
     * 메모 삭제
     * @param $params
     * @return array
     * @throws Exception
     */
    public function deleteComment($params){
        DBUtil2::delete(ImsDBName::PROJECT_COMMENT, new SearchVo('sno=?', $params['sno']));
        return ['data'=> $params,'msg'=>'삭제 완료'];
    }


    /**
     * 요청사항 처리
     * @param $params
     * @return array
     */
    public function setCompleteQb($params){
        $this->imsService->setCompleteQb($params);
        return ['data'=> 'OK','msg'=>'저장 완료'];
    }

    public function setRevokeQb($params){
        $this->imsService->setRevokeQb($params);
        return ['data'=> 'OK','msg'=>'저장 완료'];
    }

    /**
     * 간단 저장
     * @param $params
     * @return string[]
     */
    public function simpleSave($params){
        $this->imsService->simpleSave($params);
        return ['data'=> 'OK','msg'=>'저장 완료'];
    }

    /**
     * @param $params
     * @return array
     */
    public function saveMeeting($params){
        $sno = $this->imsService->saveNewMeeting($params);
        return ['data'=>[
            'inputData' => $params,
            'sno' => $sno,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 디자인 정보 저장
     * @param $params
     * @return array
     */
    public function saveInline($params){
        $rslt = $this->imsService->saveInline($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 최신 파일 가져오기
     * @param $params
     * @return array
     */
    public function getLatestFileList($params){
        $rslt = $this->imsService->getLatestFileList($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 원단 요청 양식
     * @param $params
     */
    public function downloadFabricRequestForm($params){
        $this->imsService->downloadFabricRequestForm($params);
    }

    /**
     * 생산 개별 코멘트
     * @param $params
     * @return array
     */
    public function saveProductionComment($params){
        $this->imsService->saveProductionComment($params);
        return ['data'=> $params,'msg'=>'저장 완료'];
    }

    /**
     * 생산 심플 저장
     * @param $params
     * @return array
     */
    public function saveSimpleProduction($params){
        $this->imsService->saveSimpleProduction($params);
        return ['data'=> $params,'msg'=>'저장 완료'];
    }

    /**
     * 생산 개별 코멘트 가져오기
     * @param $params
     * @return array
     */
    public function getProductionCommentList($params){
        $list = $this->imsService->getProductionCommentList($params);
        return ['data'=> $list,'msg'=>'조회 완료'];
    }

    /**
     * 프로젝트 코멘트 리스트
     * @param $params
     * @return array
     */
    public function getProjectCommentList($params){
        $commentList = $this->imsService->getProjectComment($params['projectSno'], $params['commentType']);
        return ['data'=> $commentList,'msg'=>'조회 완료'];
    }

    /**
     * 프로젝트 리스트 수
     * @param $params
     * @return array
     */
    public function getProjectCommentCnt($params){
        //$cnt = DBUtil2::getCount(ImsDBName::PROJECT_COMMENT, new SearchVo('projectSno=?', $params['projectSno']));
        //$commentList = $this->imsService->getProjectComment($params['projectSno']);
        $commentList = DBUtil2::runSelect("
            select commentDiv, count(1) as cnt  from sl_imsComment where projectSno = {$params['projectSno']} group by commentDiv
        ");
        return ['data'=> $commentList,'msg'=>'조회 완료'];
    }

    /**
     * 상품별 견적가, 생산가 상태 가져온다.
     * @param $params
     * @return array
     */
    public function getEstimateCostStatus($params){
        $list = $this->imsService->getEstimateCostStatus($params);
        return ['data'=> $list,'msg'=>'조회 완료'];
    }

    /**
     * 프로젝트 저장 (필요한 정보만)
     * @param $params
     * @return array
     */
    public function saveSimpleProject($params){
        $this->imsService->saveSimpleProject($params['saveData']);
        return ['data'=>[
            'inputData' => $params,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 확장 정보 저장
     * @param $params
     * @return array
     */
    public function saveProjectExt($params){
        $this->imsService->saveProjectExt($params['saveData']);
        return ['data'=>[
            'inputData' => $params,
        ],'msg'=>'저장 완료'];
    }

    /**
     * 메인 데이터 저장
     * @param $params
     * @return string[]
     */
    public function saveData($params){
        $sno = $this->imsService->saveData($params);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 샘플 수 가져오기
     * @param $params
     * @return string[]
     */
    public function getSampleCountByProject($params){
        $sno = $this->imsService->getSampleCountByProject($params);
        return ['data'=> $sno,'msg'=>'조회 완료'];
    }

    /**
     * 협상 단계 메모 가져오기
     * @param $params
     * @return array
     */
    public function getNegoData($params){
        $data = $this->imsService->getNegoData($params);
        return ['data'=> $data,'msg'=>'조회 완료'];
    }

    /**
     * 프로젝트 리오더
     * @param $params
     * @return array
     */
    public function reOrderProject($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $sno = $imsProjectService->reOrder($params['projectSno'],50,date('y')); //리오더
        //$sno = $this->imsService->reOrderProject($params['projectSno'],$params['status']); //구버전 리오더
        return ['data'=> $sno,'msg'=>'리오더 프로젝트 생성완료'];
    }

    /**
     * 작지 파일 가져오기
     * @param $params
     * @return array
     */
    public function getProjectFile($params){
        $data =$this->imsService->getProjectFiles($params['projectSno']);
        return ['data'=> $data,'msg'=>'리오더 프로젝트 생성완료'];
    }

    /**
     * 고객 견적 발송
     * @param $params
     * @return array
     */
    public function sendCustomerEstimate($params){
        $this->imsCustomerEstimateService->sendCustomerEstimate($params);
        return ['data'=> $params,'msg'=>'견적서 발송 완료'];
    }

    /**
     * 고객 견적 승인
     * @param $params
     * @return array
     */
    public function setApprovalCustEstimate($params){
        $this->imsCustomerEstimateService->setApprovalCustEstimate($params);
        return ['data'=> $params,'msg'=>'승인 완료'];
    }

    /**
     * 원부자재 선적일 변경
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setFabricShipConfirmCompleteDt($params){
        DBUtil2::update(ImsDBName::PRODUCTION, ['fabricShipCompleteDt'=>'now()'], new SearchVo('sno=?',$params['sno']));
        return ['data'=> $params,'msg'=>'원부자재 선적일자를 현재일자로 변경했습니다.(변경내용은 화면 새로고침 후 확인 가능)'];
    }

    /**
     * 스타일 리스트 가져오기
     * @param $params
     * @return array
     */
    public function getListStyle($params){
        return ['data'=>$this->imsStyleService->getListStyle($params),'msg'=>'조회 완료'];
    }

    /**
     * 스타일 리스트 가져오기 (고객 리스트)
     * @param $params
     * @return array
     */
    public function getListStyleWithCustomerField($params){
        $rslt['list'] = $this->imsStyleService->getListStyle($params);
        $rslt['field'] = $this->imsStyleService->getDisplayCustomerStyle();
        return ['data'=>$rslt,'msg'=>'조회 완료'];
    }

    /**
     * 결재 후 처리
     * @param $params
     * @return array
     * @throws Exception
     */
    public function afterApprovalModify($params){
        //상태 변경
        $rslt = DBUtil2::update(ImsDBName::EWORK, ['mainApproval'=>$params['status']], new SearchVo('styleSno=?', $params['styleSno']));
        if( 'p' === $params['status'] ){
            $recordData['sno'] = $params['styleSno']; //StyleSNo
            $this->imsService->recordHistory('update', ImsDBName::EWORK, $recordData, ['<b class="sl-blue">▶▶▶ 완료 결재 임시 해제 원복</b>']); //임시 해재 원복 이력 기록
        }else{
            $recordData['sno'] = $params['styleSno']; //StyleSNo
            $this->imsService->recordHistory('update', ImsDBName::EWORK, $recordData, ['<b class="sl-blue">▶▶▶ 완료 결재 임시 해제 : '. $params['reason'].'</b>']); //임시 해제 이력 기록
        }
        return ['data'=>$rslt,'msg'=>'처리 완료'];
    }

    /**
     * 작지 저장
     * @param $params
     * @return array
     */
    public function saveEwork($params){
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        unset($params['eworkData']['data']['revision']); //리비전 업데이트 금지
        $eworkService->saveEworkWithPrdInfo($params);
        return ['data'=>$params,'msg'=>'저장 완료'];
    }

    /**
     * 비축 원부자재 리스트 가져오기
     * @param $params
     * @return array
     */
    public function getListStored($params){
        return ['data'=>$this->imsStoredService->getListStored($params),'msg'=>'조회 완료'];
    }

    /**
     * 전산작지 업로드
     * @param $params
     * @return array
     * @throws Exception
     */
    public function saveEworkUpload($params){
        DBUtil2::merge('sl_imsEwork', [
            'styleSno'=>$params['styleSno'],
            $params['type']=>$params['fileInfo']
        ], new SearchVo('styleSno=?',$params['styleSno']));

        //변경 이력 기록


        return ['data'=> $params,'msg'=>'저장되었습니다.'];
    }


    /**
     * 아소트 URL 전달 (인터페이스)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendAssortUrl($params){
        $this->imsStyleService->sendAssortUrl($params);
        return ['data'=> $params,'msg'=>'발송 되었습니다.'];
    }

    /**
     * 사양서 URL 전달 (인터페이스)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendOrderUrl($params){
        $this->imsStyleService->sendOrderUrl($params);
        return ['data'=> $params,'msg'=>'발송 되었습니다.'];
    }
    //제안서 URL 전달
    public function sendProposalUrl($params){
        $this->imsStyleService->sendProposalUrl($params);
        return ['data'=> $params,'msg'=>'발송 되었습니다.'];
    }

    /**
     * 프로젝트 키 전달
     * @param $params
     * @return array
     */
    public function getProjectKey($params){
        return ['data'=> SlCommonUtil::aesEncrypt($params['projectSno']),'msg'=>'조회 완료.'];
    }


    /**
     * 작지 원부자재 저장
     * @param $params
     * @return array
     */
    public function saveEworkFabric($params){
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        $eworkService->saveEworkFabric($params);
        return ['data'=> $params,'msg'=>'저장 완료.'];
    }

    /**
     * 전산작지 정보 가져오기
     * @param $params
     * @return array
     */
    public function getEworkData($params){
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        $rslt = $eworkService->getEworkData($params['styleSno']);
        return ['data'=> $rslt,'msg'=>'조회 완료.'];
    }

    /**
     * 현대 최초 로그인 인증
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setAdult($params){
        DBUtil2::update(DB_MEMBER, [
            'adultFl'=>'y',
            'adultConfirmDt'=>'now()'
        ], new SearchVo('memNo=?',$params['memNo']));

        return ['data'=> $params,'msg'=>'처리 완료.'];
    }

    /**
     * 기초설정 및 전산 작지 복사
     * @param $params
     * @return array
     */
    public function copyStyleBasicInfo($params){
        return ['data'=>$this->imsStyleService->copyStyleBasicInfo($params),'msg'=>'처리 완료'];
    }

    /**
     * 생산확정 견적에서 원부자재 복사
     * @param $params
     * @return array
     */
    public function copyMaterial($params){
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        return ['data'=>$eworkService->copyMaterial($params),'msg'=>'처리 완료'];
    }

    /**
     * 전체 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getAllList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getAllList($params),'msg'=>'처리 완료'];
    }

    /**
     * 발주완료 리스트
     * @param $params
     * @return array
     */
    public function getCompleteList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getCompleteList($params),'msg'=>'처리 완료'];
    }

    /**
     * 영업 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getSalesList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getSalesList($params),'msg'=>'처리 완료'];
    }

    /**
     * 영업 프로젝트 기타 리스트
     * @param $params
     * @return array
     */
    public function getSalesAnotherList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getSalesAnotherList($params),'msg'=>'처리 완료'];
    }

    /**
     * 디자인 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getDesignList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getDesignList($params),'msg'=>'처리 완료'];
    }

    /**
     * QC 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getQcList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getQcList($params),'msg'=>'처리 완료'];
    }

    /**
     * 리오더 프로젝트 리스트
     * @param $params
     * @return array
     */
    public function getReorderList($params){
        $imsProjectListService = SlLoader::cLoad('imsv2','ImsProjectListService');
        return ['data'=>$imsProjectListService->getReorderList($params),'msg'=>'처리 완료'];
    }

    /**
     * 저장 25년 버전
     * @param $params
     * @return array
     */
    public function imsSave($params){
        //$tableName = 'sl_ims'.ucfirst($params['target']);
        $sno = $this->imsService->imsSave($params['target'], $params['saveData']);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 저장
     * @param $params
     * @return array
     */
    public function saveImsProject($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $sno = $imsProjectService->saveImsProject($params);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 업데이트 ( 수정 )
     * @param $params
     * @return array
     */
    public function updateProject($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $sno = $imsProjectService->updateProject($params);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 일괄 업데이트 ( 수정 )(영업리스트 -> 리스트 내 프로젝트들 일괄수정기능)
     * @param $params
     * @return array
     */
    public function updateProjectMulti($params){
        $imsNkService = SlLoader::cLoad('imsv2','ImsNkMultiUpdateService');
        $imsNkService->updateProjectMulti($params);
        return ['data'=> 0,'msg'=>'저장 완료'];
    }

    public function setSalesStatus($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $sno = $imsProjectService->setSalesStatus($params['projectSno'], $params['salesStatus']);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 스타일 리스트 저장
     * @param $params
     * @return array
     */
    public function saveStyleList($params){
        $service = SlLoader::cLoad('ims','ImsStyleService');
        $sno = $service->saveStyleList($params['styleList']);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 간단한 스타일 정보 가져오기
     * @param $params
     * @return array
     */
    public function getSimpleProductData($params){
        return ['data'=>$this->imsService->getSimpleProductData($params['sno']),'msg'=>'조회 완료'];
    }

    /**
     * Simple 프로젝트 데이터 반환
     * @param $params
     * @return array
     */
    public function getSimpleProject($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        return ['data'=>$imsProjectService->getSimpleProject($params['sno']),'msg'=>'조회 완료'];
    }

    /**
     * 스타일 기본 구조 반환
     * @param $params
     * @return array
     */
    public function getProductDefaultScheme($params){
        return ['data'=>$this->imsService->getProductDefaultScheme([]),'msg'=>'조회 완료'];
    }

    /**
     * 프로젝트 스케쥴 일괄 저장
     * @param $params
     * @return array
     */
    public function saveProjectSchedule($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        $rslt = SlCommonUtil::transactionMethod($imsProjectService, 'saveProjectSchedule', $params);
        return ['data'=>$rslt,'msg'=>'저장 완료'];
    }

    /**
     * 프로젝트 결재 상태 패스
     * @param $params
     * @return array
     */
    public function setApproval($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        return ['data'=>$imsProjectService->setApproval($params),'msg'=>'저장 완료'];
    }

    /**
     * 리오더 처리
     * @param $params
     * @return array
     */
    public function reOrder($params){
        $imsProjectService = SlLoader::cLoad('imsv2','ImsProjectService');
        return ['data'=>$imsProjectService->reOrder($params['projectSno'], 50),'msg'=>'생성 완료'];
    }

    /**
     * 기획 불가 처리 
     * @param $params
     * @return array
     */
    public function setPlanNotPossible($params){
        $this->imsService->setPlanNotPossible($params['projectSno'], $params['reason']);
        return ['data'=>$params,'msg'=>'처리 완료'];
    }

    /**
     * 아시아나 카트 삭제
     * @param $params
     * @return array
     * @throws Exception
     */
    public function removeAsianaCart($params){
        DBUtil2::delete('sl_asianaCart',new SearchVo('sno=?',$params['cartSno']));
        return ['data'=>$params,'msg'=>'카트 삭제 처리 완료'];
    }

    /**
     * 프로젝트 회계 반영
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setBookRegistered($params){

        $imsService = SlLoader::cLoad('ims', 'imsService');

        foreach($params['projectCheckList'] as $projectSno){

            if( empty($params['type']) || 'book' == $params['type'] ){
                if( 'y' == $params['isBookRegistered'] ){
                    $isBookRegistered = 'y';
                    $isBookRegisteredDt = 'now()';
                }else{
                    $isBookRegistered = 'n';
                    $isBookRegisteredDt = '';
                }
                DBUtil2::update(ImsDBName::PROJECT,[
                    'isBookRegistered'=>$isBookRegistered,
                    'isBookRegisteredDt'=>$isBookRegisteredDt,
                ], new SearchVo('sno=?', $projectSno));
            }else if( 'work' == $params['type'] ){
                if( 'y' == $params['isBookRegistered'] ){
                    $isBookRegistered = 'y';
                    $isBookRegisteredDt = 'now()';
                }else{
                    $isBookRegistered = 'n';
                    $isBookRegisteredDt = '';
                }
                DBUtil2::update(ImsDBName::PROJECT,[
                    'refineOrder'=>$isBookRegistered,
                    'refineOrderDt'=>$isBookRegisteredDt,
                ], new SearchVo('sno=?', $projectSno));
            }else if( 'stock' == $params['type'] ){
                if( 'y' == $params['isBookRegistered'] ){
                    $isBookRegistered = 'y';
                    $isBookRegisteredDt = 'now()';
                }else{
                    $isBookRegistered = 'n';
                    $isBookRegisteredDt = '';
                }
                DBUtil2::update(ImsDBName::PROJECT,[
                    'confirmStock'=>$isBookRegistered,
                    'confirmStockDt'=>$isBookRegisteredDt,
                ], new SearchVo('sno=?', $projectSno));
            }

            $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);
            if( 91 != $projectData['projectStatus']
                && 'y'===$projectData['isBookRegistered']
                && 'y'===$projectData['refineOrder']
                && 'y'===$projectData['confirmStock']
            ){
                $imsService->setStatus(['projectSno'=>$projectSno,'projectStatus'=>91, 'reason'=>'마감 조건 완료로 단계변경'], true);
            }
        }
        return ['data'=>$params,'msg'=>'회계 처리 완료'];
    }

    /**
     * 퀄리티 해당 없음 처리
     * @param $params
     * @return array
     */
    public function setPassFabric($params){
        $this->imsStyleService->setPassFabric($params['styleSno'],$params['isYn']);
        return ['data'=>$params,'msg'=>'처리 완료'];
    }

    /**
     * 작지 리비전 저장
     * @param $params
     * @return array
     */
    public function saveEworkRevision($params){
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        $sno = $eworkService->saveEworkRevision($params);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 작지 리비전 구조 전달
     * @param $params
     * @return array
     */
    public function addEworkRevision($params){
        $revisionScheme = ImsJsonSchema::EWORK_REVISION;
        $revisionScheme['regDt'] = SlCommonUtil::getNow();
        $revisionScheme['regManagerName'] = \Session::get('manager.managerNm');
        return ['data'=>$revisionScheme,'msg'=>'조회 완료'];
    }

    /**
     * 긴급 TO-DO 처리
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setEmergencyTodoConfirm($params){
        $resSnoList = explode(',', $params['resSnoListStr']);
        $searchVo = new SearchVo();
        $searchVo->setWhere(DBUtil2::bind('sno', DBUtil2::IN, count($resSnoList) ));
        $searchVo->setWhereValueArray( $resSnoList );
        $rslt = DBUtil2::update(ImsDBName::TODO_RESPONSE,['emergencyConfirmDt'=>'now()'],$searchVo);
        return ['data'=>$rslt,'msg'=>'처리 완료'];
    }

    /**
     * 긴급요청건 등록
     * @param $params
     * @return array
     */
    public function reqEmergencyTodo($params){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $hopeDt = SlCommonUtil::getDateCalc(date('Y-m-d'), 1);
        $imsService->addTodoData($params['subject'], $params['subject'], $hopeDt, $params['projectSno'], [$params['targetManagerSno']], 'y');
        return ['data'=>$params,'msg'=>'처리 완료'];
    }


    /**
     * 고객 리스트
     * @param $params
     * @return array
     */
    public function getCustomerList($params){
        $imsCustomerListService = SlLoader::cLoad('imsv2','ImsCustomerListService');
        return ['data'=>$imsCustomerListService->getCustomerList($params),'msg'=>'처리 완료'];
    }

    /**
     * 고객 리스트 + 프로젝트 정보 검색
     * @param $params
     * @return array
     */
    public function getCustomerListWithProject($params){
        $imsCustomerListService = SlLoader::cLoad('imsv2','ImsCustomerListService');
        return ['data'=>$imsCustomerListService->getCustomerList($params),'msg'=>'처리 완료'];
    }

    /**
     * TM EM 이력 반환
     * @param $params
     * @return array
     */
    public function getTmHistory($params){
        $imsCustomerService = SlLoader::cLoad('ims','ImsCustomerService');
        return ['data'=>$imsCustomerService->getTmHistory($params),'msg'=>'처리 완료'];
    }


    /**
     * 코멘트 리스트 데이터 전달
     * @param $params
     * @return array
     */
    public function getCommentListData($params){
        return ['data'=>ImsUtil::getCommentListData($params),'msg'=>'처리 완료'];
    }

    /**
     * TM 리스트 데이터 전달
     * @param $params
     * @return array
     */
    public function getTmListData($params){
        return ['data'=>ImsUtil::getTmListData($params),'msg'=>'처리 완료'];
    }

    /**
     * 프로젝트 참여자 추가
     * @param $params
     * @return array
     */
    public function addProjectMember($params){
        $service = SlLoader::cLoad('imsv2','ImsProjectService');
        $sno = $service->addProjectMember($params);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 고객에 메일 발송
     * sendMailToCustomer
     * @param $params
     * @return array
     * @throws Exception
     */
    public function sendMailToCustomer($params){
        $this->imsService->sendMailToCustomer($params);
        return ['data'=> $params,'msg'=>'발송 완료'];
    }

    /**
     * 테스트
     * @param $params
     * @return array
     */
    public function test($params){
        return ['data'=>$params,'msg'=>'테스트 완료'];
    }

}



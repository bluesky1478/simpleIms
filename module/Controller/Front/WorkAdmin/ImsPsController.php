<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Controller\Front\WorkAdmin;

use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Controller\Admin\Ims\ImsPsProductTrait;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;
use Request;

class ImsPsController extends \Controller\Front\Controller{
    use SlControllerTrait;
    use ImsPsProductTrait;

    private $imsService;
    private $imsProduceService;
    private $imsStyleService;
    private $imsCustomerEstimateService;

    public function __construct(){
        parent::__construct();
        $this->imsService = SlLoader::cLoad('ims', 'imsService');
        $this->imsProduceService = SlLoader::cLoad('ims', 'imsProduceService');
        $this->imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $this->imsCustomerEstimateService = SlLoader::cLoad('ims', 'imsCustomerEstimateService');
    }

    public function getMyService(){
        return $this;
    }

    public function delayTest($params){
        sleep(5);
        return ['msg'=>'조회 완료'];
    }

    /**
     * target 데이터 가져오기
     * @param $params
     * @return array
     */
    public function getDataFront($params){
        $fncName = 'get'.ucfirst($params['target']);
        $rslt = $this->imsService->$fncName($params);
        return ['data'=>$rslt,'msg'=>'조회 완료'];
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
        $fncName = 'getList'.ucfirst($params['target']);
        return ['data'=>$this->imsService->$fncName($params),'msg'=>'조회 완료'];
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
     * 프로젝트 등록
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
     * 생산 개별 코멘트
     * @param $params
     * @return array
     */
    public function saveProductionComment($params){
        $this->imsService->saveProductionComment($params);
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
        return ['data'=> $schema,'msg'=>'조회 완료'];
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
            ]);
        }

        $imsProduceService->save(ImsDBName::PRODUCE,$saveData);

        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 코멘트 저장 (프론트)
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


    public function downloadFabricRequestForm($params){
        $this->imsService->downloadFabricRequestForm($params);
    }

}

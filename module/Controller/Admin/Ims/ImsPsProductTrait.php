<?php

namespace Controller\Admin\Ims;

use App;
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
 * 상품관련 Trait
 */
trait ImsPsProductTrait {

    /**
     * 퀄리티 상태 셋팅
     * @param $params
     * @return array
     */
    public function setFabricPass($params){
        $rslt = $this->imsService->save(ImsDBName::PRODUCT, $params);
        $this->imsService->setSyncStatus($params['projectSno'], __METHOD__);
        return ['data'=> $rslt,'msg'=>'처리 완료'];
    }

    //--- 샘플 처리

    /**
     * 샘플 정보 저장
     * @param $params
     * @return array
     */
    public function setSampleNothing($params){
        $rslt = $this->imsService->save(ImsDBName::PRODUCT, $params);
        $this->imsService->setSyncStatus($params['projectSno'], __METHOD__);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 샘플 정보 저장
     * @param $params
     * @return array
     */
    public function saveSample($params){
        $rslt = $this->imsService->saveSample($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 샘플 복사
     * @param $params
     * @return array
     */
    public function copySample($params){
        $rslt = $this->imsService->copySample($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 샘플 삭제
     * @param $params
     * @return array
     */
    public function deleteSample($params){
        $rslt = $this->imsService->deleteSample($params);
        return ['data'=> $rslt,'msg'=>'삭제 완료'];
    }

    /**
     * 샘플 확정
     * @param $params
     * @return array
     */
    public function confirmSample($params){
        $rslt = $this->imsService->confirmSample($params);
        return ['data'=> $rslt,'msg'=>'컨펌 완료'];
    }

    /**
     * 상품 샘플리스트 조회
     * @param $params
     * @return array
     */
    public function getSampleList($params){
        return ['data'=>$this->imsService->getSampleList($params),'msg'=>'조회 완료'];
    }

    /**
     * 연관 스타일 조회
     * @param $params
     * @return array
     */
    public function getRelatedList($params){
        return ['data'=>$this->imsService->getRelatedList($params),'msg'=>'조회 완료'];
    }

    /**
     * 새로 등록을 위해 기존 정보 가져오기.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function loadSample($params){
        preg_match_all('/\d+/', $params['loadSampleNo'], $matches);
        $numberStr = implode('', $matches[0]);
        $sno =  intval($numberStr) - 1000;
        $data = $this->imsService->getSample(['sno'=>$sno]);
        if(!empty($data['sno'])){
            $unsetFieldList = [
                'sampleFile1Approval'
                ,'sampleConfirmDt'
                ,'sampleConfirmManager'
                ,'sampleConfirm'
                ,'sampleFactoryBeginDt'
                ,'sampleFactoryEndDt'
                ,'sno'
                ,'regDt'
                ,'modDt'
            ];
            $data = SlCommonUtil::unsetByList($data, $unsetFieldList);
            $data['sampleManagerSno'] = SlCommonUtil::getManagerSno();
        }else{
            throw new \Exception('샘플 정보를 찾을 수 없습니다.');
        }
        return ['data'=>$data,'msg'=>'조회 완료'];
    }


    /**
     * 가견적에서 원부자재 정보 가져오기.
     * @param $params
     * @return array
     * @throws Exception
     */
    public function loadCostEstimate($params){
        $data = $this->imsService->getFactoryEstimate(['sno'=>$params['loadCostEstimateNo']]);
        $rslt = null;
        if(!empty($data['sno'])){
            $rslt = $data['contents'];
        }else{
            throw new \Exception('가견적 정보를 찾을 수 없습니다.');
        }
        return ['data'=>$rslt,'msg'=>'조회 완료'];
    }

    //--- 원단 처리
    public function saveFabric($params){
        $rslt = $this->imsService->saveFabric($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    public function saveFabricReq($params){
        $rslt = $this->imsService->saveFabricReq($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 반려처리
     * @param $params
     * @return array
     */
    public function setRejectQb($params){
        $rslt = $this->imsService->setRejectQb($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 관리 원단 리스트 조회
     * @param $params
     * @return array
     */
    public function getFabricList($params){
        return ['data'=>$this->imsService->getFabricList($params),'msg'=>'조회 완료'];
    }


    // --- 가견적 / 생산가 확정 공통 처리

    public function saveEstimateCostReq($params){
        $rslt = $this->imsService->saveEstimateReq($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    // --- 가견적 처리

    /**
     * 가견적 요청 저장
     * @param $params
     * @return array
     */
    public function saveEstimateReq($params){
        $rslt = $this->imsService->saveEstimateReq($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 가견적 리스트 반환
     * @param $params
     * @return array
     */
    public function getEstimateList($params){
        return ['data'=>$this->imsService->getEstimateList($params),'msg'=>'조회 완료'];
    }

    /**
     * 가견적 선택
     * @param $params
     * @return array
     */
    public function selectEstimate($params){
        return ['data'=>$this->imsService->selectEstimate($params),'msg'=>'선택 완료'];
    }

    /**
     * 가견적 취소
     * @param $params
     * @return array
     */
    public function cancelEstimate($params){
        return ['data'=>$this->imsService->cancelEstimate($params),'msg'=>'취소 완료'];
    }


    // ---- 확정가 처리

    /**
     * 확정가 요청 저장
     * @param $params
     * @return array
     */
    public function saveCostReq($params){
        $params['estimateType'] = 'cost';
        $rslt = $this->imsService->saveEstimateReq($params);
        return ['data'=> $rslt,'msg'=>'조회 완료'];
    }

    /**
     * 확정견적
     * @param $params
     * @return array
     */
    public function getCostList($params){
        //$params['estimateType'] = 'cost';
        return ['data'=>$this->imsService->getEstimateList($params),'msg'=>'조회 완료'];
    }

    /**
     * 기존 견적 불러오기
     * @param $params
     * @return array
     * @throws Exception
     */
    public function loadEstimate($params){
        preg_match_all('/\d+/', $params['loadEstimateSno'], $matches);
        $numberStr = implode('', $matches[0]);
        $sno =  intval($numberStr);
        $data = $this->imsService->getFactoryEstimate(['sno'=>$sno]);
        if(empty($data['sno'])){
            throw new \Exception('견적 정보를 찾을 수 없습니다.');
        }
        return ['data'=>$data['contents'],'msg'=>'조회 완료'];
    }

    /**
     * 생산가 확정
     * @param $params
     * @return array
     */
    public function selectCost($params){
        return ['data'=>$this->imsService->selectCost($params),'msg'=>'선택 완료'];
    }

    /**
     * 가견적 취소
     * @param $params
     * @return array
     */
    public function cancelCost($params){
        $params['estimateType'] = 'cost';
        return ['data'=>$this->imsService->cancelEstimate($params),'msg'=>'취소 완료'];
    }

    /**
     * 재요청
     * @param $params
     * @return array
     */
    public function setEstimateStatus($params){
        return ['data'=>$this->imsService->setEstimateStatus($params),'msg'=>'재요청 완료'];
    }

    /*생산처리*/

    /**
     * 생산 저장
     * @param $params
     * @return array
     */
    public function saveProduction($params){
        $rslt = $this->imsService->saveProduction($params);
        return ['data'=> $rslt,'msg'=>'저장 완료'];
    }

    /**
     * 스케쥴 요청
     * @param $params
     * @return array
     */
    public function setScheduleReq($params){
        $this->imsService->setScheduleReq($params);
        return ['data'=> $params,'msg'=>'저장 완료'];
    }

    /**
     * 스케쥴 체크 값 설정
     * @param $params
     * @return array
     */
    public function setScheduleCheck($params){
        $this->imsService->setScheduleCheck($params);
        return ['data'=> $params,'msg'=>'저장 완료'];
    }

    /**
     * 스케쥴 체크 값 설정
     * @param $params
     * @return array
     */
    public function setProduceStatus($params){
        $this->imsService->setProduceStatus($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스케쥴 체크 값 설정 (일괄 처리)
     * @param $params
     * @return array
     */
    public function setProduceStatusBatch($params){
        foreach($params['checkSnoList'] as $sno){
            $this->imsService->setProduceStatus([
                'sno'=>$sno,
                'status'=>$params['checkValue'],
            ]);
        }
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /**
     * 스케쥴 일괄 수정
     * @param $params
     * @return array
     */
    public function saveScheduleBatch($params){
        $this->imsService->saveScheduleBatch($params);
        return ['data'=> $params,'msg'=>'처리 완료'];
    }

    /*기타*/

    /**
     * 가견적이나 확정가의 원단 관리에서 QB등록하기
     * @param $params
     * @return array
     */
    public function addQb($params){
        $this->imsService->addQb($params);
        return ['data'=> $rslt,'msg'=>'저장 완료'];
    }
    public function deleteQb($params){
        $this->imsService->deleteQb($params);
        return ['data'=> $rslt,'msg'=>'삭제 완료'];
    }


    /**
     * QB 데드라인 설정 (ims_request_list.php 에서 사용)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setQbDeadLine($params){
        $searchVo = new SearchVo();
        $searchVo->setWhere(DBUtil2::bind('sno', DBUtil2::IN, count($params['snoList']) ));
        $searchVo->setWhereValueArray( $params['snoList']  );
        DBUtil2::update(ImsDBName::FABRIC_REQ, [
            'completeDeadLineDt' => $params['deadLineDt']
        ] ,$searchVo);
        return ['data'=> $rslt,'msg'=>'처리 완료'];
    }


    /**
     * 원단정보 업데이트
     * @param $params
     * @return array
     */
    public function updateFabricReq($params){
        $rslt = $this->imsService->updateFabricReq($params);
        return ['data'=> $rslt,'msg'=>'처리 완료'];
    }

    /**
     * 아소트 고객 입력 완료 (인터페이스)
     * @param $params
     * @return array
     */
    public function setAssortComplete($params){
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $imsStyleService->setAssortComplete($params);
        return ['data'=> $params,'msg'=>'처리 되었습니다.'];
    }

    /**
     * 아소트 상태 변경
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setAssortStatus($params){
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $imsStyleService->setAssortStatus($params);
        return ['data'=> $params,'msg'=>'발송 되었습니다.'];
    }

    /**
     * 사양서 상태 변경 (우선 상태만)
     * @param $params
     * @return array
     * @throws Exception
     */
    public function setOrderStatus($params){
        $updateData = ['customerOrderConfirm' => $params['status']];
        if('r' === $params['status']){
            $updateData['customerOrderConfirmDt'] = '0000-00-00 00:00:00'; //고객 입력 단계로 변경
            DBUtil2::update(ImsDBName::PROJECT, $updateData ,new SearchVo('sno=?', $params['sno']));
            DBUtil2::update(ImsDBName::PROJECT_EXT, ['cpOrderConfirm'=>'0000-00-00','stOrderConfirm'=>0] ,new SearchVo('projectSno=?', $params['sno']));//발송완료 단계로 변경
        }
        return ['data'=> $params,'msg'=>'처리 되었습니다.'];
    }

    /**
     * 사양서 고객 체크 완료 (인터페이스)
     * @param $params
     * @return array
     */
    public function setOrderComplete($params){
        $imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $imsStyleService->setOrderComplete($params);
        return ['data'=> $params,'msg'=>'처리 되었습니다.'];
    }

}


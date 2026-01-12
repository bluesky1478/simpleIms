<?php

namespace Controller\Admin\Ims;

use App;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * TO_DO List 관련 Trait
 */
trait ImsPsTodoTrait {

    /**
     * 결재저장
     * @param $params
     * @return array
     */
    public function saveApproval($params){
        $rslt = $this->imsService->saveApproval($params);
        return ['data'=> $rslt,'msg'=>'처리 완료'];
    }

    /**
     * 자체결재
     * @param $params
     * @return array
     */
    public function saveApprovalSelf($params){
        $rslt = $this->imsService->saveApprovalSelf($params);
        return ['data'=> $rslt,'msg'=>'처리 완료'];
    }

    /**
     * 결재선 저장
     * @param $params
     * @return array
     */
    public function saveApprovalLine($params){
        $rslt = $this->imsService->saveApprovalLine($params);
        return ['data'=> $rslt,'msg'=>'저장 완료'];
    }

    /**
     * 할일저장
     * @param $params
     * @return array
     */
    public function saveTodo($params){
        $rslt = $this->imsService->saveTodo($params);
        return ['data'=> $rslt,'msg'=>'처리 완료'];
    }

    /**
     * 처리 예정일 저장
     * @param $params
     * @return array
     */
    public function saveTodoExpectedDt($params){
        $rslt = $this->imsService->saveTodoExpectedDt($params);
        return ['data'=> $rslt,'msg'=>'저장 완료'];
    }

    /**
     * 상태변경
     * @param $params
     * @return array
     */
    public function setTodoStatus($params){
        $rslt = $this->imsService->setTodoStatus($params);
        return ['data'=> $rslt,'msg'=>'상태변경 완료'];
    }

    /**
     * 코멘트 저장
     * @param $params
     * @return array
     */
    public function writeComment($params){
        $rslt = $this->imsService->writeComment($params);
        return ['data'=> $rslt,'msg'=>'저장 완료'];
    }


    /**
     * 결재 상태 변경
     * @param $params
     * @return array
     */
    public function setApprovalStatus($params){
        $returnData = $this->imsService->setApprovalStatus($params);
        return ['data'=> $returnData,'msg'=>$returnData['msg']];
    }

}



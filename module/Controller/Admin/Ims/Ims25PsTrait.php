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
 * Ims25 서비스 인터페이스
 */
trait Ims25PsTrait {

    /**
     * 단순 테이블 데이터 반환 ( 추후 조건 추가하여 확장 가능성 있음 )
     * @param $params
     * @return array
     */
    public function getTableData($params){
        $tableName = 'sl_ims'.ucfirst($params['target']);
        return ['data'=> DBUtil2::getList($tableName, '1', '1'),'msg'=>'조회 완료'];
    }

    /**
     * 스케쥴 설정 정보 반환
     * @param $params
     * @return array
     */
    public function getScheduleConfig($params){
        return ['data'=> ImsScheduleUtil::getScheduleConfig(),'msg'=>'조회 완료'];
    }

    
    /**
     * 고객 저장
     * @param $params
     * @return array
     */
    public function saveIms25Customer($params){
        $ims25ProjectService = SlLoader::cLoad('ims25','ims25ProjectService');
        return ['data'=> $ims25ProjectService->saveIms25Customer($params),'msg'=>'저장 완료'];
    }
    /**
     * 프로젝트 저장
     * @param $params
     * @return array
     */
    public function saveIms25Project($params){
        $ims25ProjectService = SlLoader::cLoad('ims25','ims25ProjectService');
        return ['data'=> $ims25ProjectService->saveIms25Project($params),'msg'=>'저장 완료'];
    }
    /**
     * 상품 저장
     * @param $params
     * @return array
     */
    public function saveIms25Product($params){
        $ims25ProjectService = SlLoader::cLoad('ims25','ims25ProjectService');
        return ['data'=> $ims25ProjectService->saveIms25Product($params),'msg'=>'저장 완료'];
    }
    /**
     * 프로젝트 저장 후처리
     * @param $params
     * @return array
     */
    public function saveProjectAfterProc($params){
        $ims25ProjectService = SlLoader::cLoad('ims25','ims25ProjectService');
        return ['data'=> $ims25ProjectService->saveProjectAfterProc($params),'msg'=>'저장 완료'];
    }

}



<?php

namespace Controller\Front\Ics;

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
use Controller\Admin\Ims\ImsPsNkTrait;
use Controller\Admin\Ims\ImsPsProductTrait;
use Controller\Admin\Ims\ImsPsTodoTrait;
use Controller\Admin\Ims\ImsPsTrait;
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
 * API
 */
class IcsPsController extends \Controller\Front\Controller {

    use SlControllerTrait;
    use ImsPsProductTrait;
    use ImsPsTodoTrait;
    use ImsPsTrait;
    use ImsPsNkTrait;

    private $imsService;
    private $imsProduceService;
    private $imsCustomerEstimateService;
    private $imsStyleService;

    public function __construct(){
        parent::__construct();
        $this->imsService = SlLoader::cLoad('ims', 'imsService');
        $this->imsStyleService = SlLoader::cLoad('ims', 'imsStyleService');
        $this->imsProduceService = SlLoader::cLoad('ims', 'imsProduceService'); //구 생산관리 서비스
        $this->imsCustomerEstimateService = SlLoader::cLoad('ims', 'imsCustomerEstimateService'); //구 생산관리 서비스
    }

    /**
     * target 데이터 가져오기
     * @param $params
     * @return array
     */
    public function getFrontData($params){
        $fncName = 'get'.ucfirst($params['target']);
        $rslt = $this->imsService->$fncName($params);
        return ['data'=>$rslt,'msg'=>'조회 완료'];
    }

    /**
     * 파일 업로드
     * @param $params
     * @return array
     */
    public function asianaOrder($params){
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $service->asianaOrder($params);
        return ['data'=>$params,'msg'=>'완료'];
    }

    public function getAsianaEmpData($params){
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $list = $service->getAsianaEmpData($params['companyId']);
        return ['data'=>$list,'msg'=>'완료'];
    }

}

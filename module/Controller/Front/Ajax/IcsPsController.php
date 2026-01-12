<?php

namespace Controller\Front\Ajax;

use App;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
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
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * API
 */
class IcsPsController extends Controller {

    /*use SlControllerTrait;

    public function getMyService(){
        return SlLoader::cLoad('api','ApiService','sl');
    }*/
    public function index(){

        $ip = \Request::getRemoteAddress();
        $allowIp = ['133.186.209.40', '180.83.86.189','1.243.196.160'];

        if( in_array($ip, $allowIp) ){
            $this->json(
                [
                    'code' => 200,
                    'message' => '테스트완료!',
                    'data' => [
                        'msg'=>'테스트 완료 데이터',
                        'myIp'=>\Request::getRemoteAddress(),
                    ],
                ]
            );
        }else{
            $this->json(
                [
                    'code' => 403,
                    'message' => '권한없음',
                    'data' => [],
                ]
            );
        }

        exit();
    }


}

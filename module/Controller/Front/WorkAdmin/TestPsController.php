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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

class TestPsController extends \Controller\Front\Controller{

    public function index(){
        $requestValue  = Request::request()->toArray();
        if (\Request::isAjax()) {
            $this->ajax($requestValue);
        } else {
            $ref = Request::getReferer();
            if( strpos($ref, 'gdadmin') !== false ){
                $this->iframe($requestValue);
            }else{
                $this->frontIframe($requestValue);
            }
        }
        exit();
    }


    public function myTest($params){

        SitelabLogger::logger2(__METHOD__, $params);

        return ['msg'=>'조회 완료'];
    }

}

<?php

namespace Controller\Admin\Test;

use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;
use UserFilePath;
use Framework\Security\Digester;

/**
 * TEST 페이지
 */
class TestFormController extends \Controller\Admin\Controller{

    public function index(){
        $post = \Request::request()->toArray();
        if( !empty($post) ){

            $sql = $post['sql'];

            if( strpos($sql,'password:') !== false ){
                $pwdStr = explode(':', $sql)[1];
                gd_debug($pwdStr);
                gd_debug(Digester::digest($pwdStr));
            }else{
                gd_debug(DBUtil2::runSql($sql));
            }
        }
    }

}
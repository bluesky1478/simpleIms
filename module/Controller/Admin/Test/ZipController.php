<?php

namespace Controller\Admin\Test;

use Component\Member\Util\MemberUtil;
use Controller\Admin\Order\ControllerService\DeliveryListSql;
use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use SlComponent\Database\DBConst;
use SlComponent\Godo\FactoryService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlErpUtil;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlSmsUtil;
use Request;
use Framework\Utility\DateTimeUtils;
use UserFilePath;
use App;
use Component\Godo\GodoSmsServerApi;
use Component\Member\Group\Util;
use Component\Page\Page;
use Component\Validator\Validator;
use Component\Sms\Sms;
use Component\Sms\SmsAutoCode;
use Component\Sms\Code;
use Component\Storage\Storage;
use Exception;
use Framework\Database\DBTool;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;
use Framework\Utility\ComponentUtils;
use Logger;
use Framework\Debug\Exception\AlertBackException;
use Component\Coupon\Coupon;

/**
 * TEST 페이지
 * $memberService = SlLoader::cLoad('godo','memberService','sl');
 */
class ZipController extends \Controller\Admin\Controller{

    public function index(){
        gd_debug(" === 압축풀기 시작 ===");
        $zip = new \ZipArchive;

        $path = '/www/msinnover4_godomall_com/admin/script/';
        //$path = '/www/admin/script/';

        $filePath = $path.'module.zip';
        $toPath = $path.'module/';

        gd_debug($filePath);
        gd_debug($toPath);
        gd_debug(realpath(__FILE__));

        if( $zip->open($filePath) === true ){
            $zip->extractTo($toPath);
            $zip->close();
            gd_debug('complete');
        }else{
            gd_debug('fail');
        }
        gd_debug(" === 압축풀기 종료 ===");
        exit();
    }
}
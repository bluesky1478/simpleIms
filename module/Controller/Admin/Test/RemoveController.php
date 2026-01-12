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
class RemoveController extends \Controller\Admin\Controller{

    public function index(){
        echo `rm -rf /www/msinnover4_godomall_com/admin/script/module`;
        //echo `rm -rf /admin/script/kkk`;
        gd_debug(" === 파일 삭제 종료 ===");
        exit();
    }

}
<?php

namespace Controller\Admin\Test;

use Framework\Utility\NumberUtils;
use Component\Database\DBTableField;
use Component\Sitelab\SiteLabSmsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use Globals;
use Component\Deposit\Deposit;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * TEST 페이지
 */
class TestStaticsController extends \Controller\Admin\Controller{

    public function index(){
        gd_debug("== 통계 데이터 테스트 ==");






        gd_debug("완료");
        exit();
    }

}
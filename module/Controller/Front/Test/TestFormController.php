<?php

namespace Controller\Front\Test;

use Component\Sitelab\MallConfig;
use Component\Sitelab\SiteLabDownloadUtil;
use Component\Sitelab\SiteLabGodoUtil;
use Component\Sitelab\SlLoader;
use Component\Storage\Storage;
use Domain\Sl\Estimate\EstimateService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use UserFilePath;
use FileHandler;
use Component\Sitelab\SitelabDBTable;
use Component\Sitelab\SitelabLogger;
use Component\Database\DBTableField;

use Component\Mall\Mall;
use Component\Mall\MallDAO;
use Session;
use Component\Member\Util\MemberUtil;
use Component\Validator\Validator;
use Component\Cart\Cart;
use App;
/**
 * 주문 상세 페이지
 * [관리자 모드] 주문 상세 페이지
 *
 * @package Bundle\Controller\Admin\Order
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class TestFormController extends \Controller\Front\Controller{

    public function index(){

        gd_debug('테스트');

    }

}
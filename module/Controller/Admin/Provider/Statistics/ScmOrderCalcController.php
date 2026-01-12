<?php

namespace Controller\Admin\Provider\Statistics;

use App;
use Component\Order\OrderPolicyListService;
use Component\Scm\ScmOrderListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;

class ScmOrderCalcController extends \Controller\Admin\Order\ScmOrderCalcController{
    public function index(){
        parent::index();
    }
}
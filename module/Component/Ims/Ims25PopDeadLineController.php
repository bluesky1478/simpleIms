<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Imsv2\ImsScheduleUtil;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Godo\ControllerService;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 상세 스케쥴관리 팝업 ( 최신 버전 25/04/18 )
 */
class Ims25PopDeadLineController extends \Controller\Admin\Ims\Ims25ViewController
{
    public function index()
    {
        parent::index();
        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
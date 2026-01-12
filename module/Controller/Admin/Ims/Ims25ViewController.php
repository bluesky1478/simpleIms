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
 * 프로젝트 상세 ( 최신 버전 25/04/18 )
 */
class Ims25ViewController extends \Controller\Admin\Controller
{

    use ImsControllerTrait;
    use ImsStepTrait;

    public function index()
    {
        ControllerService::setReloadData($this);

        $request = \Request::request()->toArray();
        $this->setData('projectKey', SlCommonUtil::aesEncrypt($request['sno']));

        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setSyncStatus($request['sno']);

        $this->setDefault();

        $current = gd_isset($request['current'], 'all');
        $this->callMenu('ims', 'list', $current);

        $schedule = ImsScheduleUtil::getScheduleList();
        $this->setData('prepSales', $schedule['prep_s']);
        $this->setData('prepDesign', $schedule['prep_d']);
        $this->setData('sales', $schedule['s']);
        $this->setData('design', $schedule['d']);
        $this->setData('qc', $schedule['q']);
        $this->setData('summary', $schedule['summary']);
    }
}
<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\ImsCodeMap;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 프로젝트 리스트 24년 12월 Version
 */
class ImsListSalesController extends \Controller\Admin\Controller{

    use ImsControllerTrait;
    use ImsStepTrait;
    use ImsListControllerTrait;

    public function index(){
        $request = \Request::request()->toArray();
        $this->callMenu('ims', 'prj', 'sales');
        $this->setDefault();
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
            'prj.sno' => '프로젝트번호',
            'sales.managerNm' => '영업담당자',
            'prj.projectYear' => '연도',
            'prj.projectSeason' => '시즌',
            'cust.industry' => '업종',
        ];
        $this->setData('search', $search);
        $this->setEmergencyTodoList();
        $this->listDownload();
    }

}
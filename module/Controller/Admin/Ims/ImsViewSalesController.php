<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
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
class ImsViewSalesController extends \Controller\Admin\Ims\ImsView2Controller{
    public function index()
    {
        parent::index();
        /*if( SlCommonUtil::isDev() ){
            $this->getView()->setPageName("ims/ims_view_sales.php");
        }else{
            $this->getView()->setPageName("ims/ims_view_sales.php"); //legacy
        }*/

        //$this->getView()->setPageName("ims/ims_view_sales.php");

        $managerId = \Session::get('manager.managerId');
        if( 'nkin' === $managerId ){
            $this->getView()->setPageName("ims/ims_view_sales_plan_reorder.php"); //new
        }else{
            $this->getView()->setPageName("ims/ims_view_sales_plan.php"); //new
        }

        $this->getView()->setDefine('layout', 'layout_blank.php');

    }
}
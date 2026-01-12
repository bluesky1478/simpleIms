<?php

namespace Controller\Admin\Ims\Popup;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsCustomerEstimateService;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Ims\ImsControllerTrait;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ImsPopCustomerEstimateController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        $this->setDefault();
        $this->getView()->setDefine('layout', 'layout_blank.php');
        $this->setData('estimateDataScheme', json_encode(ImsCustomerEstimateService::getDefaultData()));

    }

}
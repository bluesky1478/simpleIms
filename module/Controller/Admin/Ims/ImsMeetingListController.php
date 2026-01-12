<?php

namespace Controller\Admin\Ims;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsJsonSchema;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
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
class ImsMeetingListController extends \Controller\Admin\Controller{

    use ImsControllerTrait;

    public function index(){
        //$request = \Request::request()->toArray();
        $this->callMenu('ims', 'customer', 'meeting');
        $this->setDefault();
        $search['combineSearch'] = [
            'cust.customerName' => '고객명',
        ];
        $this->setData('search', $search);
        $this->setData('meetingRegUrl', SlCommonUtil::getHost() . '/workAdmin/document.php?docDept=SALES&docType=10');

    }

}
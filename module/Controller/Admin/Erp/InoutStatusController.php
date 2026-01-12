<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Globals;
use Request;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class InoutStatusController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->callMenu('erp', 'stock', 'inoutStatus');
    }

}
<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Globals;
use Request;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class ConfigStyleController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){

        $this->callMenu('work', 'config', 'style');

    }

}
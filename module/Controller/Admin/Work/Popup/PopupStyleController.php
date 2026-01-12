<?php

namespace Controller\Admin\Work\Popup;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Controller\Admin\Work\AdminWorkControllerTrait;
use Globals;
use Request;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class PopupStyleController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }

}
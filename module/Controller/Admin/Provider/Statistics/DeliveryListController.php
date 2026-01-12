<?php

namespace Controller\Admin\Provider\Statistics;

use App;
use Component\Claim\ClaimListService;
use Component\Claim\ClaimService;
use Component\Stock\StockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;

class DeliveryListController extends \Controller\Admin\Controller{

    public function index(){

        $isProvider = Manager::isProvider();
        $this->callMenu('statistics', 'accept', 'delivery_list');
        $this->setData('isProvider',$isProvider);

        $scmNo = \Session::get('manager.scmNo');
        $this->setData('scmNo', $scmNo);

        $list = DBUtil2::getList('sl_setScmDeliveryList', 'scmNo', $scmNo);
        $this->setData('list', $list);

    }

}
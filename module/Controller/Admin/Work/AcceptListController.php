<?php

namespace Controller\Admin\Work;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Globals;
use Request;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class AcceptListController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){
        $this->callMenu('work', 'manage', 'accept');
        /*$reqParam = \Request::request()->toArray();
        if( empty($reqParam['isApplyFl']) ){
            \Request::get()->set('isApplyFl','n');
        }*/

        $companyListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'acceptList');
        $listService=SlLoader::cLoad('godo','listService','sl');
        $listService->setList($companyListService, $this);
    }

}
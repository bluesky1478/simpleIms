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

class RequestListController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){

        $reqParam = \Request::request()->toArray();

        if( !empty( $reqParam['allDocument'] ) ){
            $this->callMenu('work', 'manage', 'reqlist');
        }else{
            $this->callMenu('work', strtolower($reqParam['docDept']) , 'reqlist' );
        }

        $companyListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'requestList');
        $listService=SlLoader::cLoad('godo','listService','sl');
        $listService->setList($companyListService, $this);
        //세션에서 식별 가능한 정보 .
    }

    /**
     * 엑셀 다운로드 처리
     * @param $getData
     */
    public function simpleExcelDownload($getData){
        //TODO
    }

}
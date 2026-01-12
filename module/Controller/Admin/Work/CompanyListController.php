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

class CompanyListController extends \Controller\Admin\Controller{

    use AdminWorkControllerTrait;

    public function workIndex(){

        $this->callMenu('work', 'manage', 'comp');
        $companyListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'companyList');
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
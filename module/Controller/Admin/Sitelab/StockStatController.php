<?php

namespace Controller\Admin\Sitelab;

use App;
use Component\Stock\DailyReportService;
use Component\Stock\StockStatService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class StockStatController extends \Controller\Admin\Controller{
    public function index(){

        $getValue = Request::get()->toArray();

        $this->callMenu('sitelab', 'stock', 'stock_stat');
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
            '../../script/vue.js',
        ]);

        $stockStatService = \App::load('\\Component\\Stock\\StockStatService');

        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);
        $getData = $stockStatService->getStockList($getValue);
        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);

        //타이틀
        $this->setData('listTitles',StockStatService::LIST_TITLES);
        //리스트 데이터
        $this->setData('data',$getData['data']);

        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

    }

    public function simpleExcelDownload($getData){
        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';

        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['inStock']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['outStock']));
            $fieldData[] = ExcelCsvUtil::wrapTd('-');
            $fieldData[] = ExcelCsvUtil::wrapTd($getData['search']['searchDate'][0] . '~' . $getData['search']['searchDate'][1] );
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('재고집계표',StockStatService::LIST_TITLES,$excelBody);
    }

}
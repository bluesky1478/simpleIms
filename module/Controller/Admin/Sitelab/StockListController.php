<?php

namespace Controller\Admin\Sitelab;

use App;
use Component\Stock\StockListService;
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

class StockListController extends \Controller\Admin\Controller{
    public function index(){

        $getValue = Request::get()->toArray();

        $this->callMenu('sitelab', 'stock', 'stock_list');
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
            /*'../../script/vue.js',*/
        ]);

        $stockListService = \App::load('\\Component\\Stock\\StockListService');

        //TODO : 1만 로우 이상 다운로드 관리자 문의 (성능에 문제될 수 있음) 기능 추가
        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);
        $getData = $stockListService->getStockList($getValue);
        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //사유
        $this->setData('stockReason', SlCodeMap::STOCK_REASON);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);

        //타이틀
        $this->setData('listTitles',StockListService::LIST_TITLES);
        //리스트 데이터
        $this->setData('data',$getData['data']);

        //재고 유형 맵
        $this->setData('stockTypeMap',SlCodeMap::STOCK_TYPE);
        $this->setData('stockReasonMap',SlCodeMap::STOCK_REASON);

        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

    }

    public function simpleExcelDownload($getData){
        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';
        $stockTypeMap = SlCodeMap::STOCK_TYPE;
        $stockReasonMap = SlCodeMap::STOCK_REASON;

        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($stockTypeMap[$val['stockType']]);
            $fieldData[] = ExcelCsvUtil::wrapTd($stockReasonMap[$val['stockReason']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['regDt']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('재고리스트',StockListService::LIST_TITLES,$excelBody);
    }

}
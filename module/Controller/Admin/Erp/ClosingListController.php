<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Globals;
use Request;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class ClosingListController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->callMenu('erp', 'stock', 'stockClosing');

        //복붙하기 좋게
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'closingList');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
    }




    public function simpleExcelDownload3($getData, $title){
        //$request = \Request::request()->get('type'); //TODO
        $erpService = SlLoader::cLoad('erp','erpService');
        $closingSno = \Request::request()->get('sno');
        $encodeData = DBUtil2::getOne('sl_3plStockClosing', 'sno'  ,$closingSno)['totalMemo'];
        $data = $erpService->getDecodeAllProductList($encodeData);

        $listTitle = [
            '업체명',
            '상품코드',
            '상품명',
            '옵션',
            '현재재고',
            '과거재고',
        ];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['pastStockCnt']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('재고현황_'.date('Y-m-d'),$listTitle,$excelBody);
    }

    public function simpleExcelDownload($getData, $title){
        $type = \Request::request()->get('type');
        $fncName = 'simpleExcelDownload'.$type;
        $this->$fncName();
    }

    public function simpleExcelDownload1($getData, $title){

        //$request = \Request::request()->get('type'); //TODO
        $erpService = SlLoader::cLoad('erp','erpService');
        $closingSno = \Request::request()->get('sno');
        $data = DBUtil2::runSelect("select b.scmName, a.thirdPartyProductCode, b.productName, b.optionName, a.quantity from sl_3plStockInOut a join sl_3plProduct b on a.productSno = b.sno where a.closingSno={$closingSno} and a.inOutType=1");

        $listTitle = [
            '업체명',
            '상품코드',
            '상품명',
            '옵션',
            '입고수량',
        ];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['quantity']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('마감_입고리스트',$listTitle,$excelBody);
    }

    public function simpleExcelDownload2($getData, $title){

        //$request = \Request::request()->get('type'); //TODO
        $erpService = SlLoader::cLoad('erp','erpService');
        $closingSno = \Request::request()->get('sno');
        $data = DBUtil2::runSelect("select b.scmName, a.thirdPartyProductCode, b.productName, b.optionName, a.quantity from sl_3plStockInOut a join sl_3plProduct b on a.productSno = b.sno where a.closingSno={$closingSno} and a.inOutType=2");

        $listTitle = [
            '업체명',
            '상품코드',
            '상품명',
            '옵션',
            '입고수량',
        ];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['quantity']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('마감_출고리스트',$listTitle,$excelBody);
    }

}
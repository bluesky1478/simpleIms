<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Erp\ControllerService\StockStatusService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class StockStatusController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->callMenu('erp', 'stock', 'stockStatus');

        //복붙하기 좋게 - Default에 재정의?
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'stockStatus');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
        $this->setData('isDev', SlCommonUtil::isDevIp());
    }

    public function simpleExcelDownload($getData){
        $downloadType = \Request::get()->get('downloadType');
        $fncName = 'simpleExcelDownload'.$downloadType;
        $this->$fncName($getData);
    }

    public function simpleExcelDownload1($getData){
        $listTitles = StockStatusService::LIST_TITLES;
        $data = $getData['data'];
        $page = $getData['page'];
        $totalStockCnt = number_format($page['totalStockCnt']);

        $htmlList = [];
        $htmlList[] = '<table class="table table-rows" border="1">';
        $title1 = [];

        foreach ($listTitles as $titleKey => $titleValue) {
            $title1[] = ExcelCsvUtil::wrapTh($titleValue,'title','background-color:#f0f0f0; font-weight:bold');
        }
        //$title1[] = '<th class="text-center" colspan='.($page['optionMaxCount']+1).'>품목 사이즈별 재고수량 <small>(단위 벌)</small></th>';
        $maxCount = $page['optionMaxCount']+1;
        $title1[] =  ExcelCsvUtil::wrapTh("품목 사이즈별 재고수량 <small>(단위 벌)</small>",'title','background-color:#f0f0f0; font-weight:bold', "colspan={$maxCount}"); // '<th class="text-center" colspan='.($page['optionMaxCount']+1).'>품목 사이즈별 재고수량 <small>(단위 벌)</small></th>';
        $htmlList[] = '<tr>'.implode('',$title1).'</tr>';
        $htmlList[] = "<tr>
                            <td colspan=2 class='text-left bg-light-gray' style='background-color:#f0f0f0; font-weight:bold'><b>Sum tt.</b></td>
                            <td class='text-center bg-light-gray' style='background-color:#f0f0f0; font-weight:bold'><b>{$totalStockCnt}</b></td>
                            <td class='text-center bg-light-gray' style='background-color:#f0f0f0; font-weight:bold' colspan={$page['optionMaxCount']}></td>
                      </tr>";
        foreach ($data as $dataKey => $val) {
            $htmlList[] = '<tr>';
            $htmlList[] = ExcelCsvUtil::wrapTd('#Type'.++$idx, '', 'background-color:#fffff2; font-weight:bold', 'colspan=2');
            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['optionTotalStockCnt']), '', 'background-color:#fffff2; font-weight:bold');
            foreach ($val['optionList'] as $optionKey => $optionName) {
                $htmlList[] = ExcelCsvUtil::wrapTd($optionName,'','background-color:#fffff2; font-weight:bold');
            }
            for ($i=count($val['optionList']); $page['optionMaxCount']>$i; $i++) {
                $htmlList[] = ExcelCsvUtil::wrapTd('','','background-color:#fffff2');
            }
            $htmlList[] = '</tr><tbody>';

            foreach ($val['data'] as $dataVal) {
                $htmlList[] = '<tr>';
                $htmlList[] = ExcelCsvUtil::wrapTd($dataVal['scmName']);
                $htmlList[] = ExcelCsvUtil::wrapTd($dataVal['productName']);
                $htmlList[] = ExcelCsvUtil::wrapTd(number_format($dataVal['productStockCnt']),'','background-color:#fffff2; font-weight:bold');
                foreach ($dataVal['optionList'] as $optionKey => $stockCnt) {
                    $htmlList[] = ExcelCsvUtil::wrapTd(number_format($stockCnt));
                }
                for ($i=count($dataVal['optionList']); $page['optionMaxCount']>$i; $i++) {
                    $htmlList[] = ExcelCsvUtil::wrapTd('');
                }
                $htmlList[] = '</tr></tbody>';
            }
        }

        $htmlList[] = '</table>';

        $excelBody =  implode('',$htmlList);

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, '재고현황_'.date('Y-m-d'));
    }

    /**
     * 심플 재고 현황
     */
    public function simpleExcelDownload2(){
        $erpService = SlLoader::cLoad('erp','erpService');
        $erpService->getTotalStockDownload();
    }

    /**
     * 대사용 리포트
     */
    public function simpleExcelDownload3(){
        $sql = "SELECT 
  a.scmNo,
  a.scmName, 
  a.thirdPartyProductCode,
  a.productName,
  a.optionName as prdOptionName,
  a.stockCnt as prdStockCnt,
  c.goodsNm,
  concat(b.optionValue1, b.optionValue2, b.optionValue3, b.optionValue4, b.optionValue5) as optionName,
  b.stockCnt,
  c.goodsNo,    
  c.goodsDisplayFl,    
  c.goodsSellFl    
  FROM sl_3plProduct a
    JOIN es_goodsOption b 
	  ON a.thirdPartyProductCode = b.optionCode 
    JOIN es_goods c 
      ON b.goodsNo = c.goodsNo
   WHERE c.delFl = 'n' 
 ORDER BY c.goodsNo, b.sno";

        $data = DBUtil2::runSelect($sql);

        $title = [
            '고객사명',
            '상품코드',
            '상품명',
            '옵션',
            '재고',
            '3PL코드',
            '3PL상품명',
            '3PL옵션명',
            '3PL재고',
            '판매여부',
            '노출여부',
        ];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['prdOptionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['prdStockCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd('y'===$val['goodsSellFl']?'<span style="color:darkgreen">예</span>':'<span style="color:red">아니오</span>');
            $fieldData[] = ExcelCsvUtil::wrapTd('y'===$val['goodsDisplayFl']?'<span style="color:darkgreen">예</span>':'<span style="color:red">아니오</span>');
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $date = date('Y-m-d');
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('이노버_3PL_상품_대사자료_'.$date,$title,$excelBody);

    }

}
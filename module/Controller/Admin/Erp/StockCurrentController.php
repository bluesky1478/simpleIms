<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Controller\Admin\Erp\ControllerService\StockCurrentService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 문서 리스트
 */
class StockCurrentController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->callMenu('erp', 'stock', 'stockCurrent');

        //scmFl=y&scmNo%5B%5D=2

        $get = \Request::get()->toArray();
        if(!isset($get['scmFl'])){
            \Request::request()->set('scmFl','y');
            \Request::request()->set('scmNo','-1');
            \Request::request()->set('scmNoNm',['삼성전자(주)']);
        }

        //복붙하기 좋게 - Default에 재정의?
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'stockCurrent');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
        $this->setData('isDev', SlCommonUtil::isDevId());
    }

    public function simpleExcelDownload($getData){
        $downloadType = \Request::get()->get('downloadType');
        $fncName = 'simpleExcelDownload'.$downloadType;
        $this->$fncName($getData);
    }


    public function simpleExcelDownload1($getData){

        $isSale = false;

        $listTitles = StockCurrentService::LIST_TITLES;

        $data = $getData['data'];
        $page = $getData['page'];
        $totalStockCnt = number_format($page['totalStockCnt']);

        $htmlList = [];
        $htmlList[] = '<table class="table table-rows" border="1">';

        $htmlList[] = '<tr>';
        foreach ($listTitles as $titleKey => $titleValue) {
            $htmlList[] = ExcelCsvUtil::wrapTh($titleValue,'title','background-color:#f0f0f0; font-weight:bold');
        }
        $htmlList[] = '</tr>';

        foreach ($data as $dataKey => $val) {
            $saleGoodsTable = [];

            $htmlList[] = '<tr>';

            $htmlList[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['productName']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['optionName']);

            $htmlList[] = ExcelCsvUtil::wrapTd($val['attr1']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['attr2']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['attr3']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['attr4']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['attr5']);

            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt']));
            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['saleCnt']));
            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['waitCnt']));

            $htmlList[] = ExcelCsvUtil::wrapTd( number_format($val['stockCnt'] - ($val['saleCnt'] + $val['waitCnt']) ) );


            foreach( $val['saleGoodsList'] as $saleGoods ) {
                $saleGoodsTable[] = '<tr>';
                $saleStr = '판매:'.('y'==$saleGoods['goodsSellFl']?'Y':'N'). ', 노출:'.('y'==$saleGoods['goodsDisplayFl']?'Y':'N') ;
                $saleGoodsTable[] = ExcelCsvUtil::wrapTd($saleGoods['goodsNo'],'','font-size:12px; color:#a1a1a1');
                $saleGoodsTable[] = ExcelCsvUtil::wrapTd($saleGoods['goodsNm']. ' ' .$saleGoods['optionName']);
                $saleGoodsTable[] = ExcelCsvUtil::wrapTd(number_format($saleGoods['stockCnt']));
                $saleGoodsTable[] = ExcelCsvUtil::wrapTd($saleStr,'','font-size:12px; color:#a1a1a1');
                $saleGoodsTable[] = '</tr>';
            }

            $saleGoodsTableStr = '';
            if( $isSale && !empty($saleGoodsTable) ){
                $saleGoodsTableStr = '<table>'.implode('',$saleGoodsTable).'</table>';
            }
            $htmlList[] = ExcelCsvUtil::wrapTd($saleGoodsTableStr);

            $htmlList[] = '</tr>';
        }

        $htmlList[] = '</table>';

        $excelBody =  implode('',$htmlList);

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, '재고현황리스트_'.date('Y-m-d'));
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
<?php

namespace Controller\Admin\Order;

use App;
use Component\Claim\ClaimListService;
use Component\Claim\ClaimService;
use Component\Claim\ReturnListService;
use Component\Stock\StockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class ReturnListController extends \Controller\Admin\Controller{

    public function index(){

        $this->callMenu('order', 'order', 'return_list');
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $controllerService = SlLoader::cLoad('godo','controllerService','sl');
        $controllerService->setReturnListData($this);

        $this->setData('adminTitle', ReturnListService::LIST_TITLES_ADMIN);

    }

    public function simpleExcelDownload($getData){
        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['claimRegDt']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['claimTypeKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['nickNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['masterCellPhone']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['bdSno']);
            
            $claimGoodsHtml = "<table>";
            foreach( $val['claimGoods'] as $claimGoods ){
                foreach( $claimGoods['option'] as $claimOption ){
                    $claimGoodsHtml .= "<tr><td>{$claimOption['optionCode']}</td><td>{$claimGoods['goodsNm']}";
                    if(!empty($claimOption['optionName'])) {
                        $claimGoodsHtml .= $claimOption['optionName'];
                    }
                    $claimGoodsHtml .= "</td>";
                    $claimGoodsHtml .= "<td>".number_format($claimOption['optionCnt'])."</td></tr>";
             }
            }
            $claimGoodsHtml .= "</table>";
            
            $exchangeGoodsHtml = "<table>";
            foreach( $val['exchangeGoods'] as $exchangeGoods ){
                foreach( $exchangeGoods['goodsOptionList'] as $claimOption ){
                    $exchangeGoodsHtml .= "<tr><td>{$claimOption['optionCode']}</td><td>{$exchangeGoods['goodsNm']}";
                    if(!empty($claimOption['optionName'])) {
                        $exchangeGoodsHtml .= $claimOption['optionName'];
                    }
                    $exchangeGoodsHtml .= "</td>";
                    $exchangeGoodsHtml .= "<td>".number_format($claimOption['optionCount'])."</td></tr>";
             }
            }
            $exchangeGoodsHtml .= "</table>";

            $fieldData[] = ExcelCsvUtil::wrapTd($claimGoodsHtml);
            $fieldData[] = ExcelCsvUtil::wrapTd($exchangeGoodsHtml);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['claimStatusKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['refundData']['refundTypeKr']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('클레임리스트',ClaimListService::EXCEL_LIST_TITLES,$excelBody);
    }

}
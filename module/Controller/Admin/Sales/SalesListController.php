<?php

namespace Controller\Admin\Sales;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Database\DBTableField;
use Component\Stock\StockListService;
use Component\Work\WorkCodeMap;
use Controller\Admin\Erp\AdminErpControllerTrait;
use Controller\Admin\Erp\ControllerService\InoutListService;
use Controller\Admin\Sales\ControllerService\SalesListService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Recap\RecapService;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * Recap Customer
 */
class SalesListController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $request = \Request::get()->toArray();
        $status = empty($request['step']) ? 'all' : 'step'.$request['step'];
        $this->callMenu('sales', 'customer', $status);

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'salesList');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
        $this->setData('isDev', SlCommonUtil::isDevIp());
        $this->setData('adminHost', SlCommonUtil::getAdminHost());
        $this->setData('listTitles', SalesListService::LIST_TITLE);
        $this->setData('listDataDetail', SalesListService::LIST_TITLE_DETAIL);

        $recapFileField = DBTableField::tableRecapPrdFile();
        $salesFile = [];
        foreach($recapFileField as $fileKey => $fieldValue){
            if( strpos($fieldValue['val'],'filePrd')!==false ){
                $salesFile[] = $fieldValue;
            }
        }
        $this->setData('salesFile', $salesFile);
    }

    public function simpleExcelDownload($getData){
        $LIST_TITLES = SalesListService::LIST_TITLE_EXCEL;
        $listDataKey = SalesListService::LIST_TITLE_DETAIL;
        unset($listDataKey[12]);
        $data = $getData['data'];
        $page = $getData['page'];
        $excelBody = '';

        foreach ($data as $key => $val) {
            $fieldData = array();
            foreach( $listDataKey as $listKey => $listIdx ) {
                if( is_numeric($val[$listIdx['top'][1]]) ){
                    $fieldData[] = ExcelCsvUtil::wrapTd($val[$listIdx['top'][1]]);
                }else{
                    $fieldData[] = ExcelCsvUtil::wrapTd($val[$listIdx['top'][1]],'text','mso-number-format:\'\@\'');
                }
            }
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('이노버_영업리스트_'.date('ymd'),$LIST_TITLES,$excelBody);
    }


}
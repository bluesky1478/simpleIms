<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Controller\Admin\Erp\ControllerService\StockCurrentService;
use Controller\Admin\Erp\ControllerService\StockTableService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;

/**
 * 문서 리스트
 */
class StockTableController extends \Controller\Admin\Controller{

    use AdminErpControllerTrait;

    public function workIndex(){
        $this->setData('isDev', SlCommonUtil::isDevIp());
        $isProvider = Manager::isProvider();
        $this->setData('isProvider', $isProvider);
        if( $isProvider ){
            $this->callMenu('statistics', 'scm', 'stockTable');
        }else{
            $this->callMenu('erp', 'stock', 'stockTable');
        }

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'stockTable');
        $listService = SlLoader::cLoad('godo','listService','sl');
        $listService->setList($controllerListService, $this);
        //gd_debug($option);

        //ScmList : 분류 완료된 고객사만.
        $this->setData('scmList', [
            6 => '한국타이어',
            8 => 'TKE(티센크루프)',
        ]);


        //$listService->setList($controllerListService, $this);
        $this->setListAfterData(null);

        $this->getView()->setPageName('erp/stock_table.php');
    }

    public function setListAfterData($getData){
        if( empty($getData) ){
            $searchData = $this->getData('search');
        }else{
            $searchData = $getData['search'];
        }
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'stockTable');
        $scmNo = $searchData['scmNo'];
        $optionList = $controllerListService->getDefaultOptionList($scmNo);
        $this->setData('optionList', $optionList['top']);
        $this->setData('summaryTitles',$controllerListService->getSummaryTitle($searchData));
        $this->setData('optionTitles',$controllerListService->getOptionTitle($searchData));
        $this->setData('summaryField',$controllerListService->getSummaryField($scmNo));
    }

    public function simpleExcelDownloadDetail($getData){
        $getToArray = \Request::get()->toArray();
        $listAllData = $getData['totalData'];
        //gd_debug('데이터 확인.....');
        //gd_debug($listAllData['outHistory']);

        $excelBody = '';
        foreach($listAllData['outHistory'] as $index => $val) {
            $htmlList = [];
            $htmlList[] = '<tr>';
            $htmlList[] = ExcelCsvUtil::wrapTd($val['inOutDate']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $htmlList[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['address']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $htmlList[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['productName']);
            $htmlList[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['quantity']));
            $htmlList[] = '</tr>';
            $excelBody .=  implode('', $htmlList);
        }

        $title = [
            '출고일자',
            '주문번호',
            '수령자',
            '주소',
            '송장번호',
            '제품코드',
            '제품명',
            '옵션',
            '수량',
        ];
        $date = date('Y-m-d');
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('출고상세_'.$date,$title,$excelBody);

    }

    public function simpleExcelDownload($getData){
        $this->setListAfterData($getData);
        $summaryTitles = $this->getData('summaryTitles');
        $optionTitles = $this->getData('optionTitles');
        $listAllData = $getData['totalData'];
        $optionList = $this->getData('optionList');
        $data = $getData['data'];
        $checked = $getData['checked'];

        $htmlList = [];

        $htmlList[] = '<table class="table table-rows" border="1">';

        //TITLE
        $htmlList[] = '<tr>';
        foreach ($summaryTitles as $titleValue) {
            $htmlList[] =ExcelCsvUtil::wrapTh($titleValue);
        }
        $htmlList[] = ExcelCsvUtil::wrapTh("TOTAL",null,"color:red");
        foreach ($optionTitles as $titleValue) {
            $htmlList[] =ExcelCsvUtil::wrapTh($titleValue);
        }
        $htmlList[] = '</tr>';

        //출고만
        $onlyOut = false;

        //List Data
        foreach($data as $index => $val) {

            //기본 현재고
            if( !$onlyOut ){
                $htmlList[] = '<tr>';
                foreach($summaryTitles as $listTitleKey => $listTitle) {
                    $htmlList[] = ExcelCsvUtil::wrapTd($val['info']['attr'.$listTitleKey],null,"background: #fafafa"); //Summary
                }
                $htmlList[] = ExcelCsvUtil::wrapTd(number_format($listAllData['keyStockCnt'][$index]),null,"color:red"); //TOTAL
                foreach($optionList as $optionValue) {
                    $htmlList[] = ExcelCsvUtil::wrapTd(number_format($val['stockCnt'][$optionValue])); //Stock.
                }
                $htmlList[] = '</tr>';
            }


            if (empty($checked['isViewMode']['all'])) {

                if( !$onlyOut ){
                    //출고비율
                    $htmlList[] = '<tr>';
                    foreach($summaryTitles as $listTitleKey => $listTitle) {
                        $htmlList[] = ExcelCsvUtil::wrapTd($val['info']['attr'.$listTitleKey],null, "background-color:#fff6e0; color:#aaaaaa"); //Summary
                    }
                    $htmlList[] = ExcelCsvUtil::wrapTd("출고비율",null, "text-align:right; background-color:#fff6e0");
                    foreach($optionList as $optionValue) {
                        if(!empty($listAllData['outTotalCnt'][$index][$optionValue])) {
                            $ratio = round($listAllData['outTotalCnt'][$index][$optionValue]  / $listAllData['outTotalCnt'][$index]['total']*100);
                        }else{
                            $ratio = 0;
                        }
                        $htmlList[] = ExcelCsvUtil::wrapTd($ratio.'%',null, "background-color:#fff6e0");
                    }
                    $htmlList[] = '</tr>';

                    //출고요약
                    $htmlList[] = '<tr>';
                    foreach($summaryTitles as $listTitleKey => $listTitle) {
                        $htmlList[] = ExcelCsvUtil::wrapTd($val['info']['attr'.$listTitleKey],null, "background-color:#fff6e0; color:#aaaaaa"); //Summary
                    }

                    $totalOutStockCount = number_format($listAllData['outTotalCnt'][$index]['total']);
                    $htmlList[] = ExcelCsvUtil::wrapTd("({$totalOutStockCount}) 총출고수량", null, "text-align:right; background-color:#fff6e0");
                    foreach($optionList as $optionValue) {
                        $fieldTotal = number_format($listAllData['outTotalCnt'][$index][$optionValue]);
                        $htmlList[] = ExcelCsvUtil::wrapTd($fieldTotal,null, "background-color:#fff6e0");
                    }
                    $htmlList[] = '</tr>';
                }

                if ( !empty($checked['isViewMode']['2']) ) {
                    foreach($listAllData['outCnt'][$index] as $outKey => $outData) {
                        $htmlList[] = '<tr>';
                        foreach($summaryTitles as $listTitleKey => $listTitle) {
                            $htmlList[] = ExcelCsvUtil::wrapTd($val['info']['attr'.$listTitleKey],null, "background-color:#FFFFF2; color:#aaaaaa"); //Summary
                        }
                        $monthTitle = '(' . number_format($outData['total']) . ') ' . gd_date_format('y년m월',$outKey);
                        $htmlList[] = ExcelCsvUtil::wrapTd($monthTitle, null, "text-align:right; background-color:#FFFFF2");

                        foreach($optionList as $optionValue) {
                            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($outData[$optionValue]), null, "text-align:right; background-color:#FFFFF2");
                        }
                        $htmlList[] = '</tr>';
                    }
                }
                if ( !empty($checked['isViewMode']['3']) ) {
                    foreach($listAllData['outCnt'][$index] as $outKey => $outData) {
                        $htmlList[] = '<tr>';
                        foreach($summaryTitles as $listTitleKey => $listTitle) {
                            $htmlList[] = ExcelCsvUtil::wrapTd($val['info']['attr'.$listTitleKey],null, "background-color:#FFFFF2; color:#aaaaaa"); //Summary
                        }

                        //$monthTitle = '(' . number_format($outData['total']) . ') ' . $outKey.'년';
                        $monthTitle = $outKey.'년';

                        $htmlList[] = ExcelCsvUtil::wrapTd($monthTitle, null, "text-align:right; background-color:#FFFFF2");

                        foreach($optionList as $optionValue) {
                            $htmlList[] = ExcelCsvUtil::wrapTd(number_format($outData[$optionValue]), null, "text-align:right; background-color:#FFFFF2");
                        }
                        $htmlList[] = '</tr>';
                    }
                }
            }
        }

        //gd_debug($listAllData['outTotalCnt']);
        //gd_debug($listAllData['outCnt']);

        $htmlList[] = '</table>';
        $excelBody =  implode('',$htmlList);
        //gd_debug($excelBody);
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($excelBody, '고객사재고현황_'.date('Y-m-d'));
    }

}
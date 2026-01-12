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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

/**
 * 마감 등록
 */
class ClosingRegController extends \Controller\Admin\Controller{
    public function index(){
        $this->callMenu('erp', 'stock', 'stockClosing');
        $erpService = SlLoader::cLoad('erp','erpService');
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        $this->setData('requestUrl2', basename(\Request::getPhpSelf()) .'?simple_excel_download=2&'.  \Request::getQueryString() );
        $getValue = \Request::get()->toArray();

        $closingTargetData = $erpService->getClosingInoutListSearchOption();
        //$lastClosingDate = gd_isset($erpService->getLastClosingDate(), '2019-01-01');
        //gd_debug($lastClosingDate);

        $title = [
            '구분',
            '입/출고일',
            '사유',
            '품목코드',
            '품목명',
            '옵션',
            '수량',
            '고객사',
        ];

        $page = gd_isset(\Request::get()->get('page'),1);
        $pageNum = 150;
        if(  !empty($getValue['simple_excel_download'])  ){
            $page = 1;
            $pageNum = 10000;
        }
        $closingTargetData['page'] = $page;
        $closingTargetData['pageNum'] = $pageNum;
        $closingTargetData['sort'] = ' a.inOutDate, a.inOutType, a.thirdPartyProductCode ';
        $lastClosingDate = $closingTargetData['lastClosingDate'];
        $list = $erpService->getSummaryInOutList($closingTargetData);
        $countData = $erpService->getInOutStockCount($closingTargetData);

        //gd_debug($list['pageData']->page['total']);
        //gd_debug($list['listData'][0]);

        $this->setData('page', $list['pageData']);
        $this->setData('listData', $list['listData']);
        $this->setData('lastClosingDate', $lastClosingDate);
        $this->setData('title', $title);

        $this->setData('totalStockCount', number_format($erpService->getStockCount()));

        $this->setData('inputStockCount', number_format($countData['inStockCnt']));
        $this->setData('outStockCount', number_format($countData['outStockCnt']));
        $this->setData('totalInOutCount', number_format($countData['totalStockCnt']));


        //TODO : 1만 로우 이상 다운로드 관리자 문의 (성능에 문제될 수 있음) 기능 추가
        if(  !empty($getValue['simple_excel_download'])  ){
            if( 1 == $getValue['simple_excel_download'] ){
                $this->simpleExcelDownload1($list, $title);
            }else{
                $this->simpleExcelDownload2();
            }
            exit();
        }
    }

    public function simpleExcelDownload1($getData, $title){
        $data = $getData['listData'];
        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutDate']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutTypeKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutReasonKr']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['thirdPartyProductCode']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['quantity']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['scmName']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $date = date('Y-m-d');
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('마감대기_입출고리스트_'.$date,$title,$excelBody);
    }

    /**
     * 전체 재고 다운로드
     */
    public function simpleExcelDownload2(){
        $erpService = SlLoader::cLoad('erp','erpService');
        $erpService->getTotalStockDownload();
    }

}
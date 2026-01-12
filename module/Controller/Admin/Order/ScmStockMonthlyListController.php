<?php

namespace Controller\Admin\Order;

use App;
use Component\Order\OrderPolicyListService;
use Component\Scm\ScmOrderListService;
use Component\Scm\ScmStockListService;
use Component\Scm\ScmStockMonthlyListService;
use Component\Stock\StockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;

class ScmStockMonthlyListController extends \Controller\Admin\Controller{
    public function index(){
        $getValue = Request::get()->toArray();

        //공급사 리스트
        $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
        $scmList = $scmAdmin->getSelectScmList();
        foreach($scmList as $scmKey => $scmValue){
            $firstScmNo = $scmKey;
            $firstScmName = $scmValue;
            break;
        }

        $isProvider = Manager::isProvider();
        if( $isProvider ){
            $this->callMenu('statistics', 'scm', 'goodsCountMonthly');
            $scmNo = \Session::get('manager.scmNo');
            $companyNm = \Session::get('manager.companyNm');
        }else{
            $this->callMenu('order', 'order', 'stock_monthly_list');
            $scmNo = empty($getValue['scmNo'][0]) ? $firstScmNo : $getValue['scmNo'][0];
            $companyNm = empty($getValue['scmNo'][0]) ?  $firstScmName : $scmList[$getValue['scmNo'][0]];
        }

        $this->setData('scmList', $scmList);
        $this->setData('isProvider', $isProvider);
        $this->setData('scmNo', $scmNo);
        $this->setData('companyNm', $companyNm);

        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $scmStockListService = SlLoader::cLoad('Scm','ScmStockMonthlyListService');

        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue); //미사용
        //gd_debug($getValue);
        $getValue['scmFl'] =  1;
        $getValue['scmNo'][] =  $scmNo;
        $getValue['scmNoNm'][] =  $companyNm;
        //gd_debug($getValue);

        $getData = $scmStockListService->getList($getValue);

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);
        //타이틀
        $this->setData('listTitles',ScmStockMonthlyListService::LIST_TITLES);
        $this->setData('maxOptionCnt',$getData['maxOptionCnt']);
        $this->setData('monthlyData',$getData['monthlyData']);

        //리스트 데이터
        $this->setData('data',$getData['data']);
        $this->setData('scmNo',$scmNo);
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        $this->setData('orderStatusMap',SlCommonUtil::getOrderStatusAllMap());

        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

        $goodsList = DBUtil::getListBySearchVo(DB_GOODS, new SearchVo(['scmNo=?','delFl=?'],[$scmNo,'n']));
        $goodsMap = SlCommonUtil::arrayAppKeyValue($goodsList, 'goodsNo', 'goodsNm');
        $this->setData('goodsMap', $goodsMap);

        $this->getView()->setPageName('order/scm_stock_monthly_list.php');
    }

    /**
     * 엑셀 다운로드 체크하고 get 값 정제하여 반환
     * @param $getValue
     * @return mixed
     */
    public function getRefineValueAndExcelDownCheck($getValue){
        if(  !empty($getValue['simple_excel_download'])  ){
            $getValue['pageNum'] = 10000;
            $getValue['page'] = 1;
        }
        return $getValue;
    }

    public function simpleExcelDownload($getData){
        $maxOptionCnt = $getData['maxOptionCnt'];
        $data = $getData['data'];
        $page = $getData['page'];
        $startDate = substr($getData['search']['searchDate'][0],0,10);
        $startDate = gd_date_format('y년m월',substr($startDate,0,7));
        $endDate = substr($getData['search']['searchDate'][1],0,10);
        $endDate = gd_date_format('y년m월',substr($endDate,0,7));

        $excelBody = '';
        foreach (ScmStockMonthlyListService::LIST_TITLES as $titleKey => $title) {
            if( $titleKey == (count(ScmStockMonthlyListService::LIST_TITLES)-1) ){
                $excelBody .= ExcelCsvUtil::wrapTh($title,'title',null, 'colspan=' . $maxOptionCnt );
            }else{
                $excelBody .= ExcelCsvUtil::wrapTh($title);
            }
        }

        $rowspanCnt = 4;
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--, null, 'text-align:center', 'rowspan='.(count($val['monthlyData'])+$rowspanCnt));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm'], null, null, 'rowspan='.(count($val['monthlyData'])+$rowspanCnt));
            $fieldData[] = ExcelCsvUtil::wrapTd('<b>옵션</b>' , null, 'background-color:#f0f0f0');
            foreach( $val['optionList'] as $optionData ) {
                $fieldData[] = ExcelCsvUtil::wrapTd('<b>'.$optionData['optionName'].'</b>', null, 'background-color:#f0f0f0');
            }
            $this->setBlankTd($fieldData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
            //gd_debug( $fieldData );

            //월별출고수량
            foreach ( $val['monthlyData'] as $monthlyKey => $monthlyValue ) {
                $monthlyCntData = array();
                $monthlyCntData[] = ExcelCsvUtil::wrapTd($monthlyKey, null, 'background-color:#fffff22');
                foreach( $val['optionList'] as $optionData ) {
                    $monthlyCntData[] = ExcelCsvUtil::wrapTd(number_format($monthlyValue[$optionData['optionNo']]['stockOut']));
                }
                $this->setBlankTd($monthlyCntData, $maxOptionCnt, $val['optionList']);
                $excelBody .=  "<tr>". implode('',$monthlyCntData) . "</tr>";
            }

            //기간합계
            $totalCntData = array();
            $totalCntData[] = ExcelCsvUtil::wrapTd('기간합계', null, 'background-color:#fffff2');
            foreach( $val['optionList'] as $optionData ) {
                $totalCntData[] = ExcelCsvUtil::wrapTd(number_format($optionData['totalStockCnt']), null, 'background-color:#fffff2');
            }
            $this->setBlankTd($totalCntData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$totalCntData) . "</tr>";

            //기간비율
            $stockPercentData = array();
            $stockPercentData[] = ExcelCsvUtil::wrapTd('기간비율', null, 'background-color:#fffff2');
            foreach( $val['optionList'] as $optionData ) {
                $stockPercentData[] = ExcelCsvUtil::wrapTd('<b>'.number_format($optionData['totalStockPercent']).'%</b>', null, 'background-color:#fffff2');
            }
            $this->setBlankTd($stockPercentData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$stockPercentData) . "</tr>";

            //현재수량
            $currentCntData = array();
            $currentCntData[] = ExcelCsvUtil::wrapTd('현재수량', null, 'background-color:#fffff2');
            foreach( $val['optionList'] as $optionData ) {
                $currentCntData[] = ExcelCsvUtil::wrapTd('<b>'.number_format($optionData['currentCnt']).'</b>', null, 'background-color:#fffff2');
            }
            $this->setBlankTd($currentCntData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$currentCntData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');

        $topTitleColspan = $maxOptionCnt + count(ScmStockMonthlyListService::LIST_TITLES)-1;
        $totalBody = "<table border='1'>";
        $totalBody .= "<tr><td colspan='{$topTitleColspan}' style='font-size:20px;font-weight: bold;text-align: center; '>월별 출고 현황 {$startDate} ~ {$endDate} </td></tr>";
        $totalBody .= $excelBody;
        $totalBody .= "</table>";

        $simpleExcelComponent->downloadCommon($totalBody, '월별출고현황_'.$startDate.'∽'.$endDate);


    }

    public function setBlankTd(&$array ,$maxOptionCnt, $optionList){
        if ( $maxOptionCnt > count($optionList) ) {
            for ($forIdx = 0; $forIdx < ($maxOptionCnt - count($optionList)); $forIdx++) {
                $array[] = ExcelCsvUtil::wrapTd('');
            }
        }
    }

}
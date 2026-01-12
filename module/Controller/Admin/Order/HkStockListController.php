<?php

namespace Controller\Admin\Order;

use App;
use Component\Claim\ClaimListService;
use Component\Order\OrderPolicyListService;
use Component\Scm\HkStockListService;
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

class HkStockListController extends \Controller\Admin\Controller{
    public function index(){
        $getValue = Request::get()->toArray();

        $getData = $this->setDefault();

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //타이틀
        $this->setData('listTitles',HkStockListService::LIST_TITLES);
        //리스트 데이터

        //gd_debug($getData['total']);
        /*$getData['data'] = $resultList2;
        $getData['span'] = $seasonCntMap;
        $getData['seasonTotal'] = $seasonTotalMap;
        $getData['total'] = $totalList;*/

        //$this->setData('data',$getData['data']);
        foreach($getData as $key => $value){
            $this->setData($key,$value);
        }

        $this->setData('scmNo',6);
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

        $this->getView()->setPageName('order/hk_stock_list.php');
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
        $data = $getData['data'];
        $span = $getData['span'];
        $seasonTotal = $getData['seasonTotal'];
        $total = $getData['total'];

        $excelBody = '';
        foreach ($data as $key => $val) {
            if( 'y' === $val['seasonRowspan'] && 0 != $key ) {
                $fieldData = []; //Reset
                $fieldData[] = ExcelCsvUtil::wrapTd('소계','center', 'background-color:#dfdfdf; text-align:center', 'colspan=3');
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[$key-1]['season']]['pastCnt']),'center', 'background-color:#dfdfdf; text-align:center');
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[$key-1]['season']]['lastPastCnt']),'center', 'background-color:#dfdfdf; text-align:center');
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[$key-1]['season']]['currentCnt']),'center', 'background-color:#dfdfdf; text-align:center');
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[$key-1]['season']]['stockCnt']),'center', 'background-color:#dfdfdf; text-align:center');
                $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[$key-1]['season']]['accSaleCnt'] + $seasonTotal[$data[$key-1]['season']]['stockCnt']),'center', 'background-color:#dfdfdf; text-align:center');
                $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
                $fieldData = []; //Reset..
            }else{
                $fieldData = [];
            }


            //public static function wrapTd($str , $class=null, $style=null, $etcTag=null){
            if( 'y' === $val['seasonRowspan'] ) {
                $fieldData[] = ExcelCsvUtil::wrapTd($val['season'],'text-center','background-color:#f0f0f0;text-align:center','rowspan='.$span[$val['season']]['seasonSpan']);
            }
            if( 'y' === $val['channelRowspan'] ) {
                $fieldData[] = ExcelCsvUtil::wrapTd($val['channel'],'center','background-color:#f7f7f7;text-align:center','rowspan='.$span[$val['season']]['channelSpan'][$val['channel']]);
            }
            $bgColor = ('y' === $val['isMain']) ? 'background-color:#f7f7f7' : '';
            $fieldData[] = ExcelCsvUtil::wrapTd($val['type'],'center', $bgColor);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format( $val['pastCnt']), null, $bgColor);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format( $val['lastPastCnt']), null, $bgColor);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format( $val['currentCnt']), null, $bgColor);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format( $val['stockCnt']), null, $bgColor);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format( $val['accSaleCnt'] + $val['stockCnt'] ), null, $bgColor);

            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }

        $fieldData = [];
        $fieldData[] = ExcelCsvUtil::wrapTd('소계','center', 'background-color:#dfdfdf; text-align:center', 'colspan=3');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[count($data)-1]['season']]['pastCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[count($data)-1]['season']]['lastPastCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[count($data)-1]['season']]['currentCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[count($data)-1]['season']]['stockCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($seasonTotal[$data[count($data)-1]['season']]['accSaleCnt'] + $seasonTotal[$data[$valKey-1]['season']]['stockCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";

        $fieldData = [];
        $fieldData[] = ExcelCsvUtil::wrapTd('합계','center', 'background-color:#dfdfdf; text-align:center', 'colspan=3');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($total['pastCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($total['lastPastCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($total['currentCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($total['stockCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $fieldData[] = ExcelCsvUtil::wrapTd(number_format($total['accSaleCnt'] + $total['stockCnt']),'center', 'background-color:#dfdfdf; text-align:center');
        $excelBody .=  "<tr style='font-weight: bold'>". implode('',$fieldData) . "</tr>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('한국타이어_재고표',HkStockListService::LIST_TITLES,$excelBody);
    }

    public function setDefault(){
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
            $this->callMenu('statistics', 'scm', 'hkStock');
            $scmNo = \Session::get('manager.scmNo');
            $companyNm = \Session::get('manager.companyNm');
        }else{
            $this->callMenu('order', 'order', 'hkStock');
            //$scmNo = empty($getValue['scmNo'][0]) ? $firstScmNo : $getValue['scmNo'][0];
            $scmNo = 6;
            $companyNm = empty($getValue['scmNo'][0]) ?  $firstScmName : $scmList[$getValue['scmNo'][0]];
        }

        $this->setData('scmList', $scmList);
        $this->setData('isProvider', $isProvider);
        $this->setData('scmNo', $scmNo);
        $this->setData('companyNm', $companyNm);
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $getValue = Request::get()->toArray();
        //gd_debug($getValue);

        $scmStockListService = SlLoader::cLoad('Scm','HkStockListService');

        $getValue['scmFl'] =  1;
        $getValue['scmNo'][] =  $scmNo;
        $getValue['scmNoNm'][] =  $companyNm;
        //gd_debug($getValue);

        return $scmStockListService->getList($getValue);
    }

}
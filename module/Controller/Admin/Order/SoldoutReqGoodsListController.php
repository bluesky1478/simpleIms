<?php

namespace Controller\Admin\Order;

use App;
use Component\Claim\ClaimListService;
use Component\Claim\ClaimService;
use Component\Member\Util\MemberUtil;
use Component\Order\SoldoutReqGoodsListService;
use Component\Order\SoldoutReqListService;
use Component\Scm\ScmStockMonthlyListService;
use Component\Stock\StockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class SoldoutReqGoodsListController extends \Controller\Admin\Controller{

    use ApiTrait;

    public function index(){

        $scmService = SlLoader::cLoad('godo','scmService','sl');

        $getValue = Request::get()->toArray();
        $this->setData('requestParam', $getValue);
        if( 1 === count($getValue['scmNo']) ){
            $mainCategory = $scmService->getScmCategoryCode($getValue['scmNo'][0]);
            if( !empty($mainCategory) ){
                $cateList = DBUtil2::runSelect("select * from es_categoryGoods where cateCd like '{$mainCategory}%' and cateCd <> '{$mainCategory}' ");
                if( !empty($cateList) ){
                    $this->setData('categoryList', SlCommonUtil::arrayAppKeyValue($cateList, 'cateCd','cateNm'));
                }
            }
        }

        $this->callMenu('order', 'order', 'soldout_req_list2');
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $soldOutReqListService = \App::load('\\Component\\Order\\SoldoutReqGoodsListService');

        //TODO : 1만 로우 이상 다운로드 관리자 문의 (성능에 문제될 수 있음) 기능 추가
        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);

        if(  !empty($getValue['simple_excel_download'])  ){
            $getData = $soldOutReqListService->getList($getValue, 'goods');
            $this->simpleExcelDownload($getData);
            exit();
        }else{
            $getData = $soldOutReqListService->getList($getValue);
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);
        //최대옵션 카운트
        $this->setData('maxOptionCount', $getData['maxOptionCount']);
        //옵션별 신청 맵
        $this->setData('optionReqMap', $getData['optionReqMap']);

        //타이틀
        $this->setData('listTitles',SoldoutReqGoodsListService::LIST_TITLES);
        //리스트 데이터
        $this->setData('data',$getData['data']);

        //맵
        $this->setData('soldoutReqSendType',SlCodeMap::SOLDOUT_REQ_SEND_TYPE);
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
    }

    //TODO : 엑셀 다운로드 만들기
    public function simpleExcelDownload($getData){

        $maxOptionCnt = $getData['maxOptionCount'];
        $optionReqMap = $getData['optionReqMap'];
        $data = $getData['data'];
        $page = $getData['page'];

        $excelBody = '<tr>';
        foreach (SoldoutReqGoodsListService::EXCEL_LIST_TITLES as $titleKey => $title) {
            if( $titleKey == (count(SoldoutReqGoodsListService::EXCEL_LIST_TITLES)-1) ){
                $excelBody .= ExcelCsvUtil::wrapTh($title,'title',null, 'colspan=' . ($maxOptionCnt+1) );
            }else{
                $excelBody .= ExcelCsvUtil::wrapTh($title);
            }
        }
        $excelBody .= '</tr>';

        $rowspanCnt = 3;

        foreach ($data as $key => $val) {
            $fieldData = [];

            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--, null, 'text-align:center', 'rowspan=' . (count($val['monthlyData']) + $rowspanCnt));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm'], null, null, 'rowspan=' . $rowspanCnt);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo'], null, null, 'rowspan=' . $rowspanCnt);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm'], null, null, 'rowspan=' . $rowspanCnt);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['reqCnt']), null, null, 'rowspan=' . $rowspanCnt);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['sendCnt']), null, null, 'rowspan=' . $rowspanCnt);

            //옵션명
            $fieldData[] = ExcelCsvUtil::wrapTd('옵션' , null, 'background-color:#f0f0f0');
            foreach( $val['optionList'] as $optionData ) {
                $fieldData[] = ExcelCsvUtil::wrapTd($optionData['optionFullName'], null, 'background-color:#f0f0f0');
            }
            $this->setBlankTd($fieldData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";

            //신청수량
            $reqData = [];
            $reqData[] = ExcelCsvUtil::wrapTd('신청수량' , null, 'background-color:#f0f0f0');
            foreach( $val['optionList'] as $optionData ) {
                $reqData[] = ExcelCsvUtil::wrapTd(number_format($optionReqMap[$val['goodsNo']][$optionData['sno']]));
            }
            $this->setBlankTd($reqData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$reqData) . "</tr>";

            //현재 재고
            $currentData = [];
            $currentData[] = ExcelCsvUtil::wrapTd('현재재고' , null, 'background-color:#f0f0f0');
            foreach( $val['optionList'] as $optionData ) {
                $currentData[] = ExcelCsvUtil::wrapTd(number_format($optionData['stockCnt']));
            }
            $this->setBlankTd($currentData, $maxOptionCnt, $val['optionList']);
            $excelBody .=  "<tr>". implode('',$currentData) . "</tr>";


        }

        $topTitleColspan = $maxOptionCnt + count(SoldoutReqGoodsListService::EXCEL_LIST_TITLES);

        $totalBody = "<table border='1'>";
        $totalBody .= "<tr><td colspan='{$topTitleColspan}' style='font-size:20px;font-weight: bold;text-align: center; '>품절 재입고 요청 리스트 (상품기준)</td></tr>";
        $totalBody .= $excelBody;
        $totalBody .= "</table>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($totalBody, '품절상품_요청_리스트');

        /*$data = $getData['data'];

        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['reqName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['cellPhone'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memId']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['nickNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd(str_replace('^|^','/',$val['optionInfo']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['reqCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd($val['deliveryName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(gd_date_format('Y-m-d h:i:s',$val['regDt']));
            $fieldData[] = ExcelCsvUtil::wrapTd(SlCodeMap::SOLDOUT_REQ_SEND_TYPE[$val['sendType']]);
            $fieldData[] = ExcelCsvUtil::wrapTd(gd_date_format('Y-m-d h:i:s',$val['sendDt']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('품절상품_요청_리스트',SoldoutReqListService::EXCEL_LIST_TITLES,$excelBody);*/
    }

    public function setBlankTd(&$array ,$maxOptionCnt, $optionList){
        if ( $maxOptionCnt > count($optionList) ) {
            for ($forIdx = 0; $forIdx < ($maxOptionCnt - count($optionList)); $forIdx++) {
                $array[] = ExcelCsvUtil::wrapTd('');
            }
        }
    }

}
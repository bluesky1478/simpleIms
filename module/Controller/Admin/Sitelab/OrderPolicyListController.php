<?php

namespace Controller\Admin\Sitelab;

use App;
use Component\Order\OrderPolicyListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;

class OrderPolicyListController extends \Controller\Admin\Controller{
    public function index(){

        //gd_debug(SET_CHARSET);

        $getValue = Request::get()->toArray();

        $this->callMenu('sitelab', 'order', 'order_policy_list');
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
            /*'../../script/vue.js',*/
        ]);

        $orderPolicyListService = \App::load('\\Component\\Order\\OrderPolicyListService');

        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue);
        $getData = $orderPolicyListService->getList($getValue);
        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload($getData);
            exit();
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);
        //타이틀
        $this->setData('listTitles',OrderPolicyListService::LIST_TITLES);
        //리스트 데이터
        $this->setData('data',$getData['data']);

        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

        $this->setData('orderStatusMap',SlCommonUtil::getOrderStatusAllMap());

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
        $page = $getData['page'];
        $excelBody = '';
        $orderStatusMap = SlCommonUtil::getOrderStatusAllMap();

        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['companyNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['optionNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm'].'('. $val['memId'] . ')');
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['goodsCnt']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['freeDcCount']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['freeDcAmount']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['companyPayment']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['buyerPayment']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['totalPayment']));
            $fieldData[] = ExcelCsvUtil::wrapTd($orderStatusMap[$val['orderStatus']]);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['regDt']);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('정책적용_주문리스트',OrderPolicyListService::LIST_TITLES,$excelBody);
    }

}
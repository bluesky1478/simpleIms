<?php

namespace Controller\Admin\Erp;

use App;
use Component\Category\BrandAdmin;
use Component\Category\CategoryAdmin;
use Component\Erp\ErpCodeMap;
use Controller\Admin\Erp\ControllerService\StockCurrentService;
use Controller\Admin\Erp\ControllerService\StockTableService;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;

/**
 * 문서 리스트
 */
class ScmCalcController extends \Controller\Admin\Controller{

    public function index()
    {
        $isProvider = Manager::isProvider();
        $this->setData('isProvider', $isProvider);
        if( $isProvider ){
            $this->callMenu('statistics', 'accept', 'calc');
        }else{
            $this->callMenu('erp', 'scm', 'calc');
        }

        $requestParam = \Request::get()->toArray();

        //ScmList : 분류 완료된 고객사만.
        $this->setData('scmList', [
            //3 => '혼다코리아',
            //4 => '제일건설',
            //6 => '한국타이어',
            8 => 'TKE(티센크루프)',
            //10 => '무영건설',
            //11 => '오티스',
            //12 => '영구크린',
            14 => '미쓰비시(서비스)',
            16 => '미쓰비시(설치)',
            24 => '반도',
            23 => '동양',
            21 => 'OEK',
        ]);

        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'scmCalc');

        if( 'ex' === $requestParam['simple_excel_download'] ){
            $this->simpleExcelDownloadEx();
            exit();
        }

        $getData = $controllerListService->getList($requestParam);
        $getDetailData = $controllerListService->getListDetail($requestParam);

        $totalPrice = $getDetailData['totalData']['prdPrice'] +
            $getData['totalData']['workPrice'] +
            $getData['totalData']['packagePrice'] +
            $getData['totalData']['polyPrice'] +
            $getData['totalData']['exchangePrice']+
            $getData['totalData']['boxPrice'];

        if(!empty($requestParam['simple_excel_download'])){
            if( 1 == $requestParam['simple_excel_download'] ){
                $this->simpleExcelDownload($getData);
            }else if( 2 == $requestParam['simple_excel_download'] ){
                $this->simpleExcelDownload2($getDetailData);
            }else if( 3 == $requestParam['simple_excel_download'] ){
                $this->simpleExcelDownload3($getData, $getDetailData, $totalPrice);
            }
            exit();
        }

        $this->setData('data',$getData['data']);
        $this->setData('totalData',$getData['totalData']);
        $this->setData('totalDataDetail',$getDetailData['totalData']);
        $this->setData('calcData',$getData['calcData']);

        $this->setData('totalPrice',$totalPrice);

        $this->setData('search',$getData['search']);

        $this->setData('requestUrl1', SlCommonUtil::getCurrentPageUrl("simple_excel_download=1&"));
        $this->setData('requestUrl2', SlCommonUtil::getCurrentPageUrl("simple_excel_download=2&"));
        $this->setData('requestUrl3', SlCommonUtil::getCurrentPageUrl("simple_excel_download=3&"));
        $this->setData('requestUrlExchange', SlCommonUtil::getCurrentPageUrl("simple_excel_download=ex&"));

        $this->getView()->setPageName('erp/scm_calc.php');
    }

    public function simpleExcelDownload($getData){
        $fileName = '기타비용상세_'. gd_date_format('Y-m-d', $getData['search']['treatDate'][0]) . '-' . gd_date_format('Y-m-d', $getData['search']['treatDate'][1]);

        $LIST_TITLES = [
            '번호'
            ,'출고일자'
            ,'송장번호'
            ,'상품수량'
            ,'합포장비용'
            ,'폴리백발송비용'
            ,'박스발송비용'
            ,'박스수량'
        ];

        $totalData = $getData['totalData'];
        $data = $getData['data'];

        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($totalData['orderCount']-$key);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutDate']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['qty']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['packagePrice']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['polyPrice']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['boxPrice']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['boxCount']));
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($fileName,$LIST_TITLES,$excelBody);

    }

    public function simpleExcelDownload2(){
        //$getData
        $requestParam = \Request::get()->toArray();
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'scmCalc');
        $getData = $controllerListService->getListHistory($requestParam);

        $fileName = '출고내역_'. gd_date_format('Y-m-d', $getData['search']['treatDate'][0]) . '-' . gd_date_format('Y-m-d', $getData['search']['treatDate'][1]);

        $LIST_TITLES = [
            '번호'
            ,'출고일자'
            ,'송장번호'
            ,'주문번호'
            ,'회원분류'
            ,'회원등급'
            ,'주문자명'
            ,'수령자명'
            ,'상품명'
            ,'상품단가'
            ,'상품수량'
            ,'출고비용'
            ,'교환여부'
        ];

        //$field = ['a.inOutDate','a.invoiceNo','b.productName','b.prdPrice','a.orderNo','a.orderNo','a.customerName']; //주문번호 추가.


        $totalData = $getData['totalData'];
        $data = $getData['data'];


        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($totalData['orderCount']-$key);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutDate']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd( SlCodeMap::MEMBER_TYPE[$val['memberType']] );
            $fieldData[] = ExcelCsvUtil::wrapTd($val['groupNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['prdPrice']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['qty']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['prdPrice'] * $val['qty']));
            $fieldData[] = ExcelCsvUtil::wrapTd(array_flip(ErpCodeMap::ERP_STOCK_REASON)[$val['inOutReason']]);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($fileName,$LIST_TITLES,$excelBody);

    }

    public function simpleExcelDownload3($getData, $getDetailData, $totalPrice){

        $totalPrice = number_format($totalPrice);

        $totalData = $getData['totalData'];
        $totalDataDetail = $getDetailData['totalDataDetail'];
        $calcData = $getData['calcData'];

        $fileName = '정산내역'. gd_date_format('Y-m-d', $getData['search']['treatDate'][0]) . '-' . gd_date_format('Y-m-d', $getData['search']['treatDate'][1]);

        $LIST_TITLES = [
            '품목'
            ,'작업비용'
            ,'Total'
        ];

        $bodyDataList = [];

        $printList = [];
        $printList[] = [
            '출고비용',
            '상품별',
            number_format($totalDataDetail['prdPrice']).' 원',
        ];
        $printList[] = [
            '작업비용',
            number_format($calcData['workAmount']).' 원',
            number_format($totalData['workPrice']).' 원',
        ];
        $printList[] = [
            '합포장',
            number_format($calcData['packageAmount']).' 원',
            number_format($totalData['packagePrice']).' 원',
        ];
        $printList[] = [
            '폴리백 발송 비용',
            number_format($calcData['polyAmount']).' 원',
            number_format($totalData['polyPrice']).' 원',
        ];
        $printList[] = [
            '박스 발송 비용',
            number_format($calcData['boxAmount']).' 원',
            number_format($totalData['boxPrice']).' 원',
        ];

        foreach($printList as $each){
            $fieldData = [];
            $fieldData[] = ExcelCsvUtil::wrapTd($each[0]);
            $fieldData[] = ExcelCsvUtil::wrapTd($each[1],'','text-align:right');
            $fieldData[] = ExcelCsvUtil::wrapTd($each[2],'','text-align:right');
            $bodyDataList[] = "<tr>". implode('',$fieldData) . "</tr>";
        }
        $excelBody = implode('',$bodyDataList);

        $excelBody .= "<tr><th colspan='2' style='font-size: 15px'>TOTAL</th><td style='color:red;font-weight: bold; font-size:15px; text-align:right'>{$totalPrice} 원</td></tr>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($fileName,$LIST_TITLES,$excelBody);
    }

    public function simpleExcelDownloadEx(){
        //$getData
        $requestParam = \Request::get()->toArray();
        $requestParam['exchange'] = true;
        //SitelabLogger::logger('TEST : simpleExcelDownloadEx');
        //SitelabLogger::logger($requestParam);
        $controllerListService = SlLoader::controllerServiceLoad(__NAMESPACE__,'scmCalc');

        $getData = $controllerListService->getListExHistory($requestParam);

        $fileName = '교환출고내역_'. gd_date_format('Y-m-d', $getData['search']['treatDate'][0]) . '-' . gd_date_format('Y-m-d', $getData['search']['treatDate'][1]);

        $LIST_TITLES = [
            '번호'
            ,'출고일자'
            ,'송장번호'
            ,'주문번호'
            ,'신청자'
            ,'수령자명'
            ,'상품명'
            ,'상품수량'
            ,'교환여부'
        ];
        //$field = ['a.inOutDate','a.invoiceNo','b.productName','b.prdPrice','a.orderNo','a.orderNo','a.customerName']; //주문번호 추가.

        $totalData = $getData['totalData'];
        $data = $getData['data'];

        $excelBody = '';
        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($totalData['orderCount']-$key);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['inOutDate']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['invoiceNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['orderNo'],'text','mso-number-format:\'\@\'');
            $fieldData[] = ExcelCsvUtil::wrapTd($val['memNm']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['customerName']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['productName']);
            //$fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['prdPrice']));
            $fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['qty']));
            //$fieldData[] = ExcelCsvUtil::wrapTd(number_format($val['prdPrice'] * $val['qty']));
            $fieldData[] = ExcelCsvUtil::wrapTd(array_flip(ErpCodeMap::ERP_STOCK_REASON)[$val['inOutReason']]);
            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload($fileName,$LIST_TITLES,$excelBody);
    }

}

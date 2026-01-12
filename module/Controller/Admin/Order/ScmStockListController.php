<?php

namespace Controller\Admin\Order;

use App;
use Component\Order\OrderPolicyListService;
use Component\Scm\ScmOrderListService;
use Component\Scm\ScmStockListService;
use Component\Stock\StockListService;
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
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;

class ScmStockListController extends \Controller\Admin\Controller{
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
            $this->callMenu('statistics', 'scm', 'goodsCount');
            $scmNo = \Session::get('manager.scmNo');
            $companyNm = \Session::get('manager.companyNm');
        }else{
            $this->callMenu('order', 'order', 'stock_list');
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

        $scmStockListService = SlLoader::cLoad('Scm','ScmStockListService');;

        $getValue = SlCommonUtil::getRefineValueAndExcelDownCheck($getValue); //미사용
        //gd_debug($getValue);
        $getValue['scmFl'] =  1;
        $getValue['scmNo'][] =  $scmNo;
        $getValue['scmNoNm'][] =  $companyNm;
        //gd_debug($getValue);

        if(  !empty($getValue['simple_excel_download'])  ){
            //unset($getValue['scmNo']);
            $getValue['pageNum'] = 10000;
            $getValue['page'] = 1;
            $getData = $scmStockListService->getList($getValue);
            $this->simpleExcelDownload($getData);
            exit();
        }else{
            $getData = $scmStockListService->getList($getValue);
        }

        //라디오 체크
        $this->setData('checked', $getData['checked']);
        //검색정보
        $this->setData('search', $getData['search']);
        //페이지
        $this->setData('page', $getData['page']);
        //타이틀
        $this->setData('listTitles',ScmStockListService::LIST_TITLES);
        $this->setData('maxOptionCnt',$getData['maxOptionCnt']);

        //리스트 데이터
        $this->setData('data',$getData['data']);
        $this->setData('scmNo',$scmNo);
        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        $this->setData('orderStatusMap',SlCommonUtil::getOrderStatusAllMap());
        $this->getView()->setPageName('order/scm_stock_list.php');
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

        $startDate = substr($getData['search']['searchDate'][0],0,10);
        $endDate = substr($getData['search']['searchDate'][1],0,10);

        foreach ($data as $key => $val) {
            $fieldData = array();
            $fieldData[] = ExcelCsvUtil::wrapTd($page->idx--);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNo']);
            $fieldData[] = ExcelCsvUtil::wrapTd($val['goodsNm']);

            $tableInfo = '<table style="width:100%; ">';
            $tableInfo .= '<tr><td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0">옵션</td>';
            foreach( $val['optionList'] as $optionData ) {
                $tableInfo .= '<td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0">'.$optionData['optionName'].'</td>';
            }
            $tableInfo .= '</tr><tr><td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0">현재 수량</td>';
            foreach( $val['optionList'] as $optionData ) {
                $tableInfo .= '<td class="text" style="border-bottom : solid 1px #e0e0e0; border-right : solid 1px #e0e0e0">'.$optionData['stockCnt'].'</td>';
            }
            $tableInfo .= '</tr><tr><td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0">출고 수량</td>';
            foreach( $val['optionList'] as $optionData ) {
                $tableInfo .= '<td class="text" style="border-bottom : solid 1px #e0e0e0; border-right : solid 1px #e0e0e0;">'.$optionData['totalStockCnt'].'</td>';
            }

            /*
            $tableInfo .= '</tr><tr><td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0">월별 출고 수량</td>';
            foreach( $val['optionList'] as $optionData ) {
                $tableInfo .= '<td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0"><table>';
                foreach( $optionData['stockDetailList'] as $stockCntData ) {
                    $tableInfo .= '<tr>';
                    $tableInfo .= '<td style="border:none;width:50px">'.$stockCntData['stockDate'].':</td>';
                    $tableInfo .= '<td style="border:none;text-align: left ">'.$stockCntData['stockCnt'].'<span class="">개</span></td>';
                    $tableInfo .= '</tr>';
                }
                $tableInfo .= '</table></td>';
            }

            $tableInfo .= '</tr><tr><td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0">월별 입고 수량</td>';
            foreach( $val['optionList'] as $optionData ) {
                $tableInfo .= '<td style="border-right : solid 1px #e0e0e0; border-bottom : solid 1px #e0e0e0"><table>';
                foreach( $optionData['stockInputList'] as $stockCntData ) {
                    $tableInfo .= '<tr>';
                    $tableInfo .= '<td style="border:none;width:50px">'.$stockCntData['stockDate'].':</td>';
                    $tableInfo .= '<td style="border:none;text-align: left ">'.$stockCntData['stockCnt'].'<span class="">개</span></td>';
                    $tableInfo .= '</tr>';
                }
                $tableInfo .= '</table></td>';
            }
            */

            $tableInfo .= '</tr>';

            $tableInfo .= '</table>';


            /*$tableInfo = '<table style="width:100%">';
            foreach( $val['optionList'] as $stockCntData ) {
                $tableInfo .= '<tr>';
                $tableInfo .= '<td style="border:none;width:50px">'.$stockCntData['stockDate'].':</td>';
                $tableInfo .= '<td style="border:none;text-align: left ">'.$stockCntData['stockCnt'].'<span class="">개</span></td>';
                $tableInfo .= '</tr>';
            }
            $tableInfo .= '</table>';*/

            $fieldData[] = ExcelCsvUtil::wrapTd($tableInfo);

            $excelBody .=  "<tr>". implode('',$fieldData) . "</tr>";
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('상품출고현황' ,[
            '번호','상품번호','상품명','현재 수량 및 조회기간 출고수량'
        ],$excelBody);
    }

}
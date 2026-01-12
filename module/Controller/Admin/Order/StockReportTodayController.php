<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Order;

use Component\Mall\Mall;
use Component\Member\Manager;
use Component\VisitStatistics\VisitStatistics;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlLoader;

/**
 * 방문통계 당일
 * @author Seung-gak Kim <surlira@godo.co.kr>
 */
class StockReportTodayController extends \Controller\Admin\Controller
{
    private $stockReportService = null;

    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {
        try {
            $combineSearch = [
                '' => '주문번호',
                '' => '주문자명',
                '' => '회원ID',
                '' => '닉네임',
            ];
            $this->setData('combineSearch', $combineSearch);

            $getValue = Request::get()->toArray();

            //공급사 리스트
            $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
            $scmList = $scmAdmin->getSelectScmList();
            $refineScmList = array();
            $firstScmNo = 0;
            $isFirst = true;
            foreach( $scmList as $key => $val ){
                $refineScmList[$key] = $val;
                if(true === $isFirst){
                    $firstScmNo = $key;
                    $isFirst = false;
                }
            }

            $isProvider = Manager::isProvider();
            if( $isProvider ){
                $this->callMenu('statistics', 'scm', 'stock'); //공급사 메뉴
                $scmNo = \Session::get('manager.scmNo');
            }else{
                $this->callMenu('order', 'order', 'stock');
                $scmNo = empty($getValue['scmNo'][0]) ? $firstScmNo : $getValue['scmNo'][0];
            }

            $linkId = gd_isset($getValue['linkId'], 'stockToday');
            $this->setData('linkId',$linkId);

            //gd_debug( $searchDate  );
            $startDate = $getValue['searchDate'][0];
            $endDate = $getValue['searchDate'][1];
            $searchDate[0] = $startDate;
            $searchDate[1] = $endDate;
            if(  empty($searchDate[0])  ){
                $startDate = SlCommonUtil::getNowDate();
                $endDate = SlCommonUtil::getNowDate();
                switch ($linkId){
                    case 'stockDay' :
                       $startDate = date('Y-m-d', strtotime('-7 days'));
                        break;
                    case 'stockWeek' :
                        $startDate = date('Y-m-d', strtotime('-15 days'));
                        $endDate = date('Y-m-d');
                        break;
                    case 'stockMonth' :
                        $startDate = date('Y-m',strtotime('-10 year'));
                        $endDate = date('Y-m');
                        break;
                    case 'stockYear' :
                        $startDate = date('Y',strtotime('-10 year'));
                        $endDate = date('Y');
                        break;
                }
                $getValue['searchDate'][0] = $searchDate[0] = $startDate;
                $getValue['searchDate'][1] = $searchDate[1] = $endDate;
            }
            $getValue['scmNo'] =  $scmNo;

            //공급사 재고 리포트 서비스
            $this->stockReportService = SlLoader::cLoad('scm','ScmStockReportService');
            $this->stockReportService->setScmGoods($getValue);

            //주문 집계 정보
            $this->setData('orderTotal',$this->stockReportService->getOrderTotal() );
            //상품검색용
            $this->setData('selectedGoodsInfo',$this->stockReportService->getSelectedGoodsInfo() );
            //차트 셋업
            $this->setCahrtData($getValue);
            //통계 셋업
            $this->setStatisticsData($getValue);
            $this->setData('searchDate', $searchDate);
            $this->setData('scmList', $refineScmList);
            $this->setData('isProvider', $isProvider);
            $this->setData('scmNo', $scmNo);
            $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
            $this->setData('selfUrl', basename(\Request::getPhpSelf()));
            //엑셀 다운로드
            if(  !empty($getValue['simple_excel_download'])  ){
                $this->simpleExcelDownload($getValue);
                exit();
            }
            $this->getView()->setPageName('order/stock_report_today.php');
        } catch (\Exception $e) {
            throw new AlertBackException($e->getMessage());
        }
        $this->addScript(
            [
                'backbone/backbone-min.js',
                'tui/code-snippet.min.js',
                'raphael/effects.min.js',
                'raphael/raphael-min.js',
                '../../script/chart.min.js',
                'jquery/jquery.multi_select_box.js',
            ]
        );
        $this->addCss(
            [
                '../../css/admin-custom.css',
            ]
        );
    }

    /**
     *  통계 데이터 셋업
     */
    public function setStatisticsData($getValue){
        $dataTypeList = ['totalStat','compareStat','periodStat','currentStat','ratioStat'];
        foreach($dataTypeList as $dataType ){
            $getDataFncName = 'get'.ucfirst($dataType);
            $getHtmlBodyFncName = $dataType . 'BodySet';
            $data = $this->stockReportService->$getDataFncName($getValue);
            $data['htmlBody'] =  $this->$getHtmlBodyFncName($data);
            $this->setData($dataType, $data);
        }
        //gd_debug($this->getData('compareStat'));
    }

    /**
     *  차트 데이터 셋업
     */
    public function setCahrtData($getValue){
        // 차트 데이터 가져오기
        $getDataArr['total']['orderCnt'] = 25;
        $getDataArr['total']['goodsCnt'] = 135;
        $getDataArr['total']['orderAmount'] = 5625400;
        $getDataArr['total']['orderPayAmount'] = 5625400;
        $this->setData('total', $getDataArr['total']);
    }

    /**
     * 전체 출고 현황 HTML
     * @param $getValue
     */
    public function totalStatBodySet($totalStat, $isExcel = false){
        $htmlContents = "<tr>";
        foreach(  $totalStat['title'] as $title  ) {
            $htmlContents .= "<th class='title'>{$title}</th>";
        }
        $htmlContents .= "</tr>";
        $htmlContents .= "
        <tr>
            <td>{$totalStat['data']['dp_goodsCnt']}</td>
            <td>{$totalStat['data']['dp_fineCnt']}</td>
            <td>{$totalStat['data']['dp_backCnt']}</td>
            <td>{$totalStat['data']['dp_exchangeCnt']}</td>
            <td>{$totalStat['data']['dp_refundCnt']}</td>
            <td>{$totalStat['data']['dp_asCnt']}</td>
        </tr>
        ";
        return $htmlContents;
    }

    /**
     *  출고량 비교 HTML
     * @param $data
     * @return string
     */
    public function compareStatBodySet($data, $isExcel = false){
        //gd_debug($data);
        $htmlContents = "";
        foreach($data['data'] as $datKey => $optionClassDataList  ){
            $htmlContents .= '<tr>';
           foreach( $optionClassDataList['title'] as $titleKey => $title ){
               if( $isExcel && $titleKey === 'goodsImage'  ){
                   continue;
               }
                $htmlContents .= "<th class='title'>{$title}</th>";
            }
            $htmlContents .= "<th class='title'>합계</th>";
            $htmlContents .= '</tr>';
            foreach( $optionClassDataList['data'] as $optionClassData ){
                $outStockCnt = 0;
                $htmlContents .= '<tr>';
                $htmlContents .= "<td>{$optionClassData['goodsNo']}</td>";
                if( !$isExcel ){
                    $htmlContents .= "<td>{$optionClassData['goodsImage']}</td>";
                }
                $htmlContents .= "<td>{$optionClassData['goodsNm']}</td>";
                $htmlContents .= "<td>".number_format($optionClassData['goodsPrice']) ."</td>";
                foreach( $optionClassData['option'] as $optionValue ){
                    $outStockCnt += $optionValue['outStockCnt'];
                    $htmlContents .= "<td>".number_format($optionValue['outStockCnt']) . "</td>";
                }
                $htmlContents .= "<td>".number_format($outStockCnt). "</td>";
                $htmlContents .= '</tr>';
            }
        }
        return $htmlContents;
    }

    /**
     * 기간별 출고량 HTML
     * @param $totalStat
     * @param false $isExcel
     * @return string
     */
    public function periodStatBodySet($data, $isExcel = false){
        $htmlContents = "<tr><th class='title'>상품코드</th><td>-</td>";
        foreach($data['goodsNoGoodsInfo'] as $goodsInfo){
            $htmlContents .= "<td>{$goodsInfo['goodsNo']}</td>";
        }
        $htmlContents .= "</tr><tr><th class='title'>판매가</th><td>-</td>";
        foreach($data['goodsNoGoodsInfo'] as $goodsInfo){
            $htmlContents .= "<td>".number_format($goodsInfo['goodsPrice']) ."</td>";
        }
        $htmlContents .= "</tr><tr><th class='title'>상품코드</th><td>합계</td>";
        foreach($data['goodsNoGoodsInfo'] as $goodsInfo){
            $htmlContents .= "<td>{$goodsInfo['goodsNm']}</td>";
        }
        $htmlContents .= "</tr>";

        $total = 0;
        $totalGoodsNo = array();

        //Title Max 찾기
        $maxColcount = 0;
        foreach($data['data'] as $regDt => $regDtData) {
            $colCount = 0;
            foreach ($regDtData['data'] as $value) {
                $colCount++;
            }
            if( $colCount > $maxColcount ){
                $maxColcount = $colCount;
            }
        }
        //gd_debug($colCount);

        foreach($data['data'] as $regDt => $regDtData){
            $htmlContents .= "<tr>";
            $isFirst = true;
            foreach( $regDtData['data'] as $value ){
                if($isFirst){
                    $htmlContents .= "<th class='title'>".$value['dpDate']."</th>";
                    $htmlContents .= "<td>".number_format($regDtData['total'])."</td>";
                    $isFirst=false;
                }
                $htmlContents .= "<td>".number_format($value['stockCnt'])."</td>";
                $totalGoodsNo[$value['goodsNo']] += (int)$value['stockCnt'];
            }
            $htmlContents .= "</tr>";
            $total += (int)$regDtData['total'];
        }

        $htmlContents .= "<tr><th class='title'>합계</th><td>".number_format($total)."</td>";
        ksort($totalGoodsNo);
        foreach($totalGoodsNo as $value){
            $htmlContents .= "<td>" .  $value  ."</td>";
        }
        $htmlContents .= "</tr>";

        return $htmlContents;
    }

    /**
     * 재고현황
     * @param $data
     * @param false $isExcel
     * @return string
     */
    public function currentStatBodySet($data, $isExcel = false){
        //gd_debug($data);
        $htmlContents = "";
        foreach($data['data'] as $datKey => $optionClassDataList  ){
            $htmlContents .= '<tr>';
            foreach( $optionClassDataList['title'] as $titleKey => $title ){
                if( 32 === strlen($titleKey)){
                    $rowspan = "";
                    $colspan = "colspan='2'";
                }else{
                    $rowspan = "rowspan='2'";
                    $colspan = "";
                }
                if( $isExcel && $titleKey === 'goodsImage'  ){
                    continue;
                }
                $htmlContents .= "<th class='title' {$rowspan} {$colspan}>{$title}</th>";
            }
            $htmlContents .= "<th class='title' colspan='2'>합계</th>";
            $htmlContents .= '</tr><tr>';
            foreach( $optionClassDataList['title'] as $titleKey => $title ){
                if( 32 === strlen($titleKey)){
                    $htmlContents .= "<th class='title'>현재고</th><th>안전재고</th>";
                }
                if( $isExcel && $titleKey === 'goodsImage'  ){
                    continue;
                }
            }
            $htmlContents .= "<th class='title'>현재고</th><th>안전재고</th></tr>";

            foreach( $optionClassDataList['data'] as $optionClassData ){
                $stockCnt = 0;
                $safeStockCnt = 0;
                $htmlContents .= '<tr>';
                $htmlContents .= "<td>{$optionClassData['goodsNo']}</td>";
                if( !$isExcel ){
                    $htmlContents .= "<td>{$optionClassData['goodsImage']}</td>";
                }
                $htmlContents .= "<td>{$optionClassData['goodsNm']}</td>";
                $htmlContents .= "<td>".number_format($optionClassData['goodsPrice']) ."</td>";
                foreach( $optionClassData['option'] as $optionValue ){
                    $stockCnt += $optionValue['stockCnt'];
                    $safeStockCnt += $optionValue['safeCnt'];

                    if( $optionValue['safeCnt'] >= $optionValue['stockCnt'] ){
                        $fontColor = "style='color:red'";
                    }else{
                        $fontColor = "";
                    }
                    $htmlContents .= "<td {$fontColor}>".number_format($optionValue['stockCnt']) . "</td>";
                    $htmlContents .= "<td>".number_format($optionValue['safeCnt']) . "</td>";
                }

                if( $safeStockCnt >= $stockCnt ){
                    $fontColor = "style='color:red'";
                }else{
                    $fontColor = "";
                }
                $htmlContents .= "<td {$fontColor} >".number_format($stockCnt). "</td>";
                $htmlContents .= "<td>".number_format($safeStockCnt). "</td>";
                $htmlContents .= '</tr>';
            }
        }
        return $htmlContents;
    }

    /**
     *  출고 사이즈 비율 HTML
     * @param $data
     * @return string
     */
    public function ratioStatBodySet($data, $isExcel = false){
        //gd_debug($data);
        $htmlContents = "";
        foreach($data['data'] as $datKey => $optionClassDataList  ){
            $htmlContents .= '<tr>';
            foreach( $optionClassDataList['title'] as $titleKey => $title ){
                if( $isExcel && $titleKey === 'goodsImage'  ){
                    continue;
                }
                $htmlContents .= "<th class='title'>{$title}</th>";
            }
            $htmlContents .= "<th class='title'>합계</th>";
            $htmlContents .= '</tr>';
            foreach( $optionClassDataList['data'] as $optionClassData ){
                $outStockCnt = 0;
                $htmlContents .= '<tr>';
                $htmlContents .= "<td>{$optionClassData['goodsNo']}</td>";
                if( !$isExcel ){
                    $htmlContents .= "<td>{$optionClassData['goodsImage']}</td>";
                }
                $htmlContents .= "<td>{$optionClassData['goodsNm']}</td>";
                $htmlContents .= "<td>".number_format($optionClassData['goodsPrice']) ."</td>";
                foreach( $optionClassData['option'] as $optionValue ){
                    $outStockCnt += $optionValue['outStockRatio'];
                    $htmlContents .= "<td>".number_format($optionValue['outStockRatio']) . "%</td>";
                }
                $htmlContents .= "<td>100%</td>";
                $htmlContents .= '</tr>';
            }
        }
        return $htmlContents;
    }


    /**
     * 엑셀 다운로드
     * @param $mode
     */
    public function simpleExcelDownload($getValue){
        $mode  = $getValue['downType'];
        $data = $this->getData($mode);
        $getBodyFncName = $mode . 'BodySet';

        $htmlBody = "";

        //Period
        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>기간</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTd($data['period'], null, 'font-size:15px;font-weight:bold', 'colspan=2')."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>'.$data['fileName'].'</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<table border='1'>";
        $htmlBody .= $this->$getBodyFncName($data, true);
        $htmlBody .= "</table>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($htmlBody, $data['fileName'].'_'.$data['period']);
    }

}

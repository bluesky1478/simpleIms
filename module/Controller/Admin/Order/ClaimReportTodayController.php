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

use Component\Scm\ScmOrderListService;
use Component\VisitStatistics\VisitStatistics;
use Component\Mall\Mall;
use DateTime;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;

/**
 * 방문통계 당일
 * @author Seung-gak Kim <surlira@godo.co.kr>
 */
class ClaimReportTodayController extends \Controller\Admin\Controller
{
    private $claimReportService = null;

    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {

        try {

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
                $this->callMenu('statistics', 'scm', 'claim'); //공급사 메뉴
                $scmNo = \Session::get('manager.scmNo');
            }else{
                $this->callMenu('order', 'order', 'claim');
                $scmNo = empty($getValue['scmNo'][0]) ? $firstScmNo : $getValue['scmNo'][0];
            }

            $linkId = gd_isset($getValue['linkId'], 'stockToday');
            $this->setData('linkId',$linkId);

            $startDate = $getValue['searchDate'][0];
            $endDate = $getValue['searchDate'][1];
            $searchDate[0] = $startDate;
            $searchDate[1] = $endDate;
            if(  empty($searchDate[0])  ){
                if( 7 == $scmNo ){
                    $startDate = date('Y-m-d', strtotime('-10 years'));
                }else{
                    $startDate = date('Y-m-d', strtotime('-365 days'));
                }

                $endDate = SlCommonUtil::getNowDate();
                $getValue['searchDate'][0] = $searchDate[0] = $startDate;
                $getValue['searchDate'][1] = $searchDate[1] = $endDate;
            }
            $getValue['scmNo'] =  $scmNo;

            //공급사 재고 리포트 서비스
            $this->claimReportService = SlLoader::cLoad('scm','ScmClaimReportService');
            $this->claimReportService->setScmGoods($getValue);

            //주문 집계 정보
            $this->setData('orderTotal',$this->claimReportService->getOrderTotal() );
            //상품검색용
            $this->setData('selectedGoodsInfo',$this->claimReportService->getSelectedGoodsInfo() );

            //$this->setData('claimInfo', $this->claimReportService->getClaimInfo($getValue));
            $claimInfo = $this->claimReportService->getClaimInfo($getValue);

            if( !empty($getValue['simple_excel_download']) ){
                $isExcel = true;
            }else{
                $isExcel = false;
            }

            $this->setData('totalBody', $this->makeTotalStatBody($this->claimReportService->getTotalStat($getValue),$isExcel));
            //교환/반품/환불/AS
            foreach(SlCodeMap::CLAIM_TYPE as $key => $value){
                $bodyList[$key] = $this->makeBody($claimInfo[$key]['data'],$isExcel);
            }
            $this->setData('bodyList', $bodyList);

            $this->setData('searchDate', $searchDate);
            $this->setData('scmList', $refineScmList);
            $this->setData('isProvider', $isProvider);
            $this->setData('scmNo', $scmNo);
            $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
            $this->setData('selfUrl', basename(\Request::getPhpSelf()));

            $this->setData('chartColor', '\''  . implode('\',\'', SlCodeMap::ADMIN_CLAIM_REASON_COLOR) . '\'' );

            $this->setData('chartLabel', '\''  . implode('\',\'', SlCodeMap::ADMIN_CLAIM_REASON) . '\'' );

            $this->setData('claimType', SlCodeMap::CLAIM_TYPE );
            $this->setData('claimInfo', $claimInfo);
            //gd_debug($claimInfo);

            $this->getView()->setPageName('order/claim_report_today.php');

            //엑셀 다운로드
            if(  !empty($getValue['simple_excel_download'])  ){
                $this->simpleExcelDownload($searchDate, $getValue['downType']);
                exit();
            }
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
     * TOTAL 정보
     * @param $totalStat
     * @param false $isExcel
     * @return string
     */
    public function makeTotalStatBody($totalStat, $isExcel = false){
        $widthStyle = "";
        if($isExcel){
            $widthStyle = "style='width:150px'";
        }
        $title ="
        <colgroup>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
            <col style='width:12.5%'/>
        </colgroup>
         <tr>
            <th {$widthStyle} class='title'>전체 출고건<br><small class='font-kor'>(전체주문건수)</small></th>
            <th  {$widthStyle} class='title'>전체 출고 수량<br><small class='font-kor'>(전체 상품 사이즈별 수량 합계)</small></th>
            <th  {$widthStyle} class='title'>전체 접수건<br><small class='font-kor'>(교환/반품/환불/AS)</small></th>
            <th  {$widthStyle} class='title'>전체 접수 수량</th>
            <th  {$widthStyle} class='title'>교환 접수</th>
            <th  {$widthStyle} class='title'>반품 접수</th>
            <th  {$widthStyle} class='title'>환불 접수</th>
            <th  {$widthStyle} class='title'>AS 접수</th>
        </tr>        
        ";
        $body = "
        <tr>
            <td>
                <strong>{$totalStat['data']['dp_orderCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_goodsCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_acctCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_claimCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_exchangeCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_backCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_refundCnt']}</strong>
            </td>
            <td>
                <strong>{$totalStat['data']['dp_asCnt']}</strong>
            </td>
        </tr>        
        ";
        return $title.$body;
    }

    /**
     * 사용안함(기존)
     * @param $data
     * @param false $isExcel
     * @return string
     */
    public function makeBody($data, $isExcel = false){
        $htmlContents = "<tr><th class='title' style='width:100px'>상품코드</th>";
        $nodataRowspan = '7';
        if(!$isExcel){
            $htmlContents .= "<th style='width:80px'>이미지</th>";
        }else{
            $nodataRowspan = '6';
        }
        $htmlContents .= "<th class='title' style='width:350px'>상품명</th><th class='title' style='width:100px'>판매가</th><th class='title' style=''>사유</th><th class='title' style='width:100px'>수량</th><th class='title' style='width:100px'>비율</th></tr>";

        if( empty($data) ){
            $htmlContents .= "<td style='text-align: center' colspan='{$nodataRowspan}'>데이터가 없습니다.</td>";
        }

        foreach( $data as $goodsNo => $value  ){
            $valueData = $value['data'];
            //claimCnt
            foreach( $valueData as $valueDataKey => $dpData ){
                $htmlContents .= "<tr>";
                if( 'true' === $dpData['rowspan']  ){
                    $rowspan = "rowspan=".$value['rowspan'];
                    $image = gd_html_goods_image($goodsNo, $dpData['imageName'], $dpData['imagePath'], $dpData['imageStorage'], 40, $dpData['goodsNm'], '_blank');
                    $htmlContents .= ExcelCsvUtil::wrapTd( $goodsNo, null, 'width:100px',  $rowspan );
                    if(!$isExcel){
                        $htmlContents .= ExcelCsvUtil::wrapTd( $image , null, 'width:80px',  $rowspan );
                    }
                    $htmlContents .= ExcelCsvUtil::wrapTd( $dpData['goodsNm'], null, 'width:350px',  $rowspan  );
                    $htmlContents .= ExcelCsvUtil::wrapTd( number_format($dpData['goodsPrice']) , null, 'width:100px',  $rowspan );
                }
                $htmlContents .= ExcelCsvUtil::wrapTd( $dpData['claimReasonStr'] , null , '' );
                $htmlContents .= ExcelCsvUtil::wrapTd( $dpData['claimCnt'] , null , 'width:100px' );
                $htmlContents .= ExcelCsvUtil::wrapTd( (int) round($dpData['claimCnt'] / $value['totalClaimCnt'] * 100,2) . '%'   , null , 'width:100px');
                $htmlContents .= "</tr>";
            }
        }
        return $htmlContents;
    }

    /**
     * 엑셀 다운로드
     * @param $mode
     */
    public function simpleExcelDownload($searchDate, $downType){
        $period = $searchDate[0] . '~' . $searchDate[1];
        $body = $this->getData('bodyList')[$downType];
        $claimType = SlCodeMap::CLAIM_TYPE[$downType];
        $htmlBody = "";

        //Period
        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>기간</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTd($period, null, 'font-size:15px;font-weight:bold', 'colspan=2')."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>'.$claimType.'</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<table border='1'>";

        $htmlBody .= $body;
        $htmlBody .= "</table>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->downloadCommon($htmlBody, $claimType.'_'.$period);
    }

    /**
     * 엑셀 다운로드
     * @param $mode
     */
    public function simpleExcelDownload_previousVersion($searchDate){
        $htmlBody = "";

        //Period
        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>기간</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTd($searchDate[0]."~".$searchDate[1], null, 'font-size:15px;font-weight:bold', 'colspan=2')."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        //Table1
        $htmlBody .= "<table border='1'>";
        $htmlBody .= $this->getData('totalBody');
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        //Table2
        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>교환</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<table border='1'>";
        $htmlBody .= $this->getData('exchangeBody');
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>반품</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<table border='1'>";
        $htmlBody .= $this->getData('backBody');
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>환불</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<table border='1'>";
        $htmlBody .= $this->getData('refundBody');
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        $htmlBody .= "<table border='0'>";
        $htmlBody .= "<tr>".ExcelCsvUtil::wrapTh( '<b>AS</b>' , null, 'font-size:17px;background-color:#DAEEF3',  'colspan=2' )."</tr>";
        $htmlBody .= "</table>";
        $htmlBody .= "<table border='1'>";
        $htmlBody .= $this->getData('asBody');
        $htmlBody .= "</table>";
        $htmlBody .= "<div>&nbsp;</div>";

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $fileName = '교환.반품.환불.AS통계_' . DateTimeUtils::dateFormat('Y-m-d', 'now');
        $simpleExcelComponent->downloadCommon($htmlBody, $fileName);
    }

}

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
namespace Controller\Admin\Statistics;

use Component\VisitStatistics\VisitStatistics;
use DateTime;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SlComponent\Util\SlLoader;

/**
 * 일별 리포트
 * @author ssong
 */
class MonthlyReportController extends \Controller\Admin\Controller
{
    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {
        $getValue = Request::get()->toArray();

        if( empty($getValue['searchDate']) ){
            $getValue['searchDate'] = gd_isset($searchData['searchDate'], date('Y-m'));;
        }

        // --- 메뉴 설정
        $this->callMenu('statistics', 'scm', 'monthly');
        $this->addScript(
            [
                'backbone/backbone-min.js',
                'tui/code-snippet.min.js',
                'raphael/effects.min.js',
                'raphael/raphael-min.js',
                //'tui.chart-master/chart.min.js',
                'tui.grid/grid.min.js',
                '../../script/tui-chart.min.js'
            ]
        );
        $this->addCss(
            [
                 '../../css/tui-chart.min.css'
            ]
        );

        //공급사 리스트
        $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
        $this->setData('scmList',$scmAdmin->getSelectScmList());

        $reportService = \App::load(\Component\Report\DailyReportService::class);
        $getData = $reportService->getStat($getValue);
        $getOrderData = $reportService->getOrderStat();

        $claimService = SlLoader::cLoad('claim','claimService');;
        $claimSerachParam['searchDate'][0] = $getValue['searchDate'].'-01';
        $claimSerachParam['searchDate'][1] = $getValue['searchDate'].'-31';
        $getExchangeData = $claimService->getSimpleClaimList($claimSerachParam);

        //$getExchangeData = $reportService->getHandleData('e');
        //$getBackData = $reportService->getHandleData('b');
        //$getAsData = $reportService->getHandleData('a');

        //검색정보
        $this->setData('search', $getData['search']);
        //리스트 데이터
        $this->setData('data',$getData['data']); //집계
        $this->setData('orderData',$getOrderData['data']); //주문
        $this->setData('exchangeData',$getExchangeData);//교환
        //$this->setData('backData',$getBackData['data']);//반품
        //$this->setData('asData',$getAsData['data']);//AS

        $this->getView()->setPageName('statistics/monthly_report.php');

    }
}

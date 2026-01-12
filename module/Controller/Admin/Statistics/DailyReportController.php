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
class DailyReportController extends \Controller\Admin\Controller
{
    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {
        $getValue = Request::get()->toArray();

        // --- 메뉴 설정
        $this->callMenu('statistics', 'scm', 'daily');
        $this->addScript(
            [
                'backbone/backbone-min.js',
                'tui/code-snippet.min.js',
                'raphael/effects.min.js',
                'raphael/raphael-min.js',
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
        $claimSerachParam['searchDate'][0] = $getValue['searchDate'];
        $claimSerachParam['searchDate'][1] = $getValue['searchDate'];

        $getExchangeData = $claimService->getSimpleClaimList($claimSerachParam);
        //gd_debug($getExchangeData);
        $this->setData('exchangeData',$getExchangeData);//교환 및 클레임 데이터

        //$getExchangeData = $reportService->getHandleData('e');
        //$getBackData = $reportService->getHandleData('b');
        //$getAsData = $reportService->getHandleData('a');

        //검색정보
        $this->setData('search', $getData['search']);
        //리스트 데이터
        $this->setData('data',$getData['data']); //집계
        $this->setData('orderData',$getOrderData['data']); //주문

        //$this->setData('exchangeData',$getExchangeData['data']);//교환
        //$this->setData('backData',$getBackData['data']);//반품
        //$this->setData('asData',$getAsData['data']);//AS

        $this->getView()->setPageName('statistics/daily_report.php');

    }
}

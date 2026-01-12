<?php

namespace Controller\Admin\Order;

use App;
use Component\Order\OrderPolicyListService;
use Component\Scm\ScmOrderListService;
use Component\Scm\ScmStockListService;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use function SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;

class ScmStockListPopupController extends \Controller\Admin\Controller{
    public function index(){

        $getValue = \Request::get()->toArray();

        $this->addScript(
            [
                '../../script/chart.min.js',
            ]
        );
        /*$chartDataList[0]['optionName'] = 'S';
        $chartDataList[0]['data'] = [];
        $chartDataList[0]['data'][0]['title'] = '20/01';
        $chartDataList[0]['data'][1]['title'] = '20/02';
        $chartDataList[0]['data'][2]['title'] = '20/03';
        $chartDataList[0]['data'][0]['count'] = '20';
        $chartDataList[0]['data'][1]['count'] = '50';
        $chartDataList[0]['data'][2]['count'] = '30';

        $chartDataList[1]['optionName'] = 'L';
        $chartDataList[1]['data'] = [];
        $chartDataList[1]['data'][0]['title'] = '20/01';
        $chartDataList[1]['data'][1]['title'] = '20/02';
        $chartDataList[1]['data'][2]['title'] = '20/03';
        $chartDataList[1]['data'][0]['count'] = '250';
        $chartDataList[1]['data'][1]['count'] = '80';
        $chartDataList[1]['data'][2]['count'] = '60';*/

        $this->setData('chartGoodsName', DBUtil2::getOne(DB_GOODS,'goodsNo', $getValue['goodsNo'] ) );

        $startDate = $getValue['startDate'];
        $endDate = $getValue['endDate'];

        $scmStockListService = SlLoader::cLoad('Scm','ScmStockListService');;
        $monthlyStockCountData = $scmStockListService->getOptionMonthlyStockCount($getValue['goodsNo'], $startDate, $endDate);

        $chartDataList = [];
        foreach($monthlyStockCountData as $key => $value){
            $chartDataList[$key]['optionName'] = $value['optionName'];
            foreach($value['stockDetailList'] as $stockDetailKey => $stockDetailData){
                $chartDataList[$key]['data'][$stockDetailKey]['title'] = $stockDetailData['stockDate'];
                $chartDataList[$key]['data'][$stockDetailKey]['count'] = $stockDetailData['stockCnt'];
            }
        }

        $this->setData('chartDataList', $chartDataList );

        $this->setData('chartGoodsData', DBUtil2::getOne(DB_GOODS,'goodsNo', $getValue['goodsNo'] ) );
        $this->setData('startDate', $startDate );
        $this->setData('endDate', $endDate );

        $this->getView()->setDefine('layout', 'layout_blank.php');

    }


}
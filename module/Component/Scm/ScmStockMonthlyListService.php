<?php
namespace Component\Scm;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;

/**
 *  정책 적용 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ScmStockMonthlyListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'상품명'
        ,'항목'
        ,'현재 수량 및 조회기간 출고수량'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmStockMonthlyListSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'a.goodsNm' => '상품명'
        ];
        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }
        // --- 정렬
        $this->search['sortList'] = [
            'a.regDt desc' => sprintf('%s↓', __('등록일')),
            'a.regDt asc' => sprintf('%s↑', __('등록일')),
        ];
        $this->search['sort'] = gd_isset( $searchData['sort'] ,'a.regDt desc' );

        // -- 페이징 기본 설정
        $this->search['page'] = gd_isset( $searchData['page'] ,1);
        $this->search['pageNum'] = gd_isset( $searchData['pageNum'] ,20);

        // 검색 항목 설정 끝 ----------------------------------------------------------

        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'searchPeriod'
            ,'goodsNo'
        ],$searchData);
        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl'
        ],$searchData,'all');
        $this->setRadioSearch([
        ],$searchData,'');

        // 기간 설정
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
            //$this->search['searchDate'][0] .= ' 00:00:00';
            //$this->search['searchDate'][1] .= ' 23:59:59';
        } else {
            if( 7 != $searchData['scmNo'][0] ){
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m', strtotime('-364 day')));
            }else{
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m', strtotime('-20 year')));
            }
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m'));
            //$this->search['searchDate'][0] .= ' 00:00:00';
            //$this->search['searchDate'][1] .= ' 23:59:59';
        }
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //공급사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }

    }

    /**
     * 주문 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData){

        if(in_array($searchData['scmNo'][0], SlCodeMap::STATISTICS_MERGE)){
            $mergeSw = true;
        }else{
            $mergeSw = false;
        }

        //$isProvider = Manager::isProvider();

        // --- 검색 설정 (WHERE 을 여기서 설정...)
        $this->_setSearch($searchData);
        //검색 값 설정
        if (empty($this->search) === false) {
            $getData['search'] = $this->search;
        }
        // 라디오 체크값 설정
        if (empty($this->checked) === false) {
            $getData['checked'] = $this->checked;
        }

        $scmGoodsList = $this->sql->getList($this->search);
        $maxOptionCount = 0;
        //gd_debug($scmGoodsList);

        foreach($scmGoodsList['listData'] as $key => $value  ){
            $repGoodsNo = $value['goodsNo'];//대표 상품 번호
            $goodsNoList = [$value['goodsNo']];

            if( $mergeSw ){
                $searchVo = new SearchVo(['goodsNm=?','delFl=?'], [$value['goodsNm'],'n']);
                $goodsClassList = DBUtil2::getListBySearchVo(DB_GOODS, $searchVo); //대표명
                $goodsNoList = []; //재할당
                foreach($goodsClassList as $goodsClassData){
                    $goodsNoList[] = $goodsClassData['goodsNo'];
                }
                $repGoodsNo = $goodsClassList[0]['goodsNo'];
            }else{

            }
            $optionList = DBUtil2::getList(DB_GOODS_OPTION,'optionSellFl=\'y\' and goodsNo', $repGoodsNo);

            $goodsNoInValue = implode(',',$goodsNoList);

            $inputStartDate = $this->search['searchDate'][0] . '-01 00:00:00';
            $inputEndDate = $this->search['searchDate'][1] . '-' . date('t', strtotime($this->search['searchDate'][1].'-01')). ' 23:59:59';

            if ( false !== strpos(\Request::getPhpSelf(), 'scm_stock_monthly_list2') ){
                $stockData = $this->setMonthlyStockIn($optionList,$inputStartDate,$inputEndDate,$goodsNoInValue, $value);
            }else{
                $stockData = $this->setMonthlyStockOut($optionList,$inputStartDate,$inputEndDate,$goodsNoInValue, $value);
            }

            //상품들의 옵션 최댓수 구하기
            if(  count($optionList) > $maxOptionCount  ){
                $maxOptionCount = count($optionList);
            }
            $value['optionList'] = $stockData['optionList'];
            $value['monthlyData'] = $stockData['monthlyData'];
            $scmGoodsList['listData'][$key] = $value;
        }

        $getData['title'] = $scmGoodsList['listData'];
        $getData['maxOptionCnt'] = $maxOptionCount+1;

        $getData['page'] = $scmGoodsList['pageData'];
        $getData['data'] = $scmGoodsList['listData'];

        return $getData;
    }

    /**
     * 월별 출고 수량 셋팅
     * @param $optionList
     * @param $inputStartDate
     * @param $inputEndDate
     * @param $goodsNoInValue
     * @param $goodsData
     * @return array
     */
    public function setMonthlyStockOut($optionList,$inputStartDate,$inputEndDate,$goodsNoInValue,$goodsData){

        $allOptionStockCnt = 0;
        $monthlyData = [];

        foreach($optionList as $optionListKey => $optionListValue){
            $sqlCommon = "SELECT DATE_FORMAT(a.regDt, '%Y-%m') AS stockDate2, DATE_FORMAT(a.regDt, '%Y년 %m월') AS stockDate,  IFNULL(ABS(SUM(a.stockCnt)),0) AS stockCnt, b.stockCnt AS currentCnt FROM sl_goodsStock a ";
            $sqlCommon .= " LEFT OUTER JOIN es_goodsOption b ON a.goodsNo = b.goodsNo AND a.optionNo = b.optionNo  ";
            $sqlCommon .= " JOIN es_order o ON o.orderNo = a.orderNo";
            $sqlCommon .= " WHERE a.goodsNo IN ({$goodsNoInValue}) AND a.optionNo={$optionListValue['optionNo']}  ";
            $sqlCommon .= " AND a.stockCnt != 0 AND stockType=2 AND LEFT(o.orderStatus,1) <> 'f' AND LEFT(o.orderStatus,1) <> 'c' AND a.regDt >= '{$inputStartDate}' AND  '{$inputEndDate}' >= a.regDt ";
            $sqlCommon .= " GROUP BY stockDate, stockDate2 ORDER BY stockDate ";
            //AND a.stockCnt > 0
            //gd_debug( $sqlCommon );

            $stockOutList = DBUtil2::runSelect($sqlCommon);
            $totalStockCnt = 0;
            foreach( $stockOutList as $stockOutEach ){
                $allOptionStockCnt += $stockOutEach['stockCnt'];
                $totalStockCnt += $stockOutEach['stockCnt'];
                $monthlyData[$stockOutEach['stockDate']][$optionListValue['optionNo']]['stockOut'] = $stockOutEach['stockCnt'];
                $monthlyData[$stockOutEach['stockDate']]['total']['stockOut'] += $stockOutEach['stockCnt'];

                if(!empty($stockOutEach['stockDate2'])){
                    $searchBeginDate = $stockOutEach['stockDate2'].'-01';
                    $searchEndDate = $stockOutEach['stockDate2'].'-'.date('t', strtotime($searchBeginDate).'');
                }else{
                    $searchBeginDate = $inputStartDate;
                    $searchEndDate = $inputEndDate;
                }
                $reqQuery = "/sitelab/stock_list.php?sort=a.regDt+desc&searchFl=y&pageNum=100&scmFl=all&key=b.goodsNm&keyword={$goodsData['goodsNm']}&searchDateFl=a.regDt&searchDate%5B%5D={$searchBeginDate}&searchDate%5B%5D={$searchEndDate}&searchPeriod=364&stockType=&stockReason%5B%5D=6&stockReason%5B%5D=7&stockReason%5B%5D=11";
                $monthlyData[$stockOutEach['stockDate']][$optionListValue['optionNo']]['detailLink'] = Request::getDomainUrl().$reqQuery."&optionNo={$optionListValue['optionNo']}";
                $monthlyData[$stockOutEach['stockDate']]['total']['detailLink'] = Request::getDomainUrl().$reqQuery;
            }

            $optionListValue['stockDetailList'] =  $stockOutList;
            $optionListValue['totalStockCnt'] =  $totalStockCnt;
            $optionListValue['currentCnt'] =  DBUtil2::runSelect("select sum(stockCnt) as stockCnt from es_goodsOption where goodsNo IN ({$goodsNoInValue}) AND optionNo={$optionListValue['optionNo']}")[0]['stockCnt'];

            $optionNameList = [];
            if(  !empty($optionListValue['optionValue1'])  )  $optionNameList[] = $optionListValue['optionValue1'];
            if(  !empty($optionListValue['optionValue2'])  )  $optionNameList[] = $optionListValue['optionValue2'];
            if(  !empty($optionListValue['optionValue3'])  )  $optionNameList[] = $optionListValue['optionValue3'];
            if(  !empty($optionListValue['optionValue4'])  )  $optionNameList[] = $optionListValue['optionValue4'];
            if(  !empty($optionListValue['optionValue5'])  )  $optionNameList[] = $optionListValue['optionValue5'];

            $optionListValue['optionName'] = ( count($optionNameList) > 0  ) ? implode('/', $optionNameList) : '옵션없음' ;
            $optionList[$optionListKey] = $optionListValue;
        }

        //한번 더 돌려서 옵션별 비율 계산
        $optionTotalStockCnt = 0;
        $optionTotalStockPercent = 0;
        $optionTotalCurrentCnt = 0;
        foreach($optionList as $optionListKey => $optionListValue){
            $optionListValue['totalStockPercent'] = empty($optionListValue['totalStockCnt'])?0:$optionListValue['totalStockCnt'] / $allOptionStockCnt * 100;
            $optionList[$optionListKey] = $optionListValue;
            $optionTotalStockPercent += $optionListValue['totalStockPercent'];
            $optionTotalStockCnt += $optionListValue['totalStockCnt'];
            $optionTotalCurrentCnt += $optionListValue['currentCnt'];
        }

        $optionList[count($optionList)] = [
            'optionNo' => 'total',
            'optionName' => '합계',
            'totalStockCnt' => $optionTotalStockCnt,
            'totalStockPercent' => number_format($optionTotalStockPercent),
            'currentCnt' => $optionTotalCurrentCnt,
        ];

        ksort($monthlyData);

        return [
            'optionList' => $optionList ,
            'monthlyData' => $monthlyData ,
        ];
    }

    /**
     * 월별 입고 수량 셋팅
     * @param $optionList
     * @param $inputStartDate
     * @param $inputEndDate
     * @param $goodsNoInValue
     * @param $goodsData
     * @return array
     */
    public function setMonthlyStockIn($optionList,$inputStartDate,$inputEndDate,$goodsNoInValue,$goodsData){
        $allOptionStockCnt = 0;
        $monthlyData = [];

        foreach($optionList as $optionListKey => $optionListValue){
            $sqlCommon = "SELECT DATE_FORMAT(a.regDt, '%Y-%m') AS stockDate2, DATE_FORMAT(a.regDt, '%Y년 %m월') AS stockDate,  IFNULL(ABS(SUM(a.stockCnt)),0) AS stockCnt, b.stockCnt AS currentCnt FROM sl_goodsStock a ";
            $sqlCommon .= " LEFT OUTER JOIN es_goodsOption b ON a.goodsNo = b.goodsNo AND a.optionNo = b.optionNo  ";
            $sqlCommon .= " WHERE a.goodsNo IN ({$goodsNoInValue}) AND a.optionNo={$optionListValue['optionNo']} AND a.regDt >= '{$inputStartDate}' AND  '{$inputEndDate}' >= a.regDt ";
            $sqlCommon .= " AND a.stockType=1 AND a.stockReason IN (1,10) AND a.stockCnt > 0 ";
            $sqlCommon .= " GROUP BY stockDate, stockDate2 ORDER BY stockDate ";

            $stockInList = DBUtil2::runSelect($sqlCommon);
            $totalStockCnt = 0;
            foreach( $stockInList as $stockInEach ){
                $allOptionStockCnt += $stockInEach['stockCnt'];
                $totalStockCnt += $stockInEach['stockCnt'];
                $monthlyData[$stockInEach['stockDate']][$optionListValue['optionNo']]['stockIn'] = $stockInEach['stockCnt'];
                $monthlyData[$stockInEach['stockDate']]['total']['stockIn'] += $stockInEach['stockCnt'];

                if(!empty($stockInEach['stockDate2'])){
                    $searchBeginDate = $stockInEach['stockDate2'].'-01';
                    $searchEndDate = $stockInEach['stockDate2'].'-'.date('t', strtotime($searchBeginDate).'');
                }else{
                    $searchBeginDate = $inputStartDate;
                    $searchEndDate = $inputEndDate;
                }

                $reqQuery = "/sitelab/stock_list.php?sort=a.regDt+desc&searchFl=y&pageNum=100&scmFl=all&key=b.goodsNm&keyword={$goodsData['goodsNm']}&searchDateFl=a.regDt&searchDate%5B%5D={$searchBeginDate}&searchDate%5B%5D={$searchEndDate}&searchPeriod=364&stockType=&stockReason%5B%5D=1&stockReason%5B%5D=10";
                $monthlyData[$stockInEach['stockDate']][$optionListValue['optionNo']]['detailLink'] = Request::getDomainUrl().$reqQuery."&optionNo={$optionListValue['optionNo']}";
                $monthlyData[$stockInEach['stockDate']]['total']['detailLink'] = Request::getDomainUrl().$reqQuery;
            }
            $optionListValue['stockDetailList'] =  $stockInList;
            $optionListValue['totalStockCnt'] =  $totalStockCnt;
            $optionListValue['currentCnt'] =  DBUtil2::runSelect("select sum(stockCnt) as stockCnt from es_goodsOption where goodsNo IN ({$goodsNoInValue}) AND optionNo={$optionListValue['optionNo']}")[0]['stockCnt'];

            $optionNameList = [];
            if(  !empty($optionListValue['optionValue1'])  )  $optionNameList[] = $optionListValue['optionValue1'];
            if(  !empty($optionListValue['optionValue2'])  )  $optionNameList[] = $optionListValue['optionValue2'];
            if(  !empty($optionListValue['optionValue3'])  )  $optionNameList[] = $optionListValue['optionValue3'];
            if(  !empty($optionListValue['optionValue4'])  )  $optionNameList[] = $optionListValue['optionValue4'];
            if(  !empty($optionListValue['optionValue5'])  )  $optionNameList[] = $optionListValue['optionValue5'];
            $optionListValue['optionName'] = ( count($optionNameList) > 0  ) ? implode('/', $optionNameList) : '옵션없음' ;

            $optionList[$optionListKey] = $optionListValue;
        }

        //한번 더 돌려서 옵션별 비율 계산
        $optionTotalStockCnt = 0;
        $optionTotalStockPercent = 0;
        $optionTotalCurrentCnt = 0;
        foreach($optionList as $optionListKey => $optionListValue){
            $optionListValue['totalStockPercent'] = empty($optionListValue['totalStockCnt'])?0:$optionListValue['totalStockCnt'] / $allOptionStockCnt * 100;
            $optionList[$optionListKey] = $optionListValue;
            $optionTotalStockPercent += $optionListValue['totalStockPercent'];
            $optionTotalStockCnt += $optionListValue['totalStockCnt'];
            $optionTotalCurrentCnt += $optionListValue['currentCnt'];
        }
        $optionList[count($optionList)] = [
            'optionNo' => 'total',
            'optionName' => '합계',
            'totalStockCnt' => $optionTotalStockCnt,
            'totalStockPercent' => number_format($optionTotalStockPercent),
            'currentCnt' => $optionTotalCurrentCnt,
        ];

        ksort($monthlyData);

        return [
            'optionList' => $optionList ,
            'monthlyData' => $monthlyData ,
        ];
    }

    public function getOptionMonthlyStockCount($goodsNo, $startDate, $endDate){
        $optionList = DBUtil2::getList(DB_GOODS_OPTION,  'optionSellFl=\'y\' and goodsNo', $goodsNo );
        foreach($optionList as $optionListKey => $optionListValue){
            $sql = "SELECT DATE_FORMAT(regDt, '%y/%m') AS stockDate  ,  IFNULL(ABS(SUM(stockCnt)),0) AS stockCnt FROM sl_goodsStock WHERE goodsNo={$goodsNo}  AND optionNo={$optionListValue['optionNo']} AND stockType=2 ";
            $sql .= " AND regDt >= '{$startDate}' AND  '{$endDate}' >= regDt  GROUP BY stockDate ORDER BY regDt";
            $optionStockDataList = DBUtil2::runSelect($sql);
            $totalStockCnt = 0;
            foreach( $optionStockDataList as $optionStockData ){
                $totalStockCnt += $optionStockData['stockCnt'];
            }
            $optionListValue['stockDetailList'] =  $optionStockDataList;
            $optionListValue['totalStockCnt'] = $totalStockCnt;

            $optionNameList = [];
            if(  !empty($optionListValue['optionValue1'])  )  $optionNameList[] = $optionListValue['optionValue1'];
            if(  !empty($optionListValue['optionValue2'])  )  $optionNameList[] = $optionListValue['optionValue2'];
            if(  !empty($optionListValue['optionValue3'])  )  $optionNameList[] = $optionListValue['optionValue3'];
            if(  !empty($optionListValue['optionValue4'])  )  $optionNameList[] = $optionListValue['optionValue4'];
            if(  !empty($optionListValue['optionValue5'])  )  $optionNameList[] = $optionListValue['optionValue5'];
            $optionListValue['optionName'] = ( count($optionNameList) > 0  ) ? implode('/', $optionNameList) : '단일옵션' ;
            $optionList[$optionListKey] = $optionListValue;
        }
        return $optionList;
    }


}

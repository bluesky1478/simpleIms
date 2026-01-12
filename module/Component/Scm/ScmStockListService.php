<?php
namespace Component\Scm;

use App;
use Component\Ims\ImsDBName;
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
class ScmStockListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'상품명'
        ,'현재 수량 및 조회기간 출고수량'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmStockListSql::class);
    }

    protected function _setSearch($searchData){

        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'a.goodsNm' => '상품명'
        ];
        // -- 기간
        /*$this->search['combineTreatDate'] = [
            'a.regDt' => __('등록일'),
        ];*/
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
        ],$searchData);
        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl',
            'delFl',
        ],$searchData,'all');
        $this->setRadioSearch([
        ],$searchData,'');

        // 기간 설정
        //$this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]. ' 00:00:00');
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
            $this->search['searchDate'][0] .= ' 00:00:00';
            $this->search['searchDate'][1] .= ' 23:59:59';
        } else {
            if( 7 != $searchData['scmNo'][0] ){
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-364 day')));
            }else{
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-20 year')));
            }
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
            $this->search['searchDate'][0] .= ' 00:00:00';
            $this->search['searchDate'][1] .= ' 23:59:59';
        }
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //공급사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }

        //$this->search['delFl'] = gd_isset($this->search['delFl'],'n');
        //gd_debug($this->checked['delFl']);
        if(empty($searchData['delFl'])){
            $this->search['delFl'] = 'n';
            $this->checked['delFl']['n'] = 'checked="checked"';
            unset( $this->checked['delFl']['all'] );
        }
        //기간 Validation
        /*if (DateTimeUtils::intervalDay($this->search['searchDate'][0], $this->search['searchDate'][1]) > 365) {
            throw new AlertBackException(__('1년이상 기간으로 검색하실 수 없습니다.'));
        }*/

    }

    /**
     * 주문 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData){
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
        foreach($scmGoodsList['listData'] as $key => $value  ){

            $optionList = DBUtil2::getList(DB_GOODS_OPTION,  'optionSellFl=\'y\' and goodsNo', $value['goodsNo'] );

            foreach($optionList as $optionListKey => $optionListValue){
                //현대나 아시아나의 경우 여기를 바꿔주자. (다만 리뉴얼 하자 / 삼영 출고를 기준으로 한다)
                if( in_array($searchData['scmNo'][0],[32,34]) ){

                    $linkCodeList = DBUtil2::getList('sl_goodsOptionLink','optionSno',$optionListValue['sno']);
                    $codeMap = SlCommonUtil::arrayAppKeyValue($linkCodeList,'code','code');
                    $codeMapStr = "'".implode("','",$codeMap)."'";
                    if( "''" == $codeMapStr ){
                        $optionListValue['totalStockCnt'] = 0;
                    }else{
                        $sql = "select sum(quantity) as qty from sl_3plStockInOut where thirdPartyProductCode in ({$codeMapStr}) and inOutType=2";
                        $optionListValue['totalStockCnt'] = DBUtil2::runSelect($sql)[0]['qty'];
                    }

                    //gd_debug( DBUtil2::runSelect($sql)[0] );
                }else{
                    $sqlCommon = "SELECT DATE_FORMAT(regDt, '%y/%m') AS stockDate, IFNULL(ABS(SUM(stockCnt)),0) AS stockCnt FROM sl_goodsStock WHERE goodsNo={$value['goodsNo']}  AND optionNo={$optionListValue['optionNo']}  ";
                    $sqlInStock = $sqlCommon . " AND stockType=1 AND stockReason IN (1, 10)   GROUP BY stockDate ORDER BY regDt";
                    $sqlOutStock = $sqlCommon . " AND stockType=2 AND regDt >= '{$this->search['searchDate'][0]}' AND  '{$this->search['searchDate'][1]}' >= regDt  GROUP BY stockDate ORDER BY regDt";
                    //, '1'=>'신규 입고' , , '2'=>'관리자수정 입고' , , '10'=>'재고관리 입고'
                    //gd_debug($sqlOutStock);
                    $optionStockDataList = DBUtil2::runSelect($sqlOutStock);

                    $totalStockCnt = 0;
                    foreach( $optionStockDataList as $optionStockData ){
                        $totalStockCnt += $optionStockData['stockCnt'];
                    }
                    $optionListValue['stockDetailList'] =  $optionStockDataList;
                    $optionListValue['stockInputList'] =  DBUtil2::runSelect($sqlInStock);
                    $optionListValue['totalStockCnt'] = $totalStockCnt;

                    $replaceList = [
                        '티스테이션',
                        '더타이어샵',
                    ];
                    foreach ($replaceList as $replace){
                        $optionListValue['optionName'] = str_replace($replace, '', $optionListValue['optionName']);
                    }
                }

                //공통적인 부분
                $optionNameList = [];
                if(  !empty($optionListValue['optionValue1'])  )  $optionNameList[] = $optionListValue['optionValue1'];
                if(  !empty($optionListValue['optionValue2'])  )  $optionNameList[] = $optionListValue['optionValue2'];
                if(  !empty($optionListValue['optionValue3'])  )  $optionNameList[] = $optionListValue['optionValue3'];
                if(  !empty($optionListValue['optionValue4'])  )  $optionNameList[] = $optionListValue['optionValue4'];
                if(  !empty($optionListValue['optionValue5'])  )  $optionNameList[] = $optionListValue['optionValue5'];
                $optionListValue['optionName'] = ( count($optionNameList) > 0  ) ? implode('/', $optionNameList) : '단일옵션' ;

                $optionList[$optionListKey] = $optionListValue;
            }

            //현재 수량
            $value['optionList'] = $optionList;
            if(  count($optionList) > $maxOptionCount  ){
                $maxOptionCount = count($optionList);
            }

            $value['rowspan'] = 3;

            $scmGoodsList['listData'][$key] = $value;
        }

        $getData['title'] = $scmGoodsList['listData'];
        $getData['maxOptionCnt'] = $maxOptionCount;

        $getData['page'] = $scmGoodsList['pageData'];
        $getData['data'] = $scmGoodsList['listData'];

        return $getData;
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
            $optionListValue['optionName'] = ( count($optionNameList) > 0  ) ? implode('/', $optionNameList) : '옵션없음' ;
            $optionList[$optionListKey] = $optionListValue;
        }
        return $optionList;
    }


}

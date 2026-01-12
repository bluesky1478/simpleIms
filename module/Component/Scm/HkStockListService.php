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
 *  한국타이어 재고표 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class HkStockListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '구분'
        ,'채널'
        ,'종류'
        ,'과거기간 출고'
        ,'과거기간 재고'
        ,'조회기간 출고 '
        ,'현재재고'
        ,'누적입고(발주량)'
    ];

    private $sql;
    private $search;

    const CALC_FIELD_LIST = ['pastCnt','lastPastCnt','currentCnt', 'stockCnt','accSaleCnt'];

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\HkStockListSql::class);
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
        // 검색 항목 설정 끝 ----------------------------------------------------------

        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'goodsNo'
            ,'isOpenSum'
        ],$searchData);
        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl'
        ],$searchData,'all');
        $this->setRadioSearch([
            'isOpenSum'
        ],$searchData,'');

        //과거
        if(empty(\Request::get()->get('searchDate'))){
            $arr[] = date('Y-m-d', strtotime('-365 day')); //1년전
            $arr[] = date('Y-m-d', strtotime('-182 day')); //6개월전.
            \Request::get()->set('searchDate', $arr);
        }
        SlCommonUtil::setDefaultDate('searchDate2','-181 day');

        $this->search['searchDate'] = \Request::get()->get('searchDate');
        $this->search['searchDate2'] = \Request::get()->get('searchDate2');

        //기타 처리 ----------------------------------------------------------
        //공급사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }
    }

    /**
     * 한국타이어 옵션 치환
     * @param $str
     * @return string|string[]
     */
    public function replaceHkOption($str){
        $replaceStr = [
            '티스테이션',
            '더타이어샵',
            '(',
            ')',
        ];

        $replaceTargetStr = $str;
        foreach($replaceStr as $replace){
            $replaceTargetStr = str_replace($replace, '', $replaceTargetStr);
        }

        return $replaceTargetStr;
    }


    /**
     * 주문 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData){
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

        $goodsList = $this->sql->getGoodsList($this->search);

        //1. 춘추, 하계, 동계 구분.  ( 상품명으로 구분 )
        //2. 채널 구분 ( 옵션으로 구분 )
        //3. 종류 구분 (티셔츠 , 점퍼, 바지)
        //4. 업체 구분 (TS , TTS/TBX/HK)
        $seasonList = ['춘추','하계','추계','동계'];

        if( !empty($searchData['isOpenSum']) ){
            $seasonList = array_merge($seasonList, ['춘추(OP)','하계(OP)','추계(OP)','동계(OP)']);
        }
        //gd_debug($seasonList);
        $channelList = ['TS','HK'];
        $typeList = ['카라티','조끼','점퍼','바지'];
        $storeTypeList = ['TTS','TS','TBX','HK'];
        $channelType = ['TS','HK','티스테이션'];

        $goodsMap = [];
        foreach($goodsList as $goodsData){
            if( empty($goodsMap[$goodsData['goodsNo']]['info']) ){
                $goodsMap[$goodsData['goodsNo']]['info'] = SlCommonUtil::getAvailData($goodsData, ['goodsNm', 'isOpenFl', 'goodsNo' ]);

                $goodsMap[$goodsData['goodsNo']]['info']['season'] = SlCommonUtil::arrayInString($goodsData['goodsNm'], $seasonList);

                //오픈패키지 처리
                if( !empty($goodsData['isOpenFl']) && !empty($searchData['isOpenSum']) ){
                    $goodsMap[$goodsData['goodsNo']]['info']['season'] = $goodsMap[$goodsData['goodsNo']]['info']['season'].'(OP)';
                }

                $goodsMap[$goodsData['goodsNo']]['info']['type'] = SlCommonUtil::arrayInString($goodsData['goodsNm'], $typeList);
            }

            $goodsData['season'] = $goodsMap[$goodsData['goodsNo']]['info']['season'];
            $goodsData['type'] = $goodsMap[$goodsData['goodsNo']]['info']['type'];

            $optionList = [];
            for($i=1; 5>=$i; $i++){
                if( !empty($goodsData['optionValue'.$i]) ){
                    $goodsData['optionValue'.$i] = $this->replaceHkOption($goodsData['optionValue'.$i]);
                    $optionList[] = $goodsData['optionValue'.$i];
                }
            }

            $goodsData['optionStr'] = implode('/', $optionList);
            $goodsData['storeType'] = SlCommonUtil::arrayInString($goodsData['optionStr'], $storeTypeList);
            if(empty($goodsData['storeType'])){
                $goodsData['storeType'] = str_replace('티스테이션','TS',SlCommonUtil::arrayInString($goodsData['goodsNm'], $channelType));
            }
            if( 'TS' === strtoupper($goodsData['storeType'])){
                $goodsData['channelType'] = 'TS';
            }else{
                $goodsData['channelType'] = 'HK';
            }
            $goodsMap[$goodsData['goodsNo']]['option'][$goodsData['optionNo']] = $goodsData;
        }

        //gd_debug( count($goodsMap) );

        $goodsNoList = array_keys($goodsMap);
        $pastList = $this->sql->getPastList($goodsNoList, $this->search); //goodsNo와 optionNo기준 : 과거 조회기간 출고량
        $currentList = $this->sql->getCurrentList($goodsNoList, $this->search); //조회기간 출고량
        $accSaleList = $this->sql->getAccStockList($goodsNoList); //조회기간 출고량
        //$remainStockList = $this->sql->getRemainStockList($goodsNoList, $this->search); //과거 조회일 기준 잔여재고

        $this->setStockCnt('pastCnt', $pastList, $goodsMap); //재고 현황 셋팅 (과거)
        $this->setStockCnt('currentCnt', $currentList, $goodsMap); //재고 현황 셋팅 (현재-사실상 그냥 조회)
        $this->setStockCnt('accSaleCnt', $accSaleList, $goodsMap); //누적 판매 수량
        //$this->setStockCnt('remainStockCnt', $accSaleList, $goodsMap); //과거 잔여 재고
        // (현재 재고 입력 분에 따라 과거 재고량이 변경된다 ? ) --> 이것도 말은 안되네. (이후 추가 잔여분) 누적이야 그렇다 쳐도.
        // 추가 Order에 대한 부분이 있어서..... 그냥 신뢰 해야하나 ?
        //gd_debug($accSaleList);

        $resultMap = [];
        $seasonTotalMap = [];
        foreach($goodsMap as $goodsData){
            $optionList = $goodsData['option'];
            foreach($optionList as $each){
                $season = $each['season'];
                $channel = $each['channelType'];
                $type = $each['type'];
                $storeType = $each['storeType'];

                $this->setDataSummation($resultMap[$season][$channel][$type], $each);
                if( 'HK' === $channel ){
                    $this->setDataSummation($resultMap[$season][$channel][$type]['storeData'][$storeType], $each);
                }

                //시즌별 소계.
                $this->setDataSummation($seasonTotalMap[$season], $each);
            }
        }

        $resultList2 = [];
        $seasonCntMap = [];

        $beforeSeason = '';

        foreach($seasonList as $season){

            $seasonSpanCnt = 0;
            $totalCountMap = [];

            if(!empty($seasonTotalMap[$season])){
                foreach( $channelList as $channel ){
                    $channelCnt = 0;
                    foreach( $typeList as $type ){
                        $seasonData = $resultMap[$season][$channel][$type];
                        $seasonData['season'] = $season;
                        $seasonData['channel'] = $channel;
                        $seasonData['type'] = $type;
                        $seasonData['isMain'] = 'y';
                        unset($seasonData['storeData']);

                        if( !empty($seasonData['pastCnt']) || !empty($seasonData['currentCnt']) ){

                            if( 0 == $seasonSpanCnt ){
                                $seasonData['seasonRowspan'] = 'y';
                            }
                            if( 0 == $channelCnt ){
                                $seasonData['channelRowspan'] = 'y';
                            }

                            foreach( HkStockListService::CALC_FIELD_LIST as $calcField ){
                                $totalCountMap[$calcField] += $seasonData[$calcField];
                            }
                            $seasonData['totalCnt'] = $totalCountMap;

                            $resultList2[] = $seasonData;
                            $seasonSpanCnt++;
                            $channelCnt++;
                        }

                        //서브 데이터 추가 --------------------
                        if( 'HK' === $channel ){
                            unset($seasonData['storeData']);
                            foreach($storeTypeList as $store){
                                if( 'TS' !== $store ){
                                    //$seasonData['storeData'][$store] = $resultMap[$season][$channel][$type]['storeData'][$store];
                                    $addStoreData = $seasonData;
                                    $addStoreData['isMain'] = 'n';
                                    $addStoreData['type'] = $store;
                                    unset($addStoreData['channelRowspan']);
                                    foreach(HkStockListService::CALC_FIELD_LIST as $storeField){
                                        $addStoreData[$storeField] = $resultMap[$season][$channel][$type]['storeData'][$store][$storeField];
                                    }
                                    if( !empty($addStoreData['pastCnt']) || !empty($addStoreData['currentCnt']) ){
                                        $resultList2[] = $addStoreData;
                                        $seasonSpanCnt++;
                                        $channelCnt++;
                                    }
                                }
                            }
                        }
                        //--------------------

                    }
                    $seasonCntMap[$season]['channelSpan'][$channel] = $channelCnt;
                }
            }

            if( $seasonSpanCnt > 0  ){
                $seasonCntMap[$season]['seasonSpan'] = $seasonSpanCnt;
            }
        }


        $totalList = [];
        foreach( $seasonTotalMap as $seasonTotal ){
            $this->setDataSummation($totalList, $seasonTotal);
        }
        //gd_debug($totalList);
        //gd_debug($seasonTotalMap);
        //gd_debug($seasonCntMap);
        //gd_debug($resultList2);
        $getData['data'] = $resultList2;
        $getData['span'] = $seasonCntMap;
        $getData['seasonTotal'] = $seasonTotalMap;
        $getData['total'] = $totalList;

        return $getData;
    }

    public function setDataSummation(&$dataBox, $data){
        $dataBox['pastCnt'] += (int)$data['pastCnt'];//과거 재고량
        $dataBox['lastPastCnt'] += (int)$data['lastPastCnt'];//과거 재고량
        $dataBox['accSaleCnt'] += (int)$data['accSaleCnt']; //조회기간 입고량
        $dataBox['currentCnt'] += (int)$data['currentCnt'];//조회기간 출고량
        $dataBox['stockCnt'] += (int)$data['stockCnt'];//현재 재고
    }

    public function setStockCnt($fieldName, $list, &$map){
        foreach($list as $each){
            $map[$each['goodsNo']]['option'][$each['optionNo']][$fieldName] += ((int)$each['stockCnt']*-1) ;
            $map[$each['goodsNo']]['option'][$each['optionNo']]['last'.ucfirst($fieldName)] = (int)$each['afterCnt']; //마지막 재고
        }
    }


}

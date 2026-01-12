<?php
namespace Component\Order;

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
 * 품절상품 요청 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class SoldoutReqGoodsListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'공급사'
        ,'상품번호'
        ,'상품명'
    ];
    
    const EXCEL_LIST_TITLES = [
        '번호'
        ,'공급사'
        ,'상품번호'
        ,'상품명'
        ,'알림신청'
        ,'알림발송'
        ,'옵션별 신청 수량 / 현재 재고'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Order\Sql\SoldoutReqGoodsListSql::class);
    }

    protected function _setSearch($searchData){

        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'e.goodsNm' => '상품명'
            ,'a.goodsNo' => '상품코드'
        ];
        // -- 기간
        $this->search['combineTreatDate'] = [
            'a.regDt' => __('요청일'),
        ];
        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }
        // --- 정렬
        $this->search['sortList'] = [
            'a.regDt desc' => sprintf('%s↓', __('요청일')),
            'a.regDt asc' => sprintf('%s↑', __('요청일')),
        ];
        $this->search['sort'] = gd_isset( $searchData['sort'] ,'a.regDt desc' );

        // -- 페이징 기본 설정
        $this->search['page'] = gd_isset( $searchData['page'] ,1);
        $this->search['pageNum'] = gd_isset( $searchData['pageNum'] ,50);

        // 검색 항목 설정 끝 ----------------------------------------------------------

        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'searchPeriod'
            , 'sendType',
        ],$searchData);
        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl',
        ],$searchData,'all');
        $this->setRadioSearch([
            'sendType',
        ],$searchData,'0');

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]. ' 00:00:00');
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            //3개월
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-364 day')));
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
        }
        $this->search['searchDate'][0] .= ' 00:00:00';
        $this->search['searchDate'][1] .= ' 23:59:59';
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //공급사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }
        //기간 Validation
        /*if (DateTimeUtils::intervalDay($this->search['searchDate'][0], $this->search['searchDate'][1]) > 365) {
            throw new AlertBackException(__('1년이상 기간으로 검색하실 수 없습니다.'));
        }*/
    }

    /**
     * 재고 리스트
     * @param string $searchData 검색 데이타
     * @param $type
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

        $reqList = $this->sql->getList($this->search);
        $optionList = $this->sql->getOptionList($this->search);

        $optionReqMap = [];
        foreach( $optionList as $optionEach ){
            $optionReqMap[$optionEach['goodsNo']][$optionEach['optionSno']] = $optionEach['reqCnt'];
        }
        //gd_debug($optionReqMap);
        $getData['optionReqMap'] = $optionReqMap;

        $getData['page'] = $reqList['pageData'];
        //$getData['data'] = $reqList['listData'];
        $maxOptionCount = [
            'maxCnt' => 0
        ];
        $getData['data'] = SlCommonUtil::setEachData($reqList['listData'], $this, 'setListData', $maxOptionCount);

        //gd_debug('===> 상품별 최대 옵션 ');
        //gd_debug($getData['data'][count($getData['data'])-1]['optionMaxCount']);

        //$getData['maxOptionCount'] = $getData['data'][count($getData['data'])-1]['optionMaxCount'];
        $getData['maxOptionCount'] = $getData['data'][count($getData['data'])-1]['optionMaxCount'];

        return $getData;
    }

    /**
     * 리스트 데이터를 셋팅
     * @param $each
     * @param $key
     * @param $maxOptionCount
     * @return mixed
     */
    public function setListData($each, $key, &$maxOptionCount){
        $optionList = DBUtil2::getList(DB_GOODS_OPTION, 'goodsNo', $each['goodsNo']);
        $each['optionList'] = SlCommonUtil::setEachData($optionList, $this, 'setOptionData');
        $optionCount = count($optionList);
        if( $optionCount > $maxOptionCount['maxCnt'] ){
            $maxOptionCount['maxCnt'] = $optionCount;
        }
        $each['optionMaxCount'] = $maxOptionCount['maxCnt'];
        return $each;
    }

    /**
     * 리스트 내 옵션을 셋팅
     * @param $each
     * @param $key
     * @return mixed
     */
    public function setOptionData($each, $key){
        //sno
        $optionNameList = [];
        for($i=0; 5>$i; $i++){
            if(!empty($each['optionValue'.($i+1)])){
                $optionNameList[] = $each['optionValue'.($i+1)];
            }
        }
        $each['optionFullName'] = implode('/',$optionNameList);
        return $each;
    }

}

<?php
namespace Component\Stock;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;

/**
 * 재고 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class StockListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'상품번호'
        ,'공급사'
        ,'상품명'
        ,'옵션명'
        ,'유형'
        ,'사유'
        ,'수량'
        ,'주문번호'
        ,'회원명'
        ,'회원ID'
        ,'이력등록일자'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Stock\Sql\StockListSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'b.goodsNm' => '상품명'
            ,'a.goodsNo' => '상품코드'
            ,'d.memNm' => '회원명'
            ,'d.memId' => '회원ID'
            ,'a.orderNo' => '주문번호'
        ];
        // -- 기간
        $this->search['combineTreatDate'] = [
            'a.regDt' => __('이력등록일'),
        ];
        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }
        // --- 정렬
        $this->search['sortList'] = [
            'a.regDt desc' => sprintf('%s↓', __('이력등록일')),
            'a.regDt asc' => sprintf('%s↑', __('이력등록일')),
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
            ,'stockType'
            ,'stockReason'
            ,'searchPeriod'
            ,'optionNo'
            ,'goodsNo'
        ],$searchData);
        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl'
        ],$searchData,'all');
        $this->setRadioSearch([
            'stockType'
        ],$searchData,'');
        // 체크박스 검색 설정
        $this->setCheckSearch([
            'stockReason'
        ]);

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-6 day')));
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
        }
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //공급사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }
        //기간 Validation
        /*if (DateTimeUtils::intervalDay($this->search['searchDate'][0], $this->search['searchDate'][1]) > 99999) {
            throw new AlertBackException(__('검색 기간이 너무 깁니다. 검색하실 수 없습니다.'));
        }*/

    }

    /**
     * 재고 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getStockList($searchData){
        return $this->getTraitList($searchData,'getStockList'); //SQL List 정의 되어 있어야함
    }

    /**
     * 집계표에서 전달된 상세 리스트를 보여준다.
     * @param $inputData
     * @return array
     */
    public function getStatToList($inputData){
        $searchData['goodsNo'] = $inputData['goodsNo'];
        $searchData['optionNo'] = $inputData['optionNo'];
        $searchData['searchDate'][0] = $inputData['startDate'];
        $searchData['searchDate'][1] = $inputData['endDate'];
        $searchData['page'] = '1';
        $searchData['sort'] = 'a.regDt asc';
        $searchData['pageNum'] = '100000';
        return $this->getStockList($searchData);
    }


}

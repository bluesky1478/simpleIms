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
 * 재고 집계표 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class StockStatService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'상품번호'
        ,'고객사'
        ,'상품명'
        ,'옵션명'
        ,'기간입고'
        ,'기간출고'
        ,'상세보기'
        ,'조회기간'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Stock\Sql\StockStatSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'b.goodsNm' => '상품명'
            ,'a.goodsNo' => '상품코드'
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
            'a.regDt desc' => sprintf('%s↓', __('상품번호')),
            'a.regDt asc' => sprintf('%s↑', __('상품번호')),
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
            'scmFl'
        ],$searchData,'all');

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-3 month')));
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
        }
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //고객사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }
    }

    /**
     * 재고 리스트
     * @param string  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getStockList($searchData){
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

        $stockList = $this->sql->getStockList($this->search);
        //gd_debug($searchData);
        //gd_debug($stockList['listData']);

        $getData['page'] = $stockList['pageData'];
        $getData['data'] = $stockList['listData'];

        return $getData;
    }

}

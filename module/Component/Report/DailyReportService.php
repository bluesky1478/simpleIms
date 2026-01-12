<?php
namespace Component\Report;

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
 */
class DailyReportService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        , '상품번호'
        , '공급사'
        , '상품명'
        , '옵션명'
        , '유형'
        , '사유'
        , '수량'
        , '주문번호'
        , '회원명'
        , '회원ID'
        , '이력등록일자'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Report\Sql\DailyReportSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }
        // 검색 항목 설정 끝 ----------------------------------------------------------

        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'scmNo'
            ,'searchDate'
        ],$searchData);
        // 기간 설정
        if( empty($this->search['searchDate']) ){
            $this->search['searchDate'] = gd_isset($searchData['searchDate'], date('Y-m-d', strtotime('-1 day')));
        }
        //공급사
        if( empty($this->search['scmNo']) ){
            $this->search['scmNo'] = gd_isset($searchData['scmNo'], 2);
        }
        // 검색 설정 끝 ----------------------------------------------------------
    }

    /**
     * 집계
     */
    public function getStat($searchData){
        // --- 검색 설정 (WHERE 을 여기서 설정...)
        $this->_setSearch($searchData);
        //검색 값 설정
        if (empty($this->search) === false) {
            $getData['search'] = $this->search;
        }
        $getData['data'] = $this->sql->getStat($this->search)[0];
        return $getData;
    }

    /**
     * 주문상세
     */
    public function getOrderStat(){
        $getData['data'] = $this->sql->getOrderStat($this->search);
        return $getData;
    }

    /**
     * 교환, 반품, AS 리스트
     * @param $handleCase
     * @return mixed
     */
    public function getHandleData($handleCase){
        $getData['data'] = $this->sql->getHandleData($this->search, $handleCase);
        return $getData;
    }

}

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
class SoldoutReqListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'공급사'
        ,'신청자명'
        ,'연락처'
        ,'회원정보'
        ,'상품명'
        ,'옵션/수량'
        ,'배송지점'
        ,'요청날짜/시간'
        ,'입고알림'
        ,'알림시간'
    ];

    const EXCEL_LIST_TITLES = [
        '공급사'
        ,'신청자명'
        ,'연락처'
        ,'회원ID'
        ,'회원명'
        ,'닉네임'
        ,'상품번호'
        ,'상품명'
        ,'옵션'
        ,'수량'
        ,'배송지점'
        ,'요청날짜/시간'
        ,'입고알림'
        ,'알림시간'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Order\Sql\SoldoutReqListSql::class);
    }

    protected function _setSearch($searchData){

        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'e.goodsNm' => '상품명'
            ,'a.goodsNo' => '상품코드'
            ,'a.reqName' => '신청자'
            ,'c.memNm' => '회원명'
            ,'c.memId' => '회원ID'
        ];
        // -- 기간
        $this->search['combineTreatDate'] = [
            'a.regDt' => __('요청일'),
            'a.sendDt' => __('알림 발송일'),
        ];
        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }
        // --- 정렬
        $this->search['sortList'] = [
            'a.regDt desc' => sprintf('%s↓', __('요청일')),
            'a.regDt asc' => sprintf('%s↑', __('요청일')),
            'a.sendDt desc' => sprintf('%s↓', __('발송일')),
            'a.sendDt asc' => sprintf('%s↑', __('발송일')),
        ];
        $this->search['sort'] = gd_isset( $searchData['sort'] ,'a.regDt desc' );

        // -- 페이징 기본 설정
        $this->search['page'] = gd_isset( $searchData['page'] ,1);
        $this->search['pageNum'] = gd_isset( $searchData['pageNum'] ,30);

        // 검색 항목 설정 끝 ----------------------------------------------------------

        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'searchPeriod'
            ,'sendType'
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
    public function getList($searchData, $type){
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

        $reqList = $this->sql->getList($this->search, $type);

        $getData['page'] = $reqList['pageData'];
        $getData['data'] = SlCommonUtil::setEachData($reqList['listData'], $this, 'setListData');
        //gd_debug( $getData['data'] );

        return $getData;
    }

    public function setListData($each, $key){
        //gd_debug($each);
        $optionList = DBUtil2::getList('sl_soldOutReqOptionList', 'reqSno', $each['reqSno']);
        //gd_debug($optionList);
        $refineOptionList = SlCommonUtil::setEachData($optionList, $this, setOptionList);
        //gd_debug($refineOptionList);
        $each['optionList'] = $optionList;
        $each['optionListStr'] = implode('<br>',$refineOptionList);
        return $each;
    }

    public function setOptionList($each, $key){
        $each = str_replace( '^|^' , '/' , $each['optionInfo']) . ' : ' . $each['reqCnt'];
        return $each;
    }

}

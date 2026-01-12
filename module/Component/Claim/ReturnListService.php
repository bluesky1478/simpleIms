<?php
namespace Component\Claim;

use App;
use Component\Erp\ErpCodeMap;
use Component\Member\Util\MemberUtil;
use Component\Work\WorkCodeMap;
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
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;

/**
 *  반품 리스트 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ReturnListService {
    use SlCommonTrait;

    const LIST_TITLES_ADMIN = [
        '번호'
        , '처리상태'
        , '상품상태'
        , '요청업체/고객명'
        , '원송장번호'
        , '주소'
        , '제품정보'
        , '창고메모'
        , '요청일'
        , '회수일'
    ];
    const LIST_TITLES = [
        '번호'
        , '처리상태'
        , '상품상태'
        , '요청업체'
        , '원송장번호'
        , '고객명'
        , '주소'
        , '제품정보'
        , '이노버메모'
        , '창고메모'
        , '요청일'
        , '회수일'
    ];
    const LIST_TITLES_FRONT = [
        '반품번호'
        , '처리상태'
        , '상품상태'
        , '요청업체'
        , '원송장번호'
        , '고객명'
        , '주소'
        , '제품정보'
        , '요청일'
        , '회수일'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Claim\Sql\ReturnListSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'a.customerName' => '고객명'
            ,'a.invoiceNo' => '원송장번호'
            ,'a.address' => '주소'
        ];

        // -- 기간
        $this->search['combineTreatDate'] = [
            'a.regDt ' => __('등록일'),
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
        $this->search['pageNum'] = gd_isset( $searchData['pageNum'] ,100);
        // 검색 항목 설정 끝 ----------------------------------------------------------

        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'scmNoNm'
            ,'returnStatus' //처리상태
        ],$searchData);

        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl'
            ,'returnStatus'
        ],$searchData,'all');

        /*$this->setRadioSearch([
            'returnStatus'
        ],$searchData,'');*/

        // 체크박스 검색 설정
        $this->setCheckSearch([
            'returnStatus'
        ]);

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-365 day')));
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
        }
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //공급사 선택했으나 없는 경우
        if ($searchData['scmNo'] == 0 && $searchData['scmFl'] == 1) {
            $this->search['scmFl'] = 'all';
        }

    }

    /**
     * 클레임 리스트 (고객 / 어드민 같이)
     * @param $searchData
     * @return mixed
     */
    public function getReturnList($searchData){
        $getData = $this->getTraitList($searchData,'getReturnList'); //SQL List 정의 되어 있어야함
        foreach( $getData['data'] as $key => $val ){
            $val['warehouseReturnMap'] = ErpCodeMap::WAREHOUSE_RETURN;
            $val['warehouseReturnPrdMap'] = ErpCodeMap::WAREHOUSE_RETURN_PRD;
            $val['returnGoods'] = json_decode( gd_htmlspecialchars_stripslashes($val['prdInfo']),true);
            $val['returnStatusKr'] = ErpCodeMap::WAREHOUSE_RETURN[$val['returnStatus']];
            $val['prdStatusKr'] =ErpCodeMap::WAREHOUSE_RETURN_PRD[$val['prdStatus']];
            $getData['data'][$key] = $val;
        }
        return $getData;
    }

}

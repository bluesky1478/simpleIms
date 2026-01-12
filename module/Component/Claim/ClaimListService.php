<?php
namespace Component\Claim;

use App;
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
 *  클레임 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ClaimListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        , '등록일/요청일'
        , '주문자정보'
        , '게시판번호/주문번호/주문상태/신청상품'
        , '교환상품'
        , '처리상태'
        , '메모/환불정보'
        , '저장'
    ];

    const EXCEL_LIST_TITLES = [
        '번호'
        , '등록일'
        , '신청유형'
        , '고객사명'
        , '주문자ID'
        , '주문자명'
        , '주문자닉네임'
        , '주문자연락처'
        , '주문번호'
        , '게시판번호'
        , '신청상품'
        , '교환상품'
        , '처리상태'
        , '환불정보'
        , '비고'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Claim\Sql\ClaimListSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'a.orderNo' => '주문번호'
            ,'c.memId' => '주문자ID'
            ,'c.memNm' => '주문자명'
            ,'c.nickNm' => '주문자닉네임'
            ,'a.bdSno' => '게시판번호'
        ];

        // -- 기간
        $this->search['combineTreatDate'] = [
            'a.regDt' => __('요청일'),
            'a.claimRegDt' => __('클레임 등록일'),
            'a.claimCompleteDt' => __('처리일'),
        ];

        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }

        // --- 정렬
        $this->search['sortList'] = [
            'a.claimRegDt desc' => sprintf('%s↓', __('클레임 등록일')),
            'a.claimRegDt asc' => sprintf('%s↑', __('클레임 등록일')),
            'a.regDt desc' => sprintf('%s↓', __('요청일')),
            'a.regDt asc' => sprintf('%s↑', __('요청일')),
            'a.claimCompleteDt desc' => sprintf('%s↓', __('처리일')),
            'a.claimCompleteDt asc' => sprintf('%s↑', __('처리일')),
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
            ,'claimType'
        ],$searchData);

        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl'
        ],$searchData,'all');
        $this->setRadioSearch([
            'claimStatus'
        ],$searchData,'');

        // 체크박스 검색 설정
        $this->setCheckSearch([
            'claimType'
        ]);

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-7 day')));
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
    public function getClaimList($searchData){
        $claimBoardService = SlLoader::cLoad('claim','claimBoardService');

        $getData = $this->getTraitList($searchData,'getClaimList'); //SQL List 정의 되어 있어야함
        foreach( $getData['data'] as $key => $val ){
            $val = $claimBoardService->getScmClaimData($val);
            if( 'cash' == $val['refundData']['refundType'] ){
                $val['refundData']['refundTypeKr'] = '환불요청 정보<br>[' . SlCodeMap::REFUND_TYPE[$val['refundData']['refundType']] . '] ' . $val['refundData']['bankName']. ' ' . $val['refundData']['deposit']. ' ' . $val['refundData']['depositor'];
            }else if( !empty($val['refundData']['refundType']) ){
                $val['refundData']['refundTypeKr'] = '환불요청 정보<br>[' . SlCodeMap::REFUND_TYPE[$val['refundData']['refundType']] . ']';
            }
            $val['orderStatusKr'] = SlCommonUtil::getOrderStatusName2($val['orderStatus']);
            if( !empty($val['cellPhone']) ){
                $val['masterCellPhone'] = $val['cellPhone'];
            }else if( !empty($val['orderCellPhone']) ){
                $val['masterCellPhone'] = $val['orderCellPhone'];
            }else if( !empty($val['receiverCellPhone']) ){
                $val['masterCellPhone'] = $val['receiverCellPhone'];
            }

            $val['claimStatusColor'] = SlCodeMap::NEW_CLAIM_STATUS_COLOR[$val['claimStatus']];

            $getData['data'][$key] = $val;
        }
        //gd_debug($getData['data']);
        return $getData;
    }

}

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
class ScmOrderListService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'주문번호' //a.orderNo
        ,'주문자'    //b.orderName
        ,'수령자'    //b.receiverName
        ,'주문상품' //- logic
        ,'결제금액' //a.settlePrice
        ,'주문상태' // a.orderStatus + logicCode
        ,'배송정보' //
        ,'주문일자' // a.regDt
    ];
    const LIST_TITLES_ASIANA = [
        '번호'
        ,'주문번호' //a.orderNo
        ,'주문자'    //b.orderName
        ,'수령자'    //b.receiverName
        ,'주문정보' //- logic
        ,'주문상태' // a.orderStatus + logicCode
        ,'배송정보' //
        ,'주문일자' // a.regDt
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmOrderListSql::class);
    }

    protected function _setSearch($searchData){

        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'b.orderName' => '주문자명'
            , 'e.nickNm' => '닉네임'
            , 'e.memId' => '회원ID'
            , 'a.orderNo' => '주문번호'
            , 'b.receiverAddress' => '주소'
            , 'b.receiverAddressSub' => '주소상세'
        ];
        // -- 기간
        $this->search['combineTreatDate'] = [
            'a.regDt' => __('주문일'),
            'c.acctDt' => __('승인일'),
            'i.deliveryDt' => __('배송일'),
        ];
        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }
        // --- 정렬
        $this->search['sortList'] = [
            'a.regDt desc' => sprintf('%s↓', __('주문일')),
            'a.regDt asc' => sprintf('%s↑', __('주문일')),
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
            ,'orderAcctStatus'
            ,'asianaStatus'
            ,'scmOrderDelivery'
            ,'orderStatus'
            ,'memberType'
        ],$searchData);
        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl',
            'orderStatus',
            'memberType',
        ],$searchData,'all');
        $this->setRadioSearch([
            'orderAcctStatus',
            'asianaStatus',
        ],$searchData,'');

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]. ' 00:00:00');
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
            $this->search['searchDate'][0] .= ' 00:00:00';
            $this->search['searchDate'][1] .= ' 23:59:59';
        } else {
            if( 7 != $searchData['scmNo'][0] ){
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-6 day')));
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
        //기간 Validation
/*        if ( 7 != $searchData['scmNo'][0] && DateTimeUtils::intervalDay($this->search['searchDate'][0], $this->search['searchDate'][1]) > 365) {
            throw new AlertBackException(__('1년이상 기간으로 검색하실 수 없습니다.'));
        }*/

    }

    /**
     * 주문 리스트
     * @param mixed  $searchData   검색 데이타
     *
     * @return array 주문 리스트 정보
     */
    public function getList($searchData){

        $isProvider = Manager::isProvider();

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

        $orderAdmin = \App::load('\\Component\\Order\\OrderAdmin');

        $orderStatusMap = SlCommonUtil::getOrderStatusAllMap();
        $acceptStatusMap = SlCodeMap::ORDER_ACCT_STATUS;
        $acceptStatusColorMap = SlCodeMap::ORDER_ACCT_STATUS_COLOR;
        $orderList = $this->sql->getList($this->search);
        foreach($orderList['listData'] as $key => $value){

            $goodsList = $this->sql->getGoodsList($value['orderNo'], $this->search);


            $decoratedGoodsList = $this->decorationGoodsListData($goodsList);

            $value['asianaOrderMap'] = $decoratedGoodsList['asianaOrderMap'];
            $value['goodsHtml'] = implode('<br>',$decoratedGoodsList['goodsHtml']);
            $value['goodsInfo'] = $decoratedGoodsList['goodsInfo']; //상품 생정보
            $value['orderStatusStr'] = $orderStatusMap[$value['orderStatus']];
            $value['orderAcctStatusStr'] = $acceptStatusMap[$value['orderAcctStatus']];
            $value['orderAcctStatusColor'] = $acceptStatusColorMap[$value['orderAcctStatus']];

            //원복 가능 여부
            //출고 불가 상태이며 + 공급사가 아님
            if( '3' === $value['orderAcctStatus'] && !empty($isProvider) ){
                $value['isRevoke'] = false;
            }else{
                $value['isRevoke'] = true;
            }

            //관리자 요청 메세지
            $addInfo = json_decode(gd_htmlspecialchars_stripslashes($value['addField']), true);
            if(!empty($addInfo)){
                $value['requestToAdmin'] = $addInfo[1]['data'];
            }

            $value['settleKindStr'] = $orderAdmin->getSettleKind($value['settleKind']);

            //$value
            $orderList['listData'][$key] = $value;
        }

        $getData['page'] = $orderList['pageData'];
        $getData['data'] = $orderList['listData'];

        return $getData;
    }

    public static function decorationGoodsListData($goodsDataList){
        $deliveryCompanyMap = SlCommonUtil::getDeliveryCompanyMap();
        $data = [];
        $asianaOrderMap = [];
        foreach($goodsDataList as  $goodsData){
            //gd_debug($data['invoiceCompanyName']);
            $goodsHtml = $goodsData['goodsNm'];
            $optionValue = array();
            $optionInfo = json_decode($goodsData['optionInfo'],true);
            foreach($optionInfo as $option){
                $optionValue[] = $option[1];
            }
            $goodsHtml .= ' <em>'.implode('/',$optionValue).'</em> ';
            $goodsHtml .= ' x '. $goodsData['goodsCnt']. '개';

            $goodsData['invoiceCompanyName'] = $deliveryCompanyMap[$goodsData['invoiceCompanySno']];

            $data['goodsHtml'][] = $goodsHtml;
            $data['goodsInfo'][] = $goodsData;

            $key = $goodsData['companyId'].$goodsData['name'];
            $key2 = $goodsData['goodsNm'].' '.$goodsData['optionValue1'];

            $provideData = array_reverse(json_decode($goodsData['provideInfo'], true));

            $asianaOrderMap[$key]['info'] = $goodsData;
            $asianaOrderMap[$key]['provideInfo'] = json_encode($provideData);

            $provideYear = [];
            foreach($provideData as $provide){
                $year=substr($provide['requestDt'],2,2);
                $provideYear[$year][$provide['prdName']] += $provide['orderCnt'];
            }
            //gd_debug($provideYear);
            $asianaOrderMap[$key]['provideYear'] = $provideYear;

            $asianaOrderMap[$key]['orderCnt'][$key2] += $goodsData['goodsCnt'];
        }
        //gd_debug($asianaOrderMap);

        $empTeams = [];
        $empPart2s = [];

        foreach ($asianaOrderMap as $key => $value) {
            $empTeams[] = $value['info']['empTeam'];
            $empPart2s[] = $value['info']['empPart2'];
        }

        // empTeam과 empPart2에 대한 정렬
        array_multisort($empTeams, SORT_ASC, $empPart2s, SORT_ASC, $asianaOrderMap);

        $data['asianaOrderMap'] = $asianaOrderMap;

        return $data;
    }

}

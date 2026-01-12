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
class ScmOrderCalcService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '번호'
        ,'주문번호' //a.orderNo
        ,'주문자'    //b.orderName
        ,'주문상품' //- logic
        ,'결제금액' //a.settlePrice
        ,'주문상태' // a.orderStatus + logicCode
        ,'배송정보' //
        ,'주문일자' // a.regDt
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Scm\Sql\ScmOrderCalcSql::class);
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
        $this->search['pageNum'] = gd_isset( $searchData['pageNum'] ,30);

        // 검색 항목 설정 끝 ----------------------------------------------------------


        // 검색 설정 시작 ----------------------------------------------------------
        // 기본 검색 설정
        $this->setSearchData([
            'key'
            ,'keyword'
            ,'scmNo'
            ,'searchPeriod'
            ,'orderAcctStatus'
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
            'orderAcctStatus'
        ],$searchData,'');

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'a.regDt');

        if( !empty($searchData['searchDate']) && !empty($searchData['searchDate'][1]) ){
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
            $this->search['searchDate'][0] .= ' 00:00:00';
            $this->search['searchDate'][1] .= ' 23:59:59';
        }else{
            if( 7 != $searchData['scmNo'] ){
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-364 day'))); //이노버가 아니라면
            }else{
                $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-20 year'))); //이노버라면
            }
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1], date('Y-m-d'));
            $this->search['searchDate'][0] .= ' 00:00:00';
            $this->search['searchDate'][1] .= ' 23:59:59';
        }
        // 검색 설정 끝 ----------------------------------------------------------

        //기타 처리 ----------------------------------------------------------
        //gd_debug( $this->search );
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

            $goodsList = $this->sql->getGoodsList($value['orderNo']);

            $decoratedGoodsList = $this->decorationGoodsListData($goodsList);

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
        $data = array();
        foreach( $goodsDataList as  $goodsData ){
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
        }
        return $data;
    }

}

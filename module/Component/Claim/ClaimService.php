<?php
namespace Component\Claim;

use App;
use Component\Member\Util\MemberUtil;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;

/**
 *  클레임 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ClaimService {
    use SlCommonTrait;

    const LIST_TITLES = [
        '요청번호'
        , '구분'
        , '고객사'
        , '주문번호'
        , '회원명'
        , '요청일자'
        , '상품번호'
        , '상품'
        , '요청수량'
        , '요청내용'
        , '요청분류(관리자선택/집계용)'
        , '처리상태'
        , '처리내용'
        , '처리일자'
        , '기타/개선요청사항'
        , '관리자메모'
    ];

    private $sql;
    private $search;

    public function __construct(){
        $this->sql = \App::load(\Component\Claim\Sql\ClaimSql::class);
    }

    protected function _setSearch($searchData){
        // 검색 항목 설정 시작 ----------------------------------------------------------
        $this->search['combineSearch'] = [
            'd.memNm' => '회원명'
            ,'d.memId' => '회원ID'
            ,'a.orderNo' => '주문번호'
            ,'a.claimSno' => '요청번호'
        ];

        // -- 기간
        $this->search['combineTreatDate'] = [
            'b.regDt' => __('요청일'),
            'b.procDt' => __('처리일'),
        ];

        // --- $searchData trim 처리
        if (isset($searchData)) {
            gd_trim($searchData);
        }

        // --- 정렬
        $this->search['sortList'] = [
            'b.regDt desc' => sprintf('%s↓', __('요청일')),
            'b.regDt asc' => sprintf('%s↑', __('요청일')),
            'b.procDt desc' => sprintf('%s↓', __('처리일')),
            'b.procDt asc' => sprintf('%s↑', __('처리일')),
        ];

        $this->search['sort'] = gd_isset( $searchData['sort'] ,'b.regDt desc' );

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
            ,'claimType'
        ],$searchData);
        //TODO : 요청분류
        //TODO : 처리상태 procStatus

        // 라디오 검색 설정
        $this->setRadioSearch([
            'scmFl'
        ],$searchData,'all');

        // 체크박스 검색 설정
        $this->setCheckSearch([
            'claimType'
        ]);

        // 기간 설정
        $this->search['searchDateFl'] = gd_isset($searchData['searchDateFl'], 'b.regDt');
        if ($this->search['searchPeriod'] < 0) {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0]);
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][1]);
        } else {
            $this->search['searchDate'][] = gd_isset($searchData['searchDate'][0], date('Y-m-d', strtotime('-7 day')));
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
     * 클레임 리스트 (고객 / 어드민 같이)
     * @param $searchData
     * @return mixed
     */
    public function getClaimList($searchData){
        $getData = $this->getTraitList($searchData,'getClaimList'); //SQL List 정의 되어 있어야함

        //리스트 가공
        $reqGoodsCnt = [];
        $claimTypeMap = SlCodeMap::CLAIM_TYPE;
        $claimStatusMap = SlCodeMap::CLAIM_STATUS;

        $valSno = 0;
        $valIdx = 0;
        foreach( $getData['data'] as $key => $val ){

            $reqGoodsCnt[$val['sno']]++;

            if( $valSno != $val['sno'] ){
                $valSno = $val['sno'];
                $valIdx = 0;
            }
            $val['idx'] = $valIdx++;
            $val['claimTypeStr'] = $claimTypeMap[$val['claimType']];
            $val['procStatusStr'] = $claimStatusMap[$val['procStatus']];
            //옵션처리 시작 ---
            $optionInfo = [];
            if (empty($val['optionInfo']) === false) {
                $option = json_decode(gd_htmlspecialchars_stripslashes($val['optionInfo']), true);
                if (empty($option) === false) {
                    foreach ($option as $oKey => $oVal) {
                        $optionInfo[$oKey]['optionName'] = $oVal[0];
                        $optionInfo[$oKey]['optionValue'] = $oVal[1];
                        $optionInfo[$oKey]['optionCode'] = $oVal[2];
                        $optionInfo[$oKey]['optionRealPrice'] = $oVal[3];
                        $optionInfo[$oKey]['deliveryInfoStr'] = $oVal[4];
                    }
                    unset($option);
                }
            }
            $val['optionInfo'] = $optionInfo;
            //옵션처리 끝 ---
            $getData['data'][$key] = $val;
            //++$getData['data'][$key]['goodsCnt'][$val['sno']];
        }
        $getData['reqGoodsCnt'] = $reqGoodsCnt;
        //gd_debug($getData['reqOrderCnt']);
        //gd_debug($getData['data']);
        return $getData;
    }

    /**
     * 리포트용 클레임 리스트
     * @param $searchParam
     * @return mixed
     */
    public function getSimpleClaimList($searchParam){
        $claimList = $this->getClaimList($searchParam)['data'];
        $simpleList = array();
        $simpleGoodsInfo = array();

        foreach( $claimList as $claimData ){
            $simpleList[ $claimData['sno'] ] = $claimData; //겹쳐짐-나중것이 최근으로
            $optionValue = array();
            foreach($claimData['optionInfo'] as $option){
                $optionValue[] = $option['optionValue'];
            }
            $goodsNm = $claimData['goodsNm'] .'('.implode('/',$optionValue).') x ' . $claimData['reqGoodsCnt']. '개'  ;
            $simpleGoodsInfo[ $claimData['sno'] ][] = $goodsNm;
        }

        foreach( $simpleList as $simpleKey => $simpleData ){
            $simpleList[$simpleKey]['simpleGoodsInfo'] = implode('<br>' ,$simpleGoodsInfo[$simpleKey]);
        }

        return $simpleList;
    }
    
    /**
     * 클레임 데이터 가져오기
     * @param $inputData
     * @return mixed
     */
    public function getClaimContents($inputData){
        $data = DBUtil::getOne('sl_claimHistory','sno',$inputData['sno']);
        //gd_debug($data);
        $searchData['key'] = 'claimSno';
        $searchData['keyword'] = $inputData['sno'];
        $searchData['searchDate'][0] = '2000-01-01 00:00:00';
        $searchData['searchDate'][1] = '3000-01-01 23:59:59';

        $goodsDataList = $this->getClaimList($searchData)['data'];
        //gd_debug($goodsDataList);
        $orderGoodsSnoListArray = array();
        $reqCntArray = array();
        foreach( $goodsDataList as  $goodsData ){
            $goodsTargetUrl = \Request::getScheme()."://".\Request::getHost() . '/goods/goods_register.php?goodsNo='.$goodsData['goodsNo'];
            $reqGoodsData['targetUrl'] = $goodsTargetUrl;
            $reqGoodsData['goodsNo'] = $goodsData['goodsNo'];
            $reqGoodsData['goodsNm'] = $goodsData['goodsNm'];
            $goodsHtml = '(<a href="'.$goodsTargetUrl.'" target="_blank">'.$goodsData['goodsNo'].'</a>'.')'.$goodsData['goodsNm'];
            $optionValue = array();
            foreach($goodsData['optionInfo'] as $option){
                $optionValue[] = $option['optionValue'];
            }
            $reqGoodsData['goodsOption'] = $optionValue;
            $goodsHtml .= '<em>'.implode('/',$optionValue).'</em>';
            $goodsHtml .= ' x '. $goodsData['reqGoodsCnt']. '개';
            $data['goods'][] = $reqGoodsData;
            $data['goodsHtml'][] = $goodsHtml;
            $orderGoodsSnoListArray[] = $goodsData['reqGoodsSno'];
            $reqCntArray[] = $goodsData['reqGoodsCnt'];
        }

        $data['orderGoodsSnoList'] = implode(',',$orderGoodsSnoListArray);
        $data['reqCnt'] = implode(',',$reqCntArray);

        return $data;
    }

    /**
     * 요청 분류 가져오기
     * @param $inputData
     * @return array
     */
    public function getReqTypeMap($inputData){
        $reqTypeMap = array();
        $reqTypeList = DBUtil::getList('sl_claimRequestType','claimType', $inputData['claimType']);
        foreach($reqTypeList as $key => $value){
            $reqTypeMap[$value['sno']] = $value['reqTypeContents'];
        }
        return $reqTypeMap;
    }

    /**
     * 사용자 요청 저장
     */
    public function saveRequest($inputData){
        $memNo = Session::get('member.memNo');
        $scmNo = MemberUtil::getMemberScmNo();

        $saveData['orderNo'] = $inputData['orderNo'];
        $saveData['memNo'] = $memNo;
        $saveData['scmNo'] = $scmNo;
        $saveData['claimType'] = $inputData['claimType'];
        $saveData['reqContents'] = $inputData['reqContents'];
        $saveData['memberMemo'] = $inputData['memberMemo'];
        $claimSno = DBUtil::insert('sl_claimHistory',$saveData);

        foreach($inputData['orderGoodsNo'] as $key => $orderGoodsSno){
            $saveData['claimSno'] = $claimSno;
            $saveData['reqGoodsSno'] = $orderGoodsSno;
            $saveData['reqGoodsCnt'] = $inputData['claimGoodsCnt'][$orderGoodsSno];
            $saveData['reqGoodsIdx'] = $key;
            //요청 상품 저장
            DBUtil::insert('sl_claimHistoryGoods',$saveData);
        }
    }

    /**
     * 요청 분류 저장하기
     * @param $inputData
     * @return mixed
     */
    public function saveClaimRequestType($inputData){
        $return['sno'] = DBUtil::insert('sl_claimRequestType',$inputData);
        $return['contents'] = $inputData['reqTypeContents'];
        return $return;
    }

    /**
     * 클레임 정보 저장하기
     * @param $saveData
     * @return mixed
     * @throws Exception
     */
    public function saveClaimProcData($saveData){
        $updateData = DBUtil::makeUpdateData($saveData,'reqType,procStatus,reqType,procContents,procDt,adminMemo');
        return DBUtil::update('sl_claimHistory',$updateData,new SearchVo('sno=?',$saveData['sno']));
    }

    /**
     * 교환처리 배송비 설정
     * @param $params
     * @throws Exception
     */
    public function setExchangeDelivery($params){
        $saveData['goodsDeliveryCollectFl'] = $params['deliveryValue'];
        DBUtil2::update(DB_ORDER_GOODS,$saveData,new SearchVo('sno=?', $params['sno']));
    }

}

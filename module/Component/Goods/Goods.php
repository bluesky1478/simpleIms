<?php
namespace Component\Goods;

use App;
use Component\Database\DBTableField;
use Component\Member\Util\MemberUtil;
use LogHandler;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\OekCodeMap;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use Component\PlusShop\PlusReview\PlusReviewConfig;
use Component\ExchangeRate\ExchangeRate;
use Component\Member\Group\Util;
use Component\Validator\Validator;
use Cookie;
use Exception;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Globals;
use Session;
use SlComponent\Util\SlLoader;
use UserFilePath;
use Framework\Utility\DateTimeUtils;


class Goods extends \Bundle\Component\Goods\Goods{
    use SlCommonTrait;

    /**
     * 개별 소스
     * 상품 이미지 데이터 가져오기
     * @param $goodsNo
     * @param null $imageKind
     * @return mixed
     */
    public function getGoodsImageData($goodsNo, $imageKind = null){
        $table['a'] = new TableVo(DB_GOODS,null,'a');
        $table['a']->setField('
            a.imagePath
            , a.imageStorage
        ');

        $table['b'] = new TableVo(DB_GOODS_IMAGE,null,'b');
        $table['b']->setField('
            b.imageName
        ');
        //JoinType
        $table['b']->setJoinType('JOIN');
        //Join Condition
        $table['b']->setJoinCondition('a.goodsNo = b.goodsNo');

        //Search
        $searchVo = new SearchVo([ 'a.goodsNo=?' ] ,  [$goodsNo] );
        if( !empty($imageKind)  ) {
            $searchVo->setWhere('b.imageKind = ? ' );
            $searchVo->setWhereValue($imageKind);
        }

        return DBUtil::getComplexList($table ,$searchVo);
    }

    /**
     * 개별 소스
     * 상품번호로 옵션 가져오기
     * @param $goodsNo
     * @return array
     */
    public function getGoodsOptionByGoodsNo($goodsNo){
        $resultData = array();
        $goodsOptionInfo = DBUtil::getList(DB_GOODS_OPTION, 'goodsNo', $goodsNo, 'optionNo');;
        $goodsOptionNameArray = array();
        foreach( $goodsOptionInfo as $goodsOptionKey => $goodsOptionValue ){
            $goodsUnitOption = array();
            for($i=1; $i<=5; $i++){
                $optionValue = $goodsOptionValue['optionValue'.$i];
                if(!empty($optionValue)){
                    $goodsUnitOption[] = $goodsOptionValue['optionValue'.$i];
                }
            }
            $goodsOptionNameArray[] = implode('/',$goodsUnitOption);
        }
        $resultData['goodsOptionInfo'] = $goodsOptionInfo;
        $resultData['goodsOptionNameStrList'] =  implode('^|^', $goodsOptionNameArray);
        $resultData['goodsOptionNameHtmlList'] =  '<option>' . implode('</option><option>', $goodsOptionNameArray) . '</option>';
        return $resultData;
    }


    /**
     * 상품리스트에 안전재고 정보 삽입
     * @param $goodsNo
     * @return mixed
     */
    public function getGoodsOptionWithSafeCnt($goodsNo){
        $goodsOptionInfo = $this->getGoodsOption($goodsNo);
        unset($goodsOptionInfo['optVal']);
        foreach($goodsOptionInfo as $k => $v){
            if($goodsOptionInfo[$k]['optionSellFl'] == 't'){
                $goodsOptionInfo[$k]['optionSellFl'] = $goodsOptionInfo[$k]['optionSellCode'];
            }
            $safeData = $this->getOptionSafeData($goodsNo, $v['optionNo']);
            //gd_debug( $safeData );

            $safeStockCnt = $safeData['safeCnt'];
            $shareNotCnt = $safeData['shareNotCnt'];

            $goodsOptionInfo[$k]['safeStockCnt']= empty($safeStockCnt)?0:$safeStockCnt;
            $goodsOptionInfo[$k]['shareNotCnt']= empty($shareNotCnt)?0:$shareNotCnt;
        }
        return $goodsOptionInfo;
    }

    /**
     * 안전재고 가져오기
     * @param $goodsNo
     * @param $sno
     * @return mixed
     */
    public function getOptionSafeCnt($goodsNo, $sno){
        return DBUtil::getOne('sl_goodsSafeStock',['goodsNo','optionNo'],[$goodsNo,$sno])['safeCnt'];
    }
    public function getOptionSafeData($goodsNo, $sno){
        return DBUtil::getOne('sl_goodsSafeStock',['goodsNo','optionNo'],[$goodsNo,$sno]);
    }

    /**
     * 안전재고 등록
     * @param $postValue
     * @return boolean
     * @throws \Exception
     */
    public function saveSafeCnt($postValue){
        //SitelabLogger::logger($postValue);
        $goodsNo = $postValue['goodsNo'];

        $optionNo = 1;
        foreach($postValue['safeStockCnt'] as $key => $value){
            if( !empty($this->getOptionSafeData($goodsNo, $optionNo))){
                $updateData['safeCnt'] = $value;
                $updateData['shareNotCnt'] = $postValue['shareNotCnt'][$key];
                DBUtil::update('sl_goodsSafeStock',$updateData,new SearchVo(['goodsNo=?','optionNo=?'],[$goodsNo,$optionNo]));
            }else{
                $saveData['goodsNo'] = $goodsNo;
                $saveData['optionNo'] = $optionNo;
                $saveData['safeCnt'] = $value;
                $saveData['shareNotCnt'] = $postValue['shareNotCnt'][$key];
                DBUtil::insert('sl_goodsSafeStock',$saveData);
            }
            $optionNo++;
        }
        return true;
    }

    public function setOpenGoods($getData, $goodsNo){
        $orderService=SlLoader::cLoad('order','orderService','');
        $goodsData = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
        if( '1' == $goodsData['isOpenFl'] && !empty($goodsData['openGoodsNo']) ){
            $shareCntDataOrg = $orderService->getShareStockCnt($goodsData['openGoodsNo']);
            $shareCntData = SlCommonUtil::arrayAppKey($shareCntDataOrg, 'optionNo');
            foreach($getData as $dataKey => $each){
                $optionNo = $each['optionNo'];
                $shareCnt = ( $shareCntData[$optionNo]['shareTotalCnt'] > 0 ) ? $shareCntData[$optionNo]['shareTotalCnt'] : 0;;
                $each['stockCnt'] += $shareCnt;
                $each['shareAvailCnt'] = $shareCnt;
                $getData[$dataKey] = $each;
            }
        }
        return $getData;
    }

    /**
     * 현재 구매수량
     * @param $goodsNo
     * @param $memNo
     * @return mixed
     */
    public function getCurrentBuyGoodsCnt($goodsNo, $memNo) {
        $excludeBuyLimitGoods = [1000000328,1000000330];
        if( in_array($goodsNo, $excludeBuyLimitGoods) ){
            $sql = "select ifnull(sum(goodsCnt), 0) as buyGoodsCnt from es_order a join es_orderGoods b  on a.orderNo = b.orderNo where a.memNo = {$memNo} and left(b.orderStatus,1) in ( 'g','p','d','s','o') and b.goodsNo = '{$goodsNo}' and a.regDt >= '2023-08-18 00:00:00'   ";
        }else{
            $sql = "select ifnull(sum(goodsCnt), 0) as buyGoodsCnt from es_order a join es_orderGoods b  on a.orderNo = b.orderNo where a.memNo = {$memNo} and left(b.orderStatus,1) in ( 'g','p','d','s', 'o' ) and b.goodsNo = '{$goodsNo}'";
        }

        return DBUtil2::runSelect($sql)[0]['buyGoodsCnt'];
    }

    /**
     * 자체 코드
     * 현재 장바구니 수량
     * @param $goodsNo
     * @param $memNo
     * @return mixed
     */
    public function getCurrentCartGoodsCnt($goodsNo, $memNo) {
        $sql = "select sum(goodsCnt) cartGoodsCnt from es_cart where memNo={$memNo} and goodsNo={$goodsNo}";
        return DBUtil2::runSelect($sql)[0]['cartGoodsCnt'];
    }


    /**
     * 자체 코드 
     * 구매 가능 수량
     * @param $memberConfig
     * @param $goodsNo
     * @return int
     */
    public function getBuyLimitMaxCnt(&$memberConfig, $goodsNo){
        $maxCnt = $memberConfig['buyLimitCount'];    //구매수량 제한

        //OEK DOUBLE
        if( in_array($goodsNo, SlCodeMap::BUY_LIMIT_DOUBLE_GOODS )){
            $maxCnt *= 2;
            $memberConfig['buyLimitCount'] *= 2;
        }

        //OEK ZERO
        if( in_array($goodsNo, SlCodeMap::BUY_LIMIT_ZERO_GOODS )){
            $maxCnt = 0;
            $memberConfig['buyLimitCount'] = 0;
        }

        //OEK 춘추 풀기... 하드코딩.
        $exceptionGoods = OekCodeMap::LIMIT_EXCEPTION_GOODS;
        $exceptionMember = OekCodeMap::LIMIT_EXCEPTION_MEMBER;

        $exceptionMemberCnt = $exceptionMember[\Session::get('member.memNo')];
        if( in_array($goodsNo, $exceptionGoods) && !empty($exceptionMemberCnt) ){
            $maxCnt = $exceptionMemberCnt;
            $memberConfig['buyLimitCount'] = $exceptionMemberCnt;
        }

        //조끼만....
        $exceptionGoods2 = OekCodeMap::LIMIT_EXCEPTION_GOODS2;
        $exceptionMember2 = OekCodeMap::LIMIT_EXCEPTION_MEMBER2;
        $exceptionMemberCnt2 = $exceptionMember2[\Session::get('member.memNo')];
        if( in_array($goodsNo, $exceptionGoods2) && !empty($exceptionMemberCnt2) ){
            $maxCnt = $exceptionMemberCnt2;
            $memberConfig['buyLimitCount'] = $exceptionMemberCnt2;
        }

        //제외 할.
        $exceptGoods = OekCodeMap::LIMIT_EXCEPT_GOODS;
        if( in_array($goodsNo, $exceptGoods)){
            $maxCnt = 999;
            unset($memberConfig['buyLimitCount']);
        }

        //모두 무시하고 특정 상품 회원 수량
        foreach( OekCodeMap::LIMIT_EXCEPTION_GOODS3 as $targetGoodsNo => $exceptData ){
            if( $goodsNo == $targetGoodsNo ){
                foreach( $exceptData as $targetMemNo => $exceptCnt ){
                    if( $targetMemNo == \Session::get('member.memNo')){
                        $maxCnt = $exceptCnt;
                        $memberConfig['buyLimitCount'] = $exceptCnt;
                        break;
                    }
                }
            }
        }

        return $maxCnt;
    }


    public function getGoodsView($goodsNo){

        $parentResult = parent::getGoodsView($goodsNo);
        $memNo = \Session::get('member.memNo');
        $memberConfig = DBUtil2::getOne( 'sl_setMemberConfig', 'memNo', $memNo );

        //특정상품은 구매제한 체크하지 않음.
        //구매 가능 수량에 따른 체크
        if( !empty($memberConfig)  ){
            if( !empty($memberConfig['buyLimitCount']) ){

                $maxCnt = $this->getBuyLimitMaxCnt($memberConfig, $goodsNo);

                if( $maxCnt != 999 ){
                    //구매제한 셋트 상품이라면 (모두 체크)
                    $orderGoodsCnt=0;
                    $cartGoodsCnt=0;
                    $setGoodsKey = SlCodeMap::SET_GOODS_MAP[$goodsNo];

                    if( !empty($setGoodsKey) ){
                        foreach( SlCodeMap::SET_GOODS_LIST[$setGoodsKey] as $setGoodsNo ){
                            $orderGoodsCnt += $this->getCurrentBuyGoodsCnt($setGoodsNo, $memNo); //이미 구매한 부분 제외
                            $cartGoodsCnt += $this->getCurrentCartGoodsCnt($setGoodsNo, $memNo); //이미 구매한 부분 제외
                        }
                    }else{
                        //현재 구매한거 빼기
                        $orderGoodsCnt = $this->getCurrentBuyGoodsCnt($goodsNo, $memNo); //이미 구매한 부분 제외
                        $cartGoodsCnt = $this->getCurrentCartGoodsCnt($goodsNo, $memNo);//이미 구매한 부분 제외
                    }

                    $parentResult['orderCnt'] = $orderGoodsCnt; //이미 구매한 부분 제외
                    $parentResult['cartCnt'] = $cartGoodsCnt;
                    $maxCnt -= ($orderGoodsCnt+$cartGoodsCnt);

                    if( 0 >= $maxCnt ){
                        $parentResult['orderPossible'] = 'n';
                    }else{
                        $parentResult['maxOrderCnt'] = $maxCnt;    //구매수량 제한
                    }

                    $parentResult['availOrderCnt'] = $maxCnt;
                }

            }
        }

        //if( !in_array($goodsNo, $excludeBuyLimitGoods) ){
            $parentResult['memberConfig'] = $memberConfig;
        //}

        //매장유형에 따른 체크
        $hankookTypeUser = DBUtil2::getOne(DB_MEMBER, 'memNo', $memNo)['hankookType'];
        /*gd_debug($hankookTypeUser);*/
        if( !empty( $hankookTypeUser ) ){
            $hankookTypeGoods = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo)['hankookType'];
            /*gd_debug(  $hankookTypeUser  );
            gd_debug(  $hankookTypeGoods  );
            gd_debug(  $hankookTypeUser & $hankookTypeGoods  );*/

            if( (int)$hankookTypeUser & (int)$hankookTypeGoods ){
            }else{
                $parentResult['orderPossible'] = 'n';
            }
        }

        //gd_debug( $parentResult );
        //아래 상품 코드는 안내 메세지 출력
        $specialGoods = [
            '1000000304',
            '1000000303',
            '1000000302',
            '1000000301',
        ];
        if( in_array($goodsNo, $specialGoods) ){
            $parentResult['isConfirmGoods'] = 'y';
        }

        return $parentResult;
    }

}

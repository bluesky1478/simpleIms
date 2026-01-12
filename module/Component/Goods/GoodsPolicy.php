<?php
namespace Component\Goods;

use App;
use LogHandler;
use Request;
use Globals;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use FileHandler;
use UserFilePath;
use Component\Storage\Storage;

class GoodsPolicy{

    use SlCommonTrait;

    private $sql;

    public function __construct(){
        $this->sql = \App::load(\Component\Goods\Sql\GoodsPolicySql::class);
    }

    /**
     * 설문 정책
     * @return mixed
     */
    public function getSurveyPolicy(){
        return DBUtil::getList('sl_policySurvey','1','1');
    }

    /**
     * 무상 정책 가져오기
     * @return mixed
     */
    public function getFreePolicy(){
        return DBUtil::getList('sl_policyFreeSale','1','1');
    }

    /**
     * 할인 정책 가져오기
     * @return mixed
     */
    public function getSalePolicy(){
        return DBUtil::getList('sl_policySale','1','1');
    }

    /**
     * 무상 정책 저장
     * @param $inputSaveData
     * @return mixed
     */
    public function addFreePolicy($inputSaveData){
        return DBUtil::insert('sl_policyFreeSale',$inputSaveData);
    }

    /**
     * 할인 정책 저장
     * @param $inputSaveData
     * @return mixed
     */
    public function addSalePolicy($inputSaveData){
        return DBUtil::insert('sl_policySale',$inputSaveData);
    }

    /**
     * 설문 정책 저장
     * @param $inputSaveData
     * @return mixed
     */
    public function addSurveyPolicy($inputSaveData){
        return DBUtil::insert('sl_policySurvey',$inputSaveData);
    }

    /**
     * 정책 업데이트
     * @param $inputUpdateData
     * @throws \Exception
     */
    public function updatePolicy($inputUpdateData){
        $updateTable = 'sl_policy'.ucfirst($inputUpdateData['updatePolicyType']);
        $updateData[$inputUpdateData['updateKey']] = $inputUpdateData['updateValue'];
        $searchVo = new SearchVo('sno=?',$inputUpdateData['sno']);
        /*SitelabLogger::logger('updatepolicy 확인');
        SitelabLogger::logger($searchVo);
        SitelabLogger::logger($updateTable);
        SitelabLogger::logger($updateData);*/
        DBUtil::update($updateTable,$updateData,$searchVo);

        //Update Key가 회사 비율 이면 구매자 비율도 자동 변경
        if('companyRatio' === $inputUpdateData['updateKey']){
            $updateData['buyerRatio'] = 100 - (int)$inputUpdateData['updateValue'] ;
            DBUtil::update($updateTable,$updateData,new SearchVo('sno=?',$inputUpdateData['sno']));
        }
    }

    /**
     * 정책 연결
     * @param $inputLinkData
     * @throws \Exception
     */
    public function linkPolicy($inputLinkData){
        foreach($inputLinkData['goodsNo'] as $key => $goodsNo){
            $mergeData['goodsNo'] = $goodsNo;
            $mergeData[$inputLinkData['policyKey']] = $inputLinkData['policySno'];
            DBUtil::merge('sl_goodsPolicy', $mergeData,new SearchVo('goodsNo=?',$goodsNo) );
        }
    }

    /**
     * 상품에 연결된 정책 삭제
     * @param $inputUpdateData
     * @throws \Exception
     */
    public function linkDeletePolicy($inputUpdateData){
        $updateTable = 'sl_goodsPolicy';
        $updateData[$inputUpdateData['updateKey']] = $inputUpdateData['updateValue'];
        $searchVo = new SearchVo('goodsNo=?',$inputUpdateData['goodsNo']);

        //SitelabLogger::logger($inputUpdateData);
        //SitelabLogger::logger($searchVo);

        DBUtil::update($updateTable,$updateData,$searchVo);
    }

    /**
     * 상품정보에 정책/회원 정보 붙여 반환
     * @param $goodsData
     * @return mixed
     */
    public function getAdditionalInfoGoodsData($goodsData){
        //대상 goodsNoList 가져오기
        $goodsNoList = array();
        foreach($goodsData as $key => $goodsEachData){
            $goodsNoList[] = $goodsEachData['goodsNo'];
        }

        //상품 정책 정보 가져오기
        $policyInfo = $this->sql->getGoodsPolicyInfo($goodsNoList);
        $refinePolicyInfo = array();
        foreach($policyInfo as $key => $value){
            $refinePolicyInfo[$value['goodsNo']] = $value;
        }

        //상품 회원수 정보 가져오기
        $policyMemberCount = $this->sql->getGoodsPolicyMemberCount($goodsNoList);
        $refinePolicyMemberCount = array();
        foreach($policyMemberCount as $key => $value){
            $refinePolicyMemberCount[$value['goodsNo']] = $value['memberCount'];
        }

        //추가정보 삽입
        foreach($goodsData as $key => $goodsEachData){
            $goodsNo = $goodsEachData['goodsNo'];
            //무상정책 정보 추가
            $goodsEachData['freePolicy']['name'] = $refinePolicyInfo[$goodsNo]['policyFreeSaleName'];
            $goodsEachData['freePolicy']['sno']  = $refinePolicyInfo[$goodsNo]['policyFreeSaleSno'];
            $goodsEachData['salePolicy']['name'] = $refinePolicyInfo[$goodsNo]['policySaleName'];
            $goodsEachData['salePolicy']['sno']  = $refinePolicyInfo[$goodsNo]['policySaleSno'];
            $goodsEachData['surveyPolicy']['name'] = $refinePolicyInfo[$goodsNo]['policySurveyName'];
            $goodsEachData['surveyPolicy']['sno']  = $refinePolicyInfo[$goodsNo]['policySurveySno'];
            $goodsEachData['policyMember'] = empty($refinePolicyMemberCount[$goodsNo])?0:$refinePolicyMemberCount[$goodsNo];

            $eachGoodsData = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo );
            $goodsEachData['memberTypeName'] = SlCodeMap::MEMBER_TYPE[ $eachGoodsData['memberType'] ];

            $hankookList = [];
            foreach( SlCodeMap::HANKOOK_TYPE as $hankookKey => $hankookValue ){
                if( $hankookKey & $eachGoodsData['hankookType'] ){
                    $hankookList[] = $hankookValue;
                }
            }

            $goodsAddReason = explode(',' , $eachGoodsData['addReason'] );
            $addReasonList = [];
            foreach( $goodsAddReason as $eachKey => $eachValue ){
                $addReasonList[] = '．'.SlCodeMap::ADMIN_CLAIM_REASON[$eachValue];
            }

            $goodsEachData['hankookTypeName'] = implode('<br>', $hankookList);

            $goodsEachData = array_merge($goodsEachData , SlCommonUtil::getAvailData($eachGoodsData,[
                'hankookType',
                'soldOutMemo',
                'sizeFilePath',
                'isOpenFl',
                'openGoodsNo',
                'groupBuyStart',
                'groupBuyEnd',
                'groupBuyCount',
                'groupBuyPrice',
                'groupBuyComment',
            ]));

            $goodsEachData['addReasonName'] = implode('<br>', $addReasonList);

            $optionList = DBUtil2::getList(DB_GOODS_OPTION,  'goodsNo', $goodsEachData['goodsNo'] );
            foreach($optionList as $optionListKey => $optionListValue){
                $optionNameList = [];
                if(  !empty($optionListValue['optionValue1'])  )  $optionNameList[] = $optionListValue['optionValue1'];
                if(  !empty($optionListValue['optionValue2'])  )  $optionNameList[] = $optionListValue['optionValue2'];
                if(  !empty($optionListValue['optionValue3'])  )  $optionNameList[] = $optionListValue['optionValue3'];
                if(  !empty($optionListValue['optionValue4'])  )  $optionNameList[] = $optionListValue['optionValue4'];
                if(  !empty($optionListValue['optionValue5'])  )  $optionNameList[] = $optionListValue['optionValue5'];
                $optionListValue['optionName'] = ( count($optionNameList) > 0  ) ? implode('/', $optionNameList) : '단일옵션' ;
                $optionList[$optionListKey] = $optionListValue;
            }
            $goodsEachData['optionList'] = $optionList;
            $goodsData[$key]=$goodsEachData;
        }
        return $goodsData;
    }

    /**
     * 검색 회원 리스트 반환
     * @param $searchObject
     * @return mixed
     */
    public function getMemberList($searchObject){

        //SitelabLogger::logger('getMemberList');

        $linkGoodsCnt = count($searchObject['goodsNo']);
        //SitelabLogger::logger('연결할 상품의 수');
        //SitelabLogger::logger($linkGoodsCnt);
        //gd_debug('연결할 상품의 수');
        //gd_debug($linkGoodsCnt);
        $goodsNoListStr = implode(',',$searchObject['goodsNo']);
        // = new SearchVo('1=?','1');

        $searchVo = new SearchVo('1=?','1');
        if( !empty($searchObject['memNm']) ){
            $searchVo->setWhere('memNm like concat(\'%\',\'\',\'%\')');
            $searchVo->setWhereValue($searchObject['memNm']);
        }
        if( !empty($searchObject['memId']) ){
            //$searchVo->setWhere('memId like concat(\'%\',\'\',\'%\')');
            //$searchVo->setWhere('memId like concat(\'%\', \'?\' ,\'%\')');
            $searchVo->setWhere("memId like '%{$searchObject['memId']}%' ");
            //$searchVo->setWhereValue($searchObject['memId']);
        }
        if( !empty($searchObject['ex1']) ){
            $searchVo->setWhere('ex1 = ?');
            $searchVo->setWhereValue($searchObject['ex1']);
        }

        $memberList = DBUtil::getListBySearchVo('es_member',$searchVo);
        $refineMemberList = array();

        //조회된 Member리스트에서 연결된 회원은 제외
        foreach( $memberList as $key => $value  ){
            $linkInfo = $this->getGoodsMemberLinkInfo($goodsNoListStr, $value['memNo']);
            if( $linkGoodsCnt  != $linkInfo['cnt'] ){
                $refineMemberList[] = $value;
            }
        }
        return $refineMemberList;
    }

    /**
     * 정책 적용 회원 반환
     * @param $searchObject
     * @return mixed
     */
    public function getPolicyGoodsMemberList($searchObject){
        return $this->sql->getPolicyGoodsMemberList($searchObject);
    }

    /**
     * 상품+회원별 정책 가져오기
     * @param $searchData
     * @return mixed
     */
    public function getPolicyByGoodsMember($searchData){
        return $this->sql->getPolicyByGoodsMember($searchData);
    }

    /**
     * 회원 연결
     * @param $inputLinkData
     * @throws \Exception
     */
    public function linkMember($inputLinkData){
        foreach($inputLinkData['goodsNo'] as $key => $goodsNo){
            foreach($inputLinkData['memNo'] as $memNoKey => $memNo){
                $mergeData['goodsNo'] = $goodsNo;
                $mergeData['memNo'] = $memNo;
                $searchVo = new SearchVo();
                $searchVo->setWhereArray(['goodsNo=?','memNo=?']);
                $searchVo->setWhereValueArray([$goodsNo,$memNo]);
                DBUtil::merge('sl_goodsPolicyMember', $mergeData, $searchVo);
            }
        }
    }

    /**
     * 회원 연결 삭제
     * @param $inputLinkData
     * @throws \Exception
     */
    public function linkDeleteMember($inputLinkData){
        foreach($inputLinkData['sno'] as $key => $sno){
            DBUtil::delete('sl_goodsPolicyMember',new SearchVo('sno=?',$sno));
        }
    }

    /**
     * 주문상품 일련번호 가져오기
     * @param $orderNo
     * @param $goodsNo
     * @param $optionSno
     * @return mixed
     */
    public function getOrderGoodsSno($orderNo,$goodsNo,$optionSno){
        return $this->sql->getOrderGoodsSno($orderNo,$goodsNo,$optionSno);
    }

    public function getFreeCount($goodsNo, $memNo){
        $cnt = $this->sql->getFreeCountByGoodsNoAndMemNo($goodsNo, $memNo)['freeDcCount'];
        return empty($cnt)?0:$cnt;
    }

    /**
     * 주문 상품 적용 정책 계산
     * @param $goodsData
     * @param $memNo
     * @return array
     */
    public function calculationGoodsPolicy($goodsData, $memNo){
        $result = array();
        $truncGoods = Globals::get('gTrunc.goods');

        //정책 적용 결과
        $freePrice = 0;
        $dcPrice = 0;
        $goodsNo = $goodsData['goodsNo'];
        $goodsCnt = $goodsData['goodsCnt'];

        if( empty($goodsData['price']) ){
            $goodsData['price'] = $goodsData;
        }
        $goodsUnitPrice = $goodsData['price']['goodsPrice'] + $goodsData['price']['optionPrice'] + $goodsData['price']['optionCostPrice'] + $goodsData['price']['optionTextPrice'];

        //정책 여부 확인, 없으면 새로 가져온다.(상품별 1회수행)
        if( empty($policyInfo[$goodsNo]) ){
            //상품번호에 해당하는 정책 가져오기
            $searchData['memNo'] = $memNo;
            $searchData['goodsNo'] = $goodsNo;
            $policyInfo[$goodsNo] = $this->getPolicyByGoodsMember($searchData);
            $policyInfo[$goodsNo]['freeBuyerCountUsed'] = $this->getFreeCount($goodsNo, $memNo);
            $policyInfo[$goodsNo]['freeBuyerAvailCount'] = $policyInfo[$goodsNo]['freeBuyerCount'] - $policyInfo[$goodsNo]['freeBuyerCountUsed'];
        }

        $goodsDcInfo = $policyInfo[$goodsNo];

        //튜닝 . 동계 근무복 1회 이상 구매시 할인 미적용. ( 23/11/27 까지 )
        /*$dcGoodsList = [ 1000000342 , 1000000343 ];
        if(in_array($goodsNo, $dcGoodsList) && ('23-11-27' >= date('y-m-d')) ){
            $memNo = \Session::get('member.memNo');
            $groupSno = \Session::get('member.groupSno');
            $count = DBUtil2::runSelect("select count(1) cnt from es_order a join es_orderGoods b on a.orderNo = b.orderNo where a.memNo = {$memNo} and b.goodsNo = {$goodsNo} and left(b.orderStatus,1) in ('s','d','g','p','o') ")[0]['cnt'];
            if( 0 >= $count ){
                if( 5 == $groupSno OR 12 == $groupSno ){
                    $policyInfo[$goodsNo]['companyRatio'] = 100;
                }else{
                    $policyInfo[$goodsNo]['companyRatio'] = 50;
                }
            }
        }*/

        //무상 및 할인 혜택 적용
        for($i=0; $i<$goodsCnt; $i++){
            if($policyInfo[$goodsNo]['freeBuyerAvailCount'] > 0){
                //1. 무상횟수 처리 (상품수 만큼 Loop)
                $policyInfo[$goodsNo]['freeBuyerCountUsed']++;
                $freeDcPrice = $goodsUnitPrice;
                $freePrice += $freeDcPrice;
                $dcPrice += $freeDcPrice;
                $policyInfo[$goodsNo]['freeBuyerAvailCount']--;
                //History
                $goodsDcInfo['freeDcAmount']+=$freeDcPrice;
                $goodsDcInfo['freeDcCount']++;
            }else{
                //2. 무상 횟수가 0이면 할인정책을 적용한다.
                //본사 구매 비율
                $orgCompanyRatio = $policyInfo[$goodsNo]['companyRatio'];
                if( $orgCompanyRatio >= 0.0 ){
                    $companyRatio = $orgCompanyRatio / 100.0;
                    $salePrice = gd_number_figure($goodsUnitPrice * $companyRatio, $truncGoods['unitPrecision'], $truncGoods['unitRound']);
                    $dcPrice += $salePrice;
                    //History
                    $goodsDcInfo['companyRatio']=$orgCompanyRatio;
                    $goodsDcInfo['companyPayment']+=$salePrice;
                    $goodsDcInfo['buyerRatio']=100-$orgCompanyRatio;
                    $goodsDcInfo['buyerPayment']+=$goodsUnitPrice-$salePrice;
                    $goodsDcInfo['dcCount']++;
                }
            }
        }

        $result['unitCustomDcInfo'] = $goodsDcInfo;
        $result['unitDcPrice'] = $dcPrice;
        $result['unitPolicyInfo'] = json_encode($goodsDcInfo, JSON_UNESCAPED_SLASHES);

        return $result;
    }

    /**
     * 상품에 연결된 회원 정보
     * @param $goodsNoList
     * @param $memNo
     * @return mixed
     */
    public function getGoodsMemberLinkInfo($goodsNoList, $memNo){
        return $this->sql->getGoodsMemberLinkInfo($goodsNoList, $memNo)[0];
    }

    /**
     * 품절상품 메모 저장
     * @param $postValue
     * @return mixed
     * @throws \Exception
     */
    public function saveSoldoutMemo($postValue){
        $updateData['soldOutMemo'] = $postValue['soldOutMemo'];
        return DBUtil2::update(DB_GOODS, $updateData, new SearchVo('goodsNo=?', $postValue['goodsNo']));
    }

    /**
     * 사이즈표 저장
     * @param $postValue
     * @param $files
     * @throws \Exception
     */
    public function saveSizeImg($postValue, $files){
        $fileData = $files['file'];
        $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $fileData['name']);
        $fileName = $postValue['goodsNo'].'.'.$ext;
        $rslt = GoodsPolicy::getSizeStorage()->upload($fileData['tmp_name'], 'goods/'.$fileName);
        DBUtil2::update(DB_GOODS,['sizeFilePath'=>$rslt],new SearchVo('goodsNo=?',$postValue['goodsNo']));
    }

    /**
     * 사이즈표 제거
     * @param $postValue
     * @param $files
     * @throws \Exception
     */
    public function deleteSizeImg($postValue){
        $goodsInfo = DBUtil2::getOne(DB_GOODS,'goodsNo',$postValue['goodsNo']);
        GoodsPolicy::getSizeStorage()->delete(str_replace('/data/etc/','',$goodsInfo['sizeFilePath']));
        DBUtil2::update(DB_GOODS,['sizeFilePath'=>''],new SearchVo('goodsNo=?',$postValue['goodsNo']));
    }

    public static function getSizeStorage(){
        return Storage::disk(Storage::PATH_CODE_ETC, 'local');
    }

}

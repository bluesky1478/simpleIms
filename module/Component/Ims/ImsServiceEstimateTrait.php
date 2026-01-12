<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlSmsUtil;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;

/**
 * IMS 생산가 견적 관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceEstimateTrait {

    /**
     * 견적 기본 구조 정보
     * @param $src
     * @return array
     */
    public function getEstimateViewDefaultData($src){
        $result = DBTableField::getTableKeyAndBlankValue(ImsDBName::ESTIMATE);
        $result['contents'] = ImsJsonSchema::ESTIMATE;
        //factory 기본값.
        $result['reqFactory'] =  $src['product']['produceCompanySno'];
        $result['estimateCount'] =  $src['product']['prdExQty'];

        /*$currency = new CurrencyExchangeRate();
        $result['contents']['exchange']=$currency->fetchPublicData(date('Ymd'))->USD;
        $result['contents']['exchangeDt']=date('Y-m-d');*/
        return $result;
    }

    /**
     * 생산가 확정 기본 구조
     * @param $src
     * @return array
     */
    public function getCostViewDefaultData($src){
        $result = DBTableField::getTableKeyAndBlankValue(ImsDBName::ESTIMATE);
        $result['contents'] = ImsJsonSchema::ESTIMATE;
        //factory 기본값.
        $result['reqFactory'] =  $src['product']['produceCompanySno'];
        $result['estimateCount'] =  $src['product']['prdExQty'];

        /*$currency = new CurrencyExchangeRate();
        $result['contents']['exchange']=$currency->fetchPublicData(date('Ymd'))->USD;
        $result['contents']['exchangeDt']=date('Y-m-d');*/

        return $result;
    }

    /**
     * 요청사항 처리
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveEstimateReq($params){

        $saveData = $params;
        $estimateData = DBUtil2::getOne(ImsDBName::ESTIMATE, 'sno', $saveData['sno']);

        //요청회차 처리.
        if( empty($saveData['sno']) ){
            //초기 요청
            DBTableField::checkRequired(ImsDBName::ESTIMATE, $params);
            $estimateCount = DBUtil2::getCount(ImsDBName::ESTIMATE, new SearchVo('styleSno=?',$params['styleSno']));
            $saveData['reqStatus'] = $params['reqStatus'];
            $saveData['reqCount'] = $estimateCount + 1;
            $saveData['reqManagerSno'] = SlCommonUtil::getManagerSno();
            //기본 정보
            $aReceiveContents = $saveData['contents'];
            $saveData['contents'] = ImsJsonSchema::ESTIMATE;
            foreach ($saveData['contents'] as $key => $val) {
                if (!empty($aReceiveContents[$key])) $saveData['contents'][$key] = $aReceiveContents[$key];
            }

            $copyFieldList = [ 'fabric', 'subFabric', 'jsonUtil', 'jsonMark', 'jsonLaborCost', 'jsonEtc', 'laborCost', 'marginCost', 'dutyCost', 'managementCost', 'prdMoq', 'priceMoq', 'addPrice' ,'deliveryType' , 'produceType', 'producePeriod' ];
            foreach($copyFieldList as $field){
                if (isset($params[$field])) $saveData['contents'][$field] = $params[$field];
            }

            //요청을 눌렀을 경우 진행중 처리.
            $savePrdData['sno'] = $params['styleSno'];
            $savePrdData['estimateStatus'] = array_flip(ImsCodeMap::IMS_PRD_PROC_STATUS)['진행'];
            $this->save(ImsDBName::PRODUCT, $savePrdData);
        }else{
            if( 3 == $params['reqStatus'] ){
                $saveData['completeDt'] = 'now()';
                //처리 완료 단계에서 환율이 없다면 환율 입력
            }
        }
        $saveData['contents'] = json_encode($saveData['contents']);
        $saveData['reqFiles'] = json_encode($params['reqFiles']);

        $sno = $this->save(ImsDBName::ESTIMATE, $saveData);
        $this->setSyncStatus($params['projectSno'], __METHOD__);

        //처리완료시 단가 선택을 자동으로 한다. (단 프로젝트가 기획 단계일 경우는 하지 않는다.)
        $projectInfo = DBUtil2::getOne(ImsDBName::PROJECT, 'sno' ,$estimateData['projectSno']);
        if( 3 == $params['reqStatus'] && !empty($estimateData['projectSno']) && !empty($estimateData['styleSno']) && $projectInfo['projectStatus'] > 20 ){
            $this->selectEstimate([
                'projectSno' => $estimateData['projectSno'],
                'styleSno' => $estimateData['styleSno'],
                'sno' => $sno,
            ]);
        }

        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 생산 가견적 정보 가져오기.
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function getFactoryEstimate($params){
        $list = DBUtil2::getComplexList($this->sql->getEstimateListTable(), new SearchVo('a.sno=?',$params['sno']), false, false, false);
        return $this->decorationEstimate($list[0]);
    }

    /**
     * 가견적 요청 리스트
     * @param $params
     * @return mixed
     */
    public function getEstimateList($params){
        $searchVo = new SearchVo('styleSno=?',$params['styleSno']);
        if(!empty($params['estimateType'])){
            $searchVo->setWhere('estimateType=?');
            $searchVo->setWhereValue($params['estimateType']);
        }
        $searchVo->setOrder('a.sno desc');
        $list = DBUtil2::getComplexList($this->sql->getEstimateListTable(), $searchVo, false, false, false);
        $list = SlCommonUtil::setEachData($list, $this, 'decorationEstimate');
        return $list;
    }

    /**
     * 원단 관리 정보 꾸미기
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     * @throws \Exception
     */
    public function decorationEstimate($each, $key=null, $mixData=null){
        $each['reqFiles'] = gd_htmlspecialchars_stripslashes($each['reqFiles']);
        $each = DBTableField::parseJsonField(ImsDBName::ESTIMATE, $each);
        $each = DBTableField::fieldStrip(ImsDBName::ESTIMATE, $each);
        $each['reqStatusKr'] = ImsCodeMap::IMS_BT_REQ_STATUS[$each['reqStatus']];
        $each['styleFullName'] = implode(' ',[substr($each['prdYear'],2,2),$each['prdSeason'],$each['productName']]);
        $each['reqMemo'] = gd_htmlspecialchars_stripslashes($each['reqMemo']);
        $each['resMemo'] = gd_htmlspecialchars_stripslashes($each['resMemo']);
        $each['reqMemoBr'] = nl2br($each['reqMemo']);
        $each['reqMemo1Br'] = nl2br($each['reqMemo1']);
        $each['reqMemo2Br'] = nl2br($each['reqMemo2']);
        $each['reqMemo3Br'] = nl2br($each['reqMemo3']);
        $each['resMemoBr'] = nl2br($each['resMemo']);
        $each['isFactory'] = SlCommonUtil::isFactory();
        if(empty($each['contents'])){
            $each['contents'] = ImsJsonSchema::ESTIMATE;
        }


        if( 0 === $each['contents']['deliveryType'] || '0' === $each['contents']['deliveryType'] ){
            $each['contents']['deliveryType'] = '';
        }

        if( empty($each['contents']['fabric'][0]) ){
            $fabric = $each['contents']['fabric'];
            $subFabric = $each['contents']['subFabric'];
            $each['contents']['fabric'] = [];
            $each['contents']['subFabric'] = [];
            $each['contents']['fabric'][] = $fabric;
            $each['contents']['subFabric'][] = $subFabric;
        }

        return $each;
    }


    /**
     * 가견적 선택
     * @param $params
     * projectSno , styleSno , sno
     * @return bool
     * @throws \Exception
     */
    public function selectEstimate($params){

        //기존 확정 상태는 처리완료 상태로 변경.
        DBUtil2::update(ImsDBName::ESTIMATE, [
            'reqStatus' => 3,
        ], new SearchVo(" reqStatus = 5 and styleSno = ? ",$params['styleSno']));

        //$saveData = [];
        //$saveData['sno'] = $params['sno']; //ReqSno
        //$saveData['reqStatus'] = array_flip(ImsCodeMap::IMS_BT_REQ_STATUS)['확정'];
        //$this->save(ImsDBName::ESTIMATE, $saveData);

        //스타일(상품) 상태변경
        $estimateData = $this->getFactoryEstimate(['sno'=>$params['sno']]);
        $savePrdData['estimateCost'] = $estimateData['estimateCost'];
        $savePrdData['estimateCount'] = $estimateData['estimateCount'];

        $savePrdData['sno'] = $params['styleSno'];
        $savePrdData['estimateConfirmSno'] = $params['sno'];
        $savePrdData['estimateConfirmManagerSno'] = SlCommonUtil::getManagerSno();
        $savePrdData['estimateConfirmDt'] = 'now()';
        $savePrdData['estimateStatus'] = array_flip(ImsCodeMap::IMS_PRD_PROC_STATUS)['완료'];

        /*SitelabLogger::logger2(__METHOD__, '상품 정보 저장(견적)');
        SitelabLogger::logger2(__METHOD__, $savePrdData);
        SitelabLogger::logger2(__METHOD__, '파라미터');
        SitelabLogger::logger2(__METHOD__, $params);*/

        $this->save(ImsDBName::PRODUCT, $savePrdData);

        //프로젝트 상태변경
        $this->setSyncStatus($params['projectSno'], __METHOD__);

        return true;
    }

    /**
     * 가견적(확정견적) 취소
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function cancelEstimate($params){
        //$estimateType = empty($params['estimateType']) ?  'estimate' : $params['estimateType'];
        //if('cost' === $estimateType){
            //스타일(상품) 상태변경
            //$savePrdData['prdCost'] = 0;
            //$savePrdData['prdCount'] = 0;
        //}else{
            //스타일(상품) 상태변경
            $savePrdData['estimateCost'] = 0;
            $savePrdData['estimateCount'] = 0;
        /*['val' => 'estimateConfirmSno', 'typ' => 'i', 'def' => null, 'name' => '가견적 선택여부'],     //가견적 확정 여부
            ['val' => 'estimateConfirmManagerSno', 'typ' => 'i', 'def' => null, 'name' => '선택한 사람'], //가견적 확정 여부
            ['val' => 'estimateConfirmDt', 'typ' => 'i', 'def' => null, 'name' => '선택일자'],         //가견적 확정 일자
            ['val' => 'estimateStatus', 'typ' => 'i', 'def' => 0, 'name' => '가견적 선택여부'],     //가견적 확정 여부*/

        //}

        //기존 확정 상태는 처리완료 상태로 변경.
        DBUtil2::update(ImsDBName::ESTIMATE, [
            'reqStatus' => 3,
        ], new SearchVo(" styleSno = ? ",$params['styleSno']));

        $savePrdData['sno'] = $params['styleSno'];
        $fieldPrefix = 'estimate';
        $savePrdData[$fieldPrefix.'ConfirmSno'] = 0;
        $savePrdData[$fieldPrefix.'ConfirmManagerSno'] = 0;
        $savePrdData[$fieldPrefix.'ConfirmDt'] = '';
        $savePrdData[$fieldPrefix.'Status'] = array_flip(ImsCodeMap::IMS_PRD_PROC_STATUS)['진행'];

        $fieldPrefix = 'cost';
        $savePrdData[$fieldPrefix.'ConfirmSno'] = 0;
        $savePrdData[$fieldPrefix.'ConfirmManagerSno'] = 0;
        $savePrdData[$fieldPrefix.'ConfirmDt'] = '';
        $savePrdData[$fieldPrefix.'Status'] = array_flip(ImsCodeMap::IMS_PRD_PROC_STATUS)['진행'];

        $this->save(ImsDBName::PRODUCT, $savePrdData);

        //프로젝트 상태변경
        $this->setSyncStatus($params['projectSno'], __METHOD__);

        return true;
    }

    /**
     * 생산가 확정 선택
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function selectCost($params){

        //기존 확정 상태는 처리완료 상태로 변경.
        DBUtil2::update(ImsDBName::ESTIMATE, [
            'reqStatus' => 3,
        ], new SearchVo(" reqStatus = 5 and styleSno = ? ",$params['styleSno']));

        $saveData = [];
        $saveData['sno'] = $params['sno']; //ReqSno
        $saveData['reqStatus'] = array_flip(ImsCodeMap::IMS_BT_REQ_STATUS)['확정'];
        $this->save(ImsDBName::ESTIMATE, $saveData);


        //스타일(상품) 상태변경
        $costData = $this->getFactoryEstimate(['sno'=>$params['sno']]);
        $savePrdData['prdCost'] = $costData['estimateCost'];
        $savePrdData['prdCount'] = $costData['estimateCount'];

        $savePrdData['sno'] = $params['styleSno'];
        $savePrdData['prdCostConfirmSno'] = $params['sno'];
        $savePrdData['prdCostConfirmManagerSno'] = SlCommonUtil::getManagerSno();
        $savePrdData['prdCostConfirmDt'] = 'now()';
        $savePrdData['prdCostStatus'] = array_flip(ImsCodeMap::IMS_PRD_PROC_STATUS)['완료'];

        $this->save(ImsDBName::PRODUCT, $savePrdData);

        //확정했으면 나머지는 모두 반려 처리 시킨다.
        DBUtil2::update(ImsDBName::ESTIMATE, ['reqStatus'=>'6'],new SearchVo("sno <> {$params['sno']} and styleSno=?", $params['styleSno']));

        //프로젝트 상태변경
        $this->setSyncStatus($params['projectSno'], __METHOD__);

        return true;
    }

    /**
     * 견적상태 변경
     * @param $params
     * @return bool
     */
    public function setEstimateStatus($params){
        if(!empty($params['sno'])){
            if( 1 == $params['reqStatus']){
                $params['completeDt'] = '0000-00-00 00:00:00';
            }
            $this->save(ImsDBName::ESTIMATE, $params);
            return true;
        }
        return false;
    }


    /**
     * 가견적 생산가 입력여부 => 생산견적 입력 여부
     * @param $prdList
     * @return mixed
     */
    public function getEstimateCostStatus($prdList){
        foreach($prdList['prdList'] as $key => $prd){
            if( 1 == $prd['prdCostStatus'] || 1 == $prd['estimateStatus'] ){
                $searchVoCost = new SearchVo("styleSno=?", $prd['sno']);
                $searchVoCost->setOrder('reqCount desc, regDt desc');
                $cost = DBUtil2::getOneBySearchVo(ImsDBName::ESTIMATE, $searchVoCost);
                if(!empty($cost['estimateCost']) && $cost['estimateCost'] > 0){
                    $prd['existsCost'] = true;
                    $prd['existsEstimate'] = true;
                }
            }

            if( true !== $prd['existsCost']) $prd['existsCost'] = false;
            if( true !== $prd['existsEstimate']) $prd['existsEstimate'] = false;

            $prdList['prdList'][$key] = $prd;
        }
        return $prdList['prdList'];
    }

    /*const STATUS = [
        0 => '요청',
        1 => '처리중',
        2 => '처리완료',
        3 => '처리불가',
        4 => '승인',   //승인 -> 승인
        5 => '반려', //반려,번복 -> 다시해.
    ];*/

}



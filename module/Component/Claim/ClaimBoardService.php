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
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;
use SlComponent\Util\SlLoader;
use Framework\Utility\SkinUtils;
use Component\Storage\Storage;
use SlComponent\Util\SlSkinUtil;

/**
 *  클레임 게시판 서비스
 * @package Component\Claim
 */
class ClaimBoardService {
    use SlCommonTrait;

    /***
     * 클레임 상품 정보 반환(관리자 통계용)
     * @param $bdId
     * @param $bdSno
     * @param string $tableName
     * @return array
     */
    public function getClaimGoodsDataAdmin($bdId, $bdSno){
        return $this->getClaimGoodsData($bdId, $bdSno, $tableName = 'sl_claimBoardAdminData' );
    }

    /**
     * 클레임 상품 정보 반환
     * @param $bdId
     * @param $bdSno
     * @param string $tableName
     * @return array
     */
    public function getClaimGoodsData($bdId, $bdSno, $tableName = 'sl_claimBoardData' ){
        $searchVo = new SearchVo(['bdId=?','bdSno=?'] , [$bdId, $bdSno]);
        $searchVo->setOrder(' goodsNo , optionIdx ');
        $optionBaseDataList = DBUtil::getListBySearchVo( $tableName , $searchVo );
        $claimBoardType = '';
        $etcRequest = '';
        $resultDataList = array();
        //상품번호별로 정제
        $goods = SlLoader::cLoad('Goods','Goods');

        foreach($optionBaseDataList as $optionBaseDataListKey =>  $goodsData){
            $claimBoardType = $goodsData['claimBoardType'];
            $etcRequest = $goodsData['etcRequest'];
            $goodsImageInfo = $goods->getGoodsImageData($goodsData['goodsNo'],'list')[0];
            $goodsImageSrc = SkinUtils::imageViewStorageConfig($goodsImageInfo['imageName'], $goodsImageInfo['imagePath'], $goodsImageInfo['imageStorage'], 100, 'goods')[0];
            $optionBaseDataList[$optionBaseDataListKey]['goodsImageSrc'] = $goodsImageSrc;
            $optionBaseDataList[$optionBaseDataListKey]['claimReasonStr'] = SlCodeMap::CLAIM_REASON[$goodsData['claimReason']];

            $optionListStr = $goods->getGoodsOptionByGoodsNo($goodsData['goodsNo'])['goodsOptionNameStrList'];
            $optionListHtml = $goods->getGoodsOptionByGoodsNo($goodsData['goodsNo'])['goodsOptionNameHtmlList'];
            $optionBaseDataList[$optionBaseDataListKey]['optionListStr'] = empty($optionListStr) ? '' : $optionListStr;
            $optionBaseDataList[$optionBaseDataListKey]['optionListHtml'] = empty($optionListHtml) ? '' : $optionListHtml;
        }
        $resultDataList['claimBoardType'] = $claimBoardType;
        $resultDataList['etcRequest'] = $etcRequest;
        $resultDataList['optionBaseData'] = $optionBaseDataList;

        return  $resultDataList;
    }

    /**
     * 컨트롤러에 클레임 값 넣어주기
     * @param $controller
     * @param $bdId
     * @param $bdSno
     */
    public function setClaimData(&$controller, $bdId, $bdSno ){
        $boardData = $controller->getData('bdWrite');

        //추가 정보 넣기
        $controller->setData('claimBoardType', SlCodeMap::CLAIM_BOARD_TYPE);
        $controller->setData('claimReason', SlCodeMap::CLAIM_REASON);

        //상품정보 가져오기
        $claimGoods = $this->getClaimGoodsData($bdId, $bdSno);

        //관리자 상품 정보 가져오기
        $adminClaimGoods = $this->getClaimGoodsDataAdmin($bdId, $bdSno);
        if(empty($adminClaimGoods['optionBaseData'])){
            $adminClaimGoods = $claimGoods;
        }

        $controller->setData('claimGoods', $claimGoods['optionBaseData']);
        $controller->setData('selectedClaimBoardType', $claimGoods['claimBoardType']);
        $controller->setData('etcRequest', $claimGoods['etcRequest']);
        $controller->setData('claimBoardTypeStr', SlCodeMap::CLAIM_BOARD_TYPE[$claimGoods['claimBoardType']]);
        $controller->setData('claimReasonStr', SlCodeMap::CLAIM_REASON[$claimGoods['claimReason']]);

        //초도 물량 주문번호
        $controller->setData('initOrderNo', SlCodeMap::SCM_ORDER_INIT[MemberUtil::getMemberScmNo()]);

        //관리자용
        $controller->setData('adminClaimGoods', $adminClaimGoods['optionBaseData']);
        $controller->setData('selectedAdminClaimBoardType', $adminClaimGoods['claimBoardType']);
        //gd_debug($claimGoods);
        //SitelabLogger::logger($claimGoods);



        $controller->setData('bdWrite', $boardData);

    }

    /**
     * @Deprecated
     * 게시판 클레임 정보 저장 ( 관리자 통계용 )
     * @param $req
     */
    public function saveBoardClaimInfoAdmin($req){
        $this->saveBoardClaimInfo($req, 'adminGoodsInfo' , 'sl_claimBoardAdminData' );
    }

    /**
     * 게시판 클레임 정보 저장 / 수정까지 처리.
     * @param $req
     * @param string $dataKey
     * @throws Exception
     */
    public function saveBoardClaimInfo($req, $dataKey = 'goodsInfo', $tableName = 'sl_scmClaimData'  ){
        $saveData = SlCommonUtil::getAvailData($req,[
            'claimData',
            'claimGoods',
            'exchangeGoods',
            'bdId',
        ]);

        $saveData['bdSno'] = $req['sno'];
        //json data decode
        $jsonDataFieldList = [
            'claimData',
            'claimGoods',
            'exchangeGoods',
        ];

        //데이터 풀기
        foreach($jsonDataFieldList as $fieldName ){
            $saveData[$fieldName] = json_decode($saveData[$fieldName], true);
        }
        //불필요 데이터 제거
        foreach( $saveData['claimGoods'] as $key => $value ){
            unset($value['claimReasonList']);
            $saveData['claimGoods'][$key] = $value;
        }
        foreach( $saveData['exchangeGoods'] as $key => $value ){
            unset($value['claimReasonList']);
            $saveData['exchangeGoods'][$key] = $value;
        }

        $saveData['orderNo'] = $saveData['claimData']['orderNo'];
        $saveData['claimType'] = $saveData['claimData']['claimType'];

        $saveData['scmNo'] = MemberUtil::getMemberScmNo(\Session::get('member.memNo'));

        $saveData['refundData'] = json_encode(SlCommonUtil::getAvailData($req,[
            'depositor','bankName', 'refundType', 'deposit'
        ]));

        //데이터 묶기
        foreach( $jsonDataFieldList as $fieldName ){
            $saveData[$fieldName] = json_encode($saveData[$fieldName]);
        }
        unset($saveData['claimData']);

        $searchVo = new SearchVo(['bdId=?', 'bdSno=?'], [$req['bdId'], $req['sno']]);
        $claimData = DBUtil2::getOneBySearchVo($tableName, $searchVo);
        if(empty($claimData)){
            DBUtil2::insert($tableName, $saveData);
        }else{
            unset($saveData['scmNo']);
            DBUtil2::update($tableName, $saveData, $searchVo); //데이터를 수정.
        }
    }


    /**
     * 상품 기본 정보 가져오기
     * @param $goodsInfo
     * @param $goodsNo
     * @return mixed
     */
    public function setGoodsDefaultInfo($goodsInfo, $goodsNo){
        $goodsData = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
        $goodsImage = DBUtil2::getOneBySearchVo(DB_GOODS_IMAGE, new SearchVo(['goodsNo=?', 'imageKind=?'],[$goodsNo,'list']));
        $goodsInfo['imagePath'] = Storage::disk(Storage::PATH_CODE_GOODS,$goodsData['imageStorage'])->getHttpPath().$goodsData['imagePath'].$goodsImage['imageName'];
        $claimReasonDataList = SlCommonUtil::getClaimCodeReasonByGoodsNo($goodsNo);
        $refineClaimReason = [];
        foreach( $claimReasonDataList as $claimReasonKey => $claimReasonData ){
            $claimReason = [
                'code' => $claimReasonKey,
                'contents' => $claimReasonData,
            ];
            $refineClaimReason[] = $claimReason;
        }
        $goodsInfo['claimReasonList'] = $refineClaimReason;
        $goodsInfo['goodsNo'] = $goodsNo;
        $goodsInfo['goodsNm'] = $goodsData['goodsNm'];
        return $goodsInfo;
    }

    /**
     * 주문상품 반환
     * @param $orderNo
     * @return array
     */
    public function getOrderGoods($orderNo){
        $orderService = SlLoader::cLoad('Order','OrderService');
        $orderGoodsList = $orderService->getOrderGoodsAndGoodsOption($orderNo);

        $availOrderGoodsList = [];
        foreach( $orderGoodsList as $goodsData ){

            $goodsNo = $goodsData['goodsNo'];

            if(empty($availOrderGoodsList[$goodsNo])){
                $availOrderGoodsList[$goodsNo] = $this->setGoodsDefaultInfo($availOrderGoodsList[$goodsNo], $goodsNo);
            }

            $optionNameList = [];
            for($i=1; 5>=$i; $i++){
                if(!empty($goodsData['optionValue'.$i])){
                    $optionNameList[] = $goodsData['optionValue'.$i];
                }
            }
            $availOrderGoodsList[$goodsNo]['option'][] = [
                'optionNo' => $goodsData['optionNo'],
                'optionName' => implode('/', $optionNameList),
                'optionCnt' => 0,
                'maxCnt' => $goodsData['goodsCnt'],
            ];
            $availOrderGoodsList[$goodsNo]['optionTotalCount'] = 0;
            $availOrderGoodsList[$goodsNo]['claimReason'] = '';
        }

        //키 정제
        $refineOrderGoodsList = [];
        foreach( $availOrderGoodsList as $value ){
            $refineOrderGoodsList[] = $value;
        }

        return $refineOrderGoodsList;
    }

    /**
     * @param $goodsNo
     * @return mixed
     */
    public function getGoodsInfoWithoutOrder($goodsNo){
        $goodsInfo = $this->setGoodsDefaultInfo([], $goodsNo);
        $optionList = DBUtil2::getList(DB_GOODS_OPTION, 'goodsNo', $goodsNo);
        foreach($optionList as $optionData){
            $optionNameList = [];
            for($i=1; 5>=$i; $i++){
                if(!empty($optionData['optionValue'.$i])){
                    $optionNameList[] = $optionData['optionValue'.$i];
                }
            }
            $goodsInfo['option'][] = [
                'optionNo' => $goodsData['optionNo'],
                'optionName' => implode('/', $optionNameList),
                'optionCnt' => 0,
                'maxCnt' => 30,
            ];
        }

        $goodsInfo['optionTotalCount'] = 0;
        $goodsInfo['claimReason'] = '';

        return $goodsInfo;
    }

    /**
     * 교환 상품 정보
     * @param $goodsNo
     * @return array
     */
    public function getGoodsInfo($goodsNo){
        $refineGoodsInfo = [];

        $goodsInfo = DBUtil2::getOne(DB_GOODS, 'goodsNo', $goodsNo);
        $goodsImage = DBUtil2::getOneBySearchVo(DB_GOODS_IMAGE, new SearchVo(['goodsNo=?', 'imageKind=?'],[$goodsNo,'main']));

        $refineGoodsInfo['goodsNo'] = $goodsNo;
        $refineGoodsInfo['imageSrc'] = Storage::disk(Storage::PATH_CODE_GOODS,$goodsInfo['imageStorage'])->getHttpPath().$goodsInfo['imagePath'].$goodsImage['imageName'];
        $refineGoodsInfo['goodsNm'] = $goodsInfo['goodsNm'];
        $refineGoodsInfo['optionTotalCount'] = 0;
        $goodsOptionData = $this->getGoodsOption($goodsInfo);
        $refineGoodsInfo = array_merge($refineGoodsInfo, $goodsOptionData);

        return $refineGoodsInfo;
    }


    /**
     * 상품의 옵션 가져오기
     * @param $goodsInfo
     * @return array[]
     */
    public function getGoodsOption($goodsInfo){
        $goodsNo = $goodsInfo['goodsNo'];
        $optionValueList = explode('^|^', $goodsInfo['optionName']);
        $goodsOptionSelectList = [];
        $goodsOptionSelectFirstList = [];

        if(count($optionValueList)>1){
            //$optionValueList
            $lastIdx = count($optionValueList)-1;

            $selectList = [];
            foreach($optionValueList as $key => $optionName){
                if( $key == $lastIdx ) {
                    break;
                }
                $optionSubject = '=== ' . $optionName . ' 선택(필수) ===';
                $selectList[] = $optionSubject;
                $goodsOptionSelectFirstList[] = '';
                $optionValueIdx = $key + 1;
                $goodsDistinctSelectOption = DBUtil2::runSelect("SELECT DISTINCT optionValue{$optionValueIdx} as optionValue FROM es_goodsOption WHERE goodsNo={$goodsNo}");
                foreach($goodsDistinctSelectOption as $optionValue){
                    $selectList[] = $optionValue['optionValue'];
                }

                $goodsOptionSelectList[] = $selectList;
                $selectList = [];
            }
        }else{
            $lastIdx = 0;
        }
        $lastOptionValue = $lastIdx + 1;
        $goodsDistinctOption = DBUtil2::runSelect("SELECT DISTINCT optionValue{$lastOptionValue} as optionValue  FROM es_goodsOption WHERE goodsNo={$goodsNo}");

        $optionList = [];
        foreach($goodsDistinctOption as $optionValue){
            $optionList[] = [
                'optionName' => $optionValue['optionValue'],
                'optionCount' => 0
            ];
        }

        return [
            'goodsOptionSelectData' => $goodsOptionSelectFirstList,
            'goodsOptionSelectList' => $goodsOptionSelectList,
            'goodsOptionList' => $optionList,
        ];
    }

    public function getScmClaimDataByBdSno($bdSno){
        return $this->getScmClaimData(DBUtil2::getOne('sl_scmClaimData', 'bdSno', $bdSno, false));
    }
    public function getScmClaimDataBySno($sno){
        return $this->getScmClaimData(DBUtil2::getOne('sl_scmClaimData', 'sno', $sno, false));
    }

    public function getGoodsOptionCode($goodsNo, $optionNo){
        $optionNo = empty($optionNo) ? -1 : $optionNo;
        return DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo(['goodsNo=?', 'optionNo=?'],[$goodsNo,$optionNo]) );
    }

    public function getGoodsOptionName($goodsNo, $optionNameList){
        if( !empty($optionNameList[0]) ){
            return DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo(['goodsNo=?', 'optionValue1=?', 'optionValue2=?'],[$goodsNo,$optionNameList[0],$optionNameList[1]]) );
        }else{
            return DBUtil2::getOneBySearchVo(DB_GOODS_OPTION, new SearchVo(['goodsNo=?', 'optionValue1=?'],[$goodsNo,$optionNameList[1]]) );
        }
    }

    /**
     * 클레임 데이터 반환
     * @param $claimData
     * @return mixed
     */
    public function getScmClaimData($claimData){

        $claimData['memo'] = nl2br($claimData['memo']);

        //JSON DECODE
        $fieldList = [
            'claimGoods',
            'exchangeGoods',
        ];
        foreach($fieldList as $field){
            $claimData[$field] = json_decode(stripslashes($claimData[$field]), true);
        }
        $claimData['refundData'] = json_decode($claimData['refundData'], true);
        $claimData['refundData']['refundTypeKr'] = SlCodeMap::REFUND_TYPE[$claimData['refundData']['refundType']];

        //Refine Claim Goods
        $refineClaimGoodsData = [];
        foreach( $claimData['claimGoods'] as $claimGoodsKey => $claimGoods){
            //클레임 KR 처리
            $claimGoods['claimReasonKr'] = SlCodeMap::ADMIN_CLAIM_REASON[$claimGoods['claimReason']];
            $totalCount = 0;

            foreach( $claimGoods['option'] as $optionKey => $optionEach){
                $totalCount += $optionEach['optionCnt'];
                $optionEach['optionCode'] = $this->getGoodsOptionCode($claimGoods['goodsNo'], $optionEach['optionNo'])['optionCode'];
                $claimGoods['option'][$optionKey] = $optionEach;
            }

            //if( $totalCount > 0 ){
                $claimGoods['optionTotalCount'] += $totalCount;
                $refineClaimGoodsData[] = $this->setGoodsDefaultInfo($claimGoods, $claimGoods['goodsNo']);
            //}
        }

        //Refine Exchange Goods
        $refineExchangeGoodsData = [];
        foreach( $claimData['exchangeGoods'] as $exchangeGoodsKey => $exchangeGoods){

            //SitelabLogger::logger($exchangeGoods);

            //클레임 KR 처리
            $totalCount = 0;
            foreach( $exchangeGoods['goodsOptionList'] as $optionKey => $optionEach){

                //SitelabLogger::logger($optionKey);
                //SitelabLogger::logger($optionEach);

                //if( $optionEach['optionCount'] > 0){
                    $totalCount += $optionEach['optionCount'];

                    if( empty($optionEach['optionNo']) ){
                        $getOptionData = $this->getGoodsOptionName($exchangeGoods['goodsNo'], [$exchangeGoods['goodsOptionSelectData'][0],$optionEach['optionName']] );
                    }else{
                        //$optionNo = $optionEach['optionNo'];
                        $getOptionData = $this->getGoodsOptionCode($exchangeGoods['goodsNo'], $optionEach['optionNo']);
                    }

                    $optionEach['optionCode'] = $getOptionData['optionCode'];
                    $optionEach['optionStock'] = $getOptionData['stockCnt'];

                    //$optionEach//

                    $exchangeGoods['goodsOptionList'][$optionKey] = $optionEach;
                //}else{
                    //unset($exchangeGoods['goodsOptionList'][$optionKey]);
                //}
            }
            //if( $totalCount > 0 ){
                $exchangeGoods['optionTotalCount'] += $totalCount;
                $refineExchangeGoodsData[] = $this->setGoodsDefaultInfo($exchangeGoods, $exchangeGoods['goodsNo']);
            //}
        }

        $claimData['claimGoods'] = $refineClaimGoodsData;
        $claimData['exchangeGoods'] = $refineExchangeGoodsData;

        //Kr 처리
        $claimData['claimTypeKr'] = SlCodeMap::CLAIM_TYPE[$claimData['claimType']];
        $claimData['claimStatusKr'] = SlCodeMap::NEW_CLAIM_STATUS[$claimData['claimStatus']];

        //OrderStatus
        $orderData = DBUtil2::getOne(DB_ORDER,'orderNo',$claimData['orderNo']);
        $claimData['orderStatus'] = $orderData['orderStatus'];
        $claimData['orderStatusKr'] = SlCommonUtil::getOrderStatusName2($orderData['orderStatus']);

        $claimData['invoiceNo'] = DBUtil2::runSelect("select distinct invoiceCompanySno, invoiceNo from es_orderGoods where orderNo={$claimData['orderNo']} and invoiceNo <> ''");

        return $claimData;
    }

    /**
     * 클레임 등록
     * @param $sno
     * @return mixed
     * @throws Exception
     */
    public function regClaim($sno){
        DBUtil2::update('sl_scmClaimData',['claimStatus'=>1, 'claimRegDt'=>'now()'],new SearchVo('sno=?',$sno)); //처리중으로 변경
        $claimData = $this->getScmClaimDataBySno($sno);
        $this->regClaimBoardAdminData($sno, $claimData);//통계용 자료 등록
        return $claimData;
    }

    /**
     * 단순 클레임 완료 처리.
     * @param $sno
     * @return mixed
     * @throws Exception
     */
    public function setComplete($sno){
        $update = DBUtil2::update('sl_scmClaimData',['claimStatus'=>2, 'claimCompleteDt'=>'now()'],new SearchVo('sno=?',$sno)); //처리완료 변경
        return $this->getScmClaimDataBySno($sno);
    }


    public function setChange($sno){
        //TODO $update = DBUtil2::update('sl_scmClaimData',['claimStatus'=>2, 'claimCompleteDt'=>'now()'],new SearchVo('sno=?',$sno)); //처리완료 변경

        //SitelabLogger::logger($this->getScmClaimDataBySno($sno));

        //재고 수정.


        //TODO 전체 재고 수정.

        return $this->getScmClaimDataBySno($sno);
    }

    /**
     * 교환 처리 불가
     * @param $sno
     * @return mixed
     * @throws Exception
     */
    public function setReject($sno){
        $update = DBUtil2::update('sl_scmClaimData',['claimStatus'=>9, 'claimCompleteDt'=>'now()'],new SearchVo('sno=?',$sno)); //처리불가 변경
        return $this->getScmClaimDataBySno($sno);
    }

    /**
     * 통계용 자료 등록
     * @param $sno
     * @param $claimData
     * @throws Exception
     */
    public function regClaimBoardAdminData($sno, $claimData){

        //통계 자료 등록 (삭제후)
        DBUtil2::delete('sl_claimBoardAdminData', new SearchVo(['bdSno=?','bdId=?'],[$claimData['bdSno'], $claimData['bdId']]));
        $claimData = $this->getScmClaimDataBySno($sno);
        $claimGoodsData = SlCommonUtil::getAvailData($claimData,[
            'bdId',
            'bdSno',
            'orderNo',
        ]);
        $claimGoodsData['claimSno'] = $sno;
        $claimGoodsData['claimBoardType'] = $claimData['claimType'];

        foreach($claimData['claimGoods'] as $key => $each){
            $claimGoodsData['goodsNo'] = $each['goodsNo'];
            $claimGoodsData['goodsName'] = $each['goodsNm'];
            $claimGoodsData['goodsIdx'] = $key;
            foreach($each['option'] as $option){
                if( $option['optionCnt'] > 0 ){
                    $claimGoodsData['optionIdx'] = $option['optionNo'];
                    $claimGoodsData['optionName'] = $option['optionName'];
                    $claimGoodsData['optionCount'] = $option['optionCnt'];
                    $claimGoodsData['claimReason'] = $each['claimReason'];
                    DBUtil2::insert('sl_claimBoardAdminData', $claimGoodsData);
                }
            }
        }
    }

    /**
     * 클레임 정보 업데이트
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function updateClaim($params){
        $updateData = SlCommonUtil::getAvailData($params, ['memo', 'claimStatus']);
        $orgData = DBUtil2::getOne('sl_scmClaimData','sno',$params['sno']);

        if( 2 == $params['claimStatus'] ){
            if( '0000-00-00' == $orgData['claimCompleteDt'] || empty($orgData['claimCompleteDt']) ){
                $updateData['claimCompleteDt'] = 'now()';
            }
        }else{
            $updateData['claimCompleteDt'] = '';
        }

        DBUtil2::update('sl_scmClaimData', $updateData, new SearchVo('sno=?', $params['sno']));
        return $this->getScmClaimDataBySno($sno);
    }

    /**
     * 게시판 공통 설정
     * @param $controller
     * @param $claimType
     */
    public function setClaimBoardCommonController($controller, $claimType){
        $controller->addCss(
            [
                '../css/gd_custom_contents.css'
                , '../css/custom.css'
                , '../js/jquery/chosen/chosen.css'
            ]
        );
        $controller->addScript(
            [
                '../js/jquery/chosen/chosen.jquery.js'
                ,'plugin/vue.js'
            ]
        );
        $controller->setData('claimBoardType', SlCodeMap::CLAIM_BOARD_TYPE);

        if( 'general' === $claimType ){
            $claimTitle = '1:1문의(일반문의)';
        }else{
            $claimTitle = SlCodeMap::CLAIM_TYPE[$claimType].' 요청';
        }
        $controller->setData('claimTitle', $claimTitle);
        $controller->setData('claimType', $claimType);
    }

    /**
     * Write 컨트롤러에 값 셋팅
     * @param $controller
     */
    public function setClaimBoardController($controller){
        $bdWrite = $controller->getData('bdWrite');

        $getValue = \Request::get()->toArray();
        $bdId = $getValue['bdId'];
        $claimType = gd_isset($getValue['claimType'],'general');

        $controller->setData('otherSkin', SlSkinUtil::getOtherSkinName());
        $controller->setData('maxUploadSize',ini_get('upload_max_filesize'));
        if( !empty(SlCodeMap::CLAIM_BOARD[$bdId]) ){
            $validationMapField = [
                'as' => [
                    'order'=>true,
                    'photo'=>true,
                ],
                'exchange' => [
                    'order'=>true,
                    'exchangeGoods'=>true,
                ],
                'back' => [
                    'order'=>true,
                ],
            ];
            $controller->setData('validationFieldMap',$validationMapField[$claimType]);

            /*if( 'general' === $claimType && empty($getValue['adminModify']) ){
                $refineCategory = [];
                $idx = 0;
                foreach($bdWrite['cfg']['arrCategory'] as $each){
                    if($idx > 2){
                        $refineCategory[$each] = $each;
                    }
                    $idx++;
                }
                $bdWrite['cfg']['arrCategory'] = $refineCategory;
            }*/
            $this->setClaimBoardCommonController($controller, $claimType);
        }

        //제목 셋팅
        if( 'general' !== $claimType && empty($bdWrite['data']['subject']) ){
            //FIXE 교환 자동 셋팅 - 추후 선택한 것에 따라
            $memNm = \Session::get('member.memNm');
            $memNick = SlCommonUtil::setEmptyValue(\Session::get('member.nickNm'), '/'.\Session::get('member.nickNm'));
            $memId = \Session::get('member.memId');
            $cellPhone = SlCommonUtil::setEmptyValue(\Session::get('member.cellPhone'), '/'.\Session::get('member.cellPhone'));
            $memberInfo = DBUtil2::getOne(DB_MEMBER,'memNo', \Session::get('member.memNo'));
            $claimTypeKr = SlCodeMap::CLAIM_TYPE[$claimType];
            $bdWrite['data']['subject'] = "[{$memberInfo['ex1']}] {$claimTypeKr}요청 : {$memNm}({$memId}{$memNick}{$cellPhone})";
            $bdWrite['data']['category'] = SlCodeMap::CLAIM_TYPE[$claimType];
        }

        //로그인 여부 체크.
        if( !gd_is_login() ){
            unset( $bdWrite['cfg']['arrCategory']['A/S'] );
            unset( $bdWrite['cfg']['arrCategory']['교환'] );
            unset( $bdWrite['cfg']['arrCategory']['반품/환불'] );
        }

        $controller->setData('bdWrite', $bdWrite);
        $controller->setData('claimStatusMap', SlCodeMap::NEW_CLAIM_STATUS);
    }

    /**
     * 리스트 컨트롤러에 값 셋팅
     * @param $controller
     */
    public function setClaimBoardListController($controller){
        $getValue = \Request::get()->toArray();
        $bdId = $getValue['bdId'];
        if( !empty(SlCodeMap::CLAIM_BOARD[$bdId]) ){
            $claimType = gd_isset($getValue['claimType'],'general');

            if( 'general' === $claimType ){
                $claimTitle = '1:1문의(일반문의)' ;
            }else{
                $claimTitle = SlCodeMap::CLAIM_TYPE[$claimType]. ' 문의';
            }
            $controller->setData('claimTitle', $claimTitle);
        }
        $controller->setData('otherSkin', SlSkinUtil::getOtherSkinName());
    }

    /**
     * View 컨트롤러에 값 셋팅
     * @param $controller
     */
    public function setClaimBoardViewController($controller){
        $getValue = \Request::get()->toArray();
        $bdId = $getValue['bdId'];
        $claimType = gd_isset($getValue['claimType'],'general');

        if( !empty(SlCodeMap::CLAIM_BOARD[$bdId]) ){
            $this->setClaimBoardCommonController($controller, $claimType);
            $bdViewData = $controller->getData('bdView');
            $bdViewData['data']['auth']['view'] = 'y';
            $bdViewData['data']['auth']['modify'] = 'y';
            $controller->setData('bdView', $bdViewData);
        }
        $controller->setData('otherSkin', SlSkinUtil::getOtherSkinName());
    }

}


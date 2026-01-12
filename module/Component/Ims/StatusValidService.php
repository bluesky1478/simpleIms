<?php

namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Manager;
use Component\Sms\Code;
use Controller\Admin\Ims\Step\ImsStepTrait;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Mail\MailService;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 상태 변경 강제값 체크 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class StatusValidService
{

    /**
     * 발주 체크
     * @param $projectData
     * @param $addData
     * @param $fileData
     * @return string|null
     * @throws \Exception
     */
    public static function checkOrderComplete($projectData, $addData, $fileData){
        
        $rsltArray = [];
        //생산가 체크
        if( 2 != $projectData['priceStatus']){
            $rsltArray[] = '판매가 승인';
        }
        //판매가 체크
        if( 2 != $projectData['costStatus']){
            $rsltArray[] = '생산가 승인';
        }
        if( 'p' !== $projectData['assortApproval'] && 'x' !== $projectData['assortApproval'] ){
            $rsltArray[] = '아소트 승인 ';
        }
        if( '2' != $projectData['workStatus'] ){
            $rsltArray[] = '작지 승인 ';
        }
        if( 'p' !== $projectData['customerOrderConfirm'] && 'x' !== $projectData['customerOrderConfirm'] ){
            $rsltArray[] = '사양서 승인 ';
        }
        if( '2' !== $projectData['fabricStatus'] ){
            $rsltArray[] = '퀄리티 확정 여부';
        }

        //슈퍼 관리자 패스
        if(SlCommonUtil::isDevId()){
            $rsltArray = [];
        }

        if( count($rsltArray) > 0 ){
            return implode(', ', $rsltArray).' 은(는) 필수 입니다.';
        }else{
            //정상이라면 생산데이터를 자동 생성해준다.
            $imsService = SlLoader::cLoad('ims', 'imsService');
            $projectDetail = $imsService->getProject(['sno'=>$projectData['sno']]);

            foreach( $projectDetail['productList'] as $prd){
                if( 'y' == $prd['delFl'] ) continue;
                $productionData = DBUtil2::getCount(ImsDBName::PRODUCTION,new SearchVo('styleSno=?',$prd['sno']));

                if( empty( $productionData ) ){

                    //작지 데이터 가져오기
                    $eworkData = DBUtil2::getOne(ImsDBName::EWORK, 'styleSno', $prd['sno']);
                    $productionData = SlCommonUtil::getAvailData($prd, [
                        'projectSno',
                        'customerSno',
                        'produceCompanySno',
                        'msDeliveryDt',
                    ]);

                    $productionData['styleSno'] = $prd['sno'];
                    $productionData['totalQty'] = $prd['prdExQty'];
                    $productionData['assortConfirm'] = 'p';
                    $productionData['workConfirm'] = 'p';
                    $productionData['sizeOptionQty'] = '{"\ubcc4\ucca8":"'. $prd['prdExQty'] .'"}';
                    $productionData['firstData'] = '\"\"';
                    $productionData['produceStatus'] = '10';

                    //기본설정
                    //생산처 체크
                    $prdUpdate = [];
                    if(empty($prd['produceCompanySno'])){
                        $productionData['produceCompanySno'] = 43;//기본 하나어패럴
                        $prdUpdate['produceCompanySno'] = 43;
                    }
                    //제조국 체크
                    /*if(empty($prd['produceNational'])){
                        $prdUpdate['produceNational'] = '베트남';
                    }*/

                    $eworkUpdate = [];
                    if(empty($eworkData['writeDt']) || '0000-00-00' == $eworkData['writeDt'] ) $eworkUpdate['writeDt']=date('Y-m-d'); //작성일 체크
                    if(empty($eworkData['requestDt']) || '0000-00-00' == $eworkData['requestDt'] ) $eworkUpdate['requestDt']=date('Y-m-d'); //의뢰일 체크

                    if( !empty($eworkUpdate) ){
                        DBUtil2::update(ImsDBName::EWORK, $eworkUpdate, new SearchVo('sno=?', $eworkData['sno']));
                    }

                    //제품 정보 업데이트
                    if( !empty($prdUpdate) ){
                        DBUtil2::update(ImsDBName::PRODUCT, $prdUpdate, new SearchVo('sno=?', $prd['sno']));
                    }

                    //생산데이터 생성
                    DBUtil2::insert(ImsDBName::PRODUCTION, $productionData);

                    //확정 생산가 수량 업데이트
                    DBUtil2::update(ImsDBName::ESTIMATE, ['estimateCount'=>$prd['prdExQty']], new SearchVo('sno=?', $prd['prdCostConfirmSno']));

                    //발주일자 등록
                    DBUtil2::update(ImsDBName::PROJECT, [
                        'msOrderDt' => 'now()'
                    ], new SearchVo('sno=?', $prd['projectSno']));

                    ImsScheduleUtil::setScheduleCompleteDt($projectData['sno'],'productionOrder','now()'); //신 발주일자

                }
            }

            ImsScheduleUtil::setProjectScheduleStatus($projectData['sno']);

            return null;
        }
    }

    /**
     * 기획서 체크
     * @param $projectData
     * @param $addData
     * @param $fileData
     * @return string|null
     */
    public static function checkPlan($projectData, $addData, $fileData){
        //신규만 확인.
        if( !in_array($projectData['projectType'], ImsService::PROJECT_CHECK_STEP)) return null;
        if( 'p' !== $projectData['planConfirm'] ){
            return '기획서가 승인되지 않았습니다.';
        }else{
            return null;
        }
    }

    /**
     * 제안서 체크
     * @param $projectData
     * @param $addData
     * @param $fileData
     * @return string|null
     */
    public static function checkProposal($projectData, $addData, $fileData){
        //신규만 확인.
        if( !in_array($projectData['projectType'], ImsService::PROJECT_CHECK_STEP)) return null;

        if( 'p' !== $projectData['proposalConfirm'] ){
            return '제안서가 승인되지 않았습니다.';
        }else{
            return null;
        }
    }

    /**
     * 고객 제안 확정 완료일 체크
     * @param $projectData
     * @param $addData
     * @param $fileData
     * @return string|null
     */
    public static function checkProposalConfirm($projectData, $addData, $fileData){
        //신규만 확인.
        if( !in_array($projectData['projectType'], ImsService::PROJECT_CHECK_STEP)) return null;
        $rsltArray = [];

        //고객서 제안서 확정예정 완료일
        if( empty($addData['custProposalConfirm']['completeDt']) || '0000-00-00' == $addData['custProposalConfirm']['completeDt'] ){
            $rsltArray[] = '고객사 제안서 확정 완료일';
        }
        if( count($rsltArray) > 0 ){
            return implode(', ', $rsltArray).' 은(는) 필수 입니다.';
        }else{
            return null;
        }
    }


    public static function stepCheck41($projectData, $addData, $fileData){
        $rsltArray = [];
        //신규만 확인.
        if( !in_array($projectData['projectType'], ImsService::PROJECT_CHECK_STEP)) return null;

        $checkList = [
            'cost'
        ];
        //샘플 관련 완료일
        foreach($checkList as $chekItem){
            if( empty($addData[$chekItem]['expectedDt']) || '0000-00-00' == $addData[$chekItem]['expectedDt'] ){
                $rsltArray[] = ImsCodeMap::PROJECT_ADD_INFO[$chekItem]['name'];
            }
        }
        $checkList = [
            'sampleOrder','sampleIn','sampleOut','sampleReview'
        ];
        //샘플 관련 완료일
        foreach($checkList as $chekItem){
            if( empty($addData[$chekItem]['completeDt']) || '0000-00-00' == $addData[$chekItem]['completeDt'] ){
                $rsltArray[] = ImsCodeMap::PROJECT_ADD_INFO[$chekItem]['name'];
            }
        }

        if( count($rsltArray) > 0 ){
            return implode(', ', $rsltArray).' 은(는) 필수 입니다.';
        }else{
            return null;
        }
    }

    public static function stepCheck50($projectData, $addData, $fileData){
        $rsltArray = [];

        //신규만 확인.
        if( !in_array($projectData['projectType'], ImsService::PROJECT_CHECK_STEP)) return null;

        $checkList = [
            'custSampleConfirm'
        ];

        //고객서 샘플 확정예정 완료일
        foreach($checkList as $chekItem){
            if( empty($addData[$chekItem]['completeDt']) || '0000-00-00' == $addData[$chekItem]['completeDt'] ){
                $rsltArray[] = ImsCodeMap::PROJECT_ADD_INFO[$chekItem]['name'];
            }
        }

        if( count($rsltArray) > 0 ){
            return implode(', ', $rsltArray).' 완료일은 필수 입니다.';
        }else{
            return null;
        }
    }

    public static function stepCheck60($projectData, $addData, $fileData){
        $rsltArray = [];

        //고객 발주일
        $checkList = ['custOrder'];
        foreach($checkList as $chekItem){
            if( empty($addData[$chekItem]['completeDt']) || '0000-00-00' == $addData[$chekItem]['completeDt'] ){
                $rsltArray[] = ImsCodeMap::PROJECT_ADD_INFO[$chekItem]['name'];
            }
        }

        if( count($rsltArray) > 0 ){
            return implode(', ', $rsltArray).' 완료일은 필수 입니다.';
        }else{
            return null;
        }
    }


}


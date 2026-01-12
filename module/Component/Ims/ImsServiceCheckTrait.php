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

/**
 * IMS 상태 체크 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceCheckTrait {

    /**
     * 생산 상태 체크 (생산 완료는 모두 완료상태여야함)
     * @param $prd
     * @param $statusList
     * @param $project
     * @throws \Exception
     */
    public function checkStatusProduction($prd, &$statusList, $project){
        $searchVo = new SearchVo('sno=?', $prd['sno']);
        $productionList = DBUtil2::getListBySearchVo(ImsDBName::PRODUCTION, new SearchVo("styleSno=?",$prd['sno']));
        $type = 'production';

        $updateField = 'productionStatus';
        if(!empty($productionList)){
            $complete = true;
            $process = false;
            //PRODUCE_STATUS
            foreach($productionList as $production){
                if(99 == $production['produceStatus']){ //생산완료
                    $complete &= true;
                    $process |= true;
                }else if($production['produceStatus'] >= 10){ //진행
                    $complete &= false;
                    $process |= true;
                }else{
                    $complete &= false;
                }
            }
            /*if( SlCommonUtil::isDevId() ){
                gd_debug($production['sno'] . ' : ' . $complete );
            }*/
            if( !empty($complete) ){
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>2],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= true;
            }else if( !empty($process) ){
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>1],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= false;
            }else{
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>0],$searchVo );
                $statusList[$type.'Complete'] &= false;
            }
        }else{
            $statusList[$type.'Process'] |= false;
            $statusList[$type.'Complete'] &= false;
            DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>0],$searchVo );
        }
    }

    /**
     * 가견적 . 생산가 상태 체크 및 업데이트
     * @param $prd
     * @param $statusList
     * @param $type
     * @param $updateField
     * @param $project
     * @throws \Exception
     */
    public function checkStatusAndPrdStatusUpdate($prd, &$statusList, $type, $updateField, $project){
        //최소 완료 상태 있음. ( 현재 요청 => 수기등으로. / 기존 처리완료는 선택할 수 있게 / 확정있으면 그걸로 확정 )
        $searchVo = new SearchVo('sno=?', $prd['sno']);
        $estimateList = DBUtil2::getListBySearchVo(ImsDBName::ESTIMATE, new SearchVo(" styleSno=?",$prd['sno']));
        if(!empty($estimateList)){
            $complete = false;
            $process = true; //empty가 아니면 진행 중.

            if('estimate' === $type && !empty($prd['estimateConfirmSno'])){
                $complete = true;
            }

            if('cost' === $type && !empty($prd['prdCostConfirmSno'])){
                $complete = true;
            }
            /*foreach($estimateList as $production){
                //0 => '미요청', 1 => '요청', 2 => '처리중', 3 => '처리완료', 4 => '처리불가', 5 => '확정', 6 => '반려',
                if(5 == $production['reqStatus']){
                    $complete |= true;
                    $process |= true;
                }else if(0 != $production['reqStatus'] && 5 !== $production['reqStatus'] ){
                    $process |= true;
                }
            }*/
            /*if( SlCommonUtil::isDevId() ){
                gd_debug($production['sno'] . ' : ' . $complete );
            }*/
            if( !empty($complete) ){
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>2],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= true;
            }else if( !empty($process) ){
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>1],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= false;
            }else{
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>0],$searchVo );
                $statusList[$type.'Complete'] &= false;
            }
        }else{
            //기성복 여부 체크....
            //$projectInfo = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $prd['projectSno']);
            if( 4 == $project['projectType'] && (-1 == $prd['estimateConfirmSno'] || $prd['estimateConfirmSno'] > 0 ) ) {
                $statusList[$type . 'Process'] |= true;
                $statusList[$type . 'Complete'] &= true;
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField => 2], $searchVo);
            }else{
                $statusList[$type.'Process'] |= false;
                $statusList[$type.'Complete'] &= false;
                DBUtil2::update(ImsDBName::PRODUCT, [$updateField=>0],$searchVo );
            }
        }
    }


    /**
     * 판매가 확정 진행상태 체크
     * @param $prd
     * @param $statusList
     * @return bool
     * @throws \Exception
     */
    public function checkStatusPrice($prd, &$statusList){
        if( 'p' === $prd['priceConfirm'] ){
            $statusList['priceProcess'] |= true;
            $statusList['priceComplete'] &= true;
        }else if( 'r' === $prd['priceConfirm'] || 'f' === $prd['priceConfirm'] ){
            $statusList['priceProcess'] |= true;
            $statusList['priceComplete'] &= false;
        }else{
            $statusList['priceProcess'] |= false;
            $statusList['priceComplete'] &= false;
        }
    }

    public function setSyncStatusPrice(&$project, $processStatus, $completeStatus, $etcData){
        return $this->setSyncStatusCommon('price', $project, $processStatus, $completeStatus, $etcData);
    }


    /**
     * 가견적 진행 상태 체크
     * @param $prd
     * @param $statusList
     * @param $project
     * @throws \Exception
     */
    public function checkStatusEstimate($prd, &$statusList, $project){
        $this->checkStatusAndPrdStatusUpdate($prd, $statusList, 'estimate', 'estimateStatus', $project);
    }

    /**
     * 생산가 확정 진행상태 체크
     * @param $prd
     * @param $statusList
     * @param $project
     * @throws \Exception
     */
    public function checkStatusCost($prd, &$statusList, $project){
        //최소 완료 상태 있음. ( 현재 요청 => 수기등으로. / 기존 처리완료는 선택할 수 있게 / 확정있으면 그걸로 확정 )
        $this->checkStatusAndPrdStatusUpdate($prd, $statusList, 'cost', 'prdCostStatus', $project);
    }

    /**
     * 퀄리티(원단) 체크
     * @param $prd
     * @param $statusList
     * @param $project
     * @throws \Exception
     */
    public function checkStatusFabric($prd, &$statusList, $project){
        //* IMS_FABRIC_STATUS : 0. 미확보 , 1. 확보중, 2. 확보완료, 3. 리오더, 4. 반려, 5. 사용안함
        //fabricStatus ... ImsCodeMap::IMS_FABRIC_STATUS
        $searchVo = new SearchVo('sno=?', $prd['sno']);

        $fabricList = DBUtil2::getListBySearchVo(ImsDBName::FABRIC, new SearchVo("styleSno=?",$prd['sno']));
        $type = 'fabric';
        if(!empty($fabricList)){
            $complete = true;
            $process = false;
            $national = 0;
            foreach($fabricList as $fabric){
                if( 2 == $fabric['fabricStatus'] ){
                    $national += gd_isset( array_flip(ImsCodeMap::FABRIC_BUY_TYPE)[$fabric['makeNational']],0);
                }

                if(2 == $fabric['fabricStatus'] || 3 == $fabric['fabricStatus'] || 5 == $fabric['fabricStatus'] ){ //완료/리오더/사용안함은 완료 상태이다.
                    $complete &= true;
                    $process |= true;
                }else if( !empty($fabric['fabricStatus']) ){
                    $process |= true;
                    $complete &= false;
                }else{
                    $complete &= false;
                }
            }

            if( !empty($complete) ){
                DBUtil2::update(ImsDBName::PRODUCT, ['fabricStatus'=>2, 'fabricNational'=>$national],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= true;
            }else if( !empty($process) ){
                DBUtil2::update(ImsDBName::PRODUCT, ['fabricStatus'=>1, 'fabricNational'=>$national],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= false;
            }else{
                DBUtil2::update(ImsDBName::PRODUCT, ['fabricStatus'=>0, 'fabricNational'=>$national],$searchVo );
                $statusList[$type.'Complete'] &= false;
            }
        }else{

            //리오더 프로젝트는 QB 무조건 해당 없음 처리
            if(in_array($project['projectType'], array_keys(ImsCodeMap::PROJECT_TYPE_R)) ){
                $prd['fabricPass'] = 'y'; //리오더라면 해당 없음 자동 처리
                $fabricPass = 'y';
            }else{
                $fabricPass = $prd['fabricPass']; //리오더가 아니라면 원래 있던 내용으로 업데이트
            }

            if( 'y' === $prd['fabricPass'] ){
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= true;
                $fabricStatus = 2;
                //DBUtil2::update(ImsDBName::PRODUCT, ['fabricStatus'=>2, 'fabricNational'=>0],$searchVo );
            }else{
                $statusList[$type.'Process'] |= false;
                $statusList[$type.'Complete'] &= false;
                $fabricStatus = 0;
                //DBUtil2::update(ImsDBName::PRODUCT, ['fabricStatus'=>0, 'fabricNational'=>0],$searchVo );
            }

            DBUtil2::update(ImsDBName::PRODUCT, ['fabricStatus'=>$fabricStatus, 'fabricNational'=>0, 'fabricPass'=>$fabricPass],$searchVo);
        }
    }

    /**
     * BT상태 체크
     * @param $prd
     * @param $statusList
     * @param $project
     * @throws \Exception
     */
    public function checkStatusBt($prd, &$statusList, $project){
        $searchVo = new SearchVo('sno=?', $prd['sno']);

        $fabricList = DBUtil2::getListBySearchVo(ImsDBName::FABRIC, new SearchVo("fabricStatus <> 5 and styleSno=?",$prd['sno'])); //사용하지 않는 원단은 제외.
        $type = 'bt';
        if(!empty($fabricList)){
            $complete = true;
            $process = false;
            //* IMS_BT_STATUS : 0. 미확정 , 1. 진행중, 2. 확정, 3. 리오더, 4. 반려
            foreach($fabricList as $bt){
                if(2 == $bt['btStatus'] || 3 == $bt['btStatus'] ){ //확정/리오더는 완료 상태이다.
                    $complete &= true;
                    $process |= true;
                }else if( !empty($bt['btStatus']) ){
                    $process |= true;
                    $complete &= false;
                }else{
                    $complete &= false;
                }
            }
            if( !empty($complete) ){
                DBUtil2::update(ImsDBName::PRODUCT, ['btStatus'=>2],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= true;
            }else if( !empty($process) ){
                DBUtil2::update(ImsDBName::PRODUCT, ['btStatus'=>1],$searchVo );
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= false;
            }else{
                DBUtil2::update(ImsDBName::PRODUCT, ['btStatus'=>0],$searchVo );
                $statusList[$type.'Complete'] &= false;
            }
        }else{
            //리오더 프로젝트는 QB 무조건 해당 없음 처리
            if(in_array($project['projectType'], array_keys(ImsCodeMap::PROJECT_TYPE_R)) ){
                $prd['fabricPass'] = 'y'; //리오더라면 해당 없음 자동 처리
                $fabricPass = 'y';
            }else{
                $fabricPass = $prd['fabricPass']; //리오더가 아니라면 원래 있던 내용으로 업데이트
            }

            if( 'y' === $prd['fabricPass'] ){
                $statusList[$type.'Process'] |= true;
                $statusList[$type.'Complete'] &= true;
                $btStatus = 2;
                //DBUtil2::update(ImsDBName::PRODUCT, ['btStatus'=>2],$searchVo );
            }else{
                $statusList[$type.'Process'] |= false;
                $statusList[$type.'Complete'] &= false;
                $btStatus = 0;
                //DBUtil2::update(ImsDBName::PRODUCT, ['btStatus'=>0],$searchVo );
            }
            DBUtil2::update(ImsDBName::PRODUCT, ['btStatus'=>$btStatus, 'fabricPass'=>$fabricPass],$searchVo );
        }
    }


    /**
     * 작지상태 체크
     * @param $prd
     * @param $statusList
     * @param $project
     * @throws \Exception
     */
    public function checkStatusWork($prd, &$statusList, $project){
        //최소 완료 상태 있음. ( 현재 요청 => 수기등으로. / 기존 처리완료는 선택할 수 있게 / 확정있으면 그걸로 확정 )
        $searchVo = new SearchVo('sno=?', $prd['sno']);

        $ework = DBUtil2::getOne(ImsDBName::EWORK, "styleSno", $prd['sno']);
        if(empty($ework)){
            $complete = false;
            $process = false;
            //구버전으로 체크? 구버전이 문제네? 아님 마이그해야됨.
        }else{
            $complete = true;
            $process = false;
            $checkList1 = ['main'];
            $eworkApprovalCheckList=[];
            //일반
            foreach($checkList1 as $check){
                $eworkApprovalCheckList[] = $ework[$check.'Approval'];
            }
            foreach($eworkApprovalCheckList as $check){
                if('p' === $check){
                    $complete &= true;
                    $process |= true;
                    $statusList['workComplete'] &= true;
                    $statusList['workProcess'] |= true;
                }else if('n' !== $check){
                    $complete &= false;
                    $process |= true;
                    $statusList['workProcess'] |= true;
                    $statusList['workComplete'] &= false;
                }else{
                    //최종확인
                    /*if(strLen($ework['fileMain'])>35){
                        $complete &= false;
                        $process |= true;
                        $statusList['workProcess'] |= true;
                        $statusList['workComplete'] &= false;
                    }else{

                    }*/
                    $complete &= false;
                    $statusList['workComplete'] &= false;
                }
            }
        }

        if( !empty($complete) ){
            DBUtil2::update(ImsDBName::PRODUCT, ['workStatus'=>2],$searchVo );
        }else if( !empty($process) ){
            DBUtil2::update(ImsDBName::PRODUCT, ['workStatus'=>1],$searchVo );
        }else{
            DBUtil2::update(ImsDBName::PRODUCT, ['workStatus'=>0],$searchVo );
        }
    }

    //-- 상태 처리

    public function setSyncStatusCommon($type, &$project, $processStatus, $completeStatus, $etcData){
        if( $completeStatus ){
            $rslt = 2;
        }else{
            if( $processStatus ){
                $rslt = 1;
            }else{
                $rslt = 0;
            }
        }

        /*if( 'price' == $type ){
            gd_debug($type . ' => '. $rslt);
            gd_debug($completeStatus);
            gd_debug($processStatus);
        }*/

        return [
            $type.'Status' => $rslt
        ];
    }

    /**
     * 가견적 진행상태 업데이트
     * @param $project
     * @param $processStatus
     * @param $completeStatus
     * @param $etcData
     * @return int[]|string[]
     */
    public function setSyncStatusEstimate(&$project, $processStatus, $completeStatus, $etcData){
        return $this->setSyncStatusCommon('estimate', $project, $processStatus, $completeStatus, $etcData);
    }

    /**
     * 생산가 확정 후 처리
     * @param $project
     * @param $processStatus
     * @param $completeStatus
     * @param $etcData
     * @return int[]|string[]
     */
    public function setSyncStatusCost($project, $processStatus, $completeStatus, $etcData){
        return $this->setSyncStatusCommon('cost', $project, $processStatus, $completeStatus, $etcData);
    }

    /**
     * BT 처리
     * @param $project
     * @param $processStatus
     * @param $completeStatus
     * @param $etcData
     * @return int[]
     */
    public function setSyncStatusBt($project, $processStatus, $completeStatus, $etcData){
        return $this->setSyncStatusCommon('bt', $project, $processStatus, $completeStatus, $etcData);
    }

    /**
     * 작지 상태 처리
     * @param $project
     * @param $processStatus
     * @param $completeStatus
     * @param $etcData
     * @return int[]
     */
    public function setSyncStatusWork($project, $processStatus, $completeStatus, $etcData){
        return $this->setSyncStatusCommon('work', $project, $processStatus, $completeStatus, $etcData);
    }

    /**
     * 생산 상태 처리
     * @param $project
     * @param $processStatus
     * @param $completeStatus
     * @param $etcData
     * @return int[]
     */
    public function setSyncStatusProduction($project, $processStatus, $completeStatus, $etcData){
        return $this->setSyncStatusCommon('production', $project, $processStatus, $completeStatus, $etcData);
    }

    /**
     * 원단 완료 상태 처리
     * IMS_FABRIC_STATUS : 0. 미확보 , 1. 확보중, 2. 확보완료, 3. 리오더, 4. 반려 ?
     * 반려 > 리오더 > 상태
     * @param $project
     * @param $processStatus
     * @param $completeStatus
     * @param $etcData
     * @return int[]|string[]
     */
    public function setSyncStatusFabric($project, $processStatus, $completeStatus, $etcData){
        $rsltMap = [];

        //원단 리오더 상태 저장시에는 변경 없음.
        if( $completeStatus  ){
            $rslt = 2; //확보완료
        }else{
            if( $processStatus ){
                $rslt = 1; //확보중 (반려도 포함)
            }else{
                $rslt = 0; //미확보
            }
        }
        $rsltMap['fabricNational'] = SlCommonUtil::sumUnique($etcData['fabricNational']);
        $rsltMap['fabricStatus'] = $rslt;

        return $rsltMap;
    }


}
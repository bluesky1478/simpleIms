<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Member\Manager;
use Component\Member\Member;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 리스트 조건 설정 Trait
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceConditionTrait {

    /**
     * IMS 25 공통 검색 조건 (검색 속도 개선)
     * @param $condition
     * @param $searchVo
     * @return mixed
     */
    public function setIms25CommonCondition($condition,SearchVo $searchVo){
        //SitelabLogger::logger2(__METHOD__, $condition);
        //KEY, KEYWORD 검색 인터셉트
        $interceptField = [
            'sales.managerNm' => ['db' => 'es_manager sales', 'join' => 'sales.sno = prj.salesManagerSno'],
            'desg.managerNm'  => ['db' => 'es_manager desg' , 'join' => 'desg.sno = prj.designManagerSno'],
            'prd.styleCode'   => ['db' => ImsDBName::PRODUCT.' prd' , 'join' => 'prd.projectSno = prj.sno'],
            'prd.productName' => ['db' => ImsDBName::PRODUCT.' prd' , 'join' => 'prd.projectSno = prj.sno'],
        ];
        $interceptKey = array_keys($interceptField);

        $multiKey = [];
        foreach($condition['multiKey'] as $each){
            $key = "REPLACE(".$each['key'].",' ','')";
            $keyword = str_replace(' ','',$each['keyword']);
            if(in_array($each['key'],$interceptKey) && !empty($keyword) ){
                $searchVo->setWhere("EXISTS (SELECT 1 FROM {$interceptField[$each['key']]['db']} WHERE {$interceptField[$each['key']]['join']} AND {$key} like '%{$keyword}%')");
            }else{
                //일반
                $multiKey[] = $each;
            }
        }
        $condition['multiKey'] = $multiKey;

        //담당 매니저 검색 OR designManagerSno = {$condition['searchManager']}
        if(!empty($condition['searchManager'])){
            if( 'salesTbc' === $condition['searchManager'] ){
                $searchVo->setWhere("prj.salesManagerSno = ''");
            }else if( 'designTbc' === $condition['searchManager'] ){
                $searchVo->setWhere("prj.designManagerSno = ''");
            }else{
                $searchVo->setWhere(
                    "(prj.salesManagerSno = {$condition['searchManager']} 
                       OR prj.designManagerSno = {$condition['searchManager']}
                       OR EXISTS (
                       SELECT 1 FROM sl_imsProjectManager pm 
                        WHERE pm.projectSno = prj.sno 
                          AND pm.managerSno = {$condition['searchManager']}
                          AND pm.scheduleStatus = '1' 
                    ))"
                );
            }
        }


        //부모 업종 검색
        if( !empty($condition['parentBusiCateSno']) ){
            $searchVo->setWhere('pBiz.sno=?');
            $searchVo->setWhereValue($condition['parentBusiCateSno']);
        }
        //업종 검색
        if( !empty($condition['busiCateSno']) ){
            $searchVo->setWhere('biz.sno=?');
            $searchVo->setWhereValue($condition['busiCateSno']);
        }
        //고객 상태 검색
        $this->setInCondition($condition, $searchVo, 'customerStatus', 'cust.customerStatus');
        
        //목표 매출 년도
        if( !empty($condition['targetSalesYear']) ){
            $searchVo->setWhere('ext.targetSalesYear=?');
            $searchVo->setWhereValue('20'.$condition['targetSalesYear']);
        }
        

        //SitelabLogger::logger2(__METHOD__, $condition);
        //SitelabLogger::logger2(__METHOD__, $searchVo);
        return $this->setCommonCondition($condition, $searchVo);
    }

    /**
     * 공통 검색 조건
     * @param $condition
     * @param $searchVo
     * @return mixed
     */
    public function setCommonCondition($condition, $searchVo){
        //프로젝트 SNO 검색
        if( !empty($condition['projectSno']) ){
            $searchVo->setWhere('prj.sno=?');
            $searchVo->setWhereValue($condition['projectSno']);
        }

        //번호검색
        if( !empty($condition['sno']) ){
            $searchVo->setWhere('a.sno=?');
            $searchVo->setWhereValue($condition['sno']);
        }
        //고객 번호검색
        if( !empty($condition['customerSno']) ){
            $searchVo->setWhere('cust.sno=?');
            $searchVo->setWhereValue($condition['customerSno']);
        }

        //고객 단위 폐쇄몰
        if( 'all' !== $condition['chkCustUse3pl'] && !empty($condition['chkCustUse3pl'])){
            $searchVo->setWhere("cust.use3pl=?");
            $searchVo->setWhereValue($condition['chkCustUse3pl']);
        }
        //고객 단위 3PL
        if( 'all' !== $condition['chkCustUseMall'] && !empty($condition['chkCustUseMall'])){
            $searchVo->setWhere("cust.useMall=?");
            $searchVo->setWhereValue($condition['chkCustUseMall']);
        }
        //고객 단위 매출 발생
        if( 'y' === $condition['chkContract'] ){
            $searchVo->setWhere("cust.customerPrice > 0");
            $searchVo->setWhere("cust.customerPrice <> cust.customerRtwPrice");
            //FIXME : 나중에 수정 매출있음 조건 시 기성복 매출 제외
        }else if( 'n' === $condition['chkContract'] ){
            $searchVo->setWhere("0 >= cust.customerPrice");
        }

        //스타일로 검색
        if( !empty($condition['styleSno']) ){
            $searchVo->setWhere('a.styleSno=?');
            $searchVo->setWhereValue($condition['styleSno']);
        }
        //Fabric 검색
        if( !empty($condition['fabricSno']) ){
            $searchVo->setWhere('a.fabricSno=?');
            $searchVo->setWhereValue($condition['fabricSno']);
        }
        //검색 Tool
        if( !empty($condition['key']) && !empty($condition['keyword']) ){
            if( 'prd.styleCode' === $condition['key'] ){
                $keyword=str_replace(' ','',$condition['keyword']);
                $searchVo->setWhere(" replace(prd.styleCode,' ','') LIKE '%{$keyword}%' ");
            }else if( 'prd.styleSno' === $condition['key'] ){
                $keyword=str_replace(' ','',$condition['keyword']);
                $searchVo->setWhere(" replace(prd.sno,' ','') LIKE '%{$keyword}%' ");
            }else if( 'prj.sno' === $condition['key'] ){
                $keyword=str_replace(' ','',$condition['keyword']);
                $searchVo->setWhere("prj.sno='{$keyword}' ");
            }else{
                $searchVo->setWhere(DBUtil2::bind($condition['key'], DBUtil2::BOTH_LIKE));
                $searchVo->setWhereValue($condition['keyword']);
            }
        }

        if( !empty($condition['key2']) && !empty($condition['keyword2']) ){
            if( 'prd.styleCode' === $condition['key2'] ){
                $keyword=str_replace(' ','',$condition['keyword2']);
                $searchVo->setWhere(" replace(prd.styleCode,' ','') LIKE '%{$keyword}%' ");
            }else if( 'prd.styleSno' === $condition['key'] ){
                $keyword=str_replace(' ','',$condition['keyword']);
                $searchVo->setWhere(" replace(prd.sno,' ','') LIKE '%{$keyword}%' ");
            }else if( 'prj.sno' === $condition['key'] ){
                $keyword=str_replace(' ','',$condition['keyword']);
                $searchVo->setWhere("prj.sno='{$keyword}' ");
            }else{
                $searchVo->setWhere(DBUtil2::bind($condition['key2'], DBUtil2::BOTH_LIKE));
                $searchVo->setWhereValue($condition['keyword2']);
            }
        }

        //멀티 검색
        if( !empty($condition['multiKey']) ){
            $whereConditionList = [];
            foreach( $condition['multiKey'] as $keyIndex => $keyCondition ){
                if( 'prj.sno'  === $keyCondition['key'] ) {
                    $key = 'prj.sno';
                }else{
                    $key = "REPLACE(".$keyCondition['key'].",' ','')";
                }

                $keyword = str_replace(' ','',$keyCondition['keyword']);
                if(!empty($keyword)){
                    if( 'OR' != $condition['multiCondition'] ){
                        if( 'prj.sno'  === $key ){
                            $searchVo->setWhere('prj.sno=?');
                        }else{
                            $searchVo->setWhere(DBUtil2::bind($key, DBUtil2::BOTH_LIKE));
                        }
                        $searchVo->setWhereValue($keyword);
                    }else{
                        if( 'prj.sno'  === $key ){
                            $whereConditionList[] = " ( prj.sno = '{$keyword}' ) ";
                        }else{
                            $whereConditionList[] = " ( {$key} like '%{$keyword}%' ) ";
                        }
                    }
                }
            }
            if( 'OR' == $condition['multiCondition'] ){
                if(count($whereConditionList)>0){
                    $searchVo->setWhere(implode(' OR ', $whereConditionList));
                }
            }
        }

        //연도
        if( !empty($condition['year']) ) {
            $searchVo->setWhere(DBUtil2::bind('prj.projectYear', DBUtil2::BOTH_LIKE));
            $searchVo->setWhereValue($condition['year']);
        }
        //시즌
        if( !empty($condition['season']) ) {
            $searchVo->setWhere(DBUtil2::bind('prj.projectSeason', DBUtil2::BOTH_LIKE));
            $searchVo->setWhereValue($condition['season']);
        }
        //프로젝트 연도
        if( !empty($condition['projectYear']) ) {
            $searchVo->setWhere(DBUtil2::bind('prj.projectYear', DBUtil2::BOTH_LIKE));
            $searchVo->setWhereValue($condition['projectYear']);
        }
        //프로젝트 시즌
        if( !empty($condition['projectSeason']) ) {
            $searchVo->setWhere(DBUtil2::bind('prj.projectSeason', DBUtil2::BOTH_LIKE));
            $searchVo->setWhereValue($condition['projectSeason']);
        }

        //요청 상태
        if(!empty($condition['status'])){
            $requestStatusMap = [
                '1' => 'a.reqStatus in (1,2,3) ', //전체 진행건
                '2' => 'a.reqStatus in (1,2) ',   //요청 + 처리중
                '3' => 'a.reqStatus in (3) ',     //처리완료
                '4' => 'a.reqStatus in (4) ',     //처리불가
                '5' => 'a.reqStatus in (6) ',     //반려
                '6' => 'a.reqStatus in (5) ',     //확정
            ];
            if(!empty(($condition['status']))){
                $searchVo->setWhere($requestStatusMap[$condition['status']]);
            }
        }
        //생산 진행 상태
        if(!empty($condition['productionStatus'])){
            $requestStatusMap = [
                '1' => 'a.produceStatus = 0 ',    //생산준비
                '2' => 'a.produceStatus = 10 ',   //생산스케쥴입력
                '3' => 'a.produceStatus = 20 ',   //생산스케쥴확정대기
                '4' => 'a.produceStatus = 30 ',   //생산스케쥴관리
                '5' => 'a.produceStatus = 99 ',   //생산완료
            ];
            $searchVo->setWhere($requestStatusMap[$condition['productionStatus']]);
        }

        //패킹여부
        if(!empty($condition['packingYn'])){
            $searchVo->setWhere('prj.packingYn=?');
            $searchVo->setWhereValue($condition['packingYn']);
        }
        //회계반영여부
        if(!empty($condition['isBookRegistered'])){
            $searchVo->setWhere('prj.isBookRegistered=?');
            $searchVo->setWhereValue($condition['isBookRegistered']);
        }
        //3PL여부
        if(!empty($condition['use3pl'])){
            $searchVo->setWhere('prj.use3pl=?');
            $searchVo->setWhereValue($condition['use3pl']);
        }
        //폐쇄몰여부
        if(!empty($condition['useMall'])){
            $searchVo->setWhere('prj.useMall=?');
            $searchVo->setWhereValue($condition['useMall']);
        }
        //폐쇄몰
        if('true' == $condition['chkUseMall']){
            $searchVo->setWhere("prj.useMall='y'");
        }
        //3PL
        if('true' == $condition['chkUse3pl']){
            $searchVo->setWhere("prj.use3pl='y'");
        }
        //분류패킹
        if('true' == $condition['chkPackingYn']){
            $searchVo->setWhere("prj.packingYn='y'");
        }
        //직접납품
        if('true' == $condition['chkDirectDeliveryYn']){
            $searchVo->setWhere("prj.directDeliveryYn='y'");
        }


        //기간검색
        if(!empty($condition['searchDateType'])){
            if( !empty($condition['startDt']) ){
                $searchVo->setWhere($condition['searchDateType'].' >= ?');
                $searchVo->setWhereValue( $condition['startDt'] );
            }
            if( !empty($condition['endDt']) ){
                $searchVo->setWhere($condition['searchDateType'].' <= ?');
                $searchVo->setWhereValue( $condition['endDt'].' 23:59:59' );
            }
        }


        //프로젝트 타입 검색
        if( 'all' !== $condition['projectType'] && isset($condition['projectType']) ){
            $searchVo->setWhere('prj.projectType = ?');
            $searchVo->setWhereValue( $condition['projectType']);
        }

        //프로젝트 타입 검색
        //if( !empty($condition['projectTypeChk'])  && 'all' !== $condition['projectTypeChk'][0]  ){
        $this->setInCondition($condition, $searchVo, 'projectTypeChk', 'prj.projectType');
        //}

        //생산 진행 상태
        //if( !empty($condition['productionChk'])  && 'all' !== $condition['productionChk'][0]  ){
        $this->setInCondition($condition, $searchVo, 'productionChk', 'prj.productionStatus');
        //}

        //프로젝트 진행상태
        //if( !empty($condition['orderProgressChk'])  && 'all' !== $condition['orderProgressChk'][0]  ){
        $this->setInCondition($condition, $searchVo, 'orderProgressChk', 'prj.projectStatus');
        //}

        //제외
        $this->setInCondition($condition, $searchVo, 'excludeStatus', 'prj.projectStatus',DBUtil2::NOT_IN);

        /**
         * 생산처 검색 (일반 리스트)
         */
        if(!empty($condition['factoryProduceCompanySno'])){
            $searchVo->setWhere('prd.produceCompanySno=?');
            $searchVo->setWhereValue($condition['factoryProduceCompanySno']);
        }

        /**
         * 생산처 검색
         */
        if(!empty($condition['produceCompanySno'])){
            $searchVo->setWhere('a.produceCompanySno=?');
            $searchVo->setWhereValue($condition['produceCompanySno']);
        }
        if(!empty($condition['prjListCompanySno'])){
            $searchVo->setWhere('prj.produceCompanySno=?');
            $searchVo->setWhereValue($condition['prjListCompanySno']);
        }

        //생산처(의뢰처)
        if(!empty($condition['reqFactory'])){
            $searchVo->setWhere('a.reqFactory=?');
            $searchVo->setWhereValue($condition['reqFactory']);
        }

        //데드라인
        if(!empty($condition['deadlineYn'])){
            if( 'y' === $condition['deadlineYn']){
                $searchVo->setWhere("( a.completeDeadLineDt <> '0000-00-00' and a.completeDeadLineDt is not null )");
            }else{
                $searchVo->setWhere("( a.completeDeadLineDt = '0000-00-00' or a.completeDeadLineDt is null )"); //미입력
            }
        }

        //생산리스트에서 사용. ( 납기 상태-> 촉박, 지연 등.. )
        if(!empty($condition['deliveryStatus'])){
            //양호 검색
            switch ($condition['deliveryStatus']){
                case 1 :
                    $searchVo->setWhere(" 6 > datediff(a.msDeliveryDt, a.deliveryExpectedDt) and datediff(a.msDeliveryDt, a.deliveryExpectedDt) >= 0 ");
                    break;
                case 2 :
                    $searchVo->setWhere(" 0 > datediff(a.msDeliveryDt, a.deliveryExpectedDt) ");
                    break;
                case 3 :
                    $searchVo->setWhere(" datediff(a.msDeliveryDt, a.deliveryExpectedDt) >= 6 "); //양호
                    break;
            }
        }

        //기성복 제외
        if( 'true' == $condition['isExcludeRtw'] ){
            $searchVo->setWhere('prj.projectType <> ?');
            $searchVo->setWhereValue(4);
        }
        
        //다음 시즌 발주건 제외
        if( 'true' == $condition['isExcludeNextSeason'] ){
            $searchVo->setWhere("nextSeason = 0");
        }

        //지연건 조회
        if( 'true' == $condition['isDelay'] ){
            $now = date('Y-m-d');
            $delayCheckList = [];
            foreach( ImsCodeMap::PRODUCTION_STEP as $stepEach ){
                $stepName = 'a.'.$stepEach;
                $delayCheckList[] = "( 
                    {$stepName}ExpectedDt <> '0000-00-00'
                    and {$stepName}ExpectedDt is not null
                    and {$stepName}Memo = ''
                    and '{$now}' > {$stepName}ExpectedDt 
                    and ( '0000-00-00' = {$stepName}CompleteDt or {$stepName}CompleteDt is null ) 
                    and ( {$stepName}Memo is null or {$stepName}Memo2 = '' )
                )";
            }
            $delayCheckSql = implode(' OR ',$delayCheckList);
            $searchVo->setWhere('('. $delayCheckSql . ')');
        }

        //지연건 조회2 (최초 예정일 대비)
        if( 'true' == $condition['isDelayFirst'] ){
            $now = date('Y-m-d');
            $delayCheckList = [];
            foreach( ImsCodeMap::PRODUCTION_STEP as $stepEach ){
                //SELECT DATE_ADD(REPLACE(JSON_EXTRACT(firstData, '$.schedule.wash.ConfirmExpectedDt'),'"',''), INTERVAL 4 DAY) as data
                //FROM sl_imsProduction
                $stepName = 'a.'.$stepEach;
                $delayCheckList[] = "( 
                    {$stepName}ExpectedDt <> '0000-00-00'
                    and {$stepName}ExpectedDt is not null
                    and {$stepName}Memo = ''
                    and {$stepName}ExpectedDt > DATE_ADD(REPLACE(JSON_EXTRACT(firstData, '$.schedule.{$stepEach}.ConfirmExpectedDt'),'\"',''), INTERVAL 4 DAY) 
                    and ( '0000-00-00' = {$stepName}CompleteDt or {$stepName}CompleteDt is null ) 
                    and ( {$stepName}Memo is null or {$stepName}Memo2 = '' )
                )";



            }
            $delayCheckSql = implode(' OR ',$delayCheckList);
            $searchVo->setWhere('('. $delayCheckSql . ')');
        }

        //예정 디자이너
        if( !empty($condition['extDesigner']) ){
            if( 'designTbc' === $condition['extDesigner'] ){
                $replaceSql='REPLACE(REPLACE(REPLACE(extDesigner, \'\\\\\', \'\'), \'\\"\', \'"\'), \'\"\', \'"\') = \''.''.'\'';
            }else{
                $replaceSql='REPLACE(REPLACE(REPLACE(extDesigner, \'\\\\\', \'\'), \'\\"\', \'"\'), \'\"\', \'"\') LIKE \'%'.stripslashes(json_encode($condition['extDesigner'])).'%\'';
                $replaceSql.=' AND REPLACE(REPLACE(REPLACE(extDesigner, \'\\\\\', \'\'), \'\\"\', \'"\'), \'\"\', \'"\') <> \''.''.'\'';
            }
            $searchVo->setWhere($replaceSql);
        }

        //처리완료 조회
        if( 'true' == $condition['isComplete'] ){
            $completeCheckList = [];
            foreach( ImsCodeMap::PRODUCTION_STEP as $stepEach ){
                $stepName = 'a.'.$stepEach;
                $completeCheckList[] = "{$stepName}Confirm = 'r'";
            }
            $completeCheckSql = implode(' OR ',$completeCheckList);
            $searchVo->setWhere('('. $completeCheckSql . ')');
        }

        //견적 타입
        if( !empty($condition['estimateType']) ) {
            $searchVo->setWhere('a.estimateType=?');
            $searchVo->setWhereValue($condition['estimateType']);
        }

        //영업 진행 상태
        $this->setInCondition($condition, $searchVo, 'salesStatusChk', 'ext.salesStatus');

        //사업계획
        if( 'all' !== $condition['bizPlanYn'] && !empty($condition['bizPlanYn']) ){
            $searchVo->setWhere('prj.bizPlanYn=?');
            $searchVo->setWhereValue($condition['bizPlanYn']);
        }
        
        //업무타입
        if( 'all' !== $condition['designWorkType'] && !empty($condition['designWorkType']) ){
            $searchVo->setWhere('ext.designWorkType=?');
            $searchVo->setWhereValue($condition['designWorkType']);
        }

        //진행타입
        if( 'all' !== $condition['bidType2'] && !empty($condition['bidType2']) ){
            $searchVo->setWhere('prj.bidType2=?');
            $searchVo->setWhereValue($condition['bidType2']);
        }


        //지연/미확정 (일단 디자인에서만 쓰지만 확장 가능)
        //1 .일정 지연
        if(in_array(1,$condition['delayStatus'])){
            //구 코드
            //$searchVo->setWhere(" ( '' = added.alterText or alterText is null )  and  '0000-00-00' = added.completeDt and added.expectedDt != '0000-00-00' and CURDATE() > added.expectedDt");
            $now = date('Y-m-d');
            $orList = [];
            foreach(ImsCodeMap::PROJECT_MAIN_SCHEDULE_LIST as $scKey => $scName){
                $compareKey = ucfirst($scKey);
                $orList[] = " ( ifnull(ex{$compareKey},'0000-00-00') and '{$now}' > ex{$compareKey}  and ( '0000-00-00' = ifnull(cp{$compareKey},'0000-00-00') and '' = ifnull(tx{$compareKey},''))       ) ";
            }
            $orCondition = implode(' OR ',$orList);
            $searchVo->setWhere("({$orCondition})");
        }
        //2. 생산가 미확정
        if(in_array(2,$condition['delayStatus'])){
            $searchVo->setWhere("prj.costStatus <> 2");
        }
        //3. 판매가 미확정
        if(in_array(3,$condition['delayStatus'])){
            $searchVo->setWhere("prj.priceStatus <> 2");
        }
        //4. 아소트 미확정
        if(in_array(4,$condition['delayStatus'])){
            $searchVo->setWhere("prj.assortApproval <> 'p'");
        }
        //5 .일정 지연 (디자인)
        if(in_array(8,$condition['delayStatus'])){
            //구 코드
            //$searchVo->setWhere(" ( '' = added.alterText or alterText is null )  and  '0000-00-00' = added.completeDt and added.expectedDt != '0000-00-00' and CURDATE() > added.expectedDt");
            $now = date('Y-m-d');
            $orList = [];
            foreach(ImsCodeMap::PROJECT_DESIGN_SCHEDULE_LIST as $scKey => $scName){
                $compareKey = ucfirst($scKey);
                $orList[] = " ( ifnull(ex{$compareKey},'0000-00-00') and '{$now}' > ex{$compareKey}  and ( '0000-00-00' = ifnull(cp{$compareKey},'0000-00-00') and '' = ifnull(tx{$compareKey},''))       ) ";
            }
            $orCondition = implode(' OR ',$orList);
            $searchVo->setWhere("({$orCondition})");
        }

        //신규/리오더/기성 구분 (신규)
        $projectType = [];
        if(in_array('new',$condition['orderType'])){
            //$searchVo->setWhere("prj.projectType in (0,2,5,6)");
            $projectType[]=0;
            $projectType[]=2;
            $projectType[]=5;
            $projectType[]=6;
        }
        //신규/리오더/기성 구분 (리오더)
        if(in_array('reorder',$condition['orderType'])){
            $projectType[]=1;
            $projectType[]=3;
            $projectType[]=7;
        }
        //신규/리오더/기성 구분 (기성복)
        if(in_array('rtw',$condition['orderType'])){
            $projectType[]=4;
        }
        if(count($projectType)>0){
            $prjTypeCondition = implode(',',$projectType);
            $searchVo->setWhere("prj.projectType in ( {$prjTypeCondition} ) ");
        }


        //생산가 확정 미확정
        if( 'y' === $condition['costStatus'] ){
            $searchVo->setWhere("prd.prdCostStatus=2");
        }else if( 'n' === $condition['costStatus'] ){
            $searchVo->setWhere('prd.prdCostStatus<>2');
        }
        //판매가 확정 미확정
        if( 'y' === $condition['priceStatus'] ){
            $searchVo->setWhere("prd.priceConfirm='p'");
        }else if( 'n' === $condition['priceStatus'] ){
            $searchVo->setWhere("prd.priceConfirm<>'p'");
        }

        //아소트 확정 미확정
        if( 'y' === $condition['assortStatus'] ){
            $searchVo->setWhere("prj.assortApproval='p'");
        }else if( 'n' === $condition['assortStatus'] ){
            $searchVo->setWhere("prj.assortApproval<>'p'");
        }
        //작지 확정 미확정
        if( 'y' === $condition['workStatus'] ){
            $searchVo->setWhere("prd.workStatus=2");
        }else if( 'n' === $condition['workStatus'] ){
            $searchVo->setWhere("prd.workStatus<>2");
        }

        //담당 디자이너
        if( 'all' !== $condition['designManager'] && !empty($condition['designManager'] ) ){
            $searchVo->setWhere('design.sno=?');
            $searchVo->setWhereValue($condition['designManager']);
        }

        //영업 미지정건
        if( isset($condition['designManagerSno']) && 'all' !== $condition['salesManagerSno'] && '' !== $condition['salesManagerSno']  ){
            $searchVo->setWhere('prj.salesManagerSno=?');
            $searchVo->setWhereValue($condition['salesManagerSno']);
        }
        //디자인 미지정건
        if( isset($condition['designManagerSno'])  && 'all' !== $condition['designManagerSno'] && '' !== $condition['designManagerSno']  ){
            $searchVo->setWhere('prj.designManagerSno=?');
            $searchVo->setWhereValue($condition['designManagerSno']);
        }

        /*SitelabLogger::logger2(__METHOD__, '검색 조건 확인');
        SitelabLogger::logger2(__METHOD__, $searchVo);*/

        return $searchVo;
    }

    /**
     * In Condition 설정
     * @param $condition
     * @param $searchVo
     * @param $fieldName
     * @param $dbFieldName
     * @param string $inType
     */
    public function setInCondition($condition, $searchVo, $fieldName, $dbFieldName, $inType = DBUtil2::IN){
        $inData = $condition[$fieldName];
        if( !is_array($inData) ){
            $inData = str_replace('[','',$inData);
            $inData = str_replace(']','',$inData);
            $inData = explode(',',$inData);
        }
        if( !empty($inData) && 'all' !== $inData[0] && '' !== $inData[0] ){
            $searchVo->setWhere(DBUtil2::bind($dbFieldName, $inType, count($inData) ));
            $searchVo->setWhereValueArray($inData);
        }
    }

}
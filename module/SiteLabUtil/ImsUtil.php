<?php
namespace SiteLabUtil;

use Component\Database\DBTableField;
use Component\Ims\ImsDBName;
use Component\Imsv2\ImsProjectService;
use Component\Imsv2\ImsScheduleConfig;
use Component\Imsv2\ImsScheduleUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 *
 * 프로젝트 데이터 종합 갱신
 * 코멘트 수 집계
 * 프로젝트 상태 갱신
 * 상품 집계 정보 반환
 * 프로젝트 추가 담당자 갱신 ( 완료되지 않은 예정일 추가 담당자 정제 갱신 )
 * 업종 맵 반환
 * 업종 1차 2차 쪼개서 반환
 * 기본 JSON 값 설정
 * 발송 이력 기록
 * 고객, 프로젝트 매출가 갱신 (5분 마다 갱신)
 * 코멘트 리스트 데이터 전달 (변환까지 한다. )
 * TM 리스트 데이터 전달
 *
 * Class ImsUtil
 * @package SiteLabUtil
 */
class ImsUtil {

    /**
     * 프로젝트 데이터 종합 갱신
     * @param $projectSno
     * @throws \Exception
     */
    public static function refreshProject($projectSno){
        $prj = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);
        $prd = DBUtil2::getList(ImsDBName::PRODUCT, 'projectSno', $projectSno, 'sort');
        $prjExt = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $projectSno);
        $addGoods = DBUtil2::getList(ImsDBName::ADDED_B_S, 'projectSno', $projectSno);

        $managerSearchVo = new SearchVo("scmNo=1 and employeeFl='y' and departmentCd not in ('', '02001011', '02001006','02001009','02001008') and isDelete=?",'n');
        $manager = DBUtil2::getListBySearchVo(DB_MANAGER, $managerSearchVo);
        $managerMap = SlCommonUtil::arrayAppKey($manager,'sno');

        //추가 담당자 설정
        $addManagerScheduleMap = ImsUtil::refreshProjectAddManager($projectSno, $prjExt);

        //스케쥴 코멘트 수 집계
        $commentCountInfo = ImsUtil::getProjectCommentCount($projectSno);

        //스타일 처리 
        $styleName = $prd[0]['productName']; //대표
        $styleCount = count($prd);
        if($styleCount > 1){
            $styleDpCount = $styleCount-1;
            $styleName = $prd[0]['productName'] . " 외 {$styleDpCount}건" ;//그 외 상품 갯수
        }

        $prdTotalInfo = ImsUtil::getProductTotalInfo($prd, $addGoods, $prj);

        //등록자 반 정규화 / 집계 데이터 업데이트
        DBUtil2::update(ImsDBName::PROJECT, [
            'regManagerName'  => gd_isset($managerMap[$prj['regManagerSno']]['managerNm'],''), //등록자
            'lastManagerName' => gd_isset($managerMap[$prj['lastManagerSno']]['managerNm'],''),//수정자
            'salesManagerName' => gd_isset($managerMap[$prj['salesManagerSno']]['managerNm'],''),//영업
            'designManagerName' => gd_isset($managerMap[$prj['designManagerSno']]['managerNm'],''),//디자인
        ], new SearchVo('sno=?', $projectSno));

        //추가 담당자 / 스타일 집계 업데이트 (TODO : 판관비 추가 - 부가금액)
        $extUpdateData = [
            'salesStyleName'  => gd_isset($styleName,'-'),
            'salesStyleCount' => $styleCount,
            'commentCount'  => json_encode($commentCountInfo),
            'addManager'  => json_encode($addManagerScheduleMap),
            'extPriceStatus' => $prdTotalInfo['extPriceStatus'],
            'extCostStatus' => $prdTotalInfo['extCostStatus'],
            'extPrice' => $prdTotalInfo['extPrice'],
            'extCost' => $prdTotalInfo['extCost'],
        ];

        //추정 매출이 입력되면 (상품별 현재가 입력시)
        if(!empty($prdTotalInfo['extAmount'])){
            $extUpdateData['extAmount'] = $prdTotalInfo['extAmount'];
        }

        DBUtil2::update(ImsDBName::PROJECT_EXT, $extUpdateData, new SearchVo('projectSno=?', $projectSno));

        //TODO 생산정보 갱신
    }

    /**
     * 고객 데이터 갱신
     * @param $customerSno
     * @throws \Exception
     */
    public static function refreshCustomer($customerSno){
        $customer = DBUtil2::getOne(ImsDBName::CUSTOMER, 'customerSno', $prj['customerSno']);
        $industryMap = ImsUtil::getIndustryMap();
        //고객 정보 집계
        /**
         * [ TODO 고객 상태 ==> 아래 기본으로 처리하고 운영서버 반영 후 맞춰 나간다. ]
         * 0 신규 : 신규(초도) - 새로운 프로젝트 등록시 신규(발주없음) or 재입찰(발주있음)로 셋팅
         * 1 재입찰 : 수기     - 새로운 프로젝트 등록시 신규(발주없음) or 재입찰(발주있음)로 셋팅
         * 2 계약중 : 자동 - 발주건 있음 + 현재 상태가 수기 상태가 아님 ( 재입찰, 보류, 이탈, 유찰 )
         * 10 보류 : 수기 - 초도는 마지막 프로젝트가 보류인 경우.
         * 11 이탈 : 수기 - 이탈 처리된 고객
         * 12 유찰 : 수기 - 유찰 처리된 고객
         */
        DBUtil2::update(ImsDBName::CUSTOMER,[
            'industry' => gd_isset($industryMap[$customer['busiCateSno']],'-') //업종개인
        ], new SearchVo('sno=?',$customerSno));

        //고객 집계 금액 갱신 (연도별)
        ImsUtil::refreshSalesPrice($customerSno);
    }

    /**
     * 프로젝트 상태 갱신
     * @param $projectSno
     * @param $reqClass
     * @return false
     * @throws \Exception
     */
    public static function setSyncStatus($projectSno, $reqClass){

        if (empty($projectSno)) return false;

        $imsService = SlLoader::cLoad('ims', 'imsService');

        //여기서 부터 상태 변경 시작
        $project = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $projectSno);

        $checkFieldList = [
            'estimate', 'cost', 'fabric', 'bt' , 'production', 'price', 'work'
        ]; //견적, 생산가, 원단, 비티, 생산, 판매가, 작지
        $statusList = [];

        //기본값 셋팅
        foreach ($checkFieldList as $checkField) {
            $statusList[$checkField . 'Process'] = false;
            $statusList[$checkField . 'Complete'] = true;
            //Ex) estimateProcess = false; //진행 상태는 하나라도 True 이면 진행 중 표기
            //Ex) estimateComplete = true; //완료 상태는 모두 True 이어야 완료 포기
        }

        //상품 불러오기
        $productList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl='n' and projectSno", $projectSno);

        //체크 함수 기본 Prefix ( checkStatus ... , setSyncStatus ... )
        if(!empty($productList)){
            //상품별 진행상태 체크
            $etcData = []; //기타 데이터
            foreach ($productList as $prd) {
                //위 checkFieldList에서 설정checkStatus한 항목 체크
                foreach ($checkFieldList as $checkField) {
                    $fncName = 'checkStatus' . ucfirst($checkField);
                    if( 'price' === $checkField ){
                        //gd_debug( $checkField );
                        //gd_debug( $prd['sno'] );
                        //gd_debug( $prd['priceConfirm'] );
                        //gd_debug($fncName);
                    }
                    $imsService->$fncName($prd, $statusList, $project); //각 상태 체크!
                }
                $etcData['fabricNational'][] = $prd['fabricNational'];//상품의 원단 제조국.
            }
            //결과 저장
            $updateDataList = [];
            foreach ($checkFieldList as $checkField) {
                $fncName = "setSyncStatus" . ucfirst($checkField);
                /*gd_debug( $fncName );
                gd_debug( $statusList[$checkField . 'Process'] );
                gd_debug( $statusList[$checkField . 'Complete'] );*/
                $updateData = $imsService->$fncName($project, $statusList[$checkField . 'Process'], $statusList[$checkField . 'Complete'], $etcData);
                $updateDataList = array_merge($updateDataList, $updateData);
            }

            DBUtil2::update(ImsDBName::PROJECT, $updateDataList, new SearchVo('sno=?', $projectSno));
        }else{
            //스타일 없음. 모든상태 초기화
            DBUtil2::update(ImsDBName::PROJECT, [
                'fabricStatus' => '',
                'btStatus' => '',
                'estimateStatus' => '',
                'priceStatus' => '',
                'costStatus' => '',
                'workStatus' => '',
                'productionStatus' => '',

                'mainApproval' => '',
                'markApproval' => '',
                'specApproval' => '',
                'careApproval' => '',
                'materialApproval' => '',
                'packingApproval' => '',
                'batekApproval' => '',
                // 'fabricPass' => $fabricPass, //TODO 확인 필요
            ], new SearchVo('sno=?', $projectSno));
        }
        //SitelabLogger::logger("[ Status Sync ProjectSno : {$projectSno} ({$reqClass}) ] ");
        //SitelabLogger::logger($updateDataList);

        //스케쥴 연동 처리
        ImsScheduleUtil::setProjectScheduleStatus($projectSno);
        
    }


    /**
     * 상품 집계 정보 반환
     *
     * status : 0.추정 1.타겟 2.견적 3.확정
     *
     * @param $prd
     * @param $addGoods
     * @param $project
     * @return array
     */
    public static function getProductTotalInfo($prd, $addGoods, $project){
        $extPriceStatus = 0;
        $extCostStatus = 0;
        $extPrice = 0;
        $extCost = 0;
        $extAmount = 0;

        if( '2' === $project['priceStatus'] ){
            $extPriceStatus = 3;
        }
        if( '2' === $project['costStatus'] ){
            $extCostStatus = 3;
        }

        foreach($prd as $prdEachKey => $prdEach){
            //현재가 계산 (추정매출)
            $extAmount += $prdEach['prdExQty'] * $prdEach['currentPrice'];

            //미청구 수량은 제외 (실제 청구할 것만 계산)
            $priceQty = $prdEach['prdExQty']-$prdEach['msQty'];

            //판매가 상태/종합금액 정의
            if(3 === $extPriceStatus){
                $extPrice += ($prdEach['salePrice']*$priceQty); //확정
            }else if( $prdEach['salePrice'] > 0 ){ //가격은 입력되어 있나?
                $extPriceStatus = 2;
                $extPrice += ($prdEach['salePrice']*$priceQty); //견적
            }else if( $prdEach['targetPrice'] > 0 ){ //타겟 가격은 입력되어 있나?
                $extPriceStatus = 1;
                $extPrice += ($prdEach['targetPrice']*$priceQty); //타겟
            }

            //생산가 상태/종합금액 정의
            if(3 === $extCostStatus){
                $extCost += ($prdEach['prdCost']*$prdEach['prdExQty']); //확정
            }else if( $prdEach['prdCost'] > 0 ){ //가격은 입력되어 있나?
                $extCostStatus = 2;
                $extCost += ($prdEach['prdCost']*$prdEach['prdExQty']); //견적
            }else if( $prdEach['targetPrice'] > 0 ){ //타겟 가격은 입력되어 있나?
                $extCostStatus = 1;
                $extCost += ($prdEach['targetPrdCost']*$prdEach['prdExQty']); //타겟
            }
        }

        //부가 금액 처리
        foreach($addGoods as $eachAddGoodsKey => $eachAddGoods){
            $extPrice += ($eachAddGoods['addedSaleAmount']*$eachAddGoods['addedQty']);
            $extCost += $eachAddGoods['addedBuyAmount']*$eachAddGoods['addedQty'];
        }

        return [
            'extPriceStatus' => $extPriceStatus,
            'extCostStatus' => $extCostStatus,
            'extPrice' => $extPrice,
            'extCost' => $extCost,
            'extAmount' => $extAmount, //추정매출
        ];
    }

    /**
     * 코멘트 수 집계
     * @param $projectSno
     * @return array
     */
    public static function getProjectCommentCount($projectSno){
        $inString = "'" . implode("','", array_keys(ImsScheduleUtil::getScheduleMap()) ) . "'";
        $sql = "select commentDiv, count(1) as cnt from sl_imsComment where projectSno={$projectSno} and commentDiv in ({$inString}) group by commentDiv";
        $commentData = DBUtil2::runSelect($sql);
        return SlCommonUtil::arrayAppKeyValue($commentData,'commentDiv','cnt');
    }

    /**
     * 프로젝트 추가 담당자 갱신 ( 완료되지 않은 예정일 추가 담당자 정제 갱신 )
     * @param $projectSno
     * @param $prjExt
     * @return array
     * @throws \Exception
     */
    public static function refreshProjectAddManager($projectSno, $prjExt){
        DBUtil2::update(ImsDBName::PROJECT_MANAGER, ['scheduleStatus'=>'0'], new SearchVo('projectSno=?', $projectSno)); //전체 0처리
        $scheduleTargetKeyList = ImsProjectService::getExpectedSchedule($projectSno, $prjExt);
        if(count($scheduleTargetKeyList) > 0){
            $scheduleStatusUpdateVo = new SearchVo('projectSno=?', $projectSno);
            $scheduleStatusUpdateVo->setWhere(DBUtil2::bind('scheduleDiv', DBUtil2::IN, count($scheduleTargetKeyList) ));
            $scheduleStatusUpdateVo->setWhereValueArray( $scheduleTargetKeyList );
            DBUtil2::update(ImsDBName::PROJECT_MANAGER, ['scheduleStatus'=>'1'], $scheduleStatusUpdateVo); //참여중인 스케쥴 상태는 1처리
        }
        $projectAddManager = ImsProjectService::getProjectAddManagerList($projectSno, true);
        $addManagerScheduleMap = [];
        foreach($projectAddManager as $managerList){
            foreach($managerList as $manager){
                $addManagerScheduleMap[$manager['managerSno']]['managerNm'] = $manager['managerNm'];
                $addManagerScheduleMap[$manager['managerSno']]['schedule'][] = [
                    'name' => ImsScheduleConfig::SCHEDULE_LIST[$manager['scheduleDiv']]['name'],
                    'date' => $prjExt['ex'.ucfirst($manager['scheduleDiv'])],
                ];
            }
        }
        return $addManagerScheduleMap;
    }


    /**
     * 업종 맵 반환
     * @return array
     */
    public static function getIndustryMap(){
        $indusTryList = DBUtil2::getList(ImsDBName::BUSI_CATE, '1', '1');
        $indusTryParentMap = [];
        $indusTryMap = [];
        foreach($indusTryList as $each){
            if(empty($each['parentBusiCateSno'])){
                $indusTryParentMap[$each['sno']]=$each['cateName'];
                $indusTryMap[$each['sno']]=$each['cateName'];
            }
        }
        foreach($indusTryList as $each){
            if(!empty($each['parentBusiCateSno'])){
                $indusTryMap[$each['sno']]=$indusTryParentMap[$each['parentBusiCateSno']] . '>' .$each['cateName'];
            }
        }

        $indusTryMap[0] = '';

        return $indusTryMap;
    }

    /**
     * 업종 1차 2차 쪼개서 반환
     * @return array
     */
    public static function getIndustrySplitMap(){
        $rslt = [];
        $industry = ImsUtil::getIndustryMap();
        foreach($industry as $key => $each){
            $rslt[$key] = explode('>',$each);
            if(empty($rslt[$key][1])){
                $rslt[$key][1] = '';
            }
        }
        return $rslt;
    }


    /**
     * 기본 JSON 값 설정
     * @defrecated
     * @param $updateField
     * @param $value
     * @param $field
     * @param $projectSno
     * @return array
     * @throws \Exception
     */
    public static function updateSchedule($updateField, $value, $field, $projectSno) {
        DBUtil2::update(ImsDBName::PROJECT_ADD_INFO, [
            $updateField => $value,
        ], new SearchVo("fieldDiv='{$field}' and projectSno=?", $projectSno));
    }

    /**
     * 발송 이력 기록
     * @param $sendType
     * @param $projectSno
     * @param $receiverInfo
     */
    public static function saveSendHistory($sendType, $projectSno, $receiverInfo){
        $sendManagerSno = SlCommonUtil::getManagerSno();
        $receiverName = $receiverInfo['receiverName'];
        $receiverMail = $receiverInfo['receiverMail'];
        $subject = $receiverInfo['subject'];
        $contents = $receiverInfo['contents'];
        DBUtil2::insert(ImsDBName::SEND_HISTORY,[
            'sendType' => $sendType,
            'projectSno' => $projectSno,
            'sendManagerSno' => $sendManagerSno,
            'receiverName' => $receiverName,
            'receiverMail' => $receiverMail,
            'subject' => $subject,
            'contents' => $contents                
        ]);
    }

    /**
     * 고객, 프로젝트 매출가 갱신 (5분 마다 갱신)
     * 생산으로 넘어간 프로젝트
     *  - 프로젝트 매입, 매출 // - 스타일 매입 , - 스타일 청구 매출 (아소트별)
     * @param int $customerSno
     */
    public static function refreshSalesPrice($customerSno=0){
        //모든 상태 0으로 초기화 ( 전체 집계 아니라면 아래 과정 필요, 전체 집계시에는 리셋하지 말 것 )
        $customerWhere='';
        if(!empty($customerSno)){
            $customerWhere=' AND sno = '.$customerSno;
        }

        DBUtil2::runSql("update sl_imsCustomer set customerCost=0,customerPrice=0 where 1=1 {$customerWhere}");

        $searchVo = new SearchVo();
        $searchVo->setWhere('projectYear >= 23  and projectStatus in (90,91)'); //발주된 건 만.
        if(!empty($customerSno)){
            $searchVo->setWhere('customerSno=?'); //특정 고객 건만
            $searchVo->setWhereValue($customerSno); //특정 고객 건만
        }

        $projectList = DBUtil2::getListBySearchVo(ImsDBName::PROJECT, $searchVo);
        $updateCustomerList = [];

        $updateList = [];
        foreach($projectList as $project){
            $projectCost = 0;
            $projectPrice = 0;
            $addedPriceInfoList = DBUtil2::getList(ImsDBName::ADDED_B_S, 'projectSno', $project['sno']);
            $prdList = DBUtil2::getList(ImsDBName::PRODUCT, "delFl='n' and projectSno", $project['sno']);

            foreach($addedPriceInfoList as $priceInfo){
                $projectCost += ($priceInfo['addedBuyAmount']*$priceInfo['addedQty']);
                $projectPrice += ($priceInfo['addedSaleAmount']*$priceInfo['addedQty']);
            }
            foreach($prdList as $prd){
                //이거 좀 애매한데 ? ( 제작스량 만큼만 집계 해야하나 ? )
                $projectCost +=($prd['prdCost']*($prd['prdExQty']-$prd['msQty']));
                $projectPrice +=($prd['salePrice']*($prd['prdExQty']-$prd['msQty'])); //이게 적고. 마진 작겠지... (고민이네.. 우리가 만든 수량만..)
            }

            $updateList[] = "SELECT {$project['sno']} AS sno, {$projectCost} AS projectCost, {$projectPrice} AS projectPrice";

            $updateCustomerList[$project['customerSno']]['customerCost'] += $projectCost;
            $updateCustomerList[$project['customerSno']]['customerPrice'] += $projectPrice;
            $updateCustomerList[$project['customerSno']]['yearData'][$project['projectYear']]['customerCost'] += $projectCost;
            $updateCustomerList[$project['customerSno']]['yearData'][$project['projectYear']]['customerPrice'] += $projectPrice;
            $updateCustomerList[$project['customerSno']]['yearData'][$project['projectYear']]['customerRtwCost'] += 0;
            $updateCustomerList[$project['customerSno']]['yearData'][$project['projectYear']]['customerRtwPrice'] += 0;

            if( 4 == $project['projectType'] ) { //기성복 집계
                $updateCustomerList[$project['customerSno']]['customerRtwCost'] += $projectCost;
                $updateCustomerList[$project['customerSno']]['customerRtwPrice'] += $projectPrice;
                $updateCustomerList[$project['customerSno']]['yearData'][$project['projectYear']]['customerRtwCost'] += $projectCost;
                $updateCustomerList[$project['customerSno']]['yearData'][$project['projectYear']]['customerRtwPrice'] += $projectPrice;
            }
        }
        $sql = 'UPDATE sl_imsProject t JOIN ('.implode(' UNION ALL ', $updateList).') tmp ON t.sno = tmp.sno SET t.projectCost = tmp.projectCost ,  t.projectPrice = tmp.projectPrice';
        $rslt1 = DBUtil2::runSql($sql);
        //gd_debug('Rslt1 :' . $rslt1);

        $customerPriceUpdateList = [];
        foreach($updateCustomerList as $customerSno => $priceInfo){
            $customerYearPrice = json_encode($priceInfo['yearData']);
            $customerPriceUpdateList[] = "SELECT 
                {$customerSno} AS sno,
                '{$priceInfo['customerRtwCost']}' AS customerRtwCost, 
                '{$priceInfo['customerRtwPrice']}' AS customerRtwPrice,
                '{$priceInfo['customerCost']}' AS customerCost, 
                '{$priceInfo['customerPrice']}' AS customerPrice,
                '{$customerYearPrice}' AS customerYearPrice";
        }

        $sql = 'UPDATE sl_imsCustomer t JOIN ('.implode(' UNION ALL ', $customerPriceUpdateList).') tmp ON t.sno = tmp.sno SET 
        t.customerRtwCost = tmp.customerRtwCost, 
        t.customerRtwPrice = tmp.customerRtwPrice,
        t.customerCost = tmp.customerCost, 
        t.customerPrice = tmp.customerPrice, 
        t.customerYearPrice = tmp.customerYearPrice';
        $rslt2 = DBUtil2::runSql($sql);
        //gd_debug('Rslt2 :' .$rslt2);
        //gd_debug($sql);
        //SitelabLogger::logger2(__METHOD__, 'refreshSalesPrice : '.$rslt1.'/'.$rslt2);
    }

    /**
     * 코멘트 리스트 데이터 전달 (변환까지 한다. )
     * @param $params [ projectSnoList 필수 ]
     * @return mixed
     */
    public static function getCommentListData($params){
        $searchVo = new SearchVo();
        $searchVo->setWhere(DBUtil2::bind('projectSno', DBUtil2::IN, count($params['projectSnoList']) ));
        $searchVo->setWhereValueArray($params['projectSnoList']);
        $searchVo->setOrder('a.regDt desc');

        $commentListCountData = DBUtil2::getSimpleJoinList([
            'tableName' => ImsDBName::PROJECT_COMMENT,
            'field' => 'a.*',
        ],['b' => [DB_MANAGER, 'a.regManagerSno=b.sno','b.managerNm as regManagerName']], $searchVo);

        $rsltMap = [];
        foreach($commentListCountData as $comment){
            $rsltMap[$comment['projectSno']][$comment['commentDiv']][] = $comment;
        }
        return $rsltMap;
    }


    /**
     * TM 리스트 데이터 전달
     * @param $params
     * @return array
     */
    public static function getTmListData($params){
        $searchVo = new SearchVo();
        $searchVo->setWhere(DBUtil2::bind('d.sno', DBUtil2::IN, count($params['projectSnoList']) ));
        $searchVo->setWhereValueArray($params['projectSnoList']);
        $searchVo->setOrder('a.regDt desc');

        $tmList = DBUtil2::getSimpleJoinList([
            'tableName' => ImsDBName::SALES_CUSTOMER_CONTENTS,
            'field' => 'a.*',
        ],[
            'b' => [ImsDBName::SALES_CUSTOMER, 'a.salesSno=b.sno','b.contactName'],
            'c' => [ImsDBName::CUSTOMER, 'b.customerSno=c.sno','c.customerDiv'],
            'd' => [ImsDBName::PROJECT, 'c.sno=d.customerSno','d.sno as projectSno'],
            'e' => [DB_MANAGER, 'b.regManagerSno=e.sno','e.managerNm as regManagerName'],
        ], $searchVo);

        $rsltMap = [];
        foreach($tmList as $tmData){
            $rsltMap[$tmData['projectSno']][] = $tmData;
        }
        return $rsltMap;
    }


    /**
     * 프로젝트 리스트 다운로드
     * @param $request
     */
    public static function simpleExcelDownload($request){
        //데이터 가져오기, default 작업
        $service = SlLoader::cLoad('ims25','ims25ListService');
        //$aResult = $service->getIms25List('all', $request);
        $aResult = $service->getIms25List('style', $request);

        $aList = $aResult['list'];
        $aFlds = [
            'sno' => '번호',
            'customerName' => '고객명',
            'projectTypeKr' => '프로젝트타입',
            'assortApproval' => '아소트확정',
            'customerOrderConfirm' => '사양서확정',
            'prdYear' => '생산년도',
            'prdSeason' => '시즌',
            'productName' => '제품명',
            'styleCode' => '스타일 코드',
            'styleSno' => '스타일 넘버',
            'produceCompanyName' => '생산처',
            'prdFabricStatusKr' => '원단처리상태',
            'prdBtStatusKr' => 'BT처리상태',
            'prdCustomerDeliveryDt' => '고객납기',
            'prdMsDeliveryDt' => 'MS납기',
            'exProductionOrder' => '발주예정일',
            'cpProductionOrder' => '발주완료일',
            'prdPeriod' => '생산기간',
            'prdExQty' => '수량',
            'repFabric' => '대표원단',
            'prdMoq' => '생산 MOQ',
            'priceMoq' => '단가 MOQ',
            'priceConfirm' => '판매가승인',
            'salePrice' => '판매가',
            'totalCost' => '생산가',
            'margin' => '마진',
        ];

        $title = [];
        foreach($aFlds as $key => $val) $title[] = $val;

        //리스트의 데이터 정제
        foreach($aList as $key => $val) {
            $aList[$key]['sno'] = $key+1;
            $aList[$key]['assortApproval'] = $val['assortApproval'] === 'p' ? '확정' : ''; //아소트확정
            $aList[$key]['customerOrderConfirm'] = $val['customerOrderConfirm'] === 'p' ? '확정' : ''; //사양서확정
            if ($val['isReorder'] === 'y') {
                $aList[$key]['prdFabricStatusKr'] = $aList[$key]['prdBtStatusKr'] = '해당 없음'; //원단처리상태 and BT처리상태
            }
            $aList[$key]['prdExQty'] = number_format($val['prdExQty']); //수량
            $aList[$key]['repFabric'] = !empty($val['repFabric']) ? $val['repFabric'] : '미정'; //대표원단
            if (!empty($val['estimateData'])) {
                $aList[$key]['prdPeriod'] = !empty($val['prdPeriod']) ? $val['prdPeriod'] : '생산처 미입력'; //생산기간
                $aList[$key]['prdMoq'] = number_format($val['estimateData']['prdMoq']); //생산MOQ
                $aList[$key]['priceMoq'] = number_format($val['estimateData']['priceMoq']); //단가MOQ
            } else {
                $aList[$key]['prdPeriod'] = $aList[$key]['prdMoq'] = $aList[$key]['priceMoq'] = '미정';
            }
            if ((int)$val['salePrice'] > 0) {
                $aList[$key]['priceConfirm'] = $val['priceConfirm'] === 'p' ? '확정' : ''; //판매가 승인
                $aList[$key]['salePrice'] = number_format($val['salePrice']).'원'; //판매가
            } else {
                $aList[$key]['priceConfirm'] = $aList[$key]['salePrice'] = '확인중';
            }
            if (!empty($val['estimateData']) && ($val['prdCostConfirmSno'] > 0 || $val['estimateConfirmSno'] > 0)) { //생산가
                if ($val['estimateConfirmSno'] > 0 && $val['prdCostConfirmSno'] <= 0) $aList[$key]['totalCost'] = '(가)';
                else $aList[$key]['totalCost'] = '';
                $aList[$key]['totalCost'] .= number_format($val['estimateData']['totalCost']).'원';
            } else {
                $aList[$key]['totalCost'] = '선택 견적 없음';
            }
            $aList[$key]['margin'] = number_format($val['margin']).'%'; //마진
        }
        //엑셀파일 만들기
        foreach($aList as $val) {
            $contentsRows = [];
            foreach($aFlds as $key2 => $val2) {
                $contentsRows[] = ExcelCsvUtil::wrapTd($val[$key2]);
            }
            $contents[] = '<tr>'.implode('',$contentsRows).'</tr>';
        }
        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('프로젝트리스트', $title, implode('',$contents));
    }


    /**
     * DB 저장 (25년 version , imsService에서 분리, 후속 작업등은 없음 )
     * @param $table
     * @param $saveData
     * @param array|null $newOnlyDataArray
     * @param false $insertOnly
     * @return mixed
     * @throws \Exception
     */
    public static function save($table, $saveData, array $newOnlyDataArray = null, $insertOnly = false){
        $sno = $saveData['sno'];
        $unsetList = ['mode','sno','regDt','modDt','lastManagerSno','regManagerSno'];
        foreach($unsetList as $unsetField){
            unset($saveData[$unsetField]);
        }
        $beforeData = null;
        if( !$insertOnly ){
            if( !empty($sno) ){
                $beforeData = DBUtil2::getOne($table,'sno',$sno);
            }
        }
        if( !empty($beforeData) ){
            if( in_array('lastManagerSno', DBTableField::getTableKey($table)) ) $saveData['lastManagerSno'] = \Session::get('manager.sno');  //마지막 수정자 자동 지정
            //table.
            $dbFieldMap = SlCommonUtil::arrayAppKey(DBTableField::callTableFunction($table), 'val');
            $updateMsg = [];
            $refineSaveData = [];
            foreach($saveData as $eachSaveKey => $eachSaveValue){
                if(isset($dbFieldMap[$eachSaveKey])){
                    if( '0000-00-00' == $beforeData[$eachSaveKey] ) $beforeData[$eachSaveKey] = '';
                    if( empty($beforeData[$eachSaveKey]) ) $beforeData[$eachSaveKey] = '';
                    if( empty($eachSaveValue) ) $eachSaveValue = '';
                    if( !in_array($eachSaveKey,$unsetList) && $eachSaveValue != $beforeData[$eachSaveKey] ){
                        if( $dbFieldMap[$eachSaveKey]['json'] ){
                            $msg = $dbFieldMap[$eachSaveKey]['name'] . '이(가) 변경됨';
                        }else{
                            $msg = $dbFieldMap[$eachSaveKey]['name'] . ':' . $beforeData[$eachSaveKey] .' → '. $eachSaveValue;
                        }
                        $updateMsg[] = $msg;
                    }
                    $refineSaveData[$eachSaveKey] = $eachSaveValue;
                }
            }
            if(!empty($updateMsg)){
                ImsUtil::recordHistory('update', $table, $beforeData, $updateMsg);
            }
            DBUtil2::updateBySno($table, $refineSaveData, $sno);
        }else{
            if( !empty($newOnlyDataArray) ){
                foreach($newOnlyDataArray as $key => $value){
                    $saveData[$key] = $value;
                }
            }
            if( in_array('regManagerSno', DBTableField::getTableKey($table)) ) $saveData['regManagerSno'] = \Session::get('manager.sno');
            $sno = DBUtil2::insert($table, $saveData);
        }
        return $sno;
    }


    /**
     * 수정/삭제 이력 기록.
     * @param $type
     * @param $tableName
     * @param $recordData
     * @param $updateMsg
     */
    public static function recordHistory($type, $tableName, $recordData, $updateMsg){
        DBUtil2::insert( 'sl_ims'.ucfirst($type).'History', [
            'tableName' => $tableName,
            'tableSno' => $recordData['sno'],
            'contents' => json_encode($recordData),
            'comment' => json_encode($updateMsg, JSON_UNESCAPED_UNICODE),
            'regManagerSno' => \Session::get('manager.sno'),
        ]);
    }

}

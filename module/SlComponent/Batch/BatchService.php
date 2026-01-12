<?php
namespace SlComponent\Batch;

use Component\Claim\ReturnListService;
use Component\Erp\ErpCodeMap;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Util\MemberUtil;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;


/**
 * Class BatchService

 * 프로젝트 백업
 * 삼영 출고 이력 갱신
 * 생산 스케쥴 일정 체크 리셋 (매주)
 * 환율정보 업데이트
 * 출고내역 자동 등록 ( 15시부터 20시30분까지 30분마다 실행 )
 * 입고내역 자동 등록 (매 1시간)
 * 생산가 확정 시켰는데 확정 안된 건 처리
 * 재고 집계 (5분마다)
 * IMS 영업 매출 갱신 30분마다
 * 25/12/19 미팅.입찰예정일 완료일 체크

- 미사용건
지연 투두 리스트 체크 및 발송
지연 TO DO 리스트 메일 전달
프로젝트 스케쥴 알림
당일 or 지연 프로젝트 가져오기 ( sign 으로 분기 )
23년 하계 주문권유 메세지 발송 ( 4/30, 5/4, 5/7 ) 3회발송 @Deprecated  : 나중엔 또 사용할수도 있음.
 *
 *
 * @package SlComponent\Batch
 */
class BatchService {

    public function runAsiaMemberApp(){
        $list = DBUtil2::getList(DB_MEMBER, 'ex1', '아시아나에어포트');
        foreach($list as $each){
            if( 19963 != $each['memNo'] ){
                //DBUtil2::update(DB_MEMBER, ['appFl'=>$appFl], new SearchVo('memNo=?',$each['memNo']) );
                $sql = "update es_member set  appFl='y' ,  loginLimit = '{\"limitFlag\": \"n\",\"onLimitDt\": \"0000-00-00 00:00:00\",\"loginFailLog\": [],\"loginFailCount\": 0}' where memNo = {$each['memNo']}";
                DBUtil2::runSql($sql);
            }
        }
    }

    /**
     * 일일 배치 작업 (08:30)
     * @throws \Exception
     */
    public function runDailyBatch(){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $imsService->setTodo3plStoreAlarm(); //TO-DO 3PL 입고 알림 요청
        $imsService->setExchange(); //환율 갱신

        //이상한 수령자 번호 수정
        DBUtil2::runSql("update es_orderInfo set receiverCellPhone = concat('0',REPLACE(receiverCellPhone, '-', '')) where receiverCellPhone like '1%'");

        //결제가 된 것은 결제일자 넣기
        DBUtil2::runSql("update es_orderGoods set paymentDt = regDt where orderStatus = 'p1' and paymentDt = '00-00-00 00:00:00'");

        //원부자재 선적일 기준 패킹 리스트 요청 TO-DO LIST 등록
        $imsService->autoPackingTodo();

        //Project Backup
        $this->projectBackup();

        //생산 완료 알림 (7일전)
        $imsService->alarmProductionComplete();

        //삼영 출고 이력 갱신
        $this->setStockOutHistory();

        //생산가 확정 체크
        $this->runRefineImsSalePriceApproval();

        //생산가 자동 선택
        $manualService = SlLoader::cLoad('godo','manualService','sl');
        $manualService->refineCost();

        //아시아나 이력 갱신
        $service = SlLoader::cLoad('scm','ScmAsianaService');
        $refreshList = DBUtil2::runSelect("select distinct companyId from sl_asianaOrderHistory");
        foreach($refreshList as $data){
            $service->saveEmpAllHistory($data['companyId']);
        }

        //영업 현황 갱신 (TM이력)
        $imsService = SlLoader::cLoad('imsv2','ImsSalesService');
        $imsService->updateSalesCustomerStat();

        //입찰일자 종료일 자동 설정 (기간 지나면)
        //ImsScheduleUtil::setCpMeeting();

        SitelabLogger::logger2(__METHOD__, '===> ' . SlCommonUtil::getNow() . ' 일일배치 성공');
    }

    /**
     * 프로젝트 백업
     */
    public function projectBackup(){
        DBUtil2::runSql("drop table zzz_imsProject");
        DBUtil2::runSql("create table zzz_imsProject select * from sl_imsProject");
        DBUtil2::runSql("drop table zzz_imsProjectProduct");
        DBUtil2::runSql("create table zzz_imsProjectProduct select * from sl_imsProjectProduct");
        DBUtil2::runSql("drop table zzz_imsProjectExt");
        DBUtil2::runSql("create table zzz_imsProjectExt select * from sl_imsProjectExt");
        DBUtil2::runSql("drop table zzz_imsPrdMaterial");
        DBUtil2::runSql("create table zzz_imsPrdMaterial select * from sl_imsPrdMaterial");
        DBUtil2::runSql("drop table zzz_imsEwork");
        DBUtil2::runSql("create table zzz_imsEwork select * from sl_imsEwork");
        DBUtil2::runSql("drop table zzz_imsFile");
        DBUtil2::runSql("create table zzz_imsFile select * from sl_imsFile");
    }


    /**
     * 삼영 출고 이력 갱신
     * @throws \Exception
     */
    public function setStockOutHistory(){
        $prevDay = date('Ymd', strtotime('-10 day'));
        $now = date('Ymd', strtotime('-1 day'));
        DBUtil2::delete('sl_3plStockInOut', new SearchVo([" inOutType=2 and memo <> '분류배송' and inOutDate >= ?",'? >= inOutDate'],[$prevDay, $now]) ); //출고 데이터만 삭제하기
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->regSimple3plOutHistory($prevDay, $now);
        $service->update3plStock(); //재고 갱신
    }

    /**
     * 생산 스케쥴 일정 체크 리셋
     */
    public function runCheckSchedule(){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        //생산 관리 단계
        $list = DBUtil2::getList(ImsDBName::PRODUCTION, 'produceStatus' , 30);
        $params = [];
        $params['checkValue'] = 'n';
        foreach($list as $each){
            $params['checkSnoList'][] = $each['sno'];
        }
        $imsService->setScheduleCheck($params,'-1');
    }

    /**
     * 환율정보 업데이트
     */
    public function runExchangeUpdate(){
        $exchangeService = SlLoader::cLoad('api','ExchangeRateService','sl');
        $exchangeService->updateCurrentExchange();
    }

    /**
     * 출고내역 자동 등록
     * 15시부터 20시30분까지 30분마다 실행
     */
    public function runToday3plOutHistory(){
        $today = date('Ymd');
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->reg3plOutHistory($today, $today, 'wait');
    }

    /**
     * 입고내역 자동 등록 (매 1시간)
     */
    public function runInputHistory(){
        $start = date('Y-m-d', strtotime('-30 day'));
        $end = date('Y-m-d');
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->reg3plInHistory($start, $end);
    }

    /**
     * 지연 투두 리스트 체크 및 발송
     */
    public function runCheckTodoDelay(){

        //휴무 패스.
        if( SlCommonUtil::isHoliday()  ) return false;

        $now = date('Y-m-d');
        $managerList = [];
        foreach( ImsCodeMap::CODE_DEPT_INFO as $deptKey => $deptInfo){
            $deptManagerList = DBUtil2::getList(DB_MANAGER, " isDelete = 'n' and departmentCd", $deptKey);
            //하나어패럴 개별 추가

            foreach($deptManagerList as $manager){
                //예정일 지난 건
                $searchVo = new SearchVo("? > expectedDt and 'ready' = status and '0000-00-00' != expectedDt " , $now);

                //기타일경우 부서 등록 안되게 한다.
                if( '02001005' === $deptKey ){
                    $searchVo->setWhere('managerSno=?');
                    $searchVo->setWhereValue($manager['sno']);
                }else{
                    $searchVo->setWhere('(managerSno=? or managerSno=?)');
                    $searchVo->setWhereValue($manager['sno']);//개인
                    $searchVo->setWhereValue($deptKey);//부서
                }

                $delayResponseList = DBUtil2::getListBySearchVo(ImsDBName::TODO_RESPONSE, $searchVo);
                foreach($delayResponseList as $key => $each){
                    $sql = "select a.*, b.managerNm as reqManagerNm, c.projectNo, d.customerName  
                             from sl_imsTodoRequest a 
                             left outer join es_manager b on a.regManagerSno = b.sno 
                             left outer join sl_imsProject c on a.projectSno = c.sno
                             left outer join sl_imsCustomer d on c.customerSno = d.sno      
                             where a.sno = {$each['reqSno']}";

                    $requestDataList = DBUtil2::runSelect($sql);
                    $requestData = $requestDataList[0];
                    unset($requestData['modDt']);
                    unset($requestData['regDt']);
                    unset($requestData['sno']);

                    if( empty($requestData['reqManagerNm']) ) continue;

                    $mergeData = array_merge($each, $requestData);
                    $delayResponseList[$key] = $mergeData;
                }

                if(!empty($delayResponseList)){
                    $managerList[] = [
                        'sno' => $manager['sno'],
                        'name' => $manager['managerNm'],
                        'mail' => $manager['email'],
                        'delayList' => $delayResponseList,
                    ];
                }
            }
        }

        foreach($managerList as $delayInfo){
            $this->sendTodoDelayContents($delayInfo);
        }
    }

    /**
     * 지연 TO DO 리스트 메일 전달 
     * @param $delayInfo
     */
    public function sendTodoDelayContents($delayInfo){
        $deptContentList = [];
        $individualContentList = [];

        foreach($delayInfo['delayList'] as $delay){
            //gd_debug($delay);

            if( empty($delay['reqManagerNm']) ){
                $delay['reqManagerNm'] = '시스템';
                //SitelabLogger::logger2(__METHOD__, '시스템 발송건 로그 확인');
                //SitelabLogger::logger2(__METHOD__, $delay);
            }
            
            $projectInfo = $delay['customerName'].$delay['projectSno'];
            if( empty(trim($projectInfo)) ){
                $content =" * " . $delay['subject'] . '(~'.$delay['expectedDt'].'까지 '.$delay['reqManagerNm'].'님 요청) ';
            }else{
                $content =" * ({$delay['customerName']} <span style='color:red'>{$delay['projectSno']}</span>) " . $delay['subject'] . '(~'.$delay['expectedDt'].'까지 '.$delay['reqManagerNm'].'님 요청)';
            }

            if($delay['managerSno'] > 1000000 ){
                //부서
                $deptContentList[] = $content;
            }else{
                //개별
                $individualContentList[] = $content;
            }
        }

        $deptContent = implode('<br>', $deptContentList);
        $individualContent = implode('<br>', $individualContentList);

        $contentList[] = "<b>{$delayInfo['name']}님 지연건을 알려드립니다.</b>";

        if(!empty($deptContent)){
            $contentList[] = '<br><b>부서별 요청 지연 건</b>';
            $contentList[] = $deptContent;
        }
        if(!empty($individualContent)){
            $contentList[] = '<br><b>개인 요청 지연 건</b>';
            $contentList[] = $individualContent;
        }
        $contentList[] = '<br>완료가 된 건은 \'처리 완료\' 해주시기 바라며<br> 일정연기가 필요한 경우 요청자와 협의 하여 완료 예정일을 수정 바랍니다.<br>자세한 내용은 IMS에서 확인해주시기 바랍니다.';

        $contents = implode('<br>', $contentList);

        $now = date('Y/m/d');
        //gd_debug( $contents );
        //SiteLabMailUtil::sendSystemMail('IMS TO-DO LIST 지연건 알림 ('.$now.') ', $contents, $delayInfo['mail']);
        //SiteLabMailUtil::sendSystemMail('IMS TO-DO LIST 지연건 알림 ('.$now.') ', $contents, 'innover_dev@msinnover.com');
    }

    /**
     * 프로젝트 스케쥴 알림
     */
    public function runProjectSchedule(){

        //휴무 패스.
        if( SlCommonUtil::isHoliday()  ) return false;

        $now = date('Y/m/d');
        $contents = [];

        $checkStatus = [
            20,30,31,40,41,50,60
        ];
        $contents[] = '<b style="font-size:15px;">IMS 프로젝트 스케쥴 금일 완료 예정 건</b>';
        foreach($checkStatus as $status){
            $rslt = $this->getDelayProject($status);
            if(!empty($rslt)){
                $contents[] = '<br><b>&nbsp;&nbsp;* '.$rslt['projectInfo'].'</b>';
                $contents[] = $rslt['contents'].'<br>';
            }
        }
        $prjIndx = 1;
        $contents[] = '<br><br><b style="font-size:15px;">IMS 프로젝트 스케쥴 처리 지연 건</b>';
        foreach($checkStatus as $status){
            $rslt = $this->getDelayProject($status,'>');
            if(!empty($rslt)){
                $contents[] = '<br><b>&nbsp;&nbsp;* '.$rslt['projectInfo'].'</b>';
                $contents[] = $rslt['contents'].'<br>';
                $prjIndx++;
            }
        }
        
        $contents[] = '<br><br>이상 끝';
        
        SiteLabMailUtil::sendSystemMail('IMS 스케쥴 리포트 ('.$now.') ', implode('',$contents), implode(',', ImsCodeMap::PROJECT_SCHEDULE_ALARM_LIST));
    }

    /**
     * 당일 or 지연 프로젝트 가져오기 ( sign 으로 분기 )
     * @param $status
     * @param string $searchSign
     * @return array
     * @throws \ReflectionException
     */
    public function getDelayProject($status, $searchSign='='){
        $delayTypeMap = [
            '=' => '예정',
            '>' => '처리 지연',
        ];
        
        $today = date('Y-m-d');
        $imsService = SlLoader::cLoad('ims', 'imsService');

        $tableList = DBUtil2::setTableInfo([
            'a' =>[
                'data' => [ ImsDBName::PROJECT ]
                , 'field' => ['a.*']
            ],
            'b' =>['data' => [ ImsDBName::CUSTOMER , 'LEFT OUTER JOIN', 'a.customerSno = b.sno']
                , 'field' => ['b.customerName']
            ]
        ], false);
        $projectList = DBUtil2::getComplexList($tableList,new SearchVo('a.projectStatus=?',$status));
        $fncName = 'setList'.$status;

        $delayContents = '';
        $prjCnt = 1;

        $methods = SlCommonUtil::getMethodMap('\Component\Ims\ImsService');

        foreach($projectList as $projectKey => $project){
            $checkFieldList = [];
            $checkFieldMap = [];
            if( !empty($methods[$fncName]) ){
                $fieldList = $imsService->$fncName();
            }else{
                $fieldList = $imsService->setList();
            }

            foreach($fieldList['list'] as $fieldData){
                //예정일이 있는 것만!
                if(!empty($fieldData['split']) && strpos($fieldData['period'],'Expected') !== false ){
                    //gd_debug($fieldData['field'].':'.$fieldData['period']);
                    $checkFieldList[] = $fieldData['field'];
                    $checkFieldMap[$fieldData['field']] = $fieldData['title'];
                }
            }

            $searchVo = new SearchVo('projectSno=?',$project['sno']);
            $searchVo->setWhere(DBUtil2::bind('fieldDiv', DBUtil2::IN, count($checkFieldList) ));
            $searchVo->setWhereValueArray($checkFieldList);

            //초과냐. 당일이냐.
            $searchConditionField = "? {$searchSign} expectedDt";
            $searchVo->setWhere($searchConditionField);
            $searchVo->setWhere("'0000-00-00' <> expectedDt and '' <> expectedDt and expectedDt is not null");
            $searchVo->setWhere("('' = alterText or alterText is null)");
            $searchVo->setWhere("('0000-00-00' = completeDt or '' = expectedDt or expectedDt is null)");
            $searchVo->setWhereValue($today);

            $delayList = DBUtil2::getListBySearchVo(ImsDBName::PROJECT_ADD_INFO, $searchVo);

            $strList = [];
            foreach($delayList as $delayKey => $delayData){
                $strList[] = '<br>&nbsp;&nbsp; '.($delayKey+1).')';
                $strList[] = ' '. strip_tags($checkFieldMap[$delayData['fieldDiv']]).' '.$delayTypeMap[$searchSign];
                $strList[] = ' '. $delayData['expectedDt'].' 까지';
                $projectStatusKr = ImsCodeMap::PROJECT_STATUS[$project['projectStatus']];
                $delayContents = [
                    'projectInfo' => "{$project['customerName']} {$project['projectNo']} {$projectStatusKr} 단계",
                    'contents' => implode('', $strList),
                ];
                $prjCnt++;
            }
        }

        return $delayContents;
    }

    /**
     * 생산가 확정 시켰는데 확정 안된 건 처리
     * @throws \Exception
     */
    public function runRefineImsSalePriceApproval(){
        $imsService = SlLoader::cLoad('ims', 'imsService');
        $list = DBUtil2::getListBySearchVo(ImsDBName::PROJECT, new SearchVo('projectStatus >= ?', '20'));
        foreach($list as $each){
            $params['projectSno'] = $each['sno'];
            $params['approvalType'] = 'salePrice';
            $approvalData = $imsService->getApprovalData($params);

            if('accept' === $approvalData['approvalStatus'] && 2 != $each['priceStatus'] ){
                $completeDt = substr($approvalData['targetManagerList'][count($approvalData['targetManagerList'])-1]['completeDt'], 0, 10);
                DBUtil2::update(ImsDBName::PROJECT, [
                    'priceStatus' => 2,
                    'priceConfirm' => 'p',
                    'priceConfirmDt' => $completeDt
                ], new SearchVo('sno=?', $each['sno']));
                DBUtil2::runSql("update sl_imsProjectProduct set priceConfirm = 'p' , priceConfirmDt = '{$completeDt}' where projectSno = {$each['sno']}");
            }
        }
    }

    /**
     * 재고 집계 (5분마다)
     * http://innoverb2b.com/ajax/batch_job.php?mode=runRefineStockCnt
     */
    public function runRefineStockCnt(){
        $service = SlLoader::cLoad('godo','sopService','sl');
        $service->summarizeStock();
    }

    /**
     * IMS 영업 매출 갱신 30분마다
     * REFRESH_SALE_PRICE_30
     * http://innoverb2b.com/ajax/batch_job.php?mode=runRefreshSalesPrice
     */
    public function runRefreshSalesPrice(){
        ImsUtil::refreshSalesPrice();
    }

    /**
     * @Deprecated  : 나중엔 또 사용할수도 있음.
     * 23년 하계 주문권유 메세지 발송 ( 4/30, 5/4, 5/7 ) 3회발송
     */
    public function runTkeOrderAdvice($params){
        $sql = "
        select distinct a.memNm, a.cellPhone
            from es_member a 
            join sl_setMemberConfig b 
            on a.memNo = b.memNo 
            where b.memberType <> 2 
            and ex1 = 'TKE(티센크루프)'
            and a.memNo NOT IN (  1 , 4, 5469, 4991  )
            and a.memNo NOT IN (
            select distinct a.memNo 
            from es_member a
                     join sl_setMemberConfig b on a.memNo = b.memNo
                     join es_order c on a.memNo = c.memNo
                     join es_orderGoods d on c.orderNo = d.orderNo
            where b.memberType <> 2
              and ex1 = 'TKE(티센크루프)'
              and a.memNo NOT IN (  1 , 4, 5469, 4991  )
              and d.orderStatus = 'p3'
              and d.goodsNo in ('1000000328','1000000330') 
            )
        ";
        //sum( if ( d.goodsNo = '1000000328' , 1, 0)) as teeCnt,
        //sum( if ( d.goodsNo = '1000000330', 1, 0 )) as pantsCnt
        $list = DBUtil2::runSelect($sql);

        SitelabLogger::logger('주문 독려 알림톡 발송');
        SitelabLogger::logger(count($list));

        foreach($list as $each){
            $param['memNm'] = $each['memNm'];
            $param['prdName'] = '하계 근무복';
            $param['appPeriod'] = '5/8(월)';
            $param['targetShopUrl'] = 'tkeb2b.co.kr';
            $param['btnUrl'] = 'https://momotee.co.kr/callback/link_callback.php';
            SitelabLogger::logger(" {$each['memNm']} : {$each['cellPhone']} ");
            //SlKakaoUtil::send(19 , $each['cellPhone'],  $param);
        }
    }

}

<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Imsv2\ImsScheduleUtil;
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
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 생산가 견적 관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceProductionTrait {

    /**
     * 생산 기본 구조 반환
     * @param $params
     * @return array
     */
    public function getSchemaProductionByStyleSno($params){
        $product = SlCommonUtil::setDateBlank($this->getSimpleProductData($params['condition']['styleSno']));
        return $this->getSchemaProduction($product);
    }
    public function getSchemaProduction($productData){

        if(empty($productData['sizeOption'])){
            $productData['sizeOption'] = [];
        }
        if(empty($productData['typeOption'])){
            $productData['typeOption'] = [];
        }

        $result = DBTableField::getTableKeyAndBlankValue(ImsDBName::PRODUCTION);

        $result['projectSno'] = $productData['projectSno'];
        $result['customerSno'] = $productData['customerSno'];
        $result['styleSno'] = $productData['sno'];
        $result['customerName'] = $productData['customerName'];
        $result['styleFullName'] = $productData['styleFullName'];
        $result['styleCode'] = $productData['styleCode'];

        $result['sizeOption'] = $productData['sizeOption'];
        $result['typeOption'] = $productData['typeOption'];

        $result['sizeOptionQty'] = [];
        foreach( $productData['sizeOption'] as $size){
            if(!empty($productData['typeOption'])){
                foreach( $productData['typeOption'] as $type){
                    $result['sizeOptionQty'][$size.$type] = '';
                }
            }else{
                $result['sizeOptionQty'][$size] = '';
            }
        }

        $fileFieldList = array_merge(ImsCodeMap::PRODUCTION_STEP,['fileWork']);
        foreach( $fileFieldList as $field ){
            $result['fileList'][$field] = [
                'title' => '등록된 파일이 없습니다.',
                'memo' => '',
                'files' => [],
                'noRev' => $value['noRev']
            ];
        }

        //SitelabLogger::logger2(__METHOD__, '생산 기본구조 체크');
        //SitelabLogger::logger2(__METHOD__, $result);

        return $result;
    }

    /**
     * 요청사항 처리
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveProduction($params){
        $saveData = $params;
        //요청회차 처리.
        if( empty($saveData['sno']) ){
            //초기 요청
            DBTableField::checkRequired(ImsDBName::PRODUCTION, $params);
            //요청을 눌렀을 경우 진행중 처리.
            //$savePrdData['sno'] = $params['styleSno'];
            //$savePrdData['estimateStatus'] = array_flip(ImsCodeMap::IMS_PRD_PROC_STATUS)['진행'];
            //$this->save(ImsDBName::PRODUCT, $savePrdData);
        }else{
            $saveData['contents'] = json_encode($saveData['contents']);
            $this->checkUpdateSchedule($saveData['sno'], $saveData);
        }
        $saveData = DBTableField::parseJsonField(ImsDBName::PRODUCTION, $saveData, 'encode');

        //customerSno, projectSno는 외부 입력 없이 저장되도록 로직 추가.
        $productInfo = DBUtil2::getOne(ImsDBName::PRODUCT,'sno', $params['styleSno']);

        $saveData['projectSno'] = $productInfo['projectSno'];
        $saveData['customerSno'] = $productInfo['customerSno'];

        $sno = $this->save(ImsDBName::PRODUCTION, $saveData);

        $this->setAutoAccept($sno);

        $this->setSyncStatus($params['projectSno'], __METHOD__);
        return ['data'=> $sno,'msg'=>'저장 완료'];
    }

    /**
     * 도착, 검수, 납기 자동 승인
     * @param $sno
     * @throws \Exception
     */
    public function setAutoAccept($sno){
        //도착 검수 납기는 날짜가 들어오면 승인 처리 / 아니면 n 준비 처리.
        $checkSearchVo = new SearchVo('sno=?', $sno);
        $newData = DBUtil2::getOneBySearchVo(ImsDBName::PRODUCTION,$checkSearchVo);

        //일반 완료일 등록 시 승인완료.
        $checkList = [
            'arrival',
            'check',
            'delivery',
            'cutting',
            'sew',
        ];
        foreach($checkList as $check){
            if( ( !empty($newData[$check.'CompleteDt']) && '0000-00-00' != $newData[$check.'CompleteDt'] ) || !empty($newData[$check.'Memo2']) ){
                $setAcceptStatus = 'p';
            }else{
                $setAcceptStatus = 'n';
            }
            DBUtil2::update(ImsDBName::PRODUCTION, [
                $check.'Confirm' => $setAcceptStatus
            ],$checkSearchVo);
        }
        
        //파일 등록시 자동 승인완료 (Script에서 처리)
        /*if( 'p' !== $newData['shipConfirm'] ){
            $shipFileData = DBUtil2::getOneBySearchVo(ImsDBName::PROJECT_FILE, new SearchVo("fileDiv='fileShip' and eachSno=?",$sno));
            if(!empty($shipFileData)){
                $updateData['shipConfirm'] = 'p';
                if( empty($newData['shipCompleteDt']) ){
                    $updateData['shipCompleteDt'] = 'now()';
                }
                DBUtil2::update(ImsDBName::PRODUCTION, $updateData,$checkSearchVo);
            }
        }*/

    }

    /**
     * 생산 정보 가져오기.
     * @param $params
     * @return string
     */
    public function getProduction($params){
        if(empty($params['sno'])){
            $data = DBTableField::getTableBlankData('tableImsProduction'); //초기 데이터.
        }else{
            $data = $this->getListProduction([
                'condition' => ['sno' => $params['sno'] ]
            ])['list'][0];
        }
        return $data;
    }


    public function setConditionProduction($condition, $searchVo){
        //프로젝트 SNO 검색
        if( !empty($condition['scheduleCheck']) && 'all' !== $condition['scheduleCheck'] ){
            $searchVo->setWhere('a.scheduleCheck=?');
            $searchVo->setWhereValue($condition['scheduleCheck']);
        }
    }

    /**
     * 생산 리스트
     * @param $params
     * @return mixed
     */
    public function getListProduction($params){
        $searchVo = new SearchVo();
        $totalSearchVo = new SearchVo();

        //일정체크 옵션 .
        $this->setConditionProduction($params['condition'], $searchVo);
        $this->setConditionProduction($params['condition'], $totalSearchVo);
        $this->setCommonCondition($params['condition'], $searchVo); //Request쪽에 있음.
        $this->setCommonCondition($params['condition'], $totalSearchVo); //Request쪽에 있음.
        $this->setListSort($params['condition']['sort'], $searchVo);
        //$this->sql->setProductionListCondition($params['condition'], $searchVo);
        //$searchVo->setOrder('a.sno desc');
        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];
        $allData = DBUtil2::getComplexListWithPaging($this->sql->getProductionListTable(), $searchVo, $searchData, false, false);
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationProduction');
        //TODO List에서 파일 정보 추가.
        /*foreach($qbList as $key => $each){
            $each = $this->setFabricFileDefault($each); //파일 정보 추가.
            $each = $this->decorationFabric($each, $key, $mixData); //꾸미기
            $qbList[$key] = $each;
        }*/
        $pageEx = $allData['pageData']->getPage('#');

        $sqlWithoutLimit = 'select 
            count(distinct customerSno) as customerCnt
            , count(distinct projectSno) as projectCnt
            , count( case when 30 = a.produceStatus and datediff(msDeliveryDt, deliveryExpectedDt) >= 6 then 1 else null end ) as safeCnt
            , count( case when 30 = a.produceStatus and 0 > datediff(msDeliveryDt, deliveryExpectedDt)  then 1 else null end ) as delayCnt
            , count( case when 30 = a.produceStatus and 6 > datediff(msDeliveryDt, deliveryExpectedDt) and datediff(msDeliveryDt, deliveryExpectedDt) >= 0 then 1 else null end ) as warnCnt
        from ( '. $allData['queryWithoutPage'] .') a ';

        $summaryData = DBUtil2::runSelect($sqlWithoutLimit, $allData['bindData']);

        $allData['pageData']->customerTotal = $summaryData[0]['customerCnt'];
        $allData['pageData']->projectTotal = $summaryData[0]['projectCnt'];
        $allData['pageData']->safeCnt = $summaryData[0]['safeCnt'];
        $allData['pageData']->delayCnt = $summaryData[0]['delayCnt'];
        $allData['pageData']->warnCnt = $summaryData[0]['warnCnt'];

        //--- Rowspan설정
        SlCommonUtil::setListRowSpan($list, [
            'project'  => ['valueKey' => 'projectSno']
        ], $params);

        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list
        ];
    }

    /**
     * FIXME Row span 유틸 ( 중복 코드 제거 필요 )
     * @param $params
     * @param $value
     * @return array|string[]
     */
    public function getRowspanKey($params, $value){
        $customerKey = $value['customerSno'];
        $projectKey = $value['projectSno'];
        $fabricKey = $value['fabricSno'];
        $sortCondition = explode(',', $params['condition']['sort']);

        if ( 'D' == $sortCondition[0] ){ //등록 일기준
            $customerKey .= gd_date_format('Y-m-d', $value['regDt']);
            $projectKey .= gd_date_format('Y-m-d', $value['regDt']);
            $fabricKey .= gd_date_format('Y-m-d', $value['regDt']);
        }else if ( 'C' == substr($sortCondition[0],0,1) ){ //납기일 기준
            $customerKey .= $value['msDeliveryDt'];
            $projectKey .= $value['msDeliveryDt'];
            $fabricKey .= $value['msDeliveryDt'];
        }
        return [
            'customerKey' => $customerKey
            ,'projectKey' => $projectKey
            ,'fabricKey' => $fabricKey
        ];
    }

    /**
     * 코멘트 리스트 가져오기
     * @param $params
     * @return mixed
     */
    public function getProductionCommentList($params){
        $searchVo = new SearchVo(
            [
                'productionSno=?',
                'commentType=?',
            ]
            ,[
                $params['productionSno'],
                $params['commentType'],
            ]
        );
        $searchVo->setOrder('regDt desc');

        $list = DBUtil2::getListBySearchVo(ImsDBName::PRODUCTION_COMMENT,  $searchVo);

        foreach($list as $key => $each){
            $each['isModify'] = 'n';
            $each['commentBr'] = nl2br($each['comment']);
            $each['regManagerName'] = DBUtil2::getOne(DB_MANAGER, 'sno', $each['regManagerSno'])['managerNm'];
            $list[$key]=$each;
        }

        return $list;
    }

    /**
     * 생산 개별 코멘트 지우기
     * @param $params
     */
    public function deleteProductionComment($params){
        $this->delete(ImsDBName::PRODUCTION_COMMENT, $params['sno']); //프로젝트 등록된 고객이라면 삭제 불가.
    }


    /**
     * 원단 관리 정보 꾸미기
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     * @throws \Exception
     */
    public function decorationProduction($each, $key=null, $mixData=null){
        $each = DBTableField::parseJsonField(ImsDBName::PRODUCTION, $each);
        $each = DBTableField::fieldStrip(ImsDBName::PRODUCTION, $each);

        $each['eachSno'] = $each['sno'];

        $each = $this->setDefaultFile([
            'fileWork', // 작지
            'fileCareMark', // 캐어
            'filePrdMark', // 캐어
            'filePrdEtc', // 캐어
            'fileWash', //
            'fileFabricConfirm', //
            'fileFabricShip', //
            'fileQc', //
            'fileInline', //
            'fileShip', //
            'fileProductionComplete', //
            'fileProductionPacking', //
            'fileProductionInvoice', //
        ], $each);
        //$this->setDefaultFile($checkFileList, $each);

        $each['produceStatusKr'] = ImsCodeMap::PRODUCE_STATUS[$each['produceStatus']];
        $each['assortConfirmKr'] = ImsCodeMap::PROJECT_CONFIRM_TYPE_SIMPLE[$each['assortConfirm']];
        $each['workConfirmKr'] = ImsCodeMap::PROJECT_CONFIRM_TYPE_SIMPLE[$each['workConfirm']];
        //생산파일
        foreach(ImsCodeMap::PRODUCTION_STEP as $key => $step){
            $krConvertFieldList[] = $step . 'confirm';
            $each[$step.'ConfirmKr'] = ImsCodeMap::PROJECT_CONFIRM_TYPE_SIMPLE[$each[$step.'Confirm']];
        }

        $each['styleFullName'] = implode(' ',[substr($each['prdYear'],2,2),$each['prdSeason'],$each['productName']]);
        $each['isFactory'] = SlCommonUtil::isFactory();

        $each['sizeOptionTmp'] = $each['sizeOption'];
        $each['sizeOption'] = json_decode(stripslashes($each['sizeOption']), true);
        $each['typeOption'] = json_decode(stripslashes($each['typeOption']), true);
        //SitelabLogger::logger2(__METHOD__, $each);
        $each['projectTypeEn'] = ImsCodeMap::PROJECT_TYPE_EN[$each['projectType']];

        $each['projectKey'] = SlCommonUtil::aesEncrypt($each['projectSno']);

        $this->setProjectIcon($each, $each['projectSno']);

        //Project 정보 넣기
        $each['projectFiles'] = $this->getProjectFiles($each['projectSno']);

        //개별 코멘트 수
        $sql = "select commentType, count(1) as cnt from sl_imsProductionComment where productionSno = {$each['sno']} group by commentType";
        $commentDataList = DBUtil2::runSelect($sql);
        $commentCntMap = [];
        foreach($commentDataList as $commentEach){
            $commentCntMap[$commentEach['commentType']] = $commentEach['cnt'];
        }

        $beforeDelayList = [];
        $lastDelayField = null;
        foreach(ImsCodeMap::PRODUCTION_STEP as $key => $step){
            $each['commentCnt'][$step] = gd_isset($commentCntMap[$step],0);
            if(!isset($each['firstData']['schedule'][$step])){
                //초기 기준 데이터
                $each['firstData']['schedule'][$step]['ConfirmExpectedDt'] = '';
                $each['firstData']['schedule'][$step]['CompleteDt'] = '';
                $each['firstData']['schedule'][$step]['Memo'] = '';
                $each['firstData']['schedule'][$step]['Confirm'] = '';
            }

            //일정 지연 여부 체크.
            if( 30 == $each['produceStatus'] ) {
                $nowDt = SlCommonUtil::getNowDate();
                if( $each[$step.'ExpectedDt'] != ''
                    && $each[$step.'ExpectedDt'] != '0000-00-00'
                    && $each[$step.'Memo'] == ''
                    && ( $nowDt > $each[$step.'ExpectedDt'] && ( empty($each[$step.'CompleteDt']) || $each[$step.'CompleteDt'] == '0000-00-00' ))
                ){
                    foreach($beforeDelayList as $beforeDelay){
                        $each[$beforeDelay] = false;
                    }
                    $each[$step.'Delay'] = true;
                    $beforeDelayList[] = $step.'Delay';
                }else{
                    $each[$step.'Delay'] = false;
                }
            }else{
                $each[$step.'Delay'] = false;
            }

            //현재 스텝이 완료일 경우 이전 스텝'들'은 지연 아님.
            /*if( (!empty($each[$step.'CompleteDt']) && $each[$step.'CompleteDt'] != '0000-00-00') || !empty($each[$step.'Memo2']) ){
                foreach($beforeDelayList as $beforeDelay){
                    $each[$beforeDelay] = false;
                }
            }*/
        }

        if( !isset($each['firstData']['acceptData']) ){
            $each['firstData']['acceptData'] = [
                'managerNm' => '', //승인자.
                'acceptDt' => ''
            ];
        }

        //납기상태. ( deliveryExpectedDt_납기예정 : msDeliveryDt_이노버납기    )
        $diff = SlCommonUtil::getDateDiff($each['deliveryExpectedDt'],$each['msDeliveryDt']);
        if( $diff >= 6 ){
            $each['deliveryStatusName'] = '양호';
            $each['deliveryStatusColor'] = 'sl-green';
        }else if( 6 > $diff && $diff >= 0 ){
            $each['deliveryStatusName'] = '주시';
            $each['deliveryStatusColor'] = 'sl-orange';
        }else{
            $each['deliveryStatusName'] = '지연';
            $each['deliveryStatusColor'] = 'text-danger';
        }

        $each['projectStatusKr'] = ImsCodeMap::PROJECT_STATUS[$each['projectStatus']];

        //봉제기간
        $periodOfSaw = SlCommonUtil::getDateDiff($each['cuttingCompleteDt'],SlCommonUtil::getDateCalc($each['shipCompleteDt'], -3));
        $periodOfSaw = 0 >= $periodOfSaw?0:$periodOfSaw;
        //$periodOfSaw = SlCommonUtil::getDateDiff($each['cuttingCompleteDt'],$each['sewCompleteDt']);

        $each['periodOfSaw'] = '0000-00-00' !== $each['cuttingCompleteDt'] && !empty($each['cuttingCompleteDt']) && '0000-00-00' !== $each['sewCompleteDt'] && !empty($each['sewCompleteDt']) && !empty($periodOfSaw) ? $periodOfSaw.'일':'-';

        //생산기간 ( 원부자재 확정일로 부터... )
        $periodOfProduction = SlCommonUtil::getDateDiff($each['fabricConfirmCompleteDt'],$each['deliveryCompleteDt']);
        $each['periodOfProduction'] = '0000-00-00' !== $each['fabricConfirmCompleteDt'] && !empty($each['fabricConfirmCompleteDt'] ) &&  '0000-00-00' !== $each['deliveryCompleteDt'] && !empty($each['deliveryCompleteDt']) && !empty($periodOfProduction)?$periodOfProduction.'일':'-';

        return SlCommonUtil::setDateBlank($each);
    }

    /**
     * 스케쥴 상태 변경
     * @param $params
     */
    public function setScheduleReq($params){
        //TODO : 아소트 및 작지 승인 상태 확인! => 이 후 변경 되는 건은 알림 발생!
        $this->save(ImsDBName::PRODUCTION, [
            'sno' => $params['sno'],
            'produceStatus' => $params['status'],
        ]);
    }

    /**
     * 생산 상태 변경
     * @param $params
     * @throws \Exception
     */
    public function setProduceStatus($params){
        $production = DBUtil2::getOne(ImsDBName::PRODUCTION, 'sno', $params['sno'], false);
        //스케쥴 요청 시 작지/아소트 상태 확인 필
        if(10 == $params['status']){
            /*if('p' !== $production['assortConfirm'] || 'p' !== $production['workConfirm']){
                throw new \Exception('아소트 혹은 작업지시서 승인이 되지 않았습니다.');
            }*/
            if('p' !== $production['workConfirm']){
                throw new \Exception('작업지시서 승인이 되지 않았습니다.');
            }
        }
        
        $this->save(ImsDBName::PRODUCTION, [
            'sno' => $params['sno'],
            'produceStatus' => $params['status'],
        ]);

        //저장 후 상태가 생산요청이라면.
        if(10 == $params['status']){
            $projectData = $this->getProject(['sno'=>$projectData['sno']]);
            $prdData = DBUtil2::getOne(ImsDBName::PRODUCT,'sno',$production['styleSno']);
            $prdName = $prdData['productName'].' '.number_format($production['totalQty']).'장';
            $replaceData = ImsSendMessage::imsMessageReplacer(ImsSendMessage::PRODUCE_REQ,[
                'company'=>$projectData['customer']['customerName'],
                'projectNo'=>$projectData['project']['projectNo'],
                'productName'=>$prdName,
            ]);
            $this->sendAlarm($replaceData['title'],$replaceData['msg'],$projectData['project']['produceCompanySno']);
        }

        //저장 후 상태가 생산스케쥴 관리고 최초 스케쥴데이터가 없으면 FirstData 저장.
        //---> 이전상태가 20(확정대기) 이라면 으로 로직 변경
        //$firstData = json_decode($production['firstData'],true);

        if(30 == $params['status'] && 20 == $production['produceStatus'] ){
            $firstData = [];
            $firstData['acceptData'] = [
                'managerNm' => \Session::get('manager.managerNm'), //승인자.
                'acceptDt' => date('Y-m-d')
            ];
            foreach(ImsCodeMap::PRODUCTION_STEP as $key => $step){
                //초기 기준 데이터
                $firstData['schedule'][$step]['ConfirmExpectedDt'] = gd_isset($production[$step.'ExpectedDt'],'');
                $firstData['schedule'][$step]['CompleteDt'] = gd_isset($production[$step.'CompleteDt'],'');
                $firstData['schedule'][$step]['Memo'] = gd_isset($production[$step.'Memo'],'');
                $firstData['schedule'][$step]['Confirm'] = gd_isset($production[$step.'ConfirmYn'],'');
            }
            $this->save(ImsDBName::PRODUCTION,[
                'sno' => $params['sno'],
                'firstData' => json_encode($firstData),
            ]);
        }

        //납품 완료시 처리
        if(99 == $params['status']){
            ImsScheduleUtil::setScheduleCompleteDt($production['projectSno'],'projectComplete','now()');
            ImsScheduleUtil::setProjectScheduleStatus($production['projectSno']);
        }
        
    }

    /**
     * 스케쥴 체크
     * @param $params
     * @param string $checkDt
     */
    public function setScheduleCheck($params, $checkDt = 'now()'){
        foreach( $params['checkSnoList'] as $sno ){
            $saveParams = [
                'sno' => $sno,
                'scheduleCheck' => $params['checkValue'],
            ];
            if( '-1' !== $checkDt ){
                $saveParams['scheduleCheckDt'] = 'now()';
            }
            $this->save(ImsDBName::PRODUCTION, $saveParams);
        }
    }

    /**
     * 스케쥴 일괄 수정.
     * @param $params
     * @throws \Exception
     */
    public function saveScheduleBatch($params){
        foreach($params['checkList'] as $sno){
            $saveData['sno'] = $sno;

            foreach( ImsCodeMap::PRODUCTION_STEP as $stepName ){
                $saveData[$stepName.'ExpectedDt'] = $params[$stepName.'ExpectedDt'];
                $saveData[$stepName.'CompleteDt'] = $params[$stepName.'CompleteDt'];
                $saveData[$stepName.'Memo'] = $params[$stepName.'Memo'];
                $saveData[$stepName.'Memo2'] = $params[$stepName.'Memo2'];
            }

            $this->checkUpdateSchedule($sno, $saveData);

            $this->save(ImsDBName::PRODUCTION, $saveData);

            $this->setAutoAccept($sno);
        }
    }

    /**
     * 납기일 체크
     * @param $sno
     * @param $saveData
     * @throws \Exception
     */
    public function checkUpdateSchedule($sno, $saveData){
        if( SlCommonUtil::isFactory() ){
            //TODO : 저장시 이전 일정보다 늦은 일정으로 저장할 수 없게 하기.
            //납기일 체크.
            $productionData = DBUtil2::getOne(ImsDBName::PRODUCTION, 'sno', $sno);
            if( $saveData['deliveryExpectedDt'] > $productionData['msDeliveryDt'] ){
                $customer = DBUtil2::getOne(ImsDBName::CUSTOMER,'sno',$productionData['customerSno']);
                $style = DBUtil2::getOne(ImsDBName::PRODUCT,'sno',$productionData['styleSno']);
                $prdYear = substr($style['prdYear'], 2, 2);
                $styleFullName = "{$prdYear} {$style['prdSeason']} {$style['productName']}";
                $msg = "공장납기가 이노버 납기일을 넘을 수 없습니다.<br>이노버납기:{$productionData['msDeliveryDt']} <br>수정 공장납기:{$saveData['deliveryExpectedDt']}  <br>생산번호:{$sno} / 고객:{$customer['customerName']} <br>스타일:{$styleFullName} ";
                throw new \Exception($msg);
            }
        }
    }

    /**
     * 생산 삭제
     * @param $params
     * @throws \Exception
     */
    public function deleteProduction($params){
        //이미 생산처 요청한 건은 삭제 불가.
        $production = DBUtil2::getOne(ImsDBName::PRODUCTION, 'sno', $params['sno']);
        if( empty($production['reqStatus']) ){
            throw new \Exception('스케쥴 요청 전 데이터만 삭제 가능합니다.');
        }else{
            $this->delete(ImsDBName::PRODUCTION, $params['sno']);
        }
    }


    /**
     * 생산 코멘트 등록
     * @param $params
     * @throws \Exception
     */
    public function saveProductionComment($params){
        if( empty($params['sno']) ){
            $saveData = [
                'productionSno' => $params['productionSno'],
                'commentType' => $params['commentType'],
                'comment' => $params['comment'],
                'regManagerSno' => SlCommonUtil::getManagerSno()
            ];
            DBTableField::checkRequired(ImsDBName::PRODUCTION_COMMENT, $saveData);
            DBUtil2::insert(ImsDBName::PRODUCTION_COMMENT, $saveData);
        }else{
            DBUtil2::update(ImsDBName::PRODUCTION_COMMENT, ['comment'=>$params['comment']], new SearchVo('sno=?',$params['sno']));
        }
    }

    /**
     * 생산 데이터 심플 저장
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function saveSimpleProduction($params){
        if( !empty($params['sno']) ){
            $this->save(ImsDBName::PRODUCTION, $params);
            //$this->setAutoAccept($saveData['sno']);
            //$this->saveProductionComment();
        }
        return ['data'=> $params,'msg'=>'저장 완료'];
    }


    /**
     * 생산완료 후 회계팀에 알림
     */
    public function alarmProductionComplete(){
        $sql = "
            SELECT 
                (CURDATE() - INTERVAL 7 DAY) as completeDt, 
                a.projectSno, 
                a.styleSno,
                b.productName,
                d.customerName,
                right(b.prdYear,2) as prdYear,   
                b.prdSeason   
            FROM sl_imsProduction a 
             JOIN sl_imsProjectProduct b 
               ON a.styleSno = b.sno
             JOIN sl_imsProject c 
               ON a.projectSno = c.sno 
             JOIN sl_imsCustomer d 
               ON c.customerSno = d.sno
            WHERE a.deliveryCompleteDt = CURDATE() - INTERVAL 7 DAY        
        ";

        $list = DBUtil2::runSelect($sql);

        if( count($list) > 0 ){
            //생산 완료 프로젝트가 있으면 메일 발송
            $completeDt = date('y/m/d', strtotime('-7 days'));
            $subject = "{$completeDt} 생산완료 알림";

            $contents = [];
            $contents[] = $completeDt . '에 아래 프로젝트/스타일이 납기 완료 되었습니다.<br>';
            foreach($list as $each){
                $contents[] = "<span style='color:red'>{$each['projectSno']}</span> {$each['customerName']} {$each['prdYear']} {$each['prdSeason']} {$each['productName']}";
            }

            SiteLabMailUtil::sendSystemMail($subject, implode('<br>',$contents), implode(',',ImsCodeMap::COST_APPROVAL_ALARM_LIST));
        }

    }

}


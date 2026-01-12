<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\ImsStatusUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceTrait {

    public function saveDataRefine(&$saveData){
        if( is_array($saveData['fabricNational']) ){
            $saveData['fabricNational'] = array_sum($saveData['fabricNational']);
        }
        if(isset($saveData['styleCode'])){
            $saveData['styleCode'] = strtoupper($saveData['styleCode']);
        }
    }

    /**
     * 저장뒤 추가 로직.
     * @param $table
     * @param $saveData
     * @param $sno
     * @throws \Exception
     */
    public function saveAfterAction($table, $saveData, $sno){
        if( ImsDBName::PRODUCE === $table ){
            //생산 테이블 업데이트시에는 선적 정보 싱크 하기.
            $prdData = DBUtil2::getOne($table, 'sno', $sno);
            $prdStepData = json_decode($prdData['prdStep'],true);
            DBUtil2::update(ImsDBName::PRODUCE, [
                'shipExpectedDt' => $prdStepData['prdStep60']['expectedDt'],
                'shipCompleteDt' => $prdStepData['prdStep60']['completeDt'],
            ], new SearchVo('sno=?',$sno));
        }
    }

    /**
     * IMS 관련 테이블 저장.
     * @param $table
     * @param $saveData
     * @param array|null $newOnlyDataArray SaveData 추가 데이터.
     * @param bool $insertOnly
     * @return mixed
     * @throws \Exception
     */
    public function save($table, $saveData, array $newOnlyDataArray = null, $insertOnly = false){

        $this->saveDataRefine($saveData);

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
            if( in_array('lastManagerSno', DBTableField::getTableKey($table)) ) $saveData['lastManagerSno'] = \Session::get('manager.sno');

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
                $this->recordHistory('update', $table, $beforeData, $updateMsg);
            }

            //SitelabLogger::logger('refine save...');
            //SitelabLogger::logger($refineSaveData);

            DBUtil2::updateBySno($table, $refineSaveData, $sno);

            $this->saveAfterAction($table, $refineSaveData, $sno);

        }else{
            if( !empty($newOnlyDataArray) ){
                foreach($newOnlyDataArray as $key => $value){
                    $saveData[$key] = $value;
                }
            }
            if( in_array('regManagerSno', DBTableField::getTableKey($table)) ) $saveData['regManagerSno'] = \Session::get('manager.sno');
            //SitelabLogger::logger('저장확인');
            //SitelabLogger::logger($saveData);
            $sno = DBUtil2::insert($table, $saveData);
        }


        //생산선행 관련 저장일 경우.
        if(ImsDBName::PREPARED == $table){
            $preparedData = DBUtil2::getOne(ImsDBName::PREPARED, 'sno', $sno);
            DBUtil2::update(ImsDBName::PROJECT,[$preparedData['preparedType'].'Status'=>$preparedData['preparedStatus']], new SearchVo('sno=?',$preparedData['projectSno']));
        }

        //프로젝트 저장시 선행작업 체크.
        if(ImsDBName::PROJECT == $table){
            $this->setRefinePreparedStatus($sno);
            $list = DBUtil::getList(ImsDBName::PREPARED, 'projectSno', $sno);
            foreach($list as $each){
                DBUtil2::update(ImsDBName::PROJECT,[
                    $each['preparedType'].'Status' => $each['preparedStatus']
                ], new SearchVo('sno=?', $sno));
            }

            //프로젝트 저장시 후속 작업
            $this->saveProjectAfterProc($beforeData, $saveData);
        }

        return $sno;
    }

    /**
     * 프로젝트 저장 후속 작업
     * 고객 납기일이 변경되었다면... 발주DL 후속처리
     * @param $beforeData
     * @param $saveData
     * @throws \Exception
     */
    public function saveProjectAfterProc($beforeData, $saveData){
        if( !empty($beforeData['customerDeliveryDt'])
            && !empty($saveData['customerDeliveryDt'])
            && !empty($beforeData['customerOrderDeadLine'])
            && $beforeData['customerDeliveryDt'] != $saveData['customerDeliveryDt']
            && $beforeData['customerOrderDeadLine'] == $saveData['customerOrderDeadLine'] //발주DL이 기존이랑 같다면...
        ){
            //$orderCompleteData = DBUtil2::getOne(ImsDBName::PROJECT_ADD_INFO, "fieldDiv = 'orderComplete' and projectSno=?", $beforeData['sno']);
            $customer = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $beforeData['customerSno']);
            $comment = $customer['customerName']. ' ' . $beforeData['projectYear'] . $beforeData['projectSeason'] . " 고객납기일 변경 : " . $beforeData['customerDeliveryDt'] . ' => ' . $saveData['customerDeliveryDt'] . ' 발주DL확인필요';

            //생산처.
            /*if( SlCommonUtil::isDev() ){
                SitelabLogger::logger($comment);
            }else{
                //SitelabLogger::logger($comment);
                foreach(ImsCodeMap::CUSTOMER_DELIVERY_SMS as $phoneNumber){
                    SlSmsUtil::sendSmsSimple($comment, $phoneNumber);
                }
            }*/
            //TODO 발주DL 초기화.
            //내용을 코멘트 남기기
            //CommentDiv가 없으면 자동으로 채운다.
            /*$saveData['regManagerSno'] = \Session::get('manager.sno');
            $saveData['commentDiv'] = ImsCodeMap::PROJECT_STEP_COMMENT_DIV[$beforeData['projectStatus']];
            $saveData['comment'] = $comment;
            $saveData['projectSno'] = $beforeData['sno'];
            $saveData['isShare'] = 'n';
            DBUtil2::insert(ImsDBName::PROJECT_COMMENT, $saveData);*/
        }
    }

    /**
     * 생산 선행 상태 초기화
     * @param $sno
     */
    public function setRefinePreparedStatus($sno){
        DBUtil2::runSql("update sl_imsProject set btStatus = null, costStatus=null, estimateStatus=null, workStatus=null, orderStatus=null where sno = {$sno}");
    }

    /**
     * 상태변경 히스토리 기록
     * @param $saveParam
     * @throws \Exception
     */
    public function saveStatusHistory($saveParam){
        ImsStatusUtil::saveStatusHistory($saveParam);
    }

    /**
     * 변경이력 반환
     * @param $params
     * @return mixed
     */
    public function getStatusHistory($params){
        //$projectSno = empty($params['projectSno']) ? $params['sno'] : $params['projectSno'];
        //$sql = "select a.*, b.managerNm as regManagerNm from sl_imsStatusHistory a left outer join es_manager b on a.regManagerSno = b.sno ";
        //$sql .= " where projectSno = {$projectSno} AND {$historyDiv} order by regDt desc ";
        //$list = DBUtil2::runSelect($sql);

        $searchVo = new SearchVo('historyDiv=?', $params['historyDiv']);
        $searchVo->setOrder('a.sno desc');
        $checkConditionList = [
            'customerSno',
            'projectSno',
            'styleSno',
            'eachSno',
        ];
        foreach( $checkConditionList as $field ){
            if(!empty($params[$field])){
                $searchVo->setWhere('a.'.$field.'=?');
                $searchVo->setWhereValue($params[$field]);
            }
        }

        $list = DBUtil2::getComplexList($this->sql->getStatusTable(), $searchVo);

        foreach($list as $key => $value){
            $value['beforeStatus'] = !empty(ImsCodeMap::PROJECT_STATUS_ALL_MAP[$value['beforeStatus']]) ? ImsCodeMap::PROJECT_STATUS_ALL_MAP[$value['beforeStatus']] : $value['beforeStatus'];
            $value['afterStatus'] = !empty(ImsCodeMap::PROJECT_STATUS_ALL_MAP[$value['afterStatus']]) ? ImsCodeMap::PROJECT_STATUS_ALL_MAP[$value['afterStatus']] : $value['afterStatus'];
            $list[$key] = $value;
        }

        return $list;
    }
    /**
     * 변경이력 반환
     * @param $params
     * @return mixed
     */
    public function getStatusHistoryLegacy($params){
        $projectSno = empty($params['projectSno']) ? $params['sno'] : $params['projectSno'];
        if(empty($params['historyDiv'])) {
            //프로젝트
            $historyDiv = " a.historyDiv = '' ";
            $statusMap = ImsCodeMap::PROJECT_STATUS;
        }else if( 'produce' == $params['historyDiv']){
            //생산
            $historyDiv = " a.historyDiv = '{$params['historyDiv']}' ";
            $statusMap = ImsCodeMap::PRODUCE_STATUS;
        }else{
            //기타
            $historyDiv = " a.historyDiv = '{$params['historyDiv']}' ";
            $statusMap = [];
        }

        $sql = "select a.*, b.managerNm as regManagerNm from sl_imsStatusHistory a left outer join es_manager b on a.regManagerSno = b.sno ";
        $sql .= " where projectSno = {$projectSno} AND {$historyDiv} order by regDt desc ";

        $list = DBUtil2::runSelect($sql);
        foreach($list as $key => $each){
            //$each['regDt'] = gd_date_format('',$each['regDt']);
            $each['beforeStatusKr'] = gd_isset($statusMap[$each['beforeStatus']],$each['beforeStatus']) ;
            $each['afterStatusKr'] = gd_isset($statusMap[$each['afterStatus']],$each['afterStatus']);
            $list[$key] = $each;
        }
        return $list;
    }

    /**
     * 전산작지 특정 데이터 이력 보여주기
     * @param $params
     * @return mixed
     */
    public function getEworkHistory($params){
        $eworkService = SlLoader::cLoad('ims','ImsEworkService');
        return $eworkService->getEworkHistory($params);
    }

    /**
     * 업데이트 이력
     * @param $params
     * @return mixed
     */
    public function getUpdateHistory($params){
        $tableMap = [
            'product' => 'sl_imsProjectProduct',
            'project' => '',
            'customer' => 'sl_imsCustomer',
            'produce' => 'sl_imsProduce',
            'ework' => 'sl_imsEwork',
        ];

        $tableName = $tableMap[$params['historyDiv']];
        $historyDiv = " a.tableName = '{$tableName}' ";

        if( !empty($tableName) ){
            $sql = "select a.*, b.managerNm as regManagerNm from sl_imsUpdateHistory a left outer join es_manager b on a.regManagerSno = b.sno ";
            $sql .= " where tableSno = {$params['sno']} AND {$historyDiv} order by regDt desc";
            $list = DBUtil2::runSelect($sql);
        }else{
            //TOTAL
            $projectData = DBUtil2::getOne(ImsDBName::PROJECT, 'sno', $params['sno']);
            $extData = DBUtil2::getOne(ImsDBName::PROJECT_EXT, 'projectSno', $params['sno']);
            $produceData = DBUtil2::getOne(ImsDBName::PRODUCTION, 'projectSno', $params['sno']);

            $sql[] = $this->getUpdateHistoryQuery($params['sno'], 'sl_imsProject');
            $sql[] = $this->getUpdateHistoryQuery($projectData['customerSno'], 'sl_imsCustomer');

            if( !empty($extData['sno']) ){
                $sql[] = $this->getUpdateHistoryQuery($extData['sno'], 'sl_imsProjectExt');
            }

            if( !empty($produceData['sno']) ){
                $sql[] = $this->getUpdateHistoryQuery($produceData['sno'], 'sl_imsProduction');
            }

            $sqlStr = implode(' union all ', $sql);
            $sqlStr = 'select * from (' . $sqlStr . ') a order by regDt desc ' ;

            $list = DBUtil2::runSelect($sqlStr);
        }

        foreach($list as $key => $each){
            $commentList = json_decode($each['comment'],true);
            $unsetList = [
                '이노버마진', '옵션과수량이', '사이즈스펙'
            ];
            $filtered = array_filter($commentList, function($item) use ($unsetList) {
                foreach($unsetList as $unsetWord) {
                    if (strpos($item, $unsetWord) !== false) {
                        return false;
                    }
                }
                return true;
            });

            $each['comment'] = $filtered;
            $list[$key] = $each;
        }

        return $list;
    }

    public function getUpdateHistoryQuery($sno, $tableName){
        $historyDiv = " a.tableName = '{$tableName}' ";
        $sql = "select a.*, b.managerNm as regManagerNm from sl_imsUpdateHistory a left outer join es_manager b on a.regManagerSno = b.sno ";
        $sql .= " where tableSno = {$sno} AND {$historyDiv} ";
        return $sql;
    }

    /**
     * 수정/삭제 이력 기록.
     * @param $type
     * @param $tableName
     * @param $recordData
     * @param $updateMsg
     */
    public function recordHistory($type, $tableName, $recordData, $updateMsg){
        DBUtil2::insert( 'sl_ims'.ucfirst($type).'History', [
            'tableName' => $tableName,
            'tableSno' => $recordData['sno'],
            'contents' => json_encode($recordData),
            'comment' => json_encode($updateMsg, JSON_UNESCAPED_UNICODE),
            'regManagerSno' => \Session::get('manager.sno'),
        ]);
    }


    /**
     * 데이터 삭제
     * @param $table
     * @param $sno
     * @throws \Exception
     */
    public function delete($table, $sno){
        $beforeData = DBUtil2::getOne($table,'sno',$sno);
        $this->recordHistory('delete', $table, $beforeData);
        DBUtil2::delete($table, new SearchVo('sno=?', $sno));
    }

    /**
     * 코멘트 리스트 가져오기
     * @param $div
     * @param $projectSno
     * @return mixed
     */
    public function getCommentList($div, $projectSno){
        $divStr = empty($div) ? "" : " and commentDiv = '{$div}' ";
        $sql = " select a.*, b.managerNm as regManagerName from sl_imsComment a left outer join es_manager b on a.regManagerSno = b.sno where projectSno = {$projectSno} {$divStr} order by regDt desc";
        return DBUtil2::runSelect($sql);
    }

    /**
     * 프로젝트 코멘트 반환
     * @param $projectSno
     * @param null $div
     * @return mixed
     */
    public function getProjectComment($projectSno, $div = null){
        $divWhere = '';
        if(!empty($div)){
            $divWhere = " AND commentDiv = '$div' ";
        }

        $addWhere = '';
        if( SlCommonUtil::isFactory() ) {
            $addWhere = " AND isShare = 'y'";
        }

        $sql = " select a.*, b.managerNm as regManagerName, b.dispImage from sl_imsComment a left outer join es_manager b on a.regManagerSno = b.sno where projectSno = {$projectSno} {$divWhere} {$addWhere} order by regDt desc";

        $list = DBUtil2::runSelect($sql);
        foreach($list as $key => $each){
            $each['commentDivKr'] = ImsCodeMap::PROJECT_COMMENT_DIV[$each['commentDiv']];
            $each['commentBr'] = nl2br($each['comment']);
            $each['dispImage'] = empty($each['dispImage']) ? '/data/commonimg/ico_noimg_75.gif' : $each['dispImage'];
            $each['isModify'] = 'n';
            $list[$key] = $each;
        }
        return $list;
    }
    public function getProjectCommentList($projectSno, $div = null){
        $divWhere = '';
        if(!empty($div)){
            $divWhere = " AND commentDiv = '$div' ";
        }
        /*if(!empty($params['div'])){
            $divWhere = " AND commentDiv = '$div' ";
        }*/

        $sql = " select a.*, b.managerNm as regManagerName, b.dispImage from sl_imsComment a left outer join es_manager b on a.regManagerSno = b.sno where projectSno = {$projectSno} {$divWhere} order by regDt desc";

        $list = DBUtil2::runSelect($sql);
        foreach($list as $key => $each){
            $each['commentDivKr'] = ImsCodeMap::PROJECT_COMMENT_DIV[$each['commentDiv']];
            $each['commentBr'] = nl2br($each['comment']);
            $each['dispImage'] = empty($each['dispImage']) ? '/data/commonimg/ico_noimg_75.gif' : $each['dispImage'];
            $list[$key] = $each;
        }
        return $list;
    }

    /**
     * 코멘트 숫자 반환
     * @param $div
     * @param $sno
     * @return mixed
     */
    public function getCommentCount($div, $sno){
        if( empty($div) ){
            $data = DBUtil2::getCount(ImsDBName::PROJECT_COMMENT, new SearchVo('projectSno=?', $sno));
        }else{
            $data = DBUtil2::getCount(ImsDBName::PROJECT_COMMENT, new SearchVo(['commentDiv=?','projectSno=?'], [$div,$sno]));
        }
        return $data;
    }

    public function getLatestComment($div, $sno){
        if( empty($div) ){
            $searchVo = new SearchVo('projectSno=?', $sno);
        }else{
            $searchVo = new SearchVo(['commentDiv=?','projectSno=?'], [$div,$sno]);
        }
        $searchVo->setOrder('regDt desc');
        $data = DBUtil2::getOneBySearchVo(ImsDBName::PROJECT_COMMENT, $searchVo);
        if(!empty($data)){
            $data['regManagerName'] = DBUtil2::getOne(DB_MANAGER,'sno',$data['regManagerSno'])['managerNm'];
        }
        return $data;
    }

    /**
     * 알림 보내기.
     * @param $title
     * @param $msg
     * @param $managerSno
     */
    public function sendAlarm($title,$msg,$managerSno){
        $info = SlCommonUtil::getManagerInfo($managerSno);
        $list = json_decode($info['memo'],true);
        foreach($list as $key => $each){
            //메일발송
            if(!empty($each['email'])){
                SiteLabMailUtil::sendSimpleMail($title, nl2br($msg), $each['email']);
            }
            //전화번호
            if(!empty($each['phone'])){
                SlSmsUtil::sendSmsSimple($title.' : '.$msg, $each['phone']);
            }
            //알림톡발송?
        }
    }

    public function sendAlarmTest($title,$msg,$managerSno){
        $info = SlCommonUtil::getManagerInfo($managerSno);
        $list = json_decode($info['memo'],true);
        gd_debug($list);

        foreach($list as $key => $each){
            //메일발송
            if(!empty($each['email'])){
                //SiteLabMailUtil::sendSimpleMail($title, nl2br($msg), $each['email']);
            }
            //전화번호
            if(!empty($each['phone'])){
                //SlSmsUtil::sendSmsSimple($title.' : '.$msg, $each['phone']);
            }
            //알림톡발송?
        }

        gd_debug('complete');
    }


    /**
     * 환율 입력 안된 생산견적서 환율 입력
     * @throws \Exception
     */
    public function setExchange(){
        // 8월 8일 이후 건 부터 진행.
        $list = DBUtil2::getListBySearchVo(ImsDBName::ESTIMATE, new SearchVo(" completeDt >= '2024-08-08 00:00:00' and (reqStatus=3 or reqStatus=5) ", 3), false);
        foreach($list as $each){
            $contents = json_decode($each['contents'], true);
            if(empty($contents['exchange'])){
                $exchange = DBUtil2::getOneSortData('es_exchangeRateConfig', 'left(regDt,10)=?', substr($each['completeDt'],0,10), 'regDt desc');
                $contents['exchange'] = $exchange['exchangeRateConfigUSDManual'];
                $contents['exchangeDt'] = substr($each['completeDt'],0,10);
                $encodeContents = json_encode($contents);
                $updateRslt = DBUtil2::update(ImsDBName::ESTIMATE, [
                    'contents'=>$encodeContents
                ], new SearchVo('sno=?',$each['sno']));
                //gd_debug( $each['sno'].':'.$updateRslt);
            }
            ///gd_debug( $contents['exchange'].':'.$contents['exchangeDt'] );
        }
    }



    // Save Version 2

    /**
     * Save 25년 버전
     * @param $table
     * @param $saveData
     * @param $isJsonStrip
     * @return mixed
     * @throws \Exception
     */
    public function imsSave( $table, $saveData, $isJsonStrip=false){
        $tableName = 'sl_ims'.ucfirst($table);
        $dbFieldMap = SlCommonUtil::arrayAppKey(DBTableField::callTableFunction($tableName), 'val');

        $sno = $saveData['sno'];
        $unsetList = ['mode','sno','regDt','modDt','lastManagerSno','regManagerSno'];
        foreach($unsetList as $unsetField){
            unset($saveData[$unsetField]);
        }
        foreach($dbFieldMap as $dbFieldKey => $dbFieldInfo){
            if( isset($saveData[$dbFieldKey]) ){
                //JSON 필드 인코딩.
                if(!empty($dbFieldInfo['json'])){
                    $saveData[$dbFieldKey] = json_encode($saveData[$dbFieldKey]);
                }
            }
        }

        if( empty($sno) || 0 >= $sno ){
            $sno = $this->simpleInsert($dbFieldMap, $tableName, $saveData); //데이터 등록
        }else{
            //SitelabLogger::logger2(__METHOD__, $saveData);
            $this->simpleUpdate($dbFieldMap, $tableName, $saveData , $sno, $isJsonStrip); //데이터 업데이트
        }

        return $sno;
    }

    /**
     * 데이터 삽입
     * @param $dbFieldMap
     * @param $table
     * @param $saveData
     * @return mixed
     * @throws \Exception
     */
    public function simpleInsert( $dbFieldMap, $table, $saveData ){

        if( in_array('regManagerSno', DBTableField::getTableKey($table)) ) $saveData['regManagerSno'] = \Session::get('manager.sno'); //등록자

        //필수값 체크
        foreach($dbFieldMap as $dbFieldKey => $dbFieldInfo){
            if( true === $dbFieldInfo['required'] && empty($saveData[$dbFieldKey]) ){
                throw new \Exception('필수값 미입력 : ' . $dbFieldInfo['name']);
            }
        }

        return DBUtil2::insert($table, $saveData); //신규 데이터 삽입
    }

    /**
     * 데이터 업데이트
     * @param $dbFieldMap
     * @param $table
     * @param $saveData
     * @param $sno
     * @param bool $isJsonStrip
     * @throws \Exception
     */
    public function simpleUpdate( $dbFieldMap, $table, $saveData, $sno , $isJsonStrip = false ){

        $updateMsg = [];
        $refineSaveData = [];

        if( in_array('lastManagerSno', DBTableField::getTableKey($table)) ) $saveData['lastManagerSno'] = \Session::get('manager.sno'); //마지막 수정자

        //기존데이터 가져오기
        $beforeData = DBUtil2::getOne($table, 'sno', $sno, false); //Strip하지 않음

        //변경사항 체크 및 저장 데이터 정제(필요한것만)
        foreach($dbFieldMap as $dbFieldKey => $dbFieldInfo){
            if( isset($saveData[$dbFieldKey]) ){

                //STRIP은 기존 데이터 해제해서 비교한다.
                if(!empty($dbFieldInfo['strip'])){
                    $beforeData[$dbFieldKey] = gd_htmlspecialchars_stripslashes($beforeData[$dbFieldKey]);
                }

                //0000-00-00 정제
                if('0000-00-00' === $beforeData[$dbFieldKey]){
                    $beforeData[$dbFieldKey] = '';
                }
                if('0000-00-00' === $saveData[$dbFieldKey]){
                    $saveData[$dbFieldKey] = '';
                }

                //변경사항 비교 (FIXME : 맞추기가 어렵다 , 이게 한 이유가 있을텐데.)
                $checkValue = $beforeData[$dbFieldKey];

                if(!empty($dbFieldInfo['json']) && $isJsonStrip ){
                    $checkValue = stripslashes($beforeData[$dbFieldKey]); //addedInfo. 저장이 다른가. ?
                }

                if( $saveData[$dbFieldKey] != $checkValue ){
                    //변경 사항이 JSON 이라면.
                    if(!empty($dbFieldInfo['json'])){
                        $updateMsgStr = "{$dbFieldInfo['name']} 변경 됨 ";
                    }else{
                        $updateMsgStr = "{$dbFieldInfo['name']} 변경 : {$beforeData[$dbFieldKey]} ▶ {$saveData[$dbFieldKey]} ";
                    }
                    $updateMsg[] = $updateMsgStr;

                    /*SitelabLogger::logger('============ > ' . $dbFieldKey . ' < ============');
                    SitelabLogger::logger('CHECK 1');
                    SitelabLogger::logger($saveData[$dbFieldKey]); //Save는 슬래시가 들어감
                    SitelabLogger::logger('CHECK 2');
                    SitelabLogger::logger($checkValue);*/

                    $refineSaveData[$dbFieldKey] = $saveData[$dbFieldKey];
                }
            }
        }

        //업데이트 사항이 있을 경우.
        if( !empty($refineSaveData) ){
            $updateRslt = DBUtil2::updateBySno($table, $refineSaveData, $sno);
            //SitelabLogger::logger2(__METHOD__, '업데이트 기록');
            //SitelabLogger::logger2(__METHOD__, $updateRslt);
            if( !empty($updateRslt) ){
                $this->recordHistory('update', $table, $beforeData, $updateMsg); //이력 기록
            }
        }else{
            //throw new \Exception('변경된 사항이 없습니다.');
            //SitelabLogger::logger2(__METHOD__, '변경된 사항이 없습니다.');
        }
    }

    /**
     * 저장 데이터 비교 로그 기록
     * @param $beforeData
     * @param $saveData
     * @param $compareList
     */
    public function saveCompareLog($beforeData, $saveData, $compareList){
        foreach( $compareList as $compareField ){
            SitelabLogger::logger2(__METHOD__, $compareField);

            SitelabLogger::logger2(__METHOD__, '저장 비교  => 이전 데이터');
            SitelabLogger::logger2(__METHOD__, $beforeData[$compareField]);

            SitelabLogger::logger2(__METHOD__, '저장 비교 => 이후 데이터');
            SitelabLogger::logger2(__METHOD__, $saveData[$compareField]);

            SitelabLogger::logger2(__METHOD__, '★ 같은지 여부 확인');
            SitelabLogger::logger2(__METHOD__, $saveData[$compareField] == $beforeData[$compareField]);
        }
    }


    /**
     * 아소트 ( 아소트가 등록되면 더이상 사이즈 수정 불가 )
     * @param $productData
     * @return array
     */
    public function getAssort($productData){
        //SitelabLogger::logger2(__METHOD__, '아소트 셋팅 디버그' . $productData['sno']);
        //SitelabLogger::logger2(__METHOD__, $productData['assort']);
        //SitelabLogger::logger2(__METHOD__, 'SizeSpec...');
        //SitelabLogger::logger2(__METHOD__, $productData['sizeSpec']);
        //SitelabLogger::logger2(__METHOD__, $productData);
        //SitelabLogger::logger2(__METHOD__, $assort);
        if(!is_array($productData['assort'])){
            $assort = json_decode($productData['assort'], true); //이미 되어서 왔을 수도.
        }else{
            $assort = $productData['assort']; //이미 되어서 왔을 수도.
        }
        //SitelabLogger::logger2(__METHOD__, $productData['sno']);
        //SitelabLogger::logger2(__METHOD__, $assort);

        //초기 셋팅
        $sizeList = explode(',',$productData['sizeSpec']['specRange']); //추후 아소트 입력시 고정시켜야함

        if( empty($assort) || 0 >= count($assort) ){
            //초기 셋팅
            //SitelabLogger::logger2(__METHOD__, 'Check... $sizeList ');
            //SitelabLogger::logger2(__METHOD__, $sizeList);
            $defaultData = ImsJsonSchema::ASSORT;
            $defaultData['packingYn'] = 'N';
            $defaultData['type'] = '수량';
            $defaultData['qtyType'] = '청구';
            $defaultData['optionList'] = [];
            foreach($sizeList as $size){
                $defaultData['optionList'][$size] = '';
            }
            //SitelabLogger::logger2(__METHOD__, 'Check... $defaultData ');
            //SitelabLogger::logger2(__METHOD__, $defaultData);
            $assort = [
                $defaultData
            ];
            //'type' => '',  //구분
            //'optionList' => [], //이름
        }else{
            foreach($assort as $assortKey => $assortData){
                $refineAssortData = [];
                $refineAssortData['packingYn'] = isset($assortData['packingYn']) ? $assortData['packingYn'] : 'N';
                $refineAssortData['type'] = $assortData['type'];
                $refineAssortData['qtyType'] = isset($assortData['qtyType']) ? $assortData['qtyType'] : '청구';
                foreach($sizeList as $size){
                    if( empty($assortData['optionList'][$size]) ){
                        $refineAssortData['optionList'][$size] = '';
                    }else{
                        $refineAssortData['optionList'][$size] = $assortData['optionList'][$size];
                    }
                }

                $refineAssortData['total']=1;

                $assort[$assortKey] = $refineAssortData;
            }
        }

        return $assort;
    }


    /**
     * 특정 상태에 따른 자동 상태 변경
     * @param $key
     * @param $each
     */
    public function saveAddInfoAfterProc($key, $each){
        //자동 단계는 앞으로만 간다.
        $checkAutoStepList = [
            //제안서 발송 완료일 => 샘플단계로 변경
            'custInform' => ['nextStep' => 40, 'reason'=>'제안발송 완료일 입력으로 단계변경'],
            //샘플 발송 완료일 => 샘플확정대기 단계로 변경
            'custSampleInform' => ['nextStep' => 41, 'reason'=>'샘플발송 완료일 입력으로 단계변경'],
            //고객 발주(아소트) 완료일 => 고객발주(아소트) 단계로 변경
            'custOrder' => ['nextStep' => 50, 'reason'=>'아소트 입력(고객발주)으로 단계변경'],
            //작지 완료일 입력시 => 발주 단계로 변경
            'order' => ['nextStep' => 60, 'reason'=>'작업지시서 완료되어 단계변경'],
        ];
        foreach($checkAutoStepList as $checkAutoStepKey => $checkAutoStep){
            if( $checkAutoStepKey === $key && !empty($each['completeDt']) ){
                $this->setStatus(['projectSno'=>$each['projectSno'],'projectStatus'=>$checkAutoStep['nextStep'], 'reason'=>$checkAutoStep['reason']], true);
            }
        }
    }

    /**
     * 리스트 , 뷰 공통 데코레이션
     * @param $each
     * @return mixed
     */
    public function setCommonProductDecoration($each){
        $each['isWorkModifyAuth'] = 'y';
        if( in_array($each['projectType'], ImsCodeMap::PROJECT_TYPE_N) //신규
            && !( $each['sampleConfirmSno'] > 0 || $each['sampleConfirmSno'] === -1 ) //샘플확정 여부 안됨
        ){
            $each['isWorkModifyAuth'] = 'n';
        }
        return $each;
    }

    /**
     * 추가 정보 저장
     * @param $saveProject
     * @throws \Exception
     */
    public function saveAddInfo($saveProject){
        foreach(ImsCodeMap::PROJECT_ADD_INFO as $addInfoKeyPrefix => $addInfoValue){
            $isSave = false;
            $updateData = [];
            $updateData['fieldDiv'] = $addInfoKeyPrefix;
            $updateData['projectSno'] = $saveProject['sno'];
            foreach(ImsCodeMap::PROJECT_ADD_INFO_KEY as $addInfoKey){
                //if( $saveProject[$addInfoKeyPrefix.ucfirst($addInfoKey)] ){
                $updateData[$addInfoKey] = $saveProject[$addInfoKeyPrefix.ucfirst($addInfoKey)];
                $isSave=true;
                //}
            }
            if($isSave){
                if(!empty($saveProject['sno'])){
                    DBUtil2::merge(ImsDBName::PROJECT_ADD_INFO, $updateData, new SearchVo(['fieldDiv=?','projectSno=?'],[$addInfoKeyPrefix, $saveProject['sno']]));
                    $this->saveAddInfoAfterProc($addInfoKeyPrefix, $updateData);
                }
            }
        }
    }

}



<?php

namespace SlComponent\Database;

use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;

class DBUtil2
{
    const QUERY_DEBUG_MODE = false;
    const QUERY_DEBUG_LOG_MODE = false;
    const QUERY_DEBUG_CRUD_LOG_MODE = false;

    const EMPTY_FILED = '{EMPTY}';

    const JOIN = 'JOIN';
    const LEFT_OUTER_JOIN = 'LEFT OUTER JOIN';

    const EQ = '=';
    const NEQ = '<>';
    const GTS_EQ = '>=';
    const LTS_EQ = '<=';
    const IN = 'IN';
    const NOT_IN = 'NOT IN';
    const BEFORE_LIKE = '%~';
    const AFTER_LIKE = '~%';
    const BOTH_LIKE = '%~%';

    public static $signMap = array(
        DBUtil2::EQ             => DBUtil2::EQ
        , DBUtil2::NEQ          => DBUtil2::NEQ
        , DBUtil2::IN           => DBUtil2::IN
        , DBUtil2::NOT_IN       => DBUtil2::NOT_IN
        , DBUtil2::GTS_EQ       => DBUtil2::GTS_EQ
        , DBUtil2::LTS_EQ       => DBUtil2::LTS_EQ
        , DBUtil2::BEFORE_LIKE  => ""
        , DBUtil2::AFTER_LIKE   => ""
        , DBUtil2::BOTH_LIKE    => ""
    );

    /**
     * searchvo 간편 생성
     * @param $sno
     * @return SearchVo
     */
    public static function createSearchVo($sno){
        return new SearchVo('sno=?', $sno);
    }

    public static function getLikeSign($sign){
        $resultArray = array(
            'beforeLike' => ''
        , 'afterLike' => ''
        );
        if( empty(DBUtil2::$signMap[$sign]) ){
            $resultArray['beforeLike'] = DBUtil2::AFTER_LIKE  !== $sign ? "%" : "";
            $resultArray['afterLike']  = DBUtil2::BEFORE_LIKE !== $sign ? "%" : "";
        }
        return $resultArray;
    }

    public static function bind($fieldName, $sign = DBUtil2::EQ, $inCount = 0){
        $like = DBUtil2::getLikeSign($sign);
        $bindStrArray = array();
        $bindStrArray['fieldName'] = $fieldName;
        $bindStrArray['sign'] = DBUtil2::$signMap[$sign];

        if( $sign === DBUtil2::IN || $sign === DBUtil2::NOT_IN ){
            //IN
            $inValueArray = array();
            for($i=0; $i<$inCount; $i++){
                $inValueArray[] = '?';
            }
            $bindStrArray['value'] = '('.implode(',',$inValueArray).')';
        }else if( !empty($like['beforeLike']) || !empty($like['afterLike']) ){
            //Like
            $bindStrArray['sign'] = 'LIKE';
            $bindStrArray['value'] = 'concat(\''.$like['beforeLike'].'\',?,\''.$like['afterLike'].'\')';
        }else{
            //일반
            $bindStrArray['value'] = '?';
        }

        return implode(' ',$bindStrArray);
    }


    public static function getDistinctList($tableName, $distinctFiled, SearchVo $searchVo){
        //Array 이 일 경우는 외부에서 하기.
        $distinctFiled = "DISTINCT {$distinctFiled}";
        $tableInfo = DBUtil2::getTableInfo($tableName, $distinctFiled);
        return DBUtil2::getListBySearchVo($tableInfo, $searchVo);
    }

    //키를 사용한 단순 조회 시
    public static function getOne($tableInfo, $searchKey, $searchValue, $isStrip = true){
        return DBUtil2::getList($tableInfo, $searchKey, $searchValue, null,  $isStrip)[0];
    }
    public static function getOneSortData($tableInfo, $searchKey, $searchValue, $sortKey){
        $searchVo = new SearchVo($searchKey,$searchValue);
        $searchVo->setOrder($sortKey);
        return DBUtil2::getOneBySearchVo($tableInfo, $searchVo);
    }

    /*public static function getOneParse($tableInfo, $searchKey, $searchValue){

    }
    public static function getOneParseSearchVo($tableInfo, $searchKey, $searchValue){

    }*/


    //Max값 가져오기
    public static function getMax($tableInfo, $findField, $searchKey, $searchValue){
        $strSQL = "SELECT MAX({$findField}) AS maxCnt FROM {$tableInfo} WHERE {$searchKey} = '{$searchValue}' ";
        return DBUtil2::runSelect($strSQL,null)[0]['maxCnt'];
    }

    public static function getListLoopAction($tableInfo, $searchVo, $fnc, $isStrip = true){
        $list = DBUtil2::getListBySearchVo($tableInfo, $searchVo, $isStrip);
        foreach($list as $key => $each){
            $each = $fnc($key, $each);
            $list[$key] = $each;
        }
        return $list;
    }

    public static function getList($tableInfo, $searchKey = null, $searchValue = null, $sortKey = null, $isStrip = true){
        $keyList = array();
        $valueList = array();
        if( !is_array($searchKey) ){
            $keyList[] = $searchKey;
            $valueList[] = $searchValue;
        }else{
            $keyList = $searchKey;
            $valueList = $searchValue;
        }
        $searchVo = new SearchVo();
        if( !empty($searchKey) ){
            foreach($keyList as $index => $whereKey){
                $searchVo->setWhere(DBUtil2::bind($whereKey));
                $whereValue = ['value'=>$valueList[$index],'type'=>'s'];
                $searchVo->setWhereValue($whereValue);
            }
        }
        if(!empty($sortKey)){
            $searchVo->setOrder($sortKey);
        }
        return DBUtil2::getListBySearchVo($tableInfo, $searchVo, $isStrip);
    }

    public static function getOneBySearchVo($tableInfo,SearchVo $searchVo){
        return DBUtil2::getListBySearchVo($tableInfo, $searchVo)[0];
    }

    public static function getTableInfo($tableInfo, $setField = null){
        $tableVo = null;
        if( $tableInfo instanceof TableVo){
            $tableVo = $tableInfo;
        }else{
            //테이블 필드 반환 함수명
            $tableName = $tableInfo;
            $tableFunctionName = 'table' . ucfirst(explode('_',$tableName)[1]);
            $tableVo = new TableVo($tableName,$tableFunctionName,'a');
            $tableVo->setField('a.*');
        }
        if(!empty($setField)){
            $tableVo->setField($setField);
        }
        return $tableVo;
    }

    public static function getJoinList($mainTable, array $joinTable, SearchVo $searchVo, $isStrip = true){
        $tableInfo['a'] = [
            'data' => [ $mainTable ] , 'field' => ['a.*']
        ];
        foreach($joinTable as $alias => $info){
            $tableInfo[$alias] = [
                'data' => [$info[0],'LEFT OUTER JOIN',$info[1]],
                'field' => [$info[2]],
            ];
        }
        $tableInfo=DBUtil2::setTableInfo($tableInfo,false);
        return DBUtil2::getComplexList($tableInfo,$searchVo, false, false, $isStrip);
    }

    /**
     * 간단 조인 테이블
     * @param $mainTableInfo
     * @param array $joinTable
     * @param SearchVo $searchVo
     * @param bool $isStrip
     * @return mixed
     */
    public static function getSimpleJoinList($mainTableInfo, array $joinTable, SearchVo $searchVo, $isStrip = true){
        $tableInfo[gd_isset($mainTableInfo['alias'], 'a')] = [
            'data' => [ $mainTableInfo['tableName'] ] , 'field' => [gd_isset($mainTableInfo['field'], 'a.*')]
        ];
        foreach($joinTable as $alias => $info){
            $tableInfo[$alias] = [
                'data' => [$info[0],'LEFT OUTER JOIN',$info[1]],
                'field' => [$info[2]],
            ];
        }
        $tableInfo=DBUtil2::setTableInfo($tableInfo,false);
        return DBUtil2::getComplexList($tableInfo,$searchVo, false, false, $isStrip);
    }

    public static function getListBySearchVo($tableInfo,SearchVo $searchVo, $isStrip = true){
        $tableVo = DBUtil2::getTableInfo($tableInfo);
        return DBUtil2::getComplexList([$tableVo],$searchVo, false, false, $isStrip);
    }

    public static function setWhereCondition(SearchVo $searchVo, &$arrBind){
        $db = \App::getInstance('DB');
        $searchCondition = implode(' AND ', $searchVo->getWhere());
        foreach( $searchVo->getWhereValue() as $key => $value ){
            $db->bind_param_push($arrBind, $value['type'], $value['value']);
        }
        return $searchCondition;
    }

    public static function getComplexListWithPaging(array $tableList,SearchVo $searchVo, $searchData, $isDebug = false, $isStrip = true){
        // --- 페이지 기본설정
        $result = array();

        /**
         * 생성자
         *
         * @param integer $page   현재 페이지
         * @param integer $total  검색 총 레코드수
         * @param integer $amount 총 레코드수
         * @param integer $list   페이지당 리스트 수
         * @param integer $block  페이지 블록 갯수
         * @param string  $url    이동할 페이지 URL
         */
        $page = \App::load('\\Component\\Page\\Page', gd_isset($searchData['page'],1),0,0,gd_isset($searchData['pageNum'],100));
        //$page->setCache(true)->setUrl(\Request::getQueryString()); // 페이지당 리스트 수

        $setPage = $page->recode['start'] . ',' . gd_isset($searchData['pageNum'],100);
        $searchVo->setLimit($setPage);

        //전체 갯수
        $totalData = DBUtil2::getComplexListTotalCount($tableList,$searchVo);
        $result['totalData'] = $totalData;

        $totalCount = $totalData['totalCnt'];

        //검색 데이터
        $searchListData = DBUtil2::getComplexListWithQuery($tableList,$searchVo, false, $isDebug, $isStrip);
        $listData = $searchListData['list'];

        //Total 정보 설정
        $page->setTotal($totalCount);
        //$page->recode['total'] = count($listData); //검색
        $page->recode['amount'] = $totalCount; //전체

        $page->setPage();
        $page->setUrl(\Request::getQueryString());

        $result['queryWithoutPage'] = $searchListData['queryWithoutPage'];
        $result['bindData'] = $searchListData['bind'];

        $result['pageData'] = $page;
        $result['listData'] = $listData;

        return $result;
    }

    public static function getComplexListTotalCount(array $tableList,SearchVo $searchVo){
        return DBUtil2::getComplexList($tableList,$searchVo,true)[0];
    }

    public static function getComplexList(array $tableList,SearchVo $searchVo, $isAllCountMode=false, $isDebug = false, $isStrip = true){
        return DBUtil2::getComplexListWithQuery($tableList,$searchVo,$isAllCountMode,$isDebug,$isStrip)['list'];
    }

    public static function getComplexListWithQuery(array $tableList,SearchVo $searchVo, $isAllCountMode=false, $isDebug = false, $isStrip = true){

        $db = \App::getInstance('DB');

        $tableInfo = '';
        $queryFieldList = array();
        $arrBind = array();

        $strSQLWithoutLimit = '';

        foreach($tableList as $tableListKey => $tableVo){
            $tableName = $tableVo->getTableName();
            $alias = $tableVo->getAlias();
            $tableName .= empty($alias)?'':' as '.$alias;
            $joinType = $tableVo->getJoinType();
            $joinCondition = $tableVo->getJoinCondition();

            if(   strpos($tableVo->getField(), DBUtil2::EMPTY_FILED) !== false ){
            }else{
                $fieldList = empty($tableVo->getField())?implode(', ', DBTableField::setTableField($tableVo->getTableFunctionName(),null,null,$alias)):$tableVo->getField();
                //gd_debug($fieldList);
                $queryFieldList[$tableListKey] = $fieldList;
            }
            if( !empty($joinType) && !empty($joinCondition) ){
                $tableInfo .= ' '.$joinType.' '.$tableName.' ON '.$joinCondition;
            }else{
                $tableInfo = $tableName;
            }
        }

        $queryField = implode(',', $queryFieldList);
        //gd_debug($queryField);

        // Where문 작성
        $searchCondition = array();
        $where  = DBUtil2::setWhereCondition($searchVo,$arrBind);

        $searchCondition['where']  = empty($where)?'':' WHERE '.$where;
        $searchCondition['group']  = empty($searchVo->getGroup())?'':'GROUP BY '.$searchVo->getGroup();
        $searchCondition['having'] = empty($searchVo->getHaving())?'':'HAVING '.$searchVo->getGroup();

        $distinct = $searchVo->getIsDistinct() ? 'distinct':'';
        $mainQuery = 'SELECT '.$distinct .' '.$queryField.' FROM '.$tableInfo.' ';

        if( $isAllCountMode === true ){
            $excludeTableList = $searchVo->getExcludeTableAlias();
            if( !empty($excludeTableList) ){
                foreach($excludeTableList as $excludeTable){
                    unset( $queryFieldList[$excludeTable] );
                }
                $queryField = implode(',', $queryFieldList); //새로 작성
                $mainQuery = 'SELECT '.$distinct .' '.$queryField.' FROM '.$tableInfo.' '; //새로작성
            }
            //카운트 모드 에서는 where, limit, order 빠짐
            $strSQL = 'SELECT COUNT(1) totalCnt '. $searchVo->getAddTotalField() .' FROM ( '. $mainQuery.implode(' ',$searchCondition).') AS countTable';

        }else{
            $strSQLWithoutLimit = $mainQuery.implode(' ',$searchCondition); //limit 하지 않는쿼리

            $searchCondition['order']  = empty($searchVo->getOrder())?'':'ORDER BY '.$searchVo->getOrder();
            $searchCondition['limit']  = empty($searchVo->getLimit())?'':'LIMIT '.$searchVo->getLimit();
            $strSQL = $mainQuery.implode(' ',$searchCondition);
        }

        if( SlCommonUtil::isDevIp() ){
            //gd_debug($strSQL);
            //SitelabLogger::logger2(__METHOD__, $searchVo);
            //SitelabLogger::logger2(__METHOD__, $strSQL);
        }

        if( SlCommonUtil::isDevId() ){
            //SitelabLogger::logger2(__METHOD__, $searchVo);
            //SitelabLogger::logger2(__METHOD__, $strSQL);
            //gd_debug( $searchVo );
            //gd_debug( $strSQL );
        }

        //SitelabLogger::logger2(__METHOD__, $strSQL);

        //SitelabLogger::logger($arrBind);
        //SitelabLogger::logger($strSQL);
        if($isDebug){
            //gd_debug($strSQL);
            //gd_debug($arrBind);
            //SitelabLogger::logger($strSQL);
            //SitelabLogger::logger($arrBind);
        }

        /*SitelabLogger::logger('=====================');
        SitelabLogger::logger($arrBind);
        SitelabLogger::logger($strSQL);*/

        //gd_debug($strSQL);
        //gd_debug($arrBind);

        //안전장치.( SQL에 WHERE 과 Limit 이 없는 경우 강제로 LIMIT 0,1000 붙인다.
        if(strpos($strSQL, 'WHERE') === false && strpos($strSQL, 'LIMIT') === false ) {
            $strSQL .= ' LIMIT 0, 1000 ';
        }

        $result = $db->query_fetch($strSQL, $arrBind);
        //gd_debug($arrBind);
        //gd_debug($strSQL);

        if( DBUtil2::QUERY_DEBUG_MODE === true ){
            gd_debug('========== SELECT DEBUG ==============');
            gd_debug($strSQL);
            gd_debug($arrBind);
        }
        if( DBUtil2::QUERY_DEBUG_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== SELECT DEBUG ==============');
            SitelabLogger::loggerSql($strSQL);
            SitelabLogger::loggerSql($arrBind);
        }

        if( $isStrip ){
            $finalResult = gd_htmlspecialchars_stripslashes($result);
        }else{
            $finalResult = $result;
        }

        return [
            'list' => $finalResult,
            'query' => $strSQL,
            'bind' => $arrBind,
            'queryWithoutPage' => $strSQLWithoutLimit,
        ];
    }

    //일반 텍스트 SQL 실행
    public static function runSelect($strSQL, $arrBind=null, $isStrip = true){
        $db = \App::getInstance('DB');
        //gd_debug($strSQL);
        if( DBUtil2::QUERY_DEBUG_MODE === true ){
            gd_debug('========== SELECT DEBUG ==============');
            gd_debug($strSQL);
            gd_debug($arrBind);
        }
        if( DBUtil2::QUERY_DEBUG_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== SELECT DEBUG ==============');
            SitelabLogger::loggerSql($strSQL);
            SitelabLogger::loggerSql($arrBind);
        }
        $result = $db->query_fetch($strSQL, $arrBind);

        if( $isStrip ) $result = gd_htmlspecialchars_stripslashes($result);

        return $result;
    }

    /**
     * 데이터 삽입
     * @param $tableName
     * @param array $inputInsertData
     * @param $tableFunction
     * @return mixed
     */
    public static function insert($tableName, array $inputInsertData, $tableFunction = null){
        $db = \App::getInstance('DB');

        if( empty($tableFunction) ){
            $tableField = DBTableField::callTableFunction($tableName);
        }else{
            $tableField = DBTableField::$tableFunction();
        }
        $arrBind = $db->get_binding($tableField, $inputInsertData, 'insert');

        /*SitelabLogger::logger('Insert Binding Table ... ');
        SitelabLogger::logger($tableName);
        SitelabLogger::logger('Insert Binding Data ... ');
        SitelabLogger::logger($arrBind);*/

        $db->set_insert_db($tableName, $arrBind['param'], $arrBind['bind'], 'y');

        if( DBUtil2::QUERY_DEBUG_MODE === true ){
            gd_debug('========== INSERT DEBUG ==============');
            gd_debug($db);
        }

        if( DBUtil2::QUERY_DEBUG_LOG_MODE === true && DBUtil2::QUERY_DEBUG_CRUD_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== INSERT DEBUG ==============');
            SitelabLogger::loggerSql($db);
        }

        return $db->insert_id();
    }

    /**
     * 데이터 삭제
     * @param $tableName
     * @param SearchVo $searchVo
     * @throws \Exception
     */
    public static function delete($tableName, SearchVo $searchVo){
        $arrBind = array();
        $db = \App::getInstance('DB');

        $searchCondition = DBUtil2::setWhereCondition($searchVo,$arrBind);

        //안전장치.( SQL에 WHERE이 없는 경우 오류
        if(empty($searchCondition)) {
            throw new \Exception(__('UPDATE문에 WHERE 조건이 빠져있음. 전체 삭제 시 1=1 이라도 넣기'));
        }

        $db->set_delete_db($tableName, $searchCondition, $arrBind);

        if( DBUtil2::QUERY_DEBUG_MODE === true ){
            gd_debug('========== DELETE DEBUG ==============');
            gd_debug($db);
        }

        if( DBUtil2::QUERY_DEBUG_LOG_MODE === true && DBUtil2::QUERY_DEBUG_CRUD_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== DELETE DEBUG ==============');
            SitelabLogger::loggerSql($db);
        }

    }


    /**
     * 일련번호 업데이트
     * @param $tableName
     * @param array $inputUpdateData
     * @param $sno
     * @return mixed
     * @throws \Exception
     */
    public static function updateBySno($tableName, array $inputUpdateData, $sno){
        return DBUtil2::update($tableName, $inputUpdateData, new SearchVo('sno=?', $sno));
    }

    /**
     * 테이블 업데이트
     * @param $tableName
     * @param array $inputUpdateData
     * @param SearchVo $searchVo
     * @param array|null $checkField
     * @return mixed
     * @throws \Exception
     */
    public static function update($tableName, array $inputUpdateData, SearchVo $searchVo, array $checkField = null){
        $db = \App::getInstance('DB');
        $arrBind = array();

        $updateData = array();
        foreach( DBTableField::callTableFunction($tableName) as $each){
            $value = $inputUpdateData[ $each['val'] ];
            if( isset( $value ) ){
                if( $value === 'now()' ){
                    $updateData[] = $each['val'] . '=now()';
                }else{
                    $updateData[] = $each['val'] . '=?';
                    $db->bind_param_push($arrBind, $each['typ']  , empty($value)?$each['def']:$value );
                }
            }else if( !empty($checkField) ){
                //Value가 셋팅 되지 않아도 업데이트 해야하는 것 (체크박스 데이터)
                if( in_array( $each['val'] , $checkField) ){
                    $updateData[] = $each['val'] . '=\'\'';
                }
            }
        }

        //체크 박스 같은 것 안들어오면 Temp로 지정
        $searchCondition = DBUtil2::setWhereCondition($searchVo,$arrBind);
        //안전장치.( SQL에 WHERE이 없는 경우 오류
        if(empty($searchCondition)) {
            SitelabLogger::error('UPDATE문에 WHERE 조건이 빠져있음. 전체 업데이트 시 1=1 이라도 넣기');
            SitelabLogger::error($searchCondition);
            throw new \Exception(__('UPDATE문에 WHERE 조건이 빠져있음. 전체 업데이트 시 1=1 이라도 넣기'));
        }

        $result = $db->set_update_db($tableName, $updateData, $searchCondition, $arrBind);

        if( DBUtil2::QUERY_DEBUG_MODE === true ){
            gd_debug('========== UPDATE DEBUG ==============');
            gd_debug($updateData['data']);
            gd_debug($db);
        }

        if( DBUtil2::QUERY_DEBUG_LOG_MODE === true && DBUtil2::QUERY_DEBUG_CRUD_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== UPDATE DEBUG ==============');
            SitelabLogger::loggerSql($updateData['data']);
            SitelabLogger::loggerSql($db);
        }

        return $result;
    }

    /**
     * Merge
     * @param $tableName
     * @param array $inputMergeData
     * @param SearchVo $searchVo
     * @throws \Exception
     */
    public static function merge($tableName, array $inputMergeData, SearchVo $searchVo){
        if(empty(DBUtil2::getListBySearchVo($tableName,$searchVo))){
            DBUtil2::insert($tableName,$inputMergeData);
        }else{
            DBUtil2::update($tableName, $inputMergeData, $searchVo);
        }
    }

    /**
     * 단순 카운트
     * @param $tableName
     * @param SearchVo $searchVo
     * @return mixed
     */
    public static function getCount($tableName, SearchVo $searchVo){
        $tableVo = new TableVo($tableName,null,'a');
        $tableVo->setField(' count(1) as cnt ');
        return DBUtil2::getOneBySearchVo( $tableVo, $searchVo )['cnt'];
    }

    /**
     * 쿼리에 rowNum 추가
     * @param $strSQL
     * @return string
     */
    public static function addRowNum($strSQL){
        $prefix = "SELECT * FROM (";
        $suffix = ") a ORDER BY a.rowNum";
        return $strSQL = $prefix.$strSQL.$suffix;
    }

    /**
     * 입력 데이터를 업데이트 데이터로 만들어준다.
     * @param $inputData
     * @param $updateField
     * @return array
     */
    public static function makeUpdateData($inputData,$updateField){
        $updateData = array();
        $updateFieldList = explode(',',$updateField);
        foreach($updateFieldList as $updateField){
            $updateData[$updateField] = $inputData[$updateField];
        }
        return $updateData;
    }

    /**
     * 테이블 정보 일괄 셋팅
     * @param $tableList
     * @param bool $isPrefix
     * @return array
     */
    public static function setTableInfo($tableList , $isPrefix = true ){
        $table = [];
        foreach( $tableList as $tableKey => $tableData){
            $tableName = $tableData['data'][0];
            $joinType = $tableData['data'][1];
            $joinCondition = $tableData['data'][2];

            $fncName = null;
            if(  !empty($tableData['fnc']) ){
                $fncName = $tableData['fnc'];
            }

            $table[$tableKey] = new TableVo($tableName,$fncName,$tableKey);
            $fieldList = [];
            foreach($tableData['field'] as $field){
                if( $isPrefix ){
                    $fieldList[] = $tableKey.'.'.$field;
                }else{
                    $fieldList[] = $field;
                }
            }
            $table[$tableKey]->setField(implode(',', $fieldList));
            if( !empty($joinType) ){
                $table[$tableKey]->setJoinType($joinType);
                $table[$tableKey]->setJoinCondition($joinCondition);
            }
        }
        return $table;
    }

    /**
     * 정해진 규칙에 맞게 로그를 백업
     * @param $tableName
     * @param $searchKey
     * @param $searchValue
     */
    public static function logBackup($tableName, $searchKey, $searchValue ){
        $query = "insert into {$tableName}Log select null as logSerial , a.* from {$tableName} a WHERE {$searchKey} = '{$searchValue}'  ";
        DBUtil2::runSelect($query);
    }

    /**
     * @param $sql
     * @return string
     */
    public static function runSql($sql){
        $db = \App::getInstance('DB');
        return empty($db->query($sql)) ? 'fail' : 'complete';
    }

}
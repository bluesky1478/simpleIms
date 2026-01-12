<?php

namespace SlComponent\Database;

use Component\Database\DBTableField;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;

class DBUtil
{
    const QUERY_DEBUG_MODE = false;
    const QUERY_DEBUG_LOG_MODE = false;
    const QUERY_DEBUG_CRUD_LOG_MODE = false;

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
        DBUtil::EQ                => DBUtil::EQ
        , DBUtil::NEQ            => DBUtil::NEQ
        , DBUtil::IN               => DBUtil::IN
        , DBUtil::NOT_IN               => DBUtil::NOT_IN
        , DBUtil::GTS_EQ       => DBUtil::GTS_EQ
        , DBUtil::LTS_EQ        => DBUtil::LTS_EQ
        , DBUtil::BEFORE_LIKE  => ""
        , DBUtil::AFTER_LIKE   => ""
        , DBUtil::BOTH_LIKE    => ""
    );

    public static function getLikeSign($sign){
        $resultArray = array(
            'beforeLike' => ''
            , 'afterLike' => ''
        );
        if( empty(DBUtil::$signMap[$sign]) ){
            $resultArray['beforeLike'] = DBUtil::AFTER_LIKE  !== $sign ? "%" : "";
            $resultArray['afterLike']  = DBUtil::BEFORE_LIKE !== $sign ? "%" : "";
        }
        return $resultArray;
    }

    public static function bind($fieldName, $sign = DBUtil::EQ, $inCount = 0){
        $like = DBUtil::getLikeSign($sign);
        $bindStrArray = array();
        $bindStrArray['fieldName'] = $fieldName;
        $bindStrArray['sign'] = DBUtil::$signMap[$sign];

        if( $sign === DBUtil::IN || $sign === DBUtil::NOT_IN ){
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

    //키를 사용한 단순 조회 시
    public static function getOne($tableInfo, $searchKey, $searchValue){
        return DBUtil::getList($tableInfo, $searchKey, $searchValue)[0];
    }
    public static function getOneSortData($tableInfo, $searchKey, $searchValue, $sortKey){
        $searchVo = new SearchVo($searchKey,$searchValue);
        $searchVo->setOrder($sortKey);
        return DBUtil::getOneBySearchVo($tableInfo, $searchVo);
    }

    //Max값 가져오기
    public static function getMax($tableInfo, $findField, $searchKey, $searchValue){
        $strSQL = "SELECT MAX({$findField}) AS maxCnt FROM {$tableInfo} WHERE {$searchKey} = '{$searchValue}' ";
        return DBUtil::runSelect($strSQL,null)[0]['maxCnt'];
    }

    public static function getList($tableInfo, $searchKey = null, $searchValue = null, $sortKey = null){
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
                $searchVo->setWhere(DBUtil::bind($whereKey));
                $whereValue = ['value'=>$valueList[$index],'type'=>'s'];
                $searchVo->setWhereValue($whereValue);
            }
        }
        if(!empty($sortKey)){
            $searchVo->setOrder($sortKey);
        }
        return DBUtil::getListBySearchVo($tableInfo, $searchVo);
    }

    public static function getOneBySearchVo($tableInfo,SearchVo $searchVo){
        return DBUtil::getListBySearchVo($tableInfo, $searchVo)[0];
    }

    public static function getListBySearchVo($tableInfo,SearchVo $searchVo){
        $tableVo = null;
        if( $tableInfo instanceof TableVo){
            $tableVo = $tableInfo;
        }else{
            //테이블 필드 반환 함수명
            $tableName = $tableInfo;
            $tableFunctionName = 'table' . ucfirst(explode('_',$tableName)[1]);
            $tableVo = new TableVo($tableName,$tableFunctionName);
        }
        return DBUtil::getComplexList([$tableVo],$searchVo);
    }

    public static function setWhereCondition(SearchVo $searchVo, &$arrBind){
        $db = \App::getInstance('DB');
        $searchCondition = implode(' AND ', $searchVo->getWhere());
        foreach( $searchVo->getWhereValue() as $key => $value ){
            $db->bind_param_push($arrBind, $value['type'], $value['value']);
        }
        return $searchCondition;
    }

    public static function getComplexListWithPaging(array $tableList,SearchVo $searchVo, $searchData){
        // --- 페이지 기본설정
        $result = array();

        gd_isset($searchData['page'], 1);
        gd_isset($searchData['pageNum'], 20);
        $page = \App::load('\\Component\\Page\\Page', $searchData['page'],0,0,$searchData['pageNum']);
        //$page->setCache(true)->setUrl(\Request::getQueryString()); // 페이지당 리스트 수

        $setPage = $page->recode['start'] . ',' . $searchData['pageNum'];
        $searchVo->setLimit($setPage);

        //전체 갯수
        $totalCount = DBUtil::getComplexListTotalCount($tableList,$searchVo);

        //검색 데이터
        $listData = DBUtil::getComplexList($tableList,$searchVo);

        //Total 정보 설정
        $page->setTotal($totalCount);
        //$page->recode['total'] = count($listData); //검색
        $page->recode['amount'] = $totalCount; //전체

        $page->setPage();
        $page->setUrl(\Request::getQueryString());

        $result['pageData'] = $page;
        $result['listData'] = $listData;

        return $result;
    }

    public static function getComplexListTotalCount(array $tableList,SearchVo $searchVo){
        return DBUtil::getComplexList($tableList,$searchVo,true)[0]['totalCnt'];
    }

    public static function getComplexList(array $tableList,SearchVo $searchVo, $isAllCountMode=false){

        $db = \App::getInstance('DB');

        $tableInfo = '';
        $queryFieldList = array();
        $arrBind = array();

        foreach($tableList as $tableListKey => $tableVo){
            $tableName = $tableVo->getTableName();
            $alias = $tableVo->getAlias();
            $tableName .= empty($alias)?'':' as '.$alias;
            $joinType = $tableVo->getJoinType();
            $joinCondition = $tableVo->getJoinCondition();

            //gd_debug(implode(', ', DBTableField::setTableField($tableVo->getTableFunctionName(),null,null,$alias)));

            $fieldList = empty($tableVo->getField())?implode(', ', DBTableField::setTableField($tableVo->getTableFunctionName(),null,null,$alias)):$tableVo->getField();
            $queryFieldList[$tableListKey] = $fieldList;

            if( !empty($joinType) && !empty($joinCondition) ){
                $tableInfo .= ' '.$joinType.' '.$tableName.' ON '.$joinCondition;
            }else{
                $tableInfo = $tableName;
            }
        }

        $queryField = implode(',', $queryFieldList);

        // Where문 작성
        $searchCondition = array();
        $where  = DBUtil::setWhereCondition($searchVo,$arrBind);

        $searchCondition['where']  = empty($where)?'':' WHERE '.$where;
        $searchCondition['group']  = empty($searchVo->getGroup())?'':'GROUP BY '.$searchVo->getGroup();
        $searchCondition['having'] = empty($searchVo->getHaving())?'':'HAVING '.$searchVo->getGroup();

        if( $isAllCountMode === true ){
            //카운트 모드 에서는 where, limit, order 빠짐
            $strSQL = 'SELECT COUNT(1) totalCnt FROM ( SELECT '.$queryField.' FROM '.$tableInfo.' '.implode(' ',$searchCondition).') AS countTable';
        }else{
            $searchCondition['order']  = empty($searchVo->getOrder())?'':'ORDER BY '.$searchVo->getOrder();
            $searchCondition['limit']  = empty($searchVo->getLimit())?'':'LIMIT '.$searchVo->getLimit();
            $strSQL = 'SELECT '.$queryField.' FROM '.$tableInfo.' '.implode(' ',$searchCondition);
        }

        //안전장치.( SQL에 WHERE 과 Limit 이 없는 경우 강제로 LIMIT 0,1000 붙인다.
        if(strpos($strSQL, 'WHERE') === false && strpos($strSQL, 'LIMIT') === false ) {
            $strSQL .= ' LIMIT 0, 1000 ';
        }

        $result = $db->query_fetch($strSQL, $arrBind);

        if( DBUtil::QUERY_DEBUG_MODE === true ){
            gd_debug('========== SELECT DEBUG ==============');
            gd_debug($strSQL);
            gd_debug($arrBind);
        }
        if( DBUtil::QUERY_DEBUG_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== SELECT DEBUG ==============');
            SitelabLogger::loggerSql($strSQL);
            SitelabLogger::loggerSql($arrBind);
        }

        if( SlCommonUtil::isDevIp() ){
            //SitelabLogger::logger($strSQL);
            //SitelabLogger::logger($arrBind);
        }

        return gd_htmlspecialchars_stripslashes($result);
    }

    //일반 텍스트 SQL 실행
    public static function runSelect($strSQL, $arrBind){
        $db = \App::getInstance('DB');
        if( DBUtil::QUERY_DEBUG_MODE === true ){
            gd_debug('========== SELECT DEBUG ==============');
            gd_debug($strSQL);
            gd_debug($arrBind);
        }
        if( DBUtil::QUERY_DEBUG_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== SELECT DEBUG ==============');
            SitelabLogger::loggerSql($strSQL);
            SitelabLogger::loggerSql($arrBind);
        }
        $result = $db->query_fetch($strSQL, $arrBind);
        return gd_htmlspecialchars_stripslashes($result);
    }

    /**
     * 데이터 삽입
     * @param $tableName
     * @param array $inputInsertData
     */
    public static function insert($tableName, array $inputInsertData){
        $db = \App::getInstance('DB');
        $arrBind = $db->get_binding(DBTableField::callTableFunction($tableName), $inputInsertData, 'insert');
        $db->set_insert_db($tableName, $arrBind['param'], $arrBind['bind'], 'y');

        if( DBUtil::QUERY_DEBUG_MODE === true ){
            gd_debug('========== INSERT DEBUG ==============');
            gd_debug($db);
        }

        if( DBUtil::QUERY_DEBUG_LOG_MODE === true && DBUtil::QUERY_DEBUG_CRUD_LOG_MODE === true ){
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

        $searchCondition = DBUtil::setWhereCondition($searchVo,$arrBind);

        //안전장치.( SQL에 WHERE이 없는 경우 오류
        if(empty($searchCondition)) {
            throw new \Exception(__('UPDATE문에 WHERE 조건이 빠져있음. 전체 삭제 시 1=1 이라도 넣기'));
        }

        $db->set_delete_db($tableName, $searchCondition, $arrBind);
        
        if( DBUtil::QUERY_DEBUG_MODE === true ){
            gd_debug('========== DELETE DEBUG ==============');
            gd_debug($db);
        }

        if( DBUtil::QUERY_DEBUG_LOG_MODE === true && DBUtil::QUERY_DEBUG_CRUD_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== DELETE DEBUG ==============');
            SitelabLogger::loggerSql($db);
        }

    }

    /**
     * 테이블 업데이트
     * @param $tableName
     * @param array $inputUpdateData
     * @param SearchVo $searchVo
     * @throws \Exception
     */
    public static function update($tableName, array $inputUpdateData, SearchVo $searchVo){
        $db = \App::getInstance('DB');
        $arrBind = array();

        $updateData = array();
        foreach ($inputUpdateData as $key => $value) {
            $updateData[] = $key . '=?';
            $db->bind_param_push($arrBind, 's', $value);
        }

        $searchCondition = DBUtil::setWhereCondition($searchVo,$arrBind);

        //안전장치.( SQL에 WHERE이 없는 경우 오류
        if(empty($searchCondition)) {
            throw new \Exception(__('UPDATE문에 WHERE 조건이 빠져있음. 전체 업데이트 시 1=1 이라도 넣기'));
        }
        $db->set_update_db($tableName, $updateData, $searchCondition, $arrBind);

        if( DBUtil::QUERY_DEBUG_MODE === true ){
            gd_debug('========== UPDATE DEBUG ==============');
            gd_debug($updateData['data']);
            gd_debug($db);
        }

        if( DBUtil::QUERY_DEBUG_LOG_MODE === true && DBUtil::QUERY_DEBUG_CRUD_LOG_MODE === true ){
            SitelabLogger::loggerSql('========== UPDATE DEBUG ==============');
            SitelabLogger::loggerSql($updateData['data']);
            SitelabLogger::loggerSql($db);
        }

    }

    /**
     * Merge
     * @param $tableName
     * @param array $inputMergeData
     * @param SearchVo $searchVo
     * @throws \Exception
     */
    public static function merge($tableName, array $inputMergeData, SearchVo $searchVo){
        if(empty(DBUtil::getListBySearchVo($tableName,$searchVo))){
            DBUtil::insert($tableName,$inputMergeData);
        }else{
            DBUtil::update($tableName,$inputMergeData,$searchVo);
        }
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

}
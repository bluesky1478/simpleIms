<?php

namespace SlComponent\Database;

class TableVo{
    //테이블명
    private $tableName;
    //테이블 필드 함수명
    private $tableFunctionName;
    //테이블 별칭
    private $alias;
    //조인 유형
    private $joinType;
    //조인 조건
    private $joinCondition;
    //검색 필드
    private $field;

    public function __construct($tableName, $tableFunctionName, $alias = null){
        $this->tableName = $tableName;
        if( empty($tableFunctionName) ){
            $tableNameArray = explode('_',$tableName);
            $tableFncName =  'table'.ucfirst($tableNameArray[1]);
            $this->tableFunctionName = $tableFncName;
        }else{
            $this->tableFunctionName = $tableFunctionName;
        }

        $this->alias = $alias;
    }

    public function getField(){
        return $this->field;
    }
    public function setField($field){
        $this->field = $field;
    }
    public function getAlias(){
        return $this->alias;
    }
    public function setAlias($alias){
        $this->alias = $alias;
    }
    public function getJoinType(){
        return $this->joinType;
    }
    public function setJoinType($joinType){
        $this->joinType = $joinType;
    }
    public function getJoinCondition(){
        return $this->joinCondition;
    }
    public function setJoinCondition($joinCondition){
        $this->joinCondition = $joinCondition;
    }
    public function getTableName(){
        return $this->tableName;
    }
    public function setTableName($tableName){
        $this->tableName = $tableName;
    }
    public function getTableFunctionName(){
        return $this->tableFunctionName;
    }
    public function setTableFunctionName($tableFunctionName){
        $this->tableFunctionName = $tableFunctionName;
    }
}
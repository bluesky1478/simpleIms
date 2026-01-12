<?php

namespace SlComponent\Database;

class SearchVo
{
    //테이블명
    private $where = array();
    //테이블 필드 함수명
    private $whereValue = array();
    //정렬 (문자열 입력)
    private $order;
    //그룹 절 (문자열 입력)
    private $group;
    //Having 절 (문자열 입력)
    private $having;
    //Limit 절 (문자열 입력)
    private $limit;
    //명확화 설정
    private $isDistinct;
    //Total 계산할 때 함께 계산
    private $addTotalField;
    //Total 계산할 때 제외할 테이블
    private $excludeTableAlias;

    public function __construct($where=null,$whereValue=null){

        $this->isDistinct = false;

        if(!empty($where)){
            if( is_array($where) ){
                $this->setWhereArray($where);
            }else{
                $this->setWhere($where);
            }
        }
        if(isset($whereValue)){
            if( is_array($where) ){
                $this->setWhereValueArray($whereValue);
            }else{
                $this->setWhereValue($whereValue);
            }
        }
    }

    public function getLimit(){
        return $this->limit;
    }
    public function setLimit($limit){
        $this->limit = $limit;
    }
    public function getWhere(){
        return $this->where;
    }
    public function setWhere($where){
        if( !empty($where) ) $this->where[] = $where;
    }
    public function setWhereArray($where){
        $this->where = array_merge($this->where, $where);
    }
    public function getWhereValue(){
        return $this->whereValue;
    }
    public function setWhereValue($whereValue){
        if( is_array($whereValue) ){
            $this->whereValue[] = $whereValue;
        }else{
            if( isset($whereValue) ) $this->whereValue[] = ['value'=>$whereValue,'type'=>'s'];
        }
    }
    public function setWhereValueArray($whereValue){
        if(!is_array($whereValue[0])){
            foreach($whereValue as $key => $value){
                $whereValue[$key] = ['type'=>'s','value'=>$value];
            }
        }
        $this->whereValue = array_merge($this->whereValue, $whereValue);
    }
    public function getOrder(){
        return $this->order;
    }
    public function setOrder($order){
        $this->order = $order;
    }
    public function getGroup(){
        return $this->group;
    }
    public function setGroup($group){
        $this->group = $group;
    }
    public function getHaving(){
        return $this->having;
    }
    public function setHaving($having){
        $this->having = $having;
    }

    public function setDistinct(){
        $this->isDistinct = true;
    }
    public function getIsDistinct(){
        return $this->isDistinct;
    }

    public function getAddTotalField()
    {
        return $this->addTotalField;
    }
    public function setAddTotalField($addTotalField)
    {
        $this->addTotalField = $addTotalField;
    }
    public function getExcludeTableAlias()
    {
        return $this->excludeTableAlias;
    }
    public function setExcludeTableAlias($excludeTableAlias)
    {
        $this->excludeTableAlias = $excludeTableAlias;
    }

}
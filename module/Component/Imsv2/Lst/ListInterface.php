<?php
namespace Component\Imsv2\Lst;

use SlComponent\Database\SearchVo;

/**
 * IMS 리스트 처리 규약
 */
interface ListInterface {
    public function getListField(); //기본 표현 항목 설정
    public function setCondition($condition,SearchVo $searchVo); //Where 조건 설정
    public function setOrder($sortCondition,SearchVo $searchVo); //Order 조건 설정
    public function getList($params);   //리스트 반환
    public function getTableInfo($params);  //메인 테이블 반환
    public function decoration($each);  //메인 테이블 반환
}

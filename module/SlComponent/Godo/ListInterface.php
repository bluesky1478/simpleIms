<?php
namespace SlComponent\Godo;

/**
 * 리스트 처리 규약
 * @package SlComponent\Godo
 */
interface ListInterface {

    public function getSearch();
    public function setSearch($search);

    /**
     * 리스트 타이틀 목록 반환
     * @param $searchData
     * @return mixed
     */
    public function getTitle($searchData);

    /**
     * 검색 설정 데이터 반환
     * @param $searchData
     * @return mixed
     */
    public function _setSearch($searchData);

    /**
     * 리스트 데이터 반환
     * @param $searchData
     * @return mixed
     */
    public function getList($searchData);



}

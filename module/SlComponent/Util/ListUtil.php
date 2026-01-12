<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

class ListUtil {

    /**
     * 검색 설정
     * @param $listService
     * @param $param
     */
    public static function setSearch($listService, $param){
        $search = $listService->getSearch();
        foreach($param as $key => $value){
            $search[$key] = $value;
        }
        $listService->setSearch($search);
    }

}

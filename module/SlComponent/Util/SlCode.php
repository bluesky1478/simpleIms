<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class SlCode {
    const USE_FL = 'USE_FL';
    const STOCK_TYPE = 'STOCK_TYPE';
    const STOCK_REASON = 'STOCK_REASON';

    public static function getCodeMap($codeType = null){
        $searchVo = new SearchVo();
        $searchVo->setOrder('codeType, codeSort');
        if(!empty($codeType)){
            if(is_array($codeType)){
                $typeList = $codeType;
            }else{
                $typeList = explode(',',$codeType);
            }

            $searchVo->setWhere(DBUtil::bind('codeType',DBUtil::IN, count($typeList)));
            $searchVo->setWhereValueArray($typeList);
        }
        $dbResult = DBUtil::getListBySearchVo('sl_slCode', $searchVo);
        $codeMap = array();
        foreach($dbResult as $key => $value){
            $codeMap[$value['codeType']][$value['codeValue']] = $value['codeNm'];
        }
        return $codeMap;
    }

}

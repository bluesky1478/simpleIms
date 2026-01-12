<?php
namespace SiteLabUtil;

use Component\Database\DBTableField;
use Component\Ims\ImsJsonSchema;
use Component\Scm\ScmHyundaeService;
use Component\Work\WorkCodeMap;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlPostRequestUtil;
use DateTime;
use ReflectionClass;
use ReflectionException;

class FileUtil {

    /**
     * 단순 파일 내용 등록
     * @param $files
     * @param $dataMap
     * @param $tableName
     * @return array
     */
    public static function loadAndInsert($files, $dataMap, $tableName) {
        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){
            $data = [];
            foreach($dataMap as $dataKey => $dataName){
                $data[$dataName] = $val[$dataKey];
            }
            DBUtil2::insert($tableName, $data);
        }
    }

    public static function loadAndMerge($files, $dataMap, $tableName, $mergeCondition) {
        $result = PhpExcelUtil::readToArray($files, 1);
        foreach($result as $index => $val){
            $data = [];
            foreach($dataMap as $dataKey => $dataName){
                $data[$dataName] = $val[$dataKey];
            }

            DBUtil2::getOne($tableName, $data);

            $mergeCondition['key'];
            $mergeCondition['key'];

            DBUtil2::insert($tableName, $data);
        }
    }


}

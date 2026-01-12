<?php


namespace Component\Work\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;

/**
 * 프로젝트 관리 SQL
 */
class ProjectSql{

    const MAIN_TABLE = 'sl_project';

    public function getProjectData($sno){
        $tableList= [
            'a' => //메인
                [
                    'data' => [ self::MAIN_TABLE ]
                    , 'field' => ['*']
                ]
            , 'b' => //등록자
                [
                    'data' => [ DB_MANAGER, 'JOIN', 'a.regManagerSno = b.sno' ]
                    , 'field' => ['managerNm as regManagerName']
                ]
            , 'c' => //고객사
                [
                    'data' => [ 'sl_workCompany', 'LEFT OUTER JOIN', 'a.companySno = c.sno' ]
                    , 'field' => ['companyName']
                ]
            , 'd' => //영업담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.salesManagerSno = d.sno' ]
                    , 'field' => ['managerNm as salesManagerName', 'phone as salesManagerPhone', 'cellPhone as salesManagerCellPhone']
                ]
            , 'e' => //디자인담당자
                [
                    'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.designManagerSno = e.sno' ]
                    , 'field' => ['managerNm as designManagerName', 'phone as designManagerPhone', 'cellPhone as designManagerCellPhone']
                ]
        ];
        $table = DBUtil2::setTableInfo($tableList);
        //Search
        $searchVo = new SearchVo('a.sno=?' , $sno);

        //SitelabLogger::logger( DBUtil2::getComplexList($table ,$searchVo, false, false, false)[0] );

        return DBUtil2::getComplexList($table ,$searchVo, false, false, false)[0];
    }

}
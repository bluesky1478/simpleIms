<?php


namespace Component\Claim\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 반품 처리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class ReturnListSql{

    /**
     * 반품 리스트
     * @param $searchData
     * @return array
     */
    public function getReturnList($searchData){

        $tableList = [
            'a' =>
                [
                    'data' => [ 'sl_3plReturnList' ]
                    , 'field' => ['*']
                ]
        ];

        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = new SearchVo();

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        //처리 상태
        if( !empty($searchData['returnStatus']) && 'all' !== $searchData['returnStatus'] ){
            //공급사 검색
            $searchVo->setWhere('a.returnStatus=?');
            $searchVo->setWhereValue( $searchData['returnStatus']  );
        }

        //공급사
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('a.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil::bind('a.scmNo', DBUtil::IN, count($searchData['scmNo']) ));
                $searchVo->setWhereValueArray( $searchData['scmNo']  );
            }
        }

        //검색어
        if( !empty($searchData['keyword']) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::BOTH_LIKE ) );
            $searchVo->setWhereValue( $searchData['keyword'] );
        }
        //기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0].' 00:00:00' );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1].' 23:59:59'  );
        }

        return DBUtil2::getComplexListWithPaging($table ,$searchVo, $searchData, false, false);

    }
}
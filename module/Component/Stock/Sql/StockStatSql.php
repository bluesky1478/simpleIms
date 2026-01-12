<?php


namespace Component\Stock\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 상품 재고 관리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class StockStatSql{

    public function getStockList($searchData){

        $table['a'] = new TableVo('sl_goodsStock','tableGoodsStock','a');
        $table['a']->setField(' 
            a.goodsNo
            , a.optionNo
            , sum(if(a.stockType=1, a.stockCnt, 0)) AS inStock
            , sum(if(a.stockType=2, abs(a.stockCnt), 0)) AS outStock'
        );
        //'.',\''.$searchData['searchDate'][0].'~'.$searchData['searchDate'][0].'\' AS searchPeriod'

        $table['b'] = new TableVo(DB_GOODS,'tableGoods','b');
        $table['b']->setField('
            b.goodsNm                    
        ');

        $table['c'] = new TableVo(DB_GOODS_OPTION,'tableGoodsOption','c');
        $table['c']->setField('
            concat(c.optionValue1,c.optionValue2,c.optionValue3,c.optionValue4,c.optionValue5) AS optionNm
        ');

        $table['e'] = new TableVo(DB_SCM_MANAGE,'tableScmManage','e');
        $table['e']->setField('
            e.companyNm
        ');

        //JoinType
        $table['b']->setJoinType('LEFT OUTER JOIN');
        $table['c']->setJoinType('LEFT OUTER JOIN');
        $table['e']->setJoinType('LEFT OUTER JOIN');
        //Join Condition
        $table['b']->setJoinCondition('a.goodsNo = b.goodsNo');
        $table['c']->setJoinCondition('a.goodsNo = c.goodsNo AND a.optionNo = c.optionNo');
        $table['e']->setJoinCondition('b.scmNo = e.scmNo');

        //Search
        $searchVo = new SearchVo();

        //Group
        $searchVo->setGroup('
            a.goodsNo
            , b.goodsNm
            , a.optionNo
            , e.companyNm
            , concat(c.optionValue1,c.optionValue2,c.optionValue3,c.optionValue4,c.optionValue5)
        ');

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        //1. 공급사
        //scmNo
        if( !empty($searchData['scmFl']) && 'all' !== $searchData['scmFl']  ){
            if( 'n' === $searchData['scmFl']){
                //본사
                $searchVo->setWhere('b.scmNo = ?');
                $searchVo->setWhereValue('1');
            }else{
                //공급사 검색
                $searchVo->setWhere(DBUtil::bind('b.scmNo', DBUtil::IN, count($searchData['scmNo']) ));
                $searchVo->setWhereValueArray( $searchData['scmNo']  );
            }
        }

        //2. 검색어
        if( !empty($searchData['keyword']) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::AFTER_LIKE ) );
            $searchVo->setWhereValue( $searchData['keyword'] );
        }

        //5. 기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0].' 00:00:00' );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1].' 23:59:59'  );
        }
        //gd_debug($searchData);
        return DBUtil::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

}
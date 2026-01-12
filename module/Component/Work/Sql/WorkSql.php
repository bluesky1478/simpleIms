<?php


namespace Component\Work\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 상품 재고 관리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class WorkSql{

    public function getStockList($searchData){

        $table['a'] = new TableVo('sl_goodsStock','tableGoodsStock','a');
        $table['a']->setField(' 
            a.sno
            , a.goodsNo
            , a.optionNo
            , a.stockType
            , a.stockReason
            , a.stockCnt
            , a.orderNo
            , a.memNo
            , a.regDt
            , if(a.stockCnt>0,\'text-red\',\'text-blue\') AS stockCntColor  
        ');

        $table['b'] = new TableVo(DB_GOODS,'tableGoods','b');
        $table['b']->setField('
            b.goodsNm                    
        ');

        $table['c'] = new TableVo(DB_GOODS_OPTION,'tableGoodsOption','c');
        $table['c']->setField('
            concat(c.optionValue1,c.optionValue2,c.optionValue3,c.optionValue4,c.optionValue5) AS optionNm
        ');

        $table['d'] = new TableVo(DB_MEMBER,'tableMember','d');
        $table['d']->setField('
            d.memNm
            , d.memId                    
        ');

        $table['e'] = new TableVo(DB_SCM_MANAGE,'tableScmManage','e');
        $table['e']->setField('
            e.companyNm
        ');

        //JoinType
        $table['b']->setJoinType('LEFT OUTER JOIN');
        $table['c']->setJoinType('LEFT OUTER JOIN');
        $table['d']->setJoinType('LEFT OUTER JOIN');
        $table['e']->setJoinType('LEFT OUTER JOIN');
        //Join Condition
        $table['b']->setJoinCondition('a.goodsNo = b.goodsNo');
        $table['c']->setJoinCondition('a.goodsNo = c.goodsNo AND a.optionNo = c.optionNo');
        $table['d']->setJoinCondition('a.memNo = d.memNo');
        $table['e']->setJoinCondition('b.scmNo = e.scmNo');

        //Search
        $searchVo = new SearchVo();

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

        //3. 유형
        if( !empty($searchData['stockType']) ){
            $searchVo->setWhere( 'a.stockType = ?');
            $searchVo->setWhereValue( $searchData['stockType']  );
        }

        //4. 사유
        if( !empty($searchData['stockReason'])  && 'all' !== $searchData['stockReason'][0]  ){
            $searchVo->setWhere(DBUtil::bind('a.stockReason', DBUtil::IN, count($searchData['stockReason']) ));
            $searchVo->setWhereValueArray( $searchData['stockReason'] );
        }

        //5. 기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0].' 00:00:00' );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1].' 23:59:59'  );
        }

        //상세 리스트에서 사용
        //상품번호 eq
        if( !empty($searchData['goodsNo']) ){
            $searchVo->setWhere( 'a.goodsNo = ?');
            $searchVo->setWhereValue( $searchData['goodsNo']  );
        }
        //옵션번호 eq
        if( !empty($searchData['optionNo']) ){
            $searchVo->setWhere( 'a.optionNo = ?');
            $searchVo->setWhereValue( $searchData['optionNo']  );
        }

        //gd_debug($searchData);
        return DBUtil::getComplexListWithPaging($table ,$searchVo, $searchData);
    }

}
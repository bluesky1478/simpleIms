<?php


namespace Component\Claim\Sql;


use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 클레임 처리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class ClaimSql{

    /**
     * 클레임 리스트
     * @param $searchData
     * @return array
     */
    public function getClaimList($searchData){

        $table['a'] = new TableVo('sl_claimHistoryGoods',null,'a');
        $table['a']->setField('
            a.sno AS claimGoodsSno
            , a.handleGroupCd
            , a.handleSno
            , a.reqGoodsSno	 	 
	        , a.reqGoodsCnt  		
        ');
        
        $table['b'] = new TableVo('sl_claimHistory',null,'b');
        $table['b']->setField('
            b.sno
            , b.orderNo
            , b.memNo
            , b.scmNo
            , b.claimType
	        , b.reqContents			
	        , b.reqType	 
	        , b.procStatus  
	        , b.procContents		
	        , b.procDt 
	        , b.memberMemo			
	        , b.adminMemo			
	        , b.regDt 
	        , b.modDt  
        ');

        $table['c'] = new TableVo('es_orderGoods',null,'c');
        $table['c']->setField('
            c.goodsNm         
            , c.optionInfo           
            , c.goodsNo
            , c.invoiceNo
        ');

        $table['d'] = new TableVo('es_member',null,'d');
        $table['d']->setField('
            d.memNm         
            , d.memId           
        ');

        $table['e'] = new TableVo('es_scmManage',null,'e');
        $table['e']->setField('              
            e.companyNm           
        ');

        $table['f'] = new TableVo('sl_claimRequestType',null,'f');
        $table['f']->setField('              
            f.reqTypeContents           
        ');

        //JoinType
        $table['b']->setJoinType('JOIN');
        $table['c']->setJoinType('JOIN');
        $table['d']->setJoinType('JOIN');
        $table['e']->setJoinType('JOIN');
        $table['f']->setJoinType('LEFT OUTER JOIN');

        //Join Condition
        $table['b']->setJoinCondition('a.orderNo = b.orderNo AND a.claimSno = b.sno');
        $table['c']->setJoinCondition('a.orderNo = c.orderNo AND a.reqGoodsSno = c.sno');
        $table['d']->setJoinCondition('b.memNo = d.memNo');
        $table['e']->setJoinCondition('b.scmNo = e.scmNo');
        $table['f']->setJoinCondition('b.claimType = f.claimType AND b.reqType = f.sno');

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

        //5. 기간
        if( !empty( $searchData['searchDate'][0] )  && !empty( $searchData['searchDate'][1] ) ){
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::GTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][0].' 00:00:00' );
            $searchVo->setWhere( DBUtil::bind( $searchData['searchDateFl']  , DBUtil::LTS_EQ ) );
            $searchVo->setWhereValue( $searchData['searchDate'][1].' 23:59:59'  );
        }

        return DBUtil::getComplexListWithPaging($table ,$searchVo, $searchData);

    }
}
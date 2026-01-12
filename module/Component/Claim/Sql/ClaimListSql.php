<?php


namespace Component\Claim\Sql;


use Component\Member\Util\MemberUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 클레임 처리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class ClaimListSql{

    /**
     * 클레임 리스트
     * @param $searchData
     * @return array
     */
    public function getClaimList($searchData){

        $tableList = [
            'a' => //메인 - 클레임 데이타
                [
                    'data' => [ 'sl_scmClaimData' ]
                    , 'field' => ['*']
                ]
            , 'b' => [
                'data' => [ 'es_bd_qa', 'LEFT OUTER JOIN', 'a.bdSno = b.sno' ]
                , 'field' => ['memNo','contents','answerContents']
            ]
            , 'c' => [ //주 .
                'data' => [ DB_MEMBER, 'LEFT OUTER JOIN', 'b.memNo = c.memNo' ]
                , 'field' => ['memNm', 'cellPhone', 'memId', 'nickNm' ]
            ]
            , 'd' => [
                'data' => [ DB_SCM_MANAGE, 'LEFT OUTER JOIN', 'a.scmNo = d.scmNo' ]
                , 'field' => ['companyNm' ]
            ]
            , 'o' => [
                    'data' => [ DB_ORDER, 'LEFT OUTER JOIN', 'a.orderNo = o.orderNo' ]
                    , 'field' => ['orderStatus']
                ]
            , 'oi' => [
                    'data' => [ DB_ORDER_INFO, 'LEFT OUTER JOIN', 'o.orderNo = oi.orderNo' ]
                    , 'field' => ['orderCellPhone','receiverCellPhone'] // cellPhone -> orderCellPhone -> receiverCellPhone
                ]
            , 'rt' => [
                    'data' => [ 'sl_3plReturnList', 'LEFT OUTER JOIN', 'a.sno = rt.claimSno' ]
                    , 'field' => ['returnStatus','prdStatus','partnerMemo','sno as rtSno'] // cellPhone -> orderCellPhone -> receiverCellPhone
                ]
        ];

        $table = DBUtil2::setTableInfo($tableList);

        //Search
        $searchVo = new SearchVo();

        //정렬 설정
        $searchVo->setOrder($searchData['sort']);

        if( 34 != SlCommonUtil::getManagerInfo()['scmNo'] ){
            $searchVo->setWhere('a.claimStatus <> ?');
            $searchVo->setWhereValue(0);
        }

        //클레임 처리 상태
        if( !empty($searchData['claimStatus']) ){
            //공급사 검색
            $searchVo->setWhere('a.claimStatus=?');
            $searchVo->setWhereValue( $searchData['claimStatus']  );
        }

        //클레임 타입
        if( !empty($searchData['claimType']) && 'all' !== $searchData['claimType'][0] ){
            $searchVo->setWhere(DBUtil::bind('a.claimType', DBUtil::IN, count($searchData['claimType']) ));
            $searchVo->setWhereValueArray( $searchData['claimType']  );
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
            $searchVo->setWhere( DBUtil::bind( $searchData['key'], DBUtil::AFTER_LIKE ) );
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
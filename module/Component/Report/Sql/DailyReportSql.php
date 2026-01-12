<?php


namespace Component\Report\Sql;


use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;

/**
 * 상품 재고 관리 SQL
 * Class GoodsStockSql
 * @package Component\Goods\Sql
 */
class DailyReportSql{

    public function commonBind($db,&$arrBind,$searchData){
        $startDate = $searchData['searchDate'];
        $endDate = $searchData['searchDate'];
        if( 7 === strlen($searchData['searchDate']) ){
            $startDate = $searchData['searchDate'].'-01';
            $endDate = $searchData['searchDate'].'-31';
        }
        $db->bind_param_push($arrBind, 's', $startDate);
        $db->bind_param_push($arrBind, 's', $endDate);
        $db->bind_param_push($arrBind, 'i', $searchData['scmNo']);
    }

    /**
     * 집계
     * @param $searchData
     * @return mixed
     */
    public function getStat($searchData){
        $strSQL = "
            SELECT SUM(orderCnt)           AS orderCnt          -- 주문건     
                     , SUM(orderGoodsCnt)  AS orderGoodsCnt  -- 주문수량
                     , SUM(settlePrice)         AS settlePrice         -- 주문금액
                     , SUM(backCnt)           AS backCnt            -- 반품
                     , SUM(exchangeCnt)    AS exchangeCnt      -- 교환
                     , SUM(refundCnt)        AS refundCnt          -- 환불
                     , SUM(asCnt)              AS asCnt                -- AS
             FROM (
                        SELECT COUNT(1) AS orderCnt
                                 , SUM(a.orderGoodsCnt)  AS orderGoodsCnt
                                 , SUM(a.settlePrice) AS settlePrice
                                 , 0 AS backCnt
                                 , 0 AS refundCnt
                                 , 0 AS exchangeCnt
                                 , 0 AS asCnt
                          FROM es_order a 
                            JOIN es_orderPayHistory b
                             ON a.orderNo = b.orderNo
                            JOIN sl_orderScm c 
                              ON a.orderNo = c.orderNo
                        WHERE a.regDt >= concat(?,' 00:00:00')
                           AND a.regDt <= concat(?,' 23:59:59')
                           AND c.scmNo = ?
                        
                        UNION ALL 
                        
                        SELECT 0           AS orderCnt      
                                 , 0           AS orderGoodsCnt
                                 , 0           AS settlePrice      
                                 , SUM(if(claimType='back',1,0))         AS backCnt         
                                 , SUM(if(claimType='refund',1,0))      AS refundCnt         
                                 , SUM(if(claimType='exchange',1,0))  AS exchangeCnt   
                                 , SUM(if(claimType='as',1,0))            AS asCnt             
                        FROM sl_claimHistory a     	                                                           
                      WHERE a.regDt >= concat(?,' 00:00:00')
                         AND a.regDt <= concat(?,' 23:59:59')
                         AND a.scmNo = ?
            ) a
            ";
        $arrBind = [];
        $db = \App::getInstance('DB');

        for($i=0; $i<2; $i++){
            $this->commonBind($db,$arrBind,$searchData);
        }

        return DBUtil::runSelect($strSQL, $arrBind);
    }

    /**
     * 주문상세
     * @param $searchData
     * @return
     */
    public function getOrderStat($searchData){
        $strSQL = "
                SELECT @ROWNUM := @ROWNUM + 1 AS rowNum
                , a.orderNo
                , a.orderGoodsNm
                , a.orderGoodsCnt
                , a.settlePrice 
                , a.regDt
                FROM es_order a
                  JOIN sl_orderScm b 
                    ON a.orderNo = b.orderNo
                , (SELECT @ROWNUM := 0) TMP
                WHERE a.regDt >= concat(?,' 00:00:00')
                   AND a.regDt <= concat(?,' 23:59:59')
                   AND b.scmNo = ?   
                 ORDER BY a.regDt DESC 
        ";

        $strSQL = DBUtil::addRownum($strSQL);

        $arrBind = [];
        $db = \App::getInstance('DB');
        $this->commonBind($db,$arrBind,$searchData);
        return DBUtil::runSelect($strSQL, $arrBind);
    }

    /**
     * 교환, 반품, AS
     * @param $searchData
     * @param $handleCase
     */
    public function getHandleData($searchData, $handleCase){
        //주문번호	상품주문번호	상품번호	옵션명	수량	사유	처리내용 (sno desc)
        $strSQL = "
            SELECT @ROWNUM := @ROWNUM + 1 AS rowNum 
                     , a.orderNo
                     , a.userHandleGoodsNo
                     , b.goodsNo
                     , b.optionInfo
                     , a.userHandleGoodsCnt
                     , a.userHandleReason
                     , a.adminHandleReason
                     , a.regDt
             FROM es_orderUserHandle a
               JOIN es_orderGoods b
                 ON a.orderNo = b.orderNo
               AND a.userHandleGoodsNo = b.sno
                    , (SELECT @ROWNUM := 0) TMP
           WHERE a.regDt >= concat(?,' 00:00:00')
              AND a.regDt <= concat(?,' 23:59:59')
              AND b.scmNo = ?    
              AND a.userHandleMode = ?
          ORDER BY a.regDt DESC
        ";

        $strSQL = DBUtil::addRownum($strSQL);
        $arrBind = [];
        $db = \App::getInstance('DB');
        $this->commonBind($db,$arrBind,$searchData);
        $db->bind_param_push($arrBind, 's', $handleCase);
        $listData = DBUtil::runSelect($strSQL, $arrBind);;
        return $listData;
    }

}
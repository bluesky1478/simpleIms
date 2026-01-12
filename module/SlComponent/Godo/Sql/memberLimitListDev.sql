SELECT a.memNo,
       a.memId,
       a.memNm,
       a.nickNm,
       b.buyLimitCount,
       ( SELECT ifnull(sum(goodsCnt), 0)
           FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
          WHERE aa.memNo = a.memNo
            AND LEFT(bb.orderStatus,1) IN ( 'g','p','d','e', 's', 'o' )
            AND bb.goodsNo = '1000002052' ) -
       ( SELECT ifnull(sum(goodsCnt),0) FROM es_cart WHERE memNo=a.memNo AND goodsNo='1000002052') as usedCount1,
       ( SELECT ifnull(sum(goodsCnt), 0)
           FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
          WHERE aa.memNo = a.memNo
            AND LEFT(bb.orderStatus,1) IN ( 'g','p','d','e', 's', 'o' )
            AND bb.goodsNo = '1000002051' ) -
       ( SELECT ifnull(sum(goodsCnt),0) FROM es_cart WHERE memNo=a.memNo AND goodsNo='1000002051') as usedCount2
 FROM es_member a
 JOIN sl_setMemberConfig b
   ON a.memNo = b.memNo
WHERE a.ex1 = 'TKE'
  AND b.memberType = 2

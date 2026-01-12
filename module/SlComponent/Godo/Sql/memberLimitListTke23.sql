SELECT a.memNo,
       a.memId,
       a.memNm,
       a.nickNm,
       b.buyLimitCount,
       ( SELECT ifnull(sum(goodsCnt), 0)
         FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
         WHERE aa.memNo = a.memNo
           AND LEFT(bb.orderStatus,1) IN ( 'g','p','d','e', 's', 'o' )
    AND bb.goodsNo = '1000000329' ) -
    ( SELECT ifnull(sum(goodsCnt),0) FROM es_cart WHERE memNo=a.memNo AND goodsNo='1000000247') as usedCount1,
                                                      ( SELECT ifnull(sum(goodsCnt), 0)
FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
WHERE aa.memNo = a.memNo
  AND LEFT(bb.orderStatus,1) IN ( 'g','p','d','e', 's', 'o' )
  AND bb.goodsNo = '1000000331' ) -
    ( SELECT ifnull(sum(goodsCnt),0) FROM es_cart WHERE memNo=a.memNo AND goodsNo='1000000246') as usedCount2
FROM es_member a
    JOIN sl_setMemberConfig b
ON a.memNo = b.memNo
WHERE a.ex1 = 'TKE(티센크루프)'
  AND b.memberType = 2
  AND a.memNo <> 53 -- TEST



select * from (
SELECT a.memNo,
       a.memId,
       a.memNm,
       a.nickNm,
       a.cellPhone,
       b.buyLimitCount,
       ( SELECT ifnull(sum(goodsCnt), 0)
         FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
         WHERE aa.memNo = a.memNo
           AND LEFT(bb.orderStatus,1) IN ( 'g','p','d','e', 's', 'o' )
      AND bb.goodsNo = '1000000406' ) as usedCount1,  -- -( SELECT ifnull(sum(goodsCnt),0) FROM es_cart WHERE memNo=a.memNo AND goodsNo='1000000390')

      ( SELECT ifnull(sum(goodsCnt), 0)
        FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
        WHERE aa.memNo = a.memNo
          AND LEFT(bb.orderStatus,1) IN ( 'g','p','d','e', 's', 'o' )
          AND bb.goodsNo = '1000000405' ) as usedCount2 -- - ( SELECT ifnull(sum(goodsCnt),0) FROM es_cart WHERE memNo=a.memNo AND goodsNo='1000000392')
FROM es_member a
    JOIN sl_setMemberConfig b
ON a.memNo = b.memNo
WHERE a.ex1 = '오티스(OEK)'
  AND b.memberType = 1
  AND a.memNo NOT IN( 15221 , 15157 , 15156, 1)  -- TEST
) a
where 1=1

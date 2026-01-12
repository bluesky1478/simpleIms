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
           AND LEFT(bb.orderStatus,1) IN ( 'p','d','s',  'o' )
    AND bb.goodsNo = '1000000591' ) as usedCount1,

      ( SELECT ifnull(sum(goodsCnt), 0)
         FROM es_order aa JOIN es_orderGoods bb ON aa.orderNo = bb.orderNo
         WHERE aa.memNo = a.memNo
           AND LEFT(bb.orderStatus,1) IN ( 'p','d','s', 'o' )
           AND bb.goodsNo = '1000000593' ) as usedCount2

FROM es_member a
    JOIN sl_setMemberConfig b
ON a.memNo = b.memNo
WHERE a.ex1 = 'TKE(티센크루프)'
  -- AND b.memberType = 1 --정규직
  AND a.groupSno = 5
  AND a.memNo NOT IN( 53 , 5469 )  -- TEST
) a
where 1=1


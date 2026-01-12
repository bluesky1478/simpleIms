select b.deliveryName,
       count(1) cnt,
       sum( if ( d.goodsNo = '1000000328' , 1, 0)) as teeCnt,
       sum( if ( d.goodsNo = '1000000330', 1, 0 )) as pantsCnt
from es_member a
         join sl_setMemberConfig b on a.memNo = b.memNo
         join es_order c on a.memNo = c.memNo
         join es_orderGoods d on c.orderNo = d.orderNo
where b.memberType <> 2
  and ex1 = 'TKE(티센크루프)'
  and d.goodsNo in (
                    1000002052, 1000002051
    ) group by b.deliveryName order by b.deliveryName

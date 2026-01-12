select b.deliveryName,
       0 cnt,
       sum( if ( d.goodsNo = '1000000328' , 1, 0)) as teeCnt,
       sum( if ( d.goodsNo = '1000000330', 1, 0 )) as pantsCnt
from es_member a
         join sl_setMemberConfig b on a.memNo = b.memNo
         join es_order c on a.memNo = c.memNo
         join es_orderGoods d on c.orderNo = d.orderNo
where b.memberType <> 2
  and ex1 = 'TKE(티센크루프)'
  and a.memNo NOT IN (  1 , 4, 5469, 4991  )
  and d.orderStatus = 'p3'
  and d.goodsNo in ('1000000328', '1000000330') group by b.deliveryName order by b.deliveryName

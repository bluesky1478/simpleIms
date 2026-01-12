INSERT INTO sl_goodsOptionExt(goodsNo, optionSno, reserveCnt, realCnt, inCnt, outCnt, otherMappingCnt, realCntOfYear, regDt)
select
    goodsNo,
    optionSno,
    sum(reserveCnt) as reserveCnt,
    sum(realCnt) as realCnt,
    sum(inCnt) as inCnt,
    sum(outCnt) as outCnt,
    sum(otherCnt) as otherMappingCnt,
    group_concat(prdYear) as prdYear,
    now()
from
    (
        -- 예약 수량
        select
            a.goodsNo,
            a.optionSno,
            a.goodsCnt as reserveCnt,
            0 as realCnt,
            0 as inCnt,
            0 as outCnt,
            0 as otherCnt,
            ''  as prdYear
        from es_orderGoods a
        join es_goods b on a.goodsNo = b.goodsNo
        where a.orderStatus in ( 'o1', 'p1', 'p2', 'p3', 'g1', 'g3' )

        union all

        -- 실제 수량
        select
            goodsNo,
            optionSno,
            0 as reserveCnt,
            b.stockCnt-ifnull(otherCnt,0) as realCnt,
            0 as inCnt,
            0 as outCnt,
            a.otherCnt,
            concat(b.attr5,':',b.stockCnt-ifnull(otherCnt,0))  as prdYear
        from sl_goodsOptionLink a
        join sl_3plProduct b
          on a.code = b.thirdPartyProductCode

        union all

        -- 입/출고 수량

        select
            a.goodsNo,
            a.optionSno,
            0 as reserveCnt,
            0 as realCnt,
            sum(inCnt) as inCnt,
            sum(outCnt) as outCnt,
            0 as otherCnt,
            ''  as prdYear
         from sl_goodsOptionLink a
         join sl_3plProduct b
           on a.code = b.thirdPartyProductCode
        -- WHERE b.attr5 = 25 -- 아시아나 임시 (TODO 나중에 연도별 출고수량 다로 만들어야겠다.)
        group by a.goodsNo, a.optionSno

    ) a
group by goodsNo, optionSno
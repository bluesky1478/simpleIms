update sl_3plProduct p
    join (
    select thirdPartyProductCode,
        sum(case when inOutType = 1 then quantity else 0 end) as inCnt,
        sum(case when inOutType = 2 then quantity else 0 end) as outCnt
    from sl_3plStockInOut
    group by thirdPartyProductCode
) b on p.thirdPartyProductCode = b.thirdPartyProductCode
set p.inCnt  = b.inCnt,
    p.outCnt = b.outCnt
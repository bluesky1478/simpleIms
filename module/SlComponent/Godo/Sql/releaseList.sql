( SELECT orderNo
       , a.customerName as receiverName
       , a.zipcode as receiverZoneCode
       , a.address
       , a.phone as receiverPhone
       , a.mobile as receiverCellPhone
       , a.productCode as optionCode
       , a.productName as productName
       , a.qty as goodsCnt
       , d.stockCnt as stockCnt
       , a.remark as orderMemo
       , a.scmName
       , a.scmNo
       , '' as memo1
       , '' as memo2
       , '' as memo3
       , '' as handleSno
       , '' as orderGoodsSno
       , '' as goodsNo
       , '' as memNo
  FROM sl_3plOrderTmp a
           LEFT OUTER JOIN sl_3plProduct d ON a.productCode = d.thirdPartyProductCode
  WHERE a.productCode is not null
    AND a.productCode <> ''
)

UNION ALL

( SELECT a.orderNo
       , if(d.scmNo=34,concat(asiana.empTeam,asiana.empPart1,asiana.empPart2),b.receiverName) as receiverName
       , b.receiverZoneCode
       , concat(b.receiverAddress, ' ', b.receiverAddressSub, if(b.receiverName='공종원',memCfg.teamName,'') ) as address
       , b.receiverPhone
       , b.receiverCellPhone
       , c.optionCode
       , if(d.optionName <> '', concat(d.productName,'_',d.optionName), d.productName) as productName
       , a.goodsCnt
       , d.stockCnt
       , b.orderMemo
       , d.scmName
       , d.scmNo
       , IF( sorder.orderNo is not null AND 0 = handleSno , '', '') as memo1 -- 한타
       , IF( ( e.deliveryCollectPrice > 0 OR 4 = a.scmNo OR ( handleSno > 0 AND 'later' = goodsDeliveryCollectFl ) ) , '택배착불', '') as memo2 -- 착불 (착불선택, 제일건설, 교환)
       , IF( f.memNo = 1662 , '사이즈수량표기' , ''  ) as memo3 -- 사이즈수량 표기 고객
       , a.handleSno
       , a.sno as orderGoodsSno
       , a.goodsNo
       , f.memNo
  FROM es_orderGoods a
           JOIN es_orderInfo b
                ON a.orderNo = b.orderNo
           JOIN es_goodsOption c
                ON a.optionSno = c.sno
           JOIN sl_3plProduct d
                ON c.optionCode = d.thirdPartyProductCode
           JOIN es_orderDelivery e
                ON a.orderDeliverySno = e.sno
           JOIN es_order f
                ON a.orderNo = f.orderNo
           LEFT  JOIN sl_orderAccept oac
                      ON a.orderNo = oac.orderNo
           LEFT  JOIN sl_setScmConfig scon
                      ON scon.scmNo = a.scmNo
           LEFT  JOIN sl_orderAddedData as sorder
                      ON a.orderNo = sorder.orderNo
           LEFT OUTER JOIN sl_asianaOrderHistory asiana
                           ON a.sno = asiana.orderGoodsSno
           LEFT OUTER JOIN sl_setMemberConfig memCfg
                           ON f.memNo = memCfg.memNo

  WHERE a.paymentDt >= '{$startDate} 00:00:00'
    AND '{$today} 23:59:59' >= a.paymentDt
    AND ( oac.orderAcctStatus = '2' or scon.orderAcceptFl <> 'y' )  -- 승인여부
    AND ( '{$today}' >= sorder.reqDeliveryDt or sorder.reqDeliveryDt is null ) -- 한국타이어 배송요청일
    AND a.orderStatus = 'p1' )
ORDER BY scmNo desc, receiverName, address, receiverName, handleSno -- 교환을 가장 뒤로.
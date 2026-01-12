<table class="table table-rows">
    <colgroup>
        <col style="width:50px"/>
        <col style="width:80px"/>
        <col style="width:150px"/>
        <col style="width:200px"/>
        <col style="width:150px"/>
        <col />
        <col style="width:130px"/><!--결제금액-->
        <col style="width:100px"/><!--주문상태-->
        <col style="width:180px"/><!--배송상태-->
        <?php if( 'y' === $scmConfig['orderAcceptFl'] || empty($isProvider)  ) { ?>
            <col style="width:130px"/><!--승인상태-->
            <col style="width:130px"/><!--승인일자-->
        <?php } ?>
        <col style="width:100px"/><!--주문일자-->
    </colgroup>

    <thead>
    <tr>
        <th>
            <input type="checkbox" id="chk_all" class="js-checkall" data-target-name="orderNo"/>
        </th>
        <?php foreach ($listTitles as $val) { ?>
            <th><?=$val?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody class="order-list">
    <?php
    if (gd_isset($data)) {
        foreach ($data as $val) {
            $rowSpan = ( empty($val['requestToAdmin']) && empty($val['orderFileList'])  )?1:2;
            ?>
            <tr class="center">
                <td rowspan="<?=$rowSpan?>">
                    <input type="checkbox" name="orderNo[]" value="<?= $val['orderNo']; ?>" <?= $val['isRevoke'] ? '' :  'disabled="disabled"'  ?> />
                </td>
                <td class="font-num" rowspan="<?=$rowSpan?>">
                    <span class="number"><?= $page->idx--; ?></span>
                </td>
                <!--주문번호-->
                <td class="center text-nowrap order-no" rowspan="<?=$rowSpan?>">
                    <p>
                        <a href="#;" onclick="javascript:open_order_link('<?= $val["orderNo"]; ?>', 'newTab', '1')" title="주문번호" class="font-num" data-order-no="<?= $val["orderNo"]; ?>" data-is-provider="true"><?= $val["orderNo"]; ?></a>
                    </p>
                    <button type="button" class="btn btn-sm btn-white" onclick="window.open('<?=URI_HOME?>mypage/sl_estimate_print.php?orderNo=<?= $val['orderNo'] ?>', 'recommendPrintPopup', 'width=1000,height=950,menubar=yes,scrollbars=yes,resizable=yes');">견적서</button>
                </td>
                </td>
                <!--주문자-->
                <td class="center text-nowrap" rowspan="<?=$rowSpan?>">
                    <?php $memberMasking = \App::load('Component\\Member\\MemberMasking'); ?>
                    <?=$val['orderName']; ?>
                    <div>
                        (<?= $memberMasking->masking('order','id',$val['memId']); ?>/<?=$val['nickNm']?>)
                    </div>
                </td>
                <!--수령자-->
                <td class="center text-nowrap" rowspan="<?=$rowSpan?>">
                    <?=$val['receiverName']; ?>
                </td>
                <!--주문상품-->
                <td class="text-nowrap"  style="padding:10px;text-align: left" >
                    <div>
                        <?= $val['goodsHtml']; ?>
                    </div>
                    <?php if ( 3 == $val['orderAcctStatus'] && !empty($val['reason']) ) { ?>
                        <div style="max-width:330px;">
                            <small class="text-muted" style="white-space:normal">출고 불가 사유 : <?=$val['reason']?></small>
                        </div>
                    <?php } ?>
                </td>
                <!--결제금액-->
                <td class="center text-nowrap" rowspan="<?=$rowSpan?>"><?=number_format($val['settlePrice']); ?>원</td>
                <!--주문상태-->
                <td class="center text-nowrap" rowspan="<?=$rowSpan?>"><?=$val['orderStatusStr']; ?></td>
                <!--배송정보-->
                <td class="center text-nowrap font-date" rowspan="<?=$rowSpan?>">
                    <?php foreach($val['goodsInfo'] as $goodsInfoKey => $goodsInfo) { ?>
                        <?php if( !empty($goodsInfo['invoiceNo']) && count($val['goodsInfo'])-1 == $goodsInfoKey ) { ?>
                            <?=$goodsInfo['invoiceCompanyName']?>
                            <?=$goodsInfo['invoiceNo']?><br>
                            <input type="button" onclick="delivery_trace('<?= $goodsInfo['invoiceCompanySno']; ?>', '<?= $goodsInfo['invoiceNo']; ?>');" value="배송추적" class="btn btn-sm btn-gray mgt5"/>
                        <?php } ?>
                    <?php } ?>
                </td>
                <!--주문일자-->
                <td class="center text-nowrap font-date" rowspan="<?=$rowSpan?>">
                    <?= str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $val['regDt'])); ?>
                </td>
                <?php if( 'y' === $scmConfig['orderAcceptFl'] || empty($isProvider)  ) { ?>
                    <!--출고 승인여부-->
                    <td class="center text-nowrap" rowspan="<?=$rowSpan?>">
                        <strong class="<?=$val['orderAcctStatusColor'];?>"><?=$val['orderAcctStatusStr']; ?></strong>
                    </td>
                    <!--승인일자-->
                    <td class="center text-nowrap font-date" rowspan="<?=$rowSpan?>">
                        <?= str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $val['acctDt'])); ?>
                    </td>
                <?php } ?>
            </tr>
            <?php if( !empty($val['requestToAdmin']) || !empty($val['orderFileList'])  ) { ?>
                <tr>
                    <td style="max-width:200px;padding-left:10px;">
                        <?php if( !empty($val['requestToAdmin']) ) { ?>
                            <div class="font-kor text-muted" >관리자 전달 메세지 : <?= $val['requestToAdmin']; ?></div>
                        <?php } ?>

                        <?php foreach( $val['orderFileList'] as $orderFileInfo ){ ?>
                            <div>* <a href="../order/orderDownload.php?sno=<?=$orderFileInfo['sno']?>" style="color:#1E2C89" ><?=$orderFileInfo['fileName']?></a></div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <?php
        }
    } else {
        echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
</table>
<div class="table-title ">고객 요청 상세</div>
<table class="table table-rows" >
    <colgroup>
        <col />
        <col/>
        <col/>
        <col class="width20p" />
        <col/>
        <col class="width20p" />
        <col />
        <col/>
    </colgroup>
    <thead>
    <tr>
        <th>구분</th>
        <th>주문번호</th>
        <th>요청상품</th>
        <th>요청내용</th>
        <th>요청구분</th>
        <th>처리내용</th>
        <th>요청일자</th>
        <th>처리일자</th>
    </tr>
    </thead>
    <tbody class="order-list">
    <?php
    if (gd_isset($exchangeData)) {
        foreach ($exchangeData as $val) {
            ?>
            <tr class="center">
                <td class="center text-nowrap"><?=$val['claimTypeStr']; ?></td>
                <td class="center text-nowrap"><?=$val['orderNo']; ?></td>
                <td class="center text-nowrap"><?=$val['simpleGoodsInfo']; ?></td>
                <td class="left text-nowrap"><?=$val['reqContents']; ?></td>
                <td class="left text-nowrap"><?=$val['reqTypeContents']; ?></td>
                <td class="left text-nowrap"><?=$val['procContents']; ?></td>
                <td class="center text-nowrap"><?=$val['regDt']; ?></td>
                <td class="center text-nowrap"><?=$val['procDt']; ?></td>
            </tr>
            <?php
        }
    } else {
        echo '<tr><td class="center" colspan="16">주문 정보가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
</table>
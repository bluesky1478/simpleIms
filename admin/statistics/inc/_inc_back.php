    <div class="table-title ">반품상세</div>
    <table class="table table-rows" >
        <colgroup>
                <col/>
                <col/>
                <col/>
                <col/>
                <col/>
                <col/>
            <col class="width20p" />
            <col class="width20p"/>
                <col/>
        </colgroup>
        <thead>
        <tr>
            <th>번호</th>
            <th>주문번호</th>
            <th>상품주문번호</th>
            <th>상품번호</th>
            <th>옵션정보</th>
            <th>반품수량</th>
            <th>사유</th>
            <th>처리내용</th>
            <th>등록일자</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($backData)) {
        foreach ($backData as $val) {
        ?>
            <tr class="center">
                <td class="center text-nowrap"><?=$val['rowNum']; ?></td>
                <td class="center text-nowrap"><?=$val['orderNo']; ?></td>
                <td class="center text-nowrap"><?=$val['userHandleGoodsNo']; ?></td>
                <td class="center text-nowrap"><?=$val['goodsNo']; ?></td>
                <td class="center text-nowrap">
                    <?php
                    // 옵션 처리
                    if (empty($val['optionInfo']) === false) {
                        echo '<div class="option_info" title="상품 옵션">';
                        foreach (json_decode($val['optionInfo']) as $option) {
                            $tmpOption[] = $option[0] . ':' . $option[1];
                        }
                        echo implode(', ', $tmpOption);
                        echo '</div>';
                        unset($tmpOption);
                    }

                    // 텍스트 옵션 처리
                    if (empty($val['optionTextInfo']) === false) {
                        echo '<div class="option_info" title="텍스트 옵션">';
                        foreach (json_decode($val['optionTextInfo']) as $option) {
                            $tmpOption[] = $option[0] . ':' . $option[1];
                        }
                        echo implode(', ', $tmpOption);
                        echo '</div>';
                        unset($tmpOption);}
                    ?>
                </td>
                <td class="center text-nowrap"><?=$val['userHandleGoodsCnt']; ?></td>
                <td class="left text-nowrap"><?=$val['userHandleReason']; ?></td>
                <td class="left text-nowrap"><?=$val['adminHandleReason']; ?></td>
                <td class="center text-nowrap"><?=$val['regDt']; ?></td>
            </tr>
        <?php
        }
        } else {
            echo '<tr><td class="center" colspan="16">주문 정보가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>
<div class="layer-order-add-info" >
    <div class="order-add-info-title" >
        <span class="text-danger"><?=$estimateInfo['sno']?></span> 통화내역
        <span style="float:right">
            <!--<a href="#" onclick="$('.memo_layer').hide();return false;">닫기</a>-->
            <button type="button" class="btn btn-sm btn-white" title="닫기" onclick="$('.memo_layer').hide();return false;">닫기</button>
        </span>
    </div>
    <div class="table-title gd-help-manual ">
        <?=$recapData['customerName']?> 통화이력
    </div>
    <table class="table table-rows " >
        <colgroup>
            <col class="width-xs"/>
            <col class="width-xs"/>
            <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class="ta-c">통화일자</th>
            <th class="ta-c">담당자</th>
            <th class="ta-c">통화내용</th>
        </tr>
        <?php foreach($callData as $callValue) { ?>
            <tr>
                <td class="ta-c">
                    <?=$callValue['regDt']?>
                </td>
                <td class="ta-c">
                    <?=$callValue['regManagerName']?>
                </td>
                <td>
                    <?=nl2br($callValue['contents'])?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
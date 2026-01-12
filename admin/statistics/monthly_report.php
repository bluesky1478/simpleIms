<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
</div>

<div class="table-title ">검색 </div>
<form id="frmSearchStatistics" method="get">

    <input type="hidden" name="mode">

    <table class="table table-cols">
        <colgroup>
            <col class="width-md"/>
            <col/>
        </colgroup>
        <tbody>
        <tr>
            <th>회원사</th>
            <td>
                <div class="form-inline">
                    <?php if( !empty($isProvider)) { ?>
                        <?=$companyNm?>
                        <input type="hidden" name="scmNo" id="scmNo" value="<?=$scmNo?>">
                    <?php }else{ ?>
                        <?=gd_select_box('scmNo', 'scmNo', $scmList, null, $search['scmNo'], null); ?>
                    <?php } ?>
                </div>
            </td>
        </tr>
        <tr>
            <th>조회일자</th>
            <td>
                <div class="form-inline">
                    <div class="input-group js-datepicker-months">
                        <input type="text" class="form-control width-xs start-date" name="searchDate" value="<?= $search['searchDate']; ?>"/>
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="table-btn">
        <button type="submit" class="btn btn-lg btn-black btn-search">검색</button>
    </div>

    <div class="table-title "><span id="title-scm-name"></span> 월간집계 ( <?=$search['searchDate']?> )</div>
    <table class="table table-rows" style="width:50%">
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>
        <thead>
        <tr>
            <th>주문건수</th>
            <th>주문수량</th>
            <th>주문금액</th>
            <th>반품</th>
            <th>교환</th>
            <th>AS</th>
        </tr>
        </thead>
        <tbody class="order-list">
            <tr class="center">
                <td class="center text-nowrap"><?=$data['orderCnt']; ?></td>
                <td class="center text-nowrap"><?=$data['orderGoodsCnt']; ?></td>
                <td class="center text-nowrap"><?=number_format($data['settlePrice']); ?>원</td>
                <td class="center text-nowrap"><?=$data['backCnt']; ?></td>
                <td class="center text-nowrap"><?=$data['exchangeCnt']; ?></td>
                <td class="center text-nowrap"><?=$data['asCnt']; ?></td>
            </tr>
        </tbody>
    </table>

    <div class="table-title ">주문상세</div>
    <table class="table table-rows" style="width:50%">
        <colgroup>
                <col/>
                <col/>
                <col/>
                <col/>
                <col/>
                <col/>
        </colgroup>
        <thead>
        <tr>
            <th>번호</th>
            <th>주문번호</th>
            <th>상품명</th>
            <th>주문수량</th>
            <th>주문금액</th>
            <th>주문일자</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($orderData)) {
        foreach ($orderData as $val) {
        ?>
            <tr class="center">
                <td class="center text-nowrap"><?=$val['rowNum']; ?></td>
                <td class="center text-nowrap"><?=$val['orderNo']; ?></td>
                <td class="center text-nowrap"><?=$val['orderGoodsNm']; ?></td>
                <td class="center text-nowrap"><?=$val['orderGoodsCnt']; ?></td>
                <td class="center text-nowrap"><?=number_format($val['settlePrice']); ?>원</td>
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

    <?php include "inc/_inc_claim.php"; ?>

</form>

<div class="code-html js-excel-data mgb20">
    <div id="grid"></div>
</div>


<script type="text/html" id="progressExcel">
    <div class="js-progress-excel progress-excel" style="position:absolute;width:100%;height:100%;top:0px;left:0px;background:#000000;z-index:1041;opacity:0.5;"></div>
    <div class="js-progress-excel progress-excel" id="js-progress-excel" style="width:300px;background:#fff;margin:0 auto;position:absolute;z-index:1042;padding:20px;left:50%;transform: translate(-50%, 0);text-align:center;">다운로드할 엑셀파일을 생성 중입니다.<br/> 잠시만 기다려주세요.
        <p style="font-size:22px;" id="progressView">0%</p>
        <div style="widtht:260px;height:18px;background:#ccc;margin-bottom:10px;">
            <div id="progressViewBg" style="height:100%">&nbsp;</div>
        </div>
        <span class="pregress-msg-btn"><input type="button" class="downloadCancel btn btn-lg btn-black" value="요청취소" /></span>
    </div>
</script>


<script>
    <!--
    $(document).ready(function () {

        $("#scmNo").on('change',function(){
            $("#title-scm-name").html( $(this).find("option:checked").text() );
        });
        $("#scmNo").change();

        $('[name="deviceFl"]').change(function (e) {
            $('[name="searchDevice"]').val($('[name="deviceFl"]').val());
            $('#frmSearchStatistics').submit();
        });
    });
    //-->
</script>



<div class="search-detail-box form-inline">
    <table class="table table-cols">
        <colgroup>
            <col class="width-md">
            <col class="width-3xl">
            <col class="width-md">
            <col class="width-3xl">
        </colgroup>
        <tbody>
        <?php if(empty($isProvider)) { ?>
        <tr>
            <th>공급사 구분</th>
            <td colspan="3">
                <?=gd_select_box('scmNo', 'scmNo[]', $scmList, null, $scmNo, null); ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <th>검색어</th>
            <td colspan="3">
                <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"
                       class="form-control"/>
            </td>
        </tr>
        <tr>
            <th>상품검색</th>
            <td colspan="3">
                <select class="js-example-basic-single form-control" name="goodsNo" >
                    <option value="all">== 선택 ==</option>
                    <?php foreach($goodsMap as $eachGoodsKey => $eachGoods) { ?>
                    <option value="<?=$eachGoodsKey?>" <?=$eachGoodsKey==$search['goodsNo']?'selected':''?> ><?=$eachGoods?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <!--
        <tr>
            <th>입고기간</th>
            <td colspan="3">
                <div class="form-inline">
                    <div class="input-group js-datepicker-months">
                        <input type="text" class="form-control width-xs start-date2" name="searchDate2[]" value="<?=$search['searchDate2'][0]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                    ~
                    <div class="input-group js-datepicker-months">
                        <input type="text" class="form-control width-xs end-date2" name="searchDate2[]" value="<?=$search['searchDate2'][1]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                </div>
            </td>
        </tr>
        -->
        <tr>
            <th>출고기간</th>
            <td colspan="3">
                <div class="form-inline">

                    <div class="input-group js-datepicker-months">
                        <input type="text" class="form-control width-xs start-date" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                    ~
                    <div class="input-group js-datepicker-months">
                        <input type="text" class="form-control width-xs end-date" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>

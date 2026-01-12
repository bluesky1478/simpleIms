
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
            <th>출고기간</th>
            <td>
                <div class="form-inline">

                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs start-date" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                    ~
                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs end-date" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>

                    <?= gd_search_date(gd_isset($search['searchPeriod'], 6), 'searchDate[]', false) ?>

                </div>
            </td>
            <th>삭제여부</th>
            <td>
                <label class="radio-inline">
                    <input type="radio" name="delFl" value="all"   <?=gd_isset($checked['delFl']['all']); ?> />전체
                </label>
                <label class="radio-inline">
                    <input type="radio" name="delFl" value="y" <?=gd_isset($checked['delFl']['y']); ?> />삭제상품
                </label>
                <label class="radio-inline">
                    <input type="radio" name="delFl" value="n" <?=gd_isset($checked['delFl']['n']); ?> />정상상품
                </label>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>

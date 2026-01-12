
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
                <td>
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"  class="form-control" id="keyword" />
                </td>
                <th>상태</th>
                <td >
                    <label class="radio-inline">
                        <input type="radio" name="asianaStatus" value=""   <?=gd_isset($checked['asianaStatus']['']); ?> />전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="asianaStatus" value="1" <?=gd_isset($checked['asianaStatus']['1']); ?> />승인대기
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="asianaStatus" value="2" <?=gd_isset($checked['asianaStatus']['2']); ?> />승인완료
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="asianaStatus" value="5" <?=gd_isset($checked['asianaStatus']['5']); ?> />준비중
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="asianaStatus" value="4" <?=gd_isset($checked['asianaStatus']['4']); ?> />출고처리
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="asianaStatus" value="3" <?=gd_isset($checked['asianaStatus']['3']); ?> />출고불가
                    </label>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td >
                    <div class="form-inline">

                        <?= gd_select_box('searchDateFl', 'searchDateFl', $search['combineTreatDate'], null, $search['searchDateFl'], null, null, 'form-control '); ?>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 6), 'searchDate[]', false) ?>

                    </div>
                </td>
                <th></th>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>


<div class="search-detail-box form-inline">
    <table class="table table-cols">
        <colgroup>
            <col class="width-md">
            <col class="width-3xl">
            <col class="width-md">
            <col class="width-3xl">
        </colgroup>
        <tbody>
            <tr>
            <th>고객사 구분</th>
            <td colspan="3">
                <label class="radio-inline">
                    <input type="radio" name="scmFl" value="all" <?=gd_isset($checked['scmFl']['all']); ?> onclick="$('#scmLayer').html('');"/>전체
                </label>
                <label class="radio-inline">
                    <input type="radio" name="scmFl" value="y" <?=gd_isset($checked['scmFl']['y']); ?> onclick="layer_register('scm', 'checkbox')"/>고객사
                </label>
                <label>
                    <button type="button" class="btn btn-sm btn-gray" onclick="layer_register('scm','checkbox')">고객사 선택</button>
                </label>

                <div id="scmLayer" class="selected-btn-group <?=$search['scmFl'] == 'y' && !empty($search['scmNo']) ? 'active' : ''?>">
                    <h5>선택된 고객사 : </h5>
                    <?php if ($search['scmFl'] == 'y') {
                        foreach ($search['scmNo'] as $k => $v) { ?>
                            <span id="info_scm_<?= $v ?>" class="btn-group btn-group-xs">
                                <input type="hidden" name="scmNo[]" value="<?= $v ?>"/>
                                <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                <span class="btn"><?= $search['scmNoNm'][$k] ?></span>
                                <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#info_scm_<?= $v ?>">삭제</button>
                                </span>
                        <?php }
                    } ?>
                </div>
            </td>
        </tr>
            <tr>
                <th>검색어</th>
                <td colspan="3">
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"
                           class="form-control"/>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td colspan="3">
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

                        <?= gd_search_date(gd_isset($search['searchPeriod'])) ?>

                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>

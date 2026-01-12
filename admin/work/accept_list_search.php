
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
                <th>검색어</th>
                <td colspan="3">
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control"/>
                </td>
            </tr>
            <tr>
                <th>승인상태</th>
                <td colspan="3">
                    <label class="radio-inline"><input type="radio" name="isApplyFl" value="all" <?=gd_isset($checked['isApplyFl']['all']); ?> />전체</label>
                    <label class="radio-inline"><input type="radio" name="isApplyFl" value="y" <?=gd_isset($checked['isApplyFl']['y']); ?> />승인</label>
                    <label class="radio-inline"><input type="radio" name="isApplyFl" value="n" <?=gd_isset($checked['isApplyFl']['n']); ?> />미승인</label>
                    <label class="radio-inline"><input type="radio" name="isApplyFl" value="r" <?=gd_isset($checked['isApplyFl']['r']); ?> />반려</label>
                </td>
            </tr>
            <tr>
                <th>문서</th>
                <td colspan="3">
                    <div class="document-select">
                        <?= gd_select_box('docDept', 'docDept', \Component\Work\WorkCodeMap::DEPT_KR, null, gd_isset($search['docDept']), '전체', null, 'form-control'); ?>
                        <select id="docType" name="docType" class="form-control" style="width:250px">
                            <option value="">전체</option>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td colspan="3">
                    <div class="form-inline">

                        <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'form-control '); ?>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 364), 'treatDate[]', false) ?>

                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>

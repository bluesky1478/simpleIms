
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
        <?php }else{ ?>

            <tr style="display:none">
                <td colspan="99">
                    <?=$scmList?>
                    <br>
                    <?=$scmNo?>
                    <input type="text" name="scmFl" value="y">
                    <?=gd_select_box('scmNo', 'scmNo[]', $scmList, null, $scmNo, null); ?>
                </td>
            </tr>

        <?php } ?>
            <tr>
                <th>검색어</th>
                <td>
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"
                           class="form-control"/>
                </td>
                <th>처리상태</th>
                <td>
                    <label class="radio-inline"><input type="radio" name="claimStatus" value="" <?=gd_isset($checked['claimStatus']['']); ?> />전체</label>
                    <label class="radio-inline"><input type="radio" name="claimStatus" value="1" <?=gd_isset($checked['claimStatus']['1']); ?> />처리중</label>
                    <label class="radio-inline"><input type="radio" name="claimStatus" value="2" <?=gd_isset($checked['claimStatus']['2']); ?> />처리완료</label>
                    <label class="radio-inline"><input type="radio" name="claimStatus" value="9" <?=gd_isset($checked['claimStatus']['9']); ?> />처리불가</label>
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
                <th>클레임 구분</th>
                <td >
                    <div class="checkbox">
                        <label class="checkbox-inline" style="width:150px;">
                            <input type="checkbox" name="claimType[]" value="all" class="js-not-checkall" data-target-name="claimType[]" <?=gd_isset($checked['claimType']['all']); ?>> 전체
                        </label>
                        <?php foreach($claimTypeMap as $k => $v) { ?>
                            <label style="width:150px;">
                                <input class="checkbox-inline" type="checkbox" name="claimType[]" value="<?=$k?>"  <?=gd_isset($checked['claimType'][$k]); ?>> <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>


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

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 6), 'treatDate[]', false) ?>

                    </div>
                </td>
            </tr>
            <tr>
                <th>프로젝트 타입</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="projectType" value="all" <?=gd_isset($checked['projectType']['all']); ?> />전체
                    </label>
                    <?php foreach( $PROJECT_TYPE as $projectTypeKey => $projectType ){ ?>
                        <label class="radio-inline">
                            <input type="radio" name="projectType" value="<?=$projectTypeKey?>" <?=gd_isset($checked['projectType'][$projectTypeKey]); ?> />
                            <?=$projectType?>
                        </label>
                    <?php } ?>
                </td>
            </tr>
            <?php if( 'total' === $listType ) { ?>
            <tr>
                <th>진행단계</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="projectStatus" value="all" <?=gd_isset($checked['projectStatus']['all']); ?> />전체
                    </label>
                    <?php foreach( $PRJ_STATUS as $eachKey => $eachData ){ ?>
                        <label class="radio-inline">
                            <input type="radio" name="projectStatus" value="<?=$eachKey?>" <?=gd_isset($checked['projectStatus'][$eachKey]); ?> />
                            <?=$eachData?>
                        </label>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>

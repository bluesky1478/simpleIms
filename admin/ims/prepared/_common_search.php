<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" id="list-sort" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 100); ?>"/>
    <input type="hidden" name="preparedType" value="<?=$requestParam['preparedType'] ?>"/>

    <div class="table-title gd-help-manual">
        고객사 검색
    </div>
    <!--검색 시작-->

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
                <td >
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control"/>
                </td>
                <th>처리상태</th>
                <td >
                    <label class="radio-inline"><input type="radio" name="workStep" onclick="submit()" value="all" <?=gd_isset($checked['workStep']['all']);  ?> />전체</label>
                    <label class="radio-inline"><input type="radio" name="workStep" onclick="submit()" value="n" <?=gd_isset($checked['workStep']['n']); ?> />작업대기</label>
                    <label class="radio-inline"><input type="radio" name="workStep" onclick="submit()" value="r" <?=gd_isset($checked['workStep']['r']); ?> />승인대기</label>
                    <label class="radio-inline"><input type="radio" name="workStep" onclick="submit()" value="p" <?=gd_isset($checked['workStep']['p']); ?> />승인완료</label>
                    <label class="radio-inline"><input type="radio" name="workStep" onclick="submit()" value="f" <?=gd_isset($checked['workStep']['f']); ?> />반려</label>
                    <!--
                    0 => '요청', ==> 작업대기
                    1 => '처리중', ===> 작업대기
                    2 => '처리완료', ==>> 승인대기
                    3 => '처리불가', ==> 없음
                    4 => '승인',   //승인 -> 승인  ==> 승인완료
                    5 => '재요청', //반려,번복 -> 다시해.  반려 -->
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>

    <!--검색 끝-->
</form>

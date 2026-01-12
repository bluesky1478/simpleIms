<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <a href="../scm/scm_regist.php" class="btn btn-red-line">고객사 등록</a>
    </div>
</div>

<form id="frmSearchScm" method="get" class="js-form-enter-submit">
    <div class="table-title">
        고객사 검색
    </div>
    <div class="search-detail-box">
        <table class="table table-cols">
            <colgroup>
                <col class="width-sm">
                <col>
                <col class="width-sm">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th>검색어</th>
                <td>
                    <div class="form-inline">
                        <?= gd_select_box('key', 'key', $search['combineSearch'], null, $search['key']); ?>
                        <input type="text" name="keyword" value="<?php echo $search['keyword']; ?>" class="form-control"/>
                    </div>
                </td>
                <th>상태</th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="scmType" value="" <?= $checked['scmType']['']; ?>/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmType" value="y" <?= $checked['scmType']['y']; ?>/>운영
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmType" value="x" <?= $checked['scmType']['x']; ?>/>탈퇴
                    </label>
                </td>
            </tr>
            <tr>
                <th>상품등록권한</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionInsert" value="" <?= $checked['scmPermissionInsert']['']; ?>/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionInsert" value="a" <?= $checked['scmPermissionInsert']['a']; ?>/>자동승인
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionInsert" value="c" <?= $checked['scmPermissionInsert']['c']; ?>/>관리자승인
                    </label>
                </td>
            </tr>
            <tr>
                <th>상품수정권한</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionModify" value="" <?= $checked['scmPermissionModify']['']; ?>/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionModify" value="a" <?= $checked['scmPermissionModify']['a']; ?>/>자동승인
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionModify" value="c" <?= $checked['scmPermissionModify']['c']; ?>/>관리자승인
                    </label>
                </td>
            </tr>
            <tr>
                <th>상품삭제권한</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionDelete" value="" <?= $checked['scmPermissionDelete']['']; ?>/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionDelete" value="a" <?= $checked['scmPermissionDelete']['a']; ?>/>자동승인
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmPermissionDelete" value="c" <?= $checked['scmPermissionDelete']['c']; ?>/>관리자승인
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>

    <div class="table-header">
        <div class="pull-left">
            고객사 리스트 (검색결과
            <strong><?= number_format($page->recode['total'], 0); ?></strong>건, 전체<strong><?= number_format($page->recode['amount'], 0); ?></strong>건)
        </div>
        <div class="pull-right">
            <div class="form-inline">
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?php echo gd_select_box(
                    'pageNum', 'pageNum', gd_array_change_key_value(
                    [
                        10,
                        20,
                        30,
                        40,
                        50,
                        60,
                        70,
                        80,
                        90,
                        100,
                        200,
                        300,
                        500,
                    ]
                ), '개 보기', Request::get()->get('pageNum'), null
                ); ?>
            </div>
        </div>
    </div>
</form>

<form id="frmScmList" name="frmScmList" action="../scm/scm_ps.php" method="post">
    <input type="hidden" name="mode" value="deleteScmList"/>
    <table class="table table-rows">
        <thead>
        <tr>
            <th><input type="checkbox" class="js-checkall" data-target-name="chkScm[]"/></th>
            <th>아이디</th>
            <th>고객사명</th>
            <th>카테고리</th>
            <th>재고 관리</th>
            <th>회원승인 사용 여부</th>
            <th>주문승인 사용 여부</th>
            <th>배송지 선택 여부</th>
            <th>특이사항</th>
            <th>운영여부</th>
            <th>등록일</th>
            <th>추가 정보 수정</th>
            <th>기본 정보 수정</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (empty($data) === false && is_array($data)) {
            foreach ($data as $row) {

                if ($row['scmType'] == 'y') {
                    $row['scmType'] = '운영';
                    $disabled = 'disabled="disabled"';
                } else if ($row['scmType'] == 'n') {
                    $row['scmType'] = '일시정지';
                    $disabled = '';
                } else if ($row['scmType'] == 'x') {
                    $row['scmType'] = '탈퇴';
                    $disabled = '';
                }

        ?>
                <tr class="text-center">
                    <td>
                        <input type="checkbox" name="chkScm[]" value="<?= $row['scmNo']; ?>" data-type="<?= $row['scmType']; ?>" <?= $disabled; ?> />
                    </td>
                    <td><small class="text-muted">(<?=$row['scmNo']?>)</small> <?= $row['managerId'].$row['deleteText']; ?></td>
                    <td><?= $row['companyNm']; ?></td>
                    <td>
                        <?= $row['category']; ?>
                    </td>
                    <td><?= $row['stockManage']?></td>
                    <td><?= $row['memberAccept']?></td>
                    <td><?= $row['orderAccept']?></td>
                    <td><?= $row['deliverySelect']; ?></td>
                    <td style="text-align: left"><?= nl2br($row['memo']) ?></td>
                    <td><?= $row['scmType']; ?></td>
                    <td><?= gd_date_format('Y-m-d', $row['regDt']); ?></td>
                    <td>
                        <a href="../scm/scm_modify.php?scmno=<?= $row['scmNo']; ?>" class="btn btn-dark-gray btn-sm">추가 정보 수정</a>
                    </td>
                    <td>
                        <a href="../scm/scm_regist.php?scmno=<?= $row['scmNo']; ?>" class="btn btn-dark-gray btn-sm">수정</a>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="13" class="no-data">
                    검색된 고객사가 없습니다.
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <div class="table-action">
        <div class="pull-left">
            <button type="button" class="btn btn-white js-scm-delete">선택 삭제</button>
        </div>
        <div class="pull-right">
            <!--<button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchScm" data-target-list-form="frmScmList" data-target-list-sno="chkScm" data-search-count="<?/*=$page->recode['total']*/?>" data-total-count="<?/*=$page->recode['amount']*/?>">엑셀다운로드</button>-->
        </div>
    </div>
</form>

<div class="center"><?= $page->getPage(); ?></div>

<script type="text/javascript">
    <!--
    $(document).ready(function () {
        $('#frmScmList').validate({
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
            rules: {
                'chkScm[]': 'required',
            },
            messages: {
                'chkScm[]': {
                    required: "삭제할 고객사를 선택해 주세요.",
                }
            }
        });

        $('.js-scm-delete').click(function (e) {
            if ($('#frmScmList').valid()) {
                BootstrapDialog.confirm({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: '고객사삭제',
                    message: '선택된 ' + $('input[name*=chkScm]:checked').length + '개의 고객사를 정말로 삭제 하시겠습니까?<br />삭제 시 정보는 복구 되지 않습니다.',
                    closable: false,
                    callback: function (result) {
                        if (result) {
                            $('#frmScmList').submit();
                        }
                    }
                });
            }
        });
    });
    //-->
</script>

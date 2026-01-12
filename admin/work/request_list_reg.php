<?php
use SiteLabUtil\SlCommonUtil;
?>

<div class="search-detail-box form-inline">
    <table class="table table-cols">
        <colgroup>
            <col class="width-md">
            <col class="width-2xl">
            <col class="width-md">
            <col class="width-2xl">
        </colgroup>
        <tbody>
            <tr>
                <th>요청자</th>
                <td colspan="3">
                    <?=\Session::get('manager.managerNm')?>
                </td>
            </tr>
            <tr>
                <th>대상부서/<br>완료 요청일</th>
                <td colspan="3">
                    <?= gd_select_box('targetDeptNo', 'targetDeptNo', SlCommonUtil::getDeptList(), null, null, '전체', null, 'form-control'); ?>
                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs" name="completeRequestDt"  />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>요청내용</th>
                <td colspan="3">
                    <textarea class="form-control w100" rows="3" name="reqContents"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="button" value="등록" class="btn btn-lg btn-red" id="reqReg">
</div>

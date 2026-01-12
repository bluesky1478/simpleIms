
<!--스위트 얼럿-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/promise-polyfill/7.1.0/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.6/sweetalert2.all.min.js"></script>


<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?>
    </h3>
    <?php ?>
</div>
<?php include "_article_detail.php" ?>

<?php if( 'qa' === $req['bdId'] ) { ?>
    <?php include "_claim_info.php" ?>
<?php } ?>

<?php if ($bdView['cfg']['bdReplyStatusFl'] == 'y') { ?>
    <div class="table-title gd-help-manual">게시글답변</div>
    <table class="table table-cols">
        <col class="width-md"/>
        <col/>
        <tr>
            <th>답변 작성자</th>
            <td>
                <?= $bdView['data']['answerWriter'] ?>
            </td>
        </tr>

        <tr>
            <th>답변 상태</th>
            <td>
                <?= $bdView['data']['replyStatusText'] ?>
            </td>
        </tr>

        <tr>
            <th>답변 제목</th>
            <td>
                <?= gd_isset($bdView['data']['answerSubject'], '-') ?>
            </td>
        </tr>
        <tr>
            <th>답변 내용</th>
            <td>
                <?= gd_isset($bdView['data']['workedAnswerContents'], '-'); ?>
            </td>
        </tr>
    </table>
<?php } ?>
<div class="text-center">
    <?php if($req['popupMode'] !='yes') { // CRM 팝업모드가 아닐 경우 ?>
    <a href="javascript:btnList('<?= $req['bdId'] ?>')" class="btn btn-white">목록</a>
    <?php } ?>
    <?php if($bdView['data']['auth']['modify'] == 'y' && $isShow == 'y'){?>
    <a href="javascript:btnModifyWrite('<?= $req['bdId'] ?>','<?= $req['sno'] ?>')" class="btn btn-white">수정</a>
    <?php }?>
    <?php if($bdView['data']['auth']['reply'] == 'y' && $isShow == 'y'){?>
    <a href="javascript:btnReplyWrite('<?= $req['bdId'] ?>','<?= $req['sno'] ?>')" class="btn btn-white">답변</a>
    <?php }?>
    <?php if($bdView['data']['auth']['delete'] == 'y' && $listType == 'board'){?>
    <a href="javascript:btnDelete('<?= $req['bdId'] ?>','<?= $req['sno'] ?>', '<?=$req['popupMode'] ?>', '<?=$bdView['cfg']['bdReplyDelFl']?>', '<?=$isShow?>')" class="btn btn-white js-btn-delete">삭제</a>
    <?php }?>
    <?php if($bdView['data']['auth']['delete'] == 'y' && $listType == 'memo'){?>
        <a href="javascript:btnMemoDelete('<?= $req['bdId'] ?>','<?= $bdView['data']['bdSno'] ?>','<?= $req['sno'] ?>', '<?=$req['popupMode'] ?>')" class="btn btn-white js-btn-delete">삭제</a>
    <?php }?>
    <?php if($isShow == 'n'){?>
        <a href="javascript:btnReport('<?= $req['bdId'] ?>','<?= $req['sno'] ?>', '<?=$req['popupMode'] ?>', '<?=$listType?>')" class="btn btn-white js-btn-report">신고해제</a>
    <?php }?>
</div>

<style>
    textarea {
        width: 85% !important;;
        height: 70px !important;;
    }

    .js-btn-modify, .js-btn-reply, .js-btn-memo-save {
        margin-left: 10px;
        height: 70px;
        width: 13%;
    }
</style>


<?php include "_claim_script.php" ?>

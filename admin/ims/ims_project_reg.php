<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp">

    <form id="frm">
        <div class="page-header js-affix">
            <h3><?=$title?> <span class="text-danger" v-show="!$.isEmpty(project.projectNo)">({% project.projectNo %})</span></h3>
            <?php if(empty($requestParam['popup'])) { ?>
                <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
            <?php }else{ ?>
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            <?php } ?>
            <input type="button" value="<?=$saveBtnTitle?>" class="btn btn-red btn-register" @click="save(items, project)" style="margin-right:75px">
        </div>
    </form>

    <?php include 'template/_template_project.php'?>

</section>

<?php include './admin/ims/reg/_common_script.php'?>
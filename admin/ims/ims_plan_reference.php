<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<style>
    .mx-datepicker { width:100px!important; }
</style>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>스타일 기획 레퍼런스 관리</h3>
        <div class="btn-group">
            <?php if ($iInfoType==0) { ?>
            <span @click="openCommonPopup('upsert_style_plan_ref', 1580, 910, {'sno':0});" class="btn btn-red" style="line-height:38px;">레퍼런스 등록</span>
            <?php } ?>
        </div>
    </div>
    <div class="" >
        <ul class="nav nav-tabs mgb20" role="tablist">
            <li class="<?=$iInfoType==0?'active':''?>">
                <a href="/ims/ims_plan_reference.php?iInfoType=0">스타일 기획 레퍼런스</a>
            </li>
            <?php foreach (\Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_INFO_TYPE as $key => $val) { ?>
            <li class="<?=$iInfoType==$key?'active':''?>">
                <a href="/ims/ims_plan_reference.php?iInfoType=<?=$key?>" ><?=$val?> 관리</a>
            </li>
            <?php } ?>
        </ul>
        <?php include 'tabmenu/ims_plan_reference_'.($iInfoType==0?$iInfoType:'append').'.php'?>
    </div>
    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_plan_reference'.($iInfoType==0?'':'_append').'_script.php'?>

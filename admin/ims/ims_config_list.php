<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>기초정보 관리</h3>
        <div class="btn-group"></div>
    </div>
    <div class="" >
        <ul class="nav nav-tabs mgb20" role="tablist">
            <li class="<?=$iTabNum==1?'active':''?>">
                <a href="/ims/ims_config_list.php?tabNum=1" >사이즈스펙 관리</a>
            </li>
            <li class="<?=$iTabNum==3?'active':''?>">
                <a href="/ims/ims_config_list.php?tabNum=3" >공임비용/기타비용 항목관리</a>
            </li>
            <li class="<?=$iTabNum==4?'active':''?>">
                <a href="/ims/ims_config_list.php?tabNum=4" >샘플실/패턴실 관리</a>
            </li>
            <li class="<?=$iTabNum==6?'active':''?>">
                <a href="/ims/ims_config_list.php?tabNum=6" >피팅체크 양식관리</a>
            </li>
            <li class="<?=$iTabNum==5?'active':''?>">
                <a href="/ims/ims_config_list.php?tabNum=5" >업종 관리</a>
            </li>
            <li class="<?=$iTabNum==7?'active':''?>">
                <a href="/ims/ims_config_list.php?tabNum=7" >제안서가이드 양식관리</a>
            </li>
        </ul>
        <?php include 'tabmenu/ims_config_list_'.$iTabNum.'.php'?>
    </div>
    <div style="margin-bottom:150px"></div>
</section>

<?php include 'script/ims_config_list_'.$iTabNum.'_script.php'?>

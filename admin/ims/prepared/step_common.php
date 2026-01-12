<?php include './admin/ims/library_all.php'?>

<?php
$openType = 'newTab';
?>

    <div class="page-header js-affix">
        <h3><?= end($naviMenu->location); ?></h3>
        <div class="btn-group">

        </div>
    </div>


<?php include './admin/ims/prepared/_common_search.php'?>

<form id="frmList" action="" method="get" target="ifrmProcess">

    <?php if( 'step10' !== $requestParam['status'] ) { ?>
        <!--
        <ul class="nav nav-tabs mgb0" role="tablist" style="border-bottom:none!important;">
            <li role="presentation" <?=$requestParam['view'] !== 'style' ? 'class="active"' : ''?>>
                <a href="../ims/ims_project_list.php?view=project&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">프로젝트별</a>
            </li>
            <li role="presentation" <?=$requestParam['view'] == 'style' ? 'class="active"' : ''?>>
                <a href="../ims/ims_project_list.php?view=style&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">스타일별</a>
            </li>
        </ul>
        -->
    <?php } ?>

    <div class="table-header form-inline">
        <div class="pull-left">
        <span class="font-15">
        검색
        <strong>
            <?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong> 건
        </span>
        </div>
        <div class="pull-right">
            <div>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 100)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows">
        <colgroup>
            <!--공통 열 colgroup-->
            <?php include './admin/ims/prepared/_fixed_colgroup.php'?>
            <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                <col style="width:<?=$stepValue['col']?>%" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
            <th>번호</th>
            <?php include './admin/ims/prepared/_fixed_title.php'?>
            <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                <th><?=$stepValue['title']?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                ?>
                <tr class="center field-parent" data-sno="<?=$val['sno']?>">
                    <!--공통 열 td-->
                    <?php include './admin/ims/prepared/_fixed_td.php'?>
                    <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                        <?php if('text' === $stepValue['type']) { ?>
                            <td class=" <?=$stepValue['class']?>" style="text-align: center">
                                <span><?=$val[$stepKey]?></span>
                                <br><span class="" style="color:#0c4da2"><?=$val[$stepValue['addKey']]?></span>
                            </td>
                        <?php } ?>

                        <?php if('style' === $stepValue['type']) { ?>
                            <td class=" <?=$stepValue['class']?>">
                                <?php if($isProduceCompany) { ?>
                                    <?=$val[$stepKey]?>
                                <?php }else{ ?>
                                    <a href="<?=$targetPage?>?sno=<?=$val['sno']?>&status=<?=$requestParam['status']?>" class="">
                                        <?=$val[$stepKey]?>
                                    </a>
                                <?php } ?>
                            </td>
                        <?php } ?>

                        <?php if('number' === $stepValue['type']) { ?>
                            <td class=" <?=$stepValue['class']?>"><?=number_format($val[$stepKey]); ?></td>
                        <?php } ?>
                        <?php if('percent' === $stepValue['type']) { ?>
                            <td class=" <?=$stepValue['class']?>"><?=round($val[$stepKey]); ?>%</td>
                        <?php } ?>

                        <?php if('img' === $stepValue['type']) { ?>
                            <td class="font-num ">
                                <a href="<?=$targetPage?>?sno=<?=$val['sno']?>&status=<?=$requestParam['status']?>" class="ims-project-no text-danger">
                                    <?php if (!empty($val['fileThumbnail'])) { ?>
                                        <img src="<?=$val['fileThumbnail']?>" width="40">
                                    <?php }else{ ?>
                                        <img src="/data/commonimg/ico_noimg_75.gif" width="40">
                                    <?php } ?>
                                </a>
                            </td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <div class="table-action clearfix">

        <div class="pull-left"></div>
        <div class="pull-right">
            <!--
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            -->
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<?php include './admin/ims/list/_common_script.php'?>
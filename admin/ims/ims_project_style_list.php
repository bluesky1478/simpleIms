<?php include 'library_all.php'?>

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" />
    </div>
</div>

<?php include './admin/ims/list/_common_search.php'?>

<form id="frmList" action="" method="get" target="ifrmProcess">

    <ul class="nav nav-tabs mgb0" role="tablist" style="border-bottom:none!important;">
        <li role="presentation" <?=$requestParam['view'] !== 'style' ? 'class="active"' : ''?>>
            <a href="../ims/ims_project_list.php?view=project&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">프로젝트별</a>
        </li>
        <li role="presentation" <?=$requestParam['view'] == 'style' ? 'class="active"' : ''?>>
            <a href="../ims/ims_project_list.php?view=style&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">스타일별</a>
        </li>
    </ul>

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
            <col style="width:3%" />
            <col style="width:6%" />
            <col style="width:6%" />
            <col style="width:13%" />
            <col style="width:6%" />
            <col style="width:6%" />
            <col style="width:6%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:6%" />
            <col style="width:6%" />
            <col style="width:6%" />
            <col style="width:5%" />
            <col style="width:6%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
        </colgroup>
        <thead>
        <tr>
            <th>번호</th>
            <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                <th><?=$titleValue?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                ?>
                <tr class="center field-parent" data-sno="<?=$val['sno']?>">
                    <td class="font-num">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <td class="font-num field-customer">
                        <div class="number text-danger project-no">
                            <a href="ims_project_view.php?sno=<?=$val['sno']?>" class="text-danger"><?= $val['projectNo']; ?></a>
                        </div>
                        <div class="number">
                            <?= $val['projectStatusKr']; ?>
                        </div>
                        <div class="display-none">
                            <span class="btn-hide-process cursor-pointer hover-btn btn-modify text-muted" style="display: none">수정</span>
                            <?php if( $isAuth ) { ?>
                                <span class="btn-hide-process cursor-pointer hover-btn btn-delete text-muted"  style="display: none">삭제</span>
                            <?php } ?>
                        </div>
                    </td>
                    <td class="font-num">
                        <div class=""><?= $val['projectTypeKr']; ?></div>
                    </td>
                    <td class="center text-nowrap ">
                        <a href="ims_project_view.php?sno=<?=$val['sno']?>" class="text-blue">
                            <?=$val['customerName']; ?><?=$val['use3plKr']; ?>
                        </a>
                    </td>
                    <td class="font-num ">
                        <a href="ims_project_view.php?sno=<?=$val['sno']?>">
                        <?php if (!empty($val['fileThumbnail'])) { ?>
                            <img src="<?=$val['fileThumbnail']?>" width="40">
                        <?php }else{ ?>
                            <img src="/data/commonimg/ico_noimg_75.gif" width="40">
                        <?php } ?>
                        </a>
                    </td>
                    <td class="center text-nowrap">
                        <span class="open-style text-blue cursor-pointer" data-project-sno="<?=$val['sno']?>" data-product-sno="<?=$val['productSno']?>">
                            <?=$val['productName']?>
                        </span>
                    </td>
                    <td class="center text-nowrap">
                        <span class="open-style text-blue cursor-pointer" data-project-sno="<?=$val['sno']?>" data-product-sno="<?=$val['productSno']?>">
                            <?=$val['styleCode']?>
                        </span>
                    </td>

                    <td class="center text-nowrap"><?=number_format($val['prdExQty']);?><span class="text-muted">(기획)</span></td>
                    <td class="center text-nowrap"><?=number_format($val['currentPrice']);?></td>
                    <td class="center text-nowrap"><?=number_format($val['targetPrice']);?></td>
                    <td class="center text-nowrap"><?=number_format($val['targetPrdCost']);?></td>
                    <td class="center text-nowrap"><?=number_format($val['prdCost']);?></td>
                    <td class="center text-nowrap"><?=$val['margin']?></td>
                    <td class="center text-nowrap"><?=$val['customerOrderDt']?></td>
                    <td class="center text-nowrap"><?=$val['customerDeliveryDt']?></td>
                    <td class="center text-nowrap"><?=$val['confirmed']; ?></td>
                    <td class="center">
                        <div><?=$val['regDt'] ?></div>
                        <div><?=$val['modDt'] ?></div>
                    </td>
                    <td class="center"></td>
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


<script type="text/javascript">
    $(()=>{

        $('.btn-modify').on('click',function(){
            const sno = $(this).closest('.field-parent').data('sno');
            location.href=`ims_project_reg.php?sno=${sno}`;
        });

        $('.btn-delete').on('click',function(){
            const sno = $(this).closest('.field-parent').data('sno');
            $.msgConfirm('삭제시 복구가 불가능 합니다. 계속 하시겠습니까?', "").then((result)=>{
                if( result.isConfirmed ){
                    $.postAsync('<?=$imsAjaxUrl?>',{
                        mode:'deleteData',
                        sno:sno,
                        target:DATA_MAP.CUSTOMER
                    }).then(()=>{
                        $.msg('처리 되었습니다.', "", "success").then(()=>{
                            location.reload();
                        });
                    });
                }
            });
        });

        $('.field-customer').hover(function(){
            $('.btn-hide-process').hide();
            $(this).find('.btn-hide-process').show();
        },()=>{
            $('.btn-hide-process').hide();
        });

        $('.btn-reg').click(()=>{
            location.href='./ims_project_reg.php';
        });

        $('.open-style').on('click', function(){
            const projectSno = $(this).data('projectSno');
            const productSno = $(this).data('productSno');
            openProductReg(projectSno, productSno);

        });

    });
</script>
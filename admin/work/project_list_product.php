
<?php include 'import_lib.php'?>

<script>
    // 정렬&출력수
    $(function(){
        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });
        //simple excel download
        $('.simple-download').click(function(){
            location.href = "<?=$requestUrl?>";
        });
    });

    /**
     * 카테고리 연결하기 Ajax layer
     */
    function layer_register(typeStr, mode, isDisabled) {
        var addParam = {
            "mode": mode,
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        if (!_.isUndefined(isDisabled) && isDisabled == true) {
            addParam.disabled = 'disabled';
        }
        layer_add_info(typeStr,addParam);
    }
</script>

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="프로젝트 등록" class="btn btn-red-line js-register"/>
    </div>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title">
        프로젝트 검색
    </div>
    <?php include('project_list_search.php'); ?>
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
            검색
            <strong><?= empty($page->recode['total'])? 0 : $page->recode['total']; ?></strong>
            건
        </div>
        <div class="pull-right">
            <div>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 20)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows">
        <colgroup>
            <col style="width:50px" />
            <col style="width:120px"/>
            <col style="width:200px" />
            <col />
            <col />
            <?php foreach ($PRD_PLAN_LIST as $val) { ?>
                <col style="width:60px;"  />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th rowspan="2">번호</th>
            <?php foreach ($listTitles as $val) { ?>
                <th rowspan="2"><?=$val?></th>
            <?php } ?>
            <th colspan="99">일정</th>
        </tr>
        <tr>
            <th class="font-11">예정일/확정일</th>
            <?php foreach ($PRD_PLAN_LIST as $val) { ?>
                <th class="font-11"><?=$val['planName']?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php if (gd_isset($data)) { ?>
            <?php foreach ($data as $dataKey => $val) { ?>
                <?php foreach ($val['productData'] as $productKey => $productData) { ?>
                <tr>
                    <?php if( 0 === $productKey ) { ?>
                        <td class="center font-num" rowspan="<?=count($val['productData'])*2?>">
                            <span class="number"><?= $page->idx--; ?></span>
                        </td>
                        <td class="center text-nowrap date-field" rowspan="<?=count($val['productData'])*2?>">
                            <a href="project_view.php?sno=<?=$val['sno']; ?>&type=<?=$listType?>" target="_parent" class="hover-btn">
                                <div class="text-danger font-15" ><?=$val['sno']; ?></div>
                                <div><?=$val['companyName']?></div>
                                <div><small class="text-muted"><?=$val['projectTypeKr']?> 프로젝트</small> </div>
                            </a>
                        </td>
                    <?php } ?>

                    <td class="center text-nowrap date-field" rowspan="2">
                        <a href="project_view.php?sno=<?=$val['sno']; ?>&type=<?=$listType?>" target="_parent" class="hover-btn">
                        <div><?=$productData['prdName']?></div>
                        <div class="mgt5"><?=number_format($productData['count'])?><small class="">개</small></div>
                        </a>
                    </td>
                    <td class="center text-nowrap date-field" rowspan="2"><?=$productData['factoryName']?></td>
                    <td class="center text-nowrap date-field">▶ 예상일</td>
                    <?php foreach ($productData['producePlan'] as $productPlan) { ?>
                        <td class="center text-nowrap date-field font-11 pd2"><?=$productPlan['planDt']?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="center text-nowrap">▶ 확정일</td>
                    <?php foreach ($productData['producePlan'] as $productPlan) { ?>
                        <td class="center text-nowrap date-field font-11 pd2"><?=$productPlan['completeDt']?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td class="center" colspan="16">검색된 정보가 없습니다.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="table-action clearfix">

        <div class="pull-left"></div>
        <div class="pull-right">
            <!--<button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>-->
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

</form>

<script type="text/javascript">
    $(function(){
        //프로젝트 등록
        var openProjectDataForm = function(sno){
            var childNm = 'project_register';
            var addParam = {
                mode: 'simple',
                layerTitle: '프로젝트 등록',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                sno : sno
            };
            layer_add_info(childNm, addParam);
        };

        //[ 이벤트 등록 ]

        //프로젝트 등록
        $('.js-register').click(function(){
            openProjectDataForm('');
        });

        $('.btn-open-comment').click(function(){
            $(this).closest('td').find('.comment-table').removeClass('display-none');
        });

    });
</script>

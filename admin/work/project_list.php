
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
        <thead>
        <tr>
            <th style="width:50px">번호</th>
            <?php foreach ($listTitles as $val) { ?>
                <th><?=$val?></th>
            <?php } ?>
            <th>등록일자</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php if (gd_isset($data)) { ?>
            <?php foreach ($data as $val) { ?>
                <tr >
                    <td class="center font-num" rowspan="2">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--프로젝트 번호-->
                    <td class="center text-nowrap date-field" rowspan="2">
                        <a href="project_view.php?sno=<?=$val['sno']; ?>&type=<?=$listType?>" target="_parent">
                        <div class="text-danger font-15"><?=$val['sno']; ?></div>
                        </a>
                    </td>
                    <!--프로젝트 타입-->
                    <td class="center text-nowrap date-field" rowspan="2">
                        <?=$val['projectTypeKr']; ?>
                    </td>
                    <!--프로젝트 명-->
                    <td class="center text-nowrap" rowspan="2">
                        <a href="project_view.php?sno=<?=$val['sno']; ?>&type=<?=$listType?>" target="_parent">
                            <?=$val['projectName']; ?>
                        </a>
                    </td>
                    <!--진행단계 - 상태-->
                    <?php if( 'total' === $listType ) { ?>
                    <td class="center text-nowrap date-field" rowspan="2">
                        <?=$val['projectStatusKr']?>
                    </td>
                    <?php } ?>
                    <!--고객사-->
                    <td class="center text-nowrap" rowspan="2" style="width:200px">
                        <?=$val['companyName']?>
                    </td>
                    <td class="center text-nowrap date-field" rowspan="2" >
                        <?=$val['meetingDt']?>
                    </td>
                    <td class="center text-nowrap date-field" rowspan="2" >
                        <?=$val['hopeDeliveryDt']?>
                    </td>
                    <td class="center text-nowrap date-field" rowspan="2" >
                        <?=$val['deadlineDt']?>
                    </td>
                    <!-- 예상 일정-->
                    <td class="center text-nowrap date-field">▶ 예상일</td>

                    <?php if( $isEstimate ) { ?>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['SALES']['planDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['DESIGN']['planDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['QC']['planDt'], '-')?></td>
                    <?php } ?>

                    <?php if( $isOrder ) { ?>
                    <td class="center text-nowrap date-field " ><?=gd_isset($val['planData']['ORDER1']['planDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['ORDER2']['planDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['ORDER3']['planDt'], '-')?></td>
                    <?php } ?>
                    <!--등록일자-->
                    <td class="center date-field" rowspan="2" >
                        <?= str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $val['regDt'])); ?>
                    </td>
                </tr>
                <tr>
                    <!-- 확정 일정-->
                    <td class="center text-nowrap">▶ 확정일</td>
                    <?php if( $isEstimate ) { ?>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['SALES']['confirmDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['DESIGN']['confirmDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['QC']['confirmDt'], '-')?></td>
                    <?php }?>

                    <?php if( $isOrder ) { ?>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['ORDER1']['confirmDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['ORDER2']['confirmDt'], '-')?></td>
                    <td class="center text-nowrap date-field" ><?=gd_isset($val['planData']['ORDER3']['confirmDt'], '-')?></td>
                    <?php }?>
                </tr>
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

<script type="text/javascript">
    // 정렬&출력수
    $(function(){
        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });

        //simple excel download
        $('.simple-download').click(function(){
            let sno = $(this).data('sno');
            let type = $(this).data('type');
            location.href = "<?=$requestUrl?>&sno="+sno+"&type="+type;
        });

        $('.excel-submit').click(()=>{
            $('#frmExcel').submit();
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
        <input type="button" value="마감 등록" class="btn btn-red-line js-register" onclick="location.href='closing_reg.php'" />
    </div>
</div>

<section class="excel-upload-section display-none">
    <div class="table-title">
        입/출고 등록
    </div>
    <div class="">
        <form id="frmExcel" name="frmModifyGoodsInfo" action="./erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
            <table class="table table-cols">
                <colgroup>
                    <col class="width20p"/>
                    <col class="width-xl"/>
                </colgroup>
                <tbody>
                <tr>
                    <th>입고 정보 업로드</th>
                    <td>
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="setInputStock"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                        </div>
                        <div>
                            <span class="notice-info">엑셀 파일은 반드시 &quot;Excel 97-2003 통합문서&quot;만 가능하며, csv 파일은 업로드가 되지 않습니다.</span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</section>


<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 100); ?>"/>
    <div class="table-title gd-help-manual">
        마감 이력 검색
    </div>
    <!--검색 시작-->

    <div class="search-detail-box form-inline">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tbody>
            <tr>
                <th>기간검색</th>
                <td colspan="3">
                    <div class="form-inline">

                        <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'form-control '); ?>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 364), 'treatDate[]', false) ?>

                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>

    <!--검색 끝-->
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
            검색
            <strong><?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong>
            건
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
            <?php foreach ($data as $val => $key) { ?>
            <col/>
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
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
                <tr class="center">
                    <td class="font-num" style="width:60px">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--마감일자-->
                    <td class="cen">
                        <?= str_replace(' ', '<br>', gd_date_format('Y-m-d', $val['regDt'])); ?>
                    </td>
                    <!--입고수량-->
                    <td class="center text-nowrap">
                        <span data-sno="<?=$val['sno']?>" data-type="1" class="simple-download hover-btn" style="cursor: pointer">
                        <b><?=number_format($val['stockInQty']); ?></b>
                        </span>
                    </td>
                    <!--출고수량-->
                    <td class="center text-nowrap">
                        <span data-sno="<?=$val['sno']?>" data-type="2" class="simple-download hover-btn" style="cursor: pointer">
                        <b><?=number_format($val['stockOutQty']); ?></b>
                        </span>
                    </td>
                    <!--총 재고수량-->
                    <td class="center text-nowrap">
                        <span data-sno="<?=$val['sno']?>" data-type="3" class="simple-download hover-btn" style="cursor: pointer">
                            <b><?=number_format($val['totalQty']); ?></b>
                        </span>
                    </td>
                    <!--마감처리자-->
                    <td class="center text-nowrap"><?=$val['managerNm']; ?></td>
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
            <!--<button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>-->
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<script type="text/javascript">
    let prevDateHtml = '<label class="btn btn-white btn-sm <?='1'===$search['searchPeriod']?'active':''?>" ><input type="radio" name="searchPeriod" value="1" >전일</label>';
    $('[class*=js-dateperiod]').find('label').eq(0).after(prevDateHtml);
</script>
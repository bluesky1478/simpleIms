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

        //적용회원 리스트 가져오기
        $('button.js-layer-stock-detail').click(function (e) {
            var goodsNo = $(this).data('goodsno');
            var optionNo = $(this).data('optionno');
            var goodsNm = $(this).data('goodsnm');
            var optionNm = $(this).data('optionnm');

            var childNm = 'stock_detail';
            var addParam = {
                mode: 'simple',
                layerTitle: goodsNm+'( ' + optionNm +  ' ) 재고 이력',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                goodsNo: goodsNo,
                optionNo: optionNo,
                startDate : '<?=$search['searchDate'][0]?>',
                endDate : '<?=$search['searchDate'][1]?>'
            };
            layer_add_info(childNm, addParam);
        });

    });

    /**
     *  공급사 선택
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

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title gd-help-manual">
        재고 이력 집계
    </div>
    <?php include('stock_stat_search.php'); ?>
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
            <col class="width-xs"/>
            <?php foreach ($data as $val => $key) { ?>
            <col/>
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
        <?php foreach ($listTitles as $val) { ?>
            <th><?=$val?></th>
        <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="font-num">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--상품번호-->
                    <td class="center text-nowrap"><?=$val['goodsNo']; ?></td>
                    <!--공급사-->
                    <td class="center text-nowrap"><?=$val['companyNm']; ?></td>
                    <!--상품명-->
                    <td class="center text-nowrap"><?=$val['goodsNm']; ?></td>
                    <!--옵션명-->
                    <td class="center text-nowrap"><?=$val['optionNm']; ?></td>
                    <!--기간입고-->
                    <td class="center text-nowrap text-red"><b><?=$val['inStock']; ?></b></td>
                    <!--기간출고-->
                    <td class="center text-nowrap text-blue"><b><?=$val['outStock']; ?></b></td>
                    <!--상세보기-->
                    <td class="center text-nowrap">
                        <button type="button" class="js-layer-stock-detail btn btn-sm btn-black"  data-goodsno="<?=$val['goodsNo']; ?>" data-optionno="<?=$val['optionNo']?>>"  data-goodsnm="<?=$val['goodsNm']; ?>"  data-optionnm="<?=$val['optionNm']; ?>"   >상세보기 </button>
                    </td>
                    <!--조회기간-->
                    <td class="center text-nowrap"><?=$search['searchDate'][0]?> ~ <?=$search['searchDate'][1]?></td>
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

        <div class="pull-left">
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

</form>



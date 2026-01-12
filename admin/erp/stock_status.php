<script type="text/javascript">
    // 정렬&출력수
    $(function(){
        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });

        $('.simple-download1').click(function(){
            location.href = "<?=$requestUrl?>&downloadType=1";
        });
        $('.simple-download2').click(function(){
            location.href = "<?=$requestUrl?>&downloadType=2";
        });
        $('.simple-download3').click(function(){
            location.href = "<?=$requestUrl?>&downloadType=3";
        });

        $('.excel-submit1').click(()=>{
            $('#frmExcel1').submit();
        });
        $('.excel-submit2').click(()=>{
            $('#frmExcel2').submit();
        });
        $('.excel-submit4').click(()=>{
            $('#frmExcel4').submit();
        });
        $('.excel-submit5').click(()=>{
            $('#frmExcel5').submit();
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
    <div class="btn-group"></div>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 100); ?>"/>
    <div class="table-title gd-help-manual">
        검색
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
                <th>고객사 구분</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="all" <?=gd_isset($checked['scmFl']['all']); ?> onclick="$('#scmLayer').html('');"/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="y" <?=gd_isset($checked['scmFl']['y']); ?> onclick="layer_register('scm', 'checkbox')"/>
                    </label>
                    <label>
                        <button type="button" class="btn btn-sm btn-gray" onclick="layer_register('scm','checkbox')">고객사 선택</button>
                    </label>

                    <div id="scmLayer" class="selected-btn-group <?=$search['scmFl'] == 'y' && !empty($search['scmNo']) ? 'active' : ''?>">
                        <h5>선택된 고객사 : </h5>
                        <?php if ($search['scmFl'] == 'y') {
                            foreach ($search['scmNo'] as $k => $v) { ?>
                                <span id="info_scm_<?= $v ?>" class="btn-group btn-group-xs">
                                <input type="hidden" name="scmNo[]" value="<?= $v ?>"/>
                                <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                <span class="btn"><?= $search['scmNoNm'][$k] ?></span>
                                <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#info_scm_<?= $v ?>">삭제</button>
                                </span>
                            <?php }
                        } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>검색어</th>
                <td colspan="3">
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"
                           class="form-control"/>
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
            <strong><?=$page['totalCnt']?></strong>
            건
        </div>
        <div class="pull-right">
            <div>

                <button type="button" class="btn btn-white btn-icon-excel simple-download3" >엑셀다운로드(대사용)</button>
                <button type="button" class="btn btn-white btn-icon-excel simple-download2" >엑셀다운로드(Simple)</button>
                <button type="button" class="btn btn-white btn-icon-excel simple-download1" >엑셀다운로드(화면)</button>

            </div>
        </div>
    </div>
    <!--<div class="table-action clearfix">

        <div class="pull-left"></div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel simple-download3" >엑셀다운로드(대사용)</button>
            <button type="button" class="btn btn-white btn-icon-excel simple-download2" >엑셀다운로드(Simple)</button>
            <button type="button" class="btn btn-white btn-icon-excel simple-download1" >엑셀다운로드(화면)</button>
        </div>
    </div>-->
    <?php if (empty($data)) { ?>
        <table class="table table-rows">
            <colgroup>
                <col style="width:150px;">
                <col style="width:300px;">
                <col >
            </colgroup>
            <tr>
                <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                    <th><?=$titleValue?></th>
                <?php } ?>
            </tr>
            <td colspan="3" class="text-center">
                검색된 데이터가 없습니다.
            </td>
        </table>
    <?php }else{ ?>

        <table class="table table-rows">
            <colgroup>
                <col style="width:150px;">
                <col style="width:300px;">
                <?php for ($i=0; $page['optionMaxCount']+1>$i; $i++) { ?>
                    <!--<col style="width:(80/<?=$page['optionMaxCount']+1?>)%;">-->
                    <col style="width:60px;">
                <?php } ?>
            </colgroup>
            <tr>
                <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                <th class="text-center"><?=$titleValue?></th>
                <?php } ?>
                <th class="text-center" colspan="<?=$page['optionMaxCount']+1?>">품목 사이즈별 재고수량 <small>(단위 벌)</small></th>
            </tr>
            <tr>
                <td colspan="2" class="text-left bg-light-gray" ><b>Sum tt.</b></td>
                <td class="text-center bg-light-gray"><b><?=number_format($page['totalStockCnt'])?></b></td>
                <td class="text-center bg-light-gray" colspan="<?=$page['optionMaxCount']?>"></td>
            </tr>

            <?php foreach ($data as $dataKey => $val) { ?>
                <tr>
                    <td colspan="2" class="text-left bg-light-yellow" ><b>#Type <?=++$idx?></b></td>
                    <td class="text-center bg-light-yellow"><b><?=number_format($val['optionTotalStockCnt'])?></b></td>
                    <?php foreach ($val['optionList'] as $optionKey => $optionName) { ?>
                        <td class="center text-nowrap bg-light-yellow">
                            <b><?=$optionName?></b>
                        </td>
                    <?php } ?>
                    <?php for ($i=count($val['optionList']); $page['optionMaxCount']>$i; $i++) { ?>
                        <td class="center text-nowrap bg-light-yellow"></td>
                    <?php } ?>
                </tr>
                <tbody>
                <?php foreach ($val['data'] as $dataVal) { ?>
                <tr>
                    <td class="text-center"><?=$dataVal['scmName']?></td>
                    <td class="text-left"><?=$dataVal['productName']?></td>
                    <td class="center text-nowrap bg-light-yellow" >
                        <?=number_format($dataVal['productStockCnt']); ?>
                    </td>
                    <?php foreach ($dataVal['optionList'] as $optionKey => $stockCnt) { ?>
                    <!--수량-->
                    <td class="center text-nowrap ">
                       <?=number_format($stockCnt); ?>
                    </td>
                    <?php } ?>
                    <?php for ($i=count($dataVal['optionList']); $page['optionMaxCount']>$i; $i++) { ?>
                    <td class="center text-nowrap "></td>
                    <?php } ?>
                </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
        </table>
    <?php } ?>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>



<script type="text/javascript">
    let prevDateHtml = '<label class="btn btn-white btn-sm <?='1'===$search['searchPeriod']?'active':''?>" ><input type="radio" name="searchPeriod" value="1" >전일</label>';
    $('[class*=js-dateperiod]').find('label').eq(0).after(prevDateHtml);
</script>
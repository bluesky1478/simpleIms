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
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title ">
        상품 검색
        <?php if(empty($isProvider)) { ?>
        <?php } ?>
    </div>
    <?php include('scm_stock_monthly_list_search.php'); ?>
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
            <col class="" style="width:50px"/>
            <col class="width-lg"/>
            <col class="width-lg"/>
        </colgroup>

        <thead>
        <tr>
            <?php foreach ($listTitles as $titleKey => $val) { ?>
                <th rowspan="2"><?=$val?></th>
            <?php } ?>
            <?php foreach ($monthlyData as $titleKey => $val) { ?>
                <th colspan="2"><?=$titleKey?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php foreach ($monthlyData as $titleKey => $val) { ?>
                <th class="center">입고</th>
                <th class="center">출고</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) { ?>
                <tr class="center">
                    <td class="font-num" style="width:60px" rowspan="<?=count($val['optionList'])?>">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--상품명-->
                    <td class="center text-nowrap" rowspan="<?=count($val['optionList'])?>">
                        <?= $val['goodsNm']; ?>
                        <br>
                        <small class="text-blue"><?= $val['goodsNo']; ?></small>
                    </td>
                    <td>
                        <?=$val['optionList'][0]['optionName']?>
                        <br><small class="text-muted"><?=$val['optionList'][0]['optionNumber']?></small>
                    </td>
                    <td>
                        <?=$val['optionList'][0]['stockCnt']?>
                    </td>
                    <?php foreach ($monthlyData as $monthlyKey => $monthlyValue) { ?>
                        <td class="stock-in">
                            <?=number_format(gd_isset($monthlyValue[$val['goodsNo']][$val['optionList'][0]['optionNo']]['stockIn'],0))?>
                        </td>
                        <td class="stock-out">
                            <?=number_format(gd_isset($monthlyValue[$val['goodsNo']][$val['optionList'][0]['optionNo']]['stockOut'],0))?>
                        </td>
                    <?php } ?>
                </tr>

                <?php foreach ($val['optionList'] as $optionListKey => $optionList ) {
                    if( 0 === $optionListKey ) continue;
                ?>
                <tr class="center">
                    <td>
                        <?=$optionList['optionName']?>
                        <br><small class="text-muted"><?=$optionList['optionNumber']?></small>
                    </td>
                    <td><?=$optionList['stockCnt']?></td>
                    <?php foreach ($monthlyData as $monthlyKey => $monthlyValue) { ?>
                        <td class="stock-in"><?=number_format(gd_isset($monthlyValue[$val['goodsNo']][$optionList['optionNo']]['stockIn'],0))?></td>
                        <td class="stock-out"><?=number_format(gd_isset($monthlyValue[$val['goodsNo']][$optionList['optionNo']]['stockOut'],0))?></td>
                    <?php } ?>
                </tr>
                <?php } ?>

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

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<script>
    $(function(){
        $('#btnWait').on('click', wait_order);
        $('#btnApply').on('click', apply_order);
        $('#btnCancel').on('click', denied_order);

        $('.btn-stock-graph').on('click', function(){
            var goodsNo = $(this).data('goodsno');
            var startDate = $('.start-date').val();
            var endDate = $('.end-date').val();
            var url = 'scm_stock_list_popup.php?goodsNo='+goodsNo+'&startDate='+startDate+'&endDate='+endDate;
            window.open(url, 'scm_stock_list_popup', 'width=1400, height=910, resizable=yes, scrollbars=yes');
        });

        var setStockClass = function($el, className){
            if( '0' != $el.text().replace(/[^0-9]/gi,"") ){
                $el.addClass(className);
                $el.addClass('bold');
            }
        }
        //Stock CSS
        $('.stock-in').each(function(){
            setStockClass( $(this) , 'text-danger' );
        });
        $('.stock-out').each(function(){
            setStockClass( $(this) , 'text-blue' );
        });
    });

    function wait_order(e) {
        e.preventDefault();
        var orderNo = [];
        $('input[name*="orderNo"]:checked').each(function() {
            orderNo.push($(this).val());
        });
        if(0 >= orderNo.length){
            alert('주문을 선택해주세요!');
            return false;
        }else{
            dialog_confirm('선택한' + $(':checkbox:checked').not('.js-checkall').length + '주문을 대기상태로 변경 하시겠습니까?', function (result) {
                if (result) {
                    var param = {
                        mode : 'order_wait'
                        , orderNo : orderNo
                    };
                    $.post('scm_member_ps.php', param, function (data) {
                        if(data){
                            //바로 닫기
                            window.location.reload();
                        }
                    });

                }
            });
        }
    }

    function apply_order(e) {
        e.preventDefault();
        var orderNo = [];
        $('input[name*="orderNo"]:checked').each(function() {
            orderNo.push($(this).val());
        });
        if(0 >= orderNo.length){
            alert('주문을 선택해주세요!');
            return false;
        }else{
            dialog_confirm('선택한' + $(':checkbox:checked').not('.js-checkall').length + '주문의 출고를 승인하시겠습니까?', function (result) {
                if (result) {
                    var param = {
                        mode : 'order_accept'
                        , orderNo : orderNo
                    };
                    $.post('scm_member_ps.php', param, function (data) {
                        if(data){
                            //바로 닫기
                            window.location.reload();
                        }
                    });

                }
            });
        }
    }

    function denied_order(e) {
        e.preventDefault();
        var orderNo = [];
        $('input[name*="orderNo"]:checked').each(function() {
            orderNo.push($(this).val());
        });
        if(0 >= orderNo.length){
            alert('주문을 선택해주세요!');
            return false;
        }else{
            dialog_confirm('선택한' + $(':checkbox:checked').not('.js-checkall').length + '주문의 출고 불가 처리 하시겠습니까?', function (result) {
                if (result) {
                    var param = {
                        mode : 'order_denied'
                        , orderNo : orderNo
                    };
                    $.post('scm_member_ps.php', param, function (data) {
                        if(data){
                            //바로 닫기
                            window.location.reload();
                        }
                    });

                }
            });
        }
    }

</script>

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
    <?php include('scm_stock_list_search.php'); ?>
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

    <div class="table-responsive">
        <table class="table table-rows order-list">
        <colgroup>
            <col class="" style="width:50px"/>
            <?php foreach ($data as $val => $key) { ?>
                <?php if( 1 == $val ) { ?>
                    <col style="width:100px" />
                <?php }else{ ?>
                    <col/>
                <?php } ?>
            <?php } ?>
        </colgroup>

        <thead>
        <tr>
            <!--<th style="width:30px">
                <input type="checkbox" id="chk_all" class="js-checkall" data-target-name="orderNo"/>
            </th>-->
            <?php foreach ($listTitles as $titleKey => $val) { ?>
                <?php if(  $titleKey == 2  ) { ?>
                    <th colspan="<?=$maxOptionCnt+1?>"><?=$val?></th>
                <?php }else{ ?>
                    <th><?=$val?></th>
                <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="font-num" rowspan="<?=$val['rowspan']?>" style="width:60px">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--상품명-->
                    <td class="center text-nowrap" rowspan="<?=$val['rowspan']?>" style="width:300px">
                        <?= $val['goodsNm']; ?> <?= 'y' === $val['delFl'] ? '<small class="text-muted">(삭제상품)</small>':'' ; ?>
                        <br>
                        <small class="text-blue"><?= $val['goodsNo']; ?></small>

                        <!--
                        <div class="btn btn-sm btn-white color-gray btn-stock-graph" data-goodsno = '<?=$val['goodsNo']?>'  style="margin-top:10px;">&nbsp;&nbsp;그래프 보기&nbsp;&nbsp;</div>
                        -->

                    </td>
                    <td class="right bg-light-gray" style="width:100px">
                        <b>옵션</b>
                    </td>
                    <?php foreach($val['optionList'] as $optionData ) { ?>
                    <td class="center text-nowrap bg-light-gray" >
                        <b><?=$optionData['optionName']?></b>
                    </td>
                    <?php } ?>
                    <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                        <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                            <td class="center text-nowrap">
                                -
                            </td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="right text-nowrap bg-light-yellow">
                        <b>현재 재고</b>
                    </td>
                    <?php foreach($val['optionList'] as $optionData ) { ?>
                        <td class="center text-nowrap bg-light-yellow">
                            <?=number_format($optionData['stockCnt'])?>
                        </td>
                    <?php } ?>
                    <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                        <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                            <td class="center text-nowrap">
                                -
                            </td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="right text-nowrap bg-light-yellow">
                        <b>출고 수량</b>
                    </td>
                    <?php foreach($val['optionList'] as $optionData ) { ?>
                        <td class="center text-nowrap bg-light-yellow">
                            <?=number_format($optionData['totalStockCnt'])?>
                        </td>
                    <?php } ?>
                    <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                        <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                            <td class="center text-nowrap">
                                -
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
    </div>

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

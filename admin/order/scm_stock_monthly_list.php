<?php
use SlComponent\Util\SlCodeMap;
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

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

    <div class="table-responsive">
        <table class="table table-rows order-list" >
            <colgroup>
                <col class="" style="width:50px"/>
                <col class="" style="width:250px"/>
                <col class="" style="width:100px"/>
                <?php foreach ($data as $val => $key) { ?>
                    <col/>
                <?php } ?>
            </colgroup>

            <thead>
            <tr>
                <?php foreach ($listTitles as $titleKey => $val) { ?>
                    <?php if(  $titleKey == 3  ) { ?>
                        <th colspan="<?=$maxOptionCnt?>"><?=$val?></th>
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
                        <td class="font-num" rowspan="<?=count($val['monthlyData'])+4?>" style="width:60px">
                            <span class="number"><?= $page->idx--; ?></span>
                        </td>
                        <!--상품명-->
                        <td class="center text-nowrap" rowspan="<?=count($val['monthlyData'])+4?>">
                            <?= $val['goodsNm']; ?>
                            <br>
                            <?php if(!in_array($searchData['scmNo'][0], SlCodeMap::STATISTICS_MERGE)){ ?>
                                <small class="text-blue"><?= $val['goodsNo']; ?></small>
                            <?php } ?>
                        </td>
                        <td class="center bg-light-gray" style=" ">
                            <b>옵션</b><!--<small>(현재)</small>-->
                        </td>
                        <?php foreach($val['optionList'] as $optionData ) { ?>
                            <td class="center bg-light-gray">
                                <b><?=$optionData['optionName']?></b>
                            </td>
                        <?php } ?>
                        <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                            <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                                <td class="center text-nowrap">

                                </td>
                            <?php } ?>
                        <?php } ?>
                    </tr>

                    <?php foreach ( $val['monthlyData'] as $monthlyKey => $monthlyValue ) { /*월별리스트*/ ?>
                    <tr>
                        <td class="center text-nowrap bg-light-yellow">
                            <?=$monthlyKey?>
                        </td>
                        <?php foreach($val['optionList'] as $optionData) { ?>
                        <td class="center">
                            <?php if( !$isProvider ) { ?>
                                <a href="<?=$monthlyValue[$optionData['optionNo']]['detailLink']?>" target="_blank"><?=number_format($monthlyValue[$optionData['optionNo']]['stockOut'])?></a>
                            <?php }else{ ?>
                                <?=number_format($monthlyValue[$optionData['optionNo']]['stockOut'])?>
                            <?php } ?>
                        </td>
                        <?php } ?>
                        <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                            <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                                <td class="center text-nowrap"></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="center bg-light-yellow" >출고합계</td>
                        <?php foreach($val['optionList'] as $optionData) { ?>
                            <td class="center bg-light-yellow" >
                                <?=number_format($optionData['totalStockCnt'])?>
                            </td>
                        <?php } ?>
                        <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                            <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                                <td class="center text-nowrap bg-light-yellow" ></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td class="center bg-light-yellow" >출고비율</td>
                        <?php foreach($val['optionList'] as $optionData) { ?>
                            <td class="center bg-light-yellow" >
                                <?=number_format($optionData['totalStockPercent'])?>%
                            </td>
                        <?php } ?>
                        <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                            <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                                <td class="center text-nowrap bg-light-yellow" ></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td class="center bg-light-yellow" ><b>현재수량</b></td>
                        <?php foreach($val['optionList'] as $optionData) { ?>
                            <td class="center bg-light-yellow" >
                                <b><?=number_format($optionData['currentCnt'])?></b>
                            </td>
                        <?php } ?>
                        <?php if ( $maxOptionCnt > count($val['optionList']) ) { ?>
                            <?php for( $forIdx=0; $forIdx < ($maxOptionCnt-count($val['optionList'])); $forIdx++ ) { ?>
                                <td class="center text-nowrap bg-light-yellow" ></td>
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


        $(function(){
            $('.js-example-basic-single').select2({
                placeholder: '상품 선택'
            });
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

<?php
use SlComponent\Util\SlCodeMap;
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

<script>
    // 정렬&출력수
    $(function(){
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
    <input type="hidden" name="searchFl" value="y"/>
    <div class="table-title ">
        상품 검색
        <?php if(empty($isProvider)) { ?>
        <?php } ?>
    </div>
    <?php include('hk_stock_list_search.php'); ?>
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">

        </div>
        <div class="pull-right">
            <div></div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-rows order-list" >
            <colgroup>
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
                foreach ($data as $valKey => $val) {
                    ?>

                    <?php if( 'y' === $val['seasonRowspan'] && 0 != $valKey ) { ?>
                        <tr>
                            <td colspan="3" class="center bg-gray">소계</td>
                            <td class="center bg-gray"><?=number_format($seasonTotal[$data[$valKey-1]['season']]['pastCnt'])?></td>
                            <td class="center bg-gray"><?=number_format($seasonTotal[$data[$valKey-1]['season']]['lastPastCnt'])?></td>
                            <td class="center bg-gray"><?=number_format($seasonTotal[$data[$valKey-1]['season']]['currentCnt'])?></td>
                            <td class="center bg-gray"><?=number_format($seasonTotal[$data[$valKey-1]['season']]['stockCnt'])?></td>
                            <td class="center bg-gray"><?=number_format($seasonTotal[$data[$valKey-1]['season']]['accSaleCnt'] + $seasonTotal[$data[$valKey-1]['season']]['stockCnt'])?></td>
                        </tr>
                    <?php } ?>

                    <tr class="center">
                        <!--상품명-->
                        <?php if( 'y' === $val['seasonRowspan']) { ?>
                        <td class="center text-nowrap bg-light-gray" rowspan="<?=$span[$val['season']]['seasonSpan']?>" >
                            <?= $val['season']; ?>
                        </td>
                        <?php } ?>
                        <?php if( 'y' === $val['channelRowspan'] ) { ?>
                        <td class="center text-nowrap bg-light-gray2" rowspan="<?=$span[$val['season']]['channelSpan'][$val['channel']]?>">
                            <?= $val['channel']; ?>
                        </td>
                        <?php } ?>
                        <td class="center text-nowrap <?= 'y' === $val['isMain'] ? 'bg-light-gray2':'' ?>">
                            <?= $val['type']; ?>
                        </td>
                        <td class="center text-nowrap <?= 'y' === $val['isMain'] ? 'bg-light-gray2':'' ?>">
                            <?=number_format( $val['pastCnt'])?>
                        </td>
                        <td class="center text-nowrap <?= 'y' === $val['isMain'] ? 'bg-light-gray2':'' ?>">
                            <?=number_format( $val['lastPastCnt']) ?>
                        </td>
                        <td class="center text-nowrap <?= 'y' === $val['isMain'] ? 'bg-light-gray2':'' ?>">
                            <?=number_format( $val['currentCnt']) ?>
                        </td>
                        <td class="center text-nowrap <?= 'y' === $val['isMain'] ? 'bg-light-gray2':'' ?>">
                            <?= number_format($val['stockCnt']) ?>
                        </td>
                        <td class="center text-nowrap <?= 'y' === $val['isMain'] ? 'bg-light-gray2':'' ?>">
                            <?=number_format( $val['accSaleCnt'] + $val['stockCnt'] ) ?>
                        </td>
                    </tr>

                    <?php
                }?>

                <tr>
                    <td colspan="3" class="center bg-gray">소계</td>
                    <td class="center bg-gray"><?=number_format($seasonTotal[$data[count($data)-1]['season']]['pastCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($seasonTotal[$data[count($data)-1]['season']]['lastPastCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($seasonTotal[$data[count($data)-1]['season']]['currentCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($seasonTotal[$data[count($data)-1]['season']]['stockCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($seasonTotal[$data[count($data)-1]['season']]['accSaleCnt'] + $seasonTotal[$data[count($data)-1]['season']]['stockCnt'])?></td>
                </tr>
                <tr class="bold">
                    <td colspan="3" class="center bg-gray">합계</td>
                    <td class="center bg-gray"><?=number_format($total['pastCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($total['lastPastCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($total['currentCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($total['stockCnt'])?></td>
                    <td class="center bg-gray"><?=number_format($total['accSaleCnt'] + $totla['stockCnt'] )?></td>
                </tr>
                
            <?php } else {
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

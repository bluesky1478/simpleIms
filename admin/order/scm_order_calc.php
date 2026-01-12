<?php

use SlComponent\Util\SlCodeMap;

?>
<!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">
-->

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

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
        </div>
        <div class="pull-right">
            <!--
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            -->
        </div>
    </div>
    <table class="table table-rows table-default-center">
        <colgroup>
            <col style="100px" />
            <col style="100px" />
            <col style="100px" />
            <col style="60px" />
        </colgroup>
        <thead>
        <tr>
            <?php foreach ($titleList as $val) { ?>
                <th><?=$val?></th>
            <?php } ?>
            <?php foreach ($goodsList as $val) {
                if( in_array( $val['goodsNo'],SlCodeMap::BUY_LIMIT_DOUBLE_GOODS ) ) $double = true; else $double = false; ?>
                <th>
                    <?=$val['goodsNm']?>
                    <?php if( $double ) { ?>
                        <div class="font-11 sl-blue">(구매제한*2벌제공)</div>
                    <?php } ?>
                    <div class="font-11 text-muted"><?=$val['goodsNo']?></div>
                </th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php foreach ($memberList as $val) { ?>
            <tr >
                <td>
                    <?=$val['memNm']?>
                    <!--
                    <?=$val['memNo']?>
                    -->
                </td>
                <td>
                    <?=$val['memId']?>
                </td>
                <td>
                    <?=$val['nickNm']?>
                </td>
                <td>
                    <?php if( $val['memId'] ==  'sylee_test' ){ ?>
                        <div class="text-danger">신청안함</div>
                    <?php }else{ ?>
                        <?php $cnt=0; ?>
                        <?php foreach ($goodsList as $val2) { ?>
                            <?php $cnt += $mapData[$val['memNo']][$val2['goodsNo']]?>
                        <?php } ?>
                        <?=$cnt?>
                    <?php } ?>
                </td>
                <?php foreach ($goodsList as $val2) {
                    if( in_array( $val2['goodsNo'],SlCodeMap::BUY_LIMIT_DOUBLE_GOODS ) ) $maxCnt = $val['memberConfig']['buyLimitCount'] * 2;
                    else $maxCnt = $val['memberConfig']['buyLimitCount'];
                 ?>
                    <td>
                        <?=number_format($mapData[$val['memNo']][$val2['goodsNo']])?>
                        <?php if( $maxCnt > 0 && 0 > $maxCnt - $mapData[$val['memNo']][$val2['goodsNo']] ) { ?>
                            <div class="font-11 text-danger"><?=$mapData[$val['memNo']][$val2['goodsNo']] - $maxCnt?>개 초과</div>
                        <?php }?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="table-action clearfix">
        <div class="pull-right">
            <!--
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            -->
        </div>
    </div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<script>
    $(function(){
        $('#btnWait').on('click', wait_order);
        $('#btnApply').on('click', apply_order);
        $('#btnCancel').on('click', denied_order);
        //$('#scm-order-delivery').change(function(){});
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
            dialog_confirm('선택한' + $(':checkbox:checked').not('.js-checkall').length + '주문의 출고를 승인하시겠습니까.?', function (result) {
                if (result) {
                    var param = {
                        mode : 'order_accept'
                        , orderNo : orderNo
                    };
                    $.post('scm_member_ps.php', param, function (data) {
                        if( 200 == data.code ){
                            window.location.reload();
                        }else{
                            alert(data.message);
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
            let msg = '선택한 ' + $(':checkbox:checked').not('.js-checkall').length + '건의 주문의 출고 불가 처리 하시겠습니까?';
            $.msgPrompt(msg,'','불가 사유 입력', (confirmMsg)=>{
                if( confirmMsg.isConfirmed ){
                    let param = {
                        mode : 'order_denied'
                        , orderNo : orderNo
                        , reason : confirmMsg.value
                    };
                    $.post('scm_member_ps.php', param, function (data) {
                        if(data){
                            $.msg('출고 불가 처리되었습니다.','').then(()=>{
                                window.location.reload();
                            });
                        }
                    });

                }
            });
        }
    }

</script>

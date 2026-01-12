
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
        $('.simple-download2').click(function(){
            location.href = "<?=$requestUrl?>&downType=3";
        });

        $('.simple-download3').click(function(){
            location.href = "<?=$requestUrl?>&downType=4";
        });

        $('.simple-download5').click(function(){
            location.href = "<?=$requestUrl?>&downType=5";
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
        주문 검색
    </div>

    <?php if( 34 == $scmNo ) { ?>
        <?php include('scm_order_list_search_asiana.php'); ?>
    <?php }else{ ?>
        <?php include('scm_order_list_search.php'); ?>
    <?php } ?>

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
        <div class="pull-right">


            <?php if(empty($isProvider) || 8 == $scmNo ) { ?>
            <button type="button" class="btn btn-white btn-icon-excel simple-download2" >배송지별주문</button>
            <?php } ?>

            <?php if(34 != $scmNo ) { ?>
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            <?php } ?>

        </div>
    </div>

    <?php if( 34 == $scmNo ) { ?>
        <?php include('scm_order_list_template_asiana.php'); ?>
    <?php }else{ ?>
        <?php include('scm_order_list_template.php'); ?>
    <?php } ?>

    <div class="table-action clearfix">
        <?php if( 'y' === $scmConfig['orderAcceptFl'] || empty($isProvider) ) { ?>
        <div class="pull-left">
            <button type="button" class="btn btn-white" id="btnApply">선택 주문 승인</button>
            <button type="button" class="btn btn-white" id="btnCancel">선택 주문 출고불가</button>
            <button type="button" class="btn btn-white" id="btnWait">선택 주문 승인대기</button>
        </div>
        <?php } ?>
        <div class="pull-right">

            <?php if(34 != $scmNo ) { ?>
                <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            <?php }else{ ?>
                <button type="button" class="btn btn-white btn-icon-excel simple-download5" >엑셀다운로드</button>
            <?php } ?>
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
        //$('#scm-order-delivery').change(function(){});
    });

    function downloadHistory(fileName) {
        const originalTable = document.getElementById("order-history");
        // 테이블 복제
        const clone = originalTable.cloneNode(true);
        // "no-export" 클래스가 붙은 요소 제거
        clone.querySelectorAll('.no-export').forEach(el => el.remove());
        // 워크북 생성
        const wb = XLSX.utils.table_to_book(clone, { sheet: "Sheet1" });
        // 파일 저장
        XLSX.writeFile(wb, fileName+'.xlsx').then(()=>{
            alert('test');
        });
    }

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

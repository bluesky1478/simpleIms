<script type="text/javascript">
    // 정렬&출력수
    $(function(){
        $('.manual-order-upload').click(()=>{
            $('#frmExcel').submit();
        });
        $('.manual-order-upload2').click(()=>{
            $('#frmExcel2').submit();
        });
        $('.ktng-manual-order-upload').click(()=>{
            $('#frmExcel3').submit();
        });
    });
</script>

    <link type="text/css"  rel="stylesheet" href="/admin/css/font_awesome/css/font-awesome.css" />

<!--'../../css/admin-custom.css'-->

<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
</div>


<?php if( \SiteLabUtil\SlCommonUtil::isDevId() ) { ?>
<section class="excel-upload-section ">
    <div class="table-title">
        수기 출고 등록
    </div>
    <div class="search-detail-box form-inline">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tr>
                <th>수기 출고지시등록</th>
                <td>
                    <form id="frmExcel" name="frmExcel" action="../erp/erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>

                            <!--<input type="hidden" name="mode" value="set3plOrderTemp"/>-->
                            <input type="hidden" name="mode" value="setYoung9OrderTemp"/>

                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm manual-order-upload" value="업로드">
                            <!--<input type="button"  class="btn btn-white btn-sm" value="등록확인">-->
                            <!--<input type="button"  class="btn btn-white btn-sm btn-icon-excel" value="등록양식 다운로드">-->
                        </div>
                    </form>
                </td>
                <th>영구크린약품</th>
                <td>
                    <form id="frmExcel2" name="frmExcel2" action="../erp/erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="setYounguOrderTemp"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm manual-order-upload2" value="업로드">
                        </div>
                    </form>
                </td>
            </tr>
            <!--<tr>
                <th>KTNG 출고지시등록</th>
                <td>
                    <form id="frmExcel3" name="frmExcel3" action="../erp/erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="setKtngOrderTemp"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm ktng-manual-order-upload" value="업로드">
                        </div>
                    </form>
                </td>
                <th></th>
                <td></td>
            </tr>-->
        </table>
    </div>
</section>
<?php } ?>


<?php include $layoutOrderSearchForm;// 검색 및 프린트 폼 ?>

<form id="frmOrderStatus" action="./order_ps.php" method="post">
    <input type="hidden" name="mode" value="combine_status_change"/>
    <input type="hidden" id="orderStatus" name="changeStatus" value=""/>

    <div class="table-action-dropdown">
        <div class="table-action mgt0 mgb0">
            <div class="pull-left form-inline">
                <span class="action-title">선택한 주문을</span>
                <?php echo gd_select_box('orderStatusTop', null, $selectBoxOrderStatus, null, null, '=주문상태='); ?>
                <button type="submit" class="btn btn-white js-order-status" />일괄처리</button>
                <?php if (gd_is_plus_shop(PLUSSHOP_CODE_ORDERDRAFTEXCEL) === true) { //플러스샵 설치 유무 ?>
                <button type="button" class="btn btn-black order-draft-down" data-target-form="frmSearchOrder" data-search-count="<?=$page->recode['total']?>" data-total-count="<?=$page->recode['amount']?>" data-state-code ="<?=$currentStatusCode?>" data-target-list-form="frmOrderStatus" data-target-list-sno="statusCheck" />발주서 다운로드</button>
                <?php } ?>
            </div>
            <div class="pull-right">
                <div class="form-inline">
                    <?php if ($search['view'] != 'orderGoods') { ?>
                    <div class="dropdown">
                        <?php if( \SiteLabUtil\SlCommonUtil::isDevId() ) { ?>
                        <button type="button" class="btn btn-white" id="btn-run-today-confirm" />
                            <i class="fa fa-play text-blue" aria-hidden="true"></i>
                            출고지시 확인
                        </button>
                        <button type="button" class="btn btn-white" id="btn-run-today-release" />
                            <i class="fa fa-play text-danger" aria-hidden="true"></i>
                            출고지시 실행
                        </button>
                        <?php } ?>
                        <button type="button" class="btn btn-white btn-icon-excel " id="btn-download-today-release" >당일 출고 다운로드</button>
                        <button type="button" class="btn btn-white btn-icon-excel " id="btn-download-today-release2" >당일 출고 재고확인</button>
                        <button type="button" class="btn btn-white " id="btn-download-today-release3" >한전동계_명찰출고</button>
                        <button type="button" class="btn btn-white " id="btn-download-today-release4" >한전하계_명찰출고</button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include $layoutOrderList;// 주문리스트 ?>

    <div class="table-action">
        <div class="pull-left form-inline">
            <span class="action-title">선택한 주문을</span>
            <?php echo gd_select_box('orderStatusBottom', 'orderStatusBottom', $selectBoxOrderStatus, null, null, '=주문상태='); ?>
            <button type="submit" class="btn btn-white js-order-status" />일괄처리</button>
            <?php if (gd_is_plus_shop(PLUSSHOP_CODE_ORDERDRAFTEXCEL) === true) { //플러스샵 설치 유무 ?>
            <button type="button" class="btn btn-black order-draft-down" data-target-form="frmSearchOrder" data-search-count="<?=$page->recode['total']?>" data-total-count="<?=$page->recode['amount']?>" data-state-code ="<?=$currentStatusCode?>" data-target-list-form="frmOrderStatus" data-target-list-sno="statusCheck" />발주서 다운로드</button>
            <?php } ?>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchOrder" data-search-count="<?=$page->recode['total']?>" data-total-count="<?=$page->recode['amount']?>" data-state-code ="<?=$currentStatusCode?>" data-target-list-form="frmOrderStatus" data-target-list-sno="statusCheck" >엑셀다운로드</button>
        </div>
    </div>
</form>

<div class="text-center"><?= $page->getPage(); ?></div>

<form id="frmConfirm" action="../erp/erp_ps.php" method="post" target="ifrmProcess">
    <input type="hidden" name="mode" value="runTodayConfirm">
</form>

<script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>
<script type="text/javascript">
    <?php if (gd_is_plus_shop(PLUSSHOP_CODE_ORDERDRAFTEXCEL) === true) { //플러스샵 설치 유무 ?>
    $(document).ready(function(){
       $('#frmOrderStatus').on('click', '.order-draft-down', function() {
           if ($(this).data('function-auth') == 'deny') {
               dialog_alert("권한이 없습니다. 권한은 대표운영자에게 문의하시기 바랍니다.");
               return false;
           }
           var addParam = {
               "targetListForm": $(this).data('target-list-form'),
               "targetListSno": $(this).data('target-list-sno'),
               "targetForm": $(this).data('target-form'),
               "searchCount": $(this).data('search-count'),
               "totalCount": $(this).data('total-count')
           };

           if ($(this).data('state-code')) addParam.orderStateMode = $(this).data('state-code');
           // 고객 교환/반품/환불신청 관리 탭 페이지 변수
           if ($(this).data('target-list-tabview'))addParam.currentTabView = $(this).data('target-list-tabview');

           layer_add_info('excel_order_draft', addParam);
       });
    });
    <?php } ?>

    $(()=>{
        //튜닝 추가.
        //당일출고 다운로드
        $('#btn-download-today-release').click(()=>{
            location.href = "downloadTodayRelease.php";
        });
        $('#btn-download-today-release2').click(()=>{
            location.href = "downloadTodayRelease.php?showStock=1";
        });
        $('#btn-download-today-release3').click(()=>{
            location.href = "downloadHanName.php";
        });
        $('#btn-download-today-release4').click(()=>{
            location.href = "downloadHanName.php?summer=1";
        });


        $('#btn-run-today-confirm').click(()=>{
            //$('#frmConfirm').submit();
            try{
                $.post('../erp/erp_ps.php',{
                    mode:'runTodayConfirm',
                }, function (data) {
                    alert('확인 메일이 발송되었습니다.');
                });
            }catch(e){
                alert('확인 메일이 발송되었습니다.');
            }
        });

        $('#btn-run-today-release').click(()=>{
            $.msgConfirm('출고 지시 처리를 진행하시겠습니까?','출고 처리된 주문은 상품준비중 상태로 변경됩니다.').then(function(result){
                if( result.isConfirmed ){
                    try{
                        $.post('../erp/erp_ps.php',{
                            mode:'runTodayRelease',
                        }, function (data) {
                            alert('처리 되었습니다.');
                        });
                    }catch(e){
                        alert('처리 되었습니다.');
                    }
                }
            });
        });
    });

</script>

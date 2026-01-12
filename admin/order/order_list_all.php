<script type="text/javascript">
    // 정렬&출력수
    $(function(){
        /*$('#btn-download-today-release').click(()=>{
            location.href = "downloadTodayRelease.php";
        });*/
    });
</script>


<link type="text/css"  rel="stylesheet" href="/admin/css/font_awesome/css/font-awesome.css" />

<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?>
        <small>취소/환불/반품/교환을 포함한 전체 주문리스트입니다.</small>
    </h3>
    <?php if (!isset($isProvider) && $isProvider != true) { ?>
        <div class="btn-group">
            <input type="button" value="일괄 등록" class="btn btn-red-line"  onclick="$('.excel-upload-goods-info').show('fade')"  />
            <a href="order_write.php" class="btn btn-red-line">수기주문 등록</a>
        </div>
    <?php } ?>
</div>

<div class="table-title excel-upload-goods-info display-none">
    주문 정보 일괄등록
</div>
<div class="excel-upload-goods-info display-none">
    <form id="frmModifyGoodsInfo" name="frmModifyGoodsInfo" action="./order_batch_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
        <table class="table table-cols">
            <colgroup>
                <col class="width20p"/>
                <col class="width-xl"/>
            </colgroup>
            <tbody>
            <tr>
                <th>주문 정보 업로드</th>
                <td>
                    <div class="form-inline">
                        <input type="hidden" name="mode" value="regBatchOrder"/>
                        <input type="file" name="excel" value="" class="form-control width50p" />
                        <input type="submit"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
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

<?php include $layoutOrderSearchForm;// 검색 및 프린트 폼 ?>

<form id="frmOrderStatus" action="./order_ps.php" method="post">
    <input type="hidden" name="mode" value="combine_status_change"/>
    <input type="hidden" id="orderStatus" name="changeStatus" value=""/>
    <div class="table-action-dropdown">
        <div class="table-action mgt0 mgb0">
            <?php if ($search['view'] !== 'order') { ?>
                <div class="pull-left form-inline">
                    <span class="action-title">선택한 주문을</span>
                    <?php echo gd_select_box('orderStatusTop', null, $selectBoxOrderStatus, null, null, '=주문상태='); ?>
                    <button type="submit" class="btn btn-white js-order-status"/>
                    일괄처리</button>
                </div>
            <?php } ?>
            <div class="pull-right">
                <div class="form-inline">
                    <?php if ($search['view'] != 'orderGoods') { ?>
                    <div class="dropdown">
                        <button type="button" id="btnSmsLayer" class="btn btn-red js-sms-layer-open dropdown-toggle dropdown-arr" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">SMS발송</button>
                        <ul class="dropdown-menu mgt10" aria-labelledby="btnSmsLayer">
                            <li class="dropdown-item"><a class="js-sms-send" data-type="select" data-opener="order" data-target-selector="input[name*=statusCheck]:checked">선택 주문 배송</a></li>
                            <li class="dropdown-item"><a class="js-sms-send" data-type="search" data-opener="order" data-target-selector="#frmSearchOrder">검색 주문 배송</a></li>
                        </ul>
                    </div>
                    <?php } ?>
                    <?= gd_select_box(
                        'orderPrintMode', null, [
                        'report'         => '주문내역서',
                        'customerReport' => '주문내역서 (고객용)',
                        'reception'      => '간이영수증',
                        'particular'     => '거래명세서',
                        'taxInvoice'     => '세금계산서',
                    ], null, null, '=인쇄 선택=', null
                    ) ?>
                    <input type="button" onclick="order_print_popup($('#orderPrintMode').val(), 'frmOrderPrint', 'frmOrderStatus', 'statusCheck[', <?= $isProvider ? 'true' : 'false' ?>);" value="프린트" class="btn btn-white btn-icon-print"/>

                    <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>

                     <button type="button" class="btn btn-white" id="btn-download-tke-release" >
                        <i class="fa fa-play" aria-hidden="true"></i>
                        TKE 출고패킹리스트
                    </button>
                     <button type="button" class="btn btn-white" id="btn-download-tke-release2" >
                        <i class="fa fa-play" aria-hidden="true"></i>
                        파트너 출고패킹리스트
                    </button>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>

    <?php include $layoutOrderList;// 주문리스트 ?>

    <div class="table-action">
        <?php if ($search['view'] !== 'order') { ?>
            <div class="pull-left form-inline">
                <span class="action-title">선택한 주문을</span>
                <?php echo gd_select_box('orderStatusBottom', 'orderStatusBottom', $selectBoxOrderStatus, null, null, '=주문상태='); ?>
                <button type="submit" class="btn btn-white js-order-status"/>일괄처리</button>
            </div>
        <?php } ?>
        
        <button type="button" class="btn btn-red" id="setPoliBack" />폴리백설정</button>
        <button type="button" class="btn btn-white" id="setPoliBackCancel"/>폴리백취소</button>
        
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchOrder" data-search-count="<?= $page->recode['total'] ?>" data-total-count="<?= $page->recode['amount'] ?>"
                    data-state-code="<?= $currentStatusCode ?>" data-target-list-form="frmOrderStatus" data-target-list-sno="statusCheck">엑셀다운로드
            </button>
        </div>
    </div>
</form>
<div class="text-center"><?= $page->getPage(); ?></div>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/orderList.js?ts=<?= time(); ?>"></script>

<script>
    $(function(){

        $('#btn-download-tke-release').click(()=>{
            location.href='download_tke_release.php';
        });
        $('#btn-download-tke-release2').click(()=>{
            location.href='download_tke_release.php?mode=partner';
        });

    });
</script>
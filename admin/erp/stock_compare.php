<style>
    .table-stock th,td{
        height:30px !important; padding:0!important;;
    }
</style>

<!--스위트 얼럿-->
<!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/promise-polyfill/7.1.0/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.6/sweetalert2.all.min.js"></script>


<script type="text/javascript">
    // 정렬&출력수
    $(function(){

    });
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
                <td colspan="3" >
                    <select class="form-control mgl10" name="scmNo">
                        <?php foreach($scmList as $scmListKey => $scmListData){ ?>
                                <?php if( $search['scmNo']  == $scmListKey ) { ?>
                                <option value="<?=$scmListKey?>" selected><?=$scmListData?></option>
                                <?php }else{ ?>
                                <option value="<?=$scmListKey?>"><?=$scmListData?></option>
                                <?php } ?>
                        <?php } ?>
                    </select>
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

    <div class="table-header form-inline display-none">
        <div class="pull-left">
            전체수량 <strong><?=number_format(0)?></strong> 개
        </div>
        <div class="pull-right">
            <div>
                <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            </div>
        </div>
    </div>

    <div class="search-detail-box form-inline" style="width:50%">
        <table class="table table-cols table-stock">
            <!--<colgroup>
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col >
            </colgroup>-->
            <thead>
            <tr>
                <th>창고재고</th>
                <th>창고미판매</th>
                <th>Total</th>
            </tr>
            <tr>
                <td class="text-center"><?=number_format($data['total'])?></td>
                <td class="text-center"><?=number_format($data['notSale'])?></td>
                <td class="text-center text-danger"><?=number_format($data['total']-$data['notSale'])?></td>
            </tr>
            <tr>
                <th>폐쇄몰 판매</th>
                <th>출고대기</th>
                <th>Total</th>
            </tr>
            <tr>
                <td class="text-center"><?=number_format($data['mallCount'])?></td>
                <td class="text-center"><?=number_format($data['waitCount'])?></td>
                <td class="text-center text-danger"><?=number_format($data['mallCount']+$data['waitCount'])?></td>
            </tr>
            <tr>
                <th>TOTAL1(창고 미판매 제외)</th>
                <td class="text-danger text-center" colspan="2">
                    <b><?=number_format( ($data['total']) - ($data['mallCount']+$data['waitCount']) )?></b>
                </td>
            </tr>
            <tr>
                <th>TOTAL2(창고 미판매 포함)</th>
                <td class="text-danger text-center" colspan="2">
                    <b><?=number_format( ($data['total']-$data['notSale']) - ($data['mallCount']+$data['waitCount']) )?></b>
                </td>
            </tr>
            </thead>
        </table>
    </div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

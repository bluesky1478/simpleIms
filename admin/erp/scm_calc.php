<style>
    .table-stock th,td{
        height:30px !important; padding:0!important;;
    }
    .calc-form table td {
        border-bottom-width : 1px !important; height:50px !important;
    }
</style>

<!--스위트 얼럿-->
<!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/promise-polyfill/7.1.0/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.6/sweetalert2.all.min.js"></script>

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
            <?php if( empty($isProvider) ) { ?>
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
            <?php } ?>
            <tr>
                <th>출고일자</th>
                <td colspan="3">
                    <div class="form-inline" style="padding-left:10px;">

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 6), 'treatDate[]', false) ?>

                    </div>
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

<form id="frmList1" action="" method="get" target="ifrmProcess"  class="calc-form" style="width:49%; display: inline-block">
    <div class="table-title gd-help-manual">
        정산내역
        <div class="pull-right">
            <div >
                <!--
                <button type="button" class="btn btn-white btn-icon-excel simple-download3" >정산내역 다운로드</button>
                -->
                <!--<button type="button" class="btn btn-white btn-icon-excel simple-download2" >기타비용상세 다운로드</button>-->
            </div>
        </div>
    </div>
    <div class="search-detail-box form-inline">
        <table class="table table-cols ">
            <colgroup>
                <col >
                <col >
                <col style="width:100px">
            </colgroup>
            <thead>
            <tr>
                <th>품목</th>
                <th>Total</th>
                <th>상세</th>
            </tr>
            <tr>
                <td class="text-center">
                    <b>출고비용</b>
                    <div class="text-muted">상품별 단가 X 출고건수</div>
                </td>
                <td class="text-center">
                    <?=number_format($totalDataDetail['prdPrice'])?> 원
                </td>
                <td class="text-center">
                    <div class="btn btn-white simple-download">출고상품내역 다운로드</div>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>교환출고비용</b>
                    <div class="text-muted">교환비용 X 교환건수</div>
                </td>
                <td class="text-center">
                    <?=number_format($totalData['exchangePrice'])?> 원
                </td>
                <td class="text-center">
                    <div class="btn btn-white simple-download-exchange">교환상품내역 다운로드</div>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>작업비용</b>
                    <div class="text-muted">출고건수 X 작업비용 단가</div>
                </td>
                <td class="text-center">
                    <?=number_format($totalData['workPrice'])?> 원
                </td>
                <td>
                    <div class="btn btn-white simple-download2">기타비용상세 다운로드</div>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>합포장</b>
                    <div class="text-muted">출고건별 <?=$calcData['packageBegin']?>장 이상 총수량 X 합포장 단가</div>
                </td>
                <td class="text-center ">
                    <?=number_format($totalData['packagePrice'])?> 원
                </td>
                <td>
                    <div class="btn btn-white simple-download2">기타비용상세 다운로드</div>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>폴리백 발송 비용</b>
                    <div class="text-muted"><?=$calcData['polyBoxGuide']?>장 이하 출고건 * 폴리백단가</div>
                </td>
                <td class="text-center ">
                    <?=number_format($totalData['polyPrice'])?> 원
                </td>
                <td>
                    <div class="btn btn-white simple-download2">기타비용상세 다운로드</div>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>박스 발송 비용</b>
                    <div class="text-muted">(<?=$calcData['polyBoxGuide']+1?>장 이상 출고건 박스수량 ) * 박스단가 <span style="display: none">+ ( 박스포장 후 잔여 수량 폴리백단가 )</span></div>
                </td>
                <td class="text-center ">
                    <?=number_format($totalData['boxPrice'])?> 원
                </td>
                <td>
                    <div class="btn btn-white simple-download2">기타비용상세 다운로드</div>
                </td>
            </tr>
            <tr>
                <th >TOTAL</th>
                <td class="text-danger text-center font-16" colspan="2">
                    <b><?=number_format($totalPrice)?></b> 원
                </td>
            </tr>
            </thead>
        </table>
    </div>

</form>


<form id="frmList2" action="" method="get" target="ifrmProcess" class="calc-form"  style="width:49%; display: inline-block">
    <div class="table-title gd-help-manual">
        작업비용
        <div class="pull-right">
            <!--
            <div class="display-none">
                <button type="button" class="btn btn-white btn-icon-excel simple-download2" >엑셀다운로드</button>
            </div>
            -->
        </div>
    </div>
    <div class="search-detail-box form-inline">
        <table class="table table-cols ">
            <thead>
            <tr>
                <th>세부항목</th>
                <th class="text-center">작업비용</th>
                <th class="text-center">조회기간 집계</th>
                <th class="text-center">계산방법</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-center">작업비(출고건당)</td>
                <td class="text-center">
                    <?=number_format($calcData['workAmount'])?> 원
                </td>
                <td class="text-center">
                    <?=number_format($totalData['orderCount'])?> 건 / 상품수량 : <?=number_format($totalData['goodsCount'] )?>장
                </td>
                <td class="text-center">
                    <b><?=number_format($totalData['workPrice'])?></b> = <?=number_format($totalData['orderCount'])?> * <?=number_format($calcData['workAmount'])?>
                </td>
            </tr>
            <tr>
                <td class="text-center">합포장</td>
                <td class="text-center">
                    <?=number_format($calcData['packageAmount'])?> 원
                </td>
                <td class="text-center">
                    <?=number_format($totalData['packageCount'])?> 건 / 합포장 대상 상품수량 : <?=number_format($totalData['packageGoodsCount'] )?> 장
                </td>
                <td class="text-center">
                    <b><?=number_format($totalData['packagePrice'])?></b> = <?=number_format($totalData['packageGoodsCount'])?> * <?=number_format($calcData['packageAmount'])?>
                </td>
            </tr>
            <tr>
                <td class="text-center">폴리백</td>
                <td class="text-center">
                    <?=number_format($calcData['polyAmount'])?> 원
                </td>
                <td class="text-center">
                    <?=number_format($totalData['polyCount'])?> 건
                </td>
                <td class="text-center">
                    <b><?=number_format($totalData['polyPrice'])?></b> = <?=number_format($totalData['polyCount'])?> * <?=number_format($calcData['polyAmount'])?>
                </td>
            </tr>
            <tr>
                <td class="text-center">박스</td>
                <td class="text-center">
                    <?=number_format($calcData['boxAmount'])?> 원
                </td>
                <td class="text-center">
                    <?=number_format($totalData['boxCount'])?> 박스
                </td>
                <td class="text-center">
                    <b><?=number_format($totalData['boxPrice'])?></b> = (<?=number_format($totalData['boxCount'])?> ) * <?=number_format($calcData['boxAmount'])?>
                </td>
            </tr>
            <tr>
                <td class="text-center">교환처리비용</td>
                <td class="text-center">
                    <?=number_format($calcData['exchangeAmount'])?> 원
                </td>
                <td class="text-center">
                    <?=number_format($totalData['exchangeCount'])?> 건
                </td>
                <td class="text-center">
                    <b><?=number_format($totalData['exchangePrice'])?></b> = (<?=number_format($totalData['exchangeCount'])?> ) * <?=number_format($calcData['exchangeAmount'])?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</form>

<script type="text/javascript">
    // 정렬&출력수
    $(function(){
        $('.simple-download').click(()=>{
            location.href = "<?=$requestUrl2?>";
        });
        $('.simple-download-exchange').click(()=>{
            location.href = "<?=$requestUrlExchange?>";
        });
        $('.simple-download2').click(()=>{
            location.href = "<?=$requestUrl1?>";
        });
        $('.simple-download3').click(()=>{
            location.href = "<?=$requestUrl3?>";
        });
    });
</script>


<script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>
<style>
    .table-stock-sale{
        border:none !important;
    }
    .table-stock-sale td{
        padding:0 !important;
        border:none !important;
        border-bottom:solid 1px #f1f1f1 !important;
    }
    .table-stock-sale tr:last-child td {
        border-bottom:none !important;
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
        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });

        $('.simple-download1').click(function(){
            location.href = "<?=$requestUrl?>&downloadType=1";
        });

        $('.excel-submit1').click(()=>{
            $('#frmExcel1').submit();
        });

        $('.input-sale-stock-cnt').keyup(function(){
            let setVal = 0;
            $(this).closest('.table-stock-sale').find('.input-sale-stock-cnt').each(function(){
                setVal += Number($(this).val());
            });

            $(this).closest('.parent-tr').find('.sale-cnt').text(setVal);

            const waitCnt = Number($(this).closest('.parent-tr').find('.wait-cnt').text());
            const stockCnt = Number($(this).closest('.parent-tr').find('.stock-cnt').text());
            $(this).closest('.parent-tr').find('.total-cnt').text( stockCnt-(setVal+waitCnt));

        });

        $('.btn-modify-sale-stock').click(function(){
            $.msgConfirm('재고를 수정하시겠습니까?', '수정 완료후에는 복원이 불가 합니다.').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {

                    let sno = $(this).data('sno');
                    let stockCnt = $(this).parent().find('.input-sale-stock-cnt').val();
                    $.postAsync('./erp_ps.php',{
                        mode:'modifySaleStock',
                        sno : sno,
                        stockCnt : stockCnt
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('재고 수정 완료.','','info').then(()=>{
                                // /location.href='stock_current.php';
                            });
                        }
                    });

                }
            });
        });

        $('.batch-save').click(()=>{
            $.msgConfirm('재고를 일괄 수정하시겠습니까?', '수정 완료후에는 복원이 불가 합니다.').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    let updateList = [];
                    $('.sale-goods-cnt-modify-area').each(function(){
                        let sno = $(this).find('.btn-modify-sale-stock').data('sno');
                        let stock = $(this).find('.input-sale-stock-cnt').val();
                        updateList.push({
                            sno : sno,
                            stock : stock,
                        });
                    });
                    $.postAsync('./erp_ps.php',{
                        mode:'modifySaleStockBatch',
                        updateList : updateList,
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('재고 수정 완료.','','info').then(()=>{
                                // /location.href='stock_current.php';
                            });
                        }
                    });

                }
            });
        });
        $('.batch-set').click(()=>{
            console.log("batch-set");
            $('.sale-goods-cnt-modify-area').each(function(){
                let addStockCnt = Number($(this).find('.stock-add-cnt').val());
                let saleStock = Number($(this).find('.input-sale-stock-cnt').val());

                console.log(addStockCnt);

                if(0 != addStockCnt){
                    console.log( addStockCnt , '+' , saleStock  , '='   , addStockCnt + saleStock );
                    $(this).find('.input-sale-stock-cnt').val(addStockCnt + saleStock);
                }
                //console.log('addStockCnt', addStockCnt);
                //console.log('saleStock', saleStock);
            });
        });


        $('.batch-sync').click(()=>{
            $.msgConfirm('창고 재고를 업데이트 하시겠습니까?', '').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    $.postAsync('./erp_ps.php',{
                        mode:'syncProduct',
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('업데이트 완료.','','info').then(()=>{
                                // /location.href='stock_current.php';
                            });
                        }
                    });

                }
            });
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
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="all" <?=gd_isset($checked['scmFl']['all']); ?> onclick="$('#scmLayer').html('');"/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="y" <?=gd_isset($checked['scmFl']['y']); ?> onclick="layer_register('scm', 'checkbox')"/>
                    </label>
                    <label>
                        <button type="button" class="btn btn-sm btn-gray" onclick="layer_register('scm','checkbox')">고객사 선택</button>
                    </label>

                    <div id="scmLayer" class="selected-btn-group <?=$search['scmFl'] == 'y' && !empty($search['scmNo']) ? 'active' : ''?>">
                        <h5>선택된 고객사 : </h5>
                        <?php if ($search['scmFl'] == 'y') {
                            foreach ($search['scmNo'] as $k => $v) { ?>
                                <span id="info_scm_<?= $v ?>" class="btn-group btn-group-xs">
                                <input type="hidden" name="scmNo[]" value="<?= $v ?>"/>
                                <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                <span class="btn"><?= $search['scmNoNm'][$k] ?></span>
                                <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#info_scm_<?= $v ?>">삭제</button>
                                </span>
                            <?php }
                        } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>검색어</th>
                <td >
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"
                           class="form-control"/>
                </td>
                <th>속성</th>
                <td>
                    <input type="text" name="attr1" placeholder="속성1(분류)" class="form-control w15" value="<?=$search['attr1']?>">
                    <input type="text" name="attr2" placeholder="속성2(시즌)" class="form-control w15" value="<?=$search['attr2']?>">
                    <input type="text" name="attr3" placeholder="속성3(타입)" class="form-control w15" value="<?=$search['attr3']?>">
                    <input type="text" name="attr4" placeholder="속성4(색상)" class="form-control w15" value="<?=$search['attr4']?>">
                    <input type="text" name="attr5" placeholder="속성5(년도)" class="form-control w15" value="<?=$search['attr5']?>">
                    <input type="text" name="optionName" placeholder="사이즈" class="form-control w15" value="<?=$search['optionName']?>">
                </td>
            </tr>
            <tr>
                <th>재고이상 판매상품</th>
                <td colspan="3">
                    <label class="radio-inline"><input type="radio" name="isErrorStockFl" value="all" <?=gd_isset($checked['isErrorStockFl']['all']); ?> />전체</label>
                    <label class="radio-inline"><input type="radio" name="isErrorStockFl" value="1" <?=gd_isset($checked['isErrorStockFl']['1']); ?> />재고 초과 판매건</label>
                    <label class="radio-inline"><input type="radio" name="isErrorStockFl" value="2" <?=gd_isset($checked['isErrorStockFl']['2']); ?> />재고 동일 판매건</label>
                    <label class="radio-inline"><input type="radio" name="isErrorStockFl" value="3" <?=gd_isset($checked['isErrorStockFl']['3']); ?> />재고 미만 판매건</label>
                    <label class="radio-inline"><input type="radio" name="isErrorStockFl" value="4" <?=gd_isset($checked['isErrorStockFl']['4']); ?> />재고 불일치 판매건</label>
                    <label class="radio-inline"><input type="radio" name="isErrorStockFl" value="5" <?=gd_isset($checked['isErrorStockFl']['5']); ?> />차이 불일치 판매건</label>
                </td>
            </tr>
            <tr>
                <th>판매상품 집계방법</th>
                <td colspan="3">
                    <label class="radio-inline"><input type="radio" name="statTypeFl" value="all" <?=gd_isset($checked['statTypeFl']['all']); ?> />전체</label>
                    <label class="radio-inline"><input type="radio" name="statTypeFl" value="1" <?=gd_isset($checked['statTypeFl']['1']); ?> />판매중만 집계</label>
                    <label class="radio-inline"><input type="radio" name="statTypeFl" value="2" <?=gd_isset($checked['statTypeFl']['2']); ?> />노출중만 집계</label>
                    <label class="radio-inline"><input type="radio" name="statTypeFl" value="3" <?=gd_isset($checked['statTypeFl']['3']); ?> />판매+노출된것만 집계</label>
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

    <div class="table-header form-inline">
        <div class="pull-left">
            검색
            <strong><?=number_format($page['totalCnt'])?></strong>
            건

            /

            검색수량
            <strong><?=number_format($page['totalStockCnt'])?></strong>
            개

        </div>
        <div class="pull-right">
            <div>
                <button type="button" class="btn btn-white btn-icon-excel simple-download1" >엑셀다운로드</button>
            </div>
        </div>
        <div class="pull-right">
            <div>
                <button type="button" class="btn btn-white batch-sync" >창고재고 연동</button>
                <button type="button" class="btn btn-white batch-set" >일괄차이처리</button>
                <button type="button" class="btn btn-white batch-save" >일괄수정</button>
            </div>
        </div>
    </div>
    <?php if (empty($data)) { ?>
        <table class="table table-rows">
            <colgroup>
                <col style="width:150px;">
                <col style="width:300px;">
                <col >
            </colgroup>
            <tr>
                <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                    <th><?=$titleValue?></th>
                <?php } ?>
            </tr>
            <td colspan="99" class="text-center">
                검색된 데이터가 없습니다.
            </td>
        </table>
    <?php }else{ ?>

        <table class="table table-rows " >
            <colgroup>
                <col style="width:6%;">
                <col style="width:7%;">
                <col style="width:11%;">
                <col style="width:3%;">
                <col style="width:4%;">
                <col style="width:4%;">
                <col style="width:4%;">
                <col style="width:4%;">
                <col style="width:4%;">
                <col style="width:65px;">
                <col style="width:65px;">
                <col style="width:55px;">
                <col style="width:55px;">
                <col style="">
                <!--<col style="width:160px;">-->
            </colgroup>
            <tr>
                <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                    <th class="text-center"><?=$titleValue?></th>
                <?php } ?>
            </tr>
            <?php foreach ($data as $dataKey => $val) { ?>
                <tr class="parent-tr">
                    <td class="text-center"><?=$val['scmName']?></td>
                    <td class="text-center"><?=$val['thirdPartyProductCode']?></td>
                    <td class="text-left <?=$val['isStockErrorClass']?>">
                        <?=$val['productName']?>
                    </td>
                    <td class="text-center <?=$val['isStockErrorClass']?>">
                        <?=$val['optionName']?>
                    </td>
                    <td>
                        <?=$val['attr1']?>
                    </td>
                    <td>
                        <?=$val['attr2']?>
                    </td>
                    <td>
                        <?=$val['attr3']?>
                    </td>
                    <td>
                        <?=$val['attr4']?>
                    </td>
                    <td>
                        <?=$val['attr5']?>
                    </td>
                    <td class="center text-nowrap" ><!--창고수량-->
                        <span class="stock-cnt"><?=number_format($val['stockCnt']); ?></span>
                    </td>
                    <td class="center text-nowrap <?=$val['isStockErrorClass']?>" ><!--판매수량-->
                        <span class="sale-cnt"><?=number_format($val['saleCnt']); ?></span>
                    </td>
                    <td class="center text-nowrap" >
                        <span class="text-danger wait-cnt"><?=number_format($val['waitCnt']); ?> </span>
                        <br><small class="text-muted"><?=number_format($val['saleCnt']+$val['waitCnt']); ?></small>
                    </td>
                    <td class="center text-nowrap" >
                        <span class="text-blue total-cnt"><?=number_format($val['totalCnt'])?></span>
                    </td>
                    <td>
                        <table class="table table-rows table-stock-sale" style="margin-bottom:0">
                            <colgroup>
                                <col style="width:65%">
                                <col style="width:25%">
                                <!--<col style="width:10%">-->
                            </colgroup>
                            <?php foreach( $val['saleGoodsList'] as $saleGoodsKey => $saleGoods ) { ?>
                            <tr>
                                <td>
                                    <small class="text-muted">(<?=$saleGoods['goodsNo']?>)</small>
                                    <?=$saleGoods['goodsNm']?>
                                    <?=$saleGoods['optionName']?>
                                    <?php if($isDev) { ?>
                                    <i class="fa fa-trash-o text-muted btn-remove-option cursor-pointer hover-btn"  data-sno="<?=$saleGoods['optionSno']?>" aria-hidden="true"></i>
                                    <?php } ?>
                                </td>
                                <td class="form-inline sale-goods-cnt-modify-area">
                                    <?php if( 0 == $saleGoodsKey ){ ?>
                                    <input type="hidden" value="<?=$val['totalCnt']?>" class="stock-add-cnt">
                                    <?php } ?>

                                    <input type="text" value="<?=$saleGoods['stockCnt']?>" class="form-control input-sale-stock-cnt" style="width:100px; display: inline-block">
                                    <div class="btn btn-sm btn-white btn-modify-sale-stock" data-sno="<?=$saleGoods['optionSno']?>">수정</div>
                                </td>
                                <!--
                                <td class="text-center">
                                    <small class="text-muted">판매:<?='y'==$saleGoods['goodsSellFl']?'Y':'N'?> , 노출:<?='y'==$saleGoods['goodsDisplayFl']?'Y':'N'?></small>
                                </td>
                                -->
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                    <!--
                    <td class="text-center form-inline">
                        <label class="radio-inline">
                            <input type="radio" name="kk<?=$dataKey?>" value="y" />예
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="kk<?=$dataKey?>" value="n" />아니오
                        </label>
                    </td>
                    -->
                </tr>
                </tbody>
            <?php } ?>
        </table>
    <?php } ?>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>



<script type="text/javascript">
    let prevDateHtml = '<label class="btn btn-white btn-sm <?='1'===$search['searchPeriod']?'active':''?>" ><input type="radio" name="searchPeriod" value="1" >전일</label>';
    $('[class*=js-dateperiod]').find('label').eq(0).after(prevDateHtml);
</script>
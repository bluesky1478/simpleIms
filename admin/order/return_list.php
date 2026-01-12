<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

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

        $('.btn-proc').click(function(){
            var sno = $(this).data('sno');
            var claimType = $(this).data('claimtype');
            var claimTypeStr = $(this).data('claimtypestr');
            var orderNo = $(this).data('orderno');
            var childNm = 'order_claim';
            var addParam = {
                mode: 'simple',
                sno: sno,
                orderNo: orderNo,
                claimType: claimType,
                claimTypeStr: claimTypeStr,
                layerTitle: claimTypeStr+' 요청 처리',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
            };
            layer_add_info(childNm, addParam);
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
    <div class="btn-group">
        <input type="button" value="반품 등록" class="btn btn-red-line btn-return" data-sno=""/>
    </div>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title gd-help-manual">
        검색
    </div>
    <?php include('return_list_search.php'); ?>
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

    <table class="table table-rows">
        <colgroup>
            <col style="width:60px" /><!--번호-->
            <col style="width:90px" /><!--처리상태-->
            <col style="width:90px" /><!--상품상태-->
            <col style="width:150px" /><!--요청업체/고객명-->
            <col style="width:100px" /><!--원송장번호-->
            <col style="width:200px" /><!--주소-->
            <col style="width:450px" /><!--제품정보-->
            <col  /><!--창고메모-->
            <col style="width:70px" /><!--요청일-->
            <col style="width:70px" /><!--회수일-->
        </colgroup>
        <thead>
        <tr>
        <?php foreach ($adminTitle as $val) { ?>
            <th><?=$val?></th>
        <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list ">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center hover-light">
                    <td class="font-num"><!--번호-->
                        <?=number_format($page->idx--); ?>
                    </td>
                    <td class="center text-nowrap" ><!--처리상태-->
                        <?=$val['returnStatusKr']?>
                    </td>
                    <td class="center text-nowrap" ><!--처리상태-->
                        <?=$val['prdStatusKr']?>
                    </td>
                    <td class="ta-l pdl5 text-nowrap" ><!--요청업체-->
                        <div><?=$val['scmName']?></div>
                        <div>
                            <?php if(empty($val['customerName'])){ ?>
                                <span class="btn-return text-blue cursor-pointer hover-btn"  data-sno="<?=$val['sno']?>">[열기]</span>
                            <?php }else{ ?>
                                <span class="btn-return text-blue cursor-pointer hover-btn"  data-sno="<?=$val['sno']?>"><?=$val['customerName']?></span>
                            <?php } ?>
                        </div>
                    </td>
                    <td class="ta-l pdl5 font-11" ><!--원송장번호-->
                        <?=$val['invoiceNo']?>
                    </td>
                    <td class="ta-l font-11 pdl5" ><!--주소-->
                        <?=$val['address']?>
                    </td>
                    <td class="left text-nowrap" style="vertical-align: middle !important; padding:0" ><!--반품상품-->
                        <?php if(!empty($val['returnGoods']) ) { ?>
                            <table class="table table-rows" style="margin:0; border-bottom:none; border-top:none">
                                <colgroup>
                                    <col style="width:100px"/>
                                    <col />
                                    <col style="width:70px"/>
                                </colgroup>
                                <?php foreach( $val['returnGoods'] as  $returnGoodsKey => $returnGoods ){ ?>
                                    <tr>
                                        <td <?=(0===$returnGoodsKey)?'style="border-top:none"':''?>><?=$returnGoods['prdCode']?></td>
                                        <td <?=(0===$returnGoodsKey)?'style="border-top:none"':''?> class="text-left">
                                            <?=$returnGoods['prdName']?>
                                        </td>
                                        <td <?=(0===$returnGoodsKey)?'style="border-top:none"':''?> class="text-center">
                                            <?=number_format($returnGoods['prdCnt'])?>개
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } ?>
                    </td>
                    <td class="text-left text-nowrap" ><!--메모-->
                        <div style="width: 500px%; white-space : normal	; ">
                            <?=nl2br(str_replace('\n',"\n",$val['partnerMemo']))?>
                        </div>
                    </td>
                    <td class="center font-11" ><!--등록일-->
                        <?=gd_date_format('Y-m-d',$val['regDt']);?>
                    </td>
                    <td class="center font-11" ><!--회수일-->
                        <?=gd_date_format('Y-m-d',$val['returnDt']);?>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <div class="table-action clearfix">

        <div class="pull-left"></div>
        <div class="pull-right">
            <!--<button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>-->
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<script>
    $(function(){
        $('.btn-save').click(function(){
            var sno = $(this).data('sno');
            var claimStatus = $(this).closest('tr').find('.claim-proc').val();
            var memo = $(this).closest('tr').find('.etc-memo').val();
            $.postAsync('<?=$claimApiUrl?>',{
                mode:'updateClaim',
                'sno':sno,
                'memo': memo,
                'claimStatus':claimStatus,
            }).then(function(afterClaimData){
                alert('저장 되었습니다.');
            });
        });

        $('.textarea').each(function(){
            let newText = $(this).val().replaceAll("\\n", "\n");
            $(this).val(newText);
        });

        $('.btn-return').on('click',function(){
            let sno = $(this).data('sno');
            let url = `/order/popup/warehouse_return.php?sno=${sno}`;
            let win = popup({
                url: url,
                target: '',
                width: 925,
                height: 750,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win.focus();
            return win;
        });

    });
</script>

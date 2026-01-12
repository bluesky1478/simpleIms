<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<!--스위트 얼럿-->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="재입고 알림 상품 관리" class="btn btn-red-box js-batch-restock"/>
    </div>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 30); ?>"/>
    <div class="table-title gd-help-manual">
        품절상품 요청 리스트(상품기준)
    </div>
    <?php include('soldout_req_goods_search.php'); ?>
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
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-rows">
        <colgroup>
            <col style="width:55px" /><!--체크-->
            <col style="width:55px" /><!--번호-->
            <col style="width:110px" /><!--공급사-->
            <col style="width:90px" /><!--상품코드-->
            <col style="min-width:120px" /><!--상품명-->
            <col style="width:90px" /><!--옵션-->
            <?php for ( $i=0; $maxOptionCount > $i; $i++ ) { ?>
                <col style="width:80px"  />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th>
                <input type="checkbox" value="y" class="js-checkall" data-target-name="goodsNo">
            </th>
            <?php foreach ($listTitles as $val) { ?>
            <th><?=$val?></th>
            <?php } ?>
            <th colspan="99">옵션별 신청 수량 / 현재 재고</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="center rowspan-class" rowspan="3">
                        <input type="checkbox" name="goodsNo[]" class="check-req-sno" value="<?=$val['goodsNo']?>">
                    </td>
                    <td class="font-num" rowspan="3"><!--번호-->
                        <?=number_format($page->idx--); ?>
                    </td>
                    <td class="center text-nowrap" rowspan="3"><!--공급사-->
                        <?=$val['companyNm']; ?>
                    </td>
                    <td class="center text-nowrap" rowspan="3"><!--상품코드-->
                        <?=$val['goodsNo']; ?>
                    </td>
                    <td class="center text-nowrap" rowspan="3"><!--상품명-->
                        <div>
                            <?=$val['goodsNm']; ?>
                        </div>

                        <small class="text-muted">
                            <a href="soldout_req_list.php?sendType=<?=$search['sendType']?>&key=a.goodsNo&keyword=<?=$val['goodsNo']; ?>" target="_blank" style="color:#919191">알림신청 : <?=$val['reqCnt']; ?></a>
                            /
                            <a href="soldout_req_list.php?sendType=1&key=a.goodsNo&keyword=<?=$val['goodsNo']; ?>" target="_blank"  style="color:#919191">알림발송 : <?=$val['sendCnt']; ?></a>
                        </small>
                    </td>
                    <td class="center bg-light-gray">
                        옵션
                    </td>
                    <?php foreach($val['optionList'] as $option) { ?>
                        <td class="center bg-light-gray">
                            <?=$option['optionFullName']; ?>
                        </td>
                    <?php } ?>
                    <?php if ( $maxOptionCount > count($val['optionList']) ) { ?>
                        <?php for( $forIdx=0; $forIdx < ($maxOptionCount-count($val['optionList'])); $forIdx++ ) { ?>
                            <td class="center text-nowrap"></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="center bg-light-gray">
                        요청수량
                    </td>
                    <?php foreach($val['optionList'] as $option) { ?>
                        <td class="center">
                            <?=number_format($optionReqMap[$val['goodsNo']][$option['sno']])?>
                        </td>
                    <?php } ?>
                    <?php if ( $maxOptionCount > count($val['optionList']) ) { ?>
                        <?php for( $forIdx=0; $forIdx < ($maxOptionCount-count($val['optionList'])); $forIdx++ ) { ?>
                            <td class="center"></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="center bg-light-gray">
                        현재재고
                    </td>
                    <?php foreach($val['optionList'] as $option) { ?>
                        <td class="center">
                            <?=number_format($option['stockCnt'])?>
                        </td>
                    <?php } ?>
                    <?php if ( $maxOptionCount > count($val['optionList']) ) { ?>
                        <?php for( $forIdx=0; $forIdx < ($maxOptionCount-count($val['optionList'])); $forIdx++ ) { ?>
                            <td class="center"></td>
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
            <button type="button" class="btn btn-red" id="btn-req-response">선택한 상품 알림보내기(미전송 대상자)</button>

            <?php if( !empty($categoryList) ) { ?>
            <span id="sendCategory" class="dn">
                주문하기 연결 카테고리 :
                <select id="selected-send-category">
                    <option value="">없음</option>
                    <?php foreach( $categoryList as $scmCateKey => $scmCate ) { ?>
                        <option value="<?=$scmCateKey?>"><?=$scmCate?></option>
                    <?php } ?>
                </select>
                <div>* 공급사 1개를 선택하면 알림톡 발송시 '주문하기' 버튼에 연결할 카테고리를 설정할 수 있습니다. </div>
                <div>* 카테고리를 선택하면 주문하기 버튼을 클릭할 때 해당 카테고리로 자동으로 이동 됩니다. </div>
            </span>
            <?php } ?>
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

        $('#btn-req-response').click(function(){
            var chkCnt = $('input[name*="goodsNo"]:checked').length;
            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return;
            }

            $.msgConfirm(`선택된 상품의 입고 알림톡을 전송하시겠습니까?`,'미전송 대상자 전체').then(function(result){
                if( result.isConfirmed ){

                    let goodsNoList = [];
                    let category = $('#selected-send-category').val();

                    $('input[name*="goodsNo"]:checked').each(function(){
                        goodsNoList.push($(this).val());
                    });

                    let params = {
                        'mode' : 'sendSoldOutRequestList',
                        'goodsNoList': goodsNoList,
                        'category': category,
                    }

                    $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                        if( 200 == data.code ){
                            $.msg('전송이 완료되었습니다.', "", "success").then(()=>{
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // 재입고 알림 상품 관리
        $('.js-batch-restock').on('click', function (e) {
            var addParam = {
                "layerFormID": 'restockBatchLayer',
                "parentFormID": 'restockScmLayer',
                "dataFormID": 'restock_info_scm',
                "layerTitle": '재입고 알림 상품 관리'
            };
            layer_add_info('goods_restock_batch', addParam);
        });

        $('.btn-save').click(function(){
            /*var sno = $(this).data('sno');
            var claimStatus = $(this).closest('tr').find('.claim-proc').val();
            var memo = $(this).closest('tr').find('.etc-memo').val();
            $.postAsync('<?=$claimApiUrl?>',{
                mode:'updateClaim',
                'sno':sno,
                'memo': memo,
                'claimStatus':claimStatus,
            }).then(function(afterClaimData){
                alert('저장 되었습니다.');
            });*/
        });

        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });
        //simple excel download
        $('.simple-download').click(function(){
            location.href = "<?=$requestUrl?>";
        });
        //console.log( '<?=$requestUrl?>' );
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

    function afterProcScmLayerClose(){
        var defaultOption = '<option value="">없음</option>';
        $('#selected-send-category').html(defaultOption);

        if( 1 == $('.selected-scm-no').length ){
            var params = {
                'mode' : 'getScmCategory',
                'scmNo' : $('.selected-scm-no').eq(0).val() ,
            }
            $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                if( 200 == data.code ){
                    data.data.forEach(function(each){
                        var optionHtml = `<option value="${each.cateCd}">${each.cateNm}</option>`;
                        $('#selected-send-category').append(optionHtml);
                    });
                }
            });
        }
    }

</script>

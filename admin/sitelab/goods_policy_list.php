<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!--스위트 얼럿-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/promise-polyfill/7.1.0/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.6/sweetalert2.all.min.js"></script>

<div class="page-header js-affix">
    <h3><?=end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="상품 등록" class="btn btn-red-line js-register"/>
    </div>
</div>

<?php include($goodsSearchFrm); ?>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <input type="hidden" name="mode" value="">
    <input type="hidden" name="modDtUse" value=""/>
    <div class="table-action" style="margin:0;">
        <div class="pull-left">
            <button type="button" class="btn btn-black js-check-free" style="float:left;margin-right:5px">무상정책 연결</button>
            <button type="button" class="btn btn-black js-check-sale" style="float:left;margin-right:5px">할인정책 연결</button>
            <button type="button" class="btn btn-black js-check-member" style="float:left;margin-right:5px">회원 연결</button>
            <div style="display: inline-block; float:left"></div>
        </div>
        <div class="pull-right"></div>
        <div class="pull-left" style="width:100%; padding-top: 5px;">
            <div class="pull-left" style="width:100%; padding-top: 5px;">
                <b style="float: left">* TKE 회원구매유형 설정 : </b>
                <?=gd_select_box('memberType', 'memberType', $memberTypeMap, null, null, __('없음'), ' style="margin-left:10px;float:left" ' , 'form-control')?>
                <button type="button" class="btn btn-red js-check-member-type" style="margin-right:5px:float:left">회원구매 유형 연결</button>
            </div>
            <div class="pull-left"  style="width:100%; padding-top: 5px;">
                <b style="float: left">* 한국타이어 매장유형 설정 : &nbsp; </b>
                <div style="display: inline-block;float: left;margin-right:10px">
                    <?php foreach( $hankookTypeMap as $hankookKey => $hankookType ) { ?>
                        <input type="checkbox" value="<?=$hankookKey?>" class="chk-hankook-type" > <?=$hankookType?>
                    <?php } ?>
                </div>
                <button type="button" class="btn btn-red js-check-hankook-type" style="display: inline-block;float:left; margin-right:5px">매장유형 연결</button>
                <input type="hidden" id="hankookType">
            </div>
            <div class="pull-left"  style="width:100%; padding-top: 5px;">
                <b>* 상품 클레임 추가 사유 지정 : </b>
                <?php foreach( $adminClaimReasonMap as $reasonKey => $reasonType ) { ?>
                    <input type="checkbox" value="<?=$reasonKey?>" class="chk-reason-type" > <?=$reasonType?>
                <?php } ?>
                <button type="button" class="btn btn-red js-check-add-reason" >추가사유 지정</button>
                <input type="hidden" id="addReason">
            </div>
            <div class="pull-left"  style="width:100%; padding-top: 5px;">
                <table class="inner-noline-table" style="display: inline-block">
                    <tr>
                        <th class="text-right" style="border:none; color:#000;padding-right:10px">* 공동구매 설정 : </th>
                        <th class="text-right" style="border:none">공구기간 </th>
                        <td>
                            <input type="date" style="border:solid 1px #d1d1d1; width:130px " class="form-control inline-block" id="groupBuyStart"> ~ <input type="date"   style="border:solid 1px #d1d1d1; width:130px " class="form-control inline-block" id="groupBuyEnd">
                        </td>
                        <th class="text-right">적용수량 </th>
                        <td>
                            <input type="number" class="form-control" placeholder="할인적용수량" style="width:130px" id="groupBuyCount">
                        </td>
                        <th class="text-right">할인가격 </th>
                        <td>
                            <input type="number" class="form-control inline-block" placeholder="할인 가격" style="width:150px" id="groupBuyPrice">원
                        </td>
                        <th class="text-right">안내문구 </th>
                        <td>
                            <input type="text" class="form-control" placeholder="공구 일정 안내" style="width:300px" id="groupBuyComment">
                        </td>
                        <td>
                            <button type="button" class="btn btn-red js-group-buy-save" >공동구매설정 저장</button>
                            <button type="button" class="btn btn-gray js-group-buy-reset" >공동구매설정 삭제</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-rows">
            <thead>
                <tr>
                    <!-- FIELD TITLE -->
                    <?php foreach($titles as $titleHtml) {?>
                        <?=$titleHtml?>
                    <?php } ?>
                </tr>
            </thead>
            <?php foreach ($data as $key => $val) {
                list($totalStock,$stockText) = gd_is_goods_state($val['stockFl'],$val['totalStock'],$val['soldOutFl']); ?>
                <tbody class="list-parent">
                <tr>
                    <!--선택-->
                    <td class="center rowspan-class" ><input type="checkbox" name="goodsNo[<?=$val['goodsNo']; ?>]" value="<?=$val['goodsNo']; ?>" <?php if($val['applyFl'] !='y') { echo "disabled = 'true'"; }  ?> /></td>
                    <!--상품코드번호/공급사명-->
                    <td class="center number rowspan-class" >
                        <?=$val['goodsNo']; ?>
                        <div><small class="bold"><?=$val['scmNm']; ?></small></div>
                        <small class="text-muted"><?=gd_date_format('Y-m-d',$val['regDt'])?></small>
                    </td>
                    <!--상품이미지-->
                    <td class="width-2xs center rowspan-class" >
                        <?=gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 40, $val['goodsNm'], '_blank'); ?>
                    </td>
                    <!--상품명-->
                    <td>
                        <div>
                            <a class="text-blue hand" onclick="goods_register_popup('<?=$val['goodsNo']; ?>' <?php if(gd_is_provider() === true) { echo ",'1'"; } ?>);"><?=$val['goodsNm']; ?></a>
                            <?=empty($totalStock)?'':"<small class='text-muted'>(총재고 : {$totalStock} , 노출:{$val['goodsDisplayFl']} , 판매:{$val['goodsSellFl']} )</small>"?>
                        </div>
                        <div class="notice-ref notice-sm"><?=Globals::get('gDelivery.' . $val['deliveryFl']); ?><?php if ($val['deliveryFl'] == 'free') {
                                echo '(' . $arrDeliveryFree[$val['deliveryFree']] . ')';
                            } ?></div>
                        <!--오픈패키지 연결-->
                        <div class="set-open-package" >
                            <select class="form-control select-goods-type " style="float:left">
                                <option value="0" <?=empty($val['isOpenFl']) ? 'selected': ''?> >일반상품</option>
                                <option value="1" <?=('1' == $val['isOpenFl']) ? 'selected': ''?>>오픈패키지</option>
                            </select>
                            <div class="btn btn-white btn-sm save-open-package" style="margin-left:5px" data-goodsno="<?=$val['goodsNo']?>" >저장</div>
                            <?php
                            
                            foreach($val['optionInfo'] as $okey => $oValue) { ?>
                                <?=$val['optionName'][$okey];?> : <?=$oValue;?><br/>
                            <?php }?>
                            <button type="button" class="btn btn-sm btn-white btn-show-option-modify">▼ 옵션재고수정 열기</button>
                            <button type="button" class="js-layer-grid-option btn btn-sm btn-black" data-type="goods_option" data-goods-option="<?=$val['goodsNo']?>">옵션재고보기</button>

                            <input type="text" class="form-control display-none open-package-input open-goodsno mgt5 clear-both" value="<?=$val['openGoodsNo']?>" placeholder="오픈패키지 원상품 번호" >
                        </div>
                    </td>
                    <!--판매가-->
                    <td class="center text-nowrap">
                        <div><span class="font-num"><?=gd_currency_display($val['goodsPrice']); ?></span></div>
                        <?php if(!empty($val['fixedPrice'])) { ?> <small class="text-muted" style="text-decoration:line-through"><?=gd_currency_display($val['fixedPrice']); ?></small> <?php } ?>
                    </td>
                    <!--공급사 ==> 변경 공동구매 설정 -->
                    <td class="text-nowrap pd5" style="padding:10px;">
                        <?php if( !empty($val['groupBuyStart']) && !empty($val['groupBuyEnd']) && '0000-00-00' !== $val['groupBuyStart'] ) { ?>
                            <b>기간</b> : <?=$val['groupBuyStart']; ?> ~ <?=$val['groupBuyEnd']; ?>
                            <br><b>적용수량/할인가</b> : <?=$val['groupBuyCount']; ?>개 이상 구매시 <?=number_format($val['groupBuyPrice']); ?>원
                            <br><b>안내문구</b> : <?=$val['groupBuyComment']; ?>
                        <?php } ?>
                    </td>
                    <!--적용정책-->
                    <td class="center">
                        <ul class="text-left">
                        <?php if(!empty($val['freePolicy']['sno'])) { ?>
                            <li>▶
                                <span class="sp-free-policy"><?=$val['freePolicy']['name']?></span>
                                <button type="button" class="btn btn-icon-delete btn-policy-delete" data-toggle="delete" data-updatekey="policyFreeSaleSno" data-policytype="freeSale" data-sno="<?=$val['freePolicy']['sno']?>" data-goodsno="<?=$val['goodsNo']; ?>"  >삭제</button>
                            </li>
                        <?php } ?>
                        <?php if(!empty($val['salePolicy']['sno'])) { ?>
                            <li>▶
                                <span class="sp-sale-policy"><?=$val['salePolicy']['name']?></span>
                                <button type="button" class="btn  btn-icon-delete btn-policy-delete" data-toggle="delete" data-updatekey="policySaleSno" data-policytype="sale" data-sno="<?=$val['salePolicy']['sno']?>"  data-goodsno="<?=$val['goodsNo']; ?>">삭제</button>
                            </li>
                        <?php } ?>
                        <?php if(!empty($val['surveyPolicy']['sno'])) { ?>
                            <li>▶
                                <?=$val['surveyPolicy']['name']?>
                                <button type="button" class="btn btn-icon-delete btn-policy-delete" data-toggle="delete" data-updatekey="policySurveySno" data-policytype="survey" data-sno="<?=$val['surveyPolicy']['sno']?>"  data-goodsno="<?=$val['goodsNo']; ?>">삭제</button>
                            </li>
                        <?php } ?>
                        </ul>

                        <?php if(!empty($val['freePolicy']['sno']) || !empty($val['salePolicy']['sno']) || !empty($val['surveyPolicy']['sno']) ) { ?>
                        <div class="mgt10">
                            <button type="button" class="js-layer-goods-member btn btn-sm btn-black " style="height: 20px !important;" data-goodsnm="<?=$val['goodsNm']; ?>" data-goodsno="<?=$val['goodsNo']?>">적용회원보기</button>
                        </div>
                        <div>
                            <?=$val['policyMember']?>명 적용됨
                        </div>
                        <?php } ?>
                    </td>
                    <!--특이사항-->
                    <td class="center">
                        <?=$val['memberTypeName']?>
                        <?=$val['hankookTypeName']?>
                    </td>
                    <!--클레임 추가사유-->
                    <td class="">
                        <?=$val['addReasonName']?>
                    </td>
                    <!--품절메세지-->
                    <td class="left sold-out-memo-area" >
                        <?php if( !empty($val['sizeFilePath']) ) { ?>
                        <div>
                            <div>
                                사이즈표 파일 : <a href="<?=$val['sizeFilePath']?>" target="_blank" class="bold text-blue font-14">확인</a>
                                <i class="fa fa-times-circle  cursor-pointer hover-btn text-danger size-file-delete" aria-hidden="true" data-goodsno="<?=$val['goodsNo']?>"></i>
                            </div>
                            <input type="file" class="size-file" >
                        </div>
                        <?php }else{ ?>
                            <div>
                                사이즈표 파일 : <span class="text-muted">없음</span>
                            </div>
                            <input type="file" class="size-file" >
                        <?php } ?>
                        <div class="mgt5">
                            <input type="text" class="form-control width-xl sold-out-memo-text" style="float:left" value="<?=$val['soldOutMemo']?>" placeholder="품절메세지 표기">
                            <div class="btn btn-white btn-sm save-soldout-memo" style="float:left;margin-left:5px" data-goodsno="<?=$val['goodsNo']?>" >저장</div>
                        </div>
                    </td>
                </tr>
                <tr class="table-show-option-modify dn display-none">
                    <td colspan="99" >
                        <?php include( '_option_stock_list.php' ); ?>
                    </td>
                </tr>
                </tbody>
            <?php } ?>
        </table>
    </div>

    <div class="table-action">
        <div class="pull-left">
            <button type="button" class="btn btn-black js-check-free">무상정책 연결</button>
            <button type="button" class="btn btn-black js-check-sale">할인정책 연결</button>
            <button type="button" class="btn btn-black js-check-survey">설문정책 연결</button>
        </div>
        <div class="pull-left" style="width:100%; padding-top: 5px;">
            <?php if(gd_is_provider() === false) {?>
            <button type="button" class="btn btn-white js-check-maindisplay">메인상품진열</button>
            <?php }?>
            <button type="button" class="btn btn-white js-check-group">분류관리</button>
            <button type="button" class="btn btn-white js-check-moddt">수정일변경</button>
            <button type="button" class="btn btn-white js-check-soldout">품절처리</button>
            <button type="button" class="btn btn-white js-check-copy">선택 복사</button>
            <button type="button" class="btn btn-white js-check-delete">선택 삭제</button>
            <div class="pull-right">
                <!--<button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchGoods" data-target-list-form="frmList" data-target-list-sno="goodsNo" data-search-count="<?/*=$page->recode['total']*/?>" data-total-count="<?/*=$page->recode['amount']*/?>">엑셀다운로드</button>-->
            </div>
        </div>
    </div>
</form>
<div class="text-center"><?=$page->getPage();?></div>

<script type="text/javascript">
    <!--
    $(document).ready(function () {

        // 삭제
        $('button.js-check-delete').click(function () {

            var chkCnt = $('input[name*="goodsNo"]:checked').length;

            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return;
            }

            dialog_confirm('선택한 ' + chkCnt + '개 상품을  정말로 삭제하시겠습니까?\n삭제 된 상품은 [삭제상품 리스트]에서 확인 가능합니다.', function (result) {
                if (result) {
                    $('#frmList input[name=\'mode\']').val('delete_state');
                    $('#frmList').attr('method', 'post');
                    $('#frmList').attr('action', './goods_ps.php');
                    $('#frmList').submit();
                }
            });

        });

        $('button.js-check-copy').click(function () {
            var chkCnt = $('input[name*="goodsNo"]:checked').length;
            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return;
            }
            dialog_confirm('선택한 ' + chkCnt + '개 상품을  정말로 복사하시겠습니까?', function (result) {
                if (result) {
                    $('#frmList input[name=\'mode\']').val('copy');
                    $('#frmList').attr('method', 'post');
                    $('#frmList').attr('action', './goods_ps.php');
                    $('#frmList').submit();
                }
            });

        });

        $('button.js-check-soldout').click(function () {
            var chkCnt = $('input[name*="goodsNo"]:checked').length;
            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return;
            }

            dialog_confirm('선택한 ' + chkCnt + '개 상품을 품절처리 하시겠습니까?', function (result) {
                if (result) {
                    //상품수정일 변경 확인 팝업
                    <?php if ($goodsConfig['goodsModDtTypeList'] == 'y' && $goodsConfig['goodsModDtFl'] == 'y') { ?>
                    dialog_confirm("상품수정일을 현재시간으로 변경하시겠습니까?", function (result) {
                        if (result) {
                            $('input[name="modDtUse"]').val('y');
                        } else {
                            $('input[name="modDtUse"]').val('n');
                        }
                        $('#frmList input[name=\'mode\']').val('soldout');
                        $('#frmList').attr('method', 'post');
                        $('#frmList').attr('action', './goods_ps.php');
                        $('#frmList').submit();
                    }, '상품수정일 변경', {cancelLabel:'유지', 'confirmLabel':'변경'});
                    <?php } else { ?>
                        //상품 수정일 변경 범위설정 체크
                        <?php if ($goodsConfig['goodsModDtTypeList'] == 'y') { ?>
                            $('input[name="modDtUse"]').val('y');
                        <?php } else { ?>
                            $('input[name="modDtUse"]').val('n');
                        <?php } ?>
                        $('#frmList input[name=\'mode\']').val('soldout');
                        $('#frmList').attr('method', 'post');
                        $('#frmList').attr('action', './goods_ps.php');
                        $('#frmList').submit();
                    <?php } ?>
                }
            });

        });

        // 등록
        $('.js-register').click(function () {
            location.href = './goods_register.php';
        });

        $('select[name=\'pageNum\']').change(function () {
            $('#frmSearchGoods').submit();
        });

        $('select[name=\'sort\']').change(function () {
            $('#frmSearchGoods').submit();
        });

        <?php if(gd_is_provider() === false) {?>
        $('button.js-check-populate').click(function () {
            var chkCnt = $('input[name*="goodsNo"]:checked').length;

            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return false;
            }else if (chkCnt > 100) {
                alert('인기상품노출수정은 1회 100개까지만 수정할 수 있습니다.');
                return false;
            }else{
                display_populate_popup(<?php if(gd_is_provider() === true) { echo ",'1'"; } ?>);
            }

        });
        <?php } ?>

        // 메인상품진열 설정
        <?php if(gd_is_provider() === false) {?>
        $('button.js-check-maindisplay').click(function () {
            var chkCnt = $('input[name*="goodsNo"]:checked').length;

            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return false;
            }else if (chkCnt > 100) {
                alert('메인상품진열은 1회 100개까지만 수정할 수 있습니다.');
                return false;
            }else{
                display_main_popup(<?php if(gd_is_provider() === true) { echo ",'1'"; } ?>);
            }

        });
        <?php } ?>

        // 분류관리 설정
        $('button.js-check-group').click(function () {
            var chkCnt = $('input[name*="goodsNo"]:checked').length;

            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return false;
            }else if (chkCnt > 100) {
                alert('분류관리는 1회 100개까지만 수정할 수 있습니다.');
                return false;
            }else{
                category_popup(<?php if(gd_is_provider() === true) { echo "1"; } ?>);
            }

        });

        // 수정일 변경
        $('button.js-check-moddt').click(function(){
            var chkCnt = $('input[name*="goodsNo"]:checked').length;

            if (chkCnt == 0) {
                alert('선택된 상품이 없습니다.');
                return;
            }

            var childNm = 'goods_moddt';
            var addParam = {
                mode: 'simple',
                layerTitle: '상품 수정일 변경',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm
            };
            layer_add_info(childNm, addParam);
        });

    });

    <?php if(gd_is_provider() === false) {?>
    /**
     * 인기상품노출수정 등록/수정 팝업창
     *
     * @author seonghu
     */
    function display_populate_popup(isProvider, page) {
        url = '/share/popup_populate_list.php?popupMode=yes';

        if (page) url += page;

        win = popup({
            url: url,
            width: 1000,
            height: 800,
            resizable: 'yes'
        });
    }
    <?php } ?>

    <?php if(gd_is_provider() === false) {?>
    /**
     * 메인상품진열 등록/수정 팝업창
     *
     * @author sueun
     */
    function display_main_popup(isProvider, page) {
        var url = '/share/popup_display_main_list.php?popupMode=yes';

        if (page) url += page;

        win = popup({
            url: url,
            width: 1000,
            height: 800,
            resizable: 'yes',
            scrollbars: 'yes'
        });
    }
    <?php } ?>

    /**
     * 분류관리 팝업창
     *
     * @author sueun
     */
    function category_popup(isProvider) {
        if(isProvider) var url = '/provider/share/popup_display_main_group.php?popupMode=yes';
        else var url = '/share/popup_display_main_group.php?popupMode=yes';

        win = popup({
            url: url,
            width: 1000,
            height: 800,
            resizable: 'yes',
            scrollbars : 'yes',
        });
    }

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

    //-->
</script>

<!--추가 스크립트-->
<script type="text/javascript">
    <!--
    $(function(){

        //오픈패키지 설정
        $('.select-goods-type').change(function() {
            var $parentEl = $(this).closest('.set-open-package');
            if( '1' == $(this).val() ){
                $parentEl.find('.open-package-input').removeClass('display-none');
            }else{
                $parentEl.find('.open-package-input').addClass('display-none');
            }
        });
        //초기화
        $('.select-goods-type').change();

        //오픈 패키지 저장
        $('.save-open-package').click(function(){
            var $parentEl = $(this).closest('.set-open-package');
            var param = {
                mode : 'save_open_package',
                goodsNo : $(this).data('goodsno'),
                isOpenFl : $parentEl.find('.select-goods-type').eq(0).val(),
                openGoodsNo : $parentEl.find('.open-goodsno').eq(0).val(),
            };
            $.post('goods_ps.php', param, function (result){
                alert('저장되었습니다.');
            });
        });

        //사이즈표 삭제
        $('.size-file-delete').click(function(){
            $.msgConfirm('사이즈표를 삭제 하시겠습니까?', '').then((confirmData)=>{
                if (true === confirmData.isConfirmed) {
                    $.postAsync('goods_ps.php',{
                        mode:'delete-size-file',
                        goodsNo: $(this).data('goodsno')
                    }).then((data)=>{
                        $.msg('저장되었습니다.', "", "success").then(()=>{
                            location.reload();
                        });
                    });
                }
            });
        });

        //품절 메세지 저장 (+ 사이즈표 저장)
        $('.save-soldout-memo').click(function(){
            var $parentEl = $(this).closest('.sold-out-memo-area');

            var file_data = $parentEl.find('.size-file').prop('files')[0];
            var form_data = new FormData();
            form_data.append('mode', 'save_soldout_memo');
            form_data.append('file', file_data);
            form_data.append('goodsNo', $(this).data('goodsno'));
            form_data.append('soldOutMemo', $parentEl.find('.sold-out-memo-text').eq(0).val());

            $.ajax({
                url: 'goods_ps.php',
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(response){
                    //console.log(response);
                    $.msg('저장되었습니다.', "", "success").then(()=>{
                        location.reload();
                    });
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });
            /*$.post('goods_ps.php', param, function (result){
                alert('저장되었습니다.');
            });*/
        });

        $(".btn-policy-delete").click(function(){
            var $thisEl = $(this);
            var updateObject = {
                sno : $thisEl.data('sno')
                , updateKey : $thisEl.data('updatekey')
                , updateValue : ''
                , updatePolicyType : $thisEl.data('policytype') // freeSale, sale, survey
                , goodsNo : $thisEl.data('goodsno') // freeSale, sale, survey
            }
            var callback = function(){
                if( confirm('선택하신 상품과 연결된 정책을 해제하시겠습니까?') ){
                    $thisEl.closest('td').html("");
                }
            };
            deleteGoodsPolicy(updateObject,callback);
        });

        $('.chk-hankook-type').change(function(){
            var typeValue = 0;
            $('.chk-hankook-type').each(function(){
                if( true === $(this).is(':checked') ){
                    typeValue += Number( $(this).val() );
                }
            });
            if( 0 === typeValue  ){
                $('#hankookType').val('');
            }else{
                $('#hankookType').val(typeValue);
            }
        });


        $('.chk-reason-type').change(function(){
            var chkList = [];
            $('.chk-reason-type').each(function(){
                if( true === $(this).is(':checked') ){
                    chkList.push( $(this).val() );
                }
            });
            $('#addReason').val(chkList.join(','));
        });


        //옵션보여주기
        $('.btn-show-option-modify').click(function(){


            let $el = $(this).closest('.list-parent').find('.table-show-option-modify');
            if($el.hasClass('display-none')){
                //$('.btn-show-option-modify').text('▲ 옵션재고수정 닫기');
                $(this).closest('.list-parent').find('.btn-show-option-modify').text('▲ 옵션재고수정 닫기');
                $el.removeClass('display-none');
                $(this).closest('.list-parent').find('.rowspan-class').attr('rowspan','2');
            }else{
                //$('.btn-show-option-modify').text('▼ 옵션재고수정 열기');
                $(this).closest('.list-parent').find('.btn-show-option-modify').text('▼ 옵션재고수정 열기');
                $el.addClass('display-none');
                $(this).closest('.list-parent').find('.rowspan-class').attr('rowspan','1');
            }
        });


    });

    //정책 업데이트
    function updatePolicy(updateObject,callback){
        updateObject.mode = 'update_policy';
        $.post('goods_ps.php', updateObject, function (data) {
            if(data){
                if(typeof callback !== 'undefined'){
                    callback();
                }
                //console.log('update complete');
            }
        });
    }
    function deleteGoodsPolicy(updateObject,callback){
        updateObject.mode = 'delete_goods_link_policy';
        $.post('goods_ps.php', updateObject, function (data) {
            if(data){
                if(typeof callback !== 'undefined'){
                    callback();
                }
                //console.log('update complete');
            }
        });
    }

    //회원 연결
    $('button.js-check-member').click(function () {
        var chkCnt = $('input[name*="goodsNo"]:checked').length;
        if (chkCnt == 0) {
            alert('선택된 상품이 없습니다.');
            return;
        }
        var childNm = 'policy_link_member';
        var addParam = {
            mode: 'simple',
            layerTitle: '회원연결',
            layerFormID: childNm + "Layer",
            parentFormID: childNm + "Row",
            dataFormID: childNm + "Id",
            dataInputNm: childNm
        };
        layer_add_info(childNm, addParam);
    });

    // 무상정책 설정
    $('button.js-check-free').click(function () {
        var isContinue = true;
        //띄우기 전 Validation
        $('input[name*="goodsNo"]:checked').each(function(){
            var checkSale = $.isEmpty($(this).closest('tr').find('.sp-sale-policy').eq(0).html());
            if( !checkSale ){
                alert('무상정책과 할인정책을 함께 설정할 수 없습니다.');
                isContinue = false;
                return false;
            }
        });

        if( !isContinue ) return false;

        var selectedGoodsCntMessage = '';
        var selectedGoodsCnt = $('input[name*="goodsNo"]:checked').length;
        if(selectedGoodsCnt > 0){
            selectedGoodsCntMessage = '/연결(' + selectedGoodsCnt + '개 상품 선택됨)'
        }

        var childNm = 'free_policy';
        var addParam = {
            mode: 'simple',
            layerTitle: '무상정책 설정' + selectedGoodsCntMessage,
            layerFormID: childNm + "Layer",
            parentFormID: childNm + "Row",
            dataFormID: childNm + "Id",
            dataInputNm: childNm,
            selectedGoodsCnt : selectedGoodsCnt
        };
        layer_add_info(childNm, addParam);
    });

    // 할인정책 설정
    $('button.js-check-sale').click(function () {

        var isContinue = true;

        //띄우기 전 Validation
        $('input[name*="goodsNo"]:checked').each(function(){
            var checkSale = $.isEmpty($(this).closest('tr').find('.sp-free-policy').eq(0).html());
            if( !checkSale ){
                alert('할인정책과 무상정책을 함께 설정할 수 없습니다.');
                isContinue = false;
                return false;
            }
        });

        if( !isContinue ) return false;

        var selectedGoodsCntMessage = '';
        var selectedGoodsCnt = $('input[name*="goodsNo"]:checked').length;
        if(selectedGoodsCnt > 0){
            selectedGoodsCntMessage = '/연결(' + selectedGoodsCnt + '개 상품 선택됨)'
        }

        var childNm = 'sale_policy';
        var addParam = {
            mode: 'simple',
            layerTitle: '할인정책 설정' + selectedGoodsCntMessage,
            layerFormID: childNm + "Layer",
            parentFormID: childNm + "Row",
            dataFormID: childNm + "Id",
            dataInputNm: childNm,
            selectedGoodsCnt : selectedGoodsCnt
        };
        layer_add_info(childNm, addParam);

    });

    // 할인정책 설정
    $('button.js-check-survey').click(function () {
        var selectedGoodsCntMessage = '';
        var selectedGoodsCnt = $('input[name*="goodsNo"]:checked').length;
        if(selectedGoodsCnt > 0){
            selectedGoodsCntMessage = '/연결(' + selectedGoodsCnt + '개 상품 선택됨)'
        }
        var childNm = 'survey_policy';
        var addParam = {
            mode: 'simple',
            layerTitle: '설문정책 설정' + selectedGoodsCntMessage,
            layerFormID: childNm + "Layer",
            parentFormID: childNm + "Row",
            dataFormID: childNm + "Id",
            dataInputNm: childNm,
            selectedGoodsCnt : selectedGoodsCnt
        };
        layer_add_info(childNm, addParam);
    });

    //적용회원 리스트 가져오기
    $('button.js-layer-goods-member').click(function (e) {
        var goodsNo = $(this).data('goodsno');
        var goodsNm = $(this).data('goodsnm');
        var childNm = 'policy_goods_member';
        var addParam = {
            mode: 'simple',
            layerTitle: goodsNm+' 정책 적용 회원',
            layerFormID: childNm + "Layer",
            parentFormID: childNm + "Row",
            dataFormID: childNm + "Id",
            dataInputNm: childNm,
            goodsNo: goodsNo,
        };
        layer_add_info(childNm, addParam);
    });

    //상품 체크
    var checkGoods = function(){
        var goodsList = [];
        var chkCnt = $('input[name*="goodsNo"]:checked').length;
        if (chkCnt == 0) {
            alert('선택된 상품이 없습니다.');
        }
        $('input[name*="goodsNo"]:checked').each(function(){
            goodsList.push( $(this).val() );
        });
        return goodsList;
    }

    //회원유형 수정
    $('.js-check-member-type').click(function () {
        var goodsList = checkGoods();
        if( goodsList.length > 0 ){
            var param = {
                mode: 'set_member_type',
                selectedGoods : goodsList,
                memberType : $('#memberType option:checked').val()
            };
            $.post('goods_ps.php', param, function (data) {
                if(data){
                    location.reload();
                }
            });
        }
    });

    //한국타이어 매장 유형 수정
    $('.js-check-hankook-type').click(function () {
        var goodsList = checkGoods();
        if( goodsList.length > 0 ){
            var param = {
                mode: 'set_hankook_type',
                selectedGoods : goodsList,
                hankookType : $('#hankookType').val()
            };
            $.post('goods_ps.php', param, function (data) {
                if(data){
                    location.reload();
                }
            });
        }
    });

    //상품 추가 사유 수정
    $('.js-check-add-reason').click(function () {
        var goodsList = checkGoods();
        if( goodsList.length > 0 ){
            var param = {
                mode: 'set_add_reason',
                selectedGoods : goodsList,
                addReason : $('#addReason').val()
            };
            $.post('goods_ps.php', param, function (data) {
                if(data){
                    location.reload();
                }
            });
        }
    });

    //공동구매 저장
    $('.js-group-buy-save').click(function(){
        var goodsList = checkGoods();
        if( goodsList.length > 0 ){
            var param = {
                mode: 'saveGroupBuy',
                selectedGoods : goodsList,
                groupBuyStart : $('#groupBuyStart').val(),
                groupBuyEnd : $('#groupBuyEnd').val(),
                groupBuyCount : $('#groupBuyCount').val(),
                groupBuyPrice : $('#groupBuyPrice').val(),
                groupBuyComment : $('#groupBuyComment').val(),
            };
            $.post('goods_ajax.php', param, function (data) {
                if(data){
                    alert('저장 되었습니다.');
                }
            });
        }
    });
    //공동구매 리셋
    $('.js-group-buy-reset').click(function(){
        var goodsList = checkGoods();
        if( goodsList.length > 0 ){
            var param = {
                mode: 'resetGroupBuy',
                selectedGoods : goodsList,
            };
            $.post('goods_ajax.php', param, function (data) {
                if(data){
                    location.reload();
                }
            });
        }
    });

    //-->
</script>

<script type="text/javascript">

    $(function(){
        //일괄 값 셋팅
        $('.btn-batch-proc').click(function(){
           let $parentEl = $(this).closest('.set-goods-option-list');
           let stockCnt = $parentEl.find('.batch-proc-value').val();
           $parentEl.find('.stock-modify-cnt').val(stockCnt);
        });

        $('.btn-proc-modify').click(function(){
            let $parentEl = $(this).closest('tbody');
            let stockFlText = $parentEl.find('.sel-batch-proc option:selected').text();
            $.msgConfirm('재고 ' + stockFlText + '을(를) 진행하시겠습니까?', "").then((result)=>{
                if( result.isConfirmed ){
                    let goodsNo = $(this).data('goodsno');
                    let stockFl = $parentEl.find('.sel-batch-proc').val();
                    let stockOptionSnoList = [];
                    let stockCurrentCnt = [];
                    let stockCnt = [];

                    $parentEl.find('.stock-current').each(function(){
                        stockOptionSnoList.push($(this).data('optionsno'));
                        stockCurrentCnt.push($(this).val());
                    });
                    $parentEl.find('.stock-modify-cnt').each(function(){
                        stockCnt.push($(this).val());
                    });

                    let params = {
                        mode : 'setBatchStock',
                        goodsNo : goodsNo,
                        stockFl : stockFl,
                        stockOptionSnoList : stockOptionSnoList,
                        stockCurrentCnt : stockCurrentCnt,
                        stockCnt : stockCnt,
                    }

                    $.postAsync('goods_ajax.php', params).then((returnValue)=>{
                        alert(returnValue.message);
                        $parentEl.find('.display-current-cnt').each(function(idx){
                            let newStockCnt = returnValue.data[idx]['stockCnt'];
                            $(this).text(newStockCnt);
                            $parentEl.find('.stock-current').eq(idx).val(newStockCnt);
                        });
                        $parentEl.find('.stock-modify-cnt').val('');
                    });
                }
            });

        });

    });

</script>




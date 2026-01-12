<style>
    .table-stock th,td{
        height:30px !important; padding:0!important;;
    }
    .simple-download-detail { cursor: pointer }
    .simple-download-detail:hover { opacity: 0.6 }
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

        $('.simple-download').click(function(){
            location.href = "<?=$requestUrl?>";
        });

        $('.simple-download-detail').click(function(){
            let detailKey = $(this).data('detailKey');
            location.href = "<?=$requestUrl?>&detailKey="+detailKey;
        });

        $('.excel-submit1').click(()=>{
            $('#frmExcel1').submit();
        });


        $('.btn-remove-option').click(function(){
            const optionSno = $(this).data('sno');
            $.msgConfirm('상품 옵션을 삭제하시겠습니까?', '수정 완료후에는 복원이 불가 합니다.').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    $.postAsync('./erp_ps.php',{
                        mode:'removeGoodsOption',
                        sno : optionSno,
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('처리 완료.','','success').then(()=>{
                                location.reload();
                            });
                        }
                    });
                }
            });
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
            <?php if(empty($isProvider)) { ?>
            <tr>
                <th>고객사 구분</th>
                <td >
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
                <th rowspan="2">출고내역확인</th>
                <td rowspan="2">

                    <div class="checkbox mgl10 mgt10">
                        <label class="radio-inline"><input type="radio" id="isViewModeAll" class="isViewMode"  name="isViewMode" value="all" <?=gd_isset($checked['isViewMode']['all']); ?> />기본</label>
                        <label class="radio-inline"><input type="radio" id="isViewMode1" class="isViewMode" name="isViewMode" value="1" <?=gd_isset($checked['isViewMode']['1']); ?> />출고요약보기</label>
                        <label class="radio-inline"><input type="radio" id="isViewMode2" class="isViewMode" name="isViewMode" value="3" <?=gd_isset($checked['isViewMode']['3']); ?> />연별출고내역보기</label>
                        <label class="radio-inline"><input type="radio" id="isViewMode2" class="isViewMode" name="isViewMode" value="2" <?=gd_isset($checked['isViewMode']['2']); ?> />월별출고내역보기</label>
                    </div>
                    <div style="margin:10px;" class="form-inline" id="outDatePeriod">

                        출고 기간 :

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker ">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <div class="mgt5">
                            출고구분 :
                            <label class="checkbox-inline" style="width:80px;">
                                <input type="checkbox" name="inOutReason[]" value="all" class="js-not-checkall" data-target-name="inOutReason[]" <?=gd_isset($checked['inOutReason']['all']); ?>> 전체
                            </label>
                            <label style="width:80px;">
                                <input class="checkbox-inline" type="checkbox" name="inOutReason[]" value="2"  <?=gd_isset($checked['inOutReason'][2]); ?>>정기출고
                            </label>
                            <label style="width:80px;">
                                <input class="checkbox-inline" type="checkbox" name="inOutReason[]" value="4"  <?=gd_isset($checked['inOutReason'][4]); ?>>교환출고
                            </label>
                        </div>

                    </div>

                </td>
            </tr>
            <?php } ?>
            <tr>
                <th>구분 항목</th>
                <td >
                    <div class="checkbox mgl10">
                        <label class="checkbox-inline" style="width:80px;">
                            <input type="checkbox" name="attr[]" value="all" class="js-not-checkall" data-target-name="attr[]" <?=gd_isset($checked['attr']['all']); ?>> 전체
                        </label>
                        <?php foreach($summaryField as $k => $v) { ?>
                            <label style="width:80px;">
                                <input class="checkbox-inline" type="checkbox" name="attr[]" value="<?=$k?>"  <?=gd_isset($checked['attr'][$k]); ?>><?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </td>
                <?php if(!empty($isProvider)) { ?>
                <th >출고내역확인</th>
                <td >

                    <div class="checkbox mgl10 mgt10">
                        <label class="radio-inline"><input type="radio" id="isViewModeAll" class="isViewMode"  name="isViewMode" value="all" <?=gd_isset($checked['isViewMode']['all']); ?> />기본</label>
                        <label class="radio-inline"><input type="radio" id="isViewMode1" class="isViewMode" name="isViewMode" value="1" <?=gd_isset($checked['isViewMode']['1']); ?> />출고요약보기</label>
                        <label class="radio-inline"><input type="radio" id="isViewMode2" class="isViewMode" name="isViewMode" value="3" <?=gd_isset($checked['isViewMode']['3']); ?> />연별출고내역보기</label>
                        <label class="radio-inline"><input type="radio" id="isViewMode2" class="isViewMode" name="isViewMode" value="2" <?=gd_isset($checked['isViewMode']['2']); ?> />월별출고내역보기</label>
                    </div>
                    <div style="margin:10px;" class="form-inline" id="outDatePeriod">

                        출고 기간 :

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker ">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <div class="mgt5">
                            출고구분 :
                            <label class="checkbox-inline" style="width:80px;">
                                <input type="checkbox" name="inOutReason[]" value="all" class="js-not-checkall" data-target-name="inOutReason[]" <?=gd_isset($checked['inOutReason']['all']); ?>> 전체
                            </label>
                            <label style="width:80px;">
                                <input class="checkbox-inline" type="checkbox" name="inOutReason[]" value="2"  <?=gd_isset($checked['inOutReason'][2]); ?>>정기출고
                            </label>
                            <label style="width:80px;">
                                <input class="checkbox-inline" type="checkbox" name="inOutReason[]" value="4"  <?=gd_isset($checked['inOutReason'][4]); ?>>교환출고
                            </label>
                        </div>
                    </div>
                </td>
                <?php }  ?>
            </tr>
            <tr>
                <th>속성</th>
                <td colspan="99">
                    <ul class="dp-flex" style="gap:15px">
                        <li>
                            구분 : <input type="text" class="form-control" placeholder="구분" style="width:100px" name="attr1" value="<?=$search['attr1']; ?>">
                            <i class="fa fa-times hover-btn cursor-pointer text-muted" aria-hidden="true" onclick=$('input[name="attr1"]').val('')></i>
                        </li>
                        <li>
                            년도 : <input type="text" class="form-control" placeholder="입고년도" style="width:100px" name="attr5" value="<?=$search['attr5']; ?>">
                            <i class="fa fa-times hover-btn cursor-pointer text-muted" aria-hidden="true" onclick=$('input[name="attr5"]').val('')></i>
                        </li>
                        <li>
                            시즌 : <input type="text" class="form-control" placeholder="시즌" style="width:100px" name="attr2" value="<?=$search['attr2']; ?>">
                            <i class="fa fa-times hover-btn cursor-pointer text-muted" aria-hidden="true" onclick=$('input[name="attr2"]').val('')></i>
                        </li>
                        <li>
                            상품 : <input type="text" class="form-control" placeholder="상품구분" style="width:100px" name="attr3" value="<?=$search['attr3']; ?>">
                            <i class="fa fa-times hover-btn cursor-pointer text-muted" aria-hidden="true" onclick=$('input[name="attr3"]').val('')></i>
                        </li>
                        <li>
                            색상 : <input type="text" class="form-control" placeholder="색상" style="width:100px" name="attr4" value="<?=$search['attr4']; ?>">
                            <i class="fa fa-times hover-btn cursor-pointer text-muted" aria-hidden="true" onclick=$('input[name="attr4"]').val('')></i>
                        </li>
                    </ul>

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
            전체수량 <strong><?=number_format($listAllData['totalData']['stockCnt'])?></strong> 개
        </div>
        <div class="pull-right">
            <div>
                <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            </div>
        </div>
    </div>
    <div class="search-detail-box form-inline">
        <table class="table table-cols table-stock">
            <colgroup>
                <?php foreach($summaryTitles as $val) { ?>
                    <col class="width-md">
                <?php } ?>
                <col class="width-md">
            </colgroup>
            <thead>
            <tr>
            <?php foreach($summaryTitles as $val) { ?>
                <th><?=$val?></th>
            <?php } ?>
                <th>TOTAL</th>
            <?php foreach($optionTitles as $val) { ?>
                <th><?=$val?></th>
            <?php } ?>
            </tr>
            </thead>
            <tbody>
                <?php foreach($data as $index => $val) { ?>
                <tr>
                    <?php foreach($summaryTitles as $listTitleKey => $listTitle) { ?>
                        <td class="text-center"  style="background: #fafafa">
                             <?=$val['info']['attr'.$listTitleKey]?>
                        </td>
                    <?php } ?>
                    <td class="text-center text-danger"  style="background: #fafafa">
                        <?=number_format($listAllData['totalData']['keyStockCnt'][$index])?>
                    </td>
                    <?php foreach($optionList as $optionValue) { ?>
                        <td class="text-center">
                            <?=number_format($val['stockCnt'][$optionValue])?>
                        </td>
                    <?php } ?>
                </tr>
                    <?php if ( empty($checked['isViewMode']['all']) ) { ?>
                        <!-- 입고 내역
                        <tr>
                            <?php foreach($summaryTitles as $listTitleKey => $listTitle) { ?>
                                <td class="text-center text-muted" style="background-color:#fff6e0">
                                    <?=$val['info']['attr'.$listTitleKey]?>
                                </td>
                            <?php } ?>
                            <td class="text-right" style="background-color:#fff6e0">
                                <small class="text-muted">(<?=number_format($listAllData['totalData']['inCnt'][$index]['total'])?>)</small>
                                총입고수량
                            </td>
                            <?php foreach($optionList as $optionValue) { ?>
                                <td class="text-center" style="background-color:#fff6e0">
                                    <?=number_format($listAllData['totalData']['inCnt'][$index][$optionValue])?>
                                </td>
                            <?php } ?>
                        </tr>
                        -->
                        <tr>
                            <?php foreach($summaryTitles as $listTitleKey => $listTitle) { ?>
                                <td class="text-center text-muted" style="background-color:#fff6e0">
                                    <?=$val['info']['attr'.$listTitleKey]?>
                                </td>
                            <?php } ?>
                            <td class="text-right" style="background-color:#fff6e0">
                                출고비율
                            </td>
                            <?php foreach($optionList as $optionValue) { ?>
                                <td class="text-center" style="background-color:#fff6e0">
                                    <?php if(!empty($listAllData['totalData']['outTotalCnt'][$index][$optionValue])) { ?>
                                        <?=round($listAllData['totalData']['outTotalCnt'][$index][$optionValue] / $listAllData['totalData']['outTotalCnt'][$index]['total']*100)?>%
                                    <?php }else{ ?>
                                        0
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <?php foreach($summaryTitles as $listTitleKey => $listTitle) { ?>
                                <td class="text-center text-muted" style="background-color:#fff6e0">
                                    <?=$val['info']['attr'.$listTitleKey]?>
                                </td>
                            <?php } ?>
                            <td class="text-right" style="background-color:#fff6e0">
                                <small class="text-muted" >
                                    <span class="simple-download-detail" data-detail-key="totalData,<?=$index?>,total">
                                        (<?=number_format($listAllData['totalData']['outTotalCnt'][$index]['total'])?>)
                                    </span>
                                </small>
                                총출고수량
                            </td>
                            <?php foreach($optionList as $optionValue) { ?>
                                <td class="text-center" style="background-color:#fff6e0">
                                    <span class="text-center simple-download-detail" data-detail-key="totalData,<?=$index?>,<?=$optionValue?>">
                                        <?=number_format($listAllData['totalData']['outTotalCnt'][$index][$optionValue])?>
                                    </span>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php if ( !empty($checked['isViewMode']['2']) ) { ?>
                            <?php foreach($listAllData['totalData']['outCnt'][$index] as $outKey => $outData) { ?>
                            <tr>
                                <?php foreach($summaryTitles as $listTitleKey => $listTitle) { ?>
                                    <td class="text-center text-muted bg-light-yellow" >
                                        <?=$val['info']['attr'.$listTitleKey]?>
                                    </td>
                                <?php } ?>
                                <td class="bg-light-yellow text-right">
                                    <small class="text-muted">
                                        <span class="simple-download-detail" data-detail-key="<?=$outKey?>,<?=$index?>,total">
                                            (<?=number_format($outData['total'])?>)
                                        </span>
                                    </small>
                                    &nbsp;&nbsp;<?=gd_date_format('y년m월',$outKey)?>
                                </td>
                                <?php foreach($optionList as $optionValue) { ?>
                                    <td class="text-center bg-light-yellow">
                                        <span class="simple-download-detail" data-detail-key="<?=$outKey?>,<?=$index?>,<?=$optionValue?>">
                                            <?=number_format($outData[$optionValue])?>
                                        </span>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        <?php } ?>

                        <?php if ( !empty($checked['isViewMode']['3']) ) { ?>
                            <?php foreach($listAllData['totalData']['outCnt'][$index] as $outKey => $outData) { ?>
                                <tr>
                                    <?php foreach($summaryTitles as $listTitleKey => $listTitle) { ?>
                                        <td class="text-center text-muted bg-light-yellow" >
                                            <?=$val['info']['attr'.$listTitleKey]?>
                                        </td>
                                    <?php } ?>
                                    <td class="bg-light-yellow text-right">
                                        <small class="text-muted">
                                        <span class="simple-download-detail" data-detail-key="<?=$outKey?>,<?=$index?>,total">
                                            (<?=number_format($outData['total'])?>)
                                        </span>
                                        </small>
                                        &nbsp;&nbsp;<?=$outKey?>년
                                    </td>
                                    <?php foreach($optionList as $optionValue) { ?>
                                        <td class="text-center bg-light-yellow">
                                        <span class="simple-download-detail" data-detail-key="<?=$outKey?>,<?=$index?>,<?=$optionValue?>">
                                            <?=number_format($outData[$optionValue])?>
                                        </span>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        <?php } ?>

                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>



<script type="text/javascript">
    let prevDateHtml = '<label class="btn btn-white btn-sm <?='1'===$search['searchPeriod']?'active':''?>" ><input type="radio" name="searchPeriod" value="1" >전일</label>';
    $('[class*=js-dateperiod]').find('label').eq(0).after(prevDateHtml);
</script>

<script type="text/javascript">
    $(function(){

        <?php if(!empty($checked['isViewMode']['all'])) { ?>
        $('#outDatePeriod').hide();
        <?php }else{ ?>
        $('#outDatePeriod').show();
        <?php } ?>

        $('.isViewMode').click(function(){
            if( $('#isViewModeAll').is(':checked') ){
                $('#outDatePeriod').hide();
            }else{
                $('#outDatePeriod').show();
            }
        });
    });
</script>
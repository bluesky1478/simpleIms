<link rel="stylesheet" href="<?=URI_HOME?><?=PATH_SKIN?>wcustomer/css/preloader.css">
<!--스위트 얼럿-->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">

<div id="layerDim" class="display-none">
    <div class="sl-pre-loader">
        <div class="throbber-loader"> </div>
    </div>
</div>

<script type="text/javascript">
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

        $('.excel-submit').click(()=>{
            $('#frmExcel').submit();
        });

        $('.download-all-product').click(()=>{
            location.href = "<?=$requestUrl2?>";
        });

        $('.js-closing').click(()=>{
            $.msgConfirm('마감 처리를 진행하시겠습니까?', '마감완료후에는 수정이 불가 합니다.').then((confirmData)=>{
                if( true === confirmData.isConfirmed){
                    $('#layerDim').removeClass('display-none');
                    $.postAsync('./erp_ps.php',{
                        mode:'setClosing',
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('마감처리 완료.','','info').then(()=>{
                                location.href='closing_list.php';
                                $('#layerDim').addClass('display-none');
                            });
                        }else{
                            $('#layerDim').addClass('display-none');
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
    <h3><?= end($naviMenu->location); ?> 등록</h3>
    <div class="btn-group">
        <input type="button" value="목록" class="btn btn-white btn-icon-list" onclick="goList('./closing_list.php');">
        <input type="button" value="마감 완료" class="btn btn-red js-closing" />
    </div>
</div>

<div class="table-title gd-help-manual">
    마감정보
</div>
<div id="depth-toggle-layer-detailView">
    <table class="table table-cols">
        <colgroup>
            <col class="width-lg">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th>최종 마감일자</th>
            <td>
                <?=$lastClosingDate?>
            </td>
        </tr>
        <tr>
            <th>마감일자</th>
            <td>
                <?=date('Y-m-d')?>
            </td>
        </tr>
        <tr>
            <th>현재재고</th>
            <td>
                <span class="font-15 hover-btn cursor-pointer download-all-product"><b class="text-danger"><?=$totalStockCount?></b>개</span>
                <div><small>클릭시 전체 내역이 다운로드 됩니다. 반드시 확인해주세요.</small></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-title gd-help-manual">
    입/출고리스트 <!--<strong class="text-danger"><?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong>건-->
    <small class="text-muted" style="font-weight: normal">(입고 <?=$inputStockCount?>개 / 출고 <?=$outStockCount?>개 <!--/ Total : <?=$totalInOutCount?>개-->)</small>
    <small class="text-muted">최종마감일(<?=$lastClosingDate?>) ~ 오늘(<?=date('Y-m-d')?>)</small>
</div>
<div id="depth-toggle-line-settleInfo" class="depth-toggle-line display-none"></div>
<div id="depth-toggle-layer-settleInfo">
    <!--입/출고일자 | 구분 | 사유 | 고객사 | 상품코드 | 상품명 | 옵션 | 수량-->
    <table class="table-cols w100 table-default-center" >
        <thead>
        <tr>
            <th>번호</th>
            <?php foreach($title as $key => $titleValue) { ?>
                <th><?=$titleValue?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
            if (gd_isset($listData)) {
                foreach($listData as $key => $val) { ?>
                <tr>
                    <td><?= $page->idx--; ?></td>
                    <td><?=$val['inOutDate']; ?></td>
                    <td class="<?=$val['inOutTypeClass']?>"><?=$val['inOutTypeKr']; ?></td>
                    <td><?=$val['inOutReasonKr']; ?></td>
                    <td><?=$val['thirdPartyProductCode']; ?></td>
                    <td class="text-left"><?=$val['productName']; ?></td>
                    <td><?=$val['optionName']; ?></td>
                    <td><?=number_format($val['quantity']); ?></td>
                    <td><?=$val['scmName']; ?></td>
                </tr>
        <?php
            }
        } else {
            echo '<tr><td class="center" colspan="99">마감할 데이터가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <div class="table-action clearfix mgt10">
        <div class="pull-left"></div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

</div>

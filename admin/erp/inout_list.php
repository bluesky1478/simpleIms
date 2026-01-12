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

        $('.excel-submit1').click(()=>{
            $('#frmExcel1').submit();
        });
        $('.excel-submit2').click(()=>{
            $('#frmExcel2').submit();
        });
        $('.excel-submit4').click(()=>{
            $('#frmExcel4').submit();
        });
        $('.excel-submit5').click(()=>{
            $('#frmExcel5').submit();
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
        재고 이력 검색
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
                        <input type="radio" name="scmFl" value="y" <?=gd_isset($checked['scmFl']['y']); ?> onclick="layer_register('scm', 'checkbox')"/>고객사
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
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control"/>
                </td>
                <th>기간검색</th>
                <td >
                    <div class="form-inline">

                        <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'form-control '); ?>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 364), 'treatDate[]', false) ?>

                    </div>
                </td>
            </tr>
            <tr>
                <th>유형</th>
                <td >
                    <label class="radio-inline">
                        <input type="radio" name="inOutType" value=""   <?=gd_isset($checked['inOutType']['']); ?> />전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="inOutType" value="1" <?=gd_isset($checked['inOutType']['1']); ?> />입고
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="inOutType" value="2" <?=gd_isset($checked['inOutType']['2']); ?> />출고
                    </label>
                </td>
                <th>
                    회원구분
                </th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="memberType" value=""   <?=gd_isset($checked['memberType']['']); ?> />전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="memberType" value="1" <?=gd_isset($checked['memberType']['1']); ?> />정규직
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="memberType" value="2" <?=gd_isset($checked['memberType']['2']); ?> />파트너
                    </label>
                </td>
            </tr>
            <tr>
                <th>사유</th>
                <td colspan="99">
                    <div class="checkbox">
                        <label class="checkbox-inline" style="width:80px;">
                            <input type="checkbox" name="inOutReason[]" value="all" class="js-not-checkall" data-target-name="inOutReason[]" <?=gd_isset($checked['inOutReason']['all']); ?>> 전체
                        </label>
                        <?php foreach($inoutReason as $k => $v) { ?>
                            <label style="width:80px;">
                                <input class="checkbox-inline" type="checkbox" name="inOutReason[]" value="<?=$k?>"  <?=gd_isset($checked['inOutReason'][$k]); ?>><?=$v?>
                            </label>
                        <?php } ?>
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

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
            <span class="font-15">
            검색
            <strong>
                <?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong> 건
                <small>(입고수량:<strong><?=number_format($listAllData['totalInoutCount']['inTotal'])?></strong>개, 출고수량:<strong><?=number_format($listAllData['totalInoutCount']['outTotal'])?></strong>개)</small>
            </span>
            / 마지막 입고일 : <strong class="text-blue"><?=$latestData['inDate']?></strong>
            / 최근 입고 등록일 : <strong class="text-blue"><?=gd_date_format('Y-m-d',$latestData['inRegDate'])?></strong>
            / 마지막 출고일 : <strong class="text-blue"><?=$latestData['outDate']?></strong>
            / 최근 출고 등록일 : <strong class="text-blue"><?=gd_date_format('Y-m-d',$latestData['outRegDate'])?></strong>
        </div>
        <div class="pull-right">
            <div>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 100)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows table-th-height30 table-td-height30">
        <colgroup>
            <?php foreach ($data as $val => $key) { ?>
            <col/>
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
        <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
            <th><?=$titleValue?></th>
        <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="font-num" style="width:60px">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--입출고일자-->
                    <td class="center text-nowrap"><?=$val['inOutDate']; ?></td>
                    <!--구분-->
                    <td class="center text-nowrap <?=$val['quantityClass']?>"><?=$val['inOutTypeKr']; ?></td>
                    <!--사유-->
                    <td class="center text-nowrap"><?=$val['inOutReasonKr']; ?></td>
                    <!--고객사-->
                    <td class="center text-nowrap"><?=$val['scmName']; ?></td>
                    <!--상품코드-->
                    <td class="center text-nowrap"><?=$val['thirdPartyProductCode']; ?></td>
                    <!--상품명-->
                    <td class="center text-nowrap"><?=$val['productName']; ?></td>
                    <!--옵션명-->
                    <td class="center text-nowrap"><?=$val['optionName']; ?></td>
                    <!--수량-->
                    <td class="center text-nowrap <?=$val['quantityClass']?>">
                        <b><?=number_format($val['quantity']); ?></b>
                    </td>
                    <!--메모 ( 출고시 Order정보 Memo ) -->
                    <td class="ta-l text-nowrap" style="width:150px"><?=$val['memo']; ?></td>
                    <!--주문번호-->
                    <td class="center text-nowrap">
                        <?=$val['orderNo']; ?>
                        <br><span class="text-muted"><?=$val['invoiceNo']; ?></span>
                    </td>
                    <!--일련번호-->
                    <td class="center text-nowrap">
                        <?= $val['identificationText']?>
                    </td>
                    <!--등록일-->
                    <td class="cen">
                        <?= str_replace(' ', '<br>', gd_date_format('Y-m-d', $val['regDt'])); ?>
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
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

    <section class="excel-upload-section ">
        <div class="table-title">
            초기 제품 등록
        </div>
        <div class="">
            <form id="frmExcel1" name="frmExcel1" action="./erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                <table class="table table-cols">
                    <colgroup>
                        <col class="width20p"/>
                        <col class="width-xl"/>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>초기 제품 등록</th>
                        <td>
                            <div class="form-inline">
                                <input type="hidden" name="runMethod" value="iframe"/>
                                <input type="hidden" name="mode" value="setProduct"/>
                                <input type="file" name="excel" value="" class="form-control width50p" />
                                <input type="button"  class="btn btn-white btn-sm excel-submit1" value="엑셀업로드 하기">
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </section>
    <section class="excel-upload-section ">
        <div class="table-title">
            초기 출고 등록
        </div>
        <div class="">
            <form id="frmExcel4" name="frmExcel4" action="./erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                <table class="table table-cols">
                    <colgroup>
                        <col class="width20p"/>
                        <col class="width-xl"/>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>초기 출고 등록</th>
                        <td>
                            <div class="form-inline">
                                <input type="hidden" name="runMethod" value="iframe"/>
                                <input type="hidden" name="mode" value="saveOutputHistory"/>
                                <input type="file" name="excel" value="" class="form-control width50p" />
                                <input type="button"  class="btn btn-white btn-sm excel-submit4" value="엑셀업로드 하기">
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </section>

<section class="excel-upload-section ">
    <div class="table-title">
        입고 등록
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
                <th>입고등록</th>
                <td>
                    <form id="frmExcel2" name="frmExcel2" action="./erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="saveInputHistory"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm excel-submit2" value="엑셀업로드 하기">
                        </div>
                    </form>
                    <input type="button" value="입고등록양식 샘플 다운로드" class="btn btn-white btn-icon-excel mgt10" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/input_template.xls')?>&fileName=<?=urlencode('입고등록양식_템플릿.xls')?>'">
                </td>
                <th>3PL링크</th>
                <td>
                    <a href="http://wms.korea-soft.com/syl/" class="hover-btn" target="_blank"><img src="/admin/image/samyoung-logo.png" style="width:150px"></a>
                </td>
            </tr>
        </table>
    </div>
</section>


<script type="text/javascript">
    let prevDateHtml = '<label class="btn btn-white btn-sm <?='1'===$search['searchPeriod']?'active':''?>" ><input type="radio" name="searchPeriod" value="1" >전일</label>';
    $('[class*=js-dateperiod]').find('label').eq(0).after(prevDateHtml);
</script>

<?php include 'sales_common.php'?>

<style>
    /*주문리스트 관리자 추가 상품 정보 */
    .layer-order-add-info {padding:15px}
    .layer-order-add-info .order-add-info-title {font-size:14px;text-align: left;font-weight: bold;}
    .layer-order-add-info th{ background-color:#F6F6F6!important; text-align: center; color:#5c5c5c }
    .layer-order-add-info td{ text-align: left }

    .sales-table td,th{
        font-size:11px;
    }

</style>

<script>
    // 정렬&출력수
    $(function(){
        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });
    });
</script>

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="등록" class="btn btn-red-line btn-recap" data-sno="" />
    </div>
</div>

<?php if ($isDev) { ?>
<section class="excel-upload-section display-none" >
    <div class="search-detail-box form-inline" style="border-top:none !important;">
        <table class="table table-cols" style="border-top:none!important;">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tr>
                <th>일괄등록/수정</th>
                <td colspan="3">
                    <form id="frmExcel" name="frmExcel" action="./sales_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="saveRecapProduceData"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                        </div>
                    </form>
                    <div class="notice-info">엑셀 다운로드 후 수정하여 업데이트 합니다. 관리번호(업데이트용)이 빈칸일 경우 신규 입력 됩니다.</div>
                </td>
            </tr>
        </table>
    </div>
</section>
<?php } ?>
<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 30); ?>"/>
    <input type="hidden" name="step" value="<?= Request::get()->get('step'); ?>"/>
    <div class="table-title">
        검색
    </div>
    <div class="search-detail-box form-inline" style="margin-bottom:0 !important; border-bottom: none !important;">
        <table class="table table-cols" style="margin-bottom:0 !important; border-bottom: none !important;">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tbody>
            <tr>
                <th>검색어</th>
                <td>
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control"/>
                    <input type="submit" value="검색" class="btn btn-lg btn-black" style="margin-left:10px">
                </td>
                <th>
                    <?= gd_select_box('searchDateFl', 'searchDateFl', $search['combineTreatDate'], null, $search['searchDateFl'], null, null, 'form-control '); ?>
                </th>
                <td >
                    <div class="form-inline">
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod']), 'searchDate[]', true) ?>
                        <input type="submit" value="검색" class="btn btn-lg btn-black" style="margin-left:10px">
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left font-16">
            검색
            <strong><?= empty($page->recode['total'])? 0 : $page->recode['total']; ?></strong>
            건
        </div>
        <div class="pull-left">
            <?php if( empty($isSalesCompany) ) { ?>
            <select class="form-control" id="select-status" style="margin-left:20px">
                <option value="">== 변경상태선택 ==</option>
                <option value="10">잠재고객</option>
                <option value="20">관심고객</option>
                <option value="30">가망고객</option>
                <option value="40">기타고객</option>
                <option value="80">미팅고객(진행)</option>
                <option value="90">미팅고객(계약)</option>
                <option value="99">미팅고객(이탈)</option>
            </select>
            <div class="btn btn-gray btn-status btn-batch-update-status">상태변경</div>
            <?php } ?>
        </div>
        <div class="pull-right">
            <div>
                <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows sales-table">
        <colgroup>
            <col style="width:1%" />
            <col style="width:4%" />
            <col style="width:2%" />
            <col style="width:4%" />
            <col style="width:10%" />
            <col style="width:4%" />
            <col style="width:3%" />
            <col style="width:6%" />
            <col style="width:4%" />
            <col style="width:6%" />
            <col style="width:6%" />
            <col style="width:4%" />
            <col style="width:15%" />
            <col style="width:3%" />
            <col style="width:3%" />
            <col style="width:4%" />
            <col style="width:4%" />
            <col style="width:4%" />
            <col style="width:4%" />
            <col style="width:4%" />
        </colgroup>
        <thead>
        <tr>
            <th>
                <input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/>
            </th>
            <?php foreach ($listTitles as $val) { ?>
                <th><?=$val?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                ?>

        <?php  $NAME_EN = 1; $NAME_KR = 0; $HIDDEN = 2; $LEFT = 3; ?>
        <tbody class="one-data-row">
        <tr class="center tr-data" data-sno="<?= $val['sno'] ?>" data-style-code="<?= $val['styleCode'] ?>" data-customer="<?= $val['customer'] ?>"  >

            <td>
                <input type="checkbox" name="sno[<?=$val['sno']; ?>]" value="<?=$val['sno']; ?>" />
            </td>

            <?php foreach( $listDataDetail as $listInfo ) {
                $nameEn = $listInfo['top'][$NAME_EN];
                $nameKr = $listInfo['top'][$NAME_KR];
                $isBold = empty($listInfo['top'][4])?"":"font-bold";
                $isLeft = empty($listInfo['top'][$LEFT])?"":"text-left mgl10";
                $isHidden = empty($listInfo['top'][$HIDDEN])?"":"td-hidden-class";
                $isSpan = empty($listInfo['bottom'][0])?'rowspan=2':'';
                ?>

                <?php if( 'contactContents' == $nameEn ) { ?>
                    <td class="number-focus <?=$isHidden?> <?=$isLeft?> <?=$isBold?> " <?=$isSpan?>>


                        <div class="btn btn-gray btn-sm btn-call-with" data-sno="<?=$val['sno']?>">내용(<?=number_format($val['contactCnt'])?>)</div>
                    </td>
                <?php }else if( empty($val[$nameEn]) || '-' == $val[$nameEn] ) { ?>
                    <td class="text-center empty-data <?=$isHidden?>" <?=$isSpan?>  >
                        <span class="text-muted muted-title" style="font-size:10px !important; color:#e0e0e0"><?=$nameKr?></span>
                        <div id="<?= $nameEn ?><?= $val['sno'] ?>" class="btn-modify"  data-type="goods_option" data-sno="<?= $val['sno'] ?>"  data-key="<?= $nameEn ?>"  data-title="<?=$nameKr?>" style="display: none">수정</div>
                    </td>
                <?php }else{ ?>
                    <td class="number-focus <?=$isHidden?> <?=$isLeft?> <?=$isBold?> " <?=$isSpan?>>
                        <?php if( 'contactContents' == $nameEn ) { ?>
                            <div class="btn btn-gray" click="openCallView(<?=$val['sno']?>)">통화내용</div>
                        <?php } else if( 'customerName' == $nameEn ) { ?>
                            <!--고객명 전체 수정-->
                            <span class="btn-recap text-blue cursor-pointer hover-btn"  data-sno="<?=$val['sno']?>" ><?=$val[$nameEn]?></span>
                            </span>
                            <div>
                                <!--<span class="btn-cpdel cursor-pointer hover-btn btn-copy text-danger" style="display: none">복사</span>-->
                                <span class="btn-cpdel cursor-pointer hover-btn btn-delete text-danger" data-sno="<?=$val['sno']?>">삭제</span>
                            </div>
                        <?php }else if( strpos($nameEn,'Dt')!==false  ) { ?>
                            <!--날짜형-->
                            <span id="<?= $nameEn ?><?= $val['sno'] ?>" class="text-modify hover-btn" data-key="<?= $nameEn ?>"  data-title="<?=$nameKr?>" data-values="<?= $val[$nameEn] ?>">
                                <?php if( '-' == gd_date_format('y/m/d',$val[$nameEn]) ) { ?>
                                    <?= $val[$nameEn] ?>
                                <?php }else{ ?>
                                    <?= gd_date_format('y/m/d',$val[$nameEn]) ?>
                                <?php } ?>
                            </span>
                        <?php }else if( is_numeric($val[$nameEn]) ) { ?>
                            <!--숫자형-->
                            <span id="<?= $nameEn ?><?= $val['sno'] ?>" class="text-modify hover-btn" data-key="<?= $nameEn ?>"  data-title="<?=$nameKr?>" data-values="<?= $val[$nameEn] ?>"><?= number_format($val[$nameEn]) ?></span>
                        <?php }else{ ?>
                            <span id="<?= $nameEn ?><?= $val['sno'] ?>" class="text-modify hover-btn" data-key="<?= $nameEn ?>"  data-title="<?=$nameKr?>" data-values="<?= $val[$nameEn] ?>"><?= $val[$nameEn] ?></span>
                        <?php } ?>
                    </td>
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

    <div class="table-action clearfix">
        <div class="pull-left"></div>
        <div class="pull-right"></div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <div style="margin-top:250px"></div>

</form>

<script type="text/javascript">

    var openRecapView = function(url){
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
    }

    $(function(){
        $('.excel-submit').click(()=>{
            $('#frmExcel').submit();
        });

        $('.btn-recap').click(function(){
            let sno = $(this).data('sno');
            let url = `/sales/sales_view.php?salesSno=${sno}`;
            openRecapView(url);
        });

        //simple excel download
        $('.simple-download').click(function(){
            location.href = "<?=$requestUrl?>";
        });

        $('.btn-batch-update-status').click(()=>{

            let selectedProjectCnt = $('input[name*="sno"]:checked').length;

            if(0 >= selectedProjectCnt){
                alert('선택된 데이터가 없습니다.');
                return false;
            }

            let projectSnoList = [];
            $('input[name*="sno"]:checked').each(function(){
                projectSnoList.push( $(this).val() );
            });

            let selectedStatus = $('#select-status option:selected').val();

            if( '' === selectedStatus ){
                alert('변경하실 상태를 선택해주세요.');
                return false;
            }
            let param = {
                mode : 'setBatchStatus',
                snoList : projectSnoList.join(','),
                changeStatus : selectedStatus,
            };
            $.post('./sales_ps.php', param, function (data) {
                location.reload();
            });
        });

        $('.btn-delete').click(function(){
            let sno = $(this).data('sno');
            if ( '1234' === prompt("삭제하시려면 암호를 입력하세요.(복구불가)") ) {
                $.post('sales_ps.php', {
                    mode : 'deleteItem',
                    sno : sno
                }, function (data) {
                    location.reload();
                });
            }
        });

    });
</script>

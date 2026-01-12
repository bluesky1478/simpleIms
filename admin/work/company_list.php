<script src="<?=URI_HOME?><?=PATH_SKIN?>work/assets/js/sl_js_module.js"></script>
<script src="<?=URI_HOME?><?=PATH_SKIN?>work/assets/js/work_custom.js"></script>

<!--스위트 얼럿-->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">

<style>
    .swal2-input-label { font-weight:bold; font-size:18px;  }
    .swal2-popup { width:700px }
    .swal2-content { font-size:13px; }
    .swal2-confirm { font-size:13px; }
    .bootstrap-filestyle { display:none }
</style>

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
    });
</script>

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <a href="<?=$workFrontURL?>/work_admin/company_list.php" target="_blank"><input type="button" value="거래처 등록" class="btn btn-red-line"/></a>
    </div>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title">
        거래처 검색
    </div>
    <?php include('project_list_search.php'); ?>
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
            <?php foreach ($listTitles as $titleKey => $val) { ?>
                <?php if( '등록일' == $val || '번호'  == $val  ) { ?>
                    <col style="width:80px"  />
                <?php }else{ ?>
                    <col/>
                <?php } ?>
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
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
                <tr >
                    <td class="center font-num">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <td class="center text-nowrap"><!--구분-->
                        <?=\Component\Work\WorkCodeMap::COMP_TYPE[$val['companyType']]?>
                    </td>
                    <td class="center text-nowrap"><!--업체명-->
                        <div>
                            <a href="<?=$workFrontURL?>/work_admin/company_list.php?sno=<?=$val['sno']; ?>&companyPage=1" target="_blank"><?=$val['companyName']; ?></a>
                        </div>
                        <div class="text-muted">
                            <?=$val['busiNo']; ?>
                        </div>
                        <div class="text-muted">
                            <?php if( $val['latestDocData']['version'] > 0 ) { ?>
                                (진행 <?=$val['latestDocData']['version'] ?> 차 미팅보고서 기준)
                            <?php } ?>
                        </div>
                    </td>
                    <!--
                    <td class="text-center text-nowrap" style="padding:0px">
                        <?php if( $val['latestDocData']['version'] > 0 ) { ?>
                        <table class="table doc-step-table" >
                            <tr>
                                <td class="text-center" style="width:80px !important;">
                                    <div class="btn btn-red btn-sm btn-mod-plan"  data-sno="<?=$val['latestDocData']['sno']?>">수정</div>
                                    <div class="btn btn-white btn-sm btn-plan-history"  onclick="window.open('popup_plan.php?docSno=<?=$val['latestDocData']['sno']?>', 'plan_history_popup', 'width=1400, height=910, resizable=yes, scrollbars=yes');">이력</div>
                                </td>
                                <?php foreach( $COMPANY_STEP as $stepKey => $step ) { ?>
                                <td class="text-center <?php if( $stepKey == $val['latestDocData']['docData']['currentStep'] ) { ?>step-active<?php } ?> <?php if( $stepKey == 3 ) { ?>step-large<?php } ?>">
                                    <?php if( $stepKey == $val['latestDocData']['docData']['currentStep'] ) { ?>● <?php } ?>
                                    <a class="btn-set-step" data-step="<?=$stepKey?>" data-step-name="<?=$step?>"  data-sno="<?=$val['latestDocData']['sno']?>"  style="cursor:pointer"><?=$step?></a>
                                </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td class="text-right">진행계획 ▶</td>
                                <?php foreach( $COMPANY_STEP as $stepKey => $step ) { ?>
                                    <td class="text-center">
                                        <div class="input-group js-datepicker text-center" >
                                            <input type="text" class="form-control text-center plan-date" value="<?=$val['latestDocData']['docData']['stepData'][$stepKey]?>"  style="padding:0px" >
                                            <span class="input-group-addon" style="padding:0px"><span class="btn-icon-calendar" style="display: none"></span></span>
                                        </div>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td class="text-right">고객확정 ▶</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center  step-active">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                            </tr>
                        </table>
                        <?php }else{ ?>
                            <div class="text-muted">
                                미팅 보고서 없음 - <a href="<?=URI_HOME?>/work/sales_meeting_reg.php?companySno=<?=$val['sno']?>" target="_blank" class="btn btn-white btn-sm reg-meeting-rpt">미팅 보고서 작성</a>
                            </div>
                        <?php } ?>
                    </td>
                    -->
                    <td class="center text-nowrap"><!--대표전화-->
                        <?=$val['phone']; ?>
                    </td>
                    <td class="center text-nowrap"><!--영업담당자-->
                        <?=$val['salesManagerName']; ?>
                    </td>
                    <td class="center text-nowrap"><!--등록일-->
                        <div><?=gd_date_format('Y-m-d', $val['regDt']); ?></div>
                        <div class="text-muted"><small><?=gd_date_format('H:i:s', $val['regDt']); ?></small></div>
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

</form>

<script type="text/javascript">
    $(function(){
        //프로젝트 등록
        var openProjectDataForm = function(sno){
            var childNm = 'project_register';
            var addParam = {
                mode: 'simple',
                layerTitle: '프로젝트 등록',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                sno : sno
            };
            layer_add_info(childNm, addParam);
        };
        //프로젝트 등록
        $('.js-register').click(function(){
            openProjectDataForm('');
        });
        $('.btn-open-comment').click(function(){
            $(this).closest('td').find('.comment-table').removeClass('display-none');
        });
        $('.btn-set-step').click(function(){
            let stepName = $(this).data('step-name');
            if ( confirm( '진행 상태를 ' + stepName +  ' 상태로 변경하시겠습니까?' ) ) {
                let param = {
                    mode :  'updateStep',
                    sno : $(this).data('sno'),
                    step : $(this).data('step'),
                }
                $.post('work_ps.php', param, function (data) {
                    location.reload();
                });
            }
        });

        /**
         * 계획 수정
         */
        $('.btn-mod-plan').click(function(){
            let docSno = $(this).data('sno');
            let planDt = [];
            $(this).closest('table').find('.plan-date').each(function(){
                planDt.push($(this).val());
            })
            let inputOptions = new Promise((resolve) => {
                resolve({
                    <?php foreach( \Component\Work\WorkCodeMap::PLAN_MOD_REASON_TYPE as $reasonKey => $reasonValue) { ?>
                        <?=$reasonKey . ' : \'' . $reasonValue .'\',' ?>
                    <?php } ?>
                })
            });
            let promise = $.msgTextareaAndRadio('수정 사유 지정 / 입력' , '수정 사유를 입력하세요.'  , inputOptions);
            promise.then(function(result){
                if( result.isConfirmed ){
                    console.log(result);
                    console.log(planDt);
                    let param = {
                        mode : 'updateStepPlan',
                        sno : docSno,
                        planDt : planDt,
                        reasonType : result.value[1],
                        reasonText : result.value[0],
                    }
                    console.log(param);
                    $.post('work_ps.php', param, function(result){
                        console.log(result);
                        if( 200 == result.code ){
                            $.msg(result.message,'', 'success');
                            //location.reload();
                        }
                    });
                }
            });
        });
        
        $('.btn-plan-history').click(function(){

        });


    });
</script>

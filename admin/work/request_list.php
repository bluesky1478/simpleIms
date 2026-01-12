
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

    </div>
</div>

<div class="col-md-6">
    <form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
        <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
        <input type="hidden" name="searchFl" value="y"/>
        <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
        <div class="table-title">
            요청 검색
        </div>
        <?php include('request_list_search.php'); ?>
    </form>
</div>
<div class="col-md-6">
    <form id="frmReg" method="get" class="content-form" target="ifrmProcess">
        <input type="hidden" name="mode" value="saveWorkRequest">
        <div class="table-title" @click="openTodoRequestWrite()">
            요청 등록
        </div>
        <?php include('request_list_reg.php'); ?>
    </form>
</div>


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
            <col class="width-xs"/>
            <?php foreach ($data as $val => $key) { ?>
            <col/>
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
                    <td class="font-num text-center">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <td class="font-num text-center">
                        <a href="project_view.php?sno=<?=$val['projectSno']; ?>" target="_blank">
                            <div class="text-danger font-15"><?=$val['projectSno']; ?></div>
                        </a>
                    </td>
                    <td class="center text-nowrap"><!--관련문서 -->
                        <div>
                            <?= $val['companyName'] ?>
                        </div>
                        <div>
                            <?php if ( 'SALES' == $val['docDept'] ) { ?>
                            <a href="<?=$workFrontURL?>/work_admin/document.php?sno=<?=$val['documentSno']; ?>" target="_blank">
                            <?php }else{ ?>
                            <a href="document_reg.php?sno=<?=$val['sno']; ?>" target="_blank">
                            <?php } ?>
                            <?=$PRJ_DOCUMENT[$val['docDept']]['typeDoc'][$val['docType']]['name'];?> <?=empty($val['version'])?'':$val['version'].'차' ?>
                            </a>
                        </div>
                    </td>
                    <td class="center text-nowrap"><!--작성자-->
                        <div>(<?=$val['writerDeptName']; ?>)<?=$val['writeManagerName']; ?></div>
                        <samll class="text-muted"><?=$val['regDt']; ?></samll>
                    </td>
                    <td class="center text-nowrap"><!--대상부서-->
                        <?=$val['targetDeptName']; ?>
                    </td>
                    <td class="text-nowrap"><!--요청내용-->
                        <?=nl2br($val['reqContents'])?>
                    </td>
                    <td class="center text-nowrap"><!--완료 요청일-->
                        <?=$val['completeRequestDt']; ?> 까지
                    </td>
                    <td class="center text-nowrap"><!--답변내용-->
                        <textarea class="form-control realtime-update" data-update-field="resContents" data-sno="<?=$val['sno']?>" ><?=$val['resContents']; ?></textarea>
                    </td>
                    <td class="text-nowrap center"><!--처리여부-->
                        <input type="radio" name="r<?=$val['sno']?>" value="n" <?=( 'n' == $val['isProcFl']) ? "checked": ""?>  data-sno="<?=$val['sno']?>" data-update-field="isProcFl" class="realtime-update isProcRadio" >
                        <span class="proc-uncomplete-text" style="font-weight: <?php ( 'n' == $val['isProcFl'])?'bold':'normal'?>"> 미처리</span>
                        <input type="radio" name="r<?=$val['sno']?>" value="y" <?=( 'y' ==  $val['isProcFl']) ? "checked": ""?> data-sno="<?=$val['sno']?>" data-update-field="isProcFl" class="realtime-update isProcRadio" >
                        <span class="proc-complete-text" style="font-weight: <?php ( 'y' == $val['isProcFl'])?'bold':'normal'?>">처리완료</span>
                    </td>
                    <td class="center text-nowrap"><!--처리자-->
                        <select class="form-control w100 realtime-update select-proc-manager" data-update-field="procManagerSno" data-sno="<?=$val['sno']?>" >
                            <option value="">선택</option>
                            <?php foreach(\SiteLabUtil\SlCommonUtil::getManagerList($val['targetDeptNo']) as  $managerKey => $managerValue ) {?>
                                <option value="<?=$managerKey?>"  <?=$val['procManagerSno']==$managerKey ? 'selected' : '' ?> ><?=$managerValue?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="center text-nowrap"><!--처리 일자-->
                        <span class="proc-dt<?=$val['sno']; ?>"><?=$val['procDt']; ?></span>
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

        //필드별 업데이트
        var realtimeUpdate = function(){
            if( true === $(this).hasClass("num-only-format") ){
                $(this).val(comma($(this).val().replace(/[^0-9]/gi,"")));
            }
            var params = {
                mode : 'updateResponseData',
                sno : $(this).data('sno'),
                key : $(this).data('update-field'),
                value : encodeURIComponent($(this).val()),
            };
            $.post('./project_ps.php', params, function (result) {
            }).done(function(result){
                //console.log(result);
                if( 'undefined' != typeof result.data.procDt && null != result.data.procDt  ){
                    $('.proc-dt' + result.data.sno  ).html(result.data.procDt);
                }
            });
        };

        $(".realtime-update").on("change",realtimeUpdate);
        $(".realtime-update").on("focusout",realtimeUpdate);
        $(".realtime-update").on("keyup",realtimeUpdate);

        $('.isProcRadio').change(function(){
            if( 'y' == $(this).val() ){
                $(this).closest('tr').find('.select-proc-manager').val('<?=$mySno?>');
                $(this).closest('tr').find('.proc-uncomplete-text').css('font-weight','normal');
                $(this).closest('tr').find('.proc-complete-text').css('font-weight','bold');
                $(this).closest('tr').find('.select-proc-manager').change();
            }else{
                $(this).closest('tr').find('.proc-uncomplete-text').css('font-weight','bold');
                $(this).closest('tr').find('.proc-complete-text').css('font-weight','normal');
            }
        });

        //요청사항 등록
        $('#reqReg').click(function(){
            var params = $('#frmReg').serialize();
            $.post('./work_ps.php', params, function (result) {
            }).done(function(result){
                location.reload();
            });
        });

    });
</script>

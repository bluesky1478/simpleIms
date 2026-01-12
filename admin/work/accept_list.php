
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
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title">
        문서 검색
    </div>
    <?php include('accept_list_search.php'); ?>
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
            <col class="w70p"/>
            <col class="w150p"/> <!--프로젝트번호-->
            <col class="w200p"/> <!--고객사-->
            <col  /> <!--문서명-->
            <col class="w80p" />  <!--버전-->
            <col class="w400p" /> <!--승인정보-->
            <col class="w80p" /> <!--승인상태-->
            <col class="w80p" /> <!--작성자-->
            <col class="w80p" /> <!--영업-->
            <col class="w80p" /> <!--디자인-->
            <col class="w80p" /> <!--등록일-->
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
                    <td class="font-num text-center" style="height:69.5px;">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <td class="center text-nowrap"><!--프로젝트번호-->
                        <a href="project_view.php?sno=<?=$val['projectSno']; ?>" target="_blank">
                        <div class=""><?=$val['projectName']; ?></div>
                        <div class="text-danger font-15"><?=$val['projectSno']; ?></div>
                        </a>
                    </td>
                    <td class="center text-nowrap"><!--고객사-->
                        <?=$val['companyName']; ?>
                    </td>
                    <td class="center text-nowrap"><!--문서명-->
                        <?php if ( 'SALES' == $val['docDept'] ) { ?>
                        <a href="<?=$workFrontURL?>/work_admin/document.php?sno=<?=$val['sno']; ?>" target="_blank">
                        <?php }else{ ?>
                        <a href="document_reg.php?sno=<?=$val['sno']; ?>" target="_blank">
                        <?php } ?>
                        <?=$PRJ_DOCUMENT[$val['docDept']]['typeDoc'][$val['docType']]['name'];?>
                        </a>
                    </td>
                    <td class="center text-nowrap"><!--문서 버전-->
                        <?=$val['version']?>차
                    </td>
                    <td class="center text-nowrap" style="padding: 0px"><!--승인상태-->
                        <table style="width:100%;margin:0px;border-bottom:none" class="table table-rows">
                            <tr>
                                <?php foreach(json_decode($val['applyManagers'],true) as $applyManager ) { ?>
                                    <th class="text-center" style="background-color:#eee!important; color:#000; font-weight:bold">
                                        <?=$applyManager['title']?><span class="">( <?=$applyManager['managerNm']?> )</span>
                                    </th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php foreach(json_decode($val['applyManagers'],true) as $applyManager ) { ?>
                                <td class="status_<?=$applyManager['status']?>" style="border-bottom:none">
                                    <?=$applyManager['statusKr']?>
                                </td>
                                <?php } ?>
                            </tr>
                        </table>
                    </td>
                    <td class="center text-nowrap"><!--승인상태-->
                        <?php if('y'===$val['isApplyFl']) echo '<span class="sl-blue">승인완료</span>' ?>
                        <?php if('r'===$val['isApplyFl']) echo '<span class="text-danger">반려</span>' ?>
                        <?php if('y'!==$val['isApplyFl'] && 'r'!==$val['isApplyFl']) echo '<span class="text-danger">미승인</span>' ?>
                    </td>
                    <td class="center text-nowrap"><!--담당자-->
                        <?=$val['regManagerName']; ?>
                    </td>
                    <td class="center text-nowrap"><!--담당자-->
                        <?=$val['salesManagerName']; ?>
                    </td>
                    <td class="center text-nowrap"><!--담당자-->
                        <?=$val['designManagerName']; ?>
                    </td>
                    <td class="center text-nowrap"><!--등록일-->
                        <?=gd_date_format('Y-m-d',$val['regDt']); ?>
                        <br><small class="text-muted"><?=gd_date_format('H:i:s',$val['regDt']); ?></small>
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
        <div class="pull-right"></div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

</form>

<script type="text/javascript">
    var isFirst = true;
    var salesLink = JSON.parse('<?=$salesLink?>');
    $(function(){

        //문서 등록
        $('.js-register').click(function(){
            var docDept = $('#regDocDept').val();
            var isPass = 'y';
            if( $.isEmpty(docDept) ){
                alert('문서 대상 부서를 선택해주세요.');
                isPass = 'n';
            }
            var docType = $('#regDocType').val();
            if( $.isEmpty(docType) ){
                alert('등록할 문서를 선택해주세요.');
                isPass = 'n';
            }

            if( isPass == 'y' ){
                if(  'SALES' == docDept  ){
                    window.open( '<?=$workFrontURL?>/work/' + salesLink[docType] + '.php'  );
                }else{
                    location.href = 'document_reg.php?docDept=' + docDept + '&docType=' + docType;
                }
            }

        });

        $('.btn-open-comment').click(function(){
            $(this).closest('td').find('.comment-table').removeClass('display-none');
        });

        $('#docDept').change(function(){
            $('#docType').html('<option value="">전체</option>');
            var params = {
                mode : 'getDocType',
                docDept : $(this).val()
            }
            $.post('project_ps.php', params, function(result){
                if( !$.isEmpty(result.data) && false != result.data ){
                    for(var key in result.data){
                        var html = '';
                        if( isFirst == true  && !$.isEmpty('<?=$search['docType']?>') && key == '<?=$search['docType']?>'  ){
                            html = "<option value='"+key+"' selected>" + result.data[key] + "</option>";
                        }else{
                            html = "<option value='"+key+"'>" + result.data[key] + "</option>";
                        }
                        $('#docType').append(html);
                        $('#docType').val('<?=$search['docType']?>');
                    }
                }
                isFirst = false;
            });
        });

        $('#regDocDept').change(function(){
            var html = '';
            $('#regDocType').html('');
            var params = {
                mode : 'getDocType',
                docDept : $(this).val()
            }
            $.post('project_ps.php', params, function(result){
                if( !$.isEmpty(result.data) && false != result.data ){
                    for(var key in result.data){
                        if( isFirst == true  && !$.isEmpty('<?=$search['docType']?>') && key == '<?=$search['docType']?>'  ){
                            html = "<option value='"+key+"' selected>" + result.data[key] + "</option>";
                        }else{
                            html = "<option value='"+key+"'>" + result.data[key] + "</option>";
                        }
                        $('#regDocType').append(html);
                        $('#regDocType').val('<?=$search['docType']?>');
                    }
                }
                isFirst = false;
            });
        });

        //초기화
        $('#docDept').change();
        $('#regDocDept').change();
    });
</script>

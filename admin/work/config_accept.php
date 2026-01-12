
<?php include 'import_lib.php'?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <!--
        <input type="button" value="승인권자 등록" class="btn btn-red-line js-register "/>
        -->
    </div>
</div>

<div class="col-md-12">
    <form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
        <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
        <input type="hidden" name="searchFl" value="y"/>
        <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
        <div class="table-title"></div>
    </form>
</div>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <table class="table table-cols accept-config-list">
        <colgroup>
            <col class="width-md"/>
            <col class="width-md"/>
            <col />
        </colgroup>
        <thead>
        <tr>
            <th>처리부서/단계</th>
            <th>문서명</th>
            <th class="text-left">승인정보</th>
        </tr>
        </thead>

        <?php foreach( $acceptList as $deptKey => $dept) { ?>
        <tbody class="order-list">
            <?php foreach( $dept['typeDoc'] as $docKey => $docData) { ?>
            <tr >
                <td class="text-center">
                    <?=$dept['typeName']?>
                </td>
                <td class="text-center">
                    <?=$docData['name']?>
                </td>
                <td class="text-center ">
                    <?php foreach($docData['acceptData'] as $acceptData) { ?>
                    <div class="accept-box-table">
                        <table class="table table-rows accept-box">
                            <tr>
                                <th>순서</th>
                                <th>승인명</th>
                                <th>승인자</th>
                                <th>수정/삭제</th>
                            </tr>
                            <tr>
                                <td><?=$acceptData['idx']?></td>
                                <td><?=$acceptData['title']?></td>
                                <td><?=$acceptData['managerNm']?></td>
                                <td>
                                    <div class="btn btn-white btn-sm btn-add-accept" data-dept="<?=$deptKey?>" data-type="<?=$docKey?>" data-sno="<?=$acceptData['sno']?>">수정</div>
                                    <div class="btn btn-white btn-sm btn-remove-accept" data-sno="<?=$acceptData['sno']?>">삭제</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php } ?>

                    <div class="accept-box-table">
                        <div class="btn btn-white btn-add-accept" data-dept="<?=$deptKey?>" data-type="<?=$docKey?>">+추가</div>
                    </div>

                </td>
            </tr>
            <?php } ?>
        </tbody>
        <?php } ?>
    </table>

    <div class="table-action clearfix">
        <div class="pull-left"></div>
        <div class="pull-right"></div>
    </div>

</form>

<script type="text/javascript">
    $(function(){
        //승인권자 등록
        let openAcceptDataForm = function(docDept, docType, sno){
            let childNm = 'project_accept_register';
            let addParam = {
                mode: 'simple',
                layerTitle: '승인권자 등록',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                sno : sno,
                docDept : docDept,
                docType : docType,
            };
            layer_add_info(childNm, addParam);
        };

        //승인라인 등록
        $('.btn-add-accept').click(function(){
            let docDept = $(this).data('dept');
            let docType = $(this).data('type');
            let sno = $(this).data('sno');
            openAcceptDataForm(docDept, docType, sno);
        });

        //승인라인 수정
        $('.btn-remove-accept').click(function(){
            $.msgConfirm('해당 승인라인을 삭제 하시겠습니까?', "이미 등록된 문서에는 반영되지 않습니다.").then((result)=>{
                if( result.isConfirmed ){
                    let sno = $(this).data('sno');
                    let param = {
                        mode : 'removeAccept',
                        sno : sno,
                    };
                    $.postAsync('project_ps.php', param).then((data)=>{
                        //무조건 실행.
                        $.msgWithErrorCheck(data, data.message,'','success', ()=>{ window.location.reload() });
                    });
                }
            });
        });

    });
</script>

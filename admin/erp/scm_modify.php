<style>
    .permission-item > td {
        width:100%;
    }

    .permission-item > td > table > td {
        width:50%;
        text-align:center;
    }

    #table-address th {text-align: center}
    #table-address td {text-align: center}

</style>
<form id="frmScm" name="frmScm" action="scm_custom_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
    <input type="hidden" name="mode" value="customModify" id="mode"/>
    <input type="hidden" name="scmNo" value="<?= $getData['scmNo'] ?>"/>

    <div class="page-header js-affix">
        <h3><?php echo end($naviMenu->location); ?></h3>
        <div class="btn-group">
            <?php if($popupMode != 'yes') {?>
                <input type="button" value="목록" class="btn btn-white btn-icon-list" onclick="goList('./scm_list.php');" />
            <?php }?>
            <input type="submit" value="저장" class="btn btn-red"/>
        </div>
    </div>

    <div class="table-title">
        고객사 개별설정 수정
    </div>
    <table class="table table-cols">
        <colgroup>
            <col style="width:200px"/>
            <col/>
        </colgroup>
        <tbody>
        <tr>
            <th >고객사명</th>
            <td ><?= $getData['companyNm'] ?></td>
        </tr>
        <tr>
            <th>카테고리 연결</th>
            <td class="form-inline">
                <?=gd_select_box('cateCd', 'cateCd', $scmCategory, null, $data['cateCd'], '==선택==', 'form-control width-lg'); ?>
            </td>
        </tr>
        <tr>
            <th>폐쇄몰 재고 관리</th>
            <td class="form-inline">
                <label class="checkbox-inline mgl10">
                    <input type="checkbox" name="stockManageFl" value="y" <?= ( 'y' == $data['stockManageFl']) ? 'checked' : '' ; ?>>
                </label>
            </td>
        </tr>
        <tr>
            <th>회원승인 처리</th>
            <td class="form-inline">
                <label class="checkbox-inline mgl10">
                    <input type="checkbox" name="memberAcceptFl" value="y" <?= ( 'y' == $data['memberAcceptFl']) ? 'checked' : '' ; ?>>
                    <span class="notice-info">고객사에서 직접 회원을 승인처리 합니다.</span>
                </label>
            </td>
        </tr>
        <tr>
            <th>주문승인 처리</th>
            <td class="form-inline">
                <label class="checkbox-inline mgl10">
                    <input type="checkbox" name="orderAcceptFl" value="y" <?= ( 'y' == $data['orderAcceptFl']) ? 'checked' : '' ; ?>>
                    <span class="notice-info">고객사에서 직접 주문을 승인처리 합니다.</span>
                </label>
            </td>
        </tr>
        <tr>
            <th >
                배송지 지정 여부
            </th>
            <td class="form-inline">
                <label class="checkbox-inline mgl10">
                    <input type="checkbox" name="deliverySelectFl" value="y" <?= ( 'y' == $data['deliverySelectFl']) ? 'checked' : '' ; ?>>
                    <span class="notice-info">지정된 배송지만 사용합니다.</span>
                </label>
                <label class="checkbox-inline mgl10">
                    <input type="checkbox" name="directAddressFl" value="y" <?= ( 'y' == $data['directAddressFl']) ? 'checked' : '' ; ?>>
                    <span class="notice-info">지정된 배송지 사용시 주소지 직접 입력 여부</span>
                </label>
            </td>
        </tr>
        <tr>
            <th >
                특이사항
            </th>
            <td class="form-inline">
                <textarea class="form-control width-3xl" name="memo" rows="3"><?=$data['memo']?></textarea>
            </td>
        </tr>
        <tr>
            <th>파일첨부</th>
            <td>
                <ul >
                    <?php foreach( $data['files'] as $fileKey => $fileValue ) { ?><li >
                        첨부<?=$fileKey+1?>. <a href="<?=URI_HOME.'download/download.php?name=' . urlencode($fileValue['name']) . '&path=' . urlencode($fileValue['path']) ;?>" class="import-blue" ><?=$fileValue['name']?></a>
                        <a href="#" class="delete-file" data-filekey="<?=$fileKey?>">삭제</a>
                    </li>
                    <?php } ?>
                </ul>

                <ul class="pdl0" id="uploadBox">
                    <li class="form-inline mgb5">
                        <input type="file" name="files[]">
                        <a class="btn btn-white btn-icon-plus addUploadBtn btn-sm">추가</a>
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="table-title">
        배송지 일괄등록
    </div>
    <table class="table table-cols">
        <colgroup>
            <col style="width:200px"/>
            <col/>
        </colgroup>
        <tbody>
        <tr>
            <th >배송지 업로드</th>
            <td >
                <div class="form-inline">
                    <input type="file" name="excel" value="" class="form-control width50p" />
                    <input type="button"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                </div>
                <div>
                    <span class="notice-info">엑셀 파일은 반드시 &quot;Excel 97-2003 통합문서&quot;만 가능하며, csv 파일은 업로드가 되지 않습니다.</span>
                </div>
                <div>
                    <span class="notice-info">배송지명,우편번호,주소,주소상세,전화번호,수령자명</span>
                </div>
            </td>
        </tr>
    </table>
    
    
    <div class="table-title">
        배송지 정보
        <div class="flo-right">
            <span style="font-size:14px;font-weight: normal; color:#8C8C8C">
                <button type="button" class="btn btn-red btn-sm js-add-address" data-sno="" data-scmno="<?= $getData['scmNo'] ?>" >배송지 추가</button>
            </span>
        </div>
    </div>
    <table id="table-address" class="table table-cols">
        <colgroup>
            <col class="width-xs"/>
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />
        </colgroup>
        <tbody>
        <tr>
            <th>번호</th>
            <th>배송지명</th>
            <th>수령자명</th>
            <th>휴대전화</th>
            <th>우편번호</th>
            <th>주소</th>
            <th>주소 상세</th>
            <th>수정</th>
            <th>삭제</th>
        </tr>
        <?php foreach( $addressList as $addressKey => $addressData ) { ?>
        <tr>
            <td><?=$addressKey+1?></td>
            <td><?=$addressData['subject']?></td>
            <td><?=$addressData['receiverName']?></td>
            <td><?=$addressData['receiverCellPhone']?></td>
            <td><?=$addressData['receiverZonecode']?></td>
            <td><?=$addressData['receiverAddress']?></td>
            <td><?=$addressData['receiverAddressSub']?></td>
            <td >
                <a href="#" class="btn btn-dark-gray btn-sm js-add-address" data-sno="<?= $addressData['sno'] ?>" data-scmno="<?= $getData['scmNo'] ?>">수정</a>
            </td>
            <td >
                <a href="#" class="btn btn-dark-gray btn-sm js-remove-address" data-sno="<?= $addressData['sno'] ?>" data-scmno="<?= $getData['scmNo'] ?>">삭제</a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php
    if($popupMode == 'yes') {
        ?>
        <div id="gnbAnchor" style="position: fixed; bottom: 25px; right: 25px;">
            <div class="scrollTop" style="display:none;">
                <a href="#top"><img src="<?= PATH_ADMIN_GD_SHARE ?>img/scroll_top_btn.png"></a>
            </div>
            <div class="scrollDown" style="display:block;">
                <a href="#down"><img src="<?= PATH_ADMIN_GD_SHARE ?>img/scroll_down_btn.png"></a>
            </div>
            <div class="scrollSave">
                <input type="submit" value="" class="save-btn"/>
            </div>
        </div>
        <?php
    }
    ?>
</form>


<script type="text/template" class="template">
    <li class="form-inline mgb5">
        <input type="file" name="files[]">
        <a class="btn btn-white btn-icon-minus minusUploadBtn btn-sm">삭제</a>
    </li>
</script>


<script type="text/javascript">

    $(function(){
        //배송지 주소 추가/수정
        $('.js-add-address').click(function(){
            var scmNo = $(this).data('scmno');
            var sno = $(this).data('sno');
            var title = '배송지 주소 추가';
            if( !$.isEmpty(sno) ){
                title = '배송지 주소 수정';
            }

            var childNm = 'add_address';
            var addParam = {
                mode: 'simple',
                layerTitle: title,
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                scmNo: scmNo,
                sno: sno,
                layerSubject: title,
            };
            layer_add_info(childNm, addParam);
        });

        $('.excel-submit').click(function(){
            $('#mode').val('add_address_batch');
            $('#frmScm').submit();
        });

        $('.js-remove-address').click(function(){

            if( confirm('해당 배송지 주소를 정말 삭제하시겠습니까?') ) {
                var scmNo = $(this).data('scmno');
                var sno = $(this).data('sno');
                var param = {
                    mode : 'delete_address'
                    , scmNo : scmNo
                    , sno : sno
                };

                $.post('scm_custom_ps.php', param, function (data) {
                    alert("삭제 완료 되었습니다.");
                    location.reload();
                });
            }
        });


        $('body').on('click', '.addUploadBtn', function () {
            var uploadBoxCount = $('#uploadBox').find('input[name="upfiles[]"]').length;
            if (uploadBoxCount >= 10) {
                alert("업로드는 최대 10개만 지원합니다");
                return;
            }

            var addUploadBox = _.template(
                $("script.template").html()
            );
            $(this).closest('ul').append(addUploadBox);
            init_file_style();
        });

        $('body').on('click', '.minusUploadBtn', function () {
            index = $(this).prevAll('input:file').attr('index'); //$('.file-upload button.uploadremove').index(target)+1;
            $("input[name='uploadFileNm[" + index + "]']").remove();
            $("input[name='saveFileNm[" + index + "]']").remove();
            $(this).closest('li').remove();
        })

        $('.delete-file').click(function(){
            var fileKey = $(this).data('filekey');
            var params = {
                mode : 'delete_file',
                fileKey : fileKey,
                scmNo : <?= $getData['scmNo'] ?>,
            };
            if( confirm('파일을 삭제하시겠습니까?') ) {
                console.log(params);
                $.post('scm_custom_ps.php', params, function (data) {
                    location.reload();
                });
            }
        });

    });

</script>
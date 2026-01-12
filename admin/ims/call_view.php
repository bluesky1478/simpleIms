<?php include 'library_all.php'?>

<style>
    .claim-option-box{ clear:both; padding-top:5px }
    .claim-option-box .claim-option-size{ padding-left:15px; }
    .claim-option-box .claim-size-name{ min-width:80px; text-align: right; display: inline-block }
    .claim-option-box .claim-option-size ul li{float:left; padding:5px 0px 5px 10px }
    .claim-option-box .claim-option-name{ margin : 10px 0px 5px 0px }
    .claim-selected-item { font-weight: bold; color: #d51f1f }
    .refund-table-box td { height:15px!important; text-align: left; border-bottom: none!important; }
</style>

<form id="frmOrder" class="frm-order"  action="ims_ps.php" method="post" target="ifrmProcess">
    <div id="return-app">

        <input type="hidden" value="saveComment" name="mode">
        <input type="hidden" value="<?=$requestParam['div']?>" name="commentDiv">
        <input type="hidden" value="<?=$projectData['project']['sno']?>" name="projectSno">
        <input type="hidden" value="<?=$requestParam['commentSno']?>" name="sno">

        <div class="page-header js-affix" style="margin-bottom:0">
                <h3><span class="text-blue"><?=$projectData['customer']['customerName']?></span> <?=$commentDivName?> 단계 비고</h3>
            <div class="btn-group">
                <?php if($requestParam['commentSno']) { ?>
                    <div class="btn btn-red btn-sm btn-save" style="padding-top:6px;" >수정</div>
                    <div class="btn btn-white btn-sm btn-reset" style="padding-top:6px;" >신규</div>
                <?php }else{ ?>
                    <div class="btn btn-red btn-sm btn-save" style="padding-top:6px;" >저장</div>
                <?php } ?>
                <div class="btn btn-white btn-sm" style="padding-top:6px;" onclick="self.close()">닫기</div>
            </div>
        </div>

        <table class="table table-cols" style="border-top:none !important;margin-right:10px">
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-r">비고내용</th>
                <td class="ta-l" colspan="3" style="padding:0px">
                    <textarea class="form-control" rows="5" name="comment" id="editor" placeholder="내용입력" ><?=$defaultData?></textarea>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="table-title gd-help-manual ">
            이력
        </div>
        <table class="table table-rows ims-comment-table" >
            <colgroup>
                <col class="width-xs"/>
                <col class="width-xs"/>
                <col class="width-xs"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-c">등록일자</th>
                <th class="ta-c">등록자</th>
                <th class="ta-c">등록단계</th>
                <th class="ta-c">내용</th>
            </tr>
            <?php foreach($list as $callValue) { ?>
            <tr>
                <td class="ta-c">
                    <?=$callValue['regDt']?>
                    <?php if( $managerInfo['sno'] == $callValue['regManagerSno']) { ?>
                        <br>
                        <div v-show=" == comment.regManagerSno " class="btn btn-sm btn-white btn-memo-modify"  data-sno="<?=$callValue['sno']?>">수정</div>
                        <div v-show=" == comment.regManagerSno " class="btn btn-sm btn-white btn-memo-delete"  data-sno="<?=$callValue['sno']?>">삭제</div>
                    <?php } ?>
                </td>
                <td class="ta-c">
                    <?=$callValue['regManagerName']?>
                </td>
                <td class="ta-c">
                    <?=\Component\Ims\ImsCodeMap::PROJECT_COMMENT_DIV[$callValue['commentDiv']]?>
                </td>
                <td>
                    <?=nl2br($callValue['comment'])?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</form>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/editorLoad.js?ss=<?= date('YmdHis') ?>" charset="utf-8"></script>

<script type="text/javascript">

    var myFnc = function(){
        //self.close();
        //parent.opener.location.reload();
        location.reload();
    }

    $(()=>{

        $('.js-datepicker').on('dp.change', function(){
            if($(this).prop('class') == 'input-group js-datepicker mix-picker') {
                $(this).closest('td').find('.mix-picker-data').val($(this).find('.mix-picker-text').val());
            }
        })

        $('.input-dept').click(function(){
            $( '.input-info' ).hide();
            $( '#' + $(this).val()+'-info' ).show('fast');
        });

        $('.btn-memo-delete').click(function(){
            const sno = $(this).data('sno');
            $.msgConfirm('코멘트를 삭제 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('deleteComment',{
                        'sno' : sno,
                    }).then((data)=>{
                        location.reload();
                    });
                }
            });
        });

        $('.btn-memo-modify').click(function(){
            const sno = $(this).data('sno');
            location.href=`call_view.php?sno=<?=$projectData['project']['sno']?>&commentSno=${sno}`;
        });

        $('.btn-save').click(()=>{
            oEditors.getById["editor"].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.
            if( $.isEmpty($('#editor').val()) ){
                alert('빈내용은 저장할 수 없습니다.');
                return false;
            }else{
                $('.btn-save').off('click');
                $('#frmOrder').submit();
            }
        });

        $('.btn-reset').click(()=>{
            location.href='call_view.php?sno=<?=$projectData['project']['sno']?>';
        });


    });

</script>
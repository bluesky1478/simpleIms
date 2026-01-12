<style>
	.goods-grid-area { height: 480px; }
	.goods-grid-act-top select,
	.goods-grid-act-top span { float: left; line-height: 20px; }
	.goods-grie-bottom-info-area>div{ margin-bottom: 3px !important; }
	.goods-grie-bottom-info-area>div:first-child{ margin-top: 3px; }
	.goods-grie-bottom-area {
		width: 100%;
		float: left;
		text-align: center;
		margin-top: 10px;
	}
	.js-field-select-wapper {
		height:400px;
		overflow:scroll;
		overflow-x:hidden;
		border:1px solid #dddddd;
	}

	.js-field-default td { border:1px solid #dddddd; }

	.table-cols { margin-top:3px; margin-bottom:3px; border: 1px solid #dddddd;}

	.add-display-td input[type="checkbox"]{
		margin : 0 !important;
	}

    .bootstrap-filestyle {display:none}

</style>

<div class="goods-grid-area">
	<form name="frmProjectRegister" id="frmProjectRegister">
        <input type="hidden" name="mode" value="saveAccept" />
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>순서</th>
                    <td>
                        <input type="text" class="form-control" placeholder="숫자 입력" name="idx" value="<?=$acceptData['idx']?>">
                        <input name="docDept" value="<?=$requestParam['docDept']?>" type="hidden">
                        <input name="docType" value="<?=$requestParam['docType']?>" type="hidden">
                        <input name="sno" value="<?=$requestParam['sno']?>"  type="hidden">
                    </td>
                </tr>
                <tr>
                    <th>승인제목</th>
                    <td>
                        <input type="text" class="form-control" placeholder="승인, 관리자 등.." name="title" value="<?=$acceptData['title']?>">
                    </td>
                </tr>
                <tr>
                    <th>승인자</th>
                    <td>
                        <div class="form-inline">
                            <?= gd_select_box('managerSno', 'managerSno', $managerList, null, $acceptData['managerSno'], '선택', '', 'form-control'); ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!--정책 리스트 영역-->
        <div class="goods-grie-bottom-area">
            <?php if( empty($sno) ) { ?>
                <input type="button" value="등록" class="btn btn-gray js-save"   />
            <?php }else{ ?>
                <input type="button" value="수정" class="btn btn-gray js-save"   />
            <?php } ?>
            <input type="button" value="취소" class="btn btn-white js-close"   />
        </div>
        
    </form>
</div>

<script type="text/javascript">
    $(function(){

        $('#regDocDept').change(function(){
            let html = '';
            $('#regDocType').html('');
            let params = {
                mode : 'getDocType',
                docDept : $(this).val()
            }
            $.post('project_ps.php', params, function(result){
                if( !$.isEmpty(result.data) && false != result.data ){
                    for(let key in result.data){
                        html = "<option value='"+key+"'>" + result.data[key] + "</option>";
                        $('#regDocType').append(html);
                        $('#regDocType').val('<?=$search['docType']?>');
                    }
                }
                isFirst = false;
            });
        });
        
        let saveAccept = function(){
            let saveData = $('#frmProjectRegister').serialize();
            $.postAsync('project_ps.php', saveData).then((data)=>{
                //무조건 실행.
                $.msgWithErrorCheck(data, data.message,'','success', ()=>{ window.location.reload() });
            });
        }

        $('.js-save').click(function(){
            saveAccept();
        });

        $('.js-close').click(function(){
            layer_close();
        });

    });
</script>

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

<script>
    $(function(){
        $('.js-example-basic-single').select2({
            placeholder: '고객사 선택'
        });
    });
</script>

<div class="goods-grid-area">
	<form name="frmProjectRegister" id="frmProjectRegister">
        <input type="hidden" name="mode" value="saveProject" />
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>프로젝트 등록자</th>
                    <td>
                        <div class="form-inline">
                            <input type="hidden" name="regManagerSno" value="<?=\Session::get('manager.sno')?>" />
                            <input type="text" class="form-control" style="width:100%"  value="<?=\Session::get('manager.managerNm')?>"  readonly  />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>프로젝트명</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="projectName" id="projectName" class="form-control" placeholder="프로젝트명 입력" style="width:100%" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>고객사</th>
                    <td>
                        <select2 class="js-example-basic-single" style="width:50%"  id="company"  name="companySno">
                            <option value="">미정(추후등록)</option>
                            <?php foreach ($companyListMap as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                        <small class="text-muted">미선택시 나중에 지정가능</small>
                    </td>
                </tr>
                <tr>
                    <th>프로젝트타입</th>
                    <td>
                        <div class="form-inline">
                            <select class="form-control">
                                <option value="">미정(추후등록)</option>
                                <?php foreach ($typeListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>프로젝트 설명</th>
                    <td>
                        <textarea class="form-control width100" name="description" rows="3"></textarea>
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
            <input type="button" value="취소" class="btn btn-white js-close "  />
        </div>
        
    </form>
</div>

<script type="text/javascript">
    $(function(){

        let saveProject = function(){
            let companySno = '';
            if( $('#company').select2('data').length > 0 ){
                companySno = $('#company').select2('data')[0];
            }
            let saveData = $('#frmProjectRegister').serialize() + '&companySno=' + companySno;
            $.postAsync('project_ps.php', saveData).then((data)=>{
                //무조건 실행.
                $.msgWithErrorCheck(data, data.message,'','success', ()=>{ window.location.reload() });
            });
        }

        $('.js-save').click(function(){
            saveProject();
        });
        $('.js-close').click(function(){
            layer_close();
        });
    });
</script>

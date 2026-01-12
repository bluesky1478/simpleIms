<script>
    var myFnc = function(){
        console.log('TEST');
    }
</script>
<style>
	.goods-grid-area { height: 145px; }
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
</style>

<div class="goods-grid-area" id="app-member-list">
	<form name="frmPolicyRegister" id="frmPolicyRegister" action="./goods_ps.php" method="post" target="ifrmProcess" >
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm" />
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        <?=$request['title']?>
                    </th>
                    <td>
                        <?php if( strpos($request['key'], "Dt") !== false ) { ?>

                            <input type="text" class="form-control width-md mix-picker-data" id="updateField" value="<?=$request['dataValue']?>"  style="0"   />

                            <div class="input-group js-datepicker mix-picker" >
                                <input type="text" class="form-control width-xs mix-picker-text" value="<?=gd_date_format('Y-m-d',$request['dataValue'])?>" />
                                <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                            </div>
                        <?php }else{ ?>
                            <div class="form-inline">
                                <input type="text" id="updateField" class="form-control" style="width:100%" value="<?=$request['dataValue']?>"  />
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        자동입력
                    </th>
                    <td>
                        <div class="btn btn-gray btn-auto-input">해당없음</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!--정책 리스트 영역-->
    <div class="goods-grie-bottom-area mgt20">
        <input type="button" value="수정" class="btn btn-red js-save"  />
        <input type="button" value="취소" class="btn btn-white js-close" />
    </div>

</div>

<script type="text/javascript">

    $(()=>{
        let firstClick = false;

        $('#updateField').keyup((e)=>{
            if(e.keyCode == 13) {
                $('.js-save').click();
            }
        });

        $('.btn-auto-input').click(function(){
            $('#updateField').val($(this).text());
            $('.js-save').click();
        });

        $('.js-save').click(function(){

            let mode = 'eachUpdate';
            $.post('ims_ps.php', {
                mode:mode,
                field:'<?=$request['key']?>',
                dataValue:$('#updateField').val(),
                sno:'<?=$request['sno']?>',
            }, function (data) {
                if(data){
                    location.reload();
                }
            });
        });
        $('.js-close').click(function(){
            $(document).off("keydown");
            layer_close();
        });

        $('.js-datepicker').on('dp.change', function(){
            if( firstClick ){
                if($(this).prop('class') == 'input-group js-datepicker mix-picker') {
                    $(this).closest('td').find('.mix-picker-data').val($(this).find('.mix-picker-text').val());
                }
            }
            firstClick = true;
        })

    });
</script>



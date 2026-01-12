

<style>
	.goods-grid-area { height: 780px; }
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
		height:700px;
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

<div class="goods-grid-area" id="app-policy-list">
	<form name="frmClaimProc" id="frmClaimProc" method="post" target="ifrmProcess" >
        <input type="hidden" name="sno" id="sno" value="<?=$data['sno']?>" />
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>고객요청분류<br>(집계용)</th>
                    <td>
                        <div>
                            <?=gd_select_box('reqType', 'reqType', $reqTypeContents, null, $data['reqType'], '==분류선택==', 'form-control js-status-change width-lg'); ?>
                        </div>
                        <div class="form-inline">
                            <input type="text" name="addReqTypeContents" id="addReqTypeContents" class="form-contro width-lg" />
                            <input type="button" value="추가" class="btn btn-gray js-policy-add" @click="addReqTypeContents" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>요청상품</th>
                    <td>
                            <div style="font-weight:bold;">
                                <?=implode('<br>',$data['goodsHtml']) ?>
                            </div>
                    </td>
                </tr>
                <tr>
                    <th>요청내용</th>
                    <td>
                        <textarea name="reqContents" class="form-control width100p" rows="5" disabled="disabled"><?= $data['reqContents']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>처리내용</th>
                    <td>
                        <textarea name="procContents" id="procContents" class="form-control width100p" rows="5" placeholder="처리내용 입력"><?= $data['procContents']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>처리상태</th>
                    <td>
                        <?=gd_select_box('procStatus', 'procStatus', $procStatusMap, null, $data['procStatus'], '==선택==', 'form-control js-status-change width-lg'); ?>
                    </td>
                </tr>
                <tr>
                    <th>처리일자</th>
                    <td>
                        <div class="input-group js-datepicker" style="width:100px">
                            <input type="text" class="form-control width-xs" name="procDt" id="procDt" value="<?=$data['procDt']; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>기타/개선<br>요청사항</th>
                    <td>
                        <textarea name="memberMemo" id="memberMemo" class="form-control width100p" rows="3" readonly><?= $data['memberMemo']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>관리자메모</th>
                    <td>
                        <textarea name="adminMemo" id="adminMemo" class="form-control width100p" rows="3" placeholder="메모 입력"><?= $data['adminMemo']; ?></textarea>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </form>
    <!--정책 리스트 영역-->
    <div class="goods-grie-bottom-area">
        <?php if( 'as' !== $claimType  ) { ?>
        <input type="button" value="<?=$claimTypeStr?>처리" class="btn btn-red js-proc"  @click="proc" />
        <?php } ?>
        <input type="button" value="저장" class="btn btn-gray js-save"  @click="saveProc" />
        <input type="button" value="닫기" class="btn btn-white js-close" />
    </div>
</div>


<script type="text/javascript">
	<!--
	$(document).ready(function () {

	    var appPolicyList = new Vue({
            el: '#app-policy-list'
            , data : {
                items : []
            }
            , methods : {
                proc:function(){
                    var sno = $("#sno").val();
                    var url = '/order/popup_order_view_status.php?orderNo=' + <?=$orderNo?> + '&actionType=' + '<?=$claimType?>' + '&claimSno=' + sno + '&orderGoodsSnoList=' + '<?=$data["orderGoodsSnoList"]?>' + '&reqCnt=' + '<?=$data["reqCnt"]?>' ;
                    //window.open(url);
                    var win = popup({
                        url: url,
                        target: 'popup_order_view_status',
                        width: 1024,
                        height: 800,
                        scrollbars: 'yes',
                        resizable: 'yes'
                    });
                    win.focus();
                }
                , saveProc : function(){
                    var sno = $("#sno").val();
                    var saveData = {
                        mode : 'save_claim_proc'
                        , sno : sno
                        , procStatus : $('#procStatus').val()
                        , procContents : $('#procContents').val()
                        , procDt : $('#procDt').val()
                        , adminMemo : $('#adminMemo').val()
                        , reqType : $('#reqType').val()
                    };
                    //console.log(saveData);
                    $.post('claim_ps.php', saveData, function (data) {
                        if(data){
                            //alert('처리내용 저장완료.','성공');
                            //바로 닫기
                            window.location.reload();
                        }
                    });
                }
                , addReqTypeContents : function(){
                    var contents = $('#addReqTypeContents').val();

                    if( $.isEmpty(contents) ){
                        alert('분류 내용을 입력해주세요.');
                        return false;
                    }

                    var claimType = '<?=$claimType?>';
                    var param = {
                        mode : 'add_req_contents'
                        , claimType : claimType
                        , reqTypeContents : contents
                    };
                    $.post('claim_ps.php', param, function (data) {
                        if(data){
                            var parsingData = JSON.parse(data);
                            var optionTag = '<option value="'+parsingData.sno+'">'+contents+'</option>';
                            $('#reqType').append(optionTag);
                            $('#addReqTypeContents').val('');
                            dialog_alert('요청분류가 추가되었습니다. 분류선택에서 확인하세요.','성공');
                        }
                    });
                }
            }
        });

		//document 에 할당된 event 가 레이어 클로즈 후 다시 레이어 실행시 중복 이벤트 등록되어 초기화 해줌.
		//$(document).off("click", ".js-field-default tbody tr");
		//$(document).off("click", ".js-field-select tbody tr");
		//$(document).off("keydown");

		$('.js-close').click(function(){
			$(document).off("keydown");
			layer_close();
            window.location.reload();
		});
		$('div.bootstrap-dialog-close-button').click(function() {
			$(document).off("keydown");
            window.location.reload();
		});

	});
	//-->
</script>

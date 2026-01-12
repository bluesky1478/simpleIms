<style>
    .modal-content { height:370px }
	.goods-grid-area { height: 130px; }
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
	.js-field-default td { border:1px solid #dddddd; }
	.table-cols { margin-top:3px; margin-bottom:3px; border: 1px solid #dddddd;}
	.add-display-td input[type="checkbox"]{
		margin : 0 !important;
	}
</style>

<div class="goods-grid-area" id="app-payment-request">
	<form name="frmAddAddress" id="frmAddAddress" method="post" >

        <input type="hidden" name="mode" value="add_address" />
        <input type="hidden" name="scmNo" value="<?=$scmNo?>" />
        <input type="hidden" name="sno" value="<?=$sno?>" />

        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>배송지 제목</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="subject" id="subject" class="form-control" value="<?=$addressData['subject']?>"  style="width:90%"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>수령자명</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="receiverName" id="receiverName" class="form-control" value="<?=$addressData['receiverName']?>"  style="width:90%"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>휴대폰번호</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="receiverCellPhone" id="receiverCellPhone" class="form-control" value="<?=$addressData['receiverCellPhone']?>"  style="width:90%"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td class="js-receiver-parent-info-area">
                        <div class="form-inline">
                            <input type="text" name="receiverZonecode" value="<?=$addressData['receiverZonecode']?>" size="5" class="form-control js-receiver-zonecode" readonly="readonly" />
                            <input type="hidden" name="receiverZipcode" value="<?=$addressData['receiverZipcode']?>" class="js-receiver-zipcode" />
                            <input type="button" value="우편번호찾기" class="btn btn-sm btn-gray js-post-search-btn"/>
                        </div>
                        <div class="mgt5">
                            <input type="text" name="receiverAddress" value="<?=$addressData['receiverAddress']?>" class="form-control js-receiver-address" readonly="readonly" />
                        </div>
                        <div class="mgt5">
                            <input type="text" name="receiverAddressSub" value="<?=$addressData['receiverAddressSub']?>" class="form-control js-receiver-address-sub"/>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!--정책 리스트 영역-->
    <div class="goods-grie-bottom-area">
        <?php if( empty($sno) ) { ?>
            <input type="button" value="추가" class="btn btn-gray btn-add-address js-save"   />
        <?php }else{ ?>
            <input type="button" value="수정" class="btn btn-gray btn-add-address js-save"   />
        <?php } ?>
        <input type="button" value="취소" class="btn btn-white js-close" />
    </div>
</div>


<script type="text/javascript">
	$(document).ready(function () {

        var addAddress = function(){
            var saveData = $('#frmAddAddress').serialize();
            $.post('scm_custom_ps.php', saveData, function (data) {
                if(data){
                    //바로 닫기
                    window.location.reload();
                }
            });
        }

        //주소 추가
        $('.btn-add-address').off('click').on('click',addAddress);

		$('.js-close').click(function(){
			$(document).off("keydown");
			layer_close();
		});

		$('div.bootstrap-dialog-close-button').click(function() {
			$(document).off("keydown");
		});

        $(document).off("click", ".js-field-default tbody tr");
        $(document).off("click", ".js-field-select tbody tr");
        $(document).off("keydown");


        //우편번호 찾기
        $(document).on('click', '.js-post-search-btn', function(e){
            var parentObj = $(this).closest(".js-receiver-parent-info-area");
            var receiverZonecodeName = parentObj.find(".js-receiver-zonecode").attr('name');
            var receiverAddressName = parentObj.find(".js-receiver-address").attr('name');
            var receiverZipcodeName = parentObj.find(".js-receiver-zipcode").attr('name');
            var popupAble = true;

            if($("input[name='isUseMultiShipping']").val() && $("input[name='multiShippingFl']").val() === 'y'){
                if(parentObj.find('.select-goods-area>table>tbody>tr').length > 0){
                    var result = confirm("수령자 정보를 변경 할 경우 선택한 상품이 초기화 됩니다.\n계속 진행하시겠습니까?");
                    if(!result){
                        popupAble = false;
                    }
                    else {
                        resetMultiShippingSelectGoods(parentObj);
                    }
                }
            }

            if(popupAble === true){
                postcode_search(receiverZonecodeName, receiverAddressName, receiverZipcodeName);
            }
        });

	});
</script>

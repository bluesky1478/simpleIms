<style>
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
	<form name="frmAddPayments" id="frmAddPayments" method="post" >

        <input type="hidden" name="mode" value="addPayments" />
        <input type="hidden" name="orderNo" value="<?=$orderNo?>" />

        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>결제요청제목</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="paymentSubject" id="paymentSubject" class="form-control" value="" placeholder="추가 결제 내용을 입력하세요"  style="width:90%"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>결제요청금액</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="reqPrice" id="reqPrice" class="form-control js-number" value=""  style="width:90%" />원
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!--정책 리스트 영역-->
    <div class="goods-grie-bottom-area">
        <input type="button" value="추가" class="btn btn-gray btn-add-payment js-save"   />
        <input type="button" value="취소" class="btn btn-white js-close" />
    </div>
</div>


<script type="text/javascript">
	$(document).ready(function () {

        var addPayments = function(){
            if( !$.isEmpty( $('#paymentSubject').val()) && !$.isEmpty($('#reqPrice').val())) {
                var saveData = $('#frmAddPayments').serialize();
                $.post('order_ajax.php', saveData, function (data) {
                    if(data){
                        //바로 닫기
                        window.location.reload();
                    }
                });
            }else{
                alert('결제 제목과 금액은 필수 입니다.');
            }
        }

        //결제 추가
        $('.btn-add-payment').off('click').on('click',addPayments);

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

	});
</script>

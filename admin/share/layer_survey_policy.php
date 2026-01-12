<style>
	.goods-grid-area { height: 730px; }
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
		height:550px;
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
	<form name="frmPolicyRegister" id="frmPolicyRegister" action="./goods_ps.php" method="post" target="ifrmProcess" >
        <input type="hidden" name="mode" value="get_connect_goods_free_policy" />
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                    <col class="width-sx"/>
                </colgroup>
                <tbody>
                <tr>
                    <th>정책명</th>
                    <td >
                        <div class="form-inline">
                            <input type="text" name="policyName" id="policyName" class="form-control width100p"/>
                        </div>
                    </td>
                    <th rowspan="2">
                        <input type="button" value="추가" class="btn btn-gray js-policy-add" @click="addPolicy" />
                    </th>
                </tr>
                <tr>
                    <th>설문주소</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="surveyAddress" class="form-control width100p"  />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>설문발송일</th>
                    <td>
                        <div class="form-inline">
                            구매 확정일로부터 <input type="text" name="surveyDayCount" class="form-control" style="width:20%" />일
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>
    <form name="frmPolicyList" id="frmPolicyList" action="./goods_ps.php" method="post" target="ifrmProcess" >
        <div class="js-field-select-wapper">
            <table class="table table-rows">
                <thead>
                <tr>
                    <th class="width2p">선택</th>
                    <th class="width18p">정책명</th>
                    <th class="width18p">설문주소</th>
                    <th class="width2p">설문발송일</th>
                    <th class="width12p">사용여부</th>
                    <!--<th class="width5p">제거</th>-->
                </tr>
                </thead>
                <tbody >
                <tr v-for="(item, index) in items" :key="index">
                    <td class="center">
                        <input type="radio" name="sno" :value="item.sno"/>
                    </td>
                    <td>
                        <input type="text" name="policyName" :value="item.policyName" :data-sno="item.sno" class="form-control" @keyup="updateInputPolicy" />
                    </td>
                    <td class="left">
                        <input type="text" name="surveyAddress" :value="item.surveyAddress" :data-sno="item.sno" class="form-control" @keyup="updateInputPolicy" />
                    </td>
                    <td class="center">
                        <input type="text" name="surveyDayCount" :value="item.surveyDayCount" :data-sno="item.sno" class="form-control center" @keyup="updateInputPolicy" />
                    </td>
                    <td class="center">
                        <input type="radio" :name="item.useFlName" data-updatekey="useFl" :data-sno="item.sno" value="y" :checked="item.useFlY" @change="updateRadioPolicy" /> 예
                        <input type="radio" :name="item.useFlName" data-updatekey="useFl" :data-sno="item.sno" value="n" :checked="item.useFlN" @change="updateRadioPolicy" /> 아니오
                    </td>
                    <!--<td class="center">
                        <input type="button" value="삭제" class="btn btn-gray js-policy-add" @click="addPolicy" /> TODO 삭제(연결 완료후 시간나면)
                    </td>-->
                </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!--정책 리스트 영역-->
    <div class="goods-grie-bottom-area">
        <?php if( $selectedGoodsCnt > 0 ) { ?>
        <input type="button" value="연결" class="btn btn-gray js-save"  @click="linkGoodsPolicy" />
        <?php } ?>
        <input type="button" value="취소" class="btn btn-white js-close" />
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
                addPolicy : function(){
                    $("input[name='mode']").val('add_survey_policy');
                    var formData = $("#frmPolicyRegister").serialize();

                    if( !$.isEmpty($('#policyName').val()) ){
                        $.post('goods_ps.php', formData, function (data) {
                            if(data){
                                setPolicyItemList();
                            }
                            //dialog_alert('정책 추가 완료','성공');
                        });
                    }else{
                        dialog_alert("정책명을 입력하세요!");
                    }
                }
                , updateInputPolicy : function(e){
                    var updateData = {
                        sno : e.target.attributes.getNamedItem('data-sno').value
                        , updateKey : e.target.name
                        , updateValue : e.target.value
                        , updatePolicyType : 'survey' // freeSale, sale, survey
                    };
                    updatePolicy(updateData);
                }
                , updateRadioPolicy : function(e){
                    var updateData = {
                        sno : e.target.attributes.getNamedItem('data-sno').value
                        , updateKey : e.target.attributes.getNamedItem('data-updatekey').value
                        , updateValue : e.target.value
                        , updatePolicyType : 'survey' // freeSale, sale, survey
                    };
                    updatePolicy(updateData);
                }
                , linkGoodsPolicy : function(){
                    var policySno = $('input[name="sno"]:checked').val();

                    if( $.isEmpty(policySno)){
                        alert('정책을 선택해주세요!');
                        return false;
                    }

                    //리스트가 필요할 때
                    var goodsNo = [];
                    $('input[name*="goodsNo"]:checked').each(function() {
                        goodsNo.push($(this).val());
                    });

                    var mergeData = {
                        mode : 'link_policy'
                        , goodsNo : goodsNo
                        , policySno : policySno
                        , policyKey : 'policySurveySno'
                    };

                    $.post('goods_ps.php', mergeData, function (data) {
                        if(data){
                            //setPolicyItemList();
                            //dialog_alert('정책 적용 완료','성공');
                            //바로 닫기
                            window.location.reload();
                        }
                        //dialog_alert('정책 추가 완료','성공');
                    });
                }
            }
        });
        var setPolicyItemList = function(){
            $.post('goods_ps.php', 'mode=get_survey_policy_list', function (data) {
                var parsingData = JSON.parse(data);
                for(var key in parsingData){
                    if(parsingData[key]['useFl'] === 'y'){
                        parsingData[key]['useFlName'] = 'useFl'+parsingData[key]['sno'];
                        parsingData[key]['useFlY'] = "Checked";
                    }else{
                        parsingData[key]['useFlN'] = "Checked";
                    }
                }
                appPolicyList.items = parsingData;
            });
        };

		//document 에 할당된 event 가 레이어 클로즈 후 다시 레이어 실행시 중복 이벤트 등록되어 초기화 해줌.
		$(document).off("click", ".js-field-default tbody tr");
		$(document).off("click", ".js-field-select tbody tr");
		$(document).off("keydown");

		$('.js-close').click(function(){
			$(document).off("keydown");
			layer_close();
		});

		$('div.bootstrap-dialog-close-button').click(function() {
			$(document).off("keydown");
		});

        setPolicyItemList();

	});
	//-->
</script>

<style>
	.goods-grid-area { height: 550px; }
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
                    <th><div  style="width:40px">공급사</div></th>
                    <td>
                        <div class="form-inline">
                            <?=gd_select_box('scmList', 'scmList', $scmList, null, null, '=전체='); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div  style="width:40px">ID</div</th>
                    <td>
                        <div class="form-inline">
                            <input type="text" name="memId" id="memId" class="form-control" />
                            <input type="button" value="조회" class="btn btn-gray js-policy-add" @click="searchMember" />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>
    <form name="frmMemberList" id="frmMemberList" action="./goods_ps.php" method="post" target="ifrmProcess" >
        <div class="js-field-select-wapper">
            <table class="table table-rows">
                <thead>
                <tr>
                    <th class="width5p">
                        <input type='checkbox' value='y' class='js-checkall' data-target-name='memNo'/>
                    </th>
                    <th class="width10p">공급사</th>
                    <th class="width10p">회원명</th>
                    <th class="width10p">회원ID</th>
                </tr>
                </thead>
                <tbody >
                <tr v-for="(item, index) in items" :key="index">
                    <td class="center">
                        <input type="checkbox" name="memNo[]" :value="item.memNo"/>
                    </td>
                    <td>
                        {{item.ex1}}
                    </td>
                    <td class="center">
                        {{item.memNm}}
                    </td>
                    <td class="center">
                        {{item.memId}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!--정책 리스트 영역-->
    <div class="goods-grie-bottom-area">
        <input type="button" value="연결" class="btn btn-gray js-save"  @click="linkGoodsMember" />
        <input type="button" value="취소" class="btn btn-white js-close" />
    </div>

</div>


<script type="text/javascript">
	<!--
	$(document).ready(function () {

	    var appMemberList = new Vue({
            el: '#app-member-list'
            , data : {
                items : []
            }
            , methods : {
                searchMember : function(){
                    //고객 검색
                    setMemberItemList();
                }
                , linkGoodsMember : function(){

                    var memNo = [];
                    $('input[name*="memNo"]:checked').each(function() {
                        memNo.push($(this).val());
                    });

                    if(0 >= memNo.length){
                        alert('회원을 선택해주세요!');
                        return false;
                    }

                    var goodsNo = [];
                    $('input[name*="goodsNo"]:checked').each(function() {
                        goodsNo.push($(this).val());
                    });

                    var mergeData = {
                        mode : 'link_member'
                        , goodsNo : goodsNo
                        , memNo : memNo
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
        var setMemberItemList = function(){
            var $elScmSelectBox = $('#scmList option:selected');

            var goodsNo = [];
            $('input[name*="goodsNo"]:checked').each(function() {
                goodsNo.push($(this).val());
            });

            var searchObject = {
                mode : 'get_member_list'
                , memId : $('#memId').val()
                , ex1 : $.isEmpty($elScmSelectBox.val())?'':$elScmSelectBox.text()
                , goodsNo : goodsNo
            }
            //console.log('search object');
            //console.log(searchObject);
            $.post('goods_ps.php', searchObject, function (data) {
                var parsingData = JSON.parse(data);
                //console.log(parsingData);
                appMemberList.items = parsingData;
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

        //setMemberItemList();

	});
	//-->
</script>

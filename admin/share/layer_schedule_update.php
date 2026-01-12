<script>
    var myFnc = function(){
        console.log('TEST');
    }
</script>
<style>
    .goods-grid-area { height: 300px; }
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
        <input type="hidden" name="mode" value="scheduleUpdate">
        <input type="hidden" name="sno" value="<?=$request['sno']?>">

        <div class="search-detail-box">
            <table class="table table-cols">
                <tbody>
                <tr>
                    <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                        <th class="center "><?=$stepData?></th><!--스텝별 예정일-->
                    <?php }?>
                </tr>
                <tr>
                    <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                        <td class="center ">
                            <div class="input-group js-datepicker">
                                <input type="text" class="form-control width-xs" name="prdStep<?=$stepKey?>[expectedDt]" value="<?=$data['prdStep'.$stepKey]['expectedDt']?>" />
                                <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                            </div>
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                        <td class="center ">
                            <input type="text" class="form-control w100" placeholder="일정 대체 내용" name="prdStep<?=$stepKey?>[memo]"  value="<?=$data['prdStep'.$stepKey]['memo']?>">
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                        <td class="center ">
                            <div class="input-group js-datepicker">
                                <input type="text" class="form-control width-xs" name="prdStep<?=$stepKey?>[completeDt]" value="<?=$data['prdStep'.$stepKey]['completeDt']?>" />
                                <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                            </div>
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <th>비고 남기기</th>
                    <td colspan="4" style="padding:0">
                        <textarea class="form-control" name="produceMemo" rows="5" placeholder="비고 남기기"></textarea>
                    </td>
                    <th>기존 비고 내용</th>
                    <td colspan="4" >
                        <div class="btn btn-gray btn-call-with" data-sno="<?=$request['sno']?>" data-div="produce">내용(<?=number_format($data['commentCnt'])?>)</div>
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

<script type="text/javascript" src="../js/jquery/jquery.serialize.object.js"></script>
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
            const formData = $("#frmPolicyRegister").serializeObject();

            validProduceSchedule(formData).then((isContinue)=>{
                if( isContinue ){
                    $.post('ims_ps.php', formData, function (data) {
                        if(data){
                            location.reload();
                        }
                    });
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
        });

        $('.btn-call-with').on({
            'click': function(e){
                const sno = $(this).data('sno');
                const div = $(this).data('div');
                const url = `call_view.php?sno=${sno}&div=${div}`;
                openCallView(url);
                return false;
            },'mouseover' :function (e) { // 메모보기 클릭 시
                /*
                const $el = $(this);
                const sno = $(this).data('sno');
                const div = $(this).data('div');
                const top = ($(this).position().top) - 50;  //보기 버튼 top
                const left = ($(this).position().left) - 660; //보기 버튼의 left
                $.post("layer_order_add_info", {
                    sno: sno,
                    div: div
                }, function (result) {
                    console.log(result);
                    $el.after('<div class="memo_layer"></div>');
                    $('.memo_layer').html(result);
                    $('.memo_layer').css({
                        "top": top
                        , "left": left
                        , "right": "0px"
                        , "position": "absolute"
                        , "width": "650px"
                        , "overflow": "hidden"
                        , "height": "auto"
                        , "z-index": "999"
                        , "border": "1px solid #cccccc"
                        , "background": "#ffffff"
                    }).show();
                }, "html");
                //}
                //});*/
            },
            'mouseout'  :function (e) {
                /*$('.memo_layer').remove();*/
            }
        });

    });
</script>



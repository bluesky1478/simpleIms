<script type="text/javascript">
    function goLogout(){
        confirm(dialog_confirm('로그아웃 하시겠습니까?', function (result) {
            if (result) {
                location.href = "../base/login_ps.php?mode=logout";
            }
        }));
    }

    var openCallView = function(url){
        let win = popup({
            url: url,
            target: '',
            width: 925,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
        return win;
    }

    $(()=>{
        $('.btn-call').on({
            'click': function(e){
                let sno = $(this).data('sno');
                let url = `call_view.php?salesSno=${sno}`;
                openCallView(url);
                return false;
            }
        });
        $('.btn-call-with').on({
            'click': function(e){
                let sno = $(this).data('sno');
                let url = `call_view.php?salesSno=${sno}`;
                openCallView(url);
                return false;
            },'mouseover' :function (e) { // 메모보기 클릭 시
                const $el = $(this);
                const sno = $(this).data('sno');
                const top = ($(this).position().top) - 50;  //보기 버튼 top
                const left = ($(this).position().left) - 660; //보기 버튼의 left
                //$.each($('.js-order-add-info').closest('td'), function (key, val) {
                    //if ($(val).data('order-no') === selectOrderNo) {
                        $.post("layer_order_add_info", {salesSno: sno}, function (result) {
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
                //});
            },
            'mouseout'  :function (e) {
                $('.memo_layer').remove();
            }
        });


        <?php if( !empty($isSalesCompany) ) { ?>
        /*생산처 화면 설정*/
        $('.navbar-nav.reform').find('li').each(function(idx){
            if( $(this).html().indexOf('영업관리') === -1 ){
                $(this).remove();
            }
            $('.list-inline').html('<li class="hover-btn cursor-pointer" style="font-size:15px; color:#fff; " onclick="goLogout()"><?=\Session::get('manager.managerNm')?>님</li>');
        });

        /*$('.panel-heading').each(function(index){
            if( $(this).html().indexOf('생산처') !== -1 ){
            }else{
                $(this).hide();
                $('.list-group').eq(index).hide();
                //console.log($(this).find());
            }
        });*/
        <?php } ?>

    });



</script>
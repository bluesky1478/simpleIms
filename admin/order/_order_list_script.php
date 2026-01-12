<!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/promise-polyfill/7.1.0/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.6/sweetalert2.all.min.js"></script>

<script type="text/javascript">

    //전일자 추가.
    let prevDateHtml = '<label class="btn btn-white btn-sm <?='1'===$search['searchperiod']?'active':''?>" ><input type="radio" name="searchperiod" value="1" >전일</label>';
    $('[class*=js-dateperiod]').find('label').eq(0).after(prevDateHtml);

    $(function(){
        let setPoliBackCommon = function(){
            if ($('input[name*=statusCheck]:checked').length < 1) {
                alert('설정할 주문을 선택해 주세요.');
                return -1;
            }
            let list = [];
            $('input[name*=statusCheck]:checked').each(function(){
                list.push( $(this).val() );
            });
            return list;
        }

        let setPoliBackAjax = function(orderNoList, type){
            let saveData = {
                mode : 'setDeliveryBoxType',
                orderNoList : orderNoList,
                type : type,
            }
            $.post('order_ajax.php', saveData, function (data) {
                if(data){
                    $.msg("처리되었습니다.", "", "success").then(()=>{
                        window.location.reload();
                    });
                }
            });
        }

        $('#setPoliBack').click(function(){
            let selectedOrder = setPoliBackCommon();
            if( -1 !== selectedOrder){
                setPoliBackAjax(selectedOrder, 1);
            }
        });
        $('#setPoliBackCancel').click(function(){
            let selectedOrder = setPoliBackCommon();
            if( -1 !== selectedOrder){
                setPoliBackAjax(selectedOrder, 0);
            }
        });
    });

</script>
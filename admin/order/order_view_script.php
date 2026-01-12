<script type="text/javascript">

    $(function(){

        //결제 추가 버튼 이벤트
        $('.js-paymentBtn').on('click',function(){
            var childNm = 'add_payments';
            var orderNo = $(this).data('orderno');
            var addParam = {
                mode: 'simple',
                layerTitle: '결제요청',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                orderNo: orderNo,
                autoPaymentSubject: '<?=$data['orderGoodsNm']?> 결제',
                autoReqPrice: '<?=$data['totalGoodsPrice'] + $data['totalDeilveryCharge'] - $estimateInfo['totalPayed'] ?>',
            };
            //console.log(addParam);
            layer_add_info(childNm, addParam);
        });

        $('.btn-sync-status').click(function(){
            const orderNo = $(this).data('orderno');
            $.post('./order_change_ps.php', {
                'mode' : 'refresh_order_status',
                'orderNo' : orderNo,
            }, function (data) {
                location.reload();
            });
        });

    });


    /**
     * * 주문상세창 노출
     *
     * @param string orderNo 주문 번호
     * @param string openType 상세창 노출 타입
     * @param boolean isProvider 공급사 유무
     * */
    function open_order_link(orderNo, openType, isProvider) {
        if (openType.length == 0 || openType == '' || typeof openType == 'undefined') {
            openType = 'newTab';
        }

        switch (openType) {
            case 'newTab' :
                //새로운 탭에서 열기
                open_order_link_tab(orderNo, openType, isProvider);
                break;
            case 'oneTab' :
                //하나의 탭에서 열기
                open_order_link_tab(orderNo, openType, isProvider);
                break;
            case 'newWindow' :
                //새로운 창에서 열기
                open_order_link_window(orderNo, openType, isProvider);
                break;
            case 'oneWindow' :
                //하나의 창에서 열기
                open_order_link_window(orderNo, openType, isProvider);
                break;
            default :
                open_order_link_tab(orderNo, 'newTab', isProvider);
        }
    }

    /**
     * 주문상세창 탭으로 노출
     *
     * @param string orderNo 주문 번호
     * @param string openType
     * @param boolean isProvider 공급사 유무
     * */
    function open_order_link_tab(orderNo, openType, isProvider) {
        var url = '/order/order_view.php?orderNo=' + orderNo;
        var tabName = 'orderTab';

        if (openType.length == 0 || openType == '' || typeof openType == 'undefined') {
            openType = 'newTab';
        }

        if (isProvider) {
            url = '/provider/order/order_view.php?orderNo=' + orderNo;
        }

        switch (openType) {
            case 'newTab' :
                var win = window.open(url, '');
                break;
            case 'oneTab' :
                var win = window.open(url, tabName);
                break;
            default :
                var win = window.open(url, '');
        }
        win.focus();
    }

</script>
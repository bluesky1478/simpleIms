<script type="text/javascript">
    var vueApp = null;
    /**
     * 클레임 
     * @returns {Promise<*>}
     */
    async function getClaimData(){
        //console.log('<?=$claimApiUrl?>');
        return await $.postAsync('<?=$claimApiUrl?>',{mode:'getScmClaimData', 'bdSno':'<?= $req['sno'] ?>'});
    }

    getClaimData().then(function(claimData){

        console.log(claimData);

        if( $.isEmpty(claimData.data.sno) ){
            $('.vue-contents').hide();
        }

        vueApp = new Vue({
            el: '#request-goods-info'
            , delimiters: ['{%', '%}']
            , data : {
                items : claimData.data.claimGoods,
                exchangeItems : claimData.data.exchangeGoods,
                refundData : claimData.data.refundData,
                claimData : {
                    claimNo : claimData.data.sno,
                    orderNo : claimData.data.orderNo,
                    orderStatusKr : claimData.data.orderStatusKr,
                    claimType : claimData.data.claimType,
                    claimTypeKr : claimData.data.claimTypeKr,
                    claimStatus : claimData.data.claimStatus,
                    claimStatusKr : claimData.data.claimStatusKr,
                    memo : claimData.data.memo,
                }
            }, methods : {
                setComplete : function() {
                    var appObject = this;
                    var sno = appObject.claimData.claimNo;
                    $.msgConfirm('배송전 단순교환으로 완료 처리함.','배송 전 처리 건 입니다.').then(function(result){
                        if( result.isConfirmed ){
                            $.postAsync('<?=$claimApiUrl?>',{mode:'setComplete', 'sno':sno}).then(function(afterClaimData){
                                $.msg('처리 되었습니다.', "", "success").then(()=>{
                                    location.reload();
                                });
                            });
                        }
                    });
                },
                setReject : function() {
                    var appObject = this;
                    var sno = appObject.claimData.claimNo;
                    $.msgConfirm('재고 없음 등의 사유로 교환불가 처리함.','').then(function(result){
                        if( result.isConfirmed ){
                            $.postAsync('<?=$claimApiUrl?>',{mode:'setReject', 'sno':sno}).then(function(afterClaimData){
                                $.msg('처리 되었습니다.', "", "success").then(()=>{
                                    location.reload();
                                });
                            });
                        }
                    });
                },
                regClaim : function() {
                    var appObject = this;
                    var sno = appObject.claimData.claimNo;
                    //console.log('req sno : ' + sno);
                    $.msgConfirm('클레임을 등록합니다.','클레임 등록 후 수정은 불가합니다.').then(function(result){
                        if( result.isConfirmed ){
                            $.postAsync('<?=$claimApiUrl?>',{mode:'regClaim', 'sno':sno}).then(function(afterClaimData){
                                $.msg('등록이 완료되었습니다.', "", "success").then(()=>{
                                    location.reload();
                                });
                            });
                        }
                    });
                },
                setChange : function() {
                    var appObject = this;
                    var sno = appObject.claimData.claimNo;
                    //console.log('req sno : ' + sno);
                    $.msgConfirm('출고전 주문변경을 진행 합니다.','').then(function(result){
                        if( result.isConfirmed ){
                            $.postAsync('<?=$claimApiUrl?>',{mode:'setChange', 'sno':sno}).then(function(afterClaimData){
                                $.msg('등록이 완료되었습니다.', "", "success").then(()=>{
                                    //location.reload();
                                });
                            });
                        }
                    });
                }
            }, computed : {
                getTotalCount : function(){
                    var items = this.items;
                    var totalCount = 0;
                    for(var idx in items){
                        var optionCount = 0;
                        for(var itemsIdx in items[idx].option){
                            optionCount += Number(items[idx].option[itemsIdx].optionCnt);
                        }
                        items[idx].optionTotalCount = optionCount;
                        totalCount += optionCount;
                    }
                    return (totalCount+'').number_format();
                },
                getTotalExchangeCount : function(){
                    var exchangeItems = this.exchangeItems;
                    var totalCount = 0;
                    for(var idx in exchangeItems){
                        var optionCount = 0;
                        for(var exItemsIdx in exchangeItems[idx].goodsOptionList){
                            optionCount += Number(exchangeItems[idx].goodsOptionList[exItemsIdx].optionCount);
                        }
                        exchangeItems[idx].optionTotalCount = optionCount;
                        totalCount += optionCount;
                    }
                    return (totalCount+'').number_format();
                }
            },mounted: function() {
                this.$nextTick(function () {
                    let claimType = $.isEmpty(vueApp.claimData.claimType) ? 'general': vueApp.claimData.claimType;
                    let openUrl = `<?=$frontUrl?>&claimType=${claimType}`;

                    $('.vue-contents').removeClass('display-none');
                    $('.vue-loader').addClass('display-none');

                    $('.btn-modify').click(function(){
                        window.open(openUrl,'admin-modify','width=1300,height=900');
                    });
                    $('.btn-modify_all').click(function(){
                        window.open(openUrl+'&adminModify=1','admin-modify','width=1300,height=900');
                    });
                });
            }
        });
    });

    $(function(){
        $(window).on('message', function (e) {
            if ( e.originalEvent.data == 'crossTest' ) {
                location.reload();
            }
        });
    });

</script>
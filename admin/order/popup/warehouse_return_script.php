<script type="text/javascript">

    /**
     * 함수 모음
     * @type {{loopAllItem: vueFunction.loopAllItem, getTotalSelectedCount: (function(): number)}}
     */
    var vueFunction = {
        save:function(){
            let params = vueApp.$data;
            params.mode = 'save3plReturn';
            params.scmName = $('#sel-company option:checked').text().replace(/^\s+/,"").replace(/\s+$/,"");
            $.postAsync('../../erp/erp_ps.php',params).then(function(data){
                alert('저장 되었습니다.');
                opener.location.reload();
                setTimeout(function(){
                    self.close();
                },1000);
            });

        }, modify:function(){
            var params = vueApp.$data;
            params.mode = 'modify_as';
            $.post('popup_claim_ps.php', params, function(json){
                vueApp.sno = json.sno;
                alert('수정 완료');
                opener.location.reload();
                setTimeout(function(){
                    self.close();
                },1000);
            });
        }, addItem:function(){
            vueApp.items.push(JSON.parse('{"prdCode":"", "prdName":"", "optionName":"", "prdCnt":"", "stockCnt":""}'));
        }, removeItem:function(index){
            vueApp.items.splice(index,1);
        }, getPrdName:function(item){
            //소문자를 대문자로.
            item.prdCode = item.prdCode.toUpperCase();
            if( item.prdCode.length > 6 ){
                $.postAsync('../../erp/erp_ps.php',{
                    mode:'getPrdInfo',
                    prdCode:item.prdCode,
                }).then(function(data){
                    if( null !== data.data.prdData ){
                        vueApp.scmNo = data.data.prdData.scmNo;
                        item.prdName = data.data.prdData.productName;
                        item.optionName = data.data.prdData.optionName;
                        item.stockCnt = data.data.prdData.stockCnt;
                    }
                });
            }
        }
    }

    console.log('<?=$claimData['prdInfo']?>');

    //VueJS App
    var vueApp = new Vue({
        el: '#return-app'
        , delimiters: ['{%', '%}']
        , data : {
            sno : '<?=$claimData['sno']?>',
            scmNo : '<?=$claimData['scmNo']?>',
            customerName : '<?=$claimData['customerName']?>',
            address : '<?=$claimData['address']?>',
            phone : '<?=$claimData['phone']?>',
            mobile : '<?=$claimData['mobile']?>',
            claimSno : '<?=$claimData['claimSno']?>',
            innoverMemo : decodeURIComponent('<?=$claimData['innoverMemo']?>'),
            invoiceNo : '<?=$claimData['invoiceNo']?>',
            items : JSON.parse('<?=$claimData['prdInfo']?>'),
        }, methods : {
            close:function(){
                self.close();
            },modify:function(){
                vueFunction.save();
            },save:function(){
                vueFunction.save();
            },addItem:function(){
                vueFunction.addItem();
            },removeItem:function(index){
                vueFunction.removeItem(index);
            },getPrdName:function(item){
                vueFunction.getPrdName(item);
            }
        }, filters : {
            numberFormat: function (value) {
                return ('' + value).number_format();
            }
        }
    });

    $(function(){

    });
</script>
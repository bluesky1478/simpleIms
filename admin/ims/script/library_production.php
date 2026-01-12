<script type="text/javascript">
    /**
     * 생산 관리 서비스
     */
    const ImsProductionService = {
        getListProduction : (page)=>{
            ImsRequestService.getList('production', page);
            if(typeof vueApp.productionCheckList != 'undefined'){
                $('#prdAllCheck').prop('checked',false);
                vueApp.productionCheckList = [];
            }
        },
        setScheduleCheck : (checkValue)=>{
            const selectedCount = vueApp.productionCheckList.length;
            if( 0 >= selectedCount ){
                $.msg('대상을 선택해주세요.','','warning');
                return false;
            }
            $.msgConfirm(selectedCount +'개의 스케쥴 체크상태를 변경하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('setScheduleCheck',{
                        checkValue : checkValue,
                        checkSnoList : vueApp.productionCheckList,
                    }).then(()=>{
                        ImsProductionService.getListProduction();
                    });
                }
            });
        },
        saveScheduleBatch : ()=>{
            $.msgConfirm('스케쥴을 일괄 수정하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('saveScheduleBatch',vueApp.scheduleModify).then((data)=>{
                        if( 200 === data.code ){
                            $.msg(data.message,'','success').then(()=>{
                                ImsProductionService.getListProduction();
                            });
                        }
                    });
                }
            });
        },
        setProduceStatusBatch : (checkValue)=>{
            const selectedCount = vueApp.productionCheckList.length;
            if( 0 >= selectedCount ){
                $.msg('대상을 선택해주세요.','','warning');
                return false;
            }
            const msgMap = {
                10 : `선택하신 ${selectedCount}개의 스케쥴의 재입력을 요청하시겠습니까?`,
                20 : `선택하신 ${selectedCount}개의 스케쥴 입력을 완료 하시겠습니까?`,
                30 : `선택하신 ${selectedCount}개의 스케쥴을 확정 하시겠습니까?`,
                99 : `선택하신 ${selectedCount}개의 생산을 완료 처리 하시겠습니까?`,
            };
            $.msgConfirm(msgMap[checkValue],'').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('setProduceStatusBatch',{
                        checkValue : checkValue,
                        checkSnoList : vueApp.productionCheckList,
                    }).then(()=>{
                        ImsProductionService.getListProduction();
                    });
                }
            });
        },
        setProduceStatus : (sno, status)=>{ //'0' => '10' => '20' => '30', => '99'
            const msgMap = {
                10 : '생산처에 스케쥴 입력을 요청하시겠습니까?',
                20 : '스케쥴을 입력을 완료하시겠습니까? ',
                30 : '스케쥴을 확정하시겠습니까? ',
            };

            $.msgConfirm(msgMap[status],'').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('setProduceStatus',{
                        sno : sno,
                        status : status,
                    }).then((data)=>{
                        if(200 === data.code){
                            ImsProductionService.getListProduction(1);
                            $.msg('처리 되었습니다.','','success');
                        }
                    });
                }
            });
        },
        assortType : function(typeOption){
            //console.log('타입체크 : ',typeOption);
            return (!$.isEmpty(typeOption) && typeOption.length > 0) ? true : false;
        },
        sizeOptionQtyTotal : function(el){
            let total = 0;
            for (const key in el.productionView.sizeOptionQty) {
                total += Number(el.productionView.sizeOptionQty[key]);
            }
            el.productionView.totalQty = total;
            return total;
        },
    }

</script>
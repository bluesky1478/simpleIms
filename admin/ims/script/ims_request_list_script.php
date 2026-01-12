<script type="text/javascript">

    $(appId).hide();

    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'],'')?>',
    };
    const defaultMultiKey2= {
        key : 'prd.productName',
        keyword : '',
    };

    $(()=>{
        const qbRequestDefault = {
            snoList : [],
            reqDeliveryInfo : '',
            makeNational : '',
            resMemo : '',
            reqStatus : '',
        }
        //검색 기본 시작 ---------------
        //const commonSearchDefault = ImsProductService.getSearchDefault();
        const qbSearchDefault = $.copyObject(ImsProductService.getSearchDefault({
            sort : 'D,desc',
            pageNum : 50,
            status : '2',
            reqFactory : '<?=empty($imsProduceCompany)?'':$managerSno?>',
            deadlineYn : 0,
            designManager : 'all',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
        }));
        const estimateSearchDefault = $.copyObject(ImsProductService.getSearchDefault({
            sort : 'D,desc',
            pageNum : 50,
            status : '2',
            reqFactory : '<?=empty($imsProduceCompany)?'':$managerSno?>',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
        }));
        const prdCostSearchDefault = $.copyObject(ImsProductService.getSearchDefault({
            sort : 'D,desc',
            pageNum : 50,
            status : '2',
            estimateType : '',
            reqFactory : '<?=empty($imsProduceCompany)?'':$managerSno?>',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
        }));
        //추가 되는게 있다면 별도 추가.
        //검색 기본 종료 ---------------

        const init = ()=>{
            const initParams = {
                data : {
                    product : [],
                    isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,
                    isList : true,
                    //tabMode : 'qb',  //'cost', // qb,  estimate
                    tabMode : '<?=$requestParam['tabMode']?>',  //'cost', // qb,  estimate

                    //QB리스트
                    qbList : [],
                    qbTotal : ImsProductService.getTotalPageDefault(),
                    qbPage : '',
                    qbRequest : $.copyObject(qbRequestDefault),
                    qbSearchCondition : $.copyObject(qbSearchDefault),

                    //가견적 리스트
                    estimateView : {
                        sno : -1
                    },
                    estimateList : [],
                    estimateTotal : ImsProductService.getTotalPageDefault(),
                    estimatePage : '',
                    estimateSearchCondition : $.copyObject(estimateSearchDefault),

                    //확정 견적 리스트
                    costView : {
                        sno : -1
                    },
                    costList : [],
                    costTotal : ImsProductService.getTotalPageDefault(),
                    costPage : '',
                    costSearchCondition : $.copyObject(prdCostSearchDefault),
                },
                mounted : (vueInstance)=>{
                    //QB List 갱신.
                    ImsRequestService.getListQb(1);
                    ImsRequestService.getListEstimate(1);
                    ImsRequestService.getListCost(1);

                    //NextThick
                    vueApp.$nextTick(function () {
                        console.log('mounted complete..');
                        if( 'cost' === vueApp.tabMode ){
                            $('title').html('생산견적 리스트');
                        }else{
                            $('title').html('QB리스트');
                        }
                    });

                },
                methods : {
                    changeTab : function(tabName){
                        vueApp.tabMode = tabName;
                    },
                    openRequestView : (reqStatus)=>{
                        const snoList = ImsService.getSelectSnoList('reqSno', '처리 대상을 체크해주세요.');
                        if( snoList.length > 0 ){
                            vueApp.qbRequest = $.copyObject(qbRequestDefault);
                            vueApp.qbRequest.reqStatus = reqStatus;
                            vueApp.qbRequest.snoList = snoList;
                            $('#modalRequestView').modal('show');
                        }
                    },
                    setCompleteQb : ()=>{
                        $.imsPost('setCompleteQb', vueApp.qbRequest).then((data) => {
                            if( 200 === data.code ){
                                //리스트 갱신
                                ImsRequestService.getListQb();
                                $.msg('처리되었습니다.','', 'success').then(()=>{
                                    $('#modalRequestView').modal('hide');
                                });
                            }
                        });
                    },
                    setRevokeQb : (reqStatus)=>{
                        const snoList = ImsService.getSelectSnoList('reqSno', '처리 대상을 체크해주세요.');
                        if( snoList.length > 0 ){
                            $.imsPost('setRevokeQb',{
                                snoList : snoList,
                                reqStatus : reqStatus,
                            }).then((data)=>{
                                if( 200 === data.code ){
                                    //리스트 갱신
                                    ImsRequestService.getListQb();
                                    $.msg('처리되었습니다.','', 'success').then(()=>{
                                        $('#modalRequestView').modal('hide');
                                    });
                                }
                            });
                        }
                    },
                    qbConditionReset : () => {
                        vueApp.qbSearchCondition = $.copyObject(qbSearchDefault);
                        ImsRequestService.getListQb(1);
                    },
                    searchQb : ImsRequestService.getListQb,
                    searchEstimate : ImsRequestService.getListEstimate,
                    estimateConditionReset : () => {
                        vueApp.estimateSearchCondition = $.copyObject(estimateSearchDefault);
                        ImsRequestService.getListEstimate(1);
                    },
                    searchCost : ImsRequestService.getListCost,
                    costConditionReset : () => {
                        vueApp.costSearchCondition = $.copyObject(prdCostSearchDefault);
                        ImsRequestService.getListCost(1);
                    },
                    getTabName : (tabValue)=>{
                        const tabNameMap = {
                            'qb' : '퀄리티&BT 요청',  
                            'estimate' : '가견적 요청',  
                            'cost' : '생산견적 요청',  
                            'produce' : '생산',  
                        };
                        
                        return tabNameMap[tabValue]; 
                    },
                    setDeadLine : ()=>{
                        //처리 완료 예정일 설정
                        const deadLineDt = $('#qb-dead-line').val();
                        if($.isEmpty(deadLineDt)){
                            $.msg('예정일을 입력해주세요.','','warning');
                            return false;
                        }

                        const snoList = ImsService.getSelectSnoList('reqSno', '처리 대상을 체크해주세요.');
                        if( snoList.length > 0 ){
                            $.imsPost('setQbDeadLine',{
                                snoList : snoList,
                                deadLineDt : deadLineDt,
                            }).then((data)=>{
                                if(200 === data.code){
                                    $.msg('처리완료 예정일이 등록되었습니다.','','success').then(()=>{
                                        ImsRequestService.getListQb();
                                    });
                                }
                            });
                        }
                    }
                },

            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }

        init();

        //Load Data And Init
        /*ImsService.getList(DATA_MAP.REQUEST,).then((data)=>{

        });*/
    });

    function refreshEstimateList(){
        ImsRequestService.getListEstimate(vueApp.estimateSearchCondition.page);
    }

</script>
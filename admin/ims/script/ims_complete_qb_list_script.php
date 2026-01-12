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
        const cqbRequestDefault = {
            snoList : [],
            reqDeliveryInfo : '',
            makeNational : '',
            resMemo : '',
        }
        //검색 기본 시작 ---------------
        const cqbSearchDefault = $.copyObject(ImsProductService.getSearchDefault({
            sort : 'C1,asc',
            pageNum : 50,
            reqFactory : '<?=empty($imsProduceCompany)?'':$managerSno?>',
            deadlineYn : 0,
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
                    tabMode : 'cqb',
                    //QB리스트
                    cqbList : [],
                    cqbTotal : ImsProductService.getTotalPageDefault(),
                    cqbPage : '',
                    cqbRequest : $.copyObject(cqbRequestDefault),
                    cqbSearchCondition : $.copyObject(cqbSearchDefault),
                },
                mounted : (vueInstance)=>{
                    //QB List 갱신.
                    ImsRequestService.getListCompleteBt(1);

                    //NextThick
                    vueApp.$nextTick(function () {
                        console.log('mounted complete..');
                    });

                },
                methods : {
                    cqbConditionReset : () => {
                        vueApp.cqbSearchCondition = $.copyObject(cqbSearchDefault);
                        ImsRequestService.getListCompleteBt(1);
                    },
                    searchCqb : ImsRequestService.getListCompleteBt,
                },

            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }
        init();
    });

</script>
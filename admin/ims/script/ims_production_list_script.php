<script type="text/javascript">

    $('title').html('생산스케쥴');
    
    $(appId).hide();
    let init = true;

    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'],'')?>',
    };
    const defaultMultiKey2= {
        key : 'prd.styleCode',
        keyword : '',
    };

    $(()=>{
        const requestDefault = {
            snoList : [],
            reqDeliveryInfo : '',
            makeNational : '',
            resMemo : '',
        }
        //검색 기본 시작 ---------------

        const commonSearchDefault = {
            key : '<?=gd_isset($requestParam['key'],'cust.customerName')?>',
            keyword : '<?=gd_isset($requestParam['keyword'],'')?>',
            key2 : 'prd.productName',
            keyword2 : '',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
            multiCondition : 'AND',
            scheduleCheck : 'all',
            year : '',
            season : '',
            isComplete : '',
            status : '4', //3 -> 4 -> 2
            page : 1,
            pageNum : 100,
            sort : '<?=5 == $requestParam['initStatus']?'C,desc':'C,asc'?>'
        }

        const searchDefault = $.copyObject(commonSearchDefault);

        <?php if( empty($requestParam['initStatus'])) { ?>
        searchDefault.isExcludeRtw = true;
        <?php } else { ?>
        searchDefault.isExcludeRtw = false;
        <?php } ?>

        <?php if( 4 == $requestParam['initStatus']) { ?>
        searchDefault.projectType = '4';
        <?php } else { ?>
        searchDefault.projectType = 'all';
        <?php } ?>

        searchDefault.isDelay = false;
        searchDefault.isDelayFirst = false;
        searchDefault.isComplete = false;
        searchDefault.productionStatus = <?=gd_isset($requestParam['initStatus'],4)?>;  //2-3-4
        searchDefault.produceCompanySno = '<?=!empty($imsProduceCompany)? $managerSno :''?>';
        searchDefault.packingYn = '0';
        searchDefault.use3pl = '0';
        searchDefault.useMall = '0';
        searchDefault.startDt = '';
        searchDefault.endDt = '';
        searchDefault.searchDateType = '';
        searchDefault.deliveryStatus = '';
        searchDefault.delayStatus = '';

        delete searchDefault.status;

        //추가 되는게 있다면 별도 추가.
        //검색 기본 종료 ---------------

        const init = ()=>{
            /*console.log($.remainDate('2023-11-04'));
            console.log($.remainDate('2024-02-03'));
            console.log($.remainDate('2024-02-04'));
            console.log($.remainDate('2024-02-05'));
            console.log($.remainDate('2024-12-05'));*/

            /*const productionViewDefault = $.copyObject(data.data.viewDefaultProduction);
            productionViewDefault.customerName = data.data.customer.customerName;
            productionViewDefault.styleCode = data.data.product.styleCode;
            productionViewDefault.styleFullName = data.data.product.styleFullName;*/

            const titleMap = {
                0: '전체',
                4: '스케쥴관리',
                2: '스케쥴입력요청',
                3: '스케쥴확정대기',
                5: '생산완료',
                1: '생산준비(미발주)',
            };

            const initParams = {
                data : {
                    product : [],
                    isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,
                    isList : true,
                    tabMode : 'production',  //'cost', // qb,  estimate

                    //생산 리스트
                    productionCheckList : [],
                    productionList : [],
                    productionTotal : ImsProductService.getTotalPageDefault(),
                    productionPage : '',
                    productionRequest : $.copyObject(requestDefault),
                    productionSearchCondition : $.copyObject(searchDefault),
                    viewModeProduction : 'v',
                    productionView : null,
                    scheduleModify : {},
                },
                mounted : (vueInstance)=>{
                    //NextThick
                    vueApp.$nextTick(function () {
                        //production List 갱신.
                        ImsService.getSchema('productionByStyleSno',{styleSno : 0}).then((data)=>{
                            if(200 === data.code){
                                vueApp.productionView = data.data;
                                ImsProductionService.getListProduction(1);
                            }
                        });

                        const setAffix = function(){
                            if ($(document).scrollTop() > 360) {
                                $('#affix-show-type2').show();
                                $('#affix-show-type1').hide();
                            }else{
                                $('#affix-show-type1').show();
                                $('#affix-show-type2').hide();
                            }
                        }

                        $(window).resize(function (e) {
                            setAffix();
                        });
                        $(window).scroll(setAffix);

                        $('#gnbAnchor').prepend ('<div class="float-side-menu cursor-pointer hover-btn" style="color:#fff; background-color:#666 " data-type="" onclick="vueApp.searchProduction()">검색</div>');

                        console.log('mounted complete..');
                    });
                },
                methods : {
                    listDownload : (type)=>{
                        //Not Ajax.
                        const downloadSearchCondition = $.copyObject( vueApp.productionSearchCondition );
                        downloadSearchCondition.pageNum = 15000;
                        downloadSearchCondition.listType = type;
                        location.href='ims_production_list.php?simple_excel_download=1&' + $.objectToQueryString(downloadSearchCondition);
                    },
                    openRequestView : ()=>{
                        const snoList = ImsService.getSelectSnoList('reqSno', '처리 대상을 체크해주세요.');
                        if( snoList.length > 0 ){
                            vueApp.productionRequest = $.copyObject(requestDefault);
                            vueApp.productionRequest.snoList = snoList;
                            $('#modalRequestView').modal('show');
                        }
                    },
                    productionConditionReset : () => {
                        const currentSearch = $.copyObject(searchDefault);
                        currentSearch.keyword = '';
                        vueApp.productionSearchCondition = currentSearch;
                        ImsProductionService.getListProduction(1);
                    },
                    searchProduction : ImsProductionService.getListProduction,
                    toggleAllCheck : ()=>{
                        if (vueApp.productionCheckList.length === vueApp.productionList.length) {
                            // 모든 항목이 선택되어 있으면 선택 해제
                            vueApp.productionCheckList = [];
                        } else {
                            // 그렇지 않으면 모든 항목 선택
                            vueApp.productionCheckList = vueApp.productionList.map(production => production.sno);
                        }
                    },
                    toggleCustomerCheck : (targetPrd)=>{
                        let isEndSno = '';
                        vueApp.productionList.forEach((prd)=>{
                            if( $.isEmpty(isEndSno) && targetPrd.sno === prd.sno ){ //시작번호
                                //vueApp.productionCheckList
                                isEndSno = prd.customerSno;
                            }
                            if( isEndSno === prd.customerSno && !vueApp.productionCheckList.includes(prd.sno) ){
                                vueApp.productionCheckList.push(prd.sno); //체크 넣기.
                            }
                            if(!$.isEmpty(isEndSno) && isEndSno !== prd.customerSno  ){
                                return false;
                            }
                        });
                    },
                    toggleProjectCheck : (targetPrd)=>{
                        let isEndSno = '';
                        vueApp.productionList.forEach((prd)=>{
                            if( $.isEmpty(isEndSno) && targetPrd.sno === prd.sno ){ //시작번호
                                //vueApp.productionCheckList
                                isEndSno = prd.projectSno;
                            }
                            if( isEndSno === prd.projectSno && !vueApp.productionCheckList.includes(prd.sno) ){
                                vueApp.productionCheckList.push(prd.sno); //체크 넣기.
                            }
                            if(!$.isEmpty(isEndSno) && isEndSno !== prd.projectSno  ){
                                return false;
                            }
                        });
                    },
                    openModifySchedule : function(){
                        if( vueApp.productionCheckList.length > 0 ){
                            vueApp.scheduleModify = $.copyObject(vueApp.productionList.find(obj => obj.sno === vueApp.productionCheckList[0]));
                            vueApp.scheduleModify.checkCnt = vueApp.productionCheckList.length;
                            vueApp.scheduleModify.checkList = vueApp.productionCheckList;
                            //console.log('수정대상', vueApp.scheduleModify);
                            $('#modalScheduleModify').modal('show');
                        }else{
                            $.msg('일괄 수정 대상을 선택해주세요.','','warning');
                        }
                    },
                    exportToExcel : function() {
                        /*deprecated*/
                    },
                },
                computed : {
                    sizeOptionQtyTotal() {
                        //생산준비
                        return ImsProductionService.sizeOptionQtyTotal(this);
                    }
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }

        init();

    });

    function refreshEstimateList(){
        ImsRequestService.getListEstimate(vueApp.estimateSearchCondition.page);
    }
    function refreshProductionList(){
        ImsProductionService.getListProduction();
    }

</script>
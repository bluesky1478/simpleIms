<script type="text/javascript">

    const SALES_LIST_CONDITION_MAP = {
        'wait' : ['10'], //대기
        'plan' : ['15'], //사전
        'proc' : ['20','30','31','40','41','50','60'], //진행
        'hold' : ['97','98'], //진행
    };

    /**
     * IMS 리스트 처리 서비스
     */
    class ImsListService { //= vue / 별도.

        refreshFnc = ()=>{};
        refreshAfterFnc = ()=>{};
        searchDefault = null;
        searchCondition = null;
        listPrefix = null;

        constructor(listPrefix, searchDefault, refreshFnc, refreshAfterFnc) {
            this.listPrefix = listPrefix;
            this.searchDefault = searchDefault;
            <?php if('y' === $imsPageReload) { ?>
            try{
                this.searchCondition = $.copyObject(JSON.parse($.cookie(listPrefix+'ImsSearchCondition')));
                console.log('확인', this.searchCondition);
            }catch (e){
                this.searchCondition = $.copyObject(searchDefault);//오류시 초기화
                $.removeCookie(listPrefix+'ImsSearchCondition');
            }
            <?php }else{ ?>
                this.searchCondition = $.copyObject(searchDefault);
            <?php } ?>

            this.refreshFnc = refreshFnc;
            if(typeof refreshAfterFnc != 'undefined'){
                this.refreshAfterFnc = refreshAfterFnc;
            }
        };

        //리스트 갱신
        refreshList(page){
            //console.log('refresh click', vueApp.searchCondition.sort);
            const refreshAfterFnc = this.refreshAfterFnc;
            if( typeof page != 'undefined' ){
                //console.log('페이지가 변경', page);
                vueApp.searchCondition.page = page; //page 변경
            }
            this.refreshFnc(vueApp.searchCondition, this.listPrefix).then((data)=>{
                console.log( 'Prefix.', this.listPrefix );
                console.log( 'Prefix.', vueApp.searchCondition );

                const listService = this;
                const pagePrefix = this.listPrefix;

                $.imsPostAfter(data, (data)=>{
                    console.log('조회 데이터',data);
                    console.log('조회 리스트',data.list);

                    vueApp.searchData = data; //전체 데이터
                    vueApp.listTotal = data.page;
                    vueApp.listData  = data.list;
                    vueApp.pageHtml  = data.pageEx;

                    //Paging Event
                    vueApp.$nextTick(function () {
                        $('#'+pagePrefix+'-page .pagination').find('a').each(function(){
                            $(this).off('click').on('click',function(){
                                listService.refreshList($(this).data('page'));
                            });
                        });

                        //검색값을 저장.
                        $.cookie(pagePrefix+'ImsSearchCondition', JSON.stringify(vueApp.searchCondition));
                        refreshAfterFnc();
                    });
                });
            });
        };

        //리스트 초기화
        init(serviceData){
            const initParams = {
                data : {
                    searchCondition : this.searchCondition,
                    searchData : null,
                    pageHtml  : '',
                    listTotal : {
                        idx : 0 ,
                        recode : {
                            total : 0
                        }
                    },
                    listData  : [],
                    projectCheckList : [],
                    listAllCheck : false,
                },
                methods : {
                    //refresh list
                    refreshList : async ( page )=>{
                        this.refreshList( page );
                    },
                    conditionResetNotRefresh : ()=>{
                        vueApp.searchCondition = $.copyObject(this.searchDefault);
                    },
                    conditionReset : ()=>{
                        vueApp.conditionResetNotRefresh();
                        this.refreshList(1);
                    },
                    /** 영업 리스트 초기화 */
                    salesConditionResetNotRefresh:()=>{
                        const currentTabMode = vueApp.listTabMode;
                        vueApp.searchCondition = $.copyObject(this.searchDefault);
                        vueApp.listTabMode = currentTabMode;
                        if('hold'===currentTabMode){
                            vueApp.searchCondition.orderProgressChk = SALES_LIST_CONDITION_MAP[currentTabMode];
                            delete vueApp.searchCondition.excludeStatus;
                        }else{
                            vueApp.searchCondition.orderProgressChk = SALES_LIST_CONDITION_MAP[currentTabMode];
                        }
                    },
                    salesConditionReset:()=>{
                        vueApp.salesConditionResetNotRefresh();
                        this.refreshList(1);
                    },
                    addMultiKey : ()=>{
                        vueApp.searchCondition.multiKey.push($.copyObject(listSearchDefaultData.multiKey[0]));
                    },
                    toggleAllCheck : ()=>{
                        if( vueApp.listAllCheck ){
                            vueApp.projectCheckList = [];
                        }else{
                            vueApp.projectCheckList = vueApp.listData.map(project => project.sno);
                        }
                    },
                }
            };

            if(!$.isEmpty(serviceData.serviceValue)){ //data value
                for(const key in serviceData.serviceValue){
                    initParams.data[key] = serviceData.serviceValue[key];
                }
            }
            if(!$.isEmpty(serviceData.serviceMethods)){ //Methods
                initParams.methods = Object.assign(initParams.methods, serviceData.serviceMethods);
            }
            if(!$.isEmpty(serviceData.serviceMounted)){ //Mounted
                initParams.mounted = serviceData.serviceMounted;
            }
            if(!$.isEmpty(serviceData.serviceComputed)){ //Computed
                initParams.computed = serviceData.serviceComputed;
            }

            vueApp = ImsService.initVueApp(appId, initParams);
            //console.log('initVueApp 이전');
            this.refreshList(1);
        };
    }

</script>
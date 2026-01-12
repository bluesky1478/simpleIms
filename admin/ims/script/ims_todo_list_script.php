<script type="text/javascript">
    $(appId).hide();
    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'],'a.subject')?>',
        keyword : '<?=gd_isset($requestParam['keyword'],'')?>',
    };
    const defaultMultiKey2= {
        key : 'cust.customerName',
        keyword : '',
    };
    const searchCommon = {
        sort : 'T1,asc',
        pageNum : 100,
        multiKey : [
            $.copyObject(defaultMultiKey1),
        ],
        searchDateType : 'a.regDt',
        startDt : '',
        endDt : '',
        todoType : 'todo',
    };

    $(()=>{
        const todoRequestSearchDefault = $.copyObject(ImsProductService.getSearchDefault($.copyObject(searchCommon)));
        todoRequestSearchDefault.sort = 'D,desc';
        todoRequestSearchDefault.status = 'ready';
        todoRequestSearchDefault.managerSno = '';
        todoRequestSearchDefault.reqManagerSno = '<?=$managerSno?>';

        const todoResponseSearchDefault = $.copyObject(ImsProductService.getSearchDefault($.copyObject(searchCommon)));
        todoResponseSearchDefault.status = 'ready';
        todoResponseSearchDefault.respManagerSno = '<?=$managerSno?>';
        todoResponseSearchDefault.teamSno = '<?=$teamSno?>';
        todoResponseSearchDefault.reqManagerSno = '';
        //검색 기본 종료 ---------------

        const init = ()=>{
            const initParams = {
                data : {
                    isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,
                    isList : true,
                    tabMode : '<?=$requestParam['tabMode']?>',  //request, response, payment

                    //요청 리스트
                    todoRequestList : [],
                    todoRequestTotal : ImsProductService.getTotalPageDefault(),
                    todoRequestPage : '',
                    todoRequestSearchCondition : $.copyObject(todoRequestSearchDefault),
                    //받은 리스트
                    todoResponseList : [],
                    todoResponseTotal : ImsProductService.getTotalPageDefault(),
                    todoResponsePage : '',
                    todoResponseSearchCondition : $.copyObject(todoResponseSearchDefault),
                },
                mounted : (vueInstance)=>{
                    //List 갱신.
                    ImsTodoService.getListTodoRequest(1);
                    ImsTodoService.getListTodoResponse(1);

                    //NextThick
                    vueApp.$nextTick(function () {
                        console.log('mounted complete..');
                    });
                },
                methods : {
                    /**
                     * 검색
                     */
                    searchTodoRequest : ImsTodoService.getListTodoRequest,
                    searchTodoResponse : ImsTodoService.getListTodoResponse,
                    /**
                     * 탭변경
                     * @param tabName
                     */
                    changeTab : function(tabName){
                        console.log(tabName);
                        vueApp.tabMode = tabName;
                    },
                    /**
                     * 초기화
                     */
                    todoRequestConditionReset : () => {
                        vueApp.todoRequestSearchCondition = $.copyObject(todoRequestSearchDefault);
                        ImsTodoService.getListTodoRequest(1);
                    },
                    todoResponseConditionReset : () => {
                        vueApp.todoResponseSearchCondition = $.copyObject(todoResponseSearchDefault);
                        ImsTodoService.getListTodoResponse(1);
                    },
                    /**
                     * 탭 이름 반환
                     * @param tabValue
                     * @returns {*}
                     */
                    getTabName : (tabValue)=>{
                        const tabNameMap = {
                            'approval' : '결재관리',  
                            'request'  : '나의요청',  
                            'inbox'    : '받은요청',  
                        };
                        return tabNameMap[tabValue];
                    },

                    /**
                     * 상태변경 일괄처리
                     */
                    setResStatusBatch : ()=>{
                        const resSnoList = getResCheckList();
                        if( 0 >= resSnoList.length ){
                            $.msg('변경하실 데이터를 선택해주세요!','','warning');
                        }else{
                            ImsTodoService.setTodoStatus(resSnoList,'complete');
                        }
                    },

                    /**
                     * 예정일 일괄 등록
                     */
                    saveExpectedDateBatch : ()=>{
                        const resSnoList = getResCheckList();
                        const expectedDt = $('#res-expected-dt').val();
                        ImsTodoService.saveExpectedDate(resSnoList,expectedDt);
                    }

                },

            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }
        init();
    });

    function getResCheckList(){
        const resSnoList = [];
        $('.res-list-check').each(function(){
            if( $(this).is(':checked') ){
                resSnoList.push($(this).val());
            }
        });
        return resSnoList;
    }

    function refreshTodoRequestList(){
        ImsTodoService.getListTodoRequest(vueApp.todoRequestSearchCondition.page);
    }
    function refreshTodoResponseList(){
        ImsTodoService.getListTodoResponse(vueApp.todoRequestSearchCondition.page);
    }

</script>
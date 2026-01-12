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
        sort : 'D,desc',
        pageNum : 100,
        multiKey : [
            $.copyObject(defaultMultiKey1),
        ],
        searchDateType : 'a.regDt',
        startDt : '',
        endDt : '',
        todoType : 'approval',
        approvalStatus : 'proc',
    };

    $(()=>{

        const todoApprovalSearchDefault = $.copyObject(ImsProductService.getSearchDefault($.copyObject(searchCommon)));
        todoApprovalSearchDefault.managerSno = '';
        //todoApprovalSearchDefault.reqManagerSno = '<?=$managerSno?>';
        todoApprovalSearchDefault.approvalManagerSno = '<?=$managerSno?>';

        //검색 기본 종료 ---------------

        const init = ()=>{
            const initParams = {
                data : {
                    isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,
                    isList : true,
                    tabMode : '<?=$requestParam['tabMode']?>',  //request, response, payment

                    //요청 리스트
                    todoApprovalList : [],
                    todoApprovalTotal : ImsProductService.getTotalPageDefault(),
                    todoApprovalPage : '',
                    todoApprovalSearchCondition : $.copyObject(todoApprovalSearchDefault),
                },
                mounted : (vueInstance)=>{
                    //List 갱신.
                    ImsTodoService.getListTodoApproval(1);

                    //NextThick
                    vueApp.$nextTick(function () {
                        console.log('mounted complete..');
                    });
                },
                methods : {
                    /**
                     * 검색
                     */
                    searchTodoApproval : ImsTodoService.getListTodoApproval,
                    /**
                     * 탭변경
                     * @param tabName
                     */
                    changeTab : function(tabName){
                        console.log(tabName);
                        vueApp.tabMode = tabName;
                        vueApp.todoApprovalSearchCondition.approvalStatus=tabName;
                        ImsTodoService.getListTodoApproval(1);
                    },
                    /**
                     * 초기화
                     */
                    todoApprovalConditionReset : () => {
                        vueApp.todoApprovalSearchCondition = $.copyObject(todoApprovalSearchDefault);
                        ImsTodoService.getListTodoApproval(1);
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

    function refreshTodoApprovalList(){
        ImsTodoService.getListTodoApproval(vueApp.todoApprovalSearchCondition.page);
    }

</script>
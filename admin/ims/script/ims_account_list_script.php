<script type="text/javascript">
    const mainListPrefix = 'account';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',
        sRadioSchIsBookRegistered : 'n',
        aChkboxSchProjectStatus : [90,91],
        sTextboxRangeStartSchCpProductionOrder : '',
        sTextboxRangeEndSchCpProductionOrder : '',
        page : 1,
        pageNum : 100,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListAccount';
        return ImsNkService.getList('account', params);
    };

    $(()=>{
        
        $('title').html('정산관리');
        
        const serviceData = {};
        ImsBoneService.setMethod(serviceData, {
            //회계 반영
            setBookRegistered :(ynFlag, type)=>{
                if(0>=vueApp.projectCheckList.length){
                    $.msg('선택된 프로젝트가 없습니다.','','warning');
                    return false;
                }
                $.imsPost2('setBookRegistered',{
                    'projectCheckList'   : vueApp.projectCheckList,
                    'isBookRegistered' : ynFlag,
                    'type' : type,
                },()=>{
                    $.msg('처리 완료','','success').then(()=>{
                        vueApp.projectCheckList = [];
                        vueApp.refreshList(vueApp.searchCondition.page);
                    });
                });
            },
            //엑셀 다운로드
            listDownload : (type)=>{
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href='ims_account_list.php?simple_excel_download='+type+'&' + queryString;
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
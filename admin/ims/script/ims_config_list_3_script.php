<script type="text/javascript">
    const mainListPrefix = 'sample_etc_cost';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.costName',
            keyword : '',
        }],
        multiCondition : 'OR',
        aChkboxSchCostType : [],
        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListSampleEtcCost';
        return ImsNkService.getList('sampleEtcCost', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            oUpsertForm : { sno:0 },
        });
        ImsBoneService.setMethod(serviceData, {
            openUpsertModal : (sno)=>{
                ImsNkService.getList('sampleEtcCost', {'upsertSnoGet':Number(sno)}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertForm = data.info;
                        $('#modalUpsert').modal('show');
                    });
                });
            },
            save : ()=>{
                if (vueApp.oUpsertForm.costName === null || vueApp.oUpsertForm.costName === '') {
                    $.msg('구분명을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertForm, 'table_number':2}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        vueApp.refreshList(vueApp.searchCondition.page);
                        $('#modalUpsert').modal('hide');
                        vueApp.isModify = false;

                        // $.msg('저장이 완료되었습니다.','','success').then(()=>{
                        //     vueApp.refreshList(vueApp.searchCondition.page);
                        //     $('#modalUpsert').modal('hide');
                        //     vueApp.isModify = false;
                        // });
                    });
                });
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>
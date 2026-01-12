<script type="text/javascript">
    const mainListPrefix = 'sample_room';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.factoryName',
            keyword : '',
        }],
        multiCondition : 'OR',
        aChkboxSumSchFactoryType : [],
        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListSampleRoom';
        return ImsNkService.getList('sampleRoom', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            oUpsertForm : { sno:0 },
        });
        ImsBoneService.setMethod(serviceData, {
            openUpsertModal : (sno)=>{
                ImsNkService.getList('sampleRoom', {'upsertSnoGet':Number(sno)}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertForm = data.info;
                        $('#modalUpsert').modal('show');
                    });
                });
            },
            save : ()=>{
                if (vueApp.oUpsertForm.factoryType === null || vueApp.oUpsertForm.factoryType === '') {
                    $.msg('타입을 선택하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertForm.factoryName === null || vueApp.oUpsertForm.factoryName === '') {
                    $.msg('이름을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertForm, 'table_number':3}).then((data)=>{
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
<script type="text/javascript">
    const mainListPrefix = 'busi_cate';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'parent.cateName',
            keyword : '',
        }],
        multiCondition : 'OR',

        page : 1,
        pageNum : 500,
        sort : 'BCN,asc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListBusiCate';
        let oPost = ImsNkService.getList('busiCate', params);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                vueApp.oParentCateList = data.parent_cate_list;
            });
        });
        return oPost;
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            oUpsertForm : { sno:0 },
            oParentCateList : {},
        });
        ImsBoneService.setMethod(serviceData, {
            openUpsertModal : (sno, iParentCateSno=0)=>{
                if (sno == 0) vueApp.isModify = true;
                else vueApp.isModify = false;
                ImsNkService.getList('busiCate', {'upsertSnoGet':Number(sno)}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertForm = data.info;
                        if (iParentCateSno > 0) {
                            vueApp.oUpsertForm.busiCateType = '세부업종';
                            vueApp.oUpsertForm.parentBusiCateSno = iParentCateSno;
                        }
                        $('#modalUpsert').modal('show');
                    });
                });
            },
            save : ()=>{
                if (vueApp.oUpsertForm.busiCateType === '세부업종' && vueApp.oUpsertForm.parentBusiCateSno == '0') {
                    $.msg('세부업종은 상위업종을 선택하셔야 합니다','','error');
                    return false;
                }
                if (vueApp.oUpsertForm.cateName === null || vueApp.oUpsertForm.cateName === '') {
                    $.msg('업종명을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertForm, 'table_number':8}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        if (vueApp.oUpsertForm.parentBusiCateSno == 0) {
                            location.reload();
                        } else {
                            vueApp.refreshList(vueApp.searchCondition.page);
                            $('#modalUpsert').modal('hide');
                            vueApp.isModify = false;
                        }
                    });
                });
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>
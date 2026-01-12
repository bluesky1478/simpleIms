<?php
    use Component\Ims\ImsDBName;
?>
<script type="text/javascript">
    const mainListPrefix = 'basic_size_spec';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.fitStyle',
            keyword : '',
        }],
        multiCondition : 'OR',
        aChkboxSchFitSeason : [],
        page : 1,
        pageNum : 100,
        sort : 'CF1,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListFitSpec';
        return ImsNkService.getList('fitSpec', params);
    };
    //자식 팝업창에서 실행
    function refreshList() {
        vueApp.refreshList(vueApp.searchCondition.page);
    }

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            oUpsertForm : { sno:0 },
            ooDefaultJson :{},
            sFocusTable : '',
            iFocusIdx : 0,
            iCopySno : 0,
            sCopyName : '',
        });
        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            //json 기본폼 가져오기
            $.imsPost('getJsonDefaultForm', {'data':'<?=ImsDBName::BASIC_SIZE_SPEC?>'}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.ooDefaultJson = data;
                });
            });
        });
        ImsBoneService.setMethod(serviceData, {
            openUpsertModal : (sno)=>{
                ImsNkService.getList('fitSpec', {'upsertSnoGet':Number(sno)}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertForm = data.info;
                        $('#modalUpsert').modal('show');
                    });
                });
            },
            openCopyModal : (sno, sName)=>{
                vueApp.iCopySno = sno;
                vueApp.sCopyName = sName+' (복사)';
                $('#modalCopy').modal('show');
            },
            save : ()=>{
                if (vueApp.oUpsertForm.fitStyle === null || vueApp.oUpsertForm.fitStyle === '') {
                    $.msg('스타일을 선택하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertForm.fitSeason === null || vueApp.oUpsertForm.fitSeason === '') {
                    $.msg('시즌을 선택하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertForm.fitSizeName === null || vueApp.oUpsertForm.fitSizeName === '') {
                    $.msg('양식명을 입력하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertForm.fitSize === null || vueApp.oUpsertForm.fitSize === '') {
                    $.msg('기준 사이즈를 입력하세요','','error');
                    return false;
                }

                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertForm, 'table_number':11}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        vueApp.refreshList(vueApp.searchCondition.page);
                        $('#modalUpsert').modal('hide');
                        vueApp.isModify = false;
                    });
                });
            },
            copy : ()=>{
                $.msgConfirm('해당 양식을 복사하시겠습니까?', '').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        $.imsPost('copyRecordSimple', { 'table_name':'<?=ImsDBName::BASIC_SIZE_SPEC?>',  'sno':vueApp.iCopySno, 'chg_val':{'fitSizeName':vueApp.sCopyName}}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                vueApp.refreshList(vueApp.searchCondition.page);
                                $('#modalCopy').modal('hide');
                            });
                        });
                    }
                });
            },
            deleteRow : (sno)=>{
                $.msgConfirm('정말 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                    if( result.isConfirmed ){
                        ImsNkService.setDelete('hhhhh', sno).then(()=>{
                            vueApp.refreshList(vueApp.searchCondition.page);
                        });
                    }
                });
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
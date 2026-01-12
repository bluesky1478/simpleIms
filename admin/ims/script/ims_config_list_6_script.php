<?php
use Component\Ims\ImsDBName;
?>
<script type="text/javascript">
    const mainListPrefix = 'fitting_check';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.fitStyle',
            keyword : '',
        }],
        multiCondition : 'OR',
        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListFittingCheck';
        return ImsNkService.getList('fittingCheck', params);
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
            $.imsPost('getJsonDefaultForm', {'data':'<?=ImsDBName::BASIC_FITTING_CHECK?>'}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.ooDefaultJson = data;
                });
            });
        });
        ImsBoneService.setMethod(serviceData, {
            openUpsertModal : (sno)=>{
                ImsNkService.getList('fittingCheck', {'upsertSnoGet':Number(sno)}).then((data)=> {
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
                //시즌, 스타일 값이 없으면 모든 샘플리뷰서 등록/수정시 모든 스타일에서 검색됨
                // if (vueApp.oUpsertForm.fitSeason === null || vueApp.oUpsertForm.fitSeason === '') {
                //     $.msg('시즌을 선택하세요','','error');
                //     return false;
                // }
                // if (vueApp.oUpsertForm.fitStyle === null || vueApp.oUpsertForm.fitStyle === '') {
                //     $.msg('스타일을 선택하세요','','error');
                //     return false;
                // }
                if (vueApp.oUpsertForm.fittingCheckName === null || vueApp.oUpsertForm.fittingCheckName === '') {
                    $.msg('양식명을 입력하세요','','error');
                    return false;
                }
                
                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertForm, 'table_number':10}).then((data)=>{
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
                        $.imsPost('copyRecordSimple', { 'table_name':'<?=ImsDBName::BASIC_FITTING_CHECK?>',  'sno':vueApp.iCopySno, 'chg_val':{'fittingCheckName':vueApp.sCopyName}}).then((data)=>{
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
                        ImsNkService.setDelete('ggggg', sno).then(()=>{
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
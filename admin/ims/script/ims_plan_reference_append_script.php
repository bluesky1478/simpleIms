<script type="text/javascript">
    const iInfoType = <?=$iInfoType?>;
    const mainListPrefix = 'ref_style_plan_append_'+iInfoType;
    const listSearchDefaultData = {
        multiKey : [{
            key : 'infoName',
            keyword : '',
        }],
        multiCondition : 'OR',
        sRadioSchInfoType : iInfoType,
        page : 1,
        pageNum : 255,
        sort : 'sortNum,asc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListRefStylePlanAppendInfo';
        return ImsNkService.getList('refStylePlanAppendInfo', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : true,
            bFlagRunSearch : false,
            iInfoType : iInfoType,
        });
        ImsBoneService.setMethod(serviceData, {

            save : ()=>{
                //검색하면 모든리스트가 나오는게 아니라서 순서update 안한다.
                $.each(vueApp.searchCondition.multiKey, function (key, val) {
                    if (this.keyword != '') {
                        vueApp.bFlagRunSearch = true;
                        return false;
                    }
                });
                //유효성 검사
                let bFlagErr = false;
                $.each(vueApp.listData, function (key, val) {
                    if (this.infoName == '') {
                        bFlagErr = true;
                        return false;
                    }
                });
                if (bFlagErr === true) {
                    $.msg('<?=$sAppendInfoName?>명을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setRefStylePlanAppendInfo', {'list':vueApp.listData, 'infoType':iInfoType, 'bFlagRunSearch':vueApp.bFlagRunSearch}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        let sAppendMsg = '';
                        if (vueApp.bFlagRunSearch === true) sAppendMsg = '검색 후 저장하시면 순서가 변경되지 않습니다.';
                        $.msg('저장 완료',sAppendMsg,'success').then(()=>{
                            vueApp.refreshList(vueApp.searchCondition.page);
                            //vueApp.isModify = false;
                        });
                    });
                });
            },
            deleteRow : (sno, iKey)=>{
                if (sno == '') {
                    vueApp.deleteElement(vueApp.listData, iKey);
                } else {
                    $.msgConfirm('정말 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                        if( result.isConfirmed ) {
                            $.imsPost('removeRefStylePlanAppendInfo', {'sno':sno}).then((data)=>{
                                $.imsPostAfter(data,(data)=>{
                                    if (data != '') {
                                        $.msg(data,'','error');
                                        return false;
                                    }
                                    vueApp.deleteElement(vueApp.listData, iKey);
                                });
                            });
                        }
                    });
                }
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
<script type="text/javascript">
    const mainListPrefix = 'material';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.name',
            keyword : '',
        }],
        multiCondition : 'OR',
        aChkboxSchMaterialType : [],
        aChkboxSchMakeNational : [],
        aChkboxSumSchUsedStyle : [],
        sTextboxRangeStartSchUnitPrice : '',
        sTextboxRangeEndSchUnitPrice : '',
        sRadioSchBtYn : 'all',
        aChkboxSchOnHandYn : [],
        'sExistOrNotSchTest.sno' : 'all',
        'sExistOrNotSchTest2.sno' : 'all',
        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);

        params.mode = 'getListMaterial';
        return ImsNkService.getList('material', params);
    };
    //자식 팝업창에서 실행
    function refreshList() {
        vueApp.refreshList(vueApp.searchCondition.page);
    }

    $(()=>{
        $('title').html('자재 정보 관리');

        const serviceData = {};

        ImsBoneService.setData(serviceData,{
            isExcelUpload : false,
            excelUploadType : 1,

            //유사퀄리티
            bFlagModifyGrp : false,
            oGroupInfo : { sno:0, grpName:'' },
            aoGroupItemList : [], //선택된(==수정예정)(or 등록하려는) 유사퀄리티의 자재리스트
            aGroupMaterialSno : [],
            aoGroupFlds : [],
            aoGroupList : [], //유사퀄리티(sl_imsMaterialGroup) 그룹리스트
        });
        ImsBoneService.setMethod(serviceData, {
            //엑셀 다운로드
            listDownload : (type)=>{
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href='ims_material_list.php?simple_excel_download='+type+'&' + queryString;
            },
            listUpload : ()=>{
                if (document.getElementsByName('excel')[0].value == '') {
                    $.msg('업로드할 엑셀파일을 첨부하세요','','error');
                    return false;
                }
                document.getElementsByName('frmRegistMaterialInfo')[0].submit();
            },

            //유사퀄리티
            openUpsertGrpLayer : (iSno)=>{
                vueApp.bFlagModifyGrp = true;
                vueApp.oGroupInfo.sno = iSno;
                vueApp.aoGroupItemList = [];
                vueApp.aGroupMaterialSno = [];
                if (iSno == 0) {
                    vueApp.oGroupInfo.grpName = '';
                } else {
                    ImsNkService.getList('materialGrp', { upsertSnoGet:iSno }).then((data)=> {
                        $.imsPostAfter(data, (data) => {
                            vueApp.oGroupInfo.grpName = data.info.grpName;
                            if (data.list_items.length > 0) vueApp.aoGroupItemList = data.list_items;
                            $('#modalListGrp').modal('hide');
                        });
                    });
                }
            },
            openListGrpModal : ()=>{
                ImsNkService.getList('materialGrp', {}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.aoGroupFlds = data.fieldData;
                        vueApp.aoGroupList = data.list;

                        $('#modalListGrp').modal('show');
                    });
                });
            },
            saveGroup : () =>{
                if (vueApp.oGroupInfo.grpName == '') {
                    $.msg('유사퀄리티 이름을 입력하세요.','','warning');
                    return false;
                }
                if (vueApp.oGroupInfo.sno == 0 && vueApp.aoGroupItemList.length == 0) {
                    $.msg('유사퀄리티에 소속시킬 자재를 선택하세요.','','warning');
                    return false;
                }
                $.each(vueApp.aoGroupItemList, function(key, val) {
                    vueApp.aGroupMaterialSno.push(this.sno);
                });

                $.imsPost('setMaterialGrp', {'data':vueApp.oGroupInfo, 'itemSnos':vueApp.aGroupMaterialSno}).then((data) => {
                    $.imsPostAfter(data,(data)=>{
                        vueApp.bFlagModifyGrp = false;
                        vueApp.refreshList(vueApp.searchCondition.page);
                    });
                });
            },
        });

        ImsBoneService.setMounted(serviceData,()=>{
            $('.bootstrap-filestyle').remove();
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //style , storedSearchCondition
        listService.init(serviceData);
    });

</script>
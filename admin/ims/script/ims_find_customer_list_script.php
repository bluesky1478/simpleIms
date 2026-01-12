<script type="text/javascript">
    const mainListPrefix = 'find_customer';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',

        aChkboxSchCustomerType : [],
        aChkboxSchBuyDiv : [],
        aChkboxSchBuyMethod : [],
        'sRadioSchCate1.sno' : 'all',
        sRadioSchBusiCateSno : 'all',
        sExistOrNotSchCustomerSno : 'all',
        selectSchDate : 1,
        sTextboxRangeStartSchContactDt : '',
        sTextboxRangeEndSchContactDt : '',
        sTextboxRangeStartSchRegDt : '',
        sTextboxRangeEndSchRegDt : '',

        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListFindCustomer';
        return ImsNkService.getList('findCustomer', params);
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
            oParentCateList : {},
            oCateList : {},
        });
        ImsBoneService.setMounted(serviceData, ()=>{
            if (vueApp.searchCondition.selectSchDate == undefined) vueApp.searchCondition.selectSchDate = 1;

            $.imsPost('getBusiCateListByDepth', {}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.oParentCateList = data.parent_cate_list;
                    vueApp.oCateList = data.cate_list;
                });
            });
        });
        ImsBoneService.setMethod(serviceData, {
            //일자 검색 selectbox 변경시
            changeSchDtType : (iVal)=>{
                if (iVal == 1) {
                    vueApp.searchCondition.sTextboxRangeStartSchContactDt = vueApp.searchCondition.sTextboxRangeStartSchRegDt;
                    vueApp.searchCondition.sTextboxRangeEndSchContactDt = vueApp.searchCondition.sTextboxRangeEndSchRegDt;
                    vueApp.searchCondition.sTextboxRangeStartSchRegDt = '';
                    vueApp.searchCondition.sTextboxRangeEndSchRegDt = '';
                } else {
                    vueApp.searchCondition.sTextboxRangeStartSchRegDt = vueApp.searchCondition.sTextboxRangeStartSchContactDt;
                    vueApp.searchCondition.sTextboxRangeEndSchRegDt = vueApp.searchCondition.sTextboxRangeEndSchContactDt;
                    vueApp.searchCondition.sTextboxRangeStartSchContactDt = '';
                    vueApp.searchCondition.sTextboxRangeEndSchContactDt = '';
                }
            },

            openUpsertModal : (sno)=>{
                if (sno == 0) vueApp.isModify = true;
                else vueApp.isModify = false;
                ImsNkService.getList('findCustomer', {'upsertSnoGet':Number(sno)}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertForm = data.info;
                        $('#modalUpsert').modal('show');
                    });
                });
            },
            save : ()=>{
                if (vueApp.oUpsertForm.customerName === null || vueApp.oUpsertForm.customerName === '') {
                    $.msg('업체명을 입력하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertForm.busiCateSno === null || vueApp.oUpsertForm.busiCateSno == '0') {
                    $.msg('세부업종명을 선택하세요','','error');
                    return false;
                }

                $.imsPost('setFindCustomer', {'data':vueApp.oUpsertForm}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        vueApp.refreshList(vueApp.searchCondition.page);
                        $('#modalUpsert').modal('hide');
                        vueApp.isModify = false;
                    });
                });
            },

            registProject : ()=>{
                let aSendParam = [];
                $.each(document.getElementsByName('saleCustomerSno[]'), function(key, val) {
                    if (this.checked === true) {
                        aSendParam.push(this.value);
                    }
                });
                if (aSendParam.length === 0) {
                    $.msg('프로젝트로 등록할 업체를 선택하세요','','error');
                    return false;
                }

                $.msgConfirm('체크하신 업체로 프로젝트를 등록하시겠습니까?','').then(function(result){
                    if( result.isConfirmed ){
                        $.imsPost('registProjectBySaleCustomer', {'saleCustomerSnos':aSendParam}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                if (data != '') {
                                    $.msg(data,'','error');
                                    return false;
                                } else {
                                    $.msg('고객과 프로젝트가 등록되었습니다.', "", "success").then(()=>{
                                        $.each(document.getElementsByName('saleCustomerSno[]'), function(key, val) {
                                            this.checked = false;
                                        });
                                        vueApp.refreshList(vueApp.searchCondition.page);
                                    });
                                }
                            });
                        });
                    }
                });
            },



            //엑셀 다운로드
            listDownload : (type)=>{
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href='ims_find_customer_list.php?simple_excel_download='+type+'&' + queryString;
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
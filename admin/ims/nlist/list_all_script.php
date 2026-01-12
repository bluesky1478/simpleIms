<script type="text/javascript">
    let listUpdateMulti = null;
    let listUpdateMultiOrigin = null;
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const mainListPrefix = 'all';

    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);
        let oPost = $.imsPost('getAllList', params);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                listUpdateMulti = data.listUpdateMulti;
                listUpdateMultiOrigin = data.listUpdateMultiOrigin;
            });
        });
        return oPost;
    };

    function refreshListExtFnc(){
        vueApp.refreshList(1);
    };

    $(()=>{

        listSearchDefaultData.orderProgressChk = [
            '20','30','31','40','41','50','60'
        ];

        listSearchDefaultData.sort = "P5,asc";

        listSearchDefaultData.delayStatus = [];
        listSearchDefaultData.bizPlanYn = 'all';
        listSearchDefaultData.designWorkType = 'all';
        listSearchDefaultData.pageNum = '100';
        listSearchDefaultData.isBookRegistered = '0';

        listSearchDefaultData.chkUseMall = false;
        listSearchDefaultData.chkUse3pl = false;
        listSearchDefaultData.chkPackingYn = false;
        listSearchDefaultData.directDeliveryYn = false;

        listSearchDefaultData.projectTypeChk = [];
        listSearchDefaultData.viewType = 'project';

        listSearchDefaultData.searchDateType = 'prj.regDt';
        listSearchDefaultData.startDt = '';
        listSearchDefaultData.endDt = '';
        listSearchDefaultData.year = '';
        listSearchDefaultData.season = '';

        const serviceData = {};
        //console.log('검색 기본 조건 : ',listSearchDefaultData );

        ImsBoneService.setData(serviceData,{
            visibleTodayTodo : true,
            isModify : false, //수정버튼 표시여부
        });

        ImsBoneService.setMethod(serviceData, {
            setProgress : (prjStatusList)=>{
                //vueApp.conditionReset();
                vueApp.searchCondition.orderProgressChk=prjStatusList;
                //console.log('set progress...');
                vueApp.refreshList(1);
            },

            //엑셀 다운로드
            listDownload : (type)=>{
                //Not Ajax.
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                // /console.log(queryString);
                location.href=`ims_list_all.php?simple_excel_download=${type}&` + queryString;
            },

            //리스트 일괄수정
            save : ()=>{
                //수정하지 않은 값은 update하지 않도록 배열정리
                let aSendData = ImsNkService.refineMultiUpdate(listUpdateMulti, listUpdateMultiOrigin, ['sno', 'styleSno']);
                if (aSendData.length === 0) {
                    $.msg('수정한 값이 없습니다','','error');
                    return false;
                }

                //일괄업데이트 처리(updateDesignMulti : sl_imsProject(customerDeliveryDt), sl_imsProjectExt update)
                $.imsPost('updateDesignMulti',{
                    'multi_data' : aSendData,
                }).then((data)=>{
                    if (data.code == 200) {
                        $.msg('저장 완료','','success').then(()=>{
                            vueApp.isModify = false;
                            listService.refreshList(vueApp.searchCondition.page);
                        });
                    } else {
                        alert('수정실패');
                    }
                });
            },
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            //프로젝트 등록 이벤트
            $('#btn-reg-project').click(()=>{
                $('#btn-reg-project-hide').click();
            });
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData, getListAfter); //style , styleSearchCondition
        listService.init(serviceData);
    });
</script>
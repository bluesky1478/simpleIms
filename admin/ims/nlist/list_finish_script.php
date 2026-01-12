<script type="text/javascript">
    let listUpdateMulti = null;
    let listUpdateMultiOrigin = null;

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const mainListPrefix = 'all';

    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);
        return $.imsPost('getCompleteList', params);
    };

    function refreshListExtFnc(){
        vueApp.refreshList(1);
    };

    $(()=>{
        listSearchDefaultData.orderProgressChk = ['91'];

        listSearchDefaultData.sort = "P5,asc";

        listSearchDefaultData.delayStatus = [];
        listSearchDefaultData.bizPlanYn = 'all';
        listSearchDefaultData.designWorkType = 'all';
        listSearchDefaultData.pageNum = '100';
        listSearchDefaultData.isBookRegistered = '0';

        listSearchDefaultData.chkUseMall = false;
        listSearchDefaultData.chkUse3pl = false;
        listSearchDefaultData.chkPackingYn = false;

        listSearchDefaultData.projectTypeChk = [];
        listSearchDefaultData.viewType = 'project';

        listSearchDefaultData.searchDateType = 'prj.regDt';
        listSearchDefaultData.startDt = '';
        listSearchDefaultData.endDt = '';
        listSearchDefaultData.year = '';
        listSearchDefaultData.season = '';

        const serviceData = {};

        ImsBoneService.setData(serviceData,{
            visibleTodayTodo : true,
            isModify : false, //수정버튼 표시여부
        });

        //console.log('검색 기본 조건 : ',listSearchDefaultData );
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
                location.href=`ims_style_admin.php?simple_excel_download=${type}&` + queryString;
            },
            // 회계 반영
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
                        refreshListExtFnc();
                    });
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
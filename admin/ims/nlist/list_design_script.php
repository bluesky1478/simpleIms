<?php use Component\Ims\ImsCodeMap; ?>
<script type="text/javascript">
    let listUpdateMulti = null;
    let listUpdateMultiOrigin = null; //수정했는지 비교하는 배열. vueApp.listData로 대체해도 되지만 listUpdateMultiOrigin이 없으면 date-picker textbox(예정일,완료일)에서 일자 없던것을 임의일자로 지정 -> 일자 없애면(x클릭) 수정되는 것으로 취급됨
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const mainListPrefix = 'design';

    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);
        let oPost = $.imsPost('getDesignList', params);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                listUpdateMulti = data.listUpdateMulti;
                listUpdateMultiOrigin = data.listUpdateMultiOrigin;
            });
        });
        return oPost;
    };

    $(()=>{

        listSearchDefaultData.orderProgressChk = [
            '20','30','31','40','41','50'
        ];

        listSearchDefaultData.sort = "P5,asc";
        listSearchDefaultData.delayStatus = [];
        listSearchDefaultData.projectTypeChk = [
            <?= implode(',',array_keys(ImsCodeMap::PROJECT_TYPE_N)) ?>
        ];
        listSearchDefaultData.bizPlanYn = 'all';
        listSearchDefaultData.designWorkType = 'all';
        listSearchDefaultData.pageNum = '100';
        listSearchDefaultData.salesManagerSno = 'all';
        listSearchDefaultData.designManagerSno = 'all';

        const serviceData = {};
        console.log('검색 기본 조건 : ',listSearchDefaultData );

        ImsBoneService.setData(serviceData,{
            visibleTodayTodo : true,
            isModify : false, //수정버튼 표시여부
        });

        ImsBoneService.setMethod(serviceData, {
            setProgress : (prjStatusList)=>{
                //vueApp.conditionReset();
                vueApp.searchCondition.orderProgressChk=prjStatusList;
                console.log('set progress...');
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

            //리스트 일괄수정
            save : ()=>{
                //수정하지 않은 값은 update하지 않도록 배열정리
                let aSendData = ImsNkService.refineMultiUpdate(listUpdateMulti, listUpdateMultiOrigin, ['sno']);
                if (aSendData.length === 0) {
                    $.msg('수정한 값이 없습니다','','error');
                    return false;
                }
                //일괄업데이트 처리
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
            setWait : (waitNumber) =>{
                vueApp.searchCondition = $.copyObject(listSearchDefaultData);
                if( 1 === waitNumber ){ //전체 확정 대기
                    vueApp.searchCondition.orderProgressChk = [31, 41];
                }else if( 2 === waitNumber ){ //제안서 대기
                    vueApp.searchCondition.orderProgressChk = [31];
                }else if( 3 === waitNumber ){//샘플 대기
                    vueApp.searchCondition.orderProgressChk = [41];
                }
                
                console.log('웨잇 체크', vueApp.searchCondition.orderProgressChk);
                
                vueApp.refreshList(1);
            },
            setSales : (sales) =>{
                vueApp.searchCondition = $.copyObject(listSearchDefaultData);
                vueApp.searchCondition.salesManagerSno = sales;
                vueApp.refreshList(1);
                //vueApp.searchCondition.salesManagerSno = 'all';
            },
            setDesigner : (designer) =>{
                /*vueApp.searchCondition = $.copyObject(listSearchDefaultData);
                vueApp.searchCondition.multiKey[0].key = 'desg.managerNm';
                vueApp.searchCondition.multiKey[0].keyword = designer;
                vueApp.refreshList(1);*/

                vueApp.searchCondition = $.copyObject(listSearchDefaultData);
                vueApp.searchCondition.designManagerSno = designer;
                vueApp.refreshList(1);
                //vueApp.searchCondition.designManagerSno = 'all';
            },

        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData, getListAfter); //style , styleSearchCondition
        listService.init(serviceData);
    });
</script>
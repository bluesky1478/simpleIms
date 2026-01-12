<script type="text/javascript">
    function namkuu(str) {
        console.log('namkuuuuuuuuu', str);
    }

    //체크박스 전체체크
    function toggleAllChkNk(sTargetChkBox, bChked) {
        $.each(document.getElementsByName(sTargetChkBox), function (key, val) {
            document.getElementsByName(sTargetChkBox)[key].checked = bChked;
        });
    }

    //textbox에서 화살표 or 엔터키 누르면 inputbox 이동(호출ex> gfnMoveInputBox(oPlanInfo.jsonFitSpec, key, event.key, $refs.inputOptionName))
    //사용페이지 : 스타일기획upsert, 샘플upsert(NEW)
    function gfnMoveInputBox(aoList, iCurrKey, keyNm, oTarget) {
        if (keyNm == 'ArrowUp' || keyNm == 'ArrowDown' || keyNm == 'Enter') {
            if (iCurrKey != 0 && keyNm == 'ArrowUp') {
                oTarget[iCurrKey-1].focus();
                return true;
            } else if (iCurrKey != aoList.length - 1 && (keyNm == 'ArrowDown' || keyNm == 'Enter')) {
                // oTarget[iCurrKey+1].focus();
                oTarget[iCurrKey+1].select();
                return true;
            } else {
                return false;
            }
        } else return false;
    }
    //환율 바꿨을때 환율기준일 수정(사용페이지에 vueApp 변수 4개 필요(sSaveDollerRatio,sSaveDollerRatioDt,sCurrDollerRatio,sCurrDollerRatioDt))
    //사용페이지 : 스타일기획upsert, 샘플upsert(NEW)
    function gfnChangeDollerRatioDt(oTarget) {
        if (Number(oTarget.dollerRatio) === Number(vueApp.sSaveDollerRatio)) oTarget.dollerRatioDt = vueApp.sSaveDollerRatioDt; //저장된 환율기준일 우선
        else if (Number(oTarget.dollerRatio) === Number(vueApp.sCurrDollerRatio)) oTarget.dollerRatioDt = vueApp.sCurrDollerRatioDt;
        else oTarget.dollerRatioDt = '0000-00-00';
    }

    const ImsNkService = {
        //html table tag내용을 엑셀파일로 다운로드. 현재는 쓰는 곳 없음
        excelDownload : (fileName, sId)=>{
            // HTML 테이블 가져오기
            const table = document.getElementById(sId);
            // 테이블 데이터를 엑셀 시트로 변환
            const workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

            //특정 필드를 안보여줌
            let sDeleteFld = '';
            // if (sId == 'excel_table_1') sDeleteFld = 'J';
            // else if (sId == 'excel_table_2') sDeleteFld = 'F';
            if (sDeleteFld != '') {
                for(let i=1; 1000>=i; i++){
                    try{
                        delete workbook.Sheets.Sheet1[sDeleteFld+i];
                    }catch (e){}
                }
            }

            // 엑셀 파일 다운로드
            XLSX.writeFile(workbook, `${fileName}.xlsx`);
        },

        //혼동하지 말아야 할 내용 : getList 함수만 실행하면 vueApp.searchData.fieldData, vueApp.listData 에 값 넣지 않음. frontend단의 중간중간 DB select용도로 막 써도 됨
        getList : async (target, condition) => {
            //console.log('getList condition ', condition);
            //backend url 변경($myHost/ims/ims_ps.php -> $imsAjaxUrl)
            //return await $.postAsync('<?php //=$myHost?>///ims/ims_ps.php', {
            return await $.postAsync('<?=$imsAjaxUrl?>', {
                mode:'getListNk',
                target:target,
                condition : condition
            });
        },
        setDelete : async (target, sno) => {
            //backend url 변경($myHost/ims/ims_ps.php -> $imsAjaxUrl)
            //return await $.postAsync('<?php //=$myHost?>///ims/ims_ps.php', {
            return await $.postAsync('<?=$imsAjaxUrl?>', {
                mode:'hardDeleteNk',
                target:target,
                sno : sno
            });
        },
        //일괄수정시 수정하지 않은 값은 update하지 않도록 배열정리
        refineMultiUpdate : (listUpdateMulti, listUpdateMultiOrigin, aFixedKeyNm) => {
            let aSendData = [];
            let bFlagUpdate = false;
            let iIdx = 0;
            $.each(listUpdateMulti, function(key, val) {
                bFlagUpdate = false;
                $.each(val, function(key2, val2) {
                    if (!aFixedKeyNm.includes(key2) && listUpdateMulti[key][key2] != listUpdateMultiOrigin[key][key2]) {
                        bFlagUpdate = true;
                        return false;
                    }
                });
                if (bFlagUpdate === true) {
                    aSendData[iIdx] = {};
                    $.each(val, function(key2, val2) {
                        if (!aFixedKeyNm.includes(key2)) {
                            if (listUpdateMulti[key][key2] != listUpdateMultiOrigin[key][key2]) {
                                aSendData[iIdx][key2] = val2;
                            }
                        } else {
                            aSendData[iIdx][key2] = val2;
                        }
                    });
                    iIdx++;
                }
            });
            return aSendData;
        },
        //스타일팝업창에서 스타일기획리스트 가져오기
        getStylePlanListRefresh : (styleSno=0, projectSno=0, sCallback='') => {
            ImsNkService.getList('stylePlan', {'sno':styleSno,'projectSno':projectSno}).then((data)=>{
                if(200 === data.code){
                    vueApp.stylePlanList = data.data.list;
                    if (sCallback != '') {
                        eval(sCallback)();
                    }
                }
            });
        },
        changeSortStylePlanList : () => {
            let aSnoList = [];
            $.each(vueApp.stylePlanList, function (key, val) {
                aSnoList.push(val.sno);
            });
            $.imsPost('updateSortStylePlan', {'data' : aSnoList});
        },
        //기획확정 처리(status > 0) or 확정기획 없음처리(status == -1)
        setStylePlanConfirm : (styleSno, projectSno, status)=>{
            let msg = '해당 스타일은 확정 기획없이 진행합니까?';
            if(-1 !== status){
                msg = '기획을 확정 하시겠습니까?';
            }
            $.msgConfirm(msg, '').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    $.imsPost('setSampleNothing', {
                        sno:styleSno,
                        projectSno:projectSno,
                        planConfirmSno:status,
                    }).then((data) => {
                        if( 200 === data.code ){
                            location.reload();
                            $.msg('처리되었습니다.','','success');
                        }
                    });
                }
            });
        },

    };
    //원부자재 관련
    const StoredServiceNk = {
        //고객상세팝업에서 해당 고객의 원부자재 리스트 가져오기
        getListOfCustom : ()=>{
            ImsNkService.getList('storedOfCustom',vueApp.storedListSearchCondition).then((data)=>{
                if(200 === data.code){
                    vueApp.storedList = data.data.list;
                    vueApp.storedPage = data.data.pageEx;
                    vueApp.storedTotal = data.data.page;
                    //console.log('~~stored리스트',vueApp.storedList);
                }
            });
        },
    };
    //product샘플 관련
    const CustSampleServiceNk = {
        //고객상세팝업에서 해당 고객의 샘플 리스트 가져오기
        getListOfCustom : ()=>{
            ImsNkService.getList('productSample',vueApp.sampleListSearchCondition).then((data)=>{
                if(200 === data.code){
                    vueApp.sampleList = data.data.list;
                    vueApp.samplePage = data.data.pageEx;
                    vueApp.sampleTotal = data.data.page;
                }
            });
        },
    };
    //고객견적 관련
    const CustEstimateServiceNk = {
        //고객상세팝업에서 해당 고객의 견적 리스트 가져오기
        getListOfCustom : ()=>{
            ImsNkService.getList('customerEstimateNk',vueApp.estimateListSearchCondition).then((data)=>{
                if(200 === data.code){
                    vueApp.estimateList = data.data.list;
                    vueApp.estimatePage = data.data.pageEx;
                    vueApp.estimateTotal = data.data.page;
                }
            });
        },
    };
</script>

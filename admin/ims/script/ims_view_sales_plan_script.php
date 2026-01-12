<script type="text/javascript">
    const allScheduleMap = JSON.parse('<?=$allScheduleMap?>');
    const sno = '<?=gd_isset($requestParam['sno'],$requestParam['projectSno'])?>';
    let iBasicSalesPlanSno = 0;

    $(()=>{
        ImsNkService.getList('basicFormToSalesPlanPage', {'projectSno':sno}).then((data)=>{
            $.imsPostAfter(data, (data)=> {
                $.each(data.basic_form_list, function (key, val) {
                    iBasicSalesPlanSno = key;
                    return false;
                });

                //답변 배열, obj 구성하기
                //namkuuuuu 후순위작업. 영업기획서양식을 변경시 : iBasicSalesPlanSno 값 넣기, 아래 내용 실행해서 vueApp.aoFillDatail 재구성 시켜야함
                let aoFillDatail = [];
                let sFillVal = '';
                $.each(data.basic_form_list[iBasicSalesPlanSno].jsonBasicFormContents, function (key, val) {
                    aoFillDatail.push({'grpType':val.grpType, 'questions':[]});
                    $.each(val.questions, function (key2, val2) {
                        aoFillDatail[key].questions.push({'cells':[]});
                        $.each(val2.cells, function (key3, val3) {
                            //해당 프로젝트에 영업기획서 이미 등록한 경우 값 가져옴
                            if (data.fill_detail[val.grpTitle] != undefined && data.fill_detail[val.grpTitle][val2.cells[0].cellValue] != undefined && data.fill_detail[val.grpTitle][val2.cells[0].cellValue][val3.cellTitle] != undefined) sFillVal = data.fill_detail[val.grpTitle][val2.cells[0].cellValue][val3.cellTitle];
                            else sFillVal = '';

                            aoFillDatail[key].questions[key2].cells.push({'cellType':val3.cellType, 'textGroup':val.grpTitle, 'textQuestion':val2.cells[0].cellValue, 'textCell':val3.cellTitle, 'cellValue':sFillVal});
                        });
                    });
                });
                let ooFillJson = {};
                $.each(data.basic_form_list[iBasicSalesPlanSno].jsonBasicFormContents, function (key, val) {
                    if (val.grpType == 'json') {
                        //해당 프로젝트에 영업기획서 이미 등록한 경우 값 가져옴
                        if (data.fill_json[val.grpTitle] != undefined) {
                            ooFillJson[key] = { 'textGroup':val.grpTitle, 'jsonValue':data.fill_json[val.grpTitle] };
                        } else {
                            ooFillJson[key] = { 'textGroup':val.grpTitle, 'jsonValue':[{}] };
                            $.each(val.questions, function (key2, val2) {
                                $.each(val2.cells, function (key3, val3) {
                                    ooFillJson[key].jsonValue[0][val3.cellValue] = '';
                                });
                            });
                        }
                    }
                });
                //영업기획서양식을 변경시 end

                let bFlagIsModify = true;
                if(data.info.sno > 0) bFlagIsModify = false;

                const initParams = {
                    data: {
                        schListMultiNk : schListMultiModalServiceNk.objDefault,

                        isModify : bFlagIsModify,
                        sFocusTable : '',
                        iFocusIdx : 0,
                        ooDefaultJson : data.json_default_form,
                        aoGuideFormList : data.guide_list,
                        aoBasicForm : data.basic_form_list[iBasicSalesPlanSno].jsonBasicFormContents,
                        oUpsertForm : data.info,
                        aoFillDatail : aoFillDatail,
                        ooFillJson : ooFillJson,

                        //namkuuu 후순위작업. 프로젝트정보, 고객정보 가져와서 넣어주기
                        customer : {}, 
                        mainData : {},  //프로젝트 확장 정보
                        productList : [], //스타일 정보

                        //추가 참여자 등록 레이어 팝업
                        chkSchedule : [], //선택 스케쥴 리스트
                        //코멘트
                        commentMap : [],
                        //스타일 수정 관련
                        batchSeason : '',
                        batchStyleProcType : 0,
                        batchCustSampleType : 0,
                        isStyleDetail : 'n',

                        //DL 계산
                        scheduleConfig : [],
                        //파일 리스트
                        fileList : [],

                        //결재 정보
                        projectApprovalInfo : {
                            'salesPlan' : {sno:-1}, //영업 기획
                        },

                    },
                    mounted : (vueInstance)=>{
                        //mainData = Project+ProjectExt 갱신
                        $.imsPostWithoutPreload('getSimpleProject',{sno:sno}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                vueInstance.mainData = data;
                                //스케쥴 데드라인 설정
                                setScheduleDeadLine();
                                //Customer 갱신
                                $.imsPostWithoutPreload('getData',{
                                    mode:'getData',
                                    target:DATA_MAP.CUSTOMER,
                                    sno :vueApp.mainData.customerSno
                                }).then((data)=>{
                                    $.imsPostAfter(data,(data)=>{
                                        vueApp.customer = data;
                                        //파일 셋팅
                                        setProjectFiles(vueInstance);
                                        //스케쥴 셋팅
                                        refreshProjectApproval();
                                    });
                                });
                            });
                        });
                        //ProductList 갱신
                        $.imsPostWithoutPreload('getListStyle',{'projectSno':sno,'sort':'S1',}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                vueInstance.productList = data.list;
                            });
                        });
                    },
                    methods : {
                        //스타일 추가
                        addSalesStyle : ()=>{
                            ImsProductService.addSalesStyle();
                        },
                        //스타일 명 설정
                        setStyleName : (product) =>{
                            ImsProductService.setStyleName(product);
                        },
                        //스타일 코드 설정
                        setStyleCode : (product, customerInitial) =>{
                            ImsProductService.setStyleCode(product, customerInitial);
                        },

                        //제안서양식 변경시
                        changeGuidePage : (iKey, sTextGuideName)=>{
                            if (sTextGuideName == '') {
                                vueApp.oUpsertForm.jsonProposalGuide[iKey].guideName = '';
                                vueApp.oUpsertForm.jsonProposalGuide[iKey].guideDesc = '';
                                vueApp.oUpsertForm.jsonProposalGuide[iKey].guideFileUrl = '';
                            } else {
                                $.each(vueApp.aoGuideFormList, function (key, val) {
                                    if (sTextGuideName == this.guideName) {
                                        vueApp.oUpsertForm.jsonProposalGuide[iKey].guideName = this.guideName;
                                        vueApp.oUpsertForm.jsonProposalGuide[iKey].guideDesc = this.guideDesc.replaceAll('\n','<br/>');
                                        vueApp.oUpsertForm.jsonProposalGuide[iKey].guideFileUrl = this.guideFileUrl;
                                        return false;
                                    }
                                });
                            }
                            if(!$.isEmptyAll(vueApp.$refs.guideImage)){
                                vueApp.$refs.guideImage[iKey].style.display = 'none';
                            }
                        },
                        //체크박스 클릭시
                        changeCheckbox : (iGrpKey, iQKey, iCellKey)=>{
                            vueApp.aoFillDatail[iGrpKey].questions[iQKey].cells[iCellKey].cellValue = '';
                            $.each(vueApp.$refs['salesPlanFormCheckbox_'+iGrpKey+'_'+iQKey+'_'+iCellKey], function (key, val) {
                                if (this.checked == true) {
                                    vueApp.aoFillDatail[iGrpKey].questions[iQKey].cells[iCellKey].cellValue += '|||'+this.value+'|||';
                                }
                            });
                        },
                        //저장
                        save : ()=>{
                            $.msgConfirm('저장하시겠습니까?','').then(function(result){
                                if( result.isConfirmed ) {
                                    vueApp.oUpsertForm.projectSno = sno;
                                    vueApp.oUpsertForm.basicSalesPlanSno = iBasicSalesPlanSno;

                                    let aoFillDatailRecords = [];
                                    let aoFillJsonRecords = [];
                                    $.each(vueApp.aoFillDatail, function (key, val) {
                                        if (val.grpType != 'json') {
                                            $.each(val.questions, function (key2, val2) {
                                                $.each(val2.cells, function (key3, val3) {
                                                    if (val3.cellType != 'fixed') {
                                                        aoFillDatailRecords.push({'salesPlanFillSno':0, 'textGroup':val3.textGroup, 'textQuestion':val3.textQuestion, 'textCell':val3.textCell, 'cellValue':val3.cellValue});
                                                    }
                                                });
                                            });
                                        }
                                    });

                                    if (Object.keys(vueApp.ooFillJson).length > 0) {
                                        $.each(vueApp.ooFillJson, function (key, val) {
                                            aoFillJsonRecords.push({'salesPlanFillSno':0, 'textGroup':val.textGroup, 'jsonValue':val.jsonValue});
                                        });
                                    }

                                    $.imsPost('setProjectSalesPlanFill', {'data':vueApp.oUpsertForm, 'detail':aoFillDatailRecords, 'json':aoFillJsonRecords}).then((data) => {
                                        $.imsPostAfter(data,(data)=>{
                                            $.msg('영업기획서가 작성되었습니다.', "", "success").then(()=>{
                                                vueApp.oUpsertForm.sno = data;
                                                vueApp.isModify = false;
                                            });
                                        });
                                    });
                                    
                                    //Project 확장정보 저장
                                    $.imsPostWithoutPreload('updateProject',{project:vueApp.mainData});
                                    //Product 정보 저장
                                    $.imsPostWithoutPreload('saveStyleList',{styleList:vueApp.productList});
                                }
                            });
                        },


                    },
                }

                initParams.computed = COMPUTED_DEAD_LINE;

                vueApp = ImsService.initVueApp(appId, initParams);
            });
        });
    });


</script>



<script type="text/javascript">
    const styleFieldConfig = [
        { title: '시즌', type: 'c', name: 'prdSeason', col: '4', class: 'font-11 ta-l pdl5' },
        { title: '타입', type: 'c', name: 'prdType', col: '9', class: 'font-11 ta-l pdl5' },
        { title: '스타일명', type: 's', name: 'productName', col: '', class: 'font-11 pdl5 ta-l' },
        
        { title: '진행 형태', type: 'c', name: 'styleProcType', col: '6', class: 'font-11 pdl5 ta-l' },
        { title: '고객사 샘플', type: 'c', name: 'addedInfo.prd002', col: '6', class: 'font-11 pdl5 ta-l' },

        { title: '예상수량', type: 'i', name: 'prdExQty', col: '4', class: 'font-11 ta-c' },
        { title: '원단MOQ', type: 'i', name: 'fabricMoq', col: '5', class: 'font-11 ta-c' },
        { title: '생산MOQ', type: 'i', name: 'prdMoq', col: '5', class: 'font-11 ta-c' },

        { title: '타겟생산가', type: 'i', name: 'targetPrdCost', col: '5', class: 'font-11 ta-c' },
        { title: '타겟판매가', type: 'i', name: 'targetPrice', col: '5', class: 'font-11 ta-c' },
        { title: '타겟판매(최대)', type: 'i', name: 'targetPriceMax', col: '5', class: 'font-11 ta-c' },

        { title: '컨셉수', type: 'as', name: 'prd010', col: '4', class: 'font-11 pdl5' },
        { title: '선호 디자인', type: 'as', name: 'prd014', col: '', class: 'font-11 pdl5' },
        { title: '선호 원단', type: 'as', name: 'prd013', col: '', class: 'font-11 pdl5' },
        { title: '선호 컬러', type: 'as', name: 'prd011', col: '', class: 'font-11 pdl5' }
    ];

    //TODO : 선호원단 , 선호 컬러

</script>
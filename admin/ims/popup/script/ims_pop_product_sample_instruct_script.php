<script type="text/javascript">
    const productSampleInstructData = {
        isModify : <?=$iSno==0?'true':'false'?>,
        sFocusTable : '',
        iFocusIdx : 0,
        bFlagEnableConfirm : false, //확정서페이지 진입가능여부 flag
        bFlagChangePlan : true,
        oMatchFldNmSamplePlan : {'productPlanSno':'sno','sampleName':'planConcept','planConcept':'planConcept','fitSpecSno':'fitSpecSno','fitName':'fitName','fitSize':'fitSize','sampleMemo':'planMemo','dollerRatio':'dollerRatio','dollerRatioDt':'dollerRatioDt','jsonFitSpec':'jsonFitSpec','jsonFixedSpec':'jsonFixedSpec','fabric':'fabric','subFabric':'subFabric','jsonUtil':'jsonUtil','jsonMark':'jsonMark','jsonLaborCost':'jsonLaborCost','jsonEtc':'jsonEtc','planPrdCost':'planPrdCost'},
        oUpsertInfo : {
            'jsonFitSpec': [],
            'fileList' : {
                'sampleFile7' : {},
                'sampleFile8' : {},
                'sampleFile9' : {},
                'sampleFile2' : {},
                'sampleFile3' : {},
                'sampleFile10' : {},
            }
        },

        factoryTel : { //샘플실,패턴실 선택하면 전화번호 바꿀때 쓰임
            <?php foreach($factoryTelMap as $key => $val) { ?>
            '<?=$key?>':'<?=str_replace("'","",$val)?>',
            <?php } ?>
        },

        sRedirectUrl : "/ims/popup/ims_pop_product_sample_new.php?styleSno=<?=$styleSno?>&sno=", //저장시 이동하는 url(self url)

        //어떻게 될지 모르니 일단 놔두기(샘플 불러오기 -> 스타일기획 불러오기(리스트검색module사용)). 샘플 불러오기
        loadSampleNo : '',

        //환율
        sCurrDollerRatio : '<?=$sCurrDollerRatio?>',
        sCurrDollerRatioDt : '<?=$sCurrDollerRatioDt?>',
        sSaveDollerRatio : '<?=$sCurrDollerRatio?>',
        sSaveDollerRatioDt : '<?=$sCurrDollerRatioDt?>',
        //고객제공사이즈
        ooCustomerFitList : {sizeName:[]},
    };
    const productSampleInstructMethod = {
        //고객제공사이즈 가져오기 + 원부자재모듈의 부위selectbox, 생산처 selectbox 조정
        getCustomerFit : ()=>{
            vueApp.ooCustomerFitList.sizeName = [];
            if (vueApp.oUpsertInfo.productPlanSno > 0) {
                ImsNkService.getList('stylePlanCustomerFit', {'productPlanSno':vueApp.oUpsertInfo.productPlanSno}).then((data)=>{
                    $.imsPostAfter(data, (data)=> {
                        vueApp.ooCustomerFitList = data;
                        vueApp.ooCustomerFitList.sizeName = [];
                        $.each(vueApp.ooCustomerFitList, function (key, val) {
                            $.each(val, function (key2, val2) {
                                vueApp.ooCustomerFitList.sizeName.push(key2);
                            });
                            return false;
                        });
                    });
                });
            }

            //부위selectbox 조정
            vueApp.refreshMateSelectbox(vueApp.oUpsertInfo.fabric, 'Fabric');
            vueApp.refreshMateSelectbox(vueApp.oUpsertInfo.subFabric, 'SubFabric');
            vueApp.refreshMateSelectbox(vueApp.oUpsertInfo.jsonUtil, 'UtilFabric');
            vueApp.refreshMateSelectbox(vueApp.oUpsertInfo.jsonMark, 'MarkFabric');
            vueApp.refreshMateSelectbox(vueApp.oUpsertInfo.jsonLaborCost, 'LaborCost');
            vueApp.refreshMateSelectbox(vueApp.oUpsertInfo.jsonEtc, 'EtcCost');
        },
        //파일 업로드할때 실행하는 함수
        uploadAfterActionSampleByPopup : (tmpFile, dropzoneId)=>{
            ImsProductService.uploadAfterActionWithoutMemo(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                let saveData = {
                    customerSno : vueApp.oUpsertInfo.customerSno,
                    projectSno : vueApp.oUpsertInfo.projectSno,
                    styleSno : vueApp.oUpsertInfo.styleSno,
                    eachSno : vueApp.oUpsertInfo.sno,
                    fileDiv : dropzoneId,
                    fileList : saveFileList,
                    memo : promptValue,
                };
                //console.log(saveData);
                $.imsPost('saveProjectFiles',{
                    saveData : saveData
                }).then((data)=>{
                    if(200 === data.code) {
                        //리스트 갱신
                        location.reload();
                    }
                });
            });
        },
        //첨부파일 관련 - 이미지파일이면 true, 아니면 false
        checkImageExtension : (sFileNm)=>{
            const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/i;
            return allowedExtensions.exec(sFileNm);
        },

        //확정스펙에서 측정항목 추가/삭제시
        addSpecOption : ()=>{
            vueApp.addElement(vueApp.oUpsertInfo.jsonFitSpec, vueApp.ooDefaultJson.jsonFitSpec, 'after');
            vueApp.addElement(vueApp.oUpsertInfo.jsonFixedSpec, vueApp.ooDefaultJson.jsonFixedSpec, 'after');
        },
        deleteSpecOption : (iOptionKey)=>{
            vueApp.deleteElement(vueApp.oUpsertInfo.jsonFitSpec, iOptionKey);
            vueApp.deleteElement(vueApp.oUpsertInfo.jsonFixedSpec, iOptionKey);
        },

        //샘플 불러오기
        loadSampleNk : ()=>{
            $.imsPost('loadSample', {
                loadSampleNo : vueApp.loadSampleNo
            }).then((data) => {
                if( 200 === data.code ){
                    //기본키, 외래키(스타일sno), 확정정보, 첨부파일정보는 불러오기에서 제외시킨다.
                    let aNotOverWriteFlds = ['sno', 'customerSno', 'projectSno', 'styleSno', 'productPlanSno', 'sampleConfirm', 'sampleConfirmManager', 'sampleConfirmDt', 'sampleFile1Approval', 'fileList'];

                    $.each(vueApp.oUpsertInfo, function(key, val) {
                        if (aNotOverWriteFlds.indexOf(key) === -1) {
                            if (data.data[key] != undefined) vueApp.oUpsertInfo[key] = data.data[key];
                        }
                    });
                }
            });
        },

        saveSampleNew : ()=>{
            if (vueApp.oUpsertInfo.productPlanSno == null || vueApp.oUpsertInfo.productPlanSno == '0') {
                $.msg('스타일기획을 선택하세요.','','warning');
                return false;
            }

            if (vueApp.oUpsertInfo.sampleName == null || vueApp.oUpsertInfo.sampleName == '') {
                $.msg('샘플명을 입력하세요.','','warning');
                return false;
            }
            if (vueApp.oUpsertInfo.sampleType == null || vueApp.oUpsertInfo.sampleType == '') {
                $.msg('구분을 선택하세요.','','warning');
                return false;
            }

            if (vueApp.oUpsertInfo.jsonFitSpec != null && vueApp.oUpsertInfo.jsonFitSpec != '') {
                let sErrMsg = '';
                let aOptionNms = new Array();
                $.each(vueApp.oUpsertInfo.jsonFitSpec, function(key, val) {
                    if (val.optionUnit == '') {
                        sErrMsg = '사이즈스펙의 측정항목 중 선택하지 않은 단위가 있습니다.';
                        return false;
                    }
                    if (val.optionName == '') {
                        sErrMsg = '사이즈스펙의 측정항목 중 입력하지 않은 부위명이 있습니다.';
                        return false;
                    }
                    if (aOptionNms.indexOf(val.optionName) !== -1) {
                        sErrMsg = '사이즈스펙의 측정항목 중 중복되는 부위명이 있습니다.';
                        return false;
                    }
                    aOptionNms.push(val.optionName);
                });
                if (sErrMsg != '') {
                    $.msg(sErrMsg,'','warning');
                    return false;
                }
            }

            $.imsPost('saveSampleNk', {'data':vueApp.oUpsertInfo}).then((data) => {
                $.imsPostAfter(data,(data)=>{
                    $.msg('샘플 저장 완료.', "", "success").then(()=>{
                        if (parent.opener && typeof parent.opener.refreshSampleList === 'function') {
                            parent.opener.refreshSampleList();
                        }
                        location.href = vueApp.sRedirectUrl + data;
                    });
                });
            });
        },
    };
    const productSampleInstructComputed = {};


    //스타일기획 선택 콜백함수
    function getCustomerFit(oTarget) {
        vueApp.getCustomerFit();
    }
    //사이즈스펙CRU창에서 핏 선택하면 아래 함수 실행
    function copyFitSpec(sSno, sName, sSize, aoOption) {
        vueApp.oUpsertInfo.fitSpecSno = sSno;
        vueApp.oUpsertInfo.fitName = sName;
        vueApp.oUpsertInfo.fitSize = sSize;
        vueApp.oUpsertInfo.jsonFitSpec = [];
        vueApp.oUpsertInfo.jsonFixedSpec = [];

        if (aoOption == undefined || aoOption.length === 0) {
            vueApp.oUpsertInfo.jsonFitSpec.push($.copyObject(vueApp.ooDefaultJson.jsonFitSpec));
            vueApp.oUpsertInfo.jsonFixedSpec.push($.copyObject(vueApp.ooDefaultJson.jsonFixedSpec));
        } else {
            $.each(aoOption, function(key, val) {
                vueApp.oUpsertInfo.jsonFitSpec.push({'optionName':val.optionName, 'optionRange':val.optionRange, 'optionValue':val.optionValue, 'optionUnit':val.optionUnit});
                vueApp.oUpsertInfo.jsonFixedSpec.push({'optionName':val.optionName, 'optionValue':val.optionValue});
            });
        }
    }
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData, Object.assign({}, productSampleInstructData, materialModuleData));

        ImsBoneService.setMethod(serviceData, Object.assign({}, productSampleInstructMethod, materialModuleMethods));

        ImsBoneService.setComputed(serviceData, Object.assign({}, productSampleInstructComputed, materialModuleComputed));

        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            //페이지로딩시 샘플upsert폼 가져온다(등록시 기본폼, 수정시 저장데이터)
            ImsNkService.getList('productSample', {'upsertSnoGet':'<?=$iSno?>'}).then((data)=>{
                if(200 === data.code) {
                    vueApp.oUpsertInfo = data.data.list[0];
                    if (vueApp.oUpsertInfo.sno == null || vueApp.oUpsertInfo.sno == 0) { //등록페이지 진입시
                        vueApp.oUpsertInfo.sno = 0;
                        vueApp.oUpsertInfo.styleSno = <?=$styleSno?>;
                        vueApp.oUpsertInfo.productPlanSno = <?=$productPlanSno?>;
                        vueApp.oUpsertInfo.sampleFactorySno = 0;
                        vueApp.oUpsertInfo.patternFactorySno = 0;

                        //스타일기획리스트 -> 샘플등록 클릭시 스타일기획 정보 가져와서 샘플정보에 덮어씀(스타일기획 선택버튼 클릭한 것과 같은 동작)
                        if (vueApp.oUpsertInfo.productPlanSno > 0) {
                            ImsNkService.getList('stylePlan', {'productPlanSno':vueApp.oUpsertInfo.productPlanSno}).then((data)=>{
                                $.imsPostAfter(data, (data)=> {
                                    $.each(vueApp.oMatchFldNmSamplePlan, function(key, val) {
                                        vueApp.oUpsertInfo[key] = data.list[0][val];
                                    });

                                    //고객제공샘플 가져오기 + 부위selectbox 조정
                                    vueApp.getCustomerFit();
                                });
                            });

                        } else {
                            vueApp.oUpsertInfo.dollerRatio = $.copyObject(vueApp.sCurrDollerRatio);
                            vueApp.oUpsertInfo.dollerRatioDt = $.copyObject(vueApp.sCurrDollerRatioDt);
                            vueApp.oUpsertInfo.planPrdCost = 0; //기획 생산가. 스타일기획 선택하면 가져오는 값
                            vueApp.oUpsertInfo.planConcept  = '선택하세요';
                        }
                    } else { //상세/수정페이지 진입시
                        if (vueApp.oUpsertInfo.productPlanSno > 0) {
                            vueApp.bFlagChangePlan = false;

                            //고객제공샘플 가져오기 + 부위selectbox 조정
                            vueApp.getCustomerFit();
                        }

                        //등록했을때 저장한 환율 넣기(저장된 환율이 없으면 현재환율)
                        if (Number(vueApp.oUpsertInfo.dollerRatio) != 0.00) {
                            if (vueApp.oUpsertInfo.dollerRatioDt == null || vueApp.oUpsertInfo.dollerRatioDt == '') vueApp.oUpsertInfo.dollerRatioDt = '0000-00-00';
                            vueApp.sSaveDollerRatio = $.copyObject(vueApp.oUpsertInfo.dollerRatio);
                            vueApp.sSaveDollerRatioDt = $.copyObject(vueApp.oUpsertInfo.dollerRatioDt);
                        } else {
                            vueApp.oUpsertInfo.dollerRatio = $.copyObject(vueApp.sCurrDollerRatio);
                            vueApp.oUpsertInfo.dollerRatioDt = $.copyObject(vueApp.sCurrDollerRatioDt);
                        }

                        //파일 업로드버튼 셋팅. 수정시에만 첨부파일 업로드 가능
                        $('.set-dropzone').addClass('dropzone');
                        ImsService.setDropzone(vueApp, 'sampleFile7', vueApp.uploadAfterActionSampleByPopup); //썸네일
                        ImsService.setDropzone(vueApp, 'sampleFile8', vueApp.uploadAfterActionSampleByPopup); //샘플 도안
                        ImsService.setDropzone(vueApp, 'sampleFile9', vueApp.uploadAfterActionSampleByPopup); //마크 도안
                        ImsService.setDropzone(vueApp, 'sampleFile2', vueApp.uploadAfterActionSampleByPopup); //실물사진
                        ImsService.setDropzone(vueApp, 'sampleFile3', vueApp.uploadAfterActionSampleByPopup); //실패턴
                        ImsService.setDropzone(vueApp, 'sampleFile10', vueApp.uploadAfterActionSampleByPopup); //마카
                        //샘플확정서페이지 이동(탭메뉴) 유효/무효
                        if (vueApp.oUpsertInfo.jsonReviewCheck != null && vueApp.oUpsertInfo.jsonReviewCheck.length != 0) vueApp.bFlagEnableConfirm = true;
                    }

                    vueApp.materialModuleInit();
                }
            });


        }); //setMounted end

        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
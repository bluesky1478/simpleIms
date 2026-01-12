<script type="text/javascript">
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            bFlagEnableConfirm : false, //확정서페이지 진입가능여부 flag
            bFlagChangePlan : false, //스타일기획 변경가능여부 flag. 리뷰서페이지는 무조건 false
            isModify : <?=$iSno==0?'true':'false'?>,
            //json으로 저장되는 컬럼data의 default form
            ooDefaultJson :{
                <?php foreach ($aDefaultJson as $key => $val) { ?>
                '<?=$key?>':{
                    <?php foreach ($val as $key2 => $val2) { ?>
                    '<?=$key2?>':'<?=$val2?>',
                    <?php } ?>
                },
                <?php } ?>
            },
            oUpsertInfo : {
                'jsonFitSpec': [],
                'fileList' : {
                    'sampleFile7' : {},
                    'sampleFile8' : {},
                    'sampleFile9' : {},
                    'sampleFile2' : {},
                    'sampleFile3' : {},
                    'sampleFile10' : {},
                    'sampleFile11' : {},
                    'sampleFile12' : {},
                    'sampleFile13' : {},
                }
            },
            sFocusTable : '',
            iFocusIdx : 0,
            factoryTel : { //샘플실,패턴실 선택하면 전화번호 바꿀때 쓰임
                <?php foreach($factoryTelMap as $key => $val) { ?>
                '<?=$key?>':'<?=str_replace("'","",$val)?>',
                <?php } ?>
            },
            //피팅체크양식 리스트
            aoFittingCheckList : [],
            //피팅체크-구분명 변경시 같은 구분명 가진 row의 key 담음
            aTmpTargetChgTypeNmKeys : [],
        });
        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            ImsNkService.getList('productSample', {'upsertSnoGet':'<?=$iSno?>'}).then((data)=>{
                if(200 === data.code){
                    //리뷰서,확정서는 등록페이지가 없다(샘플지시서 등록 === 샘플 등록)
                    vueApp.oUpsertInfo = data.data.list[0];
                    //사이즈스펙 피팅체크
                    if (vueApp.oUpsertInfo.jsonFitSpec.length == 0) vueApp.oUpsertInfo.jsonReviewSpec = [];
                    else {
                        let oDefaultForm = {};
                        if (vueApp.oUpsertInfo.jsonReviewSpec == null || vueApp.oUpsertInfo.jsonReviewSpec.length == 0) {
                            //샘플리뷰서 최초 저장시
                            vueApp.oUpsertInfo.jsonReviewSpec = [];
                            $.each(vueApp.oUpsertInfo.jsonFitSpec, function (key, val) {
                                oDefaultForm = $.copyObject(vueApp.ooDefaultJson.jsonReviewSpec);
                                oDefaultForm.optionName = vueApp.oUpsertInfo.jsonFixedSpec[key].optionName;
                                oDefaultForm.madeValue = vueApp.oUpsertInfo.jsonFixedSpec[key].optionValue;
                                oDefaultForm.checkValue = vueApp.oUpsertInfo.jsonFixedSpec[key].optionValue;
                                vueApp.oUpsertInfo.jsonReviewSpec.push($.copyObject(oDefaultForm));
                            });
                        } else {
                            //샘플지시서에서 사이즈스펙을 수정한 경우를 감안하여 vueApp.oUpsertInfo.jsonReviewSpec 재구성
                            let oSavedForm = $.copyObject(vueApp.oUpsertInfo.jsonReviewSpec);
                            vueApp.oUpsertInfo.jsonReviewSpec = [];
                            $.each(vueApp.oUpsertInfo.jsonFitSpec, function (key, val) {
                                oDefaultForm = $.copyObject(vueApp.ooDefaultJson.jsonReviewSpec);
                                oDefaultForm.optionName = this.optionName;
                                $.each(oSavedForm, function (key2, val2) {
                                    if (oDefaultForm.optionName == this.optionName) {
                                        oDefaultForm.madeValue = this.madeValue;
                                        oDefaultForm.checkValue = this.checkValue;
                                        oDefaultForm.specDesc = this.specDesc;
                                        return false;
                                    }
                                });
                                vueApp.oUpsertInfo.jsonReviewSpec.push($.copyObject(oDefaultForm));
                            });
                        }
                    }

                    if (vueApp.oUpsertInfo.jsonReviewCheck == null || vueApp.oUpsertInfo.jsonReviewCheck.length == 0) {
                        vueApp.oUpsertInfo.jsonReviewCheck = [];
                        //샘플리뷰서 처음 작성시 가장 먼저 등록된 같은시즌, 같은스타일의 피팅체크 불러와서 자동으로 뿌려줌
                        ImsNkService.getList('fittingCheck', { 'sRadioSchFitStyle':'<?=$sSendPrdStyle?>', 'sRadioSchFitSeason':'<?=$sSendPrdSeason?>' }).then((data)=> {
                            $.imsPostAfter(data, (data) => {
                                if (data.list.length > 0) {
                                    vueApp.oUpsertInfo.jsonReviewCheck = data.list[data.list.length-1].jsonOptions;
                                    //rowspan 값 구해서 넣어주기
                                    vueApp.calcReviewCheckRowspan();
                                }
                            });
                        });
                    } else {
                        //피팅체크 저장해야 샘플확정서 넘어갈 수 있음
                        vueApp.bFlagEnableConfirm = true;
                    }

                    //파일 업로드버튼 셋팅. 수정시에만 첨부파일 업로드 가능
                    $('.set-dropzone').addClass('dropzone');
                    ImsService.setDropzone(vueApp, 'sampleFile7', vueApp.uploadAfterActionSampleByPopup); //썸네일
                    ImsService.setDropzone(vueApp, 'sampleFile8', vueApp.uploadAfterActionSampleByPopup); //샘플 도안
                    ImsService.setDropzone(vueApp, 'sampleFile9', vueApp.uploadAfterActionSampleByPopup); //마크 도안
                    ImsService.setDropzone(vueApp, 'sampleFile2', vueApp.uploadAfterActionSampleByPopup); //실물사진
                    ImsService.setDropzone(vueApp, 'sampleFile3', vueApp.uploadAfterActionSampleByPopup); //실패턴
                    ImsService.setDropzone(vueApp, 'sampleFile10', vueApp.uploadAfterActionSampleByPopup); //마카
                    ImsService.setDropzone(vueApp, 'sampleFile11', vueApp.uploadAfterActionSampleByPopup); //샘플사진앞면
                    ImsService.setDropzone(vueApp, 'sampleFile12', vueApp.uploadAfterActionSampleByPopup); //샘플사진뒷면
                    ImsService.setDropzone(vueApp, 'sampleFile13', vueApp.uploadAfterActionSampleByPopup); //샘플사진디테일
                }
            });

            //피팅체크양식리스트 가져오기
            ImsNkService.getList('fittingCheck', {'sEqualOrEmptySchFitStyle':'<?=$sSendPrdStyle?>', 'sEqualOrEmptySchFitSeason':'<?=$sSendPrdSeason?>'}).then((data)=> {
                $.imsPostAfter(data, (data) => {
                    vueApp.aoFittingCheckList = data.list;
                });
            });
        }); //setMounted end

        ImsBoneService.setComputed(serviceData,{
        });

        ImsBoneService.setMethod(serviceData,{
            //피팅체크의 구분rowspan을 계산
            calcReviewCheckRowspan : ()=>{
                let iCntType = 0;
                for (var i = vueApp.oUpsertInfo.jsonReviewCheck.length-1; i >= 0; i--) {
                    iCntType++;
                    if (i == 0 || vueApp.oUpsertInfo.jsonReviewCheck[i].checkType != vueApp.oUpsertInfo.jsonReviewCheck[i-1].checkType) {
                        vueApp.oUpsertInfo.jsonReviewCheck[i].cntType = iCntType;
                        iCntType = 0;
                    } else vueApp.oUpsertInfo.jsonReviewCheck[i].cntType = 0;
                }
            },
            //피팅체크양식 선택시
            chooseFittingCheck : (iKey)=>{
                vueApp.oUpsertInfo.jsonReviewCheck = [];
                if (iKey != '') {
                    let oDefaultForm = $.copyObject(vueApp.ooDefaultJson.jsonReviewCheck);
                    $.each(vueApp.aoFittingCheckList[iKey].jsonOptions, function (key, val) {
                        oDefaultForm.cntType = this.cntType;
                        oDefaultForm.checkType = this.checkType;
                        oDefaultForm.checkName = this.checkName;
                        vueApp.oUpsertInfo.jsonReviewCheck.push($.copyObject(oDefaultForm));
                    });
                }
                //rowspan 값 구해서 넣어주기
                vueApp.calcReviewCheckRowspan();
            },
            //피팅체크 항목 추가시
            appendFittingCheck : (iKey)=>{
                let sTargetType = vueApp.oUpsertInfo.jsonReviewCheck[iKey].checkType;
                let oAppendObj = vueApp.addElement(vueApp.oUpsertInfo.jsonReviewCheck, vueApp.ooDefaultJson.jsonReviewCheck, 'down', iKey);
                oAppendObj.checkType = sTargetType;
                vueApp.calcReviewCheckRowspan();
            },
            //피팅체크 구분추가시
            appendFittingCheckByType : (sTypeNm)=>{
                let iKey = 0;
                for (var i = vueApp.oUpsertInfo.jsonReviewCheck.length-1; i >= 0; i--) {
                    if (vueApp.oUpsertInfo.jsonReviewCheck[i].checkType == sTypeNm) {
                        iKey = i;
                        break;
                    }
                }
                vueApp.addElement(vueApp.oUpsertInfo.jsonReviewCheck, vueApp.ooDefaultJson.jsonReviewCheck, 'down', iKey);
                vueApp.calcReviewCheckRowspan();
            },
            //피팅체크 항목 삭제시
            deleteFittingCheck : (iKey)=>{
                vueApp.deleteElement(vueApp.oUpsertInfo.jsonReviewCheck, iKey);
                vueApp.calcReviewCheckRowspan();
            },
            //피팅체크 구분명 수정시(start, end)
            startChgCheckTypeName : (sPrevNm)=>{
                if (vueApp.aTmpTargetChgTypeNmKeys.length == 0) {
                    $.each(vueApp.oUpsertInfo.jsonReviewCheck, function(key, val) {
                        if (vueApp.oUpsertInfo.jsonReviewCheck[key].checkType == sPrevNm) vueApp.aTmpTargetChgTypeNmKeys.push(key);
                    });
                }
            },
            endChgCheckTypeName : (sChgNm)=>{
                if (vueApp.aTmpTargetChgTypeNmKeys.length > 0) {
                    $.each(vueApp.aTmpTargetChgTypeNmKeys, function(key, val) {
                        vueApp.oUpsertInfo.jsonReviewCheck[this].checkType = sChgNm;
                    });
                    vueApp.aTmpTargetChgTypeNmKeys = [];
                }
            },
            //직접 클릭시 : 피팅체크 초기화
            clearFittingCheckList : ()=>{
                vueApp.oUpsertInfo.jsonReviewCheck = [];
                vueApp.addElement(vueApp.oUpsertInfo.jsonReviewCheck, vueApp.ooDefaultJson.jsonReviewCheck, 'down', 0);
                vueApp.calcReviewCheckRowspan();
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
            saveSampleNew : ()=>{
                if (vueApp.oUpsertInfo.sampleName == null || vueApp.oUpsertInfo.sampleName == '') {
                    $.msg('샘플명을 입력하세요.','','warning');
                    return false;
                }
                if (vueApp.oUpsertInfo.sampleType == null || vueApp.oUpsertInfo.sampleType == '') {
                    $.msg('구분을 선택하세요.','','warning');
                    return false;
                }

                $.imsPost('saveSampleNk', {'data':vueApp.oUpsertInfo}).then((data) => {
                    $.imsPostAfter(data,(data)=>{
                        $.msg('샘플리뷰서 정보 저장 완료.', "", "success").then(()=>{
                            if (parent.opener && typeof parent.opener.refreshSampleList === 'function') {
                                parent.opener.refreshSampleList();
                            }
                            location.reload();
                        });
                    });
                });

            },
        });

        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
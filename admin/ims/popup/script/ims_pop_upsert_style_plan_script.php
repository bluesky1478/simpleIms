<script type="text/javascript">
    function fnCallback(oTarget) {
        oTarget.planConcept = oTarget.refName;
        $.imsPost('getJsonMateListFromRefMateList', {'data':oTarget.refStylePlanSno}).then((data) => {
            $.imsPostAfter(data,(data)=>{
                $.each(data, function (key, val) {
                    oTarget[key] = val;
                });
                vueApp.refreshMateSelectbox(oTarget.fabric, 'Fabric');
                vueApp.refreshMateSelectbox(oTarget.subFabric, 'SubFabric');
                vueApp.refreshMateSelectbox(oTarget.jsonUtil, 'UtilFabric');
                vueApp.refreshMateSelectbox(oTarget.jsonMark, 'MarkFabric');
                vueApp.refreshMateSelectbox(oTarget.jsonLaborCost, 'LaborCost');
                vueApp.refreshMateSelectbox(oTarget.jsonEtc, 'EtcCost');
            });
        });
    }


    const upsertStylePlanData = {
        isModify : <?=$aTableFldList['sno']==0?'true':'false'?>,
        sFocusTable : '',
        iFocusIdx : 0,
        oPlanInfo : {
            <?php foreach ($aTableFldList as $key => $val) {
            if ($key == 'fileList')  { ?>
            'fileList' : {
                'stylePlanFile1' : {
                    <?php foreach ($val['stylePlanFile1'] as $key2 => $val2) { ?>
                    '<?=str_replace("'","",$key2)?>':<?php if ($key2 == 'files') { echo "["; foreach($val2 as $key3 => $val3){ echo "{"; foreach($val3 as $key4 => $val4){ echo "'".$key4."':'".$val4."',"; } echo "},"; } echo "]"; } else echo "'".$val2."'"; ?>,
                    <?php } ?>
                },
                'stylePlanFile2' : {
                    <?php foreach ($val['stylePlanFile2'] as $key2 => $val2) { ?>
                    '<?=str_replace("'","",$key2)?>':<?php if ($key2 == 'files') { echo "["; foreach($val2 as $key3 => $val3){ echo "{"; foreach($val3 as $key4 => $val4){ echo "'".$key4."':'".$val4."',"; } echo "},"; } echo "]"; } else echo "'".$val2."'"; ?>,
                    <?php } ?>
                },
            },
            <?php } else if (in_array($key, ['jsonUtil','fabric','subFabric','jsonFitSpec','jsonFixedSpec','jsonMark','jsonLaborCost','jsonEtc'])) { ?>
            '<?=$key?>': [
                <?php foreach ($val as $key2 => $val2) { ?>
                {
                    <?php foreach ($val2 as $key3 => $val3) { if (is_array($val3)) { echo str_replace("'","",$key3).':{';   foreach($val3 as $key4 => $val4) { ?>'<?=str_replace("'","",$key4)?>':'<?=str_replace("'","",$val4)?>',<?php } echo '},'; } else { ?>'<?=str_replace("'","",$key3)?>':'<?=str_replace("'","",$val3)?>',<?php } } ?>
                },
                <?php } ?>
            ],
            <?php } else { ?>
            '<?=str_replace("'","",$key)?>':'<?=str_replace("'","",$val)?>',
            <?php }
            } ?>
        },
        referFilePaths : [],
        proposalFilePaths : [],
        sRedirectUrl : "/ims/popup/ims_pop_upsert_style_plan.php?projectSno=<?=$iProjectSno?>&styleSno=<?=$iStyleSno?>&sno=", //저장시 이동하는 url(self url)
        //환율
        sCurrDollerRatio : '<?=$sCurrDollerRatio?>',
        sCurrDollerRatioDt : '<?=$sCurrDollerRatioDt?>',
        sSaveDollerRatio : '<?=$sSaveDollerRatio?>',
        sSaveDollerRatioDt : '<?=$sSaveDollerRatioDt?>',

        //사이즈스펙 관련
        customerSampleYn : '<?=$sCustomerSampleYn?>',
        bFlagShowCompare : true, //고객제공샘플 중에는 제공사이즈(들) 중에서 기준사이즈가 무조건 있다고 판단하여 true
        aaoCustomerSample : [ //$arr[측정항목][사이즈] = ['optionSize'=>'','optionName'=>'','optionValue'=>'','optionUnit'=>'']
            <?php foreach ($aCustomerSample as $key => $val) { ?>
            [
                <?php foreach ($val as $key2 => $val2) { ?>
                {
                    <?php foreach ($val2 as $key3 => $val3) { ?>
                    '<?=str_replace("'","",$key3)?>':'<?=str_replace("'","",$val3)?>',
                    <?php } ?>
                },
                <?php } ?>
            ],
            <?php } ?>
        ],

    };

    const upsertStylePlanMethods = {
        changeUpdateMode : (iFlag)=>{
            if (iFlag === true) {
                vueApp.oPlanInfo.planMemo = vueApp.oPlanInfo.planMemo.replaceAll('<br/>','\n');
                vueApp.oPlanInfo.produceMemo = vueApp.oPlanInfo.produceMemo.replaceAll('<br/>','\n');
            } else {
                vueApp.oPlanInfo.planMemo = vueApp.oPlanInfo.planMemo.replaceAll('\n','<br/>');
                vueApp.oPlanInfo.produceMemo = vueApp.oPlanInfo.produceMemo.replaceAll('\n','<br/>');
            }
            vueApp.isModify = iFlag;
        },
        uploadStylePlanFile : (sFileType)=>{
            const fileInput = vueApp.$refs[sFileType+'Element'];
            if (fileInput.files.length > 0) {
                const formData = new FormData();
                formData.append('upfile', fileInput.files[0]);
                $.ajax({
                    url: '<?=$nasUrl?>/img_upload.php?projectSno='+vueApp.oPlanInfo.projectSno,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result){
                        const rslt = JSON.parse(result);
                        vueApp.oPlanInfo[sFileType] = '<?=$nasUrl?>'+rslt.downloadUrl;

                        //파일 업로드하는 시점에 파일내용을 DB에 저장하고 싶으면 아래 주석해제(단, 등록일때 modDt에도 값 들어가진다)
                        //let oSend = {'targetSno' : vueApp.oPlanInfo.sno, 'styleSno': vueApp.oPlanInfo.styleSno};
                        //if (sFileType == 'filePlan') oSend.filePlan = vueApp.oPlanInfo[sFileType];
                        // $.imsPost('setStylePlanFileInfo', oSend).then((data)=>{
                        //     $.imsPostAfter(data,(data)=>{
                        //         if (vueApp.oPlanInfo.sno == 0) vueApp.oPlanInfo.sno = data;
                        //     });
                        // });
                    }
                });
            }
        },
        deleteStylePlanFile: (sFileType)=>{
            let sTargetText = '';
            let oSend = {'targetSno' : vueApp.oPlanInfo.sno, 'styleSno': vueApp.oPlanInfo.styleSno};
            if (sFileType == 'filePlan') {
                sTargetText = '기획이미지를';
                oSend.filePlan = '';
            }
            $.msgConfirm(sTargetText+' 삭제 하시겠습니까?','복구 불가.').then(function(result){
                if( result.isConfirmed ){
                    vueApp.oPlanInfo[sFileType] = '';

                    //파일 업로드하는 시점에 파일내용을 DB에 저장하고 싶으면 아래 주석해제(단, 등록일때 modDt에도 값 들어가진다)
                    // $.imsPost('setStylePlanFileInfo', oSend);
                }
            });
        },
        //파일 업로드할때 실행하는 함수
        uploadAfterActionStylePlan : (tmpFile, dropzoneId)=>{
            ImsProductService.uploadAfterActionWithoutMemo(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                if (vueApp.oPlanInfo.sno == 0) {
                    vueApp.oPlanInfo.fileList[dropzoneId].title = tmpFile.length+'개 파일 업로드';
                    vueApp.oPlanInfo.fileList[dropzoneId].memo = promptValue;
                    vueApp.oPlanInfo.fileList[dropzoneId].files = [];
                    $.each(tmpFile, function(key, val) {
                        vueApp.oPlanInfo.fileList[dropzoneId].files.push(val);
                    });
                    $.msg('저장을 클릭하셔야 참고파일이 반영됩니다.','','success');
                } else {
                    let saveData = {
                        customerSno : vueApp.oPlanInfo.customerSno,
                        projectSno : vueApp.oPlanInfo.projectSno,
                        styleSno : vueApp.oPlanInfo.styleSno,
                        eachSno : vueApp.oPlanInfo.sno,
                        fileDiv : dropzoneId,
                        fileList : saveFileList,
                        memo : promptValue,
                    };
                    $.imsPost('saveProjectFiles',{
                        saveData : saveData
                    }).then((data)=>{
                        if(200 === data.code) {
                            vueApp.oPlanInfo.fileList[dropzoneId].title = tmpFile.length+'개 파일 업로드';
                            vueApp.oPlanInfo.fileList[dropzoneId].memo = promptValue;
                            vueApp.oPlanInfo.fileList[dropzoneId].files = [];
                            $.each(tmpFile, function(key, val) {
                                vueApp.oPlanInfo.fileList[dropzoneId].files.push(val);
                            });
                            $.msg('참고파일이 반영되었습니다.','','success');
                        }
                    });
                }
            });
        },
        //첨부파일보기 - 이미지들의 url 정리
        revertReferFilePath : ()=>{
            vueApp.referFilePaths = [];
            if (vueApp.oPlanInfo.fileList.stylePlanFile1.files.length > 0) {
                $.each(vueApp.oPlanInfo.fileList.stylePlanFile1.files, function (key, val) {
                    vueApp.referFilePaths.push('<?=$nasUrl?>'+val.filePath);
                });
            }
            vueApp.proposalFilePaths = [];
            if (vueApp.oPlanInfo.fileList.stylePlanFile2.files.length > 0) {
                $.each(vueApp.oPlanInfo.fileList.stylePlanFile2.files, function (key, val) {
                    vueApp.proposalFilePaths.push('<?=$nasUrl?>'+val.filePath);
                });
            }
        },
        //사이즈스펙 관련메소드 start
        //사이즈스펙 직접입력 클릭시 사이즈스펙,고객제공샘플 초기화
        directInputFitSpec : ()=>{
            vueApp.oPlanInfo.fitSpecSno = 0;
            vueApp.oPlanInfo.fitName = '직접입력';
            vueApp.oPlanInfo.fitSize = '';
            vueApp.oPlanInfo.jsonFitSpec = [$.copyObject(vueApp.ooDefaultJson.jsonFitSpec)];
            vueApp.oPlanInfo.jsonFixedSpec = [$.copyObject(vueApp.ooDefaultJson.jsonFixedSpec)];
            vueApp.aaoCustomerSample = [[{'optionSize':'','optionName':'','optionValue':'','optionUnit':'CM'}]];
        },
        //고객제공샘플에서 사이즈추가시
        appendCustomerSampleSize : (bFlagBigger)=>{
            let oAppend = {'optionSize':'','optionName':'','optionValue':'','optionUnit':'CM'};
            if (bFlagBigger === true) {
                let aaoTarget = $.copyObject(vueApp.aaoCustomerSample);
                $.each(vueApp.aaoCustomerSample, function(key, val) { //항목 반복
                    $.each(val, function(key2, val2) { //사이즈 반복
                        if (key2 === val.length - 1) { //가장 큰 치수라면(가장 오른쪽에 위치)
                            oAppend.optionSize = Number(val2.optionSize) > 80 ? Number(val2.optionSize)+5 : Number(val2.optionSize)+1;
                            oAppend.optionName = val2.optionName;
                            // oAppend.optionValue = Math.round((Number(val2.optionValue) + Number(vueApp.oPlanInfo.jsonFitSpec[key].optionRange)) * 100) / 100; //고객 제공 사이즈 추가시 defualt값 공백. 편차에 따라 default수치를 + 계산
                            oAppend.optionValue = '';
                            oAppend.optionUnit = val2.optionUnit;
                            aaoTarget[key].push($.copyObject(oAppend));
                        }
                    });
                });
                vueApp.aaoCustomerSample = $.copyObject(aaoTarget);
            } else {
                let aaoTarget = [];
                $.each(vueApp.aaoCustomerSample, function(key, val) { //항목 반복
                    $.each(val, function(key2, val2) { //사이즈 반복
                        if (key2 === 0) { //가장 작은 치수라면(가장 왼쪽에 위치)
                            oAppend.optionSize = Number(val2.optionSize) > 80 ? Number(val2.optionSize)-5 : Number(val2.optionSize)-1;
                            oAppend.optionName = val2.optionName;
                            // oAppend.optionValue = Math.round((Number(val2.optionValue) - Number(vueApp.oPlanInfo.jsonFitSpec[key].optionRange)) * 100) / 100; //고객 제공 사이즈 추가시 defualt값 공백. 편차에 따라 default수치를 - 계산
                            oAppend.optionValue = '';
                            oAppend.optionUnit = val2.optionUnit;
                            if (aaoTarget[key] == undefined) aaoTarget[key] = [];
                            aaoTarget[key].push($.copyObject(oAppend));
                            aaoTarget[key].push($.copyObject(val2));
                        } else {
                            aaoTarget[key].push($.copyObject(val2));
                        }
                    });
                });
                vueApp.aaoCustomerSample = $.copyObject(aaoTarget);
            }
            vueApp.checkCustomerSampleSize();
        },
        //확정스펙의 기준사이즈와 고객샘플 제공사이즈 비교 -> 기준사이즈와 일치하는 고객제공사이즈가 있을때만 확정스펙에 고객제공사이즈 비교함
        checkCustomerSampleSize : ()=> {
            //확정스펙LIST에서 모든 고객제공샘플을 보여주기로 해서 이 함수는 기능을 상실함
            // let aCustomerSampleSize = [];
            // $.each(vueApp.aaoCustomerSample, function(key, val) { //항목 반복
            //     $.each(val, function(key2, val2) { //사이즈 반복
            //         if (key === 0) aCustomerSampleSize.push(Number(val2.optionSize));
            //     });
            // });
            // if (aCustomerSampleSize.indexOf(Number(vueApp.oPlanInfo.fitSize)) < 0) vueApp.bFlagShowCompare = false;
            // else vueApp.bFlagShowCompare = true;
        },
        //고객제공샘플에서 제공사이즈 수정시 다른 측정항목의 optionSize도 수정(이 함수 실행전에는 aaoCustomerSample[0][iSizeKey].optionSize 만 수정된 상태)
        changeCustomerSampleSize : (iSizeKey)=>{
            let iChgSize = 0;
            $.each(vueApp.aaoCustomerSample, function(key, val) { //항목 반복
                $.each(val, function(key2, val2) { //사이즈 반복
                    if (key2 == iSizeKey) {
                        if (key === 0) iChgSize = val2.optionSize;
                        else val2.optionSize = iChgSize;
                    }
                });
            });
            vueApp.checkCustomerSampleSize();
        },
        //확정스펙에서 항목명,단위 수정시 고객제공샘플에도 반영
        changeFixedOptionName : (iOptionKey)=>{
            let oChgText = vueApp.oPlanInfo.jsonFitSpec[iOptionKey].optionName;
            $.each(vueApp.aaoCustomerSample, function(key, val) { //항목 반복
                $.each(val, function(key2, val2) { //사이즈 반복
                    if (key == iOptionKey) vueApp.aaoCustomerSample[key][key2].optionName = oChgText;
                });
            });

            vueApp.oPlanInfo.jsonFixedSpec[iOptionKey].optionName = oChgText;
        },
        changeFixedOptionUnit : (iOptionKey)=>{
            let oChgText = vueApp.oPlanInfo.jsonFitSpec[iOptionKey].optionUnit;
            $.each(vueApp.aaoCustomerSample, function(key, val) { //항목 반복
                $.each(val, function(key2, val2) { //사이즈 반복
                    if (key == iOptionKey) vueApp.aaoCustomerSample[key][key2].optionUnit = oChgText;
                });
            });
        },

        //확정스펙에서 측정항목 추가/삭제시 고객제공샘플에도 반영
        addSpecOption : (iKey)=>{
            if (iKey >= 0) {
                vueApp.addElement(vueApp.oPlanInfo.jsonFitSpec, vueApp.ooDefaultJson.jsonFitSpec, 'down', iKey);
                vueApp.addElement(vueApp.oPlanInfo.jsonFixedSpec, vueApp.ooDefaultJson.jsonFixedSpec, 'down', iKey);
            } else {
                vueApp.addElement(vueApp.oPlanInfo.jsonFitSpec, vueApp.ooDefaultJson.jsonFitSpec, 'after');
                vueApp.addElement(vueApp.oPlanInfo.jsonFixedSpec, vueApp.ooDefaultJson.jsonFixedSpec, 'after');
            }

            let aoTmp = vueApp.aaoCustomerSample[0] == undefined ? [{'optionSize':'','optionName':'','optionValue':'','optionUnit':''}] : $.copyObject(vueApp.aaoCustomerSample[0]);
            $.each(aoTmp, function (key, val) {
                aoTmp[key].optionName = '';
                aoTmp[key].optionValue = '';
                aoTmp[key].optionUnit = '';
            });
            //namku(chk) vueApp.addElement(vueApp.aaoCustomerSample, aoTmp, 'down', iKey); 에 문제가 생겨서 addSpecOption() 무조건 iKey = -1 로 호출
            if (iKey >= 0) vueApp.addElement(vueApp.aaoCustomerSample, aoTmp, 'down', iKey);
            else vueApp.aaoCustomerSample.push(aoTmp);
        },
        deleteSpecOption : (iOptionKey)=>{
            vueApp.deleteElement(vueApp.oPlanInfo.jsonFitSpec, iOptionKey);
            vueApp.deleteElement(vueApp.oPlanInfo.jsonFixedSpec, iOptionKey);
            vueApp.deleteElement(vueApp.aaoCustomerSample, iOptionKey);

        },
        //사이즈스펙 관련메소드 end
        save_style_plan : ()=>{
            if (vueApp.oPlanInfo.planConcept == null || vueApp.oPlanInfo.planConcept == '') {
                $.msg('디자인컨셉은 필수 입니다.','','warning');
                return false;
            }

            if (vueApp.oPlanInfo.fitSize == null || vueApp.oPlanInfo.fitSize == '') {
                $.msg('확정스펙의 기준사이즈를 입력하세요','','warning');
                return false;
            }

            if (vueApp.oPlanInfo.jsonFitSpec != null && vueApp.oPlanInfo.jsonFitSpec != '') {
                let sErrMsg = '';
                let aOptionNms = new Array();
                $.each(vueApp.oPlanInfo.jsonFitSpec, function(key, val) {
                    if (val.optionUnit == '') {
                        sErrMsg = '측정항목 중 선택하지 않은 단위가 있습니다.';
                        return false;
                    }
                    if (val.optionName == '') {
                        sErrMsg = '측정항목 중 입력하지 않은 부위명이 있습니다.';
                        return false;
                    }
                    if (aOptionNms.indexOf(val.optionName) !== -1) {
                        sErrMsg = '측정항목 중 중복되는 부위명이 있습니다.';
                        return false;
                    }
                    aOptionNms.push(val.optionName);
                });
                if (sErrMsg != '') {
                    $.msg(sErrMsg,'','warning');
                    return false;
                }
            }

            //고객제공샘플 arr
            let dataCustomerSample = vueApp.customerSampleYn == 'y' ? vueApp.aaoCustomerSample : [];

            $.imsPost('setStylePlan', {'data' : vueApp.oPlanInfo, 'styleSno': vueApp.oPlanInfo.styleSno, 'customerSample':dataCustomerSample}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    $.msg('스타일기획 저장완료','','success').then(()=>{
                        if (parent.opener && typeof parent.opener.listRefreshStylePlan === 'function') {
                            parent.opener.listRefreshStylePlan();
                        }
                        location.href = vueApp.sRedirectUrl + data;
                    });
                });
            });
        },
    };

    const upsertStylePlanComputed = {
        computed_sum_plan_cost() {
            this.oPlanInfo.planPrdCost = Math.round(Number(this.iSumUtilAmt) + Number(this.iSumFabricAmt) + Number(this.iSumSubFabricAmt) + Number(this.iSumMarkAmt) + Number(this.iSumLaborAmt) + Number(this.iSumEtcAmt) + Number(this.oPlanInfo.marginCost) + Number(this.oPlanInfo.dutyCost));
            return this.oPlanInfo.planPrdCost;
        },
    };

    //사이즈스펙CRU팝업창에서 핏 선택하면 아래 함수 실행
    function copyFitSpec(sSno, sName, sSize, aoOption) {
        vueApp.oPlanInfo.fitSpecSno = sSno;
        vueApp.oPlanInfo.fitName = sName;
        vueApp.oPlanInfo.fitSize = sSize;
        vueApp.oPlanInfo.jsonFitSpec = [];
        vueApp.oPlanInfo.jsonFixedSpec = [];
        //고객제공샘플은 핏정보에서 불러와야 된다고 하니 기존에 내용있어도 덮어쓰기
        vueApp.aaoCustomerSample = [];

        if (aoOption == undefined || aoOption.length === 0) {
            vueApp.oPlanInfo.jsonFitSpec.push($.copyObject(vueApp.ooDefaultJson.jsonFitSpec));
            vueApp.oPlanInfo.jsonFixedSpec.push($.copyObject(vueApp.ooDefaultJson.jsonFixedSpec));
            vueApp.aaoCustomerSample.push([{'optionSize':'','optionName':'','optionValue':'','optionUnit':''}]);
        } else {
            $.each(aoOption, function(key, val) {
                vueApp.oPlanInfo.jsonFitSpec.push({'optionName':val.optionName, 'optionRange':val.optionRange, 'optionValue':val.optionValue, 'optionUnit':val.optionUnit});
                vueApp.oPlanInfo.jsonFixedSpec.push({'optionName':val.optionName, 'optionValue':val.optionValue});
                //고객 제공 사이즈 추가시 defualt값 공백.
                vueApp.aaoCustomerSample.push([{'optionSize':sSize,'optionName':val.optionName,'optionValue':'','optionUnit':val.optionUnit}]);
            });
        }
    }

    $(()=>{
        const serviceData = {};

        ImsBoneService.setData(serviceData, Object.assign({}, upsertStylePlanData, materialModuleData));

        ImsBoneService.setMethod(serviceData, Object.assign({}, upsertStylePlanMethods, materialModuleMethods));

        ImsBoneService.setComputed(serviceData, Object.assign({}, upsertStylePlanComputed, materialModuleComputed));

        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            $('.set-dropzone').addClass('dropzone');
            ImsService.setDropzone(vueApp, 'stylePlanFile1', vueApp.uploadAfterActionStylePlan);
            ImsService.setDropzone(vueApp, 'stylePlanFile2', vueApp.uploadAfterActionStylePlan);
            vueApp.revertReferFilePath();

            if (Number(vueApp.oPlanInfo.sno) == 0) {
                vueApp.oPlanInfo.dollerRatio = vueApp.sCurrDollerRatio;
                vueApp.oPlanInfo.dollerRatioDt = vueApp.sCurrDollerRatioDt;
            }

            vueApp.materialModuleInit();
        });

        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
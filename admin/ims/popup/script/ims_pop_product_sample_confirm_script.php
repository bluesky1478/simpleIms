<script type="text/javascript">
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            bFlagEnableConfirm : true, //확정서페이지 진입가능여부 flag. 확정서페이지는 무조건 true
            bFlagChangePlan : false, //스타일기획 변경가능여부 flag. 확정서페이지는 무조건 false
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
                }
            },
            sFocusTable : '',
            iFocusIdx : 0,
            aoGuideList : [],
            aGuideTypeList : [],
            sSchGuideType : '',
            iChooseKeyConfirmGuide : 0,
        });
        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            ImsNkService.getList('productSample', {'upsertSnoGet':'<?=$iSno?>'}).then((data)=>{
                if(200 === data.code){
                    //리뷰서,확정서는 등록페이지가 없다(샘플지시서 등록 === 샘플 등록)
                    vueApp.oUpsertInfo = data.data.list[0];
                    //사이즈스펙 피팅체크. 확인해보기
                    if (vueApp.oUpsertInfo.jsonFitSpec.length == 0) vueApp.oUpsertInfo.jsonConfirmSpec = [];
                    else {
                        let oDefaultForm = {};
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
                        if (vueApp.oUpsertInfo.jsonConfirmSpec == null || vueApp.oUpsertInfo.jsonConfirmSpec.length == 0) {
                            //샘플확정서 최초 저장시
                            vueApp.oUpsertInfo.jsonConfirmSpec = [];
                            $.each(vueApp.oUpsertInfo.jsonFitSpec, function (key, val) {
                                oDefaultForm = $.copyObject(vueApp.ooDefaultJson.jsonConfirmSpec);
                                oDefaultForm.optionName = this.optionName;
                                vueApp.oUpsertInfo.jsonConfirmSpec.push($.copyObject(oDefaultForm));
                            });
                        } else {
                            //샘플지시서에서 사이즈스펙을 수정한 경우를 감안하여 vueApp.oUpsertInfo.jsonConfirmSpec 재구성
                            let oSavedForm = $.copyObject(vueApp.oUpsertInfo.jsonConfirmSpec);
                            vueApp.oUpsertInfo.jsonConfirmSpec = [];
                            $.each(vueApp.oUpsertInfo.jsonFitSpec, function (key, val) {
                                oDefaultForm = $.copyObject(vueApp.ooDefaultJson.jsonConfirmSpec);
                                oDefaultForm.optionName = this.optionName;
                                $.each(oSavedForm, function (key2, val2) {
                                    if (oDefaultForm.optionName == this.optionName) {
                                        oDefaultForm.optionValue = this.optionValue;
                                        return false;
                                    }
                                });
                                vueApp.oUpsertInfo.jsonConfirmSpec.push($.copyObject(oDefaultForm));
                            });
                        }
                    }

                    if (vueApp.oUpsertInfo.jsonReviewCheck == null || vueApp.oUpsertInfo.jsonReviewCheck.length == 0) vueApp.oUpsertInfo.jsonReviewCheck = [];
                    if (vueApp.oUpsertInfo.jsonConfirmSuggest == null || vueApp.oUpsertInfo.jsonConfirmSuggest.length == 0) vueApp.oUpsertInfo.jsonConfirmSuggest = [];
                    if (vueApp.oUpsertInfo.jsonConfirmRequest == null || vueApp.oUpsertInfo.jsonConfirmRequest.length == 0) vueApp.oUpsertInfo.jsonConfirmRequest = [];
                    if (vueApp.oUpsertInfo.jsonConfirmGuide == null || vueApp.oUpsertInfo.jsonConfirmGuide.length == 0) vueApp.oUpsertInfo.jsonConfirmGuide = [];
                    //이미 입력한 안내사항 리스트 가져오기
                    ImsNkService.getList('productSampleGuide', {}).then((data)=> {
                        $.imsPostAfter(data, (data) => {
                            vueApp.aoGuideList = data.list;
                            vueApp.aGuideTypeList = data.type_list;
                        });
                    });
                }
            });


        }); //setMounted end

        ImsBoneService.setComputed(serviceData,{
        });

        ImsBoneService.setMethod(serviceData,{
            //사이즈스펙 피팅 체크 - 실물 샘플 사이즈대로 확정 체크시
            inputSizeSpec : (mChked)=>{
                if(mChked == true) {
                    $.each(vueApp.oUpsertInfo.jsonReviewSpec, function (key, val) {
                        vueApp.oUpsertInfo.jsonConfirmSpec[key].optionValue = this.madeValue;
                    });
                } else {
                    $.each(vueApp.oUpsertInfo.jsonConfirmSpec, function (key, val) {
                        this.optionValue = '';
                    });
                }
            },
            //안내사항 리스트 modal -> 안내사항 클릭시
            putGuideInfo : (oVal)=>{
                vueApp.oUpsertInfo.jsonConfirmGuide[vueApp.iChooseKeyConfirmGuide].guideType = oVal.guideType;
                vueApp.oUpsertInfo.jsonConfirmGuide[vueApp.iChooseKeyConfirmGuide].guideContent = oVal.guideContent;
                $('#modal_sch_list_guide').modal('hide');
            },
            //첨부파일 관련 - 이미지파일이면 true, 아니면 false
            checkImageExtension : (sFileNm)=>{
                const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/i;
                return allowedExtensions.exec(sFileNm);
            },
            //저장
            saveSampleNew : ()=>{
                $.imsPost('saveSampleNk', {'data':vueApp.oUpsertInfo}).then((data) => {
                    $.imsPostAfter(data,(data)=>{
                        $.msg('샘플확정서 정보 저장 완료.', "", "success").then(()=>{
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
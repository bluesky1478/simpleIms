<script type="text/javascript">
    function fnRevertUnitPrice(oTarget) {
        if (oTarget.unitPrice != undefined) oTarget.unitPrice = String(oTarget.unitPrice).replaceAll('\\','').replaceAll(",",'');
    }

    $(()=>{
        const serviceData = {
        };
        ImsBoneService.setData(serviceData,{
            schListNk : schListModalServiceNk.objDefault,

            // isModify : true,
            viewModeSample : 'v',
            sampleView : {
                'fileList' : {
                    'sampleFile1' : {},
                    'sampleFile2' : {},
                    'sampleFile3' : {},
                    'sampleFile4' : {},
                    'sampleFile5' : {},
                    'sampleFile6' : {},
                }
            },
            loadSampleNo : '',
            focusedRow: null,
            subFocusedRow: null,
        });
        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            ImsNkService.getList('productSample', {'upsertSnoGet':'<?=$iSno?>'}).then((data)=>{
                if(200 === data.code){
                    vueApp.sampleView = data.data.list[0];
                    //파일 업로드버튼 셋팅
                    $('.set-dropzone').addClass('dropzone');
                    ImsService.setDropzone(vueApp, 'sampleFile1', vueApp.uploadAfterActionSampleByPopup); //샘플의뢰서
                    ImsService.setDropzone(vueApp, 'sampleFile2', vueApp.uploadAfterActionSampleByPopup); //실물사진
                    ImsService.setDropzone(vueApp, 'sampleFile3', vueApp.uploadAfterActionSampleByPopup); //실패턴
                    ImsService.setDropzone(vueApp, 'sampleFile4', vueApp.uploadAfterActionSampleByPopup); //샘플리뷰서
                    ImsService.setDropzone(vueApp, 'sampleFile5', vueApp.uploadAfterActionSampleByPopup); //기타파일
                    ImsService.setDropzone(vueApp, 'sampleFile6', (tmpFile, dropzoneId)=>{
                        $.msgConfirm('샘플 확정서 등록시 자동으로 고객확정 샘플이 됩니다.', '아니오 선택시 파일업로드가 취소됩니다. 계속 진행하시겠습니까?').then((confirmData)=> {
                            if (true === confirmData.isConfirmed) {
                                //Upload
                                vueApp.uploadAfterActionSampleByPopup(tmpFile, dropzoneId);
                                //고객 확정
                                $.imsPost('confirmSample',{
                                    sampleSno : vueApp.sampleView.sno,
                                    projectSno: vueApp.sampleView.projectSno,
                                    styleSno  : vueApp.sampleView.styleSno,
                                    confirmYn : 'y'
                                }).then(()=>{
                                    parent.opener.location.reload(); //부모창 갱신.
                                    location.reload();
                                });
                            }
                        });
                    }); //샘플확정서
                }
            });
            // vueApp.calc_total();




        }); //setMounted end
        ImsBoneService.setMethod(serviceData,{
            calc_total : () => {
                let sampleUnitAmount = 0;
                const calc = (field) =>{
                    let unitAmount = 0;
                    for(let idx in vueApp.sampleView[field]){
                        const eachValue = vueApp.sampleView[field][idx];
                        const amount = Math.round(Number($.getOnlyNumber(eachValue.meas)) * Number($.getOnlyNumber(eachValue.unitPrice)));
                        vueApp.sampleView[field][idx].price = amount;
                        unitAmount += amount;
                        sampleUnitAmount += amount;
                    }
                    vueApp.sampleView[field+'Cost'] = unitAmount;
                }
                calc('fabric');
                calc('subFabric');

                //기타비용 추가.
                sampleUnitAmount += $.setNumber(vueApp.sampleView.laborCost);
                sampleUnitAmount += $.setNumber(vueApp.sampleView.marginCost);
                sampleUnitAmount += $.setNumber(vueApp.sampleView.dutyCost);
                sampleUnitAmount += $.setNumber(vueApp.sampleView.managementCost);
                //sampleUnitAmount += $.setNumber(vueApp.sampleView.prdMoq);
                //sampleUnitAmount += $.setNumber(vueApp.sampleView.priceMoq);
                sampleUnitAmount += $.setNumber(vueApp.sampleView.addPrice);

                vueApp.sampleView['sampleUnitCost'] = sampleUnitAmount ;
                vueApp.sampleView['sampleCost'] = sampleUnitAmount * vueApp.sampleView['sampleCount'];
                vueApp.sampleView['sampleCost'] += Number($.getOnlyNumber(vueApp.sampleView['addCost']))

                return vueApp.sampleView['sampleCost'];
            },
            focusRowByPopup : (index) =>{
                vueApp.focusedRow = index;
            },
            subFocusRowByPopup : (index) =>{
                vueApp.subFocusedRow = index;
            },
            //파일 업로드할때 실행하는 함수
            uploadAfterActionSampleByPopup : (tmpFile, dropzoneId)=>{
                ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                    let saveData = {
                        customerSno : vueApp.sampleView.customerSno,
                        projectSno : vueApp.sampleView.projectSno,
                        styleSno : vueApp.sampleView.styleSno,
                        eachSno : vueApp.sampleView.sno,
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
            //샘플정보 수정
            saveSampleByPopup : ()=>{
                const saveData = $.copyObject(vueApp.sampleView);
                const isNew = $.isEmpty(saveData.sno);
                saveData.loadSampleNo = vueApp.loadSampleNo;
                $.imsPost('saveSample', saveData).then((data) => {
                    if( 200 === data.code ){
                        vueApp.sampleView.sno = data.data.sno;
                        vueApp.viewModeSample = 'v';
                        $.msg('샘플 저장 완료.', "", "success");
                        vueApp.loadSampleNo = '';
                    }
                });
            },
        });
        ImsBoneService.serviceBegin('data',{sno:0},serviceData);

    });
</script>
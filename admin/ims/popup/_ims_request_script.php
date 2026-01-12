<script type="text/javascript">
    const sno = '<?=$requestParam['sno']?>';
    const projectSno = '<?=$requestParam['projectSno']?>';
    const reqType = '<?=$requestParam['reqType']?>';

    $(appId).hide();

    /**
     * 업로드 후 처리
     * @param tmpFile
     * @param dropzoneId
     */
    const uploadAfterAction = (tmpFile, dropzoneId)=>{
        const saveFileList = [];
        tmpFile.forEach((value)=>{
            saveFileList.push(value);
        });

        $.postAsync('<?=$imsAjaxUrl?>', {
            mode:'saveProjectFiles',
            saveData : {
                projectSno : '<?=$requestParam['projectSno']?>',
                fileDiv : dropzoneId,
                fileList : saveFileList,
            }
        }).then((data)=>{
            vueApp.fileList[dropzoneId] = data.data[dropzoneId];
            vueApp.prepared.contents[dropzoneId] = data.data[dropzoneId].sno;
            $.msg('업로드 완료!', "업로드 후 요청 데이터는 반드시 저장해주세요.", "success");
        });
    }


    /**
     * Validation
     * @param items = prepared
     * @returns {boolean}
     */
    const validation = (items)=>{
        <?php if( $imsProduceCompany ) { ?>
            //생산처일경우 생산국가 선택 필수
            if(  'cost' === reqType && $.isEmpty(items.contents.produceNational)){
                $.msg('생산국가 선택 필수.', "", "warning");
                $('#produceNational').focus();
                return false;
            }
            //생산처 납기일reqType
            if( 'cost' === reqType && ($.isEmpty(items.contents.produceDeliveryDt) || '0000-00-00' === items.contents.produceDeliveryDt) ){
                $.msg('생산처 납기일 필수.', "", "warning");
                $('#produceDeliveryDt').focus();
                return false;
            }
        <?php }else{ ?>
            //이노버에서는 생산타입 선택 필수
            if( 'work' !== reqType && $.isEmpty(items.produceCompanySno) ){
                $.msg('요청 대상 생산처 선택 필수.', "", "warning");
                $('#produceCompanySno').focus();
                return false;
            }
            if( ('cost' === reqType || 'estimate' === reqType ) && 0 == items.contents.produceType){
                $.msg('생산형태 선택 필수.', "", "warning");
                $('#produceType').focus();
                return false;
            }
            if( $.isEmpty(items.deadLineDt) || '0000-00-00' === items.deadLineDt  ){
                $.msg('완료 요청일자 필수.', "", "warning");
                $('#deadLineDt').focus();
                return false;
            }
        <?php } ?>
        return true;
    }


    $(()=>{
        //Load Data.
        ImsService.getDataParams(DATA_MAP.PREPARED,{
            'sno' : sno,
            'projectSno' : projectSno,
            'reqType' : reqType,
        }).then((data)=>{
            console.log('기초데이터(Common)',data);
            const initParams = {
                data : {
                    currentTab : 0,
                    focusedRow : null,
                    subFocusedRow : null,
                    parentMounted : false,
                    items : data.data.customer, //customer.
                    project : data.data.project,
                    prepared : data.data.prepared,
                    fileList : data.data.fileList,
                    productList  : data.data.productList ,
                },
                methods : {
                    isActive : (currentTab, tabIndex)=>{
                        return currentTab === tabIndex ? 'active' : '';
                    },

                    setTab : (tabIndex)=>{
                        vueApp.currentTab = tabIndex;
                    },
                    save : ( items )=>{

                        if( !validation(items) ) return false;

                        $.imsPost('savePreparedReq',{
                            saveData : items,
                        }).then((data)=>{
                            if( 200 === data.code ){
                                $.msg('저장 되었습니다.', "", "success").then(()=>{
                                    parent.opener.location.reload(); //부모창 갱신.
                                    if( $.isEmpty(sno) || 0 == sno ){
                                        self.close();
                                    }
                                });
                            }
                        });
                    },
                    setStatus : (sno, status)=>{
                        $.msgConfirm('상태를 변경 하시겠습니까?','상태 변경 후 수정이 불가합니다.').then(function(result){
                            if( result.isConfirmed ){
                                let updateStatus = {
                                    sno : sno,
                                    preparedStatus : status
                                };
                                if( 2 == status || 1 == status ){
                                    //요청/승인일 경우 저장 먼저.
                                    updateStatus = vueApp.prepared;
                                    if( !validation(updateStatus) ) return false;
                                    updateStatus.preparedStatus = status;
                                }
                                $.imsPost('savePreparedReq',{
                                    saveData : updateStatus
                                }).then((data)=>{
                                    if( 200 === data.code ){
                                        $.msg('저장 되었습니다.', "", "success").then(()=>{
                                            //location.reload(); //부모창 갱신.
                                            parent.opener.location.reload(); //부모창 갱신.
                                            self.close();
                                        });
                                    }
                                });
                            }
                        });
                    },
                    setPreparedStatus : (preparedData, status)=>{
                        let addMessage = '';
                        let acceptTypeKr = '반려';
                        let acceptTypeMemo = '반려 사유 입력';
                        if( 4 === status ){
                            acceptTypeKr = '승인';
                            acceptTypeMemo = '승인 메모';
                        }else if(-2 === status){
                            status = 2;
                            acceptTypeKr = '승인 또는 반력 상태를 번복';
                            acceptTypeMemo = '번복 메모';
                        }else if(-1 === status){
                            status = 1;
                            acceptTypeKr = '처리완료 상태를 번복';
                            acceptTypeMemo = '번복 메모';
                        }

                        if('estimate' === reqType){
                            addMessage = '승인할 경우 스타일 정보가 모두 승인한 가견적 정보로 업데이트 됩니다.';
                        }else if('cost' === reqType){
                            addMessage = '승인할 경우 스타일 정보가 모두 승인한 정보로 업데이트 됩니다.';
                        }

                        $.msgPrompt(acceptTypeKr + ' 처리 하시겠습니까?',addMessage,acceptTypeMemo, (confirmMsg)=>{
                            if( confirmMsg.isConfirmed ){
                                if( $.isEmpty(confirmMsg.value) ){
                                    $.msg('사유/메모 필수.', "", "warning");
                                    return false;
                                }
                                const updateStatus = {
                                    sno : preparedData.sno,
                                    preparedStatus : status,
                                    acceptMemo : confirmMsg.value,
                                };
                                $.imsPost('savePreparedReq',{
                                    saveData : updateStatus
                                }).then((data)=>{
                                    if( 200 === data.code ){
                                        //Sync.
                                        if('estimate' === reqType || 'cost' === reqType ){
                                            $.imsPost('setEstimate',{
                                                sno : preparedData.sno,
                                                projectSno : projectSno,
                                                reqType : reqType,
                                                status : status,
                                            }).then(()=>{
                                                $.msg('처리 되었습니다.', "", "success").then(()=>{
                                                    location.reload();
                                                });
                                            });
                                        }else{
                                            $.msg('처리 되었습니다.', "", "success").then(()=>{
                                                location.reload();
                                            });
                                        }
                                    }
                                });
                            }
                        });
                    },
                    focusRow : (index) =>{
                        vueApp.focusedRow = index;
                    },
                    subFocusRow : (index) =>{
                        vueApp.subFocusedRow = index;
                    },
                    addFabric : ( product )=>{
                        $.imsPost('getFabricSchema',{
                            no : product.fabric[product.fabric.length-1].no,
                            index : product.fabric.length-1
                        }).then((data)=>{
                            product.fabric.push(data.data);
                        });
                    },
                    addSubFabric : ( product )=>{
                        $.imsPost('getSubFabricSchema',{
                            no : product.subFabric[product.subFabric.length-1].no
                        }).then((data)=>{
                            product.subFabric.push(data.data);
                        });
                    },
                    deleteFabric : (data, index)=>{
                        data.splice(index,1);
                    },
                }, mounted : (vueInstance)=>{

                    const setSimpleFile = (fileSno, fieldName)=>{
                        if( !$.isEmpty(fileSno) ){
                            $.imsPost('loadFile',{
                                'sno' : fileSno
                            }).then((data)=>{
                                console.log(data);
                                vueInstance.fileList[fieldName] = data.data[fieldName];
                            });
                        }
                    }

                    //parentMounted
                    //Dropzone 셋팅.
                    $('.set-dropzone').addClass('dropzone');
                    <?php foreach( $PREPARED_FILE as $idx => $prjFileField ) { ?>
                        console.log('<?=$prjFileField['fieldName']?>');
                        setSimpleFile(vueInstance.prepared.contents['<?=$prjFileField['fieldName']?>'], '<?=$prjFileField['fieldName']?>');
                        ImsService.setDropzone(vueInstance, '<?=$prjFileField['fieldName']?>', uploadAfterAction);
                        //업로드한 파일이 있다면 가져오기
                    <?php } ?>

                    <?php if($isProduceCompany) { ?>
                    if( 0 == vueInstance.prepared.preparedStatus ){
                        //자동확인처리
                        $.imsPost('savePreparedReq',{
                            saveData : {
                                sno : sno,
                                preparedStatus : 1,
                            }
                        }).then(()=>{
                            location.reload();
                        });
                    }
                    <?php } ?>

                },
                computed: {
                    total() {
                        let allTotal = 0;
                        if('undefined' !== typeof this.prepared.contents.productList){
                            this.prepared.contents.productList.forEach((value)=>{
                                let total = Number(value.laborCost) + Number(value.marginCost) + Number(value.dutyCost) + Number(value.managementCost)

                                value.fabricCost = 0;
                                for(let idx in value.fabric){
                                    const eachValue = value.fabric[idx];
                                    value.fabric[idx].price = Math.round(Number($.getOnlyNumber(eachValue.meas)) * Number(eachValue.unitPrice));
                                    total += Number(eachValue.price);
                                    value.fabricCost += Number(eachValue.price);
                                }

                                value.subFabricCost = 0;
                                for(let idx in value.subFabric){
                                    const eachValue = value.subFabric[idx];
                                    value.subFabric[idx].price = Math.round(Number($.getOnlyNumber(eachValue.meas) ) * Number(eachValue.unitPrice));
                                    //console.log(eachValue.price);
                                    total += Number(eachValue.price);
                                    value.subFabricCost += Number(eachValue.price);
                                }
                                value.prdCost = total;
                                if( value.targetPrice > 0 && total > 0 ){
                                    value.msMargin = Math.round((value.salePrice - total ) / value.salePrice * 100);
                                }else{
                                    value.msMargin = 0;
                                }
                                allTotal += total;
                            });
                        }
                        return allTotal.toLocaleString();
                    }
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

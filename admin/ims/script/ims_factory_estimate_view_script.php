<script type="text/javascript">
    $(appId).hide();
    $(()=>{
        //Load Data.
        const projectSno = '<?=$requestParam['projectSno']?>';
        const styleSno = '<?=$requestParam['styleSno']?>';
        const sno = '<?=$requestParam['sno']?>';
        const mode = '<?=$requestParam['mode']?>';

        ImsService.getProductData(projectSno, styleSno).then((data)=>{
            console.log('getProductData',data.data);
            const initParams = {
                data : {
                    showImage : false,
                    sampleTabMode : 0, //0: 샘플 , 1: 퀄리티, 2:가견적, 3:생산가, 4:생산
                    focusedRow: null,
                    subFocusedRow: null,
                    items : data.data.customer,
                    project : data.data.project,
                    product : data.data.product, //product = style.
                    fileList : data.data.fileList, //product = style.
                    loadEstimateSno : '',
                    /*가견적 내용*/
                    estimate : data.data.viewDefaultEstimate,

                    //원부자재모듈에 필요한 변수
                    isModify : true,
                    sFocusTable : '',
                    iFocusIdx : 0,
                },
                mounted : ()=>{
                    $('.set-dropzone').addClass('dropzone');
                    ImsService.getData(DATA_MAP.FACTORY_ESTIMATE,sno).then((data)=>{
                        //console.log('factory estimate data....', data.data);
                        vueApp.estimate = $.copyObject(data.data);
                        //console.log('이거 확인', Number(vueApp.estimate.reqStatus));
                        /*if(1 === Number(vueApp.estimate.reqStatus) && ( 0 >= Number(vueApp.estimate.contents.exchange) || isNaN(Number(vueApp.estimate.contents.exchange)) ) ){
                            vueApp.estimate.contents.exchange = '<?=$currencyUsd?>';
                            vueApp.estimate.contents.exchangeDt = '<?=$currencyDate?>';
                        }*/
                        if( Number(vueApp.estimate.contents.exchange) > 0 || 1 !== Number(vueApp.estimate.reqStatus) ){
                        }else{
                            vueApp.estimate.contents.exchange = '<?=$currencyUsd?>';
                            vueApp.estimate.contents.exchangeDt = '<?=$currencyDate?>';
                        }

                        ImsService.setDropzone(vueApp, 'costFile1', (tmpFile)=>{
                            const saveFileList = [];
                            tmpFile.forEach((value)=>{
                                saveFileList.push(value);
                            });
                            vueApp.estimate.reqFiles = saveFileList;
                            vueApp.saveEstimateRes(vueApp.reqStatus);
                        });

                        //원부자재모듈 작동
                        //namkuuuuuu update시 estimate.contents.sno, estimate.contents.customerSno, estimate.contents.styleSno, estimate.contents.dollerRatio 도 contents컬럼에 저장되는지 확인하기
                        vueApp.estimate.contents.sno = sno;
                        vueApp.estimate.contents.customerSno = vueApp.estimate.customerSno;
                        vueApp.estimate.contents.styleSno = styleSno;
                        vueApp.estimate.contents.dollerRatio = '<?=$currencyUsd?>';
                        vueApp.materialModuleInit();
                    });
                },methods : {
                    popupClose : ()=>{
                        $.msgConfirm('현재 스타일 창을 닫으시겠습니까?', '저장 하지 않은 데이터는 사라집니다.').then((confirmData)=> {
                            if (true === confirmData.isConfirmed) {
                                self.close();
                            }
                        });
                    },
                    focusRow : (index) =>{ vueApp.focusedRow = index; },
                    subFocusRow : (index) =>{ vueApp.subFocusedRow = index; },
                    saveEstimateRes :  ImsProductService.saveEstimateRes,
                    saveEstimateResComplete :  ImsProductService.saveEstimateResComplete,
                    loadEstimate : ImsProductService.loadEstimate,
                    download : ()=>{
                        //Not Ajax.
                        location.href="ims_factory_estimate_view.php?simple_excel_download=1&sno=<?=$requestParam['sno']?>";
                    },
                },
                computed: {
                    total() {
                        //원부자재모듈의 sum변수값을 this.estimate.contents. 에 넣기
                        this.estimate.contents.fabricCost = this.iSumFabricAmt;
                        this.estimate.contents.subFabricCost = this.iSumSubFabricAmt;
                        this.estimate.contents.utilCost = this.iSumUtilAmt;
                        this.estimate.contents.markCost = this.iSumMarkAmt;
                        this.estimate.contents.laborCost = this.iSumLaborAmt;
                        this.estimate.contents.etcCost = this.iSumEtcAmt;

                        let totalAmount = 0;
                        // const calc = (field) =>{
                        //     let unitAmount = 0;
                        //     for(let idx in this.estimate.contents[field]){
                        //         const eachValue = this.estimate.contents[field][idx];
                        //         const amount = Math.round(Number($.getOnlyNumber(eachValue.meas)) * Number($.getOnlyNumber(eachValue.unitPrice)));
                        //         this.estimate.contents[field][idx].price = amount;
                        //         unitAmount += amount;
                        //         totalAmount += amount;
                        //     }
                        //     this.estimate.contents[field+'Cost'] = unitAmount;
                        // }
                        // calc('fabric');
                        // calc('subFabric');

                        totalAmount += Number(this.estimate.contents.fabricCost);
                        totalAmount += Number(this.estimate.contents.subFabricCost);
                        totalAmount += Number(this.estimate.contents.utilCost);
                        totalAmount += Number(this.estimate.contents.markCost);
                        //기타비용 추가.
                        totalAmount += Number(this.estimate.contents.laborCost);
                        totalAmount += Number(this.estimate.contents.etcCost);
                        totalAmount += Number(this.estimate.contents.marginCost);
                        totalAmount += Number(this.estimate.contents.dutyCost);
                        totalAmount += Number(this.estimate.contents.managementCost);
                        //totalAmount += Number(this.estimate.contents.prdMoq);
                        //totalAmount += Number(this.estimate.contents.priceMoq);
                        totalAmount += Number(this.estimate.contents.addPrice);

                        this.estimate.contents['totalCost'] = totalAmount;
                        this.estimate.estimateCost = totalAmount;

                        return $.setNumberFormat(this.estimate.contents['totalCost']);
                    }
                }
            };

            //원부자재모듈 data, method, computed 추가
            initParams.data = Object.assign({}, initParams.data, materialModuleData);
            initParams.methods = Object.assign({}, initParams.methods, materialModuleMethods);
            initParams.computed = Object.assign({}, initParams.computed, materialModuleComputed);

            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

<script type="text/javascript">

    const defaultStyleSettingDataSpec = JSON.parse("<?=$defaultStyleSettingDataSpec?>");
    //console.log('스펙정보',defaultStyleSettingDataSpec);

    //스타일기획 리스트 새로고침
    function listRefreshStylePlan(){
        ImsNkService.getStylePlanListRefresh(vueApp.product.sno);
    }
    function refreshSampleList(){
        if( vueApp.product.sno > 0 ){
            ImsProductService.getSampleListAndListRefresh(vueApp.product.sno); //샘플
        }
    }
    function listRefresh(){
        if( vueApp.product.sno > 0 ){
            ImsProductService.getRelatedListAndListRefresh(vueApp.product.sno); //유사연관
            ImsProductService.getSampleListAndListRefresh(vueApp.product.sno); //샘플
            ImsProductService.getFabricListAndListRefresh(vueApp.product.sno); //원단
            ImsProductService.getCostListAndListRefresh(vueApp.product.sno); //생산 견적
            ImsProductionService.getListProduction(1);
            //ImsProductService.getEstimateListAndListRefresh(vueApp.product.sno); //가견적
            //ImsProductService.getProductionListAndListRefresh(vueApp.product.sno); //생산

            ImsNkService.getStylePlanListRefresh(vueApp.product.sno); //스타일기획
        }
    }

    function refreshCostList(){
        ImsProductService.getCostListAndListRefresh(vueApp.product.sno); //생산 견적
    }

    const fabricReqClass = {
        snoList : [],
        reqType : [],
        reqFactory : 0,
        reqDeliveryInfo : '',
        completeDeadLineDt : '',
        fabricReqFile : [],
    }

    $(appId).hide();

    $(()=>{
        //Load Data.
        const projectSno = '<?=$requestParam['projectSno']?>';
        const sno = '<?=$requestParam['sno']?>';
        let tabMode = <?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ?'-1':$requestParam['tabMode']?>;

        <?php if('y' === $imsPageReload) { ?>
            tabMode = $.cookie('imsStyleTabMode'); //이전 tabMode 설정
        <?php }else{ ?>
            $.cookie('imsStyleTabMode', tabMode);
        <?php } ?>

        console.log( '탭모드', tabMode );
        console.log( 'reload : <?=$imsPageReload?>');

        const commonSearchDefault = {
            key : 'cust.customerName',
            keyword : '',
            year : '',
            season : '',
            isComplete : '',
            status : '2',
            page : 1,
            pageNum : 100,
            sort : 'C,asc'
        }
        const searchDefault = $.copyObject(commonSearchDefault);
        searchDefault.productionStatus = 4;
        searchDefault.produceCompanySno = '<?=!empty($imsProduceCompany)? $managerSno :''?>';
        delete searchDefault.status;

        ImsService.getProductData(projectSno, sno).then((data)=>{
            console.log('getProductData',data.data);
            //console.log('viewDefaultProduction',data.data.viewDefaultProduction);
            const initParams = {
                data : {
                    bFlagUpdateRatio : false,
                    visibleWorkOrderPossibleStatus : false,
                    sampleOrderFile : {files:[]} ,
                    loadEstimateCostSno : '',
                    showBeforeInfo : false,
                    isFactory : data.data.isFactory,
                    isList : false,
                    sizeOptionAddType : 'after',  // before, after
                    showImage : false,
                    sampleTabMode : 5, //0: 샘플 , 1: 퀄리티, 2:가견적, 3:생산가, 4:생산, 7:기획
                    tabMode : 0,
                    focusedRow: null,
                    subFocusedRow: null,
                    items : data.data.customer,
                    project : data.data.project,
                    product : data.data.product, //product = style.
                    fileList : data.data.fileList, //product = style.

                    ework : data.data.ework,
                    relatedList : [],
                    /*productApprovalInfo : {
                        'salePrice' : {sno:0}, //판매가
                    },*/
                    /*샘플*/
                    viewModeSample : 'v',
                    loadSampleNo : '',
                    sampleView : $.copyObject(data.data.viewDefaultSample),
                    sampleViewDefault : $.copyObject(data.data.viewDefaultSample),
                    sampleList : [],
                    /*원단*/
                    viewModeFabric : 'v',
                    loadFabricNo : '',
                    fabricView : $.copyObject(data.data.viewDefaultFabric),
                    fabricViewDefault : $.copyObject(data.data.viewDefaultFabric),
                    fabricList : [],
                    fabricReq : $.copyObject(fabricReqClass),
                    /*가견적*/
                    estimateTotal : ImsProductService.getTotalPageDefault(),
                    estimateView : $.copyObject(data.data.viewDefaultEstimate),
                    estimateViewDefault : $.copyObject(data.data.viewDefaultEstimate),
                    estimateList : [],
                    estimateSearchCondition : ImsProductService.getSearchDefault({
                        'styleSno' : sno,
                        page : 1,
                        pageNum : 100,
                    }),
                    /*생산가확정*/
                    costView : $.copyObject(data.data.viewDefaultCost),
                    costViewDefault : $.copyObject(data.data.viewDefaultCost),
                    costList : [],
                    costTotal : ImsProductService.getTotalPageDefault(),
                    costSearchCondition : ImsProductService.getSearchDefault({
                        'styleSno' : sno,
                        'estimateType' : '',
                        page : 1,
                        pageNum : 1000,
                    }),
                    /*생산*/
                    viewModeProduction : 'v',
                    productionView : data.data.viewDefaultProduction,
                    productionList : [],
                    productionCheckList : [],
                    productionTotal : ImsProductService.getTotalPageDefault(),
                    productionSearchCondition : ImsProductService.getSearchDefault({
                        'styleSno' : sno,
                        page : 1,
                        pageNum : 100,
                    }),
                    //productionSearchCondition
                    //sort : 'C,desc'
                    //스타일기획
                    stylePlanList : [],
                    bFlagCallProjectDetail : false,
                },
                mounted : ()=>{
                    vueApp.$nextTick(function () {
                        console.log('MyStyle : ', sno);
                        $.imsPost2('getListStyle',{
                            'projectSno':projectSno,
                            'sort':'S1',
                            'sno':sno,
                            }
                            ,(data)=>{
                            console.log('getStyleData', data.list[0]);
                            vueApp.product.sampleFile1Exsists = data.list[0]['sampleFile1Exsists'];
                            vueApp.product.sampleFile4Exsists = data.list[0]['sampleFile4Exsists'];
                            vueApp.product.sampleFile6Exsists = data.list[0]['sampleFile6Exsists'];
                            vueApp.product.isWorkModify = data.list[0]['isWorkModify'];
                        });

                        /*Object.keys(vueApp.productApprovalInfo).forEach(key=>{
                            ImsTodoService.getApprovalData(key, vueApp.project.sno, vueApp.product.sno, 0).then((data)=>{
                                console.log(`${key} 결재 데이터`, data);
                                vueApp.productApprovalInfo[key] = $.copyObject(data);
                            });
                        });*/


                        //리스트 가져오기.
                        listRefresh();

                        //샘플 파일 업로드 셋팅
                        $('.set-dropzone').addClass('dropzone');

                        //샘플 지시서 (등록시)
                        ImsService.setDropzone(vueApp, 'sampleOrderFile', (tmpFile, dropzoneId)=>{
                            vueApp.sampleOrderFile.files = tmpFile;
                        });

                        ImsService.setDropzone(vueApp, 'sampleFile1', ImsProductService.uploadAfterActionSample); //샘플의뢰서
                        ImsService.setDropzone(vueApp, 'sampleFile2', ImsProductService.uploadAfterActionSample); //실물사진
                        ImsService.setDropzone(vueApp, 'sampleFile3', ImsProductService.uploadAfterActionSample); //실패턴
                        ImsService.setDropzone(vueApp, 'sampleFile4', ImsProductService.uploadAfterActionSample); //샘플리뷰서
                        ImsService.setDropzone(vueApp, 'sampleFile5', ImsProductService.uploadAfterActionSample); //기타파일
                        ImsService.setDropzone(vueApp, 'sampleFile6', (tmpFile, dropzoneId)=>{
                            $.msgConfirm('샘플 확정서 등록시 자동으로 고객확정 샘플이 됩니다.', '아니오 선택시 파일업로드가 취소됩니다. 계속 진행하시겠습니까?').then((confirmData)=> {
                                if (true === confirmData.isConfirmed) {
                                    //Upload
                                    ImsProductService.uploadAfterActionSample(tmpFile, dropzoneId);
                                    //고객 확정
                                    $.imsPost('confirmSample',{
                                        sampleSno : vueApp.sampleView.sno,
                                        projectSno: vueApp.sampleView.projectSno,
                                        styleSno  : vueApp.sampleView.styleSno,
                                        confirmYn : 'y'
                                    }).then(()=>{
                                        parent.opener.location.reload(); //부모창 갱신.
                                    });
                                }
                            });
                        }); //샘플확정서

                        ImsService.setDropzone(vueApp, 'btFile1', ImsProductService.uploadAfterActionFabric); //BT의뢰
                        ImsService.setDropzone(vueApp, 'btFile2', ImsProductService.uploadAfterActionFabric); //BT결과
                        ImsService.setDropzone(vueApp, 'bulkFile', ImsProductService.uploadAfterActionFabric); //BULK결과

                        ImsService.setDropzone(vueApp, 'estimateFile1', ImsProductService.uploadAfterActionEstimate); //견적요청파일
                        ImsService.setDropzone(vueApp, 'costFile1', ImsProductService.uploadAfterActionCost); //확정요청파일

                        ImsService.setDropzone(vueApp, 'fileWork', ImsProductService.uploadAfterActionProduction); //작지
                        ImsService.setDropzone(vueApp, 'fileCareMark', ImsProductService.uploadAfterActionProduction); //캐어라벨
                        ImsService.setDropzone(vueApp, 'filePrdMark', ImsProductService.uploadAfterActionProduction); //캐어라벨
                        ImsService.setDropzone(vueApp, 'filePrdEtc', ImsProductService.uploadAfterActionProduction); //캐어라벨
                        //ImsService.setDropzone(vueApp, 'fileDeliveryPlan', uploadAfterActionCost); //확정요청파일

                        ImsService.setDropzone(vueApp, 'fabricReqFile', (tmpFile)=>{
                            const saveFileList = [];
                            tmpFile.forEach((value)=>{
                                saveFileList.push(value);
                            });
                            vueApp.fabricReq.fabricReqFile = saveFileList;
                        }); //QB의뢰서 파일

                        if( -1 != tabMode ){
                            vueApp.sampleTabMode = Number(tabMode);
                            /*$('html, body').animate({
                                scrollTop: $(document).height()
                            }, 'slow');*/
                        }

                        //FIXME : 테스트
                        //vueApp.openSampleView(-1, 'modify');

                    });
                },methods : {

                    popupClose : ()=>{
                        $.msgConfirm('현재 스타일 창을 닫으시겠습니까?', '저장 하지 않은 데이터는 사라집니다.').then((confirmData)=> {
                            if (true === confirmData.isConfirmed) {
                                self.close();
                            }
                        });
                    },

                    setFabricPass : ImsProductService.setFabricPass,

                    //기존 견적 자료 불러오기
                    loadBeforeEstimateData: ()=>{
                        $.imsPost('loadEstimate', {
                            loadEstimateSno : vueApp.loadEstimateCostSno
                        }).then((data) => {
                            if( 200 === data.code ){
                                const copyFieldList = [
                                    'fabric', 'subFabric', 'jsonUtil', 'jsonMark', 'jsonLaborCost', 'jsonEtc',
                                    'laborCost', 'marginCost', 'dutyCost', 'managementCost',
                                    'prdMoq', 'priceMoq', 'addPrice' ,'deliveryType' , 'produceType', 'producePeriod'
                                ];
                                copyFieldList.forEach((field)=>{
                                    vueApp.sampleView[field] = data.data[field];
                                });
                                vueApp.loadEstimateCostSno = '';
                            }
                        });
                    },
                    changeTab : function(no){
                        vueApp.sampleTabMode = no;
                        $.cookie('imsStyleTabMode', no);
                    },
                    focusRow : (index) =>{
                        vueApp.focusedRow = index;
                    },
                    subFocusRow : (index) =>{
                        vueApp.subFocusedRow = index;
                    },
                    setStyleName : ( product ) =>{
                        if(!$.isEmpty(product.prdStyle)) {
                           product.productName = $('#sel-style option:selected').text();
                        }
                    },
                    setStyleCode : ( product, customerInitial ) =>{
                        let styleCode = [];
                        if(!$.isEmpty(product.prdYear) && "구분없음" !== product.prdYear ) styleCode.push( (''+product.prdYear).substr(2,2) );
                        if(!$.isEmpty(product.prdSeason) && "구분없음" !== product.prdSeason ) styleCode.push( product.prdSeason.toUpperCase() );
                        if(!$.isEmpty(product.prdGender) && "구분없음" !== product.prdGender ) styleCode.push( product.prdGender.toUpperCase() );
                        if(!$.isEmpty(customerInitial)) styleCode.push( customerInitial ); //고객이니셜.
                        if(!$.isEmpty(product.prdStyle) && "구분없음" !== product.prdStyle ) styleCode.push( product.prdStyle.toUpperCase() );
                        if(!$.isEmpty(product.prdColor) && "구분없음" !== product.prdColor ) styleCode.push( product.prdColor.toUpperCase() );
                        if(!$.isEmpty(product.addStyleCode)) styleCode.push( product.addStyleCode.toUpperCase() );
                        product.styleCode = styleCode.join(' ');
                        //vueApp.$forceUpdate();
                    },
                    /**
                     * 썸네일 파일 업로드 (저장 안하면 날아감)
                     */ 
                    uploadFile : (product, fieldName)=>{
                        const fileInput = vueApp.$refs[fieldName];
                        if (fileInput.files.length > 0) {
                            const formData = new FormData();
                            const projectSno = vueApp.project.sno;
                            formData.append('upfile', fileInput.files[0]);
                            $.ajax({
                                url: '<?=$nasUrl?>/img_upload.php?projectSno=' + projectSno ,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(result){
                                    const rslt = JSON.parse(result);
                                    product[fieldName] = '<?=$nasUrl?>'+rslt.downloadUrl;
                                    vueApp.save(product);
                                }
                            });
                        }
                    },
                    deleteThumbnail: (product)=>{
                        $.msgConfirm('썸네일을 삭제 하시겠습니까?','복구 불가.').then(function(result){
                            if( result.isConfirmed ){
                                product.fileThumbnail='';
                                vueApp.save(product);
                            }
                        });
                    },
                    setMargin : (saleCost, prdCost)=>{
                        let margin = 0;
                        if(saleCost>0){
                            margin = Math.round((saleCost-prdCost)/saleCost*100);
                        }
                        return margin;
                    },
                    /**
                     * 상품저장
                     * @param product
                     */
                    save : ( product )=>{
                        //console.log('저장 전 데이터 확인');
                        //console.log(product);
                        product.projectSno = projectSno;
                        //필수값 체크.
                        const requiredField = ['prdStyle','prdSeason','productName'];
                        let isPass = true;
                        requiredField.forEach((checkValue)=>{
                            if($.isEmpty(product[checkValue])){
                                $.msg('필수값 누락, 필수값은 반드시 입력 바랍니다.','','warning');
                                isPass = false;
                                return false;
                            }
                        });

                        if(!isPass) return false;

                        $.postAsync('ims_ps.php', {
                            mode:'saveProduct',
                            saveData : product,
                        }).then((data)=>{
                            console.log('처리 완료');
                            console.log(data);
                            if( 200 === data.code ){
                                $.msg('저장 되었습니다.', "", "success").then(()=>{
                                    window.opener.refreshProject(projectSno);
                                    window.opener.refreshProductList(projectSno);
                                    console.log('save complete...');
                                    if($.isEmpty(sno)){
                                        //parent.opener.location.reload(); //부모창 갱신.
                                        self.close();
                                    }else{
                                        const saveSno = data.data.sno;
                                        //저장 후 판매가가 입력되었을 경우 승인요청 (단 승인요청이나 승인 상태가 아니어야 하며 판매가가 0 이상 )
                                        /*if( product.salePrice > 0 && 'r' != product.priceConfirm && 'p' != product.priceConfirm ){
                                            const requestAcceptData = $.copyObject(product);
                                            requestAcceptData.memo = $.setNumberFormat(product.salpePrice)+'원';
                                            requestAcceptData.sno = saveSno;
                                            ImsService.setNewAccept('r', 'priceConfirm', product, null, null);
                                        }*/
                                    }
                                });
                            }else{
                                $.msg('저장시 오류 발생','개발자에 문의 하세요','warning');
                            }
                        });
                    },
                    //24/01/09 추가.
                    //사이즈 옵션 표준 적용.
                    setStandard : (standardType, element)=>{
                        const standardTypeList = JSON.parse('<?=$sizeOptionStandard?>');
                        element.sizeOption = [];
                        element.sizeOption = standardTypeList[standardType];
                    },
                    /*샘플처리*/
                    saveSample : ImsProductService.saveSample,
                    openSampleView :  ImsProductService.openSampleView,
                    loadSample     :  ImsProductService.loadSample,
                    copySample     :  ImsProductService.copySample,
                    deleteSample   :  ImsProductService.deleteSample,
                    confirmSample  :  ImsProductService.confirmSample,
                    setSampleNothing : ImsProductService.setSampleNothing,
                    /*원단처리*/
                    openFabricReq : ImsProductService.openFabricReq,
                    saveFabricReq : ImsProductService.saveFabricReq,
                    openFabricView :  ImsProductService.openFabricView,
                    saveFabric : ImsProductService.saveFabric,
                    btConfirm : ImsProductService.btConfirm,
                    setRejectQb : ImsProductService.setRejectQb, //반려처리.
                    /*가견적처리*/
                    openEstimateReq : ImsProductService.openEstimateReq,
                    selectEstimate : ImsProductService.selectEstimate,
                    cancelEstimate : ImsProductService.cancelEstimate,
                    /*생산가 확정 처리*/
                    openCostReq : ImsProductService.openCostReq,
                    //saveCostReq : ImsProductService.saveCostReq,
                    selectCost : ImsProductService.selectCost,
                    cancelCost : ImsProductService.cancelCost,
                    /*생산관리 처리*/
                    saveProduction : ImsProductService.saveProduction,
                    openProduction : ImsProductService.openProduction,
                    openQbConfirm : (index, request, reqStatus)=>{
                        vueApp.fabricView = $.copyObject(vueApp.fabricList[index]); //FabricView를 이용
                        vueApp.focusedRow = -1;
                        vueApp.subFocusedRow = -1;
                        vueApp.loadSampleNo = '';

                        vueApp.fabricView.request = request; //확정 정보 용
                        vueApp.fabricView.reqStatus = reqStatus;
                        vueApp.fabricView.request.confirmInfo = '';
                        vueApp.fabricView.request.rejectMemo = '';

                        if( 1 & request.reqType ) vueApp.fabricView.fabricStatus = 5 === reqStatus ? 2 : 4; //5(2확정), 6(4반려) 퀄리티
                        if( 2 & request.reqType ) vueApp.fabricView.btStatus = 5 === reqStatus ? 2 : 4; //5(2확정), 6(4반려) BT
                        if( 4 & request.reqType ) vueApp.fabricView.bulkStatus = 5 === reqStatus ? 2 : 4; //5(2확정), 6(4반려) BULK

                        $('#modalFabricConfirm').modal('show');
                        //ImsProductService.getFabricListAndListRefresh(vueApp.product.sno); //원단
                    },
                    setConfirmInfo : ()=>{
                        let confirmInfo = [];

                        if( 1 & vueApp.fabricView.request.reqType ){
                            confirmInfo.push('<b>퀄리티 확정 : </b>' + vueApp.fabricView.fabricConfirmInfo + ' <span class="text-muted">' + vueApp.fabricView.fabricMemo + '</span>' );
                        }
                        if( 2 & vueApp.fabricView.request.reqType ){
                            confirmInfo.push('<b>BT 확정 : </b>' + vueApp.fabricView.btConfirmInfo + '  <span class="text-muted">' + vueApp.fabricView.btMemo  + '</span>');
                        }
                        if( 4 & vueApp.fabricView.request.reqType ){
                            confirmInfo.push('<b>BULK 확정 : </b>' + vueApp.fabricView.bulkConfirmInfo + '  <span class="text-muted">' + vueApp.fabricView.bulkMemo  + '</span>');
                        }
                        return confirmInfo.join('<br>');
                    },
                    setDefaultBasicConfig : ()=>{
                        const selValue = $('#sel-default-style-config').val();
                        const defaultValue = defaultStyleSettingDataSpec[selValue];
                        vueApp.product.sizeSpec.specRange = defaultValue.specRange;
                        vueApp.product.sizeSpec.standard = defaultValue.standard;
                        vueApp.product.sizeSpec.specData = $.copyObject(defaultValue.specData);
                    },
                    setPassFabric:(isYn)=>{
                        $.imsPost2('setPassFabric',{
                            styleSno:sno,
                            isYn:isYn,
                        }).then(()=>{
                            window.opener.refreshProject(projectSno);
                            window.opener.refreshProductList(projectSno);

                            $.msg('처리되었습니다.','','success');
                        });
                    },
                    //스타일기획 복사(복수)
                    registCopyMultiStylePlan : ()=>{
                        let aChkSnos = [];
                        $('input[name^=stylePlanSno]:checked').each(function(){
                            aChkSnos.push($(this).val());
                        });
                        if( 0 === aChkSnos.length  ){
                            $.msg('복사 대상 기획이 없습니다.','', "warning");
                            return false;
                        }
                        $.msgConfirm('체크하신 스타일기획을 복사 하시겠습니까?','').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('registCopyMultiStylePlan', {'data':aChkSnos}).then((data)=>{
                                    $.imsPostAfter(data,(data)=>{
                                        $('input[name^=stylePlanSno]').prop('checked', false);
                                        ImsNkService.getStylePlanListRefresh(vueApp.product.sno);
                                    });
                                });
                            }
                        });
                    },
                    //스타일기획 삭제(복수)
                    removeMultiStylePlan : ()=>{
                        let aChkSnos = [];
                        $('input[name^=stylePlanSno]:checked').each(function(){
                            aChkSnos.push($(this).val());
                        });
                        if( 0 === aChkSnos.length  ){
                            $.msg('삭제 대상 기획이 없습니다.','', "warning");
                            return false;
                        }
                        $.msgConfirm('체크하신 스타일기획을 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                            if( result.isConfirmed ){
                                ImsNkService.setDelete('bbbbb', aChkSnos).then(()=>{
                                    $('input[name^=stylePlanSno]').prop('checked', false);
                                    ImsNkService.getStylePlanListRefresh(vueApp.product.sno);
                                });
                            }
                        });
                    },
                    //샘플 - 환율 일괄수정
                    modifyDollerRatio : ()=>{
                        let oSendParam = {};
                        if (vueApp.$refs.sampleDollerRatio != undefined && vueApp.$refs.sampleDollerRatio.length > 0) {
                            $.each(vueApp.$refs.sampleDollerRatio, function(key, val) {
                                oSendParam[val.getAttribute('data-sno')] = val.value;
                            });
                        }
                        $.imsPost('modifySampleRatioMulti', {'data':oSendParam}).then((data) => {
                            $.imsPostAfter(data,(data)=>{
                                vueApp.bFlagUpdateRatio = false;
                            });
                        });
                    }
                },
                computed: {
                    total() {
                        let sampleUnitAmount = 0;
                        const calc = (field) =>{
                            let unitAmount = 0;
                            for(let idx in this.sampleView[field]){
                                const eachValue = this.sampleView[field][idx];
                                const amount = Math.round(Number($.getOnlyNumber(eachValue.meas)) * Number($.getOnlyNumber(eachValue.unitPrice)));
                                this.sampleView[field][idx].price = amount;
                                unitAmount += amount;
                                sampleUnitAmount += amount;
                            }
                            this.sampleView[field+'Cost'] = unitAmount;
                        }
                        calc('fabric');
                        calc('subFabric');

                        //기타비용 추가.
                        sampleUnitAmount += $.setNumber(this.sampleView.laborCost);
                        sampleUnitAmount += $.setNumber(this.sampleView.marginCost);
                        sampleUnitAmount += $.setNumber(this.sampleView.dutyCost);
                        sampleUnitAmount += $.setNumber(this.sampleView.managementCost);
                        //sampleUnitAmount += $.setNumber(this.sampleView.prdMoq);
                        //sampleUnitAmount += $.setNumber(this.sampleView.priceMoq);
                        sampleUnitAmount += $.setNumber(this.sampleView.addPrice);

                        this.sampleView['sampleUnitCost'] = sampleUnitAmount ;
                        this.sampleView['sampleCost'] = sampleUnitAmount * this.sampleView['sampleCount'];
                        this.sampleView['sampleCost'] += Number($.getOnlyNumber(this.sampleView['addCost']))
                        
                        return this.sampleView['sampleCost'].toLocaleString();
                    },
                    sizeOptionQtyTotal() {
                        //생산준비
                        return ImsProductionService.sizeOptionQtyTotal(this);
                    },
                    total2() {
                        let total = 0;
                        let fabricCount = 0;
                        let btCount = 0;
                        total = Number(this.product.laborCost) + Number(this.product.marginCost) + Number(this.product.dutyCost) + Number(this.product.managementCost)

                        let fabricCost = 0;
                        for(let idx in this.product.fabric){
                            const eachValue = this.product.fabric[idx];
                            this.product.fabric[idx].price = Math.round(Number($.getOnlyNumber(eachValue.meas)) * Number(eachValue.unitPrice));
                            total += Number(eachValue.price);
                            fabricCost += Number(eachValue.price);

                            if( !$.isEmpty(eachValue['fabricName']) && !$.isEmpty(eachValue['color']) && !$.isEmpty(eachValue['btConfirm']) ){
                                fabricCount++;
                                if('y' === eachValue['btConfirm']) btCount++;
                            }
                        }
                        this.product.fabricCount = fabricCount;
                        this.product.btCount = btCount;
                        this.product.fabricCost = fabricCost;

                        let subFabricCost = 0;
                        for(let idx in this.product.subFabric){
                            const eachValue = this.product.subFabric[idx];
                            this.product.subFabric[idx].price = Math.round(Number((''+eachValue.meas).match(/(\d+\.\d+|\d+)/g)) * Number(eachValue.unitPrice));
                            total += Number(eachValue.price);
                            subFabricCost += Number(eachValue.price);
                        }
                        this.product.subFabricCost = subFabricCost;

                        //this.product.prdCostTmp = total;

                        if( this.product.salePrice > 0 && total > 0 ){
                            this.product.msMargin = Math.round((Number(this.product.salePrice) - total ) / Number(this.product.salePrice) * 100);
                        }else{
                            this.product.msMargin = 0;
                        }
                        return total;
                    }
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

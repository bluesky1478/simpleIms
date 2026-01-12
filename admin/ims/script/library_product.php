<script type="text/javascript">

    function refreshEstimateList(){
        ImsRequestService.getListEstimate(1);
    }

    function refreshCostList(){
        ImsRequestService.getListCost(1);
    }

    /**
     * 스타일/상품 관련 서비스
     */
    const ImsProductService = {
        /**
         * 프로젝트 리스트 반환
         */
        getListStyle : async (projectSno)=>{
            return await $.imsPost2('getListStyle',
                {
                    'projectSno':projectSno,
                    'sort':'S1',
                },()=>{});
        },
        
        /* =============================  [ 기능  ]   ============================= */
        /**
         * 스타일 저장
         */ 
        saveStyleList : async (styleList)=>{
            return await $.imsPost('saveStyleList',{
                'styleList' : styleList,
            });
        },
        /**
         * 스타일 기초 정보 복사
         */ 
        copyStyleBasicInfo : (srcInfo, targetInfo)=>{
            const msg = `${srcInfo.styleCode} 코드의 기초설정 및 전산작지 정보를 ${targetInfo.styleCode}에 등록하시겠습니까?`;
            $.msgConfirm(msg,'').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('copyStyleBasicInfo',{
                        srcSno : srcInfo.sno,
                        targetSno : targetInfo.sno,
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('처리 되었습니다.','', "success");
                            location.reload();
                        }
                    });
                }
            });
        },
        /**
         * 리스트 페이지 Default 값
         */
        getTotalPageDefault : ()=>{
          return $.copyObject({
              idx : 0 ,
              recode : {
                  total : 0
              }
          });
        },
        addQb : (fabric, styleSno)=>{
            $.imsPost('addQb', {
                styleSno : styleSno,
                fabric : fabric,
            }).then((data) => {
                if( 200 === data.code ){
                    $.msg('관리원단 등록 완료.', "", "success");
                    //ImsProductService.getFabricListAndListRefresh(styleSno);
                    try{window.opener.listRefresh();}catch(e){}//팝업형태 갱신
                    try{listRefresh();}catch(e){}//레이어 형태 갱신
                }
            });
        },
        addSubQb : (subFabric, styleSno)=>{
            const fabric = $.copyObject(subFabric);
            fabric.fabricName = subFabric.subFabricName;
            fabric.fabricMix = subFabric.subFabricMix;
            $.imsPost('addQb', {
                styleSno : styleSno,
                fabric : fabric,
            }).then((data) => {
                if( 200 === data.code ){
                    $.msg('관리원단 등록 완료.', "", "success");
                    //ImsProductService.getFabricListAndListRefresh(styleSno);
                    try{window.opener.listRefresh();}catch(e){}//팝업형태 갱신
                    try{listRefresh();}catch(e){}//레이어 형태 갱신
                }
            });
        },
        deleteQb : (qbSno, styleSno)=>{
            $.msgConfirm('QB 정보를 삭제 하시겠습니까?','삭제시 복구불가').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('deleteQb',{
                        sno : qbSno,
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('삭제 되었습니다.','', "success");
                            ImsProductService.getFabricListAndListRefresh(styleSno);
                        }
                    });
                }
            });
        },
        getSearchDefault : (addObject)=>{
            const defaultObject = {
                key : 'cust.customerName',
                keyword : '',
                year : '',
                season : '',
                isComplete : '',
                page : 1,
            }
            return Object.assign({},addObject,defaultObject);
        },
        /* =============================  [ 샘플처리 ]   ============================= */
        /**
        * 샘플없이 진행
        */
        setSampleNothing : (styleSno, projectSno, status)=>{
            let msg = '해당 스타일은 샘플없이 진행합니까?';
            if(-1 !== status){
                msg = '샘플을 진행 하시겠습니까?';
            }

            $.msgConfirm(msg, '').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    $.imsPost('setSampleNothing', {
                        sno:styleSno,
                        projectSno:projectSno,
                        sampleConfirmSno:status,
                    }).then((data) => {
                        if( 200 === data.code ){
                            location.reload();
                            $.msg('처리되었습니다.','','success');
                        }
                    });
                }
            });
        },

        /**
         * 샘플저장
         */
        saveSample : ()=>{
            const saveData = $.copyObject(vueApp.sampleView);
            const isNew = $.isEmpty(saveData.sno);
            saveData.customerSno = vueApp.items.sno;
            saveData.projectSno = vueApp.project.sno;
            saveData.styleSno = vueApp.product.sno;
            saveData.loadSampleNo = vueApp.loadSampleNo;
            $.imsPost('saveSample', saveData).then((data) => {
                //console.log('샘플저장 정보',data);
                if( 200 === data.code ){
                    vueApp.sampleView.sno = data.data.sno;
                    vueApp.sampleList = data.data.list;
                    //console.log('저장 후 갱신 리스트');
                    //console.log(vueApp.sampleList);
                    vueApp.viewModeSample = 'v';
                    if( isNew ){
                        $('#modalSampleView').modal('hide'); //신규면 추가 후 닫기.
                        if(vueApp.sampleOrderFile.files.length > 0 ){
                            //샘플 지시서 파일 등록
                            let saveData = {
                                customerSno : vueApp.items.sno,
                                projectSno : vueApp.project.sno,
                                styleSno : vueApp.product.sno,
                                eachSno : vueApp.sampleView.sno,
                                fileDiv : 'sampleFile1',
                                fileList : vueApp.sampleOrderFile.files,
                                memo : '신규등록',
                            };
                            console.log('저장 파일 정보 확인', saveData);
                            $.imsPost('saveProjectFiles',{
                                saveData : saveData
                            }).then((data)=>{
                                if(200 === data.code) {
                                    //리스트 갱신
                                    ImsProductService.getSampleList(vueApp.product.sno, (list)=>{
                                        vueApp.sampleList = list;
                                        //Sample View...갱신
                                        vueApp.sampleList.forEach((sample)=>{
                                            if( vueApp.sampleView.sno === sample.sno ){
                                                vueApp.sampleView = $.copyObject(sample); // 9 sampleView.sno.
                                            }
                                        });
                                    });
                                }
                            });
                        }
                        $.msg('샘플 추가 완료.', "", "success");
                    }else{
                        $.msg('샘플 저장 완료.', "", "success");
                    }
                    vueApp.loadSampleNo = '';
                }
            });
        },
        /**
         * 샘플리스트 가져오기
         * @param styleSno
         * @param afterProc
         */
        getSampleList : (styleSno, afterProc)=>{
            $.imsPost('getSampleList', {
                'styleSno' : styleSno
            }).then((data) => {
                if( 200 === data.code ) {
                    if( typeof afterProc !== 'undefined' ){
                        afterProc(data.data);
                    }
                }
            });
        },
        getSampleListAndListRefresh : (styleSno) =>{
            console.log( 'getSampleListAndListRefresh' );
            ImsProductService.getSampleList(styleSno, (data)=>{
                vueApp.sampleList = data;
                console.log( '<?=$requestParam['sampleSno']?>' );
                console.log( '<?=!empty($requestParam['sampleSno'])?>' );
                console.log( '<?=-1 == $requestParam['sampleSno']?>' );
                //만일 샘플 열기라면.
                <?php if( !empty($requestParam['sampleSno']) || -1 == $requestParam['sampleSno'] ){ ?>
                    if( -1 === Number('<?=$requestParam['sampleSno']?>') ){
                        ImsProductService.openSampleView(-1, 'modify')
                    }else{
                        for(let idx in vueApp.sampleList){
                            if( <?=$requestParam['sampleSno']?> === Number(vueApp.sampleList[idx].eachSno) ){
                                console.log('A');
                                ImsProductService.openSampleView(idx, 'view');
                            }
                        }
                    }
                <?php } ?>
            });
        },
        /**
         * 유사 연관 리스트
         */
        getRelatedList : (styleSno, afterProc)=>{
            $.imsPost('getRelatedList', {
                'styleSno' : styleSno
            }).then((data) => {
                if( 200 === data.code ) {
                    if( typeof afterProc !== 'undefined' ){
                        afterProc(data.data);
                    }
                }
            });
        },
        getRelatedListAndListRefresh : (styleSno) =>{
            ImsProductService.getRelatedList(styleSno, (data)=>{
                vueApp.relatedList = data;
            });
        },
        /**
         * 샘플 정보 열기
         * @param index
         * @param viewMode
         */
        openSampleView: (index, viewMode)=>{
            //console.log('openSampleView', index);
            vueApp.viewModeSample = viewMode;
            if( -1 !== index ){
                vueApp.sampleView = $.copyObject(vueApp.sampleList[index]);
            }else{
                vueApp.sampleView = $.copyObject(vueApp.sampleViewDefault);
                vueApp.sampleView.sampleName = vueApp.product.styleCode + ' - ' + (vueApp.sampleList.length+1);
                vueApp.viewModeSample = 'm'; //-1 등록은 무조건 수정(등록)모드
            }

            vueApp.focusedRow = -1;
            vueApp.subFocusedRow = -1;
            vueApp.loadSampleNo = '';

            console.log('열린Sample 정보', vueApp.sampleView);

            //샘플 승인정보 불러오기 ( approvalType, projectSno, styleSno, eachSno )
            ImsTodoService.getApprovalData('sampleFile1', vueApp.sampleView.projectSno, vueApp.sampleView.styleSno, vueApp.sampleView.sno).then((data)=>{
                //console.log(`샘플의뢰서 결재 데이터`, data);
                vueApp.sampleView.sampleFile1ApprovalInfo = $.copyObject(data);
                vueApp.$forceUpdate();
            });

            vueApp.sampleOrderFile.files = [];

            $('#modalSampleView').modal('show');
            $('#selSampleFactory').select2({ dropdownParent:$('#modalSampleView')});
            $('#selSampleManager').select2({ dropdownParent:$('#modalSampleView')});

        },
        loadCostEstimate: ()=>{
            $.imsPost('loadCostEstimate', {
                loadCostEstimateNo : vueApp.loadCostEstimateNo
            }).then((data) => {
                $.imsPostAfter(data, (data)=>{
                    vueApp.sampleView.fabric = data.fabric;
                    vueApp.sampleView.subFabric = data.subFabric;
                });
            });
        },
        loadSample: ()=>{
            $.imsPost('loadSample', {
                loadSampleNo : vueApp.loadSampleNo
            }).then((data) => {
                if( 200 === data.code ){
                    vueApp.sampleView = data.data;
                }
            });
        },copySample : ()=>{
            const snoList = [];
            $('input[name*="sampleSno"]:checked').each(function(){
                snoList.push( $(this).val() );
            });
            if( 0 === snoList.length  ){
                $.msg('복사 대상 샘플이 없습니다.','', "warning");
                return false;
            }
            $.msgConfirm(snoList.length + '개의 샘플을 복사 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('copySample',{
                        'styleSno':vueApp.product.sno,
                        'snoList':snoList
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('완료 되었습니다.','', "success").then(()=>{
                                vueApp.sampleList = data.data;
                            });
                        }
                    });
                }
            });
        },deleteSample : ()=>{
            //영구삭제
            const snoList = [];
            $('input[name*="sampleSno"]:checked').each(function(){
                snoList.push( $(this).val() );
            });
            if( 0 === snoList.length  ){
                $.msg('삭제 대상 샘플이 없습니다.','', "warning");
                return false;
            }
            $.msgConfirm(snoList.length + '의 샘플을 삭제 하시겠습니까?','복구불가').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('deleteSample',{
                        'styleSno':vueApp.product.sno,
                        'snoList':snoList
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('삭제 되었습니다.','', "success").then(()=>{
                                vueApp.sampleList = data.data;
                                $('.list-check').prop('checked',false);
                            });
                        }
                    });
                }
            });
        }, confirmSample : (sample, confirmYn)=> {
            let msg = '#' + (Number(sample.sno)+1000) + ' ' + sample.sampleName + ' 샘플을 확정 하시겠습니까?';
            if( 'y' !== confirmYn ){
                msg = '#' + (Number(sample.sno)+1000) + ' ' + sample.sampleName + ' 샘플 확정을 취소 하시겠습니까?';
            }

            $.msgConfirm( msg,'').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('confirmSample',{
                        sampleSno : sample.sno,
                        projectSno: sample.projectSno,
                        styleSno  : sample.styleSno,
                        confirmYn : confirmYn
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('처리 완료.','', "success").then(()=>{
                                vueApp.sampleList = data.data;
                                ImsService.getData(DATA_MAP.PRODUCT, sample.styleSno).then((data)=>{
                                    vueApp.product.sampleConfirmSno = data.data.product.sampleConfirmSno;
                                });
                            });
                        }
                    });
                }
            });
        },

        /* =============================  [ 원단처리(퀄리티&BT) ]   ============================= */

        /**
         * 퀄리티 없이 진행
         */
        setFabricPass : (styleSno, projectSno, status)=>{
            let msg = '해당 스타일은 퀄리티 수배 없이 진행합니까?';
            if('y' !== status){
                msg = '퀄리티 수배를 진행 하시겠습니까?';
            }

            $.msgConfirm(msg, '').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    $.imsPost('setFabricPass', {
                        sno:styleSno,
                        projectSno:projectSno,
                        fabricPass:status,
                    }).then((data) => {
                        if( 200 === data.code ){
                            location.reload();
                            $.msg('처리되었습니다.','','success');
                        }
                    });
                }
            });
        },
        /**
         * 샘플 정보 열기
         * @param index
         * @param viewMode
         */
        openFabricView: (index, viewMode)=>{
            vueApp.viewModeFabric = viewMode;
            if( -1 !== index ){
                vueApp.fabricView = $.copyObject(vueApp.fabricList[index]);
            }else{
                vueApp.fabricView = $.copyObject(vueApp.fabricViewDefault);
                vueApp.viewModeFabric = 'm'; //-1 등록은 무조건 수정(등록)모드
            }

            vueApp.focusedRow = -1;
            vueApp.subFocusedRow = -1;
            vueApp.loadSampleNo = '';

            $('#modalFabricView').modal('show');
        },
        /**
         * 관리 원단 저장.
         */
        saveFabric : ()=>{
            const saveData = $.copyObject(vueApp.fabricView);
            const isNew = $.isEmpty(saveData.sno);
            saveData.customerSno = vueApp.items.sno;
            saveData.projectSno = vueApp.project.sno;
            saveData.styleSno = vueApp.product.sno;

            $.imsPost('saveFabric', saveData).then((data) => {
                //console.log(data);
                if( 200 === data.code ){
                    //vueApp.fabricView.sno = data.data.sno;

                    vueApp.viewModeFabric = 'v';

                    if( isNew ){
                        $('#modalFabricView').modal('hide'); //신규면 추가 후 닫기.
                        $.msg('추가 완료.', "", "success").then(()=>{
                            ImsProductService.getFabricListAndListRefresh(vueApp.product.sno); //원단
                        });
                    }else{
                        //확정 혹은 반려 정보가 있다면 추가 저장.
                        if( typeof saveData.request != 'undefined' ){

                            if( 5 === vueApp.fabricView.reqStatus ){
                                //확정일 경우
                                saveData.request.confirmInfo = vueApp.setConfirmInfo(); //확정 정보
                            }
                            saveData.request.reqStatus = vueApp.fabricView.reqStatus; //확정 정보
                            $.imsPost('updateFabricReq',saveData.request).then(()=>{
                                $.msg('저장 완료.', "", "success").then(()=>{
                                    ImsProductService.getFabricListAndListRefresh(vueApp.product.sno); //원단상태까지 변경하고 닫는다.
                                    $('#modalFabricConfirm').modal('hide');
                                });
                            });
                            $('#modalFabricConfirm').modal('hide');
                        }else{
                            vueApp.fabricView.fabricStatusKr = '상태 수정중...';
                            ImsProductService.getFabricListAndListRefresh(vueApp.product.sno); //원단
                            $.msg('저장 완료.', "", "success").then(()=>{
                                //index 찾아두기
                                let fabricIndex = 0;
                                vueApp.fabricList.forEach((value)=>{
                                    if( value.sno == vueApp.fabricView.sno ){
                                        //View 창 갱신 , index 찾아서
                                        ImsProductService.openFabricView(fabricIndex, 'v');
                                    }
                                    fabricIndex++;
                                });

                            });
                        }
                    }
                    /*vueApp.fabricList = data.data.list;
                    for(const fabricKey in vueApp.fabricList){
                        if( data.data.sno == vueApp.fabricList[fabricKey].sno ){
                            vueApp.fabricView = vueApp.fabricList[fabricKey];
                            break;
                        }
                    }*/
                    vueApp.loadFabricNo = '';
                }
            });
        },
        getFabricList : (styleSno, afterProc)=>{
            $.imsPost('getFabricList', {
                'styleSno' : styleSno
            }).then((data) => {
                if( 200 === data.code ) {
                    if( typeof afterProc !== 'undefined' ){
                        afterProc(data.data);
                    }
                }
            });
        },
        getFabricListAndListRefresh : (styleSno) =>{
            ImsProductService.getFabricList(styleSno,(data)=>{
                //console.log('getFabricListAndListRefresh',data);
                vueApp.fabricList = data;

                /*만일 원단 열기라면*/
                <?php if( !empty($requestParam['fabricSno']) ){ ?>
                for(let idx in vueApp.fabricList){
                    if( <?=$requestParam['fabricSno']?> === Number(vueApp.fabricList[idx].eachSno) ){
                        ImsProductService.openFabricView(idx, 'view');
                    }
                }
                <?php } ?>

            });
        },
        openFabricReq : (type)=>{
            const snoList = ImsService.getSelectSnoList('fabricSno', '요청 대상 원단을 체크해주세요.');
            if( snoList.length > 0 ){
                vueApp.fabricReq = $.copyObject(fabricReqClass);
                vueApp.fabricReq.snoList = snoList;
                $('#modalFabricReq').modal('show');
                $('#selFabricFactory').select2({ dropdownParent:$('#modalFabricReq')});
            }
        },
        saveFabricReq : ()=>{
            const saveData = $.copyObject(vueApp.fabricReq);
            saveData.customerSno = vueApp.items.sno;
            saveData.projectSno = vueApp.project.sno;
            saveData.styleSno = vueApp.product.sno;
            $.imsPost('saveFabricReq', saveData).then((data) => {
                if( 200 === data.code ){
                    vueApp.fabricList = data.data.list;
                    $('#modalFabricReq').modal('hide'); //신규면 추가 후 닫기.
                    $.msg('요청 완료.', "", "success");
                }
            });
        },
        setRejectQb : (sno)=>{
            $.msgConfirm('선택한 원단은 반려처리 하시겠습니까?', '반려후 의뢰처에 재요청시 QB요청 버튼을 이용해 다시 진행하세요').then((confirmData)=>{
                if( true === confirmData.isConfirmed){
                    $.imsPost('setRejectQb', {sno:sno}).then((data) => {
                        if( 200 === data.code ){
                            ImsProductService.getFabricList(vueApp.product.sno,(data)=>{
                                vueApp.fabricList = data;
                            });
                            $.msg('반려 처리됨', "", "success");
                        }
                    });
                }
            });
        },

        /* =============================  [ 통합 생산 가견적 요청창 ]   ============================= */
        openEstimateCostReq : (index, estimateType)=>{
            vueApp.estimateView = $.copyObject(vueApp.estimateViewDefault);
            vueApp.estimateView.estimateType = estimateType;
            if( -1 !== index ){
                vueApp.sampleView = $.copyObject(vueApp.sampleList[index]);
            }else{
                vueApp.sampleView = $.copyObject(vueApp.sampleViewDefault);
            }
            vueApp.focusedRow = -1;
            vueApp.subFocusedRow = -1;
            vueApp.loadSampleNo = '';
            $('#modalEstimateCostReq').modal('show');
        },
        openCostReqByBeforeEstimate : (index, estimateType)=>{
            vueApp.estimateView = $.copyObject(vueApp.estimateViewDefault);
            vueApp.estimateView.estimateType = estimateType;

            if( -1 !== index ){
                //vueApp.sampleView = $.copyObject(vueApp.costList[index]);
                vueApp.loadEstimateCostSno = vueApp.costList[index].sno;
                vueApp.loadBeforeEstimateData();
            }else{
                vueApp.sampleView = $.copyObject(vueApp.sampleViewDefault);
            }

            vueApp.focusedRow = -1;
            vueApp.subFocusedRow = -1;
            vueApp.loadSampleNo = '';
            $('#modalEstimateCostReq').modal('show');
        },
        saveEstimateCostReq : (reqStatus)=>{ //이노버 요청 등록 (estimateView , costView)
            const saveData = $.copyObject(vueApp.estimateView);

            const copyFieldList = [
                'fabric', 'subFabric',
                'laborCost', 'marginCost', 'dutyCost', 'managementCost',
                'prdMoq', 'priceMoq', 'addPrice' ,'deliveryType' , 'produceType', 'producePeriod'
            ];
            copyFieldList.forEach((field)=>{
                saveData[field] = vueApp.sampleView[field];
            });

            saveData.fabric = vueApp.sampleView.fabric; //SampleViwe에 있는 데이터를 넣는다.
            saveData.subFabric = vueApp.sampleView.subFabric;
            saveData.customerSno = vueApp.items.sno;
            saveData.projectSno = vueApp.project.sno;
            saveData.styleSno = vueApp.product.sno;
            saveData.reqStatus = reqStatus;

            $.imsPost('saveEstimateCostReq', saveData).then((data) => {
                if( 200 === data.code ){
                    try{
                        window.opener.refreshProject(vueApp.project.sno);
                        window.opener.refreshProductList(vueApp.project.sno);
                    }catch(e){console.log(e)}

                    try{
                        listRefresh();
                    }catch(e){console.log(e)}

                    $('#modalEstimateCostReq').modal('hide'); //신규면 추가 후 닫기.
                    $.msg(1===reqStatus ? '요청 완료' : '저장 완료', "", "success").then(()=>{
                        if( 'estimate' === vueApp.estimateView.estimateType ){
                            ImsProductService.getEstimateListAndListRefresh(vueApp.product.sno);
                        }else{
                            ImsProductService.getCostListAndListRefresh(vueApp.product.sno);
                        }
                    });
                }
            });
        },

        /* =============================  [ 가견적 ]   ============================= */
        //가견적 불러오기
        loadEstimate: ()=>{
            $.imsPost('loadEstimate', {
                loadEstimateSno : vueApp.loadEstimateSno
            }).then((data) => {
                //console.log(vueApp.estimate);
                //console.log(data.data);
                if( 200 === data.code ){
                    //불러와도 환율과 / 기준환율은 그대로 둔다.
                    const exchange = vueApp.estimate.contents.exchange;
                    const exchangeDt = vueApp.estimate.contents.exchangeDt;
                    vueApp.estimate.contents = data.data;
                    vueApp.estimate.contents.exchange = exchange;
                    vueApp.estimate.contents.exchangeDt = exchangeDt;
                }
            });
        },
        openEstimateReq : ()=>{
            //vueApp.estimateView = $.copyObject(vueApp.estimateViewDefault);
            //$('#modalEstimateReq').modal('show');
            ImsProductService.openEstimateCostReq(-1,'estimate');
        },
        saveEstimateReq : ()=>{ //이노버 요청 등록
            ImsProductService.saveEstimateCostReq('estimateView', ImsProductService.getEstimateListAndListRefresh);
            /*const saveData = $.copyObject(vueApp.estimateView);
            saveData.customerSno = vueApp.items.sno;
            saveData.projectSno = vueApp.project.sno;
            saveData.styleSno = vueApp.product.sno;
            $.imsPost('saveEstimateReq', saveData).then((data) => {
                if( 200 === data.code ){
                    $('#modalEstimateReq').modal('hide'); //신규면 추가 후 닫기.
                    $.msg('요청 완료.', "", "success").then(()=>{
                        ImsProductService.getEstimateListAndListRefresh(vueApp.product.sno);
                    });
                }
            });*/
        },

        selectEstimate : (project, product, sno)=>{ //가견적 선택
            $.msgConfirm('선택한 생산가견적을 적용합니다.','').then(function(result) {
                if( result.isConfirmed ){
                    const params = {
                        sno : sno,
                        projectSno : project.sno,
                        styleSno : product.sno,
                    }
                    $.imsPost('selectEstimate', params).then((data) => {
                        if( 200 === data.code ){
                            $.msg('선택 완료!', "", "success").then(()=>{
                                //product data 갱신.
                                ImsService.getProductData(project.sno, product.sno).then((data)=>{
                                    vueApp.product = data.data.product;
                                });
                                //estimate data 갱신.
                                ImsProductService.getEstimateListAndListRefresh(product.sno);
                            });
                        }
                    });
                }
            });
        },
        cancelEstimate : (project, product, sno)=>{ //가견적 선택
            $.msgConfirm('선택된 가견적을 취소합니다.','').then(function(result) {
                if( result.isConfirmed ){
                    const params = {
                        sno : sno,
                        projectSno : project.sno,
                        styleSno : product.sno,
                    }
                    $.imsPost('cancelEstimate', params).then((data) => {
                        if( 200 === data.code ){
                            $.msg('취소 완료!', "", "success").then(()=>{
                                //product data 갱신.
                                ImsService.getProductData(project.sno, product.sno).then((data)=>{
                                    vueApp.product = data.data.product;
                                });
                                //estimate data 갱신.
                                ImsProductService.getEstimateListAndListRefresh(product.sno);
                            });
                        }
                    });
                }
            });
        },
        reRequest : (sno)=>{ //재요청
            $.msgConfirm('재요청 하시겠습니까?','').then(function(result) {
                if( result.isConfirmed ){
                    const params = {
                        sno : sno,
                        reqStatus : 1,
                    }
                    $.imsPost('setEstimateStatus', params).then((data) => {
                        if( 200 === data.code ){
                            $.msg('처리 완료!', "", "success").then(()=>{
                                refreshEstimateList();
                                refreshCostList();
                            });
                        }
                    });
                }
            });
        },

        saveEstimateRes : (reqStatus)=>{ //생산처 응답 등록
            const saveData = $.copyObject(vueApp.estimate);
            saveData.reqStatus = reqStatus;
            $.imsPost('saveEstimateReq', saveData).then((data) => {
                if( 200 === data.code ){
                    $.msg('처리 완료!', "", "success").then(()=>{
                        parent.opener.refreshEstimateList();
                        //self.close();
                    });
                }
            });

        },
        saveEstimateResComplete : (reqStatus)=>{ //생산처 응답 등록
            let msg = '현재 정보로 처리 완료 하시겠습니까?';
            if( 1 === reqStatus ){
                msg = '생산처에 요청하시겠습니까?';
            }

            let isPass = true;
            if( 'cost' === vueApp.estimate.estimateType &&  3 === reqStatus ){
                //제조국 강제값 처리
                vueApp.estimate.contents.fabric.forEach((fabricData)=>{
                    if(!$.isEmpty(fabricData.no) && $.isEmpty(fabricData.makeNational) ){
                        $.msg(fabricData.no + '  제조국은 필수 입니다.','','warning');
                        isPass = false;
                        return false;
                    }
                });

                //MOQ 필수
                //if( !(Number(vueApp.estimate.contents.prdMoq) > 0 && Number(vueApp.estimate.contents.priceMoq) > 0 && Number(vueApp.estimate.contents.addPrice) > 0) ){
                if( 0 >= Number(vueApp.estimate.contents.priceMoq) ){
                    $.msg('단가MOQ 미달금은 필수입니다.','','warning');
                    isPass = false;
                    return false;
                }

                //생산기간, 운송형태 강제
                if( $.isEmpty(vueApp.estimate.contents.producePeriod) || 0 >= Number(vueApp.estimate.contents.producePeriod) || $.isEmpty(vueApp.estimate.contents.deliveryType) || 0 == vueApp.estimate.contents.deliveryType ){
                    $.msg('생산기간과 운송형태는 필수 입니다.','','warning');
                    isPass = false;
                    return false;
                }

            }

            if( isPass ){
                $.msgConfirm(msg,'처리 후 수정 불가').then(function(result) {
                    if( result.isConfirmed ){
                        vueApp.saveEstimateRes(reqStatus);

                        ImsService.getData(DATA_MAP.FACTORY_ESTIMATE,vueApp.estimate.sno).then((data)=>{
                            vueApp.estimate = $.copyObject(data.data);
                            if( 1 === reqStatus ){
                                $.msg('요청완료','','success').then(()=>{
                                    try{parent.opener.refreshCostList();}catch(e){
                                        console.log(e);
                                        try{parent.opener.listRefresh();}catch(e){
                                            console.log(e);
                                            try{parent.opener.parent.opener.location.reload(); //parent reload 기본.}catch(e){
                                                console.log(e);
                                            }catch (e){console.log(e);}
                                        }
                                    }
                                    self.close();
                                });
                            }else{
                                location.reload();
                            }
                        });
                    }
                });
            }

        },

        getEstimateList : (styleSno, afterProc)=>{
            $.imsPost('getEstimateList', {
                'styleSno' : styleSno
            }).then((data) => {
                if( 200 === data.code ) {
                    //console.log('가견적 리스트 체크 ',data.data);
                    if( typeof afterProc !== 'undefined' ){
                        afterProc(data.data);
                    }
                }
            });
        },
        getEstimateListAndListRefresh : (styleSno) =>{
            ImsProductService.getEstimateList(styleSno,(data)=>{
                vueApp.estimateList = data;
                vueApp.estimateTotal.idx = data.length;
            });
        },
        /* =============================  [ 확정가 등록 ]   ============================= */
        openCostReq : (type)=>{
            //vueApp.costView = $.copyObject(vueApp.costViewDefault);
            //$('#modalCostReq').modal('show');
            ImsProductService.openEstimateCostReq(-1,type);
        },
        saveCostReq : (reqStatus)=>{ //이노버 요청 등록
            const saveData = $.copyObject(vueApp.costView);
            saveData.customerSno = vueApp.items.sno;
            saveData.projectSno = vueApp.project.sno;
            saveData.styleSno = vueApp.product.sno;
            saveData.reqStatus = reqStatus;
            $.imsPost('saveCostReq', saveData).then((data) => {
                if( 200 === data.code ){
                    $('#modalCostReq').modal('hide'); //신규면 추가 후 닫기.

                    $.msg(1===reqStatus ? '요청 완료' : '저장 완료', "", "success").then(()=>{
                        ImsProductService.getCostListAndListRefresh(vueApp.product.sno);
                    });
                }
            });
        },
        getCostList : (styleSno, afterProc)=>{
            $.imsPost('getCostList', {
                'styleSno' : styleSno,
                'estimateType' : vueApp.costSearchCondition.estimateType
            }).then((data) => {
                if( 200 === data.code ) {
                    if( typeof afterProc !== 'undefined' ){
                        afterProc(data.data);
                    }
                }
            });
        },
        getCostListAndListRefresh : (styleSno) =>{
            ImsProductService.getCostList(styleSno,(data)=>{
                vueApp.costList = [];
                data.forEach((costData)=>{
                    costData.fabricView = 'n';
                    costData.subFabricView = 'n';
                    vueApp.costList.push(costData);
                });
                vueApp.costTotal.idx = data.length;
            });
        },
        selectCost : (project, product, sno)=>{ //가견적 선택
            $.msgConfirm('선택한 확정 생산가를 적용합니다.','').then(function(result) {
                if( result.isConfirmed ){
                    const params = {
                        sno : sno,
                        projectSno : project.sno,
                        styleSno : product.sno,
                    }
                    $.imsPost('selectCost', params).then((data) => {
                        if( 200 === data.code ){
                            $.msg('선택 완료!', "", "success").then(()=>{
                                //product data 갱신.
                                ImsService.getProductData(project.sno, product.sno).then((data)=>{
                                    vueApp.product = data.data.product;
                                });
                                //estimate data 갱신.
                                ImsProductService.getCostListAndListRefresh(product.sno);
                            });
                        }
                    });
                }
            });
        },


        /**
         * 확정 생산가 취소
         */
        cancelCost : (project, product, sno)=> { //확정견적 취소
            $.msgConfirm('확정된 생산가를 취소합니다.','').then(function(result) {
                if( result.isConfirmed ){
                    const params = {
                        sno : sno,
                        projectSno : project.sno,
                        styleSno : product.sno,
                    }
                    $.imsPost('cancelCost', params).then((data) => {
                        if( 200 === data.code ){
                            $.msg('취소 완료!', "", "success").then(()=>{
                                //product data 갱신.
                                ImsService.getProductData(project.sno, product.sno).then((data)=>{
                                    vueApp.product = data.data.product;
                                });
                                //estimate data 갱신.
                                ImsProductService.getCostListAndListRefresh(product.sno);
                            });
                        }
                    });
                }
            });
        },

        /* =============================  [ 생산관리 ]   ============================= */
        openProduction : (index, viewMode)=>{
            vueApp.productionView = $.copyObject(vueApp.productionList[index]);
            vueApp.viewModeProduction = viewMode; //-1 등록은 무조건 수정(등록)모드

            //Dropzone이 없다면 셋팅.
            $('.set-dropzone').addClass('dropzone');
            ImsService.setDropzone(vueApp, 'fileWork', ImsProductService.uploadAfterActionProduction); //작지
            ImsService.setDropzone(vueApp, 'fileCareMark', ImsProductService.uploadAfterActionProduction); //작지
            ImsService.setDropzone(vueApp, 'filePrdMark', ImsProductService.uploadAfterActionProduction); //작지
            ImsService.setDropzone(vueApp, 'filePrdEtc', ImsProductService.uploadAfterActionProduction); //작지
            ImsService.setDropzone(vueApp, 'fileWash', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileFabricConfirm', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileFabricShip', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileQc', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileInline', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileShip', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileProductionComplete', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileProductionPacking', ImsProductService.uploadAfterActionProduction); //
            ImsService.setDropzone(vueApp, 'fileProductionInvoice', ImsProductService.uploadAfterActionProduction); //

            vueApp.productionView.ework = null;

            $.imsPost('getEworkData',{
                'styleSno' : vueApp.productionView.styleSno
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.productionView.ework = $.copyObject(data.ework);
                    vueApp.$forceUpdate();
                });
            });

            $('#modalProduction').modal('show');
            setJqueryEvent();
        },
        openProduction2 : (index)=>{
            vueApp.productionView = $.copyObject(vueApp.productionList[index]);
            $('#modalProduction2').modal('show');
        },
        openNewProduction : (styleSno)=>{
            ImsService.getSchema('productionByStyleSno',{
                styleSno : styleSno
            }).then((data)=>{
                if(200 === data.code){
                    //console.log('생산기본구조정보',data.data);
                    let modFlag = false;
                    for(let idx in data.data.sizeOptionQty){
                        if($.isEmpty(idx)){
                            modFlag=true;
                            break;
                        }
                    }

                    if( modFlag ){
                        data.data.sizeOptionQty = {
                            '별첨' : ''
                        }
                    }

                    vueApp.productionView = data.data;
                    vueApp.viewModeProduction = 'm';
                    $('#modalProduction').modal('show');
                }
            });
        },
        saveProduction : (step)=>{ //생산 등록
            const saveData = $.copyObject(vueApp.productionView);
            if(typeof step != 'undefined'){
                saveData.produceStatus = step;
            }
            //console.log('생산 저장 데이터',saveData);
            //saveData.customerSno = vueApp.items.sno;
            //saveData.projectSno = vueApp.project.sno;
            //saveData.styleSno = vueApp.product.sno;
            $.imsPost('saveProduction', saveData).then((data) => {
                if( 200 === data.code ){
                    $.msg('저장 완료.', "", "success").then(()=>{
                        if( !$.isEmpty(vueApp.product.sno) && 0 != vueApp.product.sno ){
                            ImsProductService.getProductionListAndListRefresh(vueApp.product.sno);
                        }else{
                            //ImsProductService.getProductionListAndListRefresh();
                            ImsProductionService.getListProduction();
                        }
                        if( $.isEmpty(vueApp.productionView.sno) || 0 == vueApp.productionView.sno ){
                            $('#modalProduction').modal('hide'); //신규면 추가 후 닫기.
                        }
                        vueApp.viewModeProduction = 'v';
                        //console.log('Complete');
                    });
                }
            });
        },
        getProductionList : (styleSno, afterProc)=>{ //미팅 보고 참고.
            ImsService.getList(DATA_MAP.PRODUCTION,{'styleSno':styleSno}).then((data) => {
                if( 200 === data.code ) {
                    if( typeof afterProc !== 'undefined' ){
                        afterProc(data.data);
                    }
                }
            });
        },
        getProductionListAndListRefresh : (styleSno, afterAction ) =>{
            ImsProductService.getProductionList(styleSno,(data)=>{
                //console.log('생산정보',data);
                vueApp.productionList = data.list;
                if( typeof afterAction != 'undefined' ){
                    afterAction();
                }
            });
        },
        //승인 (작지, 아소트)
        setProductionFieldConfirm : ()=>{
            //생산 리스트 갱신
            ImsProductService.getProductionListAndListRefresh(vueApp.product.sno,()=>{
                //프로젝트 뷰 화면 갱신
                vueApp.productionList.forEach((each)=>{
                    if( each.sno == vueApp.productionView.sno ){
                        vueApp.productionView = $.copyObject(each);
                        return false;
                    }
                });
            });
        },
        //승인 (생산파일)
        setProductionFileConfirm : (target, confirmData)=>{
            ImsProductionService.getListProduction();
            ImsService.getList('production',{sno : vueApp.productionView.sno}).then((data)=>{
                if(200 === data.code){
                    vueApp.productionView = data.data.list[0];
                    if( 'fabricShipConfirm' === target && 'p' === confirmData.data.acceptValue ){
                        $.msgConfirm('현재 승인일을 완료일로 처리하시겠습니까?','변경시 새로고침 후 확인 가능').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('setFabricShipConfirmCompleteDt', {
                                    'sno':confirmData.data.condition.eachSno
                                }).then((data)=>{
                                    $.imsPostAfter(data,()=>{
                                        $.msg(data.message,'','success');
                                    });
                                });
                            }
                        });
                    }
                }
            });
        },
        /**============  [ 파일업로드 ]   ============================= */
        /**
         * 생산 스케쥴 상태 변경
         */
        setScheduleReq : (status ,productionSno)=>{
            $.imsPost('setScheduleReq',{
                sno : productionSno,
                status : status,
            }).then((data)=>{
                if(200 === data.code) {
                    //리스트 갱신
                    ImsProductService.setProductionFieldConfirm();
                }
            });
        },

        /* =================
         * 스타일 부분 업로드 작업
         * @param tmpFile
         * @param dropzoneId
         * @param afterAction
         */
        uploadAfterAction : (tmpFile, dropzoneId, afterAction)=>{
            const saveFileList = [];
            let promptValue = '';
            promptValue = window.prompt("메모입력 : ");
            tmpFile.forEach((value)=>{
                saveFileList.push(value);
            });
            afterAction(saveFileList, promptValue);
        },

        uploadAfterActionWithoutMemo : (tmpFile, dropzoneId, afterAction)=>{
            const saveFileList = [];
            tmpFile.forEach((value)=>{
                saveFileList.push(value);
            });
            afterAction(saveFileList, '');
        },

        /**
         * 샘플 업로드 작업
         * @param tmpFile
         * @param dropzoneId
         */
        uploadAfterActionSample : (tmpFile, dropzoneId)=>{
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
                        ImsProductService.getSampleList(vueApp.product.sno, (list)=>{
                            vueApp.sampleList = list;
                            //Sample View...갱신
                            vueApp.sampleList.forEach((sample)=>{
                                if( vueApp.sampleView.sno === sample.sno ){
                                    vueApp.sampleView = $.copyObject(sample); // 9 sampleView.sno.
                                }
                            });
                        });
                    }
                });
            });
        },
        /**
         * 원단관리 업로드 작업
         * @param tmpFile
         * @param dropzoneId
         */
        uploadAfterActionFabric : (tmpFile, dropzoneId)=>{
            ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                let saveData = {
                    customerSno : vueApp.fabricView.customerSno,
                    projectSno : vueApp.fabricView.projectSno,
                    styleSno : vueApp.fabricView.styleSno,
                    eachSno : vueApp.fabricView.sno,
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
                        ImsProductService.getFabricList(vueApp.product.sno, (list)=>{
                            vueApp.fabricList = list;
                            //fabric View...갱신
                            vueApp.fabricList.forEach((fabric)=>{
                                if( vueApp.fabricView.sno === fabric.sno ){
                                    vueApp.fabricView = $.copyObject(fabric); // 9 fabricView.sno.
                                }
                            });

                        });
                    }
                });
            });
        },
        /**
         * 생산 파일 업로드
         */ 
        uploadAfterActionProduction : (tmpFile, dropzoneId)=>{
            const confirmMap = {
                fileWork : 'workConfirm',
                fileWash : 'washConfirm',
                fileFabricConfirm : 'fabricConfirmConfirm',
                fileFabricShip : 'fabricShipConfirm',
                fileQc : 'qcConfirm',
                fileInline : 'inlineConfirm',
                fileShip : 'shipConfirm',
                fileProductionComplete : 'productionComplete',
                fileProductionPacking : 'fileProductionPacking',
                fileProductionInvoice : 'fileProductionInvoice',
            };

            ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                let saveData = {
                    customerSno : vueApp.productionView.customerSno,
                    projectSno : vueApp.productionView.projectSno,
                    styleSno : vueApp.productionView.styleSno,
                    eachSno : vueApp.productionView.sno,
                    fileDiv : dropzoneId,
                    fileList : saveFileList,
                    memo : promptValue,
                };
                //console.log(saveData);
                $.imsPost('saveProjectFiles',{
                    saveData : saveData
                }).then((data)=>{
                    if(200 === data.code) {

                        //이게 리스트냐. View에 따라서 다르게 처리 되어야함.
                        if( !$.isEmpty(confirmMap[dropzoneId]) ){
                            //파일 업로드 후 작업지시서 승인요청 상태로 변경.
                            const acceptSaveData = $.copyObject(saveData);
                            acceptSaveData.memo += ' (파일 등록으로 자동 승인 요청)';
                            acceptSaveData.sno = acceptSaveData.eachSno;

                            let uploadAfterFnc = null;
                            if(!vueApp.isList){
                                uploadAfterFnc = ()=>{
                                    //원래 있던 리스트 갱신하고 거기있는 오브젝트를 가져온다.
                                    ImsProductService.getProductionListAndListRefresh(vueApp.product.sno,()=>{
                                        vueApp.productionList.forEach((each)=>{
                                            if( vueApp.productionView.sno === each.sno ){
                                                vueApp.productionView = $.copyObject(each); // 9 sampleView.sno.
                                            }
                                        });
                                    });
                                }
                            }else{
                                uploadAfterFnc = ()=>{
                                    //오브젝트 다시 불러오기. sno.
                                    ImsProductionService.getListProduction();
                                    ImsService.getList('production',{sno : vueApp.productionView.sno}).then((data)=>{
                                        if(200 == data.code){
                                            //console.log('생산 데이터 다시 가져오기.', data.data);
                                            vueApp.productionView = data.data.list[0]; //confirm.. re..
                                        }
                                    });
                                }
                            }
                            //console.log( acceptSaveData );
                            if( 'fileShip' === dropzoneId ){
                                ImsService.setNewAccept('p',confirmMap[dropzoneId], acceptSaveData, uploadAfterFnc);
                            }else{
                                ImsService.setNewAccept('r',confirmMap[dropzoneId], acceptSaveData, uploadAfterFnc);
                            }
                        }else{
                            if(!vueApp.isList){
                                //원래 있던 리스트 갱신하고 거기있는 오브젝트를 가져온다.
                                ImsProductService.getProductionListAndListRefresh(vueApp.product.sno,()=>{
                                    vueApp.productionList.forEach((each)=>{
                                        if( vueApp.productionView.sno === each.sno ){
                                            vueApp.productionView = $.copyObject(each); // 9 sampleView.sno.
                                        }
                                    });
                                });
                            }else{
                                
                                //오브젝트 다시 불러오기. sno.
                                ImsProductionService.getListProduction();
                                ImsService.getList('production',{sno : vueApp.productionView.sno}).then((data)=>{
                                    if(200 == data.code){
                                        //console.log('생산 데이터 다시 가져오기.', data.data);
                                        vueApp.productionView = data.data.list[0]; //confirm.. re..
                                    }
                                });

                            }
                        }
                    }
                });
            });
        },
        /**
         * 생산견적 요청 업로드.
         * @param tmpFile
         * @param dropzoneId
         */
        uploadAfterActionEstimate : (tmpFile)=>{
            const saveFileList = [];
            tmpFile.forEach((value)=>{
                saveFileList.push(value);
            });
            vueApp.estimateView.reqFiles = saveFileList;
        },

        /**
         * 확정가 요청 업로드
         * @param tmpFile
         */
        uploadAfterActionCost : (tmpFile)=>{
            const saveFileList = [];
            tmpFile.forEach((value)=>{
                saveFileList.push(value);
            });
            vueApp.estimateView.reqFiles = saveFileList;
        },

        fabricDownload : (fileName)=>{
            // HTML 테이블 가져오기
            const table = document.getElementById('myTable');
            // 테이블 데이터를 엑셀 시트로 변환
            const workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

            for(let i=2; 100>=i; i++){
                try{
                    delete workbook.Sheets.Sheet1['L'+i];
                }catch (e){}
            }
            // 엑셀 파일 다운로드
            XLSX.writeFile(workbook, `${fileName}_원부자재.xlsx`);
        },

        addSalesStyle : ()=>{
            //스타일 구조를 가져와야한다.
            $.imsPost('getProductDefaultScheme',{
                sno:-1
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    //vueApp.isModify = true;
                    vueApp.isStyleModify = true;
                    const defaultStyle = $.copyObject(data);
                    //console.log( 'season', vueApp.mainData.projectSeason );
                    defaultStyle.customerSno = vueApp.mainData.customerSno;
                    defaultStyle.projectSno = vueApp.mainData.sno;
                    defaultStyle.prdYear = '20'+vueApp.mainData.projectYear;
                    defaultStyle.prdSeason = vueApp.mainData.projectSeason;
                    defaultStyle.customerDeliveryDt = vueApp.mainData.customerDeliveryDt; //고객납기
                    defaultStyle.msDeliveryDt = vueApp.mainData.msDeliveryDt; //MS납기
                    vueApp.productList.push(defaultStyle);
                    setTimeout(() => {
                        $('#btn-style-save').focus();
                    }, 1);
                });
            });
        },
        setStyleName : (product)=>{
            if(!$.isEmpty(product.prdStyle)) {
                //product.productName = $('#sel-style option:selected').text();
                let seasonName = '';
                if( !$.isEmpty(product.prdSeason) && 'ALL' !== product.prdSeason ){
                    seasonName = JS_LIB_CODE['codeSeason'][product.prdSeason] + ' ';
                }
                product.productName =  seasonName + styleMap[product.prdStyle];
            }

            console.log('#### SetStyleName');
            console.log(product);
            console.log(product.productName);

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

            console.log('#### SetStyleCode');
            console.log(customerInitial);
            console.log(product);
            console.log(product.styleCode);

            //vueApp.$forceUpdate();
        },
    }

    /**
     * 전산 작지 서비스
     * @type {{afterApprovalModify: ImsEworkService.afterApprovalModify}}
     */
    const ImsEworkService = {
        afterApprovalModify : (styleSno, status, reason)=>{
            const params = {
                styleSno : styleSno,
                status : status,
                reason : reason,
            };
            $.imsPost('afterApprovalModify', params).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    $.msg('처리 되었습니다.','','success').then(()=>{
                        location.reload();
                    });
                });
            });
        },
        //작업 지시서 저장
        saveEwork : (prdInfo, eworkData, orgInfo)=>{
            const params = {
                styleSno : prdInfo.sno,
                prdInfo : $.getObjectDiff(orgInfo.product,prdInfo),
                eworkData : $.getObjectDiff(orgInfo.ework,eworkData),
                fabricList : $.getObjectDiff(orgInfo.product.fabricList, vueApp.mainData.product.fabricList),
                subFabricList : $.getObjectDiff(orgInfo.product.subFabricList,vueApp.mainData.product.subFabricList),
            };
            //Params에 생산 유의 사항이 들어 있으면 있는 그대로 넣는다.
            if (typeof params.eworkData.data !== 'undefined' && typeof params.eworkData.data.produceWarning !== 'undefined') {
                params.eworkData.data.produceWarning = eworkData.data.produceWarning; //생산 유의사항은 수정사항이 있으면 그대로 넣는다.
            }
            //Params에 Fabric이 들어있으면 그대로 넣는다.
            if ( !$.isEmptyObject(params.fabricList) || !$.isEmptyObject(params.subFabricList)) {
                params.fabricList = vueApp.mainData.product.fabricList;
                params.subFabricList = vueApp.mainData.product.subFabricList;
            }

            if( $.isEmptyObject(params.eworkData) && $.isEmptyObject(params.prdInfo) && $.isEmptyObject(params.fabricList)  && $.isEmptyObject(params.subFabricList) ){
                $.msg('변경사항 없음','','warning');
            }else{
                $.imsPost('saveEwork', params).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('저장 되었습니다.','','success').then(()=>{
                            location.reload();
                        });
                    });
                });
            }
        },
    };

</script>
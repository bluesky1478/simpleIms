<script type="text/javascript">
    /**
     *  프로젝트 VIEW 메소드
     */
    const viewMethods = {

        setSampleNothing : ImsProductService.setSampleNothing,

        /**
         * 투입 예정 디자이너 등록
         */ 
        addExtDesigner:()=>{
            if( $.isEmpty(vueApp.designManager) ){
                $.msg('디자이너를 선택해주세요.','','warning');
            }else{
                vueApp.project.extDesigner.push(vueApp.designManager);
            }
        },

        deleteExtDesigner(designer) {
            this.project.extDesigner = this.project.extDesigner.filter(d => d !== designer);
        },

        /**
         * 프로젝트 저장
         */
        save : ()=>{
            ImsCustomerService.save(vueApp.customer);
            ImsProjectService.updateProject(vueApp.project).then((data)=>{
                refreshProject(sno);
                refreshProductList(sno);
                vueApp.isModify = false;
            });
        },

        /**
         * 수정 모드
         */
        setModify : (bool)=>{
            vueApp.isModify = bool;
            if(bool){
                setJqueryEvent();
            }
        },
        /**
         * 영업 상태 변경
         */
        setSalesStatus : ()=>{
            const salesStatus = vueApp.project.salesStatus;
            $.imsPost2('setSalesStatus',{
                projectSno:vueApp.project.sno,
                salesStatus:salesStatus,
            }).then(()=>{
                vueApp.initSalesStatus = salesStatus;
                $.msg('상태변경 완료','','success');
            });
        },

        /**
         * 상품 순서 변경
         */
        changeProductList : ()=>{
            vueApp.saveStyleList(false);
        },


        /**
         * 스타일 저장
         */
        saveStyleList : (isMessage)=>{
            ImsProductService.saveStyleList(vueApp.productList).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    if( isMessage ){
                        vueApp.isStyleModify = false;
                        refreshProductList(sno);
                        $.msg('저장 되었습니다.','','success');
                    }
                });
            });
        },

        /**
         * 탭 변경
         */
        changeTab : (tabName)=>{
            vueApp.tabMode = tabName;
            $.cookie('viewTabMode', tabName);
            if( 'comment' === tabName && false === vueApp.isSetCommentEditor ){
                setCommentEditor();
            }

        },
        changeFileTab : (tabName)=>{
            vueApp.fileTabMode = tabName;
        },

        //부가판매/매입 리스트 가져오기
        getAddedBSList : (iProjectSno)=>{
            vueApp.addedSaleList = [];
            vueApp.addedBuyList = [];
            let oList = ImsNkService.getList('addedBS', {mode:'getListAddedBS', project_sno:iProjectSno});
            oList.then((data)=>{
                $.each(data.data, function(key, val) {
                    val.typingBuyAmt = '';
                    val.typingBuyAmtType = 'n';
                    if (val.addedType == 1) { //부가판매
                        val.typingSaleAmt = '';
                        val.typingSaleAmtType = 'n';
                        vueApp.addedSaleList.push(val);
                    } else if (val.addedType == 2) vueApp.addedBuyList.push(val);
                });
            });
        },
        //부가판매,구매 추가/수정시 금액 입력하면 vat 계산/미계산
        calc_not_vat_amt : (iType, iKey)=>{
            let oTarget = {};
            let sChangeFldNm = '';
            switch(iType) {
                case 1: //부가판매 판매단가
                    oTarget = vueApp.addedSaleList;
                    sChangeFldNm = 'Sale';
                    break;
                case 2: //부가판매 구매단가
                    oTarget = vueApp.addedSaleList;
                    sChangeFldNm = 'Buy';
                    break;
                default: //부가구매 구매단가
                    oTarget = vueApp.addedBuyList;
                    sChangeFldNm = 'Buy';
                    break;
            }
            let iAmt = Number(oTarget[iKey]['typing'+sChangeFldNm+'Amt']);
            if (oTarget[iKey]['typing'+sChangeFldNm+'AmtType'] == 'y') {
                oTarget[iKey]['added'+sChangeFldNm+'Amount'] = iAmt - Math.floor(iAmt*10/110);
            } else {
                oTarget[iKey]['added'+sChangeFldNm+'Amount'] = iAmt;
            }
        },

        //부가판매 추가
        addAddedSale : ()=>{
            $.imsPost('getTableSchemeAddedBuySale', {}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.isModifyAddedSale = true;
                    let defaultRowAddedBuySale = $.copyObject(data);
                    defaultRowAddedBuySale.projectSno = vueApp.project.sno;
                    defaultRowAddedBuySale.addedType = 1;
                    defaultRowAddedBuySale.typingSaleAmt = '';
                    defaultRowAddedBuySale.typingSaleAmtType = 'n';
                    defaultRowAddedBuySale.typingBuyAmt = '';
                    defaultRowAddedBuySale.typingBuyAmtType = 'n';
                    vueApp.addedSaleList.push(defaultRowAddedBuySale);
                });
            });
        },
        //부가구매 추가
        addAddedBuy : ()=>{
            $.imsPost('getTableSchemeAddedBuySale', {}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.isModifyAddedBuy = true;
                    let defaultRowAddedBuySale = $.copyObject(data);
                    defaultRowAddedBuySale.projectSno = vueApp.project.sno;
                    defaultRowAddedBuySale.addedType = 2;
                    defaultRowAddedBuySale.typingBuyAmt = '';
                    defaultRowAddedBuySale.typingBuyAmtType = 'n';
                    vueApp.addedBuyList.push(defaultRowAddedBuySale);
                });
            });
        },

        //부가판매 정보만 저장(upsert)
        save_added_sale : ()=>{
            if (vueApp.addedSaleList.length === 0) {
                $.msg('저장할 부가판매정보가 없습니다.','','error');
                return false;
            }
            let bFlagErr = false;
            $.each(vueApp.addedSaleList, function (key, val) {
                if (val.addedName == null || val.addedName == '') {
                    bFlagErr = true;
                    return false;
                }
            });
            if (bFlagErr === true) {
                $.msg('부가판매정보의 항목명은 필수입니다.','','error');
                return false;
            }

            $.imsPost('setAddedBS', {'data' : vueApp.addedSaleList}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.isModifyAddedSale = false;
                    vueApp.getAddedBSList(vueApp.project.sno);
                });
            });
        },
        //부가구매 정보만 저장(upsert)
        save_added_buy : ()=>{
            if (vueApp.addedBuyList.length === 0) {
                $.msg('저장할 부가구매정보가 없습니다.','','error');
                return false;
            }
            let bFlagErr = false;
            let sErrMsg = '';
            $.each(vueApp.addedBuyList, function (key, val) {
                if (val.addedName == null || val.addedName == '') {
                    bFlagErr = true;
                    sErrMsg = '부가구매정보의 항목명은 필수입니다.';
                    return false;
                }
                if (val.buyManagerSno == -1 && val.buyManagerSnoHan == '') {
                    bFlagErr = true;
                    sErrMsg = '매입처 새로등록시 매입처명을 입력하셔야 합니다.';
                    return false;
                }
            });
            if (bFlagErr === true && sErrMsg != '') {
                $.msg(sErrMsg,'','error');
                return false;
            }

            $.imsPost('setAddedBS', {'data' : vueApp.addedBuyList}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.isModifyAddedBuy = false;
                    vueApp.getAddedBSList(vueApp.project.sno);
                });
            });
        },
        //부가판매/구매 삭제
        deleteAddedBS : (sno)=>{
            $.msgConfirm('부가건을 삭제 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    ImsNkService.setDelete('aaaaa', sno).then(()=>{
                        vueApp.getAddedBSList(vueApp.project.sno);
                    });
                }
            });
        },

        //최초기획일정 가져오기
        getProjectPlanScheList : (iProjectSno)=>{
            vueApp.oPlanScheList = {};
            ImsNkService.getList('projectPlanSche', {mode:'getListProjectPlanSche', project_sno:iProjectSno}).then((data)=>{
                $.each(data.data, function(key, val) {
                    $.each(val, function(key2, val2) {
                        if (val2 != '') {
                            vueApp.bFlagExistPlanSche = true;
                            return false;
                        }
                    });
                    if (vueApp.bFlagExistPlanSche === true) return false;
                });
                vueApp.oPlanScheList = data.data;
                if (vueApp.project.planScheMemo != null) vueApp.project.planScheMemo = vueApp.project.planScheMemo.replaceAll('\\n', '\n').replaceAll('\\', '');
            });
        },
        //최초기획일정 저장(upsert)
        save_plan_sche : ()=>{
            $.imsPost('setProjectPlanSche', {'data' : vueApp.oPlanScheList, project_sno:vueApp.project.sno, memo:vueApp.project.planScheMemo}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.bFlagExistPlanSche = true;
                    vueApp.isModifyPlan = false;
                });
            });
        },

        /**
         * 영업 스타일 추가
         */
        addSalesStyle : ()=>{
            //console.log('Add sales style...');
            //스타일 구조를 가져와야한다.
            $.imsPost('getProductDefaultScheme',{
                sno:-1
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    //vueApp.isModify = true;
                    vueApp.isStyleModify = true;
                    const defaultStyle = $.copyObject(data);
                    //console.log( 'season', vueApp.project.projectSeason );
                    defaultStyle.customerSno = vueApp.project.customerSno;
                    defaultStyle.projectSno = vueApp.project.sno;
                    defaultStyle.prdYear = '20'+vueApp.project.projectYear;
                    defaultStyle.prdSeason = vueApp.project.projectSeason;
                    
                    defaultStyle.customerDeliveryDt = vueApp.project.customerDeliveryDt; //고객납기
                    defaultStyle.msDeliveryDt = vueApp.project.msDeliveryDt; //MS납기
                    
                    //console.log('Array 정보',vueApp.productList);
                    //console.log('기본 스타일 정보',defaultStyle);
                    //console.log('추가 정보',defaultStyle.addedInfo);
                    vueApp.productList.push(defaultStyle);
                    setTimeout(() => {
                        $('#btn-style-save').focus();
                    }, 1);
                });
            });
        },
        /**
         * 일괄 수정
         */
        batchModify : (model, targetName,value)=>{
            model.forEach((each)=>{
                each[targetName] = value;
            });
        },
        batchAddInfoModify : (model, targetName,value)=>{
            model.forEach((each)=>{
                each.addedInfo[targetName] = value;
            });
        },
        /**
         * 스타일 명 설정
         * @param product
         */
        setStyleName : ( product ) =>{
            if(!$.isEmpty(product.prdStyle)) {
                //product.productName = $('#sel-style option:selected').text();
                product.productName = styleMap[product.prdStyle];
            }
        },
        setStyleCodeBatch : ( product, customerInitial ) =>{
            vueApp.productList.forEach((product)=>{
                vueApp.setStyleCode(product, vueApp.customer.styleCode);
            });
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

        //코멘트 관리
        modifyComment : (commentList, comment, commentIndex)=>{
            openCallView(`call_view.php?sno=${comment.projectSno}&commentSno=${comment.sno}`);
        },
        deleteComment : (commentList, comment, commentIndex)=>{
            $.msgConfirm('코멘트를 삭제 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('deleteComment',{
                        'sno' : comment.sno,
                    }).then((data)=>{
                        commentList.splice(commentIndex, 1);
                    });
                }
            });
        },
        saveComment : (project)=>{
            oEditors.getById["editor"].exec("UPDATE_CONTENTS_FIELD", []);
            if( '&nbsp;' !== $('#editor').val().replace(/<\/?p[^>]*>/gi, "") ){
                const comment = $('#editor').val();
                if( '<p><br></p>' != comment.trim() ){
                    $.imsPost('saveComment',{
                        'projectSno' : project.sno,
                        'comment' : comment,
                    }).then((data)=>{
                        vueApp.commentList = data.data;
                        $('#editor').val('');
                        oEditors.getById["editor"].exec("LOAD_CONTENTS_FIELD", []);
                    });
                }else{
                    $.msg('등록할 코멘트가 없습니다.','','warning');
                }

            }
        },
        setApprovalPass : (approvalType)=>{
            $.msgPrompt('해당 항목을 결재 없이 진행하시겠습니까?'
                , '사유 입력 필수'
                , '사유 입력'
                , (confirmMsg)=>{
                    if( confirmMsg.isConfirmed ){
                        if( $.isEmpty(confirmMsg.value)){
                            $.msg('사유 필수','', "warning");
                        }else{
                            $.imsPost2('setApproval',{
                                'projectSno'   : sno,
                                'approvalType' : approvalType+'Confirm',
                                'status' : 'p', //승인처리
                                'approvalMemo' : approvalType+'Memo',
                                'memo' : confirmMsg.value,
                            },()=>{
                                location.reload();
                            });
                        }
                    }
                }
                , ()=>{});
        },
        openEworkStatus : (product)=>{
            $.imsPost('getEworkData',{
                'styleSno' : product.sno
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    console.log('Ework결과',data);
                    product.ework = $.copyObject(data.ework);
                    product.usedEworkListShow = 'on';
                });
            });
        },
        showSchedule:()=>{
            vueApp.isScheduleDetail = true; //일반 스케쥴 상세
            vueApp.isScheduleModify = false; //일반 스케쥴 수정
            vueApp.isQbDetail = false; //QB 스케쥴 상세
        },
        showQbSchedule:()=>{
            vueApp.isScheduleDetail = false; //일반 스케쥴 상세
            vueApp.isScheduleModify = false; //일반 스케쥴 수정
            vueApp.isQbDetail = true; //QB 스케쥴 상세
        },
        /**
         * 생산가 일괄 요청
         */ 
        goBatchEstimate : (projectSno, estimateType)=>{
            //휴지통
            const prdSnoList = [];
            let isContinue = true;
            $('input[name="prdSno"]:checked').each(function(){
                if( 0 >= $(this).data('cnt') ){
                    isContinue = false;
                }
                prdSnoList.push( {
                    sno : $(this).val(),
                    cnt : $(this).data('cnt'),
                } );
            });

            if( !isContinue ){
                $.msg( '수량은 필수 입니다.','', "warning");
                return false;
            }
            if( 0 === prdSnoList.length  ){
                $.msg('요청 스타일이 없습니다.','', "warning");
                return false;
            }

            if( $.isEmpty2(vueApp.batchEstimateFactory) ){
                $.msg('업체선택 필수.','', "warning");
                return false;
            }
            $.msgConfirm(prdSnoList.length + '의 스타일의 가견적을 요청 하시겠습니까?','생산처에서 견적시 필요한 정보는 별도 전달이 필요합니다!').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('goBatchEstimate',{
                        'estimateType': estimateType,
                        'customerSno':vueApp.customer.sno,
                        'reqFactory':vueApp.batchEstimateFactory,
                        'projectSno':projectSno,
                        'prdSnoList':prdSnoList
                    }).then((data)=>{
                        if(200 === data.code){
                            $.msg('완료 되었습니다.','', "success").then(()=>{
                                location.reload();
                            });
                        }else{
                            $.msg(data.message,'','warning');
                        }
                    });
                }
            });
        },costReset : (projectSno)=>{
            $.msgConfirm('생산가를 초기화 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('costReset',{
                        'projectSno' : projectSno
                    }).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            $.msg('처리 완료','','success').then(()=>{
                                refreshProductList(sno);
                            });
                        });
                    });
                }
            });
        },changeStyleTab : (type)=>{
            console.log(type);
            vueApp.styleTabMode = type;
            $.cookie('viewStyleTabMode', type);
        },

        saveProjectRealTime : (updateField,updateData)=>{
            const saveData = {
                'mode':'saveRealTime',
                'target':'project',
                'key':'sno',
                'keyValue':sno, //projectSno
                'updateField':updateField,
                'updateData':updateData,
                'dataMerge':'n', //기본 merge
            }
            $.post('<?=$imsAjaxUrl?>',saveData, (data)=>{console.log(data)});
        },
        saveRealTime : (target,key,keyValue,updateField,updateData)=>{ //아직 미사용
            const saveData = {
                'mode':'saveRealTime',
                'target':target,
                'key':key, //key : warnBatek
                'keyValue':keyValue, //key : currentValue
                'updateField':updateField,
                'updateData':updateData,
                'dataMerge':'y', //기본 merge
            }
            $.post('<?=$imsAjaxUrl?>',saveData, (data)=>{console.log(data)});
        },

        copyStyleName : (type, checkName)=>{
            const prdCheckName = typeof checkName != 'undefined' ? checkName : 'prdSno';

            //name , code
            const prdInfoList = [];

            //타입이 goods_info 일 경우 타이틀 붙이기
            if (type === 'goods_info') {
                prdInfoList.push( '상품명\t수량\t생산가\t판매단가\t마진%' );
            }
            
            $('input[name="'+ prdCheckName +'"]:checked').each(function(){
                if (type === 'goods_info') {
                    //품명 수량 생산가 판매가 마진
                    prdInfoList.push( $(this).data('name')+'\t'+$(this).data('cnt')+'\t'+$(this).data('estimate-cost')+'\t'+$(this).data('price')+'\t'+$(this).data('margin')+'%' );
                } else if(!$.isEmpty($(this).data(type))){
                    prdInfoList.push( $(this).data(type) );
                }
            });

            if( 0 === prdInfoList.length  ){
                $.msg('클립보드 복사 대상 스타일이 없습니다.','', "warning");
                return false;
            }else{
                $.copyClipBoard(prdInfoList.join("\n"))
            }

        },
        copyProduct : (projectSno)=>{
            const prdSnoList = [];
            $('input[name="prdSno"]:checked').each(function(){
                prdSnoList.push( $(this).val() );
            });
            if( 0 === prdSnoList.length  ){
                $.msg('복사 대상 스타일이 없습니다.','', "warning");
                return false;
            }
            $.msgConfirm(prdSnoList.length + '개의 스타일을 복사 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('copyProduct',{
                        'projectSno':projectSno,
                        'prdSnoList':prdSnoList
                    }).then((data)=>{
                        $.msg('완료 되었습니다.','', "success").then(()=>{
                            location.reload();
                        });
                    });
                }
            });
        },
        copyProductToTargetProject : (projectSno)=>{
            const prdSnoList = [];
            $('input[name="prdSno"]:checked').each(function(){
                prdSnoList.push( $(this).val() );
            });
            if( 0 === prdSnoList.length  ){
                $.msg('복사 대상 스타일이 없습니다.','', "warning");
                return false;
            }
            $.msgPrompt('복사 대상 프로젝트 번호','','', (confirmMsg)=>{
                if( confirmMsg.isConfirmed ){
                    $.imsPost('copyProductToTargetProject',{
                        'projectSno':confirmMsg.value,
                        'prdSnoList':prdSnoList
                    }).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            $.msg('대상 프로젝트에 복사가 완료 되었습니다.','', "success");
                        });
                    });
                }
            });
        },

        /**
         * QB 정보 스케쥴 관리 상태에 따른 배경색 관리
         */ 
        setQbBackgroundColor : (fabricStatus, btStatus)=>{
            let className = '';
            if( $.isEmpty2(fabricStatus) && $.isEmpty2(btStatus) ){
                className = ' bg-light-yellow';
            }else if( 2 == fabricStatus && 2 == btStatus ){
                className = ' bg-light-green';
            }
            return className;
        },

        setPlanNotPossible : ()=>{
            let msg = '기획불가 처리 하시겠습니까?';
            $.msgPrompt(msg, '사유 필수','사유', ((result)=>{
                if( result.isConfirmed ){
                    if( $.isEmpty(result.value) ){
                        $.msg('사유는 필수 입니다.','','warning');
                    }else{
                        $.imsPost2('setPlanNotPossible',{
                            projectSno:vueApp.project.sno,
                            reason:result.value,
                        }).then(()=>{
                            $.msg('처리 완료','','success').then(()=>{
                                location.reload();
                            });
                        });
                    }
                }
            }));
        },
        setStatus : (status)=>{
            $.imsPost2('setStatus',{
                projectSno : vueApp.project.sno,
                projectStatus : status
            },(data)=>{
                $.msg('처리되었습니다.','','success').then(()=>{
                    location.reload();
                });
            });
        },
        /**
         * 발주하기
         */
        orderToFactory : ()=>{
            $.msgConfirm('발주를 진행하시겠습니까?','').then(function(result) {
                if (result.isConfirmed) {
                    vueApp.setStatus(90);
                }
            });
        },
        /**
         * 상태변경
         */
        setStatusWithMsg : (msg,status)=>{
            $.msgConfirm(msg,'').then(function(result) {
                if (result.isConfirmed) {
                    $.imsPost2('setStatus',{
                        projectSno:vueApp.project.sno,
                        projectStatus:status,
                    }).then(()=>{
                        $.msg('처리되었습니다.','','success');
                    });
                }
            });
        },
        /**
         * 리오더
         */  
        reOrder : (projectSno)=>{
            $.msgConfirm('리오더 프로젝트를 생성하시겠습니까?','').then(function(result) {
                if (result.isConfirmed) {
                    $.imsPost2('reOrderProject',{
                        projectSno:projectSno,
                    }).then((data)=>{
                        $.imsPostAfter(data,(newSno)=>{
                            $.msg('처리되었습니다.','복사한 프로젝트로 이동합니다.','success').then(()=>{
                                location.href='<?=$myHost?>/ims/ims_view2.php?currentStatus=50&sno=' + newSno;
                            });
                        });
                    });
                }
            });
        },
        /**
         * 프로젝트 분할
         */
        splitProject : (projectSno)=>{
            const prdSnoList = [];
            /*vueApp.productList.forEach((prd)=>{
                prdSnoList.push(prd.sno);
            });*/

            $('input[name="prdSno"]:checked').each(function(){
                prdSnoList.push( $(this).val() );
            });

            let message = '프로젝트를 분할 하시겠습니까?';
            if( 0 >= prdSnoList.length ){
                message = '이동할 스타일 없이 ' + message;
            }

            const procCopy = (subMsg)=>{
                $.msgConfirm(message,subMsg).then(function(result){
                    if( result.isConfirmed ){
                        //승인
                        $.imsPost('copyProject',{
                            'projectSno':projectSno,
                            'prdSnoList':prdSnoList,
                            'prdCopy':'n'
                        }).then((data)=>{
                            const newSno = data.data;
                            $.msg('이동이 완료 되었습니다.','생성된 프로젝트로 이동합니다.', "success").then(()=>{
                                location.href = '<?=$myHost?>/ims/ims_view2.php?sno='+ newSno;
                            });
                        });
                    }
                });
            }

            let subMsg = '';
            subMsg = prdSnoList.length + '개의 스타일을 새 프로젝트로 이동시킵니다.';
            procCopy(subMsg);
        },
        openSampleListPopup : ()=>{
            vueApp.visibleSamplePopup=true;
            setTimeout(()=>{
                const tableHtml = $('#project-sample-list').prop('outerHTML');
                $('#popup-table-container').html(tableHtml);
            },200);
        },

        setType : ()=>{
            const typeMap = {
                'single': '0',
                'bid': '2',
                'costBid': '2',
            };
            vueApp.project.projectType = typeMap[vueApp.project.bidType2];
        },

        setRecommend : ()=>{
            vueApp.project.recommend = 0;
            vueApp.project.recommendList.forEach((recommendValue)=>{
                vueApp.project.recommend += Number(recommendValue);
            });
        },

        //---------------------------- Assort / 사양서 설정 관련 시작 ---------------------------------------//

        addAssort : (productList, prdIndex)=>{
            for(let prdIdx in productList){

                if( 'y' !== vueApp.syncAssortType && prdIndex != prdIdx ){
                    continue; //동시 수정 아니면 패스
                }

                const product = productList[prdIdx];
                const copyAssort = $.copyObject(product.assort[0]);
                for(let idx in copyAssort.optionList){
                    copyAssort.optionList[idx] = '';
                }
                copyAssort.type = '';
                product.assort.push(copyAssort);
            }
        },
        deleteAssort : (productList, assort, targetIdx)=>{
            for(let prdIdx in productList){
                if( 'y' !== vueApp.syncAssortType && productList[prdIdx].assort[targetIdx] !== assort ){
                    continue;
                }
                if( typeof productList[prdIdx].assort[targetIdx] != 'undefined' ){
                    vueApp.deleteElement(productList[prdIdx].assort,targetIdx);
                }
            }
        },
        assortTypeCopy : (productList, assort, targetIdx)=>{
            if( 'y' === vueApp.syncAssortType ){
                for(let prdIdx in productList){
                    productList[prdIdx].assort[targetIdx].type = assort.type;
                }
            }
        },
        /**
         * 아소트 저장
         */
        saveAssort : ()=>{
            for(let prdIdx in vueApp.productList){
                const prd = vueApp.productList[prdIdx];
                const saveData = {
                    'mode':'saveRealTime',
                    'target':'projectProduct',
                    'key':'sno',
                    'keyValue':prd.sno, //projectSno
                    'updateField':'assort',
                    'updateData':prd.assort,
                    'dataMerge':'n', //기본 merge
                }
                $.post('<?=$imsAjaxUrl?>',saveData, (data)=>{});
                $.imsPost('saveRealTime',{
                    'target':'projectProduct',
                    'key':'sno',
                    'keyValue':prd.sno, //projectSno
                    'updateField':'moq',
                    'updateData':prd.moq,
                    'dataMerge':'n', //기본 merge
                }); //MOQ저장
                $.imsPost('saveRealTime',{
                    'target':'projectProduct',
                    'key':'sno',
                    'keyValue':prd.sno, //projectSno
                    'updateField':'prdExQty',
                    'updateData':prd.assortTotal,
                    'dataMerge':'n', //기본 merge
                }).then(()=>{
                    prd.prdExQty = prd.assortTotal;
                });
                $.imsPost('saveRealTime',{
                    'target':'projectProduct',
                    'key':'sno',
                    'keyValue':prd.sno, //projectSno
                    'updateField':'msQty',
                    'updateData':prd.assortMsTotal,
                    'dataMerge':'n', //기본 merge
                }).then(()=>{
                    prd.msQty = prd.assortMsTotal;
                });
            }

            $.msg('아소트 저장 완료.','','success');

        },

        /**
         * 아소트 입력 URL 발송
         * @param receiver
         * @param email
         */
        sendAssortUrl : (receiver,email)=>{
            $.msgConfirm('아소트 입력 요청 메일을 발송합니다.<br>반드시 고객 입력화면 체크 후 발송해주세요.','계속 하시겠습니까?').then((result)=>{
                if( result.isConfirmed ){
                    $.imsPost('sendAssortUrl',{
                        sno : sno,
                        customerSno : vueApp.customer.sno,
                        assortReceiver : receiver,
                        assortEmail : email,
                    }).then((data)=>{

                        $.imsPostAfter(data,(data)=>{
                            $.msg('발송하였습니다.','','success').then(()=>{
                                location.reload();
                            });
                        });

                    });
                }
            });
        },
        /**
         * 사양서 입력 URL 발송
         * @param receiver
         * @param email
         */
        sendOrderUrl : (receiver,email)=>{
            $.msgConfirm('사양서 체크 메일을 발송합니다.<br>반드시 고객화면 체크 후 발송해주세요.','계속 하시겠습니까?').then((result)=>{
                if( result.isConfirmed ){
                    $.imsPost('sendOrderUrl',{
                        sno : sno,
                        customerSno : vueApp.customer.sno,
                        receiver : receiver,
                        email : email,
                    }).then((data)=>{

                        $.imsPostAfter(data,(data)=>{
                            $.msg('발송하였습니다.','','success').then(()=>{
                                location.reload();
                            });
                        });

                    });
                }
            });
        },
        //제안서 url 발송
        sendProposalUrl : (receiver,email, fileUrl)=>{
            $.msgConfirm('제안서 체크 메일을 발송합니다.<br>반드시 고객화면 체크 후 발송해주세요.','계속 하시겠습니까?').then((result)=>{
                if( result.isConfirmed ){
                    $.imsPost('sendProposalUrl',{
                        sno : sno,
                        customerSno : vueApp.customer.sno,
                        receiver : receiver,
                        email : email,
                        fileUrl : fileUrl,
                    }).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            $.msg('발송하였습니다.','','success').then(()=>{
                                location.reload();
                            });
                        });
                    });
                }
            });
        },

        setAssortStatusProcess : (status)=>{
            //아소트 확정취소시 고객 분류패킹 진행중인지 확인(고객담당자가 담당직원(==배송지점) 지정했는지 확인)
            if (status == 'f') {
                let bFlagErr = false;
                $.each(vueApp.aoPackingList, function (key, val) {
                    if (Number(val.cntDelivery) > 0) {
                        bFlagErr = true;
                        return false;
                    }
                });
                if (bFlagErr === true) {
                    $.msg('고객이 이미 분류패킹 정보를 입력중입니다.','아소트 취소/수정 필요시 개발팀에 문의하세요.','warning');
                    return false;
                }
            }

            $.imsPost('setAssortStatus',{
                sno : sno,
                status : status,
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    $.msg('처리완료.','','success').then(()=>{
                        location.reload();
                    });
                });
            });
        },
        setAssortStatus : (status)=>{
            const msgMap = {
                'r' : '고객이 아소트를 다시 입력할 수 있게 합니다.',
                'p' : '아소트를 확정하시겠습니까?<br>확정 후 수정은 반드시 고객 및 유관부서에 공유 바랍니다!',
                'f' : '아소트 확정 상태를 취소하시겠습니까?<br>확정 후 수정은 반드시 고객 및 유관부서에 공유 바랍니다!',
            };
            $.msgConfirm(msgMap[status],'계속 하시겠습니까?').then((result)=>{
                if( result.isConfirmed ){
                    if (status == 'p') {
                        let bFlagErr = true;
                        $.each(vueApp.productList, function (key, val) {
                            bFlagErr = true;
                            $.each(val.assort, function (key2, val2) {
                                if (val2.packingYn == 'Y') bFlagErr = false;
                            });
                            if (bFlagErr === true) return false;
                        });
                        if (bFlagErr === true) {
                            $.msgConfirm('분류패킹 미포함 스타일이 있습니다. 진행하시겠습니까?','분류패킹 미포함된 스타일은 고객에게 보여지지 않습니다').then((result)=>{
                                if( result.isConfirmed ){
                                    vueApp.setAssortStatusProcess(status);
                                }
                            });
                        } else {
                            vueApp.setAssortStatusProcess(status);
                        }
                    } else {
                        vueApp.setAssortStatusProcess(status);
                    }
                }
            });
        },
        setOrderStatus : (status)=>{
            const msgMap = {
                'r' : '고객이 사양서를 다시 체크할 수 있게 합니다.',
                'p' : '고객대신 사양서를 확정하시겠습니까?<br>확정 후 수정은 반드시 고객 및 유관부서에 공유 바랍니다!',
            };
            $.msgConfirm(msgMap[status],'계속 하시겠습니까?').then((result)=>{
                if( result.isConfirmed ){
                    $.imsPost('setOrderStatus',{
                        sno : sno,
                        status : status,
                    }).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            $.msg('처리완료.','','success').then(()=>{
                                location.reload();
                            });
                        });
                    });
                }
            });
        },
        openAssortUrl : ()=>{
            if($.isEmpty(vueApp.project.assortReceiver)){
                vueApp.project.assortReceiver = vueApp.customer.contactName;
                vueApp.project.assortEmail = vueApp.customer.contactEmail;
            }
            vueApp.visibleAssortSendUrl=true;
        },
        openOrderUrl : ()=>{
            if($.isEmpty(vueApp.project.customerOrderReceiver)){
                vueApp.project.customerOrderReceiver = vueApp.customer.contactName;
                vueApp.project.customerOrderEmail = vueApp.customer.contactEmail;
            }
            vueApp.visibleOrderSendUrl=true;
        },
        openProposalUrl : ()=>{ //project테이블에 컬럼이 너무 많아서 proposalReceiver, proposalEmail 안만들었음
            if($.isEmpty(vueApp.project.assortReceiver)){
                vueApp.project.assortReceiver = vueApp.customer.contactName;
                vueApp.project.assortEmail = vueApp.customer.contactEmail;
            }
            vueApp.visibleProposalSendUrl=true;
        },

        applyYearSeason : ()=>{
            const prdList = getCheckedPrd();
            prdList.forEach((prd)=>{
                prd.prdYear = '20'+vueApp.project.projectYear;
                prd.prdSeason = vueApp.project.projectSeason;
            });
        },
        applyDeliveryDt : ()=>{
            const prdList = getCheckedPrd();
            prdList.forEach((prd)=>{
                prd.customerDeliveryDt = vueApp.project.customerDeliveryDt;
                prd.msDeliveryDt = vueApp.project.msDeliveryDt;
            });
        },
        //---------------------------- Assort / 사양서 설정 관련 끝  ---------------------------------------//

        //샘플위치관련 start
        openListSampleLocation : (prdIndex)=>{
            vueApp.chooseSampleSno = vueApp.sampleList[prdIndex].sampleSno;
            vueApp.chooseSampleIdx = prdIndex;
            vueApp.chooseSampleLocationIdx = -1;

            if (vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation == null || vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation == '') vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation = [];

            $('#modalSampleLocationList').modal('show');
        },
        openRegistSampleLocation : ()=>{
            vueApp.isModifySampleLocation = true;
            vueApp.chooseSampleLocationIdx = -1;
            if (vueApp.sampleLocationInfoDefault.locationEnd == '') { //위치등록 기초폼에 값 넣기
                vueApp.sampleLocationInfoDefault.locationEnd = vueApp.customer.customerName;
                vueApp.sampleLocationInfoDefault.locationReceiver = vueApp.customer.contactName+' '+vueApp.customer.contactPosition;
                vueApp.sampleLocationInfoDefault.locationTel = vueApp.customer.contactMobile;
                vueApp.sampleLocationInfoDefault.locationAddr = (vueApp.customer.contactZipcode+' '+vueApp.customer.contactAddress+' '+vueApp.customer.contactAddressSub).trim();
                vueApp.sampleLocationInfoDefault.locationMethod = '택배';
            }
            vueApp.sampleLocationInfo = $.copyObject(vueApp.sampleLocationInfoDefault);

            $('#modalSampleLocationUpsert').modal('show');
        },
        saveSampleLocation : ()=>{
            if (vueApp.sampleLocationInfo.locationDt == '') {
                $.msg('날짜를 선택해주세요.','','warning');
                return false;
            }
            if (vueApp.sampleLocationInfo.locationMethod == '') {
                $.msg('배송방법을 입력해주세요.','','warning');
                return false;
            }

            if (vueApp.chooseSampleLocationIdx == -1) {
                vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation.push($.copyObject(vueApp.sampleLocationInfo));
            } else {
                vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation[vueApp.chooseSampleLocationIdx] = $.copyObject(vueApp.sampleLocationInfo);
            }
            vueApp.sampleLocationInfo = $.copyObject(vueApp.sampleLocationInfoDefault);

            //jsonLocation update -> recentLocation update
            $.imsPost('modifySimpleDbCol', {'table_number':2, 'colNm':'jsonLocation', 'where':{'sno':vueApp.chooseSampleSno}, 'data':vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation});
            let sChgRecentLocation = '';
            if (vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation.length > 0) {
                let sRecentLocation = '';
                let iCompareDt = 10000000;
                $.each(vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation, function (key, val) {
                    if (Number(val.locationDt.replaceAll('-','')) > iCompareDt) {
                        iCompareDt = Number(val.locationDt.replaceAll('-',''));
                        sRecentLocation = val.locationEnd;
                    }
                });
                if (vueApp.sampleList[vueApp.chooseSampleIdx].recentLocation != sRecentLocation) {
                    sChgRecentLocation = sRecentLocation;
                }
            } else sChgRecentLocation = '입력';
            if (sChgRecentLocation != '') {
                $.imsPost('modifySimpleDbCol', {'table_number':2, 'colNm':'recentLocation', 'where':{'sno':vueApp.chooseSampleSno}, 'data':sChgRecentLocation});
                vueApp.sampleList[vueApp.chooseSampleIdx].recentLocation = sChgRecentLocation;
            }

            $('#modalSampleLocationUpsert').modal('hide');
        },
        openViewSampleLocation : (key)=>{
            vueApp.isModifySampleLocation = false;
            vueApp.chooseSampleLocationIdx = key;
            vueApp.sampleLocationInfo = $.copyObject(vueApp.sampleList[vueApp.chooseSampleIdx].jsonLocation[key]);
            $('#modalSampleLocationUpsert').modal('show');
        },
        //샘플위치관련 end

    };
</script>
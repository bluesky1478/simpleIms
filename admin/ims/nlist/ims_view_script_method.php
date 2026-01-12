<script type="text/javascript">
    /**
     *  프로젝트 VIEW 메소드
     */
    const viewMethods = {

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
         * 스캐쥴 업데이트
         */
        saveSchedule : () =>{
            //console.log( vueApp.project.schedule );
            const saveParams = {
                'schedule' : vueApp.project.schedule,
                'sno' : vueApp.project.sno, //발주DL
            };
            $.imsPost2('saveProjectSchedule', saveParams,()=>{
                vueApp.save(); //스케쥴 저장 후 프로젝트 저장.
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
        /**
         * 영업 스타일 추가
         */
        addSalesStyle : ()=>{
            //스타일 구조를 가져와야한다.
            $.imsPost('getProductDefaultScheme',{
                sno:-1
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.isStyleModify = true;
                    const defaultStyle = $.copyObject(data);
                    console.log( 'season', vueApp.project.projectSeason );
                    defaultStyle.customerSno = vueApp.project.customerSno;
                    defaultStyle.projectSno = vueApp.project.sno;
                    defaultStyle.prdYear = '20'+vueApp.project.projectYear;
                    defaultStyle.prdSeason = vueApp.project.projectSeason;
                    
                    defaultStyle.customerDeliveryDt = vueApp.project.customerDeliveryDt; //고객납기
                    defaultStyle.msDeliveryDt = vueApp.project.msDeliveryDt; //MS납기
                    
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
                    $.msg( $(this).data('name') + ' 수량은 필수 입니다.','', "warning");
                    isContinue = false;
                    return false;
                }
                prdSnoList.push( {
                    sno : $(this).val(),
                    cnt : $(this).data('cnt'),
                } );
            });

            if( !isContinue ){
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
            $('input[name="'+ prdCheckName +'"]:checked').each(function(){
                if(!$.isEmpty($(this).data(type))){
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
                console.log(data);
                refreshProject(vueApp.project.sno);
                refreshProductList(vueApp.project.sno);
                $.msg('처리되었습니다.','','success');
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
                $.post('<?=$imsAjaxUrl?>',saveData, (data)=>{

                });
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
                });

                $.imsPost('saveSyncProductionCnt',{
                    'prdSno':prd.sno, //projectSno
                    'assortCnt':prd.assortTotal,
                });

            }

            $.msg('아소트 저장 완료','','success');

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
        setAssortStatus : (status)=>{
            const msgMap = {
                'r' : '고객이 아소트를 다시 입력할 수 있게 합니다.',
                'p' : '아소트를 확정하시겠습니까?<br>확정 후 수정은 반드시 고객 및 유관부서에 공유 바랍니다!',
                'f' : '아소트 확정 상태를 취소하시겠습니까?<br>확정 후 수정은 반드시 고객 및 유관부서에 공유 바랍니다!',
            };
            $.msgConfirm(msgMap[status],'계속 하시겠습니까?').then((result)=>{
                if( result.isConfirmed ){
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

        //---------------------------- Assort / 사양서 설정 관련 끝  ---------------------------------------//

    };
</script>
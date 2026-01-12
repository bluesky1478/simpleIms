<script type="text/javascript">

    console.log('ready.. ver1');

    const sno = '<?=gd_isset($requestParam['sno'],$requestParam['projectSno'])?>';
    let tabMode = '<?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ?'basic':$requestParam['tabMode']?>';

    <?php if('y' === $isReload ) { ?>
    if( $.isEmpty($.cookie('viewTabMode')) ){
        tabMode = '<?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ?'basic':$requestParam['tabMode']?>';
    }else{
        tabMode = $.cookie('viewTabMode');
    }
    <?php } else { ?>
    tabMode = '<?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ?'basic':$requestParam['tabMode']?>';
    <?php } ?>

    let beforeProjectData = null;

    let currentStatus = null;
    let currentCustomerConfirm = null;
    $(appId).hide();

    //프로젝트 데이터 갱신
    function refreshProject(){
        ImsService.getData(DATA_MAP.PROJECT,sno).then((data)=> {
            if (200 !== data.code) {
                return false;
            }
            vueApp.items = data.data.customer;
            vueApp.project = data.data.project;
            vueApp.productList = data.data.productList;
            vueApp.removeProductList = data.data.removeProductList;
            vueApp.fileList = data.data.fileList;
            vueApp.prepared = data.data.prepared;
            vueApp.preparedList = data.data.preparedList;
            //vueApp.meeting = data.data.meeting;
            vueApp.commentList = data.data.commentList;
            //생산가견적 추가 정보 갱신
            $.imsPost('getEstimateCostStatus',{prdList:data.data.productList}).then((data)=>{
                vueApp.viewProductList = $.copyObject(data.data); //꾸민 데이터로 갱신.
                vueApp.$forceUpdate();
            });
        });
        
    }
    
    /**
     * 자동 상태변경 ( FIXME )
     */
    const autoSetStatus = (projectSno, status, reason)=>{
        $.imsPost('setStatus',{
            projectSno : projectSno
            , reason : reason
            , projectStatus : status
        }).then((data)=>{
            if(200 === data.code){
                $.msg('상태가 변경되었습니다.','', "success").then(()=>{
                    if( 80 === Number(status) || 90 === Number(status) ){
                        location.href=`<?=$myHost?>/ims/ims_produce_view.php?sno=${sno}&status=step${status}`;
                    }else{
                        location.href=`<?=$myHost?>/ims/ims_project_view.php?sno=${sno}&status=step${status}`;
                    }
                });
            }
        });
    }

    /**
     * 승인처리 ( FIXME )
     */
    const setAccept = async (acceptDiv, confirmStatus, memo)=>{
        return $.imsPost('setAccept', {
            projectSno: sno,
            acceptDiv: acceptDiv,
            confirmStatus: confirmStatus,
            memo : memo
        });
    }

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

        let promptValue = '';

        if( 'filePacking' !== dropzoneId && 'fileBarcode' !== dropzoneId ){
            promptValue = window.prompt("메모입력 : ");
        }

        $.imsPost('saveProjectFiles',{
            saveData : {
                projectSno : sno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            if(200 === data.code){
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
                const acceptDivMap = {
                    'filePlan' : 'planConfirm',
                    'fileProposal' : 'proposalConfirm',
                    /*'fileSampleConfirm' : 'sampleConfirm',*/
                    /*'fileWork' : 'workConfirm',*/
                };
                if( !$.isEmpty(acceptDivMap[dropzoneId]) ){
                    const acceptDiv = acceptDivMap[dropzoneId];
                    setAccept(acceptDiv, 'r', '파일등록으로 자동 승인요청').then((data)=>{
                        if(200 === data.code){
                            if( !$.isEmpty(data.data) && 'false' !== data.data && false !== data.data  ){
                                vueApp.project[acceptDiv+'Kr'] = data.data.project[acceptDiv+'Kr'];
                                vueApp.project[acceptDiv] = data.data.project[acceptDiv];
                            }
                            $.msg('저장 되었습니다.', "", "success");
                        }
                    });
                }

                //승인완료 처리
                const completeCheckFieldMap = {
                    'filePlan'      : 'planEndDt',
                    'fileProposal'  : 'proposalEndDt',
                    /*'fileWork'      : 'workEndDt',
                    'fileSampleConfirm' : 'sampleEndDt',*/
                };
                for(let key in completeCheckFieldMap){
                    if( key === dropzoneId ){
                        $.imsPost('setCompleteDt',{
                            sno : sno,
                            field : completeCheckFieldMap[key] ,
                        }).then((data)=>{
                            if(200 === data.code){
                                vueApp.project[completeCheckFieldMap[key]] = data.data.project[completeCheckFieldMap[key]];
                                console.log(data);
                            }
                        });
                    }
                }
            }
        });
    }

    $(()=>{
        //Load Data.
        ImsService.getData(DATA_MAP.PROJECT,sno).then((data)=>{
            if( 200 !== data.code  ){
                return false;
            }
            console.log('<?=$imsProduceCompany?> 초기 데이터 : ',data.data);
            currentCustomerConfirm = data.data.project.customerOrderConfirm;

            beforeProjectData = $.copyObject(data.data.project);

            const initParams = {
                data : {

                    showBasicInfo : false,

                    //tabMode : 'style', // meeting , basic , style , produce , comment
                    batchEstimateFactory : 43 , //하나 디폴트
                    isSingleBid : 'single',
                    showStyle : true,
                    isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,
                    customerSummary : true,
                    //meetingSearchCondition : {customerSno : data.data.customer.sno},
                    tabMode : 'basic', // meeting , basic , style , produce , comment , preOrder
                    setCommentEditor : false,
                    viewProductList : data.data.productList,
                    viewMode : 'style',
                    swTotalMemo : 'y',
                    swWriteComment : 'y',
                    swEtcFile : true,
                    commentInitShowCnt : 4,
                    commentShowCnt : 4,
                    items : data.data.customer,
                    project : data.data.project,
                    productList : data.data.productList,
                    workFileList : {},
                    removeProductList : data.data.removeProductList,
                    fileList: data.data.fileList,
                    prepared: data.data.prepared,
                    preparedList: data.data.preparedList,
                    //meeting : data.data.meeting,
                    commentList : data.data.commentList,
                    projectApprovalInfo : {
                        'plan' : {sno:0}, //기획
                        'proposal' : {sno:0}, //제안
                    },

                    reqList : [], //요청
                    issueList : [], //이슈
                    meetingList : [], //미팅 
                    deliveryList : [], //납품
                },
                mounted : (vueInstance)=>{
                    Object.keys(vueApp.projectApprovalInfo).forEach(key=>{
                        ImsTodoService.getApprovalData(key, vueApp.project.sno, 0, 0).then((data)=>{
                            //console.log(`${key} 결재 데이터`, data);
                            vueApp.projectApprovalInfo[key] = $.copyObject(data);
                        });
                    });

                    <?php if(!$isMobile) { ?>
                    $('#layerDim').show();
                    $('#app-hide').show();
                    <?php } ?>

                    //Dropzone 셋팅.
                    $('.set-dropzone').addClass('dropzone');

                    ImsService.setDropzone(vueInstance, 'filePlan', uploadAfterAction); //기획서
                    ImsService.setDropzone(vueInstance, 'fileProposal', uploadAfterAction); //제안서

                    ImsService.setDropzone(vueInstance, 'fileWork', uploadAfterAction); //작업지시서
                    ImsService.setDropzone(vueInstance, 'fileConfirm', uploadAfterAction); //사양서
                    ImsService.setDropzone(vueInstance, 'fileCareMark', uploadAfterAction); //캐어라벨
                    ImsService.setDropzone(vueInstance, 'filePrdMark', uploadAfterAction); //캐어라벨
                    ImsService.setDropzone(vueInstance, 'filePrdEtc', uploadAfterAction); //캐어라벨

                    //샘플파일
                    ImsService.setDropzone(vueInstance, 'fileSample', uploadAfterAction); //샘플의뢰서
                    ImsService.setDropzone(vueInstance, 'fileSampleConfirm', uploadAfterAction); //실물사진
                    ImsService.setDropzone(vueInstance, 'filePattern', uploadAfterAction); //패턴
                    ImsService.setDropzone(vueInstance, 'fileSampleEtc', uploadAfterAction); //샘플기타
                    ImsService.setDropzone(vueInstance, 'fileEtc5', uploadAfterAction); //샘플웨어링

                    //기타파일
                    ImsService.setDropzone(vueInstance, 'filePacking', uploadAfterAction); //분류패킹
                    ImsService.setDropzone(vueInstance, 'fileEtc6', uploadAfterAction); //원부자재내역
                    ImsService.setDropzone(vueInstance, 'fileEtc7', uploadAfterAction); //기타파일
                    ImsService.setDropzone(vueInstance, 'fileDeliveryReport', uploadAfterAction); //납품보고서
                    ImsService.setDropzone(vueInstance, 'fileDeliveryPlan', uploadAfterAction); //납품보고서
                    ImsService.setDropzone(vueInstance, 'fileBarcode', uploadAfterAction); //바코드

                    //영업
                    ImsService.setDropzone(vueInstance, 'fileEtc2', uploadAfterAction); //견적서
                    ImsService.setDropzone(vueInstance, 'fileEtc4', uploadAfterAction); //영업확정서
                    ImsService.setDropzone(vueInstance, 'fileMeeting', uploadAfterAction); //영업확정서
                    ImsService.setDropzone(vueInstance, 'fileEtc1', uploadAfterAction); //미팅추가정보

                    /*ImsService.setDropzone(vueInstance, 'prdStep10', uploadAfterAction); //견적서*/
                    //ImsService.setDropzone(vueInstance, 'prdStep20', uploadAfterAction); //견적서
                    //ImsService.setDropzone(vueInstance, 'fileEtc3', uploadAfterAction); //계약서

                    <?php if(!$isMobile) { ?>
                    setTimeout(()=>{
                        vueInstance.swWriteComment = 'n';
                        $('#app-hide').hide();
                        $('#layerDim').hide();
                    },500);
                    <?php } ?>

                    //비고 통합으로 단계 자동 이동 제거
                    //const status = vueInstance.project.projectStatus;
                    //vueInstance.$refs['refstep'+status].focus();

                    //미팅 리스트 갱신
                    //MeetingService.getList();

                    //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="preOrder"><a href="#">선행</a></div>');
                    //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="comment"><a href="#">코멘트</a></div>');
                    /*$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="produce"><a href="#">생산</a></div>');*/
                    //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="style"><a href="#">스타일</a></div>');
                    //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="basic"><a href="#">기본</a></div>');
                    //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="meeting"><a href="#">미팅</a></div>');
                    $('.float-side-menu').click(function(){
                        const clickId = "#ims-tab-" + $(this).data('type');
                        $(clickId).click();
                    });

                    //vueApp.tabMode = tabMode;
                    vueApp.changeTab(tabMode);

                    vueApp.$nextTick(function () {
                        const snoList = [];

                        vueInstance.productList.sort((a,b)=>a.sort - b.sort);

                        vueInstance.productList.forEach((each)=>{
                            snoList.push(each);
                        });

                        snoList.forEach((each)=>{
                            ImsService.getLatestFileList({
                                'fileDiv' : 'fileWork',
                                'styleSno' : each.sno,
                            }).then((data)=>{
                                if(200 === data.code){
                                    //console.log('최근작지:',data.data);
                                    vueInstance.workFileList[data.data.styleSno] = $.copyObject(data.data);
                                    vueInstance.$forceUpdate();
                                }
                            });
                        });
                    });

                    //생산가견적 추가 정보 갱신
                    $.imsPost('getEstimateCostStatus',{prdList:vueInstance.viewProductList}).then((data)=>{
                        vueInstance.viewProductList = $.copyObject(data.data); //꾸민 데이터로 갱신.
                        vueInstance.$forceUpdate();
                    });

                    //고객 코멘트 가져오기
                    /*reqList : [], //요청
                    issueList : [], //이슈
                    meetingList : [], //미팅
                    deliveryList : [], //납품*/
                    ImsService.getList(DATA_MAP.CUST_ISSUE, {customerSno:vueApp.items.sno, pageNum:9999, sort:'D,desc'}).then((data)=>{
                        if(200 === data.code){
                            console.log('고객 이슈 리스트 데이터', data.data);
                            data.data.list.forEach((issueData)=>{
                                vueApp[issueData.issueType+'List'].push(issueData);
                            });
                        }else{
                            console.log('고객 이슈 가져오기 error ', data.message);
                        }
                    });

                    console.log('mounted complete..');
                },
                methods : {
                    initData : () =>{
                        /*$('.date-input').each(function(){
                            console.log( $(this). );
                        });*/
                        //console.log(beforeProjectData);

                    },
                    saveSimpleProject : (project)=>{
                        let saveObject = $.getChangesObject(beforeProjectData, project); //Dirty Check.
                        //현재 프로젝트 상태와 화면에서 변경 했는지 비교
                        //setStatus : (project)

                        if(typeof saveObject.projectStatus != 'undefined' ){
                            $.postAsync('<?=$imsAjaxUrl?>', {
                                mode : 'setStatus'
                                , projectSno : project.sno
                                , reason : '일정수정에서 상태 변경'
                                , projectStatus : project.projectStatus
                            });
                        }

                        delete saveObject.fabricNational;
                        delete saveObject.recommend;
                        delete saveObject.projectStatus;

                        if(!$.isEmptyObject(saveObject)){
                            saveObject = $.refineDateToStr(saveObject);
                            saveObject.sno = project.sno;
                            console.log(saveObject);

                            $.imsPost('saveSimpleProject',{saveData : saveObject}).then((data)=>{
                                if(200 === data.code){
                                    $.msg('저장 되었습니다.','', "success");
                                    parent.opener.location.reload();
                                    beforeProjectData = $.copyObject(project);
                                }else{
                                    $.msg(data.message,'', "warning");
                                }
                            });
                        }else{
                            $.msg('변경된 사항이 없습니다.','', "warning");
                        }

                    },
                    saveInline : (prdList)=>{
                        const savePrd = $.copyObject(prdList);
                        $.imsPost('saveInline',{saveData : savePrd}).then((data)=>{
                            if(200 === data.code){
                                $.msg('인라인 상태 저장 완료','', "success");
                            }else{
                                $.msg(data.message,'', "warning");
                            }
                        });
                    },
                    saveSort : (prdList)=>{
                        const savePrd = $.copyObject(prdList);
                        $.imsPost('saveInline',{saveData : savePrd}).then((data)=>{
                            if(200 === data.code){
                                vueApp.productList.sort((a,b)=>a.sort - b.sort);
                                $.msg('정렬 완료','', "success");
                            }else{
                                $.msg(data.message,'', "warning");
                            }
                        });
                    },
                    formatValue : (prd, field)=>{
                        // 형식화된 값을 원시 값으로 변환하여 저장
                        prd[field] = parseInt((prd[field]+'').replace(/,/g, ""));
                    },
                    setMargin : (saleCost, prdCost)=>{
                        let margin = 0;
                        if(saleCost>0){
                            margin = Math.round((saleCost-prdCost)/saleCost*100);
                        }
                        return margin;
                    },

                    changeTab : function(tabName){
                        vueApp.tabMode = tabName;
                        $.cookie('viewTabMode', tabName);

                        if( 'comment' === tabName && false === vueApp.setCommentEditor ){
                            const editorPath = '<?=PATH_ADMIN_GD_SHARE ?>script/smart';
                            //코멘트.
                            nhn.husky.EZCreator.createInIFrame({
                                oAppRef: oEditors,
                                elPlaceHolder: "editor",
                                sSkinURI: editorPath + '/SmartEditor2Skin.html',
                                htParams: {
                                    bUseToolbar: true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                                    bUseVerticalResizer: true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                                    bUseModeChanger: true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                                    //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                                    fOnBeforeUnload: function () {
                                        $.ajax({
                                            method: "GET",
                                            url: "/share/editor_file_uploader.php",
                                            data: {mode: 'deleteGarbage', uploadImages : uploadImages.join('^|^')},
                                            cache: false,
                                        }).success(function (data) {
                                        }).error(function (e) {
                                        });
                                    }
                                }, //boolean
                                fOnAppLoad: function () {
                                    //예제 코드
                                    //oEditors.getById["editor"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
                                },
                                fCreator: "createSEditor2"
                            });
                            vueApp.setCommentEditor = true;
                            console.log('에디터 셋업.');
                        }

                    },
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
                            $.imsPost('saveComment',{
                                'projectSno' : project.sno,
                                'comment' : $('#editor').val(),
                            }).then((data)=>{
                                vueApp.commentList = data.data;
                                $('#editor').val('');
                                oEditors.getById["editor"].exec("LOAD_CONTENTS_FIELD", []);
                            });
                        }
                    },
                    openCallView : (project, div)=>{
                        const sno = project.sno;
                        const url = `call_view.php?sno=${sno}&div=${div}`;
                        openCallView(url);
                    },
                    openProductReg : (projectSno, sno)=>{
                        openProductReg(projectSno, sno);
                    },
                    save : ( items , project )=>{
                        project = $.refineDateToStr(project);

                        const copyProject = $.copyObject(project);
                        delete copyProject.projectStatus;

                        $.postAsync('<?=$imsAjaxUrl?>', {
                            mode:'saveProject',
                            saveCustomer : items,
                            saveProject  : copyProject,
                        }).then((data)=>{
                            if(200 === data.code){
                                let saveSno = data.data.sno;
                                $.msg('저장 되었습니다.', "", "success").then(()=>{
                                    <?php if( !$requestParam['modify'] ) {  ?>
                                    location.href='<?=$myHost?>/ims/ims_project_view.php?sno=' + saveSno;
                                    <?php }else{  ?>
                                    parent.opener.location.reload();
                                    <?php }  ?>
                                });
                            }
                        });
                    },
                    setCustomer : (customerSno)=>{
                        ImsService.getData(DATA_MAP.CUSTOMER, customerSno).then((data)=>{
                            if(200 === data.code){
                                vueApp.items = data.data;
                            }
                        });
                    },
                    saveDesignData : (project)=>{
                        const saveProject = $.copyObject(project);
                        delete saveProject.projectStatus;
                        $.imsPost('saveDesignData',{saveData : saveProject}).then((data)=>{
                            if(200 === data.code){
                                $.msg('프로젝트 저장 완료','', "success");
                            }
                        });

                        //고객 데이터도 저장.
                        $.imsPost('saveCustomer',{
                            saveData : $.copyObject(vueApp.items)
                        });

                    },
                    saveQcData : (project)=>{
                        $.imsPost('saveProjectEachData',{
                            sno : sno,
                            customerOrderDt : project.customerOrderDt,
                            customerDeliveryDt : project.customerDeliveryDt,
                            msOrderDt : project.msOrderDt,
                            msDeliveryDt : project.msDeliveryDt,
                            produceCompanySno : project.produceCompanySno,
                            produceType : project.produceType,
                            produceNational : project.produceNational,
                            customerOrderConfirm : project.customerOrderConfirm,
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 완료','', "success").then(()=>{
                                    location.reload();
                                });
                            }
                        });
                    },
                    setStatus : (project)=>{
                        if( currentStatus !== project.projectStatus ){
                            $.postAsync('<?=$imsAjaxUrl?>', {
                                mode : 'setStatus'
                                , projectSno : project.sno
                                , reason : ''
                                , projectStatus : project.projectStatus
                            }).then((data)=>{
                                if(200 === data.code){
                                    $.msg('상태가 변경되었습니다.','', "success").then(()=>{
                                        <?php if( empty($requestParam['modify']) ) { ?>
                                        location.href=`<?=$myHost?>/ims/ims_project_view.php?sno=${sno}&status=step${project.projectStatus}`;
                                        <?php }else{ ?>
                                        opener.location.reload();
                                        <?php } ?>
                                    });
                                }
                            });
                        }else{
                            $.msg('현재 상태와 동일합니다.','','success');
                        }
                    },
                    setNextStep : (project, nextStep, msg)=>{
                        $.msgPrompt(msg,'','상태변경 메모', (confirmMsg)=>{
                            if( confirmMsg.isConfirmed ){
                                if( $.isEmpty(confirmMsg.value)){
                                    $.msg('상태 변경시 사유(메모) 필수','', "warning");
                                }else{
                                    autoSetStatus(project.sno, nextStep, confirmMsg.value);
                                }
                            }
                        });
                    },
                    checkNextStep60 : (project)=>{
                        if( 'step50' === '<?=$requestParam['status']?>' && 50 == project.projectStatus
                            && 'p' === project.planConfirm
                            && 'p' === project.sampleConfirm
                            && 'p' ===  project.proposalConfirm ){
                            return true;
                        }else{
                            return false;
                        }
                        // && 'p' === project.planConfirm && 'p' ===  project.sampleConfirm && 'p' ===  project.proposalConfirm
                    },
                    checkNextStep80 : (project)=>{
                        if( 'step60' === '<?=$requestParam['status']?>' && 60 == project.projectStatus && 'y' === currentCustomerConfirm    ){
                            return true;
                        }else{
                            return false;
                        }
                    },
                    setPreparedStatus : (preparedData, status)=>{
                        let acceptTypeKr = '반려';
                        let acceptTypeMemo = '반려 사유 입력';
                        if( 4 === status ){
                            acceptTypeKr = '승인';
                            acceptTypeMemo = '승인 메모';
                        }

                        $.msgPrompt(acceptTypeKr + ' 처리 하시겠습니까?','',acceptTypeMemo, (confirmMsg)=>{
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
                                        $.msg('처리 되었습니다.', "", "success").then(()=>{
                                            //location.reload();
                                            preparedData.preparedStatus = status;
                                        });
                                    }
                                });
                            }
                        });
                    },recoveryProduct : (projectSno)=>{
                        //휴지통
                        const prdSnoList = [];
                        $('input[name="prdDelSno"]:checked').each(function(){
                            prdSnoList.push( $(this).val() );
                        });
                        if( 0 === prdSnoList.length  ){
                            $.msg('복원 대상 스타일이 없습니다.','', "warning");
                            return false;
                        }
                        $.msgConfirm(prdSnoList.length + '의 스타일을 복구 하시겠습니까?','').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('recoveryProduct',{
                                    'projectSno':projectSno,
                                    'prdSnoList':prdSnoList
                                }).then((data)=>{
                                    $.msg('완료 되었습니다.','', "success").then(()=>{
                                        location.reload();
                                    });
                                });
                            }
                        });
                    },goTrashProduct : (projectSno)=>{
                        //휴지통
                        const prdSnoList = [];
                        $('input[name="prdSno"]:checked').each(function(){
                            prdSnoList.push( $(this).val() );
                        });
                        if( 0 === prdSnoList.length  ){
                            $.msg('삭제 대상 스타일이 없습니다.','', "warning");
                            return false;
                        }
                        $.msgConfirm(prdSnoList.length + '의 스타일을 삭제 하시겠습니까?','스타일 휴지통에서 확인 가능').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('goTrashProduct',{
                                    'projectSno':projectSno,
                                    'prdSnoList':prdSnoList
                                }).then((data)=>{
                                    $.msg('완료 되었습니다.','', "success").then(()=>{
                                        location.reload();
                                    });
                                });
                            }
                        });
                    },deleteProduct : (projectSno)=>{
                        //영구삭제
                        const prdSnoList = [];
                        $('input[name="prdDelSno"]:checked').each(function(){
                            prdSnoList.push( $(this).val() );
                        });
                        if( 0 === prdSnoList.length  ){
                            $.msg('삭제 대상 스타일이 없습니다.','', "warning");
                            return false;
                        }
                        $.msgConfirm(prdSnoList.length + '의 스타일을 영구 삭제 하시겠습니까?','복구불가').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('deleteProduct',{
                                    'projectSno':projectSno,
                                    'prdSnoList':prdSnoList
                                }).then((data)=>{
                                    $.msg('완료 되었습니다.','', "success").then(()=>{
                                        location.reload();
                                    });
                                });
                            }
                        });
                    },copyProduct : (projectSno)=>{
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
                    },copyProject : (projectSno)=>{
                        //console.log(projectSno);
                        const prdSnoList = [];
                        $('input[name="prdSno"]:checked').each(function(){
                            prdSnoList.push( $(this).val() );
                        });
                        //console.log(prdSnoList);

                        const procCopy = (subMsg)=>{
                            $.msgConfirm('프로젝트를 복사 하시겠습니까?',subMsg).then(function(result){
                                if( result.isConfirmed ){
                                    //승인
                                    $.imsPost('copyProject',{
                                        'projectSno':projectSno,
                                        'prdSnoList':prdSnoList,
                                        'prdCopy':'y'
                                    }).then((data)=>{
                                        const newSno = data.data;
                                        //console.log('ims_project_view.php?sno='+ newSno +'&status=<?=$requestParam['status']?>');
                                        $.msg('복사가 완료 되었습니다.','', "success").then(()=>{
                                            location.href = '<?=$myHost?>/ims/ims_project_view.php?sno='+ newSno +'&status=<?=$requestParam['status']?>';
                                        });
                                    });
                                }
                            });
                        }

                        let subMsg = '';
                        if( prdSnoList.length > 0 ){
                            subMsg = prdSnoList.length + '개의 스타일을 복사된 프로젝트에 함께 복사 합니다.';
                            procCopy(subMsg);
                        }else{
                            let isContinue = false;
                            $.msgConfirm('스타일은 복사 하지 않습니까?','스타일도 복사하려면 선택해주세요.').then(function(result) {
                                if (result.isConfirmed) {
                                    procCopy('');
                                }
                            });
                        }

                    },
                    /*미팅*/
                    openMeetingView : MeetingService.openMeetingView,

                    copyStyleName : (type)=>{
                        //name , code
                        const prdInfoList = [];
                        $('input[name="prdSno"]:checked').each(function(){
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

                    },goBatchEstimate : (projectSno, estimateType)=>{
                        //휴지통
                        const prdSnoList = [];
                        $('input[name="prdSno"]:checked').each(function(){
                            if( 0 >= $(this).data('cnt') ){
                                $.msg( $(this).data('name') + ' 수량은 필수 입니다.','', "warning");
                                return false;
                            }
                            prdSnoList.push( {
                                sno : $(this).val(),
                                cnt : $(this).data('cnt'),
                            } );
                        });
                        if( 0 === prdSnoList.length  ){
                            $.msg('요청 스타일이 없습니다.','', "warning");
                            return false;
                        }

                        if( 0 == vueApp.batchEstimateFactory || $.isEmpty(vueApp.batchEstimateFactory) ){
                            $.msg('업체선택 필수.','', "warning");
                            return false;
                        }

                        console.log(prdSnoList);

                        $.msgConfirm(prdSnoList.length + '의 스타일의 가견적을 요청 하시겠습니까?','생산처에서 견적시 필요한 정보는 별도 전달이 필요합니다!').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('goBatchEstimate',{
                                    'estimateType': estimateType,
                                    'customerSno':vueApp.items.sno,
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
                    }
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log(' ims_project_view.php Init OK');
        });
    });
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>

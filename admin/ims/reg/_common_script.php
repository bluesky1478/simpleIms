<script type="text/javascript">

    const sno = '<?=$requestParam['sno']?>';

    $(appId).hide();

    const setStatus = (project, status)=>{
        //if( currentStatus !== project.projectStatus ){
        $.msgPrompt('변경사유 입력','고객과 협의된사항은 반드시 모두 기록 후 상태변경 바랍니다.','변경 사유 입력', (confirmMsg)=>{
            if( confirmMsg.isConfirmed ){

                if( 0 >= vueApp.productList.length ){
                    $.msg('스타일 정보는 필수입니다.', "", "warning");
                    return false;
                }
                if( $.isEmpty(vueApp.project.customerSno) ){
                    $.msg('선택된 고객이 없습니다.', "", "warning");
                    return false;
                }
                if( $.isEmpty(vueApp.project.customerOrderDt) ){
                    $.msg('발주일자는 필수 입니다.', "", "warning");
                    return false;
                }
                if( $.isEmpty(vueApp.project.customerDeliveryDt) ){
                    $.msg('납기일자는 필수 입니다.', "", "warning");
                    return false;
                }
                if( 0 >= vueApp.project.recommend.length ){
                    $.msg('제안형태는 필수 입니다.', "", "warning");
                    return false;
                }

                if( $.isEmpty(confirmMsg.value) ){
                    $.msg('사유는 필수 입니다.', "", "warning");
                    return false;
                }

                $.imsPost('setStatus',{
                    projectSno : project.sno
                    , reason : confirmMsg.value
                    , projectStatus : status
                }).then((data)=>{
                    $.msg('상태가 변경되었습니다.','', "success").then(()=>{
                        location.href=`ims_project_view.php?sno=${sno}&status=step${status}`;
                    });
                });
            }
        });
        /*}else{
            $.msg('현재 상태와 동일합니다.','','success');
        }*/
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

        const promptValue = window.prompt("메모입력 : ");

        $.postAsync('<?=$imsAjaxUrl?>', {
            mode:'saveProjectFiles',
            saveData : {
                projectSno : sno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            vueApp.fileList[dropzoneId] = data.data[dropzoneId];
            $.msg('저장 되었습니다.', "", "success");
            //미팅 보고서가 등록되었고, 현재 Step 미팅준비라면 상태변경 물어본다.
            //-> 안할경우 다음 Step으로 넘어갈수 있는 버튼 활성화
            /*if( 'fileMeeting' === dropzoneId ){
                $.msgConfirm('미팅보고서가 등록되었습니다.<br>바로 디자인기획 단계로 변경하시겠습니까?','').then(function(result){
                    if( result.isConfirmed ){
                        setStatus(vueApp.project, 20);
                    }
                });
            }*/
        });
    }

    $(()=>{
        //Load Data.
        const sno = '<?=$requestParam['sno']?>'; //projectSno.
        ImsService.getData(DATA_MAP.PROJECT,sno).then((data)=>{

            if (-1 == data.data.project.projectStatus){
                data.data.project.projectStatus = '<?=$currentProjectStatus?>';
            }
            console.log('초기데이터:',data.data);
            console.log('상태', data.data.project);


            const initParams = {
                data : {
                    commentInitShowCnt : 4,
                    commentShowCnt : 4,
                    items : data.data.customer,
                    project : data.data.project,
                    meeting : data.data.meeting,
                    fileList: data.data.fileList,
                    productList: data.data.productList,
                    commentList : data.data.commentList,
                },
                mounted : (vueInstance)=>{
                    //Dropzone 셋팅.
                    $('.set-dropzone').addClass('dropzone');
                    <?php foreach( $PROJECT_FILE_LIST as $idx => $prjFileField ) { ?>
                    try{
                        ImsService.setDropzone(vueInstance, '<?=$prjFileField['fieldName']?>', uploadAfterAction);
                    }catch(e){}
                    <?php } ?>

                    <?php foreach( $PROJECT_ETC_FILE_LIST as $idx => $prjFileField ) { ?>
                    try{
                        ImsService.setDropzone(vueInstance, '<?=$prjFileField['fieldName']?>', uploadAfterAction);
                    }catch(e){}
                    <?php } ?>
                },
                methods : {
                    saveComment : (project)=>{
                        //oEditors.getById["editor"].exec("UPDATE_CONTENTS_FIELD", []);
                        //if( '&nbsp;' !== $('#editor').val().replace(/<\/?p[^>]*>/gi, "") ){
                            $.imsPost('saveComment',{
                                'projectSno' : project.sno,
                                'comment' : $('#project-comment').val(),
                            }).then((data)=>{
                                vueApp.commentList = data.data;
                                $('#project-comment').val('');
                                //oEditors.getById["editor"].exec("LOAD_CONTENTS_FIELD", []);
                            });
                        //}
                    },
                    save : ( items , project , meeting )=>{
                        project = $.refineDateToStr(project);
                        $.imsPost('saveProject',{
                            saveCustomer : items,
                            saveProject  : project,
                            saveMeeting  : meeting,
                        }).then((data)=>{
                            if(  200 === data.code ){
                                //let saveSno = data.data.sno;
                                $.msg('저장 되었습니다.', "", "success").then(()=>{
                                    <?php if(empty($requestParam['popup'])) { ?>
                                    <?php if(!empty($requestParam['sno'])){ ?>
                                    location.reload();
                                    <?php }else{ ?>
                                    window.history.back();
                                    <?php } ?>
                                    <?php }else{ ?>
                                    opener.location.reload();
                                    //self.close();
                                    <?php } ?>
                                });
                            }
                        });
                    },
                    setCustomer : (customerSno)=>{
                        ImsService.getData(DATA_MAP.CUSTOMER, customerSno).then((data)=>{
                            vueApp.items = data.data;

                            console.log(data.data);

                            $('.salesManagerSno').val(vueApp.items.salesManagerSno);
                            setJqueryEvent();
                        });
                    },
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log(' _common_script.php Init OK');
        });
    });


</script>

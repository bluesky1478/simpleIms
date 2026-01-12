<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-danger">{% project.sno %}</span>
                {% customer.customerName %}
                {% project.projectYear %}
                {% project.projectSeason %}
            </h3>
            <div class="btn-group">
                <input type="button" value="저장" class="btn btn-red"  @click="save()" >
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <div class="_mgb10">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                기본정보
            </div>
            <div class="flo-right"></div>
        </div>
        <div class="clear-both"></div>
        <div class="mgt5">
            <table class="table table-cols">
                <colgroup>
                    <col class="w-11p">
                    <col class="w-22p">
                    <col class="w-11p">
                    <col class="w-22p">
                    <col class="w-11p">
                    <col class="w-22p">
                </colgroup>
                <tbody >
                <tr>
                    <th>상태</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">
                        </div>
                    </td>
                    <th>연도/시즌</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">
                        </div>
                    </td>
                    <th>담당자</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="_mgb10">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                타입/구분
            </div>
            <div class="flo-right"></div>
        </div>
        <div class="clear-both"></div>
        <div class="mgt5">
            <table class="table table-cols">
                <colgroup>
                    <col class="w-11p">
                    <col class="w-22p">
                    <col class="w-11p">
                    <col class="w-22p">
                    <col class="w-11p">
                    <col class="w-22p">
                </colgroup>
                <tbody >
                <tr>
                    <th>프로젝트 타입</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">
                        </div>
                    </td>
                    <th>입찰형태</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">
                        </div>
                    </td>
                    <th>디자인 업무 타입</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>



    <div>
        <div class="dp-flex dp-flex-center">
            <div class="btn btn-lg btn-red" @click="save()" >저장</div>
            <div class="btn btn-white btn-lg" @click="self.close()" >닫기</div>
        </div>
    </div>

</section>

<script type="text/javascript">
    const projectSno = '<?=$requestParam['sno']?>';
    const type = '<?=$requestParam['type']?>';

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
                projectSno : projectSno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            if(200 === data.code){
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
                //parent.opener.refreshProjectList();
                parent.opener.location.reload();
            }
        });
    }

    $(appId).hide();

    $(()=>{
        ImsService.getData(DATA_MAP.PROJECT,projectSno).then((data)=>{
            if( 200 !== data.code  ){
                return false;
            }
            const initParams = {
                data: {
                    commentList: [],
                    fileList: data.data.fileList,
                    showComment: false,
                    comment: '',
                    customer: data.data.customer,
                    project: data.data.project,
                    projectExt: data.data.projectExt,
                },
                mounted : (vueInstance)=>{
                },
                methods: {
                    save: () => {
                        const saveObject = {
                            projectSno: projectSno,
                            ['ex'+$.ucfirst(type)]: vueApp.projectExt['ex'+$.ucfirst(type)], //예정일
                            ['cp'+$.ucfirst(type)]: vueApp.projectExt['cp'+$.ucfirst(type)], //완료일
                            ['tx'+$.ucfirst(type)]: vueApp.projectExt['tx'+$.ucfirst(type)], //대체텍스트
                        }
                        console.log(saveObject);
                        $.imsPost('saveProjectExt',{saveData : saveObject}).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 되었습니다.','', "success");
                                parent.opener.location.reload();
                                self.close();
                            }else{
                                $.msg(data.message,'', "warning");
                            }
                        });
                    },
                    saveComment : (comment)=>{
                        if( $.isEmpty(comment) ) {
                            $.msg('코멘트를 입력해주세요!','','warning');
                            return false;
                        }

                        $.imsPost('saveComment',{
                            'projectSno' : projectSno,
                            'comment' : comment,
                            'commentDiv' : type,
                        }).then((data)=>{
                            if(200 === data.code){
                                $.imsPost('getProjectCommentList',{
                                    projectSno : projectSno
                                    , commentType : type
                                }).then((data)=>{
                                    if(200 === data.code){
                                        console.log(data.data);
                                        initParams.data.commentList = data.data;
                                        parent.opener.location.reload();
                                    }else{
                                    }
                                });
                                //parent.opener.refreshProjectList();
                                vueApp.comment = '';
                            }else{
                                $.msg('코멘트 저장 오류.','개발팀 문의', "warning");
                            }

                        });
                    },
                    updateComment : (eachComment)=>{
                        $.imsPost('saveComment',{
                            sno : eachComment.sno
                            , comment : eachComment.comment
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('수정 되었습니다.','', "success").then(()=>{
                                    eachComment.commentBr = $.nl2br(eachComment.comment)
                                    eachComment.isModify = 'n';
                                });
                            }
                        });
                    },
                    setCommentBr : (item)=>{
                        item.commentBr = $.nl2br(item.comment)
                    },
                }
            }

            //코멘트 리스트 가져오기
            $.imsPost('getProjectCommentList',{
                projectSno : projectSno
                , commentType : type
            }).then((data)=>{
                if(200 === data.code){
                    console.log('comment data...',data.data);
                    initParams.data.commentList = data.data;
                    vueApp = ImsService.initVueApp(appId, initParams);
                }else{
                }
            });

        });
    });
</script>

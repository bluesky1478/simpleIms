<?php use Component\Imsv2\ImsScheduleUtil;

include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>{% customer.customerName %} <?=strip_tags($requestParam['title'])?> 일정</h3>
            <div class="btn-group">
                <input type="button" value="수정" class="btn btn-gray" v-if="'v' === viewMode" @click="viewMode = 'm'" >
                <input type="button" value="저장" class="btn btn-red" v-if="'m' === viewMode"  @click="save()" >
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <div class="mgb10">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                <span class="sl-blue"><?=strip_tags($requestParam['title'])?></span> 스케쥴
            </div>
            <div class="flo-right"></div>
        </div>
        <div class="clear-both"></div>
        <div class="mgt5">
            <table class="table table-cols">
                <colgroup>
                    <col class="w-15p">
                    <col class="w-45p">
                    <col class="w-15p">
                    <col class="">
                </colgroup>
                <tbody >
                <tr>
                    <th>고객사</th>
                    <td class="font-14">
                        {% customer.customerName %}
                    </td>
                    <th>프로젝트번호</th>
                    <td class="font-14 text-danger">
                        {% project.sno %}
                    </td>
                </tr>

                <tr>
                    <th>예정일</th>
                    <td class="font-14 relative">
                        <div class="dp-flex">

                            <date-picker v-model="projectExt.ex<?=ucfirst($requestParam['type'])?>" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>

                            <div class="mgl35 btn btn-sm btn-white" @click="ImsService.setSearchDate(projectExt, 'ex<?=ucfirst($requestParam['type'])?>', 'ex<?=ucfirst($requestParam['type'])?>', 'today')">오늘</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectExt, 'ex<?=ucfirst($requestParam['type'])?>', 'ex<?=ucfirst($requestParam['type'])?>', 'week')">이번주</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectExt, 'ex<?=ucfirst($requestParam['type'])?>', 'ex<?=ucfirst($requestParam['type'])?>', 'month')">이번달</div>
                        </div>
                    </td>
                    <th rowspan="2">완료일<br>대체텍스트</th>
                    <td rowspan="2" class="font-14" colspan="99">
                        <div >
                            <div class="" style="display: block !important;">
                                <input type="text" class="form-control" v-model="projectExt.tx<?=ucfirst($requestParam['type'])?>" placeholder="대체텍스트" style="display: inline-block !important;width:60%" maxlength="10">
                            </div>
                            <div class="mgt2 mgb3" v-if="!excludeType.includes('<?=$requestParam['type']?>')">
                                <a href="#" @click="projectExt.tx<?=ucfirst($requestParam['type'])?>='해당없음'" class="text-blue line">해당없음</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>완료일</th>
                    <td class="font-14">
                        <div class="dp-flex" v-if="!excludeType.includes('<?=$requestParam['type']?>')" >
                            <div >
                                <date-picker v-model="projectExt.cp<?=ucfirst($requestParam['type'])?>" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            </div>
                            <div class="mgl35 btn btn-sm btn-white" @click="ImsService.setSearchDate(projectExt, 'cp<?=ucfirst($requestParam['type'])?>', 'cp<?=ucfirst($requestParam['type'])?>', 'today')">오늘</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectExt, 'cp<?=ucfirst($requestParam['type'])?>', 'cp<?=ucfirst($requestParam['type'])?>', 'week')">이번주</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectExt, 'cp<?=ucfirst($requestParam['type'])?>', 'cp<?=ucfirst($requestParam['type'])?>', 'month')">이번달</div>
                        </div>
                        <div class="" v-if="excludeType.includes('<?=$requestParam['type']?>')" >
                            <div>{% projectExt.cp<?=ucfirst($requestParam['type'])?> %}</div>
                            <div class="font-11">조건에 따라 자동 등록됩니다.</div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                코멘트 리스트
            </div>
            <div class="flo-right"></div>
        </div>

        <table class="table table-cols ch-table table-pd-3 table-th-height30 table-td-height30">
            <colgroup>
                <col class="w-8p"  />
                <col class="w-10p" />
                <col class="w-10p"/>
                <col  />
                <col class="w-8p"  />
                <col class="w-8p" />
            </colgroup>
            <tr>
                <th>번호</th>
                <th>등록일</th>
                <th>등록자</th>
                <th>등록내용</th>
                <th>수정</th>
                <th>삭제</th>
            </tr>
            <tr v-for="(eachComment, eachCommentIndex) in commentList">
                <td>{% commentList.length - eachCommentIndex %}</td>
                <td>{% $.formatShortDateWithoutWeek(eachComment.regDt) %}</td>
                <td>{% eachComment.regManagerName %}</td>
                <td class="ta-l font-11">
                    <span v-html="eachComment.commentBr" v-show="'n' === eachComment.isModify"></span>
                    <textarea v-model="eachComment.comment" class="form-control w100" v-show="'y' === eachComment.isModify"></textarea>
                </td>
                <td>
                    <div class="btn btn-sm btn-white" @click="()=>{eachComment.isModify = 'y';commentModify='y'}" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'n' === eachComment.isModify ">수정</div>
                    <div class="btn btn-sm btn-red" @click="updateComment(eachComment)" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'y' === eachComment.isModify ">저장</div>
                    <div class="btn btn-sm btn-white" @click="()=>{eachComment.isModify = 'n';commentModify='n'}" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'y' === eachComment.isModify ">취소</div>
                </td>
                <td>
                    <div class="btn btn-sm btn-white" @click="ImsService.deleteData('projectComment',eachComment.sno, ()=>{parent.opener.location.reload(); location.reload();})" v-if="<?=$managerSno?> == eachComment.regManagerSno">삭제</div>
                </td>
            </tr>
            <tr v-if="0 >= commentList.length">
                <td colspan="99" class="ta-c">데이터가 없습니다.</td>
            </tr>

        </table>
    </div>

    <div>
        <div class="dp-flex dp-flex-center" v-show="!showComment">
            <?php if('mix14' !== $modifyType) { ?>
                <div class="btn btn-lg btn-gray" v-if="'v' === viewMode"  @click="viewMode = 'm'" >수정</div>
                <div class="btn btn-lg btn-red"  v-if="'m' === viewMode && 'n' === commentModify"  @click="save()" >저장</div>
            <?php } ?>
            <div class="btn btn-white btn-lg" @click="self.close()" >닫기</div>

            <div @click="showComment=true" class="btn btn-white btn-lg hover-btn cursor-pointer" v-show="!showComment">
                코멘트 등록 <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>

            <div @click="showComment=false" class="btn btn-white btn-lg hover-btn cursor-pointe" v-show="showComment">
                코멘트 등록 <i class="fa fa-chevron-up" aria-hidden="true"></i>
            </div>
        </div>

        <div class="clear-both mgt10" v-show="showComment">
            <textarea class="form-control" rows="5" placeholder="코멘트 입력" v-model="comment" ></textarea>
            <div class="ta-c mgt20">
                <div class="btn btn-lg btn-red" @click="saveComment(comment)">코멘트 등록</div>
                <div class="btn btn-lg btn-white" @click="showComment=false">취소</div>
            </div>
        </div>
    </div>

</section>

<script type="text/javascript">
    const projectSno = '<?=$requestParam['sno']?>';
    const type = '<?=$requestParam['type']?>';

    //완료일 강제 수정 불가
    const excludeType = JSON.parse('<?=json_encode(ImsScheduleUtil::getAutoScheduleKey())?>');

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
                    viewMode: 'm', //v : view , m : modify
                    commentModify: 'n', //v : view , m : modify
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
                        //console.log(saveObject);
                        $.imsPost('saveProjectExt',{saveData : saveObject}).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 되었습니다.','', "success");
                                try{
                                    window.opener.refreshProject(projectSno);
                                }catch (e){
                                    window.opener.refreshListExtFnc(1);
                                }
                                //parent.opener.location.reload();
                                //self.close();
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
                                        //console.log(data.data);
                                        initParams.data.commentList = data.data;
                                        //parent.opener.location.reload();
                                        try{
                                            window.opener.refreshProject(projectSno);
                                        }catch (e){
                                            window.opener.refreshListExtFnc(1);
                                        }
                                        vueApp.showComment = false;
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
                                    vueApp.commentModify = 'n';
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

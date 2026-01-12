<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>개별 ( {% customer.customerName %} <?=strip_tags($requestParam['title'])?> ) 항목 보기</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <div class="">
        <div class="table-title gd-help-manual">
            <div class="flo-left font-16">
                <span class="sl-blue"><?=strip_tags($requestParam['title'])?></span> 정보
            </div>
            <div class="flo-right">
                <div class="btn btn-white" v-if="'v' === viewMode" @click="viewMode = 'm'">일정 수정하기</div>
                <div class="btn btn-red" v-if="'m' === viewMode" viewMode = 'v' @click="save()">저장</div>
                <div class="btn btn-white" v-if="'m' === viewMode" @click="viewMode = 'v'">취소</div>
            </div>
        </div>
        <div class="clear-both"></div>
        <div class="mgt5">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody >
                <tr>
                    <th>고객사</th>
                    <td class="font-14">
                        {% customer.customerName %}
                    </td>
                    <th>프로젝트번호</th>
                    <td class="font-14">
                        {% project.projectNo %}
                    </td>
                </tr>

                <?php if(empty($requestParam['type']) || 'picker' == $requestParam['type']) { ?>
                    <tr>
                        <th>예정일</th>
                        <td class="font-14">
                            <div v-show="'v' === viewMode">
                                {% project.<?=$requestParam['div1']?> %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <date-picker v-model="project.<?=$requestParam['div1']?>" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            </div>
                        </td>
                        <th>완료일</th>
                        <td class="font-14">
                            <div v-show="'v' === viewMode">
                                {% project.<?=$requestParam['div2']?> %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <date-picker v-model="project.<?=$requestParam['div2']?>" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            </div>
                        </td>
                    </tr>
                <?php } ?>

                <?php if('text' === $requestParam['type']) { ?>
                    <tr>
                        <th>대기사유</th>
                        <td colspan="99">

                            <div v-show="'v' === viewMode">
                                {% project.<?=$requestParam['div1']?> %}
                            </div>

                            <div v-show="'m' === viewMode">
                                <input type="text" class="form-control" v-model="project.<?=$requestParam['div1']?>">
                            </div>

                        </td>
                    </tr>
                <?php } ?>

                <?php if('mix8' === $requestParam['type']) { ?>
                    <tr>
                        <th>미팅일자</th>
                        <td>
                            <div v-show="'v' === viewMode">
                                {% project.meetingInfoExpectedDt %}
                            </div>

                            <div v-show="'m' === viewMode">
                                <date-picker v-model="project.meetingInfoExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="미팅일자"></date-picker>
                            </div>
                        </td>
                        <th>정보</th>
                        <td>
                            <div v-show="'v' === viewMode">
                                {% project.meetingInfoMemo %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <input type="text" class="form-control" v-model="project.meetingInfoMemo" placeholder="시간/장소">
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if('mix9' === $requestParam['type']) { ?>
                    <tr>
                        <th>디자인</th>
                        <td colspan="99">
                            <div v-show="'v' === viewMode">
                                {% project.designAgreeMemo %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <select class="form-control w-90p inline-block" v-model="project.designAgreeMemo" style="width:50%">
                                    <option value="">미확인</option>
                                    <option >준비중</option>
                                    <option >준비완료</option>
                                    <option >참여불가</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if('mix10' === $requestParam['type']) { ?>
                    <tr>
                        <th>생산</th>
                        <td colspan="99">
                            <div v-show="'v' === viewMode">
                                {% project.qcAgreeMemo %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <select class="form-control w-90p inline-block" v-model="project.qcAgreeMemo" style="width:50%">
                                    <option value="">미확인</option>
                                    <option >준비중</option>
                                    <option >준비완료</option>
                                    <option >참여불가</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if('mix11' === $requestParam['type']) { ?>
                    <tr>
                        <th>유관부서 협의 예정일자</th>
                        <td>
                            <div v-show="'v' === viewMode">
                                {% project.allAgreeExpectedDt %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <date-picker v-model="project.allAgreeExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="협의예정"></date-picker>
                            </div>
                        </td>
                        <th>협의시간</th>
                        <td>
                            <div v-show="'v' === viewMode">
                                {% project.allAgreeMemo %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <input type="text" class="form-control" v-model="project.allAgreeMemo" placeholder="시간">
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if('mix12' === $requestParam['type']) { ?>
                    <tr>
                        <th>유관부서 협의 예정일자</th>
                        <td>
                            <div v-show="'v' === viewMode">
                                {% project.allAgreeCompleteDt %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <date-picker v-model="project.allAgreeCompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="협의완료"></date-picker>
                            </div>
                        </td>
                        <th>기타사항</th>
                        <td>
                            <div v-show="'v' === viewMode">
                                {% project.allAgreeEtcMemo %}
                            </div>
                            <div v-show="'m' === viewMode">
                                <input type="text" class="form-control" v-model="project.allAgreeEtcMemo" placeholder="기타사항">
                            </div>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                코멘트 등록
            </div>
            <div class="flo-right"></div>
        </div>

        <div>
            <textarea class="form-control" rows="5" placeholder="코멘트 입력" v-model="comment"></textarea>
        </div>
    </div>

    <div class="ta-c mgt20">
        <div class="btn btn-lg btn-red" @click="saveComment(comment)">코멘트 등록</div>
        <div class="btn btn-lg btn-white" @click="self.close()">닫기</div>
    </div>

    <div>
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                코멘트 리스트
            </div>
            <div class="flo-right"></div>
        </div>

        <table class="table table-rows ch-table">
            <colgroup>
                <col style="width:13%" />
                <col style="width:13%" />
                <col  />
                <col style="width:11%" />
                <col style="width:10%" />
            </colgroup>
            <tr>
                <th>등록일</th>
                <th>등록자</th>
                <th>등록내용</th>
                <th>수정</th>
                <th>삭제</th>
            </tr>
            <tr v-for="eachComment in commentList">
                <td>{% eachComment.regDt %}</td>
                <td>{% eachComment.regManagerName %}</td>
                <td class="ta-l">
                    <span v-html="eachComment.commentBr" v-show="'n' === eachComment.isModify"></span>
                    <textarea v-model="eachComment.comment" class="form-control w100" v-show="'y' === eachComment.isModify"></textarea>
                </td>
                <td>
                    <div class="btn btn-sm btn-white" @click="()=>{eachComment.isModify = 'y'}" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'n' === eachComment.isModify ">수정</div>
                    <div class="btn btn-sm btn-red" @click="updateComment(eachComment)" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'y' === eachComment.isModify ">저장</div>
                    <div class="btn btn-sm btn-white" @click="()=>{eachComment.isModify = 'n'}" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'y' === eachComment.isModify ">취소</div>
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

</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{

        console.log('loading...');

        //Load Data.
        const projectSno = '<?=$requestParam['sno']?>';
        const type = '<?=gd_isset($requestParam['type'],'picker')?>';
        const div1 = '<?=$requestParam['div1']?>';
        const div2 = '<?=$requestParam['div2']?>';

        console.log('projectSno',projectSno);
        console.log('type',type);
        console.log('div1',div1);
        console.log('div2',div2);

        ImsService.getData(DATA_MAP.PROJECT,projectSno).then((data)=>{
            if( 200 !== data.code  ){
                return false;
            }
            console.log(data.data);

            const initParams = {
                data: {
                    commentList: [],
                    comment: '',
                    customer: data.data.customer,
                    project: data.data.project,
                    viewMode: 'v', //v : view , m : modify
                },
                methods: {
                    save: () => {
                        const saveObject = {
                            sno: projectSno,
                            [div1]: vueApp.project[div1],
                        }
                        if(type !== 'text'){
                            saveObject[div2] = vueApp.project[div2];
                        }
                        $.imsPost('saveSimpleProject',{saveData : saveObject}).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 되었습니다.','', "success");
                                parent.opener.location.reload();
                                vueApp.viewMode = 'v'
                            }else{
                                $.msg(data.message,'', "warning");
                            }
                        });
                    },
                    saveComment : (comment)=>{
                        $.imsPost('saveComment',{
                            'projectSno' : projectSno,
                            'comment' : comment,
                            'commentDiv' : div1,
                        }).then((data)=>{
                            if(200 === data.code){
                                $.imsPost('getProjectCommentList',{
                                    projectSno : projectSno
                                    , commentType : div1
                                }).then((data)=>{
                                    if(200 === data.code){
                                        console.log(data.data);
                                        initParams.data.commentList = data.data;
                                    }else{
                                    }
                                });
                                parent.opener.location.reload();
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
                , commentType : div1
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

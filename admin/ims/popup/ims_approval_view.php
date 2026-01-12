<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="pdt70">
                {% document.subject %}
            </h3>

            <div class="dp-flex " style="position:absolute; top:6px; right:20px">

                <div class="" v-for="myApproval in document.targetManagerList" v-if="!$.isEmpty(myApproval.reason)">
                    전결처리함 : {% myApproval.reason %} ({% myApproval.name %})
                </div>

                <table class="table table-cols apply-table " style="width:350px; ">
                    <tbody>
                    <tr>
                        <th class="text-center">기안</th>
                        <th class="text-center" v-for="target in document.targetManagerList">{% target.appTitle %}</th>
                    </tr>
                    <tr>
                        <td class="text-center">
                            {% document.regManagerNm %}
                        </td>
                        <td class="text-center" v-for="target in document.targetManagerList">
                            {% target.name %}
                            <div class="rounded-circle bg-success" v-if="'accept' === target.status">승인</div><!--승인-->
                            <div class="rounded-circle bg-success" v-else-if="'complete' === target.status">PASS</div><!--승인(최종결재자결재)-->
                            <div class="rounded-circle bg-danger"  v-else-if="'reject' === target.status">반려</div><!--반려-->
                            <div class="rounded-circle bg-light" v-else>대기</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="ta-c font-11 text-muted">
                            {% $.formatShortDate(document.regDt) %}
                        </td>
                        <td class="ta-c text-center pd0 text-muted2 font-11" v-for="target in document.targetManagerList" style="height:25px !important;">
                            {% $.formatShortDate(target.completeDt) %}
                        </td>
                        <!--<td class="text-center pd0 text-muted2 font-11" style="height:25px !important;">24/04/01</td>
                        <td class="text-center pd0 text-muted2 font-11" style="height:25px !important;">24/04/05</td>
                        <td class="text-center pd0 text-muted2 font-11" style="height:25px !important;">24/04/08</td>-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col class="width-sm">
                    <col>
                </colgroup>
                <tbody>
                <?php include './admin/ims/popup/template/_sample.php'?>
                <?php include './admin/ims/popup/template/_salePrice.php'?>
                <?php include './admin/ims/popup/template/_cost.php'?>
                <?php include './admin/ims/popup/template/_ework.php'?>
                <tr>
                    <th>
                        요청내용
                    </th>
                    <td class="mgb150">
                        <div v-html="document.contentsNl2br" class="pd10" style="min-height:150px; line-height: 25px"></div>
                    </td>
                </tr>
                <tr>
                    <th>
                        고객/프로젝트
                    </th>
                    <td>
                        <span class="text-danger hover-btn cursor-pointer " @click="openProjectView(document.projectSno)">{% document.projectSno %}</span>
                        <span class="sl-blue hover-btn cursor-pointer " @click="openCustomer(document.customerSno)">{% document.customerName %}</span>
                        <span class="font-14" v-html="document.projectYear"></span>
                        <span class="font-14" v-html="document.projectSeason"></span>
                        <span class="font-14" v-html="document.styleName"></span>
                    </td>
                </tr>
                <tr v-if="!$.isEmptyObject(style)">
                    <th>
                        스타일
                    </th>
                    <td class="font-14">
                        <span @click="openProductReg2(project.sno, style.styleSno)" class="hover-btn cursor-pointer">
                            {% style.styleFullName %}
                            <span class="font-12">({% style.styleCode %})</span>
                        </span>
                    </td>
                </tr>
                <tr v-if="!$.isEmpty(ImsTodoService.approvalType[document.approvalType].fileDiv)">
                    <th>
                        {% ImsTodoService.approvalType[document.approvalType].name %} 파일
                    </th>
                    <td>
                        <div v-if="!$.isEmpty(project.projectFile) && $.isObject(project.projectFile)">
                            <ul class="ims-file-list" >
                                <li class="hover-btn" v-for="(file, fileIndex) in project.projectFile[ImsTodoService.approvalType[document.approvalType].fileDiv].files">
                                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">
                                        {% fileIndex+1 %}. {% file.fileName %}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <!--
                <tr >
                    <th>
                        추가 첨부
                    </th>
                    <td>
                        <ul class="ims-file-list" >
                            <li class="hover-btn" v-for="(file, fileIndex) in document.todoFile1">
                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                            </li>
                        </ul>
                    </td>
                </tr>
                -->
                <tr>
                    <th >
                        첨언
                    </th>
                    <td>
                        <table class="table table-rows table-fixed">
                            <colgroup>
                                <col class="width-sm">
                                <col>
                                <col style="width:100px">
                            </colgroup>
                            <tbody>
                            <tr v-for="(todoComment, todoCommentIndex) in todoCommentList">
                                <td style="vertical-align: top" class="font-11">
                                    {% todoComment.regManagerNm %}({% todoComment.regManagerId %})
                                    <br>
                                    <span class="text-muted font-11">
                                        {% todoComment.regDt %}
                                    </span>
                                </td>
                                <td>
                                    <form name="frmMemo2" action="memo_ps.php" method="post" target="ifrmProcess">

                                        <div class="js-text-memo" v-html="todoComment.commentBr"
                                             v-if="typeof todoComment.commentToggle == 'undefined' || false === todoComment.commentToggle">
                                        </div>

                                        <form method="post" >
                                            <div v-if="typeof todoComment.commentToggle != 'undefined' && true === todoComment.commentToggle">
                                                <div >
                                                    <textarea class="form-control " v-model="todoComment.comment" rows="4"></textarea>
                                                </div>
                                                <div class="mgt5 ta-c">
                                                    <button type="button" class="btn btn-white " @click="modifyComment(document.sno, todoComment.comment, todoComment.sno)">수정</button>
                                                    <button type="button" class="btn btn-white mgl5" @click="setCommentModifyToggle(todoCommentIndex, false)">취소</button>
                                                </div>
                                            </div>
                                        </form>

                                    </form>
                                </td>
                                <td style="vertical-align:top">
                                    <div v-if="<?=$managerInfo['sno']?> == todoComment.regManagerSno">
                                        <button class="btn btn-white btn-sm js-btn-memo-modify" @click="setCommentModifyToggle(todoCommentIndex, true)">수정</button>
                                        <button class="btn btn-white btn-sm js-btn-memo-delete" title="확인" @click="ImsService.deleteData('todoComment', todoComment.sno, ()=>{ ImsTodoService.getListTodoComment(document.sno) })">삭제</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$managerInfo['managerNm']?>(<?=$managerInfo['managerId']?>)
                                </td>
                                <td colspan="2">
                                    <form name="frmMemoWrite" method="post">
                                        <textarea class="form-control " name="memo" required="" v-model="todoComment" placeholder="댓글" rows="4"></textarea>
                                        <button type="button" class="btn btn-white mgt5" @click="writeComment(document.sno)">저장</button>
                                    </form>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="dp-flex" style="justify-content: center">

            <!--{% document.approvalStatus %} // {% document.myApprovalStatus %} // {% document.myCancel %}-->

            <div class="btn btn-reject hover-btn btn-lg mg5"
                 @click="ImsTodoService.setApprovalStatus(document.sno,'remove')"
                 v-show="<?=$managerInfo['sno']?> == document.regManagerSno">
                결재 요청 취소
            </div>

            <!--내가 최종 결재자가 아니라면.-->
            <div class="btn btn-accept hover-btn btn-lg mg5" style="background-color:#047759 !important;"
                 @click="ImsTodoService.setApprovalComplete(document.sno)"
                 v-show="'proc' === document.myApprovalStatus && true === document.myAccept && true != document.myAcceptInfo.isLast">
                전결승인
            </div>

            <div class="btn btn-accept hover-btn btn-lg mg5"
                 @click="ImsTodoService.setApprovalStatus(document.sno,'accept')"
                 v-show="'proc' === document.myApprovalStatus && true === document.myAccept">
                승인
            </div>

            <div class="btn btn-reject hover-btn btn-lg mg5"
                 @click="ImsTodoService.setApprovalStatus(document.sno,'reject')"
                 v-show="'proc' == document.myApprovalStatus && true == document.myAccept">
                반려
            </div>

            <!--기안자 , 결재자만 취소 가능 처리하기-->
            <!--<div class="btn btn-reject hover-btn btn-lg mg5"
                 @click="ImsTodoService.setApprovalStatus(document.sno,'cancel')"
                 v-show="true == document.myCancel">
                <span >결재취소</span>
            </div>-->

            <!--<div class="btn btn-reject hover-btn btn-lg mg5"
                 v-if="!$.isEmpty(document.projectSno) && 0 != document.projectSno && 'accept'===document.status"
                 v-show="true != document.myCancel"
                 @click="ImsService.reqEmergencyTodo(document.subject+' 취소 요청.', document.projectSno, document.targetManagerList[document.targetManagerList.length-1]['sno'])">
                <span >결재취소 요청</span>
            </div>-->

            <!--관리자 결재 취소 기능-->
            <?php if(!empty(\SiteLabUtil\SlCommonUtil::isDevId())) { ?>
            <!--<div class="btn btn-reject hover-btn btn-lg mg5"
                 @click="ImsTodoService.setApprovalStatus(document.sno,'cancel')">
                <span >결재/반려 취소</span>
            </div>-->
            <?php } ?>

            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>

    </div>

    <div class="text-muted">#결재번호:{% document.sno %}</div>

</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const sno = '<?=$requestParam['sno']?>';
        const resSno = '<?=$requestParam['resSno']?>';
        ImsService.getDataParams(DATA_MAP.TODO_REQUEST, {
            sno : sno,
            resSno : resSno,
        } ).then((data)=>{
            console.log('초기 데이터',data);
            const initParams = {
                data : {
                    document : data.data,
                    project : {},
                    customer : {},
                    style : {},
                    sample : {},
                    productList : {},
                    todoComment : '',
                    todoCommentList : [],
                },
                mounted : (vueInstance)=>{
                    ImsTodoService.getListTodoComment(sno);
                    //프로젝트 셋팅
                    const projectSno = vueApp.document.projectSno;
                    ImsService.getData(DATA_MAP.PROJECT,projectSno).then((prjData)=>{
                        vueApp.project = prjData.data.project;
                        vueApp.customer = prjData.data.customer;
                        vueApp.project.projectFile = prjData.data.fileList;
                        //vueApp.document.projectSno = prjData.data.project.sno;

                        if( vueApp.document.styleSno > 0 ){
                            const styleInfo = prjData.data.productList.filter(style => vueApp.document.styleSno == style.sno)[0];
                            vueApp.style = styleInfo;
                        }

                        vueApp.productList = prjData.data.productList;


                        //샘플 불러오기
                        //console.log(`샘플 불러올 정보 ${eachSno} / ${vueApp.document.approvalType}`);

                        const eachSno = vueApp.document.eachSno;
                        if(!$.isEmpty(eachSno) && 'sampleFile1' === vueApp.document.approvalType ){
                            ImsService.getData(DATA_MAP.SAMPLE,eachSno).then((sampleData)=>{
                                if( 200 === sampleData.code ){
                                    vueApp.sample = sampleData.data;
                                    vueApp.document.styleSno = sampleData.data.styleSno;
                                    vueApp.document.eachSno = sampleData.data.sno;
                                    vueApp.document.eachDiv = 'sample';
                                }else{
                                    console.log(sampleData.message);
                                }
                            });
                        }

                    });
                },
                methods : {
                    saveExpectedDt : function(){
                        if( $.isEmpty(vueApp.document.expectedDt) ){
                            $.msg('예정일을 입력해주세요.','','warning');
                        }else{
                            $.imsPost('saveTodoExpectedDt',{
                                sno : vueApp.document.resSno,
                                expectedDt : vueApp.document.expectedDt,
                            }).then((data)=>{
                                if( 200 === data.code ){
                                    $.msg('저장 되었습니다.', "", "success").then(()=>{
                                        if( $.isEmpty(sno) || 0 == sno ){
                                            self.close();
                                        }
                                    });
                                }
                            });
                        }
                    },
                    setTodoStatus : function(resSno){
                        ImsTodoService.setTodoStatus([resSno],'complete', sno);
                    },
                    writeComment : function(reqSno){
                        ImsTodoService.writeComment(reqSno,vueApp.todoComment, 0).then((data)=>{
                            vueApp.todoComment = '';
                            ImsTodoService.getListTodoComment(sno);
                        });
                    },
                    modifyComment : function(reqSno, comment, commentSno){
                        ImsTodoService.writeComment(reqSno,comment, commentSno).then((data)=>{
                            vueApp.todoComment = '';
                            ImsTodoService.getListTodoComment(sno);
                        });
                    },
                    setCommentModifyToggle : function(todoCommentIndex, flag){
                        vueApp.todoCommentList[todoCommentIndex].commentToggle=flag;
                        vueApp.$forceUpdate()
                    }
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

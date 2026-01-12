<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">{% items.subject %}</h3>
            <div class="btn-group font-18 bold">
                <!--TODO : 한사람 이라도 읽으면->완료일 표기시 내용 수정 불가-->
                <input type="button" value="수정" class="btn btn-red" @click="location.href='ims_todo_request_write.php?sno=<?=$requestParam['sno']?>'"  v-if="<?=$managerInfo['sno']?> == items.regManagerSno" />
                <input type="button" value="닫기" class="btn btn-white " @click="self.close()" />
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
                <tr>
                    <th>
                        요청자
                    </th>
                    <td>
                        {% items.regManagerNm %}
                    </td>
                </tr>
                <tr v-if="items.targetManagerSno > 0">
                    <th>
                        요청 대상
                    </th>
                    <td>
                        <div class="mgt5"></div>

                        <div class="pdr10 bold font-14" v-for="resTarget in items.targetManagerList"  v-if="items.targetManagerSno == resTarget.sno">
                            {% resTarget.name %}
                        </div>

                        <div class="dp-flex">
                            <div class="mgr5" v-for="refManager in items.refManagerList">
                                {% refManager.managerNm %} <span class="text-muted font-9">({% refManager.statusKr %}상태)</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr v-if="0 >= items.targetManagerSno">
                    <th>
                        요청 대상
                    </th>
                    <td style="padding:0!important;">

                        <table class="table table-rows-soft table-center border-0 table-pd-0 mg0 ims-list-sub-table table-borderless"  style="border:none !important;">
                            <colgroup>
                                <col style="width:100px!important;">
                                <col class="" v-for="resTarget in items.targetManagerList">
                            </colgroup>
                            <tr>
                                <td style="background-color:#f1f1f1;border-top:none !important;" class="bold border-0 border-top-0">대상자명</td>
                                <td v-for="resTarget in items.targetManagerList" style="background-color:#f1f1f1;border-top:none !important;">{% resTarget.name %}</td>
                            </tr>
                            <tr>
                                <td style="background-color:#f1f1f1" class="bold border-0">상태</td>
                                <td v-for="resTarget in items.targetManagerList">{% resTarget.statusKr %}</td>
                            </tr>
                            <tr>
                                <td style="background-color:#f1f1f1" class="bold">예정일</th>
                                <td v-for="resTarget in items.targetManagerList" class="font-12">
                                    {% $.formatShortDate(resTarget.expectedDt) %}
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#f1f1f1" class="bold">완료일</td>
                                <td v-for="resTarget in items.targetManagerList" class="font-12">
                                    {% $.formatShortDate(resTarget.completeDt) %}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>
                        완료 희망일
                    </th>
                    <td class="font-14">
                        {% $.formatShortDate(items.hopeDt) %}
                    </td>
                </tr>

                <tr v-if=" 'complete' === items.status">
                    <th>
                        처리 정보
                    </th>
                    <td>
                        {% items.completeManagerNm %}
                        {% $.formatShortDate(items.completeDt) %} 완료
                    </td>
                </tr>

                <tr v-if=" 'complete' !== items.status && items.targetManagerSno > 0">
                    <th>
                        처리 예정일
                    </th>
                    <td>
                        <div v-if="'complete' !== items.status">
                            <date-picker value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="완료 예정일" style="width:140px;font-weight: normal" v-model="items.expectedDt"></date-picker>
                            <div class="btn btn-sm btn-red-line2 btn-red mgl10 hover-btn" @click="saveExpectedDt()">예정일 저장</div>

                            <span class="pdl20">
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',0)">오늘</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',1)">+1</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',2)">+2</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',3)">+3</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',4)">+4</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',5)">+5</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',10)">+10</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(items, 'expectedDt',15)">+15</div>
                            </span>

                        </div>
                        <div v-if="'complete' === items.status" class="font-14">
                            {% $.formatShortDate(items.expectedDt) %}
                        </div>

                    </td>
                </tr>

                <tr v-if="items.targetManagerSno > 0">
                    <th>
                        현재 상태
                    </th>
                    <td class="font-14">
                        {% items.statusKr %}
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php if( !empty($requestParam['projectSno']) ) { ?>
                            고객/프로젝트
                        <?php }else{ ?>
                            문의 타입
                        <?php } ?>
                    </th>
                    <td>
                        <?php if( !empty($requestParam['projectSno']) ) { ?>
                            <span class="text-blue">{% document.customerName %}</span>
                            <span class="cursor-pointer hover-btn text-danger" @click="openProjectViewAndSetTabMode(document.projectSno,'basic')">{% document.projectNo %}({% document.projectYear %}{% document.projectSeason %})</span>
                        <?php }else{ ?>

                            <div v-if="0 >= Number(items.projectSno)">
                                일반문의
                            </div>
                            <div v-if="Number(items.projectSno) > 0">
                                <span class="text-blue">{% items.customerName %}</span>
                                <span class="cursor-pointer hover-btn text-danger" @click="openProjectViewAndSetTabMode(items.projectSno,'basic')">{% items.projectNo %}({% items.projectYear %}{% items.projectSeason %})</span>
                            </div>

                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        요청 내용
                    </th>
                    <td class="mgb150 mgt15" style="vertical-align: top">
                        <div v-html="items.contentsNl2br" class="pd10" style="min-height:150px; line-height: 25px"></div>
                    </td>
                </tr>
                <tr >
                    <th>
                        첨부
                    </th>
                    <td>
                        <ul class="ims-file-list" >
                            <li class="hover-btn" v-for="(file, fileIndex) in items.todoFile1">
                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr >
                    <th >
                        댓글
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
                                                    <button type="button" class="btn btn-white " @click="modifyComment(items.sno, todoComment.comment, todoComment.sno)">수정</button>
                                                    <button type="button" class="btn btn-white mgl5" @click="setCommentModifyToggle(todoCommentIndex, false)">취소</button>
                                                </div>
                                            </div>
                                        </form>

                                    </form>
                                </td>
                                <td style="vertical-align:top">
                                    <div v-if="<?=$managerInfo['sno']?> == todoComment.regManagerSno">
                                        <button class="btn btn-white btn-sm js-btn-memo-modify" @click="setCommentModifyToggle(todoCommentIndex, true)">수정</button>
                                        <button class="btn btn-white btn-sm js-btn-memo-delete" title="확인" @click="ImsService.deleteData('todoComment', todoComment.sno, ()=>{ ImsTodoService.getListTodoComment(items.sno) })">삭제</button>
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
                                        <button type="button" class="btn btn-red btn-red-line2 mgt5" @click="writeComment(items.sno)">댓글등록</button>
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

            <div class="btn btn-accept hover-btn btn-lg mg5" v-if="'complete' !== items.status && items.targetManagerSno > 0" @click="setTodoStatus(items.resSno)">
                {% $.formatShortDate(items.completeDt) %} {% items.completeManagerNm %} 처리완료
            </div>

            <div class="btn btn-red hover-btn btn-lg mg5" v-if="<?=$managerInfo['sno']?> == items.regManagerSno && 'complete' !== items.status " @click="ImsService.deleteData('todoRequest',items.resSno, parentRefreshClose)">요청삭제</div>

            <div class="btn-lg mg5" v-if="'complete' === items.status && items.targetManagerSno > 0" >
                {% $.formatShortDate(items.completeDt) %} {% items.completeManagerNm %} 처리 완료
            </div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>

    </div>

</section>

<script type="text/javascript">

    const parentOpener = parent.opener;

    $(appId).hide();

    const parentRefreshClose = ()=>{
        //parentOpener.refreshTodoResponseList();
        parent.opener.location.reload(); //부모창 갱신.
        self.close();
    }

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
                    items : data.data,
                    todoComment : '',
                    todoCommentList : [],
                },
                mounted : (vueInstance)=>{
                    ImsTodoService.getListTodoComment(sno);
                },
                methods : {
                    saveExpectedDt : function(){

                        $.imsPost('saveTodoExpectedDt',{
                            sno : vueApp.items.resSno,
                            expectedDt : vueApp.items.expectedDt,
                        }).then((data)=>{
                            if( 200 === data.code ){
                                $.msg('저장 되었습니다.', "", "success").then(()=>{
                                    if( $.isEmpty(sno) || 0 == sno ){
                                        self.close();
                                    }
                                });
                            }
                        });

                        /*if( $.isEmpty(vueApp.items.expectedDt) ){
                            $.msg('예정일을 입력해주세요.','','warning');
                        }else{

                        }*/
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

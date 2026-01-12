<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_nk.php'?>

<?php include './admin/ims/library_nk_file_multi_view_modal.php'?>
<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">프로젝트/스타일 이슈 {% issueDetail.sno == 0 ? '등록' : (isModify ? '수정' : '상세') %}</h3>
        <div class="btn-group font-18 bold">
            <input type="button" v-show="issueDetail.sno != 0" class="btn btn-blue-line hover-btn" @click="openCommonPopup('update_history_list', 600, 810, {type:1, sno:issueDetail.sno});" value="수정이력" />
        </div>
    </div>
    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col style="width:10%;">
                    <col>
                </colgroup>
                <tbody>
                <tr v-show="issueDetail.sno != 0">
                    <th>상태</th>
                    <td>
                        {% issueDetail.issueStHan %}
                        <span class="btn btn-sm btn-white hover-btn cursor-pointer" v-show="!isModify && issueDetail.issueSt == 1" @click="changeSt(2)">처리시작</span>
                        <span class="btn btn-sm btn-white hover-btn cursor-pointer" v-show="!isModify && issueDetail.issueSt == 2" @click="changeSt(3)">종결</span>
                        <span class="btn btn-sm btn-white hover-btn cursor-pointer" v-show="!isModify && issueDetail.issueSt == 3" @click="changeSt(2)">재조치</span>
                    </td>
                </tr>
                <tr>
                    <th>이슈 제목</th>
                    <td>
                        <input type="hidden" v-model="issueDetail.sno" />
                        <?php $model='issueDetail.issueSubject'; $placeholder='이슈 제목' ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>고객</th>
                    <td>
                        <div v-show="isModify">
                            <select2 class="js-example-basic-single" v-model="issueDetail.customerSno" style="width:100%" >
                                <option value="0">선택</option>
                                <?php foreach ($customerListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </div>
                        <div v-show="!isModify" >
                            {% issueDetail.customerName %}
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>프로젝트</th>
                    <td>
                        <div v-show="isModify">
                            <select2 class="form-control" v-model="issueDetail.projectSno" style="width:100%" >
                                <option v-for="val in aSelectProject" :value="val.key">{% val.text %}</option>
                            </select2>
                        </div>
                        <div v-show="!isModify" >
                            {% issueDetail.projectName %}
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>스타일</th>
                    <td>
                        <div v-show="isModify">
                            <select2 class="form-control" v-model="issueDetail.styleSno"  style="width:100%" >
                                <option v-for="val in aSelectStyle" :value="val.key">{% val.text %}</option>
                            </select2>
                        </div>
                        <div v-show="!isModify" >
                            {% issueDetail.styleName %}
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>유형</th>
                    <td>
                        <div v-show="isModify">
                            <label class="radio-inline">
                                <input type="radio" name="chooseType" value="" v-model="issueDetail.issueType" data-text="" style="margin:0!important;" />
                                <span class="font-12">선택</span>
                            </label>
                            <?php foreach(\Component\Ims\NkCodeMap::PROJECT_ISSUE_TYPE as $key => $val) { ?>
                            <label class="radio-inline">
                                <input type="radio" name="chooseType" value="<?=$key?>" v-model="issueDetail.issueType" data-text="<?=$val?>" style="margin:0!important;" />
                                <span class="font-12"><?=$val?></span>
                            </label>
                            <?php } ?>
                            <input type="text" v-show="issueDetail.issueType==5" class="form-control mgt5" v-model="issueDetail.issueTypeText" placeholder="유형명 기타입력" />
                        </div>
                        <div v-show="!isModify">{% issueDetail.issueTypeText %}</div>
                    </td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td>
                        <div v-show="isModify">
                            <textarea class="form-control" rows="3" v-model="issueDetail.issueContents" placeholder="내용"></textarea>

                            <!--첨부파일 관련 - html 정의-->
                            <file-upload2 :file="issueDetail.fileList[sAppendFileDiv]" :id="sAppendFileDiv" :params="issueDetail" :accept="false"></file-upload2>
                        </div>
                        <div v-show="!isModify" >
                            <div v-if="!$.isEmpty(issueDetail.issueContents)" v-html="issueDetail.issueContents.replaceAll('\n','<br/>')"></div>
                            <div v-else class="text-muted">미입력</div>
                        </div>
                        <div>
                            <img v-if="checkImageExtension(val.fileName)" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in issueDetail.fileList[sAppendFileDiv].files" style="width:150px;" @click="revertReferFilePath(issueDetail.fileList[sAppendFileDiv].files); openMultiFileView(referFilePaths);" class="cursor-pointer hover-btn" />
                        </div>
                    </td>
                </tr>
                <tr v-show="issueDetail.sno != 0">
                    <th>원인</th>
                    <td>
                        <div v-show="isModify">
                            <textarea class="form-control" rows="3" v-model="issueDetail.issueReason" placeholder="원인"></textarea>
                        </div>
                        <div v-show="!isModify" >
                            <div v-if="!$.isEmpty(issueDetail.issueReason)" v-html="issueDetail.issueReason.replaceAll('\n','<br/>')"></div>
                            <div v-else class="text-muted">미입력</div>
                        </div>
                    </td>
                </tr>
                <tr v-show="issueDetail.sno != 0">
                    <th>영향범위</th>
                    <td>
                        <div v-show="isModify">
                            <textarea class="form-control" rows="3" v-model="issueDetail.issueRange" placeholder="영향범위"></textarea>
                        </div>
                        <div v-show="!isModify" >
                            <div v-if="!$.isEmpty(issueDetail.issueRange)" v-html="issueDetail.issueRange.replaceAll('\n','<br/>')"></div>
                            <div v-else class="text-muted">미입력</div>
                        </div>
                    </td>
                </tr>
                <tr v-show="issueDetail.sno != 0 && !isModify">
                    <th>댓글</th>
                    <td>
                        <!--댓글관련-->
                        <table class="table table-rows table-fixed">
                            <colgroup>
                                <col class="width-sm">
                                <col>
                                <col style="width:100px">
                            </colgroup>
                            <tbody>
                            <tr v-for="(val, key) in replyList">
                                <td style="vertical-align: top" class="font-11">
                                    {% val.regManagerName %}({% val.regManagerId %})
                                    <br>
                                    <span class="text-muted font-11">
                                        {% val.regDt %}
                                    </span>
                                </td>
                                <td>
                                    <div class="js-text-memo" v-html="val.commentBr"
                                         v-if="typeof val.commentToggle == 'undefined' || false === val.commentToggle">
                                    </div>
                                    <div v-if="typeof val.commentToggle != 'undefined' && true === val.commentToggle">
                                        <div >
                                            <textarea class="form-control " v-model="val.comment" rows="2"></textarea>
                                        </div>
                                        <div class="mgt5 ta-c">
                                            <button type="button" class="btn btn-white " @click="modifyReply(val.sno, val.comment)">수정</button>
                                            <button type="button" class="btn btn-white mgl5" @click="setReplyModifyToggle(key, false)">취소</button>
                                        </div>
                                    </div>
                                </td>
                                <td style="vertical-align:top">
                                    <div v-if="<?=$managerInfo['sno']?> == val.regManagerSno">
                                        <button class="btn btn-white btn-sm " @click="setReplyModifyToggle(key, true)">수정</button>
                                        <button class="btn btn-white btn-sm " title="확인" @click="ImsService.deleteData('projectComment', val.sno, ()=>{ vueApp.getListReply(); parent.opener.refreshList(); })">삭제</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$managerInfo['managerNm']?>(<?=$managerInfo['managerId']?>)
                                </td>
                                <td colspan="2">
                                    <textarea style="width:100%;" class="form-control " name="memo" required="" v-model="replyComment" placeholder="댓글" rows="2"></textarea>
                                    <button type="button" class="btn btn-red btn-red-line2 mgt5" @click="writeReply()">댓글등록</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="dp-flex" style="justify-content: center">
        <div class="btn btn-accept hover-btn btn-lg mg5" v-show="!isModify" @click="isModify=true">수정</div>
        <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="save()">저장</div>
        <div class="btn btn-white hover-btn btn-lg mg5" v-show="isModify && issueDetail.sno != 0" @click="isModify=false">수정취소</div>
        <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
    </div>

    <div v-show="issueDetail.sno != 0 && !isModify" class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">조치내역</h3>
        <div class="btn-group font-18 bold">
            <input type="button" class="btn btn-blue hover-btn" @click="openCommonPopup('project_issue_action_upsert', 500, 610, {'issueSno':issueDetail.sno, 'sno':0});" value="등록" />
        </div>
    </div>
    <div v-show="issueDetail.sno != 0 && !isModify" class="">
        <table class="table table-rows table-default-center table-td-height30 mgt5 ">
            <colgroup>
                <col class="w-7p" />
                <col class="w-10p" />
                <col class="w-10p" />
                <col class="" />
                <col class="w-13p" />
            </colgroup>
            <tr>
                <th >번호</th>
                <th >등록일</th>
                <th >처리자</th>
                <th >처리사항</th>
                <th >관리</th>
            </tr>
            <tr v-show="aActionList.length == 0">
                <td colspan="5">조치내역이 없습니다.</td>
            </tr>
            <tr v-for="(val, key) in aActionList">
                <td>{% val.sno == 0 ? '신규' : (aActionList.length - key) %}</td>
                <td>{% val.regDt %}</td>
                <td>{% val.regManagerName %}</td>
                <td class="ta-l">
                    <div v-html="val.actionContents.replaceAll('\n','<br>')"></div>
                    <div>
                        <img v-if="checkImageExtension(val2.fileName)" :src="'<?=$nasUrl?>'+val2.filePath" v-for="(val2, key2) in val.fileList[sAppendFileDiv2].files" style="width:150px;" @click="revertReferFilePath(val.fileList[sAppendFileDiv2].files); openMultiFileView(referFilePaths);" class="cursor-pointer hover-btn" />
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-white " @click="openCommonPopup('project_issue_action_upsert', 500, 610, {'issueSno':issueDetail.sno, 'sno':val.sno});">수정</button>
                    <button type="button" class="btn btn-white mgl5" @click="deleteIssueAction(val.sno)">삭제</button>
                </td>
            </tr>
        </table>
    </div>

    <?php if ((int)$requestParam['customerSno'] > 0 && (int)$requestParam['projectSno'] > 0) { /* 프로젝트상세에서 open시에만 실행함 */ ?>
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class=""><?=(int)$requestParam['styleSno']>0?'스타일':'프로젝트'?> 이슈 리스트</h3>
    </div>
    <div>
        <table class="table table-rows table-default-center table-td-height30 mgt5 ">
            <colgroup>
                <col class="w-10p" />
                <col class="w-10p" />
                <col class="" />
                <col class="w-10p" />
                <col class="w-10p" />
                <col class="w-20p" />
            </colgroup>
            <tr>
                <th >번호</th>
                <th v-for="fieldData in aoIssueFldList"  v-if="fieldData.skip != true && fieldData.name != 'projectTitle' && fieldData.name != 'styleTitle'" :class="fieldData.titleClass">
                    {% fieldData.title %}
                </th>
            </tr>
            <tr  v-if="0 >= aoIssueRowList.length">
                <td colspan="99">
                    데이터가 없습니다.
                </td>
            </tr>
            <tr v-for="(val , key) in aoIssueRowList" :class="issueDetail.sno == val.sno ? 'focused' : ''">
                <td >{% (aoIssueRowList.length - key) %}</td>
                <td v-for="fieldData in aoIssueFldList"  v-if="fieldData.skip != true && fieldData.name != 'projectTitle' && fieldData.name != 'styleTitle'" :class="fieldData.class">
                    <span v-if="fieldData.type === 'title'" class="sl-blue  cursor-pointer hover-btn" @click="location.href=sRedirectUrl + val.sno + sRedirectUrlParam<?=(int)$requestParam['styleSno']>0?'2':'1'?>">
                        {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                        <span v-if="val.cnt_reply > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% val.cnt_reply %}</div></span>
                    </span>
                    <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                    <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                    <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                </td>
            </tr>
        </table>
    </div>
    <?php } ?>

</section>

<script type="text/javascript">
    //자식 팝업창에서 실행
    function refreshActionList(chgSt) {
        if (Number(chgSt) != 0) {
            vueApp.issueDetail.issueSt = Number(chgSt);
            vueApp.issueDetail.issueStHan = vueApp.issueDetail.issueSt == 2 ? '조치중' : '종결';
        }
        vueApp.getIssueActionList();
    }

    $(()=>{
        //프로젝트상세 or 스타일리스트에서 이슈등록클릭으로 진입시 고객사sno, 프로젝트sno, 스타일sno 파라메터 던져줌
        var oSendParam = { issueSnoGet:'<?=(int)$requestParam['sno']?>', customerSnoGet:'<?=(int)$requestParam['customerSno']?>', projectSnoGet:'<?=(int)$requestParam['projectSno']?>' };
        var styleSnoGet = '<?=(int)$requestParam['styleSno']?>';
        ImsNkService.getList('projectIssue', oSendParam).then((data)=>{
            $.imsPostAfter(data, (data)=> {
                let isModify = true;
                let issueDetail = data.info;
                let aSelectProject = data.list_project_selects;
                let aSelectStyle = data.list_style_selects;
                if (issueDetail.sno > 0) { //수정인 경우
                    isModify = false;
                } else {
                    if (oSendParam.customerSnoGet != 0) issueDetail.customerSno = oSendParam.customerSnoGet;
                    if (oSendParam.projectSnoGet != 0) issueDetail.projectSno = oSendParam.projectSnoGet;
                    if (styleSnoGet != 0) issueDetail.styleSno = styleSnoGet;
                }

                const initParams = {
                    data: {
                        isModify: isModify,
                        issueDetail: issueDetail,
                        aSelectProject: aSelectProject,
                        aSelectStyle: aSelectStyle,
                        aActionList: [],
                        //댓글관련 - replayType변수에 고유값 넣기
                        replyComment : '',
                        replyList : [],
                        replayType:'projectIssue',
                        //첨부파일 관련 - fileDiv값 정의
                        sAppendFileDiv:'projectIssueFile1',
                        sAppendFileDiv2:'projectIssueActionFile1',
                        referFilePaths:[],

                        sRedirectUrl : "/ims/popup/ims_pop_project_issue_upsert.php?sno=",
                        sRedirectUrlParam1 : "&customerSno="+oSendParam.customerSnoGet+"&projectSno="+oSendParam.projectSnoGet,
                        sRedirectUrlParam2 : "&customerSno="+oSendParam.customerSnoGet+"&projectSno="+oSendParam.projectSnoGet+"&styleSno="+styleSnoGet,
                        aoIssueFldList : [], //프로젝트상세에서 open한 경우 하단에 리스트를 띄우기 위해 쓰임
                        aoIssueRowList : [], //프로젝트상세에서 open한 경우 하단에 리스트를 띄우기 위해 쓰임
                    },
                    methods : {
                        //조치내역 가져오기
                        getIssueActionList : ()=>{
                            ImsNkService.getList('projectIssueAction', {'issueSno':vueApp.issueDetail.sno}).then((data)=>{
                                $.imsPostAfter(data, (data)=> {
                                    vueApp.aActionList = data.list;
                                });
                            });
                        },
                        //고객사 변경시 프로젝트 selectbox 갱신
                        refreshProjectSelect : (val)=>{
                            vueApp.aSelectProject = [{key:'0', text:'선택'}];
                            vueApp.aSelectStyle = [{key:'0', text:'선택'}];
                            if (val != '0') {
                                ImsNkService.getList('projectSimple', { customerSno:val }).then((data)=>{
                                    $.imsPostAfter(data, (data)=> {
                                        $.each(data, function(key, val) {
                                            vueApp.aSelectProject.push({key:val.sno, text:val.projectName});
                                        });
                                        vueApp.issueDetail.projectSno = '0';
                                        vueApp.issueDetail.styleSno = '0';
                                    });
                                });
                            }
                        },
                        //프로젝트 변경시 스타일 selectbox 갱신
                        refreshStyleSelect : (val)=>{
                            vueApp.aSelectStyle = [{key:'0', text:'선택'}];
                            if (val != '0') {
                                ImsNkService.getList('styleSimple', { projectSno:val }).then((data)=>{
                                    $.imsPostAfter(data, (data)=> {
                                        $.each(data, function(key, val) {
                                            vueApp.aSelectStyle.push({key:val.sno, text:val.productName});
                                        });
                                        vueApp.issueDetail.styleSno = '0';
                                    });
                                });
                            }
                        },
                        save : ()=>{
                            if (vueApp.issueDetail.issueSubject === null || vueApp.issueDetail.issueSubject === '') {
                                $.msg('이슈제목을 입력하세요','','error');
                                return false;
                            }
                            if (vueApp.issueDetail.projectSno === null || vueApp.issueDetail.projectSno == '0') {
                                $.msg('프로젝트를 선택하세요','','error');
                                return false;
                            }
                            if (vueApp.issueDetail.issueTypeText === null || vueApp.issueDetail.issueTypeText === '') {
                                if (vueApp.issueDetail.issueType == 5) $.msg('기타유형을 입력하세요','','error');
                                else $.msg('유형을 선택하세요','','error');
                                return false;
                            }
                            if (vueApp.issueDetail.issueContents === null || vueApp.issueDetail.issueContents === '') {
                                $.msg('내용을 입력하세요','','error');
                                return false;
                            }

                            $.imsPost('setProjectIssue', {'data':vueApp.issueDetail}).then((data)=>{
                                $.imsPostAfter(data,(data)=>{
                                    $.msg('저장이 완료되었습니다.','','success').then(()=>{
                                        if (Number(oSendParam.customerSnoGet) > 0 && Number(oSendParam.projectSnoGet) > 0) {
                                            if (Number(styleSnoGet) === 0) location.href = vueApp.sRedirectUrl + data + vueApp.sRedirectUrlParam1;
                                            else location.href = vueApp.sRedirectUrl + data + vueApp.sRedirectUrlParam2;
                                        } else {
                                            parent.opener.refreshList();
                                            location.href = vueApp.sRedirectUrl + data;
                                            //페이지이동 안하고 issueDetail.sno값을 변경함으로써 상세페이지로 바꾸기. 상태한글값 등 체크필요이슈가 있어서 페이지이동으로 했음
                                            // vueApp.issueDetail.sno = data;
                                            // vueApp.isModify = false;
                                        }
                                    });
                                });
                            });
                        },
                        changeSt : (iSt)=>{
                            let sSt = iSt == 2 ? '조치중으로' : '종결로';
                            $.msgConfirm(sSt+' 상태를 변경하시겠습니까?','').then((confirmData)=> {
                                if (true === confirmData.isConfirmed) {
                                    vueApp.issueDetail.issueSt = iSt;
                                    vueApp.save();
                                }
                            });
                        },
                        //댓글관련 - vueApp.issueDetail.sno 게시글의 기본키로 변경, 부모창에서 리스트 새로고침 함수 만들기
                        writeReply : function(){
                            if( $.isEmpty(vueApp.replyComment) ) {
                                $.msg('댓글내용을 입력해주세요!','','warning');
                                return false;
                            }
                            $.imsPost('saveComment',{
                                'eachSno' : vueApp.issueDetail.sno,
                                'commentDiv' : vueApp.replayType,
                                'comment' : vueApp.replyComment,
                            }).then((data)=>{
                                if(200 === data.code){
                                    vueApp.replyComment = '';
                                    vueApp.getListReply();
                                    //부모창에서 리스트 새로고침 함수 만들기
                                    parent.opener.refreshList();
                                }else{
                                    $.msg('코멘트 저장 오류.','개발팀 문의', "warning");
                                }
                            });
                        },
                        getListReply : function(){
                            let oParam = { commentDiv : vueApp.replayType, eachSno : vueApp.issueDetail.sno };
                            ImsNkService.getList('reply', oParam).then((data)=>{
                                if(200 === data.code){
                                    vueApp.replyList = data.data;
                                }
                            });
                        },
                        //댓글 수정
                        setReplyModifyToggle : function(replyIndex, flag){
                            vueApp.replyList[replyIndex].commentToggle=flag;
                            vueApp.$forceUpdate();
                        },
                        modifyReply : function(replaySno, replyContents) {
                            if( $.isEmpty(replyContents) ) {
                                $.msg('댓글내용을 입력해주세요!','','warning');
                                return false;
                            }
                            $.imsPost('saveComment',{
                                'sno' : replaySno,
                                'comment' : replyContents,
                            }).then((data)=>{
                                if(200 === data.code){
                                    vueApp.getListReply();
                                }else{
                                    $.msg('코멘트 저장 오류.','개발팀 문의', "warning");
                                }
                            });
                        },
                        //조치내역 삭제
                        deleteIssueAction : function (actionSno) {
                            $.msgConfirm('정말 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                                if( result.isConfirmed ){
                                    ImsNkService.setDelete('ddddd', actionSno).then(()=>{
                                        vueApp.getIssueActionList();
                                    });
                                }
                            });
                        },

                        //첨부파일 관련 - 파일 업로드할때 실행하는 함수(파일 dropdown하거나 '여기에 파일을 올려주세요'버튼 클릭->파일 선택시 바로 실행)
                        uploadAfterActionProjectIssue : (tmpFile, dropzoneId)=>{
                            ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                                vueApp.issueDetail.bFlagAppendFile = true;
                                vueApp.issueDetail.fileList[vueApp.sAppendFileDiv].title = tmpFile.length+'개 파일 업로드';
                                vueApp.issueDetail.fileList[vueApp.sAppendFileDiv].memo = promptValue;
                                vueApp.issueDetail.fileList[vueApp.sAppendFileDiv].files = [];
                                $.each(tmpFile, function(key, val) {
                                    vueApp.issueDetail.fileList[vueApp.sAppendFileDiv].files.push(val);
                                });
                                $.msg('저장을 클릭하셔야 첨부파일이 반영됩니다.','','success');
                            });
                        },
                        //첨부파일 관련 - 이미지파일이면 true, 아니면 false
                        checkImageExtension : (sFileNm)=>{
                            const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/i;
                            return allowedExtensions.exec(sFileNm);
                        },
                        //첨부파일 관련 - 파일url 배열화 -> 다수파일 show modal 에 쓰임
                        revertReferFilePath : (aTarget)=>{
                            vueApp.referFilePaths = [];
                            if (aTarget.length > 0) {
                                $.each(aTarget, function (key, val) {
                                    vueApp.referFilePaths.push('<?=$nasUrl?>'+val.filePath);
                                });
                            }
                        },

                    },
                    watch : {
                        'issueDetail.customerSno'(val, pre) {
                            vueApp.refreshProjectSelect(val);
                        },
                        'issueDetail.projectSno'(val, pre) {
                            vueApp.refreshStyleSelect(val);
                        },
                        'issueDetail.issueType'(val, pre) {
                            if (val == '5') vueApp.issueDetail.issueTypeText = '';
                            else vueApp.issueDetail.issueTypeText = $('[name=chooseType]:checked').data('text');
                        },
                    },
                    mounted : ()=>{
                        vueApp.getIssueActionList();
                        //댓글관련 - vueApp.issueDetail.sno 게시글의 기본키로 변경
                        if (vueApp.issueDetail.sno > 0) vueApp.getListReply();

                        //첨부파일 관련 - 첨부파일html에 event 부여
                        $('.set-dropzone').addClass('dropzone');
                        ImsService.setDropzone(vueApp, vueApp.sAppendFileDiv, vueApp.uploadAfterActionProjectIssue);
                        vueApp.revertReferFilePath(vueApp.issueDetail.fileList[vueApp.sAppendFileDiv].files);

                        //프로젝트상세페이지에서 open한 경우 하단에 해당 프로젝트/스타일의 이슈리스트 출력
                        if (Number(oSendParam.customerSnoGet) > 0 && Number(oSendParam.projectSnoGet) > 0) { //oSendParam에는 값을 집어넣을 일이 없음
                            let oListParam = {};
                            if (Number(styleSnoGet) === 0) { //styleSnoGet에는 값을 집어넣을 일이 없음
                                oListParam.listProjectSno = oSendParam.projectSnoGet;
                            } else {
                                oListParam.listStyleSno = styleSnoGet;
                            }
                            ImsNkService.getList('projectIssue', oListParam).then((data)=> {
                                $.imsPostAfter(data, (data) => {
                                    vueApp.aoIssueFldList = data.fieldData;
                                    vueApp.aoIssueRowList = data.list;
                                });
                            });
                        }
                    },
                }
                vueApp = ImsService.initVueApp(appId, initParams);
            });
        });
    });
</script>

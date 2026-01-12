<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
        <div class="new-style mgt30" >
            <div class="table-title gd-help-manual">
                <div class="flo-left area-title">
                    <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                    고객 코멘트 ( {% mainData.customerName %} )
                </div>
                <div class="flo-right">
                    <div class="btn btn-sm btn-white mgb5" v-if="!$.isEmpty(mainData.sno)"
                         @click="ImsService.deleteData('customerIssue',mainData.sno, ()=>{parent.opener.location.reload(); self.close();})">삭제</div>
                </div>
            </div>

            <!--보기모드-->
            <table class="table table-cols" v-show="'v' === viewMode">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody >

                <tr v-show="'req'===mainData.issueType">
                    <th>코멘트 타입</th>
                    <td class="font-14">
                        <div v-html="mainData.issueTypeKr"></div>
                    </td>
                    <th>접수 방법</th>
                    <td class="font-14">
                        <div v-html="mainData.inboundTypeKr"></div>
                    </td>
                </tr>
                <tr v-show="'req'!==mainData.issueType">
                    <th>코멘트 타입</th>
                    <td class="font-14" colspan="99">
                        <div v-html="mainData.issueTypeKr"></div>
                    </td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td colspan="99">
                        <div class="" v-html="mainData.subject"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="pd20">
                        <div v-html="mainData.contents"></div>
                    </td>
                </tr>
                <tr>
                    <th>댓글</th>
                    <td colspan="3">
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
                                            <textarea class="form-control " v-model="val.comment" rows="4"></textarea>
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
                                        <button class="btn btn-white btn-sm " title="확인" @click="ImsService.deleteData('projectComment', val.sno, ()=>{ vueApp.getListReply(); })">삭제</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?=$managerInfo['managerNm']?>(<?=$managerInfo['managerId']?>)
                                </td>
                                <td colspan="2">
                                    <textarea style="width:100%;" class="form-control " name="memo" required="" v-model="replyComment" placeholder="댓글" rows="4"></textarea>
                                    <button type="button" class="btn btn-red btn-red-line2 mgt5" @click="writeReply()">댓글등록</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>첨부파일</th>
                    <td colspan="99">
                        <simple-file-only-not-history-upload :file="mainData.fileData" :id="'fileDataView'"></simple-file-only-not-history-upload>
                    </td>
                </tr>
                </tbody>
            </table>

            <!--수정모드-->
            <table class="table table-cols" v-show="'m' === viewMode">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody >
                <tr v-show="'req'===mainData.issueType">
                    <th>코멘트 타입</th>
                    <td class="font-14">
                        <select class="form-control font-14" v-model="mainData.issueType">
                            <option value="">미정</option>
                            <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $key => $value){ ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>}
                        </select>
                    </td>
                    <th>접수 방법</th>
                    <td class="font-14">
                        <select class="form-control font-14" v-model="mainData.inboundType">
                            <?php foreach(\Component\Ims\ImsCodeMap::INBOUND_TYPE as $key => $value){ ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>}
                        </select>
                    </td>
                </tr>
                <tr v-show="'req'!==mainData.issueType">
                    <th>코멘트 타입</th>
                    <td class="font-14" colspan="99">
                        <select class="form-control font-14" v-model="mainData.issueType">
                            <option value="">미정</option>
                            <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $key => $value){ ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>}
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td colspan="99">
                        <input type="text" class="form-control" v-model="mainData.subject">
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="pd5">
                        <textarea class="form-control" rows="27" v-model="mainData.contents" id="editor"></textarea>
                    </td>
                </tr>
                <tr>
                    <th>첨부파일</th>
                    <td colspan="99">
                        <simple-file-not-history-upload :file="mainData.fileData" :id="'fileData'"></simple-file-not-history-upload>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>


        <div class="text-center" v-show="'m' === viewMode && $.isEmpty(mainData.sno) ">
            <div class="btn btn-lg btn-red" @click="saveData('imsCustomerIssue', mainData, saveAfterFnc, saveBeforeFnc)">저장</div>
            <div class="btn btn-lg btn-white" @click="self.close()" >닫기</div>
        </div>

        <div class="text-center" v-show="'m' === viewMode && !$.isEmpty(mainData.sno)">
            <div class="btn btn-lg btn-red" @click="saveData('imsCustomerIssue', mainData, saveAfterFnc, saveBeforeFnc)">저장</div>
            <div class="btn btn-lg btn-white" @click="setViewMode('v')" >취소</div>
        </div>

        <div class="text-center" v-show="'v' === viewMode">
            <div class="btn btn-lg btn-red btn-red-line2" @click="setViewMode('m')">수정</div>
            <div class="btn btn-lg btn-white" @click="self.close()" >닫기</div>
        </div>
</section>

<script type="text/javascript">

    $(appId).hide();
    const sno = '<?=$requestParam['sno']?>';

    $(()=>{
        const serviceData = {
            serviceValue : {
                viewMode : 'v', //이 화면에서 사용하는 변수 ( 화면모드 : m 수정 , v 보기모드 )
                editorSet : false,
                replyComment : '',
                replyList : [],
                replayType:'custCommentReply',
                commentSno : 0,
            },serviceMounted : (vueInstance)=>{
                //신규 등록
                if( $.isEmpty(vueInstance.mainData.sno) ){
                    vueInstance.mainData.customerSno = '<?=$requestParam['customerSno']?>';
                    vueInstance.mainData.projectSno = '<?=$requestParam['projectSno']?>';
                    vueInstance.mainData.issueType = '<?=$requestParam['issueType']?>';
                    vueInstance.mainData.inboundType = '';
                    vueInstance.viewMode = 'm';
                    ImsBoneService.setEditor('editor');
                    vueApp.editorSet = true;
                } else { //고객코멘트 수정인 경우에만 댓글작성 가능, 댓글리스트 가져오기
                    vueApp.commentSno = vueInstance.mainData.sno;
                    vueApp.getListReply();
                }

                //Dropzone
                $('.set-dropzone').addClass('dropzone');
                ImsService.setDropzone(vueInstance, 'fileData', (tmpFile, dropzoneId)=>{
                    vueInstance.mainData.fileData.memo = '<span class="text-danger font-11">저장되지 않음 (반드시 저장해주세요)</span>';
                    vueInstance.mainData.fileData.files = tmpFile;
                }); //첨부 등록.

                ImsService.setDropzone(vueInstance, 'fileDataView'); //첨부 보기.

            },serviceMethods : {
                setViewMode : (viewMode)=>{
                    console.log(viewMode);
                    vueApp.viewMode = viewMode;
                    if( false === vueApp.editorSet && 'm' === vueApp.viewMode ){
                        //Editor
                        ImsBoneService.setEditor('editor');
                        vueApp.editorSet = true;
                    }
                },
                writeReply : function(){
                    if(vueApp.commentSno == 0) {
                        $.msg('고객 코멘트 수정페이지에서만 댓글작성이 가능합니다.','','warning');
                        return false;
                    }
                    if( $.isEmpty(vueApp.replyComment) ) {
                        $.msg('댓글내용을 입력해주세요!','','warning');
                        return false;
                    }
                    $.imsPost('saveComment',{
                        'eachSno' : vueApp.commentSno,
                        'commentDiv' : vueApp.replayType,
                        'comment' : vueApp.replyComment,
                    }).then((data)=>{
                        if(200 === data.code){
                            vueApp.replyComment = '';
                            vueApp.getListReply();
                            parent.opener.location.reload();
                        }else{
                            $.msg('코멘트 저장 오류.','개발팀 문의', "warning");
                        }
                    });
                },
                getListReply : function(){
                    let oParam = { commentDiv : vueApp.replayType, eachSno : vueApp.commentSno };
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
            }
        }
        ImsBoneService.serviceBegin(DATA_MAP.CUST_ISSUE,{sno:sno},serviceData);
    });

    const saveBeforeFnc = ()=>{
        //고객 요청시 접수 방법 필수.
        if( 'req' === vueApp.mainData.issueType && $.isEmpty(vueApp.mainData.inboundType) ){
            $.msg('고객 요청 등록시 접수 방법은 필수 입니다.', '', 'warning');
            return false;
        }else{
            vueApp.mainData.fileData.memo = '';
            oEditors.getById["editor"].exec("UPDATE_CONTENTS_FIELD", []);
            vueApp.mainData.contents = $('#editor').val();
            return true;
        }
    }

    const saveAfterFnc = (data)=>{
        if(!$.isEmpty(sno) && sno > 0 ){
            ImsService.getDataParams(DATA_MAP.CUST_ISSUE,{sno:data}).then((commentData)=>{
                vueApp.mainData = commentData.data; //수정일 때는 다시 갱신.
                vueApp.mainData.fileData.memo = '';
                vueApp.viewMode = 'v';
                //$('#editor').val('');
                //oEditors.getById["editor"].exec("LOAD_CONTENTS_FIELD", []);
            });
        }else{
            self.close();
        }
    }

</script>


<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>
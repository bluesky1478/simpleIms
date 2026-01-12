<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_nk.php'?>

<?php include './admin/ims/library_nk_file_multi_view_modal.php'?>
<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">조치내역 {% sTextUpsertMode %}</h3>
        <div class="btn-group font-18 bold">
        </div>
    </div>
    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col style="width:20%;">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <td colspan="2">
                        <label class="mgr10">
                            <input type="checkbox" class="checkbox-inline chk-progress" v-model="issueActionDetail.chkDirectComplete" /> <span>즉시 종결 처리</span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>처리사항</th>
                    <td>
                        <div>
                            <textarea class="form-control" rows="3" v-model="issueActionDetail.actionContents" placeholder="내용"></textarea>

                            <!--첨부파일 관련 - html 정의-->
                            <file-upload2 :file="issueActionDetail.fileList[sAppendFileDiv2]" :id="sAppendFileDiv2" :params="issueActionDetail" :accept="false"></file-upload2>
                        </div>
                        <div>
                            <img v-if="checkImageExtension(val.fileName)" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in issueActionDetail.fileList[sAppendFileDiv2].files" style="width:150px;" @click="revertReferFilePath(); openMultiFileView(referFilePaths);" class="cursor-pointer hover-btn" />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">저장</div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(()=>{
        var oSendParam = { issueActionSnoGet:'<?=(int)$requestParam['sno']?>', };
        var sIssueSnoGet = '<?=(int)$requestParam['issueSno']?>';
        ImsNkService.getList('projectIssueAction', oSendParam).then((data)=>{
            $.imsPostAfter(data, (data)=> {
                let issueActionDetail = data.info;
                if (sIssueSnoGet != 0) issueActionDetail.issueSno = sIssueSnoGet;
                issueActionDetail.chkDirectComplete = '';

                const initParams = {
                    data: {
                        issueActionDetail: issueActionDetail,
                        sTextUpsertMode: oSendParam.issueActionSnoGet == '0' ? '등록' : '수정',
                        //첨부파일 관련 - fileDiv값 정의
                        sAppendFileDiv2:'projectIssueActionFile1',
                        referFilePaths:[],
                    },
                    methods : {
                        save : ()=>{
                            if (vueApp.issueActionDetail.actionContents === null || vueApp.issueActionDetail.actionContents === '') {
                                $.msg('처리사항을 입력하세요','','error');
                                return false;
                            }

                            $.imsPost('setProjectIssueAction', {'data':vueApp.issueActionDetail}).then((data)=>{
                                $.imsPostAfter(data,(data)=>{
                                    $.msg('저장이 완료되었습니다.','','success').then(()=>{
                                        parent.opener.refreshActionList(data);
                                        self.close();
                                    });
                                });
                            });
                        },
                        //첨부파일 관련 - 파일 업로드할때 실행하는 함수(파일 dropdown하거나 '여기에 파일을 올려주세요'버튼 클릭->파일 선택시 바로 실행)
                        uploadAfterActionProjectIssueAction : (tmpFile, dropzoneId)=>{
                            ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                                vueApp.issueActionDetail.bFlagAppendFile = true;
                                vueApp.issueActionDetail.fileList[vueApp.sAppendFileDiv2].title = tmpFile.length+'개 파일 업로드';
                                vueApp.issueActionDetail.fileList[vueApp.sAppendFileDiv2].memo = promptValue;
                                vueApp.issueActionDetail.fileList[vueApp.sAppendFileDiv2].files = [];
                                $.each(tmpFile, function(key, val) {
                                    vueApp.issueActionDetail.fileList[vueApp.sAppendFileDiv2].files.push(val);
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
                        revertReferFilePath : ()=>{
                            vueApp.referFilePaths = [];
                            if (vueApp.issueActionDetail.fileList[vueApp.sAppendFileDiv2].files.length > 0) {
                                $.each(vueApp.issueActionDetail.fileList[vueApp.sAppendFileDiv2].files, function (key, val) {
                                    vueApp.referFilePaths.push('<?=$nasUrl?>'+val.filePath);
                                });
                            }
                        },
                    },
                    mounted : ()=>{
                        //첨부파일 관련 - 첨부파일html에 event 부여
                        $('.set-dropzone').addClass('dropzone');
                        ImsService.setDropzone(vueApp, vueApp.sAppendFileDiv2, vueApp.uploadAfterActionProjectIssueAction);
                        vueApp.revertReferFilePath();
                    },
                }
                vueApp = ImsService.initVueApp(appId, initParams);
            });
        });
    });
</script>




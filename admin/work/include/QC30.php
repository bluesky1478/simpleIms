<form name="frmOrder" method="post" class="frm-order" id="documentApp" @submit.stop.prevent="onSubmit(items)"  >
    <?php include 'accept_history.php'; ?>
    <div class="page-header js-affix">
        <h3><?=$title?></h3>
        <div class="btn-group">
            <input type="submit" value="저장" class="btn btn-red btn-reg-control"  @click="officialSave(items)" />
        </div>
    </div>
    <div class="row">
        <?php include 'accept_area.php'?>

        <!--CONTENTS BEGIN-->

        <div class="col-xs-6" >
            <div class="table-title ">
                <div class="flo-left" >첨부 파일</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>첨부파일</th>
                        <td>
                            <div id="my-dropzone1" class="set-dropzone"></div>
                            <div>
                                <ul >
                                    <li v-for="(file, fileIndex) in items.docData.fileDefault" :key="fileIndex">
                                        첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                        <a href="#" @click="removeFile(items.docData.fileDefault, fileIndex)">삭제</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-xs-6" >
            <div class="table-title ">
                <div class="flo-left" >기타 메모</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col/>
                    </colgroup>
                    <tr>
                        <td>
                            <textarea class="form-control" placeholder="기타 사항" rows="6"></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!--CONTENTS END-->

    </div>
    <div class="w100 text-right">
        <input type="submit" value="저장" class="btn btn-lg btn-red btn-reg-control"  style="width:300px; font-size:15px; border-radius: 10px"  @click="officialSave(items)" />
    </div>
</form>

<script type="text/javascript">
    let externMountedFnc = function(){
        setDropzone(0, 'fileDefault');
        dropzoneInstanceList.push(new Dropzone( "div#my-dropzone1" , dropzoneOptionList[0]));
    }
    let externVueMethod = {
        /**
         * 저장
         */
        onSubmit : function (items) {
            $('#layerDim').removeClass('display-none');
            WorkDocument.saveDocument(DOC_DEPT, DOC_TYPE, items, ()=>{ $('#layerDim').addClass('display-none'); });
        },
    };
</script>
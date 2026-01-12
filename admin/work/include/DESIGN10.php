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

        <div class="col-xs-12" >
            <div class="table-title ">
                <div class="flo-left" >디자인 방향</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col/>
                        <col/>
                        <col/>
                        <col/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>번호</th>
                        <th>스타일</th>
                        <th>제안서 타입</th>
                        <th>제안 예상 수량</th>
                        <th>추가설명</th>
                    </tr>
                    <tbody v-for="(item, index) in items.docData.designDirection" :key="index">
                        <tr>
                            <td>{% index+1 %}</td>
                            <td>
                                <input type="text" class="form-control" v-model="item.styleName">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="item.styleType">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="item.recommendCnt">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="item.description">
                            </td>
                        </tr>
                    </tbody>
                </table>
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
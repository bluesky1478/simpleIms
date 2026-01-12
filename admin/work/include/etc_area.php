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
                    <textarea class="form-control" placeholder="기타 사항" rows="6" v-model="items.docData.etcMemo"></textarea>
                </td>
            </tr>
        </table>
    </div>
</div>
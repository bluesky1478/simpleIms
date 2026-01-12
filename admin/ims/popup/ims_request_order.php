<tr>
    <th >가발주 근거 자료</th>
    <td >
        <simple-file-upload :file="fileList.filePreparedOrder" :id="'filePreparedOrder'" :project="project" ></simple-file-upload>
    </td>
    <th >작업지시서 발송 예정일</th>
    <td >
        <date-picker value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" v-model="prepared.contents.workSendDt" placeholder="발송예정일"></date-picker>
    </td>
</tr>

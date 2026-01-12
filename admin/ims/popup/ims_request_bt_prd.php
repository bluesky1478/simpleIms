<tr>
    <th >BT의뢰서</th>
    <td colspan="3">
        <simple-file-only :file="fileList.filePreparedBt" :id="'filePreparedBt'" :project="project" ></simple-file-only>
    </td>
</tr>
<tr>
    <th >BT결과</th>
    <td colspan="3">
        <span class="display-none">[{% prepared.contents.filePreparedBtPrdResult %}]</span>
        <simple-file-upload :file="fileList.filePreparedBtPrdResult" :id="'filePreparedBtPrdResult'" :project="project" ></simple-file-upload>
    </td>
</tr>
<tr>
    <th >발송형태</th>
    <td >
        <input type="text" class="form-control w100" placeholder="발송형태(퀵, 택배 등)" v-model="prepared.contents.sendType">
    </td>
    <th >발송예정일</th>
    <td >
        <date-picker value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" v-model="prepared.contents.sendDt" placeholder="발송예정일"></date-picker>
    </td>
</tr>
<tr>
    <th >발송정보</th>
    <td colspan="3">
        <input type="text" class="form-control w100" placeholder="발송정보" v-model="prepared.contents.sendInfo">
    </td>
</tr>


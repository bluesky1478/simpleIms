<tr>
    <th >BT의뢰서</th>
    <td colspan="3">
        <simple-file-upload :file="fileList.filePreparedBt" :id="'filePreparedBt'" :project="project" ></simple-file-upload>
    </td>
</tr>
<tr>
    <th >BT결과서</th>
    <td colspan="3" >
        <simple-file-only :file="fileList.filePreparedBtPrdResult" :id="'filePreparedBtPrdResult'" :project="project" ></simple-file-only>
    </td>
</tr>
<tr>
    <th >발송형태(생산처입력)</th>
    <td  class="font-16">
        {% prepared.contents.sendType %}
    </td>
    <th >발송예정일(생산처입력)</th>
    <td  class="font-16">
        {% prepared.contents.sendDt %}
    </td>
</tr>
<tr>
    <th >발송정보(생산처입력)</th>
    <td colspan="3" class="font-16">
        {% prepared.contents.sendInfo %}
    </td>
</tr>

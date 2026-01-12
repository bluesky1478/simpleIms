<tr>
    <th >가발주 근거 자료</th>
    <td >
        <simple-file-only :file="fileList.filePreparedOrder" :id="'filePreparedOrder'" :project="project" ></simple-file-only>
    </td>
    <th >작업지시서 발송 예정일</th>
    <td >
        {% prepared.contents.workSendDt %}
    </td>
</tr>


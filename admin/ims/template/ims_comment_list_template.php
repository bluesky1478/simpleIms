<table class="table table-rows table-default-center mgt5">
    <colgroup>

    </colgroup>
    <thead>
    <tr>
        <th>
            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="imsCommentSno">
        </th>
        <th>번호</th>
        <th>등록자</th>
        <th>등록일</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for=" (item , itemIndex) in imsCommentList">
        <td>
            <input type="checkbox" name="imsCommentSno[]" class="list-check" :value="item.sno">
        </td>
        <td>
            {% imsCommentList.length - itemIndex %}
        </td>
        <td >
            {% item.regManagerNm %}
            <div class="btn btn-sm btn-white" @click="ImsService.deleteData('newimsComment',item.sno, imsCommentService.getList)">삭제</div>
        </td>
        <td >
            {% $.formatShortDate(item.regDt) %}
            <br><small class="text-muted">{% $.formatShortDate(item.modDt) %}</small>
        </td>
    </tr>
    <tr v-if="0 >= imsCommentList.length">
        <td colspan="99" class="ta-c">데이터 없음</td>
    </tr>
    </tbody>
</table>
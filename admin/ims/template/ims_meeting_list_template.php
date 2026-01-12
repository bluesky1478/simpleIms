<table class="table table-rows table-default-center mgt5">
    <colgroup>
        <col style="width:50px" /><!--체크-->
        <col style="width:50px" /><!--번호-->
        <col style="width:80px" /><!--미팅상태-->
        <col style="width:220px" /><!--고객사-->
        <col  /><!--미팅목적-->
        <col style="width:250px" /><!--참석자-->
        <col style="width:150px" /><!--미팅일자/시간-->
        <col style="width:250px" v-if="!customerSummary"/><!--미팅장소-->
        <col style="width:100px" v-if="!customerSummary"  /><!--미팅준비D/L-->
        <col style="width:100px" v-if="!customerSummary"  /><!--등록-->
        <col style="width:100px" /><!--등록일자-->
    </colgroup>
    <thead>
    <tr>
        <th>
            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="meetingSno">
        </th>
        <th>번호</th>
        <th>미팅상태</th>
        <th>고객사</th>
        <th>미팅목적</th>
        <th>참석자</th>
        <th>미팅일자/시간</th>
        <th v-if="!customerSummary">미팅장소</th>
        <th v-if="!customerSummary">미팅준비D/L</th>
        <th v-if="!customerSummary">등록</th>
        <th>등록일</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for=" (item , itemIndex) in meetingList">
        <td>
            <input type="checkbox" name="meetingSno[]" class="list-check" :value="item.sno">
        </td>
        <td>
            {% meetingList.length - itemIndex %}
        </td>
        <td class="ta-c">
            {%  item.meetingStatusKr %}
        </td>
        <td class="ta-l pdl10">
            <span class="ims-customer-name hover-btn btn-pop-customer-info" :data-sno="item.customerSno" @click="openCustomer(item.customerSno)" v-if="!customerSummary">
                {% item.customerName %}
            </span>
            <span class="" v-if="customerSummary">
                {% item.customerName %}
            </span>
        </td>
        <td class="ta-l pdl10">
            <span class="cursor-pointer hover-btn" @click="openMeetingView(item.sno,0)">{% item.purpose %}</span>
            <div class="btn btn-sm btn-white" @click="openMeetingView(item.sno,0)">보기</div>
        </td>
        <td class="ta-l pdl10">
            {% item.attend %}
        </td>
        <td >
            {% $.formatShortDate(item.meetingDt) %} {% item.meetingTime %}
        </td>
        <td class="ta-l pdl10" v-if="!customerSummary">
            {% item.location %}
        </td>
        <td  v-if="!customerSummary">
            {% $.formatShortDate(item.readyDeadLineDt) %}
        </td>
        <td  v-if="!customerSummary">
            {% item.regManagerNm %}
            <div class="btn btn-sm btn-white" @click="ImsService.deleteData('newMeeting',item.sno, MeetingService.getList)">삭제</div>
        </td>
        <td >
            {% $.formatShortDate(item.regDt) %}
            <br><small class="text-muted">{% $.formatShortDate(item.modDt) %}</small>
        </td>
    </tr>
    <tr v-if="0 >= meetingList.length">
        <td colspan="99" class="ta-c">데이터 없음</td>
    </tr>
    </tbody>
</table>
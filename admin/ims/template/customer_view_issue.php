<div class="col-xs-12 pd15">
    <div class="table-responsive ">
        <div class="table-title gd-help-manual">
            <div class="font-18">프로젝트/스타일 이슈 리스트</div>
        </div>
        <table class="table table-rows">
            <colgroup>
                <col style="width: 5%;" />
                <col :class="`w-${fieldData.col}p`" v-for="fieldData in projectIssueFieldData" v-if="true != fieldData.skip" />
                <col style="width: 5%;" />
            </colgroup>
            <thead>
            <tr>
                <th>번호</th>
                <th v-for="fieldData in projectIssueFieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                    {% fieldData.title %}
                </th>
                <th >상세</th>
            </tr>
            </thead>
            <tbody class="text-center ">
            <tr v-if="0 >= projectIssueList.length">
                <td colspan="99">데이터가 없습니다.</td>
            </tr>
            <tr v-for="(val, key) in projectIssueList">
                <td>{% projectIssueTotal.idx - key %}</td>
                <td v-for="fieldData in projectIssueFieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                    <span v-if="fieldData.type === 'title'" class="sl-blue  cursor-pointer hover-btn" @click="openCommonPopup('project_issue_upsert', 1000, 910, {'sno':val.sno});">
                        {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                        <span v-if="val.cnt_reply > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% val.cnt_reply %}</div></span>
                    </span>
                    <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                    <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                    <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                </td>
                <td><span class="btn btn-sm btn-white hover-btn cursor-pointer" @click="openCommonPopup('project_issue_upsert', 1000, 910, {'sno':val.sno});">상세보기</span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="col-xs-12 pd15">
    <div class="table-responsive ">
        <div class="table-title gd-help-manual">
            <div class="font-18">견적 리스트</div>
        </div>
        <table class="table table-rows">
            <colgroup>
                <col style="width: 5%;" />
                <col style="width: 10%;" />
                <col style="width: 8%;" />
                <col style="width: 5%;" />
                <col style="width: 20%;" />
                <col style="width: 15%;" />
                <col />
                <col />
            </colgroup>
            <thead>
            <tr>
                <th>번호</th>
                <th>견적일</th>
                <th>견적타입</th>
                <th>프로젝트번호</th>
                <th>제목</th>
                <th>총금액</th>
                <th>고객메모</th>
                <th>이노버(내부)메모</th>
            </tr>
            </thead>
            <tbody class="text-center ">
            <tr v-if="0 >= estimateList.length">
                <td colspan="99">데이터가 없습니다.</td>
            </tr>
            <tr v-for="(val, key) in estimateList">
                <td>{% estimateTotal.idx - key %}</td>
                <td>{% val.estimateDt=='0000-00-00'?'-':val.estimateDt %}</td>
                <td>{% val.estimateTypeHan %}</td>
                <td>{% val.projectSno %}</td>
                <td>
                    <div class="hover-btn cursor-pointer sl-blue" @click="window.open(`<?=$customerEstimateUrl?>?key=${val.key}`);">
                        {% val.subject %}
                    </div>
                </td>
                <td>{% $.setNumberFormat(Number(val.supply) + Number(val.tax)) %}</td>
                <td>{% val.estimateMemo %}</td>
                <td>{% val.innoverMemo %}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
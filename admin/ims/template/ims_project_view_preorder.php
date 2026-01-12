<!-- 프로젝트 기본 정보 -->
<div class="col-xs-12" >
    <div class="table-title gd-help-manual">
        <div class="flo-left">원부자재 선행 요청</div>
        <div class="flo-right">
            <div class="btn btn-red-line" @click="openProduceRequest(project.sno, 'order', '')">원부자재 선행 요청</div>
        </div>
    </div>
    <table class="table table-cols">
        <colgroup>
            <col style="width:10%" />
            <col style="width:12%" />
            <col style="width:15%" />
            <col style="width:12%" />
            <col  />
        </colgroup>
        <tbody>
        <tr>
            <th>의뢰일자</th>
            <th>의뢰업체</th>
            <th>현재상태</th>
            <th>완료요청일(D/L)</th>
            <th>요청메모</th>
        </tr>
        <tr v-if="0 >= preparedList.order.length">
            <td colspan="99" class="text-center">의뢰 이력 없음</td>
        </tr>
        </tbody>
        <tbody v-for="preparedData in preparedList.order" v-if="preparedList.order.length > 0">
        <tr >
            <td rowspan="2">{% preparedData.regDtShort %}</td>
            <td rowspan="2">
                {% preparedData.produceCompany %}
                <div class="btn btn-sm btn-white" @click="openProduceRequest(project.sno, 'order', preparedData.sno)">상세</div>
            </td>
            <td rowspan="2">
                <div class="font-16" v-html="preparedData.preparedStatusKr"></div>
            </td>
            <td>{% preparedData.deadLineDtShort %} <div v-html="preparedData.deadLineDtRemain"></div></td>
            <td class="text-left" v-html="preparedData.reqMemo"></td>
        </tr>
        <tr v-show="4 == preparedData.preparedStatus || 5 == preparedData.preparedStatus">
            <td colspan="2" class="text-left">
                <div v-html="preparedData.preparedStatusKr"></div> 사유/메모 : {% preparedData.acceptMemo %} {% preparedData.acceptManager %} ({% preparedData.acceptDt %})
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="col-xs-12 pd15">
    <div class="table-responsive">
        <div class="table-title gd-help-manual">
            <div class="font-18">비축 원부자재 리스트</div>
        </div>

        <table class="table table-rows">
            <colgroup>
                <col style="width: 17%;"><!-- 자재명 -->
                <col style="width: 2%;"><!-- 입고번호 -->
                <col style="width: 6%;"><!-- 입고일 -->
                <col style="width: 5%;"><!-- 단가 -->
                <col style="width: 5%;"><!-- 입고수량 -->
                <col style="width: 3%;"><!-- 단위 -->
                <col style="width: 7%;"><!-- 금액 -->
                <col style="width: 5%;"><!-- 잔여수량 -->
                <col style="width: 6%;"><!-- 만료일자-->
                <col style="width: 10%;"><!-- 저장위치 -->
                <col style=""><!-- 입고사유 -->
                <col style="width: 6%;"><!-- 소유구분 -->
                <col style="width: 5%;"><!-- 출고등록 -->
            </colgroup>
            <thead>
            <tr>
                <th>자재명</th>
                <th>입고번호</th>
                <th>입고일</th>
                <th>단가</th>
                <th>입고수량</th>
                <th>단위</th>
                <th>금액</th>
                <th>잔여수량</th>
                <th>만료일자</th>
                <th>저장위치</th>
                <th>입고사유</th>
                <th>소유구분</th>
                <th>출고등록</th>
            </tr>
            </thead>
            <tbody class="text-center ">
            <tr v-if="0 >= storedList.length">
                <td colspan="99" class="ta-c">데이터가 없습니다.</td>
            </tr>
            <tr v-for="(val, key) in storedList">
                <td>{% val.fabricName %}({% val.fabricMix %} / {% val.color %})</td>
                <td>{% key + 1 %}</td>
                <td>{% val.inputDt=='0000-00-00'?'-':val.inputDt %}</td>
                <td>{% $.setNumberFormat(val.unitPrice) %}</td>
                <td>{% $.setNumberFormat(val.inputQty) %}</td>
                <td>{% val.inputUnit %}</td>
                <td>{% $.setNumberFormat(val.inputQty*val.unitPrice) %}</td>
                <td><span class='font-11 sl-blue hover-btn cursor-pointer' @click="openCommonPopup('stored_output_list', 860, 710, {'sno':val.sno});">{% $.setNumberFormat(val.inputQty - (val.outQty==null?0:val.outQty)) %}</span></td>
                <td>{% val.expireDt=='0000-00-00'?'-':val.expireDt %}</td>
                <td>{% val.inputLocation %}</td>
                <td>{% val.inputReason %}</td>
                <td>{% val.inputOwn %}</td>
                <td><span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="openCommonPopup('stored_output_reg', 500, 410, {'sno':val.sno});">출고등록</span></td>
            </tr>
            </tbody>
        </table>
    </div>

</div>
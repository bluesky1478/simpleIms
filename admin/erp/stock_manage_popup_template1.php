<td class="font-11" :rowspan="0 == item['3pl'].length?1:item['3pl'].length">
    <!--코드 연결 옵션 선택-->
    <input type="radio" name="linkGoods" :value="item.sno" v-model="linkGoods"  />
</td>
<td class="ta-l" :rowspan="0 == item['3pl'].length?1:item['3pl'].length" style="padding-left:5px!important;">
    <!--옵션번호-->
    <span class="font-11 ">{% item.sno %}</span>
</td>
<td class="ta-l" :rowspan="0 == item['3pl'].length?1:item['3pl'].length" style="padding-left:5px!important;">
    <!--옵션명-->
    <span v-for="n in 5">
        {% item['optionValue'+(n)] %}
    </span>

    <i class="fa fa-plus sl-blue cursor-pointer hover-btn" aria-hidden="true" v-show="isModify" @click="add(itemIndex,0)"></i>
    <!--<i class="fa fa-plus sl-blue cursor-pointer hover-btn" aria-hidden="true" v-show="isModify" @click="add(itemIndex,0)" v-if="0 === item['3pl'].length"></i>-->
</td>
<td :rowspan="0 == item['3pl'].length?1:item['3pl'].length">
    <!--판매수량-->
    <div v-if="!isModify">
        <div v-if="item.stockCnt > item.realCnt-item.reserveCnt" class="font-13 bold">
            {% $.setNumberFormat(item.stockCnt) %}
            <br><span class="text-danger font-normal">{% item.realCnt-item.reserveCnt - item.stockCnt %}부족</span>
        </div>
        <div v-else-if="item.realCnt-item.reserveCnt > item.stockCnt" class="font-13 bold">
            {% $.setNumberFormat(item.stockCnt) %}
            <br><span class="font-11 font-normal sl-blue">{% item.realCnt-item.reserveCnt - item.stockCnt %}가능</span>
        </div>
        <div v-else class="font-13 bold">
            {% $.setNumberFormat(item.stockCnt) %}
        </div>
    </div>
    <div v-else>
        <input type="text" class="form-control" v-model="item.stockCnt">
    </div>
</td>
<td :rowspan="0 == item['3pl'].length?1:item['3pl'].length">
    <!--예약수량-->
    <span class="cursor-pointer hover-btn text-danger" v-if="item.reserveCnt > 0" @click="openReserved(item.goodsNo, 'o'+item.sno)">
        {% item.reserveCnt %}
    </span>
    <span class="text-muted" v-else>-</span>
</td>
<td :rowspan="0 == item['3pl'].length?1:item['3pl'].length">
    <!--창고수량-->
    {% $.setNumberFormat(item.realCnt) %}
</td>
<td :rowspan="0 == item['3pl'].length?1:item['3pl'].length">
    <!--총입고수량-->
    {%  $.setNumberFormat(item.inCnt) %}
</td>
<td :rowspan="0 == item['3pl'].length?1:item['3pl'].length">
    <!--총출고수량-->
    {%  $.setNumberFormat(item.outCnt) %}
</td>
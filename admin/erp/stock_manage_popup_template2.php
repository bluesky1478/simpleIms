<td class="font-11">
    <div v-show="!isModify">{% tp.sort %}</div>
    <input type="text" class="form-control" v-model="tp.sort" v-show="isModify">
</td>
<td class="ta-l font-11" style="padding-left:5px!important;">
    <div v-if="!isModify">
        <span class="text-muted">{% tp.sno %} </span>{% tp.code %}
    </div>
    <div v-if="isModify" class="dp-flex">
        <input type="text" class="form-control font-11 w-90p" placeholder="연결코드 입력" v-model="tp.code">
        <i class="fa fa-minus text-danger  cursor-pointer hover-btn" aria-hidden="true" v-show="isModify" @click="unlink(itemIndex,tpIndex,tp.sno)"></i>
    </div>

    <input typ="text" class="form-control w20p" placeholder="입고수량" v-show="isAddCnt" v-model="tp.inputHisCnt">

</td>
<td class="" >
    <div v-if='tp.otherCnt > 0'>{% tp.stockCnt-tp.otherCnt %}</div>
    <div v-else>{% tp.stockCnt %}</div>
</td>
<td class="ta-r">
    <div v-show="!isModify">
        <span class="text-muted font-11" v-show="tp.otherCnt > 0">
            {% tp.otherCnt %}<span class="font-10">/{% tp.stockCnt %}</span>
        </span>
        <span class="text-muted font-11" v-show="0 >= tp.otherCnt">
            -
        </span>
    </div>
    <input type="text" class="form-control" v-model="tp.otherCnt" v-show="isModify">
</td>
<td class="font-11">
    {% tp['attr1'] %}<!--분류-->
</td>
<td class="font-11">
    {% tp['attr2'] %}<!--시즌-->
</td>
<td class="font-11">
    {% tp['attr5'] %}<!--연도-->
</td>
<td class="ta-r">
    <span class="cursor-pointer hover-btn" @click="searchCodeHistory(1, tp.code)">{% $.setNumberFormat(tp.inCnt) %}</span>
</td>
<td  class="ta-r">
    <span class="cursor-pointer hover-btn" @click="searchCodeHistory(2, tp.code)">{% $.setNumberFormat(tp.outCnt) %}</span>
</td>
<td  class="font-10  ta-l">
    {% tp.productName %} {% tp.optionName %}
</td>
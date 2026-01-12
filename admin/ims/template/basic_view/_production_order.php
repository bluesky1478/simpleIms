<div v-if="!isModify || $.isEmpty(listUpdateMulti) || listUpdateMulti[index]['exProductionOrder'] === undefined">
    <!--완료일-->
    <div v-if="'0000-00-00' != each.cpProductionOrder && !$.isEmpty(each.cpProductionOrder)" class="text-muted">
    <span class="font-14 sl-green">
        {% $.formatShortDateWithoutWeek(each.cpProductionOrder) %} 발주
    </span>
    </div>
    <!--대체텍스트-->
    <div v-else-if="!$.isEmpty(each.txProductionOrder)">
    <span class="font-11">
        {% each.txProductionOrder %}
    </span>
    </div>
    <!--예정일-->
    <div v-else-if="!$.isEmpty(each.exProductionOrder)" class="">
    <span class="font-14">
        {% $.formatShortDateWithoutWeek(each.exProductionOrder) %}
    </span>
        <div class="font-11 mgt5" v-html="$.remainDate(each.exProductionOrder,true)"></div>
    </div>
    <!--미설정-->
    <div v-else class="text-muted">미정</div>
</div>
<div v-else style="max-width:100px;">
    <div v-if="'0000-00-00' != each.cpProductionOrder && !$.isEmpty(each.cpProductionOrder)" class="text-muted">
        <span class="font-14 sl-green">{% $.formatShortDateWithoutWeek(each.cpProductionOrder) %} 발주</span>
    </div>
    <div v-else-if="!$.isEmpty(each.txProductionOrder)">
        <span class="font-11">{% each.txProductionOrder %}</span>
    </div>
    <div v-else>
        <date-picker v-model="listUpdateMulti[index]['exProductionOrder']" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주예정일"></date-picker>
    </div>
</div>
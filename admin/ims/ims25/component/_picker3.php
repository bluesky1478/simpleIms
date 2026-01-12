<div v-show="!isModify">
    <div v-if="$.isEmpty(<?=$model?>)">
        <span class="font-11 text-muted">미정</span>
    </div>
    <div v-if="!$.isEmpty(<?=$model?>)">
        {% $.formatShortDate(<?=$model?>) %}
    </div>
</div>
<div v-show="isModify" class="">
    <date-picker v-model="<?=$model?>" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
</div>
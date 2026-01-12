<div v-show="!isModify">
    {% <?=$radioKey?> %}
</div>

<div v-show="isModify">
    <div class="" >
        <label class="radio-inline " >
            <input type="radio" :name="'<?=$modelPrefix.$radioKey?>'"  :value="''" v-model="<?=$radioKey?>"  />
            <span class="font-12">미확인</span>
        </label>
        <label class="radio-inline " v-for="eachValue in [<?=$listData?>]">
            <input type="radio" :name="'<?=$modelPrefix.$radioKey?>'"  :value="eachValue" v-model="<?=$radioKey?>"  />
            <span class="font-12">{%eachValue%}</span>
        </label>
    </div>
</div>
<div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>">
    {% getCodeMap()['<?=$listCode?>'][<?=$radioKey?>] %}
    <span v-show="'etc' === <?=$radioKey?>">({% <?=$textKey?> %})</span>
</div>

<div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
    <div class="" >
        <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['<?=$listCode?>']">
            <input type="radio" :name="'<?=$modelPrefix.$radioKey?>'"  :value="eachKey" v-model="<?=$radioKey?>"  />
            <span class="font-12">{%eachValue%}</span>
        </label>
    </div>
    <div class="mgt5 dp-flex" v-show="'etc' === <?=$radioKey?>">
        <input type="text" class="form-control mgt5" v-model="<?=$textKey?>" placeholder="<?=$placeHolder?>">
    </div>
</div>
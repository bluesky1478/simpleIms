<div class="" >
    <div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
        <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listCode?>']" >
            <input type="radio" :name="<?=$listIndexData?>'<?=$modelPrefix.$model?>'"  :value="eachKey" v-model="<?=$model?>" style="margin:0!important;" />
            <span class="font-12">{%eachValue%}</span>
        </label>
    </div>

    <div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
        <div v-if="!$.isEmpty2(<?=$model?>)" >
            {% getCodeMap()['<?=$listCode?>'][<?=$model?>] %}
        </div>
        <div class="text-muted" v-else>
            {% getCodeMap()['<?=$listCode?>'][<?=$model?>] %}
        </div>
    </div>
</div>
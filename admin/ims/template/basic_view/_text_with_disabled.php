<div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
    <input type="text" class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>" :disabled="<?=empty($disabledKey)?'isDisabled':$disabledKey?>">
</div>
<div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
    <div v-if="!$.isEmpty(<?=$model?>)">
        {% <?=$model?> %}
    </div>
    <div v-else class="text-muted">
        미확인
    </div>
</div>

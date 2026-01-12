<div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
    <textarea class="form-control" rows="<?=empty($textareaRows)?6:$textareaRows?>" v-model="<?=$model?>" placeholder="<?=$placeholder?>"></textarea>
</div>
<div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
    <div v-if="!$.isEmpty(<?=$model?>)">
        {% <?=$model?> %}
    </div>
    <div v-else class="text-muted">
        미확인
    </div>
</div>

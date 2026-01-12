<div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
    <input type="number" class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>">
</div>
<div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
    {% $.setNumberFormat(<?=$model?>) %}<?=$suffixText?>
</div>

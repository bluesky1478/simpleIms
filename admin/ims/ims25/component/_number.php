<div v-show="<?=gd_isset($modifyKey,'isModify')?>">
    <input type="number" class="form-control pd2" v-model="<?=$model?>" :placeholder="<?=$placeholder?>">
</div>
<div v-show="!<?=gd_isset($modifyKey,'isModify')?>" >
    {% $.setNumberFormat(<?=$model?>) %}<?=$suffixText?>
</div>

<div v-show="<?=gd_isset($modifyKey,'isModify')?>">
    <input type="text" class="form-control pd2" v-model="<?=$model?>" :placeholder="<?=$placeholder?>">
</div>
<div v-show="!<?=gd_isset($modifyKey,'isModify')?>">
    <div v-if="!$.isEmpty(<?=$model?>)">
        {% <?=$model?> %}
    </div>
    <div v-else>
        <span class="font-11 text-muted"><?=gd_isset($defaultText,'-')?></span>
    </div>
</div>

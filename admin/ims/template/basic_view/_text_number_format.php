<div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
    <input type="text" class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/\B(?=(\d{3})+(?!\d))/g, ',');">
</div>
<div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
    <div v-if="!$.isEmpty(<?=$model?>)">
        {% <?=$model?> %}
    </div>
    <div v-else class="text-muted">
        미확인
    </div>
</div>

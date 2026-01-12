<div class="" >
    <div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
        <select class="form-control" v-model="<?=$model?>"  style="width:<?=$selectWidth?>%" >
            <?php foreach ($listData as $key => $value ) { ?>
                <option value="<?=$key?>"><?=$value?></option>
            <?php } ?>
        </select>
    </div>
    <div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
        {% <?=$modelValue?> %}
    </div>
</div>
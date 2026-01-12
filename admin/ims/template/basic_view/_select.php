<div class="" >
    <div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
        <select2 class="js-example-basic-single" v-model="<?=$model?>"  style="width:<?=$selectWidth?>%" >
            <option value="<?=$defaultValue[0]?>"><?=$defaultValue[1]?></option>
            <?php foreach ($listData as $key => $value ) { ?>
                <option value="<?=$key?>"><?=$value?></option>
            <?php } ?>
        </select2>
    </div>

    <div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
        {% <?=$modelValue?> %}
    </div>
</div>
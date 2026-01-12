<?php $title = '고객 성향'?>
<?php include 'basic_type_head.php'?>
<tbody>
<tr >
    <th >색상</th>
    <td >
        <?php $key1 = 'info009'; $listType = 'ratingType'?>
        <div class="" >
            <div v-show="isModify">
                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                    <input type="radio" :name="'project-added-info-<?=$key1?>'"  :value="eachKey" v-model="project.addedInfo.<?=$key1?>" />
                    <span class="font-12">{%eachValue%}</span>
                </label>
            </div>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listType?>'][project.addedInfo.<?=$key1?>] %}
            </div>
        </div>
    </td>
    <th >품질</th>
    <td >
        <?php $key1 = 'info010'; $listType = 'ratingType'?>
        <div class="" >
            <div v-show="isModify">
                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                    <input type="radio" :name="'project-added-info-<?=$key1?>'"  :value="eachKey" v-model="project.addedInfo.<?=$key1?>" />
                    <span class="font-12">{%eachValue%}</span>
                </label>
            </div>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listType?>'][project.addedInfo.<?=$key1?>] %}
            </div>
        </div>
    </td>
</tr>
<tr>
    <th >단가</th>
    <td >
        <?php $key1 = 'info011'; $listType = 'ratingType'?>
        <div class="" >
            <div v-show="isModify">
                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                    <input type="radio" :name="'project-added-info-<?=$key1?>'"  :value="eachKey" v-model="project.addedInfo.<?=$key1?>" />
                    <span class="font-12">{%eachValue%}</span>
                </label>
            </div>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listType?>'][project.addedInfo.<?=$key1?>] %}
            </div>
        </div>
    </td>
    <th >납기</th>
    <td >
        <?php $key1 = 'info012'; $listType = 'ratingType'?>
        <div class="" >
            <div v-show="isModify">
                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                    <input type="radio" :name="'project-added-info-<?=$key1?>'"  :value="eachKey" v-model="project.addedInfo.<?=$key1?>" />
                    <span class="font-12">{%eachValue%}</span>
                </label>
            </div>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listType?>'][project.addedInfo.<?=$key1?>] %}
            </div>
        </div>
    </td>
</tr>
</tbody>

<tbody v-if="15 >= Number(project.projectStatus)">
<tr>
    <th >폐쇄몰 관심도</th>
    <td colspan="99">
        <?php $key1 = 'info015'; $listType = 'ratingType'?>
        <div class="" >
            <div v-show="isModify">
                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                    <input type="radio" :name="'project-added-info-<?=$key1?>'"  :value="eachKey" v-model="project.addedInfo.<?=$key1?>" />
                    <span class="font-12">{%eachValue%}</span>
                </label>
            </div>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listType?>'][project.addedInfo.<?=$key1?>] %}
            </div>
        </div>
    </td>
</tr>
</tbody>

<tbody v-if="Number(project.projectStatus) > 15">
<tr >
    <th >이노버 제공 샘플 선호도</th>
    <td >
        <?php $key1 = 'info013'?>
        <div v-show="isModify">
            <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>" placeholder="이노버 제공 샘플 선호도">
        </div>
        <div v-show="!isModify">
            {% project.addedInfo.<?=$key1?> %}
        </div>
    </td>
    <th >폐쇄몰 관심도</th>
    <td >
        <?php $key1 = 'info015'; $listType = 'ratingType'?>
        <div class="" >
            <div v-show="isModify">
                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                    <input type="radio" :name="'project-added-info-<?=$key1?>'"  :value="eachKey" v-model="project.addedInfo.<?=$key1?>" />
                    <span class="font-12">{%eachValue%}</span>
                </label>
            </div>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listType?>'][project.addedInfo.<?=$key1?>] %}
            </div>
        </div>
    </td>
</tr>
<tr >
    <th >고객 희망 기능</th>
    <td colspan="3">
        <?php $key1 = 'info014'?>
        <div v-show="isModify">
            <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>" placeholder="고객이 근무복에 필요로 하는 기능">
        </div>
        <div v-show="!isModify">
            {% project.addedInfo.<?=$key1?> %}
        </div>
    </td>
</tr>
</tbody>
<?php include 'basic_type_foot.php'?>
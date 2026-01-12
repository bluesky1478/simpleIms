<div class="search-detail-box form-inline" >
    <table class="table table-cols table-td-height0">
        <colgroup>
            <col class="width-sm">
            <col class="width-3xl">
            <col class="width-sm">
            <col class="width-3xl">
        </colgroup>
        <tbody>
        <tr>
            <th rowspan="2">
                검색어
            </th>
            <td rowspan="2">
                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchProject()" />
                    <div class="btn btn-sm btn-red" @click="searchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
                    <div class="btn btn-sm btn-gray" @click="searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="searchCondition.multiKey.length > 1 ">-제거</div>
                </div>
                <div class="mgb5">
                    다중 검색 :
                    <select class="form-control" v-model="searchCondition.multiCondition">
                        <option value="AND">AND (그리고)</option>
                        <option value="OR">OR (또는)</option>
                    </select>
                </div>
            </td>
            <th></th>
            <td >

            </td>
        </tr>
        <tr>
            <th>

            </th>
            <td>

            </td>
        </tr>
        <tr>
            <th>
                프로젝트 타입 검색
            </th>
            <td colspan="3">
                <div class="checkbox ">
                    <div >

                        {% searchCondition.projectTypeChk %}

                        <label class="checkbox-inline mgr10">
                            <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]"
                                   :checked="0 >= searchCondition.projectTypeChk.length?'checked':''" @click="searchCondition.projectTypeChk=[]"> 전체
                        </label>
                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                            <label class="mgr10">
                                <?=$k?>
                                <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  v-model="searchCondition.projectTypeChk"> <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="99" class="ta-c" style="border-bottom: none">
                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshList(1)">
                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="conditionReset()">
            </td>
        </tr>
        </tbody>
    </table>
</div>
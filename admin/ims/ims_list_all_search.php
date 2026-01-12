<!--검색 시작-->
<div class="search-detail-box form-inline">
    <table class="table table-cols table-td-height0 table-pd-5 table-pdl-7 border-top-none ">
        <colgroup>
            <col class="w-7p">
            <col class="w-34p">
            <col class="w-6p">
            <col class="w-20p">
            <col class="w-6p">
            <col class="w-18p">
            <col class="w-6p">
        </colgroup>
        <tbody>
        <tr>
            <th rowspan="2" class="text-center">
                검색어
            </th>
            <td rowspan="2">
                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
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
            <th class="font-12">프로젝트타입</th>
            <td colspan="4">
                <div class="checkbox ">
                    <div >
                        <label class="checkbox-inline mgr10">
                            <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]" :checked="0 >= searchCondition.projectTypeChk.length?'checked':''" @click="searchCondition.projectTypeChk=[]"> 전체
                        </label>
                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                            <label class="mgr10">
                                <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  v-model="searchCondition.projectTypeChk"> <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </td>
            <!--<td rowspan="4" class="">
                <div class="btn btn-lg btn-black w-100p" style="height:75px;padding-top:30px" @click="refreshList(1)">검색</div>
                <div class="btn btn-white mgt5 w-100p" @click="conditionReset()">초기화</div>
            </td>-->
        </tr>
        <tr>
            <th>
                진행상태
            </th>
            <td colspan="4">
                <div class="checkbox ">
                    <div >
                        <label class="checkbox-inline " style="width:115px">
                            <input type="checkbox" name="orderProgressChk[]" value="all" class="js-not-checkall" data-target-name="orderProgressChk[]"
                                   :checked="0 >= searchCondition.orderProgressChk.length?'checked':''" @click="searchCondition.orderProgressChk=[]"> 전체
                        </label>
                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS_ALL_MAP as $k => $v){ ?>
                            <label class="" style="width:115px">
                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressChk[]" value="<?=$k?>"  v-model="searchCondition.orderProgressChk"  >
                                <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>지연/미확정</th>
            <td>
                <label class="checkbox-inline mgr10">
                    <input type="checkbox" name="delayStatus[]" value="all" class="js-not-checkall" data-target-name="delayStatus[]"
                           :checked="0 >= searchCondition.delayStatus.length?'checked':''" @click="searchCondition.delayStatus=[];refreshList(1)"> 전체
                </label>

                <label class="mgr10 hover-btn cursor-pointer">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="1" v-model="searchCondition.delayStatus"  >
                    <span class="text-danger">
                        <i aria-hidden="true" class="fa fa-exclamation-triangle"></i>일정 지연
                    </span>
                </label>

                <label class="mgr10 hover-btn cursor-pointer">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="2" v-model="searchCondition.delayStatus"  >
                    생산가 미확정
                </label>

                <label class="mgr10 hover-btn cursor-pointer">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="3" v-model="searchCondition.delayStatus"  >
                    판매가 미확정
                </label>

                <label class="mgr10 hover-btn cursor-pointer">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="4" v-model="searchCondition.delayStatus"  >
                    아소트 미확정
                </label>

            </td>
            <th>
                사업계획
            </th>
            <td>
                <label class="radio-inline ">
                    <input type="radio" name="bizPlanYn" value="all" v-model="searchCondition.bizPlanYn"  />전체
                </label>
                <label class="radio-inline">
                    <input type="radio" name="bizPlanYn" value="y" v-model="searchCondition.bizPlanYn"/>포함
                </label>
                <label class="radio-inline">
                    <input type="radio" name="bizPlanYn" value="n" v-model="searchCondition.bizPlanYn" />미포함
                </label>
            </td>
            <th>회계 반영</th>
            <td colspan="2">
                <label class="radio-inline ">
                    <input type="radio" name="isBookRegistered" value="0" v-model="searchCondition.isBookRegistered"/>전체
                </label>
                <label class="radio-inline">
                    <input type="radio" name="isBookRegistered" value="y" v-model="searchCondition.isBookRegistered"/>회계반영
                </label>
                <label class="radio-inline">
                    <input type="radio" name="isBookRegistered" value="n" v-model="searchCondition.isBookRegistered"/>회계미반영
                </label>
            </td>
        </tr>
        <tr>
            <th>부가서비스</th>
            <td>
                <label class="mgr10">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="useMall" value="y"
                           v-model="searchCondition.chkUseMall"  > <span class="">폐쇄몰</span>
                </label>
                <label class="mgr10">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="use3pl" value="y"
                           v-model="searchCondition.chkUse3pl"  > <span class="">3PL</span>
                </label>
                <label class="mgr10">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="packingYn" value="y"
                           v-model="searchCondition.chkPackingYn" > <span class="">분류패킹</span>
                </label>
                <label class="mgr10">
                    <input class="checkbox-inline chk-progress" type="checkbox" name="directDeliveryYn" value="y"
                           v-model="searchCondition.chkDirectDeliveryYn" > <span class="">직접납품</span>
                </label>
            </td>
            <th>
                업무타입
            </th>
            <td colspan="4" class="">
                <div class="checkbox ">
                    <div>
                        <label class="radio-inline ">
                            <input type="radio" name="designWorkType" value="all" v-model="searchCondition.designWorkType"  />전체
                        </label>
                        <?php foreach(\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $designWorkKey => $designWorkName){ ?>
                            <?php if(empty($designWorkKey)) continue;?>
                            <label class="radio-inline">
                                <input type="radio" name="designWorkType" value="<?=$designWorkKey?>" v-model="searchCondition.designWorkType" /><?=$designWorkName?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                검색 기간
            </th>
            <td >
                <div class="dp-flex">

                    <select class="form-control" style="height:26px" v-model="searchCondition.searchDateType">
                        <option value="prj.regDt">등록일</option>
                        <option value="ext.exProductionOrder">발주D/L</option>
                    </select>

                    <div class="mini-picker mgl5">
                        <date-picker v-model="searchCondition.startDt"
                                     value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="font-weight: normal"></date-picker>
                    </div>
                    <div>~</div>
                    <div class="mini-picker">
                        <date-picker v-model="searchCondition.endDt"
                                     value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="font-weight: normal;"></date-picker>
                    </div>

                    <div class="form-inline" >
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                    </div>

                </div>
            </td>
            <th>
                연도/시즌
            </th>
            <td colspan="99">
                연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="searchCondition.year" style="width:80px" />
                시즌 :
                <select class="form-control" name="projectSeason" v-model="searchCondition.season">
                    <option value="">선택</option>
                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<!--검색 끝-->

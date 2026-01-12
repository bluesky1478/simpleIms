<!--입찰관리 검색 조건-->
<div class="row" v-show="'tab1' === tabMode">
    <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
        <div>
            <!--검색 시작-->
            <div class="search-detail-box form-inline" >
                <table class="table table-cols table-th-height0 table-td-height0 table-pd-10 border-top-none">
                    <colgroup>
                        <col class="w-7p">
                        <col class="w-33p">
                        <col class="w-7p">
                        <col class="w-18p">
                        <col class="w-7p">
                        <col class="w-28p">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th rowspan="2">
                            검색어
                        </th>
                        <td rowspan="2">
                            <div v-for="(keyCondition,multiKeyIndex) in anotherList.tab1.condition.multiKey" class="mgb5">
                                검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="getSalesAnotherList('tab1')" />

                                <div class="btn btn-sm btn-red" @click="anotherList.tab1.condition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === anotherList.tab1.condition.multiKey.length ">+추가</div>
                                <div class="btn btn-sm btn-gray" @click="anotherList.tab1.condition.multiKey.splice(multiKeyIndex, 1)" v-if="anotherList.tab1.condition.multiKey.length > 1 ">-제거</div>
                            </div>
                            <div class="mgb5">
                                다중 검색 :
                                <select class="form-control" v-model="anotherList.tab1.condition.multiCondition">
                                    <option value="AND">AND (그리고)</option>
                                    <option value="OR">OR (또는)</option>
                                </select>
                            </div>
                        </td>
                        <th>
                            진행타입
                        </th>
                        <td >
                            <div>
                                <label class="radio-inline ">
                                    <input type="radio" name="bidType2" value="all" v-model="anotherList.tab1.condition.bidType2" @change="getSalesAnotherList('tab1')" />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bidType2" value="bid" v-model="anotherList.tab1.condition.bidType2"  @change="getSalesAnotherList('tab1')" />입찰
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bidType2" value="costBid" v-model="anotherList.tab1.condition.bidType2"  @change="getSalesAnotherList('tab1')" />비딩
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bidType2" value="single" v-model="anotherList.tab1.condition.bidType2"  @change="getSalesAnotherList('tab1')" />단독
                                </label>
                            </div>
                        </td>
                        <th>사업계획</th>
                        <td >
                            <label class="radio-inline ">
                                <input type="radio" name="bizPlanYn" value="all" v-model="anotherList.tab1.condition.bizPlanYn" @change="getSalesAnotherList('tab1')" />전체
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="bizPlanYn" value="y" v-model="anotherList.tab1.condition.bizPlanYn"  @change="getSalesAnotherList('tab1')" />포함
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="bizPlanYn" value="n" v-model="anotherList.tab1.condition.bizPlanYn"  @change="getSalesAnotherList('tab1')" />미포함
                            </label>

                            <input type="text" class="form-control mgl20" style="width:80px" placeholder="계획연도">
                            <div class="btn btn-white btn-sm">25년</div>
                            <div class="btn btn-white btn-sm">26년</div>
                        </td>
                    </tr>
                    <th >담당</th>
                    <td colspan="3">
                        <div class="dp-flex">
                            담당영업 :
                            <div class="btn btn-white btn-sm" @click="anotherConditionReset()">전체</div>
                            <div class="btn btn-white btn-sm" @click="setSales(0,'tab1')">미지정</div>
                            <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                <div class="btn btn-white btn-sm" @click="setSales('<?=$salesSno?>','tab1')"><?=$sales?></div>
                            <?php } ?>
                        </div>
                        <div class="dp-flex mgt5">
                            디자이너 :
                            <div class="btn btn-white btn-sm" @click="anotherConditionReset()">전체</div>
                            <div class="btn btn-white btn-sm" @click="setDesigner('designTbc')">미지정</div>
                            <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                <div class="btn btn-white btn-sm" @click="setDesigner('<?=$designer?>','tab1')"><?=$designer?></div>
                            <?php } ?>
                        </div>
                    </td>
                    </tbody>
                </table>
            </div>
            <!--검색 끝-->
        </div>
        <div class="dp-flex dp-flex-center">
            <div class="btn btn-lg btn-black w-100px" @click="getSalesAnotherList('tab1')">검색</div>
            <div class="btn btn-lg btn-white w-100px" @click="anotherConditionResetSales()">초기화</div>
        </div>

    </div>

</div>

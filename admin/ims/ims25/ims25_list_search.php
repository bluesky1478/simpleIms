<!--검색 시작-->
<div class="search-detail-box form-inline">
    <table class="table table-cols table-td-height0 table-th-height0 table-pd-5 table-pdl-7 border-top-none ">
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
            <th rowspan="2" >
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
        </tr>
        <tr>
            <th style="height:30px !important;">
                진행상태
            </th>
            <td colspan="4" class="pd0">
                <div class="checkbox ">
                    <div >
                        <label class="checkbox-inline " style="width:115px">
                            <input type="checkbox" name="orderProgressChk[]" value="all" class="js-not-checkall" data-target-name="orderProgressChk[]"
                                   :checked="!$.isEmpty(searchCondition.orderProgressChk)  && 0 >= searchCondition.orderProgressChk.length?'checked':''" @click="searchCondition.orderProgressChk=[]"> 전체
                        </label>
                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS_PROC_MAP as $k => $v){ ?>
                            <label class="" style="width:115px">
                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressChk[]" value="<?=$k?>"  v-model="searchCondition.orderProgressChk"  >
                                <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
                <div class="checkbox ">
                    <div class="_dp-flex">
                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_HOLD_STATUS as $k => $v){ ?>
                            <label class="" style="width:115px">
                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressChk[]" value="<?=$k?>"  v-model="searchCondition.orderProgressChk"  >
                                <?=$v?>
                            </label>
                        <?php } ?>

                        <button type="button" class="ims-mini-btn ims-mini-btn--ghost ims-mini-btn-gray mgt3" @click="searchCondition.orderProgressChk=['15']">
                            사전영업건 선택
                        </button>

                        <button type="button" class="ims-mini-btn ims-mini-btn--ghost ims-mini-btn-gray mgt3" @click="searchCondition.orderProgressChk=['20','30','31','40','41','50']">
                            기획/제작건 선택
                        </button>

                        <button type="button" class="ims-mini-btn ims-mini-btn--ghost ims-mini-btn-gray mgt3" @click="searchCondition.orderProgressChk=['50','60']">
                            발주진행건 선택
                        </button>

                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                사업계획
            </th>
            <td>
                <div class="dp-flex dp-flex-gap25">
                    <div class="dp-flex">
                        <b>사업계획 :</b>
                        <label class="radio-inline ">
                            <input type="radio" name="bizPlanYn" value="all" v-model="searchCondition.bizPlanYn"  />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="bizPlanYn" value="y" v-model="searchCondition.bizPlanYn"/>포함
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="bizPlanYn" value="n" v-model="searchCondition.bizPlanYn" />미포함
                        </label>
                    </div>
                    <div class="dp-flex">
                        <b>목표매출 년도 :</b>
                        <select class="form-control" name="targetSalesYear" v-model="searchCondition.targetSalesYear">
                            <option value="">선택</option>
                            <option v-for="year in getYearList(25)">{% year %}</option>
                        </select>
                    </div>
                </div>
            </td>
            <th>연도/시즌</th>
            <td>
                연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="searchCondition.year" style="width:80px" />
                시즌 :
                <select class="form-control" name="projectSeason" v-model="searchCondition.season">
                    <option value="">선택</option>
                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                    <?php } ?>
                </select>
            </td>
            <th>부가서비스</th>
            <td colspan="3">
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
        </tr>
        <tr>
            <th>진행타입</th>
            <td>
                <div class="checkbox ">
                    <div>
                        <label class="radio-inline ">
                            <input type="radio" name="bidType2" value="all" v-model="searchCondition.bidType2"  />전체
                        </label>
                        <?php foreach(\Component\Ims\ImsCodeMap::BID_TYPE as $bidTypeKey => $bidTypeKeyName){ ?>
                            <label class="radio-inline">
                                <input type="radio" name="bidType2" value="<?=$bidTypeKey?>" v-model="searchCondition.bidType2" /><?=$bidTypeKeyName?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </td>
            <th>
                고객상태
            </th>
            <td colspan="4" class="">
                <div class="checkbox ">
                    <div >
                        <label class="checkbox-inline " >
                            <input type="checkbox" name="customerStatus[]" value="all" class="js-not-checkall" data-target-name="customerStatus[]"
                                   :checked="0 >= searchCondition.customerStatus.length?'checked':''" @click="searchCondition.customerStatus=[]"> 전체
                        </label>
                        <?php foreach( \Component\Ims\ImsCodeMap::CUSTOMER_STATUS as $k => $v){ ?>
                            <label class="checkbox-inline">
                                <input class="checkbox-inline chk-progress" type="checkbox" name="customerStatus[]" value="<?=$k?>"  v-model="searchCondition.customerStatus"  >
                                <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                영업 담당자
            </th>
            <td >
                <div class="dp-flex">
                    <div class="btn btn-white btn-sm" @click="salesConditionReset()">전체</div>
                    <div :class="'btn btn-sm ' + (('salesTbc' == searchCondition.searchManager)?'btn-gray':'btn-white')"
                         @click="setManager('salesTbc')">영업 미지정</div>
                    <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                        <div :class="'btn btn-sm ' + (('<?=$salesSno?>' == searchCondition.searchManager)?'btn-gray':'btn-white')"
                             @click="setManager('<?=$salesSno?>')"><?=$sales?></div>
                    <?php } ?>
                </div>
            </td>
            <th>
                고객업종
            </th>
            <td colspan="99">
                <div class="dp-flex">

                    <select @change="searchCondition.busiCateSno = 0;" v-model="searchCondition.parentBusiCateSno" class="form-control w100p">
                        <option value="0">선택</option>
                        <option v-for="(val, key) in parentCateList" :value="key">{% val %}</option>
                    </select>

                    <select v-model="searchCondition.busiCateSno" class="form-control w100p">
                        <option v-for="(val, key) in cateList[searchCondition.parentBusiCateSno]" :value="key">{% val %}</option>
                    </select>

                </div>
            </td>
        </tr>
        <tr>
            <th>
                디자인 담당자
            </th>
            <td >
                <div class="dp-flex">
                    <div class="btn btn-white btn-sm" @click="conditionReset()">전체</div>
                    <div :class="'btn btn-sm ' + (('designTbc' == searchCondition.searchManager)?'btn-gray':'btn-white')"
                         @click="setManager('designTbc')">디자인미지정</div>
                    <?php foreach($designManagerList as $designerSno => $designer) { ?>
                        <div :class="'btn btn-sm ' + (('<?=$designerSno?>' == searchCondition.searchManager)?'btn-gray':'btn-white')"
                             @click="setManager('<?=$designerSno?>')"><?=$designer?></div>
                    <?php } ?>
                </div>
            </td>
            <th>
                검색 기간
            </th>
            <td colspan="99">
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
        </tr>
        </tbody>
    </table>
</div>
<!--검색 끝-->

<div class="dp-flex dp-flex-center">
    <div class="btn btn-lg btn-black w-100px" @click="refreshList(1)">검색</div>
    <div class="btn btn-lg btn-white w-100px" @click="conditionReset()" v-if="typeof listTabMode == 'undefined'">초기화</div>
    <div class="btn btn-lg btn-white w-100px" @click="salesConditionReset()" v-else>초기화</div>
</div>
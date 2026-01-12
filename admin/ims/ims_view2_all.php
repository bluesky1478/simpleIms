<?php use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
$modelPrefix='design';  ?>

<div class="col-xs-12" >
    <div class="col-xs-12" >
        <div >
            <div class="table-title gd-help-manual">
                <div class="flo-left area-title ">
                    <div class="godo dp-flex dp-flex-gap10">
                        현재 프로젝트 상태 :
                        <div class="sl-blue dp-flex dp-flex-gap5">
                            {% project.projectStatusKr %} 단계
                            <div class="dp-flex dp-flex-gap10">
                                <div class="btn btn-sm btn-white mgl5" @click="openProjectStatusHistory(project.sno,'')">단계변경/이력</div>
                                <div class="btn btn-sm btn-white " @click="openSalesView(project.sno)">영업기획서</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="dp-flex" style="position:absolute;top:-10px; right:20px">
                        <div class="btn btn-lg btn-white mgl3" @click="setModify(true)" v-show="!isModify">
                            수정
                        </div>
                        <div class="btn btn-lg btn-red btn-red2 mgl10" @click="save()" v-show="isModify">저장</div>
                        <div class="btn btn-lg btn-white" @click="setModify(false)" v-show="isModify">수정 취소</div>
                    </div>
                </div>
            </div>
            <div>
                <table class="table ims-schedule-table w100 table-default-center table-fixed table-td-height35 table-th-height35 mgb10 table-pd-3 font-14">
                    <colgroup>
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                    </colgroup>
                    <tr>
                        <th>연도/시즌</th>
                        <th>사업계획</th>
                        <th>프로젝트 타입</th>
                        <th>입찰 구분</th>
                        <th>디자인업무 타입</th>
                        <th>영업 담당자</th>
                        <th>디자인 담당자</th>
                    </tr>
                    <!--보여주기-->
                    <tr v-if="!isModify">
                        <td>
                            {% project.projectYear %}/{% project.projectSeason %}
                        </td>
                        <td>
                            {% getCodeMap()['includeType'][project.bizPlanYn] %}
                        </td>
                        <td>
                            {% project.projectTypeKr %}
                        </td>
                        <td>
                            <span class="">{% project.bidType2Kr %}</span>
                            <span v-show="'single' !== project.bidType2" class="font-12">
                                (예정일:{% $.formatShortDateWithoutWeek(project.exMeeting) %})
                            </span>
                        </td>
                        <td>
                            {% project.designWorkTypeKr %}
                        </td>
                        <td>
                            <div v-show="$.isEmpty(project.salesManagerNm)">미정</div>
                            <div v-show="!$.isEmpty(project.salesManagerNm)">{% project.salesManagerNm %}</div>
                        </td>
                        <td>
                            <div v-show="$.isEmpty(project.designManagerNm)">미정</div>
                            <div v-show="!$.isEmpty(project.designManagerNm)">{% project.designManagerNm %}</div>
                        </td>
                    </tr>
                    <!--수정-->
                    <tr v-if="isModify">
                        <td>
                            <select v-model="project.projectYear" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                                <?php foreach($yearList as $yearEach) {?>
                                    <option><?=$yearEach?></option>
                                <?php }?>
                            </select>
                            <select v-model="project.projectSeason" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                                <option >ALL</option>
                                <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                    <option><?=$seasonEn?></option>
                                <?php }?>
                            </select>
                        </td>
                        <td>
                            <label class="radio-inline mgl5 mgr5" v-for="(eachValue, eachKey) in getCodeMap()['includeType']" >
                                <input type="radio" name="salesProjectBizPlanYn'"  :value="eachKey" v-model="project.bizPlanYn" style="margin-top:3px" />
                                <span class="font-14">{%eachValue%}</span>
                            </label>
                        </td>
                        <td>
                            <?php
                            $model='project.projectType';
                            $modelValue='project.projectTypeKr';
                            $listData=\Component\Ims\ImsCodeMap::PROJECT_TYPE;
                            $selectWidth=100
                            ?>
                            <?php include 'template/basic_view/_select2.php'?>
                        </td>
                        <td class="text-center">
                            <div class="dp-flex dp-flex-center">
                                <select class="form-control w35 " v-model="project.bidType2" >
                                    <?php foreach (\Component\Ims\ImsCodeMap::BID_TYPE as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                                <div class="dp-flex mgt3 font-13" v-show="'single' !== project.bidType2">
                                    예정:<date-picker v-model="project.exMeeting" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="예정일"></date-picker>
                                </div>
                            </div>
                        </td>
                        <td>
                            <select class="form-control w100" v-model="project.designWorkType" >
                                <?php foreach (\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control w100" v-model="project.salesManagerSno" >
                                <option value="0">미정</option>
                                <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control w100" v-model="project.designManagerSno" >
                                <option value="0">미정</option>
                                <?php foreach ($designManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                </table>
            </div>
            <div>
                프로젝트 등록일: {% project.regDt %} ({% project.regManagerNm %})
            </div>
        </div>
    </div>
</div>

<div v-show="bFlagExistPlanSche || isModifyPlan" class="col-xs-12" >
    <div class="col-xs-12" >
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title ">
                <span class="godo">최초 기획 스케줄</span>
            </div>
            <div class="flo-right sl-test1">
                <div class="dp-flex" style="position:absolute;top:-10px; right:20px">
                    <div class="btn btn-lg btn-white mgl3" @click="isModifyPlan=true;" v-show="!isModifyPlan">수정</div>
                    <div class="btn btn-lg btn-red btn-red2 mgl10" @click="save_plan_sche()" v-show="isModifyPlan">저장</div>
                    <div class="btn btn-lg btn-white" @click="isModifyPlan=false;" v-show="isModifyPlan">저장 취소</div>
                </div>
            </div>
        </div>
        <table class="table ims-schedule-table w100 table-default-center table-fixed  table-td-height35 table-th-height35 mgb10 table-pd-3">
            <colgroup>
                <col class="w-5p">
            </colgroup>
            <tr>
                <th class="" >구분</th>
                <?php foreach( NkCodeMap::PROJECT_PLAN_SCHE_STEP as $key => $val ){ ?>
                    <th class="" ><?=$val?></th>
                <?php } ?>
            </tr>
            <tr v-for="(val, key) in oPlanScheList">
                <td class="bg-light-gray">{% oPlanScheTypeHan[key] %}</td>
                <td v-for="(val2, key2) in val">
                    <span v-show="!isModifyPlan">
                        <div v-if="val2 == ''" class="text-muted">미정</div>
                        <div v-else>
                            {% $.formatShortDate(val2) %}
                        </div>
                    </span>
                    <span v-show="isModifyPlan">
                        <date-picker v-model="oPlanScheList[key][key2]" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="일자선택"></date-picker>
                    </span>
                </td>
            </tr>
            <tr>
                <th>일정비고</th>
                <td colspan="<?=count(NkCodeMap::PROJECT_PLAN_SCHE_STEP)?>">
                    <div v-show="!isModifyPlan" style="padding: 10px;" class="ta-l" v-html="project.planScheMemo == null ? project.planScheMemo : project.planScheMemo.replaceAll('\n', '<br />')"></div>
                    <span v-show="isModifyPlan">
                        <textarea class="form-control" rows="6" v-model="project.planScheMemo" placeholder="일정비고"></textarea>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>

<!--업무 스케쥴-->
<div class="col-xs-12" >
    <div class="col-xs-12" >
        <div >
            <div class="table-title gd-help-manual">
                <div class="flo-left area-title ">
                    <span class="godo">
                        스케쥴 관리 <span class="" v-show="isViewDetail">( 상세 )</span>
                    </span>
                </div>
                <div class="flo-right sl-test1">

                    <div class="" style="position:absolute;top:-5px; left:200px" v-show="isViewDetail">
                        <div class="dp-flex dp-flex-gap10 font-13" v-show="!isModify">
                            <div class="round-box bg-light-orange">
                                고객납기 : {% $.formatShortDateWithoutWeek(project.customerDeliveryDt) %}
                            </div>
                            <!--
                            <div class="round-box bg-light-blue">
                                MS납기 : {% $.formatShortDateWithoutWeek(project.msDeliveryDt) %}
                            </div>
                            -->
                            <div class="round-box bg-light-gray dp-flex font-13">
                                발주D/L :
                                <!--완료일-->
                                <div v-if="'0000-00-00' != project.cpProductionOrder && !$.isEmpty(project.cpProductionOrder)" class="text-muted">
                                    <span class="font-13 sl-green">
                                        {% $.formatShortDateWithoutWeek(project.cpProductionOrder) %} 발주
                                    </span>
                                </div>
                                <!--대체텍스트-->
                                <div v-else-if="!$.isEmpty(project.txProductionOrder)">
                                    <span class="font-11">
                                        {% project.txProductionOrder %}
                                    </span>
                                </div>
                                <!--예정일-->
                                <div v-else-if="!$.isEmpty(project.exProductionOrder)" class="">
                                    <span class="font-13">
                                        {% $.formatShortDateWithoutWeek(project.exProductionOrder) %}
                                    </span>
                                    <span class="font-11 mgl5" v-html="$.remainDate(project.exProductionOrder,true)"></span>
                                </div>
                                <!--미설정-->
                                <div v-else class="text-muted">미정</div>

                            </div>
                        </div>
                        <div class="dp-flex dp-flex-gap10 font-13" v-show="isModify">
                            <div class="round-box bg-light-orange">
                                고객납기 : <date-picker v-model="project.customerDeliveryDt" class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 납기"></date-picker>
                            </div>
                            <!--
                            <div class="round-box bg-light-blue">
                                MS납기 : <date-picker v-model="project.msDeliveryDt" class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="MS 납기"></date-picker>
                            </div>
                            -->
                            <div class="round-box bg-light-gray dp-flex">
                                <div class="w-100px">발주D/L :</div>
                                <!--발주 예정일 수정-->
                                <date-picker v-model="project.exProductionOrder"
                                             class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                                </date-picker>
                                <input type="text" placeholder="대체텍스트" class="form-control " v-model="project.txProductionOrder">
                            </div>
                        </div>
                    </div>

                    <div class="dp-flex" style="position:absolute;top:-10px; right:20px">
                        <div class="btn btn-lg btn-black mgl3" @click="isViewDetail=true" v-show="!isViewDetail && !isModify ">
                            세부 일정
                        </div>
                        <div class="btn btn-lg btn-blue mgl3" @click="isViewDetail=false" v-show="isViewDetail && !isModify">
                            기본 일정
                        </div>
                        <div class="btn btn-lg btn-white mgl3" @click="setModify(true)" v-show="!isModify">
                            수정
                        </div>
                        <div class="btn btn-lg btn-red btn-red2 mgl10" @click="save()" v-show="isModify">저장</div>
                        <div class="btn btn-lg btn-white" @click="setModify(false)" v-show="isModify">수정 취소</div>
                    </div>
                    <!--refreshing-loader-->
                </div>
            </div>


            <!-- 기본 스케쥴 -->
            <div v-show="!isViewDetail">

                <?php include 'ims_view2_all_schedule.php'?>

                <table class="table ims-schedule-table w100 table-default-center table-fixed  table-td-height35 table-th-height35 mgb10 table-pd-3" v-show="!scheduleLoad">
                    <tr>
                        <td class="ta-c" style="padding:15px!important;">
                            <div class="spinner-loader vue-loader"> </div>
                        </td>
                    </tr>
                </table>

                <div v-show="isModify" class="round-box bg-light-blue">
                    <label class="radio-inline" style="font-weight: normal;font-size:14px">
                        <input type="radio" name="syncProduct"  value="y" v-model="project.syncProduct"/> 납기일자 프로젝트로 한번에 관리
                    </label>
                    <label class="radio-inline" style="font-weight: normal;font-size:14px">
                        <input type="radio" name="syncProduct"  value="n" v-model="project.syncProduct"/> 납기일자 상품별 관리
                    </label>
                </div>
                <div v-show="!isModify" class="mgb10">
                    <div v-show="'y'===project.syncProduct" class="_notice-info font-14">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        납기일자(고객,MS)가 프로젝트 수정시 연동되어 함께 변경됩니다.
                    </div>
                    <div v-show="'n'===project.syncProduct" class="_notice-info font-14">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        납기일자(고객,MS)는 상품별 관리합니다.
                    </div>
                </div>
            </div>

            <!-- 상세 스케쥴  -->
            <div v-show="isViewDetail">
                <?php include 'ims_view2_all_schedule_detail.php'?>
            </div>

            <!--테스트버튼-->
            <div class="btn btn-white btn-sm mgt10" @click="showSchedule()" v-if="false">
                (테스트용) 결재정보 열기 - 나중에 지우기
            </div>

            <!--퀄리티비티 QB 스케쥴 관리-->
            <div v-show="isQbDetail" class="pd5" style="border:solid 1px #e0e0e0; border-radius: 10px; background-color: #fdfdfd">
                <div>
                    <div class="dp-flex dp-flex-between mgb5 pd5">
                        <div>
                            <span class="fnt-godo font-16 mgr10">
                                <i class="fa fa-sort-desc fa-lg" aria-hidden="true"></i>
                                QB 정보/스케쥴 관리
                            </span>
                        </div>
                        <div><!--닫기-->
                            <i class="fa fa-times fa-2x cursor-pointer hover-btn" aria-hidden="true" @click="isQbDetail=false"></i>
                        </div>
                    </div>

                    <table class="table ims-schedule-table ims-sub-schedule-table w-100p table-fixed table-default-center table-th-height35 table-td-height35 table-pd-3" >
                        <colgroup>
                            <col class="w-50px"><!--구분-->
                            <col ><!--상품명-->
                            <col class="w-90px"><!--퀄리티상태-->
                            <col class="w-90px"><!--부위-->
                            <col><!--원단정보-->
                            <col><!--확정 정보-->
                            <col class="w-50px"><!--제조국-->
                            <col class="w-100px"><!--의뢰처-->
                            <col class="w-80px"><!--요청일-->
                            <col class="w-80px"><!--완료예정일-->
                            <col class="w-90px"><!--BT상태-->
                            <col class="w-100px"><!--의뢰처-->
                            <col class="w-80px"><!--요청일-->
                            <col class="w-80px"><!--완료예정일-->
                            <col>
                        </colgroup>
                        <tr>
                            <th rowspan="2" class="border-right-gray-imp border-bottom-gray-imp" style="border-top:solid 1px #888888 !important;">구분</th>
                            <th colspan="9" class="border-right-gray-imp" style="border-top:solid 1px #888888 !important;">퀄리티 현황</th>
                            <th colspan="5" class="border-right-gray-imp" style="border-top:solid 1px #888888 !important;">BT 현황</th>
                        </tr>
                        <tr>
                            <th class="border-bottom-gray-imp">상품명</th>
                            <th class="border-bottom-gray-imp">퀄리티 상태</th>
                            <th class="border-bottom-gray-imp">부위</th>
                            <th class="border-bottom-gray-imp">원단 정보</th>
                            <th class="border-bottom-gray-imp">확정 정보</th>
                            <th class="border-bottom-gray-imp">제조국</th>
                            <th class="border-bottom-gray-imp">의뢰처</th>
                            <th class="border-bottom-gray-imp">요청일</th>
                            <th class="border-bottom-gray-imp border-right-gray-imp">완료 예정일</th>
                            <th class="border-bottom-gray-imp">BT 상태</th>
                            <th class="border-bottom-gray-imp">확정 정보</th>
                            <th class="border-bottom-gray-imp">의뢰처</th>
                            <th class="border-bottom-gray-imp">요청일</th>
                            <th class="border-bottom-gray-imp">완료 예정일</th>
                        </tr>
                        <tbody v-for="(prd , prdIndex) in productList" >
                            <tr v-for="(fabric , fabricIndex) in prd.fabricList">
                                <th class="border-right-gray-imp" :rowspan="prd.fabricList.length" v-if="0 === fabricIndex" style="border-bottom:solid 1px #e8e8e8 !important;">
                                    {% prdIndex+1 %}
                                </th><!--번호-->
                                <td class="" :rowspan="prd.fabricList.length" v-if="0 === fabricIndex" style="border-bottom:solid 1px #e8e8e8 !important;">
                                    <div class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, prd.sno, 1)">
                                        {% prd.styleFullName %}
                                    </div>
                                </td><!--상품명-->
                                <td :class="$.getProcColor(fabric.fabricStatus) + setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    {% fabric.fabricStatusKr %}
                                    <div class="font-11 text-muted">
                                        {% $.formatShortDate(fabric.latestFabricRequest.completeDt) %}
                                    </div>
                                </td><!--퀄리티상태-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    {% fabric.position %}
                                    {% fabric.attached %}
                                </td><!--부위-->
                                <td :class="'font-11 ta-l '+setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div :class="'hover-btn cursor-pointer'" @click="openProductWithFabric(project.sno, prd.sno, fabric.sno)">
                                        {% fabric.fabricName %}
                                        {% fabric.fabricMix %}
                                        {% fabric.color %}
                                    </div>
                                </td><!--원단정보-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    {% fabric.fabricConfirmInfo %}
                                </td><!--확정 정보-->
                                <td :class="''+setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-if="!$.isEmpty(fabric.makeNational)" ></i>
                                    <div v-if="$.isEmpty(fabric.makeNational)" class="text-muted">미정</div>
                                </td><!--제조국-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div v-if="!$.isEmpty(fabric.latestFabricRequest.sno)">
                                        {% fabric.latestFabricRequest.reqFactoryNm %}
                                    </div>
                                    <div class="text-muted" v-else>
                                        미지정
                                    </div>
                                </td><!--의뢰처-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div v-if="!$.isEmpty(fabric.latestFabricRequest.sno)">
                                        {% $.formatShortDate(fabric.latestFabricRequest.regDt) %}
                                    </div>
                                    <div class="text-muted" v-else>
                                        -
                                    </div>
                                </td><!--요청일-->
                                <td :class="'border-right-gray-imp' + setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div v-if="!$.isEmpty(fabric.latestFabricRequest.sno)">
                                        {% $.formatShortDate(fabric.latestFabricRequest.completeDeadLineDt) %}
                                    </div>
                                    <div class="text-muted" v-else>
                                        -
                                    </div>
                                </td><!--완료 예정일-->
                                <td :class="$.getProcColor(fabric.btStatus)+setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    {% fabric.btStatusKr %}
                                    <div class="font-11 text-muted">
                                        {% $.formatShortDate(fabric.latestBtRequest.completeDt) %}
                                    </div>
                                </td><!--BT상태-->
                                <td :class="''+setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    {% fabric.btConfirmInfo %}
                                </td><!--확정 정보-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div v-if="!$.isEmpty(fabric.latestBtRequest.sno)">
                                        {% fabric.latestBtRequest.reqFactoryNm %}
                                    </div>
                                    <div class="text-muted" v-else>
                                        미지정
                                    </div>
                                </td><!--의뢰처-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div v-if="!$.isEmpty(fabric.latestBtRequest.sno)">
                                        {% $.formatShortDate(fabric.latestBtRequest.regDt) %}
                                    </div>
                                    <div class="text-muted" v-else>
                                        -
                                    </div>
                                </td><!--요청일-->
                                <td :class="setQbBackgroundColor(fabric.fabricStatus, fabric.btStatus)">
                                    <div v-if="!$.isEmpty(fabric.latestBtRequest.sno)">
                                        {% $.formatShortDate(fabric.latestBtRequest.completeDeadLineDt) %}
                                    </div>
                                    <div class="text-muted" v-else>
                                        -
                                    </div>
                                </td><!--완료 예정일-->
                            </tr>
                        </tbody>
                    </table>

                    <div class="ta-c">
                        <div class="btn btn-white" @click="isQbDetail=false">▲ QB 정보/스케쥴 닫기</div>
                    </div>
                </div>
            </div>

            <!--결재/승인 관리-->
            <div v-show="isScheduleDetail" class="pd5" style="border:solid 1px #e0e0e0; border-radius: 10px; background-color: #fcfcfc ">
                <!--결재/승인-->
                <div>
                    <div class="dp-flex dp-flex-between mgb5">
                        <div>
                            <span class="fnt-godo font-14 mgr10">
                                <i class="fa fa-sort-desc fa-lg" aria-hidden="true"></i>
                                결재/승인 정보
                            </span>
                        </div>
                    </div>
                    <div>
                        <table class="table table-cols w100 table-default-center table-fixed table-th-height30">
                            <colgroup>
                                <col class="w-6p" />
                                <col class="w-18p" />
                                <col class="w-18p" />
                                <col />
                                <col />
                                <col />
                                <col />
                            </colgroup>
                            <tr>
                                <th class="border-right-gray">구분</th>
                                <th class="border-right-gray">기획서</th>
                                <th class="border-right-gray">제안서</th>
                                <th class="border-right-gray">아소트</th>
                                <th class="border-right-gray">사양서</th>
                                <th class="border-right-gray">판매가</th>
                                <th >생산가</th>
                            </tr>
                            <tr>
                                <th class="border-right-gray" >
                                    결재
                                </th>
                                <!--기획서 결재-->
                                <td class="border-right-gray" >
                                    <div class="btn btn-sm btn-red btn-red-line2"
                                         v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 && (0 >= projectApprovalInfo.plan.sno || $.isEmpty(projectApprovalInfo.plan.sno ) )"
                                         @click="openApprovalWrite(customer.sno, project.sno, 'plan')">기획서 결재 요청</div>

                                    <div class="btn btn-sm btn-red btn-red-line2" @click="setApprovalPass('plan')"
                                         v-if="!$.isEmpty(fileList.filePlan) && 0 >= fileList.filePlan.files.length && 'p' !== project.planConfirm">기획서 PASS</div>

                                    <approval-template2
                                            :project="project"
                                            :approval="projectApprovalInfo"
                                            :confirm-type="'plan'"
                                            :confirm-field="'planConfirm'"
                                            :memo-field="'planMemo'"
                                    ></approval-template2>
                                </td>
                                <!--제안서 결재-->
                                <td class="border-right-gray" >
                                    <div class="btn btn-sm btn-red btn-red-line2"
                                         v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 && (0 >= projectApprovalInfo.proposal.sno || $.isEmpty(projectApprovalInfo.proposal.sno ) )"
                                         @click="openApprovalWrite(customer.sno, project.sno, 'proposal')">제안서 결재 요청</div>

                                    <div class="btn btn-sm btn-red btn-red-line2" @click="setApprovalPass('proposal')"
                                         v-if="!$.isEmpty(fileList.fileProposal) && 0 >= fileList.fileProposal.files.length && 'p' !== project.proposalConfirm">제안서 PASS</div>

                                    <approval-template2
                                            :project="project"
                                            :approval="projectApprovalInfo"
                                            :confirm-type="'proposal'"
                                            :confirm-field="'proposalConfirm'"
                                            :memo-field="'proposalMemo'"
                                    ></approval-template2>
                                </td>
                                <!--아소트-->
                                <td class="border-right-gray" >

                                    <div :class="$.getAssortAcceptNameColor(project.assortApproval)['color'] + ' font-16 mgr10'"  v-if="!$.isEmpty(project.assortApproval)">
                                        {% $.getAssortAcceptNameColor(project.assortApproval)['name'] %}
                                    </div>

                                    <div class="dp-flex dp mgt10" style="justify-content: center" >
                                        <div class="" v-if="'n'===project.assortApproval">
                                            <div class=" btn btn-sm btn-black-line" @click="openAssortUrl()">
                                                입력URL전달
                                            </div>
                                        </div>
                                        <div class=" btn btn-sm btn-black-line"
                                             v-if="'r'===project.assortApproval"
                                             @click="openAssortUrl()">
                                            입력URL 재전달
                                        </div>
                                        <div class=" btn btn-sm btn-red btn-red-line2 cursor-pointer hover-btn"
                                             v-if="'f'===project.assortApproval"
                                             @click="setAssortStatus('p')">
                                            아소트 확정
                                        </div>
                                        <div class="btn btn-sm btn-black-line"
                                             v-if="'f'===project.assortApproval"
                                             @click="setAssortStatus('r')">
                                            이전상태로
                                        </div>
                                        <div class="btn btn-sm btn-black-line"
                                             v-if="'p'===project.assortApproval"
                                             @click="setAssortStatus('f')">
                                            확정취소
                                        </div>
                                        <div class="" >
                                            <div class=" btn btn-sm btn-black-line" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
                                                입력화면
                                            </div>
                                        </div>
                                    </div>

                                    <!--아소트 입력URL 전송 레이어팝업-->
                                    <ims-modal :visible.sync="visibleAssortSendUrl" title="아소트 입력 URL 전송정보">
                                        <div class="dp-flex justify-content-start">
                                            <label class="w-80px">담당자:</label>
                                            <input type="text" class="form-control w-85p" v-model="project.assortReceiver">
                                        </div>
                                        <div class="dp-flex justify-content-start mgt5">
                                            <label class="w-80px">Email:</label>
                                            <input type="text" class="form-control w-85p" v-model="project.assortEmail">
                                        </div>

                                        <template #footer>
                                            <div class="btn btn-blue mgt5" @click="sendAssortUrl(project.assortReceiver, project.assortEmail)">전송</div>
                                            <div class="btn btn-white mgt5" @click="visibleAssortSendUrl=false">취소</div>
                                        </template>
                                    </ims-modal>

                                </td>
                                <!--사양서-->
                                <td class="border-right-gray" >

                                    <div v-html="$.getAcceptName(project.customerOrderConfirm)" class="font-16 mgr10"></div>

                                    <div class="dp-flex mgt10" style="justify-content: center">

                                        <div class="" >
                                            <div class=" btn btn-sm btn-black-line"
                                                 v-if="'n'===project.customerOrderConfirm"
                                                 @click="openOrderUrl()">
                                                사양서 전달
                                            </div>
                                        </div>

                                        <div class=" btn btn-sm btn-black-line"
                                             v-if="'r'===project.customerOrderConfirm"
                                             @click="openOrderUrl()">
                                            사양서 재전달
                                        </div>

                                        <!-- 추가 기능 상태 변경 -> 요청 단계로 -->
                                        <div class=" btn btn-sm btn-black-line "
                                             v-if="'p'===project.customerOrderConfirm"
                                             @click="setOrderStatus('r')">
                                            확정취소
                                        </div>

                                        <div class="" >
                                            <div class=" btn btn-sm btn-black-line" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)">
                                                사양서보기
                                            </div>
                                        </div>

                                    </div>

                                    <!--사양서URL 전송 레이어팝업-->
                                    <ims-modal :visible.sync="visibleOrderSendUrl" title="사양서 입력 URL 전송정보">
                                        <div class="dp-flex justify-content-start">
                                            <label class="w-80px">담당자:</label>
                                            <input type="text" class="form-control w-85p" v-model="project.customerOrderReceiver">
                                        </div>
                                        <div class="dp-flex justify-content-start mgt5">
                                            <label class="w-80px">Email:</label>
                                            <input type="text" class="form-control w-85p" v-model="project.customerOrderEmail">
                                        </div>
                                        <template #footer>
                                            <div class="btn btn-blue mgt5" @click="sendOrderUrl(project.customerOrderReceiver, project.customerOrderEmail)">전송</div>
                                            <div class="btn btn-white mgt5" @click="visibleOrderSendUrl=false">취소</div>
                                        </template>
                                    </ims-modal>

                                </td>
                                <!--판매가 결재-->
                                <td class="border-right-gray" rowspan="2">

                                    <div class="btn btn-sm btn-red btn-red-line2"
                                         v-if="0 >= projectApprovalInfo.salePrice.sno || $.isEmpty(projectApprovalInfo.salePrice.sno)"
                                         @click="openApprovalWrite(customer.sno, project.sno, 'salePrice')">판매가 결재 요청</div>

                                    <approval-template2
                                            :project="project"
                                            :approval="projectApprovalInfo"
                                            :confirm-type="'salePrice'"
                                            :confirm-field="'prdPriceApproval'"
                                            :memo-field="'unused'"
                                    ></approval-template2>

                                </td>
                                <!--생산가 결재-->
                                <td class="border-right-gray" rowspan="2">

                                    <div class="btn btn-sm btn-red btn-red-line2"
                                         v-if="'n' === project.prdCostApproval
                                         && ( 0 >= projectApprovalInfo.cost.sno || $.isEmpty(projectApprovalInfo.cost.sno) )"
                                         @click="openApprovalWrite(customer.sno, project.sno, 'cost')">생산가 결재 요청</div>

                                    <!--
                                    <div v-if="projectApprovalInfo.<?=$each['field']?>.sno > 0" class="text-left dp-flex">
                                        <div class="mgt10 pdl5 text-green" v-show="'p' === project.<?=$each['approval']?>">
                                            결재완료
                                        </div>
                                        <div class="pd5 text-danger" v-show="'f' === project.<?=$each['approval']?>">
                                            반려
                                            <div class="btn btn-sm btn-red btn-red-line2" @click="openApprovalWrite(items.sno, project.sno, '<?=$each['field']?>')">
                                                재결재요청
                                            </div>
                                        </div>
                                        <div class="pd5" v-show="'r' === project.<?=$each['approval']?>">
                                            결재 진행 중
                                        </div>

                                        <div class="btn btn-white btn-sm" @click="openApprovalHistory({projectSno:project.sno}, '<?=$each['field']?>')">결재이력</div>
                                    </div>
                                    -->

                                    <approval-template2
                                            :project="project"
                                            :approval="projectApprovalInfo"
                                            :confirm-type="'cost'"
                                            :confirm-field="'prdCostApproval'"
                                            :memo-field="'unused'"
                                    ></approval-template2>
                                </td>
                            </tr>

                            <!--파일 업로드-->
                            <tr>
                                <th class="border-right-gray" >
                                    파일/기능
                                </th>
                                <!--기획서 파일-->
                                <td class="font-11 border-right-gray">
                                    <div class="w-100p text-left set-dropzone-type1">
                                        <file-upload :file="fileList.filePlan" :id="'filePlan'" :project="project" :accept="'p'===project.planConfirm" ></file-upload>
                                    </div>
                                </td>
                                <!--제안서 파일-->
                                <td class="border-right-gray">
                                    <div class="w-100p text-left set-dropzone-type1">
                                        <file-upload :file="fileList.fileProposal" :id="'fileProposal'" :project="project" :accept="'p'===project.proposalConfirm" ></file-upload>
                                    </div>
                                </td>
                                <!--아소트 상태-->
                                <td class="border-right-gray pd0">

                                    <table class="table-borderless font-11 table-pd-0 w-100p" >
                                        <colgroup>
                                            <col class="w-15p">
                                            <col class="w-10px">
                                            <col>
                                        </colgroup>
                                        <tr>
                                            <td class="ta-r">발송일</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl10">
                                                {% $.formatShortDateWithoutWeek(project.assortSendDt) %}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ta-r">수신인</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl3">
                                                {% project.assortReceiver %}
                                                <span class="font-10 text-muted">{% project.assortEmail %}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ta-r">입력일</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl3">
                                                {% project.assortApprovalName %}
                                                {% $.formatShortDateWithoutWeek(project.assortCustomerDt) %}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ta-r">확정일</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl3">
                                                {% $.formatShortDateWithoutWeek(project.assortManagerDt) %}
                                                <span class="font-11">{% project.assortApprovalManager %}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <!--사양서 상태-->
                                <td class="border-right-gray pd0">

                                    <table class="table-borderless font-11 table-pd-0 w-100p" >
                                        <colgroup>
                                            <col class="w-15p">
                                            <col class="w-10px">
                                            <col>
                                        </colgroup>
                                        <tr>
                                            <td class="ta-r">발송일</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl10">
                                                {% $.formatShortDateWithoutWeek(project.customerOrderSendDt) %}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ta-r">수신인</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl10">
                                                {% project.customerOrderReceiver %}
                                                <span class="font-10 text-muted">{% project.customerOrderEmail %}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ta-r">확정일</td>
                                            <td >
                                                <div class="pd5">:</div>
                                            </td>
                                            <td class="ta-l pdl10">
                                                <div v-show="!$.isEmpty(project.customerOrderConfirmDt)">
                                                    {% $.formatShortDateWithoutWeek(project.customerOrderConfirmDt) %}
                                                    <span class="font-11">{% project.customerOrderReceiver %}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="ta-c">
                    <div class="btn btn-white" @click="isScheduleDetail=false">▲ 상세정보/스케쥴 닫기</div>
                </div>

            </div>

        </div>

    </div>
</div>

<!--상품정보-->
<div class="col-xs-12 ">
    <!--예상스타일-->
    <div class="col-xs-12 js-order-view-receiver-area">
        <div class="js-layout-order-view-receiver-info">

            <div >
                <div class="relative" style="height:40px">
                    <!--스타일 탭-->
                    <ul class="nav nav-tabs mgb0" role="tablist" ><!--제안서 이상 단계에서만 선택 가능-->
                        <li role="presentation" :class="'basic' === styleTabMode?'active':''">
                            <a href="#" data-toggle="tab"  @click="changeStyleTab('basic')" >제작상품</a>
                        </li>
                        <li role="presentation" :class="'plan' === styleTabMode?'active':''">
                            <a href="#" data-toggle="tab"  @click="changeStyleTab('plan')" >기획</a>
                        </li>
                        <li role="presentation" :class="'sample' === styleTabMode?'active':''" >
                            <a href="#" data-toggle="tab" @click="changeStyleTab('sample')">샘플</a>
                        </li>
                        <li role="presentation" :class="'estimate' === styleTabMode?'active':''" >
                            <a href="#" data-toggle="tab" @click="changeStyleTab('estimate')" id="btn-customer-estimate">고객 견적서(발송이력)</a>
                        </li>
                        <li role="presentation" :class="'assort' === styleTabMode?'active':''" >
                            <a href="#" data-toggle="tab" @click="changeStyleTab('assort')">아소트</a>
                        </li>
                    </ul>
                    <div class="" style="position: absolute; top:5px; right:0">
                        <div class="mgl10 dp-flex dp-flex-gap5" >

                            <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=false" v-show="showStyle">
                                <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 숨기기
                            </div>
                            <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=true" v-show="!showStyle">
                                <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 상품 보기
                            </div>

                            <div class="btn btn-red  " @click="saveStyleList(true)" v-show="isStyleModify">스타일 저장</div>
                            <div class="btn btn-white  w-50px" @click="isStyleModify=false" v-show="isStyleModify">취소</div>
                            <div class="btn btn-red  btn-red-line2" v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;스타일 수정&nbsp;&nbsp;</div>
                            <div class="btn btn-blue btn-blue-line " @click="addSalesStyle()">+ 스타일 추가</div>

                            <div class="btn btn-blue" @click="openCommonPopup('customer_estimate', 1100, 850, {projectSno:project.sno})">고객 견적서 발송</div>

                            <button type="button" class="btn btn-red-box  js-receiverInfoBtnSave js-orderViewInfoSave display-none" >저장</button>
                        </div>
                    </div>
                </div>
                <div class="bg-light-gray pd10" style="height:50px">
                    <div class="dp-flex dp-flex-gap10">
                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px">
                            스타일 수량 : <span class="bold">{% $.setNumberFormat(styleTotal) %}ea</span>
                        </div>
                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px">
                            스타일 판매 금액 합계 : <span class="text-danger bold">{% $.setNumberFormat(styleTotalPrice) %}원</span>

                            <span class="font-12" v-if="styleTotalCost > 0 && styleTotalPrice > 0">
                                (마진:  {%  $.setNumberFormat(styleTotalPrice - styleTotalCost)  %}원, {% (100-(Math.round(styleTotalCost/styleTotalPrice*100)) ) %}%)
                            </span>
                            <span class="font-12" v-if="styleTotalPrice > 0 && 0 >= styleTotalCost && styleTotalEstimate > 0 ">
                                (미확정 마진:  {%  $.setNumberFormat(styleTotalPrice - styleTotalEstimate)  %}원, {% (100-(Math.round(styleTotalEstimate/styleTotalPrice*100)) ) %}%)
                            </span>
                        </div>

                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px" v-if="styleTotalCost > 0">
                            스타일 매입금액 합계 : <span class="sl-blue bold">{% $.setNumberFormat(styleTotalCost) %}원</span>
                        </div>
                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px" v-if="0 >= styleTotalCost && styleTotalEstimate > 0">
                            (미확정) 매입금액 합계 : <span class="color-gray bold">{% $.setNumberFormat(styleTotalEstimate) %}원</span>
                        </div>
                    </div>
                </div>
                <div v-show="showStyle && 'basic' == styleTabMode" class="new-style2">
                    <?php include 'ims_view2_all_style.php'?>
                </div>
                <div v-show="showStyle && 'plan' == styleTabMode" class="new-style2">
                    <?php include 'template/ims_product_plan.php' ?>
                </div>
                <div v-show="showStyle && 'sample' == styleTabMode" class="">
                    <?php include 'template/style/type_sample.php'?>
                </div>
                <div v-show="showStyle && 'estimate' == styleTabMode" class="">
                    <?php include 'template/style/type_estimate.php'?>
                </div>
                <div v-show="showStyle && 'assort' == styleTabMode" class="">
                    <?php include 'template/style/type_assort.php'?>
                </div>

                <div class="js-layout-order-view-receiver-info new-style2" v-show="!showStyle">
                    <table class="table ">
                        <td class="ta-c">상품 숨김</td>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="col-xs-12 " v-if="!$.isEmpty(customer.addedInfo)">
    <div class="col-xs-12" >
        <div class="border-light-gray pd10 font-16 dp-flex dp-flex-gap20">
            <div>
                프로젝트
                <span v-if="2 != project.costStatus">예정</span>
                생산가 : <span class="sl-blue">{% $.setNumberFormat(computed_calc_project_account) %}원</span>
            </div>
            <div >
                프로젝트
                <span v-if="2 != project.priceStatus">예정</span>
                판매가 : <span class="text-danger">{% $.setNumberFormat(iProjectSaleAmount) %}원</span>
            </div>
            <div>
                마진 : {% iProjectMargin %}%
            </div>
        </div>
    </div>
</div>

<!-- 부가 금액 정보 -->
<div class="col-xs-12 mgt20" v-if="!$.isEmpty(customer.addedInfo)">
    <!--부가판매-->
    <div class="col-xs-12" >
        <div class="table-title gd-help-manual">
            <div class="flo-left   text-danger">
                부가 판매/구매 정보
                <span class="font-12 font-black font-normal noto" style="font-weight:normal !important;">
                    고객 청구 대상 프로젝트 발생 비용 (VAT제외 가격으로 입력하세요!)
                </span>
            </div>
            <div class="flo-right">
                <div class="btn btn-white btn-sm" @click="addAddedSale();">+ 부가 판매 추가</div>
                <button type="button" class="btn btn-red btn-sm" v-show="!isModifyAddedSale" @click="vueApp.isModifyAddedSale=true;">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModifyAddedSale" @click="save_added_sale()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModifyAddedSale" @click="vueApp.isModifyAddedSale=false;">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30 table-center">
                <colgroup>
                    <col class="w-35px">
                    <col class="w-10p"><!--항목명-->
                    <col class="w-11p"><!--매입처-->
                    <col class="w-6p"><!--수량-->
                    <col v-show="isModifyAddedSale" class="w-13p">
                    <col class="w-10p"><!--판매단가-->
                    <col v-show="isModifyAddedSale" class="w-13p">
                    <col class="w-10p"><!--매입단가-->
                    <col class="w-10p"><!--판매금액-->
                    <col class="w-10p"><!--매입금액-->
                    <col class="w-14p"><!--비고-->
                    <col class="w-25px">
                </colgroup>
                <tbody>
                <tr>
                    <th>번호</th>
                    <th>항목명</th>
                    <th>매입처</th>
                    <th>수량</th>
                    <th v-show="isModifyAddedSale">판매단가입력</th>
                    <th>판매단가</th>
                    <th v-show="isModifyAddedSale">매입단가입력</th>
                    <th>매입단가</th>
                    <th class="text-danger">판매금액</th>
                    <th class="sl-blue">매입금액</th>
                    <th>비고</th>
                    <th>삭제</th>
                </tr>
                <tr v-if="0 >= addedSaleList.length">
                    <td colspan="99">부가 판매/구매 정보 없음</td>
                </tr>
                <tr v-for="(val, key) in addedSaleList">
                    <td>
                        <span v-if="val.sno==0">신규</span>
                        <span v-else>{% key+1 %}</span>
                    </td>
                    <td>
                        <?php $model="val.addedName"; $placeholder='항목명'; $modifyKey='isModifyAddedSale';  ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                    <td>
                        <span v-if="isModifyAddedSale==true">
                            <select v-model="val.buyManagerSno" @change="if (event.target.options[event.target.selectedIndex].value == -1) val.buyManagerSnoHan=''; else val.buyManagerSnoHan=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                <option value="0">없음</option>
                                <option value="-1">▶ 직접입력</option>
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                            <input type="text" v-show="val.buyManagerSno==-1" class="form-control mgt3" v-model="val.buyManagerSnoHan" placeholder="매입처명"   />
                        </span>
                        <span v-else>{% val.buyManagerSnoHan %}</span>
                    </td>
                    <td>
                        <?php $model="val.addedQty"; $placeholder='수량';$suffixText='ea';   ?>
                        <?php include './admin/ims/template/basic_view/_number.php'?>
                    </td>
                    <td v-show="isModifyAddedSale">
                        <div class="dp-flex">
                            <label class="radio-inline"><input type="radio" :name="'saleVatYn1_'+key" value="y" v-model="val.typingSaleAmtType" @change="calc_not_vat_amt(1, key);" />VAT포함</label>
                            <label class="radio-inline"><input type="radio" :name="'saleVatYn1_'+key" value="n" v-model="val.typingSaleAmtType" @change="calc_not_vat_amt(1, key);" />VAT미포함</label>
                        </div>
                        <input type="number" class="form-control mgt3" v-model="val.typingSaleAmt" @keyup="calc_not_vat_amt(1, key);" placeholder="금액입력" />
                    </td>
                    <td>
                        <div class="text-danger font-bold font-13">{% $.setNumberFormat(val.addedSaleAmount) %}원</div>
                        <div class="font-10 text-muted">자동계산</div>
                    </td>
                    <td v-show="isModifyAddedSale">
                        <div class="dp-flex">
                            <label class="radio-inline"><input type="radio" :name="'buyVatYn1_'+key" value="y" v-model="val.typingBuyAmtType" @change="calc_not_vat_amt(2, key);" />VAT포함</label>
                            <label class="radio-inline"><input type="radio" :name="'buyVatYn1_'+key" value="n" v-model="val.typingBuyAmtType" @change="calc_not_vat_amt(2, key);" />VAT미포함</label>
                        </div>
                        <input type="number" class="form-control mgt3" v-model="val.typingBuyAmt" @keyup="calc_not_vat_amt(2, key);" placeholder="금액입력" />
                    </td>
                    <td>
                        <div class="sl-blue font-bold font-13">{% $.setNumberFormat(val.addedBuyAmount) %}원</div>
                        <div class="font-10 text-muted">자동계산</div>
                    </td>
                    <td class="text-danger">{% $.setNumberFormat(val.addedSaleAmount * val.addedQty) %}원</td>
                    <td class="sl-blue">{% $.setNumberFormat(val.addedBuyAmount * val.addedQty) %}원</td>
                    <td>
                        <?php $model="val.addedDesc"; $placeholder='비고';  ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                    <td>
                        <span class="btn btn-sm btn-white hover-btn cursor-pointer" @click="if (val.sno == 0) addedSaleList.splice(key,1); else deleteAddedBS(val.sno);">삭제</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- 회계 전달 메세지 -->
<div class="col-xs-12 mgt20">

    <!--부가 구매-->
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left sl-blue">
                부가 구매 정보
                <span class="font-12 font-black font-normal noto" style="font-weight:normal !important;">
                    고객 미청구 대상 프로젝트 부가 발생 비용 (VAT제외 가격으로 입력하세요!)
                </span>
            </div>
            <div class="flo-right">
                <div class="btn btn-white btn-sm" @click="addAddedBuy();">+ 부가 구매 추가</div>
                <button type="button" class="btn btn-red btn-sm" v-show="!isModifyAddedBuy" @click="vueApp.isModifyAddedBuy=true;">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModifyAddedBuy" @click="save_added_buy()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModifyAddedBuy" @click="vueApp.isModifyAddedBuy=false;">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30 table-center table-pd-3">
                <colgroup>
                    <col style="width:30px;">
                    <col class=""><!--항목명-->
                    <col class="w-17p"><!--매입처-->
                    <col class="w-10p"><!--수량-->
                    <col v-show="isModifyAddedBuy" class="w-21p"><!--입력금액(VAT계산용)-->
                    <col class="w-11p"><!--매입단가(VAT계산 or 미계산된 금액(저장됨))-->
                    <col class="w-13p"><!--매입금액-->
                    <!--<col class="w-19p">비고-->
                    <col class="w-50px">
                </colgroup>
                <tbody>
                <tr>
                    <th>번호</th>
                    <th>항목명</th>
                    <th>매입처</th>
                    <th>수량</th>
                    <th v-show="isModifyAddedBuy">금액입력</th>
                    <th>매입단가</th>
                    <th>매입금액</th>
<!--                    <th>비고</th>-->
                    <th>삭제</th>
                </tr>
                <tr v-if="0 >= addedBuyList.length">
                    <td colspan="99">부가 구매 정보 없음</td>
                </tr>
                <tr v-for="(val, key) in addedBuyList">
                    <td>
                        <span v-if="val.sno==0">신규</span>
                        <span v-else>{% key+1 %}</span>
                    </td>
                    <td>
                        <?php $model="val.addedName"; $placeholder='항목명'; $modifyKey='isModifyAddedBuy';  ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                    <td>
                        <span v-if="isModifyAddedBuy==true">
                            <select v-model="val.buyManagerSno" @change="if (event.target.options[event.target.selectedIndex].value == -1) val.buyManagerSnoHan=''; else val.buyManagerSnoHan=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                <option value="0">없음</option>
                                <option value="-1">▶ 직접입력</option>
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                            <input type="text" v-show="val.buyManagerSno==-1" class="form-control mgt2" v-model="val.buyManagerSnoHan" placeholder="매입처명" />
                        </span>
                        <span v-else>{% val.buyManagerSnoHan %}</span>
                    </td>
                    <td>
                        <?php $model="val.addedQty"; $placeholder='수량'; $suffixText='ea'; ?>
                        <?php include './admin/ims/template/basic_view/_number.php'?>
                    </td>
                    <td v-show="isModifyAddedBuy">
                        <div class="dp-flex">
                            <label class="radio-inline"><input type="radio" :name="'buyVatYn2_'+key" value="y" v-model="val.typingBuyAmtType" @change="calc_not_vat_amt(3, key);" />VAT포함</label>
                            <label class="radio-inline"><input type="radio" :name="'buyVatYn2_'+key" value="n" v-model="val.typingBuyAmtType" @change="calc_not_vat_amt(3, key);" />VAT미포함</label>
                        </div>
                        <input type="number" class="form-control mgt3" v-model="val.typingBuyAmt" @keyup="calc_not_vat_amt(3, key);" placeholder="금액입력" />
                    </td>
                    <td>
                        <div class="sl-blue font-bold font-13">{% $.setNumberFormat(val.addedBuyAmount) %}원</div>
                        <div class="font-10 text-muted">자동계산</div>
                    </td>
                    <td class="sl-blue">{% $.setNumberFormat(val.addedBuyAmount * val.addedQty) %}원</td>
<!--                    <td>-->
<!--                        --><?php //$model="val.addedDesc"; $placeholder='비고';  ?>
<!--                        --><?php //include './admin/ims/template/basic_view/_text.php'?>
<!--                    </td>-->
                    <td class="">
                       <span class="btn btn-sm btn-white hover-btn cursor-pointer" @click="if (val.sno == 0) addedBuyList.splice(key,1); else deleteAddedBS(val.sno);">삭제</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left ">회계 담당자 전달 메세지</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height40 table-th-height40" >
                <tbody>
                <tr>
                    <td style="padding:7px 0">
                        <?php $model='project.accountingMessage'; $placeholder='회계 담당자 전달 메세지'; $modifyKey='isModify' ?>
                        <div v-show="isModify">
                            <textarea class="form-control" rows="6" v-model="project.accountingMessage" placeholder="회계 담당자 전달 메세지"></textarea>
                        </div>
                        <div v-show="!isModify" >
                            <div v-if="!$.isEmpty(project.accountingMessage)" v-html="project.accountingMessageBr"></div>
                            <div v-else class="text-muted">
                                없음
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 고객사 정보 -->
<div class="col-xs-12 mgt20" v-if="!$.isEmpty(customer.addedInfo)">
    <!--고객사 정보-->
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">고객사 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height40 table-th-height40" >
                <colgroup>
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>고객명</th>
                    <td colspan="3">
                        <div class="dp-flex">
                            <?php $model='customer.customerName'; $placeholder='고객명'; $modifyKey='isModify' ?>
                            <?php include 'template/basic_view/_text.php'?>
                            <div class="btn btn-white btn-sm mgl5" @click="openCustomer(customer.sno)">상세</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th >업종</th>
                    <td colspan="3" >
                        <?php $model='customer.industry'; $placeholder='업종'; $modifyKey='isModify' ?>
                        <?php include 'template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th >근무환경</th>
                    <td colspan="3">
                        <?php $model='customer.addedInfo.etc1'; $placeholder='근무환경' ?>
                        <?php include 'template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th >사원수</th>
                    <td colspan="3">
                        <?php $model='customer.addedInfo.etc2'; $placeholder='사원수' ?>
                        <?php include 'template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th >담당자</th>
                    <td >
                        <div class="dp-flex">
                            <?php $model='customer.contactName'; $placeholder='담당자명' ?>
                            <?php include 'template/basic_view/_text.php'?>

                            <div v-show="isModify">
                                <input type="text" class="form-control" v-model="customer.contactPosition" placeholder="직함">
                            </div>
                            <div v-show="!isModify" >
                                <div v-if="!$.isEmpty(customer.contactPosition)">
                                    {% customer.contactPosition %}
                                </div>
                            </div>
                            <span class="hover-btn cursor-pointer sl-blue" v-show="!isModify && cntCustomerContact > 0" @click="openCommonPopup('customer_contact', 840, 710, {sno:customer.sno});"> 포함 {% cntCustomerContact %}명</span>
                        </div>
                    </td>
                    <th>연락처</th>
                    <td >
                        <div class="dp-flex dp-flex-gap5">
                            <?php $model='customer.contactMobile'; $placeholder='휴대전화' ?>
                            <?php include 'template/basic_view/_text.php'?>
                            /
                            <?php $model='customer.contactEmail'; $placeholder='이메일' ?>
                            <?php include 'template/basic_view/_text.php'?>
                        </div>
                    </td>
                </tr>
                <tr>
                </tbody>
            </table>
        </div>
    </div>

    <!--고객사 안내/승인-->
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">고객사 안내/승인</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height40 table-th-height40">
                <colgroup>
                    <col class="w-150px">
                    <col>
                    <col>
                    <col class="w-200px">
                    <col class="w-110px">
                </colgroup>
                <tbody>
                <!-- #### 제안 ### -->
                <tr v-if="'n'===project.isReorderType && $.isEmpty(project.txProposal) && 0 >= project.stProposal">
                    <th >제안서</th>
                    <td class="text-muted" colspan="99">
                        제안서 등록 전
                    </td>
                </tr>
                <tr v-if="'n'===project.isReorderType && $.isEmpty(project.txProposal) && project.stProposal > 0 ">
                    <th >제안서</th>
                    <td v-if="project.stProposal != 9">
                        예정일:{% $.formatShortDate(project.exProposal) %}
                    </td>
                    <td v-if="project.stProposal != 9">
                        승인일:{% $.formatShortDate(project.cpProposal) %}
                        <div class="btn btn-sm btn-white" v-if="10 == project.stProposal"
                             @click="openApprovalView(projectApprovalInfo['proposal'].sno)" class="cursor-pointer hover-btn btn btn-white btn-sm">
                            결재정보
                        </div>
                    </td>
                    <td :class="project.proposalColor" v-if="project.stProposal != 9">
                        {% project.proposalStatus %}
                    </td>
                    <td v-if="project.stProposal != 9">
                        <div class="btn btn-white btn-sm"
                             v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                             @click="openUrl('proposal'+project.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                            보기
                        </div>
                        <div class="btn btn-white btn-sm" disabled
                             v-if="$.isEmpty(fileList.fileProposal) || 0 >= fileList.fileProposal.files.length">
                            보기
                        </div>
                        <span class="btn btn-white btn-sm" @click="openCommonPopup('send_mail_history', 850, 750, {'sno':project.sno, 'historyDiv':'제안서'});">이력</span>
                    </td>
                    <td :class="project.proposalColor" v-if="project.stProposal == 9" colspan="99">
                        {% project.proposalStatus %}
                        <span class="font-11 text-black">(사유:{% project.proposalMemo %})</span>
                    </td>
                </tr>
                <tr v-if="'n'===project.isReorderType && !$.isEmpty(project.txProposal)">
                    <th>제안서</th>
                    <td colspan="99" class="bg-light-gray">
                        {% project.txProposal %}
                    </td>
                </tr>
                <tr v-if="'y'===project.isReorderType">
                    <th>제안서</th>
                    <td colspan="99" class="bg-light-gray">
                        {% project.projectTypeKr %} 해당 없음
                    </td>
                </tr>
                <!-- #### 샘플 ### -->
                <tr v-if="'n'===project.isReorderType && $.isEmpty(project.txSampleInform) ">
                    <th>샘플</th>
                    <td ><!--발송일-->
                        <div v-if="!$.isEmpty(project.cpSampleInform) && '0000-00-00' !== project.cpSampleInform">
                            발송일:{% $.formatShortDateWithoutWeek(project.cpSampleInform) %}
                        </div>
                        <div v-else>
                            발송예정:{% $.formatShortDateWithoutWeek(project.exSampleInform) %}
                        </div>
                    </td>
                    <td ><!--확정일-->
                        확정일:{% $.formatShortDateWithoutWeek(project.cpSampleConfirm) %}
                    </td>
                    <td ><!--상태-->
                        <div v-if="!$.isEmpty(project.cpSampleConfirm) && '0000-00-00' !== project.cpSampleConfirm" class="text-green">
                            샘플확정
                        </div>
                        <div v-else-if="!$.isEmpty(project.cpSampleInform) && '0000-00-00' !== project.cpSampleInform" class="sl-blue">
                            발송완료
                        </div>
                        <div v-else-if="!$.isEmpty(project.cpSampleOrder) && '0000-00-00' !== project.cpSampleOrder" class="sl-blue">
                            샘플작업중
                        </div>
                        <div v-else>
                            작업대기
                        </div>
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm" @click="openSampleListPopup()">보기</div>
                        <!-- 레이어 팝업 project-sample-list -->
                        <ims-modal :visible.sync="visibleSamplePopup" max-width="1650px" title="샘플 목록">
                            <div id="popup-table-container" style="overflow-y: auto; max-height: 650px !important; /* 가로 스크롤 자동 생성 */">
                            </div>
                            <template #footer>
                                <div class="btn btn-white mgt5" @click="visibleSamplePopup=false">닫기</div>
                            </template>
                        </ims-modal>
                    </td>
                </tr>
                <tr v-if="'n'===project.isReorderType && !$.isEmpty(project.txSampleInform)">
                    <th>샘플</th>
                    <td colspan="99" class="bg-light-gray">
                        {% project.txSampleInform %}
                    </td>
                </tr>
                <tr v-if="'y'===project.isReorderType">
                    <th>샘플</th>
                    <td colspan="99" class="bg-light-gray">
                        {% project.projectTypeKr %} 해당 없음
                    </td>
                </tr>
                <!-- ### 고객 견적서 ### -->
                <tr>
                    <th>판매가</th>
                    <td >
                        최근견적:
                        <span v-if="customerEstimateList.length > 0">
                            {% $.formatShortDateWithoutWeek(customerEstimateList[customerEstimateList.length-1].regDt) %}
                        </span>
                        <span v-else>
                            -
                        </span>
                    </td>
                    <td >
                        상태:
                        <span :class="$.getProcColor(project.priceStatus)">
                            {% project.priceStatusKr %}
                        </span>
                    </td>
                    <td>
                        <div class="btn btn-sm btn-red btn-red-line2" v-if="0 >= projectApprovalInfo.salePrice.sno || $.isEmpty(projectApprovalInfo.salePrice.sno)" @click="openApprovalWrite(customer.sno, project.sno, 'salePrice')">
                            결재요청
                        </div>
                        <div >
                            <div class="dp-flex">
                                <approval-template3
                                        :project="project"
                                        :approval="projectApprovalInfo"
                                        :confirm-type="'salePrice'"
                                        :confirm-field="'prdPriceApproval'"
                                        :memo-field="'unused'"
                                ></approval-template3>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm"
                             @click="changeStyleTab('estimate');scrollToAndHighlight('btn-customer-estimate')">
                            이력({% customerEstimateList.length %})
                        </div>
                    </td>
                </tr>
                <tr v-if="!$.isEmpty(project.assortSendDt) && '0000-00-00 00:00:00' != project.assortSendDt">
                    <th>아소트</th>
                    <td >
                        요청일:{% $.formatShortDateWithoutWeek(project.assortSendDt) %}
                    </td>
                    <td >
                        고객입력일:{% $.formatShortDateWithoutWeek(project.assortCustomerDt) %}
                        <span class="font-11" v-if="!$.isEmpty(project.assortReceiver)">(입력: {% project.assortReceiver %})</span>
                    </td>
                    <td >
                        최종확정:{% $.formatShortDateWithoutWeek(project.assortManagerDt) %}
                        <span class="font-11">{% project.assortApprovalManager %}</span>
                    </td>
                    <td>
                        <div class=" btn btn-sm btn-white" v-if="'!$.isEmpty(project.assortSendDt) && 0000-00-00' !== project.assortSendDt"
                             @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
                            확인
                        </div>
                        <div class=" btn btn-sm btn-white" v-if="'$.isEmpty(project.assortSendDt) || 0000-00-00' === project.assortSendDt">
                            확인
                        </div>
                        <span class="btn btn-white btn-sm" @click="openCommonPopup('send_mail_history', 850, 750, {'sno':project.sno, 'historyDiv':'아소트'});">이력</span>
                    </td>
                </tr>
                <tr v-if="$.isEmpty(project.assortSendDt) || '0000-00-00 00:00:00' == project.assortSendDt">
                    <th>아소트</th>
                    <td colspan="99" v-if="!$.isEmpty(project.assortManagerDt) && '0000-00-00 00:00:00' != project.assortManagerDt">
                        <!--요청 없이 자동 승인시-->
                        (담당자 대리 입력) 최종확정:{% $.formatShortDateWithoutWeek(project.assortManagerDt) %}
                        <span class="font-11">{% project.assortApprovalManager %}</span>
                    </td>
                    <td colspan="99" class="text-muted" v-else>
                        고객 입력 요청 전
                    </td>
                </tr>
                <tr v-if="$.isEmpty(project.customerOrderSendDt) || '0000-00-00 00:00:00' === project.customerOrderSendDt">
                    <th>사양서</th>
                    <td colspan="99" class="text-muted">사양서 발송 전</td>
                </tr>
                <tr v-if="!$.isEmpty(project.customerOrderSendDt) && '0000-00-00 00:00:00' !== project.customerOrderSendDt">
                    <th>사양서</th>
                    <td >
                        발송일:{% $.formatShortDateWithoutWeek(project.customerOrderSendDt) %}
                    </td>
                    <td >
                        수신:{% project.customerOrderReceiver %}
                        <span class="font-10 text-muted">{% project.customerOrderEmail %}</span>
                    </td>
                    <td >
                        확정일:{% $.formatShortDateWithoutWeek(project.customerOrderConfirmDt) %}
                        <span v-show="!$.isEmpty(project.customerOrderConfirmDt)">
                            <span class="font-11">{% project.customerOrderReceiver %}</span>
                        </span>
                    </td>
                    <td>
                        <div class=" btn btn-sm btn-white" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)" v-if="4 > project.stOrder">
                            보기
                        </div>
                        <div class=" btn btn-sm btn-white" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)" v-if="project.stOrder >= 4">
                            보기
                        </div>
                        <span class="btn btn-white btn-sm" @click="openCommonPopup('send_mail_history', 850, 750, {'sno':project.sno, 'historyDiv':'사양서'});">이력</span>
                    </td>
                </tr>
                <!--<tr>
                    <th>가발주</th>
                    <td >
                        요청일:25/03/10
                    </td>
                    <td >
                        승인일:25/03/10
                    </td>
                    <td >
                        승인완료
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm">확인</div>
                    </td>
                </tr>-->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 기획 / 제안 정보 -->
<div class="col-xs-12 mgt20" v-if="!$.isEmpty(customer.addedInfo) && !$.isEmpty(project.addedInfo)">
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">기획 정보</div>
            <div class="flo-right">

                <div class="btn btn-sm btn-white " @click="openSalesView(project.sno)">영업기획서</div>

                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a></div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30">
                <colgroup>
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>업체선정 방법</th>
                    <td colspan="3">
                        <?php $radioKey='customer.addedInfo.info125'; $textKey='customer.addedInfo.info126'; $listCode='prjInfo02'; $placeHolder='업체선정 기준' ?>
                        <?php include 'template/basic_view/_radio_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>업체 선정 기준</th>
                    <td colspan="3">
                        <?php $radioKey='customer.addedInfo.info127'; $textKey='customer.addedInfo.info128'; $listCode='prjInfo01'; $placeHolder='업체선정 방법' ?>
                        <?php include 'template/basic_view/_radio_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>변경사유</th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            {% project.addedInfo.etc31.join(', ') %}
                        </div>
                        <div v-show="isModify">
                            <?php foreach (ImsCodeMap::PRJ_INFO_03 as $recommendKey => $recommendValue) { ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="<?=$recommendValue?>" v-model="project.addedInfo.etc31">
                                    <?=$recommendValue?>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>제안형태</th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            <?php foreach (ImsCodeMap::RECOMMEND_TYPE as $recommendKey => $recommendValue) { ?>
                                <label class="mgr10" v-if="project.recommendList.includes('<?=$recommendKey?>')">
                                    <?=$recommendValue?><span class="ims-recommend ims-recommend<?=$recommendKey?>"><?=mb_substr($recommendValue, 0, 1, 'UTF-8')?></span>
                                </label>
                            <?php } ?>
                        </div>
                        <div v-show="isModify">
                            <?php foreach (ImsCodeMap::RECOMMEND_TYPE as $recommendKey => $recommendValue) { ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="<?=$recommendKey?>" v-model="project.recommendList" @change="setRecommend()">
                                    <?=$recommendValue?><span class="ims-recommend ims-recommend<?=$recommendKey?>"><?=mb_substr($recommendValue, 0, 1, 'UTF-8')?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>제안내용</th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            <div v-if="$.isEmpty(project.addedInfo.etc21) || 0 >= project.addedInfo.etc21.length" class="text-muted">
                                미정
                            </div>
                            <div v-else>
                                {% project.addedInfo.etc21.join(', ') %}
                            </div>

                        </div>
                        <div v-show="isModify">
                            <?php foreach (ImsCodeMap::RECOMMEND_CONTENTS as $recommendKey => $recommendValue) { ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="<?=$recommendValue?>" v-model="project.addedInfo.etc21">
                                    <?=$recommendValue?>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>샘플비용</th>
                    <td colspan="3">
                        <?php $model = 'project.addedInfo.etc34'; $listCode = 'prjInfo05'; $listIndexData=''?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr>
                    <th>색상 민감도</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info009'; $listCode = 'ratingType'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                    <th>품질 민감도</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info010'; $listCode = 'ratingType'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr>
                    <th>단가 민감도</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info011'; $listCode = 'ratingType'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                    <th>납기 민감도</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info012'; $listCode = 'ratingType'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr v-if="!$.isEmpty(customer.contactMemo)">
                    <th>민감도 기타</th>
                    <td colspan="99">
                        <?php $model='customer.contactMemo'; $placeholder='민감도 기타' ?>
                        <?php include 'template/basic_view/_text.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">서비스 정보</div>
            <div class="flo-right">
                <div class="btn btn-sm btn-white " @click="openSalesView(project.sno)">영업기획서</div>
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30">
                <colgroup>
                    <col class="w-150px">
                    <col>
                    <col>
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>안전 재고 생산</th>
                    <td colspan="99">
                        <div class="dp-flex">
                            <?php $model = 'customer.addedInfo.info044'; $listCode = 'processType'?>
                            <?php include 'template/basic_view/_radio.php'?>
                            <div class="dp-flex mgl5">
                                <div v-show="isModify" class="dp-flex">
                                    안전재고 비율 :
                                    <input type="text" class="form-control" v-model="customer.addedInfo.info027" placeholder="" style="width:50px">%
                                </div>
                                <div v-show="!isModify" >
                                    <div v-if="!$.isEmpty(customer.addedInfo.info027) && 'y' === customer.addedInfo.info044 ">
                                        ({% customer.addedInfo.info027 %}%)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>원부자재 비축</th>
                    <td colspan="99">
                        <div class="dp-flex">
                            <?php $model = 'customer.addedInfo.info039'; $listCode = 'processType'?>
                            <?php include 'template/basic_view/_radio.php'?>
                            <div class="dp-flex mgl5">
                                <div v-show="isModify" class="dp-flex">
                                    비축 비율 :
                                    <input type="text" class="form-control" v-model="customer.addedInfo.info040" placeholder="" style="width:50px">%
                                </div>
                                <div v-show="!isModify" >
                                    <div v-if="!$.isEmpty(customer.addedInfo.info040) && 'y' === customer.addedInfo.info039 ">
                                        ({% customer.addedInfo.info040 %}%)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>명찰 정보(사용시)</th>
                    <td colspan="99">
                        <?php $modifyKey='isModify'; $model='customer.addedInfo.info118'; $placeholder='명찰 정보' ?>
                        <?php include 'template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>분류 패킹</th>
                    <td colspan="99">
                        <div v-show="!isModify">
                            {% getCodeMap()['processType'][project.packingYn] %}
                            <span v-show="'y' === project.packingYn">
                                        ( 비용:{% getCodeMap()['existType2'][project.addedInfo.etc35] %}
                                        <span v-show="!$.isEmpty(project.addedInfo.etc36)"> / {% project.addedInfo.etc36 %}</span>)
                                    </span>
                        </div>
                        <div v-show="isModify">
                            <div class="" >
                                <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['processType']">
                                    <input type="radio" :name="'project-added-info-packingYn'"  :value="eachKey" v-model="project.packingYn"  />
                                    <span class="font-12">{%eachValue%}</span>
                                </label>
                            </div>
                            <div class="mgt5 " v-show="'y' === project.packingYn">
                                <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['existType2']">
                                    <input type="radio" :name="'project-added-info-etc35'"  :value="eachKey" v-model="project.addedInfo.etc35"  />
                                    <span class="font-12">{%eachValue%}</span>
                                </label>
                                <div class="dp-flex mgt5">
                                    분류패킹 정보:<input type="text" class="form-control w-80p" v-model="project.addedInfo.etc36"  placeholder="분류패킹 기타 정보(예:0개지사)" >
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>계약 종료 재고 처리</th>
                    <td colspan="99">
                        <?php $model='customer.addedInfo.info124'; $placeholder='계약 종료 재고 처리' ?>
                        <?php include 'template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th class="sl-blue">3PL</th>
                    <td >
                        <div v-show="!isModify">
                            {% project.use3plKr %}
                        </div>
                        <div v-show="isModify">
                            <label class="radio-inline font-11">
                                <input type="radio" name="use3pl" value="n"  v-model="project.use3pl" />사용안함
                            </label>
                            <label class="radio-inline font-11">
                                <input type="radio" name="use3pl" value="y"  v-model="project.use3pl" />사용
                            </label>
                        </div>
                    </td>
                    <th class="sl-blue">폐쇄몰</th>
                    <td >
                        <div v-show="!isModify">
                            {% project.useMallKr %}
                        </div>
                        <div v-show="isModify">
                            <label class="radio-inline font-11">
                                <input type="radio" name="useMall" value="n"  v-model="project.useMall" />사용안함
                            </label>
                            <label class="radio-inline font-11">
                                <input type="radio" name="useMall" value="y"  v-model="project.useMall" />사용
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="cust-mark">입고비</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info119'; $listCode = 'existType2'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                    <th class="cust-mark">보관비</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info120'; $listCode = 'existType2'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr>
                    <th class="cust-mark">출고비</th>
                    <td >
                        <?php $model = 'customer.addedInfo.info121'; $listCode = 'existType2'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                    <th class="">직접납품 여부</th>
                    <td >
                        <?php $model = 'project.directDeliveryYn'; $listCode = 'processType'?>
                        <?php include 'template/basic_view/_radio.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!--발주정보-->
<div class="col-xs-12 mgt20" >
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">발주 기본 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30">
                <colgroup>
                    <col class="width-xs">
                    <col class="width-md">
                    <col class="width-xs">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr>
                    <th>고객납기</th>
                    <td >
                        <div v-if="2 == project.productionStatus || 91 == project.productionStatus" class="sl-green mgt5">
                            <span class="font-14">{% $.formatShortDateWithoutWeek(project.customerDeliveryDt) %}</span>
                            납기완료
                        </div>
                        <div v-else>
                            <?php $model='project.customerDeliveryDt';?>
                            <?php include 'template/basic_view/_picker2.php'?>

                            <div v-if="!isModify" class="">
                                <div class="text-danger " v-if="'y' !== project.customerDeliveryDtConfirmed">변경불가</div>
                                <div class="sl-blue " v-if="'n' !== project.customerDeliveryDtConfirmed">변경가능</div>
                            </div>
                            <div v-if="isModify" class="dp-flex">
                                <div class="">
                                    <label class="radio-inline">
                                        <input type="radio" name="order_deliveryConfirm"  value="y" v-model="project.customerDeliveryDtConfirmed"/>변경가능
                                    </label>
                                </div>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="order_deliveryConfirm"  value="n" v-model="project.customerDeliveryDtConfirmed"/>변경불가
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <th rowspan="2">
                        납기일 연동 여부
                    </th>
                    <td rowspan="2">
                        <div v-show="!isModify">
                            <div v-show="'y' === project.syncProduct">
                                납기일자 프로젝트로 한번에 관리
                                <div class="notice-info">상품 납기일이 프로젝트 수정 시 해당 납기일로 변경됩니다.</div>
                            </div>
                            <div v-show="'n' === project.syncProduct">
                                납기일자 상품별 관리
                                <div class="notice-info">프로젝트 납기일과 상관없이 상품별 관리 합니다.</div>
                            </div>
                        </div>
                        <div v-show="isModify">
                            <label class="radio-inline" style="font-weight: normal;font-size:12px">
                                <input type="radio" name="orderSyncProduct"  value="y" v-model="project.syncProduct"/> 납기일자 프로젝트로 한번에 관리
                            </label>
                            <br>
                            <label class="radio-inline" style="font-weight: normal;font-size:12px">
                                <input type="radio" name="orderSyncProduct"  value="n" v-model="project.syncProduct"/> 납기일자 상품별 관리
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>이노버납기</th>
                    <td >
                        <div v-if="2 == project.productionStatus || 91 == project.productionStatus" class="sl-green mgt5">
                            납기완료
                        </div>
                        <div v-else>
                            <?php $model='project.msDeliveryDt';?>
                            <?php include 'template/basic_view/_picker2.php'?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>발주D/L</th>
                    <td colspan="3">

                        <div v-show="!isModify">
                            <!--완료일-->
                            <div v-if="'0000-00-00' != project.cpProductionOrder && !$.isEmpty(project.cpProductionOrder)" class="text-muted">
                                    <span class="font-13 sl-green">
                                        {% $.formatShortDateWithoutWeek(project.cpProductionOrder) %} 발주
                                    </span>
                            </div>
                            <!--대체텍스트-->
                            <div v-else-if="!$.isEmpty(project.txProductionOrder)">
                                    <span class="font-11">
                                        {% project.txProductionOrder %}
                                    </span>
                            </div>
                            <!--예정일-->
                            <div v-else-if="!$.isEmpty(project.exProductionOrder)" class="">
                                    <span class="font-13">
                                        {% $.formatShortDateWithoutWeek(project.exProductionOrder) %}
                                    </span>
                                <span class="font-11 mgl5" v-html="$.remainDate(project.exProductionOrder,true)"></span>
                            </div>
                            <!--미설정-->
                            <div v-else class="text-muted">미정</div>
                        </div>

                        <div class="dp-flex" v-show="isModify">
                            <!--발주 예정일 수정-->
                            <date-picker v-model="project.exProductionOrder"
                                         class="mini-picker " value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                            </date-picker>
                            <input type="text" placeholder="대체텍스트" class="form-control " v-model="project.txProductionOrder">
                        </div>

                    </td>
                </tr>
                <tr>
                    <th>발주일</th>
                    <td colspan="3">

                        <div v-if="project.productionStatus > 0">
                            <a :href="'../ims/imsProductionList.php?initStatus=0&key=prj.sno&keyword='+project.sno" class="font-14 sl-blue" target="_blank">{% $.formatShortDate(project.cpProductionOrder) %}</a>
                        </div>
                        <div v-else>
                            <div class="btn btn-sm btn-blue" @click="orderToFactory()"
                                 v-if="
                                2 == project.priceStatus
                                && 2 == project.costStatus
                                && 'p' == project.assortApproval
                                && 2 == project.workStatus
                                && 'p' == project.customerOrderConfirm
                                ">
                                발주하기
                            </div>

                            <div class="btn btn-sm btn-gray" @click="visibleOrderCondition=true"
                                 v-if="!(
                                2 == project.priceStatus
                                && 2 == project.costStatus
                                && 'p' == project.assortApproval
                                && 2 == project.workStatus
                                && 'p' == project.customerOrderConfirm
                                )">
                                발주하기(불가)
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        발주조건
                    </th>
                    <td colspan="99" class="pd0">
                        <table class="table-fixed w-100p table-center">
                            <colgroup>
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                                <col class="w-7p">
                            </colgroup>
                            <tr>
                                <th class="bg-light-gray">판매가</th>
                                <td class=""><!--판매가-->
                                    <span v-html="project.priceStatusIcon"></span>
                                </td>
                                <th class="bg-light-gray">생산가</th>
                                <td><!--생산가-->
                                    <span v-html="project.costStatusIcon"></span>
                                </td>
                                <th class="bg-light-gray">아소트</th>
                                <td><!--아소트-->
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == project.assortApproval"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == project.assortApproval"></i>
                                    <span class="text-muted" v-else>-</span>
                                </td>
                                <th class="bg-light-gray">작업지시서</th>
                                <td>
                                    <span v-html="project.workStatusIcon"></span>
                                </td>
                                <th class="bg-light-gray">사양서</th>
                                <td>
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == project.customerOrderConfirm"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == project.customerOrderConfirm"></i>
                                    <span class="text-muted" v-else>-</span>
                                </td>
                                <th class="bg-light-gray">퀄리티</th>
                                <td>
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'2' == project.fabricStatus"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'1' == project.fabricStatus"></i>
                                    <span class="text-muted" v-else>-</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>3PL 여부/바코드 파일</th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            {% project.use3plKr %}
                        </div>
                        <div v-show="isModify">
                            <label class="radio-inline font-11">
                                <input type="radio" name="salesUse3pl" value="n"  v-model="project.use3pl" />사용안함
                            </label>
                            <label class="radio-inline font-11">
                                <input type="radio" name="salesUse3pl" value="y"  v-model="project.use3pl" />사용
                            </label>
                        </div>

                        <div v-show="'y' === project.use3pl" >
                            <simple-file-upload :file="fileList.fileBarcode" :id="'fileBarcode'" :project="project" ></simple-file-upload>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>분류패킹 여부/파일</th>
                    <td colspan="3">
                        <?php $model = 'project.packingYn'; $listCode = 'processType'; $modelPrefix='order'; $listIndexData="";?>
                        <?php include 'template/basic_view/_radio.php'?>
                        <div v-show="'y' === project.packingYn" >
                            <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="project" ></simple-file-upload>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">발주/납품 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a></div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30">
                <colgroup>
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>대표 생산처</th>
                    <td colspan="3">

                        <div v-show="!isModify">
                            <div v-if="!$.isEmpty(project.mainFactoryName)">{% project.mainFactoryName %}</div>
                            <div v-else>미정</div>
                        </div>

                        <select class="form-control" style="width:30%" v-model="project.produceCompanySno" v-show="isModify">
                            <option value="0">미정</option>
                            <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>

                        <div class="notice-info">대표생산처 지정시 스타일별 지정된 스타일이 없을 경우 대표생산처로 자동 지정</div>

                    </td>
                </tr>
                <tr>
                    <th>생산처 형태/국가</th>
                    <td colspan="3">

                        <div class="form-inline" v-show="!isModify">
                            {% project.produceTypeKr %}
                            {% project.produceNational %}
                        </div>

                        <div class="form-inline" v-show="isModify">
                            <select class="form-control " v-model="project.produceType">
                                <?php foreach ($prdType as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" v-model="project.produceNational" placeholder="선택">
                                <option value="">미정</option>
                                <?php foreach ($prdNational as $key => $value ) { ?>
                                    <option value="<?=$value?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </td>
                </tr>
                <tr>
                    <th>납품계획 메모</th>
                    <td colspan="99">
                        <div class="dp-flex dp-flex-gap10">
                            <div class="bg-light-gray2 round-box w-55p " style="height:100px" v-show="!isModify" v-html="$.nl2br(project.deliveryMethod)">
                            </div>
                            <textarea class="form-control w50 inline-block flo-left" rows="4" v-model="project.deliveryMethod" placeholder="납품 계획/방법 메모" v-show="isModify"></textarea>

                            <div >
                                <simple-file-upload :file="fileList.fileDeliveryPlan" :id="'fileDeliveryPlan'" :project="project" ></simple-file-upload>
                                <div class="notice-info">납품 계획 파일</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>납품 보고서</th>
                    <td colspan="99">
                        <simple-file-upload :file="fileList.fileDeliveryReport" :id="'fileDeliveryReport'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 기획서/제안서 -->
<div class="col-xs-12 mgt20" v-if="false">
    <div class="col-xs-12" >
        <div id="tabViewDiv">
            <ul class="nav nav-tabs mgb5" role="tablist">
                <li role="presentation" :class="'plan' === fileTabMode?'active':''" @click="changeFileTab('plan')" id="file-tab1" >
                    <a href="#file-tab1" data-toggle="tab" >기획서</a>
                </li>
                <li role="presentation" :class="'proposal' === fileTabMode?'active':''" @click="changeFileTab('proposal')" id="file-tab2" >
                    <a href="#file-tab2" data-toggle="tab" >제안서</a>
                </li>
                <li role="presentation" :class="'designGuide' === fileTabMode?'active':''" @click="changeFileTab('designGuide')" id="file-tab3" >
                    <a href="#file-tab3" data-toggle="tab" >사양서</a>
                </li>
            </ul>
        </div>

        <div style="border:solid 1px #d1d1d1; min-height:900px; width:100%; padding:7px;" v-show="'plan' === fileTabMode" v-if="!$.isEmpty(fileList.filePlan)">
            <iframe :src="'<?=$nasUrl?>' + fileList.filePlan.files[0].filePath" style="width:100%; height:900px;" v-if="fileList.filePlan.files.length > 0"></iframe>
            <div v-else>
                표시할 데이터 없음
            </div>
        </div>

        <div style="border:solid 1px #d1d1d1; min-height:900px; width:100%; padding:7px;" v-show="'proposal' === fileTabMode" v-if="!$.isEmpty(fileList.fileProposal)">
            <iframe :src="'<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath" style="width:100%; height:900px;" v-if="fileList.fileProposal.files.length > 0"></iframe>
            <div v-else>
                표시할 데이터 없음
            </div>

        </div>

        <div style="border:solid 1px #d1d1d1; min-height:900px; width:100%; padding:7px;" v-show="'designGuide' === fileTabMode" v-if="!$.isEmpty(fileList.filePlan) && !$.isEmpty(fileList.fileProposal)">
            <iframe src="<?=$guideUrl?>?key=<?=$projectKey?>" style="width:100%; height:900px;" ></iframe>
        </div>
    </div>
    
</div>

<!--파일-->
<div class="col-xs-12" v-show="!isFactory && false"  v-if="!$.isEmpty(fileList)">
    <div class="col-xs-12" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">영업 파일</div>
            <div class="flo-right"></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        견적서 (파일)
                    </th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileEtc2" :id="'fileEtc2'" :project="project" ></simple-file-upload>
                    </td>
                    <th>영업 확정서 (파일)</th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileEtc4" :id="'fileEtc4'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                <tr>
                    <th>
                        입찰 추가 정보
                    </th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileMeeting" :id="'fileMeeting'" :project="project" ></simple-file-upload>
                    </td>
                    <th>
                        기타파일
                    </th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileEtc7" :id="'fileEtc7'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

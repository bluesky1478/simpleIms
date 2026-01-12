<?php use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
$modelPrefix='main';  ?>

<!--
[ 프로젝트 상세의 인클루드 파일 : 프로젝트 상세 메인 화면 ]
Include 목록.
-->
<div class="col-xs-12" >
    <!--프로젝트 기본 정보-->
    <div class="col-xs-6" >

        <!--프로젝트 기본정보-->
        <section>
            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 기본 정보</div>
                <div class="flo-right">
                    <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                    <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                    <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
                </div>
                <a href="#" target="_blank" class=""></a>
            </div>
            <table class="table table-cols ims-table-style1 table-height30 table-pd-5">
                <colgroup>
                    <col class="w-16p">
                    <col class="w-34p">
                    <col class="w-16p">
                    <col class="w-34p">
                </colgroup>
                <tbody>
                <tr>
                    <th>현재 상태</th>
                    <td>
                        {% mainData.projectStatusKr %} 단계
                        <div class="btn btn-sm btn-white mgl5" @click="openProjectStatusHistory(mainData.sno,'')">단계변경/이력</div>
                    </td>
                    <th>프로젝트 타입</th>
                    <td>
                        <?php
                        $model='mainData.projectType';
                        $modelValue='mainData.projectTypeKr';
                        $listData=\Component\Ims\ImsCodeMap::PROJECT_TYPE;
                        $selectWidth=100
                        ?>
                        <?php include './admin/ims/ims25/component/_select.php'?>
                    </td>
                </tr>
                <tr>
                    <th>연도/시즌</th>
                    <td>
                        <div v-show="!isModify">
                            {% mainData.projectYear %} {% mainData.projectSeason %}
                        </div>
                        <div v-show="isModify">
                            <select v-model="mainData.projectYear" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                                <?php foreach($yearList as $yearEach) {?>
                                    <option><?=$yearEach?></option>
                                <?php }?>
                            </select>
                            <select v-model="mainData.projectSeason" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                                <option >ALL</option>
                                <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                    <option><?=$seasonEn?></option>
                                <?php }?>
                            </select>
                        </div>
                    </td>
                    <th class="font-12">고객납기</th>
                    <td>
                        <div v-if="2 == mainData.productionStatus || 91 == mainData.productionStatus" class="sl-green mgt5">
                            <span class="font-14">{% $.formatShortDateWithoutWeek(mainData.customerDeliveryDt) %}</span>
                            납기완료
                        </div>
                        <div v-else class="">
                            <div v-show="!isModify" class="dp-flex-gap10 dp-flex">
                                <div v-if="$.isEmpty(mainData.customerDeliveryDt)">
                                    <span class="font-11 text-muted">미정</span>
                                </div>
                                <div v-if="!$.isEmpty(mainData.customerDeliveryDt)">
                                    {% $.formatShortDate(mainData.customerDeliveryDt) %}
                                    <span class="font-11 " v-html="$.remainDate(mainData.customerDeliveryDt,true)"></span>
                                </div>

                                <div class="text-danger " v-if="'y' !== mainData.customerDeliveryDtConfirmed">
                                    변경불가
                                </div>
                                <div class="sl-blue " v-else>
                                    변경가능
                                </div>
                            </div>
                            <div v-show="isModify" class="">
                                <date-picker v-model="mainData.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>

                                <div class="">
                                    <label class="radio-inline">
                                        <input type="radio" name="order_deliveryConfirm"  value="y" v-model="mainData.customerDeliveryDtConfirmed"/>변경가능
                                    </label>
                                </div>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="order_deliveryConfirm"  value="n" v-model="mainData.customerDeliveryDtConfirmed"/>변경불가
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>사업계획</th>
                    <td>
                        <div class="dp-flex dp-flex-gap10" v-show="!isModify">
                            <div>
                                <b>사업계획</b> : {% JS_LIB_CODE['includeType'][mainData.bizPlanYn] %}
                            </div>
                            <div>
                            <span v-show="!$.isEmpty(mainData.targetSalesYear)">
                                <b>목표매출연도</b> : {% mainData.targetSalesYear %}년
                            </span>
                                <span v-show="$.isEmpty(mainData.targetSalesYear)">
                                <b>목표매출연도</b> : <span class="text-muted">미지정</span>
                            </span>
                            </div>
                        </div>
                        <div class="" v-show="isModify">
                            <div class="dp-flex mgt3">
                                사업계획 :
                                <label class="radio-inline mgl5 mgr5" v-for="(eachValue, eachKey) in JS_LIB_CODE['includeTypeSimple']" >
                                    <input type="radio" name="salesBizPlanYn'"  :value="eachKey" v-model="mainData.bizPlanYn" />
                                    <span class="">{%eachValue%}</span>
                                </label>
                            </div>
                            <div class="dp-flex mgt3">
                                목표매출연도 :
                                <select2 v-model="mainData.targetSalesYear" class="form-control form-inline inline-block " style="width:100px;">
                                    <?php foreach($yearFullList as $key => $val) {?>
                                        <option value="<?=$val?>"><?=$key?></option>
                                    <?php }?>
                                </select2>
                            </div>
                        </div>

                    </td>
                    <th>발주D/L</th>
                    <td>
                        <div v-show="!isModify">
                            <!--완료일-->
                            <div v-if="'0000-00-00' != mainData.cpProductionOrder && !$.isEmpty(mainData.cpProductionOrder)" class="text-muted">
                            <span class="font-13 sl-green">
                                {% $.formatShortDateWithoutWeek(mainData.cpProductionOrder) %} 발주
                            </span>
                            </div>
                            <!--대체텍스트-->
                            <div v-else-if="!$.isEmpty(mainData.txProductionOrder)">
                            <span class="font-11">
                                {% mainData.txProductionOrder %}
                            </span>
                            </div>
                            <!--예정일-->
                            <div v-else-if="!$.isEmpty(mainData.exProductionOrder)" class="">
                            <span class="font-13">
                                {% $.formatShortDateWithoutWeek(mainData.exProductionOrder) %}
                            </span>
                                <span class="font-11 mgl5" v-html="$.remainDate(mainData.exProductionOrder,true)"></span>
                            </div>
                            <!--미설정-->
                            <div v-else class="text-muted">미정</div>
                        </div>

                        <div class="dp-flex" v-show="isModify">
                            <!--발주 예정일 수정-->
                            <date-picker v-model="mainData.exProductionOrder"
                                         class="mini-picker " value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                            </date-picker>
                            <input type="text" placeholder="대체텍스트" class="form-control " v-model="mainData.txProductionOrder">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>영업 담당자</th>
                    <td>
                        <div class="" v-show="!isModify">
                            <div v-show="$.isEmpty(mainData.salesManagerNm)">미정</div>
                            <div v-show="!$.isEmpty(mainData.salesManagerNm)">{% mainData.salesManagerNm %}</div>
                        </div>
                        <div class="" v-show="isModify">
                            <select class="form-control w100" v-model="mainData.salesManagerSno" >
                                <option value="0">미정</option>
                                <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <th>진행 구분</th>
                    <td>
                        <div v-show="!isModify">
                            {% JS_LIB_CODE['bidType'][mainData.bidType2] %}
                        </div>
                        <div v-show="isModify">
                            <?php foreach( \Component\Ims\ImsCodeMap::BID_TYPE as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="bidType2" value="<?=$k?>" v-model="mainData.bidType2"  @change="setPrjTypeByBidType()" /><?=$v?>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>디자인 담당자</th>
                    <td>
                        <div class="" v-show="!isModify">
                            <div v-show="$.isEmpty(mainData.designManagerNm)">미정</div>
                            <div v-show="!$.isEmpty(mainData.designManagerNm)">{% mainData.designManagerNm %}</div>
                        </div>
                        <div class="" v-show="isModify">
                            <select class="form-control w100" v-model="mainData.designManagerSno" >
                                <option value="0">미정</option>
                                <?php foreach ($designManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <th>디자인 업무 타입</th>
                    <td>
                        <div class="" v-show="!isModify">
                            {% JS_LIB_CODE['designWorkType'][mainData.designWorkType] %}
                        </div>
                        <div class="" v-show="isModify">
                            <select class="form-control w100" v-model="mainData.designWorkType" >
                                <?php foreach (\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        업무 시작일
                    </th>
                    <td colspan="3">
                        <?php $model='mainData.salesStartDt'; $placeholder='fieldData.title' ?>
                        <?php include './admin/ims/ims25/component/_picker3.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </section>

        <!--결재 승인-->
        <section>
            <div class="table-title gd-help-manual">
                <div class="flo-left">결재/승인/확정</div>
                <div class="flo-right">
                    <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                    <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                    <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
                </div>
                <a href="#" target="_blank" class=""></a>
            </div>
            <table class="table table-cols ims-table-style1 table-height35 table-pd-5">
                <colgroup>
                    <col class="w-16p">
                    <col class="w-34p">
                    <col class="w-16p">
                    <col class="w-34p">
                </colgroup>
                <tbody>
                <tr>
                    <th>영업 기획서</th>
                    <td >

                        <schedule-template :data="mainData" :modify="isModify" :type="'salesPlan'" class="mgb5"></schedule-template>

                        <div class="dp-flex dp-flex-gap10">
                            <div class="btn btn-sm btn-white" @click="openSalesView(mainData.sno)" >영업기획서</div>
                            <!--영업기획서 결재 라인-->
                            <div class="btn btn-sm btn-red btn-red-line2"
                                 v-if=" 'n' === mainData.salesPlanApproval || 0 >= Number(projectApprovalInfo.salesPlan.sno) "
                                 @click="openApprovalWrite(customer.sno, mainData.sno, 'salesPlan')">
                                결재요청
                            </div>
                            <approval-template4
                                    :project="mainData"
                                    :approval="projectApprovalInfo"
                                    :confirm-type="'salesPlan'"
                                    :confirm-field="'salesPlanApproval'"
                                    :memo-field="'unused'"
                            ></approval-template4>
                        </div>
                    </td>
                    <th>생산가</th>
                    <td >
                        <div class="" v-show="projectApprovalInfo.cost.sno >= 0">
                            <div >
                                <span v-html="mainData.costStatusIcon"></span>
                                {% mainData.costStatusKr %}
                            </div>
                            <div class="mgt5">
                                <div class="btn btn-sm btn-red btn-red-line2"
                                     v-if="'n' === mainData.prdCostApproval && ( 0 >= projectApprovalInfo.cost.sno || $.isEmpty(projectApprovalInfo.cost.sno) )"
                                     @click="openApprovalWrite(customer.sno, mainData.sno, 'cost')">
                                    결재요청
                                </div>
                            </div>
                            <div>
                                <approval-template3
                                        :project="mainData"
                                        :approval="projectApprovalInfo"
                                        :confirm-type="'cost'"
                                        :confirm-field="'prdCostApproval'"
                                        :memo-field="'unused'"
                                ></approval-template3>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>디자인 기획서</th>
                    <td class="ta-l">

                        <schedule-template :data="mainData" :modify="isModify" :type="'plan'" class="mgb5"></schedule-template>

                        <!--기획-->
                        <?php $addBtn = true ?>
                        <?php include './admin/ims/ims25/template/_ims25_schedule_func_plan.php'?>
                        <!--업로드-->
                        <div class="w-100p text-left set-dropzone-type1" v-show="isModify">
                            <file-upload :file="fileList.filePlan" :id="'filePlan'" :project="mainData" :accept="'p'===mainData.planConfirm" ></file-upload>
                        </div>
                    </td>
                    <th>판매가</th>
                    <td >
                        <div class="" v-show="projectApprovalInfo.salePrice.sno >= 0">
                            <div >
                                <span v-html="mainData.priceStatusIcon"></span>
                                {% mainData.priceStatusKr %}
                            </div>

                            <div class="mgt5">
                                <div class="btn btn-sm btn-red btn-red-line2" v-if="0 >= projectApprovalInfo.salePrice.sno || $.isEmpty(projectApprovalInfo.salePrice.sno)" @click="openApprovalWrite(customer.sno, mainData.sno, 'salePrice')">
                                    결재요청
                                </div>
                            </div>

                            <div>
                                <approval-template3
                                        :project="mainData"
                                        :approval="projectApprovalInfo"
                                        :confirm-type="'salePrice'"
                                        :confirm-field="'prdPriceApproval'"
                                        :memo-field="'unused'"
                                ></approval-template3>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>제안서(작성)</th>
                    <td >
                        <schedule-template :data="mainData" :modify="isModify" :type="'proposal'" class="mgb5"></schedule-template>
                        <!--기획-->
                        <?php $addBtn = true ?>
                        <?php include './admin/ims/ims25/template/_ims25_schedule_func_proposal.php'?>
                        <!--업로드-->
                        <div class="w-100p text-left set-dropzone-type1" v-show="isModify">
                            <file-upload :file="fileList.fileProposal" :id="'fileProposal'" :project="mainData" :accept="'p'===mainData.proposalConfirm" ></file-upload>
                        </div>
                    </td>
                    <th>아소트(이노버)</th>
                    <td >
                        <div>
                            예정 :
                            <span v-show="!isModify">{% $.formatShortDate(mainData['exAssortConfirm']) %}</span>
                            <span v-show="isModify" class="mini-picker">
                                <date-picker v-model="mainData['exAssortConfirm']" value-type="format" format="YYYY-MM-DD" :editable="false"></date-picker>
                            </span>
                        </div>
                        <div class="dp-flex">
                            상태 :
                            <span v-if="'p'==mainData.assortApproval">
                                <div class="dp-flex text-green">
                                    <i aria-hidden="true" class="fa fa-check-circle"></i>
                                    <span>{% $.formatShortDate(mainData.cpAssortConfirm) %}</span>
                                </div>
                            </span>
                            <span v-else :class="$.getAssortAcceptNameColor(mainData.assortApproval)['color']" >
                                {% $.getAssortAcceptNameColor(mainData.assortApproval)['name'] %}
                            </span>
                        </div>

                        <div class="mgt5">
                            <div v-if="'n' == mainData.assortApproval || 'r' == mainData.assortApproval">
                                고객 아소트 입력 전입니다.
                                <br>고객 입력 수량 확인 후 최종 확정 합니다.
                            </div>
                            <div v-else class="dp-flex">

                                <div class=" btn btn-sm btn-red btn-red-line2 cursor-pointer hover-btn"
                                     v-if="'f'===mainData.assortApproval"
                                     @click="setAssortStatus('p')">
                                    아소트 최종 확정
                                </div>

                                <div class="btn btn-sm btn-white"
                                     v-if="'p'===mainData.assortApproval"
                                     @click="setAssortStatus('f')">
                                    확정취소
                                </div>

                                <div class="" >
                                    <div class=" btn btn-sm btn-white" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
                                        고객 입력화면 보기
                                    </div>
                                </div>

                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="font-12">작지/사양서 제작</th>
                    <td >
                        <!--php include './admin/ims/ims25/template/_ims25_schedule_func_work.php'-->
                        <schedule-template :data="mainData" :modify="isModify" :type="'order'" class="mgb5"></schedule-template>

                        <div>
                            <span class="text-green">{% workOrderCompleteCnt %}</span><span class="font-11 bold" style="color:#b1b1b1">(완료)</span>
                            /
                            <span class="sl-blue">{% productList.length %}</span><span class="font-11 " style="color:#b1b1b1">(스타일)</span>
                        </div>
                    </td>
                    <th class="font-12">Q/B 정보</th>
                    <td >
                        <schedule-template :data="mainData" :modify="isModify" :type="'qb'" class="mgb5"></schedule-template>

                        <div class="pdl5 " >
                            <span :class="$.getProcColor(mainData.fabricStatus) + ' hand hover-btn'" @click="openQb()">
                                Q : {% mainData.fabricStatusKr %}
                            </span>
                            <div class="mgt5"></div>
                            <span :class="$.getProcColor(mainData.btStatus) + ' hand hover-btn'" @click="openQb()">
                                B : {% mainData.btStatusKr %}
                            </span>
                        </div>

                        <!--QB레이어-->
                        <transition name="ims-layer-fade">
                            <div class="ims-layer-dim" v-if="typeof qbLayer != 'undefined' && qbLayer.visible" @click.self="vueApp.qbLayer.visible=false" >
                                <div class="ims-layer-wrap" style="min-width:1500px">
                                    <!--헤더-->
                                    <div class="ims-layer-header">
                                        <div class="ims-layer-title">
                                            <!--<span class="ims-layer-title-sub text-danger">
                                                #번호
                                            </span>-->
                                            <!-- 제목 영역 -->
                                            <span class="ims-layer-title-main">
                                            <span class="">QB 정보/스케쥴 관리</span>
                                        </span>

                                        </div>
                                        <!-- X 버튼 -->
                                        <button class="ims-layer-close-x" @click="vueApp.qbLayer.visible=false">&times;</button>
                                    </div>

                                    <!--바디-->
                                    <div class="ims-layer-body">
                                        <div v-if="qbLayer.loading" class="ims-layer-loading">
                                            불러오는 중...
                                        </div>
                                        <div v-else>

                                            <table class="table ims-schedule-table ims-sub-schedule-table w-100p table-fixed table-default-center table-height35 table-pd-3 font-11">
                                                <colgroup>
                                                    <col class="w-50px"><!--구분-->
                                                    <col ><!--상품명-->
                                                    <col class="w-80px"><!--퀄리티상태-->
                                                    <col class="w-50px"><!--부위-->
                                                    <col><!--원단정보-->
                                                    <col><!--확정 정보-->
                                                    <col class="w-50px"><!--제조국-->
                                                    <col class="w-100px"><!--의뢰처-->
                                                    <col class="w-80px"><!--요청일-->
                                                    <col class="w-80px"><!--완료예정일-->
                                                    <col class="w-90px"><!--BT상태-->
                                                    <col><!--확정 정보-->
                                                    <col class="w-100px"><!--의뢰처-->
                                                    <col class="w-80px"><!--요청일-->
                                                    <col class="w-80px"><!--완료예정일-->
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
                                                        <div class="hover-btn cursor-pointer" @click="openProductReg2(mainData.sno, prd.sno, 1)">
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
                                                        <div :class="'hover-btn cursor-pointer'" @click="openProductWithFabric(mainData.sno, prd.sno, fabric.sno)">
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


                                        </div>
                                    </div>

                                    <!--푸터-->
                                    <div class="ims-layer-footer">
                                        <div class="dp-flex dp-flex-between">
                                            <div>
                                                <ul class="font-weight-bold font-14 dp-flex dp-flex-gap15">
                                                </ul>
                                            </div>
                                            <button class="ims-layer-btn" @click="vueApp.qbLayer.visible=false">
                                                닫기
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </transition>

                    </td>
                </tr>
                </tbody>
            </table>
        </section>

    </div>

    <!--고객 안내/승인-->
    <div class="col-xs-6" >

        <!--고객정보-->
        <section v-if="!$.isEmpty(customer.addedInfo)">
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
                <table class="table table-cols ims-table-style1 table-height30 table-pd-5" >
                    <colgroup>
                        <col class="w-16p">
                        <col class="w-34p">
                        <col class="w-16p">
                        <col class="w-34p">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>고객명</th>
                        <td colspan="3">
                            <div class="dp-flex">
                                <?php $model='customer.customerName'; $placeholder="'고객명'" ?>
                                <?php include './admin/ims/ims25/component/_text.php'?>
                                <div class="btn btn-white btn-sm mgl5" @click="openCustomer(customer.sno)">상세</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>업종</th>
                        <td colspan="3" >
                            <div v-if="isModify" class="dp-flex dp-flex-gap5">
                                <select v-model="parentBizCateName" @change="customer.busiCateSno=0;" class="form-control">
                                    <option v-for="val in parentBizCateList">{% val %}</option>
                                </select>
                                <select v-model="customer.busiCateSno" class="form-control">
                                    <option value="0">선택</option>
                                    <option v-for="val in bizCateList" :value="val.busiCateSno" v-show="val.parentCateName == parentBizCateName">
                                        {% val.cateName %}
                                    </option>
                                </select>
                            </div>
                            <div v-else="">{% customer.busiCateText %}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>근무환경</th>
                        <td colspan="3">
                            <?php $model='customer.addedInfo.etc1'; $placeholder="'근무환경'" ?>
                            <?php include './admin/ims/ims25/component/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>사원수</th>
                        <td colspan="3">
                            <?php $model='customer.addedInfo.etc2'; $placeholder="'사원수'" ?>
                            <?php include './admin/ims/ims25/component/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>담당자</th>
                        <td colspan="3">
                            <div class="dp-flex">
                                <?php $model='customer.contactName'; $placeholder="'담당자명'" ?>
                                <?php include './admin/ims/ims25/component/_text.php'?>

                                <div v-show="isModify">
                                    <input type="text" class="form-control" v-model="customer.contactPosition" placeholder="직함">
                                </div>
                                <div v-show="!isModify" >
                                    <div v-if="!$.isEmpty(customer.contactPosition)">
                                        {% customer.contactPosition %}
                                    </div>
                                </div>
                                <span class="hover-btn cursor-pointer sl-blue"
                                      v-show="!isModify && customerContactList.length > 1"
                                      @click="openCommonPopup('customer_contact', 840, 710, {sno:customer.sno});">
                                    외 {% customerContactList.length-1 %}명
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>연락처</th>
                        <td colspan="3">
                            <div class="dp-flex dp-flex-gap5">
                                <?php $model='customer.contactMobile'; $placeholder="'휴대전화'" ?>
                                <?php include './admin/ims/ims25/component/_text.php'?>
                                /
                                <?php $model='customer.contactEmail'; $placeholder="'이메일'" ?>
                                <?php include './admin/ims/ims25/component/_text.php'?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        
        <!--고객 안내/승인-->
        <section>
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객 안내/승인</div>
                <div class="flo-right">
                    <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                    <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                    <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
                </div>
                <a href="#" target="_blank" class=""></a>
            </div>

            <table class="table table-cols ims-table-style1 table-height65 table-pd-5">
                <colgroup>
                    <col class="w-16p">
                    <col class="w-34p">
                    <col class="w-16p">
                    <col class="w-34p">
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        스케쥴 안내
                    </th>
                    <td>
                        <!--TODO-->
                        안내완료
                        <div class="btn btn-white btn-sm">보기</div>
                    </td>
                    <th>견적서(판매가)</th>
                    <td>
                        <!--TODO-->
                        발송:0회
                        <div class="btn btn-white btn-sm">발송이력</div>
                    </td>
                </tr>
                <tr v-if="!$.isEmptyAll(fileList.filePre1)">
                    <th>회의록</th>
                    <td>
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.fileMeetingReport.files" ></simple-file-list>
                        </div>

                        <file-upload :file="fileList.fileMeetingReport" :id="'fileMeetingReport'" :project="mainData" :accept="false" v-show="isModify"></file-upload>

                        <div class="mgt5" v-show="fileList.fileMeetingReport.files.length > 0">
                            <div class="btn btn-sm btn-white"
                                 @click="openEmailPopup('meetingReport',fileList.fileMeetingReport.files)">
                                회의록 발송
                            </div>
                            <div class="btn btn-sm btn-white" @click="openSendHistory('회의록')">
                                발송이력
                            </div>
                        </div>
                    </td>
                    <th class="font-12">사양서</th>
                    <td>
                        <div v-if="10 == mainData.stOrder">
                            <!--작지 완료시-->
                            <schedule-template :data="mainData" :modify="isModify" :type="'orderConfirm'"></schedule-template>

                            <div class="mgt5 dp-flex">
                                <div v-if="10 == mainData.stOrderConfirm"
                                     class="btn btn-sm btn-white"
                                     @click="setOrderStatus('r')">
                                    사양서 확정취소
                                </div>
                                <div v-else
                                     class="btn btn-sm btn-red btn-red-line2"
                                     @click="openEmailPopup('designGuide')">
                                    <span v-if="!$.isEmptyAll(mainData.cpOrderSend)">사양서 재발송</span>
                                    <span v-else>사양서 발송</span>
                                </div>

                                <div class=" btn btn-sm btn-white" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)">
                                    보기
                                </div>

                                <div class="btn btn-sm btn-white" @click="openSendHistory('사양서')">
                                    발송이력
                                </div>
                            </div>

                        </div>
                        <div v-else>
                            작업 지시서 완료 후 발송 가능
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        제안서(발송)
                        <div class="text-muted font-11">(제안서 전달)</div>
                    </th>
                    <td>
                        <schedule-template :data="mainData" :modify="isModify" :type="'meetingProposal'" class="mgb5"></schedule-template>

                        <!--제안서 결재 완료시 발송 가능-->
                        <div v-if="mainData.stProposal >= 9 && !$.isEmptyAll(fileList.fileProposal)">
                            <div class="dp-flex font-11" v-show="!isModify">
                                <simple-file-list :files="fileList.fileProposal.files" ></simple-file-list>
                            </div>
                            <div class="dp-flex w-100p mgt5">

                                <div class="btn btn-sm btn-red btn-red-line2"
                                     v-if="10 === mainData.stProposal"
                                     @click="openEmailPopup('proposal',fileList.fileProposal.files)">
                                    <span v-if="!$.isEmptyAll(mainData.proposalDt)">재</span>발송
                                </div>

                                <div v-if="9 > mainData.stProposal">
                                    제안서 승인 후 발송 가능
                                </div>

                                <div class="btn btn-white btn-sm"
                                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                                     @click="openUrl('proposal'+mainData.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                                    보기
                                </div>

                                <div class="btn btn-sm btn-white"
                                     v-if="!$.isEmptyAll(mainData.proposalDt)"
                                     @click="openSendHistory('제안서')">
                                    발송이력
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            제안서 승인 후 발송 가능
                        </div>
                    </td>
                    <th>아소트(고객입력)</th>
                    <td>
                        <div>
                            예정 :
                            <span v-show="!isModify">{% $.formatShortDate(mainData['exAssortConfirm']) %}</span>
                            <span v-show="isModify" class="mini-picker">
                                <date-picker v-model="mainData['exAssortConfirm']" value-type="format" format="YYYY-MM-DD" :editable="false"></date-picker>
                            </span>
                        </div>
                        <div class="dp-flex">
                            상태 :
                            <span v-if="'p'==mainData.assortApproval">
                                <div class="dp-flex text-green">
                                    <i aria-hidden="true" class="fa fa-check-circle"></i>
                                    <span>{% $.formatShortDate(mainData.cpAssortConfirm) %}</span>
                                </div>
                            </span>
                            <span v-else :class="$.getAssortAcceptNameColor(mainData.assortApproval)['color']" >
                                {% $.getAssortAcceptNameColor(mainData.assortApproval)['name'] %}
                            </span>
                        </div>

                        <div class="dp-flex mgt5">
                            <div class="" v-if="'n'===mainData.assortApproval">
                                <div class=" btn btn-sm btn-blue btn-blue-line" @click="openEmailPopup('assort')">
                                    입력요청
                                </div>
                            </div>
                            <div class=" btn btn-sm btn-blue btn-blue-line"
                                 v-if="'r'===mainData.assortApproval"
                                 @click="openEmailPopup('assort')">
                                재요청
                            </div>

                            <div class="btn btn-sm btn-blue btn-blue-line" title="고객이 재입력하게 합니다."
                                 v-if="'f'===mainData.assortApproval"
                                 @click="setAssortStatus('r')">
                                이전상태로
                            </div>

                            <div class="" >
                                <div class=" btn btn-sm btn-white" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
                                    고객 입력화면 보기
                                </div>
                            </div>

                            <div class="btn btn-sm btn-white"
                                 v-if="'n'!==mainData.assortApproval"
                                 @click="openSendHistory('아소트')">
                                발송이력
                            </div>

                        </div>

                    </td>
                </tr>
                <tr>
                    <th>샘플 안내서</th>
                    <td>
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.fileSampleGuide.files" ></simple-file-list>
                        </div>

                        <file-upload :file="fileList.fileSampleGuide" :id="'fileSampleGuide'" :project="mainData" :accept="false" v-show="isModify"></file-upload>

                        <div class="mgt5" v-show="fileList.fileSampleGuide.files.length > 0">
                            <div class="btn btn-sm btn-white"
                                 @click="openEmailPopup('sampleGuide',fileList.fileSampleGuide.files)">
                                샘플 안내서 발송
                            </div>
                            <div class="btn btn-sm btn-white" @click="openSendHistory('샘플 안내서')">
                                발송이력
                            </div>
                        </div>
                    </td>
                    <th class="font-12">분류패킹 정보</th>
                    <td class="font-11">
                        <label class="radio-inline">
                            <input type="radio" name="tmpx" value="1" />고객이 IMS 등록
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="tmpx" value="2" />고객이 메일 전달
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="tmpx" value="3" />폐쇄몰 이용
                        </label>
                        <!--<div class="btn btn-white btn-sm">입력안내</div>-->
                    </td>
                </tr>
                <tr>
                    <th>샘플 확정 알림</th>
                    <td colspan="3">
                        3개 중 2개 확정
                        <div class="notice-info">확정 대상 모두 확정되어야 알림 발송 가능.</div>

                        <!--TODO : 발송할 때 마다 Insert 되고 스냅샷으로 처리한다.
                        <div class="btn btn-white btn-sm">발송</div>
                        <div class="btn btn-white btn-sm">발송이력</div>
                        -->
                    </td>
                </tr>
                </tbody>
            </table>
        </section>

    </div>

</div>

<!--------------------------------- [ 업무 스케쥴 ] ------------------------------------------->
<div class="col-xs-12 ">
    <div class="col-xs-12 " >
        <!--스케쥴 탭 + 스케쥴 내용-->
        <?php include 'ims25/ims25_view_schedule.php' ?>
    </div>
</div>


<!--------------------------------- [ 프로젝트 상품 관련 탭 => 상품 / 기획 / 샘플 / 아소트 / 견적  ] ------------------------------------------->
<div class="col-xs-12 ">
    <!--예상스타일-->
    <div class="col-xs-12 js-order-view-receiver-area">

        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <span class="godo">
                    제작 상품 관리
                </span>
            </div>
            <div class="flo-right"></div>
        </div>

        <div class="">
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

                            <div class="btn btn-red btn-red-line2" v-show="!isModify" @click="setModify(true)">수정</div>
                            <div class="btn btn-blue btn-blue-line " @click="addSalesStyle()" v-show="isModify">+ 스타일 추가</div>
                            <div class="btn btn-red" v-show="isModify" @click="saveWithStyle()">저장</div>
                            <div class="btn btn-white" v-show="isModify" @click="setModify(false)">취소</div>
                            <div class="btn btn-blue" @click="openCommonPopup('customer_estimate', 1100, 850, {projectSno:mainData.sno})" v-show="!isModify">고객 견적서 발송</div>
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
                    <?php include 'ims25_view_main_style.php'?>
                </div>
                <div v-show="showStyle && 'plan' == styleTabMode" class="new-style2">
                    <?php include 'template/ims_product_plan.php' ?>
                </div>
                <div v-show="showStyle && 'sample' == styleTabMode" class="">
                    샘플 탭
                </div>
                <div v-show="showStyle && 'estimate' == styleTabMode" class="">
                    견적 탭
                </div>
                <div v-show="showStyle && 'assort' == styleTabMode" class="">
                    아소트 탭
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

<!--영업 기획서 영역-->
<div class="col-xs-12" v-if="!$.isEmpty(salesPlan)">

    <div class="col-xs-6">
        <div class="table-title gd-help-manual">
            <div class="flo-left">영업 기획 정보</div>
            <div class="flo-right">

                <div class="btn btn-sm btn-white" @click="openSalesView(mainData.sno)" >영업기획서 보기</div>

                <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
        </div>

        <table class="table table-cols ims-table-style1 table-height30 table-pd-5">
            <colgroup>
                <col class="w-16p">
                <col class="w-34p">
                <col class="w-16p">
                <col class="w-34p">
            </colgroup>
            <tbody>
            <tr>
                <th>입찰 시기</th>
                <td>
                    {% mainData.exMeeting %}
                </td>
                <th>경쟁 업체</th>
                <td>
                    {% getSalesPlanVal('입찰 정보', '참여 업체 (경쟁사)') %}
                </td>
            </tr>
            <tr>
                <th>현재 계약 업체</th>
                <td class="text-blue font-weight-bold">
                    {% getSalesPlanVal('입찰 정보 (현재 상황)', '현재 계약 업체') %}
                </td>
                <th>변경 사유</th>
                <td>
                    {% getSalesPlanVal('입찰 정보 (현재 상황)', '변경 사유') %}
                </td>
            </tr>
            <tr>
                <th>단가 민감도</th>
                <td>
                    {% getSalesPlanVal('추가 정보', '단가 민감도') %}
                </td>
                <th>납기 민감도</th>
                <td>
                    {% getSalesPlanVal('추가 정보', '납기 민감도') %}
                </td>
            </tr>

            <tr>
                <th>색상 민감도</th>
                <td>
                    {% getSalesPlanVal('추가 정보', '색상 민감도') %}
                </td>
                <th>품질 민감도</th>
                <td>
                    {% getSalesPlanVal('추가 정보', '품질 민감도') %}
                </td>
            </tr>
            <tr>
                <th>제안/품평 방식</th>
                <td colspan="3">
                    <div v-html="getSalesPlanAll('제안/품평 방식 (절차)')"></div>
                </td>
            </tr>
            <tr>
                <th>업체 선정 기준</th>
                <td colspan="3">
                    <div v-html="getSalesPlanAll('업체 선정 기준 (평가 항목)')"></div>
                </td>
            </tr>
            <tr>
                <th>의사 결정 라인</th>
                <td colspan="3" >
                    <div v-html="getSalesPlanJsonList('의사 결정 라인')"></div>
                </td>
            </tr>
            <tr>
                <th>근무 환경</th>
                <td colspan="3">
                    {% getSalesPlanMix('근무 환경 / 세탁 환경', ['근무 형태', '근무 강도']) %}
                </td>
            </tr>

            <tr>
                <th>세탁 환경</th>
                <td colspan="3">
                    {% getSalesPlanMix('근무 환경 / 세탁 환경', ['세탁 방법', '세탁 조건', '건조 조건']) %}
                </td>
            </tr>
            <tr>
                <th>원단 적용 특이 사항</th>
                <td colspan="3">
                    {% getSalesPlanVal('근무 환경 / 세탁 환경', '원단 적용 특이 사항') %}
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>

<!--발주 관련-->
<div class="col-xs-12" v-if="!$.isEmpty(customer.addedInfo)">

    <div class="col-xs-6">
        <div class="table-title gd-help-manual">
            <div class="flo-left">발주 기본 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
        </div>

        <table class="table table-cols ims-table-style1 table-height30 table-pd-5">
            <colgroup>
                <col class="w-16p">
                <col class="w-34p">
                <col class="w-16p">
                <col class="w-34p">
            </colgroup>
            <tbody>
            <tr>
                <th>고객납기</th>
                <td >
                    <div v-if="2 == mainData.productionStatus || 91 == mainData.productionStatus" class="sl-green mgt5">
                        <span class="font-14">{% $.formatShortDateWithoutWeek(mainData.customerDeliveryDt) %}</span>
                        납기완료
                    </div>
                    <div v-else>
                        <?php $model='mainData.customerDeliveryDt';?>
                        <?php include 'template/basic_view/_picker2.php'?>

                        <div v-if="!isModify" class="">
                            <div class="text-danger " v-if="'y' !== mainData.customerDeliveryDtConfirmed">변경불가</div>
                            <div class="sl-blue " v-if="'n' !== mainData.customerDeliveryDtConfirmed">변경가능</div>
                        </div>
                        <div v-if="isModify" class="dp-flex">
                            <div class="">
                                <label class="radio-inline">
                                    <input type="radio" name="order_deliveryConfirm"  value="y" v-model="mainData.customerDeliveryDtConfirmed"/>변경가능
                                </label>
                            </div>
                            <div>
                                <label class="radio-inline">
                                    <input type="radio" name="order_deliveryConfirm"  value="n" v-model="mainData.customerDeliveryDtConfirmed"/>변경불가
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
                        <div v-show="'y' === mainData.syncProduct">
                            납기일자 프로젝트로 한번에 관리
                            <div class="notice-info">상품 납기일이 프로젝트 수정 시 해당 납기일로 변경됩니다.</div>
                        </div>
                        <div v-show="'n' === mainData.syncProduct">
                            납기일자 상품별 관리
                            <div class="notice-info">프로젝트 납기일과 상관없이 상품별 관리 합니다.</div>
                        </div>
                    </div>
                    <div v-show="isModify">
                        <label class="radio-inline" style="font-weight: normal;font-size:12px">
                            <input type="radio" name="orderSyncProduct"  value="y" v-model="mainData.syncProduct"/> 납기일자 프로젝트로 한번에 관리
                        </label>
                        <br>
                        <label class="radio-inline" style="font-weight: normal;font-size:12px">
                            <input type="radio" name="orderSyncProduct"  value="n" v-model="mainData.syncProduct"/> 납기일자 상품별 관리
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>이노버납기</th>
                <td >
                    <div v-if="2 == mainData.productionStatus || 91 == mainData.productionStatus" class="sl-green mgt5">
                        납기완료
                    </div>
                    <div v-else>
                        <?php $model='mainData.msDeliveryDt';?>
                        <?php include 'template/basic_view/_picker2.php'?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>발주D/L</th>
                <td colspan="3">

                    <div v-show="!isModify">
                        <!--완료일-->
                        <div v-if="'0000-00-00' != mainData.cpProductionOrder && !$.isEmpty(mainData.cpProductionOrder)" class="text-muted">
                                    <span class="font-13 sl-green">
                                        {% $.formatShortDateWithoutWeek(mainData.cpProductionOrder) %} 발주
                                    </span>
                        </div>
                        <!--대체텍스트-->
                        <div v-else-if="!$.isEmpty(mainData.txProductionOrder)">
                                    <span class="font-11">
                                        {% mainData.txProductionOrder %}
                                    </span>
                        </div>
                        <!--예정일-->
                        <div v-else-if="!$.isEmpty(mainData.exProductionOrder)" class="">
                                    <span class="font-13">
                                        {% $.formatShortDateWithoutWeek(mainData.exProductionOrder) %}
                                    </span>
                            <span class="font-11 mgl5" v-html="$.remainDate(mainData.exProductionOrder,true)"></span>
                        </div>
                        <!--미설정-->
                        <div v-else class="text-muted">미정</div>
                    </div>

                    <div class="dp-flex" v-show="isModify">
                        <!--발주 예정일 수정-->
                        <date-picker v-model="mainData.exProductionOrder"
                                     class="mini-picker " value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                        </date-picker>
                        <input type="text" placeholder="대체텍스트" class="form-control " v-model="mainData.txProductionOrder">
                    </div>

                </td>
            </tr>
            <tr>
                <th>발주일</th>
                <td colspan="3">

                    <div v-if="mainData.productionStatus > 0">
                        <a :href="'../ims/imsProductionList.php?initStatus=0&key=prj.sno&keyword='+mainData.sno" class="font-14 sl-blue" target="_blank">{% $.formatShortDate(mainData.cpProductionOrder) %}</a>
                    </div>
                    <div v-else>
                        <div class="btn btn-sm btn-blue" @click="orderToFactory()"
                             v-if="
                                2 == mainData.priceStatus
                                && 2 == mainData.costStatus
                                && 'p' == mainData.assortApproval
                                && 2 == mainData.workStatus
                                && 'p' == mainData.customerOrderConfirm
                                ">
                            발주하기
                        </div>

                        <div class="btn btn-sm btn-gray" @click="visibleOrderCondition=true"
                             v-if="!(
                                2 == mainData.priceStatus
                                && 2 == mainData.costStatus
                                && 'p' == mainData.assortApproval
                                && 2 == mainData.workStatus
                                && 'p' == mainData.customerOrderConfirm
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
                <td colspan="99" class="pd0" style="padding:0!important;">
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
                                <span v-html="mainData.priceStatusIcon"></span>
                            </td>
                            <th class="bg-light-gray">생산가</th>
                            <td><!--생산가-->
                                <span v-html="mainData.costStatusIcon"></span>
                            </td>
                            <th class="bg-light-gray">아소트</th>
                            <td><!--아소트-->
                                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == mainData.assortApproval"></i>
                                <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == mainData.assortApproval"></i>
                                <span class="text-muted" v-else>-</span>
                            </td>
                            <th class="bg-light-gray">작업지시서</th>
                            <td>
                                <span v-html="mainData.workStatusIcon"></span>
                            </td>
                            <th class="bg-light-gray">사양서</th>
                            <td>
                                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == mainData.customerOrderConfirm"></i>
                                <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == mainData.customerOrderConfirm"></i>
                                <span class="text-muted" v-else>-</span>
                            </td>
                            <th class="bg-light-gray">퀄리티</th>
                            <td>
                                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'2' == mainData.fabricStatus"></i>
                                <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'1' == mainData.fabricStatus"></i>
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
                        {% mainData.use3plKr %}
                    </div>
                    <div v-show="isModify">
                        <label class="radio-inline font-11">
                            <input type="radio" name="salesUse3pl" value="n"  v-model="mainData.use3pl" />사용안함
                        </label>
                        <label class="radio-inline font-11">
                            <input type="radio" name="salesUse3pl" value="y"  v-model="mainData.use3pl" />사용
                        </label>
                    </div>

                    <div v-show="'y' === mainData.use3pl" >
                        <simple-file-upload :file="fileList.fileBarcode" :id="'fileBarcode'" :project="mainData" ></simple-file-upload>
                    </div>
                </td>
            </tr>
            <tr>
                <th>분류패킹 여부/파일</th>
                <td colspan="3">
                    <?php $model = 'mainData.packingYn'; $listCode = 'processType'; $modelPrefix='order'; $listIndexData="";?>
                    <?php include 'template/basic_view/_radio.php'?>
                    <div v-show="'y' === mainData.packingYn" >
                        <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="mainData" ></simple-file-upload>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    회계 담당자 전달
                </th>
                <td colspan="3">
                    <div v-show="isModify">
                        <textarea class="form-control" rows="6" v-model="mainData.accountingMessage" placeholder="회계 담당자 전달 메세지"></textarea>
                    </div>
                    <div v-show="!isModify" >
                        <div v-if="!$.isEmpty(mainData.accountingMessage)" v-html="mainData.accountingMessageBr"></div>
                        <div v-else class="text-muted">
                            없음
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="col-xs-6">
        <div class="table-title gd-help-manual">
            <div class="flo-left">발주/납품 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
        </div>

        <table class="table table-cols ims-table-style1 table-height30 table-pd-5">
            <colgroup>
                <col class="w-16p">
                <col class="w-34p">
                <col class="w-16p">
                <col class="w-34p">
            </colgroup>
            <tbody>
            <tr>
                <th>대표 생산처</th>
                <td colspan="3">

                    <div v-show="!isModify">
                        <div v-if="!$.isEmpty(mainData.mainFactoryName)">{% mainData.mainFactoryName %}</div>
                        <div v-else>미정</div>
                    </div>

                    <select class="form-control" style="width:30%" v-model="mainData.produceCompanySno" v-show="isModify">
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
                        {% mainData.produceTypeKr %}
                        {% mainData.produceNational %}
                    </div>

                    <div class="form-inline" v-show="isModify">
                        <select class="form-control " v-model="mainData.produceType">
                            <?php foreach ($prdType as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                        <select class="form-control" v-model="mainData.produceNational" placeholder="선택">
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
                        <div class="bg-light-gray2 round-box w-55p " style="height:100px" v-show="!isModify" v-html="$.nl2br(mainData.deliveryMethod)">
                        </div>
                        <textarea class="form-control w50 inline-block flo-left" rows="4" v-model="mainData.deliveryMethod" placeholder="납품 계획/방법 메모" v-show="isModify"></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <th>납품계획 파일</th>
                <td colspan="99">
                    <simple-file-upload :file="fileList.fileDeliveryPlan" :id="'fileDeliveryPlan'" :project="mainData" ></simple-file-upload>
                    <div class="notice-info">납품 계획 파일</div>
                </td>
            </tr>
            <tr>
                <th>납품 보고서</th>
                <td colspan="99">
                    <simple-file-upload :file="fileList.fileDeliveryReport" :id="'fileDeliveryReport'" :project="mainData" ></simple-file-upload>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>


<!-- 우측 하단 플로팅 메뉴 -->
<!--<div class="ims-fab2" style="bottom:220px">
    <div class="ims-fab2-menu">
        <div class="ims-fab2-panel">
            <a href="#" class="ims-fab2-item">스케쥴관리</a>
            <a href="#" class="ims-fab2-item">스타일관리</a>
            <a href="#" class="ims-fab2-item">기타정보</a>
            <a href="#" class="ims-fab2-item">발주정보</a>
            <a href="#" class="ims-fab2-item">TODO-LIST</a>
        </div>
    </div>
    <button type="button" class="ims-fab2-btn" aria-label="빠른 메뉴">
        <span class="ims-fab2-plus" aria-hidden="true"></span>
    </button>
</div>-->

<!-- 부가 금액 정보 -->
<!-- 회계 전달 메세지 -->
<!-- 고객사 정보 -->
<!-- 기획 / 제안 정보 -->
<!--발주정보-->
<!-- 기획서/제안서 -->
<!--파일-->


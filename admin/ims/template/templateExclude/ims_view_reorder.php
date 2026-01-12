<?php $modelPrefix='design';  ?>
<!--업무 스케쥴-->
<div class="col-xs-12" >
    <div class="col-xs-12" id="layoutOrderViewOrderInfoArea">

        <div v-if="typeof project.scheduleStatus != 'undefined' && !$.isEmpty(project.scheduleStatus)" >
            <div class="table-title gd-help-manual">
                <div class="flo-left area-title ">
                    <span class="godo">
                        리오더 스케쥴 관리
                    </span>
                    <div class="dp-flex dp-flex-gap20 font-15 mgt10 mgb5">

                        <div class="round-box bg-light-gray2 dp-flex dp-flex-center">
                            현재단계 :
                            <div class="sl-blue">
                                {% project.projectStatusKr %} 단계
                                <div class="btn btn-sm btn-white mgl5" @click="openProjectStatusHistory(project.sno,'')">변경이력</div>
                            </div>
                            <div class="btn btn-sm btn-red btn-red-line2 mgl10 font-normal"  @click="setPlanNotPossible" v-if="20 == project.projectStatus">
                                기획불가 처리
                            </div>
                        </div>
                        <div class="dp-flex dp-flex-center round-box bg-light-gray2 w-190px">
                            영업 담당자 :
                            <?php
                            $modifyKey='isModify';
                            $model='project.salesManagerSno';
                            $modelValue='project.salesManagerNm';
                            $defaultValue=['0','미정'];
                            $listData=$salesEtcManagerList;
                            $selectWidth=130;
                            ?>
                            <?php include 'basic_view/_select.php'?>
                        </div>
                    </div>
                </div>
                <div class="flo-right ">
                    <div class="dp-flex " style="position:absolute;top:30px; right:20px">
                        <div class="btn btn-lg btn-white mgl10 " @click="setModify(true)" v-show="!isModify">
                            <!--<i class="fa fa-pencil-square-o" aria-hidden="true"></i>-->
                            정보 수정
                        </div>
                        <div class="btn btn-lg btn-red btn-red2 mgl10" @click="saveSchedule()" v-show="isModify">저장</div>
                        <div class="btn btn-lg btn-white" @click="setModify(false)" v-show="isModify">수정 취소</div>
                    </div>
                    <!--refreshing-loader-->
                </div>
            </div>

            <!--스케쥴 집계-->
            <div >
                <table class="table ims-schedule-table w100 table-default-center table-fixed  table-td-height35 table-th-height35 mgb10 table-pd-3">
                    <colgroup>
                        <col class="w-7p">
                        <col class="w-7p">
                        <col class="w-7p">

                        <col class="w-4p">
                        <col>
                        <col>
                        <col>

                        <col class="w-10p">
                        <col class="w-10p">
                    </colgroup>
                    <tr>
                        <th class="text-danger" rowspan="2">
                            고객 납기
                            <div class="font-9 text-muted" v-if="'1'===isDevId">
                                customerDeliveryDt
                            </div>
                        </th>
                        <th class="sl-blue" rowspan="2">
                            이노버 납기
                            <div class="font-9 text-muted" v-if="'1'===isDevId">
                                msDeliveryDt
                            </div>
                        </th>
                        <th class="" rowspan="2">
                            발주D/L
                            <div class="font-9 text-muted" v-if="'1'===isDevId">
                                customerOrderDeadLine
                            </div>
                        </th>
                        <th class="" rowspan="2">구분</th>
                        <th class="" colspan="3">발주준비</th>
                        <th class="" rowspan="2">판매가</th>
                        <th class="" rowspan="2">생산가</th>
                    </tr>
                    <tr>
                        <th style="border-top: solid 1px #dddddd !important;">
                            고객발주
                            <span class="font-11">(아소트)</span>
                        </th>
                        <th style="border-top: solid 1px #dddddd !important;">작지/사양서</th>
                        <th style="border-top: solid 1px #dddddd !important;">사양서발송</th>
                    </tr>
                    <tr>
                        <td rowspan="3" class="border-bottom-light-gray-imp ">
                            <!--고객 납기일-->
                            <div v-if="!isModify" class="mgt10">
                                <span class="font-14">{% $.formatShortDateWithoutWeek(project.customerDeliveryDt) %}</span>
                                <div class="font-11 " v-html="$.remainDate(project.customerDeliveryDt,true)"></div>

                                <div class="text-danger font-11" v-if="'y' !== project.customerDeliveryDtConfirmed">변경불가</div>
                                <div class="sl-blue font-11" v-if="'n' !== project.customerDeliveryDtConfirmed">변경가능</div>
                            </div>
                            <div v-if="isModify">
                                <date-picker v-model="project.customerDeliveryDt" class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 납기"></date-picker>

                                <div class="mgt5">
                                    <label class="radio-inline">
                                        <input type="radio" name="deliveryConfirmYn"  value="y" v-model="project.customerDeliveryDtConfirmed"/>변경가능
                                    </label>
                                </div>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="deliveryConfirmYn"  value="n" v-model="project.customerDeliveryDtConfirmed"/>변경불가
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td rowspan="3" class="border-bottom-light-gray-imp ">
                            <!--이노버 납기일-->
                            <div v-if="!isModify">
                                <span class="text-muted" v-if="$.isEmpty(project.msDeliveryDt)">미정</span>
                                <span class="font-14" v-if="!$.isEmpty(project.msDeliveryDt)">
                                    {% $.formatShortDateWithoutWeek(project.msDeliveryDt) %}
                                </span>
                                <div class="font-11 mgt5" v-html="$.remainDate(project.msDeliveryDt,true)"></div>
                            </div>
                            <div v-if="isModify">
                                <date-picker v-model="project.msDeliveryDt" class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="이노버 납기"></date-picker>
                            </div>
                        </td>
                        <td rowspan="3" class="border-bottom-light-gray-imp ">
                            <!--발주DL-->
                            <div v-if="!isModify && !$.isEmpty(project.customerOrderDeadLine)">
                                <span class="font-14">{% $.formatShortDateWithoutWeek(project.customerOrderDeadLine) %}</span>
                                <div class="font-11 mgt5" v-html="$.remainDate(project.customerOrderDeadLine,true)"></div>
                            </div>
                            <div v-if="!isModify && $.isEmpty(project.customerOrderDeadLine)">
                                <span class="font-12">{% project.customerOrderDeadLineText %}</span>
                            </div>
                            <div v-if="isModify">
                                <date-picker v-model="project.customerOrderDeadLine" class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL"></date-picker>
                                <input type="text" placeholder="대체텍스트" class="form-control" v-model="project.customerOrderDeadLineText">
                            </div>
                        </td>
                        <td class="bg-light-gray">
                            예정일
                        </td>
                        <td class="bg-light-yellow">
                            <expected-template :modify="isModify" :data="project.schedule.custOrder" :title="'고객발주(아소트)'"></expected-template>
                        </td>
                        <td class="bg-light-yellow">
                            <expected-template :modify="isModify" :data="project.schedule.order" :title="'작지/사양서'"></expected-template>
                        </td>
                        <td class="bg-light-yellow">
                            <expected-template :modify="isModify" :data="project.schedule.custSpec" :title="'사양서 발송'"></expected-template>
                        </td>
                        <td class="border-bottom-light-gray-imp" rowspan="3">
                            <!--판매가-->
                            <div >
                                <span v-html="project.priceStatusIcon"></span>
                                {% project.priceStatusKr %}
                            </div>

                            <div class="btn btn-white btn-sm mgt10" @click="showSchedule()">
                                결재정보
                            </div>
                        </td>
                        <td class="border-bottom-light-gray-imp" rowspan="3">
                            <div >
                                <span v-html="project.costStatusIcon"></span>
                                {% project.costStatusKr %}
                            </div>

                            <div class="btn btn-white btn-sm mgt10" @click="showSchedule()">
                                결재정보
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light-gray">
                            완료일
                        </td>
                        <!--발주단계 완료일-->
                        <td class="">
                            <complete-template :modify="isModify" :data="project.schedule.custOrder" :title="'고객사 발주(아소트)'"></complete-template>
                        </td>
                        <td class="">
                            <complete-template :modify="isModify" :data="project.schedule.order" :title="'작지/사양서'"></complete-template>
                        </td>
                        <td class="">
                            <complete-template :modify="isModify" :data="project.schedule.custSpec" :title="'사양서 발송'"></complete-template>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light-gray">
                            등록/보기
                        </td>
                        <td class="">
                            <div class="btn btn-white btn-sm" @click="showSchedule()">
                                발송정보
                            </div>
                            <div class="btn btn-white btn-sm" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
                                확인
                            </div>
                        </td>
                        <td class="">

                            <div v-if="!$.isEmpty(project.workStatus)">
                                <span v-html="project.workStatusIcon"></span>
                                {% project.workStatusKr %}
                            </div>
                            <div class="text-muted" v-else>
                                작업대기
                            </div>
                        </td>
                        <td class="">
                            <div class="btn btn-white btn-sm" @click="showSchedule()">
                                발송정보
                            </div>
                            <div class="btn btn-white btn-sm" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)">
                                확인
                            </div>
                        </td>
                    </tr>
                </table>

                <div v-show="isModify">
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="y" v-model="project.syncProduct"/> 납기일자 프로젝트로 한번에 관리
                    </label>
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="n" v-model="project.syncProduct"/> 납기일자 상품별 관리
                    </label>
                </div>
                <div v-show="!isModify" class="mgb10">
                    <div v-show="'y'===project.syncProduct" class="_notice-info">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        납기일자(고객,MS)가 프로젝트 수정시 연동되어 함께 변경됩니다.
                    </div>
                    <div v-show="'n'===project.syncProduct" class="_notice-info">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        납기일자(고객,MS)는 상품별 관리합니다.
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
                                <col />
                                <col />
                                <col />
                                <col />
                            </colgroup>
                            <tr>
                                <th class="border-right-gray">구분</th>
                                <th class="border-right-gray">아소트</th>
                                <th class="border-right-gray">사양서</th>
                                <th class="border-right-gray">판매가</th>
                                <th >생산가</th>
                            </tr>
                            <tr>
                                <th class="border-right-gray" >
                                    결재
                                </th>
                                <!--아소트-->
                                <td class="border-right-gray" >

                                    <div :class="$.getAssortAcceptNameColor(project.assortApproval)['color'] + ' font-16 mgr10'"  v-if="!$.isEmpty(project.assortApproval)">
                                        {% $.getAssortAcceptNameColor(project.assortApproval)['name'] %}
                                    </div>

                                    <div class="dp-flex mgt10" style="justify-content: center" >
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
                        <li role="presentation" :class="'estimate' === styleTabMode?'active':''" >
                            <a href="#" data-toggle="tab" @click="changeStyleTab('estimate')">고객 견적서(발송이력)</a>
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
                            상품 수량 : <span class="bold">{% $.setNumberFormat(styleTotal) %}ea</span>
                        </div>
                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px">
                            판매 금액 합계 : <span class="text-danger bold">{% $.setNumberFormat(styleTotalPrice) %}원</span>

                            <span class="font-12" v-if="styleTotalCost > 0 && styleTotalPrice > 0">
                                (마진:  {%  $.setNumberFormat(styleTotalPrice - styleTotalCost)  %}원, {% (100-(Math.round(styleTotalCost/styleTotalPrice*100)) ) %}%)
                            </span>
                            <span class="font-12" v-if="styleTotalPrice > 0 && 0 >= styleTotalCost && styleTotalEstimate > 0 ">
                                (미확정 마진:  {%  $.setNumberFormat(styleTotalPrice - styleTotalEstimate)  %}원, {% (100-(Math.round(styleTotalEstimate/styleTotalPrice*100)) ) %}%)
                            </span>
                        </div>

                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px" v-if="styleTotalCost > 0">
                            매입금액 합계 : <span class="sl-blue bold">{% $.setNumberFormat(styleTotalCost) %}원</span>
                        </div>
                        <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px" v-if="0 >= styleTotalCost && styleTotalEstimate > 0">
                            (미확정) 매입금액 합계 : <span class="color-gray bold">{% $.setNumberFormat(styleTotalEstimate) %}원</span>
                        </div>
                    </div>
                </div>
                <div v-show="showStyle && 'basic' == styleTabMode" class="new-style2">
                    <?php include 'style/type_reorder.php'?>
                </div>
                <div v-show="showStyle && 'estimate' == styleTabMode" class="">
                    <?php include 'style/type_estimate.php'?>
                </div>
                <div v-show="showStyle && 'assort' == styleTabMode" class="">
                    <?php include 'style/type_assort.php'?>
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

<!-- 고객사 정보 -->
<div class="col-xs-12 mgt20" v-if="!$.isEmpty(customer.addedInfo)">
    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">고객사 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a></div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30" >
                <colgroup>
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>고객명</th>
                    <td>
                        <div class="dp-flex">
                            <?php $model='customer.customerName'; $placeholder='고객명'; $modifyKey='isModify' ?>
                            <?php include 'basic_view/_text.php'?>
                            <div class="btn btn-white btn-sm mgl5" @click="openCustomer(customer.sno)">상세</div>
                        </div>
                    </td>
                    <th>3PL</th>
                    <td>
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
                </tr>
                <tr>
                    <th>업종</th>
                    <td >
                        <?php $model='customer.industry'; $placeholder='업종'; $modifyKey='isModify' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                    <th>폐쇄몰</th>
                    <td>
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
                    <th>근무환경</th>
                    <td colspan="3">
                        <?php $model='customer.addedInfo.etc1'; $placeholder='근무환경' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>사원수</th>
                    <td colspan="3">
                        <?php $model='customer.addedInfo.etc2'; $placeholder='사원수' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>담당자</th>
                    <td colspan="3">
                        <div class="dp-flex">
                            <?php $model='customer.contactName'; $placeholder='담당자명' ?>
                            <?php include 'basic_view/_text.php'?>

                            <?php $model='customer.contactPosition'; $placeholder='직함' ?>
                            <?php include 'basic_view/_text.php'?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>연락처</th>
                    <td colspan="3">
                        <?php $model='customer.contactMobile'; $placeholder='휴대전화' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>E-MAIL</th>
                    <td colspan="3">
                        <?php $model='customer.contactEmail'; $placeholder='이메일' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">고객사 추가 정보</div>
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
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>진행 가능성</th>
                    <td colspan="99">
                        <?php $model = 'customer.addedInfo.info111'; $listCode = 'ratingType'; $modelPrefix=''; $listIndexData="";?>
                        <?php include 'basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr>
                    <th>업체 변경 사유</th>
                    <td colspan="99">
                        <?php $model='customer.addedInfo.info116'; $placeholder='업체 변경 사유' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>경쟁 업체</th>
                    <td colspan="99">
                        <?php $model='customer.addedInfo.info102'; $placeholder='경쟁 업체' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>의사 결정 라인</th>
                    <td>
                        <?php $model='customer.addedInfo.info089'; $placeholder='의사 결정 라인' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                    <th>노사 합의 여부</th>
                    <td>
                        <?php $model = 'customer.addedInfo.info088'; $listCode = 'existType3'; $modelPrefix=''; $listIndexData=""; ?>
                        <?php include 'basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr>
                    <th>폐쇄몰 관심도</th>
                    <td colspan="99">
                        <?php $model = 'customer.addedInfo.info015'; $listCode = 'ratingType'?>
                        <?php include 'basic_view/_radio.php'?>
                    </td>
                </tr>
                <tr>
                    <th>이해 관계</th>
                    <td colspan="99">
                        <?php $model='customer.addedInfo.info108'; $placeholder='이해관계' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>기존 업체</th>
                    <td colspan="99">
                        <?php $model='customer.addedInfo.info117'; $placeholder='기존 업체' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



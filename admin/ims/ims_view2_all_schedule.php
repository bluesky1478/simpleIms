
<table class="table ims-schedule-table w100 table-default-center table-fixed  table-td-height30 table-th-height30 mgb10 table-pd-3" v-show="scheduleLoad">
    <colgroup>
        <col class="w-6p">
        <!--<col class="w-7p">-->
        <col class="w-6p">
        <col class="w-4p">
        <col class="w-8p">
        <col class="w-8p">
        <col class="w-7p">
        <col class="w-8p">
        <col class="w-7p">
        <col class="w-5p">
        <col class="w-8p">
        <col class="w-6p">
        <col class="w-6p">
    </colgroup>
    <tr>
        <th class="text-danger">고객 납기</th>
        <!--<th class="sl-blue">이노버 납기</th>-->
        <th class="">발주D/L</th>
        <th class="" >구분</th>
        <th class="" >기획</th>
        <th class="" >제안서</th>
        <th class="" >샘플발송</th>
        <th class="" >작지/사양서</th>
        <th class="" >발주</th>
        <th class="" >Q/B</th>
        <th class="" >아소트</th>
        <th class="" >생산가</th>
        <th class="" >판매가</th>
    </tr>
    <!--1. 예정일-->
    <tr>
        <!--고객납기-->
        <td rowspan="3" class="border-bottom-light-gray-imp ">
            <div v-if="!isModify" class="mgt10">
                <span class="font-14">{% $.formatShortDateWithoutWeek(project.customerDeliveryDt) %}</span>

                <div v-if="2 == project.productionStatus || 91 == project.productionStatus" class="sl-green mgt5">
                    납기완료
                </div>
                <div v-else>
                    <div class="font-11 " v-html="$.remainDate(project.customerDeliveryDt,true)"></div>
                    <div class="text-danger font-11" v-if="'y' !== project.customerDeliveryDtConfirmed">변경불가</div>
                    <div class="sl-blue font-11" v-if="'n' !== project.customerDeliveryDtConfirmed">변경가능</div>
                </div>
            </div>
            <div v-if="isModify">
                <date-picker v-model="project.customerDeliveryDt" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 납기"></date-picker>
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
        <!--이노버납기
        <td rowspan="3" class="border-bottom-light-gray-imp ">

            <div v-if="91 == project.projectStatus" class="mgt10">
                <div class="font-14">{% $.formatShortDateWithoutWeek(project.msDeliveryDt) %}</div>
                <div class="font-12 mgt5 sl-green">납기완료</div>
            </div>
            <div v-else>
                <div v-if="!isModify">
                    <span class="text-muted" v-if="$.isEmpty(project.msDeliveryDt)">미정</span>
                    <span class="font-14" v-if="!$.isEmpty(project.msDeliveryDt)">
                    {% $.formatShortDateWithoutWeek(project.msDeliveryDt) %}
                </span>
                    <div class="font-11 mgt5" v-html="$.remainDate(project.msDeliveryDt,true)"></div>
                </div>
                <div v-if="isModify">
                    <date-picker v-model="project.msDeliveryDt" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="이노버 납기"></date-picker>
                </div>
            </div>
        </td>-->
        <!--발주D/L-->
        <td rowspan="3" class="border-bottom-light-gray-imp ">
            <div v-if="!isModify">
                <!--완료일-->
                <div v-if="'0000-00-00' != project.cpProductionOrder && !$.isEmpty(project.cpProductionOrder)" class="text-muted">
                    <span class="font-14 sl-green">
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
                    <span class="font-14">
                        {% $.formatShortDateWithoutWeek(project.exProductionOrder) %}
                    </span>
                    <div class="font-11 mgt5" v-html="$.remainDate(project.exProductionOrder,true)"></div>
                </div>
                <!--미설정-->
                <div v-else class="text-muted">미정</div>
            </div>

            <div v-if="isModify && ('0000-00-00' == project.cpProductionOrder || $.isEmpty(project.cpProductionOrder))">
                <!--발주 예정일 수정-->
                <date-picker v-model="project.exProductionOrder"
                             class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                </date-picker>
                <input type="text" placeholder="대체텍스트" class="form-control mgt3" v-model="project.txProductionOrder">
            </div>
            <div v-if="isModify && '0000-00-00' != project.cpProductionOrder && !$.isEmpty(project.cpProductionOrder)">
                <!--
                발주 완료일 수정
                <date-picker v-model="project.cpProductionOrder"
                             class="mini-picker pdl5" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                </date-picker>
                -->
            </div>
        </td>
        <td class="bg-light-gray">
            예정일
        </td>

        <!-- 예정일 스케쥴 -->
        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_MAIN_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
            <td class="bg-light-yellow" v-if="$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])">
                <div :class="'dp-flex dp-flex-center ' + ( 'y' === project.delay<?=ucfirst($mainSchedule)?> ? 'text-danger':''  )">
                    <expected-template :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></expected-template>
                </div>
            </td>
            <td class="bg-light-gray " rowspan="3" v-if="!$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">
                <div class="relative w-100px dp-flex dp-flex-center cursor-pointer hover-btn" @click="openProjectUnit(project.sno,'<?=$mainSchedule?>','<?=$mainScheduleKr?>')" >
                    {% project['tx'+$.ucfirst('<?=$mainSchedule?>')] %}
                    <comment-cnt2 :data="project['<?=$mainSchedule?>CommentCnt']"></comment-cnt2>
                </div>
            </td>
        <?php } ?>
        <!--Qb-->
        <td class="ta-l border-bottom-light-gray-imp" rowspan="3">
            <div class="pdl5 cursor-pointer hover-btn" @click="showQbSchedule()">
                <div :class="$.getProcColor(project.fabricStatus)">
                    Q : {% project.fabricStatusKr %}
                </div>
                <div class="mgt5"></div>
                <div :class="$.getProcColor(project.btStatus)">
                    B : {% project.btStatusKr %}
                </div>
                <div class="btn btn-sm btn-white mgt5 w-100p">상세확인</div>
            </div>
        </td>
        <!--아소트-->
        <td class="border-bottom-light-gray-imp" rowspan="3">

            <div v-if="!$.isEmpty(project.assortApproval)">
                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' === project.assortApproval"></i>
                <span :class="$.getAssortAcceptNameColor(project.assortApproval)['color']">
                    {% $.getAssortAcceptNameColor(project.assortApproval)['name'] %}
                </span>
            </div>

            <div class="dp-flex dp-flex-center mgt5">
                <div class="" v-if="'n'===project.assortApproval">
                    <div class=" btn btn-sm btn-blue btn-blue-line" @click="openAssortUrl()">
                        입력요청
                    </div>
                </div>
                <div class=" btn btn-sm btn-blue btn-blue-line"
                     v-if="'r'===project.assortApproval"
                     @click="openAssortUrl()">
                    재요청
                </div>

                <div class="btn btn-sm btn-blue btn-blue-line"
                     v-if="'f'===project.assortApproval"
                     @click="setAssortStatus('r')">
                    이전상태로
                </div>

                <div class="btn btn-sm btn-blue"
                     v-if="'p'===project.assortApproval"
                     @click="setAssortStatus('f')">
                    확정취소
                </div>

                <div class="" >
                    <div class=" btn btn-sm btn-white" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
                        입력화면
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

            </div>

            <div class=" btn btn-sm btn-red btn-red-line2 mgt3 w-100p cursor-pointer hover-btn"
                 v-if="'f'===project.assortApproval"
                 @click="setAssortStatus('p')">
                아소트 확정
            </div>

        </td>
        <!--생산가-->
        <td class="border-bottom-light-gray-imp" rowspan="3">
            <div >
                <span v-html="project.costStatusIcon"></span>
                {% project.costStatusKr %}
            </div>
            <div class="mgt5">
                <div class="btn btn-sm btn-red btn-red-line2"
                     v-if="'n' === project.prdCostApproval && ( 0 >= projectApprovalInfo.cost.sno || $.isEmpty(projectApprovalInfo.cost.sno) )"
                     @click="openApprovalWrite(customer.sno, project.sno, 'cost')">
                    결재 요청
                </div>
            </div>
            <div>
                <approval-template3
                        :project="project"
                        :approval="projectApprovalInfo"
                        :confirm-type="'cost'"
                        :confirm-field="'prdCostApproval'"
                        :memo-field="'unused'"
                ></approval-template3>
            </div>
        </td>
        <!--판매가-->
        <td class="border-bottom-light-gray-imp" rowspan="3">
            <div >
                <span v-html="project.priceStatusIcon"></span>
                {% project.priceStatusKr %}
            </div>

            <div class="mgt5">
                <div class="btn btn-sm btn-red btn-red-line2" v-if="0 >= projectApprovalInfo.salePrice.sno || $.isEmpty(projectApprovalInfo.salePrice.sno)" @click="openApprovalWrite(customer.sno, project.sno, 'salePrice')">
                    결재요청
                </div>
            </div>

            <div>
                <approval-template3
                        :project="project"
                        :approval="projectApprovalInfo"
                        :confirm-type="'salePrice'"
                        :confirm-field="'prdPriceApproval'"
                        :memo-field="'unused'"
                ></approval-template3>
            </div>

        </td>
    </tr>

    <tr>
        <td class="bg-light-gray font-11" rowspan="2" style="border-bottom:solid 1px #dddddd">
            상태/완료일
        </td>
        <!-- 완료일 스케쥴 -->
        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_MAIN_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
            <td class="" v-if="$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])">
                <div class="dp-flex-center dp-flex">
                    <div class="dp-flex dp-flex-center">
                        <?php if( !in_array($mainSchedule,['plan','proposal','order','productionOrder']) ) { ?>
                        <complete-template2 :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
                        <?php }else{ ?>
                            <div v-show="!isModify">
                                <complete-template2 :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
                            </div>
                            <div v-show="isModify">
                                자동등록
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </td>
        <?php } ?>
    </tr>
    <tr>
        <!--기획-->
        <?php include 'ims_view2_all_schedule_func_plan.php'?>
        <!--제안-->
        <?php include 'ims_view2_all_schedule_func_proposal.php'?>
        <!--샘플발송 -->
        <td style="border-top:none !important;" v-if="$.isEmpty(project['txSampleInform'])"></td>
        <!--작지사양서-->
        <?php include 'ims_view2_all_schedule_func_work.php'?>
        <!--발주-->
        <?php include 'ims_view2_all_schedule_func_order.php'?>
    </tr>
</table>




<div v-show="false">
    price: {% project.priceStatus %} /
    cost: {% project.costStatus %} /
    assort: {% project.assortApproval %} /
    작지: {% project.workStatus %} /
    사양: {% project.customerOrderConfirm %}
</div>
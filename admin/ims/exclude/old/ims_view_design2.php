<!--업무 스케쥴-->
<div class="col-xs-12" >
    <div class="col-xs-12" id="layoutOrderViewOrderInfoArea">

        <div v-if="typeof project.scheduleStatus != 'undefined' && !$.isEmpty(project.scheduleStatus)" >
            <div class="table-title gd-help-manual">
                <div class="flo-left area-title ">
                    <span class="godo">업무 진행 상태</span>
                </div>
                <div class="flo-right">

                </div>
            </div>

            <!--스케쥴 집계-->
            <div >
                <table class="table ims-schedule-table w100 table-default-center table-fixed table-th-height30 mgb10">
                    <tr>
                        <th class="">제안 단계</th>
                        <th class="">샘플 단계</th>
                        <th class="">발주 관리</th>
                        <th class="">Q&B</th>
                        <th class="">판매가</th>
                        <th class="">생산가</th>
                        <th class="">생산 발주 D/L</th>
                        <th class="text-danger">고객 납기</th>
                    </tr>
                    <tr>
                        <td :class="'ims-step'+project.scheduleStatus.stepProposal.lastStatusCode">
                            <div class="hover-btn cursor-pointer" @click="showSchedule()">
                                {% project.scheduleStatus.stepProposal.lastStatus %}
                            </div>
                            <div class="line-gray w-95p mgt10 mgb5"></div>
                            <div class="hover-btn cursor-pointer" @click="showSchedule()">
                                {% project.scheduleStatus.stepProposal.completeCnt %}/{% project.scheduleStatus.stepProposal.checkCnt %}
                            </div>
                        </td>
                        <td :class="'ims-step'+project.scheduleStatus.stepSample.lastStatusCode">
                            <div class="hover-btn cursor-pointer" @click="showSchedule()">
                                {% project.scheduleStatus.stepSample.lastStatus %}
                            </div>
                            <div class="line-gray w-95p mgt10 mgb5"></div>
                            <div class="hover-btn cursor-pointer" @click="showSchedule()">
                                {% project.scheduleStatus.stepSample.completeCnt %}/{% project.scheduleStatus.stepSample.checkCnt %}
                            </div>
                        </td>
                        <td :class="'ims-step'+project.scheduleStatus.stepOrder.lastStatusCode">
                            <div class="hover-btn cursor-pointer" @click="showSchedule()">
                                {% project.scheduleStatus.stepOrder.lastStatus %}
                            </div>
                            <div class="line-gray w-95p mgt10 mgb5"></div>
                            <div class="hover-btn cursor-pointer" @click="showSchedule()">
                                {% project.scheduleStatus.stepOrder.completeCnt %}/{% project.scheduleStatus.stepOrder.checkCnt %}
                            </div>
                        </td>
                        <td class="border-right-gray" class="text-danger">
                            <div class="cursor-pointer hover-btn" @click="showQbSchedule()">퀄리티 수배 중</div>
                            <div class="line-gray w-95p mgt10"></div>
                            <div class="mgt5" @click="isQbDetail=true">3/4</div>
                        </td>
                        <td  class="">
                            <div class="" @click="showSchedule()">
                                <div class="pd5 sl-green" v-show="'p' !== project['prdPriceApproval']">준비중</div>
                                <div class="pd5 sl-green" v-show="'p' === project['prdPriceApproval']">결재 완료</div>
                            </div>
                            <div class="line-gray w-95p mgt10 mgb5"></div>
                            2/2 (등록완료)
                            <!--
                            <div class="btn btn-sm btn-red btn-red-line2">판매가 결재요청</div>
                            -->
                        </td>
                        <td  class="">
                            <div class="" @click="showSchedule()">
                                <div class="pd5 sl-green" v-show="'p' !== project['prdCostApproval']">준비중</div>
                                <div class="pd5 sl-green" v-show="'p' === project['prdCostApproval']">결재 완료</div>
                            </div>
                            <div class="line-gray w-95p mgt10 mgb5"></div>
                            2/2 (견적완료)
                        </td>
                        <td  class="" >
                            <div class="" @click="showSchedule()">25/01/27(월)</div>
                            <div class="line-gray w-95p mgt10"></div>
                            <div class="mgt5 text-green">25일남음</div>
                        </td>
                        <td  class="">
                            <div class="" @click="showSchedule()">25/06/13(금)</div>
                            <div class="line-gray w-95p mgt10"></div>
                            <div class="mgt5 text-green">197일남음</div>
                        </td>
                    </tr>
                </table>
            </div>


            <!--퀄리티비티 QB 스케쥴 관리-->
            <div v-show="isQbDetail" class="pd5" style="border:solid 1px #e0e0e0; border-radius: 10px; background-color: #fdfdfd">
                <div>
                    <div class="dp-flex dp-flex-between mgb5">
                        <div>
                            <span class="fnt-godo font-14 sl-blue mgr10">
                                <i class="fa fa-sort-desc fa-2x sl-blue" aria-hidden="true"></i>
                                QB 정보/스케쥴 관리
                            </span>
                            <!--
                            <div class="btn btn-white" @click="isScheduleModify=true" v-show="!isScheduleModify">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                스케쥴 일괄 수정
                            </div>
                            -->
                            <div class="btn btn-red btn-red2" @click="saveSchedule()" v-show="isScheduleModify">스케쥴 저장</div>
                            <div class="btn btn-white" @click="isScheduleModify=false" v-show="isScheduleModify">스케쥴 수정 취소</div>
                        </div>
                        <div><!--닫기-->
                            <i class="fa fa-times fa-2x cursor-pointer hover-btn" aria-hidden="true" @click="isQbDetail=false"></i>
                        </div>
                    </div>

                    <table class="table ims-schedule-table ims-sub-schedule-table w-100p table-fixed table-default-center table-th-height35 table-td-height35 table-pd-3" >
                        <colgroup>
                            <col style="width: 80px;">
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <tr>
                            <th rowspan="2" class="border-right-gray-imp border-bottom-gray-imp">구분</th>
                            <th colspan="9" class="border-right-gray-imp">퀄리티 현황</th>
                            <th colspan="5" class="border-right-gray-imp">BT 현황</th>
                        </tr>
                        <tr>
                            <th class="border-bottom-gray-imp">상품명</th>
                            <th class="border-bottom-gray-imp">퀄리티 상태</th>
                            <th class="border-bottom-gray-imp">의뢰처</th>
                            <th class="border-bottom-gray-imp">요청일</th>
                            <th class="border-bottom-gray-imp">완료 예정일</th>
                            <th class="border-bottom-gray-imp">부위</th>
                            <th class="border-bottom-gray-imp">원단 정보</th>
                            <th class="border-bottom-gray-imp">확정 정보</th>
                            <th class="border-bottom-gray-imp border-right-gray-imp">제조국</th>

                            <th class="border-bottom-gray-imp">BT 상태</th>
                            <th class="border-bottom-gray-imp">의뢰처</th>
                            <th class="border-bottom-gray-imp">요청일</th>
                            <th class="border-bottom-gray-imp">완료 예정일</th>
                            <th class="border-bottom-gray-imp border-right-gray-imp">확정 정보</th>
                        </tr>
                        <tr>
                            <th class="border-right-gray-imp" rowspan="2">1</th>
                            <td class="" rowspan="2">25 SP 팬츠</td>
                            <td class="sl-blue">수배 중</td>
                            <td class="">하나어패럴</td>
                            <td class="">25/01/31(금)</td>
                            <td class="">25/02/05(수)</td>
                            <td class="">G 메인</td>
                            <td class="font-11">N/NC 40'S SPAN N69 C25 SP6 그레이</td>
                            <td class="">-</td>
                            <td class=" border-right-gray-imp">중국</td>
                            <td class="sl-blue">진행중</td>
                            <td class="">하나어패럴</td>
                            <td class="">25/01/31(금)</td>
                            <td class="">25/02/05(수)</td>
                            <td class=" border-right-gray-imp">-</td>
                        </tr>
                        <tr>
                            <td class="text-danger">반려<div class="text-muted">25/02/05(수)</div></td>
                            <td class="">하나어패럴</td>
                            <td class="">25/01/31(금)</td>
                            <td class="">25/02/05(수)</td>
                            <td class="">A 배색</td>
                            <td class="font-11">N/NC 40'S SPAN N69 C25 SP6 블랙</td>
                            <td class="">-</td>
                            <td class=" border-right-gray-imp">중국</td>
                            <td class="">-</td>
                            <td class="">하나어패럴</td>
                            <td class="">25/01/31(금)</td>
                            <td class="">25/02/05(수)</td>
                            <td class=" border-right-gray-imp">-</td>
                        </tr>
                        <tr>
                            <th class="border-bottom-light-gray-imp border-right-gray-imp" rowspan="3">3</th>
                            <td class="border-bottom-light-gray-imp" rowspan="3">25 SP 조끼</td>
                            <td class="sl-green">수배 완료<div class="text-muted">25/02/05(수)</div></td>
                            <td class="">하나어패럴</td>
                            <td class="">25/01/31(금)</td>
                            <td class="">25/02/05(수)</td>
                            <td class="">G 메인</td>
                            <td class="">시장 원단</td>
                            <td class="">시장 원단</td>
                            <td class=" border-right-gray-imp">시장</td>
                            <td class="">진행중</td>
                            <td class="">하나어패럴</td>
                            <td class="">25/01/31(금)</td>
                            <td class="">25/02/05(수)</td>
                            <td class=" border-right-gray-imp">-</td>
                        </tr>
                        <tr>
                            <td class="bg-light-yellow">진행대기</td>
                            <td class="bg-light-yellow">하나어패럴</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow">A 배색 메쉬</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow border-right-gray-imp">-</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow">-</td>
                            <td class="bg-light-yellow border-right-gray-imp">-</td>
                        </tr>
                        <tr>
                            <td class="bg-light-green">수배완료</td>
                            <td class="bg-light-green">하나어패럴</td>
                            <td class="bg-light-green">25/02/01(월)</td>
                            <td class="bg-light-green">25/02/03(수)</td>
                            <td class="bg-light-green">G 메인</td>
                            <td class="bg-light-green">50D 듀스포 라미 코딩 P100 네이비</td>
                            <td class="bg-light-green">50D 듀스포 라미 P100 네이비</td>
                            <td class="bg-light-green border-right-gray-imp">중국</td>
                            <td class="bg-light-green sl-green">확정<div>25/02/03(수)</div></td>
                            <td class="bg-light-green">하나어패럴</td>
                            <td class="bg-light-green">25/02/01(월)</td>
                            <td class="bg-light-green">25/02/03(수)</td>
                            <td class="bg-light-green border-right-gray-imp sl-green">2차"A"</td>
                        </tr>
                    </table>
                </div>

                <div class="ta-c">
                    <div class="btn btn-white" @click="isQbDetail=false">▲ QB 정보/스케쥴 닫기</div>
                </div>

            </div>

            <!--스케쥴 상세-->
            <div v-show="isScheduleDetail" class="pd5" style="border:solid 1px #e0e0e0; border-radius: 10px; background-color: #fdfdfd">
                <!--결재/승인-->
                <div>
                    <div class="dp-flex dp-flex-between mgb5">
                        <div>
                            <span class="fnt-godo font-14 sl-blue mgr10">
                                <i class="fa fa-sort-desc fa-2x sl-blue" aria-hidden="true"></i>
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
                                <col class="w-10p" />
                                <col class="w-15p" />
                                <col class="w-15p" />
                                <col  />
                                <col  />
                            </colgroup>
                            <tr>
                                <th class="border-right-gray">구분</th>
                                <th class="border-right-gray">기획서</th>
                                <th class="border-right-gray">제안서</th>
                                <th class="border-right-gray">아소트</th>
                                <th class="border-right-gray">판매가</th>
                                <th class="border-right-gray">생산가</th>
                                <th class="border-right-gray">작업지시서</th>
                                <th >사양서</th>
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
                                    <div class="mgt3" v-if="'n'===project.assortApproval">
                                        <div class=" btn btn-sm btn-black-line" @click="visibleAssortSendUrl=true">
                                            아소트 입력URL 전달
                                        </div>
                                    </div>
                                </td>
                                <!--판매가 결재-->
                                <td class="border-right-gray" >

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
                                <td class="border-right-gray" >

                                    <div class="btn btn-sm btn-red btn-red-line2"
                                         v-if="'n' === project.prdCostApproval && ( 0 >= projectApprovalInfo.salePrice.sno || $.isEmpty(projectApprovalInfo.salePrice.sno) )"
                                         @click="openApprovalWrite(customer.sno, project.sno, 'cost')">생산가 결재 요청</div>

                                    <approval-template2
                                            :project="project"
                                            :approval="projectApprovalInfo"
                                            :confirm-type="'cost'"
                                            :confirm-field="'prdCostApproval'"
                                            :memo-field="'unused'"
                                    ></approval-template2>
                                </td>
                                <!--작지-->
                                <td class="border-right-gray" >
                                    진행
                                </td>
                                <!--사양서-->
                                <td class="border-right-gray" >
                                    <div class="btn btn-sm btn-white">열기</div>
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
                                <td class="border-right-gray">
                                    <div class="w-100p text-left set-dropzone-type1">
                                        <file-upload :file="fileList.fileProposal" :id="'fileProposal'" :project="project" :accept="'p'===project.proposalConfirm" ></file-upload>
                                    </div>
                                </td>
                                <td class="border-right-gray">
                                    <button class="badge-button gray-button mgl10" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`);">
                                        고객 화면
                                    </button>
                                </td>
                                <td class="border-right-gray"></td>
                                <td class="border-right-gray">

                                </td>
                                <td class="border-right-gray"></td>
                                <td class="border-right-gray">
                                    <button class="badge-button gray-button mgl10" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`);">
                                        고객 화면
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="ta-c">
                    <div class="btn btn-white" @click="isScheduleDetail=false">▲ 결재/승인 닫기</div>
                </div>

            </div>

        </div>

    </div>
</div>

<!--상품정보-->
<div class="col-xs-12 mgt20">
    <!--예상스타일-->
    <div class="col-xs-12 js-order-view-receiver-area">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                상품 정보
            </div>
            <div class="flo-right">
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div class="js-layout-order-view-receiver-info">
            <?php include 'basic_view/_style2.php'?>
        </div>
    </div>

    <!-- 기획/선호도 정보 -->
    <div class="col-xs-12 js-order-view-receiver-area display-none">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                기획/선호도 정보
            </div>
            <div class="flo-right">
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div class="js-layout-order-view-receiver-info">

            <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                <tr>
                    <th style="border-right:solid 1px #aaaaaa">구분</th>
                    <th style="border-right:solid 1px #aaaaaa">진행 형태</th>
                    <th style="border-right:solid 1px #aaaaaa">고객사 샘플</th>
                    <th style="border-right:solid 1px #aaaaaa">사이즈 스펙</th>
                    <th style="border-right:solid 1px #aaaaaa">스타일</th>
                    <th style="border-right:solid 1px #aaaaaa">원단</th>
                    <th style="border-right:solid 1px #aaaaaa">부자재</th>
                    <th style="border-right:solid 1px #aaaaaa">인쇄</th>
                    <th style="border-right:solid 1px #aaaaaa">기능</th>
                    <th style="border-right:solid 1px #aaaaaa">컬러</th>
                    <th style="border-right:solid 1px #aaaaaa">불편사항</th>
                </tr>
                <tr>
                    <td style="border-right:solid 1px #aaaaaa">하계 카라티</td>
                    <td style="border-right:solid 1px #aaaaaa">
                        신규 기획
                    </td>
                    <td style="border-right:solid 1px #aaaaaa">제공 불가</td>
                    <td style="border-right:solid 1px #aaaaaa">신규 제안</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                </tr>
                <tr>
                    <td style="border-right:solid 1px #aaaaaa">하계 바지</td>                    <td style="border-right:solid 1px #aaaaaa">신규 기획</td>                    <td style="border-right:solid 1px #aaaaaa">제공 불가</td>                    <td style="border-right:solid 1px #aaaaaa">신규 제안</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                </tr>
                <tr>
                    <td style="border-right:solid 1px #aaaaaa">하계 조끼</td><td style="border-right:solid 1px #aaaaaa">소재 개선</td><td style="border-right:solid 1px #aaaaaa">제공 불가</td>                    <td style="border-right:solid 1px #aaaaaa">신규 제안</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                </tr>
                <tr>
                    <td style="border-right:solid 1px #aaaaaa">춘추 점퍼</td><td style="border-right:solid 1px #aaaaaa">기존 동일</td><td style="border-right:solid 1px #aaaaaa">추후 반납</td>                    <td style="border-right:solid 1px #aaaaaa">신규 제안</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                </tr>
                <tr>
                    <td style="border-right:solid 1px #aaaaaa">동계 점퍼</td><td style="border-right:solid 1px #aaaaaa">기존 동일</td><td style="border-right:solid 1px #aaaaaa">훼손 가능</td>                    <td style="border-right:solid 1px #aaaaaa">신규 제안</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                    <td style="border-right:solid 1px #aaaaaa">고객사 제공 자료 참고</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- 고객사 정보 -->
<div class="col-xs-12 mgt20">
    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">고객사 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
                <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyOrderInfo">저장</button>
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
                    <th>고객명</th>
                    <td>
                        {% customer.customerName %}
                    </td>
                    <th>3PL</th>
                    <td>
                        사용
                    </td>
                </tr>
                <tr>
                    <th>업종 / 근무환경</th>
                    <td >
                        가구 운송업
                    </td>
                    <th>폐쇄몰</th>
                    <td>
                        사용
                    </td>
                </tr>
                <tr>
                    <th>사원수</th>
                    <td colspan="3">
                        232
                    </td>
                </tr>
                <tr>
                    <th>담당자</th>
                    <td colspan="3">
                        박혜정 디자이너
                    </td>
                </tr>
                <tr>
                    <th>연락처</th>
                    <td colspan="3">
                        010-1234-1234
                    </td>
                </tr>
                <tr>
                    <th>E-MAIL</th>
                    <td colspan="3">
                        hyejeong_par@fusys.com
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
                <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
                <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyOrderInfo">저장</button>
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
                        보통
                    </td>
                </tr>
                <tr>
                    <th>업체 변경 사유</th>
                    <td colspan="99">
                        리브랜딩으로 시공복 디자인 변경
                    </td>
                </tr>
                <tr>
                    <th>경쟁 업체</th>
                    <td colspan="99">
                        25/01/01 (30일 남음)
                    </td>
                </tr>
                <tr>
                    <th>의사 결정 라인</th>
                    <td>
                        대표이사
                    </td>
                    <th>노사 합의 여부</th>
                    <td>
                        있음
                    </td>
                </tr>
                <tr>
                    <th>폐쇄몰 진행 여부</th>
                    <td colspan="99">
                        추후 협의 (우선 협상 업체 선정 후 협의)
                    </td>
                </tr>
                <tr>
                    <th>이해 관계</th>
                    <td colspan="99">
                        유대 관계는 없지만 이노버 폐쇄몰 등 서비스에 만족도 높음
                    </td>
                </tr>
                <tr>
                    <th>기존 업체</th>
                    <td colspan="99">
                        확인 불가
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 기획 / 제안 정보 -->
<div class="col-xs-12 mgt20">
    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">기획 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
                <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyOrderInfo">저장</button>
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
                    <th>업체 선정 방법</th>
                    <td colspan="3">
                        전체 임직원 내부 품평회
                    </td>
                </tr>
                <tr>
                    <th>업체 선정 기준</th>
                    <td colspan="3">
                        배점 기준 선정 (정성 80, 정량 20)
                    </td>
                </tr>
                <tr>
                    <th>계약 기간</th>
                    <td colspan="3">
                        2년
                    </td>
                </tr>
                <tr>
                    <th>발주 수량 변동</th>
                    <td colspan="3">
                        초도 지급 이후 감소
                    </td>
                </tr>
                <tr>
                    <th>납기 일자 변경 여부</th>
                    <td colspan="3">
                        기타 (입찰 4월 중, 우선 협상 업체 선정시 납기 협의 가능)
                    </td>
                </tr>
                <tr>
                    <th>세탁 방법</th>
                    <td colspan="3">
                        개별 세탁
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">제안 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
                <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyOrderInfo">저장</button>
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
                    <th>제안 컨셉 수</th>
                    <td colspan="99">
                        1개 컨셉 (샘플 2세트)
                    </td>
                </tr>
                <tr>
                    <th>제안서 필수 항목</th>
                    <td colspan="99">
                        운영 관리, 긴급 생산 운영 방안, 폐쇄몰, 사후 관리(A/S등)
                    </td>
                </tr>
                <tr>
                    <th>로고 형태</th>
                    <td colspan="99">
                        본사/협력사 별도 로고
                    </td>
                </tr>
                <tr>
                    <th>명찰 구분</th>
                    <td colspan="99">
                        명찰 오바르크 부착
                    </td>
                </tr>
                <tr>
                    <th class="sl-blue">단가 인상 가능 여부</th>
                    <td colspan="99">
                        불가능, 타겟 단가로 진행
                    </td>
                </tr>
                <tr>
                    <th>샘플비 기준</th>
                    <td colspan="99">
                        전체 무상 (벌수 상관 없음)
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-xs-12 mgt30">

    <div class="col-xs-12">
        <div class="table-title gd-help-manual">
            <div class="flo-left">제안서</div>
            <div class="flo-right">
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div v-if="!$.isEmpty(fileList.fileProposal)" class="pd7" style="border:solid 1px #d1d1d1;">
            <embed :src="'<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath" type="application/pdf" width="100%" height="850px" style="border-radius: 10px"  v-if="fileList.fileProposal.files.length > 0" />
        </div>
    </div>

</div>
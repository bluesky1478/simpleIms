<div class="col-xs-6" >
    <div class="table-title ">
        <div class="flo-left" >프로젝트 및 승인 정보</div>
        <div class="flo-right " v-if="'n' == items.tempFl && !$.isEmpty(documentSno) ">
            <div class="btn btn-sm btn-white"  @click="openAcceptHistory()">승인이력</div>
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"  />
                <col class="width-xl"  />
                <col class="width-md"  />
                <col class="width-xl"  />
            </colgroup>
            <tr >
                <th>승인정보</th>
                <td colspan="3" style="padding:0px">
                    <table class="table table-cols apply-table">
                        <tr>
                            <th class="text-center" >작성</th>
                            <th class="text-center" v-for="(item, index) in items.applyManagers" :key="index">
                                {% item.title %}
                                <div class="accept-button-area">
                                    <button type="button" class="btn btn-sm btn-info btn-ssm" @click="acceptDoc('q', index)" v-if="!$.isEmpty(documentSno) && <?=$mySno?> == items.regManagerSno && ( item.status === 'n' || item.status === 'r' )">승인요청</button>
                                    <span v-if="<?=$mySno?> == item.managerSno">
                                        <button type="button" class="btn btn-sm btn-success btn-ssm" @click="acceptDoc('y', index)" >승인</button>
                                        <button type="button" class="btn btn-sm btn-danger btn-ssm"  @click="acceptDoc('r', index)">반려</button>
                                    </span>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <td class="text-center" >{% items.regManagerName %}</td>
                            <td class="text-center" v-for="(item, index) in items.applyManagers" :key="index">
                                {% item.managerNm %}
                                <div class="rounded-circle bg-success" v-if="'y' === item.status">{% item.statusKr %}</div>
                                <div class="rounded-circle bg-danger" v-else-if="'r' === item.status">{% item.statusKr %}</div>
                                <div class="rounded-circle bg-light" v-else>{% item.statusKr %}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th>번호/버전</th>
                <td>
                    <div class="col-form-label" v-if="0 == items.version">
                        <b class="text-danger font-15">{% items.projectData.sno %}</b> - 신규등록
                        <b class="text-color-orange" v-if="items.sno > 0 && 'y' === items.tempFl">(임시저장)</b>
                    </div>
                    <div class="col-form-label" v-else>
                        <b class="text-danger font-15">{% items.projectData.sno %}</b>
                        <small class="text-muted">(버전 {% items.version %})</small>
                    </div>
                </td>
                <th>프로젝트 타입</th>
                <td>
                    <div>{% items.projectData.projectTypeKr %}</div>
                </td>
            </tr>
            <tr>
                <th>영업담당자</th>
                <td>
                    {% items.projectData.salesManagerName %}
                </td>
                <th>디자인 담당자</th>
                <td >
                    {% items.projectData.designManagerName %}
                </td>
            </tr>
            <tr v-if="!$.isEmpty(items.docDefaultInfo.mailLink)">
                <th>메일 발송 정보</th>
                <td colspan="3">
                    <div>
                        <input type="text" class="form-control inline-block w100p" placeholder="수신자명" v-model="items.docData.mailReceiverName" >
                        <input type="text" class="form-control inline-block w250p" placeholder="수신자 E-Mail" v-model="items.docData.sendEmail" >
                    </div>
                    <div v-if="!$.isEmpty(items.projectData.companySno) && 0 != items.projectData.companySno" class="mgt10">
                        <button type="button" class="btn btn-sm btn-gray" @click="openManagerListModal(items.projectData.companySno, selectManagerList)">수신자 선택</button>
                        <span v-show="'n' == items.tempFl && !$.isEmpty(items.sno)">
                            <button type="button" class="btn btn-sm btn-gray" @click="window.open('<?=$documentCustomerPreviewUrl?>')">미리보기</button>
                            <button type="button" class="btn btn-sm btn-gray" @click="openMailHistoryModal()">발송이력 확인</button>
                            <button type="button" class="btn btn-sm btn-red"  @click="sendMail(items.docData, items.sno)">메일발송</button>
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-xs-6" >
    <div class="table-title ">
        <div class="flo-left" >고객사 정보</div>
        <div class="flo-right" >
        </div>
    </div>
    <div class="" >
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>업체명</th>
                <td>
                    {% items.projectData.companyName %}
                    <small class="text-muted">({% items.projectData.companyDivKr %})</small>
                </td>
            </tr>
            <tr>
                <th>담당자</th>
                <td>
                    <div v-for="(item, index) in items.projectData.companyManagers" v-html="item.html"></div>
                </td>
            </tr>
            <tr>
                <th>주소</th>
                <td style="height:148px" v-if="!$.isEmpty(items.docDefaultInfo.mailLink)">
                    {% items.projectData.companyData.address %}
                </td>
                <td style="height:75px" v-if="$.isEmpty(items.docDefaultInfo.mailLink)">
                    {% items.projectData.companyData.address %}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-xs-6" >
    <div class="table-title gd-help-manual">
        <div class="flo-left">디자인 컨셉 <small class="text-muted">(미팅보고서 {% items.projectData.meetingData.version %}차 기준)</small></div>
        <div class="flo-right"></div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>디자인 컨셉 색상</th>
                <td>
                    <ul class="list-inline m-l-5">
                        <li class="list-inline-item" style="width:45%"   v-for="(item, index) in items.projectData.meetingData.docData.designColor" :key="index">
                            <div class="m-b-0 d-flex align-items-center">
                                <div class="m-0 ">
                                    <div :style="'background-color:#' + item.value " class="doc-color-icon"></div>
                                    {% item.name %}
                                </div>
                            </div>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <th>디자인 컨셉</th>
                <td v-if="!$.isEmpty(items.projectData.meetingData.docData.designConcept)">
                    {% items.projectData.meetingData.docData.designConcept.join(',') %}
                </td>
                <td v-else></td>
            </tr>
            <tr>
                <th>기존 샘플 제공</th>
                <td>
                    {% items.projectData.meetingData.docData.sampleSupport %}
                </td>
            </tr>
            <tr>
                <th>샘플비</th>
                <td>
                    {% items.projectData.meetingData.docData.sampleCostType %}
                </td>
            </tr>
            <tr>
                <th>이노버 관심 샘플</th>
                <td>
                    {% items.projectData.meetingData.docData.interestSample %}
                </td>
            </tr>
            <tr>
                <th>포트폴리오 희망일</th>
                <td>
                    {% items.projectData.meetingData.docData.portfolioDt %}
                </td>
            </tr>
            <tr>
                <th>샘플 제작 희망일</th>
                <td>
                    {% items.projectData.meetingData.docData.sampleDt %}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-xs-6" >
    <div class="table-title gd-help-manual">
        <div class="flo-left">업체 선정 기준 <small class="text-muted">(미팅보고서 {% items.projectData.meetingData.version %}차 기준)</small></div>
        <div class="flo-right"></div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>선정 기준</th>
                <td>
                    {% items.projectData.meetingData.docData.selectCriteria %}
                </td>
            </tr>
            <tr>
                <th>선정요소</th>
                <td>
                    {% items.projectData.meetingData.docData.selectFactor %}
                </td>
            </tr>
            <tr>
                <th>생산기간 안내</th>
                <td>
                    사양서 확정일로부터 <b>{% items.projectData.meetingData.docData.producePeriod %}</b> 일 소요
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-xs-6" >
    <div class="table-title gd-help-manual">
        <div class="flo-left">서비스 선호도 <small class="text-muted">(미팅보고서 {% items.projectData.meetingData.version %}차 기준)</small></div>
        <div class="flo-right">
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>폐쇄몰</th>
                <td>
                    {% items.projectData.meetingData.docData.preferMall %}
                </td>
            </tr>
            <tr>
                <th>근무환경</th>
                <td>
                    {% items.projectData.meetingData.docData.workEnv %}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-xs-12" >
    <div class="table-title">스타일별 단가 및 희망 납기 <small class="text-muted">(미팅보고서 {% items.projectData.meetingData.version %}차 기준)</small> </div>
    <div>
        <table class="table table-cols">
            <thead>
            <tr>
                <th class="text-center">스타일</th>
                <th class="text-center">예상수량</th>
                <th class="text-center">현재단가</th>
                <th class="text-center">타겟단가</th>
                <th class="text-center">진행형태</th>
                <th class="text-center">예상발주</th>
                <th class="text-center">희망납기</th>
                <th class="text-center">불편사항</th>
            </tr>
            </thead>
            <tbody v-for="(item, index) in items.projectData.meetingData.docData.hopeData" :key="index">
            <tr>
                <td class="text-center">{% item.style %}</td>
                <td class="text-center">{% item.count.number_format() %}</td>
                <td class="text-center">{% item.currentPrice.number_format() %}</td>
                <td class="text-center">{% item.targetPrice.number_format() %}</td>
                <td class="text-center">{% item.progMode %}</td>
                <td class="text-center">{% item.orderDt %}</td>
                <td class="text-center">{% item.deliveryDt %}</td>
                <td class="text-center">{% item.discomfort %}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
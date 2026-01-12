<!-- 종합현황  -->
<div>
    <div class="row" >
        <!--기본정보-->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 기본 정보</div>
                <div class="flo-right">
                    <div class="btn btn-white">상세</div>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>
                            고객사명
                        </th>
                        <td class="font-14">
                            {% items.customerName %} <span class="font-11 text-muted">({% items.styleCode %})</span>
                            <!--<div class="btn btn-white btn-sm">상세</div>-->
                        </td>
                        <th>프로젝트 연도/시즌</th>
                        <td class="font-14">
                            {% project.projectYear %} {% project.projectSeason %}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            프로젝트 상태
                        </th>
                        <td class="font-14 bold">
                            {% project.projectStatusKr %}
                            <div class="btn btn-white btn-sm">변경이력</div>
                        </td>
                        <th>
                            프로젝트 타입
                        </th>
                        <td>
                            {% project.projectTypeKr %}
                        </td>
                    </tr>
                    <tr>
                        <th>프로젝트 별칭</th>
                        <td >
                            {% project.projectName %}
                        </td>
                        <th>제안형태</th>
                        <td >
                            <label class="checkbox-inline">
                                기획서<span class="ims-recommend ims-recommend1">기</span>
                            </label>
                            <label class="checkbox-inline">
                                샘플<span class="ims-recommend ims-recommend4">샘</span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>영업 담당자</th>
                        <td >
                            {% project.salesManagerNm %}
                        </td>
                        <th>디자인 담당자</th>
                        <td >
                            {% project.designManagerNm %}
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>

        </div>

        <!--프로젝트 납기 정보-->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    납기 정보
                </div>
                <div class="flo-right">
                    <!--<div class="btn btn-white">상세</div>-->
                </div>
            </div>
            <div>
                <table class="table table-cols table-pd-5">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>고객 제안 마감일</th>
                        <td class="font-14">
                            {% $.formatShortDate(project.recommendDt) %}
                        </td>
                        <th>발주D/L</th>
                        <td class="font-14">
                            {% $.formatShortDate(project.customerOrderDeadLine) %}
                        </td>
                    </tr>
                    <tr>
                        <th>고객 발주일</th>
                        <td class="font-14">
                            {% $.formatShortDate(project.customerOrderDt) %}
                        </td>
                        <th>고객 납기일</th>
                        <td class="font-14 text-danger">
                            {% $.formatShortDate(project.customerDeliveryDt) %}
                        </td>
                    </tr>
                    <tr>
                        <th>이노버 발주일</th>
                        <td class="font-14">
                            {% $.formatShortDate(project.msOrderDt) %}
                        </td>
                        <th>이노버 납기일</th>
                        <td class="font-14 sl-blue">
                            {% $.formatShortDate(project.msDeliveryDt) %}
                        </td>
                    </tr>
                    <tr>
                        <th>3PL/폐쇄몰 사용</th>
                        <td>
                            <i class="fa fa-university fa-lg" aria-hidden="true" ></i> 3PL : 사용
                            <i class="fa fa-internet-explorer fa-lg mgl15" aria-hidden="true" ></i> 폐쇄몰 : 사용
                            <div class="mgt5">
                                <a href="http://imsftp.msinnover.com:8027/ims/download.php?name=blank.txt&amp;path=/data/project/65c2e800549ef_240207111632" class="text-blue">MAX바코드_240429.xlsx</a>
                            </div>
                        </td>
                        <th>분류패킹</th>
                        <td class="">
                            <div class="font-14">진행</div>
                            <div class="mgt5">
                                <a href="http://imsftp.msinnover.com:8027/ims/download.php?name=blank.txt&amp;path=/data/project/65c2e800549ef_240207111632" class="text-blue">MAX분류패킹_240429.xlsx</a>
                            </div>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">

            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 진행 상태</div>
                <div class="flo-right">
                    <!--<div class="btn btn-red btn-red-line2 hover-btn">상담등록</div>-->
                </div>
            </div>
            <div>

                <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                    <colgroup>
                        <col >
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                        <col style="width:8.7%">
                    </colgroup>
                    <tr>
                        <th rowspan="2"></th>
                        <th colspan="4" style="background-color:#d6e7ff !important;">영업</th>
                        <th colspan="3" style="background-color:#ffffd3 !important;">디자인</th>
                        <th colspan="4" style="background-color:#f0d3ff !important;">생산</th>
                    </tr>
                    <tr>
                        <th style="background-color:#d6e7ff !important;">기획</th>
                        <th style="background-color:#d6e7ff !important;">제안</th>
                        <th style="background-color:#d6e7ff !important;">견적서/판매가</th>
                        <th style="background-color:#d6e7ff !important;">영업확정서</th>

                        <th style="background-color:#ffffd3 !important;">샘플&테스트</th>
                        <th style="background-color:#ffffd3 !important;">퀄리티/BT</th>
                        <th style="background-color:#ffffd3 !important;">작지/사양서</th>

                        <th style="background-color:#f0d3ff !important;">생산가 확정</th>
                        <th style="background-color:#f0d3ff !important;">생지확보D/L</th>
                        <th style="background-color:#f0d3ff !important;">발주</th>
                        <th style="background-color:#f0d3ff !important;">공장납기</th>
                    </tr>
                    <tr>
                        <th>예정</th>
                        <td class="td-sales">
                            {% project.planDtShort %}
                        </td>
                        <td class="td-sales">
                            {% project.proposalDtShort %}
                        </td>
                        <td class="td-sales">
                            {% $.formatShortDate(project.sampleStartDt) %}
                        </td>
                        <td class="td-sales">
                            -
                        </td>
                        <td class="td-design">
                            {% project.planDtShort %}
                        </td>
                        <td class="td-design">
                            {% project.proposalDtShort %}
                        </td>
                        <td class="td-design">
                            {% $.formatShortDate(project.sampleStartDt) %}
                        </td>
                        <td class="td-production">
                            {% project.proposalDtShort %}
                        </td>
                        <td class="td-production">
                            {% project.proposalDtShort %}
                        </td>
                        <td class="td-production">
                            {% project.planDtShort %}
                        </td>
                        <td class="td-production">
                            {% $.formatShortDate(project.sampleStartDt) %}
                        </td>
                    </tr>
                    <tr>
                        <th>완료</th>
                        <td class="td-sales">
                            {% project.planEndDtShort %}
                        </td>
                        <td class="td-sales">
                            {% project.planEndDtShort %}
                        </td>
                        <td class="td-sales">
                            {% project.proposalEndDtShort %}
                        </td>
                        <td class="td-sales">
                            -
                        </td>
                        <td class="td-design">
                            03/12(화)
                        </td>
                        <td class="td-design">
                            -
                        </td>
                        <td class="td-design">
                            -
                        </td>
                        <td class="td-production">
                            {% project.planEndDtShort %}
                        </td>
                        <td class="td-production">
                            {% project.planEndDtShort %}
                        </td>
                        <td class="td-production">
                            {% project.proposalEndDtShort %}
                        </td>
                        <td class="td-production">
                            -
                        </td>
                    </tr>
                    <tr>
                        <th>상태</th>
                        <td class="td-sales">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">승인완료</span>
                            <!--
                            <span :class="setAcceptClass(project.planConfirm)" v-html="project.planConfirmKr"></span>
                            -->
                        </td>
                        <td class="td-sales">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">승인완료</span>
                            <!--<span :class="setAcceptClass(project.proposalConfirm)" v-html="project.proposalConfirmKr"></span>-->
                        </td>
                        <td class="td-sales">
                            <i aria-hidden="true" class="fa fa-lg fa-times-circle text-danger"></i>
                            <span class="text-danger">반려</span>
                            <!--<span :class="setAcceptClass(project.sampleConfirm)" v-html="project.sampleConfirmKr"></span>-->
                        </td>
                        <td class="td-sales">
                            -
                            <!--<span v-show="'y' === project.customerOrderConfirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerOrderConfirm">미확정</span>-->
                        </td>
                        <td class="td-design">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">완료</span>
                            <!--<span v-show="'y' === project.customerOrder2Confirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerOrder2Confirm">미확정</span>-->
                        </td>
                        <td class="td-design">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">완료</span>
                            <!--<span v-show="'y' === project.customerSaleConfirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerSaleConfirm">미확정</span>-->
                        </td>
                        <td class="td-design">
                            <i class="fa fa-play sl-blue" aria-hidden="true" style=""></i> 진행중
                            <!--<span v-show="'y' === project.customerSaleConfirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerSaleConfirm">미확정</span>-->
                        </td>
                        <td class="td-production">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">승인완료</span>
                        </td>
                        <td class="td-production">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">완료</span>
                        </td>
                        <td class="td-production">
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                            <span class="text-green">완료</span>
                        </td>
                        <td class="td-production">
                            -
                        </td>
                    </tr>

                    <!--최종승인-->
                    <tr>
                        <th>최종승인</th>
                        <td class="td-sales">
                            <div class="font-10 mgt5">
                                문상범(24/04/15 승인)
                            </div>
                            <div class="btn btn-sm btn-white mgt5 mgb5">결제이력</div>
                        </td>
                        <td class="td-sales">
                            <div class="font-10 mgt5">
                                서재훈(24/04/20 승인)
                            </div>
                            <div class="btn btn-sm btn-white mgt5 mgb5">결제이력</div>
                        </td>
                        <td class="td-sales">
                            <div class="font-10 mgt5">
                                서재훈(24/04/21 반려)
                            </div>
                            <div class="btn btn-sm btn-white mgt5 mgb5">결제이력</div>
                        </td>
                        <td class="td-sales">
                            -
                        </td>
                        <td class="td-design">
                            <div class="font-10 mgt5">
                                자동 완료 처리
                            </div>
                        </td>
                        <td class="td-design">
                            <div class="font-10 mgt5">
                                자동 완료 처리
                            </div>
                        </td>
                        <td class="td-design">
                            -
                        </td>
                        <td class="td-production">
                            <div class="font-10 mgt5">
                                문상범(24/03/02 승인)
                            </div>
                            <div class="btn btn-sm btn-white mgt5 mgb5">결제이력</div>
                        </td>
                        <td class="td-production">
                            <div class="font-10 mgt5">
                                자동 완료 처리
                            </div>
                        </td>
                        <td class="td-production">
                            <div class="font-10 mgt5">
                                자동 완료 처리
                            </div>
                        </td>
                        <td class="td-production">
                            -
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="table-title gd-help-manual">
                <div class="flo-left">스타일별 진행 상태/정보</div>
                <div class="flo-right">

                    <span >
                        <span class="pdl10 font-14">
                            총수량 : <span class="bold">{% $.setNumberFormat(project.totalCount) %}ea</span>
                        </span>
                        <span class="pdl10 font-14">
                            생산 TOTAL : <span class="text-danger bold">{% $.setNumberFormat(project.totalCost) %}원</span>
                        </span>
                        <span class="pdl10 font-14">
                            판매 TOTAL : <span class="sl-blue bold">{% $.setNumberFormat(project.totalSalePrice) %}원</span>

                            <span class="font-12" v-if="project.totalCost > 0 && project.totalSalePrice > 0">(마진:  {%  $.setNumberFormat(project.totalSalePrice - project.totalCost)  %}원, {% (100-(Math.round(project.totalCost/project.totalSalePrice*100)) ) %}%)</span>

                        </span>
                    </span>

                    <!--<div class="btn btn-red btn-red-line2 hover-btn">상담등록</div>-->
                </div>
            </div>
            <div>
                <div class="table-responsive mgt5">
                    <table class="table table-rows">
                        <colgroup>
                            <col class="w-3p" /><!-- 체크 -->
                            <col class="w-3p" /><!-- 번호 -->
                            <col class="w-5p" /><!-- 이미지 -->
                            <col  /><!-- 스타일 -->
                            <col class="w-5p" /><!-- 수량 -->

                            <col class="w-7p" /><!-- 고객/이노버 납기 -->
                            <col class="w-8p" /><!-- 생산정보 -->

                            <col class="w-7p" /><!-- 판매단가 -->
                            <col class="w-7p" /><!-- 생산가 -->
                            <col class="w-4p" /><!-- 마진 -->

                            <col class="w-4p" /><!-- 샘플 -->
                            <col class="w-4p" /><!-- 퀄리티 -->
                            <col class="w-4p" /><!-- BT -->
                            <col class="w-4p" /><!-- 생산가확정 -->
                            <col class="w-4p" /><!-- 작지 -->
                            <col class="w-4p" /><!-- 인라인 -->
                            <col class="w-4p" /><!-- 생지 -->
                        </colgroup>
                        <thead>
                        <tr>
                            <th >
                                <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno">
                            </th>
                            <th>번호</th>
                            <th>이미지</th>
                            <th>스타일</th>
                            <th>수량</th>

                            <th>
                                고객 납기일
                                <br>이노버 납기일
                            </th>
                            <th>생산정보</th>

                            <th style="background-color: #0a6aa1 !important;">
                                판매단가
                                <div class="font-11">(부가세 미포함)</div>
                            </th>
                            <th style="background-color: #0a6aa1 !important;">
                                생산 확정가
                                <div class="font-11">(부가세 미포함)</div>
                            </th>
                            <th style="background-color: #0a6aa1 !important;">마진</th>

                            <th>샘플</th>
                            <th>퀄리티</th>
                            <th>BT</th>
                            <th>생산가확정</th>
                            <th>작업지시서</th>
                            <th>인라인</th>
                            <th>생지</th>
                        </tr>
                        </thead>
                        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-for="(product, prdIndex) in viewProductList">
                        <tr>
                            <td class="center" >
                                <div class="display-block">
                                    <input type="checkbox" name="prdSno" :value="product.sno" class="">
                                </div>
                            </td>
                            <td >
                                {% prdIndex+1 %}
                                <div class="text-muted font-11">#{% product.sno %}</div>
                            </td>
                            <td ><!--이미지-->
                                <span class="hover-btn cursor-pointer"  @click="openProductReg2(project.sno, product.sno)">
                                    <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)" class="middle" width="40">
                                    <img :src="product.fileThumbnail" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)" class="middle" width="40" >
                                    <img :src="product.fileThumbnailReal" v-if="!$.isEmpty(product.fileThumbnailReal)" class="middle" width="40" >
                                </span>
                            </td>
                            <td class="pdl5 ta-l" ><!--스타일명-->
                                <span class="font-16 text-blue hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >{% product.productName %}</span>
                                <br>
                                <div class="font-14">
                                    {% product.styleCode.toUpperCase() %}
                                </div>
                            </td>
                            <td class=""><!--제작수량-->
                                <div class="font-14 bold">{% $.setNumberFormat(product.prdExQty) %}장</div>
                                <div class="font-12 ">(확정)</div>
                            </td>

                            <td class=""><!--고객 납기일-->
                                <div class="text-danger" style="border-bottom: solid 1px #e5e5e5">
                                    <div class="font-13" v-html="$.formatShortDate(product.customerDeliveryDt)"></div>
                                    <div class="font-11" v-html="$.remainDate(product.customerDeliveryDt,true)"></div>
                                </div>
                                <div class="sl-blue mgt5">
                                    <div class="font-13" v-html="$.formatShortDate(product.msDeliveryDt)"></div>
                                    <div class="font-11" v-html="$.remainDate(product.msDeliveryDt,true)"></div>
                                </div>
                            </td>

                            <td class="ta-c">
                                <div @click="openProductReg2(project.sno, product.sno, 4)" class="hover-btn cursor-pointer">하나어패럴 생산</div>
                                <div @click="openProductReg2(project.sno, product.sno, 4)" class="hover-btn cursor-pointer text-muted">생산스케쥴관리 상태</div>
                            </td>

                            <td class="relative"><!--판매가-->
                                <div class="reject-icon" v-if="'p' === product.priceConfirm ">승인</div>
                                <span class="font-15 bold text-danger">{% $.setNumberFormat(product.salePrice) %}원</span>
                                <br><span class="text-muted">({% $.setNumberFormat(Number(product.salePrice) * Number(product.prdExQty) )%}원)</span>
                            </td>

                            <td ><!--생산가-->
                                <span class="font-15 bold sl-blue">{% $.setNumberFormat(product.prdCost) %}원</span>
                                <div class="text-muted" >({% $.setNumberFormat(Number(product.prdCost) * Number(product.prdExQty) )%}원)</div>
                            </td>

                            <td ><!--마진-->
                                <span class="font-15 bold" v-if="Number(product.salePrice) > 0">{%  $.setNumberFormat(100-(Math.round(product.prdCost/product.salePrice*100))) %}%</span>
                                <span class="font-15 bold" v-if="0 >= Number(product.salePrice)">-%</span>
                            </td>

                            <td ><!--샘플-->
                                <div class="font-14 bold cursor-pointer hover-btn" v-if="product.sampleCnt > 0" @click="openProductReg2(project.sno, product.sno, 0)">
                                    {% product.sampleCnt %}개
                                </div>
                                <div class="font-14 text-muted" v-if="0 >= product.sampleCnt">
                                    없음
                                </div>
                            </td>
                            <td ><!--퀄리티-->
                                <span v-html="product.fabricStatusIcon"></span>
                                <span v-html="product.fabricStatusKr" ></span>
                            </td>
                            <td ><!--BT-->
                                <span v-html="product.btStatusIcon"></span>
                                <span v-html="product.btStatusKr" ></span>
                            </td>
                            <td >
                                <span v-html="product.prdCostStatusIcon"></span>
                                <span v-html="product.prdCostStatusKr" ></span>
                            </td>
                            <td><!--작지-->
                                <a href="#" class="text-blue">다운로드</a>
                            </td>

                            <td class="ta-c">
                                <div class="font-13">
                                    <div class="" v-if="0 == product.inlineStatus">
                                        무
                                    </div>
                                    <div class="" v-if="1 == product.inlineStatus">
                                        유
                                    </div>
                                </div>
                            </td>
                            <td class="ta-c">
                                <div class="font-13">
                                    <div class="" v-if="!0 == product.inlineStatus">
                                        무
                                    </div>
                                    <div class="" v-if="!1 == product.inlineStatus">
                                        유
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
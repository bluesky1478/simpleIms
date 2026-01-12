<!-- 레이어 팝업 -->
<transition name="ims-layer-fade">
    <div class="ims-layer-dim" v-if="layer.visible" @click.self="closeStyle">
        <div class="ims-layer-wrap">
            <div class="ims-layer-header">
                <div class="ims-layer-title">

                    <span class="ims-layer-title-sub text-danger">
                        #{% layer.data.sno %}
                    </span>

                    <!-- 제목 영역 -->
                    <span class="ims-layer-title-main">
                        <span class="sl-blue">{% layer.data.customerName %}</span>
                        {% layer.data.projectYear %} {% layer.data.projectSeason %} {% layer.data.salesStyleName %}
                        <div class="font-13 normal">
                            ({% layer.data.projectStatusKr %} 단계)
                        </div>
                    </span>

                    <span class="dp-flex dp-flex-gap20 mgl20">
                        <table class=" ">
                            <tr>
                                <td>발주D/L</td>
                                <td>:</td>
                                <td  class="pdl10">
                                    <!--완료일-->
                                    <div v-if="'0000-00-00' != layer.data.cpProductionOrder && !$.isEmpty(layer.data.cpProductionOrder)" class="text-muted">
                                        <span class="sl-green">
                                            {% $.formatShortDateWithoutWeek(layer.data.cpProductionOrder) %} 발주
                                        </span>
                                    </div>
                                    <div v-else-if="!$.isEmpty(layer.data.txProductionOrder)"><!--대체텍스트-->
                                        <span class="">
                                            {% layer.data.txProductionOrder %}
                                        </span>
                                    </div>
                                    <div v-else-if="!$.isEmpty(layer.data.exProductionOrder)" class=""><!--예정일-->
                                        <span class="">
                                            {% $.formatShortDateWithoutWeek(layer.data.exProductionOrder) %}
                                        </span>
                                        <span class="" v-html="$.remainDate(layer.data.exProductionOrder,true)"></span>
                                    </div>
                                    <div v-else class="text-muted">미정</div><!--미설정-->
                                </td>
                            </tr>
                            <tr>
                                <td>고객납기</td>
                                <td>:</td>
                                <td class="pdl10">
                                    <div class="dp-flex">
                                        <span v-if="$.isEmpty(layer.data.customerDeliveryDt)" class="text-muted">미정</span>
                                        <span v-if="!$.isEmpty(layer.data.customerDeliveryDt)" class="dp-flex">
                                            <span class="">{% $.formatShortDateWithoutWeek(layer.data.customerDeliveryDt) %}</span>
                                            <div class="" v-html="$.remainDate(layer.data.customerDeliveryDt,true)" v-if="91 != layer.data.projectStatus"></div>
                                            <div class="sl-green" v-if="91 == layer.data.projectStatus">납기완료</div>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </span>
                </div>

                <span class="dp-flex dp-flex-gap10">
                        발주조건
                        <table class="table mgb0 table-pd-5 table-th-height0 table-td-height0 table-fixed table-center" style="width:300px">
                            <tr>
                                <th class="border-top-none">판매가</th>
                                <th class="border-top-none">생산가</th>
                                <th class="border-top-none">아소트</th>
                                <th class="border-top-none">작지</th>
                                <th class="border-top-none">사양서</th>
                                <th class="border-top-none">퀄리티</th>
                            </tr>
                            <tr>
                                <td><!--판매가-->
                                    <div v-html="$.getStatusIcon2(layer.data.priceStatus)"></div>
                                </td>
                                <td><!--생산가-->
                                    <div v-html="$.getStatusIcon2(layer.data.costStatus)"></div>
                                </td>
                                <td><!--아소트-->
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == layer.data.assortApproval"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == layer.data.assortApproval"></i>
                                    <span class="text-muted" v-else>-</span>
                                </td>
                                <td><!--작지-->
                                    <div v-html="$.getStatusIcon2(layer.data.workStatus)"></div>
                                </td>
                                <td><!--사양서-->
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == layer.data.customerOrderConfirm"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == layer.data.customerOrderConfirm"></i>
                                    <span class="text-muted" v-else>-</span>
                                </td>
                                <td><!--퀄리티-->
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'2' == layer.data.fabricStatus"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'1' == layer.data.fabricStatus"></i>
                                    <span class="text-muted" v-else>-</span>
                                </td>
                            </tr>
                        </table>
                </span>

                <!-- X 버튼 -->
                <button class="ims-layer-close-x" @click="closeStyle">&times;</button>
            </div>

            <div class="ims-layer-body">
                <div v-if="layer.loading" class="ims-layer-loading">
                    불러오는 중...
                </div>
                <div v-else>
                    <!--불러온 스타일 테이블 시작-->
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " >
                        <colgroup>
                            <col class="w-2p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in layer.data.fieldData"/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in layer.data.styleList.fieldData"  :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                        </tr>
                        </thead>
                        <tbody v-if="0 >= layer.data.styleList.list.length || $.isEmpty(layer.data.styleList.list[0].styleSno)">
                        <tr>
                            <td class="ta-c" colspan="99">
                                등록 스타일 없음
                            </td>
                        </tr>
                        </tbody>
                        <tbody v-if="layer.data.styleList.list.length > 0 && !$.isEmpty(layer.data.styleList.list[0].styleSno)">
                        <tr v-for="(each , index) in layer.data.styleList.list">
                            <td><!--번호-->
                                <div>{% (index+1) %}</div>
                            </td>
                            <td v-for="fieldData in layer.data.styleList.fieldData"  :class="fieldData.class + ' font-11'">
                                <div v-if="'productName' === fieldData.name">
                                    <span class="hand hover-btn" @click="openProductReg2(each.sno, each.styleSno, 5)">
                                        {% each[fieldData.name] %}
                                    </span>
                                    <div class="font-11 text-muted">
                                        #{% each.styleSno %} / {% each['styleCode'] %}
                                    </div>
                                </div>
                                <div v-else-if="'fabricStatusKr' === fieldData.name">
                                    <div v-if="'y' === each.isReorder">
                                        해당 없음
                                    </div>
                                    <div v-else>
                                        <div class="dp-flex font-11">
                                            <div v-html="$.getStatusIcon(each.prdFabricStatus)" ></div>
                                            Q:{% each.prdFabricStatusKr %}
                                        </div>
                                        <div class="dp-flex font-11">
                                            <div v-html="$.getStatusIcon(each.prdBtStatus)" ></div>
                                            B:{% each.prdBtStatusKr %}
                                        </div>
                                    </div>
                                </div>
                                <!--판매가-->
                                <div v-else-if="'salePrice' === fieldData.name" class="text-danger">
                                    <div v-if="each.salePrice > 0">

                                        <span v-if="'p' === each.priceConfirm" class="font-11">(확)</span>

                                        {% $.setNumberFormat(each.salePrice) %}원
                                    </div>
                                    <div class="text-muted" v-if="0 >= each.salePrice">
                                        확인중
                                    </div>
                                </div>
                                <div v-else-if="'prdCost' === fieldData.name"><!--생산가-->
                                    <!--생산가격-->
                                    <div v-if="!$.isEmpty(each.estimateData) && each.prdCostConfirmSno > 0"
                                         class="sl-blue cursor-pointer hover-btn"
                                         @click="openFactoryEstimateView(each.sno, each.styleSno, each.prdCostConfirmSno, 'cost')"
                                    >
                                        {% $.setNumberFormat(each.estimateData.totalCost) %}원
                                    </div>
                                    <!--견적가격-->
                                    <div v-else-if="!$.isEmpty(each.estimateData) && each.estimateConfirmSno > 0"
                                         class="text-muted cursor-pointer hover-btn"
                                         @click="openFactoryEstimateView(each.sno, each.styleSno, each.estimateConfirmSno, 'estimate')"
                                    >
                                        (가){% $.setNumberFormat(each.estimateData.totalCost) %}원
                                    </div>
                                    <!--견적없을 때-->
                                    <div v-else class="text-muted font-10 cursor-pointer hover-btn"
                                         @click="openProductReg2(each.sno, each.styleSno, 3)">
                                        선택 견적 없음<br>(견적리스트)
                                    </div>
                                </div>
                                <div v-else-if="'prdMoq' === fieldData.name && !$.isEmpty(each.estimateData)">{% each.estimateData.prdMoq %}</div>
                                <div v-else-if="'priceMoq' === fieldData.name && !$.isEmpty(each.estimateData)">{% each.estimateData.priceMoq %}</div>
                                <div v-else-if="'workStatus' === fieldData.name" @click="window.open(`<?=$eworkUrl?>?sno=${each.styleSno}`);" class="hand hover-btn"><!--작지 @click="openCommonPopup('ework', 1300, 850, {sno:each.styleSno, tabMode:'main'})" -->
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.prdWorkStatus"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.prdWorkStatus"></i>
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.prdWorkStatus"></i>
                                </div>
                                <div v-else>
                                    <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">
                                        -
                                    </div>
                                    <div v-else>
                                        {% each[fieldData.name] %}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!--불러온 스타일 테이블 끝 -->
                </div>
            </div>

            <div class="ims-layer-footer">
                <div class="dp-flex dp-flex-between">
                    <div>
                        <ul class="font-weight-bold font-14 dp-flex dp-flex-gap15">
                            <li class="sl-blue">생산가: {% $.setNumberFormat(popStyleTotal.costTotal) %}원</li>
                            <li class="text-danger">판매가: {% $.setNumberFormat(popStyleTotal.priceTotal) %}원</li>
                            <li>마진: {% $.setNumberFormat(popStyleTotal.priceTotal-popStyleTotal.costTotal) %}원({% $.getMargin(popStyleTotal.costTotal,popStyleTotal.priceTotal) %}%)</li>
                        </ul>
                    </div>
                    <button class="ims-layer-btn" @click="closeStyle">
                        닫기
                    </button>
                </div>
            </div>
        </div>
    </div>
</transition>
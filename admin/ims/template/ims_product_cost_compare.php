<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
    <div class="row">
        <div class="col-xs-6 pd0">
            <div class="ims-vtable-2col">
                <!-- 1행 -->
                <div class="ims-vcell">
                    <div class="ims-vlabel">고객사</div>
                    <div class="ims-vvalue">{% items.customerName %}</div>
                </div>

                <div class="ims-vcell">
                    <div class="ims-vlabel">스타일명</div>
                    <div class="ims-vvalue">
                        {% product.productName %}
                        <span class="font-11">({% product.styleCode %})</span>
                    </div>
                </div>

                <!-- 2행 -->
                <div class="ims-vcell">
                    <div class="ims-vlabel">생산처</div>
                    <div class="ims-vvalue">
                        {% $.isset(product.produceCompanyKr,'미정') %}
                    </div>
                </div>

                <div class="ims-vcell">
                    <div class="ims-vlabel">예정 수량</div>
                    <div class="ims-vvalue">{% $.setNumberFormat(product.prdExQty) %}</div>
                </div>

                <!-- 3행 -->
                <div class="ims-vcell">
                    <div class="ims-vlabel">타겟 생산가</div>
                    <div class="ims-vvalue">{% $.setNumberFormat(product.targetPrdCost) %}</div>
                </div>

                <div class="ims-vcell">
                    <div class="ims-vlabel">타겟 판매가</div>
                    <div class="ims-vvalue">{% $.setNumberFormat(product.targetPrice) %}</div>
                </div>

                <!-- 4행 -->
                <div class="ims-vcell">
                    <div class="ims-vlabel">생산가</div>
                    <div class="ims-vvalue sl-blue">{% $.setNumberFormat(product.prdCost) %}</div>
                </div>

                <div class="ims-vcell">
                    <div class="ims-vlabel">판매가</div>
                    <div class="ims-vvalue text-danger">{% $.setNumberFormat(product.salePrice) %}</div>
                </div>
            </div>

        </div>
        <div class="col-xs-6 pd0">

        </div>
    </div>

    <div class="row mgt10">
        <div class="quote-grid">
            <!-- 카드 시작 -->
            <div class="quote-card" v-for="estimate in costList" v-if="'estimate' === estimate.estimateType">
                <!-- 제목 -->
                <div class="quote-card__header">
                    <span class="quote-card__title">
                        <span class="text-danger">#{% estimate.reqCount %}번&nbsp;&nbsp;</span>
                        {% $.setNumberFormat(estimate.estimateCount) %}장 견적 -
                        <span class="sl-blue font-12">{% estimate.reqFactoryNm %} {% estimate.reqStatusKr %} 상태</span>
                    </span>
                    <span class="quote-card__tag">
                        <div class="btn btn-sm btn-white" @click="openFactoryEstimateView(estimate.projectSno, estimate.styleSno, estimate.sno, 'cost')">
                            상세
                        </div>
                    </span>
                </div>

                <!-- 상단 메모 -->
                <div class="quote-card__note h90 font-12">
                    <div v-if="!$.isEmpty(estimate.reqMemo1)">{% estimate.reqMemo1 %}</div>
                    <div v-if="!$.isEmpty(estimate.reqMemo2)">{% estimate.reqMemo2 %}</div>
                    <div v-if="!$.isEmpty(estimate.reqMemo3)">{% estimate.reqMemo3 %}</div>
                    <div v-if="!$.isEmpty(estimate.reqMemo)">{% estimate.reqMemo %}</div>
                    <ul class="ims-file-list" >
                        <li class="hover-btn" v-for="(file, fileIndex) in estimate.reqFiles">
                            <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                        </li>
                    </ul>
                </div>

                <!-- 판매/공임/환율 블록 -->
                <table class="quote-main-table">
                    <colgroup>
                        <col class="w-21p">
                        <col class="w-31p">
                        <col class="w-19p">
                        <col class="w-29p">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td class="bg-light-gray3" v-if="product.salePrice > 0">판매가</td>
                        <td class="t-right" v-if="product.salePrice > 0">{% $.setNumberFormat(product.salePrice) %}</td>

                        <td class="bg-light-gray3" v-if="0 >= product.salePrice">타겟판매가</td>
                        <td class="t-right" v-if="0 >= product.salePrice">{% $.setNumberFormat(product.targetPrice) %}</td>

                        <td class="bg-light-gray3">공임비</td>
                        <td class="t-right">
                            {% $.setNumberFormat(estimate.contents.laborCost) %}
                            <span v-if="!$.isEmpty(estimate.contents.exchange) && estimate.contents.exchange > 0" class="text-muted">
                                <span v-if="Number(estimate.contents.exchange) > 0">
                                    {% (estimate.contents.laborCost/estimate.contents.exchange).toFixed(2) %}$
                                </span>
                            </span>
                            <span v-else class="text-muted">
                                <span v-if="Number('<?=$currencyUsd?>') > 0">
                                {% (Number(estimate.contents.laborCost)/Number(<?=$currencyUsd?>)).toFixed(2) %}$
                                </span>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light-gray3">견적가</td>
                        <td class="t-right">{% $.setNumberFormat(estimate.contents.totalCost) %}</td>
                        <td class="bg-light-gray3">환율</td>
                        <td class="t-right">
                            <div v-if="!$.isEmpty(estimate.contents.exchange) && estimate.contents.exchange > 0 ">
                                {% $.setNumberFormat(estimate.contents.exchange) %}
                            </div>
                            <div v-else>
                                <?=number_format($currencyUsd)?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light-gray3">마진</td>
                        <td class="t-right">
                            <div v-if="product.salePrice > 0 && estimate.contents.totalCost > 0">
                                <!--//판매가에서 마진 측정-->
                                {% $.setNumberFormat(product.salePrice-estimate.contents.totalCost) %}
                                ({% $.getMargin(estimate.contents.totalCost,product.salePrice) %}%)
                            </div>
                            <div v-else-if="product.targetPrice > 0 && estimate.contents.totalCost > 0">
                                <!--//타겟가에서 마진 측정-->
                                {% $.setNumberFormat(product.targetPrice-estimate.contents.totalCost) %}
                                ({% $.getMargin(estimate.contents.totalCost,product.targetPrice) %}%)
                            </div>
                        </td>
                        <td class="bg-light-gray3">환율일자</td>
                        <td class="t-right">
                            <div v-if="!$.isEmpty(estimate.contents.exchange) && estimate.contents.exchange > 0 ">
                                {% $.formatShortDateWithoutWeek(estimate.contents.exchangeDt) %}
                            </div>
                            <div v-else>
                                <?=date('y/m/d')?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="notice-info">환율정보 미입력시 오늘 기준 표기</div>

                <!-- 원단 정보 -->
                <table class="quote-section-table">
                    <colgroup>
                        <col span="2"/>
                        <col class="w-15p" />
                        <col class="w-17p"/>
                        <col class="w-17p"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th colspan="2" class="q-sec-title ">
                            <div class="dp-flex dp-flex-between">
                                <div>원단</div>
                                <div class="q-sec-total"><b>Total : {% $.setNumberFormat(estimate.contents.fabricCost) %}\</b></div>
                            </div>
                        </th>
                        <th class="q-sec-head t-center">가요척</th>
                        <th class="q-sec-head t-center">단가</th>
                        <th class="q-sec-head t-center">금액</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(fabric, fabricIndex) in estimate.contents.fabric" v-show="'y' === estimate.fabricView || 3 >= fabricIndex">
                        <td colspan="2">
                            <div>
                                {% fabric.no %}
                                {% fabric.fabricName %}
                            </div>
                            <div>
                                {% fabric.fabricMix %}
                                {% fabric.color %}&nbsp;
                            </div>
                        </td>
                        <td class="t-right">
                            {% fabric.meas %}
                        </td>
                        <td class="t-right">
                            {% $.setNumberFormat(fabric.unitPrice) %}
                        </td>
                        <td class="t-right">
                            {% $.setNumberFormat(fabric.price) %}
                        </td>
                    </tr>
                    <tr v-if="estimate.contents.fabric.length > 3">
                        <td colspan="5" class="ta-c">
                            <button type="button" class="mini-btn-toggle" @click="estimate.fabricView = 'y'" v-show="'n'===estimate.fabricView">전체 +{% estimate.contents.fabric.length-3 %}</button>
                            <button type="button" class="mini-btn-toggle-upper" @click="estimate.fabricView = 'n'" v-show="'y'===estimate.fabricView">요약</button>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- 부자재/기타 -->
                <table class="quote-section-table">
                    <colgroup>
                        <col span="2"/>
                        <col class="w-15p" />
                        <col class="w-17p"/>
                        <col class="w-17p"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th colspan="2" class="q-sec-title">
                            <div class="dp-flex dp-flex-between">
                                <div>부자재/기타</div>
                                <div class="q-sec-total"><b>Total : {% $.setNumberFormat(estimate.contents.subFabricCost) %}\</b></div>
                            </div>
                        </th>
                        <th class="q-sec-head t-center">수량</th>
                        <th class="q-sec-head t-center">단가</th>
                        <th class="q-sec-head t-center">금액</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(fabric, fabricIndex) in estimate.contents.subFabric" v-show="'y' === estimate.subFabricView || 3 >= fabricIndex">
                        <td colspan="2">
                            {% fabric.no %}
                            {% fabric.subFabricName %}
                            {% fabric.subFabricMix %}
                            {% fabric.color %}
                        </td>
                        <td class="t-right">
                            {% fabric.meas %}
                        </td>
                        <td class="t-right">
                            {% $.setNumberFormat(fabric.unitPrice) %}
                        </td>
                        <td class="t-right">
                            {% $.setNumberFormat(fabric.price) %}
                        </td>
                    </tr>
                    <tr v-if="estimate.contents.subFabric.length > 3">
                        <td colspan="5" class="ta-c">
                            <button type="button" class="mini-btn-toggle" @click="estimate.subFabricView = 'y'" v-show="'n'===estimate.subFabricView">전체 +{% estimate.contents.subFabric.length-3 %} </button>
                            <button type="button" class="mini-btn-toggle mini-btn-toggle-upper" @click="estimate.subFabricView = 'n'" v-show="'y'===estimate.subFabricView">요약</button>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <!-- 카드 끝 -->

            <!-- 같은 구조로 quote-card 를 여러 개 반복하면 #2, #3, #4 ... -->

        </div>

    </div>


</div>
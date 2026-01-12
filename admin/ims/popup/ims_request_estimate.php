<tr>
    <th >이노버 납기일</th>
    <td class="font-16">
        {% project.msOrderDtShort %}
    </td>
    <th>생산처 납기일</th>
    <td>
        <span class="font-16" v-html="prepared.contents.produceDeliveryDt"></span>
    </td>
    </td>
</tr>
<tr>
    <th >고객 발주일</th>
    <td class="font-16">
        {% project.customerOrderDtShort %}
    </td>
    <th >고객 납기일</th>
    <td  class="font-16">
        {% project.customerDeliveryDtShort %}
    </td>
</tr>
<tr>
    <th >생산국가</th>
    <td>
        <select class="form-control" v-model="prepared.contents.produceNational" placeholder="선택" id="produceNational">
            <option value="">미정</option>
            <option>베트남</option>
            <option>중국</option>
            <option>한국</option>
        </select>
    </td>
    <th >생산형태</th>
    <td>
        <select class="form-control" v-model="prepared.contents.produceType" id="produceType">
            <option value="0">미정</option>
            <option value="1">완사입</option>
            <option value="2">CMT</option>
            <option value="3">임가공</option>
        </select>
    </td>
</tr>
<tr>
    <th >기획서</th>
    <td>
        <simple-file-only :file="fileList.filePlan" :id="'filePlan'" :project="project" ></simple-file-only>
    </td>
    <th >견적 요청서</th>
    <td>
        <simple-file-upload :file="fileList.filePreparedEstimateMs1" :id="'filePreparedEstimateMs1'" :project="project" ></simple-file-upload>
    </td>
</tr>
<tr>
    <th colspan="99" class="text-center font-16">
        스타일별 견적 입력 <span class="display-none">{% total %}</span>
    </th>
</tr>
<tr>
    <td colspan="99" class="pd0">

        <ul class="nav nav-tabs mgb0" role="tablist" style="border-bottom:none!important;">
            <li role="presentation" :class="isActive(currentTab, reqPrdIndex)" v-for="(reqPrd, reqPrdIndex) in prepared.contents.productList" @click="setTab(reqPrdIndex)">
                <a class="hover-btn cursor-pointer">{% reqPrd.productName %}<br>{% reqPrd.styleCode %}<br>{% $.setNumberFormat(reqPrd.prdCost) %}원</a>
            </li>
        </ul>

        <div class="" v-show="reqPrdIndex === currentTab" v-for="(product, reqPrdIndex) in prepared.contents.productList">

            <div class="font-16" style="padding:15px 0 5px 0; border-top:solid 1px #000">
                <b>{% product.productName %} {% product.styleCode %} 정보 입력</b>
            </div>
            <div>
                <table class="table table-rows table-default-center">
                    <colgroup>
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                        <col style="width:9%">
                    </colgroup>
                    <tr>
                        <th>생산가<small class="font-white">(VAT별도)</small></th>
                        <th>원자재 소계</th>
                        <th>부자재 소계</th>
                        <th>공임</th>
                        <th>기준환율</th>
                        <th>마진</th>
                        <th>물류 및 관세</th>
                        <th>관리비</th>
                        <th>생산MOQ</th>
                        <th>단가MOQ</th>
                        <th>MOQ미달 추가금</th>
                    </tr>
                    <tr>
                        <td>
                            <span class="font-16 text-danger bold">{% $.setNumberFormat(product.prdCost) %}원</span>
                        </td>
                        <td>{% product.fabricCost.toLocaleString() %}원</td>
                        <td>{% product.subFabricCost.toLocaleString() %}원</td>
                        <td>{% $.setNumberFormat(product.laborCost) %}원</td>
                        <td>{% $.setNumberFormat(product.exchange) %}$원</td>
                        <td>{% $.setNumberFormat(product.marginCost) %}원</td>
                        <td>{% $.setNumberFormat(product.dutyCost) %}원</td>
                        <td>{% $.setNumberFormat(product.managementCost) %}원</td>
                        <td>{% $.setNumberFormat(product.prdMoq) %}</td>
                        <td>{% $.setNumberFormat(product.priceMoq) %}</td>
                        <td>{% $.setNumberFormat(product.addPrice) %}원</td>
                    </tr>
                </table>
            </div>

            <div class="">
                <div class="">
                    <div class="flo-left font-16 bold">
                        원단정보
                    </div>
                    <div class="flo-right">
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col style="width:5%" />
                            <col style="width:13%" />
                            <col style="width:10%" />
                            <col style="width:8%" />
                            <col style="width:6%" />
                            <col style="width:6%" />
                            <col style="width:5%" />
                            <col style="width:5%" />
                        </colgroup>
                        <tr>
                            <th>NO</th>
                            <th>자재명</th>
                            <th>혼용율</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>가요척</th>
                            <th>단가</th>
                            <th>금액</th>
                        </tr>
                        <tr v-for="(fabric, fabricIndex) in product.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                            <td>
                                <input type="text" class="form-control text-center" placeholder="자재명" v-model="fabric.no">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="요척" v-model="fabric.meas">
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.unitPrice) %}원
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.price) %}원
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div>
                <div class="">
                    <div class="flo-left">
                        부자재정보
                    </div>
                    <div class="flo-right"></div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col style="width:5%" />
                            <col style="width:20%" />
                            <col style="width:15%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col />
                        </colgroup>
                        <tr>
                            <th>NO</th>
                            <th>자재명</th>
                            <th>부자재업체</th>
                            <th>규격</th>
                            <th>가요척</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>비고</th>
                        </tr>
                        <tr v-for="(subFabric, subFabricIndex) in product.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                            <td>
                                <input type="text" class="form-control text-center" placeholder="자재명" v-model="subFabric.no">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="자재명" v-model="subFabric.subFabricName">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="부자재업체" v-model="subFabric.company">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="subFabric.spec">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="가요척" v-model="subFabric.meas">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가(생산처)" v-model="subFabric.unitPrice">
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.price) %}원
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="비고" v-model="subFabric.memo">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="clear: both"></div>
        </div>

    </td>
</tr>




 <div id="style-plan-view" v-if="null !== stylePlanList">
    <table class="table table-rows table-default-center font-11">
        <colgroup>
            <col v-if="bFlagCallProjectDetail === false" style="width:35px" /><!--이동-->
            <col v-if="bFlagCallProjectDetail === false" style="width:35px" /><!--체크-->
            <col v-if="bFlagCallProjectDetail === true" style="width:80px" /><!--스타일명-->
            <col style="width:40px" /><!--번호-->
            <col style="width:80px" /><!--이미지-->
            <col style="width:130px" /><!--디자인 컨셉-->
            <col style="width:80px"/><!--견적 수량-->
            <col style="width:50px" /><!--수량 변동-->
            <col style="width:80px" /><!--기획 생산가-->
            <col style="width:80px" /><!--타겟 판매가-->
            <col style="width:70px" /><!--마진-->
            <col style="width:110px" /><!--메인원단 / 업체-->
            <col style="width:70px" /><!--생지/BT-->
            <col style="width:75px" /><!--생산기간-->
            <col style="width:80px" /><!--생산처/타입-->
            <col style="width:65px" /><!--생산기간/국가-->
            <col style="width:110px" /><!--MOQ-->
            <col style="width:70px" /><!--담당자-->
            <col style="width:60px" /><!--등록/수정일-->
            <col style="width:50px" />
        </colgroup>
        <thead>
        <tr>
            <th v-if="bFlagCallProjectDetail === false">이동</th>
            <th v-if="bFlagCallProjectDetail === false">
                <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="stylePlanSno">
            </th>
            <th v-if="bFlagCallProjectDetail === true">스타일</th>
            <th>번호</th>
            <th>이미지</th>
            <th>디자인 컨셉</th>
            <th>견적 수량</th>
            <th>수량 변동</th>
            <th>기획 생산가</th>
            <th>타겟 판매가</th>
            <th>마진</th>
            <th>메인원단 / 업체</th>
            <th>생지/BT</th>
            <th>생산기간(有/無)</th>
            <th>생산처/타입</th>
            <th>생산기간<br/>국가</th>
            <th>MOQ</th>
            <th>담당자</th>
            <th>등록/수정일</th>
            <th>샘플<br/>등록</th>
        </tr>
        </thead>
        <tbody is="draggable" :list="stylePlanList"  :animation="200" tag="tbody" handle=".handle" @change="ImsNkService.changeSortStylePlanList()">
        <tr v-for="(val , key) in stylePlanList" :class="stylePlanList.sno === val.sno ? 'choice-skyblue' : ''">
            <td v-if="bFlagCallProjectDetail === false" class="handle">
                <div class="cursor-pointer hover-btn">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td v-if="bFlagCallProjectDetail === false">
                <input type="checkbox" name="stylePlanSno[]" :value="val.sno" class="list-check">
            </td>
            <td v-if="bFlagCallProjectDetail === true && val.cntStylePlan != undefined" :rowspan="val.cntStylePlan">
                {% val.productName %}
            </td>
            <td>{% (stylePlanList.length-key) %}</td>
            <td class="ta-c">
                <div class="dp-flex dp-flex-center">
                    <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(val.filePlan)" style="display: block; margin:0px auto;" >
                    <img :src="val.filePlan" v-show="!$.isEmpty(val.filePlan)" style="max-height:50px">
                </div>
            </td>
            <td class="ta-l pdl10">
                <div class="hover-btn cursor-pointer ta-l" style="padding:0 5px" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':project.sno, 'styleSno':val.styleSno, 'sno':val.sno});"  >
                    {% val.planConcept %}
                </div>
                <div class="btn btn-sm btn-white" @click="openCommonPopup('upsert_estimate', 1600, 900, {customerSno:product.customerSno, projectSno:project.sno, stylePlanSno:val.sno, styleQty:product.prdExQty});">가견적 요청</div>
            </td>
            <td>
                {% $.setNumberFormat(val.planQty) %}
            </td>
            <td>
                {% val.changeQtyHan %}
            </td>
            <td>
                {% $.setNumberFormat(val.planPrdCost) %}원
            </td>
            <td>
                {% $.setNumberFormat(val.targetPrice) %}원
            </td>
            <td>
                {% $.setNumberFormat(Math.round((val.targetPrice-val.planPrdCost)/val.targetPrice*100*100)/100) %}%
            </td>
            <td class="ta-l pdl10 font-11">
                {% val.mainFabric_fabricName %}
                <div class="text-muted">{% val.mainFabric_makeNational %} / {% val.mainFabric_fabricCompany %}</div>
            </td>
            <td class="ta-l pdl10">
                생지 : {% val.mainFabric_onHandYn %}<br/>
                BT : {% val.mainFabric_btYn %}
            </td>
            <td>
                {% val.mainFabric_makePeriod %} / {% val.mainFabric_makePeriodNoOnHand %}
            </td>
            <td>
                {% val.prdCustomerName %}
                <div class="text-muted">{% val.produceType %}</div>
            </td>
            <td>
                {% val.producePeriod %}일
                <div class="text-muted">{% val.produceNational %}</div>
            </td>
            <td class="ta-l pdl10">
<!--                원단 : {% val.mainFabric_moq %}<br/>-->
                생산 : {% $.setNumberFormat(val.prdMoq) %}<br/>
                단가 : {% $.setNumberFormat(val.priceMoq) %}<br/>
            </td>
            <td>
                {% val.regManagerName %}
            </td>
            <td class="font-11">
                <div>{% $.formatShortDateWithoutWeek(val.regDt) %}</div>
                <div class="text-muted">{% $.formatShortDateWithoutWeek(val.modDt) %}</div>
            </td>
            <td><span @click="openCommonPopup('product_sample_new', 1550, 900, {styleSno:val.styleSno, productPlanSno:val.sno, sno:0});" class="btn btn-sm btn-white hover-btn cursor-pointer">등록</span></td>
        </tr>
        <tr v-show=" 0 >= stylePlanList.length || $.isEmpty(stylePlanList.length)  ">
            <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
        </tr>
        </tbody>
    </table>
</div>


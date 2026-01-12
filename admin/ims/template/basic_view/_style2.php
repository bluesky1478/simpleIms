<!-- [ 스타일 ] =========================================================  -->
<div class="new-style2">

    <div class="relative" style="height:40px">
        <ul class="nav nav-tabs mgb0" role="tablist" ><!--제안서 이상 단계에서만 선택 가능-->
            <li role="presentation" :class="'basic' === styleTabMode?'active':''">
                <a href="#" data-toggle="tab"  @click="changeStyleTab('basic')" >스타일</a>
            </li>
            <li role="presentation" :class="'sample' === styleTabMode?'active':''" >
                <a href="#" data-toggle="tab" @click="changeStyleTab('sample')">샘플</a>
            </li>
            <li role="presentation" :class="'estimate' === styleTabMode?'active':''" >
                <a href="#" data-toggle="tab" @click="changeStyleTab('estimate')">고객 견적서</a>
            </li>
            <li role="presentation" :class="'assort' === styleTabMode?'active':''" >
                <a href="#" data-toggle="tab" @click="changeStyleTab('assort')">아소트</a>
            </li>
        </ul>

        <div class="" style="position: absolute; top:5px; right:0">
            <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=false" v-show="showStyle">
                <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 숨기기
            </div>
            <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=true" v-show="!showStyle">
                <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 상품 보기
            </div>
        </div>
    </div>
    <div class="bg-light-gray pd10" style="height:50px">

        <div class="dp-flex dp-flex-gap10">
            <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px">
                상품 수량 : <span class="bold">{% $.setNumberFormat(project.totalCount) %}ea</span>
            </div>
            <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px">
                매입금액 합계 : <span class="text-danger bold">{% $.setNumberFormat(project.totalCost) %}원</span>
            </div>
            <div class="pdl10 font-14" style="background-color: #fff; border-radius: 10px; padding:5px 15px">
                판매 금액 합계 : <span class="sl-blue bold">{% $.setNumberFormat(project.totalSalePrice) %}원</span>
                <span class="font-12" v-if="project.totalCost > 0 && project.totalSalePrice > 0">
                    (마진:  {%  $.setNumberFormat(project.totalSalePrice - project.totalFactoryCost)  %}원, {% (100-(Math.round(project.totalFactoryCost/project.totalSalePrice*100)) ) %}%)</span>
            </div>
        </div>

    </div>

    <table class="table-default-center">
        <colgroup>
            <col class="w-3p"><!--번호-->
            <col class="w-3p"><!--체크-->
            <?php foreach($prdSetupData['list'] as $each) { ?>
                <col class="w-<?=$each[1]?>p" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th style="height:50px">번호</th>
            <th >
                <input type="checkbox" value="all" class="checkbox">
            </th>
            <?php foreach($prdSetupData['list'] as $each) { ?>
                <th><b><?=$each[0]?></b></th>
            <?php } ?>
        </tr>
        </thead>
        <!--
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-show="!showStyle">
        <tr>
            <td colspan="99" class="center">
                <div class="btn btn-white" @click="showStyle=true">상품 보기</div>
            </td>
        </tr>
        </tbody>
        -->
        <tbody :class="'text-center'" v-for="(product, prdIndex) in productList" >
        <tr>
            <td ><!--번호-->
                {% prdIndex+1 %}
                <div class="text-muted font-11">#{% product.sno %}</div>
            </td>
            <td >
                <input type="checkbox" value="all" class="checkbox">
            </td>
            <td ><!--이미지-->
                <span class="hover-btn cursor-pointer"  v-if="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)">
                    <img src="/data/commonimg/ico_noimg_75.gif" class="middle" width="40">
                </span>
                <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)">
                    <img :src="product.fileThumbnail" class="middle" width="60" height="60" >
                </span>
                <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnailReal,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnailReal)">
                    <img :src="product.fileThumbnailReal" class="middle" width="60" height="60">
                </span>
            </td>
            <td class="pdl5 ta-l relative" ><!--스타일명-->
                <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                    {% (product.prdYear+'').substring(2,4) %}
                    {% product.prdSeason %}
                    {% product.productName %}
                </span>
                <br>
                <span class="text-muted">{% product.styleCode.toUpperCase() %}</span>
            </td>
            <td><!--진행상태-->

                <div class="font-11 font-black btn btn-gray-line2 hover-btn cursor-pointer mgr5"  @click="openEworkStatus(product)">
                    기획 정보
                </div>
                <div class="font-11 font-black btn btn-gray-line2 hover-btn cursor-pointer mgr5"  @click="openProductReg2(project.sno, product.sno, 2)">
                    샘플 정보
                </div>
                <div class="font-11 font-black btn btn-gray-line2 hover-btn cursor-pointer mgr5"  @click="openProductReg2(project.sno, product.sno, 1)">
                    Q/B 정보
                </div>
                <div class="font-11 font-black btn btn-gray-line2 hover-btn cursor-pointer mgr5"  @click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'main'})">
                    작업지시서  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </div>
            </td>
            <td class=""><!--제작수량-->
                <span class="">{% $.setNumberFormat(product.prdExQty) %}장</span>
            </td>
            <td class=""><!--고객납기-->
                <span class="">{% $.formatShortDate(product.customerDeliveryDt) %}</span>
            </td>
            <td class=""><!--MS납기-->
                <span class="">{% $.formatShortDate(product.msDeliveryDt) %}</span>
            </td>
            <td class="text-left">
                <div class="font-11">
                    현재단가: {% $.setNumberFormat(product.currentPrice) %}
                </div>
                <div class="font-11">
                    타겟판매: {% $.setNumberFormat(product.targetPrice) %}
                    <span v-if="product.targetPriceMax > 0">(최대:{% $.setNumberFormat(product.targetPriceMax) %})</span>
                </div>
                <div class="font-11">
                    타겟생산: {% $.setNumberFormat(product.targetPrdCost) %}
                </div>
            </td>
            <td >
                <div v-if="4 != project.projectType">
                    <!--생산가 표기-->
                    <div class=" bold sl-blue" v-if="Number(product.prdCostConfirmSno) > 0">
                        <div class="hover-btn cursor-pointer"
                             @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                            {% $.setNumberFormat(product.prdCost) %}원
                        </div>
                        <div class="font-11">(확정)</div>
                    </div>

                    <!--가견적표기-->
                    <div class=" " v-if="Number(product.estimateConfirmSno) > 0 && 0 >= Number(product.prdCostConfirmSno)">
                        <div class="hover-btn cursor-pointer"
                             @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                            {% $.setNumberFormat(product.estimateCost) %}원</div>
                        <div class="font-11">(가견적/미확정)</div>
                    </div>

                    <!--견적/생산가 없을 때-->
                    <div class="" v-if="0 >= Number(product.prdCostConfirmSno) || 0 >= Number(product.estimateConfirmSno)">
                        -
                    </div>

                    <div class="btn btn-sm btn-white cursor-pointer hover-btn" @click="openProductReg2(project.sno, product.sno, 3)">견적리스트</div>
                </div>

                <div v-if="4 == project.projectType">
                    <div class="sl-blue bold">
                        {% $.setNumberFormat(product.prdCost) %}원
                    </div>
                </div>

            </td>
            <td class="relative">
                <div class=" bold text-danger">
                    {% $.setNumberFormat(product.salePrice) %}원
                    <!--판매가 승인시-->
                    <div class="font-11 text-danger" v-if="'p' === project.prdPriceApproval">
                        (확정)
                    </div>
                </div>
            </td>
            <td ><!--마진-->
                <span class=" bold" v-if="Number(product.salePrice) > 0">
                    <!--견적마진-->
                    <span class="text-muted"v-if="Number(product.estimateConfirmSno) > 0 && 0 >= Number(product.prdCostConfirmSno)">
                        (가){%  $.setNumberFormat(100-(Math.round(product.estimateCost/product.salePrice*100))) %}%
                    </span>

                    <!--확정마진-->
                    <span v-if="Number(product.prdCostConfirmSno) > 0">
                        {%  $.setNumberFormat(100-(Math.round(product.prdCost/product.salePrice*100))) %}%
                    </span>
                </span>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="99" class="text-left bg-light-gray2" >
                <div class="col-xs-6">
                    선택한 스타일을
                    <div class="btn btn-white">삭제</div>
                    <div class="btn btn-red ">스타일추가</div>
                    <div class="btn btn-red">스타일복사</div>
                    <div class="btn btn-red">고객 견적서 발송</div>
                </div>
                <div class="col-xs-6 dp-flex dp-flex-gap5" style="justify-content: flex-end">
                    일괄견적:
                    <select class="form-control " style="background-color:#fff">
                        <option>하나어패럴</option>
                    </select>
                    <button type="button" class="btn btn-blue-line" @click="goBatchEstimate(project.sno, 'estimate')"><i class="fa fa-krw" aria-hidden="true"></i> 가견적 일괄 요청</button>
                    <button type="button" class="btn btn-red-line2 btn-red" @click="goBatchEstimate(project.sno, 'cost')"><i class="fa fa-krw" aria-hidden="true"></i> 생산가 일괄 요청</button>
                    <?php if( in_array(\Session::get('manager.managerId'),\Component\Ims\ImsCodeMap::AUTH_MANAGER)  ) { ?>
                        <div class="btn btn-gray" @click="costReset(project.sno)">생산가 초기화</div>
                    <?php } ?>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>

</div>
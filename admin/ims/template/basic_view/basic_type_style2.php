<div class="row relative">
    <div class="col-xs-12 mgt3" >
        <div class="relative" style="height:40px">
            <ul class="nav nav-tabs mgb0" role="tablist" v-if="Number(project.projectStatus) >= 40"><!--제안서 이상 단계에서만 선택 가능-->
                <li role="presentation" :class="'basic' === styleTabMode?'active':''">
                    <a href="#" data-toggle="tab"  @click="changeStyleTab('basic')" >스타일</a>
                </li>
                <li role="presentation" :class="'sample' === styleTabMode?'active':''">
                    <a href="#" data-toggle="tab" @click="changeStyleTab('sample')">샘플</a>
                </li>
            </ul>

            <div class="" style="position: absolute; top:9px; right:0">
                <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=false" v-show="showStyle">
                    <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 숨기기
                </div>
                <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=true" v-show="!showStyle">
                    <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 상품 보기
                </div>
            </div>
        </div>
        <div class="clear-both"></div>

        <!-- [ 스타일1 ] =========================================================  -->
        <div v-show="'basic' === styleTabMode">
            <table class="table table-cols" :style="Number(project.projectStatus) >= 40?'border-top:none':''">
                <colgroup>
                    <col class="w-3p"><!--번호-->
                    <?php foreach($prdSetupData2['list'] as $each) { ?>
                        <col class="w-<?=$each[1]?>p" />
                    <?php } ?>
                </colgroup>
                <thead>
                <tr>
                    <th>번호</th>
                    <?php foreach($prdSetupData2['list'] as $each) { ?>
                        <th><?=$each[0]?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-show="!showStyle">
                <tr>
                    <td colspan="99" class="center">
                        <div class="btn btn-white" @click="showStyle=true">상품 보기</div>
                    </td>
                </tr>
                </tbody>
                <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-for="(product, prdIndex) in viewProductList" v-show="showStyle">
                <tr>
                    <td ><!--번호-->
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
                    <td>
                        {% (product.prdYear+'').substring(2,4) %}
                        {% product.prdSeason %}
                    </td>
                    <td class="pdl5 ta-l" ><!--스타일명-->
                        <span class="text-blue hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                            {% product.productName %}
                        </span>
                        <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.productName)"></i>
                        <br>
                        <div class="">
                            {% product.styleCode.toUpperCase() %} <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode.toUpperCase())"></i>
                        </div>
                    </td>
                    <td class=""><!--제작수량-->
                        <span class="">{% $.setNumberFormat(product.prdExQty) %}장</span>
                    </td>
                    <td class=""><!--고객납기-->
                        <span class="">{% $.formatShortDate(product.customerDeliveryDt) %}</span>
                    </td>
                    <td class="text-left">
                        <div class="font-12">현재단가: {% $.setNumberFormat(product.currentPrice) %}</div>
                        <div class="font-12">타겟판매: {% $.setNumberFormat(product.targetPrice) %}</div>
                        <div class="font-12">타겟생산: {% $.setNumberFormat(product.targetPrdCost) %}</div>
                    </td>
                    <!--<td>
                        <div>임가공</div>
                        <div>하나어패럴</div>
                        <div>베트남</div>
                    </td>-->
                    <!--<td>
                        110일
                    </td>-->
                    <td >

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
                    <td class="text-center">
                        <table class="w-80p">
                            <tr>
                                <td class="text-right">
                                    <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, 1)">퀄리티</span>
                                </td>
                                <td class="text-left">
                                    : {% product.fabricStatusKr %}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right">
                                    <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, 1)">BT</span>
                                </td>
                                <td class="text-left">
                                    : {% product.btStatusKr %}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <?php include './admin/ims/template/basic_view/basic_type_style_sample.php'?>


    </div>
</div>

<tr v-if="'cost' === document.approvalType">
    <th>
        생산가 정보
    </th>
    <td class="new-style">
        <table class="table mgt10">
            <tr>
                <th>
                    상품명
                </th>
                <th>
                    스타일코드
                </th>
                <th class="sl-blue">
                    생산 견적가
                </th>
                <th>
                    판매가
                </th>
                <th>
                    마진
                </th>
                <th>
                    견적상세
                </th>
            </tr>
            <tr v-for="product in productList">
                <td>
                    <span @click="openProductReg2(product.projectSno, product.sno, 3)" class="cursor-pointer hover-btn">
                        {% product.productName %}
                    </span>
                </td>
                <td>
                    <span class="">{% product.styleCode %}</span>
                </td>
                <td class="sl-blue ">
                    <b class="font-14">{% $.setNumberFormat(product.estimateCost) %}원</b>
                </td>
                <td class="text-danger">
                    {% $.setNumberFormat(product.salePrice) %}원
                </td>
                <td class="">
                    <span v-if="product.estimateCost == 0 || product.salePrice == 0">0%</span>
                    <span v-if="product.estimateCost > 0 && product.salePrice > 0">
                        {% Math.round(100-(product.estimateCost / product.salePrice * 100)) %}%
                    </span>
                </td>
                <td class="">
                    <div v-if="0 >= product.estimateConfirmSno && 4 != project.projectType" class="text-danger">선택된 견적없음(결재불가)</div>
                    <div class="btn btn-white btn-sm" v-if="product.estimateConfirmSno > 0"
                         @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                        견적상세
                    </div>
                    <!--{% project.sno %} // {% product.sno %} //{% product.estimateConfirmSno %}-->
                </td>
            </tr>
        </table>

        <!--<div class="font15">판매가 동시 결재</div>-->

        <div class="text-danger" v-show="2 !== project.estimateStatus && 4 != project.projectType">
            모든 스타일의 견적이 있어야 합니다. 견적 없을 경우 결재 불가.
        </div>

    </td>
</tr>
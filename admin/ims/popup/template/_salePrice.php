<tr v-if="'salePrice' === document.approvalType">
    <th>
        판매가 정보
    </th>
    <td class="new-style">
        <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
            <span class="text-muted">DEBUG 견적상태 : {% document.approvalType %}</span>
        <?php }?>
        <table class="table mgt10">
            <tr>
                <th>
                    상품명
                </th>
                <th>
                    스타일코드
                </th>
                <th class="text-danger">
                    판매가
                </th>
                <th>
                    생산가
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
                    <span @click="openProductReg2(product.projectSno, product.sno, -1)" class="cursor-pointer hover-btn">
                        {% product.productName %}
                    </span>
                </td>
                <td>
                    <span class="">{% product.styleCode %}</span>
                </td>
                <td class="text-danger">
                    <b class="font-14">{% $.setNumberFormat(product.salePrice) %}원</b>
                </td>
                <td class="sl-blue ">
                    <span class="font-14">{% $.setNumberFormat(product.estimateCost) %}원</span>
                </td>
                <td>
                    <span v-if="product.estimateCost == 0 || product.salePrice == 0">0%</span>
                    <span v-if="product.estimateCost > 0 && product.salePrice > 0">
                        {% Math.round(100-(product.estimateCost / product.salePrice * 100)) %}%
                    </span>
                </td>
                <td>
                    <!--
                    <div v-if="0 >= product.salePrice" class="text-danger">판매가 0원 결재 불가</div>
                    -->

                    <div v-if="product.estimateConfirmSno > 0">
                        <div class="btn btn-white btn-sm"
                             @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                            생산견적상새
                        </div>
                    </div>

                    <div v-if="0 >= product.estimateConfirmSno">
                        생산견적없음
                    </div>
                </td>
                <td class="">
                    <!--{% project.sno %} // {% product.sno %} //{% product.estimateConfirmSno %}-->
                </td>
            </tr>
        </table>
        <div class="" >
            모든 스타일의 판매가가 입력되어 있어야 합니다. 판매가 0원이 있으면 결재가 불가합니다.
        </div>

    </td>
</tr>
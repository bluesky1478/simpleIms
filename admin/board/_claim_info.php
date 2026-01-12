<section id="request-goods-info" >
    <div class="table-title gd-help-manual ">문의정보변경</div>
    <table class="table table-cols">
        <colgroup>
            <col class="width-md"/>
            <col/>
        </colgroup>
        <tr>
            <th>문의정보 변경</th>
            <td class="">
                <button class="btn btn-white btn-lg btn-modify_all" >문의 정보 수정하기</button>
            </td>
        </tr>
    </table>

    <div class="table-title gd-help-manual vue-contents display-none">요청 정보</div>
    <table class="table table-cols vue-loader" >
        <td>
            <div class="spinner-loader"></div>
        </td>
    </table>
    <table class="table table-cols vue-contents display-none" >
        <colgroup>
            <col class="width-md"/>
            <col/>
        </colgroup>
        <tr>
            <th>주문번호</th>
            <td class="">
                <b><a :href="'/order/order_view.php?orderNo=' + claimData.orderNo " target="_blank" class="text-blue font-18" >{% claimData.orderNo %}</a></b>
                <span><b>{% claimData.orderStatusKr %}</b></span>
            </td>
        </tr>
        <tr>
            <th>문의유형</th>
            <td>
                {% claimData.claimTypeKr %}
            </td>
        </tr>
        <tr>
            <th>요청 상품</th>
            <td>
                <div id="customer-request-goods-info">
                    <table class="table table-cols">
                        <colgroup>
                            <col style="width:400px"/>
                            <col/>
                            <col style="width:250px"/>
                            <col style="width:150px"/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>상품명</th>
                            <th>옵션/수량</th>
                            <th class="ta-l">요청사유</th>
                            <th>상품별 요청 수량</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, itemIndex) in items" v-if="item.optionTotalCount > 0">
                            <td style="padding-left:10px;position: relative">
                                <div class="img" style="display: inline-block">
                                    <img :src="item.imagePath" width="50" :alt="item.goodsNm" :title="item.goodsNm" class="middle">
                                </div>
                                <div class="goods-info" style="text-align: left;display: inline-block" >
                                    <div class="goods-name">
                                        {% item.goodsNm %}
                                        <br><small class="text-muted">({%item.goodsNo%})</small>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: left;font-size:14px;" >
                                <ul>
                                    <li style="display: inline-block; margin-right: 10px " v-for="(option) in item.option" v-if="option.optionCnt > 0">
                                        <div class="claim-size-name">
                                            <span v-if="!$.isEmpty(option.optionName)">{% option.optionName %} : {% option.optionCnt %}개</span>
                                            <span v-if="$.isEmpty(option.optionName)">{% option.optionCnt %}개</span>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td class="ta-l">
                                {% item.claimReasonKr %}
                            </td>
                            <td class="ta-c">
                                <b><span class="claim-selected-item">{% item.optionTotalCount %}</span>개</b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="w100 text-right">요청 Total : {% getTotalCount %}개</div>
                </div>
            </td>
        </tr>
        <tr v-if="!$.isEmpty(exchangeItems) && 'exchange' === claimData.claimType ">
            <th>교환 희망 상품</th>
            <td>
                <table class="table table-cols">
                    <colgroup>
                        <col style="width:400px"/>
                        <col/>
                        <col style="width:150px"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>교환 상품명</th>
                        <th>교환 옵션/수량</th>
                        <th>상품별 요청 수량</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, itemIndex) in exchangeItems" v-if="item.optionTotalCount > 0">
                        <td style="padding-left:10px;position: relative">
                            <div class="img" style="display: inline-block">
                                <img :src="item.imageSrc" width="50" :alt="item.goodsNm" :title="item.goodsNm" class="middle">
                            </div>
                            <div class="goods-info" style="text-align: left;display: inline-block" >
                                <div class="goods-name">
                                    {% item.goodsNm %}
                                </div>
                            </div>
                        </td>
                        <td style="text-align: left;font-size:14px;" >
                            <ul>
                                <li style="display: inline-block; margin-right: 10px " v-for="(option) in item.goodsOptionList" v-if="option.optionCount > 0">
                                    <div class="claim-size-name">
                                        <span >{% item.goodsOptionSelectData.join('/') %} {% option.optionName %} : {% option.optionCount %}개 <span class="text-muted">({% option.optionCode %} 재고:{% option.optionStock %}) </span>
                                    </div>
                                </li>
                            </ul>
                        </td>
                        <td class="ta-c">
                            <b><span class="claim-selected-item">{% item.optionTotalCount %}</span>개</b>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="w100 text-right">교환 Total : {% getTotalExchangeCount %}개</div>
            </td>
        </tr>
        <tr v-if="!$.isEmpty(refundData.refundTypeKr) && 'back' === claimData.claimType ">
            <th>환불입력정보</th>
            <td>
                <ul>
                    <li>{% refundData.refundTypeKr %}</li>
                    <li v-if="'cash' === refundData.refundType">
                        {% refundData.bankName %} {% refundData.deposit %} {% refundData.depositor %}
                    </li>
                </ul>
            </td>
        </tr>
        <tr v-if="0 >= claimData.claimStatus">
            <th>클레임처리</th>
            <td>
                <button class="btn btn-white btn-sm btn-modify" >수정하기</button>
                <button class="btn btn-gray btn-sm btn-reg-as-order" @click="regClaim()">클레임 확정(클레임리스트등록)</button>
                <button class="btn btn-gray btn-sm btn-complete" @click="setComplete()">단순교환 처리완료</button>
                <button class="btn btn-gray btn-sm btn-complete" @click="setReject()">교환불가처리</button>

                <button class="btn btn-gray btn-sm btn-complete" @click="setChange()">출고전변경</button>

            </td>
        </tr>
        <tr v-if="claimData.claimStatus > 0">
            <th>클레임처리 정보</th>
            <td>
                <ul>
                    <li>
                        <b>클레임 번호 :</b> <a href="../order/claim_list.php?key=a.bdSno&keyword=<?=$req['sno']?>&searchDate[]=2000-01-01&searchDate[]=2100-12-31" class="text-danger font-14" target="_blank"><b>{% claimData.claimNo %}</b></a>
                    </li>
                    <li>
                        <b>상태 :</b> {% claimData.claimStatusKr %}
                    </li>
                    <li v-if="!$.isEmpty(claimData.memo)">
                        <b>처리메모</b>
                        <p>{% claimData.memo %}</p>
                    </li>
                </ul>
            </td>
        </tr>
    </table>
</section>


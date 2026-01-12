<div class="row">
    <div class="col-xs-12">

        <?php $first = date('y',strtotime('-2 year'))?>
        <?php $second = date('y',strtotime('-1 year'))?>
        <?php $third = date('y')?>

        <div class="dp-flex">
            <div class="btn btn-white" @click="reportComment=true" v-show="!reportComment">코멘트 작성 모드 전환</div>
            <div class="btn btn-white" @click="reportComment=false" v-show="reportComment">보기 모드 전환</div>
        </div>
        
        <div class="mgt5">
            <table class="table table-rows table-default-center table-th-height35 table-td-height0 table-pd-2" id="stock-table">
                <colgroup>
                    <col class="w-20px"><!--번호-->
                    <col class="w-12p"><!--업체명-->
                    <col class="w-4p"><!--TOTAL-->
                    <col class="w-4p"><!--23-->
                    <col class="w-4p"><!--24-->
                    <col class="w-4p"><!--25-->

                    <col class="w-34p">
                    <col class="w-34p">
                    <!--<col class="w-23p">
                    <col class="w-23p">
                    <col class="w-23p">-->
                </colgroup>
                <thead>
                <tr>
                    <th>번호</th>
                    <th>업체명</th>
                    <th>TOTAL</th>
                    <th><?=$first?> 이전 재고</th>
                    <th><?=$second?> 재고</th>
                    <th><?=$third?> 재고</th>
                    <th>90%소진/품절 상품 (최근1달 내 주문제품)</th>
                    <th>관리 내역</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="0 >= reportList.length">
                    <td colspan="99">데이터가 없습니다</td>
                </tr>
                </tbody>
                <tbody v-for="(each, idx) in reportList" class="hover-light">
                <tr  >
                    <td rowspan="2">{% idx+1 %}</td>
                    <td class="pdl5 ta-l"  rowspan="2">{% each.companyNm %}</td>
                    <td rowspan="2">{% $.setNumberFormat(each.totalStock) %}</td>
                    <td rowspan="2">{% $.setNumberFormat(each.stock1) %}</td>
                    <td rowspan="2">{% $.setNumberFormat(each.stock2) %}</td>
                    <td rowspan="2">{% $.setNumberFormat(each.stock3) %}</td>
                    <td class="ta-l pdl5" style="vertical-align: top">
                        <div v-for="n in [90,100]">
                            <div v-if="null !== each.burnGoods && typeof each.burnGoods[n] != 'undefined'">
                                <div v-for="(burnGoods, burnGoodsIdx, idx) in each.burnGoods[n]" class="mgb3">
                                    <span class="cursor-pointer hover-btn" @click="openDetail(burnGoodsIdx,'all')"> · {% burnGoods.goodsNm %}</span>
                                    <div class="pdl10">
                                        <span v-for="(option,optionIdx) in burnGoods.option" class="font-11" ><span v-if="0 !== optionIdx">,</span>{% option.optionName %}<span class="text-muted" v-if="option.stockCnt == 0">(품절)</span><span class="text-muted" v-if="option.stockCnt > 0">({% option.stockCnt %}/{% option.inCnt %})</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="ta-l pdl5" style="vertical-align: top">
                        <div v-show="reportComment" class="dp-flex dp-flex-gap5 mgt5 mgb5">
                            <input type="text" v-model="each.comment" class="w-50p form-control" placeholder="관리 코멘트 등록" @keyup.enter="saveStockComment(each.scmNo, each)">
                            <div class="btn btn-white btn-sm" @click="saveStockComment(each.scmNo, each)">등록</div>
                        </div>

                        <div v-if="typeof 'undefined' != commentMap[each.scmNo]">
                            <div class="pdl5" v-for="(comment) in commentMap[each.scmNo]">
                                {% $.formatShortDateWithoutWeek(comment.regDt) %} {% comment.managerNm %} :
                                {% comment.comment %}
                                <i class="fa fa-times cursor-pointer hover-btn text-muted" v-show="reportComment" @click="delStockComment(comment.sno)" aria-hidden="true"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
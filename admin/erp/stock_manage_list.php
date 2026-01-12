<div class="row">
    <div class="col-xs-12">
        <?php foreach($scmMap as $scmKey => $scmName){?>
            <label class="radio-inline mgb5 mgl0 pdr10">
                <input type="radio" name="scmNo" value="<?=$scmKey?>" v-model="searchCondition.scmSno" style="margin-top:0 !important;"
                       @click="changeScm(<?=$scmKey?>)" id="rdo-scm2-<?=$scmKey?>"  />
                <?=$scmName?>
            </label>
        <?php }?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 mgt10 font-16 bold noto">
        {% scmMap[searchCondition.scmSno] %} 재고 관리
    </div>
    <div class="col-xs-12">
        <table class="table table-cols mgb0">
            <colgroup>
                <col class="w-13p"/>
                <col>
                <col class="w-13p"/>
                <col/>
                <col class="w-6p">
            </colgroup>
            <tbody>
            <tr>
                <th>
                    검색
                    <div class="dp-flex">
                        다중 검색 :
                        <select class="form-control" v-model="searchCondition.multiCondition" style="background-color: #fff">
                            <option value="AND">AND (그리고)</option>
                            <option value="OR">OR (또는)</option>
                        </select>
                    </div>
                </th>
                <td class="contents" colspan="3">
                    <div class="mgb5 dp-flex">
                        <div class="form-inline dp-flex">
                            카테고리 :
                            <category-selector :root-parent-id="scmMapCate[searchCondition.scmSno]" v-model="searchCondition.scmCate" @change="changeCate()"></category-selector>
                        </div>
                    </div>
                    <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5 dp-flex">
                        검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                        <input type="text" name="keyword" class="form-control w-10p" v-model="keyCondition.keyword"  @keyup.enter="getMainData()"   />
                        <div class="btn btn-sm btn-red" @click="searchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
                        <div class="btn btn-sm btn-gray" @click="searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="searchCondition.multiKey.length > 1 ">-제거</div>
                    </div>
                </td>
                <td>
                    <div class="btn btn-lg btn-black w-100p" style="height:45px;padding-top:12px" @click="getMainData()">검색</div>
                    <div class="btn btn-white mgt5 w-100p" @click="conditionReset()">초기화</div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="mgt10">

            <div class="dp-flex dp-flex-between">
                <div class="font-16 bold noto">
                    검색 상품 Total: <span class="bold text-danger">{% mainData.goodsCnt %}</span>종

                    <span class="font-13 mgl15" v-show="34 == searchCondition.scmSno">
                        발송대기 수량 : <?=number_format($asianaWaitDeliveryCnt)?>개

                        <?php if($asianaWaitDeliveryCnt > 0) { ?>
                            <div class="btn btn-sm btn-white" @click="procAsianaDelivery()">발송처리</div>
                        <?php } ?>

                    </span>

                </div>
                <div>
                    <button type="button" class="btn btn-white mgb5 mgr3"
                            @click="refreshStoredStock()">창고 재고 갱신</button>
                    <button type="button" class="btn btn-white btn-icon-excel simple-download  mgb5 mgr3"
                            @click="detailDownload()">RAW 다운로드</button>
                    <button type="button" class="btn btn-white btn-icon-excel simple-download  mgb5 mgr3"
                            @click="summaryDownload()">집계 다운로드</button>
                </div>
            </div>

            <table class="table table-rows table-default-center table-th-height35 table-td-height0 table-pd-2" id="stock-table">
                <colgroup>
                    <col class="w-20px"><!--번호-->
                    <col class="w-200px"><!--상품명-->
                    <col class="w-50px"><!--판매수량-->
                    <col class="w-50px"><!--창고수량-->
                    <col class="w-50px"><!--예약수량-->
                    <col class="w-50px"><!--입고-->
                    <col class="w-60px"><!--출고-->
                    <col class="w-40px"><!--상세-->
                    <col class="w-30px"><!--구분-->
                    <col style="width:60px" v-for="n in mainData.maxOptionCnt"></col>
                </colgroup>
                <thead>
                <tr>
                    <th>번호</th>
                    <th>상품명</th>
                    <th>판매수량</th>
                    <th>창고수량</th>
                    <th>예약수량</th>
                    <th>입고</th>
                    <th>출고</th>
                    <th>상세</th>
                    <th>구분</th>
                    <th colspan="99">
                        옵션/수량
                        <label class="radio-inline">
                            <input type="radio" name="showType" value="basic" v-model="showType"/>기본
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="showType" value="detail" v-model="showType" />상세
                        </label>
                    </th>
                </tr>
                </thead>
                <tbody v-if="0 >= mainData.goodsList.length" class="hover-light">
                <td colspan="99">
                    데이터가 없습니다.
                </td>
                </tbody>
                <tbody v-for="(goods, goodsNo, goodsIdx) in mainData.goodsList" class="hover-light" >
                <tr >
                    <td rowspan="2">
                        {% goodsIdx+1 %}
                    </td>
                    <td class="_bg-light-gray ta-l" style="padding-left:10px !important;;" rowspan="2">
                        <span class="cursor-pointer hover-btn" :onclick="`goods_register_popup(${goods.goodsNo})`">{% goods.goodsNm %}</span>
                        <br><span class="font-11 text-muted cursor-pointer hover-btn" :onclick="`goods_register_popup(${goods.goodsNo})`">
                                {% goods.goodsNo %} <span class="font-10">({% $.formatShortDateWithoutWeek(goods.regDt) %}등록)</span>
                            </span>
                    </td>
                    <td class="" rowspan="2">
                        <span class="bold">{% $.setNumberFormat(goods.totalStock) %}</span>
                        <div class="font-11 text-danger" v-if="goods.totalStock > (goods.realStock-goods.reserveStock)">
                            부족 : {% goods.totalStock-(goods.realStock-goods.reserveStock) %}
                        </div>
                        <div class="font-11 sl-blue" v-else-if="(goods.realStock-goods.reserveStock) > goods.totalStock">
                            가능 : {% (goods.realStock-goods.reserveStock)-goods.totalStock %}
                        </div>
                    </td>
                    <td class="_bg-light-gray" rowspan="2">
                        <span class="">{% $.setNumberFormat(goods.realStock) %}</span>
                    </td>
                    <td class="_bg-light-gray" rowspan="2">
                        <div class="cursor-pointer hover-btn" v-if="goods.reserveStock>0" @click="openReserved(goods.goodsNo,0)">
                            {% $.setNumberFormat(goods.reserveStock) %}
                        </div>
                        <div v-else class="text-muted">
                            -
                        </div>
                    </td>
                    <td class="ta-r" rowspan="2">
                        {% $.setNumberFormat(goods.inCnt) %}
                    </td>
                    <td class="ta-r" rowspan="2">
                        {% $.setNumberFormat(goods.outCnt) %}

                        <span class="font-11 text-muted" v-if="70 > goods.totalBurnRatio">({% goods.totalBurnRatio %}%)</span>
                        <span class="font-11 " v-else-if="80 > goods.totalBurnRatio">({% goods.totalBurnRatio %}%)</span>
                        <span class="font-11 sl-blue" v-else-if="90 > goods.totalBurnRatio">({% goods.totalBurnRatio %}%)</span>
                        <span class="font-11 text-danger" v-else-if="goods.totalBurnRatio >= 90">({% goods.totalBurnRatio %}%)</span>

                    </td>
                    <td class="_bg-light-gray" rowspan="2">
                        <div class="btn btn-sm btn-white mgt3" @click="openDetail(goods.goodsNo,'all')">상세</div>
                    </td>
                    <td class="bg-light-gray font-11">
                        옵션
                    </td>
                    <td v-for="(eachOption, key, idx) in goods.option" class="bg-light-gray font-12">
                            <span @click="openDetail(goods.goodsNo,key)" class="cursor-pointer hover-btn">
                                {% eachOption.optionName %}
                            </span>
                    </td>
                    <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)" class="bg-light-gray"></td><!--빈칸-->
                </tr>
                <tr >
                    <td class="bg-light-gray font-11">수량</td>
                    <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                        <!-- 수량 -->
                        <span class="font-12" v-show="'basic' === showType">
                                {% $.setNumberFormat(eachOption.stockCnt) %}
                                <div v-if="0 > (Number(eachOption.realCnt)-Number(eachOption.reserveCnt)) - Number(eachOption.stockCnt)" class="text-danger font-11">
                                부족:{% (Number(eachOption.realCnt)-Number(eachOption.reserveCnt)) - Number(eachOption.stockCnt) %}
                                </div>
                                <div v-if="(Number(eachOption.realCnt)-Number(eachOption.reserveCnt)) - Number(eachOption.stockCnt) > 0" class="sl-blue font-11">
                                +{% (Number(eachOption.realCnt)-Number(eachOption.reserveCnt)) - Number(eachOption.stockCnt) %}
                                </div>
                            </span>

                        <div class="ta-l pdl5 font-11" v-show="'detail' === showType">
                            <ul>
                                <li class="border-bottom-light-gray-imp">입고: {% $.setNumberFormat(eachOption.inCnt) %}</li>
                                <li class="border-bottom-light-gray-imp">
                                    출고: {% $.setNumberFormat(eachOption.outCnt) %}
                                </li>
                                <li class="border-bottom-light-gray-imp">재고: {% $.setNumberFormat(eachOption.stockCnt) %}</li>
                                <li class="border-bottom-light-gray-imp">예약: {% $.setNumberFormat(eachOption.reserveCnt) %}</li>
                                <li class="border-bottom-light-gray-imp">판매: {% $.setNumberFormat(eachOption.stockCnt) %}</li>
                                <li class="border-bottom-light-gray-imp">
                                    출고비: {% eachOption.outRate %}%
                                </li>
                                <li >
                                    소진율: {% eachOption.burnRate %}%
                                </li>
                            </ul>
                        </div>

                    </td>
                    <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!--다운로드 폼-->
<div class="row" v-show="false">
    <div class="col-xs-12">
        <table class="table table-rows table-default-center table-th-height35 table-td-height0 table-pd-2" id="stock-simple-table">
            <thead>
            <tr>
                <th>번호</th>
                <th>상품번호</th>
                <th>상품명</th>
                <th>판매수량</th>
                <th>창고수량</th>
                <th>예약수량</th>
                <th>입고</th>
                <th>출고</th>
                <th>출고율</th>

                <th>출고+재고</th>
                <th>입고-(출고+재고)</th>

                <th>구분</th>
                <th :colspan="mainData.maxOptionCnt">
                    옵션/수량
                </th>
            </tr>
            </thead>
            <tbody v-if="0 >= mainData.goodsList.length" class="hover-light">
            <td colspan="99">
                데이터가 없습니다.
            </td>
            </tbody>
            <tbody v-for="(goods, goodsNo, goodsIdx) in mainData.goodsList" class="hover-light" >
            <tr >
                <td :rowspan="'basic'===showType?2:7">{% goodsIdx+1 %}</td>
                <td :rowspan="'basic'===showType?2:7">{% goods.goodsNo %}</td>
                <td class="ta-l" style="padding-left:10px !important;;" :rowspan="'basic'===showType?2:7">{% goods.goodsNm %}</td>
                <td class="" :rowspan="'basic'===showType?2:7">
                    <span class="bold">{% $.setNumberFormat(goods.totalStock) %}</span>
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    <span class="">{% $.setNumberFormat(goods.realStock) %}</span>
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    <div class="cursor-pointer hover-btn" v-if="goods.reserveStock>0" @click="openReserved(goods.goodsNo,0)">
                        {% $.setNumberFormat(goods.reserveStock) %}
                    </div>
                    <div v-else class="text-muted">
                        -
                    </div>
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    {% $.setNumberFormat(goods.inCnt) %}
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    {% $.setNumberFormat(goods.outCnt) %}
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    {% goods.totalBurnRatio %}
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    {% $.setNumberFormat(goods.outCnt+goods.realStock) %}
                </td>
                <td class="_bg-light-gray" :rowspan="'basic'===showType?2:7">
                    {% $.setNumberFormat(goods.inCnt-(goods.outCnt+goods.realStock)) %}
                </td>
                <td class="bg-light-gray font-11">
                    옵션
                </td>
                <td v-for="(eachOption, key, idx) in goods.option" class="bg-light-gray font-12">
                    <span @click="openDetail(goods.goodsNo,key)" class="cursor-pointer hover-btn">
                        {% eachOption.optionName %}
                    </span>
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)" class="bg-light-gray"></td><!--빈칸-->
            </tr>
            <tr >
                <td class="bg-light-gray font-11">판매</td>
                <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                    <!-- 수량 -->
                    {% $.setNumberFormat(eachOption.stockCnt) %}
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
            </tr>
            <tr v-if="'detail'===showType">
                <td class="bg-light-gray font-11">예약</td>
                <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                    <!-- 수량 -->
                    {% $.setNumberFormat(eachOption.reserveCnt) %}
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
            </tr>
            <tr v-if="'detail'===showType">
                <td class="bg-light-gray font-11">입고</td>
                <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                    <!-- 수량 -->
                    {% $.setNumberFormat(eachOption.inCnt) %}
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
            </tr>
            <tr v-if="'detail'===showType">
                <td class="bg-light-gray font-11">출고</td>
                <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                    <!-- 수량 -->
                    {% $.setNumberFormat(eachOption.outCnt) %}
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
            </tr>
            <tr v-if="'detail'===showType">
                <td class="bg-light-gray font-11">출고비율(%)</td>
                <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                    <!-- 수량 -->
                    {% eachOption.outRate %}
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
            </tr>
            <tr v-if="'detail'===showType">
                <td class="bg-light-gray font-11">소진율(%)</td>
                <td v-for="(eachOption, key, idx) in goods.option" style="padding:0!important">
                    <!-- 수량 -->
                    {% eachOption.burnRate %}
                </td>
                <td v-for="n in (mainData.maxOptionCnt-goods.optionCnt)"></td><!--빈칸-->
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- 스타일 관련 커스텀 필드 -->
<table class="table table-rows table-default-center table-td-height30 table-pd-3n th-dark-gray table-fixed">
    <colgroup>
        <col class="w-2p" />
        <col class="w-2p" />
        <col class="w-3p" />
        <col v-for="fieldData in getStyleField('main')"
             v-if="true != fieldData.skip && true !== fieldData.subRow"
             :class="`w-${fieldData.col}p`"/>
    </colgroup>
    <thead>
    <tr>
        <th>이동</th>
        <th>
            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno">
        </th>
        <th>번호</th>
        <th v-for="fieldData in getStyleField('main')">
            <span v-if="'productName' === fieldData.name">
                상품명
                <i class="fa fa-files-o text-muted cursor-pointer font-14 mgl3" aria-hidden="true" @click="copyStyleName('name')" ></i>
                <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('code')" ></i>
                <i class="fa fa-files-o text-muted cursor-pointer text-danger font-14" aria-hidden="true" @click="copyStyleName('goods_info')" ></i>
            </span>
            <span v-else-if="'prdQty' === fieldData.name">
                수량
                <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cnt')" ></i>
            </span>
            <span v-else-if="'prdPrice' === fieldData.name">
                판매가<br>(부가세 제외)
                <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('price')"></i>
            </span>
            <span v-else-if="'prdCost' === fieldData.name">
                생산가<br>(부가세 제외)
                <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cost')"></i>
            </span>
            <span v-html="fieldData.title" v-else></span>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr v-if="isStyleModify" class="bg-light-yellow">
        <td colspan="4" >
            일괄작업
        </td>
        <td >
            <div class="dp-flex dp-flex-gap5">
                연도:
                <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="mainData.projectYear" >
                    <?php foreach($yearList as $codeValue) { ?>
                        <option value="<?=$codeValue?>"><?=$codeValue?></option>
                    <?php } ?>
                </select>
                시즌:
                <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="mainData.projectSeason" >
                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>"><?=$codeKey?></option>
                    <?php } ?>
                </select>
                <div class="btn btn-sm btn-gray" @click="applyYearSeason()">적용</div>
            </div>
        </td>
        <td><!--샘플--></td>
        <td class="ta-l">
            <div >
                <div class="sl-blue">MS납기</div>
                <div class="">
                    <date-picker v-model="mainData.msDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                </div>
                <div class="text-danger mgt3">고객납기</div>
                <div class="dp-flex">
                    <date-picker v-model="mainData.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                    <div class="btn btn-sm btn-gray" @click="applyDeliveryDt()">적용</div>
                </div>
            </div>
        </td>
        <td ></td>
        <td ></td>
        <td ></td>
        <td ></td>
        <td ></td>
        <td ></td>
        <td ></td>
        <td ></td>
        <td ></td>
    </tr>
    </tbody>
    <tbody :class="'text-center'"  is="draggable" :list="productList"  :animation="200" tag="tbody" handle=".handle" @change="changeProductList()">
        <tr v-for="(each, idx) in productList" >
            <td :class="each.sno > 0 ? 'handle' : ''"><!--이동-->
                <div class="cursor-pointer hover-btn" v-show="each.sno > 0">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
                <div class="text-danger font-9" v-show="$.isEmpty(each.sno) || 0 >= each.sno">
                    신규
                </div>
            </td>
            <td ><!--체크-->
                <input type="checkbox" name="prdSno" :value="idx" class="prd-sno"
                       :data-name="each.productName"
                       :data-code="each.styleCode"
                       :data-cnt="each.prdExQty"
                       :data-cost="each.prdCost"
                       :data-price="each.salePrice"
                       :data-estimate-cost="each.estimateCost"
                       :data-margin="each.margin">
            </td>
            <td ><!--번호-->
                {% idx+1 %}
                <div class="text-muted font-11">#{% each.sno %}</div>
            </td>
            <td v-for="fieldData in getStyleField('main')" :class="fieldData.class + ' relative'">
                <?php include 'ims25/template/_ims25_custom_style_template.php'?>
            </td>
        </tr>
        <tr v-if="0 >= productList.length">
            <td colspan="99" class="ta-c">등록된 스타일 없음</td>
        </tr>
    </tbody>
</table>

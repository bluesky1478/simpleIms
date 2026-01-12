<!-- 25년 발주 상품 스타일 -->
<table class="table-default-center">
    <colgroup>
        <col style="width:1.5%"><!--이동-->
        <col class="w-2p"><!--번호-->
        <col class="w-2p"><!--체크-->
        <?php foreach($prdSetupDataOrder['list'] as $each) { ?>
            <col class="w-<?=$each[1]?>p" />
        <?php } ?>
    </colgroup>
    <thead>
    <tr>
        <th style="height:50px" class="font-10">이동</th>
        <th style="height:50px">번호</th>
        <th >
            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno2">
        </th>
        <?php foreach($prdSetupDataOrder['list'] as $each) { ?>
            <th>
                <b><?=$each[0]?></b>
                <?php if( '상품명' === $each[0] ){ ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('name','prdSno2')" ></i>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('code','prdSno2')" ></i>
                <?php } ?>
                <?php if( '수량' === $each[0] ){ ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cnt','prdSno2')" ></i>
                <?php } ?>
                <?php if( strpos($each[0], "판매단가") !== false ) { ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('price','prdSno2')"></i>
                <?php } ?>
                <?php if( strpos($each[0], "생산가") !== false ) { ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cost','prdSno2')"></i>
                <?php } ?>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody :class="'text-center'"  is="draggable" :list="productList"  :animation="200" tag="tbody" handle=".handle" @change="changeProductList()">
    <tr v-for="(product, prdIndex) in productList">
        <td :class="product.sno > 0 ? 'handle' : ''">
            <div class="cursor-pointer hover-btn" v-show="product.sno > 0">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </div>
            <div class="text-danger font-9" v-show="$.isEmpty(product.sno) || 0 >= product.sno">
                신규
            </div>
        </td>
        <td ><!--번호-->
            {% prdIndex+1 %}
            <div class="text-muted font-11">#{% product.sno %}</div>
        </td>
        <td >
            <input type="checkbox" name="prdSno2" :value="product.sno" class=""
                   :data-name="product.productName"
                   :data-code="product.styleCode"
                   :data-cnt="product.prdExQty"
                   :data-cost="product.prdCost"
                   :data-price="product.salePrice"
            >
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
            <div v-show="isStyleModify">
                <!--시즌-->
                <div class="dp-flex dp-flex-gap5">
                    <!--시즌-->
                    <select class="js-example-basic-single sel-style border-line-gray w-100px" v-model="product.prdSeason" @change="setStyleCode(product, customer.styleCode)">
                        <option value="">미정</option>
                        <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                            <option value="<?=$codeKey?>"><?=$codeKey?></option>
                        <?php } ?>
                    </select>
                    <!--스타일-->
                    <select class="js-example-basic-single sel-style border-line-gray w-100px" v-model="product.prdStyle" @change="setStyleName(product);setStyleCode(product, customer.styleCode)">
                        <option value="">미정</option>
                        <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                            <option value="<?=$codeKey?>"><?=$codeValue?></option>
                        <?php } ?>
                    </select>
                    <!--보조코드-->
                    <input type="text" class="form-control" v-model="product.addStyleCode" placeholder="보조코드" @input="setStyleCode(product, customer.styleCode)">
                </div>

                <!--스타일명-->
                <div class="dp-flex mgt3">
                    스타일명:
                    <input type="text" class="form-control w-200px" v-model="product.productName" placeholder="스타일명">
                </div>

                <div class="mgt5">
                    스타일코드:
                    {% product.styleCode.toUpperCase() %}
                </div>

            </div>

            <div v-show="!isStyleModify">
                <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                    {% (product.prdYear+'').substring(2,4) %}
                    {% product.prdSeason %}
                    {% product.productName %}
                </span>
                <span>
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.productName)"></i>
                </span>

                <br>
                <span class="text-muted">
                    {% product.styleCode.toUpperCase() %}
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode.toUpperCase())"></i>
                </span>
            </div>
        </td>
        <td class=""><!--생산처-->
            {% product.reqFactoryNm %}
        </td>
        <td class=""><!--생산형태-->
            {% product.produceTypeKr %}
        </td>
        <td class=""><!--생산국가-->
            {% product.produceNational %}
        </td>
        <td class=""><!--샘플수량-->
            <span class="cursor-pointer hover-btn" @click="openProductReg2(project.sno, product.sno, 2)">
                {% product.sampleCnt %}
            </span>
        </td>
        <td class=""><!--제작수량-->
            <?php $modifyKey='isStyleModify'; $model='product.prdExQty'; $placeholder='예상수량' ?>
            <?php include './admin/ims/template/basic_view/_number.php'?>
        </td>
        <td class="pd0 ta-c"><!--고객납기-->
            <table class="table-borderless font-12 table-pd-0" >
                <tr>
                    <td class="ta-r font-11">고객납기</td>
                    <td >:</td>
                    <td class="text-danger">
                        <div v-show="isStyleModify">
                            <date-picker v-model="product.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                        </div>
                        <div v-show="!isStyleModify">
                            {% $.formatShortDate(product.customerDeliveryDt) %}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="ta-r font-11">MS납기</td>
                    <td >:</td>
                    <td class="sl-blue">
                        <div v-show="isStyleModify">
                            <date-picker v-model="product.msDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                        </div>
                        <div v-show="!isStyleModify">
                            {% $.formatShortDate(product.msDeliveryDt) %}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td >
            <div class=" bold text-danger" v-show="!isStyleModify">
                {% $.setNumberFormat(product.salePrice) %}원
                <!--판매가 승인시-->
                <div class="font-11 text-danger" v-if="'p' === project.prdPriceApproval">
                    (확정)
                </div>
            </div>
            <div v-show="isStyleModify">
                <input type="number" class="form-control" v-model="product.salePrice">
            </div>
        </td>
        <td class="relative">
            <!--{% product.prdCostConfirmSno %}-->
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
                <div class="pdt15" v-else-if="Number(product.estimateConfirmSno) > 0">
                    <div class="hover-btn cursor-pointer"
                         @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                        {% $.setNumberFormat(product.estimateCost) %}원</div>
                    <div class="font-11 text-muted">(미확정가)</div>
                </div>

                <!--견적/생산가 없을 때-->
                <div class="text-muted cursor-pointer hover-btn" v-if="0 >= Number(product.prdCostConfirmSno) && 0 >= Number(product.estimateConfirmSno)" @click="openProductReg2(project.sno, product.sno, 3)">
                    <div v-if="0 >= product.estimateCnt">
                        견적 필요
                    </div>
                    <div v-if="product.estimateCnt > 0">
                        견적 중<span class="font-11">({% product.estimateCnt %}회)</span>
                    </div>
                </div>

                <div class="btn btn-sm btn-white cursor-pointer hover-btn" @click="openProductReg2(project.sno, product.sno, 3)">견적이력</div>

            </div>

            <div v-if="4 == project.projectType">
                <div class="sl-blue bold">
                    {% $.setNumberFormat(product.prdCost) %}원
                    <div class="font-11" v-if="project.costStatus == 2">(확정)</div>
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

        <!--고객 MOQ-->
        <td>
            <?php $modifyKey='isStyleModify'; $model='product.moq'; $placeholder='고객MOQ' ?>
            <?php include './admin/ims/template/basic_view/_number.php'?>
        </td>

        <!--생산 MOQ-->
        <td>
            <span class="text-muted" v-if="$.isEmpty2(product.estimateConfirmSno) && $.isEmpty2(product.prdCostConfirmSno) ">
                미등록
            </span>
            <span class="" v-else>
                {% $.setNumberFormat(product.prdMoq) %}
            </span>
        </td>

        <!--단가 MOQ-->
        <td>
            <span class="text-muted" v-if="$.isEmpty2(product.estimateConfirmSno) && $.isEmpty2(product.prdCostConfirmSno) ">
                미등록
            </span>
            <span class="" v-else>
                {% $.setNumberFormat(product.priceMoq) %}
            </span>
        </td>

        <!--작지-->
        <td class="pd0">
            <span v-html="$.getStatusIcon(product.workStatus)"></span>
            {% product.workStatusKr %}
            <div class="dp-flex-center mgt5">
                <div class="btn btn-white btn-sm font-11" @click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'main'})">보기</div>
                <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkP_${product.sno}`,`<?=$eworkUrl?>?sno=${product.sno}`,1600,950);">인쇄</div>
            </div>
        </td>

        <!--QB-->
        <td>
            <div class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, 1)">
                <table class="table-borderless font-12 table-pd-0" >
                    <colgroup>
                        <col class="w-40px">
                        <col>
                        <col>
                    </colgroup>
                    <tr>
                        <td class="ta-r ">퀄리티</td>
                        <td class="">:</td>
                        <td class="ta-l">
                            {% product.fabricStatusKr %}
                        </td>
                    </tr>
                    <tr>
                        <td class="ta-r">BT</td>
                        <td >:</td>
                        <td class="ta-l">
                            {% product.btStatusKr %}
                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>
    </tbody>
</table>


<!--기획정보 모달-->
<ims-modal :visible.sync="simpleLayerPrd" max-width="700px" title="상품 기획 정보" >
    <div class="pd0" v-if="null !== simpleLayerPrdData">
        <table class="table table-pd-3 table-td-height30 table-th-height30">
            <colgroup>
                <col class="w-100px">
                <col>
            </colgroup>
            <tr>
                <th class="ta-c">상품명</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.productName %}
                </td>
            </tr>
            <tr>
                <th class="ta-c">진행형태</th>
                <td class="ta-l">
                    {% getCodeMap()['styleProcType'][simpleLayerPrdData.styleProcType] %}
                </td>
            </tr>
            <tr>
                <th class="ta-c">고객사샘플</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.prd002 %}
                    {% getCodeMap()['custSampleType'][simpleLayerPrdData.addedInfo.prd002] %}
                </td>
            </tr>
            <tr>
                <th class="ta-c">상세</th>
                <td class="ta-l">
                    <div :class="'mgt5 ' + ( 'prd008' === styleEtcKey ? 'text-danger':'' )"
                         v-for="(styleEtc, styleEtcKey) in styleEtcListMap"
                         v-show="!$.isEmpty(simpleLayerPrdData.addedInfo[styleEtcKey])" >
                        <b><i class='fa fa-info-circle' aria-hidden="true"></i> {% styleEtc %}</b> : {% simpleLayerPrdData.addedInfo[styleEtcKey] %}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <template #footer>
        <div class="btn btn-white mgt5" @click="simpleLayerPrd=false; simpleLayerPrdData=null">닫기</div>
    </template>
</ims-modal>




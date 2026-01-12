<!-- 25년 스타일 -->
<div class="dp-flex bg-light-gray pdl15 pdb5 text-danger font-bold font-14">
    <b class="">고객 구분 코드 :</b>
    <div v-show="isStyleModify">
        <input type="text" class="form-control" v-model="customer.styleCode" @input="setStyleCodeBatch(customer.styleCode)">
    </div>
    <div v-show="!isStyleModify">
        {% customer.styleCode %}
    </div>
</div>
<table class="table-default-center">
    <colgroup>
        <col style="width:1.5%"><!--이동-->
        <col class="w-2p"><!--번호-->
        <col class="w-2p"><!--체크-->
        <?php foreach($prdSetupData['list'] as $each) { ?>
            <col class="w-<?=$each[1]?>p" />
        <?php } ?>
    </colgroup>
    <thead>
    <tr>
        <th style="height:50px" class="font-10">이동</th>
        <th style="height:50px">번호</th>
        <th >
            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno">
        </th>
        <?php foreach($prdSetupData['list'] as $each) { ?>
            <th>
                <b><?=$each[0]?></b>
                <?php if( '상품명' === $each[0] ){ ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('name')" ></i>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('code')" ></i>
                <?php } ?>
                <?php if( '수량' === $each[0] ){ ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cnt')" ></i>
                <?php } ?>
                <?php if( strpos($each[0], "판매단가") !== false ) { ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('price')"></i>
                <?php } ?>
                <?php if( strpos($each[0], "생산가") !== false ) { ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cost')"></i>
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
            <input type="checkbox" name="prdSno" :value="product.sno" class=""
                   :data-name="product.productName"
                   :data-code="product.styleCode"
                   :data-cnt="product.prdExQty"
                   :data-cost="product.prdCost"
                   :data-price="product.salePrice"
            >
        </td>
        <td ><!--이미지-->
            <span class="hover-btn cursor-pointer"  v-if="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnail)">
                <img src="/data/commonimg/ico_noimg_75.gif" class="middle" width="40">
            </span>
            <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnail)">
                <img :src="product.fileThumbnail" class="middle" width="60" height="60" >
            </span>
            <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail)">
                <img :src="product.fileThumbnail" class="middle" width="60" height="60">
            </span>
        </td>
        <td class="pdl5 ta-l relative" ><!--스타일명-->

            <div v-show="isStyleModify">

                <!--시즌-->
                <div class="dp-flex dp-flex-gap5">
                    <!--년도-->
                    <select class="js-example-basic-single sel-style border-line-gray w-100px" v-model="product.prdYear" @change="setStyleCode(product, customer.styleCode)">
                        <?php foreach($yearList as $codeValue) { ?>
                            <option value="20<?=$codeValue?>"><?=$codeValue?></option>
                        <?php } ?>
                    </select>
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

            <div class="dp-flex">
                <div class="hover-btn cursor-pointer btn-sm btn btn-white" @click="simpleLayerPrdData=product;simpleLayerPrd=true">
                    <i class="fa fa-info-circle" aria-hidden="true"></i> 기획정보
                </div>

                <div class="text-muted hover-btn cursor-pointer" v-show="product.sno > 0"
                     @click="ImsService.deleteData('projectProduct',product.sno, ()=>{ refreshProductList(sno) })">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </div>
                <div class="text-muted hover-btn cursor-pointer" v-show="$.isEmpty(product.sno) || 0 >= product.sno"
                     @click="deleteElement(productList, prdIndex)">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </div>
            </div>


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
        <!--
        <td class="">MS납기
            <span class="">{% $.formatShortDate(product.msDeliveryDt) %}</span>
        </td>
        -->
        <td class="text-left">
            <div class="font-11">
                현재단가: {% $.setNumberFormat(product.currentPrice) %}
            </div>
            <div class="font-11">
                타겟판매: {% $.setNumberFormat(product.targetPrice) %}
                <div v-if="product.targetPriceMax > 0">(최대:{% $.setNumberFormat(product.targetPriceMax) %})</div>
            </div>
            <div class="font-11">
                타겟생산: {% $.setNumberFormat(product.targetPrdCost) %}
            </div>
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
                <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkM_${product.sno}`,`./popup/ims_pop_ework.php?sno=${product.sno}&tabMode=main`,1300, 850)">보기</div>
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
                        <col >
                        <col class="w-30px">
                    </colgroup>
                    <tr>
                        <td class="ta-r ">퀄리티</td>
                        <td class="">:</td>
                        <td class="ta-l">
                            {% product.fabricStatusKr %}
                        </td>
                        <td >
                            <span class="text-muted font-11">({% product.fabricCompleteCnt %}/{% product.fabricCnt %})</span>
                        </td>
                        <td >
                            <span v-html="product.fabricStatusIcon" class="font-11"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ta-r">BT</td>
                        <td >:</td>
                        <td class="ta-l">
                            {% product.btStatusKr %}
                        </td>
                        <td >
                            <span class="text-muted font-11">({% product.btCompleteCnt %}/{% product.fabricCnt %})</span>
                        </td>
                        <td >
                            <span v-html="product.btStatusIcon" class="font-11"></span>
                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="99" class="text-left bg-light-gray2" >
            <div class="col-xs-6">
                <!--선택한 스타일을
                <div class="btn btn-white">삭제</div>
                <div class="btn btn-red ">스타일추가</div> -->
                <div class="btn btn-red  " @click="saveStyleList(true)" v-show="isStyleModify">스타일 저장</div>
                <div class="btn btn-white w-50px" @click="isStyleModify=false" v-show="isStyleModify">취소</div>
                <div class="btn btn-red  btn-red-line2" v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;스타일 수정&nbsp;&nbsp;</div>
                <div class="btn btn-blue btn-blue-line " @click="addSalesStyle()">+ 스타일 추가</div>
                <button type="button" class="btn btn-white" @click="copyProduct(project.sno)"><i class="fa fa-files-o" aria-hidden="true"></i> 스타일 복사</button>
                <!--<div class="btn btn-red">스타일복사</div>
                <div class="btn btn-red">고객 견적서 발송</div>-->
            </div>
            <div class="col-xs-6 dp-flex dp-flex-gap5" style="justify-content: flex-end">
                일괄견적:

                <select2 class="js-example-basic-single" style="width:150px" v-model="batchEstimateFactory" >
                    <option value="0">미정</option>
                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                        <option value="<?=$key?>"><?=$value?></option>
                    <?php } ?>
                </select2>

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




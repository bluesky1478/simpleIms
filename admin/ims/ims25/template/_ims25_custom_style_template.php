<?php $modifyKey = gd_isset($styleModifyFieldName, 'isModify'); ?>
<!-- TYPE INDEX
c : 커스텀
i
d1 : 날짜1 (00/00/00(월))
d2 : 날짜2 (00/00/00)
d3 : 날짜3 (00/00/00) 00남음  (TODO)
textOnly : 수정 불가 고정문자
기타 : 일반 textBox
-->
<!-- ######################################################## -->
<!-- ################  스타일 커스텀 필드 시작 ################ -->
<!-- ######################################################## -->

<div v-if="'c' === fieldData.type">
    <!--------------------------------- 스타일 썸네일 --------------------------------->
    <div v-if="'fileThumbnail' === fieldData.name" >
        <span class="hover-btn cursor-pointer"  v-if="$.isEmpty(each.fileThumbnail) && $.isEmpty(each.fileThumbnail)">
            <img src="/data/commonimg/ico_noimg_75.gif" class="middle" width="40">
        </span>
        <span class="hover-btn cursor-pointer"  v-if="!$.isEmpty(each.fileThumbnail) && $.isEmpty(each.fileThumbnail)"
              @click="window.open(each.fileThumbnail,'img_thumbnail','width=950,height=1200')">
            <img :src="each.fileThumbnail" class="middle" width="60" height="60" >
        </span>
        <span class="hover-btn cursor-pointer" v-if="!$.isEmpty(each.fileThumbnail)"
              @click="window.open(each.fileThumbnail,'img_thumbnail','width=950,height=1200')">
            <img :src="each.fileThumbnail" class="middle" width="60" height="60">
        </span>
    </div>
    <!--------------------------------- 상품명 --------------------------------->
    <div v-if="'productName' === fieldData.name" >
        <div v-show="<?=$modifyKey?>">
            <!--시즌-->
            <div class="dp-flex dp-flex-gap5">
                <!--년도-->
                <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="each.prdYear" @change="setStyleCode(each, customer.styleCode)">
                    <?php foreach($yearList as $codeValue) { ?>
                        <option value="20<?=$codeValue?>"><?=$codeValue?></option>
                    <?php } ?>
                </select>
                <!--시즌-->
                <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="each.prdSeason" @change="setStyleName(each);setStyleCode(each, customer.styleCode)">
                    <option value="">미정</option>
                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>"><?=$codeKey?></option>
                    <?php } ?>
                </select>
                <!--스타일-->
                <select class="js-example-basic-single sel-style border-line-gray w-100px" v-model="each.prdStyle" @change="setStyleName(each);setStyleCode(each, customer.styleCode)">
                    <option value="">미정</option>
                    <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                    <?php } ?>
                </select>
                <!--보조코드-->
                <input type="text" class="form-control w-70px" v-model="each.addStyleCode" :placeholder="'보조코드'" @input="setStyleCode(each, customer.styleCode)">
            </div>
            <!--스타일명-->
            <div class="dp-flex mgt3">
                스타일명:
                <input type="text" class="form-control w-230px" v-model="each.productName" :placeholder="'스타일명'">
            </div>
            <div class="mgt5 ">
                <div class="round-box bg-light-gray">
                    스타일코드:
                    {% each.styleCode.toUpperCase() %}
                </div>
            </div>
        </div>
        <div v-show="!<?=$modifyKey?>" >
                <span class="hover-btn cursor-pointer" @click="openProductReg2(mainData.sno, each.sno, -1)" >
                    {% (each.prdYear+'').substring(2,4) %}
                    {% each.prdSeason %}
                    {% each.productName %}
                </span>
            <span>
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(each.productName)"></i>
                </span>
            <span class="text-muted hover-btn cursor-pointer" v-show="each.sno > 0"
                  @click="ImsService.deleteData('projectProduct',each.sno, ()=>{ refreshProductList(sno) })">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
            <span class="text-muted hover-btn cursor-pointer" v-show="$.isEmpty(each.sno) || 0 >= each.sno"
                  @click="deleteElement(productList, prdIndex)">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
            <br>
            <span class="text-muted">
                    {% each.styleCode.toUpperCase() %}
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(each.styleCode.toUpperCase())"></i>
                </span>
        </div>
        <div style="position: absolute; top:2px; right:2px; display: none">
            <input type="button" value="이슈 관리" class="btn btn-red btn-sm btn-red-line2"
                   @click="openCommonPopup('project_issue_upsert', 1000, 910, {'sno':0,'customerSno':mainData.customerSno,'projectSno':mainData.sno,'styleSno':each.sno});"
            >
        </div>
    </div>
    <!--------------------------------- 샘플정보 --------------------------------->
    <div v-if="'sample' === fieldData.name" >

        <div v-if="true === mainData.isReorder">
            리오더<span class="font-10">(해당없음)</span>
        </div>
        <div v-else>
            <div v-if="each.sampleConfirmSno >= 0">

                <div v-if="each.sampleCnt > 0">
                    <div class="hover-btn cursor-pointer font-13" @click="openProductReg2(mainData.sno, each.sno, 2)">
                        {% each.sampleCnt %}종 제작
                    </div>
                    <div class="sl-green font-11 hover-btn cursor-pointer mgt5" v-if="each.sampleConfirmSno > 0" @click="openProductWithSample(mainData.sno, each.sno, each.sampleConfirmSno)">
                        (확정)
                    </div>
                    <div class="btn btn-white btn-sm mgt5" @click="openProductWithSample(mainData.sno, each.sno, -1, 1)">샘플등록</div>
                </div>
                <div v-else-if="90 == mainData.projectStatus || 91 == mainData.projectStatus" class="font-13">
                    등록샘플없음
                </div>
                <div v-else class="dp-flex dp-flex-gap5">
                    <div class="btn btn-red btn-red-line2 btn-sm pdt11 pdb10" @click="openProductWithSample(mainData.sno, each.sno, -1, 1)">등록</div>
                    <div class="btn btn-blue btn-blue-line btn-sm" @click="setSampleNothing(each.sno, mainData.sno, -1)">샘플진행<br>없음처리</div>
                </div>
            </div>
            <div v-else class="font-11">
                샘플진행<br>없음
                <!--
                <div class="btn btn-black btn-sm" @click="setSampleNothing(each.sno, mainData.sno, 0)">샘플진행처리</div>
                -->
            </div>
        </div>

    </div>
    <!---------------------------------  상품 납기 --------------------------------->
    <div v-if="'deliveryDt' === fieldData.name" >
        <table class="table-borderless font-12 table-pd-0">
            <colgroup>
                <col class="w-50px" />
                <col style="width:2px;" />
                <col />
            </colgroup>
            <tr>
                <td class="ta-r font-11" style="height:0!important;">
                    <span v-show="!<?=$modifyKey?>" class="sl-blue">MS납기</span>
                </td>
                <td style="height:0!important;">:&nbsp;</td>
                <td class="sl-blue ta-l" style="height:0!important;">
                    <span v-show="<?=$modifyKey?>" class="sl-blue">MS납기</span>
                    <div v-show="<?=$modifyKey?>">
                        <date-picker v-model="each.msDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                    </div>
                    <div v-show="!<?=$modifyKey?>" class="sl-blue">
                        {% $.formatShortDate(each.msDeliveryDt) %}
                    </div>
                </td>
            </tr>
            <tr>
                <td class="ta-r font-11 text-danger" style="height:0!important;">
                    <span v-show="!<?=$modifyKey?>">고객납기</span>
                </td>
                <td style="height:0!important;">:&nbsp;</td>
                <td class="text-danger ta-l" style="height:0!important;">
                    <span v-show="<?=$modifyKey?>">고객납기</span>
                    <div v-show="<?=$modifyKey?>">
                        <date-picker v-model="each.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                    </div>
                    <div v-show="!<?=$modifyKey?>">
                        {% $.formatShortDate(each.customerDeliveryDt) %}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <!---------------------------------  수량  --------------------------------->
    <div v-if="'prdQty' === fieldData.name" >
        <div v-if="'f' == mainData.assortApproval || 'p' == mainData.assortApproval" class="font-16">
            <div v-if="'p' == mainData.assortApproval" class="bold font-13">
                {% $.setNumberFormat(each.prdExQty) %}장
                <span class="font-10 normal" v-if="each.msQty>0">
                        (미청구:{% $.setNumberFormat(each.msQty) %}장)
                    </span>
                <div class="font-11">(확정)</div>
            </div>
            <div v-if="'f' == mainData.assortApproval" class="sl-purple-imp">
                {% $.setNumberFormat(each.prdExQty) %}장
                <div class="font-11">(예정)</div>
            </div>
        </div>
        <div v-else class="sl-purple-imp">
            <div v-show="<?=$modifyKey?>" class="ta-l">
                <span class="sl-purple">▼ 예정 수량</span>
            </div>
            <?php $model='each.prdExQty'; $placeholder="'예상수량'" ?>
            <?php include './admin/ims/template/basic_view/_number.php'?>
            <span class="font-11">(예정)</span>
        </div>
    </div>
    <!---------------------------------  생산가  --------------------------------->
    <div v-if="'prdCost' === fieldData.name" >

        <div v-if="4 != mainData.projectType">
            <!--일반 생산가-->
            <!--1. 생산가 확정 시-->
            <div v-if="Number(each.prdCostConfirmSno) > 0" @click="openFactoryEstimateView(mainData.sno, each.sno, each.estimateConfirmSno, 'cost')" class="hover-btn cursor-pointer">
                <div class="bold sl-blue" v-if="Number(each.prdCostConfirmSno) > 0">
                    {% $.setNumberFormat(each.prdCost) %}원
                </div>
                <div class="font-11 font-bold sl-blue">(확정)</div>
            </div>
            <!--2-1. 생산가가 입력되었다면-->
            <div v-else-if="Number(each.estimateConfirmSno) > 0">
                <div class="sl-purple-imp">
                    <div class="hover-btn cursor-pointer"
                         @click="openFactoryEstimateView(mainData.sno, each.sno, each.estimateConfirmSno, 'cost')">
                        {% $.setNumberFormat(each.estimateCost) %}원
                    </div>
                    <div class="font-11">(예정 생산가)</div>
                </div>
            </div>
            <!--3. 견적 없을 때는 타겟 생산가   -->
            <div v-else class="text-deep-orange" >
                <div v-show="!<?=$modifyKey?>" >
                    <div class="dp-flex dp-flex-center">
                        {% $.setNumberFormat(each.targetPrdCost) %}원
                    </div>
                    <div class="font-11 text-deep-orange">
                        (타겟 생산가)
                    </div>
                </div>
                <div v-show="<?=$modifyKey?>" class="ta-l relative">
                    <input type="text" class="form-control" v-model="each.targetPrdCost">
                    <div style="position:absolute;top:25px; left:0">
                        타겟 생산가
                    </div>
                </div>
            </div>
            <div class="font-10 cursor-pointer hover-btn" @click="openProductReg2(mainData.sno, each.sno, 3)"
                 style="position: absolute;top:0;right:0;color: #000;background-color:#e9e9e9;padding:2px; border-radius: 5px">
                견적({% each.estimateCnt %})
            </div>
        </div>
        <div v-else>
            <div class="sl-blue bold" v-if="-1 == each.prdCostConfirmSno">
                <span class="cursor-pointer hover-btn" @click="openCommonPopup('product_prd_cost', 1040, 710, {sno:each.sno});">{% $.setNumberFormat(each.prdCost) %}원</span>
                <div class="font-11 sl-blue">(확정)</div>
            </div>
            <div class="sl-blue bold" v-else>
                <span class="cursor-pointer hover-btn" @click="openCommonPopup('product_prd_cost', 1040, 710, {sno:each.sno});">{% $.setNumberFormat(each.prdCost) %}원</span>
            </div>
        </div>

        <div class="text-muted" v-if="false">
            개발용 스타일상태:{% each.prdCostStatus %}
        </div>

    </div>

    <!---------------------------------  판매가  --------------------------------->
    <div v-if="'prdPrice' === fieldData.name" >

        <div class="text-center  text-danger" v-show="!<?=$modifyKey?> || (<?=$modifyKey?>  && 'p' === mainData.prdPriceApproval)">

            <div v-if="'p' === mainData.prdPriceApproval" class="bold">
                {% $.setNumberFormat(each.salePrice) %}원
                <!--판매가 승인시-->
                <div class="font-11 text-danger" >
                    (확정)
                </div>
            </div>
            <div v-else>
                <div v-show="each.salePrice > 0" class="">
                    {% $.setNumberFormat(each.salePrice) %}원
                    <div class="font-11 text-danger" >
                        (예정 판매가)
                    </div>
                </div>
                <div v-show="0 >= each.salePrice" >
                    <div class="dp-flex dp-flex-center">
                        <div class="dp-flex text-deep-orange dp-flex-center">
                            {% $.setNumberFormat(each.targetPrice) %}원
                        </div>
                        <div class="dp-flex text-deep-orange dp-flex-center" v-if="each.targetPriceMax > 0">
                            ~ {% $.setNumberFormat(each.targetPriceMax) %}원
                        </div>
                    </div>
                    <div class="font-11 text-deep-orange">
                        (타겟 판매가)
                    </div>
                </div>
            </div>
        </div>

        <div v-show="<?=$modifyKey?> && 'p' !== mainData.prdPriceApproval "> <!--확정 가격 수정 불가-->
            <div >
                <div class="dp-flex font-11">
                    타겟 판매가
                    <input type="number" class="form-control" v-model="each.targetPrice" :placeholder="'타겟 판매가'">
                </div>
                <div class="text-danger dp-flex font-11 mgt5">
                    예정 판매가
                    <input type="number" class="form-control" v-model="each.salePrice" :placeholder="'예정 판매가'">
                </div>
            </div>
        </div>

        <div style="" class="font-9 text-muted" v-if="'p' !== mainData.prdPriceApproval && (each.salePrice > 0 || each.targetPrice > 0)">
            <div v-if="each.salePrice > 0">
                <!--예상대비-->
                최대 권장 생산가:{% $.setNumberFormat(Math.round(each.salePrice*0.7)) %}원
                <br>(판매가의-30% 자동 계산)
            </div>
            <div v-else-if="each.targetPrice > 0">
                <!--타겟대비-->
                최대 권장 생산가:{% $.setNumberFormat(Math.round(each.targetPrice*0.7)) %}원
                <br>(타겟 판매가의-30% 자동 계산)
            </div>
        </div>

    </div>
    <!---------------------------------  마진  --------------------------------->
    <div v-if="'margin' === fieldData.name" >
        <span class="dp-flex" v-if="'p' === mainData.prdPriceApproval && Number(each.prdCostConfirmSno) > 0">
            <span class="font-10">(확)</span>
            {% each.margin %}%</span>
        <!--마진-->
        <span class="text-muted dp-flex" v-else>
            <span class="font-10">(가)</span>
            {% each.margin %}%
        </span>
    </div>
    <!---------------------------------  생산 MOQ   ---------------------------------->
    <div v-if="'prdMoq' === fieldData.name" >
        <?php $model='each.prdMoq'; $placeholder="'MOQ'" ?>
        <?php include './admin/ims/template/basic_view/_number.php'?>
        <div v-if="each.estimateConfirmSno > 0" class="font-10">
            <div v-if="each.estimateConfirmSno > 0" class="font-10">
                <div v-if="each.prdMoq > 0">
                    생산처 생산MOQ: {% $.setNumberFormat(each.productionPrdMoq) %}
                </div>
                <div v-if="each.priceMoq > 0">
                    생산처 단가MOQ: {% $.setNumberFormat(each.productionPriceMoq) %}
                </div>
            </div>
        </div>
    </div>
    <!---------------------------------  원단 MOQ   ---------------------------------->
    <div v-if="'fabricMoq' === fieldData.name" >
        <?php $model='each.fabricMoq'; $placeholder="'MOQ'" ?>
        <?php include './admin/ims/template/basic_view/_number.php'?>
    </div>
    <!---------------------------------  퀄리티  --------------------------------->
    <div v-if="'quality' === fieldData.name" >
        <div v-if="'y' === each.fabricPass" class="font-12 sl-blue">
            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
            해당없음
        </div>
        <div v-else>
            <div @click="openProductReg2(mainData.sno, each.sno, 1)" class="cursor-pointer hover-btn" v-show="!<?=$modifyKey?>">
                <span v-html="each.fabricStatusIcon" class="font-12"></span>
                <span v-html="each.fabricStatusKr" class="font-12"></span>
            </div>
        </div>
    </div>
    <!---------------------------------  BT  --------------------------------->
    <div v-if="'bt' === fieldData.name" >
        <div v-if="'y' === each.fabricPass" class="font-12 sl-blue">
            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
            해당없음
        </div>
        <div v-else>
            <div @click="openProductReg2(mainData.sno, each.sno, 1)" class="cursor-pointer hover-btn font-12">
                <span v-html="each.btStatusIcon" class="font-12"></span>
                <span v-html="each.btStatusKr" class="font-12"></span>
            </div>
        </div>
    </div>
    <!---------------------------------  작지  --------------------------------->
    <div v-if="'work' === fieldData.name" >
        <div v-if="'y' === each.isWorkModifyAuth || 90 == mainData.projectStatus || 91 == mainData.projectStatus ">
            <div>
                <span v-html="each.workStatusIcon"></span>
                <span v-html="each.workStatusKr" ></span>
            </div>
            <div class="mgt3">

                <div v-if="'y' === each.isWorkModify || each.workStatus > 0 || 90 == mainData.projectStatus || 91 == mainData.projectStatus "><!-- 작지가 이미 진행/승인된건은 그냥 보여준다.-->
                    <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkP_${each.sno}`,`<?=$eworkUrl?>?sno=${each.sno}`,1600,950);">보기</div>
                    <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkM_${each.sno}`,`./popup/ims_pop_ework.php?sno=${each.sno}&tabMode=main`,1300, 850)">수정/결재</div>
                </div>
                <div v-else>
                    <div class="btn btn-gray btn-sm" @click="popProduct=each;visibleWorkOrderPossibleStatus=true">
                        작지등록불가<br>(사유확인)
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div v-if="0 >= each.sampleConfirmSno" class="font-11">
                샘플을 확정해주세요
            </div>
        </div>
    </div>
    <!---------------------------------  기획  --------------------------------->
    <div v-if="'plan' === fieldData.name" >
        <div v-show="null == stylePlanList">
            <div class="ims-loader-container">
                <div class="ims-pure-loader"></div>
            </div>
        </div>
        <div v-show="null !== stylePlanList">

            <div class="hover-btn cursor-pointer" @click="openProductReg2(mainData.sno, each.sno, 7)" v-show="each.planCnt > 0">
                {% each.planCnt %}개 기획
            </div>
            <div class="btn btn-white btn-sm" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':mainData.sno, 'styleSno':each.sno});" v-show="each.planCnt > 0">등록</div>

            <div v-if="each.sampleConfirmSno >= 0 ">
                <div class="btn btn-red btn-red-line2 btn-sm" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':mainData.sno, 'styleSno':each.sno});" v-show="0 >= each.planCnt">등록</div>
            </div>
            <div v-else>
                -
            </div>

        </div>
    </div>

    <!--------------- 추정매출 ----------------------------->
    <div v-if="'extUnitPrice' === fieldData.name" >
        {% $.setNumberFormat(each.prdExQty * each.currentPrice) %}
    </div>
    
    <!--------------- 진행 형태  ( 두군데 동시 사용시 튜닝 필요 ) ----------------------------->
    <div v-if="'styleProcType' === fieldData.name">
        <div v-show="isModify">
            <div v-for="(eachValue, eachKey) in JS_LIB_CODE['styleProcType']" >
                <label class="radio-inline" >
                    <input type="radio" :name="'styleProcType'+idx"  :value="eachKey" v-model="each.styleProcType" class="mg0" />
                    <span class="">{% eachValue %}</span>
                </label>
            </div>
        </div>
        <div v-show="!isModify">
            {% JS_LIB_CODE['styleProcType'][each.styleProcType] %}
        </div>
    </div>
    <!--------------- 고객사 샘플  ( 두군데 동시 사용시 튜닝 필요 ) ----------------------------->
    <div v-if="'addedInfo.prd002' === fieldData.name">
        <div v-show="isModify">
            <div v-for="(eachValue, eachKey) in JS_LIB_CODE['custSampleType']" >
                <label class="radio-inline" >
                    <input type="radio" :name="'custSampleType'+idx"  :value="eachKey" v-model="each.addedInfo.prd002" class="mg0"/>
                    <span class="">{% eachValue %}</span>
                </label>
            </div>
        </div>
        <div v-show="!isModify">
            {% JS_LIB_CODE['custSampleType'][each.addedInfo.prd002] %}
        </div>
    </div>
    <!--------------- 스타일 시즌 ----------------------------->
    <div v-if="'prdSeason' === fieldData.name" >
        <div class="" >
            <div v-show="isModify">
                <select class="form-control" v-model="each.prdSeason" @change="setStyleName(each);setStyleCode(each, customer.styleCode)">
                    <option :value="key" v-for="(option, key) in JS_LIB_CODE.codeSeason">{% option %}</option>
                </select>
            </div>
            <div v-show="!isModify">
                {% JS_LIB_CODE.codeSeason[each.prdSeason] %}
            </div>
        </div>
    </div>
    <!--------------- 스타일 타입 ----------------------------->
    <div v-if="'prdType' === fieldData.name" >
        <div class="">
            <div v-show="isModify">
                <select class="form-control" v-model="each.prdStyle" @change="setStyleName(each);setStyleCode(each, customer.styleCode)">
                    <option :value="key" v-for="(option, key) in JS_LIB_CODE.codeStyle">{% option %}</option>
                </select>
            </div>
            <div v-show="!isModify">
                {% JS_LIB_CODE.codeStyle[each.prdStyle] %}
            </div>
        </div>
    </div>
    <!--------------- 스타일 ----------------------------->
    <div v-if="'prdStyle' === fieldData.name" >
        <div v-show="!isModify">
            {% each.prdStyle %}
        </div>
        <div v-show="isModify">
            <select class="js-example-basic-single sel-style border-line-gray w-100px" v-model="each.prdStyle" @change="setStyleName(each);setStyleCode(each, customer.styleCode)">
                <option value="">미정</option>
                <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                    <option value="<?=$codeKey?>"><?=$codeValue?></option>
                <?php } ?>
            </select>
        </div>
    </div>

</div>

<!-- ######################################################## -->
<!-- ################  스타일 커스텀 필드 종료 ################ -->
<!-- ######################################################## -->

<!--------------- 숫자형 ----------------------------->
<div v-else-if="'i' === fieldData.type">
    <?php $model='each[fieldData.name]'; $placeholder='fieldData.title' ?>
    <?php include './admin/ims/ims25/component/_number.php'?>
</div>
<!--------------- 날짜1 ( 00/00/00(월) ) ----------------------------->
<div v-else-if="'d1' === fieldData.type">
    <?php $model='each[fieldData.name]'; $placeholder='fieldData.title' ?>
    <?php include './admin/ims/ims25/component/_picker.php'?>
</div>
<!--------------- 날짜2 ( 00/00/00 )----------------------------->
<div v-else-if="'d2' === fieldData.type">
    <?php $model='each[fieldData.name]'; $placeholder='fieldData.title' ?>
    <?php include './admin/ims/ims25/component/_picker2.php'?>
</div>
<!--------------- 수정불가 고정 문자 ----------------------------->
<div v-else-if="'textOnly' === fieldData.type">
    {% fieldData.name %}
</div>
<!--------------- 추가 정보 addinfo 텍스트 ----------------------------->
<div v-else-if="'as' === fieldData.type">
    <?php $model='each.addedInfo[fieldData.name]'; $placeholder='fieldData.title' ?>
    <?php include './admin/ims/ims25/component/_text.php'?>
</div>
<!--------------- 그 외 일반 텍스트 ----------------------------->
<div v-else>
    <?php $model='each[fieldData.name]'; $placeholder='fieldData.title' ?>
    <?php include './admin/ims/ims25/component/_text.php'?>
</div>



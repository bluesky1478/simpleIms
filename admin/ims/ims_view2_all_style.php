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
        <?php foreach($prdSetupData2['list'] as $each) { ?>
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
        <?php foreach($prdSetupData2['list'] as $each) { ?>
            <th>
                <b><?=$each[0]?></b>
                <?php if( '상품명' === $each[0] ){ ?>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('name')" ></i>
                    <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('code')" ></i>
                    <i class="fa fa-files-o text-muted cursor-pointer sl-blue font-14" aria-hidden="true" @click="copyStyleName('goods_info')" ></i>
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
    <tr v-if="isStyleModify" >
        <th colspan="4" style="background-color:#fffdf2 !important;">
            일괄작업
        </th>
        <th style="background-color:#fffdf2 !important;">
            <div class="dp-flex dp-flex-gap5">
                연도:
                <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="project.projectYear" >
                    <?php foreach($yearList as $codeValue) { ?>
                        <option value="<?=$codeValue?>"><?=$codeValue?></option>
                    <?php } ?>
                </select>
                시즌:
                <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="project.projectSeason" >
                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>"><?=$codeKey?></option>
                    <?php } ?>
                </select>

                <div class="btn btn-sm btn-gray" @click="applyYearSeason()">적용</div>

            </div>
        </th>
        <th style="background-color:#fffdf2 !important;"></th>
        <th class="dp-flex pdb5 pdt5" style="background-color:#fffdf2 !important;">
            <table class="table-borderless font-12 table-pd-0" >
                <colgroup>
                    <col class="w-50px" />
                    <col style="width:2px;" />
                    <col />
                </colgroup>
                <tr>
                    <td class="ta-r font-11 sl-blue">MS납기</td>
                    <td >:&nbsp;</td>
                    <td class="sl-blue">
                        <div style="width:100px">
                            <date-picker v-model="project.msDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="h3"></td>
                </tr>
                <tr>
                    <td class="ta-r font-11 text-danger">고객납기</td>
                    <td >:&nbsp;</td>
                    <td class="text-danger">
                        <div style="width:100px">
                            <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                        </div>
                    </td>
                </tr>
            </table>
            
            <div class="btn btn-sm btn-gray" @click="applyDeliveryDt()">적용</div>
            
        </th>
        <th colspan="99" style="background-color:#fffdf2 !important;"></th>
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
            <input type="checkbox" name="prdSno" :value="product.sno" class="prd-sno"
                   :data-name="product.productName"
                   :data-code="product.styleCode"
                   :data-cnt="product.prdExQty"
                   :data-cost="product.prdCost"
                   :data-price="product.salePrice"
                   :data-estimate-cost="product.estimateCost"
                   :data-margin="product.margin"
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
                    <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="product.prdYear" @change="setStyleCode(product, customer.styleCode)">
                        <?php foreach($yearList as $codeValue) { ?>
                            <option value="20<?=$codeValue?>"><?=$codeValue?></option>
                        <?php } ?>
                    </select>
                    <!--시즌-->
                    <select class="js-example-basic-single sel-style border-line-gray w-50px" v-model="product.prdSeason" @change="setStyleCode(product, customer.styleCode)">
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
                    <input type="text" class="form-control w-70px" v-model="product.addStyleCode" placeholder="보조코드" @input="setStyleCode(product, customer.styleCode)">
                </div>
                <!--스타일명-->
                <div class="dp-flex mgt3">
                    스타일명:
                    <input type="text" class="form-control w-230px" v-model="product.productName" placeholder="스타일명">
                </div>
                <div class="mgt5 ">
                    <div class="round-box bg-light-gray">
                        스타일코드:
                        {% product.styleCode.toUpperCase() %}
                    </div>
                </div>
            </div>

            <div v-show="!isStyleModify" >
                <span class="font-14 hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                    {% (product.prdYear+'').substring(2,4) %}
                    {% product.prdSeason %}
                    {% product.productName %}
                </span>
                <span>
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.productName)"></i>
                </span>
                <span class="text-muted hover-btn cursor-pointer" v-show="product.sno > 0"
                     @click="ImsService.deleteData('projectProduct',product.sno, ()=>{ refreshProductList(sno) })">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
                <span class="text-muted hover-btn cursor-pointer" v-show="$.isEmpty(product.sno) || 0 >= product.sno"
                     @click="deleteElement(productList, prdIndex)">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </span>
                <br>
                <span class="text-muted">
                    {% product.styleCode.toUpperCase() %}
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode.toUpperCase())"></i>
                </span>
            </div>

            <div style="position: absolute; top:2px; right:2px; display: none">
                <input type="button" value="이슈 관리" class="btn btn-red btn-sm btn-red-line2"
                       @click="openCommonPopup('project_issue_upsert', 1000, 910, {'sno':0,'customerSno':project.customerSno,'projectSno':project.sno,'styleSno':product.sno});" >
            </div>

        </td>
        <td><!--샘플수-->

            <div v-if="true === project.isReorder">
                리오더<span class="font-10">(해당없음)</span>
            </div>
            <div v-else>
                <div v-if="product.sampleConfirmSno >= 0">

                    <div v-if="product.sampleCnt > 0">
                        <div class="hover-btn cursor-pointer font-13" @click="openProductReg2(project.sno, product.sno, 2)">
                            {% product.sampleCnt %}종 제작
                        </div>
                        <div class="sl-green font-11 hover-btn cursor-pointer mgt5" v-if="product.sampleConfirmSno > 0" @click="openProductWithSample(project.sno, product.sno, product.sampleConfirmSno)">
                            (확정)
                        </div>
                        <div class="btn btn-white btn-sm mgt5" @click="openProductWithSample(project.sno, product.sno, -1, 1)">샘플등록</div>
                    </div>
                    <div v-else-if="90 == project.projectStatus || 91 == project.projectStatus" class="font-13">
                        등록샘플없음
                    </div>
                    <div v-else class="dp-flex dp-flex-gap5">
                        <div class="btn btn-red btn-red-line2 btn-sm pdt11 pdb10" @click="openProductWithSample(project.sno, product.sno, -1, 1)">등록</div>
                        <div class="btn btn-blue btn-blue-line btn-sm" @click="setSampleNothing(product.sno, project.sno, -1)">샘플진행<br>없음처리</div>
                    </div>
                </div>
                <div v-else class="font-11">
                    샘플진행<br>없음
                    <!--
                    <div class="btn btn-black btn-sm" @click="setSampleNothing(product.sno, project.sno, 0)">샘플진행처리</div>
                    -->
                </div>
            </div>
        </td>
        <td class="pd0 ta-c"><!--고객납기-->
            <table class="table-borderless font-12 table-pd-0" >
                <colgroup>
                    <col class="w-50px" />
                    <col style="width:2px;" />
                    <col />
                </colgroup>
                <tr>
                    <td class="ta-r font-11 sl-blue">MS납기</td>
                    <td >:&nbsp;</td>
                    <td class="sl-blue">
                        <div v-show="isStyleModify">
                            <date-picker v-model="product.msDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                        </div>
                        <div v-show="!isStyleModify">
                            {% $.formatShortDate(product.msDeliveryDt) %}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="h3"></td>
                </tr>
                <tr>
                    <td class="ta-r font-11 text-danger">고객납기</td>
                    <td >:&nbsp;</td>
                    <td class="text-danger">
                        <div v-show="isStyleModify">
                            <date-picker v-model="product.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker>
                        </div>
                        <div v-show="!isStyleModify">
                            {% $.formatShortDate(product.customerDeliveryDt) %}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td class="relative"><!--제작수량-->
<!--            아소트 결재 승인(project에 있음)값이 f(반려) or p(승인) 이면 아래 뿌림-->
            <div v-if="'f' == project.assortApproval || 'p' == project.assortApproval" class="font-16">
                <div v-if="'p' == project.assortApproval" class="bold font-13">
                    {% $.setNumberFormat(product.prdExQty) %}장
                    <span class="font-10 normal" v-if="product.msQty>0">
                        (미청구:{% $.setNumberFormat(product.msQty) %}장)
                    </span>
                    <div class="font-11">(확정)</div>
                </div>
                <div v-if="'f' == project.assortApproval" class="sl-purple-imp">
                    {% $.setNumberFormat(product.prdExQty) %}장
                    <div class="font-11">(예정)</div>
                </div>
            </div>
            <div v-else class="sl-purple-imp">
                <div v-show="isStyleModify" class="ta-l">
                    <span class="sl-purple">▼ 예정 수량</span>
                </div>
                <?php $modifyKey='isStyleModify'; $model='product.prdExQty'; $placeholder='예상수량' ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
                <span class="font-11">(예정)</span>
            </div>

            <!--
            <div class="font-10" style="position:absolute;bottom:0; right:0; background-color:#e9e9e9; padding:2px; border-radius: 5px;" v-show="!isStyleModify && product.msQty > 0">
                미청구:
                {% $.setNumberFormat(product.msQty) %}장
            </div>
            -->
            <!--<div v-show="isStyleModify" class="ta-l mgt5">
                <span class="sl-blue">▼ 미청구수량</span>
                <input type="number" class="form-control" v-model="product.msQty" placeholder="미청구수량">
            </div>-->
        </td>
        <!--생산가-->
        <td class="relative">

            <div v-if="4 != project.projectType">
                <!--일반 생산가-->
                <!--1. 생산가 확정 시-->
                <div v-if="Number(product.prdCostConfirmSno) > 0" @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')" class="hover-btn cursor-pointer">
                    <div class="bold sl-blue" v-if="Number(product.prdCostConfirmSno) > 0">
                        {% $.setNumberFormat(product.prdCost) %}원
                    </div>
                    <div class="font-11 font-bold sl-blue">(확정)</div>
                </div>
                <!--2-1. 생산가가 입력되었다면-->
                <div v-else-if="Number(product.estimateConfirmSno) > 0">
                    <div class="sl-purple-imp">
                        <div class="hover-btn cursor-pointer"
                             @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                            {% $.setNumberFormat(product.estimateCost) %}원
                        </div>
                        <div class="font-11">(예정 생산가)</div>
                    </div>
                </div>
                <!--3. 견적 없을 때는 타겟 생산가   -->
                <div v-else class="text-deep-orange" >
                    <div v-show="!isStyleModify" >
                        <div class="dp-flex dp-flex-center">
                            {% $.setNumberFormat(product.targetPrdCost) %}원
                        </div>
                        <div class="font-11 text-deep-orange">
                            (타겟 생산가)
                        </div>
                    </div>
                    <div v-show="isStyleModify" class="ta-l relative">
                        <input type="text" class="form-control" v-model="product.targetPrdCost">
                        <div style="position:absolute;top:25px; left:0">
                            타겟 생산가
                        </div>
                    </div>
                </div>
                <div class="font-10 cursor-pointer hover-btn" @click="openProductReg2(project.sno, product.sno, 3)"
                     style="position: absolute;top:0;right:0;color: #000;background-color:#e9e9e9;padding:2px; border-radius: 5px">
                    견적({% product.estimateCnt %})
                </div>
            </div>
            <div v-else>
                <div class="sl-blue bold" v-if="-1 == product.prdCostConfirmSno">
                    <span class="cursor-pointer hover-btn" @click="openCommonPopup('product_prd_cost', 1040, 710, {sno:product.sno});">{% $.setNumberFormat(product.prdCost) %}원</span>
                    <div class="font-11 sl-blue">(확정)</div>
                </div>
                <div class="sl-blue bold" v-else>
                    <span class="cursor-pointer hover-btn" @click="openCommonPopup('product_prd_cost', 1040, 710, {sno:product.sno});">{% $.setNumberFormat(product.prdCost) %}원</span>
                </div>
            </div>

            <div class="text-muted" v-if="false">
                개발용 스타일상태:{% product.prdCostStatus %}
            </div>

        </td>
        <!--판매가-->
        <td>

            <div class="text-center  text-danger" v-show="!isStyleModify || (isStyleModify  && 'p' === project.prdPriceApproval)">

                <div v-if="'p' === project.prdPriceApproval" class="bold">
                    {% $.setNumberFormat(product.salePrice) %}원
                    <!--판매가 승인시-->
                    <div class="font-11 text-danger" >
                        (확정)
                    </div>
                </div>
                <div v-else>
                    <div v-show="product.salePrice > 0" class="">
                        {% $.setNumberFormat(product.salePrice) %}원
                        <div class="font-11 text-danger" >
                            (예정 판매가)
                        </div>
                    </div>
                    <div v-show="0 >= product.salePrice" >
                        <div class="dp-flex dp-flex-center">
                            <div class="dp-flex text-deep-orange dp-flex-center">
                                {% $.setNumberFormat(product.targetPrice) %}원
                            </div>
                            <div class="dp-flex text-deep-orange dp-flex-center" v-if="product.targetPriceMax > 0">
                                ~ {% $.setNumberFormat(product.targetPriceMax) %}원
                            </div>
                        </div>
                        <div class="font-11 text-deep-orange">
                            (타겟 판매가)
                        </div>
                    </div>
                </div>
            </div>

            <div v-show="isStyleModify && 'p' !== project.prdPriceApproval "> <!--확정 가격 수정 불가-->
                <div >
                    <div class="dp-flex font-11">
                        타겟 판매가
                        <input type="number" class="form-control" v-model="product.targetPrice" placeholder="타겟판매가">
                    </div>
                    <div class="text-danger dp-flex font-11 mgt5">
                        예정 판매가
                        <input type="number" class="form-control" v-model="product.salePrice">
                    </div>
                </div>
            </div>

            <div style="" class="font-9 text-muted" v-if="'p' !== project.prdPriceApproval && (product.salePrice > 0 || product.targetPrice > 0)">
                <div v-if="product.salePrice > 0">
                    <!--예상대비-->
                    최대 권장 생산가:{% $.setNumberFormat(Math.round(product.salePrice*0.7)) %}원
                    <br>(판매가의-30% 자동 계산)
                </div>
                <div v-else-if="product.targetPrice > 0">
                    <!--타겟대비-->
                    최대 권장 생산가:{% $.setNumberFormat(Math.round(product.targetPrice*0.7)) %}원
                    <br>(타겟 판매가의-30% 자동 계산)
                </div>
            </div>

        </td>
        <td >
            <span class="dp-flex" v-if="'p' === project.prdPriceApproval && Number(product.prdCostConfirmSno) > 0">
                <span class="font-10">(확)</span>{% product.margin %}%
            </span>
            <!--마진-->
            <span class="text-muted dp-flex" v-else>
                <span class="font-10">(가)</span>{% product.margin %}%
            </span>
        </td>

        <!--고객 MOQ-->
        <td>
            <?php $modifyKey='isStyleModify'; $model='product.moq'; $placeholder='MOQ' ?>
            <?php include './admin/ims/template/basic_view/_number.php'?>
            <div v-if="product.estimateConfirmSno > 0" class="font-10">
                <div v-if="product.prdMoq > 0">
                    생산MOQ: {% $.setNumberFormat(product.prdMoq) %}
                </div>
                <div v-if="product.priceMoq > 0">
                    단가MOQ: {% $.setNumberFormat(product.priceMoq) %}
                </div>
            </div>
        </td>
        <td><!--퀄리티-->
            <div v-if="'y' === product.fabricPass" class="font-12 sl-blue">
                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                해당없음
            </div>
            <div v-else>
                <div @click="openProductReg2(project.sno, product.sno, 1)" class="cursor-pointer hover-btn" v-show="!isStyleModify">
                    <span v-html="product.fabricStatusIcon" class="font-12"></span>
                    <span v-html="product.fabricStatusKr" class="font-12"></span>
                </div>
            </div>
        </td>
        <td><!--BT-->
            <div v-if="'y' === product.fabricPass" class="font-12 sl-blue">
                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>
                해당없음
            </div>
            <div v-else>
                <div @click="openProductReg2(project.sno, product.sno, 1)" class="cursor-pointer hover-btn font-12">
                    <span v-html="product.btStatusIcon" class="font-12"></span>
                    <span v-html="product.btStatusKr" class="font-12"></span>
                </div>
            </div>
        </td>
        <td><!--작업지시서-->
            <div v-if="'y' === product.isWorkModifyAuth || 90 == project.projectStatus || 91 == project.projectStatus ">
                <div>
                    <span v-html="product.workStatusIcon"></span>
                    <span v-html="product.workStatusKr" ></span>
                </div>
                <div class="mgt3">

                    <div v-if="'y' === product.isWorkModify || product.workStatus > 0 || 90 == project.projectStatus || 91 == project.projectStatus "><!-- 작지가 이미 진행/승인된건은 그냥 보여준다.-->
                        <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkP_${product.sno}`,`<?=$eworkUrl?>?sno=${product.sno}`,1600,950);">보기</div>
                        <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkM_${product.sno}`,`./popup/ims_pop_ework.php?sno=${product.sno}&tabMode=main`,1300, 850)">수정/결재</div>
                    </div>
                    <div v-else>
                        <div class="btn btn-gray btn-sm" @click="popProduct=product;visibleWorkOrderPossibleStatus=true">
                            작지등록불가<br>(사유확인)
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div v-if="0 >= product.sampleConfirmSno" class="font-11">
                    샘플을 확정해주세요
                </div>
            </div>
        </td>
        <td>
            <div v-if="product.planConfirmSno >= 0">
                <div v-if="product.planCnt > 0">
                    <div class="hover-btn cursor-pointer font-13" @click="openProductReg2(project.sno, product.sno, 7)">
                        {% product.planCnt %}개 기획
                    </div>
                    <div class="sl-green font-11 hover-btn cursor-pointer mgt5" v-if="product.planConfirmSno > 0" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':project.sno, 'styleSno':product.sno, 'sno':product.planConfirmSno});">
                        (확정)
                    </div>
                    <div class="btn btn-white btn-sm mgt5" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':project.sno, 'styleSno':product.sno});">기획등록</div>
                </div>
                <div v-else-if="90 == project.projectStatus || 91 == project.projectStatus" class="font-13">
                    기획없음
                </div>
                <div v-else class="dp-flex dp-flex-gap5">
                    <div class="btn btn-red btn-red-line2 btn-sm pdt11 pdb10" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':project.sno, 'styleSno':product.sno});">등록</div>
                    <div class="btn btn-blue btn-blue-line btn-sm" @click="ImsNkService.setStylePlanConfirm(product.sno, project.sno, -1)">확정기획<br>없음처리</div>
                </div>
            </div>
            <div v-else class="font-11">
                확정기획<br>없음
            </div>
            <!--<div class="hover-btn cursor-pointer btn-sm btn btn-white" @click="simpleLayerPrdData=product;simpleLayerPrd=true">
                <i class="fa fa-info-circle" aria-hidden="true"></i> 기획정보
            </div>-->
        </td>

    </tr>
    <tr v-if="0 >= productList.length">
        <td colspan="99" class="text-center ">
            <div class="pd20 font-16">
                등록된 스타일이 없습니다.
            </div>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="99" class="text-left bg-light-gray2" >
            <div class="col-xs-6 dp-flex dp-flex-gap5" >
                일괄견적:
                <select class="form-control" style="width:150px;background-color:#fff" v-model="batchEstimateFactory" >
                    <option value="0">미정</option>
                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                        <option value="<?=$key?>"><?=$value?></option>
                    <?php } ?>
                </select>

                <button type="button" class="btn btn-blue-line pdb6" @click="goBatchEstimate(project.sno, 'estimate')"><i class="fa fa-krw" aria-hidden="true"></i> 가견적 일괄 요청</button>
                <button type="button" class="btn btn-red-line2 btn-red pdb6" @click="goBatchEstimate(project.sno, 'cost')"><i class="fa fa-krw" aria-hidden="true"></i> 생산가 일괄 요청</button>

                <div class="btn btn-gray pdb6" @click="costReset(project.sno)">생산가 초기화</div>
            </div>
            <div class="col-xs-6 dp-flex" style="justify-content: flex-end">
                <!--선택한 스타일을
                <div class="btn btn-white">삭제</div>
                <div class="btn btn-red ">스타일추가</div> -->
                <div class="btn btn-red  " @click="saveStyleList(true)" v-show="isStyleModify">스타일 저장</div>
                <div class="btn btn-white w-50px" @click="isStyleModify=false" v-show="isStyleModify">취소</div>
                <div class="btn btn-red  btn-red-line2" v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;스타일 수정&nbsp;&nbsp;</div>
                <div class="btn btn-blue btn-blue-line " @click="addSalesStyle()">+ 스타일 추가</div>
                <button type="button" class="btn btn-white" @click="copyProduct(project.sno)"><i class="fa fa-files-o" aria-hidden="true"></i> 스타일 복사</button>
                <button type="button" class="btn btn-white" @click="copyProductToTargetProject(project.sno)"><i class="fa fa-files-o" aria-hidden="true"></i> 다른 프로젝트에 스타일 복사</button>

                <!--<div class="btn btn-red">스타일복사</div>
                <div class="btn btn-red">고객 견적서 발송</div>-->
            </div>
        </td>
    </tr>
    </tfoot>
</table>


<!--기획정보 모달-->
<ims-modal :visible.sync="simpleLayerPrd" max-width="700px" title="상품 기획 정보" >
    <div class="pd0" v-if="null !== simpleLayerPrdData">
        <table class="table table-pd-3 table-td-height30 table-th-height30 table-pd-5">
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
                <th class="ta-c">단가정보</th>
                <td class="ta-l">
                    <div v-if="simpleLayerPrdData.currentPrice > 0">
                        현재단가:{% $.setNumberFormat(simpleLayerPrdData.currentPrice) %}원
                    </div>
                    <div v-if="simpleLayerPrdData.targetPrdCost > 0">
                        타겟생산가:{% $.setNumberFormat(simpleLayerPrdData.targetPrdCost) %}원
                    </div>
                    <div >
                        타겟단가:{% $.setNumberFormat(simpleLayerPrdData.targetPrice) %}원
                        <span v-if="simpleLayerPrdData.targetPriceMax > 0">
                            ~ {% $.setNumberFormat(simpleLayerPrdData.targetPriceMax) %}원
                        </span>
                    </div>
                </td>
            </tr>
            <tr v-if="!$.isEmpty(simpleLayerPrdData.addedInfo.prd010)">
                <th class="ta-c">컨셉</th>
                <td class="ta-l">
                    <div >
                        {% simpleLayerPrdData.addedInfo.prd010 %}
                    </div>
                </td>
            </tr>
            <tr v-if="!$.isEmpty(simpleLayerPrdData.addedInfo.prd011)">
                <th class="ta-c">컬러</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.addedInfo.prd011 %}
                </td>
            </tr>
            <tr v-if="!$.isEmpty(simpleLayerPrdData.addedInfo.prd012)">
                <th class="ta-c">기능</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.addedInfo.prd012 %}
                </td>
            </tr>
            <tr v-if="!$.isEmpty(simpleLayerPrdData.addedInfo.prd013)">
                <th class="ta-c">원단</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.addedInfo.prd013 %}
                </td>
            </tr>
            <tr v-if="!$.isEmpty(simpleLayerPrdData.addedInfo.prd014)">
                <th class="ta-c">추가옵션</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.addedInfo.prd014 %}
                </td>
            </tr>
            <tr v-if="!$.isEmpty(simpleLayerPrdData.addedInfo.prd015)">
                <th class="ta-c">로고사양</th>
                <td class="ta-l">
                    {% simpleLayerPrdData.addedInfo.prd015 %}
                </td>
            </tr>

        </table>
    </div>
    <template #footer>
        <div class="btn btn-white mgt5" @click="simpleLayerPrd=false; simpleLayerPrdData=null">닫기</div>
    </template>
</ims-modal>

<ims-modal :visible.sync="visibleWorkOrderPossibleStatus" title="작지 등록/수정 조건">
    <div>
        * 아래 조건이 충족되어야 작지 등록/수정 가능.
        <table class="w-100p">
            <colgroup>
                <col class="w-25p">
                <col class="w-25p">
                <col class="w-25p">
                <col class="w-25p">
            </colgroup>
            <tr>
                <th>샘플지시서</th>
                <th>샘플리뷰서</th>
                <th>샘플확정서</th>
                <th>고객 샘플확정</th>
            </tr>
            <tr>
                <td>
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === popProduct.sampleFile1Exsists"></i>
                    <span class="text-muted" v-else>-</span> <!--샘플지시서-->
                </td>
                <td>
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === popProduct.sampleFile4Exsists"></i>
                    <span class="text-muted" v-else>-</span> <!--샘플리뷰서-->
                </td>
                <td>
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === popProduct.sampleFile6Exsists"></i>
                    <span class="text-muted" v-else>-</span> <!--샘플확정서-->
                </td>
                <td>
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="popProduct.sampleConfirmSno > 0"></i>
                    <span class="text-muted" v-else>-</span> <!--샘플확정상태-->
                </td>
            </tr>
        </table>
    </div>
    <!--</div>-->
    <template #footer>
        <div class="btn btn-white mgt5" @click="visibleWorkOrderPossibleStatus=false">닫기</div>
    </template>
</ims-modal>
<?php include 'library_all.php'?>
<?php include 'library.php'?>

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>
    .bootstrap-filestyle input{display: none }
    .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}
    .mx-input {padding:0 12px !important; font-size: 14px !important; }
    .pd-custom { padding:10px 15px 15px 15px !important; }
    .gd-help-manual { font-size:16px !important;}
    .ims-style-attribute-table td{border-bottom: none !important;}
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom: 0 !important;">
            <h3>
                <span class="sl-purple">{% items.customerName %}</span> {% product.productName %}
                <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(items.customerName+' '+product.productName)"></i>
                (
                    <span class="sl-blue">{% product.styleCode %}</span>
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode)"></i>
                )
            </h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            <input type="button" value="저장" class="btn btn-red btn-register" @click="save(product)" style="margin-right:178px">
            <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(product.sno, 'product')" style="margin-right:75px">
        </div>
    </form>

    <!-- 기본 정보 -->
    <div class="row mgt10">
        <div class="col-xs-4">
            <div class="table-title gd-help-manual">
                <div class="flo-left cursor-pointer hover-btn" v-show="!showImage" @click="showImage=true">스타일이미지 <div class="btn btn-sm btn-white">▼ 보기</div></div>
                <div class="flo-left cursor-pointer hover-btn" v-show="showImage" @click="showImage=false">스타일이미지 <div class="btn btn-sm btn-white">▲ 닫기</div></div>
                <div class="flo-right"></div>
            </div>
        </div>
    </div>
    <div class="row mgt10 ims-product-image" v-show="showImage">
        <?php foreach( $thumbnailFieldList as $thumbnailField ){ ?>
            <div class="col-xs-4">
                <div class="table-title gd-help-manual">
                    <div class="flo-left"><?=$thumbnailField['title']?></div>
                    <div class="flo-right">
                        <div class="notice-info">업로드 후 저장시 적용됩니다.</div>
                    </div>
                </div>
                <table class="table table-cols">
                    <tbody>
                    <tr>
                        <td>
                            <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(product.<?=$thumbnailField['field']?>)" style="height:150px;">
                            <img :src="product.<?=$thumbnailField['field']?>" v-show="!$.isEmpty(product.<?=$thumbnailField['field']?>)" style="height:150px;">
                        </td>
                    </tr>
                    <tr>
                        <th >
                            <div class="text-right">
                                <form @submit.prevent="uploadFile">
                                    <input :type="'file'" ref="<?=$thumbnailField['field']?>" style="display: block;width:1px!important;" />
                                    <input type="button" class="btn btn-black" value="업로드" @click="uploadFile(product,'<?=$thumbnailField['field']?>')"  />
                                </form>
                            </div>

                            <div class="btn btn-sm btn-white" v-show="!$.isEmpty(product.<?=$thumbnailField['field']?>)"
                                 @click="()=>{ product.<?=$thumbnailField['field']?>=''; }">썸네일 삭제 (저장 후 적용)</div>

                        </th>
                    </tr>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>

    <!-- FIXME 기본 정보 -->
    <div class="row" v-show="true">
        <div class="col-xs-12 pd-custom">
            <div class="table-title gd-help-manual">
                <div class="flo-left">기본정보</div>
                <div class="flo-right"></div>
            </div>
            <table class="table table-cols table-ims-product-detail" style="margin-bottom:0">
                <colgroup>
                    <col class="width-md"/>
                    <col class="width-xl"/>
                    <col class="width-md"/>
                    <col class="width-xl"/>
                    <col class="width-md"/>
                    <col class="width-xl"/>
                    <col class="width-md"/>
                    <col class="width-xl"/>
                </colgroup>
                <tbody>
                <tr>
                    <th>스타일속성</th>
                    <td colspan="5" class="pd0">
                        <table class="table table-cols table-center table-pd-5 ims-style-attribute-table" style="margin:0 !important;">
                            <colgroup>
                                <col style="width:20%" />
                                <col style="width:14%" />
                                <col style="width:14%" />
                                <col style="width:11%" />
                                <col style="width:20%" />
                                <col style="width:20%" />
                            </colgroup>
                            <tr>
                                <th>스타일</th>
                                <th>생산년도</th>
                                <th>시즌</th>
                                <th>남/여</th>
                                <th>색상</th>
                                <th>보조코드</th>
                            </tr>
                            <tr>
                                <td class="font-14">
                                    <select2 id="sel-style" class="js-example-basic-single" v-model="product.prdStyle" style="width:100%;" @change="setStyleCode(product,items.styleCode); setStyleName(product)">
                                        <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                            <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <td class="font-14">
                                    <select2 class="js-example-basic-single" v-model="product.prdYear" style="width:100%" @change="setStyleCode(product,items.styleCode)">
                                        <?php foreach($yearList as $codeValue) { ?>
                                            <option value="20<?=$codeValue?>">20<?=$codeValue?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <td class="font-14">
                                    <select2 class="js-example-basic-single" v-model="product.prdSeason" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                                        <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                            <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <td class="font-14">
                                    <select2 class="js-example-basic-single" v-model="product.prdGender" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                                        <option>구분없음</option>
                                        <?php foreach($codeGender as $codeKey => $codeValue) { ?>
                                            <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <td class="font-14">
                                    <select2 class="js-example-basic-single" v-model="product.prdColor" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                                        <option>구분없음</option>
                                        <?php foreach($codeColor as $codeKey => $codeValue) { ?>
                                            <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <td class="font-14">
                                    <input type="text" class="form-control width-lg ims-number" placeholder="보조코드" v-model="product.addStyleCode" @change="setStyleCode(product,items.styleCode)" @keyup="setStyleCode(product,items.styleCode)">
                                </td>
                            </tr>
                        </table>
                    </td>
                    <th class="required font-18">
                        제품명
                        <div class="text-muted font-14">스타일코드</div>
                    </th>
                    <td class="font-16">
                        <div>
                            <input type="text" class="form-control width-lg" placeholder="제품명" v-model="product.productName" style="font-size:13px">
                        </div>
                        <div class="mgt5">
                            <span class="font-16 bold">{% product.styleCode %}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="font-14">
                        <span class="sl-blue font-15">이노버 납기일</span>
                    </th>
                    <td class="font-14">
                        <date-picker v-model="product.msDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="이노버 납기일" style="width:140px;font-weight: normal; " ></date-picker>
                    </td>
                    <th>
                        <span class="text-danger  font-14">고객납기일</span>
                    </th>
                    <td class="font-14">
                        <date-picker v-model="product.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="고객 납기일" style="width:140px;font-weight: normal"></date-picker>
                    </td>
                    <th>생산처</th>
                    <td class="font-14">
                        <select2 class="js-example-basic-single" style="width:100%" v-model="product.produceCompanySno">
                            <option value="0">미정</option>
                            <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                    </td>
                    <th>생산형태/국가</th>
                    <td class="font-14">
                        <select class="form-control font-14 inline-block" v-model="product.produceType" style="height:30px;">
                            <?php foreach ($prdType as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                        <select class="form-control font-14 inline-block" v-model="product.produceNational" style="height:30px;">
                            <option value="">미정</option>
                            <?php foreach ($prdNational as $key => $value ) { ?>
                                <option value="<?=$value?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th class="font-15">
                        <span v-if="4 != project.projectType" >생산 가견적</span>
                    </th>
                    <td class="font-15" >

                        <!--기성복 판매가격-->
                        <span v-if="4 == project.projectType" class="bold">
                            기성복 스타일
                        </span>

                        <!--기타-->
                        <span v-if="4 != project.projectType">
                            {% $.setNumberFormat(product.estimateCost) %}원
                            <div class="font-11 text-muted" v-show="!$.isEmpty(product.estimateConfirmManagerNm)">
                                {% product.estimateConfirmManagerNm %}, {% $.setNumberFormat(product.estimateCount) %}ea이상 발주시
                                <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openFactoryEstimateView(product.projectSno, product.sno, product.estimateConfirmSno, 'estimate')">견적정보</div>
                            </div>
                        </span>
                    </td>
                    <th class="font-15">
                        <span class="text-danger">견적 수량</span>
                    </th>
                    <td class="font-14" >
                        <input type="number" class="form-control font-14 ims-number" placeholder="수량" v-model="product.prdExQty"> <span class="font-15 bold">장</span>
                    </td>
                    <th >타겟 단가</th>
                    <td class="font-14">
                        <input type="number" class="form-control font-14 ims-number" placeholder="타겟 단가" v-model="product.targetPrice"> 원
                    </td>
                    <th >타겟 생산가<br><span class="text-muted" style="font-weight: normal">(타겟 마진)</span></th>
                    <td class="font-14">
                        <div style="width:195px">
                            <input type="number" class="form-control font-14 ims-number" placeholder="타겟생산가" v-model="product.targetPrdCost"> 원
                            <span class="font-13">({% setMargin(product.targetPrice, product.targetPrdCost) %}%)</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="font-16">
                        <span v-if="4 == project.projectType" class="sl-blue">기성 매입가</span>
                        <span v-if="4 != project.projectType" class="sl-blue">생산 확정가</span>
                    </th>
                    <td class="sl-blue font-16 bold">
                        <!--기성복-->
                        <span v-if="4 == project.projectType">
                            <div v-if="'p' !== project.prdCostApproval" class="">
                                <input type="number" class="form-control sl-blue font-15" v-model="product.prdCost" @keyup="()=>{product.estimateCost = product.prdCost}" v-if="">
                            </div>
                            <div v-if="'p' === project.prdCostApproval" class="sl-blue">
                                {% $.setNumberFormat(product.prdCost) %}원
                            </div>
                        </span>

                        <!--기타-->
                        <span v-if="4 != project.projectType">
                            {% $.setNumberFormat(product.prdCost) %}원
                            <div class="font-11 text-muted" v-show="!$.isEmpty(product.prdCostConfirmManagerNm)">
                                {% product.prdCostConfirmManagerNm %} 확정, {% $.setNumberFormat(product.prdCount) %}ea이상 발주시
                                <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openFactoryEstimateView(product.projectSno, product.sno, product.prdCostConfirmSno, 'cost')">확정정보</div>
                            </div>
                        </span>
                    </td>

                    <th class="font-15">
                        <span class="text-danger">판매가격</span>
                    </th>
                    <td class="font-15 bold relative" >
                        <!-- 견적서 발송 기능 완료 시 반드시 견적 후
                        {% $.setNumberFormat(product.salePrice) %}원
                        -->

                        <div v-if="'p' == project.prdPriceApproval" class="text-danger">
                            {% $.setNumberFormat(product.salePrice) %}원
                        </div>
                        <div v-if="'p' != project.prdPriceApproval">
                            <input type="number" class="form-control w150p inline-block font-16" placeholder="판매가격" v-model="product.salePrice"> 원
                        </div>

                        <div class="font-13 text-muted" v-show="product.estimateCost > 0 && 0 >= product.prdCost ">
                            가견적 대비 마진:{% getMargin(product.estimateCost, product.salePrice) %}%
                        </div>
                        <div class="font-13 text-muted" v-show="product.prdCost > 0 ">
                            <span v-if="4 != project.projectType">생산 확정가 대비</span>
                            마진:{% getMargin(product.prdCost, product.salePrice) %}%
                        </div>

                        <!--
                        <ims-accept2
                                :title="'판매가 승인상태'"
                                :field="'priceConfirm'"
                                :condition="product"
                                :before="()=>{save(product)}"
                                :after="()=>{location.reload()}"
                                :memo="'판매가격 : ' + $.setNumberFormat(product.salePrice) + '원 승인 '" >
                        </ims-accept2>
                        -->

                        <div class="">

                            <!--<div class="mgl15 font-16 pd5" style="background-color:#eff7ff; position: absolute; top:0; right:10px">
                                판매가 승인 상태: <b :class="setAcceptClass(product.priceConfirm)" v-html="product.priceConfirmKr"></b>
                            </div>-->

                            <section>
                                <!--<div class="mgt5 " v-if="$.isEmpty(productApprovalInfo.salePrice.sno) || 0 >= productApprovalInfo.salePrice.sno">
                                    <div class="btn btn-accept hover-btn" @click="openApprovalWrite(items.sno, project.sno, 'salePrice', product.sno)">
                                        결재요청
                                    </div>
                                </div>-->

                                <!--<div v-if="productApprovalInfo.salePrice.sno > 0">
                                    <div class="mgt10 pd0 bold font-14">
                                        승인정보
                                    </div>
                                    <div class="mgt5 font-12 "  >
                                        <span @click="openApprovalView(productApprovalInfo.salePrice.sno)" class="cursor-pointer hover-btn">
                                            기안:{% productApprovalInfo.salePrice.regManagerNm %}
                                            <span v-for="target in productApprovalInfo.salePrice.targetManagerList">
                                                <i class="fa fa-chevron-right" aria-hidden="true" ></i> {% target.name %}( {% target.statusKr %} {% $.formatShortDate(target.completeDt) %})
                                            </span>
                                        </span>
                                    </div>
                                </div>-->
                            </section>

                        </div>

                    </td>

                    <th ></th>
                    <td class="font-14"></td>

                    <th >현재 단가</th>
                    <td class="font-14">
                        <input type="number" class="form-control font-14 ims-number" placeholder="현재 단가" v-model="product.currentPrice"> 원
                    </td>

                </tr>
                <tr v-show="false">
                    <th>
                        사이즈
                        <div>
                            <label class="radio-inline" style="padding:0">
                                <input type="radio" name="optionAddType"  value="before" v-model="sizeOptionAddType" />앞
                            </label>
                            <label class="radio-inline" style="padding:0">
                                <input type="radio" name="optionAddType"  value="after"  v-model="sizeOptionAddType" />뒤
                            </label>
                        </div>
                        <div class="btn btn-red btn-sm mgt5" @click="addElement(product.sizeOption, [], sizeOptionAddType)">+추가</div>
                    </th>
                    <td colspan="99" style="padding:0 0 0 15px !important;word-wrap: break-word !important;  " >
                        <div class="">
                            <div class="ims-prd-option-block-area mgt10">
                                <div class="ims-prd-option-block mgt2" v-for="(option, optionIdx) in product.sizeOption">
                                    <input type="text" placeholder="size" class="form-control inline-block full-left" v-model="product.sizeOption[optionIdx]">
                                    <i class="fa fa-trash hover-btn cursor-pointer fa-lg mgl5" aria-hidden="true" style="color:#8a8a8a; padding-top:7px;" @click="deleteElement(product.sizeOption, optionIdx)"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mgt10 mgb10 text-muted">
                            <div class="btn btn-sm btn-white" @click="setStandard('top', product)">상의표준</div>
                            <div class="btn btn-sm btn-white" @click="setStandard('bottom', product)">하의표준</div>
                            <div class="btn btn-sm btn-white" @click="setStandard('bottomKepid', product)">한전하의</div>
                        </div>
                    </td>
                </tr>
                <tr v-show="false">
                    <th>
                        타입(예:기장)
                        <div class="btn btn-red btn-sm mgt5" @click="addElement(product.typeOption, '')" >+추가</div>
                    </th>
                    <td style="padding:0 0 0 15px !important;word-wrap: break-word !important;  " colspan="3">
                        <div class="">
                            <div class="ims-prd-option-block-area mgt10 flo-left">
                                <div class="ims-prd-option-block " v-for="(option, optionIdx) in product.typeOption" style="width:120px!important;">
                                    <input type="text" placeholder="타입" class="form-control inline-block full-left" v-model="product.typeOption[optionIdx]" style="width:100px!important;">
                                    <i class="fa fa-trash hover-btn cursor-pointer fa-lg mgl5" aria-hidden="true" style="color:#8a8a8a; padding-top:7px;" @click="deleteElement(product.typeOption, optionIdx)"></i>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th >
                        메모
                    </th>
                    <td class="" colspan="3" >
                        <textarea class="form-control w100" rows="4" v-model="product.memo" ></textarea>
                    </td>

                    <th class="font-16" v-if="4 != project.projectType">진행상황</th>
                    <td colspan="99" class="pd0"  v-if="4 != project.projectType">
                        <table class="table table-cols w100 h100 border-top-none table-center mg0">
                            <colgroup>
                                <col class="width-sm" />
                                <col class="width-sm" />
                                <col class="width-sm" />
                                <col class="width-sm" />
                            </colgroup>
                            <tr>
                                <th>퀄리티수배</th>
                                <th>BT</th>
                                <th>생산견적</th>
                                <th>작업지시서</th>
                            </tr>
                            <tr>
                                <td class="bottom-bottom-none">
                                    <div v-html="product.fabricStatusIcon"></div>
                                    <!--
                                    <div>
                                        <span class="flag flag-16 flag-kr" v-if="1 & product.fabricNational"></span>
                                        <span class="flag flag-16 flag-cn" v-if="2 & product.fabricNational"></span>
                                        <span class="flag flag-16 flag-market" v-if="4 & product.fabricNational"></span>
                                    </div>
                                    -->
                                </td>
                                <td class="bottom-bottom-none">
                                    <div v-html="product.btStatusIcon"></div>
                                </td>
                                <td class="bottom-bottom-none">
                                    <span v-html="product.prdCostStatusIcon"></span>
                                </td>
                                <td class="bottom-bottom-none">
                                    <span v-html="product.workStatusIcon"></span>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
                </tbody>
            </table>


            <?php if( \SiteLabUtil\SlCommonUtil::isDevId() ){ ?>
                {% product.sizeOption %}
            <?php } ?>

        </div>

    </div>

    <div class="row" v-show="product.sno > 0">
        <div class="col-xs-12 pd-custom">
            <!-- 탭 -->
            <div class="w100">
                <ul class="nav nav-tabs mgb30" role="tablist" style="margin-bottom:10px !important; ">
                    <li role="presentation" :class=" 0 === sampleTabMode ? 'active':''" @click="sampleTabMode = 0">
                        <a href="#tab-status-cancel" data-toggle="tab" >샘플관리</a> <!-- 기본 단계 -->
                    </li>
                    <li role="presentation" :class=" 1 === sampleTabMode ? 'active':'' " @click="sampleTabMode = 1">
                        <a href="#tab-status-cancel" data-toggle="tab" >퀄리티/BT관리</a>
                    </li>
                    <!--<li role="presentation" :class=" 2 === sampleTabMode ? 'active':'' " @click="sampleTabMode = 2">
                        <a href="#tab-status-cancel" data-toggle="tab" >가견적리스트</a>
                    </li>-->
                    <li role="presentation" :class=" 3 === sampleTabMode ? 'active':'' " @click="sampleTabMode = 3">
                        <a href="#tab-status-cancel" data-toggle="tab" >생산견적 리스트</a>
                    </li>
                    <li role="presentation" :class=" 4 === sampleTabMode ? 'active':'' " @click="sampleTabMode = 4">
                        <a href="#tab-status-cancel" data-toggle="tab" >생산관리</a> <!-- 생산 단계 -->
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row" v-show="product.sno > 0">
        <div v-show="0 === sampleTabMode"><?php include 'template/ims_product_sample.php'?></div>
        <div v-show="1 === sampleTabMode"><?php include 'template/ims_product_fabric.php'?></div>
        <!--<div v-show="2 === sampleTabMode"><?php /*include 'template/ims_product_estimate.php'*/?></div>-->
        <div v-show="3 === sampleTabMode"><?php include 'template/ims_product_cost.php'?></div>
        <div v-show="4 === sampleTabMode"><?php include 'template/ims_product_production.php'?></div>
    </div>

    <div class="row"></div>

    <hr>

    <div class="text-center" style="margin-bottom:50px;">
        <div class="btn btn-red btn-lg" @click="save(product)">저장</div>
        <div class="btn btn-white btn-lg" @click="self.close()">닫기</div>
    </div>

    <!--가견적/생산가 요청 창-->
    <div class="modal fade" id="modalEstimateCostReq" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:1350px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    {% product.styleFullName %}의 <span class="sl-blue"></span>
                    <span >생산 견적 요청</span>
                </span>
                </div>
                <div class="modal-body">
                    <section >
                        <div class="table-title gd-help-manual">
                            <div class="flo-left pdt5 pdl5">
                                # 생산견적 요청
                            </div>
                            <div class="flo-right pdt5 pdl5">

                            </div>
                        </div>

                        <div class="">
                            <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                                <colgroup>
                                    <col class="width-md">
                                    <col class="width-xl">
                                    <col class="width-md">
                                    <col class="width-xl">
                                </colgroup>
                                <tbody>
                                <tr >
                                    <th>의뢰처</th>
                                    <td>
                                        <select2 class="js-example-basic-single" style="width:100%" v-model="estimateView.reqFactory">
                                            <option value="0">선택</option>
                                            <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select2>
                                    </td>
                                    <th>견적타입</th>
                                    <td>
                                        <label class="radio-inline font-14" style="padding:0">
                                            <input type="radio" name="optionAddType"  value="estimate"  v-model="estimateView.estimateType" />가견적
                                        </label>
                                        <label class="radio-inline font-14" style="padding:0">
                                            <input type="radio" name="optionAddType"  value="cost" v-model="estimateView.estimateType" />생산확정견적
                                        </label>
                                    </td>
                                </tr>
                                <tr >
                                    <th>수량</th>
                                    <td>
                                        <input type="number" class="form-control h100 w30 font-16" placeholder="수량(숫자만)" v-model="estimateView.estimateCount">
                                    </td>
                                    <th>처리완료D/L</th>
                                    <td>
                                        <date-picker v-model="estimateView.completeDeadLineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>

                                        <span class="pdl30">
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',1)">+1</div>
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',2)">+2</div>
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',3)">+3</div>
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',4)">+4</div>
                                        </span>

                                    </td>
                                </tr>
                                <tr >
                                    <th>요청내용</th>
                                    <td class="pd0" >
                                        <textarea class="form-control w100" rows="3" v-model="estimateView.reqMemo" placeholder="요청내용"></textarea>
                                    </td>
                                    <th>참고파일</th>
                                    <td class="pd0">

                                        <ul class="ims-file-list" >
                                            <li class="hover-btn" v-for="(file, fileIndex) in estimateView.reqFiles">
                                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                            </li>
                                        </ul>

                                        <form id="estimateFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'estimate' === estimateView.estimateType">
                                            <div class="fallback">
                                                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                                            </div>
                                        </form>

                                        <form id="costFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'estimate' !== estimateView.estimateType">
                                            <div class="fallback">
                                                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="99">
                                        <div style="display: flex" class="mgt20">
                                            <span class="notice-info" style="margin-right:10px">기존 견적 정보를 불러올 수 있습니다.</span>
                                            <input type="text" class="form-control w110p font-14" placeholder="기존요청번호" style="height:30px" v-model="loadEstimateCostSno">
                                            <div class="btn btn-gray mgl5" @click="loadBeforeEstimateData()">기존자료 불러오기</div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mgt5">
                            <table class="table table-rows table-default-center">
                                <colgroup>
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                    <col style="width:11%" />
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                    <col style="width:7%" />
                                </colgroup>
                                <tr>
                                    <th >생산가<small class="font-white">(VAT별도)</small></th>
                                    <th >원자재 소계</th>
                                    <th >부자재 소계</th>
                                    <th >공임</th>
                                    <th >마진</th>
                                    <th >물류 및 관세</th>
                                    <th >관리비</th>
                                    <th >생산MOQ</th>
                                    <th >단가MOQ</th>
                                    <th >MOQ미달 추가금</th>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="font-16 text-danger bold">
                                            {% total %}원
                                        </span>
                                    </td>
                                    <td>{% $.setNumberFormat(sampleView.fabricCost) %}원</td>
                                    <td>{% $.setNumberFormat(sampleView.subFabricCost) %}원</td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="공임(숫자만 입력, 원단위)" v-model="sampleView.laborCost"  >
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="마진(숫자만 입력, 원단위)" v-model="sampleView.marginCost" >
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="물류 및 관세(숫자만 입력, 원단위)" v-model="sampleView.dutyCost" >
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="관리비(숫자만 입력, 원단위)" v-model="sampleView.managementCost">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="생산MOQ" v-model="sampleView.prdMoq">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="단가MOQ" v-model="sampleView.priceMoq">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-center" placeholder="MOQ미달 추가금" v-model="sampleView.addPrice">
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div>
                            <!--원단-->
                            <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30 mgb0" style="border-top:none!important">
                                <colgroup>
                                    <col style="width:5%"><!--위치-->
                                    <col style="width:8%"><!--부착위치-->
                                    <col style="width:13%"><!--자재명-->
                                    <col style="width:8%"><!--혼용률-->
                                    <col style="width:8%"><!--컬러-->
                                    <col style="width:6%"><!--규격-->
                                    <col style="width:6%"><!--가요척-->
                                    <col style="width:6%"><!--단가-->
                                    <col style="width:6%"><!--금액-->
                                    <col style="width:10%"><!--샘플원단업체-->
                                    <col style="width:14%"><!--비고-->
                                    <col style="width:9%"><!--기능-->
                                </colgroup>
                                <tr>
                                    <th colspan="99" style="padding:10px 5px 5px 5px!important; text-align: left;  height:1px; ">
                                        <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> 견적 <span class="sl-blue">원단정보</span>
                                        <span class="font-11 normal">가요척과 단가는 반드시 숫자로 입력하세요.( 금액 계산을 위함 )</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>부위</th>
                                    <th>부착위치</th>
                                    <th>자재명</th>
                                    <th>혼용율</th>
                                    <th>컬러</th>
                                    <th>규격</th>
                                    <th>가요척</th>
                                    <th>단가</th>
                                    <th>금액</th>
                                    <th>샘플 원단 구매처</th>
                                    <th>비고</th>
                                    <th>기능</th>
                                </tr><!--원단-->
                                <tr v-for="(fabric, fabricIndex) in sampleView.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                                    <td>
                                        <input type="text" class="form-control text-center" placeholder="위치" v-model="fabric.no" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="부착위치" v-model="fabric.attached" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice" >
                                    </td>
                                    <td>
                                        {% $.setNumberFormat(fabric.price) %}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="원단업체" v-model="fabric.fabricCompany" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" >
                                    </td>
                                    <td class="ta-l">
                                        <div >
                                            <button type="button" class="btn btn-white btn-sm" @click="addElement(sampleView.fabric, sampleView.fabric[0], 'after')">+ 추가</button>
                                            <div class="btn btn-sm btn-red" @click="deleteElement(sampleView.fabric, fabricIndex)" v-show="sampleView.fabric.length > 1">- 삭제</div>
                                            <div class="btn btn-sm btn-red" v-show="1 >= sampleView.fabric.length" disabled="" title="최소 1개 필요">- 삭제</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!--부자재-->
                            <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
                                <colgroup>
                                    <col style="width:5%"><!--위치-->
                                    <col style="width:8%"><!--부착위치-->
                                    <col style="width:13%"><!--자재명-->
                                    <col style="width:8%"><!--혼용률-->
                                    <col style="width:8%"><!--컬러-->
                                    <col style="width:6%"><!--규격-->
                                    <col style="width:6%"><!--가요척-->
                                    <col style="width:6%"><!--단가-->
                                    <col style="width:6%"><!--금액-->
                                    <col style="width:10%"><!--샘플원단업체-->
                                    <col style="width:14%"><!--비고-->
                                    <col style="width:9%"><!--기능-->
                                </colgroup>
                                <tr>
                                    <th colspan="99" style="padding:10px 5px 5px 5px!important; text-align: left;  height:1px; ">
                                        <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> 견적 <span class="sl-blue">부자재</span> 정보
                                        <span class="font-11 normal" v-show="'m' === viewModeSample">가요척과(or수량)과 단가는 반드시 숫자로 입력하세요.( 금액 계산을 위함 )</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>위치</th>
                                    <th colspan="3">자재명</th>
                                    <th>컬러</th>
                                    <th>규격</th>
                                    <th>가요척</th>
                                    <th>단가</th>
                                    <th>금액</th>
                                    <th>부자재업체</th>
                                    <th>비고</th>
                                    <th>기능</th>
                                </tr><!--부자재-->
                                <tr v-for="(fabric, subFabricIndex) in sampleView.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                                    <td>
                                        <input type="text" class="form-control text-center" placeholder="위치" v-model="fabric.no" >
                                    </td>
                                    <td colspan="3">
                                        <input type="text" class="form-control" placeholder="자재명" v-model="fabric.subFabricName" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice" >
                                    </td>
                                    <td>
                                        {% $.setNumberFormat(fabric.price) %}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="구매업체" v-model="fabric.company" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" >
                                    </td>
                                    <td class="ta-l">
                                        <div >
                                            <button type="button" class="btn btn-white btn-sm" @click="addElement(sampleView.subFabric, sampleView.subFabric[0], 'after')">+ 추가</button>
                                            <div class="btn btn-sm btn-red" @click="deleteElement(sampleView.subFabric, subFabricIndex)" v-show="sampleView.subFabric.length > 1">- 삭제</div>
                                            <div class="btn btn-sm btn-red" disabled="" v-show="1 >= sampleView.subFabric.length" title="최소 1개 필요">- 삭제</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </section>
                </div>
                <div class="modal-footer">
                    <div class="btn btn-red" @click="ImsProductService.saveEstimateCostReq(0)">임시저장</div>
                    <div class="btn btn-red" @click="ImsProductService.saveEstimateCostReq(1)">요청</div>
                    <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>


</section>


<?php include 'script/ims_product_script.php'?>
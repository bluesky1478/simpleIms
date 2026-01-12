<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>
    .bootstrap-filestyle input{display: none }
    .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}

    .mx-input {padding:0 12px !important; font-size: 14px !important; }
    .pd-custom { padding:10px 15px 15px 15px !important; }
    .gd-help-manual { font-size:16px !important;}
    .ims-style-attribute-table td{border-bottom: none !important;}

    input[type=number] { padding:4px 6px; }
    .compare_td { display: inline-block; width:60px; text-align: center; }
    .table-fixed-list tr, .table-fixed-list td, .table-fixed-list th { padding:0px!important; }
    .table-fixed-list input { margin:0px auto; }
    .mx-datepicker {width:100px}
</style>

<?php include './admin/ims/library_nk_file_multi_view_modal.php'?>
<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom: 0 !important;">
        <h3>
            <?=$aStyleInfo['productName']?> 기획서 {% oPlanInfo.sno == 0 ? '등록' : (isModify ? '수정' : '상세') %}
            <span class="sl-blue"><?=$aStyleInfo['styleCode']?></span>
        </h3>

        <div class="btn-group">
            <input type="button" value="영업기획서 보기" class="btn btn-white" @click="openSalesView(oPlanInfo.projectSno)" />
            <input type="button" v-show="isModify" @click="save_style_plan()" value="저장" class="btn btn-red" />
            <input type="button" v-show="!isModify" @click="changeUpdateMode(true)" value="수정하기" class="btn btn-red btn-red-line2" />
            <input type="button" v-show="!isModify" @click="ImsProductService.fabricDownload(oPlanInfo.planConcept)" value="원부자재 다운로드" class="btn btn-white btn-icon-excel simple-download w-180px" />
            <input type="button" v-show="isModify && <?=$bFlagShowCancelBtn?>" @click="changeUpdateMode(false)" value="취소" class="btn btn-white" />
            <input type="button" @click="self.close()" value="닫기" class="btn btn-white" />
        </div>

    </div>

    <div class="row">

        <!--TODO : 고객 제안 스케쥴-->




        <!--기본정보-->
        <div class="col-xs-12 pd-custom">
            <div class="table-title gd-help-manual">
                <div class="flo-left pdt5 font-16 w80p">
                    # 기본정보
                    <input type="hidden" v-model="oPlanInfo.sno" />
                </div>
                <div class="flo-right w20p dp-flex dp-flex-right pdb5" style="width:300px !important;" v-show="isModify">
                    <template v-if="Number(oPlanInfo.refStylePlanSno) > 0">
                        <span class="font-12 text-muted" style="white-space: nowrap;">Ref : </span>
                        <div class="input-group input-group-sm mgb0" style="width: 200px;">
                            <input type="text"
                                   class="form-control cursor-pointer hover-btn text-blue bold"
                                   v-model="oPlanInfo.refName"
                                   readonly
                                   @click="openCommonPopup('upsert_style_plan_ref', 1580, 910, {'sno':oPlanInfo.refStylePlanSno})"
                                   title="클릭하여 레퍼런스 상세 보기">

                            <span class="input-group-btn">
                                <button class="btn btn-white" type="button"
                                        @click="oPlanInfo.refStylePlanSno=0; oPlanInfo.refName=''; oPlanInfo.filePlan=''"
                                        title="레퍼런스 해제">
                                    <i class="fa fa-times"></i>
                                </button>
                            </span>
                        </div>
                    </template>

                    <button type="button" class="btn btn-sm btn-black"
                            @click="schListModalServiceNk.popup({title:'레퍼런스 검색',width:1200}, 'stylePlanRef', oPlanInfo, {'refStylePlanSno':'sno', 'refName':'refName', 'filePlan':'refThumbImg'}, {}, 'fnCallback')">
                        <i class="fa fa-search"></i>
                        <span v-if="Number(oPlanInfo.refStylePlanSno) > 0">변경</span>
                        <span v-else>레퍼런스 불러오기</span>
                    </button>

                </div>
            </div>
            <div class="">

                <table class="table table-cols table-pd-5 table-height20" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col class="w-5p">
                        <col class="w-20p">
                        <col class="width-xs">
                        <col class="width-xl">
                        <col class="width-xs">
                        <col class="width-xl">
                        <col class="width-xs">
                        <col class="width-xl">
                    </colgroup>
                    <tr>
                        <th rowspan="6">
                            기획 이미지<br/>
                            <div class="btn btn-sm btn-white" v-show="!$.isEmpty(oPlanInfo.filePlan) && isModify" @click="deleteStylePlanFile('filePlan')">삭제</div>
                        </th>
                        <td rowspan="6" class="ta-c">
                            <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(oPlanInfo.filePlan)" style="display: block; margin:0px auto;" >
                            <img :src="oPlanInfo.filePlan" v-show="!$.isEmpty(oPlanInfo.filePlan)" style="max-height:250px; max-width:315px">
                        </td>
                        <th>디자인 컨셉명<span v-show="isModify" class="text-danger"> *</span></th>
                        <td>
                            <?php $model="oPlanInfo.planConcept"; $placeholder='디자인 컨셉'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                        <th>제품명</th>
                        <td>
                            <?=$aStyleInfo['productName']?>
                            <span class="text-muted"><?=$aStyleInfo['styleCode']?></span>
                        </td>
                        <th>생산처</th>
                        <td>
                            <input type="hidden" v-model="oPlanInfo.prdCustomerName" />
                            <div v-show="isModify">
                                <select2 v-model="oPlanInfo.prdCustomerSno" @change="oPlanInfo.prdCustomerName=event.target.options[event.target.selectedIndex].innerHTML;" style="width:100%;">
                                    <option value="0">미정</option>
                                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                            <div v-show="!isModify" >
                                {% oPlanInfo.prdCustomerName %}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>타겟 판매가</th>
                        <td >
                            <?=number_format($aStyleInfo['targetPrice'])?>원
                        </td>
                        <th>타겟 생산가</th>
                        <td>
                            <?=number_format($aStyleInfo['targetPrdCost'])?>원
                            <span class="text-muted">(권장:<?=number_format($aStyleInfo['targetPrdCost']*0.7)?>원)</span>
                        </td>
                        <th>생산 타입</th>
                        <td>
                            <?php $model="oPlanInfo.produceType"; $placeholder='생산 타입'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>기획 판매가</th>
                        <td >
                            <?php $model="oPlanInfo.targetPrice"; $placeholder='타겟 판매가'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <th>기획 생산가</th>
                        <td>
                            {% $.setNumberFormat(computed_sum_plan_cost) %} <span class="text-muted font-11">자동계산</span>
                        </td>
                        <th>생산 기간</th>
                        <td>
                            <?php $model="oPlanInfo.producePeriod"; $placeholder='생산 기간'; $suffixText='일'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>발주 수량 변동</th>
                        <td>
                            <div v-show="isModify">
                                <?php foreach( \Component\Ims\NkCodeMap::PRODUCT_PLAN_CHANGE_QTY as $k => $v){ ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="radioChangeQty" value="<?=$k?>" v-model="oPlanInfo.changeQty"/><?=$v?>
                                    </label>
                                <?php } ?>
                            </div>
                            <div v-show="!isModify" >
                                {% oPlanInfo.changeQtyHan %}
                            </div>
                        </td>
                        <th>견적 수량</th>
                        <td>
                            <div class="dp-flex dp-flex-gap10">
                                <div class="w-50p">
                                    <?php $model="oPlanInfo.planQty"; $placeholder='견적 수량'; $suffixText='개';?>
                                    <?php include './admin/ims/template/basic_view/_number.php'?>
                                </div>
                                <div class="text-muted font-11">예상수량:<?=number_format($aStyleInfo['prdExQty'])?>장</div>
                            </div>
                        </td>
                        <th>생산 국가</th>
                        <td>
                            <div v-show="isModify">
                                <select2 v-model="oPlanInfo.produceNational" >
                                    <option value="">선택</option>
                                    <?php foreach( \Component\Ims\ImsCodeMap::PRD_NATIONAL_CODE as $key => $val ){ ?>
                                        <option value="<?=$val?>"><?=$val?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                            <div v-show="!isModify">
                                {% oPlanInfo.produceNational %}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>남/여</th>
                        <td colspan="3">
                            <div v-show="isModify">
                                <?php foreach( \Component\Ims\NkCodeMap::PRODUCT_PLAN_GENDER as $k => $v){ ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="radioPrdGender" value="<?=$k?>" v-model="oPlanInfo.prdGender"/><?=$v?>
                                    </label>
                                <?php } ?>
                            </div>
                            <div v-show="!isModify" >
                                {% oPlanInfo.prdGenderHan %}
                            </div>
                        </td>
                        <th>견적 진행 상태</th>
                        <td>
                            <?php $model="oPlanInfo.estimateStatus"; $placeholder='견적 진행 상태'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>메모</th>
                        <td colspan="3">
                            <div v-show="isModify">
                                <textarea class="form-control h100p" rows="4" v-model="oPlanInfo.planMemo" placeholder="메모"></textarea>
                            </div>
                            <div v-show="!isModify" >
                                <div v-if="!$.isEmpty(oPlanInfo.planMemo)" v-html="oPlanInfo.planMemo"></div>
                                <div v-else class="text-muted">
                                    미확인
                                </div>
                            </div>
                        </td>
                        <th>생산처 메모</th>
                        <td>
                            <div v-show="isModify">
                                <textarea class="form-control" rows="4" v-model="oPlanInfo.produceMemo" placeholder="생산처 메모"></textarea>
                            </div>
                            <div v-show="!isModify" >
                                <div v-if="!$.isEmpty(oPlanInfo.produceMemo)" v-html="oPlanInfo.produceMemo"></div>
                                <div v-else class="text-muted">
                                    미확인
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            기획 이미지 업로드
                        </th>
                        <th colspan="3">
                            참고파일
                            <span class="btn btn-sm btn-blue" v-show="referFilePaths.length > 0" @click="revertReferFilePath(); openMultiFileView(referFilePaths);" >보기</span>
                        </th>
                        <th colspan="3">
                            연구 개발용 제안서
                            <span class="btn btn-sm btn-blue" v-show="proposalFilePaths.length > 0" @click="revertReferFilePath(); openMultiFileView(proposalFilePaths);" >보기</span>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div v-show="isModify" class="text-right ims-product-image">
                                <form @submit.prevent="uploadStylePlanFile">
                                    <input :type="'file'" ref="filePlanElement" style="display: block;width:1px!important;" />
                                    <input type="button" class="btn btn-black" value="업로드" @click="uploadStylePlanFile('filePlan')" />
                                </form>
                            </div>
                        </td>
                        <td colspan="3">
                            <file-upload2 :file="oPlanInfo.fileList.stylePlanFile1" :id="'stylePlanFile1'" :params="oPlanInfo" :accept="false"></file-upload2>
                        </td>
                        <td colspan="3">
                            <file-upload2 :file="oPlanInfo.fileList.stylePlanFile2" :id="'stylePlanFile2'" :params="oPlanInfo" :accept="false"></file-upload2>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!--사이즈 스펙-->
        <div class="col-xs-12 pd-custom">
            <table style="width: 100%;">
                <colgroup>
                    <col class="w-20p">
                    <col class="w-20px">
                    <col>
                </colgroup>
                <tr>
                    <td style="vertical-align: top;">
                        <div class="table-title gd-help-manual">
                            <div class="flo-left pdt5 font-16">
                                # 사이즈 스펙
                            </div>
                            <div class="flo-right pdb3">
                                <span class="btn btn-blue-line" v-show="isModify" @click="openCommonPopup('upsert_fit_spec', 640, 910, {'prdStyleGet':'<?=$sPrdStyle?>','prdSeasonGet':'<?=$sPrdSeason?>'});">스펙 불러오기</span>
                                <span class="btn btn-red-line" v-show="isModify" @click="directInputFitSpec()">직접등록</span>
                            </div>
                        </div>
                        <table class="table table-rows table-default-center" style="margin-bottom:0 !important;">
                            <colgroup>
                                <col class="w-30p">
                                <col class="w-70p">
                            </colgroup>
                            <tr>
                                <th>고객사 요청</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>핏</th>
                                <td class="ta-l" style="line-height: 25px;">
                                    {% oPlanInfo.fitName %}
                                </td>
                            </tr>
                            <tr>
                                <th>제작 사이즈</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>고객 샘플 제공</th>
                                <td>
                                <span v-show="isModify">
                                    <label class="radio-inline"><input type="radio" name="customerSampleYn" value="y" v-model="customerSampleYn" />있음</label>
                                    <label class="radio-inline"><input type="radio" name="customerSampleYn" value="n" v-model="customerSampleYn" />없음</label>
                                </span>
                                    <span v-show="!isModify">
                                    {% customerSampleYn=='y'?'있음':'없음' %}
                                </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td></td>
                    <td style="vertical-align: top;">
                        <div class="table-title gd-help-manual">
                            <div class="flo-left pdt5 font-16">
                                # 확정 스펙
                            </div>
                            <div v-show="isModify && customerSampleYn=='y'" class="flo-right pdt5 pdl5 mgb5">
                                <span @click="appendCustomerSampleSize(false)" class="btn btn-red-line">( 기준 앞 ) 고객 제공 사이즈 추가</span>
                                <span @click="appendCustomerSampleSize(true)" class="btn btn-red-line">( 기준 뒤 ) 고객 제공 사이즈 추가</span>
                            </div>
                        </div>
                        <table class="table-fixed-list table table-cols table-pd-3 table-height30 table-default-center" >
                            <tbody>
                            <tr>
                                <th colspan="3">구분</th>
                                <td class="ta-c text-danger">이노버 표준</td>
                                <th v-if="aaoCustomerSample.length > 0 && customerSampleYn == 'y' && bFlagShowCompare === true" :colspan="aaoCustomerSample[0].length" class="ta-c text-blue">
                                    고객 제공 사이즈
                                </th>
                                <td :colspan="isModify ? 3 : 2"></td>
                            </tr>
                            <tr>
                                <th :rowspan="oPlanInfo.jsonFitSpec.length+1" style="width:90px!important;">
                                    측정항목
                                    <button type="button" v-if="isModify" class="btn btn-white btn-sm" @click="addSpecOption(-1)">+ 추가</button>
                                </th>
                                <th style="width:160px!important;">부위</th>
                                <th>편차</th>
                                <th>
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="oPlanInfo.fitSize" @keyup="if (event.key == 'ArrowDown' || event.key == 'Enter') $refs.inputOptionValue[0].focus(); else checkCustomerSampleSize()" @blur="checkCustomerSampleSize()" style="display:inline-block; width:50px;" /><span class="text-danger"> *</span></span>
                                    <span v-show="!isModify">{% oPlanInfo.fitSize %}</span>
                                </th>
                                <th v-if="aaoCustomerSample.length > 0 && customerSampleYn == 'y' && bFlagShowCompare === true" v-for="(val2, key2) in aaoCustomerSample[0]">
                                <span class="compare_td">
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val2.optionSize" style="width:50px;" @keyup="changeCustomerSampleSize(key2)" @blur="changeCustomerSampleSize(key2)" /></span>
                                    <span v-show="!isModify">{% val2.optionSize %}</span>
                                </span>
                                    <span class="compare_td">차이</span>
                                </th>
                                <th>확정 스펙</th>
                                <th>단위</th>
                                <th v-show="isModify">기능</th>
                            </tr>
                            <tr v-for="(val, key) in oPlanInfo.jsonFitSpec" @focusin="sFocusTable='fitSpec'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='fitSpec' && iFocusIdx==key ? 'focused' : ''">
                                <th>
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val.optionName" ref="inputOptionName" @keyup="if (gfnMoveInputBox(oPlanInfo.jsonFitSpec, key, event.key, $refs.inputOptionName) === false) changeFixedOptionName(key)" @blur="changeFixedOptionName(key)" style="width:130px;" /></span>
                                    <span v-show="!isModify">{% val.optionName %}</span>
                                </th>
                                <td>
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val.optionRange" ref="inputOptionRange" @keyup="gfnMoveInputBox(oPlanInfo.jsonFitSpec, key, event.key, $refs.inputOptionRange)" style="width:70px;" /></span>
                                    <span v-show="!isModify">{% val.optionRange %}</span>
                                </td>
                                <td>
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val.optionValue" ref="inputOptionValue" @keyup="gfnMoveInputBox(oPlanInfo.jsonFitSpec, key, event.key, $refs.inputOptionValue)" style="width:80px;" /></span>
                                    <span v-show="!isModify">{% val.optionValue %}</span>
                                </td>
                                <th v-if="aaoCustomerSample.length > 0 && customerSampleYn == 'y'" v-for="(val2, key2) in aaoCustomerSample[key]"> <!-- && val2.optionSize == oPlanInfo.fitSize-->
                                    <span class="compare_td">
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val2.optionValue" :ref="'inputCustomSpec_'+key2" @keyup="gfnMoveInputBox(oPlanInfo.jsonFitSpec, key, event.key, eval('$refs.inputCustomSpec_'+key2))" style="width:50px;" /></span>
                                    <span v-show="!isModify">{% val2.optionValue %}</span>
                                </span>
                                    <span class="compare_td"><span :class="Math.round((Number(val2.optionValue) - Number(val.optionValue)) * 100) / 100 == 0 ? '' : (Math.round((Number(val2.optionValue) - Number(val.optionValue)) * 100) / 100 > 0 ? 'text-danger' : 'text-blue')">{% Math.round((Number(val2.optionValue) - Number(val.optionValue)) * 100) / 100 %}</span></span>
                                </th>
                                <td>
                                <span v-show="isModify">
                                    <input class="form-control" type="hidden" v-model="oPlanInfo.jsonFixedSpec[key].optionName" />
                                    <input class="form-control" type="text" v-model="oPlanInfo.jsonFixedSpec[key].optionValue" ref="inputFixedSpec" @keyup="gfnMoveInputBox(oPlanInfo.jsonFitSpec, key, event.key, $refs.inputFixedSpec)" style="width:80px;" />
                                </span>
                                    <span v-show="!isModify">{% oPlanInfo.jsonFixedSpec[key].optionValue %}</span>
                                </td>
                                <td>
                                <span v-show="isModify">
                                    <select class="form-control" v-model="val.optionUnit" @change="changeFixedOptionUnit(key)" @blur="changeFixedOptionUnit(key)" style="width: 100%;">
                                        <option value="CM">CM</option>
                                        <option value="인치">인치</option>
                                    </select>
                                </span>
                                    <span v-show="!isModify">{% val.optionUnit %}</span>
                                </td>
                                <td v-show="isModify">
                                    <button type="button" class="btn btn-white btn-sm" @click="addSpecOption(-1)">+ 추가</button>
                                    <div class="btn btn-sm btn-red" @click="deleteSpecOption(key)">- 삭제</div> <!-- v-show="oPlanInfo.jsonFitSpec.length > 1"-->
                                    <!--                                <div class="btn btn-sm btn-red" disabled="" v-show="1 >= oPlanInfo.jsonFitSpec.length" title="최소 1개 필요">- 삭제</div>-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <!--확정스펙에서 고객 제공 샘플 내용을 모두 보여주기 때문에 위치 바꾸고 v-if="false"-->
                <tr v-if="false" v-show="customerSampleYn == 'y'">
                    <td style="vertical-align: top;" colspan="3">
                        <div class="table-title gd-help-manual">
                            <div class="flo-left pdt5 font-16">
                                # 고객 제공 샘플
                            </div>
                            <div class="flo-right pdt5 pdl5 mgb5">
                                <span v-show="isModify" @click="appendCustomerSampleSize(false)" class="btn icon_plus btn-blue-line" style="background: url(/admin/gd_share/img/icon_minus_s_on.png) no-repeat 10px 50%; padding-left:25px;">사이즈 추가</span>
                                <span v-show="isModify" @click="appendCustomerSampleSize(true)" class="btn btn-red-line">사이즈 추가</span>
                            </div>
                        </div>
                        <table class="table table-cols table-pd-5 table-default-center" style="width:100%; border-top:none !important;">
                            <tbody>
                            <tr>
                                <th colspan="2">구분</th>
                                <th :colspan="aaoCustomerSample.length > 0 ? aaoCustomerSample[0].length : 1">제공사이즈</th>
                                <th rowspan="2">단위</th>
                            </tr>
                            <tr>
                                <th :rowspan="oPlanInfo.jsonFitSpec.length+1" style="width:120px!important;">측정항목</th>
                                <th style="width:220px!important;">부위</th>
                                <th v-if="aaoCustomerSample.length == 0">없음</th>
                                <th v-else v-for="(val2, key2) in aaoCustomerSample[0]">
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val2.optionSize" style="width:50px;" @keyup="changeCustomerSampleSize(key2)" @blur="changeCustomerSampleSize(key2)" /></span>
                                    <span v-show="!isModify">{% val2.optionSize %}</span>
                                </th>
                            </tr>
                            <tr v-if="aaoCustomerSample.length > 0" v-for="(val, key) in aaoCustomerSample">
                                <th>{% val[0].optionName %}</th>
                                <td v-for="(val2, key2) in val">
                                    <span v-show="isModify"><input class="form-control" type="text" v-model="val2.optionValue" style="width:80px;" /></span>
                                    <span v-show="!isModify">{% val2.optionValue %}</span>
                                </td>
                                <td>{% val[0].optionUnit %}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!--세부 견적-->
        <div class="col-xs-12 pd-custom">
            <div class="table-title gd-help-manual">
                <div class="flo-left pdt5 font-16 dp-flex dp-flex-gap10">
                    # 세부 견적 (VAT별도)
                    <div class="font-15 bold text-green">
                        현재환율 : {% $.setNumberFormat(sCurrDollerRatio) %} ({% sCurrDollerRatioDt %})
                    </div>
                </div>
                <div class="flo-right">
                    <div class="btn btn-white" @click="openInnoverPrice">공임단가</div>
                </div>
            </div>
            <div class="">
                <table class="table table-rows table-default-center" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-80px">
                        <col class="w-100px">
                        <col class="w-110px">
                        <col class="w-110px">
                        <col class="w-110px">
                        <col class="w-110px">
                        <col class="w-120px">
                        <col class="w-110px">
                        <col class="w-110px">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>기획 생산가</th>
                        <th>마진</th>
                        <th>마진율</th>
                        <th>원자재 소계</th>
                        <th>부자재 소계</th>
                        <th>기능 소계</th>
                        <th>마크 소계</th>
                        <th>공임</th>
                        <th>기타비용</th>
                        <th>환율</th>
                        <th :class="String(oPlanInfo.dollerRatioDt).substring(0,5)=='0000-'?'text-danger':''">환율기준일</th>
                        <th>마진(10%)</th>
                        <th>물류/관세</th>
                        <th>운송 형태</th>
                        <th>생산 MOQ</th>
                        <th>단가 MOQ</th>
                        <th>MOQ미달 추가금</th>
                    </tr>
                    <tr>
                        <td>{% $.setNumberFormat(computed_sum_plan_cost) %}</td>
                        <td>{% $.setNumberFormat(Math.round((Number(oPlanInfo.targetPrice) - Number(oPlanInfo.planPrdCost)) * 100 ) / 100) %}</td>
                        <td>{% Number(oPlanInfo.targetPrice)==0?'0':Math.round((Number(oPlanInfo.targetPrice) - oPlanInfo.planPrdCost) / Number(oPlanInfo.targetPrice)*100 * 100) / 100 %} %</td>
                        <td>{% $.setNumberFormat(iSumFabricAmt) %}</td>
                        <td>{% $.setNumberFormat(iSumSubFabricAmt) %}</td>
                        <td>{% $.setNumberFormat(iSumUtilAmt) %}</td>
                        <td>{% $.setNumberFormat(iSumMarkAmt) %}</td>
                        <td>{% $.setNumberFormat(iSumLaborAmt) %}</td>
                        <td>{% $.setNumberFormat(iSumEtcAmt) %}</td>
                        <td>
                            <div v-if="isModify">
                                <input type="text" v-model="oPlanInfo.dollerRatio" @keyup="gfnChangeDollerRatioDt(oPlanInfo)" class="form-control" placeholder="환율">
                            </div>
                            <div v-else>
                                <div v-if="!$.isEmpty(oPlanInfo.dollerRatio)">{% $.setNumberFormat(oPlanInfo.dollerRatio) %}</div>
                                <div v-else class="text-muted">미입력</div>
                            </div>
                        </td>
                        <td>
                            <div v-if="isModify" class="mini-picker">
                                <date-picker v-model="oPlanInfo.dollerRatioDt" value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                            </div>
                            <div v-else>
                                <span :class="String(oPlanInfo.dollerRatioDt).substring(0,5)=='0000-'?'text-danger':''">{% oPlanInfo.dollerRatioDt %}</span>
                            </div>
                        </td>
                        <td>
                            <?php $model="oPlanInfo.marginCost"; $placeholder='마진(10%)'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <td>
                            <?php $model="oPlanInfo.dutyCost"; $placeholder='물류/관세'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <td>
                            <?php $model="oPlanInfo.deliveryType"; $placeholder='운송 형태'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                        <td>
                            <?php $model="oPlanInfo.prdMoq"; $placeholder='생산 MOQ'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <td>
                            <?php $model="oPlanInfo.priceMoq"; $placeholder='단가 MOQ'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <td>
                            <?php $model="oPlanInfo.addPrice"; $placeholder=''; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--원부자재 정보-->
        <div id="myTable" class="pd-custom">
            <?php $sMaterialTargetNm = 'oPlanInfo'; ?>
            <?php include './admin/ims/template/view/materialModule.php'?>
        </div>

    </div>

    <div class="modal-footer">
        <div class="btn btn-red btn-red-line2 btn-lg" v-show="!isModify" @click="changeUpdateMode(true)">수정하기</div>
        <div class="btn btn-red btn-lg" v-show="isModify" @click="save_style_plan()">저장</div>
        <div class="btn btn-white btn-lg" v-show="isModify && <?=$bFlagShowCancelBtn?>" @click="changeUpdateMode(false)">취소</div>
        <div class="btn btn-gray btn-lg" @click="self.close();">닫기</div>
    </div>


    <!-- 우측 하단 플로팅 메뉴 -->
    <div class="ims-fab2" style="bottom:150px;">
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="!isModify" @click="changeUpdateMode(true)">
            수정
        </button>
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="isModify" @click="save_style_plan()">
            저장
        </button>
        <button type="button" class="ims-fab2-btn bg-white font-black" aria-label="취소" v-show="isModify && <?=$bFlagShowCancelBtn?>"  @click="changeUpdateMode(false)">
            취소
        </button>
    </div>

</section>

<?php include './admin/ims/popup/script/ims_pop_upsert_style_plan_script.php'?>

<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .table td { padding:3px 6px!important; }

    .bootstrap-filestyle input{display: none }
    .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class=""><?=$aTextTitle?> {% oMaterialInfo.sno == 0 ? '등록' : (!isModify ? '상세' : '수정') %} <span v-show="isModify" class="font-13 text-danger">* : 필수입력</span></h3>
        <div v-if="oMaterialInfo.sno > 0" class="btn-group font-18 bold">
            <div v-show="!isModify && !isModifyTestReport" @click="clickUpdateBtn();" class="btn btn-red btn-red-line2" style="line-height: 35px;">수정</div>
            <div v-show="isModify" @click="save()" class="btn btn-red" style="line-height: 35px;">저장</div>
            <div v-show="isModify" @click="isModify=false" class="btn btn-white" style="line-height: 35px;">수정취소</div>
            <div v-show="isModifyTestReport" @click="save_test_report()" class="btn btn-red" style="line-height: 35px;">저장</div>
            <div v-show="isModifyTestReport" @click="if (iChooseTestReportSno != 0) {bFlagTestListFold=false;isModifyTestReport=false;} else resetTestReportData();" class="btn btn-white" style="line-height: 35px;">{% iChooseTestReportSno != 0 ? '수정' : '등록' %}취소</div>
            <span @click="openCommonPopup('material_update_log', 740, 910, {'sno':oMaterialInfo.sno});" class="btn btn-white" style="line-height: 35px;">수정이력</span>
            <span @click="self.close()" class="btn btn-white" style="line-height: 35px;">닫기</span>
        </div>
    </div>
    <ul v-show="!isModify" class="nav nav-tabs mgt10" role="tablist">
        <li @click="resetTestReportData(); iTabNum = 1;" role="presentation" :class="iTabNum == 1 ? 'active' : ''">
            <a href="#" data-toggle="tab">기본정보</a>
        </li>
        <li @click="resetTestReportData(); iTabNum = 2;" role="presentation" :class="iTabNum == 2 ? 'active' : ''">
            <a href="#" data-toggle="tab">납품 정보</a>
        </li>
        <li @click="resetTestReportData(); iTabNum = 3;" role="presentation" :class="iTabNum == 3 ? 'active' : ''">
            <a href="#" data-toggle="tab">시험성적서</a>
        </li>
    </ul>

    <div class="mgt10">
        <div>
            <table class="w-100p">
                <colgroup>
                    <col v-show="oMaterialInfo.sno > 0" :class="iTabNum == 3 ? 'w-1p' : 'w-30p'">
                    <col v-show="oMaterialInfo.sno > 0" class="w-20px">
                    <col>
                </colgroup>
                <tr style="vertical-align: top;">
                    <td v-show="oMaterialInfo.sno > 0 && iTabNum != 3">
                        <div class="table-title gd-help-manual"><div class="font-18">이미지</div></div>
                        <div class="ta-c" style="min-height:250px; border:1px #000 solid;">
                            <img v-show="oMaterialInfoHan.imgMaterial != ''" :src="oMaterialInfoHan.imgMaterial" class="w-100p" />
                        </div>
                        <div class="mgt5">
                            <div v-show="!isModify" class="text-right ims-product-image">
                                <form @submit.prevent="uploadImageFile">
                                    <input :type="'file'" ref="imgMaterialElement" style="display: block;width:1px!important;" />
                                    <input type="button" class="btn btn-black" value="업로드" @click="uploadImageFile('imgMaterial')" />
                                </form>
                            </div>
                        </div>
                        <div class="table-title gd-help-manual mgt20"><div class="font-18">스와치 사진</div></div>
                        <div class="ta-c" style="min-height:250px; border:1px #000 solid;">
                            <img v-show="oMaterialInfoHan.imgSwatch != ''" :src="oMaterialInfoHan.imgSwatch" class="w-100p" />
                        </div>
                        <div class="mgt5">
                            <div v-show="!isModify" class="text-right ims-product-image">
                                <form @submit.prevent="uploadImageFile">
                                    <input :type="'file'" ref="imgSwatchElement" style="display: block;width:1px!important;" />
                                    <input type="button" class="btn btn-black" value="업로드" @click="uploadImageFile('imgSwatch')" />
                                </form>
                            </div>
                        </div>
                    </td>
                    <td v-show="oMaterialInfo.sno > 0"></td>
                    <td v-show="iTabNum == 1">
                        <div class="table-title gd-help-manual"><div class="font-18">기본 정보</div></div>
                        <table class="w-100p">
                            <colgroup>
                                <col class="w-50p">
                                <col class="w-20px">
                                <col>
                            </colgroup>
                            <tr style="vertical-align: top;">
                                <td>
                                    <table class="table table-cols table-pd-3 table-th-height30 table-td-height30">
                                        <colgroup>
                                            <col class="w-30p">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <?php foreach ($aUpsertForm as $key => $val) { if ($key <= 14) { ?>
                                            <?php if ($val['flag_display'] === true && $val['input_type'] != '')  { ?>
                                                <tr v-show="<?php if (isset($val['is_modify']) && $val['is_modify'] === true) echo 'false || isModify'; else echo 'true'; ?>">
                                                    <th class="ta-c"><?=$val['fld_text']?> <?php if (isset($val['flag_required']) && $val['flag_required'] === true) { ?><span v-show="isModify" class="font-13 text-danger">*</span><?php } ?></th>
                                                    <td>
                                                        <?php switch($val['input_type']) {
                                                            case 'text' : ?>
                                                                <?php $model='oMaterialInfo.'.$val['db_fld']; $placeholder=$val['fld_text']; ?>
                                                                <span v-show="isModify">
                                                                    <input type="text" <?=isset($val['append_str_nm'])?'style="display:inline; width:20%;"':''?> class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>">
                                                                </span>
                                                                <span v-show="!isModify">{% <?=$model?> %}</span>
                                                                <?=isset($val['append_str_nm'])?'{% vue_val_'.$val['append_str_nm'].' %}':''?>
                                                                <?php break;
                                                            case 'select' : ?>
                                                                <select2 v-model="oMaterialInfo.<?=$val['db_fld']?>" style="width:100%;">
                                                                    <?php foreach($val['options'] as $k => $v){ ?>
                                                                        <option value="<?=$k?>"><?=$v?></option>
                                                                    <?php } ?>
                                                                </select2>
                                                                <?php break;
                                                            case 'radio' : ?>
                                                                <span v-show="isModify">
                                                                    <?php $model = 'oMaterialInfo.'.$val['db_fld']; $sRadioNm = $val['db_fld']; ?>
                                                                    <?php foreach ($val['options'] as $key2 => $val2) { ?>
                                                                        <label class="radio-inline" for="<?=$sRadioNm?>_<?=$key2?>">
                                                                            <input type="<?=$val['input_type']?>" name="<?=$sRadioNm?>" id="<?=$sRadioNm?>_<?=$key2?>"  value="<?=$key2?>" v-model="<?=$model?>" style="margin:0!important;" />
                                                                            <span class="font-12"><?=$val2?></span>
                                                                        </label>
                                                                    <?php } ?>
                                                                </span>
                                                                <span v-show="!isModify">{% <?='oMaterialInfoHan.'.$val['db_fld']?> %}</span>
                                                                <?php break;
                                                            case 'checkbox' : ?>
                                                                <span v-show="isModify">
                                                                    <?php $model = 'oMaterialInfo.'.$val['db_fld']; $sChkboxNm = $val['db_fld']; ?>
                                                                    <?php foreach ($val['options'] as $key2 => $val2) { ?>
                                                                    <label class="mgr10">
                                                                        <input class="checkbox-inline chk-progress" type="checkbox" name="<?=$sChkboxNm?>[]" value="<?=$key2?>"  v-model="<?=$model?>"> <?=$val2?>
                                                                    </label>
                                                                    <?php } ?>
                                                                </span>
                                                                <span v-show="!isModify">{% <?='oMaterialInfoHan.'.$val['db_fld']?> %}</span>
                                                                <?php break;
                                                            case 'text_buyer_info' : ?>
                                                                <span v-show="isModify">
                                                                    <input type="text" v-model="<?='oMaterialInfo.'.$val['db_fld']?>" :disabled="isDisabledFactoryName" placeholder="매입처명" class="form-control" />
                                                                    <input v-show="oMaterialInfo.buyerSno == -1" type="text" v-model="oMaterialInfo.factoryPhone" :disabled="isDisabledFactoryName" placeholder="연락처" class="form-control" />
                                                                    <input v-show="oMaterialInfo.buyerSno == -1" type="text" v-model="oMaterialInfo.factoryAddress" :disabled="isDisabledFactoryName" placeholder="주소" class="form-control" />
                                                                </span>
                                                                <span v-show="!isModify">
                                                                    <div v-if="!$.isEmpty(<?='oMaterialInfo.'.$val['db_fld']?>)">
                                                                        {% <?='oMaterialInfo.'.$val['db_fld']?> %}
                                                                    </div>
                                                                    <div v-else class="text-muted">
                                                                        미선택
                                                                    </div>
                                                                </span>
                                                                <?php break;
                                                            default :
                                                                ?>
                                                                <?php $model='oMaterialInfo.'.$val['db_fld']; $placeholder=$val['fld_text']; $disabledKey='isDisabled'.ucfirst($val['db_fld']);$textareaRows=3; ?>
                                                                <?php include './admin/ims/template/basic_view/'.$val['input_type'].'.php'?>
                                                                <?php
                                                                break;
                                                        } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php }} ?>
                                        <tr v-show="!isModify">
                                            <th class="ta-c">원단 시험성적서</th>
                                            <td><span @click="iTabNum = 3;" class="btn btn-white btn-sm">보기</span> (전체평균값 : {% fTotalAvgByMaterial %})</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                                <td>
                                    <table class="table table-cols table-pd-3 table-th-height30 table-td-height30">
                                        <colgroup>
                                            <col class="w-35p">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <?php foreach ($aUpsertForm as $key => $val) { if ($key > 14 && $key <= 27) { ?>
                                            <?php if ($val['flag_display'] === true && $val['input_type'] != '')  { ?>
                                                <tr>
                                                    <th class="ta-c"><?=$val['fld_text']?> <?php if (isset($val['flag_required']) && $val['flag_required'] === true) { ?><span v-show="isModify" class="font-13 text-danger">*</span><?php } ?></th>
                                                    <td>
                                                        <?php switch($val['input_type']) {
                                                            case 'text' : ?>
                                                                <?php $model='oMaterialInfo.'.$val['db_fld']; $placeholder=$val['fld_text']; ?>
                                                                <span v-show="isModify">
                                                                    <input type="text" <?=isset($val['append_str_nm'])?'style="display:inline; width:20%;"':''?> class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>">
                                                                </span>
                                                                <span v-show="!isModify">{% <?=$model?> %}</span>
                                                                <?=isset($val['append_str_nm'])?'{% vue_val_'.$val['append_str_nm'].' %}':''?>
                                                                <?php break;
                                                            case 'select' : ?>
                                                                <select2 v-model="oMaterialInfo.<?=$val['db_fld']?>" style="width:100%;">
                                                                    <?php foreach($val['options'] as $k => $v){ ?>
                                                                        <option value="<?=$k?>"><?=$v?></option>
                                                                    <?php } ?>
                                                                </select2>
                                                                <?php break;
                                                            case 'radio' : ?>
                                                                <span v-show="isModify">
                                                                    <?php $model = 'oMaterialInfo.'.$val['db_fld']; $sRadioNm = $val['db_fld']; ?>
                                                                    <?php foreach ($val['options'] as $key2 => $val2) { ?>
                                                                        <label class="radio-inline" for="<?=$sRadioNm?>_<?=$key2?>">
                                                                            <input type="<?=$val['input_type']?>" name="<?=$sRadioNm?>" id="<?=$sRadioNm?>_<?=$key2?>"  value="<?=$key2?>" v-model="<?=$model?>" style="margin:0!important;" />
                                                                            <span class="font-12"><?=$val2?></span>
                                                                        </label>
                                                                    <?php } ?>
                                                                </span>
                                                                <span v-show="!isModify">{% <?='oMaterialInfoHan.'.$val['db_fld']?> %}</span>
                                                                <?php break;
                                                            case 'checkbox' : ?>
                                                                <span v-show="isModify">
                                                                    <?php $model = 'oMaterialInfo.'.$val['db_fld']; $sChkboxNm = $val['db_fld']; ?>
                                                                    <?php foreach ($val['options'] as $key2 => $val2) { ?>
                                                                        <label class="mgr10">
                                                                            <input class="checkbox-inline chk-progress" type="checkbox" name="<?=$sChkboxNm?>[]" value="<?=$key2?>"  v-model="<?=$model?>"> <?=$val2?>
                                                                        </label>
                                                                    <?php } ?>
                                                                </span>
                                                                <span v-show="!isModify">{% <?='oMaterialInfoHan.'.$val['db_fld']?> %}</span>
                                                                <?php break;
                                                            default :
                                                                ?>
                                                                <?php $model='oMaterialInfo.'.$val['db_fld']; $placeholder=$val['fld_text']; $disabledKey='isDisabled'.ucfirst($val['db_fld']);$textareaRows=3; ?>
                                                                <?php include './admin/ims/template/basic_view/'.$val['input_type'].'.php'?>
                                                                <?php
                                                                break;
                                                        } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php }} ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr v-show="!isModify">
                                <td colspan="3">
                                    <div class="table-title gd-help-manual"><div class="font-18">유사 원단 정보</div></div>
                                    <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                                        <colgroup>
                                            <col class="w-40px" />
                                            <col class="" />
                                            <col class="w-25p" />
                                            <col class="w-12p" />
                                            <col class="w-10p" />
                                            <col class="w-10p" />
                                            <col class="w-10p" />
                                            <col class="w-10p" />
                                            <col class="w-50px" />
                                        </colgroup>
                                        <tr>
                                            <th>번호</th>
                                            <th>업체명</th>
                                            <th>주소</th>
                                            <th>번호</th>
                                            <th>스와치명</th>
                                            <th>폭</th>
                                            <th>혼용율</th>
                                            <th>단가</th>
                                            <th>스와치 사진</th>
                                        </tr>
                                        <tbody>
                                        <tr v-if="aoGrpMateList.length == 0">
                                            <td colspan="99">같은 유사퀄리티를 가진 자재가 없습니다.</td>
                                        </tr>
                                        <tr v-for="(val, key) in aoGrpMateList">
                                            <td>{% key + 1 %}</td>
                                            <td>{% val.customerName %}</td>
                                            <td>{% val.customerAddr %}</td>
                                            <td>{% val.customerPhone %}</td>
                                            <td>{% val.name %}</td>
                                            <td>{% val.spec %}</td>
                                            <td>{% val.mixRatio %}</td>
                                            <td>{% val.unitPrice %}</td>
                                            <td><span @click="if (val.imgSwatch == '' || val.imgSwatch == null) { $.msg('스와치사진이 없습니다','','warning'); } else { $refs.imageSwatch.src=val.imgSwatch; $('#modalImageSwatch').modal('show'); }" class="btn btn-white btn-sm">보기</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="table-title gd-help-manual"><div class="font-18">추가 정보</div></div>
                                    <table class="table table-cols table-pd-3 table-th-height30 table-td-height30">
                                        <colgroup>
                                            <col class="w-15p">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <?php foreach ($aUpsertForm as $key => $val) { if ($key > 27) { ?>
                                            <?php if ($val['flag_display'] === true && $val['input_type'] != '')  { ?>
                                                <tr>
                                                    <th class="ta-c"><?=$val['fld_text']?> <?php if (isset($val['flag_required']) && $val['flag_required'] === true) { ?><span v-show="isModify" class="font-13 text-danger">*</span><?php } ?></th>
                                                    <td>
                                                        <?php switch($val['input_type']) {
                                                            case 'text' : ?>
                                                                <?php $model='oMaterialInfo.'.$val['db_fld']; $placeholder=$val['fld_text']; ?>
                                                                <span v-show="isModify">
                                                                    <input type="text" <?=isset($val['append_str_nm'])?'style="display:inline; width:20%;"':''?> class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>">
                                                                </span>
                                                                <span v-show="!isModify">{% <?=$model?> %}</span>
                                                                <?=isset($val['append_str_nm'])?'{% vue_val_'.$val['append_str_nm'].' %}':''?>
                                                                <?php break;
                                                            case 'select' : ?>
                                                                <select2 v-model="oMaterialInfo.<?=$val['db_fld']?>" style="width:100%;">
                                                                    <?php foreach($val['options'] as $k => $v){ ?>
                                                                        <option value="<?=$k?>"><?=$v?></option>
                                                                    <?php } ?>
                                                                </select2>
                                                                <?php break;
                                                            case 'radio' : ?>
                                                                <span v-show="isModify">
                                                                    <?php $model = 'oMaterialInfo.'.$val['db_fld']; $sRadioNm = $val['db_fld']; ?>
                                                                    <?php foreach ($val['options'] as $key2 => $val2) { ?>
                                                                        <label class="radio-inline" for="<?=$sRadioNm?>_<?=$key2?>">
                                                                            <input type="<?=$val['input_type']?>" name="<?=$sRadioNm?>" id="<?=$sRadioNm?>_<?=$key2?>"  value="<?=$key2?>" v-model="<?=$model?>" style="margin:0!important;" />
                                                                            <span class="font-12"><?=$val2?></span>
                                                                        </label>
                                                                    <?php } ?>
                                                                </span>
                                                                <span v-show="!isModify">{% <?='oMaterialInfoHan.'.$val['db_fld']?> %}</span>
                                                                <?php break;
                                                            case 'checkbox' : ?>
                                                                <span v-show="isModify">
                                                                    <?php $model = 'oMaterialInfo.'.$val['db_fld']; $sChkboxNm = $val['db_fld']; ?>
                                                                    <?php foreach ($val['options'] as $key2 => $val2) { ?>
                                                                        <label class="mgr10">
                                                                            <input class="checkbox-inline chk-progress" type="checkbox" name="<?=$sChkboxNm?>[]" value="<?=$key2?>"  v-model="<?=$model?>"> <?=$val2?>
                                                                        </label>
                                                                    <?php } ?>
                                                                </span>
                                                                <span v-show="!isModify">{% <?='oMaterialInfoHan.'.$val['db_fld']?> %}</span>
                                                                <?php break;
                                                            default :
                                                                ?>
                                                                <?php $model='oMaterialInfo.'.$val['db_fld']; $placeholder=$val['fld_text']; $disabledKey='isDisabled'.ucfirst($val['db_fld']);$textareaRows=3; ?>
                                                                <?php include './admin/ims/template/basic_view/'.$val['input_type'].'.php'?>
                                                                <?php
                                                                break;
                                                        } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php }} ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td v-show="iTabNum == 2">
                        <div class="table-title gd-help-manual"><div class="font-18">납품 정보</div></div>
                        <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                            <colgroup>
                                <col class="w-20p" />
                                <col class="" />
                                <col class="w-20p" />
                                <col class="w-20p" />
                            </colgroup>
                            <tr>
                                <th>번호</th>
                                <th>자재명</th>
                                <th>타입</th>
                                <th>품목 구분</th>
                            </tr>
                            <tbody>
                            <tr v-if="aoGrpMateList.length == 0">
                                <td colspan="99">같은 유사퀄리티를 가진 자재가 없습니다.</td>
                            </tr>
                            <tr v-for="(val, key) in aoGrpMateList">
                                <td>{% key + 1 %}</td>
                                <td>{% val.materialTypeHan %}</td>
                                <td>{% val.materialTypeText %}</td>
                                <td>{% val.name %}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td v-show="iTabNum == 3">
                        <table class="w-100p">
                            <colgroup>
                                <col v-if="!bFlagTestListFold" class="w-35p" />
                                <col class="w-10px" />
                                <col />
                            </colgroup>
                            <tr>
                                <td v-if="!bFlagTestListFold" style="vertical-align: top;">
                                    <div class="table-title gd-help-manual">
                                        <span class="font-18">시험성적서</span>
                                        <span v-if="igCustomerSno > 0 && sgMaterialColor != ''" @click="iChooseTestType = 1; openRegistTestReport()" class="btn btn-white">시험성적서 등록</span>
                                        <span v-else>(작업지시서 설정페이지에서 시험성적서 등록가능)</span>
                                    </div>
                                    <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                                        <colgroup>
                                            <col class="w-150px" />
                                            <col class="w-50px" />
                                            <col />
                                            <col class="w-70px" />
                                            <col class="w-50px" />
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>고객사</th>
                                            <th >컬러</th>
                                            <th >사용 스타일 / 위치</th>
                                            <th >작성일자</th>
                                            <th>성적서</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="aoTestReportList.length == 0">
                                            <td colspan="99">등록된 시험성적서가 없습니다.</td>
                                        </tr>
                                        <tr v-else v-for="(val, key) in aoTestReportList" :class="iChooseTestReportSno == val.sno ? 'focused' : ''">
                                            <td>{% val.customerName %}</td>
                                            <td >{% val.materialColor %}</td>
                                            <td  v-html="val.styleInfo"></td>
                                            <td >{% $.formatShortDateWithoutWeek(val.regDt) %}</td>
                                            <td><span @click="viewTestDetail(1, val);" class="btn btn-white btn-sm">보기</span></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="table-title gd-help-manual mt-50">
                                        <span class="font-18">자체테스트</span>
                                        <span v-if="igCustomerSno > 0 && sgMaterialColor != ''" @click="iChooseTestType = 2; openRegistTestReport()" class="btn btn-white">자체테스트 등록</span>
                                        <span v-else>(작업지시서 설정페이지에서 자체테스트 등록가능)</span>
                                    </div>
                                    <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                                        <colgroup>
                                            <col class="w-150px"/>
                                            <col  class="w-50px" />
                                            <col  />
                                            <col  class="w-70px" />
                                            <col class="w-50px" />
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>고객사</th>
                                            <th >컬러</th>
                                            <th >사용 스타일 / 위치</th>
                                            <th >작성일자</th>
                                            <th>테스트</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="aoTestSelfList.length == 0">
                                            <td colspan="99">등록된 자체테스트가 없습니다.</td>
                                        </tr>
                                        <tr v-else v-for="(val, key) in aoTestSelfList" :class="iChooseTestReportSno == val.sno ? 'focused' : ''">
                                            <td>{% val.customerName %}</td>
                                            <td >{% val.materialColor %}</td>
                                            <td  v-html="val.styleInfo"></td>
                                            <td >{% $.formatShortDateWithoutWeek(val.regDt) %}</td>
                                            <td><span @click="viewTestDetail(2, val);" class="btn btn-white btn-sm">보기</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td></td>
                                <td style="vertical-align: top;">
                                    <div class="table-title gd-help-manual"><div class="font-18">{% computed_change_detail_title %}</div></div>
                                    <table v-if="aoTestReportDetail.length == 0" class="table table-rows table-default-center table-td-height30 w-100p">
                                        <colgroup>
                                            <col v-for="(val, key) in ooAvgTestReport" />
                                        </colgroup>
                                        <tr>
                                            <th v-for="(val, key) in ooAvgTestReport" colspan="2">{% key %}</th>
                                        </tr>
                                        <tr style="background: #F6F6F6;">
                                            <template v-for="(val, key) in ooAvgTestReport">
                                                <td>평균</td>
                                                <td>{% Math.round(val.iSumVal / val.aAvgVal.length * 100) / 100 %}</td>
                                            </template>
                                        </tr>
                                        <tr v-for="key in iStaticAvgTestReportMaxRow">
                                            <template v-for="(val2, key2) in ooAvgTestReport">
                                                <td>{% val2.aGrpList[key-1] == undefined ? '' : val2.aGrpList[key-1].sGrpName %}</td>
                                                <td>{% val2.aGrpList[key-1] == undefined ? '' : Math.round(val2.aGrpList[key-1].iSumVal / val2.aGrpList[key-1].aAvgVal.length * 100) / 100 %}</td>
                                            </template>
                                        </tr>
                                    </table>
                                    <table v-if="iChooseTestType == 1 && aoTestReportDetail.length > 0" class="table table-rows">
                                        <colgroup>
                                            <col class="w-30px" />
                                            <col />
                                        </colgroup>
                                        <tr>
                                            <th class="ta-c">시<br/>험<br/>항<br/>목</th>
                                            <td style="padding:0px!important;">
                                                <ul class="nav nav-tabs" role="tablist" style="margin:0px!important;">
                                                    <li v-for="(val, key) in aoTestReportDetail" @click="iChkTabTestReportDetail = key;" role="presentation" :class="iChkTabTestReportDetail == key ? 'active' : ''">
                                                        <a href="#" data-toggle="tab">
                                                            <div v-if="isModifyTestReport">
                                                                <input type="text" v-model="val.grpName" class="form-control w-100px" />
                                                                <select v-model="val.sumAvgYn" class="form-control w-100px">
                                                                    <option value="y">평균계산</option>
                                                                    <option value="n">평균계산제외</option>
                                                                </select>
                                                                <span @click="deleteTestReportElement(1, aoTestReportDetail, key);" class="btn btn-red">삭제</span>
                                                            </div>
                                                            <div v-else>{% val.grpName %}</div>
                                                        </a>
                                                    </li>
                                                    <li v-if="isModifyTestReport" class="">
                                                        <a @click="addTestGrp(1)" href="#" style="line-height:74px;">+ 추가</a>
                                                    </li>
                                                </ul>
                                                <div v-show="iChkTabTestReportDetail == key" v-for="(val, key) in aoTestReportDetail">
                                                    <span v-for="(val2, key2) in val.childGrp" :style="'display: inline-block; vertical-align:top; width:calc('+(100/val.childGrp.length)+'%'+(isModifyTestReport==true?' - '+(50/val.childGrp.length)+'px':'')+');'">
                                                        <table class="table table-rows table-default-center table-td-height30 w-100p" :style="'margin:0px 0px 0px -'+(key2+1)+'px;'">
                                                            <colgroup>
                                                                <col />
                                                                <col class="w-60px" />
                                                            </colgroup>
                                                            <tr style="border-left:1px #E6E6E6 solid; border-right:1px #E6E6E6 solid;">
                                                                <th colspan="2">
                                                                    <div v-if="isModifyTestReport">
                                                                        <input type="text" v-model="val2.grpName" class="form-control" placeholder="그룹명" />
                                                                        <input type="text" v-model="val2.standardVal" class="form-control" placeholder="기준값" />
                                                                        <span @click="deleteTestReportElement(2, val.childGrp, key2);" class="btn btn-red">삭제</span>
                                                                    </div>
                                                                    <div v-else>
                                                                        {% val2.grpName %}
                                                                        <span v-if="val2.standardVal!='' && val2.standardVal!='0'"> (기준 : {% val2.standardVal %})</span>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                            <tr style="border-left:1px #E6E6E6 solid; border-right:1px #E6E6E6 solid; background: #F6F6F6;">
                                                                <td class="ta-l">평균</td>
                                                                <td>{% val2.avgVal %}</td>
                                                            </tr>
                                                            <tr v-for="(val3, key3) in val2.childRow" style="border-left:1px #E6E6E6 solid; border-right:1px #E6E6E6 solid;">
                                                                <td class="ta-l">
                                                                    <div v-if="isModifyTestReport">
                                                                        <input type="text" v-model="val3.rowName" class="form-control" placeholder="항목명" />
                                                                        <span @click="deleteTestReportElement(3, val2.childRow, key3);" class="btn btn-red">삭제</span>
                                                                    </div>
                                                                    <div v-else>{% val3.rowName %}</div>
                                                                </td>
                                                                <td>
                                                                    <div v-if="isModifyTestReport"><input type="text" v-model="val3.rowVal" @keyup="calcTestReportAvg(key, key2, key3);" class="form-control" placeholder="점수" /></div>
                                                                    <div v-else><span :class="Number(val2.standardVal)>Number(val3.rowValNumber)?'text-danger':''">{% val3.rowVal %}</span></div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="isModifyTestReport" style="border-left:1px #E6E6E6 solid; border-right:1px #E6E6E6 solid;">
                                                                <td colspan="2">
                                                                    <span @click="addElement(val2.childRow, {rowName:'',rowVal:'',rowValNumber:0}, 'after')" class="btn btn-white btn-sm">+ 추가</span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </span>
                                                    <span v-if="isModifyTestReport" :style="'width:40px; display: inline-block; margin-left:-'+(4+val.childGrp.length)+'px;'">
                                                        <table class="table table-rows table-default-center" style="margin:0px;">
                                                            <colgroup>
                                                                <col class="" />
                                                            </colgroup>
                                                            <tr>
                                                                <th style="height:85px; border-left:1px #AEAEAE solid;">
                                                                    <span @click="addTestGrp(2, key);" class="btn btn-white btn-sm">+ 추가</span>
                                                                </th>
                                                            </tr>
                                                        </table>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <table v-if="iChooseTestType == 2 && aoTestReportDetail.length > 0" class="table table-rows">
                                        <colgroup>
                                            <col class="w-20p" />
                                            <col />
                                        </colgroup>
                                        <tr>
                                            <th>원단명</th>
                                            <td>{% oMaterialInfo.name %}</td>
                                        </tr>
                                        <tr>
                                            <th>색상</th>
                                            <td>{% iChooseTestReportSno == 0 ? sgMaterialColor : oChooseTestSelf.materialColor %}</td>
                                        </tr>
                                        <tr>
                                            <th>고객사</th>
                                            <td>{% iChooseTestReportSno == 0 ? sgCustomerName : oChooseTestSelf.customerName %}</td>
                                        </tr>
                                        <tr>
                                            <th>테스트 진행 장소</th>
                                            <td>
                                                <div v-if="isModifyTestReport">
                                                    <input type="text" v-model="aoTestReportDetail[0].testPlace" class="form-control" placeholder="테스트 진행 장소" />
                                                </div>
                                                <div v-else>{% aoTestReportDetail[0].testPlace %}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>테스트 진행 일자</th>
                                            <td>
                                                <div v-if="isModifyTestReport">
                                                    <date-picker v-model="aoTestReportDetail[0].testDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
                                                </div>
                                                <div v-else>{% $.isEmpty(aoTestReportDetail[0].testDt) ? '미정' : $.formatShortDate(aoTestReportDetail[0].testDt) %} <span class="font-11 " v-html="$.remainDate(aoTestReportDetail[0].testDt,true)"></span></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>손세탁 방식</th>
                                            <td>
                                                <div v-if="isModifyTestReport">
                                                    <input type="text" v-model="aoTestReportDetail[0].handWashMethod" class="form-control" placeholder="손세탁 방식" />
                                                </div>
                                                <div v-else>{% aoTestReportDetail[0].handWashMethod %}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>테스트 사진</th>
                                            <td>
                                                <div v-if="isModifyTestReport">보기 상태에서 파일 업로드 가능</div>
                                                <div v-else>
                                                    <file-upload2 :file="oTestSelfFile" :id="'materialTestSelf'" :params="{'customerSno':igCustomerSno,'projectSno':0,'styleSno':0,'eachSno':iChooseTestReportSno}" :accept="false"></file-upload2>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>코멘트 <span @click="addTestSelfComment()" class="btn btn-white">등록</span></th>
                                            <td>
                                                <table class="table table-rows">
                                                    <colgroup>
                                                        <col class="w-100px" />
                                                        <col class="w-100px" />
                                                        <col class="" />
                                                        <col v-if="isModifyTestReport" class="w-50px" />
                                                    </colgroup>
                                                    <thead>
                                                    <tr>
                                                        <th>등록일</th><th>등록자</th><th>내용</th><th v-if="isModifyTestReport">삭제</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <template v-for="(val, key) in aoTestReportDetail[0].testComment">
                                                    <tr v-if="val.commentUserSno != ''" >
                                                        <td class="ta-c">{% $.formatShortDateWithoutWeek(val.commentDt) %}</td>
                                                        <td class="ta-c">{% val.commentName %}</td>
                                                        <td>
                                                            <div v-if="isModifyTestReport && val.commentUserSno == istaticManagerSno">
                                                                <input type="text" v-model="val.commentDesc" class="form-control" placeholder="코멘트 내용" />
                                                            </div>
                                                            <div v-else>{% val.commentDesc %}</div>
                                                        </td>
                                                        <td v-if="isModifyTestReport"><span @click="deleteElement(aoTestReportDetail[0].testComment, key)" class="btn btn-red">삭제</span></td>
                                                    </tr>
                                                    </template>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dp-flex" style="justify-content: center">
            <div v-show="!isModify && !isModifyTestReport" @click="clickUpdateBtn();" class="btn btn-red btn-lg btn-red-line2 mg5" >수정</div>
            <div v-show="isModify" @click="save()" class="btn btn-red btn-lg mg5"><?=$aTextMode?></div>
            <div v-show="isModify && oMaterialInfo.sno != 0" @click="isModify=false" class="btn btn-white btn-lg mg5" >수정취소</div>
            <div v-show="isModifyTestReport" @click="save_test_report()" class="btn btn-red btn-lg mg5" >저장</div>
            <div v-show="isModifyTestReport" @click="if (iChooseTestReportSno != 0) {bFlagTestListFold=false;isModifyTestReport=false;} else resetTestReportData();" class="btn btn-white btn-lg mg5" >{% iChooseTestReportSno != 0 ? '수정' : '등록' %}취소</div>
            <div @click="self.close()" class="btn btn-white btn-lg mg5">닫기</div>
        </div>
    </div>

    <div class="modal fade" id="modalImageSwatch" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:1000px; top:0px;">
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >스와치 사진</span>
                </div>
                <div class="modal-body ta-c">
                    <img ref="imageSwatch" src="" style="max-width:100%;" />
                </div>
                <div class="modal-footer ">
                    <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>

</section>

<script type="text/javascript">
    $(()=>{
        const serviceData = {
            serviceWatch: {
                'oMaterialInfo.makeNational'(val, pre) { //생산국 변경시 통화단위 변경
                    vueApp.changeMoneyUnit(val);
                },
                'oMaterialInfo.currencyUnit'(val, pre) { //생산국 변경시 통화단위 변경
                    vueApp.changeMoneyUnit2(val);
                },
                'oMaterialInfo.buyerSno'(val, pre) { //매입처 변경시
                    vueApp.oMaterialInfo.factoryName = '';
                    vueApp.oMaterialInfo.factoryPhone = '';
                    vueApp.oMaterialInfo.factoryAddress = '';
                    if (val == '-1') vueApp.isDisabledFactoryName = false;
                    else {
                        if (val != '0') vueApp.oMaterialInfo.factoryName = event.target.innerHTML;
                        vueApp.isDisabledFactoryName = true;
                    }
                },
                'oMaterialInfo.typeDetailSno'(val, pre) { //품목구분 변경시
                    vueApp.oMaterialInfo.materialTypeText = '';
                    if (val == '-1') vueApp.isDisabledMaterialTypeText = false;
                    else {
                        if (val != '0') vueApp.oMaterialInfo.materialTypeText = event.target.innerHTML;
                        vueApp.isDisabledMaterialTypeText = true;
                    }
                },
            }
        }
        ImsBoneService.setData(serviceData,{
            iTabNum : 1,
            isModify : false,
            isDisabledFactoryName : true,
            isDisabledMaterialTypeText : true,
            oMaterialInfo : {
            <?php foreach ($aUpsertForm as $val) { ?>
                <?=$val['db_fld']?> : <?=is_array($val['default_val'])?'['.implode(',', $val['default_val']).']':'"'.$val['default_val'].'"'?>,
            <?php } ?>
            },
            oMaterialInfoHan : {
                <?php foreach ($aUpsertFormHan as $key => $val) { ?>
                <?=$key?> : <?=is_array($val)?'['.implode(',', $val).']':'"'.$val.'"'?>,
                <?php } ?>
            },
            vue_val_width_unit : '"',
            vue_val_money_unit : "\\",
            vue_val_weight_unit : 'G',
            vue_val_text_day : '일',
            aoGrpMateList : [],
            //시험성적서
            fTotalAvgByMaterial : 0, //기본정보에서 보여줄 원단 시험성적서 평균점수
            isModifyTestReport : false, //시험성적서 작성내용 수정여부
            aoTestReportList : [], //시험성적서 리스트
            aoTestSelfList : [], //자체테스트 리스트
            iChooseTestReportSno : 0, //상세보기한 시험성적서sno
            ooAvgTestReport : {}, //시험성적서 평균리스트(시험성적서 보기 미클릭시 표시)
            iStaticAvgTestReportMaxRow : 0, //시험성적서 평균리스트 : 시험항목이 제일 많은 그룹의 항목수
            aoTestReportDetail : [], //상세보기한 시험성적서의 작성내용
            iChkTabTestReportDetail : 0, //시험성적서 작성내용에서 클릭한 1depth 분류idx
            igCustomerSno : <?=(int)$requestParam['customerSno']?>,
            sgCustomerName : '<?=$requestParam['customerName']?>',
            sgMaterialColor : '<?=$requestParam['materialColor']?>',
            sgTestReportYn : '<?=$requestParam['testReportYn']?>',
            sgTestSelfYn : '<?=$requestParam['testSelfYn']?>',
            iChooseTestType : 1, //등록/상세보기한 testType(1:시험성적서, 2:자체테스트)
            ooDefaultJson : { //자체테스트 코멘트 default form
                'jsonTestSelfComment' : { commentUserSno:'', commentDt:'', commentName:'', commentDesc:'', },
            },
            istaticManagerSno : <?=\Session::get('manager.sno')?>, //현재 접속자 sno. 고정값
            sstaticManagerName : '<?=\Session::get('manager.managerNm')?>', //현재 접속자명. 고정값
            sstaticCurrDt : '<?=date('Y-m-d')?>', //현재일자. 고정값
            oChooseTestSelf : {}, //자체테스트 보기 누르면 담김
            bFlagTestListFold : false, //시험성적서/자체테스트 리스트 접기(시험성적서 수정시 너비확보)
            oTestSelfFile : {}, //자체테스트 첨부파일
        });

        ImsBoneService.setMethod(serviceData,{
            changeMoneyUnit : (sCountryCode)=>{
                switch (sCountryCode) {
                    case 'cn':
                    case 'vn':
                        vueApp.oMaterialInfo.currencyUnit = 2;
                        break;
                    default:
                        vueApp.oMaterialInfo.currencyUnit = 1;
                        break;
                }
            },
            changeMoneyUnit2 : (iCurrencyKey)=>{
                if (iCurrencyKey == 1) vueApp.vue_val_money_unit = "\\";
                else vueApp.vue_val_money_unit = "$";
            },
            //수정버튼 클릭시(현재 탭메뉴에 따라 다르게 동작함)
            clickUpdateBtn : ()=>{
                if (vueApp.iTabNum==3) {
                    if (vueApp.iChooseTestReportSno == 0) {
                        $.msg('시험성적서 작성내용을 수정하려면 먼저 시험성적서 보기를 클릭하세요','','warning');
                        return false;
                    }
                    vueApp.isModifyTestReport=true;
                    vueApp.bFlagTestListFold=true;
                } else {
                    vueApp.iTabNum=1;
                    vueApp.isModify=true;
                }
            },
            //시험성적서 : 관련 변수 초기화(탭메뉴 클릭시 실행)
            resetTestReportData : ()=>{
                vueApp.isModifyTestReport = false;
                vueApp.iChooseTestReportSno = 0;
                vueApp.aoTestReportDetail = [];
                vueApp.iChkTabTestReportDetail = 0;
                vueApp.bFlagTestListFold=false;
            },
            //시험성적서, 자체테스트 : 리스트 가져오기
            getTestReportList : ()=>{
                let oSchParam = { 'materialSno':vueApp.oMaterialInfo.sno };
                //해당 자재의 모든 시험성적서를 가져와야 자재기본정보에서 평균점수 제대로 계산됨
                // if (vueApp.igCustomerSno > 0) oSchParam.customerSno = vueApp.igCustomerSno;
                // if (vueApp.sgMaterialColor != '') oSchParam.materialColor = vueApp.sgMaterialColor;
                ImsNkService.getList('testReport', oSchParam).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.aoTestReportList = [];
                        vueApp.aoTestSelfList = [];
                        $.each(data, function (key, val) {
                            if (val.testType == 1) vueApp.aoTestReportList.push(val);
                            else if (val.testType == 2) vueApp.aoTestSelfList.push(val);
                        });

                        //전체평균표obj 구성(시험성적서 아무것도 클릭 안했을때 표시)
                        vueApp.ooAvgTestReport = {};
                        if (vueApp.aoTestReportList.length > 0) {
                            vueApp.fTotalAvgByMaterial = 0;
                            $.each(vueApp.aoTestReportList, function (key, val) {
                                //전체평균값 구하기
                                vueApp.fTotalAvgByMaterial += Number(val.totalAvg);

                                $.each(val.jsonFillContents, function (key2, val2) {
                                    //그룹명으로 중복체크
                                    if (vueApp.ooAvgTestReport[val2.grpName] == undefined) vueApp.ooAvgTestReport[val2.grpName] = {aAvgVal:[], iSumVal:0, aGrpList:[]};
                                    $.each(val2.childGrp, function (key3, val3) {
                                        vueApp.ooAvgTestReport[val2.grpName].aAvgVal.push(val3.avgVal);
                                        vueApp.ooAvgTestReport[val2.grpName].iSumVal += Number(val3.avgVal);

                                        //그룹명으로 중복체크
                                        if (vueApp.ooAvgTestReport[val2.grpName][val3.grpName] == undefined) vueApp.ooAvgTestReport[val2.grpName][val3.grpName] = {aAvgVal:[], iSumVal:0};
                                        vueApp.ooAvgTestReport[val2.grpName][val3.grpName].aAvgVal.push(val3.avgVal);
                                        vueApp.ooAvgTestReport[val2.grpName][val3.grpName].iSumVal += Number(val3.avgVal);
                                    });
                                });
                            });
                            //전체평균값 구하기
                            vueApp.fTotalAvgByMaterial = Math.round(vueApp.fTotalAvgByMaterial / vueApp.aoTestReportList.length * 100) / 100;
                            $.each(vueApp.ooAvgTestReport, function (key, val) {
                                $.each(val, function (key2, val2) {
                                    if (val2 && toString.call(val2) === '[object Object]') {
                                        val2.sGrpName = key2;
                                        vueApp.ooAvgTestReport[key].aGrpList.push(val2);
                                    }
                                });
                            });
                            $.each(vueApp.ooAvgTestReport, function (key, val) {
                                if (val.aGrpList.length > vueApp.iStaticAvgTestReportMaxRow) vueApp.iStaticAvgTestReportMaxRow = val.aGrpList.length;
                            });
                        }

                        //작업지시서에서 시험성적서 보기/등록 으로 접근한 경우(페이지 진입시 1번만 실행), 첨부파일모듈 시작
                        if (vueApp.igCustomerSno > 0 && (vueApp.sgTestReportYn != '' || vueApp.sgTestSelfYn != '' )) {
                            vueApp.iTabNum = 3;

                            //작업지시서에서 시험성적서 보기 버튼 클릭시 and 검색된 시험성적서가 1개만 나왔을 때는 자동으로 시험성적서 보기버튼 클릭
                            if (vueApp.sgTestReportYn == 'y' && vueApp.aoTestReportList.length == 1) {
                                vueApp.viewTestDetail(1, vueApp.aoTestReportList[0]);
                            }
                            if (vueApp.sgTestReportYn == 'n') {
                                vueApp.iChooseTestType = 1;
                                vueApp.openRegistTestReport();
                            }
                            vueApp.sgTestReportYn = '';
                            
                            //작업지시서에서 자체테스트 보기 버튼 클릭시 and 검색된 자체테스트가 1개만 나왔을 때는 자동으로 자체테스트 보기버튼 클릭
                            if (vueApp.sgTestSelfYn == 'y' && vueApp.aoTestSelfList.length == 1) {
                                vueApp.viewTestDetail(2, vueApp.aoTestSelfList[0]);
                            }
                            if (vueApp.sgTestSelfYn == 'n') {
                                vueApp.iChooseTestType = 2;
                                vueApp.openRegistTestReport();
                            }
                            vueApp.sgTestSelfYn = '';
                        }
                    });
                });
            },
            //시험성적서 : 등록 클릭시(성적서폼 가져오기)
            openRegistTestReport : ()=>{
                vueApp.resetTestReportData();
                //시험성적서 작성품 가져오기
                vueApp.aoTestReportDetail = [];
                ImsNkService.getList('testReportForm', {'testType':vueApp.iChooseTestType}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.aoTestReportDetail = data;
                        vueApp.isModifyTestReport = true;
                        vueApp.bFlagTestListFold = true;
                    });
                });
            },
            //시험성적서 -> 수정 -> 그룹 추가시
            addTestGrp : (iGrpDepth, iKeyTarget=0)=>{
                if (iGrpDepth == 1) {
                    let oAddGrp = vueApp.addElement(vueApp.aoTestReportDetail, {grpName:'',sumAvgYn:'',childGrp:[]}, 'after')
                    oAddGrp.sumAvgYn = 'n';
                    oAddGrp.childGrp = [{grpName:'',standardVal:'',avgVal:'0',childRow:[{rowName:'',rowVal:'',rowValNumber:0}]}];
                } else if (iGrpDepth == 2) {
                    let oAddGrp = vueApp.addElement(vueApp.aoTestReportDetail[iKeyTarget].childGrp, {grpName:'',standardVal:'',avgVal:'',childRow:[]}, 'after');
                    oAddGrp.avgVal = '0';
                    oAddGrp.childRow = [{rowName:'',rowVal:'',rowValNumber:0}];
                }
            },
            //시험성적서 -> 수정 -> 시험값 변경시 number값 저장 and 평균값 계산
            calcTestReportAvg : (iKey1, iKey2, iKey3)=>{
                if (isNaN(vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow[iKey3].rowVal) === false) {
                    vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow[iKey3].rowValNumber = vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow[iKey3].rowVal;
                } else {
                    vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow[iKey3].rowValNumber = (Number(vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow[iKey3].rowVal.split('-')[1]) + Number(vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow[iKey3].rowVal.split('-')[0]))/2;
                }
                //2depth 그룹의 평균값 구하기
                let fSumVal = 0;
                $.each(vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow, function(key, val) {
                    fSumVal += Number(this.rowValNumber);
                });
                vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].avgVal = Math.round(fSumVal / vueApp.aoTestReportDetail[iKey1].childGrp[iKey2].childRow.length * 100) / 100;
            },
            //시험성적서 : 항목삭제
            deleteTestReportElement : (iType, oTarget, iKey)=>{
                let sMsg = '해당 그룹을 삭제하시만 하위항목의 내용이 모두 삭제됩니다. 계속하시겠습니까?';
                if (iType == 3) sMsg = '해당 항목을 삭제하시겠습니까?';

                $.msgConfirm(sMsg,'').then(function(result){
                    if( result.isConfirmed ){
                        vueApp.deleteElement(oTarget, iKey);
                    }
                });
            },
            //시험성적서, 자체테스트 상세보기
            viewTestDetail : (iTestType, oTarget)=>{
                vueApp.iChooseTestType = iTestType;
                vueApp.resetTestReportData();
                vueApp.iChooseTestReportSno = oTarget.sno;
                vueApp.aoTestReportDetail = oTarget.jsonFillContents;
                if (iTestType == 2) {
                    vueApp.oChooseTestSelf = oTarget;
                    //테스트 사진
                    vueApp.oTestSelfFile = oTarget.fileList;
                    if ($('.set-dropzone').hasClass('dropzone') === false) {
                        vueApp.$nextTick(function () {
                            $('.set-dropzone').addClass('dropzone');
                            ImsService.setDropzone(vueApp, 'materialTestSelf', vueApp.uploadAfterActionTestSelfFile);
                        });
                    }
                }
            },

            //자재이미지, 스와치사진 업로드
            uploadImageFile : (sFldName)=>{
                const fileInput = vueApp.$refs[sFldName+'Element'];
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('upfile', fileInput.files[0]);

                    //이미지파일인지 체크
                    const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$/i;
                    if (!allowedExtensions.exec(fileInput.files[0].name)) {
                        $.msg('반드시 이미지파일을 업로드하셔야 합니다','','warning');
                        return false;
                    }

                    $.ajax({
                        url: '<?=$nasUrl?>/img_upload.php?projectSno=0',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(result){
                            const rslt = JSON.parse(result);
                            //컬럼값 바로 update
                            vueApp.oMaterialInfoHan[sFldName] = '<?=$nasUrl?>'+rslt.downloadUrl;
                            $.imsPost('modifySimpleDbCol', {'table_number':3, 'colNm':sFldName, 'where':{'sno':vueApp.oMaterialInfo.sno}, 'data':vueApp.oMaterialInfoHan[sFldName]});
                        }
                    });
                }
            },
            save : ()=>{
                if (vueApp.oMaterialInfo.buyerSno == -1 && vueApp.oMaterialInfo.factoryName == '') {
                    $.msg('매입처를 신규등록 하신다면 매입처명을 입력하셔야 합니다','','error');
                    return false;
                }
                if (vueApp.oMaterialInfo.typeDetailSno == -1 && vueApp.oMaterialInfo.materialTypeText === '') {
                    $.msg('품목구분을 신규등록 하신다면 품목구분명을 입력하세요','','error');
                    return false;
                }
                if (vueApp.oMaterialInfo.name == '') {
                    $.msg('자재명을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setMaterialNk', {'data' : vueApp.oMaterialInfo}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('<?=$aTextTitle?> <?=$aTextMode?> 완료','','success').then(()=>{
                            parent.opener.refreshList();
                            self.close();
                        });
                    });
                });
            },
            //시험성적서 -> 작성내용 저장
            save_test_report : ()=>{
                let oParam = {sno:vueApp.iChooseTestReportSno, testType:vueApp.iChooseTestType, totalAvg:0, jsonFillContents:vueApp.aoTestReportDetail};

                //시험성적서 입력값정리(trim), 평균값 구하기
                if (vueApp.iChooseTestType == 1) {
                    let iSumAvg = 0;
                    let iCntAvg = 0;
                    $.each(oParam.jsonFillContents, function(key, val) {
                        $.each(this.childGrp, function(key2, val2) {
                            $.each(this.childRow, function(key3, val3) {
                                oParam.jsonFillContents[key].childGrp[key2].childRow[key3].rowVal = String(this.rowVal).trim();
                                oParam.jsonFillContents[key].childGrp[key2].childRow[key3].rowValNumber = String(this.rowValNumber).trim();
                            });
                        });
                    });
                    $.each(oParam.jsonFillContents, function(key, val) {
                        if (this.sumAvgYn == 'y') {
                            $.each(this.childGrp, function(key2, val2) {
                                iSumAvg += Number(this.avgVal);
                                iCntAvg++;
                            });
                        }
                    });
                    if (iSumAvg > 0) {
                        oParam.totalAvg = Math.round(iSumAvg / iCntAvg * 100) / 100;
                    }
                } else if (vueApp.iChooseTestType == 2) {
                    //코멘트가 하나도 없으면 json_encode할때 testComment:[] 도 안나온다. 빈line 추가해주기(리스트에는 안나옴)
                    if (oParam.jsonFillContents[0].testComment.length == 0) oParam.jsonFillContents[0].testComment = [vueApp.ooDefaultJson.jsonTestSelfComment];
                }

                //등록일때
                if (oParam.sno == 0) {
                    if (vueApp.igCustomerSno == 0) {
                        $.msg('접근오류','','error');
                        return false;
                    }
                    oParam.materialSno = <?=(int)$requestParam['sno']?>;
                    oParam.materialColor = vueApp.sgMaterialColor;
                    oParam.customerSno = vueApp.igCustomerSno;
                }

                $.imsPost('setSimpleDbTable', {'data':oParam, 'table_number':12}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        vueApp.resetTestReportData();
                        vueApp.getTestReportList();
                    });
                });
            },
            //자체테스트 - 코멘트 추가
            addTestSelfComment : ()=>{
                let oAppendObj = vueApp.addElement(vueApp.aoTestReportDetail[0].testComment, vueApp.ooDefaultJson.jsonTestSelfComment, 'after');
                oAppendObj.commentUserSno = vueApp.istaticManagerSno;
                oAppendObj.commentDt = vueApp.sstaticCurrDt;
                oAppendObj.commentName = vueApp.sstaticManagerName;
                vueApp.isModifyTestReport = true;
                vueApp.bFlagTestListFold = true;
            },
            //파일 업로드할때 실행하는 함수
            uploadAfterActionTestSelfFile : (tmpFile, dropzoneId)=>{
                ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                    let saveData = {
                        customerSno : vueApp.igCustomerSno,
                        projectSno : 0,
                        styleSno : 0,
                        eachSno : vueApp.iChooseTestReportSno,
                        fileDiv : dropzoneId,
                        fileList : saveFileList,
                        memo : promptValue,
                    };
                    $.imsPost('saveProjectFiles',{
                        saveData : saveData
                    }).then((data)=>{
                        if(200 === data.code) {
                            location.reload();
                        }
                    });
                });
            },
        });
        ImsBoneService.setMounted(serviceData, ()=>{
            vueApp.changeMoneyUnit2(vueApp.oMaterialInfo.currencyUnit);
            //등록페이지 진입시
            if (vueApp.oMaterialInfo.sno == 0) vueApp.isModify = true;
            else {
                //같은 유사퀄리티 자재리스트 가져오기
                if (vueApp.oMaterialInfoHan.groupSno != '0') {
                    ImsNkService.getList('material', {'sRadioSchGroupSno':vueApp.oMaterialInfoHan.groupSno}).then((data)=> {
                        $.imsPostAfter(data, (data) => {
                            $.each(data.list, function (key, val) {
                                if (vueApp.oMaterialInfo.sno != this.sno) {
                                    vueApp.aoGrpMateList.push(this);
                                }
                            });
                        });
                    });
                }
                //시험성적서리스트 가져오기
                vueApp.getTestReportList();
                //원부자재 리스트에서 시험성적서 아이콘 클릭으로 접근시
                if (<?=(int)$requestParam['defaultTabNum']?> > 0) {
                    vueApp.iTabNum = <?=(int)$requestParam['defaultTabNum']?>;
                }
            }
        });
        ImsBoneService.setComputed(serviceData,{
            computed_change_detail_title() {
                if (this.aoTestReportDetail.length == 0) {
                    return '시험성적서 전체평균(리스트의 시험성적서 대상)';
                } else {
                    let sTestType = '시험성적서 작성내용';
                    if (this.iChooseTestType == 2) sTestType = '자체테스트 작성내용';
                    let sType = '';
                    if (this.isModifyTestReport) {
                        if (this.iChooseTestReportSno == 0) {
                            sType = '등록';
                        } else {
                            sType = '수정 [';
                            let aoTarget = vueApp.aoTestReportList;
                            if (this.iChooseTestType == 2) aoTarget = vueApp.aoTestSelfList;
                            $.each(aoTarget, function (key, val) {
                                if (vueApp.iChooseTestReportSno == val.sno) {
                                    sType += val.customerName+' / '+val.materialColor+' / '+val.styleInfo+' / '+$.formatShortDateWithoutWeek(val.regDt);
                                }
                            });
                            sType += ']';
                        }
                    }
                    return sTestType+' '+sType;
                }
            },
        });
        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
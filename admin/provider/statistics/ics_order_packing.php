<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<?php if (isset($sResultMsgExcelUpload) && $sResultMsgExcelUpload != '') { ?>
<section class="project-view">
    <div class="page-header js-affix mgb0">
        <h3><?=$sResultMsgExcelUpload?></h3>
        <span class="btn btn-white" style="line-height: 38px;" onclick="location.href='ims_pop_check_customer_delivery.php'">돌아가기</span>
    </div>
    <div class="col-xs-12 row" v-show="'packing' === tabMode" >
        <div class="col-xs-12 js-order-view-receiver-area relative">
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    업로드 성공건수 <span onclick="document.getElementById('table-success').style.display='none';" class="btn btn-white">감추기</span> <span onclick="document.getElementById('table-success').style.display='table';" class="btn btn-white">펼치기</span>
                </div>
            </div>
            <div class="js-layout-order-view-receiver-info">
                <table id="table-success" class="table table-cols table-default-center table-pd-0 table-td-height30 mgt5" style="width:1200px;">
                    <colgroup>
                        <col class="w-50px"><!--번호-->
                        <col class="w-100px"><!--받는분-->
                        <col class="w-100px"><!--지점-->
                        <col class=""><!--주소-->
                        <col class="w-200px"><!--송장번호-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>받는분</th>
                        <th>지점</th>
                        <th>주소</th>
                        <th>송장번호</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($aResultMsgExcelUploadSuccess) == 0) { ?>
                    <tr>
                        <td colspan="99">데이터가 없습니다.</td>
                    </tr>
                    <?php } else { foreach ($aResultMsgExcelUploadSuccess as $key => $val) { ?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$val['name']?></td>
                        <td><?=$val['branch']?></td>
                        <td class="ta-l"><?=$val['addr']?></td>
                        <td><?=$val['invoice']?></td>
                    </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 js-order-view-receiver-area relative">
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    업로드 실패건수(존재하는 배송지점이지만 미선택) <span onclick="document.getElementById('table-fail2').style.display='none';" class="btn btn-white">감추기</span> <span onclick="document.getElementById('table-fail2').style.display='table';" class="btn btn-white">펼치기</span>
                </div>
            </div>
            <div class="js-layout-order-view-receiver-info">
                <table id="table-fail2" class="table table-cols table-default-center table-pd-0 table-td-height30 mgt5" style="width:1200px;">
                    <colgroup>
                        <col class="w-50px"><!--번호-->
                        <col class="w-100px"><!--받는분-->
                        <col class="w-100px"><!--지점-->
                        <col class=""><!--주소-->
                        <col class="w-200px"><!--송장번호-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>받는분</th>
                        <th>지점</th>
                        <th>주소</th>
                        <th>송장번호</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($aResultMsgExcelUploadNotSelect) == 0) { ?>
                        <tr>
                            <td colspan="99">데이터가 없습니다.</td>
                        </tr>
                    <?php } else { foreach ($aResultMsgExcelUploadNotSelect as $key => $val) { ?>
                        <tr>
                            <td><?=$key+1?></td>
                            <td><?=$val['name']?></td>
                            <td><?=$val['branch']?></td>
                            <td class="ta-l"><?=$val['addr']?></td>
                            <td><?=$val['invoice']?></td>
                        </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 js-order-view-receiver-area relative">
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    업로드 실패건수(중복된 송장번호) <span onclick="document.getElementById('table-fail3').style.display='none';" class="btn btn-white">감추기</span> <span onclick="document.getElementById('table-fail3').style.display='table';" class="btn btn-white">펼치기</span>
                </div>
            </div>
            <div class="js-layout-order-view-receiver-info">
                <table id="table-fail3" class="table table-cols table-default-center table-pd-0 table-td-height30 mgt5" style="width:1200px;">
                    <colgroup>
                        <col class="w-50px"><!--번호-->
                        <col class="w-100px"><!--받는분-->
                        <col class="w-100px"><!--지점-->
                        <col class=""><!--주소-->
                        <col class="w-200px"><!--송장번호-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>받는분</th>
                        <th>지점</th>
                        <th>주소</th>
                        <th>송장번호</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($aResultMsgExcelUploadDup) == 0) { ?>
                        <tr>
                            <td colspan="99">데이터가 없습니다.</td>
                        </tr>
                    <?php } else { foreach ($aResultMsgExcelUploadDup as $key => $val) { ?>
                        <tr>
                            <td><?=$key+1?></td>
                            <td><?=$val['name']?></td>
                            <td><?=$val['branch']?></td>
                            <td class="ta-l"><?=$val['addr']?></td>
                            <td><?=$val['invoice']?></td>
                        </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 js-order-view-receiver-area relative">
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    업로드 실패건수(담당자(배송지점) 정보 미매칭) <span onclick="document.getElementById('table-fail').style.display='none';" class="btn btn-white">감추기</span> <span onclick="document.getElementById('table-fail').style.display='table';" class="btn btn-white">펼치기</span>
                </div>
            </div>
            <div class="js-layout-order-view-receiver-info">
                <table id="table-fail" class="table table-cols table-default-center table-pd-0 table-td-height30 mgt5" style="width:1200px;">
                    <colgroup>
                        <col class="w-50px"><!--번호-->
                        <col class="w-100px"><!--받는분-->
                        <col class="w-100px"><!--지점-->
                        <col class=""><!--주소-->
                        <col class="w-200px"><!--송장번호-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>받는분</th>
                        <th>지점</th>
                        <th>주소</th>
                        <th>송장번호</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($aResultMsgExcelUploadFail) == 0) { ?>
                        <tr>
                            <td colspan="99">데이터가 없습니다.</td>
                        </tr>
                    <?php } else { foreach ($aResultMsgExcelUploadFail as $key => $val) { ?>
                        <tr>
                            <td><?=$key+1?></td>
                            <td><?=$val['name']?></td>
                            <td><?=$val['branch']?></td>
                            <td class="ta-l"><?=$val['addr']?></td>
                            <td><?=$val['invoice']?></td>
                        </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php } else { ?>
<style>
    input[type=number] {
        padding: 4px 6px!important;
    }
    .table_assort th, .table_assort td { padding:0px!important; height:24px!important; }
</style>

<section id="imsApp" class="project-view">
    <?php if (!isset($bFlagOpenIms) || $bFlagOpenIms === false) { ?>
    <div class="page-header js-affix">
        <h3>담당자 리스트
            <span v-show="bFlagReceiverFold" @click="bFlagReceiverFold=false;" class="btn btn-white" style="line-height: 35px;">펼치기</span>
            <span v-show="!bFlagReceiverFold" @click="bFlagReceiverFold=true;" class="btn btn-white" style="line-height: 35px;">접기</span>
        </h3>
    </div>
    <div v-show="!bFlagReceiverFold" class="row" >
        <div class="col-xs-12" >
            <div class="">
                <div class="flo-left mgb5">
                    <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listData.length) %}</span> 명
                        </span>
                    </div>
                </div>
                <div class="flo-right mgb5">
                    <div class="" style="display: flex; ">
                        <span v-show="!isModify" @click="isModify = true;" class="btn btn-red-line mgr10">담당자 설정</span>
                        <span v-show="isModify" @click="save_receiver()" class="btn btn-red mgr10">담당자 저장</span>
                        <span v-show="isModify" @click="isModify = false;" class="btn btn-white mgr10">담당자 설정취소</span>
                        <span v-show="!isModify && iPackingSt==1" @click="regist_delivery()" class="btn btn-red">분류패킹 등록</span>
                    </div>
                </div>
            </div>
            <!--list start-->
            <div>
                <input type="hidden" name="zonecode" id="zonecode" /><input type="hidden" name="address" id="address" /><!--우편번호찾기 : 이거 2개 필요-->
                <table v-if="!$.isEmpty(searchData)" class="table table-rows table-default-center table-td-height30">
                    <colgroup>
                        <col v-show="!isModify && iPackingSt==1" class="w-3p" />
                        <col v-show="isModify" class="w-3p" />
                        <col class="w-5p" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="fieldData.skip != true" />
                        <col v-if="isModify" class="w-5p" />
                    </colgroup>
                    <tr>
                        <th v-show="!isModify && iPackingSt==1"><input type='checkbox' name="receiverSnoAllChk" value='y' class='js-checkall' data-target-name='receiverSno' /></th>
                        <th v-show="isModify">이동</th>
                        <th>번호</th>
                        <th v-for="fieldData in searchData.fieldData">{% fieldData.title %}</th>
                        <th v-if="isModify">기능 <span @click="addRow(-1)" class="btn btn-white btn-sm">+ 추가</span></th>
                    </tr>
                    <tbody is="draggable" :list="listData"  :animation="200" tag="tbody" handle=".handle">
                    <tr v-if="listData.length == 0">
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    <tr v-else v-for="(val, key) in listData" class="hover-light">
                        <td v-show="!isModify && iPackingSt==1">
                            <input v-if="val.sno" type="checkbox" name="receiverSno[]" :value="val.sno" class="list-check" >
                        </td>
                        <td v-show="isModify" class="handle">
                            <div class="cursor-pointer hover-btn">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                            </div>
                        </td>
                        <td >{% key+1 %}</td>
                        <td v-if="fieldData.skip != true" v-for="fieldData in searchData.fieldData" :class="fieldData.class + ''">
                            <span v-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                            <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                            <span v-else-if="fieldData.type === 'postcode'">
                                <span v-if="isModify">
                                    <input type="text" v-model="val[fieldData.name]" class="form-control w-100px" style="display: inline-block;" readonly="readonly" />
                                    <input type="button" @click="iKeyChooseSchAddr=key; postcode_search('zonecode', 'address', 'zipcode');" value="우편번호찾기" class="btn btn-gray btn-sm"/>
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </span>
                            <span v-else>
                                <span v-if="isModify">
                                    <input type="text" v-model="val[fieldData.name]" class="form-control" />
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </span>
                        </td>
                        <td v-if="isModify">
                            <span @click="addRow(key)" class="btn btn-white btn-sm">+ 추가</span>
                            <span @click="deleteRow(val.sno, key)" class="btn btn-red btn-sm">삭제</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="page-header js-affix mgb0">
        <h3>분류패킹 리스트 <span ref="textPackingSt"></span></h3>
        <div class="btn-group">
            <?php if (!isset($bFlagOpenIms) || $bFlagOpenIms === false) { /* 고객페이지에서만 고객확정 */ ?>
            <span v-show="iPackingSt==1" @click="confirm_delivery_packing()" class="btn btn-blue" style="line-height: 35px;">분류패킹 확정하기</span>
            <?php } ?>

            <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { /* IMS에서만 고객확정취소, 이노버확정 */ ?>
            <span v-show="iPackingSt==2" @click="cancel_customer_confirm(1)" class="btn btn-red" style="line-height: 35px;">고객확정 취소</span>
            <span v-show="iPackingSt==2" @click="confirm_delivery_packing_ims()" class="btn btn-blue" style="line-height: 35px;">이노버확정</span>
            <span v-show="iPackingSt==3" @click="cancel_customer_confirm(2)" class="btn btn-red" style="line-height: 35px;">이노버확정 취소</span>
            <?php } ?>

            <span v-show="iPackingSt==1 <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { /* IMS에서 접근시 고객확정상태일 때에도 입력수량 변경가능하게 */ ?>|| iPackingSt==2<?php } ?>" @click="modify_delivery()" class="btn btn-red" style="line-height: 35px;">내용 저장</span>

            <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
            <div @click="self.close();" class="btn btn-gray" style="line-height: 35px;">닫기</div>
            <?php } ?>
        </div>
    </div>
    <div class="row" >
        <div class="pdl15" >
            <div style="clear: both;">
                <table class="table table-rows table-default-center table-td-height30">
                    <colgroup>
                        <col class="w-200px" />
                        <col class="w-100px" />
                        <col class="" />
                    </colgroup>
                    <thead>
                    <th>스타일명</th>
                    <th>생산수량 합계</th>
                    <th>사이즈별 수량(생산수량 / 입력수량)</th>
                    </thead>
                    <tbody>
                    <tr v-if="Object.keys(ooCntSizeTotal).length == 0">
                        <td colspan="99">데이터가 없습니다.</td>
                    </tr>
                    <template v-else v-for="(val, key) in ooCntSizeTotal">
                        <tr>
                            <td>{% ooCntSizeTotalUtil[key].styleName %}</td>
                            <td>{% $.setNumberFormat(ooCntSizeTotalUtil[key].cntTotal) %} 장</td>
                            <td style="padding:5px 0px;">
                                <table class="table table-rows table-default-center table-td-height30 mgb0">
                                    <tr>
                                        <th v-if="false" class="w-100px">구분</th>
                                        <th v-for="(val3, key3) in ooCntSizeTotalUtil[key].sizeList">{% val3 %}</th>
                                    </tr>
                                    <template v-for="(val2, key2) in val">
                                        <tr>
                                            <th v-if="false" rowspan="2">{% key2.split('___')[0] %}</th><td v-for="(val3, key3) in val2"><span :class="val3.flagOverQty == true ? 'bold text-danger' : ''">{% $.setNumberFormat(val3.customerQty) %}</span></td>
                                        </tr>
                                        <tr>
                                            <td v-for="(val3, key3) in val2"><span :class="val3.flagOverQty == true ? 'bold text-danger' : ''">{% $.setNumberFormat(val3.currQty) %}</span></td>
                                        </tr>
                                    </template>
                                </table>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>

            <div class="">
                <div class="flo-left mgb5">
                    <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            담당자별 분류패킹 리스트
                        </span>
                    </div>
                </div>
                <div v-show="iPackingSt==3" class="flo-left mgl20">
                    <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
                        <form id="frmRegistTrackingNum" name="frmRegistTrackingNum" action="./ims_pop_check_customer_delivery.php" method="post" enctype="multipart/form-data" @submit.prevent="listUpload();" style="display: inline-block; border:1px #ccc solid; padding:5px 10px; margin-top:-5px;">
                        <div class="form-inline">
                            운송장번호 업로드
                            <input type="hidden" name="packing_sno" value="" />
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="submit" class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                            <select name="delivery_company" class="form-control">
                                <option value="">배송회사를 선택하세요</option>
                                <option v-for="(val, key) in oDeliveryCompanyList" :value="key">{% val %}</option>
                            </select>
                            <input type="hidden" name="delivery_receivers" value="" />
                        </div>
                        </form>
                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload()">분류패킹현황 엑셀 다운로드</button>
                    <?php } ?>
                </div>
            </div>
            <!--list start-->
            <div>
                <table class="table table-rows table-default-center table-td-height30">
                    <colgroup>
                        <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
                        <col v-show="iPackingSt==3" class="w-50px" /> <!--체크박스 : 송장번호 업로드할 배송지선택-->
                        <?php } ?>
                        <col class="w-50px" />
                        <col class="w-100px" />
                        <col class="w-100px" />
                        <col class="w-100px" />
                        <col class="w-300px" />
                        <col class="w-150px" />
                        <col v-show="iPackingSt==1" class="w-50px" />
                        <col v-if="false" class="w-50px" />
                        <col class="" />
                        <col v-show="iPackingSt>2" class="w-200px" />
                    </colgroup>
                    <tr>
                        <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
                            <th v-show="iPackingSt==3">
                                <input type="checkbox" @click="toggleAllChkNk('deliverySno[]', event.target.checked)" />
                            </th> <!--체크박스 : 송장번호 업로드할 배송지선택-->
                        <?php } ?>
                        <th>번호</th>
                        <th>지점<br/>부서</th>
                        <th>담당자<br/>연락처</th>
                        <th>상태
                            <?php if (!isset($bFlagOpenIms) || $bFlagOpenIms === false) { ?>
                            <span v-show="iPackingSt==1" @click="requestWrite()" class="btn btn-white">일괄요청</span>
                            <?php } ?>
                        </th>
                        <th>우편번호<br/>주소<br/>희망 납품장소</th>
                        <th>스타일명</th>
                        <th v-show="iPackingSt==1">제외</th>
                        <th v-if="false">품목</th>
                        <th>옵션</th>
                        <th v-show="iPackingSt>2">운송장번호 (배송회사)</th>
                    </tr>
                    <tbody>
                    <tr v-if="aoPackingList.length == 0">
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    <template v-else v-for="(val, key) in aoPackingList">
                        <template v-for="(val2, key2) in val.jsonContents">
                            <template v-for="(val3, key3) in val2.aAssortList">
                                <tr>
                                    <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
                                    <td v-show="iPackingSt==3" v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan">
                                        <input type="checkbox" name="deliverySno[]" :value="val.sno" class="list-check" />
                                    </td> <!--체크박스 : 송장번호 업로드할 배송지선택-->
                                    <?php } ?>
                                    <td v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan">{% key+1 %}</td>
                                    <td v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan">{% val.branchName %}<br/>{% val.departmentName %}</td>
                                    <td v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan">{% val.managerName %}<br/>{% val.managerPhone %}</td>
                                    <td v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan">
                                        {% val.deliveryStHan %}
                                        <?php if (!isset($bFlagOpenIms) || $bFlagOpenIms === false) { ?>
                                        <span v-show="iPackingSt==1" @click="requestWrite(val.sno)" class="btn btn-white">입력요청</span>
                                        <?php } ?>
                                    </td>
                                    <td v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan">{% val.managerAddrPost %}<br/>{% val.managerAddr %}<br/>{% val.wishReceivePlace %}</td>
                                    <td v-if="key3 == 0" :rowspan="val2.rowspan">{% val2.styleName %}</td>
                                    <td v-show="iPackingSt==1"><span @click="deleteAssort(key, key2, key3);" class="btn btn-white btn-sm">제외</span></td>
                                    <td v-if="false">{% val3.assortType %}</td>
                                    <td style="padding:5px 0px;">
                                        <table class="table table-rows table-default-center table-td-height0 mgb0 table_assort">
                                            <tr>
                                                <th class="w-80px"></th>
                                                <th v-for="(val4, key4) in val3.oSizeList">{% key4 %}</th>
                                            </tr>
                                            <tr>
                                                <th>생산수량</th>
                                                <td v-for="(val4, key4) in val3.oSizeList" >
                                                    <span v-if="ooCntSizeTotal[val2.styleSno] == undefined"></span>
                                                    <span v-else :class="ooCntSizeTotal[val2.styleSno][val3.assortType+'___'+val3.assortCharge][key4].flagOverQty == true ? 'text-danger bold' : ''">
                                                        {% $.setNumberFormat(ooCntSizeTotal[val2.styleSno][val3.assortType+'___'+val3.assortCharge][key4].customerQty) %}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>입력수량</th>
                                                <td v-for="(val4, key4) in val3.oSizeList">
                                                    <span v-if="iPackingSt==1 <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { /* IMS에서 접근시 고객확정상태일 때에도 입력수량 변경가능하게 */ ?>|| iPackingSt==2<?php } ?>">
                                                        <input type="number" v-model="val4.expectQty" @change="changeSumCntSize(val2.styleSno, val3.assortType+'___'+val3.assortCharge, key4)" @keyup="changeSumCntSize(val2.styleSno, val3.assortType+'___'+val3.assortCharge, key4)" min="0" class="form-control" />
                                                    </span>
                                                    <span v-else>{% $.setNumberFormat(val4.expectQty) %}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td v-show="iPackingSt>2" v-if="key2 == 0 && key3 == 0" :rowspan="val.rowspan" >
                                        <div ref="update_delivery_y" style="display: none;">
                                            <textarea ref="update_delivery_nums" rows="10">{% val.invoiceNums==null?'':val.invoiceNums.replaceAll(', ', '\n') %}</textarea>
                                            <select ref="update_delivery_company" v-model="val.deliveryCompanyCode" class="form-control" style="margin:0px auto;">
                                                <option value="null">배송회사를 선택하세요</option>
                                                <option v-for="(val, key) in oDeliveryCompanyList" :value="key">{% val %}</option>
                                            </select>
                                            <br/><span @click="update_delivery_info(key)" class="btn btn-red btn-sm">저장</span>
                                            <span @click="$refs.update_delivery_n[key].style.display='block'; $refs.update_delivery_y[key].style.display='none';" class="btn btn-white btn-sm">취소</span>
                                        </div>
                                        <div ref="update_delivery_n" style="display: block;">
                                            {% val.invoiceNums==null?'미등록':val.invoiceNums %}<br/>{% val.deliveryCompanyCode==null||val.deliveryCompanyCode==''?'미등록':'('+oDeliveryCompanyList[val.deliveryCompanyCode]+')' %}
                                            <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
                                            <br/><span v-show="iPackingSt==3" @click="$refs.update_delivery_n[key].style.display='none'; $refs.update_delivery_y[key].style.display='block';" class="btn btn-red btn-red-line2 btn-sm">수정</span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </template>
                    </tbody>
                </table>
            </div>
            <!--list end-->
        </div>
    </div>
    <div class="ta-c">
        <?php if (!isset($bFlagOpenIms) || $bFlagOpenIms === false) { /* 고객페이지에서만 고객확정 */ ?>
        <span v-show="iPackingSt==1" @click="confirm_delivery_packing()" class="btn btn-blue" style="line-height: 35px;">분류패킹 확정하기</span>
        <?php } ?>
        
        <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { /* IMS에서만 고객확정취소, 이노버확정 */ ?>
        <span v-show="iPackingSt==2" @click="cancel_customer_confirm(1)" class="btn btn-red" style="line-height: 35px;">고객확정 취소</span>
        <span v-show="iPackingSt==2" @click="confirm_delivery_packing_ims()" class="btn btn-blue" style="line-height: 35px;">이노버확정</span>
        <span v-show="iPackingSt==3" @click="cancel_customer_confirm(2)" class="btn btn-red" style="line-height: 35px;">이노버확정 취소</span>
        <?php } ?>
        
        <span v-show="iPackingSt==1 <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { /* IMS에서만 고객확정상태일 때에도 입력수량 변경가능하게 */ ?>|| iPackingSt==2<?php } ?>" @click="modify_delivery()" class="btn btn-red" style="line-height: 35px;">내용 저장</span>
        
        <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { /* IMS에서만 수정취소, 팝업창닫기 */ ?>
        <div @click="self.close();" class="btn btn-gray" style="line-height: 35px;">닫기</div>
        <?php } ?>
    </div>
    <div style="margin-bottom:150px"></div>
</section>

<script type="text/javascript">
    var iPackingSno = <?=$iPackingSno?>;

    function postcode_callback() { //우편번호찾기 : 콜백함수 가로채기
        vueApp.listData[vueApp.iKeyChooseSchAddr].managerAddrPost = $('#zonecode').val();
        vueApp.listData[vueApp.iKeyChooseSchAddr].managerAddr = $('#address').val();
    }

    const mainListPrefix = 'customer_receiver';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',
        page : 1,
        pageNum : 10000,
        sort : 'sortNum,asc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListCustomerReceiver';
        return ImsNkService.getList('customerReceiver', params);
    };
    //자식 팝업창에서 실행
    function refreshList() {
        vueApp.refreshList(vueApp.searchCondition.page);
    }

    $(()=>{
        $('title').html('분류패킹 정보');

        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            /* 담당자 관련 변수들 start */
            bFlagReceiverFold : true,
            isModify:false,
            iKeyChooseSchAddr:0, //우편번호찾기 : 담당자key
            ooDefaultJson : { //담당자 default form
                'jsonReceiver' : { sno:0, customerSno:0, regManagerSno:0, branchName:'', departmentName:'', managerName:'', managerPhone:'', managerEmail:'', managerAddrPost:'', managerAddr:'', },
            },
            /* 담당자 관련 변수들 end */
            ooCntSizeTotal : {}, //사이즈별 생산수량(생산수량===총수량)
            ooCntSizeTotalUtil : {}, //개발용 보조obj
            iPackingSt : 1,
            aoPackingList : [], //분류패킹 리스트
            oDeliveryCompanyList : { //배송회사코드
                <?php foreach(\SiteLabUtil\SlCommonUtil::getDeliveryCompanyMap() as $key => $val) { ?>
                "<?=$key?>" : "<?=$val?>",
                <?php } ?>
            },
        });
        ImsBoneService.setMethod(serviceData, {
            /* 담당자 관련 함수들 start */
            addRow : (iKey)=>{
                if (iKey == -1) vueApp.addElement(vueApp.listData, vueApp.ooDefaultJson.jsonReceiver, 'after');
                else vueApp.addElement(vueApp.listData, vueApp.ooDefaultJson.jsonReceiver, 'down', iKey);
            },
            deleteRow : (sno, iKey)=>{
                if (sno == '' || sno == 0) {
                    vueApp.deleteElement(vueApp.listData, iKey);
                } else {
                    $.msgConfirm('정말 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                        if( result.isConfirmed ){
                            ImsNkService.setDelete('jjjjj', sno).then(()=>{
                                vueApp.deleteElement(vueApp.listData, iKey);
                            });
                        }
                    });
                }
            },
            save_receiver : ()=>{
                if (vueApp.listData.length == 0) {
                    $.msg('담당자를 추가해 주시기 바랍니다.','','warning');
                    return false;
                }
                let bFlagErr = false;
                $.each(vueApp.listData, function (key, val) {
                    if (val.managerName == '' || val.managerAddr == '') {
                        bFlagErr = true;
                        return false;
                    }
                });
                if (bFlagErr === true) {
                    $.msg('담당자명, 주소는 필수입력값입니다.','','warning');
                    return false;
                }

                $.imsPost('setCustomerReceiver', {'list':vueApp.listData}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('저장 완료','','success').then(()=>{
                            vueApp.refreshList(1);
                            vueApp.isModify = false;
                        });
                    });
                });
            },
            /* 담당자 관련 함수들 end */

            //담당자 선택해서 분류패킹 등록(insert(delete -> insert))
            regist_delivery : ()=>{
                if (vueApp.aoPackingList.length > 0) {
                    $.msgConfirm('기존의 담당자별 분류패킹은 모두 삭제하시겠습니까?', '입력했던 수량이 모두 초기화됩니다.', '').then((confirmData)=> {
                        if (true === confirmData.isConfirmed) {
                            vueApp.regist_delivery_process();
                        }
                    });
                } else vueApp.regist_delivery_process();
            },
            regist_delivery_process : ()=>{
                let aReceiverSnos = [];
                $.each(document.getElementsByName('receiverSno[]'), function (key, val) {
                    if (this.checked === true) {
                        aReceiverSnos.push(this.value);
                    }
                });
                if (aReceiverSnos.length === 0) {
                    $.msg('담당자를 선택해 주시기 바랍니다.','','warning');
                    return false;
                }

                $.imsPost('registCustomerReceiverDelivery', {'packingSno':iPackingSno, 'list':aReceiverSnos}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        if (data != '') {
                            $.msg(data,'','error');
                            return false;
                        }

                        $.msg('저장 완료','','success').then(()=>{
                            document.getElementsByName('receiverSnoAllChk')[0].checked = false;
                            $.each(document.getElementsByName('receiverSno[]'), function (key, val) {
                                this.checked = false;
                            });

                            vueApp.bFlagReceiverFold=true;
                            vueApp.getDeliveryList();
                        });
                    });
                });
            },
            //담당자별 분류패킹 정보(입력수량, 제외된 아소트) 저장(update)
            modify_delivery : ()=>{
                $.imsPost('modifyCustomerReceiverDelivery', {'list':vueApp.aoPackingList, 'cnt_size_list':vueApp.ooCntSizeTotal}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('저장 완료','','success').then(()=>{
                            vueApp.getDeliveryList();
                        });
                    });
                });
            },
            //납품(발주)건 정보(스타일/아소트/사이즈 별 납품수량) 가져오기 + 담당자별 분류패킹 리스트 가져오기 + 입력수량 집계 - 페이지진입시 1번만 실행
            getPackingInfo : ()=>{
                ImsNkService.getList('packingInfo', {'packingSno':iPackingSno}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.iPackingSt = data.info_st;
                        if (vueApp.iPackingSt == 2) vueApp.$refs.textPackingSt.innerHTML = ' - 고객확정완료';
                        else if (vueApp.iPackingSt == 3) vueApp.$refs.textPackingSt.innerHTML = ' - 이노버확정완료';
                        else if (vueApp.iPackingSt == 4) vueApp.$refs.textPackingSt.innerHTML = ' - 입고완료';
                        vueApp.ooCntSizeTotal = data.info; //사이즈 별 납품수량
                        vueApp.ooCntSizeTotalUtil = data.info2; //개발보조용 데이터

                        //담당자별 분류패킹 리스트 가져오기 + 입력수량 집계
                        vueApp.getDeliveryList();
                    });
                });
            },
            //담당자별 분류패킹 리스트 가져오기
            getDeliveryList : ()=>{
                ImsNkService.getList('customerReceiverDelivery', {'packingSno':iPackingSno}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.aoPackingList = data;

                        let iCntComplete = 0;
                        $.each(vueApp.aoPackingList, function (key, val) {
                            if (Number(val.deliverySt) > 2) iCntComplete++;
                        });
                        if (vueApp.aoPackingList.length == 0) vueApp.bFlagReceiverFold = false;
                        if (vueApp.iPackingSt == 1 && vueApp.aoPackingList.length > 0) vueApp.$refs.textPackingSt.innerHTML = ' - '+vueApp.aoPackingList.length+'명 중 '+iCntComplete+'명 입력완료('+Math.floor(iCntComplete/vueApp.aoPackingList.length*100)+'%)';
                        //입력수량 집계
                        vueApp.allSumCntSize();
                    });
                });
            },
            //스타일/아소트/사이즈 별 입력수량 합산 -> 아소트 집계에 표시
            allSumCntSize : ()=>{
                //입력수량 초기화
                $.each(vueApp.ooCntSizeTotal, function(key, val) {
                    $.each(val, function(key2, val2) {
                        $.each(val2, function(key3, val3) {
                            vueApp.ooCntSizeTotal[key][key2][key3].currQty = 0;
                            vueApp.ooCntSizeTotal[key][key2][key3].flagOverQty = false;
                        });
                    });
                });
                let oTmp = {};
                $.each(vueApp.aoPackingList, function(key, val) {
                    $.each(val.jsonContents, function(key2, val2) {
                        $.each(val2.aAssortList, function(key3, val3) {
                            $.each(val3.oSizeList, function(key4, val4) {
                                oTmp = vueApp.ooCntSizeTotal[val2.styleSno][val3.assortType+'___'+val3.assortCharge][key4];

                                oTmp.currQty = Number(oTmp.currQty) + Number(val4.expectQty);
                                if (oTmp.currQty > oTmp.customerQty) {
                                    oTmp.flagOverQty = true;
                                } else {
                                    oTmp.flagOverQty = false;
                                }
                            });
                        });
                    });
                });
            },
            //입력수량 변경할때마다 해당사이즈의 입력수량 합산 -> 아소트 집계에 표시
            changeSumCntSize : (iStyleSno, sAssort, sSize)=>{
                let iSum = 0;
                $.each(vueApp.aoPackingList, function(key, val) {
                    $.each(val.jsonContents, function(key2, val2) {
                        if (val2.styleSno != iStyleSno) return true;
                        $.each(val2.aAssortList, function(key3, val3) {
                            if (val3.assortType+'___'+val3.assortCharge != sAssort) return true;
                            $.each(val3.oSizeList, function(key4, val4) {
                                if (key4 != sSize) return true;
                                iSum += Number(val4.expectQty);
                            });
                        });
                    });
                });

                vueApp.ooCntSizeTotal[iStyleSno][sAssort][sSize].currQty = iSum;
                if (vueApp.ooCntSizeTotal[iStyleSno][sAssort][sSize].currQty > vueApp.ooCntSizeTotal[iStyleSno][sAssort][sSize].customerQty) {
                    vueApp.ooCntSizeTotal[iStyleSno][sAssort][sSize].flagOverQty = true;
                } else {
                    vueApp.ooCntSizeTotal[iStyleSno][sAssort][sSize].flagOverQty = false;
                }
            },

            deleteAssort : (key, key2, key3)=>{
                vueApp.deleteElement(vueApp.aoPackingList[key].jsonContents[key2].aAssortList, key3);
                vueApp.aoPackingList[key].rowspan--;
                vueApp.aoPackingList[key].jsonContents[key2].rowspan--;
                //assort를 모두 제외시킨 style은 delete
                if (vueApp.aoPackingList[key].jsonContents[key2].aAssortList.length == 0) {
                    vueApp.deleteElement(vueApp.aoPackingList[key].jsonContents, key2);
                }

                //입력수량 집계
                vueApp.allSumCntSize();
            },
            //담당자에게 입력요청 하기(메일발송+알림톡)
            requestWrite : (iSno=0)=>{
                let aSnos = [];
                let sMsg = '입력요청 하시겠습니까?';
                let sSubMsg = '';
                if (iSno === 0) {
                    if (vueApp.aoPackingList.length === 0) {
                        $.msg('분류패킹 담당자가 없습니다.','','warning');
                        return false;
                    }
                    $.each(vueApp.aoPackingList, function (key, val) {
                        if (Number(val.sno) > 0) aSnos.push(Number(val.sno));
                    });
                    sMsg = '모든 담당자에게 입력요청 하시겠습니까?';
                    sSubMsg = '입력완료한 담당자에게도 입력요청합니다.';
                } else aSnos = [iSno];
                
                $.msgConfirm(sMsg, sSubMsg, '').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        $.imsPost('requestWriteReceiverDelivery', {'deliverySnos':aSnos, 'list':vueApp.aoPackingList, 'cnt_size_list':vueApp.ooCntSizeTotal}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                if (data == true) {
                                    $.msg('담당자에게 입력요청하였습니다.','','success').then(()=>{
                                        vueApp.getDeliveryList();
                                    });
                                } else {
                                    $.msg('요청정보가 올바르지 않습니다. 관리자에게 문의 바랍니다.','','warning');
                                }
                            });
                        });
                    }
                });
            },

            //분류패킹 확정하기(고객확정)
            confirm_delivery_packing : ()=>{
                //입력수량과 생산수량이 일치하는지 확인
                let bFlagOverQty = false;
                let sAssortName = '';
                $.each(vueApp.ooCntSizeTotal, function(key, val) {
                    $.each(val, function(key2, val2) {
                        $.each(val2, function(key3, val3) {
                            if (Number(val3.currQty) > Number(val3.customerQty)) {
                                sAssortName = ' ['+vueApp.ooCntSizeTotalUtil[key].styleName+' / '+key2.split('___')[0]+' / '+key3+']';
                                bFlagOverQty = true;
                                return false;
                            }
                        });
                        if (bFlagOverQty === true) return false;
                    });
                    if (bFlagOverQty === true) return false;
                });
                if (bFlagOverQty === true) {
                    $.msg('생산수량에 초과되게 입력한 수량이 있습니다.<br/>확인 바랍니다.'+sAssortName,'','warning');
                    return false;
                }

                $.msgConfirm('분류패킹을 확정하시겠습니까?', '확정하시면 수정이 불가능합니다.', '').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        $.imsPost('confirmReceiverDelivery', {'packingSno':iPackingSno, 'list':vueApp.aoPackingList, 'cnt_size_list':vueApp.ooCntSizeTotal}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                location.reload();
                            });
                        });
                    }
                });
            },
            //고객확정 취소 or 이노버확정 취소(IMS에서만 가능)
            cancel_customer_confirm : (iChgSt)=>{
                let sMsg = '';
                let sMsgSub = '';
                if (iChgSt == 1) {
                    sMsg = '고객확정을 취소하시겠습니까?';
                    sMsgSub = '고객확정은 고객사페이지에서만 가능합니다';
                } else if (iChgSt == 2) {
                    sMsg = '이노버확정을 취소하시겠습니까?';
                }
                $.msgConfirm(sMsg, sMsgSub, '').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        $.imsPost('modifySimpleDbCol', {'table_number':4, 'colNm':'packingSt', 'where':{'sno':iPackingSno}, 'data':iChgSt}).then((data)=>{
                            location.reload();
                        });
                    }
                });
            },
            //이노버 확정하기
            confirm_delivery_packing_ims : ()=>{
                $.msgConfirm('이노버확정하시겠습니까?', '', '').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        $.imsPost('modifyCustomerReceiverDelivery', {'list':vueApp.aoPackingList, 'cnt_size_list':vueApp.ooCntSizeTotal}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                $.imsPost('modifySimpleDbCol', {'table_number':4, 'colNm':'packingSt', 'where':{'sno':iPackingSno}, 'data':3}).then((data)=>{
                                    location.reload();
                                });
                            });
                        });
                    }
                });
            },
            //엑셀 업로드
            listUpload : ()=>{
                let aSnos = [];
                $.each(document.getElementsByName('deliverySno[]'), function (key, val) {
                    if (this.checked === true) aSnos.push(this.value);
                });
                if (aSnos.length === 0) {
                    $.msg('담당자(배송지점)을 선택하세요','','error');
                    return false;
                }
                if (document.getElementsByName('delivery_company')[0].value == '') {
                    $.msg('배송회사를 선택하세요','','error');
                    return false;
                }
                if (document.getElementsByName('excel')[0].value == '') {
                    $.msg('업로드할 엑셀파일을 첨부하세요','','error');
                    return false;
                }

                document.getElementsByName('delivery_receivers')[0].value = aSnos.join(', ');
                document.getElementsByName('packing_sno')[0].value = iPackingSno;
                document.getElementsByName('frmRegistTrackingNum')[0].submit();
            },
            //운송장번호 개별update
            update_delivery_info : (iKey)=>{
                if (vueApp.$refs.update_delivery_company[iKey].value == 'null') {
                    $.msg('배송회사를 선택하세요','','error');
                    return false;
                }

                let aDeliveryNums = [];
                let sDeliveryNums = '';
                $.each(vueApp.$refs.update_delivery_nums[iKey].value.split('\n'), function(key, val) {
                    if (val != '') aDeliveryNums.push(val);
                });
                if (aDeliveryNums.length > 0) sDeliveryNums = aDeliveryNums.join(', ');
                vueApp.aoPackingList[iKey].invoiceNums = sDeliveryNums;
                $.imsPost('modifyDeliveryInfo', {'info':vueApp.aoPackingList[iKey]}).then((data)=>{
                    location.reload();
                });
            },
            //
            listDownload : ()=>{
                location.href='ims_pop_check_customer_delivery.php?excel_download=1&packing_sno='+iPackingSno;
            },
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            //스타일/아소트/사이즈 별 납품수량 가져오기
            vueApp.getPackingInfo();

            <?php if (isset($bFlagOpenIms) && $bFlagOpenIms === true) { ?>
            $('.bootstrap-filestyle')[0].remove();
            <?php } ?>
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //style , storedSearchCondition
        listService.init(serviceData);
    });

</script>

<?php } ?>
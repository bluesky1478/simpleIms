<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
    .sl-blue{ color:#2b50f0!important; }
    .table-expected-sales input { padding:2px; }
</style>

<section id="imsApp">
    <div class="page-header js-affix mgb0">
        <h3>{% salesCustomerInfo.customerName %} 업체정보 <span v-show="isModifyCustomer" class="text-danger font-13">* : 필수입력</span></h3>
        <div class="btn-group">
            <input type="button" @click="isModifyCustomer=true;" v-show="!isModifyCustomer" value="수정" class="btn btn-red btn-red-line2" />
            <input type="button" @click="saveSalesCustomer()" v-show="isModifyCustomer" value="저장" class="btn btn-red" />
            <input type="button" @click="isModifyCustomer=false;" v-show="isModifyCustomer" value="취소" class="btn btn-white" />
            <input type="button" @click="bFlagShowSCInfo=false" v-show="bFlagShowSCInfo" value="접기" class="btn btn-white" />
            <input type="button" @click="bFlagShowSCInfo=true" v-show="!bFlagShowSCInfo" value="펼치기" class="btn btn-white" />
            <input type="button" @click="self.close()" value="닫기" class="btn btn-white" />
        </div>
    </div>

    <table v-show="bFlagShowSCInfo" class="table table-cols table-td-height30 table-th-height30 mgt0 border-top-none"> <!--table-pd-5-->
        <colgroup>
            <col class="w-13p">
            <col class="w-38p">
            <col class="w-14p">
            <col class="w-39p">
        </colgroup>
        <tr>
            <th>업체명 <span v-show="isModifyCustomer" class="text-danger font-13">*</span></th>
            <td>
                <?php $model='salesCustomerInfo.customerName'; $placeholder='업체명'; $modifyKey='isModifyCustomer';  ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <th>담당자 컨텍 경로 <span v-show="isModifyCustomer" class="text-danger font-13">*</span></th>
            <td>
                <div v-show="isModifyCustomer">
                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_CONTACT_TYPE as $key => $val ) { ?>
                        <label class="radio-inline">
                            <input type="radio" v-model="salesCustomerInfo.contactType" @change="changeContactType(2)" value="<?=$key?>" name="sRadioUpsertContactType2" style="display: inline;" /><?=$val?>
                        </label>
                    <?php } ?>
                    <div class="mgt5">
                        <label class="radio-inline">
                            <input type="radio" v-model="salesCustomerInfo.contactType" @change="changeContactType(2)" value="" name="sRadioUpsertContactType2" style="display: inline;" />기타
                        </label>

                        <input type="text" ref="salesCustomerInfoContactType2"
                               name="salesCustomerInfoContactType2" v-show="'' == salesCustomerInfo.contactType"
                               v-model="salesCustomerInfo.contactTypeEtc" class="form-control w-70p" placeholder="기타 컨택 경로" />
                    </div>

                </div>
                <div v-show="!isModifyCustomer" >
                    {% salesCustomerInfo.contactTypeEtc %}
                </div>
            </td>
        </tr>
        <tr>
            <th>업종 <span v-show="isModifyCustomer" class="text-danger font-13">*</span></th>
            <td>
                <div v-show="isModifyCustomer" class="dp-flex">
                    <select v-model="salesCustomerInfo.parentBusiCateSno" @change="salesCustomerInfo.busiCateSno = 0;" class="form-control" style="display:inline; width:150px;">
                        <option value="0">선택</option>
                        <option v-for="(val, key) in oParentCateList" :value="key">{% val %}</option>
                    </select> >
                    <select v-model="salesCustomerInfo.busiCateSno" class="form-control" style="display:inline; width:150px">
                        <option v-for="(val, key) in oCateList[salesCustomerInfo.parentBusiCateSno]" :value="key">{% val %}</option>
                    </select>
                </div>
                <div v-show="!isModifyCustomer" >
                    {% salesCustomerInfo.parentCateName %} > {% salesCustomerInfo.cateName %}
                </div>
            </td>
            <th>ㄴ담당자명</th>
            <td>
                <?php $model='salesCustomerInfo.contactName'; $placeholder='담당자명'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
        </tr>
        <tr>
            <th>고객사 이니셜</th>
            <td>
                <?php $model='salesCustomerInfo.styleCode'; $placeholder='고객사 이니셜' ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <th>ㄴ부서명</th>
            <td>
                <?php $model='salesCustomerInfo.dept'; $placeholder='부서명' ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
        </tr>
        <tr>
            <th>고객 구분</th>
            <td>
                {% salesCustomerInfo.customerTypeHan %} <span v-show="isModifyCustomer" class="text-danger">(자동변경)</span>
            </td>
            <th>ㄴ담당자 연락처</th>
            <td>
                <?php $model='salesCustomerInfo.contactPhone'; $placeholder='담당자 연락처' ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
        </tr>
        <tr>
            <th>사원수</th>
            <td>
                <?php $model='salesCustomerInfo.employeeCnt'; $placeholder='사원수' ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
            </td>
            <th>ㄴ담당자 이메일</th>
            <td>
                <?php $model='salesCustomerInfo.contactEmail'; $placeholder='담당자 이메일' ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
        </tr>
        <tr>
            <th>구매 방식 <span v-show="isModifyCustomer" class="text-danger font-13">*</span></th>
            <td>
                <div v-show="isModifyCustomer">
                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_BUY_METHOD as $key => $val ) { ?>
                        <label class="radio-inline">
                            <input type="radio" v-model="salesCustomerInfo.buyMethod" value="<?=$key?>" name="sRadioUpsertBuyMethod2" /><?=$val?>
                        </label>
                    <?php } ?>
                </div>
                <div v-show="!isModifyCustomer" >
                    {% salesCustomerInfo.buyMethod %}
                </div>
            </td>
            <th>유니폼 종류</th>
            <td>
                <div v-show="isModifyCustomer">
                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_BUY_DIV as $key => $val ) { ?>
                        <label class="radio-inline">
                            <input type="radio" v-model="salesCustomerInfo.buyDiv" value="<?=$key?>" name="sRadioUpsertBuyDiv" /><?=$val?>
                        </label>
                    <?php } ?>
                </div>
                <div v-show="!isModifyCustomer" >
                    {% salesCustomerInfo.buyDiv %}
                </div>
            </td>
        </tr>
        <tr>
            <th>영업담당자</th>
            <td>
                <div v-show="isModifyCustomer">
                    <select2 class="js-example-basic-single" v-model="salesCustomerInfo.salesManagerSno" style="width:100%;">
                        <?php foreach ($salesManagerList as $key => $val ) { ?>
                            <option value="<?=$key?>"><?=$val?></option>
                        <?php } ?>
                    </select2>
                </div>
                <div v-show="!isModifyCustomer" >
                    {% salesCustomerInfo.salesManagerName %}
                </div>
            </td>
            <th>대표번호</th>
            <td>
                <?php $model='salesCustomerInfo.phone'; $placeholder='대표번호' ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
        </tr>
        <tr>
            <th>후속 연락 일정</th>
            <td>{% salesContentsList.length > 0 ? (salesContentsList[salesContentsList.length-1].afterCallDt==null||salesContentsList[salesContentsList.length-1].afterCallDt==''?'없음':salesContentsList[salesContentsList.length-1].afterCallDt) : '등록된 이력이 없습니다' %}</td>
            <th>후속 연락 사유</th>
            <td>{% salesContentsList.length > 0 ? salesContentsList[salesContentsList.length-1].afterCallReasonHan : '등록된 이력이 없습니다' %}</td>
        </tr>
        <tr>
            <th>
                추정 매출액
            </th>
            <td colspan="3">

                <div v-show="isModifyCustomer">
                    <label class="radio-inline">
                        <input type="radio" v-model="sDirectExpectSumYn" @change="if (sDirectExpectSumYn == 'y') {salesCustomerInfo.totalExpectSales = ''; salesCustomerInfo.jsonExpectSales = [];}" value="y" name="sRadioDirectExpectSumYn" style="display: inline;" />직접입력
                    </label>
                    <label class="radio-inline">
                        <input type="radio" v-model="sDirectExpectSumYn" @change="if (sDirectExpectSumYn == 'y') {salesCustomerInfo.totalExpectSales = ''; salesCustomerInfo.jsonExpectSales = [];}" value="n" name="sRadioDirectExpectSumYn" style="display: inline;" />스타일 등록
                    </label>
                </div>

                <div class="mgt10 mgb3 dp-flex">
                    <span v-if="!isModifyCustomer || sDirectExpectSumYn == 'n'">
			            합계 : {% isNaN(salesCustomerInfo.totalExpectSales) ? salesCustomerInfo.totalExpectSales : $.setNumberFormat(salesCustomerInfo.totalExpectSales) %}
                    </span>
                    <span v-else>
			            <input type="text" v-model="salesCustomerInfo.totalExpectSales" class="form-control w-100px" style="display: inline;" />원
                    </span>

                    <div v-show="isModifyCustomer && 'y' !== sDirectExpectSumYn" class="mgl20">
                        <button type="button" class="btn btn-white btn-sm"
                                @click="addElement(salesCustomerInfo.jsonExpectSales, ooDefaultJson.jsonExpectSales, 'after')">+ 스타일추가</button>
                    </div>

                </div>

                <table v-show="sDirectExpectSumYn=='n'" class="table-expected-sales table table-cols table-default-center table-pd-3 table-td-height30 table-th-height30 mgb0">
                    <colgroup>
                        <col class="w-10p">
                        <col class="w-15p">
                        <col class="">
                        <col class="w-10p">
                        <col class="w-14p">
                        <col class="w-10p">
                        <col class="w-18p">
                    </colgroup>
                    <tr>
                        <th>시즌</th>
                        <th>스타일</th>
                        <th>품목명</th>
                        <th>구매 수량</th>
                        <th>현재 구매단가</th>
                        <th>추정 매출</th>
                        <th>기능 <button type="button" v-show="isModifyCustomer" class="btn btn-white btn-sm" @click="addElement(salesCustomerInfo.jsonExpectSales, ooDefaultJson.jsonExpectSales, 'after')">+ 추가</button></th>
                    </tr>
                    <tbody is="draggable" :list="salesCustomerInfo.jsonExpectSales"  :animation="200" tag="tbody" handle=".handle">
                    <tr v-for="(val, key) in salesCustomerInfo.jsonExpectSales">
                        <td>
                            <span v-show="isModifyCustomer">
                                <select v-model="val.prdSeason" class="form-control">
                                    <option value="">선택</option>
                                <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                    <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                                <?php } ?>
                                </select>
                            </span>
                            <span v-show="!isModifyCustomer">
                                {% seasonMap[val.prdSeason] %}
                            </span>
                        </td>
                        <td>
                            <span v-show="isModifyCustomer">
                                <select v-model="val.prdStyle" class="form-control">
                                    <option value="">선택</option>
                                <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                    <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                <?php } ?>
                                </select>
                            </span>
                            <span v-show="!isModifyCustomer">
                                {% styleMap[val.prdStyle] %}
                            </span>
                        </td>
                        <td>
                            <?php $model="val.productName"; $placeholder='품목명'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                        <td>
                            <?php $model="val.saleQty"; $placeholder='구매수량'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <td>
                            <?php $model="val.unitPrice"; $placeholder='현재구매단가'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                        <td class="ta-r">
                            {% $.setNumberFormat(Number(val.saleQty) * Number(val.unitPrice)) %} 원
                        </td>
                        <td>
                            <div v-if="isModifyCustomer">
                                <button type="button" class="btn btn-white btn-sm" @click="addElement(salesCustomerInfo.jsonExpectSales, ooDefaultJson.jsonExpectSales, 'down', key)">+ 추가</button>
                                <div class="btn btn-sm btn-red" @click="deleteElement(salesCustomerInfo.jsonExpectSales, key)">- 삭제</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="ta-c">합계</td>
                        <td colspan="4" class="ta-r">{% $.setNumberFormat(computed_total_expect_sales) %} 원</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="page-header js-affix mgb0" >
        <h3>{% salesCustomerInfo.customerName %} 영업활동이력</h3>
        <input type="button" @click="openUpsertContentsModal(0);" value="활동이력 등록" class="btn btn-red-line" style="margin-right: 80px;" />
        <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
    </div>
    <table class="table table-rows ch-table table-td-height30 border-top-none">
        <colgroup>
            <col class="w-6p" /><!--일자-->
            <col class="w-6p" /><!--담당자-->
            <col class="w-6p" /><!--활동구분-->
            <col class="" /><!--내용-->
            <col class="w-6p" /><!--시간-->
            <col class="w-10p" /><!--사유-->
            <col class="w-10p" /><!--일정-->
            <col class="w-10p" /><!--타입-->
            <col class="w-5p" /><!--수정-->
        </colgroup>
        <tr>
            <th>영업 일자</th>
            <th>영업<br>담당자</th>
            <th>영업<br>활동 구분</th>
            <th>영업 내용</th>
            <th>업무 시간(분)</th>
            <th>후속 연락 사유</th>
            <th>후속 연락 일정</th>
            <th>제안서 타입</th>
            <th>수정</th>
        </tr>
        <tr v-if="0 >= salesContentsList.length">
            <td colspan="9">데이터가 없습니다.</td>
        </tr>
        <tr v-for="(val, key) in salesContentsList">
            <td class="font-11">{% $.formatShortDate(val.regDt) %}</td>
            <td class="font-11">{% val.regManagerName %}</td>
            <td class="font-11">{% val.contentsTypeHan %}</td>
            <td class="ta-l" style="padding-left:10px;" >
                <div v-html="$.nl2br(val.contents)"></div>
            </td>
            <td>{% val.contentsMinute %}</td>
            <td>{% val.afterCallReasonHan %}</td>
            <td>{% $.formatShortDate(val.afterCallDt) %}</td>
            <td>{% val.proposalType %}</td>
            <td><span @click="openUpsertContentsModal(val.sno);" class="btn btn-white btn-sm">수정</span></td>
        </tr>
    </table>
    <div class="modal fade" id="modalUpsertContents" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="top:0px; width:calc(100vw - 80px);">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    영업활동이력 {% oUpsertForm.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %} <span v-show="isModify" class="text-danger font-13">* : 필수입력</span>
                </span>
                </div>
                <div class="modal-body">
                    <div class="font-18 bold">영업활동이력 {% oUpsertForm.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}</div>
                    <table class="table table-cols table-pd-5">
                        <colgroup>
                            <col class="w-20p">
                            <col>
                        </colgroup>
                        <tr>
                            <th>영업활동 구분 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <div v-show="isModify">
                                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_CONTENTS_TYPE as $key => $val ) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="oUpsertForm.contentsType" value="<?=$key?>" name="sRadioUpsertContentsType" /><?=$val?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="!isModify" >
                                    {% oUpsertForm.contentsTypeHan %}
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>소요시간(분)</th>
                            <td>
                                <span v-if="isModify">
                                    <input type="number" v-model="oUpsertForm.contentsMinute" placeholder="소요시간(분)" class="form-control w-70px" style="display: inline;" />
                                    <span v-show="oUpsertForm.sno == 0" class="text-danger">* EM은 수동입력, TM은 자동계산(등록버튼클릭 ~ 저장버튼클릭)</span>
                                </span>
                                <span v-else>
                                    {% $.setNumberFormat(oUpsertForm.contentsMinute) %}분
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>영업 내용 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <?php $model='oUpsertForm.contents'; $placeholder='영업 내용'; $textareaRows=4; $modifyKey='isModify'; ?>
                                <?php include './admin/ims/template/basic_view/_textarea.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>후속 연락 사유</th>
                            <td>
                                <div v-show="isModify">
                                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_CONTENTS_AFTER_CALL_REASON as $key => $val ) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="oUpsertForm.afterCallReason" value="<?=$key?>" name="sRadioUpsertAfterCallReason" /><?=$val?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="!isModify" >
                                    {% oUpsertForm.afterCallReasonHan %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>후속 연락 일정</th>
                            <td>
                                <?php $model='oUpsertForm.afterCallDt'; $placeholder='후속 연락 일정' ?>
                                <?php include './admin/ims/template/basic_view/_picker.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>제안서 타입</th>
                            <td>
                                <?php $model='oUpsertForm.proposalType'; $placeholder='제안서 타입' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                    </table>

                    <div class="font-18 bold">업체정보 {% isModify ? '수정' : '' %}</div>
                    <table class="table table-cols table-pd-5">
                        <colgroup>
                            <col class="w-20p">
                            <col>
                        </colgroup>
                        <tr>
                            <th>담당자 컨텍 경로 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <div v-show="isModify">
                                    <div>
                                        <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_CONTACT_TYPE as $key => $val ) { ?>
                                            <label class="radio-inline">
                                                <input type="radio" v-model="salesCustomerInfo.contactType" @change="changeContactType()" value="<?=$key?>" name="sRadioUpsertContactType" style="display: inline;" /><?=$val?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="salesCustomerInfo.contactType" @change="changeContactType()" value="" name="sRadioUpsertContactType" style="display: inline;" />기타
                                        </label>

                                        <input type="text" v-show="'' == salesCustomerInfo.contactType"
                                               ref="salesCustomerInfoContactType"
                                               v-model="salesCustomerInfo.contactTypeEtc" class="form-control w-100px" />
                                    </div>
                                </div>
                                <div v-show="!isModify" >
                                    {% salesCustomerInfo.contactType %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>ㄴ담당자명</th>
                            <td>
                                <?php $model='salesCustomerInfo.contactName'; $placeholder='담당자명'; ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>ㄴ부서명</th>
                            <td>
                                <?php $model='salesCustomerInfo.dept'; $placeholder='부서명' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>ㄴ담당자 연락처</th>
                            <td>
                                <?php $model='salesCustomerInfo.contactPhone'; $placeholder='담당자 연락처' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>ㄴ담당자 이메일</th>
                            <td>
                                <?php $model='salesCustomerInfo.contactEmail'; $placeholder='담당자 이메일' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>


                        <tr>
                            <th>입찰 예정일</th>
                            <td>
                                <?php $model='salesCustomerInfo.bidDt'; $placeholder='입찰 예정일' ?>
                                <?php include './admin/ims/template/basic_view/_picker.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>미팅 희망일</th>
                            <td>
                                <?php $model='salesCustomerInfo.meetingDt'; $placeholder='미팅 희망일' ?>
                                <?php include './admin/ims/template/basic_view/_picker.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>구매 방식 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <div v-show="isModify">
                                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_BUY_METHOD as $key => $val ) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="salesCustomerInfo.buyMethod" value="<?=$key?>" name="sRadioUpsertBuyMethod" /><?=$val?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="!isModify" >
                                    {% salesCustomerInfo.buyMethod %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>입찰 주기</th>
                            <td>
                                <div v-show="isModify">
                                    <select v-model="salesCustomerInfo.bidCntYear" class="form-control" >
                                        <?php for($i = 0; $i < 10; $i++) { ?>
                                            <option value="<?=$i?>"><?=$i?> 년</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div v-show="!isModify" >
                                    {% salesCustomerInfo.bidCntYear %} 년
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>마지막 입찰 년도</th>
                            <td>
                                <?php $model='salesCustomerInfo.lastBidYear'; $placeholder='마지막 입찰 년도' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>현재 계약 업체</th>
                            <td>
                                <?php $model='salesCustomerInfo.currContractCompany'; $placeholder='현재 계약 업체' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                추정 매출
                                <button type="button" v-show="isModify && sDirectExpectSumYn == 'n'" class="btn btn-white btn-sm"
                                        @click="addElement(salesCustomerInfo.jsonExpectSales, ooDefaultJson.jsonExpectSales, 'after')">+ 추가
                                </button>
                            </th>
                            <td>

                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" v-model="sDirectExpectSumYn"
                                               @change="if (sDirectExpectSumYn == 'y') {salesCustomerInfo.totalExpectSales = ''; salesCustomerInfo.jsonExpectSales = [];}" value="y" name="sRadioDirectExpectSumYn2"  />직접입력
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" v-model="sDirectExpectSumYn"
                                               @change="if (sDirectExpectSumYn == 'y') {salesCustomerInfo.totalExpectSales = ''; salesCustomerInfo.jsonExpectSales = [];}" value="n" name="sRadioDirectExpectSumYn2" />스타일 합산
                                    </label>
                                </div>

                                <div>
                                    <span v-if="sDirectExpectSumYn == 'n'">
					                    합계 : {% isNaN(salesCustomerInfo.totalExpectSales) ? salesCustomerInfo.totalExpectSales : $.setNumberFormat(salesCustomerInfo.totalExpectSales) %}
                                    </span>
                                    <span v-else>
					                    <input type="text" v-model="salesCustomerInfo.totalExpectSales" class="form-control w-100px" style="display: inline;" />원
                                    </span>

                                    <table v-show="sDirectExpectSumYn=='n'" class="table-expected-sales table table-cols table-default-center table-pd-3 table-td-height30 table-th-height30 mgb0">
                                        <colgroup>
                                            <col class="w-12p">
                                            <col class="w-7p">
                                            <col class="w-7p">
                                            <col class="w-10p">
                                            <col class="w-14p">
                                            <col class="">
                                            <col class="w-18p">
                                        </colgroup>
                                        <tr>
                                            <th>품목</th>
                                            <th>시즌</th>
                                            <th>스타일</th>
                                            <th>구매 수량</th>
                                            <th>현재 구매단가</th>
                                            <th>추정 매출</th>
                                            <th>기능 <button type="button" v-show="isModify" class="btn btn-white btn-sm" @click="addElement(salesCustomerInfo.jsonExpectSales, ooDefaultJson.jsonExpectSales, 'after')">+ 추가</button></th>
                                        </tr>
                                        <tbody is="draggable" :list="salesCustomerInfo.jsonExpectSales"  :animation="200" tag="tbody" handle=".handle">
                                        <tr v-for="(val, key) in salesCustomerInfo.jsonExpectSales">
                                            <td>
                                                <?php $model="val.productName"; $placeholder='품목'; ?>
                                                <?php include './admin/ims/template/basic_view/_text.php'?>
                                            </td>
                                            <td>
                                                <select v-model="val.prdSeason" class="form-control">
                                                    <option value="">선택</option>
                                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                                        <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select v-model="val.prdStyle" class="form-control">
                                                    <option value="">선택</option>
                                                    <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php $model="val.saleQty"; $placeholder='구매수량'; ?>
                                                <?php include './admin/ims/template/basic_view/_number.php'?>
                                            </td>
                                            <td>
                                                <?php $model="val.unitPrice"; $placeholder='현재구매단가'; ?>
                                                <?php include './admin/ims/template/basic_view/_number.php'?>
                                            </td>
                                            <td>{% $.setNumberFormat(Number(val.saleQty) * Number(val.unitPrice)) %} 원</td>
                                            <td>
                                                <div v-if="isModify">
                                                    <button type="button" class="btn btn-white btn-sm" @click="addElement(salesCustomerInfo.jsonExpectSales, ooDefaultJson.jsonExpectSales, 'down', key)">+ 추가</button>
                                                    <div class="btn btn-sm btn-red" @click="deleteElement(salesCustomerInfo.jsonExpectSales, key)">- 삭제</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="ta-c">합계</td>
                                            <td colspan="4" class="ta-r">{% $.setNumberFormat(computed_total_expect_sales) %} 원</td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>

                            </td>
                        </tr>

                        <tr>
                            <th>고객 니즈</th>
                            <td>
                                <div v-show="isModify">
                                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_CUSTOMER_NEEDS as $key => $val ) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="salesCustomerInfo.customerNeeds" value="<?=$key?>" name="sRadioUpsertCustomerNeeds" /><?=$val?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="!isModify" >
                                    {% salesCustomerInfo.customerNeeds %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>폐쇄몰 관심도</th>
                            <td>
                                <div v-show="isModify">
                                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_MALL_INTEREST as $key => $val ) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="salesCustomerInfo.mallInterest" value="<?=$key?>" name="sRadioUpsertMallInterest" /><?=$val?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="!isModify" >
                                    {% salesCustomerInfo.mallInterest %}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer ">
                    <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="save()">저장</div>
                    <div class="btn btn-gray btn-lg" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    const styleMap = {};
    <?php foreach($codeStyle as $codeKey => $code){ ?>
    styleMap['<?=$codeKey?>'] = '<?=$code?>';
    <?php } ?>
    const seasonMap = {};
    <?php foreach($seasonList as $codeKey => $code){ ?>
    seasonMap['<?=$codeKey?>'] = '<?=$code?>';
    <?php } ?>

    $(()=>{
        ImsNkService.getList('salesCustomerContents', { sno:<?=$iSno?>}).then((data)=>{
            $.imsPostAfter(data, (data)=> {
                //값이 기타일때 기타 체크 and textbox show
                let bFlagEtc = true;
                $.each(document.getElementsByName('sRadioUpsertContactType2'), function(key, val) {
                    if (data.info.contactType == this.value) {
                        bFlagEtc = false;
                        return false;
                    }
                });

                data.info.contactTypeEtc = $.copyObject(data.info.contactType);

                const initParams = {
                    data: {
                        isModify : true,
                        isModifyCustomer : false,
                        bFlagShowSCInfo : true,
                        sDirectExpectSumYn : data.info.jsonExpectSales.length > 0 ? 'n' : 'y',
                        oInsertClickDt : {},
                        salesCustomerInfo : data.info,
                        salesContentsList : data.list,
                        oUpsertForm : {'sno':0, 'salesSno':<?=$iSno?>, 'contentsType':'1', 'contentsMinute':0, 'afterCallReason':'1', 'afterCallDt':'', 'contents':'', 'proposalType':'', },
                        oParentCateList : {},
                        oCateList : {},
                        ooDefaultJson :{
                            <?php foreach ($aDefaultJson as $key => $val) { ?>
                            '<?=$key?>':{
                                <?php foreach ($val as $key2 => $val2) { ?>
                                '<?=$key2?>':'<?=$val2?>',
                                <?php } ?>
                            },
                            <?php } ?>
                        },
                    },
                    computed : {
                        computed_total_expect_sales : ()=>{
                            if (initParams.data.sDirectExpectSumYn == 'n') {
                                data.info.totalExpectSales = 0;
                                let iSum = 0;
                                $.each(data.info.jsonExpectSales, function (key, val) {
                                    iSum += Number(val.saleQty) * Number(val.unitPrice);
                                });
                                data.info.totalExpectSales = iSum;
                                return data.info.totalExpectSales;
                            }
                        },
                    },
                    mounted : () => {
                        if ("<?=$sPageMode?>" === "regist") {
                            vueApp.openUpsertContentsModal(0);
                        }
                        $.imsPost('getBusiCateListByDepth', {}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                vueApp.oParentCateList = data.parent_cate_list;
                                vueApp.oCateList = data.cate_list;
                            });
                        });
                    },
                    methods : {
                        openUpsertContentsModal : (iSno)=>{
                            vueApp.oUpsertForm.sno = iSno;
                            if (iSno > 0) {
                                $.each(vueApp.salesContentsList, function (key, val) {
                                    if (this.sno == iSno) {
                                        vueApp.oUpsertForm.contentsType = this.contentsType;
                                        vueApp.oUpsertForm.contentsMinute = this.contentsMinute;
                                        vueApp.oUpsertForm.afterCallReason = this.afterCallReason;
                                        vueApp.oUpsertForm.afterCallDt = this.afterCallDt;
                                        vueApp.oUpsertForm.contents = this.contents;
                                        vueApp.oUpsertForm.proposalType = this.proposalType;
                                        return false;
                                    }
                                });
                            } else {
                                vueApp.oUpsertForm.contentsType = '1';
                                vueApp.oUpsertForm.contentsMinute = '0';
                                vueApp.oUpsertForm.afterCallReason = '1';
                                vueApp.oUpsertForm.afterCallDt = '';
                                vueApp.oUpsertForm.contents = '';
                                vueApp.oUpsertForm.proposalType = '';
                            }
                            //구분이 기타일때
                            vueApp.salesCustomerInfo.contactTypeEtc = vueApp.salesCustomerInfo.contactType;
                            let bFlagEtc = true;
                            $.each(document.getElementsByName('sRadioUpsertContactType'), function(key, val) {
                                if (vueApp.salesCustomerInfo.contactType == this.value) {
                                    bFlagEtc = false;
                                    return false;
                                }
                            });

                            //등록/수정 클릭시 현재시간 변수에 담기 -> 저장시 시간계산
                            vueApp.oInsertClickDt = new Date();

                            $('#modalUpsertContents').modal('show');
                        },

                        changeContactType : (sAppendStr='')=> { //담당자 컨텍 경로 바꿨을때 기타textbox
                            vueApp.salesCustomerInfo.contactTypeEtc = vueApp.salesCustomerInfo.contactType;
                            if (vueApp.salesCustomerInfo.contactType=='') {
                                vueApp.$refs['salesCustomerInfoContactType'+sAppendStr].style.display='inline';
                                vueApp.$refs['salesCustomerInfoContactType'+sAppendStr].focus();
                            } else {
                                vueApp.$refs['salesCustomerInfoContactType'+sAppendStr].style.display='none';
                            }
                        },
                        save : ()=> {
                            if (vueApp.salesCustomerInfo.contactTypeEtc == null || vueApp.salesCustomerInfo.contactTypeEtc == '') {
                                $.msg('담당자 컨텍 경로를 선택/입력하세요','','error');
                                return false;
                            }
                            if (vueApp.oUpsertForm.contents == null || vueApp.oUpsertForm.contents == '') {
                                $.msg('영업내용을 입력하세요','','error');
                                return false;
                            }
                            if (vueApp.salesCustomerInfo.buyMethod == null || vueApp.salesCustomerInfo.buyMethod == '') {
                                $.msg('구매 방식을 선택하세요','','error');
                                return false;
                            }

                            if (vueApp.salesCustomerInfo.jsonExpectSales.length > 0) {
                                let bFlagErr = false;
                                $.each(vueApp.salesCustomerInfo.jsonExpectSales, function(key, val) {
                                    $.each(this, function(key2, val2) {
                                        if (val2 == '' || val2 == 0) {
                                            bFlagErr = true;
                                            return false;
                                        }
                                    });
                                    if (bFlagErr === true) return false;
                                });
                                if (bFlagErr === true) {
                                    $.msg('추정매출에 값을 모두 입력하세요','','error');
                                    return false;
                                }
                            } else vueApp.salesCustomerInfo.jsonExpectSales = [];

                            //등록일때 and 영업활동 구분이 TM 일때만 소요시간 자동계산
                            if (vueApp.oUpsertForm.sno == 0 && vueApp.oUpsertForm.contentsType == 1) {
                                vueApp.oUpsertForm.contentsMinute = Math.ceil((new Date().getTime() - vueApp.oInsertClickDt.getTime()) / 60000);
                            }
                            vueApp.salesCustomerInfo.contactType = vueApp.salesCustomerInfo.contactTypeEtc;
                            let oSendParam = $.copyObject(vueApp.salesCustomerInfo);
                            if (oSendParam.jsonExpectSales.length == 0) oSendParam.jsonExpectSales = '[]';
                            $.imsPost('setSalesCustomerContents', {'customer_data':oSendParam, 'contents_data':vueApp.oUpsertForm}).then((data)=>{
                                $.imsPostAfter(data,(data)=>{
                                    ImsNkService.getList('salesCustomerContents', { sno:<?=$iSno?> }).then((data)=>{
                                        $.imsPostAfter(data, (data)=> {
                                            vueApp.salesCustomerInfo = data.info;
                                            vueApp.salesCustomerInfo.contactTypeEtc = vueApp.salesCustomerInfo.contactType;
                                            vueApp.salesContentsList = data.list;
                                        });
                                    });
                                    parent.opener.refreshList();
                                    $('#modalUpsertContents').modal('hide');
                                });
                            });

                        },
                        //업체정보 수정
                        saveSalesCustomer : ()=> {
                            if (vueApp.salesCustomerInfo.customerName == null || vueApp.salesCustomerInfo.customerName == '') {
                                $.msg('업체명을 입력하세요','','error');
                                return false;
                            }
                            if (vueApp.salesCustomerInfo.contactTypeEtc == null || vueApp.salesCustomerInfo.contactTypeEtc == '') {
                                $.msg('담당자 컨텍 경로를 선택/입력하세요','','error');
                                return false;
                            }
                            if (vueApp.salesCustomerInfo.busiCateSno === null || vueApp.salesCustomerInfo.busiCateSno == '0') {
                                $.msg('세부업종명을 선택하세요','','error');
                                return false;
                            }
                            if (vueApp.salesCustomerInfo.jsonExpectSales.length > 0) {
                                let bFlagErr = false;
                                $.each(vueApp.salesCustomerInfo.jsonExpectSales, function(key, val) {
                                    $.each(this, function(key2, val2) {
                                        if (val2 == '' || val2 == 0) {
                                            bFlagErr = true;
                                            return false;
                                        }
                                    });
                                    if (bFlagErr === true) return false;
                                });
                                if (bFlagErr === true) {
                                    $.msg('추정매출에 값을 모두 입력하세요','','error');
                                    return false;
                                }
                            } else vueApp.salesCustomerInfo.jsonExpectSales = [];

                            vueApp.salesCustomerInfo.contactType = vueApp.salesCustomerInfo.contactTypeEtc;
                            let oSendParam = $.copyObject(vueApp.salesCustomerInfo);
                            if (oSendParam.jsonExpectSales.length == 0) oSendParam.jsonExpectSales = '[]';
                            $.imsPost('setFindCustomer', {'data':oSendParam }).then((data)=>{
                                $.imsPostAfter(data,(data)=>{
                                    location.reload();
                                });
                            });
                        },


                    },
                }
                vueApp = ImsService.initVueApp(appId, initParams);
            });
        });
    });
</script>
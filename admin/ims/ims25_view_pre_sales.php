<?php use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
$modelPrefix='pre_sales';  ?>

<!--업무 스케쥴-->
<div class="col-xs-12" v-if="customer.sno > 0">
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                영업 기본 정보
                <!--<span class="font-11 normal">TODO : 고객 상태 (신규/재계약에 따라 영업 정보나 기획 정보 다르게 나와야한다)</span>-->
            </div>
            <div class="flo-right">
                <!--
                <div class="btn btn-sm btn-white " @click="openSalesView(project.sno)">영업기획서</div>
                -->
                <button type="button" class="btn btn-red-line2 btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30 table-pd-4">
                <colgroup>
                    <col class="w-15p">
                    <col class="w-35p">
                    <col class="w-15p">
                    <col class="w-35p">
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        고객명
                    </th>
                    <td>
                        <div class="dp-flex dp-flex-gap5">
                            <?php $modifyKey='isModify'; $model='customer.customerName'; $placeholder="'고객명'" ?>
                            <?php include './admin/ims/ims25/component/_text.php'?>
                            <div class="btn btn-sm btn-white" @click="openCustomer(customer.sno,'basic')" v-show="!isModify">고객상세</div>
                        </div>
                    </td>
                    <th>업종</th>
                    <td >
                        <div v-if="isModify" class="dp-flex dp-flex-gap5">
                            <select v-model="parentBizCateName" @change="customer.busiCateSno=0;" class="form-control">
                                <option v-for="val in parentBizCateList">{% val %}</option>
                            </select>
                            <select v-model="customer.busiCateSno" class="form-control">
                                <option value="0">선택</option>
                                <option v-for="val in bizCateList" :value="val.busiCateSno" v-show="val.parentCateName == parentBizCateName">
                                    {% val.cateName %}
                                </option>
                            </select>
                        </div>
                        <div v-else="">{% customer.busiCateText %}</div>
                    </td>
                </tr>
                <tr>
                    <th >담당자</th>
                    <td >
                        <div class="dp-flex">
                            <?php $model='customer.contactName'; $placeholder="'담당자명'" ?>
                            <?php include './admin/ims/ims25/component/_text.php'?>

                            <div v-show="isModify">
                                <input type="text" class="form-control" v-model="customer.contactPosition" placeholder="직함">
                            </div>
                            <div v-show="!isModify" >
                                <div v-if="!$.isEmpty(customer.contactPosition)">
                                    {% customer.contactPosition %}
                                </div>
                            </div>
                            <span class="hover-btn cursor-pointer sl-blue" v-show="!isModify && cntCustomerContact > 1" @click="openCommonPopup('customer_contact', 840, 710, {sno:customer.sno});"> 외 {% cntCustomerContact %}명</span>
                        </div>
                    </td>
                    <th>연락처/이메일</th>
                    <td >
                        <div :class="!isModify?'dp-flex dp-flex-gap5':''">
                            <?php $model='customer.contactMobile'; $placeholder="'휴대전화'" ?>
                            <?php include './admin/ims/ims25/component/_text.php'?>
                            /
                            <?php $model='customer.contactEmail'; $placeholder="'이메일'" ?>
                            <?php include './admin/ims/ims25/component/_text.php'?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>담당자 유대감</th>
                    <td >
                        <?php $model='customer.addedInfo.info129'; $placeholder="'담당자 유대감'" ?>
                        <?php include './admin/ims/ims25/component/_text.php'?>
                    </td>
                    <th>담당자 니즈</th>
                    <td >
                        <?php $model='customer.addedInfo.info130'; $placeholder="'담당자 니즈'" ?>
                        <?php include './admin/ims/ims25/component/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>영업 담당자</th>
                    <td colspan="3">
                        <div class="" v-show="!isModify">
                            <div v-show="$.isEmpty(mainData.salesManagerNm)">미정</div>
                            <div v-show="!$.isEmpty(mainData.salesManagerNm)">{% mainData.salesManagerNm %}</div>
                        </div>
                        <div class="" v-show="isModify">
                            <select class="form-control w100" v-model="mainData.salesManagerSno" >
                                <option value="0">미정</option>
                                <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        사업계획
                    </th>
                    <td colspan="3" class="">
                        <div class="">
                            <div class="dp-flex dp-flex-gap10" v-show="!isModify">
                                <div>
                                    <b>사업계획</b> : {% JS_LIB_CODE['includeType'][mainData.bizPlanYn] %}
                                </div>
                                <div>
                                    <span v-show="!$.isEmpty(mainData.targetSalesYear)">
                                        <b>목표매출연도</b> : {% mainData.targetSalesYear %}년
                                    </span>
                                    <span v-show="$.isEmpty(mainData.targetSalesYear)">
                                        <b>목표매출연도</b> : <span class="text-muted">미지정</span>
                                    </span>
                                </div>
                            </div>
                            <div class="" v-show="isModify">
                                <div class="dp-flex mgt3">
                                    사업계획 :
                                    <label class="radio-inline mgl5 mgr5" v-for="(eachValue, eachKey) in JS_LIB_CODE['includeTypeSimple']" >
                                        <input type="radio" name="preSalesBizPlanYn'"  :value="eachKey" v-model="mainData.bizPlanYn" />
                                        <span class="">{%eachValue%}</span>
                                    </label>
                                </div>
                                <div class="dp-flex mgt3">
                                    목표매출연도 :
                                    <select2 v-model="mainData.targetSalesYear" class="form-control form-inline inline-block " style="width:100px;">
                                        <?php foreach($yearFullList as $key => $val) {?>
                                            <option value="<?=$val?>"><?=$key?></option>
                                        <?php }?>
                                    </select2>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>
                        진행 구분
                    </th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            {% JS_LIB_CODE['bidType'][mainData.bidType2] %}
                        </div>
                        <div v-show="isModify">
                            <?php foreach( \Component\Ims\ImsCodeMap::BID_TYPE as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="preBidType2" value="<?=$k?>" v-model="mainData.bidType2"  @change="setPrjTypeByBidType()" /><?=$v?>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        프로젝트 타입
                    </th>
                    <td colspan="3" >
                        <div v-show="!isModify">
                            {% JS_LIB_CODE['projectType'][mainData.projectType] %}
                        </div>
                        <div v-show="isModify">
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE_N as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="projectType" value="<?=$k?>" v-model="mainData.projectType"  /><?=$v?>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-danger">
                        <span v-if="'single' === mainData.bidType2">미팅일자</span>
                        <span v-else>입찰일자</span>
                    </th>
                    <td colspan="3" >
                        <div class="dp-flex">
                            <div v-show="!isModify">
                                <div v-if="$.isEmpty(mainData.cpMeeting)">
                                    {% $.isset($.formatShortDate(mainData.exMeeting),'미정') %}
                                    <span class="font-11" v-if="!$.isEmpty(mainData.exMeeting)" v-html="$.remainDate(mainData.exMeeting,true)"></span>
                                </div>
                                <div v-else class="sl-green">
                                    {% $.isset($.formatShortDate(mainData.cpMeeting)) %} 종료
                                </div>
                            </div>
                            <div v-show="isModify" class="dp-flex dp-flex-gap5">
                                <div>예정일 : <date-picker v-model="mainData.exMeeting" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker></div>
                                <div >완료일 : <date-picker v-model="mainData.cpMeeting" value-type="format" format="YYYY-MM-DD"  :editable="false" class="mini-picker"></date-picker></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="font-12 text-danger">
                        디자인실 사전참여
                    </th>
                    <td colspan="3" >
                        <div v-show="isModify">
                            <label v-for="opt in designTeamOptionList" :key="opt.value" class="mgr10 hand hover-btn">
                                <input type="checkbox" :value="opt.value" v-model="designTeamSelected">
                                {% opt.label %}
                            </label>
                        </div>
                        <div v-show="!isModify">
                            <div class="mt-2">
                                {% designTeamText %}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        영업메모(How-To)
                        <div class="font-11">(계획/전략 요약 메모)</div>
                    </th>
                    <td colspan="3" >
                        <div class="bg-light-gray3 round-box w-100p " style="height:150px; overflow-y: auto;" v-html="$.nl2br(mainData.salesMemo)" v-show="!isModify"></div>
                        <textarea class="form-control w70 inline-block flo-left" rows="5" placeholder="납품 계획/방법 메모" v-model="mainData.salesMemo" v-show="isModify"></textarea>
                    </td>
                </tr>
                <tr v-if="mainData.projectStatus >= 95">
                    <th>
                        유찰/보류 내용
                    </th>
                    <td colspan="3" >
                        <div class="bg-light-gray3 round-box w-100p " style="height:150px; overflow-y: auto;" v-html="$.nl2br(mainData.holdMemo)" v-show="!isModify"></div>
                        <textarea class="form-control w70 inline-block flo-left" rows="5" placeholder="유찰/보류 내용" v-model="mainData.holdMemo" v-show="isModify"></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">사전 영업 자료</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="saveWithStyle()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30" v-if="!$.isEmptyAll(fileList.filePre1)">
                <colgroup>
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr v-if="!$.isEmptyAll(fileList.filePre1)">
                    <th>영업 기획서</th>
                    <td colspan="3" >
                        <div class="dp-flex dp-flex-gap10">

                            <schedule-template :data="mainData" :modify="isModify" :type="'salesPlan'"></schedule-template>

                            <div class="btn btn-sm btn-white" @click="openSalesView(mainData.sno)" >영업기획서</div>
                            <!--영업기획서 결재 라인-->
                            <div class="btn btn-sm btn-red btn-red-line2"
                                 v-if=" 'n' === mainData.salesPlanApproval || 0 >= Number(projectApprovalInfo.salesPlan.sno) "
                                 @click="openApprovalWrite(customer.sno, mainData.sno, 'salesPlan')">
                                결재요청
                            </div>

                            <approval-template4
                                    :project="mainData"
                                    :approval="projectApprovalInfo"
                                    :confirm-type="'salesPlan'"
                                    :confirm-field="'salesPlanApproval'"
                                    :memo-field="'unused'"
                            ></approval-template4>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        고객 스타일
                    </th>
                    <td colspan="3" class="" style="vertical-align: top">

                        <div class="dp-flex dp-flex-right">
                            <div class="btn btn-blue btn-sm btn-blue-line mgb3" @click="addSalesStyle()" v-show="isModify">+ 스타일 추가</div>
                        </div>
                        <!-- 스타일 관련 커스텀 필드 -->
                        <table class="table-expected-sales table table-cols table-default-center table-pd-3 table-td-height30 table-th-height30 mgb0 border-top-none mgb5">
                            <colgroup>
                                <col class="w-5p" />
                                <col class="w-5p" />
                                <col v-for="fieldData in getStyleField('preSales')"
                                     v-if="true != fieldData.skip && true !== fieldData.subRow"
                                     :class="`w-${fieldData.col}p`"/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>이동</th>
                                <th>번호</th>
                                <th v-for="fieldData in getStyleField('preSales')">
                                    <span v-html="fieldData.title"></span>
                                </th>
                            </tr>
                            </thead>
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
                                <td ><!--번호-->
                                    {% idx+1 %}<!--<div class="text-muted font-11">#{% each.sno %}</div>-->
                                </td>
                                <td v-for="fieldData in getStyleField('preSales')" :class="fieldData.class + ' relative'">
                                    <?php include 'ims25/template/_ims25_custom_style_template.php'?>
                                </td>
                            </tr>
                            <tr v-if="0 >= productList.length">
                                <td colspan="99" class="ta-c">등록된 스타일 없음</td>
                            </tr>
                            </tbody>
                        </table>

                        추정매출TOTAL : <span class="text-danger">{% $.numberToKorean(mainData.extAmount) %}원</span>
                        <span class="font-11 text-muted">(개별 예정수량 X 현재가 or 직접 입력)</span>
                    </td>
                </tr>
                <tr v-if="!$.isEmptyAll(fileList.filePre1)">
                    <th>추정매출</th>
                    <td colspan="3">
                        <div v-show="isModify">
                            <div class="">
                                <div class="col-xs-6">
                                    <div class="dp-flex" >
                                        <div>추정매출 :</div>
                                        <div v-show="styleCalcInfo.totalCurrentPrice > 0">
                                            {% $.setNumberFormat(styleCalcInfo.totalCurrentPrice) %}원
                                        </div>
                                        <div v-show="0 >= styleCalcInfo.totalCurrentPrice">
                                            <input type="text" class="form-control w-200px" v-model="mainData.extAmount" placeholder="추정매출">
                                        </div>
                                    </div>
                                    <div>{% $.numberToKorean(mainData.extAmount) %}</div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="dp-flex">
                                        <div>예상마진 :</div>
                                        <input type="text" class="form-control w-200px" v-model="mainData.extMargin" placeholder="예상마진">
                                    </div>
                                    <div>{% $.numberToKorean(mainData.extMargin)%}</div>
                                </div>
                            </div>
                        </div>
                        <div v-show="!isModify" >
                            <div v-if="!$.isEmpty(mainData.extAmount)">
                                추정매출 :
                                <span v-if="Number(mainData.extAmount)>0">
                                    {% $.numberToKorean(mainData.extAmount) %}원
                                </span>
                                <span v-else>{% mainData.extAmount %}</span>
                            </div>
                            <div v-else class="text-muted">
                                미확인
                            </div>

                            <div v-if="!$.isEmpty(mainData.extMargin)">
                                예상마진 :
                                <span v-if="Number(mainData.extMargin)>0">
                                    {% $.numberToKorean(mainData.extMargin) %}
                                    ({% $.getMargin((mainData.extAmount-mainData.extMargin), mainData.extAmount)%}%)
                                </span>
                                <span v-else>{% mainData.extMargin %}</span>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr >
                    <th>영업 전략</th>
                    <td colspan="3" >
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.fileSalesStrategy.files" ></simple-file-list>
                            {% fileList.fileSalesStrategy.memo %}
                            {% fileList.fileSalesStrategy.title %}
                        </div>
                        <file-upload :file="fileList.fileSalesStrategy" :id="'fileSalesStrategy'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>
                <!--
                <tr >
                    <th>고객사 회의록</th>
                    <td colspan="3" >
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.fileMeetingReport.files" ></simple-file-list>
                            {% fileList.fileMeetingReport.memo %}
                            {% fileList.fileMeetingReport.title %}
                        </div>
                        <file-upload :file="fileList.fileMeetingReport" :id="'fileMeetingReport'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>
                -->
                <tr >
                    <th>디자인 제안서</th>
                    <td colspan="3">
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.filePre1.files"></simple-file-list>
                            {% fileList.filePre1.memo %}
                            {% fileList.filePre1.title %}
                        </div>
                        <file-upload :file="fileList.filePre1" :id="'filePre1'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>

                <tr >
                    <th>개선 제안서</th>
                    <td colspan="3">
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.filePre2.files"></simple-file-list>
                            {% fileList.filePre2.memo %}
                            {% fileList.filePre2.title %}
                        </div>
                        <file-upload :file="fileList.filePre2" :id="'filePre2'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>

                <tr >
                    <th>선호도 조사</th>
                    <td colspan="3">
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.filePre3.files"></simple-file-list>
                            {% fileList.filePre3.memo %}
                            {% fileList.filePre3.title %}
                        </div>
                        <file-upload :file="fileList.filePre3" :id="'filePre3'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>

                <tr >
                    <th>샘플 테스트</th>
                    <td colspan="3">
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.filePre4.files"></simple-file-list>
                            {% fileList.filePre4.memo %}
                            {% fileList.filePre4.title %}
                        </div>
                        <file-upload :file="fileList.filePre4" :id="'filePre4'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>

                <tr >
                    <th>기타 자료</th>
                    <td colspan="3">
                        <div class="dp-flex font-11" v-show="!isModify">
                            <simple-file-list :files="fileList.fileEtc7.files"></simple-file-list>
                            {% fileList.fileEtc7.memo %}
                            {% fileList.fileEtc7.title %}
                        </div>
                        <file-upload :file="fileList.fileEtc7" :id="'fileEtc7'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>



<div class="col-xs-12" v-if="95 > mainData.projectStatus">

    <div class="col-xs-12" >

        <div class="row" v-if="!$.isEmpty(mainData.regDt)">

            <div class="pd15 relative">

                <div style="position: absolute;right: 18px; top:15px" class="dp-flex dp-flex-gap10" v-show="!isModify">
                    <div class="btn btn-red btn-sm" v-show="!isModify" style="font-family: Godo" @click="setModify(true)">수정</div>
                    <!--<div class="btn btn-white btn-sm" style="font-family: Godo">스케쥴 변경 이력</div>-->
                </div>

                <div style="position: absolute;right: 220px; top:18px" class="dp-flex dp-flex-gap10" v-show="isModify">
                    <div class="btn btn-red btn-sm" v-show="isModify" style="font-family: Godo" @click="openAddManager()">추가 참여자 등록</div>
                    <div class="btn btn-red btn-sm" v-show="isModify" style="font-family: Godo" @click="save()">저장</div>
                    <div class="btn btn-white btn-sm" v-show="isModify" style="font-family: Godo" @click="setModify(false)">취소</div>
                </div>

                <div >
                    <?php
                    $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 사전 영업 스케쥴 관리';
                    $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1;
                    ?>
                    <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
                </div>
            </div>
        </div>

        <div class="row" v-if="!$.isEmpty(mainData.regDt)">
            <div class="pdl15 mgt15">
                <div class="table-pop-title-small mgt5 dp-flex">
                    <div class="bold relative dp-flex">
                        <i class="fa fa-calendar-o" aria-hidden="true"></i> 업무 시작 예정일 :
                        <?php $model='mainData.salesStartDt'; $placeholder='업무 시작 예정일' ?>
                        <?php include './admin/ims/ims25/component/_picker.php'?>
                    </div>
                </div>

                <div class="table-pop-title-small mgt5 dp-flex dp-flex-between mgt15">
                    <div class="bold relative dp-flex dp-flex-wrap">
                        <i class="fa fa-calendar-o" aria-hidden="true"></i> 고객 납기 예정일 :
                        <?php $model='mainData.customerDeliveryDt'; $placeholder='고객 납기 예정일' ?>
                        <?php include './admin/ims/ims25/component/_picker.php'?>
                    </div>
                </div>

                <div class="">
                    <i class="fa fa-info-circle" aria-hidden="true"></i> 고객 납기일 - 업무 시작일로 스케쥴D/L이 계산 됩니다.
                </div>
            </div>
        </div>

        <div class="row" v-if="!$.isEmpty(mainData.regDt)">
            <div class="pd15 relative">
                <div style="position: absolute;right: 18px; top:15px" class="dp-flex dp-flex-gap10" v-show="!isModify">
                    <div class="btn btn-red btn-sm" v-show="!isModify" style="font-family: Godo" @click="setModify(true)">수정</div>
                    <!--<div class="btn btn-white btn-sm" style="font-family: Godo">스케쥴 변경 이력</div>-->
                </div>

                <div style="position: absolute;right: 18px; top:15px" class="dp-flex dp-flex-gap10" v-show="isModify">
                    <div class="btn btn-red btn-sm" v-show="isModify" style="font-family: Godo" @click="save()">저장</div>
                    <div class="btn btn-white btn-sm" v-show="isModify" style="font-family: Godo" @click="setModify(false)">취소</div>
                </div>

                <div >
                    <?php
                    $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 고객 제안 일정';
                    $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_CUSTOMER;
                    ?>
                    <?php include './admin/ims/ims25/ims25_view_schedule_cust_template.php' ?>
                </div>
                <div>
                    고객 안내 상태 :
                    <label class="radio-inline mgl5 mgr5" >
                        <input type="radio" name="scheduleShare'"  value="n" checked />
                        <span class="">미안내</span>
                    </label>
                    <label class="radio-inline mgl5 mgr5" >
                        <input type="radio" name="scheduleShare'"  value="y" />
                        <span class="">안내완료</span>
                    </label>

                    <div class="notice-info mgt3 text-danger">고객 스케쥴 안내 후에는 반드시 일정 준수 바랍니다. 일정 변경 시 공지 필수!</div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12">

    <div class="col-xs-6" >
        <!-- TM / EM 이력 -->
        <div class="table-title gd-help-manual">
            <div class="flo-left">TM/EM 이력</div>
            <div class="flo-right"></div>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-center table-td-height30 table-th-height30">
                <colgroup>
                    <col class="w-90px" /><!--일자-->
                    <col class="w-10p" /><!--담당자-->
                    <col class="w-10p" /><!--활동구분-->
                    <col class="" /><!--내용-->
                </colgroup>
                <tr>
                    <th>영업 일자</th>
                    <th>영업담당</th>
                    <th>구분</th>
                    <th>영업 내용</th>
                </tr>
                <tr v-if="0 >= tmList.length">
                    <td colspan="9">데이터가 없습니다.</td>
                </tr>
                <tr v-for="(val, key) in tmList">
                    <td class="font-11">{% $.formatShortDate(val.regDt) %}</td>
                    <td class="font-11">{% val.regManagerName %}</td>
                    <td class="font-11">{% val.contentsTypeHan %}</td>
                    <td class="ta-l" >
                        <div v-html="$.nl2br(val.contents)" class="ta-l pdl10"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-xs-6" >
        <!--협상 미팅 이력-->
        <div class="table-title gd-help-manual">
            <div class="flo-left">협상/미팅 이력</div>
            <div class="flo-right">
                <div class="btn btn-red btn-sm btn-red-line2"  @click="openCustomerComment(customer.sno, 0, 'meeting')">등록</div>
            </div>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-center table-td-height30 table-th-height30">
                <colgroup>
                    <col class="w-90px" /><!--일자-->
                    <col class="w-10p" /><!--담당자-->
                    <col class="" /><!--내용-->
                </colgroup>
                <tr>
                    <th>일자</th>
                    <th>등록</th>
                    <th>제목/요약</th>
                </tr>
                <tr v-if="0 >= meetingList.length">
                    <td colspan="9">데이터가 없습니다.</td>
                </tr>
                <tr v-for="(each, eachIndex) in meetingList">
                    <td class="font-11">
                        <div>{% $.formatShortDateWithoutWeek(each.regDt) %}</div>
                    </td>
                    <td class="font-11">
                        <div>{% each.regManagerNm %}</div>
                    </td>
                    <td class="ta-l" >
                        <div class="ta-l hover-btn cursor-pointer" @click="openCustomerComment(customer.sno, each.sno, 'meeting')">
                            <b>{% each.subject %}</b>
                        </div>
                        <div class="ta-l hover-btn cursor-pointer" @click="openCustomerComment(customer.sno, each.sno, 'meeting')">
                            {% each.textContents %}
                        </div>
                        <div class="ta-l font-11 dp-flex" >
                            <div class="sl-blue" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0">첨부 : </div>
                            <simple-file-only-not-history-upload :file="each.fileData" :id="'fileDataView'" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0"></simple-file-only-not-history-upload>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>


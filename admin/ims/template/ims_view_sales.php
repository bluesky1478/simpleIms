<?php $modelPrefix='sales';  ?>
<div class="col-xs-12" v-if="customer.sno > 0">
    <!-- 고객/영업 정보 -->
    <div class="col-xs-6 ">
        <div class="table-title gd-help-manual">
            <div class="flo-left">고객/영업 정보</div>
            <div class="flo-right pdb5">
                <button type="button" class="btn btn-red " v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box "  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white "  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
        </div>
        <div >
            <table class="table table-cols ims-table-style1">
                <colgroup>
                    <col class="width-xs">
                    <col class="width-md">
                    <col class="width-xs">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr>
                    <th>고객명</th>
                    <td >
                        <div class="dp-flex">
                            <?php $model='customer.customerName'; $placeholder='고객명' ?>
                            <?php include 'basic_view/_text.php'?>
                            <div class="btn btn-white btn-sm mgl5" @click="openCustomer(customer.sno)">상세</div>
                        </div>
                    </td>
                    <th class="_require text-danger">
                        고객 Style code
                    </th>
                    <td class="text-danger">
                        <?php $model='customer.styleCode'; $placeholder='고객사명' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th class="font-11">
                        영업 담당자
                    </th>
                    <td colspan="3">
                        <?php
                            $model='project.salesManagerSno';
                            $modelValue='project.salesManagerNm';
                            $defaultValue=['0','미정'];
                            $listData=$managerList;
                            $selectWidth=30;
                        ?>
                        <?php include 'basic_view/_select.php'?>
                    </td>
                </tr>
                <tr>
                    <th>
                        희망/예정납기
                    </th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            {% $.formatShortDateWithoutWeek(project.salesDeliveryDt) %}
                        </div>
                        <div v-show="isModify" class="">
                            <date-picker v-model="project.salesDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" placeholder="희망/예정납기"></date-picker>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>고객사 담당자</th>
                    <td colspan="3">
                        <div v-show="!isModify">
                            {% customer.contactName %}
                            <span class="text-muted">
                                ( {% customer.contactMobile %} / {% customer.contactEmail %} )
                            </span>
                        </div>
                        <div v-show="isModify">
                            <table class="table table-cols table-pd-3 table-td-height0 table-th-height0 font-11 mg0">
                                <tr>
                                    <th>담당자</th>
                                    <td class="">
                                        <input type="text" class="w-100p form-control _border-none"  v-model="customer.contactName" >
                                    </td>
                                </tr>
                                <tr>
                                    <th>연락처</th>
                                    <td>
                                        <input type="text" class="w-100p form-control _border-none"  v-model="customer.contactMobile">
                                    </td>
                                </tr>
                                <tr>
                                    <th>메일</th>
                                    <td>
                                        <input type="text" class="w-100p form-control _border-none"  v-model="customer.contactEmail">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>의사결정/노사합의</th>
                    <td colspan="3">
                        <div class="dp-flex dp-flex-gap10">
                            <b>의사결정 :</b>
                            <?php $model='customer.addedInfo.info089'; $placeholder='의사 결정 라인' ?>
                            <div v-show="<?=empty($modifyKey)?'isModify':$modifyKey?>">
                                <input type="text" class="form-control" v-model="<?=$model?>" placeholder="<?=$placeholder?>">
                            </div>
                            <div v-show="!<?=empty($modifyKey)?'isModify':$modifyKey?>" >
                                {% $.isEmpty(<?=$model?>)?'미확인':<?=$model?> %}
                            </div>
                            , <b> 노사합의 :</b>
                            <?php $model = 'customer.addedInfo.info088'; $listCode = 'existType3'?>
                            <?php include 'basic_view/_radio.php'?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>3PL</th>
                    <td colspan="99">
                        <div class="dp-flex dp-flex-gap10">
                            <div v-show="!isModify">
                                {% customer.use3plKr %}
                            </div>
                            <div v-show="isModify">
                                <div>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="salesUse3pl" value="n"  v-model="customer.use3pl" />사용안함
                                    </label>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="salesUse3pl" value="y"  v-model="customer.use3pl" />사용
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>폐쇄몰</th>
                    <td colspan="99">
                        <div class="dp-flex">
                            <div v-show="!isModify">
                                {% customer.useMallKr %}
                            </div>
                            <div v-show="isModify">
                                <label class="radio-inline font-11">
                                    <input type="radio" name="salesUseMall" value="n"  v-model="customer.useMall" />사용안함
                                </label>
                                <label class="radio-inline font-11">
                                    <input type="radio" name="salesUseMall" value="y"  v-model="customer.useMall" />사용
                                </label>
                            </div>
                            <div class="btn btn-white btn-sm mgl10" v-show="'y' === customer.useMall" @click="openCustomer(customer.sno,'mall')">폐쇄몰 개설 정보</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>미팅보고서</th>
                    <td colspan="3">
                        <div class="btn btn-sm btn-white " @click="openCustomer(customer.sno,'meeting')">미팅보고서</div>
                    </td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
    <!-- 고객/영업 정보 -->

    <!-- 수령자 정보 -->
    <div class="col-xs-6">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                프로젝트 정보 <span class="font-13">( 등록일 : {% $.formatShortDate(project.regDt) %} )</span>
            </div>
            <div class="flo-right pdb5">
                <button type="button" class="btn btn-red " v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box "  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white "  v-show="isModify" @click="isModify=false">취소</button>
            </div>
        </div>

        <div class="">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>진행 타입/예정일</th>
                    <td>
                        <div v-show="!isModify">
                            <span class="font-16 bold">진행타입 : {% project.bidType2Kr %}</span>
                            <span v-show="!$.isEmpty(project.salesExDt) && '0000-00-00' !== project.salesExDt" class="font-14">
                                <span v-show="'single' !== project.bidType2" class="mgl10">
                                 입찰 예정일 : ~ {% $.formatShortDate(project.salesExDt) %}
                                </span>
                                <span v-show="'single' === project.bidType2" class="mgl10">
                                 미팅 예정일 : ~ {% $.formatShortDate(project.salesExDt) %}
                                </span>
                            </span>
                        </div>

                        <div v-show="isModify">
                            <div class="dp-flex dp-flex-gap5">
                                <b>진행 타입 :</b>
                                <select class="form-control font-16" v-model="project.bidType2"  style="width:20%" >
                                    <?php foreach (\Component\Ims\ImsCodeMap::BID_TYPE as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mgt5">
                                예정일 : <date-picker v-model="project.salesExDt" value-type="format" format="YYYY-MM-DD"  :editable="false" placeholder="영업 예정일"></date-picker>
                            </div>
                        </div>

                        <!--//입찰 ( 입찰 예정일 : ~ 24/12/31 )-->
                    </td>
                </tr>
                <tr>
                    <th>
                        영업 진행상태
                    </th>
                    <td class="">

                        <div class="dp-flex">

                            <div class="font-16 text-green" v-if="'complete' === initSalesStatus">
                                영업완료
                            </div>

                            <div class="dp-flex dp-flex-gap5" v-if="'complete' !== initSalesStatus">

                                <select class="form-control font-15" style="width:150px; height:28px" v-model="project.salesStatus">
                                    <?php foreach (\Component\Ims\ImsCodeMap::SALES_STATUS as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select>

                                <div class="btn btn-red " @click="setSalesStatus()">변경</div>

                            </div>
                            <!--
                            <div class="btn btn-red btn-red-line2 mgl15" @click="setStatusWithMsg('보류(미확정)상태로 변경하시겠습니까?',98)" >보류(미확정)</div>
                            <div class="btn btn-red btn-red-line2 " @click="setStatusWithMsg('보류(확정)상태로 변경하시겠습니까?',99)">보류(확정)</div>
                            -->
                        </div>

                        <div class="dp-flex dp-flex-gap5 " v-if="false">
                            <!--FIXME  : 작업하다 중단. 일단 개별 사용자 판단에 맡긴다.-->
                            <div class="font-16 bold pd0">
                                {% project.salesStatusKr %}
                            </div>
                            <div class="btn btn-white mgl20"
                                 v-show=" 'proc' !== project.salesStatus
                                 && ('wait' === project.salesStatus || 'proc' === project.salesStatus)"
                            >
                                준비 하기
                            </div>
                            <div class="btn btn-white"
                                 v-show=" 'single' !== project.bidType2
                                 && 'fail' !== project.salesStatus
                                 && ('wait' === project.salesStatus || 'proc' === project.salesStatus)"
                            >
                                유찰 처리
                            </div>

                            <div class="btn btn-white"
                                 v-show=" 'hold' !== project.salesStatus
                                 && ('wait' === project.salesStatus || 'proc' === project.salesStatus)"
                            >
                                보류 처리
                            </div>
                        </div>



                    </td>
                </tr>
                <tr>
                    <th>연도/시즌</th>
                    <td>
                        <div v-show="!isModify">
                            {% project.projectYear %}/{% project.projectSeason %}
                            {% project.projectTypeKr %}
                        </div>
                        <div v-show="isModify">
                            <select v-model="project.projectYear" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                                <?php foreach($yearList as $yearEach) {?>
                                    <option><?=$yearEach?></option>
                                <?php }?>
                            </select>
                            <select v-model="project.projectSeason" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                                <option >ALL</option>
                                <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                    <option><?=$seasonEn?></option>
                                <?php }?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>프로젝트 타입</th>
                    <td>
                        <?php
                        $model='project.projectType';
                        $modelValue='project.projectTypeKr';
                        $listData=\Component\Ims\ImsCodeMap::PROJECT_TYPE;
                        $selectWidth=20
                        ?>
                        <?php include 'basic_view/_select2.php'?>
                    </td>
                </tr>
                <tr>
                    <th>디자인 업무타입</th>
                    <td>
                        <?php
                        $model='project.designWorkType';
                        $modelValue='project.designWorkTypeKr';
                        $listData=\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE;
                        $selectWidth=20
                        ?>
                        <?php include 'basic_view/_select2.php'?>
                    </td>
                </tr>
                <tr>
                    <th>추정매출</th>
                    <td>
                        <?php $model='project.extAmount'; $placeholder='추정매출' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>예상 마진</th>
                    <td>
                        <?php $model='project.extMargin'; $placeholder='예상마진(자유기입)' ?>
                        <?php include 'basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>사업계획</th>
                    <td>
                        <?php $modelPrefix='sales'; $model='project.bizPlanYn'; $listCode='includeType'?>
                        <?php include 'basic_view/_radio.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- 수령자 정보 -->
</div>

<div class="col-xs-12">
    <!-- 협상/미팅 기록 정보 -->
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">협상/미팅 내역</div>
            <div class="flo-right pdb5">
                <div class="btn  btn-red btn-red-line " @click="openCustomerComment(customer.sno, 0, 'meeting')">
                    등록
                </div>
                <div class="btn btn-white" @click="openCustomerCommentHistory(customer.sno,'meeting')">
                    이력
                </div>
            </div>
        </div>

        <div >
            <table class="table table-cols ims-table-style1">
                <colgroup>
                    <col class="width-xs">
                    <col class="width-md">
                    <col class="width-xs">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr>
                    <td colspan="99" class="pd5" style="border-bottom:none !important;">
                        <div v-if="typeof meetingList[0] != 'undefined'" >
                            <div v-html="meetingList[0].contents" class="m-zero"></div>
                            <div class="font-11 dp-flex" >
                                <div class="sl-blue" v-if="!$.isEmpty(meetingList[0].fileData.files) && meetingList[0].fileData.files.length > 0">첨부 : </div>
                                <simple-file-only-not-history-upload :file="meetingList[0].fileData" :id="'fileDataView'" v-if="!$.isEmpty(meetingList[0].fileData.files) && meetingList[0].fileData.files.length > 0"></simple-file-only-not-history-upload>
                            </div>
                        </div>
                        <div v-else class="text-center">
                            협/미팅 내역이 없습니다.
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- 협상/미팅 기록 정보 -->

    <!-- 영업 취득 정보 -->
    <div class="col-xs-6">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                미팅보고서 내용
            </div>
            <div class="flo-right">
                <button type="button" class="btn btn-red " @click="openCustomer(customer.sno,'meeting', 'true')">
                    미팅보고서 수정
                </button>
            </div>
        </div>

        <div class="js-layout-order-view-receiver-info" v-if="customer.sno > 0">

            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <td class="pd0" colspan="99" style="border-bottom:none !important; padding-top:5px !important;">
                        <div class="new-style2">
                            <table class="table table-pd-5 table-th-height0 table-td-height0 font-12" >
                                <colgroup>
                                    <col class="w-17p">
                                    <col >
                                    <col class="w-17p">
                                    <col >
                                </colgroup>

                                <tr v-if="0 >= vueApp.custInfo.length">
                                    <td colspan="99" class="text-center">확인된 정보 없음</td>
                                </tr>

                                <tr v-for="custInfoRow in vueApp.custInfo">
                                    <?php for($i=0; 2>$i; $i++){ ?>
                                        <th v-for="(custInfo,custInfoKey) in custInfoRow" v-if="<?=$i?> == custInfoKey">
                                            {% custInfo.title %}
                                        </th>
                                        <td v-for="(custInfo,custInfoKey) in custInfoRow" :colspan="1===custInfoRow.length?'3':'1'" v-if="<?=$i?> == custInfoKey">
                                            <div v-if="'radio'===custInfo.type">
                                                {% getCodeMap()[custInfo.code][custInfo.value] %}
                                            </div>
                                            <div v-else>
                                                {% custInfo.value %}
                                            </div>
                                        </td>
                                    <?php } ?>
                                </tr>

                            </table>

                        </div>

                        <div v-show="isModify" class="notice-info">
                            영업 취득 정보(미팅보고서)는 팝업 화면에서 수정바랍니다.
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
    <!-- 영업 취득 정보 -->
</div>

<div class="col-xs-12">
    <!--예상스타일-->
    <div class="col-xs-12 js-order-view-receiver-area">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                <div class="dp-flex">
                    희망 스타일
                    <div class="mgl10 dp-flex dp-flex-gap10">
                        <!--
                        <div class="mgl10 dp-flex dp-flex-gap5" v-show="isStyleModify">
                            <div class="btn btn-red btn-sm " @click="saveStyleList()">스타일 저장</div>
                            <div class="btn btn-white btn-sm w-50px" @click="isStyleModify=false">취소</div>
                        </div>
                        <div class="btn btn-red btn-red-line2 btn-sm " v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;스타일 수정&nbsp;&nbsp;</div>
                        -->
                    </div>
                </div>
            </div>
            <div class="flo-right">

                <div class="mgl10 dp-flex dp-flex-gap5" >
                    <div class="btn btn-red btn-sm " @click="saveStyleList(true)" v-show="isStyleModify">스타일 저장</div>
                    <div class="btn btn-white btn-sm w-50px" @click="isStyleModify=false" v-show="isStyleModify">취소</div>
                    <div class="btn btn-red btn-sm btn-red-line2" v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;스타일 수정&nbsp;&nbsp;</div>

                    <div class="btn btn-blue btn-blue-line btn-sm" @click="addSalesStyle()">+ 스타일 추가</div>
                    <!--
                    <button type="button" class="btn btn-blue btn-sm js-receiverInfoBtn">견적서 발송</button>
                    <button type="button" class="btn btn-white btn-sm js-receiverInfoBtn">견적서 발송 이력</button>
                    -->
                    <button type="button" class="btn btn-red-box btn-sm js-receiverInfoBtnSave js-orderViewInfoSave display-none" >저장</button>
                </div>

            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div class="js-layout-order-view-receiver-info">

            <table class="table table-cols table-pd-3 table-th-height0 table-td-height0 table-center table-fixed style-table">
                <colgroup>
                    <col class="w-2p">
                    <col class="w-2p">
                    <col class="w-6p">
                    <col class="w-8p">
                    <col class="w-10p">
                    <col class="w-7p">
                    <col class="w-7p">
                    <col class="w-6p">
                    <col class="w-6p">
                    <col class="w-8p">
                    <col class="w-8p">
                    <col class="">
                    <col class="w-6p">
                </colgroup>
                <thead>
                <tr>
                    <th class="text-center border-bottom-zero" >이동</th>
                    <th class="text-center border-bottom-zero">번호</th>
                    <th class="border-bottom-zero">시즌</th>
                    <th class="border-bottom-zero">타입</th>
                    <th class="text-left border-bottom-zero">스타일명</th>
                    <th class="border-bottom-zero">예상수량</th>
                    <th class="border-bottom-zero">현재단가</th>
                    <th class="border-bottom-zero">타겟단가</th>
                    <th class="border-bottom-zero">타겟단가(최대)</th>
                    <th class="border-bottom-zero">진행형태</th>
                    <th class="border-bottom-zero">고객사샘플</th>
                    <th class="border-bottom-zero" style="text-align:left!important;">
                        상세 정보
                    </th>
                    <th class="border-bottom-zero text-center">추가/삭제</th>
                </tr>
                <tr v-if="isStyleModify">
                    <th colspan="2" style="text-align:right !important;">
                        <span class="" style="font-weight: normal">일괄 작업</span>
                    </th>
                    <th>
                        <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchSeason" @change="batchModify(productList,'prdSeason',batchSeason)">
                            <option value="">미정</option>
                            <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>"><?=$codeKey?></option>
                            <?php } ?>
                        </select>
                    </th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center">
                        <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchStyleProcType" @change="batchModify(productList,'styleProcType',batchStyleProcType)">
                            <option :value="eachKey" v-for="(eachValue, eachKey) in getCodeMap()['styleProcType']">{% eachValue %}</option>
                        </select>
                    </th>
                    <th class="text-center">
                        <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchCustSampleType" @change="batchAddInfoModify(productList,'prd002',batchCustSampleType)">
                            <option :value="eachKey" v-for="(eachValue, eachKey) in getCodeMap()['custSampleType']">{% eachValue %}</option>
                        </select>
                    </th>
                    <th >
                        <div class="dp-flex dp-flex-gap15 border-radius-10 bg-white pdt5" style="justify-content: start">
                            <label class="mgl20">
                                <input type="radio" name="isStyleDetail"  value="n" v-model="isStyleDetail" style="margin-top:5px!important;" />
                                <span class="font-13" >기본만 수정</span>
                            </label>
                            <label class="">
                                <input type="radio" name="isStyleDetail" value="y" v-model="isStyleDetail" style="margin-top:5px!important;"  />
                                <span class="font-13">상세 수정</span>
                            </label>
                        </div>
                    </th>
                    <th class="text-center"></th>
                </tr>
                </thead>

                <tbody v-if="0 >= productList.length">
                    <tr>
                        <td colspan="12" >스타일 없음</td>
                    </tr>
                </tbody>

                <tbody is="draggable" :list="productList"  :animation="200" tag="tbody" handle=".handle" @change="changeProductList()">
                    <tr v-for="(product, prdIndex) in productList">

                        <td :class="product.sno > 0 ? 'handle' : ''">
                            <div class="cursor-pointer hover-btn" v-show="product.sno > 0">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                            </div>
                            <div class="text-danger font-9" v-show="$.isEmpty(product.sno) || 0 >= product.sno">
                                신규
                            </div>
                        </td>

                        <td class="text-center">
                            {% prdIndex+1 %}
                            <div class="text-muted font-9">{% product.sno %}</div>
                        </td>
                        <td class="text-center">
                            <div v-show="isStyleModify">
                                <select class="js-example-basic-single sel-style border-line-gray" v-model="product.prdSeason" @change="setStyleName(product); setStyleCode(product, customer.styleCode)" style="width:100%;" >
                                    <option value="">미정</option>
                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeKey?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div v-show="!isStyleModify">
                                {% product.prdSeason %}
                            </div>
                        </td>
                        <td >
                            <div v-show="isStyleModify">
                                <select class="js-example-basic-single sel-style border-line-gray" v-model="product.prdStyle" style="width:100%;" @change="setStyleName(product); setStyleCode(product, customer.styleCode)">
                                    <option value="">미정</option>
                                    <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div v-show="!isStyleModify" class="ta-l pdl5">
                                {% styleMap[product.prdStyle] %}
                            </div>
                        </td>
                        <td >
                            <div class="ta-l pdl5">
                                <?php $modifyKey='isStyleModify'; $model='product.productName'; $placeholder='스타일명' ?>
                                <?php include 'basic_view/_text.php'?>
                            </div>
                        </td>
                        <td>
                            <?php $modifyKey='isStyleModify'; $model='product.prdExQty'; $placeholder='예상수량' ?>
                            <?php include 'basic_view/_number.php'?>
                            <div class="dev" v-if="isDev">product.prdExQty</div>
                        </td>
                        <td>
                            <?php $modifyKey='isStyleModify'; $model='product.currentPrice'; $placeholder='현재단가' ?>
                            <?php include 'basic_view/_number.php'?>
                            <div class="dev" v-if="isDev">product.currentPrice</div>
                        </td>
                        <td>
                            <?php $modifyKey='isStyleModify'; $model='product.targetPrice'; $placeholder='최소' ?>
                            <?php include 'basic_view/_number.php'?>
                            <div class="dev" v-if="isDev">product.targetPrice</div>
                        </td>
                        <td>
                            <?php $modifyKey='isStyleModify'; $model='product.targetPriceMax'; $placeholder='최대' ?>
                            <?php include 'basic_view/_number.php'?>
                            <div class="dev" v-if="isDev">product.targetPriceMax</div>
                        </td>
                        <td>
                            <?php $listIndexData="prdIndex+"; $modifyKey='isStyleModify'; $modelPrefix='styleProcType'; $model = 'product.styleProcType'; $listCode = 'styleProcType'?>
                            <?php include 'basic_view/_radio.php'?>
                            <div class="dev" v-if="isDev">product.styleProcType</div>
                        </td>
                        <td >
                            <?php $listIndexData="prdIndex+"; $modifyKey='isStyleModify'; $modelPrefix='custSampleType'; $model = 'product.addedInfo.prd002'; $listCode = 'custSampleType'?>
                            <?php include 'basic_view/_radio.php'?>
                            <div class="dev" v-if="isDev">addedInfo.prd002</div>
                        </td>
                        <td >
                            <div class="new-style2 w-100p text-left" v-show="!isStyleModify || 'y' !== isStyleDetail ">
                                <div :class="'mgt5 ' + ( 'prd008' === styleEtcKey ? 'text-danger':'' )"
                                     v-for="(styleEtc, styleEtcKey) in styleEtcListMap"
                                     v-show="!$.isEmpty(product.addedInfo[styleEtcKey])" >
                                    <b><i class='fa fa-info-circle' aria-hidden="true"></i> {% styleEtc %}</b> : {% product.addedInfo[styleEtcKey] %}
                                </div>
                            </div>
                            <div class="new-style2 w-100p text-left" v-show="isStyleModify && 'y' === isStyleDetail ">
                                <table class="table-pd-0 table-th-height0 table-td-height0 font-11" >
                                    <colgroup>
                                        <col class="w-25p">
                                        <col class="w-75p">
                                    </colgroup>
                                    <tr v-for="(styleEtc, styleEtcKey) in styleEtcListMap">
                                        <th style="padding:0 !important;text-align:left!important; padding-left:10px !important; background-color:#f9f9f9">{% styleEtc %}</th>
                                        <td style="padding:0 !important;" >
                                            <input type="text" class="form-control" v-model="product.addedInfo[styleEtcKey]" :placeholder="styleEtc" style="border:none !important; border-right: solid 1px #ddd !important;">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn btn-red btn-red-line2 btn-sm" @click="addSalesStyle()">추가</div>
                            <div class="btn btn-white btn-sm" v-show="product.sno > 0"
                                 @click="ImsService.deleteData('projectProduct',product.sno, ()=>{ refreshProductList(sno) })">
                                삭제
                            </div>
                            <div class="btn btn-white btn-sm" v-show="$.isEmpty(product.sno) || 0 >= product.sno"
                                 @click="deleteElement(productList, prdIndex)">
                                삭제
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>

            <div class="mgl10 dp-flex dp-flex-gap5 justify-content-center"  style="justify-content: center" v-show="isStyleModify">
                <div class="btn btn-red btn-red-line2 btn-lg" v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;스타일 수정&nbsp;&nbsp;</div>
                <div class="btn btn-red btn-lg"  v-show="isStyleModify" @click="saveStyleList(true)"  tabindex="0" id="btn-style-save">스타일 저장</div>
                <div class="btn btn-white btn-lg" v-show="isStyleModify" @click="isStyleModify=false">취소</div>
            </div>

        </div>

    </div>
    <!-- 수령자 정보 -->
</div>

<div class="col-xs-12" v-show="!isFactory"  v-if="!$.isEmpty(fileList)">
    <div class="col-xs-12" >

        <div class="table-title gd-help-manual">
            <div class="flo-left">영업 파일</div>
            <div class="flo-right"></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        견적서 (파일)
                    </th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileEtc2" :id="'fileEtc2'" :project="project" ></simple-file-upload>
                    </td>
                    <th>영업 확정서 (파일)</th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileEtc4" :id="'fileEtc4'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                <tr>
                    <th>
                        입찰 추가 정보
                    </th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileMeeting" :id="'fileMeeting'" :project="project" ></simple-file-upload>
                    </td>
                    <th>
                        기타파일
                    </th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileEtc7" :id="'fileEtc7'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
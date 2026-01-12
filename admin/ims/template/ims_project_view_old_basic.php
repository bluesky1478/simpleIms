<section >

    <div class="row_" v-show="!isFactory">
        <!--기본정보-->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객/프로젝트 기본 정보</div>
                <div class="flo-right">
                    <span class="radio-inline" style="font-weight: normal;font-size:12px">납기일정의 </span>
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="y" v-model="project.syncProduct"/> 스타일연동
                    </label>
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="n" v-model="project.syncProduct"/> 별도관리
                    </label>
                    <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>

                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>
                            고객사
                        </th>
                        <td>
                            <span class="font-16" >{% items.customerName %}</span>
                            <span class="text-danger">{% !$.isEmpty(items.use3plAndMall) ? `(${items.use3plAndMall})`:'' %}</span>
                            <div class="btn btn-sm btn-white" @click="openCustomer(items.sno)">상세</div>
                        </td>
                        <th>연도/시즌</th>
                        <td>
                            <select v-model="project.projectYear" class="form-control form-inline inline-block font-18" style="height: 35px; width:70px;">
                                <?php foreach($yearList as $yearEach) {?>
                                    <option><?=$yearEach?></option>
                                <?php }?>
                            </select>
                            <select v-model="project.projectSeason" class="form-control form-inline inline-block font-18" style="height: 35px; width:70px;">
                                <option >ALL</option>
                                <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                    <option><?=$seasonEn?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            프로젝트 별칭
                        </th>
                        <td>
                            <input type="text" class="form-control" v-model="project.projectName" placeholder="프로젝트 별칭" style="width:100%;height:30px;">
                        </td>
                        <th>
                            프로젝트 상태
                            <div class="btn btn-sm btn-white" @click="openProjectStatusHistory(project.sno,'')">상태변경이력</div>
                        </th>
                        <td>
                            <select2 v-model="currentStatus" style="width:150px;" >
                                <?php foreach ($projectListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                            <div class="btn btn-red" @click="setStatus(project)">변경</div>
                        </td>
                    </tr>
                    <tr>
                        <th>프로젝트 타입</th>
                        <td colspan="3">
                            <?php foreach ( $projectTypeMap as $key => $value ) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="projectType" value="<?=$key?>"  v-model="project.projectType" /><?=$value?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th >영업 담당자</th>
                        <td>
                            <select2 class="js-example-basic-single" v-model="items.salesManagerSno"  style="width:100%" >
                                <option value="0">미정</option>
                                <?php foreach ($managerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                        <th >디자인 담당자</th>
                        <td>
                            <select2 class="js-example-basic-single" v-model="project.designManagerSno"  style="width:100%" >
                                <option value="0">미정</option>
                                <?php foreach ($designManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                    </tr>
                    <tr>
                        <th >고객 제안 마감일</th>
                        <td >
                            <date-picker v-model="project.recommendDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                        <th >이노버 발주</th>
                        <td>
                            <date-picker v-model="project.msOrderDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="이노버 발주"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th >
                                <span class="sl-blue font-14">
                                    이노버 납기
                                </span>
                            <div class="mgt5" v-if="'y' === project.syncProduct">
                                <div class="block-blue">연동</div>
                            </div>
                        </th>
                        <td>
                            <date-picker v-model="project.msDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="이노버 납기"></date-picker>
                        </td>
                        <th >
                            <div class="text-danger font-14">고객 납기</div>
                            <div class="mgt5" v-if="'y' === project.syncProduct">
                                <div class="block-blue">연동</div>
                            </div>
                        </th>
                        <td>
                            <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 납기"></date-picker>
                            <div class="mgt5">
                                <div>
                                    변경여부 :
                                    <label class="radio-inline">
                                        <input type="radio" name="customerDeliveryDtConfirmed"  value="y" v-model="project.customerDeliveryDtConfirmed"/>변경가능
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="customerDeliveryDtConfirmed"  value="n" v-model="project.customerDeliveryDtConfirmed"/>변경불가
                                    </label>
                                </div>

                                <div class="mgt5">
                                    납기확정 :
                                    <label class="radio-inline">
                                        <input type="radio" name="customerDeliveryDtStatus2"  value="n" v-model="project.customerDeliveryDtStatus2"/>미확정
                                    </label>
                                    <label class="radio-inline " style="margin-left:27px">
                                        <input type="radio" name="customerDeliveryDtStatus2"  value="y" v-model="project.customerDeliveryDtStatus2"/>확정
                                    </label>
                                </div>

                                <div class="mgt5 dp-flex" >
                                    확보상태 :
                                    <select class="form-control mgl5" v-model="project.customerDeliveryDtStatus">
                                        <?php foreach($customerDeliveryStatus as $cdsKey => $cdsValue) { ?>
                                            <option value="<?=$cdsKey?>"><?=$cdsValue?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th >
                            고객 발주일
                        </th>
                        <td>
                            <date-picker v-model="project.customerOrderDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 발주"></date-picker>
                        </td>
                        <th ><span class="sl-purple font-14">발주D/L</span></th>
                        <td>
                            <date-picker v-model="project.customerOrderDeadLine" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">샘플비용 협의사항</th>
                        <td rowspan="2">
                            <div class="mgb10">
                                <label class="radio-inline">
                                    <input type="radio" name="sampleCost"  value="0"  v-model="project.sampleCost"  />미확정
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="sampleCost"  value="1"  v-model="project.sampleCost" />무상
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="sampleCost"  value="2"  v-model="project.sampleCost" />유상
                                </label>
                            </div>
                            <div class="mgt5">
                                <textarea class="form-control w100 h100" placeholder="샘플비용 협의 메모" v-model="project.sampleMemo" rows="4"></textarea>

                                <div class="btn btn-white" @click="saveTest()">테스트</div>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>배송비용 협의사항</th>
                        <td>
                            <textarea class="form-control w100 h100" v-model="project.deliveryCostMemo" placeholder="배송비용 협의사항"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>

            <div class="table-title gd-help-manual">
                <div class="flo-left">생산 기본 정보</div>
                <div class="flo-right">
                    <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr >
                        <th >메인 생산처</th>
                        <td>
                            <select2 class="js-example-basic-single" style="width:100%" v-model="project.produceCompanySno" >
                                <option value="0">미정</option>
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                        <th >
                            생산처 형태/국가
                        </th>
                        <td>
                            <div class="form-inline">
                                <select class="form-control " v-model="project.produceType">
                                    <?php foreach ($prdType as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                                <select class="form-control" v-model="project.produceNational" placeholder="선택">
                                    <option value="">미정</option>
                                    <?php foreach ($prdNational as $key => $value ) { ?>
                                        <option value="<?=$value?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th ><i class="fa fa-university fa-lg" aria-hidden="true" ></i> 3PL 사용여부</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="use3pl"  value="n"  v-model="project.use3pl" <?=empty($imsProduceCompany)?'':'disabled'?> />미사용
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="use3pl"  value="y"  v-model="project.use3pl" <?=empty($imsProduceCompany)?'':'disabled'?> />사용
                            </label>
                        </td>
                        <th ><i class="fa fa-internet-explorer fa-lg" aria-hidden="true"></i> 폐쇄몰 사용여부</th>
                        <td >
                            <label class="radio-inline">
                                <input type="radio" name="useMall"  value="n"  v-model="project.useMall" <?=empty($imsProduceCompany)?'':'disabled'?> />미사용
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="useMall"  value="y"  v-model="project.useMall" <?=empty($imsProduceCompany)?'':'disabled'?> />사용
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            3PL 바코드 파일
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileBarcode" :id="'fileBarcode'" :project="project" ></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th >분류패킹 여부</th>
                        <td colspan="3">

                            <div >
                                <label class="radio-inline">
                                    <input type="radio" name="packingYn"  value="n"  v-model="project.packingYn"  />미진행
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="packingYn"  value="y"  v-model="project.packingYn" />진행
                                </label>
                            </div>

                            <!--<div v-show="'y' === project.packingYn" >
                                <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="project" ></simple-file-upload>
                                <span class="notice-info">분류패킹 파일</span>
                            </div>-->
                        </td>
                    </tr>
                    <tr>
                        <th>
                            납품 계획/방법 메모
                        </th>
                        <td colspan="3">
                            <textarea class="form-control w50 inline-block flo-left" rows="5" v-model="project.deliveryMethod" placeholder="납품 계획/방법 메모"></textarea>
                            <div class="flo-right">
                                <simple-file-upload :file="fileList.fileDeliveryPlan" :id="'fileDeliveryPlan'" :project="project" ></simple-file-upload>
                                <div class="notice-info">납품 계획 파일</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>

        </div>

        <!--입찰/제안 정보-->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    ?입찰/제안 정보
                </div>
                <div class="flo-right">
                    <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>입찰정보</th>
                        <td colspan="3">
                            <input type="text" class="form-control" placeholder="입찰정보" v-model="project.bid" >
                        </td>
                    </tr>
                    <tr>
                        <th>제안형태</th>
                        <td colspan="3">
                            <label class="checkbox-inline">
                                <input type="checkbox" value="1" v-model="project.recommend">
                                기획서<span class="ims-recommend ims-recommend1">기</span>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="2" v-model="project.recommend">
                                제안서<span class="ims-recommend ims-recommend2">제</span>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="4" v-model="project.recommend">
                                샘플<span class="ims-recommend ims-recommend4">샘</span>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="8" v-model="project.recommend">
                                견적<span class="ims-recommend ims-recommend8">견</span>
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--퀄리티 정보-->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    퀄리티 정보 <span class="notice-info">(상태는 스타일별 개별관리)</span>
                </div>
                <div class="flo-right">
                    <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>퀄리티메모/정보</th>
                        <td colspan="2" >
                            <textarea class="form-control w100 h100" rows="3" v-model="project.fabricStatusMemo" placeholder="퀄리티 수배상태 메모" disabled></textarea>
                        </td>
                        <td >
                            <div>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="1" v-model="project.fabricNational" disabled>
                                    <span class="flag flag-16 flag-kr">
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="2" v-model="project.fabricNational" disabled>
                                    <span class="flag flag-16 flag-cn">
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="4" v-model="project.fabricNational" disabled>
                                    <span class="flag flag-16 flag-market">
                                </label>
                            </div>

                            <select class="form-control font-17 mgt10" style="height: 50px;width:100%" v-model="project.fabricStatus" disabled>
                                <option value="0">미확보</option>
                                <option value="1">확보중</option>
                                <option value="2">확보완료</option>
                            </select>
                        </td>
                    </tr>
                    <!--<tr>
                        <th>퀄리티확보(과거참조)</th>
                        <td colspan="99">
                            <div class="btn btn-white">MIG</div>
                        </td>
                    </tr>-->
                    </tbody>
                </table>
            </div>
        </div>

        <!--디자인실정보-->


    </div>

    <!--<div class="row" v-show="!isFactory">-->
    <div class="row" v-show="false">

        <div class="col-xs-6" >
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">프로젝트 진행 상태</div>
                    <div class="flo-right">
                        <!--<div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                        <div class="btn btn-red" @click="openProject(project.sno)">수정</div>-->
                    </div>
                </div>

                <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                    <!--<colgroup>
                        <col style="width:12%">
                        <col style="width:12%">
                        <col style="width:12%">
                        <col style="width:12%">
                        <col style="width:12%">
                        <col style="width:12%">
                        <col style="width:12%">
                    </colgroup>-->
                    <tr>
                        <th></th>
                        <th>기획</th>
                        <th>제안</th>
                        <th>샘플</th>
                        <!--<th>견적발송</th>-->
                        <th>고객사양서</th>
                        <th>고객발주</th>
                        <th>판매구매확정</th>
                    </tr>
                    <tr>
                        <th>예정</th>
                        <td>
                            {% project.planDtShort %}
                        </td>
                        <td>
                            {% project.proposalDtShort %}
                        </td>
                        <td>
                            {% $.formatShortDate(project.sampleStartDt) %}
                        </td>
                        <td>
                            -
                        </td>
                        <td>
                            -
                        </td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>완료</th>
                        <td>
                            {% project.planEndDtShort %}
                        </td>
                        <td>
                            {% project.proposalEndDtShort %}
                        </td>
                        <td>
                            {% $.formatShortDate(project.sampleEndDt) %}
                        </td>
                        <td>
                            <!--사양서-->
                            {% $.formatShortDate(project.customerOrderConfirmDt) %}
                        </td>
                        <td>
                            <!--발주-->
                            {% $.formatShortDate(project.customerOrder2ConfirmDt) %}
                        </td>
                        <td>
                            <!--판매구매-->
                            {% $.formatShortDate(project.customerSaleConfirmDt) %}
                        </td>
                    </tr>
                    <tr>
                        <th>상태</th>
                        <td>
                            <span :class="setAcceptClass(project.planConfirm)" v-html="project.planConfirmKr"></span>
                        </td>
                        <td>
                            <span :class="setAcceptClass(project.proposalConfirm)" v-html="project.proposalConfirmKr"></span>
                        </td>
                        <td>
                            <span :class="setAcceptClass(project.sampleConfirm)" v-html="project.sampleConfirmKr"></span>
                        </td>
                        <td>
                            <span v-show="'y' === project.customerOrderConfirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerOrderConfirm">미확정</span>
                        </td>
                        <td>
                            <span v-show="'y' === project.customerOrder2Confirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerOrder2Confirm">미확정</span>
                        </td>
                        <td>
                            <span v-show="'y' === project.customerSaleConfirm" class="text-green">확정</span>
                            <span v-show="'n' === project.customerSaleConfirm">미확정</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">생산준비 진행 상태</div>
                    <div class="flo-right">
                        <!--<div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                        <div class="btn btn-red" @click="openProject(project.sno)">수정</div>-->
                    </div>
                </div>

                <table class="table table-cols w100 table-default-center table-pd-0 table-th-height30">
                    <colgroup>
                        <col style="width:10%">
                        <col style="width:10%">
                        <col style="width:10%">
                        <col style="width:10%">
                        <col style="width:10%">
                        <col style="width:10%">
                        <col style="width:10%">
                    </colgroup>
                    <tr>
                        <th>항목</th>
                        <th>퀄리티</th>
                        <th>BT</th>
                        <th>가견적</th>
                        <th>생산가확정</th>
                        <th>작업지시서</th>
                        <th>생산진행</th>
                    </tr>
                    <tr>
                        <th>상태</th>
                        <td :class="project.fabricStatusColor">
                            <div class="font-11">{% project.fabricStatusKr %}</div>
                            <div class="flag flag-16 flag-kr" v-if="1 & project.fabricNational"></div>
                            <div class="flag flag-16 flag-cn" v-if="2 & project.fabricNational"></div>
                            <div class="flag flag-16 flag-market" v-if="4 & project.fabricNational"></div>
                        </td>
                        <td>
                            <span v-html="project.btStatusIcon"></span>
                        </td>
                        <td >
                            <span v-html="project.estimateStatusIcon"></span>
                        </td>
                        <td>
                            <span v-html="project.costStatusIcon"></span>
                        </td>
                        <td>
                            <span v-html="project.workStatusIcon"></span>
                        </td>
                        <td>
                            <span v-html="$.getStatusIcon(project.productionStatus)"></span>
                        </td>
                    </tr>
                    <!--                        <tr>
                                                <th>항목</th>
                                                <th>아소트</th>
                                                <th>작지작업</th>
                                                <th>작지파일</th>
                                                <th>고객견적파일</th>
                                                <th>영업확정파일</th>
                                            </tr>
                                            <tr>
                                                <th>상태</th>
                                                <td>
                                                    <span class="text-muted">아소트</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">작지작업</span>
                                                </td>
                                                <td >
                                                    <span class="text-muted">작지파일</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">고객견적파일</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">영업확정파일</span>
                                                </td>
                                            </tr>-->
                </table>
            </div>

        </div>

        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객 확정 상태</div>
                <div class="flo-right">
                    <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th style="height:90px">
                            고객 견적서 확정
                        </th>
                        <td >
                            <label class="radio-inline">
                                <input type="radio" name="customerEstimateConfirm"  value="n"  v-model="project.customerEstimateConfirm"  />미확정
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="customerEstimateConfirm"  value="y"  v-model="project.customerEstimateConfirm" />확정
                            </label>

                            <div v-show="'y' === project.customerEstimateConfirm" class="mgt10">
                                확정일자 :
                                <date-picker v-model="project.customerEstimateConfirmDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="확정일자" style="width:160px!important;"></date-picker>
                                <span class="notice-info">확정날짜 공백시 오늘 날짜로 자동입력</span>
                            </div>

                            <div v-show=" 50 == project.projectStatus" class="mgt10">
                                최초 대기일자 : {% project.customerWaitDt  %}
                                <div class="font-16">
                                    <span v-html="project.customerWaitDtRemain"></span>
                                </div>
                            </div>
                        </td>
                        <th>고객 사양서 확정</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="customerOrderConfirm"  value="n"  v-model="project.customerOrderConfirm"  />미확정
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="customerOrderConfirm"  value="y"  v-model="project.customerOrderConfirm" />확정
                            </label>
                            <div v-show="'y' === project.customerOrderConfirm" class="mgt10">
                                확정일자 :
                                <date-picker v-model="project.customerOrderConfirmDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="확정일자" style="width:160px!important;"></date-picker>
                                <span class="notice-info">확정날짜 공백시 오늘 날짜로 자동입력</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th style="height:90px">
                            고객 발주 확정
                        </th>
                        <td >
                            <label class="radio-inline">
                                <input type="radio" name="customerOrder2Confirm"  value="n"  v-model="project.customerOrder2Confirm"  />미확정
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="customerOrder2Confirm"  value="y"  v-model="project.customerOrder2Confirm" />확정
                            </label>
                            <div v-show="'y' === project.customerOrder2Confirm" class="mgt10">
                                확정일자 :
                                <date-picker v-model="project.customerOrder2ConfirmDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="확정일자" style="width:160px!important;"></date-picker>
                                <span class="notice-info">확정날짜 공백시 오늘 날짜로 자동입력</span>
                            </div>
                        </td>
                        <th>판매구매 확정</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="customerSaleConfirm"  value="n"  v-model="project.customerSaleConfirm"  />미확정
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="customerSaleConfirm"  value="y"  v-model="project.customerSaleConfirm" />확정
                            </label>
                            <div v-show="'y' === project.customerSaleConfirm" class="mgt10">
                                확정일자 :
                                <date-picker v-model="project.customerSaleConfirmDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="확정일자" style="width:160px!important;"></date-picker>
                                <span class="notice-info">확정날짜 공백시 오늘 날짜로 자동입력</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!--파일 관리-->
    <div class="row_">
        <div class="col-xs-12" v-show="!isFactory">
            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 파일</div>
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
                            견적서
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileEtc2" :id="'fileEtc2'" :project="project" ></simple-file-upload>
                        </td>
                        <th>영업 확정서</th>
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
                    </tr>
                    <tr>
                        <th>
                            납품 보고서
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileDeliveryReport" :id="'fileDeliveryReport'" :project="project" ></simple-file-upload>
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

        <div class="col-xs-12">
            <table class="table table-cols" >
                <thead>
                <tr>
                    <th >번호</th>
                    <th>이미지</th>
                    <th>상품명</th>
                    <th>예정수량</th>
                </tr>
                </thead>
                <tbody v-for="(product, prdIndex) in viewProductList" >
                <tr>
                    <td rowspan="2" ><!--번호-->
                        {% prdIndex+1 %}
                        <div class="text-mutㅌㅌed font-11">#{% product.sno %}</div>
                    </td>
                    <td ><!--이미지-->
                        <span class="hover-btn cursor-pointer"  v-if="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)">
                            <img src="/data/commonimg/ico_noimg_75.gif" class="middle" width="40">
                        </span>
                        <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)">
                            <img :src="product.fileThumbnail" class="middle" width="60" height="60" >
                        </span>
                        <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnailReal,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnailReal)">
                            <img :src="product.fileThumbnailReal" class="middle" width="60" height="60">
                        </span>
                    </td>

                    <td class="pdl5 ta-l" ><!--스타일명-->

                        <span class="" >
                            {% product.productName %}
                        </span>
                        <span class="text-muted">{% product.styleCode.toUpperCase() %}</span>
                        <br>

                        <div v-if="typeof workFileList[product.sno] != 'undefined'" style="display: flex" class="font-11 pdt5 text-muted">
                            작업지시서 :
                            <ul class="ims-file-list" >
                                <li class="hover-btn" v-for="(file, fileIndex) in workFileList[product.sno].files">
                                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td class=""><!--제작수량-->
                        <span class="">{% $.setNumberFormat(product.prdExQty) %}장</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" colspan="99">
                        <table class="table table-pd-5 table-default-center border-top-none" style="border-top:none !important;">
                            <colgroup>
                                <col class="w-120px">
                                <col class="w-12p"><!--원단명-->
                                <col style="width:200px"><!--퀄리티 상태-->
                                <col class="w-14p"><!--확정정보-->
                                <col class="w-14p"><!--메모-->
                                <col class="w-150px"><!--BT상태-->
                                <col class="w-14p"><!--확정정보-->
                                <col class="w-14p"><!--확정메모-->
                                <col class="w-150px"><!--위치-->
                                <col class="w-200px"><!--부착위치-->
                                <col class="w-150px"><!--혼용률-->
                                <col class="w-150px"><!--컬러-->
                                <col class="w-100px"><!--제조국-->
                            </colgroup>
                            <tr >
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">원단번호</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">원단명</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">퀄리티상태</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">퀄리티확정정보</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">퀄리티메모</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">BT상태</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">BT확정정보</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">BT메모</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">위치</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">부착위치</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">혼용률</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">컬러</th>
                                <th class="text-muted"  style="border-top:none !important; font-weight:normal !important">제조국</th>
                            </tr>
                            <tr v-for="fabric in product.usedFabricList">
                                <td>{% fabric.sno %}</td>
                                <td>
                                    {% fabric.fabricName %}
                                </td>
                                <td>{% fabric.fabricStatusKr %}</td>
                                <td class="text-left pdl5">{% fabric.fabricConfirmInfo %}</td>
                                <td class="text-left pdl5">{% fabric.fabricMemo %}</td>
                                <td>{% fabric.btStatusKr %}</td>
                                <td  class="text-left pdl5">
                                    {% fabric.btConfirmInfo %}
                                    <li class="hover-btn" v-for="(file, fileIndex) in fabric.fileList.btFile2.files">
                                        <a :href="'<?=$nasDownloadUrl?>name='+file.fileName+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                    </li>
                                </td>
                                <td class="text-left pdl5">{% fabric.btMemo %}</td>
                                <td>{% fabric.position %}</td>
                                <td>{% fabric.attached %}</td>
                                <td>{% fabric.fabricMix %}</td>
                                <td>{% fabric.color %}</td>
                                <td>
                                    <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-if="!$.isEmpty(fabric.makeNational)" ></i>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <!--TODO : 추후 신/구 구분하여 표기-->
        <div class="col-xs-12" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">IMS 프로젝트 파일</div>
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
                            샘플 의뢰서
                        </th>
                        <td >
                            <simple-file-upload :file="fileList.fileSample" :id="'fileSample'" :project="project" :accept="true" ></simple-file-upload>
                            <simple-file-upload :file="fileList.sampleFile1" :id="'sampleFile1'" :project="project" :accept="true" ></simple-file-upload>
                        </td>
                        <th>샘플 패턴</th>
                        <td>
                            <simple-file-upload :file="fileList.filePattern" :id="'filePattern'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                        <th>
                            샘플 웨어링
                        </th>
                        <td >
                            <simple-file-upload :file="fileList.fileEtc5" :id="'fileEtc5'" :project="project" :accept="true" ></simple-file-upload>
                        </td>
                        <th>샘플 기타파일</th>
                        <td>
                            <simple-file-upload :file="fileList.fileSampleEtc" :id="'fileSampleEtc'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            샘플 실물사진
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileSampleConfirm" :id="'fileSampleConfirm'" :project="project" :accept="true" ></simple-file-upload>
                        </td>
                        <th>
                            작업지시서
                        </th>
                        <td colspan="99">
                            <simple-file-upload :file="fileList.fileWork" :id="'fileWork'" :project="project" :accept="true" ></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            케어라벨&마크
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileCareMark" :id="'fileCareMark'" :project="project" :accept="true" ></simple-file-upload>
                        </td>
                        <th>원부자재내역</th>
                        <td colspan="99">
                            <simple-file-upload :file="fileList.fileEtc6" :id="'fileEtc6'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            분류패킹 파일
                        </th>
                        <td colspan="99">
                            <simple-file-only-not-history-upload :file="fileList.filePacking" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row_">
        <div class="col-xs-12" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">구 생산 파일</div>
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
                            세탁 및 이화학검사
                        </th>
                        <td colspan="3">
                            <!--<simple-file-upload :file="fileList.prdStep10" :id="'prdStep10'" :project="project" :accept="true"></simple-file-upload>-->
                            <simple-file-upload :file="fileList.prdStep10" :id="'prdStep10'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                        <th>원부자재 확정</th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep20" :id="'prdStep20'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            원부자재 선적
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep30" :id="'prdStep30'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                        <th>QC</th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep40" :id="'prdStep40'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            인라인
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep50" :id="'prdStep50'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                        <th>선적</th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep60" :id="'prdStep60'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            도착
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep70" :id="'prdStep70'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                        <th>입고제품검수</th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.prdStep80" :id="'prdStep80'" :project="project" :accept="true"></simple-file-upload>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>
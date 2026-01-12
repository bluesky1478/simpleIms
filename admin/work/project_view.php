
<?php include 'import_lib.php'?>

<style>
    .swal2-input-label { font-weight:bold; font-size:18px;  }
    .swal2-popup { width:700px }
    .swal2-content { font-size:13px; }
    .swal2-confirm { font-size:13px; }
    .bootstrap-filestyle { display:none }
</style>


<form id="project-app" name="frmOrder" class="frm-order">
    <div class="page-header js-affix">
        <h3>{% items.projectName %}</h3>
        <div class="btn-group">
            <input type="button" value="저장" class="btn btn-red js-register" @click="saveProject()" />
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" >
            <b class="font-15">프로젝트번호 : </b><b class="font-15 text-danger">{% items.sno %}</b>
            <?= str_repeat('&nbsp', 4); ?>|<?= str_repeat('&nbsp', 4); ?> 프로젝트 타입 : {% items.projectTypeKr %}
            <?= str_repeat('&nbsp', 4); ?>|<?= str_repeat('&nbsp', 4); ?> 진행상태 : <b class="">{% items.projectStatusKr %} 단계</b>
            <?= str_repeat('&nbsp', 4); ?>|<?= str_repeat('&nbsp', 4); ?> 등록일시 : {% items.regDt %}
            <?= str_repeat('&nbsp', 4); ?>|<?= str_repeat('&nbsp', 4); ?> 등록자 : {% items.regManagerName %}
        </div>
    </div>

    <div class="row">
        <div class="row">
            <div class="col-xs-12">
                <!--좌측-->
                <div class="col-xs-12">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">기본 정보</div>
                        <div class="flo-right">
                            <button type="button" class="btn btn-red btn-sm btn-unit-save">저장</button>
                        </div>
                    </div>
                    <div>
                        <table class="table table-cols " style="margin-bottom:0px">
                            <colgroup>
                                <col class="width-md"/>
                                <col/>
                                <col class="width-md"/>
                                <col/>
                            </colgroup>
                            <tr>
                                <th>프로젝트명</th>
                                <td>
                                    <input type="text" class="form-control" v-model="items.projectName">
                                </td>
                                <th>고객사</th>
                                <td>
                                    <select2 class="js-example-basic-single" style="width:50%"  v-model="items.companySno" >
                                        <option value="">선택</option>
                                        <?php foreach ($companyListMap as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                            </tr>
                            <tr>
                                <th>진행상태</th>
                                <td>
                                    <?= gd_select_box('projectStatus', 'projectStatus', $PRJ_STATUS, null, null, null, 'v-model="items.projectStatus"  ', 'form-control '); ?>
                                </td>
                                <th>프로젝트 타입</th>
                                <td>
                                    <?= gd_select_box('projectType', 'projectType', $MS_PROPOSAL_TYPE, null, null, null, 'v-model="items.projectType"', 'form-control'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>미팅일</th>
                                <td>
                                    <date-picker v-model="items.meetingDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                                <th rowspan="2">프로젝트 설명</th>
                                <td rowspan="2">
                                    <textarea class="form-control pd5 full-text-area" v-model="items.description"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>희망납기</th>
                                <td>
                                    <date-picker v-model="items.hopeDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                            </tr>
                            <tr>
                                <th>발주데드라인</th>
                                <td>
                                    <date-picker v-model="items.deadlineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                                <th>
                                    업체 구분
                                </th>
                                <td colspan="3">
                                    <?php foreach($COMP_DIV as $key => $value) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" value="<?=$key?>" name="companyDiv" v-model="items.companyDiv" /><?=$value?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>영업 담당자</th>
                                <td>
                                    <?= gd_select_box(null, null, $managerList, null, $acceptData['managerSno'], '선택', 'v-model="items.salesManagerSno"', 'form-control'); ?>
                                </td>
                                <th>디자인 담당자</th>
                                <td>
                                    <?= gd_select_box(null, null, $managerList, null, $acceptData['managerSno'], '선택', 'v-model="items.designManagerSno"', 'form-control'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    고객 안내 일정
                                    <br>
                                    <div class="btn btn-white btn-sm" @click="window.open('<?=$previewUrl?>')">고객 안내페이지 확인</div>
                                </th>
                                <td colspan="3">
                                    <table class="table doc-step-table " style="width:1350px">
                                        <colgroup>
                                            <col />
                                            <col class="w15" v-for="(planItem, planKey) in items.customerPlanDt"  />
                                        </colgroup>
                                        <tr>
                                            <td class="text-center" style="width:80px !important;">
                                                <div class="btn btn-red btn-sm btn-mod-plan" v-if="true == items.isPlanDtModify && planModifySw "  @click="completeModify()">수정완료</div>
                                                <div class="btn btn-gray btn-sm btn-mod-plan" v-if="true == items.isPlanDtModify && !planModifySw " @click="planModifySw = true">일정수정</div>

                                                <div class="btn btn-white btn-sm" v-if="true == items.isPlanDtModify && !planModifySw" onclick="window.open('popup_plan.php?projectSno=<?=$requestParam['sno']?>', 'plan_history_popup', 'width=1400, height=910, resizable=yes, scrollbars=yes');">수정이력</div>
                                                <div class="btn btn-gray btn-sm" v-if="true == items.isPlanDtModify && planModifySw" @click="planModifySw = false">수정취소</div>
                                            </td>
                                            <td class="text-center " v-for="(planItem, planKey) in items.customerPlanDt" >
                                                <div class="step-active" v-if="planKey == items.customerPlanStatus">
                                                    ▶ <span class="btn-set-step">{% planItem.title %}
                                                        <br><small class="text-muted" style="font-weight:normal">(현재단계)</small> </span>
                                                </div>
                                                <div v-else>
                                                    <a class="btn-set-step" style="cursor:pointer" @click="setStep(planKey)">{% planItem.title %}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">진행계획 ▶</td>
                                            <td class="text-center" v-for="(planItem, planKey) in items.customerPlanDt" >
                                                <div v-if="(true != items.isPlanDtModify || planModifySw) ">
                                                    <date-picker v-model="planItem.planDt" value-type="format" format="YYYY-MM-DD" placeholder="0000-00-00"  :lang="lang"></date-picker>
                                                </div>
                                                <div class="input-group text-center" v-else>
                                                    {% planItem.planDt %}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">고객확정 ▶</td>
                                            <td class="text-center"><small class="text-muted">확정없음</small></td>
                                            <td class="text-center">
                                                <div v-if="'y' === items.planData.DESIGN.typeDoc[20].document.isCustomerApplyFl">
                                                    {% items.planData.DESIGN.typeDoc[20].document.isCustomerApplyDt %}
                                                </div>
                                                <div v-else>
                                                    -
                                                </div>
                                            </td>
                                            <td class="text-center"><small class="text-muted">확정없음</small></td>
                                            <td class="text-center">
                                                <div v-if="'y' === items.planData.ORDER2.typeDoc[10].document.isCustomerApplyFl">
                                                    {% items.planData.ORDER2.typeDoc[10].document.isCustomerApplyDt %}
                                                </div>
                                                <div v-else>
                                                    -
                                                </div>
                                            </td>
                                            <td class="text-center"><small class="text-muted">확정없음</small></td>
                                            <td class="text-center"><small class="text-muted">확정없음</small></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div>
                            <small class="text-danger">* 고객에게 최초 메일(미팅보고서) 발송 후 일정 수정시 수정사유 입력이 필수입니다.</small>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php foreach( $projectStepData as $stepData ) { ?>

        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-12" >
                    <div class="table-title gd-help-manual">
                        <div class="flo-left"><?=$stepData['name']?></div>
                        <div class="flo-right">
                            <button type="button" class="btn btn-red btn-sm btn-unit-save">저장</button>
                        </div>
                    </div>
                    <div>
                        <table class="table table-cols ">
                            <colgroup>
                                <col class="w150p"/><!--처리부서-->
                                <col class="w200p"/><!--예정일-->
                                <col style="min-width:200px"/><!--문서명-->
                                <col class="w200p"/><!--버전-->
                                <col class="w100p"/><!--등록자-->
                                <col class="w350p"/><!--승인정보-->
                                <col class="w100p"/><!--문서승인-->
                                <col class="w100p"/><!--고객승인-->
                                <col class="w100p"/><!--메일발송-->
                            </colgroup>
                            <tr>
                                <th class="text-center"><?=$stepData['firstTitle']?></th>
                                <th class="text-center">예정일/확정일</th>
                                <th>문서명</th>
                                <th>버전</th>
                                <th class="text-center">등록자</th>
                                <th class="text-center">승인정보</th>
                                <th class="text-center">문서승인</th>
                                <th class="text-center">고객승인</th>
                                <th class="text-center">메일발송</th>
                            </tr>
                            <?php foreach( $stepData['dept'] as $dept) { ?>
                            <tr v-for="(item, itemKey) in items.planData.<?=$dept?>.typeDoc" :data-key="itemKey">
                                <th :rowspan="items.planData.<?=$dept?>.documentCount" v-if="10 == itemKey"  class="text-center" style="height:65px;">
                                    {% items.planData.<?=$dept?>.typeName %}
                                </th>
                                <td :rowspan="items.planData.<?=$dept?>.documentCount" v-if="10 == itemKey"  class="text-left">
                                    <!--예정일-->
                                    <div class="form-inline ">
                                        예정일 : <date-picker v-model="items.planData.<?=$dept?>.planDt" value-type="format" format="YYYY-MM-DD" placeholder="0000-00-00"  :lang="lang"></date-picker>
                                    </div>
                                    <div v-if="'y' === items.planData.<?=$dept?>.docAccept">
                                        확정일 : <b class="sl-blue font-17 mgl5">{% items.planData.<?=$dept?>.confirmDt %}</b>
                                    </div>
                                </td>
                                <td>
                                    <span v-if="item.document.length != 0" @click="showDocument(item.document.sno, '<?=$dept?>')" class="cursor-pointer hover-btn">{% item.name %}</span>
                                    <span v-else>{% item.name %}</span>
                                </td>
                                <td>
                                    <div v-if="item.document.length != 0">
                                        <b v-if="'y' === item.document.tempFl" class="text-danger">임시저장 자료</b>
                                        <b v-if="'y' !== item.document.tempFl" class="text-danger">{% item.document.version %}</b>
                                        <div class="btn btn-sm btn-white" @click="showDocument(item.document.sno, '<?=$dept?>')">확인</div>
                                        <div class="btn btn-sm btn-white" @click="regDocument('<?=$dept?>', itemKey)">새로등록</div>

                                        <div class="btn btn-sm btn-white" v-if="item.document.version > 1" @click="window.open('document_list.php?docDept=<?=$dept?>&docType=' + itemKey + '&key=c.companyName&keyword=' + items.companyData.companyName)">리스트</div>
                                    </div>
                                    <div v-if="item.document.length == 0">
                                        <b class="text-muted"> 미등록 </b>
                                        <div class="btn btn-sm btn-white" @click="regDocument('<?=$dept?>', itemKey)">신규등록</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {% item.document.regManagerName %}
                                </td>
                                <td class="text-left">
                                    <div class="box-wrap">
                                        <div class="box-line project-list-accept" v-for="(acceptItem, acceptItemKey) in item.document.applyManagers" >
                                            <div><small class="text-muted font-11">({% acceptItem.title %})</small></div>
                                            <div>{% acceptItem.managerNm %}</div>
                                            <div class="accept-icon" v-if="'y' === acceptItem.status">승인</div>
                                            <div class="accept-icon" v-if="'p' === acceptItem.status">PASS</div>
                                            <div class="reject-icon" v-if="'r' === acceptItem.status">반려</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div v-if="!$.isEmpty(item.accept)" >
                                        <span v-if="'y' === item.document.isApplyFl" class="sl-blue">승인완료</span>
                                        <span v-if="'r' === item.document.isApplyFl" class="text-danger"><b>반려</b></span>
                                        <span v-if="'r' !== item.document.isApplyFl && 'y' !== item.document.isApplyFl" class="text-danger">미승인</span>
                                    </div>
                                    <div v-if="$.isEmpty(item.accept)" class="text-muted">
                                        승인없음
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div v-if="!$.isEmpty(item.customerConfirm)" >
                                        <div v-if="'y' === item.document.isCustomerApplyFl" class="sl-blue">승인<br><small>({% item.document.isCustomerApplyDt %})</small></div>
                                        <div class="text-danger" v-else>미승인</div>
                                    </div>
                                    <div v-if="$.isEmpty(item.customerConfirm)" class="text-muted">
                                        승인없음
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div v-if="!$.isEmpty(item.mailLink)" >
                                        <div v-if="!$.isEmpty(item.document.sendDt) && '-' !== item.document.sendDt " class="sl-blue">
                                            {% item.document.sendDt %}
                                        </div>
                                        <div v-else class="text-danger">
                                            미발송
                                        </div>
                                    </div>
                                    <div v-if="$.isEmpty(item.mailLink)" class="text-muted">
                                        메일없음
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <?php } ?>

        <!--생산처리-->
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-12" >
                    <div class="table-title gd-help-manual" >
                        <div class="flo-left">생산처리</div>
                        <div class="flo-right">
                            <div class="btn btn-red-line" @click="addProduct()">상품추가</div>
                            <div class="btn btn-red-line" @click="addProductByWork()" v-if="!$.isEmpty(items.planData.ORDER3.typeDoc[10].document) && items.planData.ORDER3.typeDoc[10].document.version > 0 ">작업지시서 상품 추가</div>
                        </div>
                    </div>
                    <div >
                        <table class="table table-cols mgt10">
                            <colgroup>
                                <col />
                                <?php foreach($productPlanList as $planField){?>
                                    <col style="width:110px;" />
                                <?php }?>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th rowspan="2">번호</th>
                                    <th colspan="2" class="">품목명</th>
                                    <th >수량</th>
                                    <th colspan="2">생산처</th>
                                    <th colspan="3">생산처 비고</th>
                                    <th colspan="3">고객사 비고</th>
                                    <th colspan="99">&nbsp;</th>
                                </tr>
                                <tr>
                                    <th class="text-left">예상/확정</th>
                                    <?php foreach($productPlanList as $planField){?>
                                        <th class="text-left"><?=$planField['planName']?></th>
                                    <?php }?>
                                </tr>
                            </thead>
                            <tbody v-for="(productItem, productKey) in items.productData">
                                <tr>
                                    <td rowspan="3" class="text-center">{% productKey+1 %}</td>
                                    <td colspan="2" class="pd2">
                                        <input type="text" class="form-control" v-model="productItem.prdName" placeholder="품목명 입력">
                                    </td>
                                    <td >
                                        <input type="number" class="form-control" v-model="productItem.count" placeholder="수량">
                                    </td>
                                    <td colspan="2">
                                        <select2 class="js-example-basic-single form-control"  v-model="productItem.factorySno" style="width:100%">
                                            <option value="">선택</option>
                                            <?php foreach( $sampleFactoryList as $factory) { ?>
                                                <option value="<?=$factory['sno']?>"><?=$factory['factoryName']?></option>
                                            <?php } ?>
                                        </select2>
                                    </td>
                                    <td colspan="3">
                                        <input type="text" class="form-control" v-model="productItem.companyEtc" placeholder="생산처 비고사항">
                                    </td>
                                    <td colspan="3">
                                        <input type="text" class="form-control" v-model="productItem.factoryEtc" placeholder="고객사 비고사항">
                                    </td>
                                    <td colspan="99">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>예상일 ▶</td>
                                    <td v-for="(planItem, planKey) in productItem.producePlan" class="project-date-field">
                                        <date-picker v-model="planItem.planDt" value-type="format" format="YYYY-MM-DD" :placeholder="productPlanList[planKey].planNameShort"  :lang="lang"></date-picker>
                                    </td>
                                </tr>
                                <tr>
                                    <td>확정일 ▶</td>
                                    <td v-for="(planItem, planKey) in productItem.producePlan" class="project-date-field">
                                        <date-picker v-model="planItem.completeDt" value-type="format" format="YYYY-MM-DD"  :placeholder="productPlanList[planKey].planNameShort"  :lang="lang"></date-picker>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody v-if="0 >= items.productData.length">
                                <tr>
                                    <td colspan="99" class="text-center">데이터가 없습니다.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>

</form>

<?php include 'project_view_script.php'?>
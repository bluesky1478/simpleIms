<style>
    .page-header { margin-bottom:10px; }
    .ims-schedule-table td { padding:5px 20px!important; }
</style>

<?php use Component\Ims\ImsCodeMap;
include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<div id="imsApp" class="project-view" v-if="!$.isEmpty(customer)">

    <span v-show="false">
        <!--DL계산-->
        {% computedDeadLine %}
    </span>

    <?php include './admin/ims/library_nk_sch_multi_modal.php'?>
    <div class="page-header js-affix">
        <h3>
            <span class="text-danger" >
                {% mainData.sno %}
            </span>
            <span>
                <span class="text-blue cursor-pointer hover-btn" @click="openCustomer(customer.sno,'comment')">{% customer.customerName %}</span>
                {% mainData.projectYear %}
                {% mainData.projectSeason %}
            </span> 영업 기획서 (리오더)

        </h3>
        <div class="btn-group">
            <span v-show="!isModify" @click="isModify = true;" class="btn btn-white btn-red btn-red-line2" style="line-height:37px;">수정</span>
            <span v-show="isModify" @click="save();" class="btn btn-red" style="line-height:37px;">저장</span>
            <span v-show="isModify && oUpsertForm.sno > 0" @click="isModify = false;" class="btn btn-red btn-red-line2" style="line-height:37px;">수정취소</span>
            <span @click="self.close()" class="btn btn-white" style="line-height:37px;">닫기</span>
        </div>
    </div>

    <div class="row">

        <div class="col-xs-8" style="padding: 0 15px 10px;">
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30 table-pd-4 mgb5" style="border:solid 1px #e6e6e6" >
                <colgroup>
                    <col class="w-10p">
                    <col class="w-25p">
                    <col class="w-10p">
                    <col class="w-35p">
                </colgroup>
                <tbody>
                <tr>
                    <th>영업 전략</th>
                    <td>
                        <div v-if="!$.isEmptyAll(fileList.fileSalesStrategy)">
                            <div class="dp-flex font-11" v-show="!isModify">
                                <simple-file-list :files="fileList.fileSalesStrategy.files" ></simple-file-list>
                                {% fileList.fileSalesStrategy.memo %}
                                {% fileList.fileSalesStrategy.title %}
                            </div>
                            <file-upload :file="fileList.fileSalesStrategy" :id="'fileSalesStrategy'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                        </div>
                    </td>
                    <th rowspan="4">
                        영업메모(How-To)
                        <div class="font-11">(계획/전략 요약 메모)</div>
                    </th>
                    <td rowspan="4">
                        <div class="bg-light-gray3 round-box w-100p " style="height:100%; overflow-y: auto;" v-html="$.nl2br(mainData.salesMemo)" v-show="!isModify"></div>
                        <textarea class="form-control inline-block flo-left h100p w-100p" rows="5" placeholder="납품 계획/방법 메모" v-model="mainData.salesMemo" v-show="isModify"></textarea>
                    </td>
                </tr>
                <tr>
                    <th>고객사 회의록</th>
                    <td>
                        <div v-if="!$.isEmptyAll(fileList.fileMeetingReport)">
                            <div class="dp-flex font-11" v-show="!isModify">
                                <simple-file-list :files="fileList.fileMeetingReport.files" ></simple-file-list>
                                {% fileList.fileMeetingReport.memo %}
                                {% fileList.fileMeetingReport.title %}
                            </div>
                            <file-upload :file="fileList.fileMeetingReport" :id="'fileMeetingReport'" :project="mainData" :accept="false" v-show="isModify"></file-upload>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>업무 시작 예정일</th>
                    <td>
                        <?php $model='mainData.salesStartDt'; $placeholder='업무 시작 예정일' ?>
                        <?php include './admin/ims/ims25/component/_picker.php'?>
                    </td>
                </tr>
                <tr>
                    <th>고객 납기 예정일</th>
                    <td>
                        <?php $model='mainData.customerDeliveryDt'; $placeholder='고객 납기 예정일' ?>
                        <?php include './admin/ims/ims25/component/_picker.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="notice-info">고객 납기일 - 업무 시작일로 스케쥴D/L이 계산 됩니다.</div>
        </div>
        <div class="col-xs-4" style="padding: 0 15px 10px;">

            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30 table-pd-4 mgb5" style="border:solid 1px #e6e6e6" >
                <colgroup>
                    <col class="w-15p" />
                    <col class="w-80p" />
                </colgroup>
                <tr>
                    <th class="ta-c">결재</th>
                    <td>
                        <!--영업기획서 결재 라인-->
                        <div class="ta-l pdl5 pdt5" style="height:110px" v-show="projectApprovalInfo.salesPlan.sno > -1">
                            <div class="btn btn-red btn-red-line2"
                                 v-if=" 'n' === mainData.salesPlanApproval || 0 >= Number(projectApprovalInfo.salesPlan.sno) "
                                 @click="openApprovalWrite(customer.sno, mainData.sno, 'salesPlan')">
                                결재요청
                            </div>

                            <div class="font-13 pd10" >
                                <approval-template4
                                        :project="mainData"
                                        :approval="projectApprovalInfo"
                                        :confirm-type="'salesPlan'"
                                        :confirm-field="'salesPlanApproval'"
                                        :memo-field="'unused'"
                                ></approval-template4>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-xs-12 " style="padding: 0px 15px 10px;">
            <div class="">
                <?php
                $defaultText='미확인';
                $divColWidth='w-6p';
                $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 제안 일정';
                $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_SUMMARY;
                ?>
                <?php include './admin/ims/ims25/ims25_view_schedule_cust_template.php' ?>
            </div>
        </div>
    </div>

    <!--스타일/기획정보-->
    <div class="row" v-if="!$.isEmpty(mainData.addedInfo)">
        <div class="col-xs-12" style="padding:0 15px">
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">스타일 제안 정보</div>
                    <div class="flo-right">
                        <div class="mgl10 dp-flex dp-flex-gap5" >
                            <div class="btn btn-blue btn-blue-line btn-sm" @click="addSalesStyle()" v-show="isModify">+ 스타일 추가</div>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="table table-cols table-pd-3 table-th-height0 table-td-height0 table-fixed style-table">
                        <colgroup>
                            <col class="w-2p" />
                            <!--<col class="w-2p" />-->
                            <col class="w-3p" />
                            <col v-for="fieldData in $.setColWidth(88, styleFieldConfig)"
                                 v-if="true != fieldData.skip && true !== fieldData.subRow"
                                 :class="`w-${fieldData.col}p`"/>
                            <col class="w-5p" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>이동</th>
                            <!--<th>체크박스
                                <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno">
                            </th>-->
                            <th>번호</th>
                            <th v-for="fieldData in styleFieldConfig" v-html="fieldData.title"></th>
                            <th>추가/삭제</th>
                        </tr>

                        <!--TODO : 추후 구현 (일괄 수정 편의 기능)-->
                        <tr v-if="false">
                            <th colspan="3" style="text-align:right !important;">
                                <span class="" style="font-weight: normal">일괄 작업</span>
                            </th>
                            <!--
                            <th>
                                선택된것만 @change="batchModify(productList,'prdSeason',batchSeason)" => 추 후 편의 기능 추가
                                <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchSeason" >
                                    <option value="">선택</option>
                                    <option :value="key" v-for="(option, key) in JS_LIB_CODE.codeSeason">{% option %}</option>
                                </select>
                            </th>
                            -->
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
                            <th colspan="8"></th>
                        </tr>
                        </thead>
                        <tbody v-if="0 >= productList.length">
                        <tr>
                            <td colspan="99" class="ta-c">스타일 없음</td>
                        </tr>
                        </tbody>
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
                            <!--
                            <td >체크
                                <input type="checkbox" name="prdSno" :value="each.sno" class="prd-sno"
                                       :data-name="each.productName"
                                       :data-code="each.styleCode"
                                       :data-cnt="each.prdExQty"
                                       :data-cost="each.prdCost"
                                       :data-price="each.salePrice"
                                       :data-estimate-cost="each.estimateCost"
                                       :data-margin="each.margin">
                            </td>-->
                            <td ><!--번호-->
                                {% idx+1 %}
                                <!--<div class="text-muted font-11">#{% each.sno %}</div>-->
                            </td>
                            <td v-for="fieldData in styleFieldConfig" :class="fieldData.class + ' relative'">
                                <?php include 'ims25/template/_ims25_custom_style_template.php'?>
                            </td>
                            <td>
                                <div class="dp-flex " v-show="isModify">
                                    <div class="btn btn-red btn-red-line2 btn-sm" @click="addSalesStyle()">추가</div>
                                    <div class="btn btn-white btn-sm" v-show="each.sno > 0"
                                         @click="ImsService.deleteData('projectProduct',each.sno, ()=>{ refreshProductList(sno) })">
                                        삭제
                                    </div>
                                    <div class="btn btn-white btn-sm" v-show="$.isEmpty(each.sno) || 0 >= each.sno"
                                         @click="deleteElement(productList, prdIndex)">
                                        삭제
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 " >
            <div class="table-title gd-help-manual">
                <div class="font-16">제안서 정보 &nbsp;
                    <button type="button" v-if="isModify" class="btn btn-white" @click="addElement(oUpsertForm.jsonProposalGuide, ooDefaultJson.jsonProposalGuide, 'after')">+ 추가</button>
                    <span @click="if (oUpsertForm.jsonProposalGuide.length == 0) $.msg('제안서페이지를 추가해 주세요','','warning'); else $('#modalGuideImageFull').modal('show');" class="btn btn-white">전체보기</span>
                    <span v-show="isModify" @click="schListMultiModalServiceNk.popup({title:'제안서 검색(다중 선택)', top:0, height:830}, 'basicFormProposalGuide', oUpsertForm.jsonProposalGuide, {'guideName':'guideName','guideDesc':'guideDesc','guideFileUrl':'guideFileUrl',}, {}, '');" class="btn btn-blue">제안서 선택</span>
                </div>
            </div>
            <table class="table ims-schedule-table w100 table-fixed table-height0 mgb10 ">
                <colgroup>
                    <col v-if="isModify" class="w-5p" />
                    <col class="w-5p" />
                    <col class="w-190px" />
                    <col class="w-11p" />
                    <col class="" />
                    <col v-if="isModify" class="w-14p" />
                </colgroup>
                <tr>
                    <th class="ta-c" v-if="isModify">이동</th>
                    <th class="ta-c pd0">번호</th>
                    <th class="ta-c">구분</th>
                    <th class="ta-c">내용</th>
                    <th class="ta-c">설명</th>
                    <th v-if="isModify" class="ta-c">기능</th>
                </tr>
                <tbody is="draggable" :list="oUpsertForm.jsonProposalGuide" :animation="200" tag="tbody" handle=".handle">
                <tr v-if="oUpsertForm.jsonProposalGuide.length == 0">
                    <td :colspan="isModify ? 5 : 3">입력한 제안서가이드 페이지가 없습니다. <span v-show="isModify">상단의 +추가 버튼을 클릭해 주세요</span></td>
                </tr>
                <tr v-else v-for="(val, key) in oUpsertForm.jsonProposalGuide" @focusin="sFocusTable='jsonProposalGuide'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonProposalGuide' && iFocusIdx==key ? 'focused' : ''">
                    <td :class="isModify ? 'ta-c handle pd0' : 'ta-c pd0'" v-if="isModify" style="padding:0 !important;">
                        <div class="cursor-pointer hover-btn" >
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </div>
                    </td>
                    <td style="padding:3px !important;" class="ta-c">
                        {% key+1 %}
                    </td>
                    <td style="padding:3px !important;">
                        <div v-show="isModify" class="ta-c">
                            <select v-model="val.guideName" @change="changeGuidePage(key, event.target.value);" class="form-control">
                                <option value="">선택</option>
                                <option v-for="(val2, key2) in aoGuideFormList" :value="val2.guideName">{% val2.guideName %}</option>
                            </select>
                        </div>
                        <div v-show="!isModify">{% val.guideName %}</div>
                    </td>
                    <td style="padding:3px !important;" class="ta-c">
                        <span @click="if (val.guideFileUrl == '') { $.msg('구분을 선택하세요.<br/>혹은 선택하신 구분은 이미지가 없습니다.','','warning'); } else { $refs.textProposalGuide.innerHTML=val.guideName; $refs.imageProposalGuide.src=val.guideFileUrl; $('#modalGuideImage').modal('show'); }" class="btn btn-white btn-sm">미리보기</span>
                    </td>
                    <td class="ta-l" style="padding:3px !important;">
                        <span v-if="val.guideDesc != undefined" class="font-11">
                            {% $.stripHtml(val.guideDesc, true) %}
                        </span>
                    </td>
                    <td v-if="isModify" style="padding:3px !important;" class="ta-c">
                        <button type="button" class="btn btn-red btn-red-line2 btn-sm" @click="addElement(oUpsertForm.jsonProposalGuide, ooDefaultJson.jsonProposalGuide, 'down', key)">+ 추가</button>
                        <div class="btn btn-sm btn-white" @click="deleteElement(oUpsertForm.jsonProposalGuide, key)" >- 삭제</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-xs-6 ">
            <div class="table-title gd-help-manual"><div class="font-16">제안서 정보 - 추가 필요 사항</div></div>
            <div v-show="isModify">
                <textarea v-model="oUpsertForm.proposalGuideDesc" class="form-control" rows="15" placeholder="제안서 정보 - 추가 필요 사항"></textarea>
            </div>
            <div v-show="!isModify" v-html="oUpsertForm.proposalGuideDesc.replaceAll('\n','<br/>')" class="bg-light-gray3 round-box" style="border:1px #ddd solid; height:250px; padding:7px 10px; overflow-y: auto;"></div>
        </div>
    </div>


    <div class="row">
        <div v-for="(val, key) in aoBasicForm" :class="'mgt10 col-xs-' + val.colNumber" style="padding:0 15px 10px">
            <div class="table-title gd-help-manual"><div class="font-16">{% val.grpTitle %}</div></div>
            <!--json 일때는 다른 방식-->
            <table v-if="val.grpType=='json'" class="table ims-schedule-table w100 table-default-center table-fixed table-td-height35 table-th-height35 mgb10">
                <colgroup>
                    <col v-if="isModify" class="w-60px" />
                    <col v-for="(val3, key3) in val.questions[0].cells" :class="val3.cellType=='fixed'?'w-350px':''" />
                    <col v-if="isModify" class="w-150px" />
                </colgroup>
                <tr>
                    <th v-if="isModify">이동</th>
                    <th v-for="(val3, key3) in val.questions[0].cells">{% val3.cellTitle %}</th>
                    <th v-if="isModify">기능</th>
                </tr>
                <tbody is="draggable" :list="ooFillJson[key].jsonValue" :animation="200" tag="tbody" handle=".handle">
                <tr v-for="(val2, key2) in ooFillJson[key].jsonValue" @focusin="sFocusTable='jsonTotal'+key; iFocusIdx=key2;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonTotal'+key && iFocusIdx==key2 ? 'focused' : ''">
                    <td v-if="isModify" :class="isModify ? 'handle' : ''">
                        <div class="cursor-pointer hover-btn" >
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </div>
                    </td>
                    <td v-for="(val3, key3) in val2" class="ta-l">
                    <span v-show="isModify">
                        <input type="text" v-model="val2[key3]" class="form-control" />
                    </span>
                        <span v-show="!isModify">{% val3 %}</span>
                    </td>
                    <td v-if="isModify">
                        <button type="button" class="btn btn-white btn-sm" @click="addElement(ooFillJson[key].jsonValue, ooFillJson[key].jsonValue[0], 'down', key2)">+ 추가</button>
                        <div class="btn btn-sm btn-red" @click="deleteElement(ooFillJson[key].jsonValue, key2)" >- 삭제</div>
                    </td>
                </tr>
                </tbody>
            </table>

            <!--json 이외의 일반적인 방식-->
            <table v-else class="table ims-schedule-table w100 table-default-center table-fixed table-td-height35 table-th-height35 mgb10">
                <colgroup>
                    <col v-for="(val3, key3) in val.questions[0].cells" :class="val3.cellType=='fixed'?(key == 2 ? 'w-320px' : 'w-220px'):''" />
                </colgroup>
                <tr>
                    <th v-for="(val3, key3) in val.questions[0].cells">{% val3.cellTitle %}</th>
                </tr>
                <tbody is="draggable" :list="val.questions" :animation="200" tag="tbody" handle=".handle">
                <tr v-for="(val2, key2) in val.questions" @focusin="sFocusTable='jsonTotal'+key; iFocusIdx=key2;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonTotal'+key && iFocusIdx==key2 ? 'focused' : ''">
                    <td v-for="(val3, key3) in val.questions[key2].cells" class="ta-l">
                        <div v-if="val3.cellType == 'fixed'">{% val3.cellValue %}</div>
                        <div v-else-if="val3.cellType == 'check'">
                        <span v-show="isModify">
                            <label v-for="val4 in val3.options" class="checkbox-inline mgr10">
                                <input type="checkbox" :ref="'salesPlanFormCheckbox_'+key+'_'+key2+'_'+key3" @click="changeCheckbox(key, key2, key3)" :checked="aoFillDatail[key].questions[key2].cells[key3].cellValue.indexOf('|||'+val4+'|||')!=-1?'checked':''" :value="val4" /> {% val4 %}
                            </label>
                        </span>
                            <span v-show="!isModify">{% aoFillDatail[key].questions[key2].cells[key3].cellValue.replaceAll('||||||', ', ').replaceAll('|||', '') %}</span>
                        </div>
                        <div v-else-if="val3.cellType == 'radio'">
                        <span v-show="isModify">
                            <label v-for="val4 in val3.options" class="radio-inline">
                                <input type="radio" v-model="aoFillDatail[key].questions[key2].cells[key3].cellValue" :name="'salesPlanFormRadio_'+key+'_'+key2+'_'+key3" :value="val4" /> {% val4 %}
                            </label>
                        </span>
                            <span v-show="!isModify">{% aoFillDatail[key].questions[key2].cells[key3].cellValue %}</span>
                        </div>
                        <div v-else-if="val3.cellType == 'text'">
                        <span v-show="isModify">
                            <input type="text" v-model="aoFillDatail[key].questions[key2].cells[key3].cellValue" :placeholder="val3.cellValue" class="form-control" />
                        </span>
                            <span v-show="!isModify">{% aoFillDatail[key].questions[key2].cells[key3].cellValue %}</span>
                        </div>
                        <div v-else-if="val3.cellType == 'date'">
                            <div v-show="!isModify">
                                {% $.formatShortDate(aoFillDatail[key].questions[key2].cells[key3].cellValue) %}
                                <span class="font-11 " v-html="$.remainDate(aoFillDatail[key].questions[key2].cells[key3].cellValue, true)"></span>
                            </div>
                            <div v-show="isModify" class="">
                                <date-picker v-model="aoFillDatail[key].questions[key2].cells[key3].cellValue" value-type="format" format="YYYY-MM-DD" :editable="false"></date-picker>
                            </div>
                        </div>
                        <div v-else-if="val3.cellType == 'prjDate'">
                            <div v-show="!isModify">
                                {% $.formatShortDate(mainData[val3.model]) %}
                                <span class="font-11" v-html="$.remainDateWithoutPast(mainData[val3.model],true)"></span>
                            </div>
                            <div v-show="isModify" class="">
                                <date-picker v-model="mainData[val3.model]" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dp-flex dp-flex-center mgt40" style="clear:both;">
        <span v-show="!isModify" @click="isModify = true;" class="btn btn-lg btn-white btn-red btn-red-line2">수정</span>
        <span v-show="isModify" @click="save();" class="btn btn-lg btn-red">저장</span>
        <span v-show="isModify && oUpsertForm.sno > 0" @click="isModify = false;" class="btn btn-lg btn-red btn-red-line2">수정취소</span>
        <input type="button" value="닫기" class="btn  btn-lg btn-white" @click="self.close()" >
    </div>

    <!-- 우측 하단 플로팅 메뉴 -->
    <div class="ims-fab2" style="bottom:75px;">
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="!isModify" @click="isModify = true;">
            수정
        </button>
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="isModify" @click="save();">
            저장
        </button>
        <button type="button" class="ims-fab2-btn bg-white font-black" aria-label="취소" v-show="isModify && oUpsertForm.sno > 0" @click="isModify = false;">
            취소
        </button>
    </div>

    <!--모달-->
    <div class="modal fade" id="modalGuideImage" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:1000px; top:0px;">
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span ref="textProposalGuide" class="modal-title font-16 bold" ></span>
                </div>
                <div class="modal-body ta-c">
                    <img ref="imageProposalGuide" src="" style="max-width:100%;" />
                </div>
                <div class="modal-footer ">
                    <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>

    <!--모달-->
    <div class="modal fade" id="modalGuideImageFull" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:1000px; top:0px;">
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-16 bold" >제안서 정보 전체보기</span>
                </div>
                <div class="modal-body ta-c">
                    <div v-for="(val, key) in oUpsertForm.jsonProposalGuide" class="mgb50">
                        <div class="table-title gd-help-manual ta-l"><div class="font-16">* {% val.guideName %}</div></div>
                        <img :src="val.guideFileUrl" style="max-width:100%;" />
                    </div>
                </div>
                <div class="modal-footer ">
                    <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mgt50"><!--BLank--></div>

<?php include 'ims25_view_script_ext_fnc.php' ?>
<?php include 'script/ims_view_sales_plan_script.php' ?>
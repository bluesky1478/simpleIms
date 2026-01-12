<?php use SiteLabUtil\SlCommonUtil; ?>
<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .table td { padding:3px 6px!important; }
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">납품 보고서 {% oUpsertInspect.sno == 0 ? '등록' : (!isModify ? '상세' : '수정') %}</h3>
        <div class="btn-group font-18 bold">
            <div v-show="isModify" @click="save_style_inspect_delivery();" class="btn btn-red" style="line-height: 35px;">저장</div>
            <div v-show="!isModify" @click="isModify = true;" class="btn btn-red btn-red-line2" style="line-height: 35px;">수정하기</div>
            <div v-show="oUpsertInspect.sno > 0 && isModify" @click="isModify = false;" class="btn btn-white" style="line-height: 35px;">수정취소</div>
            <div @click="openUrl(`style_inspect_print`,`<?=SlCommonUtil::getHost() . '/ics/ics_style_inspect_delivery.php'?>?sno=${istaticChooseStyleSno}`,1600,950);" class="btn btn-white" style="line-height: 35px;">양식인쇄</div>
            <div @click="self.close();" class="btn btn-gray" style="line-height: 35px;">닫기</div>
        </div>
    </div>
    <div class="mgt10">
        <div>
            <div class="pdl5">
                <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col style="width:7%">
                        <col style="width:40%">
                        <col style="width:10%">
                        <col style="width:40%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>고객사</th>
                        <td>{% oChooseStyleInfo.customerName %}</td>
                        <th>품목</th>
                        <td>{% oChooseStyleInfo.productName %}</td>
                    </tr>
                    <tr>
                        <th>제작 업체</th>
                        <td>{% oChooseStyleInfo.produceCompanyName %}</td>
                        <th>수량</th>
                        <td>{% $.setNumberFormat(oChooseStyleInfo.prdExQty) %}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <ul class="nav nav-tabs mgb10" role="tablist">
                    <li role="presentation" class="">
                        <a href="ims_pop_upsert_style_inspect.php?sno=<?=(int)$requestParam['sno']?>" >QC/인라인 검수</a>
                    </li>
                    <li role="presentation" class="active">
                        <a href="ims_pop_upsert_style_inspect_delivery.php?sno=<?=(int)$requestParam['sno']?>" >납품검수</a>
                    </li>
                </ul>
            </div>
            <div class="pdl5">
                <table class="w-100p">
                    <colgroup>
                        <col class="w-49p" />
                        <col class="w-2p" />
                        <col class="w-49p" />
                    </colgroup>
                    <tr>
                        <td style="vertical-align: top;">
                            <table v-if="oaTotalInspectInfo.sizeSpec != undefined" class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                                <thead>
                                <tr>
                                    <th>구분</th><th>점검 항목</th><th>Q/C</th><th>인라인</th><th>고객 납품 샘플</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="oaTotalInspectInfo.sizeSpec.length == 0">
                                    <td colspan="99">데이터가 없습니다.</td>
                                </tr>
                                <tr v-else v-for="(val, key) in oaTotalInspectInfo.sizeSpec">
                                    <td v-if="key == 0" :rowspan="oaTotalInspectInfo.sizeSpec.length">사이즈 분석<br/>(오차평균)</td>
                                    <td>{% val.itemName %}</td>
                                    <td>{% val.inspectList.qc %}</td>
                                    <td>{% val.inspectList.inline %}</td>
                                    <td>{% val.inspectList.delivery %}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td></td>
                        <td style="vertical-align: top;">
                            <table v-if="oaTotalInspectInfo.inspectCheck != undefined" class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                                <thead>
                                <tr>
                                    <th>구분</th><th>점검 항목</th><th>Q/C</th><th>인라인</th><th>고객 납품 샘플</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="oaTotalInspectInfo.inspectCheck.length == 0">
                                    <td colspan="99">데이터가 없습니다.</td>
                                </tr>
                                <tr v-else v-for="(val, key) in oaTotalInspectInfo.inspectCheck">
                                    <td v-if="key == 0" :rowspan="oaTotalInspectInfo.inspectCheck.length">기능 및 외관</td>
                                    <td>{% val.itemName %}</td>
                                    <td>{% val.inspectList.qc %}</td>
                                    <td>{% val.inspectList.inline %}</td>
                                    <td>{% val.inspectList.delivery %}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="pdl5">
                <div class="table-title pdt5 font-18">
                    # QC/INLINE SAMPLE COMMENT
                    <span v-show="!bFlagShowQcComment" @click="bFlagShowQcComment=true;" class="btn btn-white">보기</span>
                    <span v-show="bFlagShowQcComment" @click="bFlagShowQcComment=false;" class="btn btn-white">감추기</span>
                </div>
                <table v-show="bFlagShowQcComment" class="w-100p">
                    <colgroup>
                        <col class="w-50p" />
                        <col class="w-50p" />
                    </colgroup>
                    <tr>
                        <td v-for="val in [1,2]" style="vertical-align: top; border-left:1px #E6E6E6 solid; border-right:1px #E6E6E6 solid;" >
                            <table class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                                <colgroup>
                                    <col class="w-100px" />
                                    <col class="w-100px" />
                                    <col class="w-100px" />
                                    <col class="" />
                                </colgroup>
                                <thead>
                                <tr>
                                    <th colspan="4">{% val == 1 ? '생산처' : '이노버' %} 검사 의견</th>
                                </tr>
                                <tr>
                                    <th>등록일</th><th>등록자</th><th>구분</th><th>내용</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="key2 in maxCntComment">
                                    <tr v-if="oChooseStyleInfo['jsonInspectComment'+val][key2-1] != undefined" @focusin="sFocusTable='inspectComment'+val; iFocusIdx=key2-1;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='inspectComment'+val && iFocusIdx==key2-1 ? 'focused' : ''">
                                        <td :class="oChooseStyleInfo['jsonInspectComment'+val][key2-1].impectYn == 'Y' ? 'text-danger font-bold' : ''">{% $.formatShortDateWithoutWeek(oChooseStyleInfo['jsonInspectComment'+val][key2-1].commentDt) %}</td>
                                        <td :class="oChooseStyleInfo['jsonInspectComment'+val][key2-1].impectYn == 'Y' ? 'text-danger font-bold' : ''">{% oChooseStyleInfo['jsonInspectComment'+val][key2-1].commentName %}</td>
                                        <td :class="oChooseStyleInfo['jsonInspectComment'+val][key2-1].impectYn == 'Y' ? 'text-danger font-bold' : ''">{% oChooseStyleInfo['jsonInspectComment'+val][key2-1].commentInspect %}</td>
                                        <td :class="oChooseStyleInfo['jsonInspectComment'+val][key2-1].impectYn == 'Y' ? 'text-danger font-bold' : ''">{% oChooseStyleInfo['jsonInspectComment'+val][key2-1].commentDesc %}</td>
                                    </tr>
                                    <tr v-else>
                                        <td></td><td></td><td></td><td></td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <div class="table-title pdt5 font-18"><i class="fa fa-play fa-title-icon" aria-hidden="true" ></i> 납품 검수</div>
                <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col style="width:7%">
                        <col style="width:40%">
                        <col style="width:10%">
                        <col style="width:40%">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>점검 인원</th>
                        <td>
                            <span v-if="isModify">
                                <input type="text" v-model="oUpsertInspect.testManagerName" class="form-control" />
                            </span>
                            <span v-else>{% oUpsertInspect.testManagerName %}</span>
                        </td>
                        <th>점검 장소</th>
                        <td>
                            <span v-if="isModify">
                                <input type="text" v-model="oUpsertInspect.testPlace" class="form-control" />
                            </span>
                            <span v-else>{% oUpsertInspect.testPlace %}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>점검 시작일시</th>
                        <td>
                            <span v-if="isModify">
                                <date-picker v-model="oUpsertInspect.testStartDate" value-type="format" format="YYYY-MM-DD" :editable="false" class="w-200px"></date-picker>
                                <input type="number" v-model="oUpsertInspect.testStartHour" class="form-control w-60px mgl35" style="display: inline-block;" min="0" max="23" />시
                            </span>
                            <span v-else>{% oUpsertInspect.testStartDate %} {% oUpsertInspect.testStartHour %}시</span>
                        </td>
                        <th>점검 종료일시</th>
                        <td>
                            <span v-if="isModify">
                                <date-picker v-model="oUpsertInspect.testEndDate" value-type="format" format="YYYY-MM-DD" :editable="false" class="w-200px"></date-picker>
                                <input type="number" v-model="oUpsertInspect.testEndHour" class="form-control w-60px mgl35" style="display: inline-block;" min="0" max="23" />시
                            </span>
                            <span v-else>{% oUpsertInspect.testEndDate %} {% oUpsertInspect.testEndHour %}시</span>
                        </td>
                    </tr>
                    <tr>
                        <th>메모</th>
                        <td colspan="3">
                            <span v-if="isModify">
                                <textarea v-model="oUpsertInspect.testMemo" row="3" class="form-control"></textarea>
                            </span>
                            <span v-else v-html="$.nl2br(oUpsertInspect.testMemo)"></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <div>
                    {% computed_calc_gap %}
                    <span v-show="isModify" @click="appendInspect();" class="btn btn-white">검수 추가</span>
                </div>
                <table v-if="oUpsertInspect.jsonTestSizeSpec != undefined" class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col class="w-150px" />
                        <col class="w-100px" />
                        <col v-if="oUpsertInspect.jsonTestSizeSpec.length == 0" />
                        <col v-else v-for="val2 in oUpsertInspect.jsonTestSizeSpec" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th rowspan="2">측정부위</th>
                        <th rowspan="2" style="border-right:1px #E6E6E6 solid;">단위</th>
                        <td colspan="3" v-if="oUpsertInspect.jsonTestSizeSpec.length == 0">검수내용을 추가해 주세요.</td>
                        <td colspan="3" v-else v-for="val2 in oUpsertInspect.jsonTestSizeSpec" style="border-right:1px #E6E6E6 solid;">
                            <span v-if="isModify">
                                <select v-model="val2.inspectStandard" class="form-control" style="margin:0px auto; background: #fff; min-width:60px!important;">
                                    <option v-for="(val3, key3) in aoSizeSpecForm[0].oStandardSpec" :value="key3">{% key3 %}</option>
                                </select>
                            </span>
                            <span v-else>{% val2.inspectStandard %}</span>
                        </td>
                    </tr>
                    <tr>
                        <template v-if="oUpsertInspect.jsonTestSizeSpec.length == 0">
                            <th>기준</th><th>점검</th><th>오차</th>
                        </template>
                        <template v-else v-for="val2 in oUpsertInspect.jsonTestSizeSpec">
                            <th>기준</th><th>점검</th><th style="border-right:1px #E6E6E6 solid;">오차</th>
                        </template>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(val, key) in aoSizeSpecForm" @focusin="sFocusTable='inspectSizeSpec'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='inspectSizeSpec' && iFocusIdx==key ? 'focused' : ''">
                        <th>{% val.sTitle %}</th>
                        <th style="border-right:1px #E6E6E6 solid;">{% val.sUnit %}</th>
                        <template v-if="oUpsertInspect.jsonTestSizeSpec.length == 0">
                            <td colspan="3"></td>
                        </template>
                        <template v-else v-for="(val2, key2) in oUpsertInspect.jsonTestSizeSpec">
                            <td>{% val.oStandardSpec[val2.inspectStandard] %}</td>
                            <td>
                                <span v-if="isModify">
                                    <input type="text" v-model="val2.chkSizeList[val.sTitle]" :ref="'inputReview_chkSize'+key2" @keyup="gfnMoveInputBox(val, key, event.key, $refs['inputReview_chkSize'+key2])" class="form-control" style="margin:0px auto; max-width:100px;" />
                                </span>
                                <span v-else>{% val2.chkSizeList[val.sTitle] %}</span>
                            </td>
                            <td style="border-right:1px #E6E6E6 solid;">
                                <span :class="ooGap[val.sTitle][key2] == 0 ? '' : (Number(ooGap[val.sTitle][key2]) > 0 ? 'bold text-blue' : 'bold text-danger')">{% ooGap[val.sTitle][key2] %}</span>
                            </td>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <table v-if="oUpsertInspect.jsonTestCheck != undefined" class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col class="w-10p" />
                        <col class="w-20p" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th rowspan="2">구분</th>
                        <th rowspan="2" style="border-right:1px #E6E6E6 solid;">내용</th>
                        <td colspan="2" v-if="oUpsertInspect.jsonTestCheck.length == 0">검수내용을 추가해 주세요.</td>
                        <td colspan="2" v-else v-for="(val2, key2) in oUpsertInspect.jsonTestCheck" style="border-right:1px #E6E6E6 solid;">
                            {% oUpsertInspect.jsonTestSizeSpec[key2].inspectStandard %}
                        </td>
                    </tr>
                    <tr>
                        <template v-if="oUpsertInspect.jsonTestCheck.length == 0">
                            <th>유 / 무</th><th>내용</th>
                        </template>
                        <template v-else v-for="val2 in oUpsertInspect.jsonTestCheck">
                            <th>유 / 무</th><th style="border-right:1px #E6E6E6 solid;">내용</th>
                        </template>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(val, key) in aoCheckItemForm" @focusin="sFocusTable='inspectCheck'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='inspectCheck' && iFocusIdx==key ? 'focused' : ''">
                        <td v-if="val.cntType > 0" :rowspan="val.cntType">{% val.chkType %}</td>
                        <td>{% val.chkTitle %}</td>
                        <template v-if="oUpsertInspect.jsonTestCheck.length == 0">
                            <td colspan="2"></td>
                        </template>
                        <template v-else v-for="(val2, key2) in oUpsertInspect.jsonTestCheck">
                            <td>
                                <span v-if="isModify">
                                    <select v-model="val2[key].testPoint" class="form-control" style="margin:0px auto; background: #fff; min-width:40px!important;">
                                        <option value="유">유</option>
                                        <option value="무">무</option>
                                    </select>
                                </span>
                                <span v-else>{% val2[key].testPoint %}</span>
                            </td>
                            <td style="border-right:1px #E6E6E6 solid;">
                                <span v-if="isModify">
                                    <input class="form-control" type="text" v-model="val2[key].testDesc" :ref="'inputReview_inlineDesc'+key2" @keyup="gfnMoveInputBox(val, key, event.key, $refs['inputReview_inlineDesc'+key2])" />
                                </span>
                                <span v-else>{% val2[key].testDesc %}</span>
                            </td>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <div class="table-title pdt5 font-18">
                    <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i> Revision
                    <div class="flo-right pdb5">
                        <div class="btn btn-red btn-red-line2" @click="addEworkRevision()">
                            <i class="fa fa-plus text-danger"></i>
                            Revision 내용 등록
                        </div>
                    </div>
                </div>
                <table class="table table-cols table-pd-0 table-center table-th-height30 table-td-height30">
                    <colgroup>
                        <col class="w-7p">
                        <col class="w-7p">
                        <col class="w-8p">
                        <col class="w-8p">
                        <col class="w-14p">
                        <col class="w-13p">
                        <col class="w-13p">
                        <col class="w-6p">
                        <col class="w-11p">
                        <col class="w-10p">
                    </colgroup>
                    <tbody>
                    <tr >
                        <th >등록일</th>
                        <th >등록자</th>
                        <th >변경 사유</th>
                        <th >변경 구분</th>
                        <th >변경 세부</th>
                        <th >변경 전</th>
                        <th >변경 후</th>
                        <th >상태</th>
                        <th >변경자/변경일시</th>
                        <th >추가/삭제</th>
                    </tr>
                    <tr v-for="(rev, revIndex) in aoRevision" v-show="!isModify && rev.revRoute == '납품검수'">
                        <td>{% $.formatShortDateWithoutWeek(rev.regDt) %}</td><!--등록일-->
                        <td>{% rev.regManagerName %}</td><!--등록자-->
                        <td class="">
                            <select class="form-control" v-model="rev.revReason" disabled style="background-color:#fff">
                                <option value="0">선택</option>
                                <?php foreach($revReasonList as $revReasonKey => $revReason) { ?>
                                    <option value="<?=$revReasonKey?>"><?=$revReason?></option>
                                <?php } ?>
                            </select>
                        </td><!--변경사유-->
                        <td class="">
                            <select class="form-control" v-model="rev.revType" disabled style="background-color:#fff">
                                <option value="0">선택</option>
                                <?php foreach($revTypeList as $revTypeKey => $revType) { ?>
                                    <option value="<?=$revTypeKey?>"><?=$revType?></option>
                                <?php } ?>
                            </select>
                        </td><!--변경구분-->
                        <td>{% rev.revDetail %}</td><!--변경상세-->
                        <td>{% rev.revBefore %}</td><!--변경전-->
                        <td>{% rev.revAfter %}</td><!--변경후-->
                        <td>{% rev.revSt %}</td>
                        <td>{% rev.chgManagerName %}<br/>{% rev.chgDt %}</td>
                        <td>-</td><!--삭제-->
                    </tr>
                    <tr v-for="(rev, revIndex) in aoRevision" v-show="isModify && rev.revRoute == '납품검수'">
                        <td>{% $.formatShortDateWithoutWeek(rev.regDt) %}</td><!--등록일-->
                        <td>{% rev.regManagerName %}</td><!--등록자-->
                        <td>
                            <select class="form-control" v-model="rev.revReason">
                                <option value="0">선택</option>
                                <?php foreach($revReasonList as $revReasonKey => $revReason) { ?>
                                    <option value="<?=$revReasonKey?>"><?=$revReason?></option>
                                <?php } ?>
                            </select>
                        </td><!--변경사유-->
                        <td class="pd5">
                            <select class="form-control" v-model="rev.revType">
                                <option value="0">선택</option>
                                <?php foreach($revTypeList as $revTypeKey => $revType) { ?>
                                    <option value="<?=$revTypeKey?>"><?=$revType?></option>
                                <?php } ?>
                            </select>
                        </td><!--변경구분-->
                        <td class="pd5">
                            <input type="text" class="form-control w-90p mgt20" maxlength="20" v-model="rev.revDetail">
                            <span class="font-10">변경상세 20자내외</span>
                        <td class="pd5">
                            <input type="text" class="form-control w-90p mgt20" maxlength="18" v-model="rev.revBefore">
                            <span class="font-10">변경전 15자내외</span>
                        </td><!--변경전-->
                        <td class="pd5">
                            <input type="text" class="form-control w-90p mgt20" maxlength="18" v-model="rev.revAfter">
                            <span class="font-10">변경후 15자내외</span>
                        </td><!--변경후-->
                        <td>
                            <select class="form-control" v-model="rev.revSt" @change="if(rev.revSt == '변경완료') { rev.chgManagerName=staticLoginName; rev.chgDt=staticCurrDt; } else { rev.chgManagerName=''; rev.chgDt=''; };">
                                <option value="대기">대기</option>
                                <option value="변경완료">변경완료</option>
                            </select>
                        </td>
                        <td>{% rev.chgManagerName %}<br/>{% rev.chgDt %}</td>
                        <td>
                            <div class="btn btn-sm btn-white" @click="addEworkRevision()">추가</div>
                            <div class="btn btn-sm btn-white" @click="deleteElement(aoRevision, revIndex)">삭제</div>
                        </td><!--삭제-->
                    </tr>
                    <tr v-if="0 >= aoRevision.length">
                        <td colspan="99" class="ta-c">데이터 없음</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="dp-flex" style="justify-content: center; border-top:1px #888 solid; margin-top:20px; padding-top:5px;">
            <div v-show="isModify" @click="save_style_inspect_delivery();" class="btn btn-red" style="line-height: 35px;">저장</div>
            <div v-show="!isModify" @click="isModify = true;" class="btn btn-red btn-red-line2" style="line-height: 35px;">수정하기</div>
            <div v-show="oUpsertInspect.sno > 0 && isModify" @click="isModify = false;" class="btn btn-white" style="line-height: 35px;">수정취소</div>
            <div @click="self.close();" class="btn btn-gray" style="line-height: 35px;">닫기</div>
        </div>
    </div>
</section>






<script type="text/javascript">
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData, {
            istaticChooseStyleSno : <?=(int)$requestParam['sno']?>,
            sFocusTable : '', //html table focus용 변수
            iFocusIdx : 0, //html table focus용 변수
            aoSizeSpecForm : [], //스타일에서 가져온 사이즈스펙정보 담는 배열 -> 검수사이즈 추가할때마다 이걸 바탕으로 push
            aoCheckItemForm : [], //QC/인라인검수에서 가져온 체크내역 항목들(rowspan, 구분, 항목명) -> 검수사이즈 추가할때마다 이걸 바탕으로 push
            oChooseStyleInfo : {}, //스타일정보 (구분, 스타일명, 스타일코드, 생산처) + QC/인라인검수 정보 (사이스스펙 검수내용, 기능및외관 검수내용, 코멘트)
            isModify : true,
            oUpsertInspect : { sno:0 }, //납품검수(납품보고서) 상세
            aoRevision : [],
            ooDefaultForm : {
                'jsonTestSizeSpec' : {'inspectStandard':'', 'chkSizeList':{}},
            },
            ooGap : {}, //측정항목별, 검수별 오차
            oaTotalInspectInfo : {}, //QC/인라인/납품 검수 정보요약
            bFlagShowQcComment : true,
            maxCntComment : 0, //QC/인라인 코멘트 중에서 가장 많은 row수

            staticCurrDt : '<?=date('Y-m-d H:i')?>',
            staticLoginName : '<?=\Session::get('manager.managerNm')?>',
        });
        ImsBoneService.setMethod(serviceData,{
            //페이지 진입시 한번만 실행
            getBasicInfo : ()=>{
                if (vueApp.istaticChooseStyleSno == 0) {
                    $.msg('스타일 일련번호가 없습니다.','','warning').then((data)=> {
                        self.close();
                    });
                }

                ImsNkService.getList('styleSimple', {'styleSnos':[vueApp.istaticChooseStyleSno], 'sch_level':'1'}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        if (data.sno == 0) {
                            $.msg('먼저 QC/인라인검수를 등록하시기 바랍니다.','','warning').then((data)=> {
                                self.close();
                            });
                        }

                        //스타일테이블로부터 사이즈스펙 정보 가져와서 배열화
                        if (data.sizeSpec == undefined || data.sizeSpec.specData == undefined || data.sizeSpec.specData[0] == undefined || data.sizeSpec.specData[0].spec == undefined) {
                            $.msg('스타일의 사이즈스펙 정보가 없습니다(기준값, 편차 필요).','','warning').then((data)=> {
                                self.close();
                            });
                        }
                        vueApp.aoSizeSpecForm = [];
                        let aSpecRange = data.sizeSpec.specRange.split(',');
                        $.each(aSpecRange, function(key, val) {
                            aSpecRange[key] = Number(val);
                        });
                        let oSpecRange = {};
                        oSpecRange[data.sizeSpec.standard] = 0;
                        aSpecRange.sort(function(a,b) { return a - b; });
                        let iTmp = 0;
                        $.each(aSpecRange, function(key, val) {
                            if (val > data.sizeSpec.standard) iTmp++;
                            if (iTmp > 0) oSpecRange[val] = iTmp;
                        });
                        aSpecRange.sort(function(a,b) { return b - a; });
                        iTmp = 0;
                        $.each(aSpecRange, function(key, val) {
                            if (val < data.sizeSpec.standard) iTmp--;
                            if (iTmp < 0) oSpecRange[val] = iTmp;
                        });
                        $.each(data.sizeSpec.specData, function(key, val) {
                            vueApp.aoSizeSpecForm.push({'sTitle':val.title, 'sUnit':val.unit, 'oStandardSpec':{}});
                            $.each(oSpecRange, function(key2, val2) {
                                vueApp.aoSizeSpecForm[key].oStandardSpec[key2] = Number(val.spec) + Number(val.deviation)*val2;
                            });
                        });

                        //QC/인라인검수테이블로부터 체크항목(==기능 및 외관 점검) 정보 가져와서 배열화
                        if (data.jsonInspectCheck == undefined) {
                            $.msg('QC/인라인검수 내용이 없습니다(검수 내역).','','warning').then((data)=> {
                                self.close();
                            });
                        }
                        vueApp.aoCheckItemForm = [];
                        $.each(data.jsonInspectCheck, function(key, val) {
                            vueApp.aoCheckItemForm.push({'cntType':val.cntType, 'chkType':val.chkType, 'chkTitle':val.chkTitle});
                        });

                        //QC/INLINE SAMPLE COMMENT : QC코멘트, 인라인코멘트 중에서 가장 많은 row수 구하기
                        vueApp.maxCntComment = data.jsonInspectComment1.length;
                        if (data.jsonInspectComment2.length > vueApp.maxCntComment) vueApp.maxCntComment = data.jsonInspectComment2.length;
                        vueApp.oChooseStyleInfo = {
                            'customerName':data.customerName, 'prdExQty':data.prdExQty, 'styleCode':data.styleCode, 'productName':data.productName, 'produceCompanyName':data.produceCompanyName,
                            'projectSno':data.projectSno, 'customerSno':data.customerSno, 'standardSize':data.sizeSpec.standard,
                            'jsonInspectSizeSpec':data.jsonInspectSizeSpec, 'jsonInspectCheck':data.jsonInspectCheck, 'jsonInspectComment1':data.jsonInspectComment1, 'jsonInspectComment2':data.jsonInspectComment2,
                        };

                        //납품검수 가져오기 + 검수요약
                        vueApp.getInfo();

                    });
                });
            },
            //납품검수 가져오기 + 검수요약
            getInfo : ()=> {
                ImsNkService.getList('styleInspectDelivery', {'upsertSnoGet':vueApp.istaticChooseStyleSno}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertInspect = data.info;
                        vueApp.aoRevision = data.info2;

                        //검수요약 start
                        //사이즈 분석(오차평균)
                        vueApp.oaTotalInspectInfo.sizeSpec = [];
                        vueApp.oaTotalInspectInfo.inspectCheck = [];
                        let sInspectType = '';
                        let oCntInspectAction = {'qc':0, 'inline':0};
                        let iKeyItem = -1;
                        let oStandardSizeByItemNm = {};
                        $.each(vueApp.oChooseStyleInfo.jsonInspectSizeSpec, function (key, val) { //qc/인라인 차수반복
                            if (val.inspectTitle.indexOf('QC') != -1) sInspectType = 'qc';
                            else if (val.inspectTitle.indexOf('인라인') != -1) sInspectType = 'inline';
                            $.each(val.inspectChkUserList, function (key2, val2) { //검수자 반복
                                oCntInspectAction[sInspectType]++;
                                $.each(val2.chkSizeList, function (key3, val3) { //측정항목 반복
                                    if (key == 0 && key2 == 0) {
                                        vueApp.oaTotalInspectInfo.sizeSpec.push({'itemName':key3, inspectList:{'qc':0, 'inline':0, 'delivery':0}});
                                        iKeyItem++;
                                    }
                                });
                                //QC/인라인/납품 검수값 오차평균
                                $.each(vueApp.oaTotalInspectInfo.sizeSpec, function (key3, val3) { //측정항목 반복
                                    oStandardSizeByItemNm = {};
                                    $.each(vueApp.aoSizeSpecForm, function (key4, val4) { //사이즈스펙폼 측정항목 반복(기준값 구하기)
                                        if (val3.itemName == val4.sTitle) {
                                            oStandardSizeByItemNm[val3.itemName] = Number(val4.oStandardSpec[val.inspectStandard]);
                                        }
                                    });

                                    //QC/인라인 검수값 오차평균
                                    $.each(val2.chkSizeList, function (key4, val4) { //측정항목 반복
                                        if (val3.itemName == key4) {
                                            val3.inspectList[sInspectType] += Math.abs(Math.round((Number(val4) - oStandardSizeByItemNm[val3.itemName]) * 100)/100);
                                        }
                                    });
                                });
                            });
                        });
                        //사이즈 분석(오차평균) -> 납품 검수값 오차평균 : computed에서 넣어봤는데 반영이 한박자 늦어서 여기에서 함
                        if (vueApp.oUpsertInspect.jsonTestSizeSpec.length > 0) {
                            let oSumGapBySizeNm = {};
                            let iGap = 0;
                            $.each(vueApp.aoSizeSpecForm, function(key, val) {
                                if (oSumGapBySizeNm[val.sTitle] == undefined) oSumGapBySizeNm[val.sTitle] = 0;
                                $.each(vueApp.oUpsertInspect.jsonTestSizeSpec, function(key2, val2) {
                                    iGap = Math.round((Number(val2.chkSizeList[val.sTitle]) - Number(val.oStandardSpec[val2.inspectStandard])) * 100) / 100;
                                    oSumGapBySizeNm[val.sTitle] += Math.abs(iGap);
                                });
                            });
                            $.each(vueApp.oaTotalInspectInfo.sizeSpec, function(key, val) {
                                val.inspectList.delivery = oSumGapBySizeNm[val.itemName];
                            });
                        }
                        //사이즈 분석(오차평균) -> 검수갯수(차수*추가측정수)만큼 나누기
                        $.each(vueApp.oaTotalInspectInfo.sizeSpec, function (key, val) {
                            val.inspectList.qc = oCntInspectAction.qc == 0 ? '미검수' : Math.round(val.inspectList.qc / oCntInspectAction.qc * 100)/100;
                            val.inspectList.inline = oCntInspectAction.inline == 0 ? '미검수' : Math.round(val.inspectList.inline / oCntInspectAction.inline * 100)/100;
                            val.inspectList.delivery = vueApp.oUpsertInspect.jsonTestSizeSpec.length == 0 ? '미검수' : Math.round(val.inspectList.delivery / vueApp.oUpsertInspect.jsonTestSizeSpec.length * 100)/100;
                        });

                        //기능 및 외관
                        $.each(vueApp.oChooseStyleInfo.jsonInspectCheck, function(key, val) {
                            vueApp.oaTotalInspectInfo.inspectCheck.push({'itemName':val.chkTitle, inspectList:{'qc':val.qcPoint, 'inline':val.inlinePoint, 'delivery':[]}});
                        });
                        if (vueApp.oUpsertInspect.jsonTestCheck.length > 0) {
                            $.each(vueApp.oUpsertInspect.jsonTestCheck, function(key, val) {
                                $.each(val, function(key2, val2) {
                                    vueApp.oaTotalInspectInfo.inspectCheck[key2].inspectList.delivery.push(val2.testPoint);
                                });
                            });
                            $.each(vueApp.oaTotalInspectInfo.inspectCheck, function(key, val) {
                                val.inspectList.delivery = val.inspectList.delivery.join(', ');
                            });
                        }
                        //검수요약 end

                        if (vueApp.oUpsertInspect.sno > 0) vueApp.isModify = false;
                        else vueApp.isModify = true;
                    });
                });
            },
            //검수 추가
            appendInspect : ()=> {
                //사이즈스펙 측정 추가 jsonTestSizeSpec(aoo) : [{'inspectStandard':'100', 'chkSizeList':{'총기장':'47', ...측정항목별 반복...}}, ...검수건별 반복...]
                vueApp.ooDefaultForm.jsonTestSizeSpec.inspectStandard = vueApp.oChooseStyleInfo.standardSize;
                $.each(vueApp.aoSizeSpecForm, function (key, val) {
                    vueApp.ooDefaultForm.jsonTestSizeSpec.chkSizeList[val.sTitle] = '';
                });
                vueApp.oUpsertInspect.jsonTestSizeSpec.push($.copyObject(vueApp.ooDefaultForm.jsonTestSizeSpec));

                //검수내역 추가 jsonTestCheck(aao) : [[{'testPoint':'무', 'testDesc':'특이사항'}, ...검수항목별 반복(항목 순서대로)...], ...검수건별 반복...]
                //namkuuuuuuuuuuuu 검수내역(==기능및외관) 이거 납품검수단계에서 항목내용 바뀌거나 항목추가되는 경우 있음?(QC/인라인검수에서 변경해줘야함)
                let aoTmp = [];
                $.each(vueApp.aoCheckItemForm, function (key, val) {
                    aoTmp.push({'testPoint':'무','testDesc':''});
                });
                vueApp.oUpsertInspect.jsonTestCheck.push(aoTmp);
            },


            //납품검수(===납품보고서) 등록
            save_style_inspect_delivery : ()=>{
                if (vueApp.oUpsertInspect.jsonTestSizeSpec.length == 0) {
                    $.msg('검수내용을 추가해 주시기 바랍니다.','','warning');
                    return false;
                }

                $.msgConfirm('납품검수내용을 저장하시겠습니까?','').then(function(result){
                    if( result.isConfirmed ){
                        vueApp.oUpsertInspect.cntTest = vueApp.oUpsertInspect.jsonTestSizeSpec.length;
                        vueApp.oUpsertInspect.styleSno = vueApp.istaticChooseStyleSno;

                        $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertInspect, 'table_number':14}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                //작지-리비전 update
                                $.imsPost('modifySimpleDbCol', {'table_number':5, 'colNm':'revision', 'where':{'styleSno':vueApp.istaticChooseStyleSno}, 'data':vueApp.aoRevision}).then((data)=>{
                                    $.imsPostAfter(data,(data)=>{
                                        location.reload();
                                    });
                                });
                            });
                        });
                    }
                });
            },

            //리비전 추가
            addEworkRevision : ()=>{
                vueApp.isModify = true;
                $.imsPost2('addEworkRevision',{},(data)=>{
                    data.revRoute = '납품검수';
                    vueApp.aoRevision.push(data);
                });
            },
        });
        ImsBoneService.setMounted(serviceData, ()=>{
            vueApp.getBasicInfo();
        });
        ImsBoneService.setComputed(serviceData,{
            //오차계산
            computed_calc_gap() {
                if (this.oUpsertInspect.jsonTestSizeSpec != undefined && this.oUpsertInspect.jsonTestSizeSpec.length > 0) {
                    let oTarget = this.ooGap;
                    let oTest = this.oUpsertInspect.jsonTestSizeSpec;
                    let oSumGapBySizeNm = {};
                    let iGap = 0;
                    $.each(this.aoSizeSpecForm, function(key, val) {
                        if (oTarget[val.sTitle] == undefined) oTarget[val.sTitle] = [];
                        if (oSumGapBySizeNm[val.sTitle] == undefined) oSumGapBySizeNm[val.sTitle] = 0;
                        $.each(oTest, function(key2, val2) {
                            iGap = Math.round((Number(val2.chkSizeList[val.sTitle]) - Number(val.oStandardSpec[val2.inspectStandard])) * 100) / 100;
                            oTarget[val.sTitle][key2] = iGap;
                            oSumGapBySizeNm[val.sTitle] += Math.abs(iGap);
                        });
                    });


                    //납품검수 요약정보(사이즈스펙 오차평균, 기능및외관항목 채점항목) - 결과값 한박자 늦게 표시돼서 주석처리
                    // let oTarget2 = this.oaTotalInspectInfo.sizeSpec;
                    // $.each(oTarget2, function(key, val) {
                    //     oTarget2[key].inspectList.delivery = Math.round(oSumGapBySizeNm[val.itemName] / oTest.length * 100) / 100;
                    // });
                }
            }
        });

        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>

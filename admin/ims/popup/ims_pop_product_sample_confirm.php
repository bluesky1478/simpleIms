<table class="w-100p">
    <colgroup>
        <col class="w-60p" />
        <col class="w-20px" />
        <col class="" />
    </colgroup>
    <tr>
        <td class="vertical-align-top">
            <div class="table-title gd-help-manual">
                <div class="flo-left pdt5 font-16"># 샘플 썸네일</div>
            </div>
            <div class="ta-c border-light-gray" style="clear:both;">
                <img v-if="checkImageExtension(val.fileName) && key == 0" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in oUpsertInfo.fileList['sampleFile7'].files" style="margin:0px auto; max-width:100%;" />
            </div>
        </td>
        <td></td>
        <td class="vertical-align-top">
            <div class="table-title gd-help-manual"><div class="flo-left pdt5 font-16"># 사이즈스펙 피팅 체크</div></div>
            <div style="clear:both;" v-if="oUpsertInfo.jsonFitSpec != null && oUpsertInfo.jsonFitSpec != ''" v-show="oUpsertInfo.jsonFitSpec.length > 0">
                <table class="table table-cols table-default-center table-pd-5">
                    <colgroup>
                        <col class="w-25p" />
                        <col class="w-15p" />
                        <col class="w-20p" />
                        <col class="w-20p" />
                        <col class="" />
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>사이즈</th>
                        <td colspan="4" style="text-align: left;">{% oUpsertInfo.fitSize %}</td>
                    </tr>
                    <tr>
                        <th>구분 / 스펙</th>
                        <th>단위</th>
                        <th>제작 샘플</th>
                        <th>피팅 검토</th>
                        <th>확정 사이즈</th>
                    </tr>
                    <tr v-for="(val, key) in oUpsertInfo.jsonFitSpec" @focusin="sFocusTable='fitSpec'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='fitSpec' && iFocusIdx==key ? 'focused' : ''">
                        <th>{% val.optionName %}</th>
                        <td>{% val.optionUnit %}</td>
                        <td>{% oUpsertInfo.jsonReviewSpec[key].madeValue %}</td>
                        <td>{% oUpsertInfo.jsonReviewSpec[key].checkValue %}</td>
                        <td>
                            <span v-show="isModify">
                                <input class="form-control" type="text" v-model="oUpsertInfo.jsonConfirmSpec[key].optionValue" ref="specInputValue" @keyup="gfnMoveInputBox(oUpsertInfo.jsonConfirmSpec, key, event.key, $refs.specInputValue)" />
                            </span>
                            <span v-show="!isModify">{% oUpsertInfo.jsonConfirmSpec[key].optionValue %}</span>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr v-show="isModify">
                        <th colspan="2">실물 샘플 사이즈대로 확정</th>
                        <td><input type="checkbox" class="form-control" @click="inputSizeSpec($event.target.checked);" /></td>
                        <td colspan="2"></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </td>
    </tr>
</table>
<table class="w-100p">
    <colgroup>
        <col class="w-45p" />
        <col class="w-50px" />
        <col class="" />
    </colgroup>
    <tr>
        <td class="vertical-align-top">
            <div class="table-title gd-help-manual">
                <div class="flo-left pdt5 font-16">
                # 이노버 추가 제안 내용 (사이즈스펙 외)
                <button type="button" v-if="isModify" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonConfirmSuggest, ooDefaultJson.jsonConfirmSuggest, 'after')">+ 추가</button>
            </div></div>
            <table v-if="oUpsertInfo.jsonConfirmSuggest != undefined" class="table table-cols table-default-center table-pd-5">
                <colgroup>
                    <col v-if="isModify" class="w-10p" />
                    <col class="" />
                    <col class="w-30p" />
                    <col v-if="isModify" class="w-20p" />
                </colgroup>
                <tr>
                    <th v-if="isModify">이동</th>
                    <th>제안 내용</th>
                    <th>확인 사항</th>
                    <th v-if="isModify">기능</th>
                </tr>
                <tbody is="draggable" :list="oUpsertInfo.jsonConfirmSuggest" :animation="200" tag="tbody" handle=".handle">
                <tr v-if="oUpsertInfo.jsonConfirmSuggest.length == 0">
                    <td :colspan="isModify ? 4 : 2">입력한 추가 제안 내용이 없습니다. <span v-show="isModify">상단의 +추가 버튼을 클릭해 주세요</span></td>
                </tr>
                <tr v-else v-for="(val, key) in oUpsertInfo.jsonConfirmSuggest" @focusin="sFocusTable='confirmSuggest'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='confirmSuggest' && iFocusIdx==key ? 'focused' : ''">
                    <td :class="isModify ? 'handle' : ''" v-if="isModify">
                        <div class="cursor-pointer hover-btn" >
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </div>
                    </td>
                    <td class="ta-l">
                        <span v-show="isModify">
                            <input class="form-control" type="text" v-model="val.suggestContent" ref="inputSuggestContent" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputSuggestContent)" />
                        </span>
                        <span v-show="!isModify">{% val.suggestContent %}</span>
                    </td>
                    <td>
                        <span v-show="isModify">
                            <input class="form-control" type="text" v-model="val.suggestCheckYn" ref="inputSuggestCheckYn" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputSuggestCheckYn)" />
                        </span>
                        <span v-show="!isModify">{% val.suggestCheckYn %}</span>
                    </td>
                    <td v-if="isModify">
                        <button type="button" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonConfirmSuggest, ooDefaultJson.jsonConfirmSuggest, 'down', key)">+ 추가</button>
                        <div class="btn btn-sm btn-red" @click="deleteElement(oUpsertInfo.jsonConfirmSuggest, key)" >- 삭제</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td></td>
        <td class="vertical-align-top">
            <div class="table-title gd-help-manual"><div class="flo-left pdt5 font-16">
                # 고객사 요청 사항 <button type="button" v-if="isModify" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonConfirmRequest, ooDefaultJson.jsonConfirmRequest, 'after')">+ 추가</button>
            </div></div>
            <table v-if="oUpsertInfo.jsonConfirmRequest != undefined" class="table table-cols table-default-center table-pd-5">
                <colgroup>
                    <col v-if="isModify" class="w-10p" />
                    <col class="" />
                    <col class="w-40p" />
                    <col v-if="isModify" class="w-20p" />
                </colgroup>
                <tr>
                    <th v-if="isModify">이동</th>
                    <th>제안 내용</th>
                    <th>확인 사항</th>
                    <th v-if="isModify">기능</th>
                </tr>
                <tbody is="draggable" :list="oUpsertInfo.jsonConfirmRequest" :animation="200" tag="tbody" handle=".handle">
                <tr v-if="oUpsertInfo.jsonConfirmRequest.length == 0">
                    <td :colspan="isModify ? 4 : 2">입력한 요청 사항이 없습니다. <span v-show="isModify">상단의 +추가 버튼을 클릭해 주세요</span></td>
                </tr>
                <tr v-else v-for="(val, key) in oUpsertInfo.jsonConfirmRequest" @focusin="sFocusTable='confirmRequest'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='confirmRequest' && iFocusIdx==key ? 'focused' : ''">
                    <td :class="isModify ? 'handle' : ''" v-if="isModify">
                        <div class="cursor-pointer hover-btn" >
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </div>
                    </td>
                    <td class="ta-l">
                        <span v-show="isModify">
                            <input class="form-control" type="text" v-model="val.requestContent" ref="inputRequestContent" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputRequestContent)" />
                        </span>
                        <span v-show="!isModify">{% val.requestContent %}</span>
                    </td>
                    <td>
                        <span v-show="isModify">
                            <input class="form-control" type="text" v-model="val.requestDesc" ref="inputRequestDesc" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputRequestDesc)" />
                        </span>
                        <span v-show="!isModify">{% val.requestDesc %}</span>
                    </td>
                    <td v-if="isModify">
                        <button type="button" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonConfirmRequest, ooDefaultJson.jsonConfirmRequest, 'down', key)">+ 추가</button>
                        <div class="btn btn-sm btn-red" @click="deleteElement(oUpsertInfo.jsonConfirmRequest, key)" >- 삭제</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<div class="table-title gd-help-manual">
    <div class="flo-left pdt5 font-16"># 피팅체크</div>
</div>
<div>
    <table v-if="oUpsertInfo.jsonReviewCheck != undefined" class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30">
        <colgroup>
            <col class="w-20p" />
            <col class="w-20p" />
            <col class="w-10p" />
            <col class="w-10p" />
            <col class="" />
        </colgroup>
        <tr>
            <th>구분</th>
            <th>체크 사항</th>
            <th>체크</th>
            <th>고객체크</th>
            <th>이슈 사항</th>
        </tr>
        <tbody>
        <tr v-if="oUpsertInfo.jsonReviewCheck.length == 0">
            <td colspan="5">입력한 피팅체크 항목이 없습니다.</td>
        </tr>
        <tr v-else v-for="(val, key) in oUpsertInfo.jsonReviewCheck" @focusin="sFocusTable='reviewCheck'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='reviewCheck' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="val.cntType > 0" :rowspan="val.cntType">{% val.checkType %}</td>
            <td>{% val.checkName %}</td>
            <td><i :class="val.checkYn == 'true' ? 'fa fa-lg fa-check-circle text-green' : ''" aria-hidden="true"></i></td>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="checkbox" v-model="val.customerCheckYn" :checked="val.customerCheckYn=='true'" value="true" />
                </span>
                <span v-show="!isModify"><i :class="val.customerCheckYn == 'true' ? 'fa fa-lg fa-check-circle text-green' : ''" aria-hidden="true"></i></span>
            </td>
            <td class="ta-l">
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="val.checkDesc" ref="inputReviewCheckDesc" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReviewCheckDesc)" />
                </span>
                <span v-show="!isModify">{% val.checkDesc %}</span>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-title gd-help-manual">
    <div class="flo-left pdt5 font-16">
    # 안내사항  <button type="button" v-if="isModify" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonConfirmGuide, ooDefaultJson.jsonConfirmGuide, 'after')">+ 추가</button>
    </div>
</div>
<table v-if="oUpsertInfo.jsonConfirmGuide != undefined" class="table table-cols table-default-center table-pd-5 table-th-height30 table-td-height30">
    <colgroup>
        <col v-if="isModify" class="w-5p" />
        <col class="w-20p" />
        <col class="" />
        <col v-if="isModify" class="w-10p" />
    </colgroup>
    <tr>
        <th v-if="isModify">이동</th>
        <th>구분</th>
        <th>내용</th>
        <th v-if="isModify">기능</th>
    </tr>
    <tbody is="draggable" :list="oUpsertInfo.jsonConfirmGuide" :animation="200" tag="tbody" handle=".handle">
    <tr v-if="oUpsertInfo.jsonConfirmGuide.length == 0">
        <td :colspan="isModify ? 4 : 2">입력한 안내사항이 없습니다. <span v-show="isModify">상단의 +추가 버튼을 클릭해 주세요</span></td>
    </tr>
    <tr v-else v-for="(val, key) in oUpsertInfo.jsonConfirmGuide" @focusin="sFocusTable='confirmGuide'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='confirmGuide' && iFocusIdx==key ? 'focused' : ''">
        <td :class="isModify ? 'handle' : ''" v-if="isModify">
            <div class="cursor-pointer hover-btn" >
                <i class="fa fa-bars" aria-hidden="true"></i>
            </div>
        </td>
        <td>
            <span v-show="isModify">
                <input class="form-control" type="text" v-model="val.guideType" ref="inputGuideType" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputGuideType)" />
            </span>
            <span v-show="!isModify">{% val.guideType %}</span>
        </td>
        <td class="ta-l">
            <span v-show="isModify">
                <input class="form-control" type="text" v-model="val.guideContent" ref="inputGuideContent" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputGuideContent)" />
            </span>
            <span v-show="!isModify">{% val.guideContent %}</span>
        </td>
        <td v-if="isModify">
            <button type="button" class="btn btn-blue btn-sm" @click="if (aGuideTypeList.length == 0) $.msg('저장된 안내사항이 없습니다.','','warning'); else { iChooseKeyConfirmGuide = key; $('#modal_sch_list_guide').modal('show'); }">선택</button>
            <button type="button" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonConfirmGuide, ooDefaultJson.jsonConfirmGuide, 'down', key)">+ 추가</button>
            <div class="btn btn-sm btn-red" @click="deleteElement(oUpsertInfo.jsonConfirmGuide, key)" >- 삭제</div>
        </td>
    </tr>
    </tbody>
</table>
<!--안내사항 리스트 modal-->
<div class="modal fade" id="modal_sch_list_guide" tabindex="-1" role="dialog" aria-modal="true" style="z-index:99999999999999999;">
    <div class="modal-dialog" role="document" style="width:1200px; top:0px;">
        <div class="modal-content" style="width:1200px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title">
                    <span class="font-14">안내사항 리스트</span>
                </span>
            </div>
            <div class="modal-body" style="overflow-y:scroll;" >
                <div>
                    <div class="search-detail-box form-inline">
                        <table class="table table-cols table-td-height0">
                            <colgroup>
                                <col class="width-sm">
                                <col class="">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>구분</th>
                                <td>
                                    <select v-model="sSchGuideType" class="form-control">
                                        <option value="">전체</option>
                                        <option v-for="val in aGuideTypeList" :value="val">{% val %}</option>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="text-align: center;">
                        <input type="submit" value="닫기" class="btn btn-lg btn-white" data-dismiss="modal">
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                        <colgroup>
                            <col class="w-20p" />
                            <col class="" />
                        </colgroup>
                        <tr>
                            <th>구분</th>
                            <th>내용</th>
                        </tr>
                        <tr v-show="sSchGuideType == '' || sSchGuideType == val.guideType" v-for="(val, key) in aoGuideList" @click="putGuideInfo(val);" class="cursor-pointer hover-btn">
                            <td>{% val.guideType %}</td>
                            <td class="ta-l">{% val.guideContent %}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>

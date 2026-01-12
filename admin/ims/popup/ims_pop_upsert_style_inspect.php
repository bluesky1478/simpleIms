<?php use SiteLabUtil\SlCommonUtil; ?>
<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .table td { padding:3px 6px!important; }
    .bootstrap-filestyle input{display: none }
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">생산품 샘플 검수 리뷰서 {% oUpsertInspect.sno == 0 ? '등록' : (!isModifyInspect ? '상세' : '수정') %}</h3>
        <div v-if="oUpsertInspect.sno > 0" class="btn-group font-18 bold">
            <div v-show="isModifyInspect" @click="save_style_inspect();" class="btn btn-red" style="line-height: 35px;">저장</div>
            <div v-show="!isModifyInspect" @click="isModifyInspect = true;" class="btn btn-red btn-red-line2" style="line-height: 35px;">수정하기</div>
            <div v-show="oUpsertInspect.sno > 0 && isModifyInspect" @click="isModifyInspect = false;" class="btn btn-white" style="line-height: 35px;">수정취소</div>
            <div v-show="!isModifyInspect" @click="window.open('<?=SlCommonUtil::getHost().'/ics/ics_work.php'?>?sno='+iChooseStyleSno);" class="btn btn-white" style="line-height: 35px;">작업지시서</div>
            <div v-show="!isModifyInspect" @click="openUrl(`style_inspect_print`,`<?=SlCommonUtil::getHost() . '/ics/ics_style_inspect.php'?>?sno=${iChooseStyleSno}`,1600,950);" class="btn btn-white" style="line-height: 35px;">인쇄</div>
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
                    <li role="presentation" class="active">
                        <a href="ims_pop_upsert_style_inspect.php?sno=<?=(int)$requestParam['sno']?>" >QC/인라인 검수</a>
                    </li>
                    <li role="presentation" class="">
                        <a href="ims_pop_upsert_style_inspect_delivery.php?sno=<?=(int)$requestParam['sno']?>" >납품검수</a>
                    </li>
                </ul>
            </div>
            <div class="mgt10 pdl5">
                <div>
                    <span v-show="false" @click="appendInspect('QC');" class="btn btn-white">QC {% iNextQcInspectTerm > 1 ? iNextQcInspectTerm+'차' : '' %} 등록</span>
                    <span v-show="false" @click="appendInspect('인라인');" class="btn btn-white">인라인 {% iNextInlineInspectTerm > 1 ? iNextInlineInspectTerm+'차' : '' %} 등록</span>
                </div>
                <table v-if="oUpsertInspect.jsonInspectSizeSpec != undefined" class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col class="w-150px" />
                        <col class="w-100px" />
                        <col v-if="oUpsertInspect.jsonInspectSizeSpec.length == 0" class="" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th rowspan="2" >측정부위</th>
                        <th rowspan="2" style="border-right:1px #E6E6E6 solid;">단위</th>
                        <th v-if="oUpsertInspect.jsonInspectSizeSpec.length == 0" colspan="3">QC/인라인 등록 버튼을 클릭해 주세요</th>
                        <th v-else :colspan="1 + val2.inspectChkUserList.length * 2" v-for="(val2, key2) in oUpsertInspect.jsonInspectSizeSpec" style="border-right:1px #E6E6E6 solid;">{% val2.inspectTitle %} <span v-show="isModifyInspect" @click="appendInspectChkUser(key2);" class="btn btn-white">추가 측정</span></th>
                    </tr>
                    <tr>
                        <template v-if="oUpsertInspect.jsonInspectSizeSpec.length == 0">
                            <td>차수별 기준사이즈</td>
                            <td colspan="2">검수자명 (검수일)</td>
                        </template>
                        <template v-else v-for="(val2, key2) in oUpsertInspect.jsonInspectSizeSpec">
                            <th>
                            <span v-if="isModifyInspect">
                                <select v-model="val2.inspectStandard" class="form-control" style="margin:0px auto; background: #fff; min-width:40px!important;">
                                    <option v-for="(val4, key4) in aoSizeSpecList[0].oStandardSpec" :value="key4">{% key4 %}</option>
                                </select>
                            </span>
                                <span v-else>기준 [{% val2.inspectStandard %}]</span>
                            </th>
                            <template v-for="(val3, key3) in val2.inspectChkUserList">
                                <th colspan="2" style="border-right:1px #E6E6E6 solid;">{% val3.chkUserName %} ({% $.formatShortDateWithoutWeek(val3.chkSizeDt) %})</th>
                            </template>
                        </template>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(val, key) in aoSizeSpecList" @focusin="sFocusTable='inspectSizeSpec'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='inspectSizeSpec' && iFocusIdx==key ? 'focused' : ''">
                        <th>{% val.sTitle %}</th>
                        <th style="border-right:1px #E6E6E6 solid;">{% val.sUnit %}</th>
                        <template v-if="oUpsertInspect.jsonInspectSizeSpec.length == 0">
                            <td>기준값</td>
                            <td>입력한 측정치</td>
                            <td>기준값과 입력한 측정치 차이</td>
                        </template>
                        <template v-else v-for="(val2, key2) in oUpsertInspect.jsonInspectSizeSpec">
                            <td>{% val.oStandardSpec[val2.inspectStandard] %}</td>
                            <template v-for="(val3, key3) in val2.inspectChkUserList">
                                <td>
                                <span v-if="isModifyInspect && val3.chkUserSno == istaticManagerSno">
                                    <input type="text" v-model="val3.chkSizeList[val.sTitle]" :ref="'inputReview_chkSize'+key2+'_'+key3" @keyup="sTmpStandardSize=val2.inspectStandard; val2.inspectStandard=''; val2.inspectStandard=sTmpStandardSize; gfnMoveInputBox(val, key, event.key, $refs['inputReview_chkSize'+key2+'_'+key3])" class="form-control" style="min-width:40px!important; max-width:200px; margin:0px auto;" placeholder="측정값" />
                                </span>
                                    <span v-else>{% val3.chkSizeList[val.sTitle] %}</span>
                                </td>
                                <td style="border-right:1px #E6E6E6 solid;">
                                    <span v-if="val3.chkSizeList[val.sTitle] == ''">-</span>
                                    <span v-else :class="Number(val3.chkSizeList[val.sTitle]) > Number(val.oStandardSpec[val2.inspectStandard]) ? 'text-danger' : (Number(val3.chkSizeList[val.sTitle]) < Number(val.oStandardSpec[val2.inspectStandard]) ? 'text-blue' : '')">{% Math.round((Number(val3.chkSizeList[val.sTitle]) - Number(val.oStandardSpec[val2.inspectStandard]))*100) / 100 %}</span>
                                </td>
                            </template>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <div class="table-title pdt5 font-18"># 검수 사진 등록(QC/인라인검수 최초등록 후 업로드 가능)(보기 모드에서만 업로드 가능)</div>
                <div v-show="oUpsertInspect.sno > 0 && !isModifyInspect">
                    <table class="table table-cols table-default-center table-pd-5 mgt5 w-100p">
                        <tr>
                            <th v-for="val in oUpsertInspect.jsonInspectList">{% val %}</th>
                        </tr>
                        <tr>
                            <td v-for="val in oUpsertInspect.jsonInspectList">
                                <file-upload2 :file="ooStyleInspectFileList['styleInspect'+val.replace('인라인','INLINE').replace('차','')]" :id="'styleInspect'+val.replace('인라인','INLINE').replace('차','')" :params="{'customerSno':oChooseStyleInfo.customerSno,'projectSno':oChooseStyleInfo.projectSno,'styleSno':iChooseStyleSno,'eachSno':oUpsertInspect.sno}" :accept="false"></file-upload2>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="mgt20 pdl5">
                <div class="table-title pdt5 font-18"># 검수 내역 (이노버 확인 사항)</div>
                <table class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                    <colgroup>
                        <col class="w-150px" />
                        <col class="" />
                        <col class="w-150px" />
                        <col class="w-200px" />
                        <col class="w-150px" />
                        <col class="w-200px" />
                        <col v-if="isModifyInspect" class="w-150px" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th rowspan="2">구분</th>
                        <th rowspan="2" style="border-right:1px #E6E6E6 solid;">내용</th>
                        <th colspan="2">QC 단계</th>
                        <th colspan="2" style="border-right:1px #E6E6E6 solid;">INLINE 단계</th>
                        <th v-if="isModifyInspect" rowspan="2">기능</th>
                    </tr>
                    <tr>
                        <th>상 / 중 / 하</th>
                        <th>이상 유무</th>
                        <th>상 / 중 / 하</th>
                        <th style="border-right:1px #E6E6E6 solid;">이상 유무</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(val, key) in oUpsertInspect.jsonInspectCheck" @focusin="sFocusTable='inspectCheck'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='inspectCheck' && iFocusIdx==key ? 'focused' : ''">
                        <td v-if="val.cntType > 0" :rowspan="val.cntType">
                            <span v-if="isModifyInspect">
                                <input class="form-control" type="text" v-model="val.chkType" @keydown="startChgCheckTypeName(event.target.value);" @keyup="endChgCheckTypeName(event.target.value)" />
                                <button type="button" class="btn btn-white btn-sm" @click="appendInspectCheckByType(val.chkType);" style="margin-top:5px;">+ 구분추가</button>
                            </span>
                            <span v-else>{% val.chkType %}</span>
                        </td>
                        <td>
                            <span v-if="isModifyInspect">
                                <input class="form-control" type="text" v-model="val.chkTitle" ref="inputReview_chkTitle" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReview_chkTitle)" />
                            </span>
                            <span v-else>{% val.chkTitle %}</span>
                        </td>
                        <td>
                            <span v-if="isModifyInspect">
                                <label class="radio-inline"><input type="radio" :name="'sRadioQcPoint'+key" value="상" v-model="val.qcPoint"/>상</label>
                                <label class="radio-inline"><input type="radio" :name="'sRadioQcPoint'+key" value="중" v-model="val.qcPoint"/>중</label>
                                <label class="radio-inline"><input type="radio" :name="'sRadioQcPoint'+key" value="하" v-model="val.qcPoint"/>하</label>
                            </span>
                            <span v-else>{% val.qcPoint %}</span>
                        </td>
                        <td>
                        <span v-if="isModifyInspect">
                            <input class="form-control" type="text" v-model="val.qcDesc" ref="inputReview_qcDesc" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReview_qcDesc)" />
                        </span>
                            <span v-else>{% val.qcDesc %}</span>
                        </td>
                        <td>
                        <span v-if="isModifyInspect">
                            <label class="radio-inline"><input type="radio" :name="'sRadioInlinePoint'+key" value="상" v-model="val.inlinePoint"/>상</label>
                            <label class="radio-inline"><input type="radio" :name="'sRadioInlinePoint'+key" value="중" v-model="val.inlinePoint"/>중</label>
                            <label class="radio-inline"><input type="radio" :name="'sRadioInlinePoint'+key" value="하" v-model="val.inlinePoint"/>하</label>
                        </span>
                            <span v-else>{% val.inlinePoint %}</span>
                        </td>
                        <td>
                            <span v-if="isModifyInspect">
                                <input class="form-control" type="text" v-model="val.inlineDesc" ref="inputReview_inlineDesc" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReview_inlineDesc)" />
                            </span>
                            <span v-else>{% val.inlineDesc %}</span>
                        </td>
                        <td v-if="isModifyInspect">
                            <button type="button" class="btn btn-white btn-sm" @click="appendInspectCheck(key);">+ 추가</button>
                            <div class="btn btn-sm btn-red" @click="deleteInspectCheck(key);" >- 삭제</div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mgt20 pdl5">
                <div class="table-title pdt5 font-18"># QC/INLINE SAMPLE COMMENT</div>
                <table class="w-100p">
                    <colgroup>
                        <col class="w-50p" />
                        <col class="w-50p" />
                    </colgroup>
                    <tr>
                        <td v-for="val in [1,2]" style="vertical-align: top; border-left:1px #E6E6E6 solid; border-right:1px #E6E6E6 solid;" >
                            <table class="table table-cols table-default-center table-pd-5 mgt5" style="margin-bottom:0 !important;">
                                <colgroup>
                                    <col v-if="isModifyInspect" class="w-100px" />
                                    <col class="w-100px" />
                                    <col class="w-100px" />
                                    <col class="w-100px" />
                                    <col class="" />
                                </colgroup>
                                <thead>
                                <tr>
                                    <th :colspan="isModifyInspect ? 5 : 4">{% val == 1 ? '생산처' : '이노버' %} 검사 의견 <span @click="addInspectComment(val)" class="btn btn-white">등록</span></th>
                                </tr>
                                <tr>
                                    <th v-if="isModifyInspect">강조여부</th><th>등록일</th><th>등록자</th><th>구분</th><th>내용</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(val2, key2) in oUpsertInspect['jsonInspectComment'+val]" @focusin="sFocusTable='inspectComment'+val; iFocusIdx=key2;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='inspectComment'+val && iFocusIdx==key2 ? 'focused' : ''">
                                    <td v-if="isModifyInspect">
                                    <span v-if="isModifyInspect && val2.commentUserSno == istaticManagerSno">
                                        <select v-model="val2.impectYn" class="form-control" style="margin:0px auto;">
                                            <option value="Y">Y</option>
                                            <option value="N">N</option>
                                        </select>
                                    </span>
                                        <span v-else>{% val2.impectYn %}</span>
                                    </td>
                                    <td :class="val2.impectYn == 'Y' ? 'text-danger font-bold' : ''">{% $.formatShortDateWithoutWeek(val2.commentDt) %}</td>
                                    <td :class="val2.impectYn == 'Y' ? 'text-danger font-bold' : ''">{% val2.commentName %}</td>
                                    <td :class="val2.impectYn == 'Y' ? 'text-danger font-bold' : ''">
                                    <span v-if="isModifyInspect && val2.commentUserSno == istaticManagerSno">
                                        <select v-model="val2.commentInspect" class="form-control">
                                            <option value="전체">전체</option>
                                            <option v-for="val3 in oUpsertInspect.jsonInspectList" :value="val3">{% val3 %}</option>
                                        </select>
                                    </span>
                                        <span v-else>{% val2.commentInspect %}</span>
                                    </td>
                                    <td :class="val2.impectYn == 'Y' ? 'text-danger font-bold' : ''">
                                    <span v-if="isModifyInspect && val2.commentUserSno == istaticManagerSno">
                                        <input type="text" v-model="val2.commentDesc" class="form-control" />
                                    </span>
                                        <span v-else>{% val2.commentDesc %}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="dp-flex" style="justify-content: center; border-top:1px #888 solid; margin-top:20px; padding-top:5px;">
            <div v-show="isModifyInspect" @click="save_style_inspect();" class="btn btn-lg btn-red mg5">{% oUpsertInspect.sno == 0 ? '등록' : '저장' %}</div>
            <div v-show="!isModifyInspect" @click="isModifyInspect = true;" class="btn btn-lg btn-red btn-red-line2 mg5">수정하기</div>
            <div v-show="oUpsertInspect.sno > 0 && isModifyInspect" @click="isModifyInspect = false;" class="btn btn-lg btn-white mg5">수정취소</div>
            <div v-show="!isModifyInspect" @click="window.open('<?=SlCommonUtil::getHost().'/ics/ics_work.php'?>?sno='+iChooseStyleSno);" class="btn btn-lg btn-white mg5">작업지시서</div>
            <div v-show="!isModifyInspect" @click="openUrl(`style_inspect_print`,`<?=SlCommonUtil::getHost() . '/ics/ics_style_inspect.php'?>?sno=${iChooseStyleSno}`,1600,950);" class="btn btn-lg btn-white mg5">인쇄</div>
            <div @click="self.close()" class="btn btn-gray btn-lg mg5">닫기</div>
        </div>
    </div>
</section>

<script type="text/javascript">
    //스타일sno get파라메터
    var igstaticChooseStyleSno = <?=(int)$requestParam['sno']?>;
    var igstaticOpenWindowType = '<?=$requestParam['append_type']?>';

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            iChooseStyleSno : 0, //QC/인라인 검수관리 버튼 클릭시 styleSno 담음
            sFocusTable : '', //html table focus용 변수
            iFocusIdx : 0, //html table focus용 변수
            isModifyInspect : false, //등록/수정 flag
            aoSizeSpecList : [], //스타일에서 가져온 사이즈스펙정보 담는 배열
            oChooseStyleInfo : {}, //QC/인라인 검수관리 버튼 클릭시 가져온 선택스타일의 정보
            oUpsertInspect : {}, //QC/인라인 검수 정보(upsert용)
            iNextQcInspectTerm : 1, //QC/인라인 검수 -> 사이즈스펙 측정 QC검사의 다음차수
            iNextInlineInspectTerm : 1, //QC/인라인 검수 -> 사이즈스펙 측정 인라인검사의 다음차수
            istaticManagerSno : <?=\Session::get('manager.sno')?>, //현재 접속자 sno. 고정값
            sstaticManagerName : '<?=\Session::get('manager.managerNm')?>', //현재 접속자명. 고정값
            sstaticCurrDt : '<?=date('Y-m-d')?>', //현재일자. 고정값
            aTmpTargetChgTypeNmKeys : [], //QC/인라인 검수 -> 검수내역 - 구분명 변경시 같은 구분명 가진 row의 key 담음(개발용)
            ooDefaultJson : { //QC/인라인 검수 -> 검수내역, 코멘트 - 항목추가시 쓰이는 default obj form
                'jsonInspectCheck' : {cntType:0, chkType:'', chkTitle:'', qcPoint:'', qcDesc:'', inlinePoint:'', inlineDesc:'', },
                'jsonInspectComment' : {impectYn:'', commentUserSno:'', commentDt:'', commentName:'', commentInspect:'', commentDesc:'', },
            },
            ooStyleInspectFileList : {}, //QC/인라인 검수 -> 차수별 첨부파일 : default obj
            sTmpStandardSize : '', //개발용 변수. 측정값 입력시 기준사이즈 임시로 담는다.
        });

        ImsBoneService.setMethod(serviceData,{
            openInspectModal : function(iStyleSno) {
                vueApp.iChooseStyleSno = iStyleSno;
                ImsNkService.getList('styleSimple', {'styleSnos':[vueApp.iChooseStyleSno], 'sch_level':'1'}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        if (data.sizeSpec != undefined) {
                            if (data.sizeSpec.specData == undefined || data.sizeSpec.specData[0] == undefined || data.sizeSpec.specData[0].spec == undefined) {
                                $.msg('스타일의 사이즈스펙 정보가 없습니다(기준값, 편차 필요).','','warning').then((data)=> {
                                    self.close();
                                });
                            }
                            //보기버튼 클릭으로 진입시
                            if (igstaticOpenWindowType == '') {
                                if (data.sno == 0) {
                                    $.msg('먼저 QC등록을 진행해 주시기 바랍니다','','warning').then((data)=> {
                                        self.close();
                                    });
                                }
                            }

                            //스타일테이블로부터 사이즈스펙 정보 가져와서 배열화
                            vueApp.aoSizeSpecList = [];
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
                                vueApp.aoSizeSpecList.push({'sTitle':val.title, 'sUnit':val.unit, 'oStandardSpec':{}});
                                $.each(oSpecRange, function(key2, val2) {
                                    vueApp.aoSizeSpecList[key].oStandardSpec[key2] = Number(val.spec) + Number(val.deviation)*val2;
                                });
                            });


                            //jsonInspectList : ['QC1차', 'QC2차', '인라인1차', ....]
                            //jsonInspectSizeSpec : [{'inspectTitle':'QC1차', 'inspectStandard':'100', 'inspectChkUserList':[{'chkUserSno':'51', 'chkUserName':'하나어패럴', 'chkSizeDt':'2025-10-04', 'chkSizeList':{'총기장(뒤)':'47'}, ...}, ...]}, ...]
                            //jsonInspectCheck : [{'cntType':4, 'chkType':'완성', 'chkTitle':'작업지시서 준수', 'qcPoint':'상', 'qcDesc':'', 'inlinePoint':'중', 'inlineDesc':''}, .....]
                            //jsonInspectComment1 : [{'impectYn':'y', 'commentUserSno':'51', 'commentDt':'2025-11-06', 'commentName':'하나어패럴', 'commentInspect':'QC1차', 'commentDesc':''}, .....]
                            //jsonInspectComment2 : [{'impectYn':'n', 'commentUserSno':'51', 'commentDt':'2025-11-06', 'commentName':'인남규', 'commentInspect':'전체', 'commentDesc':''}, .....]


                            //차수별 첨부파일 obj 정의
                            if (data.jsonInspectList.length > 0) {
                                let sTmpFileDiv = '';
                                $.each(data.jsonInspectList, function(key, val) {
                                    sTmpFileDiv = 'styleInspect'+val.replace('인라인','INLINE').replace('차','');
                                    if (data.fileList[sTmpFileDiv] == undefined) vueApp.ooStyleInspectFileList[sTmpFileDiv] = {};
                                    else vueApp.ooStyleInspectFileList[sTmpFileDiv] = data.fileList[sTmpFileDiv];
                                });
                            }

                            vueApp.oChooseStyleInfo = {'customerName':data.customerName, 'prdExQty':data.prdExQty, 'productName':data.productName, 'produceCompanyName':data.produceCompanyName, 'projectSno':data.projectSno, 'customerSno':data.customerSno, 'standardSize':data.sizeSpec.standard };

                            //스타일의 사이즈스펙 항목이 변경되는(항목 추가/삭제/항목명변경) 경우를 감안하여 재구성
                            let aoTmpSizeSpec = $.copyObject(data.jsonInspectSizeSpec);
                            if (aoTmpSizeSpec.length > 0) {
                                $.each(aoTmpSizeSpec, function(key, val) {
                                    $.each(val.inspectChkUserList, function(key2, val2) {
                                        val2.chkSizeList = {};
                                        $.each(vueApp.aoSizeSpecList, function(key3, val3) {
                                            val2.chkSizeList[val3.sTitle] = data.jsonInspectSizeSpec[key].inspectChkUserList[key2].chkSizeList[val3.sTitle] != undefined ? data.jsonInspectSizeSpec[key].inspectChkUserList[key2].chkSizeList[val3.sTitle] : '';
                                        });
                                    });
                                });
                            }
                            vueApp.oUpsertInspect = {'sno':data.sno, 'jsonInspectList':data.jsonInspectList, 'jsonInspectSizeSpec':aoTmpSizeSpec, 'jsonInspectCheck':data.jsonInspectCheck, 'jsonInspectComment1':data.jsonInspectComment1, 'jsonInspectComment2':data.jsonInspectComment2};

                            //QC,인라인 다음차수 계산
                            if (vueApp.oUpsertInspect.jsonInspectList.length > 0) {
                                $.each(vueApp.oUpsertInspect.jsonInspectList, function(key, val) {
                                    if (val.indexOf('QC') !== -1) vueApp.iNextQcInspectTerm++;
                                    if (val.indexOf('인라인') !== -1) vueApp.iNextInlineInspectTerm++;
                                });
                            }


                            //검수내역 내용 없으면(등록인 경우) default-form 넣기
                            if (vueApp.oUpsertInspect.jsonInspectCheck.length == 0) {
                                vueApp.oUpsertInspect.jsonInspectCheck = data.chkDefaultForm;
                                vueApp.calcCheckRowspan();
                            }

                            //QC/인라인 검수 - 차수별 첨부파일 동작 정의
                            vueApp.$nextTick(function () {
                                $('.set-dropzone').addClass('dropzone');
                                if (Object.keys(vueApp.ooStyleInspectFileList).length > 0) {
                                    $.each(vueApp.ooStyleInspectFileList, function(key, val) {
                                        ImsService.setDropzone(vueApp, key, vueApp.uploadAfterActionInspectFile);
                                    });
                                }

                                //QC등록 or 인라인등록 클릭으로 진입시 QC등록 or 인라인등록 클릭
                                if (igstaticOpenWindowType != '') {
                                    vueApp.isModifyInspect = true;
                                    if (igstaticOpenWindowType == 'qc') vueApp.appendInspect('QC');
                                    else if (igstaticOpenWindowType == 'inline') {
                                        if (vueApp.iNextQcInspectTerm == 1) {
                                            $.msg('인라인등록을 진행하시려면 먼저 QC등록을 진행해 주시기 바랍니다','','warning').then((data)=> {
                                                self.close();
                                            });
                                        } else vueApp.appendInspect('인라인');
                                    }
                                }
                            });
                        }
                    });
                });
            },
            //체크사항 - 구분의 rowspan 계산
            calcCheckRowspan : ()=>{
                let iCntType = 0;
                for (var i = vueApp.oUpsertInspect.jsonInspectCheck.length-1; i >= 0; i--) {
                    iCntType++;
                    if (i == 0 || vueApp.oUpsertInspect.jsonInspectCheck[i].chkType != vueApp.oUpsertInspect.jsonInspectCheck[i-1].chkType) {
                        vueApp.oUpsertInspect.jsonInspectCheck[i].cntType = iCntType;
                        iCntType = 0;
                    } else vueApp.oUpsertInspect.jsonInspectCheck[i].cntType = 0;
                }
            },
            //체크사항 - 구분명 수정시(start, end)
            startChgCheckTypeName : (sPrevNm)=>{
                if (vueApp.aTmpTargetChgTypeNmKeys.length == 0) {
                    $.each(vueApp.oUpsertInspect.jsonInspectCheck, function(key, val) {
                        if (vueApp.oUpsertInspect.jsonInspectCheck[key].chkType == sPrevNm) vueApp.aTmpTargetChgTypeNmKeys.push(key);
                    });
                }
            },
            endChgCheckTypeName : (sChgNm)=>{
                if (vueApp.aTmpTargetChgTypeNmKeys.length > 0) {
                    $.each(vueApp.aTmpTargetChgTypeNmKeys, function(key, val) {
                        vueApp.oUpsertInspect.jsonInspectCheck[this].chkType = sChgNm;
                    });
                    vueApp.aTmpTargetChgTypeNmKeys = [];
                }
            },
            //체크사항 - 구분 추가시
            appendInspectCheckByType : (sTypeNm)=>{
                let iKey = 0;
                for (var i = vueApp.oUpsertInspect.jsonInspectCheck.length-1; i >= 0; i--) {
                    if (vueApp.oUpsertInspect.jsonInspectCheck[i].chkType == sTypeNm) {
                        iKey = i;
                        break;
                    }
                }
                vueApp.addElement(vueApp.oUpsertInspect.jsonInspectCheck, vueApp.ooDefaultJson.jsonInspectCheck, 'down', iKey);
                vueApp.calcCheckRowspan();
            },
            //체크사항 - 항목 추가시
            appendInspectCheck : (iKey)=>{
                let sTargetType = vueApp.oUpsertInspect.jsonInspectCheck[iKey].chkType;
                let oAppendObj = vueApp.addElement(vueApp.oUpsertInspect.jsonInspectCheck, vueApp.ooDefaultJson.jsonInspectCheck, 'down', iKey);
                oAppendObj.chkType = sTargetType;
                vueApp.calcCheckRowspan();
            },
            //체크사항 - 항목 삭제시
            deleteInspectCheck : (iKey)=>{
                vueApp.deleteElement(vueApp.oUpsertInspect.jsonInspectCheck, iKey);
                vueApp.calcCheckRowspan();
            },
            //사이즈스펙 측정 - 차수 추가시
            appendInspect : (sInspectType)=>{
                let sAppendTitle = '';
                if (sInspectType == 'QC') {
                    sAppendTitle = sInspectType+vueApp.iNextQcInspectTerm+'차';
                    vueApp.iNextQcInspectTerm++;
                } else {
                    sAppendTitle = sInspectType+vueApp.iNextInlineInspectTerm+'차';
                    vueApp.iNextInlineInspectTerm++;
                }
                vueApp.oUpsertInspect.jsonInspectList.push(sAppendTitle);

                let oTmpInspect = {'inspectTitle':sAppendTitle, 'inspectStandard':vueApp.oChooseStyleInfo.standardSize, 'inspectChkUserList':[{'chkUserSno':vueApp.istaticManagerSno, 'chkUserName':vueApp.sstaticManagerName, 'chkSizeDt':vueApp.sstaticCurrDt, 'chkSizeList':{}}]};
                $.each(vueApp.aoSizeSpecList, function(key, val) {
                    oTmpInspect.inspectChkUserList[0].chkSizeList[val.sTitle] = '';
                });

                vueApp.oUpsertInspect.jsonInspectSizeSpec.push(oTmpInspect);
            },
            //사이즈스펙 측정 - 추가측정(차수 안에서 측정자추가)
            appendInspectChkUser : (iKeyInspect)=>{
                let oTmpInspectChk = {'chkUserSno':vueApp.istaticManagerSno, 'chkUserName':vueApp.sstaticManagerName, 'chkSizeDt':vueApp.sstaticCurrDt, 'chkSizeList':{}};
                $.each(vueApp.aoSizeSpecList, function(key, val) {
                    oTmpInspectChk.chkSizeList[val.sTitle] = '';
                });

                vueApp.oUpsertInspect.jsonInspectSizeSpec[iKeyInspect].inspectChkUserList.push(oTmpInspectChk);
            },
            //의견 추가
            addInspectComment : (iCommentNum)=>{
                let oAppendObj = vueApp.addElement(vueApp.oUpsertInspect['jsonInspectComment'+iCommentNum], vueApp.ooDefaultJson.jsonInspectComment, 'after');
                oAppendObj.impectYn = 'N';
                oAppendObj.commentUserSno = vueApp.istaticManagerSno;
                oAppendObj.commentDt = vueApp.sstaticCurrDt;
                oAppendObj.commentName = vueApp.sstaticManagerName;
                oAppendObj.commentInspect = '전체';
                vueApp.isModifyInspect = true;
            },

            //검수 upsert함수(파일업로드는 별도)
            save_style_inspect : ()=>{
                $.msgConfirm('QC/인라인 검수내용을 저장하시겠습니까?','').then(function(result){
                    if( result.isConfirmed ){
                        vueApp.oUpsertInspect.styleSno = vueApp.iChooseStyleSno;

                        $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertInspect, 'table_number':13}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                location.href = '/ims/popup/ims_pop_upsert_style_inspect.php?sno=' + vueApp.iChooseStyleSno;
                            });
                        });
                    }
                });
            },
            //파일 업로드할때 실행하는 함수
            uploadAfterActionInspectFile : (tmpFile, dropzoneId)=>{
                ImsProductService.uploadAfterAction(tmpFile, dropzoneId, (saveFileList, promptValue)=>{
                    let saveData = {
                        customerSno : vueApp.oChooseStyleInfo.customerSno,
                        projectSno : vueApp.oChooseStyleInfo.projectSno,
                        styleSno : vueApp.iChooseStyleSno,
                        eachSno : vueApp.oUpsertInspect.sno,
                        fileDiv : dropzoneId,
                        fileList : saveFileList,
                        memo : promptValue,
                    };
                    //console.log(saveData);
                    $.imsPost('saveProjectFiles',{
                        saveData : saveData
                    }).then((data)=>{
                        if(200 === data.code) {
                            location.href = '/ims/popup/ims_pop_upsert_style_inspect.php?sno=' + vueApp.iChooseStyleSno;
                        }
                    });
                });
            },
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            vueApp.openInspectModal(igstaticChooseStyleSno);
        });

        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
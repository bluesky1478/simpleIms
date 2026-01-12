
<!--파일정보-->
<div class="table-title gd-help-manual" v-show="oUpsertInfo.sno > 0 && !isModify">
    <div class="flo-left area-title">
        <span class="godo"># 샘플 제작 파일</span>
    </div>
    <div class="flo-right pdt5 pdl5 mgb5"></div>
</div>
<div class=""  v-show="oUpsertInfo.sno > 0 && !isModify">
    <table class="table table-cols table-pd-5 table-th-height30 table-td-height30">
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
        <tr v-show="!isModify">
            <th>샘플 도안</th>
            <td>
                <div>
                    <div v-if="!$.isEmpty(oUpsertInfo.fileList['sampleFile8']) && !$.isEmpty(oUpsertInfo.fileList['sampleFile8'].files) && oUpsertInfo.fileList['sampleFile8'].files.length > 0">
                        <draggable v-model="oUpsertInfo.fileList.sampleFile8.files" @end="$.imsPost('modifySimpleDbCol', {'table_number':1, 'colNm':'fileList', 'where':{'sno':oUpsertInfo.fileList.sampleFile8.sno}, 'data':oUpsertInfo.fileList.sampleFile8.files});">
                            <div v-for="(file,fileIndex) in oUpsertInfo.fileList.sampleFile8.files" :key="fileIndex" class="cursor-pointer sl-blue">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %} : {% file.fileName %}</a>
                            </div>
                        </draggable>
                        <div class="notice-info" v-if="!$.isEmpty(oUpsertInfo.fileList.sampleFile8.files) && oUpsertInfo.fileList.sampleFile8.files.length > 1">
                            도안 순서 변경 가능
                        </div>
                    </div>
                </div>
                <form id="sampleFile8" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
            </td>
            <th>마크 도안</th>
            <td >
                <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile9" id="sampleFile9" :params="oUpsertInfo" :accept="false"></simple-file-not-history-upload>
            </td>
            <th>패턴 파일</th>
            <td>
                <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile3" id="sampleFile3" :params="oUpsertInfo" :accept="false"></simple-file-not-history-upload>
            </td>
            <th>마카 파일</th>
            <td>
                <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile10" id="sampleFile10" :params="oUpsertInfo" :accept="false"></simple-file-not-history-upload>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<table v-if="(oUpsertInfo.fileList['sampleFile8'].files != undefined && oUpsertInfo.fileList['sampleFile8'].files.length > 0) || (oUpsertInfo.fileList['sampleFile9'].files != undefined && oUpsertInfo.fileList['sampleFile9'].files.length > 0)" style="width:100%;">
    <colgroup>
        <col class="w-50p" />
        <col class="w-50p" />
    </colgroup>
    <tr>
        <td>
            <div class="table-title gd-help-manual">
                <div class="flo-left pdt5 pdl5"># 도식화</div>
            </div>
        </td>
        <td>
            <div class="table-title gd-help-manual">
                <div class="flo-left pdt5 pdl5"># 마크도안</div>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <img v-if="checkImageExtension(val.fileName)" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in oUpsertInfo.fileList['sampleFile8'].files" style="width:100%;" />
        </td>
        <td style="vertical-align: top;">
            <img v-if="checkImageExtension(val.fileName)" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in oUpsertInfo.fileList['sampleFile9'].files" style="width:100%;" />
        </td>
    </tr>
</table>
<div v-if="oUpsertInfo.jsonFitSpec != null && oUpsertInfo.jsonFitSpec != ''" v-show="oUpsertInfo.jsonFitSpec.length > 0" class="table-title gd-help-manual">
    <div class="pdt5 pdl5 font-16"># 사이즈 스펙 ({% oUpsertInfo.fitName %})</div>
</div>
<div v-if="oUpsertInfo.jsonFitSpec != null && oUpsertInfo.jsonFitSpec != ''" v-show="oUpsertInfo.jsonFitSpec.length > 0" class="dp-flex">

    <!--v-if="ooCustomerFitList.sizeName.length > 0"-->
    <table :class="'table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30 ' + (ooCustomerFitList.sizeName.length > 0?'w-100p':'w-60p') ">
        <tbody>
        <tr>
            <th colspan="3">구분</th>
            <td class="ta-c text-danger">이노버 표준</td>
            <template v-if="ooCustomerFitList.sizeName.length > 0">
                <th :colspan="ooCustomerFitList.sizeName.length" class="text-blue">고객제공 사이즈</th>
            </template>
            <td colspan="isModify ? 3 : 2"></td>
        </tr>
        <tr>
            <th :rowspan="oUpsertInfo.jsonFitSpec.length+1" style="width:120px!important;">측정항목</th>
            <th style="width:220px!important;">부위</th>
            <th>편차</th>
            <th>
                <span v-show="isModify"><input class="form-control" type="text" v-model="oUpsertInfo.fitSize" style="width:50px;" /></span>
                <span v-show="!isModify">{% oUpsertInfo.fitSize %}</span>
            </th>
            <template v-if="ooCustomerFitList.sizeName.length > 0">
                <th v-for="(val, key) in ooCustomerFitList.sizeName" class="text-blue">{% val %}</th>
            </template>
            <th>확정 스펙</th>
            <th>단위</th>
            <th v-show="isModify">기능</th>
        </tr>
        <tr v-for="(val, key) in oUpsertInfo.jsonFitSpec" @focusin="sFocusTable='fitSpec'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='fitSpec' && iFocusIdx==key ? 'focused' : ''">
            <th>
                <span v-if="isModify">
                    <input class="form-control" type="text" v-model="val.optionName" ref="inputOptionName" @keyup="gfnMoveInputBox(oUpsertInfo.jsonFitSpec, key, event.key, $refs.inputOptionName)" style="width:130px;" />
                </span>
                <span v-else>{% val.optionName %}</span>
            </th>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="val.optionRange" ref="inputOptionRange" @keyup="gfnMoveInputBox(oUpsertInfo.jsonFitSpec, key, event.key, $refs.inputOptionRange)" style="width:70px;" />
                </span>
                <span v-show="!isModify">{% val.optionRange %}</span>
            </td>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="val.optionValue" ref="inputOptionValue" @keyup="gfnMoveInputBox(oUpsertInfo.jsonFitSpec, key, event.key, $refs.inputOptionValue)" style="width:80px;" />
                </span>
                <span v-show="!isModify">{% val.optionValue %}</span>
            </td>
            <template v-if="ooCustomerFitList.sizeName.length > 0">
                <td v-for="(val2, key2) in ooCustomerFitList.sizeName">
                    {% ooCustomerFitList[val.optionName] != undefined ? ooCustomerFitList[val.optionName][val2] : '' %}
                </td>
            </template>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="hidden" v-model="oUpsertInfo.jsonFixedSpec[key].optionName" />
                    <input class="form-control" type="text" v-model="oUpsertInfo.jsonFixedSpec[key].optionValue" ref="inputFixedValue" @keyup="gfnMoveInputBox(oUpsertInfo.jsonFitSpec, key, event.key, $refs.inputFixedValue)"style="width:80px;" />
                </span>
                <span v-show="!isModify">{% oUpsertInfo.jsonFixedSpec[key].optionValue %}</span>
            </td>
            <td>
                <span v-show="isModify">
                    <select class="form-control" v-model="val.optionUnit" style="width: 100%;">
                        <option value="CM">CM</option>
                        <option value="인치">인치</option>
                    </select>
                </span>
                <span v-show="!isModify">{% val.optionUnit %}</span>
            </td>
            <td v-show="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="addSpecOption()">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteSpecOption(key)" v-show="oUpsertInfo.jsonFitSpec.length > 1">- 삭제</div>
                <div class="btn btn-sm btn-red" disabled="" v-show="1 >= oUpsertInfo.jsonFitSpec.length" title="최소 1개 필요">- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>

    <div v-if="0 >= ooCustomerFitList.sizeName.length" class="ta-c w-30p">
        * 고객 제공 샘플 없음
    </div>

</div>

<div style="margin:20px 0px;">

    <div class="table-title gd-help-manual">
        <div class="flo-left pdt5 font-16">
            # 샘플 제작비 정보
        </div>
        <div class="font-15 bold text-green">&nbsp; &nbsp; &nbsp; 현재환율 : {% $.setNumberFormat(sCurrDollerRatio) %} ({% sCurrDollerRatioDt %})</div>
    </div>

    <table class="table table-cols table-default-center table-th-height30 table-td-height30 mgt5" style="margin-bottom:0 !important;">
        <colgroup>
            <col class="w-8p" />
            <!--<col class="w-8p" />
            <col class="w-8p" />-->
            <col class="" />
            <col class="w-9p" />
            <col class="w-9p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-150px" />
        </colgroup>
        <tr>
            <th>기획 생산가</th>
            <!--<th>가생산가</th>
            <th>생산 견적</th>-->
            <th>샘플 총 제작비용</th>
            <th>샘플 원자재 소계</th>
            <th>샘플 부자재 소계</th>
            <th>기능 샘플비</th>
            <th>마크 샘플비</th>
            <th>공임 비용</th>
            <th>기타 비용</th>
            <th>환율</th>
            <th :class="String(oUpsertInfo.dollerRatioDt).substring(0,5)=='0000-'?'text-danger':''">환율기준일</th>
        </tr>
        <tr>
            <td>{% $.setNumberFormat(oUpsertInfo.planPrdCost) %}</td>
            <!--<td>TODO:가생산가</td>
            <td>TODO:생산견적</td>-->
            <td>
                <span :class="Number(iSumFabricAmt+iSumSubFabricAmt+iSumUtilAmt+iSumMarkAmt+iSumLaborAmt+iSumEtcAmt) > Number(oUpsertInfo.planPrdCost) ? 'text-danger font-15' : ''">
                    {% $.setNumberFormat(iSumFabricAmt+iSumSubFabricAmt+iSumUtilAmt+iSumMarkAmt+iSumLaborAmt+iSumEtcAmt) %}원
                </span>
            </td>
            <td>{% $.setNumberFormat(iSumFabricAmt) %} 원</td>
            <td>{% $.setNumberFormat(iSumSubFabricAmt) %} 원</td>
            <td>{% $.setNumberFormat(iSumUtilAmt) %} 원</td>
            <td>{% $.setNumberFormat(iSumMarkAmt) %} 원</td>
            <td>{% $.setNumberFormat(iSumLaborAmt) %} 원</td>
            <td>{% $.setNumberFormat(iSumEtcAmt) %} 원</td>
            <td>
                <div v-if="isModify">
                    <input type="text" v-model="oUpsertInfo.dollerRatio" @keyup="gfnChangeDollerRatioDt(oUpsertInfo)" class="form-control" placeholder="환율">
                </div>
                <div v-else>
                    <div v-if="!$.isEmpty(oUpsertInfo.dollerRatio)">{% $.setNumberFormat(oUpsertInfo.dollerRatio) %}</div>
                    <div v-else class="text-muted">미입력</div>
                </div>
            </td>
            <td>
                <div v-if="isModify">
                    <date-picker v-model="oUpsertInfo.dollerRatioDt" value-type="format" format="YYYY-MM-DD"  :editable="false" style="margin-left: -30px;"></date-picker>
                </div>
                <div v-else>
                    <span :class="String(oUpsertInfo.dollerRatioDt).substring(0,5)=='0000-'?'text-danger':''">{% oUpsertInfo.dollerRatioDt %}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="myTable">
    <?php $sMaterialTargetNm = 'oUpsertInfo'; ?>
    <?php include './admin/ims/template/view/materialModule.php'?>
</div>
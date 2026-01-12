
<div class="table-title gd-help-manual mgt5">
    <div class="flo-left area-title">
        <span class="godo"># 샘플 사진 이미지</span>
    </div>
</div>
<div>
    <table class="table table-cols table-default-center table-pd-5 table-td-height0 table-th-height0">
        <colgroup>
            <col class="w-33p">
            <col class="w-33p">
            <col class="w-33p">
        </colgroup>
        <tr>
            <th>앞면</th>
            <th>뒷면</th>
            <th>디테일</th>
        </tr>
        <tr>
            <td>
                <img v-if="checkImageExtension(val.fileName) && key == 0" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in oUpsertInfo.fileList['sampleFile11'].files" style="width:100%;" />
                <div v-show="!isModify">
                    <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile11" id="sampleFile11" :params="oUpsertInfo" :accept="false"></simple-file-not-history-upload>
                </div>
            </td>
            <td>
                <img v-if="checkImageExtension(val.fileName) && key == 0" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in oUpsertInfo.fileList['sampleFile12'].files" style="width:100%;" />
                <div v-show="!isModify">
                    <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile12" id="sampleFile12" :params="oUpsertInfo" :accept="false"></simple-file-not-history-upload>
                </div>
            </td>
            <td>

                <ul v-if="oUpsertInfo.fileList['sampleFile13'].files != undefined && oUpsertInfo.fileList['sampleFile13'].files.length > 0" class="sample_review_image_detail">
                    <li v-for="(val, key) in [1,2,3,4]" v-if="oUpsertInfo.fileList['sampleFile13'].files[key] != undefined && checkImageExtension(oUpsertInfo.fileList['sampleFile13'].files[key].fileName)" >
                        <img :src="'<?=$nasUrl?>'+oUpsertInfo.fileList['sampleFile13'].files[key].filePath" style="width:100%;" />
                    </li>
                </ul>
                <div v-show="!isModify">
                    <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile13" id="sampleFile13" :params="oUpsertInfo" :accept="false"></simple-file-not-history-upload>
                </div>
            </td>
        </tr>
    </table>
</div>
<div v-if="oUpsertInfo.jsonFitSpec != null && oUpsertInfo.jsonFitSpec != ''" v-show="oUpsertInfo.jsonFitSpec.length > 0" class="table-title gd-help-manual">
    <div class="flo-left pdt5 font-16"># 사이즈스펙 피팅 체크</div>
</div>
<div v-if="oUpsertInfo.jsonFitSpec != null && oUpsertInfo.jsonFitSpec != ''" v-show="oUpsertInfo.jsonFitSpec.length > 0">
    <table class="table table-cols table-default-center table-pd-5 table-th-height30 table-td-height30">
        <colgroup>
            <col class="w-13p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="w-8p" />
            <col class="" />
        </colgroup>
        <tbody>
        <tr>
            <th>사이즈</th>
            <td colspan="7" style="text-align: left;">{% oUpsertInfo.fitSize %}</td>
        </tr>
        <tr>
            <th>구분 / 스펙</th>
            <th>단위</th>
            <th>평균</th>
            <th>지시</th>
            <th>제작 샘플</th>
            <th>차이</th>
            <th>피팅 검토</th>
            <th>피팅 체크 의견</th>
        </tr>
        <tr v-for="(val, key) in oUpsertInfo.jsonFitSpec" @focusin="sFocusTable='fitSpec'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='fitSpec' && iFocusIdx==key ? 'focused' : ''">
            <th>{% val.optionName %}</th>
            <td>{% val.optionUnit %}</td>
            <td>{% val.optionValue %}</td>
            <td>{% oUpsertInfo.jsonFixedSpec[key].optionValue %}</td>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="oUpsertInfo.jsonReviewSpec[key].madeValue" ref="inputMadeValue" @keyup="gfnMoveInputBox(oUpsertInfo.jsonReviewSpec, key, event.key, $refs.inputMadeValue)"style="width:80px;" />
                </span>
                <span v-show="!isModify">{% oUpsertInfo.jsonReviewSpec[key].madeValue %}</span>
            </td>
            <td class="font-bold text-danger">{% Number(oUpsertInfo.jsonReviewSpec[key].madeValue) - Number(oUpsertInfo.jsonFixedSpec[key].optionValue) %}</td>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="oUpsertInfo.jsonReviewSpec[key].checkValue" ref="inputReviewValue" @keyup="gfnMoveInputBox(oUpsertInfo.jsonReviewSpec, key, event.key, $refs.inputReviewValue)"style="width:80px;" />
                </span>
                <span v-show="!isModify">{% oUpsertInfo.jsonReviewSpec[key].checkValue %}</span>
            </td>
            <td class="ta-l">
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="oUpsertInfo.jsonReviewSpec[key].specDesc" ref="inputDesc" @keyup="gfnMoveInputBox(oUpsertInfo.jsonReviewSpec, key, event.key, $refs.inputDesc)" />
                </span>
                <span v-show="!isModify">{% oUpsertInfo.jsonReviewSpec[key].specDesc %}</span>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-title gd-help-manual">
    <div class="flo-left pdt5 font-16">
        # 피팅체크
        <select v-show="isModify" @change="chooseFittingCheck($event.target.value)" class="form-control" style="display: inline-block;">
            <option value="">양식 선택</option>
            <option v-for="(val, key) in aoFittingCheckList" :value="key">{% val.fittingCheckName %}</option>
        </select>
        <span class="btn btn-red-line" v-show="isModify" @click="clearFittingCheckList()">직접</span>
    </div>
</div>
<div>
    <table v-if="oUpsertInfo.jsonReviewCheck != undefined" class="table table-cols table-default-center table-pd-5 table-th-height30 table-td-height30">
        <colgroup>
            <col class="w-20p" />
            <col class="w-20p" />
            <col class="w-10p" />
            <col class="" />
            <col v-if="isModify" class="w-10p" />
        </colgroup>
        <tr>
            <th>구분</th>
            <th>체크 사항</th>
            <th>체크</th>
            <th>이슈 사항</th>
            <th v-if="isModify">기능</th>
        </tr>
        <tbody>
        <tr v-if="oUpsertInfo.jsonReviewCheck.length == 0">
            <td :colspan="isModify ? 5 : 4">상단의 피팅체크양식을 선택하세요</td>
        </tr>
        <tr v-else v-for="(val, key) in oUpsertInfo.jsonReviewCheck" @focusin="sFocusTable='reviewCheck'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='reviewCheck' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="val.cntType > 0" :rowspan="val.cntType">
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="val.checkType" @keydown="startChgCheckTypeName(event.target.value);" @keyup="endChgCheckTypeName(event.target.value)" />
                    <button type="button" class="btn btn-white btn-sm" @click="appendFittingCheckByType(val.checkType);" style="margin-top:5px;">+ 구분추가</button>
                </span>
                <span v-show="!isModify">{% val.checkType %}</span>
            </td>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="val.checkName" ref="inputReviewCheckName" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReviewCheckName)" />
                </span>
                <span v-show="!isModify">{% val.checkName %}</span>
            </td>
            <td>
                <span v-show="isModify">
                    <input class="form-control" type="checkbox" v-model="val.checkYn" :checked="val.checkYn=='true'" value="true" />
                </span>
                <span v-show="!isModify"><i :class="val.checkYn == 'true' ? 'fa fa-lg fa-check-circle text-green' : ''" aria-hidden="true"></i></span>
            </td>
            <td class="ta-l">
                <span v-show="isModify">
                    <input class="form-control" type="text" v-model="val.checkDesc" ref="inputReviewCheckDesc" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReviewCheckDesc)" />
                </span>
                <span v-show="!isModify">{% val.checkDesc %}</span>
            </td>
            <td v-if="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="appendFittingCheck(key);">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteFittingCheck(key);" >- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-title gd-help-manual">
    <div class="flo-left pdt5 font-16">
        # 샘플실 의견 & 생산 유의사항
        <button type="button" v-if="isModify" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonReviewComment, ooDefaultJson.jsonReviewComment, 'after')">+ 추가</button></div>
</div>

<div>
    <table v-if="oUpsertInfo.jsonReviewComment != undefined" class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30">
        <colgroup>
            <col v-if="isModify" class="w-10p" />
            <col class="w-15p" />
            <col class="w-10p" />
            <col class="w-25p" />
            <col class="" />
            <col v-if="isModify" class="w-10p" />
        </colgroup>
        <tr>
            <th v-if="isModify">이동</th>
            <th>업체</th>
            <th>구분</th>
            <th>생산 난이도 / 공정 문제</th>
            <th>대책 방안</th>
            <th v-if="isModify">기능</th>
        </tr>
        <tbody is="draggable" :list="oUpsertInfo.jsonReviewComment" :animation="200" tag="tbody" handle=".handle">
        <tr v-if="oUpsertInfo.jsonReviewComment.length == 0">
            <td :colspan="isModify ? 6 : 4">입력한 의견 및 유의사항이 없습니다. <span v-show="isModify">상단의 +추가 버튼을 클릭해 주세요</span></td>
        </tr>
        <tr v-else v-for="(val, key) in oUpsertInfo.jsonReviewComment" @focusin="sFocusTable='jsonReviewComment'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonReviewComment' && iFocusIdx==key ? 'focused' : ''">
            <td :class="isModify ? 'handle' : ''" v-if="isModify">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <?php $model="val.commentCompany"; $placeholder='업체'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <span v-show="isModify">
                    <select v-model="val.commentType" class="form-control" style="width: 100%;">
                        <option value="">선택</option>
                        <option value="봉제 퀄리티">봉제 퀄리티</option>
                        <option value="원단">원단</option>
                    </select>
                </span>
                <span v-show="!isModify">
                    <span v-if="val.commentType == ''" class="text-muted">미선택</span>
                    <span v-else>{% val.commentType %}</span>
                </span>
            </td>
            <td class="ta-l">
                <?php $model="val.commentDiff"; $placeholder='생산 난이도 / 공정 문제'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td class="ta-l">
                <?php $model="val.commentMethod"; $placeholder='대책 방안'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td v-if="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="addElement(oUpsertInfo.jsonReviewComment, ooDefaultJson.jsonReviewComment, 'down', key)">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteElement(oUpsertInfo.jsonReviewComment, key)" >- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<div class="mgt30">&nbsp;</div>
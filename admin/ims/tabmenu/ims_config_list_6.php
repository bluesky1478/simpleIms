<div class="">
    <div>
        <div class="table-title ">
            검색
        </div>
        <!--검색 시작-->
        <div class="search-detail-box form-inline">
            <table class="table table-cols table-td-height0">
                <colgroup>
                    <col class="width-sm">
                    <col class="width-3xl">
                    <col class="width-sm">
                    <col class="width-3xl">
                </colgroup>
                <tbody>
                <tr>
                    <th colspan="1">검색어</th>
                    <td colspan="3">
                        <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                            검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                            <input type="text" name="keyword" v-show="keyCondition.key!='a.fitStyle'" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
                            <select class="form-control" v-show="keyCondition.key=='a.fitStyle'" v-model="keyCondition.keyword">
                                <option value="">선택</option>
                                <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                    <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                <?php } ?>
                            </select>
                            <div class="btn btn-sm btn-red" @click="addMultiKey" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
                            <div class="btn btn-sm btn-gray" @click="searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="searchCondition.multiKey.length > 1 ">-제거</div>
                        </div>
                        <div class="mgb5">
                            다중 검색 :
                            <select class="form-control" v-model="searchCondition.multiCondition">
                                <option value="AND">AND (그리고)</option>
                                <option value="OR">OR (또는)</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="ta-c" style="border-bottom: none">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshList(1)">
                        <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="conditionReset()">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!--검색 끝-->
    </div>

    <div class="">
        <div class="flo-left mgb5">
            <div class="font-16 dp-flex" >
                <span style="font-size: 18px !important;">
                    TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                </span>
            </div>
        </div>
        <div class="flo-right mgb5">
            <div class="" style="display: flex; ">
                <input type="button" class="btn btn-red btn-reg hover-btn" value="양식 등록" @click="isModify = true; openUpsertModal(0);" />
                <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                    <option value="D,asc">피팅체크양식 등록일시 ▲</option>
                    <option value="D,desc">피팅체크양식 등록일시 ▼</option>
                </select>
                <select @change="refreshList(1)" v-model="searchCondition.pageNum" class="form-control mgl5">
                    <option value="5">5개 보기</option>
                    <option value="20">20개 보기</option>
                    <option value="50">50개 보기</option>
                    <option value="100">100개 보기</option>
                </select>
            </div>
        </div>
    </div>
    <!--list start-->
    <div>
        <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
            <colgroup>
                <col class="w-5p" />
                <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip" />
                <col class="w-10p" />
            </colgroup>
            <tr>
                <th >번호</th>
                <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                    {% fieldData.title %}
                </th>
                <th >기능</th>
            </tr>
            <tr  v-if="0 >= listData.length">
                <td colspan="99">
                    데이터가 없습니다.
                </td>
            </tr>
            <tr v-for="(val , key) in listData">
                <td >{% (listTotal.idx - key) %}</td>
                <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                    <span v-if="fieldData.type === 'title'" class="sl-blue  cursor-pointer hover-btn" @click="isModify = false; openUpsertModal(val.sno);">
                        {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                    </span>
                    <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                    <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                    <span v-else-if="fieldData.name === 'fitSeasonHan'">
                        <span v-if="val.fitSeason==''">공통</span>
                        <span v-else>({% val.fitSeason %}) {% val[fieldData.name] %}</span>
                    </span>
                    <span v-else-if="fieldData.name === 'fitStyleHan'">
                        <span v-if="val.fitStyle==''">공통</span>
                        <span v-else>{% val[fieldData.name] %}</span>
                    </span>
                    <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                </td>
                <td>
                    <span class="btn btn-sm btn-blue" @click="openCopyModal(val.sno, val.fittingCheckName)" >복사</span>
                    <span class="btn btn-sm btn-white" @click="isModify = true; openUpsertModal(val.sno);" >수정</span>
                    <span class="btn btn-sm btn-red" @click="deleteRow(val.sno);" >삭제</span>
                </td>
            </tr>
        </table>
    </div>
    <!--list end-->
    <div id="fitting_check-page" v-html="pageHtml" class="ta-c"></div>
</div>

<div class="modal fade" id="modalUpsert" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:700px;">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    피팅체크양식 {% oUpsertForm.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}
                </span>
            </div>
            <div class="modal-body">
                <table class="table table-cols table-pd-5">
                    <colgroup>
                        <col class="w-20p">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>시즌</th>
                        <td>
                            <div v-show="isModify">
                                <select v-model="oUpsertForm.fitSeason" class="form-control" style="width:100%;">
                                    <option value="">공통</option>
                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div v-show="!isModify" >
                                <span v-if="oUpsertForm.fitSeason==''">공통</span>
                                <span v-else>({% oUpsertForm.fitSeason %}) {% oUpsertForm.fitSeasonHan %}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>스타일</th>
                        <td>
                            <div v-show="isModify">
                                <select v-model="oUpsertForm.fitStyle" class="form-control" style="width:100%;">
                                    <option value="">공통</option>
                                    <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div v-show="!isModify" >
                                <span v-if="oUpsertForm.fitStyle==''">공통</span>
                                <span v-else>{% oUpsertForm.fitStyleHan %}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>양식명</th>
                        <td>
                            <?php $model='oUpsertForm.fittingCheckName'; $placeholder='양식명' ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>항목리스트 <button type="button" v-if="isModify" class="btn btn-white btn-sm" @click="addElement(oUpsertForm.jsonOptions, ooDefaultJson.jsonBasicFittingCheck, 'after')">+ 추가</button></th>
                        <td>
                            <table v-if="oUpsertForm.jsonOptions != undefined" class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30">
                                <colgroup>
                                    <col v-if="isModify" class="w-10p" />
                                    <col class="w-30p" />
                                    <col class="" />
                                    <col v-if="isModify" class="w-20p" />
                                </colgroup>
                                <tr>
                                    <th v-if="isModify">이동</th>
                                    <th>구분</th>
                                    <th>체크사항</th>
                                    <th v-if="isModify">기능</th>
                                </tr>
                                <tbody is="draggable" :list="oUpsertForm.jsonOptions" :animation="200" tag="tbody" handle=".handle">
                                <tr v-if="oUpsertForm.jsonOptions.length == 0">
                                    <td :colspan="isModify ? 4 : 2">입력한 체크사항이 없습니다. <span v-show="isModify">좌측의 +추가 버튼을 클릭해 주세요</span></td>
                                </tr>
                                <tr v-else v-for="(val, key) in oUpsertForm.jsonOptions" @focusin="sFocusTable='jsonOptions'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonOptions' && iFocusIdx==key ? 'focused' : ''">
                                    <td :class="isModify ? 'handle' : ''" v-if="isModify">
                                        <div class="cursor-pointer hover-btn" >
                                            <i class="fa fa-bars" aria-hidden="true"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span v-show="isModify">
                                            <input class="form-control" type="text" v-model="val.checkType" ref="inputReviewCheckType" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReviewCheckType)" placeholder="구분명" />
                                        </span>
                                        <span v-show="!isModify">{% val.checkType %}</span>
                                    </td>
                                    <td class="ta-l">
                                        <span v-show="isModify">
                                            <input class="form-control" type="text" v-model="val.checkName" ref="inputReviewCheckName" @keyup="gfnMoveInputBox(val, key, event.key, $refs.inputReviewCheckName)" placeholder="체크사항명" />
                                        </span>
                                        <span v-show="!isModify">{% val.checkName %}</span>
                                    </td>
                                    <td v-if="isModify">
                                        <button type="button" class="btn btn-white btn-sm" @click="addElement(oUpsertForm.jsonOptions, ooDefaultJson.jsonBasicFittingCheck, 'down', key)">+ 추가</button>
                                        <div class="btn btn-sm btn-red" @click="deleteElement(oUpsertForm.jsonOptions, key)" >- 삭제</div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer ">
                <div class="btn btn-accept hover-btn btn-lg mg5" v-show="!isModify" @click="isModify=true">수정하기</div>
                <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="save()">저장</div>
                <div class="btn btn-white hover-btn btn-lg mg5" v-show="isModify && oUpsertForm.sno != 0" @click="isModify=false">수정취소</div>
                <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCopy" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:700px;">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    피팅체크양식 복사
                </span>
            </div>
            <div class="modal-body">
                <table>
                    <colgroup>
                        <col class="w-30p"/>
                        <col class=""/>
                    </colgroup>
                    <tr>
                        <th>양식명</th>
                        <td><input type="text" v-model="sCopyName" class="form-control" /></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer ">
                <div class="btn btn-accept hover-btn btn-lg mg5" @click="copy()">복사하기</div>
                <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
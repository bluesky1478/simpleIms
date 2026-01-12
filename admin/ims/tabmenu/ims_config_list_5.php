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
                            <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
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

    <table class="w-100p">
        <colgroup>
            <col class="w-50p" />
            <col class="w-50p" />
        </colgroup>
        <tr>
            <td style="padding:0px 10px; vertical-align: top;">
                <div class="">
                    <div class="flo-left mgb5">
                        <div class="font-16 dp-flex" >
                            <span style="font-size: 18px !important;">
                                상위업종 TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(Object.keys(oParentCateList).length-1) %}</span> 건
                            </span>
                        </div>
                    </div>
                    <div class="flo-right mgb5">
                        <div class="" style="display: flex; ">
                            <input type="button" class="btn btn-red btn-reg hover-btn" value="업종 등록" @click="openUpsertModal(0);" />
                        </div>
                    </div>
                </div>
                <!--list start-->
                <div>
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-5p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip" />
                        </colgroup>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                        </tr>
                        <tr  v-if="0 >= listData.length">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tr v-if="val.parentBusiCateSno == 0" v-for="(val , key) in listData">
                            <td >{% (listTotal.idx - key) %}</td>
                            <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                                <span v-if="fieldData.type === 'parent_cate_name'">
                                    <span v-if="val.parentBusiCateSno == 0" class="sl-blue cursor-pointer hover-btn" @click="openUpsertModal(val.sno);">
                                        {% val.cateName %}
                                    </span>
                                    <span v-else>{% val[fieldData.name] %}</span>
                                </span>
                                <span v-else-if="fieldData.type === 'cate_name'">
                                    <span v-if="val.parentBusiCateSno == 0" class="sl-blue cursor-pointer hover-btn" @click="openUpsertModal(0, val.sno);">
                                        <span @click="" class="btn btn-sm btn-white">세부업종 등록</span>
                                    </span>
                                    <span v-else class="sl-blue cursor-pointer hover-btn" @click="openUpsertModal(val.sno);">{% val[fieldData.name] %}</span>
                                </span>
                                <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                                <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--list end-->
            </td>
            <td style="padding:0px 10px; vertical-align: top;">
                <div class="">
                    <div class="flo-left mgb5">
                        <div class="font-16 dp-flex" >
                            <span style="font-size: 18px !important;">
                                세부업종 TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total-Object.keys(oParentCateList).length+1) %}</span> 건
                            </span>
                        </div>
                    </div>
                    <div class="flo-right mgb5"></div>
                </div>
                <!--list start-->
                <div>
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-5p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip" />
                        </colgroup>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                        </tr>
                        <tr  v-if="0 >= listData.length">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tr v-if="val.parentBusiCateSno != 0"  v-for="(val , key) in listData">
                            <td >{% (listTotal.idx - key) %}</td>
                            <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                                <span v-if="fieldData.type === 'parent_cate_name'">
                                    <span v-if="val.parentBusiCateSno == 0" class="sl-blue cursor-pointer hover-btn" @click="openUpsertModal(val.sno);">
                                        {% val.cateName %}
                                    </span>
                                    <span v-else>{% val[fieldData.name] %}</span>
                                </span>
                                <span v-else-if="fieldData.type === 'cate_name'">
                                    <span v-if="val.parentBusiCateSno == 0" class="sl-blue cursor-pointer hover-btn" @click="openUpsertModal(0, val.sno);">
                                        <span @click="" class="btn btn-sm btn-white">세부업종 등록</span>
                                    </span>
                                    <span v-else class="sl-blue cursor-pointer hover-btn" @click="openUpsertModal(val.sno);">{% val[fieldData.name] %}</span>
                                </span>
                                <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                                <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--list end-->
            </td>
        </tr>
    </table>
</div>

<div class="modal fade" id="modalUpsert" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:500px;">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    업종 {% oUpsertForm.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}
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
                        <th>업종구분</th>
                        <td>
                            <div v-show="isModify">
                                <label class="radio-inline">
                                    <input type="radio" v-model="oUpsertForm.busiCateType" @click="oUpsertForm.parentBusiCateSno=0;" name="sRadioCateType" value="상위업종" />상위업종
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" v-model="oUpsertForm.busiCateType" name="sRadioCateType" value="세부업종" />세부업종
                                </label>
                            </div>
                            <div v-show="!isModify" >
                                {% oUpsertForm.busiCateType %}
                            </div>
                        </td>
                    </tr>
                    <tr v-show="oUpsertForm.busiCateType=='세부업종'">
                        <th>상위업종</th>
                        <td>
                            <div v-show="isModify">
                                <select2 class="js-example-basic-single" v-model="oUpsertForm.parentBusiCateSno" style="width:100%;">
                                    <option v-for="(val, key) in oParentCateList" :value="key">{% val %}</option>
                                </select2>
                            </div>
                            <div v-show="!isModify" >
                                {% oUpsertForm.parentCateName %}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>업종명</th>
                        <td>
                            <?php $model='oUpsertForm.cateName'; $placeholder='업종명' ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>업종설명</th>
                        <td>
                            <div v-show="isModify">
                                <textarea class="form-control" rows="3" v-model="oUpsertForm.cateDesc" placeholder="업종설명"></textarea>
                            </div>
                            <div v-show="!isModify" >
                                <div v-if="!$.isEmpty(oUpsertForm.cateDesc)" v-html="oUpsertForm.cateDesc.replaceAll('\n','<br/>')"></div>
                                <div v-else class="text-muted">미입력</div>
                            </div>
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

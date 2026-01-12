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
                    <th >검색어</th>
                    <td >
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
                    <th>유형</th>
                    <td>
                        <div v-if="searchCondition.aChkboxSchCostType !== undefined" class="checkbox">
                            <div>
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="aChkboxSchCostType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchCostType[]"  :checked="0 >= searchCondition.aChkboxSchCostType.length?'checked':''" @click="searchCondition.aChkboxSchCostType=[]"> 전체
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchCostType[]" value="1"  v-model="searchCondition.aChkboxSchCostType"> 공임비용
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchCostType[]" value="2"  v-model="searchCondition.aChkboxSchCostType"> 기타비용
                                </label>
                            </div>
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
                <input type="button" class="btn btn-red btn-reg hover-btn" value="항목 등록" @click="isModify = true; openUpsertModal(0);" />
                <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                    <option value="D,asc">등록일시 ▲</option>
                    <option value="D,desc">등록일시 ▼</option>
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
                <col class="w-3p" />
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
            <tr v-for="(val , key) in listData">
                <td >{% (listTotal.idx - key) %}</td>
                <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                    <span v-if="fieldData.type === 'title'" class="sl-blue cursor-pointer hover-btn" @click="isModify = false; openUpsertModal(val.sno);">
                        {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                    </span>
                    <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                    <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                    <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                </td>
            </tr>
        </table>
    </div>
    <!--list end-->
    <div id="sample_etc_cost-page" v-html="pageHtml" class="ta-c"></div>
</div>

<div class="modal fade" id="modalUpsert" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:500px;">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    비용 {% oUpsertForm.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}
                </span>
            </div>
            <div class="modal-body">
                <table class="table table-cols table-pd-5" >
                    <colgroup>
                        <col class="w-20p">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>유형</th>
                        <td>
                            <div v-show="isModify">
                                <select2 class="js-example-basic-single" v-model="oUpsertForm.costType" style="width:100%;">
                                    <?php foreach (\Component\Ims\NkCodeMap::SAMPLE_ETC_COST_TYPE as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                            <div v-show="!isModify" >
                                {% oUpsertForm.costTypeHan %}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>코드</th>
                        <td>
                            <?php $model='oUpsertForm.costCode'; $placeholder='코드' ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>구분명</th>
                        <td>
                            <?php $model='oUpsertForm.costName'; $placeholder='구분명' ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>기본단가</th>
                        <td>
                            <?php $model='oUpsertForm.costUnitPrice'; $placeholder='단가'; $suffixText='원'; ?>
                            <?php include './admin/ims/template/basic_view/_number.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>내용</th>
                        <td>
                            <?php $model='oUpsertForm.costDesc'; $placeholder='내용' ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
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

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
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="bFlagRunSearch = true; refreshList(1)">
                        <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="bFlagRunSearch = false; conditionReset()">
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
                    TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listData.length) %}</span> 건
                </span>
                <span v-show="isModify" class="text-danger pdl5">검색 후 저장하시면 순서가 변경되지 않습니다.</span>
            </div>
        </div>
        <div class="flo-right mgb5">
            <div class="" style="display: flex; ">
                <span v-show="!isModify" @click="isModify = true;" class="btn btn-red" style="margin-left:10px;">관리</span>
                <span v-show="isModify" @click="addElement(listData, {'sno':'', 'guideName':'', 'guideFileUrl':''}, 'after')" class="btn btn-red-line" style="margin-left:10px;">추가</span>
                <span v-show="isModify" @click="save()" class="btn btn-red" style="margin-left:10px;">저장</span>
                <!--<span v-show="isModify" @click="isModify = false;" class="btn btn-white" style="margin-left:10px;">저장취소</span>-->
            </div>
        </div>
    </div>
    <!--list start-->
    <div>
        <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
            <colgroup>
                <col v-if="isModify" class="w-50px" />
                <col class="w-5p" />
                <col class="w-200px" />
                <col class="w-200px" />
                <col class="w-200px" />
                <col class="" />
                <col class="w-100px" />
            </colgroup>
            <tr>
                <th v-if="isModify" >이동</th>
                <th >번호</th>
                <th >이미지</th>
                <th >양식명</th>
                <th >이미지파일 관리</th>
                <th >설명</th>
                <th >삭제</th>
            </tr>
            <tr  v-if="0 >= listData.length">
                <td colspan="99">
                    데이터가 없습니다.
                </td>
            </tr>
            <tbody is="draggable" :list="listData"  :animation="200" tag="tbody" handle=".handle">
            <tr v-for="(val , key) in listData">
                <td v-if="isModify" class="handle">
                    <div class="cursor-pointer hover-btn">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </div>
                </td>
                <td >{% key+1 %}</td>
                <td>
                    <img v-if="val.guideFileUrl != ''" :src="val.guideFileUrl"
                         @click="if (val.guideFileUrl == '') { $.msg('파일을 올려주세요','','warning'); } else { $refs.textProposalGuide.innerHTML=val.guideName; $refs.imageProposalGuide.src=val.guideFileUrl; $('#modalGuideImage').modal('show'); }"
                         class="hover-btn cursor-pointer" style="max-height:100px;" />
                </td>
                <td class="ta-l">
                    <span v-show="isModify">
                        <input type="text" v-model="val.guideName" class="form-control" />
                    </span>
                    <span v-show="!isModify">{% val.guideName %}</span>
                </td>
                <td>
                    <span v-show="isModify">
                        <input type="file" ref="fileGuideImage" @change="uploadProposalGuideFile(key);" />
                        <input type="button" class="btn btn-blue" value="파일찾기" @click="$refs.fileGuideImage[key].click();" />
                    </span>
                    <span @click="if (val.guideFileUrl == '') { $.msg('파일을 올려주세요','','warning'); } else { $refs.textProposalGuide.innerHTML=val.guideName; $refs.imageProposalGuide.src=val.guideFileUrl; $('#modalGuideImage').modal('show'); }" class="btn btn-white">전체사이즈</span>
                </td>
                <td class="ta-l">
                    <span v-show="isModify">
                        <textarea class="form-control" rows="3" v-model="val.guideDesc" placeholder="설명"></textarea>
                    </span>
                    <span v-show="!isModify" v-html="$.nl2br(val.guideDesc)"></span>
                </td>
                <td>
                    <span @click="deleteRow(val.sno, key)" class="btn btn-red-box">삭제</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modalGuideImage" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:1000px; top:0px;">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span ref="textProposalGuide" class="modal-title font-18 bold" ></span>
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
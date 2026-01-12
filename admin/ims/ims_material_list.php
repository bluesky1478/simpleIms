<?php
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;

include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<?php if ($sExcelUploadResultHtml !== '') { ?>
<section class="project-view">
    <div class="page-header js-affix">
        <h3>자재 엑셀 업로드 결과</h3>
    </div>
    <?=$sExcelUploadResultHtml?>
    <br/><br/><a href="ims_material_list.php" class="btn btn-blue hover-btn">자재관리 페이지로 이동</a>
</section>
<?php } else { ?>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>자재 리스트</h3>
        <div class="btn-group">
            <input type="button" value="일괄 등록" class="btn btn-red-line" @click="isExcelUpload==true?isExcelUpload=false:isExcelUpload=true;" />
            <input type="button" class="btn btn-red" value="원단 등록" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':0, 'type':1});" />
            <input type="button" class="btn btn-red" value="충전재 등록" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':0, 'type':2});" />
            <input type="button" class="btn btn-red" value="부자재 등록" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':0, 'type':3});" />
            <input type="button" class="btn btn-red" value="마크 등록" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':0, 'type':4});" />
            <input type="button" class="btn btn-red" value="기능 등록" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':0, 'type':5});" />
            <input type="button" class="btn btn-red" value="공임/기타페이지" @click="location.href='ims_config_list.php?tabNum=3';" />
        </div>
    </div>
    <div class="row" >
        <div class="col-xs-12" >
            <div v-show="isExcelUpload">
                <div class="table-title excel-upload-goods-info">
                    자재 정보 일괄등록
                </div>
                <div class="excel-upload-goods-info">
                    <form id="frmRegistMaterialInfo" name="frmRegistMaterialInfo" action="./ims_material_list.php" method="post" enctype="multipart/form-data" @submit.prevent="listUpload();">
                        <table class="table table-cols">
                            <colgroup>
                                <col class="width20p"/>
                                <col class="width-xl"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>자재 정보 업로드</th>
                                <td>
                                    <div class="form-inline">
                                        <select class="form-control" name="update_type" v-model="excelUploadType">
                                            <option value="1">원단</option>
                                            <option value="2">충전재</option>
                                            <option value="3">부자재</option>
                                            <option value="4">마크</option>
                                        </select>
                                        <input type="file" name="excel" value="" class="form-control width50p" />
                                        <input type="submit" class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                                        <button v-if="excelUploadType==1" type="button" class="btn btn-white btn-icon-excel simple-download" @click="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/ims_material_type_1.xlsx')?>&fileName=<?=urlencode('자재관리양식_원단.xls')?>'">원단 등록양식 다운로드</button>
                                        <button v-else type="button" class="btn btn-white btn-icon-excel simple-download" @click="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/ims_material_type_other.xlsx')?>&fileName=<?=urlencode('자재관리양식_원단외.xls')?>'">원단외 등록양식 다운로드</button>
                                    </div>
                                    <div>
                                        <span class="notice-info">엑셀 파일은 반드시 &quot;Excel 97-2003 통합문서&quot;만 가능하며, csv 파일은 업로드가 되지 않습니다.</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
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
                            <th rowspan="2">검색어</th>
                            <td rowspan="2">
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
                            <th>타입</th>
                            <td>
                                <div v-if="searchCondition.aChkboxSchMaterialType !== undefined" class="checkbox">
                                    <div>
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="aChkboxSchMaterialType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchMaterialType[]"  :checked="0 >= searchCondition.aChkboxSchMaterialType.length?'checked':''" @click="searchCondition.aChkboxSchMaterialType=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_TYPE as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMaterialType[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchMaterialType"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>생산국</th>
                            <td>
                                <div v-if="searchCondition.aChkboxSchMakeNational !== undefined" class="checkbox">
                                    <div>
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="aChkboxSchMakeNational[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchMakeNational[]"  :checked="0 >= searchCondition.aChkboxSchMakeNational.length?'checked':''" @click="searchCondition.aChkboxSchMakeNational=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PRD_NATIONAL_CODE as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMakeNational[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchMakeNational"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>사용 스타일</th>
                            <td>
                                <div v-if="searchCondition.aChkboxSumSchUsedStyle !== undefined" class="checkbox">
                                    <div>
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="aChkboxSumSchUsedStyle[]" value="all" class="js-not-checkall" data-target-name="aChkboxSumSchUsedStyle[]"  :checked="0 >= searchCondition.aChkboxSumSchUsedStyle.length?'checked':''" @click="searchCondition.aChkboxSumSchUsedStyle=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_USED_STYLE as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSumSchUsedStyle[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSumSchUsedStyle"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                            <th>생지 보유</th>
                            <td>
                                <div v-if="searchCondition.aChkboxSchOnHandYn !== undefined" class="checkbox">
                                    <div>
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="aChkboxSchOnHandYn[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchOnHandYn[]"  :checked="0 >= searchCondition.aChkboxSchOnHandYn.length?'checked':''" @click="searchCondition.aChkboxSchOnHandYn=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_ON_HAND as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchOnHandYn[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchOnHandYn"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>원단 단가</th>
                            <td>
                                <input type="text" class="form-control" v-model="searchCondition.sTextboxRangeStartSchUnitPrice" placeholder="단가">이상 ~ <input type="text" class="form-control" v-model="searchCondition.sTextboxRangeEndSchUnitPrice" placeholder="단가">이하
                            </td>
                            <th>BT 준비</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="sRadioSchBtYn" value="all" v-model="searchCondition.sRadioSchBtYn"  />전체
                                </label>
                                <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_BT_YN as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="sRadioSchBtYn" value="<?=$k?>" v-model="searchCondition.sRadioSchBtYn"/><?=$v?>
                                </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <th>시험성적서 등록여부</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" v-model="searchCondition['sExistOrNotSchTest.sno']" value="all" name="sSchRadioTestReportYn" style="display: inline;" />전체
                            </label>
                            <label class="radio-inline">
                                <input type="radio" v-model="searchCondition['sExistOrNotSchTest.sno']" value="exist" name="sSchRadioTestReportYn" style="display: inline;" />등록
                            </label>
                            <label class="radio-inline">
                                <input type="radio" v-model="searchCondition['sExistOrNotSchTest.sno']" value="not" name="sSchRadioTestReportYn" style="display: inline;" />미등록
                            </label>
                        </td>
                        <th>자체테스트 등록여부</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" v-model="searchCondition['sExistOrNotSchTest2.sno']" value="all" name="sSchRadioTestSelfYn" style="display: inline;" />전체
                            </label>
                            <label class="radio-inline">
                                <input type="radio" v-model="searchCondition['sExistOrNotSchTest2.sno']" value="exist" name="sSchRadioTestSelfYn" style="display: inline;" />등록
                            </label>
                            <label class="radio-inline">
                                <input type="radio" v-model="searchCondition['sExistOrNotSchTest2.sno']" value="not" name="sSchRadioTestSelfYn" style="display: inline;" />미등록
                            </label>
                        </td>
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
            <div v-show="bFlagModifyGrp">
                <div class="mgb5">
                    <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            유사퀄리티 {% oGroupInfo.sno == 0 ? '등록' : (bFlagModifyGrp ? '수정' : '정보') %}
                            <input type="text" v-model="oGroupInfo.grpName" class="form-control" style="display:inline; width:200px;" placeholder="유사퀄리티 이름" />
                        </span>
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)">
                        <colgroup>
                            <col class="w-2p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                        </colgroup>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                        </tr>
                        <tr  v-if="0 >= aoGroupItemList.length">
                            <td colspan="99">
                                하단 자재리스트에서 소속시킬 자재를 선택해 주세요.
                            </td>
                        </tr>
                        <tr v-for="(val , key) in aoGroupItemList">
                            <td >{% key + 1 %}</td>
                            <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                <span v-if="fieldData.type === 'group'">
                                    {% val[fieldData.name] %}<br/>
                                    <span @click="deleteElement(aoGroupItemList, key)" class="btn btn-sm btn-white hover-btn cursor-pointer">제외</span>
                                </span>
                                <span v-else>
                                    {% val[fieldData.name] %}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="ta-c">
                    <span @click="saveGroup()" class="btn btn-lg btn-red">저장</span>
                    <span @click="bFlagModifyGrp = false;" class="btn btn-lg btn-white">취소</span>
                </div>
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
                        <input type="button" @click="openUpsertGrpLayer(0);" class="btn btn-blue btn-reg hover-btn" value="유사퀄리티 등록" />&nbsp;
                        <input type="button" @click="openListGrpModal();" class="btn btn-blue-line btn-reg hover-btn" value="유사퀄리티 관리" />&nbsp;

                        <input type="button" class="btn btn-red btn-reg hover-btn" value="품목구분 관리" @click="openCommonPopup('material_type_detail', 540, 910, {});" />&nbsp;
                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(1)">자재 리스트</button>
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
                        <col class="w-2p" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
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
                        <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                            <span v-if="fieldData.type === 'pop_modify'">
                                <span class="sl-blue  cursor-pointer hover-btn" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.sno, 'type':val.materialType});">
                                    {% val[fieldData.name] %}
                                    <div v-if="val.ordererItemNo!='' || val.ordererItemName!=''" class="text-muted">({% $.isEmpty(val.ordererItemNo)?'-':val.ordererItemNo %} / {% $.isEmpty(val.ordererItemName)?'-':val.ordererItemName %})</div>
                                </span>
                            </span>
                            <span v-else-if="fieldData.type === 'pop_modify_log'">
                                <span class="btn btn-sm btn-white hover-btn cursor-pointer" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.sno, 'type':val.materialType});">수정</span>
                                <span class="btn btn-sm btn-white hover-btn cursor-pointer" @click="openCommonPopup('material_update_log', 740, 910, {'sno':val.sno});">이력</span>
                            </span>
                            <span v-else-if="fieldData.type === 'group'">
                                <span :title="val.grpMaterialNames" class="sl-blue">{% val[fieldData.name] %}</span><br/>
                                <span v-show="bFlagModifyGrp" @click="if (aoGroupItemList.length == 0) vueApp.oGroupInfo.grpName = val.name; aoGroupItemList.push(val);" class="btn btn-sm btn-white hover-btn cursor-pointer">추가</span>
                            </span>
                            <span v-else>
                                {% val[fieldData.name] %}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <!--list end-->
            <div id="material-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>



    <div class="modal fade" id="modalListGrp" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:700px;">
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >유사퀄리티(그룹) 리스트</span>
                </div>
                <div class="modal-body">
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(aoGroupFlds)">
                        <colgroup>
                            <col class="w-10p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in aoGroupFlds" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                            <col class="w-10p" />
                        </colgroup>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in aoGroupFlds"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                            <th>수정</th>
                        </tr>
                        <tr  v-if="0 >= aoGroupList.length">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tr v-for="(val , key) in aoGroupList">
                            <td >{% key + 1 %}</td>
                            <td v-for="fieldData in aoGroupFlds"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                            <span v-if="fieldData.type === 'cnt_material'">
                                <span :title="val.materialNames" class="sl-blue">{% val[fieldData.name] %}</span>
                            </span>
                            <span v-else>
                                {% val[fieldData.name] %}
                            </span>
                            </td>
                            <td>
                                <span class="btn btn-sm btn-white hover-btn cursor-pointer" @click="openUpsertGrpLayer(val.sno)">수정</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer ">
                    <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>




    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_material_list_script.php'?>
<?php } ?>
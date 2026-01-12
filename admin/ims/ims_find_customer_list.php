<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<style>
    .mx-datepicker { width:100px!important; }
    .memo_layer { display:none; position:absolute; top:0px; right:60px; overflow:hidden; height:auto; z-index:999; width:650px; padding:10px; border:1px solid #cccccc; background:#ffffff; }
    .mgb4 { margin-bottom:4px!important; }
</style>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>신규고객 발굴</h3>
        <div class="btn-group">
            <input type="button" @click="openUpsertModal(0);" value="업체 등록" class="btn btn-red-line" />
            <input type="button" @click="registProject()" class="btn btn-red btn-reg hover-btn" value="프로젝트 등록" />
            <input type="button" @click="openCommonPopup('sales_customer_stats', 1060, 750, {});" class="btn btn-blue" value="영업 현황" />
        </div>
    </div>
    <div class="row" >
        <div class="col-xs-12" >
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0 table-th-height0">
                        <colgroup>
                            <col class="w-7p">
                            <col class="w-34p">
                            <col class="w-12p">
                            <col class="w-44p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="3">검색어</th>
                            <td rowspan="3">
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
                            <th>고객 구분</th>
                            <td >
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="aChkboxSchCustomerType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchCustomerType[]"  :checked="0 >= searchCondition.aChkboxSchCustomerType.length?'checked':''" @click="searchCondition.aChkboxSchCustomerType=[]"> 전체
                                </label>
                                <?php foreach( \Component\Ims\NkCodeMap::SALES_CUST_TYPE as $k => $v){ ?>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchCustomerType[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchCustomerType"> <?=$v?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th>유니폼 종류</th>
                            <td>
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="aChkboxSchBuyDiv[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchBuyDiv[]"  :checked="0 >= searchCondition.aChkboxSchBuyDiv.length?'checked':''" @click="searchCondition.aChkboxSchBuyDiv=[]"> 전체
                                </label>
                                <?php foreach( \Component\Ims\NkCodeMap::SALES_CUST_BUY_DIV as $k => $v){ ?>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchBuyDiv[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchBuyDiv"> <?=$v?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th>구매 방식</th>
                            <td >
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="aChkboxSchBuyMethod[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchBuyMethod[]"  :checked="0 >= searchCondition.aChkboxSchBuyMethod.length?'checked':''" @click="searchCondition.aChkboxSchBuyMethod=[]"> 전체
                                </label>
                                <?php foreach( \Component\Ims\NkCodeMap::SALES_CUST_BUY_METHOD as $k => $v){ ?>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchBuyMethod[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchBuyMethod"> <?=$v?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th>업종</th>
                            <td>
                                <select v-model="searchCondition['sRadioSchCate1.sno']" @change="searchCondition.sRadioSchBusiCateSno = 'all';" class="form-control" style="display: inline; min-width: 100px;">
                                    <option value="all">전체</option>
                                    <option v-for="(val, key) in oParentCateList" :value="key">{% val %}</option>
                                </select>
                                <select v-model="searchCondition.sRadioSchBusiCateSno" class="form-control" style="display: inline; min-width: 150px;">
                                    <option value="all">전체</option>
                                    <option v-if="key!=0" v-for="(val, key) in oCateList[searchCondition['sRadioSchCate1.sno']]" :value="key">{% val %}</option>
                                </select>
                            </td>
                            <th>IMS등록여부</th>
                            <td>
                                <label class="radio-inline">
                                    <input type="radio" v-model="searchCondition.sExistOrNotSchCustomerSno" value="all" name="sSchRadioImsYn" style="display: inline;" />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" v-model="searchCondition.sExistOrNotSchCustomerSno" value="exist" name="sSchRadioImsYn" style="display: inline;" />등록
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" v-model="searchCondition.sExistOrNotSchCustomerSno" value="not" name="sSchRadioImsYn" style="display: inline;" />미등록
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>일자 검색</th>
                            <td colspan="3">
                                <select v-model="searchCondition.selectSchDate" @change="changeSchDtType($event.target.value)" class="form-control mgb5">
                                    <option value="1">최근 영업일자</option>
                                    <option value="2">등록일자</option>
                                </select>
                                <span v-show="searchCondition.selectSchDate == 1" class="form-inline">
                                    <span class="mini-picker mgl5" >
                                        <date-picker v-model="searchCondition.sTextboxRangeStartSchContactDt" value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                                    </span>
                                    &nbsp;~
                                    <span class="mini-picker mgl5">
                                        <date-picker v-model="searchCondition.sTextboxRangeEndSchContactDt"  value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                                    </span>
                                    <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchContactDt', 'sTextboxRangeEndSchContactDt', 'today')">오늘</div>
                                    <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchContactDt', 'sTextboxRangeEndSchContactDt', 'week')">이번주</div>
                                    <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchContactDt', 'sTextboxRangeEndSchContactDt', 'month')">이번달</div>
                                    <div class="btn btn-sm btn-white mgb4" @click="searchCondition.sTextboxRangeStartSchContactDt = ''; searchCondition.sTextboxRangeEndSchContactDt = '';">전체</div>
                                </span>
                                <span v-show="searchCondition.selectSchDate == 2" class="form-inline">
                                    <span class="mini-picker mgl5" >
                                        <date-picker v-model="searchCondition.sTextboxRangeStartSchRegDt" value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                                    </span>
                                    &nbsp;~
                                    <span class="mini-picker mgl5">
                                        <date-picker v-model="searchCondition.sTextboxRangeEndSchRegDt"  value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                                    </span>
                                    <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchRegDt', 'sTextboxRangeEndSchRegDt', 'today')">오늘</div>
                                    <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchRegDt', 'sTextboxRangeEndSchRegDt', 'week')">이번주</div>
                                    <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchRegDt', 'sTextboxRangeEndSchRegDt', 'month')">이번달</div>
                                    <div class="btn btn-sm btn-white mgb4" @click="searchCondition.sTextboxRangeStartSchRegDt = ''; searchCondition.sTextboxRangeEndSchRegDt = '';">전체</div>
                                </span>
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
                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(1)">엑셀 다운로드</button>
                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="D,asc">등록일시 ▲</option>
                            <option value="D,desc">등록일시 ▼</option>
                            <option value="SCCAD,asc">후속 영업일자 ▲</option>
                            <option value="SCCAD,desc">후속 영업일자 ▼</option>
                            <option value="jsonExpectSales,asc">추정매출 ▲</option>
                            <option value="jsonExpectSales,desc">추정매출 ▼</option>
                            <option value="cate1.cateName,asc">업종 ▲</option>
                            <option value="cate1.cateName,desc">업종 ▼</option>
                            <option value="cate2.cateName,asc">세부업종 ▲</option>
                            <option value="cate2.cateName,desc">세부업종 ▼</option>
                            <option value="buyDiv,asc">유니폼 종류 ▲</option>
                            <option value="buyDiv,desc">유니폼 종류 ▼</option>
                            <option value="bidDt,asc">입찰 예정일 ▲</option>
                            <option value="bidDt,desc">입찰 예정일 ▼</option>
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
                        <col class="w-50px"><!--체크-->
                        <col class="w-36px" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip" />
                        <col class="w-60px" />
                    </colgroup>
                    <tr>
                        <th>
                            <input type="checkbox" id="resAllCheck" value="y" class="js-checkall" data-target-name="saleCustomerSno" />
                        </th>
                        <th >번호</th>
                        <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                            {% fieldData.title %}
                        </th>
                        <th>활동 이력</th>
                    </tr>
                    <tr  v-if="0 >= listData.length">
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    <tr v-for="(val , key) in listData" class="hover-light">
                        <td >
                            <input type="checkbox" name="saleCustomerSno[]" :disabled="(val.customerSno!=0&&val.customerSno!=null)?true:false" :value="val.sno" class="res-list-check " />
                        </td>
                        <td >{% (listTotal.idx - key) %}</td>
                        <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                            <span v-if="fieldData.type === 'company_name'" class="sl-blue cursor-pointer hover-btn" @click="openCommonPopup('sales_customer_contents', 1050, 750, {sno:val.sno});">
                                {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                                <span v-html="val.styleCode===null||val.styleCode==''?'':' <span class=\'text-danger font-10\'>('+val.styleCode+')</span>'"></span>
                                <div class="font-10 text-muted">영업담당자 : {% val.salesManagerName==null||val.salesManagerName==''?'-':val.salesManagerName %}</div>
                            </span>
                            <span v-else-if="fieldData.type === 'customer_detail'">
                                <span v-if="val[fieldData.name] == 'O'" @click="openCallView('/ims/customer_view.php?sno='+val.customerSno);" class="sl-blue cursor-pointer hover-btn" style="text-decoration: underline;">{% val[fieldData.name] %}</span>
                                <span v-else>{% val[fieldData.name] %}</span>
                            </span>
                            <span v-else-if="fieldData.type === 'cate_name'">
                                {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                                <div class="font-10 text-muted">{% val.cateName===null||val.cateName==''?'-':val.cateName %}</div>
                            </span>
                            <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                            <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                            <span v-else-if="fieldData.type === 'i2'">
                                <span v-if="val[fieldData.name]>0">
                                    {% $.numberToKorean(val[fieldData.name]) %}원
                                </span>
                                <span v-else>
                                    -
                                </span>
                            </span>
                            <span v-else-if="fieldData.type === 'date'">{% $.formatShortDate(val[fieldData.name]) %}</span>
                            <span v-else-if="fieldData.type === 'etc'">
                                    <span @click="openUpsertDriveModal(val.sno);" class="btn btn-sm btn-white hover-btn cursor-pointer">수정</span>
                                    <span @click="removeDrive(val.sno);" class="btn btn-sm btn-white hover-btn cursor-pointer">삭제</span>
                                </span>
                            <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                        </td>
                        <td style="position: relative;">
                            <div class="btn btn-gray btn-sm" :data-sno="val.sno" @click="openCommonPopup('sales_customer_contents', 1050, 750, {sno:val.sno, mode:'regist'});" @mouseover="$refs.layer_action_contents_list[key].style.display='block';" @mouseleave="$refs.layer_action_contents_list[key].style.display='none';">내용({% val.contentsList.length %})</div>
                            <div ref="layer_action_contents_list" class="memo_layer">
                                <div class="layer-order-add-info" >
                                    <div class="table-title">
                                        {% val.customerName %} 활동 이력
                                    </div>
                                    <table class="table table-rows " >
                                        <colgroup>
                                            <col class="width-xs"/>
                                            <col class="width-xs"/>
                                            <col/>
                                        </colgroup>
                                        <tr>
                                            <th class="ta-c">통화일자</th>
                                            <th class="ta-c">담당자</th>
                                            <th class="ta-c">통화내용</th>
                                        </tr>
                                        <tbody>
                                        <tr v-if="val.contentsList.length == 0">
                                            <td colspan="99" class="ta-c">등록된 내용이 없습니다.</td>
                                        </tr>
                                        <tr v-else v-for="val2 in val.contentsList">
                                            <td>{% val2.regDt %}</td>
                                            <td>{% val2.regManagerName %}</td>
                                            <td>{% val2.contents %}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <!--list end-->
            <div id="find_customer-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

    <div class="modal fade" id="modalUpsert" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:500px;">
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                        고객 발굴 {% oUpsertForm.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %} <span v-show="isModify" class="text-danger font-13">* : 필수입력</span>
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
                            <th>업체명 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <?php $model='oUpsertForm.customerName'; $placeholder='업체명' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>업종 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <div v-show="isModify">
                                    <select @change="oUpsertForm.busiCateSno = 0;" v-model="oUpsertForm.parentBusiCateSno" class="form-control" style="width:100%;">
                                        <option value="0">선택</option>
                                        <option v-for="(val, key) in oParentCateList" :value="key">{% val %}</option>
                                    </select>
                                </div>
                                <div v-show="!isModify" >
                                    {% oUpsertForm.parentCateName %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>세부 업종 <span v-show="isModify" class="text-danger font-13">*</span></th>
                            <td>
                                <div v-show="isModify">
                                    <select v-model="oUpsertForm.busiCateSno" class="form-control" style="width:100%;">
                                        <option v-for="(val, key) in oCateList[oUpsertForm.parentBusiCateSno]" :value="key">{% val %}</option>
                                    </select>
                                </div>
                                <div v-show="!isModify" >
                                    {% oUpsertForm.cateName %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>사원수</th>
                            <td>
                                <?php $model='oUpsertForm.employeeCnt'; $placeholder='사원수'; $suffixText='명'; ?>
                                <?php include './admin/ims/template/basic_view/_number.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>대표번호</th>
                            <td>
                                <?php $model='oUpsertForm.phone'; $placeholder='대표번호' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>영업담당자</th>
                            <td>
                                <div v-show="isModify">
                                    <select2 class="js-example-basic-single" v-model="oUpsertForm.salesManagerSno" style="width:100%;">
                                        <?php foreach ($salesManagerList as $key => $val ) { ?>
                                            <option value="<?=$key?>"><?=$val?></option>
                                        <?php } ?>
                                    </select2>
                                </div>
                                <div v-show="!isModify" >
                                    {% oUpsertForm.salesManagerName %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>유니폼 종류</th>
                            <td>
                                <div v-show="isModify">
                                    <?php foreach (\Component\Ims\NkCodeMap::SALES_CUST_BUY_DIV as $key => $val ) { ?>
                                        <label class="radio-inline">
                                            <input type="radio" v-model="oUpsertForm.buyDiv" value="<?=$key?>" name="sRadioUpsertBuyDiv" /><?=$val?>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="!isModify" >
                                    {% oUpsertForm.buyDiv %}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>고객사 이니셜</th>
                            <td>
                                <?php $model='oUpsertForm.styleCode'; $placeholder='고객사 이니셜(프로젝트 등록시 필수)' ?>
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

</section>

<?php include 'script/ims_find_customer_list_script.php'?>

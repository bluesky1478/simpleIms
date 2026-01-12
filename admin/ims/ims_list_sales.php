<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;" id="affix-menu">
    <section id="affix-show-type1">
        <h3 >영업 리스트 <span class="font-12">(입찰/협의 및 정보 취득)</span></h3>
        <div class="btn-group">
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" id="btn-reg-project" />
        </div>
    </section>
    <!--틀고정-->
    <section id="affix-show-type2" style="margin:0 !important; display: none "></section>
</div>

<section id="imsApp" class="project-view">
    <div v-show="false" style="display:none" @click="openCommonPopup('project_reg', 900, 835, {})" id="btn-reg-project-hide"></div>

    <!--검색-->
    <div class="row">
        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline" >
                    <table class="table table-cols table-th-height0 table-td-height0 table-pd-10">
                        <colgroup>
                            <col class="w-7p">
                            <col class="w-33p">
                            <col class="w-7p">
                            <col class="w-18p">
                            <col class="w-7p">
                            <col class="w-28p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="3">
                                검색어
                            </th>
                            <td rowspan="3">
                                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
                                    <div class="btn btn-sm btn-red" @click="searchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
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
                            <th>
                                진행타입
                            </th>
                            <td >
                                <div>
                                    <label class="radio-inline ">
                                        <input type="radio" name="bidType2" value="all" v-model="searchCondition.bidType2" @change="refreshList(1)" />전체
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="bidType2" value="bid" v-model="searchCondition.bidType2"  @change="refreshList(1)" />입찰
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="bidType2" value="costBid" v-model="searchCondition.bidType2"  @change="refreshList(1)" />비딩
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="bidType2" value="single" v-model="searchCondition.bidType2"  @change="refreshList(1)" />단독
                                    </label>
                                </div>
                            </td>
                            <th>사업계획</th>
                            <td >
                                <label class="radio-inline ">
                                    <input type="radio" name="bizPlanYn" value="all" v-model="searchCondition.bizPlanYn" @change="refreshList(1)" />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bizPlanYn" value="y" v-model="searchCondition.bizPlanYn"  @change="refreshList(1)" />포함
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bizPlanYn" value="n" v-model="searchCondition.bizPlanYn"  @change="refreshList(1)" />미포함
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                영업단계
                            </th>
                            <td >
                                <div class="checkbox ">
                                    <label class="radio-inline">
                                        <input type="radio" name="orderProgressChk" value="10,15,11" v-model="searchCondition.orderProgressChk"  @change="refreshList(1)" />전체
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="orderProgressChk" value="10" v-model="searchCondition.orderProgressChk"  @change="refreshList(1)" />대기
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="orderProgressChk" value="15" v-model="searchCondition.orderProgressChk"  @change="refreshList(1)" />진행
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="orderProgressChk" value="11" v-model="searchCondition.orderProgressChk"  @change="refreshList(1)" />보류
                                    </label>
                                </div>
                            </td>
                            <th>
                                진행 타입
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div>
                                        <label class="radio-inline ">
                                            <input type="radio" name="bidType" value="all" v-model="searchCondition.bidType2" @change="refreshList(1)" />전체
                                        </label>
                                        <?php foreach(\Component\Ims\ImsCodeMap::BID_TYPE as $designWorkKey => $designWorkName){ ?>
                                            <?php if(empty($designWorkKey)) continue;?>
                                            <label class="radio-inline">
                                                <input type="radio" name="bidType" value="<?=$designWorkKey?>" v-model="searchCondition.bidType2"  @change="refreshList(1)" /><?=$designWorkName?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>예정 디자이너</th>
                            <td>
                                <select class="form-control w200p" v-model="searchCondition.extDesigner">
                                    <option value="">전체</option>
                                    <?php foreach ($designManagerList as $key => $value ) { ?>
                                        <option value="<?=$value?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th>
                                업무구분
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div>
                                        <label class="radio-inline ">
                                            <input type="radio" name="designWorkType" value="all" v-model="searchCondition.designWorkType" @change="refreshList(1)" />전체
                                        </label>
                                        <?php foreach(\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $designWorkKey => $designWorkName){ ?>
                                            <?php if(empty($designWorkKey)) continue;?>
                                            <label class="radio-inline">
                                                <input type="radio" name="designWorkType" value="<?=$designWorkKey?>" v-model="searchCondition.designWorkType"  @change="refreshList(1)" /><?=$designWorkName?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>담당</th>
                            <td>
                                <div class="dp-flex">
                                    담당영업 :
                                    <div class="btn btn-white btn-sm" @click="conditionReset()">전체</div>
                                    <div class="btn btn-white btn-sm" @click="setSales(0)">미지정</div>
                                    <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                        <div class="btn btn-white btn-sm" @click="setSales('<?=$salesSno?>')"><?=$sales?></div>
                                    <?php } ?>
                                </div>
                                <div class="dp-flex mgt5">
                                    디자이너 :
                                    <div class="btn btn-white btn-sm" @click="conditionReset()">전체</div>
                                    <div class="btn btn-white btn-sm" @click="setDesigner('designTbc')">미지정</div>
                                    <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                        <div class="btn btn-white btn-sm" @click="setDesigner('<?=$designer?>')"><?=$designer?></div>
                                    <?php } ?>
                                </div>
                            </td>
                            <th class="font-12">프로젝트타입</th>
                            <td colspan="4">
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]" :checked="0 >= searchCondition.projectTypeChk.length?'checked':''" @click="searchCondition.projectTypeChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  v-model="searchCondition.projectTypeChk"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!--검색 끝-->
            </div>

            <div class="dp-flex dp-flex-center">
                <div class="btn btn-lg btn-black w-100px" @click="refreshList(1)">검색</div>
                <div class="btn btn-lg btn-white w-100px" @click="conditionReset()">초기화</div>
            </div>

            <div >
                <div class="dp-flex" style="justify-content: space-between">
                    <div class="mgb5 mgt25">
                        <div class="dp-flex dp-flex-gap10 font-16">
                            <div class="total hover-btn cursor-pointer" @click="conditionReset();refreshList(1)">검색 <span class="text-danger">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건</div>
                        </div>
                    </div>
                    <div class="mgb5">
                        <div class="" style="display: flex;padding-top:20px">
                            <input type="button" value="일괄수정" class="btn btn-white btn-red btn-red-line2" @click="setModify(true)" v-show="!isModify">
                            <input type="button" value="저장" class="btn btn-red" @click="save()" v-show="isModify">&nbsp;
                            <input type="button" value="일괄수정취소" class="btn btn-red btn-red-line2" @click="setModify(false)" v-show="isModify">
                            <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload(1)">다운로드</button>

                            <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                                <option value="SA1,asc">매출목표 ▲</option>
                                <option value="SA1,desc">매출목표 ▼</option>
                                <option value="SA2,asc">계약난이도 ▲</option>
                                <option value="SA2,desc">계약난이도 ▼</option>
                                <option value="P5,asc">프로젝트 상태 ▲</option>
                                <option value="P5,desc">프로젝트 상태 ▼</option>
                                <option value="P1,asc">등록일(프로젝트번호) ▲</option>
                                <option value="P1,desc">등록일(프로젝트번호) ▼</option>
                            </select>

                            <select v-model="searchCondition.pageNum" @change="refreshList(1)" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                                <option value="200">200개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="">
                    <table class="table table-rows table-default-center table-td-height30 table-th-height0 mgt5" v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true != fieldData.subRow" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="pd5"><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
                            <th class="pd5">번호</th>
                            <th v-for="fieldData in searchData.fieldData"
                                v-if="true != fieldData.skip && true != fieldData.subRow"
                                class="pd5" v-html="fieldData.title">
                            </th>
                        </tr>
                        </thead>
                        <tr v-if="0 >= listData.length">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tbody v-for="(each , index) in listData" >
                        <tr>
                            <td :rowspan="2">
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                            </td>
                            <td :rowspan="2">
                                <div>{% (listTotal.idx-index) %}</div>
                            </td>
                            <td v-for="fieldData in searchData.fieldData"
                                :rowspan="true == fieldData.rowspan || !$.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) ?2:1"
                                :class="fieldData.class"
                                :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''"
                                v-if="true != fieldData.subRow">

                                <!--{% each.extDesigner %}-->

                                <?php include 'nlist/list_template.php'?>

                                <!--매출목표 & 계약난이도-->
                                <div v-if="'targetSalesYear' === fieldData.name">
                                    <div v-if="!isModify">
                                        <div v-if="$.isEmpty(each.targetSalesYear) || each.targetSalesYear == ''" class="text-muted font-11">확인중</div>
                                        <div v-else>{% each.targetSalesYear %}년</div>
                                        <div v-if="$.isEmpty(each.contractDifficultKr) || each.contractDifficultKr == ''" class="text-muted font-11">확인중</div>
                                        <div v-else>{% each.contractDifficultKr %}</div>
                                    </div>
                                    <div v-else>
                                        <select class="form-control" v-model="listUpdateMulti[index].targetSalesYear">
                                            <option value="">선택</option>
                                            <?php foreach ($yearFullList as $key => $value ) { ?>
                                                <option value="<?=$value?>"><?=$key?></option>
                                            <?php } ?>
                                        </select>
                                        <select class="form-control" v-model="listUpdateMulti[index].contractDifficult">
                                            <?php foreach( \Component\Ims\ImsCodeMap::RATING_TYPE2 as $k => $v){ ?>
                                                <option value="<?=$k?>"><?=$v?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!--프로젝트 타입-->
                                <div v-else-if="'projectTypeKr' === fieldData.name">
                                    <div v-show="!isModify">
                                        <div>{% each.projectTypeKr %}</div>

                                        <div class="mgt5">
                                            <div v-if="'bid'===each.bidType2" class="font-11">
                                                <i class="fa fa-gavel" aria-hidden="true"></i>
                                                {% each.bidType2Kr %}
                                            </div>
                                            <div v-if="'costBid'===each.bidType2" class="font-11">
                                                <i class="fa fa-krw" aria-hidden="true"></i>
                                                {% each.bidType2Kr %}
                                            </div>
                                            <div v-if="'single'===each.bidType2" class="font-11">
                                                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                                                {% each.bidType2Kr %}
                                            </div>
                                        </div>

                                    </div>
                                    <div v-show="isModify">
                                        <select class="form-control" v-model="listUpdateMulti[index].projectType">
                                            <option value="">선택</option>
                                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE_N as $k => $v){ ?>
                                                <option value="<?=$k?>"><?=$v?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!--디자인 업무 타입 -> 업무구분-->
                                <div v-else-if="'designWorkTypeKr' === fieldData.name">
                                    <div v-show="!isModify">
                                        {% each.designWorkTypeKr %}
                                    </div>
                                    <div v-show="isModify">
                                        <select class="form-control" v-model="listUpdateMulti[index].designWorkType">
                                            <?php foreach( \Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $k => $v){ ?>
                                                <option value="<?=$k?>"><?=$v?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!--담당자-->
                                <div v-else-if="'salesManagerNm' === fieldData.name" class="text-left font-11">
                                    <div v-show="!isModify">
                                        <div>
                                            영업담당 :
                                            <span v-if="$.isEmpty(each[fieldData.name])">미정</span>
                                            <span v-if="!$.isEmpty(each[fieldData.name])">{% each[fieldData.name] %}</span>
                                        </div>
                                        <div v-if="!$.isEmpty(each.extDesignerList) && each.extDesignerList.length > 0">
                                            <div class="text-muted">▼ 투입 예정 디자이너</div>
                                            <ul class="dp-flex font-11 dp-flex-wrap">
                                                <li v-for="designer in each.extDesignerList">{% designer %}</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div v-show="isModify">
                                        영업 : <select v-model="listUpdateMulti[index].salesManagerSno" class="form-control">
                                            <option value="0">선택</option>
                                            <?php foreach ($salesManagerList as $key => $value ) { ?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                        <br/>
                                        예정 디자이너는 영업기획서에서 수정
                                    </div>
                                </div>
                                <!--상태-->
                                <div v-else-if="'salesStatusKr' === fieldData.name" class="font-11">
                                    <div :class="'round-box dp-flex ims-status ims-status' + each.projectStatus" style="min-height:45px;justify-content: center; align-items: center; padding-left:13px; padding-right:13px">
                                        {% each.projectStatusKr %}
                                    </div>
                                </div>
                                <!--영업기획서 => 고객 코멘트-->
                                <div v-else-if="'salesView' === fieldData.name">
                                    <!--<div class="btn btn-sm btn-white " @click="openSalesView(each.sno)">보기</div>-->
                                    <div class="btn btn-sm btn-white" @click="openCustomer(each.customerSno,'comment')">보기</div>
                                </div>

                                <div v-else-if="'open' === fieldData.name">
                                    <a :href="`ims_view2.php?sno=${each.projectSno}&current=sales`" target="_blank" class="text-danger" style="color:black!important;"  @click="$.cookie('viewTabMode', '');">
                                        <div class="btn btn-white btn-sm mgl5 " >
                                            열기
                                        </div>
                                    </a>
                                </div>
                                <div v-else-if="'totalTargetMarginKr' === fieldData.name">
                                    {% each.totalTargetMarginKr %}
                                    <div v-show="each.totalTargetMargin > 0">({% each.totalTargetMargin %}%)</div>
                                </div>

                            </td>
                        </tr>
                        <!--완료일-->
                        <tr >
                            <td v-for="fieldData in searchData.fieldData"
                                v-if="true == fieldData.subRow && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) "
                                :class="fieldData.class">
                                <!--타입-->
                                <div v-if="'fldText' === fieldData.type">
                                    완료일
                                </div>
                                <div v-if="'subTitle' !== fieldData.name && !isModify" class="font-12">
                                    {% $.formatShortDateWithoutWeek(each['cp'+$.ucfirst(fieldData.name)]) %}
                                </div>
                                <div v-show="'fldText' !== fieldData.type && 'subTitle' !== fieldData.name && isModify" style="max-width:100px;">
                                    <date-picker v-model="listUpdateMulti[index]['cp'+$.ucfirst(fieldData.name)]" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="완료일"></date-picker>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="project-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>
    <?php include 'nlist/_emergency_layer_popup.php'?>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_sales_script.php'?>
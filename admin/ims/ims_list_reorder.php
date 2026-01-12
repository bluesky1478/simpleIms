<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<style>
    .mx-datepicker { width:100px!important; }
</style>

<section id="imsApp" class="project-view">

    <!--타이틀-->
    <div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;">
        <section id="affix-show-type1">
            <h3 id="production-title">리오더/기성복 준비 리스트</h3>
            <div class="btn-group">
                <input type="button" value="기성복 프로젝트 등록" class="btn btn-red btn-reg hover-btn" />
            </div>
        </section>
        <!--틀고정-->
        <section id="affix-show-type2" style="margin:0 !important; display: none ">
            <table class="table table-rows" style="margin-bottom:0 !important; "></table>
        </section>
    </div>

    <!--검색-->
    <div class="row">
        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0">
                        <colgroup>
                            <col class="w-7p">
                            <col class="w-43p">
                            <col class="w-7p">
                            <col class="w-43p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="2">
                                검색어
                            </th>
                            <td rowspan="2">
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
                                미확정 상태
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <label class="checkbox-inline mgr10">
                                        <input type="checkbox" name="delayStatus[]" value="all" class="js-not-checkall" data-target-name="delayStatus[]"
                                               :checked="0 >= searchCondition.delayStatus.length?'checked':''" @click="searchCondition.delayStatus=[];refreshList(1)"> 전체
                                    </label>

                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="2"
                                               v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 생산가 미확정
                                    </label>

                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="3"
                                               v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 판매가 미확정
                                    </label>

                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="4"
                                               v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 아소트 미확정
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                구분
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div class="checkbox ">
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="orderType[]" value="all" class="js-not-checkall" data-target-name="orderType[]"
                                                   :checked="0 >= searchCondition.orderType.length?'checked':''" @click="searchCondition.orderType=[]"> 전체
                                        </label>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="orderType[]" value="reorder"
                                                   v-model="searchCondition.orderType"  > 리오더
                                        </label>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="orderType[]" value="rtw"
                                                   v-model="searchCondition.orderType"  > 기성
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>부가서비스</th>
                            <td>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="useMall" value="y"
                                           v-model="searchCondition.chkUseMall"  > <span class="">폐쇄몰</span>
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="use3pl" value="y"
                                           v-model="searchCondition.chkUse3pl"  > <span class="">3PL</span>
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="packingYn" value="y"
                                           v-model="searchCondition.chkPackingYn" > <span class="">분류패킹</span>
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="directDeliveryYn" value="y"
                                           v-model="searchCondition.chkDirectDeliveryYn" > <span class="">직접납품</span>
                                </label>
                            </td>
                            <th>
                                연도/시즌
                            </th>
                            <td colspan="4" class="">
                                연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="searchCondition.year" style="width:80px" />
                                시즌 :
                                <select class="form-control" name="projectSeason" v-model="searchCondition.season">
                                    <option value="">선택</option>
                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                검색 기간
                            </th>
                            <td >
                                <div class="dp-flex">

                                    <select class="form-control" style="height:26px" v-model="searchCondition.searchDateType">
                                        <option value="">선택</option>
                                        <option value="prj.regDt">등록일</option>
                                        <option value="prj.customerOrderDeadLine">발주D/L</option>
                                    </select>

                                    <div class="mini-picker mgl5">
                                        <date-picker v-model="searchCondition.startDt"
                                                     value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="font-weight: normal"></date-picker>
                                    </div>
                                    <div>~</div>
                                    <div class="mini-picker">
                                        <date-picker v-model="searchCondition.endDt"
                                                     value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="font-weight: normal;"></date-picker>
                                    </div>

                                    <div class="form-inline" >
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                                    </div>
                                </div>
                            </td>
                            <th>
                                사업계획
                            </th>
                            <td class="">
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
                <div class="">
                    <div class="flo-left mgb5 mgt25">
                        <div class="font-16 dp-flex" >
                            <span style="font-size: 18px !important;">
                                총 <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                            </span>
                            <span class="mgl15">
                                <label class="radio-inline">
                                    <input type="radio" name="viewType" value="project" v-model="searchCondition.viewType"  @change="refreshList(1)" class="mgt5" />프로젝트별 보기
                                </label>
                                <label class="radio-inline" >
                                    <input type="radio" name="viewType" value="style" v-model="searchCondition.viewType"  @change="refreshList(1)" class="mgt5" />스타일별 보기
                                </label>
                            </span>
                        </div>

                    </div>
                    <div class="flo-right mgb5">
                        <div class="" style="display: flex;padding-top:20px">
                            <input type="button" value="일괄수정" class="btn btn-white btn-red btn-red-line2" @click="vueApp.isModify = true;" v-show="!isModify">
                            <input type="button" value="저장" class="btn btn-red" @click="save()" v-show="isModify">&nbsp;
                            <input type="button" value="일괄수정취소" class="btn btn-red btn-red-line2" @click="vueApp.isModify = false;" v-show="isModify">

                            <select @change="searchProject()" class="form-control mgl5" v-model="searchCondition.sort">
                                <option value="P7,asc">발주D/L ▲</option>
                                <option value="P7,desc">발주D/L ▼</option>
                                <option value="P3,asc">희망납기일 ▲</option>
                                <option value="P3,desc">희망납기일 ▼</option>
                                <option value="P2,asc">연도/등록일 ▲</option>
                                <option value="P2,desc">연도/등록일 ▼</option>
                                <option value="P4,asc">매출규모 ▲</option>
                                <option value="P4,desc">매출규모 ▼</option>
                                <option value="P1,asc">등록일 ▲</option>
                                <option value="P1,desc">등록일 ▼</option>
                                <option value="P5,asc">진행상태 ▲</option>
                                <option value="P5,desc">진행상태 ▼</option>
                            </select>

                            <select v-model="searchCondition.pageNum" @change="searchProject()" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                                <option value="200">200개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="" v-if="'project' === searchCondition.viewType">
                    <table class="table table-rows table-default-center table-td-height30 mgt5" v-if="!$.isEmpty(searchData)">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData"/>
                        </colgroup>
                        <tr>
                            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
                            <th>번호</th>
                            <th v-for="fieldData in searchData.fieldData"  :colspan="$.isEmpty(fieldData.colspan)?'1':fieldData.colspan"  v-if="true != fieldData.skip" >{% fieldData.title %}</th>
                        </tr>
                        <tr v-for="(each , index) in listData" >
                            <td>
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                            </td>
                            <td>
                                <div>{% (listTotal.idx-index) %}</div>
                            </td>
                            <td v-for="fieldData in searchData.fieldData"   :class="fieldData.class + ''">
                                <?php include 'nlist/list_template.php'?>

                                <div v-if="'c'===fieldData.type">

                                    <div v-if="'reorderProject' === fieldData.name">

                                        <div class="relative dp-flex">
                                            <div class="dp-flex dp-flex-gap5 " @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}&currentStatus=reorder`)">
                                                <span class="text-danger cursor-pointer hover-btn">{% each.sno %}</span>
                                                <span class="sl-blue cursor-pointer hover-btn" >{% each.customerName %}</span>
                                            </div>
                                            <div class="sl-badge-small sl-badge-small-blue mgl5 mgb3 " style="position: absolute;top:-5px;right:0" v-if="'y' === each.bizPlanYn">
                                                사업계획
                                            </div>
                                            <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                                <span class="text-muted cursor-pointer hover-btn mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </span>
                                            <?php } ?>
                                        </div>

                                        <div class="ta-l mgt5">
                                            {% each.projectTypeKr %}
                                        </div>

                                    </div>

                                    <!--아소트-->
                                    <div v-if="'assort' === fieldData.name">
                                        <i class="fa fa-lg fa-stop-circle color-gray font-12" aria-hidden="true" v-if="'n'==each.assortApproval"></i>
                                        <i class="fa fa-lg fa-play-circle sl-blue  font-12" aria-hidden="true" v-if="'r' == each.assortApproval"></i>
                                        <i class="fa fa-lg fa-check-circle text-green  font-12" aria-hidden="true" v-if="'p' == each.assortApproval"></i>
                                        <span v-html="$.getAcceptName(each.assortApproval)"></span>
                                    </div>

                                    <!--작지상태-->
                                    <div v-if="'workStatus' === fieldData.name">
                                        <span v-html="each.workStatusIcon"></span>
                                        <span v-html="each.workStatusKr"></span>
                                    </div>

                                    <!--발주DL-->
                                    <div v-if="'productionOrder' === fieldData.name">
                                        <?php include 'template/basic_view/_production_order.php'?>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="" v-if="'style' === searchCondition.viewType">
                    <?php include 'ims_list_qc_style.php'?>
                </div>

                <div id="reorder-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>
    <?php include 'nlist/_emergency_layer_popup.php'?>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_reorder_script.php'?>

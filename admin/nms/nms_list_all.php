<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .mx-datepicker { width:100px!important; }
    .scheDetailGrp { background-color : #F6F6F6; }
</style>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; " id="affix-menu">
    <section id="affix-show-type1">
        <h3 class="relative">
            프로젝트 리스트

            <!--<div class="btn btn-sm btn-gray font-12 mgl10 hover-btn" style="padding:4px 10px 2px 10px!important; height:30px ;background-color:#5b5b5b">전체</div>-->

        </h3>
        <div class="btn-group" style="margin-top:-50px" >
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" id="btn-reg-project" />
        </div>
    </section>
    <!--틀고정-->
    <section id="affix-show-type2" style="margin:0 !important; display: none "></section>
</div>

<!--<div>
    <div class="btn btn-sm btn-gray font-13 mgl10 hover-btn">전체</div>
</div>-->


<section id="imsApp" class="project-view">
    <div v-show="false" style="display:none" @click="openCommonPopup('project_reg', 900, 835, {})" id="btn-reg-project-hide"></div>

    <div class="row">
        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <!--검색 시작-->
                <?php include 'nms_list_all_search.php'?>
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

                            <div class="dp-flex" >
                                <div :class="'btn btn-lg ' + ('project' === searchCondition.viewType ? 'font-bold btn-gray':'btn-white')" @click="searchCondition.viewType='project';refreshList(1)">
                                    프로젝트별
                                </div>
                                <div :class="'btn btn-lg ' + ('style' === searchCondition.viewType ? 'font-bold btn-gray':'btn-white')" @click="searchCondition.viewType='style';refreshList(1)">
                                    스타일별
                                </div>
                            </div>

                            <div class="total hover-btn cursor-pointer">검색 <span class="text-danger">{% $.setNumberFormat(listTotal.typeAllCnt) %}</span> 건</div>

                            <div :class="'font-14'" >
                                검색매출 <span class="text-danger">{% listTotal.type1Cnt %}원</span>
                            </div>
                            <div :class="'font-14'" >
                                검색생산가 <span class="sl-blue">{% listTotal.type2Cnt %}</span>
                                <span class="font-11">(마진:{% listTotal.type4Cnt %}% / {% listTotal.type3Cnt %}원)</span>
                            </div>
                        </div>

                    </div>
                    <div class="mgb5">
                        <div class="" style="display: flex;padding-top:20px">

                            <span class="mgr15 font-16" v-show="false">
                                <label class="radio-inline">
                                    <input type="radio" name="viewType" value="project" v-model="searchCondition.viewType"  @change="refreshList(1)" class="mgt5" />프로젝트별 보기
                                </label>
                                <label class="radio-inline" >
                                    <input type="radio" name="viewType" value="style" v-model="searchCondition.viewType"  @change="refreshList(1)" class="mgt5" />스타일별 보기
                                </label>
                            </span>

                            <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(1)">다운로드</button>

                            <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort" >
                                <option value="P3,asc">고객납기일 ▲</option>
                                <option value="P3,desc">고객납기일 ▼</option>

                                <option value="P7,asc">발주D/L ▲</option>
                                <option value="P7,desc">발주D/L ▼</option>

                                <option value="P1,asc">등록일 ▲</option>
                                <option value="P1,desc">등록일 ▼</option>
                                <option value="P5,asc">진행상태 ▲</option>
                                <option value="P5,desc">진행상태 ▼</option>
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

                <div class="" v-if="'project' === searchCondition.viewType">
                    <!--리스트-->
                    <table class="table table-rows table-default-center table-td-height30" v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="(fieldData, key) in searchData.fieldData" v-if="key < 5 && true != fieldData.skip && true !== fieldData.subRow" />
                            <col class="w-3p" />
                            <col class="w-5p" />
                            <col v-if="!aFlagFoldSmallType1 && aScheDetailListBySmallGrp[1].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[1]" class="w-5p" />
                            <col class="w-5p" />
                            <col v-if="!aFlagFoldSmallType2 && aScheDetailListBySmallGrp[2].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[2]" class="w-5p" />
                            <col class="w-5p" />
                            <col v-if="!aFlagFoldSmallType3 && aScheDetailListBySmallGrp[3].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[3]" class="w-5p" />
                            <col class="w-5p" />
                            <col v-if="!aFlagFoldSmallType4 && aScheDetailListBySmallGrp[4].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[4]" class="w-5p" />
                            <col class="w-5p" />
                            <col v-if="!aFlagFoldSmallType5 && aScheDetailListBySmallGrp[5].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[5]" class="w-5p" />
                        </colgroup>
                        <tr>
                            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
                            <th>번호</th>

                            <th v-for="(fieldData, key) in searchData.fieldData" v-if="key < 5 && true != fieldData.subRow"  >
                                <div :class="['assort', 'prdPriceApproval', 'prdCostApproval'].includes(fieldData.name)?'font-9':''">
                                    {% fieldData.title %}
                                </div>
                            </th>
                            <th>구분</th>
                            <th>영업 <button type="button" class="btn btn-white btn-sm" v-show="aFlagFoldSmallType1" @click="aFlagFoldSmallType1 = false;">펼치기</button> <button type="button" class="btn btn-white btn-sm" v-show="!aFlagFoldSmallType1" @click="aFlagFoldSmallType1=true">접기</button></th>
                            <td v-if="!aFlagFoldSmallType1 && aScheDetailListBySmallGrp[1].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[1]" class="scheDetailGrp">{% val %}</td>
                            <th>기획 <button type="button" class="btn btn-white btn-sm" v-show="aFlagFoldSmallType2" @click="aFlagFoldSmallType2 = false;">펼치기</button> <button type="button" class="btn btn-white btn-sm" v-show="!aFlagFoldSmallType2" @click="aFlagFoldSmallType2=true">접기</button></th>
                            <td v-if="!aFlagFoldSmallType2 && aScheDetailListBySmallGrp[2].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[2]" class="scheDetailGrp">{% val %}</td>
                            <th>제안서 <button type="button" class="btn btn-white btn-sm" v-show="aFlagFoldSmallType3" @click="aFlagFoldSmallType3 = false;">펼치기</button> <button type="button" class="btn btn-white btn-sm" v-show="!aFlagFoldSmallType3" @click="aFlagFoldSmallType3=true">접기</button></th>
                            <td v-if="!aFlagFoldSmallType3 && aScheDetailListBySmallGrp[3].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[3]" class="scheDetailGrp">{% val %}</td>
                            <th>샘플 발송 <button type="button" class="btn btn-white btn-sm" v-show="aFlagFoldSmallType4" @click="aFlagFoldSmallType4 = false;">펼치기</button> <button type="button" class="btn btn-white btn-sm" v-show="!aFlagFoldSmallType4" @click="aFlagFoldSmallType4=true">접기</button></th>
                            <td v-if="!aFlagFoldSmallType4 && aScheDetailListBySmallGrp[4].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[4]" class="scheDetailGrp">{% val %}</td>
                            <th>발주 <button type="button" class="btn btn-white btn-sm" v-show="aFlagFoldSmallType5" @click="aFlagFoldSmallType5 = false;">펼치기</button> <button type="button" class="btn btn-white btn-sm" v-show="!aFlagFoldSmallType5" @click="aFlagFoldSmallType5=true">접기</button></th>
                            <td v-if="!aFlagFoldSmallType5 && aScheDetailListBySmallGrp[5].length > 0 && val != undefined" v-for="val in aScheDetailListBySmallGrp[5]" class="scheDetailGrp">{% val %}</td>
                        </tr>
                        <tbody v-if="0 >= listData.length">
                            <tr>
                                <td colspan="99">
                                    데이터가 없습니다.
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-for="(each , index) in listData" class="hover-light">
                        <!--예정일-->
                        <tr >
                            <td :rowspan="3">
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                            </td>
                            <td :rowspan="3">
                                <div>{% (listTotal.idx-index) %}</div>
                                <div>
                                    <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                        <span class="text-muted cursor-pointer hover-btn font-10 mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                                        </span>
                                    <?php } ?>
                                </div>
                            </td>

                            <td v-for="(fieldData, key)  in searchData.fieldData"
                                :rowspan="true == fieldData.rowspan || !$.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) || 9 == each['st'+$.ucfirst(fieldData.name)]?3:1"
                                v-if="key < 5 && true !== fieldData.subRow"
                                :class="fieldData.class + ''" :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''">

                                <?php include './admin/ims/nlist/list_template.php'?>

                                <div v-if="'c' === fieldData.type" class="pd0">

                                    <!--고객납기-->
                                    <div v-if="'customerDeliveryDt' === fieldData.name" class="text-left pdl5 relative font-13">
                                        <div v-if="!isModify || $.isEmpty(listUpdateMulti) || listUpdateMulti[index][fieldData.name] === undefined">
                                            <div v-if="2 == each.productionStatus">
                                            </div>
                                            <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
                                            <div v-if="!$.isEmpty(each[fieldData.name])">
                                                <span class="font-13">{% $.formatShortDateWithoutWeek(each[fieldData.name]) %}</span>
                                                <div class="font-11 mgt5" v-html="$.remainDate(each[fieldData.name],true)" v-if="91 != each.projectStatus"></div>
                                                <div class="font-11 mgt5 sl-green" v-if="91 == each.projectStatus">납기완료</div>
                                            </div>
                                        </div>
                                        <div v-else style="max-width:100px;">
                                            <date-picker v-model="listUpdateMulti[index][fieldData.name]" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객납기일"></date-picker>
                                        </div>
                                    </div>

                                    <!--디자인 리스트 프로젝트 정보-->
                                    <div v-if="'projectNo' === fieldData.name" class="text-left pdl5 relative font-13">
                                        <div >
                                            <div class="dp-flex dp-flex-gap5 cursor-pointer hover-btn" @click="$.cookie('viewTabMode', '');window.open(`/ims/ims_view2.php?testmode=1&sno=${each.projectSno}`)">
                                                <span class="text-danger">{% each.sno %}</span>
                                                <span class="sl-blue">{% each.customerName %}</span>
                                            </div>
                                            <div>
                                            <span class="cursor-pointer hover-btn font-12" @click="$.cookie('viewTabMode', '');window.open(`/ims/ims_view2.php?testmode=1&sno=${each.projectSno}`)">
                                                {% each.projectYear %} {% each.projectSeason %}
                                                {% each.productName %}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="font-10 text-muted">{% $.formatShortDateWithoutWeek(each.regDt) %} {% each.regManagerNm %} 등록</div>

                                        <div class="sl-badge-small sl-badge-small-blue mgl5 mgb3" style="position: absolute;top:-5px;right:0" v-if="'y' === each.bizPlanYn">
                                            사업계획
                                        </div>
                                    </div>

                                    <!--타입-->
                                    <div v-if="'projectType' === fieldData.name">
                                        <div class="font-bold font-11">
                                            <div class="round-box bg-light-blue" v-if="[0,2,6,5].includes(each.projectType)" style="padding:5px 10px">
                                                {% each.projectTypeKr %}
                                            </div>
                                            <div class="round-box bg-light-orange" v-else>
                                                {% each.projectTypeKr %}
                                            </div>
                                        </div>

                                        <div class="font-10 mgt3" v-if="0 != each.designWorkType">{% each.designWorkTypeKr %}</div>

                                        <div v-if="'bid'===each.bidType2" class="font-10 mgt3 text-muted">
                                            <i class="fa fa-gavel" aria-hidden="true"></i>
                                            {% each.bidType2Kr %}
                                        </div>
                                        <div v-if="'costBid'===each.bidType2" class="font-10 mgt3 text-muted">
                                            <i class="fa fa-krw" aria-hidden="true"></i>
                                            {% each.bidType2Kr %}
                                        </div>
                                        <div v-if="'single'===each.bidType2" class="font-10 mgt3 text-muted">
                                            <i class="fa fa-handshake-o" aria-hidden="true"></i>
                                            {% each.bidType2Kr %}
                                        </div>
                                    </div>

                                    <!--발주D/L-->
                                    <div v-if="'productionOrder' === fieldData.name">
                                        <?php include './admin/ims/template/basic_view/_production_order.php'?>
                                    </div>

                                    <!--생산가-->
                                    <div v-if="'prdCostApproval' === fieldData.name">
                                        <div>
                                            <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.costStatus"></i>
                                            <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.costStatus"></i>
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.costStatus"></i>
                                        </div>
                                    </div>

                                    <!--타입-->
                                    <div v-if="'subTitle' === fieldData.name" class="cursor-pointer hover-btn"
                                         @click="openProjectSimple(each.sno)">
                                        예정일
                                    </div>

                                    <!--상태-->
                                    <div v-if="'projectStatusKr' === fieldData.name" class="lineR font-13">
                                        <div :class="'round-box dp-flex ims-status ims-status' + each.projectStatus"
                                             style="min-height:45px;justify-content: center; align-items: center; padding-left:18px; padding-right:18px">
                                            {% each.projectStatusKr %}
                                        </div>
                                    </div>

                                    <!--필수상태-->
                                    <div v-if="'requiredStatus' === fieldData.name">
                                        <div class="div-info-list">
                                            <div class="info-item">
                                                <div class="label label2 dp-flex">
                                                    <div>
                                                        Q:
                                                        <i class="fa  fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.fabricStatus"></i>
                                                        <i class="fa  fa-check-circle text-green" aria-hidden="true" v-else-if="2 == each.fabricStatus"></i>
                                                        <i class="fa  fa-stop-circle color-gray" aria-hidden="true" v-else></i>
                                                    </div>
                                                    <div class="pdl4">
                                                        B:
                                                        <i class="fa  fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.btStatus"></i>
                                                        <i class="fa  fa-check-circle text-green" aria-hidden="true" v-else-if="2 == each.btStatus"></i>
                                                        <i class="fa  fa-stop-circle color-gray" aria-hidden="true" v-else></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="label label2">아소트 :</div>
                                                <div>
                                                    <i class="fa  fa-play-circle sl-blue" aria-hidden="true" v-if="'r' == each.assortApproval"></i>
                                                    <i class="fa  fa-check-circle text-green" aria-hidden="true" v-else-if="'p' == each.assortApproval"></i>
                                                    <i class="fa  fa-stop-circle color-gray" aria-hidden="true" v-else="'n'==each.assortApproval"></i>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="label label2">판매가 :</div>
                                                <div>
                                                    <i class="fa  fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.priceStatus"></i>
                                                    <i class="fa  fa-check-circle text-green" aria-hidden="true" v-else-if="2 == each.priceStatus"></i>
                                                    <i class="fa  fa-stop-circle color-gray" aria-hidden="true" v-else></i>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <div class="label label2">생산가 :</div>
                                                <div>
                                                    <i class="fa  fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.costStatus"></i>
                                                    <i class="fa  fa-check-circle text-green" aria-hidden="true" v-else-if="2 == each.costStatus"></i>
                                                    <i class="fa  fa-stop-circle color-gray" aria-hidden="true" v-else></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--부가서비스-->
                                    <div v-if="'addService' === fieldData.name">
                                        <div class="div-info-list">
                                            <div class="info-item cursor-pointer hover-btn">
                                                <div class="label">폐쇄몰 :</div>
                                                <div :class="'value ' + ('y'==each.useMall?'text-green bold':'text-muted')">{% each.useMallKr %}</div>
                                            </div>
                                            <div class="info-item cursor-pointer hover-btn">
                                                <div class="label">3PL :</div>
                                                <div :class="'value ' + ('y'==each.use3pl?'text-green bold':'text-muted')">{% each.use3plKr %}</div>
                                            </div>
                                            <div class="info-item cursor-pointer hover-btn">
                                                <div class="label">분류패킹 :</div>
                                                <div :class="'value ' + ('y'==each.packingYn?'text-green bold':'text-muted')">{% each.packingYnKr %}</div>
                                            </div>
                                            <div class="info-item cursor-pointer hover-btn">
                                                <div class="label">직접납품 :</div>
                                                <div :class="'value ' + ('y'==each.directDeliveryYn?'text-green bold':'text-muted')">{% each.directDeliveryYnKr %}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--담당자-->
                                    <div v-if="'managerName' === fieldData.name">
                                        <div class="font-11" v-if="!$.isEmpty(each.salesManagerNm)">영 : {% each.salesManagerNm %}</div>
                                        <span class="text-muted font-11" v-if="$.isEmpty(each.salesManagerNm)">영 : 미지정</span>
                                        <div class="font-11" v-if="!$.isEmpty(each.designManagerNm)">디 : {% each.designManagerNm %}</div>
                                        <span class="text-muted font-11" v-if="$.isEmpty(each.designManagerNm)">디 : 미지정</span>
                                    </div>

                                    <!--매출정보-->
                                    <div v-if="'salesInfo' === fieldData.name" class="text-left font-11">
                                        <div>매출: {% each.totalPrdPriceKr %}</div>
                                        <div>마진: {% each.totalMarginKr %}</div>
                                        <div>마진%: {% each.totalMarginPercent %}%</div>
                                    </div>

                                    <!-- TODO 메모-->
                                    <div v-if="'projectMemo' === fieldData.name">
                                        <div class="btn btn-sm btn-white">메모</div>
                                    </div>

                                </div>
                            </td>

                            <th>예정일</th>
                            <td class="scheDetailGrp">
                                <span v-if="each.lastGrpInfo != undefined">
                                    {% $.formatShortDateWithoutWeek(each.lastGrpInfo[1].expectedDt) %}
                                    <span v-if="each.lastGrpInfo[1].cntMyWork > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% each.lastGrpInfo[1].cntMyWork %}</div></span>
                                    <div class="font-11 " v-html="$.remainDate(each.lastGrpInfo[1].expectedDt,true)"></div>
                                </span><span v-else>--</span>
                            </td>
                            <td v-if="!aFlagFoldSmallType1 && aScheDetailListBySmallGrp[1].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[1]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].expectedDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp">
                                <span v-if="each.lastGrpInfo != undefined">
                                    {% $.formatShortDateWithoutWeek(each.lastGrpInfo[2].expectedDt) %}
                                    <span v-if="each.lastGrpInfo[2].cntMyWork > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% each.lastGrpInfo[2].cntMyWork %}</div></span>
                                    <div class="font-11 " v-html="$.remainDate(each.lastGrpInfo[2].expectedDt,true)"></div>
                                </span><span v-else>--</span>
                            </td>
                            <td v-if="!aFlagFoldSmallType2 && aScheDetailListBySmallGrp[2].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[2]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].expectedDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp">
                                <span v-if="each.lastGrpInfo != undefined">
                                    {% $.formatShortDateWithoutWeek(each.lastGrpInfo[3].expectedDt) %}
                                    <span v-if="each.lastGrpInfo[3].cntMyWork > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% each.lastGrpInfo[3].cntMyWork %}</div></span>
                                    <div class="font-11 " v-html="$.remainDate(each.lastGrpInfo[3].expectedDt,true)"></div>
                                </span><span v-else>--</span>
                            </td>
                            <td v-if="!aFlagFoldSmallType3 && aScheDetailListBySmallGrp[3].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[3]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].expectedDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp">
                                <span v-if="each.lastGrpInfo != undefined">
                                    {% $.formatShortDateWithoutWeek(each.lastGrpInfo[4].expectedDt) %}
                                    <span v-if="each.lastGrpInfo[4].cntMyWork > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% each.lastGrpInfo[4].cntMyWork %}</div></span>
                                    <div class="font-11 " v-html="$.remainDate(each.lastGrpInfo[4].expectedDt,true)"></div>
                                </span><span v-else>--</span>
                            </td>
                            <td v-if="!aFlagFoldSmallType4 && aScheDetailListBySmallGrp[4].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[4]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].expectedDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp">
                                <span v-if="each.lastGrpInfo != undefined">
                                    {% $.formatShortDateWithoutWeek(each.lastGrpInfo[5].expectedDt) %}
                                    <span v-if="each.lastGrpInfo[5].cntMyWork > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">{% each.lastGrpInfo[5].cntMyWork %}</div></span>
                                    <div class="font-11 " v-html="$.remainDate(each.lastGrpInfo[5].expectedDt,true)"></div>
                                </span><span v-else>--</span>
                            </td>
                            <td v-if="!aFlagFoldSmallType5 && aScheDetailListBySmallGrp[5].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[5]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].expectedDt %}</span><span v-else>--</span>
                            </td>
                        </tr>
                        <tr>
                            <th>완료일</th>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[1].completeDt %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType1 && aScheDetailListBySmallGrp[1].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[1]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].completeDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[2].completeDt %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType2 && aScheDetailListBySmallGrp[2].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[2]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].completeDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[3].completeDt %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType3 && aScheDetailListBySmallGrp[3].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[3]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].completeDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[4].completeDt %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType4 && aScheDetailListBySmallGrp[4].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[4]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].completeDt %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[5].completeDt %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType5 && aScheDetailListBySmallGrp[5].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[5]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].completeDt %}</span><span v-else>--</span>
                            </td>
                        </tr>
                        <tr>
                            <th>담당자</th>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[1].departName!=''?each.lastGrpInfo[1].departName:each.lastGrpInfo[1].ownerManagerName %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType1 && aScheDetailListBySmallGrp[1].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[1]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].departName!=''?each.scheDetailList[key].departName:each.scheDetailList[key].ownerManagerName %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[2].departName!=''?each.lastGrpInfo[2].departName:each.lastGrpInfo[2].ownerManagerName %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType2 && aScheDetailListBySmallGrp[2].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[2]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].departName!=''?each.scheDetailList[key].departName:each.scheDetailList[key].ownerManagerName %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[3].departName!=''?each.lastGrpInfo[3].departName:each.lastGrpInfo[3].ownerManagerName %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType3 && aScheDetailListBySmallGrp[3].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[3]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].departName!=''?each.scheDetailList[key].departName:each.scheDetailList[key].ownerManagerName %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[4].departName!=''?each.lastGrpInfo[4].departName:each.lastGrpInfo[4].ownerManagerName %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType4 && aScheDetailListBySmallGrp[4].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[4]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].departName!=''?each.scheDetailList[key].departName:each.scheDetailList[key].ownerManagerName %}</span><span v-else>--</span>
                            </td>
                            <td class="scheDetailGrp"><span v-if="each.lastGrpInfo != undefined">{% each.lastGrpInfo[5].departName!=''?each.lastGrpInfo[5].departName:each.lastGrpInfo[5].ownerManagerName %}</span><span v-else>--</span></td>
                            <td v-if="!aFlagFoldSmallType5 && aScheDetailListBySmallGrp[5].length > 0 && val != undefined" v-for="(val, key) in aScheDetailListBySmallGrp[5]">
                                <span v-if="each.scheDetailList[key] != undefined">{% each.scheDetailList[key].departName!=''?each.scheDetailList[key].departName:each.scheDetailList[key].ownerManagerName %}</span><span v-else>--</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="" v-if="'style' === searchCondition.viewType">
                    <?php include 'nms_list_all_style.php'?>
                </div>

                <div id="all-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>
    <?php include './admin/ims/nlist/_emergency_layer_popup.php'?>

</section>

<?php include './admin/ims/nlist/list_common_script.php'?>
<?php include 'nms_list_all_script.php'?>

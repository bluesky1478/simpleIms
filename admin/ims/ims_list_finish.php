<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<style>
    .mx-datepicker { width:100px!important; }
</style>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; " id="affix-menu">
    <section id="affix-show-type1">
        <h3 class="relative">
            종결 프로젝트 리스트
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
                <?php include 'ims_list_complete_search.php'?>
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

                            <div class="total hover-btn cursor-pointer">검색 <span class="text-danger">{% $.setNumberFormat(listTotal.typeAllCnt) %}</span> 건</div>

                            <div :class="'font-14'" >
                                검색매출 <span class="text-danger">{% listTotal.type1Cnt %}원</span>
                            </div>
                            <div :class="'font-14'" >
                                검색생산가 <span class="sl-blue">{% listTotal.type2Cnt %}</span>
                                <span class="font-11">(마진:{% listTotal.type4Cnt %}% / {% listTotal.type3Cnt %}원)</span>
                            </div>
                        </div>

                        <div class="dp-flex dp-flex-gap10 font-16 mgt5 ">
                            <div class="dp-flex" >
                                <div :class="'btn btn-lg ' + ('project' === searchCondition.viewType ? 'font-bold btn-gray':'btn-white')" @click="searchCondition.viewType='project';refreshList(1)">
                                    프로젝트별
                                </div>
                                <div :class="'btn btn-lg ' + ('style' === searchCondition.viewType ? 'font-bold btn-gray':'btn-white')" @click="searchCondition.viewType='style';refreshList(1)">
                                    스타일별
                                </div>
                            </div>

                            <span class="mgl5 dp-flex">
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('y', 'book')">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    회계반영
                                </div>
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('n', 'book')">
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i> 회계반영 취소
                                </div>
                            </span>

                            <span class="mgl15 dp-flex">
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('y', 'work')">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    작지정제 완료
                                </div>
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('n', 'work')">
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i> 작지정제 완료 취소
                                </div>
                            </span>

                            <span class="mgl15 dp-flex">
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('y', 'stock')">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    수량확인 완료
                                </div>
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('n', 'stock')">
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i> 수량확인 완료 취소
                                </div>
                            </span>
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
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                        </colgroup>
                        <tr>
                            <th rowspan="2">
                                <!--<input type='checkbox' value='y' class='js-checkall' data-target-name='sno' />-->
                                <!--<input type="checkbox" name="sno[]" :value="project.sno" class="list-check" v-model="projectCheckList">-->
                                <input type='checkbox' value='y' class='js-checkall' data-target-name='sno' @click="toggleAllCheck()" v-model="listAllCheck"  />
                            </th>
                            <th rowspan="2" >번호</th>

                            <th v-for="fieldData in searchData.fieldData" v-if="true != fieldData.subRow"  >
                                <div :class="['assort', 'prdPriceApproval', 'prdCostApproval'].includes(fieldData.name)?'font-9':''">
                                    {% fieldData.title %}
                                </div>
                            </th>
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
                            <td :rowspan="2">
                                <!--<input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >-->
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" v-model="projectCheckList">
                            </td>
                            <td :rowspan="2">
                                <div>{% (listTotal.idx-index) %}</div>
                                <div>
                                    <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                        <span class="text-muted cursor-pointer hover-btn font-10 mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                                        </span>
                                    <?php } ?>
                                </div>
                            </td>

                            <td v-for="fieldData in searchData.fieldData"
                                :rowspan="true == fieldData.rowspan || !$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?2:1"
                                v-if="true !== fieldData.subRow"
                                :class="fieldData.class + ''" :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''">

                                <?php include 'nlist/list_template.php'?>

                                <div v-if="'c' === fieldData.type" class="pd0">
                                    <!--디자인 리스트 프로젝트 정보-->
                                    <div v-if="'projectNo' === fieldData.name" class="text-left pdl5 relative font-13">
                                        <div >
                                            <div class="dp-flex dp-flex-gap5 cursor-pointer hover-btn" @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}`)">
                                                <span class="text-danger">{% each.sno %}</span>
                                                <span class="sl-blue">{% each.customerName %}</span>
                                            </div>
                                            <div>
                                            <span class="cursor-pointer hover-btn font-12" @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}`)">
                                                {% each.projectYear %} {% each.projectSeason %}
                                                {% each.productName %}
                                            </span>
                                            </div>
                                        </div>

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
                                        <?php include 'template/basic_view/_production_order.php'?>
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

                                    <div v-if="'isBookRegistered' === fieldData.name ">
                                        <div v-if="'y' === each.isBookRegistered">
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                            <div>
                                                {% $.formatShortDate(each.isBookRegisteredDt) %}
                                            </div>
                                        </div>
                                        <div v-if="'n' === each.isBookRegistered">
                                            <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i>
                                        </div>
                                    </div>

                                    <div v-if="'refineOrder' === fieldData.name ">
                                        <div v-if="'y' === each.refineOrder">
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                            <div>
                                                {% $.formatShortDate(each.refineOrderDt) %}
                                            </div>
                                        </div>
                                        <div v-if="'n' === each.refineOrder">
                                            <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i>
                                        </div>
                                    </div>

                                    <div v-if="'confirmStock' === fieldData.name ">
                                        <div v-if="'y' === each.confirmStock">
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                            <div>
                                                {% $.formatShortDate(each.confirmStockDt) %}
                                            </div>
                                        </div>
                                        <div v-if="'n' === each.confirmStock">
                                            <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i>
                                        </div>
                                    </div>

                                    <!-- TODO 메모-->
                                    <div v-if="'projectMemo' === fieldData.name">
                                        <div class="btn btn-sm btn-white">메모</div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <!--완료일-->
                        <tr >
                            <td v-for="fieldData in searchData.fieldData" v-if="true == fieldData.subRow && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) " :class="fieldData.class">
                                <!--타입-->
                                <div v-if="'c' === fieldData.type && 'subTitle' === fieldData.name">
                                    상태
                                </div>
                                <div v-if="'subTitle' !== fieldData.name " class="font-11">
                                    <span v-html="$.getProjectScheduleIcon(each['st'+$.ucfirst(fieldData.name)])"></span>
                                    {% $.formatShortDateWithoutWeek(each['cp'+$.ucfirst(fieldData.name)]) %}
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="" v-if="'style' === searchCondition.viewType">
                    <?php include 'ims_list_all_style.php'?>
                </div>

                <div id="all-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>
    <?php include 'nlist/_emergency_layer_popup.php'?>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_finish_script.php'?>

<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; " id="affix-menu">
    <section id="affix-show-type1">
        <h3 class="relative">
            프로젝트 리스트
            <div class="btn btn-sm btn-gray font-13 mgl10 hover-btn" style="padding-top:7px; background-color:#5b5b5b">전체</div>
            <div class="btn btn-sm btn-gray font-13 hover-btn" style="padding-top:7px;background-color:#5b5b5b"">영업</div>
            <div class="btn btn-sm btn-gray font-13 hover-btn" style="padding-top:7px;background-color:#5b5b5b"">디자인</div>
            <div class="btn btn-sm btn-gray font-13 hover-btn" style="padding-top:7px;background-color:#5b5b5b"">생산</div>
            
            <div class="btn btn-sm btn-gray font-13 hover-btn" style="padding-top:7px;background-color:#5b5b5b"">리오더</div>
            <div class="btn btn-sm btn-gray font-13 hover-btn" style="padding-top:7px;background-color:#5b5b5b"">기성복</div>
        </h3>
        <div class="btn-group" style="margin-top:-50px" >
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" id="btn-reg-project" />
        </div>
    </section>
    <!--틀고정-->
    <section id="affix-show-type2" style="margin:0 !important; display: none "></section>
</div>

<section id="imsApp" class="project-view">
    <div v-show="false" style="display:none" @click="openCommonPopup('project_reg', 900, 835, {})" id="btn-reg-project-hide"></div>

    <div class="row">
        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0 table-pd-5 table-pdl-7 border-top-none ">
                        <colgroup>
                            <col class="w-7p">
                            <col class="w-34p">
                            <col class="w-6p">
                            <col class="w-20p">
                            <col class="w-6p">
                            <col class="w-20p">
                            <col class="w-7p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="2" class="text-center">
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
                            <th class="font-12">프로젝트타입</th>
                            <td colspan="3">
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
                            <td rowspan="4" class="">
                                <div class="btn btn-lg btn-black w-100p" style="height:75px;padding-top:30px" @click="refreshList(1)">검색</div>
                                <div class="btn btn-white mgt5 w-100p" @click="conditionReset()">초기화</div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                진행상태
                            </th>
                            <td colspan="3">
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline " style="width:115px">
                                            <input type="checkbox" name="orderProgressChk[]" value="all" class="js-not-checkall" data-target-name="orderProgressChk[]"
                                                   :checked="0 >= searchCondition.orderProgressChk.length?'checked':''" @click="searchCondition.orderProgressChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS_ALL_MAP as $k => $v){ ?>
                                            <label class="" style="width:115px">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressChk[]" value="<?=$k?>"  v-model="searchCondition.orderProgressChk"  >
                                                <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>지연/미확정</th>
                            <td>
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="delayStatus[]" value="all" class="js-not-checkall" data-target-name="delayStatus[]"
                                           :checked="0 >= searchCondition.delayStatus.length?'checked':''" @click="searchCondition.delayStatus=[];refreshList(1)"> 전체
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="1"
                                           v-model="searchCondition.delayStatus"  >
                                        <span class="text-danger">
                                            <i aria-hidden="true" class="fa fa-exclamation-triangle"></i>일정 지연
                                        </span>
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="2"
                                           v-model="searchCondition.delayStatus"  > 생산가 미확정
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="3"
                                           v-model="searchCondition.delayStatus"  > 판매가 미확정
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="4"
                                           v-model="searchCondition.delayStatus"  > 아소트 미확정
                                </label>

                            </td>
                            <th>
                                사업계획
                            </th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="bizPlanYn" value="all" v-model="searchCondition.bizPlanYn"  />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bizPlanYn" value="y" v-model="searchCondition.bizPlanYn"/>포함
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="bizPlanYn" value="n" v-model="searchCondition.bizPlanYn" />미포함
                                </label>
                            </td>
                            <th>회계 반영</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="isBookRegistered" value="0" v-model="searchCondition.isBookRegistered"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isBookRegistered" value="y" v-model="searchCondition.isBookRegistered"/>회계반영
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isBookRegistered" value="n" v-model="searchCondition.isBookRegistered"/>회계미반영
                                </label>
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
                            </td>
                            <th>
                                업무타입
                            </th>
                            <td colspan="3" class="">
                                <div class="checkbox ">
                                    <div>
                                        <label class="radio-inline ">
                                            <input type="radio" name="designWorkType" value="all" v-model="searchCondition.designWorkType"  />전체
                                        </label>
                                        <?php foreach(\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $designWorkKey => $designWorkName){ ?>
                                            <?php if(empty($designWorkKey)) continue;?>
                                            <label class="radio-inline">
                                                <input type="radio" name="designWorkType" value="<?=$designWorkKey?>" v-model="searchCondition.designWorkType" /><?=$designWorkName?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                검색 기간
                            </th>
                            <td colspan="99">
                                <div class="dp-flex">
                                    <select class="form-control mgr3" style="height:26px">
                                        <option>등록일</option>
                                        <option>미팅 예정일</option>
                                        <option>기획 예정일</option>
                                        <option>제안 예정일</option>
                                    </select>

                                    <div>
                                        <date-picker vv-model="productionSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="width:140px;font-weight: normal"></date-picker>
                                    </div>
                                    <div class="pd20 font-18">&nbsp;&nbsp;&nbsp;~</div>
                                    <div>
                                        <date-picker vv-model="productionSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="width:140px;font-weight: normal;margin-left:10px"></date-picker>
                                    </div>

                                    <div class="form-inline" style="margin-left:30px">
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
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
                    </div>
                    <div class="mgb5">
                        <div class="" style="display: flex;padding-top:20px">

                            <span class="mgr15 font-16">
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
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                        </colgroup>
                        <tr>
                            <th rowspan="2"><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
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
                        <tbody v-for="(each , index) in listData">
                        <!--예정일-->
                        <tr  >
                            <!--:rowspan="each.projectRowspan" v-if="each.projectRowspan > 0"-->
                            <td :rowspan="2">
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                            </td>
                            <!--:rowspan="each.projectRowspan" v-if="each.projectRowspan > 0"-->
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
                            <!--예정일 || !$.isEmpty(each[fieldData.name+'AlterText']) -->
                            <td v-for="fieldData in searchData.fieldData" :rowspan="true == fieldData.rowspan?2:1" v-if="true !== fieldData.subRow " :class="fieldData.class + ''">

                                <?php include 'nlist/list_template.php'?>

                                <div v-if="'c' === fieldData.type" class="pd0">
                                    <!--디자인 리스트 프로젝트 정보-->
                                    <div v-if="'projectNo' === fieldData.name" class="text-left pdl5 relative font-13">
                                        <div >
                                            <div class="dp-flex dp-flex-gap5 cursor-pointer hover-btn" @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}&status=${each.salesStatus}`)">
                                                <span class="text-danger">{% each.sno %}</span>
                                                <span class="sl-blue">{% each.customerName %}</span>
                                            </div>
                                            <div>
                                            <span class="cursor-pointer hover-btn font-12" @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}&status=${each.salesStatus}`)">
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
                                        <div class="font-bold">{% each.projectTypeKr %}</div>

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

                                    <!--QB-->
                                    <div v-if="'qb' === fieldData.name">
                                        <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.fabricStatus"></i>
                                        <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.fabricStatus"></i>
                                        <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.fabricStatus"></i>
                                    </div>
                                    <!--아소트-->
                                    <div v-if="'assort' === fieldData.name">
                                        <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="'r' == each.assortApproval"></i>
                                        <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-else-if="'p' == each.assortApproval"></i>
                                        <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-else="'n'==each.assortApproval"></i>
                                    </div>
                                    <!--판매가-->
                                    <div v-if="'prdPriceApproval' === fieldData.name">
                                        <div class="">
                                            <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.priceStatus"></i>
                                            <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.priceStatus"></i>
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.priceStatus"></i>
                                        </div>
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
                                    <div v-if="'subTitle' === fieldData.name">
                                        예정일
                                    </div>

                                    <!--상태-->
                                    <div v-if="'projectStatusKr' === fieldData.name" class="font-12">
                                        <div :class="'round-box dp-flex ims-status ims-status' + each.projectStatus" style="min-height:45px;justify-content: center; align-items: center; padding-left:13px; padding-right:13px">
                                            {% each.projectStatusKr %}
                                        </div>
                                    </div>

                                    <div v-if="'addService' === fieldData.name">
                                        <div class="div-info-list">
                                            <div class="info-item">
                                                <div class="label">폐쇄몰:</div>
                                                <div :class="'value ' + ('y'==each.useMall?'text-green bold':'text-muted')">{% each.useMallKr %}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="label">3PL:</div>
                                                <div :class="'value ' + ('y'==each.use3pl?'text-green bold':'text-muted')">{% each.use3plKr %}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="label">분류패킹:</div>
                                                <div :class="'value ' + ('y'==each.packingYn?'text-green bold':'text-muted')">{% each.packingYnKr %}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="label">직접납품:</div>
                                                <div class="value text-muted">미확인</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="'managerName' === fieldData.name">
                                        <div class="font-11" v-if="!$.isEmpty(each.salesManagerNm)">영 : {% each.salesManagerNm %}</div>
                                        <span class="text-muted font-11" v-if="$.isEmpty(each.salesManagerNm)">영 : 미지정</span>
                                        <div class="font-11" v-if="!$.isEmpty(each.designManagerNm)">디 : {% each.designManagerNm %}</div>
                                        <span class="text-muted font-11" v-if="$.isEmpty(each.designManagerNm)">디 : 미지정</span>
                                    </div>

                                    <div v-if="'projectMemo' === fieldData.name">
                                        <div class="btn btn-sm btn-white">메모</div>
                                    </div>

                                    <!--매출정보-->
                                    <div v-if="'salesInfo' === fieldData.name" class="text-left font-11">
                                        <div>매출: {% each.totalPrdPriceKr %}</div>
                                        <div>마진: {% each.totalMarginKr %}</div>
                                        <div>마진%: {% each.totalMarginPercent %}%</div>
                                    </div>

                                </div>

                            </td>
                        </tr>
                        <!--완료일-->
                        <tr>
                            <td v-for="fieldData in searchData.fieldData" v-if="true == fieldData.subRow" :class="fieldData.class">
                                <!--타입-->
                                <div v-if="'c' === fieldData.type && 'subTitle' === fieldData.name">
                                    상태
                                </div>
                                <?php include 'nlist/list_template.php'?>
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

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_all_script.php'?>

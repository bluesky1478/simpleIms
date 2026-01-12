<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; height:42px !important;" id="affix-menu">
    <section id="affix-show-type1">
        <h3 >기획/제작 리스트 <span class="font-11 font-normal">(기획 제안 및 발주 준비 처리 까지 단계)</span></h3>
        <div class="btn-group">
            <!--
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" @click="location.href='./ims_project_reg.php?status=<?=$requestParam['status']?>';" />
            -->
        </div>
    </section>
    <!--틀고정-->
    <section id="affix-show-type2" style="margin:0 !important; display: none "></section>
</div>

<section id="imsApp" class="project-view">
    <!--검색-->
    <div class="row">
        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0 border-top-none ">
                        <colgroup>
                            <col class="w-6p">
                            <col class="w-30p">
                            <col class="w-6p">
                            <col class="w-50p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="3" class="text-center">
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
                                프로젝트 타입
                            </th>
                            <td>
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
                        <tr>
                            <th>
                                업무타입
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
                            <th>
                                진행상태
                            </th>
                            <td >
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="orderProgressChk[]" value="all" class="js-not-checkall" data-target-name="orderProgressChk[]"
                                                   :checked="0 >= searchCondition.orderProgressChk.length?'checked':''" @click="searchCondition.orderProgressChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::DESIGN_STATUS as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressChk[]" value="<?=$k?>"  v-model="searchCondition.orderProgressChk"  > <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="" rowspan="2">
                                담당
                            </th>
                            <td rowspan="2">
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
                                    <div class="btn btn-white btn-sm" @click="setDesigner(0)">미지정</div>
                                    <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                        <div class="btn btn-white btn-sm" @click="setDesigner('<?=$desingerSno?>')"><?=$designer?></div>
                                    <?php } ?>
                                </div>
                            </td>
                            <th>지연/미확정</th>
                            <td>
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="delayStatus[]" value="all" class="js-not-checkall" data-target-name="delayStatus[]"
                                           :checked="0 >= searchCondition.delayStatus.length?'checked':''" @click="searchCondition.delayStatus=[];refreshList(1)"> 전체
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="8"
                                           v-model="searchCondition.delayStatus"  @change="refreshList(1)"> <span class="text-danger">일정 지연</span>
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

                            </td>
                        </tr>
                        <tr>
                            <th>고객 대기상태</th>
                            <td>
                                <div class="btn btn-white btn-sm" @click="setWait(1)">전체 대기건 확인</div>
                                <div class="btn btn-white btn-sm" @click="setWait(2)">제안서 확정대기 건</div>
                                <div class="btn btn-white btn-sm" @click="setWait(3)">샘플 확정 대기 건</div>
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

                            <div class="total hover-btn cursor-pointer" @click="searchCondition.projectStatus='';refreshList(1)">검색 <span class="text-danger">{% $.setNumberFormat(listTotal.typeAllCnt) %}</span> 건</div>

                            <div :class="'hover-btn cursor-pointer font-14'" @click="setProgress(['20'])">
                                기획(<span class="text-danger">{% $.setNumberFormat(listTotal.type1Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer  font-14'" @click="setProgress(['30','31'])">
                                제안(<span class="text-danger">{% $.setNumberFormat(listTotal.type2Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer font-14'"  @click="setProgress(['40','41'])">
                                샘플(<span class="text-danger">{% $.setNumberFormat(listTotal.type3Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer font-14'" @click="setProgress(['50'])">
                                발주준비(<span class="text-danger">{% $.setNumberFormat(listTotal.type4Cnt) %}</span>)
                            </div>

                        </div>

                    </div>
                    <div class="mgb5">
                        <div class="" style="display: flex;padding-top:20px">
                            <input type="button" value="일괄수정" class="btn btn-white btn-red btn-red-line2" @click="vueApp.isModify = true;" v-show="!isModify">
                            <input type="button" value="저장" class="btn btn-red" @click="save()" v-show="isModify">&nbsp;
                            <input type="button" value="일괄수정취소" class="btn btn-red btn-red-line2" @click="vueApp.isModify = false;" v-show="isModify">

                            <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort" >
                                <option value="P3,asc">고객납기일 ▲</option>
                                <option value="P3,desc">고객납기일 ▼</option>

                                <option value="P7,asc">발주D/L ▲</option>
                                <option value="P7,desc">발주D/L ▼</option>

                                <option value="P1,asc">등록일 ▲</option>
                                <option value="P1,desc">등록일 ▼</option>
                                <option value="P5,asc">진행상태 ▲</option>
                                <option value="P5,desc">진행상태 ▼</option>
                                <!--
                                <option value="P2,asc">연도/등록일 ▲</option>
                                <option value="P2,desc">연도/등록일 ▼</option>
                                <option value="P4,asc">매출규모 ▲</option>
                                <option value="P4,desc">매출규모 ▼</option>
                                -->
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
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                        </colgroup>

                        <tr>
                            <th ><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
                            <th  >번호</th>
                            <th >타입</th>
                            <th >프로젝트</th>
                            <th >고객납기</th>
                            <th >발주D/L</th>
                            <th >구분</th>
                            <th v-for="fieldData in searchData.fieldData"  :colspan="$.isEmpty(fieldData.colspan)?'1':fieldData.colspan"
                                v-if="true != fieldData.skip && true == fieldData.subRow && 'subTitle' != fieldData.name" >
                                {% fieldData.title %}
                            </th>
                            <th >Q</th>
                            <th >B</th>
                            <th >담당정보</th>
                            <th >상태</th>
                        </tr>
                        <tbody v-if="0 >= listData.length">
                            <tr>
                                <td colspan="99">
                                    데이터가 없습니다.
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-for="(each , index) in listData" class="hover-light">
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
                                        <span class="text-muted cursor-pointer hover-btn mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                                        </span>
                                    <?php } ?>
                                </div>
                            </td>
                            <!--예정일 || !$.isEmpty(each[fieldData.name+'AlterText']) -->
                            <td v-for="fieldData in searchData.fieldData"
                                :rowspan="true == fieldData.rowspan || !$.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) || 9 == each['st'+$.ucfirst(fieldData.name)] ?2:1"
                                v-if="true !== fieldData.subRow " :class="fieldData.class + ''" :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''">

                                <?php include 'nlist/list_template.php'?>

                                <div v-if="'c' === fieldData.type" class="pd0">
                                    <!--디자인 리스트 프로젝트 정보-->
                                    <div v-if="'projectNo' === fieldData.name" class="text-left pdl5 relative font-13">
                                        <div >
                                            <div class="dp-flex dp-flex-gap5 cursor-pointer hover-btn" @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}&current=design`)">
                                                <span class="text-danger">{% each.sno %}</span>
                                                <span class="sl-blue">{% each.customerName %}</span>
                                                <span class="text-muted font-11" v-if="!$.isEmpty(each.salesManagerNm)">{% each.salesManagerNm %}</span>
                                                <span class="text-muted font-11" v-if="!$.isEmpty(each.designManagerNm)">/ {% each.designManagerNm %}</span>
                                            </div>
                                            <div>
                                            <span class="cursor-pointer hover-btn font-12" @click="$.cookie('viewTabMode', '');window.open(`ims_view2.php?sno=${each.projectSno}&current=design`)">
                                                {% each.projectYear %} {% each.projectSeason %}
                                                {% each.productName %} <span class="font-10 text-muted">({% $.setNumberFormat(each.totalQty) %}ea)</span>
                                            </span>
                                            </div>
                                        </div>

                                        <div class="sl-badge-small sl-badge-small-blue mgl5 mgb3" style="position: absolute;top:-5px;right:0" v-if="'y' === each.bizPlanYn">
                                            사업계획
                                        </div>
                                    </div>

                                    <!--타입-->
                                    <div v-if="'projectType' === fieldData.name">
                                        <div class="font-11">{% each.projectTypeKr %}</div>

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

                                    <!--퀄리티-->
                                    <div v-if="'fabricStatus' === fieldData.name">
                                        <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.fabricStatus"></i>
                                        <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.fabricStatus"></i>
                                        <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.fabricStatus"></i>
                                    </div>
                                    <!--BT-->
                                    <div v-if="'btStatus' === fieldData.name">
                                        <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.btStatus"></i>
                                        <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.btStatus"></i>
                                        <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.btStatus"></i>
                                    </div>
                                    <!--아소트-->
                                    <div v-if="'assort' === fieldData.name">
                                        <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="'r' == each.assortApproval"></i>
                                        <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-else-if="'p' == each.assortApproval"></i>
                                        <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-else></i>
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
                                        <!--기획-->
                                        <div v-if="20 == each.projectStatus" class="round-box bg-light-gray">{% each.projectStatusKr %}</div>
                                        <!--제안-->
                                        <div v-if="30 == each.projectStatus || 31 == each.projectStatus" class="round-box bg-light-red">{% each.projectStatusKr %}</div>
                                        <!--샘플-->
                                        <div v-if="40 == each.projectStatus || 41 == each.projectStatus" class="round-box bg-light-orange">{% each.projectStatusKr %}</div>
                                        <!--발주준비-->
                                        <div v-if="50 == each.projectStatus" class="round-box bg-light-blue">{% each.projectStatusKr %}</div>
                                        <!--완료-->
                                        <div v-if="60 == each.projectStatus || 90 == each.projectStatus  " class="round-box bg-light-green">{% each.projectStatusKr %}</div>
                                    </div>


                                    <!--담당자-->
                                    <div v-if="'managerInfo' === fieldData.name">
                                        <div>영:한동경 / 디:정슬기</div>
                                        <div>
                                            샘플지시서:유수희
                                        </div>
                                        <div>
                                            생산가확정:이나라
                                        </div>
                                    </div>

                                </div>

                            </td>
                        </tr>
                        <!--완료일-->
                        <tr >
                            <td v-for="fieldData in searchData.fieldData"
                                v-if="true == fieldData.subRow && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) && 9 != each['st'+$.ucfirst(fieldData.name)] " :class="fieldData.class">
                                <!--타입-->
                                <div v-if="'c' === fieldData.type && 'subTitle' === fieldData.name">
                                    상태
                                </div>
                                <div v-else>
                                    <div v-if="!isModify
                                    || $.isEmpty(listUpdateMulti)
                                    || listUpdateMulti[index]['cp'+$.ucfirst(fieldData.name)] === undefined
                                    || ['plan','proposal','order','productionOrder'].includes(fieldData.name) === true
                                    ">
                                        <div v-if="'subTitle' !== fieldData.name " class="font-11">
                                            <span v-html="$.getProjectScheduleIcon(each['st'+$.ucfirst(fieldData.name)])"></span>
                                            {% $.formatShortDateWithoutWeek(each['cp'+$.ucfirst(fieldData.name)]) %}
                                        </div>
                                    </div>
                                    <div v-else style="max-width:100px;">
                                        <date-picker v-model="listUpdateMulti[index]['cp'+$.ucfirst(fieldData.name)]" class="mini-picker" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="완료일"></date-picker>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="design-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>
    <?php include 'nlist/_emergency_layer_popup.php'?>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_design_script.php'?>

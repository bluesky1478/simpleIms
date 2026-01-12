<style>
    .page-header { margin-bottom:10px };
    /*.mx-input {padding:0 !important; font-size:11px !important;}*/
    .mx-input-wrapper { width:100%!important; }

</style>

<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<div id="imsApp" class="project-view" >

    <div id="move-gnb"></div>

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-danger" >
                    {% project.sno %}
                </span>

                <span  >
                    {% project.projectYear %}
                    {% project.projectSeason %}
                </span> 프로젝트 상세정보
            </h3>
            <div class="btn-group">
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <!--탭화면-->
    <div id="tabViewDiv">
        <ul class="nav nav-tabs mgb15" role="tablist">
            <?php if(!$imsProduceCompany) {?>
                <li role="presentation" :class="'design' === tabMode?'active':''" @click="changeTab('design')" id="tab2" >
                    <a href="#tab2" data-toggle="tab" >프로젝트 정보</a>
                </li>
                <li role="presentation" :class="'customer' === tabMode?'active':''" @click="changeTab('customer')" id="tab4">
                    <a href="#tab4" data-toggle="tab" >고객 코멘트</a>
                </li>
            <?php }?>
        </ul>
    </div>

    <div class="row" v-if="!$.isEmpty(project.regDt)">
        <!--프로젝트 정보-->
        <div class="row" v-show="'design' === tabMode" >
            <div class="col-xs-12" >
                <div class="col-xs-12" >
                    <div >
                        <div class="table-title gd-help-manual">
                            <div class="flo-left area-title ">
                                <div class="godo dp-flex dp-flex-gap10">
                                    현재 프로젝트 상태 :
                                    <div class="sl-blue dp-flex dp-flex-gap5">
                                        {% project.projectStatusKr %} 단계
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <table class="table ims-schedule-table w100 table-default-center table-fixed table-td-height35 table-th-height35 mgb10 table-pd-3 font-14">
                                <colgroup>
                                    <col class="w-14p">
                                    <col class="w-14p">
                                    <col class="w-14p">
                                    <col class="w-14p">
                                    <col class="w-14p">
                                    <col class="w-14p">
                                    <col class="w-14p">
                                </colgroup>
                                <tr>
                                    <th>연도/시즌</th>
                                    <th>사업계획</th>
                                    <th>프로젝트 타입</th>
                                    <th>입찰 구분</th>
                                    <th>디자인업무 타입</th>
                                    <th>영업 담당자</th>
                                    <th>디자인 담당자</th>
                                </tr>
                                <!--보여주기-->
                                <tr>
                                    <td>
                                        {% project.projectYear %}/{% project.projectSeason %}
                                    </td>
                                    <td>
                                        {% getCodeMap()['includeType'][project.bizPlanYn] %}
                                    </td>
                                    <td>
                                        {% project.projectTypeKr %}
                                    </td>
                                    <td>
                                        <span class="">{% project.bidType2Kr %}</span>
                                        <span v-show="'single' !== project.bidType2" class="font-12">
                                            (예정일:{% $.formatShortDateWithoutWeek(project.exMeeting) %})
                                        </span>
                                    </td>
                                    <td>
                                        {% project.designWorkTypeKr %}
                                    </td>
                                    <td>
                                        <div v-show="$.isEmpty(project.salesManagerNm)">미정</div>
                                        <div v-show="!$.isEmpty(project.salesManagerNm)">{% project.salesManagerNm %}</div>
                                    </td>
                                    <td>
                                        <div v-show="$.isEmpty(project.designManagerNm)">미정</div>
                                        <div v-show="!$.isEmpty(project.designManagerNm)">{% project.designManagerNm %}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            프로젝트 등록일: {% project.regDt %} ({% project.regManagerNm %})
                        </div>
                    </div>
                </div>
            </div>


            <!--세부스케쥴 관리 html start-->
            <div v-if="aScheDetailListBySmallGrp[1].length > 0" class="col-xs-12" >
                <div class="col-xs-12" >
                    <div >
                        <div class="table-title gd-help-manual">
                            <div class="flo-left area-title ">
                                <span class="godo">
                                    세부스케쥴 관리
                                    <button type="button" class="btn btn-white" v-show="!isModifyScheDetail" @click="isModifyScheDetail=true">설정</button>
                                    <button type="button" class="btn btn-red" v-show="isModifyScheDetail" @click="saveScheDetail()">저장</button>
                                    <button type="button" class="btn" v-show="isModifyScheDetail" @click="isModifyScheDetail=false">취소</button>
                                </span>
                            </div>
                        </div>
                        <div>
                            <ul class="nav nav-tabs mgb0" role="tablist" ><!--제안서 이상 단계에서만 선택 가능-->
                                <li role="presentation" :class="'active'">
                                    <a href="#" data-toggle="tab"  @click="chgScheDetailTab(0)" >전체</a>
                                </li>
                                <li role="presentation" :class="''">
                                    <a href="#" data-toggle="tab"  @click="chgScheDetailTab(1)" >영업</a>
                                </li>
                                <li role="presentation" :class="''">
                                    <a href="#" data-toggle="tab"  @click="chgScheDetailTab(2)" >디자인(기획 제작)</a>
                                </li>
                                <li role="presentation" :class="''">
                                    <a href="#" data-toggle="tab"  @click="chgScheDetailTab(3)" >생산(발주)</a>
                                </li>
                            </ul>
                            <table class="table ims-schedule-table w100 table-default-center table-fixed table-td-height35 table-th-height35 mgb10 table-pd-3 font-14">
                                <colgroup>
                                    <col class="w-50px" /><col class="w-50px" />
                                    <col class="w-50px" v-show="iScheDetailTabNum == 0" /><col v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]"  class="w-50px" />
                                    <col class="w-50px" v-show="iScheDetailTabNum == 0" /><col v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]"  class="w-50px" />
                                    <col class="w-50px" v-show="iScheDetailTabNum == 0" /><col v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]"  class="w-50px" />
                                    <col class="w-50px" v-show="iScheDetailTabNum == 0" /><col v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]"  class="w-50px" />
                                    <col class="w-50px" v-show="iScheDetailTabNum == 0" /><col v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]"  class="w-50px" />
                                </colgroup>
                                <tr>
                                    <th>고객 납기</th><th>구분</th>
                                    <th v-show="iScheDetailTabNum == 0">영업 <button type="button" class="btn btn-white" v-show="aFlagFoldSmallType1" @click="aFlagFoldSmallType1 = false;">펼치기</button> <button type="button" class="btn" v-show="!aFlagFoldSmallType1" @click="aFlagFoldSmallType1=true">접기</button></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]">{% val.scheDetailName %}</td>
                                    <th v-show="iScheDetailTabNum == 0">기획 <button type="button" class="btn btn-white" v-show="aFlagFoldSmallType2" @click="aFlagFoldSmallType2 = false;">펼치기</button> <button type="button" class="btn" v-show="!aFlagFoldSmallType2" @click="aFlagFoldSmallType2=true">접기</button></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]">{% val.scheDetailName %}</td>
                                    <th v-show="iScheDetailTabNum == 0">제안서 <button type="button" class="btn btn-white" v-show="aFlagFoldSmallType3" @click="aFlagFoldSmallType3 = false;">펼치기</button> <button type="button" class="btn" v-show="!aFlagFoldSmallType3" @click="aFlagFoldSmallType3=true">접기</button></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]">{% val.scheDetailName %}</td>
                                    <th v-show="iScheDetailTabNum == 0">샘플 발송 <button type="button" class="btn btn-white" v-show="aFlagFoldSmallType4" @click="aFlagFoldSmallType4 = false;">펼치기</button> <button type="button" class="btn" v-show="!aFlagFoldSmallType4" @click="aFlagFoldSmallType4=true">접기</button></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]">{% val.scheDetailName %}</td>
                                    <th v-show="iScheDetailTabNum == 0">발주 <button type="button" class="btn btn-white" v-show="aFlagFoldSmallType5" @click="aFlagFoldSmallType5 = false;">펼치기</button> <button type="button" class="btn" v-show="!aFlagFoldSmallType5" @click="aFlagFoldSmallType5=true">접기</button></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]">{% val.scheDetailName %}</td>
                                </tr>
                                <tr>
                                    <td rowspan="5">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                            <br/><span class="font-11 " v-html="$.remainDate(project.customerDeliveryDt,true)"></span>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(project.customerDeliveryDt) %}
                                            <br/><span class="font-11 " v-html="$.remainDate(project.customerDeliveryDt,true)"></span>
                                        </span>
                                    </td>
                                    <th>업무 D/L</th>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[1].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].deadlineDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].deadlineDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.deadlineDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.deadlineDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[2].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].deadlineDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].deadlineDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.deadlineDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.deadlineDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[3].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].deadlineDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].deadlineDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.deadlineDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.deadlineDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[4].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].deadlineDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].deadlineDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.deadlineDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.deadlineDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[5].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].deadlineDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].deadlineDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.deadlineDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.deadlineDt) %}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>예정일</th>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[1].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].expectedDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].expectedDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.expectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.expectedDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[2].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].expectedDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].expectedDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.expectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.expectedDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[3].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].expectedDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].expectedDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.expectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.expectedDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[4].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].expectedDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].expectedDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.expectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.expectedDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[5].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].expectedDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].expectedDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.expectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.expectedDt) %}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>담당자</th>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[1].length > 0">{% aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].departName != '' ? aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].departName : aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].ownerManagerName %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]">
                                        <span v-if="isModifyScheDetail">
                                            <select @change="if(event.target.value == 1) { val.ownerManagerSno = <?=\Session::get('manager.sno')?>; val.departName = ''; } else { val.ownerManagerSno = 0; val.departName = '영업'; }" class="form-control">
                                                <option :selected="val.ownerManagerSno != 0" value="1">직원</option>
                                                <option :selected="val.departName != ''" value="2">부서</option>
                                            </select>
                                            <select v-show="val.departName != ''" v-model="val.departName" class="form-control">
                                                <option value="">없음</option>
                                                <option :selected="val.departName == '영업'" value="영업">영업</option>
                                                <option :selected="val.departName == '디자인'" value="디자인">디자인</option>
                                                <option :selected="val.departName == '생산'" value="생산">생산</option>
                                            </select>
                                            <select v-show="val.ownerManagerSno != 0" v-model="val.ownerManagerSno" @change="val.ownerManagerName=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                                <option value="">없음</option>
                                                <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$salesSno?>" value="<?=$salesSno?>">[영업]<?=$sales?></option>
                                                <?php } ?>
                                                <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$desingerSno?>" value="<?=$desingerSno?>">[디자인]<?=$designer?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <span v-else>
                                            {% val.departName == '' ? val.ownerManagerName : val.departName %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[2].length > 0">{% aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].departName != '' ? aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].departName : aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].ownerManagerName %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]">
                                        <span v-if="isModifyScheDetail">
                                            <select @change="if(event.target.value == 1) { val.ownerManagerSno = <?=\Session::get('manager.sno')?>; val.departName = ''; } else { val.ownerManagerSno = 0; val.departName = '영업'; }" class="form-control">
                                                <option :selected="val.ownerManagerSno != 0" value="1">직원</option>
                                                <option :selected="val.departName != ''" value="2">부서</option>
                                            </select>
                                            <select v-show="val.departName != ''" v-model="val.departName" class="form-control">
                                                <option value="">없음</option>
                                                <option :selected="val.departName == '영업'" value="영업">영업</option>
                                                <option :selected="val.departName == '디자인'" value="디자인">디자인</option>
                                                <option :selected="val.departName == '생산'" value="생산">생산</option>
                                            </select>
                                            <select v-show="val.ownerManagerSno != 0" v-model="val.ownerManagerSno" @change="val.ownerManagerName=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                                <option value="">없음</option>
                                                <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$salesSno?>" value="<?=$salesSno?>">[영업]<?=$sales?></option>
                                                <?php } ?>
                                                <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$desingerSno?>" value="<?=$desingerSno?>">[디자인]<?=$designer?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <span v-else>
                                            {% val.departName == '' ? val.ownerManagerName : val.departName %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[3].length > 0">{% aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].departName != '' ? aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].departName : aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].ownerManagerName %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]">
                                        <span v-if="isModifyScheDetail">
                                            <select @change="if(event.target.value == 1) { val.ownerManagerSno = <?=\Session::get('manager.sno')?>; val.departName = ''; } else { val.ownerManagerSno = 0; val.departName = '영업'; }" class="form-control">
                                                <option :selected="val.ownerManagerSno != 0" value="1">직원</option>
                                                <option :selected="val.departName != ''" value="2">부서</option>
                                            </select>
                                            <select v-show="val.departName != ''" v-model="val.departName" class="form-control">
                                                <option value="">없음</option>
                                                <option :selected="val.departName == '영업'" value="영업">영업</option>
                                                <option :selected="val.departName == '디자인'" value="디자인">디자인</option>
                                                <option :selected="val.departName == '생산'" value="생산">생산</option>
                                            </select>
                                            <select v-show="val.ownerManagerSno != 0" v-model="val.ownerManagerSno" @change="val.ownerManagerName=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                                <option value="">없음</option>
                                                <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$salesSno?>" value="<?=$salesSno?>">[영업]<?=$sales?></option>
                                                <?php } ?>
                                                <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$desingerSno?>" value="<?=$desingerSno?>">[디자인]<?=$designer?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <span v-else>
                                            {% val.departName == '' ? val.ownerManagerName : val.departName %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[4].length > 0">{% aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].departName != '' ? aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].departName : aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].ownerManagerName %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]">
                                        <span v-if="isModifyScheDetail">
                                            <select @change="if(event.target.value == 1) { val.ownerManagerSno = <?=\Session::get('manager.sno')?>; val.departName = ''; } else { val.ownerManagerSno = 0; val.departName = '영업'; }" class="form-control">
                                                <option :selected="val.ownerManagerSno != 0" value="1">직원</option>
                                                <option :selected="val.departName != ''" value="2">부서</option>
                                            </select>
                                            <select v-show="val.departName != ''" v-model="val.departName" class="form-control">
                                                <option value="">없음</option>
                                                <option :selected="val.departName == '영업'" value="영업">영업</option>
                                                <option :selected="val.departName == '디자인'" value="디자인">디자인</option>
                                                <option :selected="val.departName == '생산'" value="생산">생산</option>
                                            </select>
                                            <select v-show="val.ownerManagerSno != 0" v-model="val.ownerManagerSno" @change="val.ownerManagerName=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                                <option value="">없음</option>
                                                <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$salesSno?>" value="<?=$salesSno?>">[영업]<?=$sales?></option>
                                                <?php } ?>
                                                <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$desingerSno?>" value="<?=$desingerSno?>">[디자인]<?=$designer?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <span v-else>
                                            {% val.departName == '' ? val.ownerManagerName : val.departName %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[5].length > 0">{% aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].departName != '' ? aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].departName : aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].ownerManagerName %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]">
                                        <span v-if="isModifyScheDetail">
                                            <select @change="if(event.target.value == 1) { val.ownerManagerSno = <?=\Session::get('manager.sno')?>; val.departName = ''; } else { val.ownerManagerSno = 0; val.departName = '영업'; }" class="form-control">
                                                <option :selected="val.ownerManagerSno != 0" value="1">직원</option>
                                                <option :selected="val.departName != ''" value="2">부서</option>
                                            </select>
                                            <select v-show="val.departName != ''" v-model="val.departName" class="form-control">
                                                <option value="">없음</option>
                                                <option :selected="val.departName == '영업'" value="영업">영업</option>
                                                <option :selected="val.departName == '디자인'" value="디자인">디자인</option>
                                                <option :selected="val.departName == '생산'" value="생산">생산</option>
                                            </select>
                                            <select v-show="val.ownerManagerSno != 0" v-model="val.ownerManagerSno" @change="val.ownerManagerName=event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                                                <option value="">없음</option>
                                                <?php foreach($salesManagerList as $salesSno => $sales) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$salesSno?>" value="<?=$salesSno?>">[영업]<?=$sales?></option>
                                                <?php } ?>
                                                <?php foreach($designManagerList as $desingerSno => $designer) { ?>
                                                    <option :selected="val.ownerManagerSno == <?=$desingerSno?>" value="<?=$desingerSno?>">[디자인]<?=$designer?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <span v-else>
                                            {% val.departName == '' ? val.ownerManagerName : val.departName %}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>완료일</th>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[1].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].completeDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].completeDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.completeDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.completeDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[2].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].completeDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].completeDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.completeDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.completeDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[3].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].completeDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].completeDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.completeDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.completeDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[4].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].completeDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].completeDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.completeDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.completeDt) %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[5].length > 0">{% $.formatShortDateWithoutWeek(aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].completeDt) %} <span class="font-11 " v-html="$.remainDate(aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].completeDt,true)"></span></th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]">
                                        <span v-if="isModifyScheDetail">
                                            <date-picker v-model="val.completeDt" value-type="format" format="YYYY-MM-DD" :editable="false" style="width:100%; overflow:hidden;"></date-picker>
                                        </span>
                                        <span v-else>
                                            {% $.formatShortDateWithoutWeek(val.completeDt) %}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>메모</th>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[1].length > 0">{% aScheDetailListBySmallGrp[1][aScheDetailListBySmallGrp[1].length - 1].scheMemo %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType1) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[1]">
                                        <span v-if="isModifyScheDetail">
                                            <input type="text" v-model="val.scheMemo" class="form-control" />
                                        </span>
                                        <span v-else>
                                            {% val.scheMemo %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[2].length > 0">{% aScheDetailListBySmallGrp[2][aScheDetailListBySmallGrp[2].length - 1].scheMemo %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType2) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[2]">
                                        <span v-if="isModifyScheDetail">
                                            <input type="text" v-model="val.scheMemo" class="form-control" />
                                        </span>
                                        <span v-else>
                                            {% val.scheMemo %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[3].length > 0">{% aScheDetailListBySmallGrp[3][aScheDetailListBySmallGrp[3].length - 1].scheMemo %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType3) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[3]">
                                        <span v-if="isModifyScheDetail">
                                            <input type="text" v-model="val.scheMemo" class="form-control" />
                                        </span>
                                        <span v-else>
                                            {% val.scheMemo %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[4].length > 0">{% aScheDetailListBySmallGrp[4][aScheDetailListBySmallGrp[4].length - 1].scheMemo %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType4) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[4]">
                                        <span v-if="isModifyScheDetail">
                                            <input type="text" v-model="val.scheMemo" class="form-control" />
                                        </span>
                                        <span v-else>
                                            {% val.scheMemo %}
                                        </span>
                                    </td>
                                    <th v-show="iScheDetailTabNum == 0" v-if="aScheDetailListBySmallGrp[5].length > 0">{% aScheDetailListBySmallGrp[5][aScheDetailListBySmallGrp[5].length - 1].scheMemo %}</th>
                                    <td v-show="(iScheDetailTabNum == 0 && !aFlagFoldSmallType5) || iScheDetailTabNum == val.grpSche" v-for="val in aScheDetailListBySmallGrp[5]">
                                        <span v-if="isModifyScheDetail">
                                            <input type="text" v-model="val.scheMemo" class="form-control" />
                                        </span>
                                        <span v-else>
                                            {% val.scheMemo %}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--세부스케쥴 관리 html end-->

        </div>
        <!--고객 코멘트 (고객 정보)-->
        <div class="row" v-show="'customer' === tabMode">

        </div>
    </div>

    <div class="row ta-c font-20" v-if="$.isEmpty(project.regDt)">

        로딩 실패 새로고침 해서 다시 불러오세요.

    </div>

</div>

<script type="text/javascript">
    /**
     *  프로젝트 VIEW 메소드
     */
    const viewMethods = {
        //세부스케쥴 관리 method start
        chgScheDetailTab : (iNum)=>{
            if (iNum === 0) {
                vueApp.aFlagFoldSmallType1 = true;
                vueApp.aFlagFoldSmallType2 = true;
                vueApp.aFlagFoldSmallType3 = true;
                vueApp.aFlagFoldSmallType4 = true;
                vueApp.aFlagFoldSmallType5 = true;
            }
            vueApp.iScheDetailTabNum = iNum;
        },
        saveScheDetail : ()=>{
            let aoParam = [];
            $.each(vueApp.aScheDetailListBySmallGrp, function(key, val) {
                $.each(val, function(key2, val2) {
                    aoParam.push(this);
                });
            });
            $.imsPost('modifyProjectScheDetail', {'data':aoParam, 'customerDeliveryDt':vueApp.project.customerDeliveryDt}).then((data) => {
                $.imsPostAfter(data,(data)=>{
                    vueApp.isModifyScheDetail = false;
                });
            });
        },
        //세부스케쥴 관리 method end
    };
</script>
<script type="text/javascript">
    const sno = '<?=gd_isset($requestParam['sno'],$requestParam['projectSno'])?>';
    $(appId).hide();
    $(()=>{
        const serviceData = {
            //세부스케쥴 관리 watch start
            serviceWatch : {
                'project.customerDeliveryDt'(val, pre) { //고객 납기 변경하면 세부스케쥴의 D/L, 예정일 변경
                    if (vueApp.isModifyScheDetail === true ) {
                        let sDeliveryDt = val;
                        let oDt = new Date(sDeliveryDt);
                        let sChgDt = '';
                        $.each(vueApp.aScheDetailListBySmallGrp, function(key, val) {
                            $.each(val, function(key2, val2) {
                                oDt = new Date(sDeliveryDt);
                                oDt.setDate(oDt.getDate() - Number(this.extectedCompleteDay));
                                sChgDt = oDt.toISOString().slice(0, 10);
                                this.deadlineDt = sChgDt;
                                this.expectedDt = sChgDt;
                            });
                        });
                    }
                },
            }
            //세부스케쥴 관리 watch end
        };
        ImsBoneService.setData(serviceData,{
            scheduleLoad : false,
            <?php if('02001002' === $teamSno) { ?>
            isViewDetail : true,
            <?php }else{ ?>
            isViewDetail : false,
            <?php } ?>
            tabMode : 'design', //밑에서 들어오는 상태에 따라 분기한다.
            customer : {sno : -1},
            project  : {sno : -1},



            //세부스케쥴 vars start
            isModifyScheDetail : false,
            aScheDetailListBySmallGrp : [[],[],[],[],[],[]],
            aFlagFoldSmallType1 : true,
            aFlagFoldSmallType2 : true,
            aFlagFoldSmallType3 : true,
            aFlagFoldSmallType4 : true,
            aFlagFoldSmallType5 : true,
            iScheDetailTabNum : 0,
            //세부스케쥴 vars end
        });

        ImsBoneService.setMethod(serviceData,viewMethods);

        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            //세부스케쥴 start
            ImsNkService.getList('projectScheDetail',{sno : vueApp.project.sno}).then((data)=>{
                $.imsPostAfter(data, (data)=> {
                    $.each(data, function(key, val) {
                        vueApp.aScheDetailListBySmallGrp[this.grpSmallSche].push(this);
                    });
                });
            });
            //세부스케쥴 end
        });

        //시작.
        $.imsPost2('getSimpleProject',{sno:sno},(data)=>{
            ImsBoneService.serviceBeginCommon(serviceData,data);

            //프로젝트 정보
            vueApp.project = $.copyObject(data);
        });
    });
</script>

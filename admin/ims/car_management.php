<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<style>
    .ims-product-image .bootstrap-filestyle {display: table; width:80% ; float: left}
</style>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>업무 차량 관리</h3>
    </div>
    <div class="row" >
        <div class="col-xs-12">
            <div>
                <div>
                    <div class="table-title ">
                        차량정보
                        <?php if(\SiteLabUtil\SlCommonUtil::isDevId()){ ?>
                            <div class="btn-group" style="margin-left:10px;">
                                <input type="button" class="btn btn-red btn-reg hover-btn" value="차량 등록" @click="openUpsertCarModal(0);" />
                            </div>
                        <?php } ?>
                    </div>
                    <table v-if="aoCarList.length > 0" class="table table-rows table-default-center table-td-height30 mgt5 ">
                        <colgroup>
                            <col class="w-5p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in aoCarFlds" v-if="true != fieldData.skip" />
                            <col class="w-5p" />
                        </colgroup>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in aoCarFlds"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                            <th>수정</th>
                        </tr>
                        <tr v-if="aoCarList.length == 0">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tr v-for="(val , key) in aoCarList" class="hover-light">
                            <td >{% key+1 %}</td>
                            <td v-for="fieldData in aoCarFlds"  v-if="true != fieldData.skip" :class="fieldData.class">
                                <span v-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                                <span v-else-if="fieldData.type === 'img'">
                                    <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(val.carImage)" style="display: block; margin:0px auto;" >
                                    <img :src="val.carImage" v-show="!$.isEmpty(val.carImage)" style="width:100%;">
                                </span>
                                <span v-else-if="fieldData.type === 'btn'">
                                    <div class="dp-flex dp-flex-center dp-flex-gap5">
                                        <div @click="openUpsertDriveModal(0,val.sno)" class="btn btn-blue hover-btn cursor-pointer">운행등록</div>
                                        <div @click="openUpsertMaintainModal(0,val.sno)" class="btn btn-white hover-btn cursor-pointer">정비등록</div>
                                    </div>
                                </span>
                                <span v-else-if="fieldData.type === 'i'">
                                    {% $.setNumberFormat(val[fieldData.name]) %} {% (fieldData.appendChar != undefined && fieldData.appendChar != '') ? fieldData.appendChar : '' %}
                                </span>
                                <span v-else-if="fieldData.type === 'd3'">
                                    <span class="font-13">{% $.formatShortDateWithoutWeek(val[fieldData.name]) %}</span>
                                    <div class="font-11 mgt5" v-html="$.remainDate(val[fieldData.name],true)"></div>
                                </span>
                                <span v-else-if="fieldData.type === 'd2'">
                                    <span class="font-13">{% $.formatShortDateWithoutWeek(val[fieldData.name]) %}</span>
                                    <div class="font-11 mgt5" v-html="$.remainDate(val[fieldData.name],false)"></div>
                                    <div class="font-11 mgt5" v-if="'changeEODt' === fieldData.name ">{% $.setNumberFormat(val.totalDriveKm - val.currEODKm) %}Km 사용 중</div>
                                    <div class="font-11 mgt5" v-if="'changeTireDt' === fieldData.name">{% $.setNumberFormat(val.totalDriveKm - val.changeTireKm) %}Km 사용 중</div>
                                </span>
                                <span v-else-if="fieldData.type === 'd1'">
                                    <span class="font-13">{% $.formatShortDateWithoutWeek(val[fieldData.name]) %}</span>
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </td>
                            <td>
                                <span @click="openUpsertCarModal(val.sno)" class="btn btn-sm btn-white hover-btn cursor-pointer">수정</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="margin:10px 0;" class="relative">
                    <ul class="nav nav-tabs" role="tablist" style="margin:0">
                        <li :class="iTabMenuNum==1?'active':''" @click="changeTabmenu(1)">
                            <a href="#">운행 이력</a>
                        </li>
                        <li :class="iTabMenuNum==2?'active':''" @click="changeTabmenu(2)">
                            <a href="#">정비 이력</a>
                        </li>
                    </ul>
                    <div style="position: absolute;top:5px; right:0">
                        <input type="button" class="btn btn-white btn-reg hover-btn mgl15" value="주소지 관리" @click="$('#modalListAddr').modal('show');" style="margin-right:10px;" />
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
                                <th colspan="1">검색어</th>
                                <td colspan="3">
                                    <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                                        검색조건{% multiKeyIndex+1 %} :
                                        <span v-show="iTabMenuNum == 1">
                                        <?= gd_select_box('key', 'key', $search['combineSearch'][0], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                        </span>
                                        <span v-show="iTabMenuNum == 2">
                                        <?= gd_select_box('key', 'key', $search['combineSearch'][1], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                        </span>
                                        <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1); getListMaintain();" />
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
                                <th>차종</th>
                                <td colspan="3">
                                    <label class="radio-inline ">
                                        <input type="radio" name="sRadioSchCarSno" value="all" v-model="searchCondition.sRadioSchCarSno"  />전체
                                    </label>
                                    <label v-for="(val, key) in aoCarList" class="radio-inline">
                                        <input type="radio" name="sRadioSchCarSno" :value="val.sno" v-model="searchCondition.sRadioSchCarSno"/>{% val.carName %}
                                    </label>
                                </td>
                            </tr>
                            <tr v-show="iTabMenuNum == 2">
                                <th>정비구분</th>
                                <td colspan="3">
                                    <label class="radio-inline ">
                                        <input type="radio" name="sRadioSchMaintainType" value="all" v-model="searchCondition.sRadioSchMaintainType"  />전체
                                    </label>
                                    <?php foreach( \Component\Ims\NkCodeMap::ETC_CAR_MAINTAIN_TYPE as $k => $v){ ?>
                                        <label class="radio-inline">
                                            <input type="radio" name="sRadioSchMaintainType" value="<?=$k?>" v-model="searchCondition.sRadioSchMaintainType"/><?=$v?>
                                        </label>
                                    <?php } ?>

                                </td>
                            </tr>
                            <tr>
                                <th v-show="iTabMenuNum == 1">운행일자</th>
                                <td v-show="iTabMenuNum == 1">
                                    <date-picker v-model="searchCondition.sTextboxRangeStartSchDriveDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
                                    <span>~</span>
                                    <date-picker v-model="searchCondition.sTextboxRangeEndSchDriveDt" value-type="format" format="YYYY-MM-DD"  :editable="false" style="margin-left:50px;"></date-picker>
                                </td>
                                <th v-show="iTabMenuNum == 2">정비일자</th>
                                <td v-show="iTabMenuNum == 2">
                                    <date-picker v-model="searchCondition.sTextboxRangeStartSchMaintainDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
                                    <span>~</span>
                                    <date-picker v-model="searchCondition.sTextboxRangeEndSchMaintainDt" value-type="format" format="YYYY-MM-DD"  :editable="false" style="margin-left:50px;"></date-picker>
                                </td>
                                <th>등록일자</th>
                                <td>
                                    <date-picker v-model="searchCondition.sTextboxRangeStartSchRegDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
                                    <span>~</span>
                                    <date-picker v-model="searchCondition.sTextboxRangeEndSchRegDt" value-type="format" format="YYYY-MM-DD"  :editable="false" style="margin-left:50px;"></date-picker>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="99" class="ta-c" style="border-bottom: none">
                                    <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshList(1); getListMaintain();">
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
                                TOTAL
                                <span class="bold text-danger pdl5">
                                    <span v-show="iTabMenuNum == 1">
                                        {% $.setNumberFormat(listTotal.recode.total) %}
                                    </span>
                                    <span v-show="iTabMenuNum == 2">
                                        {% $.setNumberFormat(iCntTotalMaintain) %}
                                    </span>
                                </span> 건
                            </span>
                        </div>
                    </div>
                    <div class="flo-right mgb5">
                        <div class="" style="display: flex; ">
                            <button type="button" @click="listDownload()" class="btn btn-white btn-icon-excel simple-download">다운로드</button>
                            <select v-show="iTabMenuNum == 1" @change="refreshList(1);" class="form-control mgl5" v-model="searchCondition.sort">
                                <option value="D,asc">등록일자 ▲</option>
                                <option value="D,desc">등록일자 ▼</option>
                                <option value="CD,asc">운행일자 ▲</option>
                                <option value="CD,desc">운행일자 ▼</option>
                            </select>
                            <select v-show="iTabMenuNum == 2" @change="getListMaintain();" class="form-control mgl5" v-model="searchCondition.sort">
                                <option value="D,asc">등록일자 ▲</option>
                                <option value="D,desc">등록일자 ▼</option>
                                <option value="CM,asc">정비일자 ▲</option>
                                <option value="CM,desc">정비일자 ▼</option>
                            </select>
                            <select @change="refreshList(1); getListMaintain();" v-model="searchCondition.pageNum" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!--list start-->
                <div v-show="iTabMenuNum == 1">
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)">
                        <colgroup>
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip" />
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
                        <tr v-for="(val , key) in listData" class="hover-light">
                            <td >{% (listTotal.idx - key) %}</td>
                            <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                                <span v-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                                <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                                <span v-else-if="fieldData.type === 'date'">{% $.formatShortDate(val[fieldData.name]) %}</span>
                                <span v-else-if="fieldData.type === 'etc'">
                                    <span @click="openUpsertDriveModal(val.sno);" class="btn btn-sm btn-white hover-btn cursor-pointer">수정</span>
                                    <span @click="removeDrive(val.sno);" class="btn btn-sm btn-white hover-btn cursor-pointer">삭제</span>
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div v-show="iTabMenuNum == 2">
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(aoMaintainFlds)">
                        <colgroup>
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in aoMaintainFlds" v-if="true != fieldData.skip" />
                        </colgroup>
                        <tr>
                            <th >번호</th>
                            <th v-for="fieldData in aoMaintainFlds"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                        </tr>
                        <tr  v-if="0 >= aoMaintainList.length">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tr v-for="(val , key) in aoMaintainList" class="hover-light">
                            <td >{% (aoMaintainList.length - key) %}</td>
                            <td v-for="fieldData in aoMaintainFlds"  v-if="true != fieldData.skip" :class="fieldData.class">
                                <span v-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                                <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                                <span v-else-if="fieldData.type === 'date'">{% $.formatShortDate(val[fieldData.name]) %}</span>
                                <span v-else-if="fieldData.type === 'etc'">
                                    <span @click="openUpsertMaintainModal(val.sno);" class="btn btn-sm btn-white hover-btn cursor-pointer">수정</span>
                                    <span @click="removeMaintain(val.sno);" class="btn btn-sm btn-white hover-btn cursor-pointer">삭제</span>
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--list end-->
                <div id="car_management-page" v-html="pageHtml" class="ta-c"></div>
            </div>
            <div class="modal fade" id="modalUpsertCar" tabindex="-1" role="dialog"  aria-hidden="true" >
                <div class="modal-dialog" role="document" style="width:500px;">
                    <div class="modal-content" style="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                            <span class="modal-title font-18 bold" >차량 {% oUpsertFormCar.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}</span>
                        </div>
                        <div class="modal-body">
                            <table class="table table-cols table-pd-5" >
                                <colgroup>
                                    <col class="w-20p">
                                    <col>
                                </colgroup>
                                <tbody>
                                <tr v-show="false">
                                    <th>명의구분</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select v-model="oUpsertFormCar.carType" ref="carInfoCarTypeSelect" @change="changeCarType()" style="display: inline; width:150px;" class="form-control">
                                                <?php foreach (\Component\Ims\NkCodeMap::ETC_CAR_TYPE as $key => $val ) { ?>
                                                    <option value="<?=$key?>"><?=$val?></option>
                                                <?php } ?>
                                                <option value="">기타</option>
                                            </select>
                                            <input type="text" ref="carInfoCarType" v-model="oUpsertFormCar.carTypeEtc" style="display: none; width:100px;" class="form-control" />
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormCar.carType %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>차량 이미지</th>
                                    <td>
                                        <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(oUpsertFormCar.carImage)" style="display: block; margin:0px auto;" >
                                        <img :src="oUpsertFormCar.carImage" v-show="!$.isEmpty(oUpsertFormCar.carImage)" style="width:250px;">
                                        <div v-show="isModify" class="text-right ims-product-image">
                                            <form @submit.prevent="uploadCarImageFile">
                                                <input :type="'file'" ref="carImageElement" @change="uploadCarImageFile('carImage')" style="display: block;width:1px!important;" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>차종</th>
                                    <td>
                                        <?php $model='oUpsertFormCar.carName'; $placeholder='차종' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>차량번호</th>
                                    <td>
                                        <?php $model='oUpsertFormCar.carNumber'; $placeholder='차량번호' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>종합검사일자</th>
                                    <td>
                                        <?php $model='oUpsertFormCar.totalCheckDt'; $placeholder='종합검사일자' ?>
                                        <?php include './admin/ims/template/basic_view/_picker.php'?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer ">
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="!isModify" @click="isModify=true">수정하기</div>
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="saveCar()">저장</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" v-show="isModify && oUpsertFormCar.sno != 0" @click="isModify=false">수정취소</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalUpsertMaintain" tabindex="-1" role="dialog"  aria-hidden="true" >
                <div class="modal-dialog" role="document" style="width:500px;">
                    <div class="modal-content" style="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                            <span class="modal-title font-18 bold" >정비 {% oUpsertFormMaintain.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}</span>
                        </div>
                        <div class="modal-body">
                            <table class="table table-cols table-pd-5" >
                                <colgroup>
                                    <col class="w-20p">
                                    <col>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th>운행차량</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select2 v-model="oUpsertFormMaintain.carSno" class="form-control" style="width:100%;">
                                                <option v-if="aoCarList.length > 0" v-for="(val, key) in aoCarList" :value="val.sno">{% val.carName %}</option>
                                            </select2>
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormMaintain.carName %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>정비구분</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select v-model="oUpsertFormMaintain.maintainType" ref="carInfoMaintainTypeSelect" @change="changeMaintainType()" style="display: inline; width:150px;" class="form-control">
                                                <?php foreach (\Component\Ims\NkCodeMap::ETC_CAR_MAINTAIN_TYPE as $key => $val ) { ?>
                                                    <option value="<?=$key?>"><?=$val?></option>
                                                <?php } ?>
                                                <option value="">기타</option>
                                            </select>
                                            <input type="text" ref="carInfoMaintainType" v-model="oUpsertFormMaintain.maintainTypeEtc" style="display: none; width:100px;" class="form-control" />
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormMaintain.maintainTypeEtc %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>정비일자</th>
                                    <td>
                                        <?php $model='oUpsertFormMaintain.maintainDt'; $placeholder='정비일자' ?>
                                        <?php include './admin/ims/template/basic_view/_picker.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>정비자</th>
                                    <td>
                                        <?php $model='oUpsertFormMaintain.maintainUser'; $placeholder='직원명' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                <tr v-show="false">
                                    <th>정비금액</th>
                                    <td>
                                        <?php $model='oUpsertFormMaintain.maintainCost'; $placeholder='정비금액' ?>
                                        <?php include './admin/ims/template/basic_view/_number.php'?>
                                    </td>
                                </tr>
                                <tr >
                                    <th>정비당시 운행km(계기판)</th>
                                    <td>
                                        <?php $model='oUpsertFormMaintain.currKm'; $placeholder='운행km' ?>
                                        <?php include './admin/ims/template/basic_view/_number.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>메모</th>
                                    <td>
                                        <div v-show="!isModify" v-html="oUpsertFormMaintain.maintainMemo == null ? oUpsertFormMaintain.maintainMemo : oUpsertFormMaintain.maintainMemo.replaceAll('\n', '<br />')" style="padding: 10px;" class="ta-l"></div>
                                        <span v-show="isModify">
                                        <textarea class="form-control" rows="3" v-model="oUpsertFormMaintain.maintainMemo" placeholder="메모"></textarea>
                                    </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer ">
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="!isModify" @click="isModify=true">수정하기</div>
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="saveMaintain()">저장</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" v-show="isModify && oUpsertFormMaintain.sno != 0" @click="isModify=false">수정취소</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalUpsertDrive" tabindex="-1" role="dialog"  aria-hidden="true" >
                <div class="modal-dialog" role="document" style="width:500px;">
                    <div class="modal-content" style="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                            <span class="modal-title font-18 bold" >운행기록 {% oUpsertFormDrive.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}</span>
                        </div>
                        <div class="modal-body">
                            <table class="table table-cols table-pd-5" >
                                <colgroup>
                                    <col class="w-28p">
                                    <col>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th>운행차량</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select2 v-model="oUpsertFormDrive.carSno" class="form-control" style="width:100%;">
                                                <option v-if="aoCarList.length > 0" v-for="(val, key) in aoCarList" :value="val.sno">{% val.carName %}</option>
                                            </select2>
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormDrive.carName %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>운행일자</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveDt'; $placeholder='운행일자' ?>
                                        <?php include './admin/ims/template/basic_view/_picker.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>운행시작시간</th>
                                    <td>
                                        <div v-show="isModify">
                                            <input type="number" class="form-control" v-model="oUpsertFormDrive.driveStartTimeHour" placeholder="시작시간" style="width:75px; display: inline;" />
                                            : <input type="number" class="form-control" v-model="oUpsertFormDrive.driveStartTimeMin" placeholder="시작분" style="width:75px; display: inline;" />
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormDrive.driveStartTimeHour %} : {% oUpsertFormDrive.driveStartTimeMin %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>운행종료시간</th>
                                    <td>
                                        <div v-show="isModify">
                                            <input type="number" class="form-control" v-model="oUpsertFormDrive.driveEndTimeHour" placeholder="종료시간" style="width:75px; display: inline;" />
                                            : <input type="number" class="form-control" v-model="oUpsertFormDrive.driveEndTimeMin" placeholder="종료분" style="width:75px; display: inline;" />
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormDrive.driveEndTimeHour %} : {% oUpsertFormDrive.driveEndTimeMin %}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="false">
                                    <th>부서</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveDepartment'; $placeholder='부서' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>운행자</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveName'; $placeholder='성명' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>목적</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select v-model="oUpsertFormDrive.driveType" ref="carInfoDriveTypeSelect" @change="changeDriveType()" style="display: inline; width:150px;" class="form-control">
                                                <?php foreach (\Component\Ims\NkCodeMap::ETC_CAR_DRIVE_TYPE as $key => $val ) { ?>
                                                    <option value="<?=$key?>"><?=$val?></option>
                                                <?php } ?>
                                                <option value="">기타</option>
                                            </select>
                                            <input type="text" ref="carInfoDriveType" v-model="oUpsertFormDrive.driveTypeEtc" style="display: none; width:100px;" class="form-control" />
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormDrive.driveTypeEtc %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>출발지</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select2 v-model="oUpsertFormDrive.startAddrSno" class="form-control" style="width:100%;" id="oUpsertFormDriveStartAddrSno">
                                                <option value="0">선택</option>
                                                <option value="-1"> <직접 입력> </option>
                                                <option v-if="aoAddrList.length > 0" v-for="(val, key) in aoAddrList" :value="val.sno">[{% val.addrType %}] {% val.addrName %}</option>
                                            </select2>
                                            <div v-show="oUpsertFormDrive.startAddrSno == -1">
                                                <input type="text" class="form-control" v-model="oRegistAddrInfoStart.addrName" placeholder="새 주소지 명칭" style="margin-top:3px;" />
                                                <input type="text" class="form-control" v-model="oRegistAddrInfoStart.addrAddr" placeholder="새 주소지 주소" style="margin-top:3px;" />
                                            </div>
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormDrive.startAddrName %} / {% oUpsertFormDrive.startAddrAddr %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>도착지</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select2 v-model="oUpsertFormDrive.arriveAddrSno" class="form-control" style="width:100%;" id="oUpsertFormDriveArriveAddrSno">
                                                <option value="0">선택</option>
                                                <option value="-1"> <직접 입력> </option>
                                                <option v-if="aoAddrList.length > 0" v-for="(val, key) in aoAddrList" :value="val.sno">[{% val.addrType %}] {% val.addrName %}</option>
                                            </select2>
                                            <div v-show="oUpsertFormDrive.arriveAddrSno == -1">
                                                <input type="text" class="form-control" v-model="oRegistAddrInfoArrive.addrName" placeholder="새 주소지 명칭" style="margin-top:3px;" />
                                                <input type="text" class="form-control" v-model="oRegistAddrInfoArrive.addrAddr" placeholder="새 주소지 주소" style="margin-top:3px;" />
                                            </div>
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormDrive.arriveAddrName %} / {% oUpsertFormDrive.arriveAddrAddr %}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="false">
                                    <th>운행전 계기판km</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveBeforeCluster'; $placeholder='운행전 계기판km' ?>
                                        <?php include './admin/ims/template/basic_view/_number.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>주행거리(Km)</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveKm'; $placeholder='주행거리(Km)' ?>
                                        <?php include './admin/ims/template/basic_view/_number.php'?>

                                        <div v-show="isModify && oUpsertFormDrive.flagRecentDrive == true" class="mgt5">
                                            현재 주행거리(Km) : <input type="number" ref="textRecentKm" @keyup="oUpsertFormDrive.driveKm = event.target.value - oUpsertFormDrive.driveBeforeCluster;" class="form-control w-100px" style="display: inline;" />Km
                                            <div class="notice-info">현재 주행거리 입력시 주행거리가 자동 계산 됩니다.</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="false">
                                    <th>비용(주차,톨비 등등)</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveCost'; $placeholder='비용' ?>
                                        <?php include './admin/ims/template/basic_view/_number.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>비고</th>
                                    <td>
                                        <?php $model='oUpsertFormDrive.driveMemo'; $placeholder='비고' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer ">
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="!isModify" @click="isModify=true">수정하기</div>
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="saveDrive()">저장</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" v-show="isModify && oUpsertFormDrive.sno != 0" @click="isModify=false">수정취소</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalListAddr" tabindex="-1" role="dialog"  aria-hidden="true"  >
                <div class="modal-dialog" role="document" style="width:1000px;">
                    <div class="modal-content" style="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                            <span class="modal-title font-18 bold" >주소지 리스트</span>
                        </div>
                        <div class="modal-body">
                            <table v-if="aoAddrList.length > 0" class="table table-rows table-default-center table-td-height30 mgt5 ">
                                <colgroup>
                                    <col class="w-5p" />
                                    <col :class="`w-${fieldData.col}p`" v-for="fieldData in aoAddrFlds" v-if="true != fieldData.skip" />
                                </colgroup>
                                <tr>
                                    <th >번호</th>
                                    <th v-for="fieldData in aoAddrFlds"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                                        {% fieldData.title %}
                                    </th>
                                </tr>
                                <tr v-if="aoAddrList.length == 0">
                                    <td colspan="99">
                                        데이터가 없습니다.
                                    </td>
                                </tr>
                                <tr v-for="(val , key) in aoAddrList">
                                    <td >{% (aoAddrList.length - key) %}</td>
                                    <td v-for="fieldData in aoAddrFlds"  v-if="true != fieldData.skip" :class="fieldData.class">
                                        <span v-if="fieldData.type === 'title'" class="sl-blue cursor-pointer hover-btn" @click="openUpsertAddrModal(val.sno);">
                                            {% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}
                                        </span>
                                        <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                                        <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                                        <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer ">
                            <div class="btn btn-red hover-btn btn-lg mg5" @click="openUpsertAddrModal(0);">주소지 등록</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalUpsertAddr" tabindex="-1" role="dialog"  aria-hidden="true" >
                <div class="modal-dialog" role="document" style="width:500px;">
                    <div class="modal-content" style="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                            <span class="modal-title font-18 bold" >주소지 {% oUpsertFormAddr.sno == 0 ? '등록' : (isModify ? '수정' : '정보') %}</span>
                        </div>
                        <div class="modal-body">
                            <table class="table table-cols table-pd-5">
                                <colgroup>
                                    <col class="w-20p">
                                    <col>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th>주소지 분류</th>
                                    <td>
                                        <div v-show="isModify">
                                            <select v-model="oUpsertFormAddr.addrType" ref="carInfoAddrTypeSelect" @change="changeAddrType()" style="display: inline; width:150px;" class="form-control">
                                                <?php foreach (\Component\Ims\NkCodeMap::ETC_CAR_ADDR_TYPE as $key => $val ) { ?>
                                                    <option value="<?=$key?>"><?=$val?></option>
                                                <?php } ?>
                                                <option value="">기타</option>
                                            </select>
                                            <input type="text" ref="carInfoAddrType" v-model="oUpsertFormAddr.addrTypeEtc" style="display: none; width:100px;" class="form-control" />
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormAddr.addrTypeEtc %}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="false">
                                    <th>상단고정 여부</th>
                                    <td>
                                        <div v-show="isModify">
                                            <label class="radio-inline">
                                                <input type="radio" name="sRadioUpsertTopYn" value="1" v-model="oUpsertFormAddr.topYn"/> 상단고정
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="sRadioUpsertTopYn" value="2" v-model="oUpsertFormAddr.topYn"/> 미고정
                                            </label>
                                        </div>
                                        <div v-show="!isModify" >
                                            {% oUpsertFormAddr.topYn == 1 ? '상단고정' : '미고정' %}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>명칭</th>
                                    <td>
                                        <?php $model='oUpsertFormAddr.addrName'; $placeholder='명칭' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>주소</th>
                                    <td>
                                        <?php $model='oUpsertFormAddr.addrAddr'; $placeholder='주소' ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer ">
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="!isModify" @click="isModify=true">수정하기</div>
                            <div class="btn btn-blue-line hover-btn btn-lg mg5" v-show="!isModify" @click="$('#modalUpsertAddr').modal('hide'); $('#modalListAddr').modal('show');">목록으로</div>
                            <div class="btn btn-accept hover-btn btn-lg mg5" v-show="isModify" @click="saveAddr()">저장</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" v-show="isModify && oUpsertFormAddr.sno != 0" @click="isModify=false">수정취소</div>
                            <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="margin-bottom:150px"></div>
</section>

<?php include 'script/ims_car_management_script.php'?>

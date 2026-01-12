<?php
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
$modelPrefix='pop_view';
?>
<!--
[ 팝업형 프로젝트 스케쥴 관리 ]
Include 목록.
ims25_schedule_template : 스케쥴 템플릿 -> 이 템플릿으로 스케쥴 처리
ims25_view_layer : 참여자 선택 기능
-->

<style>
    .page-header { margin-bottom:10px };
    /*.mx-input {padding:0 !important; font-size:11px !important;}*/
    /*.mini-picker .mx-datepicker { width:100px!important; }*/
</style>

<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<div id="imsApp" class="project-view" >

    <span v-show="false">
        computed...
        <!--DL계산-->
        {% computedDeadLine %}
    </span>

    <div id="move-gnb"></div>

    <form id="frm">
        <div class="page-header js-affix mgb10">
            <h3>
                <span class="text-danger" >
                    {% mainData.sno %}
                </span>

                <span  >
                    <span class="text-blue cursor-pointer hover-btn" @click="openCustomer(customer.sno,'comment')">{% customer.customerName %}</span>
                    <span v-show="!isModify">{% mainData.projectYear %}</span>
                    <span v-show="isModify">
                        <select v-model="mainData.projectYear" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                            <?php foreach($yearList as $yearEach) {?>
                                <option><?=$yearEach?></option>
                            <?php }?>
                        </select>
                    </span>
                    <span v-show="!isModify">{% mainData.projectSeason %}</span>
                    <span v-show="isModify">
                        <select v-model="mainData.projectSeason" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                            <option >ALL</option>
                            <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                <option><?=$seasonEn?></option>
                            <?php }?>
                        </select>
                    </span>
                </span>

                - <span class="sl-blue"><{% mainData.projectStatusKr %} 단계></span>

                <!--프로젝트 상세정보-->
            </h3>
            <!--최상위 버튼-->
            <div class="btn-group">
                <input type="button" value="수정" class="btn btn-red-line2 btn-red"  @click="setModify(true)" v-show="!isModify">
                <input type="button" value="저장" class="btn btn-lg btn-red btn-red2 mgl10"  @click="save()" v-show="isModify">
                <input type="button" value="수정 취소" class="btn btn-lg btn-white"  @click="setModify(false)" v-show="isModify">
                <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(mainData.sno, 'mainData')" >
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <div class="row" v-if="!$.isEmpty(mainData.regDt)">
        <div >
            <div class="col-xs-7" >
                <div class="table-pop-title">
                    <div class="flo-left">기본정보</div>
                    <div class="flo-right"></div>
                </div>
                <div >
                    <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30 mgb5">
                        <colgroup>
                            <col class="w-150px">
                            <col class="w-300px">
                            <col class="w-150px">
                            <col class="w-300px">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>연도/시즌</th>
                            <td >
                                <div class="" v-show="!isModify">
                                    {% mainData.projectYear %}/{% mainData.projectSeason %}
                                </div>
                                <div class="" v-show="isModify" style="width:200px !important;">
                                    <div class="dp-flex">
                                        <select v-model="mainData.projectYear" class="form-control " >
                                            <?php foreach($yearList as $yearEach) {?>
                                                <option><?=$yearEach?></option>
                                            <?php }?>
                                        </select>
                                        <select v-model="mainData.projectSeason" class="form-control" >
                                            <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                                <option><?=$seasonEn?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <th class="font-11">목표매출년도/사업계획</th>
                            <td >
                                <div class="dp-flex dp-flex-gap10" v-show="!isModify">
                                    <div>
                                        <span v-show="!$.isEmpty(mainData.targetSalesYear)">
                                            <b>목표년도</b> : {% mainData.targetSalesYear %}년
                                        </span>
                                        <span v-show="$.isEmpty(mainData.targetSalesYear)">
                                            <b>목표년도</b> : <span class="text-muted">미지정</span>
                                        </span>
                                    </div>
                                    <div>
                                        <b>사업계획</b> : {% getCodeMap()['includeType'][mainData.bizPlanYn] %}
                                    </div>
                                </div>
                                <div class="" v-show="isModify">
                                    <div class="dp-flex mgt3">
                                        사업계획 :
                                        <label class="radio-inline mgl5 mgr5" v-for="(eachValue, eachKey) in getCodeMap()['includeType']" >
                                            <input type="radio" name="salesProjectBizPlanYn'"  :value="eachKey" v-model="mainData.bizPlanYn" />
                                            <span class="">{%eachValue%}</span>
                                        </label>
                                    </div>
                                    <div class="dp-flex">
                                        목표년도 :
                                        <select2 v-model="mainData.targetSalesYear" class="form-control form-inline inline-block " style="width:100px;">
                                            <?php foreach($yearFullList as $key => $val) {?>
                                                <option value="<?=$val?>"><?=$key?></option>
                                            <?php }?>
                                        </select2>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>영업 담당자</th>
                            <td >
                                <div class="" v-show="!isModify">
                                    <div v-show="$.isEmpty(mainData.salesManagerNm)">미정</div>
                                    <div v-show="!$.isEmpty(mainData.salesManagerNm)">{% mainData.salesManagerNm %}</div>
                                </div>
                                <div class="" v-show="isModify">
                                    <select class="form-control w100" v-model="mainData.salesManagerSno" >
                                        <option value="0">미정</option>
                                        <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                            <th>프로젝트 타입</th>
                            <td >
                                <?php
                                $model='mainData.projectType';
                                $modelValue='mainData.projectTypeKr';
                                $listData=\Component\Ims\ImsCodeMap::PROJECT_TYPE;
                                $selectWidth=100
                                ?>
                                <?php include 'template/basic_view/_select2.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>디자인 담당자</th>
                            <td >
                                <div class="" v-show="!isModify">
                                    <div v-show="$.isEmpty(mainData.designManagerNm)">미정</div>
                                    <div v-show="!$.isEmpty(mainData.designManagerNm)">{% mainData.designManagerNm %}</div>
                                </div>
                                <div class="" v-show="isModify">
                                    <select class="form-control w100" v-model="mainData.designManagerSno" >
                                        <option value="0">미정</option>
                                        <?php foreach ($designManagerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                            <th>디자인업무 타입</th>
                            <td >
                                <?php
                                $model='mainData.designWorkType';
                                $modelValue='mainData.designWorkTypeKr';
                                $listData=\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE;
                                ?>
                                <?php include 'template/basic_view/_select2.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>입찰 구분</th>
                            <td >
                                <?php
                                $model='mainData.bidType2';
                                $modelValue='mainData.bidType2Kr';
                                $listData=\Component\Ims\ImsCodeMap::BID_TYPE;
                                $selectWidth=50
                                ?>
                                <?php include 'template/basic_view/_select2.php'?>
                            </td>
                            <th>영업 기획서</th>
                            <td>
                                <div class="btn btn-sm btn-white" @click="openSalesView(mainData.sno)" >영업기획서</div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="mgb10">
                        프로젝트 등록일: {% mainData.regDt %} ({% mainData.regManagerNm %})
                    </div>
                </div>
            </div>

            <div class="col-xs-5 ">
                <div class="table-pop-title">
                    <div class="flo-left">고객문의</div>
                    <div class="flo-right"></div>
                </div>
                <div class="main-section clear-both mgt0">

                    <div class="main-section-inner border-top-black-imp" >
                        <ol class="content order list-unstyled reform">
                            <li >
                                <a href="#">
                                    <span class="status font-black">고객요청</span>
                                    <div class="order-list-val text-danger">1</div>
                                </a>
                            </li>
                            <li >
                                <a href="#">
                                    <span class="status font-black">처리대기</span>
                                    <div class="order-list-val ">5</div>
                                </a>
                            </li>
                            <li >
                                <a href="#">
                                    <span class="status font-black">처리중</span>
                                    <div class="order-list-val ">3</div>
                                </a>
                            </li>
                            <li >
                                <a href="#">
                                    <span class="status font-black">처리완료</span>
                                    <div class="order-list-val ">5</div>
                                </a>
                            </li>
                        </ol>
                    </div>

                    <div class="mgt10 font-14">
                        
                        <div class="dp-flex dp-flex-gap10">
                            <b>업무 시작일 :</b>
                            <?php $model='mainData.salesStartDt';?>
                            <div v-show="!isModify">
                                <div v-if="$.isEmpty(<?=$model?>)">
                                    <span class="font-11 text-muted">미정</span>
                                </div>
                                <div v-if="!$.isEmpty(<?=$model?>)">
                                    {% $.formatShortDate(<?=$model?>) %}

                                    <span class="font-11" v-if="$.diffDate(mainData.customerDeliveryDt, <?=$model?>) > 0">
                                        (
                                            <!--<span v-if="$.diffDate(mainData.customerDeliveryDt, $.todayYmd()) > 0">남은일:{% $.diffDate(mainData.customerDeliveryDt, $.todayYmd()) %}일/</span>-->
                                            총 업무일:{% $.diffDate(mainData.customerDeliveryDt, <?=$model?>) %}일
                                        )
                                    </span>
                                </div>
                            </div>
                            <div v-show="isModify" class="">
                                <date-picker v-model="<?=$model?>" value-type="format" format="YYYY-MM-DD"  :editable="false" @change="setScheduleDeadLine"></date-picker>
                            </div>
                        </div>
                        
                        <div v-if="2 == mainData.productionStatus || 91 == mainData.productionStatus" class="sl-green mgt5">
                            <b>고객납기 :</b>
                            <span class="font-14">{% $.formatShortDateWithoutWeek(mainData.customerDeliveryDt) %}</span>
                            납기완료
                        </div>
                        <div v-else class="dp-flex dp-flex-gap10 mgt5">
                            <b>고객납기 :</b>
                            <?php $model='mainData.customerDeliveryDt';?>
                            <div v-show="!isModify">
                                <div v-if="$.isEmpty(<?=$model?>)">
                                    <span class="font-11 text-muted">미정</span>
                                </div>
                                <div v-if="!$.isEmpty(<?=$model?>)">
                                    {% $.formatShortDate(<?=$model?>) %}
                                    <span class="font-11 " v-html="$.remainDate(<?=$model?>,true)"></span>
                                </div>
                            </div>
                            <div v-show="isModify" class="">
                                <date-picker v-model="<?=$model?>" value-type="format" format="YYYY-MM-DD"  :editable="false" @change="setScheduleDeadLine"></date-picker>
                            </div>
                            <?php $model = null?>


                            <!--변경여부-->
                            <div v-if="!isModify">
                                <div class="text-danger font-11 mgt3" v-if="'y' !== mainData.customerDeliveryDtConfirmed">변경불가</div>
                                <div class="sl-blue  font-11 mgt3" v-if="'n' !== mainData.customerDeliveryDtConfirmed">변경가능</div>
                            </div>
                            <div v-if="isModify" class="dp-flex pdl25">
                                <div class="">
                                    <label class="radio-inline">
                                        <input type="radio" name="order_deliveryConfirm"  value="y" v-model="mainData.customerDeliveryDtConfirmed"/>변경가능
                                    </label>
                                </div>
                                <div class="mgl5">
                                    <label class="radio-inline">
                                        <input type="radio" name="order_deliveryConfirm"  value="n" v-model="mainData.customerDeliveryDtConfirmed"/>변경불가
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mgt5 dp-flex dp-flex-gap10" >
                            <b>발주 D/L : </b>

                            <div v-show="!isModify">
                                <!--완료일-->
                                <div v-if="'0000-00-00' != mainData.cpProductionOrder && !$.isEmpty(mainData.cpProductionOrder)" class="text-muted">
                                <span class="font-14 sl-green">
                                    {% $.formatShortDate(mainData.cpProductionOrder) %} 발주
                                </span>
                                </div>
                                <!--대체텍스트-->
                                <div v-else-if="!$.isEmpty(mainData.txProductionOrder)">
                                    <span class="font-14">{% mainData.txProductionOrder %}</span>
                                </div>
                                <!--예정일-->
                                <div v-else-if="!$.isEmpty(mainData.exProductionOrder)" class="">
                                <span class="font-14">
                                    {% $.formatShortDate(mainData.exProductionOrder) %}
                                </span>
                                    <span class="font-11" v-html="$.remainDate(mainData.exProductionOrder,true)"></span>
                                </div>
                                <!--미설정-->
                                <div v-else class="text-muted font-11">미정</div>
                            </div>

                            <div v-show="isModify" class="dp-flex dp-flex-gap35">
                                <!--발주 예정일 수정-->
                                <date-picker v-model="mainData.exProductionOrder"
                                             class="font-14" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주DL">
                                </date-picker>
                                <input type="text" placeholder="대체텍스트" class="form-control" v-model="mainData.txProductionOrder">
                            </div>
                        </div>

                    </div>

                    <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </div>

    <!--스케쥴 탭 + 스케쥴 내용-->
    <?php include 'ims25/ims25_view_schedule.php' ?>

    <div class="row ta-c font-20" v-if="$.isEmpty(mainData.regDt)">
        로딩 실패 새로고침 해서 다시 불러오세요.
    </div>

    <?php include 'ims25/ims25_view_layer.php' ?>

    <button class="floating-btn" v-if="!isModify" @click="setModify(true)">수정</button>

    <button class="floating-btn-square" style="bottom: 150px;" v-if="isModify" @click="openAddManager()">추가 참여자</button>
    <button class="floating-btn-square" style="bottom: 100px;" v-if="isModify" @click="save()">저장</button>
    <button class="floating-btn-square" style="background-color: #fff; color:#000" v-if="isModify" @click="setModify(false);refreshProject(sno)">취소</button>

</div>

<div class="mgt40"></div>


<script type="text/javascript">
    const viewPageName = 'pop_view';
</script>

<?php include 'ims25_view_script_ext_fnc.php' ?>
<?php include 'ims25_view_script_method.php' ?>
<?php include 'ims25_view_script_method2.php' ?>
<?php include 'ims25_view_script.php'?>
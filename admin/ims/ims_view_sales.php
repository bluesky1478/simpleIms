<style>
    .page-header { margin-bottom:10px };
</style>

<?php use Component\Ims\ImsCodeMap;

include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<div id="imsApp" class="project-view" v-if="!$.isEmpty(customer)">

    <div id="move-gnb"></div>

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-danger" >
                    {% project.sno %}
                </span>

                <span  >
                    <span class="text-blue cursor-pointer hover-btn" @click="openCustomer(customer.sno,'comment')">{% customer.customerName %}</span>
                    <span v-show="!isModify">{% project.projectYear %}</span>
                    <span v-show="isModify">
                        <select v-model="project.projectYear" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                            <?php foreach($yearList as $yearEach) {?>
                                <option><?=$yearEach?></option>
                            <?php }?>
                        </select>
                    </span>
                    <span v-show="!isModify">{% project.projectSeason %}</span>
                    <span v-show="isModify">
                        <select v-model="project.projectSeason" class="form-control form-inline inline-block " style="height: 30px; width:70px;">
                            <option >ALL</option>
                            <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                <option><?=$seasonEn?></option>
                            <?php }?>
                        </select>
                    </span>
                </span> 영업 기획서
            </h3>
            <div class="btn-group">
                <input type="button" value="수정" class="btn btn-white btn-red btn-red-line2" @click="setModify(true)" v-show="!isModify">
                <input type="button" value="저장" class="btn btn-red" @click="save(); saveStyleList(false)" v-show="isModify">
                <input type="button" value="수정취소" class="btn btn-red btn-red-line2" @click="setModify(false)" v-show="isModify">
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <!--프로젝트/고객 정보-->
    <div class="row" v-if="!$.isEmpty(project.addedInfo) && !$.isEmpty(customer.addedInfo) ">
        <!--프로젝트 정보-->
        <div class="col-xs-6" >
            <!-- 기본 정보 -->
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">프로젝트 정보</div>
                    <div class="flo-right"></div>
                </div>
                <div>
                    <table class="table table-cols table-td-height35 table-th-height35" >
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th >영업 담당자</th>
                            <td colspan="3">
                                <span v-show="!isModify">
                                {% project.salesManagerNm %}
                                </span>

                                <select class="form-control w100" v-model="project.salesManagerSno" v-show="isModify">
                                    <option value="0">미정</option>
                                    <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">입찰/단독 구분</th>
                            <td colspan="3">
                                <div v-show="!isModify">{% project.bidType2Kr %}</div>
                                <?php foreach( \Component\Ims\ImsCodeMap::BID_TYPE as $k => $v){ ?>
                                    <label class="radio-inline mgl5" v-show="isModify">
                                        <input type="radio" name="bidType2" value="<?=$k?>" v-model="project.bidType2"  @change="setType()" /><?=$v?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">프로젝트 타입</th>
                            <td colspan="3" >
                                <div v-show="!isModify">{% project.projectTypeKr %}</div>
                                <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                                    <label class="radio-inline font-11 mgl5" v-show="isModify">
                                        <input type="radio" name="projectType" value="<?=$k?>" v-model="project.projectType"  /><?=$v?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">디자인 업무 타입</th>
                            <td colspan="3">
                                <div v-show="!isModify">{% project.designWorkTypeKr %}</div>
                                <?php foreach( \Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $k => $v){ ?>
                                    <label class="radio-inline mgl5" v-show="isModify">
                                        <input type="radio" name="desingWorkType" value="<?=$k?>" v-model="project.designWorkType" /><?=$v?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">제안형태</th>
                            <td colspan="3">
                                <div v-show="!isModify">
                                    <?php foreach (ImsCodeMap::RECOMMEND_TYPE as $recommendKey => $recommendValue) { ?>
                                        <label class="mgr10" v-if="project.recommendList.includes('<?=$recommendKey?>')">
                                            <?=$recommendValue?><span class="ims-recommend ims-recommend<?=$recommendKey?>"><?=mb_substr($recommendValue, 0, 1, 'UTF-8')?></span>
                                        </label>
                                    <?php } ?>
                                </div>
                                <div v-show="isModify">
                                    <?php foreach (ImsCodeMap::RECOMMEND_TYPE as $recommendKey => $recommendValue) { ?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="<?=$recommendKey?>" v-model="project.recommendList" @change="setRecommend()">
                                            <?=$recommendValue?><span class="ims-recommend ims-recommend<?=$recommendKey?>"><?=mb_substr($recommendValue, 0, 1, 'UTF-8')?></span>
                                        </label>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">제안내용</th>
                            <td colspan="3">
                                <div v-show="!isModify">
                                    <div v-if="$.isEmpty(project.addedInfo.etc21) || 0 >= project.addedInfo.etc21.length" class="text-muted">
                                        미정
                                    </div>
                                    <div v-else>
                                        {% project.addedInfo.etc21.join(', ') %}
                                    </div>

                                </div>
                                <div v-show="isModify">
                                    <?php foreach (ImsCodeMap::RECOMMEND_CONTENTS as $recommendKey => $recommendValue) { ?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="<?=$recommendValue?>" v-model="project.addedInfo.etc21">
                                            <?=$recommendValue?>
                                        </label>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">업체선정 기준</th>
                            <td colspan="3" >
                                <?php $radioKey='customer.addedInfo.info125'; $textKey='customer.addedInfo.info126'; $listCode='prjInfo02'; $placeHolder='업체선정 기준' ?>
                                <?php include 'template/basic_view/_radio_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="_require">업체선정 방법</th>
                            <td colspan="3" >
                                <?php $radioKey='customer.addedInfo.info127'; $textKey='customer.addedInfo.info128'; $listCode='prjInfo01'; $placeHolder='업체선정 방법' ?>
                                <?php include 'template/basic_view/_radio_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <?php $model='project.addedInfo.etc27'; $placeholder='계약 기간/금액' ?>
                            <th ><?=$placeholder?></th>
                            <td >
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <?php $model='customer.addedInfo.info102'; $placeholder='경쟁업체' ?>
                            <th class="cust-mark">
                                <?=$placeholder?>
                            </th>
                            <td >
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                추정 매출
                            </th>
                            <td>
                                <?php $model='project.extAmount'; $placeholder='추정매출' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <th>
                                예상 마진
                            </th>
                            <td>
                                <?php $model='project.extMargin'; $placeholder='예상마진(자유기입)' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--고객사 기본 정보-->
        <div class="col-xs-6" v-if="!$.isEmpty(customer) && !$.isEmpty(customer.addedInfo)">
            <!-- 기본 정보 -->
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">고객사 기본 정보</div>
                    <div class="flo-right"></div>
                </div>
                <div>
                    <table class="table table-cols table-td-height35 table-th-height35 mgb10">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th class="cust-mark">
                                고객사명
                            </th>
                            <td>
                                <?php $model='customer.customerName'; $placeholder='고객사명' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <th class="_require text-danger">
                                Style code
                            </th>
                            <td class="text-danger cust-mark">
                                <?php $model='customer.styleCode'; $placeholder='고객사명' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">담당자명</th>
                            <td >
                                <?php $model='customer.contactName'; $placeholder='담당자명' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <th class="cust-mark">직함</th>
                            <td>
                                <?php $model='customer.contactPosition'; $placeholder='직함' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">담당자 연락처</th>
                            <td>
                                <?php $model='customer.contactMobile'; $placeholder='휴대전화' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <th class="cust-mark">담당자 성향</th>
                            <td>
                                <?php $model='customer.contactPreference'; $placeholder='담당자 성향' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">이메일</th>
                            <td colspan="3">
                                <?php $model='customer.contactEmail'; $placeholder='이메일' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">업종</th>
                            <td>
                                <?php $model='customer.industry'; $placeholder='업종' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <th class="cust-mark">사원수</th>
                            <td>
                                <?php $model='customer.addedInfo.etc2'; $placeholder='사원수' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">의사결정 라인</th>
                            <td>
                                <?php $model='customer.addedInfo.info089'; $placeholder='의사 결정 라인' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <th class="cust-mark">노사 합의 여부</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info088'; $listCode = 'existType3'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-6" v-if="!$.isEmpty(customer) && !$.isEmpty(customer.addedInfo)">
            <!-- 기본 정보 -->
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">고객 민감도</div>
                    <div class="flo-right"></div>
                </div>
                <div>

                    <table class="table table-cols table-td-height30 table-th-height30" >
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr >
                            <th class="cust-mark">색상</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info009'; $listCode = 'ratingType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th class="cust-mark">품질</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info010'; $listCode = 'ratingType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">단가</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info011'; $listCode = 'ratingType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th class="cust-mark">납기</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info012'; $listCode = 'ratingType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">기타</th>
                            <td colspan="3">
                                <?php $model='customer.contactMemo'; $placeholder='기타' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <!--영업전략-->
    <div class="row" v-if="!$.isEmpty(project.addedInfo) && !$.isEmpty(customer.addedInfo) ">
        <div class="col-xs-12" style="padding:0 15px">
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">영업 전략</div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-sm " @click="save();saveStyleList(true)" v-show="isModify">저장</div>
                        <div class="btn btn-white btn-sm w-50px" @click="isModify=false" v-show="isModify">취소</div>
                        <div class="btn btn-red btn-sm btn-red-line2" v-show="!isModify" @click="isModify=true">&nbsp;&nbsp;수정&nbsp;&nbsp;</div>
                    </div>
                </div>
                <div>

                    <table class="table ims-schedule-table w100 table-default-center table-fixed  table-td-height35 table-th-height35 mgb10 table-pd-3">
                        <colgroup>
                            <col class="w-4p">
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
                                <col class="w-7p">
                            <?php } ?>
                        </colgroup>
                        <tr>
                            <th class="" >구분</th>
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
                                <th class="" ><?=$mainScheduleKr?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray">
                                예정일
                            </td>
                            <!-- 예정일 스케쥴 -->
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
                                <td class="bg-light-yellow" v-if="$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])">
                                    <div class="dp-flex dp-flex-center">
                                        <expected-template :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></expected-template>
                                    </div>
                                </td>
                                <!--메모 있을 경우...-->
                                <td class="bg-light-gray" rowspan="2" v-if="!$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">
                                    <div class="dp-flex dp-flex-center cursor-pointer hover-btn relative w-100px" @click="openProjectUnit(project.sno,'<?=$mainSchedule?>','<?=$mainScheduleKr?>')">
                                        {% project['tx'+$.ucfirst('<?=$mainSchedule?>')] %}
                                        <comment-cnt2 :data="project['<?=$mainSchedule?>CommentCnt']"></comment-cnt2>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray">
                                완료일
                            </td>
                            <!-- 예정일 스케쥴 -->
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_SALES_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
                                <td class="text-center " style="border-bottom:solid 1px #dddddd" v-if="$.isEmpty(project['tx<?=ucfirst($mainSchedule)?>'])">
                                    <!--<td v-for="fieldData in searchData.fieldData" v-if="true == fieldData.subRow && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) " :class="fieldData.class">-->
                                    <div class="">
                                        <div class="dp-flex dp-flex-center relative">
                                            <complete-template3 :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template3>
                                        </div>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                    </table>

                    <table class="table table-cols table-td-height30 table-th-height30" >
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr >
                            <th >영업전략</th>
                            <td colspan="99">
                                <?php $model='project.addedInfo.etc28'; $placeholder='영업전략' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr >
                            <th >실행 계획</th>
                            <td colspan="99">
                                <?php $model='project.addedInfo.etc29'; $placeholder='실행 계획' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr >
                            <th >기대 효과</th>
                            <td colspan="99">
                                <?php $model='project.addedInfo.etc30'; $placeholder='기대 효과' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr >
                            <th >매출목표</th>
                            <td colspan="99">
                                <div v-show="!isModify">
                                    {% project.targetSalesYear %}
                                </div>
                                <div v-show="isModify">
                                    <select2 v-model="project.targetSalesYear" class="form-control form-inline inline-block " style="width:100px;">
                                        <?php foreach($yearFullList as $key => $val) {?>
                                            <option value="<?=$val?>"><?=$key?></option>
                                        <?php }?>
                                    </select2>
                                </div>

                                <?php $model = 'project.salesTarget'; $listCode = 'periodType'; $listIndexData=''?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr >
                            <th >계약난이도</th>
                            <td colspan="99">
                                <?php $model = 'project.contractDifficult'; $listCode = 'ratingType2'; $listIndexData=''?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr >
                            <th >투입 예정 디자이너</th>
                            <td colspan="99">

                                <div class="dp-flex dp-flex-gap5" v-show="!isModify">
                                    <div class="sl-badge mg5 relative hover-btn cursor-pointer ta-c"
                                         v-for="designer in project.extDesigner" style="background-color: #ffffdd; width:100px">
                                        {% designer %}
                                    </div>
                                </div>

                                <div class="dp-flex dp-flex-gap5" v-show="isModify">
                                    <!--v-show="isModify"-->
                                    <select class="form-control " v-model="designManager" >
                                        <option value="">선택</option>
                                        <?php foreach ($designManagerList as $key => $value ) { ?>
                                            <option value="<?=$value?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="btn btn-blue-line btn-blue" @click="addExtDesigner()">추가</div>

                                    <div class="mgl20"></div>

                                    <div class="sl-badge mg5 relative hover-btn cursor-pointer ta-c"
                                         v-for="designer in project.extDesigner" @click="deleteExtDesigner(designer)"
                                         style="background-color: #ffffdd; width:100px">
                                        {% designer %} <i class="fa fa-times-circle text-muted" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <!--
                        <tr >
                            <th >계약난이도</th>
                            <td colspan="99">
                                <?php $listIndexData="prdIndex+"; $modifyKey='isModify';
                        $modelPrefix='sles'; $model = 'project.contractDifficult'; $listCode = 'ratingType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr >
                            <th >투입 예정 디자이너</th>
                            <td colspan="99">

                            </td>
                        </tr>
                        -->
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

    <!--스타일/기획정보-->
    <div class="row" v-if="!$.isEmpty(project.addedInfo) && !$.isEmpty(customer.addedInfo) ">
        <div class="col-xs-12" style="padding:0 15px">
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">기획 정보</div>
                    <div class="flo-right">
                        <div class="mgl10 dp-flex dp-flex-gap5" >
                            <div class="btn btn-red btn-sm " @click="save();saveStyleList(true)" v-show="isModify">저장</div>
                            <div class="btn btn-white btn-sm w-50px" @click="isModify=false" v-show="isModify">취소</div>
                            <div class="btn btn-red btn-sm btn-red-line2" v-show="!isModify" @click="isModify=true">&nbsp;&nbsp;수정&nbsp;&nbsp;</div>
                            <div class="btn btn-blue btn-blue-line btn-sm" @click="addSalesStyle()">+ 스타일 추가</div>
                            <button type="button" class="btn btn-red-box btn-sm js-receiverInfoBtnSave js-orderViewInfoSave display-none" >저장</button>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="table table-cols table-pd-3 table-th-height0 table-td-height0 table-center table-fixed style-table">
                        <colgroup>
                            <col class="w-2p">
                            <col class="w-2p">
                            <col class="w-4p">
                            <col class="w-7p">
                            <col class="w-8p">
                            <col class="w-5p"><!--예상수량-->
                            <col class="w-6p">
                            <col class="w-6p">
                            <col class="w-6p">
                            <col class="w-6p">
                            <col class="w-6p">
                            <col class="w-6p">
                            <col class="w-5p">
                            <col class="w-5p">
                            <col class="w-5p">
                            <col class="w-5p">
                            <col class="w-5p">
                            <col class="w-5p">
                            <col class="w-6p">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="text-center border-bottom-zero" >이동</th>
                            <th class="text-center border-bottom-zero">번호</th>
                            <th class="border-bottom-zero">시즌</th>
                            <th class="border-bottom-zero">타입</th>
                            <th class="text-left border-bottom-zero">스타일명</th>
                            <th class="border-bottom-zero">예상수량</th>
                            <th class="border-bottom-zero">현재단가</th>
                            <th class="border-bottom-zero">타겟단가</th>
                            <th class="border-bottom-zero">타겟단가(최대)</th>
                            <th class="border-bottom-zero">진행형태</th>
                            <th class="border-bottom-zero">고객사샘플</th>
                            <th class="border-bottom-zero">발주수량 변동</th>
                            <th class="border-bottom-zero">컨셉</th>
                            <th class="border-bottom-zero">컬러</th>
                            <th class="border-bottom-zero">기능</th>
                            <th class="border-bottom-zero">원단</th>
                            <th class="border-bottom-zero">추가옵션</th>
                            <th class="border-bottom-zero">로고사양</th>
                            <th class="border-bottom-zero text-center">추가/삭제</th>
                        </tr>
                        <tr v-if="isModify">
                            <th colspan="2" style="text-align:right !important;">
                                <span class="" style="font-weight: normal">일괄 작업</span>
                            </th>
                            <th>
                                <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchSeason" @change="batchModify(productList,'prdSeason',batchSeason)">
                                    <option value="">미정</option>
                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeKey?></option>
                                    <?php } ?>
                                </select>
                            </th>
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                            <th class="text-center">
                                <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchStyleProcType" @change="batchModify(productList,'styleProcType',batchStyleProcType)">
                                    <option :value="eachKey" v-for="(eachValue, eachKey) in getCodeMap()['styleProcType']">{% eachValue %}</option>
                                </select>
                            </th>
                            <th class="text-center">
                                <select class="js-example-basic-single sel-style border-line-gray" style="width:100%;" v-model="batchCustSampleType" @change="batchAddInfoModify(productList,'prd002',batchCustSampleType)">
                                    <option :value="eachKey" v-for="(eachValue, eachKey) in getCodeMap()['custSampleType']">{% eachValue %}</option>
                                </select>
                            </th>
                            <th colspan="8"></th>
                        </tr>
                        </thead>

                        <tbody v-if="0 >= productList.length">
                        <tr>
                            <td colspan="12" >스타일 없음</td>
                        </tr>
                        </tbody>

                        <tbody is="draggable" :list="productList"  :animation="200" tag="tbody" handle=".handle" @change="changeProductList()">
                        <tr v-for="(product, prdIndex) in productList">

                            <td :class="product.sno > 0 ? 'handle' : ''">
                                <div class="cursor-pointer hover-btn" v-show="product.sno > 0">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                                <div class="text-danger font-9" v-show="$.isEmpty(product.sno) || 0 >= product.sno">
                                    신규
                                </div>
                            </td>

                            <td class="text-center">
                                {% prdIndex+1 %}
                                <div class="text-muted font-9">{% product.sno %}</div>
                            </td>
                            <td class="text-center">
                                <div v-show="isModify">
                                    <select class="js-example-basic-single sel-style border-line-gray" v-model="product.prdSeason" @change="setStyleName(product); setStyleCode(product, customer.styleCode)" style="width:100%;" >
                                        <option value="">미정</option>
                                        <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                            <option value="<?=$codeKey?>"><?=$codeKey?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div v-show="!isModify">
                                    {% product.prdSeason %}
                                </div>
                            </td>
                            <td >
                                <div v-show="isModify">
                                    <select class="js-example-basic-single sel-style border-line-gray" v-model="product.prdStyle" style="width:100%;" @change="setStyleName(product); setStyleCode(product, customer.styleCode)">
                                        <option value="">미정</option>
                                        <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                            <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div v-show="!isModify" class="ta-l pdl5">
                                    {% styleMap[product.prdStyle] %}
                                </div>
                            </td>
                            <td >
                                <div class="ta-l pdl5">
                                    <?php $modifyKey='isModify'; $model='product.productName'; $placeholder='스타일명' ?>
                                    <?php include 'template/basic_view/_text.php'?>
                                </div>
                            </td>
                            <td>
                                <?php $modifyKey='isModify'; $model='product.prdExQty'; $placeholder='수량' ?>
                                <?php include 'template/basic_view/_number.php'?>
                            </td>
                            <td>
                                <?php $modifyKey='isModify'; $model='product.currentPrice'; $placeholder='현재단가' ?>
                                <?php include 'template/basic_view/_number.php'?>
                            </td>
                            <td>
                                <?php $modifyKey='isModify'; $model='product.targetPrice'; $placeholder='최소' ?>
                                <?php include 'template/basic_view/_number.php'?>
                            </td>
                            <td>
                                <?php $modifyKey='isModify'; $model='product.targetPriceMax'; $placeholder='최대' ?>
                                <?php include 'template/basic_view/_number.php'?>
                            </td>
                            <td>
                                <?php $listIndexData="prdIndex+"; $modifyKey='isModify'; $modelPrefix='styleProcType'; $model = 'product.styleProcType'; $listCode = 'styleProcType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <td >
                                <?php $listIndexData="prdIndex+"; $modifyKey='isModify'; $modelPrefix='custSampleType'; $model = 'product.addedInfo.prd002'; $listCode = 'custSampleType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd009'; $placeholder='발주수량변동' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd010'; $placeholder='컨셉' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd011'; $placeholder='컬러' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd012'; $placeholder='기능' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd013'; $placeholder='원단' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd014'; $placeholder='추가옵션' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td >
                                <?php $modifyKey='isModify'; $model='product.addedInfo.prd015'; $placeholder='로고사양' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                            <td class="text-center">
                                <div class="btn btn-red btn-red-line2 btn-sm" @click="addSalesStyle()">추가</div>
                                <div class="btn btn-white btn-sm" v-show="product.sno > 0"
                                     @click="ImsService.deleteData('projectProduct',product.sno, ()=>{ refreshProductList(sno) })">
                                    삭제
                                </div>
                                <div class="btn btn-white btn-sm" v-show="$.isEmpty(product.sno) || 0 >= product.sno"
                                     @click="deleteElement(productList, prdIndex)">
                                    삭제
                                </div>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>

                <div>

                    <table class="table table-cols table-td-height30 table-th-height30" >
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr >
                            <th >변경사유</th>
                            <td colspan="99">
                                <div v-show="!isModify">
                                    {% project.addedInfo.etc31.join(', ') %}
                                </div>
                                <div v-show="isModify">
                                    <?php foreach (ImsCodeMap::PRJ_INFO_03 as $recommendKey => $recommendValue) { ?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="<?=$recommendValue?>" v-model="project.addedInfo.etc31">
                                            <?=$recommendValue?>
                                        </label>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr >
                            <th class="cust-mark">로고 구분</th>
                            <td colspan="3">
                                <?php $model = 'customer.addedInfo.info113'; $listCode = 'custInfo01'; $listIndexData=''?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th  class="cust-mark">명찰 구분</th>
                            <td colspan="3">
                                <?php $model = 'customer.addedInfo.info114'; $listCode = 'custInfo02'; $listIndexData=''?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th >세탁 구분</th>
                            <td colspan="3">
                                <?php $model = 'project.addedInfo.etc33'; $listCode = 'prjInfo04'; $listIndexData=''?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th >샘플 비용</th>
                            <td colspan="3">
                                <?php $model = 'project.addedInfo.etc34'; $listCode = 'prjInfo05'; $listIndexData=''?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">명찰 정보(사용시)</th>
                            <td colspan="99">
                                <?php $modifyKey='isModify'; $model='customer.addedInfo.info118'; $placeholder='명찰 정보' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

    <!--부가서비스 / 근무환경-->
    <div class="row" v-if="!$.isEmpty(project.addedInfo) && !$.isEmpty(customer.addedInfo) ">
        <!--부가서비스 정보-->
        <div class="col-xs-6" >
            <!-- 기본 정보 -->
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">부가서비스 정보</div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-sm " @click="save();saveStyleList(true)" v-show="isModify">저장</div>
                        <div class="btn btn-white btn-sm w-50px" @click="isModify=false" v-show="isModify">취소</div>
                        <div class="btn btn-red btn-sm btn-red-line2" v-show="!isModify" @click="isModify=true">&nbsp;&nbsp;수정&nbsp;&nbsp;</div>
                    </div>
                </div>
                <div>
                    <table class="table table-cols table-td-height35 table-th-height35" >
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th >분류 패킹 정보</th>
                            <td colspan="3">
                                <div v-show="!isModify">
                                    {% getCodeMap()['processType'][project.packingYn] %}
                                    <span v-show="'y' === project.packingYn">
                                        ( 비용:{% getCodeMap()['existType2'][project.addedInfo.etc35] %}
                                        <span v-show="!$.isEmpty(project.addedInfo.etc36)"> / {% project.addedInfo.etc36 %}</span>)
                                    </span>
                                </div>
                                <div v-show="isModify">
                                    <div class="" >
                                        <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['processType']">
                                            <input type="radio" :name="'project-added-info-packingYn'"  :value="eachKey" v-model="project.packingYn"  />
                                            <span class="font-12">{%eachValue%}</span>
                                        </label>
                                    </div>
                                    <div class="mgt5 " v-show="'y' === project.packingYn">
                                        <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['existType2']">
                                            <input type="radio" :name="'project-added-info-etc35'"  :value="eachKey" v-model="project.addedInfo.etc35"  />
                                            <span class="font-12">{%eachValue%}</span>
                                        </label>
                                        <div class="dp-flex mgt5">
                                            분류패킹 정보:<input type="text" class="form-control w-80p" v-model="project.addedInfo.etc36"  placeholder="분류패킹 기타 정보(예:0개지사)" >
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>3PL</th>
                            <td >
                                <div v-show="!isModify">
                                    {% project.use3plKr %}
                                </div>
                                <div v-show="isModify">
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="use3pl" value="n"  v-model="project.use3pl" />사용안함
                                    </label>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="use3pl" value="y"  v-model="project.use3pl" />사용
                                    </label>
                                </div>
                            </td>
                            <th>폐쇄몰</th>
                            <td >
                                <div v-show="!isModify">
                                    {% project.useMallKr %}
                                </div>
                                <div v-show="isModify">
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="useMall" value="n"  v-model="project.useMall" />사용안함
                                    </label>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="useMall" value="y"  v-model="project.useMall" />사용
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">입고비</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info119'; $listCode = 'existType2'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th class="cust-mark">보관비</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info120'; $listCode = 'existType2'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">출고비</th>
                            <td colspan="99">
                                <?php $model = 'customer.addedInfo.info121'; $listCode = 'existType2'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">안전재고 생산</th>
                            <td>
                                <?php $model = 'customer.addedInfo.info044'; $listCode = 'processType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th class="cust-mark">안전 재고 비율</th>
                            <td>
                                <div class="dp-flex">
                                    <?php $model='customer.addedInfo.info027'; $placeholder='안전 재고 비율' ?>
                                    <?php include 'template/basic_view/_text.php'?>%
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">원부자재 비축</th>
                            <td>
                                <?php $model = 'customer.addedInfo.info039'; $listCode = 'processType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                            <th class="cust-mark">비축 비율</th>
                            <td>
                                <div class="dp-flex">
                                    <?php $model='customer.addedInfo.info040'; $placeholder='비축 비율' ?>
                                    <?php include 'template/basic_view/_text.php'?>%
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">계약 종료 재고 처리</th>
                            <td colspan="99">
                                <?php $model='customer.addedInfo.info124'; $placeholder='계약 종료 재고 처리' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--근무환경-->
        <div class="col-xs-6" v-if="!$.isEmpty(customer) && !$.isEmpty(customer.addedInfo)">
            <!-- 기본 정보 -->
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">근무 환경</div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-sm " @click="save();saveStyleList(true)" v-show="isModify">저장</div>
                        <div class="btn btn-white btn-sm w-50px" @click="isModify=false" v-show="isModify">취소</div>
                        <div class="btn btn-red btn-sm btn-red-line2" v-show="!isModify" @click="isModify=true">&nbsp;&nbsp;수정&nbsp;&nbsp;</div>
                    </div>
                </div>
                <div>
                    <table class="table table-cols table-td-height35 table-th-height35 mgb10">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th class="cust-mark">
                                근무 환경 조사
                            </th>
                            <td colspan="99">
                                <?php $model = 'customer.addedInfo.info072'; $listCode = 'processType'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">
                                착용자 연령대
                            </th>
                            <td colspan="99">
                                <?php $model='customer.addedInfo.etc3'; $placeholder='착용 연령대' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">
                                남녀 비율
                            </th>
                            <td colspan="99">
                                <?php $model='customer.addedInfo.info122'; $placeholder='남녀 비율' ?>
                                <?php include 'template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">
                                근무 환경
                            </th>
                            <td colspan="99">
                                <?php $model = 'customer.addedInfo.info123'; $listCode = 'custInfo03'?>
                                <?php include 'template/basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th class="cust-mark">
                                근무 환경 조사 자료
                            </th>
                            <td colspan="99">
                                <simple-file-upload :file="fileList.fileEtc5" :id="'fileEtc5'" :project="project" ></simple-file-upload>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row" v-show="!isFactory"  v-if="!$.isEmpty(fileList)">
        <div class="col-xs-12" style="padding:0 15px" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">영업 파일</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>
                            견적서 (파일)
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileEtc2" :id="'fileEtc2'" :project="project" ></simple-file-upload>
                        </td>
                        <th>영업 확정서 (파일)</th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileEtc4" :id="'fileEtc4'" :project="project" ></simple-file-upload>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            입찰 추가 정보
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileMeeting" :id="'fileMeeting'" :project="project" ></simple-file-upload>
                        </td>
                        <th>
                            기타파일
                        </th>
                        <td colspan="3">
                            <simple-file-upload :file="fileList.fileEtc7" :id="'fileEtc7'" :project="project" ></simple-file-upload>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="dp-flex dp-flex-center mgt40">
        <input type="button" value="수정" class="btn btn-lg btn-white btn-red btn-red-line2" @click="setModify(true)" v-show="!isModify">
        <input type="button" value="저장" class="btn btn-lg btn-red" @click="save(); saveStyleList(false)" v-show="isModify">
        <input type="button" value="수정취소" class="btn btn-lg btn-red btn-red-line2" @click="setModify(false)" v-show="isModify">
        <input type="button" value="닫기" class="btn  btn-lg btn-white" @click="self.close()" >
    </div>

</div>


<div class="mgt50"><!--BLank--></div>

<?php include 'ims_view2_script_ext_fnc.php' ?>
<?php include 'ims_view2_script_method.php' ?>
<?php include 'ims_view2_script.php'?>
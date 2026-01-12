<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .mx-datepicker {
        width: 150px;
    }
</style>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %}</span> 프로젝트 수정
                <span class="text-danger" style="font-weight:normal" v-show="!$.isEmpty(project.projectNo)">({% project.projectStatusKr %}-{% project.projectNo %})</span>
            </h3>

            <div class="btn-group">
                <!--<input type="button" value="일정 초기화" class="btn btn-gray"  @click="initData()">-->
                <input type="button" value="저장" class="btn btn-red btn-register" @click="saveSimpleProject(project)">
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <!--탭화면-->
    <section v-show="'basic' === tabMode">
        <div class="row" v-show="!isFactory">

            <!-- ####  기본정보  ####-->
            <div class="col-xs-12" >

                <!-- ####  협상단계  ####-->
                <div v-show="[15].indexOf(Number(project.projectStatus)) !== -1">
                    <!--<div class="table-title gd-help-manual">
                        <div class="flo-left">
                            협상단계 메모 : <input type="text" class="form-control" v-model="project.workMemo" style="width:500px">
                        </div>
                        <div class="flo-right"></div>
                    </div>-->
                </div>

                <!-- ####  진행준비 정보 수정  ####-->
                <div v-show="[10].indexOf(Number(project.projectStatus)) !== -1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">진행준비 항목 수정</div>
                        <div class="flo-right"></div>
                    </div>

                    <table class="table table-cols w100 table-default-center table-pd-5 table-td-height30 table-th-height30">
                        <colgroup>
                            <col class="width-md"/>
                            <col class="width-md"/>
                            <col class="width-md"/>
                            <col class="width-md"/>
                        </colgroup>
                        <tr>
                            <th>미팅일자/정보</th>
                            <th>디자인</th>
                            <th>생산</th>
                            <th>내부미팅</th>
                        </tr>
                        <tr>
                            <td class="text-left"><!--미팅정보-->
                                <date-picker v-model="project.meetingInfoExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="미팅일자" class=""></date-picker>
                                <div class="dp-flex-center mgt5">
                                    <input type="text" placeholder="시간/장소" v-model="project.meetingInfoMemo" class="form-control w100">
                                </div>
                            </td>
                            <td class="text-center"><!--디자인-->
                                <!--<input type="text" placeholder="디자인" v-model="project.designAgree" class="form-control">-->
                                <!--<textarea class="form-control" v-model="project.designAgreeMemo"  placeholder="디자인" style="height:54px;"></textarea>-->
                                <select class="form-control inline-block" v-model="project.designAgreeMemo" style="width:50%">
                                    <option value="">미확인</option>
                                    <option >준비중</option>
                                    <option >준비완료</option>
                                    <option >해당없음</option>
                                </select>
                            </td>
                            <td class="text-center"><!--생산-->
                                <!--<input type="text" placeholder="생산" v-model="project.qcAgree" class="form-control">-->
                                <!--<textarea class="form-control" v-model="project.qcAgreeMemo" placeholder="생산" style="height:54px;"></textarea>-->
                                <select class="form-control w-90p inline-block" v-model="project.qcAgreeMemo" style="width:50%">
                                    <option value="">미확인</option>
                                    <option >준비중</option>
                                    <option >준비완료</option>
                                    <option >해당없음</option>
                                </select>
                            </td>
                            <td class="text-left"><!--유관부서 협의-->
                                <date-picker v-model="project.allAgreeExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="유관부서 협의" class="" style="margin-top:0 !important;"></date-picker>
                                <div class="dp-flex-center mgt5">
                                    <input type="text" placeholder="시간/기타" v-model="project.allAgreeMemo" class="form-control">
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>

                <!-- ####  고객사미팅 정보 수정  ####-->
                <div v-show="[16,'16'].indexOf(project.projectStatus) !== -1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">고객사 미팅 항목 수정</div>
                        <div class="flo-right"></div>
                    </div>

                    <table class="table table-cols w100 table-default-center table-pd-5 table-td-height30 table-th-height30">
                        <colgroup>
                            <col class="width-md"/>
                            <col class="width-md"/>
                            <col class="width-md"/>
                            <col class="width-md"/>
                        </colgroup>
                        <tr>
                            <th>미팅일자/정보</th>
                            <th>참석자</th>
                            <th ><i class="fa fa-caret-right text-dark-red" aria-hidden="true"></i> 고객안내일</th>
                            <th ><i class="fa fa-caret-right text-dark-red" aria-hidden="true"></i> 미팅보고서</th>
                        </tr>
                        <tr>
                            <td class="text-left"><!--미팅정보-->
                                <date-picker v-model="project.meetingInfoExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="미팅일자" class=""></date-picker>
                                <div class="dp-flex-center mgt5">
                                    <input type="text" placeholder="시간/장소" v-model="project.meetingInfoMemo" class="form-control w100">
                                </div>
                            </td>
                            <td class="text-center"><!--참석자-->
                                <input type="text" placeholder="참석자" v-model="project.meetingMemberMemo" class="form-control" style="height:100%">
                                <!--<textarea class="form-control" v-model="project.meetingMemberMemo" placeholder="참석자" style="height:54px;"></textarea>-->
                            </td>
                            <td class=""><!--고객 안내일-->
                                <date-picker v-model="project.custMeetingInformExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 안내일" class="" ></date-picker>
                            </td>
                            <td class="text-left">
                                <simple-file-upload :file="fileList.fileEtc1" :id="'fileEtc1'" :project="project" ></simple-file-upload>
                            </td>
                        </tr>
                    </table>

                </div>

                <!-- ####  기획, 제안서 단계 정보 수정  ####-->
                <div v-show="[20,30,31].indexOf(Number(project.projectStatus)) !== -1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">일정수정</div>
                        <div class="flo-right">
                        </div>
                    </div>
                    <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                        <colgroup>
                            <col class="width-xs"/>
                            <?php foreach($fieldList['step20']['list'] as $each) { ?>
                                <?php if( isset($each['split']) && !empty($each['col'])) { ?>
                                    <col class="width-md"/>
                                <?php } ?>
                            <?php } ?>
                        </colgroup>
                        <tr>
                            <th>구분</th>
                            <?php foreach($fieldList['step20']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <th class="center">
                                        <!-- 필수값
                                        <?php if(!empty($each[6])) { ?>
                                            <i class="fa fa-caret-right text-dark-red" aria-hidden="true" v-show="60 == project.projectStatus"></i>
                                        <?php } ?>
                                        -->
                                        <?=$each['title']?>
                                    </th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">예정일</td>
                            <?php foreach($fieldList['step20']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td class="center bg-light-yellow">
                                        <date-picker v-model="project.<?=$each['field']?>ExpectedDt" name="<?=$each['field']?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?> 예정" class="date-input" style="margin-top:0 !important;font-size:11px !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">완료일</td>
                            <?php foreach($fieldList['step20']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td >
                                        <date-picker v-model="project.<?=$each['field']?>CompleteDt" name="<?=$each['field']?>CompleteDt"  value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?>" class="date-input" style="margin-top:0 !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">대체텍스트</td>

                            <?php foreach($fieldList['step20']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td class="text-center">
                                        <div class="" style="display: block !important;">
                                            <input type="text" class="form-control" v-model="project.<?=$each['field']?>AlterText" placeholder="대체텍스트" style="display: inline-block !important;width:60%" maxlength="10">
                                        </div>
                                        <div class="mgt2 mgb3"><a href="#" @click="project.<?=$each['field']?>AlterText='해당없음'" class="text-blue line">해당없음</a></div>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </table>
                </div>

                <!-- ####  샘플제안, 샘플확정 정보 수정  ####-->
                <div v-show="[40,41,'40','41'].indexOf(project.projectStatus) !== -1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">일정수정</div>
                        <div class="flo-right">
                            <!--<div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                            <div class="btn btn-red" @click="openProject(project.sno)">수정</div>-->
                        </div>
                    </div>
                    <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                        <colgroup>
                            <col class="width-xs"/>
                            <?php foreach($fieldList['step40']['list'] as $each) { ?>
                                <?php if( isset($each['split']) && !empty($each['col'])) { ?>
                                    <col class="width-md"/>
                                <?php } ?>
                            <?php } ?>
                        </colgroup>
                        <tr>
                            <th>구분</th>
                            <?php foreach($fieldList['step40']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <th class="center">
                                        <!-- 필수값
                                        <?php if(!empty($each[6])) { ?>
                                            <i class="fa fa-caret-right text-dark-red" aria-hidden="true" v-show="60 == project.projectStatus"></i>
                                        <?php } ?>
                                        -->
                                        <?=$each['title']?>
                                    </th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">예정일</td>
                            <?php foreach($fieldList['step40']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td class="center bg-light-yellow">
                                        <date-picker v-model="project.<?=$each['field']?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?> 예정" class="" style="margin-top:0 !important;font-size:11px !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">완료일</td>
                            <?php foreach($fieldList['step40']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td >
                                        <date-picker v-model="project.<?=$each['field']?>CompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?>" class="" style="margin-top:0 !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">대체텍스트</td>
                            <?php foreach($fieldList['step40']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td class="text-center">
                                        <div class="" style="display: block !important;">
                                            <input type="text" class="form-control" v-model="project.<?=$each['field']?>AlterText" placeholder="대체텍스트" style="display: inline-block !important;width:60%" maxlength="10">
                                        </div>
                                        <div class="mgt2 mgb3"><a href="#" @click="project.<?=$each['field']?>AlterText='해당없음'" class="text-blue line">해당없음</a></div>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </table>
                </div>

                <!-- ####  '발주 대기' 단계 정보 수정  ####-->
                <div v-show="[50,'50'].indexOf(project.projectStatus) !== -1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">일정수정</div>
                        <div class="flo-right">
                        </div>
                    </div>

                    <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                        <colgroup>
                            <col class="width-xs"/>
                            <?php foreach($fieldList['step50']['list'] as $each) { ?>
                                <?php if( isset($each['split']) && !empty($each['col'])) { ?>
                                    <col class="width-md"/>
                                <?php } ?>
                            <?php } ?>
                        </colgroup>
                        <tr>
                            <th>구분</th>
                            <?php foreach($fieldList['step50']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <th class="center">
                                        <!-- 필수값
                                        <?php if(!empty($each[6])) { ?>
                                            <i class="fa fa-caret-right text-dark-red" aria-hidden="true" v-show="60 == project.projectStatus"></i>
                                        <?php } ?>
                                        -->
                                        <?=$each['title']?>
                                    </th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">예정일</td>
                            <?php foreach($fieldList['step50']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <?php if( 'customerWait' === $each['field']) { ?>
                                        <td class="center " rowspan="3">
                                            <!--<textarea class="form-control w-100p h100" v-model="project.customerWaitMemo" maxlength="40"></textarea>-->
                                            <input type="text" class="form-control w-100p" v-model="project.customerWaitMemo" maxlength="40"></input>
                                        </td>
                                    <?php }else{ ?>
                                        <td class="center bg-light-yellow">
                                            <date-picker v-model="project.<?=$each['field']?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?> 예정" class="" style="margin-top:0 !important;font-size:11px !important;"></date-picker>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">완료일</td>
                            <?php foreach($fieldList['step50']['list'] as $each) { ?>
                                <?php if(isset($each['split']) && 'customerWait' !== $each['field'] ) { ?>
                                    <td >
                                        <date-picker v-model="project.<?=$each['field']?>CompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?>" class="" style="margin-top:0 !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">대체텍스트</td>
                            <?php foreach($fieldList['step50']['list'] as $each) { ?>
                                <?php if(isset($each['split']) && 'customerWait' !== $each['field'] ) { ?>
                                    <td class="text-center">
                                        <div class="" style="display: block !important;">
                                            <input type="text" class="form-control" v-model="project.<?=$each['field']?>AlterText" placeholder="대체텍스트" style="display: inline-block !important;width:60%" maxlength="10">
                                        </div>
                                        <div class="mgt2 mgb3"><a href="#" @click="project.<?=$each['field']?>AlterText='해당없음'" class="text-blue line">해당없음</a></div>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </table>

                </div>

                <!-- ####  '발주' 단계 정보 수정  ####-->
                <div v-show="[60,'60'].indexOf(project.projectStatus) !== -1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left">일정수정</div>
                        <div class="flo-right">
                            <!--<div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                            <div class="btn btn-red" @click="openProject(project.sno)">수정</div>-->
                        </div>
                    </div>
                    <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                        <colgroup>
                            <col class="width-xs"/>
                            <?php foreach($fieldList['step60']['list'] as $each) { ?>
                                <?php if( isset($each['split']) && !empty($each['col'])) { ?>
                                    <col class="width-md"/>
                                <?php } ?>
                            <?php } ?>
                        </colgroup>
                        <tr>
                            <th>구분</th>
                            <?php foreach($fieldList['step60']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <th class="center">
                                        <!-- 필수값
                                        <?php if(!empty($each[6])) { ?>
                                            <i class="fa fa-caret-right text-dark-red" aria-hidden="true" v-show="60 == project.projectStatus"></i>
                                        <?php } ?>
                                        -->
                                        <?=$each['title']?>
                                    </th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">예정일</td>
                            <?php foreach($fieldList['step60']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td class="center bg-light-yellow">
                                        <date-picker v-model="project.<?=$each['field']?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?> 예정" class="" style="margin-top:0 !important;font-size:11px !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">완료일</td>
                            <?php foreach($fieldList['step60']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td >
                                        <date-picker v-model="project.<?=$each['field']?>CompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=strip_tags($each['title'])?>" class="" style="margin-top:0 !important;"></date-picker>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="bg-light-gray center">대체텍스트</td>
                            <?php foreach($fieldList['step60']['list'] as $each) { ?>
                                <?php if(isset($each['split'])) { ?>
                                    <td class="text-center">
                                        <div class="" style="display: block !important;">
                                            <input type="text" class="form-control" v-model="project.<?=$each['field']?>AlterText" placeholder="대체텍스트" style="display: inline-block !important;width:60%" maxlength="10">
                                        </div>
                                        <div class="mgt2 mgb3"><a href="#" @click="project.<?=$each['field']?>AlterText='해당없음'" class="text-blue line">해당없음</a></div>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </table>


                </div>

            </div>

            <div class="col-xs-12" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        기본정보 수정 (상태, 담당자 등 수정)
                        <div class="btn btn-white hover-btn cursor-pointer" @click="showBasicInfo=false" v-show="showBasicInfo" >
                            <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 기본정보 숨기기
                        </div>
                        <div class="btn btn-white hover-btn cursor-pointer" @click="showBasicInfo=true" v-show="!showBasicInfo">
                            <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 기본정보 변경
                        </div>
                    </div>
                    <div class="flo-right">
                        <!--<div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                        <div class="btn btn-red" @click="openProject(project.sno)">수정</div>-->
                    </div>
                </div>

                <table class="table table-cols w100 table-default-center table-pd-0 " v-show="showBasicInfo">
                    <colgroup>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                        <col class="width-xs"/>
                    </colgroup>
                    <tr>
                        <th>연도/시즌</th>
                        <th>프로젝트타입</th>
                        <th>발주D/L</th>
                        <th>희망납기</th>
                        <th>납기변경</th>
                        <th>계약형태</th>
                        <th>영업담당자</th>
                        <th>디자인담당자</th>
                    </tr>
                    <tr>
                        <td>
                            <select v-model="project.projectYear" class="form-control form-inline inline-block w-45p font-14">
                                <?php foreach($yearList as $yearEach) {?>
                                    <option><?=$yearEach?></option>
                                <?php }?>
                            </select>
                            <select v-model="project.projectSeason" class="form-control form-inline inline-block w-45p font-14">
                                <option >ALL</option>
                                <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                    <option><?=$seasonEn?></option>
                                <?php }?>
                            </select>
                        </td>
                        <td>
                            <select v-model="project.projectType" class="form-control form-inline inline-block  font-14" >
                                <?php foreach ( $projectTypeMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php }?>
                            </select>
                        </td>
                        <td >
                            <date-picker v-model="project.customerOrderDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="발주D/L"></date-picker>
                        </td>
                        <td >
                            <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 희망 납기"></date-picker>
                        </td>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="deliveryConfirm"  value="y" v-model="project.customerDeliveryDtConfirmed"/>변경가능
                            </label>
                            <br>
                            <label class="radio-inline">
                                <input type="radio" name="deliveryConfirm"  value="n" v-model="project.customerDeliveryDtConfirmed"/>변경불가
                            </label>
                        </td>
                        <td >
                            <select v-model="project.bidType" class="form-control  form-inline inline-block w-45p font-14 ">
                                <option value="">미정</option>
                                <option>입찰</option>
                                <option>단독진행</option>
                            </select>
                        </td>
                        <td>
                            <select2 class="js-example-basic-single" v-model="project.salesManagerSno"  style="width:80%" >
                                <option value="0">미정</option>
                                <?php foreach ($managerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                        <td>
                            <select2 class="js-example-basic-single" v-model="project.designManagerSno"  style="width:80%" >
                                <option value="0">미정</option>
                                <?php foreach ($designManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                    </tr>
                    <tr>
                        <th>프로젝트상태</th>
                        <td class="text-left" style="padding-left:10px !important;" colspan="99">
                            <select2 v-model="project.projectStatus" style="width:150px; ">
                                <?php foreach ($projectListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                            <div class="btn btn-red display-none" @click="setStatus(project)">변경</div>
                            <div class="btn btn-white" @click="openProjectStatusHistory(project.sno,'')">상태변경이력</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-12 text-center" >
                <input type="button" value="저장" class="btn btn-lg btn-red btn-register" @click="saveSimpleProject(project)">
                <input type="button" value="닫기" class="btn btn-lg btn-white" @click="self.close()" >
            </div>

        </div>
    </section>

    <div style="margin-bottom:150px"></div>

</section>

<?php include './admin/ims/script/ims_project_view_script.php'?>


<!-- 스케쥴 영역 -->
<div class="row ">

    <!--공개입찰 영업희망일-->
    <div class="col-xs-12" >
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title"></div>
            <div class="flo-right">
                <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">수정</div>
                <div class="btn btn-red btn-red2" @click="saveProject()" v-show="isModify">저장</div>
                <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">수정취소</div>
            </div>
        </div>
        <table class="table table-cols w100 table-default-center table-pd-5 table-td-height35 table-th-height35">
            <colgroup>
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>생산 TOTAL :
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
            </colgroup>
            <tr>
                <th>구분</th>
                <th>기획서</th>
                <th>제안서</th>
                <th>가견적</th>
                <th>샘플제안</th>
                <th>샘플확정</th>
                <th>확정견적</th>
                <th>고객발주</th>
                <th>납품일</th>
            </tr>
            <tr v-show="!isModify">
                <th>영업 희망일</th>
                <td>{% $.formatShortDate(project.addedInfo.info076) %}</td><!--기획서-->
                <td>{% $.formatShortDate(project.addedInfo.info077) %}</td><!--제안서-->
                <td>{% $.formatShortDate(project.addedInfo.info078) %}</td><!--가견적-->
                <td>{% $.formatShortDate(project.addedInfo.info080) %}</td><!--샘플제안-->
                <td>{% $.formatShortDate(project.addedInfo.info082) %}</td><!--샘플확정-->
                <td>{% $.formatShortDate(project.addedInfo.info081) %}</td><!--확정견적-->
                <td>{% $.formatShortDate(project.addedInfo.info083) %}</td><!--고객발주-->
                <td>{% $.formatShortDate(project.addedInfo.info084) %}</td><!--납품일-->
            </tr>
            <tr v-show="isModify">
                <th>영업 희망일</th>
                <td>
                    <date-picker v-model="project.addedInfo.info076" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="기획서" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info077" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="제안서" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info078" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="가견적" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info080" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="샘플제안" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info082" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="샘플확정" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info081" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="확정견적" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info083" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="고객발주" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info084" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="납품일" class=""></date-picker>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="clear-both" style="clear-both"></div>


<!-- 스타일 영역 -->
<?php include 'basic_type_style.php'?>

<!-- 단계별 정보 영역 시작 -->
<div class="row">

    <?php $title = '고객사 정보'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >고객사 근무환경</th>
        <td colspan="3">
            <?php $model='project.addedInfo.info085'; $placeholder='고객사 근무 환경' ?>
            <?php include '_text.php'?>
        </td>
    </tr>
    <tr >
        <th >착용자 연령/성별</th>
        <td >
            <div class="dp-flex dp-flex-gap10">
                <?php $model='project.addedInfo.info086'; $placeholder='착용자 연령' ?>
                <?php include '_text.php'?>
                <?php $model='project.addedInfo.info087'; $listCode='sexType' ?>
                <?php include '_radio.php'?>
            </div>
        </td>
        <th >노조 개입</th>
        <td >
            <div class="dp-flex dp-flex-gap10">
                <?php $model='project.addedInfo.info088'; $placeholder='노조 개입' ?>
                <?php include '_text.php'?>
            </div>
        </td>
    </tr>
    <tr >
        <th >의사 결정 라인</th>
        <td >
            <div class="dp-flex dp-flex-gap10">
                <?php $model='project.addedInfo.info089'; $placeholder='의사 결정 라인' ?>
                <?php include '_text.php'?>
            </div>
        </td>
        <th >의사 결정 기간</th>
        <td >
            <div class="dp-flex dp-flex-gap10">
                <?php $model='project.addedInfo.info090'; $placeholder='의사 결정 기간' ?>
                <?php include '_text.php'?>
            </div>
        </td>
    </tr>
    <tr >
        <th >샘플 확보</th>
        <td >
            <?php $model = 'project.addedInfo.info003'; $listCode = 'ableType'?>
            <?php include '_radio.php'?>
        </td>
        <th >샘플 반납 유무</th>
        <td >
            <?php $model = 'project.addedInfo.info004'; $listCode = 'existType'?>
            <?php include '_radio.php'?>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->
    <!--고객성향-->
    <?php include 'basic_type_tendency.php'?>

</div>

<div class="row mgt20">
    <div class="col-xs-12 new-style">
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                미팅정보
            </div>
            <div class="flo-right">
            </div>
        </div>
        <table class="table table-cols  xsmall-picker">
            <colgroup>
                <col class="width-sm">
                <col class="width-md">
                <col class="width-sm">
                <col class="width-md">
                <col class="width-sm">
                <col class="width-md">
                <col class="width-sm">
                <col class="width-md">
            </colgroup>
            <tbody>
            <tr >
                <th >
                    미팅일자
                </th>
                <td >
                    <div v-show="isModify">
                        <date-picker v-model="project.meetingInfoExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="미팅일자" class=""></date-picker>
                        <div class="dp-flex-center mgt5">
                            <input type="text" placeholder="시간/장소" v-model="project.meetingInfoMemo" class="form-control w100">
                        </div>
                    </div>
                    <div v-show="!isModify">
                        {% $.formatShortDate(project.meetingInfoExpectedDt) %}
                        {% project.meetingInfoMemo %}
                    </div>
                </td>
                <th >
                    참석자
                </th>
                <td >
                    <div v-show="isModify">
                        <input type="text" placeholder="참석자" v-model="project.meetingMemberMemo" class="form-control" style="height:100%">
                    </div>
                    <div v-show="!isModify">
                        {% project.meetingMemberMemo %}
                    </div>
                </td>
                <th >
                    고객 안내일
                </th>
                <td >
                    <div v-show="isModify">
                        <date-picker v-model="project.custMeetingInformExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 안내일" class="" ></date-picker>
                    </div>
                    <div v-show="!isModify">
                        {% $.formatShortDate(project.custMeetingInformExpectedDt) %}
                    </div>
                </td>
                <th >
                    미팅보고서
                </th>
                <td >
                    <div v-show="isModify">
                        <simple-file-upload :file="fileList.fileEtc1" :id="'fileEtc1'" :project="project" ></simple-file-upload>
                    </div>
                    <div v-show="!isModify">
                        <simple-file-only-not-history-upload :file="fileList.fileEtc1" :project="project" ></simple-file-only-not-history-upload>
                        <span v-if="0 >= fileList.fileEtc1.files.length" class="text-muted">파일없음</span>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ================================================================================================ -->
<!-- 단계별 정보 영역 끝 -->


<!--  TO-DO LIST 영역 -->
<div class="row mgt20"></div>
<?php include './admin/ims/template/view/_infoTodo.php'?>

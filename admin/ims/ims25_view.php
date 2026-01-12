<!--
[ 프로젝트 상세 ]
Include 목록.
ims25_view_pre_sales : 사전영업 탭 화면
ims25_view_main : 메인 상세 탭 화면
? : 리오더 탭 화면
? : 기성복 관리 탭 화면
? : 고객 코멘트 탭 화면

ims25_view_style_field_config : 스타일 필드 설정

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
        <!--스타일 계산-->
        {% computedStyle %}
        <!--DL계산-->
        {% computedDeadLine %}
    </span>

    <div id="move-gnb"></div>

    <form id="frm">
        <div class="page-header js-affix">
            <div class="dp-flex dp-flex-gap15">
                <h3>
                <span class="text-danger" >
                    {% mainData.sno %}
                </span>

                    <span>
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

                    <span class="font-15 hand hover-btn" @click="openProjectStatusHistory(mainData.sno,'')">
                        ({% mainData.projectStatusKr %} 단계)
                    </span>

                    <!--프로젝트 상세정보-->
                </h3>
                <div class="ims-btn-row mgl35" >

                    <!--이전-->
                    <button class="ims-btn ims-btn--prev" type="button"
                            v-if="!$.isEmpty(STEP_MAP[mainData.projectStatus]['before']) && 95 > mainData.projectStatus "
                            @click="setStatus(STEP_MAP[mainData.projectStatus]['before']['status'], STEP_MAP[mainData.projectStatus]['before']['name'] + ' 단계로 변경 하시겠습니까?' )">
                        ◀ 이전단계({% STEP_MAP[mainData.projectStatus]['before']['name'] %}) 변경
                    </button>

                    <!--다음-->
                    <button class="ims-btn ims-btn--next" type="button"
                            v-if="!$.isEmpty(STEP_MAP[mainData.projectStatus]['after']) && 95 > mainData.projectStatus "
                            @click="setStatus(STEP_MAP[mainData.projectStatus]['after']['status'], STEP_MAP[mainData.projectStatus]['after']['name'] + ' 단계로 변경 하시겠습니까?' )">
                        다음단계({% STEP_MAP[mainData.projectStatus]['after']['name'] %}) 변경 ▶
                    </button>

                    <!--영업 재개 준비-->
                    <button class="ims-btn ims-btn--next" type="button"
                            v-if="mainData.projectStatus >= 95"
                            @click="setStatus(10, '영업 대기 단계로 변경 하시겠습니까?' )">
                        영업 재개
                    </button>

                    <!--발주준비이전까지는 유찰/보류 가능-->
                    <button class="ims-btn ims-btn--hold" type="button" v-if="50 > mainData.projectStatus" @click="setProjectHold(97)">
                        <i class="fa fa-stop-circle" aria-hidden="true" ></i> 영업보류 처리
                    </button>
                    <button class="ims-btn ims-btn--danger" type="button" v-if="50 > mainData.projectStatus" @click="setProjectHold(98)">
                        <i class="fa fa-times-circle" aria-hidden="true"></i> 유찰 처리
                    </button>
                </div>
            </div>

            <!--최상위 버튼-->
            <div class="btn-group">
                <!--
                <input type="button" value="수정" class="btn btn-white btn-red btn-red-line2" @click="setModify(true)" v-show="!isModify">
                <input type="button" value="저장" class="btn btn-red" @click="save()" v-show="isModify">
                <input type="button" value="수정취소" class="btn btn-red btn-red-line2" @click="setModify(false)" v-show="isModify">
                -->
                <input type="button" value="TO-DO 요청" class="btn btn-white" @click="openTodoRequestWrite(customer.sno,mainData.sno)" >

                <!--
                <input type="button" value="리오더" class="btn btn-white" @click="reOrder(mainData.sno)" >
                -->

                <input type="button" value="프로젝트분할" class="btn btn-white" @click="splitProject(mainData.sno)" >

                <input type="button" value="이슈 관리" class="btn btn-white" @click="openCommonPopup('project_issue_upsert', 1000, 910, {'sno':0,'customerSno':mainData.customerSno,'projectSno':mainData.sno});" >

                <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(mainData.sno, 'project')" >
                <?php if( !empty($requestParam['popup']) ) { ?>
                    <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
                <?php } ?>
            </div>
        </div>
    </form>

    <!--탭화면-->
    <div id="tabViewDiv">
        <ul class="nav nav-tabs mgb15" role="tablist">
            <?php if(!$imsProduceCompany) {?>

                <li role="presentation" :class="'sales' === tabMode?'active':''" @click="changeTab('sales')" id="tab1" >
                    <a href="#tab2" data-toggle="tab" >고객 영업 정보</a>
                </li>

                <li role="presentation" :class="'main' === tabMode?'active':''" @click="changeTab('main')" id="tab2" >
                    <a href="#tab2" data-toggle="tab" >프로젝트 정보</a>
                </li>

                <li role="presentation" :class="'customer' === tabMode?'active':''" @click="changeTab('customer')" id="tab3">
                    <a href="#tab3" data-toggle="tab" >고객 코멘트</a>
                </li>

                <li role="presentation" :class="'order' === tabMode?'active':''" @click="changeTab('order')" id="tab4">
                    <a href="#tab4" data-toggle="tab" >발주관리</a>
                </li>
                
                <li role="presentation" :class="'issue' === tabMode?'active':''" @click="changeTab('issue')" id="tab5">
                    <a href="#tab5" data-toggle="tab" >이슈관리</a>
                </li>


                <!--TODO : 프로젝트 타입에 따라 탭 생성 ( 리오더냐, 신규냐, 기성이냐 ) -->
                <li role="presentation" :class="'reOrder' === tabMode?'active':''" @click="changeTab('reOrder')" id="tab99" >
                    <a href="#tab99" data-toggle="tab" >TODO:리오더/기성 정보</a>
                </li>

                <!--
                <li role="presentation" :class="'comment' === tabMode?'active':''" @click="changeTab('comment')" id="tab5" v-if="commentList.length > 0">
                    <a href="#tab5" data-toggle="tab" >
                        프로젝트 코멘트
                    </a>
                </li>
                -->
            <?php }?>
        </ul>
    </div>

    <div class="row" v-if="!$.isEmpty(mainData.regDt)">

        <!--사전 영업 정보 (고객 단위 데이터 , 리오더류에는 표기하지 않음  ) -->
        <div class="row" v-show="'sales' === tabMode" >
            <!--사전영업 정보
            <br>신규고객에게만 보인다.
            <br>신규고객 = 발주하지 않은 고객-->
            <?php include 'ims25_view_pre_sales.php'?>
        </div>

        <!--프로젝트 정보-->
        <div class="row" v-show="'main' === tabMode" >
            <!--TODO : 파라미터 새롭게  _?php include 'ims25_view_main.php'?>-->
            <?php include 'ims25_view_main.php'?>
        </div>

        <!--고객 코멘트 (고객 정보)-->
        <div class="row" v-show="'customer' === tabMode">
            <div class="col-xs-12"  >
                <?php include 'template/ims_view_cust_comment.php'?>
                <!--변경 필요할 경우 ims25 cust comment 로 불러오기-->
            </div>
        </div>

        <!--구코멘트-->
        <div class="row" v-show="'comment' === tabMode">
            <div class="col-xs-12"  >
                <!--_?php include 'template/ims_project_view_comment.php' ?>-->
            </div>
        </div>


        <!-- TO-DO LIST : 고객 코멘트를 제외한 탭에서만 나온다.  -->
        <div class="row" v-show="'customer' !== tabMode && 'comment' !== tabMode && 'packing' !== tabMode">
            <div class="col-xs-12">
                <div class="col-xs-12 js-order-view-receiver-area relative">

                    <div class="table-title gd-help-manual">
                        <div class="flo-left">
                            TODO LIST
                        </div>
                        <div class="flo-right">

                            <div class="btn btn-white btn-sm" @click="openTodoRequestWrite(customer.sno,mainData.sno)">TODO등록</div>

                            <label class="radio-inline font-13">
                                <input type="radio" name="reqStatus" value=""  v-model="todoRequestSearchCondition.status"  @change="ImsTodoService.getListTodoRequest(1)" />전체
                            </label>
                            <label class="radio-inline font-13">
                                <input type="radio" name="reqStatus" value="ready"  v-model="todoRequestSearchCondition.status"  @change="ImsTodoService.getListTodoRequest(1)" />요청
                            </label>
                            <label class="radio-inline font-13">
                                <input type="radio" name="reqStatus" value="complete"  v-model="todoRequestSearchCondition.status" @change="ImsTodoService.getListTodoRequest(1)" />완료
                            </label>
                        </div>
                        <a href="#" target="_blank" class=""></a>
                    </div>

                    <div class="js-layout-order-view-receiver-info">
                        <table class="table table-cols table-default-center table-pd-0 table-td-height30 mgt5">
                            <colgroup>
                                <col class="w-5p"><!--번호-->
                                <col class="w-5p"><!--등록일-->
                                <col class="w-10p"><!--요청자-->
                                <col class=""><!--제목-->
                                <col class="w-8p"><!--완료 희망-->
                                <col class="w-50px"><!--체크-->
                                <!--<col class="w-50px">번호-->
                                <col class="w-8p"><!--대상자-->
                                <col class="w-8p"><!--처리자-->
                                <col class="w-8p"><!--완료예정-->
                                <col class="w-8p"><!--완료예정(남은일수)-->
                                <col class="w-10p"><!--상태-->
                            </colgroup>
                            <thead>
                            <tr>
                                <th>요청번호</th>
                                <th>등록일</th>
                                <th>요청자</th>
                                <th>제목<span class="font-10"><!--(댓글)--></span></th>
                                <th>완료 희망일</th>
                                <th>번호</th>
                                <th>대상자</th>
                                <th>처리자</th>
                                <th>완료 예정일</th>
                                <th>남은일수/완료일</th>
                                <th>상태</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(todoData , todoDataIndex) in todoRequestList" >
                                <!--번호-->
                                <td :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0" class="" style="padding-left:10px !important;">
                                    #{% Number(todoData.sno) %}
                                </td>
                                <!--등록일-->
                                <td :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0" class="" style="padding-left:10px !important;">
                                    {% $.formatShortDate(todoData.regDt) %}
                                </td>
                                <!--요청자-->
                                <td :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0" class="" style="padding-left:10px !important;">
                                    {% todoData.regManagerNm %}
                                </td>
                                <td class="ta-l " :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0" style="padding-left:15px!important;"><!--제목-->
                                    <span class="relative">
                                            <span v-html="todoData.subject" @click="openTodoRequest(todoData.sno,0)" class="hover-btn cursor-pointer "></span>
                                            <comment-cnt :data="todoData" ></comment-cnt>
                                        </span>
                                </td>
                                <td :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0"><!--완료희망일-->
                                    {% $.formatShortDate(todoData.hopeDt) %}
                                </td>
                                <!--<td :class="'todoStatusBack-' + todoData.status">
                                    <input type="checkbox" name="todoRequestSno[]" :value="todoData.sno" class="req-list-check">
                                </td>-->
                                <td :class="'todoStatusBack-' + todoData.status">
                                    {% (todoRequestTotal.idx-todoDataIndex) %}
                                </td>
                                <td :class="'todoStatusBack-' + todoData.status"><!--대상자-->
                                    {% todoData.targetManagerNm %}
                                    {% todoData.teamNm %}
                                </td>
                                <td :class="'todoStatusBack-' + todoData.status"><!--대상자-->
                                    {% todoData.completeManagerNm %}
                                </td>
                                <td :class="'todoStatusBack-' + todoData.status" ><!--완료예정일-->
                                    <span>{% $.formatShortDate(todoData.expectedDt) %}</span>
                                    <span v-if="$.isEmpty(todoData.expectedDt) || '0000-00-00' == todoData.expectedDt" class="text-muted font-11">미입력</span>
                                </td>
                                <td :class="'todoStatusBack-' + todoData.status" ><!--완료예정일(남은일수)-->
                                    <span v-html="$.remainDate(todoData.expectedDt,true)"
                                          v-if="'complete' !== todoData.status"></span>
                                    <span v-if="'complete' === todoData.status" class="font-12">
                                            {% $.formatShortDate(todoData.completeDt) %} 완료함
                                        </span>
                                </td>
                                <td class="ta-c" :class="'ta-c todoStatusBack-' + todoData.status" >
                                    <div :class="' pd0 bold todoStatus-' + todoData.status" >
                                        {% todoData.statusKr %}
                                    </div>
                                </td>
                            </tr>
                            <tr v-show=" 0 >= todoRequestList.length || $.isEmpty(todoRequestList.length) ">
                                <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                            </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row ta-c font-20" v-if="$.isEmpty(mainData.regDt)">
        로딩 실패 새로고침 해서 다시 불러오세요.
    </div>

    <!-- 우측 하단 플로팅 메뉴 -->
    <div class="ims-fab2" style="bottom:155px;">
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="!isModify" @click="setModify(true)">
            수정
        </button>
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="isModify" @click="saveWithStyle()">
            저장
        </button>
        <button type="button" class="ims-fab2-btn bg-white font-black" aria-label="취소" v-show="isModify" @click="setModify(false)">
            취소
        </button>
    </div>

    <!--추가 참여자 레이어-->
    <?php include 'ims25/ims25_view_layer.php' ?>

    <!--발송이력 레이어-->
    <send-history-layer-pop :title.sync="sendHistoryType" :visible.sync="sendHistoryVisible" :project-sno="mainData.sno"></send-history-layer-pop>

    <!--이메일 발송 레이어-->
    <email-sender-pop
            :visible.sync="emailPopVisible"
            :type="emailPopConfig.type"
            :project-sno="mainData.sno"
            :customer-sno="mainData.customerSno"

            :init-receiver="emailPopConfig.receiver"
            :init-email="emailPopConfig.email"
            :init-file-url="emailPopConfig.fileUrl">
    </email-sender-pop>

</div>

<script type="text/javascript">
    const viewPageName = 'main';
</script>

<?php include 'ims25_view_style_field_config.php'?> <!--스타일 필드 설정-->
<?php include 'ims25_view_script_ext_fnc.php' ?>
<?php include 'ims25_view_script_method.php' ?>
<?php include 'ims25_view_script_method2.php' ?>
<?php include 'ims25_view_script.php'?>

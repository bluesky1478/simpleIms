<style>
    .page-header { margin-bottom:10px };
    /*  a b c d e f g h i j k l m n o p q r script t u
    .mx-input {padding:0 !important; font-size:11px !important;}*/
    /*.mini-picker .mx-datepicker { width:100px!important; }*/

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
                    </span> 프로젝트 상세정보
                </h3>
                <div class="btn-group">

                    <input type="button" value="수정" class="btn btn-white btn-red btn-red-line2" @click="setModify(true)" v-show="!isModify">
                    <input type="button" value="저장" class="btn btn-red" @click="save()" v-show="isModify">
                    <input type="button" value="수정취소" class="btn btn-red btn-red-line2" @click="setModify(false)" v-show="isModify">

                    <input type="button" value="To-DoList 요청" class="btn btn-red btn-red-line2 btn-white" @click="openTodoRequestWrite(customer.sno,project.sno)" >

                    <input type="button" value="리오더" class="btn btn-red btn-red-line2 btn-white" @click="reOrder(project.sno)" >
                    <input type="button" value="프로젝트분할" class="btn btn-red btn-red-line2 btn-white" @click="splitProject(project.sno)" >
                    <!--<input type="button" value="최초 스케쥴 등록" class="btn btn-red btn-red-line2 btn-white" @click="isModifyPlan=true;" >-->
                    <input type="button" value="이슈 관리" class="btn btn-red btn-red-line2 btn-white" @click="openCommonPopup('project_issue_upsert', 1000, 910, {'sno':0,'customerSno':project.customerSno,'projectSno':project.sno});" >

                    <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(project.sno, 'project')" >
                    <?php if( !empty($requestParam['popup']) ) { ?>
                        <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
                    <?php }else{ ?>
                        <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
                    <?php } ?>

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
                    <li role="presentation" :class="'comment' === tabMode?'active':''" @click="changeTab('comment')" id="tab5" v-if="commentList.length > 0">
                        <a href="#tab5" data-toggle="tab" >
                            (구)프로젝트 코멘트
                        </a>
                    </li>
                <?php }?>
            </ul>
        </div>

        <div class="row" v-if="!$.isEmpty(project.regDt)">

            <!--프로젝트 정보-->
            <div class="row" v-show="'design' === tabMode" >
                <?php include 'ims25_view_add_template1.php'?>
            </div>

            <!--고객 코멘트 (고객 정보)-->
            <div class="row" v-show="'customer' === tabMode">
                <div class="col-xs-12"  >
                    <ul v-show="false">
                        <li>고객명</li>
                        <li>영담</li>
                        <li>고객사 담당자</li>
                        <li>3PL / 폐쇄몰 (관심도) </li>
                        <li>고개사 성향</li>
                    </ul>
                    <?php include 'template/ims_view_cust_comment.php'?>
                </div>
            </div>

            <!--구코멘트-->
            <div class="row" v-show="'comment' === tabMode">
                <div class="col-xs-12"  >
                    <?php include 'template/ims_project_view_comment.php' ?>
                </div>
            </div>

            <!-- TO-DO LIST : 고객 코멘트를 제외한 탭에서만 나온다.  -->
            <div class="row mgt20" v-show="'customer' !== tabMode && 'comment' !== tabMode">
                <div class="col-xs-12">
                        <div class="col-xs-12 js-order-view-receiver-area relative">

                            <div class="table-title gd-help-manual">
                                <div class="flo-left">
                                    TODO LIST
                                </div>
                                <div class="flo-right">
                                    
                                    <div class="btn btn-white btn-sm" @click="openTodoRequestWrite(customer.sno,project.sno)">TODO등록</div>
                                    
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

        <div class="row ta-c font-20" v-if="$.isEmpty(project.regDt)">
            로딩 실패 새로고침 해서 다시 불러오세요.
        </div>

    </div>

<?php include 'ims25_view_script_ext_fnc.php' ?>
<?php include 'ims25_view_script_method.php' ?>
<?php include 'ims25_view_script.php'?>
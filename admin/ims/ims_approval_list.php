<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>결재관리</h3>
            <div class="btn-group">
                <!--TODO : 일반결재 -->
                <input type="button" value="기안 등록" class="btn btn-red js-register" v-if="'approval' === tabMode" @click="openApprovalWrite()" />
            </div>
        </div>

    </form>

    <!-- TODO 검색 화면
    //고객명, 프로젝트번호, 스타일코드
    //연도, 시즌
    -->

    <!--탭화면-->
    <div id="tabViewDiv" >
        <ul class="nav nav-tabs mgb30" role="tablist">
            <li role="presentation" :class="'all' === tabMode?'active':''" @click="changeTab('all')" id="ims-tab-approval">
                <a href="#ims-tab-approval" data-toggle="tab" >전체</a>
            </li>
            <!--<li role="presentation" :class="'ready' === tabMode?'active':''" @click="changeTab('ready')" id="ims-tab-request">
                <a href="#ims-tab-request" data-toggle="tab" >기안중</a>
            </li>-->
            <li role="presentation" :class="'proc' === tabMode?'active':''" @click="changeTab('proc')" id="ims-tab-inbox">
                <a href="#ims-tab-inbox" data-toggle="tab" >진행중</a>
            </li>
            <li role="presentation" :class="'reject' === tabMode?'active':''" @click="changeTab('reject')" id="ims-tab-inbox">
                <a href="#ims-tab-inbox" data-toggle="tab" >반려</a>
            </li>
            <li role="presentation" :class="'accept' === tabMode?'active':''" @click="changeTab('accept')" id="ims-tab-inbox">
                <a href="#ims-tab-inbox" data-toggle="tab" >결재완료</a>
            </li>
            <!--<li role="presentation" :class="'ref' === tabMode?'active':''" @click="changeTab('ref')" id="ims-tab-inbox">
                <a href="#ims-tab-inbox" data-toggle="tab" >참조문서</a>
            </li>-->
        </ul>
    </div>

    <div class="row" >

        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-3xl">
                            <col class="width-md">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>검색어</th>
                            <td >
                                <div v-for="(keyCondition,multiKeyIndex) in todoApprovalSearchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchTodoApproval()" />
                                    <div class="btn btn-sm btn-red" @click="todoApprovalSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === todoApprovalSearchCondition.multiKey.length ">+추가</div>
                                    <div class="btn btn-sm btn-gray" @click="todoApprovalSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="todoApprovalSearchCondition.multiKey.length > 1 ">-제거</div>
                                    <span class="notice-info">다중 검색시 AND 검색</span>
                                </div>
                            </td>
                            <th>결재유형</th>
                            <td class="">
                                <select class="form-control">
                                    <option value="">전체</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>검색기간</th>
                            <td >
                                <div style="display: flex">
                                    <div class="pdr10">
                                        <select class="form-control" style="height:25px;" v-model="todoApprovalSearchCondition.searchDateType">
                                            <option value="a.regDt">요청일자</option>
                                            <option value="b.expectedDt">완료예정일</option>
                                            <option value="a.hopeDt">완료희망일</option>
                                            <option value="b.completeDt">완료일</option>
                                        </select>
                                    </div>
                                    <div>
                                        <date-picker v-model="todoApprovalSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="width:140px;font-weight: normal"></date-picker>
                                    </div>
                                    <div class="pd20 font-18">&nbsp;&nbsp;&nbsp;~</div>
                                    <div>
                                        <date-picker v-model="todoApprovalSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="width:140px;font-weight: normal;margin-left:10px"></date-picker>
                                    </div>

                                    <div class="form-inline" style="margin-left:30px">
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(todoApprovalSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(todoApprovalSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(todoApprovalSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                                    </div>

                                </div>
                            </td>
                            <th>
                                기안자/결재자
                            </th>
                            <td>
                                <?php if($isDev) { ?>
                                    <select2 class="js-example-basic-single" v-model="todoApprovalSearchCondition.approvalManagerSno"  style="width:45%" >
                                        <option value="">결재자</option>
                                        <?php foreach ($managerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                <?php }else{ ?>
                                    <span class="">
                                        <?=$managerInfo['managerNm']?>(<?=$managerInfo['managerId']?>)
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchTodoApproval()">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="todoApprovalConditionReset()">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
            </div>

            <div >
                <div class="">
                    <div class="flo-left mgb5 mgt25">
                        <span class="font-16 ">
                            총 <span class="bold text-danger">{% $.setNumberFormat(todoApprovalTotal.recode.total) %}</span> 건
                        </span>

                        <span v-if="'inbox' === tabMode">
                            선택항목 :
                            <div class="btn btn-gray" >처리완료</div>
                        </span>
                    </div>
                    <div class="flo-right mgb5">
                        <div class="bold font-18 ta-r" >결재관리 리스트</div>
                        <div style="display: flex">
                            <select @change="searchTodoApproval()" class="form-control" v-model="todoApprovalSearchCondition.sort">
                                <option value="D,desc">요청일 ▼</option>
                                <option value="D,asc">요청일 ▲</option>

                                <option value="T1,desc">희망일 ▼</option>
                                <option value="T1,asc">희망일 ▲</option>

                                <option value="T2,desc">예정일 ▼</option>
                                <option value="T2,asc">예정일 ▲</option>

                                <option value="T3,desc">완료일 ▼</option>
                                <option value="T3,asc">완료일 ▲</option>

                                <option value="B,desc">고객사별 ▼</option>
                                <option value="B,asc">고객사별 ▲</option>
                            </select>

                            <select v-model="todoApprovalSearchCondition.pageNum" @change="searchTodoApproval()" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="">

                    <table class="table table-rows table-default-center table-pd-0 table-td-height30 mgt5">
                        <colgroup>
                            <col class="w-50px"><!--체크-->
                            <col class="w-50px"><!--번호-->
                            <col class="w-100px"><!--상태-->
                            <col class="w-150px"><!--유형-->
                            <col class=""><!--제목-->
                            <col class="w-20p"><!--고객/프로젝트-->
                            <col class="w-100px"><!--기안자-->
                            <col class="w-15p"><!--결재자-->
                            <col class="w-100px"><!--등록일-->
                        </colgroup>
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="reqAllCheck" value="y" class="js-checkall" data-target-name="todoApprovalSno">
                            </th>
                            <th>번호</th>
                            <th>상태</th>
                            <th>결재유형</th>
                            <th>제목<span class="font-10"><!--(댓글)--></span></th>
                            <th style="height:45px!important;">고객/프로젝트</th>
                            <th>기안자</th>
                            <th>결재자</th>
                            <th>등록일</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(todoData , todoDataIndex) in todoApprovalList" >
                            <td :class="'todoStatusBack-' + todoData.status">
                                <input type="checkbox" name="todoApprovalSno[]" :value="todoData.sno" class="req-list-check">
                            </td>
                            <td :class="'todoStatusBack-' + todoData.status">
                                {% (todoApprovalTotal.idx-todoDataIndex) %}
                            </td>
                            <!--상태-->
                            <td class="ta-c" >
                                <div :class="' bold todoStatus-' + todoData.approvalStatus" >
                                    {% todoData.approvalStatusKr %}
                                </div>
                            </td>
                            <td class="">
                                {% todoData.approvalTypeKr %}
                                <div v-if="'ref' === todoData.targetType" class="font-12 text-muted">
                                    (참조)
                                </div>
                            </td>
                            <!--제목-->
                            <td class="ta-l " style="padding-left:15px!important;">
                                <span class="relative">
                                    <span v-html="todoData.subject" @click="openApprovalView(todoData.sno)" class="hover-btn cursor-pointer relative" ></span>
                                </span>
                                <span class="font-11 text-muted">({% todoData.regManagerNm %} {% todoData.regDt %} 등록 #{% Number(todoData.sno) %})</span>
                                <comment-cnt :data="todoData" ></comment-cnt>
                            </td>
                            <!--고객/프로젝트-->
                            <td class="ta-l " style="padding-left:10px !important;">
                                <div>
                                    <span v-if="todoData.customerSno > 0">
                                        <span class="sl-blue   mgb10" click="openCustomer(todoData.customerSno)">{% todoData.customerName %}</span>
                                        <span class="" v-html="todoData.projectYear"></span>
                                        <span class="" v-html="todoData.projectSeason"></span>
                                        <span class="" v-html="todoData.styleName"></span>
                                    </span>

                                    <span class="mgt5" v-if="todoData.projectSno > 0">
                                        <!--<span class="text-danger hover-btn cursor-pointer" @click="openProjectView(todoData.projectSno)">{% todoData.projectNo %}</span>-->
                                        <span class="text-danger hover-btn cursor-pointer" @click="openApprovalView(todoData.sno)">{% todoData.projectSno %}</span>
                                        <span class="text-muted">{% todoData.projectStatusKr %}</span>
                                    </span>
                                </div>
                                <div v-if="0 >= Number(todoData.customerSno) && 0 >= Number(todoData.projectSno)">
                                    일반 요청
                                </div>
                            </td>
                            <!--기안자-->
                            <td >
                                {% todoData.regManagerNm %}
                            </td>
                            <!--결재자-->
                            <td >
                                {% todoData.appManagersStr %}
                            </td>
                            <!--등록일-->
                            <td >
                                {% todoData.regDt %}
                            </td>
                        </tr>
                        <tr v-show=" 0 >= todoApprovalList.length || $.isEmpty(todoApprovalList.length) ">
                            <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="todoApproval-page" v-html="todoApprovalPage" class="ta-c"></div>

            </div>

        </div>

    </div>



    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_approval_list_script.php'?>


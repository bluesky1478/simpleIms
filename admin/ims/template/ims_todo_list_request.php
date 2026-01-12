<div id="tabViewDiv" class="col-xs-12 display-none">
    <ul class="nav nav-tabs mgb30" >
        <li role="presentation" :class="'request' === tabMode?'active':''">
            <a href="#" data-toggle="tab" @click="changeTab('request')">나의요청</a>
        </li>
        <li role="presentation" :class="'inbox' === tabMode?'active':''">
            <a href="#" data-toggle="tab" @click="changeTab('inbox')">받은요청</a>
        </li>
    </ul>
</div>

<!--결재관리-->
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
                        <div v-for="(keyCondition,multiKeyIndex) in todoRequestSearchCondition.multiKey" class="mgb5">
                            검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                            <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchTodoRequest()" />
                            <div class="btn btn-sm btn-red" @click="todoRequestSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === todoRequestSearchCondition.multiKey.length ">+추가</div>
                            <div class="btn btn-sm btn-gray" @click="todoRequestSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="todoRequestSearchCondition.multiKey.length > 1 ">-제거</div>
                            <span class="notice-info">다중 검색시 AND 검색</span>
                        </div>
                    </td>
                    <th>상태</th>
                    <td class="">
                        <label class="radio-inline ">
                            <input type="radio" name="reqStatus" value="" v-model="todoRequestSearchCondition.status" @change="searchTodoRequest()" />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="reqStatus" value="ready" v-model="todoRequestSearchCondition.status" @change="searchTodoRequest()" /> 요청
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="reqStatus" value="complete" v-model="todoRequestSearchCondition.status" @change="searchTodoRequest()" /> 완료
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>검색기간</th>
                    <td >
                        <div style="display: flex">
                            <div class="pdr10">
                                <select class="form-control" style="height:25px;" v-model="todoRequestSearchCondition.searchDateType">
                                    <option value="a.regDt">요청일자</option>
                                    <option value="b.expectedDt">완료예정일</option>
                                    <option value="a.hopeDt">완료희망일</option>
                                    <option value="b.completeDt">완료일</option>
                                </select>
                            </div>
                            <div>
                                <date-picker v-model="todoRequestSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="width:140px;font-weight: normal"></date-picker>
                            </div>
                            <div class="pd20 font-18">&nbsp;&nbsp;&nbsp;~</div>
                            <div>
                                <date-picker v-model="todoRequestSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="width:140px;font-weight: normal;margin-left:10px"></date-picker>
                            </div>

                            <div class="form-inline" style="margin-left:30px">
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(todoRequestSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(todoRequestSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(todoRequestSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                            </div>

                        </div>
                    </td>
                    <th>
                        요청자
                    </th>
                    <td>
                        <?php if($isDev) { ?>
                            <select2 class="js-example-basic-single" v-model="todoRequestSearchCondition.reqManagerSno"  style="width:100%" >
                                <option value="">전체</option>
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
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchTodoRequest()">
                        <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="todoRequestConditionReset()">
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
                    총 <span class="bold text-danger">{% $.setNumberFormat(todoRequestTotal.recode.total) %}</span> 건
                </span>

                <span v-if="'inbox' === tabMode">
                선택항목 :
                <div class="btn btn-gray" >처리완료</div>
                </span>

                <?php if(!$imsProduceCompany) { ?>
                    <!--<div class="btn btn-gray" @click="setRevokeQb(1)">요청상태로변경(임시)</div>-->
                <?php }else{ ?>
                    <!--<div class="btn btn-blue" @click="openRequestView()">처리완료</div>
                    <span class="notice-info">처리 완료된 항목을 다시 처리완료해도 적용되지 않습니다.</span>-->
                <?php } ?>
            </div>
            <div class="flo-right mgb5">

                <div class="bold font-18 ta-r" v-if="'inbox' === tabMode">받은요청</div>
                <div class="bold font-18 ta-r" v-if="'inbox' !== tabMode">나의요청</div>

                <div style="display: flex">
                    <select @change="searchTodoRequest()" class="form-control" v-model="todoRequestSearchCondition.sort">
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

                    <select v-model="todoRequestSearchCondition.pageNum" @change="searchTodoRequest()" class="form-control mgl5">
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
                    <col class="w-20p"><!--고객/프로젝트-->
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
                    <th style="height:45px!important;">고객/프로젝트</th>
                    <th>제목<span class="font-10"><!--(댓글)--></span></th>
                    <th>완료 희망일</th>
                    <!--<th>
                        <input type="checkbox" id="reqAllCheck" value="y" class="js-checkall" data-target-name="todoRequestSno">
                    </th>-->
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
                    <td :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0" class="ta-l " style="padding-left:10px !important;">
                        <div>
                            <div v-if="todoData.customerSno > 0">
                                <span class="sl-blue   mgb10" click="openCustomer(todoData.customerSno)">{% todoData.customerName %}</span>
                                <span class="" v-html="todoData.projectYear"></span>
                                <span class="" v-html="todoData.projectSeason"></span>
                                <span class="" v-html="todoData.styleName"></span>
                            </div>

                            <div class="mgt5" v-if="todoData.projectSno > 0">
                                <span class="text-danger hover-btn cursor-pointer" @click="openProjectView(todoData.projectSno)">{% todoData.projectSno %}</span>
                                <span class="text-muted">{% todoData.projectStatusKr %}</span>

                                <span class=" hover-btn cursor-pointer" v-if="1 === Number(todoData.productionStatus)">
                                    / <a :href=`/ims/imsProductionList.php?initStatus=0&key=prj.projectSno&keyword=${todoData.projectSno}` target="_blank" class="text-muted">생산진행중</a>
                                </span>
                                    <span class="text-muted hover-btn cursor-pointer" v-if="2 === Number(todoData.productionStatus)">
                                    / <a :href=`/ims/imsProductionList.php?initStatus=0&key=prj.projectSno&keyword=${todoData.projectSno}` target="_blank" class="text-muted">생산완료</a>
                                </span>
                            </div>

                        </div>
                        <div v-if="0 >= Number(todoData.customerSno) && 0 >= Number(todoData.projectSno)">
                            일반 요청
                        </div>
                    </td>
                    <td class="ta-l " :rowspan="todoData.reqSnoRowspan" v-if="todoData.reqSnoRowspan > 0" style="padding-left:15px!important;"><!--제목-->
                        <span class="relative">
                            <span v-html="todoData.subject" @click="openTodoRequest(todoData.sno,0)" class="hover-btn cursor-pointer "></span>
                            <comment-cnt :data="todoData" ></comment-cnt>
                        </span>
                        <div class="font-11 text-muted">({% todoData.regManagerNm %} {% todoData.regDt %} 등록 #{% Number(todoData.sno) %})</div>
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
                        <div :class="' bold todoStatus-' + todoData.status" >
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

        <div id="todoRequest-page" v-html="todoRequestPage" class="ta-c"></div>

    </div>

</div>


<!--처리완료 팝업-->

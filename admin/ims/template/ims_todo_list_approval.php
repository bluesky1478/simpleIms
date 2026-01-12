<!--결재관리-->
<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">

    <div id="tabViewDiv">
        <ul class="nav nav-tabs mgb30" role="tablist">
            <li role="presentation" class="" >
                <a href="#" data-toggle="tab" >전체</a>
            </li>
            <li role="presentation" class="" >
                <a href="#" data-toggle="tab" >기안중</a>
            </li>
            <li role="presentation" class="active" >
                <a href="#" data-toggle="tab" >진행중</a>
            </li>
            <li role="presentation" class="" >
                <a href="#" data-toggle="tab" >반려</a>
            </li>
            <li role="presentation" class="" >
                <a href="#" data-toggle="tab" >결재완료</a>
            </li>
        </ul>
    </div>


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
                        <div v-for="(keyCondition,multiKeyIndex) in estimateSearchCondition.multiKey" class="mgb5">
                            검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                            <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchEstimate()" />
                            <div class="btn btn-sm btn-red" @click="estimateSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === estimateSearchCondition.multiKey.length ">+추가</div>
                            <div class="btn btn-sm btn-gray" @click="estimateSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="estimateSearchCondition.multiKey.length > 1 ">-제거</div>
                        </div>
                        <div class="notice-info">다중 검색시 AND 검색</div>
                    </td>
                    <th>검색기간</th>
                    <td >
                        <div style="display: flex">
                            <div class="pdr10">
                                <select class="form-control" style="height:25px;" >
                                    <option value="">기안일자</option>
                                </select>
                            </div>
                            <div>
                                <date-picker vmodel="productionSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="width:140px;font-weight: normal"></date-picker>
                            </div>
                            <div class="pd20 font-18">&nbsp;&nbsp;&nbsp;~</div>
                            <div>
                                <date-picker vmodel="productionSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="width:140px;font-weight: normal;margin-left:10px"></date-picker>
                            </div>

                            <div class="form-inline" style="margin-left:30px">
                                <div class="btn btn-sm btn-white" click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                <div class="btn btn-sm btn-white" click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                <div class="btn btn-sm btn-white" click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                            </div>

                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="ta-c" style="border-bottom: none">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchEstimate()">
                        <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="estimateConditionReset()">
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
                    총 <span class="bold text-danger">{% $.setNumberFormat(estimateTotal.recode.total) %}</span> 건
                </span>
                <?php if(!$imsProduceCompany) { ?>
                    <!--<div class="btn btn-gray" @click="setRevokeQb(1)">요청상태로변경(임시)</div>-->
                <?php }else{ ?>
                    <!--<div class="btn btn-blue" @click="openRequestView()">처리완료</div>
                    <span class="notice-info">처리 완료된 항목을 다시 처리완료해도 적용되지 않습니다.</span>-->
                <?php } ?>
            </div>
            <div class="flo-right mgb5">

                <div class="bold font-18 ta-r">결재관리<!--(진행중 리스트)--></div>

                <div style="display: flex">
                    <select @change="searchEstimate()" class="form-control" v-model="estimateSearchCondition.sort">
                        <option value="D,desc">요청일 ▼</option>
                        <option value="D,asc">요청일 ▲</option>
                        <option value="A,desc">처리완료D/L ▼</option>
                        <option value="A,asc">처리완료D/L ▲</option>
                        <option value="B,desc">고객사별 ▼</option>
                        <option value="B,asc">고객사별 ▲</option>
                    </select>

                    <select v-model="estimateSearchCondition.pageNum" @change="searchEstimate()" class="form-control mgl5">
                        <option value="5">5개 보기</option>
                        <option value="20">20개 보기</option>
                        <option value="50">50개 보기</option>
                        <option value="100">100개 보기</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="">

            <table class="table table-rows table-default-center mgt5">
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="estimateSno">
                    </th>
                    <th>번호</th>
                    <th>구분</th>
                    <th>결재요청일</th>
                    <th>제목</th>
                    <th>고객사/프로젝트</th>
                    <th>요청자</th>
                    <th>결재자</th>
                    <th>진행상태</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(estimate , estimateIndex) in estimateList" >
                    <td>
                        <input type="checkbox" name="estimateSno[]" :value="estimate.sno" class="list-check">
                    </td>
                    <td>
                        <div class="font-14">
                            {% (estimateTotal.idx-estimateIndex) %}
                        </div>
                        <div class="text-muted font-11">(#{% Number(estimate.sno) %})</div>
                    </td>
                    <td><!--구분-->
                        기획
                    </td>
                    <td>
                        {% $.formatShortDate(estimate.regDt) %}
                    </td>
                    <td class="ta-l pdl10"><!--제목-->
                        <span v-html="estimate.reqMemoBr" @click="openApproval()" class="hover-btn cursor-pointer" @click=""></span>
                        <div class="btn btn-sm btn-white" @click="openApproval()">보기</div>
                    </td>
                    <td class="pdl10 ta-l" v-if="isList"><!--프로젝트-->
                        <div v-if="!isFactory">
                            <div class="font-14">
                                <span class="text-danger cursor-pointer hover-btn" @click="openProjectView(estimate.projectSno)">{% estimate.projectNo %}</span>
                                <span class="sl-blue cursor-pointer hover-btn" @click="openCustomer(estimate.customerSno)">{% estimate.customerName %}</span>
                            </div>
                        </div>
                    </td>
                    <td >
                        {% estimate.reqManagerNm %}
                    </td>
                    <td >
                        개발자
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        문상범
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        서재훈
                    </td>
                    <td class="ta-c">
                        <div class="font-14 bold" >
                            결재요청
                        </div>
                    </td>
                </tr>
                <tr v-show=" 0 >= estimateList.length || $.isEmpty(estimateList.length) ">
                    <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="estimate-page" v-html="estimatePage" class="ta-c"></div>

    </div>

</div>


<!--처리완료 팝업-->

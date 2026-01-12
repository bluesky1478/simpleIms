
<!-- 입찰형태 영역 -->
<div class="row ">
    <div class="col-xs-6">
        <div class="notice-info">입찰형태는 클릭시 즉시 저장</div>
        <table class="table table-cols w100 table-default-center table-pd-5 table-td-height35 table-th-height35 mgb10" >
            <colgroup>
                <col class="width-xs"/>
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
            </colgroup>
            <tr>
                <th>
                    입찰형태
                </th>
                <td class="text-left" colspan="99">
                    <label class="radio-inline">
                        <input type="radio" name="isSingleBid" value="단독진행" v-model="project.bidType" @click="saveSimpleData(project)" />단독진행
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="isSingleBid" value="입찰" v-model="project.bidType" @click="saveSimpleData(project)" />공개입찰
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="isSingleBid" value="" v-model="project.bidType" @click="saveSimpleData(project)" />미정
                    </label>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="clear-both"></div>

<!-- 스케쥴 영역 -->
<div class="row ">
    
    <!--공개입찰 영업희망일-->
    <div :class="!isModify?'col-xs-6':'col-xs-12'" v-show="'입찰' === project.bidType">
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
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
                <col class="width-md"/>
            </colgroup>
            <tr>
                <th>구분</th>
                <th>입찰 설명회</th>
                <th>기획서</th>
                <th>제안서</th>
                <th>가견적</th>
                <th>제안서 확정</th>
                <th>샘플</th>
                <th>생산가</th>
            </tr>
            <tr v-show="!isModify">
                <th>영업<br>희망일</th>
                <td>{% $.formatShortDate(project.addedInfo.info075) %}</td>
                <td>{% $.formatShortDate(project.addedInfo.info076) %}</td>
                <td>{% $.formatShortDate(project.addedInfo.info077) %}</td>
                <td>{% $.formatShortDate(project.addedInfo.info078) %}</td>
                <td>{% $.formatShortDate(project.addedInfo.info079) %}</td>
                <td>{% $.formatShortDate(project.addedInfo.info080) %}</td>
                <td>{% $.formatShortDate(project.addedInfo.info081) %}</td>
            </tr>
            <tr v-show="isModify">
                <th>영업 희망일</th>
                <td>
                    <date-picker v-model="project.addedInfo.info075" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="입찰 설명회" class=""></date-picker>
                </td>
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
                    <date-picker v-model="project.addedInfo.info079" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="제안서 확정" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info080" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="샘플" class=""></date-picker>
                </td>
                <td>
                    <date-picker v-model="project.addedInfo.info081" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="생산가" class=""></date-picker>
                </td>
            </tr>
        </table>
    </div>

    <!--기본일정-->
    <div :class="'입찰' !== project.bidType || isModify ? 'col-xs-12' : 'col-xs-6' " >
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
            </colgroup>
            <tr>
                <th>미팅일자/정보</th>
                <th>디자인</th>
                <th>생산</th>
                <th>내부미팅</th>
            </tr>
            <tr>
                <td class="text-center"><!--미팅정보-->
                    <div v-show="isModify">
                        <date-picker v-model="project.meetingInfoExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="미팅일자" class=""></date-picker>
                        <div class="dp-flex-center mgt5">
                            <input type="text" placeholder="시간/장소" v-model="project.meetingInfoMemo" class="form-control w100">
                        </div>
                    </div>
                    <div v-show="!isModify">
                        <div>{% project.meetingInfoExpectedDt %}</div>
                        <div>{% project.meetingInfoMemo %}</div>
                    </div>
                </td>
                <td class="text-center"><!--디자인-->
                    <div v-show="isModify">
                        <select class="form-control inline-block" v-model="project.designAgreeMemo" style="width:50%">
                            <option value="">미확인</option>
                            <option >준비중</option>
                            <option >준비완료</option>
                            <option >해당없음</option>
                        </select>
                    </div>
                    <div v-show="!isModify">
                        <div>{% project.designAgreeMemo %}</div>
                    </div>
                </td>
                <td class="text-center"><!--생산-->
                    <div v-show="isModify">
                        <select class="form-control w-90p inline-block" v-model="project.qcAgreeMemo" style="width:50%">
                            <option value="">미확인</option>
                            <option >준비중</option>
                            <option >준비완료</option>
                            <option >해당없음</option>
                        </select>
                    </div>
                    <div v-show="!isModify">
                        <div>{% project.qcAgreeMemo %}</div>
                    </div>
                </td>
                <td class="text-center"><!--유관부서 협의-->
                    <div v-show="isModify">
                        <date-picker v-model="project.allAgreeExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="유관부서 협의" class="" style="margin-top:0 !important;"></date-picker>
                        <div class="dp-flex-center mgt5">
                            <input type="text" placeholder="시간/기타" v-model="project.allAgreeMemo" class="form-control">
                        </div>
                    </div>
                    <div v-show="!isModify">
                        <div>{% project.allAgreeExpectedDt %}</div>
                        <div>{% project.allAgreeMemo %}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="clear-both" style="clear-both"></div>


<!-- 스타일 영역 -->
<?php include 'basic_type_style.php'?>

<!-- 단계별 정보 영역 시작 -->
<div class="row ">
    <div class="col-xs-12">
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                <span v-show="'입찰' === project.bidType">입찰 배경</span>
                <span v-show="'입찰' !== project.bidType">미팅 배경</span>
            </div>
            <div class="flo-right">
                <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">수정</div>
                <div class="btn btn-red btn-red2" @click="saveProject()" v-show="isModify">저장</div>
                <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">수정취소</div>
            </div>
        </div>
        <table class="table table-cols  xsmall-picker">
            <colgroup >
                <col class="width-md">
                <col class="">
                <col class="width-md">
                <col class="">
            </colgroup>
            <tbody>
                <tr >
                    <th >
                        <span v-show="'입찰' === project.bidType">개요</span>
                        <span v-show="'입찰' !== project.bidType">요청 배경</span>
                    </th>
                    <td colspan="3">
                        <div v-show="isModify">
                            <input type="text" class="form-control" v-model="project.addedInfo.info073">
                        </div>
                        <div v-show="!isModify">
                            {% project.addedInfo.info073 %}
                        </div>
                    </td>
                </tr>
                <tr >
                    <th >
                        영업의견
                    </th>
                    <td colspan="3">
                        <div v-show="isModify">
                            <textarea class="form-control" v-model="project.addedInfo.info074"></textarea>
                        </div>
                        <div v-show="!isModify" v-html="$.nl2br(project.addedInfo.info074)"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-------------------------------------------------------------------------------------------->
</div>

<div class="row">
    <?php $title = '사전 영업'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >현 유니폼 확보</th>
        <td colspan="3">
            <?php $radioKey='project.addedInfo.info070'; $textKey='project.addedInfo.info071'; $listCode='ableType'; $placeHolder='확보 추가 정보(예:유상 구매)' ?>
            <?php include '_radio_text.php'?>
        </td>
    </tr>
    <tr >
        <th style="height:85px">현장 조사</th>
        <td colspan="3">
            <div v-show="isModify">
                <textarea class="form-control" rows="5" placeholder="현장 조사 정보" v-model="project.addedInfo.info072"></textarea>
            </div>
            <div v-show="!isModify" v-html="$.nl2br(project.addedInfo.info072)"></div>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->
    <!--고객성향-->
    <?php include 'basic_type_tendency.php'?>

</div>

<!-- ================================================================================================ -->
<!-- 단계별 정보 영역 끝 -->


<!--  TO-DO LIST 영역 -->
<div class="row mgt20"></div>
<?php include './admin/ims/template/view/_infoTodo.php'?>

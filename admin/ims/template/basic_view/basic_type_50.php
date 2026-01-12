
<!-- 스케쥴 영역 -->
<?php include 'basic_type_schedule.php'?>

<!-- 스타일 영역 -->
<?php include 'basic_type_style.php'?>

<!-- 파일 영역 -->
<?php include 'basic_type_file.php'?>

<!-- 단계별 정보 영역 시작 -->



<!-- FIXME : TEST BEGIN -->

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

</div>

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


<!-- FIXME : TEST END -->

<!--대기사유 이슈사항-->
<div class="row " >
    <section v-if="[50].indexOf(Number(project.projectStatus)) !== -1">
    <?php $title = '대기 사유'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >고객 의사 결정</th>
        <td >
            <div v-show="!isModify">{% project.addedInfo.info023 %}</div>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.info023" placeholder="고객 의사 결정">
            </div>
        </td>
        <th >생산 기간 협의</th>
        <td >
            <div v-show="!isModify">{% project.addedInfo.info024 %}</div>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.info024" placeholder="생산 기간 협의">
            </div>
        </td>
    </tr>
    <tr >
        <th style="height:129px">대기사유</th>
        <td colspan="3">
            <div v-show="!isModify" v-html="$.nl2br(project.customerWaitMemo)">
            </div>
            <div v-show="isModify" >
                <textarea class="form-control" rows="6" v-model="project.customerWaitMemo" placeholder="협상 사유 및 진행 현황에 대해 6하 원칙에 의거하여 작성"></textarea>
            </div>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>
    </section>

    <!-------------------------------------------------------------------------------------------->

    <!--이슈사항-->
    <div class="col-xs-6">
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                이슈사항
            </div>
            <div class="flo-right">
                <!--<div class="btn btn-white" @click="changeTab('meeting')">고객 코멘트 보기</div>-->
                <a href="#" class="btn btn-white" @click="changeTab('meeting')">고객 코멘트 보기</a>
            </div>
        </div>
        <table class="table table-cols  xsmall-picker">
            <colgroup>
                <col class="width-sm">
                <col class="">
                <col class="width-xs">
                <col class="width-xs">
            </colgroup>
            <tr >
                <th class="text-left">구분</th>
                <th class="text-left">제목</th>
                <th class="text-left">등록자</th>
                <th class="text-left">등록일</th>
            </tr>
            <tbody v-if="0 >= issueList.length">
                <tr >
                    <th rowspan="3"  style="height:129px">
                        이슈 사항
                        <br>(이전 프로젝트)
                    </th>
                    <td colspan="99">등록된 이슈가 없습니다.</td>
                </tr>
            </tbody>
            <tbody >
                <tr v-for="(issue, issueKey) in issueList" v-if="3 > issueKey">
                    <th rowspan="3" v-if="0 === issueKey" style="height:129px">
                        이슈 사항
                        <br>(이전 프로젝트)
                    </th>
                    <td >
                        <div @click="openCustomerComment(items.sno, issue.sno, 'issue')" class="cursor-pointer hover-btn">
                            {% issue.subject %}
                        </div>
                    </td>
                    <td >
                        {% issue.regManagerNm %}
                    </td>
                    <td >
                        {% $.formatShortDateWithoutWeek(issue.regDt) %}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!--마크정보/스케쥴 공유 , 분류 패킹-->
<div class="row ">
    <?php $title = '마크정보/스케쥴 공유'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >마크정보</th>
        <td colspan="99">
            <div v-show="!isModify">
                {% getCodeMap()['existType3'][project.addedInfo.info029] %}
                <span v-show="!$.isEmpty(project.addedInfo.info030)">({% project.addedInfo.info030 %})</span>
            </div>

            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['existType3']">
                        <input type="radio" :name="'project-added-info-info029'"  :value="eachKey" v-model="project.addedInfo.info029"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5">
                    <input type="text" class="form-control" v-model="project.addedInfo.info030" placeholder="마크갯수 및 기타" >
                </div>
            </div>
        </td>
    </tr>
    <tr >
        <th >생산 스케쥴 고객 공유</th>
        <td colspan="99">

            <div v-show="!isModify">
                {% getCodeMap()['scheduleShareType'][project.addedInfo.info031] %}
            </div>
            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['scheduleShareType']">
                        <input type="radio" :name="'project-added-info-info031'"  :value="eachKey" v-model="project.addedInfo.info031"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
            </div>

        </td>
    </tr>
    <tr >
        <th >공유 받을 고객 정보</th>
        <td colspan="99">
            <div v-show="!isModify">
                <ul class="dp-flex dp-flex-gap10">
                    <li v-for="( item, index ) in project.addedInfo.shareCustomerInfo" class="dp-flex dp-flex-gap10">
                        {% item.name %}
                        <span class="text-muted" v-if="!$.isEmpty(item.phone+item.email)">
                            (
                             <span v-if="!$.isEmpty(item.phone)"><i class="fa fa-mobile fa-lg" aria-hidden="true"></i> {% item.phone %}</span>
                             <span v-if="!$.isEmpty(item.email)"><i class="fa fa-envelope" aria-hidden="true"></i> {% item.email %}</span>
                            )
                        </span>
                    </li>
                </ul>
            </div>
            <div v-show="isModify">
                <ul>
                    <li v-for="( item, index ) in project.addedInfo.shareCustomerInfo" class="dp-flex dp-flex-gap10 mgb10">
                        <div>
                            이름:<input type="text" class="form-control w-100px" v-model="item.name" placeholder="이름">
                        </div>
                        <div>
                            전화:<input type="text" class="form-control w-150px" v-model="item.phone" placeholder="전화">
                        </div>
                        <div>
                            이메일:<input type="text" class="form-control w-200px" v-model="item.email" placeholder="이메일">
                        </div>
                        <div>
                            <br>
                            <div class="btn btn-red btn-red-line2 btn-sm cursor-pointer hover-btn"
                                 @click="addElement(project.addedInfo.shareCustomerInfo, project.addedInfo.shareCustomerInfo[0], 'down', index)">추가</div>
                        </div>
                        <div>
                            <br>
                            <div class="btn btn-red btn-red-line2 btn-sm cursor-pointer hover-btn"
                                 @click="deleteElement(project.addedInfo.shareCustomerInfo, index)" v-show="project.addedInfo.shareCustomerInfo.length > 1">삭제</div>
                        </div>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->

    <?php $title = '분류 패킹'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >분류 패킹</th>
        <td colspan="">
            <div v-show="!isModify">
                {% getCodeMap()['processType'][project.packingYn] %}
                <span v-show="'y' === project.packingYn && !$.isEmpty(project.addedInfo.info034) ">
                    ({% project.addedInfo.info034 %})
                </span>
            </div>
            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['processType']">
                        <input type="radio" :name="'project-added-info-info033'"  :value="eachKey" v-model="project.packingYn"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5">
                    <input type="text" class="form-control" v-model="project.addedInfo.info034" v-show="'y' === project.packingYn" placeholder="분류패킹 기타 정보(예:0개지사)" >
                </div>
            </div>
        </td>
        <th >회신D/L</th>
        <td>
            <div v-show="!isModify">
                {% $.formatShortDate(project.addedInfo.info034) %}
            </div>
            <div v-show="isModify">
                <date-picker v-model="project.addedInfo.info034" value-type="format" :editable="false" ></date-picker>
            </div>
        </td>
    </tr>
    <tr >
        <th >패킹 방법</th>
        <td >
            <div v-show="!isModify">
                {% getCodeMap()['packingType'][project.addedInfo.info035] %}
                <span v-show="'etc' === project.addedInfo.info035 && !$.isEmpty(project.addedInfo.info035) ">({% project.addedInfo.info036 %})</span>
            </div>
            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['packingType']">
                        <input type="radio" :name="'project-added-info-info035'"  :value="eachKey" v-model="project.addedInfo.info035" />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5" v-show="'etc' === project.addedInfo.info035">
                    <input type="text" class="form-control" v-model="project.addedInfo.info036" placeholder="기타사항" >
                </div>
            </div>
        </td>
        <th >배송비 부담</th>
        <td >
            <div v-show="!isModify">
                {% getCodeMap()['payShippingType'][project.addedInfo.info037] %}
                <span v-show="'pay' === project.addedInfo.info037 && !$.isEmpty(project.addedInfo.info037) ">
                    ({% project.addedInfo.info038 %})
                </span>
            </div>
            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['payShippingType']">
                        <input type="radio" :name="'project-added-info-info037'"  :value="eachKey" v-model="project.addedInfo.info037" />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5" v-show="'pay' === project.addedInfo.info037">
                    <input type="text" class="form-control" v-model="project.addedInfo.info038" placeholder="유상 정보" >
                </div>
            </div>
        </td>
    </tr>
    <tr >
        <th >분류패킹파일</th>
        <td colspan="99">
            <simple-file-only-not-history-upload :file="fileList.filePacking" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
            <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="project" v-show="isModify"></simple-file-upload>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

</div>

<!--원부자재 비축 및 안전재고 / 3PL 폐쇄몰-->
<div class="row ">
    <?php $title = '원부자재 비축 및 안전재고'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >원부자재 보유 협의</th>
        <td colspan="99">
            <div v-show="!isModify">
                {% getCodeMap()['existType4'][project.addedInfo.info039] %}
                <span v-show="'y' === project.addedInfo.info039">({% project.addedInfo.info040 %}%)</span>
            </div>

            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['existType4']">
                        <input type="radio" :name="'project-added-info-info039'"  :value="eachKey" v-model="project.addedInfo.info039"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5 dp-flex" v-show="'y' === project.addedInfo.info039">
                    <input type="number" class="form-control w-70px mgl3" v-model="project.addedInfo.info040" placeholder="비율" maxlength="2">%
                </div>
            </div>
        </td>
    </tr>
    <tr >
        <th>
            시즌별 추가 발송 횟수
        </th>
        <td colspan="99">
            <span v-if="'n' === project.addedInfo.info039">해당없음</span>
            <span v-if="'n' !== project.addedInfo.info039">
            <div v-show="!isModify">
                {% getCodeMap()['existType4'][project.addedInfo.info041] %}
                <span v-show="'y' === project.addedInfo.info041">({% project.addedInfo.info042 %}회)</span>
            </div>

            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['existType4']">
                        <input type="radio" :name="'project-added-info-info041'"  :value="eachKey" v-model="project.addedInfo.info041"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5 dp-flex" v-show="'y' === project.addedInfo.info041">
                    <input type="number" class="form-control w-70px mgl3" v-model="project.addedInfo.info042" placeholder="횟수" maxlength="2">회
                </div>
            </div>
            </span>
        </td>
    </tr>
    <tr >
        <th>
            원부자재 결제 유무
        </th>
        <td colspan="99">
            <?php $key='info043'; $model='project.addedInfo.info043'; $listCode='payFabricType' ?>
            <span v-if="'n' === project.addedInfo.info039">해당없음</span>
            <span v-if="'n' !== project.addedInfo.info039"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th>
            안전재고 생산 유무
        </th>
        <td colspan="99">
            <?php $key='info044'; $model='project.addedInfo.info044'; $listCode='existType' ?>
            <span v-if="'n' === project.addedInfo.info039">해당없음</span>
            <span v-if="'n' !== project.addedInfo.info039"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th>
            안전재고 출고 방법
        </th>
        <td colspan="99">
            <?php $key='info045'; $model='project.addedInfo.info045'; $listCode='batchType' ?>
            <span v-if="'n' === project.addedInfo.info039">해당없음</span>
            <span v-if="'n' !== project.addedInfo.info039"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th>
            안전재고 결제 방법
        </th>
        <td colspan="99">
            <?php $key='info046'; $model='project.addedInfo.info046'; $listCode='paymentType2' ?>
            <span v-if="'n' === project.addedInfo.info039">해당없음</span>
            <span v-if="'n' !== project.addedInfo.info039"><?php include '_radio.php'?></span>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->

    <?php $title = '3PL/폐쇄몰'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >3PL 사용</th>
        <td colspan="">
            <?php $key='projectUse3pl'; $model='project.use3pl'; $listCode='usedType' ?>
            <?php include '_radio.php'?>
        </td>
        <th >폐쇄몰 사용</th>
        <td>
            <?php $key='projectUseMall'; $model='project.useMall'; $listCode='usedType' ?>
            <?php include '_radio.php'?>
        </td>
    </tr>
    <tr >
        <th >배송비 지급</th>
        <td colspan="">
            <?php $key='info047'; $model='project.addedInfo.info047'; $listCode='afterPaymentType' ?>
            <span v-if="'n' === project.useMall">해당없음</span>
            <span v-if="'n' !== project.useMall"><?php include '_radio.php'?></span>
        </td>
        <th >교환/반품</th>
        <td class="text-left">
            <?php $key='info048'; $model='project.addedInfo.info048'; $listCode='afterPaymentType' ?>
            <span v-if="'n' === project.useMall">해당없음</span>
            <span v-if="'n' !== project.useMall"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th >배송비 정산 주기</th>
        <td colspan="99">
            <?php $key='info049'; $model='project.addedInfo.info049'; $listCode='afterPaymentPeriod' ?>
            <span v-if="'n' === project.useMall">해당없음</span>
            <span v-if="'n' !== project.useMall"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th >출고 승인 사용</th>
        <td colspan="99">
            <?php $key='info050'; $model='project.addedInfo.info050'; $listCode='usedType' ?>
            <span v-if="'n' === project.useMall">해당없음</span>
            <span v-if="'n' !== project.useMall"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th >썸네일 타입</th>
        <td colspan="99">
            <?php $key='info051'; $model='project.addedInfo.info051'; $listCode='thumbnailType' ?>
            <span v-if="'n' === project.useMall">해당없음</span>
            <span v-if="'n' !== project.useMall"><?php include '_radio.php'?></span>
        </td>
    </tr>
    <tr >
        <th >회원가입 방식</th>
        <td colspan="99">
            <?php $key='info052'; $model='project.addedInfo.info052'; $listCode='memberJoinType' ?>
            <span v-if="'n' === project.useMall">해당없음</span>
            <span v-if="'n' !== project.useMall"><?php include '_radio.php'?></span>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

</div>

<!--결제내용 / 고객성향-->
<div class="row ">
    <?php $title = '결제 정보'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th>
            결제 방법
        </th>
        <td colspan="99">
            <?php $key='info056'; $model='project.addedInfo.info056'; $listCode='paymentType' ?>
            <?php include '_radio.php'?>
        </td>
    </tr>
    <tr >
        <th>
            결제 형태
        </th>
        <td colspan="99">
            <?php $key='info057'; $model='project.addedInfo.info057'; $listCode='afterPaymentType' ?>
            <?php include '_radio.php'?>
        </td>
    </tr>
    <!--<tr >
        <th>
            계산서 발행 업체 수
        </th>
        <td colspan="99">
            <?php /*$key='info058'; $model='project.addedInfo.info058'; $listCode='afterPaymentType' */?>
            <?php /*include '_radio.php'*/?>
        </td>
    </tr>-->
    <tr >
        <th>
            결제 일자
        </th>
        <td colspan="99">
            <?php $model='project.addedInfo.info059';?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="<?=$model?>" placeholder="예) 계산서 발행 후 익월말">
            </div>
            <div v-show="!isModify">
                {% <?=$model?> %}
            </div>

        </td>
    </tr>
    <tr >
        <th>
            계약금
        </th>
        <td colspan="99">
            <?php $key='info060'; $model1='project.addedInfo.info060'; $model2='project.addedInfo.info061'; $listCode='contractPayType' ?>
            <div v-show="!isModify">
                {% getCodeMap()['<?=$listCode?>'][<?=$model1?>] %}
                <span v-show="'contract' === <?=$model1?> || 'remain' === <?=$model1?>">{%$.setNumberFormat(<?=$model2?>)%}원</span>
            </div>

            <div v-show="isModify">
                <div class="">
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['<?=$listCode?>']">
                        <input type="radio" :name="'project-added-info-<?=$key?>'"  :value="eachKey" v-model="<?=$model1?>"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5">
                    <input type="number" class="form-control" v-model="<?=$model2?>"
                           v-show="'contract' === <?=$model1?> || 'remain' === <?=$model1?>" placeholder="금액(숫자만)">
                </div>
            </div>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->

    <?php $title = '고객성향'?>
    <?php include 'basic_type_tendency.php'?>

</div>

<div class="row">
    <?php $title = '기타사항'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >발주 물량 변동</th>
        <td colspan="3">
            <?php $key1 = 'info005'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    <tr >
        <th >계약기간</th>
        <td colspan="3">
            <?php $key1 = 'info006'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    <tr >
        <th >선호컨셉</th>
        <td colspan="3">
            <?php $key1 = 'info007'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    <tr >
        <th >선호컬러</th>
        <td colspan="3">
            <?php $key1 = 'info008'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
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





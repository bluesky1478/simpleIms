<div class="">
    <!--우측 정보-->
    <div class="col-xs-6" >
        <!-- 기본 정보 -->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객사 기본 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="_require">고객사명</th>
                        <td>
                            <?php $model='customer.customerName'; $placeholder='고객사명' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th class="_require">
                            Style code
                        </th>
                        <td>
                            <?php $model='customer.styleCode'; $placeholder='고객사명' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >영업담당자</th>
                        <td>
                            <div v-show="!isModify">
                                {% customer.salesManagerNm %}
                            </div>
                            <div v-show="isModify">
                                <select2 aclass="salesManagerSno" v-model="customer.salesManagerSno"  style="width:100%" >
                                    <?php foreach ($managerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                        </td>
                        <th>3PL/폐쇄몰</th>
                        <td >
                            <div v-show="!isModify">
                                <b>3PL:</b> {% customer.use3plKr %} ,
                                <b>폐쇄몰:</b> {% customer.useMallKr %}
                            </div>
                            <div v-show="isModify">
                                <div>
                                    3PL:
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="meetingUse3pl" value="n"  v-model="customer.use3pl" />사용안함
                                    </label>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="meetingUse3pl" value="y"  v-model="customer.use3pl" />사용
                                    </label>
                                </div>
                                <div class="mgt5">
                                    폐쇄몰:
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="meetingUseMall" value="n"  v-model="customer.useMall" />사용안함
                                    </label>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="meetingUseMall" value="y"  v-model="customer.useMall" />사용
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>업종</th>
                        <td>
                            <?php $model='customer.industry'; $placeholder='업종' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>사원수</th>
                        <td>
                            <?php $model='customer.addedInfo.etc2'; $placeholder='사원수' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>의사결정 라인</th>
                        <td>
                            <?php $model='customer.addedInfo.info089'; $placeholder='의사 결정 라인' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>노사 합의 여부</th>
                        <td >
                            <?php $model = 'customer.addedInfo.info088'; $listCode = 'existType3'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>기존 업체</th>
                        <td >
                            <?php $model='customer.addedInfo.info117'; $placeholder='기존 업체' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>계약 유지 기간</th>
                        <td>
                            <?php $model='customer.msContractMaintain'; $placeholder='계약 유지 기간' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>업체 변경 사유</th>
                        <td>
                            <?php $model='customer.addedInfo.info116'; $placeholder='업체 변경 사유' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>이해관계</th>
                        <td >
                            <?php $model='customer.addedInfo.info108'; $placeholder='이해관계' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>업체 선정 방법</th>
                        <td >
                            <?php $model='customer.addedInfo.info109'; $placeholder='업체 선정 방법' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>업체 선정 기준</th>
                        <td >
                            <?php $model='customer.addedInfo.info110'; $placeholder='업체 선정 기준' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>진행 가능성</th>
                        <td colspan="99">
                            <?php $model = 'customer.addedInfo.info111'; $listCode = 'ratingType'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!--고객사 성향-->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객사 성향</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35" >
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr >
                        <th >색상</th>
                        <td >
                            <?php $model = 'customer.addedInfo.info009'; $listCode = 'ratingType'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <th >품질</th>
                        <td >
                            <?php $model = 'customer.addedInfo.info010'; $listCode = 'ratingType'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >단가</th>
                        <td >
                            <?php $model = 'customer.addedInfo.info011'; $listCode = 'ratingType'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <th >납기</th>
                        <td class="pd0 ">
                            <?php $model = 'customer.addedInfo.info012'; $listCode = 'ratingType'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>기타</th>
                        <td colspan="3">
                            <?php $model='customer.contactMemo'; $placeholder='기타' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--미팅 취득 정보-->
        <div>
            <div>
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        미팅 취득 정보
                    </div>
                    <div class="flo-right"></div>
                </div>
                <div class="">
                    <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>폐쇄몰 관심도</th>
                            <td >
                                <?php $model = 'customer.addedInfo.info015'; $listCode = 'ratingType'?>
                                <?php include 'basic_view/_radio.php'?>
                            </td>
                            <th>착용 연령대</th>
                            <td >
                                <?php $model='customer.addedInfo.etc3'; $placeholder='착용 연령대' ?>
                                <?php include 'basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>샘플 확보 여부</th>
                            <td>
                                <?php $model = 'customer.addedInfo.info003'; $listCode = 'ableType'?>
                                <?php include 'basic_view/_radio.php'?>
                            </td>
                            <th>현장 조사</th>
                            <td>
                                <?php $model = 'customer.addedInfo.info072'; $listCode = 'ableType'?>
                                <?php include 'basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>샘플 반납 유무</th>
                            <td>
                                <div class="dp-flex">
                                    <?php $model = 'customer.addedInfo.info004'; $listCode = 'existType'?>
                                    <?php include 'basic_view/_radio.php'?>
                                </div>
                            </td>
                            <th>계약 주기</th>
                            <td>
                                <?php $model='customer.addedInfo.info101'; $placeholder='계약 주기' ?>
                                <?php include 'basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>경쟁 업체</th>
                            <td>
                                <?php $model='customer.addedInfo.info102'; $placeholder='경쟁 업체' ?>
                                <?php include 'basic_view/_text.php'?>
                            </td>
                            <th>리서치 가능 유무</th>
                            <td>
                                <?php $model = 'customer.addedInfo.info103'; $listCode = 'ableType'?>
                                <?php include 'basic_view/_radio.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>발주물량 변동사항</th>
                            <td>
                                <?php $model='customer.addedInfo.etc5'; $placeholder='발주물량 변동사항' ?>
                                <?php include 'basic_view/_text.php'?>
                            </td>
                            <th>유니폼 지급 주기</th>
                            <td>
                                <?php $model='customer.addedInfo.etc7'; $placeholder='유니폼 지급 주기' ?>
                                <?php include 'basic_view/_text.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!--좌측 정보-->
    <div class="col-xs-6" >
        <!-- 담당자 정보 -->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    담당자 정보
                </div>
                <div class="flo-right">

                </div>
            </div>
            <div class="">
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>담당자명</th>
                        <td >
                            <?php $model='customer.contactName'; $placeholder='담당자명' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>직함</th>
                        <td>
                            <?php $model='customer.contactPosition'; $placeholder='직함' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>연락처</th>
                        <td>
                            <?php $model='customer.contactMobile'; $placeholder='휴대전화' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>담당자 성향</th>
                        <td>
                            <?php $model='customer.contactPreference'; $placeholder='담당자 성향' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td colspan="3">
                            <?php $model='customer.contactEmail'; $placeholder='이메일' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>사무실 주소</th>
                        <td colspan="3">
                            <div v-show="!isModify">
                                {% customer.contactZipcode %}
                                {% customer.contactAddress %}
                                {% customer.contactAddressSub %}
                            </div>
                            <div v-show="isModify">
                                <div class="form-inline">
                                    <div title="주소를 입력해주세요!">
                                        <input type="text" class="form-control" v-model="customer.contactAddress"  style="width:100%" />
                                    </div>
                                    <div title="상세주소를 입력해주세요!" >
                                        <input type="text" class="form-control mgt5 w-100p" placeholder="상세주소" v-model="customer.contactAddressSub" style="width:100%" />
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 근무환경 / 고객 Needs -->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    근무환경 / 고객 NEEDS
                </div>
                <div class="flo-right">

                </div>
            </div>
            <div class="">
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>근무환경</th>
                        <td colspan="3">
                            <?php $model='customer.addedInfo.etc1'; $placeholder='근무환경' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>고객 NEEDS</th>
                        <td colspan="3">
                            <?php $model='customer.addedInfo.etc4'; $placeholder='고객 NEEDS' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--안내 / 제안사항-->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    안내 / 제안사항
                </div>
                <div class="flo-right"></div>
            </div>
            <div class="">
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>생산기간 안내</th>
                        <td >
                            <?php $model='customer.addedInfo.info104'; $placeholder='생산기간 안내' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>제작 샘플비 안내</th>
                        <td >
                            <?php $model='customer.addedInfo.info105'; $placeholder='제작 샘플비 안내' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>제안서 형태</th>
                        <td>
                            <?php $model='customer.addedInfo.info106'; $placeholder='제안서 형태' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>포트폴리오 제안일</th>
                        <td>
                            <?php $model='customer.addedInfo.info107'; $placeholder='포트폴리오 제안일' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>제안서 필수 항목</th>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.info112'; $placeholder='제안서 필수 항목' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>제안 컨셉 수</th>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.info115'; $placeholder='제안 컨셉 수' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>로고 형태</th>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.info113'; $placeholder='로고 형태' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>명찰 구분</th>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.info114'; $placeholder='명찰 구분' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 협상/미팅 이력 -->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    협상/미팅 이력
                </div>
                <div class="flo-right">
                    <div class="btn btn-sm btn-white mgb5" @click="openCustomerComment(customer.sno, 0, 'meeting')">
                        등록
                    </div>
                </div>
            </div>
            <div class="">
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35 table-pd-5 table-th-height30 table-td-height30">
                    <colgroup>
                        <col class="w-6p" />
                        <col class="w-10p" />
                        <col class=""/>
                    </colgroup>
                    <tr>
                        <th>번호</th>
                        <th>등록정보</th>
                        <th>제목</th>
                    </tr>
                    <tr v-if="0 >= meetingList.length" class="">
                        <td colspan="99">협상/미팅 이력이 없습니다.</td>
                    </tr>
                    <tr v-for="(each, eachIndex) in meetingList" class="">
                        <td class="text-center">
                            {% meetingList.length - eachIndex %}
                        </td>
                        <td>
                            <div>{% each.regManagerNm %}</div>
                            <div>{% $.formatShortDateWithoutWeek(each.regDt) %}</div>
                        </td>
                        <td class="text-left pdl5">
                            <div class="hover-btn cursor-pointer" @click="openCustomerComment(customer.sno, each.sno, 'meeting')">
                                <b>{% each.subject %}</b>
                            </div>
                            <div class="hover-btn cursor-pointer" @click="openCustomerComment(customer.sno, each.sno, 'meeting')">
                                {% each.textContents %}
                            </div>
                            <div class="font-11 dp-flex" >
                                <div class="sl-blue" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0">첨부 : </div>
                                <simple-file-only-not-history-upload :file="each.fileData" :id="'fileDataView'" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0"></simple-file-only-not-history-upload>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


    </div>

</div>




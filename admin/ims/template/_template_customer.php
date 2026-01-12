<div class="col-xs-6">
    <div class="table-title gd-help-manual">
        <div class="flo-left">고객사 기본 정보</div>
        <div class="flo-right">
            <!--
            <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
            <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyOrderInfo">저장</button>
            -->
        </div>
    </div>
    <div>
        <table class="table table-cols table-pd-5">
            <colgroup>
                <col class="width-sm">
                <col>
                <col class="width-sm">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th class="text-danger">고객사명</th>
                <td>
                    <input type="text" class="form-control width-lg" placeholder="고객사명" v-model="items.customerName">
                </td>
                <th class="text-danger">
                    Style code
                </th>
                <td>
                    <input type="text" class="form-control " placeholder="StyleCode에 들어가는 고객사명" v-model="items.styleCode">
                </td>
            </tr>
            <tr>
                <th >영업담당자</th>
                <td>
                    <select2 aclass="salesManagerSno" v-model="items.salesManagerSno"  style="width:100%" >
                        <?php foreach ($managerList as $key => $value ) { ?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                    </select2>
                </td>
                <th>고객상태</th>
                <td >
                    <select class="form-control" v-model="items.customerDiv">
                        <option value="0">잠재고객</option>
                        <option value="1">신규고객</option>
                        <option value="2">기존고객</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>3PL</th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="use3pl" value="n"  v-model="items.use3pl" />사용안함
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="use3pl" value="y"  v-model="items.use3pl" />사용
                    </label>
                </td>
                <th>폐쇄몰</th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="useMall" value="n"  v-model="items.useMall" />사용안함
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="useMall" value="y"  v-model="items.useMall" />사용
                    </label>
                </td>
            </tr>
            <tr>
                <th>계약연도</th>
                <td>
                    <input type="text" class="form-control " placeholder="계약연도" v-model="items.msContract">
                </td>
                <th>계약기간</th>
                <td>
                    <input type="text" class="form-control " placeholder="계약기간" v-model="items.msContractPeriod">
                </td>
            </tr>
            <tr>
                <th>계약유지기간</th>
                <td>
                    <input type="text" class="form-control " placeholder="계약유지기간" v-model="items.msContractMaintain">
                </td>
                <th>잔여계약기간</th>
                <td>
                    <input type="text" class="form-control " placeholder="잔여계약기간" v-model="items.msRemainPeriod">
                </td>
            </tr>
            <tr>
                <th>재계약조건</th>
                <td >
                    <input type="text" class="form-control " placeholder="재계약조건" v-model="items.msRecontractCondition">
                </td>
                <th>영업 형태</th>
                <td >
                    <input type="text" class="form-control " placeholder="어떤 형태로 영업이 되었는가 기입" v-model="items.salesType">
                </td>
            </tr>
            <tr>
                <th>업종</th>
                <td >
                    <input type="text" class="form-control width-lg" placeholder="업종(ex. 제조업)" v-model="items.industry">
                </td>
                <th>근무환경</th>
                <td >
                    <input type="text" class="form-control " placeholder="근무환경" v-model="items.addedInfo.etc1">
                </td>
            </tr>
            <tr>
                <th>직원수</th>
                <td>
                    <input type="text" class="form-control width-lg" placeholder="직원수(유니폼착용인원)" v-model="items.addedInfo.etc2">
                </td>
                <th>착용연령</th>
                <td>
                    <input type="text" class="form-control " placeholder="착용연령(ex. 30~40대)" v-model="items.addedInfo.etc3">
                </td>
            </tr>
            <tr>
                <th>고객 Needs</th>
                <td colspan="3">
                    <input type="text" class="form-control " placeholder="고객 Needs" v-model="items.addedInfo.etc4">
                </td>
            </tr>
            <tr>
                <th>발주물량 변동사항</th>
                <td colspan="3">
                    <input type="text" class="form-control " placeholder="발주물량 변동사항" v-model="items.addedInfo.etc5">
                </td>
            </tr>
            <tr>
                <th>현재 유니폼 제작 업체</th>
                <td>
                    <input type="text" class="form-control width-lg" placeholder="현재 유니폼 제작 업체" v-model="items.addedInfo.etc6">
                </td>
                <th>지급 주기</th>
                <td>
                    <input type="text" class="form-control " placeholder="지급 주기" v-model="items.addedInfo.etc7">
                </td>
            </tr>
            <tr>
                <th>현재 업체 계약 종료</th>
                <td colspan="3">
                    <input type="text" class="form-control " placeholder="현재 업체 계약 종료" v-model="items.addedInfo.etc8">
                </td>
            </tr>
            <tr>
                <th>계약서</th>
                <td colspan="3">
                    <!--<file-upload :file="fileList.fileEtc3" :id="'fileEtc3'" :project="project"></file-upload>-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- 기본 정보 -->

<!-- 담당자 정보 -->
<div class="col-xs-6" >
    <div class="table-title gd-help-manual ">
        <div class="flo-left">
            담당자 정보
        </div>
        <div class="flo-right"></div>
    </div>
    <div class="">
        <table class="table table-cols table-pd-5">
            <colgroup>
                <col class="width-md">
                <col class="width-xl">
                <col class="width-md">
                <col class="width-xl">
            </colgroup>
            <tbody>
            <tr>
                <th>담당자명</th>
                <td colspan="3">
                    <input type="text" class="form-control width-lg" placeholder="고객사 담당자명" v-model="items.contactName">
                </td>
            </tr>
            <tr>
                <th>직함</th>
                <td>
                    <input type="text" class="form-control width-lg" placeholder="직함(ex 수석)" v-model="items.contactPosition">
                </td>
                <th>부서</th>
                <td>
                    <input type="text" class="form-control " placeholder="부서명" v-model="items.contactDept">
                </td>
            </tr>
            <tr>
                <th>사무실 주소</th>
                <td colspan="3">
                    <div class="form-inline mgb5">
                        <span title="우편번호를 입력해주세요!">
                            <input type="text" name="zonecode" id="zonecode" size="6" maxlength="5" class="form-control js-number" data-number="5" readonly v-model="items.contactZipcode"  />
                        </span>
                        <input type="button" onclick="postcode_search('zonecode', 'address', 'zipcode');" value="우편번호찾기" class="btn btn-gray btn-sm"/>
                    </div>
                    <div class="form-inline">
                        <span title="주소를 입력해주세요!">
                            <input type="text" name="address" id="address" class="form-control width-3xl " readonly v-model="items.contactAddress"  />
                        </span>
                        <span title="상세주소를 입력해주세요!" class="mgt5">
                            <input type="text" name="addressSub" id="addressSub" class="form-control width-2xl mgt5" placeholder="상세주소" v-model="items.contactAddressSub" />
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>이메일</th>
                <td colspan="3">
                    <input type="text" class="form-control width-lg" placeholder="이메일" v-model="items.contactEmail">
                </td>
            </tr>
            <tr>
                <th>휴대전화</th>
                <td>
                    <input type="text" class="form-control width-lg" placeholder="휴대전화" v-model="items.contactMobile">
                </td>
                <th>내선번호</th>
                <td>
                    <input type="text" class="form-control " placeholder="내선번호" v-model="items.contactNumber">
                </td>
            </tr>
            <tr>
                <th>성별</th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="contactGender" value="M"  v-model="items.contactGender" />
                        남성
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="contactGender" value="F"  v-model="items.contactGender" />
                        여성
                    </label>
                </td>
                <th>나이</th>
                <td>
                    <input type="text" class="form-control " placeholder="나이" v-model="items.contactAge" >
                </td>
            </tr>
            <tr>
                <th>담당자 성향</th>
                <td colspan="3">
                    <input type="text" class="form-control w100" placeholder="담당자 성향" v-model="items.contactPreference">
                </td>
            </tr>
            <tr>
                <th>담당자 메모</th>
                <td colspan="3">
                    <textarea class="form-control " rows="4" v-model="items.contactMemo"></textarea>
                </td>
            </tr>
            <tr>
                <th>고객 기타사항</th>
                <td colspan="3">
                    <!--<input type="text" class="form-control " placeholder="기타" v-model="items.">-->
                    <textarea class="form-control " rows="4" v-model="items.addedInfo.etc99"></textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- 담당자 정보 -->
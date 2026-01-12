
<div class="col-xs-6" >
    <div class="table-title gd-help-manual">
        <div class="flo-left">고객사 기본 정보</div>
        <div class="flo-right">
            <button type="button" class="btn btn-red btn-sm mgb3" @click="openCustomerModify(items.sno)" >수정</button> &nbsp;
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-xl">
                <col class="width-md">
                <col class="width-xl">
            </colgroup>
            <tbody>
            <tr>
                <th class="require">고객사명</th>
                <td>
                    {% items.customerName %}
                </td>
                <th class="require">
                    Style code
                </th>
                <td>
                    {% items.styleCode %}
                </td>
            </tr>
            <tr>
                <th >영업담당자</th>
                <td>
                    {% items.salesManagerNm %}
                </td>
                <th>고객상태</th>
                <td >
                    {% items.customerDivKr %}
                </td>
            </tr>
            <tr>
                <th>3PL</th>
                <td>
                    <label class="radio-inline">
                        {% items.use3plKr %}
                    </label>
                </td>
                <th>폐쇄몰</th>
                <td>
                    <label class="radio-inline">
                        {% items.useMallKr %}
                    </label>
                </td>
            </tr>
            <tr>
                <th>계약연도</th>
                <td>
                    {% items.msContract %}
                </td>
                <th>계약기간</th>
                <td>
                    {% items.msContractPeriod %}
                </td>
            </tr>
            <tr>
                <th>계약유지기간</th>
                <td>
                    {% items.msContractMaintain %}
                </td>
                <th>잔여계약기간</th>
                <td>
                    {% items.msRemainPeriod %}
                </td>
            </tr>
            <tr>
                <th>재계약조건</th>
                <td >
                    {% items.msRecontractCondition %}
                </td>
                <th>영업 형태</th>
                <td >
                    {% items.salesType %}
                </td>
            </tr>
            <tr>
                <th>업종</th>
                <td >
                    {% items.industry %}
                </td>
                <th>근무환경</th>
                <td >
                    {% items.addedInfo.etc1 %}
                </td>
            </tr>
            <tr>
                <th>직원수</th>
                <td>
                    {% items.addedInfo.etc2 %}
                </td>
                <th>착용연령</th>
                <td>
                    {% items.addedInfo.etc3 %}
                </td>
            </tr>
            <tr>
                <th>고객 Needs</th>
                <td colspan="3">
                    {% items.addedInfo.etc4 %}
                </td>
            </tr>
            <tr>
                <th>발주물량 변동사항</th>
                <td colspan="3">
                    {% items.addedInfo.etc5 %}
                </td>
            </tr>
            <tr>
                <th>현재 유니폼<br> 제작 업체</th>
                <td>
                    {% items.addedInfo.etc6 %}
                </td>
                <th>지급 주기</th>
                <td>
                    {% items.addedInfo.etc7 %}
                </td>
            </tr>
            <tr>
                <th>현재 업체<br> 계약 종료</th>
                <td colspan="3">
                    {% items.addedInfo.etc8 %}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- 기본 정보 -->

<!-- 담당자 정보 -->
<div class="col-xs-6" >
    <div class="table-title gd-help-manual">
        <div class="flo-left">
            담당자 정보
        </div>
        <div class="flo-right">
            <button type="button" class="btn btn-red btn-sm mgb3" @click="openCustomerModify(items.sno)" >수정</button> &nbsp;
        </div>
    </div>
    <div class="">
        <table class="table table-cols">
            <colgroup>
                <col class="width-sm">
                <col class="width-md">
                <col class="width-sm">
                <col class="width-md">
            </colgroup>
            <tbody>
            <tr>
                <th>담당자명</th>
                <td colspan="3">
                    {% items.contactName %}
                </td>
            </tr>
            <tr>
                <th>직함</th>
                <td>
                    {% items.contactPosition %}
                </td>
                <th>부서</th>
                <td>
                    {% items.contactDept %}
                </td>
            </tr>
            <tr>
                <th>사무실 주소</th>
                <td colspan="3">
                    {% items.contactZipcode %}
                    {% items.contactAddress %}
                    {% items.contactAddressSub %}
                </td>
            </tr>
            <tr>
                <th>이메일</th>
                <td colspan="3">
                    {% items.contactEmail %}
                </td>
            </tr>
            <tr>
                <th>휴대전화</th>
                <td>
                    {% items.contactMobile %}
                </td>
                <th>내선번호</th>
                <td>
                    {% items.contactNumber %}
                </td>
            </tr>
            <tr>
                <th>성별</th>
                <td>
                    {% items.contactGenderKr %}
                </td>
                <th>나이</th>
                <td>
                    {% items.contactAge %}
                </td>
            </tr>
            <tr>
                <th>담당자 성향</th>
                <td colspan="3">
                    {% items.contactPreference %}
                </td>
            </tr>
            <tr>
                <th>메모</th>
                <td colspan="3">
                    {% items.contactMemo %}
                </td>
            </tr>
            <tr>
                <th>기타</th>
                <td colspan="3">
                    <textarea class="form-control" rows="9" disabled style="padding:8px; background-color: #f9f9f9">{% items.addedInfo.etc99 %}</textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- 담당자 정보 -->
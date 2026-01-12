
<form id="document-form">
    <div class="page-header js-affix">
        <h3>
            1차 미팅보고서
            <span class="confirm-block"><small>(등록 : 한동경 , 작성 : 2020/02/01 03:09:24, 최종수정 : 2020/02/02 17:05:34)</small></span>
        </h3>
        <div class="btn-group">
            <input type="button" value="문서 등록" class="btn btn-red js-register"/>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 confirm-block">
            <div class="table-title gd-help-manual">
                <div class="flo-left">승인 상태</div>
                <div class="flo-right">
                </div>
            </div>
            <div >
            <table class="table table-cols accept-table">
                <colgroup>
                    <col />
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <tr>
                    <th v-for="(acceptList, acceptIndex) in acceptList" :key="acceptIndex">{%acceptNm%}}</th>
                </tr>
                <tr>
                    <th v-for="(acceptList, acceptIndex) in acceptList" :key="acceptIndex">{%acceptStatus%}}</th>
                </tr>
            </table>
        </div>
        </div>

        <div class="col-xs-12 ">
            <div class="table-title gd-help-manual">
                <div class="flo-left">기본정보</div>
                <div class="flo-right">
                </div>
            </div>
            <div class="">
                <table class="table table-cols  w100">
                    <colgroup>
                        <col style="width:10%" />
                        <col style="width:23%" />
                        <col style="width:10%" />
                        <col style="width:23%" />
                        <col style="width:10%" />
                        <col style="width:23%" />
                    </colgroup>
                    <tr>
                        <th class="center">업체</th>
                        <td ><input type="text" name="업체" value="깨끗한나라" class="form-control"></td>
                        <th class="center">참석자</th>
                        <td ><input type="text" name="참석자" value="박경용 사원, 한동경 사원" class="form-control"></td>
                        <th class="center">미팅일자</th>
                        <td >
                            <div class="input-group js-datepicker" style="width:120px">
                                <input type="text" class="form-control width-xs" name="미팅일자" value="2021-08-26">
                                <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="center">구매형태</th>
                        <td >
                            <?= gd_select_box('', '구매형태', $projectCodeMap['구매형태'],null, '', null, null, 'form-control'); ?>
                        </td>
                        <th class="center">경쟁업체</th>
                        <td >
                            <?= gd_select_box('', '경쟁업체', $projectCodeMap['경쟁업체'],null, '', null, null, 'form-control'); ?>
                        </td>
                        <th class="center">업체선정요소</th>
                        <td >
                            <?= gd_select_box('', '업체선정요소', $projectCodeMap['업체선정요소'],null, '', null, null, 'form-control'); ?>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="col-xs-12 ">
            <div class="table-title gd-help-manual">
                <div class="flo-left">유니폼정보</div>
                <div class="flo-right">
                </div>
            </div>
            <div class="">
                <table class="table table-rows table-rows-soft  w100">
                    <colgroup>
                    </colgroup>
                    <tr>
                        <th class="center">품목</th>
                        <th class="center">예상수량</th>
                        <th class="center">현재단가</th>
                    </tr>
                    <tr>
                        <td ><input type="text" name="업체" value="춘추복" class="form-control"></td>
                        <td ><input type="text" name="업체" value="100~200" class="form-control"></td>
                        <td ><input type="text" name="업체" value="200000" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-xs-12 confirm-block">
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    문서 코멘트
                </div>
                <div class="flo-right">
                    <button type="button" class="btn btn-red-box btn-sm" data-submit-mode="modifyOrderInfo">코멘트등록</button>
                </div>
            </div>
            <div class="">
                <table class="table table-cols  w100">
                    <colgroup>
                        <col style="width:60px" />
                        <col  />
                        <col style="width:80px" />
                        <col />
                        <col class="width-xs" />
                    </colgroup>
                    <tr>
                        <th class="center">번호</th>
                        <th class="center">부서</th>
                        <th class="center">등록자</th>
                        <th class="center">내용</th>
                        <th class="center">등록시간</th>
                    </tr>
                    <tr>
                        <td class="center">5</td>
                        <td class="center">
                            [영업팀]
                        </td>
                        <td class="center">영업사원</td>
                        <td class="comment">
                            서대문 구청에서 진행하는 프로젝트를 등록했습니다.<br>샘플 내용입니당.
                            .<br>샘플 내용입니당.
                        </td>
                        <td class="center">2020/01/01</td>
                    </tr>
                    <tr>
                        <td class="center">4</td>
                        <td class="center">
                            [영업팀]
                        </td>
                        <td class="center">영업사원</td>
                        <td class="comment">서대문 구청에서 진행하는 프로젝트를 등록했습니다.</td>
                        <td class="center">2020/01/01</td>
                    </tr>
                    <tr>
                        <td class="center">3</td>
                        <td class="center">
                            [영업팀]
                        </td>
                        <td class="center">영업사원</td>
                        <td class="comment">서대문 구청에서 진행하는 프로젝트를 등록했습니다.</td>
                        <td class="center">2020/01/01</td>
                    </tr>
                    <tr>
                        <td class="center">2</td>
                        <td class="center">프로젝트</td>
                        <td class="center">영업사원</td>
                        <td>서대문 구청에서 진행하는 프로젝트를 등록했습니다.</td>
                        <td class="center">2020/01/01</td>
                    </tr>
                    <tr>
                        <td class="center">1</td>
                        <td class="center">프로젝트</td>
                        <td class="center">영업사원</td>
                        <td>서대문 구청에서 진행하는 프로젝트를 등록했습니다.</td>
                        <td class="center">2020/01/01</td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="col-xs-12 confirm-block" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">수정내역</div>
                <div class="flo-right">
                </div>
            </div>

            <div >
                <table class="table table-cols confirm-table">
                    <colgroup>
                        <col class="w100p"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th class="center">버전</th>
                        <th class="center">내용/확인자</th>
                    </tr>
                    <tr>
                        <th class="center">3</th>
                        <td>
                            <div>오기 수정</div>
                            <div class="confirm-line">
                                <ul class="confirm-user">
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="center">2</th>
                        <td>
                            <div>오기 수정</div>
                            <div class="confirm-line">
                                <ul class="confirm-user">
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="center">1</th>
                        <td>
                            <div>최초등록</div>
                            <div class="confirm-line">
                                <ul class="confirm-user">
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#송준호<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                    <li><small>#영업관리자<span class="color-gray">(02/01)</span></small></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</form>

<?php include 'project_doc_script.php' ?>


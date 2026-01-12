
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
                        <div v-for="(keyCondition,multiKeyIndex) in qbSearchCondition.multiKey" class="mgb5">
                            검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                            <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchQb()" />
                            <div class="btn btn-sm btn-red" @click="qbSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === qbSearchCondition.multiKey.length ">+추가</div>
                            <div class="btn btn-sm btn-gray" @click="qbSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="qbSearchCondition.multiKey.length > 1 ">-제거</div>
                        </div>
                        <div class="notice-info">다중 검색시 AND 검색</div>
                    </td>
                    <th>연도/시즌</th>
                    <td >
                        연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="qbSearchCondition.year" style="width:80px" />
                        시즌 :
                        <select class="form-control" name="projectSeason" v-model="qbSearchCondition.season">
                            <option value="">선택</option>
                            <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>상태</th>
                    <td >
                        <label class="radio-inline">
                            <input type="radio" name="qbStatus" value="0" v-model="qbSearchCondition.status" @change="searchQb(1)" />전체
                        </label>
                        <!--<label class="radio-inline">
                            <input type="radio" name="qbStatus" value="1" v-model="qbSearchCondition.status" @change="searchQb(1)" />처리 진행/완료건
                        </label>-->
                        <label class="radio-inline">
                            <input type="radio" name="qbStatus" value="2" v-model="qbSearchCondition.status" @change="searchQb(1)" />요청
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="qbStatus" value="3" v-model="qbSearchCondition.status" @change="searchQb(1)" />처리완료
                        </label>
                        <!--
                        <label class="radio-inline">
                            <input type="radio" name="qbStatus" value="4" v-model="qbSearchCondition.status" @change="searchQb()" />처리불가
                        </label>
                        -->
                        <label class="radio-inline">
                            <input type="radio" name="qbStatus" value="5" v-model="qbSearchCondition.status" @change="searchQb(1)" />반려
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="qbStatus" value="6" v-model="qbSearchCondition.status" @change="searchQb(1)" />확정
                        </label>
                    </td>
                    <?php if( empty($imsProduceCompany) ){ ?>
                        <th>의뢰처</th>
                        <td>
                            <select2 class="js-example-basic-single" style="width:200px" v-model="qbSearchCondition.reqFactory" >
                                <option value="0">전체</option>
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                    <?php }else{ ?>
                        <td colspan="99"></td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>
                        처리예정일<br>입력여부
                    </th>
                    <td >
                        <label class="radio-inline ">
                            <input type="radio" name="deadlineYn" value="0" v-model="qbSearchCondition.deadlineYn" @change="searchQb(1)"/>전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="deadlineYn" value="n" v-model="qbSearchCondition.deadlineYn" @change="searchQb(1)"/>미입력
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="deadlineYn" value="y" v-model="qbSearchCondition.deadlineYn" @change="searchQb(1)"/>입력
                        </label>
                    </td>
                    <th>
                        담당 디자이너
                    </th>
                    <td >
                        <select class="form-control w200p" v-model="qbSearchCondition.designManager">
                            <option value="all">전체</option>
                            <?php foreach ($designManagerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="ta-c" style="border-bottom: none">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchQb(1)">
                        <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="qbConditionReset()">
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

                <div class="dp-flex">

                    <span class="font-16 mgr10">
                    총 <span class="bold text-danger">{% $.setNumberFormat(qbTotal.recode.total) %}</span> 건
                    </span>

                    <?php if($isDev) { ?>
                        <div class="btn btn-gray display-none" @click="setRevokeQb(1)">요청상태로변경(임시)</div>
                    <?php } ?>

                    <?php if(!$imsProduceCompany) { ?>
                        <!--<div class="btn btn-gray" @click="setRevokeQb(2)">처리중으로변경</div>-->
                        <!--<div class="btn btn-blue" @click="openQbConfirm()">확정</div>-->
                    <?php }else{ ?>
                    <?php } ?>

                    <div class="mgr10">선택한 항목 :</div>
                    <input type="date" class="form-control mgr10" style="width:130px" id="qb-dead-line" >
                    <div class="btn btn-gray mgr10" @click="setDeadLine()">처리완료 예정일 등록</div>
                    <div class="mgr10 mgt5"> | </div>
                    <div class="btn btn-red" @click="openRequestView('')">임시저장</div>
                    <div class="mgr10 mgl10 mgt5"> | </div>
                    <div class="btn btn-blue" @click="openRequestView(3)">처리완료</div>
                </div>

                <!--
                <div class="btn btn-white" @click="alert('준비중')">확인완료</div>
                -->

                <!-- 처리불가 코멘트 등록 -->
                <!--
                <div class="btn btn-gray" @click="alert('작업중')">처리불가</div>
                -->

                <?php if(!$imsProduceCompany) { ?>
                    <!--
                    <span class="notice-info">확인완료시 이노버에서 내용을 수정할 수 업습니다.</span>
                    -->
                <?php }?>
            </div>
            <div class="flo-right mgb5">
                <div class="bold font-18 ta-r">퀄리티&BT 요청 리스트</div>
                <div style="display: flex">
                    <select @change="searchQb()" class="form-control" v-model="qbSearchCondition.sort">
                        <option value="D,desc">요청일 ▼</option>
                        <option value="D,asc">요청일 ▲</option>
                        <option value="A,desc">처리완료D/L ▼</option>
                        <option value="A,asc">처리완료D/L ▲</option>
                        <option value="B,desc">고객사별 ▼</option>
                        <option value="B,asc">고객사별 ▲</option>
                    </select>

                    <select v-model="qbSearchCondition.pageNum" @change="searchQb(1)" class="form-control mgl5">
                        <option value="20">20개 보기</option>
                        <option value="50">50개 보기</option>
                        <option value="100">100개 보기</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="">
            <table class="table table-rows table-default-center mgt5">
                <colgroup>
                    <col style="width:30px" /><!--체크-->
                    <col style="width:30px" /><!--번호-->
                    <col style="width:75px" /><!--상태-->

                    <col style="width:130px" /><!--의뢰처-->
                    <col style="width:300px" /><!--프로젝트스타일-->
                    <col style="width:200px" /><!--원단정보-->
                    <col style="width:100px" /><!--요청구분-->
                    <col style="width:26%" /><!--발송정보-->
                    <col style="width:16%" /><!--확정정보-->
                    <col style="width:100px" /><!--처리완료예정일-->
                    <col style="width:100px" /><!--요청일-->
                    <col style="width:100px" /><!--완료일-->
                </colgroup>
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="reqSno">
                    </th>
                    <th>번호</th>
                    <th>상태</th>
                    <th>의뢰처</th>
                    <th>프로젝트/스타일</th>
                    <th>원단 정보</th>
                    <th>요청구분</th>
                    <th>요청/처리/발송 정보</th>
                    <th>확정/반려 정보</th>
                    <th>요청일</th>
                    <th>처리완료예정일</th>
                    <th>완료일</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(fabric , fabricIndex) in qbList">
                    <td><!--체크-->
                        <input type="checkbox" name="reqSno[]" :value="fabric.sno" class="list-check">
                    </td>
                    <td><!--번호-->
                        <div>{% (qbTotal.idx-fabricIndex) %}</div>
                        <div class="text-muted font-14">(#{% Number(fabric.sno) %})</div>
                    </td>
                    <td><!--상태-->
                        <div :class="'bold reqStatus-'+fabric.reqStatus">{% fabric.reqStatusKr %}</div>
                    </td>
                    <td class="font-14">
                        {% fabric.reqFactoryNm %}
                    </td>
                    <td class="ta-l pdl5"><!--프로젝트/스타일-->
                        <?php if(!$imsProduceCompany) { ?>
                        <div>
                            <div>
                                <!-- window.open(`<?=$myHost?>/ims/ims_project_view.php?sno=${fabric.projectSno} -->
                                <span class="text-danger cursor-pointer hover-btn" @click="openProjectView(fabric.projectSno)">{% fabric.projectSno %}</span>
                                <span class="sl-blue cursor-pointer hover-btn" @click="openCustomer(fabric.customerSno)">{% fabric.customerName %}</span>
                            </div>
                            <div class="hover-btn cursor-pointer bold font-14" @click="openProductReg2(fabric.projectSno, fabric.styleSno, 1)">
                                {% fabric.styleFullName %} <div class="font-11 text-muted" style="font-weight: normal">({% fabric.styleCode %})</div>
                            </div>
                            <div class="font-11 mgt3">
                                영:{% fabric.salesManagerName %}
                                <span v-if="!$.isEmpty(fabric.designManagerName)">/ 디:{% fabric.designManagerName %}</span>
                                / 등록:{% fabric.reqManagerNm %}
                            </div>
                        </div>
                        <?php }else{ ?>
                        <div>
                            <div>
                                <span class="text-danger ">{% fabric.projectSno %}</span>
                                <span class="sl-blue ">{% fabric.customerName %}</span>
                            </div>
                            <div class="bold font-14" >
                                {% fabric.styleFullName %}
                                <div class="font-11 text-muted" style="font-weight: normal">({% fabric.styleCode %})</div>
                            </div>
                        </div>
                        <?php } ?>
                    </td>
                    <td class="ta-l pdl5"><!--원단정보-->
                        <div class="ta-l">
                            <span class="font-14 bold" >
                                <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-show="!$.isEmpty(fabric.makeNational)" ></i>
                                <span v-show="$.isEmpty(fabric.makeNational)"></span> {% fabric.position %} {% fabric.fabricName %}
                            </span>
                        </div>
                        <div class="font-12" >{% fabric.color %} {% fabric.fabricMix %}</div>
                        <div class="text-muted font-11" >{% fabric.fabricMemo %}</div>
                    </td>
                    <td>
                        <div>{% fabric.reqTypeKr %}</div>
                        <div class="font-11 mgt5 sl-blue">
                            요청{% fabric.reqCount %}회차
                        </div>
                    </td>
                    <td class="pd0"><!--양사 처리정보-->
                        <table class="table-borderless table table-pd-3 mg0 h100">
                            <colgroup>
                                <col style="width:100px" />
                                <col />
                            </colgroup>
                            <tr>
                                <td style="background-color: #f9f9f9!important;color:#000; border-top:none !important;" class="border-top-0">이노버</td>
                                <td class="ta-l border-top-0 pdl10" style=" border-top:none !important;">

                                    <div>{% fabric.reqDeliveryInfo %}</div>
                                    <ul v-if="fabric.fabricReqFile !== null && fabric.fabricReqFile.length > 0" class="mgl10">
                                        <li v-for="(file, fileIndex) in fabric.fabricReqFile">
                                            <?=$nasDownloadTag?>
                                        </li>
                                    </ul>

                                    <div>{% fabric.reqMemo %}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f9f9f9!important;color:#000">의뢰처</td>
                                <td class="ta-l pdl10" colspan="99">
                                    <div>{% fabric.resDeliveryInfo %}</div>
                                    <div>{% fabric.resMemo %}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f9f9f9!important;color:#000">현재 BT확정정보</td>
                                <td class="ta-l pdl10" colspan="99">
                                    <div>{% fabric.btConfirmInfo %}</div>
                                    <div>
                                        <ul v-if="fabric.btResultFile !== null && fabric.btResultFile.length > 0" class="mgl10">
                                            <li v-for="(file, fileIndex) in fabric.btResultFile">
                                                <?=$nasDownloadTag?>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="ta-l pdl5"><!--확정정보-->
                        <div v-html="fabric.confirmInfo"></div>
                        <div v-html="fabric.rejectMemo"></div>
                    </td>
                    <td><!--요청일-->
                        <span class="sl-blue font-14">{% $.formatShortDate(fabric.regDt) %}</span>
                    </td>
                    <td><!--처리완료예정일-->
                        <div><span class="text-danger font-14">{% $.formatShortDate(fabric.completeDeadLineDt) %}</span></div>
                    </td>
                    <td><!--완료일-->
                        <div v-if="!$.isEmpty($.formatShortDate(fabric.completeDt))" class="mgt5">
                            <span class="font-14">{% $.formatShortDate(fabric.completeDt) %}</span>
                        </div>
                    </td>
                </tr>
                <tr v-show=" 0 >= qbList.length">
                    <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="qb-page" v-html="qbPage" class="ta-c"></div>

    </div>

</div>

<!--처리완료 팝업-->
<div class="modal fade" id="modalRequestView" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:650px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    # 퀄리티&BT 요청 처리
                </span>
            </div>
            <div class="modal-body">
                <div v-if="3 === qbRequest.reqStatus">
                    <span class="text-danger bold">{% qbRequest.snoList.length %}</span>개의 원단을 완료 처리
                </div>
                <div v-if="3 !== qbRequest.reqStatus">
                    <span class="text-danger bold">{% qbRequest.snoList.length %}</span>개 임시 저장
                </div>
                <div class="">
                    <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                        <colgroup>
                            <col style="width:50px">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <!--
                        <tr >
                            <th>제조국</th>
                            <td colspan="99">
                                <div >
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value=""  v-model="qbRequest.makeNational"  />
                                        미정
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value="kr"  v-model="qbRequest.makeNational"  />
                                        <span class="flag flag-16 flag-kr">
                                    </label> 한국
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value="cn"  v-model="qbRequest.makeNational" />
                                        <span class="flag flag-16 flag-cn">
                                    </label> 중국
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value="market"  v-model="qbRequest.makeNational" />
                                        <span class="flag flag-16 flag-market">
                                    </label> 시장
                                </div>
                            </td>
                        </tr>
                        -->
                        <tr v-if="3 !== qbRequest.reqStatus">
                            <th>처리완료 예정일</th>
                            <td >
                                <date-picker v-model="qbRequest.completeDeadLineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="예정일"></date-picker>
                            </td>
                        </tr>
                        <tr>
                            <th>발송정보</th>
                            <td >
                                <input type="text" class="form-control" v-model="qbRequest.resDeliveryInfo" placeholder="발송정보" >
                            </td>
                        </tr>
                        <tr>
                            <th>
                                생산처 메모
                            </th>
                            <td >
                                <input type="text" class="form-control" v-model="qbRequest.resMemo" placeholder="생산처 메모" >
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn btn-red" @click="setCompleteQb()" v-if="3 !== qbRequest.reqStatus">
                    임시저장
                </div>
                <div class="btn btn-red" @click="setCompleteQb()" v-if="3 === qbRequest.reqStatus">
                    처리완료
                </div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>

        </div>
    </div>
</div>


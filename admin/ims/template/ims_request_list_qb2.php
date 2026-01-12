
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
                        <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, 'v-model="qbSearchCondition.key"', 'form-control'); ?>
                        <input type="text" name="keyword" class="form-control" v-model="qbSearchCondition.keyword"  @keyup.enter="searchQb()" />
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

                <span class="font-16 ">
                    총 <span class="bold text-danger">{% $.setNumberFormat(qbTotal.recode.total) %}</span> 건
                </span>

                <?php if($isDev) { ?>
                <div class="btn btn-gray" @click="setRevokeQb(1)">요청상태로변경(임시)</div>
                <?php } ?>

                <?php if(!$imsProduceCompany) { ?>
                    <!--<div class="btn btn-gray" @click="setRevokeQb(2)">처리중으로변경</div>-->
                    <!--<div class="btn btn-blue" @click="openQbConfirm()">확정</div>-->
                <?php }else{ ?>
                <?php } ?>

                <div class="btn btn-blue" @click="openRequestView()">처리완료</div>
                <span class="notice-info">처리 완료된 항목을 다시 처리완료해도 적용되지 않습니다.</span>

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
                    <col style="width:65px" /><!--상태-->
                    <col style="width:17%" /><!--프로젝트-->
                    <col style="width:12%" /><!--원단명-->
                    <col /><!--이노버 발송정보/메모-->
                    <col style="width:100px" /><!--요청일-->
                    <col style="width:160px" /><!--완료DL-->
                </colgroup>
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="reqSno">
                    </th>
                    <th>번호</th>
                    <th>상태</th>
                    <th>프로젝트/스타일</th>
                    <th>원단명(컬러/혼용률)</th>
                    <th>발송정보/메모/BT정보</th>
                    <th>요청일</th>
                    <th>완료D/L<br>완료일</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(fabric , fabricIndex) in qbList">
                    <td>
                        <input type="checkbox" name="reqSno[]" :value="fabric.sno" class="list-check">
                    </td>
                    <td>
                        <div>{% (qbTotal.idx-fabricIndex) %}</div>
                        <div class="text-muted font-14">(#{% Number(fabric.sno) %})</div>
                    </td>
                    <td>
                        <div :class="'bold reqStatus-'+fabric.reqStatus">{% fabric.reqStatusKr %}</div>
                        <div class="text-muted">{% fabric.reqCount %}회차</div>
                    </td>
                    <td class="ta-l pdl5">
                        <?php if(!$imsProduceCompany) { ?>
                        <div>
                            <div>
                                <!-- window.open(`<?=$myHost?>/ims/ims_project_view.php?sno=${fabric.projectSno} -->
                                <span class="text-danger cursor-pointer hover-btn" @click="openProjectView(fabric.projectSno)">{% fabric.projectNo %}</span>
                                <span class="sl-blue cursor-pointer hover-btn" @click="openCustomer(fabric.customerSno)">{% fabric.customerName %}</span>
                            </div>
                            <div class="hover-btn cursor-pointer bold font-14" @click="openProductReg2(fabric.projectSno, fabric.styleSno, 1)">
                                {% fabric.styleFullName %}
                            </div>
                        </div>
                        <?php }else{ ?>
                        <div>
                            <div>
                                <span class="text-danger ">{% fabric.projectNo %}</span>
                                <span class="sl-blue ">{% fabric.customerName %}</span>
                            </div>
                            <div class="bold font-14" >
                                {% fabric.styleFullName %}
                            </div>
                            <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5" @click="openProjectViewAndSetTabMode(fabric.projectSno,'comment')">코멘트보기</div>
                        </div>
                        <?php } ?>
                    </td>
                    <td class="ta-l pdl5">
                        <div class="ta-l">
                            <span class="font-14 bold" >
                                <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-show="!$.isEmpty(fabric.makeNational)" ></i>
                                <span v-show="$.isEmpty(fabric.makeNational)"></span> {% fabric.position %} {% fabric.fabricName %}
                            </span>
                        </div>
                        <div class="font-12" >{% fabric.color %} {% fabric.fabricMix %}</div>
                        <div class="text-muted font-11" >{% fabric.fabricMemo %}</div>
                    </td>
                    <td class="pd0">
                        <table class="table-borderless table table-pd-3 mg0 h100">
                            <colgroup>
                                <col style="width:9%" />
                                <col style="width:41%" />
                                <col style="width:12%" />
                                <col style="width:38%" />
                            </colgroup>
                            <tr>
                                <td style="background-color: #f9f9f9!important;color:#000; border-top:none !important;" class="border-top-0">이노버</td>
                                <td class="ta-l border-top-0 pdl10" style=" border-top:none !important;">
                                    <div>{% fabric.reqDeliveryInfo %}</div>
                                    <div>{% fabric.reqMemo %}</div>
                                    <div style="display: flex">
                                        <span>의뢰서 : </span>
                                        <ul v-if="fabric.fileList.btFile1.files.length > 0">
                                            <li v-for="(file, fileIndex) in fabric.fileList.btFile1.files">
                                                <?=$nasDownloadTag?>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td style="background-color: #f9f9f9!important;color:#000;border-top:none !important;" class="border-top-0">BT확정정보</td>
                                <td class="ta-l  pdl10 border-top-0" style="border-top:none !important;">
                                    <div><b>{% fabric.btConfirmInfo %}</b></div>
                                    <div class="">{% fabric.btMemo %}</div>
                                    <div style="display: flex">
                                        <ul v-if="fabric.fileList.btFile2.files.length > 0">
                                            <li v-for="(file, fileIndex) in fabric.fileList.btFile2.files">
                                                <?=$nasDownloadTag?>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f9f9f9!important;color:#000">의뢰처</td>
                                <td class="ta-l pdl10" colspan="99">
                                    <div>{% fabric.resDeliveryInfo %}</div>
                                    <div>{% fabric.resMemo %}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="font-14 bold">
                        {% $.formatShortDate(fabric.reqDt) %}
                    </td>
                    <td>
                        <div><span class="text-danger font-14 bold">{% $.formatShortDate(fabric.completeDeadLineDt) %}</span> 까지 요망</div>
                        
                        <div v-if="!$.isEmpty($.formatShortDate(fabric.completeDt))" class="mgt5">
                            <span class="sl-blue font-14 bold">{% $.formatShortDate(fabric.completeDt) %}</span>
                            <span>처리 완료</span>
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
                <div>
                    <span class="text-danger bold">{% qbRequest.snoList.length %}</span>개의 원단을 완료 처리
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
                <div class="btn btn-red" @click="setCompleteQb()" >처리완료</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>

        </div>
    </div>
</div>


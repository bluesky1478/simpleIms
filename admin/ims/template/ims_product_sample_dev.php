<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">

    <div v-if="-1 == product.sampleConfirmSno" class="font-18 mgb10 ta-c">
        이 스타일은 샘플 진행 안함
    </div>

    <div  style="" >
        <!--        openSampleView(-1, 'modify')-->
        <div class="btn btn-white" @click="openCommonPopup('product_sample_new', 1550, 900, {styleSno:product.sno, sno:0});"><i aria-hidden="true" class="fa fa-plus"></i> 샘플등록</div>
        <div class="btn btn-gray" @click="copySample()">샘플복사</div>
        <div class="btn btn-gray" @click="deleteSample()">샘플삭제</div>

        <!--
        <span v-show="0 >= sampleList.length">
            <div class="btn btn-gray" @click="ImsProductService.setSampleNothing(product.sno, product.projectSno)">샘플없음</div>
            <span class="notice-info">샘플 없이 진행시 선택</span>
        </span>
        -->
        <div id="sample-view">
            <table class="table table-rows table-default-center mgt5">
                <colgroup>
                    <col style="width:50px" /><!--체크-->
                    <col style="width:50px" /><!--번호-->
                    <col style="width:80px" /><!--확정샘플-->
                    <col style="width:80px" /><!--제작차수-->
                    <col style="width:80px" /><!--구분-->
                    <col style="width:90px" /><!--스타일기획-->
                    <col /><!--샘플명-->
                    <col style="width:110px"/><!--패턴실-->
                    <col style="width:110px"/><!--샘플실-->
                    <col style="width:50px" /><!--수량-->
                    <col style="width:80px" /><!--제작비용-->
                    <col style="width:70px" />
                    <col style="width:70px" />
                    <col style="width:70px" />
                    <col style="width:100px" /><!--환율-->
                    <!--<col style="width:250px" />기능-->
                    <col style="width:50px;"/><!--메모-->
                    <col style="width:60px" /><!--담당자-->
                    <col style="width:85px" /><!--등록수정-->
                </colgroup>
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="sampleSno">
                    </th>
                    <th>번호</th>
                    <th>확정샘플</th>
                    <th>제작차수</th>
                    <th>샘플구분</th>
                    <th>스타일기획</th>
                    <th>샘플명</th>
                    <th>패턴실</th>
                    <th>샘플실</th>
                    <th>수량</th>
                    <th>제작비용</th>
                    <th>샘플지시서</th>
                    <th>샘플리뷰서</th>
                    <th>샘플확정서</th>
                    <th>
                        환율<span @click="bFlagUpdateRatio=true;" v-show="!bFlagUpdateRatio" class="btn btn-sm btn-white">일괄수정</span>
                        <span @click="modifyDollerRatio()" v-show="bFlagUpdateRatio" class="btn btn-sm btn-blue">저장</span>
                        <span @click="bFlagUpdateRatio=false;" v-show="bFlagUpdateRatio" class="btn btn-sm btn-white">취소</span>
                    </th>
                    <!--<th>기능</th>-->
                    <th>메모</th>
                    <th>담당자</th>
                    <th>등록/수정일</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(sample , sampleIndex) in sampleList" :class="sampleView.sno === sample.sno ? 'choice-skyblue' : ''">
                    <td>
                        <input type="checkbox" name="sampleSno[]" :value="sample.sno" class="list-check">
                    </td>
                    <td>{% (sampleList.length-sampleIndex) %}</td>
                    <td>
                        <div v-show="'n' !== sample.sampleConfirm">
                            <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                            <span class="sl-green">(확정)</span>
                        </div>
                    </td>
                    <td ><!--제작차수-->
                        {% sample.sampleTerm %} 차
                    </td>
                    <td ><!--샘플구분-->
                        {% sample.sampleType==9?'구버전':sample.sampleTypeHan %}
                    </td>
                    <td ><!--스타일기획-->
                        {% sample.planConcept %}
                    </td>
                    <td >
                        <!--                        openSampleView(sampleIndex, 'view')-->
                        <div class="hover-btn cursor-pointer ta-l" style="padding:0 5px" @click="if (sample.sampleType==9) openCommonPopup('product_sample_detail', 1550, 900, {sno:sample.sno}); else openCommonPopup('product_sample_new', 1550, 900, {sno:sample.sno});"  >
                            <span class="text-muted ">(#{% Number(sample.sno)+1000 %})</span>
                            <span :class="(product.sampleConfirmSno > 0 && sample.sno == product.sampleConfirmSno)?'bold sl-green':'bold'" >{% sample.sampleName %}</span>
                        </div>
                        <div class="ta-l mgt5">
                            <!--modal open : ImsProductService.openEstimateCostReq(sampleIndex,'estimate')-->
                            <div class="btn btn-sm btn-white" @click="openCommonPopup('upsert_estimate', 1600, 900, {customerSno:sample.customerSno, projectSno:product.projectSno, sampleSno:sample.sno, styleQty:product.prdExQty});">가견적 요청</div>
                            <div class="btn btn-sm btn-gray" @click="confirmSample(sample,'y')" v-show="0 >= product.sampleConfirmSno">고객확정</div>
                            <div class="btn btn-sm btn-red" @click="confirmSample(sample,'n')" v-show="product.sampleConfirmSno > 0 && sample.sno == product.sampleConfirmSno">확정취소</div>
                        </div>
                    </td>
                    <td>
                        {% sample.patternFactoryName==null?'미선택':sample.patternFactoryName %}
                    </td>
                    <td>
                        {% sample.factoryName %}
                        <br>
                        <span class="text-muted font-11" v-if="!$.isEmpty($.formatShortDate(sample.sampleFactoryEndDt))">
                            마감:{% $.formatShortDate(sample.sampleFactoryEndDt) %}
                        </span>
                    </td>
                    <td>
                        {% sample.sampleCount %}
                    </td>
                    <td>
                        {% $.setNumberFormat(sample.sampleCost) %}원
                    </td>
                    <td>
                        <span v-if="sample.sampleType !== null && sample.sampleType != 9" @click="openProductWithSample(project.sno, sample.styleSno, sample.sno, sample.sampleType);" class="hover-btn cursor-pointer">보기</span>
                        <span v-else>
                            <simple-file-only-history-upload :file="sample.fileList.sampleFile1" :params="product" :file_div="'sampleFile1'" class="font-11"></simple-file-only-history-upload>
                        </span>
                    </td>
                    <td>
                        <span v-if="sample.sampleType !== null && sample.sampleType != 9" @click="openProductWithSample(project.sno, sample.styleSno, sample.sno, sample.sampleType, 'review');" class="hover-btn cursor-pointer">보기</span>
                        <span v-else>
                            <simple-file-only-history-upload :file="sample.fileList.sampleFile4" :params="product" :file_div="'sampleFile4'" class="font-11"></simple-file-only-history-upload>
                        </span>
                    </td>
                    <td>
                        <span v-if="sample.sampleType !== null && sample.sampleType != 9" @click="openProductWithSample(project.sno, sample.styleSno, sample.sno, sample.sampleType, 'confirm');" class="hover-btn cursor-pointer">보기</span>
                        <span v-else>
                            <simple-file-only-history-upload :file="sample.fileList.sampleFile6" :params="product" :file_div="'sampleFile6'" class="font-11"></simple-file-only-history-upload>
                        </span>
                    </td>
                    <td>
                        <span v-if="bFlagUpdateRatio">
                            <input type="text" v-model="sample.dollerRatio" ref="sampleDollerRatio" :data-sno="sample.sno" class="form-control" />
                        </span>
                        <span v-else>{% $.setNumberFormat(sample.dollerRatio) %}원</span>
                    </td>
                    <!--
                    <td class="ta-l">
                        <div class="btn btn-sm btn-white" @click="ImsProductService.openEstimateCostReq(sampleIndex,'estimate')">가견적 요청</div>
                        <div class="btn btn-sm btn-gray" @click="confirmSample(sample,'y')" v-show="0 >= product.sampleConfirmSno">고객확정</div>
                        <div class="btn btn-sm btn-red" @click="confirmSample(sample,'n')" v-show="product.sampleConfirmSno > 0 && sample.sno == product.sampleConfirmSno">확정취소</div>
                    </td>
                    -->
                    <td class="ta-l pdl10">
                        {% sample.sampleMemo %}
                    </td>
                    <td>
                        {% sample.sampleManagerNm %}
                    </td>
                    <td>
                        {% $.formatShortDate(sample.regDt) %}
                        <br>
                        <span class="text-muted">{% $.formatShortDate(sample.modDt) %}</span>
                    </td>
                </tr>
                <tr v-show=" 0 >= sampleList.length || $.isEmpty(sampleList.length)  ">
                    <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>


<!--샘플팝업-->
<div class="modal fade" id="modalSampleView" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog"  role="document" style="width:1350px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title" v-show="0 >= sampleView.sno">
                    <span class=" font-14">#{% product.productName %}의 신규 샘플 추가</span>
                    <span class="sl-blue">{% sampleView.sampleName %}</span>
                </span>
                <span class="modal-title font-18 bold" v-show="sampleView.sno > 0">
                    #{% Number(sampleView.sno)+1000 %} {% product.productName %}의 <span class="sl-blue">{% sampleView.sampleName %}</span>
                </span>
            </div>
            <div class="modal-body">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 pdl5">
                        # 기본정보
                        <span class="sl-green" v-if="'y' === sampleView.sampleConfirm">
                            ( 고객 확정 샘플 )
                        </span>
                    </div>
                    <div class="flo-right pdt5 pdl5 mgb5">
                        <!--TODO : <div class="btn btn-red" v-show="false">인쇄용 화면</div>-->

                        <div class="dp-flex dp-flex-gap15">
                            <div>
                                <span v-show=" (0 === sampleView.sno || $.isEmpty(sampleView.sno) ) && 'm' === viewModeSample ">
                                    <!--<span class="notice-info" >샘플번호를 입력하고 '샘플불러오기'를 하면 기존 정보를 불러옵니다.</span>-->
                                    <input type="text" class="form-control w130p inline-block" placeholder="샘플번호(숫자만)" v-model="loadSampleNo" >
                                    <div class="btn btn-gray" @click="loadSample()" v-show=" (0 === sampleView.sno || $.isEmpty(sampleView.sno) ) && 'm' === viewModeSample ">샘플 불러오기</div>
                                </span>
                            </div>

                            <div>
                                <span v-show=" (0 === sampleView.sno || $.isEmpty(sampleView.sno) ) && 'm' === viewModeSample ">
                                    <!--<span class="notice-info" >가견적 번호를 입력하고 '가견적 자재 불러오기'를 하면 기존 정보를 불러옵니다.</span>-->
                                    <input type="text" class="form-control w130p inline-block" placeholder="가견적번호(숫자만)" v-model="loadSampleNo" >
                                    <div class="btn btn-gray" @click="loadCostEstimate()" v-show=" (0 === sampleView.sno || $.isEmpty(sampleView.sno) ) && 'm' === viewModeSample ">가견적 자재 불러오기</div>
                                </span>
                            </div>
                            <button type="button" class="btn btn-white btn-icon-excel simple-download mgr3" @click="ImsProductService.fabricDownload(sampleView.sampleName)">원부자재 다운로드</button>
                            <div class="btn btn-white" @click="viewModeSample = 'm'" v-show="'m' !== viewModeSample ">수정하기</div>
                            <div class="btn btn-white" @click="viewModeSample = 'v'" v-show=" sampleView.sno > 0  && 'm' === viewModeSample ">수정취소</div>
                            <div class="btn btn-red" @click="saveSample()" v-show="'m' === viewModeSample ">저장</div>
                        </div>
                        <!-- TODO :  <div class="btn btn-red display-none">샘플확정</div>-->
                    </div>
                </div>

                <div class="">
                    <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>샘플명</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="sampleView.sampleName" placeholder="샘플명" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% sampleView.sampleName %}</span>
                            </td>
                            <th>담당자</th>
                            <td>
                                <div v-show="'m' === viewModeSample">
                                    <select2 class="js-example-basic-single" style="width:100%" v-model="sampleView.sampleManagerSno">
                                        <?php foreach ($designManagerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </div>
                                <span v-show="'m' !== viewModeSample">{% sampleView.sampleManagerNm %}</span>
                            </td>
                            <th>샘플실</th>
                            <td >
                                <div v-show="'m' === viewModeSample">
                                    <select2 class="js-example-basic-single" style="width:100%" v-model="sampleView.sampleFactorySno" v-show="'m' === viewModeSample">
                                        <?php foreach ($sampleFactoryMap as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </div>
                                <span v-show="'m' !== viewModeSample">
                                    {% sampleView.factoryName %}
                                </span>
                            </td>
                            <th>제작수량</th>
                            <td>
                                <select class="form-control font-16 inline-block" v-model="sampleView.sampleCount" v-show="'m' === viewModeSample">
                                    <option v-for="n in 10" :key="n">{% n %}</option>
                                </select>
                                <span v-show="'m' !== viewModeSample">{% sampleView.sampleCount %}</span> 개
                            </td>
                        </tr>
                        <tr>
                            <th>샘플 원단 단가</th>
                            <td class="font-16">
                                {% sampleView.fabricCost.toLocaleString() %}원
                            </td>
                            <th>샘플 부자재 단가</th>
                            <td class="font-16">
                                {% sampleView.subFabricCost.toLocaleString() %}원
                            </td>
                            <th>샘플 제작 단가</th>
                            <td class="font-16">
                                {% sampleView.sampleUnitCost.toLocaleString() %}원
                            </td>
                            <th>공임 비용(숫자만)</th>
                            <td class="font-16">
                                <span v-show="'m' !== viewModeSample">{% $.setNumberFormat(sampleView.addCost) %}원</span>
                                <input type="text" class="form-control" v-model="sampleView.addCost" v-show="'m' === viewModeSample">
                            </td>
                        </tr>
                        <tr>
                            <th>샘플실 투입일</th>
                            <td>
                                <div v-show="'m' === viewModeSample">
                                    <date-picker v-model="sampleView.sampleFactoryBeginDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </div>
                                <div v-show="'m' !== viewModeSample">{% $.formatShortDate(sampleView.sampleFactoryBeginDt) %}</div>
                            </td>
                            <th>샘플실 마감일</th>
                            <td>
                                <div v-show="'m' === viewModeSample">
                                    <date-picker v-model="sampleView.sampleFactoryEndDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </div>
                                <div v-show="'m' !== viewModeSample">{% $.formatShortDate(sampleView.sampleFactoryEndDt) %}</div>
                            </td>
                            <th>메모</th>
                            <td >
                                <input type="text" class="form-control" v-model="sampleView.sampleMemo" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% sampleView.sampleMemo %}</span>
                            </td>
                            <th>샘플 총 제작 비용</th>
                            <td class="text-danger bold" style="font-size:21px;" colspan="2" >
                                {% total %}원
                            </td>
                        </tr>
                        <tr v-show="$.isEmpty(sampleView.sno)">
                            <th >
                                샘플 지시서(필수)
                            </th>
                            <td colspan="99">
                                <simple-file-not-history-upload :file="sampleOrderFile" :id="'sampleOrderFile'"></simple-file-not-history-upload>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="myTable">

                    <table class="table table-cols table-default-center table-pd-3 table-td-height30 table-th-height30 mgb0" style="border-top:none!important">
                        <colgroup>
                            <col style="width:3%"><!--이동-->
                            <col style="width:5%"><!--위치-->
                            <col style="width:6%"><!--부착위치-->
                            <col style="width:13%"><!--자재명-->
                            <col style="width:8%"><!--혼용률-->
                            <col style="width:8%"><!--컬러-->
                            <col style="width:6%"><!--규격-->
                            <col style="width:6%"><!--가요척-->
                            <col style="width:6%"><!--단가-->
                            <col style="width:6%"><!--금액-->
                            <col style="width:10%"><!--샘플원단업체-->
                            <col style="width:14%"><!--비고-->
                            <col style="width:9%"><!--기능-->
                        </colgroup>
                        <tr>
                            <th colspan="99" style="padding:10px 5px 5px 5px!important; text-align: left;  height:1px; ">
                                <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> 샘플 <span class="sl-blue">원단정보</span>
                                <span class="font-11 normal" v-show="'m' === viewModeSample">가요척과 단가는 반드시 숫자로 입력하세요.( 금액 계산을 위함 )</span>
                            </th>
                        </tr>
                        <tr>
                            <th>이동</th>
                            <th>부위</th>
                            <th>부착위치</th>
                            <th>자재명</th>
                            <th>혼용율</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>가요척</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>샘플 원단 구매처</th>
                            <th>비고</th>
                            <th class="dn_hide">기능</th>
                        </tr><!--원단-->

                        <tbody  is="draggable" :list="sampleView.fabric"  :animation="200" tag="tbody" handle=".handle">
                        <tr v-for="(fabric, fabricIndex) in sampleView.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">

                            <td :class="'m' === viewModeSample ? 'handle' : '' ">
                                <div class="cursor-pointer hover-btn" v-show="'m' === viewModeSample">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" placeholder="부위" v-model="fabric.no" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.no %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" placeholder="부착위치" v-model="fabric.attached" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.attached %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.fabricName %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.fabricMix %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.color %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.spec %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.meas %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% $.setNumberFormat(fabric.unitPrice) %}</span>
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.price) %}
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="원단업체" v-model="fabric.fabricCompany" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.fabricCompany %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.memo %}</span>
                            </td>
                            <td class="ta-l dn_hide">
                                <div v-show="'m' === viewModeSample">
                                    <button type="button" class="btn btn-white btn-sm" @click="addElement(sampleView.fabric, sampleView.fabric[0], 'after')">+ 추가</button>
                                    <div class="btn btn-sm btn-red" @click="deleteElement(sampleView.fabric, fabricIndex)" v-show="sampleView.fabric.length > 1">- 삭제</div>
                                    <div class="btn btn-sm btn-red" v-show="1 >= sampleView.fabric.length" disabled="" title="최소 1개 필요">- 삭제</div>
                                </div>

                                <div v-show="'m' !== viewModeSample">
                                    <div class="btn btn-gray btn-sm" @click="ImsProductService.addQb(fabric, product.sno)">관리등록</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                    <!--부자재-->
                    <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
                        <colgroup>
                            <col style="width:3%"><!--이동-->
                            <col style="width:5%"><!--위치-->
                            <col style="width:6%"><!--부착위치-->
                            <col style="width:13%"><!--자재명-->
                            <col style="width:8%"><!--혼용률-->
                            <col style="width:8%"><!--컬러-->
                            <col style="width:6%"><!--규격-->
                            <col style="width:6%"><!--가요척-->
                            <col style="width:6%"><!--단가-->
                            <col style="width:6%"><!--금액-->
                            <col style="width:10%"><!--샘플원단업체-->
                            <col style="width:14%"><!--비고-->
                            <col style="width:9%"><!--기능-->
                        </colgroup>
                        <tr>
                            <th colspan="99" style="padding:10px 5px 5px 5px!important; text-align: left;  height:1px; ">
                                <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> 샘플 <span class="sl-blue">부자재</span> 정보
                                <span class="font-11 normal" v-show="'m' === viewModeSample">가요척과 단가는 반드시 숫자로 입력하세요.( 금액 계산을 위함 )</span>
                            </th>
                        </tr>
                        <tr>
                            <th>이동</th>
                            <th>부위</th>
                            <th colspan="3">자재명</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>가요척</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>부자재업체</th>
                            <th>비고</th>
                            <th>기능</th>
                        </tr><!--부자재-->


                        <tbody  is="draggable" :list="sampleView.subFabric"  :animation="200" tag="tbody" handle=".handle">
                        <tr v-for="(fabric, subFabricIndex) in sampleView.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                            <td :class="'m' === viewModeSample ? 'handle' : '' ">
                                <div class="cursor-pointer hover-btn" v-show="'m' === viewModeSample">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" placeholder="부위" v-model="fabric.no" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.no %}</span>
                            </td>
                            <td colspan="3">
                                <input type="text" class="form-control" placeholder="자재명" v-model="fabric.subFabricName" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.subFabricName %}</span>
                            </td>
                            <td >
                                <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.color %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.spec %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.meas %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% $.setNumberFormat(fabric.unitPrice) %}</span>
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.price) %}
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="구매업체" v-model="fabric.company" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.company %}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" v-show="'m' === viewModeSample">
                                <span v-show="'m' !== viewModeSample">{% fabric.memo %}</span>
                            </td>
                            <td class="ta-l">
                                <div v-show="'m' === viewModeSample">
                                    <button type="button" class="btn btn-white btn-sm" @click="addElement(sampleView.subFabric, sampleView.subFabric[0], 'after')">+ 추가</button>
                                    <div class="btn btn-sm btn-red" @click="deleteElement(sampleView.subFabric, subFabricIndex)" v-show="sampleView.subFabric.length > 1">- 삭제</div>
                                    <div class="btn btn-sm btn-red" disabled="" v-show="1 >= sampleView.subFabric.length" title="최소 1개 필요">- 삭제</div>
                                </div>

                                <div v-show="'m' !== viewModeSample">
                                    <div class="btn btn-gray btn-sm" @click="ImsProductService.addSubQb(fabric, product.sno)">관리등록</div>
                                </div>

                            </td>
                        </tr>
                        </tbody>

                    </table>

                </div>

                <div class="table-title gd-help-manual" v-show="'m' !== viewModeSample">
                    <div class="flo-left pdt5 pdl5">
                        # 파일정보
                    </div>
                </div>

                <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;" v-show="'m' !== viewModeSample">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tr >
                        <th class="text-danger">샘플의뢰서(지시서)</th>
                        <td colspan="3" class="relative">

                            <file-upload2 :file="sampleView.fileList.sampleFile1" :id="'sampleFile1'" :params="sampleView" :accept="false"></file-upload2>

                        </td>
                        <th>실물사진</th>
                        <td colspan="3">
                            <file-upload2 :file="sampleView.fileList.sampleFile2" :id="'sampleFile2'" :params="sampleView" :accept="false"></file-upload2>
                        </td>
                    </tr>
                    <tr >
                        <th>패턴</th>
                        <td colspan="3">
                            <file-upload2 :file="sampleView.fileList.sampleFile3" :id="'sampleFile3'" :params="sampleView" :accept="false"></file-upload2>
                        </td>
                        <th class="sl-blue">★샘플리뷰서</th>
                        <td colspan="3">
                            <file-upload2 :file="sampleView.fileList.sampleFile4" :id="'sampleFile4'" :params="sampleView" :accept="false"></file-upload2>
                        </td>
                    </tr>
                    <tr >
                        <th>기타파일</th>
                        <td colspan="3">
                            <file-upload2 :file="sampleView.fileList.sampleFile5" :id="'sampleFile5'" :params="sampleView" :accept="false"></file-upload2>
                        </td>
                        <th class="text-danger">★샘플확정서</th>
                        <td colspan="3">
                            <file-upload2 :file="sampleView.fileList.sampleFile6" :id="'sampleFile6'" :params="sampleView" :accept="false"></file-upload2>

                            <div>
                                * 샘플 확정서 등록시 자동으로 고객 확정 샘플이 됩니다.
                            </div>
                        </td>
                    </tr>
                </table>

            </div>

            <div class="modal-footer">
                <div class="btn btn-white" @click="viewModeSample='m';" v-show="'m' !== viewModeSample ">수정하기</div>
                <div class="btn btn-white" @click="viewModeSample = 'v'" v-show=" sampleView.sno > 0  && 'm' === viewModeSample ">수정취소</div>
                <div class="btn btn-red" @click="saveSample()" v-show="'m' === viewModeSample ">저장</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
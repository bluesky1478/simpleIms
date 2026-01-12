<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
    <div  style="" >
        <!--<div class="btn btn-white" @click="openFabricView(-1, 'modify')"><i aria-hidden="true" class="fa fa-plus"></i> 등록</div>-->
        <div class="flo-left pdt10">

            <div class="dp-flex dp-flex-gap5">
                <div>
                    <span class="font-16 mgr10">총 <span class="text-red bold font-16">{% $.setNumberFormat(costList.length) %}</span>건</span>
                </div>
                <div class="pdl20">
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="" v-model="costSearchCondition.estimateType" @change="ImsProductService.getCostListAndListRefresh(product.sno)"/> 전체
                    </label>
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="estimate" v-model="costSearchCondition.estimateType" @change="ImsProductService.getCostListAndListRefresh(product.sno)"/> 가견적
                    </label>
                    <label class="radio-inline" style="font-weight: normal;font-size:12px">
                        <input type="radio" name="syncProduct"  value="cost" v-model="costSearchCondition.estimateType"  @change="ImsProductService.getCostListAndListRefresh(product.sno)" /> 생산가확정
                    </label>
                </div>
            </div>

        </div>
        <div class="flo-right mgb5">
            <div class="btn btn-white" @click="openCostReq('estimate')" v-if="2 != product.prdCostStatus">가견적 요청</div>
            <div class="btn btn-red btn-red-line2" @click="openCostReq('cost')" v-if="2 != product.prdCostStatus">확정견적 요청</div>
            <div class="btn btn-gray hover-btn" @click="$.msg('확정된 생산가 취소 후 요청이 가능합니다..','','warning')" v-if="2 == product.prdCostStatus">견적 요청</div>
        </div>
        <div>

            <table class="table table-rows table-default-center mgt5 font-11">
                <colgroup>
                    <col style="width:50px" /><!--번호-->

                    <col style="width:80px" v-if="!isFactory && !isList" /><!--선택여부-->
                    <col style="width:180px" v-if="isList" /><!--고객/스타일-->
                    <col style="width:40px" /><!--요청회차-->
                    <col style="width:80px" /><!--견적타입-->
                    <col style="width:90px" /><!--진행상태-->

                    <col style="width:160px"/><!--원단MOQ-->
                    <col style="width:70px"/><!--메인원단 생지여부-->
                    <col style="width:160px"/><!--기능 단가변동-->

                    <col style="width:90px" v-if="!isFactory" /><!--의뢰처-->

                    <col style="width:70px" /><!--예정수량-->
                    <col style="width:100px" /><!--견적금액-->

                    <col style="width:160px"/><!--이노버 요청내용-->

                    <col style="width:60px" /><!--요청자-->
                    <col style="width:60px" /><!--처리DL/처리일-->
                    <col style="width:60px" /><!--요청일-->
                </colgroup>
                <thead>
                <tr>
                    <th>번호</th>
                    <th v-if="!isFactory && !isList">선택여부</th>
                    <th v-if="isList">고객/스타일</th>
                    <th>요청번호</th>
                    <th>견적타입</th>
                    <th>진행상태</th>

                    <th>원단설명/MOQ</th>
                    <th>메인원단<br>생지여부</th>
                    <th>기능<br>(단가변동/벌)</th>

                    <th v-if="!isFactory">의뢰처</th>
                    <th>수량</th>
                    <th>견적금액</th>

                    <th>이노버/생산처 작성 내용</th>

                    <th>요청자</th>
                    <th>처리D/L<br>처리일</th>
                    <th>요청일</th>
                </tr>
                </thead>
                <tbody class="">
                <tr v-for="(estimate , estimateIndex) in costList"
                    :class="product.prdCostConfirmSno == estimate.sno || product.estimateConfirmSno == estimate.sno ?'bg-light-yellow hover-light':'hover-light'">
                    <td>
                        {% (costTotal.idx-estimateIndex) %}
                        <div class="text-muted font-11">(#{% Number(estimate.sno) %})</div>
                    </td>
                    <td v-if="!isFactory && !isList ">
                        <!--확정-->
                        <div v-if="Number(product.prdCostConfirmSno) > 0">
                            <div v-if="product.prdCostConfirmSno == estimate.sno">
                                <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                                <div class="sl-green">생산가확정</div>
                                <div class="font-11 text-muted">{% product.prdCostConfirmManagerNm %}({% $.formatShortDate(product.prdCostConfirmDt) %})</div>
                            </div>
                        </div>
                        <!--확정 전-->
                        <div v-if="0 >= Number(product.prdCostConfirmSno)">
                            <div v-if="product.estimateConfirmSno == estimate.sno">
                                <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                                <span class="sl-green">(예정)</span>
                                <!--<div class="font-11 text-muted">{% product.estimateConfirmManagerNm %}({% $.formatShortDate(product.estimateConfirmDt) %})</div>-->
                                <div class="btn btn-sm btn-white mgt5" @click="cancelEstimate(project, product, estimate.sno)">예정가취소</div>
                            </div>

                            <div v-if="product.estimateConfirmSno != estimate.sno">
                                <div class="btn btn-sm btn-blue cursor-pointer" v-show="3 == estimate.reqStatus" @click="selectEstimate(project, product,estimate.sno)">예정가선택</div>
                                <div class="btn btn-sm btn-blue disabled" v-show="3 != estimate.reqStatus" title="생산처에서 처리완료시 선택가능">예정가선택</div>
                            </div>

                            <div v-if="product.estimateConfirmSno != estimate.sno && 'estimate' === estimate.estimateType" class="mgt5">
                                <div class="btn btn-sm btn-gray cursor-pointer" v-show="3 == estimate.reqStatus" @click="ImsProductService.openCostReqByBeforeEstimate(estimateIndex,'cost')">생산가요청</div>
                            </div>

                        </div>
                    </td>
                    <td class="pdl10 ta-l" v-if="isList">
                        <div class="cursor-pointer hover-btn " @click="openFactoryEstimateView(estimate.projectSno, estimate.styleSno, estimate.sno, 'estimate')"  v-if="isFactory">
                            <div class="dp-flex">
                                <div class="text-danger">{% estimate.projectSno %}</div>
                                <div class="font-14 bold sl-blue">{% estimate.customerName %}</div>
                            </div>
                            <div>
                                <div class="mgt5 bold">{% estimate.styleFullName %}</div>
                                <div class="font-11 text-muted" style="font-weight: normal">({% estimate.styleCode %})</div>
                            </div>
                        </div>
                        <div v-if="!isFactory">
                            <div>
                                <span class="text-danger cursor-pointer hover-btn" @click="openProjectView(estimate.projectSno)">{% estimate.projectSno %}</span>
                                <span class="sl-blue cursor-pointer hover-btn" @click="openCustomer(estimate.customerSno)">{% estimate.customerName %}</span>
                            </div>
                            <div class="hover-btn cursor-pointer bold font-14" @click="openProductReg2(estimate.projectSno, estimate.styleSno, 3)">
                                {% estimate.styleFullName %} <div class="font-11 text-muted" style="font-weight: normal">({% estimate.styleCode %})</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-16 text-danger bold">
                        #{% estimate.reqCount %}번
                    </td>
                    <td>
                        <div class="font-13 sl-blue" v-if="'estimate' === estimate.estimateType">
                            <i class="fa fa-quora" aria-hidden="true"></i>가견적
                        </div>
                        <div class="font-13 sl-green" v-if="'cost' === estimate.estimateType">
                            <i class="fa fa-krw" aria-hidden="true"></i>생산견적
                        </div>
                    </td>
                    <td class="ta-c">
                        <div class="ta-c" >
                            <div class="font-14 bold" >
                                {% estimate.reqStatusKr %}
                            </div>
                            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openFactoryEstimateView(estimate.projectSno, estimate.styleSno, estimate.sno, 'cost')">보기</div>
                            <div class="hover-btn cursor-pointer btn btn-sm btn-red btn-red-line2 mgt5"
                                 v-if="0 >= Number(product.prdCostConfirmSno) && 3==estimate.reqStatus && product.estimateConfirmSno != estimate.sno && !isFactory"
                                 @click="ImsProductService.reRequest(estimate.sno)">재요청</div>
                        </div>
                    </td>
                    <td class="pd5 ta-l font-11">
                        <span v-html="estimate.reqMemo1Br"></span>
                    </td>
                    <td  class="pd5 ta-l font-11">
                        <span v-html="estimate.reqMemo2Br"></span>
                    </td>
                    <td  class="pd5 ta-l font-11">
                        <span v-html="estimate.reqMemo3Br"></span>
                    </td>
                    <td v-if="!isFactory" class="font-11">
                        {% estimate.reqFactoryNm %}
                    </td>
                    <td class="font-13">
                        <span class="">{% $.setNumberFormat(estimate.estimateCount) %}</span>개
                    </td>
                    <td class="font-13">
                        <span class="text-danger">{% $.setNumberFormat(estimate.estimateCost) %}</span>원
                    </td>
                    <td class="ta-l pdl5 font-11" style="vertical-align: top!important;">
                        이노버 요청
                        <div v-html="estimate.reqMemoBr"></div>
                        <ul class="mgt5">
                            <li class="hover-btn" v-for="(file, fileIndex) in estimate.reqFiles">
                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                            </li>
                        </ul>
                        <hr>
                        생산처 답변
                        <div v-html="estimate.resMemoBr"></div>
                    </td>
                    <td class="font-11">
                        {% estimate.reqManagerNm %}
                    </td>
                    <td class="font-11">
                        <div>{% $.formatShortDate(estimate.completeDeadLineDt) %}</div>
                        <div>{% $.formatShortDate(estimate.completeDt) %}</div>
                    </td>
                    <td class="font-11">
                        {% $.formatShortDate(estimate.regDt) %}
                        <?php if(!empty(\SiteLabUtil\SlCommonUtil::isDevId()) || \SiteLabUtil\SlCommonUtil::isImsAdmin() ) { ?>
                            <div>
                                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " @click="ImsService.deleteData('estimate',estimate.sno, ImsRequestService.getListCost)">
                                    삭제
                                </div>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <tr v-show=" 0 >= costList.length || $.isEmpty(costList.length) ">
                    <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!--생산가 확정  요청-->
<div class="modal fade" id="modalCostReq" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    {% product.styleFullName %}의 <span class="sl-blue"></span> 생산견적 요청
                </span>
            </div>
            <div class="modal-body">
                <section >
                    <div class="table-title gd-help-manual">
                        <div class="flo-left pdt5 pdl5">
                            # 생산견적 요청
                        </div>
                        <div class="flo-right pdt5 pdl5">

                        </div>
                    </div>
                    <div class="">
                        <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                            <colgroup>
                                <col class="width-md">
                                <col class="width-xl">
                                <col class="width-md">
                                <col class="width-xl">
                            </colgroup>
                            <tbody>
                            <tr >
                                <th>의뢰처</th>
                                <td>
                                    <select2 class="js-example-basic-single" style="width:100%" v-model="costView.reqFactory">
                                        <option value="0">선택</option>
                                        <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <th>처리완료D/L</th>
                                <td>
                                    <date-picker v-model="costView.completeDeadLineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                            </tr>
                            <tr >
                                <th>요청내용</th>
                                <td class="pd0" colspan="99">
                                    <textarea class="form-control w100" rows="5" v-model="costView.reqMemo" placeholder="요청내용"></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
            <div class="modal-footer">
                <div class="btn btn-red" @click="ImsProductService.saveCostReq(0)">임시저장</div>
                <div class="btn btn-red" @click="ImsProductService.saveCostReq(1)">요청</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
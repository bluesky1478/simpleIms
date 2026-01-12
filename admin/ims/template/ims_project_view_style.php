<div class="col-xs-12" >
    <div >
        <div  class="relative">

            <ul class="nav nav-tabs mgb30" role="tablist">
                <li role="presentation" :class="'style' === viewMode?'active':''">
                    <a href="#tab-status-order" data-toggle="tab" @click="viewMode='style'; viewProductList=productList">사용 스타일({% productList.length %})</a>
                </li>
                <li role="presentation" :class="'styleTrash' === viewMode?'active':''">
                    <a href="#tab-status-cancel" data-toggle="tab" @click="viewMode='styleTrash'; viewProductList=removeProductList">스타일 휴지통({% removeProductList.length %})</a>
                </li>
            </ul>

            <div class="" style="position: absolute; top:0;right:0">
                <span class="pdl10 font-14">
                    총수량 : <span class="bold">{% $.setNumberFormat(project.totalCount) %}ea</span>
                </span>
                <span class="pdl10 font-14">
                    생산 TOTAL : <span class="text-danger bold">{% $.setNumberFormat(project.totalCost) %}원</span>
                </span>
                <span class="pdl10 font-14">
                    판매 TOTAL : <span class="sl-blue bold">{% $.setNumberFormat(project.totalSalePrice) %}원</span>
                    <span class="font-12" v-if="project.totalCost > 0 && project.totalSalePrice > 0">
                        (마진:  {%  $.setNumberFormat(project.totalSalePrice - project.totalFactoryCost)  %}원, {% (100-(Math.round(project.totalFactoryCost/project.totalSalePrice*100)) ) %}%)</span>
                </span>
            </div>
            <div class="" style="position: absolute; top:20px;right:5px">

                <span class="pdl10 font-11">
                    물류/관세 TOTAL : <span class="text-danger bold">{% $.setNumberFormat(project.totalDutyCostSum) %}원</span>
                </span>

                <!--<span class="pdl10 font-11">
                    물류/관세 TOTAL(스타일별총합) : <span class="text-danger bold">{% $.setNumberFormat(project.totalDutyCost) %}원</span>
                </span>-->
            </div>

            <div class="tab-content">
                <div class="table-action" style="margin-top:-31px;margin-bottom: 0px !important; border-top:solid 1px #888888">
                    <div class="pull-left form-inline" style="height: 26px; width:90%" v-show="'style' === viewMode">
                        <button type="button" class="btn btn-white" @click="openProductReg2(project.sno, '')"><i class="fa fa-plus" aria-hidden="true"></i> 스타일 추가</button>
                        <button type="button" class="btn btn-white" @click="copyProduct(project.sno)"><i class="fa fa-files-o" aria-hidden="true"></i> 스타일 복사</button>
                        <button type="button" class="btn btn-red btn-red-line2" @click="goTrashProduct(project.sno)"><i class="fa fa-trash-o" aria-hidden="true"></i> 스타일 삭제</button>

                        |

                        일괄견적 :
                        <select2 class="js-example-basic-single" style="width:150px" v-model="batchEstimateFactory" >
                            <option value="0">미정</option>
                            <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>

                        <button type="button" class="btn btn-blue-line" @click="goBatchEstimate(project.sno, 'estimate')"><i class="fa fa-krw" aria-hidden="true"></i> 가견적 일괄 요청</button>
                        <button type="button" class="btn btn-red-line2 btn-red" @click="goBatchEstimate(project.sno, 'cost')"><i class="fa fa-krw" aria-hidden="true"></i> 생산가 일괄 요청</button>

                        <!--<span class="notice-info mgl15">일괄견적시 업체 및 수량이 필수!</span>-->



                    </div>
                    <div class="pull-left form-inline" style="height: 26px;" v-show="'styleTrash' === viewMode">
                        <button type="button" class="btn btn-white" @click="recoveryProduct(project.sno)"><i class="fa fa-recycle" aria-hidden="true"></i> 스타일 복원</button>
                        <button type="button" class="btn btn-red btn-red-line2" @click="deleteProduct(project.sno)"><i class="fa fa-minus" aria-hidden="true"></i> 스타일 영구 삭제</button>
                    </div>
                    <div class="pull-right form-inline" style="height: 26px;">
                        <!--
                        <div class="display-inline-block">
                            <div class="btn btn-white" @click="copyProject(project.sno)"><i class="fa fa-files-o" aria-hidden="true"></i> 프로젝트 복사</div>
                        </div>
                        -->
                    </div>
                </div>
            </div>


            <div class="table-responsive mgt5">
                <table class="table table-rows">
                    <colgroup>
                        <col style="width:3%" /><!-- 체크 -->
                        <col style="width:3%" /><!-- 번호 -->
                        <col style="width:5%" /><!-- 이미지 -->
                        <col  /><!-- 스타일 -->
                        <col style="width:5%" /><!-- 수량 -->
                        <col style="width:7%" /><!-- 타겟가 -->
                        <col style="width:6%" /><!-- 판매단가 -->
                        <col style="width:6%" /><!-- 생산 확정가 -->
                        <col style="width:6%" /><!-- 생산 가견적 -->
                        <col style="width:3%" /><!-- 마진 -->
                        <col style="width:5%" /><!-- 샘플수 -->
                        <col style="width:5%" /><!-- 퀄리티 -->
                        <col style="width:4%" /><!-- BT -->
                        <col style="width:4%" /><!-- 생산견적 -->
                        <col style="width:4%" /><!-- 작업지시서 -->
                        <col style="width:4%" /><!-- 정렬 -->
                    </colgroup>
                    <thead>
                    <tr>
                        <th >
                            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno" v-show="'style' === viewMode">
                            <input type="checkbox" id="allDelCheck" value="y" class="js-checkall" data-target-name="prdDelSno" v-show="'styleTrash' === viewMode">
                        </th>
                        <th>번호</th>
                        <th>이미지</th>
                        <th>
                            스타일
                            <span>
                            <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('name')" style="color:#fff "></i>
                            <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('code')" style="color:#fff;"></i>
                            </span>
                        </th>
                        <th>
                            예정수량
                            <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cnt')" style="color:#fff "></i>
                        </th>
                        <th>타겟가</th>
                        <th>생산 가견적</th>
                        <th style="background-color: #0a6aa1 !important;">
                            생산 확정가 <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('cost')" style="color:#fff "></i>
                            <div class="font-11">(부가세 미포함)</div>
                        </th>
                        <th style="background-color: #0a6aa1 !important;">
                            판매단가 <i class="fa fa-files-o text-muted cursor-pointer font-14" aria-hidden="true" @click="copyStyleName('price')" style="color:#fff "></i>
                            <div class="font-11">(부가세 미포함)</div>
                        </th>
                        <th style="background-color: #0a6aa1 !important;">마진</th>
                        <th>샘플</th>
                        <th>퀄리티</th>
                        <th>BT</th>
                        <th>생산견적</th>
                        <th>작업지시서</th>
                        <th>
                            순서
                            <button type="button" class="btn btn-red btn-sm btn-red-line2" @click="saveSort(viewProductList)"> 저장</button>
                        </th>
                    </tr>
                    </thead>
                    <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-for="(product, prdIndex) in viewProductList">
                    <tr>
                        <td class="center" rowspan="2">
                            <div class="display-block" v-show="'style'===viewMode">
                                <input type="checkbox" name="prdSno" :value="product.sno" class=""
                                       :data-name="product.productName"
                                       :data-code="product.styleCode"
                                       :data-cnt="product.prdExQty"
                                       :data-cost="product.prdCost"
                                       :data-price="product.salePrice"
                                >
                            </div>
                            <div class="display-block" v-show="'styleTrash'===viewMode">
                                <input type="checkbox" name="prdDelSno" :value="product.sno" class="" :data-name="product.productName" :data-code="product.styleCode" :data-cnt="product.prdExQty">
                            </div>
                        </td>
                        <td rowspan="2">
                            {% prdIndex+1 %}
                            <div class="text-muted font-11">#{% product.sno %}</div>
                        </td>
                        <td rowspan="2">
                            <span class="hover-btn cursor-pointer"  @click="openProductReg2(project.sno, product.sno)">
                                <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)" class="middle" width="40">
                                <img :src="product.fileThumbnail" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)" class="middle" width="40" >
                                <img :src="product.fileThumbnailReal" v-if="!$.isEmpty(product.fileThumbnailReal)" class="middle" width="40" >
                            </span>
                        </td>
                        <td class="pdl5 ta-l" rowspan="2"><!--스타일명-->
                            <span class="font-16 text-blue hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                                {% product.productName %}
                            </span>
                            <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.productName)"></i>
                            <br>
                            <div class="font-14">
                                {% product.styleCode.toUpperCase() %} <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode.toUpperCase())"></i>
                            </div>
                            <div class="btn btn-white btn-sm mgt5" @click="openProductReg2(project.sno, product.sno, 4)">생산정보({% product.productionCnt %})</div>
                        </td>
                        <td class=""><!--제작수량-->
                            <span class="font-14 bold">{% $.setNumberFormat(product.prdExQty) %}장</span>
                            <!--
                            <br><span class="text-muted font-11">예정){% $.setNumberFormat(product.prdExQty) %}장</span>
                            -->
                        </td>
                        <td class="pdl10 ta-l">
                            <div class="font-11">현재단가:{% $.setNumberFormat(product.currentPrice) %}원</div>
                            <div class="font-11">타겟단가:{% $.setNumberFormat(product.targetPrice) %}원</div>
                            <div class="font-11">타겟생산:{% $.setNumberFormat(product.targetPrdCost) %}원</div>
                        </td>
                        <td >
                            <span class="font-15 bold">{% $.setNumberFormat(product.estimateCost) %}원</span>
                            <br><span class="text-muted">({% $.setNumberFormat(Number(product.estimateCost) * Number(product.prdExQty) )%}원)</span>
                        </td>
                        <td >
                            <span class="font-15 bold sl-blue">{% $.setNumberFormat(product.prdCost) %}원</span>
                            <div class="text-muted" >({% $.setNumberFormat(Number(product.prdCost) * Number(product.prdExQty) )%}원)</div>
                        </td>
                        <td class="relative">
                            <div class="reject-icon" v-if="'p' === product.priceConfirm ">승인</div>
                            <span class="font-15 bold text-danger">{% $.setNumberFormat(product.salePrice) %}원</span>
                            <br><span class="text-muted">({% $.setNumberFormat(Number(product.salePrice) * Number(product.prdExQty) )%}원)</span>
                        </td>
                        <td >
                            <span class="font-15 bold" v-if="Number(product.salePrice) > 0">{%  $.setNumberFormat(100-(Math.round(product.prdCost/product.salePrice*100))) %}%</span>
                            <span class="font-15 bold" v-if="0 >= Number(product.salePrice)">-%</span>
                        </td>
                        <td ><!--샘플-->
                            <div class="font-14 bold">{% product.sampleCnt %}개</div>
                            <div class="btn btn-white btn-sm mgt5" @click="openProductReg2(project.sno, product.sno, 0)">샘플보기</div>
                        </td>
                        <td >
                            <span v-html="product.fabricStatusIcon"></span>
                            <span v-html="product.fabricStatusKr" ></span>
                            <div class="btn btn-sm btn-white mgt5" @click="openProductReg2(project.sno, product.sno, 1)">퀄리티/BT</div>
                        </td>
                        <td >
                            <span v-html="product.btStatusIcon"></span>
                            <span v-html="product.btStatusKr" ></span>
                            <div class="btn btn-sm btn-white mgt5" @click="openProductReg2(project.sno, product.sno, 1)">퀄리티/BT</div>
                        </td>
                        <td >
                            <span v-if="false == product.existsCost">
                                <span v-html="product.prdCostStatusIcon"></span>
                            </span>
                            <span v-if="true == product.existsCost">
                                <i class="fa fa-play sl-blue" aria-hidden="true" style="color:#0abb87"></i>
                            </span>

                            <span v-html="product.prdCostStatusKr" ></span>
                            <!--
                            <div class="text-muted" v-show="0 == product.prdCostStatus">미진행</div>
                            <div class="" v-show="1 == product.prdCostStatus">진행중</div>
                            <div class="font-green bold" v-show="2 == product.prdCostStatus">
                                확정완료 #{% product.prdCostConfirmSno %}
                            </div>
                            -->
                            <div class="btn btn-sm btn-white mgt5" @click="openProductReg2(project.sno, product.sno, 3)">생산견적</div>
                        </td>
                        <td >
                            <span v-html="product.workStatusIcon"></span>
                            <span v-html="product.workStatusKr" ></span>
                            <div class="btn btn-white btn-sm mgt5" @click="openProductReg2(project.sno, product.sno, 4)">생산정보({% product.productionCnt %})</div>
                        </td>
                        <td rowspan="2" class="ta-c">
                            <input type="text" v-model="product.sort" class="form-control ta-c" >
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f9f9f9">
                            생산상태
                        </td>
                        <td class="ta-c" >
                            <span v-html="$.getStatusIcon(product.productionStatus)"></span>
                        </td>
                        <td style="background-color: #f9f9f9">
                            작지승인
                        </td>
                        <td class="ta-c">
                            <span v-html="$.getAcceptName(product.latestProduction.workConfirm)" v-if="product.latestProduction != null" ></span>
                            <span v-if="product.latestProduction == null" class="text-muted">작지없음</span>
                        </td>
                        <td style="background-color: #f9f9f9" colspan="2">
                            최신 작업지시서
                        </td>
                        <td class="pdl10 ta-l" colspan="6">
                            <div v-if="typeof workFileList[product.sno] != 'undefined'" style="display: flex">
                                <ul class="ims-file-list" >
                                    <li class="hover-btn" v-for="(file, fileIndex) in workFileList[product.sno].files">
                                        <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-action" style="display: none">
                <div class="pull-left form-inline">
                    <span class="action-title">선택한 상품을</span>
                </div>
                <div class="pull-right form-inline">
                </div>
            </div>
        </div>
    </div>
</div>


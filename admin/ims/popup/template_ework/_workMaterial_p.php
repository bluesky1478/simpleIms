<div class="col-xs-12 new-style_" v-show="'material' === tabMode">

    <div class="table-title gd-help-manual mgt20">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            원부자재 정보
        </div>
        <div class="flo-right ">

            <button type="button" class="btn btn-white btn-icon-excel simple-download  mgb5 mgr3" @click="ImsProductService.fabricDownload(mainData.product.productName)">원부자재 다운로드</button>
            <div class="btn btn-white mgb5" @click="openCommonPopup('ework_history', 1000, 750, {styleSno:mainData.product.sno, historyDiv:'material'})">원부자재 이력</div>

            <span v-if="mainData.product.prdCostConfirmSno > 0">
                <div class="btn btn-white mgb5" @click="openFactoryEstimateView(mainData.project.sno, mainData.product.sno, mainData.product.prdCostConfirmSno, 'cost')">확정견적 보기</div>
                <div class="btn btn-blue mgb5" @click="copyMaterial(mainData.product.sno, mainData.product.prdCostConfirmSno)">확정견적 가져오기</div>
            </span>

        </div>
    </div>

    <div id="myTable">

        <div style="clear:both; padding-top:10px !important;" >
            <div class="w-100p font-15 sl-purple nexon" ><b>원자재</b></div>
            <table class="table table-rows table-default-center ims-fabric-info">
                <colgroup>
                    <col class="w-6p" />
                    <col class="w-8p" />
                    <col class="w-14p" />
                    <col class="w-12p" />
                    <col class="w-9p" />
                    <col class="w-9p" />
                    <col class="w-7p" />
                    <!--<col class="w-8p" />
                    <col class="w-8p" />-->
                    <col class="w-5p" />
                    <col  />
                    <col class="w-3p" />
                </colgroup>
                <tr>
                    <th>부위</th>
                    <th>부착위치</th>
                    <th>자재(or원단)명</th>
                    <th>혼용율</th>
                    <th>컬러</th>
                    <th>규격</th>
                    <th>가요척(수량)</th>
                    <!--<th>단가</th>
                    <th>금액</th>-->
                    <th>제조국</th>
                    <th>비고</th>
                    <th>기능</th>
                </tr>
                <tr v-for="(fabric, fabricIndex) in mainData.product.fabricList" >
                    <td>
                        <input type="text" class="form-control text-center" placeholder="부위" v-model="fabric.position" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.position %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control text-center" placeholder="부착위치" v-model="fabric.attached" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.attached %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.fabricName %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.fabricMix %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.color %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.spec %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.meas %}</div>
                    </td>
                    <!--
                    <td>
                        <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice">
                    </td>
                    <td>
                        {% $.setNumberFormat(fabric.price) %}원
                    </td>
                    -->
                    <td class="ta-c">
                        <div v-if="'cn' === fabric.makeNational">중국</div>
                        <div v-else-if="'kr' === fabric.makeNational">한국</div>
                        <div v-else-if="'mk' === fabric.makeNational">시장</div>
                        <div v-else class="text-muted">-</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" v-if="fabricModify">
                        <div v-if="!fabricModify">{% fabric.position %}</div>
                    </td>
                    <td>
                        <div v-if="fabricModify">
                            <i class="fa fa-plus-circle hover-btn cursor-pointer" aria-hidden="true" @click="addElement(mainData.product.fabricList, mainData.product.fabricDefault, 'down', fabricIndex)" ></i>
                            <i class="fa fa-minus-circle hover-btn cursor-pointer" aria-hidden="true" @click="deleteElement(mainData.product.fabricList, fabricIndex)" v-show="mainData.product.fabricList.length > 1"></i>
                            <i class="fa fa-minus-circle disabled-color" aria-hidden="true" v-show="1 >= mainData.product.fabricList.length"></i>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="clear:both">
            <div class="w-100p font-15 sl-purple nexon" ><b>부자재</b></div>
            <table class="table table-rows table-default-center ims-fabric-info">
                <colgroup>
                    <col class="w-6p" /><!--위치-->
                    <col class="w-6p" />
                    <col class="w-9p" />
                    <col class="w-8p" /><!--컬러-->
                    <col class="w-12p" /><!--부자재업체-->
                    <col class="w-9p" /><!--규격-->
                    <col class="w-9p" /><!--수량-->
                    <col class="w-5p" /><!--단위-->
                    <!--
                    <col class="w-7p" />단가
                    <col class="w-8p" />금액
                    -->
                    <col class="w-8p" /><!--비고-->
                    <col class="w-5p" />
                    <col style="width:170px" />
                    <col class="w-3p" /><!--삭제-->
                </colgroup>
                <tr>
                    <th>부위</th>
                    <th colspan="2">자재명</th>
                    <th>컬러</th>
                    <th>부자재업체</th>
                    <th>규격</th>
                    <th>가요척(수량)</th>
                    <th>단위</th>
                    <!--<th>단가</th>
                    <th>금액</th>-->
                    <th colspan="3">비고</th>
                    <th>기능</th>
                </tr>
                <tr v-for="(subFabric, subFabricIndex) in mainData.product.subFabricList" >
                    <td>
                        <input type="text" class="form-control text-center" placeholder="부위" v-model="subFabric.position" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.position %}</div>
                    </td>
                    <td colspan="2">
                        <input type="text" class="form-control" placeholder="자재명" v-model="subFabric.fabricName" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.fabricName %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="컬러" v-model="subFabric.color" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.color %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="부자재업체" v-model="subFabric.makeCompany" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.makeCompany %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="규격" v-model="subFabric.spec" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.spec %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="가요척" v-model="subFabric.meas" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.meas %}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control" placeholder="단위" v-model="subFabric.unit" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.unit %}</div>
                    </td>
                    <!--
                    <td>
                        <input type="text" class="form-control" placeholder="단가" v-model="subFabric.unitPrice">
                    </td>
                    <td>
                        {% $.setNumberFormat(subFabric.price) %}원
                    </td>
                    -->
                    <td  colspan="3">
                        <input type="text" class="form-control" placeholder="비고" v-model="subFabric.memo" v-if="fabricModify">
                        <div v-if="!fabricModify">{% subFabric.memo %}</div>
                    </td>
                    <td>
                        <div v-if="fabricModify">
                            <i class="fa fa-plus-circle hover-btn cursor-pointer" aria-hidden="true" @click="addElement(mainData.product.subFabricList, mainData.product.subFabricList[0], 'down', subFabricIndex)" ></i>
                            <i class="fa fa-minus-circle hover-btn cursor-pointer" aria-hidden="true" @click="deleteElement(mainData.product.subFabricList, subFabricIndex)" v-show="mainData.product.subFabricList.length > 1"></i>
                            <i class="fa fa-minus-circle disabled-color" aria-hidden="true" v-show="1 >= mainData.product.subFabricList.length"></i>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <table class="table table-cols  xsmall-picker " >
        <colgroup>
            <col class="w-50p">
            <col class="w-50p">
        </colgroup>
        <thead>
        <tr>
            <th style="height:15px !important;padding:0 !important;" class="text-danger" colspan="2">유의사항</th>
        </tr>
        </thead>
        <tbody>
        <tr >
            <td style="padding:5px !important;" colspan="99">
                <div v-html="$.nl2br(mainData.ework.data.warnMaterial)"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
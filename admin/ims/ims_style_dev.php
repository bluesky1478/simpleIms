<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_nk.php'?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <style>
        .bootstrap-filestyle input{display: none }
        .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}

        .mx-input {padding:0 12px !important; font-size: 14px !important; }
        .pd-custom { padding:10px 15px 15px 15px !important; }
        .gd-help-manual { font-size:16px !important;}
        .ims-style-attribute-table td{border-bottom: none !important;}
    </style>

    <section id="imsApp">
        <form id="frm">
            <div class="page-header js-affix" style="margin-bottom: 0 !important;">
                <h3>
                <span class="sl-purple cursor-pointer hover-btn" @click="openCustomer(items.sno)">
                    {% items.customerName %}
                </span> {% product.productName %}
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(items.customerName+' '+product.productName)"></i>
                    (
                    <span class="sl-blue">{% product.styleCode %}</span>
                    <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode)"></i>
                    )
                </h3>
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()">
                <input type="button" value="저장" class="btn btn-red btn-register" @click="save(product)" style="margin-right:178px">
                <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(product.sno, 'product')" style="margin-right:75px">
            </div>
        </form>

        <div class="row" v-show="product.sno > 0">
            <div class="col-xs-12 pd-custom">
                <!-- 탭 -->
                <div class="w100">
                    <ul class="nav nav-tabs mgb30" role="tablist" style="margin-bottom:10px !important; ">

                        <!--
                        <li role="presentation" :class=" 6 === sampleTabMode ? 'active':''" @click="changeTab(6)">
                            <a href="#tab-status-cancel" data-toggle="tab" >작업지시서</a>
                        </li>
                        -->

                        <li role="presentation" :class=" 5 === sampleTabMode ? 'active':''" @click="changeTab(5)">
                            <a href="#tab-status-cancel" data-toggle="tab" >스타일 기본 정보</a> <!-- 기본 정보 -->
                        </li>
                        <li role="presentation" :class=" 7 === sampleTabMode ? 'active':''" @click="changeTab(7)">
                            <a href="#tab-status-cancel" data-toggle="tab" >기획({% stylePlanList.length %})</a> <!-- 기본 정보 -->
                        </li>
                        <li role="presentation" :class=" 2 === sampleTabMode ? 'active':''" @click="changeTab(2)">
                            <a href="#tab-status-cancel" data-toggle="tab" >샘플({% sampleList.length %})</a> <!-- 기본 단계 -->
                        </li>
                        <li role="presentation" :class=" 1 === sampleTabMode ? 'active':'' " @click="changeTab(1)">
                            <a href="#tab-status-cancel" data-toggle="tab" >퀄리티/BT 관리</a>
                        </li>
                        <li role="presentation" :class=" 3 === sampleTabMode ? 'active':'' " @click="changeTab(3)">
                            <a href="#tab-status-cancel" data-toggle="tab" >생산처 견적</a>
                        </li>
                        <li role="presentation" :class=" 6 === sampleTabMode ? 'active':'' " @click="changeTab(6)">
                            <a href="#tab-status-cancel" data-toggle="tab" ><span class="text-danger">(NEW)</span>가견적비교</a>
                        </li>
                        <li role="presentation" :class=" 4 === sampleTabMode ? 'active':'' " @click="changeTab(4)">
                            <a href="#tab-status-cancel" data-toggle="tab" >생산관리</a> <!-- 생산 단계 -->
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div v-show="product.sno > 0" >
            <section v-show="5 === sampleTabMode"><?php include 'template/ims_style_basic.php'?></section>
            <section v-show="7 === sampleTabMode">
                <div class="col-xs-12 pd0" style="padding-top:0 !important; ">
                    <div>
                        <div class="dp-flex mgb5">
                            <div class="btn btn-white" @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':vueApp.project.sno, 'styleSno':vueApp.product.sno});"><i aria-hidden="true" class="fa fa-plus"></i> 기획서 등록</div>
                            <div class="btn btn-gray" @click="registCopyMultiStylePlan()">기획서 복사</div>
                            <div class="btn btn-gray" @click="removeMultiStylePlan();">기획서 삭제</div>
                            <div class="btn btn-gray" @click="">가견적 요청</div>
                        </div>
                        <?php include 'template/ims_product_plan.php'?>
                    </div>
                </div>
            </section>
            <section v-show="6 === sampleTabMode"><?php include 'template/ims_style_config.php'?></section>
            <section v-show="2 === sampleTabMode"><?php include 'template/ims_product_sample_dev.php'?></section>
            <section v-show="1 === sampleTabMode"><?php include 'template/ims_product_fabric.php'?></section>
            <section v-show="3 === sampleTabMode"><?php include 'template/ims_product_cost.php'?></section>
            <section v-show="4 === sampleTabMode"><?php include 'template/ims_product_production.php'?></section>
        </div>





        <!--( 레이어 팝업 ) 가견적/생산가 요청 창-->
        <div class="modal fade" id="modalEstimateCostReq" tabindex="-1" role="dialog"  aria-hidden="true" >
            <div class="modal-dialog" role="document" style="width:1350px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                        <span class="modal-title font-18 bold" >
                    {% product.styleFullName %}의 <span class="sl-blue"></span>
                    <span >생산 견적 요청</span>
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
                                            <select2 class="js-example-basic-single" style="width:100%" v-model="estimateView.reqFactory">
                                                <option value="0">선택</option>
                                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                                    <option value="<?=$key?>"><?=$value?></option>
                                                <?php } ?>
                                            </select2>
                                        </td>
                                        <th>견적타입</th>
                                        <td>
                                            <label class="radio-inline font-14" style="padding:0">
                                                <input type="radio" name="optionAddType"  value="estimate"  v-model="estimateView.estimateType" />가견적
                                            </label>
                                            <label class="radio-inline font-14" style="padding:0">
                                                <input type="radio" name="optionAddType"  value="cost" v-model="estimateView.estimateType" />생산확정견적
                                            </label>
                                        </td>
                                    </tr>
                                    <tr >
                                        <th>수량</th>
                                        <td>
                                            <input type="number" class="form-control h100 w30 font-16" placeholder="수량(숫자만)" v-model="estimateView.estimateCount">
                                        </td>
                                        <th>처리완료D/L</th>
                                        <td>
                                            <date-picker v-model="estimateView.completeDeadLineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>

                                            <span class="pdl30">
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',1)">+1</div>
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',2)">+2</div>
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',3)">+3</div>
                                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',4)">+4</div>
                                        </span>

                                        </td>
                                    </tr>
                                    <tr >
                                        <th>요청내용</th>
                                        <td class="pd0" >
                                            <textarea class="form-control w100" rows="3" v-model="estimateView.reqMemo" placeholder="요청내용"></textarea>
                                        </td>
                                        <th>참고파일</th>
                                        <td class="pd0">

                                            <ul class="ims-file-list" >
                                                <li class="hover-btn" v-for="(file, fileIndex) in estimateView.reqFiles">
                                                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                                </li>
                                            </ul>

                                            <form id="estimateFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'estimate' === estimateView.estimateType">
                                                <div class="fallback">
                                                    <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                                                </div>
                                            </form>

                                            <form id="costFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'estimate' !== estimateView.estimateType">
                                                <div class="fallback">
                                                    <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="99">
                                            <div style="display: flex" class="mgt20">
                                                <span class="notice-info" style="margin-right:10px">기존 견적 정보를 불러올 수 있습니다.</span>
                                                <input type="text" class="form-control w110p font-14" placeholder="기존요청번호" style="height:30px" v-model="loadEstimateCostSno">
                                                <div class="btn btn-gray mgl5" @click="loadBeforeEstimateData()">기존자료 불러오기</div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mgt5">
                                <table class="table table-rows table-default-center">
                                    <colgroup>
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                        <col style="width:11%" />
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                        <col style="width:7%" />
                                    </colgroup>
                                    <tr>
                                        <th >생산가<small class="font-white">(VAT별도)</small></th>
                                        <th >원자재 소계</th>
                                        <th >부자재 소계</th>
                                        <th >공임</th>
                                        <th >마진</th>
                                        <th >물류 및 관세</th>
                                        <th >관리비</th>
                                        <th >생산MOQ</th>
                                        <th >단가MOQ</th>
                                        <th >MOQ미달 추가금</th>
                                    </tr>
                                    <tr>
                                        <td>
                                        <span class="font-16 text-danger bold">
                                            {% total %}원
                                        </span>
                                        </td>
                                        <td>{% $.setNumberFormat(sampleView.fabricCost) %}원</td>
                                        <td>{% $.setNumberFormat(sampleView.subFabricCost) %}원</td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="공임(숫자만 입력, 원단위)" v-model="sampleView.laborCost"  >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="마진(숫자만 입력, 원단위)" v-model="sampleView.marginCost" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="물류 및 관세(숫자만 입력, 원단위)" v-model="sampleView.dutyCost" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="관리비(숫자만 입력, 원단위)" v-model="sampleView.managementCost">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="생산MOQ" v-model="sampleView.prdMoq">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="단가MOQ" v-model="sampleView.priceMoq">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center" placeholder="MOQ미달 추가금" v-model="sampleView.addPrice">
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div>
                                <!--원단-->
                                <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30 mgb0" style="border-top:none!important">
                                    <colgroup>
                                        <col style="width:5%"><!--위치-->
                                        <col style="width:8%"><!--부착위치-->
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
                                            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> 견적 <span class="sl-blue">원단정보</span>
                                            <span class="font-11 normal">가요척과 단가는 반드시 숫자로 입력하세요.( 금액 계산을 위함 )</span>
                                        </th>
                                    </tr>
                                    <tr>
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
                                        <th>기능</th>
                                    </tr><!--원단-->
                                    <tr v-for="(fabric, fabricIndex) in sampleView.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                                        <td>
                                            <input type="text" class="form-control text-center" placeholder="위치" v-model="fabric.no" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="부착위치" v-model="fabric.attached" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice" >
                                        </td>
                                        <td>
                                            {% $.setNumberFormat(fabric.price) %}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="원단업체" v-model="fabric.fabricCompany" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" >
                                        </td>
                                        <td class="ta-l">
                                            <div >
                                                <button type="button" class="btn btn-white btn-sm" @click="addElement(sampleView.fabric, sampleView.fabric[0], 'after')">+ 추가</button>
                                                <div class="btn btn-sm btn-red" @click="deleteElement(sampleView.fabric, fabricIndex)" v-show="sampleView.fabric.length > 1">- 삭제</div>
                                                <div class="btn btn-sm btn-red" v-show="1 >= sampleView.fabric.length" disabled="" title="최소 1개 필요">- 삭제</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!--부자재-->
                                <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
                                    <colgroup>
                                        <col style="width:5%"><!--위치-->
                                        <col style="width:8%"><!--부착위치-->
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
                                            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> 견적 <span class="sl-blue">부자재</span> 정보
                                            <span class="font-11 normal" v-show="'m' === viewModeSample">가요척과(or수량)과 단가는 반드시 숫자로 입력하세요.( 금액 계산을 위함 )</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>위치</th>
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
                                    <tr v-for="(fabric, subFabricIndex) in sampleView.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                                        <td>
                                            <input type="text" class="form-control text-center" placeholder="위치" v-model="fabric.no" >
                                        </td>
                                        <td colspan="3">
                                            <input type="text" class="form-control" placeholder="자재명" v-model="fabric.subFabricName" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice" >
                                        </td>
                                        <td>
                                            {% $.setNumberFormat(fabric.price) %}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="구매업체" v-model="fabric.company" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo" >
                                        </td>
                                        <td class="ta-l">
                                            <div >
                                                <button type="button" class="btn btn-white btn-sm" @click="addElement(sampleView.subFabric, sampleView.subFabric[0], 'after')">+ 추가</button>
                                                <div class="btn btn-sm btn-red" @click="deleteElement(sampleView.subFabric, subFabricIndex)" v-show="sampleView.subFabric.length > 1">- 삭제</div>
                                                <div class="btn btn-sm btn-red" disabled="" v-show="1 >= sampleView.subFabric.length" title="최소 1개 필요">- 삭제</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </section>
                    </div>

                    <div class="modal-footer ">
                        <div class="btn btn-red" @click="ImsProductService.saveEstimateCostReq(0)">임시저장</div>
                        <div class="btn btn-red" @click="ImsProductService.saveEstimateCostReq(1)">요청</div>
                        <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                    </div>
                </div>
            </div>
        </div>




    </section>


<?php include 'script/ims_style_script_dev.php'?>
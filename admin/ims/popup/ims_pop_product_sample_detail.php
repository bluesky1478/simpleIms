<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>
    .fa.fa-search.btn-search { position: absolute; top:0px; right:0px; border:1px #ccc solid; padding: 4px; border-radius: 5px; }
</style>

<section id="imsApp">
    <?php include './admin/ims/library_nk_sch_modal.php'?>
    <div class="modal" style="overflow-y:scroll; display:block!important;">
        <div class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" style="opacity: 1" @click="self.close();"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold">
                        #{% Number(sampleView.sno)+1000 %} {% sampleView.productName %}의 <span class="sl-blue">{% sampleView.sampleName %}</span>
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
                            <button type="button" class="btn btn-white btn-icon-excel simple-download mgr3" @click="ImsProductService.fabricDownload(sampleView.sampleName)">원부자재 다운로드</button>
                            <div class="btn btn-white" @click="viewModeSample = 'm'" v-show="'m' !== viewModeSample ">수정하기</div>
                            <div class="btn btn-white" @click="viewModeSample = 'v'" v-show=" sampleView.sno > 0  && 'm' === viewModeSample ">수정취소</div>
                            <div class="btn btn-red" @click="saveSampleByPopup()" v-show="'m' === viewModeSample ">저장</div>
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
                                        <option v-for="n in 20" :key="n">{% n %}</option>
                                    </select>
                                    <span v-show="'m' !== viewModeSample">{% sampleView.sampleCount %}</span> 개
                                </td>
                            </tr>
                            <tr>
                                <th>샘플 원단 단가</th>
                                <td class="font-16">
                                    {% $.setNumberFormat(sampleView.fabricCost) %}원
                                </td>
                                <th>샘플 부자재 단가</th>
                                <td class="font-16">
                                    {% $.setNumberFormat(sampleView.subFabricCost) %}원
                                </td>
                                <th>샘플 제작 단가</th>
                                <td class="font-16">
                                    {% $.setNumberFormat(sampleView.sampleUnitCost) %}원
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
                                    {% $.setNumberFormat(sampleView.sampleCost) %}원
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
                            <tr v-for="(fabric, fabricIndex) in sampleView.fabric" @focusin="focusRowByPopup(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                                <td :class="'m' === viewModeSample ? 'handle' : '' ">
                                    <div class="cursor-pointer hover-btn" v-show="'m' === viewModeSample">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" v-model="fabric.materialSno" />
                                    <input type="text" class="form-control text-center" placeholder="부위" v-model="fabric.no" v-show="'m' === viewModeSample">
                                    <span v-show="'m' !== viewModeSample">{% fabric.no %}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-center" placeholder="부착위치" v-model="fabric.attached" v-show="'m' === viewModeSample">
                                    <span v-show="'m' !== viewModeSample">{% fabric.attached %}</span>
                                </td>
                                <td>
                                    <span v-show="'m' === viewModeSample" class="position-relative">
                                        <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName" />
                                        <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'원단 검색'}, 'material', fabric, {'materialSno':'sno','fabricName':'name','fabricMix':'mixRatio','color':'materialColor','spec':'spec','unitPrice':'unitPrice','fabricCompany':'customerName'}, {'sTextboxSchName':fabric.fabricName,'aChkboxSchMaterialType':[1,2]}, 'fnRevertUnitPrice')"></i>
                                    </span>
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
                                    <input type="text" class="form-control" placeholder="가요척" @keyup="calc_total()" v-model="fabric.meas" v-show="'m' === viewModeSample">
                                    <span v-show="'m' !== viewModeSample">{% fabric.meas %}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control" placeholder="단가" @keyup="calc_total()" v-model="fabric.unitPrice" v-show="'m' === viewModeSample">
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
                                        <div class="btn btn-gray btn-sm" @click="ImsProductService.addQb(fabric, sampleView.styleSno)">관리등록</div>
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
                            <tr v-for="(fabric, subFabricIndex) in sampleView.subFabric" @focusin="subFocusRowByPopup(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                                <td :class="'m' === viewModeSample ? 'handle' : '' ">
                                    <div class="cursor-pointer hover-btn" v-show="'m' === viewModeSample">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" v-model="fabric.materialSno" />
                                    <input type="text" class="form-control text-center" placeholder="부위" v-model="fabric.no" v-show="'m' === viewModeSample">
                                    <span v-show="'m' !== viewModeSample">{% fabric.no %}</span>
                                </td>
                                <td colspan="3">
                                    <span v-show="'m' === viewModeSample" class="position-relative">
                                        <input type="text" class="form-control" placeholder="자재명" v-model="fabric.subFabricName" />
                                        <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'부자재 검색'}, 'material', fabric, {'materialSno':'sno','subFabricName':'name','color':'materialColor','spec':'spec','unitPrice':'unitPrice','company':'customerName'}, {'sTextboxSchName':fabric.subFabricName,'aChkboxSchMaterialType':[3,4]}, 'fnRevertUnitPrice')"></i>
                                    </span>
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
                                    <input type="text" class="form-control" placeholder="가요척" @keyup="calc_total()" v-model="fabric.meas" v-show="'m' === viewModeSample">
                                    <span v-show="'m' !== viewModeSample">{% fabric.meas %}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control" placeholder="단가" @keyup="calc_total()" v-model="fabric.unitPrice" v-show="'m' === viewModeSample">
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
                                        <div class="btn btn-gray btn-sm" @click="ImsProductService.addSubQb(fabric, sampleView.styleSno)">관리등록</div>
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
                    <div class="btn btn-red" @click="saveSampleByPopup()" v-show="'m' === viewModeSample ">저장</div>
                    <div class="btn btn-gray" @click="self.close();">닫기</div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include './admin/ims/script/ims_pop_product_sample_detail_script.php'?>



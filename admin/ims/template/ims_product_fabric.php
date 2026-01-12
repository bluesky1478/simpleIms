<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">

    <div v-if=" 'y' === product.fabricPass" class="font-18 ta-c">
        해당 없음
        <br>
        <div class="btn btn-red mgt10" @click="setFabricPass(product.sno, project.sno, 'n')">
            해당 없음 취소
        </div>
    </div>

    <div  style=""  v-if=" 'y' !== product.fabricPass">
        <div class="btn btn-white" @click="openFabricView(-1, 'modify')"><i aria-hidden="true" class="fa fa-plus"></i> 등록</div>
        <!-- TODO
        <div class="btn btn-gray" @click="alert('작업중')">복사</div>
        <div class="btn btn-gray" @click="deleteSample()">삭제</div>
        -->
        <div class="pull-left">
            <div class="btn btn-gray" @click="openFabricReq(1)">QB요청</div>
            <span><span class="text-red bold">{% fabricList.length %}건</span>의 관리원단</span>
        </div>
        <div class="pull-right"></div>

        <div >
            <table class="table table-rows table-default-center mgt5">
                <colgroup>
                    <col style="width:50px" /><!--체크-->
                    <col style="width:70px" /><!--번호-->
                    <col style="width:90px" /><!--위치-->
                    <col style="width:20%" /><!--원단정보-->
                    <col style="width:30%" /><!--퀄리티-->
                    <col style="width:30%" /><!--BT-->
                    <!--<col style="width:20%" />--><!--BULK-->
                    <col style="width:80px" /><!--BULK-->
                </colgroup>
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="fabricSno">
                    </th>
                    <th>번호</th>
                    <th>부위</th>
                    <th>원단정보</th>
                    <th>퀄리티 수배 정보</th>
                    <th>BT 정보</th>
                    <!--<th>BULK 정보</th>-->
                    <th>등록/수정일</th>
                </tr>
                </thead>
                <tbody v-for="(fabric , fabricIndex) in fabricList" :class="fabricList.sno === fabric.sno ? 'choice-skyblue' : ''">
                <tr >
                    <td rowspan="2">
                        <input type="checkbox" name="fabricSno[]" :value="fabric.sno" class="list-check">
                    </td>
                    <td rowspan="2">
                        {% (fabricList.length-fabricIndex) %}
                        <div class="text-muted font-14">(#{% Number(fabric.sno) %})</div>
                    </td>
                    <td rowspan="2">
                        {% fabric.position %}
                        <br><small>{% fabric.attached %}</small>
                    </td>
                    <!--원단정보-->
                    <td class="ta-l pdl5 relative"  rowspan="2">
                        <div class="ta-l">
                            <div >
                                <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-show="!$.isEmpty(fabric.makeNational)" ></i>

                                <span class="font-14 bold hover-btn cursor-pointer" @click="openFabricView(fabricIndex, 'v')">
                                {% fabric.fabricName %}
                                </span>
                                <span class="text-muted font-11">
                                    {% fabric.color %} {% fabric.fabricMix %}
                                </span>
                            </div>
                        </div>

                        <div class="text-muted font-11" >
                            {% fabric.fabricMemo %}
                        </div>
                        <div class="mgt5">
                            <div class="btn btn-sm btn-white " @click="openFabricView(fabricIndex, 'v')">보기</div>
                            <div class="btn btn-sm btn-red btn-red-line2" @click="ImsProductService.deleteQb(fabric.sno, product.sno)">삭제</div>
                        </div>
                    </td>
                    <!-- 퀄리티확보정보 -->
                    <td class="ta-l">
                        <div class="w100 ta-c">
                            <div :class="$.getProcColor(fabric.fabricStatus) + ' bold font-14'">
                                {% fabric.fabricStatusKr %}
                            </div>
                        </div>
                        <div>
                            <div class="">{% fabric.fabricConfirmInfo %}</div>
                            <div class="text-muted">{% fabric.fabricMemo %}</div>
                        </div>
                    </td>
                    <!--<small>BT정보</small>-->
                    <td class="ta-l">
                        <div class="w100 ta-c">
                            <div :class="$.getProcColor(fabric.btStatus) + ' bold font-14'">
                                {% fabric.btStatusKr %}
                            </div>
                        </div>
                        <div>
                            <div class="">{% fabric.btConfirmInfo %}</div>
                            <div class="text-muted">{% fabric.btMemo %}</div>
                        </div>
                    </td>
                    <!--<small>BULK정보</small>-->
                    <!--<td class="ta-l">
                        <div class="w100 ta-c">
                            <div :class="$.getProcColor(fabric.bulkStatus) + ' bold font-14'">
                                {% fabric.bulkStatusKr %}
                            </div>
                        </div>
                        <div>
                            <div class="">{% fabric.bulkConfirmInfo %}</div>
                            <div class="text-muted">{% fabric.bulkMemo %}</div>
                        </div>
                    </td>-->
                    <td>
                        {% $.formatShortDate(fabric.regDt) %}
                        <br>
                        <span class="text-muted">{% $.formatShortDate(fabric.modDt) %}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="pd0">
                        <table class="table-borderless table table-pd-3 mg0 h100">
                            <colgroup>
                                <col style="width:15%" />
                                <col />
                            </colgroup>
                            <tr>
                                <td style="background-color: #f9f9f9!important;color:#000; border-top:none !important;" class="border-top-0">
                                    요청목록
                                    <br>
                                    <!--
                                    <div class="btn btn-white btn-sm mgt5">전체보기</div>
                                    -->
                                </td>
                                <td class="ta-l border-top-0 pdl10" style=" border-top:none !important;">
                                    <ul>
                                        <li style="border-bottom:solid 1px #f1f1f1;padding:5px" v-for="(request, reqIdx) in fabric.fabricRequest.list">
                                            <span class="bold font-14">
                                                <span class="sl-blue">{% request.reqCount %}회차</span>
                                                {% $.formatShortDate(request.regDt) %}
                                                {% request.reqManagerNm %}
                                                ->
                                                {% request.reqFactoryNm %}
                                                {% request.reqTypeKr %} 요청
                                                - 상태 : <span :class="$.getProcColor2(request.reqStatus)">{% request.reqStatusKr %}</span>
                                                <span v-if="3 > request.reqStatus">(처리예정일 : {% $.formatShortDate(request.completeDeadLineDt) %} )</span>
                                                <span v-if="request.reqStatus >= 3">(처리완료일 : {% $.formatShortDate(request.completeDt) %} )</span>
                                            </span>
                                            <br>
                                            <div class="dp-flex">
                                                <b>요청/발송내용</b> : {% request.reqDeliveryInfo %}
                                                <ul v-if="request.fabricReqFile !== null && request.fabricReqFile.length > 0"  class="mgl10">
                                                    <li v-for="(file, fileIndex) in request.fabricReqFile">
                                                        <?=$nasDownloadTag?>
                                                    </li>
                                                </ul>
                                            </div>
                                            <b>생산처 발송/처리 내용</b> : {% request.resDeliveryInfo %}
                                            <span v-if="!$.isEmpty(request.resMemo)">({% request.resMemo %})</span>
                                            <br>
                                            <div class="mgt5" v-if="3 == request.reqStatus">
                                                <div class="btn btn-blue btn-sm " @click="openQbConfirm(fabricIndex, request, 5)">확정</div>
                                                <div class="btn btn-red btn-sm" @click="openQbConfirm(fabricIndex, request, 6)">반려</div>
                                                <!--<div class="btn btn-red btn-sm" @click="setRejectQb(fabric.sno)">반려(즉시재요청)</div>-->
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
                <tbody v-show=" 0 >= fabricList.length || $.isEmpty(fabricList.length) ">
                    <tr >
                        <td colspan="99" class="ta-c">
                            
                            <div class="text-muted">데이터 없음</div>

                            <div class="btn btn-red mgt10" @click="setFabricPass(product.sno, project.sno, 'y')">
                                해당없음 처리
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>


<!--원단팝업-->
<div class="modal fade" id="modalFabricView" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:1350px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title" v-show="0 >= fabricView.sno">
                    <span class=" font-14">#{% product.styleFullName %}의 신규 원단 추가</span>
                    <span class="sl-blue">{% fabricView.fabricName %}</span>
                </span>
                <span class="modal-title font-18 bold" v-show="fabricView.sno > 0">
                    <span class="text-muted">(#{% Number(fabricView.sno) %})</span> {% product.styleFullName %}의 <span class="sl-blue">{% fabricView.fabricName %}</span>
                </span>
            </div>
            <div class="modal-body">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 pdl5">
                        # 퀄리티 정보
                    </div>
                    <div class="flo-right pdt5 pdl5 mgb3">
                        <!-- TODO
                        <span v-show=" (0 === fabricView.sno || $.isEmpty(fabricView.sno) ) && 'm' === viewModeFabric ">
                            <span class="notice-info" >원단번호를 입력하고 '불러오기'를 하면 기존 정보를 불러옵니다.</span>
                            <input type="text" class="form-control w130p inline-block" placeholder="원단번호(숫자만)" v-model="loadSampleNo" >
                            <div class="btn btn-gray" @click="loadSample()" v-show=" (0 === fabricView.sno || $.isEmpty(fabricView.sno) ) && 'm' === viewModeFabric ">불러오기</div>
                        </span>
                        -->

                        <!--
                        <div class="btn btn-gray" >퀄리티 요청 이력</div>
                        -->

                        <div class="dp-flex">
                            <button type="button" class="btn btn-white btn-icon-excel simple-download mgr3" @click="ImsFabricService.downloadFabricRequestForm(items.customerName, project, product, fabricView)">의뢰서 다운로드</button>
                            <div class="btn btn-white" @click="viewModeFabric = 'm'" v-show="'m' !== viewModeFabric ">수정하기</div>
                            <div class="btn btn-white mgr5" @click="viewModeFabric = 'v'" v-show=" fabricView.sno > 0  && 'm' === viewModeFabric ">수정취소</div>
                            <div class="btn btn-red" @click="saveFabric()" v-show="'m' === viewModeFabric ">저장</div>
                        </div>
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
                            <th>부위</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.position" placeholder="부위" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.position %}</span>
                            </td>
                            <th>부착위치</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.attached" placeholder="부착위치" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.attached %}</span>
                            </td>
                            <th>원단명</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.fabricName" placeholder="원단명" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.fabricName %}</span>
                            </td>
                            <th>혼용율</th>
                            <td >
                                <input type="text" class="form-control font-14" v-model="fabricView.fabricMix" placeholder="혼용율" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.fabricMix %}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>컬러</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.color" placeholder="컬러" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.color %}</span>
                            </td>
                            <th>규격</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.spec" placeholder="규격" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.spec %}</span>
                            </td>
                            <th>가요척</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.meas" placeholder="가요척" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.meas %}</span>
                            </td>
                            <th>후가공</th>
                            <td >
                                <input type="text" class="form-control font-14 w100" v-model="fabricView.afterMake" placeholder="후가공" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.afterMake %}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>중량</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.weight" placeholder="중량" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.weight %}</span>
                            </td>
                            <th>원단폭</th>
                            <td>
                                <input type="text" class="form-control font-14" v-model="fabricView.fabricWidth" placeholder="원단폭" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.fabricWidth %}</span>
                            </td>
                            <th>제조국</th>
                            <td colspan="99">
                                <div v-show="'m' === viewModeFabric">
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value=""  v-model="fabricView.makeNational"  />
                                        미정
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value="kr"  v-model="fabricView.makeNational"  />
                                        <span class="flag flag-16 flag-kr">
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value="cn"  v-model="fabricView.makeNational" />
                                        <span class="flag flag-16 flag-cn">
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="makeNational"  value="market"  v-model="fabricView.makeNational" />
                                        <span class="flag flag-16 flag-market">
                                    </label>
                                </div>
                                <div v-show="'m' !== viewModeFabric">
                                    <i :class="'flag flag-16 flag-'+ fabricView.makeNational" v-show="!$.isEmpty(fabricView.makeNational)" ></i>
                                    <span v-show="$.isEmpty(fabricView.makeNational)">미정</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="sl-blue font-14">퀄리티 수배상태</th>
                            <td class="font-14">
                                <select class="form-control font-14" style="width:100%" v-model="fabricView.fabricStatus" v-show="'m' === viewModeFabric">
                                    <?php foreach($fabricStatusMap as $fabricStatusKey => $fabricStatus) { ?>
                                        <option value="<?=$fabricStatusKey?>"><?=$fabricStatus?></option>
                                    <?php } ?>
                                </select>
                                <div v-show="'m' !== viewModeFabric"  :class="$.getProcColor(fabricView.fabricStatus)">
                                    {% fabricView.fabricStatusKr %}
                                </div>
                            </td>
                            <th>퀄리티 확정내용</th>
                            <td colspan="2">
                                <input type="text" class="form-control" v-model="fabricView.fabricConfirmInfo" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.fabricConfirmInfo %}</span>
                            </td>
                            <th>퀄리티 비고</th>
                            <td colspan="2">
                                <input type="text" class="form-control" v-model="fabricView.fabricMemo" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.fabricMemo %}</span>
                            </td>
                        </tr>
                        <tr v-show=" fabricView.sno > 0  && 'm' !== viewModeFabric ">
                            <th>
                                퀄리티 결과 파일
                            </th>
                            <td colspan="99">
                                <file-upload2 :file="fabricView.fileList.btFile1" :id="'btFile1'" :params="fabricView" :accept="false"></file-upload2>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>


                <div class="table-title gd-help-manual mgt10" >
                    <div class="flo-left pdt5 pdl5">
                        # BT 정보
                    </div>
                    <div class="flo-right pdt5 pdl5 pdb5">
                        <!--
                        <div class="btn btn-gray" >BT 요청 이력</div>
                        -->
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
                            <th class="font-14 sl-blue">BT상태</th>
                            <td class="font-14">
                                <select class="form-control font-14" style="width:100%" v-model="fabricView.btStatus" v-show="'m' === viewModeFabric">
                                    <?php foreach($btStatusMap as $btStatusKey => $btStatus) { ?>
                                        <option value="<?=$btStatusKey?>"><?=$btStatus?></option>
                                    <?php } ?>
                                </select>
                                <span v-show="'m' !== viewModeFabric" :class="$.getProcColor(fabricView.btStatus)">{% fabricView.btStatusKr %}</span>
                            </td>
                            <th>BT확정내용</th>
                            <td colspan="2">
                                <input type="text" class="form-control font-14" v-model="fabricView.btConfirmInfo" placeholder="BT확정내용" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.btConfirmInfo %}</span>
                            </td>
                            <th>BT비고</th>
                            <td colspan="2">
                                <input type="text" class="form-control font-14" v-model="fabricView.btMemo" placeholder="BT비고" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.btMemo %}</span>
                            </td>
                        </tr>
                        <tr v-show=" fabricView.sno > 0  && 'm' !== viewModeFabric ">
                            <th>BT결과 파일</th>
                            <td colspan="99" id="bt-area">
                                <file-upload2 :file="fabricView.fileList.btFile2" :id="'btFile2'" :params="fabricView" :accept="false"></file-upload2>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!--<div class="table-title gd-help-manual mgt10" >
                    <div class="flo-left pdt5 pdl5">
                        # BULK 정보
                    </div>
                    <div class="flo-right pdt5 pdl5 pdb5">
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
                            <th class="font-14 sl-blue">BULK 상태</th>
                            <td class="font-14">
                                <select class="form-control font-14" style="width:100%" v-model="fabricView.bulkStatus" v-show="'m' === viewModeFabric">
                                    <?php /*foreach($btStatusMap as $btStatusKey => $btStatus) { */?>
                                        <option value="<?/*=$btStatusKey*/?>"><?/*=$btStatus*/?></option>
                                    <?php /*} */?>
                                </select>
                                <span v-show="'m' !== viewModeFabric" :class="$.getProcColor(fabricView.bulkStatus)">{% fabricView.bulkStatusKr %}</span>
                            </td>
                            <th>BULK 확정내용</th>
                            <td colspan="2">
                                <input type="text" class="form-control font-14" v-model="fabricView.bulkConfirmInfo" placeholder="벌크 확정내용" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.bulkConfirmInfo %}</span>
                            </td>
                            <th>BULK 비고</th>
                            <td colspan="2">
                                <input type="text" class="form-control font-14" v-model="fabricView.bulkMemo" placeholder="벌크 비고" v-show="'m' === viewModeFabric">
                                <span v-show="'m' !== viewModeFabric">{% fabricView.bulkMemo %}</span>
                            </td>
                        </tr>
                        <tr v-show=" fabricView.sno > 0  && 'm' !== viewModeFabric ">
                            <th>BULK 결과</th>
                            <td colspan="99">
                                <file-upload2 :file="fabricView.fileList.bulkFile" :id="'bulkFile'" :params="fabricView" :accept="false"></file-upload2>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>-->

            </div>
            <div class="modal-footer ">
                <div class="btn btn-white" @click="viewModeFabric='m';" v-show="'m' !== viewModeFabric ">수정하기</div>
                <div class="btn btn-white" @click="viewModeFabric = 'v'" v-show=" fabricView.sno > 0  && 'm' === viewModeFabric ">수정취소</div>
                <div class="btn btn-red" @click="saveFabric()" v-show="'m' === viewModeFabric ">저장</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>

<!--QB요청-->
<div class="modal fade" id="modalFabricReq" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    {% product.styleFullName %}의 <span class="sl-blue"></span> QB요청
                </span>
            </div>
            <div class="modal-body">
                <section >
                    <div class="table-title gd-help-manual">
                        <div class="flo-left pdt5 pdl5">
                            # QB 요청
                        </div>
                        <div class="flo-right pdt5 pdl5">

                        </div>
                    </div>
                    <div class="">
                        <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
                            <colgroup>
                                <col class="width-xs">
                                <col class="width-xl">
                                <col class="width-xs">
                                <col class="width-xl">
                            </colgroup>
                            <tbody>
                            <tr >
                                <th>의뢰처</th>
                                <td>
                                    <select2 class="form-control" style="width:100%" v-model="fabricReq.reqFactory" id="selFabricFactory">
                                        <option value="0">선택</option>
                                        <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>


                                </td>
                                <th class="text-danger">의뢰타입</th>
                                <td class="font-14">
                                    <?php foreach($qbReqTypeList as $qbReqTypeKey => $qbReqTypeValue) { ?>
                                        <?php if( empty($qbReqTypeKey)) continue ?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="<?=$qbReqTypeKey?>" v-model="fabricReq.reqType">
                                            <?=$qbReqTypeValue?>
                                        </label>
                                    <?php } ?>
                                    <div>

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>이노버 발송/요청정보</th>
                                <td colspan="99">
                                    <input type="text" class="form-control" v-model="fabricReq.reqDeliveryInfo" placeholder="발송정보" >
                                </td>
                            </tr>
                            <tr>
                                <th>의뢰서</th>
                                <td colspan="99">
                                    <ul class="ims-file-list" >
                                        <li class="hover-btn" v-for="(file, fileIndex) in fabricReq.fabricReqFile">
                                            <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                        </li>
                                    </ul>
                                    <form id="fabricReqFile" class="set-dropzone mgt5" @submit.prevent="uploadFiles">
                                        <div class="fallback">
                                            <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                                        </div>
                                    </form>
                                </td>
                                <!--
                                <th>완료예정일</th>
                                <td >
                                    <date-picker v-model="fabricReq.completeDeadLineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="완료예정일" style="max-width: 120px!important;width:120px!important; font-weight: normal;"></date-picker>
                                </td>
                                -->
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
            <div class="modal-footer">
                <div class="btn btn-red" @click="saveFabricReq()">요청</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>




<!--QB확정 요청 처리-->
<div class="modal fade" id="modalFabricConfirm" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:1350px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" v-show="fabricView.sno > 0">
                    <span class="text-muted">(#{% Number(fabricView.sno) %})</span> {% product.styleFullName %}의 <span class="sl-blue">{% fabricView.fabricName %}</span> QB요청 처리
                </span>
            </div>
            <div class="modal-body">

                <div class="table-title gd-help-manual mgt10" >
                    <div class="flo-left pdt5 pdl5">
                        # 요청 정보
                    </div>
                    <div class="flo-right pdt5 pdl5 pdb5"></div>
                </div>
                <div class="" v-if="typeof fabricView.request != 'undefined'">
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
                            <th>의뢰처</th>
                            <td class="font-14">
                                {% fabricView.request.reqFactoryNm %}
                            </td>
                            <th class="">요청횟수</th>
                            <td >
                                <div class="font-14">{% fabricView.request.reqCount %}회차 요청</div>
                                <div>{% $.formatShortDate(fabricView.request.regDt) %}</div>
                            </td>
                            <th>요청타입</th>
                            <td class="font-14">
                                {% fabricView.request.reqTypeKr %}
                            </td>
                            <th>처리일자</th>
                            <td class="font-14">
                                {% $.formatShortDate(fabricView.request.regDt) %}
                            </td>
                        </tr>
                        <tr>
                            <th>요청내용</th>
                            <td colspan="3">
                                {% fabricView.request.reqDeliveryInfo %}
                                <ul v-if="fabricView.request.fabricReqFile !== null && fabricView.request.fabricReqFile.length > 0" class="">
                                    <li v-for="(file, fileIndex) in fabricView.request.fabricReqFile">
                                        <?=$nasDownloadTag?>
                                    </li>
                                </ul>
                            </td>
                            <th>의뢰처 처리내용</th>
                            <td colspan="99">
                                <div>{% fabricView.request.resDeliveryInfo %}</div>
                                <div class="font-11">{% fabricView.request.resMemo %}</div>
                            </td>
                        </tr>
                        <tr v-if="5 === fabricView.reqStatus">
                            <th>확정정보</th>
                            <td colspan="99">
                                <span v-html="setConfirmInfo()"></span>
                            </td>
                        </tr>
                        <tr v-if="6 === fabricView.reqStatus">
                            <th>반려정보</th>
                            <td colspan="3">
                                <input type="text" class="form-control" placeholder="반려정보" v-model="fabricView.request.rejectMemo">
                            </td>
                            <td colspan="99">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!--퀄리티-->
                <section v-show="typeof fabricView.request != 'undefined' && fabricView.request.reqType & 1">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left pdt5 pdl5">
                            # 퀄리티 정보
                        </div>
                        <div class="flo-right pdt5 pdl5 mgb3"></div>
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
                                <th>부위</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.position" placeholder="부위" >
                                </td>
                                <th>부착위치</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.attached" placeholder="부착위치" >
                                </td>
                                <th>원단명</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.fabricName" placeholder="원단명" >
                                </td>
                                <th>혼용율</th>
                                <td >
                                    <input type="text" class="form-control font-14" v-model="fabricView.fabricMix" placeholder="혼용율" >
                                </td>
                            </tr>
                            <tr>
                                <th>컬러</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.color" placeholder="컬러" >
                                </td>
                                <th>규격</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.spec" placeholder="규격" >
                                </td>
                                <th>가요척</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.meas" placeholder="가요척" >
                                </td>
                                <th>후가공</th>
                                <td >
                                    <input type="text" class="form-control font-14 w100" v-model="fabricView.afterMake" placeholder="후가공" >
                                </td>
                            </tr>
                            <tr>
                                <th>중량</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.weight" placeholder="중량" >
                                </td>
                                <th>원단폭</th>
                                <td>
                                    <input type="text" class="form-control font-14" v-model="fabricView.fabricWidth" placeholder="원단폭" >
                                </td>
                                <th>제조국</th>
                                <td colspan="99">
                                    <div >
                                        <label class="radio-inline">
                                            <input type="radio" name="makeNational"  value=""  v-model="fabricView.makeNational"  />
                                            미정
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="makeNational"  value="kr"  v-model="fabricView.makeNational"  />
                                            <span class="flag flag-16 flag-kr">
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="makeNational"  value="cn"  v-model="fabricView.makeNational" />
                                            <span class="flag flag-16 flag-cn">
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="makeNational"  value="market"  v-model="fabricView.makeNational" />
                                            <span class="flag flag-16 flag-market">
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="sl-blue font-14">퀄리티 수배상태</th>
                                <td class="font-14">
                                    <select class="form-control font-14" style="width:100%" v-model="fabricView.fabricStatus" >
                                        <?php foreach($fabricStatusMap as $fabricStatusKey => $fabricStatus) { ?>
                                            <option value="<?=$fabricStatusKey?>"><?=$fabricStatus?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <th>퀄리티 확정내용</th>
                                <td colspan="2">
                                    <input type="text" class="form-control" v-model="fabricView.fabricConfirmInfo" >
                                </td>
                                <th>퀄리티 비고</th>
                                <td colspan="2">
                                    <input type="text" class="form-control" v-model="fabricView.fabricMemo" >
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!--BT-->
                <section v-show="typeof fabricView.request != 'undefined' && fabricView.request.reqType & 2">
                    <div class="table-title gd-help-manual mgt10" >
                        <div class="flo-left pdt5 pdl5">
                            # BT 정보
                        </div>
                        <div class="flo-right pdt5 pdl5 pdb5"></div>
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
                                <th class="font-14 sl-blue">BT상태</th>
                                <td class="font-14">
                                    <select class="form-control font-14" style="width:100%" v-model="fabricView.btStatus" >
                                        <?php foreach($btStatusMap as $btStatusKey => $btStatus) { ?>
                                            <option value="<?=$btStatusKey?>"><?=$btStatus?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <th>BT확정내용</th>
                                <td colspan="2">
                                    <input type="text" class="form-control font-14" v-model="fabricView.btConfirmInfo" placeholder="BT확정내용" >
                                </td>
                                <th>BT비고</th>
                                <td colspan="2">
                                    <input type="text" class="form-control font-14" v-model="fabricView.btMemo" placeholder="BT비고" >
                                </td>
                            </tr>
                            <!--
                            <tr >
                                <th>BT결과</th>
                                <td colspan="99" id="confirm-bt-area">
                                    엘리먼트 옮기기.
                                </td>
                            </tr>
                            -->
                            </tbody>
                        </table>
                    </div>
                </section>

                <!--BULK-->
                <section v-show="typeof fabricView.request != 'undefined' && fabricView.request.reqType & 4">
                    <div class="table-title gd-help-manual mgt10" >
                        <div class="flo-left pdt5 pdl5">
                            # BULK 정보
                        </div>
                        <div class="flo-right pdt5 pdl5 pdb5"></div>
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
                                <th class="font-14 sl-blue">BULK 상태</th>
                                <td class="font-14">
                                    <select class="form-control font-14" style="width:100%" v-model="fabricView.bulkStatus" >
                                        <?php foreach($btStatusMap as $btStatusKey => $btStatus) { ?>
                                            <option value="<?=$btStatusKey?>"><?=$btStatus?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <th>BULK 확정내용</th>
                                <td colspan="2">
                                    <input type="text" class="form-control font-14" v-model="fabricView.bulkConfirmInfo" placeholder="벌크 확정내용" >
                                </td>
                                <th>BULK 비고</th>
                                <td colspan="2">
                                    <input type="text" class="form-control font-14" v-model="fabricView.bulkMemo" placeholder="벌크 비고" >
                                </td>
                            </tr>
                            <!--
                            <tr>
                                <th>BULK 결과</th>
                                <td colspan="99">
                                    엘리먼트 옮기기.
                                </td>
                            </tr>
                            -->
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
            <div class="modal-footer ">
                
                <div class="btn btn-red" @click="saveFabric()" v-if="5 == fabricView.reqStatus">확정완료</div>
                <div class="btn btn-red" @click="saveFabric()" v-if="6 == fabricView.reqStatus">반려처리</div>
                
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>

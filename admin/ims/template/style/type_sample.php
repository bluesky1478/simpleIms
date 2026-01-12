<table class="table table-cols" style="border-top:none" id="project-sample-list">
    <colgroup>
        <!--<col class="w-3p">번호-->
        <?php foreach($prdSetupDataSample['list'] as $each) { ?>
            <col class="w-<?=$each[1]?>p" />
        <?php } ?>
    </colgroup>
    <thead>
    <tr>
        <!--<th>번호</th>-->
        <?php foreach($prdSetupDataSample['list'] as $each) { ?>
            <th><?=$each[0]?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody :class="'text-center '" v-show="!showStyle">
    <tr>
        <td colspan="99" class="center">
            <div class="btn btn-white" @click="showStyle=true">샘플 보기</div>
        </td>
    </tr>
    </tbody>
    <tbody :class="'text-center '" >
    <tr v-if="0 >= sampleList.length" v-show="showStyle">
        <td colspan="99">
            샘플이 없습니다.
        </td>
    </tr>
    <tr v-for="(product, prdIndex) in sampleList" v-show="showStyle">
        <!--스타일명-->
        <td :rowspan="product.styleRowspan" v-if="product.styleRowspan > 0">
            <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.styleSno, 0)" >
                {% product.productName %}
            </span>
        </td>
        <td ><!--번호-->
            {% prdIndex+1 %}
            <div class="text-muted font-11">#{% product.sno %}</div>
        </td>
        <td ><!--제작차수-->
            {% product.sampleTerm %}
        </td>
        <td ><!--샘플구분-->
            {% product.sampleType==9?'구버전':product.sampleTypeHan %}
        </td>
        <td ><!--스타일기획-->
            <span @click="openCommonPopup('upsert_style_plan', 1660, 1200, {'projectSno':product.projectSno, 'styleSno':product.styleSno, 'sno':product.productPlanSno});" class="hover-btn cursor-pointer">{% product.planConcept %}</span>
        </td>

        <td class="pdl5 ta-l" ><!--스타일명-->
            <span v-if="'n' !== product.sampleConfirm" class="hover-btn cursor-pointer"  >
                <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                <span class="sl-green">
                    고객확정
                    <br>
                    <span @click="openProductWithSample(project.sno, product.styleSno, product.sno, product.sampleType);" class="hover-btn">
                    <b class="">{% product.sampleName %}</b>
                    </span>
                </span>
            </span>

            <!--<span v-else class="hover-btn cursor-pointer" @click="openProductWithSample(project.sno, product.styleSno, product.sno)" >
                <b>{% product.sampleName %}</b>
            </span>-->
            <span v-else class="hover-btn cursor-pointer">
                <span @click="openProductWithSample(project.sno, product.styleSno, product.sno, product.sampleType);" class="hover-btn"><b>{% product.sampleName %}</b></span>
            </span>

            <div class="text-muted font-11">
                등록:{% product.regDt %}
            </div>
        </td>
        <td>
            {% product.patternFactoryName == null ? '미선택' : product.patternFactoryName %}
        </td>
        <td>
            {% product.factoryName %}
        </td>
        <td>
            {% product.sampleCount %}
        </td>
        <td>
            {% $.setNumberFormat(product.sampleCost) %}원
        </td>
        <!--<td>
            <div class="btn btn-white btn-sm">견적요청</div>
            <div class="btn btn-white btn-sm">고객확정</div>
        </td>
            ['샘플지시서',5], //파일
            ['샘플리뷰서',5], //파일
            ['샘플투입일',5], //날짜
            ['샘플실마감일',5], //날짜
        -->
        <td class="">
            <span v-if="product.sampleType !== null && product.sampleType != 9" @click="openProductWithSample(project.sno, product.styleSno, product.sno, product.sampleType);" class="hover-btn cursor-pointer">보기</span>
            <span v-else>
                <simple-file-only-history-upload :file="product.fileList.sampleFile1" :params="product" :file_div="'sampleFile1'" class="font-11"></simple-file-only-history-upload>
            </span>
        </td>
        <td class="">
            <span v-if="product.sampleType !== null && product.sampleType != 9" @click="openProductWithSample(project.sno, product.styleSno, product.sno, product.sampleType, 'review');" class="hover-btn cursor-pointer">보기</span>
            <span v-else>
                <simple-file-only-history-upload :file="product.fileList.sampleFile4" :params="product" :file_div="'sampleFile4'" class="font-11"></simple-file-only-history-upload>
            </span>
        </td>
        <td class="">
            <span v-if="product.sampleType !== null && product.sampleType != 9" @click="openProductWithSample(project.sno, product.styleSno, product.sno, product.sampleType, 'confirm');" class="hover-btn cursor-pointer">보기</span>
            <span v-else>
                <simple-file-only-history-upload :file="product.fileList.sampleFile6" :params="product" :file_div="'sampleFile6'" class="font-11"></simple-file-only-history-upload>
            </span>
        </td>
        <td>
            {% $.formatShortDate(product.sampleFactoryBeginDt) %}
        </td>
        <td>
            {% $.formatShortDate(product.sampleFactoryEndDt) %}
        </td>
        <td><span class="sl-blue cursor-pointer hover-btn" @click="openListSampleLocation(prdIndex)">{% product.recentLocation == null || product.recentLocation == '' ? '입력' : product.recentLocation %}</span></td>
        <td>
            {% product.sampleMemo %}
        </td>
    </tr>
    </tbody>
</table>
<div v-if="sampleList[0] != undefined" class="modal fade" id="modalSampleLocationList" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    샘플 위치 이력
                </span>
            </div>
            <div class="modal-body">
                <table class="table table-rows ch-table table-td-height30">
                    <colgroup>
                        <col style="width:12%" />
                        <col style="width:15%" />
                        <col style="width:15%" />
                        <col style="width:10%" />
                        <col />
                        <col style="width:8%" />
                    </colgroup>
                    <tr>
                        <th class="ta-c">날짜</th>
                        <th class="ta-c">출발지</th>
                        <th class="ta-c">도착지</th>
                        <th class="ta-c">배송 방법</th>
                        <th class="ta-c">배송 정보</th>
                        <th class="ta-c">세부</th>
                    </tr>
                    <tr v-if="sampleList[chooseSampleIdx].jsonLocation != undefined && sampleList[chooseSampleIdx].jsonLocation.length == 0">
                        <td colspan="6" class="ta-c">데이터가 없습니다.</td>
                    </tr>
                    <tr v-else-if="sampleList[chooseSampleIdx].jsonLocation != undefined" v-for="(val, key) in sampleList[chooseSampleIdx].jsonLocation">
                        <td class="ta-c">{% val.locationDt %}</td>
                        <td>{% val.locationStart %}</td>
                        <td>{% val.locationEnd %}</td>
                        <td>{% val.locationMethod %}</td>
                        <td>{% val.locationMemo %}</td>
                        <td class="ta-c"><div class="btn btn-white btn-sm" @click="openViewSampleLocation(key);">보기</div></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer ">
                <div class="btn btn-blue" @click="openRegistSampleLocation()">위치 등록</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSampleLocationUpsert" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="width:700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    샘플위치 {% !isModifySampleLocation ? '상세' : (chooseSampleLocationIdx == -1 ? '등록' : '수정') %}
                </span>
            </div>
            <div class="modal-body">
                <table class="table table-cols ims-table-style1 table-td-height40 table-th-height40" >
                    <colgroup>
                        <col class="width-md">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th >날짜</th>
                        <td>
                            <div v-show="isModifySampleLocation" class="">
                                <date-picker v-model="sampleLocationInfo.locationDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>
                            </div>
                            <div v-show="!isModifySampleLocation">
                                <div v-if="$.isEmpty(sampleLocationInfo.locationDt)">
                                    <span class="font-11 text-muted">미정</span>
                                </div>
                                <div v-if="!$.isEmpty(sampleLocationInfo.locationDt)">
                                    {% $.formatShortDateWithoutWeek(sampleLocationInfo.locationDt) %}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th >출발지</th>
                        <td>
                            <?php $model='sampleLocationInfo.locationStart'; $placeholder='출발지'; $modifyKey='isModifySampleLocation'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >도착지</th>
                        <td>
                            <?php $model='sampleLocationInfo.locationEnd'; $placeholder='도착지'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >받는 분</th>
                        <td>
                            <?php $model='sampleLocationInfo.locationReceiver'; $placeholder='받는 분'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >연락처</th>
                        <td>
                            <?php $model='sampleLocationInfo.locationTel'; $placeholder='연락처'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >주소</th>
                        <td>
                            <?php $model='sampleLocationInfo.locationAddr'; $placeholder='주소'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th >배송 방법</th>
                        <td>
                            <div v-show="isModifySampleLocation" class="">
                                <label class="radio-inline ">
                                    <input type="radio" name="sampleLocationMethod" value="택배"  v-model="sampleLocationInfo.locationMethod" />택배
                                </label>
                                <label class="radio-inline ">
                                    <input type="radio" name="sampleLocationMethod" value="퀵"  v-model="sampleLocationInfo.locationMethod" />퀵
                                </label>
                                <label class="radio-inline ">
                                    <input type="radio" name="sampleLocationMethod" @click="sampleLocationInfo.locationMethod = '';" :checked="['택배','퀵'].indexOf(sampleLocationInfo.locationMethod) === -1" />기타
                                </label>
                                <label :style="'display:' + (['택배','퀵'].indexOf(sampleLocationInfo.locationMethod) === -1 ? 'inline-block' : 'none') ">
                                    <input type="text" class="form-control" v-model="sampleLocationInfo.locationMethod" placeholder="기타 입력" />
                                </label>
                            </div>
                            <div v-show="!isModifySampleLocation">
                                {% sampleLocationInfo.locationMethod %}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th >배송 정보</th>
                        <td>
                            <?php $model='sampleLocationInfo.locationMemo'; $placeholder='배송 정보'; ?>
                            <?php include './admin/ims/template/basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer ">
                <div class="btn btn-blue-line" v-show="!isModifySampleLocation" @click="isModifySampleLocation=true;">수정</div>
                <div class="btn btn-blue" v-show="isModifySampleLocation" @click="saveSampleLocation()">저장</div>
                <div class="btn btn-blue-line" v-show="isModifySampleLocation && chooseSampleLocationIdx != -1" @click="isModifySampleLocation=false;">수정취소</div>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
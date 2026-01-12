<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_nk.php'?>
<style>
    .bootstrap-filestyle input{display: none }
    .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}
    .mx-input-wrapper{ width:100px !important; font-size:11px !important; }
    .mx-input{ width:100px !important; font-size:11px !important;padding:5px !important;}
    .mx-datepicker {width:100px}
    .pd-custom { padding:10px 15px 15px 15px !important; }
    .gd-help-manual { font-size:16px !important;}
    .ims-style-attribute-table td{border-bottom: none !important;}
</style>

<!--    namkuuuuu update영역과 view영역 따로 있음-->

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom: 0 !important;">
            <h3>
                <span class="sl-purple">{% items.customerName %}</span> {% product.productName %} (<span class="sl-blue">{% product.styleCode.toUpperCase() %}</span>)
                <span class="text-muted font-14">( #견적요청번호:{% estimate.sno %}  #스타일번호:{% product.sno %} 프로젝트번호:{% product.projectSno %})</span>
            </h3>
            <div class="btn-group">
                <input type="button" value="임시저장" class="btn btn-red btn-register" @click="saveEstimateRes(estimate.reqStatus)"  v-show="3 > estimate.reqStatus">
                <input type="button" value="요청하기" class="btn btn-blue btn-register" @click="saveEstimateResComplete(1)"  v-show="0 === estimate.reqStatus">
                <input type="button" value="처리완료" class="btn btn-blue btn-register" @click="saveEstimateResComplete(3)"  v-show="3 > estimate.reqStatus && 0 != estimate.reqStatus">

                <input type="button" value="닫기" class="btn btn-white" @click="popupClose()" >
            </div>
            <!--
            <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(product.sno, 'product')" style="margin-right:75px">
            -->
        </div>
    </form>

    <!-- 기본 정보 -->
    <div class="row mgt10" v-if="false">
        <div class="col-xs-4">
            <div class="table-title gd-help-manual">
                <div class="flo-left cursor-pointer hover-btn" v-show="!showImage" @click="showImage=true">스타일이미지 <div class="btn btn-sm btn-white">▼ 보기</div></div>
                <div class="flo-left cursor-pointer hover-btn" v-show="showImage" @click="showImage=false">스타일이미지 <div class="btn btn-sm btn-white">▲ 닫기</div></div>
                <div class="flo-right"></div>
            </div>
        </div>
    </div>
    <div class="row mgt10 ims-product-image" v-show="showImage" v-if="false">
        <?php foreach( $thumbnailFieldList as $thumbnailField ){ ?>
            <div class="col-xs-4">
                <div class="table-title gd-help-manual">
                    <div class="flo-left"><?=$thumbnailField['title']?></div>
                    <div class="flo-right">
                        <div class="notice-info">업로드 후 저장시 적용됩니다.</div>
                    </div>
                </div>
                <table class="table table-cols">
                    <tbody>
                    <tr>
                        <td>
                            <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(product.<?=$thumbnailField['field']?>)" style="height:150px;">
                            <img :src="product.<?=$thumbnailField['field']?>" v-show="!$.isEmpty(product.<?=$thumbnailField['field']?>)" style="height:150px;">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php if(!empty($isDev)){ ?>
            <div class="dp-flex">
                <select class="form-control" v-model="estimate.reqStatus">
                    <option value="0">대기</option>
                    <option value="1">요청</option>
                    <option value="2">처리중</option>
                    <option value="3">처리완료</option>
                </select>
                <div class="btn btn-sm btn-white" @click="saveEstimateRes(estimate.reqStatus)">저장</div>
            </div>
        <?php } ?>

    </div>

    <!-- FIXME 기본 정보 -->
    <div class="row" v-show="true">
        <div class="col-xs-12 pd-custom">
            <div class="table-title gd-help-manual">
                <div class="flo-left">기본정보</div>
                <div class="flo-right"></div>
            </div>
            <table class="table table-cols table-ims-product-detail table-th-height30 table-td-height30" style="margin-bottom:0">
                <colgroup>
                    <col class="width-md"/>
                    <col class="width-xl"/>

                    <col class="width-md"/>
                    <col class="width-xl"/>

                    <col class="width-md"/>
                    <col class="width-xl"/>

                    <col class="width-md"/>
                    <col class="width-xl"/>
                </colgroup>
                <tbody>
                    <tr>
                        <th>제품명</th>
                        <td>
                            {% product.productName %} ({% product.styleCode.toUpperCase() %})
                        </td>
                        <th>생산처</th>
                        <td>
                            <span v-if="estimate.isFactory">({% estimate.reqFactoryNm %})</span>
                            <div v-if="!estimate.isFactory" v-show="!estimate.isFactory">
                                <select2 class="js-example-basic-single" style="width:100%" v-model="estimate.reqFactory"  >
                                    <option value="0">미정</option>
                                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                        </td>
                        <th rowspan="2">
                            진행상태
                        </th>
                        <td rowspan="2" colspan="3">
                            <span class="font-14 bold">{% estimate.reqStatusKr %}</span>
                            <span class="" v-if="'0000-00-00 00:00:00' != estimate.completeDt">(처리 완료일 : {% estimate.completeDt %})</span>
                            <div class="text-muted">(최종 업데이트 : {% estimate.lastManagerNm %})</div>
                        </td>
                    </tr>
                    <tr>
                        <th >이노버 납기일</th>
                        <td class="sl-blue bold">{% product.msDeliveryDt %}</td>
                        <th>견적 수량</th>
                        <td>
                            {% $.setNumberFormat(estimate.estimateCount) %}장
                            <span class="" v-if="!estimate.isFactory">
                                (판매가 : <span class="text-danger">{% $.setNumberFormat(product.salePrice) %}원)
                            </span>

                            <div>
                                <input type="text" v-model="estimate.estimateCount" class="form-control" v-if="3 > estimate.reqStatus && !estimate.isFactory">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>샘플/작지/원부자재</th>
                        <td>
                            <div class="btn btn-white btn-sm mgr5" @click="openProductWithSample(project.sno, product.sno, product.sampleConfirmSno)" v-if="product.sampleConfirmSno > 0">
                                확정샘플 보기
                            </div>
                            <div class="btn btn-white btn-sm mgr5 disabled"  v-if="0 >= product.sampleConfirmSno">
                                <span>확정샘플 없음</span>
                            </div>
                            <div class="btn btn-white btn-sm mgr5" @click="openUrl(`eworkP_${product.sno}`,`<?=$eworkUrl?>?sno=${product.sno}`,1600,950);">작업지시서</div>
                            <div class="mgt5">
                                <simple-file-only-history-upload :file="fileList.fileFabricConfirm" :params="{projectSno:product.projectSno,styleSno:product.sno}" :file_div="'fileFabricConfirm'" class="font-11"></simple-file-only-history-upload>
                            </div>
                        </td>
                        <th>
                            생산형태/국가
                        </th>
                        <td>
                            {% product.produceTypeKr %} / {% product.produceNational %}
                        </td>
                        <th rowspan="2" >생산처 메모</th>
                        <td rowspan="2" colspan="3">
                            <textarea class="form-control w100 h100" v-model="estimate.resMemo" placeholder="생산처 메모" rows="4" v-show="3 > estimate.reqStatus && 0 !== estimate.reqStatus"></textarea>
                            <div v-html="estimate.resMemoBr" ></div>
                        </td>
                    </tr>
                    <tr>
                        <th>이노버 요청 사항</th>
                        <td colspan="3">

                            <div v-html="estimate.reqMemoBr" class="mgb10"></div>

                            <textarea class="form-control w100" v-model="estimate.reqMemo" placeholder="이노버 메모" rows="3" v-if="3 > estimate.reqStatus && !estimate.isFactory"></textarea>

                            <div v-if="3 > estimate.reqStatus">
                                <ul class="ims-file-list" >
                                    <li class="hover-btn" v-for="(file, fileIndex) in estimate.reqFiles">
                                        <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                    </li>
                                </ul>
                                <form id="costFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles">
                                    <div class="fallback">
                                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                                    </div>
                                </form>
                            </div>
                            <ul class="mgt5" v-if="3 == estimate.reqStatus">
                                <li class="hover-btn" v-for="(file, fileIndex) in estimate.reqFiles">
                                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                </li>
                            </ul>
                            <div v-if="1 == estimate.reqStatus">{% $.formatShortDate(estimate.completeDeadLineDt) %}까지 처리 요청</div>
                        </td>
                    </tr>
                    <tr>
                        <th>원단 설명 / MOQ</th>
                        <td colspan="3">
                            <div v-html="estimate.reqMemo1Br"></div>
                            <textarea class="form-control w100" v-model="estimate.reqMemo1" placeholder="원단 설명 / MOQ" rows="3" v-if="3 > estimate.reqStatus && !estimate.isFactory"></textarea>
                        </td>
                        <th>메인원단 생지여부</th>
                        <td>
                            <div v-html="estimate.reqMemo2Br" ></div>
                            <textarea class="form-control w100" v-model="estimate.reqMemo2" placeholder="메인원단 생지여부" rows="3" v-if="3 > estimate.reqStatus && !estimate.isFactory"></textarea>
                        </td>
                        <th>기능(단가 변동/벌)</th>
                        <td>
                            <div v-html="estimate.reqMemo3Br" ></div>
                            <textarea class="form-control w100" v-model="estimate.reqMemo3" placeholder="기능(단가 변동/벌)" rows="3" v-if="3 > estimate.reqStatus && !estimate.isFactory"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!--처리전-->
    <div class="row" v-if="3 > estimate.reqStatus <?=in_array($managerSno, \Component\Ims\ImsCodeMap::FACTORY_ESTIMATE_MODIFY_MANAGER)?'|| true':'' ?>">
        <div class="col-xs-12 pd-custom">

            <div class="flo-left font-18" style="margin-right:10px" >

                <div class="dp-flex dp-flex-gap10">
                    요청번호 : #{% estimate.sno %}
                    <div class="btn btn-white" v-if="!estimate.isFactory" @click="openInnoverPrice">공임단가</div>

                    <div v-if="'estimate' === estimate.estimateType" class="bold font-18 sl-blue">
                        <i class="fa fa-quora" aria-hidden="true"></i> 가견적 요청 건
                    </div>

                    <div v-if="'cost' === estimate.estimateType" class="bold font-18 sl-green">
                        <i class="fa fa-krw" aria-hidden="true"></i> 생산 확정가 요청 건
                    </div>
                </div>

            </div>
            <div class="flo-right" >
                <div style="display: flex">
                    <span class="notice-info" style="margin-right:10px">가견적 정보도 불러올 수 있습니다.</span>
                    <input type="text" class="form-control w110p font-14" placeholder="기존요청번호" style="height:30px" v-model="loadEstimateSno">
                    <div class="btn btn-gray mgl5" @click="loadEstimate()">기존자료 불러오기</div>

                    <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="download()">엑셀다운로드</button>

                </div>
            </div>

            <div class="clear-both"></div>

            <div class="mgt5">
                <table class="table table-rows table-default-center">
                    <colgroup>
                        <col style="width:7%">
                        <col style="width:6%">
                        <col style="width:6%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:11%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                        <col style="width:7%">
                    </colgroup>
                    <tr>
                        <th style="background-color:#0c4da2 !important; ">
                            생산기간
                        </th>
                        <td class="pd0" colspan="2">
                            <input type="text" class="form-control " placeholder="생산기간" style="height:30px" v-model="estimate.contents.producePeriod">
                        </td>
                        <td colspan="99">
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">생산가<small class="font-white">(VAT별도)</small></th>
                        <th rowspan="2">원자재 소계<br/>기능 소계</th>
                        <th rowspan="2">부자재 소계<br/>마크 소계</th>
                        <th style="border-right:none !important;" rowspan="2">공임<br/>기타</th>
                        <th style="border-right:none !important;">&nbsp;</th>
                        <th style="border-right:none !important;">&nbsp;</th>
                        <th >&nbsp;</th>
                        <th rowspan="2">마진</th>
                        <th rowspan="2">물류 및 관세</th>
                        <th rowspan="2" style="background-color:#0c4da2 !important; ">운송형태</th>
                        <th rowspan="2">관리비</th>
                        <th rowspan="2">생산MOQ</th>
                        <th rowspan="2" style="background-color:#0c4da2 !important; ">단가MOQ</th>
                        <th rowspan="2">MOQ미달 추가금</th>
                    </tr>
                    <tr>
                        <th style="border-left:solid 1px #E6E6E6 !important;border-right:solid 1px #E6E6E6 !important;">기준환율</th>
                        <th style="border-left:solid 1px #E6E6E6 !important;border-right:solid 1px #E6E6E6 !important;">환율기준일</th>
                        <th style="border-left:solid 1px #E6E6E6 !important;">달러변환</th>
                    </tr>
                    <tr>
                        <td>
                            <span class="font-16 text-danger bold">
                                {% total %}원
                            </span>
                        </td>
                        <td>{% $.setNumberFormat(estimate.contents.fabricCost) %}원<br/>{% $.setNumberFormat(estimate.contents.utilCost) %}원</td>
                        <td>{% $.setNumberFormat(estimate.contents.subFabricCost) %}원<br/>{% $.setNumberFormat(estimate.contents.markCost) %}원</td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="공임(숫자만 입력, 원단위)" v-model="estimate.contents.laborCost"  >
                            <input type="number" class="form-control text-center" placeholder="기타(숫자만 입력, 원단위)" v-model="estimate.contents.etcCost"  >
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="환율(숫자만 입력, 원단위)" v-model="estimate.contents.exchange"  >
                        </td>
                        <td class="pd0" style="padding:0 !important;">
                            <date-picker v-model="estimate.contents.exchangeDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00" class="font-11" style="font-size:11px !important;"></date-picker>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimate.contents, 'exchangeDt',0)">오늘</div>
                        </td>
                        <td>
                            <div v-if="0 >= estimate.contents.laborCost || 0 >= estimate.contents.exchange">0$</div>
                            <div v-if="estimate.contents.laborCost > 0 && estimate.contents.exchange > 0">{% (estimate.contents.laborCost/estimate.contents.exchange).toFixed(2) %}$</div>
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="마진(숫자만 입력, 원단위)" v-model="estimate.contents.marginCost" >
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="물류 및 관세(숫자만 입력, 원단위)" v-model="estimate.contents.dutyCost" >
                        </td>
                        <td>
                            <select class="form-control w100 font-14" v-model="estimate.contents.deliveryType">
                                <option value="">선택</option>
                                <option>왕복에어</option>
                                <option>편도에어</option>
                                <option>보트(FCL)</option>
                                <option>보트(LCL)</option>
                                <option>국내생산</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="관리비(숫자만 입력, 원단위)" v-model="estimate.contents.managementCost">
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="생산MOQ" v-model="estimate.contents.prdMoq">
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="단가MOQ" v-model="estimate.contents.priceMoq">
                        </td>
                        <td>
                            <input type="number" class="form-control text-center" placeholder="MOQ미달 추가금" v-model="estimate.contents.addPrice">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="">
                <?php $sMaterialTargetNm = 'estimate.contents'; ?>
                <?php include './admin/ims/template/view/materialModule.php'?>

                <?php /*
                <div class="">
                    <div class="flo-left font-16 bold">
                        원단정보
                    </div>
                    <div class="flo-right">
                        <button type="button" class="btn btn-red btn-sm " style="margin-bottom: 3px" @click="addElement(estimate.contents.fabric, estimate.contents.fabric[0], 'after')"> + 원단추가</button>
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-6p" />
                            <col class="w-8p" />
                            <col class="w-14p" />
                            <col class="w-12p" />
                            <col class="w-9p" />
                            <col class="w-9p" />
                            <col class="w-7p" />
                            <col class="w-8p" />
                            <col class="w-8p" />
                            <col class="w-5p" />
                            <col  />
                            <col class="w-3p" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>이동</th>
                            <th>부위</th>
                            <th>부착위치</th>
                            <th>자재(or원단)명</th>
                            <th>혼용율</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>가요척(수량)</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>제조국</th>
                            <th>비고</th>
                            <th>삭제</th>
                        </tr>
                        </thead>

                        <tbody  is="draggable" :list="estimate.contents.fabric"  :animation="200" tag="tbody" handle=".handle">
                        <tr v-for="(fabric, fabricIndex) in estimate.contents.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }" :key="fabricIndex">
                            <td class="handle">
                                <div class="cursor-pointer hover-btn">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" placeholder="부위" v-model="fabric.no">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" placeholder="부착위치" v-model="fabric.attached">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="가요척" v-model="fabric.meas">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice">
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.price) %}원
                            </td>
                            <td class="ta-c">
                                <select class="form-control w100" v-model="fabric.makeNational">
                                    <option value="">미정</option>
                                    <option value="cn">중국</option>
                                    <option value="kr">한국</option>
                                    <option value="market">시장</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo">
                            </td>
                            <td>
                                <i class="fa fa-plus-circle hover-btn cursor-pointer" aria-hidden="true" @click="addElement(estimate.contents.fabric, estimate.contents.fabric[0], 'down', fabricIndex)" ></i>
                                <i class="fa fa-minus-circle hover-btn cursor-pointer" aria-hidden="true" @click="deleteElement(estimate.contents.fabric, fabricIndex)" v-show="estimate.contents.fabric.length > 1"></i>
                                <i class="fa fa-minus-circle disabled-color" aria-hidden="true" v-show="1 >= estimate.contents.fabric.length"></i>
                            </td>
                        </tr>
                    </table>
                </div>
                */ ?>

            </div>
<?php /*
            <div>
                <div class="">
                    <div class="flo-left  font-16 bold">
                        부자재정보
                    </div>
                    <div class="flo-right">
                        <button type="button" class="btn btn-red btn-sm " style="margin-bottom: 3px" @click="addElement(estimate.contents.subFabric, estimate.contents.subFabric[0], 'after')"> + 부자재추가</button>
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col class="w-1p" /><!--이동-->
                            <col class="w-6p" /><!--위치-->
                            <col class="w-6p" />
                            <col class="w-9p" />
                            <col class="w-8p" /><!--컬러-->
                            <col class="w-12p" /><!--부자재업체-->
                            <col class="w-9p" /><!--규격-->
                            <col class="w-9p" /><!--수량-->
                            <col class="w-5p" /><!--단위-->
                            <col class="w-7p" /><!--단가-->
                            <col class="w-8p" /><!--금액-->
                            <col class="w-8p" /><!--비고-->
                            <col class="w-5p" />
                            <col style="width:170px" />
                            <col class="w-3p" /><!--삭제-->
                        </colgroup>
                        <thead>
                        <tr>
                            <th>이동</th>
                            <th>부위</th>
                            <th colspan="2">자재명</th>
                            <th>컬러</th>
                            <th>부자재업체</th>
                            <th>규격</th>
                            <th>가요척(수량)</th>
                            <th>단위</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th colspan="3">비고</th>
                            <th>삭제</th>
                        </tr>
                        </thead>
                        <tbody  is="draggable" :list="estimate.contents.subFabric"  :animation="200" tag="tbody" handle=".handle">
                            <tr v-for="(subFabric, subFabricIndex) in estimate.contents.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }" :key="subFabricIndex" >
                            <td class="handle">
                                <div class="cursor-pointer hover-btn">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" placeholder="부위" v-model="subFabric.no">
                            </td>
                            <td colspan="2">
                                <input type="text" class="form-control" placeholder="자재명" v-model="subFabric.subFabricName">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="컬러" v-model="subFabric.color">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="부자재업체" v-model="subFabric.company">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="subFabric.spec">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="가요척" v-model="subFabric.meas">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단위" v-model="subFabric.unit">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가" v-model="subFabric.unitPrice">
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.price) %}원
                            </td>
                            <td  colspan="3">
                                <input type="text" class="form-control" placeholder="비고" v-model="subFabric.memo">
                            </td>
                            <td>
                                <i class="fa fa-plus-circle hover-btn cursor-pointer" aria-hidden="true" @click="addElement(estimate.contents.subFabric, estimate.contents.subFabric[0], 'down', subFabricIndex)" ></i>
                                <i class="fa fa-minus-circle hover-btn cursor-pointer" aria-hidden="true" @click="deleteElement(estimate.contents.subFabric, subFabricIndex)" v-show="estimate.contents.subFabric.length > 1"></i>
                                <i class="fa fa-minus-circle disabled-color" aria-hidden="true" v-show="1 >= estimate.contents.subFabric.length"></i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
           */ ?>

        </div>
    </div>

    <!--처리완료시-->
    <div class="row" v-if="estimate.reqStatus >= 3 && '20' != '<?=$managerSno?>' && '32' != '<?=$managerSno?>' ">
        <div class="col-xs-12 pd-custom">

            <div class="flo-left">
                <div class="btn btn-white" v-if="!estimate.isFactory" @click="openInnoverPrice">공임단가</div>
            </div>
            <div class="flo-right font-18" style="margin-right:10px">

                <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="download()">엑셀다운로드</button>

                요청번호 : #{% estimate.sno %}
            </div>

            <div class="clear-both"></div>

            <div class="mgt5">
                <table class="table table-rows table-default-center">
                    <colgroup>
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:6%">
                        <col style="width:6%">
                        <col style="width:6%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:7.5%">
                        <col style="width:8%">
                    </colgroup>
                    <tr>
                        <th style="background-color:#0c4da2 !important; ">
                            생산기간
                        </th>
                        <td class="pd0" colspan="2">
                            {% estimate.contents.producePeriod %}
                        </td>
                        <td colspan="99">
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">생산가<small class="font-white">(VAT별도)</small></th>
                        <th rowspan="2">원자재 소계</th>
                        <th rowspan="2">부자재 소계</th>
                        <th style="border-right:none !important;" rowspan="2">공임</th>
                        <th style="border-right:none !important;">&nbsp;</th>
                        <th style="border-right:none !important;">&nbsp;</th>
                        <th >&nbsp;</th>
                        <th rowspan="2">마진</th>
                        <th rowspan="2">물류 및 관세</th>
                        <th rowspan="2">운송형태</th>
                        <th rowspan="2">관리비</th>
                        <th rowspan="2">생산MOQ</th>
                        <th rowspan="2">단가MOQ</th>
                        <th rowspan="2">MOQ미달 추가금</th>
                    </tr>
                    <tr>
                        <th style="border-left:solid 1px #E6E6E6 !important;border-right:solid 1px #E6E6E6 !important;">기준환율</th>
                        <th style="border-left:solid 1px #E6E6E6 !important;">환율기준일</th>
                        <th style="border-left:solid 1px #E6E6E6 !important;">달러변환</th>
                    </tr>
                    <tr>
                        <td>
                            <span class="font-16 text-danger bold">
                                {% total %}원
                            </span>
                        </td>
                        <td>{% $.setNumberFormat(estimate.contents.fabricCost) %}원</td>
                        <td>{% $.setNumberFormat(estimate.contents.subFabricCost) %}원</td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.laborCost) %}원
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.exchange) %}원
                        </td>
                        <td>
                            {% estimate.contents.exchangeDt %}
                        </td>
                        <td>
                            <div v-if="0 >= estimate.contents.laborCost || 0 >= estimate.contents.exchange">0$</div>
                            <div v-if="estimate.contents.laborCost > 0 && estimate.contents.exchange > 0">{% (estimate.contents.laborCost/estimate.contents.exchange).toFixed(2) %}$</div>
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.marginCost) %}원
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.dutyCost) %}원
                        </td>
                        <td>
                            {% estimate.contents.deliveryType %}
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.managementCost) %}원
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.prdMoq) %}
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.priceMoq) %}
                        </td>
                        <td>
                            {% $.setNumberFormat(estimate.contents.addPrice) %}원
                        </td>
                    </tr>
                </table>
            </div>

            <div class="">
                <div class="">
                    <div class="flo-left font-16 bold">
                        원단정보
                    </div>
                    <div class="flo-right"></div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col style="width:6%" />
                            <col style="width:6%" />
                            <col style="width:14%" />
                            <col style="width:12%" />
                            <col style="width:9%" />
                            <col style="width:9%" />
                            <col style="width:7%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col style="width:5%" />
                            <col  />
                            <col style="width:6%" />
                        </colgroup>
                        <tr>
                            <th>부위</th>
                            <th>부착위치</th>
                            <th>자재(or원단)명</th>
                            <th>혼용율</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>가요척(or수량)</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>제조국</th>
                            <th>비고</th>
                            <th>관리등록</th>
                        </tr>
                        <tr v-for="(fabric, fabricIndex) in estimate.contents.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                            <td>
                                {% fabric.no %}
                            </td>
                            <td>
                                {% fabric.attached %}
                            </td>
                            <td>
                                {% fabric.fabricName %}
                            </td>
                            <td>
                                {% fabric.fabricMix %}
                            </td>
                            <td>
                                {% fabric.color %}
                            </td>
                            <td>
                                {% fabric.spec %}
                            </td>
                            <td>
                                {% fabric.meas %}
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.unitPrice) %}원
                            </td>
                            <td>
                                {% $.setNumberFormat(fabric.price) %}원
                            </td>
                            <td class="ta-c">
                                <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-show="!$.isEmpty(fabric.makeNational)" ></i>
                                <span v-show="$.isEmpty(fabric.makeNational)">미정</span>
                            </td>
                            <td>
                                {% fabric.memo %}
                            </td>
                            <td>
                                <div class="btn btn-sm btn-gray" v-if="!estimate.isFactory" @click="ImsProductService.addQb(fabric,estimate.styleSno)">관리등록</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div>
                <div class="">
                    <div class="flo-left  font-16 bold">
                        부자재정보
                    </div>
                    <div class="flo-right">
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col class="w-6p"  />
                            <col class="w-14p" />
                            <col class="w-8p"  />
                            <col class="w-9p"  />
                            <col class="w-9p"  />
                            <col class="w-7p"  />
                            <col class="w-8p"  />
                            <col class="w-8p"  />
                            <col class="w-5p"  />
                            <col class="w-5p"  />
                            <col class="w-12p"  />
                            <col style="width:195px"  />
                        </colgroup>
                        <tr>
                            <th>부위</th>
                            <th>자재명</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>수량</th>
                            <th>단위</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>부자재업체</th>
                            <th colspan="3">비고</th>
                        </tr>
                        <tr v-for="(subFabric, subFabricIndex) in estimate.contents.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                            <td>
                                {% subFabric.no %}
                            </td>
                            <td>
                                {% subFabric.subFabricName %}
                            </td>
                            <td>
                                {% subFabric.color %}
                            </td>
                            <td>
                                {% subFabric.spec %}
                            </td>
                            <td>
                                {% subFabric.meas %}
                            </td>
                            <td>
                                {% subFabric.unit %}
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.unitPrice) %}원
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.price) %}원
                            </td>
                            <td>
                                {% subFabric.company %}
                            </td>
                            <td  colspan="3">
                                {% subFabric.memo %}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <hr>

    <div class="text-center" style="margin-bottom:50px;">
        <span v-show="3 > estimate.reqStatus">
            <div class="btn btn-red btn-lg" @click="saveEstimateRes(estimate.reqStatus)">임시저장</div>
            <?php if( 43 == $managerSno ){ ?>
                <div class="btn btn-blue btn-lg" @click="saveEstimateResComplete(3)" v-show="3 > estimate.reqStatus && 0 !== estimate.reqStatus">처리완료</div>
            <?php }else{ ?>
                <div class="btn btn-blue btn-lg" @click="saveEstimateResComplete(3)" v-show="3 > estimate.reqStatus && 0 !== estimate.reqStatus && 43 != estimate.reqFactory">처리완료</div>
            <?php } ?>
            <div class="btn btn-blue btn-lg" @click="saveEstimateResComplete(1)" v-show="0 === estimate.reqStatus" @click="saveEstimateResComplete(1)">요청하기</div>
        </span>

        <div class="btn btn-red btn-lg" @click="saveEstimateRes(estimate.reqStatus)"  v-if="estimate.reqStatus >= 3 <?=in_array($managerSno, \Component\Ims\ImsCodeMap::FACTORY_ESTIMATE_MODIFY_MANAGER)?'&& true':'&& false' ?>" >완료된 생산가 수정</div>

        <div class="btn btn-white btn-lg" @click="popupClose()" >닫기</div>
    </div>
    
</section>

<?php include 'script/ims_factory_estimate_view_script.php'?>
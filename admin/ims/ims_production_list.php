<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_nk.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;">
    <section id="affix-show-type1">
        <h3 id="production-title"><?=$productionTitle?></h3>
        <div class="btn-group">
            <!--
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn">
            <input type="button" value="프로젝트 일괄 등록" class="btn btn-red-line" onclick="$('.excel-upload-goods-info').show('fade')">
            -->
        </div>
    </section>
    <!--틀고정-->
    <table id="affix-show-type2" class="table table-rows " style="margin:0 !important; display: none ">
        <colgroup>
            <col style="width:11%"/><!--        고객사/프로젝트-->
            <col style="width:1.2%"/><!--      체크/번호-->
            <col style="width:11%"/><!--        스타일-->
            <col style="width:7%"/><!--        납기일-->
            <col style="width:54%"/><!--        스케쥴-->
        </colgroup>
        <thead>
        <tr>
            <th>고객사</th>
            <th >
                <input type="checkbox" data-target-name="prdSno" @click="toggleAllCheck()" id="prdAllCheck" >
            </th>
            <th>스타일/상태</th>
            <th>납기일</th>
            <th>스케쥴</th>
        </tr>
        </thead>
    </table>
</div>

<section id="imsApp" class="project-view">
    <div class="row">

        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-sm">
                            <col class="width-3xl">
                            <col class="width-sm">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="2">
                                검색어
                            </th>
                            <td rowspan="2">
                                <div v-for="(keyCondition,multiKeyIndex) in productionSearchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchProduction()" />
                                    <div class="btn btn-sm btn-red" @click="productionSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === productionSearchCondition.multiKey.length ">+추가</div>
                                    <div class="btn btn-sm btn-gray" @click="productionSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="productionSearchCondition.multiKey.length > 1 ">-제거</div>
                                </div>
                                <div class="mgb5">
                                    다중 검색 :
                                    <select class="form-control" v-model="productionSearchCondition.multiCondition">
                                        <option value="AND">AND (그리고)</option>
                                        <option value="OR">OR (또는)</option>
                                    </select>
                                </div>
                            </td>
                            <th>연도/시즌</th>
                            <td >
                                연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="productionSearchCondition.year" style="width:80px" />
                                시즌 :
                                <select class="form-control" name="projectSeason" v-model="productionSearchCondition.season">
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
                                    <input type="radio" name="productionStatus" value="0" v-model="productionSearchCondition.productionStatus" @change="searchProduction(1); $('#production-title').text('생산스케쥴관리 - 전체')" />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="productionStatus" value="4" v-model="productionSearchCondition.productionStatus" @change="searchProduction(1); $('#production-title').text('스케쥴관리')" />스케쥴관리
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="productionStatus" value="2" v-model="productionSearchCondition.productionStatus" @change="searchProduction(1); $('#production-title').text('스케쥴입력요청')" />스케쥴입력요청
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="productionStatus" value="3" v-model="productionSearchCondition.productionStatus" @change="searchProduction(1); $('#production-title').text('스케쥴확정대기')" />스케쥴확정대기
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="productionStatus" value="5" v-model="productionSearchCondition.productionStatus" @change="searchProduction(1); $('#production-title').text('생산완료')" />생산완료
                                </label>
                                <label class="radio-inline" v-if="!isFactory">
                                    <input type="radio" name="productionStatus" value="1" v-model="productionSearchCondition.productionStatus" @change="searchProduction(1); $('#production-title').text('생산준비(미발주)')" />생산준비(미발주)
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                지연/처리완료 조회
                            </th>
                            <td >
                                <div class="form-inline">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="isDelay" value="y" v-model="productionSearchCondition.isDelay"> 지연건 조회 (예정일 대비 미완료 처리 건 )
                                    </label>

                                    <label class="checkbox-inline mgl20">
                                        <input type="checkbox" name="isComplete" value="y" v-model="productionSearchCondition.isComplete"> 처리완료 조회
                                    </label>

                                    <label class="checkbox-inline mgl20">
                                        <input type="checkbox" name="isComplete" value="y" v-model="productionSearchCondition.isDelayFirst"> 최초예정일지연(4일이상)
                                    </label>
                                </div>
                            </td>
                            <th>
                                생산처
                            </th>
                            <td>
                                <?php if(!$isProduceCompany) { ?>
                                <select class="js-example-basic-single" class="form-control" style="border:solid 1px #d1d1d1" name="produceCompanySno" v-model="productionSearchCondition.produceCompanySno" @change="searchProduction(1)">
                                    <option value="">전체</option>
                                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                        <option value="<?=$key?>"  <?=$key==$search['produceCompanySno']?'selected':''?> ><?=$value?></option>
                                    <?php } ?>
                                </select>
                                <?php }else{ ?>
                                    <span class="font-16 bold"><?=$managerName?></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th>분류패킹여부</th>
                            <td >
                                <label class="radio-inline ">
                                    <input type="radio" name="packingYn" value="0" v-model="productionSearchCondition.packingYn" @change="searchProduction(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="packingYn" value="y" v-model="productionSearchCondition.packingYn" @change="searchProduction(1)"/>진행
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="packingYn" value="n" v-model="productionSearchCondition.packingYn" @change="searchProduction(1)"/>미진행
                                </label>
                            </td>
                            <th>3PL여부</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="use3pl" value="0" v-model="productionSearchCondition.use3pl" @change="searchProduction(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="use3pl" value="y" v-model="productionSearchCondition.use3pl" @change="searchProduction(1)"/>진행
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="use3pl" value="n" v-model="productionSearchCondition.use3pl" @change="searchProduction(1)"/>미진행
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>기간검색(예정일)</th>
                            <td >
                                <div style="display: flex">
                                    <div class="pdr10">
                                        <select class="form-control" style="height:25px;" v-model="productionSearchCondition.searchDateType">
                                            <option value="">선택</option>
                                            <option value="a.msDeliveryDt">이노버납기일</option>
                                            <option value="a.washExpectedDt">세탁/이화학검사일</option>
                                            <option value="a.fabricConfirmExpectedDt">원부자재확정일</option>
                                            <option value="a.fabricShipExpectedDt">원부자재선적일</option>
                                            <option value="a.qcExpectedDt">QC</option>
                                            <option value="a.cuttingExpectedDt">재단</option>
                                            <option value="a.sewExpectedDt">봉제</option>
                                            <option value="a.inlineExpectedDt">인라인</option>
                                            <option value="a.shipExpectedDt">선적일</option>
                                            <option value="a.arrivalExpectedDt">도착일</option>
                                            <option value="a.checkExpectedDt">검수일</option>
                                            <option value="a.deliveryExpectedDt">공장납기일</option>
                                        </select>
                                    </div>
                                    <div>
                                        <date-picker v-model="productionSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="width:130px;font-weight: normal"></date-picker>
                                    </div>
                                    <div style="margin-left:30px;margin-right:10px" class="font-18">~</div>
                                    <!--<div class="pd20 font-18">&nbsp;&nbsp;&nbsp;~</div>-->
                                    <div>
                                        <date-picker v-model="productionSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="width:130px;font-weight: normal;margin-left:5px"></date-picker>
                                    </div>
                                    
                                    <div class="form-inline" style="margin-left:30px">
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(productionSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                                    </div>
                                    
                                </div>

                                <div class="btn-group mgt5 display-none" >
                                    <div class="btn btn-white mgr3">오늘</div>
                                    <div class="btn btn-white mgr3">오늘</div>
                                    <div class="btn btn-white mgr3">오늘</div>
                                    <div class="btn btn-white mgr3">오늘</div>
                                    <div class="btn btn-white mgr3">오늘</div>
                                </div>

                            </td>
                            <th>폐쇄몰 사용 여부</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="useMall" value="0" v-model="productionSearchCondition.useMall" @change="searchProduction(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="useMall" value="y" v-model="productionSearchCondition.useMall" @change="searchProduction(1)"/>진행
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="useMall" value="n" v-model="productionSearchCondition.useMall" @change="searchProduction(1)"/>미진행
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                납기일 상태 조회
                            </th>
                            <td >
                                <label class="radio-inline ">
                                    <input type="radio" name="deliveryStatus" value="" v-model="productionSearchCondition.deliveryStatus" @change="searchProduction(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="deliveryStatus" value="1" v-model="productionSearchCondition.deliveryStatus" @change="searchProduction(1)"/>납기주시
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="deliveryStatus" value="2" v-model="productionSearchCondition.deliveryStatus" @change="searchProduction(1)"/>납기지연
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="deliveryStatus" value="3" v-model="productionSearchCondition.deliveryStatus" @change="searchProduction(1)"/>양호
                                </label>
                            </td>
                            <!--
                            <th>
                                스케쥴 지연 조회
                            </th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="delayStatus" value="" v-model="productionSearchCondition.delayStatus" @change="searchProduction(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="delayStatus" value="1" v-model="productionSearchCondition.delayStatus" @change="searchProduction(1)"/>지연
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="delayStatus" value="2" v-model="productionSearchCondition.delayStatus" @change="searchProduction(1)"/>정상
                                </label>
                            </td>
                            -->
                            <th>
                                프로젝트 검색
                            </th>
                            <td>
                                <select class="form-control" v-model="productionSearchCondition.projectType">
                                    <option value="all">전체 (프로젝트타입검색)</option>
                                    <?php foreach( $projectTypeMap as $k => $v){ ?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                    <?php } ?>
                                </select>

                                <div class="form-inline">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="isExcludeRtw" value="y" v-model="productionSearchCondition.isExcludeRtw"> 기성복 제외 <!--ready-to-ware-->
                                    </label>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                일정체크여부
                            </th>
                            <td colspan="99">
                                <label class="radio-inline">
                                    <input type="radio" name="scheduleCheck" value="all" v-model="productionSearchCondition.scheduleCheck" @change="searchProduction(1); $('#production-title').text('생산스케쥴관리 - 전체')" />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="scheduleCheck" value="n" v-model="productionSearchCondition.scheduleCheck" @change="searchProduction(1); $('#production-title').text('스케쥴관리')" />
                                    <i aria-hidden="true" class="fa fa-lg fa-times-circle text-danger"></i>
                                    체크안됨
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="scheduleCheck" value="y" v-model="productionSearchCondition.scheduleCheck" @change="searchProduction(1); $('#production-title').text('스케쥴입력요청')" />
                                    <i aria-hidden="true" class="fa fa-lg fa-check-circle text-green"></i>
                                    체크완료
                                </label>
                            </td>
                        </tr>

                        <!--<tr>
                            <th>프로젝트 타입</th>
                            <td colspan="99">
                                <label class="checkbox-inline" style="width:80px;">
                                    <input type="checkbox" name="projectType[]" value="" class="js-not-checkall" data-target-name="projectType[]" v-model="productionSearchCondition.projectType"> 전체
                                </label>
                                <?php /*foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ */?>
                                    <label style="width:80px;">
                                        <input class="checkbox-inline" type="checkbox" name="projectType[<?/*=$k*/?>]" value="<?/*=$k*/?>" v-model="productionSearchCondition.projectType" @change="searchProduction(1)"> <?/*=$k*/?>:<?/*=$v*/?>
                                    </label>
                                <?php /*} */?>
                            </td>
                        </tr>-->

                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchProduction()">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="productionConditionReset()">
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
                        <div class="font-16 dp-flex" >
                        
                            <span style="font-size: 18px !important;">
                                총 스타일<span class="bold text-danger pdl5">{% $.setNumberFormat(productionTotal.recode.total) %}</span> 건
                            </span>

                            <div class="mgt2 dp-flex">

                                (<div class="pdl5">
                                    고객:<span class="text-danger">{% $.setNumberFormat(productionTotal.customerTotal) %}</span>
                                </div>
                                <div class="pdl5">
                                    프로젝트:<span class="text-danger">{% $.setNumberFormat(productionTotal.projectTotal) %}</span>
                                    )</div>

                                <div class="pdl20 sl-green">
                                    <i class="fa fa-circle" ></i> 양호 <span class="">{% $.setNumberFormat(productionTotal.safeCnt) %}</span>
                                </div>
                                <div class="pdl15 sl-orange">
                                    <i class="fa fa-circle" ></i> 주시 <span class="">{% $.setNumberFormat(productionTotal.warnCnt) %}</span>
                                </div>
                                <div class="pdl15 text-danger">
                                    <i class="fa fa-circle" ></i> 지연 <span class="">{% $.setNumberFormat(productionTotal.delayCnt) %}</span>
                                </div>

                                <div class="pdl10"></div>

                            </div>
                        </div>


                        <div class="mgt10 ">

                            <span v-if="4 == productionSearchCondition.productionStatus" >
                                <div class="btn btn-white" @click="ImsProductionService.setScheduleCheck('c')">
                                    <i class="fa fa-exclamation-triangle sl-orange" aria-hidden="true" ></i> 일정체크중 처리
                                </div>
                                <div class="btn btn-white" @click="ImsProductionService.setScheduleCheck('y')">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i> 일정체크완료 처리
                                </div>

                                |

                            </span>

                            <div class="btn btn-white" @click="openModifySchedule()" >스케쥴 일괄 수정</div> |

                            <span v-if="4 == productionSearchCondition.productionStatus && true == !isFactory" class="pdl5">
                                <div class="btn btn-red btn-red-line2" @click="ImsProductionService.setProduceStatusBatch(99)">생산완료처리</div>
                            </span>

                            <span v-if="2 == productionSearchCondition.productionStatus" class="pdl5">
                                <div class="btn btn-white" @click="ImsProductionService.setProduceStatusBatch(20)">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    스케쥴 입력 완료 처리
                                </div>
                            </span>

                            <span v-if="3 == productionSearchCondition.productionStatus && true == !isFactory" class="pdl5">
                                <div class="btn btn-white" @click="ImsProductionService.setProduceStatusBatch(10)">
                                    <i class="fa fa-lg fa-times-circle text-danger" aria-hidden="true" ></i> 스케쥴 재입력 요청(반려)
                                </div>
                                <div class="btn btn-white" @click="ImsProductionService.setProduceStatusBatch(30)">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    스케쥴 확정 처리
                                </div>
                            </span>

                        </div>

                        <div class="floating-area " style="z-index:99999 !important;">

                            <span v-if="4 == productionSearchCondition.productionStatus">
                                <div class="btn btn-white w-100p mgt5" @click="ImsProductionService.setScheduleCheck('c')">
                                    <i class="fa fa-exclamation-triangle sl-orange" aria-hidden="true" ></i> 일정체크중 처리
                                </div>
                                <div class="btn btn-white w-100p mgt5" @click="ImsProductionService.setScheduleCheck('y')">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i> 일정체크완료 처리
                                </div>
                            </span>

                            <div class="btn btn-white w-100p mgt5" @click="openModifySchedule()" >스케쥴 일괄 수정</div>

                            <span v-if="4 == productionSearchCondition.productionStatus && true == !isFactory" >
                                <div class="btn btn-red btn-red-line2 w-100p mgt5" @click="ImsProductionService.setProduceStatusBatch(99)">생산완료처리</div>
                            </span>

                            <span v-if="2 == productionSearchCondition.productionStatus">
                                <div class="btn btn-white w-100p mgt5" @click="ImsProductionService.setProduceStatusBatch(20)">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    스케쥴 입력 완료 처리
                                </div>
                            </span>

                            <span v-if="3 == productionSearchCondition.productionStatus && true == !isFactory">
                                <div class="btn btn-white w-100p mgt5" @click="ImsProductionService.setProduceStatusBatch(10)">
                                    <i class="fa fa-lg fa-times-circle text-danger" aria-hidden="true" ></i> 스케쥴 재입력 요청(반려)
                                </div>
                                <div class="btn btn-white w-100p mgt5" @click="ImsProductionService.setProduceStatusBatch(30)">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    스케쥴 확정 처리
                                </div>
                            </span>

                        </div>


                        <?php if(!$imsProduceCompany) { ?>
                            <!--<div class="btn btn-gray" @click="setRevokeQb(1)">요청상태로변경(임시)</div>-->
                        <?php }else{ ?>
                            <!--<div class="btn btn-blue" @click="openRequestView()">처리완료</div>
                            <span class="notice-info">처리 완료된 항목을 다시 처리완료해도 적용되지 않습니다.</span>-->
                        <?php } ?>
                    </div>
                    <div class="flo-right mgb5">
                        <!--<div class="ta-r mgb5">
                            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
                        </div>-->
                        <div class="" style="display: flex;padding-top:60px">

                            <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(2)">엑셀다운로드(Simple)</button>
                            <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload(1)">엑셀다운로드</button>

                            <select @change="searchProduction()" class="form-control mgl5" v-model="productionSearchCondition.sort">
                                <option value="C,desc">납기D/L ▼</option>
                                <option value="C,asc">납기D/L ▲</option>
                                <option value="B,desc">고객사별 ▼</option>
                                <option value="B,asc">고객사별 ▲</option>
                                <option value="D,asc">등록일 ▲</option>
                                <option value="D,desc">등록일 ▼</option>
                            </select>

                            <select v-model="productionSearchCondition.pageNum" @change="searchProduction()" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="">
                    <!--ims_product_production 와 함께사용-->
                    <?php include 'template/ims_product_production_list_template3.php'?>
                </div>

                <div id="production-page" v-html="productionPage" class="ta-c"></div>

            </div>

        </div>


        <!--처리완료 팝업-->

    </div>

    <div style="margin-bottom:150px"></div>

    <section v-if="null !== productionView">
        <?php include 'template/ims_product_production_detail_template.php'?>
    </section>
    <section >
        <?php include 'template/ims_product_production_modify_template.php'?>
    </section>
</section>

<?php include 'script/ims_production_list_script.php'?>

<?php include 'library_all.php'?>
<?php include 'library.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<section id="imsApp" class="project-view">

    <div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;">
        <section id="affix-show-type1">
            <h3 id="production-title"><?=$title?></h3>
            <div class="btn-group">
                <!--<input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" @click="openCommonPopup('project_reg', 900, 850, {})" />
                <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" @click="location.href='./ims_project_reg.php?status=<?=$requestParam['status']?>';" />
                -->
                <!--<input type="button" value="리오더" class="btn btn-red btn-reg hover-btn" >-->
            </div>
        </section>
        <!--틀고정-->
        <section id="affix-show-type2" style="margin:0 !important; display: none ">
            <table class="table table-rows" style="margin-bottom:0 !important; "></table>
        </section>
    </div>


    <div class="row">

        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0">
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
                                <div v-for="(keyCondition,multiKeyIndex) in projectSearchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchProject()" />
                                    <div class="btn btn-sm btn-red" @click="projectSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === projectSearchCondition.multiKey.length ">+추가</div>
                                    <div class="btn btn-sm btn-gray" @click="projectSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="projectSearchCondition.multiKey.length > 1 ">-제거</div>
                                </div>
                                <div class="mgb5">
                                    다중 검색 :
                                    <select class="form-control" v-model="projectSearchCondition.multiCondition">
                                        <option value="AND">AND (그리고)</option>
                                        <option value="OR">OR (또는)</option>
                                    </select>
                                </div>
                            </td>
                            <th>연도/시즌</th>
                            <td >
                                연도 : <input type="text" name="projectYear"  class="form-control w80p" placeholder="연도" v-model="projectSearchCondition.projectYear" style="width:80px" />
                                시즌 :
                                <select class="form-control" name="projectSeason" v-model="projectSearchCondition.projectSeason">
                                    <option value="">선택</option>
                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                프로젝트 검색
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]" :checked="0 >= projectSearchCondition.projectTypeChk.length?'checked':''" @click="projectSearchCondition.projectTypeChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  v-model="projectSearchCondition.projectTypeChk"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="isExcludeRtw" value="y" v-model="projectSearchCondition.isExcludeRtw"> 기성복 제외 <!--ready-to-ware-->
                                    </label>

                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="isExcludeNextSeason" value="y" v-model="projectSearchCondition.isExcludeNextSeason"> 다음시즌 자동등록 발주대기 제외
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>분류패킹/3PL</th>
                            <td >
                                <div class="dp-flex gap10">

                                    <div>
                                        <b>분류패킹 :</b>
                                        <label class="radio-inline ">
                                            <input type="radio" name="packingYn" value="0" v-model="projectSearchCondition.packingYn" @change="searchProject(1)"/>전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="packingYn" value="y" v-model="projectSearchCondition.packingYn" @change="searchProject(1)"/>진행
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="packingYn" value="n" v-model="projectSearchCondition.packingYn" @change="searchProject(1)"/>미진행
                                        </label>
                                    </div>

                                    <div class="mgl15">
                                        <b>3PL :</b>
                                        <label class="radio-inline ">
                                            <input type="radio" name="use3pl" value="0" v-model="projectSearchCondition.use3pl" @change="searchProject(1)"/>전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="use3pl" value="y" v-model="projectSearchCondition.use3pl" @change="searchProject(1)"/>진행
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="use3pl" value="n" v-model="projectSearchCondition.use3pl" @change="searchProject(1)"/>미진행
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <th>생산상태 검색</th>
                            <td >
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="isProduction[]" value="all" class="js-not-checkall" data-target-name="isProduction[]" :checked="0 >= projectSearchCondition.productionChk.length?'checked':''" @click="projectSearchCondition.productionChk=[]"> 전체
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline" type="checkbox" name="isProduction[]" value="0"  v-model="projectSearchCondition.productionChk"> 생산미진행
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline" type="checkbox" name="isProduction[]" value="1"  v-model="projectSearchCondition.productionChk"> 생산진행건
                                </label>
                                <label class="mgr10">
                                    <input class="checkbox-inline" type="checkbox" name="isProduction[]" value="2"  v-model="projectSearchCondition.productionChk"> 생산완료건
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>회계 반영 여부</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="isBookRegistered" value="0" v-model="projectSearchCondition.isBookRegistered" @change="searchProject(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isBookRegistered" value="y" v-model="projectSearchCondition.isBookRegistered" @change="searchProject(1)"/>회계반영
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isBookRegistered" value="n" v-model="projectSearchCondition.isBookRegistered" @change="searchProject(1)"/>회계미반영
                                </label>
                            </td>
                            <th>폐쇄몰 사용 여부</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="useMall" value="0" v-model="projectSearchCondition.useMall" @change="searchProject(1)"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="useMall" value="y" v-model="projectSearchCondition.useMall" @change="searchProject(1)"/>진행
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="useMall" value="n" v-model="projectSearchCondition.useMall" @change="searchProject(1)"/>미진행
                                </label>
                            </td>
                        </tr>
                        <tr class="display-none">
                            <th>기간검색</th>
                            <td colspan="99">
                                <div style="display: flex">
                                    <div class="pdr10">
                                        <select class="form-control" style="height:25px;" v-model="projectSearchCondition.searchDateType">
                                            <option value="">선택</option>
                                            <option value="prj.regDt">등록일</option>
                                            <!--
                                            <?php foreach($listSetupData['list'] as $eachKey => $each) { ?>
                                                <?php if(!empty($each['period'])) { ?>
                                                    <option value="prj.<?=$each['period']?>"><?=$each['periodTitle']?></option>
                                                <?php } ?>
                                            <?php } ?>
                                            -->
                                        </select>
                                    </div>
                                    <div>
                                        <date-picker v-model="projectSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="width:140px;font-weight: normal"></date-picker>
                                    </div>
                                    <div class="pd20 font-18">&nbsp;&nbsp;&nbsp;~</div>
                                    <div>
                                        <date-picker v-model="projectSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="width:140px;font-weight: normal;margin-left:10px"></date-picker>
                                    </div>

                                    <div class="form-inline" style="margin-left:30px">
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(projectSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                                    </div>

                                </div>

                            </td>
                            <th>
                                지연건 조회
                            </th>
                            <td >
                                <div class="form-inline">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="isDelay" value="y" v-model="projectSearchCondition.isDelay"> 지연건 조회 (예정일 대비 미완료 처리 건 )
                                    </label>
                                </div>
                            </td>
                        </tr>

                        <?php if( empty($requestParam['status']) ) {?>
                        <tr>
                            <th>
                                프로젝트 상태
                            </th>
                            <td colspan="99">
                                <div class="checkbox">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="orderProgressFl[]" value="all" class="js-not-checkall" data-target-name="orderProgressFl[]"  :checked="0 >= projectSearchCondition.orderProgressChk.length?'checked':''" @click="projectSearchCondition.orderProgressChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressFl[]" value="<?=$k?>"  v-model="projectSearchCondition.orderProgressChk"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchProject()">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="projectConditionReset()">
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
                                총 <span class="bold text-danger pdl5">{% $.setNumberFormat(projectTotal.recode.total) %}</span> 건

                                <span class="font-11">
                                    (스타일 : {% $.setNumberFormat(projectTotal.styleTotal) %}개)
                                </span>

                            </span>
                            <span class="mgl15">
                                <div class="btn btn-white btn-sm" @click="allOpen()">+ 펼치기</div>
                                <div class="btn btn-white btn-sm" @click="allClose()">- 접기</div>
                            </span>

                            <span class="mgl15 dp-flex">
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('y')">
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    회계반영
                                </div>
                                <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('n')">
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i> 회계반영 취소
                                </div>
                            </span>
                        </div>

                    </div>
                    <div class="flo-right mgb5">
                        <div class="" style="display: flex;padding-top:20px">

                            <?php if( '60' == $requestParam['status'] ) { ?>
                            <input type="button" value="발주/사양서 점검항목" class="btn btn-white" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/prd_order_check_guide.pdf')?>&fileName=<?=urlencode('발주(사양서).pdf')?>'" >
                            <?php } ?>

                            <?php if( empty($requestParam['status']) || '90' == $requestParam['status'] ) { ?>
                            <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload()">엑셀다운로드</button>
                            <?php } ?>

                            <select @change="searchProject()" class="form-control mgl5" v-model="projectSearchCondition.sort">
                                <option value="P3,asc">희망납기일 ▲</option>
                                <option value="P3,desc">희망납기일 ▼</option>
                                <option value="P2,asc">연도/등록일 ▲</option>
                                <option value="P2,desc">연도/등록일 ▼</option>
                                <option value="P4,asc">매출규모 ▲</option>
                                <option value="P4,desc">매출규모 ▼</option>
                                <option value="P1,asc">등록일 ▲</option>
                                <option value="P1,desc">등록일 ▼</option>
                            </select>

                            <select v-model="projectSearchCondition.pageNum" @change="searchProject()" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                                <option value="200">200개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="">
                    <!-- 리스트 템플릿 특이 타입일 경우 분기해서 사용 -->
                    <?php include 'template/ims_list_template.php'?>
                </div>

                <div id="project-page" v-html="projectPage" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_list_script.php'?>

<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>전체 스타일 리스트</h3>
            <div class="btn-group font-20 pdt10">
            </div>
        </div>
    </form>

    <!-- TODO 검색 화면
    //고객명, 프로젝트번호, 스타일코드
    //연도, 시즌
    -->
    <div class="row" >
        <div class="col-xs-12" >

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
                                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
                                    <div class="btn btn-sm btn-red" @click="addMultiKey" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
                                    <div class="btn btn-sm btn-gray" @click="searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="searchCondition.multiKey.length > 1 ">-제거</div>
                                </div>
                                <div class="mgb5">
                                    다중 검색 :
                                    <select class="form-control" v-model="searchCondition.multiCondition">
                                        <option value="AND">AND (그리고)</option>
                                        <option value="OR">OR (또는)</option>
                                    </select>
                                </div>
                            </td>
                            <th>연도/시즌</th>
                            <td >
                                연도 : <input type="text" name="projectYear"  class="form-control w80p" placeholder="연도" v-model="searchCondition.projectYear" style="width:80px" />
                                시즌 :
                                <select class="form-control" name="projectSeason" v-model="searchCondition.projectSeason">
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
                                            <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]" :checked="0 >= searchCondition.projectTypeChk.length?'checked':''" @click="searchCondition.projectTypeChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  v-model="searchCondition.projectTypeChk"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                프로젝트 상태
                            </th>
                            <td colspan="99">
                                <div class="checkbox">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="orderProgressFl[]" value="all" class="js-not-checkall" data-target-name="orderProgressFl[]"  :checked="0 >= searchCondition.orderProgressChk.length?'checked':''" @click="searchCondition.orderProgressChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressFl[]" value="<?=$k?>"  v-model="searchCondition.orderProgressChk"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                작지 자료등록여부
                            </th>
                            <td colspan="99">
                                <div class="checkbox">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="eworkDataChk[]" value="all" class="js-not-checkall" data-target-name="eworkDataChk[]"  :checked="0 >= searchCondition.eworkDataChk.length?'checked':''" @click="searchCondition.eworkDataChk=[]"> 전체
                                        </label>

                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="fileMain"  v-model="searchCondition.eworkDataChk"> 메인
                                        </label>

                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="fileMark1"  v-model="searchCondition.eworkDataChk"> 마크
                                        </label>

                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="filePosition"  v-model="searchCondition.eworkDataChk"> 케어
                                        </label>

                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="fileSpec"  v-model="searchCondition.eworkDataChk"> 스펙(도안)
                                        </label>

                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="specData"  v-model="searchCondition.eworkDataChk"> 스펙(데이터)
                                        </label>
                                        
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="material"  v-model="searchCondition.eworkDataChk"> 자재
                                        </label>
                                        
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="filePacking1"  v-model="searchCondition.eworkDataChk"> 포장
                                        </label>

                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="eworkDataChk[]" value="fileBatek"  v-model="searchCondition.eworkDataChk"> 바텍
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshList(1)">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="conditionReset()">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
            </div>

            <div class="">
                <div class="flo-left mgb5">
                    <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                        </span>
                    </div>
                </div>
                <div class="flo-right mgb5">
                    <div class="" style="display: flex; ">

                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(5)">생산가격리스트</button>

                        <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload(4)">생산가부자재표</button>
                        <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload(3)">생산가원자재표</button>
                        <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload(2)">관리원단표</button>
                        <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload(1)">다운로드</button>

                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="B,desc">고객사별 ▼</option>
                            <option value="B,asc">고객사별 ▲</option>
                            <option value="D,asc">등록일 ▲</option>
                            <option value="D,desc">등록일 ▼</option>
                        </select>

                        <select @change="refreshList(1)" v-model="searchCondition.pageNum" class="form-control mgl5">
                            <option value="5">5개 보기</option>
                            <option value="20">20개 보기</option>
                            <option value="50">50개 보기</option>
                            <option value="100">100개 보기</option>
                        </select>
                    </div>
                </div>
            </div>

            <table class="table table-rows table-default-center table-td-height30 mgt5">
                <?=$tableTitleData?>
                <tr v-for="(each , index) in listData" >
                    <?=$tableBodyData?>
                </tr>
            </table>

            <div id="style-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_style_admin_script.php'?>

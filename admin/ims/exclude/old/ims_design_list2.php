<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<section id="imsApp" class="project-view">

    <!--타이틀-->
    <div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;">
        <section id="affix-show-type1">
            <h3 id="production-title">영업 리스트 <span class="font-11">(기획/제작 전 협의 및 정보 취득)</span></h3>
            <div class="btn-group">
                <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" @click="location.href='./ims_project_reg.php?status=<?=$requestParam['status']?>';" />
            </div>
        </section>
        <!--틀고정-->
        <section id="affix-show-type2" style="margin:0 !important; display: none ">
            <table class="table table-rows" style="margin-bottom:0 !important; "></table>
        </section>
    </div>

    <!--검색-->
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
                                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchProject()" />
                                    <div class="btn btn-sm btn-red" @click="searchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
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
                            <th></th>
                            <td >

                            </td>
                        </tr>
                        <tr>
                            <th>
                                프로젝트 검색
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div >

                                    </div>
                                </div>
                            </td>
                        </tr>
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
                                총 <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                            </span>
                            <span class="mgl15">
                                <!--<div class="btn btn-white btn-sm" @click="allOpen()">+ 펼치기</div>
                                <div class="btn btn-white btn-sm" @click="allClose()">- 접기</div>-->
                            </span>
                        </div>

                    </div>
                    <div class="flo-right mgb5">
                        <div class="" style="display: flex;padding-top:20px">
                            <select @change="searchProject()" class="form-control mgl5" v-model="searchCondition.sort">
                                <option value="P3,asc">희망납기일 ▲</option>
                                <option value="P3,desc">희망납기일 ▼</option>
                                <option value="P2,asc">연도/등록일 ▲</option>
                                <option value="P2,desc">연도/등록일 ▼</option>
                                <option value="P4,asc">매출규모 ▲</option>
                                <option value="P4,desc">매출규모 ▼</option>
                                <option value="P1,asc">등록일 ▲</option>
                                <option value="P1,desc">등록일 ▼</option>
                                <option value="P5,asc">진행상태 ▲</option>
                                <option value="P5,desc">진행상태 ▼</option>
                            </select>

                            <select v-model="searchCondition.pageNum" @change="searchProject()" class="form-control mgl5">
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
                    <table class="table table-rows table-default-center table-td-height30 mgt5" v-if="!$.isEmpty(searchData)">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData"/>
                        </colgroup>
                        <tr>
                            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
                            <th>번호</th>
                            <th v-for="fieldData in searchData.fieldData"  :colspan="$.isEmpty(fieldData.colspan)?'1':fieldData.colspan"  v-if="true != fieldData.skip" >{% fieldData.title %}</th>
                        </tr>
                        <tr v-for="(each , index) in listData" >
                            <td>
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                            </td>
                            <td>
                                <div>{% (listTotal.idx-index) %}</div>
                            </td>
                            <td v-for="fieldData in searchData.fieldData" >

                                <?php include 'nlist/list_template.php'?>

                                <!--미팅보고서-->
                                <div v-if="'meeting' === fieldData.name">
                                    <div class="btn btn-white btn-sm ">
                                        <a :href="`ims_project_view.php?sno=${each.projectSno}&status=${each.projectStatus}`" target="_blank" class="text-danger" style="color:black!important;">보기</a>
                                    </div>
                                </div>

                                <!--메모-->
                                <div v-if="'salesMemo' === fieldData.name">
                                    <div class="btn btn-white btn-sm ">
                                        <a :href="`ims_project_view.php?sno=${each.projectSno}&status=${each.projectStatus}`" target="_blank" class="text-danger" style="color:black!important;">보기</a>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    </table>
                </div>

                <div id="project-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_design_script.php'?>

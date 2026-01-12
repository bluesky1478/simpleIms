<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; height:42px !important;" id="affix-menu">
    <section id="affix-show-type1">
        <h3 >발주 리스트 <span class="font-11 font-normal"></span></h3>
        <div class="btn-group">
            <!--
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" @click="location.href='./ims_project_reg.php?status=<?=$requestParam['status']?>';" />
            -->
        </div>
    </section>
    <!--틀고정-->
    <section id="affix-show-type2" style="margin:0 !important; display: none "></section>
</div>

<section id="imsApp" class="project-view">
    <!--검색-->
    <div class="row">
        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0 border-top-none ">
                        <colgroup>
                            <col class="w-6p">
                            <col class="w-30p">
                            <col class="w-8p">
                            <col class="w-50p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="3" class="text-center">
                                검색어
                            </th>
                            <td rowspan="3">
                                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
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

                            <th>
                                구분
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div class="checkbox ">
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="orderType[]" value="all" class="js-not-checkall" data-target-name="orderType[]"
                                                   :checked="0 >= searchCondition.orderType.length?'checked':''" @click="searchCondition.orderType=[]"> 전체
                                        </label>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="orderType[]" value="new"
                                                   v-model="searchCondition.orderType"  > 신규/개선
                                        </label>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="orderType[]" value="reorder"
                                                   v-model="searchCondition.orderType"  > 리오더
                                        </label>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="orderType[]" value="rtw"
                                                   v-model="searchCondition.orderType"  > 기성
                                        </label>
                                    </div>
                                </div>
                            </td>

                        </tr>
                        <tr>
                            <th>
                                판매가/생산가
                            </th>
                            <td>
                                <div class="dp-flex">

                                    <div class="round-box bg-light-gray2">
                                        <b>판매가 :</b>
                                        <label class="radio-inline ">
                                            <input type="radio" name="priceStatus" value="all" v-model="searchCondition.priceStatus" @change="refreshList(1)" />전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="priceStatus" value="y" v-model="searchCondition.priceStatus"  @change="refreshList(1)" />확정
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="priceStatus" value="n" v-model="searchCondition.priceStatus"  @change="refreshList(1)" />미확정
                                        </label>
                                    </div>

                                    <div class="mgl10"></div>

                                    <div class="round-box bg-light-gray2">
                                        <b>생산가 :</b>
                                        <label class="radio-inline ">
                                            <input type="radio" name="costStatus" value="all" v-model="searchCondition.costStatus" @change="refreshList(1)" />전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="costStatus" value="y" v-model="searchCondition.costStatus"  @change="refreshList(1)" />확정
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="costStatus" value="n" v-model="searchCondition.costStatus"  @change="refreshList(1)" />미확정
                                        </label>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>아소트/작지</th>
                            <td>
                                <div class="dp-flex">
                                    <div class="round-box bg-light-gray2">
                                        <b>아소트 :</b>
                                        <label class="radio-inline ">
                                            <input type="radio" name="assortStatus" value="all" v-model="searchCondition.assortStatus" @change="refreshList(1)" />전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="assortStatus" value="y" v-model="searchCondition.assortStatus"  @change="refreshList(1)" />확정
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="assortStatus" value="n" v-model="searchCondition.assortStatus"  @change="refreshList(1)" />미확정
                                        </label>
                                    </div>

                                    <div class="mgl10"></div>

                                    <div class="round-box bg-light-gray2">
                                        <b>작 지 :&nbsp;&nbsp;</b>
                                        <label class="radio-inline ">
                                            <input type="radio" name="workStatus" value="all" v-model="searchCondition.workStatus" @change="refreshList(1)" />전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="workStatus" value="y" v-model="searchCondition.workStatus"  @change="refreshList(1)" />확정
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="workStatus" value="n" v-model="searchCondition.workStatus"  @change="refreshList(1)" />미확정
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!--<tr>
                            <th>생산처</th>
                            <td>

                            </td>
                        </tr>-->
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
            </div>

            <div class="dp-flex dp-flex-center">
                <div class="btn btn-lg btn-black w-100px" @click="refreshList(1)">검색</div>
                <div class="btn btn-lg btn-white w-100px" @click="conditionReset()">초기화</div>
            </div>


            <div >
                <div class="dp-flex" style="justify-content: space-between">
                    <div class="mgb5 mgt25">
                        <div class="dp-flex dp-flex-gap10 font-16">

                            <div class="total hover-btn cursor-pointer" @click="searchCondition.projectStatus='';refreshList(1)">
                                총 <span class="text-danger">{% $.setNumberFormat(listTotal.typeAllCnt) %}</span> 건
                                <span class="font-13">(스타일:<span class="text-danger">{% $.setNumberFormat(listTotal.recode.total) %}</span>개)</span>
                            </div>

                            <div :class="'hover-btn cursor-pointer font-14 '"  @click="searchCondition.orderType=['new'];refreshList(1)">
                                신규(<span class="text-danger">{% $.setNumberFormat(listTotal.type1Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer  font-14 '" @click="searchCondition.orderType=['reorder'];refreshList(1)">
                                리오더(<span class="text-danger">{% $.setNumberFormat(listTotal.type2Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer  font-14 '" @click="searchCondition.orderType=['rtw'];refreshList(1)">
                                기성복(<span class="text-danger">{% $.setNumberFormat(listTotal.type2Cnt) %}</span>)
                            </div>
                        </div>

                    </div>
                    <div class="mgb5">
                        <div class="" style="display: flex;padding-top:20px">
                            <?php if(empty($imsProduceCompany)) { ?>
                            <input type="button" value="일괄수정" class="btn btn-white btn-red btn-red-line2" @click="vueApp.isModify = true;" v-show="!isModify">
                            <input type="button" value="저장" class="btn btn-red" @click="save()" v-show="isModify">&nbsp;
                            <input type="button" value="일괄수정취소" class="btn btn-red btn-red-line2" @click="vueApp.isModify = false;" v-show="isModify">
                            <?php } ?>

                            <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort" >
                                <option value="P7,asc">발주D/L ▲</option>
                                <option value="P7,desc">발주D/L ▼</option>
                                <option value="P3,asc">고객납기일 ▲</option>
                                <option value="P3,desc">고객납기일 ▼</option>
                                <option value="P1,asc">등록일 ▲</option>
                                <option value="P1,desc">등록일 ▼</option>
                            </select>

                            <select v-model="searchCondition.pageNum" @change="refreshList(1)" class="form-control mgl5">
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
                    <?php include 'ims_list_qc_style.php'?>
                </div>

                <div id="design-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>
    <?php include 'nlist/_emergency_layer_popup.php'?>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_qc_script.php'?>

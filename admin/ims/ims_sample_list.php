<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>샘플 리스트</h3>
        <div class="btn-group">
        </div>
    </div>
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
                            <th colspan="1">검색어</th>
                            <td colspan="3">
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
                        </tr>
                        <tr>
                            <th>샘플실</th>
                            <td colspan="3">
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline " style="width:115px">
                                            <input type="checkbox" name="aChkboxSchSampleFactorySno[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchSampleFactorySno[]"
                                                   :checked="0 >= searchCondition.aChkboxSchSampleFactorySno.length?'checked':''" @click="searchCondition.aChkboxSchSampleFactorySno=[]"> 전체
                                        </label>
                                        <?php foreach($sampleFactoryMap as $k => $v){ ?>
                                            <label class="" style="width:115px">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchSampleFactorySno[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchSampleFactorySno"  >
                                                <?=$v?>
                                            </label>
                                        <?php } ?>
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
                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="D,asc">샘플 등록일시 ▲</option>
                            <option value="D,desc">샘플 등록일시 ▼</option>
                            <option value="SN,asc">스타일명 ▲</option>
                            <option value="SN,desc">스타일명 ▼</option>
                        </select>
                        <select @change="refreshList(1)" v-model="searchCondition.pageNum" class="form-control mgl5">
                            <option value="5">5개 보기</option>
                            <option value="20">20개 보기</option>
                            <option value="50">50개 보기</option>
                            <option value="100">100개 보기</option>
                            <option value="200">200개 보기</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--list start-->
            <div>
                <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                    <colgroup>
                        <col class="w-2p" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip" />
                    </colgroup>
                    <tr>
                        <th >번호</th>
                        <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                            {% fieldData.title %}
                        </th>
                    </tr>
                    <tr  v-if="0 >= listData.length">
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    <tr v-for="(val, key) in listData" class="hover-light">
                        <td >{% (listTotal.idx - key) %}</td>
                        <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                            <span v-if="fieldData.type === 'pop_detail_customer'" @click="openCustomer(val.customerSno)" class="sl-blue cursor-pointer hover-btn">
                                {% val[fieldData.name] %}
                            </span>
                            <span v-else-if="fieldData.type === 'pop_detail_project'" @click="window.open(`ims_view2.php?sno=${val.projectSno}`)" class="text-danger cursor-pointer hover-btn">
                                {% val[fieldData.name] %}
                            </span>
                            <div v-else-if="fieldData.type === 'pop_detail_sample'" @click="if (val.sampleType == 9) popSampleDetail(val.sno); else openCommonPopup('product_sample_new', 1550, 900, {sno:val.sno});" class="cursor-pointer hover-btn pdl5 ta-l">
                                <div class="cursor-pointer hover-btn mgt5">
                                    {% val.prdYear %}{% val.prdSeason %} {% val.productName %}의 {% val.sampleName %}
                                </div>
                                <div class="text-muted font-11 mgt5">(#{% val.styleSno %} {% val.styleCode %})</div>
                            </div>
                            <div v-else-if="fieldData.type === 'file'" class="ta-l pd0">
                                <table class="table table-pd-0 table-td-height0 mg0">
                                    <colgroup>
                                        <col class="w-40px" />
                                        <col />
                                    </colgroup>
                                    <tr>
                                        <td class="border-top-none font-11 bg-light-gray">지시</td>
                                        <td class="border-top-none ta-l pdl5" style="padding-left:5px !important;">
                                            <simple-file-only-history-upload :file="val['fileList']['sampleFile1']" :params="val" :file_div="'sampleFile1'" class="font-11 "></simple-file-only-history-upload>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-11 bg-light-gray">리뷰</td>
                                        <td class="pdl5 ta-l" style="padding-left:5px !important;">
                                            <simple-file-only-history-upload :file="val['fileList']['sampleFile4']" :params="val" :file_div="'sampleFile4'" class="font-11 "></simple-file-only-history-upload>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border-bottom-zero font-11 bg-light-gray">확정</td>
                                        <td class="border-bottom-zero font-11 pdl5 ta-l" style="padding-left:5px !important;">
                                            <simple-file-only-history-upload :file="val['fileList']['sampleFile6']" :params="val" :file_div="'sampleFile6'" class="font-11 "></simple-file-only-history-upload>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                            <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <!--list end-->
            <div id="product_sample-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_sample_list_script.php'?>

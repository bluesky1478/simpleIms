<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>고객견적 리스트</h3>
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
                            <th>견적 타입</th>
                            <td colspan="3">
                                <label class="radio-inline ">
                                    <input type="radio" name="sRadioSchEstimateType" value="" v-model="searchCondition.sRadioSchEstimateType"/>전체
                                </label>
                                <?php foreach(\Component\Ims\ImsCodeMap::CUST_ESTIMATE_TYPE as $key => $val) { ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="sRadioSchEstimateType" value="<?=$key?>" v-model="searchCondition.sRadioSchEstimateType"/><?=$val?>
                                    </label>
                                <?php } ?>
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
                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(1)">고객견적 리스트</button>
                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="D,asc">고객견적 등록일시 ▲</option>
                            <option value="D,desc">고객견적 등록일시 ▼</option>
                            <option value="CN,asc">고객명 ▲</option>
                            <option value="CN,desc">고객명 ▼</option>
                            <option value="supply,asc">총금액 ▲</option>
                            <option value="supply,desc">총금액 ▼</option>
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
                    <tr v-for="(val , key) in listData">
                        <td >{% (listTotal.idx - key) %}</td>
                        <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                            <span v-if="fieldData.type === 'pop_detail_customer'">
                                <div class="ta-l hover-btn cursor-pointer sl-blue" @click="openCustomer(val.customerSno)">
                                    {% val[fieldData.name] %}
                                </div>
                            </span>
                            <span v-else-if="fieldData.type === 'pop_detail_project'">
                                <div class="hover-btn cursor-pointer text-dnager" @click="window.open(`ims_view2.php?sno=${val.projectSno}`)">
                                    {% val[fieldData.name] %}
                                </div>
                            </span>
                            <span v-else-if="fieldData.type === 'pop_detail_estimate'">
                                <div class="ta-l hover-btn cursor-pointer" @click="window.open(`<?=$customerEstimateUrl?>?key=${val.key}`);">
                                    {% val[fieldData.name] %}
                                </div>
                            </span>
                            <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                            <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                            <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <!--list end-->
            <div id="customer_estimate-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_estimate_list_script.php'?>

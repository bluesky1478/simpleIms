<?php use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;

include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>비축 원부자재 리스트</h3>
            <div class="btn-group font-20 pdt10">
            </div>
        </div>
    </form>
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
                            <th>
                                검색어
                            </th>
                            <td>
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
                            <th>소유구분</th>
                            <td>
                                <div v-if="searchCondition.aChkboxSchInputOwn !== undefined" class="checkbox">
                                    <div>
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="aChkboxSchInputOwn[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchInputOwn[]"  :checked="0 >= searchCondition.aChkboxSchInputOwn.length?'checked':''" @click="searchCondition.aChkboxSchInputOwn=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\NkCodeMap::STORED_INPUT_OWN as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchInputOwn[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchInputOwn"> <?=$v?>
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
                        <span style="display:none; font-size: 18px !important;">
                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                        </span>
                    </div>
                </div>
                <div class="flo-right mgb5">
                    <div class="" style="display: flex; ">
                        <input type="button" class="btn btn-red btn-reg hover-btn" value="입고 등록" @click="setInput(0);" />&nbsp;
                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload()">비축 원부자재 리스트</button>
                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="A,asc">자재정보 ▲</option>
                            <option value="A,desc">자재정보 ▼</option>
                        </select>
                        <select style="display: none;" @change="refreshList(1)" v-model="searchCondition.pageNum" class="form-control mgl5">
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
                <tbody>
                <tr v-for="(val , key) in listData">
                    <td>{% key+1 %}</td>
                    <td v-if="val.rowspan_depth2 > 0" :rowspan="val.rowspan_depth2">
                        <div class="sl-blue  cursor-pointer hover-btn" @click="openCustomer(val.customerUsageSno,'stored')">
                            {% val.customerUsageName %}
                        </div>
                    </td>
                    <td v-if="val.rowspan > 0" :rowspan="val.rowspan"> {% val.fabricName %}<br/>{% val.fabricMix %}<br/>{% val.color %}</td>
                    <?php if(in_array($managerId,ImsCodeMap::STORE_MANAGER)) { ?>
                    <td v-if="val.rowspan > 0" :rowspan="val.rowspan">
                        <span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="modFabric(val.sno)">수정</span>
                        <span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="delFabric(val.sno)">삭제</span>
                    </td>
                    <?php } ?>
                    <td v-if="val.rowspan > 0" :rowspan="val.rowspan"> {% val.btn_input %}<span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="setInput(val.sno)">입고등록</span></td>
                    <td v-if="val.rowspan > 0" :rowspan="val.rowspan"> {% $.setNumberFormat(val.total_remain) %}</td>
                    <td> {% val.no_input %}</td>
                    <td> {% val.inputDt=='0000-00-00'?'-':val.inputDt %}</td>
                    <td> {% val.expireDt=='0000-00-00'?'-':val.expireDt %}</td>
                    <td> {% $.setNumberFormat(val.unitPrice) %}</td>
                    <td> {% $.setNumberFormat(val.inputQty) %}</td>
                    <td> {% $.setNumberFormat(val.total_input_price) %}</td>
                    <td> {% $.setNumberFormat(val.total_out_qty) %}</td>
                    <td><span class='font-11 sl-blue hover-btn cursor-pointer' @click="getOutputList(val.inputSno)">{% $.setNumberFormat(val.remain_qty) %}</span></td>
                    <td> {% val.inputUnit %}</td>
                    <td> <span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="setOutput(val.inputSno)">출고등록</span></td>
                    <td class="ta-l"> {% val.inputReason %}</td>
                    <td> {% val.inputOwn %}</td>
<!--                    <td>{% val.customerSno %}</td>-->
                    <td class="ta-l"> {% val.inputLocation %}</td>
                    <td> {% val.reqInputNm %}</td>
                    <td class="ta-l"> {% val.inputMemo %}</td>
                    <?php if(in_array($managerId,ImsCodeMap::STORE_MANAGER)) { ?>
                    <td>
                        <div class="dp-flex">
                            <div class='font-11 btn btn-sm btn-white hover-btn cursor-pointer' @click="modInput(val.inputSno)">수정</div><br/>
                            <div class='font-11 btn btn-sm btn-white hover-btn cursor-pointer' @click="delInput(val.inputSno)">삭제</div>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>

            <div style="display: none;" id="stored-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_stored_list_script.php'?>

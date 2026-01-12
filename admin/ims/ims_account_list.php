<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<style>
    .mx-datepicker { width:100px!important; }
</style>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>정산 관리</h3>
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
                            <col class="w-7p">
                            <col class="w-34p">
                            <col class="w-12p">
                            <col class="w-44p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="3">검색어</th>
                            <td rowspan="3">
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
                            <th>회계 반영</th>
                            <td >
                                <label class="radio-inline ">
                                    <input type="radio" name="sRadioSchIsBookRegistered" value="" v-model="searchCondition.sRadioSchIsBookRegistered"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="sRadioSchIsBookRegistered" value="y" v-model="searchCondition.sRadioSchIsBookRegistered"/>회계반영
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="sRadioSchIsBookRegistered" value="n" v-model="searchCondition.sRadioSchIsBookRegistered"/>회계미반영
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>발주완료일</th>
                            <td>
                                <div class="dp-flex">
                                    <div class="mini-picker mgl5">
                                        <date-picker v-model="searchCondition.sTextboxRangeStartSchCpProductionOrder" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="font-weight: normal"></date-picker>
                                    </div>
                                    <div>~</div>
                                    <div class="mini-picker">
                                        <date-picker v-model="searchCondition.sTextboxRangeEndSchCpProductionOrder" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="font-weight: normal;"></date-picker>
                                    </div>

                                    <div class="form-inline" >
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchCpProductionOrder', 'sTextboxRangeEndSchCpProductionOrder', 'today')">오늘</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchCpProductionOrder', 'sTextboxRangeEndSchCpProductionOrder', 'week')">이번주</div>
                                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchCpProductionOrder', 'sTextboxRangeEndSchCpProductionOrder', 'month')">이번달</div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                프로젝트상태
                            </th>
                            <td >
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline " >
                                            <input type="checkbox" name="aChkboxSchProjectStatus[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchProjectStatus[]"
                                                   :checked="0 >= searchCondition.aChkboxSchProjectStatus.length?'checked':''" @click="searchCondition.aChkboxSchProjectStatus=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::ACCOUNTING_STATUS as $k => $v){ ?>
                                            <label class="mgl10" >
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchProjectStatus[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchProjectStatus"  >
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
                        <span class="mgl5 dp-flex">
                            <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('y', 'book')">
                                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                회계반영
                            </div>
                            <div class="btn btn-sm btn-white hover-btn" @click="setBookRegistered('n', 'book')">
                                <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i> 회계반영 취소
                            </div>
                        </span>
                    </div>
                </div>
                <div class="flo-right mgb5">
                    <div class="" style="display: flex; ">
                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(1)">정산 리스트</button>
                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="D,asc">프로젝트 등록일시 ▲</option>
                            <option value="D,desc">프로젝트 등록일시 ▼</option>
                            <option value="B,asc">회계 반영일 ▲</option>
                            <option value="B,desc">회계 반영일 ▼</option>
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
                        <col class="w-3p" />
                        <col class="w-10p" />
                        <col />
                        <col />
                        <col />
                        <col class="w-4p" /><!--회계반영-->
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
<!--                        <col />--> <!--스타일별 마진-->
                        <col class="w-20p" />
                    </colgroup>
                    <tr>
                        <th ><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' @click="toggleAllCheck()" v-model="listAllCheck"  /></th>
                        <th >번호</th>
                        <th>프로젝트</th>
                        <th>총 생산가</th>
                        <th>총 판매가</th>
                        <th>총 마진</th>
                        <th>회계반영</th>
                        <th>스타일명/부가항목</th>
                        <th>제작수량</th>
                        <th>미청구수량</th>
                        <th>생산단가</th>
                        <th>판매단가</th>
                        <th>생산가</th>
                        <th>판매가</th>
<!--                        <th>마진</th>--> <!--스타일별 마진-->
                        <th>회계 전달메시지</th>
                    </tr>
                    <tr  v-if="0 >= listData.length">
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    <tbody v-for="(val , key) in listData">
                        <tr v-for="(val2 , key2) in val.prdName">
                            <td v-if="key2 === 0" :rowspan="val.prdName.length">
                                <input type="checkbox" name="sno[]" :value="val.sno" class="list-check" v-model="projectCheckList">
                            </td>
                            <td v-if="key2 === 0" :rowspan="val.prdName.length">{% (listTotal.idx-key) %}</td>
                            <td v-if="key2 === 0" :rowspan="val.prdName.length" class="ta-l pdl5">
                                <span @click="window.open(`ims_view2.php?sno=${val.sno}`)">
                                    <span class="text-danger cursor-pointer hover-btn">{% val.sno %}</span>
                                    <span class=" cursor-pointer hover-btn" >{% val.customerName %}</span>
                                </span>
                            </td>
                            <td v-if="key2 === 0" :rowspan="val.prdName.length"  class="ta-r">
                                <span class="sl-blue">{% $.setNumberFormat(val.totalOriginAmt) %}</span>
                            </td>
                            <td v-if="key2 === 0" :rowspan="val.prdName.length"  class="ta-r">
                                <span class="text-danger">{% $.setNumberFormat(val.totalAmt) %}</span>
                            </td>
                            <td v-if="key2 === 0" :rowspan="val.prdName.length">{% val.totalMargin %}</td>
                            <td v-if="key2 === 0" :rowspan="val.prdName.length"  >
                                <!--회계반영-->
                                <span v-if="'y' === val.isBookRegistered" >
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
                                    <div>
                                        {% $.formatShortDate(val.isBookRegisteredDt) %}
                                    </div>
                                </span>
                                <span v-if="'n' === val.isBookRegistered">
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i>
                                </span>
                            </td>
                            <td class="ta-l pdl5 hover-btn cursor-pointer"> <!--스타일명/부가항목-->
                                <div v-if="val.styleSno[key2]!=0" @click="openProductReg2(val.sno, val.styleSno[key2], -1)">{% val2 %}</div>
                                <div v-else v-html="val2" @click="window.open(`ims_view2.php?sno=${val.sno}`)"></div>
                            </td>
                            <td class="ta-r">{% $.setNumberFormat(val.prdQty[key2]) %}</td>
                            <td class="ta-r">{% $.setNumberFormat(val.prdMsQty[key2]) %}</td>
                            <td class="ta-r">{% $.setNumberFormat(val.prdOriginAmt[key2]) %}</td>
                            <td class="ta-r">{% $.setNumberFormat(val.prdAmt[key2]) %}</td>
                            <td class="ta-r">{% $.setNumberFormat(val.prdOriginAmtMultiplyQty[key2]) %}</td>
                            <td class="ta-r">{% $.setNumberFormat(val.prdAmtMultiplyQty[key2]) %}</td>
<!--                            <td>{% val.prdMargin[key2] %}</td>--> <!--스타일별 마진-->
                            <td v-if="key2 === 0" :rowspan="val.prdName.length" class="ta-l pdl5" v-html="val.accountingMessage"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--list end-->
            <div id="account-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_account_list_script.php'?>

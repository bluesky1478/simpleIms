<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; height:42px !important;" id="affix-menu">
    <section id="affix-show-type1">
        <h3 >발주 리스트 <span class="font-11 font-normal"></span></h3>
        <div class="btn-group">
            <input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" @click="location.href='./ims_project_reg.php?status=<?=$requestParam['status']?>';" />
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
                            <col class="w-50p">
                            <col class="w-6p">
                            <col class="w-30p">
                            <col class="w-8p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>
                                업무타입
                            </th>
                            <td>
                                <div class="checkbox ">
                                    <div>
                                        <label class="radio-inline ">
                                            <input type="radio" name="bidType2" value="all" v-model="searchCondition.bidType2" @change="refreshList(1)" />전체
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="bidType2" value="bid" v-model="searchCondition.bidType2"  @change="refreshList(1)" />단가
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="bidType2" value="costBid" v-model="searchCondition.bidType2"  @change="refreshList(1)" />디자인(신규)
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="bidType2" value="single" v-model="searchCondition.bidType2"  @change="refreshList(1)" />디자인(개선)
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="bidType2" value="single" v-model="searchCondition.bidType2"  @change="refreshList(1)" />리오더(개선)
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <th rowspan="3" class="text-center">
                                검색어
                            </th>
                            <td rowspan="3">
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

                            <td rowspan="3" class="">
                                <div class="btn btn-lg btn-black w-100p" style="height:55px;padding-top:20px" @click="refreshList(1)">검색</div>
                                <div class="btn btn-white mgt5 w-100p" @click="conditionReset()">초기화</div>
                            </td>

                        </tr>
                        <tr>
                            <th>
                                진행상태
                            </th>
                            <td >
                                <div class="checkbox ">
                                    <div >
                                        <label class="checkbox-inline mgr10">
                                            <input type="checkbox" name="salesStatus[]" value="all" class="js-not-checkall" data-target-name="salesStatus[]"
                                                   :checked="0 >= searchCondition.salesStatusChk.length?'checked':''" @click="searchCondition.salesStatusChk=[]"> 전체
                                        </label>
                                        <?php foreach( \Component\Ims\ImsCodeMap::DESIGN_STATUS as $k => $v){ ?>
                                            <label class="mgr10">
                                                <input class="checkbox-inline chk-progress" type="checkbox" name="salesStatus[]" value="<?=$k?>"  v-model="searchCondition.salesStatusChk"  @change="refreshList(1)"> <?=$v?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>지연/미확정</th>
                            <td>
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="delayStatus[]" value="all" class="js-not-checkall" data-target-name="delayStatus[]"
                                           :checked="0 >= searchCondition.delayStatus.length?'checked':''" @click="searchCondition.delayStatus=[];refreshList(1)"> 전체
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="1"
                                           v-model="searchCondition.delayStatus"  @change="refreshList(1)"> <span class="text-danger">일정 지연</span>
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="2"
                                           v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 생산가 미확정
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="3"
                                           v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 판매가 미확정
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="4"
                                           v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 아소트 미확정
                                </label>

                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="delayStatus[]" value="5"
                                           v-model="searchCondition.delayStatus"  @change="refreshList(1)"> 작지 미결재
                                </label>

                            </td>
                        </tr>
                        
                        <!--
                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchProject()">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="projectConditionReset()">
                            </td>
                        </tr>
                        -->
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
            </div>

            <div >
                <div class="dp-flex" style="justify-content: space-between">
                    <div class="mgb5 mgt25">
                        <div class="dp-flex dp-flex-gap10 font-16">

                            <div class="total hover-btn cursor-pointer" @click="searchCondition.projectStatus='';refreshList(1)">총 <span class="text-danger">{% $.setNumberFormat(listTotal.typeAllCnt) %}</span> 건</div>

                            <!--FIXME : 총계 리스트 검색시 제안확정대기 같은거 묶어야 한다. ( 아이디어 : 특정 번호 입력시 전환 시키기 , 검색 명 바꾸기 projectStatus 가 아니게 ) -->
                            <div :class="'hover-btn cursor-pointer font-14 ' + (20 === searchCondition.projectStatus ? 'total-active':'')"  @click="searchCondition.projectStatus=20;refreshList(1)">
                                기획(<span class="text-danger">{% $.setNumberFormat(listTotal.type1Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer  font-14 ' + (30 === searchCondition.projectStatus ? 'total-active':'')" @click="searchCondition.projectStatus=30;refreshList(1)">
                                제안(<span class="text-danger">{% $.setNumberFormat(listTotal.type2Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer font-14 ' + (40 === searchCondition.projectStatus ? 'total-active':'')" @click="searchCondition.projectStatus=40;refreshList(1)">
                                샘플(<span class="text-danger">{% $.setNumberFormat(listTotal.type3Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer font-14 ' + (50 === searchCondition.projectStatus ? 'total-active':'')" @click="searchCondition.projectStatus=50;refreshList(1)">
                                발주대기(<span class="text-danger">{% $.setNumberFormat(listTotal.type4Cnt) %}</span>)
                            </div>

                            <div :class="'hover-btn cursor-pointer font-14 ' + (60 === searchCondition.projectStatus ? 'total-active':'')" @click="searchCondition.projectStatus=60;refreshList(1)">
                                발주작업(<span class="text-danger">{% $.setNumberFormat(listTotal.type5Cnt) %}</span>)
                            </div>

                        </div>

                    </div>
                    <div class="mgb5">
                        <div class="" style="display: flex;padding-top:20px">

                            <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort" v-show="false">
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
                    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                        <colgroup>
                            <col class="w-1p" />
                            <col class="w-3p" />
                            <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                        </colgroup>

                        <tr>
                            <th ><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
                            <th >번호</th>
                            <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip && 'subTitle' != fieldData.name" >
                                {% fieldData.title %}
                            </th>
                        </tr>
                        <!--<tr>
                            <th v-for="fieldData in searchData.fieldData"  :colspan="$.isEmpty(fieldData.colspan)?'1':fieldData.colspan"
                                v-if="true != fieldData.skip && true !== fieldData.subRow && true != fieldData.rowspan " >{% fieldData.title %}</th>
                        </tr>-->
                        <tr  v-for="(each , index) in listData">
                            <td :rowspan="each.projectRowspan"> <!---->
                                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                            </td>
                            <td :rowspan="each.projectRowspan"> <!--:rowspan="each.projectRowspan"-->
                                <div>{% (listTotal.idx-index) %}</div>
                                <div>
                                    <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                        <span class="text-muted cursor-pointer hover-btn mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                                        </span>
                                    <?php } ?>
                                </div>
                            </td>
                            <!--예정일 :rowspan="fieldData.rowspan?each.projectRowspan:1"    -->
                            <td v-for="fieldData in searchData.fieldData"
                                :rowspan="fieldData.rowspan?each.projectRowspan:1"
                                v-if="true != fieldData.rowspan || ( true == fieldData.rowspan && each.projectRowspan > 0 )"
                                :class="fieldData.class"  >
                                {% each.projectRowspan %} // {% fieldData.rowspan %} // {% each.sno %}
                                <?php include 'nlist/list_template.php'?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="design-page" v-html="pageHtml" class="ta-c"></div>

            </div>

        </div>
        <!--처리완료 팝업-->
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'nlist/list_common_script.php'?>
<?php include 'nlist/list_qc_script.php'?>

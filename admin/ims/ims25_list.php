<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<style>
    .mx-datepicker { width:100px!important; }
</style>

<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<!--타이틀-->
<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;margin-bottom: 0!important; " id="affix-menu">
    <section id="affix-show-type1">
        <h3 class="relative">
            프로젝트 리스트
            <!--<div class="btn btn-sm btn-gray font-12 mgl10 hover-btn" style="padding:4px 10px 2px 10px!important; height:30px ;background-color:#5b5b5b">전체</div>-->
        </h3>
        <div class="btn-group" style="margin-top:-50px" >
            <!--<input type="button" value="프로젝트 등록" class="btn btn-red btn-reg hover-btn" id="btn-reg-project" />-->
        </div>
    </section>
    <!--틀고정-->
    <section id="affix-show-type2" style="margin:0 !important; display: none "></section>
</div>

<section id="imsApp" class="project-view">
    <!--검색-->
    <div class="" >
        <?php include 'ims25/ims25_list_search.php'?>
    </div>

    <div>
        <div class="dp-flex" style="justify-content: space-between">

            <div class="mgb5 mgt25 font-15" >
                검색 <span class="text-danger ">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
            </div>

            <div class="mgb5">
                <div class="dp-flex pdt20" style="display: flex;padding-top:20px">
                    <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload('')">다운로드</button>
                    <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort" >
                        <option value="P3,asc">고객납기일 ▲</option>
                        <option value="P3,desc">고객납기일 ▼</option>

                        <option value="P7,asc">발주D/L ▲</option>
                        <option value="P7,desc">발주D/L ▼</option>

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

        <div class="" >
            <!--리스트-->
            <table class="table table-rows table-default-center mgb0" v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                <colgroup>
                    <col class="w-1p" />
                    <col class="w-3p" />
                    <col :class="`w-${fieldData.col}p`" v-for="fieldData in getListField()" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                </colgroup>
                <tr>
                    <th rowspan="2"><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
                    <th rowspan="2" >번호</th>

                    <th v-for="fieldData in getListField()" v-if="true != fieldData.subRow"  >
                        <div v-if="'salesInfo' === fieldData.name">
                            {% fieldData.title %}
                            <span class="ims-tt" >
                                <i class="fa fa-question-circle" aria-hidden="true"></i>
                                <span class="ims-tt-box">
                                    <ul class="ta-l">
                                        <li><!--(0)-->추정 : 추정한 매출액 표기(초기예상)</li>
                                        <li><!--(1)-->타겟 : 타겟 판매가/생산가 입력 상태</li>
                                        <li><!--(2)-->견적 : 판매/생산가 결재 전 상태 </li>
                                        <li><!--(3)-->확정 : 판매/생산가 결재 완료 확정 상태 </li>
                                    </ul>
                                </span>
                            </span>
                        </div>
                        <div v-else>
                            {% fieldData.title %}
                        </div>
                    </th>
                </tr>
                <tbody v-if="$.isEmpty(listData) || false === listData || 0 >= listData.length">
                <tr>
                    <td colspan="99">
                        데이터가 없습니다.
                    </td>
                </tr>
                </tbody>
                <tbody v-for="(each , index) in listData" class="hover-light">
                <!--예정일-->
                <tr >
                    <td :rowspan="2">
                        <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                    </td>
                    <td :rowspan="2">
                        <div>{% (listTotal.idx-index) %}</div>
                        <div>
                            <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                <span class="text-muted cursor-pointer hover-btn font-10 mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                                </span>
                            <?php } ?>
                        </div>
                    </td>

                    <td v-for="fieldData in getListField()"
                        :rowspan="true == fieldData.rowspan || !$.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) || 9 == each['st'+$.ucfirst(fieldData.name)]?2:1"
                        v-if="true !== fieldData.subRow"
                        :class="fieldData.class + ' relative '" :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''">

                        <div v-if="'schedule' === fieldData.type" class="pd0">
                            <!--스케쥴-->
                            <?php include 'ims25/template/_ims25_schedule_template.php'?>
                        </div>
                        <div v-else-if="'c' === fieldData.type" class="pd0">
                            <!--커스텀-->
                            <?php include 'ims25/template/_ims25_custom_template.php'?>
                        </div>
                        <div v-else>
                            <!--그 외 타입들-->
                            <?php include 'ims25/template/_ims25_list_template.php'?>
                        </div>

                    </td>
                </tr>
                <!--완료일 FieldType = Schedule , SubRow  -->
                <tr >
                    <td v-for="fieldData in getListField()" :class="fieldData.class"
                        v-if="true == fieldData.subRow
                                && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)])
                                && 9 != each['st'+$.ucfirst(fieldData.name)]">

                        <div v-if="'schedule' === fieldData.type" class="pd0">
                            <!--스케쥴-->
                            <?php include 'ims25/template/_ims25_schedule_template.php'?>
                        </div>
                        <div v-else-if="'c' === fieldData.type" class="pd0">
                            <!--커스텀-->
                            <?php include 'ims25/template/_ims25_custom_template.php'?>
                        </div>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div :id="mainListPrefix + '-page'" v-html="pageHtml" class="ta-c mgt20"></div>

    <!--스타일 레이어 팝업-->
    <div>
        <?php include 'ims25/ims25_list_style.php'?>
    </div>

    <?php include 'nlist/_emergency_layer_popup.php'?>

</section>


<?php include 'ims25/_list_common_script.php'?>
<?php include 'ims25/ims25_list_script.php'?>

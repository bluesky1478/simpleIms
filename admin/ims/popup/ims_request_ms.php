<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %} </span> 프로젝트
                <span class="text-danger">- {% project.projectNo %}</span>
                <a :href="'../ims_project_view.php?sno=' + project.sno" target="_blank">
                    <span class="btn btn-white" style="padding-top:7px">프로젝트보기</span>
                </a>
            </h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            <input type="button" value="저장" class="btn btn-red btn-register" @click="save(prepared)" v-if="0 == prepared.preparedStatus" style="margin-right:75px;">
        </div>
    </form>

    <?php include './admin/ims/popup/_ims_request_style.php'?>

    <div class="row">
        <div class="col-xs-12">
            <div class="table-title gd-help-manual">
                <div class="flo-left"><?=$title?> 요청정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <!--preparedType-->
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <?php if( 'work' !== $requestParam['reqType'] ) { ?>
                    <tr>
                        <th class="required">요청 생산처/상태</th>
                        <td>
                            <select2 class="js-example-basic-single" style="width:100%" v-model="prepared.produceCompanySno" v-if="0 == prepared.preparedStatus" id="produceCompanySno">
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                            <span v-if="0 != prepared.preparedStatus" class="font-16">
                                {% prepared.produceCompany %}
                                / <span v-html="prepared.preparedStatusKr"></span>

                                <div v-if=" 2 == prepared.preparedStatus ">
                                    <div class="btn btn-accept hover-btn" @click="setPreparedStatus(prepared, 4)">승인</div>
                                    <div class="btn btn-reject hover-btn" @click="setPreparedStatus(prepared, 5)">반려</div>
                                </div>

                                <div v-if="!$.isEmpty(prepared.acceptMemo)" class="font-13">
                                    <span v-html="prepared.preparedStatusKr"></span> 사유/메모 : {% prepared.acceptMemo %}
                                </div>

                                <?php if($isAuth) { ?>
                                <div class="btn btn-reject hover-btn" @click="setPreparedStatus(prepared, -2)" v-if=" 4 == prepared.preparedStatus || 5 == prepared.preparedStatus  ">
                                    승인/반려 번복
                                </div>

                                <div class="btn btn-reject hover-btn mgt5" @click="setPreparedStatus(prepared, -1)" v-if="2 == prepared.preparedStatus">
                                    처리완료 번복
                                </div>
                                <span class="notice-info" v-if="2 == prepared.preparedStatus">생산처에서 다시 수정할 수 있게 합니다.</span>
                                <?php } ?>
                            </span>
                        </td>
                        <?php }else{ ?>

                            <th class="required">요청 상태</th>
                            <td>
                                <span class="font-16">
                                    <span v-html="prepared.preparedStatusKr"></span>

                                    <div v-if=" 2 == prepared.preparedStatus ">
                                        <div class="btn btn-accept hover-btn" @click="setPreparedStatus(prepared, 4)">승인</div>
                                        <div class="btn btn-reject hover-btn" @click="setPreparedStatus(prepared, 5)">반려</div>
                                    </div>

                                    <div v-if="!$.isEmpty(prepared.acceptMemo)" class="font-13">
                                        <span v-html="prepared.preparedStatusKr"></span> 사유/메모 : {% prepared.acceptMemo %}
                                    </div>
                                    <?php if($isAuth) { ?>
                                        <div class="btn btn-reject hover-btn" @click="setPreparedStatus(prepared, -2)" v-if=" 4 == prepared.preparedStatus || 5 == prepared.preparedStatus  ">
                                        승인/반려 번복
                                        </div>

                                        <div class="btn btn-reject hover-btn mgt5" @click="setPreparedStatus(prepared, -1)" v-if="2 == prepared.preparedStatus">
                                        처리완료 번복
                                        </div>
                                        <span class="notice-info" v-if="2 == prepared.preparedStatus">생산처에서 다시 수정할 수 있게 합니다.</span>
                                    <?php } ?>
                                </span>
                            </td>

                        <?php } ?>
                        <th >완료D/L<br>(완료요청일자)</th>
                        <td>
                            <date-picker value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" v-model="prepared.deadLineDt" placeholder="완료D/L" id="deadLineDt"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th>이노버 요청 메모</th>
                        <td colspan="3">
                            <textarea class="form-control" rows="3" v-model="prepared.reqMemo" ></textarea>
                        </td>
                    </tr>

                    <?php include $includeContents?>

                    <?php if( 'work' !== $requestParam['reqType'] ) { ?>
                    <tr v-show="!$.isEmpty(prepared.sno)">
                        <th>생산처 메모</th>
                        <td colspan="3">
                            <span v-html="prepared.procMemoBr"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div style="clear: both"></div>

    <hr>

    <!--const STATUS = [
    0 => '요청',
    1 => '처리중',
    2 => '처리완료',
    3 => '처리불가',
    4 => '승인',   //승인 -> 승인
    5 => '반려', //반려,번복 -> 다시해.
    ];-->

    <div class="text-center">
        <div class="btn btn-red btn-lg" @click="save(prepared)" v-if="0 == prepared.preparedStatus">저장</div>
        <div class="btn btn-white btn-lg" @click="self.close()">닫기</div>
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include '_ims_request_script.php'?>
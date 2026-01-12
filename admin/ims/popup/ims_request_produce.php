<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header ">
            <h3>
                <span class="text-blue">{% items.customerName %}</span> 프로젝트
                <span class="text-danger">- {% project.projectNo %}</span>
            </h3>
            <div class="btn-group" >
                <input type="button" value="요청확인함" class="btn btn-red hover-btn btn-lg ims-btn-confirm" v-show="0 == prepared.preparedStatus" @click="setStatus(prepared.sno,1)">
                <input type="button" value="처리완료" class="btn btn-red hover-btn btn-lg ims-btn-confirm" v-show="1 == prepared.preparedStatus" @click="setStatus(prepared.sno,2)" style="width:125px;background-color: #1E2C89; border-color:#1E2C89">
                <input type="button" value="저장" class="btn btn-red btn-register" @click="save(prepared)" v-if="0 == prepared.preparedStatus || 1 == prepared.preparedStatus" >
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <?php include './admin/ims/popup/_ims_request_style.php'?>

    <div class="row">
        <div class="col-xs-12 ">
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
                    <tr>
                        <th >
                            상태
                        </th>
                        <td class="font-16" >
                            <span v-html="prepared.preparedStatusKr"></span>
                            <div v-if="!$.isEmpty(prepared.acceptMemo)" class="font-13 mgt5">
                                <span v-html="prepared.preparedStatusKr"></span> 사유/메모(이노버) : {% prepared.acceptMemo %}
                            </div>
                        </td>
                        <th >완료D/L<br>(완료요청일자)</th>
                        <td class="font-16" >
                            {% prepared.deadLineDtShort %}
                            (<span v-html="prepared.deadLineDtRemain"></span>)
                        </td>
                    </tr>
                    <tr>
                        <th>이노버 메모</th>
                        <td colspan="3">
                            <span v-html="prepared.reqMemoBr"></span>
                        </td>
                    </tr>

                    <?php include $includeContents?>

                    <tr>
                        <th>생산처 메모</th>
                        <td colspan="3">
                            <textarea class="form-control" rows="3" v-model="prepared.procMemo" placeholder="생산처 메모"></textarea>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <hr>

    <div class="text-center">
        <input type="button" value="저장" class="btn btn-red btn-lg" @click="save(prepared)" v-if="0 == prepared.preparedStatus || 1 == prepared.preparedStatus" />
        <input type="button" value="닫기" class="btn btn-white btn-lg" @click="self.close()" />
    </div>
    <div class="text-center mgt10">
        <div class="btn btn-red hover-btn btn-lg"  v-show="0 == prepared.preparedStatus" @click="setStatus(prepared.sno,1)" style="width:125px; background-color: #1E2C89; border-color:#1E2C89">요청확인함</div>
        <div class="btn btn-red hover-btn btn-lg" v-show="1 == prepared.preparedStatus" @click="setStatus(prepared.sno,2)" style="width:125px;background-color: #1E2C89; border-color:#1E2C89">처리완료</div>
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include '_ims_request_script.php'?>

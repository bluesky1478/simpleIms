<div class="row">

    <div class="col-xs-12 new-style" v-if="15 === Number(project.projectStatus)">
        <div class="row mgt20">
            <div class="col-xs-6">

                <div class="mgb3">
                    <div class="table-title flo-left"></div>
                    <div class="flo-right" style="padding-top:5px;padding-bottom:3px;">
                        <div class="btn btn-red hover-btn cursor-pointer" @click="saveDesignData(project)">
                            저장
                        </div>
                    </div>
                </div>

                <table class="table table-cols w100 table-default-center table-pd-5 ">
                    <colgroup>
                        <col class="width-md"/>
                        <col class="width-md"/>
                        <col class="width-md"/>
                        <col class="width-md"/>
                    </colgroup>
                    <tr>
                        <th>사업계획 포함</th>
                        <td colspan="99" class="text-left">
                            <div class="">
                                <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['includeType']">
                                    <input type="radio" :name="'bizPlanYn'"  :value="eachKey" v-model="project.bizPlanYn"  />
                                    <span class="font-12">{%eachValue%}</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>협상 단계 내용</th>
                        <td colspan="99" class="pd0 text-left">
                            <!--
                            <textarea class="form-control" rows="5" placeholder="협상단계 내용" v-model="project.workMemo"></textarea>
                            -->
                            <div v-if="meetingList.length > 0" v-html="meetingList[0].contents"></div>

                            <div class="mgt10">
                                <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'meeting')" >협상 정보 등록</button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php include 'basic_type_style.php'?>

    </div>

</div>


<!--  TO-DO LIST 영역 -->
<div class="row mgt20"></div>
<?php include './admin/ims/template/view/_infoTodo.php'?>
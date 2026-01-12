
<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vuejs-datepicker/1.6.2/vuejs-datepicker.min.js"></script>-->
<script src="https://unpkg.com/vuejs-datepicker"></script>

<form id="document-form">
    <div class="page-header js-affix">
        <h3>
            미팅보고서 등록
        </h3>
        <div class="btn-group">
            <input type="button" value="등록하기" class="btn btn-red js-register"/>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 ">
            <div class="table-title gd-help-manual">
                <div class="flo-left">기본정보</div>
                <div class="flo-right">
                </div>
            </div>
            <div class="">
                <table class="table table-cols  w100">
                    <colgroup>
                        <col style="width:10%" />
                        <col style="width:23%" />
                        <col style="width:10%" />
                        <col style="width:23%" />
                        <col style="width:10%" />
                        <col style="width:23%" />
                    </colgroup>
                    <tr>
                        <th class="center">업체</th>
                        <td ><input type="text" class="form-control" v-model="docData.업체" :value="docData.업체"></td>
                        <th class="center">참석자</th>
                        <td ><input type="text" class="form-control" v-model="docData.참석자" :value="docData.참석자"></td>
                        <th class="center">미팅일자</th>
                        <td >
                            <vuejs-datepicker>
                                <datepicker v-model="docData.미팅일자" :value="docData.미팅일자" :format="pickerFormatter"></datepicker>
                            </vuejs-datepicker>
                        </td>
                    </tr>
                    <tr>
                        <th class="center">구매형태</th>
                        <td >
                            <?= gd_select_box('', '', $projectCodeMap['구매형태'], null, '', null, 'v-model="docData.구매형태" :value="docData.구매형태"' , 'form-control'); ?>
                        </td>
                        <th class="center">경쟁업체</th>
                        <td >
                            <?= gd_select_box('', '', $projectCodeMap['경쟁업체'], null, '', null, 'v-model="docData.경쟁업체" :value="docData.경쟁업체"' , 'form-control'); ?>
                        </td>
                        <th class="center">업체선정요소</th>
                        <td >
                            <?= gd_select_box('', '', $projectCodeMap['업체선정요소'], null, '', null, 'v-model="docData.업체선정요소" :value="docData.업체선정요소"' , 'form-control'); ?>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="col-xs-12 ">
            <div class="table-title gd-help-manual">
                <div class="flo-left">유니폼정보</div>
                <div class="flo-right">
                </div>
            </div>
            <div class="">
                <table class="table table-rows table-rows-soft  w100">
                    <colgroup>
                    </colgroup>
                    <tr>
                        <th class="center">품목</th>
                        <th class="center">예상수량</th>
                        <th class="center">현재단가</th>
                        <th class="center">타겟단가</th>
                        <th class="center">진행형태</th>
                        <th class="center">예상발주</th>
                        <th class="center">희망납기</th>
                        <th class="center">불편사항</th>
                    </tr>
                    <tr v-for="(uniformData, uniformIndex) in docData.유니폼정보" :key="uniformIndex"  >
                        <td ><input type="text" class="form-control" v-model="uniformData.품목" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.예상수량" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.현재단가" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.타겟단가" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.진행형태" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.예상발주" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.희망납기" ></td>
                        <td ><input type="text" class="form-control" v-model="uniformData.불편사항" ></td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</form>

<?php include 'project_doc_reg_script.php' ?>



<?php if(empty($requestParam['simple'])) { ?>
<div class="row">
    <!-- 프로젝트 기본 정보 -->
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">프로젝트 기본 정보</div>
            <div class="flo-right"></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody>
                <tr>
                    <th >고객사 선택</th>
                    <td>
                        <select2 class="js-example-basic-single" v-model="project.customerSno" @change="setCustomer(project.customerSno)" style="width:100%" >
                            <option value="-1">신규등록</option>
                            <?php foreach ($customerListMap as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                    </td>
                    <th v-show="$.isEmpty(project.sno)">상태</th>
                    <td v-show="$.isEmpty(project.sno)">
                        <select2 v-model="project.projectStatus" style="width:200px;" >
                            <?php foreach ($projectListMap as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr>
                    <th >고객 제안마감일</th>
                    <td>
                        <date-picker v-model="project.recommendDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                    </td>
                    <th >입찰</th>
                    <td >
                        <input type="text" class="form-control" placeholder="입찰" v-model="project.bid" >
                    </td>
                </tr>
                <tr>
                    <th >업무 시작일</th>
                    <td>
                        <date-picker v-model="project.salesStartDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false"></date-picker>
                    </td>
                    <th >납기일자</th>
                    <td>
                        <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                    </td>
                </tr>
                <tr>
                    <th >
                        프로젝트 타입
                    </th>
                    <td colspan="3">
                        <?php foreach ( $projectTypeMap as $key => $value ) { ?>
                            <label class="radio-inline">
                                <input type="radio" name="projectType" value="<?=$key?>"  v-model="project.projectType" /><?=$value?>
                            </label>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th >제안형태</th>
                    <td colspan="3">
                        <label class="checkbox-inline">
                            <input type="checkbox" value="1" v-model="project.recommend">
                            기획서<span class="ims-recommend ims-recommend1">기</span>
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="2" v-model="project.recommend">
                            제안서<span class="ims-recommend ims-recommend2">제</span>
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="4" v-model="project.recommend">
                            샘플<span class="ims-recommend ims-recommend4">샘</span>
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="8" v-model="project.recommend">
                            견적<span class="ims-recommend ims-recommend8">견</span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 디자인실/QC 정보 -->
    <div class="col-xs-6" >
        <div class="table-title gd-help-manual">
            <div class="flo-left">디자인실 정보</div>
            <div class="flo-right"></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody>
                <tr>
                    <th >디자인 담당자</th>
                    <td>
                        <select2 class="js-example-basic-single" v-model="project.designManagerSno"  style="width:100%" >
                            <?php foreach ($designManagerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                    </td>
                    <th >
                        디자인 마감일
                    </th>
                    <td>
                        <date-picker v-model="project.designEndDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                    </td>
                </tr>
                <?php foreach($designField as $idx => $field) { ?>
                    <?php if(0 === ($idx % 2)) { ?>
                        <tr>
                    <?php } ?>
                    <th ><?=$field['title']?>   </th>
                    <td>
                        <date-picker v-model="project.<?=$field['name']?>" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=$field['title']?> " ></date-picker>
                    </td>
                    <?php if( ($idx % 2) >  0 ) { ?>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <!--
                <tr>
                    <th >QC마감일</th>
                    <td colspan="3">
                        <date-picker v-model="project.prdEndDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                    </td>
                </tr>
                -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <?php include '_template_customer.php'?>
</div>

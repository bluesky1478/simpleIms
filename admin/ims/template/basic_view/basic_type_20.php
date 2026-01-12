
<!-- 스케쥴 영역 -->
<?php include 'basic_type_schedule.php'?>

<!-- 스타일 영역 -->
<?php include 'basic_type_style.php'?>

<!-- 파일 영역 -->
<?php include 'basic_type_file.php'?>

<!-- 단계별 정보 영역 시작 -->
<div class="row ">
    <?php $title = '고객사 근무 환경'?>
    <?php include 'basic_type_head.php'?>
        <tbody>
            <tr >
                <th >고객사 근무 환경</th>
                <td colspan="3">
                    <div v-show="isModify">
                        <input type="text" class="form-control" v-model="project.addedInfo.info001">
                    </div>
                    <div v-show="!isModify">
                        {% project.addedInfo.info001 %}
                    </div>
                </td>
            </tr>
            <tr >
                <th >착용자 연령/성별</th>
                <td colspan="3">
                    <div v-show="isModify">
                        <input type="text" class="form-control" v-model="project.addedInfo.info002">
                    </div>
                    <div v-show="!isModify">
                        {% project.addedInfo.info002 %}
                    </div>
                </td>
            </tr>
        </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->

    <?php $title = '고객사 샘플 정보'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >샘플 확보</th>
        <td colspan="3">
            <?php $model = 'project.addedInfo.info003'; $listCode = 'ableType'?>
            <?php include '_radio.php'?>
        </td>
    </tr>
    <tr >
        <th >샘플 반납 유무</th>
        <td colspan="3">
            <?php $model = 'project.addedInfo.info004'; $listCode = 'existType'?>
            <?php include '_radio.php'?>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>
</div>

<!-- ================================================================================================ -->

<div class="row">
    <?php $title = '기타사항'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >발주 물량 변동</th>
        <td colspan="3">
            <?php $key1 = 'info005'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    <tr >
        <th >계약기간</th>
        <td colspan="3">
            <?php $key1 = 'info006'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    <tr >
        <th >선호컨셉</th>
        <td colspan="3">
            <?php $key1 = 'info007'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    <tr >
        <th >선호컬러</th>
        <td colspan="3">
            <?php $key1 = 'info008'?>
            <div v-show="isModify">
                <input type="text" class="form-control" v-model="project.addedInfo.<?=$key1?>">
            </div>
            <div v-show="!isModify">
                {% project.addedInfo.<?=$key1?> %}
            </div>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->

    <!--고객성향-->
    <?php include 'basic_type_tendency.php'?>

</div>
<!-- 단계별 정보 영역 끝 -->


<!--  TO-DO LIST 영역 -->
<div class="row mgt20"></div>
<?php include './admin/ims/template/view/_infoTodo.php'?>


<!-- 기획서 보기 영역 -->
<div class="row mgt20" v-if="[20].indexOf(Number(project.projectStatus)) !== -1">
    <div class="col-xs-12">
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                기획서 보기
            </div>
            <div class="flo-right">
            </div>
        </div>
        <table class="table table-cols  xsmall-picker">
            <colgroup>
                <col class="width-sm">
                <col class="width-md">
                <col class="width-sm">
                <col class="width-md">
            </colgroup>
            <tbody>
            <tr >
                <td colspan="99" class="text-center">
                    <img :src="'<?=$nasUrl?>'+file.filePath" v-for="file in fileList.filePlan.files" class="w-100p">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>



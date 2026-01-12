
<!-- 스케쥴 영역 -->
<?php include 'basic_type_schedule.php'?>

<!-- 스타일 영역 -->
<?php include 'basic_type_style.php'?>

<!-- 파일 영역 -->
<?php include 'basic_type_file.php'?>

<!-- 단계별 정보 영역 시작 -->
<div class="row ">
    <?php $title = '샘플 제작 정보'?>
    <?php include 'basic_type_head.php'?>
    <tbody>
    <tr >
        <th >샘플 제작비</th>
        <td colspan="3">
            <div v-show="!isModify">
                {% getCodeMap()['existType2'][project.addedInfo.info016] %}
                <span v-show="'y' === project.addedInfo.info016">{%$.setNumberFormat(project.addedInfo.info017)%}원</span>
            </div>

            <div v-show="isModify">
                <div class="">
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['existType2']">
                        <input type="radio" :name="'project-added-info-info016'"  :value="eachKey" v-model="project.addedInfo.info016"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
                <div class="mgt5">
                    <input type="number" class="form-control" v-model="project.addedInfo.info017" v-show="'y' === project.addedInfo.info016" placeholder="유상 비용 (숫자만)">
                </div>
            </div>
        </td>
    </tr>
    <tr >
        <th >샘플 결제 방법</th>
        <td colspan="3">
            <div v-show="!isModify">
                {% getCodeMap()['paymentType'][project.addedInfo.info018] %}
            </div>
            <div v-show="isModify">
                <div class="" >
                    <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['paymentType']">
                        <input type="radio" :name="'project-added-info-info018'"  :value="eachKey" v-model="project.addedInfo.info018"  />
                        <span class="font-12">{%eachValue%}</span>
                    </label>
                </div>
            </div>
        </td>
    </tr>
    <tr >
        <th >샘플 제출 일시</th>
        <td colspan="3">
            <div v-show="!isModify">
                {% $.formatShortDate(project.addedInfo.info019) %}
                {% project.addedInfo.info020 %}
            </div>
            <!-- 텍스트 / 텍스트 -->
            <div v-show="isModify">
                <div class="dp-flex">
                    <date-picker v-model="project.addedInfo.info019" value-type="format" :editable="false" ></date-picker>
                    <div class="mgl10 dp-flex">
                        <span>시간 : </span>
                        <span class="mgl5">
                            <input type="text" class="form-control" v-model="project.addedInfo.info020" placeholder="샘플 제출 시간">
                        </span>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr >
        <th >샘플 제출 장소</th>
        <td colspan="3">
            <div v-show="!isModify">
                {% project.addedInfo.info021 %}
                {% project.addedInfo.info022 %}
            </div>
            <!-- 텍스트 / 텍스트 -->
            <div v-show="isModify">
                <div class="dp-flex">
                    <div class="w150p">장소 :</div>
                    <input type="text" class="form-control" v-model="project.addedInfo.info021" placeholder="장소">
                </div>
                <div class="dp-flex mgt5">
                    <div class="w150p">접수자 정보 :</div>
                    <input type="text" class="form-control" v-model="project.addedInfo.info022" placeholder="접수자 정보">
                </div>
            </div>
        </td>
    </tr>
    </tbody>
    <?php include 'basic_type_foot.php'?>

    <!-------------------------------------------------------------------------------------------->

    <!--고객성향-->
    <?php include 'basic_type_tendency.php'?>

</div>

<!-- ================================================================================================ -->

<!-- 단계별 정보 영역 끝 -->

<!--  TO-DO LIST 영역 -->
<div class="row mgt20"></div>
<?php include './admin/ims/template/view/_infoTodo.php'?>





<div class="col-xs-12 mgb15">
    <div class="mgb3">

        <div class="table-title flo-left relative">
            <div class="nanum" style="position: absolute; top:10px; left:0; width:300px">
                <i class="fa fa-play fa-title-icon font-13" aria-hidden="true" ></i>
                &nbsp; 기초정보
            </div>
        </div>
        <div class="flo-right" style="padding-top:5px;padding-bottom:3px;">
            <div class="btn btn-white hover-btn cursor-pointer" @click="showBasicInfo=false" v-show="showBasicInfo" >
                <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 기초정보 숨기기
            </div>
            <div class="btn btn-white hover-btn cursor-pointer" @click="showBasicInfo=true" v-show="!showBasicInfo">
                <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 기초정보 보기
            </div>
        </div>
    </div>

    <div v-show="!showBasicInfo" style="clear: both;border-bottom: solid 1px #9a9a9a" class="mgt3"></div>

    <div v-show="showBasicInfo" style="clear: both" class="mgt3">
        <table class="table table-cols mgb5 ">
            <colgroup>
                <col class="width-xs">
                <col class="width-md">
                <col class="width-xs">
                <col class="width-md">
                <col class="width-xs">
                <col class="width-md">
                <col class="width-xs">
                <col class="width-md">
            </colgroup>
            <tr>
                <th>고객명/프로젝트</th>
                <td>
                    {% items.customerName %}
                    <span class="text-danger">{% project.projectNo %}</span>
                </td>
                <th>발주D/L</th>
                <td >
                    <div v-show="isModify">
                        <date-picker v-model="project.customerOrderDeadLine" value-type="format" format="YYYY-MM-DD"  :editable="false" placeholder="발주D/L"></date-picker>
                    </div>
                    <div v-show="!isModify">
                        {% project.customerOrderDeadLine %}
                    </div>
                </td>
                <th>계약형태</th>
                <td>
                    <div v-show="!isModify">
                        {% project.bidType %}
                    </div>
                    <div v-show="isModify">
                        <select v-model="project.bidType" class="form-control  form-inline inline-block w-45p font-14 ">
                            <option value="">미정</option>
                            <option>입찰</option>
                            <option>단독진행</option>
                        </select>
                    </div>
                </td>
                <th>진행상태 <div class="btn btn-sm btn-white" @click="openProjectStatusHistory(project.sno,'')">상태변경이력</div></th>
                <td class="">
                    <select2 v-model="currentStatus" style="width:150px;" >
                        <?php foreach ($projectListMap as $key => $value ) { ?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                    </select2>
                    <div class="btn btn-red" @click="setStatus(project)">변경</div>
                </td>
            </tr>
            <tr v-show="!isModify">
                <th>연도/시즌</th>
                <td class="">
                    {% project.projectYear %}/{% project.projectSeason %}
                    {% project.projectTypeKr %}
                </td>
                <th>희망납기</th>
                <td class="">{% project.customerDeliveryDt %}</td>
                <th>영업담당자</th>
                <td class="">{% project.salesManagerNm %}</td>
                <th>디자인담당자</th>
                <td class="">{% project.designManagerNm %}</td>
            </tr>
            <tr v-show="isModify">
                <th>연도/시즌</th>
                <td class="">
                    <select v-model="project.projectYear" class="form-control form-inline inline-block font-18" style="height: 35px; width:70px;">
                        <?php foreach($yearList as $yearEach) {?>
                            <option><?=$yearEach?></option>
                        <?php }?>
                    </select>
                    <select v-model="project.projectSeason" class="form-control form-inline inline-block font-18" style="height: 35px; width:70px;">
                        <option >ALL</option>
                        <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                            <option><?=$seasonEn?></option>
                        <?php }?>
                    </select>
                </td>
                <th>희망납기</th>
                <td class="">
                    <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false" placeholder="고객 납기"></date-picker>
                    <div class="mgt5">
                        <div>
                            납기변경 :
                            <label class="radio-inline">
                                <input type="radio" name="basic_deliveryConfirm"  value="y" v-model="project.customerDeliveryDtConfirmed"/>가능
                            </label>
                            <label class="radio-inline" style="margin-left:27px">
                                <input type="radio" name="basic_deliveryConfirm"  value="n" v-model="project.customerDeliveryDtConfirmed"/>불가
                            </label>
                        </div>

                        <div class="mgt5">
                            납기확정 :
                            <label class="radio-inline">
                                <input type="radio" name="basic_customerDeliveryDtStatus2"  value="n" v-model="project.customerDeliveryDtStatus2"/>미확정
                            </label>
                            <label class="radio-inline " >
                                <input type="radio" name="basic_customerDeliveryDtStatus2"  value="y" v-model="project.customerDeliveryDtStatus2"/>확정
                            </label>
                        </div>

                        <div class="mgt5 dp-flex" >
                            확보상태 :
                            <select class="form-control mgl5" v-model="project.customerDeliveryDtStatus">
                                <?php foreach($customerDeliveryStatus as $cdsKey => $cdsValue) { ?>
                                    <option value="<?=$cdsKey?>"><?=$cdsValue?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </td>
                <th>영업담당자</th>
                <td class="">
                    <select2 class="js-example-basic-single" v-model="project.salesManagerSno"  style="width:100%" >
                        <option value="0">미정</option>
                        <?php foreach ($managerList as $key => $value ) { ?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                    </select2>
                </td>
                <th>디자인담당자</th>
                <td class="">
                    <select2 class="js-example-basic-single" v-model="project.designManagerSno"  style="width:100%" >
                        <option value="0">미정</option>
                        <?php foreach ($designManagerList as $key => $value ) { ?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                    </select2>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="col-xs-12 new-style" >
    <!--협상단계-->
    <section v-if="[15].indexOf(Number(project.projectStatus)) !== -1">
        <?php include 'basic_view/basic_type_15.php'?>
    </section>

    <!--진행준비-->
    <section v-if="[10].indexOf(Number(project.projectStatus)) !== -1">
        <?php include 'basic_view/basic_type_10.php'?>
    </section>
    
    <!--고객사미팅-->
    <section v-if="[16].indexOf(Number(project.projectStatus)) !== -1">
        <?php include 'basic_view/basic_type_16.php'?>
    </section>

    <!--기획/제안/제안확정-->
    <section v-if="[20,30,31].indexOf(Number(project.projectStatus)) !== -1">
        <?php include 'basic_view/basic_type_20.php'?>
    </section>
    
    <!--샘플/샘플확정대기-->
    <section v-if="[40,41].indexOf(Number(project.projectStatus)) !== -1">
        <?php include 'basic_view/basic_type_40.php'?>
    </section>
    
    <!--고객 발주 대기 이상 -->
    <section v-if="Number(project.projectStatus) >= 50 ">
        <?php include 'basic_view/basic_type_50.php'?>
    </section>
</div>

<?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
<div class="text-muted">
    <ul>
        <li>estimateStatus : {% project.estimateStatus %}</li>
        <li>costStatus : {% project.costStatus %}</li>
        <li>기획 : {% project.planConfirm %} / 제안 : {% project.proposalConfirm %}</li>
        <li>생산 : {% project.prdCostApproval %} / 판매 : {% project.prdPriceApproval %}</li>
        <li>사양서 : {% project.prdConfirmApproval %}</li>
    </ul>
</div>
<?php } ?>

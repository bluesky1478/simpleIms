<!--기획 -->
<div class="dp-flex">
    <!--10. 결재완료 = 최종완료-->
    <div v-if="10 == mainData.stPlan" class="text-green">
        <div class="dp-flex dp-flex-center">
            <div @click="openApprovalView(projectApprovalInfo['plan'].sno)" class="cursor-pointer hover-btn btn btn-white btn-sm">
                결재정보
            </div>
            <div class="btn btn-white btn-sm"
                 v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 "
                 @click="openUrl('plan'+mainData.sno, '<?=$nasUrl?>' + fileList.filePlan.files[0].filePath)">
                보기
            </div>
            <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:mainData.sno}, 'filePlan')">이력</div>
        </div>
    </div>
    <!-- 9. 결재PASS 완료 -->
    <div v-if="9 == mainData.stPlan" class="w-100p" >
        <div class="font-11 text-left " >
            사유:{% mainData.planMemo %}
        </div>
        <div class="btn btn-white btn-sm hover-btn cursor-pointer" @click="setApprovalReset('plan')" >
            PASS취소
        </div>
    </div>
    <!-- 2. 결재중일 때-->
    <div v-if="2 == mainData.stPlan">
        <approval-template2 :project="mainData" :approval="projectApprovalInfo" :confirm-type="'plan'" :confirm-field="'planConfirm'":memo-field="'planMemo'"></approval-template2>

        <div class="dp-flex">

            <?php if(true === $addBtn){ ?>
                <div class="btn btn-sm btn-blue btn-blue-line " @click="$('#filePlan').find('button').click()">파일재등록</div>
            <?php } ?>

            <div class="btn btn-white btn-sm"
                 v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 "
                 @click="openUrl('plan'+mainData.sno, '<?=$nasUrl?>' + fileList.filePlan.files[0].filePath)">
                보기
            </div>
            <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:mainData.sno}, 'filePlan')">이력</div>
        </div>
    </div>
    <!-- 1. 파일 등록 했을 때-->
    <div v-if="1 == mainData.stPlan">

        <div class="dp-flex">
            <?php if(true === $addBtn){ ?>
                <div class="btn btn-sm btn-blue btn-blue-line "  @click="$('#filePlan').find('button').click()">파일재등록</div>
            <?php } ?>

            <div class="btn btn-sm btn-red btn-red-line2"
                 v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 && (0 >= projectApprovalInfo.plan.sno || $.isEmpty(projectApprovalInfo.plan.sno ) )"
                 @click="openApprovalWrite(customer.sno, mainData.sno, 'plan')">결재요청</div>
            <div class="btn btn-white btn-sm"
                 v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 "
                 @click="openUrl('plan'+mainData.sno, '<?=$nasUrl?>' + fileList.filePlan.files[0].filePath)">
                보기
            </div>
            <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:mainData.sno}, 'filePlan')">이력</div>
        </div>

    </div>

    <!-- 0 . 대기-->
    <div v-if="0 == mainData.stPlan">
        <div class="btn btn-sm btn-blue btn-blue-line" @click="$('#filePlan').find('button').click()">파일등록</div>
        <!--완료가 아니고 파일이 없을 때는 PASS 버튼 활성화-->
        <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ){ ?>
        <div class="btn btn-white btn-sm" @click="setApprovalPass('plan')">PASS</div>
        <?php } ?>
    </div>
</div>


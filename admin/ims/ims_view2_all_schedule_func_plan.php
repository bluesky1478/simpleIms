<!--기획 -->
<td class="" style="border-top:none !important;" v-if="$.isEmpty(project['txPlan'])">
    <div class="dp-flex dp-flex-center" >
        <!--10. 결재완료 = 최종완료-->
        <div v-if="10 == project.stPlan" class="text-green">
            <div class="dp-flex dp-flex-center">
                <div @click="openApprovalView(projectApprovalInfo['plan'].sno)" class="cursor-pointer hover-btn btn btn-white btn-sm">
                    결재정보
                </div>
                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 "
                     @click="openUrl('plan'+project.sno, '<?=$nasUrl?>' + fileList.filePlan.files[0].filePath)">
                    보기
                </div>
                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:project.sno}, 'filePlan')">이력</div>
            </div>
        </div>
        <!-- 9. 결재PASS 완료 -->
        <div v-if="9 == project.stPlan" class="dp-flex w-100p" >
            <div class="font-11 text-left " >
                사유:{% project.planMemo %}
                <div class="btn btn-white btn-sm hover-btn cursor-pointer" @click="setApprovalReset('plan')" style="color:#0c4da2">
                    PASS취소
                </div>
            </div>
        </div>
        <!-- 2. 결재중일 때-->
        <div v-if="2 == project.stPlan">
            <approval-template2 :project="project" :approval="projectApprovalInfo" :confirm-type="'plan'" :confirm-field="'planConfirm'":memo-field="'planMemo'"></approval-template2>

            <div class="dp-flex">
                <div class="btn btn-sm btn-blue btn-blue-line "  @click="$('#filePlan').find('button').click()">파일재등록</div>
                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 "
                     @click="openUrl('plan'+project.sno, '<?=$nasUrl?>' + fileList.filePlan.files[0].filePath)">
                    보기
                </div>
                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:project.sno}, 'filePlan')">이력</div>
            </div>

            <div class="btn btn-sm btn-red btn-red-line2 mgt5" @click="openApprovalWrite(customer.sno, project.sno, 'plan')">재결재요청</div>

        </div>
        <!-- 1. 파일 등록 했을 때-->
        <div v-if="1 == project.stPlan">

            <div class="dp-flex">
                <div class="btn btn-sm btn-red btn-red-line2"
                     v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 && (0 >= projectApprovalInfo.plan.sno || $.isEmpty(projectApprovalInfo.plan.sno ) )"
                     @click="openApprovalWrite(customer.sno, project.sno, 'plan')">결재요청</div>

                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.filePlan) && fileList.filePlan.files.length > 0 "
                     @click="openUrl('plan'+project.sno, '<?=$nasUrl?>' + fileList.filePlan.files[0].filePath)">
                    보기
                </div>

                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:project.sno}, 'filePlan')">이력</div>
            </div>

            <div class="btn btn-sm btn-blue btn-blue-line mgt5 w-100p"  @click="$('#filePlan').find('button').click()">파일재등록</div>

        </div>

        <!-- 0 . 대기-->
        <div v-if="0 == project.stPlan">
            <div class="btn btn-sm btn-blue btn-blue-line" @click="$('#filePlan').find('button').click()">파일등록</div>
            <!--완료가 아니고 파일이 없을 때는 PASS 버튼 활성화-->
            <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ){ ?>
            <div class="btn btn-white btn-sm" @click="setApprovalPass('plan')">PASS</div>
            <?php } ?>
        </div>
    </div>

</td>
<!--기획 -->
<td class="" style="border-top:none !important;" v-if="$.isEmpty(project['txProposal'])">
    <div class="dp-flex dp-flex-center" >
        <!--10. 결재완료 = 최종완료-->
        <div v-if="10 == project.stProposal" class="text-green">
            <div class="dp-flex dp-flex-center">
                <div @click="openApprovalView(projectApprovalInfo['proposal'].sno)" class="cursor-pointer hover-btn btn btn-white btn-sm">
                    결재정보
                </div>
                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                     @click="openUrl('proposal'+project.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                    보기
                </div>
                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:project.sno}, 'fileProposal')">이력</div>
            </div>
            <div class="dp-flex dp-flex-center" style="margin-top:2px;">
                <div class="w-100p btn btn-sm btn-red btn-red-line2" @click="openProposalUrl()">발&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;송</div>
            </div>
        </div>
        <ims-modal :visible.sync="visibleProposalSendUrl" title="제안서 입력 URL 전송정보">
            <div class="dp-flex justify-content-start">
                <label class="w-80px">담당자:</label>
                <input type="text" class="form-control w-85p" v-model="project.assortReceiver">
            </div>
            <div class="dp-flex justify-content-start mgt5">
                <label class="w-80px">Email:</label>
                <input type="text" class="form-control w-85p" v-model="project.assortEmail">
            </div>
            <template #footer>
                <div class="btn btn-blue mgt5" @click="sendProposalUrl(project.assortReceiver, project.assortEmail, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">전송</div>
                <div class="btn btn-white mgt5" @click="visibleProposalSendUrl=false">취소</div>
            </template>
        </ims-modal>
        <!-- 9. 결재PASS 완료 -->
        <div v-if="9 == project.stProposal" class="dp-flex w-100p" >
            <div class="font-11 text-left " >
                사유:{% project.proposalMemo %}
                <div class="btn btn-white btn-sm hover-btn cursor-pointer" @click="setApprovalReset('proposal')" style="color:#0c4da2">
                    PASS취소
                </div>
            </div>
        </div>
        <!-- 2. 결재중일 때-->
        <div v-if="2 == project.stProposal">
            <approval-template2 :project="project" :approval="projectApprovalInfo" :confirm-type="'proposal'" :confirm-field="'proposalConfirm'":memo-field="'proposalMemo'"></approval-template2>

            <div class="dp-flex">
                <div class="btn btn-sm btn-blue btn-blue-line "  @click="$('#fileProposal').find('button').click()">파일재등록</div>
                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                     @click="openUrl('proposal'+project.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                    보기
                </div>
                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:project.sno}, 'fileProposal')">이력</div>
            </div>
        </div>
        <!-- 1. 파일 등록 했을 때-->
        <div v-if="1 == project.stProposal">

            <div class="dp-flex">
                <div class="btn btn-sm btn-red btn-red-line2"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 && (0 >= projectApprovalInfo.proposal.sno || $.isEmpty(projectApprovalInfo.proposal.sno ) )"
                     @click="openApprovalWrite(customer.sno, project.sno, 'proposal')">결재요청</div>

                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                     @click="openUrl('proposal'+project.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                    보기
                </div>

                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:project.sno}, 'fileProposal')">이력</div>
            </div>

            <div class="btn btn-sm btn-blue btn-blue-line mgt5 w-100p"  @click="$('#fileProposal').find('button').click()">파일재등록</div>

        </div>

        <!-- 0 . 대기-->
        <div v-if="0 == project.stProposal">
            <div class="btn btn-sm btn-blue btn-blue-line" @click="$('#fileProposal').find('button').click()">파일등록</div>
            <!--완료가 아니고 파일이 없을 때는 PASS 버튼 활성화-->
            <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ){ ?>
            <div class="btn btn-white btn-sm" @click="setApprovalPass('proposal')">PASS</div>
            <?php } ?>
        </div>
    </div>

</td>
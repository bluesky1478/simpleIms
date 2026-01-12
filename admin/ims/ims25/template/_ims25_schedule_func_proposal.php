<!--기획 -->
<div class="dp-flex" v-if="$.isEmpty(mainData['txProposal'])">
        <!--10. 결재완료 = 최종완료-->
        <div v-if="10 == mainData.stProposal" class="text-green">
            <!--<i aria-hidden="true" class="fa fa-check-circle"></i>
            {% $.formatShortDate(mainData.exProposal) %} 완료-->
            <div class="dp-flex dp-flex-center mgt3">
                <!--<div class="btn btn-sm btn-red btn-red-line2" @click="openProposalUrl()">발송</div>-->
                <div @click="openApprovalView(projectApprovalInfo['proposal'].sno)" class="cursor-pointer hover-btn btn btn-white btn-sm">
                    결재정보
                </div>
                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                     @click="openUrl('proposal'+mainData.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                    보기
                </div>
                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:mainData.sno}, 'fileProposal')">이력</div>
            </div>
        </div>
        <ims-modal :visible.sync="visibleProposalSendUrl" title="제안서 입력 URL 전송정보">
            <div class="dp-flex justify-content-start">
                <label class="w-80px">담당자:</label>
                <input type="text" class="form-control w-85p" v-model="mainData.assortReceiver">
            </div>
            <div class="dp-flex justify-content-start mgt5">
                <label class="w-80px">Email:</label>
                <input type="text" class="form-control w-85p" v-model="mainData.assortEmail">
            </div>
            <template #footer>
                <div class="btn btn-blue mgt5" @click="sendProposalUrl(mainData.assortReceiver, mainData.assortEmail, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">전송</div>
                <div class="btn btn-white mgt5" @click="visibleProposalSendUrl=false">취소</div>
            </template>
        </ims-modal>
        <!-- 9. 결재PASS 완료 -->
        <div v-if="9 == mainData.stProposal" class="dp-flex w-100p" >
            <div class="font-11 text-left " >
                사유:{% mainData.proposalMemo %}
                <div class="btn btn-white btn-sm hover-btn cursor-pointer" @click="setApprovalReset('proposal')" >
                    PASS취소
                </div>
            </div>
        </div>
        <!-- 2. 결재중일 때-->
        <div v-if="2 == mainData.stProposal">
            <div class="dp-flex">

                <approval-template5 :project="mainData" :approval="projectApprovalInfo" :confirm-type="'proposal'" :confirm-field="'proposalConfirm'":memo-field="'proposalMemo'"></approval-template5>

                <?php if(true === $addBtn){ ?>
                    <div class="btn btn-sm btn-blue btn-blue-line "  @click="$('#fileProposal').find('button').click()">파일재등록</div>
                <?php } ?>

                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                     @click="openUrl('proposal'+mainData.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                    보기
                </div>
                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:mainData.sno}, 'fileProposal')">이력</div>
            </div>
        </div>

        <!-- 1. 파일 등록 했을 때-->
        <div v-if="1 == mainData.stProposal">

            <div class="dp-flex">

                <?php if(true === $addBtn){ ?>
                <div class="btn btn-sm btn-blue btn-blue-line w-100p"  @click="$('#fileProposal').find('button').click()">파일재등록</div>
                <?php } ?>

                <div class="btn btn-sm btn-red btn-red-line2"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 && (0 >= projectApprovalInfo.proposal.sno || $.isEmpty(projectApprovalInfo.proposal.sno ) )"
                     @click="openApprovalWrite(customer.sno, mainData.sno, 'proposal')">결재요청</div>

                <div class="btn btn-white btn-sm"
                     v-if="!$.isEmpty(fileList.fileProposal) && fileList.fileProposal.files.length > 0 "
                     @click="openUrl('proposal'+mainData.sno, '<?=$nasUrl?>' + fileList.fileProposal.files[0].filePath)">
                    보기
                </div>

                <div class="btn btn-white btn-sm" @click="openFileHistory2({projectSno:mainData.sno}, 'fileProposal')">이력</div>
            </div>
        </div>

        <!-- 0 . 대기-->
        <div v-if="0 == mainData.stProposal">
            <div class="btn btn-sm btn-blue btn-blue-line" @click="$('#fileProposal').find('button').click()">파일등록</div>
            <!--완료가 아니고 파일이 없을 때는 PASS 버튼 활성화-->
            <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ){ ?>
            <div class="btn btn-white btn-sm" @click="setApprovalPass('proposal')">PASS</div>
            <?php } ?>
        </div>
    </div>

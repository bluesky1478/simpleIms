<!--작지/사양서  -->
<td class="" style="border-top:none !important;" v-if="$.isEmpty(project['txOrder'])">
    <div class="dp-flex dp-flex-center">
        <!-- 10. 고객 사양서 승인 완료-->
        <div v-if="10 == project.stOrder">
            <div class="dp-flex-center dp-flex mgt3">
                <!--추가 기능 상태 변경 -> 요청 단계로-->
                <div class=" btn btn-sm btn-blue" @click="setOrderStatus('r')">
                    사양서 확정취소
                </div>
                <div class=" btn btn-sm btn-white" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)">
                    보기
                </div>
            </div>
        </div>

        <!-- 4. 사양서 발송완료-->
        <div v-if="4 == project.stOrder">
            <div class="sl-blue font-11">발송완료<span class="font-10">(고객승인대기)</span></div>
            <div class="dp-flex-center dp-flex mgt3">
                <div class=" btn btn-sm btn-blue btn-blue-line"
                     v-if="'r'===project.customerOrderConfirm"
                     @click="openOrderUrl()">
                    재발송
                </div>
                <div class=" btn btn-sm btn-white" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)">
                    보기
                </div>

                <!--사양서URL 전송 레이어팝업-->
                <ims-modal :visible.sync="visibleOrderSendUrl" title="사양서 입력 URL 전송정보">
                    <div class="dp-flex justify-content-start">
                        <label class="w-80px">담당자:</label>
                        <input type="text" class="form-control w-85p" v-model="project.customerOrderReceiver">
                    </div>
                    <div class="dp-flex justify-content-start mgt5">
                        <label class="w-80px">Email:</label>
                        <input type="text" class="form-control w-85p" v-model="project.customerOrderEmail">
                    </div>
                    <template #footer>
                        <div class="btn btn-blue mgt5" @click="sendOrderUrl(project.customerOrderReceiver, project.customerOrderEmail)">전송</div>
                        <div class="btn btn-white mgt5" @click="visibleOrderSendUrl=false">취소</div>
                    </template>
                </ims-modal>

            </div>
        </div>

        <!-- 3. 결재진행-->
        <div v-if="3 == project.stOrder">
            <div class="dp-flex-center dp-flex mgt3">

                <div class=" btn btn-sm btn-blue btn-blue-line"
                     @click="openOrderUrl()">
                    사양서발송
                </div>

                <div class=" btn btn-sm btn-white" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`)">
                    보기
                </div>

                <!--사양서URL 전송 레이어팝업-->
                <ims-modal :visible.sync="visibleOrderSendUrl" title="사양서 입력 URL 전송정보">
                    <div class="dp-flex justify-content-start">
                        <label class="w-80px">담당자:</label>
                        <input type="text" class="form-control w-85p" v-model="project.customerOrderReceiver">
                    </div>
                    <div class="dp-flex justify-content-start mgt5">
                        <label class="w-80px">Email:</label>
                        <input type="text" class="form-control w-85p" v-model="project.customerOrderEmail">
                    </div>
                    <template #footer>
                        <div class="btn btn-blue mgt5" @click="sendOrderUrl(project.customerOrderReceiver, project.customerOrderEmail)">전송</div>
                        <div class="btn btn-white mgt5" @click="visibleOrderSendUrl=false">취소</div>
                    </template>
                </ims-modal>

            </div>
        </div>

        <!-- 결재진행 -->
        <div v-if="2 >= project.stOrder">
            <div >
                <span class="text-green">{% workOrderCompleteCnt %}</span><span class="font-11 bold" style="color:#b1b1b1">(완료)</span>
                /
                <span class="sl-blue">{% productList.length %}</span><span class="font-11 " style="color:#b1b1b1">(스타일)</span>
            </div>
        </div>

    </div>
</td>
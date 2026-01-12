<!--작지/사양서  -->
<div class="dp-flex">
    <!-- 10. 작지 사양서 완료 -->
    <div v-if="10 == mainData.stOrder">

    </div>

    <!-- 3. 결재진행-->
    <div v-if="3 == mainData.stOrder">
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
                    <input type="text" class="form-control w-85p" v-model="mainData.customerOrderReceiver">
                </div>
                <div class="dp-flex justify-content-start mgt5">
                    <label class="w-80px">Email:</label>
                    <input type="text" class="form-control w-85p" v-model="mainData.customerOrderEmail">
                </div>
                <template #footer>
                    <div class="btn btn-blue mgt5" @click="sendOrderUrl(mainData.customerOrderReceiver, mainData.customerOrderEmail)">전송</div>
                    <div class="btn btn-white mgt5" @click="visibleOrderSendUrl=false">취소</div>
                </template>
            </ims-modal>

        </div>
    </div>

    <!-- 결재진행 -->
    <div v-if="2 >= mainData.stOrder">

        <schedule-template :data="mainData" :modify="isModify" :type="'order'" class="mgb5"></schedule-template>
        
        <div >
            <span class="text-green">{% workOrderCompleteCnt %}</span><span class="font-11 bold" style="color:#b1b1b1">(완료)</span>
            /
            <span class="sl-blue">{% productList.length %}</span><span class="font-11 " style="color:#b1b1b1">(스타일)</span>
        </div>
    </div>

</div>
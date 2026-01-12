<div v-if="!$.isEmpty(mainData.assortApproval)">
    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' === mainData.assortApproval"></i>
    <span :class="$.getAssortAcceptNameColor(mainData.assortApproval)['color']">
        {% $.getAssortAcceptNameColor(mainData.assortApproval)['name'] %}
    </span>
</div>

<div class="dp-flex mgt5">
    <div class="" v-if="'n'===mainData.assortApproval">
        <div class=" btn btn-sm btn-blue btn-blue-line" @click="openAssortUrl()">
            입력요청
        </div>
    </div>
    <div class=" btn btn-sm btn-blue btn-blue-line"
         v-if="'r'===mainData.assortApproval"
         @click="openAssortUrl()">
        재요청
    </div>

    <div class="btn btn-sm btn-blue btn-blue-line"
         v-if="'f'===mainData.assortApproval"
         @click="setAssortStatus('r')">
        이전상태로
    </div>

    <div class="btn btn-sm btn-blue"
         v-if="'p'===mainData.assortApproval"
         @click="setAssortStatus('f')">
        확정취소
    </div>

    <div class="" >
        <div class=" btn btn-sm btn-white" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`)">
            입력화면
        </div>
    </div>

    <!--아소트 입력URL 전송 레이어팝업-->
    <ims-modal :visible.sync="visibleAssortSendUrl" title="아소트 입력 URL 전송정보">
        <div class="dp-flex justify-content-start">
            <label class="w-80px">담당자:</label>
            <input type="text" class="form-control w-85p" v-model="mainData.assortReceiver">
        </div>
        <div class="dp-flex justify-content-start mgt5">
            <label class="w-80px">Email:</label>
            <input type="text" class="form-control w-85p" v-model="mainData.assortEmail">
        </div>

        <template #footer>
            <div class="btn btn-blue mgt5" @click="sendAssortUrl(mainData.assortReceiver, mainData.assortEmail)">전송</div>
            <div class="btn btn-white mgt5" @click="visibleAssortSendUrl=false">취소</div>
        </template>
    </ims-modal>

</div>

<div class=" btn btn-sm btn-red btn-red-line2 mgt3 w-100p cursor-pointer hover-btn"
     v-if="'f'===mainData.assortApproval"
     @click="setAssortStatus('p')">
    아소트 확정
</div>
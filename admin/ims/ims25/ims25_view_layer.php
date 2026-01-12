<!-- 참여자 추가 레이어 팝업 ( 프로젝트 상세, 스케쥴관리 팝업에서 사용 ) -->
<transition name="ims-layer-fade">
    <div class="ims-layer-dim" v-if="layerAddMember.visible" @click.self="closeAddMember">
        <div class="ims-layer-wrap" style="min-width:700px">
            <div class="ims-layer-header">
                <div class="ims-layer-title">
                    <span class="ims-layer-title-sub text-danger">
                        # {% mainData.sno %}
                    </span>
                    <!-- 제목 영역 -->
                    <span class="ims-layer-title-main">
                        <span class="sl-blue">
                            {% mainData.customerName %}
                        </span>
                        <span class="font-13 normal">
                            {% mainData.projectYear %} {% mainData.projectSeason %} {% mainData.salesStyleName %} 프로젝트 참여자 추가 등록
                        </span>
                    </span>
                </div>
                <!-- X 버튼 -->
                <button class="ims-layer-close-x" @click="closeAddMember">&times;</button>
            </div>

            <div class="ims-layer-body">
                <div v-if="layerAddMember.loading" class="ims-layer-loading">
                    불러오는 중...
                </div>
                <div v-else>
                    <div class="ims-layer-row">
                        <span class="ims-layer-label">
                            선택된 스케쥴
                        </span>
                        <span class="ims-layer-value">
                            {% layerAddMember.addScheduleKr.join(',') %}
                        </span>
                    </div>

                    <div class="ims-layer-row">
                        <span class="ims-layer-label">
                            참여자 선택
                        </span>
                        <span class="ims-layer-value">
                            <div class="dp-flex dp-flex-gap5">
                                <select class="form-control w-100px" v-model="layerAddMember.salesManager">
                                    <option value="0">영업/기타</option>
                                    <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                        <option value="<?=$key?>:<?=$value?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                                <select class="form-control w-100px" v-model="layerAddMember.designManager">
                                    <option value="0">디자인실</option>
                                    <?php foreach ($designManagerList as $key => $value ) { ?>
                                        <option value="<?=$key?>:<?=$value?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                                <div class="btn btn-gray" @click="addProjectManager()">추가</div>
                            </div>
                        </span>
                    </div>

                    <div class="ims-layer-row">
                        <span class="ims-layer-label">
                            추가 참여자
                        </span>
                        <span class="ims-layer-value">
                            <div >
                                <ul class="dp-flex dp-flex-gap5" v-show="layerAddMember.addManagerList.length > 0">
                                    <li v-for="addManager in layerAddMember.addManagerList">
                                        {% addManager.split(':')[1] %}
                                    </li>
                                </ul>
                            </div>
                            <div v-show="0 >= layerAddMember.addManagerList.length" :class="layerAddMember.regMessageClass">
                                ▼ 참여자 선택 (선택 후 추가버튼을 눌러주세요)
                            </div>
                        </span>
                    </div>

                </div>

            </div>

            <div class="ims-layer-footer">
                <div class="dp-flex dp-flex-right">
                    <button class="ims-layer-btn bg-red" @click="regProjectManager">
                        등록
                    </button>

                    <button class="ims-layer-btn" @click="closeAddMember">
                        닫기
                    </button>
                </div>
            </div>
        </div>
    </div>
</transition>

<!--발송 이력-->
<script type="text/x-template" id="send-history-layer-pop-template">
    <transition name="ims-layer-fade">
        <div class="ims-layer-dim" v-if="visible" @click.self="close">
            <div class="ims-layer-wrap" style="min-width:400px!important;">
                <div class="ims-layer-header">
                    <div class="ims-layer-title">
                        <span class="ims-layer-title-main">
                            <span v-text="title"></span>발송 이력
                        </span>
                    </div>
                    <button class="ims-layer-close-x" @click="close">&times;</button>
                </div>

                <div class="ims-layer-body">
                    <div v-if="loading" class="ims-layer-loading">불러오는 중...</div>
                    <div v-else>
                        <table class="ims-simple-table table-fixed ">
                            <thead>
                                <th>번호</th>
                                <th>수신자</th>
                                <th>이메일</th>
                                <th>발송일</th>
                            </thead>
                            <tbody >
                            <template v-show="list.length > 0" v-for="(each, idx) in list" :key="idx">
                                <tr  class="hover-light">
                                    <td ><span v-text="list.length-idx"></span></td>
                                    <td ><span v-text="each.receiverName"></span></td>
                                    <td ><span v-text="each.receiverMail"></span></td>
                                    <td ><span v-text="each.regDt"></span></td>
                                </tr>
                                <!--<tr>
                                    <td colspan="3">
                                        <div v-if="!$.isEmpty(each.ccList)" class="font-11">
                                            참조:<span v-text="each.ccList"></span>
                                        </div>
                                    </td>
                                </tr>-->
                            </template>
                            <tr v-if="0 >= list.length">
                                <td colspan="99">발송 이력이 없습니다.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="ims-layer-footer">
                    <button class="ims-layer-btn" @click="close">닫기</button>
                </div>
            </div>
        </div>
    </transition>
</script>

<!--이메일 발송-->
<script type="text/x-template" id="email-sender-template">
    <transition name="ims-layer-fade">
        <div class="ims-layer-dim" v-if="visible" @click.self="close">
            <div class="ims-layer-wrap" style="min-width:400px">
                <div class="ims-layer-header">
                    <div class="ims-layer-title">
                        <span class="ims-layer-title-main">
                            <span v-text="title"></span> 발송
                        </span>
                    </div>
                    <button class="ims-layer-close-x" @click="close">&times;</button>
                </div>

                <div class="ims-layer-body">
                    <table class="table table-cols ims-table-style1" style="border-top:none !important;">
                        <colgroup>
                            <col class="w-120px">
                            <col>
                        </colgroup>
                        <tr>
                            <th>수신자명</th>
                            <td><input type="text" class="form-control" v-model="localReceiver" placeholder="수신자 성함"></td>
                        </tr>
                        <tr>
                            <th>이메일</th>
                            <td><input type="text" class="form-control" v-model="localEmail" placeholder="example@email.com"></td>
                        </tr>
                        <tr>
                            <th>참조자</th>
                            <td>
                                <div v-for="(cc, index) in ccList" :key="index" class="dp-flex dp-flex-gap5 mgb5">
                                    <input type="text" class="form-control" v-model="ccList[index]" placeholder="참조자 메일">

                                    <div class="btn btn-sm btn-red btn-red-line2"
                                         v-if="index === ccList.length - 1"
                                         @click="addCc">
                                        추가
                                    </div>

                                    <div class="btn btn-sm btn-white"
                                         v-if="ccList.length > 1"
                                         @click="removeCc(index)">
                                        제거
                                    </div>
                                </div>

                                <div v-if="ccList.length === 0" class="btn btn-sm btn-red btn-red-line2" @click="addCc">
                                    + 참조자 추가
                                </div>

                                <div class="font-11 text-muted mgt5">
                                    * 발송한 사람(로그인 계정)에게는 자동 참조됩니다.
                                </div>
                            </td>
                        </tr>
                        <tr v-if="JSON.parse(localFileUrl).length > 0">
                            <th>발송파일</th>
                            <td>
                                <ul>
                                    <li v-for="(file, idx) in JSON.parse(localFileUrl)">
                                        <span v-text="file.fileName" class="sl-blue"></span>
                                    </li>
                                </ul>
                                <input type="hidden" class="form-control" v-model="localFileUrl" readonly>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="ims-layer-footer">
                    <button class="ims-layer-btn btn-red mgr5" @click="submitSend" :disabled="loading">
                        <i class="fa fa-paper-plane" v-if="!loading"></i>
                        <span v-if="loading">발송 중...</span>
                        <span v-else>메일 발송</span>
                    </button>
                    <button class="ims-layer-btn" @click="close">닫기</button>
                </div>
            </div>
        </div>
    </transition>
</script>

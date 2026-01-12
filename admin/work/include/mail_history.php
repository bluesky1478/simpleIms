<div class="modal fade" id="mailHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">메일 발송 정보</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-email-list">
                            <div class="table-responsive border-none" tabindex="1" style="overflow: hidden; outline: none;">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr class="cursor-pointer" v-for="(item, index) in items.mailHistory" :key="index" >
                                        <th class="border-none">
                                            {% index+1 %}
                                        </th>
                                        <td class="border-none">
                                            <div>
                                                TO. {% item.mailReceiverName %}님 ({% item.sendEmail %})
                                            </div>
                                            <div>
                                                <small class="text-muted">({% item.managerNm %} {% item.regDt %}에 발송)</small>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card"  v-show="0 >= items.mailHistory.length">
                                <div class="card-header bg-dark" >발송 내역 없음</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>


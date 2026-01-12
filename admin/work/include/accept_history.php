<div class="modal fade" id="acceptHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">승인이력</h4>
            </div>
            <div class="modal-body">

                <table class="table table-cols">
                    <colgroup>
                        <col class="width-sm"/>
                        <col>
                    </colgroup>
                    <tbody>
                    <tr class="cursor-pointer" v-for="(item, index) in items.acceptHistory" :key="index">
                        <th class="text-center">
                            {% items.acceptHistory.length  - index %}. {% item.regDt %}
                        </th>
                        <td>
                            {% item.title %} 항목 : {% item.managerNm %} {% acceptTypeMap[item.status] %} 처리
                            <hr>
                            <div class="card-text" v-html="item.comment"></div>

                            <div class="card"  v-show="0 >= items.acceptHistory.length">
                                <div class="card-header bg-dark" >승인내역 없음</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>
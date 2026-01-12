<div class="modal fade" id="managerListModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">담당자 정보</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-email-list">
                            <div class="table-responsive" tabindex="1" style="overflow: hidden; outline: none;">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr class="cursor-pointer" v-for="(item, index) in selectManagerList" :key="index" @click="selectManager(item, items.docData)">
                                        <th>
                                            {% index+1 %}
                                        </th>
                                        <td>
                                            <div>
                                                {% item.name %} {% item.position %}
                                                <small class="text-muted">({% item.phone %} / {% item.cellPhone %})</small>
                                            </div>
                                            <div>Email : {% item.email %}</div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
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


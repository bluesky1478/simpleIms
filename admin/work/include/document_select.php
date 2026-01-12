<div class="modal fade" id="documentSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">문서 선택</h4>
            </div>
            <div class="modal-body">

                <table class="table table-cols">
                    <colgroup>
                        <col class="width-sm"/>
                        <col>
                    </colgroup>
                    <tbody>
                    <tr class="cursor-pointer" v-for="(item, index) in selectDocumentList" :key="index" @click="selectDocument(item, items.docData)">
                        <th>
                            <div>{% item.docName %} {% item.version %}차</div>
                        </th>
                        <td>
                            <div class="btn btn-line-white">{% item.proposalTypeName %}</div>
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
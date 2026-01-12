<!--생산 스케쥴 수정 레이어 팝업-->
<div class="modal fade small-picker" id="modalScheduleModify"  role="dialog"  aria-hidden="true"  >
    <div class="modal-dialog" role="document" style="width:1490px">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
            <span class="modal-title font-18 bold" >
                <span class="text-danger">{% scheduleModify.checkCnt %}</span>개 스케쥴 일괄 수정
            </span>
            <div class="notice-info">일괄수정은 가장 처음 선택한 대상 기준으로 데이터가 표시 됩니다.</div>
        </div>
        <div class="modal-body">
                <div >
                    <table class="table table-cols table-pd-3 table-default-center" style="margin-bottom:0 !important;" >
                        <colgroup>
                            <col style="width:5.5%">
                            <?php foreach( $stepList as $stepName ) { ?>
                                <col style="width:5%">
                            <?php } ?>
                        </colgroup>
                        <tbody>
                        <tr >
                            <th>구분</th>
                            <?php foreach( $stepTitleList as $stepName ) { ?>
                                <th><?=$stepName?></th>
                            <?php } ?>
                        </tr>
                        <tr >
                            <th>예정일</th>
                            <?php foreach( $stepList as $stepName ) { ?>
                                <td  class="ta-l">
                                    <date-picker v-model="scheduleModify.<?=$stepName?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr >
                            <th>대체내용</th>
                            <?php foreach( $stepList as $stepName ) { ?>
                                <td>
                                    <input type="text" class="form-control" v-model="scheduleModify.<?=$stepName?>Memo" placeholder="일정대체내용" style="width:115px; border-radius: 3px">
                                </td>
                            <?php } ?>
                        </tr>
                        <tr >
                            <th>완료일</th>
                            <?php foreach( $stepList as $stepName ) { ?>
                                <td  class="ta-l">
                                    <date-picker v-model="scheduleModify.<?=$stepName?>CompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr >
                            <th>완료일대체</th>
                            <?php foreach( $stepList as $stepName ) { ?>
                                <td>
                                    <input type="text" class="form-control" v-model="scheduleModify.<?=$stepName?>Memo2" placeholder="완료일대체" style="width:115px; border-radius: 3px">
                                </td>
                            <?php } ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
        </div>
        <div class="modal-footer">
            <div class="btn btn-red btn-red-line2" @click="ImsProductionService.saveScheduleBatch()" >일괄 수정하기</div>
            <div class="btn btn-gray" data-dismiss="modal">닫기</div>
        </div>
    </div>
</div>



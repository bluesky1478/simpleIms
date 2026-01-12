<?php if( !empty($emergencyTodoList) ) {  ?>
    <ims-modal :visible.sync="visibleTodayTodo" show-close-button="n" title="긴급 처리 요청 건" max-width="650px">
        <div>
            <table class="w-100p table-bordered">
                <colgroup>
                    <col class="w-52p">
                    <col class="w-10p">
                    <col class="w-20p">
                    <col class="w-18p">
                </colgroup>
                <tr>
                    <th class="pd10">제목</th>
                    <th class="pd10">요청자</th>
                    <th class="pd10">고객</th>
                    <th class="pd10">프로젝트번호</th>
                </tr>
                <?php $emergencyTodoSnoList = []; ?>
                <?php foreach($emergencyTodoList as $emTodo) { ?>
                    <?php $emergencyTodoSnoList[] = $emTodo['resSno'];  ?>
                    <tr>
                        <td class="pdl10 ta-l">
                            <?=$emTodo['subject']?>
                        </td>
                        <td class="pdl10 ta-l"><?=$emTodo['regManagerName']?></td>
                        <td class="pdl10 ta-l"><?=$emTodo['customerName']?></td>
                        <td class="pdl10 ta-l text-danger"><?=$emTodo['projectSno']?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <template #footer>
            <div class="btn btn-white mgt5" @click="()=>{ ImsService.setEmergencyTodoConfirm('<?=implode(',',$emergencyTodoSnoList)?>'); visibleTodayTodo=false}">확인했습니다.</div>
        </template>
    </ims-modal>
<?php } ?>
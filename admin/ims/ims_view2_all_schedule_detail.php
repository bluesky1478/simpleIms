<table class="table ims-schedule-table w100 table-default-center table-fixed  table-td-height35 table-th-height35 mgb10 table-pd-3">
    <colgroup>
        <col class="w-4p">
        <col class="w-6p">
        <col class="w-6p">
        <col class="w-6p">
        <col class="w-7p">
        <col class="w-7p">
        <col class="w-6p">
        <col class="w-6p">
        <col class="w-7p">
        <col class="w-7p">
        <!--<col class="w-4p">
        <col class="w-5p">
        <col class="w-5p">
        <col class="w-5p">
        <col class="w-8p">
        <col class="w-8p">
        <col class="w-5p">
        <col class="w-5p">
        <col class="w-5p">
        <col class="w-5p">
        <col class="w-5p">
        <col class="w-8p">
        <col class="w-8p">-->
        <!--<col class="w-5p">-->
    </colgroup>
    <tr>
        <th class="" >구분</th>
        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_DETAIL_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
            <th class="" ><?=$mainScheduleKr?></th>
        <?php } ?>
    </tr>
    <tr>
        <td class="bg-light-gray">
            예정일
        </td>
        <!-- 예정일 스케쥴 -->
        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_DETAIL_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
            <td class="bg-light-yellow" v-if="$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])">
                <div :class="'dp-flex dp-flex-center ' + ( 'y' === project.delay<?=ucfirst($mainSchedule)?> ? 'text-danger':''  ) ">
                    <expected-template :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></expected-template>
                </div>
            </td>
            <td class="bg-light-gray" rowspan="3" v-if="!$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">
                <div class="dp-flex dp-flex-center cursor-pointer hover-btn relative w-100px" @click="openProjectUnit(project.sno,'<?=$mainSchedule?>','<?=$mainScheduleKr?>')">
                    {% project['tx'+$.ucfirst('<?=$mainSchedule?>')] %}
                    <comment-cnt2 :data="project['<?=$mainSchedule?>CommentCnt']"></comment-cnt2>
                </div>
            </td>
        <?php } ?>
    </tr>
    <tr>
        <td class="bg-light-gray font-11" rowspan="2" style="border-bottom:solid 1px #dddddd">
            상태/완료일
        </td>

        <!-- 완료일 스케쥴 -->
        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_DETAIL_SCHEDULE_LIST as $mainSchedule => $mainScheduleKr ){ ?>
            <td class="" v-if="$.isEmpty(project['tx'+$.ucfirst('<?=$mainSchedule?>')])"
                <?=(!in_array($mainSchedule,['plan','proposal','order','productionOrder']))?'rowspan=2':''?>
                style="border-bottom:solid 1px #dddddd"
            >
                <div class="dp-flex-center dp-flex">
                    <div class="dp-flex dp-flex-center">
                        <?php if( !in_array($mainSchedule,['plan','proposal','order','productionOrder']) ) { ?>
                            <complete-template2 :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
                        <?php }else{ ?>
                            <div v-show="!isModify">
                                <complete-template2 :modify="isModify" :data="project" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
                            </div>
                            <div v-show="isModify">
                                자동등록
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </td>
        <?php } ?>
    </tr>
    <tr>
        <!--기획-->
        <?php include 'ims_view2_all_schedule_func_plan.php'?>
        <!--제안-->
        <?php include 'ims_view2_all_schedule_func_proposal.php'?>
        <!--작지사양서-->
        <?php include 'ims_view2_all_schedule_func_work.php'?>
        <!--발주-->
        <?php include 'ims_view2_all_schedule_func_order.php'?>
    </tr>
</table>
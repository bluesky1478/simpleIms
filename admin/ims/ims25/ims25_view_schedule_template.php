<?php

use Component\Imsv2\ImsScheduleUtil;

?>
<div class="schedule-area">
    <div class="table-pop-title-small mgt5 dp-flex dp-flex-between">
        <div class="bold relative">
            <?=$scheduleTitle?>
            <span class="notice-info mgl10">예정일이 없거나 완료된 스케쥴의 참여자는 리스트에 표시되지 않습니다.</span>
        </div>
        <div class="dp-flex dp-flex-gap5 pdb3 relative">
            <div class="btn btn-sm btn-white" v-show="isModify" @click='resetAreaExpectedDates(mainData, <?= json_encode(array_keys($scheduleList)) ?>)'>
                <?=$scheduleTitle?> 예정일 초기화
            </div>
        </div>
    </div>
    <table class="table ims-schedule-table w100 table-default-center table-fixed table-td-height0 table-th-height0 mgb10 table-pd-4" >
        <colgroup>
            <col class="w-4p">
            <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
                <col />
            <?php } ?>
        </colgroup>
        <tr>
            <th class="">구분</th>
            <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
                <th class="font-12 relative" :style="'y' === allScheduleMap['<?=$mainSchedule?>'].cust ? 'background-color:#f2f9ff!important' : '' ">
                    <?='s' === \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST[$mainSchedule]['dept'] ? '<span class="ims-corner-badge">영</span>' : '' ?>
                    <?='d' === \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST[$mainSchedule]['dept'] ? '<span class="ims-corner-badge ims-corner-badge--yellow">디</span>' : '' ?>
                    <?='q' === \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST[$mainSchedule]['dept'] ? '<span class="ims-corner-badge ims-corner-badge--green">생</span>' : '' ?>

                    <label v-show="isModify" class="hand hover-btn">
                        <input type="checkbox" class="form-control" style="position:absolute; top:12px; left:2px" v-show="isModify" value="<?=$mainSchedule?>" name="chkSchedule[]" v-model="chkSchedule">
                        <span class="font-bold"><?=$mainScheduleKr?></span>
                    </label>
                    <div v-show="!isModify">
                        <?=$mainScheduleKr?>
                    </div>
                </th>
            <?php } ?>
        </tr>
        <tr>
            <td class="bg-light-gray">
                예정일
            </td>
            <!-- 예정일 스케쥴 -->
            <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
                <td class="bg-light-yellow" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])">

                    <div class="font-11 font-italic text-muted"
                         @click="mainData['ex<?=ucfirst($mainSchedule)?>']=scheduleConfig['<?=$mainSchedule?>']['deadLine']"
                         v-if="!$.isEmptyAll(mainData.salesStartDt) && !$.isEmptyAll(mainData.customerDeliveryDt) && !$.isEmptyAll(scheduleConfig['<?=$mainSchedule?>'])">
                        DL: {% $.formatShortDateWithoutWeek(scheduleConfig['<?=$mainSchedule?>']['deadLine']) %}
                    </div>

                    <div :class="'ims-tt ims-tt-light dp-flex dp-flex-center ' + ( 'y' === mainData.delay<?=ucfirst($mainSchedule)?> ? 'text-danger':''  ) ">
                        <expected-template :modify="isModify" :data="mainData" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></expected-template>

                        <!--코멘트 툴팁-->
                        <div class="ims-tt-box" v-if="!$.isEmpty(commentMap[mainData.sno]) && !$.isEmpty(commentMap[mainData.sno]['<?=$mainSchedule?>'])">
                            <div>
                                <ul class="ta-l">
                                    <li v-for="(comment, commentIdx) in commentMap[mainData.sno]['<?=$mainSchedule?>']"
                                        v-if="6 >= commentIdx"
                                        style="border-bottom:dot-dot-dash 1px #000" class="font-12 mgb5 pdb2"
                                    >
                                        <div>{% $.formatShortDateWithoutWeek(comment.regDt) %} {% comment.regManagerName %}</div>
                                        <div class="pdl2">▶ {% comment.comment %}</div>
                                    </li>
                                    <li v-if="commentMap[mainData.sno]['<?=$mainSchedule?>'].length > 6" class="font-11">
                                        코멘트는 최대 6개만 표시 됩니다.
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </td>
                <td class="bg-light-gray" rowspan="3" v-if="!$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">

                    <div class="dp-flex dp-flex-center cursor-pointer relative w-100px ims-tt ims-tt-light"
                         @click="openProjectUnit(mainData.sno,'<?=$mainSchedule?>','<?=$mainScheduleKr?>')">

                        {% mainData['tx'+$.ucfirst('<?=$mainSchedule?>')] %}
                        <comment-cnt2 :data="mainData['<?=$mainSchedule?>CommentCnt']"></comment-cnt2>

                        <!--코멘트 툴팁-->
                        <div class="ims-tt-box" v-if="!$.isEmpty(commentMap[mainData.sno]) && !$.isEmpty(commentMap[mainData.sno]['<?=$mainSchedule?>'])">
                            <div>
                                <ul class="ta-l">
                                    <li v-for="(comment, commentIdx) in commentMap[mainData.sno]['<?=$mainSchedule?>']"
                                        v-if="6 >= commentIdx"
                                        style="border-bottom:dot-dot-dash 1px #000" class="font-12 mgb5 pdb2"
                                    >
                                        <div>{% $.formatShortDateWithoutWeek(comment.regDt) %} {% comment.regManagerName %}</div>
                                        <div class="pdl2">▶ {% comment.comment %}</div>
                                    </li>
                                    <li v-if="commentMap[mainData.sno]['<?=$mainSchedule?>'].length > 6" class="font-11">
                                        코멘트는 최대 6개만 표시 됩니다.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <i class="fa fa-times hand hover-btn" aria-hidden="true" v-show="isModify" @click="mainData['tx'+$.ucfirst('<?=$mainSchedule?>')]=''"></i>
                    </div>
                </td>
            <?php } ?>
        </tr>
        <tr>
            <td class="bg-light-gray" style="border-bottom:solid 1px #dddddd">
                상 태
            </td>
            <!-- 완료일 스케쥴 -->
            <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
                <td class="ta-c" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">

                    <div class="dp-flex dp-flex-center font-11">
                        <?php if( !in_array($mainSchedule, ImsScheduleUtil::getAutoScheduleKey() ) ) { ?>
                            <complete-template2 :modify="isModify" :data="mainData" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
                        <?php }else{ ?>
                            <div  v-show="!isModify">
                                <div :class="mainData['<?=$mainSchedule?>Color']" v-show="!isModify">
                                    <i :class="'fa fa-'+mainData['<?=$mainSchedule?>Icon']" aria-hidden="true" v-if="!$.isEmpty(mainData['<?=$mainSchedule?>Icon'])"></i>
                                    <span v-if="mainData['st'+$.ucfirst('<?=$mainSchedule?>')] >= 9">{% $.formatShortDate(mainData['cp'+$.ucfirst('<?=$mainSchedule?>')]) %}</span>
                                    {% mainData['<?=$mainSchedule?>Status'] %}
                                </div>
                            </div>
                            <div v-show="isModify">
                                자동등록
                            </div>
                        <?php } ?>
                    </div>

                    <div class="dp-flex dp-flex-center font-11" v-show="isModify">
                        <input type="hidden" placeholder="대체문자" class="form-control w-90p mgt3" v-model="mainData['tx'+$.ucfirst('<?=$mainSchedule?>')]">
                    </div>
                    <div v-show="isModify" class="sl-blue hand hover-btn" @click="mainData['tx'+$.ucfirst('<?=$mainSchedule?>')]='해당없음'">
                        해당없음
                    </div>

                    <!-- 결재 -->
                    <div v-show="!isModify" class="dp-flex dp-flex-center mgt5">
                        <?php $addBtn = false ?>
                        <?php if( 'plan' === $mainSchedule){ ?>
                            <?php include './admin/ims/ims25/template/_ims25_schedule_func_plan.php'?>
                        <?php } ?>
                        <?php if( 'proposal' === $mainSchedule){ ?>
                            <?php include './admin/ims/ims25/template/_ims25_schedule_func_proposal.php'?>
                        <?php } ?>
                        <?php if( 'work' === $mainSchedule){ ?>
                            <?php include './admin/ims/ims25/template/_ims25_schedule_func_work.php'?>
                        <?php } ?>
                    </div>

                </td>
            <?php } ?>
        </tr>
        <tr>
            <td class="bg-light-gray" style="border-bottom:solid 1px #dddddd">
                참 여
            </td>
            <!-- 프로젝트 추가 참여자 -->
            <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
                <td class="font-11 ta-l" style="border-bottom:solid 1px #dddddd" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])">
                    <div v-show="!isModify" v-if="!$.isEmpty(mainData.<?=$mainSchedule?>AddManager)">
                    <span v-for="(managerInfo, idx) in mainData.<?=$mainSchedule?>AddManager">
                        {% managerInfo.managerNm %}
                    </span>
                    </div>
                    <div v-show="isModify" v-if="!$.isEmpty(mainData.<?=$mainSchedule?>AddManager)">
                        <ul>
                            <li v-for="(managerInfo, managerIdx) in mainData.<?=$mainSchedule?>AddManager">
                                {% managerInfo.managerNm %}
                                <i class="fa fa-times hand hover-btn" aria-hidden="true" @click="deleteElement(mainData.<?=$mainSchedule?>AddManager, managerIdx)"></i>
                            </li>
                        </ul>
                    </div>
                </td>
            <?php } ?>
        </tr>
    </table>
</div>


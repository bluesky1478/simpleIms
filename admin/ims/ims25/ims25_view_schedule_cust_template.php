<?php use Component\Imsv2\ImsScheduleUtil; ?>
<div class="table-pop-title-small mgt5 dp-flex dp-flex-between">
    <div class="bold relative">
        <?=$scheduleTitle?>
    </div>
    <div class="dp-flex dp-flex-gap5 pdb3 relative">
        
        고객 공유 상태 : 공유 전
        
    </div>
</div>

<table class="table ims-schedule-table w100 table-default-center table-fixed table-td-height0 table-th-height0 mgb10 table-pd-4" >
    <colgroup>
        <col class="<?=gd_isset($divColWidth,'w-4p')?>">
        <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
            <col />
        <?php } ?>
    </colgroup>
    <tr>
        <th class="">구분</th>
        <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
            <th class="font-12 relative"
                v-if="!$.isEmpty(allScheduleMap['<?=$mainSchedule?>'])"
                :style="'y' === allScheduleMap['<?=$mainSchedule?>'].cust ? 'background-color:#f2f9ff!important' : '' ">
                <?='s' === \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST[$mainSchedule]['dept'] ? '<span class="ims-corner-badge">영</span>' : '' ?>
                <?='d' === \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST[$mainSchedule]['dept'] ? '<span class="ims-corner-badge ims-corner-badge--yellow">디</span>' : '' ?>
                <?='q' === \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST[$mainSchedule]['dept'] ? '<span class="ims-corner-badge ims-corner-badge--green">생</span>' : '' ?>
                <label v-show="isModify" class="hand hover-btn">
                    <span class="font-bold"><?=$mainScheduleKr?></span>
                </label>
                <div v-show="!isModify">
                    <?=$mainScheduleKr?>
                </div>
            </th>
            <th v-else class="font-12 relative" style="background-color:#f2f9ff!important">
                납품
            </th>
        <?php } ?>
    </tr>
    <tr>
        <td class="bg-light-gray">
            예정일
        </td>
        <!-- 예정일 스케쥴 -->
        <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>

            <?php if('customerDeliveryDt' !== $mainSchedule) { ?>
                <td class="bg-light-yellow" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])">

                    <div class="font-11 font-italic text-muted cursor-pointer hover-btn"
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

            <?php }else{ ?>
                <td rowspan="2" style="border-bottom:1px solid rgb(221, 221, 221)">

                    <div v-show="!isModify" class="dp-flex-gap10 dp-flex dp-flex-center">
                        <div v-if="$.isEmpty(mainData.customerDeliveryDt)">
                            <span class="font-11 text-muted">미정</span>
                        </div>
                        <div v-if="!$.isEmpty(mainData.customerDeliveryDt)">
                            {% $.formatShortDate(mainData.customerDeliveryDt) %}
                            <span class="font-11 " v-html="$.remainDate(mainData.customerDeliveryDt,true)"></span>
                        </div>

                        <div class="text-danger " v-if="'y' !== mainData.customerDeliveryDtConfirmed">
                            변경불가
                        </div>
                        <div class="sl-blue " v-else>
                            변경가능
                        </div>
                    </div>
                    <div v-show="isModify" class="mini-picker ">

                        <date-picker v-model="mainData.customerDeliveryDt" value-type="format" format="YYYY-MM-DD"  :editable="false"></date-picker>

                        <div class="mgt3">
                            <label class="radio-inline">
                                <input type="radio" name="preSalesDeliveryConfirm"  value="y" v-model="mainData.customerDeliveryDtConfirmed"/>변경가능
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="preSalesDeliveryConfirm"  value="n" v-model="mainData.customerDeliveryDtConfirmed"/>변경불가
                            </label>
                        </div>
                    </div>

                </td>
            <?php } ?>
        <?php } ?>
    </tr>
    <tr>
        <td class="bg-light-gray" style="border-bottom:solid 1px #dddddd">
            상 태
        </td>
        <!-- 완료일 스케쥴 -->
        <?php foreach( $scheduleList as $mainSchedule => $mainScheduleKr ){ ?>
            <?php if('customerDeliveryDt' !== $mainSchedule) { ?>
                <td class="ta-c" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">
                    <div class="dp-flex dp-flex-center font-11">
                        <?php if( !in_array($mainSchedule, ImsScheduleUtil::getAutoScheduleKey() ) ) { ?>
                            <complete-template2 :modify="isModify" :data="mainData" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
                        <?php }else{ ?>
                            <div v-show="!isModify">
                                <complete-template2 :modify="isModify" :data="mainData" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></complete-template2>
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
                </td>
            <?php } ?>
        <?php } ?>
    </tr>
</table>


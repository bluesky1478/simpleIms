<div class="row" v-if="!$.isEmpty(mainData.regDt)">
    <div class="pd15">
        <div class="table-pop-title">
            <div class="dp-flex dp-flex-gap15">
                스케쥴 관리
                <div class="font-14 dp-flex dp-flex-gap15" v-show="!isModify">
                    <div class="dp-flex dp-flex-gap5 noto">
                        <span class="noto">영업 담당 :</span>
                        <div v-show="$.isEmpty(mainData.salesManagerNm)" class="text-muted">미정</div>
                        <div v-show="!$.isEmpty(mainData.salesManagerNm)" class="font-bold">{% mainData.salesManagerNm %}</div>
                    </div>
                    <div class="dp-flex dp-flex-gap5 noto">
                        <span class="noto">디자인 담당 :</span>
                        <div v-show="$.isEmpty(mainData.designManagerNm)" class="text-muted">미정</div>
                        <div v-show="!$.isEmpty(mainData.designManagerNm)" class="font-bold">{% mainData.designManagerNm %}</div>
                    </div>
                </div>

                <div class="font-14 dp-flex dp-flex-gap15" v-show="isModify">
                    <div class="dp-flex">영업 담당 :
                        <select class="form-control" v-model="mainData.salesManagerSno" >
                            <option value="0">미정</option>
                            <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="dp-flex mgl10">디자인 담당 :
                        <select class="form-control" v-model="mainData.designManagerSno" >
                            <option value="0">미정</option>
                            <?php foreach ($designManagerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear-both mgt3">
            <div class="relative">
                <ul class="nav nav-tabs mgb0" role="tablist" ><!--제안서 이상 단계에서만 선택 가능-->

                    <li role="presentation" :class="'all' === scheduleTabMode?'active':''" v-if="'pop_view' === viewPageName">
                        <a href="#" data-toggle="tab"  @click="changeScheduleTab('all')" >전체 스케쥴</a>
                    </li>

                    <li role="presentation" :class="'summary' === scheduleTabMode?'active':''" v-if="'main' === viewPageName">
                        <a href="#" data-toggle="tab"  @click="changeScheduleTab('summary')" >스케쥴 요약</a>
                    </li>

                    <li role="presentation" :class="'customer' === scheduleTabMode?'active':''" >
                        <a href="#" data-toggle="tab"  @click="changeScheduleTab('customer')" >고객 안내 스케쥴</a>
                    </li>

                    <li role="presentation" :class="'sales' === scheduleTabMode?'active':''">
                        <a href="#" data-toggle="tab"  @click="changeScheduleTab('sales')" >영업팀 스케쥴</a>
                    </li>
                    <li role="presentation" :class="'design' === scheduleTabMode?'active':''" >
                        <a href="#" data-toggle="tab" @click="changeScheduleTab('design')">디자인실 스케쥴</a>
                    </li>
                    <li role="presentation" :class="'qc' === scheduleTabMode?'active':''" >
                        <a href="#" data-toggle="tab" @click="changeScheduleTab('qc')">생산팀 스케쥴</a>
                    </li>

                    <li role="presentation" :class="'all' === scheduleTabMode?'active':''" v-if="'main' === viewPageName">
                        <a href="#" data-toggle="tab"  @click="changeScheduleTab('all')" >전체 스케쥴</a>
                    </li>

                    <!--<li role="presentation" :class="'comment' === scheduleTabMode?'active':''" >
                        <a href="#" data-toggle="tab" @click="changeScheduleTab('comment')">Comment List</a>
                    </li>-->
                </ul>

                <div style="position: absolute;right: 0; top:10px" class="dp-flex dp-flex-gap10" v-show="!isModify">
                    <div class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" style="font-family: Godo" @click="setModify(true)">수정</div>
                    <!--<div class="btn btn-white btn-sm" style="font-family: Godo">스케쥴 변경 이력</div>-->
                </div>

                <div style="position: absolute;right: 0; top:10px" class="dp-flex dp-flex-gap10" v-show="isModify">
                    <div class="btn btn-white btn-sm" @click="setAutoScheduleByDeadLine(0)">DL-0일 설정</div>
                    <div class="btn btn-white btn-sm" @click="setAutoScheduleByDeadLine(7)">DL-7일 설정</div>
                    <div class="btn btn-white btn-sm" @click="setAutoScheduleByDeadLine(14)">DL-14일 설정</div>
                    <div class="btn btn-white btn-sm" @click="setAutoScheduleByDeadLine(21)">DL-21일 설정</div>

                    <div class="btn btn-red btn-sm" v-show="isModify" style="font-family: Godo" @click="openAddManager()">추가 참여자 등록</div>
                    <div class="btn btn-red btn-sm" v-show="isModify" style="font-family: Godo" @click="save()">저장</div>
                    <div class="btn btn-white btn-sm" v-show="isModify" style="font-family: Godo" @click="setModify(false)">취소</div>
                </div>
            </div>
        </div>

        <!--요약-->
        <div v-show="'customer' === scheduleTabMode">
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 고객 제안 일정';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_CUSTOMER;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_cust_template.php' ?>
        </div>

        <!--요약-->
        <div v-show="'summary' === scheduleTabMode">
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 전체 스케쥴 요약';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_SUMMARY;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_cust_template.php' ?>
        </div>

        <!--전체-->
        <div v-show="'all' === scheduleTabMode">
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 사전 준비';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 기획/제안/샘플 제작 단계';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE2;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 샘플확정 / 계약 단계';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE3;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 발주 단계';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE4;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
        </div>

        <!--영업-->
        <div v-show="'sales' === scheduleTabMode">
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 사전 준비';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>

            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 영업 관리 스케쥴';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_SALES;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
        </div>

        <!--디자인-->
        <div v-show="'design' === scheduleTabMode">
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 디자인실 관리 스케쥴';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_DESIGN;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
        </div>

        <!--QC-->
        <div v-show="'qc' === scheduleTabMode">
            <?php
            $scheduleTitle = '<i class="fa fa-calendar-o" aria-hidden="true"></i> 생산팀 관리 스케쥴';
            $scheduleList = \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_QC;
            ?>
            <?php include './admin/ims/ims25/ims25_view_schedule_template.php' ?>
        </div>

        <div class="dp-flex dp-flex-center dp-flex-gap5 pdb3 relative">
            <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
            <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false);refreshProject(sno)">취소</button>
        </div>

    </div>
</div>
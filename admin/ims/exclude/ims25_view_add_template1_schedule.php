<div class="col-xs-12" >
    <div class="col-xs-12" >
        <div >

            <div class="table-title gd-help-manual">
                <div class="flo-left area-title ">
                    <span class="godo">
                        스케쥴 관리
                    </span>
                </div>
            </div>

            <!--100%는 생산이 완료된 후. -->
            
            <!--스타일 탭-->
            <ul class="nav nav-tabs mgb0" role="tablist" ><!--제안서 이상 단계에서만 선택 가능-->
                <li role="presentation" :class="'detail' === scheduleTabMode?'active':''">
                    <a href="#" data-toggle="tab"  @click="changeScheduleTab('detail')" >전체 스케쥴</a>
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
                <!--
                <li role="presentation" :class="'customer' === scheduleTabMode?'active':''">
                    <a href="#" data-toggle="tab"  @click="changeScheduleTab('customer')" >★ 고객 안내 스케쥴</a>
                </li>
                -->
            </ul>

            <div class="" style="position: absolute; top:5px; right:0">
                <div class="mgl10 dp-flex dp-flex-gap5" >

                    <div class="btn btn-white">무슨버튼</div>

                </div>
            </div>

            <div >
                <!--스케쥴
                스케쥴 나오게 하기
                1. 동일한 스케쥴 리스트 나오게 하고
                2. 가변적으로 스케쥴이 표시 된다.
                -->
                <table class="table table-cols w100 table-default-center table-fixed  table-td-height35 table-th-height35 mgb10 table-pd-3 border-top-none">
                    <tr>
                        <?php foreach(\Component\Imsv2\ImsScheduleUtil::SCHEDULE_LIST_SALES as $scheduleCode) { ?>
                            <td style="background-color:#F6F6F6" class="font-12">
                                <?=\Component\Imsv2\ImsScheduleUtil::SCHEDULE_LIST[$scheduleCode]['name']?>
                            </td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <?php foreach(\Component\Imsv2\ImsScheduleUtil::SCHEDULE_LIST_SALES as $scheduleCode) { ?>
                            <td style="font-size:11px!important;border-top:none !important;" >
                                -
                            </td>
                        <?php } ?>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>
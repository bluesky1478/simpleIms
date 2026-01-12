<div class="table-pop-title-small mgt10">
    <div class="flo-left bold relative">
        <i class="fa fa-calendar-o" aria-hidden="true"></i> 사전 준비

        <div class="dp-flex dp-flex-gap5 pd5" style="position:absolute;top:-7px;left:100px" v-show="isModify">

            <select class="form-control w-100px" v-model="addMemberSales" >
                <option value="0">영업/기타</option>
                <?php foreach ($salesEtcManagerList as $key => $value ) { ?>
                    <option value="<?=$key?>:<?=$value?>"><?=$value?></option>
                <?php } ?>
            </select>

            <select class="form-control w-100px" v-model="addMemberDesign" >
                <option value="0">디자인실</option>
                <?php foreach ($designManagerList as $key => $value ) { ?>
                    <option value="<?=$key?>:<?=$value?>"><?=$value?></option>
                <?php } ?>
            </select>
            <button type="button" class="btn btn-red btn-red-line2 btn-sm"  @click="addProjectMember()">
                <i class="fa fa-user" aria-hidden="true"></i>
                추가 참여자 등록
            </button>
        </div>

    </div>
    <div class="flo-right dp-flex dp-flex-gap5 pdb3 relative">
        <button type="button" class="btn btn-red btn-red-line2 btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
        <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
        <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false);refreshProject(sno)">취소</button>
    </div>
</div>

<table class="table ims-schedule-table w100 table-default-center table-fixed table-td-height35 table-th-height35 mgb10 table-pd-3 mgt5" >
    <colgroup>
        <col class="w-4p">
        <?php foreach( \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1 as $mainSchedule => $mainScheduleKr ){ ?>
            <col />
        <?php } ?>
    </colgroup>
    <tr>
        <th class="">구분</th>
        <?php foreach( \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1 as $mainSchedule => $mainScheduleKr ){ ?>
            <th class="font-11 relative">
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
        <?php foreach( \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1 as $mainSchedule => $mainScheduleKr ){ ?>
            <td class="bg-light-yellow" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])">
                <div :class="'dp-flex dp-flex-center ' + ( 'y' === mainData.delay<?=ucfirst($mainSchedule)?> ? 'text-danger':''  ) ">
                    <expected-template :modify="isModify" :data="mainData" :type="'<?=$mainSchedule?>'" :title="'<?=$mainScheduleKr?>'"></expected-template>
                </div>
            </td>
            <td class="bg-light-gray" rowspan="3" v-if="!$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">
                <div class="dp-flex dp-flex-center cursor-pointer hover-btn relative w-100px" @click="openProjectUnit(mainData.sno,'<?=$mainSchedule?>','<?=$mainScheduleKr?>')">
                    {% mainData['tx'+$.ucfirst('<?=$mainSchedule?>')] %}
                    <comment-cnt2 :data="mainData['<?=$mainSchedule?>CommentCnt']"></comment-cnt2>
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
        <?php foreach( \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1 as $mainSchedule => $mainScheduleKr ){ ?>
            <td class="ta-c" v-if="$.isEmpty(mainData['tx'+$.ucfirst('<?=$mainSchedule?>')])" style="border-bottom:solid 1px #dddddd">

                <div class="dp-flex dp-flex-center font-11">
                    <?php if( !in_array($mainSchedule,['plan','proposal','order','productionOrder']) ) { ?>
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
    </tr>
    <tr>
        <td class="bg-light-gray" style="border-bottom:solid 1px #dddddd">
            참 여
        </td>
        <!-- 프로젝트 추가 참여자 -->
        <?php foreach( \Component\Imsv2\ImsScheduleConfig::SCHEDULE_LIST_TYPE1 as $mainSchedule => $mainScheduleKr ){ ?>
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
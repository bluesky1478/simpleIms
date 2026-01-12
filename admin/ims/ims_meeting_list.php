<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3> 미팅관리 </h3>
            <div class="btn-group">

                <!--<div class="btn btn-red" @click="openMeetingView(0,0)">미팅등록</div>
                <div class="btn btn-red btn-sm btn-save" style="padding-top:6px;" >수정</div>-->
                <input type="button" value="미팅등록" id="btn-reg" class="btn btn-red js-register" @click="openMeetingView(0,0)" />

            </div>
        </div>
    </form>

    <div class="row">

        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-3xl">
                            <col class="width-md">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>검색어</th>
                            <td >
                                <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, 'v-model="meetingSearchCondition.key"', 'form-control'); ?>
                                <input type="text" name="keyword" class="form-control" v-model="meetingSearchCondition.keyword"  @keyup.enter="search()" />
                            </td>
                            <th>상태</th>
                            <td colspan="99">
                                <label class="radio-inline">
                                    <input type="radio" name="meetingStatus" value="" v-model="meetingSearchCondition.meetingStatus" @change="search()" />전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="meetingStatus" value="0" v-model="meetingSearchCondition.meetingStatus" @change="search()" />준비
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="meetingStatus" value="1" v-model="meetingSearchCondition.meetingStatus" @change="search()" />완료
                                </label>
                            </td>
                        </tr>
                        <!--
                        <tr>
                            <th>
                                등록일자
                            </th>
                            <td colspan="99">
                                <div class="input-group js-datepicker">
                                    <input type="text" class="form-control width-xs dt-period" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>">
                                    <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                                </div>
                                ~
                                <div class="input-group js-datepicker">
                                    <input type="text" class="form-control width-xs dt-period" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>">
                                    <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                                </div>

                                <div class="btn-group sl-dateperiod " data-toggle="buttons" data-target-name="treatDate[]" data-target-inverse="1">
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="0">오늘</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="14">15일</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="30">1개월</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="60">2개월</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="90">3개월</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="120">4개월</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="150">5개월</label>
                                    <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="180">6개월</label>
                                    <div class="btn btn-sm btn-white inline-block mgl5" onclick="$('.dt-period').val('')">초기화</div>
                                </div>
                            </td>
                        </tr>
                        -->
                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="search()">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="conditionReset()">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
            </div>

            <div >
                <div class="">
                    <div class="flo-left mgb5 mgt25">
                        <span class="font-16 ">
                            총 <span class="bold text-danger">{% $.setNumberFormat(meetingTotal.recode.total) %}</span> 건
                        </span>
                    </div>
                    <div class="flo-right mgb5">
                        <div class="bold font-18 ta-r">미팅 리스트</div>
                        <div style="display: flex">
                            <select @change="search()" class="form-control" v-model="meetingSearchCondition.sort">
                                <option value="D,desc">등록일 ▼</option>
                                <option value="D,asc">등록일 ▲</option>
                                <option value="B,desc">고객사별 ▼</option>
                                <option value="B,asc">고객사별 ▲</option>
                            </select>

                            <select v-model="meetingSearchCondition.pageNum" @change="search()" class="form-control mgl5">
                                <option value="5">5개 보기</option>
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="">
                    <?php include 'template/ims_meeting_list_template.php'?>
                </div>

                <div id="meeting-page" v-html="meetingPage" class="ta-c"></div>

            </div>

        </div>

    </div>

</section>

<?php include 'script/ims_meeting_list_script.php'?>


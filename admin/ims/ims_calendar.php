<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<!-- FullCalendar CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- FullCalendar JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<!-- FullCalendar Korean Locale CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ko.js"></script>

<style>
    .fc-daygrid-day-number {cursor: pointer}
    .fc-day-sat {background-color: #edf2ff; /* 토요일의 배경색 */}
    .fc-day-sat .fc-daygrid-day-number{color: #1f42f2;}
    /* 일요일 색상 변경 */
    .fc-day-sun {background-color: #fff4f4; /* 일요일의 배경색 */}
    .fc-day-sun .fc-daygrid-day-number{color:red}
    .holiday-event {
        background-color: #ffcccc !important;
        border-color: #ff9999 !important;
        color: #d9534f !important; /* 공휴일 텍스트 색상 */
    }
    /* 공휴일 날짜 숫자 스타일 */
    .holiday-day {
        color: red !important; /* 공휴일 날짜를 빨간색으로 */
    }

    .fc-event-title { font-size:1.1em !important; font-weight:normal !important; }
    .fc-daygrid-day-bottom { margin:0 !important; }
    .fc-event {cursor: pointer; /* 커서를 손가락 모양으로 변경 */}
    /* 툴팁 스타일 */
    .tooltip {
        display: none;
        position: absolute;
        padding: 10px;
        background-color: #333;
        color: #fff;
        border-radius: 5px;
        font-size: 12px;
        z-index: 1000;
        white-space: nowrap;
    }
</style>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>스케쥴 관리</h3>
            <div class="btn-group font-20 ">
                <input type="button" value="스케쥴 등록" class="btn btn-red btn-reg hover-btn" @click="()=>{vueApp.regData.mode = 'reg';$('#modalProduction').modal('show');}" />
            </div>
        </div>
    </form>

    <div class="row " >
        <div class="col-xs-12 relative">
            <div id="calendar" style="height:100vh;"></div>
            <!-- 툴팁 요소 -->
            <div class="tooltip" id="tooltip"></div>
        </div>
    </div>

    <div style="margin-bottom:150px"></div>

    <div class="modal fade xsmall-picker" id="modalProduction"  role="dialog"  aria-hidden="true"  >
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="font-15 bold" v-if="'reg' === regData.mode">스케쥴 등록</div>
                    <div class="font-15 bold" v-if="'modify' === regData.mode">
                        스케쥴 정보( {% regData.regManagerNm %} 등록)
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;" >
                        <colgroup>
                            <col style="width:12%">
                            <col style="width:38%">
                            <col style="width:12%">
                            <col style="width:38%">
                        </colgroup>
                        <tbody>
                        <tr >
                            <th>일정유형</th>
                            <td>
                                <select class="form-control" v-model="regData.type">
                                    <?php foreach( \Component\Ims\ImsCodeMap::SCHEDULE_TYPE as $scheduleKey => $scheduleType ) { ?>
                                        <option value=<?=$scheduleKey?>><?=$scheduleType?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th>일정제목</th>
                            <td >
                                <input type="text" class="form-control" maxlength="14" v-model="regData.title" >
                            </td>
                        </tr>
                        <tr >
                            <th>시작일</th>
                            <td>
                                <date-picker v-model="regData.start" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            </td>
                            <th>종료일</th>
                            <td >
                                <date-picker v-model="regData.end" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            </td>
                        </tr>
                        <tr>
                            <th>상세</th>
                            <td class="pd0" colspan="99">
                                <textarea class="form-control" v-model="regData.contents" rows="5"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="notice-info">* 시작일 종료일이 동일한 경우 입력하지 않아도 됩니다.</div>
                </div>
                <div class="modal-footer">
                    <div class="btn btn-white" @click="reset()">초기화</div>

                    <div class="btn btn-gray" @click="ImsService.deleteData('schedule',regData.sno, afterEvent)" v-if="'modify' === regData.mode">삭제</div>
                    |
                    <div class="btn btn-red btn-save" @click="addSchedule">저장</div>
                    <div class="btn btn-gray" data-dismiss="modal" id="btnClosePopRegCalendar">닫기</div>
                </div>
            </div>
        </div>
    </div>



</section>

<script type="text/javascript">

    let calendar = null;

    $(()=>{
        $(appId).hide();
        const serviceData = {};

        const defaultData = {
            'mode' : 'reg',
            'sno' : '',
            'title' : '',
            'contents' : '',
            'start' : '',
            'end' : '',
            'type' : '0',
        }

        ImsBoneService.setData(serviceData,{
            regData : $.copyObject(defaultData)
        });

        ImsBoneService.setMethod(serviceData, {
            reset : ()=>{
                vueApp.regData = $.copyObject(defaultData);
            },
            afterEvent : ()=>{
                $('#btnClosePopRegCalendar').click();
                vueApp.reset();
                vueApp.refresh((data)=>{
                    vueApp.mainData = data.data;
                    calendar.removeAllEvents();
                    calendar.addEventSource(vueApp.mainData);
                    $('.btn-save').show();
                });
            },
            addSchedule : ()=>{
                //vueApp.mainData = $.copyObject(vueApp.regData); //저장 후 (리스트 갱신이 나을 듯)
                const saveData = vueApp.regData;
                if( 0 === Number(saveData.type) ){
                    $.msg('타입선택은 필수 입니다.','','warning');
                    return false;
                }
                if( '0000-00-00' === saveData.start ){
                    $.msg('시작일은 필수 입니다.','','warning');
                    return false;
                }
                vueApp.saveData('imsCalendar', saveData, ()=>{
                    location.reload();
                    vueApp.afterEvent();
                });
            },
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            vueApp.$nextTick(function () {
                const bidProjectList = JSON.parse('<?=$bidProjectList?>');
                //입찰
                bidProjectList.forEach((bidProject)=>{
                    vueApp.mainData.push({
                        projectSno : bidProject.projectSno,
                        start: bidProject.exMeeting,
                        description: bidProject.customerName + ' 입찰',
                        backgroundColor: '#7c0000',
                        borderColor: '#930000',
                        title: '(★입찰) ' + bidProject.customerName,
                    });
                });

                const tooltip = document.getElementById('tooltip');
                const calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ko',  // 한국어 로케일 적용
                    events: vueApp.mainData,
                    eventClick: function(info) {
                        //console.log(info.event.extendedProps.sno);
                        ImsService.getData('imsSchedule',info.event.extendedProps.sno).then((data)=>{
                            //console.log('캘린더', data);
                            //수정하기.
                            vueApp.reset();
                            vueApp.regData.mode = 'modify';
                            vueApp.regData.title = data.data.title;
                            vueApp.regData.contents = data.data.contents;
                            vueApp.regData.start = data.data.start;
                            vueApp.regData.end = data.data.end;
                            vueApp.regData.type = data.data.type;
                            vueApp.regData.sno = data.data.sno;
                            vueApp.regData.regManagerNm = data.data.regManagerNm;

                            $('#modalProduction').modal('show');
                        });
                        // 클릭 시 링크로 이동
                        /*if (info.event.url) {
                            window.open(info.event.url); // 새 창에서 링크 열기
                            info.jsEvent.preventDefault(); // 기본 동작(FullCalendar 내부 동작) 방지
                        }*/
                    },
                    dateClick: function(info) {
                        if(info.jsEvent.target.classList.contains('fc-daygrid-day-number')) {
                            vueApp.reset();
                            vueApp.regData.start = info.dateStr;
                            $('#modalProduction').modal('show');
                        }
                    },
                    /*eventMouseEnter: function(info) {
                        // 툴팁에 내용 설정
                        if( !$.isEmpty(info.event.extendedProps.description) ){
                            tooltip.innerHTML = '<strong>' + info.event.title + '</strong><br>' + info.event.extendedProps.description;
                            // 툴팁을 보이게 설정하고 위치 지정
                            tooltip.style.display = 'block';
                            tooltip.style.top = (info.jsEvent.pageY - 220) + 'px';
                            tooltip.style.left = (info.jsEvent.pageX - 220) + 'px';
                            console.log(info.jsEvent.pageY);
                        }
                    },
                    eventMouseLeave: function(info) {
                        console.log('out...')
                        // 마우스가 이벤트에서 벗어나면 툴팁 숨기기
                        tooltip.style.display = 'none';
                    },
                    eventMouseMove: function(info) {
                        console.log('move...')
                        // 마우스가 움직일 때 툴팁 위치 업데이트
                        tooltip.style.top = (info.jsEvent.pageY + 220) + 'px';
                        tooltip.style.left = (info.jsEvent.pageX + 200) + 'px';
                    }*/

                });
                calendar.render();

                $('.holiday-event').each(function(){
                    $(this).closest('.fc-daygrid-day-frame').find('.fc-daygrid-day-number').css('color','#ff4500');
                });
            });
        });
        ImsBoneService.serviceBegin('imsScheduleAll',{},serviceData);
    });
</script>
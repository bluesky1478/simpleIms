<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        const searchConditionDefault = {
            key : 'cust.customerName',
            keyword : '',
            meetingStatus : '0',
            page : 1,
            pageNum : 20,
            sort : 'D,desc',
        }

        const init = ()=>{
            const initParams = {
                data : {
                    //미팅 리스트
                    customerSummary : false,
                    meetingSearchCondition : $.copyObject(searchConditionDefault),
                    meetingList : [],
                    meetingTotal : ImsProductService.getTotalPageDefault(),
                    meetingPage : '',
                },
                mounted : (vueInstance)=>{
                    //List 갱신.
                    MeetingService.getList();
                    console.log('mounted complete..');
                },
                methods : {
                    openMeetingView : MeetingService.openMeetingView,
                    conditionReset : () => {
                        vueApp.meetingSearchCondition = $.copyObject(searchConditionDefault);
                        MeetingService.getList();
                    },
                    search : MeetingService.getList,
                },


            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }

        init();

        //Load Data And Init
        /*ImsService.getList(DATA_MAP.REQUEST,).then((data)=>{

        });*/
    });
</script>
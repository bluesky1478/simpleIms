<script type="text/javascript">

    $(appId).hide();

    const tabList = {
        basic : '기본정보',
        meeting : '고객 코멘트',
        project : '전체 프로젝트',
        /*style : '스타일',*/
        //work : '작업지시서',
        //workConfirm : '사양서',
        /*comment : '코멘트 모아보기',*/
        //issue : '고객요청(클레임)/이슈',
    };

    $(()=>{

        const tabMode = '<?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ?'basic':$requestParam['tabMode']?>';

        //Load Data.
        const sno = '<?=$requestParam['sno']?>';
        console.log(sno);
        ImsService.getData(DATA_MAP.CUSTOMER,sno).then((data)=>{
            console.log(data.data);
            const initParams = {
                data : {
                    tabList : tabList,
                    tabMode : tabMode, //
                    items : data.data,
                    customerSummary : true,

                    //미팅 리스트 => 코멘트 리스트
                    imsCommentList : [],
                    imsCommentSearchCondition : {customerSno : sno, sort : 'D,desc'},

                    //프로젝트 리스트
                    projectList : [],
                    projectListSearchCondition : {customerSno : sno, sort : 'P2,desc'},
                    projectPage : '',
                    projectTotal : ImsProductService.getTotalPageDefault(),

                    <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueTypeKey => $issueType){ ?>
                    <?=$issueTypeKey?>List : [], //이슈
                    <?php } ?>

                    issueShowList : 'all',

                },
                methods : {
                    save : ImsCustomerService.saveCustomer,
                    changeTab : (tabName)=>{
                        vueApp.tabMode = tabName
                    },
                    openMeetingView : MeetingService.openMeetingView,
                },
                mounted : (vueInstance)=>{
                    vueApp.$nextTick(function () {
                        $('.js-sms-send').click(gd_sms_send_popup.call_opener_action);
                        CommonService.getList('imsComment');
                        ProjectService.getList();
                        ImsService.getList(DATA_MAP.CUST_ISSUE, {customerSno:vueApp.items.sno, pageNum:9999, sort:'D,desc'}).then((data)=>{
                            if(200 === data.code){
                                console.log('고객 이슈 리스트 데이터', data.data);
                                data.data.list.forEach((issueData)=>{
                                    vueApp[issueData.issueType+'List'].push(issueData);
                                });
                            }else{
                                console.log('고객 이슈 가져오기 error ', data.message);
                            }
                        });

                        console.log('Mounted...');
                    });
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>
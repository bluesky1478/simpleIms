<script type="text/javascript">

    function overwriteCustomerContact(oObj) {
        vueApp.customer.contactName = oObj.cContactName;
        vueApp.customer.contactPosition = oObj.cContactPosition;
        vueApp.customer.contactMobile = oObj.cContactMobile;
        vueApp.customer.contactPreference = oObj.cContactPreference;
        vueApp.customer.contactEmail = oObj.cContactEmail;
    }

    const sno = '<?=$requestParam['sno']?>';
    const defaultModifyMode = <?=gd_isset($requestParam['modify'], 'false')?>;

    $(appId).hide();

    const tabList = {
        basic: '고객 정보',
        comment: '고객 코멘트',
        /*meeting : '미팅 보고서',*/
        mall: '폐쇄몰 정보',
        project: '프로젝트(발주이력)',
        stored: '비축 원자재',
        style: '스타일관리',
        //sample: '고객샘플관리',
        estimate: '고객견적',
        //work : '작업지시서',
        //workConfirm : '사양서',
        /*comment : '코멘트 모아보기',*/
        //issue : '고객요청(클레임)/이슈',
        //prj_issue : '프로젝트/스타일 이슈',
    };

    $(() => {
        const tabMode = '<?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ? 'basic' : $requestParam['tabMode']?>';
        //const tabMode = 'style';
        const issueShowType = '<?=empty($requestParam['type']) || 'undefined' == $requestParam['type'] ? 'all' : $requestParam['type']?>';

        //Load Data.
        //console.log(sno);
        ImsService.getData(DATA_MAP.CUSTOMER, sno).then((data) => {
            console.log(data.data);
            const initParams = {
                data: {
                    isRtw : 'y', //프로젝트 기성 제외여부

                    isModify: defaultModifyMode,
                    tabList: tabList,
                    tabMode: tabMode, //
                    customer: data.data,
                    customerSummary: true,

                    //미팅 리스트 => 코멘트 리스트
                    imsCommentList: [],
                    imsCommentSearchCondition: {customerSno: sno, sort: 'D,desc'},

                    //프로젝트 리스트
                    projectList: [],
                    projectListSearchCondition: {
                        customerSno: sno,
                        sort: 'P8,desc',
                        orderProgressChk : [
                            '90','91'
                        ]
                    },
                    projectPage: '',
                    projectTotal: ImsProductService.getTotalPageDefault(),

                    //원자재 리스트
                    storedList: [],
                    storedListSearchCondition: {customerSno: sno, sort: 'A,asc'},
                    storedPage: '',
                    storedTotal: ImsProductService.getTotalPageDefault(),

                    //고객 견적 리스트
                    estimateList: [],
                    estimateListSearchCondition: {customerSno: sno, sort: 'D,desc'},
                    estimatePage: '',
                    estimateTotal: ImsProductService.getTotalPageDefault(),

                    //샘플 리스트
                    sampleList: [],
                    sampleListSearchCondition: {customerSno: sno, sort: 'D,desc'},
                    samplePage: '',
                    sampleTotal: ImsProductService.getTotalPageDefault(),

                    //프로젝트/스타일 이슈
                    projectIssueList: [],
                    projectIssueListSearchCondition: {customerSno: sno, sort: 'D,desc'},
                    projectIssuePage: '',
                    projectIssueTotal: ImsProductService.getTotalPageDefault(),
                    projectIssueFieldData : [],

                    <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueTypeKey => $issueType){ ?>
                    <?=$issueTypeKey?>List: [], //이슈
                    <?php } ?>
                    issueShowList: issueShowType,

                    //고객 스타일 리스트
                    customerPrdSearchCondition: {
                        'customerSno': sno,
                        'sort': 'S2',
                        'projectTypeChk': [0,2,6,8,1],
                        'orderProgressChk': [60,90,91],
                        'prdYear': '',
                        'prdSeason': '',
                        'prdName': '',
                    },
                    customerPrdList: [],
                    customerPrdField: [],
                    //업종
                    aParentBusiCateList : ['상위업종 선택'],
                    aoBusiCateList : [],
                    sChooseParentBusiCateName : '상위업종 선택',
                },
                methods: {
                    save: () => {
                        ImsCustomerService.saveCustomer(vueApp.customer).then((data) => {
                            ImsService.getData(DATA_MAP.CUSTOMER, sno).then((data) => {
                                vueApp.customer = data.data;
                                vueApp.isModify = false;
                            });
                        });
                    },
                    changeTab: (tabName) => {
                        vueApp.tabMode = tabName
                    },
                    openMeetingView: MeetingService.openMeetingView,
                    popSampleDetail: (sno) => { //샘플리스트에서 샘플명 클릭시 상세팝업
                        const win = popup({
                            url: `<?=$myHost?>/ims/popup/ims_pop_product_sample_detail.php?sno=${sno}`,
                            target: 'imsProductSampleDetail' + sno,
                            width: 1550,
                            height: 900,
                            scrollbars: 'yes',
                            resizable: 'yes'
                        });
                        win.focus();
                    },
                },
                mounted: (vueInstance) => {
                    //업종리스트 가져오기 -> 상위업종, 업종 나눠서 담기
                    let bFlagMatchBusiCate = false;
                    vueApp.sChooseParentBusiCateName = vueApp.customer.parentBusiCateName;
                    ImsNkService.getList('busiCate', {}).then((data)=> {
                        $.imsPostAfter(data, (data) => {
                            if (data.list.length > 0) {
                                $.each(data.list, function (key, val) {
                                    if (val.parentBusiCateSno == 0) {
                                        if (bFlagMatchBusiCate === false && val.sno == vueApp.customer.busiCateSno) vueApp.sChooseParentBusiCateName = val.cateName; //고객정보에 저장된 업종sno가 상위업종sno인 경우
                                        vueApp.aParentBusiCateList.push(val.cateName);
                                    } else {
                                        if (bFlagMatchBusiCate === false && val.sno == vueApp.customer.busiCateSno) bFlagMatchBusiCate = true;
                                        vueApp.aoBusiCateList.push({'busiCateSno':val.sno, 'cateName':val.cateName, 'parentCateName':val.parentCateName});
                                    }
                                });
                                //고객정보에 저장된 업종sno가 상위업종sno인 경우 or 업종이 삭제된 경우
                                if (bFlagMatchBusiCate === false) {
                                    if (vueApp.sChooseParentBusiCateName == '') vueApp.sChooseParentBusiCateName = '상위업종 선택';
                                    vueApp.customer.busiCateSno = 0;
                                }
                            }
                        });
                    });
                    vueApp.$nextTick(function () {
                        $('.js-sms-send').click(gd_sms_send_popup.call_opener_action);
                        CommonService.getList('imsComment');

                        ProjectService.getList();
                        StoredServiceNk.getListOfCustom();
                        //샘플리스트, 견적리스트 가져오기
                        //CustSampleServiceNk.getListOfCustom();
                        CustEstimateServiceNk.getListOfCustom();

                        //프로젝트/스타일 이슈 가져오기
                        ImsNkService.getList('projectIssue',vueApp.projectIssueListSearchCondition).then((data)=>{
                            $.imsPostAfter(data, (data)=> {
                                vueApp.projectIssueList = data.list;
                                vueApp.projectIssuePage = data.pageEx;
                                vueApp.projectIssueTotal = data.page;
                                vueApp.projectIssueFieldData = data.fieldData;
                            });
                        });

                        ImsService.getList(DATA_MAP.CUST_ISSUE, {
                            customerSno: vueApp.customer.sno,
                            pageNum: 9999,
                            sort: 'D,desc'
                        }).then((data) => {
                            if (200 === data.code) {
                                //console.log('고객 이슈 리스트 데이터', data.data);
                                data.data.list.forEach((issueData) => {
                                    vueApp[issueData.issueType + 'List'].push(issueData);
                                });
                            } else {
                                console.log('고객 이슈 가져오기 error ', data.message);
                            }
                        });
                        refreshCustomerProductList(sno);
                    });
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

<script>
    function refreshCustomerProductList() {
        $('#customer-style-preloader').show();
        $.imsPost2('getListStyleWithCustomerField', vueApp.customerPrdSearchCondition, (data) => {
            $('#customer-style-preloader').hide();
            vueApp.customerPrdList = data.list.list;
            vueApp.customerPrdField = data.field;
        });
    }
</script>

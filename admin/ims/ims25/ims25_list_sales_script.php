<script type="text/javascript">
    /** 타이틀 설정 */
    $('title').html('영업 관리');

    /** 검색 설정 */
    const mainListPrefix = 'sales';
    const allListSearchDefaultData = {
        searchManager : '',
        orderProgressChk : ['10'],
        customerStatus : [],
        excludeStatus : [], //제외
        sort : "P5,asc",
        delayStatus : [],
        bizPlanYn : 'all',
        designWorkType : 'all',
        pageNum : '100',
        isBookRegistered : '0',
        chkUseMall : false,
        chkUse3pl : false,
        chkPackingYn : false,
        directDeliveryYn : false,
        projectTypeChk : [0,2,6,8,5],
        viewType : 'project',
        searchDateType : 'prj.regDt',
        startDt : '',
        endDt : '',
        year : '',
        season : '',
        bidType2 : 'all',
        parentBusiCateSno : 0,
        busiCateSno : 0,
    };

    /** 기본 갱신 함수 */
    const getListData = (params, listPrefix)=>{
        //console.log('리스트 갱신 함수 실행', params);
        return $.imsPost('getIms25AllList', params);
    };

    /** 탭모드 , 리스트 개별 데이터 정의 */
    let defaultTabMode = $.isset($.cookie('salesListDefaultTabMode'),'wait');
    const eachListData = {
        listTabMode : defaultTabMode,
        parentCateList : [],
        cateList : [],
    };

    /** 탭에 따른 기본 프로젝트상태 설정 */
    const setOrderProgressByTab=(condition, tabName)=>{
        if('hold'===tabName){
            condition.orderProgressChk = SALES_LIST_CONDITION_MAP[tabName];
            //delete condition.excludeStatus;
        }else{
            condition.orderProgressChk = SALES_LIST_CONDITION_MAP[tabName];
            //condition.excludeStatus = ['11','98','99'];
        }
        /*if('hold'===tabName){
            vueApp.searchCondition.orderProgressChk = SALES_LIST_CONDITION_MAP[tabName];
            delete vueApp.searchCondition.excludeStatus;
        }else{
            vueApp.searchCondition.orderProgressChk = SALES_LIST_CONDITION_MAP[tabName];
        }*/
    };

    /** 리스트 개별 메소드 정의 */
    const eachListMethod = {
        /**
         * 탭변경
         */ 
        changeTab: (tabName)=>{
            //초기화 후 검색 조건 변경
            vueApp.conditionResetNotRefresh();
            vueApp.listTabMode = tabName;
            setOrderProgressByTab(vueApp.searchCondition, tabName);
            $.cookie('salesListDefaultTabMode', tabName);
            vueApp.refreshList(1);
        },
    };
    /** 리스트 개별 종합 계산 정의 */
    const eachListComputed = {};

    $(()=>{
        //TabMode에 따라 기본 검색 변경 (vueApp 설정 전)
        setOrderProgressByTab(allListSearchDefaultData, defaultTabMode);

        const listFoundation = getIms25ListFoundationData(()=>{
            setCommentMap();
            setTmMap();
        });

        /* 마운트 액션 */
        ImsBoneService.setMounted(listFoundation.serviceData, ()=>{
            ImsCustomerService.setBizCateSearch(vueApp, 'parentCateList', 'cateList');
            //프로젝트 등록 이벤트
            $('#btn-reg-project').click(()=>{
                $('#btn-reg-project-hide').click();
            });
        });
        listFoundation.listService.init(listFoundation.serviceData);
    });
</script>

<script type="text/javascript">
    const salesListField = {
        //대기 리스트
        wait : [
            { title : '진행타입', type : 's', name : 'bidType2Kr', col : 4,  class : '' },
            { title : '프로젝트<br>타입', type : 's', name : 'projectTypeKr', col : 4,  class : '' },
            { title : '업종', type : 'c', name : 'pBizName', col : 7,  class : 'ta-l pdl5 font-11' },
            //{ title : '업종상세', type : 's', name : 'bizName', col : 7,  class : 'ta-l pdl5 font-11' },
            { title : '고객구분', type : 'c', name : 'customerStatus', col : 4,  class : ''},
            { title : '프로젝트/고객', type : 'c', name : 'customerName', col : 0,  class : 'ta-l pdl5 sl-blue'},
            { title : '추정매출<br>예상마진', type : 'c', name : 'estSales', col : 8,  class : 'font-11' },
            { title : '미팅/입찰예정', type : 'c', name : 'exMeeting', col : 5,  class : 'font-11'},
            /*{ title : '사전기획 예정', type : 'd2', name : 'exSalesReadyPlan', col : 6,  class : '' },*/
            { title : '디자인실 참여(예정)', type : 'c', name : 'designTeamInfo', col : 0,  class : 'ta-l font-11 ' },
            { title : '영업담당자', type : 'manager', name : 'salesManagerSno', col : 5,  class : '' },
            //▼ 추 후 실제 레이아웃 보고 text-overflow : ellipsis (생략) 처리하기
            { title : '영업 메모', type : 'c', name : 'salesMemo', col : 15,  class : 'pdl5 ta-l font-11' },
            { title : '영업 기획', type : 'c', name : 'salesPlan', col : 4,  class : '' },
            { title : 'TM/EM<br>영업 내역', type : 'c', name : 'tmList', col : 4,  class : '' },
            { title : '등록일', type : 'd2s', name : 'regDt', col : 4,  class : '' },
            { title : '등록자', type : 'c', name : 'regManagerNm', col : 4,  class : '' }
        ], //메인 스타일 필드 설정
        plan : [
            { title : '진행상태', type : 'c', name : 'projectStatusKr', col : 4, rowspan : true },
            { title : '프로젝트 타입', type : 'c', name : 'projectType', col : 5, rowspan : true },
            { title : '프로젝트/스타일', type : 'c', name : 'project', col : 13, rowspan : true },
            { title : '담당/참여자', type : 'c', name : 'projectSimpleMember', col : 9, rowspan : true },
            /*{ title : '매출정보', type : 'c', name : 'salesInfo', col : 6, rowspan : true },*/
            { title : '구분', type : 'c', name : 'expectedTitle', col : 2, class : 'bg-light-gray2' },
            { title : '구분', type : 'c', name : 'completeTitle', col : 2, subRow : true, class : '' },
            // 예정 (첫번째 TR) ######################################################
            { title : '사전 기획', type : 'schedule', name : 'salesReadyPlan', col : 3, class : 'bg-light-yellow' },
            { title : '입찰설명회/사전미팅', type : 'schedule', name : 'meetingReady', col : 3, class : 'bg-light-yellow' },
            { title : '샘플확보', type : 'schedule', name : 'sampleCust', col : 3, class : 'bg-light-yellow' },
            { title : '<span class="font-11">디자인제안서</span>', type : 'schedule', name : 'readyToDesign', col : 3, class : 'bg-light-yellow' },
            { title : '<span class="font-11">개선제안서</span>', type : 'schedule', name : 'readyToImprove', col : 3, class : 'bg-light-yellow' },
            { title : '<span class="font-11">선호도조사</span>', type : 'schedule', name : 'readyToPrefer', col : 3, class : 'bg-light-yellow' },
            { title : '샘플 테스트', type : 'schedule', name : 'sampleTest', col : 3, class : 'bg-light-yellow' },
            { title : '<span class="font-11">근무환경조사</span>', type : 'schedule', name : 'envSurvey', col : 3, class : 'bg-light-yellow' },
            { title : '리서치 시행', type : 'schedule', name : 'researchField', col : 3, class : 'bg-light-yellow' },
            { title : '미팅/입찰', type : 'schedule', name : 'meeting', col : 3, class : 'bg-light-yellow' },
            { title : '영업 기획', type : 'schedule', name : 'salesPlan', col : 3, class : 'bg-light-yellow' },
            // 완료 (두번째 TR) ######################################################
            { title : '사전 기획', type : 'schedule', name : 'salesReadyPlan', col : 3, subRow : true },
            { title : '입찰설명회/사전미팅', type : 'schedule', name : 'meetingReady', col : 3, subRow : true },
            { title : '샘플확보', type : 'schedule', name : 'sampleCust', col : 3, subRow : true },
            { title : '디자인제안서', type : 'schedule', name : 'readyToDesign', col : 3, subRow : true },
            { title : '개선제안서', type : 'schedule', name : 'readyToImprove', col : 3, subRow : true },
            { title : '선호도조사', type : 'schedule', name : 'readyToPrefer', col : 3, subRow : true },
            { title : '샘플테스트', type : 'schedule', name : 'sampleTest', col : 3, subRow : true },
            { title : '근무환경조사', type : 'schedule', name : 'envSurvey', col : 3, subRow : true },
            { title : '리서치시행', type : 'schedule', name : 'researchField', col : 3, subRow : true },
            { title : '미팅/입찰', type : 'schedule', name : 'meeting', col : 3, subRow : true },
            { title : '영업 기획', type : 'schedule', name : 'salesPlan', col : 3, subRow : true },
        ],
        proc : [
            { title : '진행상태', type : 'c', name : 'projectStatusKr', col : 4, rowspan : true },
            { title : '프로젝트 타입', type : 'c', name : 'projectType', col : 5, rowspan : true },
            { title : '프로젝트/스타일', type : 'c', name : 'project', col : 13, rowspan : true },
            { title : '담당/참여자/부가서비스', type : 'c', name : 'projectMember', col : 10, rowspan : true },
            { title : '매출정보', type : 'c', name : 'salesInfo', col : 6, rowspan : true },
            { title : '고객납기', type : 'c', name : 'customerDeliveryDt', col : 5, rowspan : true },
            { title : '발주D/L', type : 'c', name : 'productionOrder', col : 5, rowspan : true },
            { title : '구분', type : 'c', name : 'expectedTitle', col : 3, class : 'bg-light-gray2' },
            { title : '구분', type : 'c', name : 'completeTitle', col : 3, subRow : true, class : '' },
            // 예정 (첫번째 TR) ######################################################
            { title : '제안서<br>전달', type : 'schedule', name : 'meetingProposal', col : 3, class : 'bg-light-yellow' },
            { title : '샘플<br>제안/발송', type : 'schedule', name : 'sampleInform', col : 3, class : 'bg-light-yellow' },
            { title : '샘플 확정', type : 'schedule', name : 'sampleConfirm', col : 3, class : 'bg-light-yellow' },
            { title : '영업 확정서/ 계약서', type : 'schedule', name : 'salesConfirmation', col : 3, class : 'bg-light-yellow' },
            { title : '고객발주(아소트)', type : 'schedule', name : 'assortConfirm', col : 3, class : 'bg-light-yellow' },
            { title : '사양서 확정', type : 'schedule', name : 'orderConfirm', col : 3, class : 'bg-light-yellow' },
            // 완료 (두번째 TR) ######################################################
            { title : '제안서 전달', type : 'schedule', name : 'meetingProposal', col : 3, subRow : true },
            { title : '샘플 제안/발송', type : 'schedule', name : 'sampleInform', col : 3, subRow : true },
            { title : '샘플 확정', type : 'schedule', name : 'sampleConfirm', col : 3, subRow : true },
            { title : '영업 확정서/ 계약서', type : 'schedule', name : 'salesConfirmation', col : 3, subRow : true },
            { title : '고객발주 (아소트 확정)', type : 'schedule', name : 'assortConfirm', col : 3, subRow : true },
            { title : '사양서 확정', type : 'schedule', name : 'orderConfirm', col : 3, subRow : true }
        ],
        hold : [
            { title : '진행타입', type : 's', name : 'bidType2Kr', col : 4,  class : '' },
            { title : '진행상태', type : 'c', name : 'projectStatusKr', col : 4, rowspan : true },
            { title : '프로젝트타입', type : 's', name : 'projectTypeKr', col : 5,  class : '' },
            { title : '업종', type : 'c', name : 'pBizName', col : 9,  class : 'ta-l pdl5 font-11' },
            { title : '고객명', type : 'c', name : 'customerName', col : 0,  class : 'ta-l pdl5' },
            { title : '영업담당자', type : 'manager', name : 'salesManagerSno', col : 5,  class : '' },
            { title : '영업 메모', type : 'c', name : 'salesMemo', col : 15,  class : 'pdl5 ta-l font-11' },
            { title : '유찰/보류 내용', type : 'c', name : 'holdMemo', col : 15,  class : 'pdl5 ta-l font-11' },
            { title : 'TM/EM<br>영업 내역', type : 'c', name : 'tmList', col : 4,  class : '' },
            { title : '등록일', type : 'd2s', name : 'regDt', col : 4,  class : '' },
            { title : '등록자', type : 'c', name : 'regManagerNm', col : 4,  class : '' }
        ]
    };

    const getSalesField = (type)=>{
        return $.setColWidth(95, salesListField[type]);
    };
</script>
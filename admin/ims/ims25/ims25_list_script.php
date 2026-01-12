<script type="text/javascript">
    /** 타이틀 설정 */
    $('title').html('프로젝트 관리'); //전체 리스트

    /** 검색 설정 */
    const mainListPrefix = 'project';
    const allListSearchDefaultData = {
        orderProgressChk : ['15','20','30','31','40','41','50','60'],
        excludeStatus : [], //제외
        sort : "P5,asc",
        delayStatus : [],
        bizPlanYn : 'all',
        designWorkType : 'all',
        pageNum : 100,
        isBookRegistered : '0',
        projectTypeChk : [0,2,6,8,5],
        //viewType : 'project',
        searchDateType : 'prj.regDt',
        startDt : '',
        endDt : '',
        bidType2 : 'all',
    };

    /** 기본 갱신 함수 */
    const getListData = (params, listPrefix)=>{
        //console.log('리스트 갱신 함수 실행', params);
        return $.imsPost('getIms25AllList', params);
    };

    /** 리스트 개별 데이터 정의 */
    const eachListData = {
        parentCateList : [],
        cateList : [],
    };
    /** 리스트 개별 메소드 정의 */
    const eachListMethod = {};
    /** 리스트 개별 종합 계산 정의 */
    const eachListComputed = {};

    $(()=>{
        /* 마운트 액션 */
        const listFoundation = getIms25ListFoundationData(()=>{
            getListAfter();
            setCommentMap();
        });
        /* 마운트 액션 */
        ImsBoneService.setMounted(listFoundation.serviceData, ()=>{
            ImsCustomerService.setBizCateSearch(vueApp, 'parentCateList', 'cateList');
        });
        listFoundation.listService.init(listFoundation.serviceData);
    });
</script>

<script type="text/javascript">
    const listField = [
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
        { title : '사전기획', type : 'schedule', name : 'salesReadyPlan', col : 3, class : 'bg-light-yellow' },
        { title : '미팅/입찰설명회', type : 'schedule', name : 'meeting', col : 3, class : 'bg-light-yellow' },
        { title : '제안', type : 'schedule', name : 'meetingProposal', col : 3, class : 'bg-light-yellow' },
        { title : '샘플 제안/발송', type : 'schedule', name : 'sampleInform', col : 3, class : 'bg-light-yellow' },
        { title : '발주', type : 'schedule', name : 'productionOrder', col : 3, class : 'bg-light-yellow' },
        // 완료 (두번째 TR) ######################################################
        { title : '기획', type : 'schedule', name : 'salesReadyPlan', col : 3, subRow : true },
        { title : '미팅/입찰설명회', type : 'schedule', name : 'meeting', col : 3, subRow : true },
        { title : '제안', type : 'schedule', name : 'meetingProposal', col : 3, subRow : true },
        { title : '샘플 제안/발송', type : 'schedule', name : 'sampleInform', col : 3, subRow : true },
        { title : '발주', type : 'schedule', name : 'productionOrder', col : 3, subRow : true }
    ];
    const getListField = ()=>{
        return $.setColWidth(95, listField);
    };
</script>
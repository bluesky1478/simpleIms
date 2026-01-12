<script type="text/javascript">
    const sno = '<?=gd_isset($requestParam['sno'],$requestParam['projectSno'])?>'; //프로젝트 번호
    const allScheduleMap = JSON.parse('<?=$allScheduleMap?>');

    let styleTabMode = '<?=empty($requestParam['styleTabMode']) || 'undefined' == $requestParam['styleTabMode'] ?'basic':$requestParam['styleTabMode']?>';
    if(!$.isEmpty($.cookie('viewTabMode')) && 'y' === '<?=$imsPageReload?>'){
        styleTabMode = $.cookie('viewStyleTabMode');
    }else{
        $.cookie('viewStyleTabMode', styleTabMode); //Reload가 아닐 때는 넘어온 것으로 쿠키 셋팅
    }

    //부서에 다른 스케쥴 디폴트
    const SCHEDULE_TAB_MAP = {
        '02001001' : 'sales',
        '02001002' : 'design',
        '02001004' : 'qc',
    };
    let scheduleTabMode = $.isset(SCHEDULE_TAB_MAP['<?=$teamSno?>'],'summary'); //자기 부서에 맞게 스케쥴 기본 셋팅


    $(appId).hide();

    $(()=>{
        const serviceData = {};

        const vueAppFileList = {};
        fileList.forEach((fileType)=>{
            vueAppFileList[fileType] = {
                files : []
            }
        });

        //View Data
        ImsBoneService.setData(serviceData,{

            //QB 레이어
            qbLayer: {
                visible: false,
                loading: false,
                data: {}
            },

            //이메일 발송 레이어
            emailPopVisible: false,
            emailPopConfig: {
                type: '',
                receiver: '',
                email: '',
                fileUrl: '',
                initialData: {}
            },

            //발송이력 레이어
            sendHistoryVisible: false,
            sendHistoryType: '',

            //## TAB 관련
            //tabMode : 'design', //밑에서 들어오는 상태에 따라 분기한다.
            tabMode : 'main',
            //tabMode : 'main',
            styleTabMode : 'basic',
            scheduleConfig : [],
            scDeadLineField : 'remain150',
            scheduleTabMode : scheduleTabMode,  //현재 스케쥴 탭
            //고객 정보
            customer : {sno : -1, customerName : 'Loading...'},
            //TM 이력
            tmList : [],
            //고객 코멘트 정보
            <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueTypeKey => $issueType){ ?>
            <?=$issueTypeKey?>List : [], //이슈
            <?php } ?>

            //고객사 담당자수
            cntCustomerContact : 2,

            //수정 상태 정보
            isModify : false, //기본 수정
            isSalesModify : false, //영업 수정

            //추가 참여자 등록 레이어 팝업
            chkSchedule : [], //선택 스케쥴 리스트
            layerAddMember : $.copyObject(layerAddMemberDefault),

            //디자인팀 참여 여부 ( 아래 array computed 계산 )
            designTeamJoinListSrc : JSON.parse('<?=json_encode(\Component\Ims\ImsCodeMap::DESIGN_JOIN_TYPE)?>'),

            //스타일 관련
            showStyle : true,
            isStyleModify : false,
            productField : [],
            productList : [],
            workOrderCompleteCnt : 0,
            styleCalcInfo : {
                totalCurrentPrice : 0,
                totalTargetPrice : 0,
                totalPrice : 0,
                totalTargetCost : 0,
                totalCost : 0,
            },

            //Legacy ... ▼
            styleTotal : 0,
            styleTotalCost : 0,
            styleTotalPrice : 0,
            styleTotalEstimate : 0,
            //Legacy ... ▲

            //업종
            parentBizCateList : ['상위업종 선택'],
            bizCateList : [],
            parentBizCateName : '상위업종 선택',

            //결재 정보
            projectApprovalInfo : {
                'salesPlan' : {sno:-1}, //영업 기획서
                'plan' : {sno:-1}, //기획
                'proposal' : {sno:-1}, //제안
                'cost' : {sno:-1}, //생산가
                'salePrice' : {sno:-1}, //판매가
            },

            //파일 리스트
            fileList : vueAppFileList,

            //코멘트
            commentMap : [],

            //Layer Popup ( Legacy TODO : 변경하기 )
            visibleOrderSendUrl : false,
            visibleAssortSendUrl : false,
            visibleProposalSendUrl : false,
            visibleOrderCondition : false,
            visibleSamplePopup : false,
            visibleWorkOrderPossibleStatus : false,

            //스타일 기획 리스트 설정
            stylePlanList : null,
            bFlagCallProjectDetail : true,

            //고객 코멘트 관련
            <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueTypeKey => $issueType){ ?>
            <?=$issueTypeKey?>List: [], //이슈
            <?php } ?>
            issueShowList : 'all',

            //견적 리스트
            customerEstimateList : [],

            //고객 담당자 리스트
            customerContactList : [],

            //TO-DO 리스트
            todoRequestSearchCondition : {
                'projectSno' : sno,
                'todoType' : 'todo',
                'pageNum' : 1000,
                'sort' : 'D,desc',
                'status' : '',
            },
            todoRequestList : [],

            //영업 기획서
            salesPlan : {},

        });

        //Methods : 1. ims25_view_script_method , 2. this file
        ImsBoneService.setMethod(serviceData,{...viewMethods,...viewMethods2});

        //Mounted
        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            
            //스케쥴 데드라인 설정
            setScheduleDeadLine();

            //디자인 JOIN TYPE 변경
            //console.log('Mounted designTeamJoinListSrc ', vueApp.designTeamJoinListSrc);
            Object.keys(vueApp.designTeamJoinListSrc)
                .map(k => Number(k))
                .filter(v => v > 0)
                .sort((a, b) => a - b)
                .map(v => ({ value: v, label: vueApp.designTeamJoinListSrc[String(v)] }));
            //console.log( vueApp.designTeamJoinListSrc );

            //고객정보 가져오기
            refreshCustomer().then(()=>{
                //업종 가져오기
                ImsCustomerService.setBizCate(vueApp, 'parentBizCateName', 'parentBizCateList', 'bizCateList');
            });

            //스타일 정보 가져오기.
            refreshProductList(vueApp.mainData.sno);
            //EM 기록 가져오기
            ImsCustomerService.getTmHistory(vueApp.mainData.customerSno).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    //console.log('Tm기록',data);
                    vueApp.tmList = data;
                });
            });
            //고객 코멘트 가져오기
            refreshCustComment(vueApp.mainData.customerSno);

            //결재 갱신
            refreshProjectApproval();

            //파일 셋팅
            setProjectFiles(vueInstance);

            //코멘트 가져오기
            refreshComment();

            //단계별 탭변경
            vueApp.tabMode = STEP_MAP[vueApp.mainData.projectStatus]['tabMode'];

            //영업 기획서 가져오기
            ImsNkService.getList('basicFormToSalesPlanPage', {'projectSno':sno}).then((data)=> {
                $.imsPostAfter(data, (data) => {
                    console.log('영업 기획서 데이터', data);
                    vueApp.salesPlan = data;
                });
            });


        });

        //Computed 셋팅
        ImsBoneService.setComputed(serviceData, Object.assign({
                //스타일 정보 계산
                computedStyle() {
                    const calc = this.styleCalcInfo;
                    calc.totalCurrentPrice = 0;
                    calc.totalTargetPrice = 0;
                    calc.totalPrice = 0;
                    calc.totalTargetCost = 0;
                    calc.totalCost = 0;
                    this.productList.forEach((prd)=>{
                        calc.totalCurrentPrice += (prd.currentPrice * prd.prdExQty)
                    });
                    if( calc.totalCurrentPrice > 0 ){
                        this.mainData.extAmount = calc.totalCurrentPrice; //추정 매출 변경 (기존에 글로 썼어도 계산이 우선함)
                    }
                    return true;
                }
            },
            makeBitmaskComputed({
                key: 'designTeam',
                srcKey: 'designTeamJoinListSrc',
                valuePath: 'mainData.designTeamInfo',
                emptyText: '-'
            }),
            COMPUTED_DEAD_LINE
        ));

        //Ref : v2 ImsProjectService.getSimpleProject ( decorationSimpleProject , decorationProjectCommon )
        ImsBoneService.serviceStart('getSimpleProject',{sno:sno}, serviceData);

    });
</script>

<script type="text/javascript">
    const STEP_MAP = {
        10 : { //영업대기
            'before' : null,
            'after' : {'status': 15,'name':'사전영업'},
            'desc' : '영업 대기 프로젝트',
            'tabMode' : 'sales',
        },
        15 : { //사전영업
            'before' : {'status': 10,'name':'영업대기'},
            'after' : {'status': 20,'name':'기획'},
            'desc' : '디자인 기획 전 사전 영업 / 기초정보 수집 단계',
            'tabMode' : 'sales',
        },
        20 : { //기획
            'before' : {'status': 15,'name':'사전영업'},
            'after' : {'status': 30,'name':'제안'},
            'desc' : '제안 전 디자인 기획 단계 (기초 원단/단가 확인)',
            'tabMode' : 'main',
        },
        30 : { //제안
            'before' : {'status': 20,'name':'기획'},
            'after' : {'status': 31,'name':'제안서 확정대기'},
            'desc' : '고객 제안서 작성 단계',
            'tabMode' : 'main',
        },
        31 : { //제안서 확정대기
            'before' : {'status': 30,'name':'제안'},
            'after' : {'status': 40,'name':'샘플'},
            'desc' : '고객 제안 후 대기 상태',
            'tabMode' : 'main',
        },
        40 : { //샘플 ( TODO 샘플 진행 여부에 따라 즉시 발주준비 단계로 이동 )
            'before' : {'status': 31,'name':'제안서 확정대기'},
            'after' : {'status': 41,'name':'고객샘플 확정대기'},
            'desc' : '샘플 제작 단계',
            'tabMode' : 'main',
        },
        41 : { //고객샘플 확정대기
            'before' : {'status': 40,'name':'샘플'},
            'after' : {'status': 50,'name':'발주준비'},
            'desc' : '샘플 제안 후 계약/발주 대기 단계',
            'tabMode' : 'main',
        },
        50 : { //발주준비
            'before' : {'status': 41,'name':'고객샘플 확정대기'},
            'after' : {'status': 60,'name':'발주'},
            'desc' : '생산처 발주서(작지)/고객 사양서 생성 단계',
            'tabMode' : 'main',
        },
        60 : { //발주
            'before' : {'status': 50,'name':'발주준비'},
            'after' : {'status': 90,'name':'발주완료'},
            'desc' : '발주 정보 확정 및 생산처 발주 단계',
            'tabMode' : 'main',
        },
        90 : { //발주완료
            'before' : {'status': 60,'name':'발주'},
            'after' : {'status': 91,'name':'프로젝트 종결'},
            'desc' : '생산처 발주 완료 단계',
            'tabMode' : 'main',
        },
        91 : { //종결
            'before' : {'status': 90,'name':'발주완료'},
            'after' : null,
            'desc' : '',
            'tabMode' : 'main',
        },
        97 : { //영업 보류
            'before' : {'status': 97,'name':'영업보류'},
            'after' : null,
            'desc' : '',
            'tabMode' : 'sales',
        },
        98 : { //유찰
            'before' : {'status': 98,'name':'유찰'},
            'after' : null,
            'desc' : '',
            'tabMode' : 'sales',
        },
    };
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript">
    const sno = '<?=gd_isset($requestParam['sno'],$requestParam['projectSno'])?>';

    let styleTabMode = '<?=empty($requestParam['styleTabMode']) || 'undefined' == $requestParam['styleTabMode'] ?'basic':$requestParam['styleTabMode']?>';
    if(!$.isEmpty($.cookie('viewTabMode')) && 'y' === '<?=$imsPageReload?>'){
        styleTabMode = $.cookie('viewStyleTabMode');
    }else{
        $.cookie('viewStyleTabMode', styleTabMode); //Reload가 아닐 때는 넘어온 것으로 쿠키 셋팅
    }
    
    //let scheduleTabMode = 'design'; //자기 부서에 맞게 스케쥴 기본 셋팅
    //let scheduleTabMode = 'sales'; //자기 부서에 맞게 스케쥴 기본 셋팅
    let scheduleTabMode = 'all'; //자기 부서에 맞게 스케쥴 기본 셋팅

    //sales .
    //design
    //qc
    //summary
    //detail

    $(appId).hide();

    //0722작성. 기성복일때 생산가관리 정보저장시 생산가 수정하기. 생산가 입력팝업에서 실행함
    function updatePrdCost(iStyleSno, iAmt) {
        $.each(vueApp.productList, function(key, val) {
            if (val.sno == iStyleSno) {
                val.prdCost = iAmt;
                val.estimateCost = iAmt;
                return false;
            }
        });
    }
    //스타일기획 리스트 새로고침
    function listRefreshStylePlan(){
        ImsNkService.getStylePlanListRefresh(0, sno, 'calcPlanPerStyle');
    }
    function calcPlanPerStyle() {
        let oCntPlanPerStyle = {};
        $.each(vueApp.productList, function (key ,val) {
            oCntPlanPerStyle[val.sno] = 0;
        });
        $.each(vueApp.stylePlanList, function (key ,val) {
            oCntPlanPerStyle[val.styleSno]++;
        });
        $.each(vueApp.productList, function (key ,val) {
            val['planCnt'] = oCntPlanPerStyle[val.sno];
        });
    }
    //고객사 담당자정보 덮어쓰기
    function overwriteCustomerContact(oObj) {
        vueApp.customer.contactName = oObj.cContactName;
        vueApp.customer.contactPosition = oObj.cContactPosition;
        vueApp.customer.contactMobile = oObj.cContactMobile;
        vueApp.customer.contactPreference = oObj.cContactPreference;
        vueApp.customer.contactEmail = oObj.cContactEmail;
    }

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            designManager : '',
            popProduct : {
                'sampleFile1Exsists' : 'n',
                'sampleFile4Exsists' : 'n',
                'sampleFile6Exsists' : 'n',
                'sampleConfirmSno' : 0,
            },

            styleBatchInfo : {
                year : '',
                season : '',
                customerDeliveryDt : '',
                msDeliveryDt : '',
            },

            scheduleLoad : false,
            workOrderCompleteCnt : 0,

            currentStatus : 20,
            batchEstimateFactory : 0,

            initSalesStatus : '',
            isDev : false,

            <?php if('02001002' === $teamSno) { ?>
            isViewDetail : true,
            <?php }else{ ?>
            isViewDetail : false,
            <?php } ?>

            simpleLayerPrd : false,
            simpleLayerPrdData : null,

            //Layer Popup
            visibleOrderSendUrl : false,
            visibleAssortSendUrl : false,
            visibleProposalSendUrl : false,
            visibleOrderCondition : false,
            visibleSamplePopup : false,
            visibleWorkOrderPossibleStatus : false,

            syncAssortType : 'y', //아소트 타입(구분) 동시 수정 여부
            assortTotalCnt : 0,
            assortModify: false,

            styleTotal : 0,
            styleTotalCost : 0,
            styleTotalPrice : 0,
            styleTotalEstimate : 0,

            isSetCommentEditor : false,
            isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,

            isModify : false,
            isStyleModify : false,
            isStyleDetail : 'n',
            isModifyAddedSale : false, //부가판매 등록/수정 y/n
            isModifyAddedBuy : false, //부가구매 등록/수정 y/n

            isScheduleDetail : false, //일반 스케쥴 상세
            //isScheduleModify : false, //일반 스케쥴 수정
            isQbDetail : false, //QB스케쥴 상세

            batchSeason : '',
            batchStyleProcType : 0,
            batchCustSampleType : 0,

            //tabMode : 'design', //밑에서 들어오는 상태에 따라 분기한다.
            tabMode : 'sales',

            styleTabMode  : styleTabMode,
            scheduleTabMode  : scheduleTabMode, //FIXME

            fileTabMode : 'plan',

            commentShowCnt : 4,
            commentInitShowCnt : 4,

            issueShowList : 'all',
            isDetail : '',
            showStyle  : true,
            viewMode   : '',

            viewProductList : [],

            customer : {sno : -1},
            project  : {sno : -1},
            productList : [],
            sampleList : [],
            customerEstimateList : [],
            custInfo : [],
            addedSaleList : [],
            addedBuyList : [],

            //코멘트 리스트
            commentList : [],

            //TO-DO 리스트
            todoRequestSearchCondition : {
                'projectSno' : sno,
                'todoType' : 'todo',
                'pageNum' : 1000,
                'sort' : 'D,desc',
                'status' : '',
            },
            todoRequestList : [],

            //고객 코멘트
            <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueTypeKey => $issueType){ ?>
            <?=$issueTypeKey?>List : [], //이슈
            <?php } ?>

            //파일 리스트
            fileList : [],

            //결재 정보
            projectApprovalInfo : {
                'plan' : {sno:0}, //기획
                'proposal' : {sno:0}, //제안
                'cost' : {sno:0}, //생산가
                'salePrice' : {sno:0}, //판매가
            },
            //프로젝트 정산정보
            iProjectOriginAmount : 0,
            iProjectSaleAmount : 0,
            iProjectMargin : 0,
            //프로젝트 최초기획
            bFlagExistPlanSche : false,
            isModifyPlan : false,
            oPlanScheTypeHan : {
                <?php foreach( \Component\Ims\NkCodeMap::PROJECT_PLAN_SCHE_TYPE as $key => $val ){ ?>
                '<?=$key?>' : '<?=$val?>',
                <?php } ?>
            },
            oPlanScheList : {},
            //스타일기획 리스트
            stylePlanList : [],
            bFlagCallProjectDetail : true,
            //고객사 담당자수
            cntCustomerContact : 0,

            //샘플리스트 -> 샘플위치 관련
            isModifySampleLocation : false,
            chooseSampleSno : 0,
            chooseSampleIdx : 0,
            chooseSampleLocationIdx : -1,
            sampleLocationInfoDefault : {'locationDt': '', 'locationStart': '', 'locationEnd': '', 'locationReceiver': '', 'locationTel': '', 'locationAddr': '', 'locationMethod': '', 'locationMemo': ''},
            sampleLocationInfo : {'locationDt': '', 'locationStart': '', 'locationEnd': '', 'locationReceiver': '', 'locationTel': '', 'locationAddr': '', 'locationMethod': '', 'locationMemo': ''},


        });

        ImsBoneService.setMethod(serviceData,viewMethods);

        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            //프로젝트 파일 리스트 가져오기.
            ImsProjectService.getProjectFile(sno).then((data)=>{
                vueInstance.fileList = data.data;
                for(let idxKey in vueInstance.fileList){
                    //console.log(idxKey, vueInstance.fileList[idxKey].files);
                    if( null === vueInstance.fileList[idxKey].files  ){
                        vueInstance.fileList[idxKey].files = [];
                    }
                }
                vueApp.$nextTick(()=>{
                    //Dropzone Setting.
                    $('.set-dropzone').addClass('dropzone');
                    $('.set-dropzone').addClass('set-dropzone-type1');

                    const fileList = [
                        'filePlan', //기획서
                        'fileProposal', //제안서
                        'fileBarcode', //3PL바코드
                        'filePacking', //분류패킹
                        'fileDeliveryPlan', //납품계획 파일
                        'fileDeliveryReport', //납품보고서
                        'fileEtc2', //견적서
                        'fileEtc4', //영업확정서
                        'fileEtc5', //근무환경조사자료
                        'fileMeeting', //입찰추가정보
                        'fileEtc7', //기타파일
                    ];
                    fileList.forEach((fileDiv)=>{
                        ImsService.setDropzone(vueInstance, fileDiv, uploadAction); //기획서
                    });
                    console.log('dropzone set.', vueInstance);
                });
            });
        });

        ImsBoneService.setComputed(serviceData,{
            computed_calc_project_account() {
                this.iProjectOriginAmount = 0;
                this.iProjectSaleAmount = 0;
                this.iProjectMargin = 0;

                let iSumOrigin = 0;
                let iSumSale = 0;
                let oProject = this.project;
                $.each(this.productList, function (key, val) {
                    //생산가(원가) 조건에 따라 다른 컬럼값
                    if (Number(oProject.projectType) === 4 || Number(val.prdCostConfirmSno) > 0) iSumOrigin += Number(val.prdCost) * Number(val.prdExQty);
                    else if (Number(val.estimateConfirmSno) > 0) iSumOrigin += Number(val.estimateCost) * Number(val.prdExQty);
                    else iSumOrigin += Number(val.targetPrdCost) * Number(val.prdExQty);
                    //판매가 조건에 따라 다른 컬럼값
                    if (oProject.prdPriceApproval === 'p' || Number(val.salePrice) > 0) iSumSale += Number(val.salePrice) * (Number(val.prdExQty)-Number(val.msQty));
                    else iSumSale += Number(val.targetPrice) * (Number(val.prdExQty)-Number(val.msQty));
                });
                $.each(this.addedSaleList, function (key, val) {
                    iSumOrigin += Number(val.addedBuyAmount)*Number(val.addedQty);
                    iSumSale += Number(val.addedSaleAmount)*Number(val.addedQty);
                });
                $.each(this.addedBuyList, function (key, val) {
                    iSumOrigin += Number(val.addedBuyAmount)*Number(val.addedQty);
                });

                this.iProjectOriginAmount = iSumOrigin;
                this.iProjectSaleAmount = iSumSale;
                if (iSumSale > 0) this.iProjectMargin =  Math.round((iSumSale - iSumOrigin) / iSumSale * 100 * 100) / 100;

                return this.iProjectOriginAmount;
            },
            assortTotal() {
                this.assortTotalCnt = 0;
                this.styleTotal = 0; //수량
                this.styleTotalPrice = 0;
                this.styleTotalCost = 0;
                this.styleTotalEstimate = 0;
                for(let prdIdx in this.productList){
                    const prd = this.productList[prdIdx];
                    this.productList[prdIdx].assortTotal = 0;
                    this.productList[prdIdx].optionTotal = {};
                    this.productList[prdIdx].assortMsTotal = 0;

                    const optionList = this.productList[prdIdx].specOptionList;
                    if(!$.isEmpty(optionList)){
                        optionList.forEach((size)=>{
                            this.productList[prdIdx].optionTotal[size] = 0;
                        });

                        if( !$.isEmpty(this.productList[prdIdx].assort) ){
                            this.productList[prdIdx].assort.forEach((assort)=>{
                                assort.total = 0;
                                for(let assortIdx in assort.optionList){

                                    const optionCnt = Number(assort.optionList[assortIdx]);
                                    this.productList[prdIdx].optionTotal[assortIdx] += optionCnt;

                                    this.productList[prdIdx].assortTotal += Number(assort.optionList[assortIdx]);
                                    if (assort.qtyType == '미청구') this.productList[prdIdx].assortMsTotal += Number(assort.optionList[assortIdx]);
                                    this.assortTotalCnt += Number(assort.optionList[assortIdx]);
                                    assort.total += Number(assort.optionList[assortIdx]);
                                }
                            });
                        }

                        this.styleTotal += prd.prdExQty;
                        this.styleTotalPrice += (prd.salePrice*prd.prdExQty);
                        this.styleTotalCost += (prd.prdCost*prd.prdExQty);
                        this.styleTotalEstimate += (prd.estimateCost*prd.prdExQty);
                    }
                }
                return this.assortTotalCnt;
            },
        });

        //시작.
        $.imsPost2('getSimpleProject',{sno:sno},(data)=>{
            //File..
            /*ImsProjectService.getProjectFile(sno).then((data)=>{
                vueApp.fileList = data.data;
                console.log('file set . ');
            });*/

            ImsBoneService.serviceBeginCommon(serviceData,data);

            //상태 셋팅
            vueApp.currentStatus = data.projectStatus;
            currentStatus = data.projectStatus;

            //최초 로딩시 영업 상태
            vueApp.initSalesStatus = data.salesStatus;

            //프로젝트 정보
            vueApp.project = $.copyObject(data);

            //고객 정보 가져오기.
            ImsCustomerService.getData(data.customerSno).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.customer = data;
                    $('title').html(  vueApp.project.sno  + '_' + vueApp.customer.customerName );
                    setCustInfo();
                });
            });

            //고객사 담당자 구해오기
            ImsNkService.getList('customerContact', {'customerSno':data.customerSno}).then((data)=>{
                if(200 === data.code) {
                    vueApp.cntCustomerContact = data.data.list.length;
                }
            });

            //스타일 정보 가져오기.
            refreshProductList(sno);

            //고객 코멘트 정보 가져오기.
            refreshCustComment(vueApp.project.customerSno);

            //To-Do 리스트 가져오기
            ImsTodoService.getListTodoRequest(1);

            //코멘트 가져오기
            $.imsPost2('getProjectCommentList',{
                projectSno : sno
            }).then((data)=>{
                vueApp.commentList = data.data;
            });

            //샘플갱신
            refreshSampleList();

            //견적갱신
            refreshEstimateList();
 
            //결재 정보 갱신
            Object.keys(vueApp.projectApprovalInfo).forEach(key=>{
                ImsTodoService.getApprovalData(key, vueApp.project.sno, 0, 0).then((data)=>{
                    //console.log(`${key} 결재 데이터`, data);
                    vueApp.projectApprovalInfo[key] = $.copyObject(data);
                    vueApp.$forceUpdate();
                    setTimeout(()=>{
                        vueApp.scheduleLoad = true;
                    },100);
                });
            });

            //부가 판매/매입정보 가져오기
            vueApp.getAddedBSList(vueApp.project.sno);
            //최초기획일정 가져오기
            //vueApp.getProjectPlanScheList(vueApp.project.sno);

            //스타일기획리스트 가져오기
            //listRefreshStylePlan();

        });

    });
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript">
    const sno = '<?=gd_isset($requestParam['sno'],$requestParam['projectSno'])?>';
    let tabMode = '<?=empty($requestParam['tabMode']) || 'undefined' == $requestParam['tabMode'] ?'customer':$requestParam['tabMode']?>';
    let styleTabMode = '<?=empty($requestParam['styleTabMode']) || 'undefined' == $requestParam['styleTabMode'] ?'basic':$requestParam['styleTabMode']?>';

    if(!$.isEmpty($.cookie('viewTabMode')) && 'y' === '<?=$imsPageReload?>'){
        tabMode = $.cookie('viewTabMode'); //Reload일 때.
        styleTabMode = $.cookie('viewStyleTabMode');
    }else{
        $.cookie('viewTabMode', tabMode); //Reload가 아닐 때는 넘어온 것으로 쿠키 셋팅
        $.cookie('viewStyleTabMode', styleTabMode); //Reload가 아닐 때는 넘어온 것으로 쿠키 셋팅
    }
    //console.log('입력 탭모드는 : ', '<?=$requestParam['tabMode']?>');
    //console.log('이전 탭모드는 : ', tabMode);
    //console.log('Reload 여부 : ', '<?=$imsPageReload?>');

    $(appId).hide();

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            currentStatus : 20,
            batchEstimateFactory : 0,

            initSalesStatus : '',
            //isDev : <?=$isDev?'true':'false'?>,
            isDev : false,

            simpleLayerPrd : false,
            simpleLayerPrdData : null,
            visibleOrderSendUrl : false,
            visibleAssortSendUrl : false,
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

            isScheduleDetail : false, //일반 스케쥴 상세
            //isScheduleModify : false, //일반 스케쥴 수정
            isQbDetail : false, //QB스케쥴 상세

            batchSeason : '',
            batchStyleProcType : 0,
            batchCustSampleType : 0,

            tabMode : 'sales', //밑에서 들어오는 상태에 따라 분기한다.
            styleTabMode  : styleTabMode,

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

        });

        ImsBoneService.setMethod(serviceData,viewMethods);

        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            //프로젝트 파일 리스트 가져오기.
            ImsProjectService.getProjectFile(sno).then((data)=>{
                vueInstance.fileList = data.data;
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
            assortTotal() {
                this.assortTotalCnt = 0;
                this.styleTotal = 0; //수량
                this.styleTotalPrice = 0;
                this.styleTotalCost = 0;
                this.styleTotalEstimate = 0;
                for(let prdIdx in this.productList){
                    const prd = this.productList[prdIdx];
                    this.productList[prdIdx].assortTotal = 0;

                    if( !$.isEmpty(this.productList[prdIdx].assort) ){
                        this.productList[prdIdx].assort.forEach((assort)=>{
                            for(let assortIdx in assort.optionList){
                                this.productList[prdIdx].assortTotal += Number(assort.optionList[assortIdx]);
                                this.assortTotalCnt += Number(assort.optionList[assortIdx]);
                            }
                        });
                    }

                    this.styleTotal += prd.prdExQty;
                    this.styleTotalPrice += (prd.salePrice*prd.prdExQty);
                    this.styleTotalCost += (prd.prdCost*prd.prdExQty);
                    this.styleTotalEstimate += (prd.estimateCost*prd.prdExQty);
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

            console.log('탭모드확인2 : ', tabMode);
            vueApp.changeTab(tabMode);

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
                    setCustInfo();
                });
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
                });
            });

        });
    });
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>

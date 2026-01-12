<script type="text/javascript">
    let currentStatus = 0;

    const styleMap = {};
    <?php foreach($codeStyle as $codeKey => $code){ ?>
    styleMap['<?=$codeKey?>'] = '<?=$code?>';
    <?php } ?>

    const scheduleMap = JSON.parse('<?=json_encode(\Component\Imsv2\ImsScheduleUtil::getScheduleMap())?>');
    //console.log('스케쥴맵', scheduleMap);

    const styleEtcListMap = {
        'prd003': '스타일 선호도',
        'prd004': '원단 선호도',
        'prd005': '부자재 선호도',
        'prd006': '인쇄 형태 선호도',
        'prd007': '기능 선호도',
        'prd008': '불편사항',
        'prd001': '기타/비고',
    };

    const fileList = [
        'fileSalesStrategy', //영업 전략
        'fileMeetingReport', //고객사 회의록
        'fileSampleGuide', //샘플 안내서
        'filePlan', //기획서
        'fileProposal', //제안서
        'filePre1', //사전 - 디자인 제안서
        'filePre2', //사전 - 개선 제안서
        'filePre3', //사전 - 선호도 조사
        'filePre4', //사전 - 샘플 테스트
        'fileBarcode', //3PL바코드
        'filePacking', //분류패킹
        'fileEtc7', //기타파일
        'fileDeliveryPlan', //납품계획 파일
        'fileDeliveryReport', //납품보고서
        /*
        'fileEtc2', //견적서
        'fileEtc4', //영업확정서
        'fileEtc5', //근무환경조사자료
        'fileMeeting', //입찰추가정보
        */
    ];

    function scrollToAndHighlight(id) {
        $('#'+id).focus();
        const el = document.getElemen
        tById(id);
        if (el) {
            el.classList.remove('highlight'); // 재적용을 위해 초기화
            void el.offsetWidth; // 강제로 리플로우 발생
            el.classList.add('highlight');
        }
    }

    function scrollToTarget(targetId) {
        const target = document.getElementById(targetId);
        if (target) {
            // 화면에 스크롤
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // 요소에 포커스
            target.focus();
            // 포커스 이동
            setTimeout(() => {
                target.focus();
            }, 1); // 스크롤 완료 후 포커스 이동
        }
    }

    const setApprovalReset = (approvalType)=>{
        $.msgConfirm('PASS 상태를 취소 합니다.','계속하려면 확인을 눌러주세요').then(function(result){
            if( result.isConfirmed ){
                $.imsPost2('setApproval',{
                    'projectSno'   : sno,
                    'approvalType' : approvalType+'Confirm',
                    'status' : 'n', //승인처리
                    'approvalMemo' : approvalType+'Memo',
                    'memo' : '',
                },()=>{
                    location.reload();
                });
            }
        });
    };

    /**
     * 업로드 후 처리
     * @param tmpFile
     * @param dropzoneId
     */
    const uploadAction = (tmpFile, dropzoneId)=>{
        const saveFileList = [];
        tmpFile.forEach((value)=>{
            saveFileList.push(value);
        });
        let promptValue = '';
        if( 'filePacking' !== dropzoneId && 'fileBarcode' !== dropzoneId ){
            promptValue = window.prompt("메모입력 : ");
        }
        $.imsPost('saveProjectFiles',{
            saveData : {
                projectSno : sno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            if(200 === data.code){
                refreshProject(sno); //프로젝트만 개별 갱신
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
                $.msg('등록완료','','success');
            }
        });
    }

    /**
     * 체크 상품 정보
     * @returns {[]}
     */
    const getCheckedPrd = ()=>{
        const prdList = [];
        const prdSnoList = [];
        let isContinue = false;
        $('input[name="prdSno"]:checked').each(function(){
            prdSnoList.push($(this).val());
            isContinue = true;
        });
        if( !isContinue ){
            $.msg('적용할 스타일을 선택해주세요','','warning');
        }
        prdSnoList.forEach((idx)=>{
            // 인덱스를 통해 직접 접근하므로 sno가 없는 신규 상품도 정확히 매칭됩니다.
            const targetItem = vueApp.productList[idx];
            if (targetItem) {
                prdList.push(targetItem);
            }
        });
        return prdList;
    };

    const setScheduleDeadLine = ()=>{
        //스케쥴 데드라인 정보 가져오기
        $.imsPostWithoutPreload('getScheduleConfig',{}).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.scheduleConfig = data;
            });
        });
    };

    const COMPUTED_DEAD_LINE = {
        computedDeadLine() {
            if(!$.isEmptyAll(this.mainData) && !$.isEmptyAll(this.mainData.salesStartDt) && !$.isEmptyAll(this.mainData.customerDeliveryDt) ){
                const deadLineField = ScheduleService.getRemainField(this.mainData.salesStartDt, this.mainData.customerDeliveryDt);
                salesStartDt = this.mainData.salesStartDt;
                customerDeliveryDt = this.mainData.customerDeliveryDt;
                for(let key in this.scheduleConfig){
                    const config = this.scheduleConfig[key];
                    if('customerDeliveryDt' === config['relationSchedule']){
                        //연관 일자가 고객 일자라면
                        config.deadLine = $.dateAdd(this.mainData.customerDeliveryDt, (config[deadLineField]*-1));
                    }else{
                        config.deadLine = $.dateAdd(this.scheduleConfig[config.relationSchedule].deadLine, (config[deadLineField]*-1));
                        if( $.diffDate(vueApp.mainData.salesStartDt, config.deadLine) > 0 ){
                            config.deadLine = vueApp.mainData.salesStartDt;
                        }
                    }
                }
            }
        }
    };


</script>

<!--레거시 함수-->
<script type="text/javascript">

    /**
     * 고객 갱신
     */
    function refreshCustomer() {
        //고객 정보 갱신
        //ImsService.getData(DATA_MAP.CUSTOMER, customerSno);
        console.time("refreshCustomer");

        const promise = $.imsPostWithoutPreload('getData',{
            mode:'getData',
            target:DATA_MAP.CUSTOMER,
            sno :vueApp.mainData.customerSno
        });
        promise.then((data)=>{
            $.imsPostAfter(data,(data)=>{
                console.timeEnd("refreshCustomer");
                vueApp.customer = data;
                $('title').html(vueApp.mainData.sno  + '_' + vueApp.customer.customerName);

                //고객 담당자 가져오기
                $.imsPostWithoutPreload('getListNk',{
                    target:'customerContact',
                    condition : {
                        mode:'getListCustomerContact',
                        customerSno:vueApp.customer.sno,
                    }
                }).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        vueApp.customerContactList = data.list;
                    });
                });

            });
        });

        return promise;
    }

    /**
     * 프로젝트 갱신
     */
    function refreshProject(sno) {
        $.imsPostWithoutPreload('getSimpleProject',{sno:sno}).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.mainData = $.copyObject(data);
            });
        });
    }

    /**
     * 상품 리스트 갱신
     */
    function refreshProductList(sno) {
        console.time("refreshProductList");
        ImsProductService.getListStyle(sno).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.productList = [];
                vueApp.workOrderCompleteCnt = 0;
                data.list.forEach((product)=>{
                    if(2 === product.workStatus){
                        vueApp.workOrderCompleteCnt++;
                    }
                    product.estimateCnt = 0;
                    product.productionPrdMoq = 0;
                    product.productionPriceMoq = 0;
                    product.fabricList = [];
                    product.fabricCnt = 0;
                    product.fabricCompleteCnt = 0;
                    product.btCompleteCnt = 0;
                    product.planCnt = 0;
                    vueApp.productList.push(product);

                    //스타일별 견적 데이터 추가
                    ImsService.getList('estimate', {'styleSno':product.sno}).then((estimateData)=>{
                        product.estimateCnt = estimateData.data.list.length;
                        if( !$.isEmpty2(product.estimateConfirmSno) && product.estimateConfirmSno > 0 ){
                            estimateData.data.list.forEach((estimate)=>{
                                if( product.estimateConfirmSno === estimate.sno ){
                                    product.productionPrdMoq = estimate.contents.prdMoq; //생산처 MOQ
                                    product.productionPriceMoq = estimate.contents.priceMoq; //생산처 단가 MOQ
                                }
                            });
                        }
                    });
                });

                //QB 데이터
                refreshQbList();

                //스타일 리스트 가져오기
                refreshStylePlanList();

            });
        });
    }

    /**
     * QB데이터 갱신 (상품 갱신 후 실행)
     */
    function refreshQbList(){
        $.imsPostWithoutPreload('getFabricList',{
            'projectSno' : sno,
            'ignoreStatus' : '5', //사용안함 제외
        }).then((data)=>{
            $.imsPostAfter(data,(fabricList)=>{
                //상품에 맞게 번호 설정
                const fabricMap = {};
                fabricList.forEach((fabric)=>{
                    if( $.isEmpty(fabricMap[fabric.styleSno]) ){
                        fabricMap[fabric.styleSno] = [];
                    }
                    fabricMap[fabric.styleSno].push(fabric);
                });
                //console.log('QB가져온 결과', fabricMap);
                vueApp.productList.forEach((prd)=>{
                    if( !$.isEmpty(fabricMap[prd.sno]) ){
                        prd.fabricList = fabricMap[prd.sno];
                        prd.fabricCnt = fabricMap[prd.sno].length;
                        fabricMap[prd.sno].forEach((fabric)=>{
                            if( 2 == fabric.fabricStatus ) prd.fabricCompleteCnt++; //수배완료
                            if( 2 == fabric.btStatus ) prd.btCompleteCnt++; //BT완료
                        });
                    }
                });
                console.timeEnd("refreshProductList");
            });
        });
    }

    /**
     * 스타일 기획 리스트 갱신 (상품 갱신 후 실행)
     */
    function refreshStylePlanList(){
        ImsNkService.getList('stylePlan', {'sno':0,'projectSno':sno}).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.stylePlanList = data.list;
                vueApp.stylePlanList.forEach((plan)=>{
                    const targetProduct = vueApp.productList.find(product => Number(product.sno) === Number(plan.styleSno));
                    if (targetProduct) {
                        targetProduct.planCnt++;
                    }
                });
            });
        });
    }

    /**
     * 결재 상태 갱신
     */
    function refreshProjectApproval(){
        Object.keys(vueApp.projectApprovalInfo).forEach(key=>{
            ImsTodoService.getApprovalData(key, vueApp.mainData.sno, 0, 0).then((data)=>{
                const approvalData = $.copyObject(data);
                if( !$.isEmpty(approvalData) && approvalData.sno > 0 ){
                    vueApp.projectApprovalInfo[key] = $.copyObject(data);
                }else{
                    vueApp.projectApprovalInfo[key] = {sno:0};
                }
            });
        });
    }

    /**
     * 코멘트 갱신
     */ 
    function refreshComment(){
        const projectSnoList = [];
        projectSnoList.push(sno);
        $.imsPostWithoutPreload('getCommentListData',{projectSnoList}).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.commentMap = data;
            });
        });
    }

    /**
     * 드롭존 셋팅
     */ 
    function setProjectFiles(vueInstance){
        //프로젝트 파일 리스트 가져오기.
        ImsProjectService.getProjectFile(sno).then((data)=>{
            vueInstance.fileList = data.data;
            for(let idxKey in vueInstance.fileList){
                if( null === vueInstance.fileList[idxKey].files  ){
                    vueInstance.fileList[idxKey].files = [];
                }
            }
            vueApp.$nextTick(()=>{
                //Dropzone Setting.
                $('.set-dropzone').addClass('dropzone');
                $('.set-dropzone').addClass('set-dropzone-type1');
                fileList.forEach((fileDiv)=>{
                    ImsService.setDropzone(vueInstance, fileDiv, uploadAction);
                });
                console.log('dropzone set.', vueInstance);
            });
        });
    }

    /**
     *  샘플리스트
     */
    function refreshSampleList(){
        ImsService.getList(DATA_MAP.SAMPLE,{
            'projectSno' : sno,
            'pageNum' : 1000,
            'sort' : 'PV_SAMPLE'
        }).then((data)=>{
            console.log('샘플리스트',data.data);
            if(200 == data.code){
                vueApp.sampleList = data.data.list;
                vueApp.$forceUpdate();
            }
        });
    }

    /**
     *  견적리스트
     */
    function refreshEstimateList(){
        ImsService.getList(DATA_MAP.CUST_ESTIMATE,{
            'projectSno' : sno,
            'pageNum' : 1000,
            'sort' : 'D,desc'
        }).then((data)=>{
            console.log('고객 견적 리스트',data.data);
            $.imsPostAfter(data,(data)=>{
                vueApp.customerEstimateList = data.list;
                vueApp.$forceUpdate();
            });
        });
    }

    /**
     * 고객 코멘트 갱신
     */
    const refreshCustComment = (customerSno) => {
        ImsService.getList(DATA_MAP.CUST_ISSUE, {customerSno:customerSno, pageNum:9999, sort:'D,desc'}).then((data)=>{
            if(200 === data.code){
                data.data.list.forEach((issueData)=>{
                    if(typeof vueApp[issueData.issueType+'List'] != 'undefined'){
                        vueApp[issueData.issueType+'List'].push(issueData);
                    }
                });
                setTimeout(()=>{
                    $('.cust-mark').append('<div style="width:3px;height:3px; background-color: #e1e1e1; position:absolute;top:10px;left:7px"></div>');
                },200);

            }else{
                console.log('고객 이슈 가져오기 error ', data.message);
            }
        });
    }

    const setCommentEditor = ()=>{

        const editorPath = '<?=PATH_ADMIN_GD_SHARE ?>script/smart';
        //코멘트.
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "editor",
            sSkinURI: editorPath + '/SmartEditor2Skin.html',
            htParams: {
                bUseToolbar: true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseVerticalResizer: true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger: true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                fOnBeforeUnload: function () {
                    $.ajax({
                        method: "GET",
                        url: "/share/editor_file_uploader.php",
                        data: {mode: 'deleteGarbage', uploadImages : uploadImages.join('^|^')},
                        cache: false,
                    }).success(function (data) {
                    }).error(function (e) {
                    });
                }
            }, //boolean
            fOnAppLoad: function () {
                //예제 코드
                //oEditors.getById["editor"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
            },
            fCreator: "createSEditor2"
        });
        vueApp.isSetCommentEditor = true;
        console.log('에디터 셋업.');
    }

</script>


<!--ims25 신규 추가 함수-->
<script type="text/javascript">
    /**
     * 프로젝트 데이터 갱신
     * 고객
     * 상품(스타일)
     */
    const refreshData=()=>{
        refreshCustomer();
        refreshProject(vueApp.mainData.sno);
        refreshProductList(vueApp.mainData.sno);
    }
</script>

<!--컴포넌트-->
<script type="text/javascript">

    /*예정일 컴포넌트*/
    const expectedTemplate = `
    <div class="w-100px">
        <div v-show="!modify" class="relative cursor-pointer hover-btn"
            @click="openProjectUnit(data.sno,type,title)">
            <span v-if='!$.isEmpty(data["ex"+$.ucfirst(type)])'>{% $.formatShortDate(data["ex"+$.ucfirst(type)]) %}</span>
            <span v-if='$.isEmpty(data["ex"+$.ucfirst(type)])'>-</span>
            <comment-cnt2 :data="data[type+'CommentCnt']"></comment-cnt2>
        </div>
        <div v-show="modify" class="mini-picker ">
           <date-picker v-model="data['ex'+$.ucfirst(type)]" value-type="format" format="YYYY-MM-DD" :editable="false" ></date-picker>
        </div>
    </div>
    `;
    Vue.component("expected-template",{
        delimiters: ['{%', '%}'],
        props:['data','modify','title','type'],
        template:expectedTemplate,
    });

    /*완료일 컴포넌트*/
    const completeTemplate = `
<div :class="modify?'w-100px':''">
    <div v-show="!modify">
        <div v-if="9 > data['st'+$.ucfirst(type)]" :class="data[type+'Color']">
            <i :class="'fa '+data[type+'Icon']" aria-hidden="true" v-if="!$.isEmpty(data[type+'Icon'])"></i>
            {% data[type+'Status'] %}
        </div>
        <div v-if="data['st'+$.ucfirst(type)] >= 9" class="text-green">
            <i class="fa fa-check-circle" aria-hidden="true"></i>
            {% $.formatShortDate(data['cp'+$.ucfirst(type)]) %}
            {% data[type+'Status'] %}
        </div>
    </div>
    <div v-show="modify" class="mini-picker ">
        <date-picker v-model="data['cp'+$.ucfirst(type)]" value-type="format" format="YYYY-MM-DD" :editable="false" ></date-picker>
    </div>
</div>
    `;
    Vue.component("complete-template",{
        delimiters: ['{%', '%}'],
        props:['data','modify','title','type'],
        template:completeTemplate,
    });

    /*완료일 컴포넌트2*/
    const completeTemplate2 = `
<div :class="modify?'w-100px':''">
    <div v-show="!modify">
        <div :class="data[type+'Color']">
            <i :class="'fa fa-'+data[type+'Icon']" aria-hidden="true" v-if="!$.isEmpty(data[type+'Icon'])"></i>
            <span v-if="data['st'+$.ucfirst(type)] >= 9">{% $.formatShortDate(data['cp'+$.ucfirst(type)]) %}</span>
            {% data[type+'Status'] %}
        </div>
    </div>
    <div v-show="modify" class="mini-picker ">
        <date-picker v-model="data['cp'+$.ucfirst(type)]" value-type="format" format="YYYY-MM-DD" :editable="false" ></date-picker>
    </div>
</div>
    `;
    Vue.component("complete-template2",{
        delimiters: ['{%', '%}'],
        props:['data','modify','title','type'],
        template:completeTemplate2,
    });

    /*완료일 컴포넌트3*/
    const completeTemplate3 = `
<div :class="modify?'w-100px':''">
    <div :class="" v-show="!modify">
        {% $.formatShortDate(data['cp'+$.ucfirst(type)]) %}
    </div>
    <div v-show="modify" class="mini-picker ">
        <date-picker v-model="data['cp'+$.ucfirst(type)]" value-type="format" format="YYYY-MM-DD" :editable="false" ></date-picker>
    </div>
</div>
    `;
    Vue.component("complete-template3",{
        delimiters: ['{%', '%}'],
        props:['data','modify','title','type'],
        template:completeTemplate3,
    });


    const scheduleTemplate = `
<div>
    <div v-if="9 != data['st'+$.ucfirst(type)]">
        예정 :
        <span v-show="!modify">{% $.formatShortDate(data['ex'+$.ucfirst(type)]) %}</span>
        <span v-show="modify" class="mini-picker">
            <date-picker v-model="data['ex'+$.ucfirst(type)]" value-type="format" format="YYYY-MM-DD" :editable="false"></date-picker>
        </span>
    </div>
    <div class="dp-flex">
        상태 :
        <div :class="'dp-flex ' + data[type+'Color']">
            <i :class="'fa fa-' + data[type+'Icon']" aria-hidden="true" v-if="!$.isEmpty(data[type+'Icon'])"></i>
            <span v-if="data['st'+$.ucfirst(type)] >= 9">{% $.formatShortDate(data['cp'+$.ucfirst(type)]) %}</span>
            {% data[type+'Status'] %}
        </div>
    </div>
</div>
    `;
    Vue.component("schedule-template",{
        delimiters: ['{%', '%}'],
        props:['data','modify','type'],
        template:scheduleTemplate,
    });


    /*결재 컴포넌트*/
    const approvalTemplate2 = `
<div>
    <div class="pd5" v-show="'r' === project[confirmField] && approval[confirmType].sno > 0 ">결재 진행 중</div>
    <div class="pd5 sl-green" v-show="'p' === project[confirmField] && $.isEmpty(project[memoField])">결재 완료</div>
    <div class="pd5 sl-green" v-show="'p' === project[confirmField] && !$.isEmpty(project[memoField])">
        결재 PASS <div class="text-muted font-11">{% project[memoField] %}<span class="cursor-pointer hover-btn text-muted font-11" @click="setApprovalReset(confirmType)">(결재진행하기)</span></div>
    </div>
    <div class="pd5" v-show="'f' === project[confirmField]">반려</div>

    <div v-if="approval[confirmType].sno > 0" class="text-left">
        <div class="font-11 pdt5 pdl5">기안:{% approval[confirmType].regManagerNm %}</div>
        <div class="font-11 pd5 dp-flex_" style="justify-content: center; align-items: center;  ">
            <div @click="openApprovalView(approval[confirmType].sno)" class="cursor-pointer hover-btn">
                <div v-for="(target, targetIndex) in approval[confirmType].targetManagerList" class="mgr5">
                    <i class="fa fa-chevron-right" aria-hidden="true" v-if="targetIndex >= 0"></i>
                    {% target.name %}
                    <span class="text-muted" v-show="'proc' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt) %})</span>
                    <span class="text-danger" v-show="'reject' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                    <span class="text-green" v-show="'accept' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                    <span class="text-green" v-show="'complete' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                </div>
            </div>
        </div>
    </div>
</div>
    `;
    Vue.component("approval-template2",{
        delimiters: ['{%', '%}'],
        props:['project','approval','confirm-type','confirm-field','memo-field'],
        template:approvalTemplate2,
    });

    const approvalTemplate3 = `
<div>
    <div class="pd5" v-show="'r' === project[confirmField] && approval[confirmType].sno > 0 ">결재 진행 중</div>

    <div class="pd5 sl-green" v-show="'p' === project[confirmField] && !$.isEmpty(project[memoField])">
        결재 PASS <div class="text-muted font-11">{% project[memoField] %}<span class="cursor-pointer hover-btn text-muted font-11" @click="setApprovalReset(confirmType)">(결재진행하기)</span></div>
    </div>

    <div class="pd5" v-show="'f' === project[confirmField]">반려</div>

    <div v-if="approval[confirmType].sno > 0" class="text-left" v-show="'p' !== project[confirmField]">
        <div class="font-11 pdt5 pdl5">기안:{% approval[confirmType].regManagerNm %}</div>
        <div class="font-11 pd5 dp-flex_" style="justify-content: center; align-items: center;  ">
            <div @click="openApprovalView(approval[confirmType].sno)" class="cursor-pointer hover-btn">
                <div v-for="(target, targetIndex) in approval[confirmType].targetManagerList" class="mgr5">
                    <i class="fa fa-chevron-right" aria-hidden="true" v-if="targetIndex >= 0"></i>
                    {% target.name %}
                    <span class="text-muted" v-show="'proc' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt) %})</span>
                    <span class="text-danger" v-show="'reject' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                    <span class="text-green" v-show="'accept' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                    <span class="text-green" v-show="'complete' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                </div>
            </div>
        </div>
    </div>

    <div v-if="approval[confirmType].sno > 0" v-show="'p' === project[confirmField]">
        <div class="dp-flex">
            <div>{% $.formatShortDate(approval[confirmType].completeDt) %}</div>
            <div class="btn btn-sm btn-white" @click="openApprovalView(approval[confirmType].sno)">
                상세
            </div>
        </div>
    </div>

</div>
    `;
    Vue.component("approval-template3",{
        delimiters: ['{%', '%}'],
        props:['project','approval','confirm-type','confirm-field','memo-field'],
        template:approvalTemplate3,
    });

    const approvalTemplate4 = `
<div>

    <div class="pd5" v-show="'r' === project[confirmField] && approval[confirmType].sno > 0 ">결재 진행 중</div>

    <div class="pd5 sl-green" v-show="'p' === project[confirmField] && !$.isEmpty(project[memoField])">
        결재 PASS <div class="text-muted font-11">{% project[memoField] %}<span class="cursor-pointer hover-btn text-muted font-11" @click="setApprovalReset(confirmType)">(결재진행하기)</span></div>
    </div>

    <div class="pd5" v-show="'f' === project[confirmField]">반려</div>

    <div v-if="approval[confirmType].sno > 0" class="text-left" >
        <div class="font-11 pdt5 pdl5">기안:{% approval[confirmType].regManagerNm %}</div>
        <div class="font-11 pd5 dp-flex_" style="justify-content: center; align-items: center;  ">
            <div @click="openApprovalView(approval[confirmType].sno)" class="cursor-pointer hover-btn">
                <div v-for="(target, targetIndex) in approval[confirmType].targetManagerList" class="mgr5">
                    <i class="fa fa-chevron-right" aria-hidden="true" v-if="targetIndex >= 0"></i>
                    {% target.name %}
                    <span class="text-muted" v-show="'proc' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt) %})</span>
                    <span class="text-danger" v-show="'reject' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                    <span class="text-green" v-show="'accept' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                    <span class="text-green" v-show="'complete' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                </div>
            </div>
        </div>
    </div>
</div>
    `;
    Vue.component("approval-template4",{
        delimiters: ['{%', '%}'],
        props:['project','approval','confirm-type','confirm-field','memo-field'],
        template:approvalTemplate4,
    });


    const approvalTemplate5 = `
<div>
    <div class="" v-show="'r' === project[confirmField] && approval[confirmType].sno > 0 ">
        <div @click="openApprovalView(approval[confirmType].sno)" class="cursor-pointer hover-btn">
            <div class="font-11 btn-sm btn-red btn-red-line2 btn">결재</div>
        </div>
    </div>
    <div class="pd5 sl-green" v-show="'p' === project[confirmField] && !$.isEmpty(project[memoField])">
        결재 PASS <div class="text-muted font-11">{% project[memoField] %}<span class="cursor-pointer hover-btn text-muted font-11" @click="setApprovalReset(confirmType)">(결재진행하기)</span></div>
    </div>
    <div class="pd5" v-show="'f' === project[confirmField]">반려</div>
</div>
    `;
    Vue.component("approval-template5",{
        delimiters: ['{%', '%}'],
        props:['project','approval','confirm-type','confirm-field','memo-field'],
        template:approvalTemplate5,
    });

</script>


<!--// 레이어 팝업 속성 정의-->
<script type="text/javascript">
    Vue.component('send-history-layer-pop', {
        delimiters: ['{%', '%}'],
        template: '#send-history-layer-pop-template',
        props: {
            title : { type: String, default:'-'},
            visible: { type: Boolean, default: false },
            projectSno: { type: [String, Number], required: true } // 프로젝트 번호를 받음
        },
        data() {
            return {
                loading: false,
                list: [] // 데이터를 내부에서 관리
            };
        },
        watch: {
            // visible 프로퍼티가 true가 될 때 실행
            visible(newVal) {
                if (newVal === true) {
                    this.fetchData();
                }
            }
        },
        methods: {
            fetchData() {
                this.loading = true;
                ImsService.getList('sendHistory', {
                    sendType:this.title,
                    projectSno:this.projectSno,
                }).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        this.list = $.copyObject(data);
                        this.loading = false;
                    });
                });
            },
            close() {
                this.$emit('update:visible', false);
                this.$emit('close');
            }
        }
    });
</script>


<!--메일 발송 레이어 팝업 속성-->
<script type="text/javascript">
    Vue.component('email-sender-pop', {
        template: '#email-sender-template',
        // [부모 -> 자식] 데이터 전달 (읽기 전용)
        props: {
            visible: { type: Boolean, default: false },
            type: { type: String, default: '' }, // proposal, assort, order

            // 초기값 Props (부모 데이터와의 연결고리)
            initReceiver: { type: String, default: '' },
            initEmail: { type: String, default: '' },
            initFileUrl: { type: String, default: '' },

            // API 호출용 키값
            projectSno: { type: [String, Number], required: true },
            customerSno: { type: [String, Number], required: true }
        },
        data() {
            return {
                loading: false,
                // [자식 내부] 독립적인 수정용 변수
                localReceiver: '',
                localEmail: '',
                localFileUrl: '',
                ccList: [''], // 참조자 목록 (기본 빈칸 1개)
            };
        },
        computed: {
            title() {
                const map = {
                    'sampleGuide': '샘플 안내서',
                    'meetingReport': '회의록',
                    'proposal': '제안서',
                    'assort': '아소트 입력 요청',
                    'designGuide': '사양서'
                };
                return map[this.type] || '이메일';
            }
        },
        watch: {
            // 팝업이 열릴 때마다 부모의 초기값을 내부 변수로 '복사' (Deep Copy 효과)
            visible(newVal) {
                if (newVal === true) {
                    this.localReceiver = this.initReceiver;
                    this.localEmail = this.initEmail;
                    this.localFileUrl = this.initFileUrl;
                    this.ccList = ['']; // 참조자 초기화
                    this.loading = false;
                }
            }
        },
        methods: {
            // 참조자 칸 추가
            addCc() {
                this.ccList.push('');
            },
            // 참조자 칸 제거
            removeCc(index) {
                this.ccList.splice(index, 1);
            },

            // 발송 전 검증
            submitSend() {
                if ($.isEmpty(this.localEmail)) {
                    $.msg('수신자 이메일 주소를 입력해주세요.', '', 'warning');
                    return;
                }

                // 참조자 빈값 필터링
                const validCcList = this.ccList.filter(email => !$.isEmpty(email));

                $.msgConfirm(`${this.title} 메일을 발송하시겠습니까?`, '').then((result) => {
                    if (result.isConfirmed) {
                        this.executeApi(validCcList);
                    }
                });
            },

            executeApi(validCcList) {
                this.loading = true;

                // 1. 공통 파라미터 생성
                const baseParams = {
                    sno: this.projectSno,
                    type : this.type,
                    customerSno: this.customerSno,
                    receiver: this.localReceiver,
                    email: this.localEmail,
                    ccList: validCcList
                };

                // 2. 타입별 전략 정의 (설정 객체)
                const strategies = {
                    meetingReport: {
                        getParams: (p) => ({ ...p, fileUrl: this.localFileUrl })
                    },
                    sampleGuide: {
                        getParams: (p) => ({ ...p, fileUrl: this.localFileUrl })
                    },
                    proposal: {
                        getParams: (p) => ({ ...p, fileUrl: this.localFileUrl })
                    },
                    assort: {
                        getParams: (p) => p
                    },
                    designGuide: { //사양서
                        getParams: (p) => p
                    }
                };

                // 3. 전략 선택 및 실행
                const currentStrategy = strategies[this.type];
                if (!currentStrategy) {
                    console.error('정의되지 않은 발송 타입입니다:', this.type);
                    this.loading = false;
                    return;
                }
                const params = currentStrategy.getParams(baseParams);

                // 발송
                $.imsPost('sendMailToCustomer', params).then((data) => {
                    $.imsPostAfter(data, () => {
                        refreshData();
                        $.msg('성공적으로 발송되었습니다.', '', 'success');
                        this.close();
                    });
                }).catch((e) => {
                    console.error(e);
                }).finally(() => {
                    this.loading = false;
                });
            },

            close() {
                this.$emit('update:visible', false);
            }
        }
    });
</script>
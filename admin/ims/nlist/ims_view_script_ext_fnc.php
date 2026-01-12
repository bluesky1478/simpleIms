<script type="text/javascript">

    let currentStatus = 0;

    const styleMap = {};
    <?php foreach($codeStyle as $codeKey => $code){ ?>
    styleMap['<?=$codeKey?>'] = '<?=$code?>';
    <?php } ?>

    const styleEtcListMap = {
        'prd003': '스타일 선호도',
        'prd004': '원단 선호도',
        'prd005': '부자재 선호도',
        'prd006': '인쇄 형태 선호도',
        'prd007': '기능 선호도',
        'prd008': '불편사항',
        'prd001': '기타/비고',
    };

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
        $.imsPost2('setApproval',{
            'projectSno'   : sno,
            'approvalType' : approvalType+'Confirm',
            'status' : 'n', //승인처리
            'approvalMemo' : approvalType+'Memo',
            'memo' : '',
        },()=>{
            location.reload();
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
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
            }
        });
    }

</script>


<script type="text/javascript">
    /**
     * 고객 정보 셋팅
     */
    const setCustInfo = () =>{
        vueApp.custInfo = [];
        let custInfo = [];
        let cnt = 0;

        <?php foreach( $customerInfoField as $custAddKey => $custAddFieldInfo ) { ?>
        if( !$.isEmpty(vueApp.customer.addedInfo['<?=$custAddKey?>']) ){
            custInfo.push(
                {
                    'title' : '<?=$custAddFieldInfo['title']?>',
                    'value' : vueApp.customer.addedInfo['<?=$custAddKey?>'],
                    'type' : '<?=$custAddFieldInfo['type']?>',
                    'code' : '<?=$custAddFieldInfo['code']?>',
                }
            );
            cnt++;

            if(2 === cnt) {
                vueApp.custInfo.push(custInfo);
                cnt=0;
                custInfo=[];
            }
        }
        <?php } ?>
        //남는부분 넣기.
        if( custInfo.length > 0 ) vueApp.custInfo.push(custInfo);
        //console.log(vueApp.custInfo);
    }

    /**
     * 프로젝트 갱신
     */
    const refreshProject = (sno)=>{
        $.imsPost2('getSimpleProject',{sno:sno},(data)=>{
            //프로젝트 정보
            vueApp.project = $.copyObject(data);
            //상태 셋팅
            vueApp.currentStatus = data.projectStatus;
            currentStatus = data.projectStatus;
            //고객 정보 가져오기.
            ImsCustomerService.getData(data.customerSno).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.customer = data;
                    setCustInfo();
                });
            });
            //스타일 정보 가져오기.
            //refreshProductList(sno);
        });
    }

    /**
     * 상품 리스트 갱신
     */
    function refreshProductList(sno) {
        ImsProductService.getListStyle(sno).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.productList = [];

                data.list.forEach((product)=>{
                    product.estimateCnt = 0;
                    product.prdMoq = 0;
                    product.priceMoq = 0;

                    product.fabricList = [];
                    product.fabricCnt = 0;
                    product.fabricCompleteCnt = 0;
                    product.btCompleteCnt = 0;

                    vueApp.productList.push(product);
                    
                    //스타일별 견적 데이터 추가
                    ImsService.getList('estimate', {'styleSno':product.sno}).then((estimateData)=>{
                        product.estimateCnt = estimateData.data.list.length;
                        if( !$.isEmpty2(product.estimateConfirmSno) && product.estimateConfirmSno > 0 ){
                            estimateData.data.list.forEach((estimate)=>{
                                if( product.estimateConfirmSno === estimate.sno ){
                                    product.prdMoq = estimate.contents.prdMoq;
                                    product.priceMoq = estimate.contents.priceMoq;
                                }
                            });
                        }
                    });
                });

                //QB 데이터
                $.imsPost2('getFabricList',{
                    'projectSno' : sno,
                    'ignoreStatus' : '5', //사용안함 제외
                },(fabricList)=>{
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

                });

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
                    vueApp[issueData.issueType+'List'].push(issueData);
                });
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


<!--컴포넌트-->
<script type="text/javascript">

    /*예정일 컴포넌트*/
    const expectedTemplate = `
    <div>
        <div v-show="!modify" class="relative cursor-pointer hover-btn"
            @click="openProjectUnit(data.projectSno,data.fieldDiv+'ExpectedDt',data.fieldDiv+'CompleteDt',data.fieldDiv,title)">
            <span v-if='!$.isEmpty(data.expectedDt)'>{% $.formatShortDate(data.expectedDt) %}</span>
            <span v-if='$.isEmpty(data.expectedDt)'>-</span>
            <comment-cnt :data="data"></comment-cnt>
        </div>
        <div v-show="modify" class="mini-picker ">
           <date-picker v-model="data.expectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" ></date-picker>
        </div>
    </div>
    `;
    Vue.component("expected-template",{
        delimiters: ['{%', '%}'],
        props:['data','modify','title'],
        template:expectedTemplate,
    });

    /*완료일 컴포넌트*/
    const completeTemplate = `
    <div>
        <div v-show="!modify" class="relative cursor-pointer hover-btn"
            @click="openProjectUnit(data.projectSno,data.fieldDiv+'ExpectedDt',data.fieldDiv+'CompleteDt',data.fieldDiv,title)">

            <div v-show='!$.isEmpty(data.alterText)'>
                <span class="font-10">{% data.alterText %}</span>
            </div>
            <div v-show='$.isEmpty(data.alterText)'>
                <span v-if='!$.isEmpty(data.completeDt)'>{% $.formatShortDate(data.completeDt) %}</span>
                <span v-if='$.isEmpty(data.completeDt)'>-</span>
            </div>

        </div>
        <div v-show="modify" class="mini-picker ">
           <date-picker v-model="data.completeDt" value-type="format" format="YYYY-MM-DD" :editable="false" ></date-picker>
        </div>
    </div>
    `;
    Vue.component("complete-template",{
        delimiters: ['{%', '%}'],
        props:['data','modify','title'],
        template:completeTemplate,
    });


    /*결재 컴포넌트*/

    //project.planConfirm
    //project.planMemo
    //projectApprovalInfo
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

</script>
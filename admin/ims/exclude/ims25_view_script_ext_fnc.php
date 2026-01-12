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

    function scrollToAndHighlight(id) {
        $('#'+id).focus();
        const el = document.getElementById(id);
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
                refreshProject(sno);
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
                //
                /*if( 'filePlan' === dropzoneId || 'fileProposal' === dropzoneId ){
                    //
                }*/
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
        prdSnoList.forEach((sno)=>{
            prdList.push(vueApp.productList.find(item => item.sno == sno));
        });

        return prdList;
    };

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
    function refreshProject(sno) {
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
        });
    };

    /**
     * 상품 리스트 갱신
     */
    function refreshProductList(sno) {
        ImsProductService.getListStyle(sno).then((data)=>{
            $.imsPostAfter(data,(data)=>{

                console.log('Refresh...',data);

                vueApp.productList = [];
                vueApp.workOrderCompleteCnt = 0;
                data.list.forEach((product)=>{

                    if(2 === product.workStatus){
                        vueApp.workOrderCompleteCnt++;
                    }

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
        <div class="dp-flex dp-flex-center">
            <div class="btn btn-sm btn-white" @click="openApprovalView(approval[confirmType].sno)">
                결재정보
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

</script>
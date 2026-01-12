
<script type="text/javascript">

    function openUrl(id, url, width, height){
        let popupWidth = window.innerWidth;  // 화면 전체 너비
        let popupHeight = window.innerHeight; // 화면 전체 높이

        if(typeof width != 'undefined'){
            popupWidth = width;
        }
        if(typeof height != 'undefined'){
            popupHeight = height;
        }

        const win = popup({
            url:url,
            width: popupWidth,
            height: popupHeight,
            scrollbars: 'yes',
            resizable: 'yes',
            target: id,
        });
        win.focus();
    }

    function openSalesView(sno){
        const win = popup({
            url: `<?=$myHost?>/ims/ims_view_sales.php?sno=${sno}`,
            target: `ims-view-sales-${sno}`,
            width: 1600,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 팝업열기
     */ 
    function openCommonPopup(popupName, width, height,params, isSingle){
        /* //DEBUG ...
        console.log('popupFileName', popupFileName);
        console.log('queryString', queryString);
        console.log('width', width);
        console.log('height', height);
        console.log('popupId', popupId);*/
        if(typeof isSingle == 'undefined'){
            isSingle = true;
        }
        const popupFileName = 'ims_pop_' + popupName;
        const queryString = $.objectToQueryString(params);
        let popupId = popupFileName;
        if(!isSingle){
            popupId += Date.now();
        }
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_${popupName}.php?${queryString}`,
            target: popupId,
            width: width,
            height: height,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }


    /**
     * 고객 코멘트 열기
     */
    function openCustomerComment(customerSno, sno, issueType){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_customer_issue.php?customerSno=${customerSno}&sno=${sno}&issueType=${issueType}`,
            target: `customer_issue_${customerSno}_${sno}`,
            width: 900,
            height: 850,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 생산 스케쥴 예정일 수정
     */
    const openScheduleLayer = function(sno){
        let childNm = 'schedule_update';
        let addParam = {
            mode: 'simple',
            layerTitle: '생산 스케쥴 예정일 수정',
            layerFormID: childNm + "Layer",
            parentFormID: childNm + "Row",
            dataFormID: childNm + "Id",
            dataInputNm: childNm,
            sno: sno,
        };
        layer_add_info(childNm, addParam);
    }
    /**
     * 요청 열기
     */
    function openProduceRequest(projectSno, reqType, sno){
        const win = popup({
            url: `popup/ims_request.php?projectSno=${projectSno}&reqType=${reqType}&sno=${sno}`,
            target: 'imsRequest' + sno,
            width: 1500,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 스타일 열기
     */
    function openProductReg(projectSno, sno){
        const win = popup({
            url: `<?=$myHost?>/ims/ims_product_reg.php?projectSno=${projectSno}&sno=${sno}`,
            target: 'imsProduct' + sno,
            width: 1650,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 스타일 열기 2 (tabMode 추가)
     */
    function openProductReg2(projectSno, sno, tabMode){
        let width = 1560;
        if( $.isEmpty(sno) ){
            width = 950;
        }
        const win = popup({
            url: `<?=$myHost?>/ims/ims_product.php?projectSno=${projectSno}&sno=${sno}&tabMode=${tabMode}`,
            target: 'imsProduct' + sno,
            width: width,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    function openProductWithSample(projectSno, sno, sampleSno, mSampleType=null, sTabMenu='instruct'){
        if (mSampleType === null || mSampleType == 9) {
            const win = popup({
                url: `<?=$myHost?>/ims/ims_product.php?projectSno=${projectSno}&sno=${sno}&tabMode=2&sampleSno=${sampleSno}`,
                target: 'imsProduct' + sno + '_' + sampleSno,
                width: 1550,
                height: 900,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win.focus();
        } else {
            let iSampleSno = sampleSno == -1 ? 0 : sampleSno;
            const win = popup({
                url: `<?=$myHost?>/ims/popup/ims_pop_product_sample_new.php?styleSno=${sno}&sno=${iSampleSno}&tabmenu=${sTabMenu}`,
                target: 'imsProductSample' + sno + '_' + sampleSno,
                width: 1550,
                height: 900,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win.focus();
        }
    }
    function openProductWithFabric(projectSno, sno, fabricSno){
        const win = popup({
            url: `<?=$myHost?>/ims/ims_product.php?projectSno=${projectSno}&sno=${sno}&tabMode=1&fabricSno=${fabricSno}`,
            target: 'imsProduct' + sno,
            width: 1550,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }


    /**
     * 생산견적 열기
     */
    function openFactoryEstimateView(projectSno, styleSno, sno, mode){
        const win = popup({
            url: `<?=$myHost?>/ims/ims_factory_estimate_view.php?projectSno=${projectSno}&styleSno=${styleSno}&sno=${sno}&mode=${mode}`,
            target: 'imsRequest' + mode + sno,
            width: 1550,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * URL에 따른 팝업 열기
     */
    function openCallView(url){
        let win = popup({
            url: url,
            target: '',
            width: 1400,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
        return win;
    }
    /**
     * 프로젝트 열기
     */
    function openProjectView(projectSno){
        openProjectViewAndSetTabMode(projectSno, 'basic');
    }
    /**
     * 프로젝트 열기 2 (탭모드 추가)
     */
    function openProjectViewAndSetTabMode(projectSno,tabMode){
        $.cookie('viewTabMode', '');
        const win = popup({
            url: `<?=$myHost?>/ims/ims_view2.php?sno=${projectSno}&popup=yes`,
            target: 'imsProject',
            width: 1750,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 프로젝트 열기 (생산처)
     */
    function openProjectViewFactory(projectSno){
        $.cookie('viewTabMode', '');
        const win = popup({
            url: `<?=$myHost?>/ims/ims_project_view.php?sno=${projectSno}&popup=yes`,
            target: 'imsProject',
            width: 1750,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 프로젝트 상태 변경이력 열기
     */
    function openProjectStatusHistory(projectSno, historyDiv){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_status_history.php?projectSno=${projectSno}&historyDiv=${historyDiv}`,
            target: 'imsStatusHistory',
            width: 850,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 상태 변경이력 열기 (다른버전 , 범용적)
     */
    function openStatusHistory(condition, historyDiv){

        //console.log('상태이력 인자값', condition);
        const paramList = [];
        const checkParam = [
            'customerSno',
            'projectSno',
            'styleSno',
            'eachSno',
        ];
        checkParam.forEach((paramName)=>{
            if( !$.isEmpty(condition[paramName]) ) {
                paramList.push( paramName + '=' + condition[paramName]);
            }
        })
        const paramStr = paramList.join('&',paramList);
        const win = popup({
            url: `popup/ims_pop_status_history.php?historyDiv=${historyDiv}&${paramStr}`,
            target: 'imsStatusHistory',
            width: 850,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 데이터 업데이트 이력 열기
     */
    function openUpdateHistory(sno, historyDiv){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_update_history.php?sno=${sno}&historyDiv=${historyDiv}`,
            target: 'imsUpdateHistory',
            width: 850,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 생산정보 열기
     */
    function openProduce(sno){
        const win = popup({
            url: `ims_produce_view.php?sno=${sno}&popup=yes`,
            target: 'ims_produce_view_'+sno,
            width: 1450,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 개별 생산 정보 열기
     */
    function openProduceUnit(sno, div){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_production_unit.php?sno=${sno}&div=${div}`,
            target: 'ims_pop_production_unit_'+sno+'_'+div,
            width: 850,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 개별 생산정보 열기 2 (파라미터 추가)
     */
    function openProjectUnit(sno, type, title){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_project_unit.php?sno=${sno}&type=${type}&title=${title}`,
            target: `ims-project-unit${sno}${type}`,
            width: 850,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    function openProjectSimple(sno){
        const win = popup({
            url: `<?=$myHost?>/ims/ims25_pop_view.php?sno=${sno}`,
            target: `ims-project-simple-${sno}`,
            width: 1600,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 고객 정보 보기
     */
    const openCustomer = function(sno, inTabMode, inModify){
        let tabMode = 'basic';
        let modify = 'false';
        if( typeof inTabMode != 'undefined' ){
            tabMode = inTabMode;
        }
        if( typeof inModify != 'undefined' ){
            modify = inModify;
        }
        const url = `<?=$myHost?>/ims/customer_view.php?sno=${sno}&tabMode=${tabMode}&modify=${modify}`;
        openCallView(url);
    };
    const openCustomer2 = function(sno, inTabMode, inModify){
        let tabMode = 'basic';
        let modify = 'false';
        if( typeof inTabMode != 'undefined' ){
            tabMode = inTabMode;
        }
        if( typeof inModify != 'undefined' ){
            modify = inModify;
        }
        const url = `<?=$myHost?>/ims/ims_customer_view.php?sno=${sno}&tabMode=${tabMode}&modify=${modify}`;
        openCallView(url);
    };

    const openCustomerCommentHistory = function(sno, type){
        const url = `<?=$myHost?>/ims/customer_view.php?sno=${sno}&tabMode=comment&type=${type}`;
        openCallView(url);
    };

    /**
     * 결재 요청 열기
     */
    function openApproval(){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_todo_approval_view.php`,
            target: 'imsApproval',
            width: 800,
            height: 650,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    /**
     * 투두 요청 열기
     */
    function openTodoRequest(sno,resSno){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_todo_request_view.php?sno=${sno}&resSno=${resSno}`,
            target: 'imsRequest',
            width: 1100,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 투두 요청 등록 열기
     */
    function openTodoRequestWrite(customerSno, projectSno, teamSno){
        const addCondition = [];
        if(typeof customerSno != 'undefined') addCondition.push('customerSno='+customerSno);
        if(typeof projectSno != 'undefined') addCondition.push('projectSno='+projectSno);
        if(typeof teamSno != 'undefined') addCondition.push('teamSno='+teamSno);
        const addConditionStr = addCondition.join('&');

        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_todo_request_write.php?${addConditionStr}`,
            target: 'imsRequest',
            width: 900,
            height: 700,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 승인(기안) 등록 열기
     */
    function openApprovalWrite(customerSno, projectSno, approvalType, styleSno){
        const addCondition = [];
        if(typeof customerSno != 'undefined') addCondition.push('customerSno='+customerSno);
        if(typeof projectSno != 'undefined') addCondition.push('projectSno='+projectSno);
        if(typeof approvalType != 'undefined') addCondition.push('approvalType='+approvalType);
        if(typeof styleSno != 'undefined') addCondition.push('styleSno='+styleSno);
        const addConditionStr = addCondition.join('&');
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_approval_write.php?${addConditionStr}`,
            target: 'imsApprovalWrite',
            width: 950,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    function openApprovalEachWrite(eachSno, projectSno,approvalType){
        const addCondition = [];
        addCondition.push('eachSno='+eachSno);
        addCondition.push('projectSno='+projectSno);
        addCondition.push('approvalType='+approvalType);
        const addConditionStr = addCondition.join('&');
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_approval_write.php?${addConditionStr}`,
            target: 'imsApprovalWrite',
            width: 950,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 결재선 보기
     * @param sno
     */
    function openApprovalView(sno){
        const addCondition = [];
        if(typeof sno != 'undefined') addCondition.push('sno='+sno);
        const addConditionStr = addCondition.join('&');

        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_approval_view.php?${addConditionStr}`,
            target: 'imsApprovalView',
            width: 950,
            height: 750,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }


    /**
     * 프로젝트 열기 (심플, 일정수정)
     * @param params
     */
    function openSimpleProject(params){
        const addCondition = [];
        if(typeof params['customerSno'] != 'undefined') addCondition.push('customerSno='+params['customerSno']);
        if(typeof params['projectSno'] != 'undefined') addCondition.push('projectSno='+params['projectSno']);
        const addConditionStr = addCondition.join('&');

        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_pop_simple_project.php?modify=yes&${addConditionStr}`,
            target: 'imsRequest' + addConditionStr ,
            width: 1450,
            height: 550,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 결재 이력
     * @param params
     * @param approvalType
     */
    function openApprovalHistory(params, approvalType){
        const revisionCheckList = [
            'customerSno',
            'projectSno',
            'styleSno',
            'eachSno',
        ];
        const searchParams = [];
        revisionCheckList.forEach((field)=>{
            const value = (typeof params[field] != 'undefined')?params[field]:'';
            searchParams.push(field + '=' + value);
        });
        const searchParamsStr = searchParams.join('&');

        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_approval_history.php?approvalType=${approvalType}&${searchParamsStr}`,
            target: 'imsProduct',
            width: 800,
            height: 650,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

</script>

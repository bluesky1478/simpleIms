<?php include './admin/ims/script/library_util.php'?>
<?php include './admin/ims/script/library_list.php'?>
<?php include './admin/ims/script/library_file.php'?>
<?php include './admin/ims/script/library_product.php'?>
<?php include './admin/ims/script/library_request.php'?>
<?php include './admin/ims/script/library_production.php'?>
<?php include './admin/ims/script/library_fabric.php'?>
<?php include './admin/ims/script/library_todo.php'?>
<?php include './admin/ims/script/library_project.php'?>
<?php include './admin/ims/script/library_pop.php'?>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<link rel="stylesheet" href="/admin/script/dropzone5/dropzone.min.css" type="text/css">
<script src="/admin/script/dropzone5/dropzone.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

<script type="text/javascript">

    Vue.directive('focus', {
        // 바인딩된 엘리먼트가 DOM에 삽입될 때...
        inserted: function (el) {
            // 엘리먼트에 포커스를 줍니다
            el.focus();
        }
    });

    Vue.component("select2",{
        props:['value','aclass'],
        template:`<select :class="'js-select2 '+aclass" :value="value"><slot></slot></select>`,
        watch: {
            value: function(newValue) {
                $(this.$el).val(newValue).trigger('change');
            }
        },
        mounted : function(){
            let parentEl = this;
            $(this.$el).on("change", function(e){
                parentEl.$emit('input', $(parentEl.$el).val());
                parentEl.$emit('change', $(parentEl.$el).val());
            });
        }
    });

    /**
     * 도로명 주소 찾기 (팝업)
     * 오버라이딩.
     * @author artherot
     * @param string zoneCodeID zonecode input ID
     * @param string addrID address input ID
     * @param string zipCodeID zipcode input ID
     */
    function gd_postcode_search(zoneCodeID, addrID, zipCodeID) {
        var win = gd_popup({
            url: '../share/postcode_search.php?zoneCodeID=' + zoneCodeID + '&addrID=' + addrID + '&zipCodeID=' + zipCodeID,
            target: 'postcode',
            width: 500,
            height: 450,
            resizable: 'yes',
            scrollbars: 'yes'
        });
        win.focus();
        return win;
    }
</script>

<script type="text/javascript">

    const appId = '#imsApp';
    let vueApp = null;

    /**
     * IMS SERVICE
     * @type {{initVueApp: (function(*=, *=): *), getData: (function(*=, *=): *)}}
     */
    const ImsService = {
        initVueApp : (el, initParams) => {
            //console.log('initVueApp BEGIN');
            const defaultOption = {
                methods : {
                    onEnd(event) {
                        //console.log('Drag ended', event);
                        //console.log('Updated items:', this.items);
                    },
                    test:()=>{
                        console.log('TEST');
                    },
                    setModify() {

                    },
                    customFormatter(date) {
                        return moment(date).format('YYYY-MM-DD');
                    },
                    addElement : (element, srcElement, attachedType, index)=>{
                        let currentIndex = -1;
                        if( typeof index != 'undefined' ){
                            currentIndex = index
                        }
                        const conditionPush = (obj)=>{
                            if( 'prefix' === attachedType || 'before' === attachedType ){
                                element.unshift(obj);
                            }else{
                                if( 'down' === attachedType && currentIndex > -1 ){
                                    element.splice(currentIndex+1, 0, obj);
                                }else{
                                    element.push(obj);
                                }
                            }
                        }
                        const copyObject = $.copyObject(srcElement);

                        $.clearArrayOrObject(copyObject);

                        //console.log('확인후삭제',copyObject);

                        if($.isEmpty(copyObject)){
                            conditionPush('');
                        }else{
                            conditionPush(copyObject);
                        }
                        return copyObject;
                    },
                    addElementAfterAction : (element, srcElement, attachedType, index, action)=>{
                        const createdObject = vueApp.addElement(element, srcElement, attachedType, index);
                        action(createdObject);
                    },
                    deleteElement : (data, index)=>{
                        data.splice(index,1);
                    },
                    getMargin : (prdCost, saleCost)=>{
                      return (saleCost>0) ? Math.round((saleCost-prdCost)/saleCost*100):0;
                    },
                },
                computed: {
                },
                watch: {
                    //"product.laborCost" : function(newValue){
                    //this.product.laborCost = Number((newValue+'').replace(/,/g, "")).toLocaleString();
                },
            };
            //인자로 넘어온 메소드와 합친다.
            Object.keys(defaultOption).forEach((optionField)=>{
                if( typeof initParams[optionField] !== 'undefined' ){
                    Object.keys(initParams[optionField]).forEach((addFncName)=>{
                        defaultOption[optionField][addFncName] = initParams[optionField][addFncName];
                    });
                }
            });
            //console.log(defaultOption);
            //console.log(el);

            initParams.data.lang = {
                /*formatLocale: {
                    firstDayOfWeek: 1,
                },*/
                monthBeforeYear: false,
            }

            //console.log('initVueApp END', initParams.data);

            return new Vue({
                el: el,
                delimiters: ['{%', '%}'],
                data : initParams.data,
                methods : defaultOption.methods,
                mounted : function() {
                    //$('#layerDim').show();
                    this.$nextTick(function () {
                        setJqueryEvent();
                        //$(el).show();
                        if( typeof initParams.mounted !== 'undefined' ){
                            initParams.mounted(this);
                        }
                        $(el).show();
                        //$('#layerDim').hide();
                        //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type="preOrder"><a href="#">TODO</a></div>');
                    });
                },
                computed : defaultOption.computed,
                watch: defaultOption.watch,
                components: {
                    draggable: vuedraggable
                },
                /*vuetify: new Vuetify(),*/
            });
        },
        getSchema : async (target, condition) => {
            return await $.postAsync('<?=$myHost?>/ims/ims_ps.php', {
                mode:'getSchema',
                target:target,
                condition:condition,
            });
        },
        getData : async (target, sno) => {
            return await $.postAsync('<?=$myHost?>/ims/ims_ps.php', {
                mode:'getData',
                target:target,
                sno :sno
            });
        },
        deleteData : async (target, sno, afterAction) => {
            $.msgConfirm('삭제시 복구가 불가능 합니다. 계속 하시겠습니까?', "").then((result)=>{
                if( result.isConfirmed ){
                    $.imsPost('deleteData',{
                        target:target,
                        sno :sno
                    }).then((data)=>{
                        if(200 === data.code){
                            afterAction();
                        }
                    });
                }
            });
        },
        getDataParams : async (target, params) => {
            params.mode = 'getData';
            params.target = target;
            return await $.postAsync('<?=$myHost?>/ims/ims_ps.php', params);
        },
        getProductData : async (projectSno, sno) => {
            return await $.postAsync('<?=$myHost?>/ims/ims_ps.php', {
                mode:'getProductData',
                projectSno :projectSno,
                sno :sno,
            });
        },
        /**
         * 파일 업로드 셋팅
         * @param appEl
         * @param dropzoneId
         * @param uploadAfterAction
         * @param saveParams
         */
        setDropzone : (appEl, dropzoneId, uploadAfterAction, saveParams)=>{
            try{
                //생성되었는지 여부 확인
                if (!document.querySelector('#'+dropzoneId).classList.contains('dz-clickable')) {

                    //console.log( '파일 #'+dropzoneId + ' 셋팅완료' );

                    let tmpFile = [];
                    //Type마다 반복
                    const myDropzone = new Dropzone("#"+dropzoneId, {
                        url: "<?=$nasUrl?>/upload.php",
                        dictDefaultMessage :'여기에 파일을 올려주세요',
                        maxFilesize: 500, // MB
                    });
                    // Handle added files
                    myDropzone.on('addedfile', (file) => {
                        $('#layerDim').show();
                        $('.dz-message').text('업로드중...');
                        $('.dz-message').show();
                    });
                    myDropzone.on('success', function(file, response) {
                        //console.log('File uploaded successfully. Response from server:', dropzoneId);
                        //myDropzone.removeAllFiles();
                        const respParse = JSON.parse(response);
                        tmpFile.push({
                            'fileName' : respParse.fileName,
                            'filePath' : respParse.filePath,
                        });
                    });
                    myDropzone.on('queuecomplete', function() {
                        if( typeof uploadAfterAction !== 'undefined' ){
                            if(typeof saveParams !== 'undefined'){
                                uploadAfterAction(tmpFile, saveParams);
                            }else{
                                uploadAfterAction(tmpFile, dropzoneId);
                            }
                        }
                        tmpFile = [];
                        myDropzone.removeAllFiles();
                        $('.dz-message').text('여기에 파일을 올려주세요');
                        $('#layerDim').hide();
                    });
                }
            }catch(e){
                console.log('dropzone setting error : ' + dropzoneId);
            }

        },
        /**
         * 체크박스 선택 여부 확인 및 선택 데이터 반환
         * @param selectEl
         * @param noCheckMessage
         * @returns {[]}
         */
        getSelectSnoList : (selectEl, noCheckMessage) => {
            const snoList = [];
            $(`input[name*="${selectEl}"]:checked`).each(function(){
                snoList.push( $(this).val() );
            });
            if( 0 === snoList.length  ){
                $.msg(noCheckMessage,'', "warning");
            }
            return snoList;
        },

        /**
         * List 데이터 반환
         * @param target
         * @param condition
         * @returns {Promise<*>}
         */
        getList : async (target, condition) => {
            //console.log('getList condition ', condition);
            return await $.postAsync('<?=$myHost?>/ims/ims_ps.php', {
                mode:'getList',
                target:target,
                condition : condition
            });
        },
        setNewAccept : (acceptValue, target, condition, afterAction, beforeAction) => {
            /*
            console.log('acceptValue', acceptValue);
            console.log('target', target);
            console.log('condition', condition);
            afterAction();*/

            if( typeof beforeAction != 'undefined' && null != beforeAction ){
                try{
                    beforeAction();
                }catch(e){
                    console.log('before action error');
                    console.log(e);
                }
            }

            $.postAsync('<?=$myHost?>/ims/ims_ps.php', {
                mode : 'setNewAccept',
                target : target,
                condition : condition,
                acceptValue : acceptValue,
            }).then((data)=>{
                if( 200 === data.code ){
                    if( typeof afterAction != 'undefined' && null !== afterAction ){
                        $.msg('처리완료','','success').then(()=>{
                            try{
                                afterAction(target,data);
                            }catch (e){
                                console.log('after action error');
                                console.log(e);
                            }
                        });
                    }
                }else{
                    console.log('setNewAccept 에러 발생');
                }
            });
        },
        setConfirmPass : (target, condition, afterAction, beforeAction, defaultMemo)=>{

            // 스크롤 위치를 저장
            window.addEventListener('scroll', saveScrollPosition());

            const saveData = $.copyObject(condition);

            $('#imsApp').hide();
            $.msgPrompt('승인 처리하시겠습니까?','승인메모','', (confirmMsg)=>{
                if( confirmMsg.isConfirmed ){
                    saveData.memo = defaultMemo + confirmMsg.value;
                    ImsService.setNewAccept('p',target, saveData, afterAction, beforeAction);
                }
                $('#imsApp').show();
            },()=>{$('#imsApp').show();});

        },
        setConfirmFail : (target, condition, afterAction, beforeAction)=>{
            const saveData = $.copyObject(condition);
            $('#imsApp').hide();
            $.msgPrompt('반려(or 승인번복) 처리하시겠습니까?','사유 필수','', (confirmMsg)=>{
                if( confirmMsg.isConfirmed ){
                    if( !$.isEmpty(confirmMsg.value) ){
                        saveData.memo = confirmMsg.value;
                        ImsService.setNewAccept('f',target, saveData, afterAction, beforeAction);
                    }else{
                        $.msg('사유는 필수 입니다.', "", "warning");
                    }
                }
                $('#imsApp').show();
            },()=>{$('#imsApp').show();});
        },
        setStatusFilter1:(status)=>{
            const map = {
                n : "<span class=''>준비</span>",
                r : "<span class='sl-blue'>요청</span>",
                p : "<span class='sl-green'>확정</span>",
                f : "<span class='text-danger'>반려</span>"
            };
            return map[status];
        },
        setStatusFilter2:(status)=>{
            const map = {
                n : "<span class=''>-</span>",
                r : "<span class='sl-blue'>처리완료</span>",
                p : "<span class='sl-green'>승인완료</span>",
                f : "<span class='text-danger'>반려</span>"
            };
            return map[status];
        },
        getLatestFileList: async (params)=>{
            return $.imsPost('getLatestFileList',params);
        },
        setProjectConfirm : ()=>{
            refreshProject();
        },
        setSearchDate : (condition, startField, endField, type)=>{
            const now = new Date();
            if( 'today' === type ){
                condition[startField] = formatDate(now);
                condition[endField] = formatDate(now);
            }
            if( 'week' === type ){
                const firstDayOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 1));
                const lastDayOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 7));
                condition[startField] = formatDate(firstDayOfWeek);
                condition[endField] = formatDate(lastDayOfWeek);
            }
            if( 'month' === type ){
                const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDayOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                condition[startField] = formatDate(firstDayOfMonth);
                condition[endField] = formatDate(lastDayOfMonth);
            }
            if( 'year' === type ){
                const firstDayOfYear = new Date(now.getFullYear(), 0, 1);   // 1월 1일
                const lastDayOfYear = new Date(now.getFullYear(), 11, 31); // 12월 31일
                condition[startField] = formatDate(firstDayOfYear);
                condition[endField] = formatDate(lastDayOfYear);
            }
        },
        setSearchDateSingle : (condition, startField, diffDate)=>{
            const now = new Date();
            const firstDayOfWeek = new Date(now.setDate(now.getDate() + diffDate));
            condition[startField] = formatDate(firstDayOfWeek);
        },
        simpleUpdate(sno, data){
            //console.log('simple update : ', sno);
            //console.log('simple update data : ', data);
            $.imsPost('simpleSave',{
                sno : sno
                , target : 'PROJECT_COMMENT'
                , data : data
            }).then((data)=>{
                console.log('simple update result', data);
            });
        },
        setEmergencyTodoConfirm : (resSnoListStr)=>{
            $.imsPost('setEmergencyTodoConfirm',{
                resSnoListStr : resSnoListStr
            }).then((data)=>{
            });
        },
        reqEmergencyTodo : (subject, projectSno, targetManagerSno)=>{
            $.msgConfirm('결재 취소를 요청하시겠습니까?', "최종 결재 담당자에게 요청합니다.").then((result)=>{
                if( result.isConfirmed ){
                    $.imsPost('reqEmergencyTodo',{
                        subject : subject,
                        projectSno : projectSno,
                        targetManagerSno : targetManagerSno,
                    }).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            $.msg('요청이 등록되었습니다.','잘 요청하였으니 추가로 취소 요청 버튼을 다시 누르지 마세요','success');
                        });
                    });
                }
            });
        }
    }

    /**
     * 고객 관련 서비스
     * @type {{}}
     */
    const ImsCustomerService = {
        getData : (customerSno)=>{
            return ImsService.getData(DATA_MAP.CUSTOMER, customerSno);
        },
        save : async (customerData,afterFnc)=>{
            let afterFncObject = null;
            if( typeof afterFnc != 'undefined' ){
                afterFncObject = afterFnc;
            }else{
                afterFncObject = ()=>{};
            }
            return $.imsSave('customer',customerData,afterFncObject);
        },
        saveCustomer : async (items) => {
            const promise = $.postAsync('<?=$myHost?>/ims/ims_ps.php', {
                mode:'saveCustomer',
                saveData : items,
            });
            promise.then((data)=>{
                if(200 === data.code){
                    let saveSno = data.data.sno;
                    $.msg('저장 되었습니다.', "", "success").then(()=>{
                        /*try {
                        <?php if(empty($requestParam['popup'])) { ?>
                        window.history.back(); //뒤로가기.
                        <?php }else{ ?>
                        opener.location.reload();
                        self.close();
                        <?php } ?>
                        }catch(e){}*/
                    });
                }else{
                    //$.msg(data.message, "", "warning"); 상위 함수에서 처리.
                }
            });
            return promise;
        },
        /**
         * TM EM 히스토리 가져오기
         * @param customerSno
         */
        getTmHistory : (customerSno) =>{
            return $.imsPost('getTmHistory', {customerSno:customerSno});
        },
        /**
         * 검색용 업종 불러오기
         */ 
        setBizCateSearch : (appObject, parentKeyName, keyName) =>{
            $.imsPost('getBusiCateListByDepth', {}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    appObject[parentKeyName] = data.parent_cate_list;
                    appObject[keyName] = data.cate_list;
                });
            });
        },
        /**
         * 업종 가져오기 ( vueApp. customer 필수 )
         * @param appObject
         * @param parentBizCateName 선택된 부모 업종명
         * @param parentBizCateList 업종 리스트 명
         * @param bizCateList 업종 상세 리스트 명
         */
        setBizCate : (appObject, parentBizCateName, parentBizCateList, bizCateList) => {
            $.imsPostWithoutPreload('getListNk',{
                target:'busiCate',
                condition :{}
            }).then((data)=>{

                //appObject[parentBizCateList] = [];
                //appObject[bizCateList] = [];

                let isMatchBizCate = false;
                vueApp[parentBizCateName] = vueApp.customer.parentBusiCateName;
                $.imsPostAfter(data, (data) => {
                    if (data.list.length > 0) {
                        $.each(data.list, (key, val) => {
                            //부모 카테고리 처리
                            if ( 0 >= Number(val.parentBusiCateSno)) {
                                if (isMatchBizCate === false && Number(val.sno) === Number(appObject.customer.busiCateSno)) {
                                    //고객정보에 저장된 업종sno가 상위업종sno인 경우
                                    appObject[parentBizCateName] = val.cateName;
                                }
                                appObject[parentBizCateList].push(val.cateName);//Confirm.
                            } else {
                                if (isMatchBizCate === false && Number(val.sno) === Number(appObject.customer.busiCateSno)) {
                                    isMatchBizCate = true;
                                }
                                appObject[bizCateList].push(
                                    {
                                        'busiCateSno':val.sno,
                                        'cateName':val.cateName,
                                        'parentCateName':val.parentCateName
                                    }
                                );
                            }
                        });
                        //고객정보에 저장된 업종sno가 상위업종sno인 경우 or 업종이 삭제된 경우
                        if (isMatchBizCate === false) {
                            if ( $.isEmpty(appObject[parentBizCateName]) ){
                                appObject[parentBizCateName] = '상위업종 선택';
                            }
                            appObject.customer.busiCateSno = 0;
                        }
                    }
                });
            });
        },
    }

    const CommonService = {
        getList : (type)=>{
            ImsService.getList(type,vueApp[type+'SearchCondition']).then((data)=>{
                if(200 === data.code){
                    vueApp[type+'List'] = data.data.list;
                    vueApp[type+'Page'] = data.data.pageEx;
                    vueApp[type+'Total'] = data.data.page;
                    //Paging Event
                    vueApp.$nextTick(function () {
                        $(`#${type}-page .pagination`).find('a').each(function(){
                            $(this).off('click').on('click',function(){
                                vueApp[type+'SearchCondition'].page = $(this).data('page');
                                CommonService.getList(type);
                            });
                        });
                    });
                }
            });
        },
    }

    /**
     * 미팅 서비스
     * @type {{getList: MeetingService.getList}}
     */
    const MeetingService = {
        getList : ()=>{
            //console.log('검색조건',vueApp.meetingSearchCondition);
            ImsService.getList('newMeeting',vueApp.meetingSearchCondition).then((data)=>{
                if(200 === data.code){
                    vueApp.meetingList = data.data.list;
                    vueApp.meetingPage = data.data.pageEx;
                    vueApp.meetingTotal = data.data.page;
                    //Paging Event
                    vueApp.$nextTick(function () {
                        $('#meeting-page .pagination').find('a').each(function(){
                            $(this).off('click').on('click',function(){
                                vueApp.meetingSearchCondition.page = $(this).data('page');
                                MeetingService.getList();
                            });
                        });
                    });
                }
            });
        },
        openMeetingView : (sno, customerSno)=>{
            const win = popup({
                url: `<?=$meetingRegUrl?>&sno=${sno}&customerSno=${customerSno}`,
                target: 'imsMeeting',
                width: 1200,
                height: 950,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win.focus();
        }
    }

    const ProjectService = {
        saveProject: ( customer, project  )=>{
            //$.imsSave2('');
            $.imsSave()
        },
        getList : ()=>{
            console.log('검색조건',vueApp.projectListSearchCondition);
            ImsService.getList('project',vueApp.projectListSearchCondition).then((data)=>{
                if(200 === data.code){
                    //console.log(data.data);
                    vueApp.projectList = data.data.list;
                    vueApp.projectPage = data.data.pageEx;
                    vueApp.projectTotal = data.data.page;
                    //console.log('프로젝트리스트',vueApp.projectList);
                }
            });
        },
        /*openMeetingView : (sno, customerSno)=>{
            const win = popup({
                url: `<?=$meetingRegUrl?>&sno=${sno}&customerSno=${customerSno}`,
                target: 'imsMeeting',
                width: 1200,
                height: 950,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win.focus();
        }*/
    }


    /**
     * 스케쥴 서비스
     * @type {{getRemainField: (function(*=, *=): (string|boolean))}}
     */
    const ScheduleService = {
        getRemainField : (startDate, endDate)=>{
            const remainDay = $.diffDate(endDate, startDate);
            if (remainDay >= 240) return 'remain240';
            if (remainDay >= 210) return 'remain210';
            if (remainDay >= 180) return 'remain180';
            if (remainDay >= 150) return 'remain150';
            return false;
        }
    }

</script>


<script type="text/javascript">
    /*전역 Function*/

    function postcode_callback(){
        vueApp.items.contactZipcode = $('#zonecode').val();
        vueApp.items.contactAddress = $('#address').val();
    }

    /**
     * 요청 열기
     */
    function openRequest(projectSno, reqType){
        const win = popup({
            url: `popup/ims_request.php?projectSno=${projectSno}&reqType=${reqType}`,
            target: 'imsRequest',
            width: 850,
            height: 550,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 고객정보 열기
     */
    function openCustomerModify(customerSno){
        const win = popup({
            url: `ims_customer_reg.php?popup=y&sno=${customerSno}`,
            target: 'imsCustomer',
            width: 1600,
            height: 700,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 프로젝트정보 열기
     */
    function openProject(projectSno){
        const win = popup({
            url: `<?=$myHost?>/ims/ims_project_reg.php?popup=y&sno=${projectSno}`,
            target: 'imsProject',
            width: 1650,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 미팅정보 열기
     */
    function openMeeting(projectSno){
        const win = popup({
            url: `ims_project_reg.php?popup=y&sno=${projectSno}&status=step10`,
            target: 'imsProject',
            width: 1650,
            height: 950,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 파일 이력 열기
     */
    function openFileHistory(projectSno, fileDiv){
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_file_history.php?projectSno=${projectSno}&fileDiv=${fileDiv}`,
            target: 'imsProduct',
            width: 800,
            height: 650,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    function openFileHistory2(params, fileDiv){
        const revisionCheckList = [
            'customerSno',
            'projectSno',
            'styleSno',
            'eachSno',
        ];
        //console.log(params);
        //console.log(fileDiv);
        const searchParams = [];
        revisionCheckList.forEach((field)=>{
            if( 'undefined' !== typeof params[field] ){
                searchParams.push(field + '=' + params[field]);
            }
        });
        const searchParamsStr = searchParams.join('&');
        const win = popup({
            url: `<?=$myHost?>/ims/popup/ims_file_history.php?fileDiv=${fileDiv}&${searchParamsStr}`,
            target: 'imsProduct',
            width: 800,
            height: 650,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    const setJqueryEvent = ()=>{
        $('.js-select2').select2({
            placeholder: '선택'
        });

        $('.number-only').each(function() {
            // 각 입력 필드에서 개별적으로 이전 값을 관리
            $(this).data('prev', '');

            $(this).on('input', function() {
                // 현재 입력된 값을 가져옴
                const currentValue = $(this).val();
                const previousValue = $(this).data('prev');

                // 숫자로만 이루어졌는지 확인
                if (/^[0-9]*$/.test(currentValue)) {
                    // 숫자만 있을 경우, 이전 값을 현재 값으로 업데이트
                    $(this).data('prev', currentValue);
                } else {
                    // 숫자가 아닌 것이 포함되었을 경우, 이전 값으로 복구
                    $(this).val(previousValue);
                }
            });
        });

    }

    const setAcceptClass = (acceptCode)=>{
        const acceptClassMap = {
            'n' : '',
            'r' : 'text-blue',
            'p' : 'text-green',
            'f' : 'text-danger',
        };
        return acceptClassMap[acceptCode];
    }

    const setConfirmY = (acceptDiv, project)=>{
        //승인
        $.msgPrompt('승인 처리하시겠습니까?','승인메모','', (confirmMsg)=>{
            if( confirmMsg.isConfirmed ){
                setAccept(acceptDiv, 'p', confirmMsg.value).then((data)=>{
                    project[acceptDiv+'Kr'] = data.data.project[acceptDiv+'Kr'];
                    project[acceptDiv] = data.data.project[acceptDiv];
                    const nextStepConfirmData = {
                        20: {
                            title: '기획서가',
                            nextStep: '30',
                            nextStepTitle: '디자인제안',
                        },
                        30: {
                            title: '제안서가',
                            nextStep: '40',
                            nextStepTitle: '샘플제안',
                        },
                        40: {
                            title: '샘플이',
                            nextStep: '50',
                            nextStepTitle: '고객승인대기',
                        },
                    };
                    if( !$.isEmpty(nextStepConfirmData[project.projectStatus]) ){
                        const nextStepData = nextStepConfirmData[project.projectStatus];
                        $.msgPrompt(`${nextStepData.title} 승인 되었습니다.<br>바로 ${nextStepData.nextStepTitle} 단계로 변경하시겠습니까?`,'','상태변경 메모', (confirmMsg)=>{
                            if( confirmMsg.isConfirmed ){
                                const reasonMsg = $.isEmpty(confirmMsg.value)?`${nextStepData.title} 승인되어 변경 처리`:confirmMsg.value;
                                autoSetStatus(project.sno, nextStepData.nextStep,reasonMsg);
                            }
                        });
                    }else{
                        $.msg('승인 되었습니다.', "", "success");
                    }
                });
            }
        });
    }
    const setConfirmN = (acceptDiv, project)=>{
        //반려
        $.msgPrompt('반려사유 입력','','반려 사유 입력', (confirmMsg)=>{
            if( confirmMsg.isConfirmed ){
                if( $.isEmpty(confirmMsg.value) ){
                    $.msg('반려 사유는 필수 입니다.', "", "warning");
                    return false;
                }
                setAccept(acceptDiv, 'f', confirmMsg.value).then((data)=>{
                    project[acceptDiv+'Kr'] = data.data.project[acceptDiv+'Kr'];
                    project[acceptDiv] = data.data.project[acceptDiv];
                    $.msg('반려 처리 되었습니다.', "", "success");
                });
            }
        });
    }

    /**
     * 공임 열기
     */
    function openInnoverPrice(){
        const win = popup({
            url: `/admin/image/iprice3.png`,
            target: 'iprice',
            width:1400,
            height: 760,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    /**
     * 생산 SOP 열기
     */
    function openProductionSopImg(){
        const win = popup({
            url: `/admin/image/production_sop.png`,
            target: 'isop',
            width: 1250,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

    function pageClick(){
        console.log('clicked...');
    }

    /**
     * 연도 리스트 반환
     */ 
    function getYearList(startYear){
        const yearList = JSON.parse(`<?=json_encode($yearList)?>`);
        const rsltYearList = [];
        yearList.forEach((year)=>{
            if( year >= startYear ){
                rsltYearList.push(year);
            }
        });
        return rsltYearList;
    }

    /**
     * JS 라이브러리 정의 코드
     */ 
    const JS_LIB_CODE = {
        codeSeason : JSON.parse(`<?=json_encode($seasonList)?>`),  //시즌 타입
        codeStyle : JSON.parse(`<?=json_encode($codeStyle)?>`),    //스타일 타입
        projectType : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PROJECT_TYPE)?>`),    //프로젝트 타입
        customerStatusMap : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUSTOMER_STATUS)?>`),  //고객 타입
        projectStatusMap : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PROJECT_STATUS)?>`),    //프로젝트 상태
        bidType : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::BID_TYPE)?>`),    //입찰타입
        designWorkType : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE)?>`),    //가능 불가
        yesOrNoType  : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::YES_OR_NO_TYPE)?>`),    //가능 불가
        ableType  : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::ABLE_TYPE)?>`),    //가능 불가
        existType : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::EXIST_TYPE)?>`),   //유 무
        existType2 : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::EXIST_TYPE2)?>`), //무상 유상
        existType3 : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::EXIST_TYPE3)?>`), //있음 없음
        existType4 : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::EXIST_TYPE4)?>`), //유 무 발주시협의
        existType5 : JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::EXIST_TYPE5)?>`), //유상 무상 발주시협의
        ratingType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::RATING_TYPE)?>`),  //상 중 하
        ratingType2: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::RATING_TYPE2)?>`),  //상 중 하 (2)
        paymentType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PAYMENT_TYPE)?>`),//현금 어음
        scheduleShareType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::SCHEDULE_SHARE_TYPE)?>`), //공유 미공유
        processType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PROCESS_TYPE)?>`), //진행 미진행
        packingType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PACKING_TYPE)?>`), //진행 미진행
        payShippingType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PAY_SHIPPING_TYPE)?>`), //배송비 부담
        payFabricType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PAY_FABRIC_TYPE)?>`), //결제X'  ,'y' => '완제품 납품 시 결제
        batchType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::BATCH_TYPE)?>`), //일괄 상시
        paymentType2: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PAYMENT_TYPE2)?>`), //고객사 결제 ,  출고후 결제
        usedType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::USED_TYPE)?>`), //사용 미사용
        afterPaymentType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::AFTER_PAYMENT_TYPE)?>`), //'본사일괄 정산' , 주문자지불
        afterPaymentPeriod: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::AFTER_PAYMENT_PERIOD)?>`), //월별, 분기별 , 정산 없음
        thumbnailType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::THUMBNAIL_TYPE)?>`), //월별, 분기별 , 정산 없음
        memberJoinType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MEMBER_JOIN_TYPE)?>`), //월별, 분기별 , 정산 없음
        contractPayType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CONTRACT_PAY_TYPE)?>`), //월별, 분기별 , 정산 없음
        includeType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::INCLUDE_TYPE)?>`), //월별, 분기별 , 정산 없음
        includeTypeSimple: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::INCLUDE_TYPE_SIMPLE)?>`), //월별, 분기별 , 정산 없음
        sexType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::SEX_TYPE)?>`), //남자, 여자
        custEstimateType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUST_ESTIMATE_TYPE)?>`), //가견적, 확정견적
        custEstimateStatus: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUST_ESTIMATE_STATUS)?>`), //고객승인상태
        styleProcType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::STYLE_PROC_TYPE)?>`), //스타일 진행 상태
        custSampleType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUST_SAMPLE_TYPE)?>`), //고객 샘플타입
        periodType: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PERIOD_TYPE)?>`), //단기,중기,장기

        prjInfo01: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PRJ_INFO_01)?>`), //업체선정기준
        prjInfo02: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PRJ_INFO_02)?>`), //업체선정방법
        prjInfo03: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PRJ_INFO_03)?>`), //변경사유
        prjInfo04: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PRJ_INFO_04)?>`), //세탁구분
        prjInfo05: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::PRJ_INFO_05)?>`), //샘플비용

        custInfo01: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUST_INFO_01)?>`), //로고구분
        custInfo02: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUST_INFO_02)?>`), //명찰구분
        custInfo03: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::CUST_INFO_03)?>`), //명찰구분

        mall1: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_1)?>`), //폐쇄몰설정
        mall2: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_2)?>`), //폐쇄몰설정
        mall3: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_3)?>`), //폐쇄몰설정
        mall4: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_4)?>`), //폐쇄몰설정
        mall41: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_4_1)?>`), //폐쇄몰설정
        mall5: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_5)?>`), //폐쇄몰설정
        mall6: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_6)?>`), //폐쇄몰설정
        mall7: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_7)?>`), //폐쇄몰설정
        mall8: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_8)?>`), //폐쇄몰설정
        mall9: JSON.parse(`<?=json_encode(\Component\Ims\ImsCodeMap::MALL_9)?>`), //폐쇄몰설정
    }

    function getCodeMap(code){
        if(typeof code != 'undefined'){
            return JS_LIB_CODE[code];
        }else{
            return JS_LIB_CODE;
        }
    }

    /**
     * 스크롤 저장
     */
    function saveScrollPosition() {
        //console.log('saveScrollPosition',window.scrollY);
        localStorage.setItem('scrollPosition', window.scrollY);
    }

</script>
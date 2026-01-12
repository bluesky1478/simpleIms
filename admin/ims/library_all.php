<div id="layerDim">
    <div class="sl-pre-loader">
        <div class="throbber-loader"> </div>
    </div>
</div>

<script type="text/javascript">

    <?php if( \SiteLabUtil\SlCommonUtil::isDevId() ) {  ?>
    //$.msg('test','test','success');
    <?php } ?>

    const currentYear = new Date().getFullYear();

    const DATA_MAP = {
        TEST : 'test',
        CUSTOMER : 'customer',
        PROJECT : 'project', //with customer, product
        PROJECT_EXT : 'projectExt', //with customer, product
        PREPARED: 'prepared', //with customer, product
        PRODUCE : 'produce', //with project
        PRODUCT : 'product', //with project, customer.
        STYLE : 'product', //with project, customer.
        STATUS_HISTORY : 'statusHistory',
        UPDATE_HISTORY : 'updateHistory',
        EWORK_HISTORY : 'eworkHistory',
        FILE_HISTORY : 'fileHistory',
        PREPARED_REQ : 'preparedReq',
        CUSTOMER_WITH_A : 'customerWithA', //이런식으로 부가 정보 가져오기 or get 재요청 ?
        SAMPLE : 'sample',
        REQUEST : 'request',
        FACTORY_ESTIMATE : 'factoryEstimate',
        NEW_MEETING : 'newMeeting',
        PRODUCTION : 'production',
        TODO_REQUEST : 'todoRequest',
        TODO_RESPONSE : 'todoResponse',
        TODO_COMMENT : 'todoComment',
        IMS_COMMENT : 'imsComment',
        CUST_ISSUE : 'imsCustomerIssue',
        CUST_ESTIMATE : 'customerEstimate',
        MASTER_STYLE : 'masterStyle',
    };
</script>

<script type="text/javascript">

    var uploadImages = [];

    function addUploadImages(data) {
        uploadImages.push(data);
    }

    function cleanUploadImages() {
        uploadImages = null;
    }

    var oEditors = [];

    $.extend({
        imsPost:async (mode,params)=>{
            params.mode = mode;
            //console.log('call params:',params);
            return $.postAsync('<?=$imsAjaxUrl?>', params, '1');
        },
        /**
         * Post 이 후 공통 처리
         * @param data
         * @param afterFnc
         */
        imsPostAfter:(data, afterFnc)=>{
            if(200 !== data.code){
                $.msg(data.message,'','warning');
            }else{
                afterFnc(data.data);
            }
        },
        imsPost2:async (mode,params,afterFnc)=>{
            params.mode = mode;
            const rslt = $.postAsync('<?=$imsAjaxUrl?>', params, '1');
            rslt.then((data)=>{
                if(200 !== data.code){
                    $.msg(data.message,'','warning');
                }else{
                    if( typeof afterFnc != 'undefined' ){
                        afterFnc(data.data);
                    }
                }
            });
            return rslt;
        },

        imsPostWithoutPreload:async (mode,params)=>{
            params.mode = mode;
            return $.postAsync('<?=$imsAjaxUrl?>', params, '1', false);
        },

        /**
         * Ims Save
         * @param target
         * @param saveData
         * @param afterFnc
         * @returns {Promise<*>}
         */
        imsSave:async (target,saveData,afterFnc)=>{
            const rslt = $.postAsync('<?=$imsAjaxUrl?>', {
                'mode' : 'imsSave',
                'target' : target,
                'saveData' : saveData,
            }, '1');
            rslt.then((data)=>{
                //console.log('save2 result : ', data);
                if(200 !== data.code){
                    $.msg(data.message,'','warning');
                }else{
                    if( typeof afterFnc != 'undefined' ){
                        afterFnc(data.data);
                    }
                }
            });
            return await rslt;
        },

    });

    const setTitleAffix = function(){

    }

    /**
     * 생산관리 저장 벨리데이션
     */ 
    const validProduceSchedule = async function(produce){
        let prevDate = '';
        let validExtDate = '';
        let isErr = false;
        let prevStr = '';
        let errStr = '';

        <?php foreach( $PRODUCE_STEP_MAP as $stepKey => $stepTitle ) { ?>
        validExtDate = produce['prdStep<?=$stepKey?>'];
        if(!$.isEmpty(prevDate) && !$.isEmpty(validExtDate.expectedDt) && prevDate > validExtDate.expectedDt ){
            errStr = `${prevStr} : ${prevDate} <> <?=$stepTitle?> : ${validExtDate.expectedDt}`;
            isErr = true;
        }
        if(!$.isEmpty(validExtDate.expectedDt)){
            prevStr = '<?=$stepTitle?>';
            prevDate = validExtDate.expectedDt;
        }
        <?php } ?>

        if( isErr ){
            const errMsg = "예정 스케쥴에 오류가 있습니다. 강제로 진행하시겠습니까?";
            const errSubMsg = "앞Step의 날짜보다 다음Step의 날짜가 이전일 수 없습니다.<br>" + errStr;
            let isContinue = false;
            await $.msgConfirm(errMsg, errSubMsg).then((confirmData)=> {
                isContinue = confirmData.isConfirmed;
            });
            return isContinue;
        }else{
            return true;
        }
    }

    /**
    * 생산 상태 변경
    */
    const setProduceChangeStep = function(msg, step, snoList){
        $.msgConfirm(msg, '').then((confirmData)=> {
            if (true === confirmData.isConfirmed) {
                //const promptValue = window.prompt("메모입력 : ");
                $.msgPrompt('상태변경 메모 입력','','상태변경 메모', (confirmMsg)=>{
                    if( confirmMsg.isConfirmed ){
                        const promptValue = confirmMsg.value;
                        $.imsPost('setBatchProduceChangeStep',{
                            snoList : snoList,
                            changeStep : step,
                            reason : promptValue,
                        }).then(()=>{
                            location.reload();
                        });
                    }else{
                        $.imsPost('setBatchProduceChangeStep',{
                            snoList : snoList,
                            changeStep : step,
                            reason : '',
                        }).then(()=>{
                            location.reload();
                        });
                    }
                });
            }
        });
    }

    //신규 생산스케쥴
    const currentProductionStat = JSON.parse('<?=$imsProductionCount?>');
    //console.log(currentProductionStat);
    for( let idx in currentProductionStat ){
        $('.imsps-'+idx).text(`(${currentProductionStat[idx]})`);
    }

    //신규 요청(퀄리티, 가견적, 생산가)
    const currentRequestStat = JSON.parse('<?=$imsRequestCount?>');
    for( let idx in currentRequestStat ){
        $('.imsps-'+idx).text(`(${currentRequestStat[idx]})`);
    }
    
    //To-DO List 카운팅
    const currentTodoRequestStat = JSON.parse('<?=$imsTodoRequestCount?>');
    $('.imsps-approval').text(`(${currentTodoRequestStat['approval']})`);
    $('.imsps-request').text(`(${currentTodoRequestStat['request']})`);
    $('.imsps-inbox').text(`(${currentTodoRequestStat['inbox']})`);

    <?php if( !empty($imsProduceCompany) ) { ?>
    /*생산처 화면 설정*/
    function goLogout(){
        confirm(dialog_confirm('로그아웃 하시겠습니까?', function (result) {
            if (result) {
                location.href = "../base/login_ps.php?mode=logout";
            }
        }));
    }
    $('.navbar-nav.reform').find('li').each(function(idx){
        if( 0 !== idx ){
            $(this).remove();
        }
        $('.list-inline').html('<li class="hover-btn cursor-pointer" style="font-size:15px; color:#fff; " onclick="goLogout()"><?=\Session::get('manager.managerNm')?>님</li>');
    });

    $('.panel-heading').each(function(index){
         if( $(this).html().indexOf('생산') !== -1 || $(this).html().indexOf('이노버요청작업') !== -1  || $(this).html().indexOf('결재') !== -1){
             $('.list-group').eq(index).find('a').each(function(){
                 if( $(this).prop('href').indexOf('initStatus=4') !== -1 || $(this).prop('href').indexOf('workReport') !== -1  ){
                     $(this).closest('.list-group-item').remove();
                 }
             });
         }else if($(this).html().indexOf('프로젝트 관리') !== -1 ) {
             $('.list-group').eq(index).find('a').each(function(){
                 console.log($(this).prop('href'));
                 if( $(this).prop('href').indexOf('ims_list_qc.php') !== -1 ){
                 }else{
                     $(this).closest('.list-group-item').remove();
                 }
             });
         }else{
             $(this).remove();
             $('.list-group').eq(index).hide();
         }
    });
    <?php } ?>


    $(()=>{

        $('#sort').change(function(){
            $('#list-sort').val($(this).val());
        });

        $('.btn_goseller').remove();
        <?php if( !empty($imsProduceCompany) ) { ?>
        $('.expire_info').remove();
        $('.navbar-header').append(`<input type="button" value="파우치 매뉴얼 다운로드" class="btn btn-white btn-icon-pdf mgt10" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/ims-manual.pdf')?>&fileName=<?=urlencode('(메뉴얼) 파우치 사용 메뉴얼v1.pdf')?>'">`);
        <?php } ?>

        <?php if( !empty(\SiteLabUtil\SlCommonUtil::isDev()) ) { ?>
        $('.navbar-header').css('background-color','#ff0000');
        $('.navbar-header').html('<div class="font-20" style="color:#fff; font-weight: bold; padding : 15px 60px 10px 30px">INNOVER TEST<span>');
        <?php } ?>

        /**
         * 메모 더보기
         */ 
        const openMemo = function($el){
            const sno = $el.data('sno');
            const div = $el.data('div');
            const url = `call_view.php?sno=${sno}&div=${div}`;
            openCallView(url);
        };
        $('.btn-more-memo').on('click',function(){
            openMemo($(this));
            return false;
        });

        $('.btn-pop-customer-info').on('click',function(){
            const sno = $(this).data('sno');
            openCustomer(sno);
            return false;
        });

        $('.btn-call-with').on({
            'click': function(){
                openMemo($(this));
                return false;
            },'mouseover' :function (e) { // 메모보기 클릭 시
                const $el = $(this);
                const sno = $(this).data('sno');
                const div = $(this).data('div');
                const top = ($(this).position().top) - 50;  //보기 버튼 top
                const left = ($(this).position().left) - 660; //보기 버튼의 left
                $.post("layer_order_add_info", {
                    sno: sno,
                    div: div
                }, function (result) {
                    $el.after('<div class="memo_layer"></div>');
                    $('.memo_layer').html(result);
                    $('.memo_layer').css({
                        "top": top
                        , "left": left
                        , "right": "0px"
                        , "position": "absolute"
                        , "width": "650px"
                        , "overflow": "hidden"
                        , "height": "auto"
                        , "z-index": "999"
                        , "border": "1px solid #cccccc"
                        , "background": "#ffffff"
                    }).show();
                }, "html");
            },
            'mouseout'  :function (e) {
                $('.memo_layer').remove();
            }
        });


        <?php if( empty($imsProduceCompany) ) { ?>
            /*생산가ON OFF*/
            const saleCostDisplay = getSaleCostDisplay();
            if( $.isEmpty(saleCostDisplay) || 'y' === saleCostDisplay ){
                $('#gnbAnchor').prepend ('<div class="float-side-menu" style="background-color: #0c4da2"  data-type=""><a href="#" onclick="setSaleCostDisplayOff();location.reload()">S:OFF</a></div>');
            }else{
                $('#gnbAnchor').prepend ('<div class="float-side-menu" style="background-color: #1c4827" data-type=""><a href="#" onclick="setSaleCostDisplayOn();location.reload()">S:ON</a></div>');
            }
        <?php } ?>

        //$('#gnbAnchor').prepend ('<div class="float-side-menu" data-type=""><a href="#" onclick="openTodoRequestWrite()">TODO</a></div>');


        $('.gnb-idinfo').append('(<?=$deptName?>)');
        
    });

    function setSaleCostDisplayOn(){
        $.cookie('setSaleCostDisplay','y');
    }
    function setSaleCostDisplayOff(){
        $.cookie('setSaleCostDisplay','n');
    }
    function getSaleCostDisplay(){
        return $.cookie('setSaleCostDisplay');
    }

</script>

<div id="app-hide" style="width:100%;height:100%; background-color: #fff;position: absolute;top:0;left:0;z-index:999;display: none"></div>

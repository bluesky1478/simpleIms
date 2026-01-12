<script type="text/javascript">

    $(appId).hide();
    let init = true;
    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'][0],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
    };
    const defaultMultiKey2= {
        key : 'prj.sno',
        keyword : '',
    };

    $(()=>{
        //검색 기본 시작 ---------------
        const commonSearchDefault = {
            key : '<?=gd_isset($requestParam['key'][0],'cust.customerName')?>',
            keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
            key2 : 'cust.customerName',
            keyword2 : '',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
            multiCondition : 'OR',
            projectYear : '',
            projectSeason : '',
            projectTypeChk : [],
            productionChk : [],
            orderProgressChk : [<?=$chkOrderProgress?>],
            //orderProgressChk : [],
            isComplete : '',
            listStatus : '<?=$requestParam['status']?>',
            page : 1,
            pageNum : 20,
            sort : 'P2,desc' //정렬
        }

        <?php if(50 == $requestParam['status'] || 10 == $requestParam['status'] ) { ?>
        //발주/진행준비 단계 기본 정렬 (희망납기)
        commonSearchDefault.sort = 'P3,asc';
        <?php } ?>
        <?php if(15 == $requestParam['status']) { ?>
        //협상단계 기본 정렬  (매출)
        commonSearchDefault.sort = 'P4,desc';
        <?php } ?>


        /*$.cookie('projectSearchCondition')*/
        <?php if('y' === $isReload ) { ?>

        const searchDefault = $.copyObject(JSON.parse($.cookie('projectSearchCondition')));
            console.log('reload option', searchDefault);

        <?php }else{ ?>

            const searchDefault = $.copyObject(commonSearchDefault);
            <?php if( empty($requestParam['initStatus'])) { ?>
            searchDefault.isExcludeRtw = true;
            <?php } else { ?>
            searchDefault.isExcludeRtw = false;
            <?php } ?>

            //다음시즌
            searchDefault.isExcludeNextSeason = false;

            /*기성복?*/
            <?php if( 4 == $requestParam['initStatus']) { ?>
            searchDefault.projectType = '4';
            <?php } else { ?>
            searchDefault.projectType = 'all';
            <?php } ?>

            searchDefault.isDelay = false;
            searchDefault.projectStatus = <?=gd_isset($requestParam['initStatus'],4)?>;  //2-3-4
            searchDefault.prjListCompanySno = '<?=!empty($imsProduceCompany)? $managerSno :''?>';
            searchDefault.packingYn = '0';
            searchDefault.isBookRegistered = '0';
            searchDefault.use3pl = '0';
            searchDefault.useMall = '0';
            searchDefault.startDt = '';
            searchDefault.endDt = '';
            searchDefault.searchDateType = '';
            searchDefault.deliveryStatus = '';
            searchDefault.delayStatus = '';

            delete searchDefault.status;
        <?php } ?>

        console.log('search default : ',searchDefault);

        //추가 되는게 있다면 별도 추가.
        //검색 기본 종료 ---------------

        const init = ()=>{

            const titleMap = {
                0: '전체',
                4: '스케쥴관리',
                2: '스케쥴입력요청',
                3: '스케쥴확정대기',
                5: '생산완료',
                1: '생산준비(미발주)',
            };

            const initParams = {
                data : {
                    listAllCheck : false,
                    product : [],
                    isFactory : <?=!empty($imsProduceCompany)?'true':'false'?>,
                    isList : true,
                    tabMode : 'project',  //'cost', // qb,  estimate

                    //생산 리스트
                    projectCheckList : [],
                    projectList : [],
                    projectTotal : ImsProductService.getTotalPageDefault(),
                    projectPage : '',
                    projectSearchCondition : $.copyObject(searchDefault),
                    viewModeProject : 'v',
                    projectView : null,
                    scheduleModify : {},
                },
                mounted : (vueInstance)=>{
                    //NextThick
                    vueApp.$nextTick(function () {
                        //project List 갱신.
                        ImsProjectService.getListProjectWithAddInfo(1);

                        //틀고정 시작 
                        const cloneElement1 = $('#main-table').find('colgroup').eq(0).clone();
                        const cloneElement2 = $('#main-table').find('thead').eq(0).clone();
                        $('#affix-show-type2').find('.table').eq(0).append(cloneElement1);
                        $('#affix-show-type2').find('.table').eq(0).append(cloneElement2);
                        const setAffix = function(){
                            if ($(document).scrollTop() > 400) {
                                $('#affix-show-type2').show();
                                $('#affix-show-type1').hide();
                            }else{
                                $('#affix-show-type1').show();
                                $('#affix-show-type2').hide();
                            }
                        }
                        $(window).resize(function (e) {
                            setAffix();
                        });
                        $(window).scroll(setAffix);

                        $('#gnbAnchor').prepend ('<div class="float-side-menu cursor-pointer hover-btn" onclick="vueApp.searchProject()" style="color:#fff; background-color:#666" data-type="" >검색</div>');

                        //틀고정 종료
                        console.log('mounted complete..' , vueApp.projectList);
                    });
                },
                methods : {
                    reOrder : (projectSno, status)=> {
                        $.msgConfirm('리오더를 진행합니까?', '').then(function (result) {
                            if (result.isConfirmed) {
                                $.imsPost('reOrderProject', {
                                    projectSno: projectSno,
                                    status: status,
                                }).then((data) => {
                                    if (200 === data.code) {
                                        $.msg('프로젝트 생성 완료', data.data, 'success');
                                    }
                                });
                            }
                        })
                    },
                    allOpen : ()=>{
                        $('.btn-style-on').click();
                    },
                    allClose : ()=>{
                        $('.btn-style-off').click();
                    },
                    listDownload : ()=>{
                        //Not Ajax.
                        //console.log(vueApp.projectSearchCondition);
                        const downloadSearchCondition = $.copyObject( vueApp.projectSearchCondition );
                        downloadSearchCondition.pageNum = 15000;
                        location.href='ims_list.php?simple_excel_download=1&' + $.objectToQueryString(downloadSearchCondition);
                    },
                    projectConditionReset : () => {
                        const currentSearch = $.copyObject(searchDefault);
                        currentSearch.keyword = '';
                        vueApp.projectSearchCondition = currentSearch;
                        ImsProjectService.getListProjectWithAddInfo(1);
                    },
                    searchProject : ImsProjectService.getListProjectWithAddInfo,
                    toggleAllCheck : ()=>{
                        if( vueApp.listAllCheck ){
                            vueApp.projectCheckList = [];
                        }else{
                            vueApp.projectCheckList = vueApp.projectList.map(project => project.sno);
                        }
                    },
                    openModifySchedule : function(){
                        if( vueApp.projectCheckList.length > 0 ){
                            vueApp.scheduleModify = $.copyObject(vueApp.projectList.find(obj => obj.sno === vueApp.projectCheckList[0]));
                            vueApp.scheduleModify.checkCnt = vueApp.projectCheckList.length;
                            vueApp.scheduleModify.checkList = vueApp.projectCheckList;
                            //console.log('수정대상', vueApp.scheduleModify);
                            $('#modalScheduleModify').modal('show');
                        }else{
                            $.msg('일괄 수정 대상을 선택해주세요.','','warning');
                        }
                    },
                    showStyle : function(projectData, addCnt){
                        //스타일 정보를 가져온다.
                        if( typeof projectData.styleList.dataList != 'undefined' ){
                            projectData.styleShow=true;
                            projectData.addRowspan=addCnt;
                        }else{
                            $.imsPost('getProjectProduct',{
                                projectSno:projectData.sno,
                                searchCondition : vueApp.projectSearchCondition
                            }).then((data)=>{
                                if(200 === data.code){
                                    console.log( '스타일 정보 불러오기' ,data.data );
                                    //tab : 0샘플, 1QB, 2가견적, 3생산가, 4생산관리

                                    const styleList = [];
                                    data.data.forEach((styleData)=>{
                                        const styleEachData = [];

                                        const styleName = styleData.styleFullName;
                                        //  + `<div class="text-muted">${styleData.styleCode}(${styleData.sno})</div>`

                                        <?php if($requestParam['status'] != 15 ) { ?>
                                        styleEachData.push({'value' : styleName,'colspan' : 1   ,'type':'prdOpen', 'link':styleData.sno, 'tabMode':-1, 'addClass':'text-left pdl10', 'styleCode':`${styleData.styleCode}(${styleData.sno})`});
                                        <?php }else{ ?>
                                        styleEachData.push({'value' : styleData.styleFullName + ' (' + $.setNumberFormat(styleData.prdExQty) + '개)' ,'colspan' : 1   ,'type':'prdOpen', 'link':styleData.sno, 'tabMode':-1, 'addClass':'text-left pdl10'});
                                        <?php } ?>

                                        <?php if($requestParam['status'] != 15 ) { ?>
                                        styleEachData.push({'value' : styleData.fabricStatusKr,'colspan' : 1,'type':'prdOpen', 'link':styleData.sno, 'tabMode':1, 'addClass':''});
                                        styleEachData.push({'value' : styleData.btStatusKr,'colspan' : 1    ,'type':'prdOpen', 'link':styleData.sno, 'tabMode':1, 'addClass':''});
                                        styleEachData.push({'value' : $.setNumberFormat(styleData.prdExQty) ,'colspan' : 1,'class':'', 'addClass':''});
                                        <?php } ?>

                                        //const currentPrice = 50000;
                                        const targetPrice = $.setNumberFormat(styleData.targetPrice);
                                        const targetPrdCost = $.setNumberFormat(styleData.targetPrdCost);

                                        <?php if(!empty($requestParam['status']) && 90 <> $requestParam['status'] ) { ?>
                                        styleEachData.push({
                                            'value' : `타겟단가:${targetPrice}<br>타겟생산:${targetPrdCost}`,
                                            'colspan' : 2,
                                            'class':'text-left pdl5 font-11'
                                            , 'addClass':''
                                        });
                                        <?php } ?>

                                        <?php if(empty($requestParam['status']) || $requestParam['status'] >= 20 ) { ?>
                                        styleEachData.push({'value' : $.setNumberFormat(styleData.prdCost)+'원'   ,'colspan' : 1,'type':'prdOpen', 'link':styleData.sno, 'tabMode':3, 'addClass':''});
                                        styleEachData.push({'value' : $.setNumberFormat(styleData.salePrice)+'원' ,'colspan' : 1,'type':'prdOpen', 'link':styleData.sno, 'tabMode':-1, 'addClass':''});
                                        styleEachData.push({'value' : styleData.msMargin+'%' ,'colspan' : 1, 'addClass':''});
                                        <?php } ?>


                                        /*샘플/작지*/
                                        <?php if(empty($requestParam['status']) || $requestParam['status'] >= 40 ) { ?>
                                        styleEachData.push({'value' : $.setNumberFormat(styleData.sampleCnt)+'개' ,'colspan' : 1,'type':'prdOpen', 'link':styleData.sno, 'tabMode':0, 'addClass':''});

                                        //작업지시서
                                        if( null != styleData.file.fileWork.files){

                                            styleEachData.push(
                                                {
                                                    'value' : styleData.file.fileWork.files ,
                                                    'colspan' : 1,
                                                    'type':'file',
                                                    'addClass':'',
                                                    'link':styleData.sno,
                                                    'projectSno':styleData.projectSno,
                                                    'customerSno':styleData.customerSno,
                                                    'eachSno':styleData.sno,
                                                    'workStatus':styleData.workStatus,
                                                }
                                            );

                                        }else{

                                            styleEachData.push(
                                                {
                                                    'value' : styleData.file.fileWork.files ,
                                                    'colspan' : 1,
                                                    'type':'work',
                                                    'addClass':'',
                                                    'link':styleData.sno,
                                                    'projectSno':styleData.projectSno,
                                                    'customerSno':styleData.customerSno,
                                                    'eachSno':styleData.sno,
                                                    'workStatus':styleData.workStatus,
                                                }
                                            );

                                        }

                                        //workStatus : 2 <== 완료
                                        //

                                        /*styleEachData.push({'value' : styleData.inlineStatusKr ,'colspan' : 1,'type':'prdOpen', 'link':styleData.sno, 'tabMode':4});*/
                                        <?php } ?>

                                        styleList.push(styleEachData);
                                    });

                                    projectData.styleList = {
                                        'titles' : [ //FIXME : 일단 스크립트에서 고정하고 필요시 서비스를 변경
                                            {'name':'스타일명', 'colspan':1, 'class':'text-left pdl10'},

                                            <?php if($requestParam['status'] != 15 ) { ?>
                                            {'name':'퀄리티', 'colspan':1, 'class':''},
                                            {'name':'BT', 'colspan':1, 'class':''},
                                            {'name':'수량', 'colspan':1, 'class':''},
                                            <?php } ?>

                                            <?php if(!empty($requestParam['status']) && 90 <> $requestParam['status'] ) { ?>
                                            {'name':'단가/타겟가', 'colspan':2, 'class':''},
                                            <?php } ?>

                                            <?php if( empty($requestParam['status']) || $requestParam['status'] >= 20 ) { ?>
                                            {'name':'생산가', 'colspan':1, 'class':''},
                                            {'name':'판매가', 'colspan':1, 'class':''},
                                            {'name':'마진', 'colspan':1, 'class':''},
                                            <?php } ?>

                                            <?php if( empty($requestParam['status']) || $requestParam['status'] >= 40 ) { ?>
                                            {'name':'샘플', 'colspan':1, 'class':''},
                                            {'name':'작지', 'colspan':1, 'class':''},
                                            <?php } ?>
                                        ],
                                        'dataList' : styleList,
                                    };
                                    projectData.styleShow=true;
                                    projectData.addRowspan=addCnt;
                                }else{
                                    $.msg(data.msg,'','warning');
                                }
                            });
                        }
                    },
                    hideStyle : function(projectData, removeCnt){
                        projectData.styleShow=false;
                        projectData.addRowspan=0;
                    },
                    // 회계 반영
                    setBookRegistered :(ynFlag)=>{
                        console.log('setBookRegistered');
                        if(0>=vueApp.projectCheckList.length){
                            $.msg('선택된 프로젝트가 없습니다.','','warning');
                            return false;
                        }
                        $.imsPost2('setBookRegistered',{
                            'projectCheckList'   : vueApp.projectCheckList,
                            'isBookRegistered' : ynFlag,
                        },()=>{
                            refreshProjectList();
                            $.msg('처리 완료','','success').then(()=>{
                            });
                        });
                    },

                },
                computed : {
                    sizeOptionQtyTotal() {
                        //생산준비
                        return 0;
                    }
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }
        init();
    });

    
    function refreshProjectList(){
        ImsProjectService.getListProjectWithAddInfo();
    }
    function refreshProject(){
        ImsProjectService.getListProjectWithAddInfo();
    }

</script>
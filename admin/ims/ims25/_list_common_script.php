<script type="text/javascript">
    $(appId).hide();
    //const industrySplitMap = JSON.parse('<?=$industrySplitMap?>');
    const managerMap = JSON.parse('<?=json_encode($managerList)?>');

    /**
     * (공통) 리스트 데이터
     */
    const commonListData = {
        commentMap : [],
        tmMap : [], //필요시 사용
        layer: {
            visible: false,
            loading: false,
            data: {}
        },
    };

    /**
     * (공통) 리스트 메소드
     */
    const commonListMethods = {
        /* 담당 관리자 검색 */
        setManager : (sno)=>{
            vueApp.searchCondition.searchManager = sno;
            vueApp.refreshList(1);
        },
        openStyle(each) {
            vueApp.layer.loading = true;
            vueApp.layer.data = $.copyObject(each);
            vueApp.layer.data.styleList = [];
            $.imsPost('getQcList', {projectSno:each.sno,viewType:'popup'}).then((rslt)=>{ //기존 발주 리스트 활용
                $.imsPostAfter(rslt,(rslt)=>{``
                    vueApp.layer.data.styleList = rslt;
                    //console.log('오픈 프로젝트 정보',vueApp.layer.data.styleList);
                    vueApp.layer.loading = false;
                    vueApp.layer.visible = true;
                });
            });
        },
        closeStyle() {
            vueApp.layer.visible = false;
        },
        listDownload:(type)=>{
            const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
            const queryString = $.objectToQueryString(downloadSearchCondition);
            location.href=`ims25_list${type}.php?simple_excel_download=1&` + queryString;
        },
    };

    /**
     * (공통) 리스트 computed
     */
    const commonListComputed = {
        popStyleTotal() {
            let priceTotal = 0;
            let costTotal = 0;
            for (let prdIdx in vueApp.layer.data.list) {
                priceTotal += Number(vueApp.layer.data.list[prdIdx]['salePrice']);
                costTotal += Number(vueApp.layer.data.list[prdIdx]['prdCost']);
            }
            return {
                priceTotal: priceTotal,
                costTotal: costTotal,
            }
        },
    }

    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'][0],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
    };
    const defaultMultiKey2= {
        key : 'prj.sno',
        keyword : '',
    };

    //기본 검색
    const commonSearchDefault = {
        key : '<?=gd_isset($requestParam['key'][0],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
        multiKey : [
            $.copyObject(defaultMultiKey1),
            $.copyObject(defaultMultiKey2),
        ],
        multiCondition : 'OR',
        page : 1,
        pageNum : 20,
        sort : 'P2,desc', //정렬
        projectTypeChk : [], //프로젝트 타입
        salesStatusChk : [], //프로젝트 타입
        searchManager : '',
        customerStatus : [],
        chkUseMall : false,
        chkUse3pl : false,
        chkPackingYn : false,
        directDeliveryYn : false,
        parentBusiCateSno : 0,
        busiCateSno : 0,
        year : '',
        season : '',
        targetSalesYear : '',
    }

    const listSearchDefaultData = $.copyObject(commonSearchDefault);
    //console.log( listSearchDefaultData );
    //let searchInitData = $.copyObject(commonSearchDefault);
    //const listSearchDefaultData  = searchInitData;

    /**
     *  리스트 검색
     */
    function refreshListExtFnc(){
        vueApp.refreshList(1);
    }

    /**
     * (리스트 공통) 리스트 검색 후 처리
     */
    const getListAfter = ()=>{
        //틀고정 이벤트
        if($.isEmpty($("#affix-show-type2").html())){
            let clonedElement = $("#list-main-table").clone(); // 복제
            clonedElement.find("tr:not(:first)").remove();
            clonedElement.appendTo("#affix-show-type2"); // 복제된 요소를 body에 추가
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
        }
    }

    /** 코멘트 데이터 가져오기 */
    const setCommentMap = ()=>{
        const projectSnoList = [];
        vueApp.listData.forEach((data)=>{
            projectSnoList.push(data.sno);
        });
        if(projectSnoList.length > 0){
            $.imsPost2('getCommentListData',{projectSnoList}).then((data)=>{
                vueApp.commentMap = data.data;
                //console.log(vueApp.commentMap);
            });
        }
    }

    /** 코멘트 데이터 가져오기 */
    const setTmMap = ()=>{
        const projectSnoList = [];
        vueApp.listData.forEach((data)=>{
            projectSnoList.push(data.sno);
        });
        if(projectSnoList.length > 0){
            $.imsPost2('getTmListData',{projectSnoList}).then((data)=>{
                vueApp.tmMap = data.data;
            });
        }
    }

    /** 리스트 기본 설정 */
    const getIms25ListFoundationData = (listAfterFnc)=>{
        const searchDefault = {...listSearchDefaultData,...allListSearchDefaultData};
        const serviceData = {};
        /* 기초 데이터 구조 */
        ImsBoneService.setData(serviceData,{...commonListData,...eachListData});
        /* 메소드 정의 */
        ImsBoneService.setMethod(serviceData,{...commonListMethods,...eachListMethod});
        /* List Computed 정의 */
        ImsBoneService.setComputed(serviceData, {...commonListComputed,...eachListComputed});
        /* 리스트 서비스 시작*/
        const listService = new ImsListService(mainListPrefix, searchDefault, getListData, listAfterFnc);
        return {
            listService : listService,
            serviceData : serviceData,
        };
    }

</script>



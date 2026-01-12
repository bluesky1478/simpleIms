<script type="text/javascript">
    const mainListPrefix = 'style_plan_ref';
    const listSearchDefaultData = {
        // multiKey : [{
        //     key : 'mate.materialName',
        //     keyword : '',
        // }],
        // multiCondition : 'OR',
        sTextboxSchRefName : '',
        aChkboxSchRefGender : [],
        aChkboxSumSchRefType : [],
        aChkboxSchRefSeason : [],
        aChkboxSchRefStyle : [],
        sTextboxRangeStartSchRefUnitPrice : '',
        sTextboxRangeEndSchRefUnitPrice : '',
        //부가정보
        'aChkboxSchB1.infoSno' : [],
        'aChkboxSchB2.infoSno' : [],
        'aChkboxSchB3.infoSno' : [],
        'aChkboxSchB4.infoSno' : [],
        //메인원단
        sTextboxRangeStartSchMainFabricUnitPrice : '',
        sTextboxRangeEndSchMainFabricUnitPrice : '',
        sRadioSchMainFabricOnHandYn : 'all',
        //원부자재
        'sTextboxSchMate.materialName' : '',
        'aChkboxSchMate.materialType' : [],
        'sTextboxSchMate.fabricMix' : '',
        'sTextboxSchAfterMake' : '',
        //고객사
        'sTextboxSchCustinfo.customerName':'',
        'sRadioSchCate1.sno' : 'all',
        sRadioSchBusiCateSno : 'all',

        // sRadioSchIsBookRegistered : 'n',
        // aChkboxSchProjectStatus : [90,91],
        // sTextboxRangeStartSchCpProductionOrder : '',
        // sTextboxRangeEndSchCpProductionOrder : '',
        page : 1,
        pageNum : 10,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListStylePlanRef';
        return ImsNkService.getList('stylePlanRef', params);
    };

    function refreshRefStylePlanList() {
        vueApp.refreshList(1);
    }

    $(()=>{

        $('title').html('스타일 기획 레퍼런스 관리');

        const serviceData = {};
        ImsBoneService.setData(serviceData, {
            ooAppendList : {}, //부가정보리스트 : ooAppendList[타입] = {~~~}
            oParentCateList : {}, //상위업종 리스트
            oCateList : {}, //세부업종 리스트
            obFlagFoldSch : {'1': false, '2': false, '3': false, '4': false, '5': false}, //검색필터 접기(===true)/펼치기(===false)
        });

        ImsBoneService.setMethod(serviceData, {


            //엑셀 다운로드
            // listDownload : (type)=>{
            //     const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
            //     const queryString = $.objectToQueryString(downloadSearchCondition);
            //     location.href='ims_account_list.php?simple_excel_download='+type+'&' + queryString;
            // },
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            ImsNkService.getList('refStylePlanAppendInfoSimple', {}).then((data)=>{
                $.imsPostAfter(data, (data) => {
                    vueApp.ooAppendList = data;

                    //업종(상위업종, 세부업종) 가져오기
                    $.imsPost('getBusiCateListByDepth', {}).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            vueApp.oParentCateList = data.parent_cate_list;
                            vueApp.oCateList = data.cate_list;
                        });
                    });
                });
            });
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
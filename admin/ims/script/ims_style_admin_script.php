<script type="text/javascript">

    const mainListPrefix = 'style';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName', //기본:고객명
            keyword : '',
        }],
        multiCondition : 'OR',
        projectYear : '',
        projectSeason : '',
        projectTypeChk : [0,1,2,3,5,6],
        productionChk : [],
        orderProgressChk : [<?=$chkOrderProgress?>],
        eworkDataChk : [],
        page : 1,
        pageNum : 50,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);
        return $.imsPost('getListStyle', params);
    };

    //외부에서 실행
    function refreshList(){
        vueApp.refreshList(1);
    }

    $(()=>{

        $('title').html('스타일 리스트');
        
        $(appId).hide();
        const serviceData = {};
        ImsBoneService.setMethod(serviceData, {
            //엑셀 다운로드
            listDownload : (type)=>{
                //Not Ajax.
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                // /console.log(queryString);
                location.href=`ims_style_admin.php?simple_excel_download=${type}&` + queryString;
            },
        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //style , styleSearchCondition
        listService.init(serviceData);
    });

//--

</script>
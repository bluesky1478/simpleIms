<script type="text/javascript">
    const mainListPrefix = 'customer_estimate';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',
        sRadioSchEstimateType : '',
        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListCustomerEstimateNk';
        return ImsNkService.getList('customerEstimateNk', params);
    };

    $(()=>{
        
        $('title').html('고객견적 관리');
        
        const serviceData = {};
        ImsBoneService.setMethod(serviceData, {
            //엑셀 다운로드
            listDownload : (type)=>{
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href='ims_estimate_list.php?simple_excel_download='+type+'&' + queryString;
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
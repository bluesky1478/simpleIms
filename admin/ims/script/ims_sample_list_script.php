<script type="text/javascript">
    const mainListPrefix = 'product_sample';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',
        aChkboxSchSampleFactorySno : [],
        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListProductSample';
        return ImsNkService.getList('productSample', params);
    };

    $(()=>{
        
        $('title').html('샘플 정보 관리');
        
        const serviceData = {};

        // ImsBoneService.setData(serviceData,{
        // });
        ImsBoneService.setMethod(serviceData, {
            popSampleDetail : (sno)=>{
                const win = popup({
                    url: `<?=$myHost?>/ims/popup/ims_pop_product_sample_detail.php?sno=${sno}`,
                    target: 'imsProductSampleDetail' + sno,
                    width: 1550,
                    height: 900,
                    scrollbars: 'yes',
                    resizable: 'yes'
                });
                win.focus();
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>
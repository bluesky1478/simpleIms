<script type="text/javascript">
    /**
     * 스타일/상품 관련 서비스
     */
    const ImsFabricService = {

        downloadFabricRequestForm : (customerName, project, product, fabricView)=>{
            const refineProject = $.getObjAvailElement(project,[
                'produceCompany', // 업체명
            ]);
            const refinePrd = $.getObjAvailElement(product,[
                'sno',
                'productName', //제품품목
                'styleCode', //S/NO
                'prdExQty', //수량
                'msDeliveryDt', //납기
                'fileThumbnail', //이미지URL
            ]);
            const refineFabric = $.getObjAvailElement(fabricView,[
                'attached', // 부착위치
                'afterMake', // 후가공
                'color', // 색상
                'fabricMix', // 혼용률
                'fabricName', // 원단명
                'fabricWidth', // 폭
                'meas', // 가요척
                'position', // 위치
                'spec', // 규격
                'weight', // 중량
            ]);

            const reqParamObj =  Object.assign({}, refineProject, refinePrd, refineFabric);
            reqParamObj.customerName = customerName; //고객사
            const reqParamStr = $.objectToQueryString(reqParamObj);
            location.href='ims_ps.php?mode=downloadFabricRequestForm&' + reqParamStr;
        },

    }

</script>
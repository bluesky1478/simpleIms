<script type="text/javascript">
    const styleField = {
        //사전기획 고객 스타일 필드 설정
        preSales : [
            { title : '시즌'  , type : 'c', name:'prdSeason', col:'6', class:'font-11 ' },
            { title : '스타일', type : 'c', name:'prdStyle', col:'14', class:'font-11 ' },
            { title : '구분'  , type : 's', name:'addStyleCode', col:'10', class:'font-11 ' },
            { title : '품목명', type : 's', name:'productName', col:'', class:'font-11 pdl5 ta-l' },
            { title : '예정수량', type : 'i', name:'prdExQty', col:'12', class:'font-11 pdl5 ta-c' },
            { title : '현재가', type : 'i', name:'currentPrice', col:'14', class:'font-11 pdl5 ta-c' },
            { title : '추정매출', type : 'c', name:'extUnitPrice', col:'12', class:'font-11 pdl5 ta-c' },
        ],

        //메인 스타일 필드 설정
        main : [
            { title : '이미지', type : 'c', name:'fileThumbnail', col:'5', class:'' },
            { title : '상품명', type : 'c', name:'productName', col:'18', class:'pdl5 ta-l' },
            { title : '기획', type : 'c', name:'plan', col:'4', class:'' },
            { title : '샘플', type : 'c', name:'sample', col:'7', class:'' },
            { title : '납기일', type : 'c', name:'deliveryDt', col:'9', class:'' },
            { title : '수량', type : 'c', name:'prdQty', col:'', class:'' },
            { title : '생산가<br>(부가세 제외)', type : 'c', name:'prdCost', col:'9', class:'' },
            { title : '판매가<br>(부가세 제외)', type : 'c', name:'prdPrice', col:'9', class:'' },
            { title : '마진', type : 'c', name:'margin', col:'4', class:'' },
            { title : '생산MOQ', type : 'c', name:'prdMoq', col:'4', class:'' },
            { title : '원단MOQ', type : 'c', name:'fabricMoq', col:'4', class:'' },
            { title : '퀄리티', type : 'c', name:'quality', col:'5', class:'' },
            { title : 'BT', type : 'c', name:'bt', col:'', class:'5' },
            { title : '작업지시서', type : 'c', name:'work', col:'7', class:'' },
        ],
    };

    const getStyleField = (type)=>{
        const rsltField = styleField[type];
        return $.setColWidth(95, rsltField);
    };
</script>
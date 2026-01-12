<!--차트 -->
<script type="text/javascript">

    var compareStatData = $.parseJSON('<?=$compareStat['chartData']?>');

    var getCompareStatData = function(goodsNo){
        var returnData = new Array();
        returnData['title'] = new Array();
        returnData['data'] = new Array();
        if( $.isEmpty(goodsNo)){
            for(var dataGoodsNo in  compareStatData){
                goodsNo = dataGoodsNo
                break;
            }
        }

        if( 0 >= compareStatData.length ){
            return null;
        }

        for(var idx in compareStatData[goodsNo]['title']){
            returnData['title'].push(compareStatData[goodsNo]['title'][idx]);
        }
        for(var idx in compareStatData[goodsNo]['data']){
            returnData['data'].push(compareStatData[goodsNo]['data'][idx].replace(/[^0-9\-]/g, ""));
        }
        return returnData;
    }

    var initData = getCompareStatData();
    if(!$.isEmpty(initData)){
        var color = Chart.helpers.color;
        var horizontalBarChartData = {
            labels: initData['title'],
            datasets: [{
                label: '옵션별 출고 수량',
                backgroundColor: color('#35a2ea').alpha(0.9).rgbString(),
                /*borderColor: window.chartColors.red,*/
                borderWidth: 1,
                data: initData['data']
            }]
        };
    }

    $(function(){
        var ctx = document.getElementById('chart2').getContext('2d');
        window.compareChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: horizontalBarChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                legend: {
                    display: false,
                    position: 'right',
                },
                title: {
                    display: false,
                    /*text: 'Chart.js Horizontal Bar Chart'*/
                },
                scales: {
                    xAxes: [{
                        ticks : {
                            stepSize : 1
                        }
                    }]
                }
            }
        });

        //차트 정보 셋팅
        gChartData['compare'] = {
            chart : window.compareChart
            , dataFnc : getCompareStatData
        };


    });
</script>
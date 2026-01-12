<!--차트 -->
<script type="text/javascript">

    var currentStatChartData = $.parseJSON('<?=$currentStat['chartData']?>');

    var getCurrentStatData = function(goodsNo){
        var returnData = new Array();
        returnData['title'] = new Array();
        returnData['data1'] = new Array();
        returnData['data2'] = new Array();
        if( $.isEmpty(goodsNo)){
            for(var dataGoodsNo in  currentStatChartData){
                goodsNo = dataGoodsNo
                break;
            }
        }
        if( 0 >= compareStatData.length ){
            return null;
        }
        for(var idx in currentStatChartData[goodsNo]['title']){
            returnData['title'].push(currentStatChartData[goodsNo]['title'][idx]);
        }
        for(var idx in currentStatChartData[goodsNo]['data1']){
            returnData['data1'].push(Number(currentStatChartData[goodsNo]['data1'][idx].replace(/[^0-9\-]/g, "")));
        }
        for(var idx in currentStatChartData[goodsNo]['data2']){
            returnData['data2'].push(Number(currentStatChartData[goodsNo]['data2'][idx].replace(/[^0-9\-]/g, "")));
        }
        return returnData;
    }

    var initData = getCurrentStatData();
    if(!$.isEmpty(initData)){
        var color = Chart.helpers.color;
        var currentStatData = {
            labels: initData['title'],
            datasets: [{
                label: '재고 수량',
                backgroundColor: color('#75c9f8').alpha(0.9).rgbString(),
                borderWidth: 1,
                data: initData['data1']
            },{
                label: '안전재고 수량',
                backgroundColor: color('#aaaaaa').alpha(0.9).rgbString(),
                borderWidth: 1,
                data: initData['data2']
            }]
        };
    }

    $(function(){
        var ctx = document.getElementById('chart4').getContext('2d');
        window.currentStatChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: currentStatData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                legend: {
                    display: true,
                    position: 'right',
                },
                title: {
                    display: false,
                },
                scales: {
                    xAxes: [{
                        ticks : {
                            stepSize : 10
                        }
                    }]
                }
            }
        });

        //차트 정보 셋팅
        gChartData['current'] = {
            chart : window.currentStatChart
            , dataFnc : getCurrentStatData
        };

    });
</script>
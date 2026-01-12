<!--차트 -->
<script type="text/javascript">
    var ratioStatData = $.parseJSON('<?=$ratioStat['chartData']?>');
    var getRatioStatData = function(goodsNo){
        var returnData = new Array();
        returnData['title'] = new Array();
        returnData['data'] = new Array();
        if( $.isEmpty(goodsNo)){
            for(var dataGoodsNo in  ratioStatData){
                goodsNo = dataGoodsNo
                break;
            }
        }
        if( 0 >= compareStatData.length ){
            return null;
        }
        for(var idx in ratioStatData[goodsNo]['title']){
            returnData['title'].push(ratioStatData[goodsNo]['title'][idx]);
        }
        for(var idx in ratioStatData[goodsNo]['data']){
            returnData['data'].push( (ratioStatData[goodsNo]['data'][idx]+'').replace(/[^0-9\-]/g, ""));
        }
        return returnData;
    }

    var initData = getRatioStatData();
    if(!$.isEmpty(initData)){
        var color = Chart.helpers.color;
        var ratioStatChartData = {
            labels: initData['title'],
            datasets: [{
                label: '옵션별 출고 수량 비율',
                backgroundColor: color('#aaaaaa').alpha(0.9).rgbString(),
                borderWidth: 1,
                data: initData['data']
            }]
        };
    }

    $(function(){
        var ctx = document.getElementById('chart5').getContext('2d');
        window.ratioStatChart = new Chart(ctx, {
            type: 'bar',
            data: ratioStatChartData,
            options: {
                responsive: true,
                legend: {
                    display: false,
                    position: 'top',
                },
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [{
                        ticks : {
                            stepSize : 10
                        }
                    }]
                }
            }
        });

        //차트 정보 셋팅
        gChartData['ratio'] = {
            chart : window.ratioStatChart
            , dataFnc : getRatioStatData
        };

    });
</script>
<!--차트 -->
<script type="text/javascript">
    var periodStatData = $.parseJSON('<?=$periodStat['chartData']?>');
    var getPeriodStatData = function(goodsNo){
        var returnData = new Array();
        returnData['title'] = new Array();
        returnData['data'] = new Array();
        if( $.isEmpty(goodsNo)){
            for(var dataGoodsNo in  periodStatData){
                goodsNo = dataGoodsNo
                break;
            }
        }
        if( 0 >= compareStatData.length ){
            return null;
        }

        if( !$.isEmpty(goodsNo) ){
            if( typeof(periodStatData[goodsNo]) !== 'undefined' ){
                for(var idx in periodStatData[goodsNo]['title']){
                    returnData['title'].push( periodStatData[goodsNo]['title'][idx]);
                }
                for(var idx in periodStatData[goodsNo]['data']){
                    returnData['data'].push( (periodStatData[goodsNo]['data'][idx]+'').replace(/[^0-9\-]/g, ""));
                }
                return returnData;
            }
        }
        return [];
    }

    var initData = getPeriodStatData();
    if(!$.isEmpty(initData)){
        var color = Chart.helpers.color;
        var peroidStatData = {
            labels: initData['title'],
            datasets: [{
                label: '기간별 출고 수량',
                backgroundColor: color('#75c9f8').alpha(0.9).rgbString(),
                borderWidth: 1,
                data: initData['data']
            }]
        };
    }

    $(function(){
        var ctx = document.getElementById('chart3').getContext('2d');
        window.periodStatChart = new Chart(ctx, {
            type: 'bar',
            data: peroidStatData,
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
                            stepSize : 1
                        }
                    }]
                }
            }
        });

        //차트 정보 셋팅
        gChartData['period'] = {
            chart : window.periodStatChart
            , dataFnc : getPeriodStatData
        };

    });
</script>
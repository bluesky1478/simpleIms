<!--차트 -->
<script type="text/javascript">
    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    <?=$totalStat['chartData']['fineCnt']?>,
                    <?=$totalStat['chartData']['backCnt']?>,
                    <?=$totalStat['chartData']['exchangeCnt']?>,
                    <?=$totalStat['chartData']['refundCnt']?>,
                    <?=$totalStat['chartData']['asCnt']?>,
                ],
                backgroundColor: [
                    '#75c9f8',
                    '#36a3e8',
                    '#0075d4',
                    '#005099',
                    '#002947',
                ],
                label: 'Dataset 1'
            }],
            labels: [
                '문제없음',
                '반품',
                '교환',
                'AS'
            ]
        },
        options: {
            responsive: true
            ,legend: {
                position: 'left',
            },
        }
    };

    $(function(){
        var ctx1 = document.getElementById('chart1').getContext('2d');
        window.myPie = new Chart(ctx1, config);
    });

</script>


<div class="page-header js-affix">
    <h3><?=$chartGoodsData['goodsNm']?> 월별 출고 수량 (조회기간 : <?=$startDate?> ~ <?=$endDate?>) </h3>
</div>

<?php foreach($chartDataList as $chartKey => $chartValue) { ?>
<div class="chart-area" style="margin-bottom:40px">
    <div class="chart-title table-title" style="font-size:20px;margin-bottom:10px"> <?=$chartValue['optionName']?> 출고 현황</div>
    <div class="chart-canvas">
        <canvas id="chart<?=$chartKey+1?>"></canvas>
    </div>
</div>
<?php } ?>


<script type="text/javascript">
    $(function(){
        var color = Chart.helpers.color;

        var ctx = [];
        var chartTitles = [];
        var chartDataList = [];

        <?php foreach($chartDataList as $chartKey => $chartValue) { ?>
        ctx.push(document.getElementById('chart<?=$chartKey+1?>').getContext('2d'));

        chartTitles[<?=$chartKey?>] = [];
        <?php foreach($chartValue['data'] as $chartValueData ) { ?>chartTitles[<?=$chartKey?>].push('<?=$chartValueData['title']?>');<?php } ?>
        chartDataList[<?=$chartKey?>] = [];
        <?php foreach($chartValue['data'] as $chartValueData ) { ?>chartDataList[<?=$chartKey?>].push('<?=$chartValueData['count']?>');<?php } ?>

        window.currentStatChart = new Chart(ctx[<?=$chartKey?>], {
            type: 'bar',
            data: {
                labels: chartTitles[<?=$chartKey?>],
                datasets: [{
                    label: '<?=$chartValue['optionName']?>',
                    backgroundColor: color('#75c9f8').alpha(0.9).rgbString(),
                    borderWidth: 1,
                    data: chartDataList[<?=$chartKey?>]
                }]
            } ,
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
                    /*y : {
                        min:0
                    },*/
                    yAxes: [{
                        ticks : {
                            /*stepSize : 10*/
                            min : 0
                        }
                    }]
                }
            }
        });
        <?php } ?>

    });
</script>
<script type="text/javascript">
    var gChartData = [];
    var gChartDataList = [];
    var gChartLabelList = [];
</script>


<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
</div>

<div class="table-title">검색</div>
<form id="frmSearchStatistics" method="get">
    <input type="hidden" name="searchDevice">
    <input type="hidden" name="linkId" value="<?=$linkId?>" >
    <table class="table table-cols">
        <colgroup>
            <col class="width-md"/>
            <col/>
        </colgroup>
        <tbody>
        <?php if(empty($isProvider)) { ?>
            <tr>
                <th>공급사 구분</th>
                <td colspan="3">
                    <?=gd_select_box('scmNo', 'scmNo[]', $scmList, null, $scmNo, null); ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <th>기간검색</th>
            <td>
                <div class="form-inline">
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?= $searchDate[0]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?php echo $searchDate[1]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <div class="btn-group js-dateperiod-statistics" data-toggle="buttons" data-target-name="searchDate[]">
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['1']; ?>">
                                <input type="radio" name="searchPeriod" value="1" <?= $checked['searchPeriod']['1']; ?> >전일
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['7']; ?>">
                                <input type="radio" name="searchPeriod" value="7" <?= $checked['searchPeriod']['7']; ?> >7일
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['15']; ?>">
                                <input type="radio" name="searchPeriod" value="15" <?= $checked['searchPeriod']['15']; ?> >15일
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['30']; ?>">
                                <input type="radio" name="searchPeriod" value="30" <?= $checked['searchPeriod']['30']; ?> >1개월
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['90']; ?>">
                                <input type="radio" name="searchPeriod" value="90" <?= $checked['searchPeriod']['90']; ?> >3개월
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['364']; ?>">
                                <input type="radio" name="searchPeriod" value="364" <?= $checked['searchPeriod']['364']; ?> >1년
                            </label>
                        </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="table-btn">
        <button type="submit" class="btn btn-lg btn-black">검색</button>
    </div>
</form>


<!--<div style="width:100%;text-align: right ">
    <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀 다운로드</button>
</div>-->
<div class="table-dashboard">
    <table class="table table-cols">
        <?=$totalBody?>
    </table>
</div>

<div class="table-dashboard chart-data-area" >
    <table class="table table-cols">
        <tr>
            <th>
                <div class="top-chart-area">
                    <!-- 교환 (pie) -->
                    <div class="chart-area" >
                        <div class="chart-title table-title">
                            교환
                            <div class="pull-right btn-more go-data" data-datatype="exchangeStat" >+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info"></div>
                            <div><?= gd_select_box('select-chart-exchange-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="exchange"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chartExchange"></canvas>
                        </div>
                    </div>

                    <!--여백-->
                    <div class="chart-margin"></div>

                    <!-- 반품 (pie) -->
                    <div class="chart-area" >
                        <div class="chart-title table-title">
                            반품
                            <div class="pull-right btn-more go-data" data-datatype="backStat" >+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info"></div>
                            <div><?= gd_select_box('select-chart-back-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="back"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chartBack"></canvas>
                        </div>
                    </div>

                    <!--여백-->
                    <div class="chart-margin"></div>

                    <!-- AS (pie) -->
                    <div class="chart-area" style="margin-top:20px; width:100% !important;">
                        <div class="chart-title table-title">
                            AS
                            <div class="pull-right btn-more go-data" data-datatype="asStat" >+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info"></div>
                            <div><?= gd_select_box('select-chart-as-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="as"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chartAs"></canvas>
                        </div>
                    </div>

                </div>
            </th>
        </tr>
    </table>
</div>

<?php foreach( \SlComponent\Util\SlCodeMap::CLAIM_TYPE as $key => $value ) { ?>
    <script type="text/javascript">
        <?php if( !empty($claimInfo[$key]['chartData']) ) { ?>
            gChartDataList['<?=$key?>'] = $.parseJSON('<?=$claimInfo[$key]['chartData']?>');
            gChartLabelList['<?=$key?>'] = $.parseJSON('<?=$claimInfo[$key]['chartLabel']?>');
        <?php } else { ?>
            gChartDataList['<?=$key?>'] = [];
            gChartLabelList['<?=$key?>'] = [];
        <?php } ?>
    </script>
    <div class="table-dashboard raw-data-area <?=$key?>Stat"  style="display:none" >
        <table class="table table-cols" style="margin-bottom:0px">
            <tr>
                <th colspan="99">
                    <div class="top-data-area">
                        <div class="data-area" >
                            <div class="data-title table-title" >
                                <div class="pull-left"><span class="go-chart" >< 차트화면으로</span></div>
                                <div class="center-title"><?=$value?> </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-white btn-icon-excel simple-download" data-downtype="<?=$key?>">엑셀 다운로드</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </th>
            </tr>
        </table>
        <table class="table table-cols">
            <?=$bodyList[$key]?>
        </table>
    </div>
<?php } ?>

<!--교환/환불/반품/AS-->
<script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/statistics.js"></script>

<script type="text/javascript">
    $(function(){
        //simple excel download
        $('.simple-download').click(function(){
            var downType = $(this).data('downtype');
            location.href = "<?=$requestUrl?>" + "&downType=" + downType;
        });

        $('.go-chart').click(function(){
            $('.chart-data-area').show();
            $('.raw-data-area').hide();
        });

        $('.go-data').click(function(){
            $('.raw-data-area').hide();
            var dataType = $(this).data('datatype');
            $('.chart-data-area').hide();
            $('.'+dataType).show();
        });

        $('.raw-data-area').hide();

    });
</script>

<script type="text/javascript">

    var compareStatData = [];

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
            returnData['data'].push(compareStatData[goodsNo]['data'][idx]);
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
</script>

<script type="text/javascript">
    var configCommon = JSON.stringify({
        type: 'pie',
        data: {
            datasets: [{
                backgroundColor: [
                    <?=$chartColor?>
                ],
                label: 'Dataset 1'
            }],
            labels: [
                <?=$chartLabel?>
            ]
        },
        options: {
            responsive: true
            ,legend: {
                position: 'left',
            },
        }
    });

    var configExchange = JSON.parse(configCommon);
    configExchange.data.datasets[0].data = [
        0,0,0,0,0,0,0,0,0,0
    ];
    var configBack = JSON.parse(configCommon);
    configBack.data.datasets[0].data = [
        0,0,0,0,0,0,0,0,0,0
    ];
    /*var configRefund = JSON.parse(configCommon);
    configRefund.data.datasets[0].data = [
        0,0,0,0,0,0,0,0,0,0
    ];*/
    var configAs = JSON.parse(configCommon);
    configAs.data.datasets[0].data = [
        0,0,0,0,0,0,0,0,0,0
    ];

    $(function(){
        <?php foreach($claimType as $key => $value){ ?>
        var ctx<?=ucfirst($key)?> = document.getElementById('chart<?=ucfirst($key)?>').getContext('2d');
        window['<?=$key?>Chart'] = new Chart(ctx<?=ucfirst($key)?>, config<?=ucfirst($key)?>);

        //차트 정보 셋팅
        gChartData['<?=$key?>'] = {
            chart : window['<?=$key?>Chart']
        };
        <?php } ?>

        var getChartData = function(chartDiv,goodsNo){
            var returnData = new Array();
            if( $.isEmpty(goodsNo)){
                for(var dataGoodsNo in  gChartDataList[chartDiv]){
                    goodsNo = dataGoodsNo;
                    break;
                }
            }

            if( typeof(gChartDataList[chartDiv]) == 'undefined'  || null == gChartDataList[chartDiv] ||  0 >= gChartDataList[chartDiv].length ){
                return false;
            }

            for(var idx in gChartDataList[chartDiv][goodsNo]){
                returnData.push(gChartDataList[chartDiv][goodsNo][idx]);
            }

            return returnData;
        }

        var getChartLabel = function(chartDiv,goodsNo){
            var returnData = new Array();
            if( $.isEmpty(goodsNo)){
                for(var dataGoodsNo in  gChartLabelList[chartDiv]){
                    goodsNo = dataGoodsNo;
                    break;
                }
            }

            if( typeof(gChartLabelList[chartDiv]) == 'undefined'  || null == gChartLabelList[chartDiv] ||  0 >= gChartLabelList[chartDiv].length ){
                return false;
            }

            for(var idx in gChartLabelList[chartDiv][goodsNo]){
                returnData.push(gChartLabelList[chartDiv][goodsNo][idx]);
            }

            return returnData;
        }

        
        //차트 데이터 업데이트
        var chartUpdate = function(chartDiv, goodsNo){
            var chart = gChartData[chartDiv].chart;
            var chartData = getChartData(chartDiv,goodsNo);
            var chartLabel = getChartLabel(chartDiv,goodsNo);

            chart.data.datasets.forEach(function(dataset) {
                dataset.data = chartData;
            });

            chart.data.labels = chartLabel;

            chart.update();
        }

        //상품선택에따른 차트 업데이트
        $('.select-chart-goods').change(function(){
            var chartDiv = $(this).data('chart');
            var goodsNo = $(this).find('option:selected').val();
            chartUpdate(chartDiv,goodsNo);
            //현재 선택된 상품 쿠키 저장
            var optionValue = $(this).find('option:selected').val();
            $.cookie(chartDiv+"<?=$scmNo?>",optionValue);
        });
        //초기 데이터 셋팅
        $('.select-chart-goods').each(function(){
            var chartDiv =  $(this).data('chart');
            var $chartEl = $('#'+$(this).attr('id'));
            var goodsNo = $.cookie(chartDiv+"<?=$scmNo?>");
            if( !$.isEmpty(goodsNo) ){
                $chartEl.val( goodsNo );
                var chartDiv = $(this).data('chart');
                chartUpdate(chartDiv,goodsNo);
                //console.log('실행1 : ' + chartDiv );
            }else{
                chartUpdate(chartDiv);
                //console.log('실행2 : ' + chartDiv );
            }
        });

    });

</script>
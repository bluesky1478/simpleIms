<script type="text/javascript">
    var gChartData = [];
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
                <th>고객사 구분</th>
                <td colspan="3">
                    <?=gd_select_box('scmNo', 'scmNo[]', $scmList, null, $scmNo, null); ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <th>기간검색</th>
            <td>
                <div class="form-inline">
                    <?php if(  'stockToday'  === $linkId ) { ?>
                    <div class="input-group">
                        <input type="text" class="form-control width-xs" name="searchDate[]" value="<?= $searchDate[0]; ?>"  readonly="readonly"  />
                    </div>
                    ~
                    <div class="input-group">
                        <input type="text" class="form-control width-xs" name="searchDate[]" value="<?php echo $searchDate[1]; ?>"  readonly="readonly" />
                    </div>
                    <?php } ?>

                    <?php if(  'stockDay' === $linkId || 'stockWeek' === $linkId  ) { ?>
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
                        </div>
                    <?php } ?>

                    <?php if(  'stockMonth' === $linkId ) { ?>
                        <div class="input-group js-datepicker-months">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?= $searchDate[0]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker-months">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?php echo $searchDate[1]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <div class="btn-group js-dateperiod-statistics-months" data-toggle="buttons" data-target-name="searchDate[]">
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['0']; ?>">
                                <input type="radio" name="searchPeriod" value="0" <?= $checked['searchPeriod']['0']; ?> >1개월
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['2']; ?>">
                                <input type="radio" name="searchPeriod" value="2" <?= $checked['searchPeriod']['2']; ?> >3개월
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['5']; ?>">
                                <input type="radio" name="searchPeriod" value="5" <?= $checked['searchPeriod']['5']; ?> >6개월
                            </label>
                            <label class="btn btn-white btn-sm hand <?= $active['searchPeriod']['11']; ?>">
                                <input type="radio" name="searchPeriod" value="11" <?= $checked['searchPeriod']['11']; ?> >12개월
                            </label>
                        </div>
                    <?php } ?>

                    <?php if(  'stockYear' === $linkId ) { ?>
                        <style>
                            .js-datepicker-year .input-group-addon{
                                padding: 3px 5px;
                                background-color: #FFFFFF;
                                border: none;
                            }
                        </style>
                        <script type="text/javascript">
                            $(function(){
                                var defaultOptions = {
                                    locale: 'ko',
                                    format: 'YYYY',
                                    dayViewHeaderFormat: 'YYYY년',
                                    viewMode: 'months',
                                    ignoreReadonly: true
                                };
                                var options = $('.js-datepicker-year').data('options');
                                options = $.extend(true, {}, defaultOptions, options);
                                $('.js-datepicker-year').datetimepicker(options);
                                /*


                                */
                            }) ;
                        </script>

                        <div class="input-group js-datepicker-year">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?= $searchDate[0]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker-year">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?php echo $searchDate[1]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                    <?php } ?>

                </div>
            </td>
        </tr>
        <tr>
            <th>검색어</th>
            <td>
                <?= gd_select_box('key', 'key', $combineSearch, null, gd_isset($search['key']), null, null, 'form-control'); ?>
                <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control " style="width:200px"/>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="table-btn">
        <button type="submit" class="btn btn-lg btn-black">검색</button>
    </div>
</form>

<ul class="nav nav-tabs mgb20">
    <li class="stockToday"><a id="stockToday" class="hand link-report">당일</a></li>
    <li class="stockDay"><a id="stockDay" class="hand link-report">일별</a></li>
    <li class="stockWeek"><a id="stockWeek" class="hand link-report">요일별</a></li>
    <li class="stockMonth"><a id="stockMonth" class="hand link-report">월별</a></li>
    <li class="stockYear"><a id="stockYear" class="hand link-report">연도별</a></li>
</ul>

<div class="table-dashboard">
    <table class="table table-cols">
        <colgroup>
            <col style="width:33%"/>
            <col style="width:33%"/>
            <col style="width:33%"/>
        </colgroup>
        <tbody>
        <tr>
            <th>전체출고건<br><small class="font-kor">(전체주문건수)</small></th>
            <th>전체출고 수량<br><small class="font-kor">(전체 상품 사이즈별 수량 합계)</small></th>
            <th>주문금액<br><small class="font-kor">(전체 제품 금액 합계)</small></th>
        </tr>
        <tr>
            <td>
                <strong><?= number_format($orderTotal['orderCnt']); ?>건</strong>
            </td>
            <td>
                <strong><?= number_format($orderTotal['goodsCnt']); ?>장</strong>
            </td>
            <td>
                <strong><?= number_format($orderTotal['settlePrice']); ?>원</strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-dashboard chart-data-area" >
    <table class="table table-cols">
        <tr>
            <th>
                <div class="top-chart-area">
                    <!-- 전체출고 현황 (pie) -->
                    <div class="chart-area" >
                        <div class="chart-title table-title" >
                            전체 출고 현황
                            <div class="pull-right btn-more go-data" data-datatype="totalStat" >+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info">출고된 전체수량 대비 반품, 교환, 환불, AS 비율</div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chart1"></canvas>
                        </div>
                    </div>

                    <!--여백-->
                    <div class="chart-margin"></div>

                    <!-- 출고량 비교(stick) -->
                    <div class="chart-area">
                        <div class="chart-title table-title" >
                            출고량 비교
                            <div class="pull-right btn-more go-data" data-datatype="compareStat">+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info">설정한 기간에 따른 출고 수량</div>
                            <div><?= gd_select_box('select-chart-compare-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="compare"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chart2"></canvas>
                        </div>
                    </div>

                    <!--여백-->
                    <div class="chart-margin"></div>

                    <!-- 기간별 출고현황(v-stick) -->
                    <div class="chart-area" style="width:99.7%">
                        <div class="chart-title table-title" >
                            기간별 출고 현황
                            <div class="pull-right btn-more go-data" data-datatype="periodStat">+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info">설정 기간별 전체 출고 수량 합계추이</div>
                            <div><?= gd_select_box('select-chart-period-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="period"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chart3"></canvas>
                        </div>
                    </div>

                    <!-- 재고현황(stick) -->
                    <div class="chart-area" style="margin-top:20px">
                        <div class="chart-title table-title" >
                            재고 현황
                            <div class="pull-right btn-more go-data" data-datatype="currentStat">+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info">사이즈별 재고와 안전재고에 임박한 수량 확인 </div>
                            <div><?= gd_select_box('select-chart-current-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="current"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chart4"></canvas>
                        </div>
                    </div>

                    <!--여백-->
                    <div class="chart-margin"></div>

                    <div class="chart-area" style="margin-top:20px">
                        <div class="chart-title table-title" >
                            출고 사이즈 비율
                            <div class="pull-right btn-more go-data" data-datatype="ratioStat">+</div>
                        </div>
                        <div class="chart-function-or-comment">
                            <div class="notice-info">사이즈별 출고 수량과 비율 확인 </div>
                            <div><?= gd_select_box('select-chart-ratio-stat', null, $selectedGoodsInfo, null,  null, null, 'data-chart="ratio"', 'form-control select-chart-goods' ); ?></div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="chart5"></canvas>
                        </div>
                    </div>

                </div>

            </th>
        </tr>
    </table>
</div>

<!--전체출고현황-->
<div class="table-dashboard raw-data-area totalStat"  style="display: none">
    <table class="table table-cols">
        <tr>
            <th colspan="<?=count($totalStat['title'])?>">
                <div class="top-data-area">
                    <div class="data-area" >
                        <div class="data-title table-title" >
                            <div class="pull-left"><span class="go-chart" >< 차트화면으로</span></div>
                            <div class="center-title">전체 출고 현황 </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-white btn-icon-excel simple-download" data-downtype="totalStat">엑셀 다운로드</button>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
        <tr>
        </tr>
        <?=$totalStat['htmlBody']?>
    </table>
</div>

<!--출고량비교-->
<div class="table-dashboard raw-data-area compareStat"  style="display: none" >
    <table class="table table-cols" style="margin-bottom:0px">
        <tr>
            <th colspan="99">
                <div class="top-data-area">
                    <div class="data-area" >
                        <div class="data-title table-title" >
                            <div class="pull-left"><span class="go-chart" >< 차트화면으로</span></div>
                            <div class="center-title">출고량 비교 </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-white btn-icon-excel simple-download" data-downtype="compareStat">엑셀 다운로드</button>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </table>
        <table class="table table-cols">
            <?=$compareStat['htmlBody']?>
        </table>
</div>

<!--기간별 출고 현황-->
<div class="table-dashboard raw-data-area periodStat"  style="display: none" >
    <table class="table table-cols" style="margin-bottom:0px">
        <tr>
            <th colspan="99">
                <div class="top-data-area">
                    <div class="data-area" >
                        <div class="data-title table-title" >
                            <div class="pull-left"><span class="go-chart" >< 차트화면으로</span></div>
                            <div class="center-title">기간별 출고 현황</div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-white btn-icon-excel simple-download" data-downtype="periodStat">엑셀 다운로드</button>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </table>
        <table class="table table-cols">
            <?=$periodStat['htmlBody']?>
        </table>
</div>

<!--재고 현황-->
<div class="table-dashboard raw-data-area currentStat"  style="display: none" >
    <table class="table table-cols" style="margin-bottom:0px">
        <tr>
            <th colspan="99">
                <div class="top-data-area">
                    <div class="data-area" >
                        <div class="data-title table-title">
                            <div class="pull-left"><span class="go-chart" >< 차트화면으로</span></div>
                            <div class="center-title">재고 현황</div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-white btn-icon-excel simple-download" data-downtype="currentStat">엑셀 다운로드</button>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </table>
    <table class="table table-cols">
        <?=$currentStat['htmlBody']?>
    </table>
</div>

<!--재고 현황-->
<div class="table-dashboard raw-data-area ratioStat"  style="display: none" >
    <table class="table table-cols" style="margin-bottom:0px">
        <tr>
            <th colspan="99">
                <div class="top-data-area">
                    <div class="data-area" >
                        <div class="data-title table-title" >
                            <div class="pull-left"><span  class="go-chart" >< 차트화면으로</span></div>
                            <div class="center-title">출고 사이즈 비율</div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-white btn-icon-excel simple-download" data-downtype="ratioStat">엑셀 다운로드</button>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </table>
    <table class="table table-cols">
        <?=$ratioStat['htmlBody']?>
    </table>
</div>


<script type="text/javascript">
    <!--
    var widthSize = 930;
    //-->
</script>
<script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/statistics.js"></script>

<?php include "chart/_total_stat.php"; ?>
<?php include "chart/_compare_stat.php"; ?>
<?php include "chart/_period_stat.php"; ?>
<?php include "chart/_current_stat.php"; ?>
<?php include "chart/_ratio_stat.php"; ?>

<script type="text/javascript">
    $(function(){

        <?php if(  empty($linkId) ){ ?>
            $('.stockToday').addClass('active');
        <?php }else{ ?>
            $('.<?=$linkId?>').addClass('active');
        <?php } ?>

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

        $('.link-report').click(function(){
            var linkId = $(this).attr('id');
            location.href = '<?=$selfUrl?>?linkId=' + linkId + '&scmNo=' + $('#scmNo option:selected').val();
        })

        var chartUpdate = function(chartDiv, goodsNo){
            var chart = gChartData[chartDiv].chart;
            var chartData = gChartData[chartDiv].dataFnc(goodsNo);
            chart.data.labels = chartData['title'];

            var idx = 1;
            chart.data.datasets.forEach(function(dataset) {
                if( 'current' === chartDiv  ){
                    dataset.data = chartData['data'+idx];
                    idx++;
                }else{
                    dataset.data = chartData['data'];
                }
            });
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

        //쿠키값 있으면 셋팅
        $('.select-chart-goods').each(function(){
            var chartDiv =  $(this).data('chart');
            var $chartEl = $('#'+$(this).attr('id'));
            var goodsNo = $.cookie(chartDiv+"<?=$scmNo?>");
            if( !$.isEmpty(goodsNo) ){
                $chartEl.val( goodsNo );
                var chartDiv = $(this).data('chart');
                chartUpdate(chartDiv,goodsNo);
            }
        });

    });
</script>

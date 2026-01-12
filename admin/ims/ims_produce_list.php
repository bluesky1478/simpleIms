<?php include 'library_all.php'?>

<style>
    /*주문리스트 관리자 추가 상품 정보 */
    .layer-order-add-info {padding:15px}
    .layer-order-add-info .order-add-info-title {font-size:14px;text-align: left;font-weight: bold;}
    .layer-order-add-info th{ background-color:#F6F6F6!important; text-align: center; color:#5c5c5c }
    .layer-order-add-info td{ text-align: left }

    .sales-table td,th{
        font-size:11px;
    }

</style>

<?php
$openType = 'newTab';
?>

<style>
    .modal-dialog{
        width:1500px !important;
    }
</style>

<div class="page-header js-affix produce-header ims-page-header" style="padding-bottom:0!important;">
    <div id="affix-show-type1" style="padding-bottom:5px !important;">
        <h3><?= end($naviMenu->location); ?></h3>

        <?php if(!empty($stepManagerInfo) && !$imsProduceCompany ) { ?>
            <div class="relative">
                <div class="list-photo" style="background-image:url('../..<?=$stepManagerInfo['dispImage']?>');"></div>
                <div class="list-photo-title">담당 : <?=$stepManagerInfo['managerNm']?> <?=$stepManagerInfo['positionName']?>
                    <div class="font-14">(
                        <?=$stepManagerInfo['cellPhone'] ?>
                        <?=empty($stepManagerInfo['email'])?'':", <a href='mailto:{$stepManagerInfo['email']}' class='sl-blue'>{$stepManagerInfo['email']}</a>"?>
                        )</div>
                </div>
            </div>
        <?php } ?>

        <div class="btn-group">
            <?php if(!empty($isDev)) { ?>
                <input type="button" value="생산 일괄 등록" class="btn btn-red-line"  onclick="$('.excel-upload-goods-info').show('fade')"  />
            <?php } ?>
        </div>
    </div>
    <table id="affix-show-type2" class="table table-rows " style="margin:0 !important; display: none">
        <colgroup>
            <col style="width:2%" />
            <col style="width:3%" />
            <col style="width:8%" />
            <col style="width:18%" />
            <col style="width:4%" />
            <col style="width:10%" />
            <col style="width:5%" />
            <col style="width:7%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
            <col style="width:5%" />
        </colgroup>
        <thead>
        <tr style="position: sticky;top:0; left:0">
            <th >

            </th>
            <th>번호</th>
            <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                <th><?=$titleValue?></th>
            <?php } ?>
        </tr>
        </thead>
    </table>
</div>

<section class="excel-upload-section excel-upload-goods-info display-none">
    <div class="table-title">
        생산 일괄 등록
    </div>
    <div class="search-detail-box form-inline">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tr>
                <th>생산 등록</th>
                <td colspan="3">
                    <form id="frmExcel" name="frmExcel" action="./ims_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="saveBatchProduce"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                        </div>
                    </form>
                    <input type="button" value="생산등록양식" class="btn btn-white btn-icon-excel mgt10" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/ims_customer.xlsx')?>&fileName=<?=urlencode('IMS고객등록_템플릿.xls')?>'">
                </td>
            </tr>
        </table>
    </div>
</section>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">

    <input type="hidden" name="sort" id="sort-hidden" value="<?= gd_isset($requestParam['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="status" value="<?=$requestParam['status']?>"/>
    <input type="hidden" name="view" value="<?=$requestParam['view']?>"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 100); ?>"/>

    <!--검색 시작-->
    <div class="ims-search-div search-detail-box form-inline">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tbody>
            <tr>
                <th>검색어</th>
                <td >
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control"/>

                    <span class="mgl10">
                        <label class="radio-inline ">
                            <input type="radio" name="showMemo" value="y" <?=gd_isset($checked['showMemo']['y']); ?> />비고보기
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="showMemo" value="n" <?=gd_isset($checked['showMemo']['n']); ?> />비고제외
                        </label>
                    </span>

                    <span>
                        <input type="submit" value="검색" class="btn btn-lg btn-black">
                    </span>

                </td>
                <?php if($imsProduceCompany) { ?>
                    <th></th>
                    <td></td>
                <?php }else{ ?>
                    <th>생산처</th>
                    <td>
                        <select class="js-example-basic-single" class="form-control" style="border:solid 1px #d1d1d1" name="produceCompanySno">
                            <option value="all">전체</option>
                            <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                <option value="<?=$key?>"  <?=$key==$search['produceCompanySno']?'selected':''?> ><?=$value?></option>
                            <?php } ?>
                        </select>

                        <span class="mgl10">
                            <label class="radio-inline ">
                                <input type="radio" name="showReqAccept" value="all" <?=gd_isset($checked['showReqAccept']['all']); ?> />전체
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="showReqAccept" value="y" <?=gd_isset($checked['showReqAccept']['y']); ?> />승인요청건만 보기
                            </label>
                        </span>

                        <span>
                            <input type="submit" value="검색" class="btn btn-lg btn-black">
                        </span>

                    </td>
                <?php } ?>
            </tr>
            <tr>
                <th>분류패킹여부</th>
                <td >

                    <span class="mgl10">
                        <label class="radio-inline ">
                            <input type="radio" name="packingYn" value="all" <?=gd_isset($checked['packingYn']['all']); ?> />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="packingYn" value="y" <?=gd_isset($checked['packingYn']['y']); ?> />진행
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="packingYn" value="n" <?=gd_isset($checked['packingYn']['n']); ?> />미진행
                        </label>
                    </span>

                </td>
                <th>3PL여부</th>
                <td>
                    <span class="mgl10">
                        <label class="radio-inline ">
                            <input type="radio" name="use3pl" value="all" <?=gd_isset($checked['use3pl']['all']); ?> />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="use3pl" value="y" <?=gd_isset($checked['use3pl']['y']); ?> />진행
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="use3pl" value="n" <?=gd_isset($checked['use3pl']['n']); ?> />미진행
                        </label>
                    </span>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td >

                    <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'form-control '); ?>

                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                    ~
                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>" />
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>

                    <?= gd_search_date(gd_isset($search['searchPeriod'], 364), 'treatDate[]', false) ?>

                </td>
                <th>폐쇄몰 사용 여부</th>
                <td>
                    <span class="mgl10">
                        <label class="radio-inline ">
                            <input type="radio" name="useMall" value="all" <?=gd_isset($checked['useMall']['all']); ?> />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="useMall" value="y" <?=gd_isset($checked['useMall']['y']); ?> />진행
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="useMall" value="n" <?=gd_isset($checked['useMall']['n']); ?> />미진행
                        </label>
                    </span>
                </td>
            </tr>
            <tr>
                <th>연도/시즌</th>
                <td >
                    연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control" placeholder="연도"/>
                    시즌 :
                    <select class="form-control" name="projectSeason">
                        <option value="">선택</option>
                        <option <?= 'FW' == $search['projectSeason'] ? 'selected':'' ; ?>>FW</option>
                        <option <?= 'SF' == $search['projectSeason'] ? 'selected':'' ; ?>>SF</option>
                        <option <?= 'SP' == $search['projectSeason'] ? 'selected':'' ; ?>>SP</option>
                        <option <?= 'SU' == $search['projectSeason'] ? 'selected':'' ; ?>>SU</option>
                        <option <?= 'FA' == $search['projectSeason'] ? 'selected':'' ; ?>>FA</option>
                        <option <?= 'WI' == $search['projectSeason'] ? 'selected':'' ; ?>>WI</option>
                        <option <?= 'SS' == $search['projectSeason'] ? 'selected':'' ; ?>>SS</option>
                        <option <?= 'ALL' == $search['projectSeason'] ? 'selected':'' ; ?>>ALL</option>
                    </select>

                    <span>
                        <input type="submit" value="검색" class="btn btn-lg btn-black">
                    </span>

                </td>
                <th></th>
                <td>

                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!--
    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>
    -->

    <!--검색 끝-->
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">

    <div class="table-header form-inline" style="vertical-align: bottom">
        <div class="pull-left pd0" style="padding-top:10px !important;">
            <div class="" style="width:220px;">
                <ul class="nav nav-tabs mgb0" role="tablist" style="border-bottom:none!important;">
                    <li role="presentation" <?=$requestParam['view'] !== 'all' ? 'class="active"' : ''?>>
                        <a href="../ims/ims_produce_list.php?status=<?=$requestParam['status']?>&view=reserved&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">
                            예정일만보기
                        </a>
                    </li>
                    <li role="presentation" <?=$requestParam['view'] == 'all' ? 'class="active"' : ''?>>
                        <a href="../ims/ims_produce_list.php?status=<?=$requestParam['status']?>&view=all&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">
                            전체항목보기
                        </a>
                    </li>
                    <!--
                    <li role="presentation" <?=$requestParam['view'] !== 'style' ? 'class="active"' : ''?>>
                        <a href="../ims/ims_project_list.php?view=project&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">프로젝트별</a>
                    </li>
                    <li role="presentation" <?=$requestParam['view'] == 'style' ? 'class="active"' : ''?>>
                        <a href="../ims/ims_project_list.php?view=style&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">스타일별</a>
                    </li>
                    -->
                </ul>
            </div>
        </div>
        <div class="pull-right">

            <input type="button" value="생산스케쥴가이드 다운로드" class="btn btn-white" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/prd_schedule_guide.pdf')?>&fileName=<?=urlencode('(IMS) 생산스케쥴_가이드라인V1_231205.pdf')?>'">

            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
            <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 100)); ?>
        </div>
    </div>

    <div class="table-action clearfix" style="margin:0">

        <div class="pull-left">
            검색
            <strong class="text-danger"><?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong> 건
            / 선택한 프로젝트 :
            <?php if( 'step10' === $requestParam['status'] ){ ?>
                <div class="btn btn-white btn-batch-next-step">스케쥴 확정요청</div>
            <?php } ?>
            <?php if( 'step20' === $requestParam['status'] && !$imsProduceCompany ){ ?>
                <div class="btn btn-white btn-batch-step-confirm">스케쥴 확정</div>
                <div class="btn btn-white btn-batch-step-back">스케쥴 반려</div>
            <?php } ?>
        </div>
        <div class="pull-right">
            <?php if( 'step30' === $requestParam['status'] ){ ?>
                <span class="notice-info" style="color:#000;font-weight: bold">반려된 항목은 반드시 재검토 후 파일을 다시 올려주시기 바랍니다.</span>
            <?php } ?>
            <!--
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            -->
        </div>
    </div>

    <table class="table table-rows ">
                <colgroup>
                    <col style="width:2%" />
                    <col style="width:3%" />
                    <col style="width:8%" />
                    <col style="width:2%" />
                    <col style="width:18%" />
                    <col style="width:4%" />
                    <col style="width:10%" />
                    <col style="width:5%" />
                    <col style="width:7%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                </colgroup>
                <thead>
                <tr style="position: sticky;top:0; left:0">
                    <th >
                        <input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/>
                    </th>
                    <th>번호</th>
                    <?php foreach ($listTitles as $titleKey => $titleValue) { ?>
                        <th><?=$titleValue?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody class="order-list">
                <?php
                if (gd_isset($data)) {
                    foreach ($data as $valIdx =>  $val) {
                        $bgColor = '';
                        if( 0 === $valIdx % 2 ){
                            $bgColor = 'table-bg-light-gray';
                        }
                        ?>
                        <tr class="center field-parent <?=$bgColor?>" data-sno="<?=$val['produceSno']?>" data-project-sno="<?=$val['sno']?>" >
                            <td rowspan="<?=$rowspan?>">
                                <input type="checkbox" name="sno[<?=$val['produceSno']?>]" value="<?=$val['produceSno']?>" />
                            </td>
                            <td class="font-num" rowspan="<?=$rowspan?>"><!--번호-->
                                <span class="number"><?= $page->idx--; ?></span>
                                <?php if(!empty($isDev)) { ?>
                                    <br><small class="text-muted"><?=$val['produceSno']?></small>
                                <?php } ?>
                            </td>
                            <td class="font-num font-13 text-danger" rowspan="<?=$rowspan?>">
                                <span style="color:#000"><?= $val['produceCompanyName']; ?></span>
                                <?php if( 99 != $val['produceStatus'] ) { ?>
                                    <br><?= $val['msDeliveryRemainDt']; ?>
                                <?php } ?>
                                <br><br><small class="text-muted">last update : <?=$val['modDt']?></small>
                            </td><!--납기까지-->
                            <td class="font-14" rowspan="<?=$rowspan?>">
                                <b>
                                    <?= $val['projectYear']; ?>
                                    <?= $val['projectSeason']; ?>
                                </b>
                                <div class="mgt10">
                                    <?=$val['seasonIcon']?>
                                </div>
                            </td>
                            <td class="font-num field-customer" rowspan="<?=$rowspan?>"><!--프로젝트번호-->

                                <div class="ims-project-title"><?= $val['projectName']; ?></div>

                                <span class="label-icon label-icon<?=$val['projectType']?>"><?=$val['projectTypeEn']?></span>

                                <?php if( $imsProduceCompany ){ ?>
                                    <span class="font-14">
                                        <?=$val['customerName']; ?>
                                        <?= $val['projectYear']; ?>
                                        <?= $val['projectSeason']; ?>
                                    </span>
                                <?php }else{ ?>
                                    <span class="font-14">
                                        <?=$val['customerName']; ?>
                                        <?= $val['projectYear']; ?>
                                        <?= $val['projectSeason']; ?>
                                    </span>
                                    <!-- TODO : 나중에 고객 집계 연결
                                    <a href="ims_project_view.php?sno=<?=$val['sno']?>" class="text-blue font-16">
                                        <?=$val['customerName']; ?>
                                    </a>
                                    -->
                                <?php } ?>
                                <div class="number text-danger project-no">
                                    <a href="ims_produce_view.php?sno=<?=$val['sno']?>&status=<?=$requestParam['status']?>" class="text-danger"><?= $val['projectNo']; ?></a>
                                    <br><span class="font-11 text-muted" >(<?=$val['produceStatusKr']; ?>상태)</span>
                                </div>

                                <div class="mgt10">
                                    <span class="font-12" style="color:#0c4da2"><?=$val['useInfo']?></span>
                                </div>

                                <?php if(!empty($isDev)) { ?>
                                    <div class="btn btn-sm btn-white btn-delete dn display-none" data-sno="<?=$val['sno']?>">삭제</div>
                                <?php } ?>

                                <?php if( 'y' === $val['useMall'] && !empty($val['privateMallDeliveryDt'])) { ?>
                                <div class="mgt5">폐쇄몰 출고가능 : <?=$val['privateMallDeliveryDt']?></div>
                                <?php } ?>

                                <?php if(!empty($val['planPayDiv'])) { ?>
                                <div class="mgt5">항공비용지불 : <?=$val['planPayDiv']?></div>
                                <?php } ?>

                            </td>
                            <!--
                    <td class="center " rowspan="<?=$rowspan?>">
                        <span class="text-muted"><?=$val['customerOrderDt']?></span>
                        <br>
                        <span class="font-16"><?=$val['customerDeliveryDt']?></span>
                    </td>
                    -->
                            <!--고객납기일-->
                            <td class="center " rowspan="<?=$rowspan?>">
                                <div class="text-muted"><?=$val['msOrderDt']?></div>
                                <div class="font-14">
                                    <?php if( !empty($val['isWarn']) ) { ?>
                                        <i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>
                                    <?php } ?>
                                    <?=$val['msDeliveryDt']?>
                                </div>
                                <?php if(!$imsProduceCompany) { ?>
                                    <div class="font-14 text-blue"><b><?=$val['customerDeliveryDt']?></b></div>
                                <?php } ?>
                            </td><!--이노버발주일-->

                            <td class="center " rowspan="<?=$rowspan?>"><?=$val['style']?> <?=$val['styleCountNm']?> </td><!--스타일-->

                            <td class="center " rowspan="<?=$rowspan?>"><?=number_format($val['prdExQty'])?></td><!--스타일 수량-->

                            <td class="center ">
                                <span class="schedule-update hover-btn cursor-pointer text-blue" data-sno="<?=$val['sno']?>">예정일 <i class="fa fa-caret-right text-muted fa-lg text-blue" aria-hidden="true"></i></span>
                            </td>
                            <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                                <?php if( !empty($val['prdStep'.$stepKey]['memo']) ) { ?>
                                    <td class="center ">
                                        <?=$val['prdStep'.$stepKey]['memo']?>
                                    </td><!--스텝별 예정일-->
                                <?php }else{ ?>
                                    <td class="center ">
                                        <?php if( !empty($val['isWarn']) && 90 == $stepKey  ) { ?>
                                            <i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>
                                        <?php } ?>
                                        <?=$val['prdStep'.$stepKey]['expectedDt']?>
                                    </td><!--스텝별 예정일-->
                                <?php } ?>
                            <?php }?>
                        </tr>
                        <?php if( $rowspan > 2 ){ ?>
                            <tr class="<?=$bgColor?>">
                                <td class="center ">완료일 <i class="fa fa-caret-right text-muted fa-lg " aria-hidden="true"></i></td>
                                <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                                    <td class="center "><?=$val['prdStep'.$stepKey]['completeDt']?></td><!--스텝별 완료일-->
                                <?php }?>
                            </tr>
                            <tr class="<?=$bgColor?>">
                                <td class="center ">승인여부 <i class="fa fa-caret-right text-muted fa-lg " aria-hidden="true"></i></td>
                                <?php foreach($PRODUCE_STEP_MAP as $stepKey => $stepData){?>
                                    <td class="center "><?=$val['prdStep'.$stepKey]['confirmYnKr']?></td><!--스텝별 승인-->
                                <?php }?>
                            </tr>
                        <?php } ?>

                        <?php if( gd_isset($checked['showMemo']['y']) ) { ?>
                            <tr class="<?=$bgColor?>">
                                <td class="center " >비&nbsp;&nbsp;고 <i class="fa fa-caret-right text-muted fa-lg " aria-hidden="true"></i></td>
                                <td class="left " colspan="99">

                                    <?php if( empty($val['latestComment']) ) { ?>
                                        -
                                    <?php }else{ ?>
                                        <i class="fa fa-lg fa-chevron-circle-right" aria-hidden="true"></i>
                                        <?=preg_replace('/<img[^>]+>/i', '[이미지]', nl2br(preg_replace('/<\/?p[^>]*>/i', '', $val['latestComment']['comment'])))?>
                                        <br><small class="text-muted">( <?=$val['latestComment']['regDt']?> <?=$val['latestComment']['regManagerName']?> )</small>
                                        <div class="btn btn-gray btn-sm btn-more-memo" data-sno="<?=$val['sno']?>" data-div="produce">+더보기 (<?=number_format($val['commentCnt'])?>)</div>
                                    <?php } ?>

                                </td>
                            </tr>
                        <?php } ?>

                        <?php
                    }
                } else {
                    echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
                }
                ?>
                </tbody>
            </table>

    <div class="table-action clearfix">

        <div class="pull-left"></div>
        <div class="pull-right">        </div>

        <!--
        <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        -->
    </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>


<script type="text/javascript">

    var myFnc = function(){
        location.reload();
    }


    $(()=>{

        $('#sort').click(function(){
            $('#sort-hidden').val($(this).val());
        });


        $('.excel-submit').click(function(){
            $('#frmExcel').submit();
        });

        const getSelectedData = function(){
            const selectedProjectCnt = $('input[name*="sno"]:checked').length;
            if(0 >= selectedProjectCnt){
                $.msg('선택된 프로젝트가 없습니다.', "", "warning");
                return false;
            }
            const snoList = [];
            $('input[name*="sno"]:checked').each(function(){
                snoList.push( $(this).val() );
            });
            return {
                selectedCnt : selectedProjectCnt,
                snoList : snoList,
            };
        }

        $('.btn-batch-next-step').on('click',function(){
            const selectedData = getSelectedData();
            if( false === selectedData ) return false;
            $.msgConfirm('스케쥴 확정을 요청하시겠습니까?', '확정이 되지 않을 경우 \'생산스케쥴 입력\' 단계에 다시 등록될 수 있습니다.').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    let snoList = selectedData.snoList.join(',');
                    $.imsPost('setBatchProduceChangeStep',{
                        snoList : snoList,
                        changeStep : '20',
                        reason : '생산스케쥴 입력하여 확인 및 확정 요청',
                    }).then(()=>{
                        location.reload();
                    });
                }
            });
        });

        const setBatchProduceChangeStep = function(msg, step){
            const selectedData = getSelectedData();
            if( false === selectedData ) return false;
            setProduceChangeStep(msg, step, selectedData.snoList.join(','));
        }
        $('.btn-batch-step-confirm').on('click',function(){
            setBatchProduceChangeStep('스케쥴을 확정 하시겠습니까?',30);
        });
        $('.btn-batch-step-back').on('click',function(){
            setBatchProduceChangeStep('스케쥴을 반려 하시겠습니까?',10);
        });

        $('.btn-modify').on('click',function(){
            const sno = $(this).closest('.field-parent').data('sno');
            location.href=`ims_project_reg.php?sno=${sno}`;
        });

        $('.btn-delete').on('click',function(){
            const sno = $(this).data('sno');
            $.msgConfirm('삭제시 복구가 불가능 합니다. 계속 하시겠습니까?', "").then((result)=>{
                if( result.isConfirmed ){
                    $.postAsync('<?=$imsAjaxUrl?>',{
                        mode:'deleteData',
                        sno:sno,
                        target:DATA_MAP.PROJECT
                    }).then(()=>{
                        $.msg('처리 되었습니다.', "", "success").then(()=>{
                            location.reload();
                        });
                    });
                }
            });
        });

        $('.field-customer').hover(function(){
            $('.btn-hide-process').hide();
            $(this).find('.btn-hide-process').show();
        },()=>{
            $('.btn-hide-process').hide();
        });

        $('.btn-reg').click(()=>{
            location.href='./ims_project_reg.php';
        });

        $('.schedule-update').click(function(){
            openScheduleLayer($(this).data('sno'));
        });

        $('.simple-download').click(()=>{
            location.href = "<?=$requestUrl?>";
        });


        $('#recap-table-div').css('height', (screen.height-200) + 'px');

        const setAffix = function(){
            if ($(document).scrollTop() > 360) {
                $('#affix-show-type2').show();
                $('#affix-show-type1').hide();
            }else{
                $('#affix-show-type1').show();
                $('#affix-show-type2').hide();
            }
        }

        $(window).resize(function (e) {
            setAffix();
        });
        $(window).scroll(setAffix);

        /*$('#recap-table-div').scroll(function(){
            $.cookie("tableScroll", $(this).scrollTop());
        });
        //스크롤 이동
        $('#recap-table-div').scrollTop($.cookie("tableScroll"));*/

    });

</script>
<?php include './admin/ims/library_all.php'?>
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

    <div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;">
        <section id="affix-show-type1">
            <h3><?= end($naviMenu->location); ?></h3>

            <?php if(!empty($stepManagerInfo)) { ?>
                <div class="relative">
                    <div class="list-photo" style="background-image:url('../..<?=$stepManagerInfo['dispImage']?>');"></div>
                    <div class="list-photo-title">담당 : <?=$stepManagerInfo['managerNm']?> <?=$stepManagerInfo['positionName']?>
                        <div class="font-14">(
                            <?=$stepManagerInfo['cellPhone'] ?>
                            <?=empty($stepManagerInfo['email'])?'':"<a href='mailto:{$stepManagerInfo['email']}' class='sl-blue'>{$stepManagerInfo['email']}</a>"?>
                            )</div>
                    </div>
                </div>
            <?php } ?>

            <div class="btn-group">
                <?php if(!empty($isSales) || !empty($isAuth) ) { ?>
                    <input type="button" value="<?=$regBtnName?>" class="btn btn-red btn-reg hover-btn" />
                <?php } ?>
                <?php if(!empty($isDev)) { ?>
                    <!--
                    <input type="button" value="프로젝트 일괄 등록" class="btn btn-red-line"  onclick="$('.excel-upload-goods-info').show('fade')"  />
                    -->
                <?php } ?>
            </div>
        </section>

        <table class="table table-rows" id="affix-show-type2" style="margin:0 !important; display: none ">
            <colgroup>
                <!--공통 열 colgroup-->
                <?php include './admin/ims/list/_fixed_colgroup.php'?>
                <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                    <col style="width:<?=$stepValue['col']?>%" />
                <?php } ?>
                <col style="width:3%" />
            </colgroup>
            <thead>
            <tr>
                <th>
                    <div style="width:14px; height:14px"></div>
                </th>
                <th>번호</th>
                <?php include './admin/ims/list/_fixed_title.php'?>
                <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                    <th style='<?=$stepValue['titleStyle']?>' ><?=$stepValue['title']?></th>
                <?php } ?>
                <th>등록/수정일</th>
            </tr>
            </thead>
        </table>

    </div>

    <!--
    <section class="excel-upload-section excel-upload-goods-info display-none">
        <div class="table-title">
            프로젝트일괄 등록
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
                    <th>프로젝트 등록</th>
                    <td colspan="3">
                        <form id="frmExcel" name="frmExcel" action="./ims_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                            <div class="form-inline">
                                <input type="hidden" name="runMethod" value="iframe"/>
                                <input type="hidden" name="mode" value="saveBatchProject"/>
                                <input type="file" name="excel" value="" class="form-control width50p" />
                                <input type="button"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                            </div>
                        </form>
                        <input type="button" value="고객등록양식" class="btn btn-white btn-icon-excel mgt10" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/ims_customer.xlsx')?>&fileName=<?=urlencode('IMS고객등록_템플릿.xls')?>'">
                    </td>
                </tr>
            </table>
        </div>
    </section>
    -->

<?php include './admin/ims/list/_common_search.php'?>

<form id="frmList" action="" method="get" target="ifrmProcess">

    <?php if( 'step10' !== $requestParam['status'] ) { ?>
        <ul class="nav nav-tabs mgb0" role="tablist" style="border-bottom:none!important;">
            <li role="presentation" <?=$requestParam['view'] !== 'style' ? 'class="active"' : ''?>>
                <a href="../ims/ims_project_list.php?view=project&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">프로젝트별</a>
            </li>
            <li role="presentation" <?=$requestParam['view'] == 'style' ? 'class="active"' : ''?>>
                <a href="../ims/ims_project_list.php?view=style&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">스타일별</a>
            </li>
            <?php if( empty($requestParam['status']) ) { ?>
            <li role="presentation" >
                <a href="../ims/ims_document_status.php">자료현황</a>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <div class="table-header form-inline">
        <div class="pull-left">
        <span class="font-15">
        검색
        <strong>
            <?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong> 건
        </span>
        </div>
        <div class="pull-right">
            <div>

                <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀 다운로드</button>
                
                <?php if( $requestParam['view'] === 'style' && false ) {?>
                    <button type="button" class="btn btn-white btn-icon-excel simple-download" >생산가확정 일괄등록 양식 다운로드</button>
                <?php } ?>

                <?php if( 'step60' == $requestParam['status'] ) { ?>
                <input type="button" value="발주/사양서 점검항목" class="btn btn-white" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/prd_order_check_guide.pdf')?>&fileName=<?=urlencode('발주(사양서).pdf')?>'">
                <?php } ?>

                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows">
        <colgroup>
            <!--공통 열 colgroup-->
            <?php include './admin/ims/list/_fixed_colgroup.php'?>
            <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                <col style="width:<?=$stepValue['col']?>%" />
            <?php } ?>
            <col style="width:3%" />
        </colgroup>
        <thead>
        <tr>
            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
            <th>번호</th>
            <?php include './admin/ims/list/_fixed_title.php'?>
            <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                <th style='<?=$stepValue['titleStyle']?>' ><?=$stepValue['title']?></th>
            <?php } ?>
            <th>등록/수정일</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                ?>
                <tr class="center field-parent" data-sno="<?=$val['sno']?>">
                    <!--공통 열 td-->
                    <?php include './admin/ims/list/_fixed_td.php'?>
                    <?php foreach ($stepItem as $stepKey => $stepValue) { ?>

                        <?php if('style' === $stepValue['type']) { ?>
                            <td class="<?=$stepValue['class']?>" style="text-align: center">
                                <?php if( empty($val[$stepKey])) { ?>
                                    <span class="text-muted">스타일미등록</span>
                                <?php }else{ ?>
                                    <span class="font-14 <?=$val[$stepValue['textClass']]?>"><?=$val[$stepKey]?></span>
                                <?php } ?>
                            </td>
                        <?php } ?>

                        <?php if('text' === $stepValue['type']) { ?>
                            <td class="<?=$stepValue['class']?>" style="text-align: center">
                                <?php if( empty($val[$stepKey]) && 'styleWithCount' === $stepKey ) { ?>
                                    <span class="text-muted">스타일미등록</span>
                                <?php } ?>
                                <span class="<?=$val[$stepValue['textClass']]?>">
                                    <?=str_replace('\\','',nl2br($val[$stepKey]))?>
                                </span>
                                <br><span class=""><?=$val[$stepValue['addKey']]?></span>
                            </td>
                        <?php } ?>

                        <?php if('comment' === $stepValue['type']) { ?>
                            <td class="<?=$stepValue['class']?>" style="text-align: center">
                                <div class="btn btn-gray btn-sm btn-call-with" data-sno="<?=$val['sno']?>" data-div="">코멘트(<?=number_format($val['commentCnt'])?>)</div>
                            </td>
                        <?php } ?>

                        <?php if('number' === $stepValue['type']) { ?>
                            <td class="<?=$stepValue['class']?>"><?=number_format($val[$stepKey]); ?></td>
                        <?php } ?>
                        <?php if('percent' === $stepValue['type']) { ?>
                            <td class="<?=$stepValue['class']?>"><?=round($val[$stepKey]); ?>%</td>
                        <?php } ?>

                        <?php if('img' === $stepValue['type']) { ?>
                            <td class="font-num ">
                                <a href="<?=$targetPage?>?sno=<?=$val['sno']?>&status=<?=$val['projectStatus']?>" class="ims-project-no text-danger">
                                    <?php if (!empty($val['fileThumbnail'])) { ?>
                                        <img src="<?=$val['fileThumbnail']?>" width="40">
                                    <?php }else{ ?>
                                        <img src="/data/commonimg/ico_noimg_75.gif" width="40">
                                    <?php } ?>
                                </a>
                            </td>
                        <?php } ?>
                    <?php } ?>
                    <td class="center">
                        <div><?=gd_date_format('y/m/d',$val['regDt']) ?></div>
                        <div class="text-muted">
                            <?=gd_date_format('y/m/d',$val['modDt']) ?>
                        </div>
                    </td>
                </tr>

                <?php if(gd_isset($checked['showMemo']['y'])){ ?>
                <tr>
                    <td colspan="99" style="padding:5px!important;">
                        <?php if( 3 != $val['projectType'] && 4 != $val['projectType'] ){ ?>
                            <table class="w100 table-rows ims-list-sub-table ">
                                <colgroup>
                                    <col class="width-sm"/>
                                    <col class="width-sm"/>
                                    <col class="width-sm"/>
                                    <col class="width-sm"/>

                                    <?php if( $isDev ) { ?>
                                    <col class="width-sm"/>
                                    <?php } ?>
                                    <col class="width-sm"/>
                                    <!--<col class="width-sm"/>-->
                                    <!--<col class="width-sm"/>
                                    <col class="width-sm"/>-->
                                    <!--<col class="width-sm"/>-->
                                </colgroup>
                                <tr>
                                    <th class="title">퀄리티수배</th>
                                    <th class="title">BT</th>
                                    <th class="title">가견적</th>
                                    <th class="title">생산가</th>
                                    <?php if( $isDev ) { ?>
                                    <th class="title">판매가</th>
                                    <?php } ?>
                                    <th class="title">가발주</th>
                                    <th class="title">작지작업</th>
                                    <!--<th class="title">작지파일</th>-->

                                    <!--<th class="title">고객견적파일</th>
                                    <th class="title">영업확정파일</th>-->

                                    <!--<th class="title">아소트</th>-->
                                </tr>
                                <tr>
                                    <td class="<?=$val['fabricStatusColor']?>">
                                        <span class="font-11"><?=$val['fabricStatusKr']?></span>
                                        <br>
                                        <?php foreach($val['fabricNational'] as $faKey => $faValue) { ?>
                                            <?php if( 1 & $faValue){ ?><span class="flag flag-16 flag-kr"></span><?php } ?>
                                            <?php if( 2 & $faValue){ ?><span class="flag flag-16 flag-cn"></span><?php } ?>
                                            <?php if( 4 & $faValue){ ?><span class="flag flag-16 flag-market"></span><?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?=$val['btStatusIcon']?>
                                    </td>
                                    <td>
                                        <?=$val['estimateStatusIcon']?>
                                    </td>
                                    <td>
                                        <?=$val['costStatusIcon']?>
                                    </td>
                                    <?php if( $isDev ) { ?>
                                    <td>
                                        <?=$val['priceStatusIcon']?>
                                    </td>
                                    <?php } ?>
                                    <td>
                                        <?=$val['orderStatusIcon']?>
                                    </td>
                                    <td>
                                        <?=$val['workStatusIcon']?>
                                    </td>
                                    <!--
                                    <td><?=$val['workIcon']?></td>
                                    -->
                                    <!--
                                    <td><?=$val['estimateIcon']?></td>
                                    <td><?=$val['orderAcceptIcon']?></td>
                                    -->
                                    <!--<td>-</td>-->
                                </tr>
                            </table>
                        <?php }else{ ?>

                            <table class="w60 table-rows ims-list-sub-table ">
                                <colgroup>
                                    <col class="width-sm"/>
                                    <col class="width-sm"/>
                                    <?php if( $isDev ) { ?>
                                    <col class="width-sm"/>
                                    <?php } ?>
                                    <col class="width-sm"/>
                                    <col class="width-sm"/>
                                </colgroup>
                                <tr>
                                    <th class="title">가견적</th>
                                    <th class="title">생산가</th>
                                    <?php if( $isDev ) { ?>
                                    <th class="title">판매가</th>
                                    <?php } ?>
                                    <th class="title">작지작업</th>
                                    <th class="title">가발주</th>
                                </tr>
                                <tr>
                                    <td>
                                        <?=$val['estimateStatusIcon']?>
                                    </td>
                                    <td>
                                        <?=$val['costStatusIcon']?>
                                    </td>
                                    <?php if( $isDev ) { ?>
                                    <td>
                                        <?=$val['priceStatusIcon']?>
                                    </td>
                                    <?php } ?>
                                    <td>
                                        <?=$val['workStatusIcon']?>
                                    </td>
                                    <td>
                                        <?=$val['orderStatusIcon']?>
                                    </td>
                                </tr>
                            </table>

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
        <div class="pull-right">
            <!--
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
            -->
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<?php include './admin/ims/list/_common_script.php'?>

<script>
    $(()=>{
        $('.simple-download').click(function(){
            let sno = $(this).data('sno');
            let type = $(this).data('type');
            location.href = "<?=$requestUrl?>&sno="+sno+"&type="+type;
        });

        const setAffix = function(){
            if ($(document).scrollTop() > 400) {
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

    });
</script>


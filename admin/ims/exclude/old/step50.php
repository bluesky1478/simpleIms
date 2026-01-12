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
        <section id="affix-show-type2" style="margin:0 !important; display: none ">
            <table class="table table-rows" style="margin-bottom:0 !important; "></table>
        </section>
    </div>

<?php include './admin/ims/list/_common_search.php'?>

<form id="frmList" action="" method="get" target="ifrmProcess">

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
                <!--<button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀 다운로드</button>-->
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows" id="main-table">
        <colgroup>
            <col style="width:1%" /><!--1-->
            <col style="width:2%" /><!--2-->
            <?php foreach($listSetupData as $each) { ?>
                <col class="w-<?=$each[1]?>p" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
            <th>번호</th>
            <?php foreach($listSetupData as $each) { ?>
                <th><?=$each[0]?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                ?>
                <tr class="center field-parent " data-sno="<?=$val['sno']?>">
                    <?php include './admin/ims/list/step_fixed.php'?>
                    <!-- 구분 -->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <?=$val['urgency']?>
                    </td>
                    <!-- 담당자 -->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div><?=$val['salesManagerNm']?></div>
                        <div><?=$val['designManagerNm']?></div>
                    </td>
                    <td class="bg-light-gray center">예정일</td>
                    <?php foreach($listSetupData as $each) { ?>

                        <?php if(!empty($each[2])) { ?>
                            <?php if( 'customerWaitMemo' === $each[2] ) { ?>
                                <td class="text-left relative" rowspan="2">
                                    <span class="cursor-pointer hover-btn" onclick="openProjectUnit(<?=$val['sno']?>, '<?=$each[2]?>', '<?=$each[3]?>','text','<?=$each[0]?>')">
                                        <?=$val[$each[2].$each[4]]?>
                                    </span>
                                    <div style="position:absolute; top:0;right:0; font-size: 14px !important; color:#f78800; display: none"  class="font-12 comment-cnt-<?=$each[2]?> comment-cnt">
                                        <i class="fa fa-circle" aria-hidden="true"></i>
                                    </div>
                                    <div style="position:absolute; top:3px;right:1px; color:#fff; font-size: 9px; text-align: center; width:10px; display: none"  class="font-12 comment-cnt-<?=$each[2]?> comment-cnt">
                                        0
                                    </div>
                                </td>
                            <?php }else{ ?>
                                <td class="center bg-light-yellow relative">
                                    <span class="cursor-pointer hover-btn" onclick="openProjectUnit(<?=$val['sno']?>, '<?=$each[2]?>', '<?=$each[3]?>','picker','<?=$each[0]?>')">
                                    <?php if( 'customerOrderDeadLine' == $each[2] ){ ?>
                                        <?php if( '-' !== $val[$each[2].$each[4]]) { ?>
                                            <?=$val[$each[2].$each[4].'2']?>
                                        <?php }else{ ?>
                                            <span class="text-muted">미입력</span>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <?=$val[$each[2].$each[4]]?>
                                    <?php } ?>
                                    </span>
                                    <div style="position:absolute; top:0;right:0; font-size: 14px !important; color:#f78800; display: none"  class="font-12 comment-cnt-<?=$each[2]?> comment-cnt">
                                        <i class="fa fa-circle" aria-hidden="true"></i>
                                    </div>
                                    <div style="position:absolute; top:3px;right:1px; color:#fff; font-size: 9px; text-align: center; width:10px; display: none"  class="font-12 comment-cnt-<?=$each[2]?> comment-cnt">
                                        0
                                    </div>
                                </td><!--예정일-->
                            <?php } ?>

                        <?php } ?>
                    <?php } ?>
                </tr>
                <tr class="">
                    <td class="bg-light-gray center">완료일</td>
                    <td class="center">
                        <?=$val['qbCompleteDtShort']?>
                    </td><!--QB확정일-->
                    <td class="center">
                        <?=$val['fakeOrderCompleteDtShort']?>
                    </td><!--가발주-->
                    <td class="center">
                        <?=$val['custOrderCompleteDtShort']?>
                    </td><!--고객예상발주일  -->
                    <td class="center">
                        <?=$val['orderCompleteCompleteDtShort']?>
                    </td><!--발주DL-->
                </tr>
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


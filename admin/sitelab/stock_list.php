<script>
    // 정렬&출력수
    $(function(){
        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });

        //simple excel download
        $('.simple-download').click(function(){
            location.href = "<?=$requestUrl?>";
        });

    });

    /**
     * 카테고리 연결하기 Ajax layer
     */
    function layer_register(typeStr, mode, isDisabled) {
        var addParam = {
            "mode": mode,
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        if (!_.isUndefined(isDisabled) && isDisabled == true) {
            addParam.disabled = 'disabled';
        }
        layer_add_info(typeStr,addParam);
    }
</script>

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 20); ?>"/>
    <div class="table-title gd-help-manual">
        재고 이력 검색
    </div>
    <?php include('stock_list_search.php'); ?>
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
            검색
            <strong><?= empty($page->recode['total'])? 0 : $page->recode['total']; ?></strong>
            건
        </div>
        <div class="pull-right">
            <div>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 20)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows">
        <colgroup>
            <col class="width-xs"/>
            <?php foreach ($data as $val => $key) { ?>
            <col/>
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <!--<th>
                <input type="checkbox" id="chk_all" class="js-checkall" data-target-name="chk"/>
            </th>-->
        <?php foreach ($listTitles as $val) { ?>
            <th><?=$val?></th>
        <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="font-num">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--상품번호-->
                    <td class="center text-nowrap"><?=$val['goodsNo']; ?></td>
                    <!--공급사-->
                        <td class="center text-nowrap"><?=$val['companyNm']; ?></td>
                    <!--상품명-->
                    <td class="center text-nowrap"><?=$val['goodsNm']; ?></td>
                    <!--옵션명-->
                    <td class="center text-nowrap"><?=$val['optionNm']; ?></td>
                    <!--유형-->
                        <td class="center text-nowrap"><?=$stockTypeMap[$val['stockType']]; ?></td>
                    <!--사유-->
                    <td class="center text-nowrap"><?=$stockReasonMap[$val['stockReason']]; ?></td>
                    <!--수량-->
                    <td class="center text-nowrap <?=$val['stockCntColor']?>">
                        <b><?=number_format($val['stockCnt']); ?></b>
                    </td>
                    <!--주문번호-->
                    <td class="center text-nowrap order-no">
                        <?php if ($val['firstSaleFl'] == 'y') { ?>
                            <p class="mgb0"><img src="<?=PATH_ADMIN_GD_SHARE?>img/order/icon_firstsale.png" alt="첫주문" /></p>
                        <?php } ?>

                        <?php if( !empty($val['orderNo']) ) { ?>
                            <a href="#;" onclick="javascript:open_order_link('<?=$val['orderNo']?>', '<?=$openType?>', '<?=$isProvider?>')" title="주문번호" class="font-num<?=$isUserHandle ? ' js-link-order' : ''?>" data-order-no="<?=$val['orderNo']?>" data-is-provider="<?= $isProvider ? 'true' : 'false' ?>"><?= $val['orderNo']; ?></a><img src="<?=PATH_ADMIN_GD_SHARE?>img/icon_grid_open.png" alt="팝업창열기" class="hand mgl5" border="0" onclick="javascript:order_view_popup('<?=$val['orderNo']?>', '<?=$isProvider?>');" />
                        <?php } ?>

                        <?php if ($val['orderChannelFl'] == 'naverpay') { ?>
                            <p>
                                <a href="#;" onclick="javascript:open_order_link('<?=$val['orderNo']?>', '<?=$openType?>', '<?=$isProvider?>')" title="주문번호" class="font-num<?=$isUserHandle ? ' js-link-order' : ''?>" data-order-no="<?=$val['orderNo']?>" data-is-provider="<?= $isProvider ? 'true' : 'false' ?>"><img src="<?= UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'naverpay.gif')->www() ?>"/> <?= $val['apiOrderNo']; ?></a>
                            </p>
                        <?php } else if($val['orderChannelFl'] == 'payco') { ?>
                            <img src="<?= UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'payco.gif')->www() ?>"/>
                        <?php } else if ($val['orderChannelFl'] == 'etc') { ?>
                            <p>
                                <a href="#;" onclick="javascript:open_order_link('<?=$val['orderNo']?>', '<?=$openType?>', '<?=$isProvider?>')" title="주문번호" class="font-num<?=$isUserHandle ? ' js-link-order' : ''?>" data-order-no="<?=$val['orderNo']?>" data-is-provider="<?= $isProvider ? 'true' : 'false' ?>">
                                    <img src="<?= UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'etc.gif')->www() ?>"/> <?= $val['apiOrderNo']; ?>
                                </a>
                            </p>
                        <?php } else { } ?>
                        <?php if (empty($val['trackingKey']) === false) {echo '<div class="c-gdred">' . $channel['paycoShopping'] . '</div>';}?>
                    </td>
                    <!--회원명-->
                    <td class="js-member-info" data-member-no="<?= $val['memNo'] ?>" data-member-name="<?= $val['orderName'] ?>" data-cell-phone="<?= $val['smsCellPhone'] ?>">
                        <?= $val['memNm'] ?>
                        <p class="mgb0">
                            <?php if (!$val['memNo']) { ?>
                                <?php if (!$val['memNoCheck']) { ?>
                                <?php } else { ?>
                                    <span class="font-kor">(탈퇴회원)</span>
                                <?php } ?>
                            <?php } else { ?>
                                <?php if (!$isProvider) { ?>
                                    <button type="button" class="btn btn-link font-eng js-layer-crm" data-member-no="<?= $val['memNo'] ?>">(<?= $val['memId'] ?>)
                                <?php } else { ?>
                                    (<?= $val['memId'] ?>)
                                <?php } ?>
                                </button>
                            <?php } ?>
                        </p>
                    </td>
                    <!--회원ID-->
                    <td class="font-date nowrap"><?=$val['memId']; ?></td>
                    <!--등록일자-->
                    <td class="cen">
                        <?= str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $val['regDt'])); ?>
                    </td>
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

        <div class="pull-left">
            <!--
            <button type="button" class="btn btn-white" id="btnApply">선택 가입승인</button>
            <button type="button" class="btn btn-white" id="btnDelete">선택 탈퇴처리</button>
            -->
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>



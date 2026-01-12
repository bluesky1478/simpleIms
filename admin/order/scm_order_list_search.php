
<div class="search-detail-box form-inline">
    <table class="table table-cols">
        <colgroup>
            <col class="width-md">
            <col class="width-3xl">
            <col class="width-md">
            <col class="width-3xl">
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
                <th>검색어</th>
                <td>
                    <?= gd_select_box('key', 'key', $search['combineSearch'], null, gd_isset($search['key']), null, null, 'form-control'); ?>
                    <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>"  class="form-control" id="keyword" />
                </td>
                <th>주문상태</th>
                <td >
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="all"   <?=gd_isset($checked['orderStatus']['all']); ?> />전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="o1" <?=gd_isset($checked['orderStatus']['o1']); ?> />입금대기
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="p1" <?=gd_isset($checked['orderStatus']['p1']); ?> />결제완료
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="p2" <?=gd_isset($checked['orderStatus']['p2']); ?> />결제완료(발송대기)
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="p3" <?=gd_isset($checked['orderStatus']['p3']); ?> />결제완료(출고대기)
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="g1" <?=gd_isset($checked['orderStatus']['g1']); ?> />상품준비중
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="d1" <?=gd_isset($checked['orderStatus']['d1']); ?> />배송중
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="d2" <?=gd_isset($checked['orderStatus']['d2']); ?> />배송완료
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderStatus" value="s1" <?=gd_isset($checked['orderStatus']['s1']); ?> />구매확정
                    </label>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td >
                    <div class="form-inline">

                        <?= gd_select_box('searchDateFl', 'searchDateFl', $search['combineTreatDate'], null, $search['searchDateFl'], null, null, 'form-control '); ?>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>

                        <?= gd_search_date(gd_isset($search['searchPeriod'], 6), 'searchDate[]', false) ?>

                    </div>
                </td>
                <?php if( '8' == $scmNo ){ ?>
                    <th>
                        파트너 구분
                    </th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="memberType" value="all" <?= gd_isset($checked['memberType']['all']); ?>/>전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="memberType" value="2" <?= gd_isset($checked['memberType'][2]); ?>/>파트너사 주문
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="memberType" value="1" <?= gd_isset($checked['memberType'][1]); ?>/>정규직원 주문
                        </label>
                    </td>
                <?php }else{?>
                    <th></th>
                    <td></td>
                <?php }?>
            </tr>
            <?php if( 'y' === $scmConfig['orderAcceptFl'] ) { ?>
            <tr>
                <th>승인상태</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="orderAcctStatus" value=""   <?=gd_isset($checked['orderAcctStatus']['']); ?> />전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderAcctStatus" value="1" <?=gd_isset($checked['orderAcctStatus']['1']); ?> />승인대기
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderAcctStatus" value="2" <?=gd_isset($checked['orderAcctStatus']['2']); ?> />승인완료
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="orderAcctStatus" value="3" <?=gd_isset($checked['orderAcctStatus']['3']); ?> />출고불가
                    </label>
                </td>
            </tr>
            <?php } ?>
            <?php if( 'y' === $scmConfig['deliverySelectFl'] ) { ?>
            <tr>
                <th>배송지점</th>
                <td colspan="3">
                    <select class="form-control" id="scm-order-delivery" name="scmOrderDelivery" style="width:550px">
                        <option value="">전체</option>
                        <?php foreach($scmDeliveryList as $scmDeliveryKey => $scmDeliveryData) { ?>
                        <option value="<?=$scmDeliveryData['receiverAddress']?>"  <?=$scmDeliveryData['receiverAddress'] === $search['scmOrderDelivery'] ? 'selected': '' ?> ><?=$scmDeliveryData['subject']?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="table-btn">
    <input type="submit" value="검색" class="btn btn-lg btn-black">
</div>

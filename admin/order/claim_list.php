<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js"></script>

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
        //console.log( '<?=$requestUrl?>' );

        $('.btn-proc').click(function(){
            var sno = $(this).data('sno');
            var claimType = $(this).data('claimtype');
            var claimTypeStr = $(this).data('claimtypestr');
            var orderNo = $(this).data('orderno');
            var childNm = 'order_claim';
            var addParam = {
                mode: 'simple',
                sno: sno,
                orderNo: orderNo,
                claimType: claimType,
                claimTypeStr: claimTypeStr,
                layerTitle: claimTypeStr+' 요청 처리',
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
            };
            layer_add_info(childNm, addParam);
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
        검색
    </div>
    <?php include('claim_list_search.php'); ?>
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
            <col style="width:60px" />
            <col style="width:100px" />
            <col style="width:150px" />
            <col style="width:27%" />
            <col style="width:27%" />
            <col style="width:90px" />
            <?php if(empty($isProvider)) { ?>
            <col style="width:200px" />
            <col style="width:60px" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <?php if(empty($isProvider)) { ?>
                <?php foreach ($listTitles as $val) { ?>
                    <th><?=$val?></th>
                <?php } ?>
            <?php }else{ ?>
                <th>번호</th>
                <th>클레임등록/요청일</th>
                <th>주문자 정보</th>
                <th>보내실 상품(원 상품)</th>
                <th>받으실 상품(교환처리 상품)</th>
                <th>처리상태</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="font-num"><!--번호-->
                        <?=number_format($page->idx--); ?>
                    </td>
                    <td class="center text-nowrap" ><!--등록일-->
                        <?='0000-00-00'===$val['claimRegDt']?'확인중':$val['claimRegDt']; ?>
                        <br><small class="text-muted">(<?=gd_date_format('Y-m-d',$val['regDt']); ?>)</small>
                        <br><b><?=$val['companyNm']; ?></b>
                        <br>
                        <span class="font-15 text-danger bold"><?=$val['claimTypeKr']; ?></span>
                    </td>
                    <td class="center text-nowrap" ><!--주문자명-->
                        <?= $val['memNm'] ?>
                        <p class="mgb0">
                            <?php if (!$val['memNo']) { ?>
                                <?php if (!$val['memNoCheck']) { ?>
                                    <span class="font-kor">(비회원)</span>
                                <?php } else { ?>
                                    <span class="font-kor">(탈퇴회원)</span>
                                <?php } ?>
                            <?php } else { ?>
                                <?php if (!empty($isProvider)) { ?>
                                    <button type="button" class="btn btn-link font-eng js-layer-crm" data-member-no="<?= $val['memNo'] ?>">(<?= $val['memId'] ?>)
                                <?php } else { ?>
                                    (<?= $val['memId'] ?>)
                                <?php } ?>
                                </button>
                            <?php } ?>
                        </p>
                        <div class="text-muted"><?=$val['nickNm']; ?></div>
                        <div class="text-muted"><?=$val['masterCellPhone']; ?></div>
                    </td>
                    <td class="left text-nowrap " style="vertical-align: top !important;" ><!--신청상품-->

                        <b>클레임번호 : <span class="text-danger"><?=$val['sno']; ?></span></b> /

                        <?php if ( empty($isProvider)  ) { ?>
                        <b>게시판 : <a href="../board/article_view.php?&bdId=qa&sno=<?=$val['bdSno']; ?>" target="_blank" class="text-danger"><?=$val['bdSno']; ?></a></b>
                        <?php } ?>

                        <b>주문 : <a href="../order/order_view.php?orderNo=<?=$val['orderNo']; ?>" target="_blank" class="text-blue"><?=!empty($val['orderNo'])?' <span style="color:#000;font-weight: normal"></span> '.$val['orderNo'] : ''; ?></a></b>
                        <?php if( !empty($val['orderStatusKr'])) {?><small class="text-muted">(<?=$val['orderStatusKr']; ?>)</small><?php } ?>

                        <table class="table table-rows" >
                            <colgroup>
                                <col style="width:100px"/>
                                <col />
                                <col style="width:70px"/>
                            </colgroup>
                            <?php foreach( $val['claimGoods'] as $claimGoods ){ ?>
                                <?php foreach( $claimGoods['option'] as $claimOption ){ ?>
                                    <?php if( !empty($claimOption['optionCnt'])) { ?>
                                    <tr>
                                        <td>
                                            <?php if(empty($claimOption['optionCode'])) {?>
                                                <small class="text-muted">코드없음</small>
                                            <?php }else{ ?>
                                                <?=$claimOption['optionCode']?>
                                            <?php } ?>
                                        </td>
                                        <td class="text-left">
                                            <?=$claimGoods['goodsNm']?>

                                            <?php if(!empty($claimOption['optionName'])) { ?>
                                                _<?=$claimOption['optionName']?>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?=number_format($claimOption['optionCnt'])?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </table>

                        <?php if(empty($isProvider)) { ?>

                            <?php if( empty($val['rtSno']) ) { ?>
                                <div class="return-div">
                                    <div class="btn btn-white btn-sm btn-return" data-sno="<?=$val['sno']?>">반품요청</div>
                                </div>
                            <?php }else{ ?>
                                <div class="return-div">
                                    반품번호 : <b class="text-dark"><?= $val['rtSno'] ?></b>
                                    - 상태 : <?=\Component\Erp\ErpCodeMap::WAREHOUSE_RETURN[$val['returnStatus']]?>
                                    / 제품상태 : <?=\Component\Erp\ErpCodeMap::WAREHOUSE_RETURN_PRD[$val['prdStatus']]?>
                                    <?php if( !empty($val['partnerMemo']) ) { ?>
                                        <div><?=$val['partnerMemo']?></div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <!--'returnStatus','prdStatus','partnerMemo'-->

                        <?php } ?>


                        <?php if( !empty($val['contents']) ){ ?>
                        <div style="word-break: break-all;white-space: normal; padding:3px; background-color: #fffef4; " class="border-radius-10">
                            <b>문의내용:</b><?=strip_tags($val['contents'])?>
                        </div>
                        <?php } ?>

                    </td>
                    <td class="left text-nowrap" style="vertical-align: top !important;" ><!--교환상품-->
                        <?php if(!empty($val['exchangeGoods']) ) { ?>
                            <div><b>교환요청 상품</b></div>
                        <table class="table table-rows" >
                            <!--<tr>
                                <td class="center bg-light-gray" style="width:100px">제품코드</td>
                                <td class="center bg-light-gray">제품명</td>
                                <td class="center bg-light-gray" style="width:70px">수량</td>
                            </tr>-->
                            <colgroup>
                                <col style="width:100px"/>
                                <col />
                                <col style="width:70px"/>
                            </colgroup>
                            <?php foreach( $val['exchangeGoods'] as $exchangeGoods ){ ?>
                                <?php foreach( $exchangeGoods['goodsOptionList'] as $exchangeOption ){ ?>
                                    <?php if($exchangeOption['optionCount']>0) { ?>
                                    <tr>
                                        <td><?=$exchangeOption['optionCode']?></td>
                                        <td class="text-left">
                                            <?=$exchangeGoods['goodsNm']?>
                                            <?php if(!empty($exchangeOption)) { ?>
                                                _<?=$exchangeOption['optionName']?>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?=number_format($exchangeOption['optionCount'])?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </table>
                        <?php } ?>

                        <?php if( count($val['invoiceNo']) > 0 ) { ?>
                        <div >
                            원송장 정보
                            <?php foreach($val['invoiceNo'] as $invoice) { ?>
                                <div>
                                    <?=$deliveryCompanyMap[$invoice['invoiceCompanySno']]?>
                                    <?=$invoice['invoiceNo']?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <?php if( !empty($val['contents']) && 'asiana' == \Session::get('manager.managerId') ){ ?>
                        <div style=" word-break: break-all;white-space: normal; display: none ">
                            답변:<?=strip_tags($val['answerContents'])?>
                        </div>
                        <?php } ?>

                    </td>
                    <td class="center text-nowrap" ><!--처리상태-->
                        <?php if(empty($isProvider)) { ?>
                            <select class="form-control claim-proc <?=$val['claimStatusColor']?>"  style="width:100%;text-align: center">
                                <option value="1" <?=1==$val['claimStatus']?'selected':''?>  >처리중</option>
                                <option value="2" <?=2==$val['claimStatus']?'selected':''?> class="text-green">처리완료</option>
                                <option value="9" <?=9==$val['claimStatus']?'selected':''?> class="text-danger">처리불가</option>
                            </select>
                            <small class="text-muted"><?=$val['claimCompleteDt']?></small>
                        <?php }else{ ?>

                            <?php if(empty($val['claimStatus'])){?><span>확인중</span><?php } ?>
                            <?php if(1==$val['claimStatus']){?><span>처리중</span><?php } ?>
                            <?php if(2==$val['claimStatus']){?><span class="text-green">처리완료</span><?php } ?>
                            <?php if(9==$val['claimStatus']){?><span class="text-danger">처리불가</span><?php } ?>

                        <?php } ?>
                    </td>
                    <?php if(empty($isProvider)) { ?>
                    <td class="text-left text-nowrap" ><!--메모-->
                        <?php if( !empty($val['refundData']['refundTypeKr']) ) { ?>
                        <div style="margin:3px">
                            <?=$val['refundData']['refundTypeKr']; ?>
                        </div>
                        <?php } ?>
                        <div>
                            <textarea class="form-control etc-memo textarea" rows="3" placeholder="기타 메모란"  ><?=$val['memo']?></textarea>
                        </div>
                    </td>
                    <td class="center text-nowrap" ><!--기타-->
                        <div class="btn btn-white btn-sm btn-save" data-sno="<?=$val['sno']?>">저장</div>
                        <!--
                        <div class="btn btn-white btn-sm btn-add-cost" data-sno="<?=$val['sno']?>">비용추가</div>
                        <br><small>추가결제1:9,530원<span class="text-muted">(결제완료)</span></small>
                        <br><small>추가결제2:29,530원<span class="text-muted">(미결제)</span></small>
                        -->
                    </td>
                    <?php } ?>
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
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<script>
    $(function(){
        $('.btn-save').click(function(){
            var sno = $(this).data('sno');
            var claimStatus = $(this).closest('tr').find('.claim-proc').val();
            var memo = $(this).closest('tr').find('.etc-memo').val();
            $.postAsync('<?=$claimApiUrl?>',{
                mode:'updateClaim',
                'sno':sno,
                'memo': memo,
                'claimStatus':claimStatus,
            }).then(function(afterClaimData){
                alert('저장 되었습니다.');
            });
        });


        $('.textarea').each(function(){
            let newText = $(this).val().replaceAll("\\n", "\n");
            $(this).val(newText);
        });

        $('.btn-return').on('click',function(){
            let sno = $(this).data('sno');
            let url = `/order/popup/warehouse_return.php?claimSno=${sno}`;
            let win = popup({
                url: url,
                target: '',
                width: 925,
                height: 750,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win.focus();
            return win;
        });

    });
</script>

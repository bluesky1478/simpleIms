<style>
	.goods-grid-area { height: 750px; }
	.goods-grid-act-top select,
	.goods-grid-act-top span { float: left; line-height: 20px; }
	.goods-grie-bottom-info-area>div{ margin-bottom: 3px !important; }
	.goods-grie-bottom-info-area>div:first-child{ margin-top: 3px; }
	.goods-grie-bottom-area {
		width: 100%;
		float: left;
		text-align: center;
		margin-top: 10px;
	}
	.js-field-select-wapper {
		height:650px;
		overflow:scroll;
		overflow-x:hidden;
		border:1px solid #dddddd;
	}

	.js-field-default td { border:1px solid #dddddd; }

	.table-cols { margin-top:3px; margin-bottom:3px; border: 1px solid #dddddd;}

	.add-display-td input[type="checkbox"]{
		margin : 0 !important;
	}
</style>

<div class="goods-grid-area" id="app-member-list">
	<form name="frmPolicyRegister" id="frmPolicyRegister" action="./goods_ps.php" method="post" target="ifrmProcess" >
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm"/>
                    <col>
                    <col class="width-sm"/>
                    <col class="width-sm"/>
                    <col class="width-sx"/>
                </colgroup>
            </table>
        </div>
    </form>
    <form name="frmMemberList" id="frmMemberList" action="./goods_ps.php" method="post" target="ifrmProcess" >
        <div class="js-field-select-wapper">
            <table class="table table-rows">
                <thead>
                <tr>
                    <!--등록일 . 유형 . 사유 . 수량 . 주문번호 . 회원명 . 회원ID-->
                    <th class="width10p">등록일</th>
                    <th class="width10p">유형</th>
                    <th class="width10p">사유</th>
                    <th class="width10p">수량</th>
                    <th class="width10p">주문번호</th>
                    <th class="width10p">회원명</th>
                    <th class="width10p">회원ID</th>
                </tr>
                </thead>
                <tbody >
                <?php
                    $sumCnt = 0;
                    if (gd_isset($data)) {
                        foreach ($data as $val) {
                            $sumCnt += $val['stockCnt'];
                ?>
                <tr>
                    <td class="center text-nowrap"><?=$val['regDt']; ?></td>
                    <td class="center text-nowrap"><?=$stockTypeMap[$val['stockType']]; ?></td>
                    <td class="center text-nowrap"><?=$stockReasonMap[$val['stockReason']]; ?></td>
                    <td class="center text-nowrap <?=$val['stockCntColor']?>">
                        <b><?=number_format($val['stockCnt']); ?></b>
                    </td>
                    <td class="center text-nowrap"><?=$val['orderNo']; ?></td>
                    <td class="center text-nowrap"><?=$val['memNm']; ?></td>
                    <td class="center text-nowrap"><?=$val['memId']; ?></td>
                </tr>
                <?php
                        }
                    } else {
                        echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
                    }
                ?>
                </tbody>
            </table>
        </div>
        <div>
            <b>조회 기간 ( <?=$searchDate['startDate']?> ~ <?=$searchDate['endDate']?>  ) 재고 : <strong class="text-danger"><?=number_format($sumCnt)?></strong></b>
        </div>
    </form>

    <div class="goods-grie-bottom-area">
        <input type="button" value="확인" class="btn btn-white js-close" style="width:118px"/>
        <button type="button" class="btn btn-white btn-icon-excel simple-download_detail" >엑셀다운로드</button>
    </div>

</div>


<script type="text/javascript">
	<!--
	$(document).ready(function () {
        $('.js-close').click(function(){
            $(document).off("keydown");
            layer_close();
        });

        //simple excel download
        $('.simple-download_detail').click(function(){
            console.log("<?=$requestUrl?>");
            location.href = "<?=$requestUrl?>";
        });

	});
	//-->
</script>

<style>
	#layerGoodsListOptionSimpleFrm .content{max-height: 400px;border-bottom: 1px solid #dedede; padding-top:10px; padding-bottom: 30px;}
	#layerGoodsListOptionSimpleFrm .text-center{padding-top: 20px;}
</style>
<form name="layerGoodsListOptionSimpleFrm" id="layerGoodsListOptionSimpleFrm">
    <div>
        <input type="hidden" name="goodsNo" value="<?=$goodsNo?>">
        <input type="hidden" name="mode" value="save_safe_stock">
    </div>
	<div class="content table-responsive">
		<table class="table-cols">
			<thead>
			<tr>
				<th>번호</th>
				<?php
				foreach($getGoodsOptionName as $optionNameVal) {
					?>
					<th class="width-md text-nowrap"><?=$optionNameVal;?></th>
					<?php
				}
				?>
				<th>옵션가</th>
				<th>
                    재고량
                    <br><small class="text-muted">공유가능</small>
                </th>
				<th>안전재고</th>
				<th>공유불가재고</th>
				<th>노출상태</th>
				<th>품절상태</th>
                <th>자체옵션코드</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($goodsOptionInfo as $key => $val) {
				if($val['optionViewFl'] == 'y') {
					$optionViewText = "노출함";
				} else {
					$optionViewText = "노출안함";
				}
                $optionSellText = $stockReason[$val['optionSellFl']];
				?>
				<tr>
					<td class="center lmenu">
						<?= $key+1;?>
					</td>
					<?php
					foreach($getGoodsOptionName as $optionCntKey => $optionCntVal) {
						?>
						<td class="center lmenu text-nowrap">
							<?= $val['optionValue'.($optionCntKey+1)]; ?>
						</td>
						<?php
					}
					?>
					<td class="center lmenu text-nowrap">
						<?= gd_currency_display($val['optionPrice']);?>
					</td>
					<td class="center lmenu">
                        <div><?= number_format($val['stockCnt'] - $val['shareAvailCnt'] );?>개</div>
                        <?php if( $val['shareAvailCnt'] > 0 ) { ?>
                        <div><small class="text-muted"><?=number_format($val['shareAvailCnt'])?>개</small> </div>
                        <?php } ?>
					</td>
                    <td class="center lmenu">
                        <input type="text" class="form-control" style="width:50px;text-align: center" value="<?=$val['safeStockCnt']?>" name="safeStockCnt[<?=$val['sno']?>]" class="safe-stock-cnt">
                    </td>
                    <td class="center lmenu">
                        <input type="text" class="form-control" style="width:50px;text-align: center" value="<?=$val['shareNotCnt']?>" name="shareNotCnt[<?=$val['sno']?>]" class="share-not-cnt">
                    </td>
					<td class="center lmenu">
						<?= $optionViewText;?>
					</td>
					<td class="center lmenu">
						<?= $optionSellText;?>
					</td>
                    <td class="center lmenu text-nowrap">
                        <?= $val['optionCode'];?>
                    </td>
				</tr>
			<?php }?>
			</tbody>
		</table>
	</div>
	<div class="text-center">
		<input type="button" value="저장" class="btn btn-gray js-save" />
		<input type="button" value="닫기" class="btn btn-white js-close" />
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function () {
	    //닫기
        $('.js-close').off('click').on('click',layer_close);
        $('.js-save').off('click').on('click',function(){
            var frmData = $('#layerGoodsListOptionSimpleFrm').serialize();
            console.log(frmData);
            $.post('goods_ps.php', frmData, function (data) {
                dialog_alert('저장 되었습니다.');
            });
        });
	});
</script>

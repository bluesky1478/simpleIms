        <div >
            <table class="table table-rows order-list" style="margin:0px">
                <colgroup>
                    <col style="width:100px!important;" />
                    <?php foreach( $val['optionList'] as $optionData ) {?>
                        <col style="width:<?=100/count($val['optionList'])?>%" />
                    <?php }?>
                </colgroup>
                <tbody class="order-list set-goods-option-list">
                    <tr class="center">
                        <td class="center bg-light-gray" style="width:100px">
                            <b>옵션</b>
                        </td>
                        <?php foreach( $val['optionList'] as $optionData ) {?>
                            <td class="center bg-light-gray" style=" ">
                                <b><?=$optionData['optionName']?></b>
                                <br>
                                <small class="text-muted"><?=$optionData['optionCode']?></small>
                            </td>
                        <?php }?>
                    </tr>

                    <tr>
                        <td class="center text-nowrap bg-light-yellow" style="width:100px">
                            현재
                        </td>
                        <?php foreach( $val['optionList'] as $optionData ) {?>
                            <td class="center" >
                                <b class="display-current-cnt"><?=$optionData['stockCnt']?></b>
                                <input type="hidden" value="<?=$optionData['stockCnt']?>" data-optionsno="<?=$optionData['sno']?>" class="stock-current" >
                            </td>
                        <?php }?>
                    </tr>
                    <tr>
                        <td class="center text-nowrap bg-light-yellow" style="width: 100px">
                            <div class="form-inline">
                                <select class="form-control sel-batch-proc" style="background: #fff">
                                    <option value="add">추가</option>
                                    <option value="subtract">차감</option>
                                    <option value="modify">수정</option>
                                </select>
                                <div class="btn btn-sm btn-red btn-proc-modify" data-goodsno="<?=$val['goodsNo']; ?>" >저장</div>
                            </div>
                        </td>
                        <?php foreach( $val['optionList'] as $optionData ) {?>
                            <td class="center" >
                                <input type="text" placeholder="수정수량" class="stock-modify-cnt inp-stock-modify form-control"></td>
                            </td>
                        <?php }?>
                    </tr>
                    <tr>
                        <td colspan="99">
                            <div class="form-inline">
                                일괄
                                <input type="text" class="form-control batch-proc-value" style="width:70px">
                                <div class="btn btn-sm btn-gray btn-batch-proc">입력</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

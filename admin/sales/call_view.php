
<style>
    .claim-option-box{ clear:both; padding-top:5px }
    .claim-option-box .claim-option-size{ padding-left:15px; }
    .claim-option-box .claim-size-name{ min-width:80px; text-align: right; display: inline-block }
    .claim-option-box .claim-option-size ul li{float:left; padding:5px 0px 5px 10px }
    .claim-option-box .claim-option-name{ margin : 10px 0px 5px 0px }
    .claim-selected-item { font-weight: bold; color: #d51f1f }
    .refund-table-box td { height:15px!important; text-align: left; border-bottom: none!important; }
</style>

<form id="frmOrder" class="frm-order"  action="sales_ps.php" method="post" target="ifrmProcess">
    <div id="return-app">

        <input type="hidden" value="saveProduceEachData2" name="mode">
        <input type="hidden" value="<?=$recapData['sno']?>" name="sno">

        <div class="page-header js-affix" style="margin-bottom:0">
                <h3><?=$recapData['customerName']?> 통화내역</h3>
            <div class="btn-group">
                <div class="btn btn-red btn-sm btn-save" style="padding-top:6px;" >저장+통화내용등록</div>
                <div class="btn btn-white btn-sm" style="padding-top:6px;" onclick="self.close()">닫기</div>
            </div>
        </div>
        <table class="table table-cols" style="border-top:none !important;">
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-r">구매방식</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyMethod" placeholder="구매방식" value="<?=$recapData['buyMethod']?>" />
                </td>
                <th class="ta-r">의류 구분</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyDiv" placeholder="의류 구분" value="<?=$recapData['buyDiv']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">구매 예정일</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyExt" placeholder="구매 예정일" value="<?=$recapData['buyExt']?>" />
                </td>
                <th class="ta-r">구매 품목</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyItem" placeholder="구매 예정일" value="<?=$recapData['buyItem']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">구매 수량</th>
                <td class="ta-l">
                    <input type="number" class="form-control" name="buyCnt" placeholder="구매 수량" value="<?=$recapData['buyCnt']?>" />
                </td>
                <th class="ta-r">비고</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="memo" placeholder="비고" value="<?=$recapData['memo']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">통화내용</th>
                <td class="ta-l" colspan="3">
                    <textarea class="form-control w100" rows="5" name="callContents" placeholder="통화내용 미입력 저장시에는 구매 방식등의 내용만 업데이트 됩니다."></textarea>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="table-title gd-help-manual ">
            통화이력
        </div>
        <table class="table table-rows " >
            <colgroup>
                <col class="width-xs"/>
                <col class="width-xs"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-c">통화일자</th>
                <th class="ta-c">담당자</th>
                <th class="ta-c">통화내용</th>
            </tr>
            <?php foreach($callData as $callValue) { ?>
            <tr>
                <td class="ta-c">
                    <?=$callValue['regDt']?>
                </td>
                <td class="ta-c">
                    <?=$callValue['regManagerName']?>
                </td>
                <td>
                    <?=nl2br($callValue['contents'])?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</form>


<script type="text/javascript">

    var myFnc = function(){
        //self.close();
        parent.opener.location.reload();
        location.reload();
    }

    $(()=>{

        $('.js-datepicker').on('dp.change', function(){
            if($(this).prop('class') == 'input-group js-datepicker mix-picker') {
                $(this).closest('td').find('.mix-picker-data').val($(this).find('.mix-picker-text').val());
            }
        })

        $('.input-dept').click(function(){
            $( '.input-info' ).hide();
            $( '#' + $(this).val()+'-info' ).show('fast');
        });

        $('.btn-save').click(()=>{
            let requiredList = [
            ];

            let isPass = true;

            requiredList.forEach((val)=>{
                if( $.isEmpty($('input[name='+val.field+']').val()) ){
                    isPass = false;
                    $('input[name='+val.field+']').focus();
                    alert(val.name + '은(는) 필수 입니다.');
                    $('input[name='+val.field+']').focus();
                }
            });

            if(isPass){
                $('#frmOrder').submit();
            }
        });


    });

</script>
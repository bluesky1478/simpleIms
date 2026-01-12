
<?php include 'sales_common.php'?>

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

        <input type="hidden" value="saveProduceEachData" name="mode">
        <input type="hidden" value="<?=$recapData['sno']?>" name="sno">

        <div class="page-header js-affix">
            <?php if( empty($recapData['sno']) ) { ?>
                <h3>고객관리 등록</h3>
            <?php }else{ ?>
                <h3>고객관리 수정</h3>
            <?php } ?>
            <div class="btn-group">
            </div>
        </div>

        <div class="table-title gd-help-manual">
            고객 정보
            <div class="pull-right" style="font-weight: normal">
                <?php if( !empty($recapData['sno']) ) { ?>
                    <span class="text-muted font-12">등록 : <?=$recapData['regManagerName']?> (<?=$recapData['regDt']?>) , 마지막 수정 : <?=$recapData['modManagerName']?> (<?=$recapData['modDt']?>)</span>
                <?php } ?>
            </div>

        </div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-r">고객사명</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="customerName" placeholder="고객사명" value="<?=$recapData['customerName']?>" />
                </td>
                <th class="ta-r">출처</th>
                <td class="ta-l">
                    <select class="form-control" name="targetSource">
                        <option value="">선택</option>
                        <option value="김앤김" <?='김앤김' == $recapData['targetSource']?'selected':''?>>김앤김</option>
                        <option value="이노버" <?='이노버' == $recapData['targetSource']?'selected':''?>>이노버</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="ta-r">고객 등급</th>
                <td class="ta-l">
                    <select class="form-control" name="level">
                        <option value="">선택</option>
                        <option value="A" <?='A' == $recapData['level']?'selected':''?> >A</option>
                        <option value="B" <?='B' == $recapData['level']?'selected':''?> >B</option>
                        <option value="C" <?='C' == $recapData['level']?'selected':''?> >C</option>
                        <option value="D" <?='D' == $recapData['level']?'selected':''?> >D</option>
                        <option value="E" <?='E' == $recapData['level']?'selected':''?> >E</option>
                    </select>
                </td>
                <th class="ta-r">최근 통화일자</th>
                <td class="ta-l">
                    <?php if( !empty($recapData['sno']) ) { ?>
                        <div class="btn btn-gray btn-sm btn-call" data-sno="<?=$recapData['sno']?>">통화내용</div>
                        <?=$recapData['contactDt']?>
                        <span class="notice-info">통화내용 등록시 자동 등록</span>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th class="ta-r">고객 구분</th>
                <td class="ta-l">
                    <select class="form-control" name="customerType">
                        <option value="">선택</option>
                        <option value="10" <?='10' == $recapData['customerType']?'selected':''?> >잠재고객</option>
                        <option value="20" <?='20' == $recapData['customerType']?'selected':''?> >관심고객</option>
                        <option value="30" <?='30' == $recapData['customerType']?'selected':''?> >가망고객</option>
                        <option value="40" <?='40' == $recapData['customerType']?'selected':''?> >기타고객</option>
                        <option value="50" <?='50' == $recapData['customerType']?'selected':''?> >발굴완료</option>
                        <option value="80" <?='80' == $recapData['customerType']?'selected':''?> >미팅고객(진행)</option>
                        <option value="90" <?='90' == $recapData['customerType']?'selected':''?> >미팅고객(계약)</option>
                        <option value="99" <?='99' == $recapData['customerType']?'selected':''?> >미팅고객(이탈)</option>
                    </select>
                </td>
                <th class="ta-r">비고</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="memo" placeholder="비고" value="<?=$recapData['memo']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">이메일</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="contactEmail" placeholder="이메일" value="<?=$recapData['contactEmail']?>" />
                </td>
                <th class="ta-r">구매방식</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyMethod" placeholder="구매방식" value="<?=$recapData['buyMethod']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">업종 구분</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="industry" placeholder="업종 구분" value="<?=$recapData['industry']?>" />
                </td>
                <th class="ta-r">의류 구분</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyDiv" placeholder="의류 구분" value="<?=$recapData['buyDiv']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">사원수</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="employeeCnt" placeholder="사원수" value="<?=$recapData['employeeCnt']?>" />
                </td>
                <th class="ta-r">구매 예정일</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyExt" placeholder="구매 예정일" value="<?=$recapData['buyExt']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">대표번호</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="phone" placeholder="사원수" value="<?=$recapData['phone']?>" />
                </td>
                <th class="ta-r">구매 품목</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="buyItem" placeholder="구매 예정일" value="<?=$recapData['buyItem']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">부서</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="dept" placeholder="부서" value="<?=$recapData['dept']?>" />
                </td>
                <th class="ta-r">구매 수량</th>
                <td class="ta-l">
                    <input type="number" class="form-control" name="buyCnt" placeholder="구매 수량" value="<?=$recapData['buyCnt']?>" />
                </td>
            </tr>
            <tr>
                <th class="ta-r">담당자</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="contactName" placeholder="담당자" value="<?=$recapData['contactName']?>" />
                </td>
                <th class="ta-r">직통번호</th>
                <td class="ta-l">
                    <input type="text" class="form-control" name="contactPhone" placeholder="직통번호" value="<?=$recapData['contactPhone']?>" />
                </td>
            </tr>
            </tbody>
        </table>


        <div class="table-title gd-help-manual display-none">
            통화이력
        </div>
        <table class="table table-cols display-none" >
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-r"></th>
                <td class="ta-l">
                    -
                </td>
                <th class="ta-r"></th>
                <td class="ta-c">
                    -
                </td>
            </tr>
            </tbody>
        </table>


        <div class="ta-c" style="margin:50px">
            <?php if( empty($recapData['sno']) ) { ?>
                <input type="button" value="등록" class="btn btn-red btn-save" style="height:38px;font-size:20px; padding:0px 20px 0px 20px" @click="save">
            <?php }else{ ?>
                <input type="button" value="수정" class="btn btn-red btn-save" style="height:38px;font-size:20px; padding:0px 20px 0px 20px" @click="modify">
            <?php }?>
            <input type="button" value="닫기" class="btn btn-white" style="height:38px;font-size:20px; padding:0px 20px 0px 20px" onclick="self.close()">
        </div>
    </div>
</form>


<script type="text/javascript">

    var myFnc = function(){
        self.close();
        parent.opener.location.reload();
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
                {field : 'customerName',name : '고객사명'},
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

        $('.btn-complete').click(function(){

            if (confirm('완료 처리 하시겠습니까?')) {
                let sno = $(this).data('sno');
                $.post('../recap_ps.php', {
                    mode : 'setCompleteProduce',
                    sno : sno
                }, function (data) {
                    opener.location.reload();
                    self.close();
                });
            }

        });

    });

</script>
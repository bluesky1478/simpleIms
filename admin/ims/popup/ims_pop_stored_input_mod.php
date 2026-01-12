<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">입고 수정 <span class="font-13 text-danger">* : 필수입력</span></h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col style="width:16%;">
                    <col style="width:33%;">
                    <col style="width:18%;">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th >원부자재 <span class="font-13 text-danger">*</span></th>
                    <td colspan="3">
                        <div>
                            <select2 class="js-example-basic-single" v-model="StoredFabric.sno" @change="setFabric()" style="width:80%;">
                                <option value="0">신규등록</option>
                                <?php foreach ($fabricList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </div>
                        <table class="table table-cols table-pd-3 table-td-height35 table-th-height35 mgt5" >
                            <colgroup>
                                <col class="width-sm">
                                <col>
                            </colgroup>
                            <tbody>
                            <tr>
                                <th class="_require">비축 자재명</th>
                                <td>
                                    <?php $model='StoredFabric.fabricName'; $placeholder='비축 자재명' ?>
                                    <?php include './admin/ims/template/basic_view/_text_with_disabled.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th class="_require">혼용율</th>
                                <td>
                                    <?php $model='StoredFabric.fabricMix'; $placeholder='혼용율' ?>
                                    <?php include './admin/ims/template/basic_view/_text_with_disabled.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th class="_require">색상</th>
                                <td>
                                    <?php $model='StoredFabric.color'; $placeholder='색상' ?>
                                    <?php include './admin/ims/template/basic_view/_text_with_disabled.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th class="_require">사용처</th>
                                <td>
                                    <select2 class="js-example-basic-single" v-model="StoredFabric.customerUsageSno" style="width:100%;" :disabled="isDisabled">
                                        <option value="0">필수선택</option>
                                        <?php foreach ($customerListMap as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>단가</th>
                    <td>
                        <?php $model='StoredFabricInput.unitPrice'; $placeholder='단가' ?>
                        <?php include './admin/ims/template/basic_view/_text_number_format.php'?>
                    </td>
                    <th>단위 <span class="font-13 text-danger">*</span></th>
                    <td>
                        <select2 v-model="StoredFabricInput.inputUnit" style="width:100%;">
                            <?php foreach( \Component\Ims\NkCodeMap::STORED_INPUT_UNIT as $k => $v){ ?>
                                <option value="<?=$k?>"><?=$v?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr>
                    <th>입고수량 <span class="font-13 text-danger">*</span></th>
                    <td>
                        <?php $model='StoredFabricInput.inputQty'; $placeholder='입고수량' ?>
                        <?php include './admin/ims/template/basic_view/_text_number_format.php'?>
                    </td>
                    <th>입고일</th>
                    <td>
                        <?php $model='StoredFabricInput.inputDt'; $placeholder='입고일' ?>
                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                    </td>
                </tr>
                <tr>
                    <th>입고사유</th>
                    <td colspan="3">
                        <?php $model='StoredFabricInput.inputReason'; $placeholder='입고사유' ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                </tr>
                <tr>
                    <th>만료일자</th>
                    <td>
                        <?php $model='StoredFabricInput.expireDt'; $placeholder='만료일자' ?>
                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                    </td>
                    <th>소유권구분 <span class="font-13 text-danger">*</span></th>
                    <td>
                        <select2 v-model="StoredFabricInput.inputOwn" @change="setOwn()" style="width:100%;">
                            <?php foreach( \Component\Ims\NkCodeMap::STORED_INPUT_OWN as $k => $v){ ?>
                                <option value="<?=$k?>"><?=$v?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr>
                    <th>저장위치</th>
                    <td colspan="3">
                        <?php $model='StoredFabricInput.inputLocation'; $placeholder='저장위치' ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                    <?php /*
                    <th>소유고객</th>
                    <td>
                        <select2 class="js-example-basic-single" v-model="StoredFabricInput.customerSno" @change="setCustomer()" style="width:100%;">
                            <option value="0">없음</option>
                            <?php foreach ($customerListMap as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                    </td>
                    */ ?>
                </tr>
                <tr>
                    <th>
                        비고
                    </th>
                    <td colspan="3">
                        <?php $model='StoredFabricInput.inputMemo'; $placeholder='비고' ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">수정</div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(appId).hide();

    $(()=>{
        const serviceData = {};
        ImsBoneService.setMounted(serviceData, ()=>{
        });
        ImsBoneService.setData(serviceData,{
            isModify : true,
            isDisabled : true,
            <?php foreach ($schemaTable as $key => $val ) { /* Table Schema대로 obj 구성 */ ?>
            <?=$key?> : {
                <?php foreach ($val as $key2 => $val2 ) { ?>
                '<?=$val2['val']?>' : '<?=$val2['def']===null?'':$val2['def']?>',
                <?php } ?>
            },
            <?php } ?>
            existFabricList : { //현재 존재하는 원단리스트 obj에 담기
                <?php foreach ($existFabricList as $key => $val ) { ?>
                '<?=$val['sno']?>' : {
                    'fabricName' : '<?=addslashes($val['fabricName'])?>',
                    'fabricMix' : '<?=addslashes($val['fabricMix'])?>',
                    'color' : '<?=addslashes($val['color'])?>',
                    'customerUsageSno' : '<?=$val['customerUsageSno']?>',
                },
                <?php } ?>
            },
        });

        ImsBoneService.setMethod(serviceData,{
            setFabric : ()=>{
                if (vueApp.StoredFabric.sno == '0') {
                    vueApp.StoredFabric.fabricName = '';
                    vueApp.StoredFabric.fabricMix = '';
                    vueApp.StoredFabric.color = '';
                    vueApp.StoredFabric.customerUsageSno = '0';
                    vueApp.isDisabled = false;
                } else {
                    console.log(vueApp.existFabricList);
                    // alert(vueApp.existFabricList[vueApp.StoredFabric.sno].fabricName);
                    vueApp.StoredFabric.fabricName = vueApp.existFabricList[vueApp.StoredFabric.sno].fabricName;
                    vueApp.StoredFabric.fabricMix = vueApp.existFabricList[vueApp.StoredFabric.sno].fabricMix;
                    vueApp.StoredFabric.color = vueApp.existFabricList[vueApp.StoredFabric.sno].color;
                    vueApp.StoredFabric.customerUsageSno = vueApp.existFabricList[vueApp.StoredFabric.sno].customerUsageSno;
                    vueApp.isDisabled = true;
                }
            },
            setOwn : ()=>{
                // if (vueApp.StoredFabricInput.inputOwn != 3) vueApp.StoredFabricInput.customerSno = '0';
            },
            setCustomer : ()=>{
                // if (vueApp.StoredFabricInput.customerSno != '0') vueApp.StoredFabricInput.inputOwn = 3;
            },
            save : ()=>{
                if (vueApp.StoredFabric.sno === null || vueApp.StoredFabric.sno === '') vueApp.StoredFabric.sno = '0';
                if (vueApp.StoredFabric.customerUsageSno === null || vueApp.StoredFabric.customerUsageSno === '') vueApp.StoredFabric.customerUsageSno = '0';
                if (vueApp.StoredFabric.sno == '0') {
                    if (vueApp.StoredFabric.fabricName == '' || vueApp.StoredFabric.fabricMix == '' || vueApp.StoredFabric.color == '' || vueApp.StoredFabric.customerUsageSno == '0') {
                        $.msg('원부자재(원단) 신규등록인 경우 자재명, 혼용율, 색상, 사용처 고객을 입력/선택하셔야 합니다','','error');
                        return false;
                    }
                    let bFlag = false;
                    $.each(vueApp.existFabricList, function(key, val) {
                        if (val.fabricName == vueApp.StoredFabric.fabricName && val.fabricMix == vueApp.StoredFabric.fabricMix && val.color == vueApp.StoredFabric.color && val.customerUsageSno == vueApp.StoredFabric.customerUsageSno) {
                            bFlag = true;
                            return false;
                        }
                    });
                    if (bFlag === true) {
                        $.msg('입력하신 정보의 원단은 이미 존재하는 원단입니다. 신규등록이 아니라 원단 선택 후 입고 진행 바랍니다','','error');
                        return false;
                    }
                }
                if (vueApp.StoredFabricInput.inputQty === null || vueApp.StoredFabricInput.inputQty === '') {
                    $.msg('입고수량을 입력하세요','','error');
                    return false;
                }
                // if (vueApp.StoredFabricInput.customerSno === null || vueApp.StoredFabricInput.customerSno === '') vueApp.StoredFabricInput.customerSno = '0';
                // if (vueApp.StoredFabricInput.inputOwn == 3 && vueApp.StoredFabricInput.customerSno == '0') {
                //     $.msg('소유권을 고객으로 선택하셨으면 고객을 선택하셔야 합니다','','error');
                //     return false;
                // }

                let oSendData = {
                    'StoredFabric' : vueApp.StoredFabric,
                    'StoredFabricInput' : vueApp.StoredFabricInput,
                };
                $.imsPost('modifyStoredFabricInput', oSendData).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('입고정보 수정 완료','','success').then(()=>{
                            parent.opener.location.reload(); //부모창 갱신.
                            self.close();
                        });
                    });
                });
            }
        });
        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
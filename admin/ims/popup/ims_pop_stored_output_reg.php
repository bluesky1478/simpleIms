<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">출고 등록</h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>
    <div class="">
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col style="width:20%;">
                    <col style="width:40%;">
                    <col style="width:20%;">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>비축 자재명</th>
                    <td><?=$chooseInput['fabricName']?></td>
                    <th>현재수량</th>
                    <td>{% $.setNumberFormat(limitQty) %}</td>
                </tr>
                <tr>
                    <th>혼용율</th>
                    <td><?=$chooseInput['fabricMix']?></td>
                    <th>출고수량</th>
                    <td>
                        <?php $model='sendData.outQty'; $placeholder='출고수량' ?>
                        <?php include './admin/ims/template/basic_view/_text_number_format.php'?>
                    </td>
                </tr>
                <tr>
                    <th>색상</th>
                    <td><?=$chooseInput['color']?></td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th>소유권</th>
                    <td><?=$chooseInput['inputOwn']?></td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th>출고사유</th>
                    <td colspan="3">
                        <?php $model='sendData.outReason'; $placeholder='출고사유' ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">등록</div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(appId).hide();
    $(()=>{
        const serviceData = {
            serviceWatch : {
                'sendData.outQty'(val, pre) { //출고수량 변경시 현재수량 자동변경
                    vueApp.limitQty = Number(vueApp.limitQty) + Number(String(pre).replaceAll(',','')) - Number(String(val).replaceAll(',',''));
                },
            }
        };
        ImsBoneService.setData(serviceData,{
            isModify : true,
            limitQty : <?=$chooseInput['remainQty']?>,
            sendData : {
                'sno' : <?=$chooseInput['sno']?>,
                'outQty' : '',
                'outReason' :'',
            }
        });

        ImsBoneService.setMethod(serviceData,{
            save : ()=>{
                if (vueApp.sendData.outQty == null || vueApp.sendData.outQty == '' || vueApp.sendData.outQty == 0) {
                    $.msg('출고수량을 입력하세요','','error');
                    return false;
                }
                if (Number(vueApp.limitQty) < 0) {
                    $.msg('입력한 출고수량이 현재수량보다 많습니다','','error');
                    return false;
                }

                $.imsPost('saveStoredFabricOutput', vueApp.sendData).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('출고 등록 완료','','success').then(()=>{
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
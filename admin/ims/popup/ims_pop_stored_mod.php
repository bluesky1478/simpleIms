<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">자재 수정</h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>
    <div class="">
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
                    <?php include './admin/ims/template/basic_view/_text.php'?>
                </td>
            </tr>
            <tr>
                <th class="_require">혼용율</th>
                <td>
                    <?php $model='StoredFabric.fabricMix'; $placeholder='혼용율' ?>
                    <?php include './admin/ims/template/basic_view/_text.php'?>
                </td>
            </tr>
            <tr>
                <th class="_require">색상</th>
                <td>
                    <?php $model='StoredFabric.color'; $placeholder='색상' ?>
                    <?php include './admin/ims/template/basic_view/_text.php'?>
                </td>
            </tr>
            <tr>
                <th class="_require">사용처</th>
                <td>
                    <select2 class="js-example-basic-single" v-model="StoredFabric.customerUsageSno" style="width:100%;">
                        <option value="0">필수선택</option>
                        <?php foreach ($customerListMap as $key => $value ) { ?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                    </select2>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="dp-flex" style="justify-content: center">
        <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">수정</div>
        <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
    </div>
</section>

<script type="text/javascript">
    $(appId).hide();
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : true,
            StoredFabric: {
                <?php foreach ($StoredInfo as $key => $val) { ?>
                '<?=$key?>' : '<?=$val?>',
                <?php } ?>
            }
        });
        ImsBoneService.setMethod(serviceData,{
            save : ()=>{
                if (vueApp.StoredFabric.customerUsageSno === null || vueApp.StoredFabric.customerUsageSno === '') vueApp.StoredFabric.customerUsageSno = '0';
                if (vueApp.StoredFabric.fabricName == '') {
                    $.msg('자재명을 입력하셔야 합니다','','error');
                    return false;
                }
                if (vueApp.StoredFabric.fabricMix == '') {
                    $.msg('혼용율을 입력하셔야 합니다','','error');
                    return false;
                }
                if (vueApp.StoredFabric.color == '') {
                    $.msg('색상을 입력하셔야 합니다','','error');
                    return false;
                }
                if (vueApp.StoredFabric.customerUsageSno == '0') {
                    $.msg('사용처 고객을 선택하셔야 합니다','','error');
                    return false;
                }

                $.imsPost('modifyStoredFabric', {'StoredFabric' : vueApp.StoredFabric}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        if (data == 500) {
                            $.msg('이미 존재하는 자재입니다.','','error');
                            return false;
                        }
                        $.msg('자재정보 수정 완료','','success').then(()=>{
                            parent.opener.location.reload(); //부모창 갱신.
                            self.close();
                        });
                    });
                });
            },
        });
        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>
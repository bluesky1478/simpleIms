<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .table td { padding:3px 6px!important; }
    .bootstrap-filestyle input{display: none }
</style>
<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">분류패킹 수량확인 {% calc_remain_qty %} <span ref="textPackingSt"></span></h3>
        <div class="btn-group font-18 bold">
            <div v-show="oInfo.packingSt == 3" @click="confirmImsPacking();" class="btn btn-blue" style="line-height: 35px;">입고확인</div>
            <div v-show="oInfo.packingSt == 4" @click="cancelConfirmImsPacking();" class="btn btn-red" style="line-height: 35px;">입고확인취소</div>
            <div v-show="isModify" @click="save();" class="btn btn-red" style="line-height: 35px;">저장</div>
            <div @click="self.close();" class="btn btn-gray" style="line-height: 35px;">닫기</div>
        </div>
    </div>
    <div class="mgt10 pdl5">
        <table class="table table-rows table-default-center table-td-height30">
            <colgroup>
                <col class="" />
                <col class="w-100px" />
                <col class="w-150px" />
                <col class="w-150px" />
                <col class="w-150px" />
                <col class="w-150px" />
            </colgroup>
            <tr>
                <th>스타일명</th><th>사이즈명</th><th>제작</th><th>분류패킹</th><th>창고</th><th>남은수량</th>
            </tr>
            <tbody>
            <tr v-if="Object.keys(ooStyleList).length === 0">
               <td colspan="99">데이터가 없습니다.</td>
            </tr>
            <template v-else v-for="(val, key) in ooStyleList">
                <template v-for="(val2, key2) in val">
                    <tr >
                        <th v-if="oFirstSizeNmByStyle[key] == key2" :rowspan="Object.keys(val).length">{% oStyleNameBySno[key] %}</th>
                        <th>{% key2 %}</th>
                        <td>{% $.setNumberFormat(val2.makeQty) %}</td>
                        <td>{% $.setNumberFormat(val2.currQty) %}</td>
                        <td>
                            <span v-if="isModify"><input type="text" v-model="val2.storageQty" class="form-control"/></span>
                            <span v-else>{% $.setNumberFormat(val2.storageQty) %}</span>
                        </td>
                        <td :class="Number(val2.remainQty) != 0 ? 'bold text-danger' : ''">{% $.setNumberFormat(val2.remainQty) %}</td>
                    </tr>
                </template>
            </template>
            </tbody>
        </table>
    </div>
    <div class="dp-flex" style="justify-content: center; border-top:1px #888 solid; margin-top:20px; padding-top:5px;">
        <div v-show="oInfo.packingSt == 3" @click="confirmImsPacking();" class="btn btn-blue" style="line-height: 35px;">입고확인</div>
        <div v-show="oInfo.packingSt == 4" @click="cancelConfirmImsPacking();" class="btn btn-red" style="line-height: 35px;">입고확인취소</div>
        <div v-show="isModify" @click="save();" class="btn btn-red" style="line-height: 35px;">저장</div>
        <div @click="self.close();" class="btn btn-gray" style="line-height: 35px;">닫기</div>
    </div>
</section>

<script type="text/javascript">
    igstaticSno = <?=(int)$requestParam['sno']?>;
    igstaticStyleSno = <?=(int)$requestParam['styleSno']?>;
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            oInfo : {},
            ooStyleList : {},
            oStyleNameBySno : {}, //개발용. 스타일명을 구할때 쓰임
            oFirstSizeNmByStyle : {}, //개발용. rowspan을 낼때 쓰임
        });
        ImsBoneService.setMethod(serviceData,{
            save : ()=>{
                if (Object.keys(vueApp.ooStyleList).length === 0) {
                    $.msg('존재하지 않는 납품건입니다.','','warning');
                    return false;
                }

                $.imsPost('modifySimpleDbCol', {'table_number':4, 'colNm':'jsonCntSizeTotalims', 'where':{'sno':igstaticSno}, 'data':[vueApp.ooStyleList]});
            },
            //확정
            confirmImsPacking : ()=>{
                if (Object.keys(vueApp.ooStyleList).length === 0) {
                    $.msg('존재하지 않는 납품건입니다.','','warning');
                    return false;
                }
                $.msgConfirm('입고확인 하시겠습니까?','').then(function(result) {
                    if( result.isConfirmed ){
                        $.imsPost('modifySimpleDbCol', {'table_number':4, 'colNm':'jsonCntSizeTotalims', 'where':{'sno':igstaticSno}, 'data':[vueApp.ooStyleList]}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                $.imsPost('modifySimpleDbCol', {'table_number':4, 'colNm':'packingSt', 'where':{'sno':igstaticSno}, 'data':4}).then((data)=>{
                                    vueApp.$refs.textPackingSt.innerHTML = ' - 입고확인';
                                    vueApp.isModify = false;
                                    vueApp.oInfo.packingSt = 4;
                                    window.opener.location.reload();
                                });
                            });
                        });
                    }
                });
            },
            //입고확인 취소
            cancelConfirmImsPacking : ()=>{
                $.msgConfirm('입고확인 취소하시겠습니까?','').then(function(result){
                    if( result.isConfirmed ){
                        $.imsPost('modifySimpleDbCol', {'table_number':4, 'colNm':'packingSt', 'where':{'sno':igstaticSno}, 'data':3}).then((data)=>{
                            vueApp.$refs.textPackingSt.innerHTML = '';
                            vueApp.isModify = true;
                            vueApp.oInfo.packingSt = 3;
                            window.opener.location.reload();
                        });
                    }
                });
            }
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            ImsNkService.getList('customerPacking', {'packingSno':igstaticSno, 'styleSno':igstaticStyleSno}).then((data)=> {
                $.imsPostAfter(data, (data) => {
                    if (data.length === 0) {
                        $.msg('존재하지 않는 납품건입니다.','','warning').then((data)=>{
                            self.close();
                        });
                    }

                    vueApp.oInfo = data[0];
                    if (vueApp.oInfo.packingSt > 3) {
                        vueApp.$refs.textPackingSt.innerHTML = ' - 입고확인';
                        vueApp.isModify = false;
                    } else {
                        vueApp.isModify = true;
                    }

                    vueApp.ooStyleList = vueApp.oInfo.jsonCntSizeTotalims[0];
                    //rowspan값을 줘야하는 사이즈명 가져오기
                    let iTmpKey = 0;
                    $.each(vueApp.ooStyleList, function (key, val) {
                        iTmpKey = 0;
                        $.each(val, function (key2, val2) {
                            if (iTmpKey === 0) vueApp.oFirstSizeNmByStyle[key] = key2;
                            iTmpKey++;
                        });
                    });

                    //스타일sno로 스타일명 가져오기
                    let aStyleAllSnos = [];
                    $.each(vueApp.ooStyleList, function (key, val) {
                        aStyleAllSnos.push(key);
                    });
                    let aStyleNames = String(vueApp.oInfo.styleNames).split(', ');
                    $.each(aStyleAllSnos, function (key, val) {
                        vueApp.oStyleNameBySno[val] = aStyleNames[key];
                    });
                });
            });
        });

        ImsBoneService.setComputed(serviceData,{
            calc_remain_qty() {
                let oTarget = this.ooStyleList;
                $.each(oTarget, function (key, val) {
                    $.each(val, function (key2, val2) {
                        oTarget[key][key2].remainQty = Number(val2.makeQty) - Number(val2.currQty) - Number(val2.storageQty);
                    });
                });
            },
        });

        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>

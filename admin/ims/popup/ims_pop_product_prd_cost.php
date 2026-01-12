<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">기성복 생산가 관리</h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div class="mgt20">
        <div class="dp-flex" style="justify-content: space-between">
            <div class="font-18 bold">매입가(생산가) <span class="sl-blue">{% $.setNumberFormat(computed_sum_total) %}</span>원 (VAT미포함)</div>
            <div class="dp-flex" style="justify-content: right">
                <?php if ($bFlagUpsert === true) { ?>
                <div class="btn btn-blue mg5" @click="appendRow()">품목등록</div>
                <div class="btn btn-red  btn-red-line2" v-show="!isModify" @click="isModify=true">등록/수정</div>
                <div class="btn btn-white mg5" v-show="isModify" @click="isModify=false">취소</div>
                <div class="btn btn-red" v-show="isModify" @click="save()">저장</div>
                <?php } ?>
                <div class="btn btn-white mg5" @click="self.close()">닫기</div>
            </div>
        </div>
    </div>

    <div>
        <div class="notice-info">
            VAT 포함가로 입력시 자동으로 미포함가가 계산되어 입력됩니다.
        </div>
        <table class="table table-rows table-default-center table-td-height0 table-th-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
            <colgroup>
                <col class="w-4p">
                <col class="w-5p">
                <col class="w-20p"><!--품목명-->
                <col v-show="isModify" class="w-23p"><!--비용-->
                <col class="w-12p"><!--비용(vat별도)-->
                <col class="w-12p"><!--매입처-->
                <col class=""><!--비고-->
                <col v-show="isModify" class="w-7p"><!--비고-->
            </colgroup>
            <tr>
                <th>이동</th>
                <th>번호</th>
                <th>품목명</th>
                <th v-show="isModify">비용입력</th>
                <th >
                    <span class="">비용(VAT별도)</span>
                </th>
                <th>매입처</th>
                <th>비고</th>
                <th v-show="isModify">삭제</th>
            </tr>
            <tr  v-if="0 >= listData.length">
                <td colspan="999">
                    데이터가 없습니다.
                </td>
            </tr>
            <tbody  is="draggable" :list="listData"  :animation="200" tag="tbody" handle=".handle">
            <tr v-for="(val , key) in listData">
                <td :class="isModify===true ? 'handle' : '' ">
                    <div class="cursor-pointer hover-btn" v-show="isModify===true">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </div>
                </td>
                <td class="ta-r">{% key+1 %}</td>
                <td class="ta-l pdl5">
                    <input v-if="isModify===true" type="text" class="form-control" v-model="val.prdCostName" placeholder="품목명">
                    <span v-else>{% val.prdCostName %}</span>
                </td>
                <td v-if="isModify===true" class="ta-l">
                    <span class="font-11">
                        <div class="dp-flex">
                            <label class="radio-inline"><input type="radio" :name="'vatYn_'+key" value="y" v-model="calcRadio[key]" @change="calc_not_vat_amt(key);" />VAT포함입력</label>
                            <label class="radio-inline"><input type="radio" :name="'vatYn_'+key" value="n" v-model="calcRadio[key]" @change="calc_not_vat_amt(key);" />VAT미포함입력</label>
                        </div>
                        <input type="number" class="form-control" @keyup="calc_not_vat_amt(key);" v-model="calcText[key]" placeholder="비용입력">
                    </span>
                </td>
                <td class="ta-r">
                    <div class="sl-blue font-bold font-13">{% $.setNumberFormat(val.prdCostAmount) %}원</div>
                    <div class="font-10 text-muted">자동계산</div>
                </td>

                <td class="ta-l pdl5">
                    <input v-if="isModify===true" type="text" class="form-control" v-model="val.prdCostBuyer" placeholder="매입처">
                    <span v-else>{% val.prdCostBuyer %}</span>
                </td>
                <td class="ta-l pdl5">
                    <input v-if="isModify===true" type="text" class="form-control" v-model="val.prdCostMemo" placeholder="비고">
                    <span v-else>{% val.prdCostMemo %}</span>
                </td>
                <td v-show="isModify">
                    <div class="btn btn-sm btn-white mg5" @click="listData.splice(key,1);">삭제</div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>


    <div class="ta-c">
        <div class="btn btn-white mg5" v-show="isModify" @click="isModify=false">취소</div>
        <div class="btn btn-red" v-show="isModify" @click="save()">저장</div>
        <div class="btn btn-white mg5" @click="self.close()">닫기</div>
    </div>

</section>

<script type="text/javascript">
    const mainListPrefix = '';
    const listSearchDefaultData = {
        page : 1,
        pageNum : 999,
        sort : 'D,asc'
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.styleSno = '<?=$iSno?>';
        params.mode = 'getListProductPrdCost';
        let oPost = ImsNkService.getList('productPrdCost', params);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                $.each(data.list, function(key, val) {
                    vueApp.calcRadio[key] = 'y';
                });
            });
        });
        return oPost;
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            iTotal : 0,
            calcRadio : [],
            calcText : [],
        });

        ImsBoneService.setMethod(serviceData,{
            appendRow : ()=>{
                vueApp.isModify = true;
                let oFrame = {};
                $.each(vueApp.searchData.fieldData, function(key, val) {
                    if (val.name === 'styleSno') oFrame[val.name] = '<?=$iSno?>';
                    else oFrame[val.name] = '';
                });
                vueApp.listData.push(oFrame);
                vueApp.calcRadio[vueApp.listData.length-1] = 'y';
            },
            calc_not_vat_amt : (iKey)=>{
                let iAmt = Number(vueApp.calcText[iKey]);
                if (vueApp.calcRadio[iKey] == 'y') {
                    vueApp.listData[iKey].prdCostAmount = iAmt - Math.floor(iAmt*10/110);
                } else {
                    vueApp.listData[iKey].prdCostAmount = iAmt;
                }
            },
            save : ()=>{
                //순서이동기능 있을때 메소드 호출전 sortNum 정리
                $.each(vueApp.listData, function (key, val) {
                    val.sortNum = key + 1;
                });
                $.imsPost('setProductPrdCost', {'data':vueApp.listData}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        parent.opener.updatePrdCost(<?=$iSno?>, vueApp.iTotal);
                        $.msg('생산가 변경 및 상세 내용이 등록되었습니다.','','success').then(()=>{
                            //부모창의 함수 실행(해당 styleSno의 생산가 바꾸기)
                            self.close();
                        });
                    });
                });
            }
        });

        ImsBoneService.setComputed(serviceData,{
            computed_sum_total() {
                this.iTotal = 0;
                if( !$.isEmpty(this.listData) ){
                    let iSumAmt = 0;
                    $.each(this.listData, function(key, val) {
                        val.prdCostAmount = Number(val.prdCostAmount);
                        iSumAmt += val.prdCostAmount;
                    });
                    this.iTotal += iSumAmt;
                }
                return this.iTotal;
            },

        });


        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>
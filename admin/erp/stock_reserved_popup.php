<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">

    <form id="frm">
        <div class="page-header js-affix">
            <h3><?=$goodsNm?> 출고 예약 리스트
                <?php if(!empty($optionName)) { ?>( <?=$optionName?> )<?php } ?>
            </h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <div class="mgt20 ">
        <div >
            <div class="font-18 bold noto mgt20">
                총 <span class="bold text-danger">{% mainData.length %}</span>건 <span class="font-13 font-normal normal font-black">(출고수량:{% goodsCntTotal %}ea)</span>
                <div class="dp-flex dp-flex-gap10"></div>
            </div>
            
            <div class="mgt5">
                <table class="table table-rows table-default-center table-th-height0 table-td-height0 table-pd-2 mgt10">
                    <colgroup>
                        <col class="w-2p"/><!--CHK-->
                        <col class="w-3p"/><!--번호-->
                        <col class="w-7p"/><!--주문번호-->
                        <col class="w-7p"/><!--아이디-->
                        <col class="w-7p"/><!--주문자-->
                        <col class="w-7p"/><!--수령자-->
                        <col class="w-20p"/><!--상품명-->
                        <col class="w-7p"/><!--옵션-->
                        <col class="w-5p"/><!--수량-->
                        <col class="w-7p"/><!--주문일-->
                        <col class="w-7p"/><!--결제일-->
                        <col class="w-10p"/><!--상태-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>CK</th>
                        <th>번호</th>
                        <th>주문번호</th>
                        <th>아이디</th>
                        <th>주문자</th>
                        <th>수령자</th>
                        <th>상품명</th>
                        <th>옵션</th>
                        <th>수량</th>
                        <th>주문일</th>
                        <th>결제일</th>
                        <th>상태</th>
                    </tr>
                    </thead>
                    <tbody class="hover-light" v-if="0 >= mainData.length">
                    <tr>
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-for="(each, idx) in mainData" class="hover-light"
                       <?php if(!empty($requestParam['optionCode'])){?>
                           v-if="'o'+each.optionSno === '<?=$requestParam['optionCode']?>'"
                       <?php } ?>
                    >
                    <tr>
                        <td>
                            <input type="checkbox" name="projectType[]" :value="idx">
                        </td>
                        <td class="ta-c">{% mainData.length - idx %}</td>
                        <td class="ta-c">
                            <span class="hover-btn cursor-pointer sl-blue" @click="order_view_popup(each.orderNo, false)">
                                {% each.orderNo %}
                            </span>
                        </td>
                        <td class="">{% each.memId %}</td>
                        <td class="">{% each.memNm %}</td>
                        <td class="">{% each.receiverName %}</td>
                        <td class="pdl5 ta-l">{% each.goodsNm %}</td>
                        <td class="">
                            {% each.optionValue1 %}
                            {% each.optionValue2 %}
                            {% each.optionValue3 %}
                            {% each.optionValue4 %}
                            {% each.optionValue5 %}
                        </td>
                        <td class="">
                            {% each.goodsCnt %}
                        </td>
                        <td>{% $.formatShortDateWithoutWeek(each.regDt) %}</td>
                        <td>{% $.formatShortDateWithoutWeek(each.paymentDt) %}</td>
                        <td>{% each.orderStatusKr %}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</section>

<script type="text/javascript">
    const goodsNo='<?=$requestParam['goodsNo']?>';
    const scmSno='<?=$scmSno?>';
    //초기
    const viewMethods = {
    };

    $(appId).hide();

    $(()=>{
        $(()=>{
            const serviceData = {};
            //화면 사용 데이터 설정
            ImsBoneService.setData(serviceData,{
                reservedList : [], //예약 리스트
            });
            ImsBoneService.setMethod(serviceData, viewMethods);
            ImsBoneService.setMounted(serviceData, ()=>{});

            ImsBoneService.setComputed(serviceData,{
                goodsCntTotal() {
                    let goodsCntTotal = 0;
                    for(let prdIdx in this.mainData){
                        <?php if(!empty($requestParam['optionCode'])){?>
                        if('o'+this.mainData[prdIdx].optionSno === '<?=$requestParam['optionCode']?>'){
                            goodsCntTotal += Number(this.mainData[prdIdx]['goodsCnt']);
                        }
                        <?php }else{ ?>
                        goodsCntTotal += Number(this.mainData[prdIdx]['goodsCnt']);
                        <?php } ?>
                    }
                    return goodsCntTotal;
                },
            });


            //초기화
            ImsBoneService.serviceStart('getReservedList',{goodsNo:goodsNo}, serviceData);
        });
    });

</script>

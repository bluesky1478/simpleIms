<!--발주-->
<td class="" style="border-top:none !important;" v-if="$.isEmpty(project['txProductionOrder'])">

    <div class="h30 dp-flex dp-flex-center">

        <div class="" v-show="project.stProductionOrder == 10">
            <div class="font-12">
                <a :href="'../ims/imsProductionList.php?initStatus=0&key=prj.sno&keyword='+project.sno" target="_blank">
                생산{% project.productionStatusKr %}
                </a>
            </div>
        </div>

        <!--발주조건 (모달로 현재 조건 보여주기) -->
        <div class="btn btn-sm btn-gray" @click="visibleOrderCondition=true"
             v-show="10 != project.stProductionOrder && !(
                        2 == project.priceStatus
                        && 2 == project.costStatus
                        && 'p' == project.assortApproval
                        && 2 == project.workStatus
                        && 'p' == project.customerOrderConfirm
                     )">
            발주하기(불가)
        </div>

        <ims-modal :visible.sync="visibleOrderCondition" title="발주조건">
            <!--<div class="dp-flex justify-content-start">-->
            <div>
                <table class="w-100p">
                    <colgroup>
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                        <col class="w-14p">
                    </colgroup>
                    <tr>
                        <th>판매가</th>
                        <th>생산가</th>
                        <th>아소트</th>
                        <th>작지</th>
                        <th>사양서</th>
                        <th>퀄리티</th>
                    </tr>
                    <tr>
                        <td>
                            <span v-html="project.priceStatusIcon"></span>
                        </td>
                        <td>
                            <span v-html="project.costStatusIcon"></span>
                        </td>
                        <td>
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == project.assortApproval"></i>
                            <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == project.assortApproval"></i>
                            <span class="text-muted" v-else>-</span>
                        </td>
                        <td>
                            <span v-html="project.workStatusIcon"></span>
                        </td>
                        <td>
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == project.customerOrderConfirm"></i>
                            <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == project.customerOrderConfirm"></i>
                            <span class="text-muted" v-else>-</span>
                        </td>
                        <td>
                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'2' == project.fabricStatus"></i>
                            <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'1' == project.fabricStatus"></i>
                            <span class="text-muted" v-else>-</span>
                        </td>
                    </tr>
                </table>
            </div>
            <!--</div>-->
            <template #footer>
                <div class="btn btn-white mgt5" @click="visibleOrderCondition=false">닫기</div>
            </template>
        </ims-modal>

        <div class="btn btn-sm btn-blue" @click="orderToFactory()"
             v-show="0 >= project.stProductionOrder"
             v-if="
                        2 == project.priceStatus
                        && 2 == project.costStatus
                        && 'p' == project.assortApproval
                        && 2 == project.workStatus
                        && 'p' == project.customerOrderConfirm
                     ">
            발주하기
        </div>
    </div>

</td>
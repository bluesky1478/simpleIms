<table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
    <colgroup>
        <col class="w-8p" />
        <col class="w-1p" />
        <col class="w-2p" />
        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
    </colgroup>
    <tr>
        <th>프로젝트</th>
        <th ><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
        <th >번호</th>
        <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip && 'subTitle' != fieldData.name" :class="fieldData.titleClass">
            {% fieldData.title %}
        </th>
    </tr>
    <tr  v-if="0 >= listData.length">
        <td colspan="99">
            데이터가 없습니다.
        </td>
    </tr>
    <tr  v-for="(each , index) in listData">
        <!--프로젝트-->
        <td class="ta-l pdl5 relative" :rowspan="each.projectRowspan" v-if="each.projectRowspan > 0">
            <div class="dp-flex dp-flex-gap5">
                <?php if($imsProduceCompany) { ?>
                    <div >
                        <div class="dp-flex">
                            <div v-if="'n' === each.isReorder" class="round-box-mini bg-green color-white">
                                {% each.projectTypeKr %}
                            </div>
                            <div v-else class="round-box-mini bg-orange color-white" >
                                {% each.projectTypeKr %}
                            </div>
                            <div class="text-danger">{% each.sno %}</div>
                        </div>
                        <div class="sl-blue mgt3">{% each.customerName %}</div>
                    </div>
                <?php }else{ ?>
                    <div class="hover-btn cursor-pointer" >
                        <div class="dp-flex" @click="window.open(`ims_view2.php?sno=${each.sno}&currentStatus=${each.projectStatus}`)">
                            <div v-if="'n' === each.isReorder" class="round-box-mini bg-green color-white">
                                {% each.projectTypeKr %}
                            </div>
                            <div v-else class="round-box-mini bg-orange color-white" >
                                {% each.projectTypeKr %}
                            </div>
                            <div class="text-danger">{% each.sno %}</div>
                        </div>
                        <div class="dp-flex dp-flex-gap5" >
                            <div class="sl-blue mgt3" @click="window.open(`ims_view2.php?sno=${each.sno}&currentStatus=${each.projectStatus}`)">{% each.customerName %}</div>
                            <!--
                            <div class="text-muted cursor-pointer hover-btn" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                            </div>
                            -->
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="dp-flex " v-show="false">
                <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                    <div class="font-11 text-muted cursor-pointer hover-btn " @click="ImsService.deleteData('project' , each.sno, refreshList)">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                    </div>
                <?php } ?>
            </div>

            <div class="dp-flex mgt5">
                <div class="sl-badge-small sl-badge-small-blue2 font-11" v-if="'p' === each.assortApproval">
                    아소트확정
                </div>
                <div class="sl-badge-small sl-badge-small-orange" v-if="'p' === each.customerOrderConfirm">
                    사양서확정
                </div>
            </div>
        </td>
        <td >
            <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
        </td>
        <td >
            <div>{% (listTotal.idx-index) %}</div>
        </td>
        <td v-for="fieldData in searchData.fieldData" :class="fieldData.class"
            :rowspan="fieldData.rowspan?each.projectRowspan:1"
            v-if="true != fieldData.rowspan || ( true == fieldData.rowspan && each.projectRowspan > 0 )"
        >
            <?php include 'nlist/list_template.php'?>

            <section v-if="'c' === fieldData.type">

                <!--퀄리티-->
                <div v-if="'fabricStatusKr' === fieldData.name && 'n' === each.isReorder" class="font-12 cursor-pointer hover-btn"
                     @click="openProductReg2(each.sno, each.styleSno, 1)"
                >
                    <div class="dp-flex font-11">
                        <div v-html="$.getStatusIcon(each.prdFabricStatus)" class="font-9"></div>
                        Q:{% each.prdFabricStatusKr %}
                    </div>
                    <div class="dp-flex font-11">
                        <div v-html="$.getStatusIcon(each.prdBtStatus)" class="font-9"></div>
                        B:{% each.prdBtStatusKr %}
                    </div>
                </div>

                <!--퀄리티-->
                <div v-if="'fabricStatusKr' === fieldData.name && 'y' === each.isReorder" >
                    해당 없음
                </div>


                <!--생산기간-->
                <div v-if="'prdPeriod' === fieldData.name" >
                    <div v-if="!$.isEmpty(each.estimateData)" class="cursor-pointer hover-btn" @click="openFactoryEstimateView(each.sno, each.styleSno, each.estimateConfirmSno, 'cost')">
                        <div v-if="!$.isEmpty(each.prdPeriod)">
                            {% each.prdPeriod %}
                        </div>
                        <div v-else class="text-muted font-11">
                            생산처<br>미입력
                        </div>
                    </div>
                    <div v-else class="text-muted font-12">
                        미정
                    </div>
                </div>

                <!--대표원단-->
                <div v-if="'repFabric' === fieldData.name" >
                    <div v-if="!$.isEmpty(each.repFabric)" class="cursor-pointer hover-btn" @click="openFactoryEstimateView(each.sno, each.styleSno, each.estimateConfirmSno, 'cost')">
                        {% each.repFabric %}
                    </div>
                    <div v-else class="text-muted font-12">
                        미정
                    </div>
                </div>

                <!--생산MOQ-->
                <div v-if="'prdMoq' === fieldData.name" >
                    <div v-if="!$.isEmpty(each.estimateData)" class="cursor-pointer hover-btn" @click="openFactoryEstimateView(each.sno, each.styleSno, each.estimateConfirmSno, 'cost')">
                        {% $.setNumberFormat(each.estimateData.prdMoq) %}
                    </div>
                    <div v-else class="text-muted">
                        미정
                    </div>
                </div>
                <!--단가MOQ-->
                <div v-if="'priceMoq' === fieldData.name" >
                    <div v-if="!$.isEmpty(each.estimateData)" class="cursor-pointer hover-btn"  @click="openFactoryEstimateView(each.sno, each.styleSno, each.estimateConfirmSno, 'cost')">
                        {% $.setNumberFormat(each.estimateData.priceMoq) %}
                    </div>
                    <div v-else class="text-muted">
                        미정
                    </div>
                </div>

                <!--판매가-->
                <div v-if="'salePrice' === fieldData.name" class="text-danger">
                    <div v-if="each.salePrice > 0">

                        <span v-if="'p' === each.priceConfirm" class="font-11">(확)</span>

                        {% $.setNumberFormat(each.salePrice) %}원
                    </div>
                    <div class="text-muted" v-if="0 >= each.salePrice">
                        확인중
                    </div>
                </div>

                <!--생산가-->
                <div v-if="'prdCost' === fieldData.name" >
                    <!--생산가격-->
                    <div v-if="!$.isEmpty(each.estimateData) && each.prdCostConfirmSno > 0"
                         class="sl-blue cursor-pointer hover-btn"
                         @click="openFactoryEstimateView(each.sno, each.styleSno, each.prdCostConfirmSno, 'cost')"
                    >
                        {% $.setNumberFormat(each.estimateData.totalCost) %}원
                    </div>
                    <!--견적가격-->
                    <div v-else-if="!$.isEmpty(each.estimateData) && each.estimateConfirmSno > 0"
                         class="text-muted cursor-pointer hover-btn"
                         @click="openFactoryEstimateView(each.sno, each.styleSno, each.estimateConfirmSno, 'estimate')"
                    >
                        (가){% $.setNumberFormat(each.estimateData.totalCost) %}원
                    </div>
                    <!--견적없을 때-->
                    <div v-else class="text-muted font-10 cursor-pointer hover-btn"
                         @click="openProductReg2(each.sno, each.styleSno, 3)">
                        선택 견적 없음<br>(견적리스트)
                    </div>
                </div>

                <!--작지-->
                <div v-if="'workStatus' === fieldData.name" class="cursor-pointer hover-btn" @click="openCommonPopup('ework', 1300, 850, {sno:each.styleSno, tabMode:'main'})">
                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.prdWorkStatus"></i>
                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.prdWorkStatus"></i>
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.prdWorkStatus"></i>
                </div>

                <!--작지(생산처)-->
                <div v-if="'factoryWorkStatus' === fieldData.name" class="cursor-pointer hover-btn" @click="window.open(`<?=$eworkUrl?>?sno=${each.styleSno}`);">
                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.prdWorkStatus"></i>
                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.prdWorkStatus"></i>
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.prdWorkStatus"></i>
                </div>

                <!--고객명(생산처)-->
                <div v-if="'factoryProductName' === fieldData.name" >
                    <div >
                        <div class="">
                            {% each.prdYear %}{% each.prdSeason %} {% each.productName %}
                        </div>
                        <div class="text-muted">
                            {% each.styleCode %} (#{% each.styleSno %})
                        </div>
                    </div>
                </div>

                <!--발주D/L-->
                <div v-if="'productionOrder' === fieldData.name">
                    <?php include 'template/basic_view/_production_order.php'?>
                </div>

            </section>

        </td>
    </tr>
</table>
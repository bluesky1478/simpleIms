<table class="table table-rows table-default-center mgt5 table-td-height0 ">
    <colgroup>
        <col style="width:11%"/><!--        고객사/프로젝트-->
        <col style="width:1.2%"/><!--      체크/번호-->
        <col style="width:11%"/><!--        스타일-->
        <col style="width:7%"/><!--        납기일-->
        <col style="width:54%"/><!--        스케쥴-->
    </colgroup>
    <thead>
    <tr>
        <th>고객사/프로젝트</th>
        <th >
            <input type="checkbox" data-target-name="prdSno" @click="toggleAllCheck()" id="prdAllCheck" >
        </th>
        <th>스타일/상태</th>
        <th>납기일</th>
        <th>스케쥴</th>
    </tr>
    </thead>

    <tbody v-if="productionList.length > 0">
    <template v-for="(production , productionIndex) in productionList" :class="(0 == productionIndex % 2)?'':''">
    <tr >
        <td :rowspan="production.projectRowspan" v-if="production.projectRowspan > 0" class="ta-l">

            <div class="sl-blue hover-btn cursor-pointer font-14 mgb10" v-if="!isFactory" @click="openCustomer(production.customerSno)">{% production.customerName %}</div>
            <div class="sl-blue hover-btn cursor-pointer font-14 mgb10" v-if="isFactory" >{% production.customerName %}</div>

            <span :class="'label-icon label-icon'+production.projectType">{% production.projectTypeEn %}</span>

            <span class="text-danger hover-btn cursor-pointer font-15" v-if="isFactory"  @click="openProjectViewFactory(production.projectSno)">{% production.projectSno %}</span>
            <span class="text-danger hover-btn cursor-pointer font-15" v-if="!isFactory" @click="openProjectViewAndSetTabMode(production.projectSno,'order')">{% production.projectSno %}</span>

            <span class="text-muted">{% production.projectStatusKr %}</span>
            <i class="fa fa-check cursor-pointer hover-btn" aria-hidden="true" style="color:#8a8a8a" @click="toggleProjectCheck(production)"></i>
            <div v-html="production.useInfo" class="mgt15 pdl5 ta-l"></div>

            <div class="">
                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w80p" @click="openProjectViewAndSetTabMode(production.projectSno,'style')">스타일</div>
                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w80p" @click="openProjectViewAndSetTabMode(production.projectSno,'comment')">코멘트</div>
            </div>

        </td><!--프로젝트-->
        <td >
            <div>{% (productionTotal.idx-productionIndex) %}</div>
            <input type="checkbox" name="prdSno[]" :value="production.sno" class="list-check" v-model="productionCheckList">
        </td><!--체크-->
        <td class="ta-l pdl5">



            <div class="font-14 cursor-pointer hover-btn" @click="openProductReg2(production.projectSno, production.styleSno, 5)" v-if="!isFactory">
                <div class="font-11" >{% production.customerName %}</div>
                <b>{% production.styleFullName %}</b>
                <div class="font-12">
                    ({% production.styleCode %})
                    <span class="">{% $.setNumberFormat(production.totalQty) %} 장</span>
                </div>
            </div>

            <div class="font-14 " v-if="isFactory">
                <div class="font-11" >{% production.customerName %}</div>
                <b>{% production.styleFullName %}</b>
                <div class="font-12">
                    ({% production.styleCode %})
                    <span class="">{% $.setNumberFormat(production.totalQty) %} 장</span>
                </div>
            </div>

            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openProduction(productionIndex, 'v')" v-if="!isList">상세정보</div>

            <div class="flex-column">
                <div class="btn btn-white btn-sm" @click="ImsProductService.openProduction(productionIndex, 'v')">
                    생산정보
                </div>

                <div class="btn btn-white btn-sm"
                     @click="openFactoryEstimateView(production.projectSno, production.styleSno, production.prdCostConfirmSno, 'cost')"
                     v-if="isList && !$.isEmpty(production.prdCostConfirmSno) && production.prdCostConfirmSno > 0">
                    생산가
                </div>
                <div class="disabled btn btn-sm btn-white hover-btn cursor-pointer" title="미확정"
                     v-if="isList && ($.isEmpty(production.prdCostConfirmSno) || 0 == production.prdCostConfirmSno)" >
                    생산가
                </div>

                <div class="btn btn-white btn-sm" @click="openUrl(`eworkP_${product.sno}`,`<?=$eworkUrl?>?sno=${production.styleSno}`,1600,950);" v-if="2 == production.workStatus">
                    작지
                </div>
                <div class="disabled btn btn-white btn-sm" v-if="2 != production.workStatus" title="미승인" alt="미승인">작지</div>
                <div class="btn btn-white btn-sm" v-if="!isFactory" @click="window.open(`<?=$guideUrl?>?key=${production.projectKey}`)">사양서</div>
                <!--
                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w80p" @click="ImsProductService.openProduction(productionIndex, 'v')" v-if="isList">생산정보</div>
                <div class="disabled btn btn-sm btn-white hover-btn cursor-pointer mgt5 w110p" v-if="isList && ($.isEmpty(production.prdCostConfirmSno) || 0 == production.prdCostConfirmSno)" >
                    생산가확정정보
                </div>
                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w110p" @click="openFactoryEstimateView(production.projectSno, production.styleSno, production.prdCostConfirmSno, 'cost')" v-if="isList && !$.isEmpty(production.prdCostConfirmSno) && production.prdCostConfirmSno > 0">
                    생산가확정정보
                </div>
                -->
            </div>

            <div class="font-12 mgt5" style="">{% production.produceStatusKr %} 상태</div>
            <div class="text-muted mgt2 font-11">
                <span v-if="30 == production.produceStatus" class="mgt5">
                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === production.scheduleCheck"></i>
                    <i class="fa fa-lg fa-times-circle text-danger" aria-hidden="true" v-if="'n' === production.scheduleCheck"></i>
                    <i class="fa fa-exclamation-triangle sl-orange" aria-hidden="true" v-if="'c' === production.scheduleCheck"></i>
                </span>
                마지막확인:{% $.formatShortDate(production.scheduleCheckDt) %}
            </div>
            <div class="text-muted mgt2 font-11">
                생산번호:#{% production.sno %}
                등록일:{% $.formatShortDate(production.regDt) %}
            </div>

        </td><!--스타일-->
        <td >
            <!--고객납기-->
            <div v-if=" 'n' !== $.cookie('setSaleCostDisplay') && !isFactory && 'p' !== production.deliveryConfirm && (!$.isEmpty(production.productCustomerDeliveryDt) && '0000-00-00' !== production.productCustomerDeliveryDt ) " style="border-bottom: solid 1px #e5e5e5; padding-bottom:3px;margin-bottom:3px">
                <span class="font-10" >고객납기:</span>
                <span class="font-10" >{% $.formatShortDate(production.productCustomerDeliveryDt) %}</span>
                <br>
                <span class="font-11" v-if="99 != production.produceStatus">
                    <span v-html="$.remainDate(production.productCustomerDeliveryDt,true)"></span>
                </span>
            </div>

            <!--발주일-->
            <div class="font-11 text-muted">발주 : {% $.formatShortDate(production.regDt) %}</div>

            <!--납기일-->
            <div v-if="99 !== production.produceStatus">
                <div class="font-14">
                    <b>{% $.formatShortDate(production.msDeliveryDt) %}</b>

                    <div v-html="$.remainDate(production.msDeliveryDt,true)" class="font-14 mgt10" v-if="'p' != production.deliveryConfirm"></div>

                    <div :class="'mgt5 font-16 ' + production.deliveryStatusColor" v-if="30 === production.produceStatus && 'p' != production.deliveryConfirm">
                        <i class="fa fa-circle" ></i> {% production.deliveryStatusName %}
                    </div>

                    <div :class="'mgt5 font-16 sl-green'" v-if="'p' === production.deliveryConfirm">
                        <div>공장납기완료</div>
                        <div class="text-muted font-11">(확인후 생산완료 처리)</div>
                    </div>

                </div>
            </div>
            <div v-if="99 === production.produceStatus" class="sl-green">
                납기 완료
            </div>
            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="ImsProductService.openProduction2(productionIndex)" v-if="isList && production.produceStatus >= 30">최초스케쥴 정보</div>
        </td><!--납기일-->

        <td class="pd0" >
            <!--스케쥴 테이블-->
            <table class="h100 font-11 w100 table-pd-3 table-borderless table-default-center table-td-height0 table table-rows " style="margin-bottom: 0 !important; border-top:none !important; border-bottom: 0 !important;">
                <colgroup>
                    <col style="width:7%">
                    <col style="width:10%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                    <col style="width:8.3%">
                </colgroup>
                <tbody>

                <tr>
                    <td colspan="4" style="font-size: 12px !important; background-color:#f9f9f9; border-top:none !important;" >생산처</td>
                    <td colspan="2" style="font-size: 12px !important; background-color:#f9f9f9; border-top:none !important;">입고지</td>
                    <td colspan="2" style="font-size: 12px !important; background-color:#f9f9f9; border-top:none !important;">폐쇄몰출고가능일</td>
                    <td colspan="2" style="font-size: 12px !important; background-color:#f9f9f9; border-top:none !important;">운송</td>
                    <td style="font-size: 12px !important; background-color:#f9f9f9; border-top:none !important;">봉제기간</td>
                    <td style="font-size: 12px !important; background-color:#f9f9f9; border-top:none !important;">생산기간</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="font-12">
                            {% production.reqFactoryNm %}
                        </div>
                    </td><!--생산처-->
                    <td colspan="2">
                        {% production.deliveryPlace %}
                    </td><!--입고지-->
                    <td colspan="2">
                        <span>{% production.privateMallDeliveryDt  %}</span>
                    </td><!--폐쇄몰출고가능일-->
                    <td colspan="2" class="ta-l pdl5 font-12">
                        <span v-if="'air' === production.globalDeliveryDiv">
                            <i class="fa fa-plane fa-lg" style="color:black!important;" aria-hidden="true"></i>
                        </span>
                        <span v-if="'ship' === production.globalDeliveryDiv">
                            <i class="fa fa-ship fa-lg" style="color:black!important;" aria-hidden="true"></i>
                        </span>
                        <span class="mgt5" v-if="!$.isEmpty(production.planPayDiv)">
                            항공비지불 : {% production.planPayDiv %}
                        </span>
                    </td><!--운송-->
                    <td>
                        {% production.periodOfSaw %}
                    </td><!--봉제기간-->
                    <td>
                        {% production.periodOfProduction %}
                    </td><!--생산기간-->

                </tr>

                <tr>
                    <td style="font-size: 11px !important; background-color:#f9f9f9; ">스케쥴명</td>
                    <?php foreach( $stepTitleList as $schedule ) { ?>
                        <td style="font-size: 12px !important; background-color:#f9f9f9; "><?=$schedule?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="font-size: 12px !important; background-color:#f9f9f9">최초예정</td>
                    <?php foreach( $stepList as $stepName ) { ?>
                        <td class="relative " style="color:#666666; background:#fcffe2">
                            <div v-if="$.isEmpty(production.firstData.schedule.<?=$stepName?>.Memo)" class="font-12">
                                {% $.formatShortDate(production.firstData.schedule.<?=$stepName?>.ConfirmExpectedDt) %}&nbsp;
                            </div><!--일반-->
                            <div v-if="!$.isEmpty(production.firstData.schedule.<?=$stepName?>.Memo)"  class="font-12">
                                {% production.firstData.schedule.<?=$stepName?>.Memo %}&nbsp;
                            </div><!--대체-->
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="font-size: 12px !important; background-color:#f9f9f9">
                        예정일
                    </td>
                    <?php foreach( $stepList as $stepName ) { ?>
                        <td class="relative hover-btn cursor-pointer" @click="openProduceUnit(production.sno, '<?=$stepName?>')" >

                            <div style="position:absolute; top:0;right:0; font-size: 14px !important; color:#f78800"  v-if="production.commentCnt.<?=$stepName?> > 0"  class="font-12">
                                <i class="fa fa-circle" aria-hidden="true"></i>
                            </div>
                            <div style="position:absolute; top:3px;right:1px; color:#fff; font-size: 9px; text-align: center; width:10px"  v-if="production.commentCnt.<?=$stepName?> > 0"  class="font-12">
                                {% production.commentCnt.<?=$stepName?> %}
                            </div>

                            <div class="font-12" v-if="$.isEmpty(production.<?=$stepName?>Memo)">
                                <div :class="true === productionSearchCondition.isDelayFirst && !$.isEmpty(production.firstData.schedule.<?=$stepName?>.ConfirmExpectedDt) && production.<?=$stepName?>ExpectedDt > $.dateAdd(production.firstData.schedule.<?=$stepName?>.ConfirmExpectedDt,4)?'text-danger':''">
                                    {% $.formatShortDate(production.<?=$stepName?>ExpectedDt) %}&nbsp;
                                </div>
                            </div>
                            <div v-if="!$.isEmpty(production.<?=$stepName?>Memo)"  class="font-12">
                                {% production.<?=$stepName?>Memo %}&nbsp;
                            </div>

                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="font-size: 12px !important; background-color:#f9f9f9">
                        완료일
                    </td>
                    <?php foreach( $stepList as $stepName ) { ?>
                        <td class="relative">
                            <div class="font-12" v-if="$.isEmpty(production.<?=$stepName?>Memo2)">{% $.formatShortDate(production.<?=$stepName?>CompleteDt) %}</div>
                            <div v-if="!$.isEmpty(production.<?=$stepName?>Memo2)"  class="font-12">{% production.<?=$stepName?>Memo2 %}</div>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="font-size: 12px !important; background-color:#f9f9f9">
                        상태
                    </td>
                    <?php foreach( $stepList as $stepName ) { ?>
                        <td class="relative">
                            <span  class="font-12" v-html="ImsService.setStatusFilter2(production.<?=$stepName?>Confirm)" v-if="false == production.<?=$stepName?>Delay"></span>
                            <div class="text-danger" v-if="true == production.<?=$stepName?>Delay">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>지연
                            </div>
                        </td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </template>
    </tbody>

    <!--데이터가 없을 때 처리-->
    <tbody v-if="0 >= productionList.length">
    <tr>
        <td colspan="99" class="ta-c">
            <div id="init-msg">Loading...</div>
        </td>
    </tr>
    </tbody>

</table>
<table class="table table-rows table-default-center mgt5 table-td-height0 ">
    <colgroup>
        <col style="width:2%"/><!--체크-->
        <col style="width:2%"/><!--번호-->
        <col style="width:6.25%"/><!--생산처/상태-->
        <col style="width:15%" v-if="isList" /><!--생산품-->

        <!--<col style="width:6%" v-if="!isList"/>-->

        <col style="width:5%"/><!--이노버발주-->
        <col style="width:8%"/><!--납기일-->
        <col style="width:3.5%"/><!--구분-->
        <?php foreach( $stepTitleList as $schedule ) { ?>
            <col style="width:5%"/>
        <?php } ?>
    </colgroup>
    <thead>
    <tr>
        <th style="height:35px !important;">
            <input type="checkbox" data-target-name="prdSno" @click="toggleAllCheck()" id="prdAllCheck" >
        </th>
        <th>번호</th>
        <th>생산처/상태</th>
        <th v-if="isList">생산품</th>

        <!--<th v-if="!isList">승인</th>-->

        <th>이노버발주일</th>
        <th>납기일</th>
        <th>구분</th>
        <?php foreach( $stepTitleList as $schedule ) { ?>
            <th style="font-size: 11px !important;"><?=$schedule?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody v-if="0 >= productionList.length">
    <tr>
        <td colspan="99" class="ta-c"></td>
    </tr>
    </tbody>
    <tbody v-for="(production , productionIndex) in productionList" :class="(0 == productionIndex % 2)?'bg-super-light-gray':''">
    <tr >
        <td rowspan="3">
            <input type="checkbox" name="prdSno[]" :value="production.sno" class="list-check" v-model="productionCheckList">
            <span class="font-11 text-muted">{% production.sno %}</span>
        </td>
        <td rowspan="3">
            {% (productionTotal.idx-productionIndex) %}
            <div v-if="30 == production.produceStatus">
                <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === production.scheduleCheck"></i>
                <i class="fa fa-lg fa-times-circle text-danger" aria-hidden="true" v-if="'n' === production.scheduleCheck"></i>
                <i class="fa fa-exclamation-triangle sl-orange" aria-hidden="true" v-if="'c' === production.scheduleCheck"></i>
            </div>
        </td>
        <td rowspan="3">
            <div class="font-14">
                {% production.reqFactoryNm %}
            </div>

            <div class="sl-blue bold font-11">{% production.produceStatusKr %}</div>

            <div class="hover-btn cursor-pointer btn btn-sm btn-red btn-red-line2 mgt5" @click="ImsProductionService.setProduceStatus(production.sno, 10)" v-if="0 == production.produceStatus">
                스케쥴요청
            </div>
            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="ImsProductionService.setProduceStatus(production.sno, 30)" v-if="20 == production.produceStatus">
                확정
            </div>
            <div class="hover-btn cursor-pointer btn btn-sm btn-red btn-red-line2 mgt5" @click="ImsProductionService.setProduceStatus(production.sno, 10)" v-if="20 == production.produceStatus">
                반려
            </div>
            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openProduction(productionIndex, 'v')" v-if="!isList">상세정보</div>
            
            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="ImsProductService.openProduction2(productionIndex)" v-if="isList && production.produceStatus >= 30">최초스케쥴</div>
            <div class="text-muted font-11">등록:{% $.formatShortDate(production.regDt) %}</div>
        </td>
        <td class="ta-l pdl5" rowspan="3" v-if="isList">
            <div v-if="!isFactory">
                <span :class="'label-icon label-icon'+production.projectType">{% production.projectTypeEn %}</span>

                <span class="sl-blue hover-btn cursor-pointer" @click="openCustomer(production.customerSno)">{% production.customerName %}</span>
                <span class="text-danger hover-btn cursor-pointer" @click="openProjectView(production.projectSno)">{% production.projectNo %}</span>
                <br>
                <div class="font-14 cursor-pointer hover-btn" @click="openProductReg2(production.projectSno, production.styleSno, 3)">
                    {% production.styleFullName %}
                    <span style="font-size:11px !important;">({% $.setNumberFormat(production.totalQty) %}개)</span>
                </div>
                <div class="text-muted">({% production.styleCode %})</div>
            </div>
            <div v-if="isFactory">
                <!--생산처일 경우-->
                <span class="sl-blue">{% production.customerName %}</span>
                <span class="text-danger hover-btn cursor-pointer" @click="openProjectView(production.projectSno)">{% production.projectNo %}</span>
                <div class="font-14 cursor-pointer hover-btn" @click="ImsProductService.openProduction(productionIndex, 'v')">
                    {% production.styleFullName %}
                    <span style="font-size:11px !important;">({% $.setNumberFormat(production.totalQty) %}개)</span>
                </div>
                <div class="text-muted">({% production.styleCode %})</div>
            </div>
            <div v-html="production.useInfo" class="mgt5"></div>

            <div class="mgt5" v-if="'y' === production.useMall && !$.isEmpty(production.privateMallDeliveryDt) ">
                폐쇄몰 출고가능 : <span>{% production.privateMallDeliveryDt  %}</span>
            </div>

            <!-- FIXME : 인터페이스 변경시 처리
            <div class="mgt5">
                <span>입고지 : 삼영</span>
                <span>봉제기간 : 측정불가</span>
            </div>
            FIXME : 인터페이스 변경시 처리 -->

            <div class="mgt5" v-if="!$.isEmpty(production.planPayDiv)">항공비용지불 : {% production.planPayDiv %}</div>
            <div class="text-muted mgt2 font-11">마지막확인:{% $.formatShortDate(production.scheduleCheckDt) %}</div>

        </td>
        <!--
        <td rowspan="3" v-if="!isList" class="ta-c pd0">
            <table class="table-pd-0 table-default-center table-borderless w100">
                <tr>
                    <th class="pd0" style="background-color: #f1f1f1!important; color:#000 !important;">아소트</th>
                </tr>
                <tr>
                    <td style="height: 25px">
                        {% production.assortConfirmKr %}
                    </td>
                </tr>
                <tr>
                    <th class="pd0" style="background-color: #f1f1f1!important; color:#000 !important;">작업지시서</th>
                </tr>
                <tr>
                    <td style="height: 25px">
                        {% production.workConfirmKr %}
                    </td>
                </tr>
            </table>
        </td>
        -->
        <td rowspan="3">
            <div v-if="99 !== production.produceStatus">
                <div class="font-14">
                    {% $.formatShortDate(production.msOrderDt) %}
                </div>
            </div>
            <div v-if="99 === production.produceStatus" class="sl-green">
                납기 완료
            </div>

            <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w100" @click="ImsProductService.openProduction(productionIndex, 'v')">상세 보기</div>
            <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w100" @click="openProjectViewAndSetTabMode(production.projectSno,'comment')">코 멘 트</div>

        </td>
        <td rowspan="3">
            <div v-if="99 !== production.produceStatus">
                <!--
                <div v-if="!isFactory" class="text-muted">
                    {% $.formatShortDate(production.productCustomerDeliveryDt) %}
                    <br>
                    <span class="font-11" v-if="(!$.isEmpty(production.productCustomerDeliveryDt) && '0000-00-00' !== production.productCustomerDeliveryDt )">
                        <span v-html="$.remainDate(production.productCustomerDeliveryDt,true)"></span>
                        <br>(고객납기일)
                    </span>
                </div>
                -->
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
        </td>
        <td style="height:61px!important;">
            <span style="font-size: 10px;color:#666666; background:#ffffd6">최초예정</span>
            <br>예정일
        </td>
        <?php foreach( $stepList as $stepName ) { ?>
            <td class="relative hover-btn cursor-pointer" @click="openProduceUnit(production.sno, '<?=$stepName?>')">

                <div style="position:absolute; top:0;left:0; font-size: 14px; color:#f78800"  v-if="production.commentCnt.<?=$stepName?> > 0">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                </div>
                <div style="position:absolute; top:5px;left:1px; color:#fff; font-size: 9px; text-align: center; width:10px"  v-if="production.commentCnt.<?=$stepName?> > 0">
                    {% production.commentCnt.<?=$stepName?> %}
                </div>

                <div class="font-11 mgb5" style="color:#666666; background:#ffffd6 ">
                    <div v-if="$.isEmpty(production.firstData.schedule.<?=$stepName?>.Memo)">
                        {% $.formatShortDate(production.firstData.schedule.<?=$stepName?>.ConfirmExpectedDt) %}&nbsp;
                    </div><!--일반-->
                    <div v-if="!$.isEmpty(production.firstData.schedule.<?=$stepName?>.Memo)">
                        {% production.firstData.schedule.<?=$stepName?>.Memo %}&nbsp;
                    </div><!--대체-->
                </div>

                <div class="font-12" v-if="$.isEmpty(production.<?=$stepName?>Memo)">
                    {% $.formatShortDate(production.<?=$stepName?>ExpectedDt) %}&nbsp;
                </div>
                <div v-if="!$.isEmpty(production.<?=$stepName?>Memo)">
                    {% production.<?=$stepName?>Memo %}&nbsp;
                </div>

            </td>
        <?php } ?>
    </tr>
    <tr>
        <td>
            완료일
        </td>
        <?php foreach( $stepList as $stepName ) { ?>
            <td class="relative">
                <div class="font-12" v-if="$.isEmpty(production.<?=$stepName?>Memo2)">{% $.formatShortDate(production.<?=$stepName?>CompleteDt) %}</div>
                <div v-if="!$.isEmpty(production.<?=$stepName?>Memo2)">{% production.<?=$stepName?>Memo2 %}</div>
                <!--
                <div v-if="!$.isEmpty(production.<?=$stepName?>Memo)">{% production.<?=$stepName?>Memo %}</div>
                -->
            </td>
        <?php } ?>
    </tr>
    <tr>
        <td>
            상&nbsp;&nbsp;태
        </td>
        <?php foreach( $stepList as $stepName ) { ?>
            <td>
                <span v-html="ImsService.setStatusFilter2(production.<?=$stepName?>Confirm)" v-if="false == production.<?=$stepName?>Delay"></span>
                <div class="text-danger" v-if="true == production.<?=$stepName?>Delay">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>지연
                </div>
            </td>
        <?php } ?>
    </tr>
    </tbody>
</table>
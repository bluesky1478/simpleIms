<!-------------------------------   담당자 / 참여 부가서비스        ------------------------------------>
<div v-if="'projectMember' === fieldData.name" class="text-left pdl5">
    <div >
        <div class="">
            <div>

                <div class="dp-flex dp-flex-gap10 mgt3 hand hover-btn w-100p" @click="openProjectSimple(each.sno)">
                    <div v-html="'영업:' + $.isset(each.salesManagerName,'<span class=\'text-muted\'>미정</span>')"></div>
                    <div v-html="'디자인:' + $.isset(each.designManagerName,'<span class=\'text-muted\'>미정</span>')"></div>
                </div>

                <span class=" hover-wrap hand" v-if="!$.isEmpty(each.addManagerList) && !$.isEmptyObject(each.addManagerList)" >
                    <span class="font-11 hover-target  hover-btn" @click="openProjectSimple(each.sno)">
                        참여:
                        <span v-for="(addManager, addManagerIdx) in each.addManagerList">
                            {% addManager.managerNm %}
                        </span>
                    </span>

                    <!-- ### LAYER POPUP 참여자 롤오버 레이어 ###-->
                    <div class="hover-layer-right " >
                        <div >
                            <span class="sl-blue">{% each.customerName %} {% each.projectYear %} {% each.projectSeason %}</span>
                        </div>
                        <div class="dp-flex dp-flex-gap10" v-if="!$.isEmpty(each.salesManagerName) || !$.isEmpty(each.designManagerName)">
                            <div v-if="!$.isEmpty(each.salesManagerName)">영업:{% each.salesManagerName %}</div>
                            <div v-if="!$.isEmpty(each.designManagerName)">디자인:{% each.designManagerName %}</div>
                        </div>

                        <table class="table table-rows table-th-height0 table-td-height0 mgt5 table-pd-5">
                            <tbody v-for="addManager in each.addManagerList" >
                                <tr v-for="(schedule,scheduleIdx) in addManager.schedule" v-if="0 === scheduleIdx">
                                    <td class="ta-l bg-light-gray font-11 pd0" :rowspan="addManager.schedule.length">
                                        {% addManager.managerNm %}
                                    </td>
                                    <td class="font-11 pd0 ta-l">
                                        {% schedule.name %}
                                    </td>
                                    <td class="font-11 pd0">
                                        ~{% $.formatShortDate(schedule.date) %}
                                    </td>
                                </tr>
                                <tr v-for="(schedule,scheduleIdx) in addManager.schedule" v-if="scheduleIdx > 0">
                                    <td class="font-11 pd0  ta-l">
                                        {% schedule.name %}
                                    </td>
                                    <td class="font-11 pd0">
                                        ~{% $.formatShortDate(schedule.date) %}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </span>
                <div class="mgt3">
                    <span class="mini-label mini-label--blue" v-if="'y' === each.use3pl">3PL</span>
                    <span class="mini-label mini-label--green" v-if="'y' === each.packingYn">분류패킹</span>
                    <span class="mini-label mini-label--orange" v-if="'y' === each.useMall">폐쇄몰</span>
                    <span class="mini-label mini-label--purple" v-if="'y' === each.directDeliveryYn">직납</span>
                </div>

            </div>

        </div>
    </div>
</div>

<!-------------------------------   담당자 / 참여    ------------------------------------>
<div v-if="'projectSimpleMember' === fieldData.name" class="text-left pdl5">
    <div >
        <div class="">
            <div>
                <div class="dp-flex dp-flex-gap10 mgt3 hand hover-btn w-100p" @click="openProjectSimple(each.sno)">
                    <div v-html="'영업:' + $.isset(each.salesManagerName,'<span class=\'text-muted\'>미정</span>')"></div>
                    <div v-html="'디자인:' + $.isset(each.designManagerName,'<span class=\'text-muted\'>미정</span>')"></div>
                </div>
                <span class=" hover-wrap hand" v-if="!$.isEmpty(each.addManagerList) && !$.isEmptyObject(each.addManagerList)" >
                    <span class="font-11 hover-target  hover-btn" @click="openProjectSimple(each.sno)">
                        참여:
                        <span v-for="(addManager, addManagerIdx) in each.addManagerList">
                            {% addManager.managerNm %}
                        </span>
                    </span>

                    <!-- ### LAYER POPUP 참여자 롤오버 레이어 ###-->
                    <div class="hover-layer-right " >
                        <div >
                            <span class="sl-blue">{% each.customerName %} {% each.projectYear %} {% each.projectSeason %}</span>
                        </div>
                        <div class="dp-flex dp-flex-gap10" v-if="!$.isEmpty(each.salesManagerName) || !$.isEmpty(each.designManagerName)">
                            <div v-if="!$.isEmpty(each.salesManagerName)">영업:{% each.salesManagerName %}</div>
                            <div v-if="!$.isEmpty(each.designManagerName)">디자인:{% each.designManagerName %}</div>
                        </div>

                        <table class="table table-rows table-th-height0 table-td-height0 mgt5 table-pd-5">
                            <tbody v-for="addManager in each.addManagerList" >
                                <tr v-for="(schedule,scheduleIdx) in addManager.schedule" v-if="0 === scheduleIdx">
                                    <td class="ta-l bg-light-gray font-11 pd0" :rowspan="addManager.schedule.length">
                                        {% addManager.managerNm %}
                                    </td>
                                    <td class="font-11 pd0 ta-l">
                                        {% schedule.name %}
                                    </td>
                                    <td class="font-11 pd0">
                                        ~{% $.formatShortDate(schedule.date) %}
                                    </td>
                                </tr>
                                <tr v-for="(schedule,scheduleIdx) in addManager.schedule" v-if="scheduleIdx > 0">
                                    <td class="font-11 pd0  ta-l">
                                        {% schedule.name %}
                                    </td>
                                    <td class="font-11 pd0">
                                        ~{% $.formatShortDate(schedule.date) %}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </span>
            </div>
        </div>
    </div>
</div>

<!-------------------------------   스타일/프로젝트 project  ------------------------------------>
<div v-if="'project' === fieldData.name" class="pdl5 ta-l font-12">
    <div>
        <span class="font-13 hand hover-btn text-danger" @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)">
            {% each.sno %}
        </span>
        <span >
            <span class="sl-blue hand hover-btn" @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)">{% each.customerName %}</span>
            <button type="button" class="ims-mini-btn ims-mini-btn--ghost mgl5" @click="openCustomer(each.customerSno)">
              고객정보
            </button>
        </span>
    </div>

    <div class="dp-flex mgt5">
        <div class="hand hover-btn font-12 " @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)">
            {% each.projectYear %} {% each.projectSeason %}
            {% each.salesStyleName %}
        </div>
        <div v-if="'-' !== each.salesStyleName">
            <!--<img src="/admin/gd_share/img/icon_grid_open.png" alt="프로젝트 새창 열기" class="hand mgl5 hover-btn" border="0" @click="openStyle(each)">-->
            <!--@click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)"-->
            <span class="ims-tt" >
                <i class="fa fa-external-link bold hand mgl5 hover-btn" aria-hidden="true" @click="openStyle(each)"></i>
                <span class="ims-tt-box">
                    클릭시 스타일 상세정보 열람
                </span>
            </span>
        </div>
    </div>

    <span v-else @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)" class="hand hover-btn">
        스타일 미등록
    </span>

    <div class="font-10 text-muted">
        {% $.formatShortDateWithoutWeek(each.regDt) %} {% each.regManagerName %} 등록
    </div>

    <div style="position: absolute;top:1px;right:1px;" class="dp-flex">

        <!--고객상태-->
        <!--<span :class="'cust-status cust-status--'+each.customerStatus">
            {% JS_LIB_CODE.customerStatusMap[each.customerStatus] %}
        </span>-->

        <span class="cust-status cust-status--biz" v-if="'y' === each.bizPlanYn">
            <span v-if="!$.isEmpty(each.targetSalesYear)">{% (''+each.targetSalesYear).slice(-2) %}사업</span>
            <span v-else>사업</span>
        </span>
        <span class="cust-status cust-status--biz2" v-if="'n' === each.bizPlanYn && !$.isEmpty(each.targetSalesYear) && each.targetSalesYear > 0 ">
            <span >{% (''+each.targetSalesYear).slice(-2) %}매출</span>
        </span>
    </div>

</div>

<!-------------------------------   고객납기 customerDeliveryDt  ------------------------------------>
<div v-if="'customerDeliveryDt' === fieldData.name" class="text-left pdl5 relative font-13">
    <div >
        <!--TODO : 생산완료 표시하기 -->
        <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
        <div v-if="!$.isEmpty(each[fieldData.name])">
            <span class="font-13">{% $.formatShortDateWithoutWeek(each[fieldData.name]) %}</span>
            <div class="font-11 mgt5" v-html="$.remainDate(each[fieldData.name],true)" v-if="91 != each.projectStatus"></div>
            <div class="font-11 mgt5 sl-green" v-if="91 == each.projectStatus">납기완료</div>
        </div>
    </div>
</div>

<!-------------------------------   프로젝트 타입 projectType   -------------------------------------->
<div v-if="'projectType' === fieldData.name">
    <div class="font-bold font-11">
        <div class="round-box bg-light-blue" v-if="[0,2,6,5].includes(each.projectType)" style="padding:5px 10px">
            {% each.projectTypeKr %}
        </div>
        <div class="round-box bg-light-orange" v-else>
            {% each.projectTypeKr %}
        </div>
    </div>

    <div class="font-10 mgt3" v-if="0 != each.designWorkType">{% each.designWorkTypeKr %}</div>

    <div v-if="'bid'===each.bidType2" class="font-10 mgt3 text-muted">
        <i class="fa fa-gavel" aria-hidden="true"></i>
        {% each.bidType2Kr %}
    </div>
    <div v-if="'costBid'===each.bidType2" class="font-10 mgt3 text-muted">
        <i class="fa fa-krw" aria-hidden="true"></i>
        {% each.bidType2Kr %}
    </div>
    <div v-if="'single'===each.bidType2" class="font-10 mgt3 text-muted">
        <i class="fa fa-handshake-o" aria-hidden="true"></i>
        {% each.bidType2Kr %}
    </div>
</div>

<!-------------------------------   발주D/L productionOrder  ------------------------------------>
<div v-if="'productionOrder' === fieldData.name" class="ta-l pdl5">
    <!--완료일-->
    <div v-if="'0000-00-00' != each.cpProductionOrder && !$.isEmpty(each.cpProductionOrder)" class="text-muted">
    <span class="font-14 sl-green">
        {% $.formatShortDateWithoutWeek(each.cpProductionOrder) %} 발주
    </span>
    </div>
    <!--대체텍스트-->
    <div v-else-if="!$.isEmpty(each.txProductionOrder)">
    <span class="font-11">
        {% each.txProductionOrder %}
    </span>
    </div>
    <!--예정일-->
    <div v-else-if="!$.isEmpty(each.exProductionOrder)" class="">
    <span class="font-14">
        {% $.formatShortDateWithoutWeek(each.exProductionOrder) %}
    </span>
        <div class="font-11 mgt5" v-html="$.remainDate(each.exProductionOrder,true)"></div>
    </div>
    <!--미설정-->
    <div v-else class="text-muted">미정</div>
</div>

<!-------------------------------   생산가 prdCostApproval   ------------------------------------>
<div v-if="'prdCostApproval' === fieldData.name">
    <div>
        <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-if="0 == each.costStatus"></i>
        <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-if="1 == each.costStatus"></i>
        <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="2 == each.costStatus"></i>
    </div>
</div>

<!-------------------------------  상태 projectStatusKr      ------------------------------------>
<div v-if="'projectStatusKr' === fieldData.name" class="lineR font-13">
    <div :class="'round-box dp-flex ims-status ims-status' + each.projectStatus" style="min-height:45px;justify-content: center; align-items: center; padding-left:14px; padding-right:14px">
        {% JS_LIB_CODE.projectStatusMap[each.projectStatus] %}
    </div>
</div>

<!-------------------------------  매출정보 salesInfo  ------------------------------------------->
<div v-if="'salesInfo' === fieldData.name" class="text-left font-11">
    <!--
    가격 : [ {% each.extPrice %} / {% each.extCost %} ]
    <div>
        상태 : [ {% each.extPriceStatus %} / {% each.extCostStatus %} ]
    </div>
    -->

    <div v-if="0 >= each.extPrice">
        <!-- 추정상태 -->
        <div>
            추정 판매 : {% $.numberToKorean(each.extAmount) %}
        </div>
        <div v-if="each.extMargin > 0 && each.extAmount > 0">
            추정 마진 : {% $.numberToKorean(each.extMargin) %}
            <span class="text-muted">{% $.getMargin(each.extAmount-each.extMargin, each.extAmount) %}%</span>
        </div>
    </div>
    <div v-else>

        <div>
            <div v-if="3 == each.extPriceStatus">
                매출 : (확정) {% $.numberToKorean(each.extPrice) %}
            </div>
            <div v-if="2 == each.extPriceStatus">
                매출 : (견적) {% $.numberToKorean(each.extPrice) %}
            </div>
            <div v-if="1 == each.extPriceStatus">
                매출 : (타겟) {% $.numberToKorean(each.extPrice) %}
            </div>
        </div>

        <div class="mgt3">
            <div v-if="3 == each.extCostStatus">
                매입 : (확정) {% $.numberToKorean(each.extCost) %}
            </div>
            <div v-if="2 == each.extCostStatus">
                매입 : (견적) {% $.numberToKorean(each.extCost) %}
            </div>
            <div v-if="1 == each.extCostStatus">
                매입 : (타겟) {% $.numberToKorean(each.extCost) %}
            </div>
        </div>

        <div class="mgt3" v-if="each.extPrice > 0 && each.extCost > 0">
            마진 : {% $.getMargin(each.extCost, each.extPrice) %}%
        </div>
    </div>


    <!--<div >
        extPriceStatus
        {% each.extPriceStatus %}
    </div>
    <div >
        extPrice
        {% each.extPrice %}
    </div>
    <div >
        extCostStatus
        {% each.extCostStatus %}
    </div>
    <div >
        extCost
        {% each.extCost %}
    </div>-->

    <!--<div>매출: {% each.totalPrdPriceKr %}</div>
    <div>마진: {% each.totalMarginKr %}</div>
    <div>마진%: {% each.totalMarginPercent %}%</div>-->
</div>

<!-------------------------------  구분 => 예정일/완료일 ( expectedTitle , completeTitle )  ------------------------------------------->
<div v-if="'expectedTitle' === fieldData.name" class="cursor-pointer hover-btn font-11" @click="openProjectSimple(each.sno)">
    <!--예정일-->
    <div class="btn btn-sm btn-white">스케쥴</div>
</div>
<div v-if="'completeTitle' === fieldData.name">
    상태
</div>

<!-------------------------------  프로젝트 번호 projectSno ------------------------------------------->
<div v-if="'projectSno' === fieldData.name">
    <span class="hand hover-btn text-danger" @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)">
        {% each.sno %}
    </span>
</div>

<!-------------------------------  고객명 customerName  ------------------------------------------->
<div v-if="'customerName' === fieldData.name">
    <div>
        <span class="hand hover-btn text-muted" @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)">
            #{% each.sno %}
        </span>
            <span class="sl-blue hand hover-btn" @click="window.open(`./ims25_view.php?sno=${each.sno}&current=<?=$current?>`)">
            {% each.customerName %}
            <span class="text-danger font-10">{% each.styleCode %}</span>
        </span>
    </div>
    <div class="font-11 text-muted">
        {% ('-' === each.salesStyleName ? '스타일 확인중':each.salesStyleName ) %}
    </div>
    <!--@click="openCustomer(each.customerSno)"-->

    <div style="position: absolute;top:1px;right:1px;" class="dp-flex">
        <span class="cust-status cust-status--biz" v-if="'y' === each.bizPlanYn">
            <span v-if="!$.isEmpty(each.targetSalesYear)">{% (''+each.targetSalesYear).slice(-2) %}사업</span>
            <span v-else>사업</span>
        </span>
        <span class="cust-status cust-status--biz2" v-if="'n' === each.bizPlanYn && !$.isEmpty(each.targetSalesYear) && each.targetSalesYear > 0 ">
            <span >{% (''+each.targetSalesYear).slice(-2) %}매출</span>
        </span>
    </div>
</div>

<!-------------------------------  미팅/입찰예정 exMeeting  ------------------------------------------->
<div v-if="'exMeeting' === fieldData.name">
    <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
    <div v-else class="">
        <div v-if="'single' === each.bidType2">미팅일</div>
        <div v-else>입찰일</div>
        <div>{% $.formatShortDateWithoutWeek(each[fieldData.name]) %}</div>
        <div class="font-11" v-html="$.remainDateWithoutPast(each[fieldData.name],true)"></div>
    </div>
</div>

<!-------------------------------  고객구분 customerStatus  ------------------------------------------->
<div v-if="'customerStatus' === fieldData.name">
    {% JS_LIB_CODE.customerStatusMap[each.customerStatus] %}
</div>

<!-------------------------------  추정매출 estSales  ------------------------------------------->
<!--스타일 현재단가 있는 경우 => ? 자동계산해서 extAmount , extMargin 저장 ?-->
<div v-if="'estSales' === fieldData.name">
    <div v-if="Number(each.extAmount)>0">
        <div>{% $.numberToKorean(each.extAmount) %}원</div>
        <div v-if="each.extMargin > 0">({% $.numberToKorean(each.extMargin) %}원/{% $.getMargin((each.extAmount-each.extMargin), each.extAmount)%}%)</div>
    </div>
    <div v-else class="font-11">
        <div v-if="!$.isEmpty(each.extAmount)">
            <div>{% each.extAmount %}</div>
            <div v-if="!$.isEmpty(each.extMargin)">({% each.extMargin %})</div>
        </div>
        <div v-else class="text-muted ">확인중</div>
    </div>
</div>

<!-------------------------------  디자인팀 참여 designTeamInfo  ------------------------------------------->
<span v-if="'designTeamInfo' === fieldData.name">
    <ul class="dp-flex dp-flex-wrap">
        <?php foreach(\Component\Ims\ImsCodeMap::DESIGN_JOIN_TYPE as $k => $v){ ?>
        <li v-if="each.designTeamInfo & <?=$k?>"><i class="fa fa-caret-right text-muted" aria-hidden="true"></i> <?=$v?></li>
        <?php } ?>
    </ul>
</span>

<!-------------------------------  TM이력 tmList  ------------------------------------------->
<span v-if="'tmList' === fieldData.name">
    <div class="ims-tt ims-tt-left _ims-tt-light" v-if="!$.isEmpty(tmMap[each.sno]) && !$.isEmpty(tmMap[each.sno])">
        <div class="btn btn-sm btn-white"
             @click="openCommonPopup('sales_customer_contents', 1050, 750, {sno:tmMap[each.sno][0]['salesSno']});">
            보기({% tmMap[each.sno].length %})
        </div>
        <div class="ims-tt-box">
            <div>
                <ul class="ta-l">
                    <li v-for="(comment, commentIdx) in tmMap[each.sno]"
                        v-if="6 >= commentIdx"
                        style="border-bottom:dot-dot-dash 1px #000" class="font-12 mgb5 pdb2"
                    >
                        <!--{% tmMap[each.sno].length - commentIdx %}.-->
                        <div>{% $.formatShortDateWithoutWeek(comment.regDt) %} {% comment.regManagerName %}</div>
                        <div class="pdl2">▶ {% comment.contents %}</div>
                    </li>
                    <li v-if="tmMap[each.sno].length > 6" class="font-11">
                        코멘트는 최대 6개만 표시 됩니다.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div v-else>-</div>
</span>

<!-------------------------------  등록자 regManagerNm  ------------------------------------------->
<span v-if="'regManagerNm' === fieldData.name">
    {% managerMap[each.regManagerSno] %}
</span>

<!-------------------------------  영업 메모 salesMemo  ------------------------------------------->
<div v-if="'salesMemo' === fieldData.name" class="">
    <div class="ims-tt ims-tt-left _ims-tt-light">
        <div class="ims-ellipsis w-230px hand hover-btn">
            {% each.salesMemo %}
        </div>
        <div class="ims-tt-box">
            {% each.salesMemo %}
        </div>
    </div>
</div>

<!-------------------------------  유찰 메모 holdMemo  ------------------------------------------->
<div v-if="'holdMemo' === fieldData.name" class="">
    <div class="ims-tt ims-tt-left _ims-tt-light">
        <div class="ims-ellipsis w-230px hand hover-btn">
            {% each.holdMemo %}
        </div>
        <div class="ims-tt-box">
            {% each.holdMemo %}
        </div>
    </div>
</div>

<!-------------------------------  업종  ------------------------------------------->
<div v-if="'pBizName' === fieldData.name" class="">
    <div class="bold">{% each.pBizName %}</div>
    <div>{% each.bizName %}</div>
</div>

<!-------------------------------  영업 기획  ------------------------------------------->
<div v-if="'salesPlan' === fieldData.name" class="">
    <div class="btn btn-sm btn-white" @click="openSalesView(each.sno)" >열기</div>
</div>

<!--TODO-->
<!-------------------------------  프로젝트 메모? projectMemo  ------------------------------------------->
<div v-if="'projectMemo' === fieldData.name">
    <div class="btn btn-sm btn-white">메모</div>
</div>



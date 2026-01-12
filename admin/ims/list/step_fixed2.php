<td :rowspan="project.defaultRowspan + project.addRowspan" class="">
    <input type="checkbox" name="sno[]" :value="project.sno" class="list-check" v-model="projectCheckList">
</td>
<td class="font-num" :rowspan="project.defaultRowspan + project.addRowspan">
    <div>{% (projectTotal.idx-projectIndex) %}</div>
    <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
        <div class="text-muted">
            {% project.sno %}
        </div>
    <?php } ?>
</td>

<!--등록일-->
<td class="center font-11" :rowspan="project.defaultRowspan  + project.addRowspan">
    <div>{% $.formatShortDateWithoutWeek(project.regDt) %}</div>
    <div class="text-muted">{% $.formatShortTime(project.regDt) %}</div>
</td>

<!--시즌-->
<td class="center" :rowspan="project.defaultRowspan  + project.addRowspan ">
    <div>
        {% project.projectYear %}
        {% project.projectSeason %}
    </div>
    <?php if( \SiteLabUtil\SlCommonUtil::isDevId() ){ ?>
        <!--<div class="btn btn-blue-line btn-sm cursor-pointer hover-btn" @click="reOrder(project.sno, 50)">reorder</div>-->
        <!--<div class="btn btn-red btn-red-line2 btn-sm cursor-pointer hover-btn" @click="reOrder(project.sno, 10)">reorder</div>-->
    <?php } ?>
</td>

<!--프로젝트 타입-->
<td class="center" :rowspan="project.defaultRowspan  + project.addRowspan">
    <div>{% project.projectTypeKr %}</div>
    <!--
    <div v-if="!$.isEmpty(project.bidType)">{% project.bidType %}</div>
    -->

    <button class="badge-button gray-button mgt3" v-if="project.nextSeason > 0"
            @click="" style="font-size:8px;">System</button>

</td>

<!--고객사-->
<td class="text-left pdl10" :rowspan="project.defaultRowspan + project.addRowspan ">
    <div class="pdl10">

        <span class="tn-pop-customer-info hover-btn cursor-pointer" :data-sno="project.customerSno" @click="openCustomer(project.customerSno)">
            {% project.customerName %}
            <span class="text-muted font-11" v-if="!$.isEmpty(project.salesManagerNm)">
                ({% project.salesManagerNm %}<span v-if="!$.isEmpty(project.designManagerNm)">/{% project.designManagerNm %}</span>)
            </span>
        </span>

        <span class="text-muted mgl5"></span>

        <div class="number text-danger">

            <a :href="`ims_view2.php?sno=${project.projectSno}&status=${project.projectStatus}`" class="text-danger" v-if="40 !== Number(project.projectStatus) && 41 !== Number(project.projectStatus) ">
                {% project.projectSno %}
            </a>

            <a :href="`ims_view2.php?sno=${project.projectSno}&status=${project.projectStatus}<?='02001002'==$teamSno? '&styleTabMode=sample' : '' ?>`" class="text-danger" v-if="40 === Number(project.projectStatus) || 41 === Number(project.projectStatus) ">
                {% project.projectSno %}
            </a>

            <div class="btn btn-white btn-sm ">
                <a :href="`ims_view2.php?sno=${project.projectSno}&status=${project.projectStatus}`" target="_blank" class="text-danger" style="color:black!important;">보기</a>
            </div>

            <a :href="`imsList.php?status=${project.projectStatus}&key[]=cust.customerName&keyword[]=${project.customerName}`" class="text-muted">
                {% project.projectStatusKr %}단계
            </a>

            <div>

                <!--<span class="btn btn-white btn-sm" @click="openSimpleProject({'projectSno':project.sno})">수정</span>-->
                <!--<span class="text-muted cursor-pointer hover-btn" @click="openSimpleProject({'projectSno':project.sno})">
                    <i class="fa fa-calendar-o" aria-hidden="true"></i>일정수정
                </span>-->

                <!--@click="ImsService.deleteData('newimsComment',item.sno, imsCommentService.getList)">삭제</div>-->

                <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                    <span class="text-muted cursor-pointer hover-btn mgl10" @click="ImsService.deleteData('project' , project.sno, refreshProjectList)">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                    </span>
                <?php } ?>

            </div>


        </div>
        <span class="text-muted"></span>
        <!--생산상태
        [ {% project.productionStatus %} ]-->
    </div>
</td>

<!--희망 납기일-->
<td class="center" :rowspan="project.defaultRowspan  + project.addRowspan">
    <div v-if="2 == project.productionStatus">생산완료</div>

    <div v-if="2 != project.productionStatus && 98 > project.projectStatus" >

        <div v-if="'0000-00-00' == project.customerDeliveryDt || $.isEmpty(project.customerDeliveryDt)">
            <div class="text-muted" >미입력</div>
        </div>
        <div v-if="'0000-00-00' != project.customerDeliveryDt && !$.isEmpty(project.customerDeliveryDt)">
            <div class="">{% $.formatShortDate(project.customerDeliveryDt) %}</div>
            <div v-html="$.remainDate2(project.customerDeliveryDt, true)"></div>
            <div class="text-green"  v-if="'y' == project.customerDeliveryDtConfirmed">변경가능</div>
            <div class="text-danger" v-if="'n' == project.customerDeliveryDtConfirmed">변경불가</div>
        </div>
    </div>
</td>

<!--발주 DL-->
<td class="center" :rowspan="project.defaultRowspan  + project.addRowspan">
    {% project.cpProductionOrder %}발주
</td>

<!--매출규모-->
<td class="center text-nowrap" :rowspan="project.defaultRowspan  + project.addRowspan ">
    <span class="text-muted" v-if="$.isEmpty(project.customerSizeKr)">파악중</span>
    <span class="" v-if="!$.isEmpty(project.customerSizeKr)">
        {% project.customerSizeKr %}
        <div class="text-muted" v-if="'expected'==project.customerSizeType">(추정)</div>
    </span>
</td>

<!--스타일-->
<td class="text-left pdl10 relative" style="padding-left:10px !important;" :rowspan="project.defaultRowspan">
    <div v-if="0 === project.prdCnt">
        <span class="text-muted" >스타일 파악중</span>
    </div>

    <div v-if="project.prdCnt > 0">
        <span class="" v-if="project.prdCnt > 0">{% project.styleName %}</span>

        <div class="btn btn-sm btn-white hover-btn cursor-pointer mgl10 btn-style-on" style="position: absolute; top:10px; right:2px" v-show="!project.styleShow"
             @click="showStyle(project,project.prdCnt+1)">
            <i class="fa fa-chevron-down" aria-hidden="true" style="color:#9E9E9E"></i> 상세
        </div>

        <div class="btn btn-sm btn-white hover-btn cursor-pointer mgl10 btn-style-off" style="position: absolute; top:10px; right:2px" v-show="project.styleShow"
             @click="hideStyle(project,project.prdCnt+1)">
            <i class="fa fa-chevron-up" aria-hidden="true" style="color:#9E9E9E"></i> 닫기
        </div>
        <i class="display-none fa fa-caret-square-o-up" aria-hidden="true"></i>
    </div>

    <div class="text-blue font-11" v-if="Number(project.sampleTotalCount) > 0">
        <a :href="`ims_view2.php?sno=${project.projectSno}&status=${project.projectStatus}&styleTabMode=sample`" class="text-blue"  target="_blank">
            샘플 : {% project.sampleTotalCount %}개
        </a>
    </div>
</td>

<?php if( empty($requestParam['status']) || $requestParam['status'] >= 20 ) { ?>
    <td :class="'text-center relative font-11'">
        <div v-if="'y' === project.isBookRegistered">
            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" ></i>
            <div>
                {% $.formatShortDate(project.isBookRegisteredDt) %}
            </div>
        </div>
        <div v-if="'n' === project.isBookRegistered">
            <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" ></i>
        </div>

    </td>
<?php } ?>


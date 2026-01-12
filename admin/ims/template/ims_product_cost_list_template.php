<table class="table table-rows table-default-center mgt5">
    <colgroup>
        <col style="width:50px" /><!--체크-->
        <col style="width:50px" /><!--번호-->

        <col style="width:80px" v-if="!isFactory && !isList" /><!--선택여부-->
        <col style="width:180px" v-if="isList" /><!--고객/스타일-->
        <col style="width:50px" /><!--요청회차-->
        <col style="width:80px" /><!--견적타입-->
        <col style="width:110px" /><!--진행상태-->
        <col style="width:300px"/><!--이노버 요청내용-->

        <col style="width:100px" v-if="!isFactory" /><!--의뢰처-->

        <col style="width:70px" /><!--예정수량-->
        <col style="width:100px" /><!--견적금액-->
        <col /><!--생산처 내용-->

        <col style="width:90px" /><!--요청자-->
        <col style="width:90px" /><!--처리DL/처리일-->
        <col style="width:90px" /><!--요청일-->
    </colgroup>
    <thead>
    <tr>
        <th>
            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="estimateSno">
        </th>
        <th>번호</th>
        <th v-if="!isFactory && !isList">선택여부</th>
        <th v-if="isList">고객/스타일</th>
        <th>요청번호</th>
        <th>견적타입</th>
        <th>진행상태</th>
        <th>이노버 요청 내용</th>
        <th v-if="!isFactory">의뢰처</th>
        <th>수량</th>
        <th>견적금액</th>
        <th>생산처 작성 내용</th>
        <th>요청자</th>
        <th>처리D/L<br>처리일</th>
        <th>요청일</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="(estimate , estimateIndex) in costList"
        :class="product.prdCostConfirmSno == estimate.sno || product.estimateConfirmSno == estimate.sno ?'bg-light-yellow':''">
        <td>
            <input type="checkbox" name="estimateSno[]" :value="estimate.sno" class="list-check">
        </td>
        <td>
            {% (costTotal.idx-estimateIndex) %}
            <div class="text-muted font-11">(#{% Number(estimate.sno) %})</div>
        </td>
        <td v-if="!isFactory && !isList ">

            <!-- 구 확정 기능 => 선택된 견적가를 결재를 통해서만 확정 가능하다.
            <div v-if="product.prdCostConfirmSno == estimate.sno">
                <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                <span class="sl-green">(확정)</span>
                <div class="font-11 text-muted">{% product.prdCostConfirmManagerNm %}({% $.formatShortDate(product.prdCostConfirmDt) %})</div>
            </div>
            <div v-if="product.prdCostConfirmSno != estimate.sno">
                <div class="btn btn-sm btn-blue cursor-pointer" v-show="3 == estimate.reqStatus" @click="selectCost(project, product,estimate.sno)">확정</div>
                <div class="btn btn-sm btn-blue disabled" v-show="3 != estimate.reqStatus" title="생산처에서 처리완료시 확정가능">확정</div>
            </div>
            -->
            <!--확정-->
            <div v-if="Number(product.prdCostConfirmSno) > 0">
                <div v-if="product.prdCostConfirmSno == estimate.sno">
                    <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                    <div class="sl-green">생산가확정</div>
                    <div class="font-11 text-muted">{% product.prdCostConfirmManagerNm %}({% $.formatShortDate(product.prdCostConfirmDt) %})</div>
                </div>
            </div>
            <!--확정 전-->
            <div v-if="0 >= Number(product.prdCostConfirmSno)">
                <div v-if="product.estimateConfirmSno == estimate.sno">
                    <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                    <span class="sl-green">(예정)</span>
                    <!--<div class="font-11 text-muted">{% product.estimateConfirmManagerNm %}({% $.formatShortDate(product.estimateConfirmDt) %})</div>-->
                    <div class="btn btn-sm btn-white mgt5" @click="cancelEstimate(project, product, estimate.sno)">예정가취소</div>
                </div>
                <div v-if="product.estimateConfirmSno != estimate.sno && 'cost' === estimate.estimateType">
                    <div class="btn btn-sm btn-blue cursor-pointer" v-show="3 == estimate.reqStatus" @click="selectEstimate(project, product,estimate.sno)">선택</div>
                    <div class="btn btn-sm btn-blue disabled" v-show="3 != estimate.reqStatus" title="생산처에서 처리완료시 선택가능">선택</div>
                </div>

                <div v-if="product.estimateConfirmSno != estimate.sno && 'estimate' === estimate.estimateType">
                    <div class="btn btn-sm btn-gray cursor-pointer" v-show="3 == estimate.reqStatus" @click="ImsProductService.openCostReqByBeforeEstimate(estimateIndex,'cost')">생산가요청</div>
                </div>

            </div>
        </td>
        <td class="pdl10 ta-l" v-if="isList">
            <div class="cursor-pointer hover-btn " @click="openFactoryEstimateView(estimate.projectSno, estimate.styleSno, estimate.sno, 'estimate')"  v-if="isFactory">
                <div class="dp-flex">
                    <div class="text-danger">{% estimate.projectSno %}</div>
                    <div class="font-14 bold sl-blue">{% estimate.customerName %}</div>
                </div>
                <div>
                    <div class="mgt5 bold">{% estimate.styleFullName %}</div>
                    <div class="font-11 text-muted" style="font-weight: normal">({% estimate.styleCode %})</div>
                </div>
            </div>
            <div v-if="!isFactory">
                <div>
                    <span class="text-danger cursor-pointer hover-btn" @click="openProjectView(estimate.projectSno)">{% estimate.projectSno %}</span>
                    <span class="sl-blue cursor-pointer hover-btn" @click="openCustomer(estimate.customerSno)">{% estimate.customerName %}</span>
                </div>
                <div class="hover-btn cursor-pointer bold font-14" @click="openProductReg2(estimate.projectSno, estimate.styleSno, 3)">
                    {% estimate.styleFullName %} <div class="font-11 text-muted" style="font-weight: normal">({% estimate.styleCode %})</div>
                </div>
            </div>
        </td>
        <td class="font-16 text-danger bold">
            {% estimate.reqCount %}차
        </td>
        <td>
            <div class="font-13 sl-blue" v-if="'estimate' === estimate.estimateType">
                <i class="fa fa-quora" aria-hidden="true"></i>가견적
            </div>
            <div class="font-13 sl-green" v-if="'cost' === estimate.estimateType">
                <i class="fa fa-krw" aria-hidden="true"></i>생산견적
            </div>
        </td>
        <td class="ta-c">
            <div class="ta-c" >
                <div class="font-14 bold" >
                    {% estimate.reqStatusKr %}
                </div>

                <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openFactoryEstimateView(estimate.projectSno, estimate.styleSno, estimate.sno, 'cost')">보기</div>

                <div class="hover-btn cursor-pointer btn btn-sm btn-red btn-red-line2 mgt5"
                     v-if="0 >= Number(product.prdCostConfirmSno) && 3==estimate.reqStatus && product.estimateConfirmSno != estimate.sno && !isFactory"
                     @click="ImsProductService.reRequest(estimate.sno)">재요청</div>

                <!--
                <div class="hover-btn cursor-pointer btn btn-sm btn-red btn-red-line2 mgt5" v-if="product.prdCostConfirmSno == estimate.sno && !isFactory" @click="cancelCost(project, product,estimate.sno)">취소</div>
                -->
            </div>
        </td>
        <td class="ta-l pdl10">
            <span v-html="estimate.reqMemoBr"></span>
            <ul class="mgt5">
                <li class="hover-btn" v-for="(file, fileIndex) in estimate.reqFiles">
                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                </li>
            </ul>
        </td>
        <td v-if="!isFactory">
            {% estimate.reqFactoryNm %}
        </td>
        <td class="">
            <span class="bold font-14">{% $.setNumberFormat(estimate.estimateCount) %}</span>개
        </td>
        <td class="">
            <span class="text-danger bold font-14">{% $.setNumberFormat(estimate.estimateCost) %}</span>원
        </td>
        <td class="ta-l pdl10">
            <span v-html="estimate.resMemoBr"></span>
        </td>
        <td >
            {% estimate.reqManagerNm %}
        </td>
        <td>
            <div>{% $.formatShortDate(estimate.completeDeadLineDt) %}</div>
            <div>{% $.formatShortDate(estimate.completeDt) %}</div>
        </td>
        <td>
            {% $.formatShortDate(estimate.regDt) %}

            <?php if(!empty(\SiteLabUtil\SlCommonUtil::isDevId()) || \SiteLabUtil\SlCommonUtil::isImsAdmin() ) { ?>
                <div>
                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " @click="ImsService.deleteData('estimate',estimate.sno, ImsRequestService.getListCost)">
                        삭제
                    </div>
                </div>
            <?php } ?>

            <!--
            <?php if(\Component\Ims\ImsCodeMap::AUTH_MANAGER) { ?>
            <div>
                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " @click="ImsService.deleteData('estimate',estimate.sno, ImsRequestService.getListCost)">
                    삭제
                </div>
            </div>
            <?php }else{ ?>
                <div v-if="!isFactory && 0 >=  Number(estimate.estimateCost) && 5 !== Number(estimate.reqStatus)">
                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " @click="ImsService.deleteData('estimate',estimate.sno, ImsRequestService.getListCost)" v-if="isList && product.prdCostConfirmSno !== estimate.sno">
                        삭제
                    </div>
                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " @click="ImsService.deleteData('estimate',estimate.sno, listRefresh)" v-if="!isList && product.prdCostConfirmSno !== estimate.sno">
                        삭제
                    </div>
                </div>
            <?php } ?>
            -->
        </td>
    </tr>
    <tr v-show=" 0 >= costList.length || $.isEmpty(costList.length) ">
        <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
    </tr>
    </tbody>
</table>
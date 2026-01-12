<!-- [ 스타일 ] =========================================================  -->
<div v-show="'basic' === styleTabMode">
    <table class="table table-cols" style="border-top:none">
        <colgroup>
            <col class="w-3p"><!--번호-->
            <?php foreach($prdSetupData2['list'] as $each) { ?>
                <col class="w-<?=$each[1]?>p" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th >번호</th>
            <?php foreach($prdSetupData2['list'] as $each) { ?>
                <th><?=$each[0]?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-show="!showStyle">
        <tr>
            <td colspan="99" class="center">
                <div class="btn btn-white" @click="showStyle=true">상품 보기</div>
            </td>
        </tr>
        </tbody>
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-for="(product, prdIndex) in viewProductList" v-show="showStyle">
        <tr>
            <td rowspan="2"><!--번호-->
                {% prdIndex+1 %}
                <div class="text-muted font-11">#{% product.sno %}</div>
            </td>
            <td rowspan="2"><!--이미지-->
                <span class="hover-btn cursor-pointer"  v-if="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnail)">
                    <img src="/data/commonimg/ico_noimg_75.gif" class="middle" width="40">
                </span>
                <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnail)">
                    <img :src="product.fileThumbnail" class="middle" width="60" height="60" >
                </span>
                <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail)">
                    <img :src="product.fileThumbnail" class="middle" width="60" height="60">
                </span>
            </td>
            <td class="pdl5 ta-l relative" ><!--스타일명-->
                <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                    {% (product.prdYear+'').substring(2,4) %}
                    {% product.prdSeason %}
                    {% product.productName %}
                </span>
                <br>
                <span class="text-muted">{% product.styleCode.toUpperCase() %}</span>

                <span class="font-11 text-blue cursor-pointer hover-btn" @click="openProductReg2(project.sno, product.sno, 0)" v-show="product.sampleCnt > 0">
                    샘플:{% product.sampleCnt %}
                </span>

                <!--<i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.productName)"></i>-->
                <!--<i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(product.styleCode.toUpperCase())"></i>-->

                <br>

                <div v-if="typeof workFileList[product.sno] != 'undefined'" style="display: flex" class="font-11 pdt5 text-muted">
                    작지 :
                    <ul class="ims-file-list" >
                        <li class="hover-btn" v-for="(file, fileIndex) in workFileList[product.sno].files">
                            <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                        </li>
                    </ul>
                </div>


                <div class="pd0 mgt5 relative" v-show="'off' !== product.usedEworkListShow">
                    <table class="table table-borderless pd0 table-th-height0 table-td-height0 mgb0" >
                        <?php foreach( \Component\Ims\ImsCodeMap::EWORK_TYPE as $typeKey => $type ){?>
                            <td class="font-8 text-center" style="border-top:solid 1px #f0f0f0 !important; padding:5px 0 !important;">
                                <div v-if="!$.isEmpty(product.ework.data)" class="hover-btn cursor-pointer"
                                     @click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'<?=$typeKey?>'})"
                                     style="padding:0 !important;">
                                    <?=$type?>
                                    <i aria-hidden="true" class="fa fa-check-circle text-green cursor-pointer" v-show="'p' === product.ework.data.<?=$typeKey?>Approval"></i>
                                    <i aria-hidden="true" class="fa fa-play-circle sl-blue cursor-pointer" v-show="'r' === product.ework.data.<?=$typeKey?>Approval"></i>
                                    <i aria-hidden="true" class="fa fa-stop-circle color-gray cursor-pointer" v-show="'n' === product.ework.data.<?=$typeKey?>Approval"></i>
                                </div>
                            </td>
                        <?php }?>
                    </table>

                </div>


                <div  style="position: absolute;top:5px;right:0" class="dp-flex">

                    <!--<div class="btn btn-white mgr5" style="" @click="window.open(`<?/*=$eworkUrl*/?>?sno=${product.sno}`);">
                        <span v-html="$.getStatusIcon(product.workStatus)"></span> 작지확인
                    </div>-->

                    <div class="font-11 btn btn-black-line hover-btn cursor-pointer mgr5" v-show="'off' === product.usedEworkListShow" @click="openEworkStatus(product)">
                        <span v-html="$.getStatusIcon(product.workStatus)"></span> 작지상태  <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </div>
                    <div class="font-11 btn btn-blue hover-btn cursor-pointer mgr5" v-show="'off' !== product.usedEworkListShow" @click="product.usedEworkListShow='off'">
                        <span v-html="$.getStatusIcon(product.workStatus)"></span> 작지상태 <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>

                    <!--@click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'main'})"-->
                    <!--<div class="font-11 btn btn-black-line hover-btn cursor-pointer mgr5" @click="window.open(`<?/*=$eworkUrl*/?>?sno=${product.sno}`);">
                        작업지시서 <i class="fa fa-external-link" aria-hidden="true"></i>
                    </div>-->

                    <section v-if="!$.isEmpty(product.usedFabricList) && 4 != project.projectType && product.usedFabricList.length > 0 ">
                        <div class="font-11 btn btn-black-line hover-btn cursor-pointer w-75px"
                             @click="product.usedFabricListShow = 'on'" v-show="'off' === product.usedFabricListShow" >
                            Q/B<i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </div>
                        <div class="font-11 btn btn-blue hover-btn cursor-pointer w-75px"
                             @click="product.usedFabricListShow = 'off'" v-show="'on' === product.usedFabricListShow">
                            Q/B<i class="fa fa-chevron-up" aria-hidden="true"></i>
                        </div>
                    </section>

                    <div v-if="4 != project.projectType && ( $.isEmpty(product.usedFabricList) || 0 >= product.usedFabricList.length)"
                         class="font-11 btn btn-white hover-btn cursor-pointer" @click="$.msg('관리원단 없음','','info')">
                        Q/B
                        <span class="font-10">(없음)</span>
                    </div>

                </div>

                <!--
                <div style="position: absolute;top:5px;right:0">
                    <div class="font-11 btn btn-blue-line hover-btn cursor-pointer" @click="product.usedFabricListShow = 'on'" v-show="'off' === product.usedFabricListShow">
                        아소트
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </div>
                </div>
                -->

                <!--샘플 버튼-->
                <!--<div class="ims-circle cursor-pointer hover-btn" style="position: absolute;top:33px; right:10px;">샘플:1</div>-->

                <div class="sl-green font-11" v-if="'p' === product.priceCustConfirm">
                    고객{% getCodeMap()['custEstimateStatus'][product.priceCustConfirm] %}
                    ({% product.priceApprovalName %} {% $.formatShortDateWithoutWeek(product.priceCustConfirmDt) %})
                </div>

                <!--<div v-if="!$.isEmpty(product.usedFabricList) && 4 != project.projectType && product.usedFabricList.length > 0 " style="position: absolute;top:5px;right:65px">
                    <div class="font-11 btn btn-white hover-btn cursor-pointer" @click="product.usedFabricListShow = 'on'" v-show="'off' === product.usedFabricListShow">
                        생산
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </div>
                    <div class="font-11 btn btn-white hover-btn cursor-pointer" @click="product.usedFabricListShow = 'off'" v-show="'on' === product.usedFabricListShow">
                        생산
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                </div>-->

                <div class="btn btn-white btn-sm" @click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'main'})">작업지시서</div>
                <div class="btn btn-white btn-sm" @click="window.open(`<?=$eworkUrl?>?sno=${product.sno}`);">인쇄용 작지</div>

            </td>
            <td class=""><!--제작수량-->
                <span class="">{% $.setNumberFormat(product.prdExQty) %}장</span>
            </td>
            <td class=""><!--고객납기-->
                <span class="">{% $.formatShortDate(product.customerDeliveryDt) %}</span>
            </td>
            <td class="text-left">
                <div class="font-12">현재단가: {% $.setNumberFormat(product.currentPrice) %}</div>
                <div class="font-12">타겟판매: {% $.setNumberFormat(product.targetPrice) %}</div>
                <div class="font-12">타겟생산: {% $.setNumberFormat(product.targetPrdCost) %}</div>
            </td>
            <!--<td>
                <div>임가공</div>
                <div>하나어패럴</div>
                <div>베트남</div>
            </td>-->
            <!--<td>
                110일
            </td>-->
            <td >
                <div v-if="4 != project.projectType">
                    <!--생산가 표기-->
                    <div class=" bold sl-blue" v-if="Number(product.prdCostConfirmSno) > 0">
                        <div class="hover-btn cursor-pointer"
                             @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                            {% $.setNumberFormat(product.prdCost) %}원
                        </div>
                        <div class="font-11">(확정)</div>
                    </div>

                    <!--가견적표기-->
                    <div class=" " v-if="Number(product.estimateConfirmSno) > 0 && 0 >= Number(product.prdCostConfirmSno)">
                        <div class="hover-btn cursor-pointer"
                             @click="openFactoryEstimateView(project.sno, product.sno, product.estimateConfirmSno, 'cost')">
                            {% $.setNumberFormat(product.estimateCost) %}원</div>
                        <div class="font-11">(가견적/미확정)</div>
                    </div>

                    <!--견적/생산가 없을 때-->
                    <div class="" v-if="0 >= Number(product.prdCostConfirmSno) || 0 >= Number(product.estimateConfirmSno)">
                        -
                    </div>

                    <div class="btn btn-sm btn-white cursor-pointer hover-btn" @click="openProductReg2(project.sno, product.sno, 3)">견적리스트</div>
                </div>

                <div v-if="4 == project.projectType">
                    <div class="sl-blue bold">
                        {% $.setNumberFormat(product.prdCost) %}원
                    </div>
                </div>

            </td>
            <td class="relative">
                <div class=" bold text-danger">
                    {% $.setNumberFormat(product.salePrice) %}원
                    <!--판매가 승인시-->
                    <div class="font-11 text-danger" v-if="'p' === project.prdPriceApproval">
                        (확정)
                    </div>
                </div>
            </td>
            <td ><!--마진-->
                <span class=" bold" v-if="Number(product.salePrice) > 0">
                    <!--견적마진-->
                    <span class="text-muted"v-if="Number(product.estimateConfirmSno) > 0 && 0 >= Number(product.prdCostConfirmSno)">
                        (가){%  $.setNumberFormat(100-(Math.round(product.estimateCost/product.salePrice*100))) %}%
                    </span>

                    <!--확정마진-->
                    <span v-if="Number(product.prdCostConfirmSno) > 0">
                        {%  $.setNumberFormat(100-(Math.round(product.prdCost/product.salePrice*100))) %}%
                    </span>
                </span>
            </td>
            <td class="text-center">
                <table class="w-80p">
                    <tr>
                        <td class="text-left">
                            <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, 1)">퀄리티 : </span>
                        </td>
                        <td class="text-left">
                            {% product.fabricStatusKr %}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                            <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, 1)">BT : </span>
                        </td>
                        <td class="text-left">
                            {% product.btStatusKr %}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr v-show="'on' === product.usedFabricListShow">
            <td colspan="99" class="pd0">
                <table class="table table-sub table-fabric-sub table-pd-5 table-default-center border-top-none" style="border-top:none !important;">
                    <colgroup>
                        <col class="w-80px"><!--원단번호-->
                        <col class="w-15p"><!--원단명-->
                        <col class="w-10p"><!--퀄리티상태-->
                        <col class="w-30p"><!--퀄리티확정정보-->
                        <col class="w-10p"><!--BT상태-->
                        <col class="w-30p"><!--BT확정정보-->
                    </colgroup>
                    <tr >
                        <?php $style='style="background-color:#fffde8!important;; border-top:none!important;font-family:Noto Sans KR !important"' ?>
                        <th <?=$style?>>원단번호</th>
                        <th <?=$style?> class="text-left pdl5">원단명/정보</th>
                        <th <?=$style?>>퀄리티상태</th>
                        <th <?=$style?> class="text-left pdl5">퀄리티확정정보</th>
                        <th <?=$style?>>BT상태</th>
                        <th <?=$style?> class="text-left pdl5">BT확정정보</th>
                        <!--
                        <th style="border-top:none !important; font-weight:normal !important">컬러</th>
                        <th style="border-top:none !important; font-weight:normal !important">제조국</th>-->
                    </tr>
                    <tr v-for="fabric in product.usedFabricList">
                        <td>{% fabric.sno %}</td>
                        <td class="text-left pdl5">
                                    <span @click="openProductWithFabric(project.sno, product.styleSno, fabric.sno)" class="cursor-pointer hover-btn">
                                        {% fabric.fabricName %}
                                        <i :class="'flag flag-16 flag-'+ fabric.makeNational" v-if="!$.isEmpty(fabric.makeNational)" ></i>
                                    </span>
                            <div>
                                {% fabric.position %} {% fabric.attached %}
                                {% fabric.fabricMix %} {% fabric.color %}
                            </div>
                        </td>
                        <td>{% fabric.fabricStatusKr %}</td>
                        <td class="text-left pdl5">
                                    <span @click="openProductWithFabric(project.sno, product.styleSno, fabric.sno)" class="cursor-pointer hover-btn">
                                        {% fabric.fabricConfirmInfo %}
                                    </span>
                            <div class="text-muted">
                                {% fabric.fabricMemo %}
                            </div>
                        </td>
                        <td>
                                    <span @click="openProductWithFabric(project.sno, product.styleSno, fabric.sno)" class="cursor-pointer hover-btn">
                                        {% fabric.btStatusKr %}
                                    </span>
                        </td>
                        <td class="text-left pdl5">
                                    <span @click="openProductWithFabric(project.sno, product.styleSno, fabric.sno)" class="cursor-pointer hover-btn">
                                    {% fabric.btConfirmInfo %}
                                    </span>
                            <div class="text-muted">
                                {% fabric.btMemo %}
                            </div>
                            <!--BT파일-->
                            <li class="hover-btn" v-for="(file, fileIndex) in fabric.fileList.btFile2.files">
                                <a :href="'<?=$nasDownloadUrl?>name='+file.fileName+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                            </li>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        </tbody>
    </table>

</div>
<div class="row ">
    <div class="col-xs-12" >

        <div class="table-title gd-help-manual">
            <div class="flo-left">스타일 정보</div>
            <div class="flo-right"></div>
        </div>
        <div id="tabOrderStatus ">
            <div class="tab-content">
                <div class="table-action" style="margin-bottom: 0px !important; border-top:solid 1px #888888">
                    <div class="pull-right form-inline" style="height: 26px;">
                        <div class="display-inline-block"></div>
                    </div>
                </div>

                <div role="tab-status-order" class="tab-pane in active" id="tab-status-order">
                    <div id="layer-wrap">
                        <div id="inc_order_view" class="table-responsive">
                            <table class="table table-rows">
                                <colgroup>
                                    <col style="width:2%"  /><!--번호-->
                                    <col style="width:6%"  /><!--이미지-->
                                    <col style="width:15%"  /><!--스타일-->
                                    <col style="width:15%"  /><!--스타일코드-->
                                    <col style="width:6%"  /><!--제작수량-->
                                    <col  /><!--원단정보-->
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>번호</th>
                                    <th>이미지</th>
                                    <th>스타일명</th>
                                    <th>스타일코드</th>
                                    <th>제작수량</th>
                                    <th>원단정보</th>
                                </tr></thead>
                                <tbody>
                                <tr class="text-center" v-for="(prd, prdIndex) in productList">
                                    <td>{% prdIndex+1 %}</td>
                                    <td>
                                        <span class="hover-btn "  >
                                            <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(prd.fileThumbnail)" class="middle" width="35">
                                            <img :src="prd.fileThumbnail" v-show="!$.isEmpty(prd.fileThumbnail)" class="middle" width="35">
                                        </span>
                                    </td>
                                    <td><!--스타일명-->
                                        <?php if($isProduceCompany) { ?>
                                            <span class="font-16 text-blue  " >{% prd.productName %}</span>
                                        <?php }else{ ?>
                                                <!--
                                            <span class="font-16 text-blue cursor-pointer hover-btn " @click="openProductReg(project.sno, prd.sno)">{% prd.productName %}</span>
                                            -->
                                            <span class="font-16 text-blue  " >{% prd.productName %}</span>
                                        <?php } ?>
                                    </td>
                                    <td style="padding-left:10px; text-align: left"><!--스타일코드-->
                                        <span class="font-16">{% prd.styleCode.toUpperCase() %}</span>
                                    </td>
                                    <td class="font-16"><!--제작수량-->
                                        {% $.setNumberFormat(prd.prdExQty) %}장
                                    </td>
                                    <td class="text-left font-15" style="padding:0;">
                                        <table class="ims-request-style-table table table-rows table-default-center">
                                            <colgroup>
                                                <col style="width:5%" />
                                                <col style="width:25%" />
                                                <col style="width:15%" />
                                                <col style="width:10%" />
                                                <col style="width:10%" />
                                                <col  />
                                            </colgroup>
                                            <tr v-for="prdFabric in prd.fabric" v-if="!$.isEmpty(prdFabric.fabricName) && !$.isEmpty(prdFabric.color) && !$.isEmpty(prdFabric.btConfirm)" class="ims-request-style-table-tr" >
                                                <td>{% prdFabric.no %}</td>
                                                <td>{% prdFabric.fabricName %}</td>
                                                <td>{% prdFabric.color %}</td>
                                                <td v-html="prdFabric.btConfirmKrHtml"></td>
                                                <td>{% prdFabric.btConfirmDt %}</td>
                                                <td class="text-left pdl5">{% prdFabric.btMemo %}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $modelPrefix='order';  ?>
<!-- 고객사 정보 -->
<div class="col-xs-12 mgt20">

    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">발주 기본 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a>
        </div>
        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30">
                <colgroup>
                    <col class="width-xs">
                    <col class="width-md">
                    <col class="width-xs">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr>
                    <th>고객명</th>
                    <td colspan="3">
                        <div class="dp-flex">
                            <?php $model='customer.customerName'; $placeholder='고객명' ?>
                            <?php include 'basic_view/_text.php'?>
                            <div class="btn btn-white btn-sm mgl5" @click="openCustomer(customer.sno)">상세</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>3PL</th>
                    <td >
                        <div class="dp-flex dp-flex-gap10">
                            <div v-show="!isModify">
                                {% project.use3plKr %}
                            </div>
                            <div v-show="isModify">
                                <div>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="orderUse3pl" value="n"  v-model="project.use3pl" />사용안함
                                    </label>
                                    <label class="radio-inline font-11">
                                        <input type="radio" name="orderUse3pl" value="y"  v-model="project.use3pl" />사용
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <th>폐쇄몰</th>
                    <td >
                        <div class="dp-flex">
                            <div v-show="!isModify">
                                {% project.useMallKr %}
                            </div>
                            <div v-show="isModify">
                                <label class="radio-inline font-11">
                                    <input type="radio" name="orderUseMall" value="n"  v-model="project.useMall" />사용안함
                                </label>
                                <label class="radio-inline font-11">
                                    <input type="radio" name="orderUseMall" value="y"  v-model="project.useMall" />사용
                                </label>
                            </div>
                            <div class="btn btn-white btn-sm mgl10" v-show="'y' === customer.useMall" @click="openCustomer(customer.sno,'mall')">폐쇄몰 개설 정보</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>고객납기</th>
                    <td >
                        <?php $model='project.customerDeliveryDt';?>
                        <?php include 'basic_view/_picker2.php'?>

                        <div v-if="!isModify" class="">
                            <div class="text-danger " v-if="'y' !== project.customerDeliveryDtConfirmed">변경불가</div>
                            <div class="sl-blue " v-if="'n' !== project.customerDeliveryDtConfirmed">변경가능</div>
                        </div>
                        <div v-if="isModify" class="dp-flex">
                            <div class="">
                                <label class="radio-inline">
                                    <input type="radio" name="order_deliveryConfirm"  value="y" v-model="project.customerDeliveryDtConfirmed"/>변경가능
                                </label>
                            </div>
                            <div>
                                <label class="radio-inline">
                                    <input type="radio" name="order_deliveryConfirm"  value="n" v-model="project.customerDeliveryDtConfirmed"/>변경불가
                                </label>
                            </div>
                        </div>

                    </td>
                    <th rowspan="2">
                        납기일 연동 여부
                    </th>
                    <td rowspan="2">
                        <div v-show="!isModify">
                            <div v-show="'y' === project.syncProduct">
                                납기일자 프로젝트로 한번에 관리
                                <div class="notice-info">상품 납기일이 프로젝트 수정 시 해당 납기일로 변경됩니다.</div>
                            </div>
                            <div v-show="'n' === project.syncProduct">
                                납기일자 상품별 관리
                                <div class="notice-info">프로젝트 납기일과 상관없이 상품별 관리 합니다.</div>
                            </div>
                        </div>
                        <div v-show="isModify">
                            <label class="radio-inline" style="font-weight: normal;font-size:12px">
                                <input type="radio" name="orderSyncProduct"  value="y" v-model="project.syncProduct"/> 납기일자 프로젝트로 한번에 관리
                            </label>
                            <br>
                            <label class="radio-inline" style="font-weight: normal;font-size:12px">
                                <input type="radio" name="orderSyncProduct"  value="n" v-model="project.syncProduct"/> 납기일자 상품별 관리
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>이노버납기</th>
                    <td >
                        <?php $model='project.msDeliveryDt';?>
                        <?php include 'basic_view/_picker2.php'?>
                    </td>
                </tr>
                <tr>
                    <th>발주D/L</th>
                    <td colspan="3">
                        <?php $model='project.customerOrderDeadLine';?>
                        <?php include 'basic_view/_picker2.php'?>
                    </td>
                </tr>
                <tr>
                    <th>발주일</th>
                    <td colspan="3">
                        <div class="" v-show="project.productionStatus > 0">
                            {% $.formatShortDate(project.msOrderDt) %} 발주 ( 생산{% project.productionStatusKr %}상태 )
                        </div>
                        <div class="btn btn-blue" @click="orderToFactory()" v-show="0 >= project.productionStatus">
                            발주하기
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        발주조건
                    </th>
                    <td colspan="99" class="pd0">

                        <table class="table-fixed w-100p table-center">
                            <tr>
                                <th>판매가</th>
                                <th>생산가</th>
                                <th>아소트</th>
                                <th>작업지시서</th>
                                <th>사양서</th>
                            </tr>
                            <tr>
                                <td><!--판매가-->
                                    <span v-html="project.priceStatusIcon"></span>
                                </td>
                                <td><!--생산가-->
                                    <span v-html="project.costStatusIcon"></span>
                                </td>
                                <td><!--아소트-->
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == project.assortApproval"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == project.assortApproval"></i>
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-else></i>
                                </td>
                                <td>
                                    <span v-html="project.workStatusIcon"></span>
                                </td>
                                <td>
                                    <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'p' == project.customerOrderConfirm"></i>
                                    <i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" v-else-if="'r' == project.customerOrderConfirm"></i>
                                    <i class="fa fa-lg fa-stop-circle color-gray" aria-hidden="true" v-else></i>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-6" id="layoutOrderViewOrderInfoArea">
        <div class="table-title gd-help-manual">
            <div class="flo-left">발주/납품 정보</div>
            <div class="flo-right">
                <button type="button" class="btn btn-red btn-sm" v-show="!isModify" @click="setModify(true)">수정</button>
                <button type="button" class="btn btn-red-box btn-sm"  v-show="isModify" @click="save()">저장</button>
                <button type="button" class="btn btn-white btn-sm"  v-show="isModify" @click="setModify(false)">취소</button>
            </div>
            <a href="#" target="_blank" class=""></a></div>

        <div >
            <table class="table table-cols ims-table-style1 table-td-height30 table-th-height30">
                <colgroup>
                    <col class="width-md">
                    <col>
                    <col class="width-md">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>대표 생산처</th>
                    <td colspan="3">

                        <div v-show="!isModify">
                            <div v-if="!$.isEmpty(project.mainFactoryName)">{% project.mainFactoryName %}</div>
                            <div v-else>미정</div>
                        </div>

                        <select class="form-control" style="width:30%" v-model="project.produceCompanySno" v-show="isModify">
                            <option value="0">미정</option>
                            <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select>

                        <div class="notice-info">대표생산처 지정시 스타일별 지정된 스타일이 없을 경우 대표생산처로 자동 지정</div>
                        
                    </td>
                </tr>
                <tr>
                    <th>생산처 형태/국가</th>
                    <td colspan="3">

                        <div class="form-inline" v-show="!isModify">
                            {% project.produceTypeKr %}
                            {% project.produceNational %}
                        </div>

                        <div class="form-inline" v-show="isModify">
                            <select class="form-control " v-model="project.produceType">
                                <?php foreach ($prdType as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" v-model="project.produceNational" placeholder="선택">
                                <option value="">미정</option>
                                <?php foreach ($prdNational as $key => $value ) { ?>
                                    <option value="<?=$value?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </td>
                </tr>
                <tr>
                    <th>3PL 바코드 파일</th>
                    <td colspan="3">
                        <simple-file-upload :file="fileList.fileBarcode" :id="'fileBarcode'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                <tr>
                    <th>분류패킹 여부/파일</th>
                    <td colspan="3">
                        <?php $model = 'project.packingYn'; $listCode = 'processType'; $modelPrefix='order'; $listIndexData="";?>
                        <?php include 'basic_view/_radio.php'?>

                        <div v-show="'y' === project.packingYn" >
                            <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="project" ></simple-file-upload>
                        </div>

                    </td>
                </tr>
                <tr>
                    <th>납품계획 메모</th>
                    <td colspan="99">

                        <textarea class="form-control w50 inline-block flo-left" rows="4" v-model="project.deliveryMethod" placeholder="납품 계획/방법 메모"></textarea>

                        <div class="flo-right">
                            <simple-file-upload :file="fileList.fileDeliveryPlan" :id="'fileDeliveryPlan'" :project="project" ></simple-file-upload>
                            <div class="notice-info">납품 계획 파일</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>납품 보고서</th>
                    <td colspan="99">
                        <simple-file-upload :file="fileList.fileDeliveryReport" :id="'fileDeliveryReport'" :project="project" ></simple-file-upload>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!--상품정보-->
<div class="col-xs-12 ">
    <div class="col-xs-12 js-order-view-receiver-area">
        <div class="dp-flex dp-flex-between mgb5 relative">
            <div>
                <span class="fnt-godo font-18">
                    발주상품
                </span>
            </div>

            <div class="" style="position: absolute; top:0; right:0">
                <div class="mgl10 dp-flex dp-flex-gap5" >

                    <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=false" v-show="showStyle">
                        <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 숨기기
                    </div>
                    <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=true" v-show="!showStyle">
                        <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 상품 보기
                    </div>

                    <div class="btn btn-red  " @click="saveStyleList(true)" v-show="isStyleModify">저장</div>
                    <div class="btn btn-white  w-50px" @click="isStyleModify=false" v-show="isStyleModify">취소</div>
                    <div class="btn btn-red  btn-red-line2" v-show="!isStyleModify" @click="isStyleModify=true">&nbsp;&nbsp;수정&nbsp;&nbsp;</div>

                    <button type="button" class="btn btn-red-box  js-receiverInfoBtnSave js-orderViewInfoSave display-none" >저장</button>
                </div>
            </div>

        </div>
        <div class="js-layout-order-view-receiver-info new-style2" v-show="showStyle">
            <?php include 'style/type_order.php'?>
        </div>
        <div class="js-layout-order-view-receiver-info new-style2" v-show="!showStyle">
            <table class="table ">
                <td class="ta-c">상품 숨김</td>
            </table>
        </div>
    </div>
</div>


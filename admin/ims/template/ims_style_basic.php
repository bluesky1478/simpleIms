<div class="relative overflow-hidden mgt10">

    <div :class="product.sno > 0?'col-xs-7':'col-xs-12'">

        <!--기본 정보-->
        <div class="row ">
            <div class="table-title gd-help-manual ">
                <div class="flo-left lineR ">기본정보</div>
                <div class="flo-right"></div>
            </div>

            <table class="table-fixed_ w-full text-left  tail-wind-table">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>

                <tbody class="bg-white text-gray-500" style="background-color: #FFFFFF; color: #6b7280;">
                <tr>
                    <th class="py-3 border text-center  p-3" rowspan="6">
                        썸네일
                        <br>
                        <div class="btn btn-sm btn-white" v-show="!$.isEmpty(product.fileThumbnail)"
                             @click="deleteThumbnail(product)">썸네일 삭제</div>
                    </th>
                    <td class="py-3 border text-center  p-3" rowspan="6">
                        <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(product.fileThumbnail)" class="w-100p">
                        <img :src="product.fileThumbnail" @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')"
                             v-show="!$.isEmpty(product.fileThumbnail)" class="w-100p cursor-pointer hover-btn">
                    </td>
                    <th class=" py-3 border text-center  p-3"><b class="text-danger">* 스타일(필수)</b></th>
                    <td class=" py-3 border text-left  p-3">
                        <select2 id="sel-style" class="js-example-basic-single" v-model="product.prdStyle" style="width:100%;" @change="setStyleCode(product,items.styleCode); setStyleName(product)">
                            <?php use Component\Ims\ImsCodeMap;

                            foreach($codeStyle as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>"><?=$codeValue?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3">생산년도</th>
                    <td class=" py-3 border text-left  p-3">
                        <select2 class="js-example-basic-single" v-model="product.prdYear" style="width:100%" @change="setStyleCode(product,items.styleCode)">
                            <?php foreach($yearList as $codeValue) { ?>
                                <option value="20<?=$codeValue?>">20<?=$codeValue?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3"><b class="text-danger">* 시즌(필수)</b></th>
                    <td class=" py-3 border text-left  p-3">
                        <select2 class="js-example-basic-single" v-model="product.prdSeason" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                            <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3">남/여</th>
                    <td class=" py-3 border text-left  p-3">
                        <select class="form-control" v-model="product.prdGender" @change="setStyleCode(product,items.styleCode)">
                            <option value="">공용</option>
                            <?php foreach($codeGender as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>"><?=$codeValue?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3">색상</th>
                    <td class=" py-3 border text-left  p-3">
                        <select2 class="js-example-basic-single" v-model="product.prdColor" style="width:70%" @change="setStyleCode(product,items.styleCode)" >
                            <?php foreach($codeColor as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                            <?php } ?>
                        </select2>
                        <div class="btn btn-white" @click="()=>{product.prdColor='';setStyleCode(product,items.styleCode);}">미지정</div>
                    </td>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3">보조코드</th>
                    <td class=" py-3 border text-left  p-3">
                        <input type="text" class="form-control w-100p" placeholder="보조코드" v-model="product.addStyleCode" @change="setStyleCode(product,items.styleCode)" @keyup="setStyleCode(product,items.styleCode)">
                    </td>
                </tr>
                <tr class=" py-3">
                    <td class=" py-3 border text-center  p-3" colspan="2">
                        <!--썸네일 업로드-->
                        <div class="text-right ims-product-image">
                            <form @submit.prevent="uploadFile">
                                <input :type="'file'" ref="fileThumbnail" style="width:1px!important;" />
                                <input type="button" class="btn btn-black" value="업로드" @click="uploadFile(product,'fileThumbnail')"  />
                            </form>
                        </div>
                    </td>
                    <th class="py-3 border text-center  p-3"><b class="text-danger">* 제품명(필수)</b></th>
                    <td class="py-3 border text-left  p-3">
                        <div>
                            <input type="text" class="form-control w-100p font-13" placeholder="제품명" v-model="product.productName" >
                        </div>
                        <div class="mgt5">
                            <span class="font-16 bold">{% product.styleCode %}</span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="mgt5 font-16 text-center">
                * 제품명은 고객 사양서에 표시되니 신중하게 작성 바랍니다.
            </div>

        </div>

        <!--가격/수량 정보-->
        <div class="row ">
            <div class="table-title gd-help-manual mgt20">
                <div class="flo-left lineR">가격/수량 정보</div>
                <div class="flo-right"></div>
            </div>

            <table class="table-fixed_ w-full text-left tail-wind-table">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody class="bg-white text-gray-500" style="background-color: #FFFFFF; color: #6b7280;">
                <tr class=" py-3">
                    <th class=" py-3 border text-center p-3 text-danger"><b>판매가</b></th>
                    <td class=" py-3 border text-left p-3 ">
                        <!--판매가-->
                        <div v-if="'p' == project.prdPriceApproval" class="text-danger font-14">
                            {% $.setNumberFormat(product.salePrice) %}원
                        </div>
                        <div v-if="'p' != project.prdPriceApproval">
                            <input type="number" class="form-control w150p inline-block text-danger font-14" placeholder="판매가격" v-model="product.salePrice"> 원
                        </div>

                        <div class=" text-muted" v-show="product.estimateCost > 0 && 0 >= product.prdCost ">
                            가견적 대비 마진:{% getMargin(product.estimateCost, product.salePrice) %}%
                        </div>
                        <div class=" text-muted" v-show="product.prdCost > 0 ">
                            <span v-if="4 != project.projectType">생산 확정가 대비</span>
                            마진:{% getMargin(product.prdCost, product.salePrice) %}%
                        </div>
                    </td>
                    <th class=" py-3 border text-center p-3">수량</th>
                    <td class=" py-3 border text-left p-3">

                        <div v-if="$.isEmpty(product.productionInfo)">
                            <input type="number" class="form-control  ims-number" placeholder="수량" v-model="product.prdExQty"> <span class="">장</span>
                        </div>
                        <div v-else>
                            {% $.setNumberFormat(product.prdExQty) %}장 (발주완료 수정불가)
                        </div>

                    </td>
                </tr>

                <tr class=" py-3" v-show="product.sno > 0">
                    <th class=" py-3 border text-center  p-3">
                        <span v-if="4 == project.projectType" class="sl-blue">기성 매입가</span>
                        <span v-if="4 != project.projectType" class="sl-blue bold">생산가</span>
                    </th>
                    <td class=" py-3 border text-left p-3">
                        <!--기성복-->
                        <span v-if="4 == project.projectType" class="sl-blue font-14">
                        <div v-if="'p' !== project.prdCostApproval" class="">
                            <input type="number" class="form-control sl-blue " v-model="product.prdCost" @keyup="()=>{product.estimateCost = product.prdCost}">
                        </div>
                        <div v-if="'p' === project.prdCostApproval" class="sl-blue">
                            {% $.setNumberFormat(product.prdCost) %}원
                        </div>
                    </span>

                        <!--기타-->
                        <span v-if="4 != project.projectType" class="sl-blue font-14">
                        {% $.setNumberFormat(product.prdCost) %}원
                        <div class="font-11 text-muted" v-show="!$.isEmpty(product.prdCostConfirmManagerNm)">
                            {% product.prdCostConfirmManagerNm %} 확정, {% $.setNumberFormat(product.prdCount) %}ea이상 발주시
                            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openFactoryEstimateView(product.projectSno, product.sno, product.prdCostConfirmSno, 'cost')">확정정보</div>
                        </div>
                    </span>
                    </td>

                    <th class=" py-3 border text-center  p-3">
                        생산 가견적
                    </th>
                    <td class=" py-3 border text-left p-3">
                        {% $.setNumberFormat(product.estimateCost) %}원
                        <div class="font-11 text-muted" v-show="!$.isEmpty(product.estimateConfirmManagerNm)">
                            {% product.estimateConfirmManagerNm %}, {% $.setNumberFormat(product.estimateCount) %}ea이상 발주시
                            <div class="hover-btn cursor-pointer btn btn-sm btn-white mgt5" @click="openFactoryEstimateView(product.projectSno, product.sno, product.estimateConfirmSno, 'estimate')">견적정보</div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!--영업정보-->
        <div class="row ">
            <!--영업정보-->
            <div class="table-title gd-help-manual mgt20">
                <div class="flo-left lineR">영업정보</div>
                <div class="flo-right"></div>
            </div>

            <table class="table-fixed_ w-full text-left tail-wind-table">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody class="bg-white text-gray-500" style="background-color: #FFFFFF; color: #6b7280;">
                <tr class=" py-3">
                    <th class=" py-3 border text-center p-3">타겟 단가</th>
                    <td class=" py-3 border text-left p-3">
                        <input type="number" class="form-control ims-number" placeholder="타겟 단가" v-model="product.targetPrice"> 원
                    </td>
                    <th class=" py-3 border text-center p-3" colspan="2">메모</th>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3">타겟 생산가</th>
                    <td class=" py-3 border text-left  p-3">
                        <input type="number" class="form-control  ims-number" placeholder="타겟생산가" v-model="product.targetPrdCost"> 원
                        <span class="font-13">({% setMargin(product.targetPrice, product.targetPrdCost) %}%)</span>
                    </td>
                    <td class=" py-3 border text-left p-3" rowspan="2" colspan="2">
                        <textarea class="form-control w100" rows="4"  v-model="product.memo" ></textarea>
                    </td>
                </tr>
                <tr class=" py-3">
                    <th class=" py-3 border text-center  p-3">현재단가</th>
                    <td class=" py-3 border text-left  p-3">
                        <input type="number" class="form-control  ims-number" placeholder="현재 단가" v-model="product.currentPrice"> 원
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>


    <div class="col-xs-5" v-if="product.sno > 0" >
        <div class="row pdl10">

            <div class="">
                <table class="table-fixed w-full text-left  tail-wind-table">
                    <colgroup>
                        <col class="w-100px">
                        <col class="">
                    </colgroup>
                    <tbody>
                        <td>
                            <div class="font-19 lineR">
                                제작 생산처
                            </div>
                        </td>
                        <td>
                            <select class="form-control"
                                    v-model="product.produceCompanySno">
                                <option value="0">미정</option>
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tbody>
                </table>
            </div>


            <!--작업지시서 상태-->
            <div class="mgt15">
                <div class="table-title gd-help-manual">
                    <div class="lineR font-18">
                        작업지시서 제작 정보
                    </div>
                </div>
            </div>
            <div>
                <div >
                    <div v-if="'y' === product.isWorkModify || 90 == project.projectStatus || 91 == project.projectStatus ">
                        <table class="table-fixed w-full text-left  tail-wind-table ">
                            <colgroup>
                                <col class="w-15p" />
                                <col class="w-85p" />
                            </colgroup>
                            <tbody >
                            <tr >
                                <td colspan="99" class="pd5">
                                    <div class="dp-flex dp-flex-gap5">
                                        <button class="badge-button gray-button" @click="window.open(`<?=$eworkUrl?>?sno=${product.sno}`);">
                                            전체
                                        </button>
                                        <?php foreach($eworkType1 as $eworkTypeKey => $eworkTypeValue) { ?>
                                            <button :title="'<?=$eworkTypeValue?>상태:' + $.getAcceptName2(ework.data.mainApproval)['name']"  :class="'badge-button '+$.getAcceptName2(ework.data.mainApproval)['bgColor']"
                                                    @click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'<?=$eworkTypeKey?>'})">
                                                <?=$eworkTypeValue?>
                                            </button>
                                        <?php } ?>
                                        <?php foreach($eworkType2 as $eworkTypeKey => $eworkTypeValue) { ?>
                                            <button :title="'<?=$eworkTypeValue?>상태:' + $.getAcceptName2(ework.data.mainApproval)['name']" :class="'badge-button '+$.getAcceptName2(ework.data.mainApproval)['bgColor']"
                                                    @click="openCommonPopup('ework', 1300, 850, {sno:product.sno, tabMode:'<?=$eworkTypeKey?>'})">
                                                <?=$eworkTypeValue?>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!$.isEmpty(product.file.fileWork) && product.file.fileWork.length > 0">
                                <td>
                                    업로드작지
                                </td>
                                <td class="">
                                    <!--프로젝트 단위 작업지시서 (23년 구버전)-->
                                    <simple-file-only-history-upload
                                            :file="product.file.fileWork"
                                            :params="product"
                                            :file_div="'fileWork'"
                                            class="font-11">
                                    </simple-file-only-history-upload>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>

                        <div class="btn btn-gray btn-sm" @click="visibleWorkOrderPossibleStatus=true">
                            작지등록불가<br>(사유확인)
                        </div>

                        <ims-modal :visible.sync="visibleWorkOrderPossibleStatus" title="작지 등록/수정 조건">
                            <div>
                                * 아래 조건이 충족되어야 작지 등록/수정 가능.
                                <table class="w-100p mgt10">
                                    <colgroup>
                                        <col class="w-25p">
                                        <col class="w-25p">
                                        <col class="w-25p">
                                        <col class="w-25p">
                                    </colgroup>
                                    <tr>
                                        <th>샘플지시서</th>
                                        <th>샘플리뷰서</th>
                                        <th>샘플확정서</th>
                                        <th>고객 샘플확정</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === product.sampleFile1Exsists"></i>
                                            <span class="text-muted" v-else>-</span> <!--샘플지시서-->
                                        </td>
                                        <td>
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === product.sampleFile4Exsists"></i>
                                            <span class="text-muted" v-else>-</span> <!--샘플리뷰서-->
                                        </td>
                                        <td>
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="'y' === product.sampleFile6Exsists"></i>
                                            <span class="text-muted" v-else>-</span> <!--샘플확정서-->
                                        </td>
                                        <td>
                                            <i class="fa fa-lg fa-check-circle text-green" aria-hidden="true" v-if="product.sampleConfirmSno > 0"></i>
                                            <span class="text-muted" v-else>-</span> <!--샘플확정상태-->
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!--</div>-->
                            <template #footer>
                                <div class="btn btn-white mgt5" @click="visibleWorkOrderPossibleStatus=false">닫기</div>
                            </template>
                        </ims-modal>

                    </div>
                </div>
                <div v-else class="bold sl-blue font-14">
                    샘플을 확정해주세요 (샘플 확정시 작지 등록/수정 가능)
                </div>
            </div>
            <div class="mgt20">
                <div class="table-title gd-help-manual ">
                    <div class="flo-left lineR ">기초설정</div>
                    <div class="flo-right font-11">

                        <?php if( '02001002' === $teamSno || \SiteLabUtil\SlCommonUtil::isDevId() ) { ?>
                        <span >기존 설정 불러오기 :</span>
                        <select2  class="js-example-basic-single" id="sel-default-style-config" style="width:350px" @change="setDefaultBasicConfig($(this))">
                            <?php foreach( $defaultStyleSettingDataPrd as $defaultStyleKey => $defaultStyleData ) { ?>
                            <option value="<?=$defaultStyleKey?>"><?=$defaultStyleData?></option>
                            <?php } ?>
                        </select2>
                        <?php } ?>

                    </div>
                </div>

                <table class="table-fixed w-full text-left  tail-wind-table">
                    <colgroup>
                        <col class="w-70px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr v-if="4 == project.projectType">
                        <td>
                            제작사이즈
                        </td>
                        <td >
                            <input type="text" class="form-control " v-model="product.sizeSpec.specRange" placeholder="제작사이즈(컴마구분)">
                            <div class="mgt5">
                                <div class="btn btn-sm btn-white" @click="product.sizeSpec.specRange = '90,95,100,105,110,115,120'; product.sizeSpec.standard='100'">상의표준</div>
                                <div class="btn btn-sm btn-white" @click="product.sizeSpec.specRange = '26,28,30,32,34,36,38,40'; product.sizeSpec.standard='32'">하의표준</div>
                            </div>
                        </td>
                    </tr>
                    <?php if( '02001002' === $teamSno ||  in_array(\Session::get('manager.managerId'), ImsCodeMap::IMS_ADMIN) ) { ?>
                    <tr v-if="4 != project.projectType">
                        <td>
                            제작사이즈
                        </td>
                        <td >
                            <input type="text" class="form-control " v-model="product.sizeSpec.specRange" placeholder="제작사이즈(컴마구분)">
                            <div class="mgt5">
                                <div class="btn btn-sm btn-white" @click="product.sizeSpec.specRange = '90,95,100,105,110,115,120'; product.sizeSpec.standard='100'">상의표준</div>
                                <div class="btn btn-sm btn-white" @click="product.sizeSpec.specRange = '26,28,30,32,34,36,38,40'; product.sizeSpec.standard='32'">하의표준</div>
                            </div>
                        </td>
                    </tr>
                    <tr >
                        <td>
                            기준사이즈
                        </td>
                        <td >
                            <input type="text" class="form-control" v-model="product.sizeSpec.standard" placeholder="기준사이즈(반드시 제작사이즈 중의 문자와 동일하게 설정)">
                        </td>
                    </tr>
                    <tr>
                        <td class="">측정항목<br>
                            <span class="font-11 text-muted">(측정단위)</span>
                        </td>
                        <td>
                            <div class="mgb5" v-for="(specInfo , specInfoIndex ) in product.sizeSpec.specData">

                                <div class="dp-flex">

                                    <input type="text" class="form-control w-40p inline-block" v-model="specInfo.title" placeholder="측정항목">

                                    <div class="bg-light-yellow border-radius-10 font-11">
                                        <label class="radio-inline  mgl15">
                                            <input type="radio" :name="'unit_'+specInfoIndex" value="CM" v-model="specInfo.unit">CM
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" :name="'unit_'+specInfoIndex" value="IN" v-model="specInfo.unit"/>IN
                                        </label>
                                    </div>

                                    <div class="bg-light-blue border-radius-10 font-11">
                                        <label class="radio-inline  mgl15">
                                            <input type="radio" :name="'share_'+specInfoIndex" value="y" v-model="specInfo.share">공개
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" :name="'share_'+specInfoIndex" value="n" v-model="specInfo.share"/>비공개
                                        </label>
                                    </div>

                                    <i class="fa fa-plus fa-lg cursor-pointer hover-btn mgl15" @click="addElementAfterAction(product.sizeSpec.specData, product.sizeSpec.specData[0], 'down', specInfoIndex, (obj)=>{obj.unit='CM'})"></i>
                                    <i class="fa fa-trash-o fa-lg cursor-pointer hover-btn" @click="deleteElement(product.sizeSpec.specData, specInfoIndex)"></i>

                                </div>

                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <table class="table-fixed w-full text-left  tail-wind-table">
                    <colgroup>
                        <col class="w-70px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>
                            (상품별)<br>고객납기일
                        </td>
                        <td >
                            <date-picker v-model="product.customerDeliveryDt"
                                         value-type="format"
                                         format="YYYY-MM-DD"
                                         :lang="lang"
                                         :editable="false"  placeholder="고개 납기일" style="width:140px;font-weight: normal; ">
                            </date-picker>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            (상품별)<br>MS납기일
                        </td>
                        <td >
                            <date-picker v-model="product.msDeliveryDt"
                                         value-type="format"
                                         format="YYYY-MM-DD"
                                         :lang="lang"
                                         :editable="false"  placeholder="이노버 납기일" style="width:140px;font-weight: normal; ">
                            </date-picker>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            프로젝트 단위 이노버 납기일 :
                            <a href="#" @click="product.msDeliveryDt=project.msDeliveryDt" class="sl-blue">{% project.msDeliveryDt %}</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            프로젝트 단위 고객 희망 납기일
                            <span  class="sl-blue">{% project.customerDeliveryDt %}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>


            <!--샘플리스트-->
            <div class="mgt20">
                <div class="table-title gd-help-manual">
                    <div class="flo-left lineR ">
                        샘플 리스트
                        <div class="btn btn-white btn-sm noto mgl10">&nbsp;&nbsp;추가&nbsp;&nbsp;</div>
                    </div>
                    <div class="flo-right"></div>
                </div>

                <table class="table-fixed w-full text-left  tail-wind-table mgt40">
                    <tbody v-if="sampleList.length > 0">
                    <tr v-for="(sample, sampleIdx) in sampleList" >
                        <td>
                            <span class="mgr5 cursor-pointer hover-btn sl-blue">{% sampleList.length - sampleIdx %}. {% sample.sampleName %}</span>
                            <span class="font-11">
                                ({% sample.factoryName %} / {% sample.sampleManagerNm %} {% sample.regDt %} 등록)
                            </span>
                        </td>
                    </tr>
                    </tbody>

                    <tbody v-if="$.isEmpty(sampleList) || 0 >= sampleList.length">
                    <tr>
                        <td>샘플 없음</td>
                    </tr>
                    </tbody>

                </table>
            </div>
            
            <!--유사리스트-->
            <div class="mgt20">
                <div class="table-title gd-help-manual">
                    <div class="flo-left lineR ">
                        유사/과거 스타일 정보
                    </div>
                    <div class="flo-right"></div>
                </div>

                <table class="table-fixed w-full text-left  tail-wind-table mgt40">
                    <tbody v-if="relatedList.length > 0">
                    <tr v-for="(related, relatedIdx) in relatedList" >
                        <td>
                            <span class="mgr5 cursor-pointer hover-btn sl-blue" @click="openProductReg2(related.projectSno, related.sno, -1)">
                                {% relatedList.length - relatedIdx %}. {% related.productName %}
                            </span>
                            <span class="font-11">
                                ({% related.styleCode %} . {% related.sno %})
                            </span>
                            <span>
                                <i aria-hidden="true"
                                   class="fa fa-files-o text-muted cursor-pointer hover-btn"
                                   @click="ImsProductService.copyStyleBasicInfo(related, product)"
                                >
                                </i>
                            </span>
                        </td>
                    </tr>
                    </tbody>

                    <tbody v-if="$.isEmpty(relatedList) || 0 >= relatedList.length">
                    <tr>
                        <td>유사 스타일 없음</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
    

    <div class="text-center mg20 col-xs-12">
        <div class="btn btn-red btn-lg" @click="save(product)">저장</div>
        <div class="btn btn-white btn-lg" @click="popupClose()">닫기</div>
    </div>

</div>


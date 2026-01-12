<?php include 'library_all.php'?>
<?php include 'library.php'?>

<style>
    .bootstrap-filestyle {display: table}
    .mx-input-wrapper {width:120px !important;}
    .mx-input {padding:0 12px !important;}
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3><?=$title?></h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            <input type="button" value="<?=$saveBtnTitle?>" class="btn btn-red btn-register" @click="save(product)" style="margin-right:178px">
            <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(product.sno, 'product')" style="margin-right:75px">
        </div>
    </form>

    <div class="row">
        <!-- 기본 정보 -->
        <div class="col-xs-12" style="padding:15px">
            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 정보</div>
                <div class="flo-right"></div>
            </div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody>
                <tr>
                    <th >고객사명</th>
                    <td class="font-16">{% items.customerName %}</td>
                    <th >고객납기</th>
                    <td class="font-16">{% project.customerDeliveryDt %}</td>
                    <th >이노버납기</th>
                    <td class="font-16">{% project.msDeliveryDt %}</td>
                    <th >생산처납기</th>
                    <td class="font-16">{% project.produceDeliveryDt %}</td>
                </tr>
                <tr>
                    <th >생산처</th>
                    <td class="font-16">{% project.produceCompany %}</td>
                    <th >생산형태</th>
                    <td class="font-16">{% project.produceTypeKr %}</td>
                    <th >생산국가</th>
                    <td class="font-16">{% project.produceNational %}</td>
                    <th >이노버 마진</th>
                    <td class="font-16">
                        {% product.msMargin %}%
                        <span class="notice-info" style="margin-left:5px;">마진 : 타겟단가 - 생산가격</span>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="table-title gd-help-manual">
                <div class="flo-left">스타일 기본 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th colspan="99" class="font-17">
                                {% product.styleCode.toUpperCase() %}
                            </th>
                        </tr>
                        <tr>
                            <th class="required">스타일</th>
                            <td>
                                <select2 id="sel-style" class="js-example-basic-single" v-model="product.prdStyle" style="width:100%" @change="setStyleCode(product,items.styleCode); setStyleName(product)" >
                                    <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                            <th class="required">제품명</th>
                            <td>
                                <input type="text" class="form-control width-lg font-16 ims-number" placeholder="제품명" v-model="product.productName">
                            </td>
                            <th >수량</th>
                            <td class="font-16">
                                <input type="number" class="form-control font-16 ims-number" placeholder="수량" v-model="product.prdExQty"> 장
                            </td>
                            <th >현재 단가</th>
                            <td class="font-16">
                                <input type="number" class="form-control font-16 ims-number" placeholder="현재 단가" v-model="product.currentPrice"> 원
                            </td>
                        </tr>
                        <tr>
                            <th>생산년도</th>
                            <td>
                                <select2 class="js-example-basic-single" v-model="product.prdYear" style="width:100%" @change="setStyleCode(product,items.styleCode)">
                                    <?php foreach($codeYear as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                            <th>시즌</th>
                            <td>
                                <select2 class="js-example-basic-single" v-model="product.prdSeason" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                                    <?php foreach($codeSeason as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                            <th>성별</th>
                            <td>
                                <select2 class="js-example-basic-single" v-model="product.prdGender" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                                    <option>구분없음</option>
                                    <?php foreach($codeGender as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                            <th >색상</th>
                            <td>
                                <select2 class="js-example-basic-single" v-model="product.prdColor" style="width:100%" @change="setStyleCode(product,items.styleCode)" >
                                    <option>구분없음</option>
                                    <?php foreach($codeColor as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                        </tr>
                        <tr>
                            <th>추가 식별 코드</th>
                            <td class="font-16">
                                <input type="text" class="form-control width-lg font-16 ims-number" placeholder="추가 식별 코드" v-model="product.addStyleCode" @change="setStyleCode(product,items.styleCode)" @keyup="setStyleCode(product,items.styleCode)">
                            </td>
                            <th >타겟 단가</th>
                            <td class="font-16">
                                <input type="number" class="form-control font-16 ims-number" placeholder="타겟 단가" v-model="product.targetPrice"> 원
                            </td>
                            <th >타겟 생산가<br><span class="text-muted" style="font-weight: normal">(타겟 마진)</span></th>
                            <td class="font-16">
                                <input type="number" class="form-control font-16 ims-number" placeholder="타겟생산가" v-model="product.targetPrdCost"> 원
                                <span class="font-13">({% setMargin(product.targetPrice, product.targetPrdCost) %}%)</span>
                            </td>
                            <th style="color:white;background-color: rgb(10, 106, 161) !important;">확정 판매 단가</th>
                            <td class="font-16">
                                <input type="number" class="form-control font-16 ims-number" placeholder="판매가" v-model="product.salePrice"> 원
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--
            <div class="table-title ">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        옵션/수량
                        <span class="notice-info mgl10">옵션명에 컴마( <span class="font-15">,</span> ) 사용시 오류가 발생할 수 있습니다.</span>
                    </div>
                    <div class="flo-right">
                        <button type="button" class="btn btn-red btn-sm"> + 옵션추가</button>
                    </div>
                </div>
                <--
                <div class="flo-left sl-test1" >
                    <div class="inline-block"></div>옵션/수량
                    <div class="btn btn-sm btn-red inline-block" @my_click="addOption(items, index)">+옵션추가</div>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="projectType" value="y"  v-model="project.projectType" />확정
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="projectType" value="y"  v-model="project.projectType" />미확정
                        </label>
                    </div>
                    <div>확정정보 : 송준호 23/07/22</div>
                </div>
                --
                <div class="flo-right sl-test3" >

                </div>
            </div>
            <div  style="clear:both">
            </div>
            -->
            <div class="table-title ">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        생산가격 <span class="display-none">(확정 : 서재훈 23/07/31)</span>
                    </div>
                    <div class="flo-right">
                        <div class="btn btn-white display-none">견적이력</div>
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center">
                        <colgroup>
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                            <col style="width:10%">
                        </colgroup>
                        <tr>
                            <th style="color:white;background-color: rgb(10, 106, 161) !important;">생산가<small class="font-white">(VAT별도)</small></th>
                            <th>원자재 소계</th>
                            <th>부자재 소계</th>
                            <th>공임</th>
                            <th>마진</th>
                            <th>물류 및 관세</th>
                            <th>관리비</th>
                            <th>생산MOQ</th>
                            <th>단가MOQ</th>
                            <th>MOQ미달추가금</th>
                        </tr>
                        <tr>
                            <td>
                                <span class="font-16 text-danger">{% total %}원</span>
                            </td>
                            <td>{% product.fabricCost %}원</td>
                            <td>{% product.subFabricCost %}원</td>
                            <td>
                                <!--<input type="number" class="form-control text-center" placeholder="공임(숫자만 입력, 원단위)" v-model="product.laborCost"  >-->
                                {% $.setNumberFormat(product.laborCost) %}원
                            </td>
                            <td>
                                <!--<input type="number" class="form-control text-center" placeholder="마진(숫자만 입력, 원단위)" v-model="product.marginCost" >-->
                                {% $.setNumberFormat(product.marginCost) %}원
                            </td>
                            <td>
                                <!--<input type="number" class="form-control text-center" placeholder="물류 및 관세(숫자만 입력, 원단위)" v-model="product.dutyCost" >-->
                                {% $.setNumberFormat(product.dutyCost) %}원
                            </td>
                            <td>
                                <!--<input type="number" class="form-control text-center" placeholder="관리비(숫자만 입력, 원단위)" v-model="product.managementCost">-->
                                {% $.setNumberFormat(product.managementCost) %}원
                            </td>
                            <td >
                                {% $.setNumberFormat(product.prdMoq) %}
                            </td>
                            <td >
                                {% $.setNumberFormat(product.priceMoq) %}
                            </td>
                            <td >
                                {% $.setNumberFormat(product.addPrice) %}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>


            <!--탭화면-->
            <div id="tabViewDiv">
                <ul class="nav nav-tabs mgb30" role="tablist">
                    <li role="presentation" :class="0 == tabMode?'active':''" @click="changeTab(0, null)">
                        <a href="#tab-status-cancel" data-toggle="tab" >현재정보</a>
                    </li>
                    <li role="presentation" :class="(historyIndex+1) == tabMode?'active':''" @click="changeTab(historyIndex+1, history)"  v-for="(history, historyIndex) in product.fabricHistory">
                        <a href="#tab-status-cancel" data-toggle="tab" >{% history.modDt %}</a>
                    </li>
                </ul>
            </div>

            <div class="table-title ">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        원단정보 <span style="font-weight: normal;font-size:12px;">원단수량: {% $.setNumberFormat(product.fabricCount) %}개 / BT완료: {% $.setNumberFormat(product.btCount) %}개</span>
                    </div>
                    <div :class="0 == tabMode?'flo-right display-none':''">
                        <button type="button" class="btn btn-red btn-sm " style="margin-bottom: 3px" @click="addFabric(product)"> + 원단추가</button>
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col style="width:5%" />
                            <col style="width:13%" />
                            <col style="width:10%" />
                            <col style="width:8%" />
                            <col style="width:4%" />
                            <col style="width:6%" />
                            <col style="width:4%" />
                            <col style="width:4%" />
                            <col style="width:9%" /><!--컨펌-->
                            <col style="width:6%" /><!--컨펌일-->
                            <col style="width:12%" /><!--비티비고-->
                            <col style="width:7%" /><!--원단업체-->
                            <col style="width:7%" /><!--비고-->
                            <col style="width:3%" /><!--삭제-->
                        </colgroup>
                        <tr>
                            <th>NO</th>
                            <th>자재명</th>
                            <th>혼용율</th>
                            <th>컬러</th>
                            <th>규격</th>
                            <th>가요척</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>BT컨펌</th>
                            <th>BT컨펌일자</th>
                            <th>BT비고</th>
                            <th>원단업체</th>
                            <th>비고</th>
                            <th>삭제</th>
                        </tr>
                        <tbody :class="(0) == tabMode?'':'display-none'">
                        <tr v-for="(fabric, fabricIndex) in product.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                            <td>
                                <input type="text" class="form-control text-center" placeholder="자재명" v-model="fabric.no">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="자재명" v-model="fabric.fabricName">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="혼용율" v-model="fabric.fabricMix">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="컬러" v-model="fabric.color">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="fabric.spec">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="요척(숫자만)" v-model="fabric.meas">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가" v-model="fabric.unitPrice">
                            </td>
                            <td>
                                {% fabric.price %}
                            </td>
                            <td>
                                <i class="fa fa-times hover-btn cursor-pointer text-muted" aria-hidden="true" @click="fabric.btConfirm=''" ></i>&nbsp;&nbsp;
                                <label class="radio-inline">
                                    <input type="radio" :name="'btConfirm_'+fabricIndex" value="n"  v-model="fabric.btConfirm" />미컨펌
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" :name="'btConfirm_'+fabricIndex" value="y"  v-model="fabric.btConfirm" />컨펌
                                </label>
                            </td>
                            <td>
                                <date-picker v-model="fabric.btConfirmDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="BT컨펌일" style="max-width: 120px!important;width:120px!important; font-weight: normal;"></date-picker>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="BT비고" v-model="fabric.btMemo">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="원단업체" v-model="fabric.fabricCompany">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="비고" v-model="fabric.memo">
                            </td>
                            <td>
                                <div class="btn btn-sm btn-white" @click="deleteFabric(product.fabric, fabricIndex)">삭제</div>
                            </td>
                        </tr>
                        </tbody>

                        <tbody v-for="(history, historyIndex) in product.fabricHistory" :class="(historyIndex+1) == tabMode?'':'display-none'">
                        <tr v-for="(fabric, fabricIndex) in history.fabric" @focusin="focusRow(fabricIndex)" :class="{ focused: focusedRow === fabricIndex }">
                            <td>
                                {% fabric.no %}
                            </td>
                            <td>
                                {% fabric.fabricName %}
                            </td>
                            <td>
                                {% fabric.fabricMix %}
                            </td>
                            <td>
                                {% fabric.color %}
                            </td>
                            <td>
                                {% fabric.spec %}
                            </td>
                            <td>
                                {% fabric.meas %}
                            </td>
                            <td>
                                {% fabric.unitPrice %}
                            </td>
                            <td>
                                {% fabric.price %}
                            </td>
                            <td>
                                <div v-show="'y' === fabric.btConfirm">
                                    컨펌
                                </div>
                                <div v-show="'n' === fabric.btConfirm">
                                    미컨펌
                                </div>
                                <div v-show="'' == fabric.btConfirm">
                                    미설정
                                </div>
                            </td>
                            <td>
                                {% fabric.btConfirmDt %}
                            </td>
                            <td>
                                {% fabric.btMemo %}
                            </td>
                            <td>
                                {% fabric.fabricCompany %}
                            </td>
                            <td>
                                {% fabric.memo %}
                            </td>
                            <td>

                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="table-title ">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        부자재정보
                    </div>
                    <div class="flo-right">
                        <button type="button" class="btn btn-red btn-sm " style="margin-bottom: 3px" @click="addSubFabric(product)"> + 부자재추가</button>
                    </div>
                </div>
                <div>
                    <table class="table table-rows table-default-center ims-fabric-info">
                        <colgroup>
                            <col style="width:5%" />
                            <col style="width:15%" />
                            <col style="width:15%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col style="width:8%" />
                            <col />
                            <col style="width:5%" />
                        </colgroup>
                        <tr>
                            <th>NO</th>
                            <th>자재명</th>
                            <th>부자재업체</th>
                            <th>규격</th>
                            <th>가요척</th>
                            <th>단가</th>
                            <th>금액</th>
                            <th>비고</th>
                            <th>삭제</th>
                        </tr>
                        <tbody :class="(0) == tabMode?'':'display-none'">
                        <tr v-for="(subFabric, subFabricIndex) in product.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                            <td>
                                <input type="text" class="form-control text-center" placeholder="자재명" v-model="subFabric.no">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="자재명" v-model="subFabric.subFabricName">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="부자재업체" v-model="subFabric.company">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="규격" v-model="subFabric.spec">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="요척(숫자만)" v-model="subFabric.meas">
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="단가" v-model="subFabric.unitPrice">
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.price) %} 원
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="비고" v-model="subFabric.memo">
                            </td>
                            <td>
                                <div class="btn btn-sm btn-white" @click="deleteFabric(product.subFabric, subFabricIndex)">삭제</div>
                            </td>
                        </tr>
                        </tbody>

                        <tbody v-for="(history, historyIndex) in product.fabricHistory" :class="(historyIndex+1) == tabMode?'':'display-none'">
                        <tr v-for="(subFabric, subFabricIndex) in product.subFabric" @focusin="subFocusRow(subFabricIndex)" :class="{ focused: subFocusedRow === subFabricIndex }">
                            <td>
                                {% subFabric.no %}
                            </td>
                            <td>
                                {% subFabric.subFabricName %}
                            </td>
                            <td>
                                {% subFabric.company %}
                            </td>
                            <td>
                                {% subFabric.spec %}
                            </td>
                            <td>
                                {% subFabric.meas %}
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.unitPrice) %}
                            </td>
                            <td>
                                {% $.setNumberFormat(subFabric.price) %} 원
                            </td>
                            <td>
                                {% subFabric.memo %}
                            </td>
                            <td>

                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>

            <!--
            <div class="table-title ">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        사이즈 Spec
                    </div>
                    <div class="flo-right">
                        <button type="button" class="btn btn-red btn-sm"> + 사이즈스펙추가</button>
                    </div>
                </div>
                <div>Todo...</div>
            </div>
            -->

        </div>
    </div>

    <div class="row">

            <div class="col-xs-6">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">스타일 이미지</div>
                    <div class="flo-right"></div>
                </div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th >
                            <div>
                                이미지 업로드
                            </div>
                            <div class="text-right">
                                <form @submit.prevent="uploadFile">
                                    <input :type="'file'" ref="fileThumbnail" style="display: block;width:1px!important;" />
                                    <input type="button" class="btn btn-black" value="업로드" @click="uploadFile(product)" style="margin-top:10px" />
                                </form>
                            </div>
                        </th>
                        <td>
                            <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(product.fileThumbnail)" >
                            <img :src="product.fileThumbnail" v-show="!$.isEmpty(product.fileThumbnail)" style="width:300px;">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-xs-6">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">메모</div>
                    <div class="flo-right"></div>
                </div>
                <table class="table table-cols">
                    <tbody>
                    <tr>
                        <td class="pd0">
                            <textarea class="form-control w100" rows="9" v-model="product.memo" ></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

    </div>

    <hr>

    <div class="text-center" style="margin-bottom:50px;">
        <div class="btn btn-red btn-lg" @click="save(product)"><?=$saveBtnTitle?></div>
        <div class="btn btn-white btn-lg" @click="self.close()">닫기</div>
    </div>
    
</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const projectSno = '<?=$requestParam['projectSno']?>';
        const sno = '<?=$requestParam['sno']?>';
        ImsService.getProductData(projectSno, sno).then((data)=>{
            console.log(data.data);
            const initParams = {
                data : {
                    tabMode : 0,
                    focusedRow: null,
                    subFocusedRow: null,
                    items : data.data.customer,
                    project : data.data.project,
                    product : data.data.product,
                },
                methods : {
                    changeTab : function(tabName, oldData){
                        vueApp.tabMode = tabName;
                    },

                    focusRow : (index) =>{
                        vueApp.focusedRow = index;
                    },
                    subFocusRow : (index) =>{
                        vueApp.subFocusedRow = index;
                    },
                    setStyleName : ( product ) =>{
                        if(!$.isEmpty(product.prdStyle)) {
                           product.productName = $('#sel-style option:selected').text();
                        }
                    },
                    setStyleCode : ( product, customerInitial ) =>{
                        let styleCode = [];
                        //console.log(product.prdYear);
                        if(!$.isEmpty(product.prdYear) && "구분없음" !== product.prdYear ) styleCode.push( (''+product.prdYear).substr(2,2) );
                        if(!$.isEmpty(product.prdSeason) && "구분없음" !== product.prdSeason ) styleCode.push( product.prdSeason.toUpperCase() );
                        if(!$.isEmpty(product.prdGender) && "구분없음" !== product.prdGender ) styleCode.push( product.prdGender.toUpperCase() );

                        if(!$.isEmpty(customerInitial)) styleCode.push( customerInitial ); //고객이니셜.

                        if(!$.isEmpty(product.prdStyle) && "구분없음" !== product.prdStyle ) styleCode.push( product.prdStyle.toUpperCase() );
                        if(!$.isEmpty(product.prdColor) && "구분없음" !== product.prdColor ) styleCode.push( product.prdColor.toUpperCase() );

                        if(!$.isEmpty(product.addStyleCode)) styleCode.push( product.addStyleCode.toUpperCase() );

                        product.styleCode = styleCode.join(' ');
                        //vueApp.$forceUpdate();
                    },
                    uploadFile : (product)=>{
                        const fileInput = vueApp.$refs.fileThumbnail;
                        //console.log('ProjectSNO');
                        //console.log(vueApp.project.sno);
                        if (fileInput.files.length > 0) {
                            const formData = new FormData();
                            const projectSno = vueApp.project.sno;
                            formData.append('upfile', fileInput.files[0]);
                            //console.log(fileInput.files.length);
                            $.ajax({
                                url: '<?=$nasUrl?>/img_upload.php?projectSno=' + projectSno ,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(result){
                                    //console.log('파일업로드 후속작업');
                                    //console.log(result);
                                    const rslt = JSON.parse(result);
                                    product.fileThumbnail = '<?=$nasUrl?>'+rslt.downloadUrl;
                                }
                            });

                        }
                    },
                    save : ( product )=>{
                        //console.log('저장 전 데이터 확인');
                        //console.log(product);
                        $.postAsync('ims_ps.php', {
                            mode:'saveProduct',
                            saveData : product,
                        }).then((data)=>{
                            console.log('처리 완료');
                            console.log(data);
                            let saveSno = data.data.sno;
                            $.msg('저장 되었습니다.', "", "success").then(()=>{
                                parent.opener.location.reload(); //부모창 갱신.
                                if($.isEmpty(sno)){
                                    self.close();
                                }
                            });
                        });
                    },
                    addFabric : ( product )=>{
                        $.imsPost('getFabricSchema',{
                            no : product.fabric[product.fabric.length-1]?.no,
                            index : product.fabric.length-1
                        }).then((data)=>{
                            vueApp.product.fabric.push(data.data);
                        });
                    },
                    addSubFabric : ( product )=>{
                        $.imsPost('getSubFabricSchema',{
                            no : product.subFabric[product.subFabric.length-1]?.no
                        }).then((data)=>{
                            vueApp.product.subFabric.push(data.data);
                        });
                    },
                    deleteFabric : (data, index)=>{
                        data.splice(index,1);
                    },
                    formatValue : (prd, field)=>{
                        // 형식화된 값을 원시 값으로 변환하여 저장
                        prd[field] = parseInt((prd[field]+'').replace(/,/g, ""));
                    },
                    setMargin : (saleCost, prdCost)=>{
                        let margin = 0;
                        if(saleCost>0){
                            margin = Math.round((saleCost-prdCost)/saleCost*100);
                        }
                        return margin;
                    },
                },
                computed: {
                    total() {
                        let total = 0;
                        let fabricCount = 0;
                        let btCount = 0;
                        total = Number(this.product.laborCost) + Number(this.product.marginCost) + Number(this.product.dutyCost) + Number(this.product.managementCost)

                        let fabricCost = 0;
                        for(let idx in this.product.fabric){
                            const eachValue = this.product.fabric[idx];
                            this.product.fabric[idx].price = Math.round(Number($.getOnlyNumber(eachValue.meas)) * Number(eachValue.unitPrice));
                            total += Number(eachValue.price);
                            fabricCost += Number(eachValue.price);

                            if( !$.isEmpty(eachValue['fabricName']) && !$.isEmpty(eachValue['color']) && !$.isEmpty(eachValue['btConfirm']) ){
                                fabricCount++;
                                if('y' === eachValue['btConfirm']) btCount++;
                            }
                        }
                        this.product.fabricCount = fabricCount;
                        this.product.btCount = btCount;
                        this.product.fabricCost = fabricCost;

                        let subFabricCost = 0;
                        for(let idx in this.product.subFabric){
                            const eachValue = this.product.subFabric[idx];
                            this.product.subFabric[idx].price = Math.round(Number((''+eachValue.meas).match(/(\d+\.\d+|\d+)/g)) * Number(eachValue.unitPrice));
                            total += Number(eachValue.price);
                            subFabricCost += Number(eachValue.price);
                        }
                        this.product.subFabricCost = subFabricCost;

                        this.product.prdCost = total;
                        if( this.product.salePrice > 0 && total > 0 ){
                            this.product.msMargin = Math.round((Number(this.product.salePrice) - total ) / Number(this.product.salePrice) * 100);
                        }else{
                            this.product.msMargin = 0;
                        }
                        return total;
                    }
                }

            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

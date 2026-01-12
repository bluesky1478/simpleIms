
<style>
    .mx-input-wrapper{ width:100px !important; }
</style>

<!--생산 스케쥴 관리 레이어 팝업-->
<div class="modal fade xsmall-picker" id="modalProduction"  role="dialog"  aria-hidden="true"  >
    <div class="modal-dialog" role="document" style="width:1450px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    <span class="sl-purple">
                        {% productionView.customerName %}
                    </span>

                    <span>
                        {% productionView.styleFullName %}
                        <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(productionView.customerName+' '+productionView.styleFullName)"></i>
                    </span>

                    <span style="font-weight: normal">
                        (
                            {% productionView.styleCode %}
                            <i class="fa fa-files-o text-muted cursor-pointer" aria-hidden="true" @click="$.copyClipBoard(productionView.styleCode)"></i>
                        )</span>의 <span class="sl-blue">
                    </span>
                    생산 관리
                </span>
            </div>
            <div class="modal-body">
                <section >
                    <div class="table-title">
                        <div class="flo-left pdt5 pdl5">
                            생산 정보 <span class="text-muted font-13" style="font-weight: normal !important;">(#{% productionView.sno %})</span>
                        </div>
                        <div class="flo-right pd5">
                            <div class="btn btn-white" @click="viewModeProduction = 'm'" v-show="'m' !== viewModeProduction && !isFactory ">수정하기</div>
                            <div class="btn btn-white" @click="viewModeProduction = 'v'" v-show=" productionView.sno > 0  && 'm' === viewModeProduction ">수정취소</div>
                            <div class="btn btn-red" @click="ImsProductService.saveProduction()" v-show="'m' === viewModeProduction ">저장</div>
                        </div>
                    </div>
                    <div class="">
                        <!--수정모드-->
                        <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;" v-show="'m' === viewModeProduction">
                            <colgroup>
                                <col style="width:7%">
                                <col style="width:40%">
                                <col style="width:10%">
                                <col style="width:40%">
                            </colgroup>
                            <tbody>
                            <tr >
                                <th>생산처</th>
                                <td>
                                    <select2 class="js-example-basic-single" style="width:50%" v-model="productionView.produceCompanySno">
                                        <option value="0">선택</option>
                                        <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                                <th>이노버 희망 납기일</th>
                                <td >
                                    <date-picker v-model="productionView.msDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                </td>
                            </tr>
                            <tr>
                                <th>운송</th>
                                <td class="pdl10">
                                    <label class="radio-inline" >
                                        <input type="radio" name="modGlobalDeliveryDiv"  value="n"  v-model="productionView.globalDeliveryDiv"  /> 미정
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="modGlobalDeliveryDiv"  value="ship"  v-model="productionView.globalDeliveryDiv"  /><i class="fa fa-ship" aria-hidden="true"></i> 선적
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="modGlobalDeliveryDiv"  value="air"  v-model="productionView.globalDeliveryDiv"  /><i class="fa fa-plane" aria-hidden="true"></i> 항공
                                    </label>

                                    <div class="mgt10 form-inline" v-show="'air' === productionView.globalDeliveryDiv">
                                        항공비용부담:
                                        <select class="form-control" v-model="productionView.planPayDiv" >
                                            <option value="이노버">이노버</option>
                                            <option value="생산처">생산처</option>
                                            <option value="고객">고객</option>
                                        </select>
                                        <br>
                                        <textarea class="form-control w100 mgt5" v-model="productionView.planPayMemo" rows="3" placeholder="항공 배송 메모"></textarea>
                                    </div>
                                </td>
                                <th>
                                    폐쇄몰 진행
                                </th>
                                <td>
                                    <label class="radio-inline" v-if="'n' === productionView.useMall">
                                        미사용
                                    </label>
                                    <label class="radio-inline font-16 bold sl-green" v-if="'y' === productionView.useMall">
                                        사용
                                    </label>
                                    <div class="notice-info">폐쇄몰 사용 정보는 프로젝트 상세 페이지에서 수정하시기 바랍니다.</div>
                                    <div>
                                        폐쇄몰 출고 가능일자 : <date-picker v-model="productionView.privateMallDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false" placeholder="출고가능일" style="width:130px !important;"></date-picker>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="'m' !== viewModeProduction">
                                <th>분류패킹</th>
                                <td class="pdl10">
                                    <label class="radio-inline font-16" v-if="'y' !== productionView.packingYn">
                                        미진행
                                    </label>
                                    <label class="radio-inline font-16" v-if="'y' === productionView.packingYn">
                                        진행
                                    </label>
                                    <simple-file-only :file="productionView.projectFiles.filePacking" :id="'filePacking'" :project="{sno:productionView.projectSno}" v-if="typeof productionView.projectFiles != 'undefined'"></simple-file-only>

                                    <div class="notice-info">분류패킹은 프로젝트 상세 페이지에서 수정하시기 바랍니다.</div>
                                </td>
                                <th>
                                    3PL 진행
                                </th>
                                <td class="font-16">
                                    <label class="radio-inline" v-if="'n' === productionView.use3pl">
                                        미사용
                                    </label>
                                    <label class="radio-inline font-16 bold sl-green" v-if="'y' === productionView.use3pl">
                                        사용
                                    </label>
                                    <simple-file-only :file="productionView.projectFiles.fileBarcode" :id="'fileBarcode'" :project="{sno:productionView.projectSno}" v-if="typeof productionView.projectFiles != 'undefined'"></simple-file-only>
                                    <div class="notice-info">3PL정보는 프로젝트 상세 페이지에서 수정하시기 바랍니다.</div>
                                </td>
                            </tr>
                            <tr >
                                <th>생산수량</th>
                                <td class="pd0">

                                    <table class="table table-cols table-borderless table-default-center table-pd-0 mg0" >
                                        <thead>
                                        <tr>
                                            <th style="width:160px!important;">사이즈</th>
                                            <th v-for="(size) in productionView.sizeOption">
                                                {% size %}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="ImsProductionService.assortType(productionView.typeOption)" v-for="(type) in productionView.typeOption">
                                            <th >
                                                {% type %}
                                                <!--<div class="text-muted font-11">10</div>-->
                                            </th>
                                            <td v-for="(size) in productionView.sizeOption" style="padding:2px !important;">

                                                <input type="text" class="form-control" :placeholder="size" v-model="productionView.sizeOptionQty[size+''+type]">

                                            </td>
                                        </tr>
                                        <tr v-if="!ImsProductionService.assortType(productionView.typeOption)">
                                            <th >
                                                수량
                                                <!--<div class="text-muted font-11">10</div>-->
                                            </th>
                                            <td style="padding:0 !important;">
                                                <input type="text" class="form-control" placeholder="생산수량 입력" v-model="productionView.sizeOptionQty['별첨']"  style="height:100%">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="font-18">
                                        Total : <span class="text-danger bold">{% $.setNumberFormat(sizeOptionQtyTotal) %}</span> ea
                                    </div>
                                </td>
                                <th>입고지</th>
                                <td class="">
                                    <input type="text" class="form-control w30" placeholder="입고지" v-model="productionView.deliveryPlace">
                                    <div class="mgt10">
                                        <a class="cursor-pointer sl-blue" @click="productionView.deliveryPlace='삼영'"><u>삼영</u></a>
                                        <a class="cursor-pointer sl-blue" @click="productionView.deliveryPlace='고객직배송'"><u>고객직배송</u></a>
                                        <a class="cursor-pointer sl-blue" @click="productionView.deliveryPlace='고객직배송'"><u>하나어패럴</u></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <!--확인모드-->
                        <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;" v-show="'v' === viewModeProduction" >
                            <colgroup>
                                <col style="width:7%">
                                <col style="width:40%">
                                <col style="width:10%">
                                <col style="width:40%">
                            </colgroup>
                            <tbody>
                            <tr >
                                <th>생산처</th>
                                <td class="font-16">
                                    {% productionView.reqFactoryNm %}
                                </td>
                                <th>이노버 희망 납기일</th>
                                <td class="font-16">
                                    {% $.formatShortDate(productionView.msDeliveryDt) %}

                                    <div v-if="30 >= productionView.produceStatus">
                                        (<span v-html="$.remainDate(productionView.msDeliveryDt, true)"></span>)
                                    </div>
                                </td>
                            </tr>
                            <!--
                            <tr >
                                <th>아소트</th>
                                <td class="pd0" colspan="99">
                                    <table class="table table-cols table-borderless table-default-center table-pd-0 mg0" >
                                        <thead>
                                        <tr>
                                            <th style="width:160px!important;">사이즈</th>
                                            <th v-for="(size) in productionView.sizeOption">
                                                {% size %}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="ImsProductionService.assortType(productionView.typeOption)" v-for="(type) in productionView.typeOption">
                                            <th >
                                                {% type %}
                                            </th>
                                            <td v-for="(size) in productionView.sizeOption" style="padding:2px !important;">
                                                {% $.setNumberFormat(productionView.sizeOptionQty[size+''+type]) %}
                                            </td>
                                        </tr>
                                        <tr v-if="!ImsProductionService.assortType(productionView.typeOption)">
                                            <th >
                                                수량
                                            </th>
                                            <td v-for="(size) in productionView.sizeOption" style="padding:2px !important;">
                                                {% $.setNumberFormat(productionView.sizeOptionQty[size]) %}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="font-18">
                                        Total : <span class="text-danger bold">{% $.setNumberFormat(sizeOptionQtyTotal) %}</span> ea
                                    </div>
                                    <div>
                                        <ims-accept2 :title="'아소트 승인상태'" :field="'assortConfirm'" :condition="productionView" :after="ImsProductService.setProductionFieldConfirm" ></ims-accept2>
                                    </div>
                                </td>
                            </tr>
                            -->
                            <tr v-show="!$.isEmpty(productionView.sno) && productionView.sno > 0">
                                <th >작업지시서</th>
                                <td >
                                    <div v-if="!$.isEmpty(productionView.ework) && 'p' === productionView.ework.data.mainApproval">
                                        <button class="badge-button gray-button mgl10" @click="window.open(`https://msinnover4.godomall.com/ics/ics_work.php?sno=${productionView.styleSno}`);">
                                            전산 작업지시서 보기
                                        </button>
                                    </div>

                                    <file-upload2 :file="productionView.fileList.fileWork" :id="'fileWork'" :params="productionView" :accept="'p' === productionView.workConfirm "></file-upload2>
                                    <ims-accept2 :title="'작지승인상태'" :field="'workConfirm'" :condition="productionView" :after="ImsProductService.setProductionFieldConfirm" ></ims-accept2>
                                </td>
                                <th >생산기타파일</th>
                                <td >
                                    <file-upload2 :file="productionView.fileList.filePrdEtc" :id="'filePrdEtc'" :params="productionView" ></file-upload2>
                                </td>
                            </tr>
                            <tr v-show="!$.isEmpty(productionView.sno) && productionView.sno > 0">
                                <th >케어라벨</th>
                                <td >
                                    <div v-if="!$.isEmpty(productionView.ework) && 'p' === productionView.ework.data.mainApproval">
                                        <div class="dp-flex dp-flex-gap10">
                                            <simple-file-list :files="productionView.ework.fileList.fileCareAi"></simple-file-list>
                                            <div v-if="!$.isEmpty(productionView.ework.fileList) && productionView.ework.fileList.fileCareAi.length > 0">
                                                최근 업로드 일자 : {% $.formatStrToDate(productionView.ework.fileList.fileCareAi[0].filePath.split('_')[1]) %}
                                            </div>
                                            <!--{% productionView.styleSno %} / {% productionView.customerSno %}  / {% productionView.projectSno %}-->
                                            <mini-file-history :file_div="'fileCareAi'" :params="{customerSno:productionView.customerSno,projectSno:productionView.projectSno, styleSno:productionView.styleSno }" class="mgl5"></mini-file-history>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <file-upload2 :file="productionView.fileList.fileCareMark" :id="'fileCareMark'" :params="productionView" ></file-upload2>
                                        <div class="notice-info">케어라벨 파일은 작업지시서 파일과 함께 검토하세요</div>
                                    </div>
                                </td>
                                <th >마크파일</th>
                                <td >
                                    <div v-if="!$.isEmpty(productionView.ework) && 'p' === productionView.ework.data.mainApproval">
                                        <simple-file-list :files="productionView.ework.fileList.fileMarkAi"></simple-file-list>
                                    </div>
                                    <div v-else>
                                        <file-upload2 :file="productionView.fileList.filePrdMark" :id="'filePrdMark'" :params="productionView" ></file-upload2>
                                        <div class="notice-info">마크 파일은 작업지시서 파일과 함께 검토하세요</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>운송</th>
                                <td class="pdl10 font-14">
                                    <label class="radio-inline" v-if="'n' === productionView.globalDeliveryDiv">
                                        미정
                                    </label>
                                    <label class="radio-inline" v-if="'ship' === productionView.globalDeliveryDiv">
                                        <i class="fa fa-ship" aria-hidden="true"></i> 선적
                                    </label>
                                    <label class="radio-inline" v-if="'air' === productionView.globalDeliveryDiv">
                                        <i class="fa fa-plane" aria-hidden="true"></i> 항공
                                    </label>

                                    <div class="mgt5 form-inline" v-show="'air' === productionView.globalDeliveryDiv">
                                        <div>항공비용 부담 : {% productionView.planPayDiv %}</div>
                                        <!--<div class="mgt5">항공 배송 메모</div>-->
                                        <textarea class="form-control w100 " disabled v-model="productionView.planPayMemo" rows="3" placeholder="memo"></textarea>
                                    </div>
                                </td>
                                <th>
                                    폐쇄몰 진행
                                </th>
                                <td>
                                    <label class="radio-inline" v-if="'n' === productionView.useMall">
                                        미사용
                                    </label>
                                    <label class="radio-inline font-16 bold sl-green" v-if="'y' === productionView.useMall">
                                        사용
                                    </label>
                                    <div class="notice-info">폐쇄몰 사용 정보는 프로젝트 상세 페이지에서 수정하시기 바랍니다.</div>

                                    <div>
                                        폐쇄몰 출고 가능일자 : {% $.formatShortDate(productionView.privateMallDeliveryDt) %}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>분류패킹</th>
                                <td class="pdl10">
                                    <label class="radio-inline font-16" v-if="'y' !== productionView.packingYn">
                                        미진행
                                    </label>
                                    <label class="radio-inline font-16 bold sl-green" v-if="'y' === productionView.packingYn">
                                        진행
                                    </label>
                                    <simple-file-only :file="productionView.projectFiles.filePacking" :id="'filePacking'" :project="{sno:productionView.projectSno}" v-if="typeof productionView.projectFiles != 'undefined'"></simple-file-only>
                                </td>
                                <th>
                                    3PL 진행
                                </th>
                                <td>
                                    <label class="radio-inline font-16 " v-if="'y' !== productionView.use3pl">
                                        미사용
                                    </label>
                                    <label class="radio-inline font-16 bold sl-green" v-if="'y' === productionView.use3pl">
                                        사용
                                    </label>
                                    <simple-file-only :file="productionView.projectFiles.fileBarcode" :id="'fileBarcode'" :project="{sno:productionView.projectSno}" v-if="typeof productionView.projectFiles != 'undefined'"></simple-file-only>
                                </td>
                            </tr>
                            <tr>
                                <th>생산수량</th>
                                <td>
                                    <span class="text-danger bold font-18">{% $.setNumberFormat(sizeOptionQtyTotal) %}</span> ea
                                </td>
                                <th>입고지</th>
                                <td>
                                    {% productionView.deliveryPlace %}
                                </td>
                            </tr>
                            <tr>
                                <th>구버전파일</th>
                                <td colspan="99">
                                    <div class="btn btn-white hover-btn cursor-pointer mgt5" @click="openProjectViewAndSetTabMode(productionView.projectSno,'basic')">구버전파일</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>

                    <div class="font-14">
                        <i class="fa fa-info-circle" aria-hidden="true"></i> 현재 생산 데이터에 작업지시서 및 케어라벨 데이터가 없다면 '구버전파일'을 클릭하여 확인해보시기 바랍니다.
                    </div>

                </section>

                <section v-show="productionView.sno > 0">
                    <div class="table-title mgt20">
                        <div class="flo-left pdt5 pdl5">
                            # 생산 스케쥴 관리
                        </div>
                        <div class="flo-right pd5">
                            <div class="btn btn-red btn-red-line2" @click="ImsProductService.saveProduction()" v-if="!$.isEmpty(productionView.sno) && isFactory && 10 >= productionView.produceStatus">스케쥴 입력 완료(이노버에확정요청)</div>
                            <div class="btn btn-gray" @click="ImsProductService.saveProduction()" v-if="!$.isEmpty(productionView.sno) && isFactory ">스케쥴저장</div>
                            <!--<div class="btn btn-red btn-red-line2" @click="ImsProductService.saveProduction()" v-if="!$.isEmpty(productionView.sno) && !isFactory ">생산완료</div>-->
                        </div>
                    </div>
                    <!--이노버?-->
                    <div v-if="!isFactory && 'v' === viewModeProduction ">
                        <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;" >
                            <colgroup>
                                <col style="width:7%">
                                <col style="width:40%">
                                <col style="width:10%">
                                <col style="width:40%">
                            </colgroup>
                            <tbody>
                            <tr >
                                <td colspan="99" class="ta-c pd20" v-if="0 == productionView.produceStatus">
                                    <div class="btn btn-red" @click="ImsProductionService.setProduceStatus(productionView.sno, 10)">스케쥴요청</div>
                                </td>
                                <td colspan="99" class="ta-c pd20" v-if="10 == productionView.produceStatus">
                                    생산처에 스케쥴 요청 상태
                                </td>
                                <td colspan="99" class="ta-c pd0" v-if="productionView.produceStatus >= 20" style="padding:0!important;border-bottom: none !important;">

                                    <div v-if="productionView.produceStatus == 20"> 본 스케쥴 리스트에서 확정 또는 반려 처리 필요 </div>

                                    <table class="table table-cols table-td-height0 table-th-height0 table-pd-2 table-default-center" style="margin-bottom:0 !important;" >
                                        <colgroup>
                                            <col style="width:5%">
                                            <?php foreach( $stepList as $stepName ) { ?>
                                                <col style="width:5%">
                                            <?php } ?>
                                        </colgroup>
                                        <tbody>
                                        <tr >
                                            <th>구분</th>
                                            <?php foreach( $stepTitleList as $stepName ) { ?>
                                                <th><?=$stepName?></th>
                                            <?php } ?>
                                        </tr>
                                        <tr >
                                            <th>예정일</th>
                                            <?php foreach( $stepList as $stepName ) { ?>
                                                <td>
                                                    {% $.formatShortDate(productionView.<?=$stepName?>ExpectedDt) %}
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <tr >
                                            <th>대체내용</th>
                                            <?php foreach( $stepList as $stepName ) { ?>
                                                <td>
                                                    {% productionView.<?=$stepName?>Memo %}
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <tr >
                                            <th>완료일</th>
                                            <?php foreach( $stepList as $stepName ) { ?>
                                                <td>
                                                    {% $.formatShortDate(productionView.<?=$stepName?>CompleteDt) %}
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <tr >
                                            <th>완료일대체</th>
                                            <?php foreach( $stepList as $stepName ) { ?>
                                                <td>
                                                    {% productionView.<?=$stepName?>Memo2 %}
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <tr >
                                            <th>승인</th>
                                            <?php foreach( $stepList as $stepName ) { ?>
                                                <td>
                                                    {% productionView.<?=$stepName?>ConfirmKr %}
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!-- 개별 필요하면 기능 구현
                                    <div style="padding:10px" v-if="20 == productionView.produceStatus">
                                        <div class="btn btn-red" @click="ImsProductService.setScheduleReq(30, productionView.sno, productionView.styleSno)">스케쥴확정</div>
                                        <div class="btn btn-red btn-red-line2" @click="ImsProductService.setScheduleReq(10, productionView.sno, productionView.styleSno)">스케쥴반려</div>
                                    </div>
                                    -->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!--생산처 입력 or 수정하기 시 -->
                    <div v-if="isFactory || 'm' === viewModeProduction ">
                        <table class="table table-cols table-td-height0 table-th-height0 table-pd-2 table-default-center" style="margin-bottom:0 !important;" >
                            <colgroup>
                                <col style="width:5%">
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <col style="width:5%">
                                <?php } ?>
                            </colgroup>
                            <tbody>
                            <tr >
                                <th>구분</th>
                                <?php foreach( $stepTitleList as $stepName ) { ?>
                                    <th><?=$stepName?></th>
                                <?php } ?>
                            </tr>
                            <tr >
                                <th>예정일</th>
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <td class="ta-l">
                                        <date-picker v-model="productionView.<?=$stepName?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr >
                                <th>대체내용</th>
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <td>
                                        <input type="text" class="form-control" v-model="productionView.<?=$stepName?>Memo" placeholder="일정대체내용" style="width:116px;border-radius: 3px">
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr >
                                <th>완료일</th>
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <td class="ta-l">
                                        <date-picker v-model="productionView.<?=$stepName?>CompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr >
                                <th>완료일대체</th>
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <td>
                                        <input type="text" class="form-control" v-model="productionView.<?=$stepName?>Memo2" placeholder="완료일대체" style="width:116px;border-radius: 3px">
                                    </td>
                                <?php } ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section v-show="productionView.sno > 0 && productionView.produceStatus >= 30">
                    <div class="table-title mgt20">
                        <div class="flo-left pdt5 pdl5">
                            # 생산 파일 등록 / 승인
                        </div>
                        <div class="flo-right pd5">
                        </div>
                    </div>
                    <div>
                        <table class="table table-cols table-pd-2 table-td-height0 table-th-height0 " style="margin-bottom:0 !important;" >
                            <colgroup>
                                <col style="width:8%">
                                <col style="width:25%">
                                <col style="width:8%">
                                <col style="width:25%">
                                <col style="width:8%">
                                <col style="width:25%">
                            </colgroup>
                            <tr>
                                <?php foreach($stepAllList as $key => $title) { if( !in_array($key, ['wash','fabricConfirm','fabricShip']) ) continue  ?>
                                    <th colspan="2" class="font-14" style="height: 40px!important;">
                                        <?=$title?>
                                    </th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php foreach($stepAllList as $key => $title) { if( !in_array($key, ['wash','fabricConfirm','fabricShip']) ) continue  ?>
                                    <td colspan="2">
                                        <file-upload2 :file="productionView.fileList.file<?=ucfirst($key)?>" :id="'file<?=ucfirst($key)?>'" :params="productionView" :accept="'p' === productionView.<?=$key?>Confirm"></file-upload2>
                                        <ims-accept2 :title="'<?=$title?> 승인상태'" :field="'<?=$key?>Confirm'" :condition="productionView" :after="ImsProductService.setProductionFileConfirm"></ims-accept2>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php foreach($stepAllList as $key => $title) { if( !in_array($key, ['qc','inline','ship']) ) continue  ?>
                                    <th colspan="2" class="font-14" style="height: 40px!important;">
                                        <?=$title?>
                                        <?php if('ship' === $key) { ?>
                                            <div class="btn btn-red btn-red-line2" @click="openProductionSopImg">체크방법</div>
                                        <?php } ?>
                                        <?php if('qc' === $key || 'inline' === $key) { ?>
                                            <?php if('qc' === $key) { ?>
                                                <span @click="openCommonPopup('upsert_style_inspect', 1300, 910, {'sno':productionView.styleSno, 'append_type':'qc'});" class="btn btn-white">QC 검수등록</span>
                                            <?php } else { ?>
                                                <span @click="openCommonPopup('upsert_style_inspect', 1300, 910, {'sno':productionView.styleSno, 'append_type':'inline'});" class="btn btn-white">인라인 검수등록</span>
                                            <?php } ?>
                                            <span @click="openCommonPopup('upsert_style_inspect', 1300, 910, {'sno':productionView.styleSno});" class="btn btn-white">QC/인라인 검수보기</span>
                                            <input v-if="false" type="button" value="샘플리뷰서" class="btn btn-white btn-icon-excel" onclick="location.href='<?=$adminHost?>/download/download.php?filePath=<?=urlencode('./data/template/ims_qc_sample_review.xlsx')?>&fileName=<?=urlencode('(이노버)샘플검수리뷰서_v1.xlsx')?>'">
                                        <?php } ?>
                                    </th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php foreach($stepAllList as $key => $title) { if( !in_array($key, ['qc','inline','ship']) ) continue  ?>
                                    <?php if('ship' === $key) { ?>
                                        <td colspan="2">
                                            <file-upload2 :file="productionView.fileList.file<?=ucfirst($key)?>" :id="'file<?=ucfirst($key)?>'" :params="productionView" <?php if( 'ship' !== $key ) { ?>:accept="'p' === productionView.<?=$key?>Confirm"<?php } ?>></file-upload2>
                                            <ims-accept2 :title="'<?=$title?> 승인상태'" :field="'<?=$key?>Confirm'" :condition="productionView" :after="ImsProductService.setProductionFileConfirm"></ims-accept2>
                                        </td>
                                    <?php } else { ?>
                                        <td colspan="2">
                                            <simple-file-only-history-upload v-if="productionView.fileList.file<?=ucfirst($key)?> != undefined" :file="productionView.fileList.file<?=ucfirst($key)?>" :params="productionView" :file_div="'file<?=ucfirst($key)?>'" ></simple-file-only-history-upload>
                                            <ims-accept2 :title="'<?=$title?> 승인상태'" :field="'<?=$key?>Confirm'" :condition="productionView" :after="ImsProductService.setProductionFileConfirm"></ims-accept2>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                            </tr>
                        </table>
                    </div>

                    <!--<div class="table-title mgt20">
                        <div class="flo-left pdt5 pdl5">
                            # 납품 완료 정보
                        </div>
                        <div class="flo-right pd5">
                        </div>
                    </div>-->
                    <div>
                        <table class="table table-cols table-pd-2 table-td-height0 table-th-height0 " style="margin-bottom:0 !important;" >
                            <colgroup>
                                <col style="width:8%">
                                <col style="width:25%">
                                <col style="width:8%">
                                <col style="width:25%">
                                <col style="width:8%">
                                <col style="width:25%">
                            </colgroup>
                            <tr>
                                <th colspan="2" class="font-14" style="height: 40px!important;">
                                    #납품 완료 정보 <span @click="openCommonPopup('upsert_style_inspect_delivery', 1300, 910, {'sno':productionView.styleSno});" class="btn btn-white">납품 보고서</span>
                                </th>
                                <th colspan="2" class="font-14" style="height: 40px!important;">
                                    #패킹 리스트 <span @click="openCommonPopup('check_customer_packing', 1000, 910, {'styleSno':productionView.styleSno});" class="btn btn-white">분류패킹 수량확인</span>
                                </th>
                                <th colspan="2" class="font-14" style="height: 40px!important;">
                                    #배송 운송장
                                </th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <file-upload2 :file="productionView.fileList.fileProductionComplete" :id="'fileProductionComplete'" :params="productionView"></file-upload2>
                                </td>
                                <td colspan="2">
                                    <file-upload2 :file="productionView.fileList.fileProductionPacking" :id="'fileProductionPacking'" :params="productionView"></file-upload2>
                                </td>
                                <td colspan="2">
                                    <file-upload2 :file="productionView.fileList.fileProductionInvoice" :id="'fileProductionInvoice'" :params="productionView"></file-upload2>
                                </td>
                            </tr>
                        </table>
                    </div>
                </section>

            </div>
            <div class="modal-footer">

                <div class="btn btn-red btn-red-line2" @click="ImsProductService.saveProduction()" v-show="10 == productionView.produceStatus && isFactory ">저장</div>

                <!--
                <div class="btn btn-red" @click="ImsProductService.saveProduction()" v-show="10 == productionView.produceStatus && isFactory ">스케쥴 입력 완료 처리</div>
                -->

                <div class="btn btn-white" @click="viewModeProduction = 'm'" v-show="'m' !== viewModeProduction && !isFactory ">수정하기</div>
                <div class="btn btn-white" @click="viewModeProduction = 'v'" v-show=" productionView.sno > 0  && 'm' === viewModeProduction ">수정취소</div>
                <div class="btn btn-red" @click="ImsProductService.saveProduction()" v-show="'m' === viewModeProduction ">저장</div>

                <!--
                <div class="btn btn-red" @click="saveProduction()" v-if="$.isEmpty(productionView.sno)">등록</div>
                <div class="btn btn-red" @click="saveProduction()" v-if="!$.isEmpty(productionView.sno)">수정</div>
                -->
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
    <div class="mgt50"></div>
</div>

<!--최초 스케쥴 정보-->
<div class="modal fade xsmall-picker" id="modalProduction2"  role="dialog"  aria-hidden="true"  >
    <div class="modal-dialog" role="document" style="width:1300px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    <span class="sl-purple">
                        {% productionView.customerName %}</span> {% productionView.styleFullName %}<span style="font-weight: normal">({% productionView.styleCode %})</span>의 <span class="sl-blue">
                    </span>
                    생산 관리
                </span>
            </div>
            <div class="modal-body">
                <section >
                    <div class="table-title">
                        <div class="flo-left pdt5 pdl5">
                            스케쥴 정보 <span class="text-muted font-13" style="font-weight: normal !important;">(#{% productionView.sno %})</span>
                        </div>
                        <div class="flo-right pd5"></div>
                    </div>
                    <!--생산처 입력 or 수정하기 시 -->

                    <div v-if="null !== productionView.firstData">
                        <table class="table table-cols table-pd-2 table-default-center" style="margin-bottom:0 !important;" >
                            <colgroup>
                                <col style="width:5%">
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <col style="width:5%">
                                <?php } ?>
                            </colgroup>
                            <tbody>
                            <tr >
                                <th>구분</th>
                                <?php foreach( $stepTitleList as $stepName ) { ?>
                                    <th><?=$stepName?></th>
                                <?php } ?>
                            </tr>
                            <tr >
                                <th>예정일</th>
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <td >
                                        {% productionView.firstData.schedule.<?=$stepName?>.ConfirmExpectedDt %}
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr >
                                <th>대체내용</th>
                                <?php foreach( $stepList as $stepName ) { ?>
                                    <td>
                                        {% productionView.firstData.schedule.<?=$stepName?>.Memo %}
                                    </td>
                                <?php } ?>
                            </tr>
                            </tbody>
                        </table>

                        <div class="mgt10">
                            <div class="font-16 bold ">스케쥴 승인자 : {% productionView.firstData.acceptData.managerNm %}</div>
                            <div class="font-16 bold ">스케쥴 승인일 : {% $.formatShortDate(productionView.firstData.acceptData.acceptDt) %}</div>
                        </div>

                    </div>

                </section>

            </div>

            <div class="modal-footer">
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
    <div class="mgt50"></div>
</div>

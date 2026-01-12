<div class="">
    <!--우측 정보-->
    <div class="col-xs-6" >
        <!-- 기본 정보 -->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객사 기본 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="_require">고객사명</th>
                        <td>
                            <?php $model='customer.customerName'; $placeholder='고객사명' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th >영업담당자</th>
                        <td>
                            <div v-show="!isModify">
                                {% customer.salesManagerNm %}
                            </div>
                            <div v-show="isModify">
                                <select2 aclass="salesManagerSno" v-model="customer.salesManagerSno"  style="width:100%" >
                                    <?php foreach ($managerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                        </td>
                    </tr>
                    <tr >
                        <th>3PL 이용 여부</th>
                        <td >
                            <?php $model = 'customer.use3pl'; $listCode = 'usedType'; $modelPrefix='mall'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <th>폐쇄몰 사용 여부</th>
                        <td >
                            <?php $model = 'customer.useMall'; $listCode = 'usedType'; $modelPrefix='mall'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                    </tr>
                    <tr v-if="'y' === customer.useMall">
                        <th>어드민ID</th>
                        <td >
                            <?php $model='customer.addedInfo.mall030'; $placeholder='어드민ID' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>몰ID</th>
                        <td >
                            <?php $model='customer.addedInfo.mall031'; $placeholder='몰ID' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!--좌측 정보-->
    <div class="col-xs-6" >
        <!-- 담당자 정보 -->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">
                    담당자 정보
                </div>
                <div class="flo-right">

                </div>
            </div>
            <div class="">
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-sm">
                        <col class="width-md">
                        <col class="width-sm">
                        <col class="width-md">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>담당자명</th>
                        <td >
                            <?php $model='customer.contactName'; $placeholder='담당자명' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                        <th>연락처</th>
                        <td>
                            <?php $model='customer.contactMobile'; $placeholder='휴대전화' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td colspan="4">
                            <?php $model='customer.contactEmail'; $placeholder='이메일' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <!--폐쇄몰 개설 정보-->
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">폐쇄몰 개설 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols table-pd-5 table-td-height35 table-th-height35">
                    <colgroup>
                        <col class="width-lg">
                        <col class="width-md">
                        <col class="width-sm">
                        <col class="width-md">
                        <col class="width-sm">
                        <col class="width-md">
                        <col class="width-sm">
                        <col class="width-md">
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="text-left">구분</th>
                        <th colspan="4" class="text-left">기본정보</th>
                        <th colspan="99" class="text-left">비고</th>
                    </tr>
                    </thead>
                    <tbody v-if="'y' !== customer.useMall">
                        <tr>
                            <td colspan="99" class="text-center">
                                해당 없음 (폐쇄몰 미사용)
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-if="'y' === customer.useMall">
                    <tr>
                        <th class="_require">회원가입</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall001'; $listCode = 'mall1'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall002'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>본사관리자 출고 승인</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall003'; $listCode = 'mall2'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall004'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>출고 방법</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall005'; $listCode = 'mall3'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall006'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>상품 결제</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall007'; $listCode = 'mall4'?>
                            <?php include 'basic_view/_radio.php'?>

                            <div v-if="'1' == customer.addedInfo.mall007" class="mgt5 bg-light-yellow pd5 font-11">
                                <div style="height: 1px; width:100%; background-color: #efefef; margin-bottom:3px; "></div>

                                <div class="dp-flex">
                                    <span class="font-11 mgr5">무상지급 건 결제 :</span>
                                    <?php $model = 'customer.addedInfo.mall008'; $listCode = 'mall41'?>
                                    <?php include 'basic_view/_radio.php'?>
                                </div>
                            </div>
                            <div v-if="'2' == customer.addedInfo.mall007" class="notice-info">
                                주문시 주문자 개별 결제
                            </div>
                            <div v-if="'3' == customer.addedInfo.mall007" class="notice-info">
                                본사 지원금액 협의 후 진행
                            </div>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall011'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>폐쇄몰 개설 비용</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall017'; $listCode = 'mall5'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall018'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>물류 운영 관리 비용</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall019'; $listCode = 'mall6'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall020'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>

                    <tr>
                        <th>물류 운영 관리 비용 결제 방법</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall021'; $listCode = 'mall7'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall022'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>배송비 결제</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall025'; $listCode = 'mall8'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall026'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    <tr>
                        <th>재고 소진 시 (안전재고 미달)</th>
                        <td colspan="4">
                            <?php $model = 'customer.addedInfo.mall027'; $listCode = 'mall9'?>
                            <?php include 'basic_view/_radio.php'?>
                        </td>
                        <td colspan="99">
                            <?php $model='customer.addedInfo.mall028'; $placeholder='비고' ?>
                            <?php include 'basic_view/_text.php'?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




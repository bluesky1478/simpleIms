<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">신규 프로젝트 등록</h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col class="width-sm">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th >고객사</th>
                    <td>

                        <div v-show="isCustomerReg">
                            신규 등록 고객 : {% customer.customerName %} ({% customer.sno %})
                        </div>

                        <div v-show="!isCustomerReg">
                            <select2 class="js-example-basic-single" v-model="mainData.project.customerSno" @change="setCustomer()" style="width:50%" >
                                <option value="-1">신규등록</option>
                                <?php foreach ($customerListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </div>

                        <!--v-show="-1 == mainData.project.customerSno"-->
                        <table class="table table-cols table-pd-3 table-td-height35 table-th-height35 mgt5" >
                            <colgroup>
                                <col class="width-sm">
                                <col class="width-md">
                                <col class="width-sm">
                                <col class="width-md">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th class="_require">고객사명</th>
                                <td>
                                    <?php $model='customer.customerName'; $placeholder='고객사명' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                                <th class="_require text-danger">
                                    Style code
                                </th>
                                <td>
                                    <?php $model='customer.styleCode'; $placeholder='고객 코드' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th>담당자명</th>
                                <td >
                                    <?php $model='customer.contactName'; $placeholder='담당자명' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                                <th>직함</th>
                                <td>
                                    <?php $model='customer.contactPosition'; $placeholder='직함' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th>연락처</th>
                                <td>
                                    <?php $model='customer.contactMobile'; $placeholder='휴대전화' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                                <th>담당자 성향</th>
                                <td>
                                    <?php $model='customer.contactPreference'; $placeholder='담당자 성향' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th>이메일</th>
                                <td colspan="3">
                                    <?php $model='customer.contactEmail'; $placeholder='이메일' ?>
                                    <?php include './admin/ims/template/basic_view/_text.php'?>
                                </td>
                            </tr>
                            <tr>
                                <th>사무실 주소</th>
                                <td colspan="3">
                                    <div v-show="!isModify">
                                        {% customer.contactZipcode %}
                                        {% customer.contactAddress %}
                                        {% customer.contactAddressSub %}
                                    </div>
                                    <div v-show="isModify">
                                        <!--<div class="form-inline mgb5">
                                            <span title="우편번호를 입력해주세요!">
                                                <input type="text" name="zonecode" id="zonecode" size="6" maxlength="5" class="form-control js-number" data-number="5" readonly v-model="customer.contactZipcode"  />
                                            </span>
                                            <input type="button" onclick="sl_postcode_search('zonecode', 'address', 'zipcode');" value="우편번호찾기" class="btn btn-gray btn-sm"/>
                                        </div>-->
                                        <div class="form-inline">
                                            <div title="주소를 입력해주세요!">
                                                <input type="text" name="address" id="address" class="form-control" placeholder="주소" v-model="customer.contactAddress"  style="width:100%" />
                                            </div>
                                            <div title="상세주소를 입력해주세요!" >
                                                <input type="text" name="addressSub" id="addressSub" class="form-control mgt5 w-100p" placeholder="상세주소" v-model="customer.contactAddressSub" style="width:100%" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                <tr>
                    <th>
                        담당자
                    </th>
                    <td>
                        <div class="dp-flex">
                            <div class="dp-flex w-25p">
                                <div class="w-60px">영업 :</div>
                                <select2 class="js-example-basic-single" v-model="mainData.project.salesManagerSno"  style="width:100%" >
                                    <option value="0">미정</option>
                                    <?php foreach ($managerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                            <div class="dp-flex w-25p mgl10">
                                <div class="w-60px">디자인 :</div>
                                <select2 class="js-example-basic-single" v-model="mainData.project.designManagerSno"  style="width:100%" >
                                    <option value="0">미정</option>
                                    <?php foreach ($designManagerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        프로젝트 연도/시즌
                    </th>
                    <td >
                        <select v-model="mainData.project.projectYear" class="form-control form-inline inline-block " >
                            <?php foreach($yearList as $yearEach) {?>
                                <option><?=$yearEach?></option>
                            <?php }?>
                        </select>
                        <select v-model="mainData.project.projectSeason" class="form-control form-inline inline-block" >
                            <option >ALL</option>
                            <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                <option><?=$seasonEn?></option>
                            <?php }?>
                        </select>
                    </td>
                </tr>

                <?php if(!empty($requestParam['rtw'])){?>
                    <!--기성복이면-->
                    <tr >
                        <th>
                            고객 납기
                        </th>
                        <td>
                            <date-picker v-model="mainData.project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :editable="false"></date-picker>
                        </td>
                    </tr>
                <?php }else{?>
                    <!--기성복이 아니라면-->
                    <tr>
                        <th>
                            진행 구분
                        </th>
                        <td >
                            <?php foreach( \Component\Ims\ImsCodeMap::BID_TYPE as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="bidType2" value="<?=$k?>" v-model="mainData.project.bidType2"  @change="setType()" /><?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            프로젝트 타입
                        </th>
                        <td >
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE_N as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="projectType" value="<?=$k?>" v-model="mainData.project.projectType"  /><?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            디자인 업무 타입
                        </th>
                        <td >
                            <?php foreach( \Component\Ims\ImsCodeMap::DESIGN_WORK_TYPE as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="desingWorkType" value="<?=$k?>" v-model="mainData.projectExt.designWorkType" /><?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            목표 매출 년도
                        </th>
                        <td>
                            <select2 v-model="mainData.projectExt.targetSalesYear" class="form-control form-inline inline-block " style="width:100px;">
                                <?php foreach($yearFullList as $key => $val) {?>
                                    <option value="<?=$val?>"><?=$key?></option>
                                <?php }?>
                            </select2>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            계약 난이도
                        </th>
                        <td>
                            <?php foreach( \Component\Ims\ImsCodeMap::RATING_TYPE2 as $k => $v){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="contractDifficult" value="<?=$k?>" v-model="mainData.projectExt.contractDifficult" /><?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }?>
                <tr >
                    <th>
                        사업계획 포함 여부
                    </th>
                    <td>
                        <label class="radio-inline " v-for="(eachValue, eachKey) in getCodeMap()['includeType']">
                            <input type="radio" :name="'bizPlanYn'"  :value="eachKey" v-model="mainData.project.bizPlanYn"  />
                            <span class="font-12">{%eachValue%}</span>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">등록</div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>

    </div>

</section>

<script>
    function sl_postcode_search(zoneCodeID, addrID, zipCodeID)
    {
        win = popup({
            url: '/share/postcode_search.php?zoneCodeID=' + zoneCodeID + '&addrID=' + addrID + '&zipCodeID=' + zipCodeID,
            target: 'postcode',
            width: 540,
            height: 700,
            resizable: 'yes',
            scrollbars: 'yes'
        });
        win.focus();
        return win;
    };
</script>


<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isCustomerReg : false,
            isModify : true,
            customer : {
                sno : -1
            },
        });

        ImsBoneService.setMethod(serviceData,{
            setType : ()=>{
                const typeMap = {
                    'single': '0',
                    'bid': '2',
                    'costBid': '2',
                };
                vueApp.mainData.project.projectType = typeMap[vueApp.mainData.project.bidType2];
            },
            setCustomer : ()=>{
                ImsCustomerService.getData(vueApp.mainData.project.customerSno).then((data)=>{
                    $.imsPostAfter(data,(customerData)=>{
                        //console.log(customerData);
                        vueApp.customer = $.copyObject(customerData);
                        if( customerData.sno > 0 ){
                            vueApp.mainData.project.salesManagerSno = customerData.salesManagerSno;
                            vueApp.mainData.project.designManagerSno = customerData.designManagerSno;
                        }else{
                            vueApp.mainData.project.salesManagerSno = <?=$managerSno?>;
                            vueApp.mainData.project.designManagerSno = '';
                        }
                    });
                });
            },
            save : ()=>{
                //고객사 등록
                console.log(vueApp.customer.sno);
                //고객 있는지 여부에 따라 저장.
                if( $.isEmpty(vueApp.customer.sno) || -1 == vueApp.customer.sno ){
                    //고객사 등록 후 프로젝트 저장
                    $.imsSave('customer',vueApp.customer,(customerSno)=>{
                        vueApp.isCustomerReg = true; //고객 등록 완료.
                        //project등록
                        vueApp.customerSno = customerSno;
                        vueApp.mainData.project.customerSno = customerSno;

                        <?php if(!empty($requestParam['rtw'])){?>
                        vueApp.mainData.project.produceCompanySno = '48'; //대표생산처
                        vueApp.mainData.project.produceType = '1'; //대표생산처
                        vueApp.mainData.project.produceNational = 'kr'; //대표생산처
                        vueApp.mainData.project.projectType = 4; //기성복
                        vueApp.mainData.project.projectStatus = '50'; //대표생산처
                        vueApp.mainData.projectExt.salesStatus = 'complete'; //영업은 완료.
                        <?php }?>

                        ImsProjectService.saveProject(vueApp.mainData.project, vueApp.mainData.projectExt);
                    }).then((data)=>{
                        console.log('저장후 단계', data);
                    });
                }else{
                    ImsProjectService.saveProject(vueApp.mainData.project, vueApp.mainData.projectExt);
                }

            }
        });

        ImsBoneService.serviceBegin(DATA_MAP.PROJECT,{sno:0},serviceData);

    });
</script>
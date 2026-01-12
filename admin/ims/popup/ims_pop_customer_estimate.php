<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>{% mainData.customer.customerName %} 견적서 발송</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <div class="row">

        <div class="col-xs-12 new-style">

            <div class="table-title gd-help-manual">
                <div class="flo-left area-title">
                    <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                    견적 발송 정보 등록
                </div>
                <div class="flo-right">
                </div>
            </div>

            <table class="table table-cols  xsmall-picker " >
                <colgroup>
                    <col class="width-sm">
                    <col class="width-md">
                    <col class="width-sm">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr >
                    <th >
                        견적 담당자
                    </th>
                    <td colspan="99">
                        <select2 class="js-example-basic-single" v-model="estimateData.estimateManagerSno"  style="width:200px" >
                            <option value="32">문상범 (010-8830-8307)</option>
                            <?php foreach ($salseManagerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr >
                    <th >
                        수신자 정보
                    </th>
                    <td colspan="99">
                        <div>
                            <input type="text" v-model="receiverData.name" placeholder="수신자명" class="form-control inline-block w-20p">
                            <input type="text" v-model="receiverData.position" placeholder="직급/직책" class="form-control inline-block w-15p">
                            <input type="text" v-model="receiverData.mail" placeholder="이메일(정확히)" class="form-control inline-block w-45p">
                            <div class="btn btn-gray btn-sm" @click="addReceiver()">+ 수신자 추가</div>
                        </div>
                        <ul class="mgt10">
                            <li v-for="(receiver, receiverIndex) in estimateData.receiverInfo" class="mgt5">
                                {% receiver.name %} {% receiver.position %} ({% receiver.mail %}) <div class="btn btn-white btn-sm" @click="deleteElement(estimateData.receiverInfo, receiverIndex)">제외</div>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr >
                    <th >
                        참조
                    </th>
                    <td colspan="99">
                        <ul class="">
                            <li >
                                한소윤 (syhan@msinnover.com)
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr >
                    <th >
                        견적일자
                    </th>
                    <td colspan="99">
                        <date-picker v-model="estimateData.estimateDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                    </td>
                </tr>
                <tr >
                    <th >
                        타입
                    </th>
                    <td colspan="99">

                        <?php $key1 = 'estimateData.estimateType'; $listType = 'custEstimateType'?>
                        <div class="" >
                            <div >
                                <label class="radio-inline" v-for="(eachValue, eachKey) in getCodeMap()['<?=$listType?>']">
                                    <input type="radio" :name="'<?=$key1?>'"  :value="eachKey" v-model="<?=$key1?>" />
                                    <span class="font-12">{%eachValue%}</span>
                                </label>
                            </div>
                        </div>

                    </td>
                </tr>
                <tr >
                    <th >
                        발송 제목
                    </th>
                    <td colspan="99">
                        <input type="text" class="form-control" v-model="estimateData.subject">
                    </td>
                </tr>
                <tr >
                    <th >
                        비고(고객용)
                    </th>
                    <td colspan="99">
                        <textarea class="form-control" rows="3" v-model="estimateData.estimateMemo"></textarea>
                    </td>
                </tr>
                <tr >
                    <th >
                        내부 열람 메모
                    </th>
                    <td colspan="99">
                        <textarea class="form-control" rows="3" v-model="estimateData.innoverMemo"></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="col-xs-12 new-style">

            <div class="table-title gd-help-manual">
                <div class="flo-left area-title">
                    <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                    견적 정보<span>(VAT 별도)</span>
                    
                    <span class="mgl10 font-13" style="font-weight:normal">
                        <label class="checkbox-inline sl-blue">
                            <input type="checkbox" value="y" v-model="isUpdate" > 현재 스타일의 단가/수량 정보를 이 견적 정보로 업데이트
                        </label>
                    </span>

                </div>
            </div>

            <?php $tableTitles = [
                ['name'=>'번호','col'=>'8'],
                ['name'=>'품명','col'=>'20'],
                ['name'=>'수량','col'=>'13'],
                ['name'=>'단가','col'=>'17'],
                ['name'=>'공급가액','col'=>''],
                ['name'=>'세액','col'=>''],
                ['name'=>'비고','col'=>'21'],
                ['name'=>'기능','col'=>'10'],
            ]; ?>

            <table class="table table-cols " style="margin-bottom:0 !important;">
                <?=\SiteLabUtil\SlCommonUtil::createHtmlTableTitle($tableTitles); ?>
                <tr v-for="(prd, prdIndex) in estimateData.contents">
                    <td>
                        {% prdIndex+1 %}
                        <span class="text-muted">
                            {% prd.styleSno %}
                        </span>
                    </td>
                    <td>
                        <span v-if="prd.styleSno === ''"><input type="text" class="form-control" v-model="prd.name" placeholder="품명"></span>
                        <span v-else>{% prd.name %}</span>
                    </td>
                    <td>
                        <input type="number" class="form-control" v-model="prd.qty" placeholder="수량">
                        <div class="hover-btn cursor-pointer font-11" @click="prd.qty = prd.prdExQty">입력:{% $.setNumberFormat(prd.prdExQty) %}</div>
                    </td>
                    <td><!--단가-->
                        <!--<div class="text-danger">{% $.setNumberFormat(prd.unitPrice) %}</div>-->
                        <input type="number" class="form-control text-danger" v-model="prd.unitPrice" >

                        <div class="font-11" v-if="prd.estimateCost > 0">생산견적:{% $.setNumberFormat(prd.estimateCost) %} ({% getMargin(prd) %}%)</div>
                        <div class="font-11" v-if="0 >= prd.estimateCost">생산견적없음</div>
                    </td>
                    <td>{% $.setNumberFormat(prd.supply) %}</td>
                    <td>{% $.setNumberFormat(prd.tax) %}</td>
                    <td>
                        <input type="text" class="form-control" v-model="prd.etc" placeholder="비고">
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm" @click="addElement(estimateData.contents, estimateData.contents[0], 'down', prdIndex)">추가</div>
                        <div class="btn btn-white btn-sm" @click="deleteElement(estimateData.contents, prdIndex)">제외</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-xs-12 text-right" >
            <table class="table table-cols w-30p " style="margin-left: auto;">
                <colgroup>
                    <col class="w-30p">
                    <col>
                </colgroup>
                <tr>
                    <td>공급가</td>
                    <td>{% $.setNumberFormat(estimateData.supply) %}</td>
                </tr>
                <tr>
                    <td>부가세</td>
                    <td>{% $.setNumberFormat(estimateData.tax) %}</td>
                </tr>
                <tr>
                    <td class="bold text-danger font-15">합계</td>
                    <td class="bold text-danger font-15">{% getTotal %}</td>
                </tr>
            </table>
        </div>

        <div class="col-xs-12 text-center">
            <div class="btn btn-red btn-red-line2 btn-lg hover-btn" @click="sendCustEstimate">견적발송</div>
            <div class="btn btn-white btn-lg" @click="self.close()">닫기</div>
        </div>
    </div>

</section>

<script type="text/javascript">

    console.log('loading...');
    const projectSno = '<?=$requestParam['projectSno']?>';
    $(appId).hide();

    $(()=>{
        const serviceData = {
            serviceValue : {
                estimateData : JSON.parse(`<?=$estimateDataScheme?>`),
                receiverData : {
                    name : '',
                    position : '',
                    mail : '',
                },
                isUpdate : false,
            },serviceMounted : (vueInstance)=>{
                //기본 수신자 (최초 없음처리)
                vueApp.estimateData.receiverInfo.splice(0,1);

                if(!$.isEmpty(vueApp.mainData.customer.contactName) && !$.isEmpty(vueApp.mainData.customer.contactEmail) ){
                    //기본 수신자 설정  (=고객 담당자 정보)
                    vueApp.estimateData.receiverInfo.push(
                        {
                            'name':vueApp.mainData.customer.contactName,
                            'position':vueApp.mainData.customer.contactPosition,
                            'mail':vueApp.mainData.customer.contactEmail,
                        }
                    );
                }

                //기본 상품
                //console.log('상품구조 = List ',vueApp.estimateData.contents);
                vueApp.estimateData.contents.splice(0,1);

                //상품 정보
                for(let key in vueApp.mainData.productList){
                    const prd = vueApp.mainData.productList[key];
                    vueApp.estimateData.contents.push({
                        'styleSno' : prd.sno,
                        'styleCode' : prd.styleCode,
                        'name' : prd.productName,
                        'prdExQty' :prd.prdExQty,
                        'qty' :1,
                        'estimateCost' :prd.estimateCost,
                        'estimateConfirmSno' :prd.estimateConfirmSno,
                        'unitPrice' : prd.salePrice,
                        'supply' : 0,
                        'tax' : 0,
                        'total' :0,
                        'etc' : '',
                    });
                }
                //부가판매(부가구매는 제외) 추가
                let oList = ImsNkService.getList('addedBS', {mode:'getListAddedBS', project_sno:projectSno});
                oList.then((data)=>{
                    $.each(data.data, function(key, val) {
                        if (val.addedType == 1) { //판매건만 견적서에 추가
                            vueApp.estimateData.contents.push({
                                'styleSno' : 0,
                                'styleCode' : '',
                                'name' : val.addedName,
                                'prdExQty' : val.addedQty,
                                'qty' :1,
                                'estimateCost' : val.addedSaleAmount,
                                'estimateConfirmSno' : 0,
                                'unitPrice' : val.addedSaleAmount, //세액 = unitPrice 반올림함
                                'supply' : 0,
                                'tax' : 0,
                                'total' :0,
                                'etc' : '',
                            });
                        }
                    });

                    //기본 제목 -> 0721. 부가판매건도 카운팅 해야돼서 여기에서 정의
                    let sEstimateTitleInfo = vueApp.mainData.project.styleWithCount.split(' 외 ')[0];
                    let iCntEstimateOpt = vueApp.estimateData.contents.length - 1;
                    if (iCntEstimateOpt > 0) sEstimateTitleInfo = sEstimateTitleInfo + ' 외 ' + iCntEstimateOpt + '건';
                    vueApp.estimateData.subject = vueApp.mainData.customer.customerName + '社 ' + sEstimateTitleInfo + ' 견적서';
                });

                //프로젝트/고객 번호
                vueApp.estimateData.customerSno = vueApp.mainData.project.customerSno;
                vueApp.estimateData.projectSno = vueApp.mainData.project.sno;

                //console.log(vueApp.estimateData.contents);
                //console.log(vueApp.mainData.productList);
                //vueApp.estimateData.contents.splice(0,1);
            },serviceMethods : {
                getMargin : (prd)=>{
                    if( prd.estimateCost > 0 && prd.unitPrice > 0){
                        return Math.round(100-(prd.estimateCost/prd.unitPrice*100));
                    }else{
                        return 0;
                    }
                },
                /**
                 * 수신자 추가
                 * @returns {boolean}
                 */
                addReceiver : ()=>{
                    const validCheckData = $.checkObjectEmptyData(vueApp.receiverData,[
                        {key:'name',name:'수신자명'},
                        {key:'mail',name:'이메일 주소'},
                    ]);
                    if( true !== validCheckData ){
                        $.msg(`${validCheckData}은(는) 필수 입니다.`,'','warning');
                        return false;
                    }
                    /*if( !$.validateEmail(vueApp.receiverData.mail) ){
                        $.msg('이메일 형식이 잘 못 되었습니다.','','warning');
                        return false;
                    }*/
                    vueApp.estimateData.receiverInfo.push($.copyObject(vueApp.receiverData));
                    $.clearObject(vueApp.receiverData);
                },

                /**
                 * 견적 발송
                 */
                sendCustEstimate:()=>{
                    //필수 값 확인.
                    const validCheckData = $.checkObjectEmptyData(vueApp.estimateData, [
                        {key:'estimateManagerSno',name:'견적 담당자'},
                        {key:'receiverInfo',name:'수신자 정보',type:'array'},
                        {key:'estimateDt',name:'견적일자'},
                        {key:'subject',name:'발송제목'},
                    ]);
                    if( true !== validCheckData ){
                        $.msg(validCheckData+'은(는) 필수 입니다.','','warning');
                        return false;
                    }

                    //판매가 0원 체크
                    let isPass = true;
                    vueApp.estimateData.contents.forEach((each)=>{
                        if( 0 >= Number(each.qty) ) isPass=false;
                        //if( 0 >= Number(each.unitPrice) ) isPass=false;
                    });
                    if( !isPass ){
                        //$.msg('견적 상품의 판매가 또는 수량은 필수 입니다.','','warning');
                        $.msg('견적 상품의 수량은 필수 입니다.','','warning');
                        return false;
                    }

                    $.msgConfirm('견적서를 발송하시겠습니까?','견적을 발송 후 수정할 수 없습니다. 신중히 발송해주시기 바랍니다.').then(function(result){
                        if( result.isConfirmed ){
                            const sendEstimate = ()=>{
                                $.imsPost('sendCustomerEstimate',vueApp.estimateData).then((data)=>{
                                    $.imsPostAfter(data, (data)=>{
                                        parent.opener.location.reload();
                                        $.msg('발송되었습니다.','','success').then(()=>{
                                            self.close();
                                        });
                                    });
                                });
                            }

                            const saveData = vueApp.estimateData;
                            saveData.isUpdate = vueApp.isUpdate;

                            //console.log('업데이트 여부 확인',saveData.isUpdate);
                            //console.log('결재 여부 확인',vueApp.mainData.project.prdPriceApproval);

                            if( true === saveData.isUpdate && 'p' === vueApp.mainData.project.prdPriceApproval ){
                                //확정된 견적에서 새롭게 업데이트 하는 경우 알림.
                                $.msgConfirm('이미 확정된 판매가입니다. 판매가 업데이트시 재결재가 필요합니다.','계속 진행하시겠습니까?').then(function(result2){
                                    if( result2.isConfirmed ){
                                        saveData.projectSno = vueApp.mainData.project.sno;
                                        saveData.prdPriceApproval = vueApp.mainData.project.prdPriceApproval;
                                        sendEstimate();
                                    }
                                });
                            }else{
                                sendEstimate();
                            }
                        }
                    });
                },

            }, serviceComputed : {
                getTotal : function(){
                    let total = 0;
                    let totalSupply = 0;
                    let totalTax = 0;
                    for(let key in this.estimateData.contents){
                        const prd = this.estimateData.contents[key];
                        if( Number(prd.unitPrice) > 0 ){
                            prd.supply = Number(prd.unitPrice) * Number(prd.qty);
                            prd.tax = Math.round(Number(prd.unitPrice/10) * Number(prd.qty));
                            totalSupply += Number(prd.supply);
                            totalTax += Number(prd.tax);
                            total += Number(prd.supply)+Number(prd.tax);
                        }else{
                            //prd.unitPrice = 0;
                        }
                    }
                    this.estimateData.supply = totalSupply;
                    this.estimateData.tax = totalTax;

                    return (total+'').number_format();
                },
            }
        }


        ImsBoneService.serviceBegin(DATA_MAP.PROJECT,{sno:projectSno},serviceData);


    });
</script>

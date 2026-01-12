<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .page-header > .btn-group > .btn { line-height: 35px!important; }

    .table td { padding:3px 6px!important; }

    .bootstrap-filestyle input{display: none }
    .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">25 ALL 조끼의 생산 견적 요청</h3>
        <div class="btn-group font-18 bold">
            <input type="button" v-show="isModify" @click="saveEstimateCostReq(0)" value="임시저장" class="btn btn-lg btn-red btn-red2 " />
            <input type="button" v-show="isModify" @click="saveEstimateCostReq(1)" value="요청" class="btn btn-lg btn-red btn-red2 " />
            <input type="button" @click="self.close()" value="닫기" class="btn btn-white" />
        </div>
    </div>
    <div class="mgt10">
        <div class="table-title gd-help-manual"><div class="font-18"># 생산견적 요청</div></div>
        <table class="table table-cols table-pd-5" style="margin-bottom:0 !important;">
            <colgroup>
                <col class="w-10p">
                <col class="w-24p">
                <col class="w-10p">
                <col class="w-24p">
                <col class="w-10p">
                <col class="">
            </colgroup>
            <tbody>
            <tr >
                <th>견적타입</th>
                <td>
                    <label class="radio-inline font-14" style="padding:0">
                        <input type="radio" name="optionAddType"  value="estimate"  v-model="estimateView.estimateType" />가견적
                    </label>
                    <label class="radio-inline font-14" style="padding:0">
                        <input type="radio" name="optionAddType"  value="cost" v-model="estimateView.estimateType" />생산확정견적
                    </label>
                </td>
                <th rowspan="2">요청내용</th>
                <td rowspan="2" class="pd0" >
                    <textarea class="form-control w100" rows="4" v-model="estimateView.reqMemo" placeholder="요청내용"></textarea>
                </td>
                <th rowspan="2">메인원단 생지여부</th>
                <td rowspan="2">
                    <textarea class="form-control w100" rows="4" v-model="estimateView.reqMemo2" placeholder="메인원단 생지여부"></textarea>
                </td>
            </tr>
            <tr >
                <th>의뢰처</th>
                <td>
                    <select2 class="js-example-basic-single" style="width:100%" v-model="estimateView.reqFactory">
                        <option value="0">선택</option>
                        <?php foreach ($produceCompanyList as $key => $value ) { ?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                    </select2>
                </td>
            </tr>
            <tr>
                <th>처리완료D/L</th>
                <td>
                    <date-picker v-model="estimateView.completeDeadLineDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                    <span class="pdl30">
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',1)">+1</div>
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',2)">+2</div>
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',3)">+3</div>
                        <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(estimateView, 'completeDeadLineDt',4)">+4</div>
                    </span>
                </td>
                <th rowspan="2">원단 설명 / MOQ</th>
                <td rowspan="2">
                    <textarea class="form-control w100" rows="4" v-model="estimateView.reqMemo1" placeholder="원단 설명 / MOQ"></textarea>
                </td>
                <th rowspan="2">기능 (단가 변동/벌)</th>
                <td rowspan="2">
                    <textarea class="form-control w100" rows="4" v-model="estimateView.reqMemo3" placeholder="기능 (단가 변동/벌)"></textarea>
                </td>
            </tr>
            <tr >
                <th>견적 수량</th>
                <td>
                    <input type="number" class="form-control h100 font-16" placeholder="수량(숫자만)" v-model="estimateView.estimateCount">
                </td>
                <th></th>
            </tr>
            <tr>
                <td colspan="6">
                    <span class="table-title gd-help-manual"><span class="font-18"># 참고파일</span></span>
                    <ul class="ims-file-list" style="display: inline-block;">
                        <li class="hover-btn" v-for="(file, fileIndex) in estimateView.reqFiles" style="display: inline-block;">
                            <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                        </li>
                    </ul>

                    <form id="estimateFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'estimate' === estimateView.estimateType">
                        <span class="fallback">
                            <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                        </span>
                    </form>

                    <form id="costFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'estimate' !== estimateView.estimateType">
                        <span class="fallback">
                            <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                        </span>
                    </form>

                    <span class="mgl20" >
                        <span class="table-title gd-help-manual" ><span class="font-18"># 기존 견적 불러오기</span></span>
                        <input type="text" v-model="loadEstimateCostSno" placeholder="기존요청번호" class="form-control w-150px font-14" style="height:30px; display: inline;" />
                        <span class="btn btn-gray mgl5" @click="loadBeforeEstimateData()" >기존자료 불러오기</span>
                        <span class="notice-info mgl10" >기존 견적 정보를 불러올 수 있습니다.</span>
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="mgt10">
            <span class="table-title gd-help-manual"><span class="font-18"># 생산가 정보</span></span>
            <span class="font-15 bold text-green">&nbsp; &nbsp; &nbsp; 현재환율 : {% $.setNumberFormat(sCurrDollerRatio) %} ({% sCurrDollerRatioDt %})</span>
            <table class="table table-rows table-default-center">
                <colgroup>
                    <col />
                    <col style="width:6%" />
                    <col style="width:6%" />
                    <col style="width:6%" />
                    <col style="width:6%" />
                    <col style="width:6%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:150px;" />
                    <col style="width:6%;" />
                    <col style="width:7%" />
                    <col style="width:7%" />
                    <col style="width:7%" />
                    <col style="width:7%" />
                    <col style="width:7%" />
                </colgroup>
                <tr>
                    <th >생산가<small class="font-white">(VAT별도)</small></th>
                    <th >원자재 소계</th>
                    <th >부자재 소계</th>
                    <th >기능 소계</th>
                    <th >마크 소계</th>
                    <th >공임</th>
                    <th >기타</th>
                    <th >환율</th>
                    <th >환율기준일</th>
                    <th >마진</th>
                    <th >물류 및 관세</th>
                    <th >관리비</th>
                    <th >생산MOQ</th>
                    <th >단가MOQ</th>
                    <th >MOQ미달 추가금</th>
                </tr>
                <tr>
                    <td>
                        <span class="font-16 text-danger bold">{% $.setNumberFormat(computed_sum_total) %}원</span>
                    </td>
                    <td>{% $.setNumberFormat(iSumFabricAmt) %}원</td>
                    <td>{% $.setNumberFormat(iSumSubFabricAmt) %}원</td>
                    <td>{% $.setNumberFormat(iSumUtilAmt) %}원</td>
                    <td>{% $.setNumberFormat(iSumMarkAmt) %}원</td>
                    <td>{% $.setNumberFormat(iSumLaborAmt) %}원</td>
                    <td>{% $.setNumberFormat(iSumEtcAmt) %}원</td>
                    <td>
                        <input type="text" v-model="estimateView.contents.dollerRatio" class="form-control" placeholder="환율" />
                    </td>
                    <td>
                        <date-picker v-model="estimateView.contents.exchangeDt" value-type="format" format="YYYY-MM-DD"  :editable="false" style="margin-left: -30px;"></date-picker>
                    </td>
                    <td>
                        <input type="number" class="form-control text-center" placeholder="마진(숫자만 입력, 원단위)" v-model="estimateView.contents.marginCost" >
                    </td>
                    <td>
                        <input type="number" class="form-control text-center" placeholder="물류 및 관세(숫자만 입력, 원단위)" v-model="estimateView.contents.dutyCost" >
                    </td>
                    <td>
                        <input type="number" class="form-control text-center" placeholder="관리비(숫자만 입력, 원단위)" v-model="estimateView.contents.managementCost">
                    </td>
                    <td>
                        <input type="number" class="form-control text-center" placeholder="생산MOQ" v-model="estimateView.contents.prdMoq">
                    </td>
                    <td>
                        <input type="number" class="form-control text-center" placeholder="단가MOQ" v-model="estimateView.contents.priceMoq">
                    </td>
                    <td>
                        <input type="number" class="form-control text-center" placeholder="MOQ미달 추가금" v-model="estimateView.contents.addPrice">
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <?php $bFlagOpenEstimatePage = true; $sMaterialTargetNm = 'estimateView.contents'; ?>
            <?php include './admin/ims/template/view/materialModule.php'?>
        </div>
        <div class="ta-c mg20">
            <input type="button" v-show="isModify" @click="saveEstimateCostReq(0)" value="임시저장" class="btn btn-red " />
            <input type="button" v-show="isModify" @click="saveEstimateCostReq(1)" value="요청" class="btn btn-red " />
            <input type="button" @click="self.close()" value="닫기" class="btn btn-white" />
        </div>
    </div>
</section>

<script type="text/javascript">
    var igstaticProjectSno = <?=(int)$requestParam['projectSno']?>;
    var igstaticCustomerSno = <?=(int)$requestParam['customerSno']?>;
    var igstaticSampleSno = <?=(int)$requestParam['sampleSno']?>;
    var igstaticStylePlanSno = <?=(int)$requestParam['stylePlanSno']?>;
    var igstaticStyleQty = <?=(int)$requestParam['styleQty']?>;

    const upsertEstimateData = {
        isModify : true,
        sFocusTable : '',
        iFocusIdx : 0,

        loadEstimateCostSno : '',
        estimateView : { reqFiles:[], 'completeDeadLineDt':'',
            'contents':{
                <?php foreach($aContentsSchema as $key => $val) { ?>
                '<?=$key?>':'<?=$val?>',
                <?php } ?>
            },
        },

        sCurrDollerRatio : '<?=$sCurrDollerRatio?>',
        sCurrDollerRatioDt : '<?=$sCurrDollerRatioDt?>',
        sSaveDollerRatio : '<?=$sCurrDollerRatio?>',
        sSaveDollerRatioDt : '<?=$sCurrDollerRatioDt?>',
    };
    const upsertEstimateMethod = {
        //이노버 요청 등록 (estimateView , costView). 팝업창
        saveEstimateCostReq : (reqStatus)=>{
            vueApp.estimateView.sno = 0;
            vueApp.estimateView.contents.exchange = vueApp.estimateView.contents.dollerRatio;
            vueApp.estimateView.reqStatus = reqStatus;

            $.imsPost('saveEstimateCostReq', vueApp.estimateView).then((data) => {
                if( 200 === data.code ){
                    $.msg(1===reqStatus ? '요청 완료' : '저장 완료', "", "success").then(()=>{
                        parent.opener.location.reload();
                        window.close();
                    });
                }
            });
        },

        //기존 견적 자료 불러오기
        loadBeforeEstimateData: ()=>{
            $.imsPost('loadEstimate', {
                loadEstimateSno : vueApp.loadEstimateCostSno
            }).then((data) => {
                if( 200 === data.code ){
                    const copyFieldList = [
                        'fabric', 'subFabric', 'jsonUtil', 'jsonMark', 'jsonLaborCost', 'jsonEtc',
                        'totalCost', 'fabricCost', 'subFabricCost', 'utilCost', 'markCost', 'etcCost',
                        'laborCost', 'marginCost', 'dutyCost', 'managementCost',
                        'prdMoq', 'priceMoq', 'addPrice' ,'deliveryType' , 'produceType', 'producePeriod'
                    ];
                    copyFieldList.forEach((field)=>{
                        vueApp.estimateView.contents[field] = data.data[field];
                    });
                    vueApp.loadEstimateCostSno = '';

                    vueApp.refreshMateSelectboxAll();
                }
            });
        },
    };
    const upsertEstimateComputed = {
        computed_sum_total() {
            this.estimateView.contents.fabricCost = this.iSumFabricAmt;
            this.estimateView.contents.subFabricCost = this.iSumSubFabricAmt;
            this.estimateView.contents.utilCost = this.iSumUtilAmt;
            this.estimateView.contents.markCost = this.iSumMarkAmt;
            this.estimateView.contents.laborCost = this.iSumLaborAmt;
            this.estimateView.contents.etcCost = this.iSumEtcAmt;
            this.estimateView.contents.totalCost = this.iSumFabricAmt + this.iSumSubFabricAmt + this.iSumUtilAmt + this.iSumMarkAmt + this.iSumLaborAmt + this.iSumEtcAmt;
            return this.estimateView.contents.totalCost;
        }
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData, Object.assign({}, upsertEstimateData, materialModuleData));

        ImsBoneService.setMethod(serviceData, Object.assign({}, upsertEstimateMethod, materialModuleMethods));

        ImsBoneService.setComputed(serviceData, Object.assign({}, upsertEstimateComputed, materialModuleComputed));

        ImsBoneService.setMounted(serviceData, ()=>{
            $('.set-dropzone').addClass('dropzone');
            ImsService.setDropzone(vueApp, 'estimateFile1', ImsProductService.uploadAfterActionEstimate); //견적요청파일
            ImsService.setDropzone(vueApp, 'costFile1', ImsProductService.uploadAfterActionCost); //확정요청파일

            let sGetMethodNm = '';
            let oMethodParam = {};
            if (igstaticStylePlanSno > 0) {
                sGetMethodNm = 'stylePlan';
                oMethodParam = {'productPlanSno':igstaticStylePlanSno};
            }
            if (igstaticSampleSno > 0) {
                sGetMethodNm = 'productSample';
                oMethodParam = {'upsertSnoGet':igstaticSampleSno};
            }
            if (sGetMethodNm != '') {
                ImsNkService.getList(sGetMethodNm, oMethodParam).then((data)=>{
                    $.imsPostAfter(data, (data) => {
                        if(data.list != undefined && data.list.length === 1) {
                            //이 페이지는 어차피 insert전용이니까 아래 값들 막 넣어도 됨
                            vueApp.estimateView.customerSno = igstaticCustomerSno;
                            vueApp.estimateView.projectSno = igstaticProjectSno;
                            vueApp.estimateView.styleSno = Number(data.list[0].styleSno);
                            vueApp.estimateView.estimateType = 'estimate';
                            vueApp.estimateView.estimateCount = igstaticStyleQty;

                            vueApp.estimateView.contents.customerSno = vueApp.estimateView.customerSno;
                            $.each(vueApp.estimateView.contents, function (key, val) {
                                if (val === 'Array') vueApp.estimateView.contents[key] = data.list[0][key];
                            });
                            vueApp.sSaveDollerRatio = data.list[0].dollerRatio;
                            vueApp.sSaveDollerRatioDt = data.list[0].dollerRatioDt;
                            vueApp.estimateView.contents.dollerRatio = data.list[0].dollerRatio;
                            vueApp.estimateView.contents.exchange = data.list[0].dollerRatio;
                            vueApp.estimateView.contents.exchangeDt = data.list[0].dollerRatioDt;

                            vueApp.materialModuleInit();

                            vueApp.refreshMateSelectboxAll();
                        } else {
                            $.msg('접근오류','','error').then(()=>{
                                self.close();
                            });
                        }
                    });
                });
            } else {
                $.msg('접근오류','','error').then(()=>{
                    self.close();
                });
            }
        });
        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });



</script>

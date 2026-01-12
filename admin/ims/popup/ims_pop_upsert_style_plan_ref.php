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
        <h3 class="">스타일기획 레퍼런스 {% oUpsertInfo.sno == 0 ? '등록' : (!isModify ? '상세' : '수정') %} <span v-show="isModify" class="font-13 text-danger">* : 필수입력</span></h3>
        <div v-if="oUpsertInfo.sno > 0" class="btn-group font-18 bold">
            <span v-show="!isModify" @click="isModify=true" class="btn btn-red btn-red-line2">수정</span>
            <span v-show="isModify" @click="save()" class="btn btn-red">저장</span>
            <span v-show="isModify" @click="isModify=false" class="btn btn-white">수정취소</span>
            <span @click="self.close()" class="btn btn-white">닫기</span>
        </div>
    </div>
    <div class="mgt10">
        <div class="table-title gd-help-manual"><div class="font-18">기본 정보</div></div>
        <table class="table table-cols table-pd-3 table-th-height30 table-td-height30">
            <colgroup>
                <col class="w-80px">
                <col class="w-300px">
                <col class="w-80px">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th rowspan="8">썸네일</th>
                <td rowspan="8" class="ta-c" style="height:300px!important; overflow-y: hidden!important;">
                    <img :src="oUpsertInfo.refThumbImg==null||oUpsertInfo.refThumbImg==''?'/data/commonimg/ico_noimg_300.gif':oUpsertInfo.refThumbImg" style="max-width:100%; max-height:300px;" />
                </td>
                <th>레퍼런스명 <span class="font-13 text-danger" style="">*</span></th>
                <td>
                    <?php $model='oUpsertInfo.refName'; $placeholder='레퍼런스명' ?>
                    <?php include './admin/ims/template/basic_view/_text.php'?>
                </td>
            </tr>
            <tr>
                <th>시즌 {% computed_change_ref_name %}</th>
                <td>
                    <div v-show="isModify">
                        <select v-model="oUpsertInfo.refSeason" @change="sChgNm1 = event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                            <option value="">공통</option>
                            <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>">(<?=$codeKey?>) <?=$codeValue?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div v-show="!isModify" >
                        <span v-if="oUpsertInfo.refSeason==''">공통</span>
                        <span v-else>({% oUpsertInfo.refSeason %}) {% oUpsertInfo.refSeasonHan %}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>스타일코드</th>
                <td>
                    <div v-show="isModify">
                        <select v-model="oUpsertInfo.refStyle" @change="sChgNm2 = event.target.options[event.target.selectedIndex].innerHTML;" class="form-control">
                            <option value="">공통</option>
                            <?php foreach($codeStyle as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>"><?=$codeValue?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div v-show="!isModify" >
                        <span v-if="oUpsertInfo.refStyle==''">공통</span>
                        <span v-else>{% oUpsertInfo.refStyleHan %}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>타입</th>
                <td>
                    <div v-show="isModify">
                        <?php foreach( \Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_TYPE as $key => $val){ if ($key > 0) { ?>
                            <label class="mgr10">
                                <input type="checkbox" name="aChkboxType[]" v-model="oUpsertInfo.refType" value="<?=$key?>" class="checkbox-inline chk-progress" /> <?=$val?>
                            </label>
                        <?php }} ?>
                    </div>
                    <div v-show="!isModify">
                        {% oUpsertInfo.refTypeHan %}
                    </div>
                </td>
            </tr>
            <tr>
                <th>성별</th>
                <td>
                    <div v-show="isModify">
                        <?php foreach( \Component\Ims\NkCodeMap::PRODUCT_PLAN_GENDER as $key => $val){ ?>
                            <label class="radio-inline">
                                <input type="radio" name="sRadioGender" v-model="oUpsertInfo.refGender" value="<?=$key?>" /><?=$val?>
                            </label>
                        <?php } ?>
                    </div>
                    <div v-show="!isModify">
                        {% oUpsertInfo.refGenderHan %}
                    </div>
                </td>
            </tr>
            <?php foreach( \Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_INFO_TYPE as $key => $val){ ?>
            <tr>
                <?php if ($key == 4) { ?>
                <td colspan="2">
                    <!--썸네일 업로드-->
                    <div v-show="isModify" class="text-right ims-product-image">
                        <form @submit.prevent="uploadThumbFile">
                            <input :type="'file'" ref="fileThumbElement" style="display: block;width:1px!important;" />
                            <input type="button" class="btn btn-black" value="업로드" @click="uploadThumbFile('fileThumb')" />
                        </form>
                    </div>
                </td>
                <?php } ?>
                <th><?=$val?></th>
                <td>
                    <span v-if="ooAppendList[<?=$key?>] != undefined">
                        <span v-if="isModify">
                            <label v-for="val in ooAppendList[<?=$key?>]" class="cursor-pointer hover-btn mgr10 w-120px">
                                <input type="checkbox" v-model="oUpsertInfo.chkAppendInfo[<?=$key?>]" :value="val.sno" class="checkbox-inline chk-progress" /> {% val.infoName %}
                            </label>
                        </span>
                        <span v-else>{% oUpsertInfo.chkAppendInfoHan[<?=$key?>] %}</span>
                    </span>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="4">
                    <div class="table-title gd-help-manual">
                        <div class="flo-left pdt5 font-16">
                            # 고객사 정보
                            &nbsp; <span v-show="isModify" @click="addElement(aoCustomerRelationList, ooDefaultCustomer, 'after')" class="btn btn-white btn-sm">+ 추가</span>
                        </div>
                    </div>
                    <table class="table table-cols table-default-center table-th-height30 table-td-height30 mgt5">
                        <colgroup>
                            <col class="" />
                            <col class="w-10p" />
                            <col class="w-12p" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-12p" />
                            <col class="w-12p" />
                            <col v-if="isModify" class="w-12p" />
                        </colgroup>
                        <tr>
                            <th>고객사명</th><th>업종</th><th>고객담당자</th><th>영업담당자</th><th>3PL 사용여부</th><th>폐쇄몰 사용여부</th><th>총매입</th><th>총매출</th><th v-if="isModify">기능</th>
                        </tr>
                        <tr v-if="aoCustomerRelationList.length == 0">
                            <td colspan="99">데이터가 없습니다.</td>
                        </tr>
                        <tr v-else v-for="(val, key) in aoCustomerRelationList">
                            <td>
                                <span v-if="isModify">
                                    <input type="text" @click="if (Number(val.customerSno) == 0) $.msg('고객사를 선택하세요','','warning'); else openCustomer(val.customerSno,'project');" v-model="val.customerName" placeholder="고객사명" readonly="readonly" class="form-control cursor-pointer hover-btn" style="display:inline; width:calc(100% - 16px);" />
                                    <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'고객사 검색',width:1200}, 'customerNk', val, ooDefaultCustomer, {}, '')"></i>
                                </span>
                                <span v-else>{% val.customerName %}</span>
                            </td>
                            <td>{% val.cateName %}</td>
                            <td>{% val.contactName %}</td>
                            <td>{% val.salesManagerNm %}</td>
                            <td>{% val.use3pl %}</td>
                            <td>{% val.useMall %}</td>
                            <td>{% $.setNumberFormat(val.customerCost) %}</td>
                            <td>{% $.setNumberFormat(val.customerPrice) %}</td>
                            <td v-if="isModify">
                                <span v-show="isModify" @click="addElement(aoCustomerRelationList, ooDefaultCustomer, 'down', key)" class="btn btn-white btn-sm">+ 추가</span>
                                <span class="btn btn-sm btn-red" @click="deleteElement(aoCustomerRelationList, key);">- 삭제</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table class="table table-cols table-default-center table-th-height30 table-td-height30 mgt5">
                        <colgroup>
                            <col class="" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-10p" />
                            <col class="w-6p" />
                            <col class="w-12p" />
                        </colgroup>
                        <tr>
                            <td colspan="99">
                                <div class="table-title gd-help-manual">
                                    <div class="flo-left pdt5 font-16">
                                        # 제작비 정보
                                        <span v-if="oUpsertInfo.sno != 0">(현재환율 : {% $.setNumberFormat(sCurrDollerRatio) %} ({% sCurrDollerRatioDt %}))</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>총 제작비용</th>
                            <th>원자재 소계</th>
                            <th>부자재 소계</th>
                            <th>기능 소계</th>
                            <th>마크 소계</th>
                            <th>공임 소계</th>
                            <th>기타 소계</th>
                            <th>환율</th>
                            <th :class="String(oUpsertInfo.dollerRatioDt).substring(0,5)=='0000-'?'text-danger':''">환율기준일</th>
                        </tr>
                        <tr>
                            <td>{% $.setNumberFormat(iSumFabricAmt + iSumSubFabricAmt + iSumUtilAmt + iSumMarkAmt + iSumLaborAmt + iSumEtcAmt) %} 원</td>
                            <td>{% $.setNumberFormat(iSumFabricAmt) %} 원</td>
                            <td>{% $.setNumberFormat(iSumSubFabricAmt) %} 원</td>
                            <td>{% $.setNumberFormat(iSumUtilAmt) %} 원</td>
                            <td>{% $.setNumberFormat(iSumMarkAmt) %} 원</td>
                            <td>{% $.setNumberFormat(iSumLaborAmt) %} 원</td>
                            <td>{% $.setNumberFormat(iSumEtcAmt) %} 원</td>
                            <td>
                                <div v-if="isModify">
                                    <input type="text" v-model="oUpsertInfo.dollerRatio" @keyup="gfnChangeDollerRatioDt(oUpsertInfo)" class="form-control" placeholder="환율" />
                                </div>
                                <div v-else>
                                    <div v-if="!$.isEmpty(oUpsertInfo.dollerRatio)">{% $.setNumberFormat(oUpsertInfo.dollerRatio) %}</div>
                                    <div v-else class="text-muted">미입력</div>
                                </div>
                            </td>
                            <td>
                                <span :class="String(oUpsertInfo.dollerRatioDt).substring(0,5)=='0000-'?'text-danger':''">
                                    <div v-if="isModify">
                                        <date-picker v-model="oUpsertInfo.dollerRatioDt" value-type="format" format="YYYY-MM-DD"></date-picker>
                                    </div>
                                    <div v-else>
                                        {% oUpsertInfo.dollerRatioDt %}
                                    </div>
                                </span>
                            </td>
                        </tr>
                    </table>

                    <?php $sMaterialTargetNm = 'oUpsertInfo'; ?>
                    <?php include './admin/ims/template/view/materialModule.php'?>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="dp-flex" style="justify-content: center">
            <div v-show="!isModify" @click="isModify=true;" class="btn btn-red btn-lg btn-red-line2 mg5" >수정</div>
            <div v-show="isModify" @click="save()" class="btn btn-red btn-lg mg5">저장</div>
            <div v-show="isModify && oUpsertInfo.sno != 0" @click="isModify=false" class="btn btn-white btn-lg mg5" >수정취소</div>
            <div @click="self.close()" class="btn btn-white btn-lg mg5">닫기</div>
        </div>
    </div>
</section>
<script type="text/javascript">
    const iRefSno = <?=(int)$requestParam['sno']?>;

    const upsertStylePlanRefData = {
        isModify : iRefSno == 0 ? true : false,
        sFocusTable : '',
        iFocusIdx : 0,
        oUpsertInfo : { sno:iRefSno },
        ooAppendList : {},
        sSaveDollerRatio : '',
        sSaveDollerRatioDt : '',
        sCurrDollerRatio : '',
        sCurrDollerRatioDt : '',
        sChgNm1 : '', //시즌의 한글명
        sChgNm2 : '', //스타일코드의 한글명
        aoCustomerRelationList : [], //연결된 고객사리스트
        ooDefaultCustomer : {'customerSno':'sno', 'customerName':'customerName', 'cateName':'cateName', 'contactName':'contactName', 'salesManagerNm':'salesManagerNm', 'use3pl':'use3pl', 'useMall':'useMall', 'customerCost':'customerCost', 'customerPrice':'customerPrice' }, //연결된 고객사 기본폼
    };
    const upsertStylePlanRefMethod = {
        save : ()=>{
            if (vueApp.oUpsertInfo.refName == '') {
                $.msg('레퍼런스명을 입력해주세요','','warning');
                return false;
            }

            //타입 checkbox 체크값 합산
            let iSumChkedVal = 0;
            $.each(document.getElementsByName('aChkboxType[]'), function (key, val) {
                if (this.checked === true) iSumChkedVal += Number(this.value);
            });
            vueApp.oUpsertInfo.refType = iSumChkedVal;
            //단가(자동계산) 컬럼값 구하기
            vueApp.oUpsertInfo.refUnitPrice = vueApp.iSumFabricAmt + vueApp.iSumSubFabricAmt + vueApp.iSumUtilAmt + vueApp.iSumMarkAmt + vueApp.iSumLaborAmt + vueApp.iSumEtcAmt;

            $.imsPost('setStylePlanRef', {'data':vueApp.oUpsertInfo, 'data_cust':vueApp.aoCustomerRelationList}).then((data) => {
                $.imsPostAfter(data,(data)=>{
                    $.msg('레퍼런스 저장 완료.', "", "success").then(()=>{
                        if (parent.opener && typeof parent.opener.refreshRefStylePlanList === 'function') {
                            parent.opener.refreshRefStylePlanList();
                        }
                        if (vueApp.oUpsertInfo.sno == 0) self.close();
                        else location.reload();
                    });
                });
            });

        },
        //썸네일 업로드
        uploadThumbFile : (sFileType)=>{
            const fileInput = vueApp.$refs[sFileType+'Element'];
            if (fileInput.files.length > 0) {
                const formData = new FormData();
                formData.append('upfile', fileInput.files[0]);
                $.ajax({
                    url: '<?=$nasUrl?>/img_upload.php?projectSno=0',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result){
                        const rslt = JSON.parse(result);
                        vueApp.oUpsertInfo.refThumbImg = '<?=$nasUrl?>'+rslt.downloadUrl;
                    }
                });
            }
        },
    };
    const upsertStylePlanRefComputed = {
        computed_change_ref_name() {
            if (this.oUpsertInfo.sno == 0) {
                let sChgNm = '';
                if (this.sChgNm1 == '공통' || this.sChgNm1 == '' || this.sChgNm1 == null) sChgNm = '';
                else sChgNm = this.sChgNm1.split(') ')[1]+' ';
                if (this.sChgNm2 != '공통' && this.sChgNm2 != '' && this.sChgNm2 != null) sChgNm = sChgNm+this.sChgNm2;
                this.oUpsertInfo.refName = sChgNm;
            }
        },
    };


    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData, Object.assign({}, upsertStylePlanRefData, materialModuleData));

        ImsBoneService.setMethod(serviceData, Object.assign({}, upsertStylePlanRefMethod, materialModuleMethods));

        ImsBoneService.setComputed(serviceData, Object.assign({}, upsertStylePlanRefComputed, materialModuleComputed));

        ImsBoneService.setMounted(serviceData, ()=>{
            ImsNkService.getList('stylePlanRef', {'upsertSnoGet':iRefSno}).then((data)=>{
                $.imsPostAfter(data, (data) => {
                    vueApp.oUpsertInfo = data.info;
                    vueApp.ooAppendList = data.info_append;
                    vueApp.aoCustomerRelationList = data.info_customer;

                    vueApp.sSaveDollerRatio = vueApp.oUpsertInfo.dollerRatio;
                    vueApp.sSaveDollerRatioDt = vueApp.oUpsertInfo.dollerRatioDt;
                    vueApp.sCurrDollerRatio = data.info_curr_doller.dollerRatio;
                    vueApp.sCurrDollerRatioDt = data.info_curr_doller.dollerRatioDt;

                    vueApp.materialModuleInit();
                });
            });
        });
        ImsBoneService.serviceBegin('data',{sno:0},serviceData);
    });
</script>





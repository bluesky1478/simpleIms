<?php
use Component\Ims\ImsDBName;
?>
<?php include './admin/ims/library_nk_sch_modal.php'?>
<?php include './admin/ims/library_nk_sch_multi_modal.php'?>
<div>
    <!--6개 리스트 금액합산 : 세부견적에 뿌려줄 변수에 값 넣음 and 환율대로 단가계산-->
    <div>{% computed_sum_fabric %} {% computed_sum_sub_fabric %} {% computed_sum_util %} {% computed_sum_mark %} {% computed_sum_labor %} {% computed_sum_etc %}</div>
    <!--원단 리스트(등록/수정)-->
    <table v-if="isModify" class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:50px;" />
            <col style="width:50px;" />
            <col style="width:90px;" />
            <col style="width:50px;" />
            <col style="width:86px;" />
            <col />
            <col style="width:75px;" />
            <col style="width:65px;" />
            <col style="width:70px;" />
            <col style="width:80px;" />
            <col style="width:75px;" />
            <col style="width:75px;" />
            <col style="width:65px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:45px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:45px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:60px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:100px;" />
            <col style="width:110px;" />
            <col style="width:50px;" />
            <col style="width:65px;" />
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 원단 정보

                        <div class="ims-btn-group pdl10">
                            <button type="button" class="ims-s-btn ims-s-btn-white"
                                    @click="appendMaterialRow(<?=$sMaterialTargetNm?>.fabric, ooDefaultJson.fabric, -1, 'Fabric');">
                                <i class="fa fa-plus" aria-hidden="true"></i> 추가
                            </button>

                            <button type="button" class="ims-s-btn ims-s-btn-blue-outline noto "
                                    @click="schListMultiModalServiceNk.popup({title:'원단 검색(다중 선택)'}, 'material', <?=$sMaterialTargetNm?>.fabric, {'materialSno':'sno','code':'code','fabricName':'name','fabricMix':'mixRatio','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','onHandYn':'onHandYn','btYn':'btYn','makePeriod':'makePeriod','makePeriodNoOnHand':'makePeriodNoOnHand','grpMaterialNames':'grpMaterialNames', 'cntTestReportByCustomerSno':'cntTestReportByCustomerSno'}, {'sRadioSchMaterialTypeByDetail':1}, 'fnCallbackRevertMaterialInfoMulti');">
                                원단 선택 등록
                            </button>

                            <button type="button" class="ims-s-btn ims-s-btn-blue-outline noto "
                                    @click="schListMultiModalServiceNk.popup({title:'충전재 검색(다중 선택)'}, 'material', <?=$sMaterialTargetNm?>.fabric, {'materialSno':'sno','code':'code','fabricName':'name','fabricMix':'mixRatio','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','onHandYn':'onHandYn','btYn':'btYn','makePeriod':'makePeriod','makePeriodNoOnHand':'makePeriodNoOnHand','grpMaterialNames':'grpMaterialNames', 'cntTestReportByCustomerSno':'cntTestReportByCustomerSno'}, {'sRadioSchMaterialTypeByDetail':2}, 'fnCallbackRevertMaterialInfoMulti');">
                                충전재 선택 등록
                            </button>
                        </div>

                        {% computed_set_no_selectbox %}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th><input type="checkbox" @click="toggleAllChkNk('chkAllChgFabric[]', event.target.checked)" /></th>
            <th>이동</th>
            <th>품목코드</th>
            <th>부위</th>
            <th>부착위치</th>
            <th>자재명</th>
            <th>혼용율<br/>컬러</th>
            <th>규격</th>
            <th>가요척</th>
            <th>단가</th>
            <th>금액</th>
            <th>생산국</th>
            <th>MOQ</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>생지</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>BT</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>생산기간<br/>(有/無)</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>생산처</th>
            <th>비고</th>
            <th>성적서</th>
            <th class="dn_hide">기능</th>
        </tr><!--원단-->
        <tr class="tr_multi_value_change">
            <td colspan="3">일괄작업</td>
            <td colspan="8">
                <div class="dp-flex">
                    <!--//부위selectbox-->
                    <select @change="$refs.textAllChgFabricNo.value=event.target.value; if (event.target.value=='') $refs.textAllChgFabricNo.style.display='inline'; else $refs.textAllChgFabricNo.style.display='none';" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textAllChgFabricNo" class="form-control w-100px font-11" placeholder="부위" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.fabric, 'Fabric', 'no', $refs.textAllChgFabricNo.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.fabric, 'Fabric');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
            <td class="ta-c" colspan="5">
                <div class="dp-flex">
                    <!--//생산국 selectbox-->
                    <select ref="selectAllChgFabricProduceNation" class="form-control" >
                        <option value="">선택</option>
                        <option v-for="val2 in aSelectProduceNation" :value="val2">{% val2 %}</option>
                    </select>
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.fabric, 'Fabric', 'makeNational', $refs.selectAllChgFabricProduceNation.value);" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
            <td class="ta-c" colspan="5">
                <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) {} else { ?>
                <div class="dp-flex" >
                    <select @change="$refs.textAllChgFabricProduceManagerSno.value = event.target.value; if (event.target.value == '') { $refs.textAllChgFabricProduceManager.style.display='inline'; $refs.textAllChgFabricProduceManager.value = '';} else { $refs.textAllChgFabricProduceManager.style.display='none'; $refs.textAllChgFabricProduceManager.value = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="hidden" ref="textAllChgFabricProduceManagerSno" placeholder="생산처sno" class="form-control" />
                    <input type="text" ref="textAllChgFabricProduceManager" placeholder="생산처" class="form-control w-100px font-11" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.fabric, 'Fabric', 'produceManagerSno', $refs.textAllChgFabricProduceManagerSno.value); fnAllChgValue(<?=$sMaterialTargetNm?>.fabric, 'Fabric', 'fabricCompany', $refs.textAllChgFabricProduceManager.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.fabric, 'Fabric');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
                <?php } ?>
            </td>
        </tr>
        <tbody is="draggable" :list="<?=$sMaterialTargetNm?>.fabric" :animation="200" tag="tbody" handle=".handle" @change="refreshMateSelectbox(<?=$sMaterialTargetNm?>.fabric, 'Fabric');">
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.fabric" @focusin="sFocusTable='fabric'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='fabric' && iFocusIdx==key ? 'focused' : ''">
            <td>
                <input type="checkbox" name="chkAllChgFabric[]" />
            </td>
            <td class="handle">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <input type="hidden" v-model="val.materialSno" />
                <input type="text" class="form-control" v-model="val.code" placeholder="품목코드">
            </td>
            <td>
                <!--//부위selectbox-->
                <select ref="selectFabricNo" @change="val.no=event.target.value; if (event.target.value=='') $refs.textFabricNo[key].style.display='inline'; else $refs.textFabricNo[key].style.display='none';" class="form-control">
                    <option value="">직접입력</option>
                    <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                </select>
                <input type="text" ref="textFabricNo" v-model="val.no" class="form-control" placeholder="부위" />
            </td>
            <td>
                <input type="text" class="form-control" v-model="val.attached" placeholder="부착위치">
            </td>
            <td>
                <input type="text" @click="if (Number(val.materialSno) == 0) $.msg('자재를 선택하세요','','warning'); else openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" v-model="val.fabricName" placeholder="자재명" readonly="readonly" class="form-control cursor-pointer hover-btn" style="display:inline; width:calc(100% - 16px);" />
                <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'원단 검색',width:1200}, 'material', val, {'materialSno':'sno','code':'code','fabricName':'name','fabricMix':'mixRatio','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','onHandYn':'onHandYn','btYn':'btYn','makePeriod':'makePeriod','makePeriodNoOnHand':'makePeriodNoOnHand','grpMaterialNames':'grpMaterialNames', 'cntTestReportByCustomerSno':'cntTestReportByCustomerSno'}, {'aChkboxSchMaterialType':[1,2], 'aChkboxSchMaterialSt':[1]}, 'fnCallbackRevertMaterialInfo')"></i>
            </td>
            <td>
                <input type="text" class="form-control" v-model="val.fabricMix" placeholder="혼용율">
                <input type="text" class="form-control" v-model="val.color" placeholder="컬러">
            </td>
            <td>
                <input type="text" class="form-control" v-model="val.spec" placeholder="규격">
            </td>
            <td>
                <input type="text" class="form-control" v-model="val.meas" placeholder="가요척">
            </td>
            <td>
                <select v-model="val.currencyUnit" @change="val.unitPriceDoller = ''; val.unitPrice = ''; if (event.target.value == 1) { $refs.textFabricUnitPriceDoller[key].style.display='none'; $refs.textFabricUnitPrice[key].style.display='block'; } else { $refs.textFabricUnitPriceDoller[key].style.display='block'; $refs.textFabricUnitPrice[key].style.display='none'; }" class="form-control">
                    <option v-for="(val2, key2) in oSelectCurrencyUnit" :value="key2">{% val2 %}</option>
                </select>
                <input type="text" ref="textFabricUnitPriceDoller" v-model="val.unitPriceDoller" placeholder="달러" class="form-control" />
                <input type="number" ref="textFabricUnitPrice" v-model="val.unitPrice" placeholder="원화" class="form-control" />
            </td>
            <td>
                {% $.setNumberFormat(Math.ceil(Number(val.unitPrice) * Number(val.meas))) %}
            </td>
            <td>
                <select v-model="val.makeNational" class="form-control" >
                    <option value="">선택</option>
                    <option v-for="val2 in aSelectProduceNation" :value="val2">{% val2 %}</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" v-model="val.moq" placeholder="MOQ">
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <select v-model="val.onHandYn" class="form-control" >
                    <option value="">선택</option>
                    <option value="O">O</option>
                    <option value="X">X</option>
                    <option value="미확인">미확인</option>
                </select>
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <select v-model="val.btYn" class="form-control" >
                    <option value="">선택</option>
                    <option value="O">O</option>
                    <option value="X">X</option>
                </select>
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <input type="text" class="form-control" v-model="val.makePeriod" placeholder="有">
                <input type="text" class="form-control" v-model="val.makePeriodNoOnHand" placeholder="無">
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <!--//생산처 selectbox-->
                <select ref="selectFabricProduceManager" @change="val.produceManagerSno = event.target.value; if (event.target.value == '') { $refs.textFabricProduceManager[key].style.display='inline'; val.fabricCompany = '';} else { $refs.textFabricProduceManager[key].style.display='none'; val.fabricCompany = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                    <option value="">직접입력</option>
                    <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                </select>
                <input type="text" ref="textFabricProduceManager" v-model="val.fabricCompany" placeholder="생산처" class="form-control" />
            </td>
            <td>
                <input type="text" class="form-control" v-model="val.memo" placeholder="비고">
            </td>
            <td>
                <span @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno, 'defaultTabNum':3});" class="cursor-pointer hover-btn">
                    <span v-if="val.cntTestReportByCustomerSno == undefined || (typeof val.cntTestReportByCustomerSno != 'array' && typeof val.cntTestReportByCustomerSno != 'object')"></span>
                    <span v-else-if="(typeof val.cntTestReportByCustomerSno == 'array' && val.cntTestReportByCustomerSno.length==0)||(typeof val.cntTestReportByCustomerSno == 'object' && Object.keys(val.cntTestReportByCustomerSno).length==0)"><i class="fa fa-lg fa-minus-circle sl-red" aria-hidden="true"></i></span>
                    <span v-else-if="val.cntTestReportByCustomerSno[<?=$sMaterialTargetNm?>.customerSno]==undefined"><i class="fa fa-lg fa-info-circle sl-orange" aria-hidden="true"></i></span>
                    <span v-else><i class="fa fa-lg fa-check-circle sl-green" aria-hidden="true"></i></span>
                </span>
            </td>
            <td>
                <button type="button" class="btn btn-white btn-sm" @click="appendMaterialRow(<?=$sMaterialTargetNm?>.fabric, ooDefaultJson.fabric, key, 'Fabric');">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteElement(<?=$sMaterialTargetNm?>.fabric, key); refreshMateSelectbox(<?=$sMaterialTargetNm?>.fabric, 'Fabric');">- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
    <!--원단 리스트(열람/엑셀)-->
    <table v-else class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:80px;" />
            <col style="width:50px;" />
            <col style="width:80px;" />
            <col />
            <col style="width:75px;" />
            <col style="width:65px;" />
            <col style="width:65px;" />
            <col style="width:70px;" />
            <col style="width:90px;" />
            <col style="width:75px;" />
            <col style="width:75px;" />
            <col style="width:65px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:45px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:45px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:50px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:50px;" />
            <col <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> style="width:75px;" />
            <col style="width:75px;" />
            <col style="width:50px;" />
            <col style="width:75px;" />
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 원단 정보
                        {% computed_set_no_selectbox %}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>품목코드</th>
            <th>부위</th>
            <th>부착위치</th>
            <th>자재명</th>
            <th>혼용율</th>
            <th>컬러</th>
            <th>규격</th>
            <th>가요척</th>
            <th>단가</th>
            <th>금액</th>
            <th>생산국</th>
            <th>MOQ</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>생지</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>BT</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?> colspan="2">생산기간(有/無)</th>
            <th <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>생산처</th>
            <th>비고</th>
            <th>성적서</th>
            <th class="dn_hide">기능</th>
        </tr><!--원단-->
        <tbody>
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.fabric" @focusin="sFocusTable='fabric'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='fabric' && iFocusIdx==key ? 'focused' : ''">
            <td>
                <input type="hidden" v-model="val.materialSno" />
                <div v-if="!$.isEmpty(val.code)">{% val.code %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.no)">{% val.no %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.attached)">{% val.attached %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.fabricName)">
                    <span :title="val.grpMaterialNames" >
                        <span v-if="Number(val.materialSno) != 0" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" class="sl-blue cursor-pointer hover-btn">{% val.fabricName %}</span>
                        <span v-else>{% val.fabricName %}</span>
                    </span>
                </div>
                <div v-else class="text-muted">
                    미확인
                </div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.fabricMix)">{% val.fabricMix %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.color)">{% val.color %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.spec)">{% val.spec %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.meas)">{% val.meas %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <span v-if="!$.isEmpty(val.unitPriceDoller) && Number(val.unitPriceDoller) > 0">{% $.setNumberFormat(val.unitPriceDoller) %}$ /</span>
                <span v-if="!$.isEmpty(val.unitPrice)">{% $.setNumberFormat(val.unitPrice) %}\</span>
                <span v-else class="text-muted">미확인</span>
            </td>
            <td>
                {% $.setNumberFormat(Math.ceil(Number(val.unitPrice) * Number(val.meas))) %}
            </td>
            <td>
                {% val.makeNational %}
            </td>
            <td>
                <div v-if="!$.isEmpty(val.moq)">{% val.moq %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                {% val.onHandYn %}
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                {% val.btYn %}
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <div v-if="!$.isEmpty(val.makePeriod)">{% val.makePeriod %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <div v-if="!$.isEmpty(val.makePeriodNoOnHand)">{% val.makePeriodNoOnHand %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td <?php if (isset($bFlagOpenEstimatePage) && $bFlagOpenEstimatePage === true) echo 'v-show="false"'; ?>>
                <div v-if="!$.isEmpty(val.fabricCompany)">{% val.fabricCompany %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <div v-if="!$.isEmpty(val.memo)">{% val.memo %}</div>
                <div v-else class="text-muted">미확인</div>
            </td>
            <td>
                <span @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno, 'defaultTabNum':3});" class="cursor-pointer hover-btn">
                    <span v-if="val.cntTestReportByCustomerSno == undefined || (typeof val.cntTestReportByCustomerSno != 'array' && typeof val.cntTestReportByCustomerSno != 'object')"></span>
                    <span v-else-if="(typeof val.cntTestReportByCustomerSno == 'array' && val.cntTestReportByCustomerSno.length==0)||(typeof val.cntTestReportByCustomerSno == 'object' && Object.keys(val.cntTestReportByCustomerSno).length==0)"><i class="fa fa-lg fa-minus-circle sl-red" aria-hidden="true"></i></span>
                    <span v-else-if="val.cntTestReportByCustomerSno[<?=$sMaterialTargetNm?>.customerSno]==undefined"><i class="fa fa-lg fa-info-circle sl-orange" aria-hidden="true"></i></span>
                    <span v-else><i class="fa fa-lg fa-check-circle sl-green" aria-hidden="true"></i></span>
                </span>
            </td>
            <td>
                <div v-if="<?=$sMaterialTargetNm?>.styleSno != 0" class="btn btn-gray btn-sm" @click="ImsProductService.addQb(val, <?=$sMaterialTargetNm?>.styleSno)">관리등록</div>
            </td>
        </tr>
        </tbody>
    </table>

    <!--부자재 리스트-->
    <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:50px;" v-if="isModify">
            <col style="width:50px;" v-if="isModify">
            <col style="width:95px;">
            <col style="width:75px;">
            <col style="">
            <col style="width:90px;">
            <col style="width:100px;">
            <col style="width:80px;">
            <col style="width:90px;">
            <col style="width:85px;">
            <col style="width:75px;">
            <col style="width:95px;">
            <col style="width:105px;">
            <col style="width:180px;">
            <col style="width:75px;">
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 부자재 정보

                        <div class="ims-btn-group pdl10" v-if="isModify" style="margin-left: 5px; display: inline-flex;">
                            <button type="button" class="ims-s-btn ims-s-btn-white"
                                    @click="appendMaterialRow(<?=$sMaterialTargetNm?>.subFabric, ooDefaultJson.subFabric, -1, 'SubFabric');">
                                <i class="fa fa-plus" aria-hidden="true"></i> 추가
                            </button>

                            <button type="button" class="ims-s-btn ims-s-btn-blue-outline noto"
                                    @click="schListMultiModalServiceNk.popup({title:'부자재 검색(다중 선택)'}, 'material', <?=$sMaterialTargetNm?>.subFabric, {'materialSno':'sno','code':'code','subFabricName':'name','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','grpMaterialNames':'grpMaterialNames'}, {'sRadioSchMaterialTypeByDetail':3}, 'fnCallbackRevertMaterialInfoMulti');">
                                부자재 선택 등록
                            </button>
                        </div>

                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th v-if="isModify"><input type="checkbox" @click="toggleAllChkNk('chkAllChgSubFabric[]', event.target.checked)" /></th>
            <th v-if="isModify">이동</th>
            <th>품목코드</th>
            <th>부위</th>
            <th>자재명</th>
            <th>컬러</th>
            <th>규격</th>
            <th>가요척</th>
            <th>단가</th>
            <th>금액</th>
            <th>생산국</th>
            <th>MOQ</th>
            <th>생산처</th>
            <th>비고</th>
            <th>기능</th>
        </tr>
        <tr v-if="isModify" class="tr_multi_value_change">
            <td colspan="3">일괄작업</td>
            <td colspan="7">
                <div class="dp-flex">
                    <!--//부위selectbox-->
                    <select @change="$refs.textAllChgSubFabricNo.value=event.target.value; if (event.target.value=='') $refs.textAllChgSubFabricNo.style.display='inline'; else $refs.textAllChgSubFabricNo.style.display='none';" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textAllChgSubFabricNo" class="form-control w-100px font-11" placeholder="부위" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric', 'no', $refs.textAllChgSubFabricNo.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
            <td class="ta-c" colspan="2">
                <div class="dp-flex">
                    <select ref="selectAllChgSubFabricProduceNation" class="form-control" >
                        <option value="">선택</option>
                        <option v-for="val2 in aSelectProduceNation" :value="val2">{% val2 %}</option>
                    </select>
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric', 'makeNational', $refs.selectAllChgSubFabricProduceNation.value);" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
            <td class="ta-c" colspan="3">
                <div class="dp-flex">
                    <select @change="$refs.textAllChgSubFabricProduceManagerSno.value = event.target.value; if (event.target.value == '') { $refs.textAllChgSubFabricProduceManager.style.display='inline'; $refs.textAllChgSubFabricProduceManager.value = '';} else { $refs.textAllChgSubFabricProduceManager.style.display='none'; $refs.textAllChgSubFabricProduceManager.value = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="hidden" ref="textAllChgSubFabricProduceManagerSno" placeholder="생산처sno" class="form-control" />
                    <input type="text" ref="textAllChgSubFabricProduceManager" placeholder="생산처" class="form-control w-100px font-11" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric', 'produceManagerSno', $refs.textAllChgSubFabricProduceManagerSno.value); fnAllChgValue(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric', 'company', $refs.textAllChgSubFabricProduceManager.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
        </tr>
        <tbody is="draggable" :list="<?=$sMaterialTargetNm?>.subFabric" :animation="200" tag="tbody" handle=".handle" @change="refreshMateSelectbox(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric');">
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.subFabric" @focusin="sFocusTable='subFabric'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='subFabric' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="isModify">
                <input type="checkbox" name="chkAllChgSubFabric[]" />
            </td>
            <td v-if="isModify" :class="isModify ? 'handle' : ''">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <input type="hidden" v-model="val.materialSno" />
                <?php $model="val.code"; $placeholder='품목코드'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-show="isModify">
                    <!--                            //부위selectbox-->
                    <select ref="selectSubFabricNo" @change="val.no=event.target.value; if (event.target.value=='') $refs.textSubFabricNo[key].style.display='inline'; else $refs.textSubFabricNo[key].style.display='none';" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textSubFabricNo" v-model="val.no" class="form-control" placeholder="부위" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.no)">{% val.no %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <div v-if="isModify">
                    <input type="text" @click="if (Number(val.materialSno) == 0) $.msg('자재를 선택하세요','','warning'); else openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" v-model="val.subFabricName" placeholder="자재명" readonly="readonly" class="form-control cursor-pointer hover-btn" style="display:inline; width:calc(100% - 16px);" />
                    <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'부자재 검색',width:1100}, 'material', val, {'materialSno':'sno','code':'code','subFabricName':'name','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','grpMaterialNames':'grpMaterialNames'}, {'aChkboxSchMaterialType':[3], 'aChkboxSchMaterialSt':[1]}, 'fnCallbackRevertMaterialInfo')"></i>
                </div>
                <div v-else>
                    <div v-if="!$.isEmpty(val.subFabricName)">
                        <span :title="val.grpMaterialNames">
                            <span v-if="Number(val.materialSno) != 0" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" class="sl-blue cursor-pointer hover-btn">{% val.subFabricName %}</span>
                            <span v-else>{% val.subFabricName %}</span>
                        </span>
                    </div>
                    <div v-else class="text-muted">
                        미확인
                    </div>
                </div>
            </td>
            <td>
                <?php $model="val.color"; $placeholder='컬러'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <?php $model="val.spec"; $placeholder='규격'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <?php $model="val.meas"; $placeholder='가요척'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-if="isModify">
                    <select v-model="val.currencyUnit" @change="val.unitPriceDoller = ''; val.unitPrice = ''; if (event.target.value == 1) { $refs.textSubFabricUnitPriceDoller[key].style.display='none'; $refs.textSubFabricUnitPrice[key].style.display='block'; } else { $refs.textSubFabricUnitPriceDoller[key].style.display='block'; $refs.textSubFabricUnitPrice[key].style.display='none'; }" class="form-control">
                        <option v-for="(val2, key2) in oSelectCurrencyUnit" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textSubFabricUnitPriceDoller" v-model="val.unitPriceDoller" placeholder="달러" class="form-control" />
                    <input type="text" ref="textSubFabricUnitPrice" v-model="val.unitPrice" placeholder="원화" class="form-control" />
                </div>
                <div v-else>
                    <span v-if="!$.isEmpty(val.unitPriceDoller) && Number(val.unitPriceDoller) > 0">{% $.setNumberFormat(val.unitPriceDoller) %}$ /</span>
                    <span v-if="!$.isEmpty(val.unitPrice)">{% $.setNumberFormat(val.unitPrice) %}\</span>
                    <span v-else class="text-muted">미확인</span>
                </div>
            </td>
            <td>
                {% $.setNumberFormat(Math.ceil(Number(val.unitPrice) * Number(val.meas))) %}
            </td>
            <td>
                <div v-if="isModify">
                    <select v-model="val.makeNational" class="form-control" >
                        <option value="">선택</option>
                        <option v-for="val2 in aSelectProduceNation" :value="val2">{% val2 %}</option>
                    </select>
                </div>
                <div v-else>
                    {% val.makeNational %}
                </div>
            </td>
            <td>
                <?php $model="val.moq"; $placeholder='MOQ'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-show="isModify">
                    <!--//생산처 selectbox-->
                    <select ref="selectSubFabricProduceManager" @change="val.produceManagerSno = event.target.value; if (event.target.value == '') { $refs.textSubFabricProduceManager[key].style.display='inline'; val.company = '';} else { $refs.textSubFabricProduceManager[key].style.display='none'; val.company = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textSubFabricProduceManager" v-model="val.company" placeholder="생산처" class="form-control" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.company)">{% val.company %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.memo"; $placeholder='비고'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-if="isModify">
                    <button type="button" class="btn btn-white btn-sm" @click="appendMaterialRow(<?=$sMaterialTargetNm?>.subFabric, ooDefaultJson.subFabric, key, 'SubFabric');">+ 추가</button>
                    <div class="btn btn-sm btn-red" @click="deleteElement(<?=$sMaterialTargetNm?>.subFabric, key); refreshMateSelectbox(<?=$sMaterialTargetNm?>.subFabric, 'SubFabric');">- 삭제</div>
                </div>
                <div v-else>
                    <div v-if="<?=$sMaterialTargetNm?>.styleSno != 0" @click="ImsProductService.addSubQb(val, <?=$sMaterialTargetNm?>.styleSno)" class="btn btn-gray btn-sm">관리등록</div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:50px;" v-if="isModify">
            <col style="width:50px;" v-if="isModify">
            <col style="width:150px;">
            <col style="width:100px;">
            <col style="">
            <col style="width:80px;">
            <col style="width:100px;">
            <col style="width:160px;">
            <col style="width:130px;">
            <col style="width:250px;">
            <col style="width:150px;" v-if="isModify">
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 기능 정보
                        <div class="ims-btn-group pdl10" v-if="isModify" style="margin-left: 5px; display: inline-flex;">
                            <button type="button" class="ims-s-btn ims-s-btn-white"
                                    @click="appendMaterialRow(<?=$sMaterialTargetNm?>.jsonUtil, ooDefaultJson.jsonUtil, -1, 'UtilFabric');">
                                <i class="fa fa-plus" aria-hidden="true"></i> 추가
                            </button>

                            <button type="button" class="ims-s-btn ims-s-btn-blue-outline noto"
                                    @click="schListMultiModalServiceNk.popup({title:'기능 검색(다중 선택)'}, 'material', <?=$sMaterialTargetNm?>.jsonUtil, {'materialSno':'sno','code':'code','utilName':'name','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice', 'grpMaterialNames':'grpMaterialNames'}, {'sRadioSchMaterialTypeByDetail':5}, 'fnCallbackRevertMaterialInfoMulti');">
                                기능 선택 등록
                            </button>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th v-if="isModify"><input type="checkbox" @click="toggleAllChkNk('chkAllChgUtilFabric[]', event.target.checked)" /></th>
            <th v-if="isModify">이동</th>
            <th>품목코드</th>
            <th>부위</th>
            <th>기능명</th>
            <th>수량</th>
            <th>단가</th>
            <th>금액</th>
            <th>생산처</th>
            <th>비고</th>
            <th v-if="isModify">기능</th>
        </tr>
        <tr v-if="isModify" class="tr_multi_value_change">
            <td colspan="3">일괄작업</td>
            <td class="ta-l" colspan="5">
                <div class="dp-flex">
                    <!--//부위selectbox-->
                    <select @change="$refs.textAllChgUtilFabricNo.value=event.target.value; if (event.target.value=='') $refs.textAllChgUtilFabricNo.style.display='inline'; else $refs.textAllChgUtilFabricNo.style.display='none';" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textAllChgUtilFabricNo" class="form-control w-100px font-11" placeholder="부위" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric', 'no', $refs.textAllChgUtilFabricNo.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric');" class="btn btn-white" style="width:74px; display: block;">일괄적용</span>
                </div>
            </td>
            <td class="ta-l" colspan="3">
                <div class="dp-flex">
                    <select @change="$refs.textAllChgUtilFabricProduceManagerSno.value = event.target.value; if (event.target.value == '') { $refs.textAllChgUtilFabricProduceManager.style.display='inline'; $refs.textAllChgUtilFabricProduceManager.value = '';} else { $refs.textAllChgUtilFabricProduceManager.style.display='none'; $refs.textAllChgUtilFabricProduceManager.value = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="hidden" ref="textAllChgUtilFabricProduceManagerSno" placeholder="생산처sno" class="form-control" />
                    <input type="text" ref="textAllChgUtilFabricProduceManager" placeholder="생산처" class="form-control w-100px font-11" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric', 'produceManagerSno', $refs.textAllChgUtilFabricProduceManagerSno.value); fnAllChgValue(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric', 'company', $refs.textAllChgUtilFabricProduceManager.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric');" class="btn btn-white" style="width:74px; display: block;">일괄적용</span>
                </div>
            </td>
        </tr>
        <tbody is="draggable" :list="<?=$sMaterialTargetNm?>.jsonUtil" :animation="200" tag="tbody" handle=".handle" @change="refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric');">
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.jsonUtil" @focusin="sFocusTable='jsonUtil'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonUtil' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="isModify">
                <input type="checkbox" name="chkAllChgUtilFabric[]" />
            </td>
            <td v-if="isModify" :class="isModify ? 'handle' : ''">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <input type="hidden" v-model="val.materialSno" />
                <?php $model="val.code"; $placeholder='품목코드'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-show="isModify">
                    <!--                            //부위selectbox-->
                    <select ref="selectUtilFabricNo" @change="val.no=event.target.value; if (event.target.value=='') $refs.textUtilFabricNo[key].style.display='inline'; else $refs.textUtilFabricNo[key].style.display='none';" class="form-control" style="margin:0px auto;">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textUtilFabricNo" v-model="val.no"  placeholder="부위" class="form-control w-70px" style="margin:0px auto;" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.no)">{% val.no %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <div v-if="isModify">
                    <input type="text" @click="if (Number(val.materialSno) == 0) $.msg('자재를 선택하세요','','warning'); else openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" v-model="val.utilName" placeholder="기능명" readonly="readonly" class="form-control cursor-pointer hover-btn" style="display:inline; width:calc(100% - 16px);" />
                    <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'기능 검색'}, 'material', val, {'materialSno':'sno','code':'code','utilName':'name','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice', 'grpMaterialNames':'grpMaterialNames'}, {'aChkboxSchMaterialType':[5], 'aChkboxSchMaterialSt':[1]}, 'fnCallbackRevertMaterialInfo')"></i>
                </div>
                <div v-else>
                    <div v-if="!$.isEmpty(val.utilName)">
                        <span :title="val.grpMaterialNames">
                            <span v-if="Number(val.materialSno) != 0" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" class="sl-blue cursor-pointer hover-btn">{% val.utilName %}</span>
                            <span v-else>{% val.utilName %}</span>
                        </span>
                    </div>
                    <div v-else class="text-muted">
                        미확인
                    </div>
                </div>
            </td>
            <td>
                <?php $model="val.utilQty"; $placeholder='수량'; ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
            </td>
            <td>
                <div v-if="isModify">
                    <select v-model="val.currencyUnit" @change="val.unitPriceDoller = ''; val.unitPrice = ''; if (event.target.value == 1) { $refs.textUtilFabricUnitPriceDoller[key].style.display='none'; $refs.textUtilFabricUnitPrice[key].style.display='block'; } else { $refs.textUtilFabricUnitPriceDoller[key].style.display='block'; $refs.textUtilFabricUnitPrice[key].style.display='none'; }" class="form-control">
                        <option v-for="(val2, key2) in oSelectCurrencyUnit" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textUtilFabricUnitPriceDoller" v-model="val.unitPriceDoller" placeholder="달러" class="form-control" />
                    <input type="text" ref="textUtilFabricUnitPrice" v-model="val.unitPrice" placeholder="원화" class="form-control" />
                </div>
                <div v-else>
                    <span v-if="!$.isEmpty(val.unitPriceDoller) && Number(val.unitPriceDoller) > 0">{% $.setNumberFormat(val.unitPriceDoller) %}$ /</span>
                    <span v-if="!$.isEmpty(val.unitPrice)">{% $.setNumberFormat(val.unitPrice) %}\</span>
                    <span v-else class="text-muted">미확인</span>
                </div>
            </td>
            <td>
                {% $.setNumberFormat(val.utilQty*val.unitPrice) %}
            </td>
            <td>
                <div v-show="isModify">
                    <!--//생산처 selectbox-->
                    <select ref="selectUtilFabricProduceManager" @change="val.produceManagerSno = event.target.value; if (event.target.value == '') { $refs.textUtilFabricProduceManager[key].style.display='inline'; val.company = '';} else { $refs.textUtilFabricProduceManager[key].style.display='none'; val.company = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textUtilFabricProduceManager" v-model="val.company" placeholder="생산처" class="form-control" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.company)">{% val.company %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.memo"; $placeholder='비고'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td v-if="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="appendMaterialRow(<?=$sMaterialTargetNm?>.jsonUtil, ooDefaultJson.jsonUtil, key, 'UtilFabric');">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteElement(<?=$sMaterialTargetNm?>.jsonUtil, key); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric');" >- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:50px;" v-if="isModify">
            <col style="width:50px;" v-if="isModify">
            <col style="width:75px;">
            <col style="width:75px;">
            <col style="">
            <col style="width:90px;">
            <col style="width:100px;">
            <col style="width:80px;">
            <col style="width:90px;">
            <col style="width:95px;">
            <col style="width:85px;">
            <col style="width:75px;">
            <col style="width:75px;">
            <col style="width:180px;">
            <col style="width:75px;" v-if="isModify">
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 마크 정보
                        <div class="ims-btn-group pdl10" v-if="isModify" style="margin-left: 5px; display: inline-flex;">
                            <button type="button" class="ims-s-btn ims-s-btn-white"
                                    @click="appendMaterialRow(<?=$sMaterialTargetNm?>.jsonMark, ooDefaultJson.jsonMark, -1, 'MarkFabric');">
                                <i class="fa fa-plus" aria-hidden="true"></i> 추가
                            </button>

                            <button type="button" class="ims-s-btn ims-s-btn-blue-outline noto"
                                    @click="schListMultiModalServiceNk.popup({title:'마크 검색(다중 선택)'}, 'material', <?=$sMaterialTargetNm?>.jsonMark, {'materialSno':'sno','code':'code','subFabricName':'name','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','grpMaterialNames':'grpMaterialNames'}, {'sRadioSchMaterialTypeByDetail':4}, 'fnCallbackRevertMaterialInfoMulti');">
                                마크 선택 등록
                            </button>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th v-if="isModify"><input type="checkbox" @click="toggleAllChkNk('chkAllChgMarkFabric[]', event.target.checked)" /></th>
            <th v-if="isModify">이동</th>
            <th>품목코드</th>
            <th>부위</th>
            <th>자재명</th>
            <th>컬러</th>
            <th>규격</th>
            <th>가요척</th>
            <th>단가</th>
            <th>금액</th>
            <th>생산국</th>
            <th>MOQ</th>
            <th>생산처</th>
            <th>비고</th>
            <th v-if="isModify">기능</th>
        </tr>
        <tr v-if="isModify" class="tr_multi_value_change">
            <td colspan="3">일괄작업</td>
            <td colspan="7">
                <div class="dp-flex">
                    <!--//부위selectbox-->
                    <select @change="$refs.textAllChgMarkFabricNo.value=event.target.value; if (event.target.value=='') $refs.textAllChgMarkFabricNo.style.display='inline'; else $refs.textAllChgMarkFabricNo.style.display='none';" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textAllChgMarkFabricNo" class="form-control w-100px font-11" placeholder="부위" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric', 'no', $refs.textAllChgMarkFabricNo.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
            <td class="ta-c" colspan="2">
                <div class="dp-flex">
                    <select ref="selectAllChgMarkFabricProduceNation" class="form-control" >
                        <option value="">선택</option>
                        <option v-for="val2 in aSelectProduceNation" :value="val2">{% val2 %}</option>
                    </select>
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric', 'makeNational', $refs.selectAllChgMarkFabricProduceNation.value);" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
            <td class="ta-c" colspan="3">
                <div class="dp-flex">
                    <select @change="$refs.textAllChgMarkFabricProduceManagerSno.value = event.target.value; if (event.target.value == '') { $refs.textAllChgMarkFabricProduceManager.style.display='inline'; $refs.textAllChgMarkFabricProduceManager.value = '';} else { $refs.textAllChgMarkFabricProduceManager.style.display='none'; $refs.textAllChgMarkFabricProduceManager.value = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="hidden" ref="textAllChgMarkFabricProduceManagerSno" placeholder="생산처sno" class="form-control" />
                    <input type="text" ref="textAllChgMarkFabricProduceManager" placeholder="생산처" class="form-control w-100px font-11" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric', 'produceManagerSno', $refs.textAllChgMarkFabricProduceManagerSno.value); fnAllChgValue(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric', 'company', $refs.textAllChgMarkFabricProduceManager.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
        </tr>
        <tbody is="draggable" :list="<?=$sMaterialTargetNm?>.jsonMark" :animation="200" tag="tbody" handle=".handle" @change="refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric');">
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.jsonMark" @focusin="sFocusTable='jsonMark'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonMark' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="isModify">
                <input type="checkbox" name="chkAllChgMarkFabric[]" />
            </td>
            <td v-if="isModify" :class="isModify ? 'handle' : ''">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <input type="hidden" v-model="val.materialSno" />
                <?php $model="val.code"; $placeholder='품목코드'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-show="isModify">
                    <!--                            //부위selectbox-->
                    <select ref="selectMarkFabricNo" @change="val.no=event.target.value; if (event.target.value=='') $refs.textMarkFabricNo[key].style.display='inline'; else $refs.textMarkFabricNo[key].style.display='none';" class="form-control" style="margin:0px auto;">
                        <option value="">직접입력</option>
                        <option v-for="val2 in aSelectFabricNo" :value="val2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textMarkFabricNo" v-model="val.no" class="form-control w-70px" placeholder="부위" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.no)">{% val.no %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <div v-if="isModify">
                    <input type="text" @click="if (Number(val.materialSno) == 0) $.msg('자재를 선택하세요','','warning'); else openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" v-model="val.subFabricName" placeholder="자재명" readonly="readonly" class="form-control cursor-pointer hover-btn" style="display:inline; width:calc(100% - 16px);" />
                    <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'마크 검색',width:1100}, 'material', val, {'materialSno':'sno','code':'code','subFabricName':'name','color':'materialColor','spec':'spec','currencyUnit':'currencyUnit','unitPriceDoller':'unitPriceDoller','unitPrice':'unitPrice','makeNational':'makeNational','moq':'moq','grpMaterialNames':'grpMaterialNames'}, {'aChkboxSchMaterialType':[4], 'aChkboxSchMaterialSt':[1]}, 'fnCallbackRevertMaterialInfo')"></i>
                </div>
                <div v-else>
                    <div v-if="!$.isEmpty(val.subFabricName)">
                        <span :title="val.grpMaterialNames">
                            <span v-if="Number(val.materialSno) != 0" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.materialSno});" class="sl-blue cursor-pointer hover-btn">{% val.subFabricName %}</span>
                            <span v-else>{% val.subFabricName %}</span>
                        </span>
                    </div>
                    <div v-else class="text-muted">
                        미확인
                    </div>
                </div>
            </td>
            <td>
                <?php $model="val.color"; $placeholder='컬러'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <?php $model="val.spec"; $placeholder='규격'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <?php $model="val.meas"; $placeholder='가요척'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-if="isModify">
                    <select v-model="val.currencyUnit" @change="val.unitPriceDoller = ''; val.unitPrice = ''; if (event.target.value == 1) { $refs.textMarkFabricUnitPriceDoller[key].style.display='none'; $refs.textMarkFabricUnitPrice[key].style.display='block'; } else { $refs.textMarkFabricUnitPriceDoller[key].style.display='block'; $refs.textMarkFabricUnitPrice[key].style.display='none'; }" class="form-control">
                        <option v-for="(val2, key2) in oSelectCurrencyUnit" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textMarkFabricUnitPriceDoller" v-model="val.unitPriceDoller" placeholder="달러" class="form-control" />
                    <input type="text" ref="textMarkFabricUnitPrice" v-model="val.unitPrice" placeholder="원화" class="form-control" />
                </div>
                <div v-else>
                    <span v-if="!$.isEmpty(val.unitPriceDoller) && Number(val.unitPriceDoller) > 0">{% $.setNumberFormat(val.unitPriceDoller) %}$ /</span>
                    <span v-if="!$.isEmpty(val.unitPrice)">{% $.setNumberFormat(val.unitPrice) %}\</span>
                    <span v-else class="text-muted">미확인</span>
                </div>
            </td>
            <td>
                {% $.setNumberFormat(Math.ceil(Number(val.unitPrice) * Number(val.meas))) %}
            </td>
            <td>
                <div v-if="isModify">
                    <select v-model="val.makeNational" class="form-control" >
                        <option value="">선택</option>
                        <option v-for="val2 in aSelectProduceNation" :value="val2">{% val2 %}</option>
                    </select>
                </div>
                <div v-else>
                    {% val.makeNational %}
                </div>
            </td>
            <td>
                <?php $model="val.moq"; $placeholder='MOQ'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-show="isModify">
                    <!--//생산처 selectbox-->
                    <select ref="selectMarkFabricProduceManager" @change="val.produceManagerSno = event.target.value; if (event.target.value == '') { $refs.textMarkFabricProduceManager[key].style.display='inline'; val.company = '';} else { $refs.textMarkFabricProduceManager[key].style.display='none'; val.company = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textMarkFabricProduceManager" v-model="val.company" placeholder="생산처" class="form-control" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.company)">{% val.company %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.memo"; $placeholder='비고'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td v-if="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="appendMaterialRow(<?=$sMaterialTargetNm?>.jsonMark, ooDefaultJson.jsonMark, key, 'MarkFabric');">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteElement(<?=$sMaterialTargetNm?>.jsonMark, key); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric');">- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:50px;" v-if="isModify">
            <col style="width:60px;" v-if="isModify">
            <col style="width:150px;">
            <col style="width:300px;">
            <col style="width:125px;">
            <col style="width:100px;">
            <col style="width:125px;">
            <col style="width:75px;">
            <col style="">
            <col style="width:150px;" v-if="isModify">
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 공임비용 정보
                        &nbsp; <span v-if="isModify" @click="addElement(<?=$sMaterialTargetNm?>.jsonLaborCost, ooDefaultJson.jsonLaborCost, 'after')" class="btn btn-white">+ 추가</span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th v-if="isModify"><input type="checkbox" @click="toggleAllChkNk('chkAllChgLaborCost[]', event.target.checked)" /></th>
            <th v-if="isModify">이동</th>
            <th>코드</th>
            <th>항목명</th>
            <th>단가</th>
            <th>수량</th>
            <th>금액</th>
            <th>생산처</th>
            <th>내용</th>
            <th v-if="isModify">기능</th>
        </tr>
        <tr v-if="isModify" class="tr_multi_value_change">
            <td colspan="3">일괄작업</td>
            <td colspan="4"></td>
            <td class="ta-c" colspan="2">
                <div class="dp-flex">
                    <select @change="$refs.textAllChgLaborCostProduceManagerSno.value = event.target.value; if (event.target.value == '') { $refs.textAllChgLaborCostProduceManager.style.display='inline'; $refs.textAllChgLaborCostProduceManager.value = '';} else { $refs.textAllChgLaborCostProduceManager.style.display='none'; $refs.textAllChgLaborCostProduceManager.value = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="hidden" ref="textAllChgLaborCostProduceManagerSno" placeholder="생산처sno" class="form-control" />
                    <input type="text" ref="textAllChgLaborCostProduceManager" placeholder="생산처" class="form-control w-100px font-11" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonLaborCost, 'LaborCost', 'produceManagerSno', $refs.textAllChgLaborCostProduceManagerSno.value); fnAllChgValue(<?=$sMaterialTargetNm?>.jsonLaborCost, 'LaborCost', 'company', $refs.textAllChgLaborCostProduceManager.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonLaborCost, 'LaborCost');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
        </tr>
        <tbody is="draggable" :list="<?=$sMaterialTargetNm?>.jsonLaborCost" :animation="200" tag="tbody" handle=".handle">
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.jsonLaborCost" @focusin="sFocusTable='jsonLaborCost'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonLaborCost' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="isModify">
                <input type="checkbox" name="chkAllChgLaborCost[]" />
            </td>
            <td v-if="isModify" :class="isModify ? 'handle' : ''">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <input type="hidden" v-model="val.etcCostSno" />
                <?php $model="val.code"; $placeholder='코드'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-if="isModify">
                    <input type="text" class="form-control" v-model="val.name" placeholder="항목명" readonly="readonly" style="display:inline; width:calc(100% - 16px);" />
                    <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'공임비용 검색',width:1100}, 'sampleEtcCost', val, {'etcCostSno':'sno','code':'costCode','name':'costName','unitPrice':'costUnitPrice','memo':'costDesc'}, {'aChkboxSchCostType':[1]}, '')"></i>
                </div>
                <div v-else>
                    <div v-if="!$.isEmpty(val.name)">{% val.name %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.unitPrice"; $placeholder='원화'; ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
            </td>
            <td>
                <?php $model="val.costQty"; $placeholder='수량'; ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
            </td>
            <td>
                {% $.setNumberFormat(Math.ceil(Number(val.unitPrice) * Number(val.costQty))) %}
            </td>
            <td>
                <div v-show="isModify">
                    <!--//생산처 selectbox-->
                    <select ref="selectLaborCostProduceManager" @change="val.produceManagerSno = event.target.value; if (event.target.value == '') { $refs.textLaborCostProduceManager[key].style.display='inline'; val.company = '';} else { $refs.textLaborCostProduceManager[key].style.display='none'; val.company = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textLaborCostProduceManager" v-model="val.company" placeholder="생산처" class="form-control" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.company)">{% val.company %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.memo"; $placeholder='내용'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td v-if="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="addElement(<?=$sMaterialTargetNm?>.jsonLaborCost, ooDefaultJson.jsonLaborCost, 'down', key)">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteElement(<?=$sMaterialTargetNm?>.jsonLaborCost, key)">- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-cols table-default-center table-pd-5 table-td-height30 table-th-height30" style="border-top:none!important">
        <colgroup>
            <col style="width:50px;" v-if="isModify">
            <col style="width:60px;" v-if="isModify">
            <col style="width:150px;">
            <col style="width:300px;">
            <col style="width:125px;">
            <col style="width:100px;">
            <col style="width:125px;">
            <col style="width:75px;">
            <col style="">
            <col style="width:150px;" v-if="isModify">
        </colgroup>
        <tr>
            <td colspan="99">
                <div class="table-title gd-help-manual">
                    <div class="flo-left pdt5 font-16">
                        # 기타비용 정보
                        &nbsp; <span v-if="isModify" @click="addElement(<?=$sMaterialTargetNm?>.jsonEtc, ooDefaultJson.jsonEtc, 'after')" class="btn btn-white">+ 추가</span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th v-if="isModify"><input type="checkbox" @click="toggleAllChkNk('chkAllChgEtcCost[]', event.target.checked)" /></th>
            <th v-if="isModify">이동</th>
            <th>코드</th>
            <th>항목명</th>
            <th>단가</th>
            <th>수량</th>
            <th>금액</th>
            <th>생산처</th>
            <th>내용</th>
            <th v-if="isModify">기능</th>
        </tr>
        <tr v-if="isModify" class="tr_multi_value_change">
            <td colspan="3">일괄작업</td>
            <td colspan="4"></td>
            <td class="ta-c" colspan="3">
                <div class="dp-flex">
                    <select @change="$refs.textAllChgEtcCostProduceManagerSno.value = event.target.value; if (event.target.value == '') { $refs.textAllChgEtcCostProduceManager.style.display='inline'; $refs.textAllChgEtcCostProduceManager.value = '';} else { $refs.textAllChgEtcCostProduceManager.style.display='none'; $refs.textAllChgEtcCostProduceManager.value = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="hidden" ref="textAllChgEtcCostProduceManagerSno" placeholder="생산처sno" class="form-control" />
                    <input type="text" ref="textAllChgEtcCostProduceManager" placeholder="생산처" class="form-control w-100px font-11" />
                    <span @click="fnAllChgValue(<?=$sMaterialTargetNm?>.jsonEtc, 'EtcCost', 'produceManagerSno', $refs.textAllChgEtcCostProduceManagerSno.value); fnAllChgValue(<?=$sMaterialTargetNm?>.jsonEtc, 'EtcCost', 'company', $refs.textAllChgEtcCostProduceManager.value); refreshMateSelectbox(<?=$sMaterialTargetNm?>.jsonEtc, 'EtcCost');" class="btn btn-gray btn-sm">일괄적용</span>
                </div>
            </td>
        </tr>
        <tbody is="draggable" :list="<?=$sMaterialTargetNm?>.jsonEtc" :animation="200" tag="tbody" handle=".handle">
        <tr v-for="(val, key) in <?=$sMaterialTargetNm?>.jsonEtc" @focusin="sFocusTable='jsonEtc'; iFocusIdx=key;" @focusout="sFocusTable=''; iFocusIdx=0;" :class="sFocusTable=='jsonEtc' && iFocusIdx==key ? 'focused' : ''">
            <td v-if="isModify">
                <input type="checkbox" name="chkAllChgEtcCost[]" />
            </td>
            <td v-if="isModify" :class="isModify ? 'handle' : ''">
                <div class="cursor-pointer hover-btn" >
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
            </td>
            <td>
                <input type="hidden" v-model="val.etcCostSno" />
                <?php $model="val.code"; $placeholder='코드'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td>
                <div v-if="isModify">
                    <input type="text" class="form-control" v-model="val.name" placeholder="항목명" readonly="readonly" style="display:inline; width:calc(100% - 16px);" />
                    <i class="btn-search fa fa-search cursor-pointer hover-btn" @click="schListModalServiceNk.popup({title:'기타비용 검색',width:1100}, 'sampleEtcCost', val, {'etcCostSno':'sno','code':'costCode','name':'costName','unitPrice':'costUnitPrice','memo':'costDesc'}, {'aChkboxSchCostType':[2]}, '')"></i>
                </div>
                <div v-else>
                    <div v-if="!$.isEmpty(val.name)">{% val.name %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.unitPrice"; $placeholder='원화'; ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
            </td>
            <td>
                <?php $model="val.costQty"; $placeholder='수량'; ?>
                <?php include './admin/ims/template/basic_view/_number.php'?>
            </td>
            <td>
                {% $.setNumberFormat(Math.ceil(Number(val.unitPrice) * Number(val.costQty))) %}
            </td>
            <td>
                <div v-show="isModify">
                    <!--//생산처 selectbox-->
                    <select ref="selectEtcCostProduceManager" @change="val.produceManagerSno = event.target.value; if (event.target.value == '') { $refs.textEtcCostProduceManager[key].style.display='inline'; val.company = '';} else { $refs.textEtcCostProduceManager[key].style.display='none'; val.company = event.target.options[event.target.selectedIndex].innerHTML;}" class="form-control">
                        <option value="">직접입력</option>
                        <option v-for="(val2, key2) in oSelectProduceManager" :value="key2">{% val2 %}</option>
                    </select>
                    <input type="text" ref="textEtcCostProduceManager" v-model="val.company" placeholder="생산처" class="form-control" />
                </div>
                <div v-show="!isModify">
                    <div v-if="!$.isEmpty(val.company)">{% val.company %}</div>
                    <div v-else class="text-muted">미확인</div>
                </div>
            </td>
            <td>
                <?php $model="val.memo"; $placeholder='내용'; ?>
                <?php include './admin/ims/template/basic_view/_text.php'?>
            </td>
            <td v-if="isModify">
                <button type="button" class="btn btn-white btn-sm" @click="addElement(<?=$sMaterialTargetNm?>.jsonEtc, ooDefaultJson.jsonEtc, 'down', key)">+ 추가</button>
                <div class="btn btn-sm btn-red" @click="deleteElement(<?=$sMaterialTargetNm?>.jsonEtc, key)">- 삭제</div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    //materialModule 호출조건(아래 변수,배열 선언안하면 빈값으로 진행시킴)(ex> popup\ims_pop_upsert_style_plan.php, popup\script\ims_pop_upsert_style_plan_script.php)
    //1. script부분에 isModify, sFocusTable, iFocusIdx 변수선언
    //2. vueApp.$sMaterialTargetNm.sno(0이면 원단,부자재 기본1개 row씩 append), vueApp.$sMaterialTargetNm.customerSno(성적서아이콘), vueApp.$sMaterialTargetNm.styleSno(관리등록버튼), vueApp.$sMaterialTargetNm.dollerRatio(원화계산시) 변수선언
    //3. vueApp.$sMaterialTargetNm.fabric, vueApp.$sMaterialTargetNm.subFabric, vueApp.$sMaterialTargetNm.jsonUtil, vueApp.$sMaterialTargetNm.jsonMark, vueApp.$sMaterialTargetNm.jsonLaborCost, vueApp.$sMaterialTargetNm.jsonEtc 배열선언

    //materialModule 호출방법(ex> popup\ims_pop_upsert_style_plan.php, popup\script\ims_pop_upsert_style_plan_script.php)
    //1. view부분에 $sMaterialTargetNm 변수에 값넣고 materialModule.php include
    //2. script부분에 ImsBoneService.setData(), ImsBoneService.setMethod(), ImsBoneService.setComputed() 변경
    //3. ImsBoneService.setMounted() 내부에 vueApp.materialModuleInit();

    //원단정보~마크정보 자재선택 콜백함수
    function fnCallbackRevertMaterialInfo(oTarget) {
        if (oTarget.unitPrice != undefined) oTarget.unitPrice = String(oTarget.unitPrice).replaceAll('\\','').replaceAll(",",'');
        if (oTarget.moq != undefined) oTarget.moq = String(oTarget.moq).replaceAll(",",'');

        vueApp.refreshMateSelectboxAll();
    }
    function fnCallbackRevertMaterialInfoMulti(aoTarget) {
        $.each(aoTarget, function (key, val) {
            if (aoTarget[key].unitPrice != undefined && aoTarget[key].unitPrice != '') aoTarget[key].unitPrice = String(aoTarget[key].unitPrice).replaceAll('\\','').replaceAll(",",'');
            if (aoTarget[key].moq != undefined && aoTarget[key].moq != '') aoTarget[key].moq = String(aoTarget[key].moq).replaceAll(",",'');
        });

        vueApp.refreshMateSelectboxAll();
    }

    const materialModuleData = {
        schListNk : schListModalServiceNk.objDefault,
        schListMultiNk : schListMultiModalServiceNk.objDefault,
        iSumFabricAmt : 0,
        iSumSubFabricAmt : 0,
        iSumUtilAmt : 0,
        iSumMarkAmt : 0,
        iSumLaborAmt : 0,
        iSumEtcAmt : 0,
        //json으로 저장되는 컬럼data의 default form(사이즈스펙2종 + 원부자재6종)
        ooDefaultJson :{},

        //부위selectbox
        aSelectFabricNo : ['G', 'A', 'B', 'C', 'C1', 'C2', 'C3', 'C4', 'C5', 'D', 'E', 'F'],
        //생산처 selectbox
        oSelectProduceManager : {
            <?php foreach ($produceCompanyList as $key => $val) { ?>
            '<?=$key?>':'<?=$val?>',
            <?php } ?>
        },
        //생산국 selectbox. 일괄적용에서 쓰려고 빼놨음
        aSelectProduceNation : [
            <?php foreach( \Component\Ims\ImsCodeMap::PRD_NATIONAL_CODE as $key => $val ) { ?>
            '<?=$val?>',
            <?php } ?>
        ],
        //화폐단위 selectbox
        oSelectCurrencyUnit : {
            <?php foreach (\Component\Ims\NkCodeMap::CURRENCY_UNIT as $key => $val) { ?>
            '<?=$key?>':'<?=$val?>',
            <?php } ?>
        },
    };

    const materialModuleMethods = {
        materialModuleInit : ()=>{
            if (vueApp.isModify == undefined) vueApp.isModify = true;
            if (vueApp.sFocusTable == undefined) vueApp.sFocusTable = '';
            if (vueApp.iFocusIdx == undefined) vueApp.iFocusIdx = 0;
            if (vueApp.<?=$sMaterialTargetNm?>.sno == undefined) vueApp.<?=$sMaterialTargetNm?>.sno = 0;
            if (vueApp.<?=$sMaterialTargetNm?>.customerSno == undefined) vueApp.<?=$sMaterialTargetNm?>.customerSno = 0;
            if (vueApp.<?=$sMaterialTargetNm?>.styleSno == undefined) vueApp.<?=$sMaterialTargetNm?>.styleSno = 0;
            if (vueApp.<?=$sMaterialTargetNm?>.dollerRatio == undefined) vueApp.<?=$sMaterialTargetNm?>.dollerRatio = 0;
            if (vueApp.<?=$sMaterialTargetNm?>.fabric == undefined) vueApp.<?=$sMaterialTargetNm?>.fabric = [];
            if (vueApp.<?=$sMaterialTargetNm?>.subFabric == undefined) vueApp.<?=$sMaterialTargetNm?>.subFabric = [];
            if (vueApp.<?=$sMaterialTargetNm?>.jsonUtil == undefined) vueApp.<?=$sMaterialTargetNm?>.jsonUtil = [];
            if (vueApp.<?=$sMaterialTargetNm?>.jsonMark == undefined) vueApp.<?=$sMaterialTargetNm?>.jsonMark = [];
            if (vueApp.<?=$sMaterialTargetNm?>.jsonLaborCost == undefined) vueApp.<?=$sMaterialTargetNm?>.jsonLaborCost = [];
            if (vueApp.<?=$sMaterialTargetNm?>.jsonEtc == undefined) vueApp.<?=$sMaterialTargetNm?>.jsonEtc = [];

            $.imsPost('getJsonDefaultForm', {'data':'<?=ImsDBName::PRODUCT_PLAN?>'}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    vueApp.ooDefaultJson = data;

                    if(vueApp.<?=$sMaterialTargetNm?>.sno == 0) {
                        if (vueApp.ooDefaultJson.fabric.length === 0) vueApp.appendMaterialRow(vueApp.<?=$sMaterialTargetNm?>.fabric, vueApp.ooDefaultJson.fabric, -1, 'Fabric');
                        if (vueApp.ooDefaultJson.subFabric.length === 0) vueApp.appendMaterialRow(vueApp.<?=$sMaterialTargetNm?>.subFabric, vueApp.ooDefaultJson.subFabric, -1, 'SubFabric');
                    }
                });
            });
        },

        //부위selectbox, 생산처 selectbox 값 조정
        refreshMateSelectbox : (aFabricList, sRefText)=>{
            vueApp.$nextTick(function() {
                if (vueApp.$refs != null && aFabricList != undefined && Array.isArray(aFabricList) === true && aFabricList.length > 0 && sRefText != '') {
                    //원부자재유형에서 부위selectbox 있는지 확인
                    let bFlagEnableSelectNo = false;
                    if (vueApp.$refs['select'+sRefText+'No'] != undefined && vueApp.$refs['select'+sRefText+'No'].length > 0) bFlagEnableSelectNo = true;
                    //원부자재유형에서 생산처 selectbox 있는지 확인
                    let bFlagEnableSelectProduceManager = false;
                    if (vueApp.$refs['select'+sRefText+'ProduceManager'] != undefined && vueApp.$refs['select'+sRefText+'ProduceManager'].length > 0) bFlagEnableSelectProduceManager = true;
                    //원부자재유형에서 화폐단위 selectbox 있는지 확인
                    let bFlagEnableSelectCurrencyUnit = false;
                    if (vueApp.$refs['text'+sRefText+'UnitPriceDoller'] != undefined && vueApp.$refs['text'+sRefText+'UnitPriceDoller'].length > 0) bFlagEnableSelectCurrencyUnit = true;

                    $.each(aFabricList, function (key, val) {
                        //부위selectbox
                        if (bFlagEnableSelectNo === true) {
                            if (val.no != '') {
                                if (vueApp.aSelectFabricNo.indexOf(val.no) == -1) {
                                    vueApp.$refs['select'+sRefText+'No'][key].value = '';
                                    vueApp.$refs['text'+sRefText+'No'][key].style.display = 'inline';
                                } else {
                                    vueApp.$refs['select'+sRefText+'No'][key].value = val.no;
                                    vueApp.$refs['text'+sRefText+'No'][key].style.display = 'none';
                                }
                            } else {
                                vueApp.$refs['select'+sRefText+'No'][key].value = '';
                                vueApp.$refs['text'+sRefText+'No'][key].style.display = 'inline';
                            }
                        }
                        //생산처 selectbox
                        if (bFlagEnableSelectProduceManager === true) {
                            if (val.produceManagerSno != undefined && val.produceManagerSno != '' && val.produceManagerSno != 0 && val.produceManagerSno != '0' && vueApp.oSelectProduceManager[val.produceManagerSno] != undefined) {
                                vueApp.$refs['select'+sRefText+'ProduceManager'][key].value = val.produceManagerSno;
                                vueApp.$refs['text'+sRefText+'ProduceManager'][key].style.display = 'none';
                            } else {
                                vueApp.$refs['select'+sRefText+'ProduceManager'][key].value = '';
                                vueApp.$refs['text'+sRefText+'ProduceManager'][key].style.display = 'inline';
                            }
                        }
                        //화폐단위 selectbox
                        if (bFlagEnableSelectCurrencyUnit === true) {
                            if (val.currencyUnit == undefined || val.currencyUnit == '' || val.currencyUnit == 0) val.currencyUnit = 1;
                            if (val.currencyUnit == 1) {
                                val.unitPriceDoller = '';
                                vueApp.$refs['text'+sRefText+'UnitPriceDoller'][key].style.display = 'none';
                                vueApp.$refs['text'+sRefText+'UnitPrice'][key].style.display = 'block';
                            } else if (val.currencyUnit == 2) {
                                vueApp.$refs['text'+sRefText+'UnitPriceDoller'][key].style.display = 'block';
                                vueApp.$refs['text'+sRefText+'UnitPrice'][key].style.display = 'none';
                            }
                        }
                    });
                }
            });
        },
        refreshMateSelectboxAll : ()=>{
            vueApp.refreshMateSelectbox(vueApp.<?=$sMaterialTargetNm?>.fabric, 'Fabric');
            vueApp.refreshMateSelectbox(vueApp.<?=$sMaterialTargetNm?>.subFabric, 'SubFabric');
            vueApp.refreshMateSelectbox(vueApp.<?=$sMaterialTargetNm?>.jsonUtil, 'UtilFabric');
            vueApp.refreshMateSelectbox(vueApp.<?=$sMaterialTargetNm?>.jsonMark, 'MarkFabric');
            vueApp.refreshMateSelectbox(vueApp.<?=$sMaterialTargetNm?>.jsonLaborCost, 'LaborCost');
            vueApp.refreshMateSelectbox(vueApp.<?=$sMaterialTargetNm?>.jsonEtc, 'EtcCost');
        },
        //원단, 부자재, 기능, 마크 row추가 함수 만들것 ()
        appendMaterialRow : (oTarget, oDefaultForm, iKey, sMateType)=>{
            let oAppend = {};
            if (iKey == -1) {
                oAppend = vueApp.addElement(oTarget, oDefaultForm, 'after');
            } else {
                oAppend = vueApp.addElement(oTarget, oDefaultForm, 'down', iKey);
            }
            oAppend.currencyUnit = 1;

            vueApp.refreshMateSelectbox(oTarget, sMateType);
        },
        //값 일괄적용
        fnAllChgValue : (aFabricList, sRefText, sTargetFldNm, sChgVal)=>{
            let iCntChg = 0;
            $.each(aFabricList, function (key, val) {
                if (document.getElementsByName('chkAllChg'+sRefText+'[]')[key].checked === true) {
                    iCntChg++;
                    this[sTargetFldNm] = sChgVal;
                }
            });
            if (iCntChg === 0) {
                $.msg('값을 지정할 자재를 선택하세요','','warning');
            }
        },
    };

    const materialModuleComputed = {
        computed_sum_labor() {
            this.iSumLaborAmt = 0;
            if( !$.isEmpty(this.<?=$sMaterialTargetNm?>.jsonLaborCost) && Array.isArray(this.<?=$sMaterialTargetNm?>.jsonLaborCost) === true && this.<?=$sMaterialTargetNm?>.jsonLaborCost.length > 0){
                let iSumAmt = 0;
                $.each(this.<?=$sMaterialTargetNm?>.jsonLaborCost, function(key, val) {
                    if (val.unitPrice != undefined && val.costQty != undefined) iSumAmt += Number(val.unitPrice) * Number(val.costQty);
                });
                this.iSumLaborAmt = iSumAmt;
            }
            // return this.iSumLaborAmt;
        },
        computed_sum_etc() {
            this.iSumEtcAmt = 0;
            if( !$.isEmpty(this.<?=$sMaterialTargetNm?>.jsonEtc) && Array.isArray(this.<?=$sMaterialTargetNm?>.jsonEtc) === true && this.<?=$sMaterialTargetNm?>.jsonEtc.length > 0){
                let iSumAmt = 0;
                $.each(this.<?=$sMaterialTargetNm?>.jsonEtc, function(key, val) {
                    if (val.unitPrice != undefined && val.costQty != undefined) iSumAmt += Number(val.unitPrice) * Number(val.costQty);
                });
                this.iSumEtcAmt = iSumAmt;
            }
            // return this.iSumEtcAmt;
        },
        computed_sum_util() {
            this.iSumUtilAmt = 0;
            if( !$.isEmpty(this.<?=$sMaterialTargetNm?>.jsonUtil) && Array.isArray(this.<?=$sMaterialTargetNm?>.jsonUtil) === true && this.<?=$sMaterialTargetNm?>.jsonUtil.length > 0){
                let iSumAmt = 0;
                let iPlanDollerRatio = Number(this.<?=$sMaterialTargetNm?>.dollerRatio);
                $.each(this.<?=$sMaterialTargetNm?>.jsonUtil, function(key, val) {
                    if (val.unitPriceDoller != undefined && Number(val.unitPriceDoller) > 0 && iPlanDollerRatio > 0) val.unitPrice =  Math.ceil(Number($.getOnlyNumber(Number(val.unitPriceDoller) * iPlanDollerRatio)));
                    if (val.unitPrice != undefined && val.utilQty != undefined) iSumAmt += Math.ceil(Number(val.unitPrice) * Number(val.utilQty));
                });
                this.iSumUtilAmt = iSumAmt;
            }
            // return this.iSumUtilAmt;
        },
        computed_sum_fabric() {
            this.iSumFabricAmt = 0;
            if( !$.isEmpty(this.<?=$sMaterialTargetNm?>.fabric) && Array.isArray(this.<?=$sMaterialTargetNm?>.fabric) === true && this.<?=$sMaterialTargetNm?>.fabric.length > 0){
                let iSumAmt = 0;
                let iPlanDollerRatio = Number(this.<?=$sMaterialTargetNm?>.dollerRatio);
                $.each(this.<?=$sMaterialTargetNm?>.fabric, function(key, val) {
                    if (val.unitPriceDoller != undefined && Number(val.unitPriceDoller) > 0 && iPlanDollerRatio > 0) val.unitPrice =  Math.ceil(Number($.getOnlyNumber(Number(val.unitPriceDoller) * iPlanDollerRatio)));
                    if (val.unitPrice != undefined && val.meas != undefined) iSumAmt += Math.ceil(Number(val.unitPrice) * Number(val.meas));
                });
                this.iSumFabricAmt = iSumAmt;
            }
            // return this.iSumFabricAmt;
        },
        computed_sum_sub_fabric() {
            this.iSumSubFabricAmt = 0;
            if( !$.isEmpty(this.<?=$sMaterialTargetNm?>.subFabric) && Array.isArray(this.<?=$sMaterialTargetNm?>.subFabric) === true && this.<?=$sMaterialTargetNm?>.subFabric.length > 0){
                let iSumAmt = 0;
                let iPlanDollerRatio = Number(this.<?=$sMaterialTargetNm?>.dollerRatio);
                $.each(this.<?=$sMaterialTargetNm?>.subFabric, function(key, val) {
                    if (val.unitPriceDoller != undefined && Number(val.unitPriceDoller) > 0 && iPlanDollerRatio > 0) val.unitPrice =  Math.ceil(Number($.getOnlyNumber(Number(val.unitPriceDoller) * iPlanDollerRatio)));
                    if (val.unitPrice != undefined && val.meas != undefined) iSumAmt += Math.ceil(Number(val.unitPrice) * Number(val.meas));
                });
                this.iSumSubFabricAmt = iSumAmt;
            }
            // return this.iSumSubFabricAmt;
        },
        computed_sum_mark() { //마크 금액 합산
            this.iSumMarkAmt = 0;
            if (this.<?=$sMaterialTargetNm?>.jsonMark != undefined && Array.isArray(this.<?=$sMaterialTargetNm?>.jsonMark) === true && this.<?=$sMaterialTargetNm?>.jsonMark.length > 0) {
                let iSumAmt = 0;
                let iPlanDollerRatio = Number(this.<?=$sMaterialTargetNm?>.dollerRatio);
                $.each(this.<?=$sMaterialTargetNm?>.jsonMark, function(key, val) {
                    if (val.unitPriceDoller != undefined && Number(val.unitPriceDoller) > 0 && iPlanDollerRatio > 0) val.unitPrice = Math.ceil(Number($.getOnlyNumber(Number(val.unitPriceDoller) * iPlanDollerRatio)));
                    if (val.unitPrice != undefined && val.meas != undefined) iSumAmt += Math.ceil(Number($.getOnlyNumber(val.meas)) * Number($.getOnlyNumber(val.unitPrice)));
                });
                this.iSumMarkAmt = iSumAmt;
            }
            // return this.iSumMarkAmt;
        },
        computed_set_no_selectbox() {
            if (this.isModify == true) {
                this.$nextTick(function() {
                    this.refreshMateSelectboxAll();
                });
            }
        },
    };

</script>
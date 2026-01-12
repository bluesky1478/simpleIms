<div class="row" >
    <div class="col-xs-12" >
        <div>
            <!--검색 시작-->
            <div class="search-detail-box form-inline">
                <div class="table-title ">
                    검색
                    &nbsp; &nbsp; <span v-show="obFlagFoldSch[1] === false" @click="obFlagFoldSch[1] = true;" class="btn btn-white">접기</span>
                    <span v-show="obFlagFoldSch[1] === true" @click="obFlagFoldSch[1] = false;" class="btn btn-white">펼치기</span>
                </div>
                <table v-show="obFlagFoldSch[1] === false" class="table table-cols table-td-height0">
                    <colgroup>
                        <col class="w-120px">
                        <col class="w-34p">
                        <col class="w-120px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>레퍼런스명</th>
                        <td>
                            <input type="text" v-model="searchCondition.sTextboxSchRefName" value="" class="form-control" placeholder="레퍼런스명" />
                        </td>
                        <th>성별</th>
                        <td >
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchRefGender[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchRefGender[]"
                                       :checked="0 >= searchCondition.aChkboxSchRefGender.length?'checked':''" @click="searchCondition.aChkboxSchRefGender=[]" /> 전체
                            </label>
                            <?php foreach( \Component\Ims\NkCodeMap::PRODUCT_PLAN_GENDER as $k => $v){ ?>
                                <label class="mgl10" >
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchRefGender[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchRefGender" />
                                    <?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>시즌</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchRefSeason[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchRefSeason[]"
                                       :checked="0 >= searchCondition.aChkboxSchRefSeason.length?'checked':''" @click="searchCondition.aChkboxSchRefSeason=[]" /> 전체
                            </label>
                            <?php foreach($seasonList as $k => $v){ ?>
                                <label class="mgl10" >
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchRefSeason[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchRefSeason" />
                                    (<?=$k?>) <?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                        <th>타입</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSumSchRefType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSumSchRefType[]"
                                       :checked="searchCondition.aChkboxSumSchRefType.length == 0?'checked':''" @click="searchCondition.aChkboxSumSchRefType=[]" /> 전체
                            </label>
                            <?php foreach( \Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_TYPE as $k => $v){ if ($k > 0) { ?>
                                <label class="mgl10" >
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSumSchRefType[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSumSchRefType" />
                                    <?=$v?>
                                </label>
                            <?php }} ?>
                        </td>
                    </tr>
                    <tr>
                        <th>스타일코드</th>
                        <td colspan="3">
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchRefStyle[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchRefStyle[]"
                                       :checked="0 >= searchCondition.aChkboxSchRefStyle.length?'checked':''" @click="searchCondition.aChkboxSchRefStyle=[]" /> 전체
                            </label>
                            <?php foreach($codeStyle as $k => $v){ ?>
                                <label class="mgl10" >
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchRefStyle[]" value="<?=$k?>"  v-model="searchCondition.aChkboxSchRefStyle" />
                                    <?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>제품 단가</th>
                        <td colspan="3">
                            <div class="dp-flex">
                                <div class="mini-picker mgl5">
                                    <input type="number" v-model="searchCondition.sTextboxRangeStartSchRefUnitPrice" class="form-control" />
                                </div>
                                <div>~</div>
                                <div class="mini-picker">
                                    <input type="number" v-model="searchCondition.sTextboxRangeEndSchRefUnitPrice" class="form-control" />
                                </div>
                                <div class="form-inline" >
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice=''; searchCondition.sTextboxRangeEndSchRefUnitPrice=''; refreshList(1);">전체</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice=''; searchCondition.sTextboxRangeEndSchRefUnitPrice='20000'; refreshList(1);">2만원 이하</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice='20000'; searchCondition.sTextboxRangeEndSchRefUnitPrice='30000'; refreshList(1);">2만원~3만원</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice='30000'; searchCondition.sTextboxRangeEndSchRefUnitPrice='50000'; refreshList(1);">3만원~5만원</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice='50000'; searchCondition.sTextboxRangeEndSchRefUnitPrice='80000'; refreshList(1);">5만원~8만원</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice='80000'; searchCondition.sTextboxRangeEndSchRefUnitPrice='100000'; refreshList(1);">8만원~10만원</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchRefUnitPrice='100000'; searchCondition.sTextboxRangeEndSchRefUnitPrice=''; refreshList(1);">10만원 이상</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="table-title ">
                    부가정보
                    &nbsp; &nbsp; <span v-show="obFlagFoldSch[2] === false" @click="obFlagFoldSch[2] = true;" class="btn btn-white">접기</span>
                    <span v-show="obFlagFoldSch[2] === true" @click="obFlagFoldSch[2] = false;" class="btn btn-white">펼치기</span>
                </div>
                <table v-show="obFlagFoldSch[2] === false" class="table table-cols table-td-height0">
                    <colgroup>
                        <col class="w-120px">
                        <col class="w-34p">
                        <col class="w-120px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>참고 브랜드</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchB1InfoSno[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchB1InfoSno[]"
                                       :checked="searchCondition['aChkboxSchB1.infoSno'].length==0?'checked':''" @click="searchCondition['aChkboxSchB1.infoSno']=[]" /> 전체
                            </label>
                            <label v-for="val in ooAppendList[1]" class="mgl10" >
                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchB1InfoSno[]" :value="val.sno"  v-model="searchCondition['aChkboxSchB1.infoSno']" />
                                {% val.infoName %}
                            </label>
                        </td>
                        <th>컨셉</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchB2InfoSno[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchB2InfoSno[]"
                                       :checked="searchCondition['aChkboxSchB2.infoSno'].length==0?'checked':''" @click="searchCondition['aChkboxSchB2.infoSno']=[]" /> 전체
                            </label>
                            <label v-for="val in ooAppendList[2]" class="mgl10" >
                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchB2InfoSno[]" :value="val.sno"  v-model="searchCondition['aChkboxSchB2.infoSno']" />
                                {% val.infoName %}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>디자인</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchB3InfoSno[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchB3InfoSno[]"
                                       :checked="searchCondition['aChkboxSchB3.infoSno'].length==0?'checked':''" @click="searchCondition['aChkboxSchB3.infoSno']=[]" /> 전체
                            </label>
                            <label v-for="val in ooAppendList[3]" class="mgl10" >
                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchB3InfoSno[]" :value="val.sno"  v-model="searchCondition['aChkboxSchB3.infoSno']" />
                                {% val.infoName %}
                            </label>
                        </td>
                        <th>부가기능</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchB4InfoSno[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchB4InfoSno[]"
                                       :checked="searchCondition['aChkboxSchB4.infoSno'].length==0?'checked':''" @click="searchCondition['aChkboxSchB4.infoSno']=[]" /> 전체
                            </label>
                            <label v-for="val in ooAppendList[4]" class="mgl10" >
                                <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchB4InfoSno[]" :value="val.sno"  v-model="searchCondition['aChkboxSchB4.infoSno']" />
                                {% val.infoName %}
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="table-title ">
                    메인원단
                    &nbsp; &nbsp; <span v-show="obFlagFoldSch[3] === false" @click="obFlagFoldSch[3] = true;" class="btn btn-white">접기</span>
                    <span v-show="obFlagFoldSch[3] === true" @click="obFlagFoldSch[3] = false;" class="btn btn-white">펼치기</span>
                </div>
                <table v-show="obFlagFoldSch[3] === false" class="table table-cols table-td-height0">
                    <colgroup>
                        <col class="w-120px">
                        <col class="w-34p">
                        <col class="w-120px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>단가</th>
                        <td>
                            <div class="dp-flex">
                                <div class="mini-picker mgl5">
                                    <input type="number" v-model="searchCondition.sTextboxRangeStartSchMainFabricUnitPrice" class="form-control" style="width:80px; padding:0px 5px;" />
                                </div>
                                <div>~</div>
                                <div class="mini-picker">
                                    <input type="number" v-model="searchCondition.sTextboxRangeEndSchMainFabricUnitPrice" class="form-control" style="width:80px; padding:0px 5px;" />
                                </div>
                                <div class="form-inline" >
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchMainFabricUnitPrice=''; searchCondition.sTextboxRangeEndSchMainFabricUnitPrice=''; refreshList(1);">전체</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchMainFabricUnitPrice=''; searchCondition.sTextboxRangeEndSchMainFabricUnitPrice='3000'; refreshList(1);">3천원 이하</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchMainFabricUnitPrice='3000'; searchCondition.sTextboxRangeEndSchMainFabricUnitPrice='4000'; refreshList(1);">3천원~4천원</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchMainFabricUnitPrice='4000'; searchCondition.sTextboxRangeEndSchMainFabricUnitPrice='5000'; refreshList(1);">4천원~5천원</div>
                                    <div class="btn btn-sm btn-white" @click="searchCondition.sTextboxRangeStartSchMainFabricUnitPrice='5000'; searchCondition.sTextboxRangeEndSchMainFabricUnitPrice=''; refreshList(1);">5천원 이상</div>
                                </div>
                            </div>
                        </td>
                        <th>생지유무</th>
                        <td>
                            <label class="radio-inline ">
                                <input type="radio" name="sRadioSchMainFabricOnHandYn" value="all" v-model="searchCondition.sRadioSchMainFabricOnHandYn"/>전체
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="sRadioSchMainFabricOnHandYn" value="O" v-model="searchCondition.sRadioSchMainFabricOnHandYn"/>생지 有
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="sRadioSchMainFabricOnHandYn" value="X" v-model="searchCondition.sRadioSchMainFabricOnHandYn"/>생지 無
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="table-title ">
                    원부자재
                    &nbsp; &nbsp; <span v-show="obFlagFoldSch[4] === false" @click="obFlagFoldSch[4] = true;" class="btn btn-white">접기</span>
                    <span v-show="obFlagFoldSch[4] === true" @click="obFlagFoldSch[4] = false;" class="btn btn-white">펼치기</span>
                </div>
                <table v-show="obFlagFoldSch[4] === false" class="table table-cols table-td-height0">
                    <colgroup>
                        <col class="w-120px">
                        <col class="w-34p">
                        <col class="w-120px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>자재명</th>
                        <td>
                            <input type="text" v-model="searchCondition['sTextboxSchMate.materialName']" value="" class="form-control" placeholder="자재명" />
                        </td>
                        <th>타입</th>
                        <td>
                            <label class="checkbox-inline " >
                                <input type="checkbox" name="aChkboxSchMateMaterialType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchMateMaterialType[]"
                                       :checked="0 >= searchCondition['aChkboxSchMate.materialType'].length?'checked':''" @click="searchCondition['aChkboxSchMate.materialType']=[]" /> 전체
                            </label>
                            <?php foreach( \Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_MATERIAL_TYPE as $k => $v){ ?>
                                <label class="mgl10" >
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMateMaterialType[]" value="<?=$k?>"  v-model="searchCondition['aChkboxSchMate.materialType']" />
                                    <?=$v?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>혼용률</th>
                        <td>
                            <input type="text" v-model="searchCondition['sTextboxSchMate.fabricMix']" value="" class="form-control" placeholder="혼용률" />
                        </td>
                        <th>후가공</th>
                        <td>
                            <input type="text" v-model="searchCondition.sTextboxSchAfterMake" value="" class="form-control" placeholder="후가공" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="table-title ">
                    고객사
                    &nbsp; &nbsp; <span v-show="obFlagFoldSch[5] === false" @click="obFlagFoldSch[5] = true;" class="btn btn-white">접기</span>
                    <span v-show="obFlagFoldSch[5] === true" @click="obFlagFoldSch[5] = false;" class="btn btn-white">펼치기</span>
                </div>
                <table v-show="obFlagFoldSch[5] === false" class="table table-cols table-td-height0">
                    <colgroup>
                        <col class="w-120px">
                        <col class="w-34p">
                        <col class="w-120px">
                        <col class="">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>업체명</th>
                        <td>
                            <input type="text" v-model="searchCondition['sTextboxSchCustinfo.customerName']" value="" class="form-control" placeholder="업체명" />
                        </td>
                        <th>업종</th>
                        <td>
                            <select v-model="searchCondition['sRadioSchCate1.sno']" @change="searchCondition.sRadioSchBusiCateSno = 'all';" class="form-control" style="display: inline; min-width: 100px;">
                                <option value="all">전체</option>
                                <option v-for="(val, key) in oParentCateList" :value="key">{% val %}</option>
                            </select>
                            <select v-model="searchCondition.sRadioSchBusiCateSno" class="form-control" style="display: inline; min-width: 150px;">
                                <option value="all">전체</option>
                                <option v-if="key!=0" v-for="(val, key) in oCateList[searchCondition['sRadioSchCate1.sno']]" :value="key">{% val %}</option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="ta-c">
                    <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshList(1)">
                    <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="conditionReset()">
                </div>
            </div>
            <!--검색 끝-->
        </div>

        <div class="">
            <div class="flo-left mgb5">
                <div class="font-16 dp-flex" >
                    <span style="font-size: 18px !important;">
                        TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                    </span>
                </div>
            </div>
            <div class="flo-right mgb5">
                <div class="" style="display: flex; ">
<!--                    <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="listDownload(1)">정산 리스트</button>-->
                    <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                        <option value="D,asc">레퍼런스 등록일시 ▲</option>
                        <option value="D,desc">레퍼런스 등록일시 ▼</option>
                    </select>
                    <select @change="refreshList(1)" v-model="searchCondition.pageNum" class="form-control mgl5">
                        <option value="5">5개 보기</option>
                        <option value="10">10개 보기</option>
                        <option value="20">20개 보기</option>
                        <option value="50">50개 보기</option>
                        <option value="100">100개 보기</option>
                    </select>
                </div>
            </div>
        </div>
        <!--list start-->
        <div style="clear:both;">
            <div v-if="listData.length == 0" class="ta-c" style="height:50px;">등록된 데이터가 없습니다.</div>
            <ul v-else class="box_list">
                <li v-for="val in listData" @click="openCommonPopup('upsert_style_plan_ref', 1580, 910, {'sno':val.sno});" class="cursor-pointer hover-btn">
                    <div>
                        <div><img :src="val.refThumbImg==null||val.refThumbImg==''?'/data/commonimg/ico_noimg_300.gif':val.refThumbImg" /></div>
                        <div>{% val.refName %}</div>
                        <div>{% $.setNumberFormat(val.refUnitPrice) %} 원</div>
                    </div>
                </li>
            </ul>


        </div>
        <!--list end-->
        <div id="style_plan_ref-page" v-html="pageHtml" class="ta-c"></div>
    </div>
</div>
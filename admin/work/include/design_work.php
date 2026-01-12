<div class="col-xs-12" >
    <div class="table-title ">
        <div class="flo-left" >상품 스타일 정보</div>
        <div class="flo-right " ></div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>스타일 추가</th>
                <td>
                    <div class="form-inline">
                        <select class="form-control" id="selected-sample-style1">
                            <option value="">선택</option>
                            <?php foreach( \Component\Work\WorkCodeMap::STYLE_TYPE as $styleKey => $styleValue) { ?>
                                <option value="<?=$styleKey?>" ><?=$styleValue?></option>
                            <?php } ?>
                        </select>
                        <div class="btn btn-sm btn-red" @click="addSample(items.docData.sampleData,1)">+ 스타일추가</div>
                    </div>
                </td>
                <th>스타일 선택</th>
                <td>
                    <select class="form-control" v-model="items.showSelectedStyle" @change="focusSampleTop"  style="width:200px"  v-show="items.docData.sampleData.length > 0">
                        <option :value="index" v-for="(item, index) in items.docData.sampleData">
                            {% item.styleName %}
                            ({% item.serial %})
                        </option>
                    </select>
                    <div  v-show="0 >= items.docData.sampleData.length">상품을 등록해주세요.</div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding:0px">
                    <div class="mgt10"><b>등록상품 리스트</b></div>
                    <table class="table table-cols">
                        <colgroup>
                            <col style="width:70px" />
                            <col  />
                            <col style="width:150px"  />
                            <col  />
                            <col  />
                            <col style="width:120px"  />
                            <col style="width:100px" />
                            <col style="width:100px" />
                            <col  />
                            <col style="width:150px" />
                        </colgroup>
                        <tr >
                            <th class="text-center">번호</th>
                            <th class="text-center">스타일</th>
                            <th class="text-center">S/#</th>
                            <th class="text-center">사이즈</th>
                            <th class="text-center">핏</th>
                            <th class="text-center">수량</th>
                            <th class="text-center">의뢰일</th>
                            <th class="text-center">완료요청일</th>
                            <th class="text-center">생산처</th>
                            <th class="text-center">확인/삭제</th>
                            <th class="text-center">다운로드</th>
                        </tr>
                        <tr v-for="(item, index) in items.docData.sampleData">
                            <td class="text-center">{% index+1 %}</td>
                            <td class="text-center">{% item.styleName %}</td>
                            <td class="text-center">{% item.serial %}</td>
                            <td class="text-center">{% item.sizeDisplay %}</td>
                            <td class="text-center">{% item.fit %}</td>
                            <td class="text-center">{% $.setNumberFormat(item.itemTotalCount) %}</td>
                            <td class="text-center">{% item.requestDt %}</td>
                            <td class="text-center">{% item.completeDt %}</td>
                            <td class="text-center">{% getSampleFactoryData(item.sampleFactorySno, 'factoryName') %}</td>
                            <td class="text-center">
                                <div class="btn btn-sm btn-white" @click="selectedStyle(items, index)">확인</div>
                                <div class="btn btn-sm btn-red" @click="removeSample(items.docData.sampleData, index)">삭제</div>
                            </td>
                            <td class="text-center">
                                <div class="btn btn-sm btn-white" @click="simpleDownload('<?=$requestUrl?>&index=' + index  )">다운로드</div>
                            </td>
                        </tr>
                        <tr v-show="0 >= items.docData.sampleData.length">
                            <td colspan="10" class="text-center" >데이터가 없습니다.</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="w100 text-right font-15"><b>총 수량 : <span class="text-danger">{% getTotalCount %}</span>개</b></div>
    </div>
</div>

<div v-for="(item, index) in items.docData.sampleData" :key="index" v-show="index == items.showSelectedStyle" >
    <!--타이틀-->
    <div class="col-xs-12"  style="border-top:dotted 2px #717171 !important;" >
        <div class="" >
            <div class="flo-left"><h2>Style{% index + 1 %} - {% item.styleName %} <small style="font-weight:normal">({% item.serial %})</small></h2></div>
            <div  class="flo-left" style="margin-left:10px">
                <h2><div class="btn btn-red " @click="removeSample(items.docData.sampleData, index)" >- 삭제</div></h2>
            </div>
        </div>
    </div>
    <!--좌 (상품 제작 정보 등) -->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >상품 제작정보</div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>스타일</th>
                    <td>{% item.styleName %}</td>
                    <th>S/#</th>
                    <td>
                        <input type="text" class="form-control sample-data-area" v-model="item.serial">
                    </td>
                </tr>
                <tr>
                    <th>사이즈(표기 기준)</th>
                    <td>
                        <label class="radio-inline" >
                            <input type="radio" :name="'sizeDisplay' + index" v-model="item.sizeDisplay" value="숫자 - 100" />숫자 - 100
                            <label class="radio-inline" >
                            </label>
                            <input type="radio" :name="'sizeDisplay' + index" v-model="item.sizeDisplay" value="영문 - L" />영문 - L
                        </label>
                    </td>
                    <th>핏</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" :name="'fit' + index" v-model="item.fit" value="슬림" @change="getGuideSpec(item, 0)" />슬림
                        </label>
                        <label class="radio-inline">
                            <input type="radio" :name="'fit' + index"  v-model="item.fit" value="기본" @change="getGuideSpec(item, 1)" />기본
                        </label>
                        <label class="radio-inline">
                            <input type="radio" :name="'fit' + index"  v-model="item.fit" value="루즈" @change="getGuideSpec(item, 2)" />루즈
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>시즌</th>
                    <td>
                        <input type="text" class="form-control" v-model="item.season">
                    </td>
                    <th>제품명</th>
                    <td>
                        <input type="text" class="form-control" v-model="item.productName">
                    </td>
                </tr>
                <tr>
                    <th>생산구분</th>
                    <td>
                        <select class="form-control"  v-model="item.produceType">
                            <option value="">선택</option>
                            <option value="완사입">완사입</option>
                            <option value="임가공">임가공</option>
                        </select>
                    </td>
                    <th>제조국</th>
                    <td>
                        <input type="text" class="form-control" v-model="item.produceCountry">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--우 (상품 제작처 등) -->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left">상품 제작처</div>
            <div class="flo-right" ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>제작처</th>
                    <td>
                        <select class="form-control"  v-model="item.sampleFactorySno">
                            <option value="">선택</option>
                            <?php foreach( $sampleFactoryList as $factory) { ?>
                                <option value="<?=$factory['sno']?>"><?=$factory['factoryName']?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>제작처 번호</th>
                    <td>
                        {% getSampleFactoryData(item.sampleFactorySno, 'factoryPhone') %}
                    </td>
                </tr>
                <tr>
                    <th style="height:100px">주소</th>
                    <td>
                        {% getSampleFactoryData(item.sampleFactorySno, 'factoryAddress') %}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--우 (상품일정 등)-->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >상품 일정</div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>의뢰일</th>
                    <td>
                        <div class="input-group " style="width:120px" >
                            <datepicker @update-date="updateDate" v-model="item.requestDt" :data-item="'[\'sampleData\'][' + index + '][\'requestDt\']'" class="form-control" ></datepicker>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>완료 요청일</th>
                    <td>
                        <div class="input-group " style="width:120px" >
                            <datepicker @update-date="updateDate" v-model="item.completeDt" :data-item="'[\'sampleData\'][' + index + '][\'completeDt\']'" class="form-control" ></datepicker>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>수령방법</th>
                    <td>
                        <select class="form-control" v-model="item.receiveMethod">
                            <option value="">선택</option>
                            <option >택배</option>
                            <option >화물</option>
                            <option>퀵</option>
                            <option>직접수령</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--좌 (상품 도식화 이미지 첨부) -->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >상품 도식화 이미지 첨부 <small class="text-muted">(파일을 상자에 올리거나 클릭하세요)</small> </div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>상품파일</th>
                    <td style="padding:5px">
                        <div :id="'sample-dropzone' + index " class="set-dropzone"></div>
                        <div>
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileSample" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileSample, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--도식화 이미지-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >상품 도식화 이미지 미리보기</div>
            <div class="flo-right " ></div>
        </div>
        <table class="table table-cols">
            <tr>
                <td>
                    <div v-for="(file, fileIndex) in item.fileSample " :key="fileIndex" style="width:100%; margin-bottom:20px" >
                        <img :src="file.path" style="max-width:900px">
                    </div>
                    <div v-for="(file, fileIndex) in item.fileSamplePreview " :key="fileIndex" style="width:100%; margin-bottom:20px">
                        <img :src="file.imageUrl" style="max-width:900px">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <!--사이즈스펙 여기 . -->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >성별 구분</div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>구분</th>
                    <td>
                        <label class="radio-inline">
                            <input :name="'specType' + index" type="radio"  v-model="item.specType" value="공용" :id="'specType1' + index" />공용
                        </label>
                        <label class="radio-inline">
                            <input :name="'specType' + index" type="radio"  v-model="item.specType" value="남성" :id="'specType2' + index" />남성
                        </label>
                        <label class="radio-inline">
                            <input :name="'specType' + index" type="radio"  v-model="item.specType" value="여성" :id="'specType3' + index" />여성
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <!--옵션수량-->
        <div class="table-title ">
            <div class="flo-left" >
                <div class="inline-block"></div>옵션/수량
                <div class="btn btn-sm btn-red inline-block" @click="addOption(items, index)">+옵션추가</div>
            </div>
            <div class="flo-right " ></div>
        </div>
        <div  style="clear:both">
            <table class="table table-cols center-header center-contents sample-spec-table" >
                <thead>
                    <colgroup>
                        <col  style="width:250px" />
                        <col  style="width:140px" v-for="(optionItem, optionIndex) in item.optionList"  />
                        <col   />
                    </colgroup>
                    <tr >
                        <th >구분</th>
                        <th v-for="(optionItem, optionIndex) in item.optionList"  class="text-right" style="padding-left:2px; padding-right:0px">
                            <input type="text" class="form-control inline-block full-left" v-model="optionItem.optionName" style="width:99px;" placeholder="옵션명">
                            <div class="btn btn-white btn-sm inline-block full-left" @click="removeOption(items,index,optionIndex)">-삭제</div>
                        </th>
                        <th>&nbsp</th>
                    </tr>
                </thead>
                <tr v-for="(typeItem, typeIndex) in item.typeList">
                    <td class="text-right">
                        <input type="text" class="form-control full-left" v-model="typeItem.typeName" style="width:150px">
                        <div class="btn btn-sm btn-white full-left" @click="addType(items,index)" >+추가</div>
                        <div class="btn btn-sm btn-white full-left" @click="removeListData(item.typeList, typeIndex)" >-삭제</div>
                        <div class="dn display-none" style="display:none">{% typeItem.typeTotalCount %}</div>
                    </td>
                    <td v-for="(optionItem, optionIndex) in item.optionList" class="text-center"><!-- Loop는 옵션의 수만큼 -->
                        <input type="text" class="form-control" v-model="typeItem.optionCount[optionIndex]" placeholder="옵션수량">
                    </td>
                    <td>&nbsp</td>
                </tr>
                <tr >
                    <th class="text-center">총합 : {% $.setNumberFormat(item.itemTotalCount) %}개</th>
                    <td v-for="(optionItem, optionIndex) in item.optionList" class="text-center"><!-- Loop는 옵션의 수만큼 -->
                        {% $.setNumberFormat(optionItem.optionTotalCount) %}개
                    </td>
                    <td>&nbsp</td>
                </tr>
            </table>
        </div>

        <!--사이즈스펙-->
        <div class="table-title ">
            <div class="flo-left" >
                <div class="inline-block"></div>사이즈스펙
                <div class="btn btn-sm btn-red inline-block" @click="addListData2(item.checkList)">+ 측정부위추가</div>
            </div>
            <div class="flo-right " ></div>
        </div>
        <div >
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:200px"/> <!--구분-->
                    <col style="width:100px" /> <!--편차-->
                    <col v-for="(optionItem, optionIndex) in item.optionList" style="width:100px" /> <!--옵션-->
                    <col style="width:120px" /><!--단위-->
                    <col style="width:120px" /><!--고객안내-->
                    <col /><!--측정부위-->
                    <col style="width:70px" /><!--삭제-->
                </colgroup>
                <tr>
                    <th>구분</th>
                    <th>고객안내</th>
                    <th>편차</th>
                    <th v-for="(optionItem, optionIndex) in item.optionList">
                        {% optionItem.optionName %}
                        <small class="text-muted btn btn-white btn-sm" @click="deleteSpec(item.checkList, 'checkSpec', optionIndex)">초기화</small>
                    </th>
                    <th>단위</th>
                    <th class="text-left">측정부위</th>
                    <th >제거</th>
                </tr>
                <tr v-for="(checkItem, checkIndex) in item.checkList" :key="checkIndex"  :class="'y' === checkItem.isCustomerGuideFl ? 'bg-light-info' : '' ">
                    <td><!--구분-->
                        <input type="text" class="form-control" v-model="checkItem.specItemName" placeholder="구분" >
                    </td>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" value="y"  :name="'isCustomerGuideFl' + index + '_' + checkIndex" v-model="checkItem.isCustomerGuideFl" />예
                            <input type="radio" value="n" :name="'isCustomerGuideFl' + index + '_' + checkIndex" v-model="checkItem.isCustomerGuideFl" />아니오
                        </label>
                    </td>
                    <td><!--편차-->
                        <div class="form-inline">
                            <input type="text" class="form-control text-center w50" v-model="checkItem.avg"  placeholder="편차" 
                                   :ref="'avg' + checkIndex"   @keyup.38="nextSpec('avg' + (checkIndex-1) )" @keyup.40="nextSpec('avg' + (checkIndex+1) )"   @keyup.enter="nextSpec('avg' + (checkIndex+1) )" >
                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(checkItem, 'avg')">X</div>
                        </div>
                    </td>
                    <td v-for="(optionItem, optionIndex) in item.optionList"><!--옵션-->
                        <div class="form-inline">
                            <input type="text" class="form-control text-center w50" v-model="checkItem.checkSpec[optionIndex]" placeholder="측정값"
                                   :ref="'checkSpec' + optionIndex + '_' + checkIndex"   @keyup.38="nextSpec('checkSpec' + optionIndex + '_' + (checkIndex-1) )" @keyup.40="nextSpec('checkSpec' + optionIndex + '_' + (checkIndex+1) )"   @keyup.enter="nextSpec('checkSpec' + optionIndex + '_' + (checkIndex+1) )">
                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(checkItem, 'checkSpec', optionIndex)">X</div>
                        </div>
                    </td>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" value="cm"  :name="'specUnit' + index + '_' + checkIndex" v-model="checkItem.specUnit" />cm
                            <input type="radio" value="inch" :name="'specUnit' + index + '_' + checkIndex" v-model="checkItem.specUnit" />inch
                        </label>
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="checkItem.specDescription" placeholder="측정부위">
                    </td>
                    <td class="text-center">
                        <div class="btn btn-sm btn-white" @click="removeListData(item.checkList, checkIndex)">-제거</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--사이즈스펙 이미지-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >사이즈 스펙 이미지 업로드 및 유의사항</div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col style="width:40%" />
                    <col class="width-md"/>
                    <col style="width:40%" />
                </colgroup>
                <tr>
                    <th>사이즈 스펙 <br>이미지</th>
                    <td>
                        <div :id="'size-dropzone' + index" class="set-dropzone"></div>
                        <div >
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileSizeSpec" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileSizeSpec, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <th>사이즈 스펙 작업<br>유의사항</th>
                    <td>
                        <textarea class="form-control" rows="6" v-model="item.sizeCaution"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <!--마크정보-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >
                <div class="inline-block">마크 정보</div>
                <div class="btn btn-sm btn-red inline-block" @click="addListData2(item.markList)">+ 마크추가</div>
            </div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:60px" />
                    <col class="width-md"/>
                    <col />
                    <col class="width-md"/>
                    <col />
                    <col class="width-md"/>
                    <col />
                    <col class="width-md"/>
                    <col />
                </colgroup>
                <tbody v-for="(markItem, markIndex) in item.markList">
                    <tr>
                        <th rowspan="2" style="border-right:solid 1px #E6E6E6">{% markIndex+1 %}</th>
                        <th>위치</th>
                        <th class="pd5"><input type="text" class="form-control" v-model="markItem.position"></th>
                        <th>종류</th>
                        <th class="pd5"><input type="text" class="form-control" v-model="markItem.kind"></th>
                        <th>색상</th>
                        <th class="pd5"><input type="text" class="form-control" v-model="markItem.color"></th>
                        <th>크기</th>
                        <th class="pd5"><input type="text" class="form-control"  v-model="markItem.size"></th>
                    </tr>
                    <tr>
                        <th>마크파일</th>
                        <td  style="padding:5px !important; text-align:left" colspan="8">
                            <div :id="'mark-dropzone' + markIndex + '_' + index   " class="set-dropzone"></div>
                            <div >
                                <ul >
                                    <li v-for="(file, fileIndex) in item.markList[markIndex]['fileMark']" :key="fileIndex">
                                        첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                        <a href="#" @click="removeFile(item.markList[markIndex]['fileMark'], fileIndex)">삭제</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >마크 작업 유의사항</div>
            <div class="flo-right " >
            </div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col class="width-md"/>
                    <col />
                </colgroup>
                <tr>
                    <th>유의사항</th>
                    <td  style="padding:5px !important; text-align:left">
                        <textarea class="form-control" rows="6" v-model="item.markCaution"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- 마크 라벨 위치 -->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >마크/라벨 위치</div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col style="width:40%" />
                    <col class="width-md"/>
                    <col style="width:40%" />
                </colgroup>
                <tr>
                    <th>마크/라벨 위치<br> 이미지</th>
                    <td>
                        <div :id="'label-dropzone' + index" class="set-dropzone"></div>
                        <div >
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileMarkLabel" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileMarkLabel, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <th>마크/라벨 위치<br> 작업 유의 사항</th>
                    <td>
                        <textarea class="form-control" rows="6" v-model="item.labelCaution"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--원자재-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >원자재</div>
            <div class="flo-right form-inline" >
                <a href="<?=$downloadBasePath?>/data/form/작업지시서_원자재등록양식.xls">
                    <div class="btn btn-white btn-icon-excel">(작업지시서)원자재 업로드 양식</div>
                </a>
                <input type="file" style="font-size:13px; font-weight:normal; display: inline-block" @change="setUploadFile('workPartData',event)">
                <div class="btn btn-sm btn-red" style="display: inline-block" @click="getUploadData('workPartData', items, index)">업로드</div>
            </div>
        </div>
        <div style="clear:both">
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:100px" /><!--부위-->
                    <col style="width:100px" /> <!--설명-->
                    <col style="width:200px"/><!--규격-->
                    <col style="width:100px" /><!--소요량-->
                    <col  /><!--아이템-->
                    <col style="width:200px"/><!--색상-->
                    <col style="width:250px" /><!--구매처정보-->
                    <col style="width:100px" /><!--요척-->
                    <col style="width:250px" /><!--비고-->
                    <col style="width:150px"/><!--추가/삭제-->
                </colgroup>
                <tr>
                    <th>부위</th>
                    <th>설명</th>
                    <th>폭</th>
                    <th>요척</th>
                    <th>원단이름</th>
                    <th>색상</th>
                    <th>구매처정보</th>
                    <th>규격</th>
                    <th>비고</th>
                    <th>추가/삭제</th>
                </tr>
                <tr v-for="(partItem, partIndex) in item.partInfo" :key="partIndex">
                    <td><input type="text" class="form-control" v-model="partItem.type"></td><!--부위-->
                    <td><input type="text" class="form-control" v-model="partItem.desc"></td><!--설명-->
                    <td><input type="text" class="form-control" v-model="partItem.size"></td><!--폭-->
                    <td><input type="text" class="form-control" v-model="partItem.yochuck"></td><!--요척-->
                    <td><input type="text" class="form-control" v-model="partItem.partName"></td><!--원단이름-->
                    <td><input type="text" class="form-control" v-model="partItem.color"></td><!--색상-->
                    <td><input type="text" class="form-control" v-model="partItem.etc"></td><!--구매처정보-->
                    <td><input type="text" class="form-control" v-model="partItem.size2"></td><!--규격-->
                    <td><input type="text" class="form-control" v-model="partItem.memo"></td><!--비고-->
                    <td class="text-left">
                        <div class="btn btn-sm btn-white" @click="addListData2(item.partInfo)" v-show="(item.partInfo.length-1) === partIndex">+추가</div>
                        <div class="btn btn-sm btn-white" @click="removeListData(item.partInfo, partIndex)" v-show="item.partInfo.length > 1">-삭제</div>
                    </td>
                </tr>
                <tr v-show="typeof item.partInfo == 'undefined' || 0 >= item.partInfo.length">
                    <td colspan="10">데이터가 없습니다.</td>
                </tr>
            </table>
        </div>
    </div>
    <!--부자재-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >부자재</div>
            <div class="flo-right form-inline" >
                <a href="<?=$downloadBasePath?>/data/form/작업지시서_부자재등록양식.xls">
                    <div class="btn btn-white btn-icon-excel">(작업지시서) 부자재 업로드 양식</div>
                </a>
                <input type="file" style="font-size:13px; font-weight:normal; display: inline-block" @change="setUploadFile('workSubPartData',event)">
                <div class="btn btn-sm btn-red" style="display: inline-block" @click="getUploadData('workSubPartData', items, index)">업로드</div>
            </div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:100px" /><!--부위-->
                    <col style="width:100px" /> <!--설명-->
                    <col style="width:200px"/><!--규격-->
                    <col style="width:100px" /><!--소요량-->
                    <col  /><!--아이템-->
                    <col style="width:200px"/><!--색상-->
                    <col style="width:250px" /><!--구매처정보-->
                    <col style="width:100px" /><!--요척-->
                    <col style="width:250px" /><!--비고-->
                    <col style="width:150px"/><!--추가/삭제-->
                </colgroup>
                <tr>
                    <th>부위</th>
                    <th>설명</th>
                    <th>규격</th>
                    <th>소요량</th>
                    <th>아이템</th>
                    <th>색상</th>
                    <th>구매처정보</th>
                    <th>요척</th>
                    <th>비고</th>
                    <th>추가/삭제</th>
                </tr>
                <tr v-for="(partItem, partIndex) in item.subPartInfo" :key="partIndex">
                    <td><input type="text" class="form-control" v-model="partItem.type"></td><!--부위-->
                    <td><input type="text" class="form-control" v-model="partItem.desc"></td><!--설명-->
                    <td><input type="text" class="form-control" v-model="partItem.size"></td><!--규격-->
                    <td><input type="text" class="form-control" v-model="partItem.soyo"></td><!--소요량-->
                    <td><input type="text" class="form-control" v-model="partItem.partName"></td><!--아이템-->
                    <td><input type="text" class="form-control" v-model="partItem.color"></td><!--색상-->
                    <td><input type="text" class="form-control" v-model="partItem.etc"></td><!--구매처정보-->
                    <td><input type="text" class="form-control" v-model="partItem.yochuck"></td><!--요척-->
                    <td><input type="text" class="form-control" v-model="partItem.memo"></td><!--비고-->
                    <td class="text-left">
                        <div class="btn btn-sm btn-white" @click="addListData2(item.subPartInfo)" v-show="(item.subPartInfo.length-1) === partIndex">+추가</div>
                        <div class="btn btn-sm btn-white" @click="removeListData(item.subPartInfo, partIndex)" v-show="item.subPartInfo.length > 1">-삭제</div>
                    </td>
                </tr>
                <tr v-show="typeof item.partInfo == 'undefined' || 0 >= item.partInfo.length">
                    <td colspan="10">데이터가 없습니다.</td>
                </tr>
            </table>
        </div>
    </div>
    <!--원부자재 유의사항-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >원부자재 관련 유의사항</div>
            <div class="flo-right " >
            </div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col class="width-md"/>
                    <col />
                </colgroup>
                <tr>
                    <th>유의사항</th>
                    <td  style="padding:5px !important; text-align:left">
                        <textarea class="form-control" rows="6" v-model="item.partCaution"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--접는 방법-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >접는방법</div>
            <div class="flo-right " >
            </div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col class="width-md"/>
                    <col style="width:40%" />
                    <col class="width-md"/>
                    <col style="width:40%" />
                </colgroup>
                <tr>
                    <th>접는방법 이미지</th>
                    <td class="text-left">
                        <div :id="'fold1-dropzone' + index" class="set-dropzone"></div>
                        <div >
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileFold1" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileFold1, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <th>포장방법 이미지</th>
                    <td class="text-left">
                        <div :id="'fold2-dropzone' + index" class="set-dropzone"></div>
                        <div >
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileFold2" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileFold2, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>박스팩킹 이미지</th>
                    <td class="text-left">
                        <div :id="'fold3-dropzone' + index" class="set-dropzone"></div>
                        <div >
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileFold3" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileFold3, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <th>접는방법 유의사항</th>
                    <td  style="padding:5px !important; text-align:left">
                        <textarea class="form-control" rows="6" v-model="item.foldCaution"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    
</div>

<div class="col-xs-12" style="border-top:dotted 2px #717171; width:100%">&nbsp;</div>

<script type="text/javascript">
    let uploadData = [];
    let sampleDropzone = new DropzoneFileProc();
    let etcDropzone = new DropzoneFileProc();

    let sampleFactoryData = JSON.parse('<?=$sampleFactoryData?>');

    let externSelectDocument = function(itemDocData, docData, parentApp){
        docData['sampleData'] = $.copyObject(itemDocData.docData.recentSampleData);
        docData.sampleData.forEach((data)=>{
            data.requestDt = '';
            data.completeDt = '';
            data.produceType = '';

            //CheckList
            data.checkList = $.copyObject( data.sampleItem );
            data.checkList.forEach((checkData)=>{
                //체크 옵션
                checkData.checkSpec = [];
                checkData.isCustomerGuideFl = 'y';
                data.optionList.forEach(()=>{
                    checkData.checkSpec.push('');
                });
            });
            delete data.sampleItem;
        });
        //console.log(docData);
    }
    let externMountedFnc = function(el){
        //el.addSample(el.items.docData.sampleData,1); //TEST
    }

    let externBeforeSaveProc = function(items){}

    let externAfterSaveProc = function(items, jsonResultData){
        sampleDropzone.upload( jsonResultData.sno );
        etcDropzone.uploadNew( jsonResultData.sno );
    }

    vueUpdatedCallBackFnc = function(el){
        for(let key in el.items.docData.sampleData){
            //Sample File 처리
            if(  typeof sampleDropzone.fileList[key] == 'undefined' ){
                //console.log(' SET Sample Drop zone ');
                let completeProc = function(dropzoneObject, file){
                    let promiseInitData = WorkDocument.getDocument(DOC_DEPT,DOC_TYPE, documentSno);
                    promiseInitData.then((jsonResult)=>{
                        let fieldName1 = dropzoneObject.optionList[key].fieldName1;
                        let fieldName2 = dropzoneObject.optionList[key].fieldName2;
                        el.items.docData.sampleData[key]['fileSample'] = jsonResult.data.docData[fieldName1][key][fieldName2];
                        dropzoneObject.fileList[key].removeFile(file);
                    });
                }

                //상품 처리
                let dropzoneSetParams = {
                    workUrl : 'work_ps.php?mode=uploadDocumentFileDropzone',
                    dropzoneId : "div#sample-dropzone" + key,
                    fieldName1 : 'sampleData',
                    fieldName2 : 'fileSample',
                };
                sampleDropzone.setDropzone( el.items.docData.sampleData[key].fileSample ,key, dropzoneSetParams);
                sampleDropzone.fileList[key].on("complete", function(file) {
                    completeProc(sampleDropzone, file);
                });
                sampleDropzone.fileList[key].on("addedfile", function(file) {
                    let imageUrl = URL.createObjectURL(file);
                    el.items.docData.sampleData[key].fileSamplePreview.push({
                        uuid : file.upload.uuid,
                        imageUrl : imageUrl,
                    });
                });
                sampleDropzone.fileList[key].on("removedfile", function(file) {
                    for(let idx in el.items.docData.sampleData[key].fileSamplePreview){
                        if( file.upload.uuid == el.items.docData.sampleData[key].fileSamplePreview[idx].uuid){
                            el.items.docData.sampleData[key].fileSamplePreview.splice(idx,1);
                        }
                    }
                });
                //---
            }


            let setEtcDropzone = function(key, params){
                if ( typeof etcDropzone.fileList[key] == 'undefined' ) {
                    let dropzoneSetParams = {
                        workUrl: 'work_ps.php?mode=uploadFileNew',
                        dropzoneId: params['dropzoneId'],
                        fieldList: params['fieldList'],
                    };
                    etcDropzone.setNewDropzone( key, dropzoneSetParams);
                    etcDropzone.fileList[key].on("complete", function (file) {
                        //Complete
                        let promiseInitData = WorkDocument.getDocument(DOC_DEPT, DOC_TYPE, documentSno);
                        promiseInitData.then((jsonResult) => {
                            params['completeProc'](jsonResult);
                            etcDropzone.fileList[key].removeFile(file);
                        });
                    });
                }
            }

            //Size Spec File 처리
            if( typeof etcDropzone.fileList['size_' + key] == 'undefined' ){
                let fileKey = 'size_' + key;
                let params = {
                    completeProc : (jsonResult)=>{
                        el.items.docData.sampleData[key]['fileSizeSpec'] = jsonResult.data.docData.sampleData[key]['fileSizeSpec']; //갱신.
                    },
                    dropzoneId: "div#size-dropzone" + key,
                    fieldList: ['sampleData',key,'fileSizeSpec',],
                }
                setEtcDropzone(fileKey, params);
            }

            //접는방법 File 처리
            for(let foldIdx=1; 3 >= foldIdx; foldIdx++){
                if( typeof etcDropzone.fileList['fold' + foldIdx + '_' + key] == 'undefined' ){
                    let fileKey = 'fold' + foldIdx + '_' + key;
                    let foldKey = 'fileFold'  + foldIdx ;

                    let params = {
                        completeProc : (jsonResult)=>{
                            el.items.docData.sampleData[key][foldKey] = jsonResult.data.docData.sampleData[key][foldKey]; //갱신.
                        },
                        dropzoneId: "div#fold"+foldIdx+"-dropzone" + key,
                        fieldList: ['sampleData',key,foldKey],
                    }
                    setEtcDropzone(fileKey, params);
                }
            }

            //MarkLabel File 처리
            if( typeof etcDropzone.fileList['label_' + key] == 'undefined' ){
                let fileKey = 'label_' + key;
                let params = {
                    completeProc : (jsonResult)=>{
                        el.items.docData.sampleData[key]['fileMarkLabel'] = jsonResult.data.docData.sampleData[key]['fileMarkLabel']; //갱신.
                    },
                    dropzoneId: "div#label-dropzone" + key,
                    fieldList: ['sampleData',key,'fileMarkLabel',],
                }
                setEtcDropzone(fileKey, params);
            }

            //ETC 파일 처리
            //마크 유의 사항
            el.items.docData.sampleData[key].markList.forEach((markData, markIndex)=>{
                let fileKey = markIndex + '_' + key;
                let params = {
                    completeProc : (jsonResult)=>{
                        el.items.docData.sampleData[key]['markList'][markIndex]['fileMark'] = jsonResult.data.docData.sampleData[key]['markList'][markIndex]['fileMark']; //갱신.
                    },
                    dropzoneId: "div#mark-dropzone" + markIndex + '_' + key ,
                    fieldList: ['sampleData',key,'markList',markIndex,'fileMark',],
                }
                setEtcDropzone(fileKey, params);
            });
        }
    }

    let externComputed = {
        getTotalCount : function(){
            let totalCount = 0;
            this.items.docData.sampleData.forEach((sampleData, sampleIndex)=>{
                let itemTotalCount = 0;

                //Type별 초기화
                sampleData.typeList.forEach( (typeData) =>{
                    typeData.typeTotalCount = 0;
                });
                sampleData.optionList.forEach((optionData, optionIndex)=>{
                    let optionTotalCount = 0;
                    sampleData.typeList.forEach( (typeData) =>{
                        let typeCount = Number( $.isEmpty(typeData.optionCount[optionIndex])? 0 : typeData.optionCount[optionIndex] );
                        typeData.typeTotalCount +=  Number(typeCount);
                        optionTotalCount += Number(typeCount);
                    });
                    optionData.optionTotalCount = optionTotalCount; //option
                    itemTotalCount += optionTotalCount; // item
                    totalCount += optionTotalCount; // total
                });
                sampleData.itemTotalCount = itemTotalCount;//item
            });
            return $.setNumberFormat(totalCount);
        }
    }

    let externVueMethod = {
        //상품추가
        addSample : function(sampleData, index){
            let selectedStyleSno = $('#selected-sample-style'+index).val();
            if( $.isEmpty(selectedStyleSno) ){
                alert('추가하실 스타일을 선택해주세요.');
                return false;
            }
            let params = {
                mode : 'getDefaultSampleData',
                styleSno : selectedStyleSno,
            };
            $.post( 'work_ps.php', params, function(jsonResult){
                let copyValueList = [
                    'serial', 'sampleFactorySno', 'requestDt', 'completeDt', 'receiveMethod'
                ];
                let targetSampleData = $.copyObject(jsonResult.data);
                if( sampleData.length > 0 )
                    copyValueList.forEach((copyValueKey)=>{
                        targetSampleData[copyValueKey] = sampleData[0][copyValueKey];
                    });
                sampleData.push(targetSampleData);
                workApp.items.showSelectedStyle =  sampleData.length-1;
                //상품 파일
                //$("html, body").animate({ scrollTop: $(document).height() }, 500);
            } );
        },
        removeSample : (data, index) => {
            let msgPromise = $.msgConfirm('선택하신 스타일을 삭제하시겠습니까?', "");
            msgPromise.then( (result)=>{
                if( result.isConfirmed ){
                    data.splice(index,1);
                    sampleDropzone.fileList.splice(index,1);
                    for(let key in etcDropzone.fileList){
                        let keyArray = key.split('_');
                        let etcIndex = keyArray[keyArray.length-1];
                        if( index == etcIndex ){
                            delete etcDropzone.fileList[key];
                        }
                    }
                }
            });
        },
        getSampleFactoryData : function(sampleFactorySno, fieldName){
            let result = '';
            if( !$.isEmpty( sampleFactorySno ) ){
                result = sampleFactoryData[sampleFactorySno][fieldName];
            }
            return result;
        },
        delSpec : function(specItem, fieldName , index){
            if(typeof index != 'undefined'){
                specItem[fieldName][index] = '';
            }else{
                specItem[fieldName] = '';
            }
            this.$forceUpdate();
        },
        getDiff : function(specItem){
            let result = Number(specItem.completeSpec) - Number(specItem.guideSpec);
            specItem.specDiff = result;
            return result;
        },
        getDiffClass : function(spec){
            let result = ''
            if( spec > 0 ){
                result='text-danger';
            }else if ( 0 > spec ){
                result='text-blue';
            }
            return result;
        },
        checkSampleDataShow : function(selectedValue){
            //console.log(selectedValue);
            //console.log(index);
            let result = true;
            if( !$.isEmpty(selectedValue)){
                result = false;
            }
            return result;
        },
        focusSampleTop : function(){
            $('#selected-sample-style1').focus();
            workApp.$forceUpdate();
            //console.log('실행');
        },
        selectedStyle : function(items, index){
            items.showSelectedStyle = index;
            workApp.$forceUpdate();
        },
        getGuideSpec : function(item, fitNo){
            let params = {
                mode : 'getGuideSpec' ,
                type : item.styleType ,
                fitNo : fitNo ,
            }
            $.post('work_ps.php', params, function(jsonResult){
                for(let idx in item.sampleItem){
                    try {
                        item.sampleItem[idx].guideSpec = jsonResult.data[idx];
                    }catch(e){}
                }
            });
        },
        nextSpec : function(refItem){
            try{
                console.log(refItem);
                this.$refs[refItem][0].focus();
            }catch(e){console.log(e)}
        },
        deleteSpec : function(checkList, field, optionIndex){
            checkList.forEach(function(item){
                item[field][optionIndex] = '';
            });
            this.$forceUpdate();
        },
        getUploadData : function(div, items, index){
            if( typeof uploadData[div] == 'undefined' ){
                $.msg('파일이 없습니다.','','error');
                return false;
            }
            let fileData = uploadData[div][0];

            let form = new FormData();
            form.append('mode', 'getUploadData');
            form.append('file', fileData);
            form.append('div', div);
            axios.post('work_ps.php', form, { 'Content-Type': 'multipart/form-data' })
                .then(function(result){
                    if( 200 == result.data.code ){
                        delete uploadData[div];
                        let refineDiv = div.replace('workPartData', 'partInfo').replace('workSubPartData', 'subPartInfo');
                        refineDiv = refineDiv.replace('Work', '');
                        //console.log( refineDiv );
                        items.docData.sampleData[index][refineDiv] = [];
                        result.data.data.forEach(function(resultData){
                            items.docData.sampleData[index][refineDiv].push( $.copyObject(resultData) );
                        });
                        //workApp.$forceUpdate();
                        //console.log( items.docData );
                        $.msg('처리 완료.','','success');
                    }else{
                        $.msg(result.data.message,'','error');
                    }
                })
                .catch(function(error){
                    $.msg(error,'','error');
                });
        },
        setUploadFile : function(div, e){
            uploadData[div] = e.target.files;
        },

        addOption : function(items,index){
            items.docData.sampleData[index].optionList.push({
                optionName : '',
                optionTotalCount : 0,
            });
        },
        removeOption : function(items,index,optionIndex){
            items.docData.sampleData[index].optionList.splice(optionIndex, 1);
            items.docData.sampleData[index].typeList.forEach((typeItem)=>{
                typeItem.optionCount.splice(optionIndex, 1);
            })
            items.docData.sampleData[index].checkList.forEach((checkItem)=>{
                checkItem.checkSpec.splice(optionIndex, 1);
            });
        },
        addType : function(items, index){
            let optionList = [];
            items.docData.sampleData[index].optionList.forEach(()=>{
                optionList.push('');
            });
            items.docData.sampleData[index].typeList.push({
                typeName : '',
                optionCount : optionList,
            });
        },

    };
</script>


<div class="col-xs-12" >
    <div class="table-title ">
        <div class="flo-left" >샘플 스타일 정보</div>
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
                                <option value="<?=$styleKey?>"><?=$styleValue?></option>
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
                    <div  v-show="0 >= items.docData.sampleData.length">샘플을 등록해주세요.</div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding:0px">
                    <div class="mgt10"><b>등록샘플 리스트</b></div>
                    <table class="table table-cols">
                        <colgroup>
                            <col style="width:70px" />
                            <col  />
                            <col style="width:150px"  />
                            <col  />
                            <col  />
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
                            <th class="text-center">의뢰일</th>
                            <th class="text-center">완료요청일</th>
                            <th class="text-center">샘플실</th>
                            <th class="text-center">확인/삭제</th>
                            <th class="text-center">다운로드</th>
                        </tr>
                        <tr v-for="(item, index) in items.docData.sampleData">
                            <td class="text-center">{% index+1 %}</td>
                            <td class="text-center">{% item.styleName %}</td>
                            <td class="text-center">{% item.serial %}</td>
                            <td class="text-center">{% item.sizeDisplay %}</td>
                            <td class="text-center">{% item.fit %}</td>
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
    <!--좌 (샘플 제작 정보 등) -->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >샘플 제작정보</div>
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
                        <label class="radio-inline">
                            <input type="radio" :name="'produceType' + index"  v-model="item.produceType" value="샘플"  />샘플
                        </label>
                    </td>
                    <th>제조국</th>
                    <td>
                        <input type="text" class="form-control" v-model="item.produceCountry">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--우 (샘플 제작처 등) -->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left">샘플 제작처</div>
            <div class="flo-right" ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>샘플실</th>
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
                    <th>샘플실 번호</th>
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
    <!--우 (샘플일정 등)-->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >샘플 일정</div>
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
    <!--좌 (샘플 도식화 이미지 첨부) -->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >샘플 도식화 이미지 첨부 <small class="text-muted">(파일을 상자에 올리거나 클릭하세요)</small> </div>
            <div class="flo-right " ></div>
        </div>
        <div>
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                <tr>
                    <th>첨부파일</th>
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
            <div class="flo-left" >샘플 도식화 이미지 미리보기</div>
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
            <div class="flo-left" >사이즈 스펙</div>
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
                <tr>
                    <th>샘플 제작 사이즈</th>
                    <td>
                        <input type="text" class="form-control w50" v-model="item.sampleSize">
                    </td>
                </tr>
            </table>
        </div>

        <!--스펙-->
        <div >
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:200px"/>
                    <col style="width:150px" />
                    <col style="width:150px" />
                    <col style="width:100px" />
                    <col style="width:120px" />
                    <col />
                </colgroup>
                <tr>
                    <th>구분</th>
                    <th>
                        완성스펙
                        <small class="text-muted btn btn-white btn-sm" @click="deleteSpec(item.sampleItem, 'completeSpec')">전체삭제</small>
                    </th>
                    <th>
                        기준스펙
                        <small class="text-muted btn btn-white btn-sm" @click="deleteSpec(item.sampleItem, 'guideSpec')">전체삭제</small>
                    </th>
                    <th>스펙차이</th>
                    <th>단위</th>
                    <th class="text-left">측정부위</th>
                </tr>
                <tr v-for="(specItem, specIndex) in item.sampleItem" :key="specIndex">
                    <td>
                        <input type="text" class="form-control" v-model="specItem.specItemName" placeholder="구분" >
                    </td>
                    <td>
                        <div class="form-inline">
                            <input type="text" class="form-control text-center w50" v-model="specItem.completeSpec" :ref="'completeSpec' + specIndex"   @keyup.38="nextSpec('completeSpec' + (specIndex-1) )" @keyup.40="nextSpec('completeSpec' + (specIndex+1) )"   @keyup.enter="nextSpec('completeSpec' + (specIndex+1) )" >
                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(specItem, 'completeSpec')">X</div>
                        </div>
                    </td>
                    <td>
                        <div class="form-inline">
                            <input type="text" class="form-control text-center w50" v-model="specItem.guideSpec" :ref="'guideSpec' + specIndex"   @keyup.38="nextSpec('guideSpec' + (specIndex-1) )" @keyup.40="nextSpec('guideSpec' + (specIndex+1) )"   @keyup.enter="nextSpec('guideSpec' + (specIndex+1) )">
                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(specItem, 'guideSpec')">X</div>
                        </div>
                    </td>
                    <td>
                        <input type="text" :class="'form-control text-center diff-text ' + getDiffClass(specItem.specDiff)" :value="getDiff(specItem)" disabled>
                    </td>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" value="cm"  :name="'specUnit' + index + '_' + specIndex" v-model="specItem.specUnit" />cm
                            <input type="radio" value="inch" :name="'specUnit' + index + '_' + specIndex" v-model="specItem.specUnit" />inch
                        </label>
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="specItem.specDescription" placeholder="측정부위">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--원단-->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >원단</div>
            <div class="flo-right form-inline" >
                <a href="<?=$downloadBasePath?>/data/form/원단등록양식.xls">
                    <div class="btn btn-white btn-icon-excel">원단 업로드 양식</div>
                </a>
                <input type="file" style="font-size:13px; font-weight:normal; display: inline-block" @change="setUploadFile('partData',event)">
                <div class="btn btn-sm btn-red" style="display: inline-block" @click="getUploadData('partData', items, index)">업로드</div>
            </div>
        </div>
        <div style="clear:both">
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:100px" /> <!--부위-->
                    <col style="width:100px" /> <!--설명-->
                    <col /> <!--폭-->
                    <col style="width:100px" /> <!--요척-->
                    <col style="width:350px" /> <!--원단이름-->
                    <col /> <!--색상-->
                    <col style="width:350px" /> <!--구매처정보-->
                    <col /> <!--추가삭제-->
                </colgroup>
                <tr>
                    <th>부위</th>
                    <th>설명</th>
                    <th>폭</th>
                    <th>요척</th>
                    <th>원단이름</th>
                    <th>색상</th>
                    <th>구매처정보</th>
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
                <a href="<?=$downloadBasePath?>/data/form/부자재등록양식.xls">
                    <div class="btn btn-white btn-icon-excel">부자재 업로드 양식</div>
                </a>
                <input type="file" style="font-size:13px; font-weight:normal; display: inline-block" @change="setUploadFile('subPartData',event)">
                <div class="btn btn-sm btn-red" style="display: inline-block" @click="getUploadData('subPartData', items, index)">업로드</div>
            </div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col style="width:100px" /><!--부위-->
                    <col style="width:100px" /> <!--설명-->
                    <col /><!--규격-->
                    <col style="width:100px" /><!--소요량-->
                    <col style="width:450px" /><!--아이템-->
                    <col /><!--색상-->
                    <col style="width:350px" /><!--구매처정보-->
                    <col /><!--추가/삭제-->
                </colgroup>
                <tr>
                    <th>부위</th>
                    <th>설명</th>
                    <th>규격</th>
                    <th>소요량</th>
                    <th>아이템</th>
                    <th>색상</th>
                    <th>구매처정보</th>
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
    <!--솜중량-->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >솜 중량 정보</div>
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
                    <th>첨부파일</th>
                    <td  style="padding:5px !important; text-align:left">
                        <div :id="'etc-dropzone' + index " class="set-dropzone"></div>
                        <div >
                            <ul >
                                <li v-for="(file, fileIndex) in item.fileEtc" :key="fileIndex">
                                    첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                    <a href="#" @click="removeFile(item.fileEtc, fileIndex)">삭제</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--기타-->
    <div class="col-xs-6">
        <div class="table-title ">
            <div class="flo-left" >기타</div>
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
                    <th>기타사항</th>
                    <td  style="padding:5px !important; text-align:left">
                        <textarea class="form-control" rows="6" v-model="item.etc"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

<div class="col-xs-12" style="border-top:dotted 2px #717171; width:100%">&nbsp;</div>

<script type="text/javascript">
    let uploadData = [];

    let sampleFileOptionList = [];
    let sampleFileList = [];
    let generalFileOptionList = [];
    let generalFileList = [];

    let sampleDropzone = new DropzoneFileProc();
    let etcDropzone = new DropzoneFileProc();
    let sampleFactoryData = JSON.parse('<?=$sampleFactoryData?>');

    let externSelectDocument = function(itemDocData, docData, parentApp){}
    let externMountedFnc = function(el){
    }
    let externAfterSaveProc = function(items, jsonResultData){
        sampleDropzone.upload( jsonResultData.sno );
        etcDropzone.upload( jsonResultData.sno );
    }

    vueUpdatedCallBackFnc = function(el){
        for(let key in el.items.docData.sampleData){
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

                //샘플 처리
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
                
                //기타 처리
                let etcDropzoneSetParams = {
                    workUrl : 'work_ps.php?mode=uploadDocumentFileDropzone',
                    dropzoneId : "div#etc-dropzone" + key,
                    fieldName1 : 'sampleData',
                    fieldName2 : 'fileEtc',
                };
                etcDropzone.setDropzone( el.items.docData.sampleData[key].fileEtc ,key, etcDropzoneSetParams);
                etcDropzone.fileList[key].on("complete", function(file) {
                    completeProc(etcDropzone, file);
                });

            }
        }
    }

    let externComputed = function(){}

    let externVueMethod = {
        //샘플추가
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
                //샘플 파일
                //$("html, body").animate({ scrollTop: $(document).height() }, 500);
            } );
        },
        removeSample : (data, index) => {
            let msgPromise = $.msgConfirm('선택하신 스타일을 삭제하시겠습니까?', "");
            msgPromise.then( (result)=>{
                if( result.isConfirmed ){
                    data.splice(index,1);
                    sampleFileOptionList.splice(index,1);
                    sampleFileList.splice(index,1);
                    generalFileOptionList.splice(index,1);
                    generalFileList.splice(index,1);
                    sampleDropzone.fileList.splice(index,1);
                    etcDropzone.fileList.splice(index,1);
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
        delSpec : function(specItem, fieldName){
            specItem[fieldName] = '';
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
                this.$refs[refItem][0].focus();
            }catch(e){}
        },
        deleteSpec : function(sampleItem, field){
            sampleItem.forEach(function(item){
                item[field] = '';
            });
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
                    let refineDiv = div.replace('Data', '') + 'Info';
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
        }
    };
</script>


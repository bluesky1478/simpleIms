<div class="col-xs-12" >
    <div class="table-title ">
        <div class="flo-left" >샘플 스타일 검색/추가</div>
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
                <th>스타일 검색</th>
                <td>
                    <select class="form-control" v-model="items.showSelectedStyle" @change="focusSampleTop" >
                        <option value="">전체</option>
                        <option :value="index" v-for="(item, index) in items.docData.sampleData" :key="index" v-if="!$.isEmpty(item.serial)">{% index+1 %}. {% item.styleName %} ({% item.serial %})</option>
                        <option :value="index" v-for="(item, index) in items.docData.sampleData" :key="index" v-if="$.isEmpty(item.serial)">{% index+1 %}. {% item.styleName %}</option>
                    </select>
                </td>
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
            </tr>
        </table>
    </div>
</div>

<div v-for="(item, index) in items.docData.sampleData" :key="index" v-show="checkSampleDataShow(items.showSelectedStyle, index)" >

    <!--타이틀-->
    <div class="col-xs-12"  style="border-top:dotted 2px #717171 !important;" >
        <div class="" >
            <div class="flo-left"><h2>Style{% index + 1 %} - {% item.styleName %}</h2></div>
            <div  class="flo-left" style="margin-left:10px">
                <h2><div class="btn btn-red " @click="removeSample(items.docData.sampleData, index)" v-show="items.docData.sampleData.length > 1" >- 삭제</div></h2>
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
                </colgroup>
                <tr>
                    <th>스타일</th>
                    <td>{% item.styleName %}</td>
                </tr>
                <tr>
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
                </tr>
                <tr>
                    <th>핏</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" :name="'fit' + index" v-model="item.fit" value="슬림" />슬림
                        </label>
                        <label class="radio-inline">
                            <input type="radio" :name="'fit' + index"  v-model="item.fit" value="기본" />기본
                        </label>
                        <label class="radio-inline">
                            <input type="radio" :name="'fit' + index"  v-model="item.fit" value="루즈" />루즈
                        </label>
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
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >샘플 도식화 이미지 첨부</div>
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
                        지시스펙
                        <small class="text-muted btn btn-white btn-sm" @click="deleteSpec(item.sampleItem, 'guideSpec')">전체삭제</small>
                    </th>
                    <th>
                        실제스펙
                        <small class="text-muted btn btn-white btn-sm" @click="deleteSpec(item.sampleItem, 'completeSpec')">전체삭제</small>
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
                            <input type="text" class="form-control text-center w50" v-model="specItem.guideSpec" :ref="'guideSpec' + specIndex"   @keyup.38="nextSpec('guideSpec' + (specIndex-1) )" @keyup.40="nextSpec('guideSpec' + (specIndex+1) )"   @keyup.enter="nextSpec('guideSpec' + (specIndex+1) )">
                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(specItem, 'guideSpec')">X</div>
                        </div>
                    </td>
                    <td>
                        <div class="form-inline">
                            <input type="text" class="form-control text-center w50" v-model="specItem.completeSpec" :ref="'completeSpec' + specIndex"   @keyup.38="nextSpec('completeSpec' + (specIndex-1) )" @keyup.40="nextSpec('completeSpec' + (specIndex+1) )"   @keyup.enter="nextSpec('completeSpec' + (specIndex+1) )" >
                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(specItem, 'completeSpec')">X</div>
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

    <!-- 체크리스트 -->
    <div class="col-xs-12">
        <div class="table-title ">
            <div class="flo-left" >체크리스트</div>
            <div class="flo-right " >
            </div>
        </div>
        <div>
            <table class="table table-cols center-header center-contents sample-spec-table">
                <colgroup>
                    <col />
                    <col />
                    <col />
                    <col class="width-md" />
                </colgroup>
                <tr>
                    <th>체크사항</th>
                    <th>문제사항</th>
                    <th>비고</th>
                    <th>추가/삭제</th>
                </tr>
                <tr v-for="(checkItem, checkIndex) in item.checkItem" :key="checkIndex">
                    <td><input type="text" class="form-control" v-model="checkItem.check1"></td>
                    <td><input type="text" class="form-control" v-model="checkItem.check2"></td>
                    <td><input type="text" class="form-control" v-model="checkItem.check3"></td>
                    <td>
                        <div class="btn btn-sm btn-white" @click="addListData2(item.checkItem)" v-show="(item.checkItem.length-1) === checkIndex">+추가</div>
                        <div class="btn btn-sm btn-white" @click="removeListData(item.checkItem, checkIndex)" v-show="item.checkItem.length > 1">-삭제</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>


</div>

<div class="col-xs-12" style="border-top:dotted 2px #717171; width:100%">&nbsp;</div>

<!--스타일추가-->
<div class="col-xs-12"  v-if="items.docData.sampleData.length > 0">
    <div class="table-title ">
        <div class="flo-left" >샘플 스타일 검색/추가</div>
        <div class="flo-right " ></div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>스타일 검색</th>
                <td>
                    <select class="form-control" v-model="items.showSelectedStyle" @change="focusSampleTop">
                        <option value="">전체</option>
                        <option :value="index" v-for="(item, index) in items.docData.sampleData" :key="index">{% item.styleName %}</option>
                    </select>
                </td>
                <th>스타일 추가</th>
                <td>
                    <div class="form-inline">
                        <select class="form-control" id="selected-sample-style2">
                            <option value="">선택</option>
                            <?php foreach( \Component\Work\WorkCodeMap::STYLE_TYPE as $styleKey => $styleValue) { ?>
                                <option value="<?=$styleKey?>"><?=$styleValue?></option>
                            <?php } ?>
                        </select>
                        <div class="btn btn-sm btn-red" @click="addSample(items.docData.sampleData,2)">+ 스타일추가</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script type="text/javascript">
    let sampleFileOptionList = [];
    let sampleFileList = [];
    let generalFileOptionList = [];
    let generalFileList = [];

    let sampleDropzone = new DropzoneFileProc();
    let etcDropzone = new DropzoneFileProc();

    let setPicker = function(){
        $('.js-datepicker').off("dp.change").on("dp.change", function(e) {
            var d = new Date(e.date);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var year = d.getFullYear();
            var setter = year + '-' + (String(month)).padStart(2,'0') + '-' + (String(day)).padStart(2,'0');
            let index = e.target.attributes.getNamedItem('data-index').value;
            console.log();
            //app.items[index].supplyDt = setter;
        });
    }

    let sampleFactoryData = JSON.parse('<?=$sampleFactoryData?>');

    let externSelectDocument = function(itemDocData, docData, parentApp){
        for(let idx in docData.sampleData){
            for(let idx2 in docData.sampleData[idx].sampleItem){
                docData.sampleData[idx].sampleItem[idx2].completeSpec = '';
            }
            //스타일별 체크리스트 가져오기
            docData.sampleData[idx].checkItem = [
                {'check1':'','check2':'','check3':''}
            ];
            let promise = $.postWork({'mode' : 'getStyleCheckList', 'type': docData.sampleData[idx].styleType});
            promise.then(function(jsonResult){
                docData.sampleData[idx].checkItem = jsonResult.data;
                parentApp.$forceUpdate();
            });
        }

        //console.log(docData);
    }
    let externMountedFnc = function(el){
    }
    let externAfterSaveProc = function(items, jsonResultData){
        sampleDropzone.upload( jsonResultData.sno );
        etcDropzone.upload( jsonResultData.sno );
    }

    vueUpdatedCallBackFnc = function(el){
        for(let key in el.items.docData.sampleData){
            if(  typeof sampleDropzone.fileList[key] == 'undefined' ){

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

    let externVueMethod = {
        getUploadWorkUrl : function(index){
            console.log('get work url index : ' + index);
            //return sampleFileOptionList[index].url;
            return 'test';
        },
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
                isCheck : 1,
            };
            $.post( 'work_ps.php', params, function(jsonResult){
                sampleData.push(jsonResult.data);
                //샘플 파일
                $("html, body").animate({ scrollTop: $(document).height() }, 500);
            } );
        },
        removeSample : (data, index) => {
            data.splice(index,1);
            sampleFileOptionList.splice(index,1);
            sampleFileList.splice(index,1);
            generalFileOptionList.splice(index,1);
            generalFileList.splice(index,1);
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
        checkSampleDataShow : function(selectedValue, index){
            let result = true;
            if( !$.isEmpty(selectedValue) && selectedValue != index ){
                result = false;
            }
            return result;
        },
        focusSampleTop : function(){
            $('#selected-sample-style1').focus();
            //console.log('실행');
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
        }


    };
</script>


<form name="frmOrder" method="post" class="frm-order" id="documentApp" @submit.stop.prevent="onSubmit(items)"  >
    <?php include 'accept_history.php'; ?>
    <div class="page-header js-affix">
        <h3><?=$title?></h3>
        <div class="btn-group">
            <input type="submit" value="저장" class="btn btn-red btn-reg-control"  @click="officialSave(items)" />
        </div>
    </div>
    <div class="row">
        <?php include 'accept_area.php'?>

        <!--CONTENTS BEGIN-->

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
                                <select class="form-control" id="selected-sample-style">
                                    <option value="">선택</option>
                                    <?php foreach( $styleList as $styleKey => $styleValue) { ?>
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
                        <td colspan="4" class="reg-item-list">
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
            <div class="col-xs-12 item-list-area" >
                <div class="" >
                    <div class="flo-left"><h2>{% item.styleName %} <small style="font-weight:normal">({% item.serial %})</small></h2></div>
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

            <!--좌 (샘플 도식화 이미지 첨부) -->
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
                                <date-picker v-model="item.requestDt" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
                            </td>
                        </tr>
                        <tr>
                            <th>완료 요청일</th>
                            <td>
                                <date-picker v-model="item.completeDt" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
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
            <!--사이즈스펙 -->
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
                                <input type="text" class="form-control w100p" v-model="item.sampleSize">
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
            <!--사이즈스펙 여기 . -->
            <div class="col-xs-12">
                <div class="table-title ">
                    <div class="flo-left" >사이즈 스펙 상세</div>
                    <div class="flo-right form-inline" >
                        <div class="btn btn-white btn-icon-excel" @click="uploadFormDownload(item.sampleItem)">사이즈 스펙 양식 다운로드</div>
                        <input type="file" style="font-size:13px; font-weight:normal; display: inline-block" @change="setUploadFile('sampleItem',event)">
                        <div class="btn btn-sm btn-red" style="display: inline-block" @click="getUploadData('sampleItem', items, index)">업로드</div>
                    </div>
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
            <!--체크리스트-->
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

        <!--CONTENTS END-->

    </div>
    <div class="w100 text-right">
        <input type="submit" value="저장" class="btn btn-lg btn-red btn-reg-control"  style="width:300px; font-size:15px; border-radius: 10px"  @click="officialSave(items)" />
    </div>
</form>

<script type="text/javascript">
    let uploadData = [];
    let sampleDropzone = new DropzoneFileProc();
    let sampleFactoryData = JSON.parse('<?=$sampleFactoryData?>');
    let externMountedFnc = function(){}

    let fileUploadCnt = 0;
    let upFileArray = [];
    let upFileList = [];

    let externVueMethod = {
        /**
         * 저장
         */
        onSubmit : function (items) {

            $('#layerFileDim').removeClass('display-none');

            for(let idx in items.docData.sampleData){
                items.docData.sampleData[idx].fileSamplePreview = [];
            }

            upFileArray = sampleDropzone.getFileList();
            upFileArray.forEach((upFile)=>{
                if(upFile.files.length > 0){
                    upFileList.push(upFile);
                }
            });

            WorkDocument.saveDocument(DOC_DEPT, DOC_TYPE, items, (items, jsonResultData)=>{
                $('#layerDim').addClass('display-none');
                if( 0 >= upFileList.length ){
                    $('#layerFileDim').addClass('display-none');
                }else{
                    sampleDropzone.upload( jsonResultData.sno );
                }
            });
        },
        //샘플추가
        addSample : function(sampleData, index){
            $.showDim();

            let selectedStyleSno = $('#selected-sample-style').val();

            if( $.isEmpty(selectedStyleSno) ){
                alert('추가하실 스타일을 선택해주세요.');
                return false;
            }
            let params = {
                mode : 'getDefaultSampleData',
                styleSno : selectedStyleSno,
                docPart : 'designCheck',
            };
            //console.log( params );

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
                $.hideDim();
            } );
        },
        removeSample : (data, index) => {
            let msgPromise = $.msgConfirm('선택하신 스타일을 삭제하시겠습니까?', "");
            msgPromise.then( (result)=>{
                if( result.isConfirmed ){
                    data.splice(index,1);
                    sampleDropzone.fileList.splice(index,1);
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
            $('#selected-sample-style').focus();
            workApp.$forceUpdate();
            //console.log('실행');
        },
        selectedStyle : function(items, index){
            items.showSelectedStyle = index;
            workApp.$forceUpdate();
        },
        getGuideSpec : function(item, fitNo){
            $.showDim();
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
                $.hideDim();
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
                        items.docData.sampleData[index][div] = [];
                        result.data.data.forEach(function(resultData){
                            items.docData.sampleData[index][div].push( $.copyObject(resultData) );
                        });
                        //workApp.$forceUpdate();
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
        /**
         * 피팅체크리스트 업로드 양식 다운로드
         * @param specItem
         */
        uploadFormDownload : function(specItem){
            let specItemStr = encodeURIComponent(JSON.stringify(specItem));
            location.href='form_download.php?specItemStr='+specItemStr+'&type=downloadSampleSpecItem';
        }
    };

    //재정의
    vueUpdatedCallBackFnc = function(el){
        for(let key in el.items.docData.sampleData){

            if(  typeof sampleDropzone.fileList[key] == 'undefined' ){

                let completeProc = function(dropzoneObject, file, response){
                    fileUploadCnt++;
                    let fieldName1 = dropzoneObject.optionList[key].fieldName1;
                    let fieldName2 = dropzoneObject.optionList[key].fieldName2;
                    let docSno = dropzoneObject.optionList[key].docSno;

                    workApp.items.docData[fieldName1][key][fieldName2] = response.data[fieldName1][key][fieldName2];
                    //console.log('최종 업데이트 체크 : ' + fileUploadCnt + ' : ' + upFileList.length);
                    if( fileUploadCnt === upFileList.length){
                        let params = {
                            mode : 'saveDocument',
                            projectSno : projectSno,
                            docDept : DOC_DEPT,
                            docType : DOC_TYPE,
                            docData : workApp.items['docData'],
                            sno : docSno,
                        }
                        //console.log(params);
                        WorkDocument.save(params).then((jsonResult)=>{
                            location.href='document_reg.php?projectSno=' + projectSno + '&sno=' + jsonResult.data.sno + 'docDept='+DOC_DEPT+'&docType='+DOC_TYPE ;
                            console.log('최종 완료');
                        });
                    }
                }

                //샘플 처리 시작---------------------------------------------------------------------------------------------
                let dropzoneSetParams = {
                    workUrl : 'work_ps.php?mode=uploadDocumentFileDropzone',
                    dropzoneId : "div#sample-dropzone" + key,
                    fieldName1 : 'sampleData',
                    fieldName2 : 'fileSample',
                    success : function(file, response){
                        completeProc(sampleDropzone, file, response);
                    }
                };

                sampleDropzone.setDropzone( el.items.docData.sampleData[key].fileSample ,key, dropzoneSetParams);
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
                //샘플 처리 종료---------------------------------------------------------------------------------------------
            }
        }
    }


</script>
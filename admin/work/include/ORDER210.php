<form name="frmOrder" method="post" class="frm-order" id="documentApp" @submit.stop.prevent="onSubmit(items)"  >
    <?php include 'accept_history.php'; ?>
    <?php include 'manager_list.php'?>
    <?php include 'mail_history.php'?>
    <div class="page-header js-affix">
        <h3><?=$title?></h3>
        <div class="btn-group">
            <input type="submit" value="저장" class="btn btn-red btn-reg-control"  @click="officialSave(items)" />
        </div>
    </div>
    <div class="row">
        <?php include 'accept_area.php'?>

        <!--CONTENTS BEGIN-->

        <!--발주 기본정보-->
        <div class="col-xs-6" >
            <div class="table-title ">
                <div class="flo-left" >기본정보</div>
                <div class="flo-right " >
                    <div v-if="!$.isEmpty(items.mallData.sno)" class="btn btn-sm btn-gray inline" @click="window.open( '<?=$workFrontURL?>/workAdmin/document.php?sno=' + items.mallData.sno )">폐쇄몰 준비자료 확인</div>
                    <div class="inline"  v-else >폐쇄몰 준비자료 없음</div>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md"  />
                        <col style="width:350px" />
                    </colgroup>
                    <tr>
                        <th>피드백 요청일 / 상태</th>
                        <td>
                            <div class=" input-group" style="width:120px; display:inline-block; float:left; margin-right:15px" >
                                <date-picker v-model="items.docData.feedbackDt" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
                            </div>
                            <div v-show="items.version > 0" class="godo inline" style="width:150px; ">
                                <select :class="'form-control color-status color-status-'+items.isCustomerApplyFl" v-model="items.isCustomerApplyFl" style="height:35px!important; width:100% ; text-align:center" v-if="items.version > 0" >
                                    <option value="n" class="normal">미확정</option>
                                    <option value="y" class="success">확정완료</option>
                                </select>
                            </div>
                            <span class="text-muted" v-if="'y' === items.isCustomerApplyFl">{% items.isCustomerApplyDt %}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            매뉴얼 파일 업로드
                        </th>
                        <td >
                            <div id="manual-dropzone" class="set-dropzone" ></div>
                            <div>
                                <ul >
                                    <li v-for="(file, fileIndex) in items.docData.fileManual" :key="fileIndex">
                                        첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                        <a href="#" @click="removeFile(items.docData.fileEtc, fileIndex)">삭제</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!--일정-->
        <div class="col-xs-6" >
            <div class="table-title ">
                <div class="flo-left" >일정</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md"  />
                        <col   />
                    </colgroup>
                    <tr>
                        <th>디자인 확정</th>
                        <td>
                            <date-picker v-model="items.docData.step1" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th>샘플확정</th>
                        <td>
                            <date-picker v-model="items.docData.step2" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th>고객발주</th>
                        <td>
                            <date-picker v-model="items.docData.step3" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th>납품예정</th>
                        <td>
                            <date-picker v-model="items.docData.step4" value-type="format" format="YYYY-MM-DD" :placeholder="'0000-00-00'"  :lang="lang"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:58px;border-bottom:none" colspan="2">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </div>

        <!--결제정보-->
        <div class="col-xs-6" >
            <div class="table-title ">
                <div class="flo-left" >결제정보</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md"  />
                        <col />
                    </colgroup>
                    <tr>
                        <th>계약서 진행</th>
                        <td>
                            <label class="radio-inline" >
                                <input type="radio" :name="'contractFl'" v-model="items.docData.contractFl" value="유" />유
                            </label>
                            <label class="radio-inline" >
                                <input type="radio" :name="'contractFl'" v-model="items.docData.contractFl" value="무" />무
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>계약서 발행</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.contractPub"></td>
                    </tr>
                    <tr>
                        <th>결제조건</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.payCondition"></td>
                    </tr>
                    <tr>
                        <th>결제방법</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.payMethod"></td>
                    </tr>
                </table>
            </div>
        </div>

        <!--발송정보-->
        <div class="col-xs-6" >
            <div class="table-title ">
                <div class="flo-left" >발송정보</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md"  />
                        <col />
                    </colgroup>
                    <tr>
                        <th>발송방법</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.deliveryMethod"></td>
                    </tr>
                    <tr>
                        <th>발송주소</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.deliveryAddress"></td>
                    </tr>
                    <tr>
                        <th>담당자</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.deliveryManager"></td>
                    </tr>
                    <tr>
                        <th>담당자 연락처</th>
                        <td><input type="text" class="form-control"  v-model="items.docData.deliveryPhone"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-xs-12" >
            <div class="table-title ">
                <div class="flo-left" >상품 스타일 정보</div>
                <div class="flo-right " >
                    <button type="button" class="btn btn-red btn-sm "  @click="window.open('<?=$documentCustomerPreviewUrl?>')">고객 화면 미리보기</button>
                </div>
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
                            <div  v-show="0 >= items.docData.sampleData.length">상품을 등록해주세요.</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="reg-item-list">
                            <div class="mgt10"><b>등록 상품 리스트</b></div>
                            <table class="table table-cols">
                                <colgroup>
                                    <col class="w70p" /> <!--번호-->
                                    <col  /> <!--스타일-->
                                    <col class="w150p"  /> <!--시리얼-->
                                    <col class="w150p" /> <!--사이즈-->
                                    <col class="w100p" />  <!--입고수량-->
                                    <col class="w100p"  /> <!--발주수량-->
                                    <col class="w100p" /><!-- 의뢰일-->
                                    <col class="w100p" /><!-- 완료요청일-->
                                    <col class="w100p" /><!-- 생산처-->
                                    <col class="w150p" /><!--확인/삭제-->
                                </colgroup>
                                <tr >
                                    <th class="text-center">번호</th>
                                    <th class="text-center">스타일</th>
                                    <th class="text-center">S/#</th>
                                    <th class="text-center">사이즈</th>
                                    <th class="text-center">입고수량</th>
                                    <th class="text-center">발주수량</th>
                                    <th class="text-center">의뢰일</th>
                                    <th class="text-center">완료요청일</th>
                                    <th class="text-center">생산처</th>
                                    <th class="text-center">확인/삭제</th>
                                </tr>
                                <tr v-for="(item, index) in items.docData.sampleData">
                                    <td class="text-center">{% index+1 %}</td>
                                    <td class="text-center">
                                        <span class="cursor-pointer hover-btn" @click="selectedStyle(items, index)">
                                            {% item.styleName %}
                                        </span>
                                    </td>
                                    <td class="text-center">{% item.serial %}</td>
                                    <td class="text-center">{% item.sizeDisplay %}</td>
                                    <td class="text-center">{% $.setNumberFormat(item.itemInputTotalCount) %}</td>
                                    <td class="text-center">{% $.setNumberFormat(item.itemTotalCount) %}</td>
                                    <td class="text-center">{% item.requestDt %}</td>
                                    <td class="text-center">{% item.completeDt %}</td>
                                    <td class="text-center">{% getSampleFactoryData(item.sampleFactorySno, 'factoryName') %}</td>
                                    <td class="text-center">
                                        <div class="btn btn-sm btn-white" @click="selectedStyle(items, index)">확인</div>
                                        <div class="btn btn-sm btn-red" @click="removeSample(items.docData.sampleData, index)">삭제</div>
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

        <!--Loop for sameDataList-->
        <div v-for="(item, index) in items.docData.sampleData" :key="index" v-show="index == items.showSelectedStyle" >
            <!--타이틀-->
            <div class="col-xs-12 item-list-area">
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
                            <th>원단정보</th>
                            <td colspan="3">
                                <input type="text" class="form-control" v-model="item.fabricInfo" >
                            </td>
                    </table>
                </div>
            </div>
            <!--우 (샘플 제작처 등) -->
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
            <!--우 (샘플일정 등)-->
            <div class="col-xs-6">
                <div class="table-title ">
                    <div class="flo-left" >도식화 이미지 첨부 <small class="text-muted">(파일을 상자에 올리거나 클릭하세요)</small> </div>
                    <div class="flo-right " ></div>
                </div>
                <div>
                    <table class="table table-cols">
                        <colgroup>
                            <col style="width:50%"/>
                            <col style="width:50%"/>
                        </colgroup>
                        <tr>
                            <th>썸네일</th>
                            <th>상품이미지</th>
                        </tr>
                        <tr>
                            <td style="padding:5px">
                                <div :id="'thumbnail-dropzone' + index " class="set-dropzone"></div>
                            </td>
                            <td style="padding:5px">
                                <div :id="'sample-dropzone' + index " class="set-dropzone"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <ul >
                                    <li v-for="(file, fileIndex) in item.fileThumbnail" :key="fileIndex">
                                        첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                        <a href="#" @click="removeFile(item.fileThumbnail, fileIndex)">삭제</a>
                                    </li>
                                </ul>
                            </td>
                            <td>
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
                    <div class="flo-left" >썸네일 / 상품이미지 미리보기</div>
                    <div class="flo-right " ></div>
                </div>
                <table class="table table-cols">
                    <tr>
                        <td>
                            <div v-for="(file, fileIndex) in item.fileThumbnail " :key="fileIndex" style="width:100%; margin-bottom:20px" >
                                <img :src="file.path" >
                            </div>
                            <div v-for="(file, fileIndex) in item.fileThumbnailPreview " :key="fileIndex" style="width:100%; margin-bottom:20px">
                                <img :src="file.imageUrl">
                            </div>
                            <div v-for="(file, fileIndex) in item.fileSample " :key="fileIndex" style="width:100%; margin-bottom:20px" >
                                <img :src="file.path" >
                            </div>
                            <div v-for="(file, fileIndex) in item.fileSamplePreview " :key="fileIndex" style="width:100%; margin-bottom:20px">
                                <img :src="file.imageUrl" >
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!--사이즈스펙 -->
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
                                <input type="text" class="form-control" v-model="typeItem.optionCount[optionIndex]" placeholder="발주수" style="width:45%;float:left;margin-right:5px;">
                                <input type="text" class="form-control" v-model="typeItem.inputCount[optionIndex]" placeholder="입고수" style="width:45%;float:left;">
                            </td>
                            <td>&nbsp</td>
                        </tr>
                        <tr >
                            <th class="text-center">
                                <div>발주총계 : {% $.setNumberFormat(item.itemTotalCount) %}개</div>
                                <div>입고총계 : {% $.setNumberFormat(item.itemInputTotalCount) %}개</div>
                            </th>
                            <td v-for="(optionItem, optionIndex) in item.optionList" class="text-center"><!-- Loop는 옵션의 수만큼 -->
                                발:{% $.setNumberFormat(optionItem.optionTotalCount) %}개 / 입:{% $.setNumberFormat(optionItem.optionInputTotalCount) %}개
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
                    <div class="flo-right " >
                        <div class="btn btn-white btn-icon-excel" @click="uploadFormDownload(item.checkList, item.optionList)">사이즈 스펙 양식 다운로드</div>
                        <input type="file" style="font-size:13px; font-weight:normal; display: inline-block" @change="setUploadFile('checkList',event)">
                        <div class="btn btn-sm btn-red" style="display: inline-block" @click="getUploadData('checkList', items, index)">업로드</div>
                    </div>
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
                    <div class="flo-left" >사이즈 스펙 이미지</div>
                    <div class="flo-right " ></div>
                </div>
                <div>
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md"/>
                            <col />
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
                            <th rowspan="2" style="border-right:solid 1px #E6E6E6">
                                {% markIndex+1 %}
                                <!--<div class="btn btn-white btn-sm" @click="removeListData(item.markList, markIndex)">-제거</div>-->
                            </th>
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
                            <col />
                        </colgroup>
                        <tr>
                            <th>마크/라벨 위치<br> 이미지</th>
                            <td >
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
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <!--코멘트-->
        <div class="col-xs-12"  v-show="items.version > 0">
            <div class="table-title ">
                <div class="flo-left" >코멘트</div>
                <div class="flo-right " ></div>
            </div>
            <div>
                <table class="table table-cols" >
                    <colgroup>
                        <col class="width-md" />
                        <col />
                    </colgroup>
                    <tr>
                        <th>작성자</th>
                        <th>내용</th>
                    </tr>
                    <tr v-for="(comment, commentIndex) in items.docData.commentList">
                        <td class="text-center">
                            <div style="color:#717171" v-if="comment.writeManagerSno > 0">
                                {% comment.writeManagerNm %}<small class="text-muted">(관리자)</small>
                            </div>
                            <div  v-else>
                                <b>{% comment.writeManagerNm %}</b><small class="text-muted">(고객)</small>
                            </div>
                            <small class="text-muted">{% comment.regDt %}</small>
                        </td>
                        <td>
                            <span v-html="$.nl2br(comment.contents)"></span>
                            <!--
                            <img src="<?= PATH_ADMIN_GD_SHARE ?>img/icon_new.png">
                            -->
                        </td>
                    </tr>
                </table>
                <div class="form-inline" style="padding:5px">
                    <textarea class="form-control comment-text-area" v-model="comment" style="width:93%; height:60px" placeholder="고객에게 전달할 코멘트를 입력하세요."></textarea>
                    <div class="btn btn-lg btn-red comment-reg-btn" @click="regComment()">코멘트 저장</div>
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
    let thumbnailDropzone = new DropzoneFileProc();
    let etcDropzone = new DropzoneFileProc();
    let sampleFactoryData = JSON.parse('<?=$sampleFactoryData?>');
    let externMountedFnc = function(){
        setDropzone(0, 'fileManual');
        dropzoneInstanceList.push(new Dropzone( "div#manual-dropzone" , dropzoneOptionList[0]));
    }

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
            upFileList = [];

            let sampleFiles = sampleDropzone.getFileList();
            for(let sampleFileKey in sampleFiles){
                let upFile = sampleFiles[sampleFileKey];
                if(upFile.files.length > 0){
                    upFileList.push(upFile);
                }
            }

            let thumbnailFiles = thumbnailDropzone.getFileList();
            for(let thumbnailFileKey in thumbnailFiles){
                let upFile = thumbnailFiles[thumbnailFileKey];
                if(upFile.files.length > 0){
                    upFileList.push(upFile);
                }
            }

            let etcFiles = etcDropzone.getFileList();
            for(let etcFileKey in etcFiles){
                let upFile = etcFiles[etcFileKey];
                if(upFile.files.length > 0){
                    upFileList.push(upFile);
                }
            }

            //console.log('Upload File List length : ' + upFileList.length);


            WorkDocument.saveDocument(DOC_DEPT, DOC_TYPE, items, (items, jsonResultData)=>{
                
                //확정상태 변경.
                console.log( '확정상태 체크' );
                $.postWork({
                    mode : 'setOrderStatus',
                    status : items.isCustomerApplyFl,
                    documentSno : documentSno,
                });

                $('#layerDim').addClass('display-none');
                
                if( 0 >= upFileList.length ){
                    console.log('Upload 파일 없음');
                    $('#layerFileDim').addClass('display-none');
                }else{
                    console.log(' ===================== Upload 시작 =======================');
                    sampleDropzone.upload( jsonResultData.sno );
                    thumbnailDropzone.upload( jsonResultData.sno );
                    etcDropzone.uploadNew( jsonResultData.sno );
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
                docPart : 'designWork',
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
                    thumbnailDropzone.fileList.splice(index,1);
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
            form.append('optionCount', items.docData.sampleData[index].optionList.length);

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
                optionCount : [],
                inputCount : [],
            });
        },
        //코멘트 등록
        regComment : function(){
            var parentEl = this;
            //console.log( parentEl.comment );
            if( $.isEmpty( parentEl.comment ) ){
                return false;
            }
            let param ={
                mode : 'saveOrderComment',
                sno : documentSno,
                contents : parentEl.comment,
            };
            $.post('work_ps.php',param, function(json){
                //console.log(json);
                if( $.isEmpty(parentEl.items.docData.commentList) ){
                    parentEl.docData.commentList = [];
                }
                parentEl.comment = '';
                parentEl.items.docData.commentList.splice(0, parentEl.items.docData.commentList.length);
                parentEl.items.docData.commentList = json.data.commentList; //교체.
            });
        },
        /**
         * 스펙 업로드 양식 다운로드
         * @param specItem
         * @param optionList
         */
        uploadFormDownload : function(specItem, optionList){
            let specItemStr = encodeURIComponent(JSON.stringify(specItem));
            let optionListParam = [];
            optionList.forEach((optionData)=>{
                optionListParam.push(optionData.optionName);
            });
            location.href='form_download.php?specItemStr='+specItemStr+'&type=downloadWorkSpecItem&optionList='+optionListParam.join(',');
        }
    };

    let externComputed = {
        getTotalCount : function(){
            let totalCount = 0;
            let inputTotalCount = 0;
            this.items.docData.sampleData.forEach((sampleData, sampleIndex)=>{
                let itemTotalCount = 0;
                let itemInputTotalCount = 0;

                //Type별 초기화
                sampleData.typeList.forEach( (typeData) =>{
                    typeData.typeTotalCount = 0;
                    typeData.typeInputTotalCount = 0;
                });
                sampleData.optionList.forEach((optionData, optionIndex)=>{
                    let optionTotalCount = 0;
                    let optionInputTotalCount = 0;

                    sampleData.typeList.forEach( (typeData) =>{
                        let typeCount = Number( $.isEmpty(typeData.optionCount[optionIndex])? 0 : typeData.optionCount[optionIndex] );
                        let inputCount = Number( $.isEmpty(typeData.inputCount[optionIndex])? 0 : typeData.inputCount[optionIndex] );
                        typeData.typeTotalCount +=  Number(typeCount);
                        typeData.typeInputTotalCount +=  Number(inputCount);
                        optionTotalCount += Number(typeCount);
                        optionInputTotalCount += Number(inputCount);
                    });

                    optionData.optionTotalCount = optionTotalCount; //option
                    optionData.optionInputTotalCount = optionInputTotalCount; //option

                    itemTotalCount += optionTotalCount; // item
                    itemInputTotalCount += optionInputTotalCount; // item

                    totalCount += optionTotalCount; // total
                    inputTotalCount += optionInputTotalCount; // total
                });
                sampleData.itemTotalCount = itemTotalCount;//item
                sampleData.itemInputTotalCount = itemInputTotalCount;//item
            });

            return $.setNumberFormat(totalCount);
        }
    }

    let completeProc = function(completeParam){
        let dropzoneObject = completeParam['dropzoneObject'];
        //let file = completeParam['file'];
        let response = completeParam['response'];
        let refreshField = completeParam['refreshField'];
        let key = completeParam['key'];
        let fileKey = completeParam['fileKey'];

        fileUploadCnt++;
        let docSno = documentSno;
        if( typeof refreshField == 'undefined' ){
            let fieldName1 = dropzoneObject.optionList[fileKey].fieldName1;
            let fieldName2 = dropzoneObject.optionList[fileKey].fieldName2;
            workApp.items.docData[fieldName1][key][fieldName2] = response.data[fieldName1][key][fieldName2];
        }else{
            if( isNaN(refreshField) ){
                workApp.items.docData.sampleData[key][refreshField] = response.data.sampleData[key][refreshField];
            }else{
                workApp.items.docData.sampleData[key]['markList'][refreshField]['fileMark'] = response.data.sampleData[key]['markList'][refreshField]['fileMark'];
            }
        }

        if( fileUploadCnt === upFileList.length){
            let params = {
                mode : 'saveDocument',
                projectSno : projectSno,
                docDept : DOC_DEPT,
                docType : DOC_TYPE,
                docData : workApp.items['docData'],
                sno : docSno,
            }
            WorkDocument.save(params).then((jsonResult)=>{
                location.href='document_reg.php?projectSno=' + projectSno + '&sno=' + jsonResult.data.sno + 'docDept='+DOC_DEPT+'&docType='+DOC_TYPE ;
                //console.log('최종 완료');
            });
        }
    }

    let setEtcDropzone = function(fileKey, params, refreshField, indexKey){
        if ( typeof etcDropzone.fileList[fileKey] == 'undefined' ) {
            let dropzoneSetParams = {
                workUrl: 'work_ps.php?mode=uploadFileNew',
                dropzoneId: params['dropzoneId'],
                fieldList: params['fieldList'],
                success : function(file, response){
                    completeProc({
                        dropzoneObject : etcDropzone,
                        file : file,
                        response : response,
                        refreshField : refreshField,
                        key : indexKey,
                        fileKey : fileKey,
                    });
                }
            };
            etcDropzone.setNewDropzone( fileKey, dropzoneSetParams);
        }
    }

    //재정의
    vueUpdatedCallBackFnc = function(el){

        for(let key in el.items.docData.sampleData){

            //기타 처리 시작---------------------------------------------------------------------------------------------
            //console.log( '====> KEY : ' + key );
            //console.log( etcDropzone.fileList[key] );
            if(  typeof etcDropzone.fileList[key] == 'undefined' ){
                //Size Spec File 처리
                if( typeof etcDropzone.fileList['size_' + key] == 'undefined' ){
                    let fileKey = 'size_' + key;
                    let params = {
                        dropzoneId: "div#size-dropzone" + key,
                        fieldList: ['sampleData',key,'fileSizeSpec',],
                    }
                    setEtcDropzone(fileKey, params, 'fileSizeSpec', key);
                }

                //접는방법 File 처리
                for(let foldIdx=1; 3 >= foldIdx; foldIdx++){
                    if( typeof etcDropzone.fileList['fold' + foldIdx + '_' + key] == 'undefined' ){
                        let fileKey = 'fold' + foldIdx + '_' + key;
                        let foldKey = 'fileFold'  + foldIdx ;

                        let params = {
                            dropzoneId: "div#fold"+foldIdx+"-dropzone" + key,
                            fieldList: ['sampleData',key,foldKey],
                        }
                        setEtcDropzone(fileKey, params, foldKey, key);
                    }
                }

                //MarkLabel File 처리
                if( typeof etcDropzone.fileList['label_' + key] == 'undefined' ){
                    let fileKey = 'label_' + key;
                    let params = {
                        dropzoneId: "div#label-dropzone" + key,
                        fieldList: ['sampleData',key,'fileMarkLabel',],
                    }
                    setEtcDropzone(fileKey, params, 'fileMarkLabel', key);
                }

                //마크 유의 사항
                el.items.docData.sampleData[key].markList.forEach((markData, markIndex)=>{
                    let fileKey = markIndex + '_' + key;
                    let params = {
                        dropzoneId: "div#mark-dropzone" + markIndex + '_' + key ,
                        fieldList: ['sampleData',key,'markList',markIndex,'fileMark',],
                    }
                    setEtcDropzone(fileKey, params, Number(markIndex), key);
                });
            }



            //샘플 처리 설정---------------------------------------------------------------------------------------------
            let setPreviewDropzone = function(element,dropzone, fieldName, key, dropzoneId){
                let dropzoneSetParams = {
                    workUrl : 'work_ps.php?mode=uploadDocumentFileDropzone',
                    dropzoneId : dropzoneId,
                    fieldName1 : 'sampleData',
                    fieldName2 : fieldName,
                    success : function(file, response){
                        completeProc({
                            dropzoneObject : dropzone,
                            file : file,
                            response : response,
                            key : key,
                            fileKey : key,
                        });
                    }
                };

                dropzone.setDropzone( element[fieldName] ,key, dropzoneSetParams);
                dropzone.fileList[key].on("addedfile", function(file) {
                    let imageUrl = URL.createObjectURL(file);
                    if( $.isEmpty(element[fieldName+'Preview']) ){
                        element[fieldName+'Preview'] = [];
                    }
                    element[fieldName+'Preview'].push({
                        uuid : file.upload.uuid,
                        imageUrl : imageUrl,
                    });
                    workApp.$forceUpdate();
                });
                dropzone.fileList[key].on("removedfile", function(file) {
                    for(let idx in element[fieldName+'Preview']){
                        if( file.upload.uuid === element[fieldName+'Preview'].uuid){
                            element[fieldName+'Preview'].splice(idx,1);
                        }
                    }
                });
            }

            //샘플 처리 설정---------------------------------------------------------------------------------------------
            if(  typeof sampleDropzone.fileList[key] == 'undefined' ){
                setPreviewDropzone(el.items.docData.sampleData[key], sampleDropzone, 'fileSample', key, "div#sample-dropzone" + key);
            }

            //샘플 처리 설정---------------------------------------------------------------------------------------------
            if(  typeof thumbnailDropzone.fileList[key] == 'undefined' ){
                setPreviewDropzone(el.items.docData.sampleData[key], thumbnailDropzone, 'fileThumbnail', key, "div#thumbnail-dropzone" + key);
            }

        }
    }

</script>
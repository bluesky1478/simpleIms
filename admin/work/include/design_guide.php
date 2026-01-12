<script type="text/javascript">

    let popupItems = '';

    function getPopupItems(){
        return popupItems;
    }

    function saveProduct(productItem) {
        //workApp
        console.log(productItem);
        console.log(' parent save product');
        workApp.items.docData.products.push(productItem);
    }

    let uploadFileList = null;
    let externSelectDocument = function(itemDocData, docData, parentApp){
        let srcDocData = itemDocData.docData;
        docData.sendEmail = srcDocData.companyEmail;
        docData.sendDt = '';

        //일정
        docData.step1 = srcDocData.stepData[1];
        docData.step2 = srcDocData.stepData[2];
        docData.step3 = srcDocData.stepData[3];
        docData.step4 = srcDocData.stepData[4];

        $.postWork({mode : 'getDefaultProductData'}).then((jsonData)=>{
            docData.products.splice(0, docData.products.length);
            let initProductData = jsonData.data;

            if( Array.isArray(itemDocData.docData.recentWorkData) ){
                itemDocData.docData.recentWorkData.forEach((workData)=>{
                    let workProduct = $.copyObject(initProductData);
                    workProduct.styleName = workData.styleName;
                    workProduct.style = workData.styleType;
                    workProduct.serial = workData.serial;
                    workProduct.fit = workData.fit;
                    workProduct.size = workData.sizeDisplay;
                    workProduct.fileImage = workData.fileSample;
                    workProduct.option = [];

                    //Option 처리.
                    workData.optionList.forEach((optionData, index)=>{
                        let targetOption = {
                            optionName : optionData.optionName,
                        }

                        //check List
                        workData.checkList.forEach((checkData)=>{
                            if( 'undefined' == typeof(targetOption.checkList) ){
                                targetOption.checkList = [];
                            }
                            let targetCheckData = $.copyObject(checkData);
                            targetCheckData.completeSpec = checkData.checkSpec[index];
                            targetOption.checkList.push(targetCheckData);
                        });

                        //option Type
                        workData.typeList.forEach((typeData)=>{
                            let targetTypeData = {
                                optionTypeName : typeData.typeName,
                                orderCnt : typeData.optionCount[index],
                                inputCnt : '',
                            }
                            if( 'undefined' == typeof(targetOption.optionType) ){
                                targetOption.optionType = [];
                            }
                            targetOption.optionType.push(targetTypeData);
                        });
                        workProduct.option.push( targetOption );

                    });
                    //workProduct.option
                    docData.products.push(workProduct);
                });
            }
            //docData.products
            console.log( '### 원본' );
            console.log( itemDocData );
            console.log( '### 복사본' );
            console.log( docData );
        });
    }
    let externMountedFnc = function(){
    }
    let externBeforeSaveProc = function(items){
    }
    let externAfterSaveProc = function(items, resultData){
    }
    let externVueMethod = {
        /**
         * 메일 발송
         * @param docData
         */
        sendMail : function(docData){
            let param ={
                mode : 'sendEmail',
                sendEmail : docData.sendEmail,
                sno : documentSno,
            };
            $.post('work_ps.php',param, function(json){
                docData.sendDt = json.data;
                $.msg('메일을 발송하였습니다.', '', 'success');
            });
        },
        openPopProduct : function(items){
            popupItems = items;
            window.open('popup_product.php', 'popup_product', 'width=1400, height=910, resizable=yes, scrollbars=yes');
        },
    };
</script>

<div class="col-xs-6" >
    <div class="table-title ">
        <div class="flo-left" >발주 기본정보</div>
        <div class="flo-right " >
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"  />
                <col class="width-xl"  />
            </colgroup>
            <tr>
                <th>피드백 요청일 / 상태</th>
                <td>
                    <div class=" input-group" style="width:120px; display:inline-block; float:left; margin-right:15px" >
                        <datepicker @update-date="updateDate" v-model="items.docData.feedbackDt" :data-item="'[\'feedbackDt\']'" class="form-control" ></datepicker>
                    </div>
                    <div v-show="items.version > 0">
                        <label class="radio-inline" >
                            <input type="radio" :name="'feedbackStatus'" v-model="items.docData.feedbackStatus" value="0" />미확정
                        </label>
                        <label class="radio-inline" >
                            <input type="radio" :name="'feedbackStatus'" v-model="items.docData.feedbackStatus" value="1" />확정
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    유니폼 디자인가이드 발송
                </th>
                <td>
                    <div class="form-inline">
                        수신 Email : <input name="text" v-model="items.docData.sendEmail" class="form-control" style="width:200px">
                        <div type="button" class="btn btn-sm btn-white" @click="sendMail(items.docData)"  v-show="items.version > 0">+ 발송</div>
                        <span style="margin-left:10px; color:red" v-show="!$.isEmpty(items.docData.sendDt)"><b>{% items.docData.sendDt %} 에 발송하였습니다.</b></span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    매뉴얼 파일 업로드
                </th>
                <td >
                    file...
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
                <col class="width-xl"  />
            </colgroup>
            <tr>
                <th>디자인 확정</th>
                <td><datepicker @update-date="updateDate" v-model="items.docData.step1" :data-item="'[\'step1\']'" class="form-control" ></datepicker></td>
            </tr>
            <tr>
                <th>샘플확정</th>
                <td><datepicker @update-date="updateDate" v-model="items.docData.step2" :data-item="'[\'step2\']'" class="form-control" ></datepicker></td>
            </tr>
            <tr>
                <th>고객발주</th>
                <td><datepicker @update-date="updateDate" v-model="items.docData.step3" :data-item="'[\'step3\']'" class="form-control" ></datepicker></td>
            </tr>
            <tr>
                <th>납품예정</th>
                <td><datepicker @update-date="updateDate" v-model="items.docData.step4" :data-item="'[\'step4\']'" class="form-control" ></datepicker></td>
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
                <col class="width-xl"  />
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
                <col class="width-xl"  />
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
        <div class="flo-left" >제품 정보</div>
        <div class="flo-right " >
            <div class="btn btn-sm btn-red" @click="openPopProduct('')">+추가</div>
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col  />
                <col  />
                <col  />
                <col  />
                <col  />
                <col  style="width:100px" />
                <col  style="width:150px" />
            </colgroup>
            <tr>
                <th class="text-center">번호</th>
                <th class="text-center">스타일</th>
                <th class="text-center">S/#</th>
                <th class="text-center">핏</th>
                <th class="text-center">원단정보</th>
                <th class="text-center">확인/수정</th>
                <th class="text-center">추가/삭제</th>
            </tr>
            <tr v-for="(item, index) in items.docData.products" :key="index">
                <td class="text-center">{% index+1 %}</td>
                <td class="text-center">{% item.styleName %}</td>
                <td class="text-center">{% item.serial %}</td>
                <td class="text-center">{% item.fit %}</td>
                <td class="text-center">{% item.fabricInfo %}</td>
                <td class="text-center">
                    <div class="btn btn-sm btn-white" @click="openPopProduct(item)">확인/수정</div>
                </td>
                <td class="text-center">
                    <div class="btn btn-sm btn-white">+추가</div>
                    <div class="btn btn-sm btn-white">-삭제</div>
                </td>
            </tr>
            <tr v-if=" 0 >= items.docData.products.length">
                <td colspan="7" class="text-center">등록된 상품이 없습니다.</td>
            </tr>
        </table>
    </div>
</div>


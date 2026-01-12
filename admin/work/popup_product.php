<?php include 'document_header.php'; ?>

<form name="frmOrder" method="post" class="frm-order" id="popup-app" @submit.stop.prevent="onSubmit(items)" >
    <div class="page-header js-affix">
        <h3>제품정보</h3>
        <div class="btn-group">
            <input type="button" value="등록하기" id="btn-reg" class="btn btn-red js-register" @click="saveProduct(items)"/>
        </div>
    </div>

    <div class="table-title">
        <span class="gd-help-manual mgt30">기본정보</span>
    </div>
    <div class="table-dashboard">
        <table id="data-table" style="display: none" class="table table-cols dn">
                <colgroup>
                    <col style="with:150px" />
                    <col   />
                    <col style="with:150px" />
                    <col   />
                </colgroup>
                <tr>
                    <th>스타일</th>
                    <td>
                        <select class="form-control" v-model="items.style"  id="select-style">
                            <option value="" selected>선택</option>
                            <?php foreach( \Component\Work\WorkCodeMap::STYLE_TYPE as $styleKey => $styleValue) { ?>
                                <option value="<?=$styleKey?>"><?=$styleValue?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <th>S/#</th>
                    <td><input type="text" class="form-control"  v-model="items.serial"></td>
                </tr>
                <tr>
                    <th>사이즈</th>
                    <td><input type="text" class="form-control"  v-model="items.size"></td>
                    <th>핏</th>
                    <td><input type="text" class="form-control"  v-model="items.fit"></td>
                </tr>
                <tr>
                    <th>원단정보</th>
                    <td><input type="text" class="form-control"  v-model="items.fabricInfo"></td>
                    <th>도안</th>
                    <td>
                        File Upload...
                    </td>
                </tr>
                <tr>
                    <th>
                        옵션
                        <div class="btn btn-sm btn-white" @click="addOption(items)">+ 옵션추가</div>
                    </th>
                    <td colspan="3">

                        <table class="table table-cols">
                            <colgroup>
                                <col style="width:165px"  />
                                <col style="width:165px" />
                                <col style="width:100px" />
                                <col style="width:100px" />
                                <col  />
                            </colgroup>
                            <tr>
                                <th>옵션명</th>
                                <th>구분</th>
                                <th>발주수량</th>
                                <th>입고수량</th>
                                <th>사이즈스펙</th>
                            </tr>

                            <tbody v-for="(item, index) in items.option">
                                <tr v-for="(typeItem, typeIndex) in item.optionType">
                                    <td :rowspan="item.optionType.length" v-if="0 === typeIndex">
                                        <input type="text" class="form-control" v-model="item.optionName">
                                        <div class="btn btn-sm btn-white mgt5" style="float:right"  @click="removeListData(items.option, index)">-삭제</div>
                                        <div class="btn btn-sm btn-white mgt5" style="float:right;margin-right:5px;"  @click="addOption(items)">+추가</div>
                                        <div style="clear:both"></div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="typeItem.optionTypeName">
                                        <div class="btn btn-sm btn-white mgt5" style="float:right"  @click="removeListData(item.optionType, typeIndex)">-삭제</div>
                                        <div class="btn btn-sm btn-white mgt5" style="float:right;margin-right:5px;"  @click="addListData2(item.optionType)" >+추가</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="typeItem.orderCnt">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="typeItem.inputCnt">
                                    </td>
                                    <td :rowspan="item.optionType.length" v-if="0 === typeIndex">
                                        <div class="table-dashboard" style="border-top:solid 1px #ddd">
                                            <table class="w100">
                                                <colgroup>
                                                    <col />
                                                    <col />
                                                    <col style="width:130px;" />
                                                    <col style="width:130px;" />
                                                </colgroup>
                                                <tr>
                                                    <th>구분</th>
                                                    <th>
                                                        측정값
                                                        <small class="text-muted btn btn-white btn-sm" @click="deleteSpec(item.checkList, 'completeSpec')">전체삭제</small>
                                                    </th>
                                                    <th>단위</th>
                                                    <!--<th>스펙 추가/삭제</th>-->
                                                </tr>
                                                <tr v-for="(specItem, specIndex) in item.checkList">
                                                    <td>
                                                        <input type="text" class="form-control" v-model="specItem.specItemName">
                                                    </td>
                                                    <td>
                                                        <div class="form-inline">
                                                            <input type="text" class="form-control text-center w50" v-model="specItem.completeSpec" :ref="'completeSpec' + specIndex"   @keyup.38="nextSpec('completeSpec' + (specIndex-1) )" @keyup.40="nextSpec('completeSpec' + (specIndex+1) )"   @keyup.enter="nextSpec('completeSpec' + (specIndex+1) )"  style="width:50px!important;">
                                                            <div class="btn btn-sm btn-white" style="color:#d1d1d1" @click="delSpec(specItem, 'completeSpec')">X</div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="radio-inline">
                                                            <input type="radio" value="cm"  :name="'specUnit' + index + '_' + specIndex" v-model="specItem.specUnit" />cm
                                                            <input type="radio" value="inch" :name="'specUnit' + index + '_' + specIndex" v-model="specItem.specUnit" />inch
                                                        </label>
                                                    </td>
                                                    <!--<td>
                                                        <div class="btn btn-white btn-sm" @click="addListData2(item.checkList)">+추가</div>
                                                        <div class="btn btn-white btn-sm" @click="removeListData(item.checkList, specIndex)">-삭제</div>
                                                    </td>-->
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-if=" typeof items.option == 'undefined'  ||  0 >= items.option.length" >
                                <tr >
                                    <td colspan="5">등록된 옵션이 없습니다.</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        <table id="loading-table" class="table table-cols">
            <trd><td>데이터 로딩 중 ....</td></trd>
        </table>
    </div>
</form>

<script type="text/javascript">
    function setPopupApp(jsonData){

        let initData = jsonData;
        console.log(jsonData);
        INIT_DATA = initData;

        workApp = new Vue({
            el: '#popup-app',
            delimiters: ['{%', '%}'],
            data : {
                items : initData,
            },
            methods : {
                addOption : function(items){
                    let parent = this;

                    if( $.isEmpty(items.style) ){
                        $.msg('스타일을 선택해주세요', "", "error");
                    }else{
                        if( items.option.length > 0 ){
                            let addOptionItem = $.copyObject(items.option[0]);
                            addOptionItem.optionName = '';
                            addOptionItem.optionType = [
                                {
                                    optionTypeName : '',
                                    orderCnt : '',
                                    inputCnt : '',
                                }
                            ];
                            //addOptionItem.orderCnt = '';
                            //addOptionItem.inputCnt = '';
                            items.option.push(addOptionItem);
                        }else{
                            let params = {
                                mode : 'getDefaultProductOption',
                                style : items.style,
                            };
                            let optionPromise = $.postWork(params);
                            optionPromise.then((optionResult)=>{
                                console.log('옵션을 추가해야지....');
                                console.log(optionResult.data);
                                items.option.push(optionResult.data);
                            });
                        }
                    }
                },
                delSpec : function(specItem, fieldName){
                    specItem[fieldName] = '';
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
                addListData2 : (data) => {
                    let addData = {};
                    for(let key in data[0]){
                        addData[key] = '';
                    }
                    data.push(addData);
                },
                removeListData : (data, index) => {
                    data.splice(index,1);
                },
                saveProduct : function(items){
                    console.log('제품 정보를 저장합니다.');
                    console.log(items);
                    if( $.isEmpty(items.style) ){
                        $.msg('스타일을 선택해주세요', "", "error");
                    }else{
                        items.styleName = $('#select-style option:selected').text();
                        window.opener.saveProduct(items);
                        self.close();
                    }
                }
            },
            mounted : function(){
                console.log('mounted');
                this.$nextTick(function () {
                    $('#data-table').show();
                    $('#loading-table').hide();
                });
            },
        });

    }

    function init(){
        let parentItem = $.copyObject(window.opener.getPopupItems());
        if( $.isEmpty(parentItem) ){
            let params = {
                mode : 'getDefaultProductData',
            };
            let initDataPromise = $.postWork(params);
            initDataPromise.then((jsonData)=>{
                setPopupApp(jsonData.data);
            });
        }else{
            $('#btn-reg').val('수정하기');
            setPopupApp(parentItem);
        }
    }

    $(function(){
        init();
    });

</script>

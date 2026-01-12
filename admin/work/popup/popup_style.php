<?php include './admin/work/document_header.php'; ?>

<form name="frmOrder" method="post" class="frm-order" id="popup-app" @submit.stop.prevent="onSubmit(items)" >
    <div class="page-header js-affix">
        <h3 id="style-title">스타일 등록</h3>
        <div class="btn-group">
            <input type="button" value="등록하기" id="btn-reg" class="btn btn-red js-register" @click="save(items)"/>
        </div>
    </div>

    <!--<div class="table-title">
        <span class="gd-help-manual mgt30">기본정보</span>
    </div>-->

    <div class="table-dashboard">
        <table id="data-table" class="table table-cols">
            <colgroup>
                <col class="width-md" />
                <col  />
            </colgroup>
            <tr>
                <th>시즌</th>
                <td class="text-left">
                    <?php foreach($SEASON_TYPE as $seasonKey => $season) { ?>
                    <label class="radio-inline">
                        <input type="radio" value="<?=$seasonKey?>"  name="season" v-model="items.season"/><?=$season?>
                    </label>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th>스타일명</th>
                <td>
                    <input type="text" class="form-control width-md" v-model="items.styleName">
                </td>
            </tr>
            <tr>
                <th>
                    스펙 체크 정보
                    <div class="btn btn-sm btn-white" @click="addSpec()">+ 구분추가</div>
                </th>
                <td colspan="3">
                    <table class="table table-cols">
                        <colgroup>
                            <col  />
                            <col  />
                            <col  class="width-sm" />
                            <col  class="width-sm" />
                            <col  class="width-sm" />
                            <col  style="width:70px" />
                        </colgroup>
                        <tr>
                            <th rowspan="2">구분</th>
                            <th rowspan="2">측정부위</th>
                            <th colspan="3">기준 스펙</th>
                            <th rowspan="2">삭제</th>
                        </tr>
                        <tr>
                            <th>슬림</th>
                            <th>기본</th>
                            <th>루즈</th>
                        </tr>
                        <tr v-for="(item, index) in items.specCheckInfo">
                            <td class="text-center">
                                <input type="text" class="form-control" v-model="item.specItemName" placeholder="총기장, 어깨넓이 등 ...">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control" v-model="item.specDescription" placeholder="▶뒷목점과 밑단 끝선의 직선길이">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control text-right" v-model="item.specGuide[0]" placeholder="-">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control text-right" v-model="item.specGuide[1]" placeholder="-">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control text-right" v-model="item.specGuide[2]" placeholder="-">
                            </td>
                            <td class="text-center">
                                <div class="btn btn-white btn-sm" @click="removeListData(items.specCheckInfo, index)">-삭제</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th>
                    체크리스트<br><small class="text-muted">(피팅체크리스트)</small>
                    <div class="btn btn-sm btn-white" @click="addCheck()">+ 체크사항추가</div>
                </th>
                <td colspan="3">
                    <table class="table table-cols">
                        <colgroup>
                            <col  />
                            <col  />
                            <col  style="width:70px" />
                        </colgroup>
                        <tr>
                            <th>체크사항</th>
                            <th>비고</th>
                            <th>삭제</th>
                        </tr>
                        <tr v-for="(item, index) in items.checkList">
                            <td class="text-center">
                                <input type="text" class="form-control" v-model="item.checkItem" placeholder="예) 원단소재 ">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control" v-model="item.checkEtc" placeholder="예) 터치감 / 스판 확인">
                            </td>
                            <td>
                                <div class="btn btn-white btn-sm" @click="removeListData(items.checkList, index)">-삭제</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</form>

<script type="text/javascript">

    let defaultSpecCheckInfo ={
            specItemName : '',
            specDescription : '',
            specGuide : ['','',''],
    };
    let defaultCheckList ={
            checkItem : '',
            checkEtc : '',
    };

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

                },
                delSpec : function(specItem, fieldName){
                },
                nextSpec : function(refItem){

                },
                deleteSpec : function(sampleItem, field){

                },
                addSpec : function() {
                    this.items.specCheckInfo.push($.copyObject(defaultSpecCheckInfo));
                },
                addCheck : function() {
                    console.log('addCheck...');
                    this.items.checkList.push($.copyObject(defaultCheckList));
                },
                removeListData : (data, index) => {
                    data.splice(index,1);
                    //this.$forceUpdate();
                },
                save : function(items){
                    $.postWork({mode : 'saveStyle', data : items}).then((jsonData)=>{
                        $.msg(jsonData.message, "", "success").then(()=>{
                            parent.opener.location.reload();
                            self.close();
                        });
                    });
                    console.log(items);
                }
            },
            mounted : function(){

            },
        });

    }

    function init(){
        <?php if( empty($requestParam['sno'])) { ?>
        let initData = {
            season : 0,
            styleName : '',
            specCheckInfo : [$.copyObject(defaultSpecCheckInfo)],
            checkList : [$.copyObject(defaultCheckList)],
        };
        setPopupApp(initData);
        <?php }else{ ?>
        $('#btn-reg').val('수정하기');
        $('#style-title').text('스타일 수정');
        WorkService.getStyleData({sno:'<?=$requestParam['sno']?>'}).then((jsonData)=>{
            if( $.isEmpty(jsonData.data['checkList']) ){
                jsonData.data.checkList = [];
            }
            setPopupApp(jsonData.data);
        });
        <?php } ?>
    }

    $(function(){
        init();
    });

</script>

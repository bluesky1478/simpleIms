
<style>
    .claim-option-box{ clear:both; padding-top:5px }
    .claim-option-box .claim-option-size{ padding-left:15px; }
    .claim-option-box .claim-size-name{ min-width:80px; text-align: right; display: inline-block }
    .claim-option-box .claim-option-size ul li{float:left; padding:5px 0px 5px 10px }
    .claim-option-box .claim-option-name{ margin : 10px 0px 5px 0px }
    .claim-selected-item { font-weight: bold; color: #d51f1f }
    .refund-table-box td { height:15px!important; text-align: left; border-bottom: none!important; }
</style>

<form id="frmOrder" class="frm-order" >
    <div id="return-app">

        <div class="page-header js-affix">
            <h3>창고 반품등록 요청</h3>
        </div>

        <div class="table-title gd-help-manual">
            반품정보 입력
        </div>

        <table class="table table-cols" >
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
            <tr>
                <th class="ta-r">업체명</th>
                <td class="ta-l">
                    <select class="form-control" v-model="scmNo" id="sel-company">
                        <option value="">=== 선택 ===</option>
                        <?php foreach( $scmList as $eachTypeKey => $eachType ) { ?>
                            <option value="<?=$eachTypeKey?>"> <?=$eachType?></option>
                        <?php } ?>
                    </select>
                    <span class="text-muted">반품제품 등록시 자동 입력됩니다.</span>
                </td>
            </tr>
            <tr>
                <th class="ta-r">고객명</th>
                <td class="ta-c">
                    <input type="text" class="form-control" style="width:50%;" v-model="customerName" placeholder="고객명"></input>
                </td>
            </tr>
            <tr>
                <th class="ta-r">주소</th>
                <td class="ta-c">
                    <input type="text" class="form-control" style="width:100%;" v-model="address" placeholder="주소"></input>
                </td>
            </tr>
            <tr>
                <th class="ta-r">원송장번호</th>
                <td class="ta-c">
                    <input type="text" class="form-control" style="width:100%;" v-model="invoiceNo" placeholder="원송장번호"></input>
                </td>
            </tr>
            <tr>
                <th class="ta-r">
                    반품제품
                    <div class="btn btn-white btn-sm" @click="addItem">+추가</div>
                </th>
                <td class="ta-l">
                        <div id="request-goods-info">
                            <div class="table1 type1" style="margin-top:10px" >
                                <table class="table table-cols">
                                    <colgroup>
                                        <col style="width:150px"/>
                                        <col/>
                                        <col style="width:80px"/>
                                        <col style="width:80px"/>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>품목코드</th>
                                        <th>품목명</th>
                                        <th>수량</th>
                                        <th>삭제</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, itemIndex) in items">
                                        <td style="padding-left:10px;position: relative">
                                            <div class="goods-info" style="text-align: left; display: inline-block">
                                                <div class="goods-name">
                                                    <input type="text" v-model="item.prdCode" class="form-control" @keyup="getPrdName(item)">
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: left;font-size:14px;" >
                                            {% item.prdName %}_{% item.optionName %} <small class="text-muted">({% item.stockCnt %}개)</small>
                                        </td>
                                        <td>
                                            <input type="text" v-model="item.prdCnt" class="form-control">
                                        </td>
                                        <td>
                                            <div class="btn btn-sm btn-white" @click="removeItem(itemIndex)">-삭제</div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </td>
            </tr>
            <tr>
                <th class="ta-r">메모(창고전달사항)</th>
                <td class="ta-c">
                    <textarea class="form-control" style="width:100%;" rows="3" v-model="innoverMemo" placeholder="메모(창고전달사항)"></textarea>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="ta-c" style="margin-bottom:50px">
            <?php if( empty($claimData['sno']) ) { ?>
                <input type="button" value="등록" class="btn btn-red" style="height:38px;font-size:20px; padding:0px 20px 0px 20px" @click="save">
            <?php }else{ ?>
                <input type="button" value="수정" class="btn btn-red" style="height:38px;font-size:20px; padding:0px 20px 0px 20px" @click="modify">
            <?php }?>
            <input type="button" value="닫기" class="btn btn-white" style="height:38px;font-size:20px; padding:0px 20px 0px 20px" @click="close">
        </div>
    </div>
</form>



<?php include 'warehouse_return_script.php' ?>
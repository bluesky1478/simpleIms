

<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
    <div>
        <div class="table-title ">
            검색
        </div>
        <!--검색 시작-->
        <div class="search-detail-box form-inline">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-3xl">
                    <col class="width-md">
                    <col class="width-3xl">
                </colgroup>
                <tbody>
                <tr>
                    <th rowspan="2">검색어</th>
                    <td rowspan="2">
                        <div v-for="(keyCondition,multiKeyIndex) in costSearchCondition.multiKey" class="mgb5">
                            검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                            <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchCost()" />
                            <div class="btn btn-sm btn-red" @click="costSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === costSearchCondition.multiKey.length ">+추가</div>
                            <div class="btn btn-sm btn-gray" @click="costSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="costSearchCondition.multiKey.length > 1 ">-제거</div>
                        </div>
                        <div class="notice-info">다중 검색시 AND 검색</div>
                    </td>
                    <th>연도/시즌</th>
                    <td >
                        연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="costSearchCondition.year" style="width:80px" />
                        시즌 :
                        <select class="form-control" name="projectSeason" v-model="costSearchCondition.season">
                            <option value="">선택</option>
                            <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <?php if( empty($imsProduceCompany) ){ ?>
                        <th>의뢰처</th>
                        <td>
                            <select2 class="js-example-basic-single" style="width:200px" v-model="costSearchCondition.reqFactory" >
                                <option value="0">미정</option>
                                <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                    <?php }else{ ?>
                        <td colspan="99"></td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>견적타입</th>
                    <td >
                        <label class="radio-inline">
                            <input type="radio" name="estimateType" value="" v-model="costSearchCondition.estimateType" @change="searchCost(1)" />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="estimateType" value="estimate" v-model="costSearchCondition.estimateType" @change="searchCost(1)" />가견적
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="estimateType" value="cost" v-model="costSearchCondition.estimateType" @change="searchCost(1)" />생산확정견적
                        </label>
                    </td>
                    <th>상태</th>
                    <td >
                        <label class="radio-inline">
                            <input type="radio" name="costStatus" value="0" v-model="costSearchCondition.status" @change="searchCost(1)" />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="costStatus" value="2" v-model="costSearchCondition.status" @change="searchCost(1)" />요청
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="costStatus" value="3" v-model="costSearchCondition.status" @change="searchCost(1)" />처리완료
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="costStatus" value="6" v-model="costSearchCondition.status" @change="searchCost(1)" />확정
                        </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="ta-c" style="border-bottom: none">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchCost(1)">
                         <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="costConditionReset()">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!--검색 끝-->
    </div>

    <div >
        <div class="">
            <div class="flo-left mgb5 mgt25">
                <span class="font-16 ">
                    총 <span class="bold text-danger">{% $.setNumberFormat(costTotal.recode.total) %}</span> 건
                </span>
                <?php if(!$imsProduceCompany) { ?>
                    <!--<div class="btn btn-gray" @click="setRevokeQb(1)">요청상태로변경(임시)</div>-->
                <?php }else{ ?>
                    <!--<div class="btn btn-blue" @click="openRequestView()">처리완료</div>
                    <span class="notice-info">처리 완료된 항목을 다시 처리완료해도 적용되지 않습니다.</span>-->
                <?php } ?>
            </div>
            <div class="flo-right mgb5">
                <div class="bold font-18 ta-r">생산견적 리스트</div>
                <div style="display: flex">
                    <select @change="searchCost()" class="form-control" v-model="costSearchCondition.sort">
                        <option value="D,desc">요청일 ▼</option>
                        <option value="D,asc">요청일 ▲</option>
                        <option value="A,desc">처리완료D/L ▼</option>
                        <option value="A,asc">처리완료D/L ▲</option>
                        <option value="B,desc">고객사별 ▼</option>
                        <option value="B,asc">고객사별 ▲</option>
                        
                        <option value="COST1,desc">견적금액 ▼</option>
                        <option value="COST1,asc">견적금액 ▲</option>
                    </select>

                    <select v-model="costSearchCondition.pageNum" @change="searchCost(1)" class="form-control mgl5">
                        <option value="20">20개 보기</option>
                        <option value="50">50개 보기</option>
                        <option value="100">100개 보기</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="">
            <!--ims_product_cost 와 함께사용-->
            <?php include 'ims_product_cost_list_template.php'?>
        </div>

        <div id="cost-page" v-html="costPage" class="ta-c"></div>

    </div>

</div>


<!--처리완료 팝업-->

<!-- [ 아소트 ] =========================================================  -->
<div v-show="'assort' === styleTabMode">
    <table class="table table-cols" style="border-top:none">
        <colgroup>
            <col class="w-3p"><!--번호-->
            <?php foreach($prdSetupDataAssort['list'] as $each) { ?>
                <col class="w-<?=$each[1]?>p" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th >번호</th>
            <?php foreach($prdSetupDataAssort['list'] as $titleKey => $each) { ?>
                <?php if( 5 === $titleKey ) { ?>
                    <th>
                        고객 발주 수량 <div class="btn btn-red btn-red-line2 mgl10" @click="saveAssort()" v-if="'p'!==project.assortApproval">아소트 저장</div>
                    </th>
                <?php }else{ ?>
                    <th><?=$each[0]?></th>
                <?php } ?>
            <?php } ?>
        </tr>
        </thead>
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-show="!showStyle">
        <tr>
            <td colspan="99" class="center">
                <div class="btn btn-white" @click="showStyle=true">상품 보기</div>
            </td>
        </tr>
        </tbody>
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-for="(product, prdIndex) in viewProductList" v-show="showStyle">
        <tr>
            <td rowspan="2"><!--번호-->
                {% prdIndex+1 %}
                <div class="text-muted font-11">#{% product.sno %}</div>
            </td>
            <td rowspan="2"><!--이미지-->
                <span class="hover-btn cursor-pointer"  v-if="$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)">
                    <img src="/data/commonimg/ico_noimg_75.gif" class="middle" width="40">
                </span>
                <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnail,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnail) && $.isEmpty(product.fileThumbnailReal)">
                    <img :src="product.fileThumbnail" class="middle" width="60" height="60" >
                </span>
                <span class="hover-btn cursor-pointer"  @click="window.open(product.fileThumbnailReal,'img_thumbnail','width=950,height=1200')" v-if="!$.isEmpty(product.fileThumbnailReal)">
                    <img :src="product.fileThumbnailReal" class="middle" width="60" height="60">
                </span>
            </td>
            <td rowspan="2">
                {% (product.prdYear+'').substring(2,4) %}
                {% product.prdSeason %}
            </td>
            <td class="pdl5 ta-l relative" ><!--스타일명-->
                <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.sno, -1)" >
                    {% product.productName %}
                </span>
                <br>
                <span class="text-muted">{% product.styleCode.toUpperCase() %}</span>
                <br>
                <div class="font-11 btn btn-black-line hover-btn cursor-pointer mgt5" @click="window.open(`<?=$eworkUrl?>?sno=${product.sno}`);">
                    작업지시서 <i class="fa fa-external-link" aria-hidden="true"></i>
                </div>
            </td>
            <td class=""><!--제작수량-->
                <span class="">{% $.setNumberFormat(product.prdExQty) %}장</span>
            </td>
            <td class="pd0">
                <div class="dp-flex" v-if="'p'===project.assortApproval">{% $.setNumberFormat(product.moq) %}장</div>
                <div class="dp-flex font-11" v-if="'p'!==project.assortApproval"><input type="text" class="form-control" v-model="product.moq">장</div>
            </td>
            <td class="text-center">
                <!--아소트-->
                <!--기초설정 반드시 필요 / 그 안에서 -->
                <table class="table table-cols w-100p mgb0 table-pd-5 text-right noto" style="table-layout: fixed">
                    <tr >
                        <th class="text-center">
                            구분
                        </th>
                        <th v-for="option in product.specOptionList" class="text-center">
                            {% option %}
                        </th>
                        <th class="text-center" v-if="'p' !== project.assortApproval">
                            삭제
                        </th>
                    </tr>

                    <tr v-for="(assort, assortIdx) in product.assort">
                        <th class="text-center">
                            <div v-if="'p'===project.assortApproval">
                                {% assort.type %}
                            </div>
                            <div v-if="'p'!==project.assortApproval">
                                <input type="text" class="form-control noto" v-model="assort.type" @keyup="assortTypeCopy(viewProductList,assort,assortIdx)" @blur="assortTypeCopy(viewProductList,assort,assortIdx)">
                            </div>
                        </th>

                        <td v-for="(optionKey) in product.specOptionList" class="text-center">
                            <!-- {% optionCnt %} -->
                            <div v-if="'p'===project.assortApproval">
                                {% assort.optionList[optionKey] %}
                            </div>
                            <div v-if="'p'!==project.assortApproval">
                                <input type="text" class="form-control noto number-only" v-model="assort.optionList[optionKey]" placeholder="수량">
                            </div>
                        </td>
                        <td class="text-center" v-if="'p' !== project.assortApproval">
                            <div class="btn btn-white btn-sm disabled" v-show="1 >= product.assort.length">삭제</div>
                            <div class="btn btn-white btn-sm" v-show="product.assort.length > 1" @click="deleteAssort(viewProductList, assort, assortIdx)">삭제</div>
                        </td>
                    </tr>
                </table>

                <div class="mgt5 text-right">

                    <div class="dp-flex" style="justify-content: space-between">

                        <div class="dp-flex" v-if="'p' !== project.assortApproval">
                            <span class="mgr10">구분항목 동시 수정 여부: </span>
                            <label class="mgr10">
                                <input type="radio" :name="'sync-assort-type'+prdIndex"  value="y" v-model="syncAssortType"/> 동시수정
                            </label>
                            <label >
                                <input type="radio" :name="'sync-assort-type'+prdIndex"  value="n" v-model="syncAssortType"/> 개별수정
                            </label>
                        </div>

                        <div class="dp-flex" v-if="'p' === project.assortApproval"></div>

                        <div class="mgl20 font-16">
                            MOQ 수량 : <span class="sl-blue">{% $.setNumberFormat(product.moq) %}</span>ea / TOTAL : <span class="text-danger">{% $.setNumberFormat(product.assortTotal) %}</span>ea
                            <div class="btn btn-white" @click="addAssort(viewProductList, prdIndex)" v-if="'p' !== project.assortApproval">+ 구분추가</div>
                        </div>

                    </div>
                </div>

            </td>
        </tr>
        </tbody>
    </table>

    <div class="dp-flex dp-flex-gap10">

        <table class="table table-cols w-50p">
            <colgroup>
                <col class="w-20p" />
                <col />
                <col class="w-10p" />
            </colgroup>
            <tr>
                <th>아소트 메모</th>
                <td>
                    <div v-show="!assortModify" v-html="$.nl2br(project.assortMemo)"></div>
                    <textarea class="form-control" rows="4" v-model="project.assortMemo" v-show="assortModify"></textarea>
                </td>
                <td>
                    <div class="btn btn-white" v-show="!assortModify" @click="assortModify=true">수정</div>
                    <div class="btn btn-red" v-show="assortModify" @click="saveProjectRealTime('assortMemo',project.assortMemo);assortModify=false;$.msg('메모가 수정되었습니다.','','success')">저장</div>
                    <div class="btn btn-white" v-show="assortModify" @click="assortModify=false">취소</div>
                </td>
            </tr>
        </table>

        <div class="font-15 text-center w-100p">
            발주 총 수량 : <span class="text-danger">{% $.setNumberFormat(assortTotal) %}</span>개
            <br>
            
            <div class="mgt10 btn btn-red btn-red-line2 btn-lg"
                 v-if="'p'!==project.assortApproval"
                 @click="saveAssort()">아소트 저장</div>


            <div class="mgt10 btn btn-lg btn-blue-line cursor-pointer hover-btn"
                 v-if="'f'===project.assortApproval"
                 @click="setAssortStatus('p')">
                아소트 확정
            </div>

            <div class="mgt10 btn btn-red btn-red-line2 btn-lg cursor-pointer hover-btn"
                 v-if="'p'===project.assortApproval"
                 @click="setAssortStatus('f')">아소트 확정취소</div>

            
        </div>
    </div>
</div>




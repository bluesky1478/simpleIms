<!-- [ 샘플 ] =========================================================  -->
<div class="" v-show="'sample' === styleTabMode">
    <table class="table table-cols" style="border-top:none">
        <colgroup>
            <!--<col class="w-3p">번호-->
            <?php foreach($prdSetupDataSample['list'] as $each) { ?>
                <col class="w-<?=$each[1]?>p" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <!--<th>번호</th>-->
            <?php foreach($prdSetupDataSample['list'] as $each) { ?>
                <th><?=$each[0]?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" v-show="!showStyle">
        <tr>
            <td colspan="99" class="center">
                <div class="btn btn-white" @click="showStyle=true">샘플 보기</div>
            </td>
        </tr>
        </tbody>
        <tbody :class="'text-center ' + ('style' !== viewMode ? 'bg-light-red':'')" >
        <tr v-for="(product, prdIndex) in sampleList" v-show="showStyle">
            <!--스타일명-->
            <td :rowspan="product.styleRowspan" v-if="product.styleRowspan > 0">
                <span class="hover-btn cursor-pointer" @click="openProductReg2(project.sno, product.styleSno, 0)" >
                    {% product.productName %}
                </span>
            </td>
            <td ><!--번호-->
                {% prdIndex+1 %}
                <div class="text-muted font-11">#{% product.sno %}</div>
            </td>

            <td class="pdl5 ta-l" ><!--스타일명-->
                <span v-if="'n' !== product.sampleConfirm" class="hover-btn cursor-pointer" @click="openProductWithSample(project.sno, product.styleSno, product.sno)" >
                    <i class="fa fa-check sl-green fa-lg" aria-hidden="true"></i>
                    <span class="sl-green">고객확정 <br><b class="">{% product.sampleName %}</b></span>
                </span>
                <span v-else class="hover-btn cursor-pointer" @click="openProductWithSample(project.sno, product.styleSno, product.sno)" >
                    <b>{% product.sampleName %}</b>
                </span>

                <div class="text-muted font-11">
                    등록:{% product.regDt %}
                </div>

            </td>
            <td>
                {% product.factoryName %}
            </td>
            <td>
                {% product.sampleCount %}
            </td>
            <td>
                {% $.setNumberFormat(product.sampleCost) %}원
            </td>
            <!--<td>
                <div class="btn btn-white btn-sm">견적요청</div>
                <div class="btn btn-white btn-sm">고객확정</div>
            </td>
                ['샘플지시서',5], //파일
                ['샘플리뷰서',5], //파일
                ['샘플투입일',5], //날짜
                ['샘플실마감일',5], //날짜
            -->
            <td class="text-left pdl5">
                <simple-file-only-history-upload :file="product.fileList.sampleFile1" :params="product" :file_div="'sampleFile1'" class="font-11"></simple-file-only-history-upload>
                <!--
                <span class="font-11">
                    승인여부:<span v-html="$.getAcceptName(product.sampleFile1Approval)">
                </span>
                -->
            </td>
            <td class="text-left pdl5">
                <simple-file-only-history-upload :file="product.fileList.sampleFile4" :params="product" :file_div="'sampleFile4'" class="font-11"></simple-file-only-history-upload>
                <!--<span class="font-11">
                    승인여부:<span v-html="$.getAcceptName(product.sampleFile1Approval)">
                </span>-->
            </td>
            <td>
                {% $.formatShortDate(product.sampleFactoryBeginDt) %}
            </td>
            <td>
                {% $.formatShortDate(product.sampleFactoryEndDt) %}
            </td>
            <td>
                {% product.sampleMemo %}
            </td>
        </tr>
        </tbody>
    </table>
</div>
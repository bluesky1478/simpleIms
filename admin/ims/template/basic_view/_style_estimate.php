<!-- [ 견적 ] =========================================================  -->
<div class="" v-show="'estimate' === styleTabMode">
    <table class="table table-cols table-default-center" style="border-top:none">

        <?php $tableTitles = [
            ['name'=>'번호','col'=>'5'],
            ['name'=>'발송정보','col'=>'10'],
            ['name'=>'견적타입','col'=>'6'],
            ['name'=>'발송제목','col'=>''],
            ['name'=>'비고(고객용)','col'=>''],
            ['name'=>'내부용 메모','col'=>''],
            ['name'=>'공급가액','col'=>'6'],
            ['name'=>'부가세','col'=>'6'],
            ['name'=>'합계','col'=>'6'],
            ['name'=>'수신','col'=>'10'],
        ]; ?>
        <?=\SiteLabUtil\SlCommonUtil::createHtmlTableTitle($tableTitles); ?>

        <tbody class="text-center" v-for="(custEstimate, custEstimateIndex) in customerEstimateList" v-show="showStyle">
        <tr>
            <td ><!--번호-->
                {% customerEstimateList.length-custEstimateIndex %}
                <div class="text-muted font-11">#{% custEstimate.sno %}</div>
            </td>
            <td ><!--발송정보-->
                <div>{% $.formatShortDate(custEstimate.regDt) %}</div>
                <div>{% custEstimate.regManagerNm %}</div>
            </td>
            <td ><!--견적타입-->
                {% getCodeMap()['custEstimateType'][custEstimate.estimateType] %}
            </td>
            <td class="pdl5 text-left"><!--발송제목-->
                <div class="hover-btn cursor-pointer sl-blue" @click="window.open(`<?=$customerEstimateUrl?>?key=${custEstimate.key}`);">{% custEstimate.subject %}</div>
                <div class="font-11 text-muted">담당자:{% custEstimate.regManagerNm %}</div>
            </td>
            <td class="pdl5 text-left"><!--비고(고객)-->
                <div>{% custEstimate.estimateMemo %}</div>
                <div class="sl-green font-11" v-if="'p'===custEstimate.approvalStatus">
                    {% getCodeMap()['custEstimateStatus'][custEstimate.approvalStatus] %}
                    ({% custEstimate.approvalName %} {% $.formatShortDate(custEstimate.approvalDt) %})
                </div>
                <div class="text-danger font-11" v-if="'f'===custEstimate.approvalStatus">
                    {% getCodeMap()['custEstimateStatus'][custEstimate.approvalStatus] %}
                    (사유:{% custEstimate.approvalName %} {% $.formatShortDate(custEstimate.approvalDt) %})
                </div>
            </td>
            <td class="pdl5 text-left"><!--비고(내부)-->
                <div>{% custEstimate.innoverMemo %}</div>
            </td>
            <td ><!--공급가-->
                <div>{% $.setNumberFormat(custEstimate.supply) %}</div>
            </td>
            <td ><!--부가세-->
                <div>{% $.setNumberFormat(custEstimate.tax) %}</div>
            </td>
            <td ><!--합계-->
                <div>{% $.setNumberFormat(Number(custEstimate.tax)+Number(custEstimate.supply)) %}</div>
            </td>
            <td ><!--수신-->
                <ul>
                    <li v-for="receiver in custEstimate.receiverInfo">
                        {% receiver.name %}
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
</div>
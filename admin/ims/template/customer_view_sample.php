<div class="col-xs-12 pd15">
    <div class="table-responsive ">
        <div class="table-title gd-help-manual">
            <div class="font-18">샘플 리스트</div>
        </div>
        <table class="table table-rows">
            <colgroup>
                <col style="width: 5%;" />
                <col style="width: 5%;" />
                <col style="width: 6%;" />
                <col style="width: 10%;" />
                <col style="width: 20%;" />
                <col style="width: 10%;" />
                <col style="width: 5%;" />
                <col style="width: 7%;" />
                <col style="width: 9%;" />
                <col style="width: 9%;" />
                <col style="width: 9%;" />
                <col style="width: 6%;" />
                <col style="width: 6%;" />
                <col />
            </colgroup>
            <thead>
            <tr>
                <th>번호</th>
                <th>제작차수</th>
                <th>구분</th>
                <th>스타일기획</th>
                <th>샘플명</th>
                <th>샘플실</th>
                <th>수량</th>
                <th>제작비용</th>
                <th>샘플지시서</th>
                <th>샘플리뷰서</th>
                <th>샘플확정서</th>
                <th>샘플투입일</th>
                <th>샘플실마감일</th>
                <th>비고</th>
            </tr>
            </thead>
            <tbody class="text-center ">
            <tr v-if="0 >= sampleList.length">
                <td colspan="99">
                    데이터가 없습니다.
                </td>
            </tr>
            <tr v-for="(val, key) in sampleList">
                <td>{% sampleTotal.idx - key %}</td>
                <td>{% val.sampleTerm %}</td>
                <td>{% val.sampleTypeHan %}</td>
                <td>{% val.planConcept %}</td>
                <td>
                    <span @click="if (val.sampleType == 9) popSampleDetail(val.sno); else openCommonPopup('product_sample_new', 1550, 900, {sno:val.sno});" class="sl-blue cursor-pointer hover-btn">{% val.sampleName %}</span>
                </td>
                <td>{% val.factoryName %}</td>
                <td>{% $.setNumberFormat(Number(val.sampleCount)) %}</td>
                <td>{% $.setNumberFormat(Number(val.sampleCost)) %}</td>
                <td>
                    <simple-file-only-history-upload :file="val['fileList']['sampleFile1']" :params="val" :file_div="'sampleFile1'" class="font-11"></simple-file-only-history-upload>
                </td>
                <td>
                    <simple-file-only-history-upload :file="val['fileList']['sampleFile4']" :params="val" :file_div="'sampleFile4'" class="font-11"></simple-file-only-history-upload>
                </td>
                <td>
                    <simple-file-only-history-upload :file="val['fileList']['sampleFile6']" :params="val" :file_div="'sampleFile6'" class="font-11"></simple-file-only-history-upload>
                </td>
                <td>{% val.sampleFactoryBeginDt===null||val.sampleFactoryBeginDt==''?'-':val.sampleFactoryBeginDt %}</td>
                <td>{% val.sampleFactoryEndDt===null||val.sampleFactoryEndDt==''?'-':val.sampleFactoryEndDt %}</td>
                <td>{% val.sampleMemo %}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="col-xs-12 new-style" v-show="'spec' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            사이즈 스펙
        </div>
        <div class="flo-right">
        </div>
    </div>

    <table class="table table-cols  xsmall-picker " >
        <colgroup>
            <col class="w-50p">
            <col class="w-50p">
        </colgroup>
        <thead>
        <tr>
            <th style="height:35px !important;padding:0 !important;">
                사이즈 스펙 도안
                <mini-file-history :file_div="'fileSpec'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <th style="height:35px !important;padding:0 !important;" class="text-danger">
                유의사항
            </th>
        </tr>
        </thead>
        <tbody>
        <tr >
            <td >
                <simple-file-list :files="mainData.ework.fileList.fileSpec"></simple-file-list>
            </td>
            <td style="padding:5px !important;">
                <div v-html="$.nl2br(mainData.ework.data.warnSpec)"></div>
            </td>
        </tr>
        <tr>
            <td colspan="99" class="text-center">
                <ul v-if="mainData.ework.fileList.fileSpec.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.fileSpec">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="" style="max-width: 800px" >
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>

        <thead >
        <tr>
            <th colspan="99" style="height:15px !important;padding:0 !important;">사이즈 스펙</th>
        </tr>
        <!--
        <tr v-show="!$.isEmpty(mainData.ework.data.specData)">
            <td >
                {% mainData.ework.data.specData %}
            </td>
        </tr>
        -->
        </thead>
        <tbody >
        <tr >
            <td colspan="99" class="pd0">

                <div class="btn btn-sm btn-green mgt10" @click="downloadSpec()">
                    다운로드
                </div>

                <div class="mgt10" style="justify-content: flex-start; display:flex ">

                    <table class="table-cols table-center mg5 table-pd-5 w-100p"  id="spec-table"  style="flex-grow: 0;">
                        <tr>
                            <th>구분</th>
                            <th>공개</th>
                            <th>단위</th>
                            <th>편차</th>
                            <th v-for="specValue in mainData.product.sizeList">
                                {% specValue %}
                            </th>
                        </tr>

                        <tr v-for="(specDetail, specDetailKey) in mainData.ework.data.specData" >

                            <!-- 구분 -->
                            <td>
                                <div >
                                    {% specDetail.title %}
                                    <div class="text-muted font-11">{% specDetail.memo %}</div>
                                </div>
                            </td>

                            <!--공개-->
                            <td>
                                {% 'y' === specDetail.share ? 'O':'X' %}
                            </td>

                            <!--단위-->
                            <td>
                                {% specDetail.unit %}
                            </td>

                            <!--편차-->
                            <td :colspan="!$.isNumeric(specDetail.deviation)?mainData.product.sizeList.indexOf(''+mainData.product.sizeSpec.standard)+1:1">
                                <span class="" v-if="0 == specDetail.deviation && !$.isEmpty(specDetail.memo)">
                                    {% specDetail.memo %}
                                </span>
                                <span class="" v-else>
                                    {% specDetail.deviation %}
                                </span>
                                <input type="text" class="form-control" v-model="specDetail.deviation" placeholder="편차" v-show="specModify" />
                            </td>

                            <!--스펙 데이터-->
                            <td v-for="(specValue, idx) in specDetail.specList" :class="mainData.product.sizeSpec.standard == idx ? 'bg-light-yellow':''"
                                v-if="$.isNumeric(specDetail.deviation) || mainData.product.sizeSpec.standard == idx">

                                <div v-if="$.isEmpty(specDetail.correctionList[idx])">
                                    <span v-if="$.isNumeric(specDetail.spec)">{% specValue %}</span>
                                    <span v-if="!$.isNumeric(specDetail.spec)">{% specDetail.spec %}</span>
                                </div>
                                <div v-else class="bg-light-red">{% specDetail.correctionList[idx] %}</div>
                            </td>

                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

</div>
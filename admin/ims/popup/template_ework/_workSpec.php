<div class="col-xs-12 new-style" v-show="'spec' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            사이즈 스펙
        </div>
        <div class="flo-right">
            <div class="btn btn-white mgb5" @click="openCommonPopup('ework_history', 1000, 750, {styleSno:mainData.product.sno, historyDiv:'spec'})">사이즈스펙 이력</div>
        </div>
    </div>

    <div class="sl-test1" v-if="false">
        상품의 사이즈 스펙
        {% mainData.product.sizeSpec %}
    </div>
    <!--mainData.product.sizeList-->
    <div class="sl-test2"  v-if="false">
        작지의 사이즈 스펙
        {% mainData.ework.data.specData %}
    </div>

    <table class="table table-cols xsmall-picker " >
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
                <form id="fileSpec" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileSpec')">- 파일삭제</div>
            </td>
            <td style="padding:5px !important;">
                <textarea class="form-control" rows="5" v-model="mainData.ework.data.warnSpec" @keyup="eworkUpdate('warnSpec',mainData.ework.data.warnSpec)" @blur="eworkUpdate('warnSpec',mainData.ework.data.warnSpec)"></textarea>
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
                <div class="mgt10" style="justify-content: flex-start; display:flex">
                    <!--측정항목{% mainData.product.sizeSpec %}-->
                    <table :class="'table-cols table-center mg5 table-pd-5 w-100p '+( specModify?'spec-modify':'spec-view')" id="spec-table"  style="flex-grow: 0;">
                        <colgroup>
                            <col class="w-40px" /><!--순서-->
                            <col class="w-10p" /><!--구분-->
                            <col style="width:100px" /><!--공개-->
                            <col class="w-5p" />
                            <col class="w-5p" />
                            <col v-for="specValue in mainData.product.sizeList"></col>
                        </colgroup>

                        <tr class="no-export">
                            <th colspan="99" style="text-align:left !important; font-size:15px !important; height: 30px !important;">
                                <!--
                                <div class="btn btn-red btn-red-line2 btn-sm mgl10" @click="saveProc('specData', mainData.ework.data.specData)" v-show="specModify">스펙저장</div>
                                -->
                                <div class="btn btn-white btn-sm mgl10" @click="specModify=true" v-show="!specModify">수정모드</div>
                                <div class="btn btn-white btn-sm " @click="specModify=false" v-show="specModify">보기모드</div>
                                <!--<div class="dp-flex mgt10" v-show="specModify">
                                    <div class="btn btn-sm btn-white" @click="addSpec(mainData.ework.data.specData, mainData.ework.data.specData[0], mainData.product.sizeSpec.standard)">스펙 항목 추가</div>
                                </div>-->
                                <div class="btn btn-sm btn-green" @click="downloadSpec()">
                                    다운로드
                                </div>
                            </th>
                        </tr>

                        <tr>
                            <th>순서</th>
                            <th>구분</th>
                            <th>공개</th>
                            <th>단위</th>
                            <th>편차</th>
                            <th v-for="specValue in mainData.product.sizeList">
                                {% specValue %}
                            </th>
                        </tr>

                        <tr v-for="(specDetail, specDetailKey) in mainData.ework.data.specData" >
                            <td>
                                {% Number(specDetailKey)+1 %}
                                <div>
                                    <i class="fa fa-plus cursor-pointer hover-btn" @click="addElementAfterAction(mainData.ework.data.specData, mainData.ework.data.specData[0], 'down', specDetailKey, (obj)=>{obj.unit='CM';obj.share='n';})"></i>
                                    <i class="fa fa-trash-o text-muted cursor-pointer hover-btn " aria-hidden="true" @click="deleteElement(mainData.ework.data.specData, specDetailKey)"></i>
                                </div>
                            </td>
                            <!-- 구분 -->
                            <td>
                                <div class="dp-flex">
                                    <div v-show="!specModify">
                                        <div>{% specDetail.title %}</div>
                                        <div class="text-muted ta-l font-11">{% specDetail.memo %}</div>
                                    </div>
                                    <div v-show="specModify">
                                        <input type="text" class="form-control" v-model="specDetail.title" placeholder="구분"  />
                                        <input type="text" class="form-control mgt5" v-model="specDetail.memo" placeholder="메모"   />
                                    </div>
                                </div>
                            </td>

                            <!--공개-->
                            <td >
                                <div v-show="!specModify">
                                    {% 'y' === specDetail.share ? 'O':'X' %}
                                </div>

                                <div v-show="specModify" class="ta-l pdl5">
                                    <label class="radio-inline no-export mgl0 ">
                                        <input type="radio" :name="'share_'+specDetailKey" value="y" v-model="specDetail.share">공개
                                    </label>
                                    <label class="radio-inline no-export mgl0 " >
                                        <input type="radio" :name="'share_'+specDetailKey" value="n" v-model="specDetail.share"/>비공개
                                    </label>
                                </div>
                            </td>

                            <!--단위-->
                            <td>
                                <div v-show="!specModify">
                                    {% specDetail.unit %}
                                </div>

                                <!--<div v-show="specModify">
                                    <input type="text" class="form-control" v-model="specDetail.unit" placeholder="단위"  />
                                    <br>
                                    <span class="cursor-pointer hover-btn text-blue font-11 underline no-export" @click="specDetail.unit='CM'">CM</span>
                                    <span class="cursor-pointer hover-btn text-blue font-11 underline no-export" @click="specDetail.unit='IN'">IN</span>
                                </div>-->

                                <div v-show="specModify" class="ta-l pdl5">
                                    <label class="radio-inline no-export mgl0 ">
                                        <input type="radio" :name="'unit_'+specDetailKey" value="CM" v-model="specDetail.unit">CM
                                    </label>
                                    <label class="radio-inline no-export mgl0 " >
                                        <input type="radio" :name="'unit_'+specDetailKey" value="IN" v-model="specDetail.unit"/>IN
                                    </label>
                                </div>

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

                                <div><!--보정값 수정-->
                                    <input type="text" class="form-control" v-model="specDetail.correctionList[idx]" placeholder="스펙 보정값" v-show="specModify && mainData.product.sizeSpec.standard != idx" />
                                </div>

                                <div><!--기준값 수정-->
                                    <input type="text" class="form-control" v-model="specDetail.spec" placeholder="기준값" v-show="specModify && mainData.product.sizeSpec.standard == idx" />
                                </div>
                            </td>

                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="99">

                <div class="text-center mgt10 mgb10 _dp-flex">
                    <!--<div class="btn btn-red btn-red-line2 btn-lg mgl10" @click="saveProc('specData', mainData.ework.data.specData)" v-show="specModify">스펙저장</div>-->
                    <div class="btn btn-white btn-lg mgl10" @click="specModify=true" v-show="!specModify">수정모드</div>
                    <div class="btn btn-white btn-lg " @click="specModify=false" v-show="specModify">보기모드</div>
                </div>

            </td>
        </tr>
        </tbody>
    </table>

</div>
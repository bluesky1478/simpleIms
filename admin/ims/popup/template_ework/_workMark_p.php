<div class="col-xs-12 new-style" v-show="'mark' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            마크 정보
        </div>
        <div class="flo-right">
            <div v-if="'y' === mainData.ework.data.useMark">
                마크 사용함
            </div>

            <div v-if="'n' === mainData.ework.data.useMark">
                마크 사용안함
            </div>
        </div>
    </div>

    <table class="table table-cols xsmall-picker">
        <colgroup>
            <col class="w-60p">
            <col class="w-40p">
        </colgroup>
        <thead>
        <tr>
            <th colspan="99" style="height:15px !important;padding:0 !important;" class="">마크 정보</th>
        </tr>
        </thead>

        <tbody v-for="n in 10" v-show="mainData.ework.markCnt >= n">
        <tr >
            <td class="pd0" style="border-bottom:none !important;">
                <table class="table table-pd-2 table-td-height30 border-top-none">
                    <tr>
                        <td colspan="99" class="pd20 border-top-none">
                            <div class="mgt10 mgb10">
                                <b>마크정보 {% n %}</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>위치</th>
                        <th>종류</th>
                        <th>색상</th>
                        <th>크기</th>
                    </tr>
                    <tr>
                        <td>
                            {% mainData.ework.data['markInfo'+n].position %}
                        </td>
                        <td>
                            {% mainData.ework.data['markInfo'+n].kind %}
                        </td>
                        <td>
                            {% mainData.ework.data['markInfo'+n].color %}
                        </td>
                        <td>
                            {% mainData.ework.data['markInfo'+n].size %}
                        </td>
                    </tr>
                </table>
            </td>
            <td colspan="" style="border-bottom:none !important;">

                <div>
                    <b>▶ 마크{% n %} 도안</b>
                    <mini-file-history :file_div="'fileMark'+n" :params="mainData.product"></mini-file-history>
                    <br>
                    <simple-file-list :files="mainData.ework.fileList['fileMark'+n]"></simple-file-list>
                </div>

                <div class="mgt5">
                    <b>▶ 위치{% n %} 도안</b>
                    <mini-file-history :file_div="'fileMarkPosition'+n" :params="mainData.product"></mini-file-history>
                    <br>
                    <simple-file-list :files="mainData.ework.fileList['fileMarkPosition'+n]"></simple-file-list>
                </div>

            </td>
        </tr>
        <tr>
            <td colspan="99" >
                <div class="inline-block w-45p">
                    <ul v-if="mainData.ework.fileList['fileMark'+n].length > 0" class="dp-flex">
                        <li class="" v-for="(file, fileIndex) in mainData.ework.fileList['fileMark'+n]">
                            <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                        </li>
                    </ul>
                </div>
                <div class="inline-block w-45p">
                    <ul v-if="mainData.ework.fileList['fileMarkPosition'+n].length > 0" class="dp-flex">
                        <li class="" v-for="(file, fileIndex) in mainData.ework.fileList['fileMarkPosition'+n]">
                            <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="99" style="height:15px !important;padding:0 !important;" class="text-danger">유의사항</th>
        </tr>
        <tr>
            <td colspan="99">
                <div v-html="$.nl2br(mainData.ework.data.warnMark)"></div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
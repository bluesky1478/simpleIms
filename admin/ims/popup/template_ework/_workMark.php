<div class="col-xs-12 new-style" v-show="'mark' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            마크 정보
        </div>
        <div class="flo-right">
            <label class="radio-inline ">
                <input type="radio" name="markYn" value="y" v-model="mainData.ework.data.useMark"  @change="eworkUpdate('useMark',mainData.ework.data.useMark)" />사용
            </label>
            <label class="radio-inline">
                <input type="radio" name="markYn" value="n" v-model="mainData.ework.data.useMark" @change="eworkUpdate('useMark',mainData.ework.data.useMark)"/>사용안함
            </label>
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
                                <div class="btn btn-sm btn-red" @click="mainData.ework.markCnt++;$('html, body').animate({ scrollTop: 2000 }, 'slow')" v-show="10 > mainData.ework.markCnt">+ 마크정보 추가</div>
                                <div class="btn btn-sm btn-gray" @click="removeMark(n)" v-show="n === mainData.ework.markCnt">- 마크정보 삭제</div>
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
                        <td><input type="text" class="form-control" placeholder="위치" v-model="mainData.ework.data['markInfo'+n].position" @keyup="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])" @blur="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])"></td>
                        <td><input type="text" class="form-control" placeholder="종류" v-model="mainData.ework.data['markInfo'+n].kind" @keyup="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])" @blur="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])"></td>
                        <td><input type="text" class="form-control" placeholder="색상" v-model="mainData.ework.data['markInfo'+n].color" @keyup="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])" @blur="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])"></td>
                        <td><input type="text" class="form-control" placeholder="크기" v-model="mainData.ework.data['markInfo'+n].size" @keyup="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])" @blur="eworkUpdate('markInfo'+n,mainData.ework.data['markInfo'+n])"></td>
                    </tr>
                </table>
            </td>
            <td colspan="" style="border-bottom:none !important;">

                <div>
                    <b>▶ 마크{% n %} 도안</b>
                    <mini-file-history :file_div="'fileMark'+n" :params="mainData.product"></mini-file-history>
                    <br>
                    <simple-file-list :files="mainData.ework.fileList['fileMark'+n]"></simple-file-list>
                    <form :id="'fileMark'+n" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                        <div class="fallback">
                            <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                        </div>
                    </form>
                    <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileMark'+n)">- 파일삭제</div>
                </div>

                <div class="mgt10">
                    <b>▶ 위치{% n %} 도안</b>
                    <mini-file-history :file_div="'fileMarkPosition'+n" :params="mainData.product"></mini-file-history>
                    <br>
                    <simple-file-list :files="mainData.ework.fileList['fileMarkPosition'+n]"></simple-file-list>
                    <form :id="'fileMarkPosition'+n" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                        <div class="fallback">
                            <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                        </div>
                    </form>
                    <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileMarkPosition'+n)">- 파일삭제</div>
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
                <textarea class="form-control" rows="5" v-model="mainData.ework.data.warnMark" @keyup="eworkUpdate('warnMark',mainData.ework.data.warnMark)" @blur="eworkUpdate('warnMark',mainData.ework.data.warnMark)"></textarea>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
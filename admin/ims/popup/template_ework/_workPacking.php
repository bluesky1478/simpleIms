<div class="col-xs-12 new-style" v-show="'packing' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            포장방법
        </div>
        <div class="flo-right">
            <label class="radio-inline ">
                <input type="radio" name="packingYn" value="y" v-model="mainData.ework.data.usePacking"  @change="eworkUpdate('usePacking',mainData.ework.data.usePacking)" />사용
            </label>
            <label class="radio-inline">
                <input type="radio" name="packingYn" value="n" v-model="mainData.ework.data.usePacking" @change="eworkUpdate('usePacking',mainData.ework.data.usePacking)"/>사용안함
            </label>
        </div>
    </div>

    <table class="table table-cols  xsmall-picker " >
        <colgroup>
            <col class="w-50p">
            <col class="w-50p">
        </colgroup>
        <tr>
            <th style="height:35px !important;padding:0 !important;">
                접는 방법
                <mini-file-history :file_div="'filePacking1'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <th style="height:35px !important;padding:0 !important;">
                포장 방법
                <mini-file-history :file_div="'filePacking2'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
        </tr>
        <tr>
            <td >
                <simple-file-list :files="mainData.ework.fileList.filePacking1"></simple-file-list>
                <form id="filePacking1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('filePacking1')">- 파일삭제</div>
                <ul v-if="mainData.ework.fileList.filePacking1.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.filePacking1">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                    </li>
                </ul>
            </td>
            <td >
                <simple-file-list :files="mainData.ework.fileList.filePacking2"></simple-file-list>
                <form id="filePacking2" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('filePacking2')">- 파일삭제</div>
                <ul v-if="mainData.ework.fileList.filePacking2.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.filePacking2">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <th style="height:35px !important;padding:0 !important;">
                박스 패킹
                <mini-file-history :file_div="'filePacking3'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <th style="height:15px !important;padding:0 !important;" class="text-danger">유의사항</th>
        </tr>
        <tr >
            <td >
                <simple-file-list :files="mainData.ework.fileList.filePacking3"></simple-file-list>
                <form id="filePacking3" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('filePacking3')">- 파일삭제</div>
            </td>
            <td style="padding:5px !important;">
                <textarea class="form-control" rows="5" v-model="mainData.ework.data.warnPacking" @keyup="eworkUpdate('warnPacking',mainData.ework.data.warnPacking)" @blur="eworkUpdate('warnPacking',mainData.ework.data.warnPacking)"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="99" class="text-center">
                <ul v-if="mainData.ework.fileList.filePacking3.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.filePacking3">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                    </li>
                </ul>
            </td>
        </tr>
    </table>
</div>
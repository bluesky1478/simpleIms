<div class="col-xs-12 new-style" v-show="'batek' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            바텍 정보
        </div>
        <div class="flo-right">
            <label class="radio-inline ">
                <input type="radio" name="batekYn" value="y" v-model="mainData.ework.data.useBatek"  @change="eworkUpdate('useBatek',mainData.ework.data.useBatek)" />사용
            </label>
            <label class="radio-inline">
                <input type="radio" name="batekYn" value="n" v-model="mainData.ework.data.useBatek" @change="eworkUpdate('useBatek',mainData.ework.data.useBatek)"/>사용안함
            </label>
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
                바텍 정보 도안
                <mini-file-history :file_div="'fileBatek'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <th style="height:15px !important;padding:0 !important;" class="text-danger">유의사항</th>
        </tr>
        </thead>
        <tbody>
        <tr >
            <td >
                <simple-file-list :files="mainData.ework.fileList.fileBatek"></simple-file-list>
                <form id="fileBatek" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileBatek')">- 파일삭제</div>
            </td>
            <td style="padding:5px !important;">
                <textarea class="form-control" rows="5" v-model="mainData.ework.data.warnBatek" @keyup="eworkUpdate('warnBatek',mainData.ework.data.warnBatek)" @blur="eworkUpdate('warnBatek',mainData.ework.data.warnBatek)"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="99" class="text-center">
                <ul v-if="mainData.ework.fileList.fileBatek.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.fileBatek">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
</div>
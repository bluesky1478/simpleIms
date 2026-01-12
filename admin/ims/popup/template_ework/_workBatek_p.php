<div class="col-xs-12 new-style" v-show="'batek' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            바텍 정보
        </div>
        <div class="flo-right">
            <div v-if="'y' === mainData.ework.data.useBatek">
                바텍정보 사용함
            </div>

            <div v-if="'n' === mainData.ework.data.useBatek">
                바텍정보 사용안함
            </div>
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
            </td>
            <td style="padding:5px !important;">
                <div v-html="$.nl2br(mainData.ework.data.warnBatek)"></div>
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
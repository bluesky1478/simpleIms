<div class="col-xs-12  new-style" v-show="'care' === tabMode">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            케어라벨 이미지
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
                케어라벨
                <mini-file-history :file_div="'fileCare'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <th style="height:35px !important;padding:0 !important;">
                케어라벨 위치
                <mini-file-history :file_div="'filePosition'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr >
            <td >
                <simple-file-list :files="mainData.ework.fileList.fileCare"></simple-file-list>
            </td>
            <td >
                <simple-file-list :files="mainData.ework.fileList.filePosition"></simple-file-list>
            </td>
        </tr>
        <tr>
            <td >
                <ul v-if="mainData.ework.fileList.fileCare.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.fileCare">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                    </li>
                </ul>
            </td>
            <td >
                <ul v-if="mainData.ework.fileList.filePosition.length > 0">
                    <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.filePosition">
                        <img :src="`<?=$nasUrl?>${file.filePath}`" class="w100" style="max-width: 800px">
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>
        <thead>
        <tr>
            <th style="height:15px !important;padding:0 !important;" class="text-danger text-center" colspan="99">
                유의사항
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="99" class="text-center">
                <div v-html="$.nl2br(mainData.ework.data.warnPosition)"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
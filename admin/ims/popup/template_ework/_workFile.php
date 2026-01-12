<!-- ############### 작지파일 ############### -->
<div class="col-xs-12 new-style" v-show="'oldFile' === tabMode">


    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            일러작업/구버전 파일
        </div>
        <div class="flo-right">
        </div>
    </div>

    <table class="table table-cols  xsmall-picker">
        <colgroup>
            <col class="width-sm">
            <col class="width-md">
            <col class="width-sm">
            <col class="width-md">
        </colgroup>
        <tbody>
        <tr >
            <th >
                메인 일러(Ai)파일
                <mini-file-history :file_div="'fileAi'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <td>
                <!--<file-upload2 :file="mainData.fileList.fileAi" :id="'fileAi'" :params="mainData.product" :accept="false"></file-upload2>-->
                <simple-file-list :files="mainData.ework.fileList.fileAi"></simple-file-list>
                <form id="fileAi" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileAi')">- 파일삭제</div>
            </td>
            <th >
                마크 일러(Ai)파일
                <mini-file-history :file_div="'fileMarkAi'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <td>
                <simple-file-list :files="mainData.ework.fileList.fileMarkAi"></simple-file-list>
                <form id="fileMarkAi" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileMarkAi')">- 파일삭제</div>
            </td>
        </tr>
        <tr>
            <th >
                케어라벨 AI 작업파일
                <mini-file-history :file_div="'fileCareAi'" :params="mainData.product" class="mgl5"></mini-file-history>
            </th>
            <td colspan="99">
                <simple-file-list :files="mainData.ework.fileList.fileCareAi"></simple-file-list>
                <form id="fileCareAi" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileCareAi')">- 파일삭제</div>
            </td>
        </tr>
        </tbody>
        <tbody>
        <tr >
            <th >
                구버전 작업지시서 파일<br>
                (전산작지 업데이트 전 파일)
            </th>
            <td colspan="99">

                <!--개별 단위 작업지시서 (24년 구버전)-->
                <simple-file-only-history-upload
                        :file="mainData.ework.fileList.fileWork"
                        :params="mainData.product"
                        :file_div="'fileWork'"
                        v-if="!$.isEmpty(mainData.ework.fileList.fileWork)"
                        class="font-11">
                </simple-file-only-history-upload>

                <!--프로젝트 단위 작업지시서 (23년 구버전)-->
                <simple-file-only-history-upload
                        :file="mainData.fileList.fileWork"
                        :params="mainData.project"
                        :file_div="'fileWork'"
                        v-if="$.isEmpty(mainData.ework.fileList.fileWork)"
                        class="font-11">
                </simple-file-only-history-upload>
            </td>
        </tr>
        </tbody>
    </table>


    <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
        <?php foreach(\Component\Ims\ImsCodeMap::EWORK_TYPE as $key => $value) { ?>
            <div class="mgt5">
                <?=$value?>
                <select class="form-control inline-block"
                        v-model="mainData.ework.data.<?=$key?>Approval" @change="eworkUpdate('<?=$key?>Approval',mainData.ework.data.<?=$key?>Approval)">
                    <option value="n">대기</option>
                    <option value="r">요청</option>
                    <option value="p">완료</option>
                    <option value="f">반려</option>
                </select>
            </div>
        <?php } ?>

    <?php } ?>

</div>
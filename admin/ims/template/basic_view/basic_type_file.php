<div class="row" >
    <div class="col-xs-12">
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title"></div>
            <div class="flo-right">
                <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">파일업로드</div>
                <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">닫기</div>
            </div>
        </div>

        <table class="table table-cols  xsmall-picker">
            <colgroup>
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
                <col class="width-md">
            </colgroup>
            <tbody >
            <tr >
                <td colspan="99">
                    <ul class="_dp-flex" v-show="!isModify">
                        <li class="dp-flex" v-if="fileList.fileEtc1.files.length > 0">
                            <i class="fa fa-file-o mgr3 dp-flex-gap3" aria-hidden="true"></i>
                            <label class="w-100px">미팅보고서:</label><simple-file-only-not-history-upload :file="fileList.fileEtc1" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </li>
                        <li class="dp-flex dp-flex-gap3 mgt3" v-if="fileList.filePlan.files.length > 0">
                            <i class="fa fa-file-o" aria-hidden="true"></i>
                            <label class="w-100px">기획서:</label><simple-file-only-not-history-upload :file="fileList.filePlan" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </li>
                        <li class="dp-flex dp-flex-gap3 mgt3" v-if="fileList.fileProposal.files.length > 0">
                            <i class="fa fa-file-o" aria-hidden="true"></i>
                            <label class="w-100px">제안서:</label><simple-file-only-not-history-upload :file="fileList.fileProposal" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </li>
                        <li class="dp-flex dp-flex-gap3 mgt3" v-if="fileList.filePacking.files.length > 0">
                            <i class="fa fa-file-o" aria-hidden="true"></i>
                            <label class="w-100px">분류패킹:</label><simple-file-only-not-history-upload :file="fileList.filePacking" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </li>
                        <li class="dp-flex dp-flex-gap3 mgt3" v-if="fileList.fileConfirm.files.length > 0">
                            <i class="fa fa-file-o" aria-hidden="true"></i>
                            <label class="w-100px">사양서:</label><simple-file-only-not-history-upload :file="fileList.fileConfirm" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </li>
                        <!-- TODO : 통합 필요
                        <li class="dp-flex dp-flex-gap3 mgt3" v-if="fileList.fileConfirm.files.length > 0">
                            <i class="fa fa-file-o" aria-hidden="true"></i>
                            <label class="w-100px">작업지시서:</label><simple-file-only-not-history-upload :file="fileList.fileWork" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                        </li>
                        -->
                    </ul>


                    <table v-show="isModify" class="w-100p">
                        <colgroup>
                            <col class="w-25p">
                            <col class="w-25p">
                            <col class="w-25p">
                            <col class="w-25p">
                        </colgroup>
                        <tr>
                            <td colspan="99">
                                <!--미팅보고서-->
                                <b>미팅보고서 업로드</b>
                                <simple-file-upload :file="fileList.fileEtc1" :id="'fileEtc1'" :project="project" v-show="isModify"></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <td class="pdt20" colspan="99">
                                <div class="notice-info">
                                    기획/제안/사양서는 각 단계에서 업로드 가능합니다.
                                </div>
                                <div class="notice-info">
                                    분류패킹 자료는 분류패킹 항목에서 업로드 가능합니다.
                                </div>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
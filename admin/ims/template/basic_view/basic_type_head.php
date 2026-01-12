
<div class="col-xs-6">
    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            <?=$title?>
        </div>
        <div class="flo-right">
            <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">수정</div>
            <div class="btn btn-red btn-red2" @click="saveProject()" v-show="isModify">저장</div>
            <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">수정취소</div>
        </div>
    </div>
    <table class="table table-cols  xsmall-picker">
        <colgroup v-if="!isModify">
            <col class="width-sm">
            <col class="width-md">
            <col class="width-sm">
            <col class="width-md">
        </colgroup>
        <colgroup v-if="isModify">
            <col class="w90p">
            <col class="width-md">
            <col class="w90p">
            <col class="width-md">
        </colgroup>
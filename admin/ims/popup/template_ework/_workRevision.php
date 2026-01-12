<!-- ############### 변경히스토리 ############### -->
<div class="col-xs-12 new-style" v-show="'revision' === tabMode">
    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            Revision
        </div>
        <div class="flo-right pdb5">

            <div class="btn btn-red" @click="revisionModify=true" v-show="!revisionModify">
                수정
            </div>

            <div class="btn btn-red btn-red-line2" @click="addEworkRevision()" v-show="!revisionModify">
                <i class="fa fa-plus text-danger"></i>
                Revision 내용 등록
            </div>

            <div class="btn btn-red cursor-pointer hover-btn" @click="saveEworkRevision()" v-show="revisionModify">저장</div>
            <div class="btn btn-white cursor-pointer hover-btn" @click="cancelEworkRevision()" v-show="revisionModify">취소</div>

        </div>
    </div>

    <table class="table table-cols table-pd-0 table-center table-th-height30 table-td-height30">
        <colgroup>
            <col class="w-5p">
            <col class="w-7p">
            <col class="w-7p">
            <col class="w-8p">
            <col class="w-8p">
            <col class="w-14p">
            <col class="w-13p">
            <col class="w-13p">
            <col class="w-6p">
            <col class="w-11p">
            <col class="w-10p">
        </colgroup>
        <tbody>
        <tr >
            <th >번호</th>
            <th >등록일</th>
            <th >등록자</th>
            <th >변경 사유</th>
            <th >변경 구분</th>
            <th >변경 세부</th>
            <th >변경 전</th>
            <th >변경 후</th>
            <th >상태</th>
            <th >변경자/변경일시</th>
            <th >{% revisionModify ? '추가/삭제' : '등록경로' %}</th>
        </tr>
        <tr v-for="(rev, revIndex) in mainData.ework.data.revision" v-show="!revisionModify">
            <td>{% revIndex+1 %}</td>
            <td>{% $.formatShortDateWithoutWeek(rev.regDt) %}</td><!--등록일-->
            <td>{% rev.regManagerName %}</td><!--등록자-->
            <td class="">
                <select class="form-control" v-model="rev.revReason" disabled style="background-color:#fff">
                    <option value="0">선택</option>
                    <?php foreach($revReasonList as $revReasonKey => $revReason) { ?>
                        <option value="<?=$revReasonKey?>"><?=$revReason?></option>
                    <?php } ?>
                </select>
            </td><!--변경사유-->
            <td class="">
                <select class="form-control" v-model="rev.revType" disabled style="background-color:#fff">
                    <option value="0">선택</option>
                    <?php foreach($revTypeList as $revTypeKey => $revType) { ?>
                        <option value="<?=$revTypeKey?>"><?=$revType?></option>
                    <?php } ?>
                </select>
            </td><!--변경구분-->
            <td>{% rev.revDetail %}</td><!--변경상세-->
            <td>{% rev.revBefore %}</td><!--변경전-->
            <td>{% rev.revAfter %}</td><!--변경후-->
            <td>{% rev.revSt %}</td>
            <td>{% rev.chgManagerName %}<br/>{% rev.chgDt %}</td>
            <td>{% rev.revRoute %}</td><!--삭제-->
        </tr>
        <tr v-for="(rev, revIndex) in mainData.ework.data.revision" v-show="revisionModify">
            <td>{% revIndex+1 %}</td>
            <td>{% $.formatShortDateWithoutWeek(rev.regDt) %}</td><!--등록일-->
            <td>{% rev.regManagerName %}</td><!--등록자-->
            <td>
                <select class="form-control" v-model="rev.revReason">
                    <option value="0">선택</option>
                    <?php foreach($revReasonList as $revReasonKey => $revReason) { ?>
                        <option value="<?=$revReasonKey?>"><?=$revReason?></option>
                    <?php } ?>
                </select>
            </td><!--변경사유-->
            <td class="pd5">
                <select class="form-control" v-model="rev.revType">
                    <option value="0">선택</option>
                    <?php foreach($revTypeList as $revTypeKey => $revType) { ?>
                        <option value="<?=$revTypeKey?>"><?=$revType?></option>
                    <?php } ?>
                </select>
            </td><!--변경구분-->
            <td class="pd5">
                <input type="text" class="form-control w-90p mgt20" maxlength="50" v-model="rev.revDetail">
                <span class="font-10">변경상세 40자내외</span>
            <td class="pd5">
                <input type="text" class="form-control w-90p mgt20" maxlength="35" v-model="rev.revBefore">
                <span class="font-10">변경전 30자내외</span>
            </td><!--변경전-->
            <td class="pd5">
                <input type="text" class="form-control w-90p mgt20" maxlength="35" v-model="rev.revAfter">
                <span class="font-10">변경후 30자내외</span>
            </td><!--변경후-->
            <td>
                <select class="form-control" v-model="rev.revSt" @change="if(rev.revSt == '변경완료') { rev.chgManagerName=staticLoginName; rev.chgDt=staticCurrDt; } else { rev.chgManagerName=''; rev.chgDt=''; };">
                    <option value="대기">대기</option>
                    <option value="변경완료">변경완료</option>
                </select>
            </td>
            <td>{% rev.chgManagerName %}<br/>{% rev.chgDt %}</td>
            <td>
                <div class="btn btn-sm btn-white" @click="addEworkRevision()">추가</div>
                <div class="btn btn-sm btn-white" @click="deleteElement(mainData.ework.data.revision, revIndex)">삭제</div>
            </td><!--삭제-->
        </tr>
        <tr v-if="0 >= mainData.ework.data.revision.length">
            <td colspan="99" class="ta-c">데이터 없음</td>
        </tr>
        </tbody>
    </table>

    <div class="ta-c">
        <div class="btn btn-red cursor-pointer hover-btn" @click="saveEworkRevision()" v-show="revisionModify">저장</div>
        <div class="btn btn-white cursor-pointer hover-btn" @click="cancelEworkRevision()" v-show="revisionModify">취소</div>
    </div>

</div>
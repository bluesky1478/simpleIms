<!--코멘트-->
<div class="col-xs-12" >
    <div class="" >

        <div class="font-18 font-bold">
            프로젝트 코멘트 (읽기전용)
        </div>


        <!--
        <div class="btn btn-white"><a href="#project-comment-area" > <i class="fa fa-pencil" aria-hidden="true"></i> 코멘트 작성</a></div>
        -->

        <div class="form-inline">
            <table class="table table-rows mgt10 ims-comment-table">
                <colgroup>
                    <col style="width:100px">
                    <col >
                </colgroup>
                <theader>
                    <tr>
                        <th class="ta-c">번호</th>
                        <th class="ta-c">내용</th>
                    </tr>
                </theader>
                <tbody v-for="(comment, commentIndex) in commentList" v-show="commentShowCnt >= commentIndex">
                <tr>
                    <td class="ta-c" rowspan="2">
                        <span class="font-16">{% commentList.length - commentIndex %}</span>
                        <br>
                        <div v-show="<?=$managerInfo['sno']?> == comment.regManagerSno || <?=\SiteLabUtil\SlCommonUtil::isDevId()?'true':'false'?> " class="mgt10">
                            <!--
                            <div class="btn btn-sm btn-white btn-memo-delete" @click="modifyComment(commentList, comment, commentIndex)">수정</div>
                            <div class="btn btn-sm btn-white btn-memo-delete" @click="deleteComment(commentList, comment, commentIndex)">삭제</div>
                            -->
                        </div>
                        <br>
                        <span class="text-muted">#{% comment.sno %}</span>
                    </td>
                    <td class="" style="border-right:none;" colspan="99">
                        <div style="display: flex; padding-bottom:0" class="pdl10 pdt5">
                            <div class="table-list-photo" :style=`background-image:url('../..${comment.dispImage}');`></div>
                            <div class="mgl5 pdt10">{% comment.regManagerName %} </div>
                            <div class="mgl10 text-muted">({% comment.commentDivKr %} 단계에서 작성)<br>{% comment.regDt %}</div>
                            <div class="mgl15" v-show="!isFactory" v-if="false">
                                대외비 설정 :
                                <label class="radio-inline">
                                    <input type="radio" :name="'commentIsShare'+commentIndex" value="n"  v-model="comment.isShare" @click="ImsService.simpleUpdate(comment.sno,{'isShare':'n'})" />대외비
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" :name="'commentIsShare'+commentIndex" value="y"  v-model="comment.isShare" @click="ImsService.simpleUpdate(comment.sno,{'isShare':'y'})"/>공유
                                </label>
                            </div>
                        </div>

                    </td>
                </tr>
                <tr >
                    <td colspan="99" style="border-top:none;">
                        <div class="ims-comment" v-html="comment.commentBr"></div>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="99" class="text-center">
                        <div class="btn btn-white btn-sm mgt5" @click="commentShowCnt+=4" v-show="commentList.length > commentShowCnt" >더보기▼</div>
                        <div class="btn btn-white btn-sm mgt5" @click="commentShowCnt=commentInitShowCnt" v-show="commentShowCnt > commentInitShowCnt ">최소화▲</div>
                    </td>
                </tr>
                </tfoot>
            </table>


            <div v-show=false>
                <span id="project-comment-area" class="">

                    <div class="" id="project-comment"  style="width:100%">
                        <textarea class="form-control" placeholder="코멘트 내용" id="editor" style="width:100%" rows="30"></textarea>
                    </div>
                </span>
                <div class="btn btn-red hover-btn" @click="saveComment(project); swWriteComment='n'" style="padding: 10px 10px; width:100%">코멘트 쓰기</div>
            </div>

        </div>

    </div>
</div>

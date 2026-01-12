<!--코멘트-->
<div class="col-xs-12 " >
    <div >
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
                <tbody v-for="(comment, commentIndex) in mainData.commentList" v-show="commentShowCnt >= commentIndex">
                <tr>
                    <td class="ta-c" rowspan="2">
                        <span class="font-16">{% mainData.commentList.length - commentIndex %}</span>
                        <br>
                        <span class="text-muted">#{% comment.sno %}</span>
                    </td>
                    <td class="" style="border-right:none;" colspan="99">
                        <div style="display: flex; padding-bottom:0" class="pdl10 pdt5">
                            <div class="table-list-photo" :style=`background-image:url('../..${comment.dispImage}');`></div>
                            <div class="mgl5 pdt10">{% comment.regManagerName %} </div>
                            <div class="mgl10 text-muted">({% comment.commentDivKr %} 단계에서 작성)<br>{% comment.regDt %}</div>
                            <div class="mgl15" v-show="!isFactory">
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
                        <div class="btn btn-white btn-sm mgt5" @click="commentShowCnt+=4" v-show="mainData.commentList.length > commentShowCnt" >더보기▼</div>
                        <div class="btn btn-white btn-sm mgt5" @click="commentShowCnt=commentInitShowCnt" v-show="commentShowCnt > commentInitShowCnt ">최소화▲</div>
                    </td>
                </tr>
                </tfoot>
            </table>

        </div>

    </div>
</div>

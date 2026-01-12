<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>개별 스케쥴 코멘트</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <div class="">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                <?=$stepAllList[$requestParam['div']]?> 정보
            </div>
            <div class="flo-right">
                <div class="btn btn-white" v-if="'v' === viewMode" @click="viewMode = 'm'">일정 수정하기</div>
                <div class="btn btn-red" v-if="'m' === viewMode" viewMode = 'v' @click="save()">저장</div>
                <div class="btn btn-white" v-if="'m' === viewMode" @click="viewMode = 'v'">취소</div>
            </div>
        </div>
        <div class="clear-both"></div>
        <div class="mgt5">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-md">
                    <col class="width-xl">
                    <col class="width-md">
                    <col class="width-xl">
                </colgroup>
                <tbody >
                <tr>
                    <th>고객사</th>
                    <td class="font-14">
                        {% production.customerName %}
                    </td>
                    <th>생산상품</th>
                    <td class="font-14">
                        {% production.styleFullName %} ({% $.setNumberFormat(production.totalQty) %}개)
                        <br><span class="text-muted">#<?=$requestParam['sno']?></span>
                    </td>
                </tr>
                <tr>
                    <th>최초예정일</th>
                    <td class="font-14">
                        <div>{% $.formatShortDate(production.firstData.schedule.<?=$requestParam['div']?>.ConfirmExpectedDt) %}</div>
                        <div>{% production.firstData.schedule.<?=$requestParam['div']?>.Memo %}</div>
                    </td>
                    <th>현재예정일</th>
                    <td class="font-14">
                        <div v-if="'v' === viewMode">
                            <div>날짜 : {% $.formatShortDate(production.<?=$requestParam['div']?>ExpectedDt) %}</div>
                            <div>대체내용 : {% production.<?=$requestParam['div']?>Memo %}</div>
                        </div>
                        <div v-if="'m' === viewMode">
                            <div>날짜 : <date-picker v-model="production.<?=$requestParam['div']?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            <div class="mgt5"><input type="text" class="form-control" v-model="production.<?=$requestParam['div']?>Memo" placeholder="예정일 대체내용"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>완료일</th>
                    <td class="font-14">
                        <div v-if="'v' === viewMode">
                            <div>날짜 : {% $.formatShortDate(production.<?=$requestParam['div']?>CompleteDt) %}</div>
                            <div>대체내용 : {% production.<?=$requestParam['div']?>Memo2 %}</div>
                        </div>
                        <div v-if="'m' === viewMode">
                            <div>날짜 : <date-picker v-model="production.<?=$requestParam['div']?>CompleteDt" value-type="format" format="YYYY-MM-DD" :lang="lang" placeholder="0000-00-00"></date-picker>
                            <div class="mgt5"><input type="text" class="form-control" v-model="production.<?=$requestParam['div']?>Memo2" placeholder="완료일 대체내용"></div>
                        </div>
                    </td>
                    <th>상태</th>
                    <td class="font-14">
                        <span  class="font-14" v-html="ImsService.setStatusFilter2(production.<?=$requestParam['div']?>Confirm)" ></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                코멘트 등록
            </div>
            <div class="flo-right"></div>
        </div>

        <div>
            <textarea class="form-control" rows="5" placeholder="코멘트 입력" v-model="comment"></textarea>
        </div>
    </div>

    <div class="ta-c mgt20">
        <div class="btn btn-lg btn-red" @click="saveProductionComment(comment)">코멘트 등록</div>
        <div class="btn btn-lg btn-white" @click="self.close()">닫기</div>
    </div>

    <div>
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                코멘트 리스트
            </div>
            <div class="flo-right"></div>
        </div>

        <table class="table table-rows ch-table">
            <colgroup>
                <col style="width:13%" />
                <col style="width:13%" />
                <col  />
                <col style="width:11%" />
                <col style="width:10%" />
            </colgroup>
            <tr>
                <th>등록일</th>
                <th>등록자</th>
                <th>등록내용</th>
                <th>수정</th>
                <th>삭제</th>
            </tr>
            <tr v-for="eachComment in commentList">
                <td>{% eachComment.regDt %}</td>
                <td>{% eachComment.regManagerName %}</td>
                <td class="ta-l">
                    <span v-html="eachComment.commentBr" v-show="'n' === eachComment.isModify"></span>
                    <textarea v-model="eachComment.comment" class="form-control w100" v-show="'y' === eachComment.isModify"></textarea>
                </td>
                <td>
                    <div class="btn btn-sm btn-white" @click="()=>{eachComment.isModify = 'y'}" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'n' === eachComment.isModify ">수정</div>
                    <div class="btn btn-sm btn-red" @click="updateComment(eachComment)" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'y' === eachComment.isModify ">저장</div>
                    <div class="btn btn-sm btn-white" @click="()=>{eachComment.isModify = 'n'}" v-if="<?=$managerSno?> == eachComment.regManagerSno && 'y' === eachComment.isModify ">취소</div>
                </td>
                <td>
                    <div class="btn btn-sm btn-white" @click="ImsService.deleteData('productionComment',eachComment.sno, ()=>{location.reload();})" v-if="<?=$managerSno?> == eachComment.regManagerSno">삭제</div>
                </td>
            </tr>
            <tr v-if="0 >= commentList.length">
                <td colspan="99" class="ta-c">데이터가 없습니다.</td>
            </tr>
            
        </table>
    </div>

</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const productionSno = '<?=$requestParam['sno']?>';
        const commentType = '<?=$requestParam['div']?>';

        ImsService.getDataParams(DATA_MAP.PRODUCTION, {
            sno : productionSno,
        }).then((data)=>{
            const initParams = {
                data : {
                    commentList : [],
                    comment : '',
                    production : data.data,
                    viewMode : 'v', //v : view , m : modify
                },
                methods : {
                    save : ()=>{
                        const updateExpectedField = commentType + 'ExpectedDt';
                        const updateMemoField = commentType + 'Memo';
                        const updateCompleteField = commentType + 'CompleteDt';
                        const updateMemo2Field = commentType + 'Memo2';
                        $.imsPost('saveSimpleProduction',{
                            sno : productionSno
                            , [updateExpectedField] : vueApp.production[updateExpectedField]
                            , [updateMemoField] : vueApp.production[updateMemoField]
                            , [updateCompleteField] : vueApp.production[updateCompleteField]
                            , [updateMemo2Field] : vueApp.production[updateMemo2Field]
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 되었습니다.','', "success").then(()=>{
                                    vueApp.viewMode = 'v';
                                    //parent.opener.location.reload();
                                    parent.opener.refreshProductionList();
                                    /*if( !$.isEmpty(vueApp.product.sno) && 0 != vueApp.product.sno ){
                                        ImsProductService.getProductionListAndListRefresh(vueApp.product.sno);
                                    }else{
                                    }*/
                                });
                            }
                        });
                    },
                    saveProductionComment : (comment)=>{
                        $.imsPost('saveProductionComment',{
                            productionSno : productionSno
                            , commentType : commentType
                            , comment : comment
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 되었습니다.','', "success").then(()=>{
                                    location.reload();
                                });
                            }
                        });
                    },
                    updateComment : (eachComment)=>{
                        $.imsPost('saveProductionComment',{
                            sno : eachComment.sno
                            , comment : eachComment.comment
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('수정 되었습니다.','', "success").then(()=>{
                                    eachComment.commentBr = $.nl2br(eachComment.comment)
                                    eachComment.isModify = 'n';
                                });
                            }
                        });
                    },
                    setCommentBr : (item)=>{
                        item.commentBr = $.nl2br(item.comment)
                    },
                },
            };

            //코멘트 리스트 가져오기
            $.imsPost('getProductionCommentList',{
                productionSno : productionSno
                , commentType : commentType
            }).then((data)=>{
                if(200 === data.code){
                    console.log(data.data);
                    initParams.data.commentList = data.data;
                    vueApp = ImsService.initVueApp(appId, initParams);
                    console.log('Init OK');
                }else{
                    console.log('Init FAIL');
                }
            });

        });
    });
</script>

<script type="text/javascript">
    /**
     * TO-DO List 서비스
     */
    const ImsTodoService = {
        //결재 타입
        approvalType : JSON.parse('<?=$approvalType?>'),

        //
        getApprovalData : async (approvalType, projectSno, styleSno, eachSno) => {
            const params = {
                mode : 'getData',
                target : 'approvalData',
                projectSno : projectSno,
                styleSno : styleSno,
                eachSno : eachSno,
                approvalType : approvalType,
            }
            let rslt = [];
            await $.postAsync('<?=$myHost?>/ims/ims_ps.php', params).then((result)=>{
                if( 200 === result.code ){
                    rslt = result.data;
                }
            });
            return rslt;
        },
        getData : async (sno) => {
            const params = {
                mode : 'getData',
                target : 'todoRequest',
                sno : sno,
                approvalType : '<?=$requestParam['approvalType']?>',
            }
            let rslt = [];
            await $.postAsync('<?=$myHost?>/ims/ims_ps.php', params).then((result)=>{
                if( 200 === result.code ){
                    rslt = result.data;
                }
            });
            return rslt;
        },
        getListApprovalLine : (page)=>{
            ImsRequestService.getList('approvalLine', page);
        },
        getListTodoRequest : (page)=>{
            $('#reqAllCheck').prop('checked',false);
            $('.req-list-check').each(function(){
                $(this).prop('checked',false);
            });
            ImsRequestService.getList('todoRequest', page);
        },
        getListTodoResponse : (page)=>{
            $('#resAllCheck').prop('checked',false);
            $('.res-list-check').each(function(){
                $(this).prop('checked',false);
            });
            ImsRequestService.getList('todoResponse', page);
        },
        getListTodoApproval : (page)=>{
            $('#appAllCheck').prop('checked',false);
            $('.req-list-check').each(function(){
                $(this).prop('checked',false);
            });
            ImsRequestService.getList('todoApproval', page);
        },
        /**
         * 상태변경
         * @param resSnoList
         * @param status
         * @param reqSno
         */
        setTodoStatus : (resSnoList, status, reqSno)=>{
            $.msgConfirm('해당 요청을 처리완료 하시겠습니까?','').then(function(result){
                if( result.isConfirmed ){
                    $.imsPost('setTodoStatus',{
                        snoList : resSnoList,
                        status : status,
                    }).then(()=>{
                        $.msg('상태변경 완료','','success').then(()=>{
                            try{
                                //댓글이 있으면 댓글도 등록한다.
                                if(!$.isEmpty(reqSno) && !$.isEmpty(vueApp.todoComment)){
                                    vueApp.writeComment(reqSno);
                                }
                                //리스트 갱신.
                                try{parent.opener.refreshTodoResponseList();}catch (e){}
                                try{parent.opener.refreshTodoRequestList();}catch (e){}
                                try{parent.opener.reload()}catch (e){}

                                location.reload();// 화면갱신
                            }catch (e){
                            }
                        });

                    });
                }
            });
        },
        saveExpectedDate : (resSnoList, expectedDt)=>{
            $.imsPost('saveTodoExpectedDt',{
                snoList : resSnoList,
                expectedDt : expectedDt,
            }).then(()=>{
                $.msg('저장 완료','','success').then(()=>{
                    try{
                        refreshTodoResponseList();//리스트 갱신.
                    }catch (e){}
                });

            });
        },
        writeComment : async (reqSno, contents, commentSno)=>{
            return await $.imsPost('writeComment',{
                todoSno : reqSno,
                comment : contents,
                sno : commentSno,//수정을 위함
            });
        },
        getListTodoComment : (todoSno)=>{
            ImsService.getList(DATA_MAP.TODO_COMMENT, {todoSno : todoSno}).then((data)=>{
                if( typeof vueApp.todoCommentList != 'undefined'){
                    vueApp.todoCommentList = data.data;
                    console.log('CommentList',vueApp.todoCommentList);
                }else{
                    $.msg('코멘트 저장소가 없습니다.','개발팀 문의','warning');
                }
            });
        },
        setApprovalStatus : async (sno, approvalStatus)=>{
            const statusKr={
                'accept' : '결재',
                'reject' : '반려',
                'cancel' : '결재를 취소',
                'remove' : '결재요청을 철회',
            };
            $.msgConfirm( statusKr[approvalStatus] + ' 하시겠습니까?','').then(function(result) {
                if (result.isConfirmed) {
                    $.imsPost('setApprovalStatus',{
                        sno : sno,
                        approvalStatus : approvalStatus,
                    }).then((data)=>{
                        if(200 === data.code){
                            $.msg(data.message,'','success').then(()=>{
                                parent.opener.location.reload(); //부모창이 있다면 부모창도 갱신
                                self.close();
                            });
                        }else{
                            $.msg(data.message,'','warning');
                        }
                    });
                }
            });
        },
        setApprovalComplete : async (sno)=>{
            $.msgPrompt('전결 승인 처리를 진행합니다.','사유 필수','전결 사유 입력', (confirmMsg)=>{
                if( confirmMsg.isConfirmed ){
                    if( $.isEmpty(confirmMsg.value) ){
                        $.msg('사유는 필수 입니다.', "", "warning");
                        return false;
                    }else{
                        $.imsPost('setApprovalStatus',{
                            sno : sno,
                            approvalStatus : 'allAccept',
                            reason : confirmMsg.value,
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg(data.message,'','success').then(()=>{
                                    parent.opener.location.reload(); //부모창이 있다면 부모창도 갱신
                                    self.close();
                                });
                            }else{
                                $.msg(data.message,'','warning');
                            }
                        });
                    }
                }
            });
        },
    }

</script>
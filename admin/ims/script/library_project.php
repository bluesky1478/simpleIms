<script type="text/javascript">
    /**
     * 프로젝트 서비스
     */
    const ImsProjectService = {
        //프로젝트 등록 ( 신규 등록 시 사용 )
        saveProject : (project, projectExt)=>{
            $.imsPost('saveImsProject',{
                'project' : project,
                'projectExt' : projectExt,
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    console.log(data);
                    $.msg('프로젝트 등록 완료','','success').then(()=>{
                        parent.opener.location.reload(); //부모창 갱신.
                        self.close();
                    });
                });
            });
        },

        //프로젝트 수정
        updateProject : async (project)=>{
            const promiseData = $.imsPost('updateProject',{
                'project' : project,
            });
            promiseData.then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    $.msg('저장 완료','','success').then(()=>{
                        //parent.opener.location.reload(); //부모창 갱신.
                        //self.close();
                    });
                });
            });
            return await promiseData;
        },

        getListProject : (page)=>{
            ImsRequestService.getList('project', page);
            if(typeof vueApp.projectCheckList != 'undefined'){
                $('#prdAllCheck').prop('checked',false);
                vueApp.projectCheckList = [];
            }
        },

        getListProjectWithAddInfo : (page)=>{
            const searchType = 'project';

            if( typeof page != 'undefined' ){
                vueApp[searchType+'SearchCondition'].page = page;
            }

            //검색 결과 쿠키에 저장.
            $.cookie(searchType+'SearchCondition', JSON.stringify(vueApp[searchType+'SearchCondition']));
            //console.log('저장된 검색 값.', $.cookie(searchType+'SearchCondition'));

            const rsltPromise = ImsService.getList('projectWithAddInfo',vueApp[searchType+'SearchCondition']);
            //1차적 처리
            rsltPromise.then((data)=>{
                if(200 === data.code){
                    console.log('getListProjectWithAddInfo',data.data.list);
                    //console.log('page info',data.data.page);
                    vueApp[searchType+'List'] = data.data.list;
                    vueApp[searchType+'Page'] = data.data.pageEx;
                    vueApp[searchType+'Total'] = data.data.page;
                    //Paging Event
                    vueApp.$nextTick(function () {

                        //샘플 수 & 협상정보 가져오기
                        vueApp[searchType+'List'].forEach((project)=>{
                            ImsProjectService.getSampleCountByProject(project.sno, project);

                            <?php if( 15 == $requestParam['status']) { ?>
                                $.imsPost('getNegoData',{customerSno:project.customerSno}).then((data)=>{
                                    if(200===data.code) {
                                        project.negoText = data.data;
                                        console.log(project.customerSno, ':', data.data);
                                    };
                                });

                            <?php } ?>
                        });

                        //페이징 이벤트 삽입
                        $('#'+searchType+'-page .pagination').find('a').each(function(){
                            $(this).off('click').on('click',function(){
                                ImsProjectService.getListProjectWithAddInfo($(this).data('page'));
                            });
                        });
                    });
                }
            });

            if(typeof vueApp.projectCheckList != 'undefined'){
                $('#prdAllCheck').prop('checked',false);
                vueApp.projectCheckList = [];
            }

            return rsltPromise;
        },

        getSampleCountByProject : async (projectSno, project)=>{
            await $.imsPost('getSampleCountByProject',{projectSno:projectSno}).then((data)=>{
                //console.log('결과',data.data);
                if(200===data.code) {
                    project.sampleTotalCount = data.data;
                }
            });
        },
        /**
         * 프로젝트 파일 가져오기
         * @param projectSno
         * @param project
         * @returns {Promise<void>}
         */
        getProjectFile : async (projectSno)=>{
            return await $.imsPost2('getProjectFile',{projectSno:projectSno},()=>{});
        },

    }

</script>
<script type="text/javascript">
    /**
     * 생산처 요청 리스트 관련 서비스
     */
    const ImsRequestService = {
        /* =============================  [ QB 리스트 조회 ]   ============================= */
        getList : async ( searchType, page )=>{
            // 스크롤 위치를 저장
            window.addEventListener('scroll', saveScrollPosition());

            if( typeof page != 'undefined' ){
                vueApp[searchType+'SearchCondition'].page = page;
            }

            //검색 결과 쿠키에 저장.

            //검색값 비교
            const searchObject = JSON.stringify(vueApp[searchType+'SearchCondition'])
            const saveObject = $.cookie(searchType+'SearchCondition');

            if(searchObject == saveObject){
                const scrollPosition = localStorage.getItem('scrollPosition');
                window.scrollTo(0, parseInt(scrollPosition));
            }
            $.cookie(searchType+'SearchCondition', JSON.stringify(vueApp[searchType+'SearchCondition']));

            const rsltPromise = ImsService.getList(searchType,vueApp[searchType+'SearchCondition']);
            //1차적 처리
            rsltPromise.then((data)=>{
                if(200 === data.code){
                    console.log(searchType + ' getListData',data.data.list);
                    //console.log('page info',data.data.page);
                    vueApp[searchType+'List'] = data.data.list;
                    vueApp[searchType+'Page'] = data.data.pageEx;
                    vueApp[searchType+'Total'] = data.data.page;
                    //Paging Event
                    vueApp.$nextTick(function () {
                        $('#'+searchType+'-page .pagination').find('a').each(function(){
                            $(this).off('click').on('click',function(){
                                //fncMap[searchType]($(this).data('page'));
                                ImsRequestService.getList(searchType, $(this).data('page'));
                            });
                        });
                        $('#init-msg').text('데이터가 없습니다.');
                    });
                }
            })
            return rsltPromise;
        },
        getListQb : (page)=>{
            ImsRequestService.getList('qb', page);
        },
        getListEstimate : (page)=>{
            ImsRequestService.getList('estimate', page);
        },
        getListCost : (page)=>{
            ImsRequestService.getList('cost', page);
        },
        getListCompleteBt : (page)=>{
            ImsRequestService.getList('cqb', page);
        },
    }

</script>
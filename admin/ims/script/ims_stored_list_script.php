<script type="text/javascript">

    const mainListPrefix = 'stored'; //페이징div의 id값 mainListPrefix+'-page' 로 해야함
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.fabricName', //기본:비축 자재명
            keyword : '',
        }],
        multiCondition : 'OR',
        aChkboxSchInputOwn : [], //체크박스 검색필터
        page : 1,
        pageNum : 99999,
        sort : 'A,asc', //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);
        return $.imsPost('getListStored', params);
    };

    //외부에서 실행
    function refreshList(){
        vueApp.refreshList(1);
    }

    $(()=>{

        $('title').html('비축 원부자재 리스트');

        $(appId).hide();
        const serviceData = {};
        ImsBoneService.setMethod(serviceData, {
            //자재수정
            modFabric : (storedPk)=>{
                openCommonPopup('stored_mod', 540, 610, {'sno':storedPk});
            },
            //자재삭제
            delFabric : (storedPk)=>{
                if (confirm('삭제하시겠습니까?')) {
                    let params = {};
                    params.storedSno = storedPk;
                    $.imsPost('deleteStoredFabric', params).then(function (result) {
                        $.msg('자재정보 삭제 완료','','success').then(()=>{
                            location.reload();
                        });
                    });
                }
            },
            //입고하기
            setInput : (storedPk)=>{
                openCommonPopup('stored_input_reg', 540, 610, {'sno':storedPk});
            },
            //입고정보 수정
            modInput : (inputPk)=>{
                openCommonPopup('stored_input_mod', 540, 610, {'sno':inputPk});
            },
            //입고정보 삭제
            delInput : (inputPk)=>{
                if (confirm('삭제하시겠습니까?')) {
                    let params = {};
                    params.inputSno = inputPk;
                    $.imsPost('deleteStoredFabricInput', params).then(function (result) {
                        $.msg('입고정보 삭제 완료','','success').then(()=>{
                            location.reload();
                        });
                    });
                }
            },
            //출고하기
            setOutput : (inputPk)=>{
                openCommonPopup('stored_output_reg', 500, 410, {'sno':inputPk});
            },
            //출고리스트 가져오기
            getOutputList : (inputPk)=>{
                openCommonPopup('stored_output_list', 860, 710, {'sno':inputPk});
            },
            //엑셀
            listDownload : ()=>{
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                // /console.log(queryString);
                location.href=`ims_stored_list.php?simple_excel_download=1&` + queryString;
            }
        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //stored , storedSearchCondition
        listService.init(serviceData);
    });

//--

</script>
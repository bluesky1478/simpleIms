<script type="text/javascript">
    const mainListPrefix = 'storedOutput';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'c.outReason', //기본:출고사유
            keyword : '',
        }],
        multiCondition : 'OR',
        page : 1,
        pageNum : 50,
        sort : 'C,asc' //정렬
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);


        params.inputSno = <?=$inputSno?>;
        return $.imsPost('getListStoredOutput', params);
    };
    //외부에서 실행
    function refreshList(){
        vueApp.refreshList(1);
    }
    $(()=>{
        $(appId).hide();
        const serviceData = {};
        ImsBoneService.setMethod(serviceData, {
            //출고정보 수정
            modOutput : (outputPk, inputPk)=>{
                openCommonPopup('stored_output_mod', 540, 610, {'sno':outputPk, 'inputSno':inputPk});
            },
            //출고정보 삭제
            delOutput : (outputPk)=>{
                if (confirm('삭제하시겠습니까?')) {
                    let params = {};
                    params.outputSno = outputPk;
                    $.imsPost('deleteStoredFabricOutput', params).then(function (result) {
                        $.msg('출고정보 삭제 완료','','success').then(()=>{
                            parent.opener.location.reload(); //부모창 갱신.
                            location.reload();
                        });
                    });
                }
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //stored , storedSearchCondition
        listService.init(serviceData);
    });

</script>

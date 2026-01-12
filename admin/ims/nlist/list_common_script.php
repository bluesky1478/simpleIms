<script type="text/javascript">

    $('title').html('프로젝트리스트');
    
    function refreshListExtFnc(){
        vueApp.refreshList(1);
    };

    const getListAfter = ()=>{
        //틀고정 이벤트
        if($.isEmpty($("#affix-show-type2").html())){
            let clonedElement = $("#list-main-table").clone(); // 복제
            clonedElement.find("tr:not(:first)").remove();
            clonedElement.appendTo("#affix-show-type2"); // 복제된 요소를 body에 추가
            const setAffix = function(){
                if ($(document).scrollTop() > 400) {
                    $('#affix-show-type2').show();
                    $('#affix-show-type1').hide();
                }else{
                    $('#affix-show-type1').show();
                    $('#affix-show-type2').hide();
                }
            }
            $(window).resize(function (e) {
                setAffix();
            });
            $(window).scroll(setAffix);
        }
    };


    $(appId).hide();
    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'][0],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
    };
    const defaultMultiKey2= {
        key : 'prj.sno',
        keyword : '',
    };

    //기본 검색
    const commonSearchDefault = {
        key : '<?=gd_isset($requestParam['key'][0],'cust.customerName')?>',
        keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
        multiKey : [
            $.copyObject(defaultMultiKey1),
            $.copyObject(defaultMultiKey2),
        ],
        multiCondition : 'OR',
        page : 1,
        pageNum : 20,
        sort : 'P2,desc', //정렬
        projectTypeChk : [], //프로젝트 타입
        salesStatusChk : [], //프로젝트 타입
    }

    const listSearchDefaultData = $.copyObject(commonSearchDefault);
    //console.log( listSearchDefaultData );
    //let searchInitData = $.copyObject(commonSearchDefault);
    //const listSearchDefaultData  = searchInitData;
</script>


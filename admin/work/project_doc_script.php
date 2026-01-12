<script type="text/javascript">

    console.log(' project doc script ');

    var tempItem = {
        acceptList : [
            { acceptNo : 1
            , acceptNm : "관리자 승인(준비)"
            , managerNo : 1
            , managerNm : "송준호"
            , acceptStatus : "w"
            , acceptData : "" },
            { acceptNo : 2
            , acceptNm : "디자인 승인(준비)"
            , managerNo : 2
            , managerNm : "이천수"
            , acceptStatus : "y"
            , acceptData : "2021-01-01" },
        ]
    };


    varapp = new Vue({
        el: '#document-form'
        , beforeCreate : function(){
        }, created : function(){
        }, data : tempItem
    });

    /*JQuery*/
    $(function(){
        //초기화
        <?php if( empty($sno)  ){ ?>
        $('.confirm-block').hide();
        <?php } ?>

    });
</script>

<script type="text/javascript">

    console.log(' project doc script ');

    var tempItem = {
        docData : {
            '업체' : '깨긋한 나라',
            '참석자' : '박영요 사원',
            '미팅일자' : '2021-07-26',
            '구매형태' : '단가입찰',
            '경쟁업체' : '기존업체',
            '업체선정요소' : '품질',
            '유니폼정보' : [
                {
                    '품목': '춘추상의',
                    '예상수량': '1000~1100',
                    '현재단가': '38,000',
                    '타겟단가': '38,000',
                    '진행형태': '미정',
                    '예상발주': '미정',
                    '희망납기': '2022-01-01',
                    '불편사항': '(1)불편사항 내용을 입력한다.<br>nl2br을 써서 줄바꿈 처리하기',
                },
                {
                    '품목': '춘추바지',
                    '예상수량': '1000~1400',
                    '현재단가': '25,000',
                    '타겟단가': '50,000',
                    '진행형태': '미정',
                    '예상발주': '미정',
                    '희망납기': '2022-01-01',
                    '불편사항': '(2)불편사항 내용을 입력한다.<br>nl2br을 써서 줄바꿈 처리하기',
                },
                {
                    '품목': '티셔츠(긴팔)',
                    '예상수량': '1100~1200',
                    '현재단가': '40,000',
                    '타겟단가': '39,000',
                    '진행형태': '미정',
                    '예상발주': '미정',
                    '희망납기': '2022-01-01',
                    '불편사항': '(3)불편사항 내용을 입력한다.<br>nl2br을 써서 줄바꿈 처리하기',
                },
            ],
        },
    };


    varapp = new Vue({
        el: '#document-form'
        , beforeCreate : function(){
        }, created : function(){
        }, data : tempItem,
        methods :{
            pickerFormatter(date) {
                console.log(date);
                return moment(date).format('yyyy-MM-dd');
            }
        },
        components: {
            vuejsDatepicker
        },
    });

    /*JQuery*/
    $(function(){
        //초기화
        <?php if( empty($sno)  ){ ?>
        $('.confirm-block').hide();
        <?php } ?>

    });
</script>

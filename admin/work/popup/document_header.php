<script src="<?=URI_HOME?><?=PATH_SKIN?>work/vendors/vue.js"></script>
<!-- Select2 -->
<link rel="stylesheet" href="<?=URI_HOME?><?=PATH_SKIN?>work/vendors/select2/css/select2.min.css" type="text/css">
<script src="<?=URI_HOME?><?=PATH_SKIN?>work/vendors/select2/js/select2.min.js"></script>
<!-- Datepicker -->
<link rel="stylesheet" href="<?=URI_HOME?><?=PATH_SKIN?>work/vendors/datepicker/daterangepicker.css">
<script src="<?=URI_HOME?><?=PATH_SKIN?>work/vendors/datepicker/daterangepicker.js"></script>
<link rel="stylesheet" href="<?=URI_HOME?><?=PATH_SKIN?>/work/assets/js/dropzone/dropzone.css" type="text/css">
<script src="<?=URI_HOME?><?=PATH_SKIN?>/work/assets/js/dropzone/min/dropzone.min.js"></script>

<link rel="stylesheet" href="<?=URI_HOME?><?=PATH_SKIN?>wcustomer/css/preloader.css">

<script src="<?=URI_HOME?><?=PATH_SKIN?>work/assets/js/sl_js_module.js?ver=3"></script>
<script src="<?=URI_HOME?><?=PATH_SKIN?>work/assets/js/work_custom.js?ver=3"></script>
<script src="<?=URI_HOME?><?=PATH_SKIN?>work/assets/js/work_service.js?ver=3"></script>


<!--스위트 얼럿-->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script type="text/javascript">

    let workApp = null;
    let INIT_DATA = null;

    let dropzoneInstanceList = [];

    let getVueMethod = (vueMethod) =>{
        for(let idx in vueMethod){
            WorkDocument.defaultVueMethod[idx] = vueMethod[idx];
        }
        return WorkDocument.defaultVueMethod;
    }

    Vue.component("select2",{
        props:["value"],
        template:'<select class="js-select2" :value="value"><slot></slot></select>',
        mounted : function(){
            let parentEl = this;
            $(this.$el).on("change", function(e){
                parentEl.$emit('input', $(parentEl.$el).val());
                parentEl.$emit('change', $(parentEl.$el).val());
            });
        }
    });

    Vue.component('datepicker', {
        props:["v-model"],
        template: '<input/>',
        mounted: function() {
            let self = this;
            $(this.$el).datepicker({
                locale: 'ko',
                format: 'YYYY-MM-DD',
                dateFormat: "yy-mm-dd", // 텍스트 필드에 입력되는 날짜 형식.
                /*yearSuffix: '년',*/
                showMonthAfterYear: true,
                /*dayViewHeaderFormat: 'YYYY년 MM월',*/
                monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                dayNames: ['일', '월', '화', '수', '목', '금', '토'],
                dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
                dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
                viewMode: 'days',
                ignoreReadonly: true,
                //showAnim: "slide",
                showButtonPanel: false,
                /*stepMonths: 3,*/
                changeMonth: true,
                changeYear: true,
                currentText: '오늘 날짜' , // 오늘 날짜로 이동하는 버튼 패널
                closeText: '닫기', // 닫기 버튼 패널
                debug: false,
                keepOpen: false,
                onSelect: function(d){
                    self.$emit('update-date',d, $(this).data('item'));
                }
            });
        },
        beforeDestroy: function(){$(this.$el).datepicker('hide').datepicker('destroy')}
    });

    Vue.component("comment-area",{
        props:["value"],
        template:'<textarea class="form-control comment-text-area"></textarea>',
        mounted : function(){
            let parentEl = this;
            $(this.$el).on("change", function(e){
                parentEl.$emit('input', $(parentEl.$el).val());
                parentEl.$emit('change', $(parentEl.$el).val());
            });
        }
    });

    let setDefaultJqueryEvent = () => {
    };

    //재정의.
    function init_file_style() {}
</script>


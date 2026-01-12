
<script type="text/html" id="search-keyword-template">
    <div class="form-inline mgt5" >
        <?=gd_select_box('', 'key[]', $search['combineSearch'], null, '', null,null,'multi-search form-control'); ?>
        <input type="text" name="keyword[]" value="" class="form-control"/>
        <button type="button" class="btn btn-sm btn-white js-remove-keyword">- 제거</button>
    </div>
</script>

<script type="text/javascript">

    var myFnc = function(){
        location.reload();
    }

    $(()=>{
        const cloneElement1 = $('#main-table').find('colgroup').eq(0).clone();
        const cloneElement2 = $('#main-table').find('thead').eq(0).clone();
        $('#affix-show-type2').find('.table').eq(0).append(cloneElement1);
        $('#affix-show-type2').find('.table').eq(0).append(cloneElement2);

        //스타일 상세 보기
        $('.btn-style-on').click(function(){
            const masterEl = $(this).closest('.row-master');
            const parentEl = $(this).closest('.field-parent');
            let styleCount = Number($(this).data('styleCount'));
            styleCount += 2;

            parentEl.find('.rspan').each(function(){
                $(this).attr('rowspan', styleCount);
            });
            masterEl.find('.style-title').show();
            masterEl.find('.style-body').show();

            masterEl.addClass('bg-light-gray2');

            $(this).hide();
            parentEl.find('.btn-style-off').show();
        });
        $('.btn-style-off').click(function(){
            const masterEl = $(this).closest('.row-master');
            const parentEl = $(this).closest('.field-parent');

            parentEl.find('.rspan').each(function(){
                $(this).attr('rowspan', 1);
            });

            masterEl.find('.style-title').hide();
            masterEl.find('.style-body').hide();

            masterEl.removeClass('bg-light-gray2');

            $(this).hide();
            parentEl.find('.btn-style-on').show();
        });


        $('.js-remove-keyword').click( function(){
            $(this).closest('div').remove();
        });

        $('.js-add-keyword').click( function(){
            var initValue = $('.multi-search').last().val();
            var selectGoodsTblTr = _.template($('#search-keyword-template').html());
            var param = {};
            $('#keyword-search-area').append(selectGoodsTblTr(param));
            $('.multi-search').last().val(initValue);
            $('.js-remove-keyword').off('click').on('click',function(){
                $(this).closest('div').remove();
            });
        } );


        $('.set-check-process').click(()=>{
            $('.chk-progress').each(function(){
                const value = $(this).val();
                const chk = $(this).is(':checked');
                if( 80 > value ){
                    $(this).prop('checked',true);
                }
                $('.js-not-checkall').prop('checked',false);
                $('.btn-search').click();
                //console.log(`value : ${value} / ${chk}`);
            });
        });

        $('.excel-submit').click(function(){
            $('#frmExcel').submit();
        });

        $('.btn-modify').on('click',function(){
            const sno = $(this).closest('.field-parent').data('sno');
            location.href=`ims_project_reg.php?sno=${sno}`;
        });

        $('.btn-delete').on('click',function(){
            const sno = $(this).closest('.field-parent').data('sno');
            $.msgConfirm('삭제시 복구가 불가능 합니다. 계속 하시겠습니까?', "").then((result)=>{
                if( result.isConfirmed ){
                    $.postAsync('<?=$imsAjaxUrl?>',{
                        mode:'deleteData',
                        sno:sno,
                        target:DATA_MAP.PROJECT
                    }).then(()=>{
                        $.msg('처리 되었습니다.', "", "success").then(()=>{
                            location.reload();
                        });
                    });
                }
            });
        });

        $('.field-customer').hover(function(){
            $('.btn-hide-process').hide();
            $(this).find('.btn-hide-process').show();
        },()=>{
            $('.btn-hide-process').hide();
        });

        $('.btn-reg').click(()=>{
            location.href='./ims_project_reg.php?status=<?=$requestParam['status']?>';
        });

        $('.btn-add-produce').click(function(){
            const projectSno = $(this).data('sno');
            $.postAsync('<?=$imsAjaxUrl?>',{
                mode:'addProduce',
                projectSno:projectSno
            }).then((data)=>{
                if(200 === data.code){
                    $.msg('처리 되었습니다.', "", "success").then(()=>{
                        //location.reload();
                        window.history.back();
                    });
                }
            });
        });

        //납기일 날짜 선택
        $('.sl-dateperiod label').click(function (e) {
            let $startDate = '',
                $endDate = moment().format('YYYY-MM-DD'),
                $period = $(this).children('input[type="radio"]').val(),
                $elements = $('input[name*=\'' + $(this).closest('.sl-dateperiod').data('target-name') + '\']'),
                $inverse = $('input[name*=\'' + $(this).closest('.sl-dateperiod').data('target-inverse') + '\']'), $format = $($elements[0]).parent().data('DateTimePicker').format();
            $period = '-' + $period;
            $startDate = moment().hours(23).minutes(59).seconds(0).subtract($period, 'days').format($format);
            $($elements[1]).val($startDate);
            $($elements[0]).val($endDate);
        });


        $('.comment-cnt').hide();

        $('.field-parent').each(function(){
            const projectSno = $(this).data('sno');
            $.imsPost('getProjectCommentCnt',{projectSno:projectSno}).then((data)=>{
                if( 200 === data.code ){
                    data.data.forEach((commentItem)=>{
                        if( commentItem.cnt > 0 ){
                            $(this).find('.comment-cnt-'+commentItem.commentDiv).show();
                            $(this).find('.comment-cnt-'+commentItem.commentDiv).eq(1).html(commentItem.cnt);
                        }else{
                            $(this).find('.comment-cnt-'+commentItem.commentDiv).hide();
                        }
                    });
                }else{
                    $.msg('코멘트 가져오기 오류', "", "warning");
                }
            });
            //$(this).find('.comment-cnt-qbExpectedDt').eq(1);

        });

    });

    /*const listVueApp = new Vue({
        el: '#frmList',
        delimiters: ['{%', '%}'],
        data : {
            tabMode : 0,
        },
        methods : {},
        mounted : function() {
            //$('#layerDim').show();
            this.$nextTick(function () {
            });
        },
    });*/

</script>


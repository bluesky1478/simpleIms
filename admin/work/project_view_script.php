
<div id="layerDim">
    <div class="sl-pre-loader">
        <div class="throbber-loader"> </div>
    </div>
</div>

<script type="text/javascript">

    let getProjectData = async function(){
        return await $.postAsync('project_ps.php', {
            mode:'getProjectData',
            sno :'<?=$requestParam['sno']?>'
        });
    }

    let vueMethods = {
        updateDate: function(d, item) {
            eval( "this.items" + item + ' = \'' + d + '\'' );
        },
        saveProject: function(){
            console.log( '▶ 프로젝트 저장데이터 체크' );
            console.log( this.items );
            let saveData = this.items;
            saveData.mode = 'saveProject';
            $.postAsyncPreloader('project_ps.php', saveData).then((data)=>{
                $.msgWithErrorCheck(data, data.message,'','success');
            });
        },
        regDocument: function(docDept, docType){
            if( 'SALES' === docDept ){
                window.open( '<?=$workFrontURL?>/workAdmin/document.php?projectSno=<?=$requestParam['sno']?>&docDept=' + docDept + '&docType=' + docType );
            }else{
                window.open( 'document_reg.php?projectSno=<?=$requestParam['sno']?>&docDept=' + docDept + '&docType=' + docType );
            }
        },
        showDocument: function(sno, docDept){
            if( 'SALES' === docDept ){
                window.open( '<?=$workFrontURL?>/workAdmin/document.php?sno=' + sno );
            }else{
                window.open( 'document_reg.php?sno=' + sno );
            }
        },
        completeModify: function(){
            let myApp = this;
            let inputOptions = new Promise((resolve) => {
                resolve({
                    <?php foreach( \Component\Work\WorkCodeMap::PLAN_MOD_REASON_TYPE as $reasonKey => $reasonValue) { ?>
                    <?=$reasonKey . ' : \'' . $reasonValue .'\',' ?>
                    <?php } ?>
                })
            });
            $.msgTextareaAndRadio('수정 사유 지정 / 입력' , '수정 사유를 입력하세요.'  , inputOptions).then((result)=>{
                if( result.isConfirmed ){
                    let param = {
                        mode : 'updateStepPlan',
                        sno : '<?=$requestParam['sno']?>',
                        planDt : myApp.items.customerPlanDt,
                        reasonType : result.value[1],
                        reasonText : result.value[0],
                    }
                    $.post('work_ps.php', param, function(result){
                        console.log(result);
                        if( 200 == result.code ){
                            $.msg(result.message,'', 'success');
                            myApp.planModifySw = false;
                        }
                    });
                }else{
                    myApp.planModifySw = false;
                }

            });
        },
        setStep: function(step){
            let myApp = this;
            $.msgConfirm('선택하신 단계로 변경하시겠습니까?', "").then((result)=>{
                if( result.isConfirmed ){
                    let param = {
                        mode :  'updateStep',
                        sno : '<?=$requestParam['sno']?>',
                        step : step,
                    }
                    $.postWork(param).then((jsonResult)=>{
                        $.msgWithErrorCheck(jsonResult, jsonResult.message,'','success',()=>{
                            console.log('단계 변경 결과.');
                            console.log(jsonResult.data);
                            myApp.items.customerPlanStatus = jsonResult.data;
                        });
                    });
                }
            });
        },
        /**
         * 작업지시서 상품 가져오기.
         */
        addProductByWork: function(){
            $.showDim();
            let myApp = this;
            $.postWork({
                'mode' : 'getLatestWorkProduct',
                'projectSno' : '<?=$requestParam['sno']?>',
            }).then((jsonResult)=>{
                console.log(jsonResult.data);
                for(let idx in jsonResult.data){
                    myApp.items.productData.push(jsonResult.data[idx]);
                }
                $.hideDim();
            });
        },
        addProduct: function(){
            $.showDim();
            let myApp = this;
            $.postWork({
                'mode' : 'getDefaultProduct',
            }).then((jsonResult)=>{
                console.log( jsonResult.data );
                myApp.items.productData.push(jsonResult.data);
                //console.log(myApp.items.productData);
                $.hideDim();
            });
        }
    };

    $(function(){
        //Vue Setting
        $.showDim('#project-app');

        getProjectData().then((projectData)=>{
            console.log('▶ 프로젝트 초기 데이터');
            console.log(projectData);
            if( $.isEmpty(projectData.data.productData) ){
                projectData.data.productData = [];
            }

            new Vue({
                el: '#project-app',
                delimiters: ['{%', '%}'],
                data : {
                    items: projectData.data,
                    planModifySw : false,
                    productPlanList : JSON.parse('<?=$productPlanListJson?>'),
                    lang : {
                        formatLocale: {
                            firstDayOfWeek: 1,
                        },
                        monthBeforeYear: false,
                    },
                },
                mounted : function() {
                    this.$nextTick(function () {
                        $.hideDim('#project-app');

                        $('.js-select2').select2({
                            placeholder: '선택'
                        });

                    });
                },
                methods : vueMethods,
            });

        });

    });

</script>
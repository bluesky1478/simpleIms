<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>결재 히스토리</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <table class="table table-rows ch-table">
        <!--번호 상태 제목 기안자 결재자-->
        <colgroup>
            <col style="width:5%" />
            <col style="width:10%" />
            <col  />
            <col style="width:10%" />
            <col style="width:20%" />
        </colgroup>
        <tr>
            <th>번호</th>
            <th>상태</th>
            <th>제목</th>
            <th>기안자</th>
            <th>결재자</th>
        </tr>
        <tr v-for="(todoData, itemIndex) in todoApprovalList">
            <td>{% todoApprovalList.length - itemIndex %}</td><!--상태-->
            <td ><!--상태-->
                {% todoData.approvalStatusKr %}
            </td>
            <td><!--제목-->
                <span class="hover-btn cursor-pointer" @click="openApprovalView(todoData.sno)">{% todoData.subject %}</span>
                <div class="text-muted">{% todoData.regDt %}</div>
            </td>
            <td><!--기안자-->
                {% todoData.regManagerNm %}
            </td>
            <td><!--결재자-->
                {% todoData.appManagersStr %}
            </td>
        </tr>
    </table>

    <div id="todoApproval-page" v-html="todoApprovalPage" class="ta-c"></div>

</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        const customerSno = '<?=$requestParam['customerSno']?>';
        const projectSno = '<?=$requestParam['projectSno']?>';
        const styleSno = '<?=$requestParam['styleSno']?>';
        const eachSno = '<?=$requestParam['eachSno']?>';
        const approvalType = '<?=$requestParam['approvalType']?>';

        const init = ()=>{
            const initParams = {
                data : {
                    //요청 리스트
                    todoApprovalList : [],
                    todoApprovalTotal : ImsProductService.getTotalPageDefault(),
                    todoApprovalPage : '',
                    todoApprovalSearchCondition : $.copyObject({
                        sort : 'D,desc',
                        pageNum : 1000,
                        todoType : 'approval',
                        approvalType : approvalType,
                        customerSno : customerSno,
                        projectSno : projectSno,
                        styleSno : styleSno,
                        eachSno : eachSno,
                    }),
                },
                mounted : (vueInstance)=>{
                    //List 갱신.
                    ImsTodoService.getListTodoApproval(1);
                },
                methods : {
                },

            };

            console.log(initParams);

            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        }

        init();

    });

/*
    $(()=>{
        //Load Data.
        const customerSno = '<?=$requestParam['customerSno']?>';
        const projectSno = '<?=$requestParam['projectSno']?>';
        const styleSno = '<?=$requestParam['styleSno']?>';
        const eachSno = '<?=$requestParam['eachSno']?>';

        ImsService.getDataParams(DATA_MAP.FILE_HISTORY, {
            customerSno : customerSno,
            projectSno : projectSno,
            styleSno : styleSno,
            eachSno : eachSno,
            fileDiv : '<?=$requestParam['fileDiv']?>',
        } ).then((data)=>{
            console.log(data);
            const initParams = {
                data : {
                    items : data.data,
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });*/
</script>

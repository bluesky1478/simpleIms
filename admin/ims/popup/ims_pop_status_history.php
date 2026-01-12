<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>상태 변경 히스토리</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <div class="mgb5">
        <div class="font-16">
            {% customer.customerName %}社 {% project.projectYear %}{% project.projectSeason %} <span class="text-danger">({% project.sno %})</span> 프로젝트 상태 변경
        </div>
        <div class="dp-flex">
            <select v-model="currentStatus" class="form-control bg-white">
                <?php foreach (\Component\Ims\ImsCodeMap::PROJECT_STATUS as $key => $value ) { ?>
                    <option value="<?=$key?>"><?=$value?></option>
                <?php } ?>
            </select>

            <?php if($isDev) { ?>
                <!--<input type="text" v-model="currentStatus" class="form-control w-80px">-->
            <?php } ?>
            <div class="btn btn-red btn-red-line2 btn-sm font-normal mgt2" @click="setStatus(project)">단계 수기 변경</div>
        </div>
    </div>

    <table class="table table-rows ch-table">
        <colgroup>
            <col style="width:18%" />
            <col style="width:30%" />
            <col  />
            <col style="width:10%" />
        </colgroup>
        <tr>
            <th >변경일</th>
            <th>변경정보</th>
            <th>사유</th>
            <th>변경</th>
        </tr>
        <tr v-for="(item, itemIndex) in items">
            <td>{% item.regDt %}</td>
            <td class="font-14" style="text-align: left;padding-left:10px;">
                {% item.beforeStatus %}
                <i class="fa fa-arrow-right text-danger" aria-hidden="true"></i>
                {% item.afterStatus %}
            </td>
            <td style="text-align: left;padding-left:10px;">{% item.reason %}</td>
            <td>{% item.regManagerNm %}</td>
        </tr>
        <tr v-if="$.isEmpty(items) || 0 >= items.length">
            <td class="ta-c" colspan="99">승인이력 없음</td>
        </tr>
    </table>
</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const customerSno = '<?=$requestParam['customerSno']?>';
        const projectSno = '<?=$requestParam['projectSno']?>';
        const styleSno = '<?=$requestParam['styleSno']?>';
        const eachSno = '<?=$requestParam['eachSno']?>';
        const historyDiv = '<?=$requestParam['historyDiv']?>';

        ImsService.getDataParams(DATA_MAP.STATUS_HISTORY, {
            customerSno : customerSno,
            projectSno : projectSno,
            styleSno : styleSno,
            eachSno : eachSno,
            historyDiv : historyDiv,
        }).then((data)=>{

            ImsService.getData(DATA_MAP.PROJECT,projectSno).then((data)=>{
                console.log(data.data);
                vueApp.currentStatus = data.data.project.projectStatus;
                vueApp.project = data.data.project;
                vueApp.customer = data.data.customer;
            });

            const initParams = {
                data : {
                    items : data.data,
                    currentStatus : 0,
                    project : {},
                    customer : {},
                },
                methods : {
                    setStatus : (project)=>{
                        $.postAsync('<?=$imsAjaxUrl?>', {
                            mode : 'setStatus'
                            , projectSno : projectSno
                            , reason : '수기변경'
                            , projectStatus : vueApp.currentStatus
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('상태가 변경되었습니다.','', "success").then(()=>{
                                    parent.opener.location.reload();
                                    self.close();
                                });
                            }
                        });
                    },
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>

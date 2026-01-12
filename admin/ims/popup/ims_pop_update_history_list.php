<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3><?=$sMenuName?> 수정이력</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <table class="table table-rows ch-table table-td-height30">
        <colgroup>
            <col style="width:15%" />
            <col  />
            <col style="width:23%" />
            <col style="width:23%" />
            <col style="width:12%" />
        </colgroup>
        <tr>
            <th>수정일</th>
            <th>수정항목</th>
            <th>이전값</th>
            <th>수정값</th>
            <th>수정직원</th>
        </tr>
        <tr v-if="0 >= aoHistoryList.length">
            <td colspan="5">데이터가 없습니다.</td>
        </tr>
        <tr v-for="(val, key) in aoHistoryList">
            <td class="font-11">{% val.regDt %}</td>
            <td>{% val.fldNameHan %}</td>
            <td class="ta-l">{% val.beforeValue %}</td>
            <td class="ta-l">{% val.afterValue %}</td>
            <td>{% val.managerNm %}</td>
        </tr>
    </table>
</section>

<script type="text/javascript">
    $(()=>{
        ImsNkService.getList('updateHistory', { type:'<?=$requestParam['type']?>', sno:'<?=$requestParam['sno']?>' }).then((data)=>{
            $.imsPostAfter(data, (data)=> {
                const initParams = {
                    data: {
                        aoHistoryList: data,
                    },
                }
                vueApp = ImsService.initVueApp(appId, initParams);
            });
        });
    });
</script>

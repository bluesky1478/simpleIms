<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>변경 이력</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <table class="table table-rows ch-table table-td-height30">
        <colgroup>
            <col style="width:15%" />
            <col  />
            <col style="width:10%" />
        </colgroup>
        <tr>
            <th>변경일</th>
            <th>변경정보</th>
            <th>변경</th>
        </tr>
        <tr v-if="0 >= items.length">
            <td colspan="3">데이터가 없습니다.</td>
        </tr>
        <tr v-for="(item, itemIndex) in items">
            <td class="font-11">{% item.regDt %}</td>
            <td style="text-align: left;padding-left:10px;">
                <ul>
                    <li v-for="comment in item.comment" v-html="comment"></li>
                </ul>
            </td>
            <td>{% item.regManagerNm %}</td>
        </tr>
    </table>
</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const sno = '<?=$requestParam['sno']?>';
        const historyDiv = '<?=$requestParam['historyDiv']?>';
        ImsService.getDataParams(DATA_MAP.UPDATE_HISTORY, {
            sno : sno,
            historyDiv : historyDiv,
        }).then((data)=>{
            console.log(data.data);
            const initParams = {
                data : {
                    items : data.data,
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>
